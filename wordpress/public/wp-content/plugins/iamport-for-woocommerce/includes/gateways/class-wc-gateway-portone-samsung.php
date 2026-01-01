<?php
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class WC_Gateway_Portone_Samsung extends PORTONE_Payment_Gateway {

	const GATEWAY_ID = 'iamport_samsung';

	public function __construct() {
		parent::__construct();

		//settings
		$this->method_title       = __( '포트원(삼성페이)', 'iamport-for-woocommerce' );
		$this->method_description = __( '=> 포트원 서비스를 이용해 쉽게 삼성페이를 연동할 수 있습니다(KG이니시스 계약 필요).<br>=> 궁금한 사항이 있다면 헬프센터를 방문해 주세요.<br><a href="https://help.portone.io/" target="_blank">헬프센터 방문</a>', 'iamport-for-woocommerce' );
		$this->has_fields         = true;
		$this->supports           = array( 'products', 'refunds' );

		$this->title       = portone_get( $this->settings, 'title' );
		$this->description = portone_get_not_clean( $this->settings, 'description' );
		$this->pg_id       = portone_get( $this->settings, 'pg_id' );
	}

	protected function get_gateway_id() {
		return self::GATEWAY_ID;
	}

	public function init_form_fields() {
		parent::init_form_fields();

		$this->form_fields = array_merge( array(
			'enabled'     => array(
				'title'   => __( 'Enable/Disable', 'iamport-for-woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( '포트원(삼성페이) 결제 사용. (삼성페이를 사용하시려면, KG이니시스 가입이 필요합니다.)', 'iamport-for-woocommerce' ),
				'default' => 'yes'
			),
			'title'       => array(
				'title'       => __( 'Title', 'iamport-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( '구매자에게 표시될 구매수단명', 'iamport-for-woocommerce' ),
				'default'     => __( '삼성페이 결제', 'iamport-for-woocommerce' ),
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => __( 'Customer Message', 'iamport-for-woocommerce' ),
				'type'        => 'textarea',
				'description' => __( '구매자에게 결제수단에 대한 상세설명을 합니다.', 'iamport-for-woocommerce' ),
				'default'     => __( '주문확정 버튼을 클릭하시면 삼성페이 결제창이 나타나 결제를 진행하실 수 있습니다.', 'iamport-for-woocommerce' )
			),
			'pg_id'       => array(
				'title'       => __( 'PG상점아이디', 'iamport-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( '동일한 PG사에서 여러 개의 상점아이디(MID)를 사용하는 경우 원하시는 PG상점아이디(MID)를 지정하여 결제할 수 있습니다.', 'iamport-for-woocommerce' ),
			),
		), $this->form_fields );
	}

	public function iamport_order_detail( $order_id ) {
		global $woocommerce;

		$order = new WC_Order( $order_id );

		$paymethod   = $order->get_meta( '_iamport_paymethod' );
		$receipt_url = $order->get_meta( '_iamport_receipt_url' );
		$tid         = $order->get_transaction_id();

		ob_start();
		?>
        <h2><?php esc_html_e( '결제 상세', 'iamport-for-woocommerce' ) ?></h2>
        <table class="shop_table order_details">
            <tbody>
            <tr>
                <th><?php esc_html_e( '결제수단', 'iamport-for-woocommerce' ) ?></th>
                <td><?php esc_html_e( '삼성페이', 'iamport-for-woocommerce' ) ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e( '매출전표', 'iamport-for-woocommerce' ) ?></th>
                <td><a target="_blank" href="<?php echo esc_url( $receipt_url ) ?>"><?php /* translators: %s: transaction id */ echo esc_html( sprintf( __( '영수증보기(%s)', 'iamport-for-woocommerce' ), $tid ) ) ?></a></td>
            </tr>
            </tbody>
        </table>
		<?php
		ob_end_flush();
	}

	public function iamport_payment_info( $order_id ) {
		$response                 = parent::iamport_payment_info( $order_id );
		$response[ 'pg' ]         = 'html5_inicis';
		$response[ 'pay_method' ] = 'samsung';

		if ( ! empty( $this->pg_id ) ) {
			$response[ 'pg' ] = sprintf( "html5_inicis.%s", $this->pg_id );
		}

		return $response;
	}

}