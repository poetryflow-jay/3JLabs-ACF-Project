<?php
/**
 * 캠페인 트래커 클래스
 *
 * @package JJ_Marketing_Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 캠페인 추적 및 분석
 */
class JJ_Campaign_Tracker {

    /**
     * 생성자
     */
    public function __construct() {
        add_action( 'init', array( $this, 'track_campaigns' ) );
    }

    /**
     * 캠페인 추적
     */
    public function track_campaigns() {
        // 캠페인 파라미터 확인
        if ( isset( $_GET['utm_source'] ) || isset( $_GET['utm_campaign'] ) || isset( $_GET['utm_medium'] ) ) {
            $this->record_campaign_visit();
        }
    }

    /**
     * 캠페인 방문 기록
     */
    private function record_campaign_visit() {
        $campaign_data = array(
            'source' => isset( $_GET['utm_source'] ) ? sanitize_text_field( $_GET['utm_source'] ) : '',
            'medium' => isset( $_GET['utm_medium'] ) ? sanitize_text_field( $_GET['utm_medium'] ) : '',
            'campaign' => isset( $_GET['utm_campaign'] ) ? sanitize_text_field( $_GET['utm_campaign'] ) : '',
            'term' => isset( $_GET['utm_term'] ) ? sanitize_text_field( $_GET['utm_term'] ) : '',
            'content' => isset( $_GET['utm_content'] ) ? sanitize_text_field( $_GET['utm_content'] ) : '',
            'timestamp' => current_time( 'mysql' ),
            'ip' => $this->get_client_ip(),
            'referrer' => isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( $_SERVER['HTTP_REFERER'] ) : '',
        );

        // 캠페인 데이터 저장
        $campaigns = get_option( 'jj_marketing_campaigns', array() );
        $campaigns[] = $campaign_data;
        
        // 최근 1000개만 유지
        if ( count( $campaigns ) > 1000 ) {
            $campaigns = array_slice( $campaigns, -1000 );
        }
        
        update_option( 'jj_marketing_campaigns', $campaigns );
    }

    /**
     * 클라이언트 IP 주소 가져오기
     */
    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );

        foreach ( $ip_keys as $key ) {
            if ( array_key_exists( $key, $_SERVER ) === true ) {
                foreach ( explode( ',', $_SERVER[ $key ] ) as $ip ) {
                    $ip = trim( $ip );
                    if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
                        return $ip;
                    }
                }
            }
        }

        return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
    }

    /**
     * 캠페인 통계 가져오기
     */
    public function get_campaign_stats( $date_range = 30 ) {
        $campaigns = get_option( 'jj_marketing_campaigns', array() );
        $stats = array(
            'total_visits' => 0,
            'unique_sources' => array(),
            'campaigns' => array(),
            'mediums' => array(),
        );

        $cutoff_date = date( 'Y-m-d H:i:s', strtotime( "-{$date_range} days" ) );

        foreach ( $campaigns as $campaign ) {
            if ( isset( $campaign['timestamp'] ) && $campaign['timestamp'] >= $cutoff_date ) {
                $stats['total_visits']++;
                
                if ( ! empty( $campaign['source'] ) ) {
                    if ( ! isset( $stats['unique_sources'][ $campaign['source'] ] ) ) {
                        $stats['unique_sources'][ $campaign['source'] ] = 0;
                    }
                    $stats['unique_sources'][ $campaign['source'] ]++;
                }
                
                if ( ! empty( $campaign['campaign'] ) ) {
                    if ( ! isset( $stats['campaigns'][ $campaign['campaign'] ] ) ) {
                        $stats['campaigns'][ $campaign['campaign'] ] = 0;
                    }
                    $stats['campaigns'][ $campaign['campaign'] ]++;
                }
                
                if ( ! empty( $campaign['medium'] ) ) {
                    if ( ! isset( $stats['mediums'][ $campaign['medium'] ] ) ) {
                        $stats['mediums'][ $campaign['medium'] ] = 0;
                    }
                    $stats['mediums'][ $campaign['medium'] ]++;
                }
            }
        }

        return $stats;
    }
}
