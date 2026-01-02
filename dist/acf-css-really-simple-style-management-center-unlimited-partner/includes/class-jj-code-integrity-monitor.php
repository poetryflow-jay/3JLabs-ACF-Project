<?php
/**
 * 코드 무결성 모니터링 클래스
 * 
 * 실시간 파일 수정 감지, 코드 변경 추적, 개발자 알림 기능을 제공합니다.
 * 
 * @package JJ_Style_Guide
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Code_Integrity_Monitor {
    
    private static $instance = null;
    private $monitored_files = array();
    private $critical_code_patterns = array();
    private $last_check_time = null;
    private $check_interval = 3600; // 1시간마다 체크
    private $developer_notification_url = '';
    private $is_locked = false;
    
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
        $this->init_monitored_files();
        $this->init_critical_patterns();
        
        // 개발자 서버 URL 가져오기 (기본값: https://j-j-labs.com/)
        $default_server_url = 'https://j-j-labs.com/';
        $developer_server_url = get_option( 'jj_license_manager_server_url', $default_server_url );
        
        // URL이 비어있거나 기본값이면 기본값 사용
        if ( empty( $developer_server_url ) ) {
            $developer_server_url = $default_server_url;
        }
        
        // URL 끝에 슬래시가 없으면 추가
        $developer_server_url = untrailingslashit( $developer_server_url ) . '/';
        
        $this->developer_notification_url = $developer_server_url;
        
        // 플러그인 로드 시 즉시 검사
        add_action( 'plugins_loaded', array( $this, 'check_integrity' ), 999 );
        
        // 정기적인 검사 (매 시간)
        add_action( 'init', array( $this, 'schedule_integrity_check' ) );
        
        // 관리자 페이지에서도 검사
        if ( is_admin() ) {
            add_action( 'admin_init', array( $this, 'check_integrity' ), 1 );
        }
        
        // 기능 잠금 상태 확인
        $this->check_lock_status();
    }
    
    /**
     * 모니터링할 파일 목록 초기화
     */
    private function init_monitored_files() {
        $plugin_path = defined( 'JJ_STYLE_GUIDE_PATH' ) ? JJ_STYLE_GUIDE_PATH : '';
        
        if ( empty( $plugin_path ) ) {
            return;
        }
        
        $edition = defined( 'JJ_STYLE_GUIDE_EDITION' ) ? JJ_STYLE_GUIDE_EDITION : 'free';
        
        // 모니터링할 주요 파일들
        $this->monitored_files = array(
            // 메인 플러그인 파일
            array(
                'path' => $plugin_path . 'acf-css-really-simple-style-guide.php',
                'type' => 'main',
                'critical' => true,
                'patterns' => array(
                    'JJ_STYLE_GUIDE_LICENSE_TYPE',
                    'JJ_STYLE_GUIDE_EDITION',
                    'TYPE_FREE',
                    'TYPE_BASIC',
                    'TYPE_PREMIUM',
                    'TYPE_UNLIMITED',
                ),
            ),
            // 라이센스 관리자 파일
            // [v5.3.6] 상대 경로 의존성 제거: master 버전은 자체 includes 폴더만 사용
            // master 버전은 자체 includes 폴더에 class-jj-license-manager.php 파일이 있으므로
            // 상대 경로 참조 없이 자체 파일만 사용합니다.
            array(
                'path' => $plugin_path . 'includes/class-jj-license-manager.php',
                'type' => 'license',
                'critical' => false, // 선택적 파일이므로 critical을 false로 변경
                'patterns' => array(
                    'verify_license',
                    'parse_license_type',
                    'TYPE_FREE',
                    'TYPE_BASIC',
                    'TYPE_PREMIUM',
                    'TYPE_UNLIMITED',
                ),
            ),
            // 버전 기능 제한 파일
            // [v5.3.6] 상대 경로 의존성 제거: master 버전은 자체 includes 폴더만 사용
            // master 버전은 자체 includes 폴더에 class-jj-version-features.php 파일이 있으므로
            // 상대 경로 참조 없이 자체 파일만 사용합니다.
            array(
                'path' => $plugin_path . 'includes/class-jj-version-features.php',
                'type' => 'features',
                'critical' => false, // 선택적 파일이므로 critical을 false로 변경
                'patterns' => array(
                    'can_use_feature',
                    'can_use_palette',
                    'get_current_edition',
                ),
            ),
        );
    }
    
    /**
     * 중요 코드 패턴 초기화
     */
    private function init_critical_patterns() {
        $this->critical_code_patterns = array(
            // 라이센스 타입 변경 시도
            array(
                'pattern' => '/define\s*\(\s*[\'"]JJ_STYLE_GUIDE_LICENSE_TYPE[\'"]\s*,\s*[\'"](FREE|BASIC|PREM|UNLIM)[\'"]\s*\)/',
                'type' => 'license_type_modification',
                'severity' => 'critical',
            ),
            // 라이센스 검증 우회 시도
            array(
                'pattern' => '/if\s*\(\s*!?\s*\$.*->verify_license|if\s*\(\s*!?\s*\$.*->get_license_status/',
                'type' => 'verification_bypass',
                'severity' => 'critical',
            ),
            // 기능 제한 우회 시도
            array(
                'pattern' => '/can_use_feature\s*\(\s*\)\s*\{?\s*return\s+true/',
                'type' => 'feature_bypass',
                'severity' => 'high',
            ),
            // 라이센스 타입 강제 변경
            array(
                'pattern' => '/JJ_STYLE_GUIDE_LICENSE_TYPE\s*=\s*[\'"](FREE|BASIC|PREM|UNLIM)[\'"]/',
                'type' => 'license_type_override',
                'severity' => 'critical',
            ),
        );
    }
    
    /**
     * 무결성 검사 스케줄링
     */
    public function schedule_integrity_check() {
        $last_check = get_option( 'jj_integrity_last_check', 0 );
        
        if ( time() - $last_check < $this->check_interval ) {
            return;
        }
        
        $this->check_integrity();
        update_option( 'jj_integrity_last_check', time() );
    }
    
    /**
     * 파일 무결성 검사
     * 
     * @return bool 무결성 검사 통과 여부
     */
    public function check_integrity() {
        // [Safety Lock] MASTER 버전은 무결성 검사를 수행하지 않음 (본인 인증)
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) === 'MASTER' ) {
            if ( $this->is_locked() ) {
                delete_option( 'jj_plugin_locked' );
                $this->is_locked = false;
            }
            return true;
        }

        // 잠금 상태 확인
        if ( $this->is_locked ) {
            return false;
        }
        
        $violations = array();
        $file_changes = array();
        
        foreach ( $this->monitored_files as $file_info ) {
            if ( ! file_exists( $file_info['path'] ) ) {
                continue;
            }
            
            $file_content = file_get_contents( $file_info['path'] );
            $file_hash = hash( 'sha256', $file_content );
            $file_mtime = filemtime( $file_info['path'] );
            
            // 저장된 해시와 비교
            $stored_hash = get_option( 'jj_file_hash_' . md5( $file_info['path'] ), '' );
            $stored_mtime = get_option( 'jj_file_mtime_' . md5( $file_info['path'] ), 0 );
            
            // 파일이 수정되었는지 확인
            if ( ! empty( $stored_hash ) && $stored_hash !== $file_hash ) {
                $file_changes[] = array(
                    'file' => $file_info['path'],
                    'type' => $file_info['type'],
                    'critical' => $file_info['critical'],
                    'old_hash' => $stored_hash,
                    'new_hash' => $file_hash,
                    'old_mtime' => $stored_mtime,
                    'new_mtime' => $file_mtime,
                );
            }
            
            // 중요 코드 패턴 검사
            foreach ( $this->critical_code_patterns as $pattern_info ) {
                if ( preg_match( $pattern_info['pattern'], $file_content, $matches ) ) {
                    // 패턴이 발견되었지만, 정상적인 코드일 수도 있으므로 추가 검증 필요
                    // 의심스러운 수정 패턴만 감지
                    if ( $this->is_suspicious_modification( $file_content, $pattern_info, $file_info ) ) {
                        $violations[] = array(
                            'file' => $file_info['path'],
                            'type' => $pattern_info['type'],
                            'severity' => $pattern_info['severity'],
                            'pattern' => $pattern_info['pattern'],
                            'match' => isset( $matches[0] ) ? $matches[0] : '',
                        );
                    }
                }
            }
            
            // 중요 라인 검사 (라이센스 타입 정의 라인)
            if ( $file_info['type'] === 'main' && ! empty( $file_info['patterns'] ) ) {
                $lines = explode( "\n", $file_content );
                foreach ( $lines as $line_num => $line ) {
                    // 라이센스 타입 정의 라인 찾기
                    if ( preg_match( '/define\s*\(\s*[\'"]JJ_STYLE_GUIDE_LICENSE_TYPE[\'"]/', $line ) ) {
                        $expected_type = $this->get_expected_license_type();
                        if ( preg_match( '/[\'"](FREE|BASIC|PREM|UNLIM)[\'"]/', $line, $type_match ) ) {
                            $found_type = $type_match[1];
                            if ( $found_type !== $expected_type ) {
                                $violations[] = array(
                                    'file' => $file_info['path'],
                                    'type' => 'license_type_mismatch',
                                    'severity' => 'critical',
                                    'line' => $line_num + 1,
                                    'expected' => $expected_type,
                                    'found' => $found_type,
                                    'code' => trim( $line ),
                                );
                            }
                        }
                    }
                }
            }
            
            // 해시 업데이트
            update_option( 'jj_file_hash_' . md5( $file_info['path'] ), $file_hash );
            update_option( 'jj_file_mtime_' . md5( $file_info['path'] ), $file_mtime );
        }
        
        // 위반 사항이 발견되면 처리
        if ( ! empty( $violations ) || ! empty( $file_changes ) ) {
            $this->handle_violations( $violations, $file_changes );
            return false;
        }
        
        return true;
    }
    
    /**
     * 의심스러운 수정인지 확인
     * 
     * @param string $file_content 파일 내용
     * @param array $pattern_info 패턴 정보
     * @param array $file_info 파일 정보
     * @return bool 의심스러운 수정 여부
     */
    private function is_suspicious_modification( $file_content, $pattern_info, $file_info ) {
        // 라이센스 타입 수정 시도 감지
        if ( $pattern_info['type'] === 'license_type_modification' ) {
            // 예상되는 라이센스 타입과 다른 값으로 변경되었는지 확인
            $expected_type = $this->get_expected_license_type();
            if ( preg_match( '/[\'"](FREE|BASIC|PREM|UNLIM)[\'"]/', $file_content, $matches ) ) {
                $found_type = $matches[1];
                // 서버 검증 결과와 비교
                $verified_type = $this->get_verified_license_type();
                if ( $found_type !== $expected_type || ( $verified_type && $found_type !== $verified_type ) ) {
                    return true;
                }
            }
        }
        
        // 검증 우회 시도 감지
        if ( $pattern_info['type'] === 'verification_bypass' ) {
            // 항상 true를 반환하는 코드 패턴 확인
            if ( preg_match( '/return\s+true\s*;.*\/\/.*bypass|return\s+true\s*;.*\/\/.*skip/i', $file_content ) ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 예상되는 라이센스 타입 가져오기
     * 
     * @return string 예상 라이센스 타입
     */
    private function get_expected_license_type() {
        $edition = defined( 'JJ_STYLE_GUIDE_EDITION' ) ? JJ_STYLE_GUIDE_EDITION : 'free';
        $type_map = array(
            'free' => 'FREE',
            'basic' => 'BASIC',
            'premium' => 'PREM',
            'unlimited' => 'UNLIM',
        );
        return isset( $type_map[ $edition ] ) ? $type_map[ $edition ] : 'FREE';
    }
    
    /**
     * 서버에서 검증된 라이센스 타입 가져오기
     * 
     * @return string|null 검증된 라이센스 타입
     */
    private function get_verified_license_type() {
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $license_manager = JJ_License_Manager::instance();
            $license_status = $license_manager->get_license_status();
            if ( isset( $license_status['type'] ) && $license_status['valid'] ) {
                return $license_status['type'];
            }
        }
        return null;
    }
    
    /**
     * 위반 사항 처리
     * 
     * @param array $violations 위반 사항 목록
     * @param array $file_changes 파일 변경 사항 목록
     */
    private function handle_violations( $violations, $file_changes ) {
        // 위반 사항 로깅
        $this->log_violations( $violations, $file_changes );
        
        // 개발자에게 알림 전송
        $this->notify_developer( $violations, $file_changes );
        
        // 기능 잠금
        $this->lock_plugin();
        
        // 경고 메시지 표시
        $this->show_warning_message();
    }
    
    /**
     * 위반 사항 로깅
     * 
     * @param array $violations 위반 사항 목록
     * @param array $file_changes 파일 변경 사항 목록
     */
    private function log_violations( $violations, $file_changes ) {
        // [v5.1.7] WordPress 함수 안전 호출
        $timestamp = function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' );
        
        // [v5.1.7] WordPress 함수 안전 호출
        $site_url = function_exists( 'home_url' ) ? home_url() : '';
        $license_key = function_exists( 'get_option' ) ? get_option( 'jj_style_guide_license_key', '' ) : '';
        
        $log_entry = array(
            'timestamp' => $timestamp,
            'violations' => $violations,
            'file_changes' => $file_changes,
            'site_url' => $site_url,
            'site_id' => $this->get_site_id(),
            'ip' => $this->get_client_ip(),
            'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'license_key' => $license_key,
            'plugin_version' => defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '1.0.0',
            'edition' => defined( 'JJ_STYLE_GUIDE_EDITION' ) ? JJ_STYLE_GUIDE_EDITION : 'unknown',
        );
        
        // 로그 저장
        $logs = get_option( 'jj_security_violations', array() );
        $logs[] = $log_entry;
        
        // 최근 100개만 유지
        if ( count( $logs ) > 100 ) {
            $logs = array_slice( $logs, -100 );
        }
        
        update_option( 'jj_security_violations', $logs );
    }
    
    /**
     * 개발자에게 알림 전송
     * 
     * @param array $violations 위반 사항 목록
     * @param array $file_changes 파일 변경 사항 목록
     */
    private function notify_developer( $violations, $file_changes ) {
        if ( empty( $this->developer_notification_url ) ) {
            return;
        }
        
        $site_id = $this->get_site_id();
        $license_key = get_option( 'jj_style_guide_license_key', '' );
        
        // 라이센스 정보 가져오기
        $license_info = $this->get_license_info();
        
        $notification_data = array(
            'action' => 'security_violation',
            'timestamp' => time(),
            'site_id' => $site_id,
            'site_url' => home_url(),
            'license_key' => $license_key,
            'license_info' => $license_info,
            'violations' => $violations,
            'file_changes' => $file_changes,
            'plugin_version' => defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '1.0.0',
            'edition' => defined( 'JJ_STYLE_GUIDE_EDITION' ) ? JJ_STYLE_GUIDE_EDITION : 'unknown',
            'ip' => $this->get_client_ip(),
            'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '',
        );
        
        // 개발자 서버로 전송
        $notification_url = trailingslashit( $this->developer_notification_url ) . 'wp-json/jj-license/v1/security-alert';
        
        wp_remote_post( esc_url_raw( $notification_url ), array(
            'timeout' => 10,
            'sslverify' => true,
            'body' => json_encode( $notification_data ),
            'headers' => array(
                'Content-Type' => 'application/json',
                'User-Agent' => 'JJ-Style-Guide-Security/' . ( defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '1.0.0' ),
            ),
        ) );
    }
    
    /**
     * 라이센스 정보 가져오기
     * 
     * @return array 라이센스 정보
     */
    private function get_license_info() {
        $info = array(
            'license_key' => get_option( 'jj_style_guide_license_key', '' ),
            'license_status' => get_option( 'jj_style_guide_license_status', array() ),
            'license_data' => get_option( 'jj_style_guide_license_data', array() ),
        );
        
        // WooCommerce 주문 정보 (있는 경우)
        if ( class_exists( 'WooCommerce' ) && ! empty( $info['license_key'] ) ) {
            global $wpdb;
            $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
            $license = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$table_licenses} WHERE license_key = %s",
                $info['license_key']
            ), ARRAY_A );
            
            if ( $license ) {
                $info['purchase_info'] = array(
                    'user_id' => $license['user_id'],
                    'product_id' => $license['product_id'],
                    'order_id' => $license['order_id'],
                    'purchase_date' => $license['purchase_date'],
                    'license_type' => $license['license_type'],
                    'status' => $license['status'],
                );
            }
        }
        
        return $info;
    }
    
    /**
     * 플러그인 기능 잠금
     */
    private function lock_plugin() {
        $this->is_locked = true;
        update_option( 'jj_plugin_locked', true );
        update_option( 'jj_plugin_locked_at', time() );
        update_option( 'jj_plugin_locked_reason', 'code_integrity_violation' );
    }
    
    /**
     * 잠금 상태 확인
     */
    private function check_lock_status() {
        $locked = get_option( 'jj_plugin_locked', false );
        if ( $locked ) {
            $this->is_locked = true;
            $this->show_warning_message();
        }
    }
    
    /**
     * 경고 메시지 표시
     */
    private function show_warning_message() {
        if ( ! is_admin() ) {
            return;
        }
        
        add_action( 'admin_notices', function() {
            $lock_reason = get_option( 'jj_plugin_locked_reason', 'security_violation' );
            $lock_time = get_option( 'jj_plugin_locked_at', 0 );
            
            ?>
            <div class="notice notice-error jj-security-warning" style="border-left-color: #dc3232; padding: 15px;">
                <h2 style="margin-top: 0; color: #dc3232;">
                    <?php esc_html_e( '⚠️ 보안 경고: 플러그인 코드 무결성 위반 감지', 'acf-css-really-simple-style-management-center' ); ?>
                </h2>
                <p style="font-size: 14px; line-height: 1.6;">
                    <strong><?php esc_html_e( '플러그인 파일이 수정되었거나 무단 변경이 감지되었습니다.', 'acf-css-really-simple-style-management-center' ); ?></strong>
                </p>
                <p>
                    <?php esc_html_e( '보안상의 이유로 플러그인 기능이 일시적으로 잠금되었습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    <?php esc_html_e( '플러그인 개발자에게 문의하시거나, 원본 파일로 복원해주세요.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <?php if ( $lock_time ) : ?>
                    <p style="color: #666; font-size: 12px;">
                        <?php printf( esc_html__( '잠금 시간: %s', 'acf-css-really-simple-style-management-center' ), date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $lock_time ) ); ?>
                    </p>
                <?php endif; ?>
                <p>
                    <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button button-primary">
                        <?php esc_html_e( '플러그인 목록으로 이동', 'acf-css-really-simple-style-management-center' ); ?>
                    </a>
                </p>
            </div>
            <?php
        } );
        
        // 프론트엔드에도 경고 표시 (선택사항)
        if ( ! is_admin() && get_option( 'jj_plugin_locked', false ) ) {
            add_action( 'wp_footer', function() {
                ?>
                <div style="position: fixed; top: 0; left: 0; right: 0; background: #dc3232; color: white; padding: 10px; text-align: center; z-index: 999999; font-size: 14px;">
                    <?php esc_html_e( '⚠️ 플러그인 보안 경고: 코드 무결성 위반이 감지되었습니다. 관리자에게 문의하세요.', 'acf-css-really-simple-style-management-center' ); ?>
                </div>
                <?php
            } );
        }
    }
    
    /**
     * 사이트 ID 가져오기
     * 
     * @return string 사이트 ID
     */
    private function get_site_id() {
        $site_id = get_option( 'jj_license_site_id' );
        if ( empty( $site_id ) ) {
            $site_url = home_url();
            $site_id = md5( $site_url . ( defined( 'ABSPATH' ) ? ABSPATH : '' ) );
            update_option( 'jj_license_site_id', $site_id );
        }
        return $site_id;
    }
    
    /**
     * 클라이언트 IP 주소 가져오기
     * 
     * @return string IP 주소
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
                return $ip;
            }
        }
        
        return '0.0.0.0';
    }
    
    /**
     * 잠금 해제 (개발자용)
     * 
     * @param string $unlock_code 잠금 해제 코드
     * @return bool 잠금 해제 성공 여부
     */
    public function unlock_plugin( $unlock_code ) {
        // 잠금 해제 코드 검증 (서버에서 검증)
        $verified = $this->verify_unlock_code( $unlock_code );
        
        if ( $verified ) {
            $this->is_locked = false;
            delete_option( 'jj_plugin_locked' );
            delete_option( 'jj_plugin_locked_at' );
            delete_option( 'jj_plugin_locked_reason' );
            return true;
        }
        
        return false;
    }
    
    /**
     * 잠금 해제 코드 검증
     * 
     * @param string $unlock_code 잠금 해제 코드
     * @return bool 검증 성공 여부
     */
    private function verify_unlock_code( $unlock_code ) {
        if ( empty( $this->developer_notification_url ) ) {
            return false;
        }
        
        $site_id = $this->get_site_id();
        $license_key = get_option( 'jj_style_guide_license_key', '' );
        
        $verify_url = trailingslashit( $this->developer_notification_url ) . 'wp-json/jj-license/v1/verify-unlock';
        
        $response = wp_remote_post( esc_url_raw( $verify_url ), array(
            'timeout' => 10,
            'sslverify' => true,
            'body' => json_encode( array(
                'unlock_code' => $unlock_code,
                'site_id' => $site_id,
                'license_key' => $license_key,
            ) ),
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
        ) );
        
        if ( is_wp_error( $response ) ) {
            return false;
        }
        
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        return isset( $body['verified'] ) && $body['verified'] === true;
    }
    
    /**
     * 잠금 상태 확인
     * 
     * @return bool 잠금 여부
     */
    public function is_locked() {
        return $this->is_locked || get_option( 'jj_plugin_locked', false );
    }
}

