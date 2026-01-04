<?php
/**
 * [v23.0.6] BuddyPress 스타일 섹션
 * - BuddyPress가 활성화된 경우에만 표시
 *
 * @package ACF_CSS_Style_Guide
 * @since 23.0.6
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// $options 변수가 없으면 기본값 사용
if ( ! isset( $options ) || ! is_array( $options ) ) {
    $options = array();
}

// BuddyPress 옵션 로드
$bp_options = $options['buddypress'] ?? array();
?>

<div class="jj-section-global" id="jj-section-buddypress">
    <h2 class="jj-section-title">
        <span class="dashicons dashicons-buddicons-buddypress-logo" style="color: #d84800; margin-right: 8px;"></span>
        <?php _e( 'BuddyPress', 'acf-css-really-simple-style-management-center' ); ?>
    </h2>
    <p class="description">
        <?php _e( 'BuddyPress 프로필, 활동 스트림, 그룹, 메시지의 스타일을 통합 관리합니다.', 'acf-css-really-simple-style-management-center' ); ?>
    </p>

    <div class="jj-bp-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">

        <!-- 활동 스트림 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '활동 스트림', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '활동 항목 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="buddypress[activity][item_bg]"
                       value="<?php echo esc_attr( $bp_options['activity']['item_bg'] ?? '#ffffff' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '활동 테두리', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="buddypress[activity][border_color]"
                       value="<?php echo esc_attr( $bp_options['activity']['border_color'] ?? '#e2e8f0' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '좋아요/댓글 아이콘', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="buddypress[activity][action_color]"
                       value="<?php echo esc_attr( $bp_options['activity']['action_color'] ?? '#d84800' ); ?>" />
            </div>
        </fieldset>

        <!-- 프로필 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '프로필', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '프로필 헤더 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="buddypress[profile][header_bg]"
                       value="<?php echo esc_attr( $bp_options['profile']['header_bg'] ?? '#f8fafc' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '사용자명 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="buddypress[profile][username_color]"
                       value="<?php echo esc_attr( $bp_options['profile']['username_color'] ?? '#d84800' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '네비게이션 활성 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="buddypress[profile][nav_active]"
                       value="<?php echo esc_attr( $bp_options['profile']['nav_active'] ?? '#d84800' ); ?>" />
            </div>
        </fieldset>

        <!-- 그룹 & 메시지 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '그룹 & 메시지', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '그룹 카드 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="buddypress[group][card_bg]"
                       value="<?php echo esc_attr( $bp_options['group']['card_bg'] ?? '#ffffff' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '메시지 버블 (받은)', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="buddypress[message][received_bg]"
                       value="<?php echo esc_attr( $bp_options['message']['received_bg'] ?? '#f1f5f9' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '메시지 버블 (보낸)', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="buddypress[message][sent_bg]"
                       value="<?php echo esc_attr( $bp_options['message']['sent_bg'] ?? '#d84800' ); ?>" />
            </div>
        </fieldset>

    </div>
</div>
