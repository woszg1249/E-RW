<?php

namespace App\Http\Controllers;

use App\Models\BankSampah;
use App\Models\Kegiatan;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class LandingController extends Controller
{
    public function index()
    {
        $totalWarga = 0;
        $totalKK = 0;
        $totalUmkm = 0;
        $totalSampahKg = 0;
        $kegiatanTerbaru = collect();

        try {
            if (Schema::hasTable('users')) {
                $totalWarga = User::where('role', 'Warga')->count();
                $totalKK = User::whereNotNull('no_kk')->where('no_kk', '!=', '')->distinct()->count('no_kk');
            }
        } catch (\Throwable $e) {
            Log::warning('LandingController users query: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('umkm')) {
                $totalUmkm = Umkm::where('status', 'Aktif')->count();
            }
        } catch (\Throwable $e) {
            Log::warning('LandingController umkm query: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('bank_sampah')) {
                $totalSampahKg = BankSampah::where('status', 'approved')->sum('berat_kg') ?? 0;
            }
        } catch (\Throwable $e) {
            Log::warning('LandingController bank_sampah query: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('kegiatan')) {
                $kegiatanTerbaru = Kegiatan::orderBy('tanggal_mulai', 'desc')->take(3)->get();
            }
        } catch (\Throwable $e) {
            Log::warning('LandingController kegiatan query: ' . $e->getMessage());
        }

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
