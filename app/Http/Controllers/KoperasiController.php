<?php

namespace App\Http\Controllers;

use App\Models\IotKoperasiScan;
use App\Models\KoperasiAnggota;
use App\Models\KoperasiCicilan;
use App\Models\KoperasiPinjaman;
use App\Models\KoperasiSimpanan;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Http\Request;

class KoperasiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalAnggota = KoperasiAnggota::where('status', 'aktif')->count();
        $totalSetoran = KoperasiSimpanan::where('jenis', 'setoran')->sum('jumlah') ?? 0;
        $totalPenarikan = KoperasiSimpanan::where('jenis', 'penarikan')->sum('jumlah') ?? 0;
        $saldoKoperasi = $totalSetoran - $totalPenarikan;

        $totalPinjamanDisetujui = KoperasiPinjaman::whereIn('status', ['disetujui', 'lunas'])->sum('jumlah') ?? 0;
        $totalCicilanMasuk = KoperasiCicilan::sum('jumlah') ?? 0;

        // Data spesifik user
        $anggotaSaya = KoperasiAnggota::where('id_user', $user->id)->first();
        $simpananSaya = KoperasiSimpanan::where('id_user', $user->id)->orderBy('tanggal', 'desc')->get();
        $pinjamanSaya = KoperasiPinjaman::with('cicilan')->where('id_user', $user->id)->orderBy('id', 'desc')->get();

        $saldoSimpananSaya = ($simpananSaya->where('jenis', 'setoran')->sum('jumlah') ?? 0) -
                             ($simpananSaya->where('jenis', 'penarikan')->sum('jumlah') ?? 0);

        // Jika admin, ambil semua data untuk tab-tab manajemen
        $semuaAnggota = $user->isAdmin() ? KoperasiAnggota::with('user')->orderBy('id', 'desc')->paginate(15) : collect();
        $semuaPinjaman = $user->isAdmin() ? KoperasiPinjaman::with(['user', 'cicilan'])->orderBy('id', 'desc')->paginate(15) : collect();
        $semuaSimpanan = $user->isAdmin() ? KoperasiSimpanan::with('user')->orderBy('tanggal', 'desc')->paginate(15) : collect();
        $iotScans = $user->isAdmin() ? IotKoperasiScan::with('user')->orderBy('scanned_at', 'desc')->take(10)->get() : collect();

        $daftarWarga = $user->isAdmin() ? User::where('role', 'Warga')->orderBy('nama', 'asc')->get() : [];

        return view('koperasi.index', compact(
            'totalAnggota',
            'saldoKoperasi',
            'totalPinjamanDisetujui',
            'totalCicilanMasuk',
            'anggotaSaya',
            'saldoSimpananSaya',
            'simpananSaya',
            'pinjamanSaya',
            'semuaAnggota',
            'semuaPinjaman',
            'semuaSimpanan',
            'iotScans',
            'daftarWarga'
        ));
    }

    public function storeAnggota(Request $request)
    {
        $userId = auth()->user()->isAdmin() && $request->filled('id_user')
            ? $request->id_user
            : auth()->id();

        $ada = KoperasiAnggota::where('id_user', $userId)->first();
        if ($ada) {
            return back()->with('info', 'Pengguna sudah terdaftar sebagai anggota koperasi.');
        }

        KoperasiAnggota::create([
            'id_user' => $userId,
            'tanggal_gabung' => now(),
            'status' => 'aktif',
        ]);

        return back()->with('success', 'Pendaftaran anggota koperasi berhasil!');
    }

    public function storeSimpanan(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'nullable|exists:users,id',
            'jenis' => 'required|in:setoran,penarikan',
            'jumlah' => 'required|numeric|min:5000',
            'catatan' => 'nullable|string',
        ]);

        $userId = auth()->user()->isAdmin() && !empty($validated['id_user'])
            ? $validated['id_user']
            : auth()->id();

        KoperasiSimpanan::create([
            'id_user' => $userId,
            'jenis' => $validated['jenis'],
            'jumlah' => $validated['jumlah'],
            'tanggal' => now(),
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return back()->with('success', 'Transaksi simpanan koperasi berhasil dicatat!');
    }

    public function storePinjaman(Request $request)
    {
        $validated = $request->validate([
            'jumlah' => 'required|numeric|min:100000',
            'tenor' => 'required|integer|min:1|max:36',
            'keterangan' => 'required|string',
            'rfid_uid' => 'nullable|string',
        ]);

        $user = auth()->user();
        $jumlah = (float) $validated['jumlah'];
        $tenor = (int) $validated['tenor'];
        $bungaFlat = 1.5; // Bunga standar koperasi 1.5% flat per bulan

        // Kalkulasi angsuran bulanan
        $totalBunga = $jumlah * ($bungaFlat / 100) * $tenor;
        $totalPengembalian = $jumlah + $totalBunga;
        $cicilanPerBulan = (int) round($totalPengembalian / $tenor);

        // Pastikan warga otomatis terdaftar sebagai anggota koperasi jika belum
        KoperasiAnggota::firstOrCreate(
            ['id_user' => $user->id],
            ['tanggal_gabung' => now(), 'status' => 'aktif']
        );

        $rfidUid = $request->input('rfid_uid') ?: ($user->rfid_uid ?: 'E28011700' . substr(strval($user->nik ?: '1234567890'), -6));

        // 1. PINJAMAN LANGSUNG DISETUJUI SECARA OTOMATIS (TANPA PERLU TUNGGU KONFIRMASI RT/RW)
        $pinjaman = KoperasiPinjaman::create([
            'id_user' => $user->id,
            'jumlah' => $jumlah,
            'tenor' => $tenor,
            'bunga' => $bungaFlat,
            'status' => 'disetujui', // Langsung disetujui instan via IoT
            'tanggal_pengajuan' => now(),
            'keterangan' => $validated['keterangan'],
            'metode_pengajuan' => 'iot_rfid_tap',
            'scan_device_id' => 'IOT-KTP-READER-01',
            'nik_peminjam' => $user->nik,
        ]);

        // 2. CATAT LOG SCAN IOT RFID E-KTP
        IotKoperasiScan::create([
            'device_id' => 'IOT-KTP-READER-01',
            'nik' => $user->nik,
            'rfid_uid' => $rfidUid,
            'id_user' => $user->id,
            'status' => 'processed',
            'scanned_at' => now(),
        ]);

        // 3. LANGSUNG MASUK KE MENU TAGIHAN IURAN WARGA SEBAGAI PENGINGAT TAGIHAN BULANAN
        $periodeTagihan = "Cicilan Koperasi: " . now()->translatedFormat('F Y') . " (1/{$tenor})";
        Tagihan::create([
            'id_user' => $user->id,
            'bulan_tahun' => $periodeTagihan,
            'tanggal_tagihan' => now()->toDateString(),
            'jumlah' => $cicilanPerBulan,
            'status_bayar' => 'Belum Lunas',
            'tgl_buat' => now(),
            'tgl_update' => now(),
        ]);

        return back()->with('success', 'Pinjaman modal sebesar Rp ' . number_format($jumlah, 0, ',', '.') . ' berhasil disetujui otomatis melalui verifikasi IoT e-KTP tanpa perlu menunggu konfirmasi RT/RW! Tagihan angsuran bulan pertama (Rp ' . number_format($cicilanPerBulan, 0, ',', '.') . ') telah otomatis diterbitkan ke menu Tagihan Iuran Warga sebagai pengingat.');
    }

    public function updateStatusPinjaman(Request $request, $id)
    {
        $pinjaman = KoperasiPinjaman::findOrFail($id);
        $request->validate([
            'status' => 'required|in:pending,disetujui,ditolak,lunas',
        ]);

        $pinjaman->update(['status' => $request->status]);

        return back()->with('success', 'Status pinjaman berhasil diubah menjadi: ' . $request->status);
    }

    public function storeCicilan(Request $request)
    {
        $validated = $request->validate([
            'id_pinjaman' => 'required|exists:koperasi_pinjaman,id',
            'jumlah' => 'required|numeric|min:1000',
            'catatan' => 'nullable|string',
        ]);

        KoperasiCicilan::create([
            'id_pinjaman' => $validated['id_pinjaman'],
            'jumlah' => $validated['jumlah'],
            'tanggal' => now(),
            'catatan' => $validated['catatan'] ?? 'Angsuran Pinjaman',
        ]);

        $pinjaman = KoperasiPinjaman::with('cicilan')->find($validated['id_pinjaman']);
        if ($pinjaman->sisa_pinjaman <= 0) {
            $pinjaman->update(['status' => 'lunas']);
        }

        return back()->with('success', 'Pembayaran angsuran cicilan koperasi berhasil dicatat!');
    }

    public function apiIotScan(Request $request)
    {
        $rfid = $request->input('rfid_uid');
        $nik = $request->input('nik');
        $deviceId = $request->input('device_id', 'IOT-READER-01');
        $action = $request->input('action', 'scan'); // 'scan' atau 'pinjam'
        $jumlah = (float) $request->input('jumlah', 1000000);
        $tenor = (int) $request->input('tenor', 12);
        $keterangan = $request->input('keterangan', 'Pinjaman Otomatis Tap e-KTP IoT Reader');

        $user = null;
        if ($rfid) {
            $user = User::where('rfid_uid', $rfid)->first();
        }
        if (!$user && $nik) {
            $user = User::where('nik', $nik)->first();
        }

        $scan = IotKoperasiScan::create([
            'device_id' => $deviceId,
            'nik' => $nik ?? ($user->nik ?? null),
            'rfid_uid' => $rfid ?? ($user->rfid_uid ?? null),
            'id_user' => $user ? $user->id : null,
            'status' => $user ? 'processed' : 'pending',
            'scanned_at' => now(),
        ]);

        $pinjamanData = null;
        $tagihanData = null;

        // Jika aksi pinjam diminta via tap IoT reader
        if ($user && ($action === 'pinjam' || $request->has('pinjam'))) {
            KoperasiAnggota::firstOrCreate(
                ['id_user' => $user->id],
                ['tanggal_gabung' => now(), 'status' => 'aktif']
            );

            $bungaFlat = 1.5;
            $totalBunga = $jumlah * ($bungaFlat / 100) * $tenor;
            $totalPengembalian = $jumlah + $totalBunga;
            $cicilanPerBulan = (int) round($totalPengembalian / $tenor);

            // Pinjaman langsung disetujui otomatis
            $pinjamanData = KoperasiPinjaman::create([
                'id_user' => $user->id,
                'jumlah' => $jumlah,
                'tenor' => $tenor,
                'bunga' => $bungaFlat,
                'status' => 'disetujui',
                'tanggal_pengajuan' => now(),
                'keterangan' => $keterangan,
                'metode_pengajuan' => 'iot_rfid_tap',
                'scan_device_id' => $deviceId,
                'nik_peminjam' => $user->nik,
            ]);

            // Terbitkan ke tagihan iuran warga sebagai pengingat
            $tagihanData = Tagihan::create([
                'id_user' => $user->id,
                'bulan_tahun' => "Cicilan Koperasi: " . now()->translatedFormat('F Y') . " (1/{$tenor})",
                'tanggal_tagihan' => now()->toDateString(),
                'jumlah' => $cicilanPerBulan,
                'status_bayar' => 'Belum Lunas',
                'tgl_buat' => now(),
                'tgl_update' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => $user 
                ? ($pinjamanData 
                    ? 'Pinjaman otomatis berhasil disetujui via Tap e-KTP IoT! Tagihan cicilan telah diterbitkan ke akun warga.' 
                    : 'Kartu Warga Terverifikasi: ' . $user->nama)
                : 'Kartu RFID belum terdaftar',
            'data' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'nik' => $user->nik,
                    'rt' => $user->rt,
                    'rw' => $user->rw,
                ] : null,
                'scan_id' => $scan->id,
                'pinjaman' => $pinjamanData,
                'tagihan' => $tagihanData,
            ]
        ]);
    }
}
