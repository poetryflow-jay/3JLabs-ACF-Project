<?php
/**
 * 3J Labs Required Plugins Manager
 *
 * TGMPA를 사용하여 상호 보완 관계에 있는 플러그인들을 필수/권장 플러그인으로 등록합니다.
 *
 * [v23.0.7] 플러그인 파일명 매핑 수정 및 중복 설치 방지 로직 강화
 *
 * @package ACF_CSS
 * @version 23.0.7
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Required_Plugins {
    
    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;
    
    /**
     * 인스턴스 반환
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
        // TGMPA 라이브러리 로드
        $this->load_tgmpa();
        
        // TGMPA가 로드되었을 때만 등록
        if ( function_exists( 'tgmpa' ) ) {
            add_action( 'tgmpa_register', array( $this, 'register_required_plugins' ) );
        } else {
            // TGMPA가 없으면 커스텀 구현 사용
            $this->init_custom_tgmpa();
        }
    }
    
    /**
     * 커스텀 TGMPA 초기화
     */
    private function init_custom_tgmpa() {
        // 커스텀 TGMPA 클래스 로드
        $custom_tgmpa_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-custom-tgmpa.php';
        if ( file_exists( $custom_tgmpa_path ) ) {
            require_once $custom_tgmpa_path;
            
            // 플러그인 등록
            add_action( 'admin_init', array( $this, 'register_with_custom_tgmpa' ), 5 );
        } else {
            // 커스텀 TGMPA도 없으면 기본 알림만 표시
            add_action( 'admin_notices', array( $this, 'show_plugin_notices' ) );
        }
    }
    
    /**
     * 커스텀 TGMPA에 플러그인 등록
     */
    public function register_with_custom_tgmpa() {
        if ( class_exists( 'JJ_Custom_TGMPA' ) ) {
            $plugins = $this->get_plugins_array();
            $config = $this->get_config_array();
            
            tgmpa( $plugins, $config );
        }
    }
    
    /**
     * 플러그인 배열 반환
     */
    private function get_plugins_array() {
        return array(
            // 권장 플러그인 (필수 아님)
            array(
                'name'               => '3J 라이센스 (ACF CSS Woo License Bridge)',
                'slug'               => 'acf-css-woo-license',
                'source'             => 'external',
                'required'           => false, // 필수에서 권장으로 변경
                'version'            => '23.0.0',
                'force_activation'   => false,
                'force_deactivation' => false,
                'external_url'       => 'https://3j-labs.com',
                'is_callable'        => 'ACF_CSS_Woo_License',
            ),
            // 권장 플러그인
            array(
                'name'               => '코드 박스 (ACF Code Snippets Box)',
                'slug'               => 'acf-code-snippets-box',
                'source'             => 'external',
                'required'           => false,
                'version'            => '4.0.0',
                'force_activation'   => false,
                'force_deactivation' => false,
                'external_url'       => 'https://3j-labs.com',
                'is_callable'        => 'ACF_Code_Snippets_Box',
            ),
            array(
                'name'               => '마케팅대시보드 (3J Labs Marketing Automation Dashboard)',
                'slug'               => 'jj-marketing-automation-dashboard',
                'source'             => 'external',
                'required'           => false,
                'version'            => '2.0.0',
                'force_activation'   => false,
                'force_deactivation' => false,
                'external_url'       => 'https://3j-labs.com',
                'is_callable'        => 'JJ_Marketing_Dashboard',
            ),
            array(
                'name'               => '원 클릭 SEO (WP Bulk SEO & AEO)',
                'slug'               => 'wp-bulk-seo-aeo',
                'source'             => 'external',
                'required'           => false,
                'version'            => '2.0.0',
                'force_activation'   => false,
                'force_deactivation' => false,
                'external_url'       => 'https://3j-labs.com',
                'is_callable'        => 'WP_Bulk_SEO_AEO',
            ),
            array(
                'name'               => 'WP Bulk Manager - Really Simple WordPress Plugin & Theme Bulk Installer and Editor',
                'slug'               => 'wp-bulk-manager',
                'source'             => 'external',
                'required'           => false,
                'version'            => '23.0.0',
                'force_activation'   => false,
                'force_deactivation' => false,
                'external_url'       => 'https://3j-labs.com',
                'is_callable'        => 'JJ_Bulk_Installer',
            ),
        );
    }
    
    /**
     * 설정 배열 반환
     */
    private function get_config_array() {
        return array(
            'id'           => 'acf-css-required-plugins',
            'default_path' => '',
            'menu'         => '3j-install-plugins',
            'parent_slug'  => 'plugins.php',
            'capability'   => 'manage_options',
            'has_notices'  => true,
            'dismissable'  => true,
            'dismiss_msg'  => '',
            'is_automatic' => false,
            'message'      => '',
            'strings'      => array(
                'page_title'                      => __( '3J Labs 필수 플러그인 설치', 'acf-css-really-simple-style-management-center' ),
                'menu_title'                      => __( '필수 플러그인', 'acf-css-really-simple-style-management-center' ),
                'installing'                      => __( '플러그인 설치 중: %s', 'acf-css-really-simple-style-management-center' ),
                'updating'                        => __( '플러그인 업데이트 중: %s', 'acf-css-really-simple-style-management-center' ),
                'oops'                            => __( '문제가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
                'notice_can_install_required'     => _n_noop(
                    'ACF CSS 설정 관리자에는 다음 필수 플러그인이 필요합니다: %1$s.',
                    'ACF CSS 설정 관리자에는 다음 필수 플러그인들이 필요합니다: %1$s.',
                    'acf-css-really-simple-style-management-center'
                ),
                'notice_can_install_recommended'  => _n_noop(
                    '다음 권장 플러그인을 설치하여 더 나은 경험을 제공합니다: %1$s.',
                    '다음 권장 플러그인들을 설치하여 더 나은 경험을 제공합니다: %1$s.',
                    'acf-css-really-simple-style-management-center'
                ),
                'notice_ask_to_update'            => _n_noop(
                    '다음 플러그인을 최신 버전으로 업데이트해야 합니다: %1$s.',
                    '다음 플러그인들을 최신 버전으로 업데이트해야 합니다: %1$s.',
                    'acf-css-really-simple-style-management-center'
                ),
                'notice_ask_to_update_maybe'      => _n_noop(
                    '다음 플러그인에 대한 업데이트가 있습니다: %1$s.',
                    '다음 플러그인들에 대한 업데이트가 있습니다: %1$s.',
                    'acf-css-really-simple-style-management-center'
                ),
                'notice_can_activate_required'    => _n_noop(
                    '다음 필수 플러그인을 활성화해야 합니다: %1$s.',
                    '다음 필수 플러그인들을 활성화해야 합니다: %1$s.',
                    'acf-css-really-simple-style-management-center'
                ),
                'notice_can_activate_recommended' => _n_noop(
                    '다음 권장 플러그인을 활성화하는 것이 좋습니다: %1$s.',
                    '다음 권장 플러그인들을 활성화하는 것이 좋습니다: %1$s.',
                    'acf-css-really-simple-style-management-center'
                ),
                'install_link'                    => _n_noop(
                    '플러그인 설치 시작',
                    '플러그인 설치 시작',
                    'acf-css-really-simple-style-management-center'
                ),
                'update_link'                     => _n_noop(
                    '플러그인 업데이트 시작',
                    '플러그인 업데이트 시작',
                    'acf-css-really-simple-style-management-center'
                ),
                'activate_link'                  => _n_noop(
                    '플러그인 활성화 시작',
                    '플러그인 활성화 시작',
                    'acf-css-really-simple-style-management-center'
                ),
                'return'                         => __( '필수 플러그인 설치 페이지로 돌아가기', 'acf-css-really-simple-style-management-center' ),
                'plugin_activated'                => __( '플러그인이 활성화되었습니다.', 'acf-css-really-simple-style-management-center' ),
                'activated_successfully'          => __( '다음 플러그인이 성공적으로 활성화되었습니다:', 'acf-css-really-simple-style-management-center' ),
                'plugin_already_active'           => __( '플러그인이 이미 활성화되어 있습니다.', 'acf-css-really-simple-style-management-center' ),
                'plugin_needs_higher_version'     => __( '플러그인을 업데이트해야 합니다. 최소 버전: %s', 'acf-css-really-simple-style-management-center' ),
                'complete'                        => __( '모든 플러그인이 설치되고 활성화되었습니다. %1$s', 'acf-css-really-simple-style-management-center' ),
                'dismiss'                         => __( '이 알림 닫기', 'acf-css-really-simple-style-management-center' ),
                'notice_cannot_install_activate' => __( '플러그인을 설치하거나 활성화할 권한이 없습니다.', 'acf-css-really-simple-style-management-center' ),
                'contact_admin'                  => __( '관리자에게 문의하세요.', 'acf-css-really-simple-style-management-center' ),
                'nag_type'                        => '',
            ),
        );
    }
    
    /**
     * TGMPA 라이브러리 로드
     */
    private function load_tgmpa() {
        // TGMPA 라이브러리가 이미 로드되었는지 확인
        if ( class_exists( 'TGM_Plugin_Activation' ) || function_exists( 'tgmpa' ) ) {
            return;
        }
        
        // TGMPA 라이브러리 파일 경로
        $tgmpa_path = JJ_STYLE_GUIDE_PATH . 'includes/vendor/class-tgm-plugin-activation.php';
        
        if ( file_exists( $tgmpa_path ) ) {
            require_once $tgmpa_path;
        } else {
            // TGMPA 라이브러리가 없으면 자체 구현 사용
            $this->load_custom_tgmpa();
        }
    }
    
    /**
     * TGMPA 없이 작동하는 커스텀 구현 로드
     */
    private function load_custom_tgmpa() {
        // 커스텀 TGMPA 구현 파일이 있으면 로드
        $custom_tgmpa_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-custom-tgmpa.php';
        if ( file_exists( $custom_tgmpa_path ) ) {
            require_once $custom_tgmpa_path;
        }
    }
    
    /**
     * TGMPA가 없을 때 대체 알림 표시
     */
    public function show_plugin_notices() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        $required_plugins = $this->get_required_plugins_list();
        $missing_plugins = array();
        
        // 필수 플러그인 체크 제거 - 모든 플러그인을 권장으로 처리
        // foreach ( $required_plugins as $plugin ) {
        //     if ( $plugin['required'] && ! $this->is_plugin_active( $plugin['slug'] ) ) {
        //         $missing_plugins[] = $plugin['name'];
        //     }
        // }
        
        if ( ! empty( $missing_plugins ) ) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <strong><?php esc_html_e( 'ACF CSS 설정 관리자', 'acf-css-really-simple-style-management-center' ); ?>:</strong>
                    <?php 
                    printf(
                        esc_html__( '다음 필수 플러그인을 설치하고 활성화해주세요: %s', 'acf-css-really-simple-style-management-center' ),
                        '<strong>' . esc_html( implode( ', ', $missing_plugins ) ) . '</strong>'
                    );
                    ?>
                    <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button button-primary" style="margin-left: 10px;">
                        <?php esc_html_e( '플러그인 관리', 'acf-css-really-simple-style-management-center' ); ?>
                    </a>
                </p>
            </div>
            <?php
        }
    }
    
    /**
     * 플러그인 활성화 여부 확인
     * [v23.0.7] 플러그인 파일명 수정 및 동적 탐색 추가
     */
    private function is_plugin_active( $slug ) {
        // [v23.0.7] 올바른 플러그인 파일 경로
        $plugin_files = array(
            'acf-css-woo-license' => 'acf-css-woo-license/acf-css-woo-license.php',
            'acf-code-snippets-box' => 'acf-code-snippets-box/acf-code-snippets-box.php',
            'jj-marketing-automation-dashboard' => 'jj-marketing-automation-dashboard/jj-marketing-dashboard.php',
            'wp-bulk-seo-aeo' => 'wp-bulk-seo-aeo/wp-bulk-seo-aeo.php',
            'wp-bulk-manager' => 'wp-bulk-manager/wp-bulk-manager.php', // [v23.0.7] 수정: wp-bulk-installer.php → wp-bulk-manager.php
        );

        // [v23.0.7] 대체 파일명 (레거시 호환)
        $alt_files = array(
            'wp-bulk-manager' => 'wp-bulk-manager/wp-bulk-installer.php',
            'wp-bulk-seo-aeo' => 'SEO/wp-bulk-seo-aeo/wp-bulk-seo-aeo.php',
        );

        if ( isset( $plugin_files[ $slug ] ) ) {
            // 기본 경로로 확인
            if ( is_plugin_active( $plugin_files[ $slug ] ) ) {
                return true;
            }
            // 대체 경로로 확인
            if ( isset( $alt_files[ $slug ] ) && is_plugin_active( $alt_files[ $slug ] ) ) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * 필수 플러그인 목록 반환
     */
    private function get_required_plugins_list() {
        return array(
            array(
                'name' => '3J 라이센스 (ACF CSS Woo License Bridge)',
                'slug' => 'acf-css-woo-license',
                'required' => false, // 필수에서 권장으로 변경
            ),
            array(
                'name' => '코드 박스 (ACF Code Snippets Box)',
                'slug' => 'acf-code-snippets-box',
                'required' => false,
            ),
            array(
                'name' => '마케팅대시보드 (3J Labs Marketing Automation Dashboard)',
                'slug' => 'jj-marketing-automation-dashboard',
                'required' => false,
            ),
            array(
                'name' => '원 클릭 SEO (WP Bulk SEO & AEO)',
                'slug' => 'wp-bulk-seo-aeo',
                'required' => false,
            ),
            array(
                'name' => 'WP Bulk Manager',
                'slug' => 'wp-bulk-manager',
                'required' => false,
            ),
        );
    }
    
    /**
     * 필수/권장 플러그인 등록 (TGMPA 사용)
     */
    public function register_required_plugins() {
        if ( ! function_exists( 'tgmpa' ) ) {
            return;
        }
        /**
         * 플러그인 배열 정의
         * 각 플러그인은 다음 정보를 포함합니다:
         * - name: 플러그인 이름
         * - slug: 플러그인 슬러그 (폴더명)
         * - required: 필수 여부 (true/false)
         * - source: 플러그인 소스 ('repo' = WordPress.org, 'external' = 외부)
         * - version: 최소 버전
         * - force_activation: 강제 활성화 여부
         * - force_deactivation: 비활성화 방지 여부
         */
        $plugins = array(
            
            // 권장 플러그인 (Recommended) - 모든 플러그인을 권장으로 변경
            
            // 3J 라이센스 - 라이센스 관리 (권장)
            array(
                'name'               => '3J 라이센스 (ACF CSS Woo License Bridge)',
                'slug'               => 'acf-css-woo-license',
                'source'             => 'external',
                'required'           => false, // 필수에서 권장으로 변경
                'version'            => '23.0.0',
                'force_activation'   => false,
                'force_deactivation' => false,
                'external_url'       => 'https://3j-labs.com',
                'is_callable'        => 'ACF_CSS_Woo_License',
            ),
            
            // 권장 플러그인 (Recommended)
            
            // 코드 박스 - ACF CSS와 연동
            array(
                'name'               => '코드 박스 (ACF Code Snippets Box)',
                'slug'               => 'acf-code-snippets-box',
                'source'             => 'external',
                'required'           => false,
                'version'            => '4.0.0',
                'force_activation'   => false,
                'force_deactivation' => false,
                'external_url'       => 'https://3j-labs.com',
                'is_callable'        => 'ACF_Code_Snippets_Box',
            ),
            
            // 마케팅대시보드 - 데이터 통합
            array(
                'name'               => '마케팅대시보드 (3J Labs Marketing Automation Dashboard)',
                'slug'               => 'jj-marketing-automation-dashboard',
                'source'             => 'external',
                'required'           => false,
                'version'            => '2.0.0',
                'force_activation'   => false,
                'force_deactivation' => false,
                'external_url'       => 'https://3j-labs.com',
                'is_callable'        => 'JJ_Marketing_Dashboard',
            ),
            
            // 원 클릭 SEO - SEO 최적화
            array(
                'name'               => '원 클릭 SEO (WP Bulk SEO & AEO)',
                'slug'               => 'wp-bulk-seo-aeo',
                'source'             => 'external',
                'required'           => false,
                'version'            => '2.0.0',
                'force_activation'   => false,
                'force_deactivation' => false,
                'external_url'       => 'https://3j-labs.com',
                'is_callable'        => 'WP_Bulk_SEO_AEO',
            ),
            
            // WP Bulk Manager - 대량 관리
            array(
                'name'               => 'WP Bulk Manager - Really Simple WordPress Plugin & Theme Bulk Installer and Editor',
                'slug'               => 'wp-bulk-manager',
                'source'             => 'external',
                'required'           => false,
                'version'            => '23.0.0',
                'force_activation'   => false,
                'force_deactivation' => false,
                'external_url'       => 'https://3j-labs.com',
                'is_callable'        => 'JJ_Bulk_Installer',
            ),
        );
        
        /**
         * TGMPA 설정
         */
        $config = array(
            'id'           => 'acf-css-required-plugins',      // 고유 ID
            'default_path' => '',                              // 기본 경로 (비워두면 WordPress 플러그인 디렉토리)
            'menu'         => '3j-install-plugins',            // 메뉴 슬러그
            'parent_slug'  => 'plugins.php',                   // 부모 메뉴
            'capability'   => 'manage_options',                // 권한
            'has_notices'  => true,                            // 알림 표시
            'dismissable'  => true,                            // 알림 닫기 가능
            'dismiss_msg'  => '',                               // 알림 닫기 메시지
            'is_automatic' => false,                           // 자동 활성화 여부
            'message'      => '',                               // 사용자 정의 메시지
            'strings'      => array(
                'page_title'                      => __( '3J Labs 필수 플러그인 설치', 'acf-css-really-simple-style-management-center' ),
                'menu_title'                      => __( '필수 플러그인', 'acf-css-really-simple-style-management-center' ),
                'installing'                      => __( '플러그인 설치 중: %s', 'acf-css-really-simple-style-management-center' ),
                'updating'                        => __( '플러그인 업데이트 중: %s', 'acf-css-really-simple-style-management-center' ),
                'oops'                            => __( '문제가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
                'notice_can_install_required'     => _n_noop(
                    'ACF CSS 설정 관리자에는 다음 필수 플러그인이 필요합니다: %1$s.',
                    'ACF CSS 설정 관리자에는 다음 필수 플러그인들이 필요합니다: %1$s.',
                    'acf-css-really-simple-style-management-center'
                ),
                'notice_can_install_recommended'  => _n_noop(
                    '다음 권장 플러그인을 설치하여 더 나은 경험을 제공합니다: %1$s.',
                    '다음 권장 플러그인들을 설치하여 더 나은 경험을 제공합니다: %1$s.',
                    'acf-css-really-simple-style-management-center'
                ),
                'notice_ask_to_update'            => _n_noop(
                    '다음 플러그인을 최신 버전으로 업데이트해야 합니다: %1$s.',
                    '다음 플러그인들을 최신 버전으로 업데이트해야 합니다: %1$s.',
                    'acf-css-really-simple-style-management-center'
                ),
                'notice_ask_to_update_maybe'      => _n_noop(
                    '다음 플러그인에 대한 업데이트가 있습니다: %1$s.',
                    '다음 플러그인들에 대한 업데이트가 있습니다: %1$s.',
                    'acf-css-really-simple-style-management-center'
                ),
                'notice_can_activate_required'    => _n_noop(
                    '다음 필수 플러그인을 활성화해야 합니다: %1$s.',
                    '다음 필수 플러그인들을 활성화해야 합니다: %1$s.',
                    'acf-css-really-simple-style-management-center'
                ),
                'notice_can_activate_recommended' => _n_noop(
                    '다음 권장 플러그인을 활성화하는 것이 좋습니다: %1$s.',
                    '다음 권장 플러그인들을 활성화하는 것이 좋습니다: %1$s.',
                    'acf-css-really-simple-style-management-center'
                ),
                'install_link'                    => _n_noop(
                    '플러그인 설치 시작',
                    '플러그인 설치 시작',
                    'acf-css-really-simple-style-management-center'
                ),
                'update_link'                     => _n_noop(
                    '플러그인 업데이트 시작',
                    '플러그인 업데이트 시작',
                    'acf-css-really-simple-style-management-center'
                ),
                'activate_link'                  => _n_noop(
                    '플러그인 활성화 시작',
                    '플러그인 활성화 시작',
                    'acf-css-really-simple-style-management-center'
                ),
                'return'                         => __( '필수 플러그인 설치 페이지로 돌아가기', 'acf-css-really-simple-style-management-center' ),
                'plugin_activated'                => __( '플러그인이 활성화되었습니다.', 'acf-css-really-simple-style-management-center' ),
                'activated_successfully'          => __( '다음 플러그인이 성공적으로 활성화되었습니다:', 'acf-css-really-simple-style-management-center' ),
                'plugin_already_active'           => __( '플러그인이 이미 활성화되어 있습니다.', 'acf-css-really-simple-style-management-center' ),
                'plugin_needs_higher_version'     => __( '플러그인을 업데이트해야 합니다. 최소 버전: %s', 'acf-css-really-simple-style-management-center' ),
                'complete'                        => __( '모든 플러그인이 설치되고 활성화되었습니다. %1$s', 'acf-css-really-simple-style-management-center' ),
                'dismiss'                         => __( '이 알림 닫기', 'acf-css-really-simple-style-management-center' ),
                'notice_cannot_install_activate' => __( '플러그인을 설치하거나 활성화할 권한이 없습니다.', 'acf-css-really-simple-style-management-center' ),
                'contact_admin'                  => __( '관리자에게 문의하세요.', 'acf-css-really-simple-style-management-center' ),
                'nag_type'                        => '', // 'updated', 'update-nag', 'error'
            ),
        );
        
        tgmpa( $plugins, $config );
    }
}
