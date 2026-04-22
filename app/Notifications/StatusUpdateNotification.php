<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StatusUpdateNotification extends Notification
{
    use Queueable;

    protected $judul;
    protected $pesan;
    protected $url;

    public function __construct(string $judul, string $pesan, string $url = '#')
    {
        $this->judul = $judul;
        $this->pesan = $pesan;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        // Admin notifications only for database to avoid spamming Mailtrap rate limits
        if ($this->judul === 'Pendaftaran Akun Baru' || $notifiable->role === 'admin') {
            return ['database'];
        }

        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        if (str_contains($this->judul, 'Disetujui') || str_contains($this->judul, 'Diaktifkan')) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Akun Anda Telah Disetujui ✅')
                ->greeting('Halo,')
                ->line('Selamat! Akun Anda telah berhasil diverifikasi oleh admin.')
                ->line('Sekarang Anda sudah dapat login dan menggunakan sistem. Gunakan akun berikut :')
                ->line('Username : ' . $notifiable->email)
                ->line('Password : (Gunakan kata sandi yang Anda buat saat pendaftaran)')
                ->action('Login ke Sistem', url('/login'))
                ->line('Terima kasih.');
        }

        if (str_contains($this->judul, 'Pendaftaran Berhasil')) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Pendaftaran Berhasil - Menunggu Verifikasi')
                ->greeting('Halo,')
                ->line('Terima kasih telah mendaftar.')
                ->line('Akun Anda saat ini sedang dalam proses verifikasi oleh admin.')
                ->line('Anda belum dapat login sampai akun Anda disetujui.')
                ->line('Selanjutnya kami akan mengirimkan email pemberitahuan setelah proses verifikasi selesai.')
                ->line('Terima kasih atas kesabaran Anda.');
        }

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($this->judul)
            ->line($this->pesan)
            ->action('Lihat Detail', $this->url)
            ->line('Terima kasih.');
    }

    public function toArray($notifiable)
    {
        return [
            'judul' => $this->judul,
            'pesan' => $this->pesan,
            'url' => $this->url,
        ];
    }
}
