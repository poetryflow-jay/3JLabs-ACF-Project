<?php
/**
 * JJ Nudge System - 사용자 행동 유도 및 온보딩 시스템
 * 
 * 넛지(Nudge)는 사용자에게 특정 행동을 부드럽게 유도하는 알림 시스템입니다.
 * - 온보딩 가이드: 플러그인 첫 사용 시 단계별 안내
 * - 미완료 설정 알림: 필수 설정 누락 시 알림
 * - 최적화 제안: 더 나은 사용을 위한 팁 제공
 * - 새 기능 안내: 업데이트된 기능 소개
 * 
 * @since 13.2.0
 * @package JJ_Style_Guide
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Nudge System Class
 */
class JJ_Nudge_System {

    /**
     * Singleton instance
     * @var JJ_Nudge_System
     */
    private static $instance = null;

    /**
     * 옵션 키
     * @var string
     */
    private $option_key = 'jj_style_guide_nudge_settings';

    /**
     * 사용자 메타 키
     * @var string
     */
    private $user_meta_key = 'jj_style_guide_nudge_dismissed';

    /**
     * 넛지 유형 상수
     */
    const TYPE_ONBOARDING    = 'onboarding';
    const TYPE_INCOMPLETE    = 'incomplete';
    const TYPE_OPTIMIZATION  = 'optimization';
    const TYPE_NEW_FEATURE   = 'new_feature';
    const TYPE_TIP           = 'tip';

    /**
     * Singleton getter
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Private constructor
    }

    /**
     * 초기화
     */
    public function init() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_nudge_dismiss', array( $this, 'ajax_dismiss_nudge' ) );
        add_action( 'wp_ajax_jj_nudge_action', array( $this, 'ajax_nudge_action' ) );
        add_action( 'wp_ajax_jj_get_active_nudges', array( $this, 'ajax_get_active_nudges' ) );
        add_action( 'wp_ajax_jj_complete_onboarding_step', array( $this, 'ajax_complete_onboarding_step' ) );
    }

    /**
     * Assets 로드
     */
    public function enqueue_assets( $hook ) {
        // JJ 관련 페이지에서만 로드
        if ( strpos( $hook, 'jj-' ) === false && 
             strpos( $hook, 'acf-css' ) === false &&
             strpos( $hook, 'style-guide' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'jj-nudge-system',
            JJ_STYLE_GUIDE_URL . 'assets/css/jj-nudge-system.css',
            array(),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '13.2.0'
        );

        wp_enqueue_script(
            'jj-nudge-system',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-nudge-system.js',
            array( 'jquery' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '13.2.0',
            true
        );

        wp_localize_script(
            'jj-nudge-system',
            'jjNudgeSystem',
            array(
                'ajax_url'       => admin_url( 'admin-ajax.php' ),
                'nonce'          => wp_create_nonce( 'jj_nudge_system_nonce' ),
                'active_nudges'  => $this->get_active_nudges(),
                'onboarding'     => $this->get_onboarding_status(),
                'strings'        => $this->get_localized_strings(),
            )
        );
    }

    /**
     * 활성 넛지 목록 가져오기
     */
    public function get_active_nudges() {
        $nudges = array();
        $dismissed = $this->get_dismissed_nudges();

        // 온보딩 체크
        if ( ! $this->is_onboarding_complete() && ! in_array( 'onboarding', $dismissed, true ) ) {
            $nudges[] = $this->create_onboarding_nudge();
        }

        // 미완료 설정 체크
        $incomplete_nudges = $this->check_incomplete_settings();
        foreach ( $incomplete_nudges as $nudge ) {
            if ( ! in_array( $nudge['id'], $dismissed, true ) ) {
                $nudges[] = $nudge;
            }
        }

        // 최적화 제안 체크
        $optimization_nudges = $this->check_optimization_suggestions();
        foreach ( $optimization_nudges as $nudge ) {
            if ( ! in_array( $nudge['id'], $dismissed, true ) ) {
                $nudges[] = $nudge;
            }
        }

        // 새 기능 안내 체크
        $new_feature_nudges = $this->check_new_features();
        foreach ( $new_feature_nudges as $nudge ) {
            if ( ! in_array( $nudge['id'], $dismissed, true ) ) {
                $nudges[] = $nudge;
            }
        }

        // 일일 팁 (랜덤)
        $tip_nudge = $this->get_daily_tip();
        if ( $tip_nudge && ! in_array( $tip_nudge['id'], $dismissed, true ) ) {
            $nudges[] = $tip_nudge;
        }

        return $nudges;
    }

    /**
     * 온보딩 넛지 생성
     */
    private function create_onboarding_nudge() {
        $current_step = $this->get_current_onboarding_step();
        $steps = $this->get_onboarding_steps();

        return array(
            'id'          => 'onboarding',
            'type'        => self::TYPE_ONBOARDING,
            'priority'    => 100, // 최우선
            'title'       => __( '🎉 JJ CSS Premium에 오신 것을 환영합니다!', 'acf-css-really-simple-style-management-center' ),
            'message'     => sprintf(
                __( '플러그인 설정을 시작해보세요. 단계 %d/%d 진행 중입니다.', 'acf-css-really-simple-style-management-center' ),
                $current_step + 1,
                count( $steps )
            ),
            'current_step' => $current_step,
            'steps'       => $steps,
            'position'    => 'top-center',
            'dismissible' => true,
            'actions'     => array(
                array(
                    'label'  => __( '시작하기', 'acf-css-really-simple-style-management-center' ),
                    'action' => 'start_onboarding',
                    'style'  => 'primary',
                ),
                array(
                    'label'  => __( '나중에', 'acf-css-really-simple-style-management-center' ),
                    'action' => 'dismiss',
                    'style'  => 'secondary',
                ),
            ),
        );
    }

    /**
     * 온보딩 단계 정의
     */
    private function get_onboarding_steps() {
        return array(
            array(
                'id'          => 'welcome',
                'title'       => __( '환영합니다', 'acf-css-really-simple-style-management-center' ),
                'description' => __( 'JJ CSS Premium은 워드프레스 사이트의 모든 스타일을 한 곳에서 관리할 수 있게 해줍니다.', 'acf-css-really-simple-style-management-center' ),
                'icon'        => 'dashicons-admin-appearance',
                'target'      => null,
            ),
            array(
                'id'          => 'colors',
                'title'       => __( '색상 팔레트 설정', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '브랜드 색상과 시스템 색상을 설정하세요. 프리셋을 사용하면 빠르게 시작할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                'icon'        => 'dashicons-art',
                'target'      => '.jj-admin-center-tab[data-tab="colors"]',
                'action_url'  => admin_url( 'admin.php?page=jj-admin-center#colors' ),
            ),
            array(
                'id'          => 'typography',
                'title'       => __( '타이포그래피 설정', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '폰트, 크기, 굵기 등 텍스트 스타일을 설정하세요. 반응형 설정도 지원됩니다.', 'acf-css-really-simple-style-management-center' ),
                'icon'        => 'dashicons-editor-textcolor',
                'target'      => '.jj-admin-center-tab[data-tab="typography"]',
                'action_url'  => admin_url( 'admin.php?page=jj-admin-center#typography' ),
            ),
            array(
                'id'          => 'style_guide',
                'title'       => __( '스타일 가이드 생성', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '설정한 스타일로 자동 생성되는 스타일 가이드 페이지를 확인하세요.', 'acf-css-really-simple-style-management-center' ),
                'icon'        => 'dashicons-book',
                'target'      => null,
                'action_url'  => admin_url( 'admin.php?page=jj-style-guide' ),
            ),
            array(
                'id'          => 'complete',
                'title'       => __( '설정 완료!', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '기본 설정이 완료되었습니다. 이제 사이트 전체에 일관된 스타일이 적용됩니다.', 'acf-css-really-simple-style-management-center' ),
                'icon'        => 'dashicons-yes-alt',
                'target'      => null,
            ),
        );
    }

    /**
     * 미완료 설정 체크
     */
    private function check_incomplete_settings() {
        $nudges = array();

        // 색상 팔레트 설정 확인
        $color_options = get_option( 'jj_style_guide_color_options', array() );
        if ( empty( $color_options ) || empty( $color_options['palettes']['brand'] ?? array() ) ) {
            $nudges[] = array(
                'id'       => 'incomplete_colors',
                'type'     => self::TYPE_INCOMPLETE,
                'priority' => 80,
                'title'    => __( '🎨 색상 팔레트를 설정해보세요', 'acf-css-really-simple-style-management-center' ),
                'message'  => __( '브랜드 색상을 설정하면 사이트 전체에 일관된 색상이 적용됩니다.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'bottom-right',
                'actions'  => array(
                    array(
                        'label'  => __( '색상 설정하기', 'acf-css-really-simple-style-management-center' ),
                        'action' => 'navigate',
                        'url'    => admin_url( 'admin.php?page=jj-admin-center#colors' ),
                        'style'  => 'primary',
                    ),
                ),
            );
        }

        // 타이포그래피 설정 확인
        $typography_options = get_option( 'jj_style_guide_typography_options', array() );
        if ( empty( $typography_options ) || empty( $typography_options['font_family_ko'] ?? '' ) ) {
            $nudges[] = array(
                'id'       => 'incomplete_typography',
                'type'     => self::TYPE_INCOMPLETE,
                'priority' => 75,
                'title'    => __( '📝 타이포그래피를 설정해보세요', 'acf-css-really-simple-style-management-center' ),
                'message'  => __( '폰트와 텍스트 스타일을 설정하면 읽기 쉬운 사이트를 만들 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'bottom-right',
                'actions'  => array(
                    array(
                        'label'  => __( '타이포그래피 설정하기', 'acf-css-really-simple-style-management-center' ),
                        'action' => 'navigate',
                        'url'    => admin_url( 'admin.php?page=jj-admin-center#typography' ),
                        'style'  => 'primary',
                    ),
                ),
            );
        }

        return $nudges;
    }

    /**
     * 최적화 제안 체크
     */
    private function check_optimization_suggestions() {
        $nudges = array();

        // 캐시 미활성화 시
        $performance_options = get_option( 'jj_style_guide_performance_options', array() );
        if ( empty( $performance_options['css_cache_enabled'] ?? false ) ) {
            $nudges[] = array(
                'id'       => 'optimization_cache',
                'type'     => self::TYPE_OPTIMIZATION,
                'priority' => 50,
                'title'    => __( '⚡ 성능 최적화 팁', 'acf-css-really-simple-style-management-center' ),
                'message'  => __( 'CSS 캐싱을 활성화하면 페이지 로딩 속도가 향상됩니다.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'bottom-right',
                'actions'  => array(
                    array(
                        'label'  => __( '캐시 설정하기', 'acf-css-really-simple-style-management-center' ),
                        'action' => 'navigate',
                        'url'    => admin_url( 'admin.php?page=jj-admin-center#performance' ),
                        'style'  => 'primary',
                    ),
                ),
            );
        }

        // 백업 미설정 시
        $backup_options = get_option( 'jj_style_guide_backup_options', array() );
        if ( empty( $backup_options['auto_backup_enabled'] ?? false ) ) {
            $nudges[] = array(
                'id'       => 'optimization_backup',
                'type'     => self::TYPE_OPTIMIZATION,
                'priority' => 40,
                'title'    => __( '💾 자동 백업 추천', 'acf-css-really-simple-style-management-center' ),
                'message'  => __( '자동 백업을 활성화하면 설정을 안전하게 보호할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'bottom-right',
                'actions'  => array(
                    array(
                        'label'  => __( '백업 설정하기', 'acf-css-really-simple-style-management-center' ),
                        'action' => 'navigate',
                        'url'    => admin_url( 'admin.php?page=jj-admin-center#backup' ),
                        'style'  => 'primary',
                    ),
                ),
            );
        }

        return $nudges;
    }

    /**
     * 새 기능 안내 체크
     */
    private function check_new_features() {
        $nudges = array();
        $last_shown_version = get_user_meta( get_current_user_id(), 'jj_style_guide_last_feature_version', true );
        $current_version = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '13.2.0';

        // 버전 비교 (새 버전에서만 표시)
        if ( version_compare( $last_shown_version, '13.0.0', '<' ) ) {
            $nudges[] = array(
                'id'       => 'new_feature_export',
                'type'     => self::TYPE_NEW_FEATURE,
                'priority' => 60,
                'title'    => __( '🆕 새 기능: 스타일 가이드 내보내기', 'acf-css-really-simple-style-management-center' ),
                'message'  => __( 'PDF, PNG, HTML, CSS, JSON 등 다양한 형식으로 스타일 가이드를 내보낼 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'top-right',
                'actions'  => array(
                    array(
                        'label'  => __( '자세히 보기', 'acf-css-really-simple-style-management-center' ),
                        'action' => 'navigate',
                        'url'    => admin_url( 'admin.php?page=jj-style-guide' ),
                        'style'  => 'primary',
                    ),
                ),
            );

            $nudges[] = array(
                'id'       => 'new_feature_figma',
                'type'     => self::TYPE_NEW_FEATURE,
                'priority' => 55,
                'title'    => __( '🆕 새 기능: Figma 연동', 'acf-css-really-simple-style-management-center' ),
                'message'  => __( 'Figma에서 스타일을 가져오거나 WordPress 스타일을 Figma로 내보낼 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                'position' => 'top-right',
                'actions'  => array(
                    array(
                        'label'  => __( 'Figma 연동 설정', 'acf-css-really-simple-style-management-center' ),
                        'action' => 'navigate',
                        'url'    => admin_url( 'admin.php?page=jj-admin-center#figma' ),
                        'style'  => 'primary',
                    ),
                ),
            );
        }

        return $nudges;
    }

    /**
     * 일일 팁 가져오기
     */
    private function get_daily_tip() {
        $tips = array(
            array(
                'id'      => 'tip_keyboard_shortcuts',
                'message' => __( '💡 팁: Ctrl+S로 빠르게 저장하고, Ctrl+Z로 실행 취소할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
            ),
            array(
                'id'      => 'tip_presets',
                'message' => __( '💡 팁: 프리셋을 사용하면 전문가가 디자인한 색상 조합을 한 번에 적용할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
            ),
            array(
                'id'      => 'tip_live_preview',
                'message' => __( '💡 팁: 라이브 미리보기 페이지에서 실시간으로 스타일 변경을 확인할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
            ),
            array(
                'id'      => 'tip_responsive',
                'message' => __( '💡 팁: 반응형 설정을 활용하면 모든 기기에서 최적의 타이포그래피를 제공할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
            ),
            array(
                'id'      => 'tip_backup',
                'message' => __( '💡 팁: 큰 변경을 하기 전에 수동 백업을 생성하면 안전합니다.', 'acf-css-really-simple-style-management-center' ),
            ),
            array(
                'id'      => 'tip_export',
                'message' => __( '💡 팁: 스타일 가이드를 PDF로 내보내 팀원들과 공유하세요.', 'acf-css-really-simple-style-management-center' ),
            ),
            array(
                'id'      => 'tip_font_recommender',
                'message' => __( '💡 팁: 폰트 추천에서 Google Fonts를 원클릭으로 적용할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
            ),
        );

        // 오늘의 팁 선택 (날짜 기반)
        $day_of_year = intval( date( 'z' ) );
        $tip_index = $day_of_year % count( $tips );
        $today_tip = $tips[ $tip_index ];

        // 하루에 한 번만 표시
        $last_tip_date = get_user_meta( get_current_user_id(), 'jj_style_guide_last_tip_date', true );
        $today = date( 'Y-m-d' );

        if ( $last_tip_date === $today ) {
            return null; // 이미 오늘 팁을 봤음
        }

        return array(
            'id'       => $today_tip['id'],
            'type'     => self::TYPE_TIP,
            'priority' => 10,
            'title'    => __( '오늘의 팁', 'acf-css-really-simple-style-management-center' ),
            'message'  => $today_tip['message'],
            'position' => 'bottom-left',
            'actions'  => array(
                array(
                    'label'  => __( '알겠습니다', 'acf-css-really-simple-style-management-center' ),
                    'action' => 'dismiss',
                    'style'  => 'secondary',
                ),
            ),
        );
    }

    /**
     * 온보딩 완료 여부 확인
     */
    private function is_onboarding_complete() {
        $settings = get_option( $this->option_key, array() );
        return ! empty( $settings['onboarding_complete'] );
    }

    /**
     * 현재 온보딩 단계 가져오기
     */
    private function get_current_onboarding_step() {
        $settings = get_option( $this->option_key, array() );
        return intval( $settings['current_onboarding_step'] ?? 0 );
    }

    /**
     * 온보딩 상태 가져오기
     */
    public function get_onboarding_status() {
        return array(
            'complete'     => $this->is_onboarding_complete(),
            'current_step' => $this->get_current_onboarding_step(),
            'steps'        => $this->get_onboarding_steps(),
        );
    }

    /**
     * 닫은 넛지 목록 가져오기
     */
    private function get_dismissed_nudges() {
        $dismissed = get_user_meta( get_current_user_id(), $this->user_meta_key, true );
        return is_array( $dismissed ) ? $dismissed : array();
    }

    /**
     * 넛지 닫기 AJAX 핸들러
     */
    public function ajax_dismiss_nudge() {
        check_ajax_referer( 'jj_nudge_system_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $nudge_id = isset( $_POST['nudge_id'] ) ? sanitize_text_field( wp_unslash( $_POST['nudge_id'] ) ) : '';

        if ( empty( $nudge_id ) ) {
            wp_send_json_error( array( 'message' => __( '넛지 ID가 필요합니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $dismissed = $this->get_dismissed_nudges();
        if ( ! in_array( $nudge_id, $dismissed, true ) ) {
            $dismissed[] = $nudge_id;
            update_user_meta( get_current_user_id(), $this->user_meta_key, $dismissed );
        }

        // 팁인 경우 오늘 날짜 기록
        if ( strpos( $nudge_id, 'tip_' ) === 0 ) {
            update_user_meta( get_current_user_id(), 'jj_style_guide_last_tip_date', date( 'Y-m-d' ) );
        }

        wp_send_json_success( array( 'message' => __( '넛지가 닫혔습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * 넛지 액션 AJAX 핸들러
     */
    public function ajax_nudge_action() {
        check_ajax_referer( 'jj_nudge_system_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $action_type = isset( $_POST['action_type'] ) ? sanitize_text_field( wp_unslash( $_POST['action_type'] ) ) : '';
        $nudge_id = isset( $_POST['nudge_id'] ) ? sanitize_text_field( wp_unslash( $_POST['nudge_id'] ) ) : '';

        switch ( $action_type ) {
            case 'start_onboarding':
                $settings = get_option( $this->option_key, array() );
                $settings['current_onboarding_step'] = 0;
                update_option( $this->option_key, $settings );
                wp_send_json_success( array( 'action' => 'start_onboarding', 'step' => 0 ) );
                break;

            default:
                wp_send_json_error( array( 'message' => __( '알 수 없는 액션입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
    }

    /**
     * 온보딩 단계 완료 AJAX 핸들러
     */
    public function ajax_complete_onboarding_step() {
        check_ajax_referer( 'jj_nudge_system_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $step = isset( $_POST['step'] ) ? intval( $_POST['step'] ) : 0;
        $steps = $this->get_onboarding_steps();
        $settings = get_option( $this->option_key, array() );

        if ( $step >= count( $steps ) - 1 ) {
            // 마지막 단계 완료
            $settings['onboarding_complete'] = true;
            $settings['onboarding_completed_at'] = current_time( 'mysql' );
        }

        $settings['current_onboarding_step'] = min( $step + 1, count( $steps ) - 1 );
        update_option( $this->option_key, $settings );

        wp_send_json_success( array(
            'message'      => __( '단계가 완료되었습니다.', 'acf-css-really-simple-style-management-center' ),
            'current_step' => $settings['current_onboarding_step'],
            'complete'     => ! empty( $settings['onboarding_complete'] ),
        ) );
    }

    /**
     * 활성 넛지 목록 AJAX 핸들러
     */
    public function ajax_get_active_nudges() {
        check_ajax_referer( 'jj_nudge_system_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        wp_send_json_success( array(
            'nudges'     => $this->get_active_nudges(),
            'onboarding' => $this->get_onboarding_status(),
        ) );
    }

    /**
     * 다국어 문자열 가져오기
     */
    private function get_localized_strings() {
        return array(
            'dismiss'           => __( '닫기', 'acf-css-really-simple-style-management-center' ),
            'next'              => __( '다음', 'acf-css-really-simple-style-management-center' ),
            'previous'          => __( '이전', 'acf-css-really-simple-style-management-center' ),
            'skip'              => __( '건너뛰기', 'acf-css-really-simple-style-management-center' ),
            'complete'          => __( '완료', 'acf-css-really-simple-style-management-center' ),
            'step_of'           => __( '단계 %1$d / %2$d', 'acf-css-really-simple-style-management-center' ),
            'got_it'            => __( '알겠습니다', 'acf-css-really-simple-style-management-center' ),
            'loading'           => __( '로딩 중...', 'acf-css-really-simple-style-management-center' ),
            'error'             => __( '오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
        );
    }
}
