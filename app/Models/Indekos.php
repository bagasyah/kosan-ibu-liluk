<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indekos extends Model
{
    use HasFactory;
    protected $table = 'indekos';

    protected $fillable = ['nama', 'alamat'];
    public function kamars()
    {
        return $this->hasMany(Kamar::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, Kamar::class);
    }
}
