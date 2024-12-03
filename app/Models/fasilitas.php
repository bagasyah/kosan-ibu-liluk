<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dari konvensi
    protected $table = 'fasilitas';

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'indekos_id',
        'nama_fasilitas',
    ];

    // Relasi dengan model Indekos
    public function indekos()
    {
        return $this->belongsTo(Indekos::class);
    }
}
