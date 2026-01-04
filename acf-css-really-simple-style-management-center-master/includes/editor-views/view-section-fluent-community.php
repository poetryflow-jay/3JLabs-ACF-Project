<?php
/**
 * [v23.0.6] Fluent Community 스타일 섹션
 * - FluentCommunity가 활성화된 경우에만 표시
 *
 * @package ACF_CSS_Style_Guide
 * @since 23.0.6
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// $options 변수가 없으면 기본값 사용
if ( ! isset( $options ) || ! is_array( $options ) ) {
    $options = array();
}

// Fluent Community 옵션 로드
$fc_options = $options['fluent_community'] ?? array();
?>

<div class="jj-section-global" id="jj-section-fluent-community">
    <h2 class="jj-section-title">
        <span class="dashicons dashicons-admin-comments" style="color: #5c6ac4; margin-right: 8px;"></span>
        <?php _e( 'Fluent Community', 'acf-css-really-simple-style-management-center' ); ?>
    </h2>
    <p class="description">
        <?php _e( 'Fluent Community 포럼, 피드, 그룹의 스타일을 통합 관리합니다. 게시물, 댓글, 알림 등의 색상을 조정할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
    </p>

    <div class="jj-fc-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">

        <!-- 피드 & 게시물 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '피드 & 게시물', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '게시물 카드 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="fluent_community[post][card_bg]"
                       value="<?php echo esc_attr( $fc_options['post']['card_bg'] ?? '#ffffff' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '게시물 테두리', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="fluent_community[post][border_color]"
                       value="<?php echo esc_attr( $fc_options['post']['border_color'] ?? '#e2e8f0' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '좋아요 버튼 활성 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="fluent_community[post][like_active]"
                       value="<?php echo esc_attr( $fc_options['post']['like_active'] ?? '#e74c3c' ); ?>" />
            </div>
        </fieldset>

        <!-- 댓글 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '댓글', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '댓글 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="fluent_community[comment][background]"
                       value="<?php echo esc_attr( $fc_options['comment']['background'] ?? '#f8fafc' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '작성자 이름 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="fluent_community[comment][author_color]"
                       value="<?php echo esc_attr( $fc_options['comment']['author_color'] ?? '#5c6ac4' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '답글 버튼 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="fluent_community[comment][reply_btn]"
                       value="<?php echo esc_attr( $fc_options['comment']['reply_btn'] ?? '#5c6ac4' ); ?>" />
            </div>
        </fieldset>

        <!-- 그룹 & 알림 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '그룹 & 알림', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '그룹 헤더 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="fluent_community[group][header_bg]"
                       value="<?php echo esc_attr( $fc_options['group']['header_bg'] ?? '#5c6ac4' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '알림 배지 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="fluent_community[notification][badge_bg]"
                       value="<?php echo esc_attr( $fc_options['notification']['badge_bg'] ?? '#e74c3c' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '멘션 하이라이트', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="fluent_community[notification][mention_bg]"
                       value="<?php echo esc_attr( $fc_options['notification']['mention_bg'] ?? '#fff3cd' ); ?>" />
            </div>
        </fieldset>

    </div>
</div>
