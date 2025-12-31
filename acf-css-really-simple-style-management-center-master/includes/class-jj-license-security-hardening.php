<?php
/**
 * 라이센스 보안 강화 클래스
 * 
 * 파일 무결성 검사 및 라이센스 우회 방지 기능을 제공합니다.
 * 
 * @package JJ_Style_Guide
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Security_Hardening {
    
    private static $instance = null;
    private $file_hashes = array();
    private $license_type_verified = false;
    
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
        $this->init_file_hashes();
        $this->verify_file_integrity();
    }
    
    /**
     * 파일 해시 초기화
     * 
     * 실제 배포 시에는 각 파일의 해시값을 미리 계산하여 저장해야 합니다.
     */
    private function init_file_hashes() {
        // 주요 파일들의 해시값 (실제 배포 시 계산된 값으로 교체)
        $this->file_hashes = array(
            // 플러그인 메인 파일들
            'free' => array(
                'file' => JJ_STYLE_GUIDE_PATH . 'jj-simple-style-guide.php',
                'expected_hash' => '', // 실제 해시값으로 교체 필요
                'critical_lines' => array( 30 ), // 라이센스 타입 정의 라인
            ),
            'premium' => array(
                'file' => JJ_STYLE_GUIDE_PATH . 'jj-simple-style-guide.php',
                'expected_hash' => '', // 실제 해시값으로 교체 필요
                'critical_lines' => array( 28 ), // 라이센스 타입 정의 라인
            ),
        );
    }
    
    /**
     * 파일 무결성 검사
     * 
     * @return bool 파일이 수정되었는지 여부
     */
    public function verify_file_integrity() {
        // Partner/Master(내부/파트너용)에서는 검사 건너뛰기
        // - 해시/라인 기준이 레거시 파일명에 묶여있고, 내부 빌드에서는 차단이 치명적일 수 있음
        $is_partner_or_higher = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        }
        if ( $is_partner_or_higher ) {
            return true;
        }
        
        $edition = defined( 'JJ_STYLE_GUIDE_EDITION' ) ? JJ_STYLE_GUIDE_EDITION : 'free';
        
        if ( ! isset( $this->file_hashes[ $edition ] ) ) {
            return true; // 해시가 설정되지 않은 경우 통과
        }
        
        $file_info = $this->file_hashes[ $edition ];
        $file_path = $file_info['file'];
        
        if ( ! file_exists( $file_path ) ) {
            return true; // 파일이 없으면 통과 (다른 버전일 수 있음)
        }
        
        // 파일 해시 검증
        if ( ! empty( $file_info['expected_hash'] ) ) {
            $actual_hash = hash_file( 'sha256', $file_path );
            if ( $actual_hash !== $file_info['expected_hash'] ) {
                $this->log_security_event( 'file_modified', array(
                    'file' => $file_path,
                    'expected_hash' => $file_info['expected_hash'],
                    'actual_hash' => $actual_hash,
                ) );
                return false;
            }
        }
        
        // 중요 라인 검증 (라이센스 타입 상수가 수정되었는지 확인)
        if ( ! empty( $file_info['critical_lines'] ) ) {
            $file_content = file_get_contents( $file_path );
            $lines = explode( "\n", $file_content );
            
            foreach ( $file_info['critical_lines'] as $line_num ) {
                $line_index = $line_num - 1; // 배열 인덱스는 0부터 시작
                if ( isset( $lines[ $line_index ] ) ) {
                    $line = trim( $lines[ $line_index ] );
                    // 라이센스 타입이 예상과 다른 값으로 변경되었는지 확인
                    if ( preg_match( "/define\s*\(\s*['\"]JJ_STYLE_GUIDE_LICENSE_TYPE['\"]\s*,\s*['\"](FREE|BASIC|PREM|UNLIM)['\"]\s*\)/", $line, $matches ) ) {
                        $found_type = $matches[1];
                        $expected_type = strtoupper( $edition === 'free' ? 'FREE' : ( $edition === 'basic' ? 'BASIC' : ( $edition === 'premium' ? 'PREM' : 'UNLIM' ) ) );
                        
                        if ( $found_type !== $expected_type ) {
                            $this->log_security_event( 'license_type_modified', array(
                                'file' => $file_path,
                                'line' => $line_num,
                                'expected_type' => $expected_type,
                                'found_type' => $found_type,
                            ) );
                            return false;
                        }
                    }
                }
            }
        }
        
        return true;
    }
    
    /**
     * 라이센스 타입 검증
     * 
     * 하드코딩된 상수 대신 외부 서버 검증 결과만 사용하도록 강제
     * 
     * @param string $hardcoded_type 파일에 하드코딩된 라이센스 타입
     * @return string 검증된 라이센스 타입
     */
    public function verify_license_type( $hardcoded_type ) {
        // 이미 검증된 경우 캐시된 결과 사용
        if ( $this->license_type_verified ) {
            return $hardcoded_type;
        }
        
        // 파일 무결성 검사 실패 시 FREE로 강제
        if ( ! $this->verify_file_integrity() ) {
            $this->log_security_event( 'integrity_check_failed', array(
                'hardcoded_type' => $hardcoded_type,
            ) );
            return 'FREE';
        }
        
        // 외부 서버에서 라이센스 검증 (최소 7일마다 강제)
        $license_manager = JJ_License_Manager::instance();
        $license_status = $license_manager->get_license_status();
        
        // 외부 서버 검증 결과가 하드코딩된 타입과 일치하는지 확인
        if ( isset( $license_status['type'] ) ) {
            $server_type = $license_status['type'];
            
            // 타입 불일치 시 하드코딩된 타입 무시하고 서버 결과 사용
            if ( $server_type !== $hardcoded_type && $license_status['valid'] ) {
                $this->log_security_event( 'license_type_mismatch', array(
                    'hardcoded_type' => $hardcoded_type,
                    'server_type' => $server_type,
                ) );
                // 서버 검증 결과를 우선 사용
                $this->license_type_verified = true;
                return $server_type;
            }
        }
        
        // 검증 실패 시 FREE로 강제
        if ( ! isset( $license_status['valid'] ) || ! $license_status['valid'] ) {
            return 'FREE';
        }
        
        $this->license_type_verified = true;
        return $hardcoded_type;
    }
    
    /**
     * 보안 이벤트 로깅
     * 
     * @param string $event_type 이벤트 타입
     * @param array $data 이벤트 데이터
     */
    private function log_security_event( $event_type, $data = array() ) {
        $log_data = array(
            'timestamp' => current_time( 'mysql' ),
            'event_type' => $event_type,
            'data' => $data,
            'ip' => $this->get_client_ip(),
            'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '',
        );
        
        // 옵션에 로그 저장 (최근 100개만 유지)
        $logs = get_option( 'jj_security_logs', array() );
        $logs[] = $log_data;
        
        // 최근 100개만 유지
        if ( count( $logs ) > 100 ) {
            $logs = array_slice( $logs, -100 );
        }
        
        update_option( 'jj_security_logs', $logs );
        
        // 심각한 이벤트는 관리자에게 이메일 발송 (선택사항)
        if ( in_array( $event_type, array( 'file_modified', 'license_type_modified', 'license_type_mismatch' ) ) ) {
            $this->send_security_alert( $event_type, $data );
        }
    }
    
    /**
     * 보안 경고 이메일 발송
     * 
     * @param string $event_type 이벤트 타입
     * @param array $data 이벤트 데이터
     */
    private function send_security_alert( $event_type, $data ) {
        $admin_email = get_option( 'admin_email' );
        $site_url = home_url();
        
        $subject = sprintf( '[보안 경고] %s - 라이센스 보안 이벤트 발생', $site_url );
        $message = sprintf(
            "보안 이벤트가 발생했습니다.\n\n" .
            "이벤트 타입: %s\n" .
            "사이트 URL: %s\n" .
            "발생 시간: %s\n\n" .
            "상세 정보:\n%s",
            $event_type,
            $site_url,
            current_time( 'mysql' ),
            print_r( $data, true )
        );
        
        // 이메일 발송 (선택사항, 기본적으로 비활성화)
        if ( defined( 'JJ_SECURITY_ALERT_EMAIL' ) && JJ_SECURITY_ALERT_EMAIL ) {
            wp_mail( $admin_email, $subject, $message );
        }
    }
    
    /**
     * 클라이언트 IP 주소 가져오기
     * 
     * @return string IP 주소
     */
    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_REAL_IP',        // Nginx proxy
            'HTTP_X_FORWARDED_FOR',  // Proxy
            'REMOTE_ADDR',           // Standard
        );
        
        foreach ( $ip_keys as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ip = sanitize_text_field( $_SERVER[ $key ] );
                // X-Forwarded-For는 여러 IP가 있을 수 있음
                if ( strpos( $ip, ',' ) !== false ) {
                    $ips = explode( ',', $ip );
                    $ip = trim( $ips[0] );
                }
                return $ip;
            }
        }
        
        return '0.0.0.0';
    }
    
    /**
     * 보안 로그 가져오기
     * 
     * @param int $limit 로그 개수 제한
     * @return array 보안 로그
     */
    public function get_security_logs( $limit = 50 ) {
        $logs = get_option( 'jj_security_logs', array() );
        return array_slice( $logs, -$limit );
    }
}

