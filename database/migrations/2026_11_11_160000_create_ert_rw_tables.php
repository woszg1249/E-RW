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
        // 1. Aset
        if (!Schema::hasTable('aset')) {
            Schema::create('aset', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama', 255);
                $table->text('deskripsi')->nullable();
                $table->string('kategori', 100)->nullable()->default('Umum');
                $table->string('kondisi', 100)->nullable()->default('Baik');
                $table->string('lokasi', 255)->nullable()->default('-');
                $table->integer('jumlah')->nullable()->default(1);
                $table->string('status', 50)->nullable()->default('Aktif');
                $table->timestamp('created_at')->nullable()->useCurrent();
            });
        }

        // 2. Peminjaman Aset
        if (!Schema::hasTable('peminjaman_aset')) {
            Schema::create('peminjaman_aset', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_aset');
                $table->integer('id_user');
                $table->integer('jumlah_pinjam')->default(1);
                $table->date('tanggal_pinjam');
                $table->date('tanggal_kembali')->nullable();
                $table->string('status', 50)->nullable()->default('Menunggu');
                $table->text('catatan')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent();
            });
        }

        // 3. Bank Sampah
        if (!Schema::hasTable('bank_sampah')) {
            Schema::create('bank_sampah', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_user');
                $table->string('nama_warga', 100)->nullable();
                $table->string('jenis_sampah', 100);
                $table->decimal('berat_kg', 10, 2);
                $table->decimal('harga_per_kg', 10, 2);
                $table->decimal('total_harga', 10, 2);
                $table->enum('status', ['pending', 'approved', 'rejected'])->nullable()->default('pending');
                $table->dateTime('tanggal_pengumpulan')->nullable()->useCurrent();
                $table->dateTime('tanggal_diproses')->nullable();
                $table->text('catatan')->nullable();
            });
        }

        // 4. Keuangan
        if (!Schema::hasTable('keuangan')) {
            Schema::create('keuangan', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama_warga', 100)->nullable();
                $table->enum('jenis', ['masuk', 'keluar'])->nullable();
                $table->string('keterangan', 255)->nullable();
                $table->integer('jumlah')->nullable();
                $table->date('tanggal')->nullable();
            });
        }

        // 5. Bukti Kas
        if (!Schema::hasTable('bukti_kas')) {
            Schema::create('bukti_kas', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_keuangan');
                $table->string('filename', 255);
                $table->dateTime('uploaded_at')->nullable()->useCurrent();
            });
        }

        // 6. Tagihan
        if (!Schema::hasTable('tagihan')) {
            Schema::create('tagihan', function (Blueprint $table) {
                $table->increments('id_tagihan');
                $table->integer('id_user');
                $table->string('bulan_tahun', 100)->nullable();
                $table->date('tanggal_tagihan')->nullable();
                $table->integer('jumlah');
                $table->string('status_bayar', 20)->nullable();
                $table->timestamp('tgl_update')->nullable()->useCurrent();
                $table->dateTime('tgl_buat')->nullable()->useCurrent();
            });
        }

        // 7. Transaksi Pembayaran
        if (!Schema::hasTable('transaksi_pembayaran')) {
            Schema::create('transaksi_pembayaran', function (Blueprint $table) {
                $table->increments('id');
                $table->string('order_id', 64)->unique();
                $table->integer('id_user')->nullable();
                $table->string('nama_warga', 100);
                $table->string('jenis_kas', 20)->nullable()->default('masuk');
                $table->string('keterangan', 255);
                $table->integer('jumlah');
                $table->integer('id_tagihan')->nullable();
                $table->string('metode', 50)->nullable()->default('QRIS');
                $table->string('status', 30)->nullable()->default('pending');
                $table->dateTime('created_at')->nullable()->useCurrent();
                $table->dateTime('paid_at')->nullable();
            });
        }

        // 8. Notif Dismissed
        if (!Schema::hasTable('notif_dismissed')) {
            Schema::create('notif_dismissed', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_tagihan');
                $table->integer('id_user');
                $table->dateTime('created_at')->nullable()->useCurrent();
            });
        }

        // 9. Kegiatan
        if (!Schema::hasTable('kegiatan')) {
            Schema::create('kegiatan', function (Blueprint $table) {
                $table->increments('id');
                $table->string('judul', 255);
                $table->text('deskripsi')->nullable();
                $table->date('tanggal_mulai')->nullable();
                $table->date('tanggal_selesai')->nullable();
                $table->string('waktu', 50)->nullable();
                $table->string('lokasi', 255)->nullable();
                $table->integer('dibuat_oleh')->nullable();
                $table->dateTime('tanggal_dibuat')->nullable()->useCurrent();
            });
        }

        // 10. Koperasi Anggota
        if (!Schema::hasTable('koperasi_anggota')) {
            Schema::create('koperasi_anggota', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_user');
                $table->dateTime('tanggal_gabung')->nullable()->useCurrent();
                $table->string('status', 30)->nullable()->default('aktif');
            });
        }

        // 11. Koperasi Simpanan
        if (!Schema::hasTable('koperasi_simpanan')) {
            Schema::create('koperasi_simpanan', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_user');
                $table->enum('jenis', ['setoran', 'penarikan']);
                $table->decimal('jumlah', 14, 2);
                $table->dateTime('tanggal')->nullable()->useCurrent();
                $table->text('catatan')->nullable();
            });
        }

        // 12. Koperasi Pinjaman
        if (!Schema::hasTable('koperasi_pinjaman')) {
            Schema::create('koperasi_pinjaman', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_user');
                $table->decimal('jumlah', 14, 2);
                $table->integer('tenor');
                $table->decimal('bunga', 5, 2);
                $table->enum('status', ['pending', 'disetujui', 'ditolak', 'lunas'])->nullable()->default('pending');
                $table->dateTime('tanggal_pengajuan')->nullable()->useCurrent();
                $table->text('keterangan')->nullable();
                $table->string('metode_pengajuan', 50)->nullable()->default('manual');
                $table->string('scan_device_id', 50)->nullable();
                $table->string('nik_peminjam', 50)->nullable();
            });
        }

        // 13. Koperasi Cicilan
        if (!Schema::hasTable('koperasi_cicilan')) {
            Schema::create('koperasi_cicilan', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_pinjaman');
                $table->decimal('jumlah', 14, 2);
                $table->dateTime('tanggal')->nullable()->useCurrent();
                $table->text('catatan')->nullable();
            });
        }

        // 14. IoT Koperasi Scans
        if (!Schema::hasTable('iot_koperasi_scans')) {
            Schema::create('iot_koperasi_scans', function (Blueprint $table) {
                $table->increments('id');
                $table->string('device_id', 50)->nullable()->default('IOT-READER-01');
                $table->string('nik', 50)->nullable();
                $table->string('rfid_uid', 50)->nullable();
                $table->integer('id_user')->nullable();
                $table->enum('status', ['pending', 'processed', 'expired'])->nullable()->default('pending');
                $table->dateTime('scanned_at')->nullable()->useCurrent();
            });
        }

        // 15. Posyandu
        if (!Schema::hasTable('posyandu')) {
            Schema::create('posyandu', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_warga')->nullable();
                $table->string('nama_bayi', 100);
                $table->integer('usia_bulan');
                $table->decimal('berat_badan', 5, 2);
                $table->decimal('tinggi_badan', 5, 2)->nullable();
                $table->decimal('lingkar_kepala', 5, 2)->nullable();
                $table->string('status_kesehatan', 50)->nullable();
                $table->text('catatan')->nullable();
                $table->dateTime('tanggal_pemeriksaan')->nullable()->useCurrent();
                $table->string('pemeriksaan_oleh', 100)->nullable();
                $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable()->default('Laki-laki');
            });
        }

        // 16. Surat Pengajuan
        if (!Schema::hasTable('surat_pengajuan')) {
            Schema::create('surat_pengajuan', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_user');
                $table->string('nama', 255);
                $table->string('jenis_surat', 100);
                $table->string('nik', 50)->nullable();
                $table->string('ttl', 255)->nullable();
                $table->text('alamat')->nullable();
                $table->text('tujuan')->nullable();
                $table->string('nama_rt', 100)->nullable();
                $table->string('nama_rw', 100)->nullable();
                $table->string('kelurahan', 100)->nullable();
                $table->string('kecamatan', 100)->nullable();
                $table->string('kota', 100)->nullable();
                $table->date('tanggal_permohonan')->nullable();
                $table->text('detail_usaha')->nullable();
                $table->text('keterangan')->nullable();
                $table->string('status', 50)->nullable()->default('Menunggu');
                $table->timestamp('created_at')->nullable()->useCurrent();
            });
        }

        // 17. UMKM
        if (!Schema::hasTable('umkm')) {
            Schema::create('umkm', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama_usaha', 255);
                $table->string('kategori', 100)->nullable()->default('Umum');
                $table->text('deskripsi')->nullable();
                $table->string('alamat', 255)->nullable()->default('-');
                $table->string('jam_operasional', 255)->nullable()->default('-');
                $table->string('whatsapp', 50)->nullable()->default('-');
                $table->string('foto', 255)->nullable();
                $table->text('produk_layanan')->nullable();
                $table->string('estimasi_jarak', 100)->nullable()->default('-');
                $table->string('google_maps_link', 255)->nullable()->default('-');
                $table->string('status', 50)->nullable()->default('Aktif');
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent();
            });
        }

        // 18. Warga
        if (!Schema::hasTable('warga')) {
            Schema::create('warga', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama', 100)->nullable();
                $table->text('alamat')->nullable();
                $table->string('no_hp', 20)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
