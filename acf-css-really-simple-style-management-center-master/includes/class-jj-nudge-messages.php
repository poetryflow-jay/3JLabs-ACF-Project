<?php
/**
 * JJ Nudge Messages - 넛지 메시지 정의 및 관리
 * 
 * @package ACF_CSS_Style_Guide
 * @version 23.0.4
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class JJ_Nudge_Messages
 * 
 * 넛지 메시지를 정의하고 관리하는 클래스
 */
class JJ_Nudge_Messages {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 넛지 메시지 정의
     */
    private $nudges = array();

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->init_nudges();
        $this->init_hooks();
    }

    /**
     * 넛지 메시지 초기화
     */
    private function init_nudges() {
        $this->nudges = array(
            // [v23.0.4] 환영 메시지
            'welcome' => array(
                'id' => 'welcome',
                'type' => 'info',
                'title' => __( '👋 ACF CSS 스타일 센터에 오신 것을 환영합니다!', 'acf-css-really-simple-style-management-center' ),
                'message' => __( 'Ctrl+K로 빠른 검색, ? 키로 단축키를 확인할 수 있습니다. 각 필드에 마우스를 올리면 도움말을 볼 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'top-right',
                'duration' => 5000,
                'dismissible' => true,
                'priority' => 10,
                'conditions' => array(
                    'first_visit' => true,
                ),
            ),

            // [v23.0.4] 색상 팔레트 미완성
            'incomplete_palette' => array(
                'id' => 'incomplete_palette',
                'type' => 'warning',
                'title' => __( '⚠️ 브랜드 팔레트가 완성되지 않았습니다', 'acf-css-really-simple-style-management-center' ),
                'message' => __( '주요 색상(기본, 보조, 강조)을 모두 설정하면 더 일관된 디자인을 유지할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'top-right',
                'dismissible' => true,
                'priority' => 8,
                'target' => '#jj-section-palettes',
                'actions' => array(
                    array(
                        'label' => __( '팔레트 설정하기', 'acf-css-really-simple-style-management-center' ),
                        'action' => 'navigate',
                        'url' => '#jj-section-palettes',
                    ),
                ),
                'conditions' => array(
                    'palette_incomplete' => true,
                ),
            ),

            // [v23.0.4] 타이포그래피 최적화 팁
            'typography_tip' => array(
                'id' => 'typography_tip',
                'type' => 'tip',
                'title' => __( '💡 타이포그래피 최적화 팁', 'acf-css-really-simple-style-management-center' ),
                'message' => __( '반응형 타이포그래피를 설정하면 다양한 기기에서 최적의 읽기 경험을 제공할 수 있습니다. 각 기기별로 폰트 크기를 조정해보세요.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'bottom-right',
                'dismissible' => true,
                'priority' => 5,
                'target' => '#jj-section-typography',
                'conditions' => array(
                    'typography_not_optimized' => true,
                ),
            ),

            // [v23.0.4] 프리셋 저장 권장
            'save_preset_tip' => array(
                'id' => 'save_preset_tip',
                'type' => 'tip',
                'title' => __( '💾 프리셋으로 저장하세요', 'acf-css-really-simple-style-management-center' ),
                'message' => __( '현재 설정을 프리셋으로 저장하면 나중에 한 번의 클릭으로 복원할 수 있습니다. 여러 프로젝트에서 재사용할 수 있어 시간을 절약할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'bottom-right',
                'dismissible' => true,
                'priority' => 6,
                'target' => '.jj-section-presets',
                'conditions' => array(
                    'has_unsaved_changes' => true,
                    'no_presets' => true,
                ),
            ),

            // [v23.0.4] 실시간 미리보기 안내
            'live_preview_tip' => array(
                'id' => 'live_preview_tip',
                'type' => 'info',
                'title' => __( '👁️ 실시간 미리보기', 'acf-css-really-simple-style-management-center' ),
                'message' => __( '변경사항을 저장하지 않아도 실시간으로 미리볼 수 있습니다. 저장 버튼을 누르기 전에 결과를 확인해보세요.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'top-center',
                'dismissible' => true,
                'priority' => 4,
                'conditions' => array(
                    'first_change' => true,
                ),
            ),

            // [v23.0.4] 키보드 단축키 안내
            'keyboard_shortcuts_tip' => array(
                'id' => 'keyboard_shortcuts_tip',
                'type' => 'tip',
                'title' => __( '⌨️ 키보드 단축키', 'acf-css-really-simple-style-management-center' ),
                'message' => __( '? 키를 눌러 모든 키보드 단축키를 확인할 수 있습니다. Ctrl+K로 빠른 검색도 가능합니다.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'bottom-left',
                'dismissible' => true,
                'priority' => 3,
                'conditions' => array(
                    'not_dismissed' => true,
                ),
            ),

            // [v23.0.4] Figma 연동 안내
            'figma_integration_tip' => array(
                'id' => 'figma_integration_tip',
                'type' => 'new_feature',
                'title' => __( '🎨 Figma 연동 기능', 'acf-css-really-simple-style-management-center' ),
                'message' => __( 'Figma 디자인에서 색상과 폰트를 자동으로 가져올 수 있습니다. 실험실 섹션에서 Figma 연동을 설정해보세요.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'top-right',
                'dismissible' => true,
                'priority' => 7,
                'target' => '.jj-section-labs',
                'actions' => array(
                    array(
                        'label' => __( 'Figma 연동하기', 'acf-css-really-simple-style-management-center' ),
                        'action' => 'navigate',
                        'url' => '#jj-section-labs',
                    ),
                ),
                'conditions' => array(
                    'figma_not_connected' => true,
                ),
            ),

            // [v23.0.4] 백업 권장
            'backup_recommendation' => array(
                'id' => 'backup_recommendation',
                'type' => 'optimization',
                'title' => __( '💾 자동 백업 활성화', 'acf-css-really-simple-style-management-center' ),
                'message' => __( '자동 백업을 활성화하면 설정 변경 시 자동으로 백업이 생성됩니다. 실수로 설정을 잃어버리는 것을 방지할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'bottom-right',
                'dismissible' => true,
                'priority' => 5,
                'conditions' => array(
                    'auto_backup_disabled' => true,
                ),
            ),

            // [v23.0.4] 성능 최적화 팁
            'performance_tip' => array(
                'id' => 'performance_tip',
                'type' => 'optimization',
                'title' => __( '⚡ 성능 최적화 팁', 'acf-css-really-simple-style-management-center' ),
                'message' => __( '사용하지 않는 폰트를 제거하고, CSS를 최적화하면 사이트 로딩 속도를 개선할 수 있습니다. 최적화 섹션을 확인해보세요.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'bottom-left',
                'dismissible' => true,
                'priority' => 4,
                'target' => '.jj-section-optimizer',
                'conditions' => array(
                    'unused_fonts_detected' => true,
                ),
            ),
        );
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        add_action( 'admin_footer', array( $this, 'output_nudge_config' ) );
    }

    /**
     * 넛지 메시지 가져오기
     */
    public function get_nudges() {
        return $this->nudges;
    }

    /**
     * 활성화된 넛지 가져오기 (조건 확인)
     */
    public function get_active_nudges() {
        $active_nudges = array();

        foreach ( $this->nudges as $nudge ) {
            if ( $this->check_conditions( $nudge ) ) {
                $active_nudges[] = $nudge;
            }
        }

        return $active_nudges;
    }

    /**
     * 조건 확인
     */
    private function check_conditions( $nudge ) {
        if ( ! isset( $nudge['conditions'] ) || empty( $nudge['conditions'] ) ) {
            return true;
        }

        foreach ( $nudge['conditions'] as $condition => $value ) {
            switch ( $condition ) {
                case 'first_visit':
                    if ( $value && get_user_meta( get_current_user_id(), 'jj_style_guide_welcome_shown', true ) ) {
                        return false;
                    }
                    break;

                case 'palette_incomplete':
                    if ( $value ) {
                        $options = get_option( 'jj_style_guide_options', array() );
                        $palettes = $options['palettes']['brand'] ?? array();
                        $primary = $palettes['primary'] ?? '';
                        $secondary = $palettes['secondary'] ?? '';
                        $accent = $palettes['accent'] ?? '';
                        
                        if ( empty( $primary ) || empty( $secondary ) || empty( $accent ) ) {
                            return true;
                        }
                    }
                    return false;

                case 'typography_not_optimized':
                    if ( $value ) {
                        $options = get_option( 'jj_style_guide_options', array() );
                        $typography = $options['typography'] ?? array();
                        // 반응형 설정이 없으면 최적화되지 않음
                        return empty( $typography['responsive'] );
                    }
                    return false;

                case 'has_unsaved_changes':
                    // JavaScript에서 확인
                    return true;

                case 'no_presets':
                    $options = get_option( 'jj_style_guide_options', array() );
                    $presets = $options['presets'] ?? array();
                    return empty( $presets );

                case 'first_change':
                    // JavaScript에서 확인
                    return true;

                case 'not_dismissed':
                    if ( $value ) {
                        $dismissed = get_user_meta( get_current_user_id(), 'jj_nudge_dismissed_' . $nudge['id'], true );
                        return ! $dismissed;
                    }
                    return false;

                case 'figma_not_connected':
                    if ( $value ) {
                        $options = get_option( 'jj_style_guide_options', array() );
                        $figma = $options['figma'] ?? array();
                        return empty( $figma['api_token'] );
                    }
                    return false;

                case 'auto_backup_disabled':
                    if ( $value ) {
                        $options = get_option( 'jj_style_guide_options', array() );
                        return empty( $options['auto_backup'] );
                    }
                    return false;

                case 'unused_fonts_detected':
                    // JavaScript에서 확인
                    return true;
            }
        }

        return true;
    }

    /**
     * 넛지 설정 출력 (JavaScript로 전달)
     */
    public function output_nudge_config() {
        if ( ! $this->is_style_guide_page() ) {
            return;
        }

        $active_nudges = $this->get_active_nudges();
        ?>
        <script type="text/javascript">
        (function() {
            if (typeof window.jjNudgeSystem === 'undefined') {
                window.jjNudgeSystem = {};
            }
            window.jjNudgeSystem.active_nudges = <?php echo wp_json_encode( $active_nudges, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?>;
            window.jjNudgeSystem.ajax_url = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';
            window.jjNudgeSystem.nonce = '<?php echo wp_create_nonce( 'jj_nudge_nonce' ); ?>';
            
            // [v23.0.4] 넛지 시스템 초기화 (DOM 준비 후)
            if (typeof jQuery !== 'undefined') {
                jQuery(document).ready(function($) {
                    if (typeof JJNudge !== 'undefined') {
                        // 활성 넛지가 있으면 표시
                        if (window.jjNudgeSystem.active_nudges && window.jjNudgeSystem.active_nudges.length > 0) {
                            setTimeout(function() {
                                JJNudge.showActiveNudges();
                            }, 1000);
                        }
                    }
                });
            }
        })();
        </script>
        <?php
    }

    /**
     * 스타일 가이드 페이지인지 확인
     */
    private function is_style_guide_page() {
        $screen = get_current_screen();
        if ( ! $screen ) {
            return false;
        }

        // 스타일 가이드 관련 페이지인지 확인
        return strpos( $screen->id, 'jj-style-guide' ) !== false ||
               strpos( $screen->id, 'acf-css' ) !== false ||
               ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'jj-style-guide' ) !== false );
    }
}

// 초기화
add_action( 'init', function() {
    if ( is_admin() ) {
        JJ_Nudge_Messages::instance();
    }
}, 20 );
