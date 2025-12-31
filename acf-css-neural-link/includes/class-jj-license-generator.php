<?php
/**
 * 라이센스 키 생성 클래스
 * 
 * @package JJ_LicenseManagerincludes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Generator {
    
    /**
     * 라이센스 키 생성
     * 
     * 형식: JJ-[VERSION]-[TYPE]-[RANDOM]-[CHECKSUM]
     * 예: JJ-5.1-BASIC-A1B2C3D4-E5F6G7H8
     * 
     * @param string $type 라이센스 타입 (FREE, BASIC, PREM, UNLIM)
     * @param string $version 플러그인 버전 (기본값: 현재 버전)
     * @return string 라이센스 키
     */
    public static function generate( $type, $version = null ) {
        if ( is_null( $version ) ) {
            $version = '5.1';
        }
        
        // 랜덤 문자열 생성 (8자리)
        $random = strtoupper( wp_generate_password( 8, false ) );
        
        // 체크섬 생성 (외부에서 확인 불가능하도록 서버 전용 키 사용)
        $secret_key = get_option( 'jj_license_manager_secret_key', wp_generate_password( 32, true, true ) );
        if ( ! get_option( 'jj_license_manager_secret_key' ) ) {
            update_option( 'jj_license_manager_secret_key', $secret_key );
        }
        
        $checksum = strtoupper( substr( md5( $version . $type . $random . $secret_key ), 0, 8 ) );
        
        // 라이센스 키 조합
        $license_key = sprintf( 'JJ-%s-%s-%s-%s', $version, $type, $random, $checksum );
        
        return $license_key;
    }
    
    /**
     * 라이센스 키 형식 검증
     * 
     * @param string $license_key 라이센스 키
     * @return bool 유효 여부
     */
    public static function validate_format( $license_key ) {
        // 형식만 확인 (실제 유효성은 데이터베이스에서 확인)
        return preg_match( '/^JJ-\d+\.\d+-(FREE|BASIC|PREM|UNLIM)-[A-Z0-9]{8}-[A-Z0-9]{8}$/i', $license_key ) === 1;
    }
    
    /**
     * 라이센스 타입 파싱
     * 
     * @param string $license_key 라이센스 키
     * @return string|false 라이센스 타입 또는 false
     */
    public static function parse_type( $license_key ) {
        if ( ! self::validate_format( $license_key ) ) {
            return false;
        }
        
        $parts = explode( '-', $license_key );
        if ( count( $parts ) >= 3 ) {
            return strtoupper( $parts[2] );
        }
        
        return false;
    }
}

