# Requires -RunAsAdministrator

# BT Guru - Apache Virtual Host Setup Script
# Run this script as Administrator

$vhostsFile = "C:\xampp\apache\conf\extra\httpd-vhosts.conf"
$projectPath = "C:/xampp/htdocs/Bt_Guru/public"

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "BT Guru - Apache Virtual Host Setup" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Check if running as administrator
$currentPrincipal = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
if (-not $currentPrincipal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    Write-Host "ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host "Right-click PowerShell and select 'Run as Administrator'" -ForegroundColor Yellow
    exit 1
}

# Check if vhosts file exists
if (-not (Test-Path $vhostsFile)) {
    Write-Host "ERROR: Virtual hosts file not found at $vhostsFile" -ForegroundColor Red
    exit 1
}

# Virtual host configuration
$vhostConfig = @"

# BT Guru - Multi-tenant Coaching Centre SaaS
<VirtualHost *:80>
    DocumentRoot "$projectPath"
    ServerName btguru.test
    ServerAlias *.btguru.test
    
    <Directory "$projectPath">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/btguru-error.log"
    CustomLog "logs/btguru-access.log" common
</VirtualHost>
"@

# Read current vhosts content
$vhostsContent = Get-Content $vhostsFile -Raw
$vhostsContent = if ($vhostsContent) { $vhostsContent } else { "" }

# Check if BT Guru config already exists
if ($vhostsContent -match "btguru\.test") {
    Write-Host "BT Guru virtual host config already exists!" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Current config found:" -ForegroundColor Gray
    # Extract and show the existing config
    $matches = [regex]::Matches($vhostsContent, '(?s)# BT Guru.*?</VirtualHost>')
    foreach ($match in $matches) {
        Write-Host $match.Value -ForegroundColor Gray
    }
} else {
    # Create backup
    $backupFile = "$vhostsFile.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')"
    Write-Host "Creating backup: $backupFile" -ForegroundColor Yellow
    Copy-Item $vhostsFile $backupFile
    
    # Add the virtual host config
    Write-Host "Adding BT Guru virtual host configuration..." -ForegroundColor Green
    $vhostsContent += $vhostConfig
    
    # Save vhosts file
    try {
        $vhostsContent | Out-File $vhostsFile -Encoding ASCII -NoNewline
        Write-Host ""
        Write-Host "======================================" -ForegroundColor Cyan
        Write-Host "SUCCESS: Virtual host config added!" -ForegroundColor Green
        Write-Host "======================================" -ForegroundColor Cyan
    } catch {
        Write-Host "ERROR: Failed to write virtual hosts file!" -ForegroundColor Red
        Write-Host $_.Exception.Message -ForegroundColor Red
        exit 1
    }
}

Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Open XAMPP Control Panel" -ForegroundColor White
Write-Host "2. Stop and Start Apache (or click 'Config' > 'Apache (httpd.conf)')" -ForegroundColor White
Write-Host "3. Wait for Apache to restart" -ForegroundColor White
Write-Host "4. Access the platform:" -ForegroundColor White
Write-Host "   - Landing: http://btguru.test" -ForegroundColor Cyan
Write-Host "   - Super Admin: http://admin.btguru.test/login" -ForegroundColor Cyan
Write-Host "   - Demo Tenant: http://futureacademy.btguru.test/login" -ForegroundColor Cyan
Write-Host ""
Write-Host "Demo Credentials:" -ForegroundColor Yellow
Write-Host "  Super Admin: admin@btguru.in / SuperAdmin@123" -ForegroundColor White
Write-Host "  Tenant Admin: admin@futureacademy.com / TenantAdmin@123" -ForegroundColor White
Write-Host "  Teacher: sarah@futureacademy.com / Teacher@123" -ForegroundColor White
Write-Host "  Student: rahul@email.com / Student@123" -ForegroundColor White
