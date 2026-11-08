# SIMAD Build Automation Script for Zero-Node.js Shared Hosting Deployment
# Run this script locally in PowerShell before deploying to cPanel

$ErrorActionPreference = "Stop"

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "  SIMAD: MI Plus Raudlatul Muta'allimin Kalipucung" -ForegroundColor Green
Write-Host "  Automated Production Build for Shared Hosting (Zero-Node)" -ForegroundColor Yellow
Write-Host "==========================================================" -ForegroundColor Cyan

# Step 1: Run Full Test Suite
Write-Host "`n[1/4] Running automated test suite..." -ForegroundColor White
php artisan test
if ($LASTEXITCODE -ne 0) {
    Write-Error "Tests failed! Aborting production build."
    exit 1
}
Write-Host "All tests passed successfully!" -ForegroundColor Green

# Step 2: Build frontend assets locally
Write-Host "`n[2/4] Compiling frontend assets locally (npm run build)..." -ForegroundColor White
if (Test-Path "package.json") {
    npm run build
}
Write-Host "Vite assets built into public/build!" -ForegroundColor Green

# Step 3: Optimize and Cache
Write-Host "`n[3/4] Clearing and optimizing caches..." -ForegroundColor White
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Step 4: Create deployable zip
$zipName = "simad-production-$(Get-Date -Format 'yyyyMMdd_HHmmss').zip"
Write-Host "`n[4/4] Generating deployment archive: $zipName..." -ForegroundColor White

$exclude = @(
    "node_modules",
    ".git",
    ".github",
    ".agents",
    "tests",
    ".env",
    "database/*.sqlite*",
    "*.log",
    "simad-production-*.zip"
)

# Use Compress-Archive
Get-ChildItem -Path . -Exclude $exclude | Compress-Archive -DestinationPath $zipName -Force

Write-Host "`n==========================================================" -ForegroundColor Green
Write-Host "  Build Complete! Archive created: $zipName" -ForegroundColor Green
Write-Host "  Upload this zip to your cPanel File Manager and extract." -ForegroundColor Yellow
Write-Host "==========================================================" -ForegroundColor Green
