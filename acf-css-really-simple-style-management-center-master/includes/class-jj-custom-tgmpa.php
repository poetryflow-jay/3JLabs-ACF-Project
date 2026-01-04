<?php
/**
 * 3J Labs Custom TGMPA Implementation
 * 
 * TGMPA 라이브러리가 없을 때 사용하는 커스텀 구현입니다.
 * 필수 플러그인 설치 및 활성화를 관리합니다.
 * 
 * @package ACF_CSS
 * @version 23.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * TGMPA 호환 함수
 * 
 * @param array $plugins 플러그인 배열
 * @param array $config 설정 배열
 */
function tgmpa( $plugins, $config ) {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    $manager = JJ_Custom_TGMPA::instance();
    $manager->register_plugins( $plugins, $config );
}

/**
 * 커스텀 TGMPA 클래스
 */
class JJ_Custom_TGMPA {
    
    private static $instance = null;
    private $plugins = array();
    private $config = array();
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );
        add_action( 'admin_notices', array( $this, 'show_admin_notices' ) );
        add_action( 'admin_init', array( $this, 'handle_plugin_actions' ) );
    }
    
    /**
     * 플러그인 등록
     */
    public function register_plugins( $plugins, $config ) {
        $this->plugins = $plugins;
        $this->config = $config;
    }
    
    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        if ( empty( $this->plugins ) ) {
            return;
        }
        
        $menu_title = isset( $this->config['strings']['menu_title'] ) 
            ? $this->config['strings']['menu_title'] 
            : __( '필수 플러그인', 'acf-css-really-simple-style-management-center' );
        
        $page_title = isset( $this->config['strings']['page_title'] ) 
            ? $this->config['strings']['page_title'] 
            : __( '3J Labs 필수 플러그인 설치', 'acf-css-really-simple-style-management-center' );
        
        $menu_slug = isset( $this->config['menu'] ) ? $this->config['menu'] : '3j-install-plugins';
        
        add_submenu_page(
            $this->config['parent_slug'],
            $page_title,
            $menu_title,
            $this->config['capability'],
            $menu_slug,
            array( $this, 'render_plugin_page' )
        );
    }
    
    /**
     * 관리자 알림 표시
     */
    public function show_admin_notices() {
        if ( empty( $this->plugins ) ) {
            return;
        }
        
        $screen = get_current_screen();
        if ( $screen && $screen->id === 'plugins' ) {
            $required_plugins = $this->get_missing_required_plugins();
            $recommended_plugins = $this->get_missing_recommended_plugins();
            
            if ( ! empty( $required_plugins ) ) {
                ?>
                <div class="notice notice-error is-dismissible">
                    <p>
                        <strong><?php esc_html_e( 'ACF CSS 설정 관리자', 'acf-css-really-simple-style-management-center' ); ?>:</strong>
                        <?php 
                        printf(
                            esc_html__( '다음 필수 플러그인을 설치하고 활성화해주세요: %s', 'acf-css-really-simple-style-management-center' ),
                            '<strong>' . esc_html( implode( ', ', $required_plugins ) ) . '</strong>'
                        );
                        ?>
                        <a href="<?php echo esc_url( admin_url( 'plugins.php?page=' . $this->config['menu'] ) ); ?>" class="button button-primary" style="margin-left: 10px;">
                            <?php esc_html_e( '필수 플러그인 설치', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </p>
                </div>
                <?php
            } elseif ( ! empty( $recommended_plugins ) ) {
                ?>
                <div class="notice notice-info is-dismissible">
                    <p>
                        <strong><?php esc_html_e( 'ACF CSS 설정 관리자', 'acf-css-really-simple-style-management-center' ); ?>:</strong>
                        <?php 
                        printf(
                            esc_html__( '다음 권장 플러그인을 설치하여 더 나은 경험을 제공합니다: %s', 'acf-css-really-simple-style-management-center' ),
                            '<strong>' . esc_html( implode( ', ', $recommended_plugins ) ) . '</strong>'
                        );
                        ?>
                        <a href="<?php echo esc_url( admin_url( 'plugins.php?page=' . $this->config['menu'] ) ); ?>" class="button" style="margin-left: 10px;">
                            <?php esc_html_e( '권장 플러그인 보기', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </p>
                </div>
                <?php
            }
        }
    }
    
    /**
     * 플러그인 페이지 렌더링
     */
    public function render_plugin_page() {
        if ( ! current_user_can( $this->config['capability'] ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $page_title = isset( $this->config['strings']['page_title'] ) 
            ? $this->config['strings']['page_title'] 
            : __( '3J Labs 필수 플러그인 설치', 'acf-css-really-simple-style-management-center' );
        
        // 성공/오류 메시지 표시
        if ( isset( $_GET['tgmpa-activate-success'] ) ) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e( '플러그인이 성공적으로 활성화되었습니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>
            <?php
        }
        
        if ( isset( $_GET['tgmpa-already-active'] ) ) {
            ?>
            <div class="notice notice-info is-dismissible">
                <p><?php esc_html_e( '플러그인이 이미 활성화되어 있습니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>
            <?php
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( $page_title ); ?></h1>
            
            <?php
            $this->render_plugin_table();
            ?>
        </div>
        <?php
    }
    
    /**
     * 플러그인 테이블 렌더링
     */
    private function render_plugin_table() {
        $required_plugins = array();
        $recommended_plugins = array();
        
        foreach ( $this->plugins as $plugin ) {
            if ( $plugin['required'] ) {
                $required_plugins[] = $plugin;
            } else {
                $recommended_plugins[] = $plugin;
            }
        }
        
        ?>
        <div class="tgmpa-wrap">
            <?php if ( ! empty( $required_plugins ) ) : ?>
                <h2><?php esc_html_e( '필수 플러그인', 'acf-css-really-simple-style-management-center' ); ?></h2>
                <p class="description">
                    <?php esc_html_e( '다음 플러그인들은 ACF CSS 설정 관리자의 정상 작동을 위해 필요합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <?php $this->render_plugin_list( $required_plugins ); ?>
            <?php endif; ?>
            
            <?php if ( ! empty( $recommended_plugins ) ) : ?>
                <h2><?php esc_html_e( '권장 플러그인', 'acf-css-really-simple-style-management-center' ); ?></h2>
                <p class="description">
                    <?php esc_html_e( '다음 플러그인들을 설치하면 더 나은 경험을 제공합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <?php $this->render_plugin_list( $recommended_plugins ); ?>
            <?php endif; ?>
        </div>
        
        <style>
        .tgmpa-wrap {
            margin-top: 20px;
        }
        .tgmpa-wrap h2 {
            margin-top: 30px;
            margin-bottom: 10px;
        }
        .tgmpa-plugin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .tgmpa-plugin-table th,
        .tgmpa-plugin-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .tgmpa-plugin-table th {
            background-color: #f5f5f5;
            font-weight: 600;
        }
        .tgmpa-plugin-table .plugin-name {
            font-weight: 600;
        }
        .tgmpa-plugin-table .plugin-status {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }
        .tgmpa-plugin-table .status-installed {
            background-color: #d4edda;
            color: #155724;
        }
        .tgmpa-plugin-table .status-not-installed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .tgmpa-plugin-table .status-active {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .tgmpa-plugin-table .plugin-actions a {
            margin-right: 10px;
        }
        </style>
        <?php
    }
    
    /**
     * 플러그인 목록 렌더링
     */
    private function render_plugin_list( $plugins ) {
        ?>
        <table class="wp-list-table widefat fixed striped tgmpa-plugin-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( '플러그인', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <th><?php esc_html_e( '소스', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <th><?php esc_html_e( '타입', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <th><?php esc_html_e( '버전', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <th><?php esc_html_e( '상태', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <th><?php esc_html_e( '작업', 'acf-css-really-simple-style-management-center' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $plugins as $plugin ) : 
                    $status = $this->get_plugin_status( $plugin );
                    $actions = $this->get_plugin_actions( $plugin, $status );
                ?>
                <tr>
                    <td class="plugin-name"><?php echo esc_html( $plugin['name'] ); ?></td>
                    <td><?php echo esc_html( $plugin['source'] === 'external' ? '외부 소스' : 'WordPress.org' ); ?></td>
                    <td>
                        <?php if ( $plugin['required'] ) : ?>
                            <span style="color: #d63638; font-weight: 600;"><?php esc_html_e( '필수', 'acf-css-really-simple-style-management-center' ); ?></span>
                        <?php else : ?>
                            <span style="color: #2271b1;"><?php esc_html_e( '권장', 'acf-css-really-simple-style-management-center' ); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php 
                        printf(
                            esc_html__( '최소: %s', 'acf-css-really-simple-style-management-center' ),
                            esc_html( $plugin['version'] )
                        );
                        ?>
                    </td>
                    <td>
                        <span class="plugin-status status-<?php echo esc_attr( $status ); ?>">
                            <?php echo esc_html( $this->get_status_label( $status ) ); ?>
                        </span>
                    </td>
                    <td class="plugin-actions">
                        <?php echo $actions; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * 플러그인 상태 확인
     */
    private function get_plugin_status( $plugin ) {
        $plugin_file = $this->get_plugin_file( $plugin['slug'] );
        
        if ( ! $plugin_file ) {
            return 'not-installed';
        }
        
        if ( is_plugin_active( $plugin_file ) ) {
            return 'active';
        }
        
        return 'installed';
    }
    
    /**
     * 플러그인 파일 경로 반환
     */
    private function get_plugin_file( $slug ) {
        // [v23.0.7] 플러그인 파일 매핑 수정 - 올바른 메인 파일명 사용
        $plugin_files = array(
            'acf-css-woo-license' => 'acf-css-woo-license/acf-css-woo-license.php',
            'acf-code-snippets-box' => 'acf-code-snippets-box/acf-code-snippets-box.php',
            'jj-marketing-automation-dashboard' => 'jj-marketing-automation-dashboard/jj-marketing-dashboard.php',
            'wp-bulk-seo-aeo' => 'wp-bulk-seo-aeo/wp-bulk-seo-aeo.php',
            'wp-bulk-manager' => 'wp-bulk-manager/wp-bulk-manager.php', // [v23.0.7] 수정: wp-bulk-installer.php → wp-bulk-manager.php
        );

        // [v23.0.7] 대체 파일명 매핑 (호환성 유지)
        $alt_files = array(
            'acf-css-woo-license' => array(
                'acf-css-woo-license/acf-css-neural-link.php', // 레거시 호환
            ),
            'wp-bulk-manager' => array(
                'wp-bulk-manager/wp-bulk-installer.php', // 레거시 호환
            ),
            'wp-bulk-seo-aeo' => array(
                'SEO/wp-bulk-seo-aeo/wp-bulk-seo-aeo.php', // 대체 경로
            ),
            'jj-marketing-automation-dashboard' => array(
                'jj-marketing-automation-dashboard/jj-marketing-automation-dashboard.php', // 대체 파일명
            ),
        );

        if ( isset( $plugin_files[ $slug ] ) ) {
            $plugin_file = $plugin_files[ $slug ];

            // 플러그인이 실제로 존재하는지 확인
            if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
                return $plugin_file;
            }

            // [v23.0.7] 대체 경로 확인 (모든 플러그인에 대해)
            if ( isset( $alt_files[ $slug ] ) ) {
                foreach ( $alt_files[ $slug ] as $alt_path ) {
                    if ( file_exists( WP_PLUGIN_DIR . '/' . $alt_path ) ) {
                        return $alt_path;
                    }
                }
            }

            // [v23.0.7] 동적 탐색: 폴더 내 메인 PHP 파일 자동 감지
            $plugin_dir = WP_PLUGIN_DIR . '/' . $slug;
            if ( is_dir( $plugin_dir ) ) {
                $main_file = $this->find_main_plugin_file( $plugin_dir, $slug );
                if ( $main_file ) {
                    return $slug . '/' . $main_file;
                }
            }

            return false;
        }

        return false;
    }

    /**
     * [v23.0.7] 플러그인 폴더에서 메인 파일 자동 감지
     *
     * @param string $plugin_dir 플러그인 디렉토리 경로
     * @param string $slug 플러그인 슬러그
     * @return string|false 메인 파일명 또는 false
     */
    private function find_main_plugin_file( $plugin_dir, $slug ) {
        // 일반적인 메인 파일 패턴
        $patterns = array(
            $slug . '.php',                    // 슬러그와 동일한 이름
            str_replace( '-', '_', $slug ) . '.php', // 언더스코어 버전
            'plugin.php',                      // 일반적인 이름
            'index.php',                       // 인덱스 파일
        );

        foreach ( $patterns as $pattern ) {
            $file_path = $plugin_dir . '/' . $pattern;
            if ( file_exists( $file_path ) ) {
                // Plugin Name 헤더가 있는지 확인
                $file_content = file_get_contents( $file_path );
                if ( strpos( $file_content, 'Plugin Name:' ) !== false ) {
                    return $pattern;
                }
            }
        }

        // 폴더 내 모든 PHP 파일을 검사하여 Plugin Name 헤더 찾기
        $php_files = glob( $plugin_dir . '/*.php' );
        if ( $php_files ) {
            foreach ( $php_files as $php_file ) {
                $file_content = file_get_contents( $php_file );
                if ( strpos( $file_content, 'Plugin Name:' ) !== false ) {
                    return basename( $php_file );
                }
            }
        }

        return false;
    }
    
    /**
     * 상태 레이블 반환
     */
    private function get_status_label( $status ) {
        $labels = array(
            'not-installed' => __( '설치되지 않음', 'acf-css-really-simple-style-management-center' ),
            'installed' => __( '설치됨 (비활성화)', 'acf-css-really-simple-style-management-center' ),
            'active' => __( '활성화됨', 'acf-css-really-simple-style-management-center' ),
        );
        
        return isset( $labels[ $status ] ) ? $labels[ $status ] : $status;
    }
    
    /**
     * 플러그인 작업 버튼 생성
     */
    private function get_plugin_actions( $plugin, $status ) {
        $actions = array();
        $nonce = wp_create_nonce( '3j-install-plugins' );
        $page_url = admin_url( 'plugins.php?page=' . $this->config['menu'] );
        
        if ( $status === 'not-installed' ) {
            $install_url = add_query_arg( array(
                'plugin' => $plugin['slug'],
                '3j-install' => 'install',
                '_wpnonce' => $nonce,
            ), $page_url );
            
            $actions[] = sprintf(
                '<a href="%s" class="button button-primary">%s</a>',
                esc_url( $install_url ),
                esc_html__( '설치', 'acf-css-really-simple-style-management-center' )
            );
        } elseif ( $status === 'installed' ) {
            $activate_url = add_query_arg( array(
                'plugin' => $plugin['slug'],
                '3j-activate' => 'activate',
                '_wpnonce' => $nonce,
            ), $page_url );
            
            $actions[] = sprintf(
                '<a href="%s" class="button">%s</a>',
                esc_url( $activate_url ),
                esc_html__( '활성화', 'acf-css-really-simple-style-management-center' )
            );
        } else {
            $actions[] = '<span style="color: #46b450;">✓ ' . esc_html__( '활성화됨', 'acf-css-really-simple-style-management-center' ) . '</span>';
        }
        
        return implode( ' ', $actions );
    }
    
    /**
     * 누락된 필수 플러그인 목록
     */
    private function get_missing_required_plugins() {
        $missing = array();
        
        foreach ( $this->plugins as $plugin ) {
            if ( $plugin['required'] && $this->get_plugin_status( $plugin ) !== 'active' ) {
                $missing[] = $plugin['name'];
            }
        }
        
        return $missing;
    }
    
    /**
     * 누락된 권장 플러그인 목록
     */
    private function get_missing_recommended_plugins() {
        $missing = array();
        
        foreach ( $this->plugins as $plugin ) {
            if ( ! $plugin['required'] && $this->get_plugin_status( $plugin ) === 'not-installed' ) {
                $missing[] = $plugin['name'];
            }
        }
        
        return $missing;
    }
    
    /**
     * 플러그인 작업 처리
     */
    public function handle_plugin_actions() {
        if ( ! current_user_can( $this->config['capability'] ) ) {
            return;
        }
        
        // TGMPA 스타일 nonce 확인
        if ( isset( $_GET['tgmpa-activate'] ) || isset( $_GET['tgmpa-install'] ) ) {
            if ( ! isset( $_GET['tgmpa-nonce'] ) || ! wp_verify_nonce( $_GET['tgmpa-nonce'], 'tgmpa' ) ) {
                wp_die( __( '보안 검증에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) );
            }
        }
        
        // 플러그인 활성화 (TGMPA 스타일)
        if ( isset( $_GET['tgmpa-activate'] ) && $_GET['tgmpa-activate'] === 'activate-plugin' && isset( $_GET['plugin'] ) ) {
            $this->activate_plugin( $_GET['plugin'] );
        }
        
        // 플러그인 설치 (TGMPA 스타일)
        if ( isset( $_GET['tgmpa-install'] ) && $_GET['tgmpa-install'] === 'install-plugin' && isset( $_GET['plugin'] ) ) {
            $this->install_plugin( $_GET['plugin'] );
        }
    }
    
    /**
     * 플러그인 활성화
     */
    private function activate_plugin( $slug ) {
        $plugin_file = $this->get_plugin_file( $slug );
        
        if ( ! $plugin_file ) {
            wp_die( __( '플러그인을 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        if ( ! is_plugin_active( $plugin_file ) ) {
            $result = activate_plugin( $plugin_file );
            
            if ( is_wp_error( $result ) ) {
                wp_die( $result->get_error_message() );
            }
            
            // TGMPA 스타일 리다이렉트 (사용자 제공 URL 구조 참고)
            $redirect_url = add_query_arg( array(
                'page' => $this->config['menu'],
                'plugin' => $slug,
                'tgmpa-activate' => 'activate-plugin',
                'tgmpa-nonce' => wp_create_nonce( 'tgmpa' ),
            ), admin_url( 'plugins.php' ) );
            
            // 성공 메시지 추가
            $redirect_url = add_query_arg( 'tgmpa-activate-success', 'true', $redirect_url );
            
            wp_redirect( $redirect_url );
            exit;
        } else {
            // 이미 활성화된 경우
            wp_redirect( add_query_arg( array(
                'page' => $this->config['menu'],
                'plugin' => $slug,
                'tgmpa-already-active' => 'true',
            ), admin_url( 'plugins.php' ) ) );
            exit;
        }
    }
    
    /**
     * 플러그인 설치 (외부 플러그인은 수동 설치 안내)
     */
    private function install_plugin( $slug ) {
        // 외부 플러그인은 ZIP 파일을 업로드해야 하므로 안내 메시지 표시
        $plugin = $this->get_plugin_by_slug( $slug );
        
        if ( ! $plugin ) {
            wp_die( __( '플러그인을 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        if ( $plugin['source'] === 'external' ) {
            wp_die(
                sprintf(
                    __( '%s는 외부 플러그인입니다. 플러그인 ZIP 파일을 업로드하여 설치해주세요.', 'acf-css-really-simple-style-management-center' ),
                    $plugin['name']
                ),
                __( '플러그인 설치', 'acf-css-really-simple-style-management-center' ),
                array( 'back_link' => true )
            );
        }
        
        // WordPress.org 플러그인은 자동 설치 가능 (향후 구현)
    }
    
    /**
     * 슬러그로 플러그인 정보 가져오기
     */
    private function get_plugin_by_slug( $slug ) {
        foreach ( $this->plugins as $plugin ) {
            if ( $plugin['slug'] === $slug ) {
                return $plugin;
            }
        }
        return false;
    }
}
