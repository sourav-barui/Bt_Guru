# Requires -RunAsAdministrator

# BT Guru - Windows Hosts File Setup Script
# Run this script as Administrator

$hostsFile = "C:\Windows\System32\drivers\etc\hosts"
$entries = @(
    "127.0.0.1  btguru.test",
    "127.0.0.1  admin.btguru.test",
    "127.0.0.1  futureacademy.btguru.test"
)

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "BT Guru - Hosts File Setup" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Check if running as administrator
$currentPrincipal = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
if (-not $currentPrincipal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    Write-Host "ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host "Right-click PowerShell and select 'Run as Administrator'" -ForegroundColor Yellow
    exit 1
}

# Check if hosts file exists
if (-not (Test-Path $hostsFile)) {
    Write-Host "ERROR: Hosts file not found at $hostsFile" -ForegroundColor Red
    exit 1
}

# Create backup
$backupFile = "$hostsFile.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')"
Write-Host "Creating backup: $backupFile" -ForegroundColor Yellow
Copy-Item $hostsFile $backupFile

# Read current hosts file content
$hostsContent = Get-Content $hostsFile -Raw
$hostsContent = if ($hostsContent) { $hostsContent } else { "" }

# Add entries if they don't exist
$addedEntries = 0
foreach ($entry in $entries) {
    if ($hostsContent -notmatch [regex]::Escape($entry)) {
        Write-Host "Adding: $entry" -ForegroundColor Green
        $hostsContent += "`n$entry"
        $addedEntries++
    } else {
        Write-Host "Already exists: $entry" -ForegroundColor Gray
    }
}

# Save hosts file
try {
    $hostsContent | Out-File $hostsFile -Encoding ASCII -NoNewline
    Write-Host ""
    Write-Host "======================================" -ForegroundColor Cyan
    if ($addedEntries -gt 0) {
        Write-Host "SUCCESS: Added $addedEntries entries to hosts file!" -ForegroundColor Green
    } else {
        Write-Host "All entries already exist in hosts file." -ForegroundColor Green
    }
    Write-Host "======================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Yellow
    Write-Host "1. Add VirtualHost config to Apache" -ForegroundColor White
    Write-Host "2. Restart Apache from XAMPP Control Panel" -ForegroundColor White
    Write-Host "3. Access http://btguru.test" -ForegroundColor White
} catch {
    Write-Host "ERROR: Failed to write hosts file!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit 1
}
