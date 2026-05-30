<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Symfony\Component\Finder\Finder;

class SystemCleanup extends Command
{
    protected $signature = 'system:cleanup {--logs=7 : Days of logs to keep}';
    protected $description = 'Clear cache, views, and old log files to free disk space';

    public function handle(): void
    {
        $start = now();
        $this->info('Starting system cleanup...');

        // 1. Clear Laravel caches
        $this->info('Clearing application cache...');
        Artisan::call('cache:clear');

        $this->info('Clearing view cache...');
        Artisan::call('view:clear');

        $this->info('Clearing route cache...');
        Artisan::call('route:clear');

        $this->info('Clearing config cache...');
        Artisan::call('config:clear');

        $this->info('Clearing compiled classes...');
        Artisan::call('clear-compiled');

        // 2. Clear old log files
        $this->info('Removing old log files...');
        $this->cleanupLogs();

        // 3. Clear old temporary files (7+ days)
        $this->info('Removing old temporary files...');
        $this->cleanupTempFiles();

        // 4. Clear old uploaded temp files in storage/tmp
        $this->info('Removing old temp uploads...');
        $this->cleanupTempUploads();

        // 5. Clear telescope data if old
        if (class_exists(\Laravel\Telescope\Telescope::class)) {
            $this->info('Pruning telescope entries...');
            Artisan::call('telescope:prune', ['--hours' => 48]);
        }

        $elapsed = $start->diffInSeconds(now());
        $this->newLine();
        $this->info("Cleanup completed in {$elapsed}s. Disk freed.");
    }

    private function cleanupLogs(): void
    {
        $logPath = storage_path('logs');
        if (!is_dir($logPath)) return;

        $keepDays = (int) $this->option('logs');
        $cutoff = Carbon::now()->subDays($keepDays);
        $deleted = 0;

        try {
            $finder = new Finder();
            $finder->files()->in($logPath)->date("before {$cutoff->format('Y-m-d')}");

            foreach ($finder as $file) {
                @unlink($file->getRealPath());
                $deleted++;
            }
        } catch (\Exception $e) {
            Log::warning('Log cleanup error: ' . $e->getMessage());
        }

        $this->info("Deleted {$deleted} old log file(s)");
    }

    private function cleanupTempFiles(): void
    {
        $paths = [
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            sys_get_temp_dir(),
        ];

        $cutoff = Carbon::now()->subDays(1)->getTimestamp();
        $deleted = 0;

        foreach ($paths as $path) {
            if (!is_dir($path)) continue;
            try {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getMTime() < $cutoff) {
                        @unlink($file->getPathname());
                        $deleted++;
                    }
                }
            } catch (\Exception $e) {
                // Silently skip permission errors
            }
        }

        $this->info("Deleted {$deleted} stale temp file(s)");
    }

    private function cleanupTempUploads(): void
    {
        $tmpPath = storage_path('app/tmp');
        if (!is_dir($tmpPath)) return;

        $cutoff = Carbon::now()->subDays(1)->getTimestamp();
        $deleted = 0;

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tmpPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getMTime() < $cutoff) {
                    @unlink($file->getPathname());
                    $deleted++;
                } elseif ($file->isDir()) {
                    @rmdir($file->getPathname());
                }
            }
        } catch (\Exception $e) {
            Log::warning('Temp upload cleanup error: ' . $e->getMessage());
        }

        $this->info("Deleted {$deleted} old temp upload(s)");
    }
}
