<?php
/**
 * 플러그인 업데이트 API 클래스
 * 
 * 라이센스 서버에서 업데이트 정보를 가져오고 플러그인 파일을 제공합니다.
 * 
 * @package JJ_License_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Update_API {
    
    /**
     * 업데이트 정보 가져오기
     * 
     * @param string $plugin_slug 플러그인 슬러그 (예: jj-style-guide-free)
     * @param string $current_version 현재 버전
     * @param string $license_key 라이센스 키
     * @param string $update_channel 업데이트 채널 (stable, beta, test, dev)
     * @param bool $beta_updates_enabled 베타 업데이트 수신 여부
     * @return array|false 업데이트 정보 또는 false
     */
    public function get_update_info( $plugin_slug, $current_version, $license_key = '', $update_channel = 'stable', $beta_updates_enabled = false ) {
        // [v2.0.2] WordPress 함수 안전 호출
        if ( ! function_exists( 'get_option' ) ) {
            return false;
        }
        
        $license_server_url = get_option( 'jj_license_manager_server_url', '' );
        
        if ( empty( $license_server_url ) ) {
            return false;
        }
        
        $site_id = $this->get_site_id();
        $site_url = function_exists( 'home_url' ) ? home_url() : '';
        
        // REST API 엔드포인트 URL 생성
        $rest_url = trailingslashit( $license_server_url ) . 'wp-json/jj-license/v1/check-update';
        
        // POST 요청 데이터
        $data = array(
            'plugin_slug' => sanitize_text_field( $plugin_slug ),
            'current_version' => sanitize_text_field( $current_version ),
            'license_key' => sanitize_text_field( $license_key ),
            'site_id' => sanitize_text_field( $site_id ),
            'site_url' => esc_url_raw( $site_url ),
            'update_channel' => sanitize_text_field( $update_channel ),
            'beta_updates_enabled' => $beta_updates_enabled ? 1 : 0,
        );
        
        // API 키 가져오기 (선택사항)
        $api_key = function_exists( 'get_option' ) ? get_option( 'jj_license_api_key', '' ) : '';
        $headers = array(
            'User-Agent' => 'JJ-License-Manager/' . JJ_LICENSE_MANAGER_VERSION,
            'Content-Type' => 'application/json',
        );
        
        if ( ! empty( $api_key ) ) {
            $headers['X-API-Key'] = $api_key;
        }
        
        // WordPress HTTP API 사용
        $response = wp_remote_post( esc_url_raw( $rest_url ), array(
            'timeout' => 10,
            'sslverify' => true,
            'body' => json_encode( $data ),
            'headers' => $headers,
            'redirection' => 0,
        ) );
        
        // 네트워크 오류 처리
        if ( is_wp_error( $response ) ) {
            error_log( 'JJ License Update API: 네트워크 오류 - ' . $response->get_error_message() );
            return false;
        }
        
        // HTTP 상태 코드 확인
        $response_code = wp_remote_retrieve_response_code( $response );
        if ( $response_code !== 200 ) {
            error_log( 'JJ License Update API: 서버 응답 오류 - HTTP ' . $response_code );
            return false;
        }
        
        $body = wp_remote_retrieve_body( $response );
        
        // 응답 본문 검증
        if ( empty( $body ) || strlen( $body ) > 100000 ) {
            error_log( 'JJ License Update API: 유효하지 않은 응답 본문' );
            return false;
        }
        
        $result = json_decode( $body, true );
        
        // JSON 파싱 오류 확인
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            error_log( 'JJ License Update API: JSON 파싱 오류 - ' . json_last_error_msg() );
            return false;
        }
        
        if ( ! $result || ! isset( $result['has_update'] ) ) {
            return false;
        }
        
        // 업데이트가 없으면 false 반환
        if ( ! $result['has_update'] ) {
            return false;
        }
        
        return $result;
    }
    
    /**
     * 플러그인 파일 다운로드 URL 가져오기
     * 
     * @param string $plugin_slug 플러그인 슬러그
     * @param string $version 버전
     * @param string $license_key 라이센스 키
     * @return string|false 다운로드 URL 또는 false
     */
    public function get_download_url( $plugin_slug, $version, $license_key ) {
        $license_server_url = get_option( 'jj_license_manager_server_url', '' );
        
        if ( empty( $license_server_url ) ) {
            return false;
        }
        
        $site_id = $this->get_site_id();
        
        // REST API 엔드포인트 URL 생성
        $download_url = trailingslashit( $license_server_url ) . 'wp-json/jj-license/v1/download';
        
        // 다운로드 URL 생성 (라이센스 서버의 download 엔드포인트)
        $download_url = add_query_arg( array(
            'plugin_slug' => urlencode( $plugin_slug ),
            'version' => urlencode( $version ),
            'license_key' => urlencode( $license_key ),
            'site_id' => urlencode( $site_id ),
            'timestamp' => time(),
        ), $download_url );
        
        return $download_url;
    }
    
    /**
     * 사이트 ID 가져오기
     * 
     * @return string 사이트 ID (MD5 해시)
     */
    private function get_site_id() {
        $site_id = get_option( 'jj_license_site_id' );
        
        if ( empty( $site_id ) ) {
            // 사이트 URL 기반 고유 ID 생성
            $site_url = home_url();
            $site_id = md5( $site_url . ( defined( 'ABSPATH' ) ? ABSPATH : '' ) );
            update_option( 'jj_license_site_id', $site_id );
        }
        
        return $site_id;
    }
}

