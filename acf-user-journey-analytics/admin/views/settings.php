<?php
/**
 * 설정 페이지 뷰
 *
 * @package ACF_User_Journey_Analytics
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$roles = wp_roles()->get_names();
?>

<div class="wrap acf-uja-wrap">
    <h1>
        <span class="dashicons dashicons-admin-settings"></span>
        User Journey Analytics 설정
    </h1>

    <?php if ( isset( $_POST['acf_uja_save_settings'] ) ) : ?>
    <div class="notice notice-success is-dismissible">
        <p>설정이 저장되었습니다.</p>
    </div>
    <?php endif; ?>

    <form method="post" action="">
        <?php wp_nonce_field( 'acf_uja_settings' ); ?>

        <div class="acf-uja-settings-section">
            <h2>기본 설정</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">추적 활성화</th>
                    <td>
                        <label>
                            <input type="checkbox" name="enabled" value="1" <?php checked( ! empty( $settings['enabled'] ) ); ?>>
                            방문자 추적 활성화
                        </label>
                        <p class="description">이 옵션을 끄면 방문자 데이터가 수집되지 않습니다.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">로그인 사용자 추적</th>
                    <td>
                        <label>
                            <input type="checkbox" name="track_logged_in" value="1" <?php checked( ! empty( $settings['track_logged_in'] ) ); ?>>
                            로그인한 사용자도 추적
                        </label>
                        <p class="description">체크하면 로그인한 사용자의 방문도 추적합니다.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">추적 제외 역할</th>
                    <td>
                        <?php foreach ( $roles as $role_key => $role_name ) : ?>
                        <label style="display: block; margin-bottom: 5px;">
                            <input type="checkbox" name="exclude_roles[]" value="<?php echo esc_attr( $role_key ); ?>"
                                <?php checked( in_array( $role_key, $settings['exclude_roles'] ?? array() ) ); ?>>
                            <?php echo esc_html( $role_name ); ?>
                        </label>
                        <?php endforeach; ?>
                        <p class="description">선택한 역할의 사용자는 추적에서 제외됩니다.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">데이터 보존 기간</th>
                    <td>
                        <input type="number" name="data_retention_days" value="<?php echo esc_attr( $settings['data_retention_days'] ?? 90 ); ?>" min="7" max="365" style="width: 80px;">
                        일
                        <p class="description">이 기간이 지난 데이터는 자동으로 삭제됩니다. (7~365일)</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="acf-uja-settings-section">
            <h2>연동 플러그인</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Nudge Flow</th>
                    <td>
                        <?php if ( is_plugin_active( 'acf-nudge-flow/acf-nudge-flow.php' ) ) : ?>
                            <span class="dashicons dashicons-yes-alt" style="color: #22c55e;"></span> 연동됨
                            <p class="description">User Journey 데이터가 Nudge Flow와 연동됩니다.</p>
                        <?php else : ?>
                            <span class="dashicons dashicons-no-alt" style="color: #9ca3af;"></span> 미설치
                            <p class="description">
                                <a href="https://wordpress.org/plugins/acf-nudge-flow/" target="_blank">Nudge Flow</a>를 설치하면
                                트래픽 분석 데이터를 기반으로 맞춤형 마케팅 자동화가 가능합니다.
                            </p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">1-Click SEO</th>
                    <td>
                        <?php if ( is_plugin_active( 'oneclick-seo-pro/oneclick-seo-pro.php' ) ) : ?>
                            <span class="dashicons dashicons-yes-alt" style="color: #22c55e;"></span> 연동됨
                            <p class="description">SEO 성과 데이터가 User Journey와 연동됩니다.</p>
                        <?php else : ?>
                            <span class="dashicons dashicons-no-alt" style="color: #9ca3af;"></span> 미설치
                            <p class="description">
                                <a href="https://wordpress.org/plugins/oneclick-seo-pro/" target="_blank">1-Click SEO</a>를 설치하면
                                오가닉 트래픽 분석과 SEO 최적화가 가능합니다.
                            </p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">WooCommerce</th>
                    <td>
                        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                            <span class="dashicons dashicons-yes-alt" style="color: #22c55e;"></span> 연동됨
                            <p class="description">WooCommerce 주문이 전환으로 자동 추적됩니다.</p>
                        <?php else : ?>
                            <span class="dashicons dashicons-no-alt" style="color: #9ca3af;"></span> 미설치
                            <p class="description">WooCommerce를 설치하면 주문 전환 추적이 가능합니다.</p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <p class="submit">
            <input type="submit" name="acf_uja_save_settings" class="button button-primary" value="설정 저장">
        </p>
    </form>

    <!-- 데이터 관리 -->
    <div class="acf-uja-settings-section">
        <h2>데이터 관리</h2>
        <table class="form-table">
            <tr>
                <th scope="row">CSV 내보내기</th>
                <td>
                    <a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=acf_uja_export_csv&nonce=' . wp_create_nonce( 'acf_uja_export' ) ) ); ?>" class="button">
                        지난 7일 데이터 내보내기
                    </a>
                    <p class="description">CSV 형식으로 트래픽 데이터를 내보냅니다.</p>
                </td>
            </tr>
        </table>
    </div>
</div>

<style>
.acf-uja-settings-section { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0; }
.acf-uja-settings-section h2 { margin-top: 0; padding-bottom: 15px; border-bottom: 1px solid #e2e8f0; }
</style>
