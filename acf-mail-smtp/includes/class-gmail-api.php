<?php
/**
 * Gmail API Manager
 *
 * Gmail API OAuth2 연동을 통한 이메일 발송
 * SMTP 없이 Gmail API를 직접 사용하여 더 안정적인 이메일 발송 제공
 *
 * @package ACF_Mail_SMTP
 * @version 2.1.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Mail_SMTP_Gmail_API {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * Gmail API 엔드포인트
     */
    const OAUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    const API_URL = 'https://gmail.googleapis.com/gmail/v1/users/me/messages/send';
    const USERINFO_URL = 'https://www.googleapis.com/oauth2/v2/userinfo';

    /**
     * 인스턴스 반환
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // OAuth 콜백 처리
        add_action( 'admin_init', array( $this, 'handle_oauth_callback' ) );

        // AJAX handlers
        add_action( 'wp_ajax_acf_mail_smtp_gmail_connect', array( $this, 'ajax_gmail_connect' ) );
        add_action( 'wp_ajax_acf_mail_smtp_gmail_disconnect', array( $this, 'ajax_gmail_disconnect' ) );
        add_action( 'wp_ajax_acf_mail_smtp_gmail_test', array( $this, 'ajax_gmail_test' ) );
        add_action( 'wp_ajax_acf_mail_smtp_save_gmail_credentials', array( $this, 'ajax_save_credentials' ) );

        // Gmail API 모드일 때 PHPMailer 오버라이드
        add_action( 'phpmailer_init', array( $this, 'maybe_use_gmail_api' ), 999 );
    }

    /**
     * Gmail API 설정 가져오기
     */
    public function get_settings() {
        return array(
            'enable_gmail_api' => get_option( 'acf_mail_smtp_enable_gmail_api', false ),
            'client_id' => get_option( 'acf_mail_smtp_gmail_client_id', '' ),
            'client_secret' => $this->decrypt_secret( get_option( 'acf_mail_smtp_gmail_client_secret', '' ) ),
            'access_token' => get_option( 'acf_mail_smtp_gmail_access_token', '' ),
            'refresh_token' => get_option( 'acf_mail_smtp_gmail_refresh_token', '' ),
            'token_expires' => get_option( 'acf_mail_smtp_gmail_token_expires', 0 ),
            'gmail_email' => get_option( 'acf_mail_smtp_gmail_email', '' ),
            'from_name' => get_option( 'acf_mail_smtp_from_name', get_option( 'blogname' ) ),
        );
    }

    /**
     * 비밀값 암호화
     */
    private function encrypt_secret( $value ) {
        if ( empty( $value ) ) {
            return '';
        }
        $key = wp_salt( 'auth' );
        $iv = substr( hash( 'sha256', wp_salt( 'secure_auth' ) ), 0, 16 );
        return base64_encode( openssl_encrypt( $value, 'AES-256-CBC', $key, 0, $iv ) );
    }

    /**
     * 비밀값 복호화
     */
    private function decrypt_secret( $encrypted ) {
        if ( empty( $encrypted ) ) {
            return '';
        }
        try {
            $key = wp_salt( 'auth' );
            $iv = substr( hash( 'sha256', wp_salt( 'secure_auth' ) ), 0, 16 );
            return openssl_decrypt( base64_decode( $encrypted ), 'AES-256-CBC', $key, 0, $iv );
        } catch ( Exception $e ) {
            return '';
        }
    }

    /**
     * OAuth 인증 URL 생성
     */
    public function get_auth_url() {
        $settings = $this->get_settings();

        if ( empty( $settings['client_id'] ) ) {
            return false;
        }

        $redirect_uri = admin_url( 'admin.php?page=acf-mail-smtp-smtp&gmail_oauth=callback' );
        $state = wp_create_nonce( 'acf_mail_smtp_gmail_oauth' );

        $params = array(
            'client_id' => $settings['client_id'],
            'redirect_uri' => $redirect_uri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/gmail.send https://www.googleapis.com/auth/userinfo.email',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        );

        return self::OAUTH_URL . '?' . http_build_query( $params );
    }

    /**
     * OAuth 콜백 처리
     */
    public function handle_oauth_callback() {
        if ( ! isset( $_GET['gmail_oauth'] ) || $_GET['gmail_oauth'] !== 'callback' ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-mail-smtp' ) );
        }

        // State 검증
        if ( ! isset( $_GET['state'] ) || ! wp_verify_nonce( $_GET['state'], 'acf_mail_smtp_gmail_oauth' ) ) {
            wp_die( __( '보안 검증에 실패했습니다.', 'acf-mail-smtp' ) );
        }

        // 에러 확인
        if ( isset( $_GET['error'] ) ) {
            $error = sanitize_text_field( $_GET['error'] );
            wp_redirect( admin_url( 'admin.php?page=acf-mail-smtp-smtp&gmail_error=' . urlencode( $error ) ) );
            exit;
        }

        // Authorization code 확인
        if ( ! isset( $_GET['code'] ) ) {
            wp_redirect( admin_url( 'admin.php?page=acf-mail-smtp-smtp&gmail_error=no_code' ) );
            exit;
        }

        $code = sanitize_text_field( $_GET['code'] );
        $result = $this->exchange_code_for_token( $code );

        if ( is_wp_error( $result ) ) {
            wp_redirect( admin_url( 'admin.php?page=acf-mail-smtp-smtp&gmail_error=' . urlencode( $result->get_error_message() ) ) );
            exit;
        }

        wp_redirect( admin_url( 'admin.php?page=acf-mail-smtp-smtp&gmail_connected=1' ) );
        exit;
    }

    /**
     * Authorization code를 Access Token으로 교환
     */
    private function exchange_code_for_token( $code ) {
        $settings = $this->get_settings();
        $redirect_uri = admin_url( 'admin.php?page=acf-mail-smtp-smtp&gmail_oauth=callback' );

        $response = wp_remote_post( self::TOKEN_URL, array(
            'timeout' => 30,
            'body' => array(
                'code' => $code,
                'client_id' => $settings['client_id'],
                'client_secret' => $settings['client_secret'],
                'redirect_uri' => $redirect_uri,
                'grant_type' => 'authorization_code',
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $body['error'] ) ) {
            return new WP_Error( 'gmail_api_error', $body['error_description'] ?? $body['error'] );
        }

        // 토큰 저장
        $this->save_tokens( $body );

        // 사용자 이메일 가져오기
        $user_info = $this->get_user_info( $body['access_token'] );
        if ( ! is_wp_error( $user_info ) && isset( $user_info['email'] ) ) {
            update_option( 'acf_mail_smtp_gmail_email', sanitize_email( $user_info['email'] ) );
        }

        return true;
    }

    /**
     * 토큰 저장
     */
    private function save_tokens( $token_data ) {
        if ( isset( $token_data['access_token'] ) ) {
            update_option( 'acf_mail_smtp_gmail_access_token', $this->encrypt_secret( $token_data['access_token'] ) );
        }
        if ( isset( $token_data['refresh_token'] ) ) {
            update_option( 'acf_mail_smtp_gmail_refresh_token', $this->encrypt_secret( $token_data['refresh_token'] ) );
        }
        if ( isset( $token_data['expires_in'] ) ) {
            update_option( 'acf_mail_smtp_gmail_token_expires', time() + intval( $token_data['expires_in'] ) - 60 );
        }
    }

    /**
     * Access Token 갱신
     */
    public function refresh_access_token() {
        $refresh_token = $this->decrypt_secret( get_option( 'acf_mail_smtp_gmail_refresh_token', '' ) );
        $settings = $this->get_settings();

        if ( empty( $refresh_token ) || empty( $settings['client_id'] ) || empty( $settings['client_secret'] ) ) {
            return new WP_Error( 'no_refresh_token', __( 'Refresh token이 없습니다. 다시 연결하세요.', 'acf-mail-smtp' ) );
        }

        $response = wp_remote_post( self::TOKEN_URL, array(
            'timeout' => 30,
            'body' => array(
                'client_id' => $settings['client_id'],
                'client_secret' => $settings['client_secret'],
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token',
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $body['error'] ) ) {
            return new WP_Error( 'token_refresh_failed', $body['error_description'] ?? $body['error'] );
        }

        $this->save_tokens( $body );
        return true;
    }

    /**
     * 유효한 Access Token 가져오기
     */
    public function get_valid_access_token() {
        $token_expires = get_option( 'acf_mail_smtp_gmail_token_expires', 0 );

        // 토큰 만료 시 갱신
        if ( time() >= $token_expires ) {
            $refresh_result = $this->refresh_access_token();
            if ( is_wp_error( $refresh_result ) ) {
                return $refresh_result;
            }
        }

        return $this->decrypt_secret( get_option( 'acf_mail_smtp_gmail_access_token', '' ) );
    }

    /**
     * 사용자 정보 가져오기
     */
    private function get_user_info( $access_token ) {
        $response = wp_remote_get( self::USERINFO_URL, array(
            'timeout' => 30,
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        return json_decode( wp_remote_retrieve_body( $response ), true );
    }

    /**
     * Gmail API 연결 상태 확인
     */
    public function is_connected() {
        $settings = $this->get_settings();
        return ! empty( $settings['gmail_email'] ) && ! empty( get_option( 'acf_mail_smtp_gmail_refresh_token', '' ) );
    }

    /**
     * Gmail API로 이메일 발송
     */
    public function send_email( $to, $subject, $message, $args = array() ) {
        $settings = $this->get_settings();

        if ( ! $settings['enable_gmail_api'] || ! $this->is_connected() ) {
            return new WP_Error( 'gmail_not_configured', __( 'Gmail API가 설정되지 않았습니다.', 'acf-mail-smtp' ) );
        }

        $access_token = $this->get_valid_access_token();
        if ( is_wp_error( $access_token ) ) {
            return $access_token;
        }

        $defaults = array(
            'from_name' => $settings['from_name'],
            'from_email' => $settings['gmail_email'],
            'headers' => array(),
            'attachments' => array(),
            'cc' => '',
            'bcc' => '',
            'reply_to' => '',
        );

        $args = wp_parse_args( $args, $defaults );

        // MIME 메시지 생성
        $raw_message = $this->create_raw_message( $to, $subject, $message, $args );

        // API 요청
        $response = wp_remote_post( self::API_URL, array(
            'timeout' => 30,
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode( array(
                'raw' => $raw_message,
            ) ),
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        $status_code = wp_remote_retrieve_response_code( $response );

        if ( $status_code >= 400 ) {
            $error_message = isset( $body['error']['message'] ) ? $body['error']['message'] : __( '이메일 발송에 실패했습니다.', 'acf-mail-smtp' );
            return new WP_Error( 'gmail_send_failed', $error_message );
        }

        return isset( $body['id'] ) ? $body['id'] : true;
    }

    /**
     * RAW MIME 메시지 생성
     */
    private function create_raw_message( $to, $subject, $body, $args ) {
        $boundary = uniqid( 'boundary_' );

        $headers = array();
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'From: ' . $this->encode_header( $args['from_name'] ) . ' <' . $args['from_email'] . '>';
        $headers[] = 'To: ' . $to;
        $headers[] = 'Subject: ' . $this->encode_header( $subject );
        $headers[] = 'Date: ' . date( 'r' );

        if ( ! empty( $args['cc'] ) ) {
            $headers[] = 'Cc: ' . $args['cc'];
        }
        if ( ! empty( $args['bcc'] ) ) {
            $headers[] = 'Bcc: ' . $args['bcc'];
        }
        if ( ! empty( $args['reply_to'] ) ) {
            $headers[] = 'Reply-To: ' . $args['reply_to'];
        }

        // 첨부파일이 있는 경우
        if ( ! empty( $args['attachments'] ) ) {
            $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

            $message = implode( "\r\n", $headers ) . "\r\n\r\n";
            $message .= '--' . $boundary . "\r\n";
            $message .= 'Content-Type: text/html; charset="UTF-8"' . "\r\n";
            $message .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
            $message .= chunk_split( base64_encode( $body ) ) . "\r\n";

            foreach ( $args['attachments'] as $attachment ) {
                if ( file_exists( $attachment ) ) {
                    $filename = basename( $attachment );
                    $content = file_get_contents( $attachment );
                    $mime_type = mime_content_type( $attachment ) ?: 'application/octet-stream';

                    $message .= '--' . $boundary . "\r\n";
                    $message .= 'Content-Type: ' . $mime_type . '; name="' . $filename . '"' . "\r\n";
                    $message .= 'Content-Disposition: attachment; filename="' . $filename . '"' . "\r\n";
                    $message .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
                    $message .= chunk_split( base64_encode( $content ) ) . "\r\n";
                }
            }

            $message .= '--' . $boundary . '--';
        } else {
            // 첨부파일 없음
            $headers[] = 'Content-Type: text/html; charset="UTF-8"';
            $headers[] = 'Content-Transfer-Encoding: base64';

            $message = implode( "\r\n", $headers ) . "\r\n\r\n";
            $message .= chunk_split( base64_encode( $body ) );
        }

        // URL-safe Base64 인코딩
        return rtrim( strtr( base64_encode( $message ), '+/', '-_' ), '=' );
    }

    /**
     * 헤더 인코딩 (한글 지원)
     */
    private function encode_header( $string ) {
        if ( preg_match( '/[^\x20-\x7E]/', $string ) ) {
            return '=?UTF-8?B?' . base64_encode( $string ) . '?=';
        }
        return $string;
    }

    /**
     * Gmail API 모드일 때 wp_mail 오버라이드
     */
    public function maybe_use_gmail_api( $phpmailer ) {
        $settings = $this->get_settings();

        if ( ! $settings['enable_gmail_api'] || ! $this->is_connected() ) {
            return;
        }

        // Gmail API 모드에서는 SMTP 설정을 Gmail SMTP로 변경
        // OAuth XOAUTH2 인증 사용
        $access_token = $this->get_valid_access_token();
        if ( is_wp_error( $access_token ) ) {
            return;
        }

        $phpmailer->isSMTP();
        $phpmailer->Host = 'smtp.gmail.com';
        $phpmailer->Port = 587;
        $phpmailer->SMTPSecure = 'tls';
        $phpmailer->SMTPAuth = true;
        $phpmailer->AuthType = 'XOAUTH2';
        $phpmailer->setOAuth(
            new class( $settings['gmail_email'], $access_token ) extends \PHPMailer\PHPMailer\OAuth {
                private $email;
                private $token;

                public function __construct( $email, $token ) {
                    $this->email = $email;
                    $this->token = $token;
                }

                public function getOauth64() {
                    return base64_encode(
                        'user=' . $this->email . "\x01" .
                        'auth=Bearer ' . $this->token . "\x01\x01"
                    );
                }
            }
        );

        $phpmailer->From = $settings['gmail_email'];
        $phpmailer->FromName = $settings['from_name'];
    }

    /**
     * Gmail 연결 (AJAX)
     */
    public function ajax_gmail_connect() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $auth_url = $this->get_auth_url();

        if ( ! $auth_url ) {
            wp_send_json_error( array( 'message' => __( 'Client ID가 설정되지 않았습니다.', 'acf-mail-smtp' ) ) );
        }

        wp_send_json_success( array( 'auth_url' => $auth_url ) );
    }

    /**
     * Gmail 연결 해제 (AJAX)
     */
    public function ajax_gmail_disconnect() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        delete_option( 'acf_mail_smtp_gmail_access_token' );
        delete_option( 'acf_mail_smtp_gmail_refresh_token' );
        delete_option( 'acf_mail_smtp_gmail_token_expires' );
        delete_option( 'acf_mail_smtp_gmail_email' );

        wp_send_json_success( array( 'message' => __( 'Gmail 연결이 해제되었습니다.', 'acf-mail-smtp' ) ) );
    }

    /**
     * Gmail 테스트 (AJAX)
     */
    public function ajax_gmail_test() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $to_email = isset( $_POST['to_email'] ) ? sanitize_email( $_POST['to_email'] ) : get_option( 'admin_email' );

        if ( ! is_email( $to_email ) ) {
            wp_send_json_error( array( 'message' => __( '유효한 이메일 주소를 입력하세요.', 'acf-mail-smtp' ) ) );
        }

        $subject = __( '[ACF Mail SMTP] Gmail API 테스트', 'acf-mail-smtp' );
        $message = '<h2>Gmail API 테스트 이메일</h2>';
        $message .= '<p>' . __( '이것은 Gmail API 연동 테스트 이메일입니다.', 'acf-mail-smtp' ) . '</p>';
        $message .= '<p><strong>' . sprintf( __( '발송 시간: %s', 'acf-mail-smtp' ), current_time( 'mysql' ) ) . '</strong></p>';
        $message .= '<p style="color: #16a34a;">Gmail API 연동이 정상적으로 작동합니다.</p>';

        $result = $this->send_email( $to_email, $subject, $message );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Gmail API 테스트 이메일이 발송되었습니다.', 'acf-mail-smtp' ) ) );
    }

    /**
     * Gmail 자격증명 저장 (AJAX)
     */
    public function ajax_save_credentials() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $client_id = isset( $_POST['client_id'] ) ? sanitize_text_field( $_POST['client_id'] ) : '';
        $client_secret = isset( $_POST['client_secret'] ) ? sanitize_text_field( $_POST['client_secret'] ) : '';
        $enable_gmail_api = isset( $_POST['enable_gmail_api'] ) && $_POST['enable_gmail_api'] === 'true';

        update_option( 'acf_mail_smtp_gmail_client_id', $client_id );
        update_option( 'acf_mail_smtp_gmail_client_secret', $this->encrypt_secret( $client_secret ) );
        update_option( 'acf_mail_smtp_enable_gmail_api', $enable_gmail_api );

        wp_send_json_success( array( 'message' => __( 'Gmail API 설정이 저장되었습니다.', 'acf-mail-smtp' ) ) );
    }
}
