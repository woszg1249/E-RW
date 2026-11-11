<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rekening Bank Digital Warga RW 19
        if (!Schema::hasTable('bank_warga_rekening')) {
            Schema::create('bank_warga_rekening', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_user')->unique();
                $table->string('nomor_rekening', 30)->unique();
                $table->string('nik', 30)->nullable();
                $table->string('no_kk', 30)->nullable();
                $table->string('rfid_uid', 50)->nullable()->index();
                $table->decimal('saldo', 15, 2)->default(0);
                $table->enum('status', ['aktif', 'dibekukan', 'tutup'])->default('aktif');
                $table->string('metode_daftar', 50)->default('otomatis_akun'); // 'otomatis_akun', 'iot_tap_ktp', 'manual'
                $table->timestamps();
            });
        }

        // 2. Transaksi / Mutasi Buku Tabungan Bank Warga
        if (!Schema::hasTable('bank_warga_transaksi')) {
            Schema::create('bank_warga_transaksi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_rekening');
                $table->string('kode_transaksi', 60)->unique();
                $table->enum('jenis', ['masuk', 'keluar']); // masuk = kredit/topup, keluar = debit/bayar
                $table->string('kategori', 50); // topup_bank, topup_ewallet, bank_sampah, bayar_iuran, bayar_tagihan, transfer
                $table->decimal('jumlah', 15, 2);
                $table->decimal('saldo_sebelum', 15, 2)->default(0);
                $table->decimal('saldo_sesudah', 15, 2)->default(0);
                $table->string('keterangan', 255);
                $table->string('metode_detail', 100)->nullable();
                $table->string('referensi_id', 100)->nullable();
                $table->enum('status', ['berhasil', 'pending', 'gagal'])->default('berhasil');
                $table->timestamps();

                $table->foreign('id_rekening')->references('id')->on('bank_warga_rekening')->onDelete('cascade');
                $table->index('jenis');
                $table->index('kategori');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_warga_transaksi');
        Schema::dropIfExists('bank_warga_rekening');
    }
};
