<?php
/**
 * 라이센스 강제 실행 클래스
 * 
 * 라이센스 검증을 다중 레이어로 강화하고, 모든 기능 사용 전 검증을 수행합니다.
 * 
 * @package JJ_Style_Guide
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Enforcement {
    
    private static $instance = null;
    private $verification_cache = array();
    private $last_server_verification = 0;
    private $server_verification_interval = 7 * 24 * 60 * 60; // 7일마다 강제 검증
    
    /**
     * 싱글톤 인스턴스
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 생성자
     */
    private function __construct() {
        // 모든 기능 호출 전 검증 훅 추가
        add_filter( 'jj_can_use_feature', array( $this, 'enforce_license_check' ), 1, 2 );
        add_filter( 'jj_get_license_type', array( $this, 'enforce_license_type' ), 1, 1 );
    }
    
    /**
     * 기능 사용 전 라이센스 강제 검증
     * 
     * @param bool $can_use 사용 가능 여부
     * @param string $feature 기능 이름
     * @return bool 검증된 사용 가능 여부
     */
    public function enforce_license_check( $can_use, $feature ) {
        // 코드 무결성 모니터 확인
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                return false;
            }
        }
        
        // 서버 검증 강제 (7일마다)
        $license_status = $this->force_server_verification();
        
        if ( ! $license_status || ! isset( $license_status['valid'] ) || ! $license_status['valid'] ) {
            return false;
        }
        
        // 라이센스 타입 검증
        $verified_type = $this->get_verified_license_type();
        if ( ! $verified_type ) {
            return false;
        }
        
        return $can_use;
    }
    
    /**
     * 라이센스 타입 강제 검증
     * 
     * @param string $license_type 라이센스 타입
     * @return string 검증된 라이센스 타입
     */
    public function enforce_license_type( $license_type ) {
        // 코드 무결성 모니터 확인
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                return 'FREE';
            }
        }
        
        // 서버 검증 결과 우선 사용
        $verified_type = $this->get_verified_license_type();
        if ( $verified_type && $verified_type !== $license_type ) {
            // 불일치 감지 시 개발자에게 알림
            $this->report_mismatch( $license_type, $verified_type );
            return $verified_type; // 서버 검증 결과 우선
        }
        
        return $license_type;
    }
    
    /**
     * 서버 검증 강제 실행
     * 
     * @return array|false 라이센스 상태 또는 false
     */
    private function force_server_verification() {
        $last_verification = get_option( 'jj_last_server_verification', 0 );
        $now = time();
        
        // 7일마다 강제 검증
        if ( $now - $last_verification < $this->server_verification_interval ) {
            // 캐시된 결과 사용
            $cached = get_option( 'jj_license_status_cache', null );
            if ( $cached && isset( $cached['valid_until'] ) && $now < $cached['valid_until'] ) {
                return $cached;
            }
        }
        
        // 서버 검증 실행
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $license_manager = JJ_License_Manager::instance();
            $license_status = $license_manager->verify_license( true ); // 강제 온라인 검증
            
            // 검증 시간 기록
            update_option( 'jj_last_server_verification', $now );
            
            // 캐시 저장 (7일간 유효)
            if ( $license_status && isset( $license_status['valid'] ) ) {
                $license_status['valid_until'] = $now + $this->server_verification_interval;
                update_option( 'jj_license_status_cache', $license_status );
            }
            
            return $license_status;
        }
        
        return false;
    }
    
    /**
     * 검증된 라이센스 타입 가져오기
     * 
     * @return string|null 검증된 라이센스 타입
     */
    private function get_verified_license_type() {
        $license_status = $this->force_server_verification();
        
        if ( $license_status && isset( $license_status['type'] ) && isset( $license_status['valid'] ) && $license_status['valid'] ) {
            return $license_status['type'];
        }
        
        return null;
    }
    
    /**
     * 라이센스 타입 불일치 보고
     * 
     * @param string $local_type 로컬 타입
     * @param string $server_type 서버 타입
     */
    private function report_mismatch( $local_type, $server_type ) {
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            $monitor->notify_developer( array(
                array(
                    'type' => 'license_type_mismatch',
                    'severity' => 'critical',
                    'local_type' => $local_type,
                    'server_type' => $server_type,
                ),
            ), array() );
        }
    }
    
    /**
     * 가짜 라이센스 키 감지
     * 
     * @param string $license_key 라이센스 키
     * @return bool 가짜 키 여부
     */
    public function detect_fake_license_key( $license_key ) {
        if ( empty( $license_key ) ) {
            return false;
        }
        
        // 형식 검증
        if ( ! preg_match( '/^JJ-\d+\.\d+-(FREE|BASIC|PREM|UNLIM)-[A-Z0-9]{8}-[A-Z0-9]{8}$/i', $license_key ) ) {
            return true; // 형식이 맞지 않으면 가짜
        }
        
        // 서버에서 실제 존재 여부 확인
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $license_manager = JJ_License_Manager::instance();
            $license_status = $license_manager->verify_license( true ); // 강제 온라인 검증
            
            if ( ! $license_status || ! isset( $license_status['valid'] ) || ! $license_status['valid'] ) {
                // 서버에서 유효하지 않다고 확인되면 가짜
                $this->report_fake_license( $license_key );
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 가짜 라이센스 키 보고
     * 
     * @param string $license_key 라이센스 키
     */
    private function report_fake_license( $license_key ) {
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            $monitor->notify_developer( array(
                array(
                    'type' => 'fake_license_key',
                    'severity' => 'critical',
                    'license_key' => $license_key,
                ),
            ), array() );
        }
    }
}

