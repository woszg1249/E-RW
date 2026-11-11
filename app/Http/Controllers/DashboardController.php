<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\BankSampah;
use App\Models\Kegiatan;
use App\Models\Keuangan;
use App\Models\KoperasiAnggota;
use App\Models\Posyandu;
use App\Models\SuratPengajuan;
use App\Models\Tagihan;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Keuangan Kas RT
        $totalMasuk = Keuangan::where('jenis', 'masuk')->sum('jumlah') ?? 0;
        $totalKeluar = Keuangan::where('jenis', 'keluar')->sum('jumlah') ?? 0;
        $saldoKas = $totalMasuk - $totalKeluar;

        // 2. Kependudukan
        $totalWarga = User::where('role', 'Warga')->count();
        $totalWargaRt06 = User::where('role', 'Warga')->where('rt', '06')->count();
        $totalWargaRt07 = User::where('role', 'Warga')->where('rt', '07')->count();
        $totalKK = User::whereNotNull('no_kk')->where('no_kk', '!=', '')->distinct('no_kk')->count('no_kk');

        // 3. Tagihan Iuran
        $tagihanBelumLunas = Tagihan::where('status_bayar', 'Belum Lunas')->count();
        $totalTunggakan = Tagihan::where('status_bayar', 'Belum Lunas')->sum('jumlah') ?? 0;

        // Tagihan khusus warga yang sedang login
        $tagihanUser = [];
        if ($user->isWarga()) {
            $tagihanUser = Tagihan::where('id_user', $user->id)
                ->orderBy('id_tagihan', 'desc')
                ->take(5)
                ->get();
        }

        // 4. Modul Lainnya
        $totalUmkm = Umkm::where('status', 'Aktif')->count();
        $totalPosyandu = Posyandu::count();
        $totalBankSampahKg = BankSampah::where('status', 'approved')->sum('berat_kg') ?? 0;
        $totalKoperasiAnggota = KoperasiAnggota::where('status', 'aktif')->count();
        $totalAset = Aset::count();
        $suratMenunggu = SuratPengajuan::where('status', 'Menunggu')->count();

        // 5. Kegiatan Terkini
        $kegiatanTerbaru = Kegiatan::orderBy('tanggal_mulai', 'desc')->take(4)->get();

        // 6. Transaksi Kas Terakhir
        $kasTerakhir = Keuangan::orderBy('id', 'desc')->take(6)->get();

        // 7. Data Grafik Keuangan Bulanan (Tahun Berjalan)
        $bulanList = [];
        $grafikMasuk = [];
        $grafikKeluar = [];

        for ($i = 5; $i >= 0; $i--) {
            $tgl = now()->subMonths($i);
            $bln = $tgl->format('m');
            $thn = $tgl->format('Y');
            $namaBulan = $tgl->translatedFormat('M Y');

            $masuk = Keuangan::where('jenis', 'masuk')
                ->whereMonth('tanggal', $bln)
                ->whereYear('tanggal', $thn)
                ->sum('jumlah') ?? 0;

            $keluar = Keuangan::where('jenis', 'keluar')
                ->whereMonth('tanggal', $bln)
                ->whereYear('tanggal', $thn)
                ->sum('jumlah') ?? 0;

            $bulanList[] = $namaBulan;
            $grafikMasuk[] = (int) $masuk;
            $grafikKeluar[] = (int) $keluar;
        }

        return view('dashboard.index', compact(
            'user',
            'saldoKas',
            'totalMasuk',
            'totalKeluar',
            'totalWarga',
            'totalWargaRt06',
            'totalWargaRt07',
            'totalKK',
            'tagihanBelumLunas',
            'totalTunggakan',
            'tagihanUser',
            'totalUmkm',
            'totalPosyandu',
            'totalBankSampahKg',
            'totalKoperasiAnggota',
            'totalAset',
            'suratMenunggu',
            'kegiatanTerbaru',
            'kasTerakhir',
            'bulanList',
            'grafikMasuk',
            'grafikKeluar'
        ));
    }
}
