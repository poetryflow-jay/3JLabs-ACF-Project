<?php
/**
 * [v1.9.0-beta1] '스포크' UI: WooCommerce '컨텍스트' 제어
 * - [신규] 깨진 이미지 아이콘을 텍스트('WC')로 교체
 * - [수정] v1.8.1-alpha의 탭 UI 및 제어판 계승
 */
if ( ! defined( 'ABSPATH' ) ) exit; // 직접 접근 차단

// v1.8.1-alpha: 'contexts' DB 경로 확장
$wc_contexts = $options['contexts']['woocommerce'] ?? array();
$wc_single = $wc_contexts['single_product'] ?? array();
$wc_archive = $wc_contexts['product_archive'] ?? array();
$wc_shop_kit = $wc_contexts['shop_kit'] ?? array();
$wc_korea = $wc_contexts['korea'] ?? array();

// v1.8.0-beta2: 타이포그래피 제어 UI를 위한 변수 로드
$font_weights = array('100'=>'100', '200'=>'200', '300'=>'300', '400'=>'400', '500'=>'500', '600'=>'600', '700'=>'700', '800'=>'800', '900'=>'900');
$font_styles = array('normal'=>'Normal', 'italic'=>'Italic');
$text_transforms = array('' => 'Default', 'none' => 'None', 'uppercase' => 'UPPERCASE', 'capitalize' => 'Capitalize', 'lowercase' => 'lowercase');
?>

<div class="jj-section-spoke" id="jj-section-spoke-wc">
    <h2>
        <span class="jj-spoke-icon-text">WC</span>
        WooCommerce (컨텍스트 제어)
    </h2>
    <p class="description">
        'WooCommerce' 및 관련 '스포크'(Shop Kit, Woo-K 등)의 특정 영역(컨텍스트) 스타일을 '장악'합니다.
    </p>

    <div class="jj-tabs-container">
        <div class="jj-tabs-nav">
            <button type="button" class="jj-tab-button is-active" data-tab="wc-single">
                <?php _e( '개별 상품', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" class="jj-tab-button" data-tab="wc-archive">
                <?php _e( '상점 / 아카이브', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" class="jj-tab-button" data-tab="wc-shop-kit">
                <?php _e( 'Kadence Shop Kit', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" class="jj-tab-button" data-tab="wc-korea">
                <?php _e( 'WooCommerce K', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
        </div>

        <div class="jj-tab-content is-active" data-tab-content="wc-single">
            
            <fieldset class="jj-fieldset-group">
                <legend><?php _e( '상품명 (Product Title)', 'acf-css-really-simple-style-management-center' ); ?></legend>
                
                <div class="jj-control-group jj-control-toggle">
                    <label for="jj-wc-single-title-override"><?php _e( '타이포그래피 재정의', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="checkbox" id="jj-wc-single-title-override" class="jj-data-field jj-toggle-switch" data-setting-key="contexts[woocommerce][single_product][title_override]" data-toggle-target="#jj-wc-single-title-controls" value="1" <?php checked( $wc_single['title_override'] ?? '', '1' ); ?> />
                    <p class="description"><?php _e( '기본값: \'2. 전역 타이포그래피\'의 H1 스타일을 따름', 'acf-css-really-simple-style-management-center' ); ?></p>
                </div>
                
                <div class="jj-toggled-controls" id="jj-wc-single-title-controls">
                    <?php 
                    $tag = 'wc_single_title'; $settings = $wc_single['title_typography'] ?? array();
                    $prefix = "contexts[woocommerce][single_product][title_typography]";
                    $font_family = $settings['font_family'] ?? ''; $font_weight = $settings['font_weight'] ?? '700'; $font_style = $settings['font_style'] ?? 'normal'; $font_size_desktop = $settings['font_size']['desktop'] ?? ''; $font_size_tablet = $settings['font_size']['tablet'] ?? ''; $font_size_mobile = $settings['font_size']['mobile'] ?? ''; $line_height = $settings['line_height'] ?? ''; $letter_spacing = $settings['letter_spacing'] ?? ''; $text_transform = $settings['text_transform'] ?? '';
                    ?>
                    <div class="jj-contextual-typography-controls">
                        <div class="jj-control-group"><label for="jj-font-family-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Font Family', 'acf-css-really-simple-style-management-center' ); ?></label><input type="text" id="jj-font-family-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[font_family]" value="<?php echo esc_attr( $font_family ); ?>" placeholder="Inter, 'Noto Sans KR', sans-serif" /></div>
                        <div class="jj-control-group"><label for="jj-font-weight-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Weight', 'acf-css-really-simple-style-management-center' ); ?></label><select id="jj-font-weight-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[font_weight]"><?php foreach ( $font_weights as $weight => $label ) : ?><option value="<?php echo esc_attr( $weight ); ?>" <?php selected( $font_weight, $weight ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></div>
                        <div class="jj-control-group"><label for="jj-font-style-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Style', 'acf-css-really-simple-style-management-center' ); ?></label><select id="jj-font-style-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[font_style]"><?php foreach ( $font_styles as $style => $label ) : ?><option value="<?php echo esc_attr( $style ); ?>" <?php selected( $font_style, $style ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></div>
                        <div class="jj-control-group"><label for="jj-line-height-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Line Height (em)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" step="0.1" id="jj-line-height-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[line_height]" value="<?php echo esc_attr( $line_height ); ?>" placeholder="예: 1.5" /></div>
                        <div class="jj-control-group"><label for="jj-letter-spacing-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Letter Spacing (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" step="0.1" id="jj-letter-spacing-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[letter_spacing]" value="<?php echo esc_attr( $letter_spacing ); ?>" placeholder="예: -0.1" /></div>
                        <div class="jj-control-group"><label for="jj-text-transform-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Transform', 'acf-css-really-simple-style-management-center' ); ?></label><select id="jj-text-transform-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[text_transform]"><?php foreach ( $text_transforms as $value => $label ) : ?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( $text_transform, $value ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></div>
                        <div class="jj-control-group jj-responsive-control"><div class="jj-responsive-tabs"><button type="button" class="jj-responsive-tab-button is-active" data-device="desktop">D</button><button type="button" class="jj-responsive-tab-button" data-device="tablet">T</button><button type="button" class="jj-responsive-tab-button" data-device="mobile">M</button></div><div class="jj-responsive-tab-content is-active" data-device="desktop"><label for="jj-font-size-<?php echo esc_attr( $tag ); ?>-desktop"><?php _e( 'Size (Desktop)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" id="jj-font-size-<?php echo esc_attr( $tag ); ?>-desktop" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[font_size][desktop]" value="<?php echo esc_attr( $font_size_desktop ); ?>" placeholder="예: 32" /></div><div class="jj-responsive-tab-content" data-device="tablet"><label for="jj-font-size-<?php echo esc_attr( $tag ); ?>-tablet"><?php _e( 'Size (Tablet)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" id="jj-font-size-<?php echo esc_attr( $tag ); ?>-tablet" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[font_size][tablet]" value="<?php echo esc_attr( $font_size_tablet ); ?>" placeholder="예: 28" /></div><div class="jj-responsive-tab-content" data-device="mobile"><label for="jj-font-size-<?php echo esc_attr( $tag ); ?>-mobile"><?php _e( 'Size (Mobile)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" id="jj-font-size-<?php echo esc_attr( $tag ); ?>-mobile" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[font_size][mobile]" value="<?php echo esc_attr( $font_size_mobile ); ?>" placeholder="예: 24" /></div></div>
                    </div>
                </div>

                <div class="jj-control-group jj-control-toggle jj-toggle-sub">
                    <label for="jj-wc-single-title-color-override"><?php _e( '색상 재정의', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="checkbox" id="jj-wc-single-title-color-override" class="jj-data-field jj-toggle-switch" data-setting-key="contexts[woocommerce][single_product][title_color_override]" data-toggle-target="#jj-wc-single-title-color-controls" value="1" <?php checked( $wc_single['title_color_override'] ?? '', '1' ); ?> />
                    <p class="description"><?php _e( '기본값: 테마의 H1 색상을 따름', 'acf-css-really-simple-style-management-center' ); ?></p>
                </div>
                
                <div class="jj-toggled-controls" id="jj-wc-single-title-color-controls">
                    <div class="jj-control-group"><label><?php _e( '색상 (Normal & Hover)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group"><input type="text" class="jj-color-field jj-data-field" data-setting-key="contexts[woocommerce][single_product][title_color]" value="<?php echo esc_attr( $wc_single['title_color'] ?? '' ); ?>" /><input type="text" class="jj-color-field jj-data-field" data-setting-key="contexts[woocommerce][single_product][title_color_hover]" value="<?php echo esc_attr( $wc_single['title_color_hover'] ?? '' ); ?>" /></div></div>
                </div>
            </fieldset>

            <fieldset class="jj-fieldset-group">
                <legend><?php _e( '가격 (Product Price)', 'acf-css-really-simple-style-management-center' ); ?></legend>
                
                <div class="jj-control-group jj-control-toggle">
                    <label for="jj-wc-single-price-override"><?php _e( '타이포그래피 재정의', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="checkbox" id="jj-wc-single-price-override" class="jj-data-field jj-toggle-switch" data-setting-key="contexts[woocommerce][single_product][price_override]" data-toggle-target="#jj-wc-single-price-controls" value="1" <?php checked( $wc_single['price_override'] ?? '', '1' ); ?> />
                    <p class="description"><?php _e( '기본값: \'2. 전역 타이포그래피\'의 P 스타일을 따름', 'acf-css-really-simple-style-management-center' ); ?></p>
                </div>

                <div class="jj-toggled-controls" id="jj-wc-single-price-controls">
                    <?php 
                    $tag = 'wc_single_price'; $settings = $wc_single['price_typography'] ?? array();
                    $prefix = "contexts[woocommerce][single_product][price_typography]";
                    $font_family = $settings['font_family'] ?? ''; $font_weight = $settings['font_weight'] ?? '400'; $font_style = $settings['font_style'] ?? 'normal'; $font_size_desktop = $settings['font_size']['desktop'] ?? ''; $font_size_tablet = $settings['font_size']['tablet'] ?? ''; $font_size_mobile = $settings['font_size']['mobile'] ?? ''; $line_height = $settings['line_height'] ?? ''; $letter_spacing = $settings['letter_spacing'] ?? ''; $text_transform = $settings['text_transform'] ?? '';
                    ?>
                     <div class="jj-contextual-typography-controls">
                        <div class="jj-control-group"><label for="jj-font-family-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Font Family', 'acf-css-really-simple-style-management-center' ); ?></label><input type="text" id="jj-font-family-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[font_family]" value="<?php echo esc_attr( $font_family ); ?>" placeholder="Inter, 'Noto Sans KR', sans-serif" /></div>
                        <div class="jj-control-group"><label for="jj-font-weight-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Weight', 'acf-css-really-simple-style-management-center' ); ?></label><select id="jj-font-weight-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[font_weight]"><?php foreach ( $font_weights as $weight => $label ) : ?><option value="<?php echo esc_attr( $weight ); ?>" <?php selected( $font_weight, $weight ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></div>
                        <div class="jj-control-group"><label for="jj-font-style-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Style', 'acf-css-really-simple-style-management-center' ); ?></label><select id="jj-font-style-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[font_style]"><?php foreach ( $font_styles as $style => $label ) : ?><option value="<?php echo esc_attr( $style ); ?>" <?php selected( $font_style, $style ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></div>
                        <div class="jj-control-group"><label for="jj-line-height-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Line Height (em)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" step="0.1" id="jj-line-height-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[line_height]" value="<?php echo esc_attr( $line_height ); ?>" placeholder="예: 1.5" /></div>
                        <div class="jj-control-group"><label for="jj-letter-spacing-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Letter Spacing (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" step="0.1" id="jj-letter-spacing-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[letter_spacing]" value="<?php echo esc_attr( $letter_spacing ); ?>" placeholder="예: -0.1" /></div>
                        <div class="jj-control-group"><label for="jj-text-transform-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Transform', 'acf-css-really-simple-style-management-center' ); ?></label><select id="jj-text-transform-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[text_transform]"><?php foreach ( $text_transforms as $value => $label ) : ?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( $text_transform, $value ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></div>
                        <div class="jj-control-group jj-responsive-control"><div class="jj-responsive-tabs"><button type="button" class="jj-responsive-tab-button is-active" data-device="desktop">D</button><button type="button" class="jj-responsive-tab-button" data-device="tablet">T</button><button type="button" class="jj-responsive-tab-button" data-device="mobile">M</button></div><div class="jj-responsive-tab-content is-active" data-device="desktop"><label for="jj-font-size-<?php echo esc_attr( $tag ); ?>-desktop"><?php _e( 'Size (Desktop)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" id="jj-font-size-<?php echo esc_attr( $tag ); ?>-desktop" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[font_size][desktop]" value="<?php echo esc_attr( $font_size_desktop ); ?>" placeholder="예: 22" /></div><div class="jj-responsive-tab-content" data-device="tablet"><label for="jj-font-size-<?php echo esc_attr( $tag ); ?>-tablet"><?php _e( 'Size (Tablet)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" id="jj-font-size-<?php echo esc_attr( $tag ); ?>-tablet" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[font_size][tablet]" value="<?php echo esc_attr( $font_size_tablet ); ?>" placeholder="예: 20" /></div><div class="jj-responsive-tab-content" data-device="mobile"><label for="jj-font-size-<?php echo esc_attr( $tag ); ?>-mobile"><?php _e( 'Size (Mobile)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" id="jj-font-size-<?php echo esc_attr( $tag ); ?>-mobile" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[font_size][mobile]" value="<?php echo esc_attr( $font_size_mobile ); ?>" placeholder="예: 18" /></div></div>
                    </div>
                </div>

                <div class="jj-control-group jj-control-toggle jj-toggle-sub">
                    <label for="jj-wc-single-price-color-override"><?php _e( '색상 재정의', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="checkbox" id="jj-wc-single-price-color-override" class="jj-data-field jj-toggle-switch" data-setting-key="contexts[woocommerce][single_product][price_color_override]" data-toggle-target="#jj-wc-single-price-color-controls" value="1" <?php checked( $wc_single['price_color_override'] ?? '', '1' ); ?> />
                    <p class="description"><?php _e( '기본값: 테마의 가격 색상을 따름', 'acf-css-really-simple-style-management-center' ); ?></p>
                </div>
                
                <div class="jj-toggled-controls" id="jj-wc-single-price-color-controls">
                    <div class="jj-control-group"><label><?php _e( '색상 (Normal)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group" style="grid-template-columns: 1fr;"><input type="text" class="jj-color-field jj-data-field" data-setting-key="contexts[woocommerce][single_product][price_color]" value="<?php echo esc_attr( $wc_single['price_color'] ?? '' ); ?>" /></div></div>
                </div>
            </fieldset>

            <fieldset class="jj-fieldset-group">
                <legend><?php _e( '\'장바구니 담기\' 버튼 (Add to Cart)', 'acf-css-really-simple-style-management-center' ); ?></legend>
                
                <div class="jj-control-group">
                    <label for="jj-wc-single-button-text"><?php _e( '버튼 텍스트 (Text)', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" id="jj-wc-single-button-text" class="jj-data-field" data-setting-key="contexts[woocommerce][single_product][add_to_cart_text]" value="<?php echo esc_attr( $wc_single['add_to_cart_text'] ?? '' ); ?>" placeholder="기본값: 담기" />
                    <p class="description"><?php _e( '상품 페이지의 \'담기\' 버튼 텍스트를 변경합니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                </div>
                 
                <div class="jj-control-group jj-control-toggle">
                    <label for="jj-wc-single-button-override"><?php _e( '버튼 스타일 재정의', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="checkbox" id="jj-wc-single-button-override" class="jj-data-field jj-toggle-switch" data-setting-key="contexts[woocommerce][single_product][button_override]" data-toggle-target="#jj-wc-single-button-controls" value="1" <?php checked( $wc_single['button_override'] ?? '', '1' ); ?> />
                    <p class="description"><?php _e( '기본값: \'3. 전역 버튼\'의 Primary Button 스타일을 따름', 'acf-css-really-simple-style-management-center' ); ?></p>
                </div>
                
                <div class="jj-toggled-controls" id="jj-wc-single-button-controls">
                    <?php 
                    $btn_options = $wc_single['button_style'] ?? array();
                    $prefix = "contexts[woocommerce][single_product][button_style]";
                    ?>
                    <fieldset class="jj-control-group jj-fieldset-group"><legend><?php _e( 'Colors', 'acf-css-really-simple-style-management-center' ); ?></legend><div class="jj-control-group"><label><?php _e( '배경 색상 (Normal & Hover)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group"><input type="text" class="jj-color-field jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[background_color]" value="<?php echo esc_attr( $btn_options['background_color'] ?? '' ); ?>" /><input type="text" class="jj-color-field jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[background_color_hover]" value="<?php echo esc_attr( $btn_options['background_color_hover'] ?? '' ); ?>" /></div></div><div class="jj-control-group"><label><?php _e( '텍스트 색상 (Normal & Hover)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group"><input type="text" class="jj-color-field jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[text_color]" value="<?php echo esc_attr( $btn_options['text_color'] ?? '' ); ?>" /><input type="text" class="jj-color-field jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[text_color_hover]" value="<?php echo esc_attr( $btn_options['text_color_hover'] ?? '' ); ?>" /></div></div><div class="jj-control-group"><label><?php _e( '테두리 색상 (Normal & Hover)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group"><input type="text" class="jj-color-field jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[border_color]" value="<?php echo esc_attr( $btn_options['border_color'] ?? '' ); ?>" /><input type="text" class="jj-color-field jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[border_color_hover]" value="<?php echo esc_attr( $btn_options['border_color_hover'] ?? '' ); ?>" /></div></div></fieldset>
                    <fieldset class="jj-control-group jj-fieldset-group jj-grid-2-col"><legend><?php _e( 'Layout', 'acf-css-really-simple-style-management-center' ); ?></legend><div class="jj-control-group"><label for="jj-wc-btn-border-radius"><?php _e( 'Border Radius (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" id="jj-wc-btn-border-radius" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[border_radius]" value="<?php echo esc_attr( $btn_options['border_radius'] ?? '' ); ?>" placeholder="예: 4" /></div><div class="jj-control-group"><label><?php _e( 'Padding (px)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-dimensions-group"><input type="number" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[padding][top]" value="<?php echo esc_attr( $btn_options['padding']['top'] ?? '' ); ?>" title="Top" placeholder="Top"><input type="number" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[padding][right]" value="<?php echo esc_attr( $btn_options['padding']['right'] ?? '' ); ?>" title="Right" placeholder="Right"><input type="number" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[padding][bottom]" value="<?php echo esc_attr( $btn_options['padding']['bottom'] ?? '' ); ?>" title="Bottom" placeholder="Bottom"><input type="number" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[padding][left]" value="<?php echo esc_attr( $btn_options['padding']['left'] ?? '' ); ?>" title="Left" placeholder="Left"></div></div></fieldset>
                    <fieldset class="jj-control-group jj-fieldset-group"><legend><?php _e( 'Button Shadow', 'acf-css-really-simple-style-management-center' ); ?></legend><div class="jj-shadow-group"><div class="jj-control-group"><label><?php _e( 'Color', 'acf-css-really-simple-style-management-center' ); ?></label><input type="text" class="jj-color-field jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[shadow][color]" value="<?php echo esc_attr( $btn_options['shadow']['color'] ?? '' ); ?>" /></div><div class="jj-control-group"><label><?php _e( 'X Offset (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[shadow][x]" value="<?php echo esc_attr( $btn_options['shadow']['x'] ?? '' ); ?>" placeholder="0"></div><div class="jj-control-group"><label><?php _e( 'Y Offset (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[shadow][y]" value="<?php echo esc_attr( $btn_options['shadow']['y'] ?? '' ); ?>" placeholder="10"></div><div class="jj-control-group"><label><?php _e( 'Blur (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[shadow][blur]" value="<?php echo esc_attr( $btn_options['shadow']['blur'] ?? '' ); ?>" placeholder="15"></div><div class="jj-control-group"><label><?php _e( 'Spread (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="<?php echo esc_attr( $prefix ); ?>[shadow][spread]" value="<?php echo esc_attr( $btn_options['shadow']['spread'] ?? '' ); ?>" placeholder="-5"></div></div></fieldset>
                </div>
            </fieldset>
        </div>

        <div class="jj-tab-content" data-tab-content="wc-archive">
            <p class="description"><?php _e( 'v1.9.0 정식 버전에서 \'상품 아카이브\' 페이지의 상세 제어 기능이 추가될 예정입니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
        </div>
        
        <div class="jj-tab-content" data-tab-content="wc-shop-kit">
            <fieldset class="jj-fieldset-group">
                <legend><?php _e( 'Kadence Shop Kit 스타일', 'acf-css-really-simple-style-management-center' ); ?></legend>
                <div class="jj-style-guide-grid jj-grid-4-col">
                    
                    <div class="jj-control-group jj-color-card">
                        <label for="jj-wc-shop-kit-quick-view-bg"><?php _e( 'Quick View 버튼 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                        <input type="text" 
                               id="jj-wc-shop-kit-quick-view-bg" 
                               class="jj-color-field jj-data-field" 
                               data-setting-key="contexts[woocommerce][shop_kit][quick_view_bg]" 
                               value="<?php echo esc_attr( $wc_shop_kit['quick_view_bg'] ?? '' ); ?>">
                        <div class="jj-color-preview"></div>
                        <p class="description">
                            'Kadence Shop Kit'의 '빠른 보기(Quick View)' 버튼 배경색을 '장악'합니다. (기본값: 브랜드 팔레트)
                        </p>
                    </div>

                </div>
            </fieldset>
        </div>
        
        <div class="jj-tab-content" data-tab-content="wc-korea">
            <fieldset class="jj-fieldset-group">
                <legend><?php _e( 'WooCommerce K (단비스토어) 스타일', 'acf-css-really-simple-style-management-center' ); ?></legend>
                <div class="jj-style-guide-grid jj-grid-4-col">
                    
                    <div class="jj-control-group jj-color-card">
                        <label for="jj-wc-korea-naver-pay"><?php _e( '네이버페이 버튼 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                        <input type="text" 
                               id="jj-wc-korea-naver-pay" 
                               class="jj-color-field jj-data-field" 
                               data-setting-key="contexts[woocommerce][korea][naver_pay_bg]" 
                               value="<?php echo esc_attr( $wc_korea['naver_pay_bg'] ?? '' ); ?>">
                        <div class="jj-color-preview"></div>
                        <p class="description">
                            'WooCommerce K'가 추가하는 '네이버페이' 버튼의 배경색을 '장악'합니다.
                        </p>
                    </div>

                    <div class="jj-control-group jj-color-card">
                        <label for="jj-wc-korea-kakao-pay"><?php _e( '카카오페이 버튼 배경', 'acf-css-really-simple-style-management-center' ); ?></label>
                        <input type="text" 
                               id="jj-wc-korea-kakao-pay" 
                               class="jj-color-field jj-data-field" 
                               data-setting-key="contexts[woocommerce][korea][kakao_pay_bg]" 
                               value="<?php echo esc_attr( $wc_korea['kakao_pay_bg'] ?? '' ); ?>">
                        <div class="jj-color-preview"></div>
                        <p class="description">
                            'WooCommerce K'가 추가하는 '카카오페이' 버튼의 배경색을 '장악'합니다.
                        </p>
                    </div>

                </div>
            </fieldset>
        </div>

    </div>
</div>