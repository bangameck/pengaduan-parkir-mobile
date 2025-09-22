<?php
namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanTemporaryFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-temporary-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up temporary files older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mulai membersihkan file temporary...');

        $directories  = Storage::disk('public')->directories('tmp');
        $cutoff       = Carbon::now()->subHours(24)->getTimestamp();
        $deletedCount = 0;

        foreach ($directories as $directory) {
            // Cek waktu modifikasi terakhir dari folder
            if (Storage::disk('public')->lastModified($directory) < $cutoff) {
                Storage::disk('public')->deleteDirectory($directory);
                $deletedCount++;
            }
        }

        $this->info("Pembersihan selesai. {$deletedCount} folder temporary usang telah dihapus.");
        return 0;
    }
}
