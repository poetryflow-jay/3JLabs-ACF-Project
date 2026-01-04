<?php
/**
 * [v23.0.6] LearnDash LMS 스타일 섹션
 * - LearnDash가 활성화된 경우에만 표시
 *
 * @package ACF_CSS_Style_Guide
 * @since 23.0.6
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// $options 변수가 없으면 기본값 사용
if ( ! isset( $options ) || ! is_array( $options ) ) {
    $options = array();
}

// LearnDash 옵션 로드
$ld_options = $options['learndash'] ?? array();
?>

<div class="jj-section-global" id="jj-section-learndash">
    <h2 class="jj-section-title">
        <span class="dashicons dashicons-welcome-learn-more" style="color: #00a0d2; margin-right: 8px;"></span>
        <?php _e( 'LearnDash LMS', 'acf-css-really-simple-style-management-center' ); ?>
    </h2>
    <p class="description">
        <?php _e( 'LearnDash 강좌, 레슨, 퀴즈 페이지의 스타일을 통합 관리합니다. 진행률 표시, 버튼, 배지 등의 색상을 조정할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
    </p>

    <div class="jj-learndash-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">

        <!-- 진행률 바 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '진행률 바', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '진행률 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="learndash[progress][background]"
                       value="<?php echo esc_attr( $ld_options['progress']['background'] ?? '#e9ecef' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '진행률 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="learndash[progress][fill_color]"
                       value="<?php echo esc_attr( $ld_options['progress']['fill_color'] ?? '#00a0d2' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '완료 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="learndash[progress][complete_color]"
                       value="<?php echo esc_attr( $ld_options['progress']['complete_color'] ?? '#28a745' ); ?>" />
            </div>
        </fieldset>

        <!-- 강좌 카드 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '강좌 카드', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '카드 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="learndash[course_card][background]"
                       value="<?php echo esc_attr( $ld_options['course_card']['background'] ?? '#ffffff' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '카드 테두리', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="learndash[course_card][border_color]"
                       value="<?php echo esc_attr( $ld_options['course_card']['border_color'] ?? '#e2e8f0' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '제목 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="learndash[course_card][title_color]"
                       value="<?php echo esc_attr( $ld_options['course_card']['title_color'] ?? '#1e293b' ); ?>" />
            </div>
        </fieldset>

        <!-- 퀴즈 & 배지 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '퀴즈 & 배지', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '정답 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="learndash[quiz][correct_color]"
                       value="<?php echo esc_attr( $ld_options['quiz']['correct_color'] ?? '#28a745' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '오답 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="learndash[quiz][incorrect_color]"
                       value="<?php echo esc_attr( $ld_options['quiz']['incorrect_color'] ?? '#dc3545' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '배지 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="learndash[badge][background]"
                       value="<?php echo esc_attr( $ld_options['badge']['background'] ?? '#ffc107' ); ?>" />
            </div>
        </fieldset>

    </div>
</div>
