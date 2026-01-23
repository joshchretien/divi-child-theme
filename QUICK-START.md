# Quick Start Guide - GitHub Setup

Follow these steps to get your theme on GitHub and enable automatic updates.

## Step 1: Create GitHub Repository (5 minutes)

1. Go to https://github.com/new
2. **Repository name**: `divi-child-theme` (or your preferred name)
3. **Description**: "WP Wizards Divi Child Theme"
4. **Visibility**: Choose **Public** (free, easiest) or **Private** (requires token)
5. **DO NOT** check "Initialize with README"
6. Click **Create repository**

## Step 2: Install Git (if needed)

If you don't have Git installed:
- Download: https://git-scm.com/downloads
- Install with default settings
- Restart your computer

## Step 3: Push Theme to GitHub (10 minutes)

Open **PowerShell** or **Command Prompt** in your theme folder:

```powershell
# Navigate to your theme folder (adjust path if needed)
cd "C:\Users\Josh Chretien\Dropbox\WP Wizards\CHILD THEME\Divi-Child\Divi-Child"

# Initialize Git
git init

# Add all files
git add .

# Create first commit
git commit -m "Initial commit - WP Wizards Divi Child Theme"

# Add GitHub remote (REPLACE with your username and repo name)
git remote add origin https://github.com/YOUR-USERNAME/divi-child-theme.git

# Push to GitHub
git branch -M main
git push -u origin main
```

**When prompted for credentials:**
- Username: Your GitHub username
- Password: Use a **Personal Access Token** (not your password)
  - Get token: https://github.com/settings/tokens
  - Click "Generate new token (classic)"
  - Give it `repo` scope
  - Copy and paste as password

## Step 4: Create First Release (5 minutes)

1. Go to your GitHub repository
2. Click **Releases** → **Create a new release**
3. **Tag version**: `v1.0.0` (must match version in style.css)
4. **Release title**: `Version 1.0.0 - Initial Release`
5. **Description**:
   ```markdown
   ## Initial Release
   
   First version of WP Wizards Divi Child Theme
   
   ### Features
   - Custom dashboard widget
   - Featured image caption shortcode
   - WP Wizards admin settings page
   - Automatic update system via GitHub
   ```
6. **Attach files** (optional but recommended):
   - Create a zip of your theme folder
   - Name it: `divi-child.zip`
   - Upload it
7. Click **Publish release**

## Step 5: Configure WordPress Sites

For each client site:

1. **Go to**: WP Wizards → Theme Updates
2. **Enter**:
   - **GitHub Username**: Your GitHub username
   - **Repository Name**: `divi-child-theme` (or your repo name)
   - **GitHub Token**: (Leave empty for public repos)
3. **Click**: Save Settings
4. **Verify**: You should see a success message with your repo link

## Step 6: Test It

1. Click **Clear Update Cache** in WP Wizards settings
2. Go to **Appearance → Themes**
3. You should see your current version
4. To test updates, create a test release with version `v1.0.1`

## ✅ Done!

Your theme is now on GitHub and ready for automatic updates!

## Future Updates Workflow

When you make changes:

```powershell
# 1. Make your changes
# Edit files as needed

# 2. Update version in style.css
# Version: 1.0.1

# 3. Commit and push
git add .
git commit -m "Version 1.0.1 - Description of changes"
git push origin main

# 4. Create GitHub Release (via web)
# - Tag: v1.0.1
# - Add notes
# - Upload zip (optional)
# - Publish

# 5. All sites see update automatically!
```

## Need Help?

- See `GITHUB-INITIAL-SETUP.md` for detailed instructions
- See `GITHUB-UPDATE-SETUP.md` for update workflow details
