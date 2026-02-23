# How to Download and Install the WP Wizards Child Theme

## Method 1: Download from GitHub Releases (Recommended)

### Step 1: Go to GitHub Releases
1. Visit: https://github.com/joshchretien/divi-child-theme/releases
2. Find the latest release (e.g., `v1.0.10`)
3. Click on the release tag name

### Step 2: Download the Source Code
1. Scroll down to the "Assets" section
2. Click **"Source code (zip)"** - This downloads the complete theme
3. **OR** if there's a pre-built zip file (e.g., `Divi-Child-v1.0.10.zip`), download that instead

### Step 3: Extract and Upload
1. Extract the downloaded zip file
2. **Important**: The folder inside should be named something like `divi-child-theme-1.0.10` or `divi-child-theme-main`
3. **Rename the folder** to match your theme's slug (e.g., `divi-child-theme` or `wp-wizards-custom`)
4. Upload the entire folder to `/wp-content/themes/` on your WordPress site
5. The final path should be: `/wp-content/themes/your-theme-name/`

### Step 4: Activate
1. Go to **WordPress Admin → Appearance → Themes**
2. Find your theme and click **Activate**

---

## Method 2: Clone from GitHub (For Developers)

If you have Git installed and want to keep it updated:

```bash
cd /path/to/wp-content/themes/
git clone https://github.com/joshchretien/divi-child-theme.git your-theme-name
cd your-theme-name
```

**Note**: This method keeps the `.git` folder, which is fine for development.

---

## Method 3: Download Main Branch (Not Recommended)

⚠️ **Warning**: Only use this if there's no release available yet.

1. Go to: https://github.com/joshchretien/divi-child-theme
2. Click the green **"Code"** button
3. Click **"Download ZIP"**
4. Extract and follow Step 3 above

**Note**: This includes development files like `.gitignore`, `create-release.ps1`, etc. These won't hurt, but releases are cleaner.

---

## Important: Folder Structure

After extraction, your theme folder should contain:
```
your-theme-name/
├── assets/
├── bundled-plugins/
├── class-tgm-plugin-activation.php
├── class-theme-updater.php
├── client-customizations.php.example
├── functions.php
├── header.php
├── screenshot.png
└── style.css
```

**Do NOT** upload a folder that contains another folder with the same name (double-nested).

---

## Common Issues

### ❌ "Theme folder not found" error
- **Cause**: Wrong folder name or double-nested folder
- **Fix**: Make sure the folder directly contains `style.css` and `functions.php`

### ❌ "Fatal error: Cannot declare class..."
- **Cause**: Class already exists (usually from `client-customizations.php`)
- **Fix**: Update to latest version (1.0.10+) which includes class_exists checks

### ❌ "Parse error: unexpected token"
- **Cause**: Corrupted download or wrong files
- **Fix**: Re-download from GitHub Releases, make sure you get the complete source code

### ❌ Theme doesn't appear in WordPress
- **Cause**: Missing `style.css` or wrong folder location
- **Fix**: Verify the folder is in `/wp-content/themes/` and contains `style.css` with proper headers

---

## After Installation

1. **Configure Updates** (Optional):
   - Go to **WP Wizards → Theme Updates**
   - Enter repository: `joshchretien/divi-child-theme`
   - Save settings

2. **Create Customizations File**:
   - The theme will auto-create `client-customizations.php` from the example
   - Or manually copy `client-customizations.php.example` to `client-customizations.php`

3. **Verify Installation**:
   - Check **WP Wizards** menu appears in WordPress admin
   - Visit **WP Wizards → Documentation** to see theme features

---

## Quick Checklist

- [ ] Downloaded from GitHub Releases (not main branch)
- [ ] Extracted zip file
- [ ] Renamed folder to desired theme name
- [ ] Uploaded to `/wp-content/themes/`
- [ ] Folder contains `style.css` and `functions.php` directly
- [ ] Activated theme in WordPress
- [ ] WP Wizards menu appears in admin

---

## Need Help?

If you're still having issues:
1. Check the error logs (usually in `/wp-content/debug.log` or server error logs)
2. Verify you downloaded from the **Releases** page, not the main branch
3. Make sure you're using the latest release version
4. Contact support at [WP Wizards](https://www.wpwizards.com)
