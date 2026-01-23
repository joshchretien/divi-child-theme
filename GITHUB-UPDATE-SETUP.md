# GitHub-Based Theme Update System

This guide explains how to use GitHub Releases to automatically push theme updates to all client sites.

## Overview

Your child theme now checks GitHub Releases for updates. When you create a new release on GitHub, all client sites will automatically see the update notification within 12 hours (or immediately if they clear the cache).

## Benefits of GitHub Releases

✅ **No custom server needed** - GitHub hosts everything  
✅ **Automatic versioning** - Tag-based version management  
✅ **Built-in changelog** - Release notes become the changelog  
✅ **Secure downloads** - GitHub handles file hosting  
✅ **Free for public repos** - No hosting costs  
✅ **Version history** - All releases are tracked  

## Initial Setup

### 1. Create GitHub Repository

1. Go to GitHub and create a new repository (or use an existing one)
2. Make it **public** (or private if you'll use a token)
3. Note your username/organization and repo name

### 2. Configure in WordPress

1. Go to **WP Wizards → Theme Updates** in WordPress admin
2. Enter your GitHub username/organization
3. Enter your repository name
4. (Optional) Add a GitHub Personal Access Token if:
   - Using a private repository
   - You need higher API rate limits (60 requests/hour vs 5000/hour)
5. Click **Save Settings**

### 3. Create Your First Release

See "Releasing Updates" section below.

## Releasing Updates

### Step-by-Step Process

1. **Make your changes** to the theme files
2. **Update version in `style.css`**:
   ```css
   Version: 1.0.1
   ```
3. **Commit and push** to GitHub:
   ```bash
   git add .
   git commit -m "Version 1.0.1 - Added new features"
   git push origin main
   ```
4. **Create a GitHub Release**:
   - Go to your GitHub repo
   - Click **Releases** → **Create a new release**
   - **Tag version**: `v1.0.1` (must match version in style.css, with or without 'v' prefix)
   - **Release title**: `Version 1.0.1` or descriptive title
   - **Description**: Add your changelog/notes (this becomes the update changelog)
   - **Attach files** (optional): Upload a zip file of your theme
   - Click **Publish release**

5. **Done!** All client sites will see the update within 12 hours

### Release Tag Format

The tag name should match your version number. Both formats work:
- `v1.0.1` (with 'v' prefix) ✅
- `1.0.1` (without prefix) ✅

The system automatically strips the 'v' prefix if present.

### Attaching Theme Zip File (Recommended)

**Option 1: Attach zip to release (Best)**
- Create a zip of your theme folder
- Name it: `divi-child.zip` or `[theme-slug].zip`
- Upload it as a release asset
- The system will automatically use this file

**Option 2: Use GitHub source code zip (Fallback)**
- If no zip is attached, the system uses GitHub's auto-generated source zip
- This includes the entire repo, so you may need to adjust the folder structure

**Recommended zip structure:**
```
divi-child.zip
└── divi-child/
    ├── functions.php
    ├── style.css
    ├── class-theme-updater.php
    └── ... (other theme files)
```

## Release Notes / Changelog

The release description on GitHub becomes the changelog shown to users. Use Markdown formatting:

```markdown
## What's New

- Added featured image caption shortcode
- Improved performance
- Fixed bug with admin menu

## Improvements

- Better error handling
- Updated documentation
```

This will be displayed in the WordPress update details.

## Workflow Example

Here's a complete example workflow:

### 1. Development
```bash
# Make changes to theme
# Edit functions.php, style.css, etc.
```

### 2. Update Version
```css
/* style.css */
Version: 1.0.2
```

### 3. Commit
```bash
git add .
git commit -m "Version 1.0.2 - Added new shortcode"
git push origin main
```

### 4. Create Release
- GitHub → Releases → Draft a new release
- Tag: `v1.0.2`
- Title: `Version 1.0.2`
- Description:
  ```
  ## New Features
  - Added [new_shortcode] shortcode
  
  ## Bug Fixes
  - Fixed issue with admin menu display
  ```
- Attach: `divi-child-v1.0.2.zip`
- Publish release

### 5. Client Sites
- Within 12 hours, all sites check for updates
- Update notification appears in WordPress admin
- Users click "Update" → Theme updates automatically

## GitHub Personal Access Token (Optional)

### When to Use a Token

1. **Private repositories** - Required to access private repos
2. **Higher rate limits** - 5,000 requests/hour vs 60/hour
3. **Multiple sites** - If you have many client sites checking frequently

### How to Create a Token

1. Go to https://github.com/settings/tokens
2. Click **Generate new token** → **Generate new token (classic)**
3. **Note**: `WP Wizards Theme Updates`
4. **Expiration**: Choose appropriate (90 days, 1 year, or no expiration)
5. **Scopes**: 
   - For **public repos**: No scopes needed (just click Generate)
   - For **private repos**: Check `repo` scope
6. Click **Generate token**
7. Copy the token (starts with `ghp_`)
8. Paste it in WP Wizards → Theme Updates → GitHub Token

### Token Security

- Tokens are stored in WordPress database (encrypted by WordPress)
- Only visible to administrators
- Can be revoked from GitHub at any time
- Use the minimum permissions needed

## Testing Updates

### Test on a Staging Site First

1. Create a test release with a higher version
2. Configure the test site with your GitHub repo
3. Clear the update cache
4. Verify the update appears
5. Test the update process
6. If successful, the release is ready for production

### Clear Update Cache

In WP Wizards → Theme Updates, click **Clear Update Cache** to force an immediate check.

## Troubleshooting

### Updates Not Showing

**Check:**
1. ✅ GitHub repo is configured correctly
2. ✅ Release exists on GitHub
3. ✅ Tag version matches style.css version
4. ✅ Release is published (not draft)
5. ✅ Cache is cleared

**Debug:**
- Check browser console for API errors
- Verify GitHub API is accessible: `https://api.github.com/repos/[username]/[repo]/releases/latest`
- Check WordPress debug log

### Download Fails

**Check:**
1. ✅ Zip file is attached to release (or source zip is accessible)
2. ✅ Zip file is not corrupted
3. ✅ File permissions allow download
4. ✅ WordPress can write to themes directory

**Solutions:**
- Re-upload the zip file to the release
- Check file permissions on server
- Verify zip structure is correct

### Rate Limit Issues

If you see rate limit errors:
- Add a GitHub Personal Access Token (increases limit to 5,000/hour)
- Reduce update check frequency (edit `$cache_expiration` in `class-theme-updater.php`)

### Version Mismatch

**Problem:** Update shows but version doesn't match

**Solution:** Ensure:
- Tag name matches version in style.css
- Both use same format (e.g., both `1.0.1` or both `v1.0.1`)
- Version comparison works (1.0.2 > 1.0.1)

## Best Practices

1. **Semantic Versioning**: Use `MAJOR.MINOR.PATCH` (1.0.1, 1.1.0, 2.0.0)
2. **Always Update style.css**: Version must match release tag
3. **Write Good Changelogs**: Help users understand what changed
4. **Test Before Releasing**: Use staging site first
5. **Tag Consistently**: Use same format (with or without 'v')
6. **Attach Zip Files**: Makes downloads faster and more reliable
7. **Keep Releases**: Don't delete old releases (for rollback if needed)

## Advanced: Automated Releases

You can automate releases using GitHub Actions:

```yaml
# .github/workflows/release.yml
name: Create Release
on:
  push:
    tags:
      - 'v*'
jobs:
  release:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Create Release
        uses: actions/create-release@v1
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
        with:
          tag_name: ${{ github.ref }}
          release_name: Release ${{ github.ref }}
          body: |
            Automated release from tag ${{ github.ref }}
          draft: false
          prerelease: false
```

Then just tag and push:
```bash
git tag v1.0.2
git push origin v1.0.2
```

## Support

For issues or questions:
- Check GitHub API status: https://www.githubstatus.com/
- Review WordPress debug logs
- Contact WP Wizards support
