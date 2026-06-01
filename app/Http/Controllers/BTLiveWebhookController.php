<?php

namespace App\Http\Controllers;

use App\Services\BTLiveRecordingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BTLiveWebhookController extends Controller
{
    protected BTLiveRecordingService $recordingService;
    
    public function __construct(BTLiveRecordingService $recordingService)
    {
        $this->recordingService = $recordingService;
    }
    
    /**
     * Handle Jitsi recording webhook
     */
    public function handleRecording(Request $request): \Illuminate\Http\JsonResponse
    {
        // Verify webhook secret
        $secret = $request->header('X-BTLive-Webhook-Secret');
        $expectedSecret = config('btlive.webhook_secret');
        
        if ($expectedSecret && $secret !== $expectedSecret) {
            Log::warning('Invalid webhook secret');
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $eventType = $request->input('event_type');
        $data = $request->all();
        
        Log::info("BTLive webhook received: {$eventType}", $data);
        
        switch ($eventType) {
            case 'recording.started':
                $this->handleRecordingStarted($data);
                break;
                
            case 'recording.stopped':
                $this->handleRecordingStopped($data);
                break;
                
            case 'recording.finished':
                $this->recordingService->handleRecordingFinished($data);
                break;
                
            case 'participant.joined':
                $this->handleParticipantJoined($data);
                break;
                
            case 'participant.left':
                $this->handleParticipantLeft($data);
                break;
                
            case 'session.ended':
                $this->handleSessionEnded($data);
                break;
        }
        
        return response()->json(['status' => 'success']);
    }
    
    protected function handleRecordingStarted(array $data): void
    {
        Log::info('Recording started', $data);
    }
    
    protected function handleRecordingStopped(array $data): void
    {
        Log::info('Recording stopped', $data);
    }
    
    protected function handleParticipantJoined(array $data): void
    {
        Log::info('Participant joined', $data);
    }
    
    protected function handleParticipantLeft(array $data): void
    {
        Log::info('Participant left', $data);
    }
    
    protected function handleSessionEnded(array $data): void
    {
        Log::info('Session ended', $data);
    }
}
