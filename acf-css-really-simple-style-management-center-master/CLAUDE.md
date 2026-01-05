# ACF CSS Style Management Center - Claude Memory

## Project Overview
ACF CSS (Advanced Custom Fonts & Colors & Styles) is a comprehensive WordPress style management plugin that provides centralized control over colors, typography, buttons, forms, and admin UI.

## Current Version
- **Version**: 23.0.9
- **Last Updated**: 2026-01-05
- **Edition**: Master (includes all features)

## Recent Changes (v23.0.9 - Phase 47)

### TGMPA Recommended Plugins Temporarily Disabled
**Problem**: ACF CSS v23.0.2 was being auto-installed repeatedly, and other plugins failed to install.

**Root Cause**: TGMPA's `source => 'external'` setting without actual download URLs.

**Solution**: Temporarily disabled TGMPA recommended plugins feature until Neural Link server is updated with latest ZIP files.

**Changes**:
- `acf-css-really-simple-style-guide.php` - Commented out `JJ_Required_Plugins::instance()` call

**Next Steps**:
1. Upload latest plugin ZIPs to WordPress server
2. Update Neural Link server with new download URLs
3. Re-enable TGMPA feature

## Previous Changes (v23.0.8 - Phase 46)

### Dashboard & Build System Upgrade
1. **Timestamp Format**: Changed to 12-hour AM/PM format (YYYY-MM-DD AM/PM H:MM)
2. **Addon Category**: Added new category between Core and Free plugins
3. **Download Buttons**: Added Free/Pro buttons in Build Registry table
4. **Plugin Names**: Now displays actual Plugin Name from PHP headers
5. **Auto-Update**: Dashboard regenerates automatically during builds
6. **Version Increment**: All plugins updated (+0.1)

**New File**: `phase46_dashboard_upgrade.py` - Comprehensive build automation script

## Previous Changes (v23.0.7)

### Bug Fix: TGMPA Plugin File Mapping
**Problem**:
1. ACF CSS v23.0.2 was being installed duplicately via WP Bulk Manager
2. Other plugins (WP Bulk Manager, Marketing Dashboard, SEO) failed to install/activate

**Root Cause**:
Wrong plugin main file mapping in TGMPA:
```php
// WRONG
'wp-bulk-manager' => 'wp-bulk-manager/wp-bulk-installer.php'

// CORRECT
'wp-bulk-manager' => 'wp-bulk-manager/wp-bulk-manager.php'
```

**Files Modified**:
- `includes/class-jj-custom-tgmpa.php` - Fixed file mapping + added dynamic detection
- `includes/class-jj-required-plugins.php` - Fixed file mapping + added fallback paths

### Previous Update (v23.0.6)
- Added conditional plugin tabs (WooCommerce, LearnDash, Ultimate Member, Fluent Community, BuddyPress)
- Moved "Quick Navigation" to top row layout in Settings Manager

## Key Files Structure
```
acf-css-really-simple-style-management-center-master/
├── acf-css-really-simple-style-guide.php   # Main plugin file
├── includes/
│   ├── class-jj-simple-style-guide.php     # Core style guide class
│   ├── class-jj-admin-center.php           # Admin settings
│   ├── class-jj-custom-tgmpa.php           # Custom TGMPA implementation
│   ├── class-jj-required-plugins.php       # Required plugins manager
│   ├── class-jj-edition-controller.php     # Edition management
│   └── editor-views/                       # Section view files
│       ├── view-section-colors.php
│       ├── view-section-typography.php
│       ├── view-section-buttons.php
│       ├── view-section-forms.php
│       ├── view-section-fields.php
│       ├── view-section-woocommerce.php    # [v23.0.6]
│       ├── view-section-learndash.php      # [v23.0.6]
│       ├── view-section-ultimate-member.php # [v23.0.6]
│       ├── view-section-fluent-community.php # [v23.0.6]
│       └── view-section-buddypress.php     # [v23.0.6]
├── assets/
│   ├── css/
│   │   ├── jj-admin-center.css
│   │   └── jj-ui-system-2026.css
│   └── js/
│       └── jj-style-guide-editor.js
└── changelog.md
```

## Important Technical Notes

### Plugin File Detection
The TGMPA system uses multiple methods to find plugin main files:
1. Static mapping (primary)
2. Alternative paths (fallback)
3. Dynamic detection via `find_main_plugin_file()` (last resort)

### Edition System
- Free: Basic features
- Basic: Extended adapters
- Premium: Admin theme + Lab Center
- Partner: White labeling
- Master: All features (internal use)

### Related Plugins (3J Labs Family)
- **3J Neural Link** (v8.0.0) - 라이센스/업데이트 중앙 관리 시스템
- ACF Code Snippets Box
- WP Bulk Manager
- WP Bulk SEO & AEO
- JJ Marketing Automation Dashboard

### 3J Neural Link (v8.0.0) - 2026-01-05
다중 플러그인 배포 관리 시스템 완성:
- 14개 플러그인 중앙 관리 UI (배포 관리 페이지)
- 메뉴명: "3J 라이센스 관리 🔑" > "📦 배포 관리"
- 빌드 스크립트: `build-plugin.ps1` (버전 없는 파일명으로 ZIP 생성)

## Development Principles
1. Always use `class_exists()`, `function_exists()` before calling classes/functions
2. Use `JJ_STYLE_GUIDE_PATH` constant for file paths
3. Check file existence before including
4. Version updates require changes in both header and constant

## Build Commands
```powershell
# Create ZIP for distribution
Compress-Archive -Path "acf-css-really-simple-style-management-center-master" -DestinationPath "acf-css-really-simple-style-management-center-master.zip" -Force
```
