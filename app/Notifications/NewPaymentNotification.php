<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewPaymentNotification extends Notification
{
    use Queueable;

    protected $payment;

    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Pembayaran Baru Dibuat')
                    ->view('emails.payment_notification', [
                        'userName' => $notifiable->name,
                        'price' => $this->payment->price,
                        'paymentDate' => $this->payment->tanggal_bayar->format('d-m-Y'),
                        'dueDate' => $this->payment->batas_pembayaran->format('d-m-Y'),
                    ]);
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Pembayaran Baru Dibuat',
            'message' => 'Pembayaran baru telah dibuat dengan batas waktu ' . $this->payment->batas_pembayaran->format('d-m-Y') . '.',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Pembayaran Baru Dibuat',
            'message' => 'Pembayaran baru telah dibuat dengan batas waktu ' . $this->payment->batas_pembayaran->format('d-m-Y') . '.',
        ]);
    }
};