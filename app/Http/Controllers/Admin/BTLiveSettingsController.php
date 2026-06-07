<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class BTLiveSettingsController extends Controller
{
    /**
     * BTLive Settings Page
     */
    public function index()
    {
        $settings = [
            // Jitsi Configuration
            'jitsi_domain' => env('BTLIVE_JITSI_DOMAIN', 'meet.btguru.tech'),
            'jitsi_app_id' => config('btlive.jitsi_app_id'),
            'jitsi_app_secret' => config('btlive.jitsi_app_secret') ? '********' : null,
            
            // Recording Settings
            'recording_enabled' => config('btlive.recordings.enabled'),
            'auto_start_recording' => config('btlive.auto_start_recording'),
            'recording_format' => config('btlive.recording_format'),
            'local_storage_enabled' => config('btlive.recordings.local_enabled'),
            'local_storage_path' => config('btlive.recordings.local_path'),
            
            // S3 Configuration
            's3_enabled' => config('btlive.recordings.s3_enabled'),
            's3_disk' => config('btlive.recordings.s3_disk'),
            's3_bucket' => config('btlive.recordings.s3_bucket'),
            's3_region' => config('btlive.recordings.s3_region'),
            's3_access_key' => config('btlive.recordings.s3_access_key'),
            's3_secret_key' => config('btlive.recordings.s3_secret_key') ? '********' : null,
            's3_endpoint' => config('btlive.recordings.s3_endpoint'),
            
            // Recording Limits
            'max_recording_duration' => config('btlive.recordings.max_duration'),
            'auto_cleanup_days' => config('btlive.recordings.auto_cleanup_days'),
            'max_storage_per_tenant' => config('btlive.recordings.max_storage_per_tenant_gb'),
            
            // Video Quality
            'teacher_resolution' => config('btlive.teacher_video_resolution'),
            'teacher_fps' => config('btlive.teacher_video_fps'),
            'enable_simulcast' => config('btlive.enable_simulcast'),
            
            // Security
            'require_jwt' => config('btlive.require_jwt'),
            'enable_lobby' => config('btlive.enable_lobby'),
            'webhook_secret' => config('btlive.webhook_secret') ? '********' : null,
            
            // Limits
            'max_participants' => config('btlive.max_participants'),
            'max_teachers_live' => config('btlive.max_teachers_live'),
        ];
        
        // Get storage stats
        $storageStats = $this->getStorageStats();
        
        return view('admin.btlive.settings', compact('settings', 'storageStats'));
    }
    
    /**
     * Update BTLive Settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'jitsi_domain' => 'required|url',
            'jitsi_app_id' => 'nullable|string|max:255',
            'jitsi_app_secret' => 'nullable|string|max:255',
            
            'recording_enabled' => 'boolean',
            'auto_start_recording' => 'boolean',
            'local_storage_enabled' => 'boolean',
            'local_storage_path' => 'required|string|max:500',
            
            's3_enabled' => 'boolean',
            's3_disk' => 'nullable|string|max:50',
            's3_bucket' => 'nullable|string|max:255',
            's3_region' => 'nullable|string|max:50',
            's3_access_key' => 'nullable|string|max:255',
            's3_secret_key' => 'nullable|string|max:255',
            's3_endpoint' => 'nullable|url|max:500',
            
            'max_recording_duration' => 'required|integer|min:10|max:480',
            'auto_cleanup_days' => 'required|integer|min:1|max:365',
            'max_storage_per_tenant' => 'required|integer|min:1|max:500',
            
            'teacher_resolution' => 'required|integer|in:180,360,480,720,1080',
            'teacher_fps' => 'required|integer|in:15,24,30',
            'enable_simulcast' => 'boolean',
            
            'require_jwt' => 'boolean',
            'enable_lobby' => 'boolean',
            'webhook_secret' => 'nullable|string|max:255',
            
            'max_participants' => 'required|integer|min:10|max:10000',
            'max_teachers_live' => 'required|integer|min:1|max:100',
        ]);
        
        // Update .env file
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        
        $envUpdates = [
            'BTLIVE_JITSI_DOMAIN' => $validated['jitsi_domain'],
            'BTLIVE_JITSI_APP_ID' => $validated['jitsi_app_id'] ?? '',
            'BTLIVE_JITSI_APP_SECRET' => $validated['jitsi_app_secret'] ?? '',
            'BTLIVE_RECORDING_ENABLED' => $validated['recording_enabled'] ? 'true' : 'false',
            'BTLIVE_AUTO_RECORD' => $validated['auto_start_recording'] ? 'true' : 'false',
            'BTLIVE_LOCAL_STORAGE' => $validated['local_storage_enabled'] ? 'true' : 'false',
            'BTLIVE_RECORDINGS_PATH' => $validated['local_storage_path'],
            'BTLIVE_S3_ENABLED' => $validated['s3_enabled'] ? 'true' : 'false',
            'BTLIVE_S3_DISK' => $validated['s3_disk'] ?? 's3',
            'BTLIVE_S3_BUCKET' => $validated['s3_bucket'] ?? '',
            'BTLIVE_S3_REGION' => $validated['s3_region'] ?? 'us-east-1',
            'BTLIVE_S3_ACCESS_KEY' => $validated['s3_access_key'] ?? '',
            'BTLIVE_S3_SECRET_KEY' => $validated['s3_secret_key'] ?? '',
            'BTLIVE_S3_ENDPOINT' => $validated['s3_endpoint'] ?? '',
            'BTLIVE_MAX_RECORDING_DURATION' => $validated['max_recording_duration'],
            'BTLIVE_AUTO_CLEANUP_DAYS' => $validated['auto_cleanup_days'],
            'BTLIVE_MAX_STORAGE_GB' => $validated['max_storage_per_tenant'],
            'BTLIVE_TEACHER_RESOLUTION' => $validated['teacher_resolution'],
            'BTLIVE_TEACHER_FPS' => $validated['teacher_fps'],
            'BTLIVE_SIMULCAST' => $validated['enable_simulcast'] ? 'true' : 'false',
            'BTLIVE_REQUIRE_JWT' => $validated['require_jwt'] ? 'true' : 'false',
            'BTLIVE_ENABLE_LOBBY' => $validated['enable_lobby'] ? 'true' : 'false',
            'BTLIVE_WEBHOOK_SECRET' => $validated['webhook_secret'] ?? '',
            'BTLIVE_MAX_PARTICIPANTS' => $validated['max_participants'],
            'BTLIVE_MAX_TEACHERS' => $validated['max_teachers_live'],
        ];
        
        foreach ($envUpdates as $key => $value) {
            $pattern = '/^' . preg_quote($key) . '=.*/m';
            $line = $key . '=' . $value;
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $line, $envContent);
            } else {
                $envContent .= "\n" . $line;
            }
        }
        
        file_put_contents($envPath, $envContent);
        
        // Clear config cache
        Artisan::call('config:clear');
        
        return redirect()->route('admin.btlive.settings')
            ->with('success', 'BTLive settings updated successfully. Please redeploy the application for changes to take full effect.');
    }
    
    /**
     * Get storage statistics
     */
    protected function getStorageStats(): array
    {
        $localPath = config('btlive.recordings.local_path');
        $totalSize = 0;
        $fileCount = 0;
        
        if (is_dir($localPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($localPath, \RecursiveDirectoryIterator::SKIP_DOTS)
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $totalSize += $file->getSize();
                    $fileCount++;
                }
            }
        }
        
        return [
            'total_size_formatted' => $this->formatBytes($totalSize),
            'total_size_bytes' => $totalSize,
            'file_count' => $fileCount,
            'storage_path' => $localPath,
            'storage_exists' => is_dir($localPath),
            'writable' => is_dir($localPath) && is_writable($localPath),
        ];
    }
    
    /**
     * Run cleanup command
     */
    public function cleanup(Request $request)
    {
        $days = $request->input('days', 30);
        
        Artisan::call('btlive:cleanup-recordings', ['--days' => $days]);
        
        $output = Artisan::output();
        
        return redirect()->route('admin.btlive.settings')
            ->with('success', 'Cleanup completed: ' . $output);
    }
    
    /**
     * Test S3 connection
     */
    public function testS3()
    {
        try {
            if (!config('btlive.recordings.s3_enabled')) {
                return response()->json(['error' => 'S3 is not enabled'], 400);
            }
            
            $disk = Storage::disk(config('btlive.recordings.s3_disk'));
            
            // Try to list root directory
            $files = $disk->files('/');
            
            return response()->json([
                'success' => true,
                'message' => 'S3 connection successful',
                'files_found' => count($files),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'S3 connection failed: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Format bytes to human readable
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
