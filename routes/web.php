<?php

use App\Http\Controllers\AsetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankSampahController;
use App\Http\Controllers\BankWargaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KoperasiController;
use App\Http\Controllers\PosyanduController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\WargaController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/ajax/check-nik', [AuthController::class, 'checkNik'])->name('ajax.check.nik');
    Route::get('/ajax/check-kk', [AuthController::class, 'checkKk'])->name('ajax.check.kk');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public IoT Scan Endpoint
Route::post('/api/iot/koperasi/scan', [KoperasiController::class, 'apiIotScan'])->name('api.iot.koperasi.scan');
Route::post('/api/iot/bank-warga/tap', [BankWargaController::class, 'apiIotTap'])->name('api.iot.bank_warga.tap');

// Protected Routes (Warga & Admin)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Keuangan Kas RT (Khusus Admin / Pengurus RT)
    Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index')->middleware('admin');
    Route::post('/keuangan', [KeuanganController::class, 'store'])->name('keuangan.store')->middleware('admin');
    Route::delete('/keuangan/{id}', [KeuanganController::class, 'destroy'])->name('keuangan.destroy')->middleware('admin');
    Route::get('/keuangan/laporan', [KeuanganController::class, 'laporan'])->name('keuangan.laporan');

    // Tagihan Iuran
    Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
    Route::post('/tagihan/generate', [TagihanController::class, 'generate'])->name('tagihan.generate')->middleware('admin');
    Route::post('/tagihan/{id}/bayar', [TagihanController::class, 'bayar'])->name('tagihan.bayar');
    Route::post('/tagihan/{id}/dismiss', [TagihanController::class, 'dismissNotif'])->name('tagihan.dismiss');

    // Warga & Kartu Keluarga (KK)
    Route::get('/warga', [WargaController::class, 'index'])->name('warga.index')->middleware('admin');
    Route::post('/warga', [WargaController::class, 'store'])->name('warga.store')->middleware('admin');
    Route::put('/warga/{id}', [WargaController::class, 'update'])->name('warga.update')->middleware('admin');
    Route::delete('/warga/{id}', [WargaController::class, 'destroy'])->name('warga.destroy')->middleware('admin');
    Route::get('/kk', [WargaController::class, 'kelolaKK'])->name('warga.kk');
    Route::get('/kk/{no_kk}/cetak', [WargaController::class, 'cetakKK'])->name('warga.kk.cetak');

    // Kegiatan RT/RW
    Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
    Route::post('/kegiatan', [KegiatanController::class, 'store'])->name('kegiatan.store');
    Route::put('/kegiatan/{id}', [KegiatanController::class, 'update'])->name('kegiatan.update');
    Route::match(['get', 'delete'], '/kegiatan/{id}', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');
    Route::get('/kegiatan/{id}/hapus', [KegiatanController::class, 'destroy'])->name('kegiatan.hapus');

    // Surat Pengantar RT/RW
    Route::get('/surat', [SuratController::class, 'index'])->name('surat.index');
    Route::post('/surat', [SuratController::class, 'store'])->name('surat.store');
    Route::post('/surat/{id}/status', [SuratController::class, 'updateStatus'])->name('surat.status')->middleware('admin');
    Route::get('/surat/{id}/cetak', [SuratController::class, 'cetak'])->name('surat.cetak');

    // Posyandu Balita
    Route::get('/posyandu', [PosyanduController::class, 'index'])->name('posyandu.index');
    Route::post('/posyandu', [PosyanduController::class, 'store'])->name('posyandu.store');
    Route::delete('/posyandu/{id}', [PosyanduController::class, 'destroy'])->name('posyandu.destroy')->middleware('admin');
    Route::get('/posyandu/cetak', [PosyanduController::class, 'cetak'])->name('posyandu.cetak');
    Route::get('/posyandu/{id}/cetak', [PosyanduController::class, 'cetakDokumen'])->name('posyandu.cetak.dokumen');

    // Bank Sampah
    Route::get('/bank-sampah', [BankSampahController::class, 'index'])->name('bank-sampah.index');
    Route::post('/bank-sampah', [BankSampahController::class, 'store'])->name('bank-sampah.store');
    Route::post('/bank-sampah/{id}/status', [BankSampahController::class, 'updateStatus'])->name('bank-sampah.status')->middleware('admin');
    Route::delete('/bank-sampah/{id}', [BankSampahController::class, 'destroy'])->name('bank-sampah.destroy')->middleware('admin');

    // Bank Lokal Digital Warga RW 19
    Route::get('/bank-warga', [BankWargaController::class, 'index'])->name('bank-warga.index');
    Route::post('/bank-warga/topup', [BankWargaController::class, 'topup'])->name('bank-warga.topup');
    Route::post('/bank-warga/daftar', [BankWargaController::class, 'daftarOtomatis'])->name('bank-warga.daftar');

    // UMKM Warga
    Route::get('/umkm', [UmkmController::class, 'index'])->name('umkm.index');
    Route::post('/umkm', [UmkmController::class, 'store'])->name('umkm.store');
    Route::put('/umkm/{id}', [UmkmController::class, 'update'])->name('umkm.update');
    Route::delete('/umkm/{id}', [UmkmController::class, 'destroy'])->name('umkm.destroy')->middleware('admin');

    // Koperasi Simpan Pinjam
    Route::get('/koperasi', [KoperasiController::class, 'index'])->name('koperasi.index');
    Route::post('/koperasi/anggota', [KoperasiController::class, 'storeAnggota'])->name('koperasi.anggota');
    Route::post('/koperasi/simpanan', [KoperasiController::class, 'storeSimpanan'])->name('koperasi.simpanan');
    Route::post('/koperasi/pinjaman', [KoperasiController::class, 'storePinjaman'])->name('koperasi.pinjaman');
    Route::post('/koperasi/pinjaman/{id}/status', [KoperasiController::class, 'updateStatusPinjaman'])->name('koperasi.pinjaman.status')->middleware('admin');
    Route::post('/koperasi/cicilan', [KoperasiController::class, 'storeCicilan'])->name('koperasi.cicilan');

    // Aset & Inventaris RT
    Route::get('/aset', [AsetController::class, 'index'])->name('aset.index');
    Route::post('/aset', [AsetController::class, 'store'])->name('aset.store')->middleware('admin');
    Route::post('/aset/pinjam', [AsetController::class, 'pinjam'])->name('aset.pinjam');
    Route::post('/aset/pinjam/{id}/status', [AsetController::class, 'updateStatusPeminjaman'])->name('aset.pinjam.status')->middleware('admin');

    // Profil Pengguna
    Route::get('/profil', [ProfileController::class, 'index'])->name('profil.index');
    Route::post('/profil', [ProfileController::class, 'update'])->name('profil.update');
});
