<?php


//소스에 URL로 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Payment_Gateway' ) ) {

	abstract class PORTONE_Payment_Gateway extends WC_Payment_Gateway {
		protected $imp_user_code;
		protected $imp_rest_key;
		protected $pg_provider;
		protected $pg_id;
		protected $imp_rest_secret;
		protected static $logger = null;

		public function __construct() {
			$this->id = $this->get_gateway_id(); //id가 먼저 세팅되어야 init_setting가 제대로 동작

			$this->init_form_fields();
			$this->init_settings();

			$this->imp_user_code   = portone_get( $this->settings, 'imp_user_code' );
			$this->imp_rest_key    = portone_get( $this->settings, 'imp_rest_key' );
			$this->imp_rest_secret = portone_get( $this->settings, 'imp_rest_secret' );

			//woocommerce action
			add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'check_payment_response' ) );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'send_usage_notification' ) );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

			add_filter( 'woocommerce_generate_order_key', array( $this, 'generate_order_key' ) );
		}
		abstract protected function get_gateway_id();
		abstract public function iamport_order_detail( $order_id );

		public function update_shipping_info( $order, $payment_data ) {
		}
		function add_log( $msg ) {
			if ( is_null( self::$logger ) ) {
				self::$logger = new WC_Logger();
			}

			self::$logger->add( $this->id, print_r( $msg, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		}
		public function init_form_fields() {
			//iamport기본 플러그인에 해당 정보가 세팅되어있는지 먼저 확인
			$default_user_code  = get_option( 'iamport_user_code' );
			$default_api_key    = get_option( 'iamport_rest_key' );
			$default_api_secret = get_option( 'iamport_rest_secret' );

			$this->form_fields = array(
				'imp_user_code'   => array(
					'title'       => __( '[포트원] 가맹점 식별코드', 'iamport-for-woocommerce' ),
					'type'        => 'text',
					'description' => __( 'https://admin.portone.io에서 회원가입 후, "결제 연동 > 연동 정보 > 식별코드 ・ API Keys > V1 API"에서 확인하실 수 있습니다.', 'iamport-for-woocommerce' ),
					'label'       => __( '[포트원] 가맹점 식별코드', 'iamport-for-woocommerce' ),
					'default'     => $default_user_code
				),
				'imp_rest_key'    => array(
					'title'       => __( '[포트원] REST API 키', 'iamport-for-woocommerce' ),
					'type'        => 'text',
					'description' => __( 'https://admin.portone.io에서 회원가입 후, "결제 연동 > 연동 정보 > 식별코드 ・ API Keys > V1 API"에서 확인하실 수 있습니다.', 'iamport-for-woocommerce' ),
					'label'       => __( '[포트원] REST API 키', 'iamport-for-woocommerce' ),
					'default'     => $default_api_key
				),
				'imp_rest_secret' => array(
					'title'       => __( '[포트원] REST API Secret', 'iamport-for-woocommerce' ),
					'type'        => 'text',
					'description' => __( 'https://admin.portone.io에서 회원가입 후, "결제 연동 > 연동 정보 > 식별코드 ・ API Keys > V1 API"에서 확인하실 수 있습니다.', 'iamport-for-woocommerce' ),
					'label'       => __( '[포트원] REST API Secret', 'iamport-for-woocommerce' ),
					'default'     => $default_api_secret
				)
			);
		}

		public function generate_order_key( $order_key ) {
			//22자 글자제한이 있어서 prefix 를 줄임
			return 'p' . wp_rand( 0, 99999 ) . substr( preg_replace( "/[^A-Za-z0-9_]/", '', uniqid( '', true ) ), 10 ); //more entropy
		}

		protected function getKcpProducts( $order_id ) {
			$order = new WC_Order( $order_id );

			$cart_items  = $order->get_items();
			$kcpProducts = array();

			foreach ( $cart_items as $item_id => $item ) { //WC_Order_Item 에서 지원되는  메소드만 사용하고 있음
				$kcpProducts[] = array(
					"orderNumber" => $item->get_order_id() . "-" . $item->get_id(),
					"name"        => $item->get_name(),
					"quantity"    => wc_get_order_item_meta( $item_id, '_qty', true ),
					"amount"      => wc_get_order_item_meta( $item_id, '_line_total', true ) + wc_get_order_item_meta( $item_id, '_line_tax', true ),
				);
			}

			return $kcpProducts;
		}

		public function is_paid_confirmed( $order, $payment_data ) {
			return $order->get_total() == $payment_data->amount;
		}

		// common for check payment
		// #1. woocommerce 결제 프로세스시 전달되는 데이터

		// #2. Notification URL에 의해 전달되는 데이터
		public function check_payment_response() {
			global $wpdb;

			$http_method = portone_get( $_SERVER, 'REQUEST_METHOD' );
			$http_param  = array(
				'imp_uid'      => $this->http_param( 'imp_uid', $http_method ),
				'merchant_uid' => $this->http_param( 'merchant_uid', $http_method ),
				'order_id'     => $this->http_param( 'order_id', $http_method )
			);

			$called_from_iamport = empty( $http_param[ 'order_id' ] ); //wp_redirect 안하기 위해서 boolean 기록

			if ( ! empty( $http_param[ 'imp_uid' ] ) ) {
				//결제승인 결과조회
				require_once( PORTONE_PLUGIN_DIR . '/includes/lib/iamport.php' );

				$imp_uid = $http_param[ 'imp_uid' ];

				//Gateway마다 다른 key/secret을 가질 수 있으므로 현재 Gateway를 확인하고처리
				$auth = $this->getRestInfo( $http_param[ 'merchant_uid' ], $called_from_iamport );

				$iamport = new WooIamport( $auth[ 'imp_rest_key' ], $auth[ 'imp_rest_secret' ] );
				$result  = $iamport->findByImpUID( $imp_uid );
				$loggers = array();

				if ( $result->success ) {
					$loggers[]    = "A:success";
					$payment_data = $result->data;

					//보안상 REST API로부터 받아온 merchant_uid에서 order_id를 찾아내야한다.(GET파라메터의 order_id를 100%신뢰하지 않도록)
					$order_id = portone_get_order_id_by_order_key( $payment_data->merchant_uid );
					$gateway  = wc_get_payment_gateway_by_order( $order_id );

					$order = wc_get_order( $order_id );
					$order->update_meta_data( '_iamport_rest_key', $auth[ 'imp_rest_key' ] );
					$order->update_meta_data( '_iamport_rest_secret', $auth[ 'imp_rest_secret' ] );
					$order->update_meta_data( '_iamport_provider', $payment_data->pg_provider );
					$order->update_meta_data( '_iamport_paymethod', $payment_data->pay_method );
					$order->update_meta_data( '_iamport_pg_tid', $payment_data->pg_tid );
					$order->update_meta_data( '_iamport_receipt_url', $payment_data->receipt_url );

					$order->save_meta_data();

					$gateway->update_shipping_info( $order, $payment_data );

					if ( $payment_data->status === 'paid' ) {
						$loggers[] = "B:paid";

						try {
							$wpdb->query( "BEGIN" );
							//lock the row
							$synced_row   = portone_get_order_row( $order_id );
							$order_status = portone_get_order_status_from_order_row( $synced_row );

							if ( $gateway->is_paid_confirmed( $order, $payment_data ) ) {
								$loggers[] = "C:confirm";

								if ( ! $this->has_status( $order_status, wc_get_is_paid_statuses() ) ) {
									$loggers[] = "D:completed";

									$order->set_payment_method( $gateway );

									//fire hook
									do_action( 'iamport_pre_order_completed', $order, $payment_data );

									$order->payment_complete( $payment_data->imp_uid ); //imp_uid

									if ( $payment_data->pay_method == 'vbank' ) {
										$order->update_meta_data( '_portone_vbank_noti_received', 'yes' );
										$order->save_meta_data();
									}

									$wpdb->query( "COMMIT" );

									//fire hook
									do_action( 'iamport_post_order_completed', $order, $payment_data );
									do_action( 'iamport_order_status_changed', $order_status, $order->get_status(), $order );

									$this->add_log( $loggers );
									$called_from_iamport ? exit( 'Payment Saved' ) : wp_safe_redirect( $this->get_return_url( $order ) );
								} else {
									$loggers[] = "D:status(" . $order_status . ")";

									$wpdb->query( "ROLLBACK" );
									//이미 이뤄진 주문 : 2016-09-01 / redirect가 중복으로 발생되는 경우들이 발견
									$this->add_log( $loggers );
									$called_from_iamport ? exit( 'Already Payment Saved' ) : wp_safe_redirect( $this->get_return_url( $order ) );
								}

								return;
							} else {
								$loggers[] = "C:invalid";

								$order->add_order_note( __( '요청하신 결제금액이 다릅니다.', 'iamport-for-woocommerce' ) );
								wc_add_notice( __( '요청하신 결제금액이 다릅니다.', 'iamport-for-woocommerce' ), 'error' );

								$wpdb->query( "COMMIT" );
							}
						} catch ( Exception $e ) {
							$loggers[] = "C:" . $e->getMessage();

							$wpdb->query( "ROLLBACK" );
						}
					} elseif ( $payment_data->status == 'ready' ) {
						$loggers[] = "B:ready";

						try {
							$wpdb->query( "BEGIN" );
							//lock the row
							$synced_row   = portone_get_order_row( $order_id );
							$order_status = portone_get_order_status_from_order_row( $synced_row );

							$order = new WC_Order( $order_id ); //lock잡은 후 호출(2017-01-16 : 의미없음. [1.6.8] synced_row의 값을 활용해서 status체크해야 함)

							if ( $payment_data->pay_method == 'vbank' ) {
								$loggers[] = "C:vbank";

								$vbank_name   = $payment_data->vbank_name;
								$vbank_num    = $payment_data->vbank_num;
								$vbank_date   = $payment_data->vbank_date;
								$vbank_holder = trim( $payment_data->vbank_holder );

								//가상계좌 입금할 계좌정보 기록
								$order->update_meta_data( '_iamport_vbank_name', $vbank_name );
								$order->update_meta_data( '_iamport_vbank_num', $vbank_num );
								$order->update_meta_data( '_iamport_vbank_date', $vbank_date );
								$order->update_meta_data( '_iamport_vbank_holder', $vbank_holder );
								$order->save_meta_data();

								//가상계좌 입금대기 중
								if ( ! $this->has_status( $order_status, array( 'awaiting-vbank' ) ) ) {
									$loggers[] = "D:awaiting";

									$order->update_status( 'awaiting-vbank', __( '가상계좌 입금대기 중', 'iamport-for-woocommerce' ) );
									$order->set_payment_method( $gateway );
									$order->save();

									$wpdb->query( "COMMIT" );

									do_action( 'iamport_order_status_changed', $order_status, $order->get_status(), $order );
								} else {
									$loggers[] = "D:status(" . $order_status . ")";

									$wpdb->query( "ROLLBACK" );
								}

								$this->add_log( $loggers );
								$called_from_iamport ? exit( 'Awaiting Vbank' ) : wp_safe_redirect( $this->get_return_url( $order ) );

								return;
							} else {
								$loggers[] = "C:invalid";

								$order->add_order_note( __( '실제 결제가 이루어지지 않았습니다.', 'iamport-for-woocommerce' ) );
								wc_add_notice( __( '실제 결제가 이루어지지 않았습니다.', 'iamport-for-woocommerce' ), 'error' );

								$wpdb->query( "COMMIT" );
							}
						} catch ( Exception $e ) {
							$loggers[] = "C:" . $e->getMessage();

							$wpdb->query( "ROLLBACK" );
						}
					} elseif ( $payment_data->status == 'failed' ) {
						$loggers[] = "B:failed";

						$order = new WC_Order( $order_id );

						$failMessage = $payment_data->fail_reason;
						if ( empty( $failMessage ) ) {
							$failMessage = __( '결제요청 승인에 실패하였습니다.', 'iamport-for-woocommerce' );
						}

						$order->add_order_note( $failMessage );
						wc_add_notice( $failMessage, 'error' );
					} elseif ( $payment_data->status == 'cancelled' ) {
						//포트원 관리자 페이지에서 취소하여 Notification이 발송된 경우도 대응
						$loggers[] = "B:cancelled";

						try {
							$wpdb->query( "BEGIN" );
							//lock the row
							$synced_row   = portone_get_order_row( $order_id );
							$order_status = portone_get_order_status_from_order_row( $synced_row );

							$order = new WC_Order( $order_id ); //lock잡은 후 호출(2017-01-16 : 의미없음. [1.6.8] synced_row의 값을 활용해서 status체크해야 함)

							if ( ! $this->has_status( $order_status, array( 'cancelled', 'refunded' ) ) ) {
								$amountLeft = $payment_data->amount > $payment_data->cancel_amount; //취소할 잔액이 남음

								if ( $amountLeft ) { //한 번 더 환불이 가능함. 다음 번 환불이 가능하도록 status는 바꾸지 않음
									$len       = count( $payment_data->cancel_history ); // always > 0
									$increment = $len - count( $order->get_refunds() );

									for ( $i = 0; $i < $increment; $i ++ ) {
										$cancelItem = $payment_data->cancel_history[ $len - $increment + $i ];

										// 취소내역을 만들어줌 (부분취소도 대응가능)
										$refund = wc_create_refund( array(
											'amount'   => $cancelItem->amount,
											'reason'   => $cancelItem->reason,
											'order_id' => $order_id
										) );

										if ( is_wp_error( $refund ) ) {
											$order->add_order_note( $refund->get_error_message() );
										} else {
											// translators: %s: Cancel Amount
											$order->add_order_note( sprintf( __( '부분환불(%s원) 내역을 환불정보에 반영하였습니다.', 'iamport-for-woocommerce' ), number_format( $cancelItem->amount ) ) );
										}
									}
								} else {
									$order->update_status( 'refunded' ); //imp_uid
									$order->add_order_note( __( '전액환불되어 우커머스 주문 상태를 "환불됨"으로 수정합니다.', 'iamport-for-woocommerce' ) );

									//fire hook
									do_action( 'iamport_order_status_changed', $order_status, $order->get_status(), $order );
								}

								$wpdb->query( "COMMIT" );

								do_action( 'iamport_order_status_changed', $order_status, $order->get_status(), $order );
							} else {
								$wpdb->query( "ROLLBACK" );
							}

							$this->add_log( $loggers );
							$called_from_iamport ? exit( 'Refund Information Saved' ) : wp_safe_redirect( $this->get_return_url( $order ) );

							return;
						} catch ( Exception $e ) {
							$loggers[] = "C:" . $e->getMessage();

							$wpdb->query( "ROLLBACK" );
						}
					}
				} else { // not result->success
					$loggers[] = "A:fail - " . $result->error[ 'message' ];

					if ( ! empty( $http_param[ 'order_id' ] ) ) {
						$order = new WC_Order( $http_param[ 'order_id' ] );

						// [v2.0.51] 운영 중인 워드프레스 서버의 문제로 HTTP통신을 요청하지 못한 상황일 수도 있는데, 굳이 failed 상태로 변경할 필요는 없을 듯
						// $old_status = $order->get_status();
						// $order->update_status('failed');
						// translators: %s: error message
						$order->add_order_note( sprintf( __( '결제승인정보를 받아오지 못했습니다. 관리자에게 문의해주세요. %s', 'iamport-for-woocommerce' ), $result->error[ 'message' ] ) );

						//fire hook
						// do_action('iamport_order_status_changed', $old_status, $order->get_status(), $order);
					}
					wc_add_notice( $result->error[ 'message' ], 'error' );
				}

				if ( ! empty( $order ) ) {
					$default_redirect_url = $order->get_checkout_payment_url( true );
				} else {
					$default_redirect_url = '/';
				}

				$this->add_log( $loggers );
				$called_from_iamport ? exit( json_encode( array( "version" => "IamportForWoocommerce 2.2.17", "log" => $loggers ) ) ) : wp_safe_redirect( $default_redirect_url );
			} else {
				//just test(포트원이 지원하는대로 호출되지 않음)
				exit( json_encode( array( "version" => "IamportForWoocommerce 2.2.17" ) ) );
			}
		}

		//common for payment
		public function process_payment( $order_id ) {
			global $woocommerce;
			$order = new WC_Order( $order_id );

			if ( $order->has_status( wc_get_is_paid_statuses() ) ) {
				$redirect_url = $this->get_return_url( $order );
			} else {
				$redirect_url = $order->get_checkout_payment_url( false );
			}

			try {
				$iamport_info = $this->iamport_payment_info( $order_id );

				return array(
					'result'       => 'success',
					'redirect'     => $redirect_url,
					'order_id'     => $order_id,
					'order_key'    => portone_get_order_key( $order ),
					'iamport'      => $iamport_info,
					'iamport_json' => json_encode( $iamport_info )
				);
			} catch ( Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );

				return array(
					'result'   => 'fail',
					'messages' => $e->getMessage(),
				);
			}
		}

		//common for refund
		public function process_refund( $order_id, $amount = null, $reason = '' ) {
			require_once( PORTONE_PLUGIN_DIR . '/includes/lib/iamport.php' );

			global $woocommerce;
			$order = new WC_Order( $order_id );

			$imp_uid = $order->get_transaction_id();
			$iamport = new WooIamport( $this->imp_rest_key, $this->imp_rest_secret );

			// 만약 데이터 동기화에 실패하는 상황이 되어 imp_uid가 없더라도 order_key가 있으면 취소를 시도해볼 수 있다.
			if ( empty( $imp_uid ) ) {
				$cancel_data = array(
					'merchant_uid' => portone_get_order_key( $order ),
					'reason'       => $reason,
					'amount'       => $amount
				);
			} else {
				$cancel_data = array(
					'imp_uid' => $imp_uid,
					'reason'  => $reason,
					'amount'  => $amount
				);
			}

			$refundTaxFree = intval( portone_get( $_POST, 'iamport_refund_taxfree' ) );
			if ( $refundTaxFree > 0 ) {
				$cancel_data[ 'tax_free' ] = $refundTaxFree;
			}

			$result = $iamport->cancel( $cancel_data );

			if ( $result->success ) {
				$payment_data = $result->data;
				// translators: %s: Cancel Amount
				$order->add_order_note( sprintf( __( '%s 원 환불완료', 'iamport-for-woocommerce' ), number_format( $amount ) ) );
				if ( $payment_data->amount == $payment_data->cancel_amount ) {
					$old_status = $order->get_status();
					$order->update_status( 'refunded' );

					//fire hook
					do_action( 'iamport_order_status_changed', $old_status, $order->get_status(), $order );
				}

				return true;
			} else {
				$order->add_order_note( $result->error[ 'message' ] );

				return false;
			}

			return false;
		}

		public function iamport_payment_info( $order_id ) {
			global $woocommerce;

			$order        = new WC_Order( $order_id );
			$order_name   = $this->get_order_name( $order );
			$redirect_url = add_query_arg( array( 'order_id' => $order_id, 'wc-api' => get_class( $this ) ), $order->get_checkout_payment_url() );
			$notice_url   = Portone_Helper::get_notice_url();
			$maxCardQuota = Portone_Helper::get_max_card_quota();

			$stripLineFeedInAddress = get_option( 'woocommerce_iamport_strip_line_feed_in_address', 'yes' ) != 'no'; //strip 이 기본
			$buyerAddr              = $order->get_formatted_shipping_address();
			if ( $stripLineFeedInAddress ) {
				$buyerAddr = wp_strip_all_tags( $buyerAddr ); //br 태그 모두 제거
			} else {
				$buyerAddr = str_replace( '<br/>', "\n", $buyerAddr );
			}

			$response = array(
				'user_code'      => $this->imp_user_code,
				'name'           => $order_name,
				'merchant_uid'   => portone_get_order_key( $order ),
				'amount'         => $order->get_total(),                                                         //amount
				'buyer_name'     => trim( $order->get_billing_last_name() . $order->get_billing_first_name() ),  //name
				'buyer_email'    => $order->get_billing_email(),                                                 //email
				'buyer_tel'      => $order->get_billing_phone() ? $order->get_billing_phone() : '010-1234-5678', //tel. KG이니시스 오류 방지. 다른 플러그인을 통해 전화번호 required해제한 경우가 있음
				'buyer_addr'     => $buyerAddr,                                                                  //address
				'buyer_postcode' => $order->get_shipping_postcode(),
				// 'vbank_due' => date('Ymd', strtotime("+1 day")),
				'm_redirect_url' => $redirect_url,
				'currency'       => $order->get_currency(),
				'app_scheme'     => get_option( 'woocommerce_iamport_app_scheme', '' ),
				'custom_data'    => array(
					$order->get_id(),
					'devops' => "codemshop"
				)
			);

			if ( empty( $response[ "buyer_name" ] ) ) {
				$response[ "buyer_name" ] = $this->get_default_user_name();
			}
			if ( $notice_url ) {
				$response[ 'notice_url' ] = $notice_url;
			}

			if ( wc_tax_enabled() ) {
				$tax_free_amount        = Portone_Helper::get_tax_free_amount( $order );
				$response[ "tax_free" ] = intval( $tax_free_amount );
				// $vat = $order->get_total_tax();
				// $response['vat'] = intval($vat);
			}

			$language = $this->paymentLanguage( $order );
			if ( $language ) {
				$response[ 'language' ] = $language;
			}

			//[2019-08-08] 최대 할부개월수 제한
			if ( $maxCardQuota > 0 ) {
				$response[ 'card_quota' ] = range( 0, $maxCardQuota );
			}

			//서비스 제공기간
			foreach ( $order->get_items() as $it ) {
				$product = $it->get_product();

				if ( $product instanceof WC_Product ) { //$product 가 boolean 인 경우가 있어 타입 체크
					$product_id = $product->get_id();
					$parent_id  = $product->get_parent_id();
					if ( ! empty( $parent_id ) ) {
						$product_id = $parent_id;
					}

					$service_period = $this->getServicePeriod( $product_id );
					if ( ! empty( $service_period ) ) { //유효한 period 정보가 하나라도 있으면, 먼저 걸리는 것을 적용(한 개만 적용 가능하므로)
						$response[ 'period' ] = $service_period;
					}
				}
			}

//			$response['pay_method'] = 'naverpay';

			return $response;
		}

		protected function get_default_user_name() {
			$current_user = wp_get_current_user();

			if ( $current_user->ID > 0 ) {
				$name = $current_user->user_lastname . $current_user->user_firstname;
				if ( ! empty( $name ) ) {
					return $name;
				}

				$name = $current_user->display_name;
				if ( ! empty( $name ) ) {
					return $name;
				}

				$name = $current_user->user_login;
				if ( ! empty( $name ) ) {
					return $name;
				}
			}

			return "구매자";
		}

		protected function get_order_name( $order ) {
			$order_name = "#" . $order->get_order_number() . "번 주문";

			$cart_items = $order->get_items();
			$cnt        = count( $cart_items );

			if ( ! empty( $cart_items ) ) {
				$index = 0;
				foreach ( $cart_items as $item ) {
					if ( $index == 0 ) {
						$order_name = $item->get_name();
					} elseif ( $index > 0 ) {

						$order_name .= ' 외 ' . ( $cnt - 1 );
						break;
					}

					$index ++;
				}
			}

			$order_name = apply_filters( 'iamport_simple_order_name', $order_name, $order );

			return $order_name;
		}

		protected function has_status( $current_status, $status ) {
			$formed_status = $this->format_status( $current_status );

			return apply_filters( 'woocommerce_order_has_status', ( is_array( $status ) && in_array( $formed_status, $status ) ) || $formed_status === $status ? true : false, null, $status );
		}

		protected function format_status( $raw_status ) {
			return apply_filters( 'woocommerce_order_get_status', 'wc-' === substr( $raw_status, 0, 3 ) ? substr( $raw_status, 3 ) : $raw_status, null );
		}

		protected function http_param( $name, $default_method ) {
			if ( $default_method == 'GET' ) {
				if ( isset( $_GET[ $name ] ) ) {
					return portone_get( $_GET, $name );
				}
			} elseif ( $default_method == 'POST' ) {
				//bugfix-2016-08-03 : 포트원 Notification URL에서 application/x-form-www-urlencoded 와 application/json 중 Content-Type을 선택적으로 지정하여 노티할 수 있음
				if ( portone_get( $_SERVER, "CONTENT_TYPE" ) === 'application/json' ) {
					$data = json_decode( file_get_contents( 'php://input' ), true );

					if ( isset( $data[ $name ] ) ) {
						return $data[ $name ];
					}
				} else {
					if ( isset( $_POST[ $name ] ) ) {
						return portone_get( $_POST, $name );
					}
					if ( isset( $_GET[ $name ] ) ) {
						return portone_get( $_GET, $name );
					}
				}
			}

			return null;
		}
		protected function paymentLanguage( $order ) {
			$payment_language = null;

			$locale = get_locale();

			if ( 'iamport_eximbay' == $this->get_gateway_id() ) {
				if ( $locale == 'ja' ) {
					$payment_language = 'jp';
				} elseif ( $locale == 'zh_CN' ) {
					$payment_language = 'zh';
				} elseif ( $locale != 'ko_KR' ) {
					$payment_language = 'en';
				}
			} else {
				if ( $locale != 'ko_KR' ) {
					$payment_language = 'en';
				}
			}

			return apply_filters( 'portone_payment_language', $payment_language, $order );
		}

		protected function getRestInfo( $merchant_uid, $called_from_iamport = true ) {
			if ( $called_from_iamport ) {
				$order_id = portone_get_order_id_by_order_key( $merchant_uid );
				$gateway  = wc_get_payment_gateway_by_order( $order_id );

				if ( $gateway ) {
					return array(
						'imp_rest_key'    => $gateway->imp_rest_key,
						'imp_rest_secret' => $gateway->imp_rest_secret,
					);
				}

			}

			return array(
				'imp_rest_key'    => $this->imp_rest_key,
				'imp_rest_secret' => $this->imp_rest_secret,
			);
		}

		protected function isMobile() {
			$userAgent = portone_get( $_SERVER, 'HTTP_USER_AGENT' );

			$mobiles = array(
				'Android',
				'AvantGo',
				'BlackBerry',
				'DoCoMo',
				'Fennec',
				'iPod',
				'iPhone',
				'iPad',
				'J2ME',
				'MIDP',
				'NetFront',
				'Nokia',
				'Opera Mini',
				'Opera Mobi',
				'PalmOS',
				'PalmSource',
				'portalmmm',
				'Plucker',
				'ReqwirelessWeb',
				'SonyEricsson',
				'Symbian',
				'UP\\.Browser',
				'webOS',
				'Windows CE',
				'Windows Phone OS',
				'Xiino'
			);

			$pattern = '/' . implode( '|', $mobiles ) . '/i';

			return (bool) preg_match( $pattern, $userAgent );
		}

		protected function getServicePeriod( $productId ) {
			$keys   = array( 'iamport_product_service_period_from', 'iamport_product_service_period_to', 'iamport_product_service_period_interval' );
			$period = array();

			foreach ( $keys as $k ) {
				$product = wc_get_product( $productId );
				$val     = $product->get_meta( $k );

				if ( ! empty( $val ) ) {
					$period[ str_replace( 'iamport_product_service_period_', '', $k ) ] = $val;
				}
			}

			return $period;
		}
		public function send_usage_notification() {
			portone_send_usage_notification( $this );
		}
	}

}