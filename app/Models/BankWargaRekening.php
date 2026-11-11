<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankWargaRekening extends Model
{
    use HasFactory;

    protected $table = 'bank_warga_rekening';

    protected $fillable = [
        'id_user',
        'nomor_rekening',
        'nik',
        'no_kk',
        'rfid_uid',
        'saldo',
        'status',
        'metode_daftar',
    ];

    protected function casts(): array
    {
        return [
            'saldo' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function transaksi()
    {
        return $this->hasMany(BankWargaTransaksi::class, 'id_rekening');
    }

    /**
     * Generate nomor rekening otomatis unik berbasis wilayah RT & User ID
     * Contoh: BW-1906-880012
     */
    public static function generateNomorRekening(User $user): string
    {
        $rt = str_pad($user->rt ?? '06', 2, '0', STR_PAD_LEFT);
        $userId = str_pad($user->id, 5, '0', STR_PAD_LEFT);
        return 'BW-19' . $rt . '-' . $userId;
    }

    /**
     * Dapatkan atau buatkan rekening otomatis untuk user
     */
    public static function getOrCreateForUser(User $user, string $metode = 'otomatis_akun'): self
    {
        $rekening = self::where('id_user', $user->id)->first();
        if (!$rekening) {
            $nomorRekening = self::generateNomorRekening($user);
            // Jaga-jaga bila duplicate
            while (self::where('nomor_rekening', $nomorRekening)->exists()) {
                $nomorRekening = 'BW-19' . str_pad($user->rt ?? '06', 2, '0', STR_PAD_LEFT) . '-' . mt_rand(10000, 99999);
            }

            $rekening = self::create([
                'id_user' => $user->id,
                'nomor_rekening' => $nomorRekening,
                'nik' => $user->nik,
                'no_kk' => $user->no_kk,
                'rfid_uid' => $user->rfid_uid,
                'saldo' => 0,
                'status' => 'aktif',
                'metode_daftar' => $metode,
            ]);
        }
        return $rekening;
    }
}
