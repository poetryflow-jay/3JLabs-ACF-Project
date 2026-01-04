<?php
/**
 * SMTP Manager
 * 
 * SMTP 설정 및 이메일 발송을 담당하는 클래스
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Mail_SMTP_SMTP_Manager {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

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
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // Override WordPress mail function
        add_filter( 'wp_mail', array( $this, 'override_wp_mail' ), 1 );
        add_action( 'phpmailer_init', array( $this, 'configure_phpmailer' ) );

        // AJAX handlers
        add_action( 'wp_ajax_acf_mail_smtp_test_smtp', array( $this, 'ajax_test_smtp' ) );
        add_action( 'wp_ajax_acf_mail_smtp_save_smtp', array( $this, 'ajax_save_smtp' ) );
    }

    /**
     * SMTP 설정 가져오기
     */
    public function get_settings() {
        return array(
            'enable_smtp' => get_option( 'acf_mail_smtp_enable_smtp', false ),
            'smtp_host' => get_option( 'acf_mail_smtp_smtp_host', '' ),
            'smtp_port' => get_option( 'acf_mail_smtp_smtp_port', 587 ),
            'smtp_encryption' => get_option( 'acf_mail_smtp_smtp_encryption', 'tls' ),
            'smtp_auth' => get_option( 'acf_mail_smtp_smtp_auth', true ),
            'smtp_username' => get_option( 'acf_mail_smtp_smtp_username', '' ),
            'smtp_password' => get_option( 'acf_mail_smtp_smtp_password', '' ),
            'from_email' => get_option( 'acf_mail_smtp_from_email', get_option( 'admin_email' ) ),
            'from_name' => get_option( 'acf_mail_smtp_from_name', get_option( 'blogname' ) ),
        );
    }

    /**
     * SMTP 설정 저장
     */
    public function save_settings( $settings ) {
        $allowed_keys = array(
            'enable_smtp',
            'smtp_host',
            'smtp_port',
            'smtp_encryption',
            'smtp_auth',
            'smtp_username',
            'smtp_password',
            'from_email',
            'from_name',
        );

        foreach ( $allowed_keys as $key ) {
            if ( isset( $settings[ $key ] ) ) {
                $value = $settings[ $key ];

                // Sanitize based on type
                if ( $key === 'enable_smtp' || $key === 'smtp_auth' ) {
                    $value = (bool) $value;
                } elseif ( $key === 'smtp_port' ) {
                    $value = intval( $value );
                } elseif ( $key === 'smtp_password' ) {
                    // Encrypt password
                    $value = $this->encrypt_password( $value );
                } else {
                    $value = sanitize_text_field( $value );
                }

                update_option( 'acf_mail_smtp_' . $key, $value );
            }
        }

        return true;
    }

    /**
     * 비밀번호 암호화
     */
    private function encrypt_password( $password ) {
        if ( empty( $password ) ) {
            return '';
        }

        // Use WordPress salts for encryption
        $key = wp_salt();
        $encrypted = base64_encode( openssl_encrypt( $password, 'AES-256-CBC', $key, 0, substr( $key, 0, 16 ) ) );
        return $encrypted;
    }

    /**
     * 비밀번호 복호화
     */
    private function decrypt_password( $encrypted ) {
        if ( empty( $encrypted ) ) {
            return '';
        }

        try {
            $key = wp_salt();
            $decrypted = openssl_decrypt( base64_decode( $encrypted ), 'AES-256-CBC', $key, 0, substr( $key, 0, 16 ) );
            return $decrypted;
        } catch ( Exception $e ) {
            return '';
        }
    }

    /**
     * PHPMailer 설정
     */
    public function configure_phpmailer( $phpmailer ) {
        $settings = $this->get_settings();

        if ( ! $settings['enable_smtp'] ) {
            return;
        }

        $phpmailer->isSMTP();
        $phpmailer->Host = $settings['smtp_host'];
        $phpmailer->Port = $settings['smtp_port'];
        $phpmailer->SMTPSecure = $settings['smtp_encryption'];
        $phpmailer->SMTPAuth = $settings['smtp_auth'];

        if ( $settings['smtp_auth'] ) {
            $phpmailer->Username = $settings['smtp_username'];
            $phpmailer->Password = $this->decrypt_password( $settings['smtp_password'] );
        }

        $phpmailer->From = $settings['from_email'];
        $phpmailer->FromName = $settings['from_name'];
    }

    /**
     * 이메일 발송
     */
    public function send_email( $to, $subject, $message, $args = array() ) {
        $settings = $this->get_settings();

        $defaults = array(
            'from_email' => $settings['from_email'],
            'from_name' => $settings['from_name'],
            'headers' => array(),
            'attachments' => array(),
            'submission_id' => 0,
            'form_id' => 0,
        );

        $args = wp_parse_args( $args, $defaults );

        // Build headers
        $headers = array();
        $headers[] = 'From: ' . $args['from_name'] . ' <' . $args['from_email'] . '>';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';

        if ( ! empty( $args['headers'] ) && is_array( $args['headers'] ) ) {
            $headers = array_merge( $headers, $args['headers'] );
        }

        // Send email
        $result = wp_mail( $to, $subject, $message, $headers, $args['attachments'] );

        // Log email
        if ( get_option( 'acf_mail_smtp_enable_email_logs', true ) ) {
            $this->log_email( $to, $args['from_email'], $subject, $message, $headers, $result, $args );
        }

        return $result;
    }

    /**
     * 이메일 로그 저장
     */
    private function log_email( $to, $from, $subject, $message, $headers, $status, $args = array() ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_emails';

        $wpdb->insert(
            $table,
            array(
                'submission_id' => isset( $args['submission_id'] ) ? intval( $args['submission_id'] ) : 0,
                'form_id' => isset( $args['form_id'] ) ? intval( $args['form_id'] ) : 0,
                'to_email' => sanitize_email( $to ),
                'from_email' => sanitize_email( $from ),
                'subject' => sanitize_text_field( $subject ),
                'message' => $message,
                'headers' => wp_json_encode( $headers ),
                'status' => $status ? 'sent' : 'failed',
                'sent_at' => $status ? current_time( 'mysql' ) : null,
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
        );
    }

    /**
     * SMTP 테스트
     */
    public function test_smtp( $to_email = null ) {
        if ( ! $to_email ) {
            $to_email = get_option( 'admin_email' );
        }

        $subject = __( '[ACF Mail SMTP] SMTP 테스트', 'acf-mail-smtp' );
        $message = '<p>' . __( '이것은 SMTP 설정 테스트 이메일입니다.', 'acf-mail-smtp' ) . '</p>';
        $message .= '<p>' . sprintf( __( '발송 시간: %s', 'acf-mail-smtp' ), current_time( 'mysql' ) ) . '</p>';

        $result = $this->send_email( $to_email, $subject, $message );

        return $result;
    }

    /**
     * SMTP 설정 저장 (AJAX)
     */
    public function ajax_save_smtp() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $settings = isset( $_POST['settings'] ) ? $_POST['settings'] : array();

        $result = $this->save_settings( $settings );

        if ( $result ) {
            wp_send_json_success( array( 'message' => __( 'SMTP 설정이 저장되었습니다.', 'acf-mail-smtp' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'SMTP 설정 저장에 실패했습니다.', 'acf-mail-smtp' ) ) );
        }
    }

    /**
     * SMTP 테스트 (AJAX)
     */
    public function ajax_test_smtp() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $to_email = isset( $_POST['to_email'] ) ? sanitize_email( $_POST['to_email'] ) : get_option( 'admin_email' );

        if ( ! is_email( $to_email ) ) {
            wp_send_json_error( array( 'message' => __( '유효한 이메일 주소를 입력하세요.', 'acf-mail-smtp' ) ) );
        }

        $result = $this->test_smtp( $to_email );

        if ( $result ) {
            wp_send_json_success( array( 'message' => __( '테스트 이메일이 발송되었습니다.', 'acf-mail-smtp' ) ) );
        } else {
            global $phpmailer;
            $error_message = '';
            if ( isset( $phpmailer ) && isset( $phpmailer->ErrorInfo ) ) {
                $error_message = $phpmailer->ErrorInfo;
            }
            wp_send_json_error( array( 'message' => __( '테스트 이메일 발송에 실패했습니다.', 'acf-mail-smtp' ) . ( $error_message ? ': ' . $error_message : '' ) ) );
        }
    }
}
