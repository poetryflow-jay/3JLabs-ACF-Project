<?php
/**
 * 라이센스 갱신 폼 템플릿
 * 
 * @package JJ_LicenseManagerTemplates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;

$license_id = isset( $_GET['license_id'] ) ? intval( $_GET['license_id'] ) : 0;
$table_licenses = JJ_License_Database::get_table_name( 'licenses' );

$license = $wpdb->get_row( $wpdb->prepare(
    "SELECT * FROM {$table_licenses} WHERE id = %d",
    $license_id
), ARRAY_A );

if ( ! $license ) {
    wp_die( __( '라이센스를 찾을 수 없습니다.', 'jj-license-manager' ) );
}
?>

<div class="wrap">
    <h1><?php esc_html_e( '라이센스 갱신', 'jj-license-manager' ); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field( 'jj_renew_license' ); ?>
        <input type="hidden" name="license_id" value="<?php echo esc_attr( $license_id ); ?>">
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e( '라이센스 키', 'jj-license-manager' ); ?></th>
                <td><code><?php echo esc_html( $license['license_key'] ); ?></code></td>
            </tr>
            
            <tr>
                <th scope="row"><?php esc_html_e( '현재 만료일', 'jj-license-manager' ); ?></th>
                <td>
                    <?php if ( $license['expires_at'] ) : ?>
                        <?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $license['expires_at'] ) ) ); ?>
                    <?php else : ?>
                        <span style="color: #2271b1;"><?php esc_html_e( '평생', 'jj-license-manager' ); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="subscription_period"><?php esc_html_e( '구독 기간 단위', 'jj-license-manager' ); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <select id="subscription_period" name="subscription_period" required>
                        <option value=""><?php esc_html_e( '선택하세요', 'jj-license-manager' ); ?></option>
                        <option value="day"><?php esc_html_e( '일', 'jj-license-manager' ); ?></option>
                        <option value="week"><?php esc_html_e( '주', 'jj-license-manager' ); ?></option>
                        <option value="month"><?php esc_html_e( '월', 'jj-license-manager' ); ?></option>
                        <option value="year" selected><?php esc_html_e( '년', 'jj-license-manager' ); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="subscription_length"><?php esc_html_e( '구독 기간 길이', 'jj-license-manager' ); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <input type="text" id="subscription_length" name="subscription_length" class="regular-text" placeholder="<?php esc_attr_e( '예: 1, 12, 또는 lifetime', 'jj-license-manager' ); ?>" required>
                    <p class="description"><?php esc_html_e( '갱신할 기간의 길이를 입력하세요. 평생 라이센스로 변경하려면 "lifetime"을 입력하세요.', 'jj-license-manager' ); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="order_id"><?php esc_html_e( '주문 ID', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <input type="number" id="order_id" name="order_id" class="regular-text">
                    <p class="description"><?php esc_html_e( '갱신과 관련된 주문 ID를 입력하세요 (선택사항).', 'jj-license-manager' ); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="send_email"><?php esc_html_e( '이메일 발송', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="send_email" name="send_email" value="1" checked>
                        <?php esc_html_e( '라이센스 갱신 시 사용자에게 이메일 발송', 'jj-license-manager' ); ?>
                    </label>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="jj_renew_license" class="button button-primary" value="<?php esc_attr_e( '라이센스 갱신', 'jj-license-manager' ); ?>">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=jj-license-manager' ) ); ?>" class="button"><?php esc_html_e( '취소', 'jj-license-manager' ); ?></a>
        </p>
    </form>
</div>

