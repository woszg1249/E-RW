<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppRoutesTest extends TestCase
{
    use RefreshDatabase;
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

    public function test_warga_cannot_access_buku_kas_rt(): void
    {
        $warga = User::where('role', 'Warga')->first();
        if (!$warga) {
            $this->markTestSkipped('Tidak ada user Warga di database');
        }

        $this->actingAs($warga);

        $response = $this->get('/keuangan');
        // Buku Kas RT hanya untuk admin
        $response->assertRedirect('/dashboard');

        // Namun Laporan Keuangan Bulanan tetap dapat diakses warga untuk transparansi
        $responseLaporan = $this->get('/keuangan/laporan');
        $responseLaporan->assertStatus(200);
        $responseLaporan->assertSee('Laporan Keuangan Bulanan');
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

        // Pastikan anggota keluarga terurut dengan Kepala Keluarga di urutan pertama
        $orderedMembers = User::where('no_kk', $warga->no_kk)->orderFamilyHierarchy()->get();
        if ($orderedMembers->where('status_keluarga', 'Kepala Keluarga')->isNotEmpty()) {
            $this->assertEquals('Kepala Keluarga', $orderedMembers->first()->status_keluarga);
        }

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

    public function test_pinjaman_koperasi_otomatis_disetujui_dan_terbit_ke_tagihan(): void
    {
        $warga = User::where('role', 'Warga')->first();
        if (!$warga) {
            $this->markTestSkipped('Tidak ada user Warga di database');
        }

        $this->actingAs($warga);

        $response = $this->post('/koperasi/pinjaman', [
            'jumlah' => 1000000,
            'tenor' => 12,
            'keterangan' => 'Pinjaman modal usaha mandiri',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan pinjaman berstatus 'disetujui' (bukan pending)
        $this->assertDatabaseHas('koperasi_pinjaman', [
            'id_user' => $warga->id,
            'jumlah' => 1000000,
            'tenor' => 12,
            'status' => 'disetujui',
        ]);

        // Pastikan otomatis terbit tagihan cicilan di tabel tagihan dengan status 'Belum Lunas'
        $tagihanKoperasi = \App\Models\Tagihan::where('id_user', $warga->id)
            ->where('status_bayar', 'Belum Lunas')
            ->where('bulan_tahun', 'like', '%Koperasi%')
            ->latest('id_tagihan')
            ->first();

        $this->assertNotNull($tagihanKoperasi);

        // Simulasi warga membayar tagihan cicilan koperasi via menu Tagihan
        $responseBayar = $this->post('/tagihan/' . $tagihanKoperasi->id_tagihan . '/bayar', [
            'metode' => 'QRIS Digital',
            'keterangan' => 'Bayar cicilan pertama koperasi',
        ]);

        $responseBayar->assertRedirect();

        // Pastikan tagihan lunas dan tercatat di cicilan koperasi
        $this->assertEquals('Lunas', $tagihanKoperasi->fresh()->status_bayar);
        $pinjamanId = \App\Models\KoperasiPinjaman::where('id_user', $warga->id)->latest('id')->value('id');
        $this->assertDatabaseHas('koperasi_cicilan', [
            'id_pinjaman' => $pinjamanId,
            'jumlah' => $tagihanKoperasi->jumlah,
        ]);
    }

    public function test_cetak_dokumen_posyandu_per_balita_works_cleanly(): void
    {
        $posyandu = \App\Models\Posyandu::first();
        if (!$posyandu) {
            $user = User::first();
            $posyandu = \App\Models\Posyandu::create([
                'id_warga' => $user->id,
                'nama_bayi' => 'Ananda Rizky Test',
                'jenis_kelamin' => 'Laki-laki',
                'usia_bulan' => 6,
                'berat_badan' => 7.5,
                'tinggi_badan' => 65.0,
                'lingkar_kepala' => 43.0,
                'status_kesehatan' => 'Gizi Baik / Normal (Sehat)',
                'tanggal_pemeriksaan' => now()->toDateString(),
            ]);
        }

        $admin = User::where('role', 'Admin')->first();
        $this->actingAs($admin);

        $response = $this->get('/posyandu/' . $posyandu->id . '/cetak');
        $response->assertStatus(200);
        $response->assertSee('SURAT KETERANGAN HASIL PEMERIKSAAN KESEHATAN BALITA');
        $response->assertSee($posyandu->nama_bayi);

        // Test Cetak Rekapitulasi Posyandu Seluruh Balita
        $responseRekap = $this->get('/posyandu/cetak');
        $responseRekap->assertStatus(200);
        $responseRekap->assertSee('DATA REKAPITULASI PENIMBANGAN', false);
    }
}
