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
        // [v2.2.0] 로그 관련 AJAX
        add_action( 'wp_ajax_acf_mail_smtp_get_email', array( $this, 'ajax_get_email' ) );
        add_action( 'wp_ajax_acf_mail_smtp_resend_email', array( $this, 'ajax_resend_email' ) );
        add_action( 'wp_ajax_acf_mail_smtp_delete_email', array( $this, 'ajax_delete_email' ) );
        add_action( 'wp_ajax_acf_mail_smtp_delete_old_logs', array( $this, 'ajax_delete_old_logs' ) );
        add_action( 'wp_ajax_acf_mail_smtp_export_logs', array( $this, 'ajax_export_logs' ) );
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
     * [v2.2.0] error_message 저장 추가
     */
    private function log_email( $to, $from, $subject, $message, $headers, $status, $args = array() ) {
        global $wpdb, $phpmailer;
        $table = $wpdb->prefix . 'acf_mail_smtp_emails';

        $error_message = '';
        if ( ! $status && isset( $phpmailer ) && isset( $phpmailer->ErrorInfo ) && ! empty( $phpmailer->ErrorInfo ) ) {
            $error_message = sanitize_text_field( $phpmailer->ErrorInfo );
        }

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
                'error_message' => $error_message,
                'sent_at' => $status ? current_time( 'mysql' ) : null,
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
        );
    }

    /**
     * [v2.2.0] 이메일 재발송
     */
    public function resend_email( $email_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_emails';

        $email = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $email_id ), ARRAY_A );
        if ( ! $email ) {
            return new WP_Error( 'not_found', __( '이메일을 찾을 수 없습니다.', 'acf-mail-smtp' ) );
        }

        $headers = json_decode( $email['headers'], true );
        if ( ! is_array( $headers ) ) {
            $headers = array();
        }

        $result = wp_mail( $email['to_email'], $email['subject'], $email['message'], $headers );

        global $phpmailer;
        $wpdb->update(
            $table,
            array(
                'status' => $result ? 'sent' : 'failed',
                'error_message' => $result ? '' : ( isset( $phpmailer->ErrorInfo ) ? $phpmailer->ErrorInfo : '' ),
                'sent_at' => $result ? current_time( 'mysql' ) : null,
            ),
            array( 'id' => $email_id ),
            array( '%s', '%s', '%s' ),
            array( '%d' )
        );

        return $result;
    }

    /**
     * [v2.2.0] 이메일 로그 삭제
     */
    public function delete_email_log( $email_id ) {
        global $wpdb;
        return $wpdb->delete( $wpdb->prefix . 'acf_mail_smtp_emails', array( 'id' => $email_id ), array( '%d' ) );
    }

    /**
     * [v2.2.0] 오래된 로그 일괄 삭제
     */
    public function delete_old_logs( $days = 30 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_emails';
        return $wpdb->query( $wpdb->prepare(
            "DELETE FROM $table WHERE created_at < %s",
            gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) )
        ) );
    }

    /**
     * [v2.2.0] 이메일 상세 정보
     */
    public function get_email_details( $email_id ) {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}acf_mail_smtp_emails WHERE id = %d",
            $email_id
        ), ARRAY_A );
    }

    /**
     * [v2.2.0] CSV 내보내기용 데이터
     */
    public function get_emails_for_export( $status = '', $limit = 1000 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_emails';

        if ( ! empty( $status ) ) {
            return $wpdb->get_results( $wpdb->prepare(
                "SELECT id, to_email, from_email, subject, status, error_message, sent_at, created_at FROM $table WHERE status = %s ORDER BY created_at DESC LIMIT %d",
                $status, $limit
            ), ARRAY_A );
        }

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT id, to_email, from_email, subject, status, error_message, sent_at, created_at FROM $table ORDER BY created_at DESC LIMIT %d",
            $limit
        ), ARRAY_A );
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

    /**
     * [v2.2.0] 이메일 상세 정보 (AJAX)
     */
    public function ajax_get_email() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $email_id = isset( $_POST['email_id'] ) ? intval( $_POST['email_id'] ) : 0;
        if ( ! $email_id ) {
            wp_send_json_error( array( 'message' => __( '이메일 ID가 필요합니다.', 'acf-mail-smtp' ) ) );
        }

        $email = $this->get_email_details( $email_id );
        if ( ! $email ) {
            wp_send_json_error( array( 'message' => __( '이메일을 찾을 수 없습니다.', 'acf-mail-smtp' ) ) );
        }

        wp_send_json_success( array( 'email' => $email ) );
    }

    /**
     * [v2.2.0] 이메일 재발송 (AJAX)
     */
    public function ajax_resend_email() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $email_id = isset( $_POST['email_id'] ) ? intval( $_POST['email_id'] ) : 0;
        if ( ! $email_id ) {
            wp_send_json_error( array( 'message' => __( '이메일 ID가 필요합니다.', 'acf-mail-smtp' ) ) );
        }

        $result = $this->resend_email( $email_id );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        if ( $result ) {
            wp_send_json_success( array( 'message' => __( '이메일이 재발송되었습니다.', 'acf-mail-smtp' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( '이메일 재발송에 실패했습니다.', 'acf-mail-smtp' ) ) );
        }
    }

    /**
     * [v2.2.0] 이메일 삭제 (AJAX)
     */
    public function ajax_delete_email() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $email_id = isset( $_POST['email_id'] ) ? intval( $_POST['email_id'] ) : 0;
        if ( ! $email_id ) {
            wp_send_json_error( array( 'message' => __( '이메일 ID가 필요합니다.', 'acf-mail-smtp' ) ) );
        }

        $result = $this->delete_email_log( $email_id );

        if ( $result ) {
            wp_send_json_success( array( 'message' => __( '로그가 삭제되었습니다.', 'acf-mail-smtp' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( '로그 삭제에 실패했습니다.', 'acf-mail-smtp' ) ) );
        }
    }

    /**
     * [v2.2.0] 오래된 로그 삭제 (AJAX)
     */
    public function ajax_delete_old_logs() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $days = isset( $_POST['days'] ) ? intval( $_POST['days'] ) : 30;
        $deleted = $this->delete_old_logs( $days );

        wp_send_json_success( array(
            'message' => sprintf( __( '%d개의 로그가 삭제되었습니다.', 'acf-mail-smtp' ), $deleted ),
            'deleted' => $deleted,
        ) );
    }

    /**
     * [v2.2.0] CSV 내보내기 (AJAX)
     */
    public function ajax_export_logs() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
        $emails = $this->get_emails_for_export( $status );

        if ( empty( $emails ) ) {
            wp_send_json_error( array( 'message' => __( '내보낼 로그가 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $csv_data = array();
        $csv_data[] = array( 'ID', 'To', 'From', 'Subject', 'Status', 'Error', 'Sent At', 'Created At' );

        foreach ( $emails as $email ) {
            $csv_data[] = array(
                $email['id'],
                $email['to_email'],
                $email['from_email'],
                $email['subject'],
                $email['status'],
                $email['error_message'],
                $email['sent_at'],
                $email['created_at'],
            );
        }

        wp_send_json_success( array( 'csv' => $csv_data ) );
    }
}
