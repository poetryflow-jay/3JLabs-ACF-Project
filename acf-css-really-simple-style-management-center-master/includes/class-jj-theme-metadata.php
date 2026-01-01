<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 테마 및 플러그인 메타데이터 관리 클래스
 * 
 * 각 어댑터에서 제공하는 설정값의 적용 위치 정보를 중앙 관리합니다.
 * 툴팁 및 설명에 사용됩니다.
 * 
 * @since v3.8.0
 */
final class JJ_Theme_Metadata {

    private static $instance = null;
    private $metadata = array();

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_default_metadata();
    }

    /**
     * 기본 메타데이터 초기화
     */
    private function init_default_metadata() {
        // 브랜드 팔레트 기본 메타데이터
        $this->metadata['palettes']['brand'] = array(
            'primary_color' => array(
                'name' => __( 'Primary Color', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '메인 브랜드 컬러로, 버튼, 링크, 주요 CTA 등에 사용됩니다.', 'acf-css-really-simple-style-management-center' ),
                'selectors' => array(
                    '.button',
                    'button',
                    'a.button',
                    '.wp-block-button__link',
                    '.elementor-button',
                ),
                'apply_to' => __( '버튼, 링크, 주요 CTA, 헤더 액션', 'acf-css-really-simple-style-management-center' ),
            ),
            'primary_color_hover' => array(
                'name' => __( 'Primary Color (Hover)', 'acf-css-really-simple-style-management-center' ),
                'description' => __( 'Primary Color의 호버 상태 색상입니다.', 'acf-css-really-simple-style-management-center' ),
                'selectors' => array(
                    '.button:hover',
                    'button:hover',
                    'a.button:hover',
                    '.wp-block-button__link:hover',
                    '.elementor-button:hover',
                ),
                'apply_to' => __( '버튼 호버, 링크 호버', 'acf-css-really-simple-style-management-center' ),
            ),
            'secondary_color' => array(
                'name' => __( 'Secondary Color', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '보조 브랜드 컬러로, 보조 버튼이나 강조 요소에 사용됩니다.', 'acf-css-really-simple-style-management-center' ),
                'selectors' => array(
                    '.button-secondary',
                    '.button.alt',
                    '.btn-secondary',
                ),
                'apply_to' => __( '보조 버튼, 강조 요소', 'acf-css-really-simple-style-management-center' ),
            ),
            'secondary_color_hover' => array(
                'name' => __( 'Secondary Color (Hover)', 'acf-css-really-simple-style-management-center' ),
                'description' => __( 'Secondary Color의 호버 상태 색상입니다.', 'acf-css-really-simple-style-management-center' ),
                'selectors' => array(
                    '.button-secondary:hover',
                    '.button.alt:hover',
                    '.btn-secondary:hover',
                ),
                'apply_to' => __( '보조 버튼 호버', 'acf-css-really-simple-style-management-center' ),
            ),
        );

        // 시스템 팔레트 기본 메타데이터
        $this->metadata['palettes']['system'] = array(
            'background_color' => array(
                'name' => __( 'Background Color', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '사이트 배경색입니다.', 'acf-css-really-simple-style-management-center' ),
                'selectors' => array(
                    'body',
                    '.site',
                ),
                'apply_to' => __( '사이트 전체 배경', 'acf-css-really-simple-style-management-center' ),
            ),
            'text_color' => array(
                'name' => __( 'Text Color', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '본문 텍스트 색상입니다.', 'acf-css-really-simple-style-management-center' ),
                'selectors' => array(
                    'body',
                    'p',
                    '.entry-content',
                ),
                'apply_to' => __( '본문 텍스트, 일반 텍스트', 'acf-css-really-simple-style-management-center' ),
            ),
            'link_color' => array(
                'name' => __( 'Link Color', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '일반 링크 색상입니다.', 'acf-css-really-simple-style-management-center' ),
                'selectors' => array(
                    'a',
                    '.entry-content a',
                ),
                'apply_to' => __( '일반 링크, 콘텐츠 링크', 'acf-css-really-simple-style-management-center' ),
            ),
        );

        // 타이포그래피 기본 메타데이터
        $tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p' );
        foreach ( $tags as $tag ) {
            $this->metadata['typography'][ $tag ] = array(
                'name' => strtoupper( $tag ),
                'description' => sprintf( __( '%s 태그의 폰트 설정입니다.', 'acf-css-really-simple-style-management-center' ), strtoupper( $tag ) ),
                'selectors' => array( $tag ),
                'apply_to' => sprintf( __( '%s 태그', 'acf-css-really-simple-style-management-center' ), strtoupper( $tag ) ),
            );
        }

        // 버튼 기본 메타데이터
        $this->metadata['buttons']['primary'] = array(
            'background_color' => array(
                'name' => __( 'Background Color', 'acf-css-really-simple-style-management-center' ),
                'description' => __( 'Primary 버튼의 배경색입니다.', 'acf-css-really-simple-style-management-center' ),
                'selectors' => array(
                    '.button',
                    'button',
                    '.wp-block-button__link',
                ),
                'apply_to' => __( 'Primary 버튼 배경', 'acf-css-really-simple-style-management-center' ),
            ),
            'text_color' => array(
                'name' => __( 'Text Color', 'acf-css-really-simple-style-management-center' ),
                'description' => __( 'Primary 버튼의 텍스트 색상입니다.', 'acf-css-really-simple-style-management-center' ),
                'selectors' => array(
                    '.button',
                    'button',
                    '.wp-block-button__link',
                ),
                'apply_to' => __( 'Primary 버튼 텍스트', 'acf-css-really-simple-style-management-center' ),
            ),
        );
    }

    /**
     * 테마별 메타데이터 등록
     * 
     * @param string $theme_slug 테마 슬러그
     * @param array $metadata 메타데이터 배열
     */
    public function register_theme_metadata( $theme_slug, $metadata ) {
        if ( ! isset( $this->metadata['themes'] ) ) {
            $this->metadata['themes'] = array();
        }
        
        $this->metadata['themes'][ $theme_slug ] = $metadata;
    }

    /**
     * 플러그인별 메타데이터 등록
     * 
     * @param string $plugin_slug 플러그인 슬러그
     * @param array $metadata 메타데이터 배열
     */
    public function register_plugin_metadata( $plugin_slug, $metadata ) {
        if ( ! isset( $this->metadata['plugins'] ) ) {
            $this->metadata['plugins'] = array();
        }
        
        $this->metadata['plugins'][ $plugin_slug ] = $metadata;
    }

    /**
     * 메타데이터 가져오기
     * 
     * @param string $category 카테고리 (palettes, typography, buttons 등)
     * @param string $type 타입 (brand, system, primary 등)
     * @param string $key 키 (primary_color, font_family 등)
     * @param string $theme_slug 테마 슬러그 (선택)
     * @param string $plugin_slug 플러그인 슬러그 (선택)
     * @return array|null 메타데이터 배열 또는 null
     */
    public function get_metadata( $category, $type, $key, $theme_slug = '', $plugin_slug = '' ) {
        // 테마별 메타데이터 우선
        if ( ! empty( $theme_slug ) && isset( $this->metadata['themes'][ $theme_slug ][ $category ][ $type ][ $key ] ) ) {
            return $this->metadata['themes'][ $theme_slug ][ $category ][ $type ][ $key ];
        }
        
        // 플러그인별 메타데이터
        if ( ! empty( $plugin_slug ) && isset( $this->metadata['plugins'][ $plugin_slug ][ $category ][ $type ][ $key ] ) ) {
            return $this->metadata['plugins'][ $plugin_slug ][ $category ][ $type ][ $key ];
        }
        
        // 기본 메타데이터
        if ( isset( $this->metadata[ $category ][ $type ][ $key ] ) ) {
            return $this->metadata[ $category ][ $type ][ $key ];
        }
        
        return null;
    }

    /**
     * 현재 활성 테마/플러그인에 맞는 통합 설명 생성
     * 
     * @param string $category 카테고리
     * @param string $type 타입
     * @param string $key 키
     * @return string 설명 텍스트
     */
    public function get_combined_description( $category, $type, $key ) {
        $theme_slug = $this->get_active_theme_slug();
        $active_plugins = $this->get_active_plugin_slugs();
        
        $meta = $this->get_metadata( $category, $type, $key, $theme_slug );
        if ( ! $meta ) {
            return '';
        }
        
        $description = $meta['description'] ?? '';
        $apply_to = $meta['apply_to'] ?? '';
        $selectors = $meta['selectors'] ?? array();
        
        // 테마 정보 추가
        if ( ! empty( $theme_slug ) ) {
            $theme_obj = wp_get_theme();
            $theme_name = $theme_obj->get( 'Name' );
            $child_theme = $theme_obj->parent();
            
            $theme_info = '';
            if ( $child_theme ) {
                $parent_name = $child_theme->get( 'Name' );
                $theme_info = sprintf( 
                    __( '현재 설치된 %s 테마의 차일드 테마인 %s에서 ', 'acf-css-really-simple-style-management-center' ),
                    $parent_name,
                    $theme_name
                );
            } else {
                $theme_info = sprintf( 
                    __( '현재 설치된 %s 테마에서 ', 'acf-css-really-simple-style-management-center' ),
                    $theme_name
                );
            }
            
            $description = $theme_info . $description;
        }
        
        // 적용 위치 정보 추가
        if ( ! empty( $apply_to ) ) {
            $description .= ' ' . sprintf( __( '이 색상은 %s에 적용됩니다.', 'acf-css-really-simple-style-management-center' ), $apply_to );
        }
        
        // 선택자 정보 추가 (기술적 정보)
        if ( ! empty( $selectors ) && count( $selectors ) > 0 ) {
            $selector_text = implode( ', ', array_slice( $selectors, 0, 3 ) );
            if ( count( $selectors ) > 3 ) {
                $selector_text .= ' ...';
            }
            $description .= ' ' . sprintf( __( 'CSS 선택자: %s', 'acf-css-really-simple-style-management-center' ), $selector_text );
        }
        
        return $description;
    }

    /**
     * 활성 테마 슬러그 가져오기
     */
    private function get_active_theme_slug() {
        $theme = wp_get_theme();
        $parent = $theme->parent();
        if ( $parent ) {
            return $parent->get_template();
        }
        return $theme->get_template();
    }

    /**
     * 활성 플러그인 슬러그 목록 가져오기
     */
    private function get_active_plugin_slugs() {
        $active_plugins = get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
        }
        
        $slugs = array();
        foreach ( $active_plugins as $plugin ) {
            $parts = explode( '/', $plugin );
            if ( ! empty( $parts[0] ) ) {
                $slugs[] = $parts[0];
            }
        }
        
        return $slugs;
    }
}
