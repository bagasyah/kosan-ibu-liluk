<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pengaduan extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak sesuai dengan konvensi
    protected $table = 'pengaduan';

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'user_id',
        'tanggal_pelaporan',
        'masalah',
        'status',
        'foto',
        'foto_akhir',
    ];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke model Kamar
    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }
}
