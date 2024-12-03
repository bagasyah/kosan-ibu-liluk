<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\PaymentReminder;
use Illuminate\Support\Facades\Mail;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentReminderNotification;

class SendPaymentReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminder emails to users';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $payments = Payment::whereDate('batas_pembayaran', now()->addDay(1)->toDateString())
                           ->where('status', '!=', 'Selesai')
                           ->get();

        foreach ($payments as $payment) {
            $user = $payment->user;

            // Kirim peringatan hanya jika status pengguna adalah 'active'
            if ($user->status === 'active') {
                $data = [
                    'title' => 'Peringatan Pembayaran',
                    'message' => 'Batas pembayaran Anda akan jatuh tempo besok.'
                ];

                // Kirim email
                Mail::to($user->email)->send(new PaymentReminder($data));

                // Kirim notifikasi
                $user->notify(new PaymentReminderNotification($data));
            }
        }

        $this->info('Payment reminders sent successfully.');
    }
}
