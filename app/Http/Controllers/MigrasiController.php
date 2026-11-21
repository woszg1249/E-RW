<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrasiController extends Controller
{
    /**
     * Halaman Utama Pengelolaan Migrasi & Database (Khusus Admin)
     */
    public function index()
    {
        $dbName = DB::connection()->getDatabaseName();
        $dbHost = config('database.connections.mysql.host', '127.0.0.1');
        $dbDriver = config('database.default', 'mysql');

        // Ambil daftar tabel dan jumlah baris data
        $tables = [];
        try {
            $rawTables = DB::select('SHOW TABLES');
            foreach ($rawTables as $row) {
                $tblName = array_values((array) $row)[0];
                $count = DB::table($tblName)->count();
                $tables[] = [
                    'name' => $tblName,
                    'rows' => $count,
                ];
            }
        } catch (\Throwable $e) {
            $tables = [];
        }

        // Ambil riwayat migrasi yang sudah dieksekusi
        $migrations = [];
        if (Schema::hasTable('migrations')) {
            $migrations = DB::table('migrations')->orderBy('id', 'desc')->get();
        }

        return view('admin.migrasi', compact('dbName', 'dbHost', 'dbDriver', 'tables', 'migrations'));
    }

    /**
     * Eksekusi Migrasi Database (php artisan migrate --force)
     */
    public function runMigration(Request $request)
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            return back()->with('success', 'Migrasi database berhasil dijalankan!')->with('terminal_output', $output);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menjalankan migrasi: ' . $e->getMessage());
        }
    }

    /**
     * Eksekusi Test Data Seeder (php artisan db:seed --class=TestDataSeeder --force)
     */
    public function runSeeder(Request $request)
    {
        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\Seeders\TestDataSeeder',
                '--force' => true,
            ]);
            $output = Artisan::output();

            return back()->with('success', 'Test Data Seeder berhasil dijalankan! Seluruh data modul pengujian telah diperbarui.')->with('terminal_output', $output);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menjalankan seeder: ' . $e->getMessage());
        }
    }

    /**
     * Bersihkan Seluruh Cache Aplikasi (php artisan optimize:clear)
     */
    public function clearCache(Request $request)
    {
        try {
            Artisan::call('optimize:clear');
            $output = Artisan::output();

            return back()->with('success', 'Seluruh cache konfigurasi, route, view, dan cache Laravel berhasil dibersihkan!')->with('terminal_output', $output);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membersihkan cache: ' . $e->getMessage());
        }
    }
}
