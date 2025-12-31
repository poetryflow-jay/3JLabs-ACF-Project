<?php
/**
 * 라이센스 수동 생성 클래스
 * 
 * @package JJ_LicenseManagerincludesAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Creator {
    
    /**
     * 라이센스 수동 생성
     * 
     * @param array $args 라이센스 정보
     * @return array 결과
     */
    public static function create_license( $args ) {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_history = JJ_License_Database::get_table_name( 'history' );
        
        // 필수 필드 확인
        if ( empty( $args['license_type'] ) || empty( $args['user_id'] ) ) {
            return array(
                'success' => false,
                'message' => __( '필수 필드가 누락되었습니다.', 'jj-license-manager' ),
            );
        }
        
        // 라이센스 키 생성
        $license_key = JJ_License_Generator::generate( $args['license_type'] );
        
        // 중복 확인
        $existing = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$table_licenses} WHERE license_key = %s",
            $license_key
        ) );
        
        if ( $existing ) {
            // 중복이면 다시 생성
            $license_key = JJ_License_Generator::generate( $args['license_type'] );
        }
        
        // 만료일 계산
        $expires_at = null;
        if ( ! empty( $args['subscription_period'] ) && ! empty( $args['subscription_length'] ) ) {
            if ( strtolower( $args['subscription_length'] ) !== 'lifetime' ) {
                $purchase_date = ! empty( $args['purchase_date'] ) ? strtotime( $args['purchase_date'] ) : time();
                $expires_at = date( 'Y-m-d H:i:s', strtotime( '+' . $args['subscription_length'] . ' ' . $args['subscription_period'], $purchase_date ) );
            }
        }
        
        // 최대 활성화 수 결정
        $max_activations = isset( $args['max_activations'] ) ? intval( $args['max_activations'] ) : 1;
        if ( $args['license_type'] === 'UNLIM' ) {
            $max_activations = 999;
        }
        
        // 라이센스 저장
        $result = $wpdb->insert(
            $table_licenses,
            array(
                'license_key' => $license_key,
                'license_type' => $args['license_type'],
                'user_id' => $args['user_id'],
                'order_id' => isset( $args['order_id'] ) ? $args['order_id'] : null,
                'order_item_id' => isset( $args['order_item_id'] ) ? $args['order_item_id'] : null,
                'product_id' => isset( $args['product_id'] ) ? $args['product_id'] : null,
                'status' => isset( $args['status'] ) ? $args['status'] : 'active',
                'created_at' => current_time( 'mysql' ),
                'expires_at' => $expires_at,
                'max_activations' => $max_activations,
                'activation_count' => 0,
                'purchase_date' => isset( $args['purchase_date'] ) ? $args['purchase_date'] : current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%s' )
        );
        
        if ( ! $result ) {
            return array(
                'success' => false,
                'message' => __( '라이센스 생성에 실패했습니다.', 'jj-license-manager' ),
            );
        }
        
        $license_id = $wpdb->insert_id;
        
        // 히스토리 기록
        $wpdb->insert(
            $table_history,
            array(
                'license_id' => $license_id,
                'action' => 'created',
                'description' => __( '관리자가 수동으로 라이센스 생성', 'jj-license-manager' ),
                'performed_by' => get_current_user_id(),
                'performed_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s', '%d', '%s' )
        );
        
        // 이메일 발송 (선택사항)
        if ( ! empty( $args['send_email'] ) ) {
            $user = get_userdata( $args['user_id'] );
            if ( $user ) {
                self::send_license_email( $user, $license_key, $args['license_type'] );
            }
        }
        
        return array(
            'success' => true,
            'license_id' => $license_id,
            'license_key' => $license_key,
            'message' => __( '라이센스가 성공적으로 생성되었습니다.', 'jj-license-manager' ),
        );
    }
    
}

