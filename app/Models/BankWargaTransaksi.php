<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankWargaTransaksi extends Model
{
    use HasFactory;

    protected $table = 'bank_warga_transaksi';

    protected $fillable = [
        'id_rekening',
        'kode_transaksi',
        'jenis', // 'masuk' atau 'keluar'
        'kategori', // 'topup_bank', 'topup_ewallet', 'bank_sampah', 'bayar_iuran', 'bayar_tagihan', 'transfer'
        'jumlah',
        'saldo_sebelum',
        'saldo_sesudah',
        'keterangan',
        'metode_detail',
        'referensi_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'saldo_sebelum' => 'decimal:2',
            'saldo_sesudah' => 'decimal:2',
        ];
    }

    public function rekening()
    {
        return $this->belongsTo(BankWargaRekening::class, 'id_rekening');
    }
}
