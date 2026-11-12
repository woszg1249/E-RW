<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureBaseUsersExist();
    }

    protected function ensureBaseUsersExist(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Admin Test',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('admin'),
                'role' => 'Admin',
                'rt' => '06',
                'rw' => '19',
                'nik' => '3275010101900001',
                'no_kk' => '3275010101900001',
                'status_keluarga' => 'Kepala Keluarga',
            ]
        );

        User::firstOrCreate(
            ['username' => 'gus.usuma31'],
            [
                'nama' => 'Agus Kusuma',
                'email' => 'agus.kusuma@gmail.com',
                'password' => bcrypt('Password123'),
                'role' => 'Warga',
                'rt' => '06',
                'rw' => '19',
                'nik' => '3275012304850005',
                'no_kk' => '3275012304850005',
                'status_keluarga' => 'Kepala Keluarga',
            ]
        );

        User::firstOrCreate(
            ['username' => 'warga_tetangga'],
            [
                'nama' => 'Warga Tetangga',
                'email' => 'tetangga@gmail.com',
                'password' => bcrypt('Password123'),
                'role' => 'Warga',
                'rt' => '07',
                'rw' => '19',
                'nik' => '3275019999990002',
                'no_kk' => '3275019999990002',
                'status_keluarga' => 'Kepala Keluarga',
            ]
        );
    }
}
