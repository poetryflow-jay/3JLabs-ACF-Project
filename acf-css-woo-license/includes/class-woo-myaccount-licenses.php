<?php
/**
 * WooCommerce 마이 어카운트 라이센스 탭
 * 
 * 고객이 구매한 라이센스를 마이 어카운트에서 확인하고 관리할 수 있습니다.
 * 
 * @package ACF_CSS_Woo_License
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ACF_CSS_Woo_MyAccount_Licenses' ) ) {

    class ACF_CSS_Woo_MyAccount_Licenses {

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
            // 마이 어카운트 메뉴에 탭 추가
            add_filter( 'woocommerce_account_menu_items', array( $this, 'add_licenses_menu_item' ) );
            
            // 탭 엔드포인트 등록
            add_action( 'init', array( $this, 'add_licenses_endpoint' ) );
            
            // 탭 콘텐츠 출력
            add_action( 'woocommerce_account_acf-css-licenses_endpoint', array( $this, 'licenses_content' ) );
            
            // 스타일 및 스크립트
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
            
            // AJAX 핸들러
            add_action( 'wp_ajax_acf_css_deactivate_site', array( $this, 'ajax_deactivate_site' ) );
        }

        /**
         * 마이 어카운트 메뉴에 라이센스 탭 추가
         */
        public function add_licenses_menu_item( $items ) {
            // 'downloads' 다음에 추가
            $new_items = array();
            
            foreach ( $items as $key => $value ) {
                $new_items[ $key ] = $value;
                if ( 'downloads' === $key ) {
                    $new_items['acf-css-licenses'] = __( '라이센스 관리', 'acf-css-woo-license' );
                }
            }
            
            // downloads가 없으면 orders 다음에 추가
            if ( ! isset( $new_items['acf-css-licenses'] ) ) {
                $new_items = array();
                foreach ( $items as $key => $value ) {
                    $new_items[ $key ] = $value;
                    if ( 'orders' === $key ) {
                        $new_items['acf-css-licenses'] = __( '라이센스 관리', 'acf-css-woo-license' );
                    }
                }
            }
            
            return $new_items;
        }

        /**
         * 엔드포인트 등록
         */
        public function add_licenses_endpoint() {
            add_rewrite_endpoint( 'acf-css-licenses', EP_ROOT | EP_PAGES );
        }

        /**
         * 스타일 등록
         */
        public function enqueue_styles() {
            if ( is_account_page() ) {
                wp_add_inline_style( 'woocommerce-general', $this->get_inline_styles() );
            }
        }

        /**
         * 인라인 스타일
         */
        private function get_inline_styles() {
            return '
                .acf-css-licenses-wrap {
                    max-width: 100%;
                }
                .acf-css-license-card {
                    background: #f8f9fa;
                    border: 1px solid #e9ecef;
                    border-radius: 8px;
                    padding: 20px;
                    margin-bottom: 20px;
                }
                .acf-css-license-card.active {
                    border-color: #28a745;
                    background: #f8fff8;
                }
                .acf-css-license-card.expired {
                    border-color: #dc3545;
                    background: #fff8f8;
                }
                .acf-css-license-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 15px;
                    padding-bottom: 15px;
                    border-bottom: 1px solid #e9ecef;
                }
                .acf-css-license-edition {
                    font-size: 18px;
                    font-weight: 600;
                    color: #333;
                }
                .acf-css-license-status {
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 500;
                }
                .acf-css-license-status.active {
                    background: #28a745;
                    color: #fff;
                }
                .acf-css-license-status.expired {
                    background: #dc3545;
                    color: #fff;
                }
                .acf-css-license-key {
                    background: #fff;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    padding: 10px 15px;
                    font-family: monospace;
                    font-size: 14px;
                    margin-bottom: 15px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .acf-css-license-key code {
                    background: none;
                    padding: 0;
                }
                .acf-css-copy-btn {
                    background: #007bff;
                    color: #fff;
                    border: none;
                    padding: 5px 10px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 12px;
                }
                .acf-css-copy-btn:hover {
                    background: #0056b3;
                }
                .acf-css-license-details {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 10px;
                    margin-bottom: 15px;
                }
                .acf-css-license-detail {
                    font-size: 14px;
                }
                .acf-css-license-detail label {
                    color: #666;
                    display: block;
                    margin-bottom: 2px;
                }
                .acf-css-license-detail span {
                    font-weight: 500;
                    color: #333;
                }
                .acf-css-activated-sites {
                    margin-top: 15px;
                    padding-top: 15px;
                    border-top: 1px solid #e9ecef;
                }
                .acf-css-activated-sites h4 {
                    font-size: 14px;
                    margin-bottom: 10px;
                }
                .acf-css-site-item {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 8px 12px;
                    background: #fff;
                    border: 1px solid #e9ecef;
                    border-radius: 4px;
                    margin-bottom: 5px;
                }
                .acf-css-site-url {
                    font-size: 13px;
                    color: #333;
                }
                .acf-css-deactivate-btn {
                    background: #dc3545;
                    color: #fff;
                    border: none;
                    padding: 4px 8px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 11px;
                }
                .acf-css-deactivate-btn:hover {
                    background: #c82333;
                }
                .acf-css-license-actions {
                    margin-top: 15px;
                    display: flex;
                    gap: 10px;
                }
                .acf-css-license-actions a {
                    padding: 8px 16px;
                    border-radius: 4px;
                    text-decoration: none;
                    font-size: 13px;
                }
                .acf-css-renew-btn {
                    background: #28a745;
                    color: #fff !important;
                }
                .acf-css-docs-btn {
                    background: #6c757d;
                    color: #fff !important;
                }
                .acf-css-no-licenses {
                    text-align: center;
                    padding: 40px 20px;
                    background: #f8f9fa;
                    border-radius: 8px;
                }
                .acf-css-no-licenses h3 {
                    margin-bottom: 10px;
                }
                .acf-css-no-licenses a {
                    display: inline-block;
                    margin-top: 15px;
                    background: #007bff;
                    color: #fff;
                    padding: 10px 20px;
                    border-radius: 4px;
                    text-decoration: none;
                }
            ';
        }

        /**
         * 라이센스 탭 콘텐츠
         */
        public function licenses_content() {
            $user_id = get_current_user_id();
            $user_email = wp_get_current_user()->user_email;
            
            // 사용자의 주문에서 라이센스 조회
            $licenses = $this->get_user_licenses( $user_id );
            
            echo '<div class="acf-css-licenses-wrap">';
            echo '<h2>' . esc_html__( 'ACF CSS Manager 라이센스', 'acf-css-woo-license' ) . '</h2>';
            
            if ( empty( $licenses ) ) {
                echo '<div class="acf-css-no-licenses">';
                echo '<h3>' . esc_html__( '구매한 라이센스가 없습니다', 'acf-css-woo-license' ) . '</h3>';
                echo '<p>' . esc_html__( 'ACF CSS Manager PRO를 구매하여 더 많은 기능을 사용해보세요.', 'acf-css-woo-license' ) . '</p>';
                echo '<a href="' . esc_url( home_url( '/shop/' ) ) . '">' . esc_html__( '지금 구매하기', 'acf-css-woo-license' ) . '</a>';
                echo '</div>';
            } else {
                foreach ( $licenses as $license ) {
                    $this->render_license_card( $license );
                }
            }
            
            echo '</div>';
            
            // JavaScript for copy and deactivate
            $this->render_scripts();
        }

        /**
         * 사용자 라이센스 조회
         */
        private function get_user_licenses( $user_id ) {
            $licenses = array();
            
            // 사용자의 완료된 주문 조회
            $orders = wc_get_orders( array(
                'customer_id' => $user_id,
                'status'      => array( 'completed', 'processing' ),
                'limit'       => -1,
            ) );
            
            foreach ( $orders as $order ) {
                $order_licenses = get_post_meta( $order->get_id(), '_acf_css_licenses', true );
                
                if ( ! empty( $order_licenses ) && is_array( $order_licenses ) ) {
                    foreach ( $order_licenses as $license ) {
                        $license['order_id'] = $order->get_id();
                        $license['order_date'] = $order->get_date_created()->format( 'Y-m-d' );
                        
                        // Neural Link에서 추가 정보 조회 (선택)
                        $license_info = $this->get_license_info_from_neural_link( $license['license_key'] );
                        if ( $license_info ) {
                            $license = array_merge( $license, $license_info );
                        }
                        
                        $licenses[] = $license;
                    }
                }
            }
            
            return $licenses;
        }

        /**
         * Neural Link에서 라이센스 정보 조회
         */
        private function get_license_info_from_neural_link( $license_key ) {
            $options = get_option( 'acf_css_woo_license_settings', array() );
            $api_url = isset( $options['api_url'] ) ? trailingslashit( $options['api_url'] ) : '';
            
            if ( empty( $api_url ) ) {
                return null;
            }
            
            $response = wp_remote_post( $api_url . 'wp-json/acf-neural-link/v1/license/verify', array(
                'timeout' => 10,
                'body'    => wp_json_encode( array(
                    'license_key' => $license_key,
                    'site_url'    => home_url(),
                ) ),
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
            ) );
            
            if ( is_wp_error( $response ) ) {
                return null;
            }
            
            $body = json_decode( wp_remote_retrieve_body( $response ), true );
            
            if ( isset( $body['valid'] ) ) {
                return array(
                    'is_valid'        => $body['valid'],
                    'expires_at'      => isset( $body['expires_at'] ) ? $body['expires_at'] : '',
                    'activated_sites' => isset( $body['activated_sites'] ) ? $body['activated_sites'] : array(),
                    'site_limit'      => isset( $body['site_limit'] ) ? $body['site_limit'] : 0,
                    'sites_count'     => isset( $body['sites_count'] ) ? $body['sites_count'] : 0,
                );
            }
            
            return null;
        }

        /**
         * 라이센스 카드 렌더링
         */
        private function render_license_card( $license ) {
            $is_expired = false;
            if ( ! empty( $license['expires_at'] ) && strtotime( $license['expires_at'] ) < time() ) {
                $is_expired = true;
            }
            
            $status_class = $is_expired ? 'expired' : 'active';
            $status_text = $is_expired ? __( '만료됨', 'acf-css-woo-license' ) : __( '활성', 'acf-css-woo-license' );
            
            ?>
            <div class="acf-css-license-card <?php echo esc_attr( $status_class ); ?>">
                <div class="acf-css-license-header">
                    <span class="acf-css-license-edition">
                        <?php echo esc_html( $license['product_name'] ?? 'ACF CSS Manager' ); ?>
                    </span>
                    <span class="acf-css-license-status <?php echo esc_attr( $status_class ); ?>">
                        <?php echo esc_html( $status_text ); ?>
                    </span>
                </div>
                
                <div class="acf-css-license-key">
                    <code><?php echo esc_html( $license['license_key'] ); ?></code>
                    <button type="button" class="acf-css-copy-btn" data-license="<?php echo esc_attr( $license['license_key'] ); ?>">
                        <?php esc_html_e( '복사', 'acf-css-woo-license' ); ?>
                    </button>
                </div>
                
                <div class="acf-css-license-details">
                    <div class="acf-css-license-detail">
                        <label><?php esc_html_e( '에디션', 'acf-css-woo-license' ); ?></label>
                        <span><?php echo esc_html( ucfirst( $license['edition'] ?? '-' ) ); ?></span>
                    </div>
                    <div class="acf-css-license-detail">
                        <label><?php esc_html_e( '주문일', 'acf-css-woo-license' ); ?></label>
                        <span><?php echo esc_html( $license['order_date'] ?? '-' ); ?></span>
                    </div>
                    <div class="acf-css-license-detail">
                        <label><?php esc_html_e( '만료일', 'acf-css-woo-license' ); ?></label>
                        <span><?php echo esc_html( $license['expires_at'] ?? __( '영구', 'acf-css-woo-license' ) ); ?></span>
                    </div>
                    <div class="acf-css-license-detail">
                        <label><?php esc_html_e( '사이트', 'acf-css-woo-license' ); ?></label>
                        <span>
                            <?php 
                            $sites_count = isset( $license['sites_count'] ) ? $license['sites_count'] : 0;
                            $site_limit = isset( $license['site_limit'] ) ? $license['site_limit'] : 0;
                            echo esc_html( $sites_count . ' / ' . ( $site_limit > 0 ? $site_limit : '∞' ) );
                            ?>
                        </span>
                    </div>
                </div>
                
                <?php if ( ! empty( $license['activated_sites'] ) ) : ?>
                <div class="acf-css-activated-sites">
                    <h4><?php esc_html_e( '활성화된 사이트', 'acf-css-woo-license' ); ?></h4>
                    <?php foreach ( $license['activated_sites'] as $site_url ) : ?>
                    <div class="acf-css-site-item">
                        <span class="acf-css-site-url"><?php echo esc_html( $site_url ); ?></span>
                        <button type="button" class="acf-css-deactivate-btn" 
                                data-license="<?php echo esc_attr( $license['license_key'] ); ?>"
                                data-site="<?php echo esc_attr( $site_url ); ?>">
                            <?php esc_html_e( '비활성화', 'acf-css-woo-license' ); ?>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div class="acf-css-license-actions">
                    <?php if ( $is_expired ) : ?>
                    <a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="acf-css-renew-btn">
                        <?php esc_html_e( '갱신하기', 'acf-css-woo-license' ); ?>
                    </a>
                    <?php endif; ?>
                    <a href="https://j-j-labs.com/docs/" class="acf-css-docs-btn" target="_blank">
                        <?php esc_html_e( '사용자 문서', 'acf-css-woo-license' ); ?>
                    </a>
                </div>
            </div>
            <?php
        }

        /**
         * JavaScript 렌더링
         */
        private function render_scripts() {
            ?>
            <script>
            jQuery(function($) {
                // 라이센스 키 복사
                $('.acf-css-copy-btn').on('click', function() {
                    var license = $(this).data('license');
                    navigator.clipboard.writeText(license).then(function() {
                        alert('<?php echo esc_js( __( '라이센스 키가 복사되었습니다.', 'acf-css-woo-license' ) ); ?>');
                    });
                });
                
                // 사이트 비활성화
                $('.acf-css-deactivate-btn').on('click', function() {
                    var $btn = $(this);
                    var license = $btn.data('license');
                    var site = $btn.data('site');
                    
                    if (!confirm('<?php echo esc_js( __( '이 사이트를 비활성화하시겠습니까?', 'acf-css-woo-license' ) ); ?>')) {
                        return;
                    }
                    
                    $btn.prop('disabled', true).text('<?php echo esc_js( __( '처리 중...', 'acf-css-woo-license' ) ); ?>');
                    
                    $.ajax({
                        url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
                        type: 'POST',
                        data: {
                            action: 'acf_css_deactivate_site',
                            license_key: license,
                            site_url: site,
                            nonce: '<?php echo wp_create_nonce( 'acf_css_deactivate_site' ); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                $btn.closest('.acf-css-site-item').fadeOut(function() {
                                    $(this).remove();
                                });
                            } else {
                                alert(response.data.message || '<?php echo esc_js( __( '오류가 발생했습니다.', 'acf-css-woo-license' ) ); ?>');
                                $btn.prop('disabled', false).text('<?php echo esc_js( __( '비활성화', 'acf-css-woo-license' ) ); ?>');
                            }
                        },
                        error: function() {
                            alert('<?php echo esc_js( __( '서버 오류가 발생했습니다.', 'acf-css-woo-license' ) ); ?>');
                            $btn.prop('disabled', false).text('<?php echo esc_js( __( '비활성화', 'acf-css-woo-license' ) ); ?>');
                        }
                    });
                });
            });
            </script>
            <?php
        }

        /**
         * AJAX: 사이트 비활성화
         */
        public function ajax_deactivate_site() {
            check_ajax_referer( 'acf_css_deactivate_site', 'nonce' );
            
            if ( ! is_user_logged_in() ) {
                wp_send_json_error( array( 'message' => __( '로그인이 필요합니다.', 'acf-css-woo-license' ) ) );
            }
            
            $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
            $site_url = isset( $_POST['site_url'] ) ? esc_url_raw( $_POST['site_url'] ) : '';
            
            if ( empty( $license_key ) || empty( $site_url ) ) {
                wp_send_json_error( array( 'message' => __( '필수 정보가 누락되었습니다.', 'acf-css-woo-license' ) ) );
            }
            
            // Neural Link API 호출
            $options = get_option( 'acf_css_woo_license_settings', array() );
            $api_url = isset( $options['api_url'] ) ? trailingslashit( $options['api_url'] ) : '';
            
            if ( empty( $api_url ) ) {
                wp_send_json_error( array( 'message' => __( 'Neural Link 서버가 설정되지 않았습니다.', 'acf-css-woo-license' ) ) );
            }
            
            $response = wp_remote_post( $api_url . 'wp-json/acf-neural-link/v1/license/deactivate', array(
                'timeout' => 15,
                'body'    => wp_json_encode( array(
                    'license_key' => $license_key,
                    'site_url'    => $site_url,
                ) ),
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
            ) );
            
            if ( is_wp_error( $response ) ) {
                wp_send_json_error( array( 'message' => $response->get_error_message() ) );
            }
            
            $body = json_decode( wp_remote_retrieve_body( $response ), true );
            
            if ( isset( $body['success'] ) && $body['success'] ) {
                wp_send_json_success( array( 'message' => __( '사이트가 비활성화되었습니다.', 'acf-css-woo-license' ) ) );
            } else {
                wp_send_json_error( array( 'message' => $body['message'] ?? __( '비활성화에 실패했습니다.', 'acf-css-woo-license' ) ) );
            }
        }
    }
}

// 초기화
ACF_CSS_Woo_MyAccount_Licenses::instance();

