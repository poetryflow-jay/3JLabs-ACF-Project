<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 10] Mobile Bridge - 모바일 앱 연동 모듈
 * 
 * WordPress 모바일 앱 및 외부 모바일 클라이언트와의 통합을 위한 브릿지 모듈입니다.
 * - REST API 확장 (모바일 친화적 엔드포인트)
 * - Push 알림 기반 설정 동기화
 * - JWT 기반 모바일 인증
 * 
 * @since 6.2.0
 */
final class JJ_Mobile_Bridge {

    private static $instance = null;
    private $namespace = 'jj-style-guide/v1';
    private $options = array();

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 모바일 브릿지 활성화 여부 확인
        $this->options = get_option( 'jj_mobile_bridge_options', array() );
        
        if ( ! $this->is_enabled() ) {
            return;
        }

        add_action( 'rest_api_init', array( $this, 'register_mobile_routes' ) );
        add_action( 'jj_style_guide_settings_updated', array( $this, 'notify_mobile_clients' ), 10, 2 );
    }

    /**
     * 모바일 브릿지 활성화 여부
     */
    public function is_enabled() {
        return isset( $this->options['enabled'] ) && $this->options['enabled'];
    }

    /**
     * 모바일 전용 REST 라우트 등록
     */
    public function register_mobile_routes() {
        // 1) 모바일용 간소화된 설정 조회
        register_rest_route( $this->namespace, '/mobile/settings', array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_mobile_settings' ),
                'permission_callback' => array( $this, 'mobile_permission_check' ),
            ),
        ) );

        // 2) 모바일에서 간단한 색상/폰트 변경
        register_rest_route( $this->namespace, '/mobile/quick-update', array(
            array(
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'quick_update_settings' ),
                'permission_callback' => array( $this, 'mobile_permission_check' ),
            ),
        ) );

        // 3) 푸시 알림 토큰 등록
        register_rest_route( $this->namespace, '/mobile/register-device', array(
            array(
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'register_device' ),
                'permission_callback' => array( $this, 'mobile_permission_check' ),
            ),
        ) );

        // 4) 연결 상태 확인 (헬스 체크)
        register_rest_route( $this->namespace, '/mobile/ping', array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array( $this, 'ping' ),
                'permission_callback' => '__return_true', // 공개
            ),
        ) );
    }

    /**
     * 모바일 권한 체크
     */
    public function mobile_permission_check( \WP_REST_Request $request ) {
        // 기본: 관리자 권한 필요
        // TODO: JWT 토큰 기반 인증 구현 시 이곳에서 토큰 검증
        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }

        // JWT 토큰 체크 (향후 구현)
        $auth_header = $request->get_header( 'Authorization' );
        if ( $auth_header && strpos( $auth_header, 'Bearer ' ) === 0 ) {
            $token = substr( $auth_header, 7 );
            return $this->validate_jwt_token( $token );
        }

        return false;
    }

    /**
     * JWT 토큰 검증 (스켈레톤)
     */
    private function validate_jwt_token( $token ) {
        // TODO: 실제 JWT 검증 로직 구현
        // 현재는 저장된 토큰과 단순 비교
        $stored_tokens = get_option( 'jj_mobile_tokens', array() );
        return in_array( $token, $stored_tokens, true );
    }

    /**
     * 모바일용 간소화된 설정 조회
     */
    public function get_mobile_settings( \WP_REST_Request $request ) {
        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $full_options = get_option( $key, array() );

        // 모바일에서 필요한 핵심 정보만 추출
        $mobile_data = array(
            'version' => defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '0.0.0',
            'site_url' => get_site_url(),
            'site_name' => get_bloginfo( 'name' ),
            'palettes' => isset( $full_options['palettes'] ) ? $full_options['palettes'] : array(),
            'typography' => isset( $full_options['typography'] ) ? $full_options['typography'] : array(),
            'buttons' => isset( $full_options['buttons'] ) ? $full_options['buttons'] : array(),
            'last_updated' => get_option( 'jj_style_guide_last_updated', '' ),
        );

        return new \WP_REST_Response( $mobile_data, 200 );
    }

    /**
     * 모바일에서 간단한 설정 변경
     */
    public function quick_update_settings( \WP_REST_Request $request ) {
        $updates = $request->get_json_params();

        if ( empty( $updates ) || ! is_array( $updates ) ) {
            return new \WP_REST_Response( array( 'message' => 'Invalid data' ), 400 );
        }

        // 허용된 필드만 업데이트
        $allowed_fields = array( 'palettes', 'typography', 'buttons' );
        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $current = get_option( $key, array() );

        foreach ( $updates as $field => $value ) {
            if ( in_array( $field, $allowed_fields, true ) && is_array( $value ) ) {
                $current[ $field ] = $this->sanitize_mobile_input( $value );
            }
        }

        update_option( $key, $current );
        update_option( 'jj_style_guide_last_updated', current_time( 'mysql' ) );

        do_action( 'jj_style_guide_settings_updated', $current, 'mobile_bridge' );

        return new \WP_REST_Response( array( 'message' => 'Updated successfully' ), 200 );
    }

    /**
     * 모바일 입력값 새니타이즈
     */
    private function sanitize_mobile_input( $input ) {
        $output = array();
        foreach ( (array) $input as $k => $v ) {
            $safe_k = sanitize_key( (string) $k );
            if ( is_array( $v ) ) {
                $output[ $safe_k ] = $this->sanitize_mobile_input( $v );
            } elseif ( strpos( $safe_k, 'color' ) !== false ) {
                $output[ $safe_k ] = sanitize_hex_color( (string) $v ) ?: '';
            } else {
                $output[ $safe_k ] = sanitize_text_field( (string) $v );
            }
        }
        return $output;
    }

    /**
     * 푸시 알림용 디바이스 토큰 등록
     */
    public function register_device( \WP_REST_Request $request ) {
        $device_token = sanitize_text_field( $request->get_param( 'device_token' ) );
        $platform = sanitize_text_field( $request->get_param( 'platform' ) ); // 'ios' or 'android'

        if ( empty( $device_token ) ) {
            return new \WP_REST_Response( array( 'message' => 'Device token required' ), 400 );
        }

        $devices = get_option( 'jj_mobile_devices', array() );
        $devices[] = array(
            'token'     => $device_token,
            'platform'  => $platform,
            'user_id'   => get_current_user_id(),
            'registered' => current_time( 'mysql' ),
        );

        // 중복 제거
        $devices = array_values( array_unique( $devices, SORT_REGULAR ) );

        update_option( 'jj_mobile_devices', $devices );

        return new \WP_REST_Response( array( 'message' => 'Device registered' ), 200 );
    }

    /**
     * 핑/헬스 체크
     */
    public function ping( \WP_REST_Request $request ) {
        return new \WP_REST_Response( array(
            'status'  => 'ok',
            'version' => defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '0.0.0',
            'mobile_bridge' => $this->is_enabled() ? 'enabled' : 'disabled',
            'timestamp' => current_time( 'c' ),
        ), 200 );
    }

    /**
     * 설정 변경 시 모바일 클라이언트에 알림 (푸시)
     */
    public function notify_mobile_clients( $new_settings, $source ) {
        if ( $source === 'mobile_bridge' ) {
            // 모바일에서 온 변경은 다시 모바일로 알리지 않음
            return;
        }

        $devices = get_option( 'jj_mobile_devices', array() );
        if ( empty( $devices ) ) {
            return;
        }

        // TODO: 실제 푸시 알림 서비스 연동 (Firebase, APNs 등)
        // 현재는 로그만 기록
        if ( function_exists( 'error_log' ) ) {
            error_log( '[JJ Mobile Bridge] Settings updated, would notify ' . count( $devices ) . ' devices.' );
        }
    }
}

