<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Partner Hub
 * 
 * 파트너들이 자신의 고객 사이트 수십, 수백 개를 한 화면에서 관리할 수 있는 중앙 관제 대시보드
 * 
 * @since v6.1.0
 */
class JJ_Partner_Hub {
    
    private static $instance = null;
    private $page_slug = 'jj-partner-hub';
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Partner 에디션에서만 활성화
        if ( ! $this->is_partner_edition() ) {
            return;
        }
        
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_partner_hub_list_sites', array( $this, 'ajax_list_sites' ) );
        add_action( 'wp_ajax_jj_partner_hub_apply_style', array( $this, 'ajax_apply_style' ) );
        add_action( 'wp_ajax_jj_partner_hub_sync_all', array( $this, 'ajax_sync_all' ) );
    }
    
    /**
     * Partner 에디션 확인
     */
    private function is_partner_edition() {
        if ( ! class_exists( 'JJ_Edition_Controller' ) ) {
            return false;
        }
        
        $controller = JJ_Edition_Controller::instance();
        $license_type = $controller->get_license_type();
        
        return in_array( $license_type, array( 'PARTNER', 'MASTER' ) );
    }
    
    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Partner Hub', 'acf-css-really-simple-style-management-center' ),
            __( 'Partner Hub', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            $this->page_slug,
            array( $this, 'render_dashboard' ),
            'dashicons-networking',
            3
        );
    }
    
    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        if ( $hook !== 'toplevel_page_' . $this->page_slug ) {
            return;
        }
        
        wp_enqueue_style( 
            'jj-partner-hub', 
            JJ_STYLE_GUIDE_URL . 'assets/css/jj-partner-hub.css', 
            array(), 
            JJ_STYLE_GUIDE_VERSION 
        );
        
        wp_enqueue_script( 
            'jj-partner-hub', 
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-partner-hub.js', 
            array( 'jquery' ), 
            JJ_STYLE_GUIDE_VERSION, 
            true 
        );
        
        wp_localize_script( 'jj-partner-hub', 'jjPartnerHub', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'jj_partner_hub_action' ),
        ) );
    }
    
    /**
     * 대시보드 렌더링
     */
    public function render_dashboard() {
        ?>
        <div class="wrap jj-partner-hub-wrap">
            <h1><?php esc_html_e( 'Partner Hub - 중앙 관제 대시보드', 'acf-css-really-simple-style-management-center' ); ?></h1>
            <p class="description">
                <?php esc_html_e( '연결된 모든 고객 사이트를 한 화면에서 관리하고, 스타일을 일괄 적용할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
            </p>
            
            <div class="jj-partner-hub-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
                <div class="jj-stat-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
                    <h3 style="margin: 0; font-size: 32px; color: #2271b1;">0</h3>
                    <p style="margin: 5px 0 0; color: #666;"><?php esc_html_e( '연결된 사이트', 'acf-css-really-simple-style-management-center' ); ?></p>
                </div>
                <div class="jj-stat-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
                    <h3 style="margin: 0; font-size: 32px; color: #00a32a;">0</h3>
                    <p style="margin: 5px 0 0; color: #666;"><?php esc_html_e( '활성 사이트', 'acf-css-really-simple-style-management-center' ); ?></p>
                </div>
                <div class="jj-stat-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
                    <h3 style="margin: 0; font-size: 32px; color: #d63638;">0</h3>
                    <p style="margin: 5px 0 0; color: #666;"><?php esc_html_e( '오류 사이트', 'acf-css-really-simple-style-management-center' ); ?></p>
                </div>
            </div>
            
            <div class="jj-partner-hub-actions" style="margin: 20px 0;">
                <button type="button" class="button button-primary" id="jj-partner-hub-sync-all">
                    <?php esc_html_e( '전체 사이트 동기화', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
                <button type="button" class="button button-secondary" id="jj-partner-hub-add-site">
                    <?php esc_html_e( '사이트 추가', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
                <button type="button" class="button button-secondary" id="jj-partner-hub-export-template">
                    <?php esc_html_e( '현재 설정을 템플릿으로 저장', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
            </div>
            
            <div class="jj-partner-hub-sites-list" style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 20px;">
                <h2><?php esc_html_e( '연결된 사이트 목록', 'acf-css-really-simple-style-management-center' ); ?></h2>
                <div id="jj-partner-hub-sites-container" style="margin-top: 20px;">
                    <div class="jj-loading" style="text-align: center; padding: 40px;">
                        <span class="spinner is-active"></span>
                        <p><?php esc_html_e( '사이트 목록을 불러오는 중...', 'acf-css-really-simple-style-management-center' ); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX: 사이트 목록 조회
     */
    public function ajax_list_sites() {
        check_ajax_referer( 'jj_partner_hub_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }
        
        // 연결된 사이트 목록 가져오기 (옵션에서)
        $sites = get_option( 'jj_partner_hub_sites', array() );
        
        // 각 사이트의 상태 확인 (실제로는 API 호출 필요)
        $sites_with_status = array();
        foreach ( $sites as $site_id => $site_data ) {
            $sites_with_status[] = array_merge( $site_data, array(
                'id' => $site_id,
                'status' => $this->check_site_status( $site_data ),
                'last_sync' => isset( $site_data['last_sync'] ) ? $site_data['last_sync'] : 'N/A',
            ) );
        }
        
        wp_send_json_success( array( 'sites' => $sites_with_status ) );
    }
    
    /**
     * AJAX: 스타일 적용
     */
    public function ajax_apply_style() {
        check_ajax_referer( 'jj_partner_hub_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }
        
        $site_id = isset( $_POST['site_id'] ) ? sanitize_text_field( $_POST['site_id'] ) : '';
        $template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';
        
        if ( empty( $site_id ) ) {
            wp_send_json_error( array( 'message' => '사이트 ID가 필요합니다.' ) );
        }
        
        // 현재 설정을 템플릿으로 사용
        $current_options = get_option( 'jj_style_guide_options', array() );
        
        // 원격 사이트에 적용 (REST API 호출)
        $result = $this->apply_style_to_remote_site( $site_id, $current_options );
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => '스타일이 적용되었습니다.' ) );
        } else {
            wp_send_json_error( array( 'message' => '스타일 적용에 실패했습니다.' ) );
        }
    }
    
    /**
     * AJAX: 전체 동기화
     */
    public function ajax_sync_all() {
        check_ajax_referer( 'jj_partner_hub_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }
        
        $sites = get_option( 'jj_partner_hub_sites', array() );
        $current_options = get_option( 'jj_style_guide_options', array() );
        
        $success_count = 0;
        $fail_count = 0;
        
        foreach ( $sites as $site_id => $site_data ) {
            $result = $this->apply_style_to_remote_site( $site_id, $current_options );
            if ( $result ) {
                $success_count++;
            } else {
                $fail_count++;
            }
        }
        
        wp_send_json_success( array(
            'message' => sprintf( '동기화 완료: 성공 %d개, 실패 %d개', $success_count, $fail_count ),
            'success_count' => $success_count,
            'fail_count' => $fail_count,
        ) );
    }
    
    /**
     * 사이트 상태 확인
     */
    private function check_site_status( $site_data ) {
        if ( ! isset( $site_data['api_url'] ) || ! isset( $site_data['api_key'] ) ) {
            return 'error';
        }
        
        // 실제로는 REST API 호출로 상태 확인
        // 현재는 데모로 'active' 반환
        return 'active';
    }
    
    /**
     * 원격 사이트에 스타일 적용
     */
    private function apply_style_to_remote_site( $site_id, $options ) {
        $sites = get_option( 'jj_partner_hub_sites', array() );
        
        if ( ! isset( $sites[ $site_id ] ) ) {
            return false;
        }
        
        $site_data = $sites[ $site_id ];
        
        if ( ! isset( $site_data['api_url'] ) || ! isset( $site_data['api_key'] ) ) {
            return false;
        }
        
        // REST API로 스타일 적용
        $response = wp_remote_post( $site_data['api_url'] . '/wp-json/jj-style-guide/v1/settings', array(
            'timeout' => 15,
            'headers' => array(
                'Authorization' => 'Bearer ' . $site_data['api_key'],
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode( array( 'settings' => $options ) ),
        ) );
        
        if ( is_wp_error( $response ) ) {
            return false;
        }
        
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        
        // 마지막 동기화 시간 업데이트
        $sites[ $site_id ]['last_sync'] = current_time( 'mysql' );
        update_option( 'jj_partner_hub_sites', $sites );
        
        return isset( $body['success'] ) && $body['success'];
    }
}

