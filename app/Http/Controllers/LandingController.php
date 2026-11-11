<?php

namespace App\Http\Controllers;

use App\Models\BankSampah;
use App\Models\Kegiatan;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $totalWarga = User::where('role', 'Warga')->count();
        $totalKK = User::whereNotNull('no_kk')->where('no_kk', '!=', '')->distinct('no_kk')->count('no_kk');
        $totalUmkm = Umkm::where('status', 'Aktif')->count();
        $totalSampahKg = BankSampah::where('status', 'approved')->sum('berat_kg') ?? 0;
        $kegiatanTerbaru = Kegiatan::orderBy('tanggal_mulai', 'desc')->take(3)->get();

        return view('landing', compact(
            'totalWarga',
            'totalKK',
            'totalUmkm',
            'totalSampahKg',
            'kegiatanTerbaru'
        ));
    }

    public function panduan()
    {
        if (auth()->check()) {
            return view('panduan_app');
        }
        return view('panduan');
    }
}
