<?php
/**
 * [v3.3 '제련'] '3. 버튼' 섹션 (UI 확장)
 * - 사이트 관리자의 설계에 따라 Primary 단일 구조에서 탭 구조로 재구성
 * - Secondary Button, Text/Outline Button 제어판 신설
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// '제련': 3종 버튼의 옵션 배열을 '모두' '로드'
$btn_primary_options = $options['buttons']['primary'] ?? array();
$btn_secondary_options = $options['buttons']['secondary'] ?? array();
$btn_text_options = $options['buttons']['text'] ?? array();

// [v5.0.0] 탭 활성화/비활성화 체크 (안전하게 처리)
$tabs_layout = array();
if ( function_exists( 'jj_style_guide_sections_layout' ) ) {
    $section_layout = jj_style_guide_sections_layout();
    if ( isset( $section_layout['buttons'] ) && is_array( $section_layout['buttons'] ) ) {
        $buttons_layout = $section_layout['buttons'];
        $tabs_layout = isset( $buttons_layout['tabs'] ) && is_array( $buttons_layout['tabs'] ) ? $buttons_layout['tabs'] : array();
    }
}
?>

<div class="jj-section-global" id="jj-section-buttons">
    <h2 class="jj-section-title">
        <?php
        $jj_section_index = function_exists( 'jj_style_guide_section_index' ) ? jj_style_guide_section_index( 'buttons' ) : null;
        if ( $jj_section_index ) :
            ?>
            <span class="jj-section-index"><?php echo intval( $jj_section_index ); ?>.</span>
        <?php endif; ?>
        <?php echo esc_html( jj_style_guide_text( 'section_buttons_title', __( '버튼 (Buttons)', 'acf-css-really-simple-style-management-center' ) ) ); ?>
    </h2>
    <p class="description">
        <?php
        echo wp_kses_post(
            jj_style_guide_text(
                'section_buttons_description',
                __(
                    '사이트의 핵심 버튼 스타일(Primary, Secondary, Text 등)을 한 곳에서 정돈합니다. 이곳에서 정한 스타일은 <strong>WooCommerce</strong>, <strong>LearnDash</strong>, <strong>ACF Forms</strong> 등 공식 지원 \'스포크\'의 버튼에 CSS 변수(--jj-btn-...) 형태로 전달되어, 화면 전반에서 일관된 인상을 유지하도록 도와줍니다.',
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
            $tab_enabled_primary = ! isset( $tabs_layout['btn-primary'] ) || ! empty( $tabs_layout['btn-primary']['enabled'] );
            $tab_enabled_secondary = ! isset( $tabs_layout['btn-secondary'] ) || ! empty( $tabs_layout['btn-secondary']['enabled'] );
            $tab_enabled_text = ! isset( $tabs_layout['btn-text'] ) || ! empty( $tabs_layout['btn-text']['enabled'] );
            
            // 활성화된 탭이 하나도 없으면 Primary 버튼을 기본으로 활성화
            $has_enabled_tabs = $tab_enabled_primary || $tab_enabled_secondary || $tab_enabled_text;
            if ( ! $has_enabled_tabs ) {
                $tab_enabled_primary = true;
            }
            
            $first_enabled_tab = $tab_enabled_primary ? 'btn-primary' : ( $tab_enabled_secondary ? 'btn-secondary' : 'btn-text' );
            ?>
            <?php if ( $tab_enabled_primary ) : ?>
            <button type="button" class="jj-tab-button <?php echo ( $first_enabled_tab === 'btn-primary' ) ? 'is-active' : ''; ?>" data-tab="btn-primary">
                <?php _e( '1. Primary Button', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <?php endif; ?>
            <?php if ( $tab_enabled_secondary ) : ?>
            <button type="button" class="jj-tab-button <?php echo ( $first_enabled_tab === 'btn-secondary' ) ? 'is-active' : ''; ?>" data-tab="btn-secondary">
                <?php _e( '2. Secondary Button', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <?php endif; ?>
            <?php if ( $tab_enabled_text ) : ?>
            <button type="button" class="jj-tab-button <?php echo ( $first_enabled_tab === 'btn-text' ) ? 'is-active' : ''; ?>" data-tab="btn-text">
                <?php _e( '3. Text / Outline Button', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <?php endif; ?>
        </div>

        <?php if ( $tab_enabled_primary ) : ?>
        <div class="jj-tab-content <?php echo ( $first_enabled_tab === 'btn-primary' ) ? 'is-active' : ''; ?>" data-tab-content="btn-primary">
            <div class="jj-button-preview-grid">
                <div class="jj-button-preview">
                    <a href="#" class="jj-button-preview-btn jj-preview-primary" onclick="return false;"><?php _e( 'Primary Button', 'acf-css-really-simple-style-management-center' ); ?></a>
                </div>
                
                <div class="jj-button-controls">
                    <fieldset class="jj-control-group jj-fieldset-group">
                        <legend><?php _e( 'Colors', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-control-group"><label><?php _e( '배경 색상 (Normal & Hover)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group"><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[primary][background_color]" value="<?php echo esc_attr( $btn_primary_options['background_color'] ?? '' ); ?>" /><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[primary][background_color_hover]" value="<?php echo esc_attr( $btn_primary_options['background_color_hover'] ?? '' ); ?>" /></div></div>
                        <div class="jj-control-group"><label><?php _e( '텍스트 색상 (Normal & Hover)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group"><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[primary][text_color]" value="<?php echo esc_attr( $btn_primary_options['text_color'] ?? '' ); ?>" /><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[primary][text_color_hover]" value="<?php echo esc_attr( $btn_primary_options['text_color_hover'] ?? '' ); ?>" /></div></div>
                        <div class="jj-control-group"><label><?php _e( '테두리 색상 (Normal & Hover)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group"><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[primary][border_color]" value="<?php echo esc_attr( $btn_primary_options['border_color'] ?? '' ); ?>" /><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[primary][border_color_hover]" value="<?php echo esc_attr( $btn_primary_options['border_color_hover'] ?? '' ); ?>" /></div></div>
                    </fieldset>
                    <fieldset class="jj-control-group jj-fieldset-group jj-grid-2-col"><legend><?php _e( 'Layout', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-control-group"><label for="jj-btn-primary-border-radius"><?php _e( 'Border Radius (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" id="jj-btn-primary-border-radius" class="jj-data-field" data-setting-key="buttons[primary][border_radius]" value="<?php echo esc_attr( $btn_primary_options['border_radius'] ?? '' ); ?>" placeholder="예: 4" /></div>
                        <div class="jj-control-group"><label><?php _e( 'Padding (px)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-dimensions-group"><input type="number" class="jj-data-field" data-setting-key="buttons[primary][padding][top]" value="<?php echo esc_attr( $btn_primary_options['padding']['top'] ?? '' ); ?>" title="Top" placeholder="Top"><input type="number" class="jj-data-field" data-setting-key="buttons[primary][padding][right]" value="<?php echo esc_attr( $btn_primary_options['padding']['right'] ?? '' ); ?>" title="Right" placeholder="Right"><input type="number" class="jj-data-field" data-setting-key="buttons[primary][padding][bottom]" value="<?php echo esc_attr( $btn_primary_options['padding']['bottom'] ?? '' ); ?>" title="Bottom" placeholder="Bottom"><input type="number" class="jj-data-field" data-setting-key="buttons[primary][padding][left]" value="<?php echo esc_attr( $btn_primary_options['padding']['left'] ?? '' ); ?>" title="Left" placeholder="Left"></div></div>
                    </fieldset>
                    <fieldset class="jj-control-group jj-fieldset-group"><legend><?php _e( 'Button Shadow', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-shadow-group"><div class="jj-control-group"><label><?php _e( 'Color', 'acf-css-really-simple-style-management-center' ); ?></label><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[primary][shadow][color]" value="<?php echo esc_attr( $btn_primary_options['shadow']['color'] ?? '' ); ?>" /></div><div class="jj-control-group"><label><?php _e( 'X Offset (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="buttons[primary][shadow][x]" value="<?php echo esc_attr( $btn_primary_options['shadow']['x'] ?? '' ); ?>" placeholder="0"></div><div class="jj-control-group"><label><?php _e( 'Y Offset (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="buttons[primary][shadow][y]" value="<?php echo esc_attr( $btn_primary_options['shadow']['y'] ?? '' ); ?>" placeholder="10"></div><div class="jj-control-group"><label><?php _e( 'Blur (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="buttons[primary][shadow][blur]" value="<?php echo esc_attr( $btn_primary_options['shadow']['blur'] ?? '' ); ?>" placeholder="15"></div><div class="jj-control-group"><label><?php _e( 'Spread (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="buttons[primary][shadow][spread]" value="<?php echo esc_attr( $btn_primary_options['shadow']['spread'] ?? '' ); ?>" placeholder="-5"></div></div>
                    </fieldset>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( $tab_enabled_secondary ) : ?>
        <div class="jj-tab-content <?php echo ( $first_enabled_tab === 'btn-secondary' ) ? 'is-active' : ''; ?>" data-tab-content="btn-secondary">
            <div class="jj-button-preview-grid">
                <div class="jj-button-preview">
                    <a href="#" class="jj-button-preview-btn jj-preview-secondary" onclick="return false;"><?php _e( 'Secondary Button', 'acf-css-really-simple-style-management-center' ); ?></a>
                </div>
                
                <div class="jj-button-controls">
                    <fieldset class="jj-control-group jj-fieldset-group">
                        <legend><?php _e( 'Colors', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-control-group"><label><?php _e( '배경 색상 (Normal & Hover)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group"><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[secondary][background_color]" value="<?php echo esc_attr( $btn_secondary_options['background_color'] ?? '' ); ?>" /><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[secondary][background_color_hover]" value="<?php echo esc_attr( $btn_secondary_options['background_color_hover'] ?? '' ); ?>" /></div></div>
                        <div class="jj-control-group"><label><?php _e( '텍스트 색상 (Normal & Hover)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group"><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[secondary][text_color]" value="<?php echo esc_attr( $btn_secondary_options['text_color'] ?? '' ); ?>" /><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[secondary][text_color_hover]" value="<?php echo esc_attr( $btn_secondary_options['text_color_hover'] ?? '' ); ?>" /></div></div>
                        <div class="jj-control-group"><label><?php _e( '테두리 색상 (Normal & Hover)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group"><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[secondary][border_color]" value="<?php echo esc_attr( $btn_secondary_options['border_color'] ?? '' ); ?>" /><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[secondary][border_color_hover]" value="<?php echo esc_attr( $btn_secondary_options['border_color_hover'] ?? '' ); ?>" /></div></div>
                    </fieldset>
                    <fieldset class="jj-control-group jj-fieldset-group jj-grid-2-col"><legend><?php _e( 'Layout', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-control-group"><label for="jj-btn-secondary-border-radius"><?php _e( 'Border Radius (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" id="jj-btn-secondary-border-radius" class="jj-data-field" data-setting-key="buttons[secondary][border_radius]" value="<?php echo esc_attr( $btn_secondary_options['border_radius'] ?? '' ); ?>" placeholder="예: 4" /></div>
                        <div class="jj-control-group"><label><?php _e( 'Padding (px)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-dimensions-group"><input type="number" class="jj-data-field" data-setting-key="buttons[secondary][padding][top]" value="<?php echo esc_attr( $btn_secondary_options['padding']['top'] ?? '' ); ?>" title="Top" placeholder="Top"><input type="number" class="jj-data-field" data-setting-key="buttons[secondary][padding][right]" value="<?php echo esc_attr( $btn_secondary_options['padding']['right'] ?? '' ); ?>" title="Right" placeholder="Right"><input type="number" class="jj-data-field" data-setting-key="buttons[secondary][padding][bottom]" value="<?php echo esc_attr( $btn_secondary_options['padding']['bottom'] ?? '' ); ?>" title="Bottom" placeholder="Bottom"><input type="number" class="jj-data-field" data-setting-key="buttons[secondary][padding][left]" value="<?php echo esc_attr( $btn_secondary_options['padding']['left'] ?? '' ); ?>" title="Left" placeholder="Left"></div></div>
                    </fieldset>
                    <fieldset class="jj-control-group jj-fieldset-group"><legend><?php _e( 'Button Shadow', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-shadow-group"><div class="jj-control-group"><label><?php _e( 'Color', 'acf-css-really-simple-style-management-center' ); ?></label><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[secondary][shadow][color]" value="<?php echo esc_attr( $btn_secondary_options['shadow']['color'] ?? '' ); ?>" /></div><div class="jj-control-group"><label><?php _e( 'X Offset (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="buttons[secondary][shadow][x]" value="<?php echo esc_attr( $btn_secondary_options['shadow']['x'] ?? '' ); ?>" placeholder="0"></div><div class="jj-control-group"><label><?php _e( 'Y Offset (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="buttons[secondary][shadow][y]" value="<?php echo esc_attr( $btn_secondary_options['shadow']['y'] ?? '' ); ?>" placeholder="10"></div><div class="jj-control-group"><label><?php _e( 'Blur (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="buttons[secondary][shadow][blur]" value="<?php echo esc_attr( $btn_secondary_options['shadow']['blur'] ?? '' ); ?>" placeholder="15"></div><div class="jj-control-group"><label><?php _e( 'Spread (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="buttons[secondary][shadow][spread]" value="<?php echo esc_attr( $btn_secondary_options['shadow']['spread'] ?? '' ); ?>" placeholder="-5"></div></div>
                    </fieldset>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( $tab_enabled_text ) : ?>
        <div class="jj-tab-content <?php echo ( $first_enabled_tab === 'btn-text' ) ? 'is-active' : ''; ?>" data-tab-content="btn-text">
             <div class="jj-button-preview-grid">
                <div class="jj-button-preview">
                    <a href="#" class="jj-button-preview-btn jj-preview-text" onclick="return false;"><?php _e( 'Text Button', 'acf-css-really-simple-style-management-center' ); ?></a>
                </div>
                
                <div class="jj-button-controls">
                    <fieldset class="jj-control-group jj-fieldset-group">
                        <legend><?php _e( 'Colors', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-control-group"><label><?php _e( '배경 색상 (Normal & Hover)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group"><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[text][background_color]" value="<?php echo esc_attr( $btn_text_options['background_color'] ?? '' ); ?>" /><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[text][background_color_hover]" value="<?php echo esc_attr( $btn_text_options['background_color_hover'] ?? '' ); ?>" /></div></div>
                        <div class="jj-control-group"><label><?php _e( '텍스트 색상 (Normal & Hover)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group"><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[text][text_color]" value="<?php echo esc_attr( $btn_text_options['text_color'] ?? '' ); ?>" /><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[text][text_color_hover]" value="<?php echo esc_attr( $btn_text_options['text_color_hover'] ?? '' ); ?>" /></div></div>
                        <div class="jj-control-group"><label><?php _e( '테두리 색상 (Normal & Hover)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-color-group"><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[text][border_color]" value="<?php echo esc_attr( $btn_text_options['border_color'] ?? '' ); ?>" /><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[text][border_color_hover]" value="<?php echo esc_attr( $btn_text_options['border_color_hover'] ?? '' ); ?>" /></div></div>
                    </fieldset>
                    <fieldset class="jj-control-group jj-fieldset-group jj-grid-2-col"><legend><?php _e( 'Layout', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-control-group"><label for="jj-btn-text-border-radius"><?php _e( 'Border Radius (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" id="jj-btn-text-border-radius" class="jj-data-field" data-setting-key="buttons[text][border_radius]" value="<?php echo esc_attr( $btn_text_options['border_radius'] ?? '' ); ?>" placeholder="예: 4" /></div>
                        <div class="jj-control-group"><label><?php _e( 'Padding (px)', 'acf-css-really-simple-style-management-center' ); ?></label><div class="jj-dimensions-group"><input type="number" class="jj-data-field" data-setting-key="buttons[text][padding][top]" value="<?php echo esc_attr( $btn_text_options['padding']['top'] ?? '' ); ?>" title="Top" placeholder="Top"><input type="number" class="jj-data-field" data-setting-key="buttons[text][padding][right]" value="<?php echo esc_attr( $btn_text_options['padding']['right'] ?? '' ); ?>" title="Right" placeholder="Right"><input type="number" class="jj-data-field" data-setting-key="buttons[text][padding][bottom]" value="<?php echo esc_attr( $btn_text_options['padding']['bottom'] ?? '' ); ?>" title="Bottom" placeholder="Bottom"><input type="number" class="jj-data-field" data-setting-key="buttons[text][padding][left]" value="<?php echo esc_attr( $btn_text_options['padding']['left'] ?? '' ); ?>" title="Left" placeholder="Left"></div></div>
                    </fieldset>
                    <fieldset class="jj-control-group jj-fieldset-group"><legend><?php _e( 'Button Shadow', 'acf-css-really-simple-style-management-center' ); ?></legend>
                        <div class="jj-shadow-group"><div class="jj-control-group"><label><?php _e( 'Color', 'acf-css-really-simple-style-management-center' ); ?></label><input type="text" class="jj-color-field jj-data-field" data-setting-key="buttons[text][shadow][color]" value="<?php echo esc_attr( $btn_text_options['shadow']['color'] ?? '' ); ?>" /></div><div class="jj-control-group"><label><?php _e( 'X Offset (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="buttons[text][shadow][x]" value="<?php echo esc_attr( $btn_text_options['shadow']['x'] ?? '' ); ?>" placeholder="0"></div><div class="jj-control-group"><label><?php _e( 'Y Offset (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="buttons[text][shadow][y]" value="<?php echo esc_attr( $btn_text_options['shadow']['y'] ?? '' ); ?>" placeholder="10"></div><div class="jj-control-group"><label><?php _e( 'Blur (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="buttons[text][shadow][blur]" value="<?php echo esc_attr( $btn_text_options['shadow']['blur'] ?? '' ); ?>" placeholder="15"></div><div class="jj-control-group"><label><?php _e( 'Spread (px)', 'acf-css-really-simple-style-management-center' ); ?></label><input type="number" class="jj-data-field" data-setting-key="buttons[text][shadow][spread]" value="<?php echo esc_attr( $btn_text_options['shadow']['spread'] ?? '' ); ?>" placeholder="-5"></div></div>
                    </fieldset>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>