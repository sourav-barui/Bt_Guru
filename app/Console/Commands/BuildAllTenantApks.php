<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class BuildAllTenantApks extends Command
{
    protected $signature = 'pwa:build-all-tenants {--tenant= : Build for specific tenant only}';
    protected $description = 'Build Android APKs for all active tenants';

    public function handle()
    {
        $specificTenant = $this->option('tenant');
        
        if ($specificTenant) {
            $tenant = Tenant::where('subdomain', $specificTenant)
                ->orWhere('slug', $specificTenant)
                ->first();
                
            if (!$tenant) {
                $this->error("Tenant not found: $specificTenant");
                return 1;
            }
            
            $tenants = collect([$tenant]);
        } else {
            $tenants = Tenant::where('status', 'active')->get();
        }

        if ($tenants->isEmpty()) {
            $this->warn('No active tenants found.');
            return 0;
        }

        $basePath = base_path('pwa-builder');
        $downloadsPath = public_path('downloads');
        
        foreach ($tenants as $tenant) {
            $this->info("Building APK for: {$tenant->coaching_name}");
            
            $slug = $tenant->subdomain;
            $name = escapeshellarg($tenant->coaching_name);
            $domain = config('app.central_domain');
            
            // Run build script
            $command = "cd {$basePath} && bash build-tenant-apk.sh {$slug} {$name} {$domain} 2>&1";
            $output = shell_exec($command);
            
            if ($output === null) {
                $this->error("Failed to build for {$tenant->coaching_name}");
                Log::error("APK build failed for tenant: {$tenant->id}");
                continue;
            }
            
            $this->line($output);
            
            // Copy to public downloads
            $sourceApk = "{$basePath}/android-apps/{$slug}/app/build/outputs/apk/release/{$slug}-student.apk";
            $destDir = "{$downloadsPath}/{$slug}";
            
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            
            if (file_exists($sourceApk)) {
                copy($sourceApk, "{$destDir}/student.apk");
                $this->info("✅ Copied to public/downloads/{$slug}/student.apk");
            } else {
                $this->warn("APK not found at: {$sourceApk}");
            }
        }
        
        $this->info('All tenant APK builds complete!');
        return 0;
    }
}
