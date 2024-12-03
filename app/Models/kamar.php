<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_kamar',
        'status',
        'harga',
        'fasilitas_id',
    ];

    public function indekos()
    {
        return $this->belongsTo(Indekos::class);
    }
    protected $table = 'kamars';
    public function users()
    {
        return $this->hasMany(User::class, 'kamar_id');
    }

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class);
    }
}
