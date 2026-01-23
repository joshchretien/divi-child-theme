# PowerShell Script to Create Theme Release Zip
# Run this script before creating a GitHub release

# Configuration
$ThemeName = "Divi-Child"  # Change to your theme folder name
$Version = "1.0.0"  # Update this for each release
$OutputDir = ".\releases"  # Output directory for zip files

# Get the script directory (where the theme files are)
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$ThemePath = Join-Path $ScriptDir $ThemeName

# Create releases directory if it doesn't exist
if (-not (Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir | Out-Null
}

# Check if theme folder exists
if (-not (Test-Path $ThemePath)) {
    Write-Host "Error: Theme folder '$ThemeName' not found!" -ForegroundColor Red
    Write-Host "Current directory: $ScriptDir" -ForegroundColor Yellow
    exit 1
}

# Create zip file name
$ZipFileName = "$ThemeName-v$Version.zip"
$ZipFilePath = Join-Path $OutputDir $ZipFileName

# Remove old zip if it exists
if (Test-Path $ZipFilePath) {
    Remove-Item $ZipFilePath -Force
    Write-Host "Removed old zip file: $ZipFileName" -ForegroundColor Yellow
}

Write-Host "Creating release zip..." -ForegroundColor Cyan
Write-Host "Theme: $ThemePath" -ForegroundColor Gray
Write-Host "Output: $ZipFilePath" -ForegroundColor Gray

# Create zip file
try {
    # Use .NET compression (requires .NET 4.5+)
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    
    # Create zip from theme folder
    [System.IO.Compression.ZipFile]::CreateFromDirectory($ThemePath, $ZipFilePath)
    
    Write-Host "`n✅ Success! Created: $ZipFileName" -ForegroundColor Green
    Write-Host "`nNext steps:" -ForegroundColor Cyan
    Write-Host "1. Go to GitHub → Releases → Create new release" -ForegroundColor White
    Write-Host "2. Tag: v$Version" -ForegroundColor White
    Write-Host "3. Upload this zip file: $ZipFilePath" -ForegroundColor White
    Write-Host "4. Add release notes and publish" -ForegroundColor White
}
catch {
    Write-Host "`n❌ Error creating zip file!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Write-Host "`nAlternative: Use 7-Zip or WinRAR to manually create the zip" -ForegroundColor Yellow
    exit 1
}
