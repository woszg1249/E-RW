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
            $nomorRekening = '-';
            if (Schema::hasTable('bank_warga_rekening')) {
                $rek = BankWargaRekening::getOrCreateForUser($user, 'iot_tap_ktp');
                if (empty($rek->rfid_uid)) {
                    $rek->update(['rfid_uid' => $cleanUid]);
                }
                $saldoBankWarga = (float) $rek->saldo;
                $nomorRekening = $rek->nomor_rekening;
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
                'access_granted' => true,
                'buka_portal' => true,
                'akses_portal' => 'DIIZINKAN',
                'message' => "Akses Portal Perumahan DIIZINKAN untuk warga: {$user->nama}",
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
            ], 200);
        }

        // KASUS 2: Kartu BELUM terhubung (Proteksi Akses Portal Ditolak)
        return response()->json([
            'status' => 'denied',
            'registered' => false,
            'access_granted' => false,
            'buka_portal' => false,
            'akses_portal' => 'DITOLAK',
            'message' => "AKSES DITOLAK! Kartu e-KTP UID {$cleanUid} belum terdaftar di akun warga E-RW.",
            'rfid_uid' => $cleanUid,
            'device_id' => $deviceId,
            'hint' => 'Login ke web E-RW -> Profil Akun lalu scan e-KTP untuk mendaftarkan kartu Anda.',
        ], 403);
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

    /**
     * Self-Pairing e-KTP untuk user yang sedang login
     * Method: POST /profil/rfid/pair
     */
    public function pairSelf(Request $request)
    {
        $user = auth()->user();
        $cleanUid = $this->normalizeUid($request->input('rfid_uid'));

        if (!$cleanUid) {
            return response()->json([
                'status' => 'error',
                'message' => 'UID kartu e-KTP tidak valid.',
            ], 400);
        }

        // Proteksi: Tolak jika UID sudah terdaftar pada warga lain
        $existingOwner = User::where('rfid_uid', $cleanUid)
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingOwner) {
            return response()->json([
                'status' => 'error',
                'message' => "Kartu e-KTP UID {$cleanUid} sudah terdaftar atas nama warga lain ({$existingOwner->nama}).",
            ], 409);
        }

        $user->update(['rfid_uid' => $cleanUid]);

        if (Schema::hasTable('bank_warga_rekening')) {
            BankWargaRekening::where('id_user', $user->id)->update(['rfid_uid' => $cleanUid]);
        }

        Cache::forget('latest_rfid_scan');

        return response()->json([
            'status' => 'success',
            'message' => "e-KTP UID {$cleanUid} berhasil didaftarkan ke akun {$user->nama}.",
            'rfid_uid' => $cleanUid,
        ]);
    }

    /**
     * Lepas kartu e-KTP dari user yang sedang login
     * Method: POST /profil/rfid/unbind
     */
    public function unbindSelf()
    {
        $user = auth()->user();
        $user->update(['rfid_uid' => null]);

        if (Schema::hasTable('bank_warga_rekening')) {
            BankWargaRekening::where('id_user', $user->id)->update(['rfid_uid' => null]);
        }

        return back()->with('success', 'Kartu e-KTP berhasil dilepas dari akun Anda.');
    }

    /**
     * Validasi keamanan header X-Device-Key atau Authorization Bearer untuk ESP32 Kelompok
     */
    private function isValidDeviceKey(Request $request): bool
    {
        $expectedKey = 'smartrw-rfid-device';
        $headerKey = $request->header('X-Device-Key');
        $bearerToken = $request->bearerToken();

        return $headerKey === $expectedKey || $bearerToken === $expectedKey;
    }

    /**
     * Endpoint API ESP32 Kelompok: POST /api/rfid/tap
     */
    public function tap(Request $request)
    {
        if (!$this->isValidDeviceKey($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: X-Device-Key atau Bearer token tidak valid.',
            ], 401);
        }

        $request->validate([
            'uid' => 'required|string',
            'device_id' => 'nullable|string',
        ]);

        $uid = $this->normalizeUid((string) $request->input('uid'));
        $deviceId = $request->input('device_id', 'esp32-scanner');

        if (!$uid) {
            return response()->json([
                'success' => false,
                'message' => 'Format UID tidak valid.',
            ], 422);
        }

        // Cari warga pemilik kartu
        $user = User::where('rfid_uid', $uid)->first();

        // Simpan sementara di Cache untuk auto-fill & real-time listener web E-RW
        Cache::put('last_rfid_uid', $uid, 30);
        Cache::put('latest_rfid_scan', [
            'rfid_uid' => $uid,
            'device_id' => $deviceId,
            'scanned_at' => now()->toIso8601String(),
            'scanned_time' => now()->format('H:i:s'),
            'registered' => (bool) $user,
            'user_nama' => $user ? $user->nama : null,
            'user_nik' => $user ? $user->nik : null,
            'user_id' => $user ? $user->id : null,
        ], now()->addMinutes(5));

        // Catat ke log IoT
        if (Schema::hasTable('iot_koperasi_scans')) {
            try {
                IotKoperasiScan::create([
                    'device_id' => $deviceId,
                    'nik' => $user ? $user->nik : null,
                    'rfid_uid' => $uid,
                    'id_user' => $user ? $user->id : null,
                    'status' => $user ? 'processed' : 'unregistered',
                    'scanned_at' => now(),
                ]);
            } catch (\Throwable $e) {
            }
        }

        if ($user && Schema::hasTable('bank_warga_rekening')) {
            $rek = BankWargaRekening::getOrCreateForUser($user, 'iot_tap_ktp');
            if (empty($rek->rfid_uid)) {
                $rek->update(['rfid_uid' => $uid]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => $user
                ? "Kartu berhasil dibaca ({$user->nama})"
                : 'Kartu berhasil dibaca',
            'uid' => $uid,
            'device_id' => $deviceId,
            'registered' => (bool) $user,
            'user' => $user ? [
                'id' => $user->id,
                'nama' => $user->nama,
                'nik' => $user->nik,
                'rt' => $user->rt,
                'rw' => $user->rw,
            ] : null,
        ], 200);
    }

    /**
     * Endpoint API ESP32 Kelompok: POST /api/rfid/heartbeat
     */
    public function heartbeat(Request $request)
    {
        if (!$this->isValidDeviceKey($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: X-Device-Key atau Bearer token tidak valid.',
            ], 401);
        }

        Cache::put('rfid_device_online', true, 15);
        Cache::put('rfid_device_info', [
            'device_id' => $request->input('device_id'),
            'ip' => $request->input('ip'),
            'last_seen' => now()->toIso8601String(),
        ], 15);

        return response()->json([
            'status' => 'online',
        ], 200);
    }

    /**
     * Halaman Portal IoT & Daftar RFID Real-Time
     * Method: GET /portal-iot
     */
    public function portalIot()
    {
        $user = auth()->user();
        $wargaList = $user->isAdmin()
            ? User::orderBy('nama')->get(['id', 'nama', 'nik', 'rt', 'rw', 'rfid_uid'])
            : collect();

        $registeredCards = $user->isAdmin()
            ? User::whereNotNull('rfid_uid')->where('rfid_uid', '!=', '')->orderBy('nama')->get(['id', 'nama', 'nik', 'rt', 'rw', 'rfid_uid'])
            : ($user->rfid_uid ? collect([$user]) : collect());

        $deviceOnline = (bool) Cache::get('rfid_device_online', false);
        $deviceInfo = Cache::get('rfid_device_info', []);
        $recentScans = $this->getRecentScans();

        return view('rfid.portal', compact('user', 'wargaList', 'registeredCards', 'deviceOnline', 'deviceInfo', 'recentScans'));
    }

    /**
     * Endpoint JSON Polling Real-Time untuk Portal IoT
     * Method: GET /portal-iot/status
     */
    public function portalStatus()
    {
        return response()->json([
            'device_online' => (bool) Cache::get('rfid_device_online', false),
            'device_info' => Cache::get('rfid_device_info'),
            'latest_scan' => Cache::get('latest_rfid_scan'),
            'recent_scans' => $this->getRecentScans(),
        ]);
    }

    /**
     * Admin pairing e-KTP ke warga terpilih via AJAX di Portal IoT
     * Method: POST /portal-iot/pair-warga
     */
    public function adminPairAjax(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $cleanUid = $this->normalizeUid($request->input('rfid_uid'));
        if (!$cleanUid) {
            return response()->json(['status' => 'error', 'message' => 'UID kartu tidak valid.'], 400);
        }

        $targetUser = User::findOrFail($request->input('user_id'));

        User::where('rfid_uid', $cleanUid)->where('id', '!=', $targetUser->id)->update(['rfid_uid' => null]);
        $targetUser->update(['rfid_uid' => $cleanUid]);

        if (Schema::hasTable('bank_warga_rekening')) {
            BankWargaRekening::where('id_user', $targetUser->id)->update(['rfid_uid' => $cleanUid]);
        }

        Cache::forget('latest_rfid_scan');

        return response()->json([
            'status' => 'success',
            'message' => "Kartu UID {$cleanUid} berhasil didaftarkan ke warga {$targetUser->nama}.",
            'rfid_uid' => $cleanUid,
        ]);
    }

    private function getRecentScans()
    {
        if (!Schema::hasTable('iot_koperasi_scans')) {
            return [];
        }

        return IotKoperasiScan::with('user:id,nama,nik,rt,rw')
            ->orderByDesc('scanned_at')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'rfid_uid' => $row->rfid_uid,
                    'device_id' => $row->device_id,
                    'status' => $row->status,
                    'id_user' => $row->id_user,
                    'nama' => $row->user ? $row->user->nama : null,
                    'nik' => $row->user ? $row->user->nik : null,
                    'rt_rw' => $row->user ? "RT {$row->user->rt}/RW {$row->user->rw}" : null,
                    'waktu' => $row->scanned_at ? \Carbon\Carbon::parse($row->scanned_at)->format('d/m/Y H:i:s') : '-',
                ];
            })
            ->values();
    }

    /**
     * CRUD Create: Daftarkan / Tambah Kartu RFID baru
     */
    public function storeCard(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|string|max:64',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $cleanUid = $this->normalizeUid($request->input('rfid_uid'));
        if (!$cleanUid) {
            return back()->with('error', 'Format UID kartu tidak valid.');
        }

        $targetUser = (auth()->user()->isAdmin() && $request->filled('user_id'))
            ? User::findOrFail($request->input('user_id'))
            : auth()->user();

        $duplicate = User::where('rfid_uid', $cleanUid)->where('id', '!=', $targetUser->id)->first();
        if ($duplicate) {
            return back()->with('error', "UID {$cleanUid} sudah terdaftar milik {$duplicate->nama}.");
        }

        $targetUser->update(['rfid_uid' => $cleanUid]);

        if (Schema::hasTable('bank_warga_rekening')) {
            BankWargaRekening::where('id_user', $targetUser->id)->update(['rfid_uid' => $cleanUid]);
        }

        Cache::forget('latest_rfid_scan');

        return back()->with('success', "Kartu RFID UID [ {$cleanUid} ] berhasil ditambahkan untuk {$targetUser->nama}.");
    }

    /**
     * CRUD Update: Ubah UID Kartu RFID terdaftar
     */
    public function updateCard(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);
        if (!auth()->user()->isAdmin() && auth()->id() !== $targetUser->id) {
            abort(403, 'Anda tidak berhak mengubah kartu warga lain.');
        }

        $request->validate([
            'rfid_uid' => 'required|string|max:64',
        ]);

        $cleanUid = $this->normalizeUid($request->input('rfid_uid'));
        if (!$cleanUid) {
            return back()->with('error', 'Format UID kartu tidak valid.');
        }

        $duplicate = User::where('rfid_uid', $cleanUid)->where('id', '!=', $targetUser->id)->first();
        if ($duplicate) {
            return back()->with('error', "UID {$cleanUid} sudah dipakai oleh {$duplicate->nama}.");
        }

        $targetUser->update(['rfid_uid' => $cleanUid]);

        if (Schema::hasTable('bank_warga_rekening')) {
            BankWargaRekening::where('id_user', $targetUser->id)->update(['rfid_uid' => $cleanUid]);
        }

        Cache::forget('latest_rfid_scan');

        return back()->with('success', "UID Kartu {$targetUser->nama} berhasil diperbarui menjadi [ {$cleanUid} ].");
    }

    /**
     * CRUD Delete: Hapus / Lepas Kartu RFID dari user
     */
    public function destroyCard($id)
    {
        $targetUser = User::findOrFail($id);
        if (!auth()->user()->isAdmin() && auth()->id() !== $targetUser->id) {
            abort(403, 'Anda tidak berhak menghapus kartu warga lain.');
        }

        $targetUser->update(['rfid_uid' => null]);

        if (Schema::hasTable('bank_warga_rekening')) {
            BankWargaRekening::where('id_user', $targetUser->id)->update(['rfid_uid' => null]);
        }

        return back()->with('success', "Kartu RFID milik {$targetUser->nama} berhasil dihapus.");
    }

    /**
     * CRUD Delete: Hapus 1 baris log riwayat tap kartu
     */
    public function destroyScan($id)
    {
        $scan = IotKoperasiScan::findOrFail($id);
        if (!auth()->user()->isAdmin() && $scan->id_user !== auth()->id()) {
            abort(403, 'Anda tidak berhak menghapus riwayat scan ini.');
        }

        $scan->delete();

        return back()->with('success', 'Riwayat tap kartu berhasil dihapus.');
    }
}
