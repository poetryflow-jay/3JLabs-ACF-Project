<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // 직접 접근 차단
}

/**
 * JJ Edition Controller
 * 
 * 플러그인의 에디션(Free, Pro, Partner, Master)을 정의하고
 * 각 에디션별 기능 사용 권한(Capability)을 관리하는 중앙 통제 장치입니다.
 * 
 * @since v5.5.0
 * @author Jason (CTO)
 */
final class JJ_Edition_Controller {

    private static $instance = null;

    // 에디션 상수
    const EDITION_FREE    = 'free';
    const EDITION_BASIC   = 'basic';
    const EDITION_PREMIUM = 'premium';
    const EDITION_PARTNER = 'partner';
    const EDITION_MASTER  = 'master';

    // 현재 에디션
    private $current_edition = self::EDITION_FREE;

    // 기능 목록 (Capabilities)
    private $capabilities = array();

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_edition();
        $this->init_capabilities();
    }

    /**
     * 현재 에디션 초기화
     * 우선순위: 1. 상수 정의 (MASTER 강제) -> 2. DB 옵션 -> 3. 기본값(Free)
     */
    private function init_edition() {
        // [Safety Lock] 상수가 MASTER라면 무조건 MASTER 권한 부여 (다른 로직 무시)
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) === 'MASTER' ) {
            $this->current_edition = self::EDITION_MASTER;
            return;
        }

        // 1. wp-config.php 등에서 정의된 상수 확인 (개발 및 테스트용)
        if ( defined( 'JJ_STYLE_GUIDE_EDITION_OVERRIDE' ) ) {
            $this->current_edition = JJ_STYLE_GUIDE_EDITION_OVERRIDE;
            return;
        }

        // 2. 일반 상수 확인
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $type = strtolower( JJ_STYLE_GUIDE_LICENSE_TYPE );
            switch ( $type ) {
                case 'dev':
                case 'master':
                    $this->current_edition = self::EDITION_MASTER;
                    break;
                case 'partner':
                    $this->current_edition = self::EDITION_PARTNER;
                    break;
                case 'premium':
                case 'unlimited':
                    $this->current_edition = self::EDITION_PREMIUM;
                    break;
                case 'basic':
                    $this->current_edition = self::EDITION_BASIC;
                    break;
                default:
                    $this->current_edition = self::EDITION_FREE;
            }
        }
    }

    /**
     * 에디션별 기능 권한 설정
     */
    private function init_capabilities() {
        // 기본 권한 (Free)
        $caps = array(
            'core_style_management' => true,
            'brand_palette'         => true,
            'basic_typography'      => true,
            'limited_adapters'      => true, // 테마 2개, 플러그인 1개
            'show_watermark'        => true,
            'show_upgrade_prompt'   => true,
        );

        // Basic 추가 권한
        if ( $this->is_at_least( self::EDITION_BASIC ) ) {
            $caps['system_palette']      = true;
            $caps['extended_adapters']   = true; // 테마 5개, 플러그인 5개
            $caps['show_watermark']      = false; // 워터마크 제거
            $caps['login_customizer']    = true; // [v5.6.0] 로그인 커스터마이저
        }

        // Premium/Unlimited 추가 권한
        if ( $this->is_at_least( self::EDITION_PREMIUM ) ) {
            $caps['all_palettes']        = true; // Alternative, Another, Temp
            $caps['all_adapters']        = true; // 무제한
            $caps['labs_center']         = true; // 실험실 접근
            $caps['admin_center_full']   = true; // 관리자 센터 전체
            $caps['show_upgrade_prompt'] = false; // 업그레이드 프롬프트 제거
            $caps['admin_theme']         = true; // [v5.6.0] 어드민 테마
        }

        // Partner 추가 권한
        if ( $this->is_at_least( self::EDITION_PARTNER ) ) {
            $caps['white_labeling']      = true; // 브랜드 숨김
            $caps['remote_control']      = true; // 원격 제어
        }

        // Master 추가 권한
        if ( $this->current_edition === self::EDITION_MASTER ) {
            $caps['license_issuing']     = true; // 라이센스 발급
            $caps['code_integrity']      = true; // 코드 무결성 감시
            $caps['debug_tools']         = true; // 디버깅 도구
            
            // Master는 모든 기능 활성화
            $caps['all_palettes']        = true;
            $caps['all_adapters']        = true;
            $caps['show_watermark']      = false;
            $caps['show_upgrade_prompt'] = false;
            $caps['login_customizer']    = true;
            $caps['admin_theme']         = true;
        }

        $this->capabilities = $caps;
    }

    /**
     * 특정 기능 사용 가능 여부 확인
     */
    public function has_capability( $cap ) {
        // [Safety Lock] MASTER 버전은 모든 권한 허용
        if ( $this->current_edition === self::EDITION_MASTER ) {
            return true;
        }
        return isset( $this->capabilities[ $cap ] ) && $this->capabilities[ $cap ];
    }

    /**
     * 현재 에디션이 특정 등급 이상인지 확인
     */
    public function is_at_least( $target_edition ) {
        $hierarchy = array(
            self::EDITION_FREE    => 0,
            self::EDITION_BASIC   => 1,
            self::EDITION_PREMIUM => 2,
            self::EDITION_PARTNER => 3,
            self::EDITION_MASTER  => 4
        );

        $current_level = isset( $hierarchy[ $this->current_edition ] ) ? $hierarchy[ $this->current_edition ] : 0;
        $target_level  = isset( $hierarchy[ $target_edition ] ) ? $hierarchy[ $target_edition ] : 0;

        return $current_level >= $target_level;
    }

    /**
     * 현재 에디션 조회
     */
    public function get_current_edition() {
        return $this->current_edition;
    }

    /**
     * 라이센스 타입 조회 (대문자 반환)
     * Partner Hub 등에서 사용
     */
    public function get_license_type() {
        return strtoupper( $this->current_edition );
    }

    /**
     * 어댑터 설정 필터링
     * 에디션 권한에 따라 사용 가능한 어댑터 제한
     */
    public function filter_adapters( $config ) {
        // 모든 어댑터 사용 권한이 있으면 필터링 없이 반환
        if ( $this->has_capability( 'all_adapters' ) ) {
            return $config;
        }

        // 제한된 어댑터 개수 설정
        $allowed_themes_count = 2; // Default Free
        $allowed_plugins_count = 1; // Default Free

        if ( $this->has_capability( 'extended_adapters' ) ) {
            $allowed_themes_count = 5;
            $allowed_plugins_count = 5;
        } elseif ( ! $this->has_capability( 'limited_adapters' ) ) {
            return array( 'themes' => array(), 'spokes' => array() );
        }

        // 화이트리스트 정의 (인기 항목 우선)
        $allowed_theme_slugs = array( 
            'twentytwentyfour', 'twentytwentyfive', 
            'kadence', 'astra', 'generatepress', 
            'oceanwp', 'hello-elementor' 
        );
        
        $allowed_plugin_slugs = array( 
            'woocommerce', 
            'elementor', 'beaver-builder', 
            'bbpress', 'divi' 
        );

        // 개수 제한 적용
        $allowed_theme_slugs = array_slice( $allowed_theme_slugs, 0, $allowed_themes_count );
        $allowed_plugin_slugs = array_slice( $allowed_plugin_slugs, 0, $allowed_plugins_count );

        if ( isset( $config['themes'] ) && is_array( $config['themes'] ) ) {
            $config['themes'] = array_intersect_key( $config['themes'], array_flip( $allowed_theme_slugs ) );
        }

        if ( isset( $config['spokes'] ) && is_array( $config['spokes'] ) ) {
            $config['spokes'] = array_intersect_key( $config['spokes'], array_flip( $allowed_plugin_slugs ) );
        }

        return $config;
    }

    /**
     * [v5.5.0] 화이트 라벨링: 브랜딩 정보 조회
     * 
     * @param string $key 'name', 'menu_title', 'logo_url', 'footer_text', 'author'
     * @return string
     */
    public function get_branding( $key ) {
        // 기본값 설정
        $defaults = array(
            'name'        => 'ACF CSS Manager',
            'full_name'   => 'ACF CSS: Advanced Custom Fonts&Colors&Styles Setting',
            'menu_title'  => 'ACF CSS Manager',
            'logo_url'    => '', // 추후 기본 로고 URL 설정
            'footer_text' => 'Ultimate Design System by Jay & Jenny Labs',
            'author'      => 'Jay & Jenny Labs',
            'author_url'  => 'https://j-j-labs.com',
        );

        $value = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

        // 화이트 라벨링 권한이 없으면 기본값 반환
        if ( ! $this->has_capability( 'white_labeling' ) ) {
            return $value;
        }

        // 파트너 설정값 조회 (옵션에서 가져오기)
        // 성능을 위해 간단한 캐싱이나 전역 변수 사용 고려 가능
        // 여기서는 예시로 get_option 사용 (실제로는 Options Cache 사용 권장)
        if ( function_exists( 'get_option' ) ) {
            $options = get_option( 'jj_style_guide_options', array() );
            if ( isset( $options['branding'] ) && isset( $options['branding'][ $key ] ) && ! empty( $options['branding'][ $key ] ) ) {
                return $options['branding'][ $key ];
            }
        }

        return $value;
    }
}
