# Requires -RunAsAdministrator

# BT Guru - Complete Windows Setup Script
# Run this script as Administrator to configure hosts file and Apache

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "BT Guru - Complete Setup" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Check if running as administrator
$currentPrincipal = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
if (-not $currentPrincipal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    Write-Host "ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host "Right-click PowerShell and select 'Run as Administrator'" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Or run these commands manually:" -ForegroundColor Gray
    Write-Host "  cd c:\xampp\htdocs\Bt_Guru" -ForegroundColor White
    Write-Host "  powershell -ExecutionPolicy Bypass -File setup-all.ps1" -ForegroundColor White
    exit 1
}

# Run hosts setup
Write-Host "Step 1: Configuring Windows hosts file..." -ForegroundColor Yellow
& "$PSScriptRoot\setup-hosts.ps1"

Write-Host ""
Write-Host "Step 2: Configuring Apache virtual hosts..." -ForegroundColor Yellow
& "$PSScriptRoot\setup-apache.ps1"

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "SETUP COMPLETE!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "IMPORTANT: Restart Apache from XAMPP Control Panel!" -ForegroundColor Red -BackgroundColor Black
Write-Host ""
Write-Host "Access URLs:" -ForegroundColor Yellow
Write-Host "  http://btguru.test - Landing Page" -ForegroundColor White
Write-Host "  http://admin.btguru.test/login - Super Admin" -ForegroundColor White
Write-Host "  http://futureacademy.btguru.test/login - Demo Tenant" -ForegroundColor White
Write-Host ""
Write-Host "Demo Credentials:" -ForegroundColor Yellow
Write-Host "  Super Admin:  admin@btguru.in / SuperAdmin@123" -ForegroundColor Cyan
Write-Host "  Tenant Admin: admin@futureacademy.com / TenantAdmin@123" -ForegroundColor Cyan
Write-Host "  Teacher:      sarah@futureacademy.com / Teacher@123" -ForegroundColor Cyan
Write-Host "  Student:      rahul@email.com / Student@123" -ForegroundColor Cyan
