<?php
/**
 * [v23.0.6] WooCommerce 스타일 섹션
 * - WooCommerce가 활성화된 경우에만 표시
 *
 * @package ACF_CSS_Style_Guide
 * @since 23.0.6
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// $options 변수가 없으면 기본값 사용
if ( ! isset( $options ) || ! is_array( $options ) ) {
    $options = array();
}

// WooCommerce 옵션 로드
$wc_options = $options['woocommerce'] ?? array();
?>

<div class="jj-section-global" id="jj-section-woocommerce">
    <h2 class="jj-section-title">
        <span class="dashicons dashicons-cart" style="color: #7f54b3; margin-right: 8px;"></span>
        <?php _e( 'WooCommerce', 'acf-css-really-simple-style-management-center' ); ?>
    </h2>
    <p class="description">
        <?php _e( 'WooCommerce 상품, 장바구니, 결제 페이지의 스타일을 통합 관리합니다. 버튼, 가격 표시, 알림 메시지 등의 색상과 스타일을 조정할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
    </p>

    <div class="jj-woocommerce-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">

        <!-- 상품 페이지 스타일 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '상품 페이지', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '가격 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="woocommerce[product][price_color]"
                       value="<?php echo esc_attr( $wc_options['product']['price_color'] ?? '#7f54b3' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '할인가 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="woocommerce[product][sale_price_color]"
                       value="<?php echo esc_attr( $wc_options['product']['sale_price_color'] ?? '#e74c3c' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '세일 배지 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="woocommerce[product][sale_badge_bg]"
                       value="<?php echo esc_attr( $wc_options['product']['sale_badge_bg'] ?? '#e74c3c' ); ?>" />
            </div>
        </fieldset>

        <!-- 장바구니 버튼 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '장바구니 버튼', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '배경 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="woocommerce[cart_button][background]"
                       value="<?php echo esc_attr( $wc_options['cart_button']['background'] ?? '#7f54b3' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '텍스트 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="woocommerce[cart_button][text_color]"
                       value="<?php echo esc_attr( $wc_options['cart_button']['text_color'] ?? '#ffffff' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '호버 배경 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="woocommerce[cart_button][background_hover]"
                       value="<?php echo esc_attr( $wc_options['cart_button']['background_hover'] ?? '#6b4599' ); ?>" />
            </div>
        </fieldset>

        <!-- 알림 메시지 -->
        <fieldset class="jj-control-group jj-fieldset-group">
            <legend><?php _e( '알림 메시지', 'acf-css-really-simple-style-management-center' ); ?></legend>

            <div class="jj-control-group">
                <label><?php _e( '성공 메시지 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="woocommerce[notices][success_bg]"
                       value="<?php echo esc_attr( $wc_options['notices']['success_bg'] ?? '#d4edda' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '오류 메시지 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="woocommerce[notices][error_bg]"
                       value="<?php echo esc_attr( $wc_options['notices']['error_bg'] ?? '#f8d7da' ); ?>" />
            </div>

            <div class="jj-control-group">
                <label><?php _e( '정보 메시지 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" class="jj-color-field jj-data-field"
                       data-setting-key="woocommerce[notices][info_bg]"
                       value="<?php echo esc_attr( $wc_options['notices']['info_bg'] ?? '#cce5ff' ); ?>" />
            </div>
        </fieldset>

    </div>
</div>
