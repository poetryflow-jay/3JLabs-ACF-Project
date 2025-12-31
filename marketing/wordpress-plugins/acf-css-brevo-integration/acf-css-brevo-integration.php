<?php
/**
 * Plugin Name: ACF CSS Brevo Integration
 * Plugin URI: https://j-j-labs.com/acf-css
 * Description: WooCommerce와 Brevo를 연동하여 이메일 마케팅 자동화를 지원합니다.
 * Version:           2.0.0
 * Author:            3J Labs
 * Created by:        Jay & Jason & Jenny
 * Author URI: https://j-j-labs.com
 * License: GPL-2.0+
 * Text Domain: acf-css-brevo
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_CSS_Brevo_Integration {

    /**
     * Brevo API URL
     */
    private $api_url = 'https://api.brevo.com/v3';

    /**
     * API Key
     */
    private $api_key;

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 리스트 ID 설정
     */
    private $lists = array(
        'newsletter'  => 1,  // 뉴스레터 구독자
        'customers'   => 2,  // 구매 고객
        'beta'        => 3,  // 베타 테스터
        'partners'    => 4,  // 파트너
    );

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
        $this->api_key = get_option( 'acf_css_brevo_api_key', '' );

        // 관리자 메뉴
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        // WooCommerce 훅
        add_action( 'woocommerce_order_status_completed', array( $this, 'on_order_completed' ), 20 );
        add_action( 'woocommerce_created_customer', array( $this, 'on_customer_created' ), 10, 3 );

        // 뉴스레터 폼 숏코드
        add_shortcode( 'acf_css_newsletter', array( $this, 'newsletter_shortcode' ) );
        
        // 베타 신청 폼 숏코드
        add_shortcode( 'acf_css_beta_signup', array( $this, 'beta_signup_shortcode' ) );

        // AJAX 핸들러
        add_action( 'wp_ajax_acf_css_subscribe', array( $this, 'ajax_subscribe' ) );
        add_action( 'wp_ajax_nopriv_acf_css_subscribe', array( $this, 'ajax_subscribe' ) );
        
        add_action( 'wp_ajax_acf_css_beta_signup', array( $this, 'ajax_beta_signup' ) );
        add_action( 'wp_ajax_nopriv_acf_css_beta_signup', array( $this, 'ajax_beta_signup' ) );

        // 스크립트/스타일
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        // 라이센스 만료 알림 스케줄
        add_action( 'acf_css_check_expiring_licenses', array( $this, 'send_expiry_notifications' ) );
        
        if ( ! wp_next_scheduled( 'acf_css_check_expiring_licenses' ) ) {
            wp_schedule_event( time(), 'daily', 'acf_css_check_expiring_licenses' );
        }
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_options_page(
            __( 'Brevo 연동 설정', 'acf-css-brevo' ),
            __( 'Brevo 연동', 'acf-css-brevo' ),
            'manage_options',
            'acf-css-brevo',
            array( $this, 'settings_page' )
        );
    }

    /**
     * 설정 등록
     */
    public function register_settings() {
        register_setting( 'acf_css_brevo_settings', 'acf_css_brevo_api_key' );
        register_setting( 'acf_css_brevo_settings', 'acf_css_brevo_lists' );
        register_setting( 'acf_css_brevo_settings', 'acf_css_brevo_templates' );
    }

    /**
     * 설정 페이지
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'ACF CSS Brevo 연동 설정', 'acf-css-brevo' ); ?></h1>
            
            <form method="post" action="options.php">
                <?php settings_fields( 'acf_css_brevo_settings' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Brevo API Key', 'acf-css-brevo' ); ?></th>
                        <td>
                            <input type="password" name="acf_css_brevo_api_key" 
                                   value="<?php echo esc_attr( get_option( 'acf_css_brevo_api_key' ) ); ?>" 
                                   class="regular-text" />
                            <p class="description">
                                <?php esc_html_e( 'Brevo 관리자 > SMTP & API > API Keys에서 발급받으세요.', 'acf-css-brevo' ); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php esc_html_e( '뉴스레터 리스트 ID', 'acf-css-brevo' ); ?></th>
                        <td>
                            <?php $lists = get_option( 'acf_css_brevo_lists', array() ); ?>
                            <input type="number" name="acf_css_brevo_lists[newsletter]" 
                                   value="<?php echo esc_attr( $lists['newsletter'] ?? 1 ); ?>" 
                                   class="small-text" />
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php esc_html_e( '구매 고객 리스트 ID', 'acf-css-brevo' ); ?></th>
                        <td>
                            <input type="number" name="acf_css_brevo_lists[customers]" 
                                   value="<?php echo esc_attr( $lists['customers'] ?? 2 ); ?>" 
                                   class="small-text" />
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php esc_html_e( '베타 테스터 리스트 ID', 'acf-css-brevo' ); ?></th>
                        <td>
                            <input type="number" name="acf_css_brevo_lists[beta]" 
                                   value="<?php echo esc_attr( $lists['beta'] ?? 3 ); ?>" 
                                   class="small-text" />
                        </td>
                    </tr>
                </table>
                
                <h2><?php esc_html_e( '이메일 템플릿 ID', 'acf-css-brevo' ); ?></h2>
                
                <table class="form-table">
                    <?php $templates = get_option( 'acf_css_brevo_templates', array() ); ?>
                    <tr>
                        <th scope="row"><?php esc_html_e( '환영 이메일', 'acf-css-brevo' ); ?></th>
                        <td>
                            <input type="number" name="acf_css_brevo_templates[welcome]" 
                                   value="<?php echo esc_attr( $templates['welcome'] ?? '' ); ?>" 
                                   class="small-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( '라이센스 발급', 'acf-css-brevo' ); ?></th>
                        <td>
                            <input type="number" name="acf_css_brevo_templates[license_issued]" 
                                   value="<?php echo esc_attr( $templates['license_issued'] ?? '' ); ?>" 
                                   class="small-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( '만료 알림 (30일 전)', 'acf-css-brevo' ); ?></th>
                        <td>
                            <input type="number" name="acf_css_brevo_templates[expiry_30]" 
                                   value="<?php echo esc_attr( $templates['expiry_30'] ?? '' ); ?>" 
                                   class="small-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( '만료 알림 (7일 전)', 'acf-css-brevo' ); ?></th>
                        <td>
                            <input type="number" name="acf_css_brevo_templates[expiry_7]" 
                                   value="<?php echo esc_attr( $templates['expiry_7'] ?? '' ); ?>" 
                                   class="small-text" />
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <hr>
            
            <h2><?php esc_html_e( '연결 테스트', 'acf-css-brevo' ); ?></h2>
            <button type="button" id="brevo-test-btn" class="button button-secondary">
                <?php esc_html_e( 'API 연결 테스트', 'acf-css-brevo' ); ?>
            </button>
            <span id="brevo-test-result" style="margin-left: 10px;"></span>
            
            <hr>
            
            <h2><?php esc_html_e( '숏코드 사용법', 'acf-css-brevo' ); ?></h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php esc_html_e( '숏코드', 'acf-css-brevo' ); ?></th>
                        <th><?php esc_html_e( '설명', 'acf-css-brevo' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>[acf_css_newsletter]</code></td>
                        <td><?php esc_html_e( '뉴스레터 구독 폼', 'acf-css-brevo' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>[acf_css_beta_signup]</code></td>
                        <td><?php esc_html_e( '베타 테스터 신청 폼', 'acf-css-brevo' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <script>
        jQuery(function($) {
            $('#brevo-test-btn').on('click', function() {
                var $btn = $(this);
                var $result = $('#brevo-test-result');
                
                $btn.prop('disabled', true);
                $result.text('테스트 중...').css('color', 'orange');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'acf_css_brevo_test',
                        nonce: '<?php echo wp_create_nonce( 'brevo_test' ); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $result.text('✓ ' + response.data.message).css('color', 'green');
                        } else {
                            $result.text('✗ ' + response.data.message).css('color', 'red');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Brevo API 호출
     */
    private function api_request( $endpoint, $method = 'GET', $data = array() ) {
        if ( empty( $this->api_key ) ) {
            return new WP_Error( 'no_api_key', __( 'Brevo API Key가 설정되지 않았습니다.', 'acf-css-brevo' ) );
        }

        $args = array(
            'method'  => $method,
            'timeout' => 15,
            'headers' => array(
                'api-key'      => $this->api_key,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ),
        );

        if ( ! empty( $data ) && in_array( $method, array( 'POST', 'PUT', 'PATCH' ), true ) ) {
            $args['body'] = wp_json_encode( $data );
        }

        $response = wp_remote_request( $this->api_url . $endpoint, $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $code >= 400 ) {
            $message = isset( $body['message'] ) ? $body['message'] : 'API Error';
            return new WP_Error( 'api_error', $message, array( 'code' => $code ) );
        }

        return $body;
    }

    /**
     * 연락처 생성/업데이트
     */
    public function upsert_contact( $email, $attributes = array(), $list_ids = array() ) {
        $data = array(
            'email'         => $email,
            'updateEnabled' => true,
        );

        if ( ! empty( $attributes ) ) {
            $data['attributes'] = $attributes;
        }

        if ( ! empty( $list_ids ) ) {
            $data['listIds'] = array_map( 'intval', $list_ids );
        }

        return $this->api_request( '/contacts', 'POST', $data );
    }

    /**
     * 트랜잭션 이메일 발송
     */
    public function send_transactional_email( $template_id, $to_email, $to_name, $params = array() ) {
        $data = array(
            'templateId' => (int) $template_id,
            'to'         => array(
                array(
                    'email' => $to_email,
                    'name'  => $to_name,
                ),
            ),
        );

        if ( ! empty( $params ) ) {
            $data['params'] = $params;
        }

        return $this->api_request( '/smtp/email', 'POST', $data );
    }

    /**
     * 주문 완료 시
     */
    public function on_order_completed( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return;
        }

        $email = $order->get_billing_email();
        $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

        // 라이센스 정보 가져오기
        $licenses = get_post_meta( $order_id, '_acf_css_licenses', true );
        $edition = '';
        $license_key = '';
        
        if ( ! empty( $licenses ) && is_array( $licenses ) ) {
            $edition = $licenses[0]['edition'] ?? '';
            $license_key = $licenses[0]['license_key'] ?? '';
        }

        // 구매자 리스트에 추가
        $lists = get_option( 'acf_css_brevo_lists', array() );
        $customer_list = isset( $lists['customers'] ) ? array( (int) $lists['customers'] ) : array( 2 );

        $this->upsert_contact( $email, array(
            'FIRSTNAME'     => $order->get_billing_first_name(),
            'LASTNAME'      => $order->get_billing_last_name(),
            'EDITION'       => strtoupper( $edition ),
            'LICENSE_KEY'   => $license_key,
            'PURCHASE_DATE' => date( 'Y-m-d' ),
            'EXPIRY_DATE'   => date( 'Y-m-d', strtotime( '+1 year' ) ),
            'ORDER_ID'      => $order_id,
        ), $customer_list );

        // 라이센스 발급 이메일 발송
        $templates = get_option( 'acf_css_brevo_templates', array() );
        if ( ! empty( $templates['license_issued'] ) && ! empty( $license_key ) ) {
            $this->send_transactional_email(
                $templates['license_issued'],
                $email,
                $name,
                array(
                    'customer_name' => $name,
                    'edition_name'  => ucfirst( $edition ) . ' PRO',
                    'license_key'   => $license_key,
                    'order_id'      => $order_id,
                    'expires_at'    => date( 'Y년 m월 d일', strtotime( '+1 year' ) ),
                    'site_limit'    => '무제한',
                    'admin_url'     => admin_url( 'options-general.php?page=jj-admin-center' ),
                )
            );
        }
    }

    /**
     * 고객 생성 시
     */
    public function on_customer_created( $customer_id, $new_customer_data, $password_generated ) {
        $customer = new WC_Customer( $customer_id );
        
        $this->upsert_contact( $customer->get_email(), array(
            'FIRSTNAME' => $customer->get_first_name(),
            'LASTNAME'  => $customer->get_last_name(),
        ) );
    }

    /**
     * 뉴스레터 숏코드
     */
    public function newsletter_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'title'       => __( '뉴스레터 구독', 'acf-css-brevo' ),
            'description' => __( '최신 소식과 팁을 받아보세요.', 'acf-css-brevo' ),
            'button'      => __( '구독하기', 'acf-css-brevo' ),
            'theme'       => 'dark',
        ), $atts );

        ob_start();
        ?>
        <div class="acf-css-newsletter-form <?php echo esc_attr( $atts['theme'] ); ?>">
            <h3><?php echo esc_html( $atts['title'] ); ?></h3>
            <p><?php echo esc_html( $atts['description'] ); ?></p>
            <form class="newsletter-form" data-type="newsletter">
                <input type="email" name="email" placeholder="이메일 주소" required />
                <button type="submit"><?php echo esc_html( $atts['button'] ); ?></button>
            </form>
            <div class="newsletter-message"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * 베타 신청 숏코드
     */
    public function beta_signup_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'title'       => __( '베타 테스터 신청', 'acf-css-brevo' ),
            'description' => __( '베타 테스터에게는 정식 출시 시 50% 할인 쿠폰을 드립니다.', 'acf-css-brevo' ),
            'button'      => __( '✨ 베타 테스터 신청', 'acf-css-brevo' ),
        ), $atts );

        ob_start();
        ?>
        <div class="acf-css-beta-form">
            <h3><?php echo esc_html( $atts['title'] ); ?></h3>
            <p><?php echo esc_html( $atts['description'] ); ?></p>
            <form class="beta-signup-form" data-type="beta">
                <input type="text" name="name" placeholder="이름" required />
                <input type="email" name="email" placeholder="이메일 주소" required />
                <select name="edition">
                    <option value="">관심 에디션 선택</option>
                    <option value="free">Free</option>
                    <option value="pro">PRO</option>
                    <option value="partner">Partner</option>
                </select>
                <button type="submit"><?php echo esc_html( $atts['button'] ); ?></button>
            </form>
            <div class="beta-message"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * 스크립트/스타일 등록
     */
    public function enqueue_scripts() {
        wp_add_inline_style( 'wp-block-library', $this->get_form_styles() );
        wp_add_inline_script( 'jquery', $this->get_form_scripts() );
    }

    /**
     * 폼 스타일
     */
    private function get_form_styles() {
        return '
            .acf-css-newsletter-form, .acf-css-beta-form {
                max-width: 500px;
                padding: 30px;
                border-radius: 12px;
                text-align: center;
            }
            .acf-css-newsletter-form.dark {
                background: #1e293b;
                color: #fff;
            }
            .acf-css-newsletter-form.light {
                background: #f8fafc;
                color: #1e293b;
            }
            .acf-css-newsletter-form h3, .acf-css-beta-form h3 {
                margin-bottom: 10px;
            }
            .acf-css-newsletter-form p, .acf-css-beta-form p {
                color: #94a3b8;
                margin-bottom: 20px;
            }
            .newsletter-form, .beta-signup-form {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .newsletter-form input, .beta-signup-form input,
            .newsletter-form select, .beta-signup-form select {
                padding: 12px 16px;
                border: 1px solid #334155;
                border-radius: 8px;
                font-size: 16px;
                background: #0f172a;
                color: #fff;
            }
            .newsletter-form button, .beta-signup-form button {
                padding: 14px 24px;
                background: linear-gradient(135deg, #2563eb, #1d4ed8);
                color: #fff;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: transform 0.2s;
            }
            .newsletter-form button:hover, .beta-signup-form button:hover {
                transform: translateY(-2px);
            }
            .newsletter-message, .beta-message {
                margin-top: 15px;
                font-size: 14px;
            }
            .newsletter-message.success, .beta-message.success { color: #10b981; }
            .newsletter-message.error, .beta-message.error { color: #ef4444; }
        ';
    }

    /**
     * 폼 스크립트
     */
    private function get_form_scripts() {
        return '
            jQuery(function($) {
                $(".newsletter-form, .beta-signup-form").on("submit", function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    var $btn = $form.find("button");
                    var $msg = $form.next(".newsletter-message, .beta-message");
                    var type = $form.data("type");
                    
                    $btn.prop("disabled", true).text("처리 중...");
                    
                    $.ajax({
                        url: "' . admin_url( 'admin-ajax.php' ) . '",
                        type: "POST",
                        data: {
                            action: type === "beta" ? "acf_css_beta_signup" : "acf_css_subscribe",
                            nonce: "' . wp_create_nonce( 'brevo_form' ) . '",
                            name: $form.find("[name=name]").val() || "",
                            email: $form.find("[name=email]").val(),
                            edition: $form.find("[name=edition]").val() || ""
                        },
                        success: function(response) {
                            if (response.success) {
                                $msg.removeClass("error").addClass("success").text(response.data.message);
                                $form[0].reset();
                            } else {
                                $msg.removeClass("success").addClass("error").text(response.data.message);
                            }
                        },
                        error: function() {
                            $msg.removeClass("success").addClass("error").text("오류가 발생했습니다.");
                        },
                        complete: function() {
                            $btn.prop("disabled", false).text(type === "beta" ? "✨ 베타 테스터 신청" : "구독하기");
                        }
                    });
                });
            });
        ';
    }

    /**
     * AJAX: 뉴스레터 구독
     */
    public function ajax_subscribe() {
        check_ajax_referer( 'brevo_form', 'nonce' );

        $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';

        if ( ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => __( '유효한 이메일 주소를 입력해주세요.', 'acf-css-brevo' ) ) );
        }

        $lists = get_option( 'acf_css_brevo_lists', array() );
        $newsletter_list = isset( $lists['newsletter'] ) ? array( (int) $lists['newsletter'] ) : array( 1 );

        $result = $this->upsert_contact( $email, array(), $newsletter_list );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        // 환영 이메일 발송
        $templates = get_option( 'acf_css_brevo_templates', array() );
        if ( ! empty( $templates['welcome'] ) ) {
            $this->send_transactional_email( $templates['welcome'], $email, '', array(
                'customer_name' => '',
            ) );
        }

        wp_send_json_success( array( 'message' => __( '구독이 완료되었습니다! 🎉', 'acf-css-brevo' ) ) );
    }

    /**
     * AJAX: 베타 신청
     */
    public function ajax_beta_signup() {
        check_ajax_referer( 'brevo_form', 'nonce' );

        $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
        $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        $edition = isset( $_POST['edition'] ) ? sanitize_text_field( $_POST['edition'] ) : '';

        if ( empty( $name ) ) {
            wp_send_json_error( array( 'message' => __( '이름을 입력해주세요.', 'acf-css-brevo' ) ) );
        }

        if ( ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => __( '유효한 이메일 주소를 입력해주세요.', 'acf-css-brevo' ) ) );
        }

        $lists = get_option( 'acf_css_brevo_lists', array() );
        $beta_list = isset( $lists['beta'] ) ? array( (int) $lists['beta'] ) : array( 3 );

        $result = $this->upsert_contact( $email, array(
            'FIRSTNAME'        => $name,
            'INTERESTED_EDITION' => strtoupper( $edition ),
            'BETA_SIGNUP_DATE' => date( 'Y-m-d' ),
        ), $beta_list );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 
            'message' => __( '베타 테스터로 등록되었습니다! 곧 연락드리겠습니다. 🎉', 'acf-css-brevo' ) 
        ) );
    }

    /**
     * 만료 알림 발송 (Cron)
     */
    public function send_expiry_notifications() {
        // 여기서 Neural Link API를 호출하여 만료 예정 라이센스 조회
        // 그리고 Brevo로 이메일 발송
        
        $templates = get_option( 'acf_css_brevo_templates', array() );
        
        // 30일 전 알림
        if ( ! empty( $templates['expiry_30'] ) ) {
            $this->process_expiry_notifications( 30, $templates['expiry_30'] );
        }
        
        // 7일 전 알림
        if ( ! empty( $templates['expiry_7'] ) ) {
            $this->process_expiry_notifications( 7, $templates['expiry_7'] );
        }
    }

    /**
     * 만료 알림 처리
     */
    private function process_expiry_notifications( $days, $template_id ) {
        // Neural Link에서 만료 예정 라이센스 조회
        $options = get_option( 'acf_css_woo_license_settings', array() );
        $api_url = isset( $options['api_url'] ) ? trailingslashit( $options['api_url'] ) : '';
        $api_key = isset( $options['api_key'] ) ? $options['api_key'] : '';
        
        if ( empty( $api_url ) || empty( $api_key ) ) {
            return;
        }
        
        $target_date = date( 'Y-m-d', strtotime( "+{$days} days" ) );
        
        $response = wp_remote_get( $api_url . 'wp-json/acf-neural-link/v1/licenses?expires_on=' . $target_date, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
            ),
        ) );
        
        if ( is_wp_error( $response ) ) {
            return;
        }
        
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        
        if ( empty( $body['licenses'] ) ) {
            return;
        }
        
        foreach ( $body['licenses'] as $license ) {
            $this->send_transactional_email(
                $template_id,
                $license['email'],
                '',
                array(
                    'customer_name'     => '',
                    'license_key_masked' => substr( $license['license_key'], 0, 8 ) . '...',
                    'edition_name'      => ucfirst( $license['edition'] ),
                    'expires_at'        => date( 'Y년 m월 d일', strtotime( $license['expires_at'] ) ),
                    'days_remaining'    => $days,
                    'renewal_url'       => home_url( '/shop/' ),
                    'account_url'       => home_url( '/my-account/acf-css-licenses/' ),
                )
            );
        }
    }
}

// AJAX: API 테스트
add_action( 'wp_ajax_acf_css_brevo_test', function() {
    check_ajax_referer( 'brevo_test', 'nonce' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
    }
    
    $api_key = get_option( 'acf_css_brevo_api_key', '' );
    
    if ( empty( $api_key ) ) {
        wp_send_json_error( array( 'message' => 'API Key가 설정되지 않았습니다.' ) );
    }
    
    $response = wp_remote_get( 'https://api.brevo.com/v3/account', array(
        'headers' => array(
            'api-key' => $api_key,
        ),
    ) );
    
    if ( is_wp_error( $response ) ) {
        wp_send_json_error( array( 'message' => $response->get_error_message() ) );
    }
    
    $code = wp_remote_retrieve_response_code( $response );
    
    if ( $code === 200 ) {
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        wp_send_json_success( array( 
            'message' => '연결 성공! 계정: ' . ( $body['email'] ?? 'Unknown' )
        ) );
    } else {
        wp_send_json_error( array( 'message' => 'API 오류: HTTP ' . $code ) );
    }
} );

// 초기화
ACF_CSS_Brevo_Integration::instance();

