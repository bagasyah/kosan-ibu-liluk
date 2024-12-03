<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments'; // Nama tabel di database
    // Tambahkan kolom yang dapat diisi jika diperlukan
    protected $fillable = ['tanggal_bayar', 'batas_pembayaran', 'status', 'price', 'user_id' ,'jenis'];

    protected $dates = ['tanggal_bayar', 'batas_pembayaran']; // Menambahkan kolom tanggal

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
