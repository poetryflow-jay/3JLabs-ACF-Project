<?php
/**
 * JJ Plugin Updater - WordPress 업데이트 시스템 통합
 * 
 * 3J Labs ACF CSS 플러그인 패밀리의 자동 업데이트를 WordPress 표준 업데이트 시스템에 통합합니다.
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
 * @version 1.0.0
 * @since 4.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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
            'sslverify' => true,
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
}
