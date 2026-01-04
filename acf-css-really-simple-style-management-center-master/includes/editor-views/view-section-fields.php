<?php
/**
 * [v3.3 '제련'] '5. 필드' 섹션 (입력 필드만)
 * - 사이트 관리자의 설계에 따라 전역 입력 필드 제어 UI
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// $options 변수가 없으면 기본값 사용
if ( ! isset( $options ) || ! is_array( $options ) ) {
    $options = array();
}

// '제련': 'forms' 배열 로드
$forms_options = $options['forms'] ?? array();
$field_options = $forms_options['field'] ?? array();
?>

<div class="jj-section-global" id="jj-section-fields">
    <h2 class="jj-section-title">
        <?php
        $jj_section_index = function_exists( 'jj_style_guide_section_index' ) ? jj_style_guide_section_index( 'fields' ) : null;
        if ( $jj_section_index ) :
            ?>
            <span class="jj-section-index"><?php echo intval( $jj_section_index ); ?>.</span>
        <?php endif; ?>
        <?php echo esc_html( jj_style_guide_text( 'section_fields_title', __( '필드 (Fields)', 'acf-css-really-simple-style-management-center' ) ) ); ?>
    </h2>
    <p class="description">
        <?php
        echo wp_kses_post(
            jj_style_guide_text(
                'section_fields_description',
                __(
                    '사이트 전역의 입력 필드(Input Fields) 스타일을 정갈하게 맞추는 영역입니다. 여기에서 정한 값은 <strong>Advanced Members</strong>, <strong>ACF Forms</strong>, <strong>FluentForm</strong> 등 폼을 사용하는 스포크에 CSS 변수(--jj-form-field-...)로 전달되어, 회원가입·문의·결제 화면 등에서도 자연스럽게 같은 톤으로 표현되도록 돕습니다.',
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
                    <li class="jj-tab-quick-nav-item"><a href="#jj-form-field-colors" class="jj-tab-quick-nav-link"><?php _e( 'Colors', 'acf-css-really-simple-style-management-center' ); ?></a></li>
                    <li class="jj-tab-quick-nav-item"><a href="#jj-form-field-layout" class="jj-tab-quick-nav-link"><?php _e( 'Layout', 'acf-css-really-simple-style-management-center' ); ?></a></li>
                    <li class="jj-tab-quick-nav-item"><a href="#jj-form-field-padding" class="jj-tab-quick-nav-link"><?php _e( 'Padding', 'acf-css-really-simple-style-management-center' ); ?></a></li>
                </ul>
            </div>
            <div class="jj-button-preview-grid">
                
                <div class="jj-button-preview" style="background: #f0f0f1; display: block; padding: 20px;">
                    <style id="jj-form-preview-style"></style> <div class="jj-form-preview-wrap">
                        <label class="jj-form-preview-label" for="jj-preview-input"><?php _e( '미리보기 라벨', 'acf-css-really-simple-style-management-center' ); ?></label>
                        <input class="jj-form-preview-input" type="text" id="jj-preview-input" placeholder="<?php _e( '입력 필드 미리보기...', 'acf-css-really-simple-style-management-center' ); ?>">
                        <p class="description" style="margin-top: 10px;"><?php _e( '이 필드는 \'활성화(Focus)\' 상태입니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                    </div>
                </div>
                
                <div class="jj-button-controls">
                    <fieldset class="jj-control-group jj-fieldset-group jj-section-subsection" id="jj-form-field-colors">
                        <legend><?php _e( 'Colors', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-style-guide-grid jj-grid-4-col" style="gap: 10px;">
                            <div class="jj-control-group"><label><?php _e( '배경', 'acf-css-really-simple-style-management-center' ); ?></label><input type="text" class="jj-color-field jj-data-field" data-setting-key="forms[field][background_color]" value="<?php echo esc_attr( $field_options['background_color'] ?? '' ); ?>" /></div>
                            <div class="jj-control-group"><label><?php _e( '텍스트', 'acf-css-really-simple-style-management-center' ); ?></label><input type="text" class="jj-color-field jj-data-field" data-setting-key="forms[field][text_color]" value="<?php echo esc_attr( $field_options['text_color'] ?? '' ); ?>" /></div>
                            <div class="jj-control-group"><label><?php _e( '테두리', 'acf-css-really-simple-style-management-center' ); ?></label><input type="text" class="jj-color-field jj-data-field" data-setting-key="forms[field][border_color]" value="<?php echo esc_attr( $field_options['border_color'] ?? '' ); ?>" /></div>
                            <div class="jj-control-group">
                                <label>
                                    <?php _e( '테두리 (Focus)', 'acf-css-really-simple-style-management-center' ); ?>
                                    <span class="dashicons dashicons-editor-help" style="font-size: 16px; color: #2271b1; cursor: help; margin-left: 4px;" data-tooltip="field-focus-ring"></span>
                                </label>
                                <input type="text" 
                                       class="jj-color-field jj-data-field" 
                                       data-setting-key="forms[field][border_color_focus]" 
                                       value="<?php echo esc_attr( $field_options['border_color_focus'] ?? '' ); ?>"
                                       data-tooltip="입력 필드에 포커스가 있을 때 표시되는 테두리 색상입니다. 접근성을 위해 권장됩니다." />
                            </div>
                        </div>
                    </fieldset>
                    
                    <fieldset class="jj-control-group jj-fieldset-group jj-grid-2-col jj-section-subsection" id="jj-form-field-layout">
                        <legend><?php _e( 'Layout', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-control-group">
                            <label for="jj-form-field-border-radius">
                                <?php _e( 'Border Radius (px)', 'acf-css-really-simple-style-management-center' ); ?>
                                <span class="dashicons dashicons-editor-help" style="font-size: 16px; color: #2271b1; cursor: help; margin-left: 4px;" data-tooltip="field-border-radius"></span>
                            </label>
                            <input type="number" 
                                   id="jj-form-field-border-radius" 
                                   class="jj-data-field" 
                                   data-setting-key="forms[field][border_radius]" 
                                   value="<?php echo esc_attr( $field_options['border_radius'] ?? '' ); ?>" 
                                   placeholder="예: 4"
                                   data-tooltip="입력 필드의 모서리 둥글기를 설정합니다. 0px는 사각형, 값이 클수록 더 둥글어집니다." />
                        </div>
                        <div class="jj-control-group">
                            <label for="jj-form-field-border-width"><?php _e( 'Border Width (px)', 'acf-css-really-simple-style-management-center' ); ?></label>
                            <input type="number" id="jj-form-field-border-width" class="jj-data-field" data-setting-key="forms[field][border_width]" value="<?php echo esc_attr( $field_options['border_width'] ?? '' ); ?>" placeholder="예: 1" />
                        </div>
                    </fieldset>

                     <fieldset class="jj-control-group jj-fieldset-group jj-section-subsection" id="jj-form-field-padding">
                        <legend><?php _e( 'Padding (px)', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-dimensions-group">
                            <input type="number" class="jj-data-field" data-setting-key="forms[field][padding][top]" value="<?php echo esc_attr( $field_options['padding']['top'] ?? '' ); ?>" title="Top" placeholder="Top">
                            <input type="number" class="jj-data-field" data-setting-key="forms[field][padding][right]" value="<?php echo esc_attr( $field_options['padding']['right'] ?? '' ); ?>" title="Right" placeholder="Right">
                            <input type="number" class="jj-data-field" data-setting-key="forms[field][padding][bottom]" value="<?php echo esc_attr( $field_options['padding']['bottom'] ?? '' ); ?>" title="Bottom" placeholder="Bottom">
                            <input type="number" class="jj-data-field" data-setting-key="forms[field][padding][left]" value="<?php echo esc_attr( $field_options['padding']['left'] ?? '' ); ?>" title="Left" placeholder="Left">
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
