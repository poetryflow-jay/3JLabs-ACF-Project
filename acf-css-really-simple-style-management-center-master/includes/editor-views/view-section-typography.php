<?php
/**
 * [v1.7.3] 타이포그래피 섹션 (CSS 클래스 구조 변경)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$typography_options = $options['typography'] ?? array();
$fonts_options      = $options['fonts'] ?? array();
$typography_settings = isset( $options['typography_settings'] ) && is_array( $options['typography_settings'] ) ? $options['typography_settings'] : array();
$typo_unit           = $typography_settings['unit'] ?? 'px';
$typo_base_px        = $typography_settings['base_px'] ?? '16';

// 반응형 브레이크포인트(고급 스타일링) 기반으로 탭을 동적으로 구성
$breakpoints = array();
if ( class_exists( 'JJ_Advanced_Styling' ) ) {
    try {
        $breakpoints = JJ_Advanced_Styling::instance()->get_breakpoints();
        if ( ! is_array( $breakpoints ) ) {
            $breakpoints = array();
        }
    } catch ( Exception $e ) {
        $breakpoints = array();
    } catch ( Error $e ) {
        $breakpoints = array();
    }
}
if ( empty( $breakpoints ) ) {
    // 폴백 (기본 3단계)
    $breakpoints = array(
        'desktop' => array( 'min' => 1024, 'label' => __( '데스크톱', 'acf-css-really-simple-style-management-center' ) ),
        'tablet'  => array( 'max' => 1024, 'label' => __( '태블릿', 'acf-css-really-simple-style-management-center' ) ),
        'mobile'  => array( 'max' => 767,  'label' => __( '모바일', 'acf-css-really-simple-style-management-center' ) ),
    );
}

// UI 표시 순서 (요청: UHD/QHD 포함 + Desktop/Laptop/Tablet/Phablet/Mobile/Old Phone)
$device_order = array(
    'desktop',
    'laptop',
    'tablet',
    'phablet',
    'mobile',
    'phone_small',
    'desktop_qhd',
    'desktop_uhd',
    'desktop_5k',
    'desktop_8k',
);

$device_tab_labels = array(
    'desktop_uhd' => 'U',
    'desktop_qhd' => 'Q',
    'desktop_5k'  => '5K',
    'desktop_8k'  => '8K',
    'desktop'     => 'D',
    'laptop'      => 'L',
    'tablet'      => 'T',
    'phablet'     => 'P',
    'mobile'      => 'M',
    'phone_small' => 'S',
);

// 권장 폰트(px) 기본값(placeholder 용) — 사용자가 직접 바꿔도 되고, 참고용으로만 노출
$recommended_sizes = array(
    'h1' => array( 'desktop' => 40, 'laptop' => 38, 'tablet' => 36, 'phablet' => 32, 'mobile' => 30, 'phone_small' => 28, 'desktop_qhd' => 44, 'desktop_uhd' => 48, 'desktop_5k' => 52, 'desktop_8k' => 60 ),
    'h2' => array( 'desktop' => 32, 'laptop' => 30, 'tablet' => 28, 'phablet' => 26, 'mobile' => 24, 'phone_small' => 22, 'desktop_qhd' => 36, 'desktop_uhd' => 40, 'desktop_5k' => 44, 'desktop_8k' => 52 ),
    'h3' => array( 'desktop' => 26, 'laptop' => 24, 'tablet' => 22, 'phablet' => 20, 'mobile' => 19, 'phone_small' => 18, 'desktop_qhd' => 28, 'desktop_uhd' => 30, 'desktop_5k' => 32, 'desktop_8k' => 36 ),
    'h4' => array( 'desktop' => 22, 'laptop' => 20, 'tablet' => 20, 'phablet' => 18, 'mobile' => 18, 'phone_small' => 16, 'desktop_qhd' => 24, 'desktop_uhd' => 26, 'desktop_5k' => 28, 'desktop_8k' => 32 ),
    'h5' => array( 'desktop' => 18, 'laptop' => 18, 'tablet' => 18, 'phablet' => 16, 'mobile' => 16, 'phone_small' => 15, 'desktop_qhd' => 20, 'desktop_uhd' => 22, 'desktop_5k' => 24, 'desktop_8k' => 28 ),
    'h6' => array( 'desktop' => 16, 'laptop' => 16, 'tablet' => 16, 'phablet' => 15, 'mobile' => 15, 'phone_small' => 14, 'desktop_qhd' => 18, 'desktop_uhd' => 20, 'desktop_5k' => 22, 'desktop_8k' => 24 ),
    'p'  => array( 'desktop' => 16, 'laptop' => 16, 'tablet' => 16, 'phablet' => 15, 'mobile' => 15, 'phone_small' => 14, 'desktop_qhd' => 16, 'desktop_uhd' => 18, 'desktop_5k' => 18, 'desktop_8k' => 20 ),
);

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
        <?php echo esc_html( jj_style_guide_text( 'section_typography_title', __( '타이포그래피 (Typography)', 'acf-css-really-simple-style-management-center' ) ) ); ?>
    </h2>
    <p class="description">
        <?php
        echo wp_kses_post(
            jj_style_guide_text(
                'section_typography_description',
                __(
                    '사이트 전역(Global)의 타이포그래피 스타일을 차분하게 정리하는 공간입니다. 이곳에서 정의한 값은 <strong>테마의 기본 폰트</strong>와 <strong>LearnDash, UM 등 공식 지원 \'스포크\' 플러그인의 텍스트 영역</strong>에 CSS 변수(--jj-font-...)로 전달되어, 페이지마다 글꼴 인상이 흐트러지지 않도록 도와줍니다.',
                    'acf-css-really-simple-style-management-center'
                )
            )
        );
        ?>
    </p>

    <div class="jj-fieldset-group jj-typography-scale-panel" style="margin-bottom: 30px;">
        <h3><?php _e( '반응형 기준점 & 단위', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <p class="description">
            <?php echo wp_kses_post( __( '폰트 크기 입력은 <strong>px 기준(숫자)</strong>으로 유지하되, 실제 프런트엔드 CSS 변수(<code>--jj-font-*-size</code>) 출력은 <strong>px/rem/em</strong> 중 선택한 단위로 자동 변환됩니다. (rem/em 변환 시 기준 px는 <code>1rem = base_px</code>로 간주합니다.)', 'acf-css-really-simple-style-management-center' ) ); ?>
        </p>

        <div class="jj-style-guide-grid jj-grid-2-col">
            <div class="jj-control-group">
                <label for="jj-typography-base-px"><?php _e( '기준 px (1rem/1em)', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="number"
                       id="jj-typography-base-px"
                       class="jj-data-field"
                       data-setting-key="typography_settings[base_px]"
                       value="<?php echo esc_attr( $typo_base_px ); ?>"
                       placeholder="16"
                       min="1"
                       step="1" />
                <p class="description"><?php _e( '예: 16 (일반적인 브라우저 기본값)', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>
            <div class="jj-control-group">
                <label for="jj-typography-unit"><?php _e( '출력 단위', 'acf-css-really-simple-style-management-center' ); ?></label>
                <select id="jj-typography-unit" class="jj-data-field" data-setting-key="typography_settings[unit]">
                    <option value="px" <?php selected( $typo_unit, 'px' ); ?>><?php _e( 'px (기본)', 'acf-css-really-simple-style-management-center' ); ?></option>
                    <option value="rem" <?php selected( $typo_unit, 'rem' ); ?>><?php _e( 'rem (권장)', 'acf-css-really-simple-style-management-center' ); ?></option>
                    <option value="em" <?php selected( $typo_unit, 'em' ); ?>><?php _e( 'em', 'acf-css-really-simple-style-management-center' ); ?></option>
                </select>
                <p class="description"><?php _e( 'rem은 반응형에서 예측 가능성이 높아 권장됩니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>
        </div>

        <div style="margin-top: 10px;">
            <p class="description" style="margin:0;">
                <?php _e( '브레이크포인트(기본값):', 'acf-css-really-simple-style-management-center' ); ?>
                <?php
                $bp_parts = array();
                foreach ( $device_order as $k ) {
                    if ( ! isset( $breakpoints[ $k ] ) || ! is_array( $breakpoints[ $k ] ) ) continue;
                    $bp = $breakpoints[ $k ];
                    $label = $bp['label'] ?? $k;
                    $range = '';
                    if ( isset( $bp['min'] ) && isset( $bp['max'] ) ) {
                        $range = sprintf( '%d~%dpx', (int) $bp['min'], (int) $bp['max'] );
                    } elseif ( isset( $bp['max'] ) ) {
                        $range = sprintf( '≤%dpx', (int) $bp['max'] );
                    } elseif ( isset( $bp['min'] ) ) {
                        $range = sprintf( '≥%dpx', (int) $bp['min'] );
                    }
                    $bp_parts[] = esc_html( $label . ( $range ? ' (' . $range . ')' : '' ) );
                }
                echo implode( ' · ', $bp_parts );
                ?>
            </p>
        </div>
    </div>

    <div class="jj-fieldset-group jj-typography-presets-panel" style="margin-bottom: 30px;">
        <h3><?php _e( '타이포그래피 프리셋 (1-click)', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <p class="description">
            <?php _e( '원하는 스타일을 선택하면 폰트 패밀리/굵기/행간/자간/반응형 폰트 크기가 한 번에 채워집니다. 적용 후에는 상단의 “스타일 저장”을 눌러야 최종 저장됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>
        <div id="jj-typography-presets"></div>
    </div>

    <?php
    // 커스텀 웹 폰트 및 매핑 영역
    $font_roles = array(
        'korean' => array(
            'label' => __( '한국어 기본 폰트', 'acf-css-really-simple-style-management-center' ),
            'description' => __( '주로 한국어 텍스트에 사용할 기본 폰트입니다. 본문/제목 설정과 함께 사용하거나, 커스텀 CSS에서 var(--jj-font-ko-family)를 참조할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
        ),
        'english' => array(
            'label' => __( '영문 기본 폰트', 'acf-css-really-simple-style-management-center' ),
            'description' => __( '주로 영문 텍스트에 사용할 기본 폰트입니다. 커스텀 CSS에서 var(--jj-font-en-family)를 참조할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
        ),
        'buttons' => array(
            'label' => __( '버튼 전용 폰트', 'acf-css-really-simple-style-management-center' ),
            'description' => __( '버튼에 사용할 전용 폰트입니다. 전역 버튼 스타일에서 var(--jj-font-button-family)를 사용해 적용됩니다.', 'acf-css-really-simple-style-management-center' ),
        ),
        'forms' => array(
            'label' => __( '폼/입력 필드 전용 폰트', 'acf-css-really-simple-style-management-center' ),
            'description' => __( '폼 라벨 및 입력 필드에 사용할 전용 폰트입니다. 전역 폼 스타일에서 var(--jj-font-form-family)를 사용해 적용됩니다.', 'acf-css-really-simple-style-management-center' ),
        ),
    );
    ?>

    <div class="jj-fieldset-group jj-custom-fonts-panel" style="margin-bottom: 30px;">
        <h3><?php _e( '커스텀 웹 폰트 및 매핑', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <p class="description">
            <?php _e( '아래에서 한국어/영문/버튼/폼에 사용할 웹 폰트를 지정할 수 있습니다. 폰트 파일은 미디어 라이브러리에 업로드한 뒤, 각 역할(Role)에 연결해 주세요.', 'acf-css-really-simple-style-management-center' ); ?>
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
                        <?php _e( 'Font Family 이름', 'acf-css-really-simple-style-management-center' ); ?>
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
                            placeholder="<?php esc_attr_e( '선택된 폰트 파일 없음', 'acf-css-really-simple-style-management-center' ); ?>"
                            style="width:100%; margin-bottom:4px; background:#f9f9f9;"
                        />
                        <button type="button" class="button jj-font-upload-button">
                            <?php _e( '폰트 파일 선택/변경', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>

                    <label for="jj-font-<?php echo esc_attr( $role_key ); ?>-format" style="margin-top:8px; display:block;">
                        <?php _e( '포맷 (선택 사항)', 'acf-css-really-simple-style-management-center' ); ?>
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
                <?php _e( '추가 커스텀 CSS (@font-face 등)', 'acf-css-really-simple-style-management-center' ); ?>
            </label>
            <p class="description">
                <?php _e( '필요하다면 @font-face 규칙이나 추가 타이포그래피 CSS를 여기에 직접 입력할 수 있습니다. 위에서 지정한 폰트 변수(var(--jj-font-ko-family), var(--jj-font-en-family) 등)도 함께 사용할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
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

    <!-- [Phase 12] 폰트 추천 -->
    <div class="jj-fieldset-group jj-font-recommender-panel" style="margin-bottom: 30px;">
        <h3><?php _e( '폰트 추천 (원클릭 적용)', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <p class="description">
            <?php _e( '무료 Google Fonts 및 Pretendard를 원클릭으로 적용할 수 있습니다. 폰트 이름을 클릭하면 바로 적용됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>
        <div id="jj-font-recommender-container" style="margin-top: 16px;">
            <p class="jj-font-loading"><?php _e( '폰트 목록 불러오는 중...', 'acf-css-really-simple-style-management-center' ); ?></p>
        </div>
        <div class="jj-font-guide" style="margin-top: 16px; padding: 14px; background: #f8fafc; border: 1px solid #c3c4c7; border-radius: 10px;">
            <h4 style="margin: 0 0 10px 0; font-size: 14px;"><?php _e( '폰트 설치 가이드', 'acf-css-really-simple-style-management-center' ); ?></h4>
            <ul style="margin: 0; padding-left: 18px; font-size: 13px; line-height: 1.7;">
                <li><strong><?php _e( '원클릭 적용', 'acf-css-really-simple-style-management-center' ); ?>:</strong> <?php _e( '폰트 카드의 "적용" 버튼을 클릭하면 즉시 사이트에 적용됩니다.', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><strong><?php _e( '수동 설치', 'acf-css-really-simple-style-management-center' ); ?>:</strong> <?php _e( '다운로드 후 미디어 라이브러리에 업로드 → 위 커스텀 폰트 섹션에서 연결', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><strong><?php _e( '유료 폰트', 'acf-css-really-simple-style-management-center' ); ?>:</strong> <?php _e( '해당 제공업체에서 라이선스 구매 후 수동 설치', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><strong><?php _e( '성능 팁', 'acf-css-really-simple-style-management-center' ); ?>:</strong> <?php _e( '한국어 폰트는 subset 사용, WOFF2 형식 권장', 'acf-css-really-simple-style-management-center' ); ?></li>
            </ul>
        </div>
    </div>

    <?php foreach ( $tags as $tag ) : 
        $settings = $typography_options[$tag] ?? array();
        $font_family = $settings['font_family'] ?? '';
        $font_weight = $settings['font_weight'] ?? '400';
        $font_style = $settings['font_style'] ?? 'normal';
        $font_sizes = isset( $settings['font_size'] ) && is_array( $settings['font_size'] ) ? $settings['font_size'] : array();
        $line_height = $settings['line_height'] ?? '';
        $letter_spacing = $settings['letter_spacing'] ?? '';
        $text_transform = $settings['text_transform'] ?? '';
    ?>
        <div class="jj-typography-row jj-section-subsection" data-type="<?php echo esc_attr( $tag ); ?>" id="jj-typography-<?php echo esc_attr( $tag ); ?>">
            <!-- [v22.4.7] 실시간 미리보기 추가 -->
            <div class="jj-typography-preview" data-tag="<?php echo esc_attr( $tag ); ?>" style="margin-bottom: 20px; padding: 20px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px;">
                <div class="jj-typography-preview-label"><?php echo esc_html( strtoupper( $tag ) ); ?> 미리보기</div>
                <<?php echo esc_attr( $tag ); ?> style="margin: 0; font-family: <?php echo esc_attr( $font_family ?: 'inherit' ); ?>; font-weight: <?php echo esc_attr( $font_weight ); ?>; font-style: <?php echo esc_attr( $font_style ); ?>; line-height: <?php echo esc_attr( $line_height ?: '1.5' ); ?>; letter-spacing: <?php echo esc_attr( $letter_spacing ? $letter_spacing . 'px' : 'normal' ); ?>; font-size: <?php echo esc_attr( isset( $font_sizes['desktop'] ) && $font_sizes['desktop'] ? $font_sizes['desktop'] . 'px' : 'inherit' ); ?>; text-transform: <?php echo esc_attr( $text_transform ?: 'none' ); ?>;">
                    <?php 
                    $preview_texts = array(
                        'h1' => '제목 1 스타일 미리보기',
                        'h2' => '제목 2 스타일 미리보기',
                        'h3' => '제목 3 스타일 미리보기',
                        'h4' => '제목 4 스타일 미리보기',
                        'h5' => '제목 5 스타일 미리보기',
                        'h6' => '제목 6 스타일 미리보기',
                        'p' => '본문 텍스트 스타일 미리보기입니다. 이 텍스트가 실제로 어떻게 보이는지 확인할 수 있습니다.',
                    );
                    echo esc_html( $preview_texts[ $tag ] ?? '미리보기 텍스트' );
                    ?>
                </<?php echo esc_attr( $tag ); ?>>
            </div>
            
            <div class="tag"><?php echo esc_html( strtoupper( $tag ) ); ?></div>
            
            <div class="controls jj-typography-controls">
                
                <div class="jj-control-group">
                    <label for="jj-font-family-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Font Family', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-font-family-<?php echo esc_attr( $tag ); ?>" 
                           class="jj-data-field" 
                           data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][font_family]"
                           value="<?php echo esc_attr( $font_family ); ?>"
                           placeholder="Inter, 'Noto Sans KR', sans-serif" />
                </div>
                
                <div class="jj-control-group">
                    <label for="jj-font-weight-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Weight', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <select id="jj-font-weight-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][font_weight]">
                        <?php foreach ( $font_weights as $weight => $label ) : ?>
                            <option value="<?php echo esc_attr( $weight ); ?>" <?php selected( $font_weight, $weight ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="jj-control-group">
                    <label for="jj-font-style-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Style', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <select id="jj-font-style-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][font_style]">
                        <?php foreach ( $font_styles as $style => $label ) : ?>
                            <option value="<?php echo esc_attr( $style ); ?>" <?php selected( $font_style, $style ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="jj-control-group">
                    <label for="jj-line-height-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Line Height (em)', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="number" 
                           step="0.1"
                           id="jj-line-height-<?php echo esc_attr( $tag ); ?>" 
                           class="jj-data-field" 
                           data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][line_height]"
                           value="<?php echo esc_attr( $line_height ); ?>"
                           placeholder="예: 1.5" />
                </div>

                <div class="jj-control-group">
                    <label for="jj-letter-spacing-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Letter Spacing (px)', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="number" 
                           step="0.1"
                           id="jj-letter-spacing-<?php echo esc_attr( $tag ); ?>" 
                           class="jj-data-field" 
                           data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][letter_spacing]"
                           value="<?php echo esc_attr( $letter_spacing ); ?>"
                           placeholder="예: -0.1" />
                </div>

                <div class="jj-control-group">
                    <label for="jj-text-transform-<?php echo esc_attr( $tag ); ?>"><?php _e( 'Transform', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <select id="jj-text-transform-<?php echo esc_attr( $tag ); ?>" class="jj-data-field" data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][text_transform]">
                        <?php foreach ( $text_transforms as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $text_transform, $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="jj-control-group jj-responsive-control">
                    <div class="jj-responsive-tabs">
                        <?php
                        $first_device = null;
                        foreach ( $device_order as $device_key ) :
                            // 데스크톱은 항상 표시, 나머지는 브레이크포인트가 있으면 표시
                            if ( 'desktop' !== $device_key && ! isset( $breakpoints[ $device_key ] ) ) {
                                continue;
                            }
                            if ( null === $first_device ) {
                                $first_device = $device_key;
                            }
                            $tab_label = $device_tab_labels[ $device_key ] ?? strtoupper( substr( $device_key, 0, 1 ) );
                            $title = isset( $breakpoints[ $device_key ]['label'] ) ? $breakpoints[ $device_key ]['label'] : $device_key;
                            ?>
                            <button type="button"
                                    class="jj-responsive-tab-button <?php echo ( $first_device === $device_key ) ? 'is-active' : ''; ?>"
                                    data-device="<?php echo esc_attr( $device_key ); ?>"
                                    title="<?php echo esc_attr( $title ); ?>">
                                <?php echo esc_html( $tab_label ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php
                    $content_first = true;
                    foreach ( $device_order as $device_key ) :
                        if ( 'desktop' !== $device_key && ! isset( $breakpoints[ $device_key ] ) ) {
                            continue;
                        }
                        $bp = $breakpoints[ $device_key ] ?? array();
                        $label = isset( $bp['label'] ) ? $bp['label'] : $device_key;
                        $range = '';
                        if ( isset( $bp['min'] ) && isset( $bp['max'] ) ) {
                            $range = sprintf( '%d~%dpx', (int) $bp['min'], (int) $bp['max'] );
                        } elseif ( isset( $bp['max'] ) ) {
                            $range = sprintf( '≤%dpx', (int) $bp['max'] );
                        } elseif ( isset( $bp['min'] ) ) {
                            $range = sprintf( '≥%dpx', (int) $bp['min'] );
                        }
                        $value = $font_sizes[ $device_key ] ?? '';
                        $ph = $recommended_sizes[ $tag ][ $device_key ] ?? '';
                        ?>
                        <div class="jj-responsive-tab-content <?php echo $content_first ? 'is-active' : ''; ?>" data-device="<?php echo esc_attr( $device_key ); ?>">
                            <label for="jj-font-size-<?php echo esc_attr( $tag ); ?>-<?php echo esc_attr( $device_key ); ?>">
                                <?php echo esc_html( sprintf( __( 'Size (%s)', 'acf-css-really-simple-style-management-center' ), $label ) ); ?>
                                <?php if ( $range ) : ?>
                                    <span style="font-size: 11px; color: #666; margin-left: 6px;"><?php echo esc_html( $range ); ?></span>
                                <?php endif; ?>
                            </label>
                            <input type="number"
                                   id="jj-font-size-<?php echo esc_attr( $tag ); ?>-<?php echo esc_attr( $device_key ); ?>"
                                   class="jj-data-field"
                                   data-setting-key="typography[<?php echo esc_attr( $tag ); ?>][font_size][<?php echo esc_attr( $device_key ); ?>]"
                                   value="<?php echo esc_attr( $value ); ?>"
                                   placeholder="<?php echo $ph ? esc_attr( $ph ) : ''; ?>" />
                            <?php if ( $ph ) : ?>
                                <p class="description" style="margin-top:6px;"><?php echo esc_html( sprintf( __( '권장: %spx', 'acf-css-really-simple-style-management-center' ), $ph ) ); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php
                        $content_first = false;
                    endforeach;
                    ?>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
</div>