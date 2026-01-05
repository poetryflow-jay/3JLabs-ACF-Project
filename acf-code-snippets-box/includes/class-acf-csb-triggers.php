<?php
/**
 * ACF Code Snippets Box - Triggers
 *
 * 트리거 조건 관리
 * [v5.0.0] 고급 조건부 트리거 시스템 추가
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Triggers 클래스
 */
class ACF_CSB_Triggers {

    /**
     * 사용 가능한 트리거 목록
     * [v5.0.0] 확장된 트리거 시스템
     */
    public static function get_available_triggers() {
        return array(
            // 위치 기반 트리거
            'location' => array(
                'label'   => __( '📍 실행 위치', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-location',
                'group'   => 'location',
                'options' => array(
                    'everywhere'     => __( '모든 페이지', 'acf-code-snippets-box' ),
                    'frontend'       => __( '프론트엔드만', 'acf-code-snippets-box' ),
                    'admin'          => __( '관리자 페이지만', 'acf-code-snippets-box' ),
                    'specific_pages' => __( '특정 페이지', 'acf-code-snippets-box' ),
                    'specific_posts' => __( '특정 포스트', 'acf-code-snippets-box' ),
                    'post_types'     => __( '특정 포스트 타입', 'acf-code-snippets-box' ),
                    'taxonomies'     => __( '특정 택소노미', 'acf-code-snippets-box' ),
                    'home'           => __( '홈페이지만', 'acf-code-snippets-box' ),
                    'archive'        => __( '아카이브 페이지만', 'acf-code-snippets-box' ),
                    'search'         => __( '검색 결과 페이지만', 'acf-code-snippets-box' ),
                    '404'            => __( '404 페이지만', 'acf-code-snippets-box' ),
                    'singular'       => __( '싱글 페이지 (포스트/페이지)', 'acf-code-snippets-box' ),
                    'category'       => __( '카테고리 아카이브', 'acf-code-snippets-box' ),
                    'tag'            => __( '태그 아카이브', 'acf-code-snippets-box' ),
                    'author'         => __( '작성자 아카이브', 'acf-code-snippets-box' ),
                    'date'           => __( '날짜 아카이브', 'acf-code-snippets-box' ),
                ),
            ),

            // URL 패턴 트리거 (신규)
            'url_pattern' => array(
                'label'       => __( '🔗 URL 패턴', 'acf-code-snippets-box' ),
                'icon'        => 'dashicons-admin-links',
                'group'       => 'url',
                'type'        => 'text',
                'placeholder' => __( '예: /shop/*, /blog/2024/*, *?utm_source=*', 'acf-code-snippets-box' ),
                'description' => __( '와일드카드(*) 사용 가능. 여러 패턴은 줄바꿈으로 구분.', 'acf-code-snippets-box' ),
            ),

            // URL 제외 패턴 (신규)
            'url_exclude' => array(
                'label'       => __( '🚫 URL 제외', 'acf-code-snippets-box' ),
                'icon'        => 'dashicons-dismiss',
                'group'       => 'url',
                'type'        => 'text',
                'placeholder' => __( '예: /admin/*, /wp-json/*', 'acf-code-snippets-box' ),
                'description' => __( '이 URL 패턴에서는 실행하지 않음', 'acf-code-snippets-box' ),
            ),

            // 쿼리 스트링 트리거 (신규)
            'query_string' => array(
                'label'       => __( '❓ 쿼리 파라미터', 'acf-code-snippets-box' ),
                'icon'        => 'dashicons-search',
                'group'       => 'url',
                'type'        => 'key_value',
                'placeholder' => __( '예: utm_source=google, ref=newsletter', 'acf-code-snippets-box' ),
                'description' => __( 'URL의 쿼리 파라미터 조건', 'acf-code-snippets-box' ),
            ),

            // 사용자 역할
            'user_roles' => array(
                'label'   => __( '👤 사용자 역할', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-admin-users',
                'group'   => 'user',
                'options' => 'dynamic', // wp_roles()에서 동적으로 가져옴
            ),

            // 로그인 상태
            'logged_in' => array(
                'label'   => __( '🔐 로그인 상태', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-lock',
                'group'   => 'user',
                'options' => array(
                    'all'        => __( '모든 사용자', 'acf-code-snippets-box' ),
                    'logged_in'  => __( '로그인한 사용자만', 'acf-code-snippets-box' ),
                    'logged_out' => __( '비로그인 사용자만', 'acf-code-snippets-box' ),
                ),
            ),

            // 사용자 능력 (신규)
            'user_capability' => array(
                'label'       => __( '🛡️ 사용자 권한', 'acf-code-snippets-box' ),
                'icon'        => 'dashicons-shield',
                'group'       => 'user',
                'type'        => 'select',
                'options'     => array(
                    ''                   => __( '선택 안 함', 'acf-code-snippets-box' ),
                    'manage_options'     => __( '관리자 권한 (manage_options)', 'acf-code-snippets-box' ),
                    'edit_posts'         => __( '포스트 편집 (edit_posts)', 'acf-code-snippets-box' ),
                    'publish_posts'      => __( '포스트 발행 (publish_posts)', 'acf-code-snippets-box' ),
                    'edit_pages'         => __( '페이지 편집 (edit_pages)', 'acf-code-snippets-box' ),
                    'upload_files'       => __( '파일 업로드 (upload_files)', 'acf-code-snippets-box' ),
                    'edit_theme_options' => __( '테마 편집 (edit_theme_options)', 'acf-code-snippets-box' ),
                ),
            ),

            // 디바이스
            'device' => array(
                'label'   => __( '📱 디바이스', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-smartphone',
                'group'   => 'device',
                'options' => array(
                    'all'     => __( '모든 디바이스', 'acf-code-snippets-box' ),
                    'desktop' => __( '데스크탑만', 'acf-code-snippets-box' ),
                    'mobile'  => __( '모바일만', 'acf-code-snippets-box' ),
                    'tablet'  => __( '태블릿만', 'acf-code-snippets-box' ),
                ),
            ),

            // 브라우저 (신규)
            'browser' => array(
                'label'   => __( '🌐 브라우저', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-desktop',
                'group'   => 'device',
                'options' => array(
                    'all'     => __( '모든 브라우저', 'acf-code-snippets-box' ),
                    'chrome'  => __( 'Chrome', 'acf-code-snippets-box' ),
                    'firefox' => __( 'Firefox', 'acf-code-snippets-box' ),
                    'safari'  => __( 'Safari', 'acf-code-snippets-box' ),
                    'edge'    => __( 'Edge', 'acf-code-snippets-box' ),
                    'ie'      => __( 'Internet Explorer', 'acf-code-snippets-box' ),
                ),
            ),

            // 시간 기반
            'time_based' => array(
                'label'   => __( '⏰ 시간 조건', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-clock',
                'group'   => 'time',
                'options' => array(
                    'always'     => __( '항상', 'acf-code-snippets-box' ),
                    'date_range' => __( '특정 기간', 'acf-code-snippets-box' ),
                    'time_range' => __( '특정 시간대', 'acf-code-snippets-box' ),
                    'weekdays'   => __( '특정 요일', 'acf-code-snippets-box' ),
                ),
            ),

            // 날짜 범위 설정 (신규)
            'date_start' => array(
                'label'   => __( '📅 시작 날짜', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-calendar-alt',
                'group'   => 'time',
                'type'    => 'date',
                'depends' => array( 'time_based' => 'date_range' ),
            ),

            'date_end' => array(
                'label'   => __( '📅 종료 날짜', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-calendar-alt',
                'group'   => 'time',
                'type'    => 'date',
                'depends' => array( 'time_based' => 'date_range' ),
            ),

            // 시간 범위 설정 (신규)
            'time_start' => array(
                'label'   => __( '🕐 시작 시간', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-clock',
                'group'   => 'time',
                'type'    => 'time',
                'depends' => array( 'time_based' => 'time_range' ),
            ),

            'time_end' => array(
                'label'   => __( '🕐 종료 시간', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-clock',
                'group'   => 'time',
                'type'    => 'time',
                'depends' => array( 'time_based' => 'time_range' ),
            ),

            // 요일 선택 (신규)
            'weekday_select' => array(
                'label'   => __( '📆 요일 선택', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-calendar',
                'group'   => 'time',
                'type'    => 'checkbox',
                'depends' => array( 'time_based' => 'weekdays' ),
                'options' => array(
                    '0' => __( '일요일', 'acf-code-snippets-box' ),
                    '1' => __( '월요일', 'acf-code-snippets-box' ),
                    '2' => __( '화요일', 'acf-code-snippets-box' ),
                    '3' => __( '수요일', 'acf-code-snippets-box' ),
                    '4' => __( '목요일', 'acf-code-snippets-box' ),
                    '5' => __( '금요일', 'acf-code-snippets-box' ),
                    '6' => __( '토요일', 'acf-code-snippets-box' ),
                ),
            ),

            // 쿠키 조건 (신규)
            'cookie_exists' => array(
                'label'       => __( '🍪 쿠키 존재', 'acf-code-snippets-box' ),
                'icon'        => 'dashicons-admin-settings',
                'group'       => 'advanced',
                'type'        => 'text',
                'placeholder' => __( '쿠키 이름 (예: visited, user_pref)', 'acf-code-snippets-box' ),
            ),

            'cookie_value' => array(
                'label'       => __( '🍪 쿠키 값', 'acf-code-snippets-box' ),
                'icon'        => 'dashicons-admin-settings',
                'group'       => 'advanced',
                'type'        => 'key_value',
                'placeholder' => __( '쿠키이름=값 (예: theme=dark)', 'acf-code-snippets-box' ),
            ),

            // 리퍼러 조건 (신규)
            'referrer' => array(
                'label'       => __( '🔙 리퍼러', 'acf-code-snippets-box' ),
                'icon'        => 'dashicons-external',
                'group'       => 'advanced',
                'type'        => 'text',
                'placeholder' => __( '예: google.com, facebook.com', 'acf-code-snippets-box' ),
                'description' => __( '방문자가 어디서 왔는지 확인 (도메인만 입력)', 'acf-code-snippets-box' ),
            ),

            // 언어 조건 (신규)
            'language' => array(
                'label'   => __( '🌍 언어', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-translation',
                'group'   => 'advanced',
                'type'    => 'select',
                'options' => 'languages', // 동적으로 가져옴
            ),

            // WooCommerce 조건 (신규)
            'woocommerce' => array(
                'label'   => __( '🛒 WooCommerce', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-cart',
                'group'   => 'integrations',
                'options' => array(
                    ''               => __( '선택 안 함', 'acf-code-snippets-box' ),
                    'is_shop'        => __( '상점 페이지', 'acf-code-snippets-box' ),
                    'is_product'     => __( '상품 페이지', 'acf-code-snippets-box' ),
                    'is_cart'        => __( '장바구니 페이지', 'acf-code-snippets-box' ),
                    'is_checkout'    => __( '결제 페이지', 'acf-code-snippets-box' ),
                    'is_account'     => __( '계정 페이지', 'acf-code-snippets-box' ),
                    'cart_has_items' => __( '장바구니에 상품 있음', 'acf-code-snippets-box' ),
                    'cart_empty'     => __( '장바구니 비어있음', 'acf-code-snippets-box' ),
                ),
                'condition' => 'class_exists("WooCommerce")',
            ),

            // 멀티사이트 조건 (신규)
            'multisite' => array(
                'label'   => __( '🏢 멀티사이트', 'acf-code-snippets-box' ),
                'icon'    => 'dashicons-networking',
                'group'   => 'integrations',
                'type'    => 'select',
                'options' => 'sites', // 동적으로 가져옴
                'condition' => 'is_multisite()',
            ),
        );
    }

    /**
     * 트리거 그룹 정의
     * [v5.0.0]
     */
    public static function get_trigger_groups() {
        return array(
            'location' => array(
                'label' => __( '📍 위치', 'acf-code-snippets-box' ),
                'icon'  => 'dashicons-location',
            ),
            'url' => array(
                'label' => __( '🔗 URL', 'acf-code-snippets-box' ),
                'icon'  => 'dashicons-admin-links',
            ),
            'user' => array(
                'label' => __( '👤 사용자', 'acf-code-snippets-box' ),
                'icon'  => 'dashicons-admin-users',
            ),
            'device' => array(
                'label' => __( '📱 디바이스', 'acf-code-snippets-box' ),
                'icon'  => 'dashicons-smartphone',
            ),
            'time' => array(
                'label' => __( '⏰ 시간', 'acf-code-snippets-box' ),
                'icon'  => 'dashicons-clock',
            ),
            'advanced' => array(
                'label' => __( '⚙️ 고급', 'acf-code-snippets-box' ),
                'icon'  => 'dashicons-admin-generic',
            ),
            'integrations' => array(
                'label' => __( '🔌 통합', 'acf-code-snippets-box' ),
                'icon'  => 'dashicons-admin-plugins',
            ),
        );
    }

    /**
     * 코드 출력 위치 옵션
     * [v5.0.0] 신규
     */
    public static function get_code_locations() {
        return array(
            'css' => array(
                'label'   => __( 'CSS 출력 위치', 'acf-code-snippets-box' ),
                'options' => array(
                    'wp_head'          => __( 'wp_head (기본, <head> 태그 내)', 'acf-code-snippets-box' ),
                    'wp_head_early'    => __( 'wp_head (우선순위 1, 가장 먼저)', 'acf-code-snippets-box' ),
                    'wp_head_late'     => __( 'wp_head (우선순위 999, 가장 나중)', 'acf-code-snippets-box' ),
                    'wp_footer'        => __( 'wp_footer (</body> 직전)', 'acf-code-snippets-box' ),
                    'inline'           => __( 'Inline (특정 요소 앞에)', 'acf-code-snippets-box' ),
                    'admin_head'       => __( 'admin_head (관리자 <head>)', 'acf-code-snippets-box' ),
                    'login_head'       => __( 'login_head (로그인 페이지)', 'acf-code-snippets-box' ),
                ),
            ),
            'js' => array(
                'label'   => __( 'JavaScript 출력 위치', 'acf-code-snippets-box' ),
                'options' => array(
                    'wp_footer'        => __( 'wp_footer (기본, </body> 직전)', 'acf-code-snippets-box' ),
                    'wp_head'          => __( 'wp_head (<head> 태그 내)', 'acf-code-snippets-box' ),
                    'wp_footer_early'  => __( 'wp_footer (우선순위 1, 가장 먼저)', 'acf-code-snippets-box' ),
                    'wp_footer_late'   => __( 'wp_footer (우선순위 999, 가장 나중)', 'acf-code-snippets-box' ),
                    'admin_footer'     => __( 'admin_footer (관리자 푸터)', 'acf-code-snippets-box' ),
                    'login_footer'     => __( 'login_footer (로그인 페이지 푸터)', 'acf-code-snippets-box' ),
                    'jquery_ready'     => __( 'jQuery ready ($(document).ready())', 'acf-code-snippets-box' ),
                    'dom_loaded'       => __( 'DOMContentLoaded', 'acf-code-snippets-box' ),
                    'window_load'      => __( 'window.onload', 'acf-code-snippets-box' ),
                ),
            ),
            'html' => array(
                'label'   => __( 'HTML 출력 위치', 'acf-code-snippets-box' ),
                'options' => array(
                    'wp_body_open'     => __( 'wp_body_open (기본, <body> 직후)', 'acf-code-snippets-box' ),
                    'wp_footer'        => __( 'wp_footer (</body> 직전)', 'acf-code-snippets-box' ),
                    'the_content'      => __( 'the_content 필터 (콘텐츠 앞/뒤)', 'acf-code-snippets-box' ),
                    'before_content'   => __( 'the_content 필터 (콘텐츠 앞)', 'acf-code-snippets-box' ),
                    'after_content'    => __( 'the_content 필터 (콘텐츠 뒤)', 'acf-code-snippets-box' ),
                    'shortcode'        => __( '쇼트코드로만 출력', 'acf-code-snippets-box' ),
                ),
            ),
            'php' => array(
                'label'   => __( 'PHP 실행 훅', 'acf-code-snippets-box' ),
                'options' => array(
                    'init'             => __( 'init (기본)', 'acf-code-snippets-box' ),
                    'after_setup_theme' => __( 'after_setup_theme (테마 설정 후)', 'acf-code-snippets-box' ),
                    'wp_loaded'        => __( 'wp_loaded (WP 완전 로드 후)', 'acf-code-snippets-box' ),
                    'template_redirect' => __( 'template_redirect (템플릿 결정 후)', 'acf-code-snippets-box' ),
                    'wp'               => __( 'wp (메인 쿼리 설정 후)', 'acf-code-snippets-box' ),
                    'wp_head'          => __( 'wp_head (헤더 출력 시)', 'acf-code-snippets-box' ),
                    'wp_footer'        => __( 'wp_footer (푸터 출력 시)', 'acf-code-snippets-box' ),
                    'shutdown'         => __( 'shutdown (종료 시)', 'acf-code-snippets-box' ),
                    'admin_init'       => __( 'admin_init (관리자 초기화)', 'acf-code-snippets-box' ),
                    'rest_api_init'    => __( 'rest_api_init (REST API 초기화)', 'acf-code-snippets-box' ),
                    'custom'           => __( '커스텀 훅 지정', 'acf-code-snippets-box' ),
                ),
            ),
        );
    }

    /**
     * 우선순위 프리셋
     * [v5.0.0] 신규
     */
    public static function get_priority_presets() {
        return array(
            1    => __( '🔴 최우선 (1)', 'acf-code-snippets-box' ),
            5    => __( '🟠 높음 (5)', 'acf-code-snippets-box' ),
            10   => __( '🟡 기본 (10)', 'acf-code-snippets-box' ),
            50   => __( '🟢 보통 (50)', 'acf-code-snippets-box' ),
            100  => __( '🔵 낮음 (100)', 'acf-code-snippets-box' ),
            999  => __( '⚪ 최후 (999)', 'acf-code-snippets-box' ),
        );
    }

    /**
     * 포스트 타입 목록 가져오기
     */
    public static function get_post_types() {
        $post_types = get_post_types( array( 'public' => true ), 'objects' );
        $result = array();

        foreach ( $post_types as $post_type ) {
            $result[ $post_type->name ] = $post_type->label;
        }

        return $result;
    }

    /**
     * 택소노미 목록 가져오기
     */
    public static function get_taxonomies() {
        $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
        $result = array();

        foreach ( $taxonomies as $taxonomy ) {
            $result[ $taxonomy->name ] = $taxonomy->label;
        }

        return $result;
    }

    /**
     * 사용 가능한 언어 목록 가져오기
     * [v5.0.0]
     */
    public static function get_available_languages() {
        $languages = array(
            ''      => __( '모든 언어', 'acf-code-snippets-box' ),
            'ko_KR' => __( '한국어', 'acf-code-snippets-box' ),
            'en_US' => __( '영어 (미국)', 'acf-code-snippets-box' ),
            'en_GB' => __( '영어 (영국)', 'acf-code-snippets-box' ),
            'ja'    => __( '일본어', 'acf-code-snippets-box' ),
            'zh_CN' => __( '중국어 (간체)', 'acf-code-snippets-box' ),
            'zh_TW' => __( '중국어 (번체)', 'acf-code-snippets-box' ),
            'es_ES' => __( '스페인어', 'acf-code-snippets-box' ),
            'fr_FR' => __( '프랑스어', 'acf-code-snippets-box' ),
            'de_DE' => __( '독일어', 'acf-code-snippets-box' ),
        );

        // WPML/Polylang 연동
        if ( function_exists( 'icl_get_languages' ) ) {
            $wpml_languages = icl_get_languages( 'skip_missing=0' );
            foreach ( $wpml_languages as $lang ) {
                $languages[ $lang['language_code'] ] = $lang['native_name'];
            }
        } elseif ( function_exists( 'pll_languages_list' ) ) {
            $pll_languages = pll_languages_list( array( 'fields' => 'name' ) );
            $pll_slugs = pll_languages_list( array( 'fields' => 'slug' ) );
            foreach ( $pll_slugs as $index => $slug ) {
                $languages[ $slug ] = $pll_languages[ $index ];
            }
        }

        return $languages;
    }

    /**
     * 브라우저 감지
     * [v5.0.0]
     */
    public static function detect_browser() {
        if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
            return 'unknown';
        }

        $user_agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );

        if ( strpos( $user_agent, 'Edg' ) !== false ) {
            return 'edge';
        } elseif ( strpos( $user_agent, 'Chrome' ) !== false ) {
            return 'chrome';
        } elseif ( strpos( $user_agent, 'Firefox' ) !== false ) {
            return 'firefox';
        } elseif ( strpos( $user_agent, 'Safari' ) !== false ) {
            return 'safari';
        } elseif ( strpos( $user_agent, 'MSIE' ) !== false || strpos( $user_agent, 'Trident' ) !== false ) {
            return 'ie';
        }

        return 'unknown';
    }

    /**
     * URL 패턴 매칭
     * [v5.0.0]
     */
    public static function match_url_pattern( $pattern, $url = null ) {
        if ( is_null( $url ) ) {
            $url = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
        }

        // 와일드카드를 정규표현식으로 변환
        $regex = str_replace(
            array( '*', '?' ),
            array( '.*', '.' ),
            preg_quote( $pattern, '/' )
        );

        return (bool) preg_match( '/^' . $regex . '$/i', $url );
    }

    /**
     * 트리거 조건 평가
     * [v5.0.0] 확장된 조건 평가 시스템
     */
    public static function evaluate_trigger( $trigger_key, $trigger_value, $settings = array() ) {
        switch ( $trigger_key ) {
            case 'url_pattern':
                if ( empty( $trigger_value ) ) {
                    return true;
                }
                $patterns = array_filter( array_map( 'trim', explode( "\n", $trigger_value ) ) );
                foreach ( $patterns as $pattern ) {
                    if ( self::match_url_pattern( $pattern ) ) {
                        return true;
                    }
                }
                return false;

            case 'url_exclude':
                if ( empty( $trigger_value ) ) {
                    return true;
                }
                $patterns = array_filter( array_map( 'trim', explode( "\n", $trigger_value ) ) );
                foreach ( $patterns as $pattern ) {
                    if ( self::match_url_pattern( $pattern ) ) {
                        return false; // 제외 패턴에 매칭되면 실행하지 않음
                    }
                }
                return true;

            case 'query_string':
                if ( empty( $trigger_value ) ) {
                    return true;
                }
                // key=value 형태 파싱
                $pairs = array_filter( array_map( 'trim', explode( ',', $trigger_value ) ) );
                foreach ( $pairs as $pair ) {
                    if ( strpos( $pair, '=' ) !== false ) {
                        list( $key, $value ) = explode( '=', $pair, 2 );
                        $key = trim( $key );
                        $value = trim( $value );
                        if ( ! isset( $_GET[ $key ] ) || $_GET[ $key ] !== $value ) {
                            return false;
                        }
                    } else {
                        // 키만 있는 경우 존재 여부만 확인
                        if ( ! isset( $_GET[ trim( $pair ) ] ) ) {
                            return false;
                        }
                    }
                }
                return true;

            case 'browser':
                if ( empty( $trigger_value ) || $trigger_value === 'all' ) {
                    return true;
                }
                return self::detect_browser() === $trigger_value;

            case 'user_capability':
                if ( empty( $trigger_value ) ) {
                    return true;
                }
                return current_user_can( $trigger_value );

            case 'cookie_exists':
                if ( empty( $trigger_value ) ) {
                    return true;
                }
                return isset( $_COOKIE[ $trigger_value ] );

            case 'cookie_value':
                if ( empty( $trigger_value ) ) {
                    return true;
                }
                if ( strpos( $trigger_value, '=' ) !== false ) {
                    list( $key, $value ) = explode( '=', $trigger_value, 2 );
                    return isset( $_COOKIE[ trim( $key ) ] ) && $_COOKIE[ trim( $key ) ] === trim( $value );
                }
                return false;

            case 'referrer':
                if ( empty( $trigger_value ) ) {
                    return true;
                }
                $referrer = isset( $_SERVER['HTTP_REFERER'] ) ? wp_parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_HOST ) : '';
                $domains = array_filter( array_map( 'trim', explode( ',', $trigger_value ) ) );
                foreach ( $domains as $domain ) {
                    if ( strpos( $referrer, $domain ) !== false ) {
                        return true;
                    }
                }
                return false;

            case 'language':
                if ( empty( $trigger_value ) ) {
                    return true;
                }
                $current_locale = get_locale();
                return $current_locale === $trigger_value || strpos( $current_locale, $trigger_value ) === 0;

            case 'time_based':
                return self::evaluate_time_trigger( $trigger_value, $settings );

            case 'woocommerce':
                return self::evaluate_woocommerce_trigger( $trigger_value );

            default:
                return true;
        }
    }

    /**
     * 시간 기반 트리거 평가
     * [v5.0.0]
     */
    private static function evaluate_time_trigger( $type, $settings ) {
        if ( $type === 'always' || empty( $type ) ) {
            return true;
        }

        $current_time = current_time( 'timestamp' );

        switch ( $type ) {
            case 'date_range':
                $start = isset( $settings['date_start'] ) ? strtotime( $settings['date_start'] ) : 0;
                $end = isset( $settings['date_end'] ) ? strtotime( $settings['date_end'] . ' 23:59:59' ) : PHP_INT_MAX;
                return $current_time >= $start && $current_time <= $end;

            case 'time_range':
                $start_time = isset( $settings['time_start'] ) ? $settings['time_start'] : '00:00';
                $end_time = isset( $settings['time_end'] ) ? $settings['time_end'] : '23:59';
                $current_hour_min = current_time( 'H:i' );
                return $current_hour_min >= $start_time && $current_hour_min <= $end_time;

            case 'weekdays':
                $selected_days = isset( $settings['weekday_select'] ) ? (array) $settings['weekday_select'] : array();
                $current_day = current_time( 'w' ); // 0 (일요일) ~ 6 (토요일)
                return in_array( (string) $current_day, $selected_days, true );
        }

        return true;
    }

    /**
     * WooCommerce 트리거 평가
     * [v5.0.0]
     */
    private static function evaluate_woocommerce_trigger( $condition ) {
        if ( empty( $condition ) || ! class_exists( 'WooCommerce' ) ) {
            return true;
        }

        switch ( $condition ) {
            case 'is_shop':
                return function_exists( 'is_shop' ) && is_shop();
            case 'is_product':
                return function_exists( 'is_product' ) && is_product();
            case 'is_cart':
                return function_exists( 'is_cart' ) && is_cart();
            case 'is_checkout':
                return function_exists( 'is_checkout' ) && is_checkout();
            case 'is_account':
                return function_exists( 'is_account_page' ) && is_account_page();
            case 'cart_has_items':
                return function_exists( 'WC' ) && WC()->cart && ! WC()->cart->is_empty();
            case 'cart_empty':
                return function_exists( 'WC' ) && WC()->cart && WC()->cart->is_empty();
        }

        return true;
    }
}
