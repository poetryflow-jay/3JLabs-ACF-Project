<?php
/**
 * Plugin Name:       ACF CSS WooCommerce Toolkit - Advanced Commerce Styling
 * Plugin URI:        https://3j-labs.com
 * Description:       ACF CSS 플러그인의 WooCommerce 특화 확장입니다. 가격 표시 강화, 할인 계산기, 할부 표시, 장바구니 UI 개선 등 우커머스 스타일링과 기능을 제공합니다. ACF CSS Pro 버전 이상의 사용자를 위한 프리미엄 기능입니다.
 * Version:           2.3.0
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com
 * Text Domain:       acf-css-woocommerce-toolkit
 * Domain Path:       /languages
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * WC requires at least: 7.0
 * WC tested up to:   9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 플러그인 상수 정의
 */
define( 'ACF_CSS_WC_VERSION', '2.3.0' );
define( 'ACF_CSS_WC_PLUGIN_FILE', __FILE__ );
define( 'ACF_CSS_WC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ACF_CSS_WC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ACF_CSS_WC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * 메인 플러그인 클래스
 */
class ACF_CSS_WooCommerce_Toolkit {

    /**
     * 싱글톤 인스턴스
     *
     * @var ACF_CSS_WooCommerce_Toolkit
     */
    private static $instance = null;

    /**
     * 싱글톤 인스턴스 반환
     *
     * @return ACF_CSS_WooCommerce_Toolkit
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->check_dependencies();
        $this->init_hooks();
    }

    /**
     * 의존성 체크
     */
    private function check_dependencies() {
        // WooCommerce 활성화 체크
        if ( ! class_exists( 'WooCommerce' ) ) {
            add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
            return;
        }

        // ACF CSS 메인 플러그인 체크 (권장)
        if ( ! defined( 'JJ_STYLE_GUIDE_VERSION' ) ) {
            add_action( 'admin_notices', array( $this, 'acf_css_missing_notice' ) );
        }

        $this->load_dependencies();
    }

    /**
     * 의존성 파일 로드
     */
    private function load_dependencies() {
        // 가격 엔진
        require_once ACF_CSS_WC_PLUGIN_DIR . 'includes/class-price-engine.php';
        
        // 할인 계산기
        require_once ACF_CSS_WC_PLUGIN_DIR . 'includes/class-discount-calculator.php';
        
        // 빠른 편집 확장
        require_once ACF_CSS_WC_PLUGIN_DIR . 'includes/class-quick-edit-fields.php';
        
        // 장바구니 UI 개선
        require_once ACF_CSS_WC_PLUGIN_DIR . 'includes/class-cart-enhancer.php';
        
        // 숏코드
        require_once ACF_CSS_WC_PLUGIN_DIR . 'includes/class-shortcodes.php';
        
        // 상품 Q&A 시스템
        require_once ACF_CSS_WC_PLUGIN_DIR . 'includes/class-product-qa.php';
        
        // Product Page Styler (One-click templates)
        require_once ACF_CSS_WC_PLUGIN_DIR . 'includes/class-product-page-styler.php';
        
        // 관리자 설정
        if ( is_admin() ) {
            require_once ACF_CSS_WC_PLUGIN_DIR . 'admin/class-admin-settings.php';
        }
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        add_action( 'init', array( $this, 'load_textdomain' ) );
        add_action( 'init', array( $this, 'init_modules' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_head', array( $this, 'output_page_styles' ), 999 );
        
        // AJAX handlers for product page styler
        add_action( 'wp_ajax_jj_wc_apply_template', array( $this, 'ajax_apply_template' ) );
        add_action( 'wp_ajax_jj_wc_remove_template', array( $this, 'ajax_remove_template' ) );
        
        // HPOS 호환성 선언
        add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
    }

    /**
     * 모듈 초기화
     */
    public function init_modules() {
        // Q&A 시스템 초기화
        if ( class_exists( 'ACF_WC_Toolkit_Product_QA' ) ) {
            ACF_WC_Toolkit_Product_QA::instance()->init();
        }
    }

    /**
     * 텍스트 도메인 로드
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'acf-css-woocommerce-toolkit',
            false,
            dirname( ACF_CSS_WC_PLUGIN_BASENAME ) . '/languages/'
        );
    }

    /**
     * 프론트엔드 에셋 로드
     */
    public function enqueue_frontend_assets() {
        // 가격 스타일
        wp_enqueue_style(
            'acf-css-wc-price-styles',
            ACF_CSS_WC_PLUGIN_URL . 'assets/css/price-styles.css',
            array(),
            ACF_CSS_WC_VERSION
        );

        // 버튼 스타일
        wp_enqueue_style(
            'acf-css-wc-button-styles',
            ACF_CSS_WC_PLUGIN_URL . 'assets/css/button-styles.css',
            array(),
            ACF_CSS_WC_VERSION
        );
    }

    /**
     * 관리자 에셋 로드
     */
    public function enqueue_admin_assets( $hook ) {
        global $post;
        
        // 상품 편집 화면에서만 로드
        if ( $hook === 'post.php' || $hook === 'post-new.php' ) {
            if ( isset( $post ) && $post->post_type === 'product' ) {
                wp_enqueue_script(
                    'acf-css-wc-discount-calculator',
                    ACF_CSS_WC_PLUGIN_URL . 'assets/js/discount-calculator.js',
                    array( 'jquery' ),
                    ACF_CSS_WC_VERSION,
                    true
                );

                wp_localize_script( 'acf-css-wc-discount-calculator', 'acfCssWcAdmin', array(
                    'currency_symbol' => get_woocommerce_currency_symbol(),
                    'i18n' => array(
                        'calculating'    => __( '계산 중...', 'acf-css-woocommerce-toolkit' ),
                        'discount'       => __( '할인', 'acf-css-woocommerce-toolkit' ),
                        'saved'          => __( '절약', 'acf-css-woocommerce-toolkit' ),
                        'monthly'        => __( '월', 'acf-css-woocommerce-toolkit' ),
                        'months'         => __( '개월', 'acf-css-woocommerce-toolkit' ),
                        'regular_price'  => __( '정가', 'acf-css-woocommerce-toolkit' ),
                        'sale_price'     => __( '할인가', 'acf-css-woocommerce-toolkit' ),
                    ),
                ) );
            }
        }

        // 상품 목록 화면 (빠른 편집)
        if ( $hook === 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'product' ) {
            wp_enqueue_script(
                'acf-css-wc-quick-edit',
                ACF_CSS_WC_PLUGIN_URL . 'assets/js/quick-edit.js',
                array( 'jquery', 'inline-edit-post' ),
                ACF_CSS_WC_VERSION,
                true
            );
        }
    }

    /**
     * WooCommerce HPOS 호환성 선언
     */
    public function declare_hpos_compatibility() {
        if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 
                'custom_order_tables', 
                __FILE__, 
                true 
            );
        }
    }

    /**
     * Output product page styles to frontend
     */
    public function output_page_styles() {
        if ( class_exists( 'JJ_WC_Product_Page_Styler' ) ) {
            JJ_WC_Product_Page_Styler::instance()->output_styles();
        }
    }

    /**
     * AJAX: Apply style template
     */
    public function ajax_apply_template() {
        check_ajax_referer( 'jj_wc_styler_nonce', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-woocommerce-toolkit' ) ) );
        }
        
        $template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';
        
        if ( empty( $template_id ) ) {
            wp_send_json_error( array( 'message' => __( '템플릿 ID가 필요합니다.', 'acf-css-woocommerce-toolkit' ) ) );
        }
        
        if ( class_exists( 'JJ_WC_Product_Page_Styler' ) ) {
            $styler = JJ_WC_Product_Page_Styler::instance();
            $result = $styler->apply_template( $template_id );
            
            if ( $result ) {
                wp_send_json_success( array( 'message' => __( '템플릿이 적용되었습니다.', 'acf-css-woocommerce-toolkit' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( '템플릿 적용에 실패했습니다.', 'acf-css-woocommerce-toolkit' ) ) );
            }
        }
        
        wp_send_json_error( array( 'message' => __( 'Product Page Styler가 로드되지 않았습니다.', 'acf-css-woocommerce-toolkit' ) ) );
    }

    /**
     * AJAX: Remove style template
     */
    public function ajax_remove_template() {
        check_ajax_referer( 'jj_wc_styler_nonce', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-woocommerce-toolkit' ) ) );
        }
        
        $target = isset( $_POST['target'] ) ? sanitize_text_field( $_POST['target'] ) : '';
        
        if ( empty( $target ) ) {
            wp_send_json_error( array( 'message' => __( '타겟이 필요합니다.', 'acf-css-woocommerce-toolkit' ) ) );
        }
        
        if ( class_exists( 'JJ_WC_Product_Page_Styler' ) ) {
            $styler = JJ_WC_Product_Page_Styler::instance();
            $result = $styler->remove_template( $target );
            
            if ( $result ) {
                wp_send_json_success( array( 'message' => __( '템플릿이 제거되었습니다.', 'acf-css-woocommerce-toolkit' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( '적용된 템플릿이 없습니다.', 'acf-css-woocommerce-toolkit' ) ) );
            }
        }
        
        wp_send_json_error( array( 'message' => __( 'Product Page Styler가 로드되지 않았습니다.', 'acf-css-woocommerce-toolkit' ) ) );
    }

    /**
     * WooCommerce 미설치 알림
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p>
                <strong><?php esc_html_e( 'ACF CSS WooCommerce Toolkit', 'acf-css-woocommerce-toolkit' ); ?></strong>
                <?php esc_html_e( '을 사용하려면 WooCommerce가 설치되고 활성화되어야 합니다.', 'acf-css-woocommerce-toolkit' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * ACF CSS 미설치 알림 (권장)
     */
    public function acf_css_missing_notice() {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong><?php esc_html_e( 'ACF CSS WooCommerce Toolkit', 'acf-css-woocommerce-toolkit' ); ?></strong>:
                <?php esc_html_e( 'ACF CSS 메인 플러그인과 함께 사용하면 스타일 변수를 활용한 더욱 강력한 기능을 사용할 수 있습니다.', 'acf-css-woocommerce-toolkit' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Pro 사용자 여부 확인
     *
     * @return bool
     */
    public static function is_pro_user() {
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $license_type = JJ_STYLE_GUIDE_LICENSE_TYPE;
            return in_array( $license_type, array( 'BASIC', 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }
        
        if ( defined( 'JJ_STYLE_GUIDE_USER_TYPE' ) ) {
            $user_type = JJ_STYLE_GUIDE_USER_TYPE;
            return in_array( $user_type, array( 'PARTNER', 'MASTER' ), true );
        }
        
        return false;
    }
}

/**
 * 플러그인 활성화 시 실행
 */
function acf_css_wc_activate() {
    // 활성화 시 필요한 설정 초기화
    if ( ! get_option( 'acf_css_wc_settings' ) ) {
        update_option( 'acf_css_wc_settings', array(
            'enable_price_engine'        => true,
            'enable_discount_calculator' => true,
            'enable_quick_edit'          => true,
            'enable_cart_enhancer'       => true,
            'enable_installment_display' => true,
        ) );
    }
    
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'acf_css_wc_activate' );

/**
 * 플러그인 비활성화 시 실행
 */
function acf_css_wc_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'acf_css_wc_deactivate' );

/**
 * 플러그인 초기화
 */
function acf_css_wc_init() {
    return ACF_CSS_WooCommerce_Toolkit::instance();
}

// WooCommerce 로드 후 플러그인 초기화
add_action( 'plugins_loaded', 'acf_css_wc_init' );
