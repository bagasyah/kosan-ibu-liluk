<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;

class UserObserver
{
    public function created(User $user)
    {
        if ($user->role === 'user' && $user->kamar) {
            Payment::create([
                'user_id' => $user->id,
                'tanggal_bayar' => Carbon::now(),
                'batas_pembayaran' => Carbon::now()->addMonth(),
                'status' => 'Belum Dibayar',
                'price' => $user->kamar->harga,
                'jenis' => 'Penyewa',
            ]);
        }
    }
}
