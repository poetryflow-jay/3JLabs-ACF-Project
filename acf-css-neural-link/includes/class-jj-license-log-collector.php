<?php
/**
 * 로그 수집 및 분석 클래스
 * 
 * 타 사이트에서 발생한 오류 및 문제를 수집하고 분석합니다.
 * 
 * @package JJ_License_Manager
 * @version 2.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Log_Collector {
    
    private static $instance = null;
    
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
        // WordPress 함수 존재 확인 후 초기화
        if ( function_exists( 'add_action' ) ) {
            $this->init_hooks();
        }
    }
    
    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 로그 수집 API 엔드포인트는 JJ_License_API에서 등록
    }
    
    /**
     * 원격 사이트에서 로그 수집
     * 
     * @param string $license_key 라이센스 키
     * @param string $site_id 사이트 ID
     * @param string $site_url 사이트 URL
     * @param array $log_data 로그 데이터
     * @return array|WP_Error 결과 또는 에러
     */
    public function collect_log( $license_key, $site_id, $site_url, $log_data ) {
        // 입력 검증
        if ( empty( $license_key ) || empty( $site_id ) || empty( $site_url ) || empty( $log_data ) ) {
            return new WP_Error( 'invalid_parameters', __( '필수 파라미터가 누락되었습니다.', 'jj-license-manager' ) );
        }
        
        // 라이센스 검증
        $validator = new JJ_License_Validator();
        $license_result = $validator->verify( $license_key, $site_id, $site_url );
        
        if ( ! $license_result || ! isset( $license_result['valid'] ) || ! $license_result['valid'] ) {
            return new WP_Error( 'invalid_license', __( '유효하지 않은 라이센스입니다.', 'jj-license-manager' ) );
        }
        
        // 로그 데이터 저장
        global $wpdb;
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_logs = $wpdb->prefix . 'jj_license_logs';
        
        // 로그 테이블이 없으면 생성
        $this->create_logs_table();
        
        // 라이센스 ID 가져오기
        $license = $wpdb->get_row( $wpdb->prepare(
            "SELECT id FROM {$table_licenses} WHERE license_key = %s",
            $license_key
        ), ARRAY_A );
        
        if ( ! $license ) {
            return new WP_Error( 'license_not_found', __( '라이센스를 찾을 수 없습니다.', 'jj-license-manager' ) );
        }
        
        // 로그 저장
        $wpdb->insert(
            $table_logs,
            array(
                'license_id' => intval( $license['id'] ),
                'site_id' => sanitize_text_field( $site_id ),
                'site_url' => esc_url_raw( $site_url ),
                'log_type' => isset( $log_data['type'] ) ? sanitize_text_field( $log_data['type'] ) : 'error',
                'log_level' => isset( $log_data['level'] ) ? sanitize_text_field( $log_data['level'] ) : 'error',
                'message' => isset( $log_data['message'] ) ? sanitize_text_field( $log_data['message'] ) : '',
                'context' => isset( $log_data['context'] ) ? json_encode( $log_data['context'] ) : '',
                'stack_trace' => isset( $log_data['stack_trace'] ) ? sanitize_textarea_field( $log_data['stack_trace'] ) : '',
                'plugin_version' => isset( $log_data['plugin_version'] ) ? sanitize_text_field( $log_data['plugin_version'] ) : '',
                'wordpress_version' => isset( $log_data['wordpress_version'] ) ? sanitize_text_field( $log_data['wordpress_version'] ) : '',
                'php_version' => isset( $log_data['php_version'] ) ? sanitize_text_field( $log_data['php_version'] ) : '',
                'created_at' => function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
        );
        
        // 심각한 오류인 경우 알림
        if ( isset( $log_data['level'] ) && in_array( $log_data['level'], array( 'critical', 'fatal' ), true ) ) {
            $this->send_alert( $license_key, $site_url, $log_data );
        }
        
        return array(
            'success' => true,
            'message' => __( '로그가 수집되었습니다.', 'jj-license-manager' ),
        );
    }
    
    /**
     * 로그 분석 및 문제 점검
     * 
     * @param string $license_key 라이센스 키 (선택사항)
     * @param string $site_id 사이트 ID (선택사항)
     * @param array $filters 필터 옵션
     * @return array 분석 결과
     */
    public function analyze_logs( $license_key = '', $site_id = '', $filters = array() ) {
        global $wpdb;
        
        $table_logs = $wpdb->prefix . 'jj_license_logs';
        
        // 테이블 존재 확인
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_logs}'" ) !== $table_logs ) {
            return array(
                'total_logs' => 0,
                'errors' => array(),
                'warnings' => array(),
                'critical_issues' => array(),
            );
        }
        
        $query = "SELECT * FROM {$table_logs} WHERE 1=1";
        
        if ( ! empty( $license_key ) ) {
            $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
            $license = $wpdb->get_row( $wpdb->prepare(
                "SELECT id FROM {$table_licenses} WHERE license_key = %s",
                $license_key
            ), ARRAY_A );
            
            if ( $license ) {
                $query .= $wpdb->prepare( " AND license_id = %d", intval( $license['id'] ) );
            }
        }
        
        if ( ! empty( $site_id ) ) {
            $query .= $wpdb->prepare( " AND site_id = %s", $site_id );
        }
        
        // 필터 적용
        if ( isset( $filters['log_level'] ) && ! empty( $filters['log_level'] ) ) {
            $query .= $wpdb->prepare( " AND log_level = %s", $filters['log_level'] );
        }
        
        if ( isset( $filters['date_from'] ) && ! empty( $filters['date_from'] ) ) {
            $query .= $wpdb->prepare( " AND created_at >= %s", $filters['date_from'] );
        }
        
        if ( isset( $filters['date_to'] ) && ! empty( $filters['date_to'] ) ) {
            $query .= $wpdb->prepare( " AND created_at <= %s", $filters['date_to'] );
        }
        
        $query .= " ORDER BY created_at DESC";
        
        if ( isset( $filters['limit'] ) && $filters['limit'] > 0 ) {
            $query .= $wpdb->prepare( " LIMIT %d", intval( $filters['limit'] ) );
        } else {
            $query .= " LIMIT 1000";
        }
        
        $logs = $wpdb->get_results( $query, ARRAY_A );
        
        // 분석
        $analysis = array(
            'total_logs' => count( $logs ),
            'errors' => array(),
            'warnings' => array(),
            'critical_issues' => array(),
            'common_errors' => array(),
            'site_statistics' => array(),
        );
        
        foreach ( $logs as $log ) {
            // 레벨별 분류
            if ( $log['log_level'] === 'error' || $log['log_level'] === 'critical' || $log['log_level'] === 'fatal' ) {
                $analysis['errors'][] = $log;
                
                if ( $log['log_level'] === 'critical' || $log['log_level'] === 'fatal' ) {
                    $analysis['critical_issues'][] = $log;
                }
            } elseif ( $log['log_level'] === 'warning' ) {
                $analysis['warnings'][] = $log;
            }
            
            // 사이트별 통계
            if ( ! isset( $analysis['site_statistics'][ $log['site_id'] ] ) ) {
                $analysis['site_statistics'][ $log['site_id'] ] = array(
                    'total' => 0,
                    'errors' => 0,
                    'warnings' => 0,
                    'critical' => 0,
                );
            }
            
            $analysis['site_statistics'][ $log['site_id'] ]['total']++;
            if ( in_array( $log['log_level'], array( 'error', 'critical', 'fatal' ), true ) ) {
                $analysis['site_statistics'][ $log['site_id'] ]['errors']++;
            }
            if ( $log['log_level'] === 'warning' ) {
                $analysis['site_statistics'][ $log['site_id'] ]['warnings']++;
            }
            if ( in_array( $log['log_level'], array( 'critical', 'fatal' ), true ) ) {
                $analysis['site_statistics'][ $log['site_id'] ]['critical']++;
            }
        }
        
        // 공통 오류 패턴 분석
        $error_messages = array();
        foreach ( $analysis['errors'] as $error ) {
            $message = $error['message'];
            if ( ! isset( $error_messages[ $message ] ) ) {
                $error_messages[ $message ] = 0;
            }
            $error_messages[ $message ]++;
        }
        
        arsort( $error_messages );
        $analysis['common_errors'] = array_slice( $error_messages, 0, 10, true );
        
        return $analysis;
    }
    
    /**
     * 로그 테이블 생성
     */
    private function create_logs_table() {
        global $wpdb;
        
        $table_logs = $wpdb->prefix . 'jj_license_logs';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$table_logs} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            license_id bigint(20) UNSIGNED NOT NULL,
            site_id varchar(255) NOT NULL,
            site_url varchar(255) NOT NULL,
            log_type varchar(50) NOT NULL,
            log_level varchar(50) NOT NULL,
            message text NOT NULL,
            context longtext,
            stack_trace longtext,
            plugin_version varchar(50),
            wordpress_version varchar(50),
            php_version varchar(50),
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY license_id (license_id),
            KEY site_id (site_id),
            KEY log_level (log_level),
            KEY created_at (created_at)
        ) {$charset_collate};";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    /**
     * 심각한 오류 알림 발송
     */
    private function send_alert( $license_key, $site_url, $log_data ) {
        $admin_email = function_exists( 'get_option' ) ? get_option( 'admin_email' ) : '';
        if ( empty( $admin_email ) ) {
            return;
        }
        
        $subject = sprintf( '[긴급] %s - 치명적 오류 발생', $site_url );
        
        $message = sprintf(
            "라이센스 키: %s\n" .
            "사이트 URL: %s\n" .
            "오류 레벨: %s\n" .
            "오류 메시지: %s\n" .
            "발생 시간: %s\n\n" .
            "상세 정보:\n%s",
            $license_key,
            $site_url,
            isset( $log_data['level'] ) ? $log_data['level'] : 'unknown',
            isset( $log_data['message'] ) ? $log_data['message'] : '',
            function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
            isset( $log_data['stack_trace'] ) ? $log_data['stack_trace'] : ''
        );
        
        if ( function_exists( 'wp_mail' ) ) {
            wp_mail( $admin_email, $subject, $message );
        }
    }
}

