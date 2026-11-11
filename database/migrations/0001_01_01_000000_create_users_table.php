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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 100);
                $table->string('username', 50)->unique();
                $table->string('email', 100)->unique();
                $table->string('password');
                $table->text('alamat')->nullable();
                $table->enum('kelamin', ['Laki-laki', 'Perempuan'])->default('Laki-laki');
                $table->string('no_hp', 15)->nullable();
                $table->string('foto')->default('profile.jpg');
                $table->enum('role', ['Admin', 'Warga'])->default('Warga');
                $table->text('info_iuran')->nullable();
                $table->string('nik', 50)->nullable();
                $table->string('rfid_uid', 50)->nullable();
                $table->string('no_kk', 50)->nullable();
                $table->string('rt', 10)->default('06');
                $table->string('rw', 10)->default('05');
                $table->string('status_keluarga', 50)->nullable()->default('Kepala Keluarga');
                $table->string('pekerjaan', 100)->nullable()->default('Belum / Tidak Bekerja');
                $table->string('status_perkawinan', 50)->nullable()->default('Kawin');
                $table->string('pendidikan', 50)->nullable()->default('SLTA / Sederajat');
                $table->string('agama', 50)->nullable()->default('Islam');
                $table->string('tempat_lahir', 100)->nullable()->default('Bekasi');
                $table->date('tanggal_lahir')->nullable()->default('2000-01-01');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop existing users table in development
    }
};
