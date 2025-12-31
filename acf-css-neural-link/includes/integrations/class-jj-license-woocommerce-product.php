<?php
/**
 * WooCommerce 상품 편집 페이지 통합
 * 
 * @package JJ_LicenseManagerincludesIntegrations
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_WooCommerce_Product {
    
    /**
     * 생성자
     */
    public function __construct() {
        // 상품 데이터 탭에 라이센스 설정 추가
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_license_tab' ) );
        add_action( 'woocommerce_product_data_panels', array( $this, 'render_license_tab' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_license_meta' ) );
        
        // 주문 상세 페이지에 라이센스 표시
        add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_licenses_in_order' ) );
        add_action( 'woocommerce_order_details_after_order_table', array( $this, 'display_licenses_in_order_frontend' ) );
    }
    
    /**
     * 라이센스 설정 탭 추가
     * 
     * @param array $tabs 기존 탭
     * @return array 수정된 탭
     */
    public function add_license_tab( $tabs ) {
        $tabs['jj_license'] = array(
            'label'    => __( '라이센스 설정', 'jj-license-manager' ),
            'target'   => 'jj_license_product_data',
            'class'    => array( 'show_if_simple', 'show_if_variable' ),
            'priority' => 25,
        );
        return $tabs;
    }
    
    /**
     * 라이센스 설정 탭 내용 렌더링
     */
    public function render_license_tab() {
        global $post;
        
        $license_type = get_post_meta( $post->ID, '_jj_license_type', true );
        $subscription_period = get_post_meta( $post->ID, '_jj_subscription_period', true );
        $subscription_length = get_post_meta( $post->ID, '_jj_subscription_length', true );
        ?>
        <div id="jj_license_product_data" class="panel woocommerce_options_panel">
            <div class="options_group">
                <p style="padding: 10px; background: #f0f0f1; margin: 0;">
                    <strong><?php esc_html_e( '라이센스 설정', 'jj-license-manager' ); ?></strong>
                </p>
                <p class="form-field">
                    <?php
                    woocommerce_wp_select( array(
                        'id' => '_jj_license_type',
                        'label' => __( '라이센스 타입', 'jj-license-manager' ),
                        'options' => array(
                            '' => __( '라이센스 없음', 'jj-license-manager' ),
                            'FREE' => __( 'Free', 'jj-license-manager' ),
                            'BASIC' => __( 'Basic', 'jj-license-manager' ),
                            'PREM' => __( 'Premium', 'jj-license-manager' ),
                            'UNLIM' => __( 'Unlimited', 'jj-license-manager' ),
                        ),
                        'value' => $license_type,
                        'desc_tip' => true,
                        'description' => __( '이 상품을 구매할 때 생성될 라이센스 타입을 선택하세요.', 'jj-license-manager' ),
                    ) );
                    ?>
                </p>
                
                <p class="form-field">
                    <?php
                    woocommerce_wp_select( array(
                        'id' => '_jj_subscription_period',
                        'label' => __( '구독 기간 단위', 'jj-license-manager' ),
                        'options' => array(
                            '' => __( '선택 안 함', 'jj-license-manager' ),
                            'day' => __( '일', 'jj-license-manager' ),
                            'week' => __( '주', 'jj-license-manager' ),
                            'month' => __( '월', 'jj-license-manager' ),
                            'year' => __( '년', 'jj-license-manager' ),
                        ),
                        'value' => $subscription_period,
                        'desc_tip' => true,
                        'description' => __( '라이센스 유효 기간의 단위를 선택하세요.', 'jj-license-manager' ),
                    ) );
                    ?>
                </p>
                
                <p class="form-field">
                    <?php
                    woocommerce_wp_text_input( array(
                        'id' => '_jj_subscription_length',
                        'label' => __( '구독 기간 길이', 'jj-license-manager' ),
                        'type' => 'number',
                        'value' => $subscription_length,
                        'placeholder' => __( '예: 1, 12, 또는 lifetime', 'jj-license-manager' ),
                        'desc_tip' => true,
                        'description' => __( '구독 기간의 길이를 입력하세요. 평생 라이센스는 "lifetime"을 입력하세요.', 'jj-license-manager' ),
                    ) );
                    ?>
                </p>
            </div>
        </div>
        <?php
    }
    
    /**
     * 라이센스 메타 저장
     * 
     * @param int $post_id 상품 ID
     */
    public function save_license_meta( $post_id ) {
        // 라이센스 타입 저장
        if ( isset( $_POST['_jj_license_type'] ) ) {
            update_post_meta( $post_id, '_jj_license_type', sanitize_text_field( $_POST['_jj_license_type'] ) );
        } else {
            delete_post_meta( $post_id, '_jj_license_type' );
        }
        
        // 구독 기간 단위 저장
        if ( isset( $_POST['_jj_subscription_period'] ) ) {
            update_post_meta( $post_id, '_jj_subscription_period', sanitize_text_field( $_POST['_jj_subscription_period'] ) );
        } else {
            delete_post_meta( $post_id, '_jj_subscription_period' );
        }
        
        // 구독 기간 길이 저장
        if ( isset( $_POST['_jj_subscription_length'] ) ) {
            $length = sanitize_text_field( $_POST['_jj_subscription_length'] );
            if ( strtolower( $length ) === 'lifetime' ) {
                $length = 'lifetime';
            }
            update_post_meta( $post_id, '_jj_subscription_length', $length );
        } else {
            delete_post_meta( $post_id, '_jj_subscription_length' );
        }
    }
    
    /**
     * 주문 상세 페이지에 라이센스 표시 (관리자)
     * 
     * @param WC_Order $order 주문 객체
     */
    public function display_licenses_in_order( $order ) {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        
        $licenses = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table_licenses} WHERE order_id = %d ORDER BY created_at DESC",
            $order->get_id()
        ), ARRAY_A );
        
        if ( empty( $licenses ) ) {
            return;
        }
        ?>
        <div class="order_data_column" style="clear: both; margin-top: 20px;">
            <h3><?php esc_html_e( '라이센스 키', 'jj-license-manager' ); ?></h3>
            <div class="address">
                <?php foreach ( $licenses as $license ) : ?>
                    <p>
                        <strong><?php echo esc_html( $license['license_key'] ); ?></strong>
                        <br>
                        <span class="license-type license-type-<?php echo esc_attr( strtolower( $license['license_type'] ) ); ?>">
                            <?php echo esc_html( $license['license_type'] ); ?>
                        </span>
                        <?php if ( $license['expires_at'] ) : ?>
                            <br>
                            <small>
                                <?php
                                printf(
                                    esc_html__( '만료일: %s', 'jj-license-manager' ),
                                    date_i18n( get_option( 'date_format' ), strtotime( $license['expires_at'] ) )
                                );
                                ?>
                            </small>
                        <?php endif; ?>
                        <br>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=jj-license-manager&s=' . urlencode( $license['license_key'] ) ) ); ?>" target="_blank">
                            <?php esc_html_e( '라이센스 관리', 'jj-license-manager' ); ?>
                        </a>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * 주문 상세 페이지에 라이센스 표시 (프론트엔드)
     * 
     * @param WC_Order $order 주문 객체
     */
    public function display_licenses_in_order_frontend( $order ) {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        
        $licenses = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table_licenses} WHERE order_id = %d ORDER BY created_at DESC",
            $order->get_id()
        ), ARRAY_A );
        
        if ( empty( $licenses ) ) {
            return;
        }
        ?>
        <section class="woocommerce-order-licenses" style="margin: 20px 0;">
            <h2><?php esc_html_e( '라이센스 키', 'jj-license-manager' ); ?></h2>
            <table class="woocommerce-table woocommerce-table--order-licenses shop_table order_details">
                <thead>
                    <tr>
                        <th><?php esc_html_e( '라이센스 키', 'jj-license-manager' ); ?></th>
                        <th><?php esc_html_e( '타입', 'jj-license-manager' ); ?></th>
                        <th><?php esc_html_e( '만료일', 'jj-license-manager' ); ?></th>
                        <th><?php esc_html_e( '상태', 'jj-license-manager' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $licenses as $license ) : ?>
                        <tr>
                            <td>
                                <code style="font-size: 12px;"><?php echo esc_html( $license['license_key'] ); ?></code>
                                <button class="button button-small copy-license-key" data-license-key="<?php echo esc_attr( $license['license_key'] ); ?>" style="margin-left: 10px;">
                                    <?php esc_html_e( '복사', 'jj-license-manager' ); ?>
                                </button>
                            </td>
                            <td>
                                <span class="license-type license-type-<?php echo esc_attr( strtolower( $license['license_type'] ) ); ?>">
                                    <?php echo esc_html( $license['license_type'] ); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ( $license['expires_at'] ) : ?>
                                    <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $license['expires_at'] ) ) ); ?>
                                <?php else : ?>
                                    <span style="color: #2271b1;"><?php esc_html_e( '평생', 'jj-license-manager' ); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="license-status license-status-<?php echo esc_attr( $license['status'] ); ?>">
                                    <?php
                                    $status_labels = array(
                                        'active' => __( '활성', 'jj-license-manager' ),
                                        'inactive' => __( '비활성', 'jj-license-manager' ),
                                        'expired' => __( '만료', 'jj-license-manager' ),
                                    );
                                    echo esc_html( $status_labels[ $license['status'] ] ?? $license['status'] );
                                    ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <?php
    }
}

// 초기화
if ( class_exists( 'WooCommerce' ) ) {
    new JJ_License_WooCommerce_Product();
}

