<?php
/**
 * Plugin Name:       WP Bulk Manager - Plugin & Theme Bulk Installer and Editor
 * Plugin URI:        https://3j-labs.com
 * Description:       WP Bulk Manager - 여러 개의 플러그인/테마 ZIP 파일을 한 번에 설치하고, 설치된 플러그인/테마를 대량 비활성화/삭제까지 관리하는 강력한 도구입니다. ACF CSS (Advanced Custom Fonts & Colors & Styles) 패밀리 플러그인으로, Pro 버전과 연동 시 무제한 기능을 제공합니다.
 * Version:           4.0.0
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com
 * Text Domain:       wp-bulk-manager
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package WP_Bulk_Manager
 */

define( 'WP_BULK_MANAGER_VERSION', '4.0.0' ); // [v4.0.0] 메뉴 시스템 최적화 및 업로드 안정성 강화 메이저 업데이트

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
        // [v2.4.0] admin_menu 훅 우선순위를 1로 설정 + 메뉴 순서 강제 지정
        add_action( 'admin_menu', array( $this, 'add_menu_pages' ), 1 );
        add_action( 'admin_notices', array( $this, 'add_install_page_notice' ) );
        
        // [v2.4.0] 메뉴 순서 강제 지정 (다른 플러그인에 의해 밀리지 않도록)
        add_filter( 'custom_menu_order', '__return_true' );
        add_filter( 'menu_order', array( $this, 'force_menu_order' ), 999 );
        
        // [v2.4.0] 메뉴 강조 스타일 (배경색, 볼드, 아이콘)
        add_action( 'admin_head', array( $this, 'add_menu_highlight_styles' ) );
        
        add_action( 'wp_ajax_jj_bulk_install_upload', array( $this, 'ajax_handle_upload' ) );
        add_action( 'wp_ajax_jj_bulk_install_process', array( $this, 'ajax_handle_install' ) );
        add_action( 'wp_ajax_jj_bulk_activate_plugin', array( $this, 'ajax_handle_activate' ) );

        // Bulk Editor (관리)
        add_action( 'wp_ajax_jj_bulk_manage_get_items', array( $this, 'ajax_get_installed_items' ) );
        add_action( 'wp_ajax_jj_bulk_manage_action', array( $this, 'ajax_bulk_manage_action' ) );
        add_action( 'wp_ajax_jj_bulk_auto_update_toggle', array( $this, 'ajax_bulk_auto_update_toggle' ) );
        
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        
        // [Phase 19.1] 플러그인 목록 페이지 UI/UX 개선
        $this->init_plugin_list_enhancer();
    }
    
    /**
     * [Phase 19.1] 플러그인 목록 페이지 향상 초기화
     */
    private function init_plugin_list_enhancer() {
        // ACF CSS Manager의 Plugin List Enhancer 클래스 사용
        if ( class_exists( 'JJ_Plugin_List_Enhancer' ) ) {
            $enhancer = new JJ_Plugin_List_Enhancer();
            $enhancer->init( array(
                'plugin_file' => __FILE__,
                'plugin_name' => 'WP Bulk Manager',
                'settings_url' => admin_url( 'tools.php?page=jj-bulk-installer' ),
                'text_domain' => 'wp-bulk-manager',
                'version_constant' => 'WP_BULK_MANAGER_VERSION',
                'license_constant' => 'JJ_BULK_INSTALLER_LICENSE',
                'upgrade_url' => 'https://3j-labs.com',
                'docs_url' => admin_url( 'tools.php?page=jj-bulk-installer' ),
                'support_url' => 'https://3j-labs.com/support',
            ) );
        } else {
            // ACF CSS Manager가 없을 경우 기본 링크만 추가
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_links' ) );
            add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 2 );
        }
    }
    
    /**
     * [Phase 19.1] 기본 플러그인 액션 링크 (ACF CSS Manager가 없을 경우)
     */
    public function add_plugin_action_links( $links ) {
        $new_links = array();
        $new_links['settings'] = '<a href="' . esc_url( admin_url( 'tools.php?page=jj-bulk-installer' ) ) . '" style="font-weight: 800; color: #2271b1; text-decoration: none;">⚙️ <strong>' . __( '설정 열기', 'wp-bulk-manager' ) . '</strong></a>';
        return array_merge( $new_links, $links );
    }
    
    /**
     * [Phase 19.1] 기본 플러그인 행 메타 (ACF CSS Manager가 없을 경우)
     */
    public function add_plugin_row_meta( $plugin_meta, $plugin_file ) {
        if ( $plugin_file !== plugin_basename( __FILE__ ) ) {
            return $plugin_meta;
        }
        
        $new_meta = array();
        $new_meta[] = '<a href="' . esc_url( 'https://3j-labs.com' ) . '" target="_blank" rel="noopener noreferrer" style="color: #2271b1; font-weight: 600;">🌐 ' . __( '공식 사이트', 'wp-bulk-manager' ) . '</a>';
        $new_meta[] = '<a href="' . esc_url( admin_url( 'tools.php?page=jj-bulk-installer' ) ) . '" style="color: #135e96; font-weight: 600;">📚 ' . __( '문서', 'wp-bulk-manager' ) . '</a>';
        
        return array_merge( $plugin_meta, $new_meta );
    }

    /**
     * [v2.4.0] 메뉴 강조 스타일 추가
     * - 에메랄드 그린 배경색 (#10b981)
     * - 볼드 텍스트
     * - 호버 시 더 진한 색상
     */
    public function add_menu_highlight_styles() {
        $menu_slug = $this->page_slug . '-main';
        ?>
        <style>
            /* WP Bulk Manager 메뉴 강조 - 에메랄드 그린 */
            #adminmenu li.menu-top[class*="<?php echo esc_attr( $menu_slug ); ?>"] > a,
            #adminmenu li.toplevel_page_<?php echo esc_attr( $menu_slug ); ?> > a {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
                color: #fff !important;
                font-weight: 700 !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
                border-radius: 4px;
                margin: 2px 8px;
                transition: all 0.2s ease;
            }
            #adminmenu li.menu-top[class*="<?php echo esc_attr( $menu_slug ); ?>"] > a:hover,
            #adminmenu li.toplevel_page_<?php echo esc_attr( $menu_slug ); ?> > a:hover {
                background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
                color: #fff !important;
            }
            #adminmenu li.menu-top[class*="<?php echo esc_attr( $menu_slug ); ?>"] > a .wp-menu-image:before,
            #adminmenu li.toplevel_page_<?php echo esc_attr( $menu_slug ); ?> > a .wp-menu-image:before {
                color: #fff !important;
            }
            /* 현재 선택된 상태 */
            #adminmenu li.menu-top[class*="<?php echo esc_attr( $menu_slug ); ?>"].current > a,
            #adminmenu li.toplevel_page_<?php echo esc_attr( $menu_slug ); ?>.current > a,
            #adminmenu li.menu-top[class*="<?php echo esc_attr( $menu_slug ); ?>"].wp-has-current-submenu > a,
            #adminmenu li.toplevel_page_<?php echo esc_attr( $menu_slug ); ?>.wp-has-current-submenu > a {
                background: linear-gradient(135deg, #047857 0%, #065f46 100%) !important;
                color: #fff !important;
            }
        </style>
        <?php
    }

    /**
     * [v2.4.0] 메뉴 순서 강제 지정
     * 다른 플러그인(Admin Menu Editor, Kinsta 등)에 의해 메뉴가 숨겨지거나 밀리지 않도록
     */
    public function force_menu_order( $menu_order ) {
        global $menu;
        
        if ( ! is_array( $menu_order ) ) {
            return $menu_order;
        }
        
        // 우리 메뉴 슬러그
        $our_slug = $this->page_slug . '-main';
        
        // 현재 메뉴 순서에서 우리 메뉴 위치 찾기
        $our_position = array_search( $our_slug, $menu_order );
        
        if ( $our_position !== false ) {
            // 우리 메뉴를 제거
            unset( $menu_order[ $our_position ] );
            $menu_order = array_values( $menu_order );
        }
        
        // Dashboard(index.php) 바로 뒤에 우리 메뉴 삽입
        $dashboard_position = array_search( 'index.php', $menu_order );
        if ( $dashboard_position !== false ) {
            array_splice( $menu_order, $dashboard_position + 1, 0, $our_slug );
        } else {
            // Dashboard를 찾지 못하면 맨 앞에 삽입
            array_unshift( $menu_order, $our_slug );
        }
        
        return $menu_order;
    }

    public function add_menu_pages() {
        // 1. 알림판 아래 최상위 메뉴 (접근성 강화) - 우선순위 높음
        // [v2.3.7] 더 눈에 띄는 메뉴 등록 + 고유한 슬러그
        add_menu_page(
            __( 'WP 벌크 매니저', 'wp-bulk-manager' ),
            __( '벌크 매니저 🚀', 'wp-bulk-manager' ), // 업로드 아이콘 + 이모지 로켓
            'manage_options', // 관리자 권한
            $this->page_slug . '-main',
            array( $this, 'render_page' ),
            'dashicons-cloud-upload',
            2 // Dashboard와 동일한 position (force_menu_order에서 순서 재조정)
        );

        // 2. 도구 하위 메뉴 (명확한 이름으로 표기)
        add_management_page(
            __( '플러그인&테마 벌크 매니저', 'wp-bulk-manager' ),
            __( '플러그인&테마 벌크 매니저', 'wp-bulk-manager' ),
            'install_plugins',
            $this->page_slug,
            array( $this, 'render_page' )
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
        
        wp_enqueue_style( 'jj-bulk-installer-css', plugin_dir_url( __FILE__ ) . 'assets/style.css', array(), WP_BULK_MANAGER_VERSION );
        wp_enqueue_script( 'jj-bulk-installer-js', plugin_dir_url( __FILE__ ) . 'assets/script.js', array( 'jquery' ), WP_BULK_MANAGER_VERSION, true );
        
        // PHP 설정값 가져오기
        $max_upload = wp_max_upload_size();
        $max_upload_formatted = size_format( $max_upload );
        
        // [Phase 19.1] 서버 사양 정보 가져오기
        $server_info = $this->get_server_upload_info();
        
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
        // [v2.3.2] Master Edition 감지 강화
        // 1. 상수로 정의된 경우
        if ( defined( 'JJ_BULK_INSTALLER_LICENSE' ) && 'MASTER' === JJ_BULK_INSTALLER_LICENSE ) {
            return array(
                'max_files' => 999,
                'can_auto_activate' => true,
                'max_manage_items' => 999,
                'can_bulk_delete' => true,
                'can_deactivate_then_delete' => true,
                'is_master' => true, // [v2.3.2] 마스터 버전 플래그 추가
            );
        }
        
        // 2. 플러그인 폴더명에 'master'가 포함된 경우 (마스터 빌드)
        $plugin_dir = dirname( plugin_basename( __FILE__ ) );
        if ( false !== strpos( strtolower( $plugin_dir ), 'master' ) ) {
            return array(
                'max_files' => 999,
                'can_auto_activate' => true,
                'max_manage_items' => 999,
                'can_bulk_delete' => true,
                'can_deactivate_then_delete' => true,
                'is_master' => true, // [v2.3.2] 마스터 버전 플래그 추가
            );
        }

        $limits = array(
            'max_files' => 3,        // 기본값 (Free)
            'can_auto_activate' => false, // 기본값 (Free)
            'max_manage_items' => 3,
            'can_bulk_delete' => false,
            'can_deactivate_then_delete' => false,
            'is_master' => false, // [v2.3.1] 마스터 버전 플래그 추가
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
                $limits['is_master'] = true; // [v2.3.2] 마스터 버전 플래그 추가
            }
        }
        
        return $limits;
    }

    /**
     * [Phase 19.1] 서버 업로드 사양 정보 조회
     */
    private function get_server_upload_info() {
        $max_upload = wp_max_upload_size();
        $max_upload_fmt = size_format( $max_upload );
        
        // PHP 설정값들
        $upload_max_filesize = ini_get( 'upload_max_filesize' );
        $post_max_size = ini_get( 'post_max_size' );
        $max_file_uploads = ini_get( 'max_file_uploads' );
        $memory_limit = ini_get( 'memory_limit' );
        
        // 바이트로 변환
        $upload_max_bytes = $this->convert_to_bytes( $upload_max_filesize );
        $post_max_bytes = $this->convert_to_bytes( $post_max_size );
        
        // 실제 제한값은 가장 작은 값
        $effective_max = min( $max_upload, $upload_max_bytes, $post_max_bytes );
        $effective_max_fmt = size_format( $effective_max );
        
        return array(
            'max_file_size' => $effective_max,
            'max_file_size_fmt' => $effective_max_fmt,
            'upload_max_filesize' => $upload_max_filesize,
            'post_max_size' => $post_max_size,
            'max_file_uploads' => $max_file_uploads,
            'memory_limit' => $memory_limit,
            'wp_max_upload_size' => $max_upload,
            'wp_max_upload_size_fmt' => $max_upload_fmt,
        );
    }
    
    /**
     * [Phase 19.1] PHP 설정값 문자열을 바이트로 변환
     */
    private function convert_to_bytes( $val ) {
        $val = trim( $val );
        $last = strtolower( $val[ strlen( $val ) - 1 ] );
        $val = (int) $val;
        
        switch ( $last ) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        
        return $val;
    }

    public function render_page() {
        $max_upload = wp_max_upload_size();
        $max_upload_fmt = size_format( $max_upload );
        $limits = $this->get_license_limits(); // 현재 제한 상태 조회
        $server_info = $this->get_server_upload_info(); // [Phase 19.1] 서버 사양 정보

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
            
            <?php 
            // [v2.3.2] 마스터 버전일 때는 업그레이드 메시지 표시 안 함
            $is_master = isset( $limits['is_master'] ) && $limits['is_master'];
            $show_upgrade_notice = ! $is_master && ( (int) $limits['max_files'] < 999 || ! $limits['can_auto_activate'] || ! $limits['can_bulk_delete'] || ! $limits['can_deactivate_then_delete'] );
            
            if ( $show_upgrade_notice ) : ?>
                <div class="notice notice-warning inline">
                    <p style="margin: 0.6em 0;">
                        <strong><?php echo esc_html( $plan_label ); ?>:</strong>
                        현재 기능 제한이 일부 적용되어 있습니다.
                        <a href="https://3j-labs.com" target="_blank" class="button button-small" style="margin-left: 10px;">업그레이드</a>
                    </p>
                    <ul style="margin: 0.5em 0 0.8em 1.4em; list-style: disc;">
                        <li>ZIP 동시 업로드: 최대 <strong><?php echo (int) $limits['max_files']; ?>개</strong></li>
                        <li>설치 후 자동 활성화: <strong><?php echo $limits['can_auto_activate'] ? '사용 가능' : '잠김'; ?></strong></li>
                        <li>대량 삭제: <strong><?php echo $limits['can_bulk_delete'] ? '사용 가능' : '잠김'; ?></strong></li>
                        <li>비활성화 후 삭제: <strong><?php echo $limits['can_deactivate_then_delete'] ? '사용 가능' : '잠김'; ?></strong></li>
                    </ul>
                </div>
            <?php elseif ( $is_master ) : ?>
                <div class="notice notice-success inline">
                    <p style="margin: 0.6em 0;">
                        <strong>⭐ Master 버전:</strong> 모든 기능이 무제한으로 사용 가능합니다.
                    </p>
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
                                최대 <?php echo (int) $limits['max_files']; ?>개 | 파일당 최대 1GB (서버 설정에 따라 파일당 최대 <?php echo esc_html( $server_info['max_file_size_fmt'] ); ?>입니다. 더 높은 용량을 사용하려면 서버 설정을 변경하세요) | 전체 용량 최대 10GB (가급적 2GB 이하 첨부 권장)
                            </p>
                            <?php if ( $server_info['max_file_size'] < 1073741824 ) : // 1GB 미만인 경우 ?>
                                <p class="description" style="color: #d63638; margin-top: 0.5em;">
                                    <strong>⚠️ 주의:</strong> 현재 서버 설정으로는 파일당 최대 <?php echo esc_html( $server_info['max_file_size_fmt'] ); ?>까지만 업로드 가능합니다. 
                                    더 큰 파일을 업로드하려면 서버의 <code>upload_max_filesize</code> (현재: <?php echo esc_html( $server_info['upload_max_filesize'] ); ?>) 
                                    및 <code>post_max_size</code> (현재: <?php echo esc_html( $server_info['post_max_size'] ); ?>) 설정을 변경해야 합니다.
                                </p>
                            <?php endif; ?>
                            <!-- label로 감싸서 클릭 영역 확보 -->
                            <label for="jj-file-input" class="screen-reader-text">파일 선택</label>
                            <input type="file" id="jj-file-input" multiple accept=".zip">
                        </div>
                    </div>

                    <!-- 파일 목록 -->
                    <div class="jj-file-list-container">
                        <!-- 대기 목록 (설치 전 업로드 목록) -->
                        <div class="jj-file-list-section" id="jj-file-list-pending">
                            <h3 class="jj-section-title">
                                📦 설치 전 업로드 목록
                                <span class="jj-section-count" id="jj-pending-count">0개</span>
                            </h3>
                            <div class="jj-file-list" id="jj-file-list"></div>
                        </div>
                        
                        <!-- 완료 목록 (설치 완료된 항목) -->
                        <div class="jj-file-list-section" id="jj-file-list-completed" style="display: none;">
                            <h3 class="jj-section-title">
                                ✅ 완료 목록
                                <span class="jj-section-count" id="jj-completed-count">0개</span>
                            </h3>
                            <div class="jj-file-list" id="jj-file-list-completed-items"></div>
                        </div>
                    </div>
                    
                    <!-- 선택 제어 버튼 (파일 목록 위) -->
                    <div class="jj-selection-controls" id="jj-selection-controls" style="display: none; margin-bottom: 15px;">
                        <div class="jj-selection-buttons">
                            <button type="button" class="button button-small" id="jj-select-all">
                                ☑️ 전체 선택
                            </button>
                            <button type="button" class="button button-small" id="jj-select-none">
                                ☐ 선택 해제
                            </button>
                            <span class="jj-selection-info" id="jj-selection-info">0개 선택됨</span>
                        </div>
                        <div class="jj-activate-controls" style="margin-top: 10px;">
                            <button type="button" class="button button-primary" id="jj-activate-selected-plugins" style="display: none;">
                                선택한 플러그인 자동 활성화
                            </button>
                            <span class="description" style="margin-left: 10px; color: #646970;">
                                💡 <strong>팁:</strong> Ctrl 키를 누른 채로 클릭하면 여러 개 선택, Shift 키를 누른 채로 클릭하면 범위 선택이 가능합니다.
                                <a href="#" class="jj-show-tooltip" data-tooltip="selection-help" style="text-decoration: underline; margin-left: 5px;">자세히 보기</a>
                            </span>
                        </div>
                    </div>

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
                            <button type="button" class="button button-primary" id="jj-bulk-action-activate" data-op="activate" data-type="plugin">선택 활성화</button>
                            <button type="button" class="button" id="jj-bulk-action-deactivate" data-op="deactivate" data-type="plugin">선택 비활성화</button>
                            <button type="button" class="button button-secondary" id="jj-bulk-action-delete" data-op="delete" data-type="plugin" <?php echo ( ! $limits['can_bulk_delete'] ) ? 'disabled' : ''; ?>>선택 삭제</button>
                            <button type="button" class="button button-danger" id="jj-bulk-action-deactivate-delete" data-op="deactivate_delete" data-type="plugin" <?php echo ( ! $limits['can_deactivate_then_delete'] ) ? 'disabled' : ''; ?>>비활성화 후 삭제</button>
                            <button type="button" class="button" id="jj-bulk-action-auto-update-enable" data-op="auto_update_enable" data-type="plugin">자동 업데이트 허용</button>
                            <button type="button" class="button" id="jj-bulk-action-auto-update-disable" data-op="auto_update_disable" data-type="plugin">자동 업데이트 비허용</button>
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
                    <a href="https://3j-labs.com" target="_blank" class="button button-secondary">ACF CSS Manager 구경하기 →</a>
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
        if ( ! in_array( $operation, array( 'activate', 'deactivate', 'delete', 'deactivate_delete', 'auto_update_enable', 'auto_update_disable' ), true ) ) {
            wp_send_json_error( '잘못된 operation 입니다.' );
        }
        if ( 'theme' === $item_type && ! in_array( $operation, array( 'delete', 'auto_update_enable', 'auto_update_disable' ), true ) ) {
            wp_send_json_error( '테마는 삭제와 자동 업데이트 관리만 지원합니다.' );
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
            if ( in_array( $operation, array( 'activate', 'deactivate', 'deactivate_delete' ), true ) && ! current_user_can( 'activate_plugins' ) ) {
                wp_send_json_error( '권한이 없습니다. (activate_plugins 필요)' );
            }
            if ( in_array( $operation, array( 'delete', 'deactivate_delete' ), true ) && ! current_user_can( 'delete_plugins' ) ) {
                wp_send_json_error( '권한이 없습니다. (delete_plugins 필요)' );
            }
            if ( in_array( $operation, array( 'auto_update_enable', 'auto_update_disable' ), true ) && ! current_user_can( 'update_plugins' ) ) {
                wp_send_json_error( '권한이 없습니다. (update_plugins 필요)' );
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
                if ( $plugin_file === $self_plugin_file && in_array( $operation, array( 'delete', 'deactivate_delete' ), true ) ) {
                    $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => '안전상 이 도구로 자기 자신을 삭제할 수 없습니다.' );
                    continue;
                }

                $network_active = is_multisite() ? is_plugin_active_for_network( $plugin_file ) : false;
                $active = is_plugin_active( $plugin_file ) || $network_active;

                // 0) activate if needed
                if ( 'activate' === $operation && ! $active ) {
                    $result = activate_plugin( $plugin_file, '', false, false );
                    if ( is_wp_error( $result ) ) {
                        $results[] = array( 'id' => $plugin_file, 'ok' => false, 'message' => '활성화 실패: ' . $result->get_error_message() );
                        continue;
                    }
                    $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => '활성화 완료' );
                    continue;
                } elseif ( 'activate' === $operation ) {
                    $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => '이미 활성 상태' );
                    continue;
                }

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

                // 3) auto_update_enable / auto_update_disable
                if ( in_array( $operation, array( 'auto_update_enable', 'auto_update_disable' ), true ) ) {
                    $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
                    $key = array_search( $plugin_file, $auto_updates, true );
                    
                    if ( 'auto_update_enable' === $operation ) {
                        if ( false === $key ) {
                            $auto_updates[] = $plugin_file;
                            update_site_option( 'auto_update_plugins', $auto_updates );
                        }
                        $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => '자동 업데이트 허용' );
                    } else {
                        if ( false !== $key ) {
                            unset( $auto_updates[ $key ] );
                            $auto_updates = array_values( $auto_updates );
                            update_site_option( 'auto_update_plugins', $auto_updates );
                        }
                        $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => '자동 업데이트 비허용' );
                    }
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
        if ( 'delete' === $operation && ! current_user_can( 'delete_themes' ) ) {
            wp_send_json_error( '권한이 없습니다. (delete_themes 필요)' );
        }
        if ( in_array( $operation, array( 'auto_update_enable', 'auto_update_disable' ), true ) && ! current_user_can( 'update_themes' ) ) {
            wp_send_json_error( '권한이 없습니다. (update_themes 필요)' );
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

            // delete operation
            if ( 'delete' === $operation ) {
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
                continue;
            }

            // auto_update_enable / auto_update_disable
            if ( in_array( $operation, array( 'auto_update_enable', 'auto_update_disable' ), true ) ) {
                $auto_updates = (array) get_site_option( 'auto_update_themes', array() );
                $key = array_search( $stylesheet, $auto_updates, true );
                
                if ( 'auto_update_enable' === $operation ) {
                    if ( false === $key ) {
                        $auto_updates[] = $stylesheet;
                        update_site_option( 'auto_update_themes', $auto_updates );
                    }
                    $results[] = array( 'id' => $stylesheet, 'ok' => true, 'message' => '자동 업데이트 허용' );
                } else {
                    if ( false !== $key ) {
                        unset( $auto_updates[ $key ] );
                        $auto_updates = array_values( $auto_updates );
                        update_site_option( 'auto_update_themes', $auto_updates );
                    }
                    $results[] = array( 'id' => $stylesheet, 'ok' => true, 'message' => '자동 업데이트 비허용' );
                }
                continue;
            }
        }

        wp_send_json_success( array(
            'item_type' => 'theme',
            'operation' => $operation,
            'results' => $results,
        ) );
    }

    /**
     * [Phase 19.1] 자동 업데이트 일괄 변경
     */
    public function ajax_bulk_auto_update_toggle() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'update_plugins' ) && ! current_user_can( 'update_themes' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $item_type = isset( $_POST['item_type'] ) ? sanitize_text_field( wp_unslash( $_POST['item_type'] ) ) : '';
        $operation = isset( $_POST['operation'] ) ? sanitize_text_field( wp_unslash( $_POST['operation'] ) ) : ''; // 'enable' or 'disable'
        $items = isset( $_POST['items'] ) ? $_POST['items'] : array();

        if ( ! in_array( $item_type, array( 'plugin', 'theme' ), true ) ) {
            wp_send_json_error( '잘못된 item_type 입니다.' );
        }
        if ( ! in_array( $operation, array( 'enable', 'disable' ), true ) ) {
            wp_send_json_error( '잘못된 operation 입니다.' );
        }
        if ( ! is_array( $items ) || empty( $items ) ) {
            wp_send_json_error( '선택된 항목이 없습니다.' );
        }

        $items = array_values( array_filter( array_map( 'sanitize_text_field', $items ) ) );
        $results = array();

        if ( 'plugin' === $item_type ) {
            if ( ! current_user_can( 'update_plugins' ) ) {
                wp_send_json_error( '권한이 없습니다. (update_plugins 필요)' );
            }

            $auto_update_plugins = (array) get_site_option( 'auto_update_plugins', array() );

            foreach ( $items as $plugin_file ) {
                if ( 'enable' === $operation ) {
                    if ( ! in_array( $plugin_file, $auto_update_plugins, true ) ) {
                        $auto_update_plugins[] = $plugin_file;
                        $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => '자동 업데이트 활성화됨' );
                    } else {
                        $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => '이미 자동 업데이트 활성화됨' );
                    }
                } else {
                    $key = array_search( $plugin_file, $auto_update_plugins, true );
                    if ( false !== $key ) {
                        unset( $auto_update_plugins[ $key ] );
                        $auto_update_plugins = array_values( $auto_update_plugins );
                        $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => '자동 업데이트 비활성화됨' );
                    } else {
                        $results[] = array( 'id' => $plugin_file, 'ok' => true, 'message' => '이미 자동 업데이트 비활성화됨' );
                    }
                }
            }

            update_site_option( 'auto_update_plugins', $auto_update_plugins );
        } else {
            // theme
            if ( ! current_user_can( 'update_themes' ) ) {
                wp_send_json_error( '권한이 없습니다. (update_themes 필요)' );
            }

            $auto_update_themes = (array) get_site_option( 'auto_update_themes', array() );

            foreach ( $items as $stylesheet ) {
                if ( 'enable' === $operation ) {
                    if ( ! in_array( $stylesheet, $auto_update_themes, true ) ) {
                        $auto_update_themes[] = $stylesheet;
                        $results[] = array( 'id' => $stylesheet, 'ok' => true, 'message' => '자동 업데이트 활성화됨' );
                    } else {
                        $results[] = array( 'id' => $stylesheet, 'ok' => true, 'message' => '이미 자동 업데이트 활성화됨' );
                    }
                } else {
                    $key = array_search( $stylesheet, $auto_update_themes, true );
                    if ( false !== $key ) {
                        unset( $auto_update_themes[ $key ] );
                        $auto_update_themes = array_values( $auto_update_themes );
                        $results[] = array( 'id' => $stylesheet, 'ok' => true, 'message' => '자동 업데이트 비활성화됨' );
                    } else {
                        $results[] = array( 'id' => $stylesheet, 'ok' => true, 'message' => '이미 자동 업데이트 비활성화됨' );
                    }
                }
            }

            update_site_option( 'auto_update_themes', $auto_update_themes );
        }

        wp_send_json_success( array(
            'item_type' => $item_type,
            'operation' => $operation,
            'results' => $results,
        ) );
    }

    // 1. 파일 업로드 핸들러
    public function ajax_handle_upload() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }
        if ( empty( $_FILES['file'] ) ) {
            wp_send_json_error( '파일이 전송되지 않았습니다.' );
        }

        $file = $_FILES['file'];
        
        // 업로드 에러 체크
        if ( isset( $file['error'] ) && $file['error'] !== UPLOAD_ERR_OK ) {
            $error_messages = array(
                UPLOAD_ERR_INI_SIZE   => '파일 크기가 서버 설정(upload_max_filesize)을 초과했습니다.',
                UPLOAD_ERR_FORM_SIZE  => '파일 크기가 폼에서 지정한 MAX_FILE_SIZE를 초과했습니다.',
                UPLOAD_ERR_PARTIAL    => '파일이 부분적으로만 업로드되었습니다.',
                UPLOAD_ERR_NO_FILE    => '파일이 업로드되지 않았습니다.',
                UPLOAD_ERR_NO_TMP_DIR => '임시 폴더가 없습니다.',
                UPLOAD_ERR_CANT_WRITE => '디스크에 파일을 쓸 수 없습니다.',
                UPLOAD_ERR_EXTENSION  => 'PHP 확장이 파일 업로드를 중단했습니다.',
            );
            $error_msg = isset( $error_messages[ $file['error'] ] ) ? $error_messages[ $file['error'] ] : '알 수 없는 업로드 오류 (코드: ' . $file['error'] . ')';
            wp_send_json_error( $error_msg );
        }
        
        // ZIP 파일 확인
        $file_ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        if ( 'zip' !== $file_ext ) {
            wp_send_json_error( 'ZIP 파일만 업로드할 수 있습니다.' );
        }

        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/jj-bulk-temp/';
        
        // 임시 디렉토리 생성
        if ( ! file_exists( $temp_dir ) ) {
            $created = wp_mkdir_p( $temp_dir );
            if ( ! $created ) {
                wp_send_json_error( '임시 폴더를 생성할 수 없습니다. 서버 권한을 확인하세요.' );
            }
        }
        
        $target_path = $temp_dir . basename( $file['name'] );
        
        // 파일 이동
        if ( move_uploaded_file( $file['tmp_name'], $target_path ) ) {
            wp_send_json_success( array( 
                'path' => $target_path,
                'name' => $file['name'],
                'type' => $this->detect_type( $target_path )
            ) );
        } else {
            $error_detail = '';
            if ( ! is_writable( $temp_dir ) ) {
                $error_detail = ' (임시 폴더에 쓰기 권한이 없습니다: ' . $temp_dir . ')';
            }
            wp_send_json_error( '파일 업로드 실패' . $error_detail );
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
