<?php

namespace App\Http\Controllers;

use App\Models\BuktiKas;
use App\Models\Keuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        // Proteksi: Buku Kas RT hanya dapat diakses oleh Pengurus RT / Admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Akses Ditolak! Buku Kas RT hanya dapat diakses oleh Pengurus / Admin RT demi kerahasiaan pembukuan kas.');
        }

        $query = Keuangan::with('bukti')->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                  ->orWhere('nama_warga', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tgl_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tgl_mulai);
        }

        if ($request->filled('tgl_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tgl_selesai);
        }

        $transaksi = $query->paginate(15)->withQueryString();

        $totalMasuk = Keuangan::where('jenis', 'masuk')->sum('jumlah') ?? 0;
        $totalKeluar = Keuangan::where('jenis', 'keluar')->sum('jumlah') ?? 0;
        $saldoKas = $totalMasuk - $totalKeluar;

        return view('keuangan.index', compact('transaksi', 'totalMasuk', 'totalKeluar', 'saldoKas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_warga' => 'nullable|string|max:100',
            'jenis' => 'required|in:masuk,keluar',
            'keterangan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $keuangan = Keuangan::create([
            'nama_warga' => $validated['nama_warga'] ?? (auth()->user()->isAdmin() ? 'Kas Pengurus RT' : auth()->user()->nama),
            'jenis' => $validated['jenis'],
            'keterangan' => $validated['keterangan'],
            'jumlah' => $validated['jumlah'],
            'tanggal' => $validated['tanggal'],
        ]);

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = 'bukti_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $file->getClientOriginalExtension();
            $destPath = public_path('img/bukti_kas');
            if (!File::isDirectory($destPath)) {
                File::makeDirectory($destPath, 0755, true, true);
            }
            $file->move($destPath, $filename);

            BuktiKas::create([
                'id_keuangan' => $keuangan->id,
                'filename' => $filename,
                'uploaded_at' => now(),
            ]);
        }

        return redirect()->route('keuangan.index')->with('success', 'Transaksi kas berhasil dicatat!');
    }

    public function destroy($id)
    {
        $keuangan = Keuangan::with('bukti')->findOrFail($id);
        foreach ($keuangan->bukti as $b) {
            $path = public_path('img/bukti_kas/' . $b->filename);
            if (File::exists($path)) {
                File::delete($path);
            }
            $b->delete();
        }
        $keuangan->delete();

        return redirect()->route('keuangan.index')->with('success', 'Transaksi kas berhasil dihapus.');
    }

    public function laporan(Request $request)
    {
        $bulan = sprintf('%02d', (int) $request->get('bulan', date('m')));
        $tahun = (int) $request->get('tahun', date('Y'));
        $rt = $request->get('rt');

        $query = Keuangan::with('bukti')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');

        if ($request->filled('rt')) {
            $rtFilter = ltrim($request->rt, '0');
            $query->where(function($q) use ($rtFilter, $request) {
                $q->where('keterangan', 'like', "%RT {$request->rt}%")
                  ->orWhere('keterangan', 'like', "%RT {$rtFilter}%")
                  ->orWhere('keterangan', 'like', "%RT{$request->rt}%");
            });
        }

        $dataLaporan = $query->get();

        // 1. Saldo Awal Kas sebelum bulan berjalan (akumulasi bulan-bulan sebelumnya)
        $startOfMonth = \Carbon\Carbon::createFromDate($tahun, (int)$bulan, 1)->startOfMonth()->toDateString();
        
        $saldoAwalQueryMasuk = Keuangan::whereDate('tanggal', '<', $startOfMonth)->where('jenis', 'masuk');
        $saldoAwalQueryKeluar = Keuangan::whereDate('tanggal', '<', $startOfMonth)->where('jenis', 'keluar');
        
        if ($request->filled('rt')) {
            $rtFilter = ltrim($request->rt, '0');
            $saldoAwalQueryMasuk->where(function($q) use ($rtFilter, $request) {
                $q->where('keterangan', 'like', "%RT {$request->rt}%")
                  ->orWhere('keterangan', 'like', "%RT {$rtFilter}%")
                  ->orWhere('keterangan', 'like', "%RT{$request->rt}%");
            });
            $saldoAwalQueryKeluar->where(function($q) use ($rtFilter, $request) {
                $q->where('keterangan', 'like', "%RT {$request->rt}%")
                  ->orWhere('keterangan', 'like', "%RT {$rtFilter}%")
                  ->orWhere('keterangan', 'like', "%RT{$request->rt}%");
            });
        }

        $saldoAwal = ($saldoAwalQueryMasuk->sum('jumlah') ?? 0) - ($saldoAwalQueryKeluar->sum('jumlah') ?? 0);

        // 2. Total Kas Masuk & Kas Keluar Bulan Berjalan
        $totalMasuk = $dataLaporan->where('jenis', 'masuk')->sum('jumlah');
        $totalKeluar = $dataLaporan->where('jenis', 'keluar')->sum('jumlah');
        $surplusDefisit = $totalMasuk - $totalKeluar;

        // 3. Saldo Akhir Kas per akhir bulan ini
        $saldoAkhir = $saldoAwal + $surplusDefisit;

        // 4. Saldo Kas Kumulatif Keseluruhan (hingga saat ini)
        $saldoTotalSaatIni = (Keuangan::where('jenis', 'masuk')->sum('jumlah') ?? 0) -
                             (Keuangan::where('jenis', 'keluar')->sum('jumlah') ?? 0);

        return view('keuangan.laporan', compact(
            'dataLaporan',
            'bulan',
            'tahun',
            'rt',
            'saldoAwal',
            'totalMasuk',
            'totalKeluar',
            'surplusDefisit',
            'saldoAkhir',
            'saldoTotalSaatIni'
        ));
    }
}
