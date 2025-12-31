<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="jj-admin-center-tab-content" data-tab="updates">
    <?php
    // Partner/Master (내부/파트너용) 판별: 업그레이드/제한 UI 금지, 로그 전송 강제 등
    $is_partner_or_higher = false;
    if ( class_exists( 'JJ_Edition_Controller' ) ) {
        try {
            $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
        } catch ( Exception $e ) {
            // ignore
        } catch ( Error $e ) {
            // ignore
        }
    } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
        $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
    }

    // WP 코어 자동 업데이트 상태(플러그인 목록 UI와 동기화)
    $plugin_file = function_exists( 'plugin_basename' )
        ? plugin_basename( JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php' )
        : 'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php';
    $auto_updates = function_exists( 'get_site_option' ) ? (array) get_site_option( 'auto_update_plugins', array() ) : array();
    $core_auto_update_enabled = in_array( $plugin_file, $auto_updates, true );

    $update_settings = get_option( 'jj_style_guide_update_settings', array(
        'auto_update_enabled' => $core_auto_update_enabled,
        'update_channel' => 'stable',
        'beta_updates_enabled' => false,
        'send_app_logs' => false,
        'send_error_logs' => false,
    ) );
    
    // Partner/Master는 모든 로그를 반드시 전송
    if ( $is_partner_or_higher ) {
        $update_settings['send_app_logs'] = true;
        $update_settings['send_error_logs'] = true;
    }
    ?>
    <div style="max-width: 800px;">
        <?php
        // ============================================================
        // Updates Overview (Core + Add-ons in one table)
        // ============================================================
        if ( ! function_exists( 'get_plugins' ) && defined( 'ABSPATH' ) ) {
            $plugin_php = ABSPATH . 'wp-admin/includes/plugin.php';
            if ( file_exists( $plugin_php ) ) {
                require_once $plugin_php;
            }
        }
        $all_plugins = function_exists( 'get_plugins' ) ? (array) get_plugins() : array();
        $updates_obj = function_exists( 'get_site_transient' ) ? get_site_transient( 'update_plugins' ) : null;
        $updates_resp = ( is_object( $updates_obj ) && isset( $updates_obj->response ) && is_array( $updates_obj->response ) ) ? $updates_obj->response : array();
        $installed_channel = defined( 'JJ_STYLE_GUIDE_UPDATE_CHANNEL' ) ? JJ_STYLE_GUIDE_UPDATE_CHANNEL : '';

        $suite = array(
            array(
                'id' => 'core',
                'label' => 'ACF CSS (Core)',
                'candidates' => array( $plugin_file ),
                'channel' => $installed_channel,
            ),
            array(
                'id' => 'ai',
                'label' => 'ACF CSS AI Extension',
                'candidates' => array( 'acf-css-ai-extension/acf-css-ai-extension.php' ),
                'channel' => '',
            ),
            array(
                'id' => 'neural',
                'label' => 'ACF CSS Neural Link',
                'candidates' => array( 'acf-css-neural-link/acf-css-neural-link.php' ),
                'channel' => '',
            ),
            array(
                'id' => 'woo',
                'label' => 'ACF CSS Woo License',
                'candidates' => array( 'acf-css-woo-license/acf-css-woo-license.php' ),
                'channel' => '',
                'requires' => array( 'WooCommerce' ),
            ),
            array(
                'id' => 'bulk',
                'label' => 'WP Bulk Manager',
                'candidates' => array( 'wp-bulk-installer/wp-bulk-installer.php', 'wp-bulk-manager/wp-bulk-installer.php' ),
                'channel' => '',
            ),
            array(
                'id' => 'menu',
                'label' => 'Admin Menu Editor Lite',
                'candidates' => array( 'admin-menu-editor-lite/admin-menu-editor-lite.php' ),
                'channel' => '',
            ),
        );

        $find_plugin_file = function( $candidates ) use ( $all_plugins ) {
            foreach ( (array) $candidates as $cand ) {
                $cand = (string) $cand;
                if ( '' !== $cand && isset( $all_plugins[ $cand ] ) ) {
                    return $cand;
                }
            }
            // 설치되지 않았으면 첫 후보를 반환(표시/토글용 키)
            return isset( $candidates[0] ) ? (string) $candidates[0] : '';
        };
        ?>

        <h2 style="margin-top: 0;"><?php esc_html_e( '업데이트 개요 (코어 + 애드온)', 'jj-style-guide' ); ?></h2>
        <p class="description"><?php esc_html_e( 'WordPress 플러그인 목록 UX처럼, 설치/활성/업데이트/자동 업데이트 상태를 한 번에 확인합니다.', 'jj-style-guide' ); ?></p>

        <table class="widefat striped" style="margin-top: 12px;">
            <thead>
                <tr>
                    <th><?php esc_html_e( '플러그인', 'jj-style-guide' ); ?></th>
                    <th><?php esc_html_e( '상태', 'jj-style-guide' ); ?></th>
                    <th><?php esc_html_e( '버전', 'jj-style-guide' ); ?></th>
                    <th><?php esc_html_e( '업데이트', 'jj-style-guide' ); ?></th>
                    <th><?php esc_html_e( '자동 업데이트', 'jj-style-guide' ); ?></th>
                    <th><?php esc_html_e( '채널', 'jj-style-guide' ); ?></th>
                    <th><?php esc_html_e( '바로가기', 'jj-style-guide' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $suite as $it ) : ?>
                    <?php
                    $pf = $find_plugin_file( $it['candidates'] ?? array() );
                    $installed = ( '' !== $pf && isset( $all_plugins[ $pf ] ) );
                    $name = $installed ? (string) $all_plugins[ $pf ]['Name'] : (string) ( $it['label'] ?? $pf );
                    $ver  = $installed ? (string) $all_plugins[ $pf ]['Version'] : '—';
                    $active = ( $installed && function_exists( 'is_plugin_active' ) ) ? is_plugin_active( $pf ) : false;

                    $auto_enabled = ( '' !== $pf ) ? in_array( $pf, $auto_updates, true ) : false;
                    $upd = ( '' !== $pf && isset( $updates_resp[ $pf ] ) ) ? $updates_resp[ $pf ] : null;
                    $has_update = is_object( $upd ) && ! empty( $upd->new_version );
                    $new_ver = $has_update ? (string) $upd->new_version : '';

                    $channel = isset( $it['channel'] ) ? (string) $it['channel'] : '';
                    if ( '' === $channel && $ver ) {
                        if ( false !== strpos( $ver, 'staging' ) ) $channel = 'staging';
                        elseif ( false !== strpos( $ver, 'beta' ) ) $channel = 'beta';
                    }

                    $update_now_url = '';
                    if ( $has_update && function_exists( 'self_admin_url' ) && function_exists( 'wp_nonce_url' ) ) {
                        $update_now_url = wp_nonce_url(
                            self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . urlencode( $pf ) ),
                            'upgrade-plugin_' . $pf
                        );
                    }
                    ?>
                    <tr class="jj-suite-row" data-plugin="<?php echo esc_attr( $pf ); ?>">
                        <td>
                            <strong><?php echo esc_html( $name ); ?></strong>
                            <?php if ( ! $installed ) : ?>
                                <div class="description" style="margin-top: 2px;"><?php esc_html_e( '미설치', 'jj-style-guide' ); ?></div>
                            <?php endif; ?>
                            <?php if ( ! empty( $it['requires'] ) ) : ?>
                                <div class="description" style="margin-top: 2px;">
                                    <?php echo esc_html( 'Requires: ' . implode( ', ', (array) $it['requires'] ) ); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="jj-status-badge <?php echo $active ? 'active' : 'inactive'; ?>">
                                <?php echo $active ? esc_html__( 'ACTIVE', 'jj-style-guide' ) : esc_html__( 'INACTIVE', 'jj-style-guide' ); ?>
                            </span>
                        </td>
                        <td>
                            <code><?php echo esc_html( $ver ); ?></code>
                        </td>
                        <td>
                            <?php if ( $has_update ) : ?>
                                <span class="jj-license-type-badge jj-license-type-basic" style="background:#dba617;">
                                    <?php esc_html_e( 'UPDATE AVAILABLE', 'jj-style-guide' ); ?>
                                </span>
                                <div class="description" style="margin-top: 4px;">
                                    <?php
                                    printf(
                                        /* translators: %s: new version */
                                        esc_html__( '새 버전: %s', 'jj-style-guide' ),
                                        esc_html( $new_ver )
                                    );
                                    ?>
                                </div>
                                <?php if ( $update_now_url ) : ?>
                                    <div style="margin-top: 8px;">
                                        <a href="<?php echo esc_url( $update_now_url ); ?>" class="button button-small button-primary">
                                            <?php esc_html_e( '지금 업데이트', 'jj-style-guide' ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else : ?>
                                <span class="jj-license-type-badge jj-license-type-basic" style="background:#1d6b2f;">
                                    <?php esc_html_e( '최신', 'jj-style-guide' ); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="jj-status-badge jj-suite-auto-badge <?php echo $auto_enabled ? 'active' : 'inactive'; ?>">
                                <?php echo $auto_enabled ? esc_html__( 'AUTO UPDATE: ON', 'jj-style-guide' ) : esc_html__( 'AUTO UPDATE: OFF', 'jj-style-guide' ); ?>
                            </span>
                            <?php if ( $installed && $pf ) : ?>
                                <div style="margin-top: 8px;">
                                    <button type="button"
                                        class="button button-small jj-suite-toggle-auto-update"
                                        data-plugin="<?php echo esc_attr( $pf ); ?>"
                                        data-enabled="<?php echo $auto_enabled ? '1' : '0'; ?>">
                                        <?php echo $auto_enabled ? esc_html__( '비활성화', 'jj-style-guide' ) : esc_html__( '활성화', 'jj-style-guide' ); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( $channel ) : ?>
                                <span class="jj-license-type-badge jj-license-type-basic" style="background:#2271b1;">
                                    <?php echo esc_html( strtoupper( $channel ) ); ?>
                                </span>
                            <?php else : ?>
                                <span class="description">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button button-small button-secondary">
                                <?php esc_html_e( '플러그인 목록', 'jj-style-guide' ); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 14px; padding: 12px; border: 1px solid #c3c4c7; border-radius: 6px; background: #fff;">
            <p class="description" style="margin:0;">
                <?php esc_html_e( '자동 업데이트 토글은 WordPress 코어의 설정(플러그인 목록 화면)과 동일하게 동작합니다.', 'jj-style-guide' ); ?>
            </p>
        </div>

        <hr style="margin: 26px 0;">

        <h2 style="margin-top: 0;"><?php esc_html_e( '플러그인 업데이트 설정', 'jj-style-guide' ); ?></h2>
        <p class="description"><?php esc_html_e( '플러그인 업데이트 및 로그 전송 설정을 관리합니다. (WordPress 플러그인 목록의 “자동 업데이트 활성/비활성”과 동기화됩니다.)', 'jj-style-guide' ); ?></p>

        <?php
        $installed_channel = defined( 'JJ_STYLE_GUIDE_UPDATE_CHANNEL' ) ? JJ_STYLE_GUIDE_UPDATE_CHANNEL : '';
        ?>
        <div style="margin: 16px 0 8px; padding: 14px; border: 1px solid #c3c4c7; border-radius: 6px; background: #fff; display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
            <span class="jj-status-badge <?php echo $core_auto_update_enabled ? 'active' : 'inactive'; ?>">
                <?php echo $core_auto_update_enabled ? esc_html__( 'AUTO UPDATE: ON', 'jj-style-guide' ) : esc_html__( 'AUTO UPDATE: OFF', 'jj-style-guide' ); ?>
            </span>
            <?php if ( $installed_channel ) : ?>
                <span class="jj-license-type-badge jj-license-type-basic" style="background:#2271b1;">
                    <?php echo esc_html( strtoupper( $installed_channel ) ); ?>
                </span>
                <span class="description" style="margin:0;">
                    <?php
                    printf(
                        /* translators: %s: channel */
                        esc_html__( '현재 설치된 빌드 채널: %s', 'jj-style-guide' ),
                        esc_html( $installed_channel )
                    );
                    ?>
                </span>
            <?php endif; ?>
            <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button button-secondary" style="margin-left:auto;">
                <?php esc_html_e( '플러그인 목록에서 확인', 'jj-style-guide' ); ?>
            </a>
        </div>
        
        <table class="form-table" style="margin-top: 20px;">
            <tr>
                <th scope="row">
                    <label for="jj_auto_update_enabled"><?php esc_html_e( '자동 업데이트', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="checkbox" 
                                   id="jj_auto_update_enabled" 
                                   name="jj_update_settings[auto_update_enabled]" 
                                   value="1" 
                                   <?php checked( $update_settings['auto_update_enabled'], true ); ?>>
                            <?php esc_html_e( '자동 업데이트 활성화', 'jj-style-guide' ); ?>
                        </label>
                        <button type="button" 
                                id="jj-toggle-auto-update" 
                                class="button button-small"
                                data-enabled="<?php echo $update_settings['auto_update_enabled'] ? '1' : '0'; ?>">
                            <?php echo $update_settings['auto_update_enabled'] ? esc_html__( '비활성화', 'jj-style-guide' ) : esc_html__( '활성화', 'jj-style-guide' ); ?>
                        </button>
                    </div>
                    <p class="description">
                        <?php esc_html_e( '새 버전이 출시되면 자동으로 업데이트됩니다. (자동 업데이트를 꺼도 “업데이트 알림/수동 업데이트”는 유지됩니다.)', 'jj-style-guide' ); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="jj_update_channel"><?php esc_html_e( '업데이트 채널', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <select id="jj_update_channel" name="jj_update_settings[update_channel]" style="min-width: 200px;">
                        <option value="stable" <?php selected( $update_settings['update_channel'], 'stable' ); ?>>
                            <?php esc_html_e( '정식 (Stable)', 'jj-style-guide' ); ?>
                        </option>
                        <option value="beta" <?php selected( $update_settings['update_channel'], 'beta' ); ?>>
                            <?php esc_html_e( '베타 (Beta)', 'jj-style-guide' ); ?>
                        </option>
                        <?php if ( $is_partner_or_higher ) : ?>
                            <option value="staging" <?php selected( $update_settings['update_channel'], 'staging' ); ?>>
                                <?php esc_html_e( '스테이징 (Staging)', 'jj-style-guide' ); ?>
                            </option>
                        <?php endif; ?>
                    </select>
                    <p class="description">
                        <?php esc_html_e( '업데이트를 받을 채널을 선택하세요. 베타/스테이징은 실험적일 수 있으니 운영 사이트에서는 주의가 필요합니다.', 'jj-style-guide' ); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="jj_beta_updates_enabled"><?php esc_html_e( '베타 업데이트', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" 
                               id="jj_beta_updates_enabled" 
                               name="jj_update_settings[beta_updates_enabled]" 
                               value="1" 
                               <?php checked( $update_settings['beta_updates_enabled'], true ); ?>
                               <?php disabled( $is_partner_or_higher, true ); ?>>
                        <?php esc_html_e( '베타 업데이트 수신', 'jj-style-guide' ); ?>
                    </label>
                    <p class="description">
                        <?php esc_html_e( '베타 버전 업데이트를 받을지 선택합니다. 베타 버전은 불안정할 수 있습니다.', 'jj-style-guide' ); ?>
                        <?php if ( $is_partner_or_higher ) : ?>
                        <br><strong style="color: #d63638;"><?php esc_html_e( 'Partner/Master는 내부 정책상 로그/업데이트 옵션이 일부 고정될 수 있습니다.', 'jj-style-guide' ); ?></strong>
                        <?php endif; ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label><?php esc_html_e( '로그 전송', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" 
                                   id="jj_send_app_logs" 
                                   name="jj_update_settings[send_app_logs]" 
                                   value="1" 
                                   <?php checked( $update_settings['send_app_logs'], true ); ?>
                                   <?php disabled( $is_partner_or_higher, true ); ?>>
                            <?php esc_html_e( '앱 내 로그 전송', 'jj-style-guide' ); ?>
                        </label>
                        <?php if ( $is_partner_or_higher ) : ?>
                        <span style="color: #d63638; margin-left: 10px;"><?php esc_html_e( '(필수)', 'jj-style-guide' ); ?></span>
                        <?php endif; ?>
                        <p class="description" style="margin-left: 25px;">
                            <?php esc_html_e( '플러그인 내부 로그를 개발자에게 전송합니다.', 'jj-style-guide' ); ?>
                        </p>
                        
                        <label style="display: block; margin-top: 15px;">
                            <input type="checkbox" 
                                   id="jj_send_error_logs" 
                                   name="jj_update_settings[send_error_logs]" 
                                   value="1" 
                                   <?php checked( $update_settings['send_error_logs'], true ); ?>
                                   <?php disabled( $is_partner_or_higher, true ); ?>>
                            <?php esc_html_e( '오류 로그 전송', 'jj-style-guide' ); ?>
                        </label>
                        <?php if ( $is_partner_or_higher ) : ?>
                        <span style="color: #d63638; margin-left: 10px;"><?php esc_html_e( '(필수)', 'jj-style-guide' ); ?></span>
                        <?php endif; ?>
                        <p class="description" style="margin-left: 25px;">
                            <?php esc_html_e( '오류 및 예외 로그를 개발자에게 전송합니다.', 'jj-style-guide' ); ?>
                        </p>
                    </fieldset>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <button type="button" 
                    id="jj-save-update-settings" 
                    class="button button-primary">
                <?php esc_html_e( '설정 저장', 'jj-style-guide' ); ?>
            </button>
            <button type="button" 
                    id="jj-check-updates-now" 
                    class="button button-secondary">
                <?php esc_html_e( '지금 업데이트 확인', 'jj-style-guide' ); ?>
            </button>
        </p>
        
        <div id="jj-update-status" style="margin-top: 20px;"></div>

        <hr style="margin: 30px 0;">

        <?php
        // [Phase 5.2] Webhook 자동화 설정
        $webhook_settings = get_option( 'jj_style_guide_webhooks', array() );
        if ( ! is_array( $webhook_settings ) ) {
            $webhook_settings = array();
        }
        $wh_enabled      = ! empty( $webhook_settings['enabled'] );
        $wh_endpoints    = ( isset( $webhook_settings['endpoints'] ) && is_array( $webhook_settings['endpoints'] ) ) ? implode( "\n", array_map( 'trim', $webhook_settings['endpoints'] ) ) : '';
        $wh_payload_mode = ( isset( $webhook_settings['payload_mode'] ) && 'full' === $webhook_settings['payload_mode'] ) ? 'full' : 'minimal';
        $wh_timeout      = isset( $webhook_settings['timeout_seconds'] ) ? (int) $webhook_settings['timeout_seconds'] : 5;
        $wh_events       = ( isset( $webhook_settings['events'] ) && is_array( $webhook_settings['events'] ) ) ? $webhook_settings['events'] : array( 'style_settings_updated', 'admin_center_updated' );
        ?>

        <h2><?php esc_html_e( 'Webhook 자동화', 'jj-style-guide' ); ?></h2>
        <p class="description"><?php esc_html_e( '설정 변경 시 외부 자동화 시스템(CI/CD, Slack, 자체 서버 등)으로 Webhook을 전송합니다.', 'jj-style-guide' ); ?></p>

        <table class="form-table" style="margin-top: 20px;">
            <tr>
                <th scope="row">
                    <label for="jj_webhooks_enabled"><?php esc_html_e( 'Webhook 활성화', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="jj_webhooks_enabled" name="jj_webhooks[enabled]" value="1" <?php checked( $wh_enabled, true ); ?> />
                        <?php esc_html_e( '전송 활성화', 'jj-style-guide' ); ?>
                    </label>
                    <p class="description"><?php esc_html_e( '활성화 시, 아래 이벤트가 발생할 때 등록된 URL로 POST 요청이 전송됩니다.', 'jj-style-guide' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="jj_webhooks_endpoints"><?php esc_html_e( 'Webhook URL', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <textarea id="jj_webhooks_endpoints" name="jj_webhooks[endpoints]" rows="4" class="large-text code" placeholder="https://example.com/webhook"><?php echo esc_textarea( $wh_endpoints ); ?></textarea>
                    <p class="description"><?php esc_html_e( '한 줄에 하나씩 입력하세요. (여러 개 가능)', 'jj-style-guide' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="jj_webhooks_secret"><?php esc_html_e( '서명(Secret)', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <input type="password" id="jj_webhooks_secret" name="jj_webhooks[secret]" value="" class="regular-text" autocomplete="new-password" />
                    <label style="margin-left: 10px;">
                        <input type="checkbox" name="jj_webhooks[clear_secret]" value="1" />
                        <?php esc_html_e( 'Secret 초기화', 'jj-style-guide' ); ?>
                    </label>
                    <p class="description"><?php esc_html_e( '입력하면 저장되며, 비워두면 기존 Secret을 유지합니다. Secret이 있으면 X-JJ-Signature 헤더(HMAC-SHA256)가 포함됩니다.', 'jj-style-guide' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="jj_webhooks_payload_mode"><?php esc_html_e( '페이로드', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <select id="jj_webhooks_payload_mode" name="jj_webhooks[payload_mode]">
                        <option value="minimal" <?php selected( $wh_payload_mode, 'minimal' ); ?>>
                            <?php esc_html_e( 'Minimal (키/메타 중심)', 'jj-style-guide' ); ?>
                        </option>
                        <option value="full" <?php selected( $wh_payload_mode, 'full' ); ?>>
                            <?php esc_html_e( 'Full (설정 전체 포함)', 'jj-style-guide' ); ?>
                        </option>
                    </select>
                    <p class="description"><?php esc_html_e( 'Full은 데이터가 커질 수 있으므로 자동화 목적에 맞게 선택하세요.', 'jj-style-guide' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="jj_webhooks_timeout"><?php esc_html_e( '타임아웃(초)', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <input type="number" id="jj_webhooks_timeout" name="jj_webhooks[timeout_seconds]" min="1" max="30" value="<?php echo esc_attr( $wh_timeout ); ?>" />
                    <p class="description"><?php esc_html_e( '테스트/동기 전송 시 적용됩니다. 실사용 전송은 비동기(blocking=false)로 수행됩니다.', 'jj-style-guide' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e( '이벤트', 'jj-style-guide' ); ?></th>
                <td>
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="checkbox" name="jj_webhooks[events][]" value="style_settings_updated" <?php checked( in_array( 'style_settings_updated', $wh_events, true ), true ); ?> />
                        <?php esc_html_e( '스타일 센터 설정 저장', 'jj-style-guide' ); ?>
                    </label>
                    <label style="display: block;">
                        <input type="checkbox" name="jj_webhooks[events][]" value="admin_center_updated" <?php checked( in_array( 'admin_center_updated', $wh_events, true ), true ); ?> />
                        <?php esc_html_e( 'Admin Center(메뉴/상단바/텍스트/업데이트 등) 저장', 'jj-style-guide' ); ?>
                    </label>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="button" id="jj-test-webhook" class="button button-secondary">
                <?php esc_html_e( 'Webhook 테스트', 'jj-style-guide' ); ?>
            </button>
            <span id="jj-webhook-test-result" style="margin-left: 10px;"></span>
        </p>
    </div>
</div>

