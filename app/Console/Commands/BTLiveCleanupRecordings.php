<?php

namespace App\Console\Commands;

use App\Services\BTLiveRecordingService;
use Illuminate\Console\Command;

class BTLiveCleanupRecordings extends Command
{
    protected $signature = 'btlive:cleanup-recordings {--days=30 : Delete recordings older than X days}';
    
    protected $description = 'Clean up old BTLive recordings';
    
    protected BTLiveRecordingService $recordingService;
    
    public function __construct(BTLiveRecordingService $recordingService)
    {
        parent::__construct();
        $this->recordingService = $recordingService;
    }
    
    public function handle(): int
    {
        $days = $this->option('days');
        
        $this->info("Cleaning up recordings older than {$days} days...");
        
        $deleted = $this->recordingService->cleanupOldRecordings($days);
        
        $this->info("Deleted {$deleted} old recordings.");
        
        return 0;
    }
}
