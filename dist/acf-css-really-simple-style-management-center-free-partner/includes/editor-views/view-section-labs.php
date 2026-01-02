<?php
/**
 * [v3.5.0-dev5 '신규'] '프리미엄' '기능': '실험실 (Labs)' 섹션
 * - '설계도' [Phase 2, Item 7]에 '따라', '미지원' '테마'/'플러그인' '분석/조정'을 '위한' 'UI' '뼈대'
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$labs_options   = $options['labs'] ?? array();
$themes_config_raw  = $adapters_config['themes'] ?? array();
$spokes_config_raw  = $adapters_config['spokes'] ?? array();

// --- 인기/가중치 기반 우선 순위 정의 (반자동: 필요 시 이 배열만 조정하면 됨) ---
// [v3.7.0 업데이트] 상위 10개 우선순위 어댑터 추가
$theme_popularity_order = array(
    // 상위 우선순위 (가장 인기 있는 테마)
    'astra',
    'kadence',
    'generatepress',
    'oceanwp', // [v3.7.0 신규]
    'divi', // [v3.7.0 신규]
    'storefront',
    'neve',
    'spectra-one',
    'nexter',
    'hello-elementor',
    'hello-biz',
    // 후순위 (기본 테마 및 기타)
    'twentytwentyfive',
    'twentytwentyfour',
    'frost',
    'onepress',
    'hestia',
);

$spoke_popularity_order = array(
    // 전자상거래 및 코어
    'woocommerce',

    // 페이지 빌더 / 블록
    'elementor',
    'beaver-builder', // [v3.7.0 신규]
    'generateblocks',
    'spectra',
    'kadence-blocks',
    'otter-blocks',
    'essential-blocks',
    'nexter-blocks',

    // 폼 플러그인
    'contact-form-7',
    'wpforms', // [v3.7.0 신규]
    'fluentform',
    'gravityforms',
    'bitform',

    // SEO 및 마케팅
    'yoast-seo', // [v3.7.0 신규]
    'rankmath',
    'monsterinsights', // [v3.7.0 신규]
    'jetpack', // [v3.7.0 신규]

    // LMS
    'learndash',
    'tutor-lms',
    'learnpress',
    'lifterlms',
    'sensei-lms',
    'masterstudy-lms',

    // 커뮤니티 / 회원
    'ultimatemember',
    'advanced-members',
    'buddypress',
    'fluent-community',

    // 기타 도구
    'acf-extended',
    'gamipress',
    
    // 백업 / 보안 (후순위)
    'updraftplus', // [v3.7.0 신규]
    'wordfence', // [v3.7.0 신규]
    'all-in-one-wp-migration', // [v3.7.0 신규]
);

// 한 번에 노출할 기본 개수 및 "우선 순위 적용" 범위 (초기 + 더 보기 2회까지)
$initial_theme_count = 6;
$initial_spoke_count = 6;
$priority_pages      = 3; // 초기 노출 + 더 보기 2회분

// 가중치 기반 정렬 + 후반부 무작위 섞기
if ( ! function_exists( 'jj_style_guide_sort_by_popularity_then_random' ) ) {
    function jj_style_guide_sort_by_popularity_then_random( $config, $popularity_order, $initial_count, $priority_pages ) {
        $slugs           = array_keys( $config );
        $seen            = array();
        $ordered_slugs   = array();

        // 1. 인기 순서대로 우선 배치
        foreach ( $popularity_order as $slug ) {
            if ( isset( $config[ $slug ] ) && ! isset( $seen[ $slug ] ) ) {
                $ordered_slugs[]   = $slug;
                $seen[ $slug ]     = true;
            }
        }

        // 2. 나머지는 알파벳 순으로 뒤에 배치
        $remaining = array();
        foreach ( $slugs as $slug ) {
            if ( ! isset( $seen[ $slug ] ) ) {
                $remaining[] = $slug;
            }
        }
        sort( $remaining, SORT_STRING );
        $ordered_slugs = array_merge( $ordered_slugs, $remaining );

        // 3. 우선 순위 페이지(초기 + 더 보기 2회) 이후는 무작위로 섞기
        $priority_limit = $initial_count * $priority_pages;
        if ( count( $ordered_slugs ) > $priority_limit ) {
            $head = array_slice( $ordered_slugs, 0, $priority_limit );
            $tail = array_slice( $ordered_slugs, $priority_limit );
            shuffle( $tail );
            $ordered_slugs = array_merge( $head, $tail );
        }

        // 최종 slug 순서를 config에 재적용
        $sorted_config = array();
        foreach ( $ordered_slugs as $slug ) {
            $sorted_config[ $slug ] = $config[ $slug ];
        }
        return $sorted_config;
    }
}

$themes_config = jj_style_guide_sort_by_popularity_then_random(
    $themes_config_raw,
    $theme_popularity_order,
    $initial_theme_count,
    $priority_pages
);

$spokes_config = jj_style_guide_sort_by_popularity_then_random(
    $spokes_config_raw,
    $spoke_popularity_order,
    $initial_spoke_count,
    $priority_pages
);

// 테마 이름 매핑 (필요 시 slug 기반 Fallback)
if ( ! function_exists( 'jj_style_guide_get_theme_label' ) ) {
    function jj_style_guide_get_theme_label( $slug, $path ) {
        $map = array(
            'astra'            => 'Astra',
            'spectra-one'      => 'Spectra One',
            'kadence'          => 'Kadence',
            'generatepress'    => 'GeneratePress',
            'nexter'           => 'Nexter',
            'neve'             => 'Neve',
            'hestia'           => 'Hestia',
            'storefront'       => 'Storefront',
            'frost'            => 'Frost',
            'twentytwentyfive' => 'Twenty Twenty-Five',
            'twentytwentyfour' => 'Twenty Twenty-Four',
            'onepress'         => 'OnePress',
            'hello-elementor'  => 'Hello Elementor',
            'hello-biz'        => 'Hello Biz',
        );
        if ( isset( $map[ $slug ] ) ) {
            return $map[ $slug ];
        }
        return ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
    }
}

// 스포크(플러그인) 이름 매핑 (필요 시 파일명 기반 Fallback)
if ( ! function_exists( 'jj_style_guide_get_spoke_label' ) ) {
    function jj_style_guide_get_spoke_label( $slug, $path ) {
        $map = array(
            'woocommerce'      => 'WooCommerce',
            'learndash'        => 'LearnDash',
            'ultimatemember'   => 'Ultimate Member',
            'advanced-members' => 'Advanced Members',
            'acf-extended'     => 'ACF Extended',
            'buddypress'       => 'BuddyPress',
            'gamipress'        => 'GamiPress',
            'rankmath'         => 'Rank Math SEO',
            'elementor'        => 'Elementor',
            'generateblocks'   => 'GenerateBlocks',
            'spectra'          => 'Spectra (Ultimate Addons for Gutenberg)',
            'kadence-blocks'   => 'Kadence Blocks',
            'otter-blocks'     => 'Otter Blocks',
            'essential-blocks' => 'Essential Blocks',
            'nexter-blocks'    => 'Nexter Blocks',
            'tutor-lms'        => 'Tutor LMS',
            'learnpress'       => 'LearnPress',
            'masterstudy-lms'  => 'MasterStudy LMS',
            'sensei-lms'       => 'Sensei LMS',
            'lifterlms'        => 'LifterLMS',
            'bbpress'          => 'bbPress',
            'fluent-community' => 'Fluent Community',
            'fluentform'       => 'Fluent Forms',
            'gravityforms'     => 'Gravity Forms',
            'contact-form-7'   => 'Contact Form 7',
            'bitform'          => 'Bit Form',
        );
        if ( isset( $map[ $slug ] ) ) {
            return $map[ $slug ];
        }

        if ( strpos( $path, '/' ) !== false ) {
            $parts = explode( '/', $path );
            $file  = reset( $parts );
            return ucwords( str_replace( array( '-', '_' ), ' ', $file ) );
        }

        return ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
    }
}

?>

<div class="jj-section-global" id="jj-section-labs">
    <h2 class="jj-section-title"><?php _e( '5. 실험실 (Labs)', 'acf-css-really-simple-style-management-center' ); ?></h2>
    <p class="description">
        <?php _e( '실험실은 아직 공식 지원되지 않는 테마나 플러그인의 스타일을 분석하고 조정하기 위한 고급 도구입니다. 사이트 환경에 맞추어 점진적으로 개선·확장될 예정입니다.', 'acf-css-really-simple-style-management-center' ); ?>
    </p>

    <div class="jj-labs-supported-adapters">
        <h3><?php _e( '공식 지원 테마 및 플러그인', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <p class="description">
            <?php _e( '현재 어댑터가 제공되는 공식 지원 테마와 플러그인 목록입니다. 이 목록은 어댑터 설정에 따라 자동으로 갱신되며, 우선 일부 항목만 먼저 보여줍니다.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>

        <div class="jj-labs-supported-blocks">
            <div class="jj-labs-supported-item jj-labs-supported-themes" data-type="themes">
                <strong><?php _e( '공식 지원 테마', 'acf-css-really-simple-style-management-center' ); ?></strong>
                <p class="description">
                    <?php _e( '아래 목록은 어댑터가 준비된 테마 중 일부만 먼저 보여주며, 더 보기/전체 보기 버튼을 통해 나머지를 확인하실 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <ul class="jj-labs-supported-list" data-initial-count="<?php echo esc_attr( $initial_theme_count ); ?>">
                    <?php
                    $theme_index = 0;
                    foreach ( $themes_config as $slug => $path ) :
                        $label       = jj_style_guide_get_theme_label( $slug, $path );
                        $theme_index++;
                        $extra_class = ( $theme_index > $initial_theme_count ) ? ' is-hidden' : '';
                        ?>
                        <li class="jj-labs-supported-list-item<?php echo esc_attr( $extra_class ); ?>">
                            <?php echo esc_html( $label ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="jj-labs-supported-actions">
                    <button type="button" class="button button-secondary jj-labs-show-more" data-target="themes"><?php _e( '더 보기', 'acf-css-really-simple-style-management-center' ); ?></button>
                    <button type="button" class="button button-link jj-labs-show-all" data-target="themes" style="display:none;"><?php _e( '전체 보기', 'acf-css-really-simple-style-management-center' ); ?></button>
                </div>
            </div>

            <div class="jj-labs-supported-item jj-labs-supported-spokes" data-type="spokes">
                <strong><?php _e( '공식 지원 플러그인', 'acf-css-really-simple-style-management-center' ); ?></strong>
                <p class="description">
                    <?php _e( '아래 목록은 어댑터가 준비된 플러그인 중 일부만 먼저 보여주며, 더 보기/전체 보기 버튼을 통해 나머지를 확인하실 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <ul class="jj-labs-supported-list" data-initial-count="<?php echo esc_attr( $initial_spoke_count ); ?>">
                    <?php
                    $spoke_index = 0;
                    foreach ( $spokes_config as $slug => $path ) :
                        $label       = jj_style_guide_get_spoke_label( $slug, $path );
                        $spoke_index++;
                        $extra_class = ( $spoke_index > $initial_spoke_count ) ? ' is-hidden' : '';
                        ?>
                        <li class="jj-labs-supported-list-item<?php echo esc_attr( $extra_class ); ?>">
                            <?php echo esc_html( $label ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="jj-labs-supported-actions">
                    <button type="button" class="button button-secondary jj-labs-show-more" data-target="spokes"><?php _e( '더 보기', 'acf-css-really-simple-style-management-center' ); ?></button>
                    <button type="button" class="button button-link jj-labs-show-all" data-target="spokes" style="display:none;"><?php _e( '전체 보기', 'acf-css-really-simple-style-management-center' ); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="jj-tabs-container">
        <div class="jj-tabs-nav">
            <button type="button" class="jj-tab-button is-active" data-tab="labs-scanner">
                <?php _e( '1. CSS/HTML 스캐너', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" class="jj-tab-button" data-tab="labs-overrides">
                <?php _e( '2. 수동 재정의 (Overrides)', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
        </div>

        <div class="jj-tab-content is-active" data-tab-content="labs-scanner">
            <fieldset class="jj-fieldset-group">
                <legend><?php _e( '페이지/플러그인 스캔', 'acf-css-really-simple-style-management-center' ); ?></legend>
                <p class="description" style="margin-bottom: 20px;">
                    <?php _e( '분석하고 싶은 페이지의 URL을 입력하거나, 활성 플러그인/테마 목록에서 대상을 선택한 뒤 [분석 시작] 버튼을 눌러 주세요.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                 <div class="jj-style-guide-grid jj-grid-2-col">
                    <div class="jj-control-group">
                        <label for="jj-labs-scan-url"><?php _e( 'URL로 스캔하기', 'acf-css-really-simple-style-management-center' ); ?></label>
                        <input type="url" 
                               id="jj-labs-scan-url" 
                               class="jj-data-field" 
                               data-setting-key="labs[scan_url]" 
                               value="<?php echo esc_attr( $labs_options['scan_url'] ?? '' ); ?>"
                               placeholder="https://... 스타일을 분석할 페이지 주소">
                        <button type="button" class="button" id="jj-labs-start-scan" style="margin-top: 10px;"><?php _e( '분석 시작', 'acf-css-really-simple-style-management-center' ); ?></button>
                        <span class="spinner" style="float:none; margin-top:10px;"></span>
                    </div>
                    <div class="jj-control-group">
                        <label for="jj-labs-scan-plugin"><?php _e( '활성 플러그인/테마 목록', 'acf-css-really-simple-style-management-center' ); ?></label>
                        <select id="jj-labs-scan-plugin" class="jj-data-field" data-setting-key="labs[scan_target]">
                            <option value=""><?php _e( '-- 대상 선택 --', 'acf-css-really-simple-style-management-center' ); ?></option>
                            <option value="theme_active"><?php _e( '현재 활성 테마', 'acf-css-really-simple-style-management-center' ); ?></option>
                            <optgroup label="<?php _e( '활성 플러그인', 'acf-css-really-simple-style-management-center' ); ?>">
                                <?php
                                $active_plugins = (array) get_option( 'active_plugins', array() );
                                foreach ( $active_plugins as $plugin_file ) {
                                    $plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file, false, false );
                                    $label       = $plugin_data['Name'] ?: $plugin_file;
                                    printf(
                                        '<option value="%1$s">%2$s</option>',
                                        esc_attr( $plugin_file ),
                                        esc_html( $label )
                                    );
                                }
                                ?>
                            </optgroup>
                        </select>
                        <p class="description"><?php _e( '현재 활성 테마 또는 플러그인을 선택하면, 해당 리소스의 스타일 구조를 파악하는 데 도움이 되는 기본 정보를 함께 확인하실 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                    </div>
                </div>
            </fieldset>
            
            <fieldset class="jj-fieldset-group">
                <legend><?php _e( '분석 결과', 'acf-css-really-simple-style-management-center' ); ?></legend>
                <div id="jj-labs-scan-results">
                    <p class="description"><?php _e( '분석이 완료되면, CSS 선택자 목록과 HTML 일부 구조가 이 영역에 표시됩니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                    <textarea style="width: 100%; height: 150px; background: #f9f9f9;" readonly></textarea>
                </div>
            </fieldset>
        </div>
        
        <div class="jj-tab-content" data-tab-content="labs-overrides">
            <fieldset class="jj-fieldset-group">
                <legend><?php _e( 'CSS 재정의 (Overrides)', 'acf-css-really-simple-style-management-center' ); ?></legend>
                <p class="description" style="margin-bottom: 20px;">
                    <?php _e( '스캐너를 통해 발견된 선택자나 직접 지정한 선택자에 전역 스타일 변수를 적용할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <div class="jj-control-group">
                    <label for="jj-labs-override-css"><?php _e( '수동 CSS 입력', 'acf-css-really-simple-style-management-center' ); ?></label>
                    <textarea id="jj-labs-override-css" 
                              class="jj-data-field" 
                              data-setting-key="labs[override_css]"
                              style="width: 100%; height: 250px;"
                              placeholder="<?php _e( "예: \n\n.some-plugin-button {\n    background-color: var(--jj-btn-primary-bg) !important;\n    color: var(--jj-btn-primary-text) !important;\n}\n\n.some-plugin-input {\n    border-color: var(--jj-form-input-border) !important;\n}\n\n", 'acf-css-really-simple-style-management-center' ); ?>"><?php echo esc_textarea( $labs_options['override_css'] ?? '' ); ?></textarea>
                </div>
            </fieldset>
        </div>

    </div> 
</div>
