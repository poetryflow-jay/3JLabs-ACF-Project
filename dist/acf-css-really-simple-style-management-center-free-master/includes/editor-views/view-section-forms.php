<?php
/**
 * [v3.3 '제련'] '4. 폼 & 필드' 섹션 (UI 신설)
 * - 사이트 관리자의 설계에 따라 전역 폼(라벨, 입력 필드) 제어 UI 신설
 * - 'ACF', 'Advanced Members' 등 스포크 폼 스타일 연동을 위한 선행 과제
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// '제련': 'forms' 배열 로드
$forms_options = $options['forms'] ?? array();
$label_options = $forms_options['label'] ?? array();
$field_options = $forms_options['field'] ?? array();

// '타이포그래피' 설정 (라벨 기본값용)
$font_weights = array('100'=>'100', '200'=>'200', '300'=>'300', '400'=>'400', '500'=>'500', '600'=>'600', '700'=>'700', '800'=>'800', '900'=>'900');
$font_styles = array('normal'=>'Normal', 'italic'=>'Italic');
$text_transforms = array('' => 'Default', 'none' => 'None', 'uppercase' => 'UPPERCASE', 'capitalize' => 'Capitalize', 'lowercase' => 'lowercase');

// [v5.0.0] 탭 활성화/비활성화 체크 (안전하게 처리)
$tabs_layout = array();
if ( function_exists( 'jj_style_guide_sections_layout' ) ) {
    $section_layout = jj_style_guide_sections_layout();
    if ( isset( $section_layout['forms'] ) && is_array( $section_layout['forms'] ) ) {
        $forms_layout = $section_layout['forms'];
        $tabs_layout = isset( $forms_layout['tabs'] ) && is_array( $forms_layout['tabs'] ) ? $forms_layout['tabs'] : array();
    }
}
?>

<div class="jj-section-global" id="jj-section-forms">
    <h2 class="jj-section-title">
        <?php
        $jj_section_index = function_exists( 'jj_style_guide_section_index' ) ? jj_style_guide_section_index( 'forms' ) : null;
        if ( $jj_section_index ) :
            ?>
            <span class="jj-section-index"><?php echo intval( $jj_section_index ); ?>.</span>
        <?php endif; ?>
        <?php echo esc_html( jj_style_guide_text( 'section_forms_title', __( '폼 & 필드 (Forms & Fields)', 'acf-css-really-simple-style-management-center' ) ) ); ?>
    </h2>
    <p class="description">
        <?php
        echo wp_kses_post(
            jj_style_guide_text(
                'section_forms_description',
                __(
                    '사이트 전역의 폼(Form) 스타일(라벨, 입력 필드)을 정갈하게 맞추는 영역입니다. 여기에서 정한 값은 <strong>Advanced Members</strong>, <strong>ACF Forms</strong>, <strong>FluentForm</strong> 등 폼을 사용하는 스포크에 CSS 변수(--jj-form-...)로 전달되어, 회원가입·문의·결제 화면 등에서도 자연스럽게 같은 톤으로 표현되도록 돕습니다.',
                    'acf-css-really-simple-style-management-center'
                )
            )
        );
        ?>
    </p>

    <div class="jj-tabs-container">
        <div class="jj-tabs-nav">
            <?php 
            // [v5.0.0] 탭 활성화/비활성화 체크
            $tab_enabled_label = ! isset( $tabs_layout['form-label'] ) || ! empty( $tabs_layout['form-label']['enabled'] );
            $tab_enabled_field = ! isset( $tabs_layout['form-field'] ) || ! empty( $tabs_layout['form-field']['enabled'] );
            
            // 활성화된 탭이 하나도 없으면 라벨 탭을 기본으로 활성화
            $has_enabled_tabs = $tab_enabled_label || $tab_enabled_field;
            if ( ! $has_enabled_tabs ) {
                $tab_enabled_label = true;
            }
            
            $first_enabled_tab = $tab_enabled_label ? 'form-label' : 'form-field';
            ?>
            <?php if ( $tab_enabled_label ) : ?>
            <button type="button" class="jj-tab-button <?php echo ( $first_enabled_tab === 'form-label' ) ? 'is-active' : ''; ?>" data-tab="form-label">
                <?php _e( '1. 라벨 (Labels)', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <?php endif; ?>
            <?php if ( $tab_enabled_field ) : ?>
            <button type="button" class="jj-tab-button <?php echo ( $first_enabled_tab === 'form-field' ) ? 'is-active' : ''; ?>" data-tab="form-field">
                <?php _e( '2. 입력 필드 (Fields)', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <?php endif; ?>
        </div>

        <?php if ( $tab_enabled_label ) : ?>
        <div class="jj-tab-content <?php echo ( $first_enabled_tab === 'form-label' ) ? 'is-active' : ''; ?>" data-tab-content="form-label">
            <p class="description" style="margin-bottom: 25px;">
                <?php _e( '모든 폼의 <code>&lt;label&gt;</code> 태그 스타일을 제어합니다. (기본값: \'2. 타이포그래피\'의 \'P\' 스타일 일부를 따름)', 'acf-css-really-simple-style-management-center' ); ?>
            </p>
            
            <div class="jj-contextual-typography-controls" style="grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));">
                
                <div class="jj-control-group">
                    <label for="jj-form-label-font-weight"><?php _e( 'Weight', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <select id="jj-form-label-font-weight" class="jj-data-field" data-setting-key="forms[label][font_weight]">
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

                <div class="jj-control-group jj-color-card" style="padding: 10px;">
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
        <?php endif; ?>

        <?php if ( $tab_enabled_field ) : ?>
        <div class="jj-tab-content <?php echo ( $first_enabled_tab === 'form-field' ) ? 'is-active' : ''; ?>" data-tab-content="form-field">
            <div class="jj-button-preview-grid">
                
                <div class="jj-button-preview" style="background: #f0f0f1; display: block; padding: 20px;">
                    <style id="jj-form-preview-style"></style> <div class="jj-form-preview-wrap">
                        <label class="jj-form-preview-label" for="jj-preview-input"><?php _e( '미리보기 라벨', 'acf-css-really-simple-style-management-center' ); ?></label>
                        <input class="jj-form-preview-input" type="text" id="jj-preview-input" placeholder="<?php _e( '입력 필드 미리보기...', 'acf-css-really-simple-style-management-center' ); ?>">
                        <p class="description" style="margin-top: 10px;"><?php _e( '이 필드는 \'활성화(Focus)\' 상태입니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                    </div>
                </div>
                
                <div class="jj-button-controls">
                    <fieldset class="jj-control-group jj-fieldset-group">
                        <legend><?php _e( 'Colors', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-style-guide-grid jj-grid-4-col" style="gap: 10px;">
                            <div class="jj-control-group"><label><?php _e( '배경', 'acf-css-really-simple-style-management-center' ); ?></label><input type="text" class="jj-color-field jj-data-field" data-setting-key="forms[field][background_color]" value="<?php echo esc_attr( $field_options['background_color'] ?? '' ); ?>" /></div>
                            <div class="jj-control-group"><label><?php _e( '텍스트', 'acf-css-really-simple-style-management-center' ); ?></label><input type="text" class="jj-color-field jj-data-field" data-setting-key="forms[field][text_color]" value="<?php echo esc_attr( $field_options['text_color'] ?? '' ); ?>" /></div>
                            <div class="jj-control-group"><label><?php _e( '테두리', 'acf-css-really-simple-style-management-center' ); ?></label><input type="text" class="jj-color-field jj-data-field" data-setting-key="forms[field][border_color]" value="<?php echo esc_attr( $field_options['border_color'] ?? '' ); ?>" /></div>
                            <div class="jj-control-group"><label><?php _e( '테두리 (Focus)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="text" class="jj-color-field jj-data-field" data-setting-key="forms[field][border_color_focus]" value="<?php echo esc_attr( $field_options['border_color_focus'] ?? '' ); ?>" /></div>
                        </div>
                    </fieldset>
                    
                    <fieldset class="jj-control-group jj-fieldset-group jj-grid-2-col">
                        <legend><?php _e( 'Layout', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-control-group">
                            <label for="jj-form-field-border-radius"><?php _e( 'Border Radius (px)', 'acf-css-really-simple-style-management-center' ); ?></label>
                            <input type="number" id="jj-form-field-border-radius" class="jj-data-field" data-setting-key="forms[field][border_radius]" value="<?php echo esc_attr( $field_options['border_radius'] ?? '' ); ?>" placeholder="예: 4" />
                        </div>
                        <div class="jj-control-group">
                            <label for="jj-form-field-border-width"><?php _e( 'Border Width (px)', 'acf-css-really-simple-style-management-center' ); ?></label>
                            <input type="number" id="jj-form-field-border-width" class="jj-data-field" data-setting-key="forms[field][border_width]" value="<?php echo esc_attr( $field_options['border_width'] ?? '' ); ?>" placeholder="예: 1" />
                        </div>
                    </fieldset>

                     <fieldset class="jj-control-group jj-fieldset-group">
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
        <?php endif; ?>
    </div>
</div>