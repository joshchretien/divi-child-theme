# WP Wizards - Auto Create GitHub Release
# This script automatically creates a GitHub release based on the version in style.css

param(
    [string]$Version = "",
    [string]$Token = "",
    [string]$Repo = "joshchretien/divi-child-theme"
)

# Get version from style.css if not provided
if ([string]::IsNullOrEmpty($Version)) {
    $styleCss = Get-Content "style.css" -Raw
    if ($styleCss -match 'Version:\s*([0-9.]+)') {
        $Version = $matches[1]
        Write-Host "Found version in style.css: $Version" -ForegroundColor Green
    } else {
        Write-Host "Error: Could not find version in style.css" -ForegroundColor Red
        exit 1
    }
}

# Check for GitHub token
if ([string]::IsNullOrEmpty($Token)) {
    $Token = $env:GITHUB_TOKEN
    if ([string]::IsNullOrEmpty($Token)) {
        Write-Host "Error: GitHub token required. Set GITHUB_TOKEN environment variable or pass -Token parameter" -ForegroundColor Red
        Write-Host "Get a token from: https://github.com/settings/tokens" -ForegroundColor Yellow
        Write-Host "Required scope: repo" -ForegroundColor Yellow
        exit 1
    }
}

# Format version tag
$Tag = "v$Version"
$ReleaseName = "Version $Version"

# Get latest commit message for release notes
$LatestCommit = git log -1 --pretty=format:"%s"
$ReleaseNotes = "## Changes`n`n$LatestCommit`n`nSee commit history for full details."

Write-Host "Creating release: $Tag" -ForegroundColor Cyan
Write-Host "Release name: $ReleaseName" -ForegroundColor Cyan
Write-Host "Repository: $Repo" -ForegroundColor Cyan

# Create release via GitHub API
$Headers = @{
    "Authorization" = "token $Token"
    "Accept" = "application/vnd.github.v3+json"
}

$Body = @{
    tag_name = $Tag
    name = $ReleaseName
    body = $ReleaseNotes
    draft = $false
    prerelease = $false
} | ConvertTo-Json

try {
    $Response = Invoke-RestMethod -Uri "https://api.github.com/repos/$Repo/releases" `
        -Method Post `
        -Headers $Headers `
        -Body $Body `
        -ContentType "application/json"
    
    Write-Host "`n✅ Release created successfully!" -ForegroundColor Green
    Write-Host "Release URL: $($Response.html_url)" -ForegroundColor Cyan
    Write-Host "Tag: $Tag" -ForegroundColor Cyan
} catch {
    Write-Host "`n❌ Error creating release:" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    
    if ($_.Exception.Response) {
        $Reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $ResponseBody = $Reader.ReadToEnd()
        Write-Host "Response: $ResponseBody" -ForegroundColor Yellow
    }
    
    exit 1
}
