<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 20] 파일 무결성 모니터
 * 
 * FTP를 통한 코드 탈취 방지 및 파일 변경 감지 시스템
 * 
 * @since 20.0.0
 */
class JJ_File_Integrity_Monitor {

    private static $instance = null;
    private $critical_files = array();
    private $option_key = 'jj_file_integrity_hashes';
    private $log_option_key = 'jj_file_integrity_logs';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_critical_files();
        $this->init_hooks();
    }

    /**
     * 중요 파일 목록 초기화
     */
    private function init_critical_files() {
        $base_path = JJ_STYLE_GUIDE_PATH;
        
        $this->critical_files = array(
            // 메인 플러그인 파일
            array(
                'path' => $base_path . 'acf-css-really-simple-style-guide.php',
                'type' => 'main',
                'description' => __( '메인 플러그인 파일', 'acf-css-really-simple-style-management-center' ),
            ),
            // 라이센스 관련 파일
            array(
                'path' => $base_path . 'includes/class-jj-license-manager.php',
                'type' => 'license',
                'description' => __( '라이센스 관리자', 'acf-css-really-simple-style-management-center' ),
            ),
            array(
                'path' => $base_path . 'includes/class-jj-license-security-hardening.php',
                'type' => 'license',
                'description' => __( '라이센스 보안 강화', 'acf-css-really-simple-style-management-center' ),
            ),
            array(
                'path' => $base_path . 'includes/class-jj-license-enforcement.php',
                'type' => 'license',
                'description' => __( '라이센스 강제', 'acf-css-really-simple-style-management-center' ),
            ),
            // 보안 관련 파일
            array(
                'path' => $base_path . 'includes/class-jj-security-hardener.php',
                'type' => 'security',
                'description' => __( '보안 강화', 'acf-css-really-simple-style-management-center' ),
            ),
            // Edition Controller
            array(
                'path' => $base_path . 'includes/class-jj-edition-controller.php',
                'type' => 'core',
                'description' => __( '에디션 컨트롤러', 'acf-css-really-simple-style-management-center' ),
            ),
        );
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 플러그인 로드 시 무결성 검사
        add_action( 'plugins_loaded', array( $this, 'check_integrity' ), 5 );
        
        // 주기적 무결성 검사 (매일)
        if ( ! wp_next_scheduled( 'jj_file_integrity_check' ) ) {
            wp_schedule_event( time(), 'daily', 'jj_file_integrity_check' );
        }
        add_action( 'jj_file_integrity_check', array( $this, 'check_integrity' ) );
        
        // 파일 변경 감지 (파일 수정 시간 모니터링)
        add_action( 'admin_init', array( $this, 'monitor_file_changes' ) );
    }

    /**
     * 파일 무결성 검사
     */
    public function check_integrity() {
        $stored_hashes = get_option( $this->option_key, array() );
        $current_hashes = array();
        $violations = array();

        foreach ( $this->critical_files as $file_info ) {
            $file_path = $file_info['path'];
            
            if ( ! file_exists( $file_path ) ) {
                continue; // 파일이 없으면 건너뛰기
            }

            // 현재 파일 해시 계산
            $current_hash = hash_file( 'sha256', $file_path );
            $file_key = md5( $file_path );
            
            $current_hashes[ $file_key ] = array(
                'path' => $file_path,
                'hash' => $current_hash,
                'mtime' => filemtime( $file_path ),
                'type' => $file_info['type'],
                'description' => $file_info['description'],
            );

            // 저장된 해시와 비교
            if ( isset( $stored_hashes[ $file_key ] ) ) {
                $stored_hash = $stored_hashes[ $file_key ]['hash'];
                
                if ( $stored_hash !== $current_hash ) {
                    // 파일이 변경됨
                    $violations[] = array(
                        'file' => $file_path,
                        'type' => $file_info['type'],
                        'description' => $file_info['description'],
                        'stored_hash' => $stored_hash,
                        'current_hash' => $current_hash,
                        'timestamp' => current_time( 'mysql' ),
                    );
                    
                    $this->log_violation( $violations[ count( $violations ) - 1 ] );
                }
            } else {
                // 처음 검사하는 파일이면 해시 저장
                $stored_hashes[ $file_key ] = $current_hashes[ $file_key ];
            }
        }

        // 해시 업데이트
        update_option( $this->option_key, $stored_hashes );

        // 위반 사항이 있으면 조치
        if ( ! empty( $violations ) ) {
            $this->handle_violations( $violations );
        }

        return empty( $violations );
    }

    /**
     * 파일 변경 모니터링 (실시간)
     */
    public function monitor_file_changes() {
        // 관리자 페이지에서만 실행 (성능 고려)
        if ( ! is_admin() ) {
            return;
        }

        $stored_hashes = get_option( $this->option_key, array() );
        
        foreach ( $this->critical_files as $file_info ) {
            $file_path = $file_info['path'];
            
            if ( ! file_exists( $file_path ) ) {
                continue;
            }

            $file_key = md5( $file_path );
            $current_mtime = filemtime( $file_path );
            
            if ( isset( $stored_hashes[ $file_key ] ) ) {
                $stored_mtime = $stored_hashes[ $file_key ]['mtime'];
                
                // 수정 시간이 변경되었으면 해시 재검사
                if ( $current_mtime !== $stored_mtime ) {
                    $this->check_integrity();
                    break; // 한 번만 실행
                }
            }
        }
    }

    /**
     * 위반 사항 처리
     */
    private function handle_violations( $violations ) {
        foreach ( $violations as $violation ) {
            // 라이센스 관련 파일 변경 시 즉시 조치
            if ( $violation['type'] === 'license' ) {
                $this->handle_license_file_violation( $violation );
            }
            
            // 관리자에게 알림
            $this->notify_admin( $violation );
        }
    }

    /**
     * 라이센스 파일 위반 처리
     */
    private function handle_license_file_violation( $violation ) {
        // 라이센스 타입을 FREE로 강제
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            // 상수는 런타임에 변경할 수 없으므로, 옵션에 플래그 설정
            update_option( 'jj_license_integrity_violation', true );
            update_option( 'jj_license_forced_free', true );
        }
        
        // 라이센스 검증 비활성화
        add_filter( 'jj_license_is_valid', '__return_false', 999 );
    }

    /**
     * 관리자 알림
     * [v22.4.3] 정상적인 업데이트 시 경고 표시 안 함
     */
    private function notify_admin( $violation ) {
        // [v22.4.3] 정상적인 업데이트인지 확인 (플러그인 버전이 변경되었는지 확인)
        $current_version = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '0.0.0';
        $stored_version = get_option( 'jj_file_integrity_last_version', '0.0.0' );
        
        // 버전이 변경되었으면 정상적인 업데이트로 간주하고 해시 재설정
        if ( version_compare( $current_version, $stored_version, '>' ) ) {
            // 해시 재설정
            $this->reset_file_hashes();
            update_option( 'jj_file_integrity_last_version', $current_version );
            return; // 경고 표시 안 함
        }
        
        // [v22.4.3] 최근 24시간 내에 같은 파일에 대한 경고가 있었는지 확인
        $recent_warning_key = 'jj_file_integrity_warning_' . md5( $violation['file'] );
        $last_warning_time = get_transient( $recent_warning_key );
        
        if ( $last_warning_time && ( time() - $last_warning_time ) < DAY_IN_SECONDS ) {
            // 24시간 내에 이미 경고를 표시했으면 다시 표시하지 않음
            return;
        }
        
        $admin_email = get_option( 'admin_email' );
        $site_url = home_url();
        
        $subject = sprintf( 
            '[보안 경고] %s - 파일 무결성 위반 감지', 
            $site_url 
        );
        
        $message = sprintf(
            "파일 무결성 위반이 감지되었습니다.\n\n" .
            "파일: %s\n" .
            "설명: %s\n" .
            "발생 시간: %s\n" .
            "사이트 URL: %s\n\n" .
            "이 파일이 정상적인 업데이트에 의해 변경된 것이 아니라면, " .
            "즉시 플러그인을 재설치하거나 백업에서 복구하시기 바랍니다.",
            $violation['file'],
            $violation['description'],
            $violation['timestamp'],
            $site_url
        );
        
        // 이메일 발송 (선택적)
        if ( defined( 'JJ_SECURITY_ALERT_EMAIL' ) && JJ_SECURITY_ALERT_EMAIL ) {
            wp_mail( $admin_email, $subject, $message );
        }
        
        // 관리자 알림 표시
        add_action( 'admin_notices', function() use ( $violation ) {
            ?>
            <div class="notice notice-error">
                <p>
                    <strong><?php esc_html_e( '보안 경고:', 'acf-css-really-simple-style-management-center' ); ?></strong>
                    <?php 
                    printf(
                        esc_html__( '중요 파일이 변경되었습니다: %s', 'acf-css-really-simple-style-management-center' ),
                        esc_html( $violation['description'] )
                    ); 
                    ?>
                </p>
            </div>
            <?php
        } );
        
        // 경고 표시 시간 기록
        set_transient( $recent_warning_key, time(), DAY_IN_SECONDS );
    }
    
    /**
     * [v22.4.3] 파일 해시 재설정 (정상적인 업데이트 후 사용)
     */
    public function reset_file_hashes() {
        $stored_hashes = array();
        
        foreach ( $this->critical_files as $file_info ) {
            $file_path = $file_info['path'];
            
            if ( ! file_exists( $file_path ) ) {
                continue;
            }
            
            $current_hash = hash_file( 'sha256', $file_path );
            $file_key = md5( $file_path );
            
            $stored_hashes[ $file_key ] = array(
                'path' => $file_path,
                'hash' => $current_hash,
                'mtime' => filemtime( $file_path ),
                'type' => $file_info['type'],
                'description' => $file_info['description'],
            );
        }
        
        update_option( $this->option_key, $stored_hashes );
    }

    /**
     * 위반 사항 로깅
     */
    private function log_violation( $violation ) {
        $logs = get_option( $this->log_option_key, array() );
        
        $log_entry = array(
            'timestamp' => current_time( 'mysql' ),
            'file' => $violation['file'],
            'type' => $violation['type'],
            'description' => $violation['description'],
            'stored_hash' => $violation['stored_hash'],
            'current_hash' => $violation['current_hash'],
            'ip' => $this->get_client_ip(),
            'user_id' => get_current_user_id(),
        );
        
        $logs[] = $log_entry;
        
        // 최근 100개만 유지
        if ( count( $logs ) > 100 ) {
            $logs = array_slice( $logs, -100 );
        }
        
        update_option( $this->log_option_key, $logs );
    }

    /**
     * 클라이언트 IP 주소 가져오기
     */
    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR',
        );
        
        foreach ( $ip_keys as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ip = sanitize_text_field( $_SERVER[ $key ] );
                if ( strpos( $ip, ',' ) !== false ) {
                    $ips = explode( ',', $ip );
                    $ip = trim( $ips[0] );
                }
                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }

    /**
     * 무결성 로그 가져오기
     */
    public function get_integrity_logs( $limit = 50 ) {
        $logs = get_option( $this->log_option_key, array() );
        return array_slice( $logs, -$limit );
    }

    /**
     * 초기 해시 생성 (빌드 시 실행)
     * 
     * 이 메서드는 빌드 스크립트에서 호출하여 초기 해시를 생성합니다.
     */
    public function generate_initial_hashes() {
        $hashes = array();
        
        foreach ( $this->critical_files as $file_info ) {
            $file_path = $file_info['path'];
            
            if ( ! file_exists( $file_path ) ) {
                continue;
            }
            
            $file_key = md5( $file_path );
            $hashes[ $file_key ] = array(
                'path' => $file_path,
                'hash' => hash_file( 'sha256', $file_path ),
                'mtime' => filemtime( $file_path ),
                'type' => $file_info['type'],
                'description' => $file_info['description'],
            );
        }
        
        return $hashes;
    }
}

// 초기화
if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    JJ_File_Integrity_Monitor::instance();
}
