<?php
/**
 * JJ Master Menu Editor - 마스터 버전 통합 관리자 메뉴 에디터 모듈
 * 
 * Admin Menu Editor 플러그인의 핵심 기능을 마스터 버전에 통합합니다.
 * 
 * @since 13.4.5
 * @package ACF_CSS
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Master_Menu_Editor {

    private static $instance = null;
    private $option_key = 'jj_master_menu_editor_layout';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    private function init() {
        // Admin Center에서 이미 메뉴 편집 기능을 제공하므로,
        // 여기서는 독립 페이지와 추가 기능만 제공
        
        // 관리자 메뉴
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );
        
        // 메뉴 커스터마이징 적용
        add_action( 'admin_menu', array( $this, 'apply_menu_customizations' ), 999 );
        add_filter( 'custom_menu_order', '__return_true' );
        add_filter( 'menu_order', array( $this, 'filter_menu_order' ) );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_menu_editor_save', array( $this, 'ajax_save_settings' ) );
        add_action( 'wp_ajax_jj_menu_editor_reset', array( $this, 'ajax_reset_settings' ) );
        add_action( 'wp_ajax_jj_menu_editor_export', array( $this, 'ajax_export_settings' ) );
        add_action( 'wp_ajax_jj_menu_editor_import', array( $this, 'ajax_import_settings' ) );
        
        // 스크립트/스타일 로드
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_submenu_page(
            'jj-admin-center',
            __( '관리자 메뉴 에디터', 'acf-css-really-simple-style-management-center' ),
            __( '📋 메뉴 에디터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            'jj-menu-editor',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * 스크립트/스타일 로드
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'jj-menu-editor' ) === false ) {
            return;
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        
        wp_enqueue_script(
            'jj-menu-editor',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-menu-editor.js',
            array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker' ),
            JJ_STYLE_GUIDE_VERSION,
            true
        );

        wp_enqueue_style(
            'jj-menu-editor',
            JJ_STYLE_GUIDE_URL . 'assets/css/jj-menu-editor.css',
            array( 'wp-color-picker' ),
            JJ_STYLE_GUIDE_VERSION
        );

        wp_localize_script( 'jj-menu-editor', 'jjMenuEditor', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'jj_menu_editor_nonce' ),
            'strings' => array(
                'saved' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ),
                'error' => __( '오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
                'reset_confirm' => __( '모든 메뉴 설정을 초기화하시겠습니까?', 'acf-css-really-simple-style-management-center' ),
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
        <div class="wrap jj-menu-editor-wrap">
            <h1><?php esc_html_e( '관리자 메뉴 에디터 (Master)', 'acf-css-really-simple-style-management-center' ); ?></h1>
            
            <div class="notice notice-info">
                <p>
                    <?php esc_html_e( '마스터 버전에서는 모든 메뉴 편집 기능을 무제한으로 사용할 수 있습니다. 드래그하여 순서를 변경하고, 메뉴 이름, 아이콘, 권한을 수정하세요.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
            </div>

            <div class="jj-menu-editor-container">
                <div class="jj-menu-editor-toolbar">
                    <button type="button" id="jj-save-menu" class="button button-primary">
                        <span class="dashicons dashicons-saved" style="margin-top: 4px;"></span>
                        <?php esc_html_e( '저장', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                    <button type="button" id="jj-reset-menu" class="button">
                        <span class="dashicons dashicons-undo" style="margin-top: 4px;"></span>
                        <?php esc_html_e( '초기화', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                    <button type="button" id="jj-export-menu" class="button">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                        <?php esc_html_e( '내보내기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                    <button type="button" id="jj-import-menu" class="button">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span>
                        <?php esc_html_e( '가져오기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                </div>

                <div class="jj-menu-editor-grid">
                    <!-- 메인 메뉴 목록 -->
                    <div class="jj-menu-list-panel">
                        <h3><?php esc_html_e( '메인 메뉴', 'acf-css-really-simple-style-management-center' ); ?></h3>
                        <ul id="jj-menu-list" class="jj-sortable-list">
                            <?php
                            $menu_items = array();
                            foreach ( $menu as $index => $item ) {
                                if ( empty( $item[0] ) ) continue;
                                $slug = $item[2];
                                $menu_items[ $slug ] = array(
                                    'title' => wp_strip_all_tags( $item[0] ),
                                    'slug'  => $slug,
                                    'icon'  => isset( $item[6] ) ? $item[6] : 'dashicons-admin-generic',
                                    'capability' => isset( $item[1] ) ? $item[1] : 'manage_options',
                                );
                            }

                            // 저장된 설정 병합
                            $display_items = array();
                            foreach ( $menu_items as $slug => $item ) {
                                $meta = isset( $layout[ $slug ] ) ? $layout[ $slug ] : array();
                                $item['hidden'] = isset( $meta['hidden'] ) ? $meta['hidden'] : false;
                                $item['label'] = isset( $meta['label'] ) ? $meta['label'] : $item['title'];
                                $item['order'] = isset( $meta['order'] ) ? $meta['order'] : 9999;
                                $item['custom_icon'] = isset( $meta['icon'] ) ? $meta['icon'] : '';
                                $item['custom_capability'] = isset( $meta['capability'] ) ? $meta['capability'] : '';
                                $display_items[] = $item;
                            }

                            // 정렬
                            usort( $display_items, function( $a, $b ) {
                                return $a['order'] - $b['order'];
                            } );

                            foreach ( $display_items as $item ) :
                                $hidden_class = $item['hidden'] ? 'hidden-menu' : '';
                                $eye_icon = $item['hidden'] ? 'dashicons-hidden' : 'dashicons-visibility';
                            ?>
                            <li class="jj-menu-item <?php echo esc_attr( $hidden_class ); ?>" data-slug="<?php echo esc_attr( $item['slug'] ); ?>">
                                <div class="jj-menu-handle"><span class="dashicons dashicons-menu"></span></div>
                                <div class="jj-menu-icon">
                                    <span class="dashicons <?php echo esc_attr( $item['custom_icon'] ?: $item['icon'] ); ?>"></span>
                                </div>
                                <div class="jj-menu-content">
                                    <input type="text" class="jj-menu-label" value="<?php echo esc_attr( $item['label'] ); ?>" placeholder="<?php echo esc_attr( $item['title'] ); ?>">
                                    <span class="jj-menu-slug"><?php echo esc_html( $item['slug'] ); ?></span>
                                </div>
                                <div class="jj-menu-actions">
                                    <button type="button" class="jj-toggle-visibility" title="<?php esc_attr_e( '숨기기/보이기', 'acf-css-really-simple-style-management-center' ); ?>">
                                        <span class="dashicons <?php echo esc_attr( $eye_icon ); ?>"></span>
                                    </button>
                                    <button type="button" class="jj-edit-advanced" title="<?php esc_attr_e( '고급 설정', 'acf-css-really-simple-style-management-center' ); ?>">
                                        <span class="dashicons dashicons-admin-generic"></span>
                                    </button>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- 서브메뉴 목록 -->
                    <div class="jj-submenu-panel">
                        <h3><?php esc_html_e( '서브메뉴', 'acf-css-really-simple-style-management-center' ); ?></h3>
                        <div id="jj-submenu-container">
                            <p class="jj-placeholder"><?php esc_html_e( '메인 메뉴를 선택하면 서브메뉴가 표시됩니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </div>
                    </div>

                    <!-- 고급 설정 패널 -->
                    <div class="jj-advanced-panel">
                        <h3><?php esc_html_e( '고급 설정', 'acf-css-really-simple-style-management-center' ); ?></h3>
                        <div id="jj-advanced-container">
                            <p class="jj-placeholder"><?php esc_html_e( '메뉴 항목의 고급 설정 버튼을 클릭하세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .jj-menu-editor-wrap { max-width: 1400px; }
                .jj-menu-editor-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .jj-menu-editor-toolbar { margin-bottom: 20px; display: flex; gap: 10px; }
                .jj-menu-editor-grid { display: grid; grid-template-columns: 1fr 1fr 300px; gap: 20px; }
                .jj-menu-list-panel, .jj-submenu-panel, .jj-advanced-panel { background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #ddd; }
                .jj-menu-list-panel h3, .jj-submenu-panel h3, .jj-advanced-panel h3 { margin: 0 0 15px; font-size: 14px; }
                .jj-sortable-list { list-style: none; margin: 0; padding: 0; }
                .jj-menu-item { display: flex; align-items: center; gap: 10px; padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 5px; cursor: move; }
                .jj-menu-item.hidden-menu { opacity: 0.5; }
                .jj-menu-item:hover { border-color: #2271b1; }
                .jj-menu-handle { cursor: move; color: #999; }
                .jj-menu-icon { width: 24px; }
                .jj-menu-content { flex: 1; }
                .jj-menu-label { width: 100%; border: 1px solid transparent; padding: 4px; background: transparent; }
                .jj-menu-label:hover, .jj-menu-label:focus { border-color: #ddd; background: #fff; }
                .jj-menu-slug { font-size: 11px; color: #999; display: block; }
                .jj-menu-actions { display: flex; gap: 5px; }
                .jj-menu-actions button { background: none; border: none; cursor: pointer; padding: 4px; color: #666; }
                .jj-menu-actions button:hover { color: #2271b1; }
                .jj-placeholder { color: #999; font-style: italic; }
                @media (max-width: 1200px) {
                    .jj-menu-editor-grid { grid-template-columns: 1fr 1fr; }
                    .jj-advanced-panel { grid-column: span 2; }
                }
                @media (max-width: 768px) {
                    .jj-menu-editor-grid { grid-template-columns: 1fr; }
                    .jj-advanced-panel { grid-column: span 1; }
                }
            </style>

            <script>
            jQuery(document).ready(function($) {
                // 드래그 앤 드롭
                $('#jj-menu-list').sortable({
                    handle: '.jj-menu-handle',
                    placeholder: 'jj-menu-placeholder',
                    update: function() {
                        // 순서 변경됨
                    }
                });

                // 숨기기/보이기 토글
                $(document).on('click', '.jj-toggle-visibility', function() {
                    var $item = $(this).closest('.jj-menu-item');
                    $item.toggleClass('hidden-menu');
                    var $icon = $(this).find('.dashicons');
                    if ($item.hasClass('hidden-menu')) {
                        $icon.removeClass('dashicons-visibility').addClass('dashicons-hidden');
                    } else {
                        $icon.removeClass('dashicons-hidden').addClass('dashicons-visibility');
                    }
                });

                // 저장
                $('#jj-save-menu').on('click', function() {
                    var layout = {};
                    $('#jj-menu-list .jj-menu-item').each(function(index) {
                        var slug = $(this).data('slug');
                        layout[slug] = {
                            order: index,
                            hidden: $(this).hasClass('hidden-menu'),
                            label: $(this).find('.jj-menu-label').val()
                        };
                    });

                    $.post(jjMenuEditor.ajaxurl, {
                        action: 'jj_menu_editor_save',
                        nonce: jjMenuEditor.nonce,
                        layout: layout
                    }, function(response) {
                        if (response.success) {
                            alert(jjMenuEditor.strings.saved);
                            location.reload();
                        } else {
                            alert(jjMenuEditor.strings.error);
                        }
                    });
                });

                // 초기화
                $('#jj-reset-menu').on('click', function() {
                    if (confirm(jjMenuEditor.strings.reset_confirm)) {
                        $.post(jjMenuEditor.ajaxurl, {
                            action: 'jj_menu_editor_reset',
                            nonce: jjMenuEditor.nonce
                        }, function(response) {
                            if (response.success) {
                                location.reload();
                            }
                        });
                    }
                });
            });
            </script>
        </div>
        <?php
    }

    /**
     * AJAX: 설정 저장
     */
    public function ajax_save_settings() {
        check_ajax_referer( 'jj_menu_editor_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $data = isset( $_POST['layout'] ) ? $_POST['layout'] : array();
        $clean_data = array();

        foreach ( $data as $slug => $meta ) {
            $clean_data[ sanitize_key( $slug ) ] = array(
                'order' => intval( $meta['order'] ),
                'hidden' => $meta['hidden'] === 'true' || $meta['hidden'] === true,
                'label' => sanitize_text_field( $meta['label'] ),
                'icon' => isset( $meta['icon'] ) ? sanitize_text_field( $meta['icon'] ) : '',
                'capability' => isset( $meta['capability'] ) ? sanitize_text_field( $meta['capability'] ) : '',
            );
        }

        update_option( $this->option_key, $clean_data );
        wp_send_json_success( array( 'message' => __( '저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * AJAX: 설정 초기화
     */
    public function ajax_reset_settings() {
        check_ajax_referer( 'jj_menu_editor_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }

        delete_option( $this->option_key );
        wp_send_json_success();
    }

    /**
     * AJAX: 설정 내보내기
     */
    public function ajax_export_settings() {
        check_ajax_referer( 'jj_menu_editor_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }

        $layout = get_option( $this->option_key, array() );
        wp_send_json_success( array( 'data' => $layout ) );
    }

    /**
     * AJAX: 설정 가져오기
     */
    public function ajax_import_settings() {
        check_ajax_referer( 'jj_menu_editor_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }

        $data = isset( $_POST['data'] ) ? json_decode( stripslashes( $_POST['data'] ), true ) : array();
        
        if ( ! is_array( $data ) ) {
            wp_send_json_error( array( 'message' => __( '잘못된 데이터 형식입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        update_option( $this->option_key, $data );
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
