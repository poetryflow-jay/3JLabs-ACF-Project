<?php
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class WC_Gateway_Portone_Eximbay extends PORTONE_Payment_Gateway {

	const GATEWAY_ID = 'iamport_eximbay';

	public function __construct() {
		parent::__construct();

		//settings
		$this->method_title       = __( '포트원(Eximbay)', 'iamport-for-woocommerce' );
		$this->method_description = __( '=> 포트원 서비스를 이용해 결제모듈을 연동할 수 있습니다.<br>=> 궁금한 사항이 있다면 헬프센터를 방문해 주세요.<br><a href="https://help.portone.io/" target="_blank">헬프센터 방문</a>', 'iamport-for-woocommerce' );
		$this->has_fields         = true;
		$this->supports           = array( 'products', 'refunds', 'default_credit_card_form' );

		$this->title       = portone_get( $this->settings, 'title' );
		$this->description = portone_get_not_clean( $this->settings, 'description' );
		$this->pg_id       = portone_get( $this->settings, 'pg_id' );

		//actions
		// add_action( 'woocommerce_thankyou_'.$this->id, array( $this, 'iamport_order_detail' ) ); woocommerce_order_details_after_order_table 로 대체. 중복으로 나오고 있음
		add_filter( 'woocommerce_credit_card_form_fields', array( $this, 'iamport_credit_card_form_fields' ), 10, 2 );
	}

	protected function get_gateway_id() {
		return self::GATEWAY_ID;
	}

	public function init_form_fields() {
		parent::init_form_fields();

		$this->form_fields = array_merge( array(
			'enabled'               => array(
				'title'   => __( 'Enable/Disable', 'iamport-for-woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( '포트원(Eximbay) 결제 사용. (Eximbay를 사용하시려면, <a href="https://admin.portone.io/settings" target="_blank">포트원 관리자페이지의 PG설정화면</a>에서 "추가PG사"로 Eximbay를 추가 후 사용해주세요.)', 'iamport-for-woocommerce' ),
				'default' => 'yes'
			),
			'title'                 => array(
				'title'       => __( 'Title', 'iamport-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( '구매자에게 표시될 구매수단명', 'iamport-for-woocommerce' ),
				'default'     => __( 'Eximbay 결제', 'iamport-for-woocommerce' ),
				'desc_tip'    => true,
			),
			'description'           => array(
				'title'       => __( 'Customer Message', 'iamport-for-woocommerce' ),
				'type'        => 'textarea',
				'description' => __( '구매자에게 결제수단에 대한 상세설명을 합니다.', 'iamport-for-woocommerce' ),
				'default'     => __( '주문확정 버튼을 클릭하시면 Eximbay 결제창이 나타나 결제를 진행하실 수 있습니다.', 'iamport-for-woocommerce' )
			),
			'detail_payment_method' => array(
				'title'       => __( '세부 결제수단', 'iamport-for-woocommerce' ),
				'type'        => 'multiselect',
				'description' => __( '엑심베이 결제수단으로 사용할 세부 결제수단들을 선택해주세요.', 'iamport-for-woocommerce' ),
				'default'     => array( 'all' ),
				'options'     => array(
					'all'      => __( '[모든 결제수단]', 'iamport-for-woocommerce' ),
					'card'     => __( 'CreditCard', 'iamport-for-woocommerce' ),
					'unionpay' => __( 'UnionPay', 'iamport-for-woocommerce' ),
					'alipay'   => __( 'Alipay', 'iamport-for-woocommerce' ),
					'wechat'   => __( 'WechatPay', 'iamport-for-woocommerce' ),
					'molpay'   => __( 'Molpay', 'iamport-for-woocommerce' ),
					'econtext' => __( 'EContext(일본편의점결제)', 'iamport-for-woocommerce' ),
				),
			),
			'pg_id'                 => array(
				'title'       => __( 'PG상점아이디', 'iamport-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( '동일한 PG사에서 여러 개의 상점아이디(MID)를 사용하는 경우 원하시는 PG상점아이디(MID)를 지정하여 결제할 수 있습니다.', 'iamport-for-woocommerce' ),
			),
		), $this->form_fields );
	}

	public function iamport_get_detail_payment_method_title( $method ) {
		if ( 'card' == $method ) {
			return __( 'CreditCard', 'iamport-for-woocommerce' );
		} elseif ( 'unionpay' == $method ) {
			return __( 'UnionPay', 'iamport-for-woocommerce' );
		} elseif ( 'alipay' == $method ) {
			return __( 'Alipay', 'iamport-for-woocommerce' );
		} elseif ( 'wechat' == $method ) {
			return __( 'WechatPay', 'iamport-for-woocommerce' );
		} elseif ( 'molpay' == $method ) {
			return __( 'Molpay', 'iamport-for-woocommerce' );
		} elseif ( 'econtext' == $method ) {
			return __( 'EContext(일본편의점결제)', 'iamport-for-woocommerce' );
		}

		return '';
	}

	public function iamport_credit_card_form_fields( $default_fields, $id ) {
		if ( $id !== $this->id ) {
			return $default_fields;
		}

		$eximbay_option = get_option( 'woocommerce_iamport_eximbay_settings' );

		if ( ! empty( $eximbay_option ) && ! empty( $eximbay_option[ 'detail_payment_method' ] ) ) {
			$pay_fields = '';
			foreach ( $eximbay_option[ 'detail_payment_method' ] as $method ) {
				if ( 'all' == $method ) {
					break;
				} else {
					$pay_fields .= '<option value="' . esc_attr( $method ) . '">' . self::iamport_get_detail_payment_method_title( $method ) . '</option>';
				}
			}

			if ( empty( $pay_fields ) ) {
				$pay_fields = '<option value="card">' . __( 'CreditCard', 'iamport-for-woocommerce' ) . '</option>
                          <option value="unionpay">' . __( 'UnionPay', 'iamport-for-woocommerce' ) . '</option>
                          <option value="alipay">' . __( 'Alipay', 'iamport-for-woocommerce' ) . '</option>
                          <option value="wechat">' . __( 'WechatPay', 'iamport-for-woocommerce' ) . '</option>
                          <option value="molpay">' . __( 'Molpay', 'iamport-for-woocommerce' ) . '</option>
                          <option value="econtext">' . __( 'EContext(일본편의점결제)', 'iamport-for-woocommerce' ) . '</option>';
			}
		}

		$pay_select = '<p class="form-row">
                      <select id="iamport_eximbay-pay-method" class="input-text wc-credit-card-form-pay-method" autocomplete="off" name="iamport_eximbay-pay-method">
                          ' . $pay_fields . '
                      </select> </p>';

		$iamport_fields = array(
			'pay-method-field' => $pay_select
		);

		return $iamport_fields;
	}

	public function iamport_order_detail( $order_id ) {
		global $woocommerce;

		$order = new WC_Order( $order_id );

		$paymethod   = $order->get_meta( '_iamport_paymethod' );
		$receipt_url = $order->get_meta( '_iamport_receipt_url' );
//        $vbank_name  = $order->get_meta( '_iamport_vbank_name' );
//        $vbank_num   = $order->get_meta( '_iamport_vbank_num' );
//        $vbank_date  = $order->get_meta( '_iamport_vbank_date' );
		$tid = $order->get_transaction_id();

		ob_start();
		?>
        <h2><?php esc_html_e( '결제 상세', 'iamport-for-woocommerce' ) ?></h2>
        <table class="shop_table order_details">
            <tbody>
            <tr>
                <th><?php esc_html_e( '결제수단', 'iamport-for-woocommerce' ) ?></th>
                <td><?php esc_html_e( 'Eximbay', 'iamport-for-woocommerce' ) ?></td>
            </tr>
			<?php
			if ( $paymethod == 'vbank' ) {
				?>
                <tr>
                    <th><?php esc_html_e( 'QR link', 'iamport-for-woocommerce' ) ?></th>
                    <td><a href="<?php echo esc_url( $receipt_url ) ?>" target="_blank"><?php echo esc_html( $receipt_url ) ?></a</td>
                </tr>
				<?php
			}
			?>
            </tbody>
        </table>
		<?php
		ob_end_flush();
	}

	public function iamport_payment_info( $order_id ) {
		$response               = parent::iamport_payment_info( $order_id );
		$response[ 'pg' ]       = 'eximbay';
		$response[ 'currency' ] = get_woocommerce_currency();

		if ( ! empty( $this->pg_id ) ) {
			$response[ 'pg' ] = sprintf( "eximbay.%s", $this->pg_id );
		}

		return $response;
	}

}