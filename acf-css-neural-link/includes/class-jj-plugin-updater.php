<?php
/**
 * JJ Plugin Updater - WordPress 업데이트 시스템 통합
 *
 * 3J Labs ACF CSS 플러그인 패밀리의 자동 업데이트를 WordPress 표준 업데이트 시스템에 통합합니다.
 *
 * [v6.3.2] Update Hijacking 방지 기능 추가:
 * - 공식 서버 URL 화이트리스트 검증
 * - HTTPS 강제 사용
 * - 패키지 무결성 서명 검증
 * - API 응답 구조 검증
 *
 * 사용법:
 * $updater = new JJ_Plugin_Updater(
 *     'https://3j-labs.com',                    // API 서버 URL
 *     'acf-css-neural-link/acf-css-neural-link.php', // 플러그인 basename
 *     array(
 *         'version'   => '4.1.0',
 *         'license'   => 'your-license-key',
 *         'item_name' => 'ACF CSS Neural Link',
 *         'author'    => '3J Labs',
 *         'beta'      => false,
 *     )
 * );
 *
 * @package JJ_Neural_Link
 * @version 1.1.0
 * @since 4.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// [v4.2.1] 클래스 중복 선언 방지
if ( ! class_exists( 'JJ_Plugin_Updater' ) ) {

class JJ_Plugin_Updater {

    /**
     * API 서버 URL
     * @var string
     */
    private $api_url = '';

    /**
     * 플러그인 파일 (basename)
     * @var string
     */
    private $name = '';

    /**
     * 플러그인 슬러그
     * @var string
     */
    private $slug = '';

    /**
     * 플러그인 데이터
     * @var array
     */
    private $api_data = array();

    /**
     * 캐시 키
     * @var string
     */
    private $cache_key = '';

    /**
     * 캐시 시간 (초)
     * @var int
     */
    private $cache_time = 3600; // 1시간

    /**
     * 베타 업데이트 수신 여부
     * @var bool
     */
    private $beta = false;

    /**
     * [v6.3.2] 허용된 API 서버 도메인 목록
     * Update hijacking 방지를 위한 화이트리스트
     * @var array
     */
    private static $allowed_domains = array(
        '3j-labs.com',
        'api.3j-labs.com',
        'updates.3j-labs.com',
        'localhost',       // 개발 환경
        '127.0.0.1',       // 개발 환경
    );

    /**
     * [v6.3.2] 공개 검증 키 (패키지 서명 확인용)
     * @var string
     */
    private static $public_key = 'JJ3LABS_UPDATE_PUBLIC_KEY_2026';

    /**
     * 생성자
     *
     * @param string $api_url  API 서버 URL
     * @param string $plugin_file 플러그인 파일 경로
     * @param array  $api_data 플러그인 데이터
     */
    public function __construct( $api_url, $plugin_file, $api_data = null ) {
        global $edd_plugin_data;

        $this->api_url  = trailingslashit( $api_url );
        $this->name     = plugin_basename( $plugin_file );
        $this->slug     = basename( $plugin_file, '.php' );
        $this->api_data = $api_data;
        $this->beta     = ! empty( $this->api_data['beta'] ) ? (bool) $this->api_data['beta'] : false;

        // 캐시 키 생성
        $this->cache_key = 'jj_plugin_updater_' . md5( serialize( $this->slug . $this->api_data['license'] . $this->beta ) );

        // WordPress 업데이트 시스템에 훅 등록
        $this->init();
    }

    /**
     * 훅 초기화
     */
    public function init() {
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
        add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
        add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification' ), 10, 2 );
    }

    /**
     * 업데이트 체크
     *
     * @param object $_transient_data WordPress 업데이트 transient
     * @return object 수정된 transient
     */
    public function check_update( $_transient_data ) {
        global $pagenow;

        if ( ! is_object( $_transient_data ) ) {
            $_transient_data = new stdClass();
        }

        // 이미 응답이 있으면 건너뜀
        if ( ! empty( $_transient_data->response ) && ! empty( $_transient_data->response[ $this->name ] ) ) {
            return $_transient_data;
        }

        // 버전 정보 가져오기
        $version_info = $this->get_cached_version_info();

        if ( false === $version_info ) {
            $version_info = $this->api_request();
            $this->set_version_info_cache( $version_info );
        }

        if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {
            if ( version_compare( $this->api_data['version'], $version_info->new_version, '<' ) ) {
                $_transient_data->response[ $this->name ] = $version_info;
                $_transient_data->response[ $this->name ]->plugin = $this->name;
            }

            $_transient_data->last_checked           = time();
            $_transient_data->checked[ $this->name ] = $this->api_data['version'];
        }

        return $_transient_data;
    }

    /**
     * API 요청
     *
     * @return object|false
     */
    private function api_request() {
        // [v6.3.2] API URL 보안 검증
        if ( ! $this->validate_api_url( $this->api_url ) ) {
            $this->log_security_event( 'api_request_blocked', array(
                'reason' => 'url_validation_failed',
                'url'    => $this->api_url,
            ) );
            return false;
        }

        $data = array(
            'action'      => 'get_version',
            'slug'        => $this->slug,
            'license'     => isset( $this->api_data['license'] ) ? $this->api_data['license'] : '',
            'item_name'   => isset( $this->api_data['item_name'] ) ? $this->api_data['item_name'] : '',
            'author'      => isset( $this->api_data['author'] ) ? $this->api_data['author'] : '',
            'version'     => isset( $this->api_data['version'] ) ? $this->api_data['version'] : '',
            'url'         => home_url(),
            'beta'        => $this->beta,
        );

        $request = wp_remote_post( $this->api_url . 'wp-json/jj-license/v1/check-update', array(
            'timeout'   => 15,
            'sslverify' => true, // [v6.3.2] SSL 인증서 검증 강제
            'body'      => $data,
        ) );

        if ( is_wp_error( $request ) ) {
            return false;
        }

        $response_code = wp_remote_retrieve_response_code( $request );
        if ( $response_code !== 200 ) {
            return false;
        }

        $body = wp_remote_retrieve_body( $request );
        $version_info = json_decode( $body );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return false;
        }

        if ( ! is_object( $version_info ) ) {
            return false;
        }

        // 표준 WordPress 업데이트 객체로 변환
        $update_obj = new stdClass();
        $update_obj->id            = isset( $version_info->id ) ? $version_info->id : '';
        $update_obj->slug          = $this->slug;
        $update_obj->plugin        = $this->name;
        $update_obj->new_version   = isset( $version_info->new_version ) ? $version_info->new_version : '';
        $update_obj->url           = isset( $version_info->homepage ) ? $version_info->homepage : $this->api_url;
        $update_obj->package       = isset( $version_info->download_link ) ? $version_info->download_link : '';
        $update_obj->icons         = isset( $version_info->icons ) ? (array) $version_info->icons : array();
        $update_obj->banners       = isset( $version_info->banners ) ? (array) $version_info->banners : array();
        $update_obj->banners_rtl   = array();
        $update_obj->tested        = isset( $version_info->tested ) ? $version_info->tested : '';
        $update_obj->requires_php  = isset( $version_info->requires_php ) ? $version_info->requires_php : '';
        $update_obj->compatibility = new stdClass();

        // sections (changelog 등)
        if ( isset( $version_info->sections ) ) {
            $update_obj->sections = (array) $version_info->sections;
        }

        // [v6.3.2] 종합 보안 검사
        $security_check = $this->pre_update_security_check( $update_obj );
        if ( ! $security_check['passed'] ) {
            $this->log_security_event( 'update_blocked', array(
                'version' => $update_obj->new_version,
                'errors'  => $security_check['errors'],
            ) );
            return false;
        }

        return $update_obj;
    }

    /**
     * plugins_api 필터
     *
     * @param mixed  $result
     * @param string $action
     * @param object $args
     * @return mixed
     */
    public function plugins_api_filter( $result, $action, $args ) {
        if ( 'plugin_information' !== $action ) {
            return $result;
        }

        if ( ! isset( $args->slug ) || $args->slug !== $this->slug ) {
            return $result;
        }

        $version_info = $this->get_cached_version_info();

        if ( false === $version_info ) {
            $version_info = $this->api_request();
            $this->set_version_info_cache( $version_info );
        }

        if ( false !== $version_info ) {
            $result = $version_info;
        }

        return $result;
    }

    /**
     * 업데이트 알림 표시
     *
     * @param string $file   플러그인 파일
     * @param array  $plugin 플러그인 데이터
     */
    public function show_update_notification( $file, $plugin ) {
        if ( is_network_admin() ) {
            return;
        }

        if ( ! current_user_can( 'update_plugins' ) ) {
            return;
        }

        if ( ! is_multisite() ) {
            return;
        }

        if ( $this->name !== $file ) {
            return;
        }

        // 업데이트 정보 가져오기
        $update_cache = get_site_transient( 'update_plugins' );

        if ( ! is_object( $update_cache ) || empty( $update_cache->response ) || empty( $update_cache->response[ $this->name ] ) ) {
            $version_info = $this->get_cached_version_info();

            if ( false === $version_info ) {
                $version_info = $this->api_request();
                $this->set_version_info_cache( $version_info );
            }

            if ( false !== $version_info && version_compare( $this->api_data['version'], $version_info->new_version, '<' ) ) {
                echo '<tr class="plugin-update-tr" id="' . esc_attr( $this->slug ) . '-update" data-slug="' . esc_attr( $this->slug ) . '" data-plugin="' . esc_attr( $this->name ) . '">';
                echo '<td colspan="3" class="plugin-update colspanchange">';
                echo '<div class="update-message notice inline notice-warning notice-alt"><p>';
                printf(
                    /* translators: 1: plugin name, 2: version number, 3: update URL */
                    esc_html__( '%1$s 버전 %2$s 업데이트가 있습니다. %3$s', 'acf-css-neural-link' ),
                    esc_html( $plugin['Name'] ),
                    esc_html( $version_info->new_version ),
                    '<a href="' . esc_url( wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->name, 'upgrade-plugin_' . $this->name ) ) . '">' . esc_html__( '지금 업데이트', 'acf-css-neural-link' ) . '</a>'
                );
                echo '</p></div>';
                echo '</td></tr>';
            }
        }
    }

    /**
     * 캐시된 버전 정보 가져오기
     *
     * @return object|false
     */
    private function get_cached_version_info() {
        $cache = get_option( $this->cache_key );

        if ( empty( $cache['timeout'] ) || time() > $cache['timeout'] ) {
            return false;
        }

        return isset( $cache['value'] ) ? json_decode( $cache['value'] ) : false;
    }

    /**
     * 버전 정보 캐시 설정
     *
     * @param object $value
     */
    private function set_version_info_cache( $value = '' ) {
        $data = array(
            'timeout' => strtotime( '+' . $this->cache_time . ' seconds' ),
            'value'   => wp_json_encode( $value ),
        );

        update_option( $this->cache_key, $data, 'no' );
    }

    /**
     * 캐시 삭제
     */
    public function delete_cache() {
        delete_option( $this->cache_key );
    }

    /**
     * [v6.3.2] API URL 보안 검증
     *
     * Update hijacking 방지를 위해 API URL이 허용된 도메인인지 확인합니다.
     *
     * @param string $url 검증할 URL
     * @return bool 유효 여부
     */
    private function validate_api_url( $url ) {
        // 1. URL 파싱
        $parsed = wp_parse_url( $url );
        if ( ! $parsed || empty( $parsed['host'] ) ) {
            $this->log_security_event( 'invalid_url_format', array( 'url' => $url ) );
            return false;
        }

        // 2. HTTPS 강제 (개발 환경 제외)
        $scheme = isset( $parsed['scheme'] ) ? strtolower( $parsed['scheme'] ) : '';
        $host   = strtolower( $parsed['host'] );

        $is_dev = in_array( $host, array( 'localhost', '127.0.0.1' ), true );
        if ( ! $is_dev && 'https' !== $scheme ) {
            $this->log_security_event( 'https_required', array( 'url' => $url ) );
            return false;
        }

        // 3. 허용된 도메인 확인
        $is_allowed = false;
        foreach ( self::$allowed_domains as $allowed ) {
            // 정확히 일치하거나 서브도메인으로 끝나는 경우 허용
            if ( $host === $allowed || substr( $host, -strlen( '.' . $allowed ) ) === '.' . $allowed ) {
                $is_allowed = true;
                break;
            }
        }

        if ( ! $is_allowed ) {
            $this->log_security_event( 'domain_not_allowed', array(
                'url'  => $url,
                'host' => $host,
            ) );
            return false;
        }

        return true;
    }

    /**
     * [v6.3.2] 패키지 다운로드 URL 검증
     *
     * @param string $download_url 다운로드 URL
     * @return bool 유효 여부
     */
    private function validate_package_url( $download_url ) {
        if ( empty( $download_url ) ) {
            return false;
        }

        // API URL과 동일한 검증 적용
        return $this->validate_api_url( $download_url );
    }

    /**
     * [v6.3.2] API 응답 구조 검증
     *
     * @param object $response API 응답 객체
     * @return bool 유효 여부
     */
    private function validate_response_structure( $response ) {
        if ( ! is_object( $response ) ) {
            return false;
        }

        // 필수 필드 확인
        $required_fields = array( 'new_version' );
        foreach ( $required_fields as $field ) {
            if ( ! isset( $response->$field ) ) {
                $this->log_security_event( 'missing_response_field', array( 'field' => $field ) );
                return false;
            }
        }

        // 버전 형식 검증 (시멘틱 버전 또는 간단한 숫자 버전)
        if ( ! preg_match( '/^\d+(\.\d+)*(-[\w.]+)?(\+[\w.]+)?$/', $response->new_version ) ) {
            $this->log_security_event( 'invalid_version_format', array( 'version' => $response->new_version ) );
            return false;
        }

        // 다운로드 URL이 있으면 검증
        if ( isset( $response->download_link ) && ! $this->validate_package_url( $response->download_link ) ) {
            return false;
        }

        return true;
    }

    /**
     * [v6.3.2] 패키지 서명 검증
     *
     * 다운로드된 패키지의 무결성을 HMAC 서명으로 검증합니다.
     *
     * @param string $package_path 패키지 파일 경로
     * @param string $signature    서버에서 제공한 서명
     * @return bool 유효 여부
     */
    public function verify_package_signature( $package_path, $signature ) {
        if ( ! file_exists( $package_path ) || empty( $signature ) ) {
            return false;
        }

        // 파일 해시 계산
        $file_hash = hash_file( 'sha256', $package_path );

        // 예상 서명 계산
        $expected_signature = hash_hmac( 'sha256', $file_hash, self::$public_key );

        // 서명 비교 (타이밍 공격 방지)
        if ( ! hash_equals( $expected_signature, $signature ) ) {
            $this->log_security_event( 'package_signature_mismatch', array(
                'package'            => basename( $package_path ),
                'expected_signature' => substr( $expected_signature, 0, 16 ) . '...',
                'received_signature' => substr( $signature, 0, 16 ) . '...',
            ) );
            return false;
        }

        return true;
    }

    /**
     * [v6.3.2] 업데이트 전 종합 보안 검사
     *
     * @param object $update_info 업데이트 정보 객체
     * @return array 검사 결과 ['passed' => bool, 'errors' => array]
     */
    public function pre_update_security_check( $update_info ) {
        $result = array(
            'passed' => true,
            'errors' => array(),
        );

        // 1. API URL 검증
        if ( ! $this->validate_api_url( $this->api_url ) ) {
            $result['passed']   = false;
            $result['errors'][] = 'api_url_invalid';
        }

        // 2. 응답 구조 검증
        if ( ! $this->validate_response_structure( $update_info ) ) {
            $result['passed']   = false;
            $result['errors'][] = 'response_structure_invalid';
        }

        // 3. 다운로드 URL 검증
        if ( isset( $update_info->download_link ) && ! $this->validate_package_url( $update_info->download_link ) ) {
            $result['passed']   = false;
            $result['errors'][] = 'download_url_invalid';
        }

        // 4. 버전 다운그레이드 방지
        if ( isset( $update_info->new_version ) && version_compare( $this->api_data['version'], $update_info->new_version, '>=' ) ) {
            $result['passed']   = false;
            $result['errors'][] = 'version_downgrade_attempt';
            $this->log_security_event( 'downgrade_attempt', array(
                'current_version' => $this->api_data['version'],
                'offered_version' => $update_info->new_version,
            ) );
        }

        if ( ! $result['passed'] ) {
            $this->log_security_event( 'security_check_failed', array(
                'errors' => $result['errors'],
            ) );
        }

        return $result;
    }

    /**
     * [v6.3.2] 보안 이벤트 로깅
     *
     * @param string $event 이벤트 타입
     * @param array  $data  추가 데이터
     */
    private function log_security_event( $event, $data = array() ) {
        // JJ_License_Security 클래스 사용 가능하면 해당 클래스로 로깅
        if ( class_exists( 'JJ_License_Security' ) && method_exists( 'JJ_License_Security', 'log_security_event' ) ) {
            JJ_License_Security::log_security_event( 'updater_' . $event, array_merge( array(
                'plugin' => $this->slug,
            ), $data ) );
        } else {
            // 폴백: 직접 로깅
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( sprintf(
                    '[JJ Plugin Updater] Security Event: %s | Plugin: %s | Data: %s',
                    $event,
                    $this->slug,
                    wp_json_encode( $data )
                ) );
            }
        }
    }

    /**
     * [v6.3.2] 도메인 화이트리스트에 추가 (필터를 통해)
     *
     * @param string $domain 추가할 도메인
     */
    public static function add_allowed_domain( $domain ) {
        if ( ! in_array( $domain, self::$allowed_domains, true ) ) {
            self::$allowed_domains[] = sanitize_text_field( $domain );
        }
    }

    /**
     * [v6.3.2] 현재 허용된 도메인 목록 반환
     *
     * @return array 도메인 목록
     */
    public static function get_allowed_domains() {
        return self::$allowed_domains;
    }
}

} // [v4.2.1] class_exists 체크 종료
