<?php

namespace App\Console\Commands;

use App\Models\BarangTemuan;
use App\Models\RiwayatPelanggaran;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoViolationCheck extends Command
{
    protected $signature = 'violation:check-deadlines';
    protected $description = 'Auto-create violations for expired handover deadlines';

    public function handle()
    {
        $expired = BarangTemuan::where('status_penyerahan', 'menunggu_diserahkan')
            ->whereNotNull('deadline_penyerahan')
            ->where('deadline_penyerahan', '<', Carbon::now())
            ->where('status_perpanjangan', '!=', 'menunggu')
            ->get();

        $count = 0;
        foreach ($expired as $item) {
            // Check if violation already exists for this item
            $exists = RiwayatPelanggaran::where('id_user', $item->id_user_pelapor)
                ->where('deskripsi', 'like', "%#{$item->id_barang}%")
                ->where('jenis_pelanggaran', 'melewati_deadline')
                ->exists();

            if (!$exists) {
                RiwayatPelanggaran::create([
                    'id_user'            => $item->id_user_pelapor,
                    'jenis_pelanggaran'  => 'melewati_deadline',
                    'deskripsi'          => "Melewati deadline penyerahan barang \"{$item->nama_barang}\" (ID #{$item->id_barang})",
                    'poin'               => 10,
                    'tingkat'            => 'sedang',
                    'tanggal'            => Carbon::now(),
                ]);

                // Deduct points from user
                $item->pelapor->decrement('poin', 10);

                $this->info("Violation created for user #{$item->id_user_pelapor} - Item: {$item->nama_barang}");
                $count++;
            }
        }

        $this->info("Done. {$count} new violations created.");
        return Command::SUCCESS;
    }
}
