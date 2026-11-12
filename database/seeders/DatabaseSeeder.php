<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Keuangan;
use App\Models\Posyandu;
use App\Models\BankSampah;
use App\Models\RekeningWarga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with essential production & initial data.
     */
    public function run(): void
    {
        // 1. Akun Admin Utama
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Reival al khafi',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin'),
                'role' => 'Admin',
                'alamat' => 'Jl. Mawar No. 01 RT 06/RW 19',
                'kelamin' => 'Laki-laki',
                'no_hp' => '081234567890',
                'nik' => '3275010101900001',
                'no_kk' => '3275010101900001',
                'rt' => '06',
                'rw' => '19',
                'status_keluarga' => 'Kepala Keluarga',
            ]
        );
        $admin->password = Hash::make('admin');
        $admin->role = 'Admin';
        $admin->save();

        // 2. Akun Ketua RT 06 (Dimas)
        $dimas = User::firstOrCreate(
            ['username' => 'dimas'],
            [
                'nama' => 'Dimas Purnomo',
                'email' => 'dimas.rt@gmail.com',
                'password' => Hash::make('Password123'),
                'role' => 'Admin',
                'alamat' => 'Jl. Kenanga No. 12 RT 06/RW 19',
                'kelamin' => 'Laki-laki',
                'no_hp' => '081298765432',
                'nik' => '3275010101900002',
                'no_kk' => '3275010101900002',
                'rt' => '06',
                'rw' => '19',
                'status_keluarga' => 'Kepala Keluarga',
            ]
        );
        $dimas->password = Hash::make('Password123');
        $dimas->role = 'Admin';
        $dimas->save();

        // 3. Akun Ketua RW 19 (Fabian)
        $fabian = User::firstOrCreate(
            ['username' => 'fabian'],
            [
                'nama' => 'Fabian',
                'email' => 'fabian.rw@gmail.com',
                'password' => Hash::make('Password123'),
                'role' => 'Admin',
                'alamat' => 'Jl. Anggrek Utama No. 05 RW 19',
                'kelamin' => 'Laki-laki',
                'no_hp' => '081311223344',
                'nik' => '3275010101900003',
                'no_kk' => '3275010101900003',
                'rt' => '06',
                'rw' => '19',
                'status_keluarga' => 'Kepala Keluarga',
            ]
        );
        $fabian->password = Hash::make('Password123');
        $fabian->role = 'Admin';
        $fabian->save();

        // 4. Akun Ketua RT 07 (Bambang)
        $rt07 = User::firstOrCreate(
            ['username' => 'rt07'],
            [
                'nama' => 'Bambang',
                'email' => 'rt07@rtrwdigital.local',
                'password' => Hash::make('Password123'),
                'role' => 'Admin',
                'alamat' => 'Jl. Melati Indah RT 07/RW 19',
                'kelamin' => 'Laki-laki',
                'no_hp' => '081399887766',
                'nik' => '3275010101900004',
                'no_kk' => '3275010101900004',
                'rt' => '07',
                'rw' => '19',
                'status_keluarga' => 'Kepala Keluarga',
            ]
        );
        $rt07->password = Hash::make('Password123');
        $rt07->role = 'Admin';
        $rt07->save();

        // 5. Akun Warga Contoh: Agus Kusuma
        $warga1 = User::firstOrCreate(
            ['username' => 'gus.usuma31'],
            [
                'nama' => 'Agus Kusuma',
                'email' => 'agus.kusuma@gmail.com',
                'password' => Hash::make('Password123'),
                'role' => 'Warga',
                'alamat' => 'Jl. Melati No. 20 RT 06/RW 19',
                'kelamin' => 'Laki-laki',
                'no_hp' => '085712345678',
                'nik' => '3275012304850005',
                'no_kk' => '3275012304850005',
                'rt' => '06',
                'rw' => '19',
                'status_keluarga' => 'Kepala Keluarga',
            ]
        );
        $warga1->password = Hash::make('Password123');
        $warga1->role = 'Warga';
        $warga1->save();

        // 6. Akun Warga Contoh: Tasya Rahmadani
        $warga2 = User::firstOrCreate(
            ['username' => 'tasya'],
            [
                'nama' => 'Tasya Rahmadani',
                'email' => 'tasya.rahmadani@gmail.com',
                'password' => Hash::make('Password123'),
                'role' => 'Warga',
                'alamat' => 'Jl. Mawar Raya No. 08 RT 06/RW 19',
                'kelamin' => 'Perempuan',
                'no_hp' => '087812345678',
                'nik' => '3270000878865562',
                'no_kk' => '3270000878865562',
                'rt' => '06',
                'rw' => '19',
                'status_keluarga' => 'Kepala Keluarga',
            ]
        );
        $warga2->password = Hash::make('Password123');
        $warga2->role = 'Warga';
        $warga2->save();

        // Rekening Bank Warga Awal untuk Agus Kusuma & Tasya
        if (class_exists(RekeningWarga::class)) {
            RekeningWarga::firstOrCreate(
                ['user_id' => $warga1->id],
                [
                    'nomor_rekening' => 'BW-1906-880012',
                    'saldo' => 125000,
                    'status' => 'aktif',
                    'pin' => Hash::make('123456'),
                ]
            );
            RekeningWarga::firstOrCreate(
                ['user_id' => $warga2->id],
                [
                    'nomor_rekening' => 'BW-1906-880013',
                    'saldo' => 75000,
                    'status' => 'aktif',
                    'pin' => Hash::make('123456'),
                ]
            );
        }

        // Data Kas Keuangan Awal jika kosong
        if (class_exists(Keuangan::class) && Keuangan::count() === 0) {
            Keuangan::create([
                'nama_warga' => 'Warga RW 19',
                'jenis' => 'masuk',
                'keterangan' => 'Saldo Awal Kas Masuk RW 19',
                'jumlah' => 6524046,
                'tanggal' => now()->format('Y-m-d'),
            ]);
            Keuangan::create([
                'nama_warga' => 'unit cctv 20 unit pak RW',
                'jenis' => 'keluar',
                'keterangan' => 'Pembelian alat cctv',
                'jumlah' => 1200000,
                'tanggal' => now()->format('Y-m-d'),
            ]);
            Keuangan::create([
                'nama_warga' => 'Kas Pengurus RT',
                'jenis' => 'keluar',
                'keterangan' => 'biaya jasa fogging nyamuk',
                'jumlah' => 500000,
                'tanggal' => now()->subDays(5)->format('Y-m-d'),
            ]);
            Keuangan::create([
                'nama_warga' => 'Kas Pengurus RT',
                'jenis' => 'keluar',
                'keterangan' => 'biaya saja fogging nyamuk',
                'jumlah' => 500000,
                'tanggal' => now()->subDays(10)->format('Y-m-d'),
            ]);
            Keuangan::create([
                'nama_warga' => 'Kas Pengurus RT',
                'jenis' => 'keluar',
                'keterangan' => 'berobatan warga',
                'jumlah' => 100000,
                'tanggal' => now()->subDays(15)->format('Y-m-d'),
            ]);
        }
    }
}
