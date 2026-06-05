#!/usr/bin/env pwsh
# Copy built APK to public downloads folder

$sourcePath = "$PSScriptRoot\android-app\app\build\outputs\apk\release\app-release.apk"
$destPath = "$PSScriptRoot\..\public\downloads\btguru-student.apk"

if (Test-Path $sourcePath) {
    Copy-Item -Path $sourcePath -Destination $destPath -Force
    Write-Host "✅ APK copied to public/downloads/btguru-student.apk" -ForegroundColor Green
    Write-Host "URL: https://btguru.tech/downloads/btguru-student.apk" -ForegroundColor Cyan
} else {
    Write-Error "APK not found at $sourcePath. Run 'npm run build' first."
}
