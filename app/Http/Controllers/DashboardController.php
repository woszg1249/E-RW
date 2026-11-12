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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Keuangan Kas RT
        $saldoKas = 0;
        $totalMasuk = 0;
        $totalKeluar = 0;
        $kasTerakhir = collect();
        $bulanList = [];
        $grafikMasuk = [];
        $grafikKeluar = [];

        try {
            if (Schema::hasTable('keuangan')) {
                $totalMasuk = (float) (Keuangan::where('jenis', 'masuk')->sum('jumlah') ?? 0);
                $totalKeluar = (float) (Keuangan::where('jenis', 'keluar')->sum('jumlah') ?? 0);
                $saldoKas = $totalMasuk - $totalKeluar;
                $kasTerakhir = Keuangan::orderBy('id', 'desc')->take(6)->get();

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
            }
        } catch (\Throwable $e) {
            Log::warning('DashboardController keuangan: ' . $e->getMessage());
        }

        // 2. Kependudukan
        $totalWarga = 0;
        $totalWargaRt06 = 0;
        $totalWargaRt07 = 0;
        $totalKK = 0;

        try {
            if (Schema::hasTable('users')) {
                $totalWarga = User::where('role', 'Warga')->count();
                $totalWargaRt06 = User::where('role', 'Warga')->where('rt', '06')->count();
                $totalWargaRt07 = User::where('role', 'Warga')->where('rt', '07')->count();
                $totalKK = User::whereNotNull('no_kk')->where('no_kk', '!=', '')->distinct()->count('no_kk');
            }
        } catch (\Throwable $e) {
            Log::warning('DashboardController users: ' . $e->getMessage());
        }

        // 3. Tagihan Iuran
        $tagihanBelumLunas = 0;
        $totalTunggakan = 0;
        $tagihanUser = collect();

        try {
            if (Schema::hasTable('tagihan')) {
                $tagihanBelumLunas = Tagihan::where('status_bayar', 'Belum Lunas')->count();
                $totalTunggakan = Tagihan::where('status_bayar', 'Belum Lunas')->sum('jumlah') ?? 0;

                if ($user && method_exists($user, 'isWarga') && $user->isWarga()) {
                    $tagihanUser = Tagihan::where('id_user', $user->id)
                        ->orderBy('id_tagihan', 'desc')
                        ->take(5)
                        ->get();
                }
            }
        } catch (\Throwable $e) {
            Log::warning('DashboardController tagihan: ' . $e->getMessage());
        }

        // 4. Modul Lainnya
        $totalUmkm = 0;
        $totalPosyandu = 0;
        $totalBankSampahKg = 0;
        $totalKoperasiAnggota = 0;
        $totalAset = 0;
        $suratMenunggu = 0;
        $kegiatanTerbaru = collect();

        try {
            if (Schema::hasTable('umkm')) {
                $totalUmkm = Umkm::where('status', 'Aktif')->count();
            }
        } catch (\Throwable $e) {
            Log::warning('DashboardController umkm: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('posyandu')) {
                $totalPosyandu = Posyandu::count();
            }
        } catch (\Throwable $e) {
            Log::warning('DashboardController posyandu: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('bank_sampah')) {
                $totalBankSampahKg = BankSampah::where('status', 'approved')->sum('berat_kg') ?? 0;
            }
        } catch (\Throwable $e) {
            Log::warning('DashboardController bank_sampah: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('koperasi_anggota')) {
                $totalKoperasiAnggota = KoperasiAnggota::where('status', 'aktif')->count();
            }
        } catch (\Throwable $e) {
            Log::warning('DashboardController koperasi_anggota: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('aset')) {
                $totalAset = Aset::count();
            }
        } catch (\Throwable $e) {
            Log::warning('DashboardController aset: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('surat_pengajuan')) {
                $suratMenunggu = SuratPengajuan::where('status', 'Menunggu')->count();
            }
        } catch (\Throwable $e) {
            Log::warning('DashboardController surat_pengajuan: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('kegiatan')) {
                $kegiatanTerbaru = Kegiatan::orderBy('tanggal_mulai', 'desc')->take(4)->get();
            }
        } catch (\Throwable $e) {
            Log::warning('DashboardController kegiatan: ' . $e->getMessage());
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
