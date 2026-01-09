# ACF CSS Master Plugin - Security Audit Report

**Date**: 2026-01-07  
**Auditor**: CTO Jason (Claude)  
**Plugin Version**: 26.0.15  
**Scope**: AJAX Handlers Security (nonce, capability, sanitization)

---

## Executive Summary

| Category | Status | Details |
|----------|--------|---------|
| Nonce Verification | **1 ISSUE** | 1 AJAX handler missing nonce |
| Capability Checks | **PASS** | All write operations protected |
| Input Sanitization | **PASS** | Proper sanitization applied |
| Output Escaping | **REVIEW** | Some areas need verification |

---

## AJAX Handlers Audit

### Total AJAX Actions: 23

### Files Analyzed:
1. `class-jj-css-variable-extractor.php` - 5 actions
2. `class-jj-demo-importer.php` - 2 actions
3. `class-jj-css-optimizer-ai.php` - 1 action
4. `class-jj-history-manager.php` - 1 action
5. `class-jj-font-manager.php` - 3 actions
6. `class-jj-palette-manager.php` - 5 actions
7. `class-jj-team-sync.php` - 3 actions
8. `class-jj-typography-manager.php` - 3 actions

---

## CRITICAL: Nonce Verification Missing

### File: `class-jj-palette-manager.php`
### Function: `ajax_get_customizer_colors()` (Line 540)

```php
// CURRENT CODE (VULNERABLE)
public function ajax_get_customizer_colors() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
    }
    // ... missing check_ajax_referer() ...
```

**Risk**: CSRF attack possible. Attacker can trick admin to execute this action.

**Fix Required**:
```php
public function ajax_get_customizer_colors() {
    check_ajax_referer( 'jj_style_guide_nonce', 'nonce' ); // ADD THIS LINE
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
    }
```

---

## Security Status by Handler

### PASSED (22/23)

| File | Action | Nonce | Capability | Sanitization |
|------|--------|-------|------------|--------------|
| css-variable-extractor | jj_extract_css_variables | ✅ | - | ✅ |
| css-variable-extractor | jj_export_css_variables | ✅ | - | ✅ |
| css-variable-extractor | jj_import_css_variables | ✅ | - | ✅ |
| css-variable-extractor | jj_get_current_variables | ✅ | - | - |
| css-variable-extractor | jj_scan_theme_variables | ✅ | - | - |
| demo-importer | jj_import_style_preset | ✅ | ✅ | ✅ |
| demo-importer | jj_apply_recommended_setup | ✅ | ✅ | - |
| css-optimizer-ai | jj_apply_css_optimization | ✅ | ✅ | - |
| history-manager | jj_rollback_settings | ✅ | ✅ | ✅ |
| font-manager | jj_save_font_settings | ✅ | ✅ | ✅ |
| font-manager | jj_get_google_fonts | ✅ | - | - |
| font-manager | jj_upload_local_font | ✅ | ✅ | ✅ |
| palette-manager | jj_save_palette | ✅ | ✅ | ✅ |
| palette-manager | jj_get_palettes | ✅ | - | - |
| palette-manager | jj_delete_palette | ✅ | ✅ | ✅ |
| palette-manager | jj_generate_palette | ✅ | - | ✅ |
| team-sync | jj_export_settings | ✅ | ✅ | ✅ |
| team-sync | jj_import_settings | ✅ | ✅ | ✅ |
| team-sync | jj_get_export_history | ✅ | ✅ | - |
| typography-manager | jj_save_typography | ✅ | ✅ | ✅ |
| typography-manager | jj_get_typography | ✅ | - | - |
| typography-manager | jj_reset_typography | ✅ | ✅ | - |

### FAILED (1/23)

| File | Action | Issue |
|------|--------|-------|
| palette-manager | jj_get_customizer_colors | ❌ Missing nonce verification |

---

## Input Sanitization Audit

### Good Practices Found:

1. **sanitize_key()** - Used for IDs (palette_id, preset_id, harmony)
2. **sanitize_text_field()** - Used for text inputs (version_tag, format)
3. **sanitize_textarea_field()** - Used for multiline (changelog, description)
4. **sanitize_hex_color()** - Used for colors (base_color)
5. **esc_url_raw()** - Used for URLs
6. **wp_unslash()** - Used for JSON data before json_decode()
7. **json_decode() + stripslashes()** - Used for complex data (palette_data, settings)

### Recommendations:

1. **JSON data validation**: After `json_decode()`, validate structure before use
2. **CSS content**: Consider sanitizing CSS content in `ajax_extract_variables()`

---

## Capability Matrix

| Operation Type | Required Capability | Status |
|---------------|---------------------|--------|
| Save settings | manage_options | ✅ |
| Delete data | manage_options | ✅ |
| Import data | manage_options | ✅ |
| Export data | manage_options | ✅ |
| Upload files | upload_files | ✅ |
| Read-only | No check needed | ✅ |

---

## Action Items

### IMMEDIATE (v26.0.16)

1. **FIX**: Add nonce verification to `ajax_get_customizer_colors()`

### RECOMMENDED (Future)

1. Add capability check to `ajax_generate_palette()` (currently read-only but generates data)
2. Validate JSON structure after `json_decode()` in team-sync
3. Consider rate limiting for AJAX requests

---

## Conclusion

The plugin demonstrates **good security practices** overall with proper use of:
- `check_ajax_referer()` for CSRF protection
- `current_user_can()` for authorization
- WordPress sanitization functions

**1 critical fix required**: Add nonce verification to `ajax_get_customizer_colors()`.

---

*Report generated by CTO Jason (3J Labs)*
