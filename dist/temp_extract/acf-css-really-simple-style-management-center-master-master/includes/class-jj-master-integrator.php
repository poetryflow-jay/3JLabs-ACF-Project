<?php
/**
 * JJ Master Integrator - 올인원 통합 로더
 * 
 * 마스터 버전에서 모든 패밀리 플러그인의 핵심 기능을 통합 로드합니다.
 * 다른 패밀리 플러그인 설치 없이 마스터 버전 하나로 모든 기능을 사용할 수 있습니다.
 * 
 * @since 13.4.4
 * @package ACF_CSS
 * 
 * ============================================================================
 * ACF CSS 패밀리 플러그인 목록 (3J Labs)
 * ============================================================================
 * 
 * 1. ACF CSS Manager (메인 플러그인)
 *    - 약자: ACF CSS
 *    - 풀 네임: Advanced Custom Fonts & Colors & Styles Setting Manager
 *    - 기능: 색상 팔레트, 타이포그래피, 버튼, 폼 스타일 중앙 관리
 *    - 파일: acf-css-really-simple-style-guide.php
 * 
 * 2. ACF Code Snippets Box (코드 스니펫 관리자)
 *    - 약자: ACF CSB
 *    - 풀 네임: Advanced Custom Function Manager
 *    - 기능: JS, CSS, PHP, HTML 코드 스니펫 저장 및 조건부 실행
 *    - 파일: acf-code-snippets-box.php
 * 
 * 3. ACF CSS WooCommerce Toolkit (우커머스 확장)
 *    - 약자: ACF CSS WC
 *    - 풀 네임: Advanced Commerce Styling
 *    - 기능: 가격 표시, 할인 계산기, 할부 표시, 장바구니 UI
 *    - 파일: acf-css-woocommerce-toolkit.php
 * 
 * 4. ACF CSS AI Extension (AI 확장)
 *    - 약자: ACF AI
 *    - 풀 네임: AI-Powered Style Intelligence
 *    - 기능: AI 기반 스타일 추천, 색상 분석, 접근성 검사
 *    - 파일: acf-css-ai-extension.php
 * 
 * 5. ACF CSS Neural Link (라이센스 & 업데이트)
 *    - 약자: ACF NL
 *    - 풀 네임: License & Update Manager
 *    - 기능: 라이센스 인증, 자동 업데이트, 원격 제어
 *    - 파일: acf-css-neural-link.php
 * 
 * 6. ACF MBA (마케팅 자동화)
 *    - 약자: ACF MBA
 *    - 풀 네임: Advanced Custom Funnel Marketing Boosting Accelerator
 *    - 기능: 트리거 기반 넛지 마케팅, IF-DO 워크플로우
 *    - 파일: acf-nudge-flow.php
 * 
 * 7. WP Bulk Manager (대량 설치 관리)
 *    - 약자: WP BM
 *    - 풀 네임: Plugin & Theme Bulk Installer and Editor
 *    - 기능: 플러그인/테마 대량 설치, 관리
 *    - 파일: wp-bulk-installer.php
 * 
 * ============================================================================
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Master_Integrator {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 로드된 모듈 목록
     */
    private $loaded_modules = array();

    /**
     * 통합 모듈 정의
     */
    private $integrated_modules = array();

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
        // 마스터 버전에서만 실행
        if ( ! $this->is_master_edition() ) {
            return;
        }

        $this->define_integrated_modules();
        $this->init_hooks();
    }

    /**
     * 마스터 버전 여부 확인
     */
    private function is_master_edition() {
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) === 'MASTER' ) {
            return true;
        }
        if ( defined( 'JJ_STYLE_GUIDE_EDITION' ) && strtolower( JJ_STYLE_GUIDE_EDITION ) === 'master' ) {
            return true;
        }
        return false;
    }

    /**
     * 통합 모듈 정의
     * [v21.0.0] Clean Master Rollback: 타 패밀리 플러그인의 강제 통합을 제거하고 
     * ACF CSS 고유의 마스터 전용 기능 및 향후 개발 예정 기능만을 로드합니다.
     */
    private function define_integrated_modules() {
        $base_path = JJ_STYLE_GUIDE_PATH;

        $this->integrated_modules = array(
            // [Internal] 스타일 센터 고급 관리 기능
            'advanced_admin' => array(
                'name' => __( '고급 관리자 센터', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '마스터 버전 전용 고급 스타일 제어 및 시스템 최적화 도구입니다.', 'acf-css-really-simple-style-management-center' ),
                'class' => 'JJ_Master_Advanced_Admin',
                'file' => $base_path . 'includes/master-modules/class-jj-master-advanced-admin.php',
                'required' => false,
            ),
            // [Future] AI 스타일 도우미 (Internal)
            'ai_assistant' => array(
                'name' => __( 'AI 스타일 어시스턴트 (Beta)', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '자연어 명령으로 스타일을 생성하고 최적화하는 내장 AI 비서 기능입니다.', 'acf-css-really-simple-style-management-center' ),
                'class' => 'JJ_Master_AI_Assistant',
                'file' => $base_path . 'includes/master-modules/class-jj-master-ai-assistant.php',
                'required' => false,
            ),
        );
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // plugins_loaded 시점에 모듈 로드
        add_action( 'plugins_loaded', array( $this, 'load_integrated_modules' ), 15 );
        
        // 관리자 메뉴에 통합 상태 표시
        add_action( 'admin_menu', array( $this, 'add_integration_status_page' ), 99 );
        
        // REST API 엔드포인트
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
    }

    /**
     * 통합 모듈 로드
     */
    public function load_integrated_modules() {
        foreach ( $this->integrated_modules as $key => $module ) {
            // 독립 플러그인이 이미 활성화되어 있으면 스킵
            if ( ! empty( $module['standalone_check'] ) && class_exists( $module['standalone_check'] ) ) {
                $this->loaded_modules[ $key ] = array(
                    'status' => 'standalone_active',
                    'message' => sprintf( 
                        __( '%s 독립 플러그인이 활성화되어 있어 마스터 통합 모듈은 비활성화됩니다.', 'acf-css-really-simple-style-management-center' ),
                        $module['name']
                    ),
                );
                continue;
            }

            // 의존성 체크
            if ( ! empty( $module['dependency'] ) && ! class_exists( $module['dependency'] ) ) {
                $this->loaded_modules[ $key ] = array(
                    'status' => 'dependency_missing',
                    'message' => sprintf(
                        __( '%s 모듈은 %s가 필요합니다.', 'acf-css-really-simple-style-management-center' ),
                        $module['name'],
                        $module['dependency']
                    ),
                );
                continue;
            }

            // 모듈 파일 로드
            if ( file_exists( $module['file'] ) ) {
                require_once $module['file'];
                
                if ( class_exists( $module['class'] ) ) {
                    // 모듈 인스턴스화
                    call_user_func( array( $module['class'], 'instance' ) );
                    
                    $this->loaded_modules[ $key ] = array(
                        'status' => 'loaded',
                        'message' => sprintf(
                            __( '%s 모듈이 마스터 버전에 통합 로드되었습니다.', 'acf-css-really-simple-style-management-center' ),
                            $module['name']
                        ),
                    );
                } else {
                    $this->loaded_modules[ $key ] = array(
                        'status' => 'class_not_found',
                        'message' => sprintf(
                            __( '%s 모듈 클래스를 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ),
                            $module['name']
                        ),
                    );
                }
            } else {
                $this->loaded_modules[ $key ] = array(
                    'status' => 'file_not_found',
                    'message' => sprintf(
                        __( '%s 모듈 파일을 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ),
                        $module['name']
                    ),
                );
            }
        }
    }

    /**
     * 관리자 메뉴에 통합 상태 페이지 추가
     */
    public function add_integration_status_page() {
        add_submenu_page(
            'jj-admin-center',
            __( '마스터 통합 상태', 'acf-css-really-simple-style-management-center' ),
            __( '🔗 통합 상태', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            'jj-master-integration',
            array( $this, 'render_integration_status_page' )
        );
    }

    /**
     * 통합 상태 페이지 렌더링
     */
    public function render_integration_status_page() {
        ?>
        <div class="wrap jj-master-integration-wrap">
            <h1><?php esc_html_e( 'ACF CSS 마스터 버전 - 통합 모듈 상태', 'acf-css-really-simple-style-management-center' ); ?></h1>
            
            <div class="jj-integration-notice notice notice-info">
                <p>
                    <?php esc_html_e( '마스터 버전은 모든 패밀리 플러그인의 핵심 기능을 단일 플러그인에 통합합니다. 독립 플러그인을 별도로 설치할 필요가 없습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
            </div>

            <table class="widefat striped jj-integration-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( '모듈', 'acf-css-really-simple-style-management-center' ); ?></th>
                        <th><?php esc_html_e( '설명', 'acf-css-really-simple-style-management-center' ); ?></th>
                        <th><?php esc_html_e( '상태', 'acf-css-really-simple-style-management-center' ); ?></th>
                        <th><?php esc_html_e( '메시지', 'acf-css-really-simple-style-management-center' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $this->integrated_modules as $key => $module ) : 
                        $status = isset( $this->loaded_modules[ $key ] ) ? $this->loaded_modules[ $key ] : array( 'status' => 'unknown', 'message' => '' );
                        $status_class = $this->get_status_class( $status['status'] );
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $module['name'] ); ?></strong></td>
                        <td><?php echo esc_html( $module['description'] ); ?></td>
                        <td>
                            <span class="jj-status-badge <?php echo esc_attr( $status_class ); ?>">
                                <?php echo esc_html( $this->get_status_label( $status['status'] ) ); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html( $status['message'] ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <style>
                .jj-master-integration-wrap { max-width: 1200px; }
                .jj-integration-table { margin-top: 20px; }
                .jj-status-badge { 
                    display: inline-block; 
                    padding: 4px 8px; 
                    border-radius: 4px; 
                    font-size: 12px; 
                    font-weight: 600;
                }
                .jj-status-loaded { background: #d4edda; color: #155724; }
                .jj-status-standalone_active { background: #cce5ff; color: #004085; }
                .jj-status-dependency_missing { background: #fff3cd; color: #856404; }
                .jj-status-file_not_found, .jj-status-class_not_found { background: #f8d7da; color: #721c24; }
                .jj-status-unknown { background: #e2e3e5; color: #383d41; }
            </style>
        </div>
        <?php
    }

    /**
     * 상태 CSS 클래스 반환
     */
    private function get_status_class( $status ) {
        return 'jj-status-' . sanitize_html_class( $status );
    }

    /**
     * 상태 레이블 반환
     */
    private function get_status_label( $status ) {
        $labels = array(
            'loaded' => __( '✅ 로드됨', 'acf-css-really-simple-style-management-center' ),
            'standalone_active' => __( '🔄 독립 플러그인 활성', 'acf-css-really-simple-style-management-center' ),
            'dependency_missing' => __( '⚠️ 의존성 누락', 'acf-css-really-simple-style-management-center' ),
            'file_not_found' => __( '❌ 파일 없음', 'acf-css-really-simple-style-management-center' ),
            'class_not_found' => __( '❌ 클래스 없음', 'acf-css-really-simple-style-management-center' ),
            'unknown' => __( '❓ 알 수 없음', 'acf-css-really-simple-style-management-center' ),
        );
        return isset( $labels[ $status ] ) ? $labels[ $status ] : $labels['unknown'];
    }

    /**
     * REST API 라우트 등록
     */
    public function register_rest_routes() {
        register_rest_route( 'acf-css/v1', '/master-integration', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_integration_status' ),
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            },
        ) );
    }

    /**
     * REST API: 통합 상태 반환
     */
    public function rest_get_integration_status() {
        return rest_ensure_response( array(
            'is_master' => $this->is_master_edition(),
            'modules' => $this->integrated_modules,
            'loaded_modules' => $this->loaded_modules,
        ) );
    }

    /**
     * 로드된 모듈 목록 반환
     */
    public function get_loaded_modules() {
        return $this->loaded_modules;
    }

    /**
     * 특정 모듈이 로드되었는지 확인
     */
    public function is_module_loaded( $key ) {
        return isset( $this->loaded_modules[ $key ] ) && $this->loaded_modules[ $key ]['status'] === 'loaded';
    }
}

// 인스턴스 초기화
add_action( 'plugins_loaded', function() {
    JJ_Master_Integrator::instance();
}, 5 );
