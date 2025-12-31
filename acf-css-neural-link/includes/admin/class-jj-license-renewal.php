<?php
/**
 * 라이센스 갱신 기능
 * 
 * @package JJ_LicenseManagerincludesAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Renewal {
    
    /**
     * 라이센스 갱신
     * 
     * @param int $license_id 라이센스 ID
     * @param string $period 구독 기간 단위
     * @param int|string $length 구독 기간 길이
     * @return array $args 추가 옵션
     * @return array 결과
     */
    public static function renew_license( $license_id, $period, $length, $args = array() ) {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_history = JJ_License_Database::get_table_name( 'history' );
        
        // 라이센스 조회
        $license = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_licenses} WHERE id = %d",
            $license_id
        ), ARRAY_A );
        
        if ( ! $license ) {
            return array(
                'success' => false,
                'message' => __( '라이센스를 찾을 수 없습니다.', 'jj-license-manager' ),
            );
        }
        
        // 만료일 계산
        $current_expires = ! empty( $license['expires_at'] ) ? strtotime( $license['expires_at'] ) : time();
        $new_expires = null;
        
        if ( strtolower( $length ) !== 'lifetime' && is_numeric( $length ) ) {
            // 기존 만료일부터 연장
            $new_expires = date( 'Y-m-d H:i:s', strtotime( '+' . $length . ' ' . $period, $current_expires ) );
        } elseif ( strtolower( $length ) === 'lifetime' ) {
            // 평생 라이센스로 변경
            $new_expires = null;
        }
        
        // 라이센스 업데이트
        $update_data = array(
            'expires_at' => $new_expires,
            'status' => 'active', // 갱신 시 활성화
        );
        
        // 주문 ID 업데이트 (새 주문이 있는 경우)
        if ( ! empty( $args['order_id'] ) ) {
            $update_data['order_id'] = intval( $args['order_id'] );
        }
        
        $wpdb->update(
            $table_licenses,
            $update_data,
            array( 'id' => $license_id ),
            array( '%s', '%s', '%d' ),
            array( '%d' )
        );
        
        // 히스토리 기록
        $wpdb->insert(
            $table_history,
            array(
                'license_id' => $license_id,
                'action' => 'renewed',
                'description' => sprintf(
                    __( '라이센스 갱신: %s %s 연장', 'jj-license-manager' ),
                    $length,
                    $period
                ),
                'performed_by' => get_current_user_id(),
                'performed_at' => current_time( 'mysql' ),
                'metadata' => json_encode( array(
                    'period' => $period,
                    'length' => $length,
                    'old_expires' => $license['expires_at'],
                    'new_expires' => $new_expires,
                ) ),
            array( '%d', '%s', '%s', '%d', '%s', '%s' )
        );
        
        // 이메일 발송 (선택사항)
        if ( ! empty( $args['send_email'] ) ) {
            $user = get_userdata( $license['user_id'] );
            if ( $user ) {
                self::send_renewal_email( $user, $license['license_key'], $new_expires );
            }
        }
        
        return array(
            'success' => true,
            'message' => __( '라이센스가 성공적으로 갱신되었습니다.', 'jj-license-manager' ),
            'new_expires' => $new_expires,
        );
    }
    
}

