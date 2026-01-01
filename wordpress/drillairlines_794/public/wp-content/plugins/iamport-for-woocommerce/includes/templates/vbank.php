<?php
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

$vbank_refunded_code   = $order->get_meta( '_portone_vbank_refunded_code' );
$vbank_refunded_num    = $order->get_meta( '_portone_vbank_refunded_num' );
$vbank_refunded_name   = $order->get_meta( '_portone_vbank_refunded_name' );
$vbank_refunded_reason = $order->get_meta( '_portone_vbank_refunded_reason' );
$vbank_refunded        = $order->get_meta( '_portone_vbank_refunded' );
$disabled = 'yes' != $vbank_refunded ? '' : 'disabled';

$vbank_lists = WC_Gateway_Portone_Vbank::get_vbank_code( $order->get_meta( '_iamport_provider' ) );

?>

<script>
    jQuery( document ).ready( function ( $ ) {
        $( '#vbank_bankcode' ).select2();
    } );

</script>
<div class="iamport_payment_info">
    <h4><?php esc_html_e( '가상계좌 환불처리는 전액환불만 가능하며 PG사 계약에 따라 환불 수수료가 부과됩니다.', 'iamport-for-woocommerce' ); ?></h4>
    <p><?php esc_html_e( '환불 은행(코드)', 'iamport-for-woocommerce' ); ?></p>
    <p>
        <select id="vbank_refund_bank_code" class="wc-enhanced-select enhanced" title="<?php esc_attr_e( '환불처리할 은행을 선택해주세요.', 'iamport-for-woocommerce' ); ?>" <?php echo $disabled; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
            <option value=""><?php esc_html_e( '환불은행을 선택하세요.', 'iamport-for-woocommerce' ); ?></option>
			<?php foreach ( $vbank_lists as $code => $name ) : ?>
                <option value="<?php echo esc_attr( $code ); ?>" <?php echo $code == $vbank_refunded_code ? 'selected' : ''; ?>><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>
        </select>
    </p>
    <p>환불 계좌번호</p>
    <p>
        <input type="text" id="vbank_refund_acc_num" placeholder="번호(숫자)만 입력하세요." value="<?php echo esc_attr( $vbank_refunded_num ); ?>" <?php echo $disabled; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
    </p>
    <p>환불 계좌주명</p>
    <p>
        <input type="text" id="vbank_refund_acc_name" placeholder="환불 계좌주명" value="<?php echo esc_attr( $vbank_refunded_name ); ?>" <?php echo $disabled; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
    </p>
    <p>취소사유</p>
    <p>
        <input type="text" id="vbank_refund_reason" placeholder="취소 사유" value="<?php echo esc_attr( $vbank_refunded_reason ); ?>" <?php echo $disabled; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
    </p>
</div>

<div class="iamport_button_wrapper">
    <input type="button" class="button iamport_action_button tips" id="iamport-vbank-refund-request" value="<?php esc_attr_e( '환불하기', 'iamport-for-woocommerce' ); ?>" data-tip="가상계좌 환불 처리를 수행합니다." <?php echo $disabled; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
</div>
