<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use App\Notifications\NewPaymentNotification;

class CreatePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create payments automatically for users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil semua pengguna dengan peran 'user' beserta kamar yang ditempati
        $users = User::where('role', 'user')->where('status', 'active')->with('kamar')->get();

        foreach ($users as $user) {
            if ($user->kamar) {
                // Dapatkan pembayaran terakhir
                $lastPayment = Payment::where('user_id', $user->id)
                    ->orderBy('tanggal_bayar', 'desc')
                    ->first();

                // Hitung tanggal pembayaran berikutnya
                $nextPaymentDate = $lastPayment ? $lastPayment->tanggal_bayar->addMonth() : Carbon::now();

                // Cek apakah sudah waktunya membuat pembayaran baru
                if (Carbon::now()->greaterThanOrEqualTo($nextPaymentDate)) {
                    $payment = Payment::create([
                        'user_id' => $user->id,
                        'tanggal_bayar' => $nextPaymentDate,
                        'batas_pembayaran' => $nextPaymentDate->copy()->addDay(),
                        'status' => 'Belum Dibayar',
                        'price' => $user->kamar->harga,
                        'jenis' => 'Penyewa',
                    ]);

                    // Kirim notifikasi ke pengguna
                    $user->notify(new NewPaymentNotification($payment));
                }
            }
        }

        $this->info('Payments created successfully.');
    }
}
