<?php
/**
 * 라이센스 생성 폼 템플릿
 * 
 * @package JJ_LicenseManagerTemplates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 사용자 목록 가져오기
$users = get_users( array( 'number' => 100, 'orderby' => 'display_name' ) );

// 상품 목록 가져오기
$products = wc_get_products( array( 'limit' => 100, 'orderby' => 'title' ) );
?>

<div class="wrap">
    <h1><?php esc_html_e( '라이센스 생성', 'jj-license-manager' ); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field( 'jj_create_license' ); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="license_type"><?php esc_html_e( '라이센스 타입', 'jj-license-manager' ); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <select id="license_type" name="license_type" required>
                        <option value=""><?php esc_html_e( '선택하세요', 'jj-license-manager' ); ?></option>
                        <option value="FREE"><?php esc_html_e( 'Free', 'jj-license-manager' ); ?></option>
                        <option value="BASIC"><?php esc_html_e( 'Basic', 'jj-license-manager' ); ?></option>
                        <option value="PREM"><?php esc_html_e( 'Premium', 'jj-license-manager' ); ?></option>
                        <option value="UNLIM"><?php esc_html_e( 'Unlimited', 'jj-license-manager' ); ?></option>
                    </select>
                    <p class="description"><?php esc_html_e( '생성할 라이센스 타입을 선택하세요.', 'jj-license-manager' ); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="user_id"><?php esc_html_e( '사용자', 'jj-license-manager' ); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <select id="user_id" name="user_id" required>
                        <option value=""><?php esc_html_e( '선택하세요', 'jj-license-manager' ); ?></option>
                        <?php foreach ( $users as $user ) : ?>
                            <option value="<?php echo esc_attr( $user->ID ); ?>" <?php selected( $preselected_user_id, $user->ID ); ?>>
                                <?php echo esc_html( $user->display_name . ' (' . $user->user_email . ')' ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php esc_html_e( '라이센스를 발급할 사용자를 선택하세요.', 'jj-license-manager' ); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="product_id"><?php esc_html_e( '상품', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <select id="product_id" name="product_id">
                        <option value=""><?php esc_html_e( '선택 안 함', 'jj-license-manager' ); ?></option>
                        <?php if ( ! empty( $products ) ) : ?>
                            <?php foreach ( $products as $product ) : ?>
                                <option value="<?php echo esc_attr( $product->get_id() ); ?>">
                                    <?php echo esc_html( $product->get_name() ); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <p class="description"><?php esc_html_e( '연관된 상품을 선택하세요 (선택사항).', 'jj-license-manager' ); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="order_id"><?php esc_html_e( '주문 ID', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <input type="number" id="order_id" name="order_id" class="regular-text">
                    <p class="description"><?php esc_html_e( '연관된 주문 ID를 입력하세요 (선택사항).', 'jj-license-manager' ); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="subscription_period"><?php esc_html_e( '구독 기간 단위', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <select id="subscription_period" name="subscription_period">
                        <option value=""><?php esc_html_e( '선택 안 함 (평생)', 'jj-license-manager' ); ?></option>
                        <option value="day"><?php esc_html_e( '일', 'jj-license-manager' ); ?></option>
                        <option value="week"><?php esc_html_e( '주', 'jj-license-manager' ); ?></option>
                        <option value="month"><?php esc_html_e( '월', 'jj-license-manager' ); ?></option>
                        <option value="year"><?php esc_html_e( '년', 'jj-license-manager' ); ?></option>
                    </select>
                    <p class="description"><?php esc_html_e( '라이센스 유효 기간의 단위를 선택하세요.', 'jj-license-manager' ); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="subscription_length"><?php esc_html_e( '구독 기간 길이', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <input type="text" id="subscription_length" name="subscription_length" class="regular-text" placeholder="<?php esc_attr_e( '예: 1, 12, 또는 lifetime', 'jj-license-manager' ); ?>">
                    <p class="description"><?php esc_html_e( '구독 기간의 길이를 입력하세요. 평생 라이센스는 "lifetime"을 입력하세요.', 'jj-license-manager' ); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="purchase_date"><?php esc_html_e( '구매일', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <input type="datetime-local" id="purchase_date" name="purchase_date" class="regular-text" value="<?php echo esc_attr( current_time( 'Y-m-d\TH:i' ) ); ?>">
                    <p class="description"><?php esc_html_e( '라이센스 구매일을 입력하세요.', 'jj-license-manager' ); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="status"><?php esc_html_e( '상태', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <select id="status" name="status">
                        <option value="active"><?php esc_html_e( '활성', 'jj-license-manager' ); ?></option>
                        <option value="inactive"><?php esc_html_e( '비활성', 'jj-license-manager' ); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="send_email"><?php esc_html_e( '이메일 발송', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="send_email" name="send_email" value="1" checked>
                        <?php esc_html_e( '라이센스 생성 시 사용자에게 이메일 발송', 'jj-license-manager' ); ?>
                    </label>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="jj_create_license" class="button button-primary" value="<?php esc_attr_e( '라이센스 생성', 'jj-license-manager' ); ?>">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=jj-license-manager' ) ); ?>" class="button"><?php esc_html_e( '취소', 'jj-license-manager' ); ?></a>
        </p>
    </form>
</div>

