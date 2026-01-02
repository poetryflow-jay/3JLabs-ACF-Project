<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 22] 업데이트 채널 관리자
 * 
 * 베타 테스트 동의, 업데이트 채널 선택, 순차적 배포 관리
 * 
 * @since 20.1.0
 */
class JJ_Update_Channel_Manager {

    private static $instance = null;
    private $option_key = 'jj_update_channel_settings';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // Admin Center 설정에 업데이트 채널 섹션 추가
        add_action( 'jj_admin_center_settings_section', array( $this, 'render_update_channel_settings' ), 20 );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_save_update_channel', array( $this, 'ajax_save_update_channel' ) );
        add_action( 'wp_ajax_jj_toggle_beta_participation', array( $this, 'ajax_toggle_beta_participation' ) );
        
        // 업데이트 체크 필터
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'filter_updates_by_channel' ), 15 );
        
        // 플러그인 목록 페이지에 베타 참여 링크 추가
        add_filter( 'plugin_row_meta', array( $this, 'add_beta_participation_link' ), 20, 2 );
        
        // 관리자 페이지에 베타 테스트 공지 표시
        add_action( 'admin_notices', array( $this, 'show_beta_notice' ) );
    }

    /**
     * 기본 설정 가져오기
     */
    private function get_default_settings() {
        return array(
            'update_channel' => 'stable',       // stable, beta, dev
            'beta_participation' => false,       // 베타 테스트 참여 동의
            'beta_consent_date' => null,         // 베타 동의 일시
            'auto_update_enabled' => true,       // 자동 업데이트 활성화
            'rollout_group' => null,             // 순차 배포 그룹 (A/B/C)
            'receive_beta_notifications' => true, // 베타 업데이트 알림 수신
            'last_update_check' => null,
        );
    }

    /**
     * 설정 가져오기
     */
    public function get_settings() {
        $settings = get_option( $this->option_key, array() );
        return wp_parse_args( $settings, $this->get_default_settings() );
    }

    /**
     * 설정 저장
     */
    public function save_settings( $settings ) {
        $current = $this->get_settings();
        $new_settings = wp_parse_args( $settings, $current );
        return update_option( $this->option_key, $new_settings );
    }

    /**
     * 현재 업데이트 채널 가져오기
     */
    public function get_update_channel() {
        $settings = $this->get_settings();
        return $settings['update_channel'];
    }

    /**
     * 베타 참여 여부 확인
     */
    public function is_beta_participant() {
        $settings = $this->get_settings();
        return (bool) $settings['beta_participation'];
    }

    /**
     * 순차 배포 그룹 할당
     */
    public function get_rollout_group() {
        $settings = $this->get_settings();
        
        if ( empty( $settings['rollout_group'] ) ) {
            // 사이트 URL 해시로 그룹 할당 (A/B/C 3개 그룹)
            $site_hash = md5( home_url() );
            $group_index = hexdec( substr( $site_hash, 0, 2 ) ) % 3;
            $groups = array( 'A', 'B', 'C' );
            $settings['rollout_group'] = $groups[ $group_index ];
            $this->save_settings( $settings );
        }
        
        return $settings['rollout_group'];
    }

    /**
     * 업데이트 채널 설정 UI 렌더링
     */
    public function render_update_channel_settings() {
        $settings = $this->get_settings();
        $nonce = wp_create_nonce( 'jj_update_channel_nonce' );
        $rollout_group = $this->get_rollout_group();
        ?>
        <div class="jj-settings-section" id="jj-update-channel-section">
            <h2><?php esc_html_e( '🔄 업데이트 채널 설정', 'acf-css-really-simple-style-management-center' ); ?></h2>
            <p class="description">
                <?php esc_html_e( '업데이트 수신 방식을 설정합니다. 베타 채널을 선택하면 새로운 기능을 먼저 테스트할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
            </p>
            
            <table class="form-table" role="presentation">
                <tbody>
                    <!-- 업데이트 채널 선택 -->
                    <tr>
                        <th scope="row">
                            <label for="jj-update-channel"><?php esc_html_e( '업데이트 채널', 'acf-css-really-simple-style-management-center' ); ?></label>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php esc_html_e( '업데이트 채널', 'acf-css-really-simple-style-management-center' ); ?></legend>
                                
                                <label style="display: block; margin-bottom: 12px; padding: 12px; border: 2px solid <?php echo $settings['update_channel'] === 'stable' ? '#00a32a' : '#c3c4c7'; ?>; border-radius: 8px; background: <?php echo $settings['update_channel'] === 'stable' ? '#f0fff4' : '#fff'; ?>;">
                                    <input type="radio" name="jj_update_channel" value="stable" <?php checked( $settings['update_channel'], 'stable' ); ?>>
                                    <strong style="font-size: 14px;">🟢 <?php esc_html_e( 'Stable (안정)', 'acf-css-really-simple-style-management-center' ); ?></strong>
                                    <p class="description" style="margin: 8px 0 0 24px;">
                                        <?php esc_html_e( '정식 릴리스된 안정적인 버전만 수신합니다. 대부분의 사용자에게 권장됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
                                    </p>
                                </label>
                                
                                <label style="display: block; margin-bottom: 12px; padding: 12px; border: 2px solid <?php echo $settings['update_channel'] === 'beta' ? '#ff9500' : '#c3c4c7'; ?>; border-radius: 8px; background: <?php echo $settings['update_channel'] === 'beta' ? '#fffbeb' : '#fff'; ?>;">
                                    <input type="radio" name="jj_update_channel" value="beta" <?php checked( $settings['update_channel'], 'beta' ); ?>>
                                    <strong style="font-size: 14px;">🟡 <?php esc_html_e( 'Beta (베타)', 'acf-css-really-simple-style-management-center' ); ?></strong>
                                    <p class="description" style="margin: 8px 0 0 24px;">
                                        <?php esc_html_e( '새로운 기능을 먼저 테스트합니다. 일부 버그가 있을 수 있습니다. 피드백을 주시면 개발에 도움이 됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
                                    </p>
                                </label>
                                
                                <label style="display: block; padding: 12px; border: 2px solid <?php echo $settings['update_channel'] === 'dev' ? '#ff3b30' : '#c3c4c7'; ?>; border-radius: 8px; background: <?php echo $settings['update_channel'] === 'dev' ? '#fff5f5' : '#fff'; ?>;">
                                    <input type="radio" name="jj_update_channel" value="dev" <?php checked( $settings['update_channel'], 'dev' ); ?>>
                                    <strong style="font-size: 14px;">🔴 <?php esc_html_e( 'Dev (개발)', 'acf-css-really-simple-style-management-center' ); ?></strong>
                                    <p class="description" style="margin: 8px 0 0 24px;">
                                        <?php esc_html_e( '최신 개발 버전을 수신합니다. 불안정할 수 있으며, 개발자 및 테스터에게만 권장됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
                                    </p>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    
                    <!-- 베타 테스트 참여 동의 -->
                    <tr>
                        <th scope="row">
                            <label for="jj-beta-participation"><?php esc_html_e( '베타 테스트 참여', 'acf-css-really-simple-style-management-center' ); ?></label>
                        </th>
                        <td>
                            <label for="jj-beta-participation" style="display: flex; align-items: flex-start; gap: 10px;">
                                <input type="checkbox" id="jj-beta-participation" name="jj_beta_participation" value="1" <?php checked( $settings['beta_participation'] ); ?>>
                                <span>
                                    <strong><?php esc_html_e( '베타 테스트 프로그램에 참여합니다', 'acf-css-really-simple-style-management-center' ); ?></strong>
                                    <p class="description" style="margin-top: 4px;">
                                        <?php esc_html_e( '참여 시 새로운 기능을 먼저 사용하고, 버그 발견 시 보고할 수 있습니다. 참여자에게는 특별 혜택이 제공될 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                                    </p>
                                </span>
                            </label>
                            <?php if ( $settings['beta_participation'] && $settings['beta_consent_date'] ) : ?>
                                <p class="description" style="margin-top: 8px; color: #00a32a;">
                                    ✅ <?php printf( esc_html__( '베타 테스트 참여 동의일: %s', 'acf-css-really-simple-style-management-center' ), date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $settings['beta_consent_date'] ) ) ); ?>
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <!-- 자동 업데이트 설정 -->
                    <tr>
                        <th scope="row">
                            <label for="jj-auto-update"><?php esc_html_e( '자동 업데이트', 'acf-css-really-simple-style-management-center' ); ?></label>
                        </th>
                        <td>
                            <label for="jj-auto-update">
                                <input type="checkbox" id="jj-auto-update" name="jj_auto_update_enabled" value="1" <?php checked( $settings['auto_update_enabled'] ); ?>>
                                <?php esc_html_e( 'WordPress 자동 업데이트 허용', 'acf-css-really-simple-style-management-center' ); ?>
                            </label>
                            <p class="description">
                                <?php esc_html_e( '활성화하면 WordPress가 백그라운드에서 자동으로 플러그인을 업데이트합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- 베타 알림 수신 -->
                    <tr>
                        <th scope="row">
                            <label for="jj-beta-notifications"><?php esc_html_e( '베타 알림 수신', 'acf-css-really-simple-style-management-center' ); ?></label>
                        </th>
                        <td>
                            <label for="jj-beta-notifications">
                                <input type="checkbox" id="jj-beta-notifications" name="jj_receive_beta_notifications" value="1" <?php checked( $settings['receive_beta_notifications'] ); ?>>
                                <?php esc_html_e( '베타 업데이트 및 테스트 요청 알림 수신', 'acf-css-really-simple-style-management-center' ); ?>
                            </label>
                            <p class="description">
                                <?php esc_html_e( '새로운 베타 버전이 출시되면 관리자 페이지에 알림이 표시됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- 순차 배포 그룹 정보 -->
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( '순차 배포 그룹', 'acf-css-really-simple-style-management-center' ); ?>
                        </th>
                        <td>
                            <span style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: #f0f0f1; border-radius: 20px; font-weight: 600;">
                                <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: <?php echo $rollout_group === 'A' ? '#00a32a' : ( $rollout_group === 'B' ? '#007aff' : '#9b59b6' ); ?>;"></span>
                                <?php printf( esc_html__( '그룹 %s', 'acf-css-really-simple-style-management-center' ), esc_html( $rollout_group ) ); ?>
                            </span>
                            <p class="description" style="margin-top: 8px;">
                                <?php esc_html_e( '순차 배포 시 그룹 A → B → C 순서로 업데이트가 배포됩니다. 그룹은 사이트 URL을 기반으로 자동 할당됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <p class="submit">
                <button type="button" id="jj-save-update-channel" class="button button-primary" data-nonce="<?php echo esc_attr( $nonce ); ?>">
                    <?php esc_html_e( '설정 저장', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
                <span id="jj-update-channel-status" style="margin-left: 10px;"></span>
            </p>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#jj-save-update-channel').on('click', function() {
                var $btn = $(this);
                var $status = $('#jj-update-channel-status');
                
                $btn.prop('disabled', true).text('<?php esc_html_e( '저장 중...', 'acf-css-really-simple-style-management-center' ); ?>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'jj_save_update_channel',
                        nonce: $btn.data('nonce'),
                        update_channel: $('input[name="jj_update_channel"]:checked').val(),
                        beta_participation: $('#jj-beta-participation').is(':checked') ? 1 : 0,
                        auto_update_enabled: $('#jj-auto-update').is(':checked') ? 1 : 0,
                        receive_beta_notifications: $('#jj-beta-notifications').is(':checked') ? 1 : 0
                    },
                    success: function(response) {
                        if (response.success) {
                            $status.html('<span style="color: #00a32a;">✅ ' + response.data.message + '</span>');
                            
                            // 자동 업데이트 상태 동기화
                            if (response.data.auto_update_synced) {
                                $status.append(' <em>(WordPress 설정 동기화됨)</em>');
                            }
                        } else {
                            $status.html('<span style="color: #d63638;">❌ ' + (response.data.message || '오류가 발생했습니다.') + '</span>');
                        }
                    },
                    error: function() {
                        $status.html('<span style="color: #d63638;">❌ 서버 통신 오류</span>');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('<?php esc_html_e( '설정 저장', 'acf-css-really-simple-style-management-center' ); ?>');
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * AJAX: 업데이트 채널 설정 저장
     */
    public function ajax_save_update_channel() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
        
        if ( ! wp_verify_nonce( $nonce, 'jj_update_channel_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( '보안 검증 실패', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        $settings = $this->get_settings();
        $old_beta = $settings['beta_participation'];
        
        $settings['update_channel'] = sanitize_text_field( $_POST['update_channel'] ?? 'stable' );
        $settings['beta_participation'] = (bool) ( $_POST['beta_participation'] ?? false );
        $settings['auto_update_enabled'] = (bool) ( $_POST['auto_update_enabled'] ?? false );
        $settings['receive_beta_notifications'] = (bool) ( $_POST['receive_beta_notifications'] ?? true );
        
        // 베타 참여가 새로 활성화되면 동의 일시 기록
        if ( $settings['beta_participation'] && ! $old_beta ) {
            $settings['beta_consent_date'] = current_time( 'mysql' );
        }
        
        // 베타 참여가 비활성화되면 동의 일시 삭제
        if ( ! $settings['beta_participation'] ) {
            $settings['beta_consent_date'] = null;
        }
        
        $this->save_settings( $settings );
        
        // WordPress 자동 업데이트 설정 동기화
        $auto_update_synced = false;
        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            $plugin_file = 'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php';
            $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
            
            if ( $settings['auto_update_enabled'] ) {
                if ( ! in_array( $plugin_file, $auto_updates, true ) ) {
                    $auto_updates[] = $plugin_file;
                    update_site_option( 'auto_update_plugins', $auto_updates );
                    $auto_update_synced = true;
                }
            } else {
                $key = array_search( $plugin_file, $auto_updates, true );
                if ( $key !== false ) {
                    unset( $auto_updates[ $key ] );
                    update_site_option( 'auto_update_plugins', array_values( $auto_updates ) );
                    $auto_update_synced = true;
                }
            }
        }
        
        wp_send_json_success( array(
            'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ),
            'auto_update_synced' => $auto_update_synced,
        ) );
    }

    /**
     * AJAX: 베타 참여 토글
     */
    public function ajax_toggle_beta_participation() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
        
        if ( ! wp_verify_nonce( $nonce, 'jj_toggle_beta_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( '보안 검증 실패', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        $settings = $this->get_settings();
        $settings['beta_participation'] = ! $settings['beta_participation'];
        
        if ( $settings['beta_participation'] ) {
            $settings['beta_consent_date'] = current_time( 'mysql' );
            $message = __( '베타 테스트 프로그램에 참여하셨습니다!', 'acf-css-really-simple-style-management-center' );
        } else {
            $settings['beta_consent_date'] = null;
            $message = __( '베타 테스트 프로그램에서 탈퇴하셨습니다.', 'acf-css-really-simple-style-management-center' );
        }
        
        $this->save_settings( $settings );
        
        wp_send_json_success( array(
            'message' => $message,
            'beta_participation' => $settings['beta_participation'],
        ) );
    }

    /**
     * 업데이트 채널에 따른 업데이트 필터링
     */
    public function filter_updates_by_channel( $transient ) {
        if ( empty( $transient->response ) ) {
            return $transient;
        }
        
        $settings = $this->get_settings();
        $channel = $settings['update_channel'];
        
        // ACF CSS 관련 플러그인 필터링
        $our_plugins = array(
            'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php',
            'acf-code-snippets-box/acf-code-snippets-box.php',
            'acf-css-woocommerce-toolkit/acf-css-woocommerce-toolkit.php',
            'acf-css-ai-extension/acf-css-ai-extension.php',
            'acf-nudge-flow/acf-nudge-flow.php',
            'wp-bulk-manager/wp-bulk-installer.php',
            'admin-menu-editor-pro/admin-menu-editor-pro.php',
        );
        
        foreach ( $our_plugins as $plugin ) {
            if ( isset( $transient->response[ $plugin ] ) ) {
                $update_info = $transient->response[ $plugin ];
                
                // 업데이트 채널 확인
                if ( isset( $update_info->update_channel ) ) {
                    $update_channel = $update_info->update_channel;
                    
                    // stable 채널 사용자는 beta/dev 업데이트 제외
                    if ( $channel === 'stable' && in_array( $update_channel, array( 'beta', 'dev' ), true ) ) {
                        unset( $transient->response[ $plugin ] );
                        continue;
                    }
                    
                    // beta 채널 사용자는 dev 업데이트 제외
                    if ( $channel === 'beta' && $update_channel === 'dev' ) {
                        unset( $transient->response[ $plugin ] );
                        continue;
                    }
                }
                
                // 순차 배포 확인
                if ( isset( $update_info->rollout_groups ) && is_array( $update_info->rollout_groups ) ) {
                    $my_group = $this->get_rollout_group();
                    
                    if ( ! in_array( $my_group, $update_info->rollout_groups, true ) ) {
                        // 아직 내 그룹에 배포되지 않음
                        unset( $transient->response[ $plugin ] );
                        continue;
                    }
                }
            }
        }
        
        return $transient;
    }

    /**
     * 플러그인 목록에 베타 참여 링크 추가
     */
    public function add_beta_participation_link( $plugin_meta, $plugin_file ) {
        // ACF CSS Manager 플러그인에만 추가
        if ( strpos( $plugin_file, 'acf-css-really-simple-style-guide' ) === false ) {
            return $plugin_meta;
        }
        
        $settings = $this->get_settings();
        $nonce = wp_create_nonce( 'jj_toggle_beta_nonce' );
        
        if ( $settings['beta_participation'] ) {
            $plugin_meta[] = sprintf(
                '<span style="color: #ff9500; font-weight: 700;" title="%s">🧪 %s</span>',
                esc_attr__( '현재 베타 테스트 프로그램에 참여 중입니다.', 'acf-css-really-simple-style-management-center' ),
                esc_html__( '베타 테스터', 'acf-css-really-simple-style-management-center' )
            );
        } else {
            $plugin_meta[] = sprintf(
                '<a href="%s" style="color: #007aff; font-weight: 600;" title="%s">🧪 %s</a>',
                esc_url( admin_url( 'options-general.php?page=jj-admin-center#jj-update-channel-section' ) ),
                esc_attr__( '베타 테스트 프로그램에 참여하세요!', 'acf-css-really-simple-style-management-center' ),
                esc_html__( '베타 참여', 'acf-css-really-simple-style-management-center' )
            );
        }
        
        return $plugin_meta;
    }

    /**
     * 베타 테스트 공지 표시
     */
    public function show_beta_notice() {
        $settings = $this->get_settings();
        
        // 베타 알림 수신이 꺼져있으면 표시 안 함
        if ( ! $settings['receive_beta_notifications'] ) {
            return;
        }
        
        // 베타 채널이 아니면 표시 안 함
        if ( $settings['update_channel'] !== 'beta' && $settings['update_channel'] !== 'dev' ) {
            return;
        }
        
        // 베타 버전 확인 (transient에서)
        $update_transient = get_site_transient( 'update_plugins' );
        $plugin_file = 'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php';
        
        if ( isset( $update_transient->response[ $plugin_file ] ) ) {
            $update_info = $update_transient->response[ $plugin_file ];
            
            if ( isset( $update_info->update_channel ) && in_array( $update_info->update_channel, array( 'beta', 'dev' ), true ) ) {
                ?>
                <div class="notice notice-info is-dismissible">
                    <p>
                        <strong>🧪 <?php esc_html_e( 'ACF CSS 베타 업데이트 가능', 'acf-css-really-simple-style-management-center' ); ?></strong>:
                        <?php printf(
                            esc_html__( '새로운 %s 버전 (%s)이 준비되었습니다. 업데이트를 진행해주세요!', 'acf-css-really-simple-style-management-center' ),
                            esc_html( $update_info->update_channel ),
                            esc_html( $update_info->new_version ?? '' )
                        ); ?>
                        <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>"><?php esc_html_e( '플러그인 페이지로 이동', 'acf-css-really-simple-style-management-center' ); ?></a>
                    </p>
                </div>
                <?php
            }
        }
    }
}

// 초기화
if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    JJ_Update_Channel_Manager::instance();
}
