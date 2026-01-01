<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Portone_Emails' ) ) {

    class Portone_Emails {

        public static function init() {
            add_filter( 'woocommerce_email_actions', array( __CLASS__, 'iamport_email_actions' ) );
            add_filter( 'woocommerce_email_classes', array( __CLASS__, 'iamport_vbank_email_notification' ) );
        }
        static function iamport_email_actions( $email_actions ) {
            return array_merge( $email_actions, array(
                'woocommerce_order_status_awaiting-vbank_to_processing',
                'woocommerce_order_status_awaiting-vbank_to_completed',
                'woocommerce_order_status_failed_to_awaiting-vbank',
                'woocommerce_order_status_on-hold_to_awaiting-vbank',
                'woocommerce_order_status_pending_to_awaiting-vbank'
            ) );
        }
        static function iamport_vbank_email_notification( $emails ) {

            $emails['IMP_Customer_Vbank_Confirm_Email'] = include( 'emails/class-iamport-email-vbank-processing-order.php' );
            $emails['IMP_Admin_Vbank_Confirm_Email'] = include( 'emails/class-iamport-email-vbank-confirm-order.php' );
            $emails['IMP_Email_Customer_Vbank_Awaiting_Order'] = include( 'emails/class-iamport-email-vbank-awaiting-order.php' );

            return $emails;
        }
    }

    Portone_Emails::init();
}