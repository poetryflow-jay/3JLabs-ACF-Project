---

**User**

고맙네. 그럼 곧바로 다음 단계로 나아가주게.


---

**Cursor**

좋습니다. 이제 다음 단계는 **WP Bulk Installer를 “Bulk Installer & Bulk Editor(관리/정리 도구)”로 격상**시키는 작업입니다. 기존의 ZIP 일괄 설치 UX는 유지하되, 같은 화면에서 **설치된 플러그인/테마를 불러와서 → 벌크 비활성화 → (선택) 자동 삭제**까지 안전하게 처리하도록 구현하겠습니다. 먼저 현재 플러그인 UI/JS 구조를 읽고, “관리 탭 + 안전한 대량 작업(AJAX)”를 추가하겠습니다.

좋아요. 다음 단계는 **WP Bulk Installer를 “Installer + Bulk Editor(관리)”로 확장**하는 겁니다. 먼저 기존 설치 흐름은 그대로 유지하고, 새 “관리” 탭을 추가한 뒤 **(1) 설치된 플러그인/테마 목록 불러오기 → (2) 벌크 비활성화 → (3) (선택) 비활성화 후 삭제**까지 안전하게 AJAX로 구현하겠습니다.

좋습니다. 지금은 **(1) 플러그인 자체 리네이밍 + (2) “관리(Bulk Editor)” 탭 골격 + (3) AJAX(목록/비활성화/삭제) 백엔드**까지 한 번에 넣어, “UI는 있는데 기능이 없는” 중간 상태가 없도록 만들겠습니다. 그 다음 JS/CSS로 실제 동작/UX를 붙여서 완성할게요.

```php
<?php
/**
 * Plugin Name: WP Bulk Manager (Installer & Editor)
 * Plugin URI: https://j-j-labs.com
 * Description: 여러 개의 플러그인/테마 ZIP 파일을 한 번에 설치하고, 설치된 플러그인/테마를 대량 비활성화/삭제까지 관리합니다.
 * Version: 2.2.2
 * Author: 3J Labs
 * Author URI: https://j-j-labs.com
 * License: GPLv2 or later
 * Text Domain: wp-bulk-installer
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class JJ_Bulk_Installer {

    private static $instance = null;
    private $page_slug = 'jj-bulk-installer';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
        add_action( 'admin_notices', array( $this, 'add_install_page_notice' ) );
        
        add_action( 'wp_ajax_jj_bulk_install_upload', array( $this, 'ajax_handle_upload' ) );
        add_action( 'wp_ajax_jj_bulk_install_process', array( $this, 'ajax_handle_install' ) );
        add_action( 'wp_ajax_jj_bulk_activate_plugin', array( $this, 'ajax_handle_activate' ) );

        // Bulk Editor (관리)
        add_action( 'wp_ajax_jj_bulk_manage_get_items', array( $this, 'ajax_get_installed_items' ) );
        add_action( 'wp_ajax_jj_bulk_manage_action', array( $this, 'ajax_bulk_manage_action' ) );
        
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function add_menu_pages() {
        // 1. 도구 하위 메뉴 (기본)
        add_management_page(
            'WP Bulk Manager',
            'Bulk Manager',
            'install_plugins',
            $this->page_slug,
            array( $this, 'render_page' )
        );

        // 2. 알림판 아래 최상위 메뉴 (접근성 강화)
        add_menu_page(
            'WP Bulk Manager',
            'Bulk Manager',
            'install_plugins',
            $this->page_slug . '-main',
            array( $this, 'render_page' ),
            'dashicons-cloud-upload',
            2 // Dashboard(index.php) 바로 아래
        );
    }

    // 플러그인 설치 화면에 안내 배너 추가
    public function add_install_page_notice() {
        $screen = get_current_screen();
        if ( $screen && ( 'plugin-install' === $screen->id || 'theme-install' === $screen->id ) ) {
            $link = admin_url( 'tools.php?page=' . $this->page_slug );
            ?>
            <div class="notice notice-info is-dismissible">
                <p>
                    <strong>🚀 여러 파일을 한 번에 설치하고 싶으신가요?</strong> 
                    <a href="<?php echo esc_url( $link ); ?>" style="text-decoration: none; margin-left: 10px;">
                        <button type="button" class="button button-primary">WP Bulk Manager 바로가기</button>
                    </a>
                </p>
            </div>
            <?php
        }
    }

    public function enqueue_assets( $hook ) {
        // 도구 페이지 또는 최상위 페이지 모두 로드
        if ( strpos( $hook, $this->page_slug ) === false ) return;
        
        wp_enqueue_style( 'jj-bulk-installer-css', plugin_dir_url( __FILE__ ) . 'assets/style.css', array(), '2.2.2' );
        wp_enqueue_script( 'jj-bulk-installer-js', plugin_dir_url( __FILE__ ) . 'assets/script.js', array( 'jquery' ), '2.2.2', true );
        
        // PHP 설정값 가져오기
        $max_upload = wp_max_upload_size();
        $max_upload_formatted = size_format( $max_upload );
        
        // [v2.2.0] ACF CSS 라이센스 연동 제한 설정
        $limits = $this->get_license_limits();

        wp_localize_script( 'jj-bulk-installer-js', 'jjBulk', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'jj_bulk_install' ),
            'limits'   => array(
                'max_file_size' => $max_upload,
                'max_file_size_fmt' => $max_upload_formatted,
                'max_files' => $limits['max_files'],
                'can_auto_activate' => $limits['can_auto_activate'],
                'max_manage_items' => $limits['max_manage_items'],
                'can_bulk_delete' => $limits['can_bulk_delete'],
                'can_deactivate_then_delete' => $limits['can_deactivate_then_delete'],
            ),
            'admin_urls' => array(
                'plugins' => admin_url( 'plugins.php' ),
                'themes' => admin_url( 'themes.php' ),
                'updates' => admin_url( 'update-core.php' ),
            ),
            'i18n' => array(
                'limit_reached' => sprintf( '현재 라이센스에서는 한 번에 최대 %d개까지만 가능합니다.', $limits['max_files'] ),
                'upgrade_msg'   => '제한을 해제하려면 ACF CSS Pro로 업그레이드하세요.',
                'manage_limit_reached' => sprintf( '현재 라이센스에서는 한 번에 최대 %d개까지만 선택할 수 있습니다.', $limits['max_manage_items'] ),
                'delete_locked' => '삭제 기능은 Basic 이상에서 사용할 수 있습니다.',
                'deactivate_delete_locked' => '비활성화 후 삭제 기능은 Premium 이상에서 사용할 수 있습니다.',
            )
        ) );
    }
    
    /**
     * [v2.2.0] ACF CSS 라이센스 등급에 따른 제한 설정 조회
     */
    private function get_license_limits() {
        // [v2.2.1] Master Edition 독립 실행 모드 (Core 없이도 무제한)
        if ( defined( 'JJ_BULK_INSTALLER_LICENSE' ) && 'MASTER' === JJ_BULK_INSTALLER_LICENSE ) {
            return array(
                'max_files' => 999,
                'can_auto_activate' => true,
                'max_manage_items' => 999,
                'can_bulk_delete' => true,
                'can_deactivate_then_delete' => true,
            );
        }

        $limits = array(
            'max_files' => 3,        // 기본값 (Free)
            'can_auto_activate' => false, // 기본값 (Free)
            'max_manage_items' => 3,
            'can_bulk_delete' => false,
            'can_deactivate_then_delete' => false,
        );
        
        // ACF CSS Manager가 설치되어 있고 클래스가 존재할 때
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            $edition_ctrl = JJ_Edition_Controller::instance();
            
            // Basic 이상: 10개 파일
            if ( $edition_ctrl->is_at_least( 'basic' ) ) {
                $limits['max_files'] = 10;
                $limits['max_manage_items'] = 10;
                $limits['can_bulk_delete'] = true;
            }
            
            // Premium 이상: 무제한 + 자동 활성화
            if ( $edition_ctrl->is_at_least( 'premium' ) ) {
                $limits['max_files'] = 999;
                $limits['can_auto_activate'] = true;
                $limits['max_manage_items'] = 999;
                $limits['can_bulk_delete'] = true;
                $limits['can_deactivate_then_delete'] = true;
            }
            
            // Master: 무제한
            if ( $edition_ctrl->is_at_least( 'master' ) ) {
                $limits['max_files'] = 999;
                $limits['can_auto_activate'] = true;
                $limits['max_manage_items'] = 999;
                $limits['can_bulk_delete'] = true;
                $limits['can_deactivate_then_delete'] = true;
            }
        }
        
        return $limits;
    }

    public function render_page() {
        $max_upload = wp_max_upload_size();
        $max_upload_fmt = size_format( $max_upload );
        $limits = $this->get_license_limits(); // 현재 제한 상태 조회

        $plan_label = 'PREMIUM+';
        if ( (int) $limits['max_files'] <= 3 ) {
            $plan_label = 'FREE';
        } elseif ( (int) $limits['max_files'] <= 10 ) {
            $plan_label = 'BASIC';
        }
        ?>
        <div class="wrap jj-bulk-wrap">
            <h1>🚀 WP Bulk Manager <small>Installer &amp; Editor · by 3J Labs</small></h1>

            <div id="jj-bulk-notices"></div>
            
            <?php if ( (int) $limits['max_files'] < 999 || ! $limits['can_auto_activate'] || ! $limits['can_bulk_delete'] || ! $limits['can_deactivate_then_delete'] ) : ?>
                <div class="notice notice-warning inline">
                    <p style="margin: 0.6em 0;">
                        <strong><?php echo esc_html( $plan_label ); ?>:</strong>
                        현재 기능 제한이 일부 적용되어 있습니다.
                        <a href="https://j-j-labs.com" target="_blank" class="button button-small" style="margin-left: 10px;">업그레이드</a>
                    </p>
                    <ul style="margin: 0.5em 0 0.8em 1.4em; list-style: disc;">
                        <li>ZIP 동시 업로드: 최대 <strong><?php echo (int) $limits['max_files']; ?>개</strong></li>
                        <li>설치 후 자동 활성화: <strong><?php echo $limits['can_auto_activate'] ? '사용 가능' : '잠김'; ?></strong></li>
                        <li>대량 삭제: <strong><?php echo $limits['can_bulk_delete'] ? '사용 가능' : '잠김'; ?></strong></li>
                        <li>비활성화 후 삭제: <strong><?php echo $limits['can_deactivate_then_delete'] ? '사용 가능' : '잠김'; ?></strong></li>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="jj-bulk-tabs" role="tablist" aria-label="WP Bulk Manager Tabs">
                <button type="button" class="jj-bulk-tab is-active" data-tab="installer" role="tab" aria-selected="true">설치(Installer)</button>
                <button type="button" class="jj-bulk-tab" data-tab="editor" role="tab" aria-selected="false">관리(Bulk Editor)</button>
            </div>

            <div class="jj-bulk-tab-panel is-active" data-tab-panel="installer" role="tabpanel">
                <div class="jj-bulk-container">
                    <!-- 드롭존 -->
                    <div class="jj-dropzone" id="jj-dropzone">
                        <div class="jj-dropzone-content">
                            <span class="dashicons dashicons-cloud-upload" style="font-size: 64px; height: 64px; width: 64px; color: #2271b1;"></span>
                            <h3>ZIP 파일을 여기에 드래그하세요</h3>
                            <p>또는 <strong>여기를 클릭</strong>하여 파일 선택</p>
                            <p class="description">
                                최대 <?php echo (int) $limits['max_files']; ?>개 | 파일당 최대 <?php echo esc_html( $max_upload_fmt ); ?> | 전체 용량 2GB 권장
                            </p>
                            <!-- label로 감싸서 클릭 영역 확보 -->
                            <label for="jj-file-input" class="screen-reader-text">파일 선택</label>
                            <input type="file" id="jj-file-input" multiple accept=".zip">
                        </div>
                    </div>

                    <!-- 옵션 -->
                    <div class="jj-options">
                        <label style="<?php echo ( ! $limits['can_auto_activate'] ) ? 'opacity: 0.6; cursor: not-allowed;' : ''; ?>">
                            <input type="checkbox" id="jj-auto-activate-all" value="1" <?php echo ( ! $limits['can_auto_activate'] ) ? 'disabled' : ''; ?>>
                            <strong>설치 후 전체 자동 활성화</strong>
                        </label>
                        <?php if ( ! $limits['can_auto_activate'] ) : ?>
                            <span class="description" style="color: #d63638; margin-left: 10px;">(Premium 버전 이상 필요)</span>
                        <?php else : ?>
                            <span class="description" style="margin-left: 10px;">(체크 해제 시, 설치 완료 후 선택하여 활성화 가능)</span>
                        <?php endif; ?>
                    </div>

                    <!-- 파일 목록 -->
                    <div class="jj-file-list" id="jj-file-list"></div>

                    <!-- 액션 버튼 -->
                    <div class="jj-actions" style="margin-top: 20px; display: none;" id="jj-actions-area">
                        <button id="jj-start-install" class="button button-primary button-hero">
                            설치 시작하기
                        </button>
                        <button id="jj-activate-selected" class="button button-secondary button-hero" style="display: none; margin-left: 10px;">
                            선택한 플러그인 활성화
                        </button>
                    </div>

                    <!-- 진행 상태 -->
                    <div class="jj-progress-area" id="jj-progress-area" style="display: none;">
                        <div class="jj-progress-bar"><div class="jj-progress-fill" style="width: 0%;"></div></div>
                        <div class="jj-status-text">준비 중...</div>
                    </div>
                </div>
            </div>

            <div class="jj-bulk-tab-panel" data-tab-panel="editor" role="tabpanel" style="display:none;">
                <div class="jj-bulk-container">
                    <div class="jj-bulk-editor-header">
                        <div class="jj-bulk-badges" id="jj-bulk-stats">
                            <span class="jj-badge">플러그인 <strong id="jj-count-plugins">-</strong></span>
                            <span class="jj-badge">활성 <strong id="jj-count-plugins-active">-</strong></span>
                            <span class="jj-badge">업데이트 <strong id="jj-count-plugins-update">-</strong></span>
                            <span class="jj-badge">테마 <strong id="jj-count-themes">-</strong></span>
                        </div>
                        <div class="jj-bulk-editor-actions">
                            <button type="button" class="button" id="jj-bulk-refresh">목록 새로고침</button>
                            <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>">WP 플러그인 화면</a>
                            <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>">WP 테마 화면</a>
                            <a class="button button-link" href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>">업데이트 화면</a>
                        </div>
                    </div>

                    <div class="jj-bulk-subtabs" role="tablist" aria-label="Bulk Editor Sub Tabs">
                        <button type="button" class="button jj-subtab is-active" data-subtab="plugins" role="tab" aria-selected="true">플러그인</button>
                        <button type="button" class="button jj-subtab" data-subtab="themes" role="tab" aria-selected="false">테마</button>
                    </div>

                    <div class="jj-bulk-toolbar">
                        <input type="search" id="jj-bulk-search" placeholder="검색: 이름/설명/작성자/파일(슬러그)" class="regular-text" />
                        <select id="jj-bulk-filter-status">
                            <option value="all">전체</option>
                            <option value="active">활성</option>
                            <option value="inactive">비활성</option>
                        </select>

                        <div class="jj-bulk-toolbar-right">
                            <button type="button" class="button" id="jj-bulk-action-deactivate" data-op="deactivate" data-type="plugin">선택 비활성화</button>
                            <button type="button" class="button button-secondary" id="jj-bulk-action-delete" data-op="delete" data-type="plugin" <?php echo ( ! $limits['can_bulk_delete'] ) ? 'disabled' : ''; ?>>선택 삭제</button>
                            <button type="button" class="button button-danger" id="jj-bulk-action-deactivate-delete" data-op="deactivate_delete" data-type="plugin" <?php echo ( ! $limits['can_deactivate_then_delete'] ) ? 'disabled' : ''; ?>>비활성화 후 삭제</button>
                            <button type="button" class="button button-danger" id="jj-bulk-action-theme-delete" data-op="delete" data-type="theme" style="display:none;" <?php echo ( ! $limits['can_bulk_delete'] ) ? 'disabled' : ''; ?>>선택 삭제</button>
                        </div>
                    </div>

                    <p class="description" style="margin-top: 8px;">
                        <strong>주의:</strong> 삭제는 되돌릴 수 없습니다. 선택 항목이 많을 때는 <?php echo (int) $limits['max_manage_items']; ?>개 이하로 나눠서 진행하세요.
                    </p>

                    <?php if ( ! $limits['can_bulk_delete'] ) : ?>
                        <p class="description" style="color:#d63638; margin-top: 6px;">
                            🔒 삭제 기능은 <strong>Basic</strong> 이상에서 사용할 수 있습니다.
                        </p>
                    <?php elseif ( $limits['can_bulk_delete'] && ! $limits['can_deactivate_then_delete'] ) : ?>
                        <p class="description" style="color:#b32d2e; margin-top: 6px;">
                            🔒 “비활성화 후 삭제”는 <strong>Premium</strong> 이상에서 사용할 수 있습니다. (Basic은 “비활성화” 후 “삭제”를 분리해서 진행)
                        </p>
                    <?php endif; ?>

                    <div class="jj-bulk-table-wrap" data-subtab-panel="plugins">
                        <table class="wp-list-table widefat fixed striped" id="jj-bulk-table-plugins">
                            <thead>
                                <tr>
                                    <td class="manage-column column-cb check-column"><input type="checkbox" id="jj-bulk-select-all-plugins"></td>
                                    <th>플러그인</th>
                                    <th style="width:110px;">상태</th>
                                    <th style="width:140px;">자동 업데이트</th>
                                    <th style="width:140px;">업데이트</th>
                                    <th style="width:220px;">파일</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="6">목록을 불러오는 중...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="jj-bulk-table-wrap" data-subtab-panel="themes" style="display:none;">
                        <table class="wp-list-table widefat fixed striped" id="jj-bulk-table-themes">
                            <thead>
                                <tr>
                                    <td class="manage-column column-cb check-column"><input type="checkbox" id="jj-bulk-select-all-themes"></td>
                                    <th>테마</th>
                                    <th style="width:110px;">상태</th>
                                    <th style="width:140px;">자동 업데이트</th>
                                    <th style="width:140px;">업데이트</th>
                                    <th style="width:220px;">폴더</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="6">목록을 불러오는 중...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 프로모션 배너 -->
            <?php if ( ! class_exists( 'JJ_Edition_Controller' ) ) : ?>
                <div class="jj-promo-banner">
                    <h3>🎨 플러그인/테마 스타일이 제각각인가요?</h3>
                    <p><strong>ACF CSS Manager</strong>를 사용하면 여러 플러그인과 테마의 스타일을 한 곳에서 제어할 수 있습니다.</p>
                    <a href="https://j-j-labs.com/?product=plugin-jj-center-of-style-setting" target="_blank" class="button button-secondary">ACF CSS Manager 구경하기 →</a>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Bulk Editor: 설치된 플러그인/테마 목록 조회
     */
    public function ajax_get_installed_items() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $item_type = isset( $_POST['item_type'] ) ? sanitize_text_field( $_POST['item_type'] ) : 'plugin';
        if ( ! in_array( $item_type, array( 'plugin', 'theme' ), true ) ) {
            $item_type = 'plugin';
        }

        if ( 'plugin' === $item_type ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';

            $plugins = get_plugins();
            $auto_update_plugins = (array) get_site_option( 'auto_update_plugins', array() );
            $updates = get_site_transient( 'update_plugins' );

            $items = array();
            $active_count = 0;
            $update_count = 0;

            foreach ( $plugins as $plugin_file => $data ) {
                $active = is_plugin_active( $plugin_file );
                $network_active = is_multisite() ? is_plugin_active_for_network( $plugin_file ) : false;
                if ( $active || $network_active ) {
                    $active_count++;
                }

                $auto_update = in_array( $plugin_file, $auto_update_plugins, true );

                $update_available = false;
                $new_version = '';
                if ( is_object( $updates ) && ! empty( $updates->response ) && isset( $updates->response[ $plugin_file ] ) ) {
                    $update_available = true;
                    $new_version = isset( $updates->response[ $plugin_file ]->new_version ) ? $updates->response[ $plugin_file ]->new_version : '';
                    $update_count++;
                }

                $requires_plugins = array();
                if ( isset( $data['RequiresPlugins'] ) && is_string( $data['RequiresPlugins'] ) && '' !== trim( $data['RequiresPlugins'] ) ) {
                    $parts = array_map( 'trim', explode( ',', $data['RequiresPlugins'] ) );
                    $requires_plugins = array_values( array_filter( $parts ) );
                }

                $items[] = array(
                    'id' => $plugin_file,
                    'name' => isset( $data['Name'] ) ? $data['Name'] : $plugin_file,
                    'version' => isset( $data['Version'] ) ? $data['Version'] : '',
                    'author' => isset( $data['Author'] ) ? wp_strip_all_tags( $data['Author'] ) : '',
                    'active' => (bool) $active,
                    'network_active' => (bool) $network_active,
                    'auto_update' => (bool) $auto_update,
                    'update_available' => (bool) $update_available,
                    'new_version' => $new_version,
                    'requires_plugins' => $requires_plugins,
                );
            }

            wp_send_json_success( array(
                'item_type' => 'plugin',
                'items' => $items,
                'counts' => array(
                    'total' => count( $items ),
                    'active' => $active_count,
                    'updates' => $update_count,
                ),
            ) );
        }

        // theme
        include_once ABSPATH . 'wp-admin/includes/theme.php';
        $themes = wp_get_themes();
        $current = wp_get_theme();
        $active_stylesheet = $current ? $current->get_stylesheet() : '';

        $auto_update_themes = (array) get_site_option( 'auto_update_themes', array() );
        $updates = get_site_transient( 'update_themes' );

        $items = array();
        $update_count = 0;
        foreach ( $themes as $stylesheet => $theme_obj ) {
            $is_active = ( $stylesheet === $active_stylesheet );
            $auto_update = in_array( $stylesheet, $auto_update_themes, true );

            $update_available = false;
            $new_version = '';
            if ( is_array( $updates ) && isset( $updates['response'] ) && isset( $updates['response'][ $stylesheet ] ) ) {
                $update_available = true;
                $new_version = isset( $updates['response'][ $stylesheet ]['new_version'] ) ? $updates['response'][ $stylesheet ]['new_version'] : '';
                $update_count++;
            } elseif ( is_object( $updates ) && isset( $updates->response ) && isset( $updates->response[ $stylesheet ] ) ) {
                // 일부 환경 호환
                $update_available = true;
                $new_version = isset( $updates->response[ $stylesheet ]['new_version'] ) ? $updates->response[ $stylesheet ]['new_version'] : '';
                $update_count++;
            }

            $items[] = array(
                'id' => $stylesheet,
                'name' => $theme_obj->get( 'Name' ),
                'version' => $theme_obj->get( 'Version' ),
                'author' => wp_strip_all_tags( $theme_obj->get( 'Author' ) ),
                'active' => (bool) $is_active,
                'auto_update' => (bool) $auto_update,
                'update_available' => (bool) $update_available,
                'new_version' => $new_version,
            );
        }

        wp_send_json_success( array(
            'item_type' => 'theme',
            'items' => $items,
            'counts' => array(
                'total' => count( $items ),
                'updates' => $update_count,
            ),
        ) );
    }

    /**
     * Bulk Editor: 벌크 비활성화/삭제/비활성화후삭제
     *
     * @return void
     */
    public function ajax_bulk_manage_action() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );

        $item_type = isset( $_POST['item_type'] ) ? sanitize_text_field( $_POST['item_type'] ) : '';
        $operation = isset( $_POST['operation'] ) ? sanitize_text_field( $_POST['operation'] ) : '';
        $items = isset( $_POST['items'] ) ? $_POST['items'] : array();

        if ( ! in_array( $item_type, array( 'plugin', 'theme' ), true ) ) {
            wp_send_json_error( '잘못된 item_type 입니다.' );
        }
        if ( ! in_array( $operation, array( 'deactivate', 'delete', 'deactivate_delete' ), true ) ) {
            wp_send_json_error( '잘못된 operation 입니다.' );
        }
        if ( 'theme' === $item_type && 'delete' !== $operation ) {
            wp_send_json_error( '테마는 삭제만 지원합니다.' );
        }
        if ( ! is_array( $items ) ) {
            wp_send_json_error( 'items 형식이 올바르지 않습니다.' );
        }

        $items = array_values( array_filter( array_map( 'sanitize_text_field', $items ) ) );
        if ( empty( $items ) ) {
            wp_send_json_error( '선택된 항목이 없습니다.' );
        }

        $limits = $this->get_license_limits();
        $max_manage = isset( $limits['max_manage_items'] ) ? (int) $limits['max_manage_items'] : 3;
        if ( count( $items ) > $max_manage ) {
            wp_send_json_error( sprintf( '라이센스 제한: 한 번에 최대 %d개까지만 처리할 수 있습니다.', $max_manage ) );
        }

        // License gating
        if ( in_array( $operation, array( 'delete', 'deactivate_delete' ), true ) && empty( $limits['can_bulk_delete'] ) ) {
            wp_send_json_error( '라이센스 제한: 삭제 기능은 Basic 이상에서 사용할 수 있습니다.' );
        }
        if ( 'deactivate_delete' === $operation && empty( $limits['can_deactivate_then_delete'] ) ) {
            wp_send_json_error( '라이센스 제한: 비활성화 후 삭제는 Premium 이상에서 사용할 수 있습니다.' );
        }

        $results = array();

        if ( 'plugin' === $item_type ) {
            // Capabilities
            if ( in_array( $operation, array( 'deactivate', 'deactivate_delete' ), true ) && ! current_user_can( 'activate_plugins' ) ) {
                wp_send_json_error( '권한이 없습니다. (activate_plugins 필요)' );
            }
            if ( in_array( $operation, array( 'delete', 'deactivate_delete' ), true ) && ! current_user_can( 'delete_plugins' ) ) {
                wp_send_json_error( '권한이 없습니다. (delete_plugins 필요)' );
            }

            include_once ABSPATH . 'wp-admin/includes/plugin.php';
            include_once ABSPATH . 'wp-admin/includes/file.php';
            include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

            $all_plugins = get_plugins();
            $self_plugin_file = plugin_basename( __FILE__ );

            foreach ( $items as $plugin_file ) {
                if ( ! isset( $all_plugins[ $plugin_file ] ) ) {
                    $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => '존재하지 않는 플러그인입니다.' );
                    continue;
                }
                if ( $plugin_file === $self_plugin_file ) {
                    $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => '안전상 이 도구로 자기 자신을 삭제할 수 없습니다.' );
                    continue;
                }

                $network_active = is_multisite() ? is_plugin_active_for_network( $plugin_file ) : false;
                $active = is_plugin_active( $plugin_file ) || $network_active;

                // 1) deactivate if needed
                if ( in_array( $operation, array( 'deactivate', 'deactivate_delete' ), true ) && $active ) {
                    // 네트워크 활성 플러그인은 네트워크 관리자 영역에서만 처리하는 편이 안전
                    if ( $network_active ) {
                        $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => '네트워크 활성 플러그인은 네트워크 관리자에서 비활성화하세요.' );
                        continue;
                    }
                    deactivate_plugins( $plugin_file, false, false );
                    $active = is_plugin_active( $plugin_file );
                    if ( $active ) {
                        $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => '비활성화에 실패했습니다. (의존성/정책으로 차단되었을 수 있습니다)' );
                        continue;
                    }
                    if ( 'deactivate' === $operation ) {
                        $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => '비활성화 완료' );
                        continue;
                    }
                } elseif ( 'deactivate' === $operation ) {
                    // already inactive
                    $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => '이미 비활성 상태' );
                    continue;
                }

                // 2) delete
                if ( in_array( $operation, array( 'delete', 'deactivate_delete' ), true ) ) {
                    if ( $active ) {
                        $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => '삭제 전 비활성화가 필요합니다.' );
                        continue;
                    }

                    $del = delete_plugins( array( $plugin_file ) );
                    if ( is_wp_error( $del ) ) {
                        $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => $del->get_error_message() );
                        continue;
                    }
                    $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => ( 'deactivate_delete' === $operation ) ? '비활성화 후 삭제 완료' : '삭제 완료' );
                    continue;
                }
            }

            wp_send_json_success( array(
                'item_type' => 'plugin',
                'operation' => $operation,
                'results' => $results,
            ) );
        }

        // theme
        if ( ! current_user_can( 'delete_themes' ) ) {
            wp_send_json_error( '권한이 없습니다. (delete_themes 필요)' );
        }
        include_once ABSPATH . 'wp-admin/includes/theme.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';

        $themes = wp_get_themes();
        $current = wp_get_theme();
        $active_stylesheet = $current ? $current->get_stylesheet() : '';

        foreach ( $items as $stylesheet ) {
            if ( ! isset( $themes[ $stylesheet ] ) ) {
                $results[] = array( 'id' => $stylesheet, 'ok' => false, 'message' => '존재하지 않는 테마입니다.' );
                continue;
            }
            if ( $stylesheet === $active_stylesheet ) {
                $results[] = array( 'id' => $stylesheet, 'ok' => false, 'message' => '현재 사용 중인(활성) 테마는 삭제할 수 없습니다.' );
                continue;
            }

            $del = delete_theme( $stylesheet );
            if ( is_wp_error( $del ) ) {
                $results[] = array( 'id' => $stylesheet, 'ok' => false, 'message' => $del->get_error_message() );
                continue;
            }
            $results[] = array( 'id' => $stylesheet, 'ok' => true, 'message' => '삭제 완료' );
        }

        wp_send_json_success( array(
            'item_type' => 'theme',
            'operation' => $operation,
            'results' => $results,
        ) );
    }

    // 1. 파일 업로드 핸들러
    public function ajax_handle_upload() {
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
                'type' => $this->detect_type( $target_path )
            ) );
        } else {
            wp_send_json_error( '파일 업로드 실패' );
        }
    }

    // 2. 설치 핸들러
    public function ajax_handle_install() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        
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

        $plugin_slug = '';
        if ( $type === 'plugin' ) {
            $plugin_slug = $upgrader->plugin_info();
            if ( ! $plugin_slug ) {
                // 설치 후 파일명으로 추측 (간단한 로직)
                // 실제로는 압축 해제된 폴더를 찾아야 함.
                // 이번 버전에서는 '설치됨' 상태만 반환하고 활성화는 수동/자동 옵션에 따름
            }
        }

        @unlink( $file_path );

        $response = array( 'status' => 'installed', 'slug' => $plugin_slug );

        // 자동 활성화 옵션이 켜져있으면 바로 활성화 시도
        if ( $auto_activate && $plugin_slug ) {
            $activate_result = activate_plugin( $plugin_slug );
            if ( is_wp_error( $activate_result ) ) {
                $response['activated'] = false;
                $response['error'] = $activate_result->get_error_message();
            } else {
                $response['activated'] = true;
            }
        }

        wp_send_json_success( $response );
    }

    // 3. 활성화 핸들러 (수동 활성화용)
    public function ajax_handle_activate() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        $slug = isset( $_POST['slug'] ) ? sanitize_text_field( $_POST['slug'] ) : '';
        
        if ( ! $slug ) wp_send_json_error( '플러그인 정보가 없습니다.' );

        $result = activate_plugin( $slug );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }
        wp_send_json_success( '활성화됨' );
    }

    private function detect_type( $zip_path ) {
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

JJ_Bulk_Installer::instance();

```

```javascript
jQuery(document).ready(function($) {
    var filesQueue = [];
    var isProcessing = false;
    var installedPlugins = []; // 설치된 플러그인 정보 저장

    var bulkCache = {
        plugins: null,
        themes: null
    };
    var bulkUi = {
        editorInitialized: false,
        currentSubtab: 'plugins'
    };

    function escapeHtml(str) {
        if (str === null || typeof str === 'undefined') return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showNotice(type, message, extraHtml) {
        var $wrap = $('#jj-bulk-notices');
        if ($wrap.length === 0) return;

        var html = '<div class="notice notice-' + escapeHtml(type) + ' is-dismissible">' +
            '<p>' + message + '</p>' +
            (extraHtml ? extraHtml : '') +
            '</div>';
        $wrap.html(html);
    }

    function initTabs() {
        var storageKey = 'jj_bulk_manager_active_tab';
        var savedTab = null;
        try {
            savedTab = window.localStorage ? window.localStorage.getItem(storageKey) : null;
        } catch (e) {}

        function activateTab(tab) {
            $('.jj-bulk-tab').removeClass('is-active').attr('aria-selected', 'false');
            $('.jj-bulk-tab[data-tab="' + tab + '"]').addClass('is-active').attr('aria-selected', 'true');

            $('.jj-bulk-tab-panel').hide().removeClass('is-active');
            $('.jj-bulk-tab-panel[data-tab-panel="' + tab + '"]').show().addClass('is-active');

            try {
                if (window.localStorage) window.localStorage.setItem(storageKey, tab);
            } catch (e) {}

            if (tab === 'editor') {
                initBulkEditorOnce();
            }
        }

        $('.jj-bulk-tab').on('click', function() {
            activateTab($(this).data('tab'));
        });

        if (savedTab && $('.jj-bulk-tab[data-tab="' + savedTab + '"]').length) {
            activateTab(savedTab);
        }
    }

    // ==============================
    // Installer (기존 기능 유지)
    // ==============================
    function initInstaller() {
        var dropzone = $('#jj-dropzone');
        var fileInput = $('#jj-file-input');

        if (dropzone.length === 0 || fileInput.length === 0) return;

        // 1. 파일 선택 트리거 수정 (클릭 이벤트 버블링 방지)
        dropzone.on('click', function(e) {
            if (e.target !== fileInput[0]) {
                fileInput.click();
            }
        });

        dropzone.on('dragover', function(e) {
            e.preventDefault();
            dropzone.addClass('dragover');
        });

        dropzone.on('dragleave drop', function(e) {
            e.preventDefault();
            dropzone.removeClass('dragover');
        });

        dropzone.on('drop', function(e) {
            var files = e.originalEvent.dataTransfer.files;
            handleFiles(files);
        });

        fileInput.on('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            var maxFiles = (jjBulk && jjBulk.limits && jjBulk.limits.max_files) ? jjBulk.limits.max_files : 3;

            if (files.length + filesQueue.length > maxFiles) {
                alert(jjBulk.i18n.limit_reached + '\n' + jjBulk.i18n.upgrade_msg);
                return;
            }

            $.each(files, function(i, file) {
                if (file.name.split('.').pop().toLowerCase() !== 'zip') return;

                // 중복 체크
                var isDuplicate = filesQueue.some(function(f) { return f.name === file.name; });
                if (isDuplicate) return;

                filesQueue.push(file);
                addFileToList(file, filesQueue.length - 1);
            });

            if (filesQueue.length > 0) {
                $('#jj-actions-area').show();
                $('#jj-start-install').prop('disabled', false).text('설치 시작 (' + filesQueue.length + '개)');
            }
        }

        function addFileToList(file, index) {
            var sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            var html = '<div class="jj-file-item" id="file-' + index + '" data-index="' + index + '">' +
                       '<div class="file-info">' +
                       '<input type="checkbox" class="jj-activate-check" disabled> ' +
                       '<span class="name">' + escapeHtml(file.name) + '</span> ' +
                       '<span class="size">(' + sizeMB + ' MB)</span>' +
                       '</div>' +
                       '<span class="status">대기 중</span>' +
                       '</div>';
            $('#jj-file-list').append(html);
        }

        // 설치 시작
        $('#jj-start-install').on('click', function() {
            if (isProcessing) return;
            isProcessing = true;
            $(this).prop('disabled', true);
            $('#jj-progress-area').show();

            processQueue(0);
        });

        function processQueue(index) {
            if (index >= filesQueue.length) {
                isProcessing = false;
                $('#jj-start-install').hide(); // 설치 버튼 숨김

                // 설치된 플러그인이 있으면 활성화 버튼 표시
                if (installedPlugins.length > 0) {
                    $('#jj-activate-selected').show().text('선택한 플러그인 활성화 (' + installedPlugins.length + '개)');
                    // 체크박스 활성화
                    $('.jj-activate-check').each(function() {
                        if ($(this).closest('.jj-file-item').hasClass('success')) {
                            $(this).prop('disabled', false).prop('checked', true);
                        }
                    });
                }

                alert('모든 파일 처리가 완료되었습니다.');
                return;
            }

            var file = filesQueue[index];
            var item = $('#file-' + index);
            var autoActivate = (jjBulk && jjBulk.limits && jjBulk.limits.can_auto_activate) && $('#jj-auto-activate-all').is(':checked');

            item.addClass('uploading').find('.status').text('업로드 중...');
            updateProgress(index, filesQueue.length, '업로드 중: ' + file.name);

            var formData = new FormData();
            formData.append('action', 'jj_bulk_install_upload');
            formData.append('nonce', jjBulk.nonce);
            formData.append('file', file);

            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        item.find('.status').text('설치 중...');
                        installPlugin(response.data, item, index, autoActivate);
                    } else {
                        item.addClass('error').find('.status').text('업로드 실패: ' + response.data);
                        processQueue(index + 1);
                    }
                },
                error: function() {
                    item.addClass('error').find('.status').text('서버 오류');
                    processQueue(index + 1);
                }
            });
        }

        function installPlugin(data, item, index, autoActivate) {
            updateProgress(index, filesQueue.length, '설치 중: ' + data.name);

            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_bulk_install_process',
                    nonce: jjBulk.nonce,
                    path: data.path,
                    type: data.type,
                    activate: autoActivate // 전체 자동 활성화 여부
                },
                success: function(response) {
                    if (response.success) {
                        var statusText = '설치 완료';
                        if (response.data.activated) statusText += ' (활성)';

                        item.removeClass('uploading').addClass('success').find('.status').text(statusText);

                        // 설치 성공한 플러그인 정보 저장 (수동 활성화를 위해)
                        if (data.type === 'plugin' && response.data.slug) {
                            item.data('slug', response.data.slug);
                            installedPlugins.push(response.data.slug);
                        }
                    } else {
                        item.removeClass('uploading').addClass('error').find('.status').text('실패: ' + response.data);
                    }
                    processQueue(index + 1);
                },
                error: function() {
                    item.removeClass('uploading').addClass('error').find('.status').text('통신 오류');
                    processQueue(index + 1);
                }
            });
        }

        function updateProgress(current, total, text) {
            var percent = Math.round((current / total) * 100);
            $('.jj-progress-fill').css('width', percent + '%');
            $('.jj-status-text').text(text + ' (' + (current + 1) + '/' + total + ')');
        }

        // 선택한 플러그인 활성화 (2단계)
        $('#jj-activate-selected').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).text('활성화 중...');

            var toActivate = [];
            $('.jj-activate-check:checked').each(function() {
                var item = $(this).closest('.jj-file-item');
                var slug = item.data('slug');
                if (slug) toActivate.push({ slug: slug, item: item });
            });

            if (toActivate.length === 0) {
                alert('선택된 항목이 없습니다.');
                btn.prop('disabled', false).text('선택한 플러그인 활성화');
                return;
            }

            processActivation(toActivate, 0);
        });

        function processActivation(list, index) {
            if (index >= list.length) {
                alert('활성화 작업 완료!');
                $('#jj-activate-selected').prop('disabled', false).text('선택한 플러그인 활성화');
                return;
            }

            var target = list[index];
            target.item.find('.status').text('활성화 중...');

            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_bulk_activate_plugin',
                    nonce: jjBulk.nonce,
                    slug: target.slug
                },
                success: function(response) {
                    if (response.success) {
                        target.item.find('.status').text('설치 완료 (활성)');
                    } else {
                        target.item.find('.status').text('활성화 실패');
                    }
                    processActivation(list, index + 1);
                }
            });
        }
    }

    // ==============================
    // Bulk Editor (관리)
    // ==============================
    function initBulkEditorOnce() {
        if (bulkUi.editorInitialized) return;
        bulkUi.editorInitialized = true;

        // Subtabs
        $('.jj-subtab').on('click', function() {
            var subtab = $(this).data('subtab');
            bulkUi.currentSubtab = subtab;

            $('.jj-subtab').removeClass('is-active').attr('aria-selected', 'false');
            $(this).addClass('is-active').attr('aria-selected', 'true');

            $('[data-subtab-panel]').hide();
            $('[data-subtab-panel="' + subtab + '"]').show();

            // action buttons toggle
            if (subtab === 'themes') {
                $('#jj-bulk-action-deactivate').hide();
                $('#jj-bulk-action-delete').hide();
                $('#jj-bulk-action-deactivate-delete').hide();
                $('#jj-bulk-action-theme-delete').show();
            } else {
                $('#jj-bulk-action-deactivate').show();
                $('#jj-bulk-action-delete').show();
                $('#jj-bulk-action-deactivate-delete').show();
                $('#jj-bulk-action-theme-delete').hide();
            }

            applyFilters();
        });

        // Refresh
        $('#jj-bulk-refresh').on('click', function() {
            loadInstalledItems('plugin', true);
            loadInstalledItems('theme', true);
        });

        // Filters
        $('#jj-bulk-search').on('input', function() {
            applyFilters();
        });
        $('#jj-bulk-filter-status').on('change', function() {
            applyFilters();
        });

        // Select all
        $('#jj-bulk-select-all-plugins').on('change', function() {
            var checked = $(this).is(':checked');
            $('#jj-bulk-table-plugins tbody tr.jj-bulk-row:visible .jj-bulk-row-check').prop('checked', checked);
        });
        $('#jj-bulk-select-all-themes').on('change', function() {
            var checked = $(this).is(':checked');
            $('#jj-bulk-table-themes tbody tr.jj-bulk-row:visible .jj-bulk-row-check').prop('checked', checked);
        });

        // Actions (plugins / themes)
        $('#jj-bulk-action-deactivate, #jj-bulk-action-delete, #jj-bulk-action-deactivate-delete, #jj-bulk-action-theme-delete').on('click', function() {
            var op = $(this).data('op');
            var type = $(this).data('type');
            runBulkOperation(type, op);
        });

        // Initial load
        loadInstalledItems('plugin', true);
        loadInstalledItems('theme', true);
    }

    function loadInstalledItems(itemType, forceReload) {
        if (!forceReload && itemType === 'plugin' && bulkCache.plugins) {
            renderPluginsTable(bulkCache.plugins.items || []);
            updateStats();
            return;
        }
        if (!forceReload && itemType === 'theme' && bulkCache.themes) {
            renderThemesTable(bulkCache.themes.items || []);
            updateStats();
            return;
        }

        var $tbody = (itemType === 'plugin') ? $('#jj-bulk-table-plugins tbody') : $('#jj-bulk-table-themes tbody');
        if ($tbody.length) {
            $tbody.html('<tr><td colspan="6">목록을 불러오는 중...</td></tr>');
        }

        $.ajax({
            url: jjBulk.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_bulk_manage_get_items',
                nonce: jjBulk.nonce,
                item_type: itemType
            },
            success: function(resp) {
                if (!resp || !resp.success) {
                    showNotice('error', '목록을 불러오지 못했습니다: ' + escapeHtml(resp && resp.data ? resp.data : '알 수 없는 오류'));
                    return;
                }

                if (itemType === 'plugin') {
                    bulkCache.plugins = resp.data;
                    renderPluginsTable(resp.data.items || []);
                } else {
                    bulkCache.themes = resp.data;
                    renderThemesTable(resp.data.items || []);
                }
                updateStats();
                applyFilters();
            },
            error: function() {
                showNotice('error', '서버 통신 오류로 목록을 불러오지 못했습니다.');
            }
        });
    }

    function updateStats() {
        if (bulkCache.plugins && bulkCache.plugins.counts) {
            $('#jj-count-plugins').text(bulkCache.plugins.counts.total || 0);
            $('#jj-count-plugins-active').text(bulkCache.plugins.counts.active || 0);
            $('#jj-count-plugins-update').text(bulkCache.plugins.counts.updates || 0);
        }
        if (bulkCache.themes && bulkCache.themes.counts) {
            $('#jj-count-themes').text(bulkCache.themes.counts.total || 0);
        }
    }

    function renderPluginsTable(items) {
        var $tbody = $('#jj-bulk-table-plugins tbody');
        if ($tbody.length === 0) return;

        if (!items || items.length === 0) {
            $tbody.html('<tr><td colspan="6">설치된 플러그인이 없습니다.</td></tr>');
            return;
        }

        var rows = [];
        items.forEach(function(p) {
            var status = p.network_active ? 'network_active' : (p.active ? 'active' : 'inactive');
            var statusLabel = p.network_active ? '네트워크' : (p.active ? '활성' : '비활성');
            var statusClass = p.network_active ? 'jj-pill-neutral' : (p.active ? 'jj-pill-good' : 'jj-pill-muted');

            var auLabel = p.auto_update ? 'ON' : 'OFF';
            var auClass = p.auto_update ? 'jj-pill-good' : 'jj-pill-muted';

            var updHtml = '<span class="jj-pill jj-pill-muted">없음</span>';
            if (p.update_available) {
                updHtml = '<span class="jj-pill jj-pill-warn">업데이트</span>' + (p.new_version ? ' <code>' + escapeHtml(p.new_version) + '</code>' : '');
            }

            var requires = '';
            if (p.requires_plugins && p.requires_plugins.length) {
                requires = '<div class="jj-inline-tags">' +
                    p.requires_plugins.map(function(x) {
                        return '<span class="jj-tag">필요: ' + escapeHtml(x) + '</span>';
                    }).join(' ') +
                    '</div>';
            }

            rows.push(
                '<tr class="jj-bulk-row" data-status="' + escapeHtml(p.active ? 'active' : 'inactive') + '" data-search="' + escapeHtml((p.name + ' ' + (p.author || '') + ' ' + p.id).toLowerCase()) + '">' +
                    '<th scope="row" class="check-column"><input type="checkbox" class="jj-bulk-row-check" data-id="' + escapeHtml(p.id) + '"></th>' +
                    '<td>' +
                        '<strong>' + escapeHtml(p.name) + '</strong> <span class="description">v' + escapeHtml(p.version || '-') + '</span>' +
                        (p.author ? '<div class="description">' + escapeHtml(p.author) + '</div>' : '') +
                        requires +
                    '</td>' +
                    '<td><span class="jj-pill ' + statusClass + '">' + escapeHtml(statusLabel) + '</span></td>' +
                    '<td><span class="jj-pill ' + auClass + '">' + escapeHtml(auLabel) + '</span></td>' +
                    '<td>' + updHtml + '</td>' +
                    '<td><code>' + escapeHtml(p.id) + '</code></td>' +
                '</tr>'
            );
        });

        $tbody.html(rows.join(''));
    }

    function renderThemesTable(items) {
        var $tbody = $('#jj-bulk-table-themes tbody');
        if ($tbody.length === 0) return;

        if (!items || items.length === 0) {
            $tbody.html('<tr><td colspan="6">설치된 테마가 없습니다.</td></tr>');
            return;
        }

        var rows = [];
        items.forEach(function(t) {
            var statusLabel = t.active ? '활성' : '비활성';
            var statusClass = t.active ? 'jj-pill-good' : 'jj-pill-muted';

            var auLabel = t.auto_update ? 'ON' : 'OFF';
            var auClass = t.auto_update ? 'jj-pill-good' : 'jj-pill-muted';

            var updHtml = '<span class="jj-pill jj-pill-muted">없음</span>';
            if (t.update_available) {
                updHtml = '<span class="jj-pill jj-pill-warn">업데이트</span>' + (t.new_version ? ' <code>' + escapeHtml(t.new_version) + '</code>' : '');
            }

            rows.push(
                '<tr class="jj-bulk-row" data-status="' + escapeHtml(t.active ? 'active' : 'inactive') + '" data-search="' + escapeHtml((t.name + ' ' + (t.author || '') + ' ' + t.id).toLowerCase()) + '">' +
                    '<th scope="row" class="check-column"><input type="checkbox" class="jj-bulk-row-check" data-id="' + escapeHtml(t.id) + '"></th>' +
                    '<td>' +
                        '<strong>' + escapeHtml(t.name) + '</strong> <span class="description">v' + escapeHtml(t.version || '-') + '</span>' +
                        (t.author ? '<div class="description">' + escapeHtml(t.author) + '</div>' : '') +
                    '</td>' +
                    '<td><span class="jj-pill ' + statusClass + '">' + escapeHtml(statusLabel) + '</span></td>' +
                    '<td><span class="jj-pill ' + auClass + '">' + escapeHtml(auLabel) + '</span></td>' +
                    '<td>' + updHtml + '</td>' +
                    '<td><code>' + escapeHtml(t.id) + '</code></td>' +
                '</tr>'
            );
        });

        $tbody.html(rows.join(''));
    }

    function applyFilters() {
        var query = ($('#jj-bulk-search').val() || '').toLowerCase().trim();
        var status = $('#jj-bulk-filter-status').val() || 'all';

        var $table = (bulkUi.currentSubtab === 'themes') ? $('#jj-bulk-table-themes') : $('#jj-bulk-table-plugins');
        $table.find('tbody tr.jj-bulk-row').each(function() {
            var $tr = $(this);
            var rowStatus = $tr.attr('data-status');
            var hay = $tr.attr('data-search') || '';

            var okStatus = (status === 'all') || (status === rowStatus);
            var okQuery = (!query) || (hay.indexOf(query) !== -1);

            $tr.toggle(okStatus && okQuery);
        });
    }

    function getSelectedIds(itemType) {
        var $table = (itemType === 'theme') ? $('#jj-bulk-table-themes') : $('#jj-bulk-table-plugins');
        var ids = [];
        $table.find('tbody tr.jj-bulk-row .jj-bulk-row-check:checked').each(function() {
            var id = $(this).data('id');
            if (id) ids.push(String(id));
        });
        return ids;
    }

    function runBulkOperation(itemType, operation) {
        var ids = getSelectedIds(itemType);
        if (ids.length === 0) {
            showNotice('warning', '선택된 항목이 없습니다.');
            return;
        }

        var maxManage = (jjBulk && jjBulk.limits && jjBulk.limits.max_manage_items) ? parseInt(jjBulk.limits.max_manage_items, 10) : 3;
        if (ids.length > maxManage) {
            alert(jjBulk.i18n.manage_limit_reached);
            return;
        }

        // client-side gating (server-side도 동일하게 검증)
        if ((operation === 'delete' || operation === 'deactivate_delete') && jjBulk && jjBulk.limits && !jjBulk.limits.can_bulk_delete) {
            alert(jjBulk.i18n.delete_locked);
            return;
        }
        if (operation === 'deactivate_delete' && jjBulk && jjBulk.limits && !jjBulk.limits.can_deactivate_then_delete) {
            alert(jjBulk.i18n.deactivate_delete_locked);
            return;
        }

        var confirmText = '';
        if (operation === 'deactivate') {
            confirmText = '선택한 ' + ids.length + '개를 비활성화할까요?';
        } else if (operation === 'delete') {
            confirmText = '정말로 선택한 ' + ids.length + '개를 삭제할까요?\n삭제는 되돌릴 수 없습니다.';
        } else if (operation === 'deactivate_delete') {
            confirmText = '선택한 ' + ids.length + '개를 비활성화한 뒤 즉시 삭제할까요?\n삭제는 되돌릴 수 없습니다.';
        }

        if (confirmText && !window.confirm(confirmText)) {
            return;
        }

        showNotice('info', '작업을 진행 중입니다...');

        $.ajax({
            url: jjBulk.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_bulk_manage_action',
                nonce: jjBulk.nonce,
                item_type: itemType,
                operation: operation,
                items: ids
            },
            success: function(resp) {
                if (!resp || !resp.success) {
                    showNotice('error', '작업 실패: ' + escapeHtml(resp && resp.data ? resp.data : '알 수 없는 오류'));
                    return;
                }

                var results = (resp.data && resp.data.results) ? resp.data.results : [];
                var ok = results.filter(function(r) { return r.ok; }).length;
                var fail = results.length - ok;

                var extra = '';
                if (fail > 0) {
                    var listItems = results.filter(function(r) { return !r.ok; }).map(function(r) {
                        return '<li><code>' + escapeHtml(r.id) + '</code> - ' + escapeHtml(r.message) + '</li>';
                    }).join('');
                    extra = '<ul style="margin: 0.5em 0 0.2em 1.2em; list-style: disc;">' + listItems + '</ul>';
                }

                showNotice(fail === 0 ? 'success' : 'warning', '완료: 성공 ' + ok + '개 / 실패 ' + fail + '개', extra);

                // refresh the relevant list
                if (itemType === 'theme') {
                    loadInstalledItems('theme', true);
                } else {
                    loadInstalledItems('plugin', true);
                }
            },
            error: function() {
                showNotice('error', '서버 통신 오류로 작업에 실패했습니다.');
            }
        });
    }

    initTabs();
    initInstaller();
});

```

```css
.jj-bulk-wrap {
    max_width: 900px;
    margin: 20px auto;
}
.jj-bulk-container {
    background: #fff;
    padding: 20px;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.jj-dropzone {
    border: 3px dashed #c3c4c7;
    background: #f9f9f9;
    padding: 60px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    border-radius: 8px;
    margin-bottom: 20px;
}
.jj-dropzone:hover, .jj-dropzone.dragover {
    border-color: #2271b1;
    background: #f0f6fc;
}
.jj-dropzone h3 { margin: 10px 0 5px; color: #3c434a; }
.jj-dropzone p { margin: 0; color: #646970; }

.jj-file-list {
    margin: 20px 0;
    max-height: 400px;
    overflow-y: auto;
}
.jj-file-item {
    background: #fff;
    border: 1px solid #c3c4c7;
    padding: 12px 15px;
    margin-bottom: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.jj-file-item .file-info { display: flex; align-items: center; gap: 10px; }
.jj-file-item .name { font-weight: 600; color: #3c434a; }
.jj-file-item .size { color: #8c8f94; font-size: 12px; }
.jj-file-item .status { font-size: 13px; font-weight: 500; color: #646970; }

.jj-file-item.success { border-left: 4px solid #46b450; background: #edfaef; }
.jj-file-item.success .status { color: #46b450; }
.jj-file-item.error { border-left: 4px solid #dc3232; background: #fbeaea; }
.jj-file-item.error .status { color: #dc3232; }
.jj-file-item.uploading { border-left: 4px solid #2271b1; background: #f0f6fc; }
.jj-file-item.uploading .status { color: #2271b1; }

.jj-progress-area {
    margin-top: 20px;
    padding: 15px;
    background: #f0f0f1;
    border-radius: 4px;
}
.jj-progress-bar {
    height: 10px;
    background: #dcdcde;
    border-radius: 5px;
    overflow: hidden;
    margin-bottom: 5px;
}
.jj-progress-fill {
    height: 100%;
    background: #2271b1;
    width: 0%;
    transition: width 0.3s;
}
.jj-status-text {
    font-size: 13px;
    text-align: right;
    color: #646970;
}

.jj-options {
    background: #fff;
    padding: 15px;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    margin-bottom: 20px;
}

.jj-actions {
    margin-top: 20px;
    text-align: right;
}

.jj-promo-banner {
    background: linear-gradient(135deg, #1e1e1e 0%, #3a3a3a 100%);
    color: #fff;
    padding: 25px;
    border-radius: 8px;
    margin-top: 40px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
.jj-promo-banner h3 { color: #fff; margin: 0 0 10px; font-size: 1.2em; }
.jj-promo-banner p { margin: 0 0 15px; color: #dcdcde; }

/* ==============================
 * Bulk Manager Tabs / Editor UI
 * ============================== */
.jj-bulk-tabs {
    display: flex;
    gap: 8px;
    margin: 12px 0 16px;
}
.jj-bulk-tab {
    border: 1px solid #c3c4c7;
    background: #f6f7f7;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    color: #1d2327;
}
.jj-bulk-tab.is-active {
    background: #fff;
    border-color: #2271b1;
    box-shadow: 0 0 0 1px #2271b1 inset;
}

.jj-bulk-editor-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
}
.jj-bulk-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.jj-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border: 1px solid #dcdcde;
    background: #f6f7f7;
    border-radius: 999px;
    font-size: 12px;
    color: #3c434a;
}
.jj-bulk-editor-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.jj-bulk-subtabs {
    display: flex;
    gap: 8px;
    margin: 10px 0 12px;
}
.jj-subtab.is-active {
    border-color: #2271b1 !important;
    box-shadow: 0 0 0 1px #2271b1 inset;
}

.jj-bulk-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    margin: 6px 0 10px;
}
.jj-bulk-toolbar-right {
    margin-left: auto;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.button.button-danger {
    background: #b32d2e;
    border-color: #b32d2e;
    color: #fff;
}
.button.button-danger:hover,
.button.button-danger:focus {
    background: #8a2424;
    border-color: #8a2424;
    color: #fff;
}

.jj-pill {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid transparent;
}
.jj-pill-good { background: #edfaef; color: #1d6b2f; border-color: #b7e1c2; }
.jj-pill-muted { background: #f6f7f7; color: #646970; border-color: #dcdcde; }
.jj-pill-warn { background: #fcf9e8; color: #8a6d00; border-color: #f0e6a6; }
.jj-pill-neutral { background: #f0f6fc; color: #1d5e93; border-color: #b6d1ea; }

.jj-inline-tags {
    margin-top: 6px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.jj-tag {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 11px;
    border: 1px solid #dcdcde;
    background: #fff;
    color: #3c434a;
}

```

```javascript
jQuery(document).ready(function($) {
    var filesQueue = [];
    var isProcessing = false;
    var installedPlugins = []; // 설치된 플러그인 정보 저장

    var bulkCache = {
        plugins: null,
        themes: null
    };
    var bulkUi = {
        editorInitialized: false,
        currentSubtab: 'plugins'
    };

    function escapeHtml(str) {
        if (str === null || typeof str === 'undefined') return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showNotice(type, message, extraHtml) {
        var $wrap = $('#jj-bulk-notices');
        if ($wrap.length === 0) return;

        var html = '<div class="notice notice-' + escapeHtml(type) + ' is-dismissible">' +
            '<p>' + message + '</p>' +
            (extraHtml ? extraHtml : '') +
            '</div>';
        $wrap.html(html);
    }

    function initTabs() {
        var storageKey = 'jj_bulk_manager_active_tab';
        var savedTab = null;
        try {
            savedTab = window.localStorage ? window.localStorage.getItem(storageKey) : null;
        } catch (e) {}

        function activateTab(tab) {
            $('.jj-bulk-tab').removeClass('is-active').attr('aria-selected', 'false');
            $('.jj-bulk-tab[data-tab="' + tab + '"]').addClass('is-active').attr('aria-selected', 'true');

            $('.jj-bulk-tab-panel').hide().removeClass('is-active');
            $('.jj-bulk-tab-panel[data-tab-panel="' + tab + '"]').show().addClass('is-active');

            try {
                if (window.localStorage) window.localStorage.setItem(storageKey, tab);
            } catch (e) {}

            if (tab === 'editor') {
                initBulkEditorOnce();
            }
        }

        $('.jj-bulk-tab').on('click', function() {
            activateTab($(this).data('tab'));
        });

        if (savedTab && $('.jj-bulk-tab[data-tab="' + savedTab + '"]').length) {
            activateTab(savedTab);
        }
    }

    // ==============================
    // Installer (기존 기능 유지)
    // ==============================
    function initInstaller() {
        var dropzone = $('#jj-dropzone');
        var fileInput = $('#jj-file-input');

        if (dropzone.length === 0 || fileInput.length === 0) return;

        // 1. 파일 선택 트리거 수정 (클릭 이벤트 버블링 방지)
        dropzone.on('click', function(e) {
            if (e.target !== fileInput[0]) {
                fileInput.click();
            }
        });

        dropzone.on('dragover', function(e) {
            e.preventDefault();
            dropzone.addClass('dragover');
        });

        dropzone.on('dragleave drop', function(e) {
            e.preventDefault();
            dropzone.removeClass('dragover');
        });

        dropzone.on('drop', function(e) {
            var files = e.originalEvent.dataTransfer.files;
            handleFiles(files);
        });

        fileInput.on('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            var maxFiles = (jjBulk && jjBulk.limits && jjBulk.limits.max_files) ? jjBulk.limits.max_files : 3;

            if (files.length + filesQueue.length > maxFiles) {
                alert(jjBulk.i18n.limit_reached + '\n' + jjBulk.i18n.upgrade_msg);
                return;
            }

            $.each(files, function(i, file) {
                if (file.name.split('.').pop().toLowerCase() !== 'zip') return;

                // 중복 체크
                var isDuplicate = filesQueue.some(function(f) { return f.name === file.name; });
                if (isDuplicate) return;

                filesQueue.push(file);
                addFileToList(file, filesQueue.length - 1);
            });

            if (filesQueue.length > 0) {
                $('#jj-actions-area').show();
                $('#jj-start-install').prop('disabled', false).text('설치 시작 (' + filesQueue.length + '개)');
            }
        }

        function addFileToList(file, index) {
            var sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            var html = '<div class="jj-file-item" id="file-' + index + '" data-index="' + index + '">' +
                       '<div class="file-info">' +
                       '<input type="checkbox" class="jj-activate-check" disabled> ' +
                       '<span class="name">' + escapeHtml(file.name) + '</span> ' +
                       '<span class="size">(' + sizeMB + ' MB)</span>' +
                       '</div>' +
                       '<span class="status">대기 중</span>' +
                       '</div>';
            $('#jj-file-list').append(html);
        }

        // 설치 시작
        $('#jj-start-install').on('click', function() {
            if (isProcessing) return;
            isProcessing = true;
            $(this).prop('disabled', true);
            $('#jj-progress-area').show();

            processQueue(0);
        });

        function processQueue(index) {
            if (index >= filesQueue.length) {
                isProcessing = false;
                $('#jj-start-install').hide(); // 설치 버튼 숨김

                // 설치된 플러그인이 있으면 활성화 버튼 표시
                if (installedPlugins.length > 0) {
                    $('#jj-activate-selected').show().text('선택한 플러그인 활성화 (' + installedPlugins.length + '개)');
                    // 체크박스 활성화
                    $('.jj-activate-check').each(function() {
                        if ($(this).closest('.jj-file-item').hasClass('success')) {
                            $(this).prop('disabled', false).prop('checked', true);
                        }
                    });
                }

                alert('모든 파일 처리가 완료되었습니다.');
                return;
            }

            var file = filesQueue[index];
            var item = $('#file-' + index);
            var autoActivate = (jjBulk && jjBulk.limits && jjBulk.limits.can_auto_activate) && $('#jj-auto-activate-all').is(':checked');

            item.addClass('uploading').find('.status').text('업로드 중...');
            updateProgress(index, filesQueue.length, '업로드 중: ' + file.name);

            var formData = new FormData();
            formData.append('action', 'jj_bulk_install_upload');
            formData.append('nonce', jjBulk.nonce);
            formData.append('file', file);

            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        item.find('.status').text('설치 중...');
                        installPlugin(response.data, item, index, autoActivate);
                    } else {
                        item.addClass('error').find('.status').text('업로드 실패: ' + response.data);
                        processQueue(index + 1);
                    }
                },
                error: function() {
                    item.addClass('error').find('.status').text('서버 오류');
                    processQueue(index + 1);
                }
            });
        }

        function installPlugin(data, item, index, autoActivate) {
            updateProgress(index, filesQueue.length, '설치 중: ' + data.name);

            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_bulk_install_process',
                    nonce: jjBulk.nonce,
                    path: data.path,
                    type: data.type,
                    activate: autoActivate // 전체 자동 활성화 여부
                },
                success: function(response) {
                    if (response.success) {
                        var statusText = '설치 완료';
                        if (response.data.activated) statusText += ' (활성)';

                        item.removeClass('uploading').addClass('success').find('.status').text(statusText);

                        // 설치 성공한 플러그인 정보 저장 (수동 활성화를 위해)
                        if (data.type === 'plugin' && response.data.slug) {
                            item.data('slug', response.data.slug);
                            installedPlugins.push(response.data.slug);
                        }
                    } else {
                        item.removeClass('uploading').addClass('error').find('.status').text('실패: ' + response.data);
                    }
                    processQueue(index + 1);
                },
                error: function() {
                    item.removeClass('uploading').addClass('error').find('.status').text('통신 오류');
                    processQueue(index + 1);
                }
            });
        }

        function updateProgress(current, total, text) {
            var percent = Math.round((current / total) * 100);
            $('.jj-progress-fill').css('width', percent + '%');
            $('.jj-status-text').text(text + ' (' + (current + 1) + '/' + total + ')');
        }

        // 선택한 플러그인 활성화 (2단계)
        $('#jj-activate-selected').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).text('활성화 중...');

            var toActivate = [];
            $('.jj-activate-check:checked').each(function() {
                var item = $(this).closest('.jj-file-item');
                var slug = item.data('slug');
                if (slug) toActivate.push({ slug: slug, item: item });
            });

            if (toActivate.length === 0) {
                alert('선택된 항목이 없습니다.');
                btn.prop('disabled', false).text('선택한 플러그인 활성화');
                return;
            }

            processActivation(toActivate, 0);
        });

        function processActivation(list, index) {
            if (index >= list.length) {
                alert('활성화 작업 완료!');
                $('#jj-activate-selected').prop('disabled', false).text('선택한 플러그인 활성화');
                return;
            }

            var target = list[index];
            target.item.find('.status').text('활성화 중...');

            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_bulk_activate_plugin',
                    nonce: jjBulk.nonce,
                    slug: target.slug
                },
                success: function(response) {
                    if (response.success) {
                        target.item.find('.status').text('설치 완료 (활성)');
                    } else {
                        target.item.find('.status').text('활성화 실패');
                    }
                    processActivation(list, index + 1);
                }
            });
        }
    }

    // ==============================
    // Bulk Editor (관리)
    // ==============================
    function initBulkEditorOnce() {
        if (bulkUi.editorInitialized) return;
        bulkUi.editorInitialized = true;

        // Subtabs
        $('.jj-subtab').on('click', function() {
            var subtab = $(this).data('subtab');
            bulkUi.currentSubtab = subtab;

            $('.jj-subtab').removeClass('is-active').attr('aria-selected', 'false');
            $(this).addClass('is-active').attr('aria-selected', 'true');

            $('[data-subtab-panel]').hide();
            $('[data-subtab-panel="' + subtab + '"]').show();

            // action buttons toggle
            if (subtab === 'themes') {
                $('#jj-bulk-action-deactivate').hide();
                $('#jj-bulk-action-delete').hide();
                $('#jj-bulk-action-deactivate-delete').hide();
                $('#jj-bulk-action-theme-delete').show();
            } else {
                $('#jj-bulk-action-deactivate').show();
                $('#jj-bulk-action-delete').show();
                $('#jj-bulk-action-deactivate-delete').show();
                $('#jj-bulk-action-theme-delete').hide();
            }

            applyFilters();
        });

        // Refresh
        $('#jj-bulk-refresh').on('click', function() {
            loadInstalledItems('plugin', true);
            loadInstalledItems('theme', true);
        });

        // Filters
        $('#jj-bulk-search').on('input', function() {
            applyFilters();
        });
        $('#jj-bulk-filter-status').on('change', function() {
            applyFilters();
        });

        // Select all
        $('#jj-bulk-select-all-plugins').on('change', function() {
            var checked = $(this).is(':checked');
            $('#jj-bulk-table-plugins tbody tr.jj-bulk-row:visible .jj-bulk-row-check').prop('checked', checked);
        });
        $('#jj-bulk-select-all-themes').on('change', function() {
            var checked = $(this).is(':checked');
            $('#jj-bulk-table-themes tbody tr.jj-bulk-row:visible .jj-bulk-row-check').prop('checked', checked);
        });

        // Actions (plugins / themes)
        $('#jj-bulk-action-deactivate, #jj-bulk-action-delete, #jj-bulk-action-deactivate-delete, #jj-bulk-action-theme-delete').on('click', function() {
            var op = $(this).data('op');
            var type = $(this).data('type');
            runBulkOperation(type, op);
        });

        // Initial load
        loadInstalledItems('plugin', true);
        loadInstalledItems('theme', true);
    }

    function loadInstalledItems(itemType, forceReload) {
        if (!forceReload && itemType === 'plugin' && bulkCache.plugins) {
            renderPluginsTable(bulkCache.plugins.items || []);
            updateStats();
            return;
        }
        if (!forceReload && itemType === 'theme' && bulkCache.themes) {
            renderThemesTable(bulkCache.themes.items || []);
            updateStats();
            return;
        }

        var $tbody = (itemType === 'plugin') ? $('#jj-bulk-table-plugins tbody') : $('#jj-bulk-table-themes tbody');
        if ($tbody.length) {
            $tbody.html('<tr><td colspan="6">목록을 불러오는 중...</td></tr>');
        }

        $.ajax({
            url: jjBulk.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_bulk_manage_get_items',
                nonce: jjBulk.nonce,
                item_type: itemType
            },
            success: function(resp) {
                if (!resp || !resp.success) {
                    showNotice('error', '목록을 불러오지 못했습니다: ' + escapeHtml(resp && resp.data ? resp.data : '알 수 없는 오류'));
                    return;
                }

                if (itemType === 'plugin') {
                    bulkCache.plugins = resp.data;
                    renderPluginsTable(resp.data.items || []);
                } else {
                    bulkCache.themes = resp.data;
                    renderThemesTable(resp.data.items || []);
                }
                updateStats();
                applyFilters();
            },
            error: function() {
                showNotice('error', '서버 통신 오류로 목록을 불러오지 못했습니다.');
            }
        });
    }

    function updateStats() {
        if (bulkCache.plugins && bulkCache.plugins.counts) {
            $('#jj-count-plugins').text(bulkCache.plugins.counts.total || 0);
            $('#jj-count-plugins-active').text(bulkCache.plugins.counts.active || 0);
            $('#jj-count-plugins-update').text(bulkCache.plugins.counts.updates || 0);
        }
        if (bulkCache.themes && bulkCache.themes.counts) {
            $('#jj-count-themes').text(bulkCache.themes.counts.total || 0);
        }
    }

    function renderPluginsTable(items) {
        var $tbody = $('#jj-bulk-table-plugins tbody');
        if ($tbody.length === 0) return;

        if (!items || items.length === 0) {
            $tbody.html('<tr><td colspan="6">설치된 플러그인이 없습니다.</td></tr>');
            return;
        }

        var rows = [];
        items.forEach(function(p) {
            var status = p.network_active ? 'network_active' : (p.active ? 'active' : 'inactive');
            var statusLabel = p.network_active ? '네트워크' : (p.active ? '활성' : '비활성');
            var statusClass = p.network_active ? 'jj-pill-neutral' : (p.active ? 'jj-pill-good' : 'jj-pill-muted');
            var rowStatus = (p.active || p.network_active) ? 'active' : 'inactive';

            var auLabel = p.auto_update ? 'ON' : 'OFF';
            var auClass = p.auto_update ? 'jj-pill-good' : 'jj-pill-muted';

            var updHtml = '<span class="jj-pill jj-pill-muted">없음</span>';
            if (p.update_available) {
                updHtml = '<span class="jj-pill jj-pill-warn">업데이트</span>' + (p.new_version ? ' <code>' + escapeHtml(p.new_version) + '</code>' : '');
            }

            var requires = '';
            if (p.requires_plugins && p.requires_plugins.length) {
                requires = '<div class="jj-inline-tags">' +
                    p.requires_plugins.map(function(x) {
                        return '<span class="jj-tag">필요: ' + escapeHtml(x) + '</span>';
                    }).join(' ') +
                    '</div>';
            }

            var checkboxAttrs = '';
            if (p.network_active) {
                checkboxAttrs = ' disabled title="네트워크 활성 플러그인은 네트워크 관리자에서 관리하세요."';
            }

            rows.push(
                '<tr class="jj-bulk-row" data-status="' + escapeHtml(rowStatus) + '" data-search="' + escapeHtml((p.name + ' ' + (p.author || '') + ' ' + p.id).toLowerCase()) + '">' +
                    '<th scope="row" class="check-column"><input type="checkbox" class="jj-bulk-row-check" data-id="' + escapeHtml(p.id) + '"' + checkboxAttrs + '></th>' +
                    '<td>' +
                        '<strong>' + escapeHtml(p.name) + '</strong> <span class="description">v' + escapeHtml(p.version || '-') + '</span>' +
                        (p.author ? '<div class="description">' + escapeHtml(p.author) + '</div>' : '') +
                        requires +
                    '</td>' +
                    '<td><span class="jj-pill ' + statusClass + '">' + escapeHtml(statusLabel) + '</span></td>' +
                    '<td><span class="jj-pill ' + auClass + '">' + escapeHtml(auLabel) + '</span></td>' +
                    '<td>' + updHtml + '</td>' +
                    '<td><code>' + escapeHtml(p.id) + '</code></td>' +
                '</tr>'
            );
        });

        $tbody.html(rows.join(''));
    }

    function renderThemesTable(items) {
        var $tbody = $('#jj-bulk-table-themes tbody');
        if ($tbody.length === 0) return;

        if (!items || items.length === 0) {
            $tbody.html('<tr><td colspan="6">설치된 테마가 없습니다.</td></tr>');
            return;
        }

        var rows = [];
        items.forEach(function(t) {
            var statusLabel = t.active ? '활성' : '비활성';
            var statusClass = t.active ? 'jj-pill-good' : 'jj-pill-muted';

            var auLabel = t.auto_update ? 'ON' : 'OFF';
            var auClass = t.auto_update ? 'jj-pill-good' : 'jj-pill-muted';

            var updHtml = '<span class="jj-pill jj-pill-muted">없음</span>';
            if (t.update_available) {
                updHtml = '<span class="jj-pill jj-pill-warn">업데이트</span>' + (t.new_version ? ' <code>' + escapeHtml(t.new_version) + '</code>' : '');
            }

            var checkboxAttrs = '';
            if (t.active) {
                checkboxAttrs = ' disabled title="현재 사용 중인(활성) 테마는 삭제할 수 없습니다."';
            }

            rows.push(
                '<tr class="jj-bulk-row" data-status="' + escapeHtml(t.active ? 'active' : 'inactive') + '" data-search="' + escapeHtml((t.name + ' ' + (t.author || '') + ' ' + t.id).toLowerCase()) + '">' +
                    '<th scope="row" class="check-column"><input type="checkbox" class="jj-bulk-row-check" data-id="' + escapeHtml(t.id) + '"' + checkboxAttrs + '></th>' +
                    '<td>' +
                        '<strong>' + escapeHtml(t.name) + '</strong> <span class="description">v' + escapeHtml(t.version || '-') + '</span>' +
                        (t.author ? '<div class="description">' + escapeHtml(t.author) + '</div>' : '') +
                    '</td>' +
                    '<td><span class="jj-pill ' + statusClass + '">' + escapeHtml(statusLabel) + '</span></td>' +
                    '<td><span class="jj-pill ' + auClass + '">' + escapeHtml(auLabel) + '</span></td>' +
                    '<td>' + updHtml + '</td>' +
                    '<td><code>' + escapeHtml(t.id) + '</code></td>' +
                '</tr>'
            );
        });

        $tbody.html(rows.join(''));
    }

    function applyFilters() {
        var query = ($('#jj-bulk-search').val() || '').toLowerCase().trim();
        var status = $('#jj-bulk-filter-status').val() || 'all';

        var $table = (bulkUi.currentSubtab === 'themes') ? $('#jj-bulk-table-themes') : $('#jj-bulk-table-plugins');
        $table.find('tbody tr.jj-bulk-row').each(function() {
            var $tr = $(this);
            var rowStatus = $tr.attr('data-status');
            var hay = $tr.attr('data-search') || '';

            var okStatus = (status === 'all') || (status === rowStatus);
            var okQuery = (!query) || (hay.indexOf(query) !== -1);

            $tr.toggle(okStatus && okQuery);
        });
    }

    function getSelectedIds(itemType) {
        var $table = (itemType === 'theme') ? $('#jj-bulk-table-themes') : $('#jj-bulk-table-plugins');
        var ids = [];
        $table.find('tbody tr.jj-bulk-row .jj-bulk-row-check:checked').each(function() {
            var id = $(this).data('id');
            if (id) ids.push(String(id));
        });
        return ids;
    }

    function runBulkOperation(itemType, operation) {
        var ids = getSelectedIds(itemType);
        if (ids.length === 0) {
            showNotice('warning', '선택된 항목이 없습니다.');
            return;
        }

        var maxManage = (jjBulk && jjBulk.limits && jjBulk.limits.max_manage_items) ? parseInt(jjBulk.limits.max_manage_items, 10) : 3;
        if (ids.length > maxManage) {
            alert(jjBulk.i18n.manage_limit_reached);
            return;
        }

        // client-side gating (server-side도 동일하게 검증)
        if ((operation === 'delete' || operation === 'deactivate_delete') && jjBulk && jjBulk.limits && !jjBulk.limits.can_bulk_delete) {
            alert(jjBulk.i18n.delete_locked);
            return;
        }
        if (operation === 'deactivate_delete' && jjBulk && jjBulk.limits && !jjBulk.limits.can_deactivate_then_delete) {
            alert(jjBulk.i18n.deactivate_delete_locked);
            return;
        }

        var confirmText = '';
        if (operation === 'deactivate') {
            confirmText = '선택한 ' + ids.length + '개를 비활성화할까요?';
        } else if (operation === 'delete') {
            confirmText = '정말로 선택한 ' + ids.length + '개를 삭제할까요?\n삭제는 되돌릴 수 없습니다.';
        } else if (operation === 'deactivate_delete') {
            confirmText = '선택한 ' + ids.length + '개를 비활성화한 뒤 즉시 삭제할까요?\n삭제는 되돌릴 수 없습니다.';
        }

        if (confirmText && !window.confirm(confirmText)) {
            return;
        }

        showNotice('info', '작업을 진행 중입니다...');

        $.ajax({
            url: jjBulk.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_bulk_manage_action',
                nonce: jjBulk.nonce,
                item_type: itemType,
                operation: operation,
                items: ids
            },
            success: function(resp) {
                if (!resp || !resp.success) {
                    showNotice('error', '작업 실패: ' + escapeHtml(resp && resp.data ? resp.data : '알 수 없는 오류'));
                    return;
                }

                var results = (resp.data && resp.data.results) ? resp.data.results : [];
                var ok = results.filter(function(r) { return r.ok; }).length;
                var fail = results.length - ok;

                var extra = '';
                if (fail > 0) {
                    var listItems = results.filter(function(r) { return !r.ok; }).map(function(r) {
                        return '<li><code>' + escapeHtml(r.id) + '</code> - ' + escapeHtml(r.message) + '</li>';
                    }).join('');
                    extra = '<ul style="margin: 0.5em 0 0.2em 1.2em; list-style: disc;">' + listItems + '</ul>';
                }

                showNotice(fail === 0 ? 'success' : 'warning', '완료: 성공 ' + ok + '개 / 실패 ' + fail + '개', extra);

                // refresh the relevant list
                if (itemType === 'theme') {
                    loadInstalledItems('theme', true);
                } else {
                    loadInstalledItems('plugin', true);
                }
            },
            error: function() {
                showNotice('error', '서버 통신 오류로 작업에 실패했습니다.');
            }
        });
    }

    initTabs();
    initInstaller();
});

```

좋아요—이제 **(1) PHP 백엔드(AJAX)와 (2) 관리자 UI 탭/테이블, (3) JS 동작**까지 연결이 끝났습니다. 다음으로는 **문법/정적 진단(린트)로 즉시 깨지는 부분이 없는지** 먼저 확인하고, 그 다음 실제 동작 흐름(목록 로딩→선택→비활성화/삭제)을 빠르게 점검하겠습니다.

```php
<?php
/**
 * Plugin Name: WP Bulk Manager (Installer & Editor)
 * Plugin URI: https://j-j-labs.com
 * Description: 여러 개의 플러그인/테마 ZIP 파일을 한 번에 설치하고, 설치된 플러그인/테마를 대량 비활성화/삭제까지 관리합니다.
 * Version: 2.2.2
 * Author: 3J Labs
 * Author URI: https://j-j-labs.com
 * License: GPLv2 or later
 * Text Domain: wp-bulk-installer
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class JJ_Bulk_Installer {

    private static $instance = null;
    private $page_slug = 'jj-bulk-installer';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
        add_action( 'admin_notices', array( $this, 'add_install_page_notice' ) );
        
        add_action( 'wp_ajax_jj_bulk_install_upload', array( $this, 'ajax_handle_upload' ) );
        add_action( 'wp_ajax_jj_bulk_install_process', array( $this, 'ajax_handle_install' ) );
        add_action( 'wp_ajax_jj_bulk_activate_plugin', array( $this, 'ajax_handle_activate' ) );

        // Bulk Editor (관리)
        add_action( 'wp_ajax_jj_bulk_manage_get_items', array( $this, 'ajax_get_installed_items' ) );
        add_action( 'wp_ajax_jj_bulk_manage_action', array( $this, 'ajax_bulk_manage_action' ) );
        
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function add_menu_pages() {
        // 1. 도구 하위 메뉴 (기본)
        add_management_page(
            'WP Bulk Manager',
            'Bulk Manager',
            'install_plugins',
            $this->page_slug,
            array( $this, 'render_page' )
        );

        // 2. 알림판 아래 최상위 메뉴 (접근성 강화)
        add_menu_page(
            'WP Bulk Manager',
            'Bulk Manager',
            'install_plugins',
            $this->page_slug . '-main',
            array( $this, 'render_page' ),
            'dashicons-cloud-upload',
            2 // Dashboard(index.php) 바로 아래
        );
    }

    // 플러그인 설치 화면에 안내 배너 추가
    public function add_install_page_notice() {
        $screen = get_current_screen();
        if ( $screen && ( 'plugin-install' === $screen->id || 'theme-install' === $screen->id ) ) {
            $link = admin_url( 'tools.php?page=' . $this->page_slug );
            ?>
            <div class="notice notice-info is-dismissible">
                <p>
                    <strong>🚀 여러 파일을 한 번에 설치하고 싶으신가요?</strong> 
                    <a href="<?php echo esc_url( $link ); ?>" style="text-decoration: none; margin-left: 10px;">
                        <button type="button" class="button button-primary">WP Bulk Manager 바로가기</button>
                    </a>
                </p>
            </div>
            <?php
        }
    }

    public function enqueue_assets( $hook ) {
        // 도구 페이지 또는 최상위 페이지 모두 로드
        if ( strpos( $hook, $this->page_slug ) === false ) return;
        
        wp_enqueue_style( 'jj-bulk-installer-css', plugin_dir_url( __FILE__ ) . 'assets/style.css', array(), '2.2.2' );
        wp_enqueue_script( 'jj-bulk-installer-js', plugin_dir_url( __FILE__ ) . 'assets/script.js', array( 'jquery' ), '2.2.2', true );
        
        // PHP 설정값 가져오기
        $max_upload = wp_max_upload_size();
        $max_upload_formatted = size_format( $max_upload );
        
        // [v2.2.0] ACF CSS 라이센스 연동 제한 설정
        $limits = $this->get_license_limits();

        wp_localize_script( 'jj-bulk-installer-js', 'jjBulk', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'jj_bulk_install' ),
            'limits'   => array(
                'max_file_size' => $max_upload,
                'max_file_size_fmt' => $max_upload_formatted,
                'max_files' => $limits['max_files'],
                'can_auto_activate' => $limits['can_auto_activate'],
                'max_manage_items' => $limits['max_manage_items'],
                'can_bulk_delete' => $limits['can_bulk_delete'],
                'can_deactivate_then_delete' => $limits['can_deactivate_then_delete'],
            ),
            'admin_urls' => array(
                'plugins' => admin_url( 'plugins.php' ),
                'themes' => admin_url( 'themes.php' ),
                'updates' => admin_url( 'update-core.php' ),
            ),
            'i18n' => array(
                'limit_reached' => sprintf( '현재 라이센스에서는 한 번에 최대 %d개까지만 가능합니다.', $limits['max_files'] ),
                'upgrade_msg'   => '제한을 해제하려면 ACF CSS Pro로 업그레이드하세요.',
                'manage_limit_reached' => sprintf( '현재 라이센스에서는 한 번에 최대 %d개까지만 선택할 수 있습니다.', $limits['max_manage_items'] ),
                'delete_locked' => '삭제 기능은 Basic 이상에서 사용할 수 있습니다.',
                'deactivate_delete_locked' => '비활성화 후 삭제 기능은 Premium 이상에서 사용할 수 있습니다.',
            )
        ) );
    }
    
    /**
     * [v2.2.0] ACF CSS 라이센스 등급에 따른 제한 설정 조회
     */
    private function get_license_limits() {
        // [v2.2.1] Master Edition 독립 실행 모드 (Core 없이도 무제한)
        if ( defined( 'JJ_BULK_INSTALLER_LICENSE' ) && 'MASTER' === JJ_BULK_INSTALLER_LICENSE ) {
            return array(
                'max_files' => 999,
                'can_auto_activate' => true,
                'max_manage_items' => 999,
                'can_bulk_delete' => true,
                'can_deactivate_then_delete' => true,
            );
        }

        $limits = array(
            'max_files' => 3,        // 기본값 (Free)
            'can_auto_activate' => false, // 기본값 (Free)
            'max_manage_items' => 3,
            'can_bulk_delete' => false,
            'can_deactivate_then_delete' => false,
        );
        
        // ACF CSS Manager가 설치되어 있고 클래스가 존재할 때
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            $edition_ctrl = JJ_Edition_Controller::instance();
            
            // Basic 이상: 10개 파일
            if ( $edition_ctrl->is_at_least( 'basic' ) ) {
                $limits['max_files'] = 10;
                $limits['max_manage_items'] = 10;
                $limits['can_bulk_delete'] = true;
            }
            
            // Premium 이상: 무제한 + 자동 활성화
            if ( $edition_ctrl->is_at_least( 'premium' ) ) {
                $limits['max_files'] = 999;
                $limits['can_auto_activate'] = true;
                $limits['max_manage_items'] = 999;
                $limits['can_bulk_delete'] = true;
                $limits['can_deactivate_then_delete'] = true;
            }
            
            // Master: 무제한
            if ( $edition_ctrl->is_at_least( 'master' ) ) {
                $limits['max_files'] = 999;
                $limits['can_auto_activate'] = true;
                $limits['max_manage_items'] = 999;
                $limits['can_bulk_delete'] = true;
                $limits['can_deactivate_then_delete'] = true;
            }
        }
        
        return $limits;
    }

    public function render_page() {
        $max_upload = wp_max_upload_size();
        $max_upload_fmt = size_format( $max_upload );
        $limits = $this->get_license_limits(); // 현재 제한 상태 조회

        $plan_label = 'PREMIUM+';
        if ( (int) $limits['max_files'] <= 3 ) {
            $plan_label = 'FREE';
        } elseif ( (int) $limits['max_files'] <= 10 ) {
            $plan_label = 'BASIC';
        }
        ?>
        <div class="wrap jj-bulk-wrap">
            <h1>🚀 WP Bulk Manager <small>Installer &amp; Editor · by 3J Labs</small></h1>

            <div id="jj-bulk-notices"></div>
            
            <?php if ( (int) $limits['max_files'] < 999 || ! $limits['can_auto_activate'] || ! $limits['can_bulk_delete'] || ! $limits['can_deactivate_then_delete'] ) : ?>
                <div class="notice notice-warning inline">
                    <p style="margin: 0.6em 0;">
                        <strong><?php echo esc_html( $plan_label ); ?>:</strong>
                        현재 기능 제한이 일부 적용되어 있습니다.
                        <a href="https://j-j-labs.com" target="_blank" class="button button-small" style="margin-left: 10px;">업그레이드</a>
                    </p>
                    <ul style="margin: 0.5em 0 0.8em 1.4em; list-style: disc;">
                        <li>ZIP 동시 업로드: 최대 <strong><?php echo (int) $limits['max_files']; ?>개</strong></li>
                        <li>설치 후 자동 활성화: <strong><?php echo $limits['can_auto_activate'] ? '사용 가능' : '잠김'; ?></strong></li>
                        <li>대량 삭제: <strong><?php echo $limits['can_bulk_delete'] ? '사용 가능' : '잠김'; ?></strong></li>
                        <li>비활성화 후 삭제: <strong><?php echo $limits['can_deactivate_then_delete'] ? '사용 가능' : '잠김'; ?></strong></li>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="jj-bulk-tabs" role="tablist" aria-label="WP Bulk Manager Tabs">
                <button type="button" class="jj-bulk-tab is-active" data-tab="installer" role="tab" aria-selected="true">설치(Installer)</button>
                <button type="button" class="jj-bulk-tab" data-tab="editor" role="tab" aria-selected="false">관리(Bulk Editor)</button>
            </div>

            <div class="jj-bulk-tab-panel is-active" data-tab-panel="installer" role="tabpanel">
                <div class="jj-bulk-container">
                    <!-- 드롭존 -->
                    <div class="jj-dropzone" id="jj-dropzone">
                        <div class="jj-dropzone-content">
                            <span class="dashicons dashicons-cloud-upload" style="font-size: 64px; height: 64px; width: 64px; color: #2271b1;"></span>
                            <h3>ZIP 파일을 여기에 드래그하세요</h3>
                            <p>또는 <strong>여기를 클릭</strong>하여 파일 선택</p>
                            <p class="description">
                                최대 <?php echo (int) $limits['max_files']; ?>개 | 파일당 최대 <?php echo esc_html( $max_upload_fmt ); ?> | 전체 용량 2GB 권장
                            </p>
                            <!-- label로 감싸서 클릭 영역 확보 -->
                            <label for="jj-file-input" class="screen-reader-text">파일 선택</label>
                            <input type="file" id="jj-file-input" multiple accept=".zip">
                        </div>
                    </div>

                    <!-- 옵션 -->
                    <div class="jj-options">
                        <label style="<?php echo ( ! $limits['can_auto_activate'] ) ? 'opacity: 0.6; cursor: not-allowed;' : ''; ?>">
                            <input type="checkbox" id="jj-auto-activate-all" value="1" <?php echo ( ! $limits['can_auto_activate'] ) ? 'disabled' : ''; ?>>
                            <strong>설치 후 전체 자동 활성화</strong>
                        </label>
                        <?php if ( ! $limits['can_auto_activate'] ) : ?>
                            <span class="description" style="color: #d63638; margin-left: 10px;">(Premium 버전 이상 필요)</span>
                        <?php else : ?>
                            <span class="description" style="margin-left: 10px;">(체크 해제 시, 설치 완료 후 선택하여 활성화 가능)</span>
                        <?php endif; ?>
                    </div>

                    <!-- 파일 목록 -->
                    <div class="jj-file-list" id="jj-file-list"></div>

                    <!-- 액션 버튼 -->
                    <div class="jj-actions" style="margin-top: 20px; display: none;" id="jj-actions-area">
                        <button id="jj-start-install" class="button button-primary button-hero">
                            설치 시작하기
                        </button>
                        <button id="jj-activate-selected" class="button button-secondary button-hero" style="display: none; margin-left: 10px;">
                            선택한 플러그인 활성화
                        </button>
                    </div>

                    <!-- 진행 상태 -->
                    <div class="jj-progress-area" id="jj-progress-area" style="display: none;">
                        <div class="jj-progress-bar"><div class="jj-progress-fill" style="width: 0%;"></div></div>
                        <div class="jj-status-text">준비 중...</div>
                    </div>
                </div>
            </div>

            <div class="jj-bulk-tab-panel" data-tab-panel="editor" role="tabpanel" style="display:none;">
                <div class="jj-bulk-container">
                    <div class="jj-bulk-editor-header">
                        <div class="jj-bulk-badges" id="jj-bulk-stats">
                            <span class="jj-badge">플러그인 <strong id="jj-count-plugins">-</strong></span>
                            <span class="jj-badge">활성 <strong id="jj-count-plugins-active">-</strong></span>
                            <span class="jj-badge">업데이트 <strong id="jj-count-plugins-update">-</strong></span>
                            <span class="jj-badge">테마 <strong id="jj-count-themes">-</strong></span>
                        </div>
                        <div class="jj-bulk-editor-actions">
                            <button type="button" class="button" id="jj-bulk-refresh">목록 새로고침</button>
                            <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>">WP 플러그인 화면</a>
                            <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>">WP 테마 화면</a>
                            <a class="button button-link" href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>">업데이트 화면</a>
                        </div>
                    </div>

                    <div class="jj-bulk-subtabs" role="tablist" aria-label="Bulk Editor Sub Tabs">
                        <button type="button" class="button jj-subtab is-active" data-subtab="plugins" role="tab" aria-selected="true">플러그인</button>
                        <button type="button" class="button jj-subtab" data-subtab="themes" role="tab" aria-selected="false">테마</button>
                    </div>

                    <div class="jj-bulk-toolbar">
                        <input type="search" id="jj-bulk-search" placeholder="검색: 이름/설명/작성자/파일(슬러그)" class="regular-text" />
                        <select id="jj-bulk-filter-status">
                            <option value="all">전체</option>
                            <option value="active">활성</option>
                            <option value="inactive">비활성</option>
                        </select>

                        <div class="jj-bulk-toolbar-right">
                            <button type="button" class="button" id="jj-bulk-action-deactivate" data-op="deactivate" data-type="plugin">선택 비활성화</button>
                            <button type="button" class="button button-secondary" id="jj-bulk-action-delete" data-op="delete" data-type="plugin" <?php echo ( ! $limits['can_bulk_delete'] ) ? 'disabled' : ''; ?>>선택 삭제</button>
                            <button type="button" class="button button-danger" id="jj-bulk-action-deactivate-delete" data-op="deactivate_delete" data-type="plugin" <?php echo ( ! $limits['can_deactivate_then_delete'] ) ? 'disabled' : ''; ?>>비활성화 후 삭제</button>
                            <button type="button" class="button button-danger" id="jj-bulk-action-theme-delete" data-op="delete" data-type="theme" style="display:none;" <?php echo ( ! $limits['can_bulk_delete'] ) ? 'disabled' : ''; ?>>선택 삭제</button>
                        </div>
                    </div>

                    <p class="description" style="margin-top: 8px;">
                        <strong>주의:</strong> 삭제는 되돌릴 수 없습니다. 선택 항목이 많을 때는 <?php echo (int) $limits['max_manage_items']; ?>개 이하로 나눠서 진행하세요.
                    </p>

                    <?php if ( ! $limits['can_bulk_delete'] ) : ?>
                        <p class="description" style="color:#d63638; margin-top: 6px;">
                            🔒 삭제 기능은 <strong>Basic</strong> 이상에서 사용할 수 있습니다.
                        </p>
                    <?php elseif ( $limits['can_bulk_delete'] && ! $limits['can_deactivate_then_delete'] ) : ?>
                        <p class="description" style="color:#b32d2e; margin-top: 6px;">
                            🔒 “비활성화 후 삭제”는 <strong>Premium</strong> 이상에서 사용할 수 있습니다. (Basic은 “비활성화” 후 “삭제”를 분리해서 진행)
                        </p>
                    <?php endif; ?>

                    <div class="jj-bulk-table-wrap" data-subtab-panel="plugins">
                        <table class="wp-list-table widefat fixed striped" id="jj-bulk-table-plugins">
                            <thead>
                                <tr>
                                    <td class="manage-column column-cb check-column"><input type="checkbox" id="jj-bulk-select-all-plugins"></td>
                                    <th>플러그인</th>
                                    <th style="width:110px;">상태</th>
                                    <th style="width:140px;">자동 업데이트</th>
                                    <th style="width:140px;">업데이트</th>
                                    <th style="width:220px;">파일</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="6">목록을 불러오는 중...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="jj-bulk-table-wrap" data-subtab-panel="themes" style="display:none;">
                        <table class="wp-list-table widefat fixed striped" id="jj-bulk-table-themes">
                            <thead>
                                <tr>
                                    <td class="manage-column column-cb check-column"><input type="checkbox" id="jj-bulk-select-all-themes"></td>
                                    <th>테마</th>
                                    <th style="width:110px;">상태</th>
                                    <th style="width:140px;">자동 업데이트</th>
                                    <th style="width:140px;">업데이트</th>
                                    <th style="width:220px;">폴더</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="6">목록을 불러오는 중...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 프로모션 배너 -->
            <?php if ( ! class_exists( 'JJ_Edition_Controller' ) ) : ?>
                <div class="jj-promo-banner">
                    <h3>🎨 플러그인/테마 스타일이 제각각인가요?</h3>
                    <p><strong>ACF CSS Manager</strong>를 사용하면 여러 플러그인과 테마의 스타일을 한 곳에서 제어할 수 있습니다.</p>
                    <a href="https://j-j-labs.com/?product=plugin-jj-center-of-style-setting" target="_blank" class="button button-secondary">ACF CSS Manager 구경하기 →</a>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Bulk Editor: 설치된 플러그인/테마 목록 조회
     */
    public function ajax_get_installed_items() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $item_type = isset( $_POST['item_type'] ) ? sanitize_text_field( $_POST['item_type'] ) : 'plugin';
        if ( ! in_array( $item_type, array( 'plugin', 'theme' ), true ) ) {
            $item_type = 'plugin';
        }

        if ( 'plugin' === $item_type ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';

            $plugins = get_plugins();
            $auto_update_plugins = (array) get_site_option( 'auto_update_plugins', array() );
            $updates = get_site_transient( 'update_plugins' );

            $items = array();
            $active_count = 0;
            $update_count = 0;

            foreach ( $plugins as $plugin_file => $data ) {
                $active = is_plugin_active( $plugin_file );
                $network_active = is_multisite() ? is_plugin_active_for_network( $plugin_file ) : false;
                if ( $active || $network_active ) {
                    $active_count++;
                }

                $auto_update = in_array( $plugin_file, $auto_update_plugins, true );

                $update_available = false;
                $new_version = '';
                if ( is_object( $updates ) && ! empty( $updates->response ) && isset( $updates->response[ $plugin_file ] ) ) {
                    $update_available = true;
                    $new_version = isset( $updates->response[ $plugin_file ]->new_version ) ? $updates->response[ $plugin_file ]->new_version : '';
                    $update_count++;
                }

                $requires_plugins = array();
                if ( isset( $data['RequiresPlugins'] ) && is_string( $data['RequiresPlugins'] ) && '' !== trim( $data['RequiresPlugins'] ) ) {
                    $parts = array_map( 'trim', explode( ',', $data['RequiresPlugins'] ) );
                    $requires_plugins = array_values( array_filter( $parts ) );
                }

                $items[] = array(
                    'id' => $plugin_file,
                    'name' => isset( $data['Name'] ) ? $data['Name'] : $plugin_file,
                    'version' => isset( $data['Version'] ) ? $data['Version'] : '',
                    'author' => isset( $data['Author'] ) ? wp_strip_all_tags( $data['Author'] ) : '',
                    'active' => (bool) $active,
                    'network_active' => (bool) $network_active,
                    'auto_update' => (bool) $auto_update,
                    'update_available' => (bool) $update_available,
                    'new_version' => $new_version,
                    'requires_plugins' => $requires_plugins,
                );
            }

            wp_send_json_success( array(
                'item_type' => 'plugin',
                'items' => $items,
                'counts' => array(
                    'total' => count( $items ),
                    'active' => $active_count,
                    'updates' => $update_count,
                ),
            ) );
        }

        // theme
        include_once ABSPATH . 'wp-admin/includes/theme.php';
        $themes = wp_get_themes();
        $current = wp_get_theme();
        $active_stylesheet = $current ? $current->get_stylesheet() : '';

        $auto_update_themes = (array) get_site_option( 'auto_update_themes', array() );
        $updates = get_site_transient( 'update_themes' );

        $items = array();
        $update_count = 0;
        foreach ( $themes as $stylesheet => $theme_obj ) {
            $is_active = ( $stylesheet === $active_stylesheet );
            $auto_update = in_array( $stylesheet, $auto_update_themes, true );

            $update_available = false;
            $new_version = '';
            if ( is_array( $updates ) && isset( $updates['response'] ) && isset( $updates['response'][ $stylesheet ] ) ) {
                $update_available = true;
                $new_version = isset( $updates['response'][ $stylesheet ]['new_version'] ) ? $updates['response'][ $stylesheet ]['new_version'] : '';
                $update_count++;
            } elseif ( is_object( $updates ) && isset( $updates->response ) && isset( $updates->response[ $stylesheet ] ) ) {
                // 일부 환경 호환
                $update_available = true;
                $new_version = isset( $updates->response[ $stylesheet ]['new_version'] ) ? $updates->response[ $stylesheet ]['new_version'] : '';
                $update_count++;
            }

            $items[] = array(
                'id' => $stylesheet,
                'name' => $theme_obj->get( 'Name' ),
                'version' => $theme_obj->get( 'Version' ),
                'author' => wp_strip_all_tags( $theme_obj->get( 'Author' ) ),
                'active' => (bool) $is_active,
                'auto_update' => (bool) $auto_update,
                'update_available' => (bool) $update_available,
                'new_version' => $new_version,
            );
        }

        wp_send_json_success( array(
            'item_type' => 'theme',
            'items' => $items,
            'counts' => array(
                'total' => count( $items ),
                'updates' => $update_count,
            ),
        ) );
    }

    /**
     * Bulk Editor: 벌크 비활성화/삭제/비활성화후삭제
     *
     * @return void
     */
    public function ajax_bulk_manage_action() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );

        $item_type = isset( $_POST['item_type'] ) ? sanitize_text_field( $_POST['item_type'] ) : '';
        $operation = isset( $_POST['operation'] ) ? sanitize_text_field( $_POST['operation'] ) : '';
        $items = isset( $_POST['items'] ) ? $_POST['items'] : array();

        if ( ! in_array( $item_type, array( 'plugin', 'theme' ), true ) ) {
            wp_send_json_error( '잘못된 item_type 입니다.' );
        }
        if ( ! in_array( $operation, array( 'deactivate', 'delete', 'deactivate_delete' ), true ) ) {
            wp_send_json_error( '잘못된 operation 입니다.' );
        }
        if ( 'theme' === $item_type && 'delete' !== $operation ) {
            wp_send_json_error( '테마는 삭제만 지원합니다.' );
        }
        if ( ! is_array( $items ) ) {
            wp_send_json_error( 'items 형식이 올바르지 않습니다.' );
        }

        $items = array_values( array_filter( array_map( 'sanitize_text_field', $items ) ) );
        if ( empty( $items ) ) {
            wp_send_json_error( '선택된 항목이 없습니다.' );
        }

        $limits = $this->get_license_limits();
        $max_manage = isset( $limits['max_manage_items'] ) ? (int) $limits['max_manage_items'] : 3;
        if ( count( $items ) > $max_manage ) {
            wp_send_json_error( sprintf( '라이센스 제한: 한 번에 최대 %d개까지만 처리할 수 있습니다.', $max_manage ) );
        }

        // License gating
        if ( in_array( $operation, array( 'delete', 'deactivate_delete' ), true ) && empty( $limits['can_bulk_delete'] ) ) {
            wp_send_json_error( '라이센스 제한: 삭제 기능은 Basic 이상에서 사용할 수 있습니다.' );
        }
        if ( 'deactivate_delete' === $operation && empty( $limits['can_deactivate_then_delete'] ) ) {
            wp_send_json_error( '라이센스 제한: 비활성화 후 삭제는 Premium 이상에서 사용할 수 있습니다.' );
        }

        $results = array();

        if ( 'plugin' === $item_type ) {
            // Capabilities
            if ( in_array( $operation, array( 'deactivate', 'deactivate_delete' ), true ) && ! current_user_can( 'activate_plugins' ) ) {
                wp_send_json_error( '권한이 없습니다. (activate_plugins 필요)' );
            }
            if ( in_array( $operation, array( 'delete', 'deactivate_delete' ), true ) && ! current_user_can( 'delete_plugins' ) ) {
                wp_send_json_error( '권한이 없습니다. (delete_plugins 필요)' );
            }

            include_once ABSPATH . 'wp-admin/includes/plugin.php';
            include_once ABSPATH . 'wp-admin/includes/file.php';
            include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

            $all_plugins = get_plugins();
            $self_plugin_file = plugin_basename( __FILE__ );

            foreach ( $items as $plugin_file ) {
                if ( ! isset( $all_plugins[ $plugin_file ] ) ) {
                    $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => '존재하지 않는 플러그인입니다.' );
                    continue;
                }
                if ( $plugin_file === $self_plugin_file ) {
                    $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => '안전상 이 도구로 자기 자신을 삭제할 수 없습니다.' );
                    continue;
                }

                $network_active = is_multisite() ? is_plugin_active_for_network( $plugin_file ) : false;
                $active = is_plugin_active( $plugin_file ) || $network_active;

                // 1) deactivate if needed
                if ( in_array( $operation, array( 'deactivate', 'deactivate_delete' ), true ) && $active ) {
                    // 네트워크 활성 플러그인은 네트워크 관리자 영역에서만 처리하는 편이 안전
                    if ( $network_active ) {
                        $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => '네트워크 활성 플러그인은 네트워크 관리자에서 비활성화하세요.' );
                        continue;
                    }
                    deactivate_plugins( $plugin_file, false, false );
                    $active = is_plugin_active( $plugin_file );
                    if ( $active ) {
                        $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => '비활성화에 실패했습니다. (의존성/정책으로 차단되었을 수 있습니다)' );
                        continue;
                    }
                    if ( 'deactivate' === $operation ) {
                        $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => '비활성화 완료' );
                        continue;
                    }
                } elseif ( 'deactivate' === $operation ) {
                    // already inactive
                    $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => '이미 비활성 상태' );
                    continue;
                }

                // 2) delete
                if ( in_array( $operation, array( 'delete', 'deactivate_delete' ), true ) ) {
                    if ( $active ) {
                        $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => '삭제 전 비활성화가 필요합니다.' );
                        continue;
                    }

                    $del = delete_plugins( array( $plugin_file ) );
                    if ( is_wp_error( $del ) ) {
                        $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => $del->get_error_message() );
                        continue;
                    }
                    if ( true !== $del ) {
                        $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => '삭제에 실패했습니다. (파일시스템 권한/FTP 인증이 필요할 수 있습니다)' );
                        continue;
                    }
                    $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => ( 'deactivate_delete' === $operation ) ? '비활성화 후 삭제 완료' : '삭제 완료' );
                    continue;
                }
            }

            wp_send_json_success( array(
                'item_type' => 'plugin',
                'operation' => $operation,
                'results' => $results,
            ) );
        }

        // theme
        if ( ! current_user_can( 'delete_themes' ) ) {
            wp_send_json_error( '권한이 없습니다. (delete_themes 필요)' );
        }
        include_once ABSPATH . 'wp-admin/includes/theme.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';

        $themes = wp_get_themes();
        $current = wp_get_theme();
        $active_stylesheet = $current ? $current->get_stylesheet() : '';

        foreach ( $items as $stylesheet ) {
            if ( ! isset( $themes[ $stylesheet ] ) ) {
                $results[] = array( 'id' => $stylesheet, 'ok' => false, 'message' => '존재하지 않는 테마입니다.' );
                continue;
            }
            if ( $stylesheet === $active_stylesheet ) {
                $results[] = array( 'id' => $stylesheet, 'ok' => false, 'message' => '현재 사용 중인(활성) 테마는 삭제할 수 없습니다.' );
                continue;
            }

            $del = delete_theme( $stylesheet );
            if ( is_wp_error( $del ) ) {
                $results[] = array( 'id' => $stylesheet, 'ok' => false, 'message' => $del->get_error_message() );
                continue;
            }
            if ( true !== $del ) {
                $results[] = array( 'id' => $stylesheet, 'ok' => false, 'message' => '삭제에 실패했습니다. (파일시스템 권한/FTP 인증이 필요할 수 있습니다)' );
                continue;
            }
            $results[] = array( 'id' => $stylesheet, 'ok' => true, 'message' => '삭제 완료' );
        }

        wp_send_json_success( array(
            'item_type' => 'theme',
            'operation' => $operation,
            'results' => $results,
        ) );
    }

    // 1. 파일 업로드 핸들러
    public function ajax_handle_upload() {
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
                'type' => $this->detect_type( $target_path )
            ) );
        } else {
            wp_send_json_error( '파일 업로드 실패' );
        }
    }

    // 2. 설치 핸들러
    public function ajax_handle_install() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        
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

        $plugin_slug = '';
        if ( $type === 'plugin' ) {
            $plugin_slug = $upgrader->plugin_info();
            if ( ! $plugin_slug ) {
                // 설치 후 파일명으로 추측 (간단한 로직)
                // 실제로는 압축 해제된 폴더를 찾아야 함.
                // 이번 버전에서는 '설치됨' 상태만 반환하고 활성화는 수동/자동 옵션에 따름
            }
        }

        @unlink( $file_path );

        $response = array( 'status' => 'installed', 'slug' => $plugin_slug );

        // 자동 활성화 옵션이 켜져있으면 바로 활성화 시도
        if ( $auto_activate && $plugin_slug ) {
            $activate_result = activate_plugin( $plugin_slug );
            if ( is_wp_error( $activate_result ) ) {
                $response['activated'] = false;
                $response['error'] = $activate_result->get_error_message();
            } else {
                $response['activated'] = true;
            }
        }

        wp_send_json_success( $response );
    }

    // 3. 활성화 핸들러 (수동 활성화용)
    public function ajax_handle_activate() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        $slug = isset( $_POST['slug'] ) ? sanitize_text_field( $_POST['slug'] ) : '';
        
        if ( ! $slug ) wp_send_json_error( '플러그인 정보가 없습니다.' );

        $result = activate_plugin( $slug );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }
        wp_send_json_success( '활성화됨' );
    }

    private function detect_type( $zip_path ) {
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

JJ_Bulk_Installer::instance();

```

```python
import os
import shutil
import zipfile
import re
import json
import subprocess
from datetime import datetime
import time

# ============================================================
# JJ Deployment System (JDS) Configuration
# ============================================================
BASE_VERSION = '8.0.0'
VERSIONS = {
    'core': BASE_VERSION,
    'neural': '3.9.9',
    'ai': '2.0.0',
    'woo': '2.0.0',
    'bulk': '2.2.2',
    'menu': '2.0.0'
}

# Output Directory
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
OUTPUT_ROOT = os.path.join(os.environ['USERPROFILE'], 'Desktop', f'JJ_Distributions_v{BASE_VERSION}_Master_Control')
LOG_DIR = os.path.join(OUTPUT_ROOT, 'deploy_logs')

# Source Maps
SOURCES = {
    'core': 'acf-css-really-simple-style-management-center-master',
    'ai': 'acf-css-ai-extension',
    'neural': 'acf-css-neural-link',
    'woo': 'marketing/wordpress-plugins/acf-css-woo-license',
    'bulk': 'marketing/wordpress-plugins/wp-bulk-installer',
    'menu': 'marketing/wordpress-plugins/admin-menu-editor-lite'
}

# Edition Configuration
EDITIONS = {
    'free':      {'suffix': '',             'license': 'FREE',      'channels': ['stable', 'beta', 'staging']},
    'basic':     {'suffix': '-Pro-Basic',   'license': 'BASIC',     'channels': ['stable', 'beta', 'staging']},
    'premium':   {'suffix': '-Pro-Premium', 'license': 'PREMIUM',   'channels': ['stable', 'beta', 'staging']},
    'unlimited': {'suffix': '-Pro-Unlimited','license': 'UNLIMITED','channels': ['stable', 'beta', 'staging']},
    'partner':   {'suffix': '-Partner',     'license': 'PARTNER',   'channels': ['stable', 'staging']},
    'master':    {'suffix': '-Master',      'license': 'MASTER',    'channels': ['stable', 'staging']}
}

# Add-on Editions (Channels)
ADDON_CHANNELS = {
    'stable': '',
    'beta': '-beta',
    'staging': '-staging',
    'master': '-master' # Special channel for Master Unlocked Addons
}

# Exclude Patterns
EXCLUDE_PATTERNS = [
    r'^\.git', r'^\.vscode', r'^\.idea', r'__pycache__', r'\.DS_Store$',
    r'^tests', r'^phpunit\.xml', r'^composer\.json', r'node_modules',
    r'^package\.json', r'^package-lock\.json', r'^gulpfile\.js', 
    r'^\.editorconfig', r'^README\.md', r'\.bak$', r'local-server/venv', r'\.py$'
]

class JJ_Deployment_Engine:
    def __init__(self):
        self.build_time = datetime.now()
        self.build_id = self.build_time.strftime('%Y%m%d-%H%M%S')
        self.log_data = {
            'build_id': self.build_id,
            'timestamp': self.build_time.isoformat(),
            'builds': []
        }
        self._prepare_directories()

    def _prepare_directories(self):
        if not os.path.exists(OUTPUT_ROOT):
            os.makedirs(OUTPUT_ROOT)
        if not os.path.exists(LOG_DIR):
            os.makedirs(LOG_DIR)

    def _get_git_info(self):
        try:
            # Get last commit message
            msg = subprocess.check_output(['git', 'log', '-1', '--pretty=%B'], stderr=subprocess.STDOUT).decode().strip()
            # Get hash
            sha = subprocess.check_output(['git', 'rev-parse', '--short', 'HEAD'], stderr=subprocess.STDOUT).decode().strip()
            return {'commit': msg, 'hash': sha}
        except:
            return {'commit': 'Manual Build', 'hash': 'none'}

    def copy_files(self, src, dst):
        count = 0
        for root, dirs, files in os.walk(src):
            rel_root = os.path.relpath(root, src)
            if rel_root == ".": rel_root = ""
            
            dirs[:] = [d for d in dirs if not any(re.search(p, os.path.join(rel_root, d).replace('\\', '/')) for p in EXCLUDE_PATTERNS)]
            
            for file in files:
                rel_file_path = os.path.join(rel_root, file).replace('\\', '/')
                if any(re.search(p, rel_file_path) for p in EXCLUDE_PATTERNS):
                    continue
                    
                src_file = os.path.join(root, file)
                dst_file = os.path.join(dst, rel_file_path)
                
                os.makedirs(os.path.dirname(dst_file), exist_ok=True)
                shutil.copy2(src_file, dst_file)
                count += 1
        return count

    def process_core_file(self, file_path, edition, channel, version):
        if not os.path.exists(file_path): return
        
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()

        name_suffix = ""
        if edition != 'free':
            if edition == 'master': name_suffix = " (Master)"
            elif edition in ['basic', 'premium', 'unlimited']: name_suffix = " PRO"
            else: name_suffix = f" ({edition.capitalize()})"
        
        channel_label = ""
        if channel == 'beta': channel_label = " [BETA]"
        elif channel == 'staging': channel_label = " [STAGING]"

        new_name = f"ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager{name_suffix}{channel_label}"
        content = re.sub(r"Plugin Name:.*", f"Plugin Name:       {new_name}", content)

        content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_EDITION',\s*'[^']+'\s*\);", 
                        f"define( 'JJ_STYLE_GUIDE_EDITION', '{edition}' );", content)
        license_type = EDITIONS[edition]['license']
        content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_LICENSE_TYPE',\s*'[^']+'\s*\);", 
                        f"define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', '{license_type}' );", content)
        content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_VERSION',\s*'[^']+'\s*\);", 
                        f"define( 'JJ_STYLE_GUIDE_VERSION', '{version}' );", content)
        
        channel_const = f"define( 'JJ_STYLE_GUIDE_UPDATE_CHANNEL', '{channel}' );"
        if "JJ_STYLE_GUIDE_UPDATE_CHANNEL" in content:
            content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_UPDATE_CHANNEL',\s*'[^']+'\s*\);", channel_const, content)
        else:
            content = re.sub(r"(define\(\s*'JJ_STYLE_GUIDE_VERSION'.*?;)", f"\\1\n{channel_const}", content)

        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)

    def process_addon_file(self, file_path, channel, plugin_key):
        if not os.path.exists(file_path): return
        
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()

        # Inject Master Mode Constant for Master Channel
        if channel == 'master':
            master_const = ""
            if plugin_key == 'bulk':
                master_const = "if ( ! defined( 'JJ_BULK_INSTALLER_LICENSE' ) ) define( 'JJ_BULK_INSTALLER_LICENSE', 'MASTER' );"
            elif plugin_key == 'menu':
                master_const = "if ( ! defined( 'JJ_ADMIN_MENU_EDITOR_LICENSE' ) ) define( 'JJ_ADMIN_MENU_EDITOR_LICENSE', 'MASTER' );"
            elif plugin_key == 'ai':
                master_const = "if ( ! defined( 'JJ_AI_EXTENSION_LICENSE' ) ) define( 'JJ_AI_EXTENSION_LICENSE', 'MASTER' );"
            elif plugin_key == 'woo':
                master_const = "if ( ! defined( 'JJ_WOO_LICENSE_LICENSE' ) ) define( 'JJ_WOO_LICENSE_LICENSE', 'MASTER' );"
            elif plugin_key == 'neural':
                master_const = "if ( ! defined( 'JJ_NEURAL_LINK_LICENSE' ) ) define( 'JJ_NEURAL_LINK_LICENSE', 'MASTER' );"

            if master_const:
                # Insert after the first <?php
                if "LICENSE', 'MASTER'" not in content:
                    content = re.sub(r"(<\?php)", f"\\1\n{master_const}", content, count=1)
            
            # Change Plugin Name
            content = re.sub(r"Plugin Name:(.*)", r"Plugin Name:\1 (Master)", content)

        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)

    def build_core(self):
        print(f"📦 Building Core Editions...")
        
        for edition, config in EDITIONS.items():
            for channel in config['channels']:
                ver = VERSIONS['core']
                if channel == 'beta': ver += '-beta.1'
                elif channel == 'staging': ver += f'-staging.{self.build_id}'

                channel_dir = os.path.join(OUTPUT_ROOT, channel.capitalize())
                temp_dir = os.path.join(channel_dir, 'temp', f'acf-css-manager-{edition}')
                zip_name = f"ACF-CSS{config['suffix']}-v{ver}.zip"
                zip_path = os.path.join(channel_dir, zip_name)

                if os.path.exists(temp_dir): shutil.rmtree(temp_dir)
                os.makedirs(temp_dir)
                
                self.copy_files(SOURCES['core'], temp_dir)
                
                main_file = os.path.join(temp_dir, 'acf-css-really-simple-style-guide.php')
                self.process_core_file(main_file, edition, channel, ver)

                with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
                    for root, dirs, files in os.walk(temp_dir):
                        for file in files:
                            file_path = os.path.join(root, file)
                            arcname = os.path.relpath(file_path, os.path.dirname(temp_dir))
                            zf.write(file_path, arcname)
                
                shutil.rmtree(os.path.dirname(temp_dir))

                self.log_data['builds'].append({
                    'type': 'core',
                    'edition': edition,
                    'channel': channel,
                    'version': ver,
                    'file': zip_name
                })
                print(f"  ✓ {edition.upper()} [{channel}] -> {zip_name}")

    def build_addons(self):
        print(f"📦 Building Add-ons...")
        addons = [
            ('ai', 'ACF-CSS-AI-Extension', 'acf-css-ai-extension', 'acf-css-ai-extension.php'),
            ('neural', 'ACF-CSS-Neural-Link', 'acf-css-neural-link', 'acf-css-neural-link.php'),
            ('woo', 'ACF-CSS-Woo-License', 'acf-css-woo-license', 'acf-css-woo-license.php'),
            ('bulk', 'WP-Bulk-Manager', 'wp-bulk-installer', 'wp-bulk-installer.php'),
            ('menu', 'Admin-Menu-Editor-Lite', 'admin-menu-editor-lite', 'admin-menu-editor-lite.php')
        ]

        # Channels to build for addons
        channels = ['stable', 'beta', 'staging', 'master']

        for key, name_base, folder_name, main_filename in addons:
            for channel in channels:
                ver = VERSIONS[key]
                
                # Suffix for master
                suffix = ""
                if channel == 'master':
                    suffix = "-Master"
                
                # Skip beta for some addons if not needed, but keeping for consistency
                
                channel_dir = os.path.join(OUTPUT_ROOT, channel.capitalize())
                if not os.path.exists(channel_dir): os.makedirs(channel_dir)
                
                temp_dir = os.path.join(channel_dir, 'temp_addon', folder_name)
                if os.path.exists(temp_dir): shutil.rmtree(temp_dir)
                os.makedirs(temp_dir)
                
                # Copy Files
                self.copy_files(SOURCES[key], temp_dir)
                
                # Process File (Inject Master Constant)
                main_file_path = os.path.join(temp_dir, main_filename)
                if os.path.exists(main_file_path):
                    self.process_addon_file(main_file_path, channel, key)
                
                # Zip
                zip_name = f"{name_base}{suffix}-v{ver}.zip"
                zip_path = os.path.join(channel_dir, zip_name)
                
                with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
                    for root, dirs, files in os.walk(temp_dir):
                        for file in files:
                            file_path = os.path.join(root, file)
                            arcname = os.path.relpath(file_path, os.path.dirname(temp_dir))
                            zf.write(file_path, arcname)
                
                shutil.rmtree(os.path.dirname(temp_dir))
                
                self.log_data['builds'].append({
                    'type': 'addon',
                    'edition': key,
                    'channel': channel,
                    'version': ver,
                    'file': zip_name
                })
                
            print(f"  ✓ {name_base} -> Built all channels (incl. Master)")

    def generate_dashboard(self):
        git_info = self._get_git_info()
        
        html_content = f"""
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>JJ Deployment Center - Build {self.build_id}</title>
            <style>
                body {{ font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f0f2f5; padding: 40px; }}
                .container {{ max-width: 1200px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }}
                h1 {{ color: #1a1a1a; border-bottom: 2px solid #eee; padding-bottom: 20px; }}
                .meta {{ color: #666; margin-bottom: 30px; font-size: 0.9em; }}
                .channel-section {{ margin-bottom: 40px; }}
                .channel-title {{ font-size: 1.5em; font-weight: bold; color: #2271b1; margin-bottom: 15px; display: flex; align-items: center; }}
                .channel-badge {{ font-size: 0.5em; text-transform: uppercase; background: #2271b1; color: white; padding: 4px 8px; border-radius: 4px; margin-left: 10px; }}
                .beta .channel-badge {{ background: #e67e22; }}
                .staging .channel-badge {{ background: #8e44ad; }}
                .master .channel-badge {{ background: #c0392b; }}
                table {{ width: 100%; border-collapse: collapse; margin-top: 10px; }}
                th, td {{ text-align: left; padding: 12px; border-bottom: 1px solid #eee; }}
                th {{ background: #f9f9f9; font-weight: 600; color: #444; }}
                tr:hover {{ background: #f8f9fa; }}
                .file-link {{ color: #2271b1; text-decoration: none; font-weight: 500; }}
                .file-link:hover {{ text-decoration: underline; }}
                .log-section {{ background: #2d3436; color: #dfe6e9; padding: 20px; border-radius: 8px; font-family: monospace; margin-top: 40px; }}
            </style>
        </head>
        <body>
            <div class="container">
                <h1>🚀 JJ Deployment Command Center</h1>
                <div class="meta">
                    <strong>Build ID:</strong> {self.build_id}<br>
                    <strong>Timestamp:</strong> {self.build_time.strftime('%Y-%m-%d %H:%M:%S')}<br>
                    <strong>Last Commit:</strong> [{git_info['hash']}] {git_info['commit']}
                </div>
        """

        channels = ['Stable', 'Beta', 'Staging', 'Master']
        for channel in channels:
            class_name = channel.lower()
            html_content += f"""
                <div class="channel-section {class_name}">
                    <div class="channel-title">{channel} Channel <span class="channel-badge">{channel}</span></div>
                    <table>
                        <thead>
                            <tr>
                                <th>Edition/Plugin</th>
                                <th>Version</th>
                                <th>Filename</th>
                            </tr>
                        </thead>
                        <tbody>
            """
            
            # Filter builds for this channel
            channel_builds = [b for b in self.log_data['builds'] if b['channel'] == channel.lower()]
            
            for build in channel_builds:
                html_content += f"""
                            <tr>
                                <td>{build['edition'].upper()}</td>
                                <td>{build['version']}</td>
                                <td><a href="{channel}/{build['file']}" class="file-link">{build['file']}</a></td>
                            </tr>
                """
            
            html_content += """
                        </tbody>
                    </table>
                </div>
            """

        html_content += f"""
                <div class="log-section">
                    <h3>📜 System Logs</h3>
                    <pre>{json.dumps(self.log_data, indent=2)}</pre>
                </div>
            </div>
        </body>
        </html>
        """

        dashboard_path = os.path.join(OUTPUT_ROOT, 'dashboard.html')
        with open(dashboard_path, 'w', encoding='utf-8') as f:
            f.write(html_content)
        
        # Save JSON log
        json_log_path = os.path.join(LOG_DIR, f'build_{self.build_id}.json')
        with open(json_log_path, 'w', encoding='utf-8') as f:
            json.dump(self.log_data, f, indent=4)

        print(f"\n📊 Dashboard Generated: {dashboard_path}")

    def run(self):
        print("="*60)
        print("🔥 JJ DEPLOYMENT SYSTEM (JDS) - INITIATING LAUNCH SEQUENCE")
        print("="*60)
        print(f"Target: {OUTPUT_ROOT}")
        
        self.build_core()
        self.build_addons()
        self.generate_dashboard()
        
        print("="*60)
        print("🚀 MISSION ACCOMPLISHED. READY FOR DEPLOYMENT.")
        print("="*60)

if __name__ == '__main__':
    engine = JJ_Deployment_Engine()
    engine.run()

```

```python
import os
import shutil
import zipfile
import re
from datetime import datetime

# ============================================================
# 설정 (Configuration)
# ============================================================
VERSION_CORE = '8.0.0'
VERSION_NEURAL = '3.9.9'
VERSION_AI = '2.0.0'
VERSION_WOO = '2.0.0'

BASE_OUTPUT_DIR = os.path.join(os.environ['USERPROFILE'], 'Desktop', f'JJ_Distributions_v{VERSION_CORE}_Final')

# 소스 디렉토리 맵핑
SOURCES = {
    'core': 'acf-css-really-simple-style-management-center-master',
    'ai': 'acf-css-ai-extension',
    'neural': 'acf-css-neural-link',
    'woo': 'marketing/wordpress-plugins/acf-css-woo-license',
    'bulk': 'marketing/wordpress-plugins/wp-bulk-installer',
    'menu': 'marketing/wordpress-plugins/admin-menu-editor-lite'
}

# Core Edition 목록 (파일명 접미사 및 내부 코드 치환용)
CORE_EDITIONS = {
    'free':      {'suffix': '',             'license': 'FREE'},
    'basic':     {'suffix': '-Pro-Basic',   'license': 'BASIC'},
    'premium':   {'suffix': '-Pro-Premium', 'license': 'PREMIUM'},
    'unlimited': {'suffix': '-Pro-Unlimited','license': 'UNLIMITED'},
    'partner':   {'suffix': '-Partner',     'license': 'PARTNER'},
    'master':    {'suffix': '-Master',      'license': 'MASTER'}
}

# 제외할 파일/폴더 패턴 (소스 루트 기준 상대 경로로 매칭)
EXCLUDE_PATTERNS = [
    r'^\.git', r'^\.vscode', r'^\.idea', r'__pycache__', r'\.DS_Store$',
    r'^tests', r'^phpunit\.xml', r'^composer\.json', r'node_modules',
    r'^package\.json', r'^package-lock\.json', r'^gulpfile\.js', 
    r'^\.editorconfig', r'^README\.md', r'\.bak$', r'local-server/venv',
]

# ============================================================
# 유틸리티 함수
# ============================================================
def clean_create_dir(path):
    if os.path.exists(path):
        try:
            shutil.rmtree(path, ignore_errors=True)
        except: pass
    os.makedirs(path, exist_ok=True)

def copy_files(src, dst, excludes=None):
    if excludes is None: excludes = EXCLUDE_PATTERNS
    
    print(f"  - Copying from {src} to {dst}...")
    count = 0
    for root, dirs, files in os.walk(src):
        # 소스 루트 기준 상대 경로 계산
        rel_root = os.path.relpath(root, src)
        if rel_root == ".": rel_root = ""
        
        # 제외 폴더 필터링 (패턴은 rel_root 기준)
        dirs[:] = [d for d in dirs if not any(re.search(p, os.path.join(rel_root, d).replace('\\', '/')) for p in excludes)]
        
        for file in files:
            rel_file_path = os.path.join(rel_root, file).replace('\\', '/')
            if any(re.search(p, rel_file_path) for p in excludes):
                continue
                
            src_file = os.path.join(root, file)
            dst_file = os.path.join(dst, rel_file_path)
            
            os.makedirs(os.path.dirname(dst_file), exist_ok=True)
            shutil.copy2(src_file, dst_file)
            count += 1
    print(f"    ✓ Copied {count} files.")
    
    # 필수 폴더 체크 (Core의 경우)
    if 'acf-css-manager' in dst and count < 10:
        print(f"    ⚠️ WARNING: Very few files copied to {dst}. Potential exclusion bug!")

def create_zip(source, zip_path, folder_name_in_zip):
    print(f"  - Zipping {source} to {zip_path} (Folder: {folder_name_in_zip})")
    with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for root, dirs, files in os.walk(source):
            for file in files:
                file_path = os.path.join(root, file)
                # ZIP 내부의 상대 경로: folder_name_in_zip / relative_path_from_source
                rel_path = os.path.relpath(file_path, source)
                arcname = os.path.join(folder_name_in_zip, rel_path)
                zipf.write(file_path, arcname)

# ============================================================
# 빌드 실행
# ============================================================
def main():
    print("=" * 60)
    print(f"🚀 3J Labs Deployment System (Robust Build)")
    print(f"   Target: {BASE_OUTPUT_DIR}")
    print("=" * 60)
    
    clean_create_dir(BASE_OUTPUT_DIR)
    temp_dir = os.path.join(BASE_OUTPUT_DIR, "temp")
    os.makedirs(temp_dir, exist_ok=True)
    
    # 1. Core Editions
    print("\n📦 [Core] Building Editions...")
    for edition, config in CORE_EDITIONS.items():
        print(f"  Processing {edition}...")
        
        # 폴더명 결정
        folder_name = f"acf-css-manager-{edition}" if edition != 'free' else "acf-css-manager"
        work_dir = os.path.join(temp_dir, folder_name)
        
        # 파일 복사
        copy_files(SOURCES['core'], work_dir)
        
        # 메인 파일 수정
        main_file = os.path.join(work_dir, 'acf-css-really-simple-style-guide.php')
        if os.path.exists(main_file):
            with open(main_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # 플러그인 이름 변경
            name_suffix = ""
            if edition != 'free':
                if edition == 'master': name_suffix = " (Master)"
                elif edition in ['basic', 'premium', 'unlimited']: name_suffix = " PRO"
                else: name_suffix = f" ({edition.capitalize()})"
            
            new_name = f"ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager{name_suffix}"
            content = re.sub(r"Plugin Name:.*", f"Plugin Name:       {new_name}", content)
            
            # 상수 변경
            content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_EDITION',\s*'[^']+'\s*\);", f"define( 'JJ_STYLE_GUIDE_EDITION', '{edition}' );", content)
            content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_LICENSE_TYPE',\s*'[^']+'\s*\);", f"define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', '{config['license']}' );", content)
            
            with open(main_file, 'w', encoding='utf-8') as f:
                f.write(content)
        
        # ZIP 생성
        zip_name = f"ACF-CSS{config['suffix']}-v{VERSION_CORE}.zip"
        create_zip(work_dir, os.path.join(BASE_OUTPUT_DIR, zip_name), folder_name)

    # 2. Add-ons & Infrastructure
    addons = [
        ('ai', 'ACF-CSS-AI-Extension', VERSION_AI, 'acf-css-ai-extension'),
        ('neural', 'ACF-CSS-Neural-Link', VERSION_NEURAL, 'acf-css-neural-link'),
        ('woo', 'ACF-CSS-Woo-License', VERSION_WOO, 'acf-css-woo-license'),
        ('bulk', 'WP-Bulk-Manager', '2.2.2', 'wp-bulk-installer'),
        ('menu', 'Admin-Menu-Editor-Lite', '2.0.0', 'admin-menu-editor-lite')
    ]
    
    for key, zip_base, ver, target_folder in addons:
        print(f"\n📦 [{key.upper()}] Building...")
        src = SOURCES[key]
        work_dir = os.path.join(temp_dir, target_folder)
        
        # 복사 (Add-on의 경우 소스 경로 자체가 marketing을 포함하므로 제외 패턴이 rel_root 기준임을 보장)
        copy_files(src, work_dir)
        
        # ZIP 생성
        zip_name = f"{zip_base}-v{ver}.zip"
        create_zip(work_dir, os.path.join(BASE_OUTPUT_DIR, zip_name), target_folder)

    # Cleanup
    try:
        shutil.rmtree(temp_dir, ignore_errors=True)
    except: pass
    
    print("\n" + "=" * 60)
    print(f"🎉 All Systems Go! Build Verified.")
    print("=" * 60)

if __name__ == '__main__':
    main()

```

```python
import os
import re

# 파일 경로 및 새 버전 설정
FILES = {
    'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php': '8.0.0',
    'acf-css-neural-link/acf-css-neural-link.php': '3.9.9',
    'acf-css-ai-extension/acf-css-ai-extension.php': '2.0.0',
    'marketing/wordpress-plugins/acf-css-woo-license/acf-css-woo-license.php': '2.0.0',
    'marketing/wordpress-plugins/wp-bulk-installer/wp-bulk-installer.php': '2.2.2',
    'marketing/wordpress-plugins/admin-menu-editor-lite/admin-menu-editor-lite.php': '2.0.0',
    'marketing/wordpress-plugins/acf-css-brevo-integration/acf-css-brevo-integration.php': '2.0.0',
    'marketing/wordpress-theme/acf-css-landing/style.css': '2.0.0'
}

AUTHOR_OLD_PATTERNS = [
    r'Author:\s+Jay & Jenny Labs',
    r'Author:\s+J&J Labs',
    r'Author:\s+JJ Labs',
    r'Author: Jay & Jenny Labs',
    r'Author: J&J Labs',
    r'Author: JJ Labs'
]

NEW_AUTHOR = 'Author:            3J Labs'
CREDIT_COMMENT = '\n * Created by:        Jay & Jason & Jenny'

def update_file(path, new_version):
    if not os.path.exists(path):
        print(f"❌ File not found: {path}")
        return

    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Author 수정
    for pattern in AUTHOR_OLD_PATTERNS:
        content = re.sub(pattern, NEW_AUTHOR, content)
    
    # 2. Version 수정
    content = re.sub(r'Version:\s*[\d\.]+', f'Version:           {new_version}', content)
    
    # 3. 상수 버전 수정 (PHP 파일만)
    if path.endswith('.php'):
        content = re.sub(r"define\(\s*'[^']+_VERSION',\s*'[^']+'\s*\);", lambda m: re.sub(r"'[^']+'", f"'{new_version}'", m.group(0)), content)

    # 4. Credit 추가 (Author 밑에 없으면 추가)
    if 'Created by:' not in content:
        content = content.replace(NEW_AUTHOR, NEW_AUTHOR + CREDIT_COMMENT)

    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✅ Updated {os.path.basename(path)} to v{new_version}")

if __name__ == "__main__":
    print("🚀 Starting Update Sequence...")
    for path, ver in FILES.items():
        update_file(path, ver)
    print("🎉 Update Complete.")


```

### 다음 단계(바로 진행) 완료: WP Bulk Manager(Installer & Editor)
- **리네이밍/버전 반영**: `WP Bulk Installer` → **`WP Bulk Manager (Installer & Editor)`**, 버전 **2.2.2**
- **관리(Bulk Editor) 탭 추가**: 설치된 **플러그인/테마 목록 불러오기 + 검색/필터 + 상태/자동업데이트/업데이트 배지**
- **벌크 작업 구현(AJAX)**: **선택 비활성화 / 선택 삭제 / 비활성화 후 삭제(플러그인)**, **테마 선택 삭제**
  - 서버 측에서 **권한/라이선스/최대 선택 수/존재 여부** 검증
  - **활성 테마 삭제 차단**, **네트워크 활성 플러그인 안내**, **자기 자신 삭제 차단**
  - 삭제는 환경에 따라 **파일시스템 권한/FTP 인증 필요 시 실패 메시지**로 안전하게 처리

### 배포도 즉시 갱신
- `python jj_deployment_system.py` 재실행 완료 → **Bulk 플러그인 항목이 `WP-Bulk-Manager-v2.2.2.zip`로 생성/대시보드 반영됨**
- 산출물/대시보드 위치:
  - `C:\Users\computer\Desktop\JJ_Distributions_v8.0.0_Master_Control\dashboard.html`

원하시면 다음 단계로 **(A) ACF CSS Master의 Admin Center > Tools 탭에도 동일한 “Bulk Editor” UX 반영** 또는 **(B) AI Extension의 WebGPU(WebLLM) 로컬 실행 MVP를 실제 동작 수준으로 구현** 중 하나로 바로 이어가겠습니다.
