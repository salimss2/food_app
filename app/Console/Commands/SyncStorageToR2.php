<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SyncStorageToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:sync-to-r2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all local public files from storage/app/public to the Cloudflare R2/S3 bucket';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sync of local public files to Cloudflare R2...');

        $localPublicPath = storage_path('app/public');

        if (!File::exists($localPublicPath)) {
            $this->error('Local public storage directory does not exist!');
            return 1;
        }

        // Get all files recursively
        $files = File::allFiles($localPublicPath);

        if (empty($files)) {
            $this->warn('No local files found to sync.');
            return 0;
        }

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $successCount = 0;
        $failCount = 0;

        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname();
            
            // Ignore system/hidden files
            if (str_starts_with($relativePath, '.') || $relativePath === '.gitignore' || $relativePath === '.htaccess') {
                $bar->advance();
                continue;
            }

            try {
                // Read local content
                $content = File::get($file->getRealPath());

                // Upload to R2/S3
                Storage::disk('s3')->put($relativePath, $content);
                $successCount++;
            } catch (\Exception $e) {
                $this->error("\nFailed to sync file: {$relativePath}. Error: " . $e->getMessage());
                $failCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Sync completed! Successfully synced: {$successCount} files. Failed: {$failCount} files.");

        return 0;
    }
}
