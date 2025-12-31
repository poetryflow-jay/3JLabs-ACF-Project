<?php
/**
 * 메인 플러그인 클래스
 * 
 * @package JJ_LicenseManagerincludes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Manager_Main {
    
    private static $instance = null;
    
    /**
     * 싱글톤 인스턴스
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
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    /**
     * 훅 초기화
     * [v2.0.2] WordPress 함수 존재 확인 후 훅 등록
     */
    private function init_hooks() {
        // WordPress 함수가 로드된 후에만 훅 등록
        if ( ! function_exists( 'add_action' ) ) {
            return;
        }
        
        // 관리자 초기화
        if ( function_exists( 'is_admin' ) && is_admin() ) {
            add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        }
        
        // API 엔드포인트 등록
        add_action( 'rest_api_init', array( $this, 'register_api_routes' ) );
        
        // WooCommerce 통합
        if ( class_exists( 'WooCommerce' ) ) {
            add_action( 'woocommerce_order_status_completed', array( $this, 'create_license_on_order_complete' ) );
            add_action( 'woocommerce_order_status_processing', array( $this, 'create_license_on_order_complete' ) );
        }
        
        // 일일 라이센스 상태 확인
        add_action( 'jj_license_manager_daily_check', array( $this, 'daily_license_check' ) );
        
        // 번역 로드
        add_action( 'init', array( $this, 'load_textdomain' ) );
    }
    
    /**
     * 의존성 로드
     */
    private function load_dependencies() {
        // 핵심 클래스들 (autoloader가 로드하지만, 안전을 위해 직접 로드)
        $core_classes = array(
            'JJ_License_Database' => 'includes/class-jj-license-database.php',
            'JJ_License_Generator' => 'includes/class-jj-license-generator.php',
            'JJ_License_Validator' => 'includes/class-jj-license-validator.php',
            'JJ_License_WooCommerce' => 'includes/integrations/class-jj-license-woocommerce.php',
            'JJ_License_WooCommerce_Product' => 'includes/integrations/class-jj-license-woocommerce-product.php',
            'JJ_License_API' => 'includes/api/class-jj-license-api.php',
            'JJ_License_Update_API' => 'includes/class-jj-license-update-api.php',
            // [v2.0.2] 원격 제어 및 로그 수집 클래스
            'JJ_License_Remote_Control' => 'includes/class-jj-license-remote-control.php',
            'JJ_License_Log_Collector' => 'includes/class-jj-license-log-collector.php',
            'JJ_License_Update_Distributor' => 'includes/class-jj-license-update-distributor.php',
        );
        
        foreach ( $core_classes as $class_name => $file_path ) {
            if ( ! class_exists( $class_name, false ) ) {
                $full_path = JJ_NEURAL_LINK_PATH . $file_path;
                if ( file_exists( $full_path ) ) {
                    require_once $full_path;
                }
            }
        }
        
        if ( is_admin() ) {
            $admin_classes = array(
                'JJ_License_Admin' => 'includes/admin/class-jj-license-admin.php',
                'JJ_License_Admin_User_Profile' => 'includes/admin/class-jj-license-admin-user-profile.php',
            );
            
            foreach ( $admin_classes as $class_name => $file_path ) {
                if ( ! class_exists( $class_name, false ) ) {
                    $full_path = JJ_NEURAL_LINK_PATH . $file_path;
                    if ( file_exists( $full_path ) ) {
                        require_once $full_path;
                    }
                }
            }
            
            // Admin 클래스 인스턴스 생성 (클래스 존재 확인 후)
            if ( class_exists( 'JJ_License_Admin' ) ) {
                JJ_License_Admin::instance();
            }
        }
    }
    
    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'JJ License Manager', 'jj-license-manager' ),
            __( 'JJ Licenses', 'jj-license-manager' ),
            'manage_options',
            'jj-license-manager',
            array( $this, 'render_admin_page' ),
            'dashicons-admin-network',
            30
        );
        
        add_submenu_page(
            'jj-license-manager',
            __( 'All Licenses', 'jj-license-manager' ),
            __( 'All Licenses', 'jj-license-manager' ),
            'manage_options',
            'jj-license-manager',
            array( $this, 'render_admin_page' )
        );
        
        add_submenu_page(
            'jj-license-manager',
            __( 'License Activations', 'jj-license-manager' ),
            __( 'Activations', 'jj-license-manager' ),
            'manage_options',
            'jj-license-activations',
            array( $this, 'render_activations_page' )
        );
        
        add_submenu_page(
            'jj-license-manager',
            __( 'Settings', 'jj-license-manager' ),
            __( 'Settings', 'jj-license-manager' ),
            'manage_options',
            'jj-license-settings',
            array( $this, 'render_settings_page' )
        );
        
        add_submenu_page(
            'jj-license-manager',
            __( 'Plugin Updates', 'jj-license-manager' ),
            __( 'Updates', 'jj-license-manager' ),
            'manage_options',
            'jj-license-updates',
            array( $this, 'render_updates_page' )
        );
    }
    
    /**
     * 관리자 페이지 렌더링
     */
    public function render_admin_page() {
        if ( ! class_exists( 'JJ_License_Admin' ) ) {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'JJ_License_Admin 클래스를 로드할 수 없습니다.', 'jj-license-manager' ) . '</p></div>';
            return;
        }
        $admin = JJ_License_Admin::instance();
        $admin->render_licenses_page();
    }
    
    /**
     * 활성화 페이지 렌더링
     */
    public function render_activations_page() {
        if ( ! class_exists( 'JJ_License_Admin' ) ) {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'JJ_License_Admin 클래스를 로드할 수 없습니다.', 'jj-license-manager' ) . '</p></div>';
            return;
        }
        $admin = JJ_License_Admin::instance();
        $admin->render_activations_page();
    }
    
    /**
     * 설정 페이지 렌더링
     */
    public function render_settings_page() {
        if ( ! class_exists( 'JJ_License_Admin' ) ) {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'JJ_License_Admin 클래스를 로드할 수 없습니다.', 'jj-license-manager' ) . '</p></div>';
            return;
        }
        $admin = JJ_License_Admin::instance();
        $admin->render_settings_page();
    }
    
    /**
     * 업데이트 페이지 렌더링
     */
    public function render_updates_page() {
        if ( ! class_exists( 'JJ_License_Admin' ) ) {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'JJ_License_Admin 클래스를 로드할 수 없습니다.', 'jj-license-manager' ) . '</p></div>';
            return;
        }
        $admin = JJ_License_Admin::instance();
        $admin->render_updates_page();
    }
    
    /**
     * 관리자 자산 로드
     */
    public function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'jj-license' ) === false ) {
            return;
        }
        
        wp_enqueue_style(
            'jj-license-admin',
            JJ_NEURAL_LINK_URL . 'assets/css/admin.css',
            array(),
            JJ_NEURAL_LINK_VERSION
        );
        
        wp_enqueue_script(
            'jj-license-admin',
            JJ_NEURAL_LINK_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            JJ_NEURAL_LINK_VERSION,
            true
        );
        
        wp_localize_script( 'jj-license-admin', 'jjLicenseManager', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'jj_license_manager_nonce' ),
            'strings' => array(
                'confirmDelete' => __( '정말로 이 라이센스를 삭제하시겠습니까?', 'jj-license-manager' ),
                'confirmDeactivate' => __( '정말로 이 라이센스를 비활성화하시겠습니까?', 'jj-license-manager' ),
            ),
        ) );
    }
    
    /**
     * API 라우트 등록
     */
    public function register_api_routes() {
        if ( ! class_exists( 'JJ_License_API' ) ) {
            return;
        }
        $api = new JJ_License_API();
        $api->register_routes();
    }
    
    /**
     * 주문 완료 시 라이센스 생성
     */
    public function create_license_on_order_complete( $order_id ) {
        if ( ! class_exists( 'JJ_License_WooCommerce' ) ) {
            return;
        }
        $woocommerce = new JJ_License_WooCommerce();
        $woocommerce->create_license_from_order( $order_id );
    }
    
    /**
     * 일일 라이센스 상태 확인
     */
    public function daily_license_check() {
        if ( ! class_exists( 'JJ_License_Validator' ) ) {
            return;
        }
        $validator = new JJ_License_Validator();
        $validator->check_expired_licenses();
    }
    
    /**
     * 번역 파일 로드
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'jj-license-manager',
            false,
            dirname( JJ_LICENSE_MANAGER_BASENAME ) . '/languages'
        );
    }
}

