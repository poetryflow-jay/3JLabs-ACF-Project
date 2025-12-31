<?php
/**
 * 원격 플러그인 제어 클래스
 * 
 * 라이센스 서버에서 타 사이트에 설치된 플러그인을 원격으로 제어합니다.
 * - 강제 활성화/비활성화
 * - 플러그인 상태 모니터링
 * - 원격 명령 실행
 * 
 * @package JJ_License_Manager
 * @version 2.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Remote_Control {
    
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
        // 원격 제어 API 엔드포인트는 JJ_License_API에서 등록
    }
    
    /**
     * 원격 플러그인 강제 활성화
     * 
     * @param string $license_key 라이센스 키
     * @param string $site_id 사이트 ID
     * @param string $site_url 사이트 URL
     * @return array|WP_Error 결과 또는 에러
     */
    public function force_activate_plugin( $license_key, $site_id, $site_url ) {
        // 라이센스 검증
        $validator = new JJ_License_Validator();
        $license_result = $validator->verify( $license_key, $site_id, $site_url );
        
        if ( ! $license_result || ! isset( $license_result['valid'] ) || ! $license_result['valid'] ) {
            return new WP_Error( 'invalid_license', __( '유효하지 않은 라이센스입니다.', 'jj-license-manager' ) );
        }
        
        // 원격 사이트에 활성화 명령 전송
        $result = $this->send_remote_command( $site_url, array(
            'action' => 'force_activate',
            'license_key' => $license_key,
            'site_id' => $site_id,
            'timestamp' => time(),
        ) );
        
        if ( is_wp_error( $result ) ) {
            return $result;
        }
        
        // 활성화 기록
        global $wpdb;
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_activations = JJ_License_Database::get_table_name( 'activations' );
        
        $license = $wpdb->get_row( $wpdb->prepare(
            "SELECT id FROM {$table_licenses} WHERE license_key = %s",
            $license_key
        ), ARRAY_A );
        
        if ( $license ) {
            // 활성화 상태 업데이트
            $wpdb->update(
                $table_activations,
                array(
                    'is_active' => 1,
                    'activated_at' => function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
                    'last_check' => function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
                ),
                array(
                    'license_id' => intval( $license['id'] ),
                    'site_id' => $site_id,
                ),
                array( '%d', '%s', '%s' ),
                array( '%d', '%s' )
            );
            
            // 히스토리 기록
            $this->add_history( intval( $license['id'] ), 'force_activated', sprintf(
                __( '원격 강제 활성화: %s', 'jj-license-manager' ),
                $site_url
            ) );
        }
        
        return array(
            'success' => true,
            'message' => __( '플러그인이 강제 활성화되었습니다.', 'jj-license-manager' ),
        );
    }
    
    /**
     * 원격 플러그인 강제 비활성화
     * 
     * @param string $license_key 라이센스 키
     * @param string $site_id 사이트 ID
     * @param string $site_url 사이트 URL
     * @return array|WP_Error 결과 또는 에러
     */
    public function force_deactivate_plugin( $license_key, $site_id, $site_url ) {
        // 라이센스 검증
        $validator = new JJ_License_Validator();
        $license_result = $validator->verify( $license_key, $site_id, $site_url );
        
        if ( ! $license_result || ! isset( $license_result['valid'] ) || ! $license_result['valid'] ) {
            return new WP_Error( 'invalid_license', __( '유효하지 않은 라이센스입니다.', 'jj-license-manager' ) );
        }
        
        // 원격 사이트에 비활성화 명령 전송
        $result = $this->send_remote_command( $site_url, array(
            'action' => 'force_deactivate',
            'license_key' => $license_key,
            'site_id' => $site_id,
            'timestamp' => time(),
        ) );
        
        if ( is_wp_error( $result ) ) {
            return $result;
        }
        
        // 비활성화 기록
        global $wpdb;
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_activations = JJ_License_Database::get_table_name( 'activations' );
        
        $license = $wpdb->get_row( $wpdb->prepare(
            "SELECT id FROM {$table_licenses} WHERE license_key = %s",
            $license_key
        ), ARRAY_A );
        
        if ( $license ) {
            // 비활성화 상태 업데이트
            $wpdb->update(
                $table_activations,
                array(
                    'is_active' => 0,
                    'deactivated_at' => function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
                    'last_check' => function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
                ),
                array(
                    'license_id' => intval( $license['id'] ),
                    'site_id' => $site_id,
                ),
                array( '%d', '%s', '%s' ),
                array( '%d', '%s' )
            );
            
            // 히스토리 기록
            $this->add_history( intval( $license['id'] ), 'force_deactivated', sprintf(
                __( '원격 강제 비활성화: %s', 'jj-license-manager' ),
                $site_url
            ) );
        }
        
        return array(
            'success' => true,
            'message' => __( '플러그인이 강제 비활성화되었습니다.', 'jj-license-manager' ),
        );
    }
    
    /**
     * 원격 사이트에 명령 전송
     * 
     * @param string $site_url 사이트 URL
     * @param array $command 명령 데이터
     * @return array|WP_Error 결과 또는 에러
     */
    private function send_remote_command( $site_url, $command ) {
        // 보안 서명 생성
        $secret_key = function_exists( 'get_option' ) ? get_option( 'jj_license_manager_secret_key', '' ) : '';
        if ( empty( $secret_key ) ) {
            return new WP_Error( 'missing_secret_key', __( '시크릿 키가 설정되지 않았습니다.', 'jj-license-manager' ) );
        }
        
        // 서명 생성
        ksort( $command );
        $data_string = http_build_query( $command );
        $signature = hash_hmac( 'sha256', $data_string, $secret_key );
        $command['signature'] = $signature;
        
        // REST API 엔드포인트 URL
        $api_url = trailingslashit( $site_url ) . 'wp-json/jj-license/v1/remote-command';
        
        // 요청 전송
        $response = wp_remote_post( esc_url_raw( $api_url ), array(
            'timeout' => 15,
            'sslverify' => true,
            'body' => json_encode( $command ),
            'headers' => array(
                'Content-Type' => 'application/json',
                'X-API-Key' => function_exists( 'get_option' ) ? get_option( 'jj_license_api_key', '' ) : '',
            ),
        ) );
        
        if ( is_wp_error( $response ) ) {
            return $response;
        }
        
        $response_code = wp_remote_retrieve_response_code( $response );
        if ( $response_code !== 200 ) {
            return new WP_Error( 'remote_error', sprintf(
                __( '원격 사이트 응답 오류: HTTP %d', 'jj-license-manager' ),
                $response_code
            ) );
        }
        
        $body = wp_remote_retrieve_body( $response );
        $result = json_decode( $body, true );
        
        if ( ! $result || ! isset( $result['success'] ) ) {
            return new WP_Error( 'invalid_response', __( '유효하지 않은 응답입니다.', 'jj-license-manager' ) );
        }
        
        return $result;
    }
    
    /**
     * 모든 활성화된 사이트 상태 조회
     * 
     * @param string $license_key 라이센스 키 (선택사항)
     * @return array 활성화 현황
     */
    public function get_all_activations_status( $license_key = '' ) {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_activations = JJ_License_Database::get_table_name( 'activations' );
        
        $query = "SELECT 
            l.id as license_id,
            l.license_key,
            l.license_type,
            l.expires_at,
            a.id as activation_id,
            a.site_id,
            a.site_url,
            a.is_active,
            a.activated_at,
            a.deactivated_at,
            a.last_check,
            a.plugin_version,
            a.wordpress_version,
            a.php_version
        FROM {$table_licenses} l
        LEFT JOIN {$table_activations} a ON l.id = a.license_id
        WHERE a.is_active = 1";
        
        if ( ! empty( $license_key ) ) {
            $query .= $wpdb->prepare( " AND l.license_key = %s", $license_key );
        }
        
        $results = $wpdb->get_results( $query, ARRAY_A );
        
        // 각 사이트의 남은 라이센스 기간 계산
        foreach ( $results as &$result ) {
            if ( ! empty( $result['expires_at'] ) ) {
                $expires_timestamp = strtotime( $result['expires_at'] );
                $current_timestamp = function_exists( 'current_time' ) ? current_time( 'timestamp' ) : time();
                $days_remaining = floor( ( $expires_timestamp - $current_timestamp ) / DAY_IN_SECONDS );
                $result['days_remaining'] = $days_remaining;
                $result['is_expired'] = $days_remaining < 0;
            } else {
                $result['days_remaining'] = null;
                $result['is_expired'] = false;
            }
        }
        
        return $results;
    }
    
    /**
     * 히스토리 기록
     */
    private function add_history( $license_id, $action, $description ) {
        global $wpdb;
        
        $table_history = JJ_License_Database::get_table_name( 'history' );
        
        $wpdb->insert(
            $table_history,
            array(
                'license_id' => $license_id,
                'action' => $action,
                'description' => $description,
                'performed_at' => function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
            ),
            array( '%d', '%s', '%s', '%s' )
        );
    }
}

