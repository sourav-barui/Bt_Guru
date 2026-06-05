#!/usr/bin/env pwsh
# Auto-build Android APK from PWA
# Run: .\pwa-builder\build-android.ps1

$ErrorActionPreference = "Stop"

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

# Create output directory
$outputDir = "android-app"
if (!(Test-Path $outputDir)) {
    New-Item -ItemType Directory -Path $outputDir | Out-Null
}

# Build the app
Push-Location $outputDir

try {
    # Initialize TWA project (only first time)
    if (!(Test-Path "build.gradle")) {
        Write-Host "Initializing TWA project..." -ForegroundColor Green
        bubblewrap init --manifest https://btguru.tech/manifest.json --directory .
    }

    # Build APK
    Write-Host "Building APK..." -ForegroundColor Green
    bubblewrap build

    Write-Host "✅ APK built successfully!" -ForegroundColor Green
    Write-Host "Location: $PWD/app/build/outputs/apk/release/" -ForegroundColor Cyan
}
finally {
    Pop-Location
}
