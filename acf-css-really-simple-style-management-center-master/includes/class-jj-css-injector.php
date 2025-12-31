<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CSS 인젝터 헬퍼 클래스
 * 
 * CSS를 안전하게 주입하는 공통 로직을 제공합니다.
 * 어댑터 간 중복 코드를 제거하고 일관성을 유지합니다.
 * 
 * @since v3.5.0
 */
final class JJ_CSS_Injector {
    
    /**
     * CSS를 적절한 핸들에 주입
     * 
     * @param string $css CSS 내용
     * @param array $preferred_handles 우선순위 핸들 배열
     * @param string $fallback_id 폴백 스타일 ID
     * @return bool 성공 여부
     */
    public static function inject( $css, $preferred_handles = array(), $fallback_id = 'jj-style-guide-fallback' ) {
        if ( empty( $css ) ) {
            return false;
        }
        
        // 기본 핸들 목록
        $default_handles = array(
            'kadence-global',
            'woocommerce-general',
            'wp-block-library',
        );
        
        // 우선순위 핸들과 기본 핸들 병합
        $handles = array_merge( $preferred_handles, $default_handles );
        
        // 프론트엔드/관리자 구분
        if ( is_admin() ) {
            $handles[] = 'wp-admin';
        }
        
        // 사용 가능한 핸들 찾기
        foreach ( $handles as $handle ) {
            if ( wp_style_is( $handle, 'enqueued' ) ) {
                wp_add_inline_style( $handle, $css );
                return true;
            }
        }
        
        // 폴백: 직접 출력 (가장 마지막에 실행되도록)
        add_action( 'wp_footer', function() use ( $css, $fallback_id ) {
            echo '<style id="' . esc_attr( $fallback_id ) . '">' . $css . '</style>';
        }, 999 );
        
        return true;
    }
    
    /**
     * 어댑터별 CSS 주입 (어댑터 전용)
     * 
     * @param string $css CSS 내용
     * @param string $adapter_name 어댑터 이름
     * @param array $adapter_handles 어댑터별 우선 핸들
     * @return bool 성공 여부
     */
    public static function inject_adapter( $css, $adapter_name, $adapter_handles = array() ) {
        $fallback_id = 'jj-adapter-' . sanitize_key( $adapter_name );
        return self::inject( $css, $adapter_handles, $fallback_id );
    }
}

