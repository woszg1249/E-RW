<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AppRoutesTest extends TestCase
{
    public function test_guest_can_see_login_and_register_pages(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_access_all_modules(): void
    {
        $admin = User::where('role', 'Admin')->first();
        $this->actingAs($admin);

        $routes = [
            '/',
            'dashboard',
            'keuangan',
            'keuangan/laporan',
            'tagihan',
            'warga',
            'kk',
            'kegiatan',
            'surat',
            'posyandu',
            'posyandu/cetak',
            'bank-sampah',
            'umkm',
            'koperasi',
            'aset',
            'profil',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_iot_rfid_scan_endpoint_works(): void
    {
        $response = $this->postJson('/api/iot/koperasi/scan', [
            'device_id' => 'IOT-TEST-01',
            'nik' => '3275012304950001',
            'rfid_uid' => 'E28011700000020',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
    }
}
