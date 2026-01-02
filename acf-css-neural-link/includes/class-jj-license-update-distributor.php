<?php
/**
 * 업데이트 배포 및 공지 클래스
 * 
 * 플러그인 업데이트를 배포하고 공지를 전송합니다.
 * - [v4.2.1] 순차 배포(롤링 업데이트) 기능 추가
 * - [v4.2.1] 베타 테스트 채널 지원
 * - [v4.2.1] 업데이트 패키지 서명 검증 강화
 * 
 * @package JJ_License_Manager
 * @version 4.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Update_Distributor {
    
    private static $instance = null;
    
    /**
     * 배포 그룹 (순차 배포용)
     */
    const ROLLOUT_GROUP_A = 'A';
    const ROLLOUT_GROUP_B = 'B';
    const ROLLOUT_GROUP_C = 'C';
    
    /**
     * 업데이트 채널
     */
    const CHANNEL_STABLE = 'stable';
    const CHANNEL_BETA = 'beta';
    const CHANNEL_DEV = 'dev';
    
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
        // 업데이트 배포 관련 훅
        add_action( 'wp_ajax_jj_distribute_rolling_update', array( $this, 'ajax_distribute_rolling_update' ) );
        add_action( 'jj_rollout_next_group', array( $this, 'rollout_next_group' ), 10, 3 );
    }
    
    /**
     * [v4.2.1] 순차 배포(롤링 업데이트) 시작
     * 
     * @param string $plugin_slug 플러그인 슬러그
     * @param string $version 버전
     * @param string $update_channel 업데이트 채널 (stable, beta, dev)
     * @param array $options 옵션 (delay_hours, start_group, package_url, signature)
     * @return array 결과
     */
    public function start_rolling_update( $plugin_slug, $version, $update_channel = 'stable', $options = array() ) {
        global $wpdb;
        
        $defaults = array(
            'delay_hours' => 24,          // 그룹 간 대기 시간 (시간)
            'start_group' => self::ROLLOUT_GROUP_A,
            'package_url' => '',
            'signature' => '',
            'changelog' => '',
        );
        
        $options = wp_parse_args( $options, $defaults );
        
        // 업데이트 테이블 생성
        $this->create_updates_table();
        $this->create_rollout_schedule_table();
        
        // 롤아웃 스케줄 생성
        $groups = array( self::ROLLOUT_GROUP_A, self::ROLLOUT_GROUP_B, self::ROLLOUT_GROUP_C );
        $start_index = array_search( $options['start_group'], $groups, true );
        if ( $start_index === false ) {
            $start_index = 0;
        }
        
        $schedule = array();
        $base_time = time();
        
        for ( $i = 0; $i < count( $groups ); $i++ ) {
            $group_index = ( $start_index + $i ) % count( $groups );
            $group = $groups[ $group_index ];
            $scheduled_time = $base_time + ( $i * $options['delay_hours'] * HOUR_IN_SECONDS );
            
            $schedule[] = array(
                'group' => $group,
                'scheduled_time' => $scheduled_time,
                'status' => $i === 0 ? 'in_progress' : 'pending',
            );
        }
        
        // 업데이트 정보 저장
        $table_updates = $wpdb->prefix . 'jj_license_updates';
        $wpdb->insert(
            $table_updates,
            array(
                'plugin_slug' => sanitize_text_field( $plugin_slug ),
                'version' => sanitize_text_field( $version ),
                'update_channel' => sanitize_text_field( $update_channel ),
                'distributed_at' => current_time( 'mysql' ),
                'status' => 'rolling',
            ),
            array( '%s', '%s', '%s', '%s', '%s' )
        );
        
        $update_id = $wpdb->insert_id;
        
        // 롤아웃 스케줄 저장
        $table_rollout = $wpdb->prefix . 'jj_license_rollout_schedule';
        foreach ( $schedule as $item ) {
            $wpdb->insert(
                $table_rollout,
                array(
                    'update_id' => $update_id,
                    'rollout_group' => $item['group'],
                    'scheduled_time' => date( 'Y-m-d H:i:s', $item['scheduled_time'] ),
                    'status' => $item['status'],
                    'package_url' => $options['package_url'],
                    'signature' => $options['signature'],
                ),
                array( '%d', '%s', '%s', '%s', '%s', '%s' )
            );
        }
        
        // 첫 번째 그룹에 즉시 배포
        $first_group = $groups[ $start_index ];
        $result = $this->distribute_to_group( $plugin_slug, $version, $update_channel, $first_group, $options );
        
        // 다음 그룹 배포를 위한 cron 스케줄링
        if ( ! wp_next_scheduled( 'jj_rollout_next_group', array( $update_id, $plugin_slug, $version ) ) ) {
            wp_schedule_single_event(
                $base_time + $options['delay_hours'] * HOUR_IN_SECONDS,
                'jj_rollout_next_group',
                array( $update_id, $plugin_slug, $version )
            );
        }
        
        return array(
            'success' => true,
            'update_id' => $update_id,
            'schedule' => $schedule,
            'first_group_result' => $result,
        );
    }
    
    /**
     * [v4.2.1] 특정 그룹에 업데이트 배포
     */
    private function distribute_to_group( $plugin_slug, $version, $update_channel, $group, $options = array() ) {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_activations = JJ_License_Database::get_table_name( 'activations' );
        
        // 해당 그룹의 활성 라이센스 조회
        $query = $wpdb->prepare(
            "SELECT DISTINCT l.id, l.license_key, a.site_id, a.site_url, a.is_active
             FROM {$table_licenses} l
             INNER JOIN {$table_activations} a ON l.id = a.license_id
             WHERE a.is_active = 1
             AND MOD(CONV(SUBSTRING(MD5(a.site_url), 1, 2), 16, 10), 3) = %d",
            array_search( $group, array( 'A', 'B', 'C' ), true )
        );
        
        $activations = $wpdb->get_results( $query, ARRAY_A );
        
        $distributed_count = 0;
        $failed_count = 0;
        
        foreach ( $activations as $activation ) {
            $result = $this->send_update_notification(
                $activation['site_url'],
                $activation['license_key'],
                $activation['site_id'],
                $plugin_slug,
                $version,
                $update_channel
            );
            
            if ( ! is_wp_error( $result ) ) {
                $distributed_count++;
            } else {
                $failed_count++;
            }
        }
        
        return array(
            'group' => $group,
            'distributed_count' => $distributed_count,
            'failed_count' => $failed_count,
            'total_targets' => count( $activations ),
        );
    }
    
    /**
     * [v4.2.1] Cron: 다음 그룹 배포
     */
    public function rollout_next_group( $update_id, $plugin_slug, $version ) {
        global $wpdb;
        
        $table_rollout = $wpdb->prefix . 'jj_license_rollout_schedule';
        
        // pending 상태의 다음 그룹 찾기
        $next_group = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_rollout} WHERE update_id = %d AND status = 'pending' ORDER BY scheduled_time ASC LIMIT 1",
            $update_id
        ), ARRAY_A );
        
        if ( ! $next_group ) {
            // 모든 그룹 배포 완료
            $table_updates = $wpdb->prefix . 'jj_license_updates';
            $wpdb->update(
                $table_updates,
                array( 'status' => 'completed' ),
                array( 'id' => $update_id ),
                array( '%s' ),
                array( '%d' )
            );
            return;
        }
        
        // 업데이트 정보 조회
        $table_updates = $wpdb->prefix . 'jj_license_updates';
        $update_info = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_updates} WHERE id = %d",
            $update_id
        ), ARRAY_A );
        
        if ( ! $update_info ) {
            return;
        }
        
        // 그룹에 배포
        $options = array(
            'package_url' => $next_group['package_url'],
            'signature' => $next_group['signature'],
        );
        
        $this->distribute_to_group( $plugin_slug, $version, $update_info['update_channel'], $next_group['rollout_group'], $options );
        
        // 상태 업데이트
        $wpdb->update(
            $table_rollout,
            array( 'status' => 'completed' ),
            array( 'id' => $next_group['id'] ),
            array( '%s' ),
            array( '%d' )
        );
        
        // 다음 그룹 스케줄링
        $next_pending = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_rollout} WHERE update_id = %d AND status = 'pending' ORDER BY scheduled_time ASC LIMIT 1",
            $update_id
        ), ARRAY_A );
        
        if ( $next_pending ) {
            $scheduled_time = strtotime( $next_pending['scheduled_time'] );
            if ( ! wp_next_scheduled( 'jj_rollout_next_group', array( $update_id, $plugin_slug, $version ) ) ) {
                wp_schedule_single_event( $scheduled_time, 'jj_rollout_next_group', array( $update_id, $plugin_slug, $version ) );
            }
        }
    }
    
    /**
     * [v4.2.1] 롤아웃 스케줄 테이블 생성
     */
    private function create_rollout_schedule_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'jj_license_rollout_schedule';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            update_id bigint(20) UNSIGNED NOT NULL,
            rollout_group varchar(10) NOT NULL,
            scheduled_time datetime NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'pending',
            package_url varchar(500) DEFAULT '',
            signature varchar(500) DEFAULT '',
            completed_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY update_id (update_id),
            KEY rollout_group (rollout_group),
            KEY status (status)
        ) {$charset_collate};";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    /**
     * [v4.2.1] 베타 테스터에게만 배포
     */
    public function distribute_to_beta_testers( $plugin_slug, $version, $options = array() ) {
        return $this->distribute_update( $plugin_slug, $version, self::CHANNEL_BETA, array(), $options );
    }
    
    /**
     * 업데이트 배포
     * 
     * @param string $plugin_slug 플러그인 슬러그
     * @param string $version 버전
     * @param string $update_channel 업데이트 채널 (stable, beta, test, dev)
     * @param array $target_licenses 대상 라이센스 키 배열 (빈 배열이면 모든 라이센스)
     * @return array 결과
     */
    public function distribute_update( $plugin_slug, $version, $update_channel = 'stable', $target_licenses = array() ) {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_activations = JJ_License_Database::get_table_name( 'activations' );
        $table_updates = $wpdb->prefix . 'jj_license_updates';
        
        // 업데이트 테이블 생성
        $this->create_updates_table();
        
        // 업데이트 정보 저장
        $wpdb->insert(
            $table_updates,
            array(
                'plugin_slug' => sanitize_text_field( $plugin_slug ),
                'version' => sanitize_text_field( $version ),
                'update_channel' => sanitize_text_field( $update_channel ),
                'distributed_at' => function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
                'status' => 'distributed',
            ),
            array( '%s', '%s', '%s', '%s', '%s' )
        );
        
        $update_id = $wpdb->insert_id;
        
        // 대상 라이센스 조회
        $query = "SELECT DISTINCT l.id, l.license_key, a.site_id, a.site_url, a.is_active
                  FROM {$table_licenses} l
                  INNER JOIN {$table_activations} a ON l.id = a.license_id
                  WHERE a.is_active = 1";
        
        if ( ! empty( $target_licenses ) ) {
            $placeholders = implode( ',', array_fill( 0, count( $target_licenses ), '%s' ) );
            $query .= $wpdb->prepare( " AND l.license_key IN ({$placeholders})", $target_licenses );
        }
        
        $activations = $wpdb->get_results( $query, ARRAY_A );
        
        $distributed_count = 0;
        $failed_count = 0;
        
        foreach ( $activations as $activation ) {
            // 업데이트 알림 전송
            $result = $this->send_update_notification(
                $activation['site_url'],
                $activation['license_key'],
                $activation['site_id'],
                $plugin_slug,
                $version,
                $update_channel
            );
            
            if ( ! is_wp_error( $result ) ) {
                $distributed_count++;
            } else {
                $failed_count++;
            }
        }
        
        return array(
            'success' => true,
            'update_id' => $update_id,
            'distributed_count' => $distributed_count,
            'failed_count' => $failed_count,
            'total_targets' => count( $activations ),
        );
    }
    
    /**
     * 공지 전송
     * 
     * @param string $title 공지 제목
     * @param string $message 공지 내용
     * @param string $type 공지 타입 (info, warning, error, success)
     * @param array $target_licenses 대상 라이센스 키 배열 (빈 배열이면 모든 라이센스)
     * @return array 결과
     */
    public function send_announcement( $title, $message, $type = 'info', $target_licenses = array() ) {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_activations = JJ_License_Database::get_table_name( 'activations' );
        $table_announcements = $wpdb->prefix . 'jj_license_announcements';
        
        // 공지 테이블 생성
        $this->create_announcements_table();
        
        // 공지 저장
        $wpdb->insert(
            $table_announcements,
            array(
                'title' => sanitize_text_field( $title ),
                'message' => wp_kses_post( $message ),
                'type' => sanitize_text_field( $type ),
                'sent_at' => function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
                'status' => 'sent',
            ),
            array( '%s', '%s', '%s', '%s', '%s' )
        );
        
        $announcement_id = $wpdb->insert_id;
        
        // 대상 라이센스 조회
        $query = "SELECT DISTINCT l.id, l.license_key, a.site_id, a.site_url, a.is_active
                  FROM {$table_licenses} l
                  INNER JOIN {$table_activations} a ON l.id = a.license_id
                  WHERE a.is_active = 1";
        
        if ( ! empty( $target_licenses ) ) {
            $placeholders = implode( ',', array_fill( 0, count( $target_licenses ), '%s' ) );
            $query .= $wpdb->prepare( " AND l.license_key IN ({$placeholders})", $target_licenses );
        }
        
        $activations = $wpdb->get_results( $query, ARRAY_A );
        
        $sent_count = 0;
        $failed_count = 0;
        
        foreach ( $activations as $activation ) {
            // 공지 전송
            $result = $this->send_announcement_notification(
                $activation['site_url'],
                $activation['license_key'],
                $activation['site_id'],
                $title,
                $message,
                $type
            );
            
            if ( ! is_wp_error( $result ) ) {
                $sent_count++;
            } else {
                $failed_count++;
            }
        }
        
        return array(
            'success' => true,
            'announcement_id' => $announcement_id,
            'sent_count' => $sent_count,
            'failed_count' => $failed_count,
            'total_targets' => count( $activations ),
        );
    }
    
    /**
     * 업데이트 알림 전송
     */
    private function send_update_notification( $site_url, $license_key, $site_id, $plugin_slug, $version, $update_channel ) {
        // 보안 서명 생성
        $secret_key = function_exists( 'get_option' ) ? get_option( 'jj_license_manager_secret_key', '' ) : '';
        if ( empty( $secret_key ) ) {
            return new WP_Error( 'missing_secret_key', __( '시크릿 키가 설정되지 않았습니다.', 'jj-license-manager' ) );
        }
        
        $data = array(
            'action' => 'update_notification',
            'license_key' => $license_key,
            'site_id' => $site_id,
            'plugin_slug' => $plugin_slug,
            'version' => $version,
            'update_channel' => $update_channel,
            'timestamp' => time(),
        );
        
        ksort( $data );
        $data_string = http_build_query( $data );
        $signature = hash_hmac( 'sha256', $data_string, $secret_key );
        $data['signature'] = $signature;
        
        // REST API 엔드포인트 URL
        $api_url = trailingslashit( $site_url ) . 'wp-json/jj-license/v1/update-notification';
        
        // 요청 전송
        $response = wp_remote_post( esc_url_raw( $api_url ), array(
            'timeout' => 10,
            'sslverify' => true,
            'body' => json_encode( $data ),
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
        
        return json_decode( wp_remote_retrieve_body( $response ), true );
    }
    
    /**
     * 공지 알림 전송
     */
    private function send_announcement_notification( $site_url, $license_key, $site_id, $title, $message, $type ) {
        // 보안 서명 생성
        $secret_key = function_exists( 'get_option' ) ? get_option( 'jj_license_manager_secret_key', '' ) : '';
        if ( empty( $secret_key ) ) {
            return new WP_Error( 'missing_secret_key', __( '시크릿 키가 설정되지 않았습니다.', 'jj-license-manager' ) );
        }
        
        $data = array(
            'action' => 'announcement',
            'license_key' => $license_key,
            'site_id' => $site_id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'timestamp' => time(),
        );
        
        ksort( $data );
        $data_string = http_build_query( $data );
        $signature = hash_hmac( 'sha256', $data_string, $secret_key );
        $data['signature'] = $signature;
        
        // REST API 엔드포인트 URL
        $api_url = trailingslashit( $site_url ) . 'wp-json/jj-license/v1/announcement';
        
        // 요청 전송
        $response = wp_remote_post( esc_url_raw( $api_url ), array(
            'timeout' => 10,
            'sslverify' => true,
            'body' => json_encode( $data ),
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
        
        return json_decode( wp_remote_retrieve_body( $response ), true );
    }
    
    /**
     * 업데이트 테이블 생성
     */
    private function create_updates_table() {
        global $wpdb;
        
        $table_updates = $wpdb->prefix . 'jj_license_updates';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$table_updates} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            plugin_slug varchar(255) NOT NULL,
            version varchar(50) NOT NULL,
            update_channel varchar(50) NOT NULL,
            distributed_at datetime NOT NULL,
            status varchar(50) NOT NULL,
            PRIMARY KEY (id),
            KEY plugin_slug (plugin_slug),
            KEY version (version),
            KEY distributed_at (distributed_at)
        ) {$charset_collate};";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    /**
     * 공지 테이블 생성
     */
    private function create_announcements_table() {
        global $wpdb;
        
        $table_announcements = $wpdb->prefix . 'jj_license_announcements';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$table_announcements} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            message longtext NOT NULL,
            type varchar(50) NOT NULL,
            sent_at datetime NOT NULL,
            status varchar(50) NOT NULL,
            PRIMARY KEY (id),
            KEY type (type),
            KEY sent_at (sent_at)
        ) {$charset_collate};";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}

