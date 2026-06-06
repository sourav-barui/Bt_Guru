<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Process;

class ApkBuildController extends Controller
{
    /**
     * Show APK build page with instructions
     */
    public function index()
    {
        $tenant = app('current_tenant');
        
        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        
        // Check if APK exists
        $apkPath = "downloads/{$tenant->subdomain}/student.apk";
        $apkExists = Storage::disk('public')->exists($apkPath);
        $apkUrl = $apkExists ? Storage::disk('public')->url($apkPath) : null;
        
        // Get last build info
        $buildInfo = $this->getBuildInfo($tenant);
        
        return view('tenant.apk_build.index', [
            'tenant' => $tenant,
            'apkExists' => $apkExists,
            'apkUrl' => $apkUrl,
            'buildInfo' => $buildInfo,
        ]);
    }
    
    /**
     * Trigger APK build
     */
    public function build(Request $request)
    {
        $tenant = app('current_tenant');
        
        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }
        
        // Check if build is already in progress
        if ($this->isBuildInProgress($tenant)) {
            return response()->json([
                'status' => 'in_progress',
                'message' => 'Build is already in progress. Please wait.'
            ]);
        }
        
        // Mark build as started
        $this->setBuildStatus($tenant, 'building');
        
        try {
            // Run build command
            $result = $this->runBuildCommand($tenant);
            
            if ($result['success']) {
                $this->setBuildStatus($tenant, 'completed', $result);
                return response()->json([
                    'status' => 'success',
                    'message' => 'APK built successfully!',
                    'download_url' => Storage::disk('public')->url("downloads/{$tenant->subdomain}/student.apk")
                ]);
            } else {
                $this->setBuildStatus($tenant, 'failed', $result);
                return response()->json([
                    'status' => 'error',
                    'message' => $result['error'] ?? 'Build failed. Check logs for details.'
                ], 500);
            }
        } catch (\Exception $e) {
            $this->setBuildStatus($tenant, 'failed', ['error' => $e->getMessage()]);
            Log::error("APK build failed for tenant {$tenant->id}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Build failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get build status
     */
    public function status()
    {
        $tenant = app('current_tenant');
        
        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }
        
        return response()->json([
            'status' => $this->getBuildStatus($tenant),
            'info' => $this->getBuildInfo($tenant)
        ]);
    }
    
    /**
     * Run the actual build command
     */
    private function runBuildCommand(Tenant $tenant)
    {
        $basePath = base_path('pwa-builder');
        $outputDir = "{$basePath}/android-apps/{$tenant->subdomain}";
        $downloadsPath = storage_path('app/public/downloads');
        
        // Create directories
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        
        // Build script path
        $scriptPath = $basePath . '/build-tenant-apk.sh';
        
        // Run build
        $command = sprintf(
            'cd %s && bash %s %s "%s" %s "%s" 2>&1',
            escapeshellarg($basePath),
            escapeshellarg($scriptPath),
            escapeshellarg($tenant->subdomain),
            escapeshellarg($tenant->coaching_name),
            escapeshellarg(config('app.base_domain', 'btguru.tech')),
            escapeshellarg($tenant->theme_color ?? '#7C3AED')
        );
        
        Log::info("Running APK build command: {$command}");
        
        $output = shell_exec($command);
        $returnCode = 0;
        
        // Check if APK was created
        $apkSource = "{$outputDir}/app/build/outputs/apk/release/{$tenant->subdomain}-student.apk";
        $apkSourceAlt = "{$outputDir}/app/build/outputs/apk/release/app-release.apk";
        
        if (file_exists($apkSource)) {
            // Copy to public downloads
            $destDir = "{$downloadsPath}/{$tenant->subdomain}";
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            
            copy($apkSource, "{$destDir}/student.apk");
            
            return [
                'success' => true,
                'output' => $output,
                'apk_path' => "{$destDir}/student.apk"
            ];
        } elseif (file_exists($apkSourceAlt)) {
            // Copy with rename
            $destDir = "{$downloadsPath}/{$tenant->subdomain}";
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            
            copy($apkSourceAlt, "{$destDir}/student.apk");
            
            return [
                'success' => true,
                'output' => $output,
                'apk_path' => "{$destDir}/student.apk"
            ];
        }
        
        return [
            'success' => false,
            'error' => 'APK file not found after build',
            'output' => $output
        ];
    }
    
    /**
     * Get build status from storage
     */
    private function getBuildStatus(Tenant $tenant)
    {
        $statusFile = storage_path("app/apk-builds/{$tenant->id}_status.json");
        
        if (!file_exists($statusFile)) {
            return 'not_built';
        }
        
        $data = json_decode(file_get_contents($statusFile), true);
        return $data['status'] ?? 'unknown';
    }
    
    /**
     * Set build status
     */
    private function setBuildStatus(Tenant $tenant, $status, $data = [])
    {
        $buildDir = storage_path('app/apk-builds');
        if (!is_dir($buildDir)) {
            mkdir($buildDir, 0755, true);
        }
        
        $statusFile = "{$buildDir}/{$tenant->id}_status.json";
        
        $data['status'] = $status;
        $data['updated_at'] = now()->toIso8601String();
        
        file_put_contents($statusFile, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Check if build is in progress
     */
    private function isBuildInProgress(Tenant $tenant)
    {
        return $this->getBuildStatus($tenant) === 'building';
    }
    
    /**
     * Get build info
     */
    private function getBuildInfo(Tenant $tenant)
    {
        $statusFile = storage_path("app/apk-builds/{$tenant->id}_status.json");
        
        if (!file_exists($statusFile)) {
            return null;
        }
        
        return json_decode(file_get_contents($statusFile), true);
    }
}
