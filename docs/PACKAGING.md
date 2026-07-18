# Packaging & WordPress.org Submission

How to build the distributable `smart-seo-booster.zip`, what it contains, and how to
verify and list it in the WordPress.org plugin directory.

---

## 1. What ships in the release zip

The zip contains a **single top-level folder** named `smart-seo-booster/` (required by
WordPress' "Upload Plugin" installer and by SVN). Only runtime files are included:

```
smart-seo-booster/
├── smart-seo-booster.php        # Main plugin bootstrap + header
├── uninstall.php                # Removes options & post meta on delete
├── readme.txt                   # WordPress.org listing (the file wp.org parses)
├── LICENSE                      # GPLv2
├── includes/                    # 18 PHP module classes
│   ├── class-loader.php
│   ├── class-settings.php
│   ├── class-admin-ui.php
│   ├── class-seo-core.php
│   ├── class-meta-templates.php
│   ├── class-meta-fields.php
│   ├── class-schema-generator.php
│   ├── class-content-auditor.php
│   ├── class-link-analyzer.php
│   ├── class-seo-score-display.php
│   ├── class-sitemap.php
│   ├── class-breadcrumbs.php
│   ├── class-setup-wizard.php
│   ├── class-redirects.php
│   ├── class-bulk-editor.php
│   ├── class-importer.php
│   ├── class-woocommerce.php
│   └── class-local-seo.php
├── schema/                      # JSON-LD templates (dynamic, filterable)
│   ├── article-schema.php
│   ├── faq-schema.php
│   ├── local-business-schema.php
│   ├── organization-schema.php
│   └── profile-page-schema.php
├── templates/
│   └── audit-report.php
├── css/                         # admin.css, meta-box.css, settings.css
├── js/                          # block-editor.js, meta-fields.js, seo-score.js,
│                                #   settings.js, local-seo-block.js
└── languages/
    └── smart-seo-booster.pot    # Translation template
```

### Deliberately **excluded** from the zip

Controlled by [`.distignore`](../.distignore). None of these belong in a shipped plugin:

| Excluded | Why |
|----------|-----|
| `.git/`, `.github/`, `.gitignore`, `.distignore` | Version-control / build metadata |
| `docs/` (incl. `docs/archive/`) | Development notes — not runtime |
| `README.md`, `STRATEGY.md`, `CONTRIBUTING.md`, `CHANGELOG.md` | Dev/GitHub docs; the wp.org listing lives in `readme.txt` |
| `templates/help-page.php` | Unused/dead template (not wired to any code) |
| `node_modules/`, `tests/`, `*.map`, `*.zip` | Tooling / build artifacts |

---

## 2. Build the zip

### Option A — PowerShell (Windows, no extra tools)

From the repo root:

```powershell
$src   = "C:\xampp\htdocs\smart-seo-booster"
$stage = Join-Path $env:TEMP "ssb-build\smart-seo-booster"

# Fresh staging folder
Remove-Item (Split-Path $stage) -Recurse -Force -ErrorAction SilentlyContinue
New-Item -ItemType Directory -Force -Path $stage | Out-Null

# Runtime files
Copy-Item "$src\smart-seo-booster.php","$src\uninstall.php","$src\readme.txt","$src\LICENSE" $stage
Copy-Item "$src\includes","$src\schema","$src\css","$src\js","$src\languages" $stage -Recurse
New-Item -ItemType Directory -Force -Path "$stage\templates" | Out-Null
Copy-Item "$src\templates\audit-report.php" "$stage\templates"

# Zip (single top-level smart-seo-booster/ folder)
Compress-Archive -Path $stage -DestinationPath "$src\smart-seo-booster.zip" -Force
```

### Option B — Bash / zip (macOS, Linux, CI)

```bash
cd /path/above/plugin
rsync -a --exclude-from=smart-seo-booster/.distignore \
      --exclude='templates/help-page.php' \
      smart-seo-booster/ build/smart-seo-booster/
( cd build && zip -r ../smart-seo-booster.zip smart-seo-booster )
```

The result, `smart-seo-booster.zip`, is what you upload for testing and to SVN.

---

## 3. Verify before submitting

Run these against a live WordPress install (the zip, or the plugin folder):

- [ ] **Plugin Check** (official): install the *Plugin Check* plugin, then
      `wp plugin check smart-seo-booster` (or **Tools → Plugin Check**). Target: 0 errors/warnings.
- [ ] **readme.txt validator**: <https://wordpress.org/plugins/developers/readme-validator/> — paste `readme.txt`, confirm no errors and that Stable tag matches the header version.
- [ ] **Install test**: Plugins → Add New → Upload Plugin → the zip → Activate. No fatals.
- [ ] **Schema**: run a post and the homepage through the [Rich Results Test](https://search.google.com/test/rich-results).
- [ ] **Sitemap**: open `/sitemap.xml` and a `/sitemap-posts.xml` — valid XML.
- [ ] **Importer / Redirects / Bulk editor / Blocks**: exercise each once.
- [ ] **Uninstall**: delete the plugin and confirm options/post-meta are removed.

---

## 4. List on the WordPress.org plugin directory

1. **Submit for review** at <https://wordpress.org/plugins/developers/add/> — upload the zip. A human reviews new plugins (typically days to a few weeks).
2. On approval you receive an **SVN repository**. Commit the release:
   ```bash
   svn co https://plugins.svn.wordpress.org/smart-seo-booster
   # copy the plugin files into trunk/
   svn cp trunk tags/2.5.0
   svn ci -m "Release 2.5.0"
   ```
3. **Assets** (not in the plugin zip — they go in the SVN `assets/` folder):
   - `icon-256x256.png`, `icon-128x128.png`
   - `banner-1544x500.png`, `banner-772x250.png`
   - `screenshot-1.png` … (match the "Screenshots" section in `readme.txt`)
4. Ensure `Stable tag` in `readme.txt` equals the tag folder (`2.5.0`).

See [SUBMISSION_CHECKLIST.md](SUBMISSION_CHECKLIST.md) for the full pre-flight list.
