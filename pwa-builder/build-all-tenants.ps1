#!/usr/bin/env pwsh
# Build APKs for all active tenants
# Requires database access - run from Laravel project root

$ErrorActionPreference = "Stop"

# Get all active tenants from database
Write-Host "Fetching active tenants from database..." -ForegroundColor Cyan

$tenants = php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

\$tenants = App\Models\Tenant::where('status', 'active')->get();
foreach (\$tenants as \$tenant) {
    echo json_encode([
        'slug' => \$tenant->slug ?? \$tenant->subdomain,
        'subdomain' => \$tenant->subdomain,
        'name' => \$tenant->coaching_name,
        'domain' => config('app.central_domain')
    ]) . PHP_EOL;
}
" 2>$null

if (!$tenants) {
    Write-Error "Could not fetch tenants. Make sure you're in the Laravel project root with database access."
    exit 1
}

# Build APK for each tenant
foreach ($tenantJson in $tenants -split "`n" | Where-Object { $_ }) {
    try {
        $tenant = $_ | ConvertFrom-Json
        
        Write-Host "`n========================================" -ForegroundColor Yellow
        Write-Host "Building: $($tenant.name)" -ForegroundColor Yellow
        Write-Host "========================================" -ForegroundColor Yellow
        
        & $PSScriptRoot/build-tenant-apk.ps1 `
            -TenantSlug $tenant.subdomain `
            -CoachingName $tenant.name `
            -Domain $tenant.domain
        
        # Copy to public downloads
        $sourceApk = "android-apps/$($tenant.subdomain)/app/build/outputs/apk/release/$($tenant.subdomain)-student.apk"
        $destDir = "public/downloads/$($tenant.subdomain)"
        
        if (!(Test-Path $destDir)) {
            New-Item -ItemType Directory -Path $destDir -Force | Out-Null
        }
        
        if (Test-Path $sourceApk) {
            Copy-Item -Path $sourceApk -Destination "$destDir/student.apk" -Force
            Write-Host "✅ Copied to public/downloads/$($tenant.subdomain)/student.apk" -ForegroundColor Green
        }
    }
    catch {
        Write-Error "Failed to build for $($tenant.name): $_"
    }
}

Write-Host "`n✅ All tenant APKs built!" -ForegroundColor Green
