<?php
/**
 * [v3.5.0-dev5 '제련'] '1. 팔레트 시스템' 섹션 UI 템플릿
 * - [신규] '5. 실험실 (Labs)' '탭' '버튼' '추가'
 * - [수정] 탭 번호 '재정렬'
 */
if ( ! defined( 'ABSPATH' ) ) exit; // 직접 접근 차단

// [v3.1 '신규'] '시스템' 팔레트 옵션 로드
$system_palettes = $options['palettes']['system'] ?? array();

// [v3.8.0] 버전별 기능 제한 체크
$version_features = class_exists( 'JJ_Version_Features' ) ? JJ_Version_Features::instance() : null;

// [v5.0.0] 탭 활성화/비활성화 체크 (안전하게 처리)
$tabs_layout = array();
if ( function_exists( 'jj_style_guide_sections_layout' ) ) {
    $section_layout = jj_style_guide_sections_layout();
    if ( isset( $section_layout['colors'] ) && is_array( $section_layout['colors'] ) ) {
        $colors_layout = $section_layout['colors'];
        $tabs_layout = isset( $colors_layout['tabs'] ) && is_array( $colors_layout['tabs'] ) ? $colors_layout['tabs'] : array();
    }
}
?>

<div class="jj-section-global" id="jj-section-palettes">
    <h2 class="jj-section-title">
        <?php
        $jj_section_index = function_exists( 'jj_style_guide_section_index' ) ? jj_style_guide_section_index( 'colors' ) : null;
        if ( $jj_section_index ) :
            ?>
            <span class="jj-section-index"><?php echo intval( $jj_section_index ); ?>.</span>
        <?php endif; ?>
        <?php echo esc_html( jj_style_guide_text( 'section_palettes_title', __( '팔레트 시스템 (Palette System)', 'acf-css-really-simple-style-management-center' ) ) ); ?>
    </h2>
    <p class="description">
        <?php
        echo wp_kses_post(
            jj_style_guide_text(
                'section_palettes_description',
                __(
                    '웹사이트 전반에서 사용할 기본 색상을 정리하는 공간입니다. <strong>브랜드 팔레트</strong>는 버튼, 폼, 링크 등 주요 요소의 기준이 되며, <strong>시스템 팔레트</strong>는 사이트의 배경과 본문 텍스트, 링크 색상을 차분하게 정렬해 줍니다. 필요하다면 얼터너티브/임시 팔레트를 활용해 실험적인 조합도 안전하게 시도하실 수 있습니다.',
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
            $tab_enabled_brand = ! isset( $tabs_layout['brand'] ) || ! empty( $tabs_layout['brand']['enabled'] );
            $tab_enabled_system = ( ! isset( $tabs_layout['system'] ) || ! empty( $tabs_layout['system']['enabled'] ) ) && ( ! $version_features || $version_features->can_use_palette( 'system' ) );
            $tab_enabled_alternative = ( ! isset( $tabs_layout['alternative'] ) || ! empty( $tabs_layout['alternative']['enabled'] ) ) && ( ! $version_features || $version_features->can_use_palette( 'alternative' ) );
            $tab_enabled_another = ( ! isset( $tabs_layout['another'] ) || ! empty( $tabs_layout['another']['enabled'] ) ) && ( ! $version_features || $version_features->can_use_palette( 'another' ) );
            $tab_enabled_temp = ( ! isset( $tabs_layout['temp-palette'] ) || ! empty( $tabs_layout['temp-palette']['enabled'] ) ) && ( ! $version_features || $version_features->can_use_palette( 'temp' ) );
            
            // 활성화된 탭이 하나도 없으면 브랜드 팔레트를 기본으로 활성화
            $has_enabled_tabs = $tab_enabled_brand || $tab_enabled_system || $tab_enabled_alternative || $tab_enabled_another || $tab_enabled_temp;
            if ( ! $has_enabled_tabs ) {
                $tab_enabled_brand = true;
            }
            
            $first_enabled_tab = $tab_enabled_brand ? 'brand' : ( $tab_enabled_system ? 'system' : ( $tab_enabled_alternative ? 'alternative' : ( $tab_enabled_another ? 'another' : 'temp-palette' ) ) );
            ?>
            <?php if ( $tab_enabled_brand ) : ?>
            <button type="button" class="jj-tab-button <?php echo ( $first_enabled_tab === 'brand' ) ? 'is-active' : ''; ?>" data-tab="brand">
                <?php _e( '1. 브랜드 팔레트', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <?php endif; ?>
            <?php if ( $tab_enabled_system ) : ?>
            <button type="button" class="jj-tab-button <?php echo ( $first_enabled_tab === 'system' ) ? 'is-active' : ''; ?>" data-tab="system">
                <?php _e( '2. 시스템 팔레트', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <?php endif; ?>
            <?php if ( $tab_enabled_alternative ) : ?>
            <button type="button" class="jj-tab-button <?php echo ( $first_enabled_tab === 'alternative' ) ? 'is-active' : ''; ?>" data-tab="alternative">
                <?php _e( '3. 얼터너티브 팔레트', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <?php endif; ?>
            <?php if ( $tab_enabled_another ) : ?>
            <button type="button" class="jj-tab-button <?php echo ( $first_enabled_tab === 'another' ) ? 'is-active' : ''; ?>" data-tab="another">
                <?php _e( '4. 어나더 팔레트', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <?php endif; ?>
            <?php if ( $tab_enabled_temp ) : ?>
            <button type="button" class="jj-tab-button jj-tab-button-temp <?php echo ( $first_enabled_tab === 'temp-palette' ) ? 'is-active' : ''; ?>" data-tab="temp-palette">
                <?php _e( '5. 임시 팔레트', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <?php endif; ?>
        </div>

        <?php if ( $tab_enabled_brand ) : ?>
        <div class="jj-tab-content <?php echo ( $first_enabled_tab === 'brand' ) ? 'is-active' : ''; ?>" data-tab-content="brand">
            <?php if ( ! $version_features || $version_features->can_use_section_refresh() ) : ?>
            <div class="jj-section-refresh-control" style="margin-bottom: 20px; padding: 15px; background: #f0f6fc; border: 1px solid #c3c4c7; border-radius: 4px;">
                <button type="button" class="button button-secondary jj-refresh-colors" data-palette-type="brand" style="margin-bottom: 8px;">
                    <span class="dashicons dashicons-update" style="margin-top: 4px;"></span>
                    <?php _e( '새로고침', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
                <p class="description" style="margin: 0; font-size: 13px;">
                    <?php _e( '새로고침을 눌러 현재 Customizer에서 설정된 브랜드 팔레트 색상을 한꺼번에 불러옵니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <span class="spinner" style="float: none; margin-left: 10px;"></span>
            </div>
            <?php endif; ?>

            <!-- [UI/UX] 빠른 시작: 추천 팔레트 + 인라인 프리뷰 -->
            <div class="jj-palette-quickstart">
                <h3><?php _e( '빠른 시작: 추천 팔레트', 'acf-css-really-simple-style-management-center' ); ?></h3>
                <p class="description" style="margin-top: 6px;">
                    <?php _e( '텍스트 입력보다 “보이는 팔레트”로 시작하세요. 프리셋을 선택하고 적용하면, 버튼/링크/배경/텍스트 조합이 즉시 정렬됩니다. (언제든 개별 필드에서 미세 조정 가능)', 'acf-css-really-simple-style-management-center' ); ?>
                </p>

                <div id="jj-palette-presets"></div>

                <div id="jj-palette-inline-preview" class="jj-palette-inline-preview" aria-label="<?php esc_attr_e( '팔레트 미리보기', 'acf-css-really-simple-style-management-center' ); ?>">
                    <div class="jj-prev-surface">
                        <div class="jj-prev-title"><?php _e( '실시간 미리보기', 'acf-css-really-simple-style-management-center' ); ?></div>
                        <p class="jj-prev-desc">
                            <?php _e( '배경/본문/링크/버튼이 실제로 어떻게 보이는지 이 카드에서 즉시 확인하세요. 링크는 ', 'acf-css-really-simple-style-management-center' ); ?>
                            <a href="#" onclick="return false;"><?php _e( '이런 느낌', 'acf-css-really-simple-style-management-center' ); ?></a>
                            <?php _e( '입니다.', 'acf-css-really-simple-style-management-center' ); ?>
                        </p>
                        <div class="jj-prev-actions">
                            <button type="button" class="jj-prev-btn primary" onclick="return false;"><?php _e( 'Primary 버튼', 'acf-css-really-simple-style-management-center' ); ?></button>
                            <button type="button" class="jj-prev-btn secondary" onclick="return false;"><?php _e( 'Secondary 버튼', 'acf-css-really-simple-style-management-center' ); ?></button>
                        </div>
                        <div class="jj-prev-kv">
                            <span><?php _e( 'Primary', 'acf-css-really-simple-style-management-center' ); ?> <code data-jj-color="primary">#</code></span>
                            <span><?php _e( 'Secondary', 'acf-css-really-simple-style-management-center' ); ?> <code data-jj-color="secondary">#</code></span>
                            <span><?php _e( 'BG', 'acf-css-really-simple-style-management-center' ); ?> <code data-jj-color="bg">#</code></span>
                            <span><?php _e( 'Text', 'acf-css-really-simple-style-management-center' ); ?> <code data-jj-color="text">#</code></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="jj-style-guide-grid jj-grid-4-col">
                <div class="jj-control-group jj-color-card">
                    <label for="jj-brand-primary_color">Primary Color</label>
                    <input type="text" 
                           id="jj-brand-primary_color" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[brand][primary_color]" 
                           value="<?php echo esc_attr( $options['palettes']['brand']['primary_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <p class="description">
                        <?php _e( '브랜드의 핵심 색상입니다. 버튼, 링크, 폼 등 주요 구성 요소의 기본 기준이 됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-brand-primary_color_hover">Primary Color (Hover)</label>
                    <input type="text" 
                           id="jj-brand-primary_color_hover" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[brand][primary_color_hover]" 
                           value="<?php echo esc_attr( $options['palettes']['brand']['primary_color_hover'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-brand-secondary_color">Secondary Color</label>
                    <input type="text" 
                           id="jj-brand-secondary_color" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[brand][secondary_color]" 
                           value="<?php echo esc_attr( $options['palettes']['brand']['secondary_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-brand-secondary_color_hover">Secondary Color (Hover)</label>
                    <input type="text" 
                           id="jj-brand-secondary_color_hover" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[brand][secondary_color_hover]" 
                           value="<?php echo esc_attr( $options['palettes']['brand']['secondary_color_hover'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                </div>
            </div>

            <div class="jj-fieldset-group jj-brand-palette-tools" style="margin-top: 24px;">
                <h3><?php _e( '브랜드 팔레트 일괄 적용', 'acf-css-really-simple-style-management-center' ); ?></h3>
                <p class="description">
                    <?php _e( '아래 버튼을 사용하면 현재 설정된 브랜드 팔레트 값을 기반으로 버튼, 폼, 링크 색상을 한 번에 맞출 수 있습니다. 적용 후에도 세부 값은 개별 섹션에서 자유롭게 조정하실 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <button type="button" class="button button-secondary jj-apply-brand-palette-to-components">
                    <?php _e( '브랜드 팔레트 기준으로 버튼/폼/링크 색상 맞추기', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
                <span class="spinner"></span>
            </div>

            <?php if ( ! $version_features || $version_features->can_use_section_export_import() ) : ?>
            <div class="jj-section-export-import-control" style="margin-top: 24px; padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
                <h3 style="margin-top: 0;"><?php _e( '브랜드 팔레트 내보내기/불러오기', 'acf-css-really-simple-style-management-center' ); ?></h3>
                <p class="description" style="margin-bottom: 10px;">
                    <?php _e( '브랜드 팔레트 설정만 선택적으로 내보내거나 불러올 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="button" class="button button-secondary jj-export-section" data-section-type="palette" data-section-subtype="brand">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                        <?php _e( '브랜드 팔레트 내보내기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-section" data-section-type="palette" data-section-subtype="brand">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span>
                        <?php _e( '브랜드 팔레트 불러오기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                </div>
                <input type="file" class="jj-section-import-file" data-section-type="palette" data-section-subtype="brand" accept=".json" style="display: none;">
                <span class="spinner" style="float: none; margin-left: 10px;"></span>
            </div>
            <?php elseif ( $version_features ) : ?>
            <div class="jj-upgrade-prompt" style="margin-top: 24px; padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
                <p class="description" style="margin: 0;">
                    <?php echo wp_kses_post( $version_features->get_upgrade_prompt( 'export_import_section' ) ); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( $tab_enabled_system ) : ?>
        <div class="jj-tab-content <?php echo ( $first_enabled_tab === 'system' ) ? 'is-active' : ''; ?>" data-tab-content="system">
            <?php if ( ! $version_features || $version_features->can_use_section_refresh() ) : ?>
            <div class="jj-section-refresh-control" style="margin-bottom: 20px; padding: 15px; background: #f0f6fc; border: 1px solid #c3c4c7; border-radius: 4px;">
                <button type="button" class="button button-secondary jj-refresh-colors" data-palette-type="system" style="margin-bottom: 8px;">
                    <span class="dashicons dashicons-update" style="margin-top: 4px;"></span>
                    <?php _e( '새로고침', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
                <p class="description" style="margin: 0; font-size: 13px;">
                    <?php _e( '새로고침을 눌러 현재 Customizer에서 설정된 시스템 팔레트 색상을 한꺼번에 불러옵니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <span class="spinner" style="float: none; margin-left: 10px;"></span>
            </div>
            <?php endif; ?>
            <div class="jj-style-guide-grid jj-grid-4-col">
                <div class="jj-control-group jj-color-card">
                    <label for="jj-sys-site_bg"><?php _e( '사이트 배경 (Site Background)', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-sys-site_bg" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[system][site_bg]" 
                           value="<?php echo esc_attr( $system_palettes['site_bg'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <p class="description">
                        <?php
                        /* translators: %s: active theme name */
                        printf(
                            __( 'body 태그의 배경색입니다. (예: 현재 활성화된 \'%s\' 테마의 사이트 배경 색상입니다. \'사용자 정의 > 색상\'에서 확인하실 수 있습니다.)', 'acf-css-really-simple-style-management-center' ),
                            esc_html( $jj_active_theme_name ?? '' )
                        );
                        ?>
                    </p>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-sys-content_bg"><?php _e( '콘텐츠 배경 (Content Background)', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-sys-content_bg" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[system][content_bg]" 
                           value="<?php echo esc_attr( $system_palettes['content_bg'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <p class="description">
                        <?php
                        /* translators: %s: active theme name */
                        printf(
                            __( '메인 콘텐츠 영역의 배경색입니다. (예: 현재 활성화된 \'%s\' 테마의 콘텐츠/본문 배경 색상입니다. \'사용자 정의 > 색상\'에서 확인하실 수 있습니다.)', 'acf-css-really-simple-style-management-center' ),
                            esc_html( $jj_active_theme_name ?? '' )
                        );
                        ?>
                    </p>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-sys-text"><?php _e( '본문 텍스트 (Base Text)', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-sys-text" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[system][text_color]" 
                           value="<?php echo esc_attr( $system_palettes['text_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <p class="description">
                        <?php
                        /* translators: %s: active theme name */
                        printf(
                            __( '기본 텍스트(p, li 등)의 색상입니다. (예: 현재 활성화된 \'%s\' 테마의 기본 텍스트/본문 색상입니다. \'사용자 정의 > 색상\'에서 확인하실 수 있습니다.)', 'acf-css-really-simple-style-management-center' ),
                            esc_html( $jj_active_theme_name ?? '' )
                        );
                        ?>
                    </p>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-sys-link"><?php _e( '링크 (Links Color)', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-sys-link" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[system][link_color]" 
                           value="<?php echo esc_attr( $system_palettes['link_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <p class="description">
                        <?php
                        /* translators: %s: active theme name */
                        printf(
                            __( '기본 링크(a) 태그의 색상입니다. (예: 현재 활성화된 \'%s\' 테마의 링크/포인트 색상입니다. \'사용자 정의 > 색상\'에서 확인하실 수 있습니다.)', 'acf-css-really-simple-style-management-center' ),
                            esc_html( $jj_active_theme_name ?? '' )
                        );
                        ?>
                    </p>
                </div>
            </div>

            <?php if ( ! $version_features || $version_features->can_use_section_export_import() ) : ?>
            <div class="jj-section-export-import-control" style="margin-top: 24px; padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
                <h3 style="margin-top: 0;"><?php _e( '시스템 팔레트 내보내기/불러오기', 'acf-css-really-simple-style-management-center' ); ?></h3>
                <p class="description" style="margin-bottom: 10px;">
                    <?php _e( '시스템 팔레트 설정만 선택적으로 내보내거나 불러올 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="button" class="button button-secondary jj-export-section" data-section-type="palette" data-section-subtype="system">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                        <?php _e( '시스템 팔레트 내보내기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-section" data-section-type="palette" data-section-subtype="system">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span>
                        <?php _e( '시스템 팔레트 불러오기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                </div>
                <input type="file" class="jj-section-import-file" data-section-type="palette" data-section-subtype="system" accept=".json" style="display: none;">
                <span class="spinner" style="float: none; margin-left: 10px;"></span>
            </div>
            <?php elseif ( $version_features ) : ?>
            <div class="jj-upgrade-prompt" style="margin-top: 24px; padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
                <p class="description" style="margin: 0;">
                    <?php echo wp_kses_post( $version_features->get_upgrade_prompt( 'export_import_section' ) ); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( $tab_enabled_alternative ) : ?>
        <div class="jj-tab-content <?php echo ( $first_enabled_tab === 'alternative' ) ? 'is-active' : ''; ?>" data-tab-content="alternative">
            <?php
            // 브랜드/시스템 팔레트 값 가져오기
            $brand_palettes = $options['palettes']['brand'] ?? array();
            $alternative_palettes = $options['palettes']['alternative'] ?? array();
            
            // 다크 모드/라이트 모드 색상 계산 함수
            if ( ! function_exists( 'jj_calculate_dark_mode_color' ) ) {
                function jj_calculate_dark_mode_color( $hex ) {
                    if ( empty( $hex ) ) return '';
                    $hex = str_replace( '#', '', $hex );
                    if ( strlen( $hex ) !== 6 ) return '';
                    $r = hexdec( substr( $hex, 0, 2 ) );
                    $g = hexdec( substr( $hex, 2, 2 ) );
                    $b = hexdec( substr( $hex, 4, 2 ) );
                    // 어두운 색상으로 변환 (20% 어둡게)
                    $r = max( 0, min( 255, $r * 0.8 ) );
                    $g = max( 0, min( 255, $g * 0.8 ) );
                    $b = max( 0, min( 255, $b * 0.8 ) );
                    return '#' . str_pad( dechex( round( $r ) ), 2, '0', STR_PAD_LEFT ) . 
                           str_pad( dechex( round( $g ) ), 2, '0', STR_PAD_LEFT ) . 
                           str_pad( dechex( round( $b ) ), 2, '0', STR_PAD_LEFT );
                }
            }
            if ( ! function_exists( 'jj_calculate_light_mode_color' ) ) {
                function jj_calculate_light_mode_color( $hex ) {
                    if ( empty( $hex ) ) return '';
                    $hex = str_replace( '#', '', $hex );
                    if ( strlen( $hex ) !== 6 ) return '';
                    $r = hexdec( substr( $hex, 0, 2 ) );
                    $g = hexdec( substr( $hex, 2, 2 ) );
                    $b = hexdec( substr( $hex, 4, 2 ) );
                    // 밝은 색상으로 변환 (20% 밝게)
                    $r = max( 0, min( 255, $r + (255 - $r) * 0.2 ) );
                    $g = max( 0, min( 255, $g + (255 - $g) * 0.2 ) );
                    $b = max( 0, min( 255, $b + (255 - $b) * 0.2 ) );
                    return '#' . str_pad( dechex( round( $r ) ), 2, '0', STR_PAD_LEFT ) . 
                           str_pad( dechex( round( $g ) ), 2, '0', STR_PAD_LEFT ) . 
                           str_pad( dechex( round( $b ) ), 2, '0', STR_PAD_LEFT );
                }
            }
            ?>
            <div class="jj-alternative-palette-info" style="margin-bottom: 24px; padding: 15px; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 4px;">
                <h3 style="margin-top: 0;"><?php _e( '브랜드/시스템 팔레트 참조', 'acf-css-really-simple-style-management-center' ); ?></h3>
                <p class="description" style="margin-bottom: 15px;">
                    <?php _e( '현재 설정된 브랜드 및 시스템 팔레트 값을 참고하여 대안 색상을 설정할 수 있습니다. 다크 모드/라이트 모드 색상은 자동 계산되며, 아래 버튼을 클릭하여 적용하거나 수동으로 입력할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <div class="jj-palette-reference" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <?php if ( ! empty( $brand_palettes['primary_color'] ) ) : ?>
                    <div style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 3px;">
                        <strong><?php _e( '브랜드 Primary', 'acf-css-really-simple-style-management-center' ); ?></strong>
                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                            <div style="width: 30px; height: 30px; background: <?php echo esc_attr( $brand_palettes['primary_color'] ); ?>; border: 1px solid #ddd; border-radius: 3px;"></div>
                            <code style="font-size: 12px;"><?php echo esc_html( $brand_palettes['primary_color'] ); ?></code>
                        </div>
                        <button type="button" class="button button-small jj-copy-color" data-color="<?php echo esc_attr( $brand_palettes['primary_color'] ); ?>" style="margin-top: 5px; font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '복사', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $system_palettes['site_bg'] ) ) : ?>
                    <div style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 3px;">
                        <strong><?php _e( '시스템 Site BG', 'acf-css-really-simple-style-management-center' ); ?></strong>
                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                            <div style="width: 30px; height: 30px; background: <?php echo esc_attr( $system_palettes['site_bg'] ); ?>; border: 1px solid #ddd; border-radius: 3px;"></div>
                            <code style="font-size: 12px;"><?php echo esc_html( $system_palettes['site_bg'] ); ?></code>
                        </div>
                        <button type="button" class="button button-small jj-copy-color" data-color="<?php echo esc_attr( $system_palettes['site_bg'] ); ?>" style="margin-top: 5px; font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '복사', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="jj-style-guide-grid jj-grid-4-col">
                <div class="jj-control-group jj-color-card">
                    <label for="jj-alt-primary"><?php _e( 'Primary Color', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-alt-primary" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[alternative][primary_color]" 
                           value="<?php echo esc_attr( $alternative_palettes['primary_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['primary_color'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color'] ); ?>"
                                data-target="#jj-alt-primary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color'] ); ?>"
                                data-target="#jj-alt-primary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <p class="description" style="margin-top: 8px;">
                        <?php _e( '브랜드 Primary의 대안 색상입니다. 다크 모드/라이트 모드 또는 특정 페이지용으로 사용할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-alt-primary-hover"><?php _e( 'Primary Color (Hover)', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-alt-primary-hover" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[alternative][primary_color_hover]" 
                           value="<?php echo esc_attr( $alternative_palettes['primary_color_hover'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['primary_color_hover'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color_hover'] ); ?>"
                                data-target="#jj-alt-primary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color_hover'] ); ?>"
                                data-target="#jj-alt-primary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-alt-secondary"><?php _e( 'Secondary Color', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-alt-secondary" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[alternative][secondary_color]" 
                           value="<?php echo esc_attr( $alternative_palettes['secondary_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['secondary_color'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color'] ); ?>"
                                data-target="#jj-alt-secondary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color'] ); ?>"
                                data-target="#jj-alt-secondary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-alt-secondary-hover"><?php _e( 'Secondary Color (Hover)', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-alt-secondary-hover" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[alternative][secondary_color_hover]" 
                           value="<?php echo esc_attr( $alternative_palettes['secondary_color_hover'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['secondary_color_hover'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color_hover'] ); ?>"
                                data-target="#jj-alt-secondary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color_hover'] ); ?>"
                                data-target="#jj-alt-secondary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ( $tab_enabled_another ) : ?>
        <div class="jj-tab-content <?php echo ( $first_enabled_tab === 'another' ) ? 'is-active' : ''; ?>" data-tab-content="another">
            <?php
            $another_palettes = $options['palettes']['another'] ?? array();
            ?>
            <div class="jj-alternative-palette-info" style="margin-bottom: 24px; padding: 15px; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 4px;">
                <h3 style="margin-top: 0;"><?php _e( '브랜드/시스템 팔레트 참조', 'acf-css-really-simple-style-management-center' ); ?></h3>
                <p class="description" style="margin-bottom: 15px;">
                    <?php _e( '특정 페이지나 플러그인 전용 색상으로 사용할 수 있는 팔레트입니다. 브랜드/시스템 팔레트 값을 기반으로 대안 색상을 설정하거나 후보군으로 컬러셋을 매핑해 둘 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <div class="jj-palette-reference" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <?php if ( ! empty( $brand_palettes['primary_color'] ) ) : ?>
                    <div style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 3px;">
                        <strong><?php _e( '브랜드 Primary', 'acf-css-really-simple-style-management-center' ); ?></strong>
                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                            <div style="width: 30px; height: 30px; background: <?php echo esc_attr( $brand_palettes['primary_color'] ); ?>; border: 1px solid #ddd; border-radius: 3px;"></div>
                            <code style="font-size: 12px;"><?php echo esc_html( $brand_palettes['primary_color'] ); ?></code>
                        </div>
                        <button type="button" class="button button-small jj-copy-color" data-color="<?php echo esc_attr( $brand_palettes['primary_color'] ); ?>" style="margin-top: 5px; font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '복사', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $system_palettes['site_bg'] ) ) : ?>
                    <div style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 3px;">
                        <strong><?php _e( '시스템 Site BG', 'acf-css-really-simple-style-management-center' ); ?></strong>
                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                            <div style="width: 30px; height: 30px; background: <?php echo esc_attr( $system_palettes['site_bg'] ); ?>; border: 1px solid #ddd; border-radius: 3px;"></div>
                            <code style="font-size: 12px;"><?php echo esc_html( $system_palettes['site_bg'] ); ?></code>
                        </div>
                        <button type="button" class="button button-small jj-copy-color" data-color="<?php echo esc_attr( $system_palettes['site_bg'] ); ?>" style="margin-top: 5px; font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '복사', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="jj-style-guide-grid jj-grid-4-col">
                <div class="jj-control-group jj-color-card">
                    <label for="jj-another-primary"><?php _e( 'Primary Color', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-another-primary" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[another][primary_color]" 
                           value="<?php echo esc_attr( $another_palettes['primary_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['primary_color'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color'] ); ?>"
                                data-target="#jj-another-primary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color'] ); ?>"
                                data-target="#jj-another-primary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <p class="description" style="margin-top: 8px;">
                        <?php _e( '특정 페이지나 플러그인용 Primary 색상 후보입니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-another-primary-hover"><?php _e( 'Primary Color (Hover)', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-another-primary-hover" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[another][primary_color_hover]" 
                           value="<?php echo esc_attr( $another_palettes['primary_color_hover'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['primary_color_hover'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color_hover'] ); ?>"
                                data-target="#jj-another-primary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color_hover'] ); ?>"
                                data-target="#jj-another-primary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-another-secondary"><?php _e( 'Secondary Color', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-another-secondary" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[another][secondary_color]" 
                           value="<?php echo esc_attr( $another_palettes['secondary_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['secondary_color'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color'] ); ?>"
                                data-target="#jj-another-secondary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color'] ); ?>"
                                data-target="#jj-another-secondary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-another-secondary-hover"><?php _e( 'Secondary Color (Hover)', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <input type="text" 
                           id="jj-another-secondary-hover" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[another][secondary_color_hover]" 
                           value="<?php echo esc_attr( $another_palettes['secondary_color_hover'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['secondary_color_hover'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color_hover'] ); ?>"
                                data-target="#jj-another-secondary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color_hover'] ); ?>"
                                data-target="#jj-another-secondary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php
        // [v5.0.0] '임시' '팔레트' '탭' '내용' '로드' - 탭 활성화/비활성화 체크
        if ( $tab_enabled_temp ) {
            $temp_palette_path = defined( 'JJ_STYLE_GUIDE_PATH' ) ? JJ_STYLE_GUIDE_PATH : plugin_dir_path( __FILE__ ) . '../../../';
            $temp_palette_file = $temp_palette_path . 'includes/editor-views/view-section-temp-palette.php';
            if ( file_exists( $temp_palette_file ) ) {
                include_once $temp_palette_file;
            } else {
                // 상대 경로로 다시 시도
                include_once __DIR__ . '/view-section-temp-palette.php';
            }
        }
        // [v5.0.0] 탭 활성화/비활성화가 적용되어 else 블록은 제거
        ?>
    </div>
</div>