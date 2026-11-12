<?php

namespace App\Http\Controllers;

use App\Models\BankWargaRekening;
use App\Models\IotKoperasiScan;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class RfidController extends Controller
{
    /**
     * Normalisasi UID kartu (hapus spasi, titik dua, dash, ubah ke UPPERCASE)
     */
    private function normalizeUid(?string $raw): ?string
    {
        if (!$raw) return null;
        $clean = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $raw));
        return !empty($clean) ? $clean : null;
    }

    /**
     * Endpoint API Utama untuk ESP32 RC-522 saat Kartu Ditap
     * Method: POST /api/iot/rfid/tap
     */
    public function apiTap(Request $request)
    {
        $rawUid = $request->input('rfid_uid') ?? $request->input('uid');
        $deviceId = $request->input('device_id', 'ESP32-RC522');
        $cleanUid = $this->normalizeUid($rawUid);

        if (!$cleanUid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter rfid_uid tidak valid atau kosong.',
            ], 400);
        }

        // Cari user pemilik kartu
        $user = User::where('rfid_uid', $cleanUid)->first();

        // Simpan ke Cache sebagai scan kartu terakhir (aktif selama 5 menit) untuk auto-detect di UI Web
        $scanInfo = [
            'rfid_uid' => $cleanUid,
            'device_id' => $deviceId,
            'scanned_at' => now()->toIso8601String(),
            'scanned_time' => now()->format('H:i:s'),
            'registered' => (bool) $user,
            'user_nama' => $user ? $user->nama : null,
            'user_nik' => $user ? $user->nik : null,
            'user_id' => $user ? $user->id : null,
        ];
        Cache::put('latest_rfid_scan', $scanInfo, now()->addMinutes(5));

        // Catat ke log IoT jika tabel tersedia
        if (Schema::hasTable('iot_koperasi_scans')) {
            try {
                IotKoperasiScan::create([
                    'device_id' => $deviceId,
                    'nik' => $user ? $user->nik : null,
                    'rfid_uid' => $cleanUid,
                    'id_user' => $user ? $user->id : null,
                    'status' => $user ? 'processed' : 'unregistered',
                    'scanned_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // Jangan gagalkan response jika logging terkendala
            }
        }

        // KASUS 1: Kartu SUDAH terhubung ke warga
        if ($user) {
            $saldoBankWarga = 0;
            if (Schema::hasTable('bank_warga_rekening')) {
                $rek = BankWargaRekening::where('id_user', $user->id)->first();
                if ($rek) {
                    $saldoBankWarga = (float) $rek->saldo;
                }
            }

            $tunggakanIuran = 0;
            if (Schema::hasTable('tagihan')) {
                $tunggakanIuran = Tagihan::where('id_user', $user->id)
                    ->where('status_bayar', 'Belum Lunas')
                    ->count();
            }

            return response()->json([
                'status' => 'success',
                'registered' => true,
                'message' => "Kartu terverifikasi milik warga: {$user->nama}",
                'rfid_uid' => $cleanUid,
                'data' => [
                    'user_id' => $user->id,
                    'nama' => $user->nama,
                    'nik' => $user->nik,
                    'no_kk' => $user->no_kk,
                    'rt' => $user->rt,
                    'rw' => $user->rw,
                    'role' => $user->role,
                    'status_keluarga' => $user->status_keluarga,
                    'saldo_bank_warga' => $saldoBankWarga,
                    'saldo_bank_warga_format' => 'Rp ' . number_format($saldoBankWarga, 0, ',', '.'),
                    'tagihan_belum_lunas' => $tunggakanIuran,
                    'scanned_at' => now()->toIso8601String(),
                ],
            ]);
        }

        // KASUS 2: Kartu BELUM terhubung (Kartu Baru)
        return response()->json([
            'status' => 'unregistered',
            'registered' => false,
            'message' => "Kartu UID {$cleanUid} belum terdaftar. Silakan hubungkan ke akun warga di portal web E-RW.",
            'rfid_uid' => $cleanUid,
            'device_id' => $deviceId,
            'hint' => 'Buka menu Data Warga di web lalu tempelkan kartu ini untuk pairing otomatis.',
        ]);
    }

    /**
     * Endpoint untuk cek scan kartu terakhir (Polled oleh antarmuka Web UI)
     * Method: GET /api/iot/rfid/latest
     */
    public function latestScan()
    {
        $scan = Cache::get('latest_rfid_scan');
        if (!$scan) {
            return response()->json([
                'status' => 'empty',
                'message' => 'Belum ada kartu yang ditap dalam 5 menit terakhir.',
            ]);
        }

        return response()->json([
            'status' => 'available',
            'data' => $scan,
        ]);
    }

    /**
     * Endpoint API untuk Menghubungkan (Pairing) Kartu ke Warga
     * Method: POST /api/iot/rfid/pair
     */
    public function apiPair(Request $request)
    {
        $rawUid = $request->input('rfid_uid');
        $userId = $request->input('user_id');
        $nik = $request->input('nik');

        $cleanUid = $this->normalizeUid($rawUid);
        if (!$cleanUid) {
            return response()->json(['status' => 'error', 'message' => 'UID kartu tidak valid.'], 400);
        }

        $user = null;
        if ($userId) {
            $user = User::find($userId);
        } elseif ($nik) {
            $user = User::where('nik', $nik)->first();
        }

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Data warga tidak ditemukan.'], 404);
        }

        // Lepas UID jika sebelumnya dipakai orang lain
        User::where('rfid_uid', $cleanUid)->where('id', '!=', $user->id)->update(['rfid_uid' => null]);

        $user->update(['rfid_uid' => $cleanUid]);

        // Sinkronkan ke rekening Bank Warga jika ada
        if (Schema::hasTable('bank_warga_rekening')) {
            BankWargaRekening::where('id_user', $user->id)->update(['rfid_uid' => $cleanUid]);
        }

        // Hapus cache scan terakhir agar UI tidak looping pairing
        Cache::forget('latest_rfid_scan');

        return response()->json([
            'status' => 'success',
            'message' => "Kartu RFID UID {$cleanUid} berhasil dihubungkan ke warga {$user->nama}.",
            'data' => [
                'user_id' => $user->id,
                'nama' => $user->nama,
                'rfid_uid' => $user->rfid_uid,
            ],
        ]);
    }

    /**
     * Endpoint Web Admin untuk update / hubungkan / lepas kartu RFID warga
     * Method: POST /warga/{id}/rfid
     */
    public function updateRfid(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $rawUid = $request->input('rfid_uid');
        $cleanUid = $this->normalizeUid($rawUid);

        // Jika UID diisi, pastikan tidak dobel dengan warga lain
        if ($cleanUid) {
            User::where('rfid_uid', $cleanUid)->where('id', '!=', $user->id)->update(['rfid_uid' => null]);
            $user->update(['rfid_uid' => $cleanUid]);

            if (Schema::hasTable('bank_warga_rekening')) {
                BankWargaRekening::where('id_user', $user->id)->update(['rfid_uid' => $cleanUid]);
            }

            Cache::forget('latest_rfid_scan');

            return back()->with('success', "Kartu RFID UID [ {$cleanUid} ] berhasil dihubungkan ke warga {$user->nama}!");
        } else {
            // Lepas kartu
            $user->update(['rfid_uid' => null]);
            if (Schema::hasTable('bank_warga_rekening')) {
                BankWargaRekening::where('id_user', $user->id)->update(['rfid_uid' => null]);
            }

            return back()->with('success', "Kartu RFID warga {$user->nama} berhasil dilepas.");
        }
    }
}
