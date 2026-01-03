<?php
/**
 * JJ Master Bulk Manager - 마스터 버전 통합 대량 관리 모듈
 * 
 * WP Bulk Manager의 핵심 기능을 마스터 버전에 통합합니다.
 * 
 * @since 13.4.4
 * @package ACF_CSS
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Master_Bulk_Manager {

    private static $instance = null;

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
        // 관리자 메뉴
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_bulk_upload', array( $this, 'ajax_bulk_upload' ) );
        add_action( 'wp_ajax_jj_bulk_install', array( $this, 'ajax_bulk_install' ) );
        add_action( 'wp_ajax_jj_bulk_activate', array( $this, 'ajax_bulk_activate' ) );
        add_action( 'wp_ajax_jj_bulk_deactivate', array( $this, 'ajax_bulk_deactivate' ) );
        
        // 스크립트 로드
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_submenu_page(
            'jj-admin-center',
            __( '대량 설치 관리', 'acf-css-really-simple-style-management-center' ),
            __( '📦 대량 관리', 'acf-css-really-simple-style-management-center' ),
            'install_plugins',
            'jj-bulk-manager',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * 관리자 스크립트 로드
     */
    public function enqueue_admin_scripts( $hook ) {
        if ( strpos( $hook, 'jj-bulk-manager' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'jj-bulk-manager',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-bulk-manager.js',
            array( 'jquery' ),
            JJ_STYLE_GUIDE_VERSION,
            true
        );

        wp_localize_script( 'jj-bulk-manager', 'jjBulkManager', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'jj_bulk_nonce' ),
            'strings' => array(
                'uploading' => __( '업로드 중...', 'acf-css-really-simple-style-management-center' ),
                'installing' => __( '설치 중...', 'acf-css-really-simple-style-management-center' ),
                'activating' => __( '활성화 중...', 'acf-css-really-simple-style-management-center' ),
                'complete' => __( '완료', 'acf-css-really-simple-style-management-center' ),
                'error' => __( '오류', 'acf-css-really-simple-style-management-center' ),
            ),
        ) );

        wp_enqueue_style(
            'jj-bulk-manager',
            JJ_STYLE_GUIDE_URL . 'assets/css/jj-bulk-manager.css',
            array(),
            JJ_STYLE_GUIDE_VERSION
        );
    }

    /**
     * 관리자 페이지 렌더링
     */
    public function render_admin_page() {
        ?>
        <div class="wrap jj-bulk-manager-wrap">
            <h1><?php esc_html_e( 'WP Bulk Manager - 대량 설치 및 관리', 'acf-css-really-simple-style-management-center' ); ?></h1>
            
            <div class="notice notice-info">
                <p><?php esc_html_e( '여러 플러그인/테마 ZIP 파일을 한 번에 업로드하고 설치합니다. 마스터 버전은 무제한 기능을 제공합니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>

            <div class="jj-bulk-upload-area" style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 20px; text-align: center;">
                <h2><?php esc_html_e( '📁 ZIP 파일 업로드', 'acf-css-really-simple-style-management-center' ); ?></h2>
                
                <div class="jj-dropzone" id="jj-bulk-dropzone" style="border: 2px dashed #ccc; padding: 40px; border-radius: 8px; cursor: pointer; transition: all 0.3s;">
                    <p style="font-size: 16px; color: #666;">
                        <?php esc_html_e( '여기에 ZIP 파일을 드래그하거나 클릭하여 선택하세요', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                    <input type="file" id="jj-bulk-files" multiple accept=".zip" style="display: none;">
                </div>

                <div class="jj-bulk-options" style="margin-top: 20px;">
                    <label>
                        <input type="checkbox" id="jj-auto-activate" checked>
                        <?php esc_html_e( '설치 후 자동 활성화', 'acf-css-really-simple-style-management-center' ); ?>
                    </label>
                </div>

                <button type="button" id="jj-start-install" class="button button-primary button-hero" style="margin-top: 20px; display: none;">
                    <?php esc_html_e( '🚀 설치 시작', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
            </div>

            <div class="jj-bulk-queue" id="jj-bulk-queue" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 20px; display: none;">
                <h3><?php esc_html_e( '📋 설치 대기열', 'acf-css-really-simple-style-management-center' ); ?></h3>
                <div class="jj-progress-bar" style="height: 20px; background: #e0e0e0; border-radius: 10px; overflow: hidden; margin: 10px 0;">
                    <div class="jj-progress-fill" style="height: 100%; background: var(--jj-primary-color, #2196f3); width: 0%; transition: width 0.3s;"></div>
                </div>
                <div class="jj-status-text" style="text-align: center; color: #666;"></div>
                <ul class="jj-queue-list" style="margin-top: 15px; list-style: none; padding: 0;"></ul>
            </div>

            <style>
                .jj-dropzone.dragover { border-color: var(--jj-primary-color, #2196f3); background: #f0f8ff; }
                .jj-queue-list li { padding: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
                .jj-queue-list li.success { background: #d4edda; }
                .jj-queue-list li.error { background: #f8d7da; }
                .jj-queue-list li .status { font-size: 12px; color: #666; }
            </style>

            <script>
            jQuery(document).ready(function($) {
                var dropzone = $('#jj-bulk-dropzone');
                var fileInput = $('#jj-bulk-files');
                var queue = [];

                // 드래그 앤 드롭
                dropzone.on('dragover', function(e) {
                    e.preventDefault();
                    $(this).addClass('dragover');
                }).on('dragleave', function() {
                    $(this).removeClass('dragover');
                }).on('drop', function(e) {
                    e.preventDefault();
                    $(this).removeClass('dragover');
                    handleFiles(e.originalEvent.dataTransfer.files);
                }).on('click', function() {
                    fileInput.click();
                });

                fileInput.on('change', function() {
                    handleFiles(this.files);
                });

                function handleFiles(files) {
                    queue = [];
                    var $list = $('.jj-queue-list').empty();
                    
                    for (var i = 0; i < files.length; i++) {
                        if (files[i].name.endsWith('.zip')) {
                            queue.push(files[i]);
                            $list.append('<li data-index="' + i + '"><span class="name">' + files[i].name + '</span><span class="status">대기 중</span></li>');
                        }
                    }

                    if (queue.length > 0) {
                        $('#jj-bulk-queue').show();
                        $('#jj-start-install').show();
                    }
                }

                $('#jj-start-install').on('click', function() {
                    $(this).prop('disabled', true);
                    processQueue(0);
                });

                function processQueue(index) {
                    if (index >= queue.length) {
                        $('#jj-start-install').prop('disabled', false).hide();
                        updateProgress(queue.length, queue.length, '모든 파일 처리 완료');
                        return;
                    }

                    var file = queue[index];
                    var $item = $('.jj-queue-list li[data-index="' + index + '"]');
                    var autoActivate = $('#jj-auto-activate').is(':checked');

                    $item.find('.status').text('업로드 중...');
                    updateProgress(index, queue.length, '업로드 중: ' + file.name);

                    var formData = new FormData();
                    formData.append('action', 'jj_bulk_upload');
                    formData.append('nonce', jjBulkManager.nonce);
                    formData.append('file', file);
                    formData.append('auto_activate', autoActivate ? '1' : '0');

                    $.ajax({
                        url: jjBulkManager.ajaxurl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $item.addClass('success').find('.status').text(response.data.message);
                            } else {
                                $item.addClass('error').find('.status').text(response.data.message);
                            }
                            processQueue(index + 1);
                        },
                        error: function() {
                            $item.addClass('error').find('.status').text('서버 오류');
                            processQueue(index + 1);
                        }
                    });
                }

                function updateProgress(current, total, text) {
                    var percent = Math.round((current / total) * 100);
                    $('.jj-progress-fill').css('width', percent + '%');
                    $('.jj-status-text').text(text + ' (' + current + '/' + total + ')');
                }
            });
            </script>
        </div>
        <?php
    }

    /**
     * AJAX: 대량 업로드 및 설치
     */
    public function ajax_bulk_upload() {
        check_ajax_referer( 'jj_bulk_nonce', 'nonce' );

        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        if ( ! isset( $_FILES['file'] ) ) {
            wp_send_json_error( array( 'message' => __( '파일이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $file = $_FILES['file'];
        $auto_activate = isset( $_POST['auto_activate'] ) && $_POST['auto_activate'] === '1';

        // 파일 타입 검증
        if ( pathinfo( $file['name'], PATHINFO_EXTENSION ) !== 'zip' ) {
            wp_send_json_error( array( 'message' => __( 'ZIP 파일만 업로드할 수 있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 임시 디렉토리에 저장
        $upload_dir = wp_upload_dir();
        $temp_file = $upload_dir['basedir'] . '/jj-bulk-temp/' . sanitize_file_name( $file['name'] );

        if ( ! file_exists( dirname( $temp_file ) ) ) {
            wp_mkdir_p( dirname( $temp_file ) );
        }

        if ( ! move_uploaded_file( $file['tmp_name'], $temp_file ) ) {
            wp_send_json_error( array( 'message' => __( '파일 저장 실패', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 플러그인/테마 설치
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
        $result = $upgrader->install( $temp_file );

        // 임시 파일 삭제
        @unlink( $temp_file );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        $plugin_slug = $upgrader->plugin_info();
        
        // 자동 활성화
        if ( $auto_activate && $plugin_slug ) {
            activate_plugin( $plugin_slug );
            wp_send_json_success( array( 'message' => __( '설치 및 활성화 완료', 'acf-css-really-simple-style-management-center' ) ) );
        }

        wp_send_json_success( array( 'message' => __( '설치 완료', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * AJAX: 대량 활성화
     */
    public function ajax_bulk_activate() {
        check_ajax_referer( 'jj_bulk_nonce', 'nonce' );

        if ( ! current_user_can( 'activate_plugins' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $plugins = isset( $_POST['plugins'] ) ? array_map( 'sanitize_text_field', (array) $_POST['plugins'] ) : array();
        $activated = 0;
        $errors = array();

        foreach ( $plugins as $plugin ) {
            $result = activate_plugin( $plugin );
            if ( is_wp_error( $result ) ) {
                $errors[] = $result->get_error_message();
            } else {
                $activated++;
            }
        }

        wp_send_json_success( array(
            'message' => sprintf( __( '%d개 플러그인 활성화됨', 'acf-css-really-simple-style-management-center' ), $activated ),
            'errors' => $errors,
        ) );
    }

    /**
     * AJAX: 대량 비활성화
     */
    public function ajax_bulk_deactivate() {
        check_ajax_referer( 'jj_bulk_nonce', 'nonce' );

        if ( ! current_user_can( 'deactivate_plugins' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $plugins = isset( $_POST['plugins'] ) ? array_map( 'sanitize_text_field', (array) $_POST['plugins'] ) : array();
        
        deactivate_plugins( $plugins );

        wp_send_json_success( array(
            'message' => sprintf( __( '%d개 플러그인 비활성화됨', 'acf-css-really-simple-style-management-center' ), count( $plugins ) ),
        ) );
    }
}
