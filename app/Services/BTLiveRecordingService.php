<?php

namespace App\Services;

use App\Models\LiveClass;
use App\Models\BTLiveRecording;
use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class BTLiveRecordingService
{
    protected string $recordingsPath;
    protected ?string $s3Bucket;
    protected ?string $s3Region;
    protected ?string $s3AccessKey;
    protected ?string $s3SecretKey;
    protected bool $s3Enabled;
    protected bool $localStorageEnabled;
    
    public function __construct()
    {
        $this->recordingsPath = config('btlive.recordings.local_path', '/var/lib/jitsi-meet/recordings');
        $this->s3Enabled = config('btlive.recordings.s3_enabled', false);
        $this->localStorageEnabled = config('btlive.recordings.local_enabled', true);
        $this->s3Bucket = config('btlive.recordings.s3_bucket');
        $this->s3Region = config('btlive.recordings.s3_region');
        $this->s3AccessKey = config('btlive.recordings.s3_access_key');
        $this->s3SecretKey = config('btlive.recordings.s3_secret_key');
    }
    
    /**
     * Start recording for a live class
     */
    public function startRecording(LiveClass $liveClass): ?BTLiveRecording
    {
        if (!$this->isRecordingEnabled($liveClass->tenant)) {
            return null;
        }
        
        $recording = BTLiveRecording::create([
            'live_class_id' => $liveClass->id,
            'tenant_id' => $liveClass->tenant_id,
            'recording_id' => 'btlive_' . uniqid(),
            'file_name' => $this->generateFileName($liveClass),
            'status' => 'recording',
            'started_at' => now(),
        ]);
        
        Log::info("Recording started for class {$liveClass->id}", [
            'recording_id' => $recording->recording_id,
        ]);
        
        return $recording;
    }
    
    /**
     * Handle recording finished webhook from Jitsi
     */
    public function handleRecordingFinished(array $data): void
    {
        $recordingId = $data['recording_id'] ?? null;
        $filePath = $data['file_path'] ?? null;
        
        if (!$recordingId) {
            Log::error('Recording finished webhook missing recording_id');
            return;
        }
        
        $recording = BTLiveRecording::where('recording_id', $recordingId)->first();
        
        if (!$recording) {
            Log::error("Recording not found: {$recordingId}");
            return;
        }
        
        $recording->update([
            'status' => 'processing',
            'ended_at' => now(),
            'file_path' => $filePath,
            'file_size' => $filePath && file_exists($filePath) ? filesize($filePath) : null,
        ]);
        
        // Upload to S3 if enabled
        if ($this->s3Enabled && $this->s3Bucket && $filePath && file_exists($filePath)) {
            $this->uploadToS3($recording, $filePath);
        }
        
        // Update final status
        $recording->update([
            'status' => 'completed',
        ]);
        
        Log::info("Recording completed: {$recording->recording_id}");
    }
    
    /**
     * Upload recording to S3
     */
    protected function uploadToS3(BTLiveRecording $recording, string $filePath): void
    {
        try {
            $s3Key = "recordings/{$recording->tenant->slug}/{$recording->file_name}";
            
            // Use Laravel S3 disk
            $disk = Storage::disk('s3');
            
            $stream = fopen($filePath, 'r');
            $disk->put($s3Key, $stream);
            fclose($stream);
            
            // Get S3 URL
            $s3Url = $disk->url($s3Key);
            
            $recording->update([
                's3_url' => $s3Url,
                's3_key' => $s3Key,
            ]);
            
            // Delete local file if local storage is disabled
            if (!$this->localStorageEnabled && file_exists($filePath)) {
                unlink($filePath);
                $recording->update(['file_path' => null]);
            }
            
            Log::info("Recording uploaded to S3: {$s3Key}");
            
        } catch (\Exception $e) {
            Log::error("S3 upload failed: " . $e->getMessage());
            $recording->update(['status' => 'failed']);
        }
    }
    
    /**
     * Get download URL for recording
     */
    public function getDownloadUrl(BTLiveRecording $recording): ?string
    {
        // Priority: S3 URL > Local URL > null
        if ($recording->s3_url) {
            // Generate signed URL for S3
            if ($this->s3Enabled) {
                return Storage::disk('s3')->temporaryUrl(
                    $recording->s3_key,
                    now()->addHours(24)
                );
            }
            return $recording->s3_url;
        }
        
        if ($recording->file_path && file_exists($recording->file_path)) {
            return route('tenant.btlive.recording.download', [
                'tenant' => $recording->tenant->slug,
                'recording' => $recording->id,
            ]);
        }
        
        return null;
    }
    
    /**
     * Check if recording is enabled for tenant
     */
    public function isRecordingEnabled(Tenant $tenant): bool
    {
        return $tenant->btlive_recording_enabled ?? config('btlive.recordings.enabled', false);
    }
    
    /**
     * Get storage usage for tenant
     */
    public function getStorageUsage(Tenant $tenant): array
    {
        $recordings = BTLiveRecording::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->get();
        
        $localSize = $recordings->whereNotNull('file_path')->sum('file_size');
        $s3Size = $recordings->whereNotNull('s3_url')->sum('file_size');
        $totalCount = $recordings->count();
        
        return [
            'local_bytes' => $localSize,
            's3_bytes' => $s3Size,
            'total_bytes' => $localSize + $s3Size,
            'total_formatted' => $this->formatBytes($localSize + $s3Size),
            'count' => $totalCount,
        ];
    }
    
    /**
     * Delete old recordings
     */
    public function cleanupOldRecordings(int $days = 30): int
    {
        $cutoff = now()->subDays($days);
        
        $oldRecordings = BTLiveRecording::where('created_at', '<', $cutoff)
            ->where('status', 'completed')
            ->get();
        
        $deleted = 0;
        
        foreach ($oldRecordings as $recording) {
            // Delete local file
            if ($recording->file_path && file_exists($recording->file_path)) {
                unlink($recording->file_path);
            }
            
            // Delete S3 file
            if ($recording->s3_key && $this->s3Enabled) {
                Storage::disk('s3')->delete($recording->s3_key);
            }
            
            $recording->delete();
            $deleted++;
        }
        
        Log::info("Cleaned up {$deleted} old recordings");
        
        return $deleted;
    }
    
    /**
     * Generate file name for recording
     */
    protected function generateFileName(LiveClass $liveClass): string
    {
        $date = now()->format('Y-m-d_H-i-s');
        $slug = \Illuminate\Support\Str::slug($liveClass->title);
        return "{$date}_{$liveClass->tenant->slug}_{$slug}.mp4";
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
