<?php
/**
 * 실험실 센터 - 공식 지원 목록 탭
 * 공식 지원 테마 및 플러그인 목록 표시
 *
 * @version 3.7.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// 인기/가중치 기반 우선 순위 정의 (v3.7.0 업데이트)
$theme_popularity_order = array(
    // 상위 우선순위 (가장 인기 있는 테마)
    'astra', 'kadence', 'generatepress', 'oceanwp', 'divi', // [v3.7.0] oceanwp, divi 추가
    'storefront', 'neve', 'spectra-one', 'nexter', 
    'hello-elementor', 'hello-biz',
    // 후순위 (기본 테마 및 기타)
    'twentytwentyfive', 'twentytwentyfour', 'frost', 'onepress', 'hestia',
);

$spoke_popularity_order = array(
    // 전자상거래 및 코어
    'woocommerce',
    
    // 페이지 빌더 / 블록
    'elementor', 'beaver-builder', 'bricks', 'wpbakery', // [v3.7.0] beaver-builder 추가, [v10.2.0] bricks/wpbakery 추가
    'generateblocks', 'spectra',
    'kadence-blocks', 'otter-blocks', 'essential-blocks', 'nexter-blocks',
    
    // 폼 플러그인
    'contact-form-7', 'wpforms', // [v3.7.0] wpforms 추가
    'fluentform', 'gravityforms', 'bitform',
    
    // SEO 및 마케팅
    'yoast-seo', 'rankmath', // [v3.7.0] yoast-seo 추가
    'monsterinsights', 'jetpack', // [v3.7.0] monsterinsights, jetpack 추가
    
    // LMS
    'learndash', 'tutor-lms', 'learnpress', 'lifterlms',
    'sensei-lms', 'masterstudy-lms',
    
    // 커뮤니티 / 회원
    'ultimatemember', 'advanced-members',
    'buddypress', 'fluent-community',
    
    // 기타 도구
    'acf-extended', 'gamipress',
    
    // 백업 / 보안 (후순위)
    'updraftplus', 'wordfence', 'all-in-one-wp-migration', // [v3.7.0] 추가
);

// 한 번에 노출할 기본 개수
$initial_theme_count = 6;
$initial_spoke_count = 6;
$priority_pages = 3;

// 정렬 함수 (이미 정의되어 있을 수 있으므로 확인)
if ( ! function_exists( 'jj_style_guide_sort_by_popularity_then_random' ) ) {
    function jj_style_guide_sort_by_popularity_then_random( $config, $popularity_order, $initial_count, $priority_pages ) {
        $slugs = array_keys( $config );
        $seen = array();
        $ordered_slugs = array();

        foreach ( $popularity_order as $slug ) {
            if ( isset( $config[ $slug ] ) && ! isset( $seen[ $slug ] ) ) {
                $ordered_slugs[] = $slug;
                $seen[ $slug ] = true;
            }
        }

        $remaining = array();
        foreach ( $slugs as $slug ) {
            if ( ! isset( $seen[ $slug ] ) ) {
                $remaining[] = $slug;
            }
        }
        sort( $remaining, SORT_STRING );
        $ordered_slugs = array_merge( $ordered_slugs, $remaining );

        $priority_limit = $initial_count * $priority_pages;
        if ( count( $ordered_slugs ) > $priority_limit ) {
            $head = array_slice( $ordered_slugs, 0, $priority_limit );
            $tail = array_slice( $ordered_slugs, $priority_limit );
            shuffle( $tail );
            $ordered_slugs = array_merge( $head, $tail );
        }

        $sorted_config = array();
        foreach ( $ordered_slugs as $slug ) {
            $sorted_config[ $slug ] = $config[ $slug ];
        }
        return $sorted_config;
    }
}

// 테마/스포크 이름 매핑 함수
if ( ! function_exists( 'jj_style_guide_get_theme_label' ) ) {
    function jj_style_guide_get_theme_label( $slug, $path ) {
        $map = array(
            'astra' => 'Astra', 'spectra-one' => 'Spectra One',
            'kadence' => 'Kadence', 'generatepress' => 'GeneratePress',
            'nexter' => 'Nexter', 'neve' => 'Neve', 'hestia' => 'Hestia',
            'storefront' => 'Storefront', 'frost' => 'Frost',
            'twentytwentyfive' => 'Twenty Twenty-Five',
            'twentytwentyfour' => 'Twenty Twenty-Four',
            'onepress' => 'OnePress', 'hello-elementor' => 'Hello Elementor',
            'hello-biz' => 'Hello Biz',
        );
        return isset( $map[ $slug ] ) ? $map[ $slug ] : ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
    }
}

if ( ! function_exists( 'jj_style_guide_get_spoke_label' ) ) {
    function jj_style_guide_get_spoke_label( $slug, $path ) {
        $map = array(
            'woocommerce' => 'WooCommerce', 'learndash' => 'LearnDash',
            'ultimatemember' => 'Ultimate Member', 'advanced-members' => 'Advanced Members',
            'acf-extended' => 'ACF Extended', 'buddypress' => 'BuddyPress',
            'gamipress' => 'GamiPress', 'rankmath' => 'Rank Math SEO',
            'elementor' => 'Elementor', 'generateblocks' => 'GenerateBlocks',
            'spectra' => 'Spectra (Ultimate Addons for Gutenberg)',
            'kadence-blocks' => 'Kadence Blocks', 'otter-blocks' => 'Otter Blocks',
            'essential-blocks' => 'Essential Blocks', 'nexter-blocks' => 'Nexter Blocks',
            'beaver-builder' => 'Beaver Builder',
            'bricks' => 'Bricks Builder',
            'breakdance' => 'Breakdance Builder',
            'wpbakery' => 'WPBakery Page Builder',
            'tutor-lms' => 'Tutor LMS', 'learnpress' => 'LearnPress',
            'masterstudy-lms' => 'MasterStudy LMS', 'sensei-lms' => 'Sensei LMS',
            'lifterlms' => 'LifterLMS', 'bbpress' => 'bbPress',
            'fluent-community' => 'Fluent Community', 'fluentform' => 'Fluent Forms',
            'gravityforms' => 'Gravity Forms', 'contact-form-7' => 'Contact Form 7',
            'bitform' => 'Bit Form',
        );
        if ( isset( $map[ $slug ] ) ) {
            return $map[ $slug ];
        }
        if ( strpos( $path, '/' ) !== false ) {
            $parts = explode( '/', $path );
            return ucwords( str_replace( array( '-', '_' ), ' ', reset( $parts ) ) );
        }
        return ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
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

// === Status helpers (installed/active) ===
if ( ! function_exists( 'is_plugin_active' ) && defined( 'ABSPATH' ) ) {
    $plugin_php = ABSPATH . 'wp-admin/includes/plugin.php';
    if ( file_exists( $plugin_php ) ) {
        require_once $plugin_php;
    }
}

if ( ! function_exists( 'jj_labs_get_plugin_status' ) ) {
    function jj_labs_get_plugin_status( $plugin_file ) {
        $plugin_file = (string) $plugin_file;
        $active = function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin_file ) : false;
        $installed = defined( 'WP_PLUGIN_DIR' ) ? file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) : false;
        if ( $active ) return 'active';
        if ( $installed ) return 'installed';
        return 'missing';
    }
}

if ( ! function_exists( 'jj_labs_get_theme_status' ) ) {
    function jj_labs_get_theme_status( $slug ) {
        $slug = (string) $slug;
        $active_template = function_exists( 'wp_get_theme' ) ? wp_get_theme()->get_template() : '';
        $active_stylesheet = function_exists( 'wp_get_theme' ) ? wp_get_theme()->get_stylesheet() : '';

        if ( $slug && ( $active_template === $slug || $active_stylesheet === $slug ) ) {
            return 'active';
        }

        $t = function_exists( 'wp_get_theme' ) ? wp_get_theme( $slug ) : null;
        if ( $t && method_exists( $t, 'exists' ) && $t->exists() ) {
            return 'installed';
        }
        return 'missing';
    }
}

if ( ! function_exists( 'jj_labs_status_badge_html' ) ) {
    function jj_labs_status_badge_html( $status ) {
        $status = (string) $status;
        $label = __( '미설치', 'acf-css-really-simple-style-management-center' );
        $bg = '#f0f0f1';
        $color = '#3c434a';
        if ( 'active' === $status ) {
            $label = __( '활성', 'acf-css-really-simple-style-management-center' );
            $bg = '#edfaef';
            $color = '#1d6b2f';
        } elseif ( 'installed' === $status ) {
            $label = __( '설치됨', 'acf-css-really-simple-style-management-center' );
            $bg = '#f0f6fc';
            $color = '#135e96';
        }
        return '<span class="jj-labs-status-badge" style="display:inline-flex; align-items:center; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:700; background:' . esc_attr( $bg ) . '; color:' . esc_attr( $color ) . '; border:1px solid rgba(0,0,0,.08);">' . esc_html( $label ) . '</span>';
    }
}
?>
<div class="jj-labs-supported-tab">
    <h2><?php _e( '공식 지원 테마 및 플러그인', 'acf-css-really-simple-style-management-center' ); ?></h2>
    <p class="description jj-labs-tab-description" data-tab-type="supported" data-tooltip="labs-tab-supported-list">
        <?php _e( '현재 어댑터가 제공되는 공식 지원 테마와 플러그인 목록입니다. 이 목록은 어댑터 설정에 따라 자동으로 갱신되며, 인기 순서에 따라 우선적으로 표시됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
        <span class="dashicons dashicons-editor-help" style="margin-left: 5px; cursor: help;" aria-label="<?php esc_attr_e( '도움말', 'acf-css-really-simple-style-management-center' ); ?>"></span>
    </p>

    <div class="jj-labs-supported-toolbar" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin: 15px 0 5px;">
        <input type="search"
               id="jj-labs-supported-search"
               placeholder="<?php echo esc_attr__( '검색: 테마/플러그인 이름', 'acf-css-really-simple-style-management-center' ); ?>"
               style="min-width: 240px; height: 34px; padding: 0 10px; border-radius: 6px; border: 1px solid #c3c4c7;" />
        <span class="description" style="margin:0;">
            <?php
            printf(
                /* translators: 1: theme count, 2: plugin count */
                __( '총 %1$d개 테마 · %2$d개 플러그인', 'acf-css-really-simple-style-management-center' ),
                count( $themes_config ),
                count( $spokes_config )
            );
            ?>
        </span>
    </div>

    <div class="jj-labs-supported-blocks" style="margin-top: 20px;">
        <!-- 공식 지원 테마 -->
        <div class="jj-labs-supported-item jj-labs-supported-themes" data-type="themes" style="margin-bottom: 30px;">
            <h3><?php _e( '공식 지원 테마', 'acf-css-really-simple-style-management-center' ); ?></h3>
            <p class="description">
                <?php _e( '아래 목록은 현재 개발된 모든 공식 지원 테마입니다.', 'acf-css-really-simple-style-management-center' ); ?>
            </p>
            <ul class="jj-labs-supported-list" data-initial-count="<?php echo esc_attr( $initial_theme_count ); ?>" style="list-style: none; padding: 0; margin: 15px 0;">
                <?php
                $theme_index = 0;
                foreach ( $themes_config as $slug => $path ) :
                    $label = jj_style_guide_get_theme_label( $slug, $path );
                    $is_hidden = ( $theme_index >= $initial_theme_count );
                    $status = jj_labs_get_theme_status( $slug );
                    ?>
                    <li class="jj-labs-supported-list-item <?php echo $is_hidden ? 'is-hidden' : ''; ?>"
                        style="padding: 8px 12px; margin: 5px 0; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 3px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                            <span><?php echo esc_html( $label ); ?></span>
                            <?php echo jj_labs_status_badge_html( $status ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </li>
                    <?php $theme_index++; ?>
                <?php endforeach; ?>
            </ul>
            <?php if ( count( $themes_config ) > $initial_theme_count ) : ?>
            <div class="jj-labs-supported-actions">
                <button type="button" class="button button-secondary jj-labs-show-more" data-target="themes"><?php _e( '더 보기', 'acf-css-really-simple-style-management-center' ); ?></button>
                <button type="button" class="button button-secondary jj-labs-show-all" data-target="themes" style="display:none;"><?php _e( '전체 보기', 'acf-css-really-simple-style-management-center' ); ?></button>
            </div>
            <?php endif; ?>
        </div>

        <!-- 공식 지원 플러그인 -->
        <div class="jj-labs-supported-item jj-labs-supported-spokes" data-type="spokes">
            <h3><?php _e( '공식 지원 플러그인', 'acf-css-really-simple-style-management-center' ); ?></h3>
            <p class="description">
                <?php _e( '아래 목록은 현재 개발된 모든 공식 지원 플러그인입니다.', 'acf-css-really-simple-style-management-center' ); ?>
            </p>
            <ul class="jj-labs-supported-list" data-initial-count="<?php echo esc_attr( $initial_spoke_count ); ?>" style="list-style: none; padding: 0; margin: 15px 0;">
                <?php
                $spoke_index = 0;
                foreach ( $spokes_config as $slug => $path ) :
                    $label = jj_style_guide_get_spoke_label( $slug, $path );
                    $is_hidden = ( $spoke_index >= $initial_spoke_count );
                    $status = jj_labs_get_plugin_status( $path );
                    ?>
                    <li class="jj-labs-supported-list-item <?php echo $is_hidden ? 'is-hidden' : ''; ?>"
                        style="padding: 8px 12px; margin: 5px 0; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 3px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                            <span><?php echo esc_html( $label ); ?></span>
                            <?php echo jj_labs_status_badge_html( $status ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </li>
                    <?php $spoke_index++; ?>
                <?php endforeach; ?>
            </ul>
            <?php if ( count( $spokes_config ) > $initial_spoke_count ) : ?>
            <div class="jj-labs-supported-actions">
                <button type="button" class="button button-secondary jj-labs-show-more" data-target="spokes"><?php _e( '더 보기', 'acf-css-really-simple-style-management-center' ); ?></button>
                <button type="button" class="button button-secondary jj-labs-show-all" data-target="spokes" style="display:none;"><?php _e( '전체 보기', 'acf-css-really-simple-style-management-center' ); ?></button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

