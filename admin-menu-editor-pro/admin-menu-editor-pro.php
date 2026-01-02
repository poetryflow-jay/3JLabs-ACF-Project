<?php
/**
 * Plugin Name:       Admin Menu Editor Pro - Advanced Admin Customizer
 * Plugin URI:        https://3j-labs.com
 * Description:       Admin Menu Editor Pro - 워드프레스 관리자 메뉴를 완전히 커스터마이징하세요. 메뉴 순서 변경, 숨기기, 이름 변경, 아이콘 변경, 권한 설정, 서브메뉴 편집까지 모든 기능을 제공합니다. ACF CSS (Advanced Custom Fonts & Colors & Styles) 패밀리 플러그인으로, Pro 버전 이상 사용자에게 고급 기능을 제공합니다.
 * Version:           1.0.0
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com
 * Text Domain:       admin-menu-editor-pro
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * ============================================================================
 * ACF CSS 패밀리 플러그인
 * ============================================================================
 * 
 * Admin Menu Editor Pro는 ACF CSS (Advanced Custom Fonts & Colors & Styles) 
 * 패밀리의 일원입니다. 다음 플러그인들과 함께 사용할 수 있습니다:
 * 
 * - ACF CSS Manager: 스타일 중앙 관리
 * - ACF Code Snippets Box: 코드 스니펫 관리
 * - ACF CSS WooCommerce Toolkit: 우커머스 스타일링
 * - ACF CSS AI Extension: AI 스타일 추천
 * - ACF CSS Neural Link: 라이센스 & 업데이트
 * - ACF MBA: 마케팅 자동화
 * - WP Bulk Manager: 대량 설치 관리
 * 
 * ============================================================================
 * 
 * @package Admin_Menu_Editor_Pro
 */

// 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 플러그인 상수 정의
 */
define( 'AME_PRO_VERSION', '1.0.0' );
define( 'AME_PRO_PATH', plugin_dir_path( __FILE__ ) );
define( 'AME_PRO_URL', plugin_dir_url( __FILE__ ) );
define( 'AME_PRO_BASENAME', plugin_basename( __FILE__ ) );
define( 'AME_PRO_SLUG', 'admin-menu-editor-pro' );

/**
 * 메인 플러그인 클래스
 */
final class Admin_Menu_Editor_Pro {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 옵션 키
     */
    private $option_key = 'ame_pro_layout';

    /**
     * 라이센스 상태
     */
    private $is_licensed = false;

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->check_license();
        $this->init_hooks();
    }

    /**
     * 라이센스 체크
     */
    private function check_license() {
        // ACF CSS Pro 버전 연동 체크
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            $edition = JJ_Edition_Controller::instance();
            $this->is_licensed = $edition->is_at_least( 'premium' );
        }
        
        // 독립 라이센스 체크
        if ( ! $this->is_licensed ) {
            $license_key = get_option( 'ame_pro_license_key', '' );
            if ( ! empty( $license_key ) ) {
                // 라이센스 검증 로직 (향후 Neural Link 연동)
                $this->is_licensed = $this->validate_license( $license_key );
            }
        }
        
        // 마스터 버전 체크
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) === 'MASTER' ) {
            $this->is_licensed = true;
        }
    }

    /**
     * 라이센스 검증
     */
    private function validate_license( $key ) {
        // 간단한 라이센스 검증 (향후 서버 검증으로 교체)
        return strlen( $key ) > 20;
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 관리자 메뉴
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        
        // 메뉴 커스터마이징 적용
        add_action( 'admin_menu', array( $this, 'apply_menu_customizations' ), 999 );
        add_filter( 'custom_menu_order', '__return_true' );
        add_filter( 'menu_order', array( $this, 'filter_menu_order' ) );
        
        // 스크립트/스타일
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_ame_pro_save', array( $this, 'ajax_save_settings' ) );
        add_action( 'wp_ajax_ame_pro_reset', array( $this, 'ajax_reset_settings' ) );
        
        // 플러그인 목록 페이지 링크
        add_filter( 'plugin_action_links_' . AME_PRO_BASENAME, array( $this, 'add_plugin_links' ) );
    }

    /**
     * 플러그인 링크 추가
     */
    public function add_plugin_links( $links ) {
        $settings_link = '<a href="' . admin_url( 'options-general.php?page=ame-pro' ) . '">' . __( '설정', 'admin-menu-editor-pro' ) . '</a>';
        array_unshift( $links, $settings_link );
        
        if ( ! $this->is_licensed ) {
            $upgrade_link = '<a href="https://3j-labs.com" target="_blank" style="color: #00a32a; font-weight: 600;">' . __( '🔓 Pro 업그레이드', 'admin-menu-editor-pro' ) . '</a>';
            $links[] = $upgrade_link;
        }
        
        return $links;
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_options_page(
            __( 'Admin Menu Editor Pro', 'admin-menu-editor-pro' ),
            __( 'Menu Editor Pro', 'admin-menu-editor-pro' ),
            'manage_options',
            'ame-pro',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * 스크립트/스타일 로드
     */
    public function enqueue_assets( $hook ) {
        if ( 'settings_page_ame-pro' !== $hook ) {
            return;
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        
        wp_enqueue_style(
            'ame-pro-css',
            AME_PRO_URL . 'assets/style.css',
            array(),
            AME_PRO_VERSION
        );

        wp_enqueue_script(
            'ame-pro-js',
            AME_PRO_URL . 'assets/script.js',
            array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker' ),
            AME_PRO_VERSION,
            true
        );

        wp_localize_script( 'ame-pro-js', 'amePro', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'ame_pro_nonce' ),
            'is_licensed' => $this->is_licensed,
            'strings' => array(
                'saved' => __( '설정이 저장되었습니다.', 'admin-menu-editor-pro' ),
                'error' => __( '오류가 발생했습니다.', 'admin-menu-editor-pro' ),
                'pro_required' => __( '이 기능은 Pro 버전에서만 사용할 수 있습니다.', 'admin-menu-editor-pro' ),
            ),
        ) );
    }

    /**
     * 관리자 페이지 렌더링
     */
    public function render_admin_page() {
        global $menu, $submenu;
        $layout = get_option( $this->option_key, array() );
        ?>
        <div class="wrap ame-pro-wrap">
            <h1>
                <span class="dashicons dashicons-menu-alt" style="margin-right: 10px;"></span>
                Admin Menu Editor Pro
                <?php if ( $this->is_licensed ) : ?>
                <span class="ame-pro-badge" style="background: #00a32a; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 12px; margin-left: 10px;">PRO</span>
                <?php else : ?>
                <span class="ame-pro-badge" style="background: #d63638; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 12px; margin-left: 10px;">FREE</span>
                <?php endif; ?>
            </h1>
            
            <?php if ( ! $this->is_licensed ) : ?>
            <div class="notice notice-warning">
                <p>
                    <strong>⚡ Pro 버전으로 업그레이드하세요!</strong><br>
                    Free 버전에서는 메뉴 순서 변경과 숨기기만 가능합니다.<br>
                    <strong>Pro 버전 기능:</strong> 서브메뉴 편집, 아이콘 변경, 권한 설정, 사용자별 메뉴, 역할별 메뉴, 내보내기/가져오기
                </p>
                <p>
                    <a href="https://3j-labs.com" target="_blank" class="button button-primary">🔓 Pro 버전 구매하기</a>
                    <span style="margin-left: 10px; color: #666;">또는 ACF CSS Premium 이상 버전을 사용하면 자동으로 활성화됩니다.</span>
                </p>
            </div>
            <?php else : ?>
            <div class="notice notice-success">
                <p><strong>✅ Pro 버전이 활성화되어 있습니다.</strong> 모든 고급 기능을 사용할 수 있습니다.</p>
            </div>
            <?php endif; ?>

            <div class="ame-pro-container">
                <div class="ame-pro-toolbar">
                    <button type="button" id="ame-save" class="button button-primary">
                        <span class="dashicons dashicons-saved" style="margin-top: 4px;"></span>
                        <?php esc_html_e( '저장', 'admin-menu-editor-pro' ); ?>
                    </button>
                    <button type="button" id="ame-reset" class="button">
                        <span class="dashicons dashicons-undo" style="margin-top: 4px;"></span>
                        <?php esc_html_e( '초기화', 'admin-menu-editor-pro' ); ?>
                    </button>
                    
                    <?php if ( $this->is_licensed ) : ?>
                    <button type="button" id="ame-export" class="button">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                        <?php esc_html_e( '내보내기', 'admin-menu-editor-pro' ); ?>
                    </button>
                    <button type="button" id="ame-import" class="button">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span>
                        <?php esc_html_e( '가져오기', 'admin-menu-editor-pro' ); ?>
                    </button>
                    <?php else : ?>
                    <button type="button" class="button" disabled title="<?php esc_attr_e( 'Pro 버전에서만 사용 가능', 'admin-menu-editor-pro' ); ?>">
                        <span class="dashicons dashicons-lock" style="margin-top: 4px;"></span>
                        <?php esc_html_e( '내보내기 (Pro)', 'admin-menu-editor-pro' ); ?>
                    </button>
                    <button type="button" class="button" disabled title="<?php esc_attr_e( 'Pro 버전에서만 사용 가능', 'admin-menu-editor-pro' ); ?>">
                        <span class="dashicons dashicons-lock" style="margin-top: 4px;"></span>
                        <?php esc_html_e( '가져오기 (Pro)', 'admin-menu-editor-pro' ); ?>
                    </button>
                    <?php endif; ?>
                </div>

                <div class="ame-pro-grid">
                    <!-- 메인 메뉴 목록 -->
                    <div class="ame-menu-panel">
                        <h3><?php esc_html_e( '메인 메뉴', 'admin-menu-editor-pro' ); ?></h3>
                        <p class="description"><?php esc_html_e( '드래그하여 순서를 변경하고, 눈 아이콘으로 숨기세요.', 'admin-menu-editor-pro' ); ?></p>
                        
                        <ul id="ame-menu-list" class="ame-sortable">
                            <?php
                            $menu_items = array();
                            foreach ( $menu as $item ) {
                                if ( empty( $item[0] ) ) continue;
                                $slug = $item[2];
                                $menu_items[ $slug ] = array(
                                    'title' => wp_strip_all_tags( $item[0] ),
                                    'slug'  => $slug,
                                    'icon'  => isset( $item[6] ) ? $item[6] : 'dashicons-admin-generic',
                                );
                            }

                            $display_items = array();
                            foreach ( $menu_items as $slug => $item ) {
                                $meta = isset( $layout[ $slug ] ) ? $layout[ $slug ] : array();
                                $item['hidden'] = isset( $meta['hidden'] ) ? $meta['hidden'] : false;
                                $item['label'] = isset( $meta['label'] ) ? $meta['label'] : $item['title'];
                                $item['order'] = isset( $meta['order'] ) ? $meta['order'] : 9999;
                                $display_items[] = $item;
                            }

                            usort( $display_items, function( $a, $b ) {
                                return $a['order'] - $b['order'];
                            } );

                            foreach ( $display_items as $item ) :
                                $hidden_class = $item['hidden'] ? 'ame-hidden' : '';
                                $eye_icon = $item['hidden'] ? 'dashicons-hidden' : 'dashicons-visibility';
                            ?>
                            <li class="ame-menu-item <?php echo esc_attr( $hidden_class ); ?>" data-slug="<?php echo esc_attr( $item['slug'] ); ?>">
                                <span class="ame-handle dashicons dashicons-menu"></span>
                                <span class="ame-icon dashicons <?php echo esc_attr( $item['icon'] ); ?>"></span>
                                <input type="text" class="ame-label" value="<?php echo esc_attr( $item['label'] ); ?>">
                                <span class="ame-slug"><?php echo esc_html( $item['slug'] ); ?></span>
                                <button type="button" class="ame-toggle-visibility" title="<?php esc_attr_e( '숨기기/보이기', 'admin-menu-editor-pro' ); ?>">
                                    <span class="dashicons <?php echo esc_attr( $eye_icon ); ?>"></span>
                                </button>
                                <?php if ( $this->is_licensed ) : ?>
                                <button type="button" class="ame-edit-icon" title="<?php esc_attr_e( '아이콘 변경', 'admin-menu-editor-pro' ); ?>">
                                    <span class="dashicons dashicons-admin-appearance"></span>
                                </button>
                                <?php else : ?>
                                <button type="button" class="ame-locked" title="<?php esc_attr_e( 'Pro 버전 기능', 'admin-menu-editor-pro' ); ?>">
                                    <span class="dashicons dashicons-lock"></span>
                                </button>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- 서브메뉴 패널 (Pro Only) -->
                    <div class="ame-submenu-panel">
                        <h3><?php esc_html_e( '서브메뉴', 'admin-menu-editor-pro' ); ?></h3>
                        <?php if ( $this->is_licensed ) : ?>
                        <p class="description"><?php esc_html_e( '메인 메뉴를 선택하면 서브메뉴가 표시됩니다.', 'admin-menu-editor-pro' ); ?></p>
                        <div id="ame-submenu-container">
                            <p class="ame-placeholder"><?php esc_html_e( '메뉴 항목을 선택하세요.', 'admin-menu-editor-pro' ); ?></p>
                        </div>
                        <?php else : ?>
                        <div class="ame-pro-overlay">
                            <div class="ame-pro-message">
                                <span class="dashicons dashicons-lock" style="font-size: 48px; width: 48px; height: 48px; color: #d63638;"></span>
                                <h4><?php esc_html_e( '서브메뉴 편집은 Pro 버전에서만 가능합니다', 'admin-menu-editor-pro' ); ?></h4>
                                <p><?php esc_html_e( 'Pro 버전으로 업그레이드하여 서브메뉴 순서 변경, 숨기기, 이름 변경 기능을 사용하세요.', 'admin-menu-editor-pro' ); ?></p>
                                <a href="https://3j-labs.com" target="_blank" class="button button-primary"><?php esc_html_e( 'Pro 버전 구매', 'admin-menu-editor-pro' ); ?></a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .ame-pro-wrap { max-width: 1200px; }
            .ame-pro-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 20px; }
            .ame-pro-toolbar { margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
            .ame-pro-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
            .ame-menu-panel, .ame-submenu-panel { background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #ddd; }
            .ame-menu-panel h3, .ame-submenu-panel h3 { margin: 0 0 10px; font-size: 14px; }
            .ame-sortable { list-style: none; margin: 0; padding: 0; }
            .ame-menu-item { display: flex; align-items: center; gap: 8px; padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 5px; cursor: move; }
            .ame-menu-item.ame-hidden { opacity: 0.5; background: #f0f0f0; }
            .ame-menu-item:hover { border-color: #2271b1; }
            .ame-handle { cursor: move; color: #999; }
            .ame-icon { color: #646970; }
            .ame-label { flex: 1; border: 1px solid transparent; padding: 4px 8px; background: transparent; min-width: 0; }
            .ame-label:hover, .ame-label:focus { border-color: #ddd; background: #fff; }
            .ame-slug { font-size: 10px; color: #999; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .ame-menu-item button { background: none; border: none; cursor: pointer; padding: 4px; color: #666; }
            .ame-menu-item button:hover { color: #2271b1; }
            .ame-locked { color: #999 !important; cursor: not-allowed !important; }
            .ame-pro-overlay { position: relative; min-height: 200px; display: flex; align-items: center; justify-content: center; }
            .ame-pro-message { text-align: center; padding: 30px; }
            .ame-pro-message h4 { margin: 15px 0 10px; }
            .ame-pro-message p { color: #666; margin-bottom: 15px; }
            .ame-placeholder { color: #999; font-style: italic; padding: 20px; text-align: center; }
            @media (max-width: 768px) {
                .ame-pro-grid { grid-template-columns: 1fr; }
            }
        </style>

        <script>
        jQuery(document).ready(function($) {
            $('#ame-menu-list').sortable({
                handle: '.ame-handle',
                placeholder: 'ame-placeholder-item',
                update: function() {}
            });

            $(document).on('click', '.ame-toggle-visibility', function() {
                var $item = $(this).closest('.ame-menu-item');
                $item.toggleClass('ame-hidden');
                var $icon = $(this).find('.dashicons');
                $icon.toggleClass('dashicons-visibility dashicons-hidden');
            });

            $('#ame-save').on('click', function() {
                var layout = {};
                $('#ame-menu-list .ame-menu-item').each(function(index) {
                    var slug = $(this).data('slug');
                    layout[slug] = {
                        order: index,
                        hidden: $(this).hasClass('ame-hidden'),
                        label: $(this).find('.ame-label').val()
                    };
                });

                $.post(amePro.ajax_url, {
                    action: 'ame_pro_save',
                    nonce: amePro.nonce,
                    layout: layout
                }, function(response) {
                    if (response.success) {
                        alert(amePro.strings.saved);
                        location.reload();
                    } else {
                        alert(amePro.strings.error);
                    }
                });
            });

            $('#ame-reset').on('click', function() {
                if (confirm('모든 메뉴 설정을 초기화하시겠습니까?')) {
                    $.post(amePro.ajax_url, {
                        action: 'ame_pro_reset',
                        nonce: amePro.nonce
                    }, function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    });
                }
            });

            $('.ame-locked').on('click', function() {
                alert(amePro.strings.pro_required);
            });
        });
        </script>
        <?php
    }

    /**
     * AJAX: 설정 저장
     */
    public function ajax_save_settings() {
        check_ajax_referer( 'ame_pro_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }

        $data = isset( $_POST['layout'] ) ? $_POST['layout'] : array();
        $clean_data = array();

        foreach ( $data as $slug => $meta ) {
            $clean_data[ sanitize_key( $slug ) ] = array(
                'order' => intval( $meta['order'] ),
                'hidden' => $meta['hidden'] === 'true' || $meta['hidden'] === true,
                'label' => sanitize_text_field( $meta['label'] ),
            );
        }

        update_option( $this->option_key, $clean_data );
        wp_send_json_success();
    }

    /**
     * AJAX: 설정 초기화
     */
    public function ajax_reset_settings() {
        check_ajax_referer( 'ame_pro_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }

        delete_option( $this->option_key );
        wp_send_json_success();
    }

    /**
     * 메뉴 순서 변경 적용
     */
    public function filter_menu_order( $menu_order ) {
        $layout = get_option( $this->option_key, array() );
        if ( empty( $layout ) ) return $menu_order;

        uasort( $layout, function( $a, $b ) {
            return $a['order'] - $b['order'];
        } );

        $new_order = array();
        foreach ( $layout as $slug => $meta ) {
            $new_order[] = $slug;
        }

        foreach ( $menu_order as $slug ) {
            if ( ! isset( $layout[ $slug ] ) ) {
                $new_order[] = $slug;
            }
        }

        return $new_order;
    }

    /**
     * 메뉴 숨김 및 레이블 변경 적용
     */
    public function apply_menu_customizations() {
        global $menu;
        $layout = get_option( $this->option_key, array() );
        if ( empty( $layout ) ) return;

        foreach ( $menu as $index => $item ) {
            $slug = $item[2];
            if ( isset( $layout[ $slug ] ) ) {
                if ( $layout[ $slug ]['hidden'] ) {
                    remove_menu_page( $slug );
                    continue;
                }
                if ( ! empty( $layout[ $slug ]['label'] ) ) {
                    $menu[ $index ][0] = $layout[ $slug ]['label'];
                }
            }
        }
    }
}

// 플러그인 인스턴스 초기화
Admin_Menu_Editor_Pro::instance();
