<?php

namespace App\Http\Controllers;

use App\Models\BTLiveRecording;
use App\Models\LiveClass;
use App\Models\Tenant;
use App\Services\BTLiveRecordingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BTLiveRecordingController extends Controller
{
    protected BTLiveRecordingService $recordingService;
    
    public function __construct(BTLiveRecordingService $recordingService)
    {
        $this->recordingService = $recordingService;
    }
    
    /**
     * List recordings for a live class
     */
    public function index(Tenant $tenant, LiveClass $liveClass)
    {
        // Authorize: must be tenant admin or teacher of this course
        $user = Auth::user();
        if (!$user->isTenantAdmin() && !$user->isTeacher()) {
            abort(403, 'Unauthorized');
        }
        
        // Must belong to same tenant
        if ($user->tenant_id !== $liveClass->tenant_id) {
            abort(403, 'Unauthorized');
        }
        
        $recordings = $liveClass->recordings()->with('approvedBy')->orderBy('created_at', 'desc')->get();
        
        return view('btlive.recordings.index', compact('liveClass', 'recordings'));
    }
    
    /**
     * Approve a recording
     */
    public function approve(Tenant $tenant, BTLiveRecording $recording, Request $request)
    {
        // Authorize: must be tenant admin or teacher
        $user = Auth::user();
        if (!$user->isTenantAdmin() && !$user->isTeacher()) {
            abort(403, 'Unauthorized');
        }
        if ($user->tenant_id !== $recording->liveClass->tenant_id) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);
        
        $recording->approve(Auth::user(), $request->input('notes'));
        
        // Update LiveClass with recording URL
        $recording->liveClass->update([
            'btlive_recording_url' => $this->recordingService->getDownloadUrl($recording),
            'btlive_recording_status' => 'approved',
        ]);
        
        return redirect()->back()->with('success', 'Recording approved successfully.');
    }
    
    /**
     * Reject a recording
     */
    public function reject(Tenant $tenant, BTLiveRecording $recording, Request $request)
    {
        // Authorize: must be tenant admin or teacher
        $user = Auth::user();
        if (!$user->isTenantAdmin() && !$user->isTeacher()) {
            abort(403, 'Unauthorized');
        }
        if ($user->tenant_id !== $recording->liveClass->tenant_id) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);
        
        $recording->reject(Auth::user(), $request->input('notes'));
        
        $recording->liveClass->update([
            'btlive_recording_status' => 'rejected',
        ]);
        
        return redirect()->back()->with('success', 'Recording rejected.');
    }
    
    /**
     * Download recording
     */
    public function download(Tenant $tenant, BTLiveRecording $recording)
    {
        // Authorize: must be tenant admin or teacher
        $user = Auth::user();
        if (!$user->isTenantAdmin() && !$user->isTeacher()) {
            abort(403, 'Unauthorized');
        }
        if ($user->tenant_id !== $recording->liveClass->tenant_id) {
            abort(403, 'Unauthorized');
        }
        
        $url = $this->recordingService->getDownloadUrl($recording);
        
        if (!$url) {
            return redirect()->back()->with('error', 'Recording not available.');
        }
        
        return redirect($url);
    }
    
    /**
     * List all recordings for tenant (admin view)
     */
    public function adminIndex(Tenant $tenant)
    {
        $recordings = BTLiveRecording::where('tenant_id', $tenant->id)
            ->with(['liveClass', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $stats = $this->recordingService->getStorageUsage($tenant);
        
        return view('btlive.recordings.admin_index', compact('recordings', 'stats'));
    }
}
