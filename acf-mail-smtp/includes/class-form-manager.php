<?php
/**
 * Form Manager
 * 
 * 폼 생성, 관리, 렌더링을 담당하는 클래스
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Mail_SMTP_Form_Manager {

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
        add_action( 'wp_ajax_acf_mail_smtp_save_form', array( $this, 'ajax_save_form' ) );
        add_action( 'wp_ajax_acf_mail_smtp_delete_form', array( $this, 'ajax_delete_form' ) );
        add_action( 'wp_ajax_acf_mail_smtp_get_form', array( $this, 'ajax_get_form' ) );
        add_action( 'wp_ajax_acf_mail_smtp_get_forms', array( $this, 'ajax_get_forms' ) );
        add_action( 'wp_ajax_acf_mail_smtp_duplicate_form', array( $this, 'ajax_duplicate_form' ) );

        // Form submission
        add_action( 'wp_ajax_acf_mail_smtp_submit_form', array( $this, 'ajax_submit_form' ) );
        add_action( 'wp_ajax_nopriv_acf_mail_smtp_submit_form', array( $this, 'ajax_submit_form' ) );
    }

    /**
     * 폼 생성
     */
    public function create_form( $name, $fields = array(), $settings = array() ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_forms';

        $default_settings = array(
            'success_message' => __( '폼이 성공적으로 제출되었습니다.', 'acf-mail-smtp' ),
            'error_message' => __( '오류가 발생했습니다. 다시 시도해주세요.', 'acf-mail-smtp' ),
            'redirect_url' => '',
            'send_email' => true,
            'email_to' => get_option( 'admin_email' ),
            'email_subject' => __( '새로운 폼 제출', 'acf-mail-smtp' ),
        );

        $settings = wp_parse_args( $settings, $default_settings );

        $result = $wpdb->insert(
            $table,
            array(
                'name' => sanitize_text_field( $name ),
                'title' => sanitize_text_field( $name ),
                'fields' => wp_json_encode( $fields ),
                'settings' => wp_json_encode( $settings ),
                'status' => 'active',
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s' )
        );

        if ( $result ) {
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * 폼 업데이트
     */
    public function update_form( $form_id, $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_forms';

        $update_data = array();
        $update_format = array();

        if ( isset( $data['name'] ) ) {
            $update_data['name'] = sanitize_text_field( $data['name'] );
            $update_format[] = '%s';
        }

        if ( isset( $data['title'] ) ) {
            $update_data['title'] = sanitize_text_field( $data['title'] );
            $update_format[] = '%s';
        }

        if ( isset( $data['description'] ) ) {
            $update_data['description'] = sanitize_textarea_field( $data['description'] );
            $update_format[] = '%s';
        }

        if ( isset( $data['fields'] ) ) {
            $update_data['fields'] = wp_json_encode( $data['fields'] );
            $update_format[] = '%s';
        }

        if ( isset( $data['settings'] ) ) {
            $update_data['settings'] = wp_json_encode( $data['settings'] );
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
            array( 'id' => intval( $form_id ) ),
            $update_format,
            array( '%d' )
        );

        return $result !== false;
    }

    /**
     * 폼 가져오기
     */
    public function get_form( $form_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_forms';

        $form = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            intval( $form_id )
        ), ARRAY_A );

        if ( $form ) {
            $form['fields'] = json_decode( $form['fields'], true );
            $form['settings'] = json_decode( $form['settings'], true );
        }

        return $form;
    }

    /**
     * 폼 목록 가져오기
     */
    public function get_forms( $args = array() ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_forms';

        $defaults = array(
            'status' => 'active',
            'limit' => 100,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );

        $where = array();
        $where_values = array();

        if ( ! empty( $args['status'] ) ) {
            $where[] = 'status = %s';
            $where_values[] = $args['status'];
        }

        $where_clause = ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '';

        $orderby = in_array( $args['orderby'], array( 'id', 'name', 'created_at', 'updated_at' ) ) ? $args['orderby'] : 'created_at';
        $order = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';

        $limit = intval( $args['limit'] );
        $offset = intval( $args['offset'] );

        $query = "SELECT * FROM $table $where_clause ORDER BY $orderby $order LIMIT %d OFFSET %d";
        $query_values = array_merge( $where_values, array( $limit, $offset ) );

        if ( ! empty( $where_values ) ) {
            $forms = $wpdb->get_results( $wpdb->prepare( $query, $where_values ), ARRAY_A );
        } else {
            $forms = $wpdb->get_results( $wpdb->prepare( $query, $limit, $offset ), ARRAY_A );
        }

        // Decode JSON fields
        foreach ( $forms as &$form ) {
            $form['fields'] = json_decode( $form['fields'], true );
            $form['settings'] = json_decode( $form['settings'], true );
        }

        return $forms;
    }

    /**
     * 폼 삭제
     */
    public function delete_form( $form_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_forms';

        $result = $wpdb->delete(
            $table,
            array( 'id' => intval( $form_id ) ),
            array( '%d' )
        );

        return $result !== false;
    }

    /**
     * 폼 렌더링
     */
    public function render_form( $form_id, $atts = array() ) {
        $form = $this->get_form( $form_id );

        if ( ! $form || $form['status'] !== 'active' ) {
            return '';
        }

        ob_start();
        include ACF_MAIL_SMTP_PLUGIN_DIR . 'templates/form.php';
        return ob_get_clean();
    }

    /**
     * 폼 저장 (AJAX)
     */
    public function ajax_save_form() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        $form_data_raw = isset( $_POST['form_data'] ) ? $_POST['form_data'] : '';

        // Handle JSON string from drag & drop form builder
        if ( is_string( $form_data_raw ) ) {
            $form_data = json_decode( stripslashes( $form_data_raw ), true );
            if ( json_last_error() !== JSON_ERROR_NONE ) {
                wp_send_json_error( array( 'message' => __( '잘못된 폼 데이터입니다.', 'acf-mail-smtp' ) ) );
            }
        } else {
            $form_data = $form_data_raw;
        }

        if ( empty( $form_data['name'] ) ) {
            wp_send_json_error( array( 'message' => __( '폼 이름을 입력하세요.', 'acf-mail-smtp' ) ) );
        }

        if ( $form_id > 0 ) {
            // Update existing form
            $result = $this->update_form( $form_id, $form_data );
        } else {
            // Create new form
            $result = $this->create_form(
                $form_data['name'],
                isset( $form_data['fields'] ) ? $form_data['fields'] : array(),
                isset( $form_data['settings'] ) ? $form_data['settings'] : array()
            );

            // Also update title and description for new forms
            if ( $result ) {
                $update_data = array();
                if ( ! empty( $form_data['title'] ) ) {
                    $update_data['title'] = $form_data['title'];
                }
                if ( ! empty( $form_data['description'] ) ) {
                    $update_data['description'] = $form_data['description'];
                }
                if ( ! empty( $update_data ) ) {
                    $this->update_form( $result, $update_data );
                }
            }
        }

        if ( $result ) {
            wp_send_json_success( array(
                'message' => __( '폼이 저장되었습니다.', 'acf-mail-smtp' ),
                'form_id' => $form_id > 0 ? $form_id : $result,
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( '폼 저장에 실패했습니다.', 'acf-mail-smtp' ) ) );
        }
    }

    /**
     * 폼 복제 (AJAX)
     */
    public function ajax_duplicate_form() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;

        if ( ! $form_id ) {
            wp_send_json_error( array( 'message' => __( '잘못된 폼 ID입니다.', 'acf-mail-smtp' ) ) );
        }

        $form = $this->get_form( $form_id );

        if ( ! $form ) {
            wp_send_json_error( array( 'message' => __( '폼을 찾을 수 없습니다.', 'acf-mail-smtp' ) ) );
        }

        // Create duplicated form
        $new_name = $form['name'] . ' (복사)';
        $new_form_id = $this->create_form(
            $new_name,
            $form['fields'],
            $form['settings']
        );

        if ( $new_form_id ) {
            // Update title and description
            $this->update_form( $new_form_id, array(
                'title' => $form['title'] ? $form['title'] . ' (복사)' : '',
                'description' => $form['description'],
            ) );

            wp_send_json_success( array(
                'message' => __( '폼이 복제되었습니다.', 'acf-mail-smtp' ),
                'form_id' => $new_form_id,
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( '폼 복제에 실패했습니다.', 'acf-mail-smtp' ) ) );
        }
    }

    /**
     * 폼 삭제 (AJAX)
     */
    public function ajax_delete_form() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;

        if ( ! $form_id ) {
            wp_send_json_error( array( 'message' => __( '잘못된 폼 ID입니다.', 'acf-mail-smtp' ) ) );
        }

        $result = $this->delete_form( $form_id );

        if ( $result ) {
            wp_send_json_success( array( 'message' => __( '폼이 삭제되었습니다.', 'acf-mail-smtp' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( '폼 삭제에 실패했습니다.', 'acf-mail-smtp' ) ) );
        }
    }

    /**
     * 폼 가져오기 (AJAX)
     */
    public function ajax_get_form() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;

        if ( ! $form_id ) {
            wp_send_json_error( array( 'message' => __( '잘못된 폼 ID입니다.', 'acf-mail-smtp' ) ) );
        }

        $form = $this->get_form( $form_id );

        if ( $form ) {
            wp_send_json_success( array( 'form' => $form ) );
        } else {
            wp_send_json_error( array( 'message' => __( '폼을 찾을 수 없습니다.', 'acf-mail-smtp' ) ) );
        }
    }

    /**
     * 폼 목록 가져오기 (AJAX)
     */
    public function ajax_get_forms() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $args = array();
        if ( isset( $_POST['status'] ) ) {
            $args['status'] = sanitize_text_field( $_POST['status'] );
        }

        $forms = $this->get_forms( $args );

        wp_send_json_success( array( 'forms' => $forms ) );
    }

    /**
     * 폼 제출 (AJAX)
     */
    public function ajax_submit_form() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;

        if ( ! $form_id ) {
            wp_send_json_error( array( 'message' => __( '잘못된 폼 ID입니다.', 'acf-mail-smtp' ) ) );
        }

        $form = $this->get_form( $form_id );

        if ( ! $form || $form['status'] !== 'active' ) {
            wp_send_json_error( array( 'message' => __( '폼을 찾을 수 없습니다.', 'acf-mail-smtp' ) ) );
        }

        // Validate and process submission
        $submission_data = isset( $_POST['data'] ) ? $_POST['data'] : array();
        
        // Save submission
        if ( class_exists( 'ACF_Mail_SMTP_Form_Submission' ) ) {
            $submission = new ACF_Mail_SMTP_Form_Submission();
            $submission_id = $submission->save_submission( $form_id, $submission_data );

            if ( $submission_id ) {
                // Send email if enabled
                if ( isset( $form['settings']['send_email'] ) && $form['settings']['send_email'] ) {
                    $this->send_form_email( $form, $submission_data, $submission_id );
                }

                // Run automation
                if ( class_exists( 'ACF_Mail_SMTP_Automation_Manager' ) ) {
                    $automation = new ACF_Mail_SMTP_Automation_Manager();
                    $automation->trigger_automation( $form_id, $submission_id, $submission_data );
                }

                wp_send_json_success( array(
                    'message' => isset( $form['settings']['success_message'] ) ? $form['settings']['success_message'] : __( '폼이 성공적으로 제출되었습니다.', 'acf-mail-smtp' ),
                    'redirect' => isset( $form['settings']['redirect_url'] ) ? $form['settings']['redirect_url'] : '',
                ) );
            }
        }

        wp_send_json_error( array(
            'message' => isset( $form['settings']['error_message'] ) ? $form['settings']['error_message'] : __( '오류가 발생했습니다.', 'acf-mail-smtp' ),
        ) );
    }

    /**
     * 폼 이메일 발송
     */
    private function send_form_email( $form, $submission_data, $submission_id ) {
        if ( ! class_exists( 'ACF_Mail_SMTP_SMTP_Manager' ) ) {
            return false;
        }

        $smtp = new ACF_Mail_SMTP_SMTP_Manager();

        $to = isset( $form['settings']['email_to'] ) ? $form['settings']['email_to'] : get_option( 'admin_email' );
        $subject = isset( $form['settings']['email_subject'] ) ? $form['settings']['email_subject'] : __( '새로운 폼 제출', 'acf-mail-smtp' );

        // Build email message
        $message = $this->build_email_message( $form, $submission_data );

        return $smtp->send_email( $to, $subject, $message, array( 'submission_id' => $submission_id, 'form_id' => $form['id'] ) );
    }

    /**
     * 이메일 메시지 생성
     */
    private function build_email_message( $form, $submission_data ) {
        // Check if custom email template exists
        if ( isset( $form['settings']['email_template'] ) && ! empty( $form['settings']['email_template'] ) ) {
            return $this->process_email_template( $form, $submission_data );
        }

        // Default email template
        $message = '<h2>' . esc_html( $form['title'] ) . '</h2>';
        $message .= $this->build_all_fields_table( $form, $submission_data );

        return $message;
    }

    /**
     * 이메일 템플릿 처리
     */
    private function process_email_template( $form, $submission_data ) {
        $template = $form['settings']['email_template'];

        // System variables
        $replacements = array(
            '{form_title}'      => isset( $form['title'] ) ? esc_html( $form['title'] ) : '',
            '{site_name}'       => esc_html( get_bloginfo( 'name' ) ),
            '{submission_date}' => date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ),
            '{all_fields}'      => $this->build_all_fields_table( $form, $submission_data ),
        );

        // Field variables
        if ( isset( $form['fields'] ) && is_array( $form['fields'] ) ) {
            foreach ( $form['fields'] as $field ) {
                $field_name = isset( $field['name'] ) ? $field['name'] : '';
                $field_id = isset( $field['id'] ) ? $field['id'] : '';

                // Try to get value by name first, then by id
                $field_value = '';
                if ( isset( $submission_data[ $field_name ] ) ) {
                    $field_value = $submission_data[ $field_name ];
                } elseif ( isset( $submission_data[ $field_id ] ) ) {
                    $field_value = $submission_data[ $field_id ];
                }

                if ( is_array( $field_value ) ) {
                    $field_value = implode( ', ', $field_value );
                }

                $replacements[ '{' . $field_name . '}' ] = esc_html( $field_value );
            }
        }

        // Replace all variables
        $message = str_replace( array_keys( $replacements ), array_values( $replacements ), $template );

        return $message;
    }

    /**
     * 모든 필드 테이블 생성
     */
    private function build_all_fields_table( $form, $submission_data ) {
        $table = '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">';

        if ( isset( $form['fields'] ) && is_array( $form['fields'] ) ) {
            foreach ( $form['fields'] as $field ) {
                // Skip layout fields
                $skip_types = array( 'hidden', 'divider', 'heading', 'html' );
                if ( isset( $field['type'] ) && in_array( $field['type'], $skip_types ) ) {
                    continue;
                }

                $field_name = isset( $field['name'] ) ? $field['name'] : '';
                $field_id = isset( $field['id'] ) ? $field['id'] : '';
                $field_label = isset( $field['label'] ) ? $field['label'] : $field_name;

                // Try to get value by name first, then by id
                $field_value = '';
                if ( isset( $submission_data[ $field_name ] ) ) {
                    $field_value = $submission_data[ $field_name ];
                } elseif ( isset( $submission_data[ $field_id ] ) ) {
                    $field_value = $submission_data[ $field_id ];
                }

                if ( is_array( $field_value ) ) {
                    $field_value = implode( ', ', $field_value );
                }

                $table .= '<tr>';
                $table .= '<td style="padding: 12px; border: 1px solid #e5e7eb; background: #f9fafb; font-weight: 600; width: 30%;">' . esc_html( $field_label ) . '</td>';
                $table .= '<td style="padding: 12px; border: 1px solid #e5e7eb;">' . esc_html( $field_value ) . '</td>';
                $table .= '</tr>';
            }
        }

        $table .= '</table>';

        return $table;
    }
}
