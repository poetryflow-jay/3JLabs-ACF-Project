<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 24] WooCommerce 라이센스 대시보드
 * - 마스터: 전체 판매 및 라이센스 현황 관리
 * - 파트너: 본인의 판매 내역 및 정산 현황 확인
 *
 * @since 20.2.2
 */
class JJ_Woo_License_Dashboard {

    private static $instance = null;
    private $edition = 'master';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        if ( defined( 'ACF_CSS_WOO_LICENSE_EDITION' ) ) {
            $this->edition = ACF_CSS_WOO_LICENSE_EDITION;
        }
        
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action( 'admin_menu', array( $this, 'add_dashboard_menu' ), 20 );
    }

    /**
     * 대시보드 메뉴 추가
     */
    public function add_dashboard_menu() {
        $title = ( 'master' === $this->edition ) ? __( '판매 & 라이센스 센터', 'acf-css-woo-license' ) : __( '판매 현황 (파트너)', 'acf-css-woo-license' );
        
        add_submenu_page(
            'acf-css-woo-license',
            $title,
            $title,
            'manage_options',
            'acf-css-woo-license-dashboard',
            array( $this, 'render_dashboard' )
        );
    }

    /**
     * 대시보드 렌더링
     */
    public function render_dashboard() {
        ?>
        <div class="wrap">
            <h1><?php echo ( 'master' === $this->edition ) ? esc_html__( '🚀 3J Labs 판매 & 라이센스 센터 (Master)', 'acf-css-woo-license' ) : esc_html__( '📊 나의 판매 현황 (Partner)', 'acf-css-woo-license' ); ?></h1>
            
            <div class="jj-dashboard-stats" style="display: flex; gap: 20px; margin-top: 20px;">
                <div class="card" style="flex: 1; padding: 20px; text-align: center; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); background: #fff;">
                    <h3><?php esc_html_e( '전체 판매량 (누적)', 'acf-css-woo-license' ); ?></h3>
                    <p style="font-size: 2.5em; font-weight: bold; color: #2271b1; margin: 10px 0;"><?php echo $this->get_total_sales_count(); ?>개</p>
                </div>
                <div class="card" style="flex: 1; padding: 20px; text-align: center; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); background: #fff;">
                    <h3><?php esc_html_e( '이번 달 판매량', 'acf-css-woo-license' ); ?></h3>
                    <p style="font-size: 2.5em; font-weight: bold; color: #00a32a; margin: 10px 0;"><?php echo $this->get_monthly_sales_count(); ?>개</p>
                </div>
                <?php if ( 'partner' === $this->edition ) : ?>
                <div class="card" style="flex: 1; padding: 20px; text-align: center; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); background: #fff;">
                    <h3><?php esc_html_e( '나의 예상 수익', 'acf-css-woo-license' ); ?></h3>
                    <p style="font-size: 2.5em; font-weight: bold; color: #d63638; margin: 10px 0;"><?php echo number_format( $this->get_partner_earnings() ); ?>원</p>
                </div>
                <?php else : ?>
                <div class="card" style="flex: 1; padding: 20px; text-align: center; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); background: #fff;">
                    <h3><?php esc_html_e( '총 매출액', 'acf-css-woo-license' ); ?></h3>
                    <p style="font-size: 2.5em; font-weight: bold; color: #d63638; margin: 10px 0;"><?php echo number_format( $this->get_total_revenue() ); ?>원</p>
                </div>
                <?php endif; ?>
            </div>

            <div class="jj-recent-orders" style="margin-top: 40px;">
                <h2 style="margin-bottom: 15px;"><?php esc_html_e( '최근 라이센스 발행 내역', 'acf-css-woo-license' ); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 80px;"><?php esc_html_e( '주문 ID', 'acf-css-woo-license' ); ?></th>
                            <th><?php esc_html_e( '고객 (Email)', 'acf-css-woo-license' ); ?></th>
                            <th style="width: 120px;"><?php esc_html_e( '에디션', 'acf-css-woo-license' ); ?></th>
                            <th><?php esc_html_e( '라이센스 키', 'acf-css-woo-license' ); ?></th>
                            <th style="width: 150px;"><?php esc_html_e( '발행일', 'acf-css-woo-license' ); ?></th>
                            <th style="width: 100px;"><?php esc_html_e( '상태', 'acf-css-woo-license' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $orders = $this->get_recent_orders_with_licenses();
                        if ( empty( $orders ) ) : ?>
                            <tr><td colspan="6" style="text-align: center; padding: 20px;"><?php esc_html_e( '발행된 라이센스 내역이 없습니다.', 'acf-css-woo-license' ); ?></td></tr>
                        <?php else : 
                            foreach ( $orders as $order ) : ?>
                            <tr>
                                <td><strong>#<?php echo esc_html( $order['order_id'] ); ?></strong></td>
                                <td><?php echo esc_html( $order['customer'] ); ?></td>
                                <td><span class="jj-pill edition-<?php echo esc_attr( $order['edition'] ); ?>"><?php echo esc_html( strtoupper( $order['edition'] ) ); ?></span></td>
                                <td><code><?php echo esc_html( $order['license_key'] ); ?></code></td>
                                <td><?php echo esc_html( $order['date'] ); ?></td>
                                <td><span style="color: #00a32a;">✅ <?php esc_html_e( '정상', 'acf-css-woo-license' ); ?></span></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ( 'master' === $this->edition ) : ?>
            <div class="jj-partner-stats" style="margin-top: 40px;">
                <h2 style="margin-bottom: 15px;"><?php esc_html_e( '파트너별 판매 현황', 'acf-css-woo-license' ); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( '파트너 명', 'acf-css-woo-license' ); ?></th>
                            <th><?php esc_html_e( '누적 판매량', 'acf-css-woo-license' ); ?></th>
                            <th><?php esc_html_e( '이번 달 판매', 'acf-css-woo-license' ); ?></th>
                            <th><?php esc_html_e( '누적 매출', 'acf-css-woo-license' ); ?></th>
                            <th><?php esc_html_e( '정산 대상 금액', 'acf-css-woo-license' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Partner A</strong></td>
                            <td>45개</td>
                            <td>8개</td>
                            <td>1,350,000원</td>
                            <td>405,000원</td>
                        </tr>
                        <tr>
                            <td><strong>Partner B</strong></td>
                            <td>12개</td>
                            <td>2개</td>
                            <td>360,000원</td>
                            <td>108,000원</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <style>
            .jj-pill { background: #f0f0f1; padding: 4px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; color: #50575e; }
            .edition-unlimited { background: #e67e22; color: #fff; }
            .edition-premium { background: #9b59b6; color: #fff; }
            .edition-master { background: #f1c40f; color: #000; }
            .card h3 { margin: 0; font-size: 14px; color: #646970; }
        </style>
        <?php
    }

    /**
     * 최근 주문 및 라이센스 정보 가져오기 (WooCommerce 쿼리)
     */
    private function get_recent_orders_with_licenses() {
        if ( ! class_exists( 'WooCommerce' ) ) return array();

        $args = array(
            'limit' => 20,
            'status' => array( 'completed', 'processing' ),
            'orderby' => 'date',
            'order' => 'DESC',
        );

        // 파트너 에디션인 경우 본인의 주문만 필터링 (메타 쿼리 예시)
        if ( 'partner' === $this->edition ) {
            $current_user_id = get_current_user_id();
            $args['meta_key'] = '_partner_id';
            $args['meta_value'] = $current_user_id;
        }

        $orders = wc_get_orders( $args );
        $results = array();

        foreach ( $orders as $order ) {
            $licenses = $order->get_meta( '_acf_css_licenses' );
            if ( ! empty( $licenses ) ) {
                foreach ( $licenses as $license ) {
                    $results[] = array(
                        'order_id' => $order->get_id(),
                        'customer' => $order->get_billing_email(),
                        'edition'  => isset( $license['edition'] ) ? $license['edition'] : 'N/A',
                        'license_key' => isset( $license['license_key'] ) ? $license['license_key'] : 'N/A',
                        'date' => $order->get_date_created()->date( 'Y-m-d H:i' ),
                    );
                }
            }
        }

        return $results;
    }

    // 통계 함수들 (실제 구현 시에는 wc_get_orders 통계 쿼리 사용)
    private function get_total_sales_count() { return 156; }
    private function get_monthly_sales_count() { return 24; }
    private function get_total_revenue() { return 4680000; }
    private function get_partner_earnings() { return 125000; }
}

// 초기화
JJ_Woo_License_Dashboard::instance();
