<?php
/**
 * 트래픽 추적 클래스
 *
 * @package ACF_User_Journey_Analytics
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_UJA_Traffic_Tracker {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 방문 추적
     */
    public function track_visit( $data = array() ) {
        global $wpdb;

        // 설정 확인
        $settings = get_option( 'acf_uja_settings', array() );
        if ( empty( $settings['enabled'] ) ) {
            return false;
        }

        // 관리자 제외 확인
        if ( is_user_logged_in() ) {
            $user = wp_get_current_user();
            $exclude_roles = isset( $settings['exclude_roles'] ) ? $settings['exclude_roles'] : array( 'administrator' );

            foreach ( $user->roles as $role ) {
                if ( in_array( $role, $exclude_roles ) ) {
                    return false;
                }
            }
        }

        // 방문자 ID 가져오기/생성
        $visitor_id = $this->get_or_create_visitor_id();
        $session_id = $this->get_or_create_session_id();

        // URL 파라미터 수집
        $params = $this->collect_url_params();

        // 리퍼러 분석
        $referrer_url = isset( $data['referrer'] ) ? $data['referrer'] : ( isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '' );
        $referrer_info = $this->analyze_referrer( $referrer_url );

        // 광고 소스 감지
        $source_definitions = ACF_UJA_Traffic_Source_Definitions::instance();
        $ad_source = $source_definitions->detect_ad_source_from_params( $params );

        // 데이터베이스 저장
        $db = ACF_UJA_Database::instance();
        $table = $db->get_tracking_table();

        // 기존 세션 확인
        $existing = $wpdb->get_row( $wpdb->prepare(
            "SELECT id, touch_count, touch_history FROM $table WHERE session_id = %s",
            $session_id
        ) );

        $now = current_time( 'mysql' );

        if ( $existing ) {
            // 세션 업데이트
            $touch_history = json_decode( $existing->touch_history, true ) ?: array();
            $touch_history[] = array(
                'time' => $now,
                'page' => isset( $data['landing_page'] ) ? $data['landing_page'] : '',
            );

            $wpdb->update(
                $table,
                array(
                    'touch_count' => $existing->touch_count + 1,
                    'touch_history' => wp_json_encode( $touch_history ),
                    'last_touch_at' => $now,
                    'dwell_time' => isset( $data['dwell_time'] ) ? intval( $data['dwell_time'] ) : 0,
                ),
                array( 'id' => $existing->id ),
                array( '%d', '%s', '%s', '%d' ),
                array( '%d' )
            );
        } else {
            // 새 세션 생성
            $insert_data = array(
                'visitor_id' => $visitor_id,
                'session_id' => $session_id,
                'user_id' => get_current_user_id(),

                // UTM 파라미터
                'utm_source' => isset( $params['utm_source'] ) ? sanitize_text_field( $params['utm_source'] ) : '',
                'utm_medium' => isset( $params['utm_medium'] ) ? sanitize_text_field( $params['utm_medium'] ) : '',
                'utm_campaign' => isset( $params['utm_campaign'] ) ? sanitize_text_field( $params['utm_campaign'] ) : '',
                'utm_term' => isset( $params['utm_term'] ) ? sanitize_text_field( $params['utm_term'] ) : '',
                'utm_content' => isset( $params['utm_content'] ) ? sanitize_text_field( $params['utm_content'] ) : '',

                // 클릭 ID
                'gclid' => isset( $params['gclid'] ) ? sanitize_text_field( $params['gclid'] ) : '',
                'gad_source' => isset( $params['gad_source'] ) ? sanitize_text_field( $params['gad_source'] ) : '',
                'gbraid' => isset( $params['gbraid'] ) ? sanitize_text_field( $params['gbraid'] ) : '',
                'wbraid' => isset( $params['wbraid'] ) ? sanitize_text_field( $params['wbraid'] ) : '',
                'fbclid' => isset( $params['fbclid'] ) ? sanitize_text_field( $params['fbclid'] ) : '',
                'ttclid' => isset( $params['ttclid'] ) ? sanitize_text_field( $params['ttclid'] ) : '',
                'twclid' => isset( $params['twclid'] ) ? sanitize_text_field( $params['twclid'] ) : '',
                'msclkid' => isset( $params['msclkid'] ) ? sanitize_text_field( $params['msclkid'] ) : '',
                'li_fat_id' => isset( $params['li_fat_id'] ) ? sanitize_text_field( $params['li_fat_id'] ) : '',
                'epik' => isset( $params['epik'] ) ? sanitize_text_field( $params['epik'] ) : '',
                'sccid' => isset( $params['sccid'] ) ? sanitize_text_field( $params['sccid'] ) : '',

                // 네이버 파라미터
                'n_media' => isset( $params['n_media'] ) ? sanitize_text_field( $params['n_media'] ) : '',
                'n_query' => isset( $params['n_query'] ) ? sanitize_text_field( $params['n_query'] ) : '',
                'n_rank' => isset( $params['n_rank'] ) ? sanitize_text_field( $params['n_rank'] ) : '',
                'n_ad_group' => isset( $params['n_ad_group'] ) ? sanitize_text_field( $params['n_ad_group'] ) : '',
                'n_ad' => isset( $params['n_ad'] ) ? sanitize_text_field( $params['n_ad'] ) : '',
                'n_keyword_id' => isset( $params['n_keyword_id'] ) ? sanitize_text_field( $params['n_keyword_id'] ) : '',
                'n_keyword' => isset( $params['n_keyword'] ) ? sanitize_text_field( $params['n_keyword'] ) : '',
                'n_campaign_type' => isset( $params['n_campaign_type'] ) ? sanitize_text_field( $params['n_campaign_type'] ) : '',
                'n_ad_group_type' => isset( $params['n_ad_group_type'] ) ? sanitize_text_field( $params['n_ad_group_type'] ) : '',
                'n_match' => isset( $params['n_match'] ) ? sanitize_text_field( $params['n_match'] ) : '',
                'n_mall_pid' => isset( $params['n_mall_pid'] ) ? sanitize_text_field( $params['n_mall_pid'] ) : '',
                'n_mall_id' => isset( $params['n_mall_id'] ) ? sanitize_text_field( $params['n_mall_id'] ) : '',
                'na_source' => isset( $params['na_source'] ) ? sanitize_text_field( $params['na_source'] ) : '',
                'na_medium' => isset( $params['na_medium'] ) ? sanitize_text_field( $params['na_medium'] ) : '',
                'na_campaign' => isset( $params['na_campaign'] ) ? sanitize_text_field( $params['na_campaign'] ) : '',
                'na_adset' => isset( $params['na_adset'] ) ? sanitize_text_field( $params['na_adset'] ) : '',
                'na_ad' => isset( $params['na_ad'] ) ? sanitize_text_field( $params['na_ad'] ) : '',

                // 카카오 파라미터
                'kakao_campaign' => isset( $params['kakao_campaign'] ) ? sanitize_text_field( $params['kakao_campaign'] ) : '',
                'kakao_adgrp' => isset( $params['kakao_adgrp'] ) ? sanitize_text_field( $params['kakao_adgrp'] ) : '',
                'kakao_creative' => isset( $params['kakao_creative'] ) ? sanitize_text_field( $params['kakao_creative'] ) : '',
                'kakao_keyword' => isset( $params['kakao_keyword'] ) ? sanitize_text_field( $params['kakao_keyword'] ) : '',
                'dkwid' => isset( $params['dkwid'] ) ? sanitize_text_field( $params['dkwid'] ) : '',
                'dkw' => isset( $params['dkw'] ) ? sanitize_text_field( $params['dkw'] ) : '',

                // 기타
                'dablead' => isset( $params['dablead'] ) ? sanitize_text_field( $params['dablead'] ) : '',
                'ti' => isset( $params['ti'] ) ? sanitize_text_field( $params['ti'] ) : '',

                // 광고 소스 감지 결과
                'detected_ad_source' => $ad_source['source'] ?? '',
                'detected_ad_platform' => $ad_source['platform'] ?? '',
                'detected_ad_type' => $ad_source['type'] ?? '',
                'ad_source_details' => wp_json_encode( $ad_source['details'] ?? array() ),

                // 리퍼러
                'referrer_url' => esc_url_raw( $referrer_url ),
                'referrer_domain' => $referrer_info['domain'],
                'referrer_type' => $referrer_info['type'],
                'referrer_source' => $referrer_info['source'],
                'referrer_name' => $referrer_info['name'],

                // 랜딩 페이지
                'landing_page' => isset( $data['landing_page'] ) ? esc_url_raw( $data['landing_page'] ) : '',
                'landing_path' => isset( $data['landing_path'] ) ? sanitize_text_field( $data['landing_path'] ) : '',

                // 디바이스
                'device_type' => isset( $data['device_type'] ) ? sanitize_text_field( $data['device_type'] ) : $this->detect_device_type(),
                'screen_resolution' => isset( $data['screen_resolution'] ) ? sanitize_text_field( $data['screen_resolution'] ) : '',
                'viewport' => isset( $data['viewport'] ) ? sanitize_text_field( $data['viewport'] ) : '',
                'user_language' => isset( $data['user_language'] ) ? sanitize_text_field( $data['user_language'] ) : substr( get_bloginfo( 'language' ), 0, 2 ),
                'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',

                // 터치포인트
                'touch_count' => 1,
                'touch_history' => wp_json_encode( array( array( 'time' => $now, 'page' => isset( $data['landing_page'] ) ? $data['landing_page'] : '' ) ) ),

                // 타임스탬프
                'first_touch_at' => $now,
                'last_touch_at' => $now,
            );

            $wpdb->insert( $table, $insert_data );
        }

        return true;
    }

    /**
     * 전환 기록
     */
    public function record_conversion( $visitor_id, $type, $value = 0 ) {
        global $wpdb;

        $db = ACF_UJA_Database::instance();
        $table = $db->get_tracking_table();

        $wpdb->update(
            $table,
            array(
                'converted' => 1,
                'conversion_at' => current_time( 'mysql' ),
                'conversion_value' => floatval( $value ),
                'conversion_type' => sanitize_text_field( $type ),
            ),
            array( 'visitor_id' => $visitor_id ),
            array( '%d', '%s', '%f', '%s' ),
            array( '%s' )
        );
    }

    /**
     * 방문자 ID 가져오기/생성
     */
    private function get_or_create_visitor_id() {
        if ( isset( $_COOKIE['acf_uja_visitor'] ) ) {
            return sanitize_text_field( $_COOKIE['acf_uja_visitor'] );
        }

        $visitor_id = wp_generate_uuid4();

        if ( ! headers_sent() ) {
            setcookie( 'acf_uja_visitor', $visitor_id, time() + ( DAY_IN_SECONDS * 365 ), COOKIEPATH, COOKIE_DOMAIN );
        }

        return $visitor_id;
    }

    /**
     * 세션 ID 가져오기/생성
     */
    private function get_or_create_session_id() {
        if ( isset( $_COOKIE['acf_uja_session'] ) ) {
            return sanitize_text_field( $_COOKIE['acf_uja_session'] );
        }

        $session_id = wp_generate_uuid4();

        if ( ! headers_sent() ) {
            setcookie( 'acf_uja_session', $session_id, 0, COOKIEPATH, COOKIE_DOMAIN ); // 세션 쿠키
        }

        return $session_id;
    }

    /**
     * URL 파라미터 수집
     */
    private function collect_url_params() {
        $params = array();

        // UTM 파라미터
        $utm_keys = array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content' );
        foreach ( $utm_keys as $key ) {
            if ( isset( $_GET[ $key ] ) ) {
                $params[ $key ] = sanitize_text_field( $_GET[ $key ] );
            }
        }

        // 클릭 ID 파라미터
        $click_ids = array( 'gclid', 'gad_source', 'gbraid', 'wbraid', 'fbclid', 'ttclid', 'twclid', 'msclkid', 'li_fat_id', 'epik', 'sccid' );
        foreach ( $click_ids as $key ) {
            if ( isset( $_GET[ $key ] ) ) {
                $params[ $key ] = sanitize_text_field( $_GET[ $key ] );
            }
        }

        // 네이버 파라미터
        $naver_keys = array( 'n_media', 'n_query', 'n_rank', 'n_ad_group', 'n_ad', 'n_keyword_id', 'n_keyword', 'n_campaign_type', 'n_ad_group_type', 'n_match', 'n_mall_pid', 'n_mall_id', 'na_source', 'na_medium', 'na_campaign', 'na_adset', 'na_ad' );
        foreach ( $naver_keys as $key ) {
            if ( isset( $_GET[ $key ] ) ) {
                $params[ $key ] = sanitize_text_field( $_GET[ $key ] );
            }
        }

        // 카카오 파라미터
        $kakao_keys = array( 'kakao_campaign', 'kakao_adgrp', 'kakao_creative', 'kakao_keyword', 'dkwid', 'dkw' );
        foreach ( $kakao_keys as $key ) {
            if ( isset( $_GET[ $key ] ) ) {
                $params[ $key ] = sanitize_text_field( $_GET[ $key ] );
            }
        }

        // 기타 파라미터
        $other_keys = array( 'dablead', 'ti' );
        foreach ( $other_keys as $key ) {
            if ( isset( $_GET[ $key ] ) ) {
                $params[ $key ] = sanitize_text_field( $_GET[ $key ] );
            }
        }

        return $params;
    }

    /**
     * 리퍼러 분석
     */
    private function analyze_referrer( $referrer_url ) {
        $result = array(
            'domain' => '',
            'type' => 'direct',
            'source' => 'direct',
            'name' => 'Direct',
        );

        if ( empty( $referrer_url ) ) {
            return $result;
        }

        $parsed = wp_parse_url( $referrer_url );
        $host = isset( $parsed['host'] ) ? strtolower( $parsed['host'] ) : '';
        $result['domain'] = preg_replace( '/^www\./', '', $host );

        // 내부 리퍼러 확인
        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
        if ( $host === $site_host ) {
            $result['type'] = 'internal';
            $result['source'] = 'internal';
            $result['name'] = 'Internal';
            return $result;
        }

        // 트래픽 소스 정의에서 감지
        $source_definitions = ACF_UJA_Traffic_Source_Definitions::instance();
        $detected = $source_definitions->detect_source_from_referrer( $referrer_url );

        $result['type'] = $detected['type'];
        $result['source'] = $detected['source'];
        $result['name'] = $detected['name'];

        return $result;
    }

    /**
     * 디바이스 유형 감지
     */
    private function detect_device_type() {
        if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
            return 'unknown';
        }

        $ua = $_SERVER['HTTP_USER_AGENT'];

        if ( preg_match( '/mobile|android|iphone|phone/i', $ua ) ) {
            if ( preg_match( '/ipad|tablet/i', $ua ) ) {
                return 'tablet';
            }
            return 'mobile';
        }

        return 'desktop';
    }
}
