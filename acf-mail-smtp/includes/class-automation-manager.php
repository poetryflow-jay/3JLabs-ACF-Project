<?php
/**
 * Automation Manager
 * 
 * 폼 제출 후 자동화 작업을 처리하는 클래스
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Mail_SMTP_Automation_Manager {

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
        // AJAX handlers
        add_action( 'wp_ajax_acf_mail_smtp_save_automation', array( $this, 'ajax_save_automation' ) );
        add_action( 'wp_ajax_acf_mail_smtp_delete_automation', array( $this, 'ajax_delete_automation' ) );
        add_action( 'wp_ajax_acf_mail_smtp_get_automation', array( $this, 'ajax_get_automation' ) );
        add_action( 'wp_ajax_acf_mail_smtp_get_automations', array( $this, 'ajax_get_automations' ) );
    }

    /**
     * 자동화 규칙 생성
     */
    public function create_automation( $form_id, $name, $trigger_type, $conditions, $actions ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_automation';

        $result = $wpdb->insert(
            $table,
            array(
                'form_id' => intval( $form_id ),
                'name' => sanitize_text_field( $name ),
                'trigger_type' => sanitize_text_field( $trigger_type ),
                'conditions' => wp_json_encode( $conditions ),
                'actions' => wp_json_encode( $actions ),
                'status' => 'active',
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%s', '%s' )
        );

        if ( $result ) {
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * 자동화 규칙 업데이트
     */
    public function update_automation( $automation_id, $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_automation';

        $update_data = array();
        $update_format = array();

        if ( isset( $data['name'] ) ) {
            $update_data['name'] = sanitize_text_field( $data['name'] );
            $update_format[] = '%s';
        }

        if ( isset( $data['trigger_type'] ) ) {
            $update_data['trigger_type'] = sanitize_text_field( $data['trigger_type'] );
            $update_format[] = '%s';
        }

        if ( isset( $data['conditions'] ) ) {
            $update_data['conditions'] = wp_json_encode( $data['conditions'] );
            $update_format[] = '%s';
        }

        if ( isset( $data['actions'] ) ) {
            $update_data['actions'] = wp_json_encode( $data['actions'] );
            $update_format[] = '%s';
        }

        if ( isset( $data['status'] ) ) {
            $update_data['status'] = sanitize_text_field( $data['status'] );
            $update_format[] = '%s';
        }

        $update_data['updated_at'] = current_time( 'mysql' );
        $update_format[] = '%s';

        if ( empty( $update_data ) ) {
            return false;
        }

        $result = $wpdb->update(
            $table,
            $update_data,
            array( 'id' => intval( $automation_id ) ),
            $update_format,
            array( '%d' )
        );

        return $result !== false;
    }

    /**
     * 자동화 규칙 가져오기
     */
    public function get_automation( $automation_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_automation';

        $automation = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            intval( $automation_id )
        ), ARRAY_A );

        if ( $automation ) {
            $automation['conditions'] = json_decode( $automation['conditions'], true );
            $automation['actions'] = json_decode( $automation['actions'], true );
        }

        return $automation;
    }

    /**
     * 자동화 규칙 목록 가져오기
     */
    public function get_automations( $args = array() ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_automation';

        $defaults = array(
            'form_id' => 0,
            'status' => 'active',
            'limit' => 100,
            'offset' => 0,
        );

        $args = wp_parse_args( $args, $defaults );

        $where = array();
        $where_values = array();

        if ( ! empty( $args['form_id'] ) ) {
            $where[] = 'form_id = %d';
            $where_values[] = intval( $args['form_id'] );
        }

        if ( ! empty( $args['status'] ) ) {
            $where[] = 'status = %s';
            $where_values[] = $args['status'];
        }

        $where_clause = ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '';

        $limit = intval( $args['limit'] );
        $offset = intval( $args['offset'] );

        $query = "SELECT * FROM $table $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d";
        $query_values = array_merge( $where_values, array( $limit, $offset ) );

        if ( ! empty( $where_values ) ) {
            $automations = $wpdb->get_results( $wpdb->prepare( $query, $where_values ), ARRAY_A );
        } else {
            $automations = $wpdb->get_results( $wpdb->prepare( $query, $limit, $offset ), ARRAY_A );
        }

        // Decode JSON
        foreach ( $automations as &$automation ) {
            $automation['conditions'] = json_decode( $automation['conditions'], true );
            $automation['actions'] = json_decode( $automation['actions'], true );
        }

        return $automations;
    }

    /**
     * 자동화 규칙 삭제
     */
    public function delete_automation( $automation_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_automation';

        $result = $wpdb->delete(
            $table,
            array( 'id' => intval( $automation_id ) ),
            array( '%d' )
        );

        return $result !== false;
    }

    /**
     * 자동화 트리거
     */
    public function trigger_automation( $form_id, $submission_id, $submission_data ) {
        $automations = $this->get_automations( array(
            'form_id' => $form_id,
            'status' => 'active',
        ) );

        foreach ( $automations as $automation ) {
            if ( $automation['trigger_type'] === 'form_submit' ) {
                // Check conditions
                if ( $this->check_conditions( $automation['conditions'], $submission_data ) ) {
                    // Execute actions
                    $this->execute_actions( $automation['actions'], $submission_id, $submission_data );
                }
            }
        }
    }

    /**
     * 조건 확인
     */
    private function check_conditions( $conditions, $submission_data ) {
        if ( empty( $conditions ) || ! is_array( $conditions ) ) {
            return true; // No conditions means always true
        }

        foreach ( $conditions as $condition ) {
            $field_id = isset( $condition['field'] ) ? $condition['field'] : '';
            $operator = isset( $condition['operator'] ) ? $condition['operator'] : 'equals';
            $value = isset( $condition['value'] ) ? $condition['value'] : '';
            $field_value = isset( $submission_data[ $field_id ] ) ? $submission_data[ $field_id ] : '';

            $result = $this->evaluate_condition( $field_value, $operator, $value );

            if ( ! $result ) {
                return false;
            }
        }

        return true;
    }

    /**
     * 조건 평가
     */
    private function evaluate_condition( $field_value, $operator, $value ) {
        switch ( $operator ) {
            case 'equals':
                return $field_value == $value;
            case 'not_equals':
                return $field_value != $value;
            case 'contains':
                return strpos( $field_value, $value ) !== false;
            case 'not_contains':
                return strpos( $field_value, $value ) === false;
            case 'greater_than':
                return floatval( $field_value ) > floatval( $value );
            case 'less_than':
                return floatval( $field_value ) < floatval( $value );
            case 'is_empty':
                return empty( $field_value );
            case 'is_not_empty':
                return ! empty( $field_value );
            default:
                return true;
        }
    }

    /**
     * 액션 실행
     */
    private function execute_actions( $actions, $submission_id, $submission_data ) {
        if ( empty( $actions ) || ! is_array( $actions ) ) {
            return;
        }

        foreach ( $actions as $action ) {
            $action_type = isset( $action['type'] ) ? $action['type'] : '';

            switch ( $action_type ) {
                case 'send_email':
                    $this->action_send_email( $action, $submission_id, $submission_data );
                    break;
                case 'redirect':
                    $this->action_redirect( $action, $submission_id, $submission_data );
                    break;
                case 'webhook':
                    $this->action_webhook( $action, $submission_id, $submission_data );
                    break;
            }
        }
    }

    /**
     * 이메일 발송 액션
     */
    private function action_send_email( $action, $submission_id, $submission_data ) {
        if ( ! class_exists( 'ACF_Mail_SMTP_SMTP_Manager' ) ) {
            return;
        }

        $smtp = ACF_Mail_SMTP_SMTP_Manager::get_instance();

        $to = isset( $action['to'] ) ? $action['to'] : get_option( 'admin_email' );
        $subject = isset( $action['subject'] ) ? $action['subject'] : __( '자동화 이메일', 'acf-mail-smtp' );
        $message = isset( $action['message'] ) ? $action['message'] : '';

        // Replace placeholders
        $message = $this->replace_placeholders( $message, $submission_data );

        $smtp->send_email( $to, $subject, $message, array( 'submission_id' => $submission_id ) );
    }

    /**
     * 리다이렉션 액션
     */
    private function action_redirect( $action, $submission_id, $submission_data ) {
        $url = isset( $action['url'] ) ? $action['url'] : '';

        if ( ! empty( $url ) ) {
            // Store redirect URL in session/transient for frontend
            set_transient( 'acf_mail_smtp_redirect_' . $submission_id, $url, 60 );
        }
    }

    /**
     * 웹훅 액션
     */
    private function action_webhook( $action, $submission_id, $submission_data ) {
        $url = isset( $action['url'] ) ? $action['url'] : '';

        if ( empty( $url ) ) {
            return;
        }

        $body = array(
            'submission_id' => $submission_id,
            'data' => $submission_data,
            'timestamp' => current_time( 'mysql' ),
        );

        wp_remote_post( $url, array(
            'body' => wp_json_encode( $body ),
            'headers' => array( 'Content-Type' => 'application/json' ),
            'timeout' => 30,
        ) );
    }

    /**
     * 플레이스홀더 치환
     */
    private function replace_placeholders( $text, $submission_data ) {
        foreach ( $submission_data as $key => $value ) {
            if ( is_array( $value ) ) {
                $value = implode( ', ', $value );
            }
            $text = str_replace( '{' . $key . '}', $value, $text );
        }

        return $text;
    }

    /**
     * 자동화 규칙 저장 (AJAX)
     */
    public function ajax_save_automation() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $automation_id = isset( $_POST['automation_id'] ) ? intval( $_POST['automation_id'] ) : 0;
        $automation_data = isset( $_POST['automation_data'] ) ? $_POST['automation_data'] : array();

        if ( empty( $automation_data['name'] ) || empty( $automation_data['form_id'] ) ) {
            wp_send_json_error( array( 'message' => __( '필수 필드를 입력하세요.', 'acf-mail-smtp' ) ) );
        }

        if ( $automation_id > 0 ) {
            $result = $this->update_automation( $automation_id, $automation_data );
        } else {
            $result = $this->create_automation(
                $automation_data['form_id'],
                $automation_data['name'],
                isset( $automation_data['trigger_type'] ) ? $automation_data['trigger_type'] : 'form_submit',
                isset( $automation_data['conditions'] ) ? $automation_data['conditions'] : array(),
                isset( $automation_data['actions'] ) ? $automation_data['actions'] : array()
            );
        }

        if ( $result ) {
            wp_send_json_success( array(
                'message' => __( '자동화 규칙이 저장되었습니다.', 'acf-mail-smtp' ),
                'automation_id' => $automation_id > 0 ? $automation_id : $result,
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( '자동화 규칙 저장에 실패했습니다.', 'acf-mail-smtp' ) ) );
        }
    }

    /**
     * 자동화 규칙 삭제 (AJAX)
     */
    public function ajax_delete_automation() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $automation_id = isset( $_POST['automation_id'] ) ? intval( $_POST['automation_id'] ) : 0;

        if ( ! $automation_id ) {
            wp_send_json_error( array( 'message' => __( '잘못된 자동화 ID입니다.', 'acf-mail-smtp' ) ) );
        }

        $result = $this->delete_automation( $automation_id );

        if ( $result ) {
            wp_send_json_success( array( 'message' => __( '자동화 규칙이 삭제되었습니다.', 'acf-mail-smtp' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( '자동화 규칙 삭제에 실패했습니다.', 'acf-mail-smtp' ) ) );
        }
    }

    /**
     * 자동화 규칙 가져오기 (AJAX)
     */
    public function ajax_get_automation() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $automation_id = isset( $_POST['automation_id'] ) ? intval( $_POST['automation_id'] ) : 0;

        if ( ! $automation_id ) {
            wp_send_json_error( array( 'message' => __( '잘못된 자동화 ID입니다.', 'acf-mail-smtp' ) ) );
        }

        $automation = $this->get_automation( $automation_id );

        if ( $automation ) {
            wp_send_json_success( array( 'automation' => $automation ) );
        } else {
            wp_send_json_error( array( 'message' => __( '자동화 규칙을 찾을 수 없습니다.', 'acf-mail-smtp' ) ) );
        }
    }

    /**
     * 자동화 규칙 목록 가져오기 (AJAX)
     */
    public function ajax_get_automations() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $args = array();
        if ( isset( $_POST['form_id'] ) ) {
            $args['form_id'] = intval( $_POST['form_id'] );
        }

        $automations = $this->get_automations( $args );

        wp_send_json_success( array( 'automations' => $automations ) );
    }
}
