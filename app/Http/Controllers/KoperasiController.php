<?php

namespace App\Http\Controllers;

use App\Models\IotKoperasiScan;
use App\Models\KoperasiAnggota;
use App\Models\KoperasiCicilan;
use App\Models\KoperasiPinjaman;
use App\Models\KoperasiSimpanan;
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
        ]);

        $bunga = 1.5; // Bunga standar koperasi 1.5%

        KoperasiPinjaman::create([
            'id_user' => auth()->id(),
            'jumlah' => $validated['jumlah'],
            'tenor' => $validated['tenor'],
            'bunga' => $bunga,
            'status' => 'pending',
            'tanggal_pengajuan' => now(),
            'keterangan' => $validated['keterangan'],
            'metode_pengajuan' => 'web_portal',
            'nik_peminjam' => auth()->user()->nik,
        ]);

        return back()->with('success', 'Pengajuan pinjaman berhasil diajukan! Menunggu verifikasi pengurus.');
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

        return response()->json([
            'status' => 'success',
            'message' => $user ? 'Kartu Warga Terverifikasi: ' . $user->nama : 'Kartu RFID belum terdaftar',
            'data' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'nik' => $user->nik,
                    'rt' => $user->rt,
                    'rw' => $user->rw,
                ] : null,
                'scan_id' => $scan->id,
            ]
        ]);
    }
}
