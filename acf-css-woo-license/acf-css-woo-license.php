<?php
/**
 * Plugin Name: ACF CSS License Bridge for WooCommerce
 * Plugin URI: https://3j-labs.com/
 * Description: WooCommerce 결제 완료 시 Neural Link 서버에 라이센스 발행 요청을 전송합니다. ACF CSS (Advanced Custom Fonts & Colors & Styles) 패밀리 플러그인으로, 개발사 내부에서만 사용하는 라이센스 및 업데이트 관리 플러그인입니다.
 * Version:           23.0.2
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI: https://3j-labs.com/
 * License: GPL-2.0+
 * Text Domain: acf-css-woo-license
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 플러그인 상수 정의
 */
if ( ! defined( 'ACF_CSS_WOO_LICENSE_VERSION' ) ) {
    define( 'ACF_CSS_WOO_LICENSE_VERSION', '23.0.2' ); // [v23.0.0] Major Version Release
}

/**
 * 에디션 설정 (빌드 시 자동 주입될 수 있음)
 */
if ( ! defined( 'ACF_CSS_WOO_LICENSE_EDITION' ) ) {
    define( 'ACF_CSS_WOO_LICENSE_EDITION', 'master' ); // master or partner
}

// WooCommerce 활성화 확인 (경고만 표시, 활성화는 허용)
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="notice notice-warning"><p>';
        echo '<strong>' . esc_html__( 'ACF CSS License Bridge for WooCommerce', 'acf-css-woo-license' ) . '</strong>: ';
        echo esc_html__( 'WooCommerce가 설치되고 활성화되어야 정상적으로 작동합니다.', 'acf-css-woo-license' );
        echo '</p></div>';
    } );
    // WooCommerce가 없어도 플러그인은 활성화 가능하도록 계속 진행
    // return; // 주석 처리하여 활성화 허용
}

/**
 * WooCommerce HPOS(High-Performance Order Storage) 호환성 선언
 */
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

// [v22.0.1] 클래스 중복 선언 방지
if ( ! class_exists( 'ACF_CSS_Woo_License' ) ) {

class ACF_CSS_Woo_License {

    /**
     * Neural Link 서버 URL
     */
    private $neural_link_url;

    /**
     * API Key
     */
    private $api_key;

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 인스턴스 반환
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
        $this->neural_link_url = get_option( 'acf_css_neural_link_url', '' );
        $this->api_key = get_option( 'acf_css_neural_link_api_key', '' );

        // WooCommerce 훅
        add_action( 'woocommerce_order_status_completed', array( $this, 'process_completed_order' ) );
        add_action( 'woocommerce_order_status_processing', array( $this, 'process_completed_order' ) );
        
        // 관리자 설정
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        
        // 상품 메타 필드
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_license_product_fields' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_license_product_fields' ) );
        
        // 주문 완료 페이지에 라이센스 키 표시
        add_action( 'woocommerce_thankyou', array( $this, 'display_license_key_on_thankyou' ) );
        add_action( 'woocommerce_order_details_after_order_table', array( $this, 'display_license_key_in_order_details' ) );
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'ACF CSS 라이센스 설정', 'acf-css-woo-license' ),
            '3J 라이센스',
            'manage_options',
            'acf-css-woo-license',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * 설정 등록
     */
    public function register_settings() {
        register_setting( 'acf_css_woo_license_settings', 'acf_css_neural_link_url' );
        register_setting( 'acf_css_woo_license_settings', 'acf_css_neural_link_api_key' );
        
        add_settings_section(
            'acf_css_neural_link_section',
            __( 'Neural Link 서버 설정', 'acf-css-woo-license' ),
            null,
            'acf-css-woo-license'
        );
        
        add_settings_field(
            'acf_css_neural_link_url',
            __( 'Neural Link URL', 'acf-css-woo-license' ),
            array( $this, 'render_url_field' ),
            'acf-css-woo-license',
            'acf_css_neural_link_section'
        );
        
        add_settings_field(
            'acf_css_neural_link_api_key',
            __( 'API Key', 'acf-css-woo-license' ),
            array( $this, 'render_api_key_field' ),
            'acf-css-woo-license',
            'acf_css_neural_link_section'
        );
    }

    /**
     * 설정 페이지 렌더링
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'ACF CSS 라이센스 설정', 'acf-css-woo-license' ); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields( 'acf_css_woo_license_settings' );
                do_settings_sections( 'acf-css-woo-license' );
                submit_button();
                ?>
            </form>
            
            <hr>
            
            <h2><?php _e( '연결 테스트', 'acf-css-woo-license' ); ?></h2>
            <p><?php _e( 'Neural Link 서버 연결 상태를 테스트합니다.', 'acf-css-woo-license' ); ?></p>
            <button type="button" class="button" id="acf-css-test-connection">
                <?php _e( '연결 테스트', 'acf-css-woo-license' ); ?>
            </button>
            <span id="acf-css-test-result" style="margin-left: 10px;"></span>
            
            <script>
            jQuery(document).ready(function($) {
                $('#acf-css-test-connection').on('click', function() {
                    var $btn = $(this);
                    var $result = $('#acf-css-test-result');
                    
                    $btn.prop('disabled', true);
                    $result.html('테스트 중...');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'acf_css_test_neural_link',
                            nonce: '<?php echo wp_create_nonce( 'acf_css_test_neural_link' ); ?>'
                        },
                        success: function(response) {
                            $btn.prop('disabled', false);
                            if (response.success) {
                                $result.html('<span style="color: green;">✓ ' + response.data.message + '</span>');
                            } else {
                                $result.html('<span style="color: red;">✗ ' + response.data.message + '</span>');
                            }
                        },
                        error: function() {
                            $btn.prop('disabled', false);
                            $result.html('<span style="color: red;">✗ 연결 실패</span>');
                        }
                    });
                });
            });
            </script>
        </div>
        <?php
    }

    /**
     * URL 필드 렌더링
     */
    public function render_url_field() {
        $url = get_option( 'acf_css_neural_link_url', '' );
        ?>
        <input type="url" name="acf_css_neural_link_url" value="<?php echo esc_attr( $url ); ?>" class="regular-text" placeholder="https://your-neural-link-server.com">
        <p class="description"><?php _e( 'Neural Link 서버가 설치된 WordPress 사이트 URL', 'acf-css-woo-license' ); ?></p>
        <?php
    }

    /**
     * API Key 필드 렌더링
     */
    public function render_api_key_field() {
        $api_key = get_option( 'acf_css_neural_link_api_key', '' );
        ?>
        <input type="password" name="acf_css_neural_link_api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text">
        <p class="description"><?php _e( 'Neural Link 서버에서 발급받은 API Key', 'acf-css-woo-license' ); ?></p>
        <?php
    }

    /**
     * 상품 편집 페이지에 라이센스 필드 추가
     */
    public function add_license_product_fields() {
        global $post;
        
        echo '<div class="options_group">';
        
        woocommerce_wp_checkbox( array(
            'id'          => '_acf_css_license_product',
            'label'       => __( 'ACF CSS 라이센스 상품', 'acf-css-woo-license' ),
            'description' => __( '이 상품 구매 시 ACF CSS 라이센스를 발행합니다.', 'acf-css-woo-license' ),
        ) );
        
        woocommerce_wp_select( array(
            'id'          => '_acf_css_license_edition',
            'label'       => __( '라이센스 에디션', 'acf-css-woo-license' ),
            'options'     => array(
                ''          => __( '선택하세요', 'acf-css-woo-license' ),
                'free'      => __( 'Free', 'acf-css-woo-license' ),
                'basic'     => __( 'Basic (PRO)', 'acf-css-woo-license' ),
                'premium'   => __( 'Premium (PRO)', 'acf-css-woo-license' ),
                'unlimited' => __( 'Unlimited (PRO)', 'acf-css-woo-license' ),
                'partner'   => __( 'Partner', 'acf-css-woo-license' ),
                'master'    => __( 'Master', 'acf-css-woo-license' ),
            ),
        ) );
        
        woocommerce_wp_text_input( array(
            'id'          => '_acf_css_license_duration_days',
            'label'       => __( '라이센스 기간 (일)', 'acf-css-woo-license' ),
            'type'        => 'number',
            'desc_tip'    => true,
            'description' => __( '0 = 영구 라이센스, 365 = 1년', 'acf-css-woo-license' ),
        ) );
        
        woocommerce_wp_text_input( array(
            'id'          => '_acf_css_license_site_limit',
            'label'       => __( '사이트 수 제한', 'acf-css-woo-license' ),
            'type'        => 'number',
            'desc_tip'    => true,
            'description' => __( '0 = 무제한', 'acf-css-woo-license' ),
        ) );
        
        echo '</div>';
    }

    /**
     * 라이센스 상품 필드 저장
     */
    public function save_license_product_fields( $post_id ) {
        $is_license = isset( $_POST['_acf_css_license_product'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_acf_css_license_product', $is_license );
        
        if ( isset( $_POST['_acf_css_license_edition'] ) ) {
            update_post_meta( $post_id, '_acf_css_license_edition', sanitize_text_field( $_POST['_acf_css_license_edition'] ) );
        }
        
        if ( isset( $_POST['_acf_css_license_duration_days'] ) ) {
            update_post_meta( $post_id, '_acf_css_license_duration_days', absint( $_POST['_acf_css_license_duration_days'] ) );
        }
        
        if ( isset( $_POST['_acf_css_license_site_limit'] ) ) {
            update_post_meta( $post_id, '_acf_css_license_site_limit', absint( $_POST['_acf_css_license_site_limit'] ) );
        }
    }

    /**
     * 주문 완료 시 라이센스 발행
     */
    public function process_completed_order( $order_id ) {
        $order = wc_get_order( $order_id );
        
        if ( ! $order ) {
            return;
        }
        
        // 이미 처리된 주문인지 확인
        if ( $order->get_meta( '_acf_css_license_issued' ) === 'yes' ) {
            return;
        }
        
        $licenses_issued = array();
        
        foreach ( $order->get_items() as $item_id => $item ) {
            $product_id = $item->get_product_id();
            
            // 라이센스 상품인지 확인
            if ( get_post_meta( $product_id, '_acf_css_license_product', true ) !== 'yes' ) {
                continue;
            }
            
            $edition = get_post_meta( $product_id, '_acf_css_license_edition', true );
            $duration = (int) get_post_meta( $product_id, '_acf_css_license_duration_days', true );
            $site_limit = (int) get_post_meta( $product_id, '_acf_css_license_site_limit', true );
            
            // Neural Link에 라이센스 발행 요청
            $license_key = $this->issue_license(
                $order->get_billing_email(),
                $edition,
                $duration,
                $site_limit,
                $order_id
            );
            
            if ( $license_key ) {
                $licenses_issued[] = array(
                    'product_name' => $item->get_name(),
                    'edition'      => $edition,
                    'license_key'  => $license_key,
                );
                
                // 주문 항목에 라이센스 키 저장
                wc_update_order_item_meta( $item_id, '_acf_css_license_key', $license_key );
            }
        }
        
        if ( ! empty( $licenses_issued ) ) {
            // 주문에 발행 완료 표시
            $order->update_meta_data( '_acf_css_license_issued', 'yes' );
            $order->update_meta_data( '_acf_css_licenses', $licenses_issued );
            $order->save();
            
            // 이메일로 라이센스 키 전송
            $this->send_license_email( $order, $licenses_issued );
            
            // 주문 노트 추가
            $order->add_order_note( 
                sprintf( 
                    __( 'ACF CSS 라이센스 %d개 발행 완료', 'acf-css-woo-license' ), 
                    count( $licenses_issued ) 
                ) 
            );
        }
    }

    /**
     * Neural Link에 라이센스 발행 요청
     */
    private function issue_license( $email, $edition, $duration, $site_limit, $order_id ) {
        if ( empty( $this->neural_link_url ) || empty( $this->api_key ) ) {
            error_log( 'ACF CSS Woo License: Neural Link 설정이 완료되지 않았습니다.' );
            return false;
        }
        
        $endpoint = trailingslashit( $this->neural_link_url ) . 'wp-json/acf-neural-link/v1/license/issue';
        
        $response = wp_remote_post( $endpoint, array(
            'timeout' => 30,
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
            ),
            'body'    => wp_json_encode( array(
                'email'       => $email,
                'edition'     => $edition,
                'duration'    => $duration,
                'site_limit'  => $site_limit,
                'order_id'    => $order_id,
                'source'      => 'woocommerce',
            ) ),
            'sslverify' => true,
        ) );
        
        if ( is_wp_error( $response ) ) {
            error_log( 'ACF CSS Woo License: Neural Link 연결 실패 - ' . $response->get_error_message() );
            return false;
        }
        
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        
        if ( isset( $body['success'] ) && $body['success'] && isset( $body['license_key'] ) ) {
            return $body['license_key'];
        }
        
        error_log( 'ACF CSS Woo License: 라이센스 발행 실패 - ' . print_r( $body, true ) );
        return false;
    }

    /**
     * 라이센스 키 이메일 전송
     */
    private function send_license_email( $order, $licenses ) {
        $to = $order->get_billing_email();
        $subject = sprintf( __( '[ACF CSS Manager] 라이센스 키 발급 완료 - 주문 #%s', 'acf-css-woo-license' ), $order->get_id() );
        
        $message = sprintf( __( '안녕하세요 %s님,', 'acf-css-woo-license' ), $order->get_billing_first_name() ) . "\n\n";
        $message .= __( 'ACF CSS Manager 라이센스가 성공적으로 발급되었습니다.', 'acf-css-woo-license' ) . "\n\n";
        
        foreach ( $licenses as $license ) {
            $message .= sprintf( "상품: %s\n", $license['product_name'] );
            $message .= sprintf( "에디션: %s\n", strtoupper( $license['edition'] ) );
            $message .= sprintf( "라이센스 키: %s\n", $license['license_key'] );
            $message .= "\n";
        }
        
        $message .= __( '라이센스 활성화 방법:', 'acf-css-woo-license' ) . "\n";
        $message .= "1. WordPress 관리자 > 설정 > ACF CSS Manager\n";
        $message .= "2. '라이센스' 탭 클릭\n";
        $message .= "3. 위 라이센스 키 입력 후 '활성화' 버튼 클릭\n\n";
        
        $message .= __( '감사합니다.', 'acf-css-woo-license' ) . "\n";
        $message .= "J&J Labs";
        
        wp_mail( $to, $subject, $message );
    }

    /**
     * 주문 완료 페이지에 라이센스 키 표시
     */
    public function display_license_key_on_thankyou( $order_id ) {
        $order = wc_get_order( $order_id );
        
        if ( ! $order ) {
            return;
        }
        
        $licenses = $order->get_meta( '_acf_css_licenses' );
        
        if ( empty( $licenses ) ) {
            return;
        }
        
        ?>
        <section class="woocommerce-acf-css-licenses">
            <h2><?php _e( '🔑 ACF CSS 라이센스 키', 'acf-css-woo-license' ); ?></h2>
            <p><?php _e( '아래 라이센스 키를 복사하여 플러그인에서 활성화하세요.', 'acf-css-woo-license' ); ?></p>
            
            <table class="woocommerce-table shop_table">
                <thead>
                    <tr>
                        <th><?php _e( '상품', 'acf-css-woo-license' ); ?></th>
                        <th><?php _e( '에디션', 'acf-css-woo-license' ); ?></th>
                        <th><?php _e( '라이센스 키', 'acf-css-woo-license' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $licenses as $license ) : ?>
                    <tr>
                        <td><?php echo esc_html( $license['product_name'] ); ?></td>
                        <td><?php echo esc_html( strtoupper( $license['edition'] ) ); ?></td>
                        <td>
                            <code style="font-size: 1.1em; padding: 5px 10px; background: #f0f0f0; user-select: all;">
                                <?php echo esc_html( $license['license_key'] ); ?>
                            </code>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <?php
    }

    /**
     * 주문 상세 페이지에 라이센스 키 표시
     */
    public function display_license_key_in_order_details( $order ) {
        $licenses = $order->get_meta( '_acf_css_licenses' );
        
        if ( empty( $licenses ) ) {
            return;
        }
        
        ?>
        <section class="woocommerce-acf-css-licenses">
            <h2><?php _e( '🔑 ACF CSS 라이센스 키', 'acf-css-woo-license' ); ?></h2>
            
            <table class="woocommerce-table shop_table">
                <thead>
                    <tr>
                        <th><?php _e( '상품', 'acf-css-woo-license' ); ?></th>
                        <th><?php _e( '라이센스 키', 'acf-css-woo-license' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $licenses as $license ) : ?>
                    <tr>
                        <td><?php echo esc_html( $license['product_name'] ); ?></td>
                        <td><code><?php echo esc_html( $license['license_key'] ); ?></code></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <?php
    }
} // End of class ACF_CSS_Woo_License

} // End of class_exists check

// 초기화
if ( class_exists( 'ACF_CSS_Woo_License' ) ) {
    add_action( 'plugins_loaded', array( 'ACF_CSS_Woo_License', 'instance' ) );
}

// 플러그인 목록 페이지 향상 초기화
add_action( 'plugins_loaded', function () {
    $plugin_list_enhancer_file = dirname( dirname( __FILE__ ) ) . '/acf-css-really-simple-style-management-center-master/includes/class-jj-plugin-list-enhancer.php';
    if ( file_exists( $plugin_list_enhancer_file ) ) {
        require_once $plugin_list_enhancer_file;
    }
    
    if ( class_exists( 'JJ_Plugin_List_Enhancer' ) ) {
        $enhancer = new JJ_Plugin_List_Enhancer();
        $enhancer->init( array(
            'plugin_file' => __FILE__,
            'plugin_name' => 'ACF CSS License Bridge for WooCommerce',
            'settings_url' => admin_url( 'admin.php?page=acf-css-woo-license' ),
            'text_domain' => 'acf-css-woo-license',
            'version_constant' => 'ACF_CSS_WOO_LICENSE_VERSION',
            'license_constant' => 'ACF_CSS_WOO_LICENSE_EDITION',
            'upgrade_url' => 'https://3j-labs.com/',
            'docs_url' => admin_url( 'admin.php?page=acf-css-woo-license' ),
            'support_url' => 'https://3j-labs.com/support',
        ) );
    }
}, 25 );

// AJAX 핸들러: 연결 테스트
add_action( 'wp_ajax_acf_css_test_neural_link', function() {
    check_ajax_referer( 'acf_css_test_neural_link', 'nonce' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
    }
    
    $url = get_option( 'acf_css_neural_link_url', '' );
    $api_key = get_option( 'acf_css_neural_link_api_key', '' );
    
    if ( empty( $url ) || empty( $api_key ) ) {
        wp_send_json_error( array( 'message' => 'Neural Link URL 또는 API Key가 설정되지 않았습니다.' ) );
    }
    
    $endpoint = trailingslashit( $url ) . 'wp-json/acf-neural-link/v1/ping';
    
    $response = wp_remote_get( $endpoint, array(
        'timeout' => 10,
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
        ),
        'sslverify' => true,
    ) );
    
    if ( is_wp_error( $response ) ) {
        wp_send_json_error( array( 'message' => '연결 실패: ' . $response->get_error_message() ) );
    }
    
    $code = wp_remote_retrieve_response_code( $response );
    
    if ( $code === 200 ) {
        wp_send_json_success( array( 'message' => 'Neural Link 서버 연결 성공!' ) );
    } else {
        wp_send_json_error( array( 'message' => '서버 응답 오류: HTTP ' . $code ) );
    }
} );

// 추가 모듈 로드
if ( file_exists( __DIR__ . '/includes/class-portone-webhook.php' ) ) {
    require_once __DIR__ . '/includes/class-portone-webhook.php';
}

if ( file_exists( __DIR__ . '/includes/class-woo-myaccount-licenses.php' ) ) {
    require_once __DIR__ . '/includes/class-woo-myaccount-licenses.php';
}

if ( file_exists( __DIR__ . '/includes/class-coupon-generator.php' ) ) {
    require_once __DIR__ . '/includes/class-coupon-generator.php';
}

if ( file_exists( __DIR__ . '/includes/class-jj-woo-license-dashboard.php' ) ) {
    require_once __DIR__ . '/includes/class-jj-woo-license-dashboard.php';
}
