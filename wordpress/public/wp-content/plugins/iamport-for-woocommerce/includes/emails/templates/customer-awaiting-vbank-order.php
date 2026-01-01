<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>고객님의 주문완료를 위한 가상계좌가 발급되었습니다. 아래의 계좌정보로 기한 내 입금해주시면 주문이 완료됩니다.</p>

<?php
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_footer', $email );
