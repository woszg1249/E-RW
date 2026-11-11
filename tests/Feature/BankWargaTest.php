<?php

namespace Tests\Feature;

use App\Models\BankSampah;
use App\Models\BankWargaRekening;
use App\Models\BankWargaTransaksi;
use App\Models\Keuangan;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankWargaTest extends TestCase
{
    public function test_warga_otomatis_memiliki_rekening_saat_akses_bank_warga(): void
    {
        $warga = User::where('role', 'Warga')->first();
        if (!$warga) {
            $warga = User::create([
                'nama' => 'Budi Santoso',
                'username' => 'budi.santoso',
                'password' => bcrypt('password'),
                'role' => 'Warga',
                'nik' => '3275019988770001',
                'rt' => '06',
            ]);
        }

        $this->actingAs($warga);

        $response = $this->get('/bank-warga');
        $response->assertStatus(200);
        $response->assertSee('BANK DIGITAL WARGA');

        $this->assertDatabaseHas('bank_warga_rekening', [
            'id_user' => $warga->id,
        ]);
    }

    public function test_topup_saldo_bank_warga_berhasil(): void
    {
        $warga = User::where('role', 'Warga')->first();
        $this->actingAs($warga);

        $rekening = BankWargaRekening::getOrCreateForUser($warga);
        $saldoAwal = (float) $rekening->saldo;

        $response = $this->post('/bank-warga/topup', [
            'jumlah' => 50000,
            'metode' => 'Transfer Bank BCA',
        ]);

        $response->assertRedirect();
        $this->assertEquals($saldoAwal + 50000, (float) $rekening->fresh()->saldo);

        $this->assertDatabaseHas('bank_warga_transaksi', [
            'id_rekening' => $rekening->id,
            'jenis' => 'masuk',
            'jumlah' => 50000,
        ]);
    }

    public function test_setoran_bank_sampah_otomatis_transfer_ke_bank_warga(): void
    {
        $admin = User::where('role', 'Admin')->first();
        $warga = User::where('role', 'Warga')->first();

        $this->actingAs($admin);

        $rekening = BankWargaRekening::getOrCreateForUser($warga);
        $saldoAwal = (float) $rekening->saldo;

        // Admin mencatat setoran bank sampah untuk warga
        $response = $this->post('/bank-sampah', [
            'id_user' => $warga->id,
            'jenis_sampah' => 'Botol Plastik Bersih',
            'berat_kg' => 10,
            'harga_per_kg' => 3000,
            'catatan' => 'Setoran dari rumah warga',
        ]);

        $response->assertRedirect();
        $expectedTotal = 30000;

        // Saldo Bank Warga otomatis bertambah
        $this->assertEquals($saldoAwal + $expectedTotal, (float) $rekening->fresh()->saldo);

        $this->assertDatabaseHas('bank_warga_transaksi', [
            'id_rekening' => $rekening->id,
            'kategori' => 'bank_sampah',
            'jumlah' => $expectedTotal,
        ]);
    }

    public function test_bayar_tagihan_iuran_memotong_saldo_bank_warga(): void
    {
        $warga = User::where('role', 'Warga')->first();
        $rekening = BankWargaRekening::getOrCreateForUser($warga);

        // Isi saldo warga agar cukup
        $rekening->saldo = 100000;
        $rekening->save();

        // Buat tagihan iuran
        $tagihan = Tagihan::create([
            'id_user' => $warga->id,
            'bulan_tahun' => 'Iuran Uji Coba Bank Warga',
            'tanggal_tagihan' => now()->toDateString(),
            'jumlah' => 25000,
            'status_bayar' => 'Belum Lunas',
            'tgl_buat' => now(),
        ]);

        $this->actingAs($warga);

        $response = $this->post('/tagihan/' . $tagihan->id_tagihan . '/bayar', [
            'metode' => 'Bank Digital Warga',
        ]);

        $response->assertRedirect();

        // Tagihan menjadi lunas
        $this->assertEquals('Lunas', $tagihan->fresh()->status_bayar);

        // Saldo bank warga berkurang 25000
        $this->assertEquals(75000, (float) $rekening->fresh()->saldo);

        // Tercatat transaksi keluar di buku tabungan
        $this->assertDatabaseHas('bank_warga_transaksi', [
            'id_rekening' => $rekening->id,
            'jenis' => 'keluar',
            'kategori' => 'bayar_iuran',
            'jumlah' => 25000,
        ]);

        // Uang iuran tercatat masuk ke Kas RT
        $this->assertDatabaseHas('keuangan', [
            'nama_warga' => $warga->nama,
            'jenis' => 'masuk',
            'jumlah' => 25000,
        ]);
    }

    public function test_api_iot_tap_ktp_bank_warga(): void
    {
        $warga = User::where('role', 'Warga')->first();

        $response = $this->postJson('/api/iot/bank-warga/tap', [
            'nik' => $warga->nik,
            'device_id' => 'IOT-BW-UNITTEST',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'nama' => $warga->nama,
            ],
        ]);
    }
}
