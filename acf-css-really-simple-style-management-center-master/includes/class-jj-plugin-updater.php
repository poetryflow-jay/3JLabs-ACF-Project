<?php
/**
 * 플러그인 업데이트 체크 클래스
 * 
 * WordPress 플러그인 업데이트 시스템과 통합하여 자동 업데이트를 제공합니다.
 * 
 * @package JJ_Style_Guide
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Plugin_Updater {
    
    private $plugin_file;
    private $plugin_slug;
    private $plugin_name;
    private $current_version;
    private $license_key;
    private $license_type;
    private $update_api_url;
    private $update_channel;
    private $beta_updates_enabled;
    
    /**
     * 생성자
     * 
     * @param string $plugin_file 플러그인 메인 파일 경로
     * @param string $plugin_slug 플러그인 슬러그
     * @param string $plugin_name 플러그인 이름
     * @param string $current_version 현재 버전
     * @param string $license_type 라이센스 타입 (FREE, BASIC, PREM, UNLIM)
     */
    public function __construct( $plugin_file, $plugin_slug, $plugin_name, $current_version, $license_type = 'FREE' ) {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = $plugin_slug;
        $this->plugin_name = $plugin_name;
        $this->current_version = $current_version;
        $this->license_type = $license_type;
        
        // 기본값 설정 (WordPress 함수가 없을 경우 대비)
        $this->license_key = '';
        $this->update_api_url = '';
        $this->update_channel = 'stable';
        $this->beta_updates_enabled = false;
        
        // WordPress가 완전히 로드된 후에만 초기화
        // plugins_loaded 훅 안에서 호출되는 경우 즉시 실행
        if ( did_action( 'plugins_loaded' ) ) {
            $this->init();
        } else {
            // plugins_loaded 훅에 등록 (일반적인 경우)
            add_action( 'plugins_loaded', array( $this, 'init' ), 25 );
        }
    }
    
    /**
     * 초기화 (WordPress 로드 후 실행)
     */
    public function init() {
        // WordPress 함수가 없으면 초기화하지 않음
        if ( ! function_exists( 'get_option' ) ) {
            return;
        }
        
        // 라이센스 키 가져오기
        $this->license_key = get_option( 'jj_style_guide_license_key', '' );
        
        // 업데이트 API URL 가져오기
        $this->update_api_url = get_option( 'jj_license_manager_server_url', 'https://j-j-labs.com' );
        
        // 업데이트 설정 가져오기
        $update_settings = get_option( 'jj_style_guide_update_settings', array(
            'auto_update_enabled' => true,
            'update_channel' => 'stable',
            'beta_updates_enabled' => false,
            'send_app_logs' => false,
            'send_error_logs' => false,
        ) );
        
        $this->update_channel = isset( $update_settings['update_channel'] ) ? $update_settings['update_channel'] : 'stable';
        $this->beta_updates_enabled = isset( $update_settings['beta_updates_enabled'] ) ? $update_settings['beta_updates_enabled'] : false;
        
        // 레거시 채널 매핑 (test/dev → staging)
        if ( in_array( $this->update_channel, array( 'test', 'dev' ), true ) ) {
            $this->update_channel = 'staging';
        }

        // Partner/Master는 기본적으로 staging 채널을 사용 (내부/파트너용)
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

        if ( $is_partner_or_higher && $this->update_channel === 'stable' ) {
            $this->update_channel = 'staging';
            $this->beta_updates_enabled = true;
        }
        
        // 업데이트 체크 훅 등록
        $this->init_hooks();
        
        // 로그 전송 스케줄링 (init 훅에서 실행)
        if ( did_action( 'init' ) ) {
            $this->schedule_log_sending();
        } else {
            add_action( 'init', array( $this, 'schedule_log_sending' ), 20 );
        }
    }
    
    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // WordPress 업데이트 시스템과 통합
        add_filter( 'site_transient_update_plugins', array( $this, 'check_for_updates' ) );
        add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
        add_filter( 'upgrader_post_install', array( $this, 'post_install' ), 10, 3 );
        
        // 수동 업데이트 체크
        add_action( 'wp_ajax_jj_check_plugin_updates', array( $this, 'ajax_check_updates' ) );
        
        // 자동 업데이트 활성화/비활성화
        $this->handle_auto_update();
    }
    
    /**
     * 자동 업데이트 활성화/비활성화 처리
     */
    private function handle_auto_update() {
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'plugin_basename' ) || ! function_exists( 'get_site_option' ) || ! function_exists( 'update_site_option' ) ) {
            return;
        }
        
        $update_settings = get_option( 'jj_style_guide_update_settings', array() );
        $auto_update_enabled = isset( $update_settings['auto_update_enabled'] ) ? $update_settings['auto_update_enabled'] : true;
        
        $plugin_file = plugin_basename( $this->plugin_file );
        if ( empty( $plugin_file ) ) {
            return;
        }
        
        if ( $auto_update_enabled ) {
            // 자동 업데이트 활성화
            $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
            if ( ! in_array( $plugin_file, $auto_updates, true ) ) {
                $auto_updates[] = $plugin_file;
                update_site_option( 'auto_update_plugins', $auto_updates );
            }
        } else {
            // 자동 업데이트 비활성화
            $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
            $key = array_search( $plugin_file, $auto_updates, true );
            if ( false !== $key ) {
                unset( $auto_updates[ $key ] );
                update_site_option( 'auto_update_plugins', array_values( $auto_updates ) );
            }
        }
    }
    
    /**
     * 업데이트 체크
     * 
     * @param object $transient 업데이트 transient 객체
     * @return object 수정된 transient 객체
     */
    public function check_for_updates( $transient ) {
        // WordPress 함수가 없으면 체크하지 않음
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'plugin_basename' ) ) {
            return $transient;
        }
        
        // 업데이트 API URL이 없으면 체크하지 않음
        if ( empty( $this->update_api_url ) ) {
            return $transient;
        }
        
        // NOTE: 자동 업데이트 OFF여도 "업데이트 알림/수동 업데이트"는 유지되어야 합니다.
        // 자동 업데이트의 실제 동작은 WordPress 코어(auto_update_plugins) 설정에 의해 제어됩니다.
        
        // 플러그인 파일 경로 생성
        $plugin_basename = plugin_basename( $this->plugin_file );
        if ( empty( $plugin_basename ) ) {
            return $transient;
        }
        
        // 업데이트 정보 가져오기
        $update_info = $this->get_update_info();
        
        if ( $update_info && isset( $update_info['new_version'] ) ) {
            // 버전 비교
            if ( version_compare( $this->current_version, $update_info['new_version'], '<' ) ) {
                // 업데이트 정보 추가
                $transient->response[ $plugin_basename ] = (object) array(
                    'slug' => $this->plugin_slug,
                    'plugin' => $plugin_basename,
                    'new_version' => $update_info['new_version'],
                    'url' => isset( $update_info['url'] ) ? $update_info['url'] : '',
                    'package' => $this->get_download_url( $update_info['new_version'] ),
                    'tested' => isset( $update_info['tested'] ) ? $update_info['tested'] : get_bloginfo( 'version' ),
                    'requires_php' => isset( $update_info['requires_php'] ) ? $update_info['requires_php'] : '7.4',
                    'compatibility' => isset( $update_info['compatibility'] ) ? $update_info['compatibility'] : new stdClass(),
                );
            }
        }
        
        return $transient;
    }
    
    /**
     * 플러그인 API 필터
     * 
     * @param false|object|array $result 플러그인 정보
     * @param string $action 액션
     * @param object $args 인수
     * @return false|object|array 수정된 플러그인 정보
     */
    public function plugins_api_filter( $result, $action, $args ) {
        if ( $action !== 'plugin_information' || $args->slug !== $this->plugin_slug ) {
            return $result;
        }
        
        // 업데이트 정보 가져오기
        $update_info = $this->get_update_info();
        
        if ( $update_info ) {
            $result = (object) array(
                'name' => $this->plugin_name,
                'slug' => $this->plugin_slug,
                'version' => isset( $update_info['new_version'] ) ? $update_info['new_version'] : $this->current_version,
                'author' => isset( $update_info['author'] ) ? $update_info['author'] : 'Jay & Jenny Labs',
                'author_profile' => isset( $update_info['author_profile'] ) ? $update_info['author_profile'] : 'https://poetryflow.blog',
                'requires' => isset( $update_info['requires'] ) ? $update_info['requires'] : '6.0',
                'tested' => isset( $update_info['tested'] ) ? $update_info['tested'] : get_bloginfo( 'version' ),
                'requires_php' => isset( $update_info['requires_php'] ) ? $update_info['requires_php'] : '7.4',
                'download_link' => $this->get_download_url( isset( $update_info['new_version'] ) ? $update_info['new_version'] : $this->current_version ),
                'sections' => isset( $update_info['sections'] ) ? $update_info['sections'] : array(),
                'banners' => isset( $update_info['banners'] ) ? $update_info['banners'] : array(),
            );
        }
        
        return $result;
    }
    
    /**
     * 업데이트 후 처리
     * 
     * @param bool $response 응답
     * @param array $hook_extra 추가 정보
     * @param array $result 결과
     * @return bool 수정된 응답
     */
    public function post_install( $response, $hook_extra, $result ) {
        $plugin_basename = plugin_basename( $this->plugin_file );
        
        if ( isset( $hook_extra['plugin'] ) && $hook_extra['plugin'] === $plugin_basename ) {
            // 플러그인 활성화 유지
            $activate = activate_plugin( $plugin_basename );
            if ( is_wp_error( $activate ) ) {
                error_log( 'JJ Plugin Updater: 플러그인 활성화 실패 - ' . $activate->get_error_message() );
            }
        }
        
        return $response;
    }
    
    /**
     * AJAX 업데이트 체크
     */
    public function ajax_check_updates() {
        check_ajax_referer( 'jj_plugin_updater_nonce', 'nonce' );
        
        if ( ! current_user_can( 'update_plugins' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        // 캐시 삭제하여 강제로 업데이트 체크
        delete_site_transient( 'update_plugins' );
        
        // 업데이트 정보 가져오기
        $update_info = $this->get_update_info();
        
        if ( $update_info && isset( $update_info['new_version'] ) ) {
            if ( version_compare( $this->current_version, $update_info['new_version'], '<' ) ) {
                wp_send_json_success( array(
                    'has_update' => true,
                    'current_version' => $this->current_version,
                    'new_version' => $update_info['new_version'],
                    'message' => sprintf( __( '새 버전 %s이(가) 사용 가능합니다.', 'jj-style-guide' ), $update_info['new_version'] ),
                ) );
            } else {
                wp_send_json_success( array(
                    'has_update' => false,
                    'message' => __( '최신 버전을 사용 중입니다.', 'jj-style-guide' ),
                ) );
            }
        } else {
            wp_send_json_error( array( 'message' => __( '업데이트 정보를 가져올 수 없습니다.', 'jj-style-guide' ) ) );
        }
    }
    
    /**
     * 업데이트 정보 가져오기
     * 
     * @return array|false 업데이트 정보 또는 false
     */
    public function get_update_info() {
        if ( ! function_exists( 'get_transient' ) || ! function_exists( 'set_transient' ) ) {
            return false;
        }
        
        // 캐시 확인 (채널 포함)
        $cache_key = 'jj_plugin_update_' . md5( $this->plugin_slug . $this->current_version . $this->license_key . $this->update_channel );
        $cached = get_transient( $cache_key );
        
        if ( $cached !== false ) {
            return $cached;
        }
        
        // 라이센스 매니저의 업데이트 API 사용
        if ( class_exists( 'JJ_License_Update_API' ) ) {
            $update_api = new JJ_License_Update_API();
            $update_info = $update_api->get_update_info( 
                $this->plugin_slug, 
                $this->current_version, 
                $this->license_key,
                $this->update_channel,
                $this->beta_updates_enabled
            );
            
            // 캐시 저장 (12시간)
            if ( $update_info && defined( 'HOUR_IN_SECONDS' ) ) {
                set_transient( $cache_key, $update_info, 12 * HOUR_IN_SECONDS );
            }
            
            return $update_info;
        }
        
        return false;
    }
    
    /**
     * 로그 전송 스케줄링
     */
    public function schedule_log_sending() {
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'wp_next_scheduled' ) || ! function_exists( 'wp_schedule_event' ) || ! function_exists( 'wp_unschedule_event' ) ) {
            return;
        }
        
        $update_settings = get_option( 'jj_style_guide_update_settings', array() );
        $send_app_logs = isset( $update_settings['send_app_logs'] ) ? $update_settings['send_app_logs'] : false;
        $send_error_logs = isset( $update_settings['send_error_logs'] ) ? $update_settings['send_error_logs'] : false;
        
        // Partner/Master는 모든 로그를 반드시 전송
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
            $send_app_logs = true;
            $send_error_logs = true;
        }
        
        // 로그 전송이 활성화된 경우에만 스케줄링
        if ( $send_app_logs || $send_error_logs ) {
            if ( ! wp_next_scheduled( 'jj_send_logs_to_server' ) ) {
                wp_schedule_event( time(), 'daily', 'jj_send_logs_to_server' );
            }
            add_action( 'jj_send_logs_to_server', array( $this, 'send_logs_to_server' ) );
        } else {
            // 비활성화된 경우 스케줄 제거
            $timestamp = wp_next_scheduled( 'jj_send_logs_to_server' );
            if ( $timestamp ) {
                wp_unschedule_event( $timestamp, 'jj_send_logs_to_server' );
            }
        }
    }
    
    /**
     * 로그를 서버로 전송
     */
    public function send_logs_to_server() {
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'home_url' ) || ! function_exists( 'wp_remote_post' ) ) {
            return;
        }
        
        $update_settings = get_option( 'jj_style_guide_update_settings', array() );
        $send_app_logs = isset( $update_settings['send_app_logs'] ) ? $update_settings['send_app_logs'] : false;
        $send_error_logs = isset( $update_settings['send_error_logs'] ) ? $update_settings['send_error_logs'] : false;
        
        // Partner/Master는 모든 로그를 반드시 전송
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
            $send_app_logs = true;
            $send_error_logs = true;
        }
        
        if ( ! $send_app_logs && ! $send_error_logs ) {
            return;
        }
        
        $license_server_url = get_option( 'jj_license_manager_server_url', '' );
        if ( empty( $license_server_url ) ) {
            return;
        }
        
        $logs = array();
        
        // 앱 내 로그 수집
        if ( $send_app_logs && class_exists( 'JJ_Error_Logger' ) ) {
            $error_logger = JJ_Error_Logger::instance();
            if ( method_exists( $error_logger, 'get_app_logs' ) ) {
                $logs['app'] = $error_logger->get_app_logs();
            }
        }
        
        // 오류 로그 수집
        if ( $send_error_logs ) {
            // WordPress 오류 로그
            $error_log_file = WP_CONTENT_DIR . '/debug.log';
            if ( file_exists( $error_log_file ) && is_readable( $error_log_file ) ) {
                $error_logs = file_get_contents( $error_log_file );
                if ( strlen( $error_logs ) > 100000 ) {
                    // 로그가 너무 크면 마지막 100KB만 전송
                    $error_logs = substr( $error_logs, -100000 );
                }
                $logs['error'] = $error_logs;
            }
        }
        
        if ( empty( $logs ) ) {
            return;
        }
        
        // REST API 엔드포인트 URL 생성
        $rest_url = trailingslashit( $license_server_url ) . 'wp-json/jj-license/v1/logs';
        
        // POST 요청 데이터
        $data = array(
            'plugin_slug' => sanitize_text_field( $this->plugin_slug ),
            'plugin_version' => sanitize_text_field( $this->current_version ),
            'site_id' => $this->get_site_id(),
            'site_url' => esc_url_raw( home_url() ),
            'logs' => $logs,
            'timestamp' => time(),
        );
        
        // API 키 가져오기 (선택사항)
        $api_key = get_option( 'jj_license_api_key', '' );
        $headers = array(
            'User-Agent' => 'JJ-License-Manager/' . ( defined( 'JJ_LICENSE_MANAGER_VERSION' ) ? JJ_LICENSE_MANAGER_VERSION : '2.0.0' ),
            'Content-Type' => 'application/json',
        );
        
        if ( ! empty( $api_key ) ) {
            $headers['X-API-Key'] = $api_key;
        }
        
        // WordPress HTTP API 사용
        $response = wp_remote_post( esc_url_raw( $rest_url ), array(
            'timeout' => 30,
            'sslverify' => true,
            'body' => json_encode( $data ),
            'headers' => $headers,
            'redirection' => 0,
        ) );
        
        // 네트워크 오류 처리 (로그만 기록, 사용자에게 알리지 않음)
        if ( is_wp_error( $response ) ) {
            error_log( 'JJ Plugin Updater: 로그 전송 실패 - ' . $response->get_error_message() );
        }
    }
    
    /**
     * 사이트 ID 가져오기
     */
    private function get_site_id() {
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'home_url' ) || ! function_exists( 'update_option' ) ) {
            return '';
        }
        
        $site_id = get_option( 'jj_license_site_id' );
        
        if ( empty( $site_id ) ) {
            $site_url = home_url();
            $site_id = md5( $site_url . ( defined( 'ABSPATH' ) ? ABSPATH : '' ) );
            update_option( 'jj_license_site_id', $site_id );
        }
        
        return $site_id;
    }
    
    /**
     * 다운로드 URL 가져오기
     * 
     * @param string $version 버전
     * @return string 다운로드 URL
     */
    private function get_download_url( $version ) {
        if ( class_exists( 'JJ_License_Update_API' ) ) {
            $update_api = new JJ_License_Update_API();
            $download_url = $update_api->get_download_url( $this->plugin_slug, $version, $this->license_key );
            
            if ( $download_url ) {
                return $download_url;
            }
        }
        
        return '';
    }
}

