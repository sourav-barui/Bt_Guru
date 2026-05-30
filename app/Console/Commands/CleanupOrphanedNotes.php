<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\Finder;

class CleanupOrphanedNotes extends Command
{
    protected $signature = 'notes:cleanup {--dry-run : Show what would be deleted without deleting}';
    protected $description = 'Remove orphaned PDF/note files not linked in database';

    private string $notesDir;

    public function handle(): void
    {
        $this->notesDir = storage_path('app/public/curriculum_notes');

        if (!is_dir($this->notesDir)) {
            $this->warn('Notes directory does not exist: ' . $this->notesDir);
            return;
        }

        $dryRun = $this->option('dry-run');

        $this->info('Scanning for orphaned note files...');
        $this->newLine();

        // Get all file_path values from database
        $dbPaths = DB::table('curriculum_notes')
            ->pluck('file_path')
            ->map(fn($p) => ltrim($p, '/'))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $dbPathSet = array_flip($dbPaths);

        // Scan all files in the notes directory
        $finder = new Finder();
        $finder->files()->in($this->notesDir);

        $orphaned = [];
        $freedBytes = 0;

        foreach ($finder as $file) {
            // Get relative path from storage/app/public
            $absolutePath = $file->getRealPath();
            $relativePath = 'curriculum_notes/' . str_replace('\\', '/', $file->getRelativePathname());

            if (!isset($dbPathSet[$relativePath])) {
                $orphaned[] = [
                    'relative' => $relativePath,
                    'absolute' => $absolutePath,
                    'size' => $file->getSize(),
                ];
                $freedBytes += $file->getSize();
            }
        }

        if (empty($orphaned)) {
            $this->info('No orphaned files found. All note files are linked in the database.');
            return;
        }

        $this->warn('Found ' . count($orphaned) . ' orphaned file(s)');
        $this->newLine();

        $tableRows = [];
        foreach ($orphaned as $file) {
            $tableRows[] = [
                $file['relative'],
                $this->formatBytes($file['size']),
            ];
        }
        $this->table(['File Path', 'Size'], $tableRows);

        $this->newLine();
        $this->info('Total disk space to free: ' . $this->formatBytes($freedBytes));

        if ($dryRun) {
            $this->newLine();
            $this->comment('Dry run mode — no files were deleted.');
            return;
        }

        // Delete orphaned files
        $deleted = 0;
        foreach ($orphaned as $file) {
            if (@unlink($file['absolute'])) {
                $deleted++;
            } else {
                Log::warning('Failed to delete orphaned note file: ' . $file['absolute']);
            }
        }

        // Clean up empty subdirectories
        $this->removeEmptyDirs($this->notesDir);

        $this->newLine();
        $this->info("Deleted {$deleted}/" . count($orphaned) . " orphaned file(s).");
        $this->info('Freed disk space: ' . $this->formatBytes($freedBytes));
        Log::info('notes:cleanup deleted ' . $deleted . ' orphaned files, freed ' . $this->formatBytes($freedBytes));
    }

    private function removeEmptyDirs(string $path): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $dir) {
            if ($dir->isDir() && is_dir($dir->getPathname())) {
                @rmdir($dir->getPathname());
            }
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        $size = $bytes;
        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }
        return round($size, 2) . ' ' . $units[$unitIndex];
    }
}
