<?php
/**
 * [v3.5.0 '제련'] '환경 스캐너' '설정' '파일'
 * - '설계도' [Phase 2, Item 6]에 '따라', 'jj-simple-style-guide.php'에 '하드코딩'되어 '있던' '표적지' '목록'을 '중앙' '관리'합니다.
 * - [v3.5.0-dev3 '갱신'] 사이트 관리자가 새로 전달한 테마 4종('twentytwentyfive', 'twentytwentyfour', 'onepress', 'frost') 표적지 추가
 */
if ( ! defined( 'ABSPATH' ) ) exit;

return array(
    
    /**
     * '테마' '표적지'
     * 'key' (slug) => 'path' (감지 기준이 되는 파일 경로, 테마 루트 기준)
     */
    'themes' => [
        'kadence'       => 'functions.php',
        'astra'         => 'functions.php',
        'generatepress' => 'functions.php',
        'nexter'        => 'functions.php',
        'neve'          => 'functions.php',
        'hestia'        => 'functions.php',
        'storefront'    => 'functions.php',
        'spectra-one'   => 'theme.json',
        'frost'         => 'theme.json', // [v3.5.0-dev3 '신규']
        'twentytwentyfive' => 'theme.json', // [v3.5.0-dev3 '신규']
        'twentytwentyfour' => 'theme.json', // [v3.5.0-dev3 '신규']
        'onepress'      => 'functions.php', // [v3.5.0-dev3 '신규']
        'hello-elementor' => 'functions.php',
        'hello-biz'     => 'functions.php', // [v3.6.0 '신규'] Hello Biz (Elementor 공식 테마)
        'oceanwp'       => 'functions.php', // [v3.7.0 '신규']
        'divi'          => 'functions.php', // [v3.7.0 '신규']
    ],

    /**
     * '스포크' (플러그인) '표적지'
     * 'key' (slug) => 'path' (플러그인 메인 파일 경로)
     */
    'spokes' => [
        'woocommerce'      => 'woocommerce/woocommerce.php',
        'learndash'        => 'sfwd-lms/sfwd_lms.php',
        'ultimatemember'   => 'ultimate-member/ultimate-member.php',
        'advanced-members' => 'advanced-members/advanced-members.php',
        'acf-extended'     => 'acf-extended/acf-extended.php',
        'buddypress'       => 'buddypress/bp-loader.php',
        'gamipress'        => 'gamipress/gamipress.php',
        'rankmath'         => 'rank-math/rank-math.php',

        // 페이지 빌더 / 엘리멘터
        'elementor'        => 'elementor/elementor.php',

        // 블록 플러그인 (우선순위 대상)
        'generateblocks'   => 'generateblocks/generateblocks.php',
        'spectra'          => 'ultimate-addons-for-gutenberg/ultimate-addons-for-gutenberg.php', // Spectra (이전 UAG)
        'kadence-blocks'   => 'kadence-blocks/kadence-blocks.php',
        'otter-blocks'     => 'otter-blocks/otter-blocks.php',
        'essential-blocks' => 'essential-blocks/essential-blocks.php',
        'nexter-blocks'    => 'nexter-blocks/nexter-blocks.php',

        // LMS 플러그인
        'tutor-lms'        => 'tutor/tutor.php',
        'learnpress'       => 'learnpress/learnpress.php',
        'masterstudy-lms'  => 'masterstudy-lms-learning-management-system/masterstudy-lms.php',
        'sensei-lms'       => 'sensei-lms/sensei-lms.php',
        'lifterlms'        => 'lifterlms/lifterlms.php',

        // 커뮤니티 / 포럼 플러그인
        'bbpress'          => 'bbpress/bbpress.php',
        'fluent-community' => 'fluent-community/fluent-community.php',

        // 폼 플러그인
        'fluentform'       => 'fluentform/fluentform.php',
        'gravityforms'     => 'gravityforms/gravityforms.php',
        'contact-form-7'   => 'contact-form-7/wp-contact-form-7.php',
        'bitform'          => 'bit-form/bit-form.php',
        'wpforms'          => 'wpforms-lite/wpforms.php', // [v3.7.0 '신규']

        // SEO 플러그인
        'yoast-seo'        => 'wordpress-seo/wp-seo.php', // [v3.7.0 '신규']

        // 분석 / 도구 플러그인
        'monsterinsights'  => 'google-analytics-for-wordpress/googleanalytics.php', // [v3.7.0 '신규']
        'jetpack'          => 'jetpack/jetpack.php', // [v3.7.0 '신규']

        // 백업 / 보안 플러그인
        'updraftplus'      => 'updraftplus/updraftplus.php', // [v3.7.0 '신규']
        'wordfence'        => 'wordfence/wordfence.php', // [v3.7.0 '신규']
        'all-in-one-wp-migration' => 'all-in-one-wp-migration/all-in-one-wp-migration.php', // [v3.7.0 '신규']

        // 페이지 빌더 (추가)
        'beaver-builder'   => 'beaver-builder-lite-version/fl-builder.php', // [v3.7.0 '신규']
        'bricks'           => 'bricks/bricks.php', // [v6.2.0 Phase 8 '신규']
        'breakdance'       => 'breakdance/plugin.php', // [v6.2.0 Phase 8 '신규']

        // 코어 / 공통 블록 어댑터는 별도 플러그인 감지 없이 항상 동작하도록 설계 (JJ_Adapter_Spoke_Blocks_Core)

        // (향후 '스포크' '추가' '시' '이곳'에 '반영')
    ],

);