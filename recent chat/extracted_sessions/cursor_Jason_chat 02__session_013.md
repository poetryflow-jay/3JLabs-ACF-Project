---

**User**

커밋해주고 다음 라운드를 진행해주게. 다음 라운드 작업까지 마친 다음에, 플러그인들을 자네가 안내하는 순서에 따라 재설치하고 테스트 후 전반적인 피드백과 디테일한 피드백을 (개선점 위주, 추가 기능과 삭제해야 할 문구나 버튼, 수정해야 할 표현이나 단어 등과 함께) 주겠네. 제이슨, 이번 작업도 잘 부탁하네. 자네에게 전권을 위임하는 바일세, 우리 3J Labs의 CTO인 제이슨 자네에게 내 믿음과 권한을 부여하네. - 사장 이재인(J a.k.a Jay Lee)


---

**Cursor**

좋습니다 대표님. 방금 커밋까지는 완료했으니, **다음 라운드**는 (1) Updates 탭 통합 테이블을 WP 플러그인 목록 수준으로 더 실전적으로(마지막 체크/다음 체크/활성·비활성 액션/필수 플러그인 상태) 다듬고 (2) Self-test가 누락/로드 오류를 “원인 파일까지” 바로 보여주도록 확장한 뒤, (3) 커밋 + 배포 갱신까지 한 번에 마무리하겠습니다.

좋습니다. 먼저 **Updates 통합 테이블을 WP 플러그인 목록처럼 ‘상태/액션/체크 시각’까지 완성**하고, 이어서 **Self-test가 “어느 파일이 문제인지” 바로 찍어주게** 확장한 뒤 커밋/배포까지 묶어서 진행하겠습니다.

좋습니다. 이번 라운드는 “Updates 탭 통합 테이블을 WP 플러그인 목록 수준으로 마감(검색/액션/체크 시각/필수 플러그인 상태)”하고, 동시에 “Self-test가 원인 파일을 바로 보여주도록” 확장한 뒤 커밋/배포까지 묶어서 끝내겠습니다.

```php
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
        ?>

        <h2 style="margin-top: 0;"><?php esc_html_e( '업데이트 개요 (코어 + 애드온)', 'jj-style-guide' ); ?></h2>
        <p class="description"><?php esc_html_e( 'WordPress 플러그인 목록 UX처럼, 설치/활성/업데이트/자동 업데이트 상태를 한 번에 확인합니다.', 'jj-style-guide' ); ?></p>

        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-top: 10px;">
            <input type="search"
                   id="jj-updates-suite-search"
                   class="regular-text"
                   style="min-width: 260px;"
                   placeholder="<?php echo esc_attr__( '플러그인 검색…', 'jj-style-guide' ); ?>" />
            <label style="display:inline-flex; gap:6px; align-items:center;">
                <input type="checkbox" id="jj-updates-suite-hide-uninstalled" />
                <?php esc_html_e( '미설치 숨기기', 'jj-style-guide' ); ?>
            </label>
            <label style="display:inline-flex; gap:6px; align-items:center;">
                <input type="checkbox" id="jj-updates-suite-only-updates" />
                <?php esc_html_e( '업데이트만 보기', 'jj-style-guide' ); ?>
            </label>
            <span class="description" style="margin-left:auto;">
                <?php
                printf(
                    /* translators: 1: last checked 2: next check */
                    esc_html__( '마지막 체크: %1$s · 다음 체크: %2$s', 'jj-style-guide' ),
                    esc_html( $last_checked_human ),
                    esc_html( $next_check_human )
                );
                ?>
                · <a href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>"><?php esc_html_e( '워드프레스 업데이트', 'jj-style-guide' ); ?></a>
            </span>
        </div>

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
                        data-name="<?php echo esc_attr( strtolower( $name ) ); ?>"
                        data-installed="<?php echo $installed ? '1' : '0'; ?>"
                        data-has-update="<?php echo $has_update ? '1' : '0'; ?>">
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
                                <?php echo $active ? esc_html__( 'ACTIVE', 'jj-style-guide' ) : esc_html__( 'INACTIVE', 'jj-style-guide' ); ?>
                            </span>
                            <?php if ( $activate_url ) : ?>
                                <div style="margin-top: 8px;">
                                    <a class="button button-small button-primary" href="<?php echo esc_url( $activate_url ); ?>">
                                        <?php esc_html_e( '활성화', 'jj-style-guide' ); ?>
                                    </a>
                                </div>
                            <?php elseif ( $deactivate_url ) : ?>
                                <div style="margin-top: 8px;">
                                    <a class="button button-small" href="<?php echo esc_url( $deactivate_url ); ?>">
                                        <?php esc_html_e( '비활성화', 'jj-style-guide' ); ?>
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


```

```javascript
/**
 * J&J Admin Center JavaScript - v4.0.2
 * 
 * 탭 기반 인터페이스와 2패널 레이아웃 제어
 * - 탭 전환 기능
 * - 2패널 레이아웃: 메뉴 아이템 선택 및 상세 정보 표시
 * - 색상 미리보기 업데이트
 * - 사이드바 액션 버튼 처리
 * - AJAX 기반 설정 저장/불러오기
 * 
 * @since 3.7.0
 * @version 4.0.2
 * 
 * [v4.0.2 변경사항]
 * - [CRITICAL FIX] 저장 기능 완전 수정
 *   - AJAX 핸들러 추가로 저장 기능 작동 보장
 *   - FormData 대신 jQuery serialize() 사용
 *   - 메뉴 순서 데이터 명시적 수집 및 업데이트
 * 
 * [v4.0.1 변경사항]
 * - 드래그앤드롭 초기화 로직 대폭 개선
 *   - 드래그 핸들 자동 생성 기능 추가
 *   - 초기화 검증 및 자동 재시도 메커니즘 강화
 *   - 이벤트 핸들링 개선 (다른 스크립트와의 충돌 방지)
 *   - 상세한 디버깅 로그 추가
 *   - 탭 전환 시 초기화 로직 개선
 *   - 재시도 횟수 증가 (20 → 30)
 *   - 터치 이벤트 지원 추가
 * 
 * [v6.3.0 추가]
 * - 왼쪽 고정 사이드바 네비게이션 제어
 * - 색상 히스토리 관리 (localStorage)
 * - 변경사항 미리보기 모달 시스템
 * - 모바일 최적화 (스와이프, 햄버거 메뉴)
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const $wrap = $('.jj-admin-center-wrap');

        // [v5.6.0] 미디어 업로드 버튼 핸들러
        $wrap.on('click', '.jj-upload-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const target = $btn.data('target');
            const $targetInput = $(target);
            
            let frame = wp.media({
                title: '이미지 선택',
                multiple: false,
                library: { type: 'image' },
                button: { text: '선택' }
            });
            
            frame.on('select', function() {
                const attachment = frame.state().get('selection').first().toJSON();
                $targetInput.val(attachment.url);
            });
            
            frame.open();
        });

        // [v5.7.0] AI Smart Palette Generator
        $wrap.on('click', '#jj-btn-generate-palette', function(e) {
            e.preventDefault();
            const baseColor = $('#jj-ai-base-color').val();
            const harmony = $('#jj-ai-harmony').val();
            const $resultDiv = $('#jj-ai-palette-result');
            const $chipsDiv = $resultDiv.find('.jj-ai-color-chips');
            
            if (!baseColor) {
                alert('색상을 선택하세요.');
                return;
            }
            
            $chipsDiv.html('<span class="spinner is-active" style="float:none;"></span>');
            $resultDiv.show();
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_generate_smart_palette',
                    nonce: jjAdminCenter.nonce,
                    base_color: baseColor,
                    harmony: harmony
                },
                success: function(response) {
                    if (response.success) {
                        const palette = response.data.palette.palette; // 배열
                        let html = '';
                        palette.forEach(function(color) {
                            html += '<div class="jj-ai-chip" style="width:40px; height:40px; background:' + color + '; border-radius:4px; cursor:pointer;" title="' + color + '" data-color="' + color + '"></div>';
                        });
                        $chipsDiv.html(html);
                        
                        // 적용하기 버튼 데이터 바인딩
                        $('#jj-btn-apply-ai-palette').data('palette', response.data.palette);
                    } else {
                        $chipsDiv.html('<span style="color:red;">' + response.data.message + '</span>');
                    }
                },
                error: function() {
                    $chipsDiv.html('<span style="color:red;">서버 오류가 발생했습니다.</span>');
                }
            });
        });
        
        // 팔레트 적용하기 버튼
        $wrap.on('click', '#jj-btn-apply-ai-palette', function(e) {
            e.preventDefault();
            const paletteData = $(this).data('palette');
            if (!paletteData) return;
            
            // Primary
            $('input[name*="[primary_color]"]').first().val(paletteData.primary).trigger('change');
            // Secondary
            $('input[name*="[secondary_color]"]').first().val(paletteData.secondary).trigger('change');
            
            alert('팔레트가 적용되었습니다. 저장 버튼을 눌러 확정하세요.');
        });

        // [v5.8.0] Cloud Export
        $wrap.on('click', '#jj-btn-cloud-export', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $result = $('#jj-cloud-export-result');
            
            $btn.prop('disabled', true).text('저장 중...');
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_cloud_export',
                    nonce: jjAdminCenter.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $result.find('.jj-code-box').text(response.data.share_code);
                        $result.slideDown();
                        alert('클라우드에 저장되었습니다. 공유 코드를 확인하세요.');
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('서버 통신 오류가 발생했습니다.');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('지금 저장하기');
                }
            });
        });

        // [v5.8.0] Cloud Import
        $wrap.on('click', '#jj-btn-cloud-import', function(e) {
            e.preventDefault();
            const code = $('#jj-cloud-share-code').val().trim();
            if (!code) {
                alert('공유 코드를 입력하세요.');
                return;
            }
            
            if (!confirm('현재 설정이 덮어씌워집니다. 계속하시겠습니까?')) {
                return;
            }
            
            const $btn = $(this);
            $btn.prop('disabled', true).text('불러오는 중...');
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_cloud_import',
                    nonce: jjAdminCenter.nonce,
                    share_code: code
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('서버 통신 오류가 발생했습니다.');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('불러오기');
                }
            });
        });

        // [v3.8.0 신규] 라이센스 관리 기능
        $('#jj-license-key-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $('#jj-save-license-btn');
            const $spinner = $('.jj-license-spinner');
            const $message = $('#jj-license-message');
            const licenseKey = $('#jj-license-key-input').val().trim();

            if (!licenseKey) {
                showLicenseMessage('error', '라이센스 키를 입력하세요.');
                return;
            }

            $btn.prop('disabled', true);
            $spinner.show();
            $message.hide();
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_save_license_key',
                    nonce: jjAdminCenter.nonce,
                    license_key: licenseKey
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    
                    if (response.success) {
                        showLicenseMessage('success', response.data.message);
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        showLicenseMessage('error', response.data.message || '라이센스 키 저장에 실패했습니다.');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    showLicenseMessage('error', '오류가 발생했습니다.');
                }
            });
        });
        
        // 라이센스 검증 버튼 클릭
        $('#jj-verify-license-btn').on('click', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $spinner = $('.jj-license-spinner');
            const $message = $('#jj-license-message');
            
            $btn.prop('disabled', true);
            $spinner.show();
            $message.hide();
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_verify_license_key',
                    nonce: jjAdminCenter.nonce
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    
                    if (response.success) {
                        const status = response.data.status;
                        let message = response.data.message;
                        
                        if (status.valid) {
                            showLicenseMessage('success', message);
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            showLicenseMessage('error', message);
                        }
                    } else {
                        showLicenseMessage('error', response.data.message || '라이센스 검증에 실패했습니다.');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    showLicenseMessage('error', '오류가 발생했습니다.');
                }
            });
        });
        
        // 라이센스 키 제거 버튼 클릭
        $('#jj-remove-license-btn').on('click', function(e) {
            e.preventDefault();
            if (!confirm('정말로 라이센스 키를 제거하시겠습니까? Free 버전으로 실행됩니다.')) {
                return;
            }
            
            const $btn = $(this);
            const $spinner = $('.jj-license-spinner');
            const $message = $('#jj-license-message');
            
            $btn.prop('disabled', true);
            $spinner.show();
            $message.hide();
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_save_license_key',
                    nonce: jjAdminCenter.nonce,
                    license_key: '' 
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    
                    showLicenseMessage('success', '라이센스 키가 제거되었습니다. Free 버전으로 실행됩니다.');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function() {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    showLicenseMessage('error', '오류가 발생했습니다.');
                }
            });
        });
        
        // 라이센스 키 복사 버튼
        $('.jj-copy-license-key-display').on('click', function(e) {
            e.preventDefault();
            const licenseKey = $(this).data('license-key');
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(licenseKey).select();
            document.execCommand('copy');
            $temp.remove();
            
            const $btn = $(this);
            const originalText = $btn.html();
            $btn.html('<span class="dashicons dashicons-yes" style="font-size: 12px; margin-top: 2px; color: #00a32a;"></span> 복사됨');
            
            setTimeout(function() {
                $btn.html(originalText);
            }, 2000);
        });
        
        function showLicenseMessage(type, message) {
            const $message = $('#jj-license-message');
            $message.removeClass('success error')
                    .addClass(type)
                    .html(message)
                    .fadeIn(300);
            
            setTimeout(function() {
                $message.fadeOut(300);
            }, 5000);
        }

        if (!$wrap.length) {
            return;
        }

        // [v6.3.0] 탭 전환 공통 함수
        function switchTab(tabId) {
            $wrap.find('.jj-admin-center-tabs li').removeClass('active');
            $wrap.find('.jj-admin-center-tabs li[data-tab="' + tabId + '"]').addClass('active');
            
            $wrap.find('.jj-admin-center-sidebar-nav a').removeClass('active');
            $wrap.find('.jj-admin-center-sidebar-nav a[data-tab="' + tabId + '"]').addClass('active');

            $wrap.find('.jj-admin-center-tab-content').removeClass('active');
            $wrap.find('.jj-admin-center-tab-content[data-tab="' + tabId + '"]').addClass('active');
            
            if (history.pushState) {
                history.pushState(null, null, '#' + tabId);
            } else {
                window.location.hash = tabId;
            }
        }

        $wrap.on('click', '.jj-admin-center-tabs a', function(e) {
            e.preventDefault();
            const $tab = $(this).closest('li');
            const tabId = $tab.data('tab');
            switchTab(tabId);
            
            if (tabId === 'admin-menu') {
                let tabInitRetryCount = 0;
                const tabInitMaxRetries = 10;
                const initSortableOnTabSwitch = function() {
                    const $menuList = $wrap.find('.jj-admin-center-menu-list');
                    const hasMenuItems = $menuList.length && $menuList.find('.jj-admin-center-menu-item').length > 0;
                    
                    if ($.fn.sortable && hasMenuItems) {
                        requestAnimationFrame(function() {
                            setTimeout(function() {
                                initializeSortable();
                            }, 100);
                        });
                    } else if (tabInitRetryCount < tabInitMaxRetries) {
                        tabInitRetryCount++;
                        setTimeout(initSortableOnTabSwitch, 200);
                    }
                };
                setTimeout(initSortableOnTabSwitch, 200);
            }
            
            if (tabId === 'colors' || tabId === 'visual') {
                setTimeout(function() {
                    initColorPickers();
                    loadPaletteChips(); 
                    initColorHistory(); 
                }, 200);
            }
        });

        let isDragging = false;
        let dragStartTime = 0;
        let dragStartPosition = null;
        
        $wrap.on('mousedown', '.jj-admin-center-menu-item', function(e) {
            if ($(e.target).closest('.jj-admin-center-menu-item-handle').length) {
                isDragging = false;
                dragStartTime = Date.now();
                dragStartPosition = { x: e.pageX, y: e.pageY };
                return true; 
            }
            isDragging = false;
            dragStartTime = 0;
            dragStartPosition = null;
        });
        
        $wrap.on('click', '.jj-admin-center-menu-item', function(e) {
            if ($(e.target).closest('.jj-toggle-submenu, .jj-admin-center-menu-item-handle').length) {
                return;
            }
            
            if (isDragging || (dragStartTime > 0 && Date.now() - dragStartTime < 300 && dragStartPosition)) {
                const currentPosition = { x: e.pageX, y: e.pageY };
                const distance = Math.sqrt(
                    Math.pow(currentPosition.x - dragStartPosition.x, 2) + 
                    Math.pow(currentPosition.y - dragStartPosition.y, 2)
                );
                if (distance > 5) return;
            }
            
            const $item = $(this);
            const itemId = $item.data('item-id');

            $wrap.find('.jj-admin-center-menu-item').removeClass('active');
            $item.addClass('active');

            loadMenuItemDetails(itemId);
        });

        function initColorPickers() {
            $wrap.find('.jj-admin-center-color-picker').each(function() {
                const $input = $(this);
                if ($input.data('wpColorPicker')) {
                    try {
                        $input.wpColorPicker('destroy');
                    } catch (e) {}
                }
                
                $input.wpColorPicker({
                    change: function(event, ui) {
                        const color = ui.color.toString();
                        $input.val(color).trigger('change');
                        updateColorPreview($input);
                    },
                    clear: function(event) {
                        $input.val('').trigger('change');
                        updateColorPreview($input);
                    }
                });
            });
        }

        function updateColorPreview($input) {
            const $preview = $input.closest('.jj-admin-center-color-input').find('.jj-admin-center-color-preview');
            const color = $input.val();

            if (color && /^#[0-9A-Fa-f]{6}$/.test(color)) {
                $preview.css('background-color', color);
                const $paletteChips = $input.closest('.jj-admin-center-color-input').find('.jj-palette-chip-inline');
                $paletteChips.css({
                    'border-color': '#ddd',
                    'border-width': '2px'
                });
                $paletteChips.filter('[data-color="' + color + '"]').css({
                    'border-color': '#2271b1',
                    'border-width': '3px'
                });
            } else {
                $preview.css('background-color', 'transparent');
            }
        }

        $wrap.on('input change', '.jj-admin-center-color-input input[type="text"]', function() {
            const $input = $(this);
            updateColorPreview($input);
            if ($input.data('wpColorPicker')) {
                try {
                    $input.wpColorPicker('color', $input.val());
                } catch (e) {}
            }
        });

        $wrap.find('.jj-admin-center-color-input input[type="text"]').each(function() {
            updateColorPreview($(this));
        });
        
        const COLOR_HISTORY_KEY = 'jj_color_history';
        const MAX_HISTORY_SIZE = 20;

        function getColorHistory() {
            try {
                const history = localStorage.getItem(COLOR_HISTORY_KEY);
                return history ? JSON.parse(history) : [];
            } catch (e) { return []; }
        }

        function saveColorToHistory(color) {
            if (!color || !/^#[0-9A-Fa-f]{6}$/.test(color)) return;
            let history = getColorHistory();
            history = history.filter(c => c !== color);
            history.unshift(color);
            if (history.length > MAX_HISTORY_SIZE) history = history.slice(0, MAX_HISTORY_SIZE);
            try {
                localStorage.setItem(COLOR_HISTORY_KEY, JSON.stringify(history));
            } catch (e) {}
        }

        function renderColorHistory($input) {
            const history = getColorHistory();
            const $historyContainer = $input.closest('.jj-admin-center-color-input').find('.jj-color-history');
            const $chipsContainer = $historyContainer.find('.jj-color-history-chips');
            
            if (history.length === 0) {
                $historyContainer.hide();
                return;
            }
            
            $historyContainer.show();
            $chipsContainer.empty();
            
            history.forEach(function(color) {
                const $chip = $('<div>')
                    .addClass('jj-color-history-chip')
                    .css('background-color', color)
                    .attr('data-color', color)
                    .attr('title', color)
                    .on('click', function() {
                        $input.val(color).trigger('change');
                        updateColorPreview($input);
                        if ($input.data('wpColorPicker')) {
                            try { $input.wpColorPicker('color', color); } catch (e) {}
                        }
                    });
                $chipsContainer.append($chip);
            });
        }

        function initColorHistory() {
            $wrap.find('.jj-admin-center-color-picker').each(function() {
                renderColorHistory($(this));
            });
        }

        $wrap.on('change', '.jj-admin-center-color-picker', function() {
            const color = $(this).val();
            if (color && /^#[0-9A-Fa-f]{6}$/.test(color)) {
                saveColorToHistory(color);
                renderColorHistory($(this));
            }
        });

        $wrap.on('click', '.jj-admin-center-color-preview', function(e) {
            e.preventDefault();
            const $input = $(this).closest('.jj-admin-center-color-input').find('.jj-admin-center-color-picker');
            if ($input.length && $input.data('wpColorPicker')) {
                $input.closest('.wp-picker-container').find('.wp-color-result').trigger('click');
            }
        });

        if ($wrap.find('.jj-admin-center-tab-content[data-tab="colors"]').hasClass('active')) {
            setTimeout(function() {
                initColorPickers();
                loadPaletteChips();
                initColorHistory();
            }, 200);
        }
        
        function loadPaletteChips() {
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_get_palette_data',
                    nonce: jjAdminCenter.nonce
                },
                success: function(response) {
                    if (response.success && response.data.palettes) {
                        const palettes = response.data.palettes;
                        $wrap.find('.jj-admin-center-palette-chips').each(function() {
                            const $container = $(this);
                            const $targetInput = $container.closest('.jj-admin-center-color-input').find('.jj-admin-center-color-picker').first();
                            const $chipsContainer = $container.find('.jj-palette-chips-container');
                            $chipsContainer.empty();
                            
                            Object.keys(palettes).forEach(function(paletteKey) {
                                const palette = palettes[paletteKey];
                                const colors = palette.colors || {};
                                Object.keys(colors).forEach(function(colorKey) {
                                    const color = colors[colorKey];
                                    if (color && /^#[0-9A-Fa-f]{6}$/.test(color)) {
                                        const $chip = $('<div class="jj-palette-chip-inline" style="background: ' + color + '" data-color="' + color + '"></div>');
                                        $chip.on('click', function() {
                                            const selectedColor = $(this).data('color');
                                            $targetInput.val(selectedColor).trigger('change');
                                            if ($targetInput.data('wpColorPicker')) {
                                                try { $targetInput.wpColorPicker('color', selectedColor); } catch (e) {}
                                            }
                                            updateColorPreview($targetInput);
                                        });
                                        $chipsContainer.append($chip);
                                    }
                                });
                            });
                        });
                    }
                }
            });
        }

        function collectChanges() {
            const changes = [];
            const $form = $('#jj-admin-center-form');
            
            $form.find('.jj-admin-center-color-picker').each(function() {
                const $input = $(this);
                const newValue = $input.val();
                const oldValue = $input.data('original-value') || '';
                if (newValue !== oldValue && newValue) {
                    changes.push({ label: $input.closest('tr').find('th').text().trim(), oldValue: oldValue || '(비어있음)', newValue: newValue });
                }
            });
            
            $form.find('input[type="text"]:not(.jj-admin-center-color-picker), textarea').each(function() {
                const $input = $(this);
                const name = $input.attr('name');
                if (!name || name.includes('_nonce')) return;
                const newValue = $input.val();
                const oldValue = $input.data('original-value') || '';
                if (newValue !== oldValue) {
                    changes.push({ label: $input.closest('tr').find('th').text().trim() || name, oldValue: oldValue || '(비어있음)', newValue: newValue || '(비어있음)' });
                }
            });
            
            return changes;
        }

        function showChangesPreview(changes) {
            const $modal = $('#jj-changes-preview-modal');
            const $list = $('#jj-changes-list');
            if (changes.length === 0) {
                $list.html('<p>변경된 내용이 없습니다.</p>');
            } else {
                let html = '';
                changes.forEach(function(change) {
                    html += '<div class="jj-change-item"><div class="jj-change-item-title">' + change.label + '</div>';
                    html += '<div class="jj-change-item-detail">이전: <span class="jj-change-old-value">' + change.oldValue + '</span> -> 변경: <span class="jj-change-new-value">' + change.newValue + '</span></div></div>';
                });
                $list.html(html);
            }
            $modal.fadeIn(200);
        }

        function executeSave() {
            const $form = $('#jj-admin-center-form');
            const $submitBtn = $form.find('button[type="submit"]');
            $submitBtn.prop('disabled', true);
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: $form.serialize() + '&action=jj_admin_center_save&security=' + jjAdminCenter.nonce,
                success: function(response) {
                    if (response.success) {
                        $('#jj-changes-preview-modal').fadeOut(200);
                        location.reload();
                    } else { alert(response.data.message); }
                },
                complete: function() { $submitBtn.prop('disabled', false); }
            });
        }

        $wrap.on('click', '.jj-modal-confirm-save', executeSave);
        $wrap.on('click', '.jj-modal-close, .jj-modal-cancel', function() { $('#jj-changes-preview-modal').fadeOut(200); });

        $('#jj-admin-center-form').on('submit', function(e) {
            e.preventDefault();
            showChangesPreview(collectChanges());
        });

        // ============================================================
        // Updates Tab (WP Plugins screen UX-aligned)
        // ============================================================
        function updateAutoUpdateBadge(isEnabled) {
            const $badge = $('.jj-admin-center-tab-content[data-tab="updates"]').find('.jj-status-badge').first();
            if (!$badge.length) return;
            $badge.toggleClass('active', !!isEnabled);
            $badge.toggleClass('inactive', !isEnabled);
            $badge.text(isEnabled ? 'AUTO UPDATE: ON' : 'AUTO UPDATE: OFF');
        }

        function syncAutoUpdateToggleUi() {
            const enabled = $('#jj_auto_update_enabled').is(':checked');
            const $btn = $('#jj-toggle-auto-update');
            if ($btn.length) {
                $btn.attr('data-enabled', enabled ? '1' : '0');
                $btn.text(enabled ? '비활성화' : '활성화');
            }
            updateAutoUpdateBadge(enabled);
        }

        // 코어 + 애드온(Updates Overview 테이블) 자동 업데이트 토글
        function setSuiteRowAutoUpdate($row, enabled) {
            const $badge = $row.find('.jj-suite-auto-badge').first();
            const $btn = $row.find('.jj-suite-toggle-auto-update').first();
            if ($badge.length) {
                $badge.toggleClass('active', !!enabled);
                $badge.toggleClass('inactive', !enabled);
                $badge.text(enabled ? 'AUTO UPDATE: ON' : 'AUTO UPDATE: OFF');
            }
            if ($btn.length) {
                $btn.attr('data-enabled', enabled ? '1' : '0');
                $btn.text(enabled ? '비활성화' : '활성화');
            }
        }

        $wrap.on('click', '.jj-suite-toggle-auto-update', function(e) {
            e.preventDefault();
            if (typeof jjAdminCenter === 'undefined' || !jjAdminCenter.ajax_url || !jjAdminCenter.nonce) return;

            const $btn = $(this);
            const $row = $btn.closest('.jj-suite-row');
            const plugin = $btn.attr('data-plugin') || '';
            const enabledNow = ($btn.attr('data-enabled') === '1');
            const target = !enabledNow;

            if (!plugin) return;

            $btn.prop('disabled', true);
            $.post(jjAdminCenter.ajax_url, {
                action: 'jj_toggle_auto_update_plugin',
                security: jjAdminCenter.nonce,
                plugin: plugin,
                enabled: target ? '1' : '0'
            }).done(function(resp) {
                if (resp && resp.success) {
                    setSuiteRowAutoUpdate($row, !!(resp.data && resp.data.enabled));
                    // 코어 토글이면 기존 UI도 함께 동기화
                    if (plugin.indexOf('acf-css-really-simple-style-guide.php') !== -1) {
                        $('#jj_auto_update_enabled').prop('checked', !!(resp.data && resp.data.enabled));
                        syncAutoUpdateToggleUi();
                    }
                } else {
                    const msg = resp && resp.data && resp.data.message ? resp.data.message : '자동 업데이트 토글에 실패했습니다.';
                    alert(msg);
                }
            }).fail(function() {
                alert('서버 통신 오류가 발생했습니다.');
            }).always(function() {
                $btn.prop('disabled', false);
            });
        });

        // Updates Overview: 검색/필터(미설치 숨김, 업데이트만)
        function filterUpdatesSuiteRows() {
            const q = ($('#jj-updates-suite-search').val() || '').toLowerCase().trim();
            const hideUninstalled = $('#jj-updates-suite-hide-uninstalled').is(':checked');
            const onlyUpdates = $('#jj-updates-suite-only-updates').is(':checked');

            $('.jj-suite-row').each(function() {
                const $row = $(this);
                const name = ($row.attr('data-name') || '').toLowerCase();
                const installed = ($row.attr('data-installed') === '1');
                const hasUpdate = ($row.attr('data-has-update') === '1');

                let visible = true;
                if (q && name.indexOf(q) === -1) visible = false;
                if (hideUninstalled && !installed) visible = false;
                if (onlyUpdates && !hasUpdate) visible = false;

                $row.toggle(visible);
            });
        }

        $wrap.on('input', '#jj-updates-suite-search', filterUpdatesSuiteRows);
        $wrap.on('change', '#jj-updates-suite-hide-uninstalled, #jj-updates-suite-only-updates', filterUpdatesSuiteRows);
        filterUpdatesSuiteRows();

        // 토글 버튼: 체크박스 상태만 즉시 변경 (저장은 별도)
        $wrap.on('click', '#jj-toggle-auto-update', function(e) {
            e.preventDefault();
            const $checkbox = $('#jj_auto_update_enabled');
            if (!$checkbox.length || $checkbox.is(':disabled')) return;
            $checkbox.prop('checked', !$checkbox.is(':checked')).trigger('change');
            syncAutoUpdateToggleUi();
        });

        $wrap.on('change', '#jj_auto_update_enabled', function() {
            syncAutoUpdateToggleUi();
        });

        // 업데이트 설정 저장
        $wrap.on('click', '#jj-save-update-settings', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $status = $('#jj-update-status');

            const payload = {
                action: 'jj_save_update_settings',
                security: jjAdminCenter.nonce,
                update_settings: {
                    auto_update_enabled: $('#jj_auto_update_enabled').is(':checked') ? '1' : '0',
                    update_channel: $('#jj_update_channel').val() || 'stable',
                    beta_updates_enabled: $('#jj_beta_updates_enabled').is(':checked') ? '1' : '0',
                    send_app_logs: $('#jj_send_app_logs').is(':checked') ? '1' : '0',
                    send_error_logs: $('#jj_send_error_logs').is(':checked') ? '1' : '0',
                }
            };

            $btn.prop('disabled', true);
            $status.html('<span class="spinner is-active" style="float:none; margin:0 6px 0 0;"></span> 저장 중...');

            $.post(jjAdminCenter.ajax_url, payload)
                .done(function(resp) {
                    if (resp && resp.success) {
                        $status.html('<div class="notice notice-success inline"><p><strong>저장 완료:</strong> 업데이트 설정이 저장되었습니다.</p></div>');
                    } else {
                        const msg = resp && resp.data && resp.data.message ? resp.data.message : '저장에 실패했습니다.';
                        $status.html('<div class="notice notice-error inline"><p><strong>오류:</strong> ' + msg + '</p></div>');
                    }
                })
                .fail(function() {
                    $status.html('<div class="notice notice-error inline"><p><strong>오류:</strong> 네트워크 오류가 발생했습니다.</p></div>');
                })
                .always(function() {
                    $btn.prop('disabled', false);
                    // UI 동기화
                    syncAutoUpdateToggleUi();
                });
        });

        // 지금 업데이트 확인
        $wrap.on('click', '#jj-check-updates-now', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $status = $('#jj-update-status');

            $btn.prop('disabled', true);
            $status.html('<span class="spinner is-active" style="float:none; margin:0 6px 0 0;"></span> 업데이트 확인 중...');

            $.post(jjAdminCenter.ajax_url, { action: 'jj_check_updates_now', security: jjAdminCenter.nonce })
                .done(function(resp) {
                    if (resp && resp.success) {
                        const d = resp.data || {};
                        if (d.has_update) {
                            $status.html('<div class="notice notice-warning inline"><p><strong>업데이트 가능:</strong> ' + (d.message || '') + '</p></div>');
                        } else {
                            $status.html('<div class="notice notice-success inline"><p><strong>최신 상태:</strong> ' + (d.message || '') + '</p></div>');
                        }
                    } else {
                        const msg = resp && resp.data && resp.data.message ? resp.data.message : '업데이트 확인에 실패했습니다.';
                        $status.html('<div class="notice notice-error inline"><p><strong>오류:</strong> ' + msg + '</p></div>');
                    }
                })
                .fail(function() {
                    $status.html('<div class="notice notice-error inline"><p><strong>오류:</strong> 네트워크 오류가 발생했습니다.</p></div>');
                })
                .always(function() {
                    $btn.prop('disabled', false);
                });
        });

        // 최초 1회 동기화
        syncAutoUpdateToggleUi();

        function loadMenuItemDetails(itemId) {
            $wrap.find('.jj-admin-center-item-details').removeClass('active');
            $wrap.find('.jj-admin-center-item-details[data-item-id="' + itemId + '"]').addClass('active');
        }

        function initializeSortable() {
            const $menuList = $wrap.find('.jj-admin-center-menu-list');
            if (!$menuList.length || !$.fn.sortable) return;
            $menuList.sortable({
                handle: '.jj-admin-center-menu-item-handle',
                placeholder: 'jj-sortable-placeholder',
                stop: function() { updateMenuOrder(); }
            });
        }

        function updateMenuOrder() {
            $wrap.find('.jj-admin-center-menu-list > .jj-admin-center-menu-item').each(function(index) {
                const order = index + 1;
                $(this).find('.jj-admin-center-menu-item-order').text(order);
                const itemId = $(this).data('item-id');
                $wrap.find('input[name="jj_admin_menu_layout[' + itemId + '][order]"]').val(order);
            });
        }

        // [Phase 6+] 시스템 상태: 자가 진단 실행 (AJAX)
        $wrap.on('click', '#jj-run-self-test', function(e) {
            e.preventDefault();
            if (typeof jjAdminCenter === 'undefined' || !jjAdminCenter.ajax_url || !jjAdminCenter.nonce) {
                alert('AJAX 설정이 초기화되지 않았습니다.');
                return;
            }

            const $btn = $(this);
            const $box = $('#jj-self-test-results');
            const $spinner = $box.find('.spinner');
            const $statusText = $box.find('.jj-test-status-text');
            const $list = $box.find('.jj-test-results-list');

            $box.show();
            $list.empty();
            $spinner.addClass('is-active').show();
            $statusText.text('진단 중...');
            $btn.prop('disabled', true);

            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_run_self_test',
                    security: jjAdminCenter.nonce
                }
            }).done(function(resp) {
                $spinner.removeClass('is-active').hide();

                if (!resp || !resp.success) {
                    const msg = (resp && resp.data && resp.data.message) ? resp.data.message : '자가 진단에 실패했습니다.';
                    $statusText.text('오류');
                    $list.append('<li style="padding:8px 10px; border:1px solid #d63638; border-radius:6px; background:#fcf0f1; color:#8a2424;">' + msg + '</li>');
                    return;
                }

                const results = (resp.data && Array.isArray(resp.data.results)) ? resp.data.results : [];
                if (!results.length) {
                    $statusText.text('완료');
                    $list.append('<li style="padding:8px 10px; border:1px solid #c3c4c7; border-radius:6px; background:#f6f7f7;">결과가 없습니다.</li>');
                    return;
                }

                let counts = { pass: 0, warn: 0, fail: 0, skip: 0 };
                results.forEach(function(r) {
                    const t = (r && r.test) ? String(r.test) : '(unknown)';
                    const s = (r && r.status) ? String(r.status) : 'warn';
                    const m = (r && r.message) ? String(r.message) : '';

                    if (counts[s] !== undefined) counts[s]++; else counts.warn++;

                    let color = '#2271b1';
                    let bg = '#f0f6fc';
                    let border = '#b6d1ea';
                    let label = 'INFO';

                    if (s === 'pass') { color = '#1d6b2f'; bg = '#edfaef'; border = '#b7e1c2'; label = 'PASS'; }
                    if (s === 'warn') { color = '#8a5a00'; bg = '#fff7e5'; border = '#f3d19e'; label = 'WARN'; }
                    if (s === 'fail') { color = '#8a2424'; bg = '#fcf0f1'; border = '#f0b6b6'; label = 'FAIL'; }
                    if (s === 'skip') { color = '#475569'; bg = '#f1f5f9'; border = '#cbd5e1'; label = 'SKIP'; }

                    const badge = '<span style="display:inline-block; min-width:46px; text-align:center; padding:2px 6px; margin-right:8px; border-radius:999px; font-weight:700; font-size:11px; border:1px solid ' + border + '; background:' + bg + '; color:' + color + ';">' + label + '</span>';
                    const item = '<li style="display:flex; gap:10px; align-items:flex-start; padding:8px 10px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; margin-bottom:6px;">'
                        + '<div style="flex:0 0 auto;">' + badge + '</div>'
                        + '<div style="flex:1 1 auto;">'
                        + '<div style="font-weight:700; color:#0f172a;">' + t + '</div>'
                        + (m ? ('<div style="font-size:12px; color:#64748b; margin-top:2px;">' + m + '</div>') : '')
                        + '</div>'
                        + '</li>';

                    $list.append(item);
                });

                $statusText.text('완료 (PASS ' + counts.pass + ' / WARN ' + counts.warn + ' / FAIL ' + counts.fail + ' / SKIP ' + counts.skip + ')');
            }).fail(function() {
                $spinner.removeClass('is-active').hide();
                $statusText.text('오류');
                $list.append('<li style="padding:8px 10px; border:1px solid #d63638; border-radius:6px; background:#fcf0f1; color:#8a2424;">서버 통신 오류가 발생했습니다.</li>');
            }).always(function() {
                $btn.prop('disabled', false);
            });
        });

        initializeSortable();
        $wrap.on('click', '.jj-admin-center-sidebar-nav a', function(e) {
            e.preventDefault();
            switchTab($(this).data('tab'));
        });

        $wrap.on('click', '.jj-sidebar-toggle', function() {
            $('.jj-admin-center-sidebar').toggleClass('jj-sidebar-open');
        });
    });
})(jQuery);

```

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Self Tester
 * 
 * 플러그인 자가 진단 및 테스트 실행기
 * 
 * @since v6.2.0
 */
class JJ_Self_Tester {

    public static function run_tests() {
        $results = array();

        // 0. Constants / Helpers
        $consts = array(
            'JJ_STYLE_GUIDE_VERSION',
            'JJ_STYLE_GUIDE_PATH',
            'JJ_STYLE_GUIDE_URL',
            'JJ_STYLE_GUIDE_OPTIONS_KEY',
        );
        foreach ( $consts as $c ) {
            $results[] = array(
                'test'    => "Constant: {$c}",
                'status'  => defined( $c ) ? 'pass' : 'fail',
                'message' => defined( $c ) ? 'Defined' : 'Not defined',
            );
        }

        $results[] = array(
            'test'    => 'Helper: jj_style_guide_text()',
            'status'  => function_exists( 'jj_style_guide_text' ) ? 'pass' : 'warn',
            'message' => function_exists( 'jj_style_guide_text' ) ? 'Available' : 'Missing (fallback recommended)',
        );

        // 1. Core Classes Check
        $core_classes = array(
            'JJ_Admin_Center',
            'JJ_Safe_Loader',
            'JJ_Edition_Controller',
            'JJ_CSS_Cache',
            'JJ_Options_Cache',
            'JJ_CSS_Injector',
            'JJ_Smart_Palette', // AI
            'JJ_Cloud_Manager', // Cloud
            'JJ_Visual_Command_Center', // Visual
            'JJ_Partner_Hub', // Partner (Optional)
        );

        foreach ( $core_classes as $class ) {
            if ( class_exists( $class ) ) {
                $results[] = array( 'test' => "Class: $class", 'status' => 'pass', 'message' => 'Loaded successfully' );
            } else {
                // Partner Hub is optional depending on edition
                if ( $class === 'JJ_Partner_Hub' ) {
                    if ( class_exists( 'JJ_Edition_Controller' ) && method_exists( 'JJ_Edition_Controller', 'instance' ) ) {
                        $license_type = JJ_Edition_Controller::instance()->get_license_type();
                        if ( $license_type !== 'PARTNER' && $license_type !== 'MASTER' ) {
                            $results[] = array( 'test' => "Class: $class", 'status' => 'skip', 'message' => 'Skipped (Edition mismatch)' );
                        } else {
                            $results[] = array( 'test' => "Class: $class", 'status' => 'fail', 'message' => 'Class not found' );
                        }
                    } else {
                        $results[] = array( 'test' => "Class: $class", 'status' => 'warn', 'message' => 'Edition controller missing (cannot decide optionality)' );
                    }
                } else {
                    $results[] = array( 'test' => "Class: $class", 'status' => 'fail', 'message' => 'Class not found' );
                }
            }
        }

        // 1.5 Safe Loader Diagnostics (missing/failed requires)
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'get_missing_files' ) ) {
            $missing = (array) JJ_Safe_Loader::get_missing_files();
            $req = array();
            $opt = array();
            foreach ( $missing as $m ) {
                if ( ! is_array( $m ) ) continue;
                if ( ! empty( $m['required'] ) ) {
                    $req[] = $m;
                } else {
                    $opt[] = $m;
                }
            }

            $results[] = array(
                'test'    => 'Safe Loader: Required files',
                'status'  => empty( $req ) ? 'pass' : 'fail',
                'message' => empty( $req )
                    ? 'No missing required files'
                    : ( 'Missing required files: ' . count( $req ) ),
            );

            $results[] = array(
                'test'    => 'Safe Loader: Optional files',
                'status'  => empty( $opt ) ? 'pass' : 'warn',
                'message' => empty( $opt )
                    ? 'No missing optional files'
                    : ( 'Missing optional files: ' . count( $opt ) ),
            );

            // required 누락은 "원인 파일"을 바로 볼 수 있도록 개별 항목으로 출력(최대 30개)
            if ( ! empty( $req ) ) {
                $max = 30;
                $n = 0;
                foreach ( $req as $r ) {
                    if ( $n >= $max ) {
                        $results[] = array(
                            'test'    => 'Safe Loader: Required files (more)',
                            'status'  => 'fail',
                            'message' => 'Too many missing required files. (truncated)',
                        );
                        break;
                    }
                    $path = isset( $r['path'] ) ? (string) $r['path'] : '';
                    $short = $path;
                    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) && '' !== $path ) {
                        $short = str_replace( JJ_STYLE_GUIDE_PATH, '', $path );
                        $short = ltrim( $short, "/\\" );
                    }
                    $count = isset( $r['count'] ) ? (int) $r['count'] : 1;
                    $results[] = array(
                        'test'    => 'Missing required file: ' . $short,
                        'status'  => 'fail',
                        'message' => 'Not found (count=' . $count . ')',
                    );
                    $n++;
                }
            }
        } else {
            $results[] = array(
                'test'    => 'Safe Loader: Diagnostics',
                'status'  => 'warn',
                'message' => 'Safe loader diagnostics not available',
            );
        }

        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'get_load_errors' ) ) {
            $errs = (array) JJ_Safe_Loader::get_load_errors();
            $results[] = array(
                'test'    => 'Safe Loader: Load errors',
                'status'  => empty( $errs ) ? 'pass' : 'fail',
                'message' => empty( $errs ) ? 'No load errors' : ( 'Load errors detected: ' . count( $errs ) ),
            );

            if ( ! empty( $errs ) ) {
                $max = 30;
                $n = 0;
                foreach ( $errs as $e ) {
                    if ( ! is_array( $e ) ) continue;
                    if ( $n >= $max ) {
                        $results[] = array(
                            'test'    => 'Safe Loader: Load errors (more)',
                            'status'  => 'fail',
                            'message' => 'Too many load errors. (truncated)',
                        );
                        break;
                    }
                    $path = isset( $e['path'] ) ? (string) $e['path'] : '';
                    $short = $path;
                    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) && '' !== $path ) {
                        $short = str_replace( JJ_STYLE_GUIDE_PATH, '', $path );
                        $short = ltrim( $short, "/\\" );
                    }
                    $msg = isset( $e['error'] ) ? (string) $e['error'] : 'Unknown error';
                    if ( function_exists( 'mb_substr' ) ) {
                        $msg = mb_substr( $msg, 0, 220 );
                    } else {
                        $msg = substr( $msg, 0, 220 );
                    }
                    $results[] = array(
                        'test'    => 'Load error: ' . $short,
                        'status'  => 'fail',
                        'message' => $msg,
                    );
                    $n++;
                }
            }
        }

        // 1.6 File Manifest (critical templates/views)
        $manifest = array(
            // Admin Center tabs
            'includes/admin/views/tabs/tab-updates.php'       => true,
            'includes/admin/views/tabs/tab-system-status.php' => true,
            // Editor views
            'includes/editor-views/view-section-colors.php'   => true,
            // Presets assets
            'assets/js/jj-style-guide-presets.js'             => true,
            'assets/css/jj-style-guide-presets.css'           => true,
        );
        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            foreach ( $manifest as $rel => $required ) {
                $path = JJ_STYLE_GUIDE_PATH . ltrim( $rel, '/\\' );
                $exists = file_exists( $path );
                $results[] = array(
                    'test'    => 'File: ' . $rel,
                    'status'  => $exists ? 'pass' : ( $required ? 'fail' : 'warn' ),
                    'message' => $exists ? 'OK' : 'Missing',
                );
            }
        }

        // 2. Directory Permissions
        $dirs = array(
            JJ_STYLE_GUIDE_PATH . 'assets/css/generated' => 'is_writable',
        );

        foreach ( $dirs as $path => $check ) {
            if ( file_exists( $path ) ) {
                if ( is_writable( $path ) ) {
                    $results[] = array( 'test' => "Dir: " . basename( $path ), 'status' => 'pass', 'message' => 'Writable' );
                } else {
                    $results[] = array( 'test' => "Dir: " . basename( $path ), 'status' => 'warn', 'message' => 'Not writable' );
                }
            } else {
                $results[] = array( 'test' => "Dir: " . basename( $path ), 'status' => 'warn', 'message' => 'Not found (Auto-created on save)' );
            }
        }

        // 3. Option Integrity
        $options = get_option( 'jj_style_guide_options' );
        if ( is_array( $options ) ) {
            $results[] = array( 'test' => "Options Integrity", 'status' => 'pass', 'message' => 'Valid Array' );
        } else {
            $results[] = array( 'test' => "Options Integrity", 'status' => 'fail', 'message' => 'Corrupted or Missing' );
        }

        return $results;
    }
}


```

```php
<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ACF CSS 관리자 센터
 * - 스타일 센터 UI에 사용되는 텍스트/레이블 일부를 옵션으로 제어하기 위한 컨트롤 패널
 * - 상위/마스터 버전에서만 노출되도록 확장 가능하도록 설계
 * 
 * @version 3.7.0
 * - 탭 기반 인터페이스 추가 (General, Admin Menu, Section Layout, Texts, Colors)
 * - 2패널 레이아웃 구현 (Admin Menu 탭: 왼쪽 메뉴 목록, 오른쪽 상세 설정)
 * - 사이드바 액션 버튼 추가 (Save, Reset, Export, Import)
 * - 색상 미리보기 기능 추가
 * - 전용 CSS/JS 파일 분리 (jj-admin-center.css, jj-admin-center.js)
 */
final class JJ_Admin_Center {

    private static $instance = null;
    private $option_key           = 'jj_style_guide_admin_texts';
    private $sections_option_key  = 'jj_style_guide_section_layout';
    private $menu_option_key      = 'jj_style_guide_admin_menu_layout';
    private $colors_option_key    = 'jj_style_guide_admin_menu_colors';
    private $config               = array();
    
    // [v5.0.0] 성능 최적화: 섹션 레이아웃 캐싱
    private static $sections_layout_cache = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/ui-text-config.php';
            if ( file_exists( $config_file ) ) {
                $this->config = include $config_file;
            }
        }
    }

    /**
     * 초기화: 메뉴 등록 훅 연결
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_center_assets' ) );
        // [Phase 4.5] 상단바(Admin Bar) 어디서나 접근 가능한 진입점 추가
        add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 100 );

        // 관리자 메뉴 커스터마이징 적용
        add_action( 'admin_menu', array( $this, 'apply_admin_menu_customizations' ), 999 );
        add_filter( 'custom_menu_order', '__return_true' );
        add_filter( 'menu_order', array( $this, 'filter_menu_order' ) );
        add_action( 'admin_head', array( $this, 'output_admin_menu_styles' ) );

        // AJAX 핸들러: 팔레트 데이터 가져오기
        add_action( 'wp_ajax_jj_get_palette_data', array( $this, 'ajax_get_palette_data' ) );
        
        // [v4.0.1 신규] Admin Center 설정 저장 AJAX 핸들러
        add_action( 'wp_ajax_jj_admin_center_save', array( $this, 'ajax_save_admin_center_settings' ) );
        
        // [v3.8.0 신규] 라이센스 관리 AJAX 핸들러
        add_action( 'wp_ajax_jj_save_license_key', array( $this, 'ajax_save_license_key' ) );
        add_action( 'wp_ajax_jj_verify_license_key', array( $this, 'ajax_verify_license_key' ) );
        
        // [v5.1.7 신규] 업데이트 설정 AJAX 핸들러
        add_action( 'wp_ajax_jj_save_update_settings', array( $this, 'ajax_save_update_settings' ) );
        add_action( 'wp_ajax_jj_check_updates_now', array( $this, 'ajax_check_updates_now' ) );
        // [v8.x] Updates 탭: 플러그인별 자동 업데이트 토글 (WP 코어 옵션과 직접 동기화)
        add_action( 'wp_ajax_jj_toggle_auto_update_plugin', array( $this, 'ajax_toggle_auto_update_plugin' ) );
        // [Phase 6] 자가 진단 AJAX 핸들러
        add_action( 'wp_ajax_jj_run_self_test', array( $this, 'ajax_run_self_test' ) );

        // [v8.0.0] Bulk Installer AJAX 핸들러 (Tools 탭용)
        add_action( 'wp_ajax_jj_bulk_install_upload', array( $this, 'ajax_handle_bulk_upload' ) );
        add_action( 'wp_ajax_jj_bulk_install_process', array( $this, 'ajax_handle_bulk_install' ) );
        add_action( 'wp_ajax_jj_bulk_activate_plugin', array( $this, 'ajax_handle_bulk_activate' ) );

        // [v8.0.0] 자동 업데이트 필터 (강제 적용)
        add_filter( 'auto_update_plugin', array( $this, 'filter_auto_update_plugin' ), 10, 2 );
    }

    /**
     * [v8.0.0] 자동 업데이트 필터 핸들러
     * 워드프레스 코어가 자동 업데이트 여부를 결정할 때 개입합니다.
     */
    public function filter_auto_update_plugin( $update, $item ) {
        // 이 플러그인인지 확인 (슬러그 또는 파일명 매칭)
        $plugin_file = plugin_basename( JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php' );
        
        // $item이 객체인 경우와 배열인 경우 모두 처리
        $item_slug = '';
        if ( is_object( $item ) ) {
            $item_slug = isset( $item->plugin ) ? $item->plugin : ( isset( $item->slug ) ? $item->slug : '' );
        } elseif ( is_array( $item ) ) {
            $item_slug = isset( $item['plugin'] ) ? $item['plugin'] : ( isset( $item['slug'] ) ? $item['slug'] : '' );
        } else {
            // $item이 문자열(파일 경로)로 넘어오는 경우도 있음
            $item_slug = $item;
        }

        // 경로 매칭 시도 (정확한 매칭 또는 끝부분 매칭)
        if ( $item_slug === $plugin_file || strpos( $item_slug, 'acf-css-really-simple-style-guide.php' ) !== false ) {
            // WordPress 코어 자동 업데이트 설정(플러그인 목록 UI)의 상태를 최우선으로 신뢰합니다.
            // - 플러그인 목록에서 토글한 값이 곧바로 반영되어야 UX가 일관됩니다.
            $core_auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
            $core_enabled = in_array( $plugin_file, $core_auto_updates, true );

            // 플러그인 내부 설정 옵션과 동기화(불일치 시에만)
            $update_settings = get_option( 'jj_style_guide_update_settings', array() );
            if ( isset( $update_settings['auto_update_enabled'] ) && (bool) $update_settings['auto_update_enabled'] !== $core_enabled ) {
                $update_settings['auto_update_enabled'] = $core_enabled;
                update_option( 'jj_style_guide_update_settings', $update_settings );
            }

            return $core_enabled;
        }
        
        return $update;
    }

    /**
     * Admin Center 전용 CSS/JS enqueue
     */
    public function enqueue_admin_center_assets( $hook ) {
        // Admin Center 페이지에서만 로드
        $allowed_hooks = array(
            'settings_page_jj-admin-center',
            'appearance_page_jj-admin-center',
            'tools_page_jj-admin-center',
        );
        if ( ! in_array( $hook, $allowed_hooks, true ) ) {
            return;
        }

        $css_url = JJ_STYLE_GUIDE_URL . 'assets/css/jj-admin-center.css';
        $js_url  = JJ_STYLE_GUIDE_URL . 'assets/js/jj-admin-center.js';

        // 캐시 이슈 방지: 파일 변경 시 자동으로 버전이 바뀌도록 filemtime 사용 (가능할 때만)
        $fallback_ver = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0';
        $css_ver = $fallback_ver;
        $js_ver  = $fallback_ver;
        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            $css_path = JJ_STYLE_GUIDE_PATH . 'assets/css/jj-admin-center.css';
            $js_path  = JJ_STYLE_GUIDE_PATH . 'assets/js/jj-admin-center.js';
            if ( file_exists( $css_path ) ) {
                $css_ver = filemtime( $css_path );
            }
            if ( file_exists( $js_path ) ) {
                $js_ver = filemtime( $js_path );
            }
        }

        wp_enqueue_style(
            'jj-admin-center',
            $css_url,
            array(),
            $css_ver
        );

        // [UI Polish] 타 플러그인 알림 숨김 (몰입형 환경)
        wp_add_inline_style( 'jj-admin-center', '
            .notice:not(.jj-notice), .error:not(.jj-notice), .updated:not(.jj-notice) { display: none !important; }
            #wpbody-content > .notice, #wpbody-content > .error, #wpbody-content > .updated { display: none !important; }
        ' );

        // wpColorPicker (색상 피커용)
        wp_enqueue_style( 'wp-color-picker' );

        // jQuery UI Sortable (드래그 앤 드롭용)
        // jQuery UI Core와 Mouse가 필요함 (의존성 명시)
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-mouse' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        
        wp_enqueue_script(
            'jj-admin-center',
            $js_url,
            array( 'jquery', 'wp-color-picker', 'jquery-ui-core', 'jquery-ui-mouse', 'jquery-ui-sortable' ),
            $js_ver,
            true
        );
        
        // [v5.0.3] 키보드 단축키 시스템 로드
        wp_enqueue_script( 'jj-keyboard-shortcuts', JJ_STYLE_GUIDE_URL . 'assets/js/jj-keyboard-shortcuts.js', array( 'jquery' ), defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0', true );
        
        // [v5.0.3] 툴팁 시스템 로드
        wp_enqueue_script( 'jj-tooltips', JJ_STYLE_GUIDE_URL . 'assets/js/jj-tooltips.js', array( 'jquery' ), defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0', true );

        // AJAX 파라미터 로컬라이즈
        wp_localize_script(
            'jj-admin-center',
            'jjAdminCenter',
            array(
                'ajax_url'           => admin_url( 'admin-ajax.php' ),
                'nonce'              => wp_create_nonce( 'jj_admin_center_save_action' ),
                'style_guide_nonce'  => wp_create_nonce( 'jj_style_guide_nonce' ), // export/import용
                'backup_nonce'       => wp_create_nonce( 'jj_style_guide_nonce' ), // [v3.7.0] 백업 관리용
                'locale'             => get_locale(), // [v5.0.3] 다국어 지원을 위한 로케일 전달
                'i18n'               => array(
                    'eyedropper_not_supported' => __( '브라우저에서 스포이드 기능을 지원하지 않습니다.', 'jj-style-guide' ),
                    'palette_load_error'       => __( '팔레트를 불러오는 중 오류가 발생했습니다.', 'jj-style-guide' ),
                    'no_colors_found'          => __( '선택한 팔레트에 사용 가능한 색상이 없습니다.', 'jj-style-guide' ),
                    'license_save_success'     => __( '라이센스 키가 저장되었습니다.', 'jj-style-guide' ),
                    'license_save_error'       => __( '라이센스 키 저장에 실패했습니다.', 'jj-style-guide' ),
                    'license_verify_success'   => __( '라이센스 검증이 완료되었습니다.', 'jj-style-guide' ),
                    'license_verify_error'     => __( '라이센스 검증에 실패했습니다.', 'jj-style-guide' ),
                ),
            )
        );
    }

    /**
     * "ACF CSS 관리자 센터" 서브메뉴 추가
     * - 기본적으로 옵션 페이지 아래에 배치 (향후 라이선스/권한에 따라 노출 제어 가능)
     */
    public function add_admin_menu_page() {
        $menu_title = ( class_exists( 'JJ_Edition_Controller' ) ? JJ_Edition_Controller::instance()->get_branding( 'menu_title' ) : __( 'ACF CSS 관리자 센터', 'jj-style-guide' ) );
        $page_title = ( class_exists( 'JJ_Edition_Controller' ) ? JJ_Edition_Controller::instance()->get_branding( 'full_name' ) : __( 'ACF CSS 관리자 센터', 'jj-style-guide' ) );

        add_options_page(
            $page_title,
            $menu_title,
            'manage_options',
            'jj-admin-center',
            array( $this, 'render_admin_center_page' )
        );
        
        // [Phase 4.5] 모양(Appearance) 및 도구(Tools) 메뉴 추가
        // 중요: 동일한 slug(jj-admin-center)로 등록해야 hook_suffix가 일관되고, CSS/JS가 정상 로드됩니다.
        add_theme_page( $page_title, $menu_title, 'manage_options', 'jj-admin-center', array( $this, 'render_admin_center_page' ) );
        add_management_page( $page_title, $menu_title, 'manage_options', 'jj-admin-center', array( $this, 'render_admin_center_page' ) );

    }

    /**
     * [Phase 4.5] 어드민 바(Admin Bar) 메뉴 추가
     * - 어디서든 "스타일 센터/관리자 센터/Customizer"로 즉시 이동
     *
     * @param WP_Admin_Bar $wp_admin_bar
     */
    public function add_admin_bar_menu( $wp_admin_bar ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $menu_title = ( class_exists( 'JJ_Edition_Controller' ) ? JJ_Edition_Controller::instance()->get_branding( 'menu_title' ) : 'ACF CSS' );

        // 메인 메뉴: Admin Center
        $wp_admin_bar->add_node( array(
            'id'    => 'jj-style-guide',
            'title' => '<span class="ab-icon dashicons dashicons-art"></span> ' . $menu_title,
            'href'  => admin_url( 'options-general.php?page=jj-admin-center' ),
        ) );

        // 서브: 스타일 센터 (기존 스타일 편집 화면)
        $wp_admin_bar->add_node( array(
            'id'     => 'jj-style-guide-style-center',
            'parent' => 'jj-style-guide',
            'title'  => __( '스타일 센터', 'jj-style-guide' ),
            'href'   => admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ),
        ) );

        // 서브: Admin Center 주요 탭
        $wp_admin_bar->add_node( array(
            'id'     => 'jj-style-guide-admin-center',
            'parent' => 'jj-style-guide',
            'title'  => __( '관리자 센터', 'jj-style-guide' ),
            'href'   => admin_url( 'options-general.php?page=jj-admin-center' ),
        ) );

        // 서브: 실험실 (Labs)
        if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'basic' ) ) {
            $wp_admin_bar->add_node( array(
                'id'     => 'jj-style-guide-labs',
                'parent' => 'jj-style-guide',
                'title'  => __( '실험실 (Labs)', 'jj-style-guide' ),
                'href'   => admin_url( 'options-general.php?page=jj-labs-center' ),
            ) );
        }

        $wp_admin_bar->add_node( array(
            'id'     => 'jj-style-guide-colors',
            'parent' => 'jj-style-guide',
            'title'  => __( 'Admin Center: Colors', 'jj-style-guide' ),
            'href'   => admin_url( 'options-general.php?page=jj-admin-center#colors' ),
        ) );

        $wp_admin_bar->add_node( array(
            'id'     => 'jj-style-guide-visual',
            'parent' => 'jj-style-guide',
            'title'  => __( 'Admin Center: Visual', 'jj-style-guide' ),
            'href'   => admin_url( 'options-general.php?page=jj-admin-center#visual' ),
        ) );

        // 서브: Customizer 패널로 이동
        $wp_admin_bar->add_node( array(
            'id'     => 'jj-style-guide-customizer',
            'parent' => 'jj-style-guide',
            'title'  => __( '실시간 편집 (Customizer)', 'jj-style-guide' ),
            'href'   => admin_url( 'customize.php?autofocus[panel]=jj_style_guide_panel' ),
        ) );
    }

    /**
     * 관리자 센터 화면 렌더링
     */
    public function render_admin_center_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // [v7.0.0] 타 플러그인 알림 숨김 (몰입형 환경)
        remove_all_actions( 'admin_notices' );

        // 색상 기본값으로 되돌리기 처리 (다른 저장 로직보다 먼저 처리)
        if ( isset( $_POST['jj_admin_center_reset_colors'] ) && check_admin_referer( 'jj_admin_center_save_action', 'jj_admin_center_nonce' ) ) {
            delete_option( $this->colors_option_key );
            ?>
            <div class="updated notice"><p><?php esc_html_e( '관리자 메뉴 / 상단바 색상이 기본값으로 되돌려졌습니다.', 'jj-style-guide' ); ?></p></div>
            <?php
        }

        // 저장 처리 (텍스트 + 섹션 레이아웃 + 메뉴 + 색상)
        if ( isset( $_POST['jj_admin_center_save'] ) && check_admin_referer( 'jj_admin_center_save_action', 'jj_admin_center_nonce' ) ) {
            // 1) 텍스트 필드 저장
            $raw_texts   = isset( $_POST['jj_admin_texts'] ) && is_array( $_POST['jj_admin_texts'] ) ? wp_unslash( $_POST['jj_admin_texts'] ) : array();
            $clean_texts = array();
            foreach ( $raw_texts as $key => $value ) {
                $clean_texts[ sanitize_key( $key ) ] = sanitize_textarea_field( $value );
            }
            update_option( $this->option_key, $clean_texts );

            // 2) 섹션 레이아웃 저장
            $raw_sections   = isset( $_POST['jj_section_layout'] ) && is_array( $_POST['jj_section_layout'] ) ? wp_unslash( $_POST['jj_section_layout'] ) : array();
            $clean_sections = array();
            foreach ( $this->get_default_sections_layout() as $slug => $meta ) {
                $enabled = isset( $raw_sections[ $slug ]['enabled'] ) && '1' === $raw_sections[ $slug ]['enabled'];
                $order   = isset( $raw_sections[ $slug ]['order'] ) ? intval( $raw_sections[ $slug ]['order'] ) : (int) $meta['order'];
                $clean_sections[ $slug ] = array(
                    'enabled' => $enabled ? 1 : 0,
                    'order'   => $order,
                );
            }
            update_option( $this->sections_option_key, $clean_sections );
            
            // [v5.0.0] 캐시 플러시
            self::flush_sections_layout_cache();

            // 3) 관리자 메뉴 레이아웃 저장
            $raw_menu   = isset( $_POST['jj_admin_menu_layout'] ) && is_array( $_POST['jj_admin_menu_layout'] ) ? wp_unslash( $_POST['jj_admin_menu_layout'] ) : array();
            $clean_menu = array();
            foreach ( $raw_menu as $slug => $meta ) {
                $slug    = sanitize_key( $slug );
                $enabled = isset( $meta['enabled'] ) && '1' === $meta['enabled'];
                $order   = isset( $meta['order'] ) ? intval( $meta['order'] ) : 0;
                $label   = isset( $meta['label'] ) ? sanitize_text_field( $meta['label'] ) : '';
                $icon    = isset( $meta['icon'] ) ? sanitize_text_field( $meta['icon'] ) : '';
                $capability = isset( $meta['capability'] ) ? sanitize_text_field( $meta['capability'] ) : '';
                
                // 서브메뉴 저장
                $submenus = array();
                if ( isset( $meta['submenus'] ) && is_array( $meta['submenus'] ) ) {
                    foreach ( $meta['submenus'] as $submenu_slug => $submenu_meta ) {
                        $submenu_slug = sanitize_key( $submenu_slug );
                        $submenu_enabled = isset( $submenu_meta['enabled'] ) && '1' === $submenu_meta['enabled'];
                        $submenu_label = isset( $submenu_meta['label'] ) ? sanitize_text_field( $submenu_meta['label'] ) : '';
                        $submenu_order = isset( $submenu_meta['order'] ) ? intval( $submenu_meta['order'] ) : 0;
                        $submenu_capability = isset( $submenu_meta['capability'] ) ? sanitize_text_field( $submenu_meta['capability'] ) : '';
                        
                        $submenus[ $submenu_slug ] = array(
                            'enabled' => $submenu_enabled ? 1 : 0,
                            'label'   => $submenu_label,
                            'order'   => $submenu_order,
                            'capability' => $submenu_capability,
                        );
                    }
                }
                
                $clean_menu[ $slug ] = array(
                    'enabled' => $enabled ? 1 : 0,
                    'order'   => $order,
                    'label'   => $label,
                    'icon'    => $icon,
                    'capability' => $capability,
                    'submenus' => $submenus,
                );
            }
            update_option( $this->menu_option_key, $clean_menu );

            // 4) 관리자 메뉴/상단바 색상 저장
            $raw_colors   = isset( $_POST['jj_admin_menu_colors'] ) && is_array( $_POST['jj_admin_menu_colors'] ) ? wp_unslash( $_POST['jj_admin_menu_colors'] ) : array();
            $clean_colors = array();
            foreach ( $this->get_default_admin_colors() as $key => $default_hex ) {
                $raw = isset( $raw_colors[ $key ] ) ? $raw_colors[ $key ] : '';
                $san = sanitize_hex_color( $raw );
                $clean_colors[ $key ] = $san ? $san : $default_hex;
            }
            update_option( $this->colors_option_key, $clean_colors );
            ?>
            <div class="updated notice"><p><?php esc_html_e( '관리자 센터 설정이 저장되었습니다.', 'jj-style-guide' ); ?></p></div>
            <?php
        }

        $stored          = (array) get_option( $this->option_key );
        $admin_texts     = $stored; // 변수명 통일
        $sections_layout = $this->get_sections_layout();
        $menu_layout     = $this->get_menu_layout();
        $colors_layout   = $this->get_admin_menu_colors();
        
        global $menu, $submenu; // [Fix] 메뉴 탭에서 사용
        ?>
        <div class="wrap jj-admin-center-wrap jj-has-sidebar">
            <!-- [v6.3.0] 왼쪽 고정 사이드바 네비게이션 -->
            <div class="jj-admin-center-sidebar">
                <div class="jj-admin-center-sidebar-header">
                    <h2><?php esc_html_e( '빠른 이동', 'jj-style-guide' ); ?></h2>
                    <button type="button" class="jj-sidebar-toggle" aria-label="<?php esc_attr_e( '사이드바 토글', 'jj-style-guide' ); ?>">
                        <span class="dashicons dashicons-menu-alt"></span>
                    </button>
                </div>
                <ul class="jj-admin-center-sidebar-nav">
                    <li>
                        <a href="#general" data-tab="general" class="active">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php esc_html_e( 'General', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#admin-menu" data-tab="admin-menu">
                            <span class="dashicons dashicons-menu"></span>
                            <?php esc_html_e( 'Admin Menu', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#section-layout" data-tab="section-layout">
                            <span class="dashicons dashicons-layout"></span>
                            <?php esc_html_e( 'Section Layout', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#texts" data-tab="texts">
                            <span class="dashicons dashicons-text"></span>
                            <?php esc_html_e( 'Texts', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#colors" data-tab="colors">
                            <span class="dashicons dashicons-admin-appearance"></span>
                            <?php esc_html_e( 'Colors', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#visual" data-tab="visual">
                            <span class="dashicons dashicons-visibility"></span>
                            <?php esc_html_e( 'Visual', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#cloud" data-tab="cloud">
                            <span class="dashicons dashicons-cloud"></span>
                            <?php esc_html_e( 'Cloud', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#backup" data-tab="backup">
                            <span class="dashicons dashicons-backup"></span>
                            <?php esc_html_e( '백업 관리', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#updates" data-tab="updates">
                            <span class="dashicons dashicons-update"></span>
                            <?php esc_html_e( '업데이트', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#license" data-tab="license">
                            <span class="dashicons dashicons-admin-network"></span>
                            <?php esc_html_e( '라이센스', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#system-status" data-tab="system-status">
                            <span class="dashicons dashicons-info"></span>
                            <?php esc_html_e( '시스템 상태', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <?php if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'basic' ) ) : ?>
                    <li class="jj-sidebar-nav-divider" style="margin: 10px 0; border-top: 1px solid #333;"></li>
                    <li>
                        <a href="<?php echo esc_url( admin_url( 'options-general.php?page=jj-labs-center' ) ); ?>">
                            <span class="dashicons dashicons-beaker"></span>
                            <?php esc_html_e( '실험실 센터', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="jj-admin-center-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h1 style="margin: 0;"><?php esc_html_e( 'ACF CSS 관리자 센터', 'jj-style-guide' ); ?></h1>
                        <a href="<?php echo esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ); ?>" 
                           class="button button-secondary" 
                           style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <?php esc_html_e( '스타일 센터', 'jj-style-guide' ); ?>
                        </a>
                    <a href="<?php echo esc_url( admin_url( 'options-general.php?page=jj-labs-center' ) ); ?>" 
                       class="button button-secondary" 
                       style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <?php esc_html_e( '실험실 센터', 'jj-style-guide' ); ?>
                    </a>
                    <?php
                    // [v5.1.6] 마스터 버전이 아닌 경우 결제 유도 문구 표시
                    $is_master_version = false;
                    if ( class_exists( 'JJ_Edition_Controller' ) ) {
                        $is_master_version = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                    }
                    if ( ! $is_master_version ) {
                        $license_manager = null;
                        $purchase_url = 'https://j-j-labs.com'; // 기본값
                        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                            if ( class_exists( 'JJ_License_Manager' ) ) {
                                $license_manager = JJ_License_Manager::instance();
                                if ( $license_manager ) {
                                    $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                                }
                            }
                        }
                        ?>
                        <a href="<?php echo esc_url( $purchase_url ); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="button button-primary" 
                           style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px; font-weight: 600;">
                            <span class="dashicons dashicons-cart" style="margin-top: 4px;"></span> <?php esc_html_e( '업그레이드하기', 'jj-style-guide' ); ?>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <div class="jj-header-actions">
                    <!-- [v3.7.0 '신규'] 관리자 센터 설정 내보내기/불러오기 (헤더) -->
                    <button type="button" class="button button-secondary jj-export-settings" data-center="admin-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px; margin-right: 8px;">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php esc_html_e( '내보내기', 'jj-style-guide' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-settings" data-center="admin-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span> <?php esc_html_e( '불러오기', 'jj-style-guide' ); ?>
                    </button>
                </div>
            </div>

            <!-- 탭 네비게이션 -->
            <ul class="jj-admin-center-tabs">
                <li data-tab="general" class="active">
                    <a href="#general"><?php esc_html_e( 'General', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="admin-menu">
                    <a href="#admin-menu"><?php esc_html_e( 'Admin Menu', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="section-layout">
                    <a href="#section-layout"><?php esc_html_e( 'Section Layout', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="texts">
                    <a href="#texts"><?php esc_html_e( 'Texts', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="colors">
                    <a href="#colors"><?php esc_html_e( 'Colors', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="visual">
                    <a href="#visual"><?php esc_html_e( 'Visual', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="cloud">
                    <a href="#cloud"><?php esc_html_e( 'Cloud', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="backup">
                    <a href="#backup"><?php esc_html_e( '백업 관리', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="tools">
                    <a href="#tools"><?php esc_html_e( '도구', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="updates">
                    <a href="#updates"><?php esc_html_e( '업데이트', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="license">
                    <a href="#license"><?php esc_html_e( '라이센스', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="system-status">
                    <a href="#system-status"><?php esc_html_e( '시스템 상태', 'jj-style-guide' ); ?></a>
                </li>
            </ul>

            <form method="post" id="jj-admin-center-form">
                <?php wp_nonce_field( 'jj_admin_center_save_action', 'jj_admin_center_nonce' ); ?>

                <!-- General 탭 -->
                <?php include JJ_STYLE_GUIDE_PATH . 'includes/admin/views/tabs/tab-general.php'; ?>

                                                <!-- Admin Menu 탭 -->
                <?php 
                $tab_admin_menu = JJ_STYLE_GUIDE_PATH . 'includes/admin/views/tabs/tab-admin-menu.php';
                if ( file_exists( $tab_admin_menu ) ) {
                    include $tab_admin_menu; 
                } else {
                    echo '<div class="notice notice-error"><p>탭 파일을 찾을 수 없습니다: tab-admin-menu.php</p></div>';
                }
                ?>

<!-- [v3.7.0 '신규'] 관리자 센터 설정 내보내기/불러오기 (푸터) -->
            <!-- Tools 탭 -->
                <?php 
                $tab_tools = JJ_STYLE_GUIDE_PATH . 'includes/admin/views/tabs/tab-tools.php';
                if ( file_exists( $tab_tools ) ) {
                    include $tab_tools;
                }
                ?>
                
            <div class="jj-admin-center-footer"  style="margin-top: 30px; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <button type="button" class="button button-secondary jj-export-settings" data-center="admin-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px; margin-right: 8px;">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php esc_html_e( '내보내기', 'jj-style-guide' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-settings" data-center="admin-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span> <?php esc_html_e( '불러오기', 'jj-style-guide' ); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * UI 텍스트 가져오기
     *
     * @param string $key
     * @param string $fallback
     * @return string
     */
    public function get_text( $key, $fallback = '' ) {
        $stored = (array) get_option( $this->option_key );

        if ( isset( $stored[ $key ] ) && $stored[ $key ] !== '' ) {
            return $stored[ $key ];
        }

        if ( isset( $this->config[ $key ]['default'] ) ) {
            return $this->config[ $key ]['default'];
        }

        return $fallback;
    }

    /**
     * 섹션 레이아웃 기본값
     *
     * @return array
     */
    public function get_default_sections_layout() {
        // [v5.0.0] 탭 활성화/비활성화 기능 추가
        $defaults = array(
            'colors'     => array(
                'label'  => __( '1. 팔레트 시스템', 'jj-style-guide' ),
                'order'  => 10,
                'enabled'=> 1,
                'tabs'   => array( // [v5.0.0] 탭 활성화/비활성화
                    'brand'       => array( 'label' => __( '브랜드 팔레트', 'jj-style-guide' ), 'enabled' => 1 ),
                    'system'      => array( 'label' => __( '시스템 팔레트', 'jj-style-guide' ), 'enabled' => 1 ),
                    'alternative' => array( 'label' => __( '얼터너티브 팔레트', 'jj-style-guide' ), 'enabled' => 1 ),
                    'another'     => array( 'label' => __( '어나더 팔레트', 'jj-style-guide' ), 'enabled' => 1 ),
                    'temp-palette'=> array( 'label' => __( '임시 팔레트', 'jj-style-guide' ), 'enabled' => 1 ),
                ),
            ),
            'typography' => array(
                'label'  => __( '2. 타이포그래피', 'jj-style-guide' ),
                'order'  => 20,
                'enabled'=> 1,
                'tabs'   => array(), // 탭 없음
            ),
            'buttons'    => array(
                'label'  => __( '3. 버튼', 'jj-style-guide' ),
                'order'  => 30,
                'enabled'=> 1,
                'tabs'   => array( // [v5.0.0] 탭 활성화/비활성화
                    'btn-primary'   => array( 'label' => __( 'Primary Button', 'jj-style-guide' ), 'enabled' => 1 ),
                    'btn-secondary' => array( 'label' => __( 'Secondary Button', 'jj-style-guide' ), 'enabled' => 1 ),
                    'btn-text'      => array( 'label' => __( 'Text / Outline Button', 'jj-style-guide' ), 'enabled' => 1 ),
                ),
            ),
            'forms'      => array(
                'label'  => __( '4. 폼 & 필드', 'jj-style-guide' ),
                'order'  => 40,
                'enabled'=> 1,
                'tabs'   => array( // [v5.0.0] 탭 활성화/비활성화
                    'form-label' => array( 'label' => __( '라벨 (Labels)', 'jj-style-guide' ), 'enabled' => 1 ),
                    'form-field' => array( 'label' => __( '입력 필드 (Fields)', 'jj-style-guide' ), 'enabled' => 1 ),
                ),
            ),
        );

        /**
         * 섹션 레이아웃 기본값을 필터링할 수 있습니다.
         */
        return apply_filters( 'jj_style_guide_default_sections_layout', $defaults );
    }

    /**
     * 저장된 섹션 레이아웃 + 기본값 병합
     *
     * @return array
     */
    /**
     * 섹션 레이아웃 가져오기
     * [v5.0.0] 성능 최적화: static 캐싱 추가
     *
     * @return array
     */
    public function get_sections_layout() {
        // [v5.0.0] 캐시된 값이 있으면 반환
        if ( self::$sections_layout_cache !== null ) {
            return self::$sections_layout_cache;
        }
        
        $stored   = (array) get_option( $this->sections_option_key );
        $defaults = $this->get_default_sections_layout();
        $result   = array();

        foreach ( $defaults as $slug => $meta ) {
            $enabled = isset( $stored[ $slug ]['enabled'] ) ? (bool) $stored[ $slug ]['enabled'] : (bool) ( $meta['enabled'] ?? true );
            $order   = isset( $stored[ $slug ]['order'] ) ? intval( $stored[ $slug ]['order'] ) : (int) ( $meta['order'] ?? 0 );
            
            // [v5.0.0] 탭 활성화/비활성화 정보 병합
            $tabs = array();
            if ( isset( $meta['tabs'] ) && is_array( $meta['tabs'] ) ) {
                foreach ( $meta['tabs'] as $tab_slug => $tab_meta ) {
                    $tab_enabled = isset( $stored[ $slug ]['tabs'][ $tab_slug ]['enabled'] ) 
                        ? (bool) $stored[ $slug ]['tabs'][ $tab_slug ]['enabled'] 
                        : (bool) ( $tab_meta['enabled'] ?? true );
                    $tabs[ $tab_slug ] = array(
                        'label'   => $tab_meta['label'],
                        'enabled' => $tab_enabled ? 1 : 0,
                    );
                }
            }
            
            $result[ $slug ] = array(
                'label'   => $meta['label'],
                'enabled' => $enabled ? 1 : 0,
                'order'   => $order,
                'tabs'    => $tabs, // [v5.0.0] 탭 활성화/비활성화 정보
            );
        }

        // [v5.0.0] 캐시에 저장
        self::$sections_layout_cache = $result;
        
        return $result;
    }
    
    /**
     * 섹션 레이아웃 캐시 플러시
     * [v5.0.0] 성능 최적화: 옵션이 업데이트되면 호출하여 캐시를 무효화
     *
     * @return void
     */
    public static function flush_sections_layout_cache() {
        self::$sections_layout_cache = null;
    }
    
    /**
     * [v5.0.0] 특정 섹션의 특정 탭이 활성화되어 있는지 확인
     *
     * @param string $section_slug 섹션 슬러그
     * @param string $tab_slug 탭 슬러그
     * @return bool
     */
    public function is_tab_enabled( $section_slug, $tab_slug ) {
        $layout = $this->get_sections_layout();
        if ( ! isset( $layout[ $section_slug ] ) ) {
            return false;
        }
        if ( ! isset( $layout[ $section_slug ]['tabs'][ $tab_slug ] ) ) {
            return true; // 탭이 정의되지 않았으면 활성화된 것으로 간주
        }
        return ! empty( $layout[ $section_slug ]['tabs'][ $tab_slug ]['enabled'] );
    }

    /**
     * 주어진 섹션 슬러그의 현재 표시 순서(1부터 시작하는 인덱스)를 반환
     *
     * @param string $slug
     * @return int|null
     */
    public function get_section_index( $slug ) {
        $layout = $this->get_sections_layout();

        // 표시가 허용된 섹션만 정렬
        $enabled_sections = array_filter(
            $layout,
            function ( $meta ) {
                return ! empty( $meta['enabled'] );
            }
        );

        uasort(
            $enabled_sections,
            function ( $a, $b ) {
                return (int) $a['order'] <=> (int) $b['order'];
            }
        );

        $index = 1;
        foreach ( $enabled_sections as $section_slug => $meta ) {
            if ( $section_slug === $slug ) {
                return $index;
            }
            $index++;
        }

        return null;
    }

    /**
     * 관리자 메뉴 레이아웃 반환
     *
     * @return array
     */
    public function get_menu_layout() {
        $stored = (array) get_option( $this->menu_option_key );
        return $stored;
    }

    /**
     * 관리자 메뉴 / 상단바 색상 기본값
     *
     * @return array
     */
    public function get_default_admin_colors() {
        return array(
            'sidebar_bg'         => '#1f2933',
            'sidebar_text'       => '#d9e2ec',
            'sidebar_text_hover' => '#ffffff',
            'sidebar_bg_hover'   => '#111827',
            'sidebar_bg_active'  => '#111827',
            'sidebar_text_active'=> '#ffffff',
            'topbar_bg'          => '#111827',
            'topbar_text'        => '#d9e2ec',
            'topbar_text_hover'  => '#ffffff',
        );
    }

    /**
     * 저장된 관리자 메뉴 / 상단바 색상
     *
     * @return array
     */
    public function get_admin_menu_colors() {
        $stored   = (array) get_option( $this->colors_option_key );
        $defaults = $this->get_default_admin_colors();

        return array_merge( $defaults, $stored );
    }

    /**
     * admin_menu 훅에서 실제 메뉴 배열에 label/visibility/icon/capability 적용
     * [v3.7.0 '신규'] 아이콘, 권한, 서브메뉴 편집 기능 추가
     */
    public function apply_admin_menu_customizations() {
        $layout = $this->get_menu_layout();
        if ( empty( $layout ) ) {
            return;
        }

        global $menu, $submenu;
        if ( ! is_array( $menu ) ) {
            return;
        }

        foreach ( $menu as $index => $item ) {
            if ( ! isset( $item[2] ) ) {
                continue;
            }
            $slug = sanitize_key( $item[2] );
            if ( ! isset( $layout[ $slug ] ) ) {
                continue;
            }

            $meta = $layout[ $slug ];

            // 표시 여부
            if ( isset( $meta['enabled'] ) && ! $meta['enabled'] ) {
                // 메뉴 숨김 시 서브메뉴도 함께 숨김
                if ( isset( $submenu[ $slug ] ) ) {
                    unset( $submenu[ $slug ] );
                }
                unset( $menu[ $index ] );
                continue;
            }

            // 커스텀 레이블
            if ( ! empty( $meta['label'] ) ) {
                $menu[ $index ][0] = $meta['label'];
            }
            
            // [v3.7.0 '신규'] 커스텀 아이콘
            if ( ! empty( $meta['icon'] ) ) {
                $icon = $meta['icon'];
                // Dashicons 클래스 이름 정규화
                if ( strpos( $icon, 'dashicons-' ) === 0 ) {
                    $menu[ $index ][6] = $icon;
                } elseif ( strpos( $icon, 'dashicons ' ) === 0 ) {
                    $menu[ $index ][6] = 'dashicons-' . str_replace( 'dashicons ', '', $icon );
                } elseif ( filter_var( $icon, FILTER_VALIDATE_URL ) ) {
                    // 이미지 URL
                    $menu[ $index ][6] = $icon;
                }
            }
            
            // [v3.7.0 '신규'] 커스텀 권한
            if ( ! empty( $meta['capability'] ) ) {
                // 현재 사용자가 해당 권한을 가지고 있는지 확인
                if ( ! current_user_can( $meta['capability'] ) ) {
                    // 권한이 없으면 메뉴 숨김
                    if ( isset( $submenu[ $slug ] ) ) {
                        unset( $submenu[ $slug ] );
                    }
                    unset( $menu[ $index ] );
                    continue;
                } else {
                    $menu[ $index ][1] = $meta['capability'];
                }
            }
            
            // [v3.7.0 '신규'] 서브메뉴 편집 적용
            if ( isset( $meta['submenus'] ) && is_array( $meta['submenus'] ) && isset( $submenu[ $slug ] ) ) {
                $submenu_items = array();
                
                // 서브메뉴 정렬 및 필터링
                foreach ( $submenu[ $slug ] as $submenu_index => $submenu_item ) {
                    $submenu_slug = isset( $submenu_item[2] ) ? sanitize_key( $submenu_item[2] ) : '';
                    if ( empty( $submenu_slug ) || ! isset( $meta['submenus'][ $submenu_slug ] ) ) {
                        // 커스터마이징되지 않은 서브메뉴는 그대로 유지
                        $submenu_items[ $submenu_index ] = $submenu_item;
                        continue;
                    }
                    
                    $submenu_meta = $meta['submenus'][ $submenu_slug ];
                    
                    // 서브메뉴 표시 여부
                    if ( isset( $submenu_meta['enabled'] ) && ! $submenu_meta['enabled'] ) {
                        continue; // 이 서브메뉴는 숨김
                    }
                    
                    // 서브메뉴 권한 확인
                    $submenu_capability = ! empty( $submenu_meta['capability'] ) ? $submenu_meta['capability'] : ( isset( $submenu_item[1] ) ? $submenu_item[1] : 'read' );
                    if ( ! current_user_can( $submenu_capability ) ) {
                        continue; // 권한이 없으면 숨김
                    }
                    
                    // 서브메뉴 레이블 변경
                    $submenu_item_copy = $submenu_item;
                    if ( ! empty( $submenu_meta['label'] ) ) {
                        $submenu_item_copy[0] = $submenu_meta['label'];
                    }
                    
                    // 서브메뉴 권한 변경
                    if ( ! empty( $submenu_meta['capability'] ) ) {
                        $submenu_item_copy[1] = $submenu_meta['capability'];
                    }
                    
                    // 서브메뉴 순서 저장 (order 값을 인덱스로 사용하기 위해 임시 저장)
                    $submenu_order = isset( $submenu_meta['order'] ) ? intval( $submenu_meta['order'] ) : $submenu_index;
                    $submenu_items[ $submenu_order * 1000 + $submenu_index ] = $submenu_item_copy; // 중복 방지를 위해 인덱스 추가
                }
                
                // 순서 정렬
                ksort( $submenu_items );
                $submenu[ $slug ] = array_values( $submenu_items );
            }
        }
    }

    /**
     * menu_order 필터에서 순서 재정의
     *
     * @param array $menu_order
     * @return array
     */
    public function filter_menu_order( $menu_order ) {
        $layout = $this->get_menu_layout();
        if ( empty( $layout ) || ! is_array( $menu_order ) ) {
            return $menu_order;
        }

        $order_map = array();
        foreach ( $menu_order as $index => $slug ) {
            $key = sanitize_key( $slug );
            $order_map[ $slug ] = isset( $layout[ $key ]['order'] ) ? (int) $layout[ $key ]['order'] : 0;
        }

        usort(
            $menu_order,
            function ( $a, $b ) use ( $order_map ) {
                $a_order = isset( $order_map[ $a ] ) ? $order_map[ $a ] : 0;
                $b_order = isset( $order_map[ $b ] ) ? $order_map[ $b ] : 0;

                if ( $a_order === $b_order ) {
                    return 0;
                }

                return ( $a_order < $b_order ) ? -1 : 1;
            }
        );

        return $menu_order;
    }

    /**
     * 관리자 메뉴/상단바 색상 커스터마이징용 CSS 출력 (기본 버전: 색상 상수, 추후 옵션화 가능)
     */
    public function output_admin_menu_styles() {
        // 향후 색상 옵션을 별도 키로 분리할 수 있도록, 현재는 최소한의 구조만 출력
        $colors = apply_filters(
            'jj_style_guide_admin_menu_colors',
            $this->get_admin_menu_colors()
        );
        ?>
        <style id="jj-admin-menu-custom-colors">
            #adminmenu, #adminmenu .wp-submenu, #adminmenuback, #adminmenuwrap {
                background-color: <?php echo esc_html( $colors['sidebar_bg'] ); ?>;
            }
            #adminmenu a {
                color: <?php echo esc_html( $colors['sidebar_text'] ); ?>;
            }
            #adminmenu .wp-has-current-submenu > a.menu-top,
            #adminmenu .current a.menu-top,
            #adminmenu .wp-menu-open > a.menu-top {
                background-color: <?php echo esc_html( $colors['sidebar_bg_active'] ); ?>;
                color: <?php echo esc_html( $colors['sidebar_text_active'] ); ?>;
            }
            #adminmenu a:hover,
            #adminmenu li.menu-top:hover,
            #adminmenu li.opensub > a.menu-top,
            #adminmenu li > a.menu-top:focus {
                background-color: <?php echo esc_html( $colors['sidebar_bg_hover'] ); ?>;
                color: <?php echo esc_html( $colors['sidebar_text_hover'] ); ?>;
            }
            #wpadminbar {
                background: <?php echo esc_html( $colors['topbar_bg'] ); ?>;
            }
            #wpadminbar .ab-item,
            #wpadminbar a.ab-item,
            #wpadminbar > #wp-toolbar span.ab-label,
            #wpadminbar > #wp-toolbar span.noticon {
                color: <?php echo esc_html( $colors['topbar_text'] ); ?>;
            }
            #wpadminbar .ab-item:hover,
            #wpadminbar .ab-item:focus,
            #wpadminbar a.ab-item:focus,
            #wpadminbar a.ab-item:hover {
                color: <?php echo esc_html( $colors['topbar_text_hover'] ); ?>;
            }
        </style>
        <?php
    }

    /**
     * AJAX 핸들러: 팔레트 데이터 가져오기
     * [v3.8.0] 관리자 센터 Colors 탭에서 팔레트 색상 불러오기용
     */
    public function ajax_get_palette_data() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }

        // 팔레트 데이터 가져오기
        $hub_options = get_option( JJ_STYLE_GUIDE_OPTIONS_KEY, array() );
        $temp_options = get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );

        $palettes = array();

        // 브랜드 팔레트
        if ( ! empty( $hub_options['palettes']['brand'] ) ) {
            $palettes['brand'] = array(
                'name' => __( '브랜드 팔레트', 'jj-style-guide' ),
                'colors' => $hub_options['palettes']['brand'],
            );
        }

        // 시스템 팔레트
        if ( ! empty( $hub_options['palettes']['system'] ) ) {
            $palettes['system'] = array(
                'name' => __( '시스템 팔레트', 'jj-style-guide' ),
                'colors' => $hub_options['palettes']['system'],
            );
        }

        // 얼터너티브 팔레트
        if ( ! empty( $hub_options['palettes']['alternative'] ) ) {
            $palettes['alternative'] = array(
                'name' => __( '얼터너티브 팔레트', 'jj-style-guide' ),
                'colors' => $hub_options['palettes']['alternative'],
            );
        }

        // 어나더 팔레트
        if ( ! empty( $hub_options['palettes']['another'] ) ) {
            $palettes['another'] = array(
                'name' => __( '어나더 팔레트', 'jj-style-guide' ),
                'colors' => $hub_options['palettes']['another'],
            );
        }

        // 임시 팔레트
        if ( ! empty( $temp_options['palettes']['brand'] ) ) {
            $palettes['temporary'] = array(
                'name' => __( '임시 팔레트', 'jj-style-guide' ),
                'colors' => $temp_options['palettes']['brand'],
            );
        }

        wp_send_json_success( array( 'palettes' => $palettes ) );
    }

    /**
     * [v3.8.0 신규] AJAX: 라이센스 키 저장
     */
    public function ajax_save_license_key() {
        check_ajax_referer( 'jj_license_save_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
        
        if ( empty( $license_key ) ) {
            wp_send_json_error( array( 'message' => __( '라이센스 키를 입력하세요.', 'jj-style-guide' ) ) );
        }
        
        $license_manager = null;
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
            }
        }
        
        if ( ! $license_manager ) {
            wp_send_json_error( array( 'message' => __( '라이센스 관리자를 로드할 수 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $result = $license_manager->save_license_key( $license_key );
        
        if ( $result['success'] ) {
            // 라이센스 타입 상수 업데이트 시도 (이미 정의되어 있으면 업데이트 불가)
            if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
                // 상수는 재정의할 수 없으므로 옵션에 저장
                update_option( 'jj_style_guide_license_type_override', $result['status']['type'] );
            }
            
            wp_send_json_success( array(
                'message' => $result['message'],
                'status' => $result['status'],
            ) );
        } else {
            wp_send_json_error( array( 'message' => $result['message'] ) );
        }
    }

    /**
     * [v3.8.0 신규] AJAX: 라이센스 키 검증
     */
    public function ajax_verify_license_key() {
        check_ajax_referer( 'jj_license_save_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $license_manager = null;
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
            }
        }
        
        if ( ! $license_manager ) {
            wp_send_json_error( array( 'message' => __( '라이센스 관리자를 로드할 수 없습니다.', 'jj-style-guide' ) ) );
        }
        
        // 강제 온라인 검증
        $status = $license_manager->verify_license( true );
        
        wp_send_json_success( array(
            'status' => $status,
            'message' => $status['message'],
        ) );
    }

    /**
     * [v5.1.7 신규] AJAX: 업데이트 설정 저장
     */
    public function ajax_save_update_settings() {
        check_ajax_referer( 'jj_admin_center_save_action', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $raw_update_settings = isset( $_POST['update_settings'] ) && is_array( $_POST['update_settings'] ) ? wp_unslash( $_POST['update_settings'] ) : array();
        $clean_update_settings = array(
            'auto_update_enabled' => isset( $raw_update_settings['auto_update_enabled'] ) && '1' === $raw_update_settings['auto_update_enabled'],
            'update_channel' => isset( $raw_update_settings['update_channel'] ) ? sanitize_text_field( $raw_update_settings['update_channel'] ) : 'stable',
            'beta_updates_enabled' => isset( $raw_update_settings['beta_updates_enabled'] ) && '1' === $raw_update_settings['beta_updates_enabled'],
            'send_app_logs' => isset( $raw_update_settings['send_app_logs'] ) && '1' === $raw_update_settings['send_app_logs'],
            'send_error_logs' => isset( $raw_update_settings['send_error_logs'] ) && '1' === $raw_update_settings['send_error_logs'],
        );
        
        // Partner/Master는 모든 로그를 반드시 전송 (내부용)
        $is_master_version = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $is_master_version = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_master_version = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        }
        if ( $is_master_version ) {
            $clean_update_settings['send_app_logs'] = true;
            $clean_update_settings['send_error_logs'] = true;
        }
        
        // 업데이트 채널 유효성 검사
        // NOTE: build/deployment 채널과 UI를 맞춤 (stable/beta/staging). test/dev는 레거시 호환용으로만 허용.
        $allowed_channels = array( 'stable', 'beta', 'staging', 'test', 'dev' );
        if ( ! in_array( $clean_update_settings['update_channel'], $allowed_channels, true ) ) {
            $clean_update_settings['update_channel'] = 'stable';
        }

        // 채널이 stable이 아니면(=실험 채널) beta flag는 자동으로 true로 맞춤 (구버전 로직 호환)
        if ( 'stable' !== $clean_update_settings['update_channel'] ) {
            $clean_update_settings['beta_updates_enabled'] = true;
        }
        
        update_option( 'jj_style_guide_update_settings', $clean_update_settings );
        
        // [v5.6.0] Visual Command Center 설정 저장
        if ( isset( $_POST['visual_options'] ) && is_array( $_POST['visual_options'] ) ) {
            // 간단한 sanitize (실제로는 더 정교해야 함)
            $visual_options = array_map( 'sanitize_text_field', wp_unslash( $_POST['visual_options'] ) );
            update_option( 'jj_style_guide_visual_options', $visual_options );
        }
        
        // 자동 업데이트 활성화/비활성화 (메인 파일 경로가 다를 수 있음 고려)
        $possible_plugin_files = array(
            'acf-css-really-simple-style-management-center/acf-css-really-simple-style-guide.php',
            'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php',
            plugin_basename( JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php' )
        );
        $plugin_file = plugin_basename( JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php' );

        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
        
        if ( $clean_update_settings['auto_update_enabled'] ) {
            if ( ! in_array( $plugin_file, $auto_updates, true ) ) {
                $auto_updates[] = $plugin_file;
                update_site_option( 'auto_update_plugins', $auto_updates );
            }
        } else {
            // 비활성화 시 모든 가능한 경로 제거 (안전장치)
            foreach ( $possible_plugin_files as $file ) {
                if ( ( $key = array_search( $file, $auto_updates, true ) ) !== false ) {
                    unset( $auto_updates[$key] );
                }
            }
            // 현재 감지된 파일도 제거
            if ( ( $key = array_search( $plugin_file, $auto_updates, true ) ) !== false ) {
                unset( $auto_updates[$key] );
            }
            update_site_option( 'auto_update_plugins', $auto_updates );
        }
        
        wp_send_json_success( array( 
            'message' => __( '업데이트 설정이 저장되었습니다.', 'jj-style-guide' ),
            'settings' => $clean_update_settings,
        ) );
    }

    /**
     * [v8.x] AJAX: 플러그인별 자동 업데이트 토글
     *
     * - WP 코어의 auto_update_plugins(site option)를 직접 업데이트합니다.
     * - 플러그인 목록 화면의 토글과 동일한 효과를 가집니다.
     */
    public function ajax_toggle_auto_update_plugin() {
        check_ajax_referer( 'jj_admin_center_save_action', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }

        $plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) : '';
        $enabled_raw = isset( $_POST['enabled'] ) ? wp_unslash( $_POST['enabled'] ) : null;

        if ( '' === $plugin ) {
            wp_send_json_error( array( 'message' => __( '플러그인 정보가 없습니다.', 'jj-style-guide' ) ) );
        }

        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
        $is_enabled = in_array( $plugin, $auto_updates, true );

        // enabled 파라미터가 오면 그 값으로, 없으면 토글
        $target = null;
        if ( null !== $enabled_raw ) {
            $target = ( '1' === (string) $enabled_raw || 'true' === (string) $enabled_raw );
        } else {
            $target = ! $is_enabled;
        }

        if ( $target ) {
            if ( ! $is_enabled ) {
                $auto_updates[] = $plugin;
                $auto_updates = array_values( array_unique( $auto_updates ) );
                update_site_option( 'auto_update_plugins', $auto_updates );
            }
        } else {
            if ( ( $key = array_search( $plugin, $auto_updates, true ) ) !== false ) {
                unset( $auto_updates[ $key ] );
                $auto_updates = array_values( $auto_updates );
                update_site_option( 'auto_update_plugins', $auto_updates );
            }
        }

        // 코어 플러그인인 경우 내부 옵션도 동기화 (UX 일관성)
        $core_plugin_file = plugin_basename( JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php' );
        if ( $plugin === $core_plugin_file ) {
            $update_settings = get_option( 'jj_style_guide_update_settings', array() );
            if ( ! is_array( $update_settings ) ) {
                $update_settings = array();
            }
            $update_settings['auto_update_enabled'] = $target;
            update_option( 'jj_style_guide_update_settings', $update_settings );
        }

        wp_send_json_success(
            array(
                'plugin'  => $plugin,
                'enabled' => (bool) $target,
            )
        );
    }

    /**
     * [v5.1.7 신규] AJAX: 지금 업데이트 확인
     */
    public function ajax_check_updates_now() {
        check_ajax_referer( 'jj_admin_center_save_action', 'security' );
        
        if ( ! current_user_can( 'update_plugins' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        // 캐시 삭제하여 강제로 업데이트 체크
        delete_site_transient( 'update_plugins' );
        
        // 업데이트 정보 가져오기
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php';
            
            $plugin_slug = 'advanced-custom-style-manage-center';
            $current_version = JJ_STYLE_GUIDE_VERSION;
            $license_key = get_option( 'jj_style_guide_license_key', '' );
            $license_type = defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ? JJ_STYLE_GUIDE_LICENSE_TYPE : 'FREE';
            
            if ( class_exists( 'JJ_Plugin_Updater' ) ) {
                $updater = new JJ_Plugin_Updater(
                    JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php',
                    $plugin_slug,
                    'JJ\'s Center of Style Setting',
                    $current_version,
                    $license_type
                );
                
                // 업데이트 정보 가져오기
                $update_info = $updater->get_update_info();
                
                if ( $update_info && isset( $update_info['new_version'] ) ) {
                    if ( version_compare( $current_version, $update_info['new_version'], '<' ) ) {
                        wp_send_json_success( array(
                            'has_update' => true,
                            'current_version' => $current_version,
                            'new_version' => $update_info['new_version'],
                            'message' => sprintf( __( '새 버전 %s이(가) 사용 가능합니다.', 'jj-style-guide' ), $update_info['new_version'] ),
                        ) );
                    } else {
                        wp_send_json_success( array(
                            'has_update' => false,
                            'message' => __( '최신 버전을 사용 중입니다.', 'jj-style-guide' ),
                        ) );
                    }
                } else {
                    wp_send_json_error( array( 'message' => __( '업데이트 정보를 가져올 수 없습니다.', 'jj-style-guide' ) ) );
                }
            } else {
                wp_send_json_error( array( 'message' => __( '업데이터 클래스를 로드할 수 없습니다.', 'jj-style-guide' ) ) );
            }
        } else {
            wp_send_json_error( array( 'message' => __( '업데이터 파일을 찾을 수 없습니다.', 'jj-style-guide' ) ) );
        }
    }

    /**
     * [v4.0.1 신규] AJAX: Admin Center 설정 저장
     */
    public function ajax_save_admin_center_settings() {
        check_ajax_referer( 'jj_admin_center_save_action', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }

        try {
            // [Phase 5.2] Webhook/자동화용 변경 전 스냅샷
            $old_snapshot = array();
            if ( function_exists( 'get_option' ) ) {
                $old_snapshot = array(
                    'admin_texts'        => get_option( $this->option_key, array() ),
                    'section_layout'     => get_option( $this->sections_option_key, array() ),
                    'admin_menu_layout'  => get_option( $this->menu_option_key, array() ),
                    'admin_menu_colors'  => get_option( $this->colors_option_key, array() ),
                    'update_settings'    => get_option( 'jj_style_guide_update_settings', array() ),
                    'visual_options'     => get_option( 'jj_style_guide_visual_options', array() ),
                    'webhooks'           => get_option( 'jj_style_guide_webhooks', array() ),
                );
            }

            // 1) 텍스트 필드 저장
            $raw_texts   = isset( $_POST['jj_admin_texts'] ) && is_array( $_POST['jj_admin_texts'] ) ? wp_unslash( $_POST['jj_admin_texts'] ) : array();
            $clean_texts = array();
            foreach ( $raw_texts as $key => $value ) {
                $clean_texts[ sanitize_key( $key ) ] = sanitize_textarea_field( $value );
            }
            update_option( $this->option_key, $clean_texts );

            // 2) 섹션 레이아웃 저장
            $raw_sections   = isset( $_POST['jj_section_layout'] ) && is_array( $_POST['jj_section_layout'] ) ? wp_unslash( $_POST['jj_section_layout'] ) : array();
            $clean_sections = array();
            foreach ( $this->get_default_sections_layout() as $slug => $meta ) {
                $enabled = isset( $raw_sections[ $slug ]['enabled'] ) && '1' === $raw_sections[ $slug ]['enabled'];
                $order   = isset( $raw_sections[ $slug ]['order'] ) ? intval( $raw_sections[ $slug ]['order'] ) : (int) $meta['order'];
                $clean_sections[ $slug ] = array(
                    'enabled' => $enabled ? 1 : 0,
                    'order'   => $order,
                );
            }
            update_option( $this->sections_option_key, $clean_sections );
            
            // [v5.0.0] 캐시 플러시
            self::flush_sections_layout_cache();

            // 3) 관리자 메뉴 레이아웃 저장
            $raw_menu   = isset( $_POST['jj_admin_menu_layout'] ) && is_array( $_POST['jj_admin_menu_layout'] ) ? wp_unslash( $_POST['jj_admin_menu_layout'] ) : array();
            $clean_menu = array();
            foreach ( $raw_menu as $slug => $meta ) {
                $slug    = sanitize_key( $slug );
                $enabled = isset( $meta['enabled'] ) && '1' === $meta['enabled'];
                $order   = isset( $meta['order'] ) ? intval( $meta['order'] ) : 0;
                $label   = isset( $meta['label'] ) ? sanitize_text_field( $meta['label'] ) : '';
                $icon    = isset( $meta['icon'] ) ? sanitize_text_field( $meta['icon'] ) : '';
                $capability = isset( $meta['capability'] ) ? sanitize_text_field( $meta['capability'] ) : '';
                
                // 서브메뉴 저장
                $submenus = array();
                if ( isset( $meta['submenus'] ) && is_array( $meta['submenus'] ) ) {
                    foreach ( $meta['submenus'] as $submenu_slug => $submenu_meta ) {
                        $submenu_slug = sanitize_key( $submenu_slug );
                        $submenu_enabled = isset( $submenu_meta['enabled'] ) && '1' === $submenu_meta['enabled'];
                        $submenu_label = isset( $submenu_meta['label'] ) ? sanitize_text_field( $submenu_meta['label'] ) : '';
                        $submenu_order = isset( $submenu_meta['order'] ) ? intval( $submenu_meta['order'] ) : 0;
                        $submenu_capability = isset( $submenu_meta['capability'] ) ? sanitize_text_field( $submenu_meta['capability'] ) : '';
                        
                        $submenus[ $submenu_slug ] = array(
                            'enabled' => $submenu_enabled ? 1 : 0,
                            'label'   => $submenu_label,
                            'order'   => $submenu_order,
                            'capability' => $submenu_capability,
                        );
                    }
                }
                
                $clean_menu[ $slug ] = array(
                    'enabled' => $enabled ? 1 : 0,
                    'order'   => $order,
                    'label'   => $label,
                    'icon'    => $icon,
                    'capability' => $capability,
                    'submenus' => $submenus,
                );
            }
            update_option( $this->menu_option_key, $clean_menu );

            // 4) 업데이트 설정 저장
            if ( isset( $_POST['jj_update_settings'] ) && is_array( $_POST['jj_update_settings'] ) ) {
                $raw_update_settings = wp_unslash( $_POST['jj_update_settings'] );
                $clean_update_settings = array(
                    'auto_update_enabled' => isset( $raw_update_settings['auto_update_enabled'] ) && '1' === $raw_update_settings['auto_update_enabled'],
                    'update_channel' => isset( $raw_update_settings['update_channel'] ) ? sanitize_text_field( $raw_update_settings['update_channel'] ) : 'stable',
                    'beta_updates_enabled' => isset( $raw_update_settings['beta_updates_enabled'] ) && '1' === $raw_update_settings['beta_updates_enabled'],
                    'send_app_logs' => isset( $raw_update_settings['send_app_logs'] ) && '1' === $raw_update_settings['send_app_logs'],
                    'send_error_logs' => isset( $raw_update_settings['send_error_logs'] ) && '1' === $raw_update_settings['send_error_logs'],
                );
                
                // 마스터 버전은 모든 로그를 반드시 전송
                $is_master_version = false;
                    if ( class_exists( 'JJ_Edition_Controller' ) ) {
                        $is_master_version = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                    }
                if ( $is_master_version ) {
                    $clean_update_settings['send_app_logs'] = true;
                    $clean_update_settings['send_error_logs'] = true;
                }
                
                // 업데이트 채널 유효성 검사
                $allowed_channels = array( 'stable', 'beta', 'test', 'dev' );
                if ( ! in_array( $clean_update_settings['update_channel'], $allowed_channels, true ) ) {
                    $clean_update_settings['update_channel'] = 'stable';
                }
                
                update_option( 'jj_style_guide_update_settings', $clean_update_settings );
            }

            // [v5.6.0] Visual Command Center 설정 저장
            if ( isset( $_POST['visual_options'] ) && is_array( $_POST['visual_options'] ) ) {
                $visual_options = array_map( 'sanitize_text_field', wp_unslash( $_POST['visual_options'] ) );
                update_option( 'jj_style_guide_visual_options', $visual_options );
            }

            // [Phase 5.2] Webhook 설정 저장
            if ( isset( $_POST['jj_webhooks'] ) && is_array( $_POST['jj_webhooks'] ) ) {
                $raw_webhooks = wp_unslash( $_POST['jj_webhooks'] );
                $existing_webhooks = function_exists( 'get_option' ) ? get_option( 'jj_style_guide_webhooks', array() ) : array();
                if ( ! is_array( $existing_webhooks ) ) {
                    $existing_webhooks = array();
                }

                $enabled = isset( $raw_webhooks['enabled'] ) && '1' === $raw_webhooks['enabled'];

                $payload_mode = ( isset( $raw_webhooks['payload_mode'] ) && 'full' === $raw_webhooks['payload_mode'] ) ? 'full' : 'minimal';
                $timeout_seconds = isset( $raw_webhooks['timeout_seconds'] ) ? (int) $raw_webhooks['timeout_seconds'] : 5;
                $timeout_seconds = max( 1, min( 30, $timeout_seconds ) );

                // endpoints: textarea (줄바꿈)
                $endpoints_text = isset( $raw_webhooks['endpoints'] ) ? (string) $raw_webhooks['endpoints'] : '';
                $lines = preg_split( "/\\r\\n|\\r|\\n/", $endpoints_text );
                $endpoints = array();
                if ( is_array( $lines ) ) {
                    foreach ( $lines as $line ) {
                        $line = trim( (string) $line );
                        if ( '' === $line ) {
                            continue;
                        }
                        $url = function_exists( 'esc_url_raw' ) ? esc_url_raw( $line ) : $line;
                        if ( $url ) {
                            $endpoints[] = $url;
                        }
                    }
                }
                $endpoints = array_values( array_unique( $endpoints ) );

                // events
                $allowed_events = array( 'style_settings_updated', 'admin_center_updated' );
                $events = array();
                if ( isset( $raw_webhooks['events'] ) && is_array( $raw_webhooks['events'] ) ) {
                    foreach ( $raw_webhooks['events'] as $evt ) {
                        $evt = sanitize_text_field( (string) $evt );
                        if ( in_array( $evt, $allowed_events, true ) ) {
                            $events[] = $evt;
                        }
                    }
                }
                $events = array_values( array_unique( $events ) );

                // secret: 비워두면 기존 유지, clear_secret 체크 시 초기화
                $secret = isset( $existing_webhooks['secret'] ) ? (string) $existing_webhooks['secret'] : '';
                if ( isset( $raw_webhooks['clear_secret'] ) && '1' === $raw_webhooks['clear_secret'] ) {
                    $secret = '';
                } elseif ( isset( $raw_webhooks['secret'] ) ) {
                    $incoming_secret = trim( (string) $raw_webhooks['secret'] );
                    if ( '' !== $incoming_secret ) {
                        $secret = sanitize_text_field( $incoming_secret );
                    }
                }

                $clean_webhooks = array(
                    'enabled'         => $enabled,
                    'endpoints'       => $endpoints,
                    'secret'          => $secret,
                    'events'          => $events,
                    'payload_mode'    => $payload_mode,
                    'timeout_seconds' => $timeout_seconds,
                );

                update_option( 'jj_style_guide_webhooks', $clean_webhooks );
            }
            
            // 5) 관리자 메뉴/상단바 색상 저장
            $raw_colors   = isset( $_POST['jj_admin_menu_colors'] ) && is_array( $_POST['jj_admin_menu_colors'] ) ? wp_unslash( $_POST['jj_admin_menu_colors'] ) : array();
            $clean_colors = array();
            foreach ( $this->get_default_admin_colors() as $key => $default_hex ) {
                $raw = isset( $raw_colors[ $key ] ) ? $raw_colors[ $key ] : '';
                $san = sanitize_hex_color( $raw );
                $clean_colors[ $key ] = $san ? $san : $default_hex;
            }
            update_option( $this->colors_option_key, $clean_colors );

            // [Phase 5.2] Webhook/자동화용 Admin Center 변경 이벤트 트리거
            if ( function_exists( 'do_action' ) && function_exists( 'get_option' ) ) {
                $new_snapshot = array(
                    'admin_texts'        => get_option( $this->option_key, array() ),
                    'section_layout'     => get_option( $this->sections_option_key, array() ),
                    'admin_menu_layout'  => get_option( $this->menu_option_key, array() ),
                    'admin_menu_colors'  => get_option( $this->colors_option_key, array() ),
                    'update_settings'    => get_option( 'jj_style_guide_update_settings', array() ),
                    'visual_options'     => get_option( 'jj_style_guide_visual_options', array() ),
                    'webhooks'           => get_option( 'jj_style_guide_webhooks', array() ),
                );
                do_action( 'jj_style_guide_admin_center_updated', $new_snapshot, $old_snapshot, 'admin_center_ajax' );
            }

            wp_send_json_success( array( 'message' => __( '관리자 센터 설정이 저장되었습니다.', 'jj-style-guide' ) ) );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'message' => __( '저장 중 오류가 발생했습니다: ', 'jj-style-guide' ) . $e->getMessage() ) );
        }
    }

    /**
     * [Phase 6] AJAX: 자가 진단 실행
     */
    public function ajax_run_self_test() {
        check_ajax_referer( 'jj_admin_center_save_action', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        if ( ! class_exists( 'JJ_Self_Tester' ) ) {
            if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
                $tester_path = JJ_STYLE_GUIDE_PATH . 'tests/class-jj-self-tester.php';
                if ( file_exists( $tester_path ) ) {
                    require_once $tester_path;
                }
            }
        }

        if ( class_exists( 'JJ_Self_Tester' ) ) {
            $results = JJ_Self_Tester::run_tests();
            wp_send_json_success( array( 'results' => $results ) );
        } else {
            wp_send_json_error( array( 'message' => __( '테스터 클래스를 로드할 수 없습니다.', 'jj-style-guide' ) ) );
        }
    }

    /**
     * [v8.0.0] Bulk Installer: 파일 업로드 핸들러
     */
    public function ajax_handle_bulk_upload() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'install_plugins' ) ) wp_send_json_error( '권한이 없습니다.' );
        if ( empty( $_FILES['file'] ) ) wp_send_json_error( '파일이 없습니다.' );

        $file = $_FILES['file'];
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/jj-bulk-temp/';
        if ( ! file_exists( $temp_dir ) ) wp_mkdir_p( $temp_dir );
        
        $target_path = $temp_dir . basename( $file['name'] );
        
        if ( move_uploaded_file( $file['tmp_name'], $target_path ) ) {
            wp_send_json_success( array( 
                'path' => $target_path,
                'name' => $file['name'],
                'type' => $this->detect_bulk_type( $target_path )
            ) );
        } else {
            wp_send_json_error( '파일 업로드 실패' );
        }
    }

    /**
     * [v8.0.0] Bulk Installer: 설치 핸들러
     */
    public function ajax_handle_bulk_install() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'install_plugins' ) ) wp_send_json_error( '권한이 없습니다.' );
        
        $file_path = isset( $_POST['path'] ) ? sanitize_text_field( $_POST['path'] ) : '';
        $type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'plugin';
        $auto_activate = isset( $_POST['activate'] ) && 'true' === $_POST['activate'];

        if ( ! file_exists( $file_path ) ) wp_send_json_error( '파일을 찾을 수 없습니다.' );

        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        include_once ABSPATH . 'wp-admin/includes/theme.php';

        ob_start();
        $skin = new WP_Ajax_Upgrader_Skin();
        $upgrader = ( $type === 'theme' ) ? new Theme_Upgrader( $skin ) : new Plugin_Upgrader( $skin );
        $result = $upgrader->install( $file_path );
        ob_end_clean();

        if ( is_wp_error( $result ) ) {
            @unlink( $file_path );
            wp_send_json_error( $result->get_error_message() );
        }

        $plugin_slug = ( $type === 'plugin' ) ? $upgrader->plugin_info() : '';
        @unlink( $file_path );

        $response = array( 'status' => 'installed', 'slug' => $plugin_slug );
        if ( $auto_activate && $plugin_slug ) {
            $activate_result = activate_plugin( $plugin_slug );
            $response['activated'] = ! is_wp_error( $activate_result );
        }

        wp_send_json_success( $response );
    }

    /**
     * [v8.0.0] Bulk Installer: 활성화 핸들러
     */
    public function ajax_handle_bulk_activate() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'install_plugins' ) ) wp_send_json_error( '권한이 없습니다.' );
        
        $slug = isset( $_POST['slug'] ) ? sanitize_text_field( $_POST['slug'] ) : '';
        if ( ! $slug ) wp_send_json_error( '플러그인 정보가 없습니다.' );

        $result = activate_plugin( $slug );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }
        wp_send_json_success( '활성화됨' );
    }

    /**
     * [v8.0.0] Bulk Installer: 파일 타입 감지
     */
    private function detect_bulk_type( $zip_path ) {
        if ( ! class_exists( 'ZipArchive' ) ) return 'plugin';
        $zip = new ZipArchive;
        if ( $zip->open( $zip_path ) === TRUE ) {
            for ( $i = 0; $i < $zip->numFiles; $i++ ) {
                $filename = $zip->getNameIndex( $i );
                if ( strpos( $filename, 'style.css' ) !== false && substr_count( $filename, '/' ) <= 1 ) {
                    $content = $zip->getFromIndex( $i );
                    if ( strpos( $content, 'Theme Name:' ) !== false ) {
                        $zip->close();
                        return 'theme';
                    }
                }
            }
            $zip->close();
        }
        return 'plugin';
    }
}

if ( ! function_exists( 'jj_style_guide_text' ) ) {
    /**
     * 헬퍼 함수: 관리자 센터에서 정의한 텍스트를 가져옵니다.
     */
    function jj_style_guide_text( $key, $fallback = '' ) {
        return JJ_Admin_Center::instance()->get_text( $key, $fallback );
    }
}

if ( ! function_exists( 'jj_style_guide_sections_layout' ) ) {
    /**
     * 헬퍼: 섹션 레이아웃 전체를 반환
     *
     * @return array
     */
    function jj_style_guide_sections_layout() {
        return JJ_Admin_Center::instance()->get_sections_layout();
    }
}

if ( ! function_exists( 'jj_style_guide_section_index' ) ) {
    /**
     * 헬퍼: 특정 섹션의 1부터 시작하는 표시 순서 인덱스 반환
     *
     * @param string $slug
     * @return int|null
     */
    function jj_style_guide_section_index( $slug ) {
        return JJ_Admin_Center::instance()->get_section_index( $slug );
    }
}

// [v5.0.0] 헬퍼: 특정 섹션의 특정 탭이 활성화되어 있는지 확인
if ( ! function_exists( 'jj_style_guide_is_tab_enabled' ) ) {
    /**
     * 헬퍼: 특정 섹션의 특정 탭이 활성화되어 있는지 확인
     *
     * @param string $section_slug 섹션 슬러그
     * @param string $tab_slug 탭 슬러그
     * @return bool
     */
    function jj_style_guide_is_tab_enabled( $section_slug, $tab_slug ) {
        return JJ_Admin_Center::instance()->is_tab_enabled( $section_slug, $tab_slug );
    }
}

```

### 다음 라운드 완료했습니다 (커밋 + 배포 갱신 포함)

## 1) Updates 탭(코어+애드온 1테이블) — WP 플러그인 목록 UX로 “마감”
- **검색/필터 추가**: `플러그인 검색`, `미설치 숨기기`, `업데이트만 보기`
- **업데이트 체크 시각 표시**: **마지막 체크 / 다음 체크** + `워드프레스 업데이트` 바로가기
- **실전 액션 추가**
  - **활성화/비활성화** 버튼(WordPress 코어 nonce 링크)
  - 플러그인별 **자동 업데이트 토글**(기존 AJAX 유지)
- **필수 플러그인 상태 배지**: 예) Woo License 행에 **WooCommerce 설치/활성 상태**를 배지로 표시

관련 파일:
- `acf-css-really-simple-style-management-center-master/includes/admin/views/tabs/tab-updates.php`
- `acf-css-really-simple-style-management-center-master/assets/js/jj-admin-center.js`
- `acf-css-really-simple-style-management-center-master/includes/class-jj-admin-center.php` (캐시/버전)

## 2) Self-test(자가 진단) — “원인 파일”이 바로 보이도록 확장
- Safe Loader에서 잡힌 **필수 파일 누락/로드 오류를 파일 단위로** 결과 리스트에 그대로 출력(최대 30개까지)

관련 파일:
- `acf-css-really-simple-style-management-center-master/tests/class-jj-self-tester.php`

## 3) 캐시 근본 원인 제거(관리자센터 자산)
- `jj-admin-center.css/js`는 **filemtime 기반 버전**을 사용하도록 변경해서, 업데이트 후에도 관리자 화면이 **캐시로 안 바뀌는 문제를 구조적으로 차단**했습니다.

## 4) 커밋 + 배포 갱신
- **커밋 완료**: `6955af1` (이번 라운드)
- **배포 갱신 완료**: `python jj_deployment_system.py` 실행 →  
  `C:\Users\computer\Desktop\JJ_Distributions_v8.0.0_Master_Control\dashboard.html` 최신화
