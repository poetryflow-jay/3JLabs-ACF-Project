<?php
/**
 * [v25.0.0] 파일 무결성 모니터 고도화
 * 
 * FTP를 통한 코드 탈취 방지 및 파일 변경 감지 시스템 (고도화 버전)
 * 
 * @package ACF_CSS_Style_Guide
 * @version 25.0.0
 * @author 3J Labs
 * 
 * 주요 개선사항:
 * - 실시간 파일 변경 감지
 * - SHA-512 해시 검증
 * - 자동 알림 시스템
 * - 자동 복구 메커니즘
 * - 성능 최적화 (비동기 검사)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_File_Integrity_Monitor_V25 {

    private static $instance = null;
    private $critical_files = array();
    private $option_key = 'jj_file_integrity_hashes_v25';
    private $log_option_key = 'jj_file_integrity_logs_v25';
    private $alert_option_key = 'jj_file_integrity_alerts_v25';
    private $monitoring_enabled = true;
    private $real_time_check_interval = 300; // 5분
    private $last_check_time = 0;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_critical_files();
        $this->init_hooks();
        $this->load_settings();
    }

    /**
     * 중요 파일 목록 초기화 (v25 확장)
     */
    private function init_critical_files() {
        $base_path = JJ_STYLE_GUIDE_PATH;
        
        // [v25.0.0] 모든 핵심 파일 포함
        $this->critical_files = array(
            // 메인 플러그인 파일
            array(
                'path' => $base_path . 'acf-css-really-simple-style-guide.php',
                'type' => 'main',
                'priority' => 'critical',
                'description' => __( '메인 플러그인 파일', 'acf-css-really-simple-style-management-center' ),
                'auto_recover' => false, // 메인 파일은 자동 복구 불가
            ),
            
            // 보안 관련 파일
            array(
                'path' => $base_path . 'includes/class-jj-security-enhancer.php',
                'type' => 'security',
                'priority' => 'critical',
                'description' => __( '보안 강화 모듈', 'acf-css-really-simple-style-management-center' ),
                'auto_recover' => true,
            ),
            array(
                'path' => $base_path . 'includes/class-jj-security-hardener.php',
                'type' => 'security',
                'priority' => 'critical',
                'description' => __( '보안 강화', 'acf-css-really-simple-style-management-center' ),
                'auto_recover' => true,
            ),
            array(
                'path' => $base_path . 'includes/class-jj-file-integrity-monitor.php',
                'type' => 'security',
                'priority' => 'critical',
                'description' => __( '파일 무결성 모니터 (레거시)', 'acf-css-really-simple-style-management-center' ),
                'auto_recover' => true,
            ),
            array(
                'path' => $base_path . 'includes/class-jj-file-integrity-monitor-v25.php',
                'type' => 'security',
                'priority' => 'critical',
                'description' => __( '파일 무결성 모니터 v25', 'acf-css-really-simple-style-management-center' ),
                'auto_recover' => false,
            ),
            
            // 라이센스 관련 파일
            array(
                'path' => $base_path . 'includes/class-jj-license-manager.php',
                'type' => 'license',
                'priority' => 'critical',
                'description' => __( '라이센스 관리자', 'acf-css-really-simple-style-management-center' ),
                'auto_recover' => false,
            ),
            array(
                'path' => $base_path . 'includes/class-jj-license-security-hardening.php',
                'type' => 'license',
                'priority' => 'critical',
                'description' => __( '라이센스 보안 강화', 'acf-css-really-simple-style-management-center' ),
                'auto_recover' => false,
            ),
            array(
                'path' => $base_path . 'includes/class-jj-license-enforcement.php',
                'type' => 'license',
                'priority' => 'critical',
                'description' => __( '라이센스 강제', 'acf-css-really-simple-style-management-center' ),
                'auto_recover' => false,
            ),
            
            // 업데이트 관련 파일
            array(
                'path' => $base_path . 'includes/class-jj-plugin-updater.php',
                'type' => 'update',
                'priority' => 'high',
                'description' => __( '플러그인 업데이터', 'acf-css-really-simple-style-management-center' ),
                'auto_recover' => true,
            ),
            array(
                'path' => $base_path . 'includes/class-jj-update-channel-manager.php',
                'type' => 'update',
                'priority' => 'high',
                'description' => __( '업데이트 채널 관리자', 'acf-css-really-simple-style-management-center' ),
                'auto_recover' => true,
            ),
            
            // 핵심 기능 파일
            array(
                'path' => $base_path . 'includes/class-jj-edition-controller.php',
                'type' => 'core',
                'priority' => 'critical',
                'description' => __( '에디션 컨트롤러', 'acf-css-really-simple-style-management-center' ),
                'auto_recover' => false,
            ),
            array(
                'path' => $base_path . 'includes/class-jj-simple-style-guide.php',
                'type' => 'core',
                'priority' => 'high',
                'description' => __( '메인 스타일 가이드 클래스', 'acf-css-really-simple-style-management-center' ),
                'auto_recover' => true,
            ),
        );
    }

    /**
     * 설정 로드
     */
    private function load_settings() {
        $settings = get_option( 'jj_file_integrity_settings_v25', array(
            'monitoring_enabled' => true,
            'real_time_check' => true,
            'check_interval' => 300, // 5분
            'auto_recover' => true,
            'alert_email' => get_option( 'admin_email' ),
            'alert_on_violation' => true,
            'log_violations' => true,
            'hash_algorithm' => 'sha512', // SHA-512 사용
        ) );

        $this->monitoring_enabled = ! empty( $settings['monitoring_enabled'] );
        $this->real_time_check_interval = absint( $settings['check_interval'] ?? 300 );
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        if ( ! $this->monitoring_enabled ) {
            return;
        }

        // 플러그인 로드 시 무결성 검사
        add_action( 'plugins_loaded', array( $this, 'check_integrity' ), 5 );
        
        // [v25.0.0] 실시간 모니터링 (페이지 로드 시)
        add_action( 'admin_init', array( $this, 'real_time_check' ), 1 );
        
        // 주기적 무결성 검사 (매일)
        if ( ! wp_next_scheduled( 'jj_file_integrity_check_v25' ) ) {
            wp_schedule_event( time(), 'daily', 'jj_file_integrity_check_v25' );
        }
        add_action( 'jj_file_integrity_check_v25', array( $this, 'check_integrity' ) );
        
        // [v25.0.0] 파일 변경 감지 (파일 수정 시간 모니터링)
        add_action( 'admin_init', array( $this, 'monitor_file_changes' ), 2 );
        
        // [v25.0.0] AJAX 핸들러
        add_action( 'wp_ajax_jj_verify_file_integrity', array( $this, 'ajax_verify_integrity' ) );
        add_action( 'wp_ajax_jj_recover_file', array( $this, 'ajax_recover_file' ) );
        add_action( 'wp_ajax_jj_get_integrity_status', array( $this, 'ajax_get_status' ) );
    }

    /**
     * [v25.0.0] 실시간 무결성 검사
     */
    public function real_time_check() {
        $current_time = time();
        
        // 마지막 검사 후 일정 시간이 지났는지 확인
        $last_check = get_transient( 'jj_file_integrity_last_check_v25' );
        if ( $last_check && ( $current_time - $last_check ) < $this->real_time_check_interval ) {
            return; // 아직 검사 시간이 안 됨
        }

        // 비동기로 검사 (성능 최적화)
        $this->async_check_integrity();
        
        // 마지막 검사 시간 업데이트
        set_transient( 'jj_file_integrity_last_check_v25', $current_time, $this->real_time_check_interval );
    }

    /**
     * [v25.0.0] 비동기 무결성 검사
     */
    private function async_check_integrity() {
        // WordPress Cron을 사용하여 백그라운드에서 실행
        if ( ! wp_next_scheduled( 'jj_file_integrity_async_check_v25' ) ) {
            wp_schedule_single_event( time() + 10, 'jj_file_integrity_async_check_v25' );
        }
        add_action( 'jj_file_integrity_async_check_v25', array( $this, 'check_integrity' ) );
    }

    /**
     * 파일 무결성 검사 (SHA-512 사용)
     */
    public function check_integrity() {
        if ( ! $this->monitoring_enabled ) {
            return;
        }

        $stored_hashes = get_option( $this->option_key, array() );
        $current_hashes = array();
        $violations = array();
        $files_to_recover = array();

        foreach ( $this->critical_files as $file_info ) {
            $file_path = $file_info['path'];
            
            if ( ! file_exists( $file_path ) ) {
                // 파일이 존재하지 않음
                $violations[] = array(
                    'file' => $file_path,
                    'type' => 'missing',
                    'priority' => $file_info['priority'] ?? 'medium',
                    'description' => $file_info['description'] ?? '',
                    'auto_recover' => $file_info['auto_recover'] ?? false,
                    'detected_at' => current_time( 'mysql' ),
                );
                continue;
            }

            // [v25.0.0] SHA-512 해시 계산
            $file_hash = hash_file( 'sha512', $file_path );
            $file_size = filesize( $file_path );
            $file_mtime = filemtime( $file_path );
            
            $current_hashes[ $file_path ] = array(
                'hash' => $file_hash,
                'size' => $file_size,
                'mtime' => $file_mtime,
                'type' => $file_info['type'] ?? 'unknown',
                'priority' => $file_info['priority'] ?? 'medium',
            );

            // 저장된 해시와 비교
            if ( isset( $stored_hashes[ $file_path ] ) ) {
                $stored_hash = $stored_hashes[ $file_path ];
                
                // 해시가 다르면 위반
                if ( $stored_hash['hash'] !== $file_hash ) {
                    $violations[] = array(
                        'file' => $file_path,
                        'type' => 'modified',
                        'priority' => $file_info['priority'] ?? 'medium',
                        'description' => $file_info['description'] ?? '',
                        'auto_recover' => $file_info['auto_recover'] ?? false,
                        'stored_hash' => substr( $stored_hash['hash'], 0, 16 ) . '...',
                        'current_hash' => substr( $file_hash, 0, 16 ) . '...',
                        'size_changed' => ( $stored_hash['size'] ?? 0 ) !== $file_size,
                        'detected_at' => current_time( 'mysql' ),
                    );

                    // 자동 복구 가능한 파일인 경우
                    if ( ! empty( $file_info['auto_recover'] ) ) {
                        $files_to_recover[] = $file_path;
                    }
                }
            } else {
                // 처음 검사하는 파일이면 해시 저장
                $stored_hashes[ $file_path ] = array(
                    'hash' => $file_hash,
                    'size' => $file_size,
                    'mtime' => $file_mtime,
                    'first_checked' => current_time( 'mysql' ),
                );
            }
        }

        // 해시 업데이트
        update_option( $this->option_key, $stored_hashes );

        // 위반이 발견된 경우
        if ( ! empty( $violations ) ) {
            $this->handle_violations( $violations, $files_to_recover );
        }

        return array(
            'status' => empty( $violations ) ? 'clean' : 'violations',
            'violations' => $violations,
            'files_checked' => count( $this->critical_files ),
            'files_clean' => count( $this->critical_files ) - count( $violations ),
        );
    }

    /**
     * [v25.0.0] 위반 처리
     */
    private function handle_violations( $violations, $files_to_recover ) {
        // 로그 기록
        $this->log_violations( $violations );

        // 알림 전송
        $this->send_alerts( $violations );

        // 자동 복구 시도
        if ( ! empty( $files_to_recover ) ) {
            foreach ( $files_to_recover as $file_path ) {
                $this->attempt_auto_recovery( $file_path );
            }
        }

        // 관리자에게 알림 표시
        $this->show_admin_notice( $violations );
    }

    /**
     * [v25.0.0] 위반 로그 기록
     */
    private function log_violations( $violations ) {
        $logs = get_option( $this->log_option_key, array() );
        
        foreach ( $violations as $violation ) {
            $logs[] = array(
                'timestamp' => current_time( 'mysql' ),
                'file' => $violation['file'],
                'type' => $violation['type'],
                'priority' => $violation['priority'],
                'description' => $violation['description'],
                'auto_recover' => $violation['auto_recover'] ?? false,
                'user_id' => get_current_user_id(),
                'ip_address' => $this->get_client_ip(),
            );
        }

        // 최대 1000개까지만 유지
        if ( count( $logs ) > 1000 ) {
            $logs = array_slice( $logs, -1000 );
        }

        update_option( $this->log_option_key, $logs );
    }

    /**
     * [v25.0.0] 알림 전송
     */
    private function send_alerts( $violations ) {
        $settings = get_option( 'jj_file_integrity_settings_v25', array() );
        
        if ( empty( $settings['alert_on_violation'] ) ) {
            return;
        }

        $alert_email = $settings['alert_email'] ?? get_option( 'admin_email' );
        if ( empty( $alert_email ) ) {
            return;
        }

        // Critical 우선순위 위반만 즉시 알림
        $critical_violations = array_filter( $violations, function( $v ) {
            return ( $v['priority'] ?? 'medium' ) === 'critical';
        } );

        if ( empty( $critical_violations ) ) {
            return; // Critical 위반이 없으면 알림 안 보냄
        }

        $subject = sprintf(
            __( '[%s] 파일 무결성 위반 감지', 'acf-css-really-simple-style-management-center' ),
            get_bloginfo( 'name' )
        );

        $message = __( '다음 파일에서 무결성 위반이 감지되었습니다:', 'acf-css-really-simple-style-management-center' ) . "\n\n";
        
        foreach ( $critical_violations as $violation ) {
            $message .= sprintf(
                "- %s (%s): %s\n",
                $violation['description'],
                $violation['type'],
                $violation['file']
            );
        }

        $message .= "\n" . __( '즉시 확인하시기 바랍니다.', 'acf-css-really-simple-style-management-center' );

        wp_mail( $alert_email, $subject, $message );
    }

    /**
     * [v25.0.0] 자동 복구 시도
     */
    private function attempt_auto_recovery( $file_path ) {
        // 백업 파일이 있는지 확인
        $backup_path = $file_path . '.backup';
        
        if ( ! file_exists( $backup_path ) ) {
            // 백업이 없으면 복구 불가
            return false;
        }

        // 백업 파일의 해시 확인
        $backup_hash = hash_file( 'sha512', $backup_path );
        $stored_hashes = get_option( $this->option_key, array() );
        
        if ( ! isset( $stored_hashes[ $file_path ] ) ) {
            return false;
        }

        $stored_hash = $stored_hashes[ $file_path ]['hash'];
        
        // 백업 파일이 저장된 해시와 일치하면 복구
        if ( $backup_hash === $stored_hash ) {
            if ( copy( $backup_path, $file_path ) ) {
                $this->log_recovery( $file_path, 'auto' );
                return true;
            }
        }

        return false;
    }

    /**
     * [v25.0.0] 복구 로그 기록
     */
    private function log_recovery( $file_path, $method ) {
        $recovery_logs = get_option( 'jj_file_integrity_recovery_logs_v25', array() );
        
        $recovery_logs[] = array(
            'timestamp' => current_time( 'mysql' ),
            'file' => $file_path,
            'method' => $method,
            'user_id' => get_current_user_id(),
        );

        // 최대 100개까지만 유지
        if ( count( $recovery_logs ) > 100 ) {
            $recovery_logs = array_slice( $recovery_logs, -100 );
        }

        update_option( 'jj_file_integrity_recovery_logs_v25', $recovery_logs );
    }

    /**
     * [v25.0.0] 관리자 알림 표시
     */
    private function show_admin_notice( $violations ) {
        $critical_count = count( array_filter( $violations, function( $v ) {
            return ( $v['priority'] ?? 'medium' ) === 'critical';
        } ) );

        if ( $critical_count > 0 ) {
            add_action( 'admin_notices', function() use ( $critical_count ) {
                ?>
                <div class="notice notice-error jj-integrity-alert">
                    <p>
                        <strong><?php _e( '⚠️ 파일 무결성 위반 감지', 'acf-css-really-simple-style-management-center' ); ?></strong>
                        <?php printf( __( '%d개의 중요 파일에서 변경이 감지되었습니다.', 'acf-css-really-simple-style-management-center' ), $critical_count ); ?>
                        <a href="<?php echo admin_url( 'admin.php?page=jj-style-guide-security' ); ?>" class="button button-primary" style="margin-left: 10px;">
                            <?php _e( '상세 확인', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </p>
                </div>
                <?php
            } );
        }
    }

    /**
     * [v25.0.0] 파일 변경 모니터링 (파일 수정 시간 기반)
     */
    public function monitor_file_changes() {
        $monitored_files = get_transient( 'jj_file_mtime_monitor_v25' );
        
        if ( $monitored_files === false ) {
            // 처음 모니터링 시작
            $monitored_files = array();
            foreach ( $this->critical_files as $file_info ) {
                $file_path = $file_info['path'];
                if ( file_exists( $file_path ) ) {
                    $monitored_files[ $file_path ] = filemtime( $file_path );
                }
            }
            set_transient( 'jj_file_mtime_monitor_v25', $monitored_files, 3600 ); // 1시간
            return;
        }

        // 파일 수정 시간 비교
        foreach ( $this->critical_files as $file_info ) {
            $file_path = $file_info['path'];
            
            if ( ! file_exists( $file_path ) ) {
                continue;
            }

            $current_mtime = filemtime( $file_path );
            $stored_mtime = $monitored_files[ $file_path ] ?? 0;

            if ( $current_mtime !== $stored_mtime && $stored_mtime > 0 ) {
                // 파일이 변경됨 - 즉시 무결성 검사 실행
                $this->check_integrity();
                break;
            }
        }

        // 모니터링 데이터 업데이트
        $updated_files = array();
        foreach ( $this->critical_files as $file_info ) {
            $file_path = $file_info['path'];
            if ( file_exists( $file_path ) ) {
                $updated_files[ $file_path ] = filemtime( $file_path );
            }
        }
        set_transient( 'jj_file_mtime_monitor_v25', $updated_files, 3600 );
    }

    /**
     * [v25.0.0] AJAX: 무결성 검증
     */
    public function ajax_verify_integrity() {
        check_ajax_referer( 'jj_file_integrity_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $result = $this->check_integrity();
        wp_send_json_success( $result );
    }

    /**
     * [v25.0.0] AJAX: 파일 복구
     */
    public function ajax_recover_file() {
        check_ajax_referer( 'jj_file_integrity_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $file_path = isset( $_POST['file_path'] ) ? sanitize_text_field( $_POST['file_path'] ) : '';
        
        if ( empty( $file_path ) ) {
            wp_send_json_error( array( 'message' => __( '파일 경로가 필요합니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $result = $this->attempt_auto_recovery( $file_path );
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => __( '파일이 복구되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( '파일 복구에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
    }

    /**
     * [v25.0.0] AJAX: 상태 조회
     */
    public function ajax_get_status() {
        check_ajax_referer( 'jj_file_integrity_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $stored_hashes = get_option( $this->option_key, array() );
        $logs = get_option( $this->log_option_key, array() );
        $recovery_logs = get_option( 'jj_file_integrity_recovery_logs_v25', array() );

        wp_send_json_success( array(
            'monitoring_enabled' => $this->monitoring_enabled,
            'files_monitored' => count( $this->critical_files ),
            'files_hashed' => count( $stored_hashes ),
            'violation_logs_count' => count( $logs ),
            'recovery_logs_count' => count( $recovery_logs ),
            'last_check' => get_transient( 'jj_file_integrity_last_check_v25' ),
        ) );
    }

    /**
     * 클라이언트 IP 주소 가져오기
     */
    private function get_client_ip() {
        $ip_keys = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
        
        foreach ( $ip_keys as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ip = sanitize_text_field( $_SERVER[ $key ] );
                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }
        
        return 'unknown';
    }

    /**
     * [v25.0.0] 백업 생성
     */
    public function create_backup( $file_path ) {
        if ( ! file_exists( $file_path ) ) {
            return false;
        }

        $backup_path = $file_path . '.backup';
        
        // 기존 백업이 있으면 삭제
        if ( file_exists( $backup_path ) ) {
            unlink( $backup_path );
        }

        return copy( $file_path, $backup_path );
    }

    /**
     * [v25.0.0] 모든 중요 파일 백업 생성
     */
    public function create_all_backups() {
        $backup_count = 0;
        
        foreach ( $this->critical_files as $file_info ) {
            $file_path = $file_info['path'];
            
            if ( file_exists( $file_path ) && $this->create_backup( $file_path ) ) {
                $backup_count++;
            }
        }

        return $backup_count;
    }
}
