<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'IMP_Email_Customer_Vbank_Awaiting_Order' ) ) :
    class IMP_Email_Customer_Vbank_Awaiting_Order extends WC_Email {
        public function __construct() {

            $this->id          = 'customer_vbank_awaiting_order';
            $this->title       = '가상계좌 입금요청(구매자통지)';
            $this->description = '가상계좌 발급이 이루어졌을 때 입금정보 요청을 위해 고객에게 자동으로 발송되는 이메일 알림입니다.';

            $this->heading = '가상계좌가 발급되었습니다.';
            $this->subject = '고객님의 {order_date} {site_title} 주문 결제를 위한 가상계좌가 발급되었습니다.';

            $this->template_html  = 'customer-awaiting-vbank-order.php'; //우커머스 내부의 이메일 그대로 사용
            $this->template_plain = 'customer-awaiting-vbank-order.php'; //우커머스 내부의 이메일 그대로 사용

            // Triggers for this email
            add_action( 'woocommerce_order_status_failed_to_awaiting-vbank_notification', array ( $this, 'trigger' ), 10, 2 );
            add_action( 'woocommerce_order_status_on-hold_to_awaiting-vbank_notification', array ( $this, 'trigger' ), 10, 2 );
            add_action( 'woocommerce_order_status_pending_to_awaiting-vbank_notification', array ( $this, 'trigger' ), 10, 2 );

            // Call parent constructor
            parent::__construct();
        }
        public function trigger( $order_id ) {
            if ( $order_id ) {
                $this->object    = wc_get_order( $order_id );
                $this->recipient = $this->object->billing_email;

                $this->find[ 'order-date' ]   = '{order_date}';
                $this->find[ 'order-number' ] = '{order_number}';

                $this->replace[ 'order-date' ]   = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );
                $this->replace[ 'order-number' ] = $this->object->get_order_number();
            }

            if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
                return;
            }

            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }
        public function get_content_html() {
            ob_start();
            wc_get_template( $this->template_html, array (
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => false
            ), "iamport/emails/templates", plugin_dir_path( __FILE__ ) . "templates/" );
            return ob_get_clean();
        }
        public function get_content_plain() {
            ob_start();
            wc_get_template( $this->template_plain, array (
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => true
            ), "iamport/emails/templates", plugin_dir_path( __FILE__ ) . "templates/" );
            return ob_get_clean();
        }
    }

endif;

return new IMP_Email_Customer_Vbank_Awaiting_Order();