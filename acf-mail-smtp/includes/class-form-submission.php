<?php
/**
 * Form Submission
 * 
 * 폼 제출 데이터를 처리하는 클래스
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Mail_SMTP_Form_Submission {

    /**
     * 제출 저장
     */
    public function save_submission( $form_id, $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_submissions';

        $ip_address = $this->get_client_ip();
        $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';

        $result = $wpdb->insert(
            $table,
            array(
                'form_id' => intval( $form_id ),
                'data' => wp_json_encode( $data ),
                'ip_address' => $ip_address,
                'user_agent' => $user_agent,
                'status' => 'new',
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%s' )
        );

        if ( $result ) {
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * 제출 가져오기
     */
    public function get_submission( $submission_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_submissions';

        $submission = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            intval( $submission_id )
        ), ARRAY_A );

        if ( $submission ) {
            $submission['data'] = json_decode( $submission['data'], true );
        }

        return $submission;
    }

    /**
     * 제출 목록 가져오기
     */
    public function get_submissions( $args = array() ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_submissions';

        $defaults = array(
            'form_id' => 0,
            'status' => '',
            'limit' => 50,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC',
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

        $orderby = in_array( $args['orderby'], array( 'id', 'form_id', 'created_at', 'status' ) ) ? $args['orderby'] : 'created_at';
        $order = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';

        $limit = intval( $args['limit'] );
        $offset = intval( $args['offset'] );

        $query = "SELECT * FROM $table $where_clause ORDER BY $orderby $order LIMIT %d OFFSET %d";
        $query_values = array_merge( $where_values, array( $limit, $offset ) );

        if ( ! empty( $where_values ) ) {
            $submissions = $wpdb->get_results( $wpdb->prepare( $query, $where_values ), ARRAY_A );
        } else {
            $submissions = $wpdb->get_results( $wpdb->prepare( $query, $limit, $offset ), ARRAY_A );
        }

        // Decode JSON data
        foreach ( $submissions as &$submission ) {
            $submission['data'] = json_decode( $submission['data'], true );
        }

        return $submissions;
    }

    /**
     * 제출 삭제
     */
    public function delete_submission( $submission_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_submissions';

        $result = $wpdb->delete(
            $table,
            array( 'id' => intval( $submission_id ) ),
            array( '%d' )
        );

        return $result !== false;
    }

    /**
     * 제출 상태 업데이트
     */
    public function update_status( $submission_id, $status ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_mail_smtp_submissions';

        $allowed_statuses = array( 'new', 'read', 'archived', 'deleted' );
        if ( ! in_array( $status, $allowed_statuses ) ) {
            return false;
        }

        $result = $wpdb->update(
            $table,
            array( 'status' => $status ),
            array( 'id' => intval( $submission_id ) ),
            array( '%s' ),
            array( '%d' )
        );

        return $result !== false;
    }

    /**
     * 클라이언트 IP 주소 가져오기
     */
    private function get_client_ip() {
        $ip_keys = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );

        foreach ( $ip_keys as $key ) {
            if ( array_key_exists( $key, $_SERVER ) === true ) {
                foreach ( explode( ',', $_SERVER[ $key ] ) as $ip ) {
                    $ip = trim( $ip );
                    if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
                        return $ip;
                    }
                }
            }
        }

        return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
    }
}
