<?php
/**
 * Plugin Name:       ACF Mail SMTP - Advanced Custom Form & Mail & SMTP
 * Plugin URI:        https://3j-labs.com/
 * Description:       ACF Mail SMTP - Advanced Custom Form & Mail & SMTP. 강력한 폼 빌더, SMTP 이메일 발송, 자동화 기능을 제공하는 올인원 폼 솔루션입니다.
 * Version:           1.0.1
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com/
 * Text Domain:       acf-mail-smtp
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package ACF_Mail_SMTP
 */

// 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 플러그인 상수 정의
 */
define( 'ACF_MAIL_SMTP_VERSION', '1.0.1' );
define( 'ACF_MAIL_SMTP_PLUGIN_FILE', __FILE__ );
define( 'ACF_MAIL_SMTP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ACF_MAIL_SMTP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ACF_MAIL_SMTP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'ACF_MAIL_SMTP_SLUG', 'acf-mail-smtp' );

// [v25.0.0] 보안 모듈 및 라이센스 관리 로드
$shared_path = dirname( dirname( __FILE__ ) ) . '/shared-ui-assets';
if ( file_exists( $shared_path . '/class-jj-security-module-v25.php' ) ) {
    require_once $shared_path . '/class-jj-security-module-v25.php';
    if ( class_exists( 'JJ_Security_Module_V25_Loader' ) ) {
        JJ_Security_Module_V25_Loader::instance( ACF_MAIL_SMTP_PLUGIN_DIR, ACF_MAIL_SMTP_PLUGIN_URL, ACF_MAIL_SMTP_VERSION, ACF_MAIL_SMTP_SLUG );
    }
}
if ( file_exists( $shared_path . '/class-jj-license-manager-shared.php' ) ) {
    require_once $shared_path . '/class-jj-license-manager-shared.php';
    if ( class_exists( 'JJ_License_Manager_Shared' ) ) {
        JJ_License_Manager_Shared::instance( ACF_MAIL_SMTP_SLUG );
    }
}

/**
 * 메인 플러그인 클래스
 */
if ( ! class_exists( 'ACF_Mail_SMTP' ) ) {
final class ACF_Mail_SMTP {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 폼 매니저
     */
    public $forms;

    /**
     * SMTP 매니저
     */
    public $smtp;

    /**
     * 자동화 매니저
     */
    public $automation;

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * 의존성 로드
     */
    private function load_dependencies() {
        // Core classes
        require_once ACF_MAIL_SMTP_PLUGIN_DIR . 'includes/class-form-manager.php';
        require_once ACF_MAIL_SMTP_PLUGIN_DIR . 'includes/class-smtp-manager.php';
        require_once ACF_MAIL_SMTP_PLUGIN_DIR . 'includes/class-automation-manager.php';
        require_once ACF_MAIL_SMTP_PLUGIN_DIR . 'includes/class-form-submission.php';
        require_once ACF_MAIL_SMTP_PLUGIN_DIR . 'includes/class-email-template.php';
        
        // Admin
        if ( is_admin() ) {
            require_once ACF_MAIL_SMTP_PLUGIN_DIR . 'admin/class-admin.php';
        }
        
        // Frontend
        require_once ACF_MAIL_SMTP_PLUGIN_DIR . 'includes/class-frontend.php';
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // Activation/Deactivation
        register_activation_hook( ACF_MAIL_SMTP_PLUGIN_FILE, array( $this, 'activate' ) );
        register_deactivation_hook( ACF_MAIL_SMTP_PLUGIN_FILE, array( $this, 'deactivate' ) );

        // Initialize
        add_action( 'plugins_loaded', array( $this, 'init' ) );
        add_action( 'init', array( $this, 'init_components' ) );

        // Admin
        if ( is_admin() ) {
            add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        }

        // Shortcode
        add_shortcode( 'acf_form', array( $this, 'render_form_shortcode' ) );
    }

    /**
     * 플러그인 초기화
     */
    public function init() {
        // Load textdomain
        load_plugin_textdomain( 'acf-mail-smtp', false, dirname( ACF_MAIL_SMTP_PLUGIN_BASENAME ) . '/languages' );
    }

    /**
     * 컴포넌트 초기화
     */
    public function init_components() {
        try {
            // Initialize core components
            if ( class_exists( 'ACF_Mail_SMTP_Form_Manager' ) ) {
                $this->forms = new ACF_Mail_SMTP_Form_Manager();
            }
            if ( class_exists( 'ACF_Mail_SMTP_SMTP_Manager' ) ) {
                $this->smtp = new ACF_Mail_SMTP_SMTP_Manager();
            }
            if ( class_exists( 'ACF_Mail_SMTP_Automation_Manager' ) ) {
                $this->automation = new ACF_Mail_SMTP_Automation_Manager();
            }

            // Initialize Admin
            if ( is_admin() && class_exists( 'ACF_Mail_SMTP_Admin' ) ) {
                new ACF_Mail_SMTP_Admin( $this );
            }

            // Initialize Frontend
            if ( class_exists( 'ACF_Mail_SMTP_Frontend' ) ) {
                new ACF_Mail_SMTP_Frontend( $this );
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF Mail SMTP: Components init error: ' . $e->getMessage() );
            }
        }
    }

    /**
     * 플러그인 활성화
     */
    public function activate() {
        // Create database tables
        $this->create_tables();

        // Set default options
        $this->set_default_options();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * 플러그인 비활성화
     */
    public function deactivate() {
        // Clear scheduled hooks
        if ( function_exists( 'wp_clear_scheduled_hook' ) ) {
            wp_clear_scheduled_hook( 'acf_mail_smtp_automation_cron' );
        }

        // Flush rewrite rules
        if ( function_exists( 'flush_rewrite_rules' ) ) {
            flush_rewrite_rules();
        }
    }

    /**
     * 데이터베이스 테이블 생성
     */
    private function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Forms table
        $table_forms = $wpdb->prefix . 'acf_mail_smtp_forms';
        $sql_forms = "CREATE TABLE IF NOT EXISTS $table_forms (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            title varchar(255) DEFAULT '',
            description text,
            fields longtext,
            settings longtext,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status)
        ) $charset_collate;";

        // Submissions table
        $table_submissions = $wpdb->prefix . 'acf_mail_smtp_submissions';
        $sql_submissions = "CREATE TABLE IF NOT EXISTS $table_submissions (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            form_id bigint(20) unsigned NOT NULL,
            data longtext,
            ip_address varchar(45) DEFAULT '',
            user_agent varchar(500) DEFAULT '',
            status varchar(20) DEFAULT 'new',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY form_id (form_id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        // Email logs table
        $table_emails = $wpdb->prefix . 'acf_mail_smtp_emails';
        $sql_emails = "CREATE TABLE IF NOT EXISTS $table_emails (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            submission_id bigint(20) unsigned DEFAULT 0,
            form_id bigint(20) unsigned DEFAULT 0,
            to_email varchar(255) NOT NULL,
            from_email varchar(255) DEFAULT '',
            subject varchar(500) DEFAULT '',
            message longtext,
            headers longtext,
            status varchar(20) DEFAULT 'pending',
            error_message text,
            sent_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY submission_id (submission_id),
            KEY form_id (form_id),
            KEY status (status),
            KEY sent_at (sent_at)
        ) $charset_collate;";

        // Automation rules table
        $table_automation = $wpdb->prefix . 'acf_mail_smtp_automation';
        $sql_automation = "CREATE TABLE IF NOT EXISTS $table_automation (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            form_id bigint(20) unsigned NOT NULL,
            name varchar(255) NOT NULL,
            trigger_type varchar(50) DEFAULT 'form_submit',
            conditions longtext,
            actions longtext,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY form_id (form_id),
            KEY status (status)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_forms );
        dbDelta( $sql_submissions );
        dbDelta( $sql_emails );
        dbDelta( $sql_automation );
    }

    /**
     * 기본 옵션 설정
     */
    private function set_default_options() {
        $defaults = array(
            'enable_smtp' => false,
            'smtp_host' => '',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'smtp_auth' => true,
            'smtp_username' => '',
            'smtp_password' => '',
            'from_email' => get_option( 'admin_email' ),
            'from_name' => get_option( 'blogname' ),
            'enable_automation' => true,
            'enable_email_logs' => true,
        );

        foreach ( $defaults as $key => $value ) {
            if ( get_option( 'acf_mail_smtp_' . $key ) === false ) {
                update_option( 'acf_mail_smtp_' . $key, $value );
            }
        }
    }

    /**
     * 관리 메뉴 추가
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __( 'ACF Mail SMTP', 'acf-mail-smtp' ),
            'ACF Mail SMTP 📧',
            'manage_options',
            'acf-mail-smtp',
            array( $this, 'render_dashboard' ),
            'dashicons-email-alt',
            71
        );

        // Submenus
        add_submenu_page(
            'acf-mail-smtp',
            __( 'Dashboard', 'acf-mail-smtp' ),
            __( 'Dashboard', 'acf-mail-smtp' ),
            'manage_options',
            'acf-mail-smtp',
            array( $this, 'render_dashboard' )
        );

        add_submenu_page(
            'acf-mail-smtp',
            __( 'Forms', 'acf-mail-smtp' ),
            __( 'Forms', 'acf-mail-smtp' ),
            'manage_options',
            'acf-mail-smtp-forms',
            array( $this, 'render_forms' )
        );

        add_submenu_page(
            'acf-mail-smtp',
            __( 'Submissions', 'acf-mail-smtp' ),
            __( 'Submissions', 'acf-mail-smtp' ),
            'manage_options',
            'acf-mail-smtp-submissions',
            array( $this, 'render_submissions' )
        );

        add_submenu_page(
            'acf-mail-smtp',
            __( 'SMTP Settings', 'acf-mail-smtp' ),
            __( 'SMTP Settings', 'acf-mail-smtp' ),
            'manage_options',
            'acf-mail-smtp-smtp',
            array( $this, 'render_smtp' )
        );

        add_submenu_page(
            'acf-mail-smtp',
            __( 'Automation', 'acf-mail-smtp' ),
            __( 'Automation', 'acf-mail-smtp' ),
            'manage_options',
            'acf-mail-smtp-automation',
            array( $this, 'render_automation' )
        );

        add_submenu_page(
            'acf-mail-smtp',
            __( 'Email Logs', 'acf-mail-smtp' ),
            __( 'Email Logs', 'acf-mail-smtp' ),
            'manage_options',
            'acf-mail-smtp-logs',
            array( $this, 'render_logs' )
        );

        add_submenu_page(
            'acf-mail-smtp',
            __( 'Settings', 'acf-mail-smtp' ),
            __( 'Settings', 'acf-mail-smtp' ),
            'manage_options',
            'acf-mail-smtp-settings',
            array( $this, 'render_settings' )
        );
    }

    /**
     * 관리자 에셋 로드
     */
    public function enqueue_admin_assets( $hook ) {
        // Only on plugin pages
        if ( strpos( $hook, 'acf-mail-smtp' ) === false ) {
            return;
        }

        // [v25.0.0] v25 디자인 시스템 CSS
        $shared_path = dirname( dirname( ACF_MAIL_SMTP_PLUGIN_DIR ) ) . '/acf-css-really-simple-style-management-center-master';
        
        if ( file_exists( $shared_path . '/assets/css/jj-design-system-v25.css' ) ) {
            wp_enqueue_style(
                'jj-design-system-v25',
                dirname( dirname( ACF_MAIL_SMTP_PLUGIN_URL ) ) . '/acf-css-really-simple-style-management-center-master/assets/css/jj-design-system-v25.css',
                array(),
                '25.0.0'
            );
        }

        // Custom UI CSS
        wp_enqueue_style(
            'acf-mail-smtp-admin-v25',
            ACF_MAIL_SMTP_PLUGIN_URL . 'assets/css/admin-v25.css',
            array( 'jj-design-system-v25' ),
            ACF_MAIL_SMTP_VERSION
        );

        // Animations
        if ( file_exists( $shared_path . '/assets/css/jj-animations-v25.css' ) ) {
            wp_enqueue_style(
                'jj-animations-v25',
                dirname( dirname( ACF_MAIL_SMTP_PLUGIN_URL ) ) . '/acf-css-really-simple-style-management-center-master/assets/css/jj-animations-v25.css',
                array(),
                '25.0.0'
            );
        }

        // Dark mode
        if ( file_exists( $shared_path . '/assets/js/jj-dark-mode-v25.js' ) ) {
            wp_enqueue_script(
                'jj-dark-mode-v25',
                dirname( dirname( ACF_MAIL_SMTP_PLUGIN_URL ) ) . '/acf-css-really-simple-style-management-center-master/assets/js/jj-dark-mode-v25.js',
                array(),
                '25.0.0',
                true
            );
        }

        // Admin JS
        wp_enqueue_script(
            'acf-mail-smtp-admin',
            ACF_MAIL_SMTP_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            ACF_MAIL_SMTP_VERSION,
            true
        );

        // Localize script
        wp_localize_script( 'acf-mail-smtp-admin', 'acfMailSmtp', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'acf_mail_smtp_nonce' ),
            'strings' => array(
                'confirmDelete' => __( '정말 삭제하시겠습니까?', 'acf-mail-smtp' ),
                'saving' => __( '저장 중...', 'acf-mail-smtp' ),
                'saved' => __( '저장되었습니다.', 'acf-mail-smtp' ),
                'error' => __( '오류가 발생했습니다.', 'acf-mail-smtp' ),
            ),
        ) );
    }

    /**
     * 폼 숏코드 렌더링
     */
    public function render_form_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'id' => 0,
            'title' => true,
        ), $atts, 'acf_form' );

        $form_id = intval( $atts['id'] );
        if ( ! $form_id ) {
            return '';
        }

        if ( ! $this->forms ) {
            return '';
        }

        return $this->forms->render_form( $form_id, $atts );
    }

    // Admin page render methods
    public function render_dashboard() {
        $file = ACF_MAIL_SMTP_PLUGIN_DIR . 'admin/views/dashboard.php';
        if ( file_exists( $file ) ) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__( 'Dashboard', 'acf-mail-smtp' ) . '</h1><p>' . esc_html__( 'Dashboard view file not found.', 'acf-mail-smtp' ) . '</p></div>';
        }
    }

    public function render_forms() {
        $file = ACF_MAIL_SMTP_PLUGIN_DIR . 'admin/views/forms.php';
        if ( file_exists( $file ) ) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__( 'Forms', 'acf-mail-smtp' ) . '</h1><p>' . esc_html__( 'Forms view file not found.', 'acf-mail-smtp' ) . '</p></div>';
        }
    }

    public function render_submissions() {
        $file = ACF_MAIL_SMTP_PLUGIN_DIR . 'admin/views/submissions.php';
        if ( file_exists( $file ) ) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__( 'Submissions', 'acf-mail-smtp' ) . '</h1><p>' . esc_html__( 'Submissions view file not found.', 'acf-mail-smtp' ) . '</p></div>';
        }
    }

    public function render_smtp() {
        $file = ACF_MAIL_SMTP_PLUGIN_DIR . 'admin/views/smtp.php';
        if ( file_exists( $file ) ) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__( 'SMTP Settings', 'acf-mail-smtp' ) . '</h1><p>' . esc_html__( 'SMTP view file not found.', 'acf-mail-smtp' ) . '</p></div>';
        }
    }

    public function render_automation() {
        $file = ACF_MAIL_SMTP_PLUGIN_DIR . 'admin/views/automation.php';
        if ( file_exists( $file ) ) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__( 'Automation', 'acf-mail-smtp' ) . '</h1><p>' . esc_html__( 'Automation view file not found.', 'acf-mail-smtp' ) . '</p></div>';
        }
    }

    public function render_logs() {
        $file = ACF_MAIL_SMTP_PLUGIN_DIR . 'admin/views/logs.php';
        if ( file_exists( $file ) ) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__( 'Email Logs', 'acf-mail-smtp' ) . '</h1><p>' . esc_html__( 'Logs view file not found.', 'acf-mail-smtp' ) . '</p></div>';
        }
    }

    public function render_settings() {
        $file = ACF_MAIL_SMTP_PLUGIN_DIR . 'admin/views/settings.php';
        if ( file_exists( $file ) ) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__( 'Settings', 'acf-mail-smtp' ) . '</h1><p>' . esc_html__( 'Settings view file not found.', 'acf-mail-smtp' ) . '</p></div>';
        }
    }

    /**
     * 설정 저장 (AJAX)
     */
    public function ajax_save_settings() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $settings = isset( $_POST['settings'] ) ? $_POST['settings'] : array();

        $allowed_keys = array( 'enable_email_logs', 'enable_automation' );

        foreach ( $allowed_keys as $key ) {
            if ( isset( $settings[ $key ] ) ) {
                update_option( 'acf_mail_smtp_' . $key, (bool) $settings[ $key ] );
            }
        }

        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', 'acf-mail-smtp' ) ) );
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new Exception( 'Cannot unserialize singleton' );
    }
}

/**
 * 플러그인 인스턴스 반환
 */
function ACF_Mail_SMTP() {
    return ACF_Mail_SMTP::get_instance();
}

// Initialize plugin
ACF_Mail_SMTP();
}
