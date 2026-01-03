<?php
/**
 * JJ Master WooCommerce - 마스터 버전 통합 우커머스 모듈
 * 
 * ACF CSS WooCommerce Toolkit의 핵심 기능을 마스터 버전에 통합합니다.
 * 
 * @since 13.4.4
 * @package ACF_CSS
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Master_WooCommerce {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // WooCommerce 필수
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        $this->init();
    }

    private function init() {
        // 가격 표시 강화
        add_filter( 'woocommerce_get_price_html', array( $this, 'enhance_price_display' ), 10, 2 );
        
        // 할인 계산기
        add_action( 'woocommerce_single_product_summary', array( $this, 'show_discount_info' ), 25 );
        
        // 할부 표시
        add_action( 'woocommerce_single_product_summary', array( $this, 'show_installment_info' ), 26 );
        
        // 장바구니 UI 개선
        add_action( 'woocommerce_cart_actions', array( $this, 'add_cart_enhancements' ) );
        
        // 빠른 편집 필드
        add_action( 'woocommerce_product_quick_edit_end', array( $this, 'add_quick_edit_fields' ) );
        
        // 관리자 메뉴
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );
        
        // ACF CSS 스타일 변수 연동
        add_filter( 'jj_css_variables', array( $this, 'add_wc_css_variables' ) );
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_submenu_page(
            'jj-admin-center',
            __( '우커머스 스타일', 'acf-css-really-simple-style-management-center' ),
            __( '🛒 우커머스', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            'jj-woocommerce-toolkit',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * 관리자 페이지 렌더링
     */
    public function render_admin_page() {
        $options = get_option( 'jj_wc_toolkit_options', array() );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'ACF CSS WooCommerce Toolkit', 'acf-css-really-simple-style-management-center' ); ?></h1>
            
            <div class="notice notice-info">
                <p><?php esc_html_e( '우커머스 상품 페이지의 가격, 할인, 스타일을 ACF CSS와 연동하여 관리합니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>

            <form method="post" action="options.php">
                <?php settings_fields( 'jj_wc_toolkit_options' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e( '할인율 표시', 'acf-css-really-simple-style-management-center' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="jj_wc_toolkit_options[show_discount_percent]" value="1" <?php checked( ! empty( $options['show_discount_percent'] ) ); ?>>
                                <?php esc_html_e( '세일 가격 옆에 할인율 표시', 'acf-css-really-simple-style-management-center' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( '할부 정보 표시', 'acf-css-really-simple-style-management-center' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="jj_wc_toolkit_options[show_installment]" value="1" <?php checked( ! empty( $options['show_installment'] ) ); ?>>
                                <?php esc_html_e( '상품 상세 페이지에 할부 정보 표시', 'acf-css-really-simple-style-management-center' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( '할부 개월', 'acf-css-really-simple-style-management-center' ); ?></th>
                        <td>
                            <input type="number" name="jj_wc_toolkit_options[installment_months]" value="<?php echo esc_attr( $options['installment_months'] ?? 12 ); ?>" min="2" max="60">
                            <?php esc_html_e( '개월', 'acf-css-really-simple-style-management-center' ); ?>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * 가격 표시 강화
     */
    public function enhance_price_display( $price_html, $product ) {
        $options = get_option( 'jj_wc_toolkit_options', array() );
        
        if ( empty( $options['show_discount_percent'] ) ) {
            return $price_html;
        }

        if ( $product->is_on_sale() ) {
            $regular = floatval( $product->get_regular_price() );
            $sale = floatval( $product->get_sale_price() );
            
            if ( $regular > 0 ) {
                $discount = round( ( ( $regular - $sale ) / $regular ) * 100 );
                $price_html .= sprintf( 
                    '<span class="jj-discount-badge" style="background: var(--jj-primary-color, #e53935); color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 12px; margin-left: 5px;">-%d%%</span>',
                    $discount
                );
            }
        }

        return $price_html;
    }

    /**
     * 할인 정보 표시
     */
    public function show_discount_info() {
        global $product;
        
        if ( ! $product || ! $product->is_on_sale() ) {
            return;
        }

        $regular = floatval( $product->get_regular_price() );
        $sale = floatval( $product->get_sale_price() );
        
        if ( $regular > 0 ) {
            $saved = $regular - $sale;
            echo '<div class="jj-wc-discount-info" style="color: var(--jj-success-color, #28a745); font-weight: bold; margin: 10px 0;">';
            echo sprintf( 
                __( '💰 %s 절약!', 'acf-css-really-simple-style-management-center' ),
                wc_price( $saved )
            );
            echo '</div>';
        }
    }

    /**
     * 할부 정보 표시
     */
    public function show_installment_info() {
        global $product;
        
        $options = get_option( 'jj_wc_toolkit_options', array() );
        
        if ( empty( $options['show_installment'] ) || ! $product ) {
            return;
        }

        $price = floatval( $product->get_price() );
        $months = intval( $options['installment_months'] ?? 12 );
        
        if ( $price > 0 && $months > 1 ) {
            $monthly = $price / $months;
            echo '<div class="jj-wc-installment-info" style="background: var(--jj-secondary-color, #f8f9fa); padding: 10px; border-radius: 5px; margin: 10px 0;">';
            echo sprintf(
                __( '📅 무이자 %d개월 할부 시 월 %s', 'acf-css-really-simple-style-management-center' ),
                $months,
                wc_price( $monthly )
            );
            echo '</div>';
        }
    }

    /**
     * 장바구니 UI 개선
     */
    public function add_cart_enhancements() {
        // 장바구니 비우기 버튼
        echo '<a href="' . esc_url( wc_get_cart_url() . '?empty-cart=true' ) . '" class="button jj-empty-cart-btn" style="background: var(--jj-danger-color, #dc3545); color: #fff; border: none;">';
        echo esc_html__( '🗑️ 장바구니 비우기', 'acf-css-really-simple-style-management-center' );
        echo '</a>';
    }

    /**
     * 빠른 편집 필드 추가
     */
    public function add_quick_edit_fields() {
        ?>
        <div class="inline-edit-group">
            <label class="alignleft">
                <span class="title"><?php esc_html_e( '할부 표시', 'acf-css-really-simple-style-management-center' ); ?></span>
                <input type="checkbox" name="_jj_show_installment" value="1">
            </label>
        </div>
        <?php
    }

    /**
     * WooCommerce CSS 변수 추가
     */
    public function add_wc_css_variables( $vars ) {
        $wc_vars = array(
            '--jj-wc-sale-color' => '#e53935',
            '--jj-wc-stock-low-color' => '#ff9800',
            '--jj-wc-stock-out-color' => '#9e9e9e',
            '--jj-wc-button-cart-bg' => 'var(--jj-primary-color, #2196f3)',
            '--jj-wc-button-checkout-bg' => 'var(--jj-success-color, #28a745)',
        );

        return array_merge( $vars, $wc_vars );
    }
}
