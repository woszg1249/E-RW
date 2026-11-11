<?php

namespace App\Services;

use App\Models\BankSampah;
use App\Models\BankWargaRekening;
use App\Models\BankWargaTransaksi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BankWargaService
{
    /**
     * Tambah saldo (kredit) ke rekening Bank Warga
     */
    public static function setorDana(
        BankWargaRekening $rekening,
        float $jumlah,
        string $kategori,
        string $keterangan,
        ?string $metodeDetail = null,
        ?string $refId = null
    ): BankWargaTransaksi {
        return DB::transaction(function () use ($rekening, $jumlah, $kategori, $keterangan, $metodeDetail, $refId) {
            // Lock row untuk integritas saldo
            $rek = BankWargaRekening::lockForUpdate()->find($rekening->id);
            $saldoSebelum = (float) $rek->saldo;
            $saldoSesudah = $saldoSebelum + $jumlah;

            $rek->saldo = $saldoSesudah;
            $rek->save();

            $trx = BankWargaTransaksi::create([
                'id_rekening' => $rek->id,
                'kode_transaksi' => 'TRX-BW-' . date('YmdHis') . '-' . strtoupper(Str::random(4)),
                'jenis' => 'masuk',
                'kategori' => $kategori,
                'jumlah' => $jumlah,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'keterangan' => $keterangan,
                'metode_detail' => $metodeDetail,
                'referensi_id' => $refId ? (string) $refId : null,
                'status' => 'berhasil',
            ]);

            return $trx;
        });
    }

    /**
     * Kurangi saldo (debit) dari rekening Bank Warga (misal untuk bayar iuran)
     */
    public static function tarikDana(
        BankWargaRekening $rekening,
        float $jumlah,
        string $kategori,
        string $keterangan,
        ?string $metodeDetail = null,
        ?string $refId = null
    ): BankWargaTransaksi {
        return DB::transaction(function () use ($rekening, $jumlah, $kategori, $keterangan, $metodeDetail, $refId) {
            $rek = BankWargaRekening::lockForUpdate()->find($rekening->id);
            $saldoSebelum = (float) $rek->saldo;

            if ($saldoSebelum < $jumlah) {
                throw new \Exception("Saldo Bank Digital Warga tidak mencukupi. Saldo saat ini: Rp " . number_format($saldoSebelum, 0, ',', '.') . ", dibutuhkan: Rp " . number_format($jumlah, 0, ',', '.'));
            }

            $saldoSesudah = $saldoSebelum - $jumlah;
            $rek->saldo = $saldoSesudah;
            $rek->save();

            $trx = BankWargaTransaksi::create([
                'id_rekening' => $rek->id,
                'kode_transaksi' => 'TRX-BW-' . date('YmdHis') . '-' . strtoupper(Str::random(4)),
                'jenis' => 'keluar',
                'kategori' => $kategori,
                'jumlah' => $jumlah,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'keterangan' => $keterangan,
                'metode_detail' => $metodeDetail,
                'referensi_id' => $refId ? (string) $refId : null,
                'status' => 'berhasil',
            ]);

            return $trx;
        });
    }

    /**
     * Transfer otomatis hasil setoran bank sampah ke Bank Warga
     */
    public static function transferDariBankSampah(BankSampah $bankSampah): ?BankWargaTransaksi
    {
        if ((float) $bankSampah->total_harga <= 0) {
            return null;
        }

        $user = User::find($bankSampah->id_user);
        if (!$user) {
            return null;
        }

        // Dapatkan atau buatkan rekening otomatis untuk warga ini
        $rekening = BankWargaRekening::getOrCreateForUser($user, 'otomatis_bank_sampah');

        // Pastikan tidak double credit untuk referensi setoran sampah yang sama
        $sudahAda = BankWargaTransaksi::where('id_rekening', $rekening->id)
            ->where('kategori', 'bank_sampah')
            ->where('referensi_id', (string) $bankSampah->id)
            ->first();

        if ($sudahAda) {
            return $sudahAda;
        }

        return self::setorDana(
            $rekening,
            (float) $bankSampah->total_harga,
            'bank_sampah',
            "Setoran Bank Sampah: " . $bankSampah->berat_kg . " kg " . $bankSampah->jenis_sampah,
            'Bank Sampah RW 19',
            (string) $bankSampah->id
        );
    }
}
