# Getting Your Theme on GitHub - Step by Step

This guide walks you through uploading your child theme to GitHub and setting it up for automatic updates.

## Step 1: Create GitHub Repository

### Option A: Create New Repository on GitHub.com

1. Go to https://github.com/new
2. **Repository name**: `divi-child-theme` (or your preferred name)
3. **Description**: "WP Wizards Divi Child Theme"
4. **Visibility**: 
   - **Public** (recommended for easiest setup, free)
   - **Private** (requires GitHub token, but more secure)
5. **DO NOT** check "Initialize with README" (we'll add files manually)
6. Click **Create repository**

### Option B: Use GitHub CLI (if you have it installed)

```bash
gh repo create divi-child-theme --public --description "WP Wizards Divi Child Theme"
```

## Step 2: Prepare Your Theme Files

### Files to Include

Make sure your theme folder contains:
- ✅ `style.css` (with correct version number)
- ✅ `functions.php`
- ✅ `class-theme-updater.php`
- ✅ `class-tgm-plugin-activation.php`
- ✅ `header.php` (if you have one)
- ✅ `screenshot.png`
- ✅ `bundled-plugins/` folder (if needed)

### Files to Exclude (Create .gitignore)

Create a `.gitignore` file in your theme root:

```gitignore
# WordPress
.DS_Store
Thumbs.db

# IDE
.idea/
.vscode/
*.sublime-project
*.sublime-workspace

# Temporary files
*.log
*.tmp
*.bak
*.swp
*~

# OS files
.DS_Store
.DS_Store?
._*
.Spotlight-V100
.Trashes
ehthumbs.db
Thumbs.db

# Don't ignore bundled plugins (if you want them in repo)
# bundled-plugins/
```

## Step 3: Initialize Git and Push to GitHub

### If you DON'T have Git installed yet:

1. **Install Git**: https://git-scm.com/downloads
2. **Configure Git** (one-time setup):
   ```bash
   git config --global user.name "Your Name"
   git config --global user.email "your.email@example.com"
   ```

### Initialize Repository and Push

Open terminal/command prompt in your theme folder:

```bash
# Navigate to your theme folder
cd "C:\Users\Josh Chretien\Dropbox\WP Wizards\CHILD THEME\Divi-Child\Divi-Child"

# Initialize Git repository
git init

# Add all files
git add .

# Create initial commit
git commit -m "Initial commit - WP Wizards Divi Child Theme v1.0.0"

# Add GitHub remote (replace with YOUR username and repo name)
git remote add origin https://github.com/YOUR-USERNAME/divi-child-theme.git

# Push to GitHub
git branch -M main
git push -u origin main
```

**Note**: Replace `YOUR-USERNAME` and `divi-child-theme` with your actual GitHub username and repository name.

### If GitHub asks for authentication:

You'll need to authenticate. Options:

**Option 1: Personal Access Token (Recommended)**
1. Go to https://github.com/settings/tokens
2. Generate new token (classic)
3. Give it `repo` scope
4. Copy the token
5. When Git asks for password, paste the token instead

**Option 2: GitHub Desktop**
- Download: https://desktop.github.com/
- Use the GUI to push files

**Option 3: SSH Key**
- Set up SSH key: https://docs.github.com/en/authentication/connecting-to-github-with-ssh
- Use SSH URL: `git@github.com:YOUR-USERNAME/divi-child-theme.git`

## Step 4: Create Your First Release

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
   - Name it: `divi-child.zip` or `[your-theme-slug].zip`
   - Upload it as a release asset
7. Click **Publish release**

## Step 5: Configure WordPress Sites

### For Each Client Site:

1. **Install the theme** (if not already installed)
2. **Go to**: WP Wizards → Theme Updates
3. **Enter GitHub details**:
   - **GitHub Username**: Your GitHub username
   - **Repository Name**: `divi-child-theme` (or your repo name)
   - **GitHub Token**: (Optional - only needed for private repos)
4. **Click Save Settings**
5. **Verify**: You should see a success message with your repo link

### Test the Update System:

1. **Clear cache**: Click "Clear Update Cache"
2. **Check for updates**: Go to Appearance → Themes
3. If everything is set up correctly, you should see the current version
4. To test, create a test release with version `v1.0.1` and see if it appears

## Step 6: Future Updates Workflow

### When You Make Changes:

```bash
# 1. Make your changes to theme files
# Edit functions.php, style.css, etc.

# 2. Update version in style.css
# Version: 1.0.1

# 3. Commit changes
git add .
git commit -m "Version 1.0.1 - Added new feature"
git push origin main

# 4. Create GitHub Release (via web interface)
# - Tag: v1.0.1
# - Add release notes
# - Attach theme zip file
# - Publish

# 5. All client sites automatically see the update!
```

## Repository Structure Example

Your GitHub repo should look like this:

```
divi-child-theme/
├── .gitignore
├── README.md (optional)
├── style.css
├── functions.php
├── class-theme-updater.php
├── class-tgm-plugin-activation.php
├── header.php
├── screenshot.png
└── bundled-plugins/
    ├── plugin1.zip
    ├── plugin2.zip
    └── ...
```

## Public vs Private Repository

### Public Repository (Recommended for Most Cases)

**Pros:**
- ✅ Free
- ✅ No authentication needed
- ✅ Easier setup
- ✅ Can share with clients easily

**Cons:**
- ❌ Code is visible to everyone
- ❌ Lower API rate limit (60/hour) - usually fine

**Best for:** Most agencies, standard child themes

### Private Repository

**Pros:**
- ✅ Code is private
- ✅ Higher API rate limit with token (5,000/hour)

**Cons:**
- ❌ Requires GitHub token for each site
- ❌ More setup complexity
- ❌ Paid GitHub plan needed (unless you're a student/teacher)

**Best for:** Proprietary code, premium themes

**To use private repo:**
1. Create Personal Access Token: https://github.com/settings/tokens
2. Give it `repo` scope
3. Enter token in WP Wizards → Theme Updates → GitHub Token

## Troubleshooting

### "Repository not found" Error

**Check:**
- ✅ Repository name is correct
- ✅ Username is correct
- ✅ Repository exists and is accessible
- ✅ If private: token is entered and has `repo` scope

### "No updates found"

**Check:**
- ✅ At least one release exists on GitHub
- ✅ Release tag matches version in style.css
- ✅ Release is published (not draft)
- ✅ Cache is cleared

### Authentication Issues

**Solutions:**
- Use Personal Access Token instead of password
- Check token has correct permissions
- Verify token hasn't expired

## Quick Reference

### Git Commands You'll Use Most:

```bash
# Check status
git status

# Add all changes
git add .

# Commit changes
git commit -m "Description of changes"

# Push to GitHub
git push origin main

# Create and push a tag (alternative to web interface)
git tag v1.0.1
git push origin v1.0.1
```

### GitHub Release Checklist:

- [ ] Version updated in `style.css`
- [ ] Changes committed and pushed to GitHub
- [ ] Release tag created (matches version)
- [ ] Release notes written
- [ ] Theme zip file attached (optional but recommended)
- [ ] Release published (not draft)

## Next Steps

1. ✅ Set up GitHub repository
2. ✅ Push your theme files
3. ✅ Create first release
4. ✅ Configure WordPress sites
5. ✅ Test update system
6. ✅ Start using the workflow!

## Alternative: Using Plugin Update Checker Library

If you prefer using a proven library instead of our custom solution, you can use:

**YahnisElsts Plugin Update Checker** (supports themes too):
- Download: https://github.com/YahnisElsts/plugin-update-checker
- Documentation: https://github.com/YahnisElsts/plugin-update-checker

However, our custom solution is already built and integrated into your theme, so you don't need this unless you want to switch.

---

**Need Help?** Check the main documentation in `GITHUB-UPDATE-SETUP.md` for detailed information about the update system.
