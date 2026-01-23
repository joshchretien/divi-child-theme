# Pushing to GitHub from Cursor

Two easy ways to push your theme to GitHub from Cursor.

## Method 1: Using Cursor's Source Control Panel (Easiest) ⭐

### Step 1: Initialize Git (if not done)

1. **Open Terminal in Cursor**: 
   - Press `` Ctrl + ` `` (backtick) or
   - View → Terminal

2. **Run these commands**:
   ```powershell
   cd "C:\Users\Josh Chretien\Dropbox\WP Wizards\CHILD THEME\Divi-Child\Divi-Child"
   git init
   ```

### Step 2: Create GitHub Repository First

1. Go to https://github.com/new
2. **Repository name**: `divi-child-theme` (or your choice)
3. **Visibility**: Public (recommended)
4. **DO NOT** check "Initialize with README"
5. Click **Create repository**
6. **Copy the repository URL** (you'll need it)

### Step 3: Add Remote in Cursor Terminal

In Cursor's terminal, run:
```powershell
git remote add origin https://github.com/YOUR-USERNAME/divi-child-theme.git
```
(Replace `YOUR-USERNAME` and `divi-child-theme` with your actual values)

### Step 4: Use Source Control Panel

1. **Click the Source Control icon** in the left sidebar (looks like a branch/fork icon)
   - Or press `Ctrl + Shift + G`

2. **Stage all files**:
   - Click the **+** next to "Changes" to stage all files
   - Or type a commit message in the box and click the checkmark

3. **Commit**:
   - Type commit message: `Initial commit - WP Wizards Divi Child Theme`
   - Press `Ctrl + Enter` or click the checkmark ✓

4. **Push to GitHub**:
   - Click the **...** (three dots) menu at the top
   - Select **Push** → **Push to...**
   - Choose `origin` → `main` (or `master`)
   - If prompted, enter your GitHub credentials:
     - **Username**: Your GitHub username
     - **Password**: Use a Personal Access Token (not your password)
       - Get token: https://github.com/settings/tokens
       - Generate new token (classic) with `repo` scope

### Step 5: Verify

Go to your GitHub repository - you should see all your files!

---

## Method 2: Using Terminal in Cursor

### Step 1: Open Terminal

Press `` Ctrl + ` `` or go to **View → Terminal**

### Step 2: Run These Commands

```powershell
# Navigate to theme folder
cd "C:\Users\Josh Chretien\Dropbox\WP Wizards\CHILD THEME\Divi-Child\Divi-Child"

# Initialize Git (if not done)
git init

# Add all files
git add .

# Commit
git commit -m "Initial commit - WP Wizards Divi Child Theme"

# Add GitHub remote (REPLACE with your info)
git remote add origin https://github.com/YOUR-USERNAME/divi-child-theme.git

# Push to GitHub
git branch -M main
git push -u origin main
```

**When prompted for credentials:**
- **Username**: Your GitHub username
- **Password**: Personal Access Token (get from https://github.com/settings/tokens)

---

## Authentication: Personal Access Token

GitHub no longer accepts passwords. You need a token:

1. Go to https://github.com/settings/tokens
2. Click **Generate new token** → **Generate new token (classic)**
3. **Note**: `WP Wizards Theme`
4. **Expiration**: Choose (90 days, 1 year, or no expiration)
5. **Scopes**: Check `repo` (for private repos) or leave empty (for public)
6. Click **Generate token**
7. **Copy the token** (starts with `ghp_`)
8. **Use this token as your password** when pushing

---

## Troubleshooting

### "Repository not found"
- Check your repository URL is correct
- Make sure the repo exists on GitHub
- Verify your username is correct

### "Authentication failed"
- Make sure you're using a Personal Access Token, not your password
- Check the token hasn't expired
- Verify token has `repo` scope if using private repo

### "Nothing to commit"
- Files might already be committed
- Check if `.gitignore` is excluding files you want
- Run `git status` to see what's happening

### "Remote origin already exists"
- Run: `git remote remove origin`
- Then add it again with the correct URL

---

## Quick Reference

**Cursor Shortcuts:**
- `Ctrl + Shift + G` - Open Source Control
- `Ctrl + ` ` - Open Terminal
- `Ctrl + Enter` - Commit (when in Source Control)

**Common Git Commands:**
```powershell
git status          # Check status
git add .           # Stage all files
git commit -m "msg" # Commit changes
git push            # Push to GitHub
git pull            # Pull from GitHub
```

---

## Next Steps After Pushing

1. **Create your first release** on GitHub:
   - Go to your repo → Releases → Create new release
   - Tag: `v1.0.0` (match your style.css version)
   - Add release notes
   - Publish

2. **Configure WordPress sites**:
   - WP Wizards → Theme Updates
   - Enter GitHub username and repo name
   - Save

3. **Test updates** work!

---

That's it! Your theme is now on GitHub and ready for automatic updates! 🎉
