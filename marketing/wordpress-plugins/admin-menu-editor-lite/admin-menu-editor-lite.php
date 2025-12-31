<?php
/**
 * Plugin Name: Admin Menu Editor Lite
 * Plugin URI: https://j-j-labs.com
 * Description: 워드프레스 관리자 메뉴 순서를 변경하고, 불필요한 메뉴를 숨기거나 이름을 변경하세요. (Lite Version)
 * Version:           2.0.0
 * Author: 3J Labs
 * Author URI: https://j-j-labs.com
 * License: GPLv2 or later
 * Text Domain: admin-menu-editor-lite
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class JJ_Admin_Menu_Editor_Lite {

    private static $instance = null;
    private $option_key = 'jj_ame_lite_layout';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_menu', array( $this, 'apply_menu_customizations' ), 999 );
        add_filter( 'custom_menu_order', '__return_true' );
        add_filter( 'menu_order', array( $this, 'filter_menu_order' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_ame_save', array( $this, 'ajax_save_settings' ) );
    }

    public function add_settings_page() {
        add_options_page(
            'Admin Menu Editor',
            'Menu Editor (Lite)',
            'manage_options',
            'jj-ame-lite',
            array( $this, 'render_page' )
        );
    }

    public function enqueue_assets( $hook ) {
        if ( 'settings_page_jj-ame-lite' !== $hook ) return;
        
        wp_enqueue_style( 'jj-ame-lite-css', plugin_dir_url( __FILE__ ) . 'assets/style.css' );
        wp_enqueue_script( 'jj-ame-lite-js', plugin_dir_url( __FILE__ ) . 'assets/script.js', array( 'jquery', 'jquery-ui-sortable' ), '1.0.0', true );
        wp_localize_script( 'jj-ame-lite-js', 'jjAME', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'jj_ame_lite' ),
        ) );
    }

    public function render_page() {
        global $menu;
        $layout = get_option( $this->option_key, array() );
        
        // [v2.2.0] ACF CSS 라이센스 감지
        $has_pro = false;
        $full_editor_url = '';
        
        // [v2.2.1] Master Edition 독립 실행 모드 (Core 없이도 무제한)
        if ( defined( 'JJ_ADMIN_MENU_EDITOR_LICENSE' ) && 'MASTER' === JJ_ADMIN_MENU_EDITOR_LICENSE ) {
            $has_pro = true;
            if ( class_exists( 'JJ_Edition_Controller' ) ) {
                $full_editor_url = admin_url( 'options-general.php?page=jj-admin-center#admin-menu' );
            }
        } elseif ( class_exists( 'JJ_Edition_Controller' ) ) {
            $edition = JJ_Edition_Controller::instance();
            if ( $edition->is_at_least( 'basic' ) ) {
                $has_pro = true;
                $full_editor_url = admin_url( 'options-general.php?page=jj-admin-center#admin-menu' );
            }
        }
        ?>
        <div class="wrap jj-ame-wrap">
            <h1>🛠️ Admin Menu Editor <small><?php echo ( defined( 'JJ_ADMIN_MENU_EDITOR_LICENSE' ) && 'MASTER' === JJ_ADMIN_MENU_EDITOR_LICENSE ) ? 'Master' : 'Lite'; ?></small></h1>
            
            <?php if ( $has_pro ) : ?>
            <div class="notice notice-success inline">
                <p>
                    <strong>🎉 <?php echo ( defined( 'JJ_ADMIN_MENU_EDITOR_LICENSE' ) && 'MASTER' === JJ_ADMIN_MENU_EDITOR_LICENSE ) ? 'Master Edition Active!' : 'ACF CSS Pro 라이센스가 감지되었습니다!'; ?></strong><br>
                    서브메뉴 편집, 권한 설정, 아이콘 변경 등 더 강력한 기능은 <strong>관리자 센터</strong>에서 사용할 수 있습니다.
                    <?php if ( $full_editor_url ) : ?>
                    <a href="<?php echo esc_url( $full_editor_url ); ?>" class="button button-primary" style="margin-left: 10px;">전체 에디터 열기</a>
                    <?php else : ?>
                    <span class="description" style="margin-left: 10px;">(ACF CSS Core 플러그인이 필요합니다)</span>
                    <?php endif; ?>
                </p>
            </div>
            <?php else : ?>
            <p>드래그 앤 드롭으로 순서를 변경하고, 눈 아이콘을 눌러 메뉴를 숨기세요.</p>
            <?php endif; ?>
            
            <div class="jj-ame-container">
                <ul id="jj-menu-list">
                    <?php
                    // 정렬 로직 (기존 저장된 순서 반영)
                    $menu_items = array();
                    foreach ( $menu as $index => $item ) {
                        if ( empty( $item[0] ) ) continue;
                        $slug = $item[2];
                        $menu_items[$slug] = array(
                            'title' => strip_tags( $item[0] ),
                            'slug'  => $slug,
                            'original_index' => $index
                        );
                    }
                    
                    // 저장된 설정 병합
                    $display_items = array();
                    foreach ( $menu_items as $slug => $item ) {
                        $meta = isset( $layout[$slug] ) ? $layout[$slug] : array();
                        $item['hidden'] = isset( $meta['hidden'] ) ? $meta['hidden'] : false;
                        $item['label'] = isset( $meta['label'] ) ? $meta['label'] : $item['title'];
                        $item['order'] = isset( $meta['order'] ) ? $meta['order'] : 9999;
                        $display_items[] = $item;
                    }
                    
                    // 정렬
                    usort( $display_items, function($a, $b) {
                        return $a['order'] - $b['order'];
                    });

                    foreach ( $display_items as $item ) : 
                        $hidden_class = $item['hidden'] ? 'hidden-menu' : '';
                        $eye_icon = $item['hidden'] ? 'dashicons-hidden' : 'dashicons-visibility';
                    ?>
                    <li class="jj-menu-item <?php echo $hidden_class; ?>" data-slug="<?php echo esc_attr( $item['slug'] ); ?>">
                        <div class="jj-menu-handle"><span class="dashicons dashicons-menu"></span></div>
                        <div class="jj-menu-content">
                            <input type="text" class="jj-menu-label" value="<?php echo esc_attr( $item['label'] ); ?>">
                            <span class="jj-menu-slug"><?php echo esc_html( $item['slug'] ); ?></span>
                        </div>
                        <div class="jj-menu-actions">
                            <button type="button" class="jj-toggle-visibility" title="숨기기/보이기">
                                <span class="dashicons <?php echo $eye_icon; ?>"></span>
                            </button>
                            
                            <?php if ( $has_pro ) : ?>
                            <a href="<?php echo esc_url( $full_editor_url ); ?>" class="button button-small" title="전체 에디터에서 편집" style="margin-left: 5px;">
                                <span class="dashicons dashicons-external" style="margin-top:3px;"></span>
                            </a>
                            <?php else : ?>
                            <button type="button" class="jj-edit-advanced" title="고급 설정 (Pro)">
                                <span class="dashicons dashicons-lock"></span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="jj-ame-footer">
                <button id="jj-save-menu" class="button button-primary button-large">설정 저장</button>
                
                <?php if ( ! $has_pro ) : ?>
                <div class="jj-pro-promo">
                    <span>⚡ <strong>서브메뉴 편집, 권한 설정, 아이콘 변경</strong>이 필요하신가요?</span>
                    <a href="https://j-j-labs.com/?product=plugin-jj-center-of-style-setting" target="_blank" class="button">ACF CSS Manager Pro 업그레이드</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    public function ajax_save_settings() {
        check_ajax_referer( 'jj_ame_lite', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error();

        $data = isset( $_POST['layout'] ) ? $_POST['layout'] : array();
        // Sanitization
        $clean_data = array();
        foreach ( $data as $slug => $meta ) {
            $clean_data[ sanitize_key($slug) ] = array(
                'order'  => intval( $meta['order'] ),
                'hidden' => $meta['hidden'] === 'true',
                'label'  => sanitize_text_field( $meta['label'] )
            );
        }
        
        update_option( $this->option_key, $clean_data );
        wp_send_json_success( '저장되었습니다.' );
    }

    // 메뉴 순서 변경 적용
    public function filter_menu_order( $menu_order ) {
        $layout = get_option( $this->option_key, array() );
        if ( empty( $layout ) ) return $menu_order;

        $new_order = array();
        $append = array();

        // 설정에 있는 순서대로 배열
        // 1. 순서값으로 정렬
        uasort( $layout, function($a, $b) {
            return $a['order'] - $b['order'];
        });

        foreach ( $layout as $slug => $meta ) {
            // 숨김 처리된 메뉴는 여기서 제거하면 안됨 (remove_menu_page 써야 함)
            // 하지만 menu_order 필터에서는 순서만 관여
            $new_order[] = $slug;
        }

        // 설정에 없는 메뉴들은 뒤에 붙임
        foreach ( $menu_order as $slug ) {
            if ( ! isset( $layout[$slug] ) ) {
                $append[] = $slug;
            }
        }

        return array_merge( $new_order, $append );
    }

    // 메뉴 숨김 및 레이블 변경 적용
    public function apply_menu_customizations() {
        global $menu;
        $layout = get_option( $this->option_key, array() );
        if ( empty( $layout ) ) return;

        foreach ( $menu as $index => $item ) {
            $slug = $item[2];
            if ( isset( $layout[$slug] ) ) {
                // 숨김 처리
                if ( $layout[$slug]['hidden'] ) {
                    remove_menu_page( $slug );
                    continue; // 레이블 변경 불필요
                }
                // 레이블 변경
                if ( ! empty( $layout[$slug]['label'] ) ) {
                    $menu[$index][0] = $layout[$slug]['label'];
                }
            }
        }
    }
}

JJ_Admin_Menu_Editor_Lite::instance();

