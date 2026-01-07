# Phase 52: Build System Fix & Plugin Updates

**Date**: 2026-01-07
**Author**: Jason (CTO, 3J Labs)
**Status**: Completed

---

## Summary

This session focused on fixing critical build system issues and updating two main plugins:
1. **ACF CSS Manager** (v26.0.14 → v26.0.15)
2. **1-Click SEO Pro** (v2.1.4 → v2.1.5)

---

## Issues Resolved

### 1. WordPress Plugin Installation Failure
**Problem**: "플러그인 파일이 존재하지 않습니다" (Plugin file does not exist)

**Root Cause**: ZIP folder name didn't match main PHP file name.
- ZIP folder: `acf-css-really-simple-style-management-center-master`
- Main PHP: `acf-css-really-simple-style-guide.php`

**Solution**: 
- Renamed ZIP internal folder to `acf-css-really-simple-style-guide`
- Folder name now matches main PHP file (without .php extension)

### 2. Duplicate Menu Registration (1-Click SEO Pro)
**Problem**: Both "Bulk SEO" and "1-Click SEO" menus appeared in admin

**Root Cause**: Two classes registering menus:
- `OneClick_SEO_Pro` class in `oneclick-seo-pro.php` → "1-Click SEO"
- `OneClick_SEO_Pro_Admin` class in `admin/class-admin.php` → "Bulk SEO"

**Solution**: 
- Commented out `add_action('admin_menu', ...)` in `admin/class-admin.php`
- Only main plugin class now registers the menu

### 3. License Type Display
**Problem**: Dashboard showed "FREE" badge even for Master edition

**Root Cause**: `$license_type = 'FREE'` was hardcoded

**Solution**:
- Added `ONECLICK_SEO_PRO_LICENSE_TYPE` constant set to 'MASTER'
- Updated dashboard to detect license type from constants
- PRO badge displayed for MASTER/UNLIMITED/PREMIUM editions

### 4. Admin Bar URL Issues (ACF CSS)
**Problem**: Admin Bar links used wrong URL pattern

**Root Cause**: Used `options-general.php?page=...` for top-level menu

**Solution**: Changed to `admin.php?page=jj-style-guide-cockpit`

---

## Files Modified

### ACF CSS Manager (v26.0.15)
| File | Changes |
|------|---------|
| `acf-css-really-simple-style-guide.php` | Version bump, welcome dashboard layout |
| `includes/class-jj-simple-style-guide.php` | Added welcome section to navigation |
| `includes/class-jj-admin-center.php` | Fixed Admin Bar URLs |

### 1-Click SEO Pro (v2.1.5)
| File | Changes |
|------|---------|
| `oneclick-seo-pro.php` | Version bump, LICENSE_TYPE constant |
| `admin/class-admin.php` | Removed duplicate menu registration |
| `admin/views/dashboard.php` | License type detection, PRO badge |

---

## Build System Improvements

### New Build Process
```
ZIP File Name: {plugin-slug}-v{version}.zip
Internal Folder: {plugin-slug}/  (NO version in folder name)
```

### Build Scripts Created
- `build-all-plugins.ps1` - Main build script
- `cleanup-and-build.ps1` - Cleanup old files and rebuild

### Archive Structure
```
dist/
├── acf-css-really-simple-style-guide-v26.0.15.zip
├── oneclick-seo-pro-v2.1.5.zip
└── old/
    └── 20260107-HHMMSS/  (archived old versions)
```

---

## Version Changes

| Plugin | Previous | New |
|--------|----------|-----|
| ACF CSS Manager | 26.0.14 | 26.0.15 |
| 1-Click SEO Pro | 2.1.4 | 2.1.5 |

---

## Nudge System (Cross-Promotion)

The plugin recommendation system is **conditionally displayed**:
- Only shows if the recommended plugin is NOT already installed
- Uses `is_plugin_active()` check

```php
// Example from dashboard.php
<?php if (!is_plugin_active('acf-user-journey-analytics/acf-user-journey-analytics.php')): ?>
    <!-- Recommendation banner -->
<?php endif; ?>
```

---

## Key Learnings

1. **WordPress ZIP Structure**: Folder name MUST match main PHP file name
2. **Menu Registration**: Avoid duplicate `add_action('admin_menu', ...)` calls
3. **Build Versioning**: Keep version in ZIP filename only, not in folder name
4. **License Detection**: Use constants and fallback chains for flexibility

---

## Next Steps

1. Deploy and test new builds on live site
2. Monitor for any remaining issues
3. Consider unifying build system for all 3J Labs plugins

---

*Documented by Jason, CTO @ 3J Labs*
