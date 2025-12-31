<?php
/**
 * WooCommerce 쿠폰 생성기
 *
 * 베타 테스터, 프로모션, 파트너 할인 등을 위한 쿠폰을 자동 생성합니다.
 *
 * @package ACF_CSS_Woo_License
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ACF_CSS_Coupon_Generator' ) ) {

    class ACF_CSS_Coupon_Generator {

        private static $instance = null;

        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
            // Admin 메뉴 추가
            add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 100 );
            
            // AJAX 핸들러
            add_action( 'wp_ajax_acf_css_generate_coupon', array( $this, 'ajax_generate_coupon' ) );
            add_action( 'wp_ajax_acf_css_generate_bulk_coupons', array( $this, 'ajax_generate_bulk_coupons' ) );
        }

        /**
         * 관리자 메뉴 추가
         */
        public function add_admin_menu() {
            add_submenu_page(
                'acf-css-woo-license',
                __( '쿠폰 생성기', 'acf-css-woo-license' ),
                __( '쿠폰 생성기', 'acf-css-woo-license' ),
                'manage_woocommerce',
                'acf-css-coupon-generator',
                array( $this, 'render_admin_page' )
            );
        }

        /**
         * 관리자 페이지 렌더링
         */
        public function render_admin_page() {
            $nonce = wp_create_nonce( 'acf_css_coupon_nonce' );
            ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'ACF CSS 쿠폰 생성기', 'acf-css-woo-license' ); ?></h1>
                
                <div class="card" style="max-width: 600px; margin-top: 20px;">
                    <h2><?php esc_html_e( '단일 쿠폰 생성', 'acf-css-woo-license' ); ?></h2>
                    <form id="single-coupon-form">
                        <input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">
                        
                        <table class="form-table">
                            <tr>
                                <th><label for="coupon-type"><?php esc_html_e( '쿠폰 유형', 'acf-css-woo-license' ); ?></label></th>
                                <td>
                                    <select name="coupon_type" id="coupon-type" required>
                                        <option value="beta_tester"><?php esc_html_e( '베타 테스터 (50% 할인)', 'acf-css-woo-license' ); ?></option>
                                        <option value="launch_promo"><?php esc_html_e( '런칭 프로모션 (30% 할인)', 'acf-css-woo-license' ); ?></option>
                                        <option value="partner_discount"><?php esc_html_e( '파트너 할인 (40% 할인)', 'acf-css-woo-license' ); ?></option>
                                        <option value="newsletter"><?php esc_html_e( '뉴스레터 구독자 (20% 할인)', 'acf-css-woo-license' ); ?></option>
                                        <option value="custom"><?php esc_html_e( '커스텀', 'acf-css-woo-license' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="custom-discount-row" style="display: none;">
                                <th><label for="custom-discount"><?php esc_html_e( '할인율 (%)', 'acf-css-woo-license' ); ?></label></th>
                                <td>
                                    <input type="number" name="custom_discount" id="custom-discount" min="1" max="100" value="10">
                                </td>
                            </tr>
                            <tr>
                                <th><label for="recipient-email"><?php esc_html_e( '수신자 이메일', 'acf-css-woo-license' ); ?></label></th>
                                <td>
                                    <input type="email" name="recipient_email" id="recipient-email" class="regular-text" placeholder="user@example.com">
                                    <p class="description"><?php esc_html_e( '특정 이메일로 제한하려면 입력하세요.', 'acf-css-woo-license' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="usage-limit"><?php esc_html_e( '사용 횟수 제한', 'acf-css-woo-license' ); ?></label></th>
                                <td>
                                    <input type="number" name="usage_limit" id="usage-limit" min="1" value="1">
                                </td>
                            </tr>
                            <tr>
                                <th><label for="expiry-days"><?php esc_html_e( '만료일 (일)', 'acf-css-woo-license' ); ?></label></th>
                                <td>
                                    <input type="number" name="expiry_days" id="expiry-days" min="1" value="30">
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <button type="submit" class="button button-primary"><?php esc_html_e( '쿠폰 생성', 'acf-css-woo-license' ); ?></button>
                        </p>
                    </form>
                    
                    <div id="single-coupon-result" style="display: none; margin-top: 20px; padding: 15px; background: #f0f0f1; border-left: 4px solid #00a32a;">
                        <strong><?php esc_html_e( '생성된 쿠폰 코드:', 'acf-css-woo-license' ); ?></strong>
                        <code id="generated-coupon-code" style="font-size: 16px; margin-left: 10px;"></code>
                        <button type="button" class="button" onclick="navigator.clipboard.writeText(document.getElementById('generated-coupon-code').textContent).then(function() { alert('복사되었습니다!'); });"><?php esc_html_e( '복사', 'acf-css-woo-license' ); ?></button>
                    </div>
                </div>
                
                <div class="card" style="max-width: 600px; margin-top: 20px;">
                    <h2><?php esc_html_e( '대량 쿠폰 생성', 'acf-css-woo-license' ); ?></h2>
                    <form id="bulk-coupon-form">
                        <input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">
                        
                        <table class="form-table">
                            <tr>
                                <th><label for="bulk-coupon-type"><?php esc_html_e( '쿠폰 유형', 'acf-css-woo-license' ); ?></label></th>
                                <td>
                                    <select name="coupon_type" id="bulk-coupon-type" required>
                                        <option value="beta_tester"><?php esc_html_e( '베타 테스터 (50% 할인)', 'acf-css-woo-license' ); ?></option>
                                        <option value="launch_promo"><?php esc_html_e( '런칭 프로모션 (30% 할인)', 'acf-css-woo-license' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="bulk-count"><?php esc_html_e( '생성 개수', 'acf-css-woo-license' ); ?></label></th>
                                <td>
                                    <input type="number" name="count" id="bulk-count" min="1" max="100" value="10">
                                </td>
                            </tr>
                            <tr>
                                <th><label for="bulk-prefix"><?php esc_html_e( '쿠폰 접두사', 'acf-css-woo-license' ); ?></label></th>
                                <td>
                                    <input type="text" name="prefix" id="bulk-prefix" class="regular-text" value="ACFCSS" maxlength="10">
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <button type="submit" class="button button-primary"><?php esc_html_e( '대량 생성', 'acf-css-woo-license' ); ?></button>
                        </p>
                    </form>
                    
                    <div id="bulk-coupon-result" style="display: none; margin-top: 20px;">
                        <h3><?php esc_html_e( '생성된 쿠폰 목록', 'acf-css-woo-license' ); ?></h3>
                        <textarea id="bulk-coupon-list" readonly style="width: 100%; height: 200px; font-family: monospace;"></textarea>
                        <p>
                            <button type="button" class="button" onclick="navigator.clipboard.writeText(document.getElementById('bulk-coupon-list').value).then(function() { alert('복사되었습니다!'); });"><?php esc_html_e( '전체 복사', 'acf-css-woo-license' ); ?></button>
                            <a id="download-csv" class="button" href="#"><?php esc_html_e( 'CSV 다운로드', 'acf-css-woo-license' ); ?></a>
                        </p>
                    </div>
                </div>
            </div>
            
            <script>
            jQuery(document).ready(function($) {
                // 쿠폰 유형 변경 시 커스텀 할인율 필드 토글
                $('#coupon-type').on('change', function() {
                    if ($(this).val() === 'custom') {
                        $('.custom-discount-row').show();
                    } else {
                        $('.custom-discount-row').hide();
                    }
                });
                
                // 단일 쿠폰 생성
                $('#single-coupon-form').on('submit', function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    var $btn = $form.find('button[type="submit"]');
                    
                    $btn.prop('disabled', true).text('<?php esc_html_e( '생성 중...', 'acf-css-woo-license' ); ?>');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'acf_css_generate_coupon',
                            nonce: $form.find('input[name="nonce"]').val(),
                            coupon_type: $form.find('#coupon-type').val(),
                            custom_discount: $form.find('#custom-discount').val(),
                            recipient_email: $form.find('#recipient-email').val(),
                            usage_limit: $form.find('#usage-limit').val(),
                            expiry_days: $form.find('#expiry-days').val()
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#generated-coupon-code').text(response.data.coupon_code);
                                $('#single-coupon-result').show();
                            } else {
                                alert('<?php esc_html_e( '오류:', 'acf-css-woo-license' ); ?> ' + response.data.message);
                            }
                        },
                        error: function() {
                            alert('<?php esc_html_e( '서버 오류가 발생했습니다.', 'acf-css-woo-license' ); ?>');
                        },
                        complete: function() {
                            $btn.prop('disabled', false).text('<?php esc_html_e( '쿠폰 생성', 'acf-css-woo-license' ); ?>');
                        }
                    });
                });
                
                // 대량 쿠폰 생성
                $('#bulk-coupon-form').on('submit', function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    var $btn = $form.find('button[type="submit"]');
                    
                    $btn.prop('disabled', true).text('<?php esc_html_e( '생성 중...', 'acf-css-woo-license' ); ?>');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'acf_css_generate_bulk_coupons',
                            nonce: $form.find('input[name="nonce"]').val(),
                            coupon_type: $form.find('#bulk-coupon-type').val(),
                            count: $form.find('#bulk-count').val(),
                            prefix: $form.find('#bulk-prefix').val()
                        },
                        success: function(response) {
                            if (response.success) {
                                var coupons = response.data.coupons;
                                $('#bulk-coupon-list').val(coupons.join('\n'));
                                $('#bulk-coupon-result').show();
                                
                                // CSV 다운로드 링크 생성
                                var csvContent = 'coupon_code,discount_type,discount_amount,expiry_date\n';
                                coupons.forEach(function(code) {
                                    csvContent += code + ',percent,' + response.data.discount + ',' + response.data.expiry + '\n';
                                });
                                var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                                var url = URL.createObjectURL(blob);
                                $('#download-csv').attr('href', url).attr('download', 'acf-css-coupons.csv');
                            } else {
                                alert('<?php esc_html_e( '오류:', 'acf-css-woo-license' ); ?> ' + response.data.message);
                            }
                        },
                        error: function() {
                            alert('<?php esc_html_e( '서버 오류가 발생했습니다.', 'acf-css-woo-license' ); ?>');
                        },
                        complete: function() {
                            $btn.prop('disabled', false).text('<?php esc_html_e( '대량 생성', 'acf-css-woo-license' ); ?>');
                        }
                    });
                });
            });
            </script>
            <?php
        }

        /**
         * 단일 쿠폰 생성 AJAX 핸들러
         */
        public function ajax_generate_coupon() {
            check_ajax_referer( 'acf_css_coupon_nonce', 'nonce' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-woo-license' ) ) );
            }

            $coupon_type      = isset( $_POST['coupon_type'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_type'] ) ) : 'beta_tester';
            $custom_discount  = isset( $_POST['custom_discount'] ) ? absint( $_POST['custom_discount'] ) : 10;
            $recipient_email  = isset( $_POST['recipient_email'] ) ? sanitize_email( wp_unslash( $_POST['recipient_email'] ) ) : '';
            $usage_limit      = isset( $_POST['usage_limit'] ) ? absint( $_POST['usage_limit'] ) : 1;
            $expiry_days      = isset( $_POST['expiry_days'] ) ? absint( $_POST['expiry_days'] ) : 30;

            // 할인율 결정
            $discount = $this->get_discount_by_type( $coupon_type, $custom_discount );

            // 쿠폰 코드 생성
            $coupon_code = $this->generate_coupon_code( $coupon_type );

            // WooCommerce 쿠폰 생성
            $result = $this->create_woocommerce_coupon( array(
                'code'            => $coupon_code,
                'discount_type'   => 'percent',
                'discount_amount' => $discount,
                'usage_limit'     => $usage_limit,
                'expiry_days'     => $expiry_days,
                'email_restriction' => $recipient_email,
            ) );

            if ( is_wp_error( $result ) ) {
                wp_send_json_error( array( 'message' => $result->get_error_message() ) );
            }

            wp_send_json_success( array( 'coupon_code' => $coupon_code ) );
        }

        /**
         * 대량 쿠폰 생성 AJAX 핸들러
         */
        public function ajax_generate_bulk_coupons() {
            check_ajax_referer( 'acf_css_coupon_nonce', 'nonce' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-woo-license' ) ) );
            }

            $coupon_type = isset( $_POST['coupon_type'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_type'] ) ) : 'beta_tester';
            $count       = isset( $_POST['count'] ) ? min( absint( $_POST['count'] ), 100 ) : 10;
            $prefix      = isset( $_POST['prefix'] ) ? sanitize_text_field( wp_unslash( $_POST['prefix'] ) ) : 'ACFCSS';

            $discount    = $this->get_discount_by_type( $coupon_type );
            $expiry_date = gmdate( 'Y-m-d', strtotime( '+30 days' ) );
            $coupons     = array();

            for ( $i = 0; $i < $count; $i++ ) {
                $coupon_code = $this->generate_coupon_code( $coupon_type, $prefix );

                $result = $this->create_woocommerce_coupon( array(
                    'code'            => $coupon_code,
                    'discount_type'   => 'percent',
                    'discount_amount' => $discount,
                    'usage_limit'     => 1,
                    'expiry_days'     => 30,
                ) );

                if ( ! is_wp_error( $result ) ) {
                    $coupons[] = $coupon_code;
                }
            }

            wp_send_json_success( array(
                'coupons'  => $coupons,
                'discount' => $discount,
                'expiry'   => $expiry_date,
            ) );
        }

        /**
         * 쿠폰 유형에 따른 할인율 반환
         */
        private function get_discount_by_type( $type, $custom = 10 ) {
            $discounts = array(
                'beta_tester'      => 50,
                'launch_promo'     => 30,
                'partner_discount' => 40,
                'newsletter'       => 20,
                'custom'           => $custom,
            );

            return isset( $discounts[ $type ] ) ? $discounts[ $type ] : 10;
        }

        /**
         * 고유한 쿠폰 코드 생성
         */
        private function generate_coupon_code( $type, $prefix = 'ACFCSS' ) {
            $type_codes = array(
                'beta_tester'      => 'BETA',
                'launch_promo'     => 'LAUNCH',
                'partner_discount' => 'PARTNER',
                'newsletter'       => 'NEWS',
                'custom'           => 'PROMO',
            );

            $type_code = isset( $type_codes[ $type ] ) ? $type_codes[ $type ] : 'PROMO';
            $random    = strtoupper( wp_generate_password( 6, false ) );

            return $prefix . '-' . $type_code . '-' . $random;
        }

        /**
         * WooCommerce 쿠폰 생성
         */
        private function create_woocommerce_coupon( $args ) {
            if ( ! class_exists( 'WC_Coupon' ) ) {
                return new WP_Error( 'woocommerce_not_active', __( 'WooCommerce가 활성화되어 있지 않습니다.', 'acf-css-woo-license' ) );
            }

            $coupon = new WC_Coupon();
            $coupon->set_code( $args['code'] );
            $coupon->set_discount_type( $args['discount_type'] );
            $coupon->set_amount( $args['discount_amount'] );
            $coupon->set_usage_limit( $args['usage_limit'] );
            $coupon->set_individual_use( true );
            $coupon->set_usage_limit_per_user( 1 );

            if ( ! empty( $args['expiry_days'] ) ) {
                $expiry_date = gmdate( 'Y-m-d', strtotime( '+' . intval( $args['expiry_days'] ) . ' days' ) );
                $coupon->set_date_expires( $expiry_date );
            }

            if ( ! empty( $args['email_restriction'] ) ) {
                $coupon->set_email_restrictions( array( $args['email_restriction'] ) );
            }

            // ACF CSS 관련 상품에만 적용 (상품 카테고리로 제한)
            // 필요시 여기에 product_ids 또는 product_categories 설정 추가

            $coupon_id = $coupon->save();

            if ( ! $coupon_id ) {
                return new WP_Error( 'coupon_creation_failed', __( '쿠폰 생성에 실패했습니다.', 'acf-css-woo-license' ) );
            }

            return $coupon_id;
        }

        /**
         * 베타 테스터용 쿠폰 생성 (외부 호출용)
         */
        public function create_beta_tester_coupon( $email = '' ) {
            $coupon_code = $this->generate_coupon_code( 'beta_tester' );

            $result = $this->create_woocommerce_coupon( array(
                'code'              => $coupon_code,
                'discount_type'     => 'percent',
                'discount_amount'   => 50,
                'usage_limit'       => 1,
                'expiry_days'       => 90, // 베타 테스터는 3개월
                'email_restriction' => $email,
            ) );

            if ( is_wp_error( $result ) ) {
                return $result;
            }

            return $coupon_code;
        }
    }

    // 초기화
    add_action( 'plugins_loaded', array( 'ACF_CSS_Coupon_Generator', 'instance' ) );
}
