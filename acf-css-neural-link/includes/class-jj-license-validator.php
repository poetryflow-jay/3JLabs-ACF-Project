<?php
/**
 * 라이센스 검증 클래스
 * 
 * @package JJ_LicenseManagerincludes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Validator {
    
    /**
     * 라이센스 검증
     * 
     * @param string $license_key 라이센스 키
     * @param string $site_id 사이트 ID
     * @param string $site_url 사이트 URL
     * @return array 검증 결과
     */
    public function verify( $license_key, $site_id, $site_url ) {
        require_once JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-security.php';
        require_once JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-cache.php';
        
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_activations = JJ_License_Database::get_table_name( 'activations' );
        
        // 입력값 재검증
        $license_key = JJ_License_Security::validate_input( $license_key, 'license_key' );
        $site_id = JJ_License_Security::validate_input( $site_id, 'site_id' );
        $site_url = JJ_License_Security::validate_input( $site_url, 'site_url' );
        
        if ( ! $license_key || ! $site_id || ! $site_url ) {
            return array(
                'valid' => false,
                'message' => __( '유효하지 않은 입력값입니다.', 'jj-license-manager' ),
            );
        }
        
        // SQL Injection 방지
        $query = "SELECT * FROM {$table_licenses} WHERE license_key = %s";
        if ( ! JJ_License_Security::validate_sql_query( $query ) ) {
            JJ_License_Security::log_security_event( 'sql_injection_attempt', array(
                'ip' => JJ_License_Security::get_client_ip(),
                'query' => $query,
            ) );
            return array(
                'valid' => false,
                'message' => __( '보안 검증에 실패했습니다.', 'jj-license-manager' ),
            );
        }
        
        // 라이센스 조회
        $license = $wpdb->get_row( $wpdb->prepare( $query, $license_key ), ARRAY_A );
        
        if ( ! $license ) {
            return array(
                'valid' => false,
                'message' => __( '라이센스 키를 찾을 수 없습니다.', 'jj-license-manager' ),
            );
        }
        
        // 상태 확인
        if ( $license['status'] !== 'active' ) {
            return array(
                'valid' => false,
                'type' => $license['license_type'],
                'message' => __( '이 라이센스는 비활성화되었습니다.', 'jj-license-manager' ),
                'status' => $license['status'],
            );
        }
        
        // 만료일 확인
        if ( ! empty( $license['expires_at'] ) ) {
            $expires_timestamp = strtotime( $license['expires_at'] );
            if ( $expires_timestamp < time() ) {
                // 만료된 라이센스 자동 비활성화
                $this->deactivate_license( $license['id'], 'expired' );
                
                return array(
                    'valid' => false,
                    'type' => $license['license_type'],
                    'message' => __( '이 라이센스는 만료되었습니다.', 'jj-license-manager' ),
                    'expired' => true,
                    'expires_timestamp' => $expires_timestamp,
                );
            }
            
            $days_until_expiry = ( $expires_timestamp - time() ) / ( 24 * 60 * 60 );
        } else {
            $days_until_expiry = null;
        }
        
        // 활성화 제한 확인
        $active_activations = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_activations} 
            WHERE license_id = %d AND is_active = 1",
            $license['id']
        ) );
        
        $max_activations = intval( $license['max_activations'] );
        
        // Premium 버전은 1개 사이트만 사용 가능
        if ( $license['license_type'] === 'PREM' && $active_activations > 0 ) {
            // 현재 사이트가 활성화되어 있는지 확인
            $current_activation = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$table_activations} 
                WHERE license_id = %d AND site_id = %s AND is_active = 1",
                $license['id'],
                $site_id
            ), ARRAY_A );
            
            if ( ! $current_activation ) {
                return array(
                    'valid' => false,
                    'type' => $license['license_type'],
                    'message' => __( '이 라이센스는 다른 사이트에서 사용 중입니다. Premium 버전은 1개 사이트에서만 사용 가능합니다.', 'jj-license-manager' ),
                );
            }
        }
        
        // 활성화 제한 확인
        if ( $active_activations >= $max_activations ) {
            // 현재 사이트가 활성화되어 있는지 확인
            $current_activation = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$table_activations} 
                WHERE license_id = %d AND site_id = %s AND is_active = 1",
                $license['id'],
                $site_id
            ), ARRAY_A );
            
            if ( ! $current_activation ) {
                return array(
                    'valid' => false,
                    'type' => $license['license_type'],
                    'message' => sprintf( __( '최대 활성화 수(%d개)에 도달했습니다.', 'jj-license-manager' ), $max_activations ),
                );
            }
        }
        
        // 활성화 기록
        $this->record_activation( $license['id'], $site_id, $site_url );
        
        return array(
            'valid' => true,
            'type' => $license['license_type'],
            'message' => __( '라이센스가 활성화되었습니다.', 'jj-license-manager' ),
            'status' => 'active',
            'expires_timestamp' => ! empty( $license['expires_at'] ) ? strtotime( $license['expires_at'] ) : null,
            'valid_until' => ! empty( $license['expires_at'] ) ? strtotime( $license['expires_at'] ) : null,
            'days_until_expiry' => $days_until_expiry,
            'expiring_soon' => $days_until_expiry !== null && $days_until_expiry <= 14,
        );
    }
    
    /**
     * 활성화 기록
     * 
     * @param int $license_id 라이센스 ID
     * @param string $site_id 사이트 ID
     * @param string $site_url 사이트 URL
     */
    public function record_activation( $license_id, $site_id, $site_url ) {
        global $wpdb;
        
        $table_activations = JJ_License_Database::get_table_name( 'activations' );
        
        // 기존 활성화 확인
        $existing = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_activations} 
            WHERE license_id = %d AND site_id = %s",
            $license_id,
            $site_id
        ), ARRAY_A );
        
        if ( $existing ) {
            // 이미 활성화되어 있으면 업데이트
            if ( ! $existing['is_active'] ) {
                $wpdb->update(
                    $table_activations,
                    array(
                        'is_active' => 1,
                        'activated_at' => current_time( 'mysql' ),
                        'deactivated_at' => null,
                        'ip_address' => $this->get_client_ip(),
                        'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null,
                    ),
                    array( 'id' => $existing['id'] ),
                    array( '%d', '%s', '%s', '%s', '%s' ),
                    array( '%d' )
                );
            }
        } else {
            // 새 활성화 기록
            $wpdb->insert(
                $table_activations,
                array(
                    'license_id' => $license_id,
                    'site_id' => $site_id,
                    'site_url' => $site_url,
                    'activated_at' => current_time( 'mysql' ),
                    'is_active' => 1,
                    'ip_address' => $this->get_client_ip(),
                    'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null,
                ),
                array( '%d', '%s', '%s', '%s', '%d', '%s', '%s' )
            );
            
            // 활성화 카운트 증가
            $wpdb->query( $wpdb->prepare(
                "UPDATE {$wpdb->prefix}jj_licenses 
                SET activation_count = activation_count + 1 
                WHERE id = %d",
                $license_id
            ) );
        }
        
        // 히스토리 기록
        $this->add_history( $license_id, 'activated', sprintf(
            __( '사이트에서 활성화됨: %s', 'jj-license-manager' ),
            $site_url
        ) );
    }
    
    /**
     * 만료된 라이센스 확인 및 비활성화
     */
    public function check_expired_licenses() {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        
        // 만료된 라이센스 조회
        $expired_licenses = $wpdb->get_results(
            "SELECT id FROM {$table_licenses} 
            WHERE status = 'active' 
            AND expires_at IS NOT NULL 
            AND expires_at < NOW()"
        );
        
        foreach ( $expired_licenses as $license ) {
            $this->deactivate_license( $license->id, 'expired' );
        }
    }
    
    /**
     * 라이센스 비활성화
     * 
     * @param int $license_id 라이센스 ID
     * @param string $reason 비활성화 사유
     */
    public function deactivate_license( $license_id, $reason = 'manual' ) {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_activations = JJ_License_Database::get_table_name( 'activations' );
        
        // 라이센스 비활성화
        $wpdb->update(
            $table_licenses,
            array( 'status' => 'inactive' ),
            array( 'id' => $license_id ),
            array( '%s' ),
            array( '%d' )
        );
        
        // 모든 활성화 비활성화
        $wpdb->update(
            $table_activations,
            array(
                'is_active' => 0,
                'deactivated_at' => current_time( 'mysql' ),
            ),
            array(
                'license_id' => $license_id,
                'is_active' => 1,
            ),
            array( '%d', '%s' ),
            array( '%d', '%d' )
        );
        
        // 히스토리 기록
        $reason_messages = array(
            'expired' => __( '만료로 인한 자동 비활성화', 'jj-license-manager' ),
            'manual' => __( '수동 비활성화', 'jj-license-manager' ),
        );
        
        $this->add_history( $license_id, 'deactivated', $reason_messages[ $reason ] ?? $reason );
    }
    
    /**
     * 히스토리 추가
     * 
     * @param int $license_id 라이센스 ID
     * @param string $action 액션
     * @param string $description 설명
     * @param array $metadata 메타데이터
     */
    private function add_history( $license_id, $action, $description = null, $metadata = array() ) {
        global $wpdb;
        
        $table_history = JJ_License_Database::get_table_name( 'history' );
        
        $wpdb->insert(
            $table_history,
            array(
                'license_id' => $license_id,
                'action' => $action,
                'description' => $description,
                'performed_by' => get_current_user_id(),
                'performed_at' => current_time( 'mysql' ),
                'ip_address' => $this->get_client_ip(),
                'metadata' => ! empty( $metadata ) ? json_encode( $metadata ) : null,
            ),
            array( '%d', '%s', '%s', '%d', '%s', '%s', '%s' )
        );
    }
    
    /**
     * 클라이언트 IP 주소 가져오기
     * 
     * @return string IP 주소
     */
    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        );
        
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
        
        return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }
}

