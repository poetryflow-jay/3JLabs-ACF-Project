<?php
/**
 * 포트원 Webhook 핸들러
 * 
 * 포트원 결제 알림을 처리하고 WooCommerce 주문 상태를 업데이트합니다.
 * 
 * @package ACF_CSS_Woo_License
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ACF_CSS_PortOne_Webhook' ) ) {

    class ACF_CSS_PortOne_Webhook {

        /**
         * 싱글톤 인스턴스
         */
        private static $instance = null;

        /**
         * 포트원 API URL
         */
        private $api_url = 'https://api.iamport.kr';

        /**
         * API Key
         */
        private $api_key;

        /**
         * API Secret
         */
        private $api_secret;

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
            $options = get_option( 'acf_css_portone_settings', array() );
            $this->api_key = isset( $options['api_key'] ) ? $options['api_key'] : '';
            $this->api_secret = isset( $options['api_secret'] ) ? $options['api_secret'] : '';

            // Webhook 엔드포인트 등록
            add_action( 'rest_api_init', array( $this, 'register_webhook_endpoint' ) );
            
            // WooCommerce 결제 완료 시 추가 처리
            add_action( 'woocommerce_payment_complete', array( $this, 'on_payment_complete' ) );
            
            // 가상계좌 입금 완료 처리
            add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'on_vbank_paid' ) );
        }

        /**
         * Webhook 엔드포인트 등록
         */
        public function register_webhook_endpoint() {
            register_rest_route( 'acf-css-portone/v1', '/webhook', array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'handle_webhook' ),
                'permission_callback' => '__return_true', // 포트원에서 직접 호출
            ) );
        }

        /**
         * Webhook 처리
         */
        public function handle_webhook( WP_REST_Request $request ) {
            $body = $request->get_body();
            $data = json_decode( $body, true );

            if ( empty( $data ) ) {
                return new WP_REST_Response( array( 'message' => 'Empty payload' ), 400 );
            }

            // 로그 기록
            $this->log( 'Webhook received', $data );

            // imp_uid (포트원 고유 결제 ID) 확인
            $imp_uid = isset( $data['imp_uid'] ) ? sanitize_text_field( $data['imp_uid'] ) : '';
            $merchant_uid = isset( $data['merchant_uid'] ) ? sanitize_text_field( $data['merchant_uid'] ) : '';
            $status = isset( $data['status'] ) ? sanitize_text_field( $data['status'] ) : '';

            if ( empty( $imp_uid ) || empty( $merchant_uid ) ) {
                return new WP_REST_Response( array( 'message' => 'Missing imp_uid or merchant_uid' ), 400 );
            }

            // 결제 정보 검증 (포트원 API 호출)
            $payment_info = $this->get_payment_info( $imp_uid );

            if ( is_wp_error( $payment_info ) ) {
                $this->log( 'Payment verification failed', array(
                    'imp_uid' => $imp_uid,
                    'error'   => $payment_info->get_error_message(),
                ) );
                return new WP_REST_Response( array( 'message' => 'Payment verification failed' ), 400 );
            }

            // WooCommerce 주문 찾기
            $order_id = $this->extract_order_id( $merchant_uid );
            $order = wc_get_order( $order_id );

            if ( ! $order ) {
                $this->log( 'Order not found', array( 'merchant_uid' => $merchant_uid ) );
                return new WP_REST_Response( array( 'message' => 'Order not found' ), 404 );
            }

            // 금액 검증
            $order_amount = $order->get_total();
            $paid_amount = isset( $payment_info['amount'] ) ? floatval( $payment_info['amount'] ) : 0;

            if ( abs( $order_amount - $paid_amount ) > 0.01 ) {
                $this->log( 'Amount mismatch', array(
                    'order_amount' => $order_amount,
                    'paid_amount'  => $paid_amount,
                ) );
                $order->add_order_note( sprintf( 
                    __( '포트원 결제 금액 불일치: 주문 금액 %s, 결제 금액 %s', 'acf-css-woo-license' ),
                    $order_amount,
                    $paid_amount
                ) );
                return new WP_REST_Response( array( 'message' => 'Amount mismatch' ), 400 );
            }

            // 상태별 처리
            switch ( $status ) {
                case 'paid':
                    // 결제 완료
                    $this->process_paid( $order, $payment_info );
                    break;

                case 'ready':
                    // 가상계좌 발급 완료 (입금 대기)
                    $this->process_vbank_ready( $order, $payment_info );
                    break;

                case 'cancelled':
                    // 결제 취소
                    $this->process_cancelled( $order, $payment_info );
                    break;

                case 'failed':
                    // 결제 실패
                    $this->process_failed( $order, $payment_info );
                    break;

                default:
                    $this->log( 'Unknown status', array( 'status' => $status ) );
            }

            return new WP_REST_Response( array( 'message' => 'Webhook processed' ), 200 );
        }

        /**
         * 결제 완료 처리
         */
        private function process_paid( $order, $payment_info ) {
            if ( $order->is_paid() ) {
                return; // 이미 결제 완료됨
            }

            $order->payment_complete( $payment_info['imp_uid'] );
            $order->add_order_note( sprintf(
                __( '포트원 결제 완료: %s (%s)', 'acf-css-woo-license' ),
                $payment_info['imp_uid'],
                $payment_info['pay_method']
            ) );

            // 메타 데이터 저장
            $order->update_meta_data( '_portone_imp_uid', $payment_info['imp_uid'] );
            $order->update_meta_data( '_portone_pay_method', $payment_info['pay_method'] );
            $order->update_meta_data( '_portone_pg_provider', $payment_info['pg_provider'] );
            $order->save();

            $this->log( 'Payment completed', array(
                'order_id' => $order->get_id(),
                'imp_uid'  => $payment_info['imp_uid'],
            ) );
        }

        /**
         * 가상계좌 발급 완료 처리
         */
        private function process_vbank_ready( $order, $payment_info ) {
            $vbank_name = isset( $payment_info['vbank_name'] ) ? $payment_info['vbank_name'] : '';
            $vbank_num = isset( $payment_info['vbank_num'] ) ? $payment_info['vbank_num'] : '';
            $vbank_date = isset( $payment_info['vbank_date'] ) ? $payment_info['vbank_date'] : '';

            $order->update_status( 'on-hold', sprintf(
                __( '가상계좌 발급: %s %s (입금기한: %s)', 'acf-css-woo-license' ),
                $vbank_name,
                $vbank_num,
                date( 'Y-m-d H:i', $vbank_date )
            ) );

            $order->update_meta_data( '_portone_vbank_name', $vbank_name );
            $order->update_meta_data( '_portone_vbank_num', $vbank_num );
            $order->update_meta_data( '_portone_vbank_date', $vbank_date );
            $order->save();
        }

        /**
         * 결제 취소 처리
         */
        private function process_cancelled( $order, $payment_info ) {
            $cancel_amount = isset( $payment_info['cancel_amount'] ) ? $payment_info['cancel_amount'] : 0;
            $cancel_reason = isset( $payment_info['cancel_reason'] ) ? $payment_info['cancel_reason'] : '';

            $order->update_status( 'cancelled', sprintf(
                __( '포트원 결제 취소: %s원 (%s)', 'acf-css-woo-license' ),
                number_format( $cancel_amount ),
                $cancel_reason
            ) );

            $this->log( 'Payment cancelled', array(
                'order_id'      => $order->get_id(),
                'cancel_amount' => $cancel_amount,
            ) );
        }

        /**
         * 결제 실패 처리
         */
        private function process_failed( $order, $payment_info ) {
            $fail_reason = isset( $payment_info['fail_reason'] ) ? $payment_info['fail_reason'] : '';

            $order->update_status( 'failed', sprintf(
                __( '포트원 결제 실패: %s', 'acf-css-woo-license' ),
                $fail_reason
            ) );
        }

        /**
         * 포트원 액세스 토큰 발급
         */
        private function get_access_token() {
            $response = wp_remote_post( $this->api_url . '/users/getToken', array(
                'body' => array(
                    'imp_key'    => $this->api_key,
                    'imp_secret' => $this->api_secret,
                ),
            ) );

            if ( is_wp_error( $response ) ) {
                return $response;
            }

            $body = json_decode( wp_remote_retrieve_body( $response ), true );

            if ( isset( $body['code'] ) && $body['code'] === 0 ) {
                return $body['response']['access_token'];
            }

            return new WP_Error( 'token_error', $body['message'] ?? 'Token error' );
        }

        /**
         * 결제 정보 조회
         */
        private function get_payment_info( $imp_uid ) {
            $token = $this->get_access_token();

            if ( is_wp_error( $token ) ) {
                return $token;
            }

            $response = wp_remote_get( $this->api_url . '/payments/' . $imp_uid, array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $token,
                ),
            ) );

            if ( is_wp_error( $response ) ) {
                return $response;
            }

            $body = json_decode( wp_remote_retrieve_body( $response ), true );

            if ( isset( $body['code'] ) && $body['code'] === 0 ) {
                return $body['response'];
            }

            return new WP_Error( 'payment_error', $body['message'] ?? 'Payment info error' );
        }

        /**
         * merchant_uid에서 주문 ID 추출
         */
        private function extract_order_id( $merchant_uid ) {
            // 형식: order_123_1703001234
            if ( preg_match( '/order_(\d+)_/', $merchant_uid, $matches ) ) {
                return intval( $matches[1] );
            }
            // 형식: 123
            if ( is_numeric( $merchant_uid ) ) {
                return intval( $merchant_uid );
            }
            return 0;
        }

        /**
         * WooCommerce 결제 완료 시
         */
        public function on_payment_complete( $order_id ) {
            $order = wc_get_order( $order_id );
            if ( ! $order ) {
                return;
            }

            // ACF CSS 라이센스 발급 트리거
            if ( class_exists( 'ACF_CSS_Woo_License' ) ) {
                ACF_CSS_Woo_License::instance()->generate_and_assign_license( $order_id );
            }
        }

        /**
         * 가상계좌 입금 완료 시
         */
        public function on_vbank_paid( $order_id ) {
            $order = wc_get_order( $order_id );
            if ( ! $order ) {
                return;
            }

            $order->add_order_note( __( '가상계좌 입금 확인됨', 'acf-css-woo-license' ) );
            
            // 라이센스 발급 트리거
            if ( class_exists( 'ACF_CSS_Woo_License' ) ) {
                ACF_CSS_Woo_License::instance()->generate_and_assign_license( $order_id );
            }
        }

        /**
         * 로그 기록
         */
        private function log( $message, $data = array() ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( sprintf(
                    '[ACF CSS PortOne] %s: %s',
                    $message,
                    wp_json_encode( $data )
                ) );
            }
        }
    }
}

// 초기화
ACF_CSS_PortOne_Webhook::instance();

