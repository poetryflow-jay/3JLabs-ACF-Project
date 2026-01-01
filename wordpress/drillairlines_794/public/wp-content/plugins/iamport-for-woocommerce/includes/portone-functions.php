<?php
function portone_get( $array, $key, $default = '' ) {
	return ! empty( $array[ $key ] ) ? wc_clean( $array[ $key ] ) : $default;
}

function portone_get_not_clean( $array, $key, $default = '' ) {
	return ! empty( $array[ $key ] ) ? $array[ $key ] : $default;
}

function portone_get_order_row( $order_id ) {
	global $wpdb;

	if ( PORTONE_HPOS::enabled() ) {
		$synced_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wc_orders WHERE  id = %d FOR UPDATE", $order_id ), ARRAY_A );
	} else {
		$synced_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}posts WHERE ID = %d FOR UPDATE", $order_id ), ARRAY_A );
	}

	return $synced_row;
}
function portone_get_order_status_from_order_row( $order_row ) {
	if ( PORTONE_HPOS::enabled() ) {
		return $order_row[ 'status' ];
	} else {
		return $order_row[ 'post_status' ];
	}
}
function portone_get_order_key( $order ) {
	return str_replace( 'wc_', '', $order->get_order_key() );
}

function portone_get_order_id_by_order_key( $order_key ) {
	if ( ! str_starts_with( $order_key, 'wc_' ) ) {
		$order_key = 'wc_' . $order_key;
	}

	return wc_get_order_id_by_order_key( $order_key );
}
function portone_send_usage_notification( $payment_gateway ) {
	$merchant_id = $payment_gateway->get_option( 'imp_user_code' );

	if ( empty( $merchant_id ) ) {
		$merchant_id = $payment_gateway->get_option( 'imp_rest_key' );
	}

	if ( ! empty( $merchant_id ) ) {
		$version = '3.3.11';

		$params = array(
			"messageKey" => sprintf( "codemshop-%s%04d", date( "Ymd", strtotime( current_time( 'mysql' ) ) ), wp_rand( 0, 9999 ) ), // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			"pluginVer"  => $version,
			"pluginId"   => "woocommerce-kr", // 고정
			"agencyId"   => "codemshop",
			"merchantId" => $merchant_id
		);

		wp_remote_post( "https://slack-message-spread.prod.iamport.co/plugin/install-check", array(
			'headers' => array( 'Content-Type' => 'application/json; charset=utf-8' ),
			'body'    => json_encode( $params ),
			'method'  => 'POST'
		) );
	}
}

if ( ! function_exists( 'iamport_woocommerce_not_installed' ) ) {
	function iamport_woocommerce_not_installed() {
		$class   = 'notice notice-error';
		$message = '[우커머스용 포트원 플러그인] 우커머스 플러그인이 설치되어있지 않거나 비활성화되어있습니다.';

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
}

if ( ! function_exists( 'iamport_woocommerce_not_compatible' ) ) {
	function iamport_woocommerce_not_compatible() {
		$class   = 'notice notice-error';
		$message = '[우커머스용 포트원 플러그인] 우커머스 3.0버전 이상과 호환이 됩니다. 현재 설치된 우커머스 플러그인과는 연동되지 않습니다.';

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
}

if ( ! function_exists( 'iamport_advanced_meta' ) ) {
	function iamport_advanced_meta() {

		woocommerce_wp_text_input(
			array(
				'id'          => 'iamport_product_service_period_from',
				'label'       => __( '서비스제공기간(시작)', 'iamport-for-woocommerce' ),
				'placeholder' => '예시) 20190101',
				'desc_tip'    => '시작일자를 YYYYMMDD 형식에 맞춰 입력합니다.',
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => 'iamport_product_service_period_to',
				'label'       => __( '서비스제공기간(종료)', 'iamport-for-woocommerce' ),
				'placeholder' => '예시) 20191231',
				'desc_tip'    => '종료일자를 YYYYMMDD 형식에 맞춰 입력합니다.',
			)
		);

		woocommerce_wp_select(
			array(
				'id'      => 'iamport_product_service_period_interval',
				'label'   => __( 'c', 'iamport-for-woocommerce' ),
				'options' => array( 'none' => '반복없음', 'year' => '연단위', 'month' => '월단위' ),
			)
		);
	}
}

if ( ! function_exists( 'iamport_advanced_meta_save' ) ) {
	function iamport_advanced_meta_save( $post_id ) {
		$keys = array( 'iamport_product_service_period_from', 'iamport_product_service_period_to', 'iamport_product_service_period_interval' );

		foreach ( $keys as $k ) {
			if ( isset( $_POST[ $k ] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
				update_post_meta( $post_id, $k, sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
			}
		}
	}
}

if ( ! function_exists( 'init_iamport_plugin' ) ) {
	function init_iamport_plugin() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return add_action( 'admin_notices', 'iamport_woocommerce_not_installed' );
		}

		global $woocommerce;
		if ( version_compare( $woocommerce->version, "3.0" ) < 0 ) {
			add_action( 'admin_notices', 'iamport_woocommerce_not_compatible' );
		}

		//Really Simple SSL 플러그인 회피(네이버페이)
		if ( ! empty( $_GET[ 'wc-api' ] ) ) {
			$wcApi = sanitize_key( wp_unslash( $_GET[ 'wc-api' ] ) );

			if ( $wcApi == 'naver-product-info' || $wcApi == 'iamport-naver-product-xml' ) {
				define( 'rsssl_no_wp_redirect', 'Leave me alone' );
				define( 'rsssl_no_rest_api_redirect', 'Leave me alone' );
			}
		}

		//상품 제공기간
		add_action( 'woocommerce_product_options_advanced', 'iamport_advanced_meta' );
		add_action( 'woocommerce_process_product_meta', 'iamport_advanced_meta_save' );
	}
}

if ( ! function_exists( 'enqueue_iamport_common_script' ) ) {
	function enqueue_iamport_common_script() {
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
	}
}

if ( ! function_exists( 'iamport_vbank_order_details' ) ) {
	function iamport_vbank_order_details( $order ) {
		$pay_method   = $order->get_meta( '_iamport_paymethod' );
		$vbank_name   = $order->get_meta( '_iamport_vbank_name' );
		$vbank_num    = $order->get_meta( '_iamport_vbank_num' );
		$vbank_date   = $order->get_meta( '_iamport_vbank_date' );
		$vbank_holder = $order->get_meta( '_iamport_vbank_holder' );

		if ( $pay_method !== 'vbank' || empty( $vbank_num ) ) {
			return;
		}

		ob_start(); ?>
        <div class="order_data_column" style="width: 100%;clear: both">
            <h3><?php esc_html_e( '가상계좌정보', 'iamport-for-woocommerce' ); ?></h3>
            <p class="form-field form-field-wide">
                <strong><?php esc_html_e( '은행명', 'iamport-for-woocommerce' ); ?></strong>&nbsp;:&nbsp;<?php echo esc_html( $vbank_name ); ?><br>
				<?php if ( ! empty( $vbank_holder ) ) : ?>
                    <strong><?php esc_html_e( '예금주', 'iamport-for-woocommerce' ); ?></strong>&nbsp;:&nbsp;<?php echo esc_html( $vbank_holder ); ?><br>
				<?php endif; ?>
                <strong><?php esc_html_e( '계좌번호', 'iamport-for-woocommerce' ); ?></strong>&nbsp;:&nbsp;<?php echo esc_html( $vbank_num ) ?><br>
                <strong><?php esc_html_e( '입금기한', 'iamport-for-woocommerce' ); ?></strong>&nbsp;:&nbsp;<?php echo esc_html( date( 'Y-m-d H:i:s', $vbank_date + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) );  // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date ?>
            </p>
        </div>
		<?php
		ob_end_flush();
	}
}

add_action( 'init', 'init_iamport_plugin' );
add_action( 'wp_enqueue_scripts', 'enqueue_iamport_common_script' );
add_action( 'admin_enqueue_scripts', 'enqueue_iamport_admin_style' );
add_action( 'woocommerce_admin_order_data_after_order_details', 'iamport_vbank_order_details' );


if ( ! function_exists( 'enqueue_iamport_admin_style' ) ) {
	function enqueue_iamport_admin_style() {
		wp_register_script( 'iamport_momentjs', plugins_url( '/assets/js/moment.min.js', PORTONE_PLUGIN_FILE ) );
		wp_enqueue_script( 'iamport_momentjs' );

		wp_register_script( 'iamport_adminjs', plugins_url( '/assets/js/portone.woocommerce.admin.js', PORTONE_PLUGIN_FILE ) );
		wp_enqueue_script( 'iamport_adminjs' );

		wp_register_style( 'iamport_wp_admin_css', plugins_url( '/assets/css/admin-style.css', PORTONE_PLUGIN_FILE ), array(), "20180914" );
		wp_enqueue_style( 'iamport_wp_admin_css' );
	}
}

if ( ! function_exists( 'find_gateway' ) ) {
	function find_gateway( $pg_provider, $pay_method ) {
		$gatewayId = null;

		switch ( $pg_provider ) {
//			case 'naverco' :
//				$gatewayId = WC_Gateway_Portone_NaverPay::GATEWAY_ID;
//				break;

			case 'naverpay' :
				$gatewayId = WC_Gateway_Portone_NaverPayExt::GATEWAY_ID;
				break;

			case 'kakao' :
			case 'kakaopay' :
				$gatewayId = WC_Gateway_Portone_Kakao::GATEWAY_ID;
				break;

			default :
				switch ( $pay_method ) {
					case 'card' :
						$gatewayId = WC_Gateway_Portone_Card::GATEWAY_ID;
						break;

					case 'trans' :
						$gatewayId = WC_Gateway_Portone_Trans::GATEWAY_ID;
						break;

					case 'vbank' :
						$gatewayId = WC_Gateway_Portone_Vbank::GATEWAY_ID;
						break;

					case 'phone' :
						$gatewayId = WC_Gateway_Portone_Phone::GATEWAY_ID;
						break;

					case 'kakao' : //order_review에서 결제가 올라올 때는 pay_method가 kakao로 그대로 올라옴(gateway이름 그대로)
					case 'kakaopay' :
						$gatewayId = WC_Gateway_Portone_Kakao::GATEWAY_ID;
						break;

					case 'samsung' :
						$gatewayId = WC_Gateway_Portone_Samsung::GATEWAY_ID;
						break;

					case 'eximbay' :
						$gatewayId = WC_Gateway_Portone_Eximbay::GATEWAY_ID;
						break;

					case 'paymentwall' :
						$gatewayId = WC_Gateway_Portone_Paymentwall::GATEWAY_ID;
						break;

					default :
						$gatewayId = WC_Gateway_Portone_Card::GATEWAY_ID;
						break;
				}
				break;
		}

		if ( $gatewayId ) {
			$availables = WC()->payment_gateways()->get_available_payment_gateways();

			if ( isset( $availables[ $gatewayId ] ) ) {
				return $availables[ $gatewayId ];
			}
		}

		return null;
	}
}

if ( ! function_exists( 'iamport_order_detail_in_history' ) ) {
	function iamport_order_detail_in_history( $order ) {
		// $pay_method = get_post_meta($order->get_id(), '_iamport_paymethod', true);
		// $pg_provider = get_post_meta($order->get_id(), '_iamport_provider', true);

		// $gateway = find_gateway($pg_provider, $pay_method);
		$gateway = wc_get_payment_gateway_by_order( $order );
		if ( Portone_Helper::isIamportGateway( $gateway ) && method_exists( $gateway, "iamport_order_detail" ) ) { //2.0.41 : iamport 관련 gateway일 때에만 반응해야 함
			$gateway->iamport_order_detail( $order->get_id() );
		}
	}
}

if ( ! function_exists( 'ajax_iamport_payment_info' ) ) {
	function ajax_iamport_payment_info() {
		header( 'Content-type: application/json' );

		if ( ! empty( $_GET[ 'gateway_name' ] ) && ! empty( $_GET[ 'order_key' ] ) ) {
			$gateway_name = portone_get( $_GET, 'gateway_name' );
			$pay_method   = portone_get( $_GET, 'pay_method' );
			$order_key    = portone_get( $_GET, 'order_key' );

			$order_id = portone_get_order_id_by_order_key( $order_key );
			$order    = wc_get_order( $order_id );
			$order->set_payment_method( $gateway_name ); //[2019-07-25]사용자가 선택한 결제수단으로 Gateway 정보를 먼저 바꿔준다.
			$order->save();

			$gateway = wc_get_payment_gateway_by_order( $order );

			//fallback : 2019-02-27 : 3rd party 플러그인에 의해 주문이 생성되는 경우 pay_method 가 없는 order가 존재하는 경우 대비
			if ( ! $gateway ) {

				$pg_provider = $order->get_meta( '_iamport_provider' );

				$gateway = find_gateway( $pg_provider, $pay_method );
			}

			if ( $gateway ) {
				try {
					$iamport_info = $gateway->iamport_payment_info( $order_id );

					echo json_encode( array(
						'result'    => 'success',
						'order_id'  => $order_id,
						'order_key' => $order_key,
						'iamport'   => $iamport_info
					) );
				} catch ( Exception $e ) {
					echo json_encode( array(
						'result'   => 'fail',
						'messages' => $e->getMessage(),
					) );
				}

				wp_die();
			} else {
				echo json_encode( array(
					'result'  => 'fail',
					'message' => __( '해당되는 woocommerce gateway를 찾을 수 없습니다.', 'iamport-for-woocommerce' )
				) );
			}
		}

		echo json_encode( array(
			'result' => 'fail'
		) );

		wp_die();
	}
}

if ( ! function_exists( 'iamport_valid_order_statuses_for_cancel' ) ) {
	function iamport_valid_order_statuses_for_cancel( $statuses, $order = null ) {
		//cancel_order가 실행될 때는 $order를 넘겨주지 않는
//        require_once('lib/Portone_Helper.php');

		$refundable_paid_statuses = array( 'processing' );
		$custom                   = Portone_Helper::paidCustomStatus( false );
		if ( $custom ) {
			$refundable_paid_statuses[] = $custom;
		}

		return array_merge( $statuses, $refundable_paid_statuses );
	}
}

if ( ! function_exists( 'iamport_refund_payment' ) ) {
	function iamport_refund_payment( $order_id ) {
		require_once( dirname( __FILE__ ) . '/lib/iamport.php' );

		$order = new WC_Order( $order_id );

		//[2.1.2] 포트원 관련 Gateway 일 때에만 시도해야 함(예. BACS 등은 시도하면 안됨)
		$gateway = wc_get_payment_gateway_by_order( $order );

		if ( Portone_Helper::isIamportGateway( $gateway ) ) {
			$imp_uid     = $order->get_transaction_id();
			$rest_key    = $order->get_meta( '_iamport_rest_key' );
			$rest_secret = $order->get_meta( '_iamport_rest_secret' );//TODO : secret이 바뀌었을 수 있으므로 order_id 로 gateway설정값을 다시 읽어들여서 처리해야 함

			$iamport = new WooIamport( $rest_key, $rest_secret );

			//전액취소
			$result = $iamport->cancel( array(
				'imp_uid' => $imp_uid,
				'reason'  => __( '구매자 환불요청', 'iamport-for-woocommerce' )
			) );

			if ( $result->success ) {
				$payment_data = $result->data;
				$order->add_order_note( __( '구매자요청에 의해 전액 환불완료', 'iamport-for-woocommerce' ) );
				if ( $payment_data->amount == $payment_data->cancel_amount ) {
					$old_status = $order->get_status();
					$order->update_status( 'refunded' ); //iamport_refund_payment가 old_status -> cancelled로 바뀌는 중이라 update_state('refunded')를 호출하는 것이 향후에 문제가 될 수 있음

					//fire hook
					do_action( 'iamport_order_status_changed', $old_status, $order->get_status(), $order );
				}
			} else {
				$order->add_order_note( $result->error[ 'message' ] );
			}
		}
	}
}

if ( ! function_exists( 'iamport_auto_complete' ) ) {
	function iamport_auto_complete( $order_id ) {
		global $woocommerce;
//        require_once('lib/Portone_Helper.php');

		//custom 상태는 processing처럼 고객 환불이 가능하다는 점에서 completed와 다르다.
		//때문에, completed 설정과 custom 설정을 동시에 했다면 completed설정으로 따라줘야 한다.

		$auto_complete_enabled = get_option( 'woocommerce_iamport_auto_complete' ) !== 'yes' ? false : true;
		$custom                = Portone_Helper::paidCustomStatus( false );

		if ( ! $auto_complete_enabled && empty( $custom ) ) {
			return;
		}

		$order = new WC_Order( $order_id );

		$old_status = $order->get_status();

		if ( $auto_complete_enabled ) {
			$order->update_status( 'completed' );
			$order->add_order_note( '처리중 주문이 완료됨으로 자동 변경되었습니다.' );
		} else {
			$order->update_status( $custom );
			$order->add_order_note( '처리중 주문이 ' . $custom . '으로 자동 변경되었습니다.' );
		}

		//fire hook
		do_action( 'iamport_order_status_changed', $old_status, $order->get_status(), $order );
	}
}

if ( ! function_exists( 'iamport_address_replacements' ) ) {
	function iamport_address_replacements( $replaces, $args ) {
		$replaces[ "{first_name}" ]     = "";
		$replaces[ "{last_name}" ]      = "";
		$replaces[ "{name}" ]           = "";
		$replaces[ "{postcode}" ]       = "";
		$replaces[ "{postcode_upper}" ] = "";
		$replaces[ "{company}" ]        = "";

		return $replaces;
	}
}

if ( ! function_exists( 'iamport_woocommerce_general_settings' ) ) {
	function iamport_woocommerce_general_settings( $settings ) {
		$settings[] = array( 'title' => __( '포트원 옵션', 'iamport-for-woocommerce' ), 'type' => 'title', 'desc' => '', 'id' => 'iamport_general_options' );

		$settings[] = array(
			'title'   => __( '자동 완료됨 처리', 'iamport-for-woocommerce' ),
			'desc'    => __( '처리중 상태를 거치지 않고 완료됨으로 자동 변경하시겠습니까?<br>(우커머스에서 "처리중"상태는 결제가 완료되었음을, "완료됨"상태는 상품발송이 완료되었음을 의미합니다. 발송될 상품없이 결제가 되면 곧 서비스가 개시되어야 하는 경우 사용하시면 편리합니다.', 'iamport-for-woocommerce' ),
			'id'      => 'woocommerce_iamport_auto_complete',
			'default' => 'no',
			'type'    => 'checkbox'
		);

		$settings[] = array( 'type' => 'sectionend', 'id' => 'iamport_general_options' );

		return $settings;
	}
}

if ( ! function_exists( 'iamport_order_endpoint_data' ) ) {
	function iamport_order_endpoint_data() {
		global $woocommerce;

		if ( isset( $_GET[ 'iamport-cancel-action' ] ) && isset( $_GET[ 'order-id' ] ) && isset( $_GET[ 'order-key' ] ) ) {
			$iamport_cancel_action = portone_get( $_GET, 'iamport-cancel-action' );
			$order_id              = portone_get( $_GET, 'order-id' );
			$order_key             = portone_get( $_GET, 'order-key' );
			$page                  = portone_get( $_GET, 'order-page' );

			$order = new WC_Order( $order_id );
			if ( is_a( $order, 'WC_Order' ) && portone_get_order_key( $order ) == $order_key ) {
				if ( ! in_array( $order->get_status(), array( 'completed' ) ) ) {
					return;
				}

				$orders_url = wc_get_endpoint_url( 'orders', $page, wc_get_page_permalink( 'myaccount' ) );
				if ( $iamport_cancel_action === 'refund-ask' ) {
					$redirect_url = add_query_arg( array(
						'iamport-cancel-action' => 'refund',
						'order-page'            => $page,
						'order-id'              => $order_id,
						'order-key'             => $order_key
					), $orders_url );

					ob_start(); ?>
                    <div class="iamport-refund-box" id="iamport-refund-box" style="display:none;clear:both">
                        <p>
							<?php
							// translators: %s: WooCommerce order ID
							echo esc_html( sprintf( __( '#%s 주문을 반품요청하시겠습니까?', 'iamport-for-woocommerce' ), $order_id ) );
							?>
                        </p>
                        <p>
                            <label for="iamport-refund-reason"><?php esc_html_e( '반품요청사유', 'iamport-for-woocommerce' ) ?> : </label>
                            <textarea id="iamport-refund-reason"></textarea>
                        <p id="invalid-reason" style="display:none"><?php esc_html_e( "사유를 입력해주세요", "iamport-for-woocommerce" ) ?></p>
                        </p>
                    </div>

                    <script type="text/javascript">
                        jQuery( function ( $ ) {
                            $( '#iamport-refund-box' ).dialog( {
                                title: "<?php echo esc_js( __( '판매자에게 반품요청하시겠습니까?', 'iamport-for-woocommerce' ) )?>",
                                resizable: false,
                                height: "auto",
                                width: 400,
                                modal: true,
                                close: function () {
                                    history.back();
                                },
                                buttons: {
                                    "<?php echo esc_js( __( '반품요청', 'iamport-for-woocommerce' ) )?>": function () {
                                        var input = $( this ).find( '#iamport-refund-reason' ),
                                            orders_url = '<?php echo esc_url_raw( $redirect_url ); ?>';

                                        var reason = input.val();
                                        if ( reason.length == 0 ) {
                                            $( this ).find( '#invalid-reason' ).show();
                                            return false;
                                        }

                                        $( this ).dialog( "close" );
                                        location.href = orders_url + '&reason=' + encodeURIComponent( reason )
                                    },
                                    "<?php echo esc_js( __( '그냥두기', 'iamport-for-woocommerce' ) )?>": function () {
                                        $( this ).dialog( "close" );
                                    }
                                }
                            } );
                        } );
                    </script>
					<?php
					ob_end_flush();
				} elseif ( $iamport_cancel_action === 'exchange-ask' ) {
					$redirect_url = add_query_arg( array(
						'iamport-cancel-action' => 'exchange',
						'order-page'            => $page,
						'order-id'              => $order_id,
						'order-key'             => $order_key
					), $orders_url );

					ob_start(); ?>
                    <div class="iamport-exchange-box" id="iamport-exchange-box" style="display:none;clear:both">
                        <p>
							<?php
							// translators: %s: WooCommerce order ID
							echo esc_html( sprintf( __( '#%s 주문을 교환요청하시겠습니까?', 'iamport-for-woocommerce' ), $order_id ) );
							?>
                        </p>
                        <p>
                            <label for="iamport-exchange-reason"><?php esc_html_e( '교환요청사유', 'iamport-for-woocommerce' ) ?> : </label>
                            <textarea id="iamport-exchange-reason"></textarea>
                        <p id="invalid-reason" style="display:none"><?php esc_html_e( "사유를 입력해주세요", "iamport-for-woocommerce" ) ?></p>
                        </p>
                    </div>

                    <script type="text/javascript">
                        jQuery( function ( $ ) {
                            $( '#iamport-exchange-box' ).dialog( {
                                title: "<?php esc_html_e( '판매자에게 교환요청하시겠습니까?', 'iamport-for-woocommerce' )?>",
                                resizable: false,
                                height: "auto",
                                width: 400,
                                modal: true,
                                close: function () {
                                    history.back();
                                },
                                buttons: {
                                    "<?php esc_html_e( '교환요청', 'iamport-for-woocommerce' )?>": function () {
                                        var input = $( this ).find( '#iamport-exchange-reason' ),
                                            orders_url = '<?php echo esc_url_raw( $redirect_url );?>';

                                        var reason = input.val();
                                        if ( reason.length == 0 ) {
                                            $( this ).find( '#invalid-reason' ).show();
                                            return false;
                                        }

                                        $( this ).dialog( "close" );
                                        location.href = orders_url + '&reason=' + encodeURIComponent( reason )
                                    },
                                    "<?php esc_html_e( '그냥두기', 'iamport-for-woocommerce' )?>": function () {
                                        $( this ).dialog( "close" );
                                    }
                                }
                            } );
                        } );
                    </script>
					<?php
					ob_end_flush();
				}
			}
		}

	}
}

if ( ! function_exists( 'iamport_cancel_request_actions' ) ) {
	function iamport_cancel_request_actions( $actions, $order ) {
		global $wp;

		if ( in_array( $order->get_status(), array( 'completed' ) ) ) {
			$exchange_capable = iamport_exchange_capable( $order );
			$refund_capable   = iamport_refund_capable( $order );

			$page = $wp->query_vars[ 'orders' ];

			if ( $refund_capable ) {
				$actions[ 'iamport_refund_request' ] = array(
					'name' => __( '반품요청', 'iamport-for-woocommerce' ),
					'url'  => add_query_arg( array(
						'iamport-cancel-action' => 'refund-ask',
						'order-page'            => $page,
						'order-id'              => $order->get_id(),
						'order-key'             => portone_get_order_key( $order )
					), wc_get_endpoint_url( 'orders', $page, wc_get_page_permalink( 'myaccount' ) ) )
				);
			}

			if ( $exchange_capable ) {
				$actions[ 'iamport_exchange_request' ] = array(
					'name' => __( '교환요청', 'iamport-for-woocommerce' ),
					'url'  => add_query_arg( array(
						'iamport-cancel-action' => 'exchange-ask',
						'order-page'            => $page,
						'order-id'              => $order->get_id(),
						'order-key'             => portone_get_order_key( $order )
					), wc_get_endpoint_url( 'orders', $page, wc_get_page_permalink( 'myaccount' ) ) )
				);
			}
		}

		return $actions;
	}
}

if ( ! function_exists( 'iamport_cancel_handle' ) ) {
	function iamport_cancel_handle() {
		global $woocommerce;

		if ( isset( $_GET[ 'iamport-cancel-action' ] ) && isset( $_GET[ 'order-key' ] ) && isset( $_GET[ 'order-id' ] ) ) {
			$order_id  = portone_get( $_GET, 'order-id' );
			$order_key = portone_get( $_GET, 'order-key' );
			$page      = portone_get( $_GET, 'order-page' );
			$reason    = portone_get( $_GET, 'reason', '구매자 요청' );

			$order = new WC_Order( $order_id );
			if ( is_a( $order, 'WC_Order' ) && portone_get_order_key( $order ) == $order_key ) {
				if ( $_GET[ 'iamport-cancel-action' ] == 'refund' ) {
					$refund_capable = iamport_refund_capable( $order );
					if ( ! $refund_capable ) {
						return;
					}

					$old_status = $order->get_status();

					$order->update_status( 'refund-request' );
					// translators: %s: Return Request Reason
					$order->add_order_note( sprintf( __( '반품요청 사유 : %s', 'iamport-for-woocommerce' ), $reason ) );

					//fire hook
					do_action( 'iamport_order_status_changed', $old_status, $order->get_status(), $order );

					wp_safe_redirect( wc_get_endpoint_url( 'orders', $page, wc_get_page_permalink( 'myaccount' ) ) );
				} elseif ( $_GET[ 'iamport-cancel-action' ] == 'exchange' ) {
					$exchange_capable = iamport_exchange_capable( $order );
					if ( ! $exchange_capable ) {
						return;
					}

					$old_status = $order->get_status();

					$order->update_status( 'exchange-request' );
					// translators: %s: Exchange Request Reason
					$order->add_order_note( sprintf( __( '교환요청 사유 : %s', 'iamport-for-woocommerce' ), $reason ) );

					//fire hook
					do_action( 'iamport_order_status_changed', $old_status, $order->get_status(), $order );

					wp_safe_redirect( wc_get_endpoint_url( 'orders', $page, wc_get_page_permalink( 'myaccount' ) ) );
				}
			}
		}
	}
}

if ( ! function_exists( 'iamport_exchange_capable' ) ) {
	function iamport_exchange_capable( $order ) {
		if ( get_option( 'woocommerce_iamport_exchange_capable' ) === 'no' ) {
			return false;
		}

		$limit = get_option( "woocommerce_iamport_exchange_limit", null );
		if ( is_numeric( $limit ) && $limit > 0 ) {
			$completedAt = $order->get_date_completed(); //exchange_capable 은 completed 상태의 주문에 대하여 제공되는 기능이므로, completed 시점 기준으로 날짜 계산
			if ( ! empty( $completedAt ) ) {
				$diff = time() - $completedAt->getTimestamp();

				if ( $diff > $limit * 24 * 60 * 60 ) {
					return false;
				}
			}
		}

		return true;
	}
}

if ( ! function_exists( 'iamport_refund_capable' ) ) {
	function iamport_refund_capable( $order ) {
		if ( get_option( 'woocommerce_iamport_refund_capable' ) === 'no' ) {
			return false;
		}

		$limit = get_option( "woocommerce_iamport_refund_limit" );
		if ( is_numeric( $limit ) && $limit > 0 ) {
			$completedAt = $order->get_date_completed(); //exchange_capable 은 completed 상태의 주문에 대하여 제공되는 기능이므로, completed 시점 기준으로 날짜 계산
			if ( ! empty( $completedAt ) ) {
				$diff = time() - $completedAt->getTimestamp();

				if ( $diff > $limit * 24 * 60 * 60 ) {
					return false;
				}
			}
		}

		return true;
	}
}

if ( ! function_exists( 'iamport_order_is_paid_statuses' ) ) {
	function iamport_order_is_paid_statuses( $statuses ) {
//        require_once('lib/Portone_Helper.php');

		$custom = Portone_Helper::paidCustomStatus( false );
		if ( $custom ) {
			$statuses[] = $custom;
		}

		return $statuses;
	}
}

if ( ! function_exists( 'iamport_add_order_phone_column_header' ) ) {
	function iamport_add_order_phone_column_header( $columns ) {
		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {

			$new_columns[ $column_name ] = $column_info;

			if ( 'shipping_address' === $column_name ) {
				$new_columns[ 'billing_phone' ] = __( '전화번호', 'iamport-for-woocommerce' );
			}
		}

		return $new_columns;
	}
}

if ( ! function_exists( 'iamport_add_order_phone_column_content' ) ) {
	function iamport_add_order_phone_column_content( $column ) {
		global $post;

		if ( 'billing_phone' === $column ) {
			$order = wc_get_order( $post->ID );
			echo esc_html( $order->get_billing_phone() );
		}
	}
}

if ( ! function_exists( 'iamport_naverpay_ext_enable_status' ) ) {
	function iamport_naverpay_ext_enable_status() {
		$npay_ext = get_option( 'woocommerce_iamport_naverpay_ext_settings' );
		if ( ! empty( $npay_ext ) && 'yes' == $npay_ext[ 'enabled' ] ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'ajax_iamport_vbank_refund' ) ) {
	function ajax_iamport_vbank_refund() {
		try {
			$order   = wc_get_order( portone_get( $_REQUEST, 'order_id' ) );
			$gateway = wc_get_payment_gateway_by_order( $order );

			if ( $gateway && is_callable( array( $gateway, 'vbank_refund' ) ) ) {
				$gateway->vbank_refund();
			}

			wp_send_json_error( __( '잘못된 요청입니다.', 'iamport-for-woocommerce' ) );
		} catch ( Exception $e ) {
			wp_send_json_error( sprintf( '[%s] %s', $e->getCode(), $e->getMessage() ) );
		}
	}
}

add_action( 'woocommerce_order_details_after_order_table', 'iamport_order_detail_in_history' );
add_action( 'woocommerce_email_after_order_table', 'iamport_order_detail_in_history' );

//ajax. iamport payment for order_review
add_action( 'wp_ajax_iamport_payment_info', 'ajax_iamport_payment_info' );
add_action( 'wp_ajax_nopriv_iamport_payment_info', 'ajax_iamport_payment_info' );

//ajax. 가상계좌 환불 시도
add_action( 'wp_ajax_iamport_vbank_refund_action', 'ajax_iamport_vbank_refund' );
add_action( 'wp_ajax_nopriv_iamport_vbank_refund_action', 'ajax_iamport_vbank_refund' );

//cancel in my-page
add_filter( 'woocommerce_valid_order_statuses_for_cancel', 'iamport_valid_order_statuses_for_cancel', 10, 2 );
//구매자가 직접 취소할 때 환불처리(processing상태일 때만)
add_action( 'woocommerce_order_status_processing_to_cancelled', 'iamport_refund_payment', 10, 1 );
//[2020-09-25] custom 주문상태에서 환불되는 경우
//        require_once('lib/Portone_Helper.php');
$custom = Portone_Helper::paidCustomStatus( false );
if ( $custom ) {
	add_action( 'woocommerce_order_status_' . $custom . '_to_cancelled', 'iamport_refund_payment', 10, 1 );
}

add_action( 'wp_footer', 'iamport_order_endpoint_data' );
add_action( 'template_redirect', 'iamport_cancel_handle' );
add_filter( 'woocommerce_my_account_my_orders_actions', 'iamport_cancel_request_actions', 10, 2 );

//auto complete추가
// add_filter( 'woocommerce_general_settings', 'iamport_woocommerce_general_settings', 10, 1 ); 별도 탭으로 변경
add_action( 'woocommerce_order_status_pending_to_processing', 'iamport_auto_complete', 10, 1 );
add_action( 'woocommerce_order_status_on-hold_to_processing', 'iamport_auto_complete', 10, 1 );
add_action( 'woocommerce_order_status_failed_to_processing', 'iamport_auto_complete', 10, 1 );
add_action( 'woocommerce_order_status_awaiting-vbank_to_processing', 'iamport_auto_complete', 10, 1 );

//buyer_addr 에 postcode 등이 넘어오지 않도록 replace filter
// add_filter( "woocommerce_formatted_address_replacements", "iamport_address_replacements", 10, 2 );

//네이버페이(결제형) 상품 카테고리
add_action( "product_cat_add_form_fields", array( "WC_Gateway_Portone_NaverPayExt", "render_add_product_category" ), 50 );
add_action( "product_cat_edit_form_fields", array( "WC_Gateway_Portone_NaverPayExt", "render_edit_product_category" ), 50 );

add_action( "edited_product_cat", array( "WC_Gateway_Portone_NaverPayExt", "save_edit_product_category" ) );
add_action( "create_product_cat", array( "WC_Gateway_Portone_NaverPayExt", "save_add_product_category" ) );

add_filter( 'woocommerce_order_is_paid_statuses', 'iamport_order_is_paid_statuses' );

//주문내역 리스트에 전화번호 추가
add_filter( 'manage_edit-shop_order_columns', 'iamport_add_order_phone_column_header', 20 );
add_action( 'manage_shop_order_posts_custom_column', 'iamport_add_order_phone_column_content' );