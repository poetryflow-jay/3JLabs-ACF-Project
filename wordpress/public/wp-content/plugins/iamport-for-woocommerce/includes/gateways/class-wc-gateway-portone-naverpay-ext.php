<?php

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class WC_Gateway_Portone_NaverPayExt extends PORTONE_Payment_Gateway {

	const GATEWAY_ID = 'iamport_naverpay_ext';

	public static $PRODUCT_CATEGORIES = array(
		"NONE"                       => "해당사항없음",
		"BOOK_GENERAL"               => "[도서] 일반",
		"BOOK_EBOOK"                 => "[도서] 전자책",
		"BOOK_USED"                  => "[도서] 중고",
		"MUSIC_CD"                   => "[음악] CD",
		"MUSIC_LP"                   => "[음악] LP",
		"MUSIC_USED"                 => "[음악] 중고 음반",
		"MOVIE_DVD"                  => "[영화] DVD",
		"MOVIE_BLUERAY"              => "[영화] 블루레이",
		"MOVIE_VOD"                  => "[영화] VOD",
		"MOVIE_TICKET"               => "[영화] 티켓",
		"MOVIE_USED"                 => "[영화] 중고 DVD, 블루 레이등",
		"PRODUCT_GENERAL"            => "[상품] 일반",
		"PRODUCT_CASHABLE"           => "[상품] 환금성",
		"PRODUCT_CLAIM"              => "[상품] 클레임",
		"PRODUCT_DIGITAL_CONTENT"    => "[상품] 디지털 컨텐츠",
		"PRODUCT_SUPPORT"            => "[상품] 후원",
		"PLAY_TICKET"                => "[공연/전시] 티켓",
		"TRAVEL_DOMESTIC"            => "[여행] 국내 숙박",
		"TRAVEL_OVERSEA"             => "[여행] 해외 숙박",
		"INSURANCE_CAR"              => "[보험] 자동차보험",
		"INSURANCE_DRIVER"           => "[보험] 운전자보험",
		"INSURANCE_HEALTH"           => "[보험] 건강보험",
		"INSURANCE_CHILD"            => "[보험] 어린이보험",
		"INSURANCE_TRAVELER"         => "[보험] 여행자보험",
		"INSURANCE_GOLF"             => "[보험] 골프보험",
		"INSURANCE_ANNUITY"          => "[보험] 연금보험",
		"INSURANCE_ANNUITY_SAVING"   => "[보험] 연금저축보험",
		"INSURANCE_SAVING"           => "[보험] 저축보험",
		"INSURANCE_VARIABLE_ANNUITY" => "[보험] 변액적립보험",
		"INSURANCE_CANCER"           => "[보험] 암보험",
		"INSURANCE_DENTIST"          => "[보험] 치아보험",
		"INSURANCE_ACCIDENT"         => "[보험] 상해보험",
		"INSURANCE_SEVERANCE"        => "[보험] 퇴직연금",
		"FLIGHT_TICKET"              => "[항공] 티켓",
		"FOOD_DELIVERY"              => "[음식] 배달",
		"ETC_ETC"                    => "[기타]",
	);

	public function __construct() {
		parent::__construct();

		//settings
		$this->method_title       = __( '포트원(결제형-네이버페이)', 'iamport-for-woocommerce' );
		$this->method_description = '<b>네이버페이 정책상, 결제형-네이버페이는 사전 승인된 일부 가맹점에 한하여 제공되고 있으며 일반적으로는 "포트원(네이버페이)"를 사용해주셔야 합니다. 결제형-네이버페이 가입기준에 대해서는 포트원 고객센터(1670-5176)으로 문의 부탁드립니다.</b>';
		$this->has_fields         = true;
		$this->supports           = array( 'products', 'refunds', 'subscriptions', 'subscription_reactivation'/*이것이 있어야 subscription 후 active상태로 들어갈 수 있음*/, 'subscription_suspension', 'subscription_cancellation', 'subscription_date_changes', 'subscription_amount_changes', 'subscription_payment_method_change_customer', 'multiple_subscriptions' );

		$this->title       = portone_get( $this->settings, 'title' );
		$this->description = portone_get_not_clean( $this->settings, 'description' );

		add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 2 );

		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'eliminateUnderInspection' ) );

		//실패한 경우 결제페이지에서 order-receipt.php 가 include되지 않도록 override
		add_filter( "wc_get_template", array( $this, "my_template" ), 10, 3 );
	}

	protected function get_gateway_id() {
		return self::GATEWAY_ID;
	}

	public function eliminateUnderInspection( $gateways ) {
		if ( isset( $this->settings[ "debug_mode" ] ) && $this->settings[ "debug_mode" ] === "yes" ) { //검수모드 체크상태

			$debuggers = isset( $this->settings[ "debuggers" ] ) ? strval( $this->settings[ "debuggers" ] ) : "";
			$allowed   = explode( ",", $debuggers );
			$login     = wp_get_current_user();

			if ( 0 == $login->ID || ( is_array( $allowed ) && ! in_array( $login->user_login, $allowed ) ) ) {
				unset( $gateways[ $this->get_gateway_id() ] );
			}
		}

		return $gateways;
	}

	public function init_form_fields() {
		parent::init_form_fields();

		$this->form_fields = array_merge( array(
			'enabled'     => array(
				'title'   => __( 'Enable/Disable', 'iamport-for-woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( '포트원(결제형-네이버페이) 사용. (결제형-네이버페이 설정을 위해서는 네이버페이 가입승인 후 포트원 고객센터로 연락부탁드립니다.)', 'iamport-for-woocommerce' ),
				'default' => 'no'
			),
			'title'       => array(
				'title'       => __( 'Title', 'iamport-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( '구매자에게 표시될 구매수단명', 'iamport-for-woocommerce' ),
				'default'     => __( '네이버페이(결제형)', 'iamport-for-woocommerce' ),
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => __( 'Customer Message', 'iamport-for-woocommerce' ),
				'type'        => 'textarea',
				'description' => __( '구매자에게 결제수단에 대한 상세설명을 합니다.', 'iamport-for-woocommerce' ),
				'default'     => __( '주문확정 버튼을 클릭하시면 네이버페이 결제창이 나타나 결제를 진행하실 수 있습니다.', 'iamport-for-woocommerce' )
			),
			'debug_mode'  => array(
				'title'       => __( '네이버페이 검수모드', 'iamport-for-woocommerce' ),
				'description' => __( '네이버페이 검수단계에서는 일반 사용자에게 네이버페이 결제수단이 보여지면 안됩니다. "검수모드" [체크]하시면 특정 사용자에게만 네이버페이 결제수단이 노출되며, [체크해제]하시면 모든 사용자에게 노출됩니다. 아래에서 네이버페이 검수용 사용자 아이디를 지정하시면 됩니다.', 'iamport-for-woocommerce' ),
				'type'        => 'checkbox',
				'label'       => __( '네이버페이 검수모드', 'iamport-for-woocommerce' ),
				'default'     => 'no'
			),
			'debuggers'   => array(
				'title'       => __( '네이버페이 검수용 사용자명', 'iamport-for-woocommerce' ),
				'label'       => __( '네이버페이 검수용 사용자명', 'iamport-for-woocommerce' ),
				'description' => __( '네이버페이 검수단계에서는 특정 사용자에게만 네이버페이 결제수단을 노출합니다. (콤마로 구분하여 여러 명 지정 가능)', 'iamport-for-woocommerce' ),
				'type'        => 'text',
				'default'     => "",
			),
			'useCfm'      => array(
				'title'       => __( '이용완료일 사용 여부', 'iamport-for-woocommerce' ),
				'label'       => __( '이용완료일 사용 여부', 'iamport-for-woocommerce' ),
				'description' => __( '이용완료일 기준 정산 및 포인트 적립 가맹점에서만 사용해주세요', 'iamport-for-woocommerce' ),
				'type'        => 'checkbox',
				'default'     => "yes",
			),
			'cfmYmdt'     => array(
				'title'       => __( '이용완료일(yyyyMMdd)', 'iamport-for-woocommerce' ),
				'label'       => __( '이용완료일(yyyyMMdd)', 'iamport-for-woocommerce' ),
				'description' => __( '사용 여부를 체크한 뒤 20991231와 같은 형식으로 적어주세요.', 'iamport-for-woocommerce' ),
				'type'        => 'text',
				'default'     => "20991231",
			),
		), $this->form_fields, array(
			'use_manual_pg' => array(
				'title'       => __( 'PG설정 구매자 선택방식 사용', 'iamport-for-woocommerce' ),
				'type'        => 'checkbox',
				'description' => __( '포트원 계정에 설정된 여러 PG사 / MID를 사용자의 선택에 따라 적용하는 기능을 활성화합니다. 네이버페이(결제형) 결제수단 선택 시, 세부 결제수단 선택창이 추가로 출력됩니다.', 'iamport-for-woocommerce' ),
				'default'     => 'no',
			),
			'manual_pg_id'  => array(
				'title'       => __( 'PG설정 구매자 선택', 'iamport-for-woocommerce' ),
				'type'        => 'textarea',
				'description' => __( '"{PG사 코드}.{PG상점아이디} : 구매자에게 표시할 텍스트" 의 형식으로 여러 줄 입력가능합니다.', 'iamport-for-woocommerce' ),
			),
		) );
	}

	public static function render_edit_product_category( $tag ) {
		self::render_product_category( $tag, "edit" );
	}

	public static function render_add_product_category( $tag ) {
		self::render_product_category( $tag, "add" );
	}

	public static function save_edit_product_category( $term_id ) {
		self::save_product_category( $term_id, "edit" );
	}

	public static function save_add_product_category( $term_id ) {
		self::save_product_category( $term_id, "add" );
	}

	private static function render_product_category( $tag, $mode ) {
		if ( is_a( $tag, 'WP_Term' ) ) {
			$term_id = $tag->term_id;
		} else {
			$term_id = $tag;
		}

		$term_meta          = get_option( "taxonomy_{$term_id}" );
		$iamport_naver_ctgr = portone_get( $term_meta, "iamport_naver_ctgr" );

		ob_start();
		?>
        <select name="term_meta[iamport_naver_ctgr]" id="term_meta[iamport_naver_ctgr]">
			<?php foreach ( self::$PRODUCT_CATEGORIES as $key => $label ) : ?>
                <option <?php echo $key === $iamport_naver_ctgr ? "selected" : "" ?> value="<?php echo esc_attr( $key ) ?>"> <?php echo esc_html( $label ) ?> </option>
			<?php endforeach; ?>
        </select>
		<?php
		$select_node = ob_get_clean();
		?>

		<?php if ( $mode === "edit" ) : ?>
            <tr class="form-field">
                <th scope="row">
                    <label for="term_meta[iamport_naver_ctgr]"><?php esc_html_e( '네이버상품 카테고리', "iamport-for-woocommerce" ) ?></label>
                <td><?php echo $select_node; ?></td>
                </th>
            </tr>
		<?php elseif ( $mode === "add" ) : ?>
            <div class="form-field term-naver-product-category-wrap">
                <label for="term_meta[iamport_naver_ctgr]"><?php esc_html_e( '네이버상품 카테고리', "iamport-for-woocommerce" ) ?></label>
				<?php echo $select_node ?>
            </div>
		<?php
		endif;
	}

	private static function save_product_category( $term_id, $mode ) {
		if ( isset( $_POST[ 'term_meta' ] ) ) {
			$term_meta          = portone_get( $_POST, "term_meta" );
			$iamport_naver_ctgr = portone_get( $term_meta, "iamport_naver_ctgr" );
			$categories         = array_keys( self::$PRODUCT_CATEGORIES );

			if ( "NONE" !== $iamport_naver_ctgr && ( is_array( $categories ) && in_array( $iamport_naver_ctgr, $categories ) ) ) {
				$term_meta = array(
					"iamport_naver_ctgr" => $iamport_naver_ctgr
				);

				update_option( "taxonomy_{$term_id}", $term_meta );
			} else {
				delete_option( "taxonomy_{$term_id}" );
			}
		}
	}

	public function my_template( $located, $template_name, $args ) {
		if ( $template_name === "checkout/order-receipt.php" && isset( $args[ "order" ] ) && $args[ "order" ] instanceof WC_Order ) {
			$order = $args[ "order" ];

			if ( $order->get_payment_method() === $this->get_gateway_id() ) { //네이버페이 결제형일 때만
				if ( in_array( $order->get_status(), array( "pending", "failed" ) ) ) {
					return plugin_dir_path( __FILE__ ) . "/includes/templates/empty-order-receipt.php";
				}
			}
		}

		return $located;
	}

	public function iamport_order_detail( $order_id ) {
		ob_start();
		?>
        <h2><?php esc_html_e( '결제 상세', 'iamport-for-woocommerce' ) ?></h2>
        <table class="shop_table order_details">
            <tbody>
            <tr>
                <th><?php esc_html_e( '결제수단', 'iamport-for-woocommerce' ) ?></th>
                <td><?php esc_html_e( '네이버페이', 'iamport-for-woocommerce' ) ?></td>
            </tr>
            </tbody>
        </table>
		<?php
		ob_end_flush();
	}

	public function iamport_payment_info( $order_id ) {
		$response = parent::iamport_payment_info( $order_id );
		$order    = new WC_Order( $order_id );
		//naverProducts 생성

		$useManualPg   = filter_var( portone_get( $this->settings, 'use_manual_pg' ), FILTER_VALIDATE_BOOLEAN );
		$naverProducts = array();
		$product_items = $order->get_items(); //array of WC_Order_Item_Product
		foreach ( $product_items as $item ) {
			$cat = $this->get_naver_category( $item );

			$naverProducts[] = array(
				"categoryType" => $cat[ "type" ],
				"categoryId"   => $cat[ "id" ],
				"uid"          => $this->get_product_uid( $item ),
				"name"         => $item->get_name(),
				"count"        => $item->get_quantity(),
			);
		}

		$response[ "naverProducts" ] = $naverProducts;
		$useCfm                      = portone_get( $this->settings, 'useCfm' );
		if ( ! isset( $useCfm ) ) {
			$response[ "naverUseCfm" ] = "20991231";
		} elseif ( $useCfm == 'yes' ) {
			$response[ "naverUseCfm" ] = portone_get( $this->settings, 'cfmYmdt' );
		}
		$response[ "unblock" ] = true;

		if ( ! wp_is_mobile() ) {
			$response[ "naverPopupMode" ] = true;
		}
		if ( $this->has_subscription( $order_id ) ) { //정기결제

			//customer_uid를 생성해서 js에 전달 및 post_meta에 미리 저장해둠
			$order        = new WC_Order( $order_id );
			$customer_uid = Portone_Helper::get_customer_uid( $order );

			$order->update_meta_data( '_customer_uid_reserved', $customer_uid );//post meta에 저장(예비용 customer_uid. 아직 빌링키까지 등록안됐으므로)
			$order->save_meta_data();
			$response[ 'customer_uid' ] = $customer_uid; //js에 전달
		}

		if ( $useManualPg ) {
			$response[ 'pay_method' ] = 'naverpay';
		} else {
			$response[ 'pg' ]         = 'naverpay';
			$response[ 'pay_method' ] = 'card';
		}

		return $response;
	}

	public function payment_fields() {
		parent::payment_fields(); //description 출력

		$useManualPg = filter_var( portone_get( $this->settings, 'use_manual_pg' ), FILTER_VALIDATE_BOOLEAN );
		if ( $useManualPg ) {
			echo Portone_Helper::htmlSecondaryPaymentMethod( portone_get_not_clean( $this->settings, 'manual_pg_id' ) );
		}
	}

	protected function get_order_name( $order, $initial_payment = true ) { // "XXX 외 1건" 같이 외 1건이 붙으면 안됨
		if ( $this->has_subscription( $order->get_id() ) ) {
			return $this->get_subscription_order_name( $order, $initial_payment );
		}
		$product_items = $order->get_items(); //array of WC_Order_Item_Product

		foreach ( $product_items as $item ) {
			return $item->get_name();
		}

		return "#" . $order->get_order_number() . "번 주문";
	}

	private function get_product_uid( $item ) {
		$product_id   = $item->get_product_id();
		$variation_id = $item->get_variation_id();

		if ( $variation_id ) {
			return sprintf( "%s-%s", $product_id, $variation_id );
		}

		return strval( $product_id );
	}

	private function get_naver_category( $product ) {
		$product_id = $product->get_product_id();
		$terms      = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

		foreach ( $terms as $term_id ) {
			$term_meta          = get_option( "taxonomy_{$term_id}" );
			$iamport_naver_ctgr = empty( $term_meta[ "iamport_naver_ctgr" ] ) ? "" : $term_meta[ "iamport_naver_ctgr" ];
			$categories         = array_keys( self::$PRODUCT_CATEGORIES );

			if ( "NONE" !== $iamport_naver_ctgr && ( is_array( $categories ) && in_array( $iamport_naver_ctgr, $categories ) ) ) {
				$arr = explode( "_", $iamport_naver_ctgr, 2 ); //처음만나는 _ 로만 잘라야 함

				return array(
					"type" => $arr[ 0 ],
					"id"   => $arr[ 1 ],
				);
			}
		}

		return array(
			"type" => "ETC",
			"id"   => "ETC",
		);
	}


	public function scheduled_subscription_payment( $amount_to_charge, $renewal_order ) {
		require_once( PORTONE_PLUGIN_DIR . '/includes/lib/iamport.php' );;

		global $wpdb;

		$creds        = $this->getRestInfo( null, false ); //this->imp_rest_key, this->imp_rest_secret사용하도록
		$customer_uid = Portone_Helper::get_customer_uid( $renewal_order );
		$response     = $this->doPayment( $creds, $renewal_order, $amount_to_charge, $customer_uid, $renewal_order->suspension_count );

		if ( is_wp_error( $response ) ) {
			$old_status = $renewal_order->get_status();
			// translators: %s: error message
			$renewal_order->update_status( 'failed', sprintf( __( '정기결제승인에 실패하였습니다. (상세 : %s)', 'iamport-for-woocommerce' ), $response->get_error_message() ) );

			//fire hook
			do_action( 'iamport_order_status_changed', $old_status, $renewal_order->get_status(), $renewal_order );
		} else {
			$recur_imp_uid = 'unknown imp_uid';

			if ( $response instanceof WooIamportPayment ) {
				$recur_imp_uid = $response->imp_uid;

				$order_id = $renewal_order->id;
				$order    = wc_get_order( $order_id );

				$order->update_meta_data( '_iamport_rest_key', $creds[ 'imp_rest_key' ] );
				$order->update_meta_data( '_iamport_rest_secret', $creds[ 'imp_rest_secret' ] );
				$order->update_meta_data( '_iamport_provider', $response->pg_provider );
				$order->update_meta_data( '_iamport_paymethod', $response->pay_method );
				$order->update_meta_data( '_iamport_pg_tid', $response->pg_tid );
				$order->update_meta_data( '_iamport_receipt_url', $response->receipt_url );
				$order->update_meta_data( '_iamport_customer_uid', $customer_uid );
				$order->update_meta_data( '_iamport_recurring_md', date( 'md' ) ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
				$order->save_meta_data();
			}

			try {
				$wpdb->query( "BEGIN" );
				//lock the row
				$synced_row   = portone_get_order_row( $renewal_order->id );
				$order_status = portone_get_order_status_from_order_row( $synced_row );

				if ( ! $this->has_status( $order_status, wc_get_is_paid_statuses() ) ) {
					$renewal_order->payment_complete( $recur_imp_uid ); //imp_uid

					$wpdb->query( "COMMIT" );

					//fire hook
					do_action( 'iamport_order_status_changed', $order_status, $renewal_order->get_status(), $renewal_order );
					$renewal_order->add_order_note( sprintf( __( '정기결제 회차 과금(%1$s차결제)에 성공하였습니다. (imp_uid : %2$s)', 'iamport-for-woocommerce' ), $renewal_order->suspension_count, $recur_imp_uid ) );

					//fire hook
					do_action( 'iamport_order_status_changed', $order_status, $renewal_order->get_status(), $renewal_order );
				} else {
					$wpdb->query( "ROLLBACK" );
					//이미 이뤄진 주문

					// translators: %s: transaction id
					$renewal_order->add_order_note( sprintf( __( '이미 결제완료처리된 주문에 대해서 결제가 발생했습니다. (imp_uid : %s)', 'iamport-for-woocommerce' ), $recur_imp_uid ) );
				}

				return;
			} catch ( Exception $e ) {
				$wpdb->query( "ROLLBACK" );
			}
		}
	}

	private function doPayment( $creds, $order, $total, $customer_uid, $number_of_tried = 0 ) {
		if ( $total == 0 ) {
			return true;
		}

		require_once( PORTONE_PLUGIN_DIR . '/includes/lib/iamport.php' );

		$is_initial_payment = $number_of_tried === 0; //항상 false 임

		$order_suffix    = $is_initial_payment ? '_sf' : date( 'md' ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
		$tax_free_amount = Portone_Helper::get_tax_free_amount( $order );
		$notice_url      = Portone_Helper::get_notice_url();
		$_extra          = array(
			'naverUseCfm' => '20990101'
		);

		$iamport  = new WooIamport( $creds[ 'imp_rest_key' ], $creds[ 'imp_rest_secret' ] );
		$pay_data = array(
			'amount'       => $total,
			'merchant_uid' => portone_get_order_key( $order ) . $order_suffix,
			'customer_uid' => $customer_uid,
			'name'         => $this->get_order_name( $order, $is_initial_payment ),
			'buyer_name'   => trim( $order->get_billing_last_name() . $order->get_billing_first_name() ),
			'buyer_email'  => $order->get_billing_email(),
			'buyer_tel'    => $order->get_billing_phone(),
			'extra'        => $_extra
		);
		if ( empty( $pay_data[ "buyer_name" ] ) ) {
			$pay_data[ "buyer_name" ] = $this->get_default_user_name();
		}
		if ( wc_tax_enabled() ) {
			$pay_data[ "tax_free" ] = $tax_free_amount;
		}
		if ( $notice_url ) {
			$pay_data[ "notice_url" ] = $notice_url;
		}

		$result = $iamport->sbcr_again( $pay_data );

		$payment_data = $result->data;
		if ( $result->success ) {
			if ( $payment_data->status == 'paid' ) {
				return $payment_data;
			} else {
				if ( $is_initial_payment ) {
					// translators: 1: payment status, 2: error detail
					$message = sprintf( __( '정기결제 최초 과금(signup fee)에 실패하였습니다(%1$s). (사유 : %2$s)', 'iamport-for-woocommerce' ), $payment_data->status, $payment_data->fail_reason );
				} else {
					// translators: 1: renewal round, 2: payment status, 3: error detail
					$message = sprintf( __( '정기결제 회차 과금(%1$s차결제)에 실패하였습니다(%2$s). (사유 : %3$s)', 'iamport-for-woocommerce' ), $number_of_tried, $payment_data->status, $payment_data->fail_reason );
				}

				return new WP_Error( 'iamport_error', $message );
			}
		} else {
			if ( $is_initial_payment ) {
				// translators: 1: payment status, 2: error detail
				$message = sprintf( __( '정기결제 최초 과금(signup fee)에 실패하였습니다(%1$s). (사유 : %2$s)', 'iamport-for-woocommerce' ), $payment_data->status, $result->error[ 'message' ] );
			} else {
				// translators: 1: renewal round, 2: payment status, 3: error detail
				$message = sprintf( __( '정기결제 회차 과금(%1$s차결제)에 실패하였습니다(%2$s). (사유 : %3$s)', 'iamport-for-woocommerce' ), $number_of_tried, $payment_data->status, $result->error[ 'message' ] );
			}

			return new WP_Error( 'iamport_error', $message );
		}
	}

	public function is_paid_confirmed( $order, $payment_data ) {
		add_action( 'iamport_post_order_completed', array( $this, 'update_customer_uid' ), 10, 2 ); //불필요하게 hook이 많이 걸리지 않도록(naver-gateway객체를 여러 군데에서 생성한다.)

		return parent::is_paid_confirmed( $order, $payment_data );
	}

	public function update_customer_uid( $order, $payment_data ) {
		$customer_uid = $order->get_meta( '_customer_uid_reserved' );
		$order->update_meta_data( '_customer_uid', $customer_uid );//성공한 customer_uid저장
		$order->save_meta_data();
	}

	protected function get_subscription_order_name( $order, $initial_payment = true ) {
		if ( $initial_payment ) {
			$order_name = "#" . $order->get_order_number() . "번 주문 정기결제(최초과금)";
		} else {
			// translators: %s: renewal round
			$order_name = "#" . $order->get_order_number() . sprintf( "번 주문 정기결제(%s회차)", $order->suspension_count );
		}

		$order_name = apply_filters( 'iamport_recurring_order_name', $order_name, $order, $initial_payment );

		return $order_name;
	}

	private function has_subscription( $order_id ) {
		return function_exists( 'wcs_order_contains_subscription' ) && ( wcs_order_contains_subscription( $order_id ) || wcs_is_subscription( $order_id ) || wcs_order_contains_renewal( $order_id ) );
	}

	private static function is_product_purchasable( $product, $disabled_categories ) {
		$is_disabled = $disabled_categories === 'all' || Portone_Helper::is_product_in_categories( $product->get_id(), $disabled_categories );

		return ! $is_disabled;
	}

}
