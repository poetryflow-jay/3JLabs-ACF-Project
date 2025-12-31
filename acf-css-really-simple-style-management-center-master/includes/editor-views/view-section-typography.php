<?php
/**
 * [v1.7.3] 타이포그래피 섹션 (CSS 클래스 구조 변경)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$typography_options = $options['typography'] ?? array();
$fonts_options      = $options['fonts'] ?? array();
$tags               = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p');
$font_weights       = array('100'=>'100', '200'=>'200', '300'=>'300', '400'=>'400', '500'=>'500', '600'=>'600', '700'=>'700', '800'=>'800', '900'=>'900');
$font_styles        = array('normal'=>'Normal', 'italic'=>'Italic');
$text_transforms    = array('' => 'Default', 'none' => 'None', 'uppercase' => 'UPPERCASE', 'capitalize' => 'Capitalize', 'lowercase' => 'lowercase');
?>

<div class="jj-section-global" id="jj-section-typography">
    <h2 class="jj-section-title">
        <?php
        $jj_section_index = function_exists( 'jj_style_guide_section_index' ) ? jj_style_guide_section_index( 'typography' ) : null;
        if ( $jj_section_index ) :
            ?>
            <span class="jj-section-index"><?php echo intval( $jj_section_index ); ?>.</span>
        <?php endif; ?>
        <?php echo esc_html( jj_style_guide_text( 'section_typography_title', __( '타이포그래피 (Typography)', 'jj-style-guide' ) ) ); ?>
    </h2>
    <p class="description">
        <?php
        echo wp_kses_post(
            jj_style_guide_text(
                'section_typography_description',
                __(
                    '사이트 전역(Global)의 타이포그래피 스타일을 차분하게 정리하는 공간입니다. 이곳에서 정의한 값은 <strong>테마의 기본 폰트</strong>와 <strong>LearnDash, UM 등 공식 지원 \'스포크\' 플러그인의 텍스트 영역</strong>에 CSS 변수(--jj-font-...)로 전달되어, 페이지마다 글꼴 인상이 흐트러지지 않도록 도와줍니다.',
                    'jj-style-guide'
                )
            )
        );
        ?>
    </p>

    <?php
    // 커스텀 웹 폰트 및 매핑 영역
    $font_roles = array(
        'korean' => array(
            'label' => __( '한국어 기본 폰트', 'jj-style-guide' ),
            'description' => __( '주로 한국어 텍스트에 사용할 기본 폰트입니다. 본문/제목 설정과 함께 사용하거나, 커스텀 CSS에서 var(--jj-font-ko-family)를 참조할 수 있습니다.', 'jj-style-guide' ),
        ),
        'english' => array(
            'label' => __( '영문 기본 폰트', 'jj-style-guide' ),
            'description' => __( '주로 영문 텍스트에 사용할 기본 폰트입니다. 커스텀 CSS에서 var(--jj-font-en-family)를 참조할 수 있습니다.', 'jj-style-guide' ),
        ),
        'buttons' => array(
            'label' => __( '버튼 전용 폰트', 'jj-style-guide' ),
            'description' => __( '버튼에 사용할 전용 폰트입니다. 전역 버튼 스타일에서 var(--jj-font-button-family)를 사용해 적용됩니다.', 'jj-style-guide' ),
        ),
        'forms' => array(
            'label' => __( '폼/입력 필드 전용 폰트', 'jj-style-guide' ),
            'description' => __( '폼 라벨 및 입력 필드에 사용할 전용 폰트입니다. 전역 폼 스타일에서 var(--jj-font-form-family)를 사용해 적용됩니다.', 'jj-style-guide' ),
        ),
    );
    ?>

    <div class="jj-fieldset-group jj-custom-fonts-panel" style="margin-bottom: 30px;">
        <h3><?php _e( '커스텀 웹 폰트 및 매핑', 'jj-style-guide' ); ?></h3>
        <p class="description">
            <?php _e( '아래에서 한국어/영문/버튼/폼에 사용할 웹 폰트를 지정할 수 있습니다. 폰트 파일은 미디어 라이브러리에 업로드한 뒤, 각 역할(Role)에 연결해 주세요.', 'jj-style-guide' ); ?>
        </p>

        <div class="jj-style-guide-grid jj-grid-2-col">
            <?php foreach ( $font_roles as $role_key => $meta ) :
                $role_settings   = $fonts_options[ $role_key ] ?? array();
                $role_family     = $role_settings['family'] ?? '';
                $role_attachment = isset( $role_settings['attachment_id'] ) ? (int) $role_settings['attachment_id'] : 0;
                $role_format     = $role_settings['format'] ?? '';
                $file_label      = '';
                if ( $role_attachment ) {
                    $file_obj   = get_post( $role_attachment );
                    $file_label = $file_obj ? $file_obj->post_title : '';
                }
            ?>
                <div class="jj-control-group jj-custom-font-role" data-font-role="<?php echo esc_attr( $role_key ); ?>">
                    <label><?php echo esc_html( $meta['label'] ); ?></label>
                    <p class="description"><?php echo esc_html( $meta['description'] ); ?></p>

                    <label for="jj-font-<?php echo esc_attr( $role_key ); ?>-family" style="margin-top:6px; display:block;">
                        <?php _e( 'Font Family 이름', 'jj-style-guide' ); ?>
                    </label>
                    <input
                        type="text"
                        id="jj-font-<?php echo esc_attr( $role_key ); ?>-family"
                        class="jj-data-field"
                        data-setting-key="fonts[<?php echo esc_attr( $role_key ); ?>][family]"
                        value="<?php echo esc_attr( $role_family ); ?>"
                        placeholder="예: 'Noto Sans KR', sans-serif"
                        style="width:100%;"
                    />

                    <div class="jj-custom-font-upload" style="margin-top:8px;">
                        <input
                            type="hidden"
                            class="jj-data-field jj-font-attachment-id"
                            data-setting-key="fonts[<?php echo esc_attr( $role_key ); ?>][attachment_id]"
                            value="<?php echo esc_attr( $role_attachment ); ?>"
                        />
                        <input
                            type="text"
                            class="jj-font-file-label"
                            value="<?php echo esc_attr( $file_label ); ?>"
                            readonly
                            placeholder="<?php esc_attr_e( '선택된 폰트 파일 없음', 'jj-style-guide' ); ?>"
                            style="width:100%; margin-bottom:4px; background:#f9f9f9;"
                        />
                        <button type="button" class="button jj-font-upload-button">
                            <?php _e( '폰트 파일 선택/변경', 'jj-style-guide' ); ?>
                        </button>
                    </div>

                    <label for="jj-font-<?php echo esc_attr( $role_key ); ?>-format" style="margin-top:8px; display:block;">
                        <?php _e( '포맷 (선택 사항)', 'jj-style-guide' ); ?>
                    </label>
                    <input
                        type="text"
                        id="jj-font-<?php echo esc_attr( $role_key ); ?>-format"
                        class="jj-data-field"
                        data-setting-key="fonts[<?php echo esc_attr( $role_key ); ?>][format]"
                        value="<?php echo esc_attr( $role_format ); ?>"
                        placeholder="예: woff2, woff, truetype, opentype"
                        style="width:100%;"
                    />
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        // 커스텀 CSS 입력 (기존 기능 유지)
        $custom_css = $typography_options['custom_css'] ?? '';
        ?>
        <div class="jj-control-group" style="margin-top: 20px;">
            <label for="jj-typography-custom-css">
                <?php _e( '추가 커스텀 CSS (@font-face 등)', 'jj-style-guide' ); ?>
            </label>
            <p class="description">
                <?php _e( '필요하다면 @font-face 규칙이나 추가 타이포그래피 CSS를 여기에 직접 입력할 수 있습니다. 위에서 지정한 폰트 변수(var(--jj-font-ko-family), var(--jj-font-en-family) 등)도 함께 사용할 수 있습니다.', 'jj-style-guide' ); ?>
            </p>
            <textarea
                id="jj-typography-custom-css"
                class="jj-data-field"
                data-setting-key="typography[custom_css]"
                rows="6"
                style="width:100%; font-family:monospace;"
            ><?php echo esc_textarea( $custom_css ); ?></textarea>
        </div>
    </div>

    <?php foreach ( $tags as $tag ) : 
        $settings = $typography_options[$tag] ?? array();
        $font_family = $settings['font_family'] ?? '';
        $font_weight = $settings['font_weight'] ?? '400';
        $font_style = $settings['font_style'] ?? 'normal';
        $font_size_desktop = $settings['font_size']['desktop'] ?? '';
        $font_size_tablet = $settings['font_size']['tablet'] ?? '';
        $font_size_mobile = $settings['font_size']['mobile'] ?? '';
        $line_height = $settings['line_height'] ?? '';
        $letter_spacing = $settings['letter_spacing'] ?? '';
        $text_transform = $settings['text_transform'] ?? '';
    ?>
        <div class="jj-typography-row" data-type="<?php echo esc_attr( $tag ); ?>">
            <div class="tag"><?php echo esc_html( strtoupper( $tag ) ); ?></div>
            
            <div class="controls jj-typography-controls">
                
                <div class="jj-control-group">
                    <label for="jj-font-family-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Font Family', 'jj-style-guide' ); ?></label>
                    <input type="text" 
                           id="jj-font-family-<?php echo esc_attr( $tag ); ?>" 
                           class="jj-data-field" 
                           data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][font_family]"
                           value="<?php echo esc_attr( $font_family ); ?>"
                           placeholder="Inter, 'Noto Sans KR', sans-serif" />
                </div>
                
                <div class="jj-control-group">
                    <label for="jj-font-weight-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Weight', 'jj-style-guide' ); ?></label>
                    <select id="jj-font-weight-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][font_weight]">
                        <?php foreach ( $font_weights as $weight => $label ) : ?>
                            <option value="<?php echo esc_attr( $weight ); ?>" <?php selected( $font_weight, $weight ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="jj-control-group">
                    <label for="jj-font-style-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Style', 'jj-style-guide' ); ?></label>
                    <select id="jj-font-style-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][font_style]">
                        <?php foreach ( $font_styles as $style => $label ) : ?>
                            <option value="<?php echo esc_attr( $style ); ?>" <?php selected( $font_style, $style ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="jj-control-group">
                    <label for="jj-line-height-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Line Height (em)', 'jj-style-guide' ); ?></label>
                    <input type="number" 
                           step="0.1"
                           id="jj-line-height-<?php echo esc_attr( $tag ); ?>" 
                           class="jj-data-field" 
                           data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][line_height]"
                           value="<?php echo esc_attr( $line_height ); ?>"
                           placeholder="예: 1.5" />
                </div>

                <div class="jj-control-group">
                    <label for="jj-letter-spacing-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Letter Spacing (px)', 'jj-style-guide' ); ?></label>
                    <input type="number" 
                           step="0.1"
                           id="jj-letter-spacing-<?php echo esc_attr( $tag ); ?>" 
                           class="jj-data-field" 
                           data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][letter_spacing]"
                           value="<?php echo esc_attr( $letter_spacing ); ?>"
                           placeholder="예: -0.1" />
                </div>

                <div class="jj-control-group">
                    <label for="jj-text-transform-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Transform', 'jj-style-guide' ); ?></label>
                    <select id="jj-text-transform-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][text_transform]">
                        <?php foreach ( $text_transforms as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $text_transform, $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="jj-control-group jj-responsive-control">
                    <div class="jj-responsive-tabs">
                        <button type="button" class="jj-responsive-tab-button is-active" data-device="desktop">D</button>
                        <button type="button" class="jj-responsive-tab-button" data-device="tablet">T</button>
                        <button type="button" class="jj-responsive-tab-button" data-device="mobile">M</button>
                    </div>
                    
                    <div class="jj-responsive-tab-content is-active" data-device="desktop">
                        <label for="jj-font-size-<?php echo esc_attr( $tag ); ?>-desktop"><?php _e( 'Size (Desktop)', 'jj-style-guide' ); ?></label>
                        <input type="number" 
                               id="jj-font-size-<?php echo esc_attr( $tag ); ?>-desktop" 
                               class="jj-data-field" 
                               data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][font_size][desktop]"
                               value="<?php echo esc_attr( $font_size_desktop ); ?>" 
                               placeholder="예: 32" />
                    </div>
                    <div class="jj-responsive-tab-content" data-device="tablet">
                         <label for="jj-font-size-<?php echo esc_attr( $tag ); ?>-tablet"><?php _e( 'Size (Tablet)', 'jj-style-guide' ); ?></label>
                        <input type="number" 
                               id="jj-font-size-<?php echo esc_attr( $tag ); ?>-tablet" 
                               class="jj-data-field" 
                               data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][font_size][tablet]"
                               value="<?php echo esc_attr( $font_size_tablet ); ?>" 
                               placeholder="예: 28" />
                    </div>
                    <div class="jj-responsive-tab-content" data-device="mobile">
                         <label for="jj-font-size-<?php echo esc_attr( $tag ); ?>-mobile"><?php _e( 'Size (Mobile)', 'jj-style-guide' ); ?></label>
                        <input type="number" 
                               id="jj-font-size-<?php echo esc_attr( $tag ); ?>-mobile" 
                               class="jj-data-field" 
                               data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][font_size][mobile]"
                               value="<?php echo esc_attr( $font_size_mobile ); ?>" 
                               placeholder="예: 24" />
                    </div>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
</div>