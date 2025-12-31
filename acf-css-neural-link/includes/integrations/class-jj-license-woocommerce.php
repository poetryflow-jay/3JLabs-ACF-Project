<?php
/**
 * WooCommerce 통합 클래스
 * 
 * @package JJ_LicenseManagerincludesIntegrations
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_WooCommerce {
    
    /**
     * 주문 완료 시 라이센스 생성
     * 
     * @param int $order_id 주문 ID
     */
    public function create_license_from_order( $order_id ) {
        $order = wc_get_order( $order_id );
        
        if ( ! $order ) {
            return;
        }
        
        // 주문의 각 상품에 대해 라이센스 생성
        foreach ( $order->get_items() as $item ) {
            $product_id = $item->get_product_id();
            $product = wc_get_product( $product_id );
            
            if ( ! $product ) {
                continue;
            }
            
            // 라이센스 생성 가능한 상품인지 확인
            $license_type = $this->get_license_type_from_product( $product_id );
            if ( ! $license_type ) {
                continue;
            }
            
            // 이미 라이센스가 생성되었는지 확인
            if ( $this->license_exists_for_order_item( $order_id, $item->get_id() ) ) {
                continue;
            }
            
            // 라이센스 생성
            $this->create_license( array(
                'user_id' => $order->get_user_id(),
                'order_id' => $order_id,
                'order_item_id' => $item->get_id(),
                'product_id' => $product_id,
                'license_type' => $license_type,
                'purchase_date' => $order->get_date_created()->date( 'Y-m-d H:i:s' ),
                'expires_at' => $this->calculate_expiry_date( $product_id, $order->get_date_created() ),
                'max_activations' => $this->get_max_activations( $license_type ),
            ) );
        }
    }
    
    /**
     * 상품에서 라이센스 타입 가져오기
     * 
     * @param int $product_id 상품 ID
     * @return string|false 라이센스 타입 또는 false
     */
    private function get_license_type_from_product( $product_id ) {
        // 상품 메타에서 라이센스 타입 확인
        $license_type = get_post_meta( $product_id, '_jj_license_type', true );
        
        if ( $license_type ) {
            return $license_type;
        }
        
        // 상품 SKU 또는 이름에서 자동 감지
        $product = wc_get_product( $product_id );
        if ( ! $product ) {
            return false;
        }
        
        $product_name = strtolower( $product->get_name() );
        $sku = strtolower( $product->get_sku() );
        
        if ( strpos( $product_name, 'unlimited' ) !== false || strpos( $sku, 'unlim' ) !== false ) {
            return 'UNLIM';
        } elseif ( strpos( $product_name, 'premium' ) !== false || strpos( $sku, 'prem' ) !== false ) {
            return 'PREM';
        } elseif ( strpos( $product_name, 'basic' ) !== false || strpos( $sku, 'basic' ) !== false ) {
            return 'BASIC';
        } elseif ( strpos( $product_name, 'free' ) !== false || strpos( $sku, 'free' ) !== false ) {
            return 'FREE';
        }
        
        return false;
    }
    
    /**
     * 만료일 계산
     * 
     * @param int $product_id 상품 ID
     * @param WC_DateTime $purchase_date 구매일
     * @return string|null 만료일 (Y-m-d H:i:s) 또는 null (평생 라이센스)
     */
    private function calculate_expiry_date( $product_id, $purchase_date ) {
        // 상품 메타에서 구독 기간 확인
        $subscription_period = get_post_meta( $product_id, '_jj_subscription_period', true );
        $subscription_length = get_post_meta( $product_id, '_jj_subscription_length', true );
        
        if ( ! $subscription_period || ! $subscription_length ) {
            // 기본값: 1년
            $subscription_period = 'year';
            $subscription_length = 1;
        }
        
        // 평생 라이센스
        if ( $subscription_length === 'lifetime' || $subscription_length === 0 ) {
            return null;
        }
        
        // 만료일 계산
        $expiry_date = clone $purchase_date;
        
        switch ( $subscription_period ) {
            case 'day':
                $expiry_date->modify( "+{$subscription_length} days" );
                break;
            case 'week':
                $expiry_date->modify( "+{$subscription_length} weeks" );
                break;
            case 'month':
                $expiry_date->modify( "+{$subscription_length} months" );
                break;
            case 'year':
                $expiry_date->modify( "+{$subscription_length} years" );
                break;
        }
        
        return $expiry_date->date( 'Y-m-d H:i:s' );
    }
    
    /**
     * 최대 활성화 수 가져오기
     * 
     * @param string $license_type 라이센스 타입
     * @return int 최대 활성화 수
     */
    private function get_max_activations( $license_type ) {
        $max_activations = array(
            'FREE' => 1,
            'BASIC' => 1,
            'PREM' => 1, // Premium은 1개 사이트만
            'UNLIM' => 999, // Unlimited는 무제한
        );
        
        return isset( $max_activations[ $license_type ] ) ? $max_activations[ $license_type ] : 1;
    }
    
    /**
     * 주문 항목에 대한 라이센스 존재 여부 확인
     * 
     * @param int $order_id 주문 ID
     * @param int $order_item_id 주문 항목 ID
     * @return bool 존재 여부
     */
    private function license_exists_for_order_item( $order_id, $order_item_id ) {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        
        $count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_licenses} 
            WHERE order_id = %d AND order_item_id = %d",
            $order_id,
            $order_item_id
        ) );
        
        return $count > 0;
    }
    
    /**
     * 라이센스 생성
     * 
     * @param array $args 라이센스 정보
     * @return int|false 라이센스 ID 또는 false
     */
    private function create_license( $args ) {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_history = JJ_License_Database::get_table_name( 'history' );
        
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
        
        // 라이센스 저장
        $result = $wpdb->insert(
            $table_licenses,
            array(
                'license_key' => $license_key,
                'license_type' => $args['license_type'],
                'user_id' => $args['user_id'],
                'order_id' => $args['order_id'],
                'order_item_id' => isset( $args['order_item_id'] ) ? $args['order_item_id'] : null,
                'product_id' => $args['product_id'],
                'status' => 'active',
                'created_at' => current_time( 'mysql' ),
                'expires_at' => isset( $args['expires_at'] ) ? $args['expires_at'] : null,
                'max_activations' => $args['max_activations'],
                'activation_count' => 0,
                'purchase_date' => $args['purchase_date'],
            ),
            array( '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%s' )
        );
        
        if ( ! $result ) {
            return false;
        }
        
        $license_id = $wpdb->insert_id;
        
        // 히스토리 기록
        $wpdb->insert(
            $table_history,
            array(
                'license_id' => $license_id,
                'action' => 'created',
                'description' => sprintf(
                    __( '주문 #%d에서 라이센스 생성됨', 'jj-license-manager' ),
                    $args['order_id']
                ),
                'performed_by' => $args['user_id'],
                'performed_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s', '%d', '%s' )
        );
        
        // 이메일 발송 (선택사항)
        if ( get_option( 'jj_license_send_email', 1 ) ) {
            require_once JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-email.php';
            $user = get_userdata( $args['user_id'] );
            if ( $user ) {
                JJ_License_Email::send_license_created_email( $user, $license_key, $args['license_type'], isset( $args['expires_at'] ) ? $args['expires_at'] : null );
            }
        }
        
        return $license_id;
    }
    
}

