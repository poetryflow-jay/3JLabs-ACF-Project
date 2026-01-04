<?php
/**
 * [v3.3 '제련'] '4. 폼' 섹션 (라벨만)
 * - 사이트 관리자의 설계에 따라 전역 폼 라벨 제어 UI
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// $options 변수가 없으면 기본값 사용
if ( ! isset( $options ) || ! is_array( $options ) ) {
    $options = array();
}

// '제련': 'forms' 배열 로드
$forms_options = $options['forms'] ?? array();
$label_options = $forms_options['label'] ?? array();

// '타이포그래피' 설정 (라벨 기본값용)
$font_weights = array('100'=>'100', '200'=>'200', '300'=>'300', '400'=>'400', '500'=>'500', '600'=>'600', '700'=>'700', '800'=>'800', '900'=>'900');
$font_styles = array('normal'=>'Normal', 'italic'=>'Italic');
$text_transforms = array('' => 'Default', 'none' => 'None', 'uppercase' => 'UPPERCASE', 'capitalize' => 'Capitalize', 'lowercase' => 'lowercase');
?>

<div class="jj-section-global" id="jj-section-forms">
    <h2 class="jj-section-title">
        <?php
        $jj_section_index = function_exists( 'jj_style_guide_section_index' ) ? jj_style_guide_section_index( 'forms' ) : null;
        if ( $jj_section_index ) :
            ?>
            <span class="jj-section-index"><?php echo intval( $jj_section_index ); ?>.</span>
        <?php endif; ?>
        <?php echo esc_html( jj_style_guide_text( 'section_forms_title', __( '폼 (Forms)', 'acf-css-really-simple-style-management-center' ) ) ); ?>
    </h2>
    <p class="description">
        <?php
        echo wp_kses_post(
            jj_style_guide_text(
                'section_forms_description',
                __(
                    '사이트 전역의 폼 라벨(Label) 스타일을 정갈하게 맞추는 영역입니다. 여기에서 정한 값은 <strong>Advanced Members</strong>, <strong>ACF Forms</strong>, <strong>FluentForm</strong> 등 폼을 사용하는 스포크에 CSS 변수(--jj-form-label-...)로 전달되어, 회원가입·문의·결제 화면 등에서도 자연스럽게 같은 톤으로 표현되도록 돕습니다.',
                    'acf-css-really-simple-style-management-center'
                )
            )
        );
        ?>
    </p>

    <div class="jj-tabs-container">
        <div class="jj-tab-content is-active">
            <!-- [v22.4.7] 퀵 네비게이션 -->
            <div class="jj-tab-quick-nav" style="margin-bottom: 20px;">
                <ul class="jj-tab-quick-nav-list">
                    <li class="jj-tab-quick-nav-item"><a href="#jj-form-label-typography" class="jj-tab-quick-nav-link"><?php _e( '타이포그래피', 'acf-css-really-simple-style-management-center' ); ?></a></li>
                    <li class="jj-tab-quick-nav-item"><a href="#jj-form-label-colors" class="jj-tab-quick-nav-link"><?php _e( '색상', 'acf-css-really-simple-style-management-center' ); ?></a></li>
                </ul>
            </div>
            
            <p class="description" style="margin-bottom: 25px;">
                <?php _e( '모든 폼의 <code>&lt;label&gt;</code> 태그 스타일을 제어합니다. (기본값: \'2. 타이포그래피\'의 \'P\' 스타일 일부를 따름)', 'acf-css-really-simple-style-management-center' ); ?>
            </p>
            
            <div class="jj-contextual-typography-controls jj-section-subsection" id="jj-form-label-typography" style="grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));">
                
                <div class="jj-control-group">
                    <label for="jj-form-label-font-weight">
                        <?php _e( 'Weight', 'acf-css-really-simple-style-management-center' ); ?>
                        <span class="dashicons dashicons-editor-help" style="font-size: 16px; color: #2271b1; cursor: help; margin-left: 4px;" data-tooltip="form-label-style"></span>
                    </label>
                    <select id="jj-form-label-font-weight" 
                            class="jj-data-field" 
                            data-setting-key="forms[label][font_weight]"
                            data-tooltip="폼 레이블의 폰트 굵기를 설정합니다.">
                        <option value=""><?php _e( '기본값 (P)', 'acf-css-really-simple-style-management-center' ); ?></option>
                        <?php foreach ( $font_weights as $weight => $label ) : ?>
                            <option value="<?php echo esc_attr( $weight ); ?>" <?php selected( $label_options['font_weight'] ?? '', $weight ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="jj-control-group">
                    <label for="jj-form-label-font-style"><?php _e( 'Style', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <select id="jj-form-label-font-style" class="jj-data-field" data-setting-key="forms[label][font_style]">
                        <option value=""><?php _e( '기본값 (P)', 'acf-css-really-simple-style-management-center' ); ?></option>
                        <?php foreach ( $font_styles as $style => $label ) : ?>
                            <option value="<?php echo esc_attr( $style ); ?>" <?php selected( $label_options['font_style'] ?? '', $style ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="jj-control-group">
                    <label for="jj-form-label-text-transform"><?php _e( 'Transform', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <select id="jj-form-label-text-transform" class="jj-data-field" data-setting-key="forms[label][text_transform]">
                        <?php foreach ( $text_transforms as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $label_options['text_transform'] ?? '', $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="jj-control-group">
                    <label for="jj-form-label-font-size"><?php _e( 'Size (px)', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="number" 
                           id="jj-form-label-font-size" 
                           class="jj-data-field" 
                           data-setting-key="forms[label][font_size]"
                           value="<?php echo esc_attr( $label_options['font_size'] ?? '' ); ?>"
                           placeholder="예: 15" />
                </div>

                <div class="jj-control-group jj-color-card jj-section-subsection" id="jj-form-label-colors" style="padding: 10px;">
                    <label for="jj-form-label-text-color"><?php _e( '텍스트 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-form-label-text-color" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="forms[label][text_color]" 
                           value="<?php echo esc_attr( $label_options['text_color'] ?? '' ); ?>">
                    <div class="jj-color-preview" style="height: 30px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
