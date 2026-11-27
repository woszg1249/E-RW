<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pengaduan_warga')) {
            Schema::create('pengaduan_warga', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_user');
                $table->string('kategori', 100);
                $table->string('judul', 150);
                $table->text('isi_laporan');
                $table->string('lokasi', 150)->nullable();
                $table->string('foto')->nullable();
                $table->string('status', 30)->default('Menunggu');
                $table->text('tanggapan_admin')->nullable();
                $table->string('ditanggapi_oleh', 100)->nullable();
                $table->timestamps();

                $table->index('id_user');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan_warga');
    }
};
