# Đóng gói theme để upload cPanel File Manager (không gồm .git, font thừa, archive).
# Usage: .\scripts\package-theme.ps1

$ErrorActionPreference = 'Stop'
$ThemeRoot = Split-Path -Parent $PSScriptRoot
$DistDir   = Join-Path $ThemeRoot 'dist'
$Stamp     = Get-Date -Format 'yyyyMMdd-HHmm'
$ZipName   = "annam-theme-generatepress_child-$Stamp.zip"
$ZipPath   = Join-Path $DistDir $ZipName

$ExcludeDirs = @(
    '.git'
    'dist'
    'node_modules'
    'vendor'
    '.idea'
    '.vscode'
    'assets\font\Noto_Sans'
)

$ExcludeFiles = @(
    '*.zip'
    '*.bak'
    '*.log'
    '.env'
    'Thumbs.db'
    'desktop.ini'
)

if (-not (Test-Path $DistDir)) {
    New-Item -ItemType Directory -Path $DistDir | Out-Null
}

$TempDir = Join-Path $env:TEMP "annam-theme-pack-$Stamp"
if (Test-Path $TempDir) {
    Remove-Item -Recurse -Force $TempDir
}
New-Item -ItemType Directory -Path $TempDir | Out-Null

Write-Host "Copying theme to temp folder..."
Get-ChildItem -Path $ThemeRoot -Force | ForEach-Object {
    if ($ExcludeDirs -contains $_.Name) { return }
    if ($_.Name -eq 'dist') { return }
    Copy-Item -Path $_.FullName -Destination (Join-Path $TempDir $_.Name) -Recurse -Force
}

foreach ($pattern in $ExcludeFiles) {
    Get-ChildItem -Path $TempDir -Recurse -Force -Filter $pattern -ErrorAction SilentlyContinue |
        Remove-Item -Force -Recurse -ErrorAction SilentlyContinue
}

$NotoPath = Join-Path $TempDir 'assets\font\Noto_Sans'
if (Test-Path $NotoPath) {
    Remove-Item -Recurse -Force $NotoPath
}

if (Test-Path $ZipPath) {
    Remove-Item -Force $ZipPath
}

Write-Host "Creating ZIP: $ZipPath"
Compress-Archive -Path (Join-Path $TempDir '*') -DestinationPath $ZipPath -CompressionLevel Optimal

Remove-Item -Recurse -Force $TempDir

$SizeMb = [math]::Round((Get-Item $ZipPath).Length / 1MB, 2)
Write-Host "Done. Size: ${SizeMb} MB"
Write-Host "Upload to: wp-content/themes/ then Extract"
