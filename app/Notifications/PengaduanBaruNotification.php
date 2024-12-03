<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Pengaduan;
use Carbon\Carbon;

class PengaduanBaruNotification extends Notification
{
    use Queueable;

    protected $pengaduan;

    public function __construct(Pengaduan $pengaduan)
    {
        $this->pengaduan = $pengaduan;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $tanggalPelaporan = Carbon::parse($this->pengaduan->tanggal_pelaporan);

        return (new MailMessage)
                    ->subject('Pengaduan Baru Diterima')
                    ->greeting('Halo Admin,')
                    ->line(new \Illuminate\Support\HtmlString('<div style="text-align: center;">'))
                    ->line('Pengaduan baru telah dibuat oleh ')
                    ->line('Nama: ' . $this->pengaduan->user->name)
                    ->line('No Kamar: ' . ($this->pengaduan->user->kamar->no_kamar ?? '-'))
                    ->line('Nama Indekos: ' . ($this->pengaduan->user->kamar->indekos->nama ?? '-'))
                    ->line('Masalah: ' . $this->pengaduan->masalah)
                    ->line('Tanggal Pelaporan: ' . $tanggalPelaporan->format('d-m-Y'))
                    ->line('Klik tombol di bawah untuk melihat pengaduan:')
                    ->line(new \Illuminate\Support\HtmlString(
                        '<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0"><tr><td><a href="' . url('/admin/pengaduan') . '" style="background-color: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Lihat Pengaduan</a></td></tr></table>'
                    ))
                    // ->line('Terima kasih telah menggunakan aplikasi kami!')
                    ->salutation('');
    }

    public function toArray($notifiable)
    {
        return [
            'pengaduan_id' => $this->pengaduan->id,
            'user_name' => $this->pengaduan->user->name,
            'masalah' => $this->pengaduan->masalah,
            'no_kamar' => $this->pengaduan->user->kamar->no_kamar ?? '-',
        ];
    }
}
