# Auto Create GitHub Releases

This repository includes a PowerShell script to automatically create GitHub releases.

## Quick Start

### Option 1: Using Environment Variable (Recommended)

1. Get a GitHub Personal Access Token:
   - Go to: https://github.com/settings/tokens
   - Click "Generate new token (classic)"
   - Give it `repo` scope
   - Copy the token

2. Set it as an environment variable:
   ```powershell
   $env:GITHUB_TOKEN = "your_token_here"
   ```

3. Run the script:
   ```powershell
   .\create-release.ps1
   ```

### Option 2: Pass Token as Parameter

```powershell
.\create-release.ps1 -Token "your_token_here"
```

### Option 3: Specify Version Manually

```powershell
.\create-release.ps1 -Version "1.0.2" -Token "your_token_here"
```

## How It Works

1. **Reads version from `style.css`** - Automatically detects the version number
2. **Creates a release tag** - Formats as `v1.0.0`, `v1.0.1`, etc.
3. **Uses latest commit message** - Automatically includes the latest commit message in release notes
4. **Creates the release** - Publishes to GitHub immediately

## Example Workflow

```powershell
# 1. Update version in style.css
# Version: 1.0.2

# 2. Commit and push changes
git add .
git commit -m "New feature: Added live editor"
git push origin main

# 3. Create release automatically
.\create-release.ps1
```

## Requirements

- PowerShell 5.1 or later
- GitHub Personal Access Token with `repo` scope
- Git repository with commits

## Troubleshooting

**Error: GitHub token required**
- Make sure you've set the `GITHUB_TOKEN` environment variable or passed it as a parameter
- Verify the token has `repo` scope

**Error: Could not find version in style.css**
- Make sure `style.css` has a `Version:` line in the header
- Format should be: `Version: 1.0.0`

**Error: Release already exists**
- The tag already exists on GitHub
- Either delete the existing release/tag or use a new version number
