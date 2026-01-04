<?php
/**
 * [v23.0.6] Ultimate Member 스타일 섹션
 * - Ultimate Member가 활성화된 경우에만 표시
 *
 * @package ACF_CSS_Style_Guide
 * @since 23.0.6
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// $options 변수가 없으면 기본값 사용
if ( ! isset( $options ) || ! is_array( $options ) ) {
    $options = array();
}

// Ultimate Member 옵션 로드
$um_options = $options['ultimate_member'] ?? array();
?>

<div class="jj-section-global" id="jj-section-ultimate-member">
    <h2 class="jj-section-title">
        <span class="dashicons dashicons-groups" style="color: #3ba1da; margin-right: 8px;"></span>
        <?php _e( 'Ultimate Member', 'acf-css-really-simple-style-management-center' ); ?>
    </h2>
    <p class="description">
        <?php _e( 'Ultimate Member 프로필, 로그인/회원가입 폼, 멤버 디렉토리의 스타일을 통합 관리합니다.', 'acf-css-really-simple-style-management-center' ); ?>
    </p>

    <div class="jj-um-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">

        <!-- 프로필 페이지 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '프로필 페이지', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '프로필 커버 높이 (px)', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="number" class="jj-data-field"
                       data-setting-key="ultimate_member[profile][cover_height]"
                       value="<?php echo esc_attr( $um_options['profile']['cover_height'] ?? '300' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '아바타 테두리 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="ultimate_member[profile][avatar_border]"
                       value="<?php echo esc_attr( $um_options['profile']['avatar_border'] ?? '#ffffff' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '탭 활성 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="ultimate_member[profile][tab_active_color]"
                       value="<?php echo esc_attr( $um_options['profile']['tab_active_color'] ?? '#3ba1da' ); ?>" />
            </div>
        </fieldset>

        <!-- 로그인/회원가입 폼 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '로그인/회원가입 폼', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '폼 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="ultimate_member[form][background]"
                       value="<?php echo esc_attr( $um_options['form']['background'] ?? '#ffffff' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '입력 필드 테두리', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="ultimate_member[form][input_border]"
                       value="<?php echo esc_attr( $um_options['form']['input_border'] ?? '#d1d5db' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '제출 버튼 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="ultimate_member[form][submit_bg]"
                       value="<?php echo esc_attr( $um_options['form']['submit_bg'] ?? '#3ba1da' ); ?>" />
            </div>
        </fieldset>

        <!-- 멤버 디렉토리 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '멤버 디렉토리', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '멤버 카드 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="ultimate_member[directory][card_bg]"
                       value="<?php echo esc_attr( $um_options['directory']['card_bg'] ?? '#f8fafc' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '역할 배지 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="ultimate_member[directory][role_badge_bg]"
                       value="<?php echo esc_attr( $um_options['directory']['role_badge_bg'] ?? '#3ba1da' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '검색 버튼 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="ultimate_member[directory][search_btn_bg]"
                       value="<?php echo esc_attr( $um_options['directory']['search_btn_bg'] ?? '#3ba1da' ); ?>" />
            </div>
        </fieldset>

    </div>
</div>
