<?php

namespace App\Http\Controllers;

use App\Models\BankWargaRekening;
use App\Models\BankWargaTransaksi;
use App\Models\User;
use App\Services\BankWargaService;
use Illuminate\Http\Request;

class BankWargaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->isWarga()) {
            // Wajib memiliki e-KTP fisik terdaftar (rfid_uid) untuk mengaktifkan rekening Bank Warga
            if (empty($user->rfid_uid)) {
                return view('bank_warga.unregistered', compact('user'));
            }

            // Dapatkan atau buat rekening untuk warga yang e-KTP-nya sudah terdaftar
            $rekening = BankWargaRekening::where('id_user', $user->id)->first();
            if (!$rekening) {
                $rekening = BankWargaRekening::getOrCreateForUser($user, 'iot_tap_ktp');
            } elseif (empty($rekening->rfid_uid)) {
                $rekening->update(['rfid_uid' => $user->rfid_uid]);
            }

            $queryTrx = BankWargaTransaksi::where('id_rekening', $rekening->id);

            if ($request->filled('kategori')) {
                $queryTrx->where('kategori', $request->kategori);
            }
            if ($request->filled('jenis')) {
                $queryTrx->where('jenis', $request->jenis);
            }

            $transaksiList = $queryTrx->orderBy('id', 'desc')->paginate(10)->withQueryString();

            $totalMasuk = BankWargaTransaksi::where('id_rekening', $rekening->id)->where('jenis', 'masuk')->sum('jumlah');
            $totalKeluar = BankWargaTransaksi::where('id_rekening', $rekening->id)->where('jenis', 'keluar')->sum('jumlah');
            $totalDariSampah = BankWargaTransaksi::where('id_rekening', $rekening->id)->where('kategori', 'bank_sampah')->sum('jumlah');
            $totalBayarIuran = BankWargaTransaksi::where('id_rekening', $rekening->id)->where('kategori', 'bayar_iuran')->sum('jumlah');

            return view('bank_warga.index', compact(
                'rekening',
                'transaksiList',
                'totalMasuk',
                'totalKeluar',
                'totalDariSampah',
                'totalBayarIuran'
            ));
        }

        // Tampilan untuk Pengurus RT / Admin RW
        $queryRekening = BankWargaRekening::with('user');

        if ($request->filled('rt')) {
            $rt = $request->rt;
            $queryRekening->whereHas('user', function ($q) use ($rt) {
                $q->where('rt', $rt);
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $queryRekening->where(function ($q) use ($s) {
                $q->where('nomor_rekening', 'like', "%{$s}%")
                    ->orWhere('nik', 'like', "%{$s}%")
                    ->orWhereHas('user', function ($qu) use ($s) {
                        $qu->where('nama', 'like', "%{$s}%");
                    });
            });
        }

        $rekeningList = $queryRekening->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $totalSaldoSeluruhWarga = BankWargaRekening::sum('saldo');
        $totalNasabah = BankWargaRekening::count();
        $totalTrxHariIni = BankWargaTransaksi::whereDate('created_at', now()->toDateString())->count();
        $totalPencairanSampah = BankWargaTransaksi::where('kategori', 'bank_sampah')->sum('jumlah');

        $daftarWargaBelumPunya = User::where('role', 'Warga')
            ->whereDoesntHave('bankWargaRekening')
            ->orderBy('nama', 'asc')
            ->get();

        $deviceOnline = (bool) \Illuminate\Support\Facades\Cache::get('rfid_device_online', false);
        $deviceInfo = \Illuminate\Support\Facades\Cache::get('rfid_device_info', []);

        return view('bank_warga.admin_index', compact(
            'rekeningList',
            'totalSaldoSeluruhWarga',
            'totalNasabah',
            'totalTrxHariIni',
            'totalPencairanSampah',
            'daftarWargaBelumPunya',
            'deviceOnline',
            'deviceInfo'
        ));
    }

    /**
     * Top-up saldo mandiri (Transfer Antar Bank / E-Wallet / QRIS)
     */
    public function topup(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:5000',
            'metode' => 'required|string|max:50',
            'id_user' => 'nullable|exists:users,id',
        ]);

        $user = (auth()->user()->isAdmin() && $request->filled('id_user'))
            ? User::findOrFail($request->id_user)
            : auth()->user();

        $rekening = BankWargaRekening::getOrCreateForUser($user);
        $jumlah = (float) $request->jumlah;
        $metode = $request->metode;

        $kategori = str_contains(strtolower($metode), 'bank') ? 'topup_bank' : 'topup_ewallet';

        BankWargaService::setorDana(
            $rekening,
            $jumlah,
            $kategori,
            "Top Up Saldo via {$metode}",
            $metode
        );

        return back()->with('success', "Top Up saldo Bank Warga sebesar Rp " . number_format($jumlah, 0, ',', '.') . " berhasil masuk ke rekening {$rekening->nomor_rekening}!");
    }

    /**
     * Aktivasi / Pendaftaran rekening Bank Warga (WAJIB sudah tap e-KTP fisik)
     */
    public function daftarOtomatis(Request $request)
    {
        $user = auth()->user();
        if ($user->isAdmin() && $request->filled('id_user')) {
            $user = User::findOrFail($request->id_user);
        }

        $rawUid = $request->input('rfid_uid');
        $cleanUid = $rawUid ? strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $rawUid)) : null;

        if ($cleanUid) {
            $duplicate = User::where('rfid_uid', $cleanUid)->where('id', '!=', $user->id)->first();
            if ($duplicate) {
                return back()->with('error', "Kartu e-KTP UID {$cleanUid} sudah terdaftar milik {$duplicate->nama}.");
            }
            $user->update(['rfid_uid' => $cleanUid]);
        }

        if (empty($user->rfid_uid)) {
            return back()->with('error', "Wajib tap fisik e-KTP {$user->nama} ke sensor IoT ESP32 terlebih dahulu sebelum membuka rekening!");
        }

        $rekening = BankWargaRekening::getOrCreateForUser($user, 'iot_tap_ktp');
        if (empty($rekening->rfid_uid)) {
            $rekening->update(['rfid_uid' => $user->rfid_uid]);
        }

        return back()->with('success', "Rekening Bank Warga berhasil dibuka untuk {$user->nama} (UID: {$user->rfid_uid}) dengan nomor rekening: {$rekening->nomor_rekening}");
    }

    /**
     * API untuk Alat IoT Tap e-KTP / RFID Reader
     * Method: POST /api/iot/bank-warga/tap
     */
    public function apiIotTap(Request $request)
    {
        $rfid = $request->input('rfid_uid');
        $nik = $request->input('nik');
        $deviceId = $request->input('device_id', 'IOT-BW-01');
        $action = $request->input('action', 'check_or_register'); // check_or_register, topup
        $nominal = (float) $request->input('nominal', 0);

        if (!$rfid && !$nik) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter rfid_uid atau nik wajib disertakan saat tap IoT.',
            ], 400);
        }

        // Cari data warga
        $user = null;
        if ($rfid) {
            $user = User::where('rfid_uid', $rfid)->first();
        }
        if (!$user && $nik) {
            $user = User::where('nik', $nik)->first();
        }

        if (!$user) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Data e-KTP / RFID belum terdaftar di Sistem RW 19. Silakan daftarkan NIK warga ke pengurus RT.',
                'rfid_scanned' => $rfid,
                'nik_scanned' => $nik,
            ], 404);
        }

        // Sinkronkan UID jika kartu baru ditap
        if ($rfid && empty($user->rfid_uid)) {
            $user->update(['rfid_uid' => $rfid]);
        }

        // Otomatis buat atau dapatkan rekening Bank Warga
        $rekening = BankWargaRekening::getOrCreateForUser($user, 'iot_tap_ktp');
        if ($rfid && empty($rekening->rfid_uid)) {
            $rekening->update(['rfid_uid' => $rfid]);
        }

        // Jika aksi top-up via IoT
        if ($action === 'topup' && $nominal > 0) {
            BankWargaService::setorDana(
                $rekening,
                $nominal,
                'topup_bank',
                "Top Up via Tap IoT Reader ({$deviceId})",
                "IoT Reader {$deviceId}"
            );
            $rekening->refresh();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Tap e-KTP berhasil diverifikasi oleh IoT Reader Bank Warga.',
            'data' => [
                'device_id' => $deviceId,
                'nama' => $user->nama,
                'nik' => $user->nik,
                'no_kk' => $user->no_kk,
                'rt' => $user->rt,
                'nomor_rekening' => $rekening->nomor_rekening,
                'saldo' => (float) $rekening->saldo,
                'saldo_format' => 'Rp ' . number_format($rekening->saldo, 0, ',', '.'),
                'status_rekening' => $rekening->status,
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }
}
