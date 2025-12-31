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
    'elementor', 'beaver-builder', // [v3.7.0] beaver-builder 추가
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
?>
<div class="jj-labs-supported-tab">
    <h2><?php _e( '공식 지원 테마 및 플러그인', 'jj-style-guide' ); ?></h2>
    <p class="description jj-labs-tab-description" data-tab-type="supported" data-tooltip="labs-tab-supported-list">
        <?php _e( '현재 어댑터가 제공되는 공식 지원 테마와 플러그인 목록입니다. 이 목록은 어댑터 설정에 따라 자동으로 갱신되며, 인기 순서에 따라 우선적으로 표시됩니다.', 'jj-style-guide' ); ?>
        <span class="dashicons dashicons-editor-help" style="margin-left: 5px; cursor: help;" aria-label="<?php esc_attr_e( '도움말', 'jj-style-guide' ); ?>"></span>
    </p>

    <div class="jj-labs-supported-toolbar" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin: 15px 0 5px;">
        <input type="search"
               id="jj-labs-supported-search"
               placeholder="<?php echo esc_attr__( '검색: 테마/플러그인 이름', 'jj-style-guide' ); ?>"
               style="min-width: 240px; height: 34px; padding: 0 10px; border-radius: 6px; border: 1px solid #c3c4c7;" />
        <span class="description" style="margin:0;">
            <?php
            printf(
                /* translators: 1: theme count, 2: plugin count */
                __( '총 %1$d개 테마 · %2$d개 플러그인', 'jj-style-guide' ),
                count( $themes_config ),
                count( $spokes_config )
            );
            ?>
        </span>
    </div>

    <div class="jj-labs-supported-blocks" style="margin-top: 20px;">
        <!-- 공식 지원 테마 -->
        <div class="jj-labs-supported-item jj-labs-supported-themes" data-type="themes" style="margin-bottom: 30px;">
            <h3><?php _e( '공식 지원 테마', 'jj-style-guide' ); ?></h3>
            <p class="description">
                <?php _e( '아래 목록은 현재 개발된 모든 공식 지원 테마입니다.', 'jj-style-guide' ); ?>
            </p>
            <ul class="jj-labs-supported-list" data-initial-count="<?php echo esc_attr( $initial_theme_count ); ?>" style="list-style: none; padding: 0; margin: 15px 0;">
                <?php
                $theme_index = 0;
                foreach ( $themes_config as $slug => $path ) :
                    $label = jj_style_guide_get_theme_label( $slug, $path );
                    $is_hidden = ( $theme_index >= $initial_theme_count );
                    ?>
                    <li class="jj-labs-supported-list-item <?php echo $is_hidden ? 'is-hidden' : ''; ?>"
                        style="padding: 8px 12px; margin: 5px 0; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 3px;">
                        <?php echo esc_html( $label ); ?>
                    </li>
                    <?php $theme_index++; ?>
                <?php endforeach; ?>
            </ul>
            <?php if ( count( $themes_config ) > $initial_theme_count ) : ?>
            <div class="jj-labs-supported-actions">
                <button type="button" class="button button-secondary jj-labs-show-more" data-target="themes"><?php _e( '더 보기', 'jj-style-guide' ); ?></button>
                <button type="button" class="button button-secondary jj-labs-show-all" data-target="themes" style="display:none;"><?php _e( '전체 보기', 'jj-style-guide' ); ?></button>
            </div>
            <?php endif; ?>
        </div>

        <!-- 공식 지원 플러그인 -->
        <div class="jj-labs-supported-item jj-labs-supported-spokes" data-type="spokes">
            <h3><?php _e( '공식 지원 플러그인', 'jj-style-guide' ); ?></h3>
            <p class="description">
                <?php _e( '아래 목록은 현재 개발된 모든 공식 지원 플러그인입니다.', 'jj-style-guide' ); ?>
            </p>
            <ul class="jj-labs-supported-list" data-initial-count="<?php echo esc_attr( $initial_spoke_count ); ?>" style="list-style: none; padding: 0; margin: 15px 0;">
                <?php
                $spoke_index = 0;
                foreach ( $spokes_config as $slug => $path ) :
                    $label = jj_style_guide_get_spoke_label( $slug, $path );
                    $is_hidden = ( $spoke_index >= $initial_spoke_count );
                    ?>
                    <li class="jj-labs-supported-list-item <?php echo $is_hidden ? 'is-hidden' : ''; ?>"
                        style="padding: 8px 12px; margin: 5px 0; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 3px;">
                        <?php echo esc_html( $label ); ?>
                    </li>
                    <?php $spoke_index++; ?>
                <?php endforeach; ?>
            </ul>
            <?php if ( count( $spokes_config ) > $initial_spoke_count ) : ?>
            <div class="jj-labs-supported-actions">
                <button type="button" class="button button-secondary jj-labs-show-more" data-target="spokes"><?php _e( '더 보기', 'jj-style-guide' ); ?></button>
                <button type="button" class="button button-secondary jj-labs-show-all" data-target="spokes" style="display:none;"><?php _e( '전체 보기', 'jj-style-guide' ); ?></button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

