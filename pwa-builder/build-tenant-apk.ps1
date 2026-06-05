#!/usr/bin/env pwsh
# Build tenant-specific Android APK
# Usage: .\pwa-builder\build-tenant-apk.ps1 -TenantSlug "coaching-name" -CoachingName "Coaching Name"

param(
    [Parameter(Mandatory=$true)]
    [string]$TenantSlug,

    [Parameter(Mandatory=$true)]
    [string]$CoachingName,

    [string]$Domain = "btguru.tech",
    [string]$ThemeColor = "#7C3AED",
    [string]$PackageName = ""
)

$ErrorActionPreference = "Stop"

# Generate package name if not provided
if ([string]::IsNullOrEmpty($PackageName)) {
    $PackageName = "tech.btguru.twa.$($TenantSlug -replace '-','_')"
}

$tenantManifestUrl = "https://$TenantSlug.$Domain/manifest.json"
$tenantDomain = "$TenantSlug.$Domain"

Write-Host "Building APK for: $CoachingName" -ForegroundColor Cyan
Write-Host "Package: $PackageName" -ForegroundColor Cyan
Write-Host "Domain: $tenantDomain" -ForegroundColor Cyan

# Check if Node.js is installed
if (!(Get-Command node -ErrorAction SilentlyContinue)) {
    Write-Error "Node.js is required. Install from https://nodejs.org/"
    exit 1
}

# Install Bubblewrap globally if not present
if (!(Get-Command bubblewrap -ErrorAction SilentlyContinue)) {
    Write-Host "Installing Bubblewrap CLI..." -ForegroundColor Yellow
    npm install -g @bubblewrap/cli
}

# Create tenant-specific output directory
$outputDir = "android-apps/$TenantSlug"
if (!(Test-Path $outputDir)) {
    New-Item -ItemType Directory -Path $outputDir -Force | Out-Null
}

# Build the app
Push-Location $outputDir

try {
    # Initialize TWA project (only first time)
    if (!(Test-Path "twa-manifest.json")) {
        Write-Host "Initializing TWA project for $CoachingName..." -ForegroundColor Green
        
        # Create answers file for bubblewrap init
        $initAnswers = @"
$CoachingName
$PackageName
$CoachingName Student App
https://$tenantDomain
$tenantDomain
$tenantDomain
n
"@
        $initAnswers | bubblewrap init --manifest $tenantManifestUrl --directory .
    } else {
        Write-Host "Updating existing TWA project..." -ForegroundColor Yellow
        bubblewrap update
    }

    # Build APK
    Write-Host "Building APK for $CoachingName..." -ForegroundColor Green
    bubblewrap build

    # Rename output files with tenant slug
    $apkSource = "app/build/outputs/apk/release/app-release.apk"
    $apkDest = "app/build/outputs/apk/release/$TenantSlug-student.apk"
    $aabSource = "app/build/outputs/bundle/release/app-release.aab"
    $aabDest = "app/build/outputs/bundle/release/$TenantSlug-student.aab"
    
    if (Test-Path $apkSource) {
        Copy-Item -Path $apkSource -Destination $apkDest -Force
        Write-Host "✅ APK: $apkDest" -ForegroundColor Green
    }
    if (Test-Path $aabSource) {
        Copy-Item -Path $aabSource -Destination $aabDest -Force
        Write-Host "✅ AAB: $aabDest" -ForegroundColor Green
    }
    
    Write-Host "`n✅ Build complete for $CoachingName!" -ForegroundColor Green
}
finally {
    Pop-Location
}
