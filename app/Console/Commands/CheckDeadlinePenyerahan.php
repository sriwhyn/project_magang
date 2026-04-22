<?php

namespace App\Console\Commands;

use App\Models\BarangTemuan;
use App\Models\User;
use App\Notifications\StatusUpdateNotification;
use Illuminate\Console\Command;

class CheckDeadlinePenyerahan extends Command
{
    protected $signature = 'deadline:check';
    protected $description = 'Kirim notifikasi ke user yang deadline penyerahannya sudah lewat atau hampir lewat';

    public function handle()
    {
        $expiredItems = BarangTemuan::where('status_penyerahan', 'menunggu_diserahkan')
            ->where('deadline_penyerahan', '<', now())
            ->where('notifikasi_deadline_terkirim', false)
            ->get();

        foreach ($expiredItems as $barang) {
            $user = User::find($barang->id_user_pelapor);
            if ($user) {
                $user->notify(new StatusUpdateNotification(
                    'Deadline Terlewat!',
                    'Deadline penyerahan barang "' . $barang->nama_barang . '" telah lewat. Segera serahkan ke petugas.',
                    route('mahasiswa.temuan.show', $barang->id_barang)
                ));
            }
            $barang->update(['notifikasi_deadline_terkirim' => true]);
        }

        $soonItems = BarangTemuan::where('status_penyerahan', 'menunggu_diserahkan')
            ->whereBetween('deadline_penyerahan', [now(), now()->addHours(3)])
            ->where('notifikasi_reminder_terkirim', false)
            ->get();

        foreach ($soonItems as $barang) {
            $user = User::find($barang->id_user_pelapor);
            if ($user) {
                $user->notify(new StatusUpdateNotification(
                    'Deadline Hampir Habis',
                    'Sisa waktu kurang dari 3 jam untuk menyerahkan "' . $barang->nama_barang . '". Segera ke petugas!',
                    route('mahasiswa.temuan.show', $barang->id_barang)
                ));
            }
            $barang->update(['notifikasi_reminder_terkirim' => true]);
        }

        $this->info("Notifikasi terkirim: {$expiredItems->count()} expired, {$soonItems->count()} reminder.");
    }
}
