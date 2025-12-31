<?php
/**
 * 설정 페이지 템플릿
 * 
 * @package JJ_LicenseManagerTemplates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$api_url = get_option( 'jj_license_api_url', home_url( '/wp-json/jj-license/v1' ) );
$send_email = get_option( 'jj_license_send_email', 1 );

// 개발자 서버 URL (기본값: https://j-j-labs.com/)
$default_server_url = 'https://j-j-labs.com/';
$developer_server_url = get_option( 'jj_license_manager_server_url', $default_server_url );

// API 키 가져오기
$api_key = get_option( 'jj_license_api_key', '' );
if ( empty( $api_key ) ) {
    $api_key = wp_generate_password( 32, false );
    update_option( 'jj_license_api_key', $api_key );
}
?>

<div class="wrap">
    <h1><?php esc_html_e( 'JJ License Manager 설정', 'jj-license-manager' ); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field( 'jj_license_settings' ); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="developer_server_url"><?php esc_html_e( '개발자 서버 URL', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <input type="url" id="developer_server_url" name="developer_server_url" value="<?php echo esc_attr( $developer_server_url ); ?>" class="regular-text" placeholder="<?php echo esc_attr( $default_server_url ); ?>">
                    <p class="description">
                        <?php esc_html_e( '플러그인 판매 웹사이트(라이센스 서버)의 기본 URL입니다. 이 URL 뒤에 자동으로 REST API 경로가 추가됩니다.', 'jj-license-manager' ); ?>
                        <br>
                        <strong><?php esc_html_e( '예:', 'jj-license-manager' ); ?></strong> 
                        <code><?php echo esc_html( trailingslashit( $developer_server_url ) . 'wp-json/jj-license/v1' ); ?></code>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="api_url"><?php esc_html_e( 'API URL', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <input type="url" id="api_url" name="api_url" value="<?php echo esc_attr( $api_url ); ?>" class="regular-text" readonly>
                    <p class="description">
                        <?php esc_html_e( '라이센스 검증 API 엔드포인트 URL입니다. 플러그인에서 이 URL을 사용하여 라이센스를 검증합니다.', 'jj-license-manager' ); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="send_email"><?php esc_html_e( '이메일 발송', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="send_email" name="send_email" value="1" <?php checked( $send_email, 1 ); ?>>
                        <?php esc_html_e( '라이센스 생성 시 사용자에게 이메일 발송', 'jj-license-manager' ); ?>
                    </label>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="security_alerts"><?php esc_html_e( '보안 경고 이메일', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="security_alerts" name="security_alerts" value="1" <?php checked( get_option( 'jj_license_manager_security_alerts', false ), 1 ); ?>>
                        <?php esc_html_e( '코드 무결성 위반 감지 시 관리자에게 이메일 발송', 'jj-license-manager' ); ?>
                    </label>
                    <p class="description">
                        <?php esc_html_e( '플러그인 파일 수정이나 라이센스 우회 시도가 감지되면 이메일로 알림을 받습니다.', 'jj-license-manager' ); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="api_key"><?php esc_html_e( 'API 키', 'jj-license-manager' ); ?></label>
                </th>
                <td>
                    <input type="text" id="api_key" name="api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" readonly>
                    <button type="button" class="button copy-license-key" data-license-key="<?php echo esc_attr( $api_key ); ?>">
                        <?php esc_html_e( '복사', 'jj-license-manager' ); ?>
                    </button>
                    <label style="margin-left: 10px;">
                        <input type="checkbox" name="regenerate_api_key" value="1">
                        <?php esc_html_e( '새 API 키 생성', 'jj-license-manager' ); ?>
                    </label>
                    <p class="description">
                        <?php esc_html_e( 'API 요청 시 X-API-Key 헤더에 이 키를 포함하세요. 보안을 위해 정기적으로 변경하는 것을 권장합니다.', 'jj-license-manager' ); ?>
                    </p>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="jj_license_settings_submit" class="button button-primary" value="<?php esc_attr_e( '설정 저장', 'jj-license-manager' ); ?>">
        </p>
    </form>
    
    <hr>
    
    <h2><?php esc_html_e( 'WooCommerce 상품 설정', 'jj-license-manager' ); ?></h2>
    <p>
        <?php esc_html_e( 'WooCommerce 상품에서 라이센스를 자동으로 생성하려면 다음 메타 필드를 설정하세요:', 'jj-license-manager' ); ?>
    </p>
    <ul>
        <li><strong>_jj_license_type</strong>: 라이센스 타입 (FREE, BASIC, PREM, UNLIM)</li>
        <li><strong>_jj_subscription_period</strong>: 구독 기간 단위 (day, week, month, year)</li>
        <li><strong>_jj_subscription_length</strong>: 구독 기간 길이 (숫자 또는 'lifetime' 평생 라이센스)</li>
    </ul>
    
    <h2><?php esc_html_e( 'API 엔드포인트', 'jj-license-manager' ); ?></h2>
    <p><?php esc_html_e( '다음 REST API 엔드포인트가 제공됩니다:', 'jj-license-manager' ); ?></p>
    <ul>
        <li><code>POST /wp-json/jj-license/v1/verify</code> - 라이센스 검증</li>
        <li><code>POST /wp-json/jj-license/v1/activate</code> - 라이센스 활성화</li>
        <li><code>POST /wp-json/jj-license/v1/deactivate</code> - 라이센스 비활성화</li>
        <li><code>POST /wp-json/jj-license/v1/check-update</code> - 플러그인 업데이트 확인</li>
        <li><code>GET /wp-json/jj-license/v1/download</code> - 플러그인 파일 다운로드</li>
        <li><code>POST /wp-json/jj-license/v1/security-alert</code> - 보안 경고 수신</li>
        <li><code>POST /wp-json/jj-license/v1/verify-unlock</code> - 잠금 해제 코드 검증</li>
    </ul>
    
    <h2><?php esc_html_e( '보안 기능', 'jj-license-manager' ); ?></h2>
    <p><?php esc_html_e( '이 플러그인은 다음과 같은 보안 기능을 제공합니다:', 'jj-license-manager' ); ?></p>
    <ul>
        <li><?php esc_html_e( '코드 무결성 검사: 플러그인 파일 수정 감지', 'jj-license-manager' ); ?></li>
        <li><?php esc_html_e( '라이센스 타입 검증: 서버 기반 다중 검증', 'jj-license-manager' ); ?></li>
        <li><?php esc_html_e( '가짜 라이센스 키 감지: 형식 및 서버 검증', 'jj-license-manager' ); ?></li>
        <li><?php esc_html_e( '자동 알림: 보안 위반 시 개발자에게 즉시 알림', 'jj-license-manager' ); ?></li>
        <li><?php esc_html_e( '기능 잠금: 위반 감지 시 플러그인 기능 자동 잠금', 'jj-license-manager' ); ?></li>
    </ul>
</div>

