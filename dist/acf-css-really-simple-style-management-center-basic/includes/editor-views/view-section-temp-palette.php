<?php
/**
 * [v1.9.0-beta4 '제련'] "4. 임시 팔레트" 탭 UI
 * - 스타일 가이드 설정 페이지에서 파싱된 임시 DB($temp_options)의 값을 미리보기
 * - 전역 팔레트로 적용하거나 임시 스냅샷을 관리하는 버튼 UI 제공
 */
if ( ! defined( 'ABSPATH' ) ) exit; // 직접 접근 차단

// [v3.8.0] 버전별 기능 제한 체크
$version_features = class_exists( 'JJ_Version_Features' ) ? JJ_Version_Features::instance() : null;

// 임시 팔레트 옵션 로드 (이 파일은 'jj-simple-style-guide.php'의 'render_style_guide_page' 함수에서 $temp_options를 미리 로드해야 합니다)
$temp_palettes = isset( $temp_options ) && is_array( $temp_options ) ? ( $temp_options['palettes'] ?? array() ) : array();
$temp_brand = is_array( $temp_palettes ) ? ( $temp_palettes['brand'] ?? array() ) : array();
?>

<div class="jj-tab-content" data-tab-content="temp-palette">
    <div class="jj-temp-palette-header">
        <h3><?php _e( '임시 팔레트', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <p class="description">
            <?php _e( '이 영역에는 스타일 가이드 설정 페이지에서 저장된 임시 팔레트 값이 표시됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
            <br>
            <?php _e( '아래 버튼을 사용하여 현재 브랜드 팔레트를 임시 팔레트로 저장하거나, 임시 팔레트를 1. 브랜드 팔레트로 적용할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
            <br>
            <a href="<?php echo esc_url( admin_url( 'post.php?post=' . ( isset( $cockpit_page_id ) ? $cockpit_page_id : get_option( JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY, 0 ) ) . '&action=edit' ) ); ?>" target="_blank">
                <?php _e( '스타일 가이드 설정 페이지 열기 (새 탭)', 'acf-css-really-simple-style-management-center' ); ?>
            </a>
        </p>
    </div>

    <fieldset class="jj-fieldset-group">
        <legend><?php _e( '임시 브랜드 팔레트', 'acf-css-really-simple-style-management-center' ); ?></legend>
        <div class="jj-style-guide-grid jj-grid-4-col">
            
            <div class="jj-control-group jj-temp-palette-preview">
                <label><?php _e( 'Primary Color', 'acf-css-really-simple-style-management-center' ); ?></label>
                <div class="jj-color-preview-box" style="background-color: <?php echo esc_attr( $temp_brand['primary_color'] ?? '#eeeeee' ); ?>;"></div>
                <code><?php echo esc_html( $temp_brand['primary_color'] ?? 'N/A' ); ?></code>
            </div>

            <div class="jj-control-group jj-temp-palette-preview">
                <label><?php _e( 'Primary (Hover)', 'acf-css-really-simple-style-management-center' ); ?></label>
                <div class="jj-color-preview-box" style="background-color: <?php echo esc_attr( $temp_brand['primary_color_hover'] ?? '#eeeeee' ); ?>;"></div>
                <code><?php echo esc_html( $temp_brand['primary_color_hover'] ?? 'N/A' ); ?></code>
            </div>

            <div class="jj-control-group jj-temp-palette-preview">
                <label><?php _e( 'Secondary Color', 'acf-css-really-simple-style-management-center' ); ?></label>
                <div class="jj-color-preview-box" style="background-color: <?php echo esc_attr( $temp_brand['secondary_color'] ?? '#eeeeee' ); ?>;"></div>
                <code><?php echo esc_html( $temp_brand['secondary_color'] ?? 'N/A' ); ?></code>
            </div>

            <div class="jj-control-group jj-temp-palette-preview">
                <label><?php _e( 'Secondary (Hover)', 'acf-css-really-simple-style-management-center' ); ?></label>
                <div class="jj-color-preview-box" style="background-color: <?php echo esc_attr( $temp_brand['secondary_color_hover'] ?? '#eeeeee' ); ?>;"></div>
                <code><?php echo esc_html( $temp_brand['secondary_color_hover'] ?? 'N/A' ); ?></code>
            </div>
        </div>
        
        <div class="jj-apply-palette-actions">
            <button type="button" 
                    class="button jj-save-to-temp-button" 
                    data-apply-target="brand">
                <?php _e( '현재 브랜드 팔레트를 임시 팔레트로 저장', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" 
                    class="button button-primary jj-apply-palette-button" 
                    data-apply-target="brand">
                <?php _e( '임시 팔레트를 1. 브랜드 팔레트로 적용', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <span class="spinner"></span>
            <p class="description">
                <?php _e( '임시 저장은 실험용 팔레트를 보관하는 용도로 사용되며, 적용 버튼을 누르기 전까지 기존 브랜드 팔레트 값은 변경되지 않습니다.', 'acf-css-really-simple-style-management-center' ); ?>
            </p>
        </div>

    </fieldset>

    </div>