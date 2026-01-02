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

        $last_checked_ts = ( is_object( $updates_obj ) && isset( $updates_obj->last_checked ) ) ? (int) $updates_obj->last_checked : 0;
        $last_checked_human = ( $last_checked_ts && function_exists( 'date_i18n' ) ) ? date_i18n( 'Y-m-d H:i:s', $last_checked_ts ) : ( $last_checked_ts ? (string) $last_checked_ts : '—' );
        $next_check_ts = function_exists( 'wp_next_scheduled' ) ? (int) wp_next_scheduled( 'wp_update_plugins' ) : 0;
        $next_check_human = ( $next_check_ts && function_exists( 'date_i18n' ) ) ? date_i18n( 'Y-m-d H:i:s', $next_check_ts ) : ( $next_check_ts ? (string) $next_check_ts : '—' );

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
                'requires_plugins' => array( 'woocommerce/woocommerce.php' ),
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

        // Suite summary
        $suite_total = count( $suite );
        $suite_installed = 0;
        $suite_active = 0;
        $suite_updates = 0;
        $suite_auto = 0;
        foreach ( $suite as $it_sum ) {
            $pf_sum = $find_plugin_file( $it_sum['candidates'] ?? array() );
            if ( '' === $pf_sum ) continue;
            $installed_sum = isset( $all_plugins[ $pf_sum ] );
            if ( $installed_sum ) {
                $suite_installed++;
                if ( function_exists( 'is_plugin_active' ) && is_plugin_active( $pf_sum ) ) {
                    $suite_active++;
                }
                if ( isset( $updates_resp[ $pf_sum ] ) ) {
                    $u = $updates_resp[ $pf_sum ];
                    if ( is_object( $u ) && ! empty( $u->new_version ) ) {
                        $suite_updates++;
                    }
                }
                if ( in_array( $pf_sum, $auto_updates, true ) ) {
                    $suite_auto++;
                }
            }
        }
        ?>

        <h2 style="margin-top: 0;"><?php esc_html_e( '업데이트 개요 (코어 + 애드온)', 'acf-css-really-simple-style-management-center' ); ?></h2>
        <p class="description"><?php esc_html_e( 'WordPress 플러그인 목록 UX처럼, 설치/활성/업데이트/자동 업데이트 상태를 한 번에 확인합니다.', 'acf-css-really-simple-style-management-center' ); ?></p>

        <?php
        // ============================================================
        // [Phase 8.0] Suite 전체 일괄 제어 패널
        // ============================================================
        // 코어 버전 추출 (주 버전만)
        $core_version = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '0.0.0';
        $core_major = preg_match( '/^(\d+\.\d+)/', $core_version, $m ) ? $m[1] : '0.0';
        
        // 버전 불일치 감지
        $version_mismatches = array();
        foreach ( $suite as $it_vm ) {
            $pf_vm = $find_plugin_file( $it_vm['candidates'] ?? array() );
            if ( '' === $pf_vm || ! isset( $all_plugins[ $pf_vm ] ) ) continue;
            
            $ver_vm = (string) $all_plugins[ $pf_vm ]['Version'];
            $addon_major = preg_match( '/^(\d+\.\d+)/', $ver_vm, $m ) ? $m[1] : '0.0';
            
            // 코어와 애드온 간 주 버전 불일치 감지
            if ( $core_major !== $addon_major && $it_vm['id'] !== 'core' ) {
                $version_mismatches[] = array(
                    'name' => isset( $it_vm['label'] ) ? (string) $it_vm['label'] : $pf_vm,
                    'version' => $ver_vm,
                    'core_version' => $core_version,
                    'expected_major' => $core_major,
                );
            }
        }
        ?>
        
        <!-- Suite 전체 일괄 제어 패널 -->
        <div style="margin: 20px 0; padding: 16px; border: 2px solid #2271b1; border-radius: 6px; background: #f0f6fc;">
            <h3 style="margin-top: 0; color: #2271b1;">
                <span class="dashicons dashicons-admin-settings" style="vertical-align: middle;"></span>
                <?php esc_html_e( 'Suite 전체 일괄 제어', 'acf-css-really-simple-style-management-center' ); ?>
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 12px; margin-top: 12px;">
                <!-- 전체 자동 업데이트 제어 -->
                <div style="padding: 12px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px;">
                    <strong style="display: block; margin-bottom: 8px;"><?php esc_html_e( '전체 자동 업데이트', 'acf-css-really-simple-style-management-center' ); ?></strong>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <button type="button" class="button button-primary" id="jj-suite-auto-update-all-on">
                            <span class="dashicons dashicons-update" style="vertical-align: middle;"></span>
                            <?php esc_html_e( '전체 ON', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <button type="button" class="button button-secondary" id="jj-suite-auto-update-all-off">
                            <span class="dashicons dashicons-dismiss" style="vertical-align: middle;"></span>
                            <?php esc_html_e( '전체 OFF', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                    <p class="description" style="margin: 8px 0 0 0; font-size: 12px;">
                        <?php esc_html_e( '설치된 모든 Suite 플러그인의 자동 업데이트를 일괄 제어합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </div>
                
                <!-- 전체 업데이트 체크 -->
                <div style="padding: 12px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px;">
                    <strong style="display: block; margin-bottom: 8px;"><?php esc_html_e( '업데이트 확인', 'acf-css-really-simple-style-management-center' ); ?></strong>
                    <button type="button" class="button button-primary" id="jj-suite-check-all-updates">
                        <span class="dashicons dashicons-update" style="vertical-align: middle;"></span>
                        <?php esc_html_e( '전체 업데이트 체크', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                    <p class="description" style="margin: 8px 0 0 0; font-size: 12px;">
                        <?php esc_html_e( 'WordPress 업데이트 서버에서 모든 Suite 플러그인의 업데이트를 확인합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </div>
                
                <!-- 업데이트 적용 안내 -->
                <div style="padding: 12px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px;">
                    <strong style="display: block; margin-bottom: 8px;"><?php esc_html_e( '업데이트 적용', 'acf-css-really-simple-style-management-center' ); ?></strong>
                    <?php if ( $suite_updates > 0 ) : ?>
                        <div style="padding: 8px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; margin-bottom: 8px;">
                            <strong style="color: #856404;">
                                <?php
                                printf(
                                    /* translators: %d: number of updates */
                                    esc_html__( '%d개 플러그인 업데이트 가능', 'acf-css-really-simple-style-management-center' ),
                                    (int) $suite_updates
                                );
                                ?>
                            </strong>
                        </div>
                        <a href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>" class="button button-primary">
                            <?php esc_html_e( '업데이트 페이지로 이동', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    <?php else : ?>
                        <div style="padding: 8px; background: #d1e7dd; border: 1px solid #198754; border-radius: 4px;">
                            <span style="color: #0f5132;">
                                <span class="dashicons dashicons-yes-alt" style="vertical-align: middle;"></span>
                                <?php esc_html_e( '모든 플러그인이 최신 버전입니다.', 'acf-css-really-simple-style-management-center' ); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <p class="description" style="margin: 8px 0 0 0; font-size: 12px;">
                        <?php esc_html_e( '업데이트가 있으면 WordPress 업데이트 페이지에서 일괄 적용할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </div>
            </div>
            
            <?php if ( ! empty( $version_mismatches ) ) : ?>
                <!-- 버전 불일치 경고 -->
                <div style="margin-top: 16px; padding: 14px; background: #f8d7da; border: 2px solid #d63638; border-radius: 4px;">
                    <h4 style="margin: 0 0 10px 0; color: #d63638;">
                        <span class="dashicons dashicons-warning" style="vertical-align: middle;"></span>
                        <?php esc_html_e( '버전 불일치 감지', 'acf-css-really-simple-style-management-center' ); ?>
                    </h4>
                    <p style="margin: 0 0 10px 0; color: #721c24;">
                        <?php
                        printf(
                            /* translators: 1: core version 2: number of mismatches */
                            esc_html__( '코어 버전(%1$s)과 호환되지 않는 애드온이 %2$d개 발견되었습니다.', 'acf-css-really-simple-style-management-center' ),
                            esc_html( $core_version ),
                            count( $version_mismatches )
                        );
                        ?>
                    </p>
                    <ul style="margin: 10px 0 0 20px; padding: 0;">
                        <?php foreach ( $version_mismatches as $vm ) : ?>
                            <li style="margin-bottom: 6px; color: #721c24;">
                                <strong><?php echo esc_html( $vm['name'] ); ?>:</strong>
                                <?php
                                printf(
                                    /* translators: 1: current version 2: expected major version */
                                    esc_html__( '현재 %1$s (예상: %2$s.x)', 'acf-css-really-simple-style-management-center' ),
                                    esc_html( $vm['version'] ),
                                    esc_html( $vm['expected_major'] )
                                );
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p style="margin: 12px 0 0 0; font-size: 12px; color: #721c24;">
                        <?php esc_html_e( '💡 권장 조치: 코어와 애드온을 모두 최신 버전으로 업데이트하거나, 동일한 주 버전으로 맞추세요.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-top: 10px;">
            <input type="search"
                   id="jj-updates-suite-search"
                   class="regular-text"
                   style="min-width: 260px;"
                   placeholder="<?php echo esc_attr__( '플러그인 검색…', 'acf-css-really-simple-style-management-center' ); ?>" />
            <label style="display:inline-flex; gap:6px; align-items:center;">
                <input type="checkbox" id="jj-updates-suite-hide-uninstalled" />
                <?php esc_html_e( '미설치 숨기기', 'acf-css-really-simple-style-management-center' ); ?>
            </label>
            <label style="display:inline-flex; gap:6px; align-items:center;">
                <input type="checkbox" id="jj-updates-suite-only-updates" />
                <?php esc_html_e( '업데이트만 보기', 'acf-css-really-simple-style-management-center' ); ?>
            </label>

            <button type="button" class="button" id="jj-suite-refresh-updates">
                <?php esc_html_e( '전체 업데이트 다시 체크', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" class="button button-secondary" id="jj-suite-auto-update-on">
                <?php esc_html_e( '보이는 항목 Auto-Update ON', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" class="button button-secondary" id="jj-suite-auto-update-off">
                <?php esc_html_e( '보이는 항목 Auto-Update OFF', 'acf-css-really-simple-style-management-center' ); ?>
            </button>

            <button type="button" class="button" id="jj-suite-copy-report">
                <?php esc_html_e( '스위트 리포트 복사', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" class="button" id="jj-suite-download-report">
                <?php esc_html_e( '스위트 리포트 JSON', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <label style="display:inline-flex; gap:6px; align-items:center;">
                <input type="checkbox" id="jj-updates-suite-only-mismatch" />
                <?php esc_html_e( '불일치만 보기', 'acf-css-really-simple-style-management-center' ); ?>
            </label>

            <span class="description" style="margin-left:auto;">
                <?php
                printf(
                    /* translators: 1: installed 2: total 3: active 4: updates 5: auto updates */
                    esc_html__( '설치 %1$d/%2$d · 활성 %3$d · 업데이트 %4$d · Auto-Update %5$d', 'acf-css-really-simple-style-management-center' ),
                    (int) $suite_installed,
                    (int) $suite_total,
                    (int) $suite_active,
                    (int) $suite_updates,
                    (int) $suite_auto
                );
                ?>
            </span>
            <span class="description" style="margin-left: 10px;">
                <?php
                printf(
                    /* translators: 1: last checked 2: next check */
                    esc_html__( '마지막 체크: %1$s · 다음 체크: %2$s', 'acf-css-really-simple-style-management-center' ),
                    esc_html( $last_checked_human ),
                    esc_html( $next_check_human )
                );
                ?>
                · <a href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>"><?php esc_html_e( '워드프레스 업데이트', 'acf-css-really-simple-style-management-center' ); ?></a>
            </span>
        </div>

        <div id="jj-suite-actions-status" style="margin-top: 10px;"></div>

        <table class="widefat striped" style="margin-top: 12px;">
            <thead>
                <tr>
                    <th><?php esc_html_e( '플러그인', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <th><?php esc_html_e( '상태', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <th><?php esc_html_e( '버전', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <th><?php esc_html_e( '업데이트', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <th><?php esc_html_e( '자동 업데이트', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <th><?php esc_html_e( '채널', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <th><?php esc_html_e( '바로가기', 'acf-css-really-simple-style-management-center' ); ?></th>
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

                    $core_channel = $installed_channel ? strtolower( (string) $installed_channel ) : 'stable';
                    $this_channel = $channel ? strtolower( (string) $channel ) : 'stable';
                    $is_mismatch = ( $installed && $pf && $this_channel !== $core_channel && ( $it['id'] ?? '' ) !== 'core' );

                    $update_now_url = '';
                    if ( $has_update && function_exists( 'self_admin_url' ) && function_exists( 'wp_nonce_url' ) ) {
                        $update_now_url = wp_nonce_url(
                            self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . urlencode( $pf ) ),
                            'upgrade-plugin_' . $pf
                        );
                    }

                    // Activate / Deactivate links (WP core actions)
                    $activate_url = '';
                    $deactivate_url = '';
                    if ( $installed && $pf && function_exists( 'current_user_can' ) && current_user_can( 'activate_plugins' ) && function_exists( 'wp_nonce_url' ) ) {
                        if ( ! $active ) {
                            $activate_url = wp_nonce_url(
                                admin_url( 'plugins.php?action=activate&plugin=' . urlencode( $pf ) ),
                                'activate-plugin_' . $pf
                            );
                        } else {
                            $deactivate_url = wp_nonce_url(
                                admin_url( 'plugins.php?action=deactivate&plugin=' . urlencode( $pf ) ),
                                'deactivate-plugin_' . $pf
                            );
                        }
                    }

                    // Required plugins status badges
                    $requires_plugins = isset( $it['requires_plugins'] ) ? (array) $it['requires_plugins'] : array();
                    $requires_badges = array();
                    if ( ! empty( $requires_plugins ) ) {
                        foreach ( $requires_plugins as $req_pf ) {
                            $req_pf = (string) $req_pf;
                            $req_installed = ( '' !== $req_pf && isset( $all_plugins[ $req_pf ] ) );
                            $req_active = ( $req_installed && function_exists( 'is_plugin_active' ) ) ? is_plugin_active( $req_pf ) : false;
                            $req_name = $req_installed ? (string) $all_plugins[ $req_pf ]['Name'] : $req_pf;
                            $requires_badges[] = array(
                                'name' => $req_name,
                                'installed' => $req_installed,
                                'active' => $req_active,
                            );
                        }
                    }

                    ?>
                    <tr class="jj-suite-row"
                        data-plugin="<?php echo esc_attr( $pf ); ?>"
                        data-name="<?php echo esc_attr( strtolower( $name . ' ' . $pf ) ); ?>"
                        data-installed="<?php echo $installed ? '1' : '0'; ?>"
                        data-has-update="<?php echo $has_update ? '1' : '0'; ?>"
                        data-mismatch="<?php echo $is_mismatch ? '1' : '0'; ?>"
                        data-channel="<?php echo esc_attr( $this_channel ); ?>"
                        data-core-channel="<?php echo esc_attr( $core_channel ); ?>"
                        data-version="<?php echo esc_attr( $ver ); ?>"
                        data-new-version="<?php echo esc_attr( $new_ver ); ?>"
                        data-active="<?php echo $active ? '1' : '0'; ?>"
                        data-auto-update="<?php echo $auto_enabled ? '1' : '0'; ?>"
                        data-update-now-url="<?php echo esc_attr( $update_now_url ); ?>"
                        data-activate-url="<?php echo esc_attr( $activate_url ); ?>"
                        data-deactivate-url="<?php echo esc_attr( $deactivate_url ); ?>"
                        >
                        <td>
                            <strong><?php echo esc_html( $name ); ?></strong>
                            <?php if ( ! $installed ) : ?>
                                <div class="description" style="margin-top: 2px;"><?php esc_html_e( '미설치', 'acf-css-really-simple-style-management-center' ); ?></div>
                            <?php endif; ?>
                            <?php if ( ! empty( $it['requires'] ) ) : ?>
                                <div class="description" style="margin-top: 2px;">
                                    <?php echo esc_html( 'Requires: ' . implode( ', ', (array) $it['requires'] ) ); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ( ! empty( $requires_badges ) ) : ?>
                                <div style="margin-top: 6px; display:flex; gap:6px; flex-wrap:wrap;">
                                    <?php foreach ( $requires_badges as $rb ) : ?>
                                        <?php
                                        $ok = ! empty( $rb['active'] );
                                        $installed_req = ! empty( $rb['installed'] );
                                        $label = $installed_req ? ( $ok ? 'ACTIVE' : 'INACTIVE' ) : 'MISSING';
                                        $cls = $ok ? 'active' : 'inactive';
                                        ?>
                                        <span class="jj-status-badge <?php echo esc_attr( $cls ); ?>" style="font-size:11px; padding:4px 8px;">
                                            <?php echo esc_html( $rb['name'] . ': ' . $label ); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="jj-status-badge <?php echo $active ? 'active' : 'inactive'; ?>">
                                <?php echo $active ? esc_html__( 'ACTIVE', 'acf-css-really-simple-style-management-center' ) : esc_html__( 'INACTIVE', 'acf-css-really-simple-style-management-center' ); ?>
                            </span>
                            <?php if ( $activate_url ) : ?>
                                <div style="margin-top: 8px;">
                                    <a class="button button-small button-primary" href="<?php echo esc_url( $activate_url ); ?>">
                                        <?php esc_html_e( '활성화', 'acf-css-really-simple-style-management-center' ); ?>
                                    </a>
                                </div>
                            <?php elseif ( $deactivate_url ) : ?>
                                <div style="margin-top: 8px;">
                                    <a class="button button-small" href="<?php echo esc_url( $deactivate_url ); ?>">
                                        <?php esc_html_e( '비활성화', 'acf-css-really-simple-style-management-center' ); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <code><?php echo esc_html( $ver ); ?></code>
                        </td>
                        <td>
                            <?php if ( $has_update ) : ?>
                                <span class="jj-license-type-badge jj-license-type-basic" style="background:#dba617;">
                                    <?php esc_html_e( 'UPDATE AVAILABLE', 'acf-css-really-simple-style-management-center' ); ?>
                                </span>
                                <div class="description" style="margin-top: 4px;">
                                    <?php
                                    printf(
                                        /* translators: %s: new version */
                                        esc_html__( '새 버전: %s', 'acf-css-really-simple-style-management-center' ),
                                        esc_html( $new_ver )
                                    );
                                    ?>
                                </div>
                                <?php if ( $update_now_url ) : ?>
                                    <div style="margin-top: 8px;">
                                        <a href="<?php echo esc_url( $update_now_url ); ?>" class="button button-small button-primary">
                                            <?php esc_html_e( '지금 업데이트', 'acf-css-really-simple-style-management-center' ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else : ?>
                                <span class="jj-license-type-badge jj-license-type-basic" style="background:#1d6b2f;">
                                    <?php esc_html_e( '최신', 'acf-css-really-simple-style-management-center' ); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="jj-status-badge jj-suite-auto-badge <?php echo $auto_enabled ? 'active' : 'inactive'; ?>">
                                <?php echo $auto_enabled ? esc_html__( 'AUTO UPDATE: ON', 'acf-css-really-simple-style-management-center' ) : esc_html__( 'AUTO UPDATE: OFF', 'acf-css-really-simple-style-management-center' ); ?>
                            </span>
                            <?php if ( $installed && $pf ) : ?>
                                <div style="margin-top: 8px;">
                                    <button type="button"
                                        class="button button-small jj-suite-toggle-auto-update"
                                        data-plugin="<?php echo esc_attr( $pf ); ?>"
                                        data-enabled="<?php echo $auto_enabled ? '1' : '0'; ?>">
                                        <?php echo $auto_enabled ? esc_html__( '비활성화', 'acf-css-really-simple-style-management-center' ) : esc_html__( '활성화', 'acf-css-really-simple-style-management-center' ); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( $channel ) : ?>
                                <span class="jj-license-type-badge jj-license-type-basic" style="background:#2271b1;">
                                    <?php echo esc_html( strtoupper( $channel ) ); ?>
                                </span>
                                <?php if ( $is_mismatch ) : ?>
                                    <span class="jj-license-type-badge jj-license-type-basic" style="background:#d63638; margin-left:6px;">
                                        <?php esc_html_e( 'MISMATCH', 'acf-css-really-simple-style-management-center' ); ?>
                                    </span>
                                <?php endif; ?>
                            <?php else : ?>
                                <span class="description">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button button-small button-secondary">
                                <?php esc_html_e( '플러그인 목록', 'acf-css-really-simple-style-management-center' ); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 14px; padding: 12px; border: 1px solid #c3c4c7; border-radius: 6px; background: #fff;">
            <p class="description" style="margin:0;">
                <?php esc_html_e( '자동 업데이트 토글은 WordPress 코어의 설정(플러그인 목록 화면)과 동일하게 동작합니다.', 'acf-css-really-simple-style-management-center' ); ?>
            </p>
        </div>

        <hr style="margin: 26px 0;">

        <h2 style="margin-top: 0;"><?php esc_html_e( '플러그인 업데이트 설정', 'acf-css-really-simple-style-management-center' ); ?></h2>
        <p class="description"><?php esc_html_e( '플러그인 업데이트 및 로그 전송 설정을 관리합니다. (WordPress 플러그인 목록의 “자동 업데이트 활성/비활성”과 동기화됩니다.)', 'acf-css-really-simple-style-management-center' ); ?></p>

        <?php
        $installed_channel = defined( 'JJ_STYLE_GUIDE_UPDATE_CHANNEL' ) ? JJ_STYLE_GUIDE_UPDATE_CHANNEL : '';
        ?>
        <div style="margin: 16px 0 8px; padding: 14px; border: 1px solid #c3c4c7; border-radius: 6px; background: #fff; display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
            <span class="jj-status-badge <?php echo $core_auto_update_enabled ? 'active' : 'inactive'; ?>">
                <?php echo $core_auto_update_enabled ? esc_html__( 'AUTO UPDATE: ON', 'acf-css-really-simple-style-management-center' ) : esc_html__( 'AUTO UPDATE: OFF', 'acf-css-really-simple-style-management-center' ); ?>
            </span>
            <?php if ( $installed_channel ) : ?>
                <span class="jj-license-type-badge jj-license-type-basic" style="background:#2271b1;">
                    <?php echo esc_html( strtoupper( $installed_channel ) ); ?>
                </span>
                <span class="description" style="margin:0;">
                    <?php
                    printf(
                        /* translators: %s: channel */
                        esc_html__( '현재 설치된 빌드 채널: %s', 'acf-css-really-simple-style-management-center' ),
                        esc_html( $installed_channel )
                    );
                    ?>
                </span>
            <?php endif; ?>
            <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button button-secondary" style="margin-left:auto;">
                <?php esc_html_e( '플러그인 목록에서 확인', 'acf-css-really-simple-style-management-center' ); ?>
            </a>
        </div>
        
        <table class="form-table" style="margin-top: 20px;">
            <tr>
                <th scope="row">
                    <label for="jj_auto_update_enabled"><?php esc_html_e( '자동 업데이트', 'acf-css-really-simple-style-management-center' ); ?></label>
                </th>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="checkbox" 
                                   id="jj_auto_update_enabled" 
                                   name="jj_update_settings[auto_update_enabled]" 
                                   value="1" 
                                   <?php checked( $update_settings['auto_update_enabled'], true ); ?>>
                            <?php esc_html_e( '자동 업데이트 활성화', 'acf-css-really-simple-style-management-center' ); ?>
                        </label>
                        <button type="button" 
                                id="jj-toggle-auto-update" 
                                class="button button-small"
                                data-enabled="<?php echo $update_settings['auto_update_enabled'] ? '1' : '0'; ?>">
                            <?php echo $update_settings['auto_update_enabled'] ? esc_html__( '비활성화', 'acf-css-really-simple-style-management-center' ) : esc_html__( '활성화', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                    <p class="description">
                        <?php esc_html_e( '새 버전이 출시되면 자동으로 업데이트됩니다. (자동 업데이트를 꺼도 “업데이트 알림/수동 업데이트”는 유지됩니다.)', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="jj_update_channel"><?php esc_html_e( '업데이트 채널', 'acf-css-really-simple-style-management-center' ); ?></label>
                </th>
                <td>
                    <select id="jj_update_channel" name="jj_update_settings[update_channel]" style="min-width: 200px;">
                        <option value="stable" <?php selected( $update_settings['update_channel'], 'stable' ); ?>>
                            <?php esc_html_e( '정식 (Stable)', 'acf-css-really-simple-style-management-center' ); ?>
                        </option>
                        <option value="beta" <?php selected( $update_settings['update_channel'], 'beta' ); ?>>
                            <?php esc_html_e( '베타 (Beta)', 'acf-css-really-simple-style-management-center' ); ?>
                        </option>
                        <?php if ( $is_partner_or_higher ) : ?>
                            <option value="staging" <?php selected( $update_settings['update_channel'], 'staging' ); ?>>
                                <?php esc_html_e( '스테이징 (Staging)', 'acf-css-really-simple-style-management-center' ); ?>
                            </option>
                        <?php endif; ?>
                    </select>
                    <p class="description">
                        <?php esc_html_e( '업데이트를 받을 채널을 선택하세요. 베타/스테이징은 실험적일 수 있으니 운영 사이트에서는 주의가 필요합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="jj_beta_updates_enabled"><?php esc_html_e( '베타 업데이트', 'acf-css-really-simple-style-management-center' ); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" 
                               id="jj_beta_updates_enabled" 
                               name="jj_update_settings[beta_updates_enabled]" 
                               value="1" 
                               <?php checked( $update_settings['beta_updates_enabled'], true ); ?>
                               <?php disabled( $is_partner_or_higher, true ); ?>>
                        <?php esc_html_e( '베타 업데이트 수신', 'acf-css-really-simple-style-management-center' ); ?>
                    </label>
                    <p class="description">
                        <?php esc_html_e( '베타 버전 업데이트를 받을지 선택합니다. 베타 버전은 불안정할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                        <?php if ( $is_partner_or_higher ) : ?>
                        <br><strong style="color: #d63638;"><?php esc_html_e( 'Partner/Master는 내부 정책상 로그/업데이트 옵션이 일부 고정될 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?></strong>
                        <?php endif; ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label><?php esc_html_e( '로그 전송', 'acf-css-really-simple-style-management-center' ); ?></label>
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
                            <?php esc_html_e( '앱 내 로그 전송', 'acf-css-really-simple-style-management-center' ); ?>
                        </label>
                        <?php if ( $is_partner_or_higher ) : ?>
                        <span style="color: #d63638; margin-left: 10px;"><?php esc_html_e( '(필수)', 'acf-css-really-simple-style-management-center' ); ?></span>
                        <?php endif; ?>
                        <p class="description" style="margin-left: 25px;">
                            <?php esc_html_e( '플러그인 내부 로그를 개발자에게 전송합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                        </p>
                        
                        <label style="display: block; margin-top: 15px;">
                            <input type="checkbox" 
                                   id="jj_send_error_logs" 
                                   name="jj_update_settings[send_error_logs]" 
                                   value="1" 
                                   <?php checked( $update_settings['send_error_logs'], true ); ?>
                                   <?php disabled( $is_partner_or_higher, true ); ?>>
                            <?php esc_html_e( '오류 로그 전송', 'acf-css-really-simple-style-management-center' ); ?>
                        </label>
                        <?php if ( $is_partner_or_higher ) : ?>
                        <span style="color: #d63638; margin-left: 10px;"><?php esc_html_e( '(필수)', 'acf-css-really-simple-style-management-center' ); ?></span>
                        <?php endif; ?>
                        <p class="description" style="margin-left: 25px;">
                            <?php esc_html_e( '오류 및 예외 로그를 개발자에게 전송합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                        </p>
                    </fieldset>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <button type="button" 
                    id="jj-save-update-settings" 
                    class="button button-primary">
                <?php esc_html_e( '설정 저장', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" 
                    id="jj-check-updates-now" 
                    class="button button-secondary">
                <?php esc_html_e( '지금 업데이트 확인', 'acf-css-really-simple-style-management-center' ); ?>
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

        <h2><?php esc_html_e( 'Webhook 자동화', 'acf-css-really-simple-style-management-center' ); ?></h2>
        <p class="description"><?php esc_html_e( '설정 변경 시 외부 자동화 시스템(CI/CD, Slack, 자체 서버 등)으로 Webhook을 전송합니다.', 'acf-css-really-simple-style-management-center' ); ?></p>

        <table class="form-table" style="margin-top: 20px;">
            <tr>
                <th scope="row">
                    <label for="jj_webhooks_enabled"><?php esc_html_e( 'Webhook 활성화', 'acf-css-really-simple-style-management-center' ); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="jj_webhooks_enabled" name="jj_webhooks[enabled]" value="1" <?php checked( $wh_enabled, true ); ?> />
                        <?php esc_html_e( '전송 활성화', 'acf-css-really-simple-style-management-center' ); ?>
                    </label>
                    <p class="description"><?php esc_html_e( '활성화 시, 아래 이벤트가 발생할 때 등록된 URL로 POST 요청이 전송됩니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="jj_webhooks_endpoints"><?php esc_html_e( 'Webhook URL', 'acf-css-really-simple-style-management-center' ); ?></label>
                </th>
                <td>
                    <textarea id="jj_webhooks_endpoints" name="jj_webhooks[endpoints]" rows="4" class="large-text code" placeholder="https://example.com/webhook"><?php echo esc_textarea( $wh_endpoints ); ?></textarea>
                    <p class="description"><?php esc_html_e( '한 줄에 하나씩 입력하세요. (여러 개 가능)', 'acf-css-really-simple-style-management-center' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="jj_webhooks_secret"><?php esc_html_e( '서명(Secret)', 'acf-css-really-simple-style-management-center' ); ?></label>
                </th>
                <td>
                    <input type="password" id="jj_webhooks_secret" name="jj_webhooks[secret]" value="" class="regular-text" autocomplete="new-password" />
                    <label style="margin-left: 10px;">
                        <input type="checkbox" name="jj_webhooks[clear_secret]" value="1" />
                        <?php esc_html_e( 'Secret 초기화', 'acf-css-really-simple-style-management-center' ); ?>
                    </label>
                    <p class="description"><?php esc_html_e( '입력하면 저장되며, 비워두면 기존 Secret을 유지합니다. Secret이 있으면 X-JJ-Signature 헤더(HMAC-SHA256)가 포함됩니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="jj_webhooks_payload_mode"><?php esc_html_e( '페이로드', 'acf-css-really-simple-style-management-center' ); ?></label>
                </th>
                <td>
                    <select id="jj_webhooks_payload_mode" name="jj_webhooks[payload_mode]">
                        <option value="minimal" <?php selected( $wh_payload_mode, 'minimal' ); ?>>
                            <?php esc_html_e( 'Minimal (키/메타 중심)', 'acf-css-really-simple-style-management-center' ); ?>
                        </option>
                        <option value="full" <?php selected( $wh_payload_mode, 'full' ); ?>>
                            <?php esc_html_e( 'Full (설정 전체 포함)', 'acf-css-really-simple-style-management-center' ); ?>
                        </option>
                    </select>
                    <p class="description"><?php esc_html_e( 'Full은 데이터가 커질 수 있으므로 자동화 목적에 맞게 선택하세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="jj_webhooks_timeout"><?php esc_html_e( '타임아웃(초)', 'acf-css-really-simple-style-management-center' ); ?></label>
                </th>
                <td>
                    <input type="number" id="jj_webhooks_timeout" name="jj_webhooks[timeout_seconds]" min="1" max="30" value="<?php echo esc_attr( $wh_timeout ); ?>" />
                    <p class="description"><?php esc_html_e( '테스트/동기 전송 시 적용됩니다. 실사용 전송은 비동기(blocking=false)로 수행됩니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e( '이벤트', 'acf-css-really-simple-style-management-center' ); ?></th>
                <td>
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="checkbox" name="jj_webhooks[events][]" value="style_settings_updated" <?php checked( in_array( 'style_settings_updated', $wh_events, true ), true ); ?> />
                        <?php esc_html_e( '스타일 센터 설정 저장', 'acf-css-really-simple-style-management-center' ); ?>
                    </label>
                    <label style="display: block;">
                        <input type="checkbox" name="jj_webhooks[events][]" value="admin_center_updated" <?php checked( in_array( 'admin_center_updated', $wh_events, true ), true ); ?> />
                        <?php esc_html_e( 'Admin Center(메뉴/상단바/텍스트/업데이트 등) 저장', 'acf-css-really-simple-style-management-center' ); ?>
                    </label>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="button" id="jj-test-webhook" class="button button-secondary">
                <?php esc_html_e( 'Webhook 테스트', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <span id="jj-webhook-test-result" style="margin-left: 10px;"></span>
        </p>
    </div>
</div>

