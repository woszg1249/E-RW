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

    public function test_warga_cannot_access_daftar_warga_induk(): void
    {
        $warga = User::where('role', 'Warga')->first();
        if (!$warga) {
            $this->markTestSkipped('Tidak ada user Warga di database');
        }

        $this->actingAs($warga);

        $response = $this->get('/warga');
        // Harus ditolak dan dialihkan demi proteksi privasi
        $response->assertRedirect('/dashboard');
    }

    public function test_warga_can_access_own_kk_and_cannot_print_other_kk(): void
    {
        $warga = User::where('role', 'Warga')->whereNotNull('no_kk')->first();
        if (!$warga) {
            $this->markTestSkipped('Tidak ada user Warga dengan KK');
        }

        $this->actingAs($warga);

        // Akses halaman KK sendiri
        $response = $this->get('/kk');
        $response->assertStatus(200);
        $response->assertSee('Kartu Keluarga Saya');

        // Cetak KK sendiri diizinkan
        $responseCetakOwn = $this->get('/kk/' . $warga->no_kk . '/cetak');
        $responseCetakOwn->assertStatus(200);

        // Cetak KK orang lain harus ditolak 403
        $otherKk = User::where('no_kk', '!=', $warga->no_kk)->whereNotNull('no_kk')->value('no_kk');
        if ($otherKk) {
            $responseCetakOther = $this->get('/kk/' . $otherKk . '/cetak');
            $responseCetakOther->assertStatus(403);
        }
    }

    public function test_bayar_tagihan_berhasil_tanpa_error(): void
    {
        $tagihan = \App\Models\Tagihan::where('status_bayar', 'Belum Lunas')->first();
        if (!$tagihan) {
            $user = User::first();
            $tagihan = \App\Models\Tagihan::create([
                'id_user' => $user->id,
                'bulan_tahun' => 'November 2026',
                'jumlah' => 50000,
                'status_bayar' => 'Belum Lunas',
                'jatuh_tempo' => now()->addDays(5),
            ]);
        }

        $this->actingAs($tagihan->user ?? User::first());

        $response = $this->post('/tagihan/' . $tagihan->id_tagihan . '/bayar', [
            'metode' => 'QRIS Digital',
            'keterangan' => 'Uji coba pembayaran otomatis',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tagihan', [
            'id_tagihan' => $tagihan->id_tagihan,
            'status_bayar' => 'Lunas',
        ]);
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
