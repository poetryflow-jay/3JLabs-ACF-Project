<?php
/**
 * ACF Code Snippets Box - Nudge Marketing System
 *
 * 넛지 마케팅 시스템 - 조건 기반 메시지, 알림, 프로모션 트리거
 * 사용자 행동 유도 및 업그레이드 안내를 위한 스마트 알림 시스템
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Nudge System 클래스
 */
class ACF_CSB_Nudge_System {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 넛지 타입 상수
     */
    const TYPE_TOOLTIP     = 'tooltip';      // 툴팁 (마우스 오버)
    const TYPE_BANNER      = 'banner';       // 배너 (페이지 상단/하단)
    const TYPE_MODAL       = 'modal';        // 모달 팝업
    const TYPE_INLINE      = 'inline';       // 인라인 메시지
    const TYPE_TOAST       = 'toast';        // 토스트 알림
    const TYPE_SPOTLIGHT   = 'spotlight';    // 스포트라이트 (요소 강조)
    const TYPE_WALKTHROUGH = 'walkthrough';  // 워크스루 가이드

    /**
     * 넛지 카테고리 상수
     */
    const CAT_ONBOARDING   = 'onboarding';   // 온보딩
    const CAT_FEATURE      = 'feature';      // 기능 안내
    const CAT_UPGRADE      = 'upgrade';      // 업그레이드 유도
    const CAT_TIP          = 'tip';          // 팁/도움말
    const CAT_PROMO        = 'promo';        // 프로모션
    const CAT_WARNING      = 'warning';      // 경고/주의
    const CAT_SUCCESS      = 'success';      // 성공/완료

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
     * 초기화
     */
    public function init() {
        // 관리자 화면에서만 로드
        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
            add_action( 'admin_footer', array( $this, 'render_nudge_container' ) );
        }

        // 프론트엔드 넛지 (WooCommerce 등)
        if ( ! is_admin() && ACF_CSB_License::is_pro() ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
            add_action( 'wp_footer', array( $this, 'render_frontend_nudges' ) );
        }

        // AJAX 핸들러
        add_action( 'wp_ajax_acf_csb_dismiss_nudge', array( $this, 'ajax_dismiss_nudge' ) );
        add_action( 'wp_ajax_acf_csb_get_nudges', array( $this, 'ajax_get_nudges' ) );
        add_action( 'wp_ajax_acf_csb_track_nudge', array( $this, 'ajax_track_nudge' ) );
    }

    /**
     * 관리자 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        // ACF Code Snippets 관련 페이지에서만
        if ( strpos( $hook, 'acf-code-snippets' ) === false && 
             get_post_type() !== 'acf_code_snippet' ) {
            return;
        }

        wp_enqueue_style(
            'acf-csb-nudge-system',
            ACF_CSB_URL . 'assets/css/nudge-system.css',
            array(),
            ACF_CSB_VERSION
        );

        wp_enqueue_script(
            'acf-csb-nudge-system',
            ACF_CSB_URL . 'assets/js/nudge-system.js',
            array( 'jquery' ),
            ACF_CSB_VERSION,
            true
        );

        wp_localize_script( 'acf-csb-nudge-system', 'acfCsbNudge', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'acf_csb_nudge_nonce' ),
            'nudges'  => $this->get_active_nudges(),
            'i18n'    => array(
                'dismiss'    => __( '닫기', 'acf-code-snippets-box' ),
                'learnMore'  => __( '자세히 알아보기', 'acf-code-snippets-box' ),
                'upgrade'    => __( '업그레이드', 'acf-code-snippets-box' ),
                'next'       => __( '다음', 'acf-code-snippets-box' ),
                'prev'       => __( '이전', 'acf-code-snippets-box' ),
                'skip'       => __( '건너뛰기', 'acf-code-snippets-box' ),
                'gotIt'      => __( '알겠습니다', 'acf-code-snippets-box' ),
            ),
        ) );
    }

    /**
     * 프론트엔드 에셋 로드
     */
    public function enqueue_frontend_assets() {
        if ( ! $this->should_show_frontend_nudges() ) {
            return;
        }

        wp_enqueue_style(
            'acf-csb-nudge-frontend',
            ACF_CSB_URL . 'assets/css/nudge-frontend.css',
            array(),
            ACF_CSB_VERSION
        );

        wp_enqueue_script(
            'acf-csb-nudge-frontend',
            ACF_CSB_URL . 'assets/js/nudge-frontend.js',
            array( 'jquery' ),
            ACF_CSB_VERSION,
            true
        );
    }

    /**
     * 활성 넛지 목록 가져오기
     */
    public function get_active_nudges() {
        $all_nudges = $this->get_registered_nudges();
        $dismissed = get_user_meta( get_current_user_id(), 'acf_csb_dismissed_nudges', true );
        $dismissed = is_array( $dismissed ) ? $dismissed : array();

        $active_nudges = array();

        foreach ( $all_nudges as $nudge_id => $nudge ) {
            // 이미 닫은 넛지는 제외
            if ( in_array( $nudge_id, $dismissed, true ) ) {
                continue;
            }

            // 조건 확인
            if ( ! $this->check_nudge_conditions( $nudge ) ) {
                continue;
            }

            $active_nudges[ $nudge_id ] = $nudge;
        }

        return $active_nudges;
    }

    /**
     * 등록된 모든 넛지
     */
    public function get_registered_nudges() {
        $nudges = array(
            // ========================================
            // 온보딩 넛지
            // ========================================
            'welcome_first_snippet' => array(
                'type'        => self::TYPE_SPOTLIGHT,
                'category'    => self::CAT_ONBOARDING,
                'title'       => __( '첫 스니펫을 만들어보세요! 🎉', 'acf-code-snippets-box' ),
                'message'     => __( '새 스니펫 추가 버튼을 클릭하여 첫 번째 코드 스니펫을 만들어보세요.', 'acf-code-snippets-box' ),
                'target'      => '.page-title-action',
                'position'    => 'bottom',
                'conditions'  => array(
                    array( 'type' => 'snippet_count', 'value' => 0 ),
                ),
                'priority'    => 100,
            ),

            'condition_builder_intro' => array(
                'type'        => self::TYPE_TOOLTIP,
                'category'    => self::CAT_FEATURE,
                'title'       => __( '조건 빌더로 스마트하게! 🧠', 'acf-code-snippets-box' ),
                'message'     => __( '조건 빌더를 사용하면 특정 상황에서만 코드를 실행할 수 있습니다.', 'acf-code-snippets-box' ),
                'target'      => '#acf-csb-condition-builder',
                'position'    => 'right',
                'conditions'  => array(
                    array( 'type' => 'snippet_count', 'value' => 1, 'operator' => '>=' ),
                    array( 'type' => 'page', 'value' => 'post.php' ),
                ),
                'priority'    => 90,
            ),

            // ========================================
            // 기능 안내 넛지
            // ========================================
            'presets_library' => array(
                'type'        => self::TYPE_BANNER,
                'category'    => self::CAT_FEATURE,
                'title'       => __( '프리셋 라이브러리를 활용해보세요', 'acf-code-snippets-box' ),
                'message'     => __( '자주 사용되는 코드 스니펫이 미리 준비되어 있습니다. 한 번의 클릭으로 적용하세요!', 'acf-code-snippets-box' ),
                'cta_text'    => __( '프리셋 둘러보기', 'acf-code-snippets-box' ),
                'cta_url'     => admin_url( 'admin.php?page=acf-code-snippets-presets' ),
                'conditions'  => array(
                    array( 'type' => 'snippet_count', 'value' => 3, 'operator' => '>=' ),
                    array( 'type' => 'page', 'value' => 'acf-code-snippets' ),
                ),
                'priority'    => 70,
            ),

            // ========================================
            // 업그레이드 유도 넛지
            // ========================================
            'upgrade_snippet_limit' => array(
                'type'        => self::TYPE_MODAL,
                'category'    => self::CAT_UPGRADE,
                'title'       => __( '스니펫 한도에 도달했습니다', 'acf-code-snippets-box' ),
                'message'     => __( 'Free 버전에서는 10개의 스니펫만 사용할 수 있습니다. Pro로 업그레이드하면 무제한 스니펫과 고급 기능을 사용할 수 있습니다.', 'acf-code-snippets-box' ),
                'cta_text'    => __( 'Pro로 업그레이드', 'acf-code-snippets-box' ),
                'cta_url'     => 'https://3j-labs.com/pricing',
                'conditions'  => array(
                    array( 'type' => 'license', 'value' => 'free' ),
                    array( 'type' => 'snippet_count', 'value' => 10, 'operator' => '>=' ),
                ),
                'priority'    => 100,
                'dismissible' => false,
            ),

            'upgrade_advanced_conditions' => array(
                'type'        => self::TYPE_INLINE,
                'category'    => self::CAT_UPGRADE,
                'title'       => __( '고급 조건 사용하기', 'acf-code-snippets-box' ),
                'message'     => __( '시간, 요일, 커스텀 PHP 조건은 Pro Premium 이상에서 사용할 수 있습니다.', 'acf-code-snippets-box' ),
                'cta_text'    => __( '업그레이드하기', 'acf-code-snippets-box' ),
                'cta_url'     => 'https://3j-labs.com/pricing',
                'conditions'  => array(
                    array( 'type' => 'license', 'value' => 'basic', 'operator' => '<=' ),
                ),
                'priority'    => 60,
            ),

            // ========================================
            // 팁/도움말 넛지
            // ========================================
            'tip_acf_css_integration' => array(
                'type'        => self::TYPE_TOAST,
                'category'    => self::CAT_TIP,
                'title'       => __( '💡 ACF CSS 연동 팁', 'acf-code-snippets-box' ),
                'message'     => __( 'ACF CSS 플러그인의 스타일 변수를 스니펫에서 바로 사용할 수 있습니다!', 'acf-code-snippets-box' ),
                'conditions'  => array(
                    array( 'type' => 'plugin_active', 'value' => 'acf-css-really-simple-style-guide' ),
                    array( 'type' => 'snippet_type', 'value' => 'css' ),
                ),
                'priority'    => 40,
            ),

            'tip_daily_productivity' => array(
                'type'        => self::TYPE_TOAST,
                'category'    => self::CAT_TIP,
                'title'       => __( '오늘의 팁 💡', 'acf-code-snippets-box' ),
                'message'     => $this->get_random_tip(),
                'conditions'  => array(
                    array( 'type' => 'time_based', 'value' => 'daily' ),
                ),
                'priority'    => 20,
            ),

            // ========================================
            // WooCommerce 관련 넛지
            // ========================================
            'wc_toolkit_promo' => array(
                'type'        => self::TYPE_BANNER,
                'category'    => self::CAT_PROMO,
                'title'       => __( '🛒 WooCommerce 스니펫 강화!', 'acf-code-snippets-box' ),
                'message'     => __( 'ACF CSS WooCommerce Toolkit으로 상품 가격, 할인, 장바구니를 쉽게 커스터마이즈하세요.', 'acf-code-snippets-box' ),
                'cta_text'    => __( '자세히 보기', 'acf-code-snippets-box' ),
                'cta_url'     => 'https://3j-labs.com/woocommerce-toolkit',
                'conditions'  => array(
                    array( 'type' => 'plugin_active', 'value' => 'woocommerce' ),
                    array( 'type' => 'plugin_inactive', 'value' => 'acf-css-woocommerce-toolkit' ),
                ),
                'priority'    => 50,
            ),

            // ========================================
            // ACF 연동 넛지
            // ========================================
            'acf_integration_available' => array(
                'type'        => self::TYPE_INLINE,
                'category'    => self::CAT_FEATURE,
                'title'       => __( 'ACF 필드 연동 가능!', 'acf-code-snippets-box' ),
                'message'     => __( 'Advanced Custom Fields 플러그인이 감지되었습니다. 조건 빌더에서 ACF 필드 값을 조건으로 사용할 수 있습니다.', 'acf-code-snippets-box' ),
                'conditions'  => array(
                    array( 'type' => 'plugin_active', 'value' => 'advanced-custom-fields' ),
                ),
                'priority'    => 55,
            ),
        );

        // 필터로 추가 넛지 등록 가능
        return apply_filters( 'acf_csb_registered_nudges', $nudges );
    }

    /**
     * 넛지 조건 확인
     */
    private function check_nudge_conditions( $nudge ) {
        if ( empty( $nudge['conditions'] ) ) {
            return true;
        }

        foreach ( $nudge['conditions'] as $condition ) {
            if ( ! $this->check_single_condition( $condition ) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * 단일 조건 확인
     */
    private function check_single_condition( $condition ) {
        $type     = $condition['type'];
        $value    = $condition['value'];
        $operator = isset( $condition['operator'] ) ? $condition['operator'] : '==';

        switch ( $type ) {
            case 'license':
                $current = ACF_CSB_License::get_license_type();
                return $this->compare_values( $current, $value, $operator );

            case 'snippet_count':
                $count = wp_count_posts( 'acf_code_snippet' );
                $total = isset( $count->publish ) ? $count->publish : 0;
                return $this->compare_values( $total, $value, $operator );

            case 'page':
                global $pagenow;
                $current_page = isset( $_GET['page'] ) ? $_GET['page'] : $pagenow;
                return $current_page === $value;

            case 'plugin_active':
                return is_plugin_active( $value . '/' . $value . '.php' ) || 
                       defined( strtoupper( str_replace( '-', '_', $value ) ) . '_VERSION' );

            case 'plugin_inactive':
                return ! is_plugin_active( $value . '/' . $value . '.php' );

            case 'snippet_type':
                global $post;
                if ( $post && $post->post_type === 'acf_code_snippet' ) {
                    $snippet_type = get_post_meta( $post->ID, '_acf_csb_code_type', true );
                    return $snippet_type === $value;
                }
                return false;

            case 'time_based':
                return $this->check_time_based_condition( $value );

            default:
                return true;
        }
    }

    /**
     * 값 비교
     */
    private function compare_values( $current, $expected, $operator ) {
        switch ( $operator ) {
            case '==':
            case '=':
                return $current == $expected;
            case '!=':
            case '<>':
                return $current != $expected;
            case '>':
                return $current > $expected;
            case '>=':
                return $current >= $expected;
            case '<':
                return $current < $expected;
            case '<=':
                return $current <= $expected;
            default:
                return $current == $expected;
        }
    }

    /**
     * 시간 기반 조건 확인
     */
    private function check_time_based_condition( $frequency ) {
        $user_id = get_current_user_id();
        $last_shown = get_user_meta( $user_id, 'acf_csb_last_tip_shown', true );
        $now = current_time( 'timestamp' );

        switch ( $frequency ) {
            case 'daily':
                return empty( $last_shown ) || ( $now - $last_shown ) > DAY_IN_SECONDS;
            case 'weekly':
                return empty( $last_shown ) || ( $now - $last_shown ) > WEEK_IN_SECONDS;
            case 'monthly':
                return empty( $last_shown ) || ( $now - $last_shown ) > MONTH_IN_SECONDS;
            default:
                return true;
        }
    }

    /**
     * 랜덤 팁 가져오기
     */
    private function get_random_tip() {
        $tips = array(
            __( 'Ctrl+S로 스니펫을 빠르게 저장할 수 있습니다.', 'acf-code-snippets-box' ),
            __( 'CSS 스니펫에서 /* @import */ 주석으로 다른 스니펫을 참조할 수 있습니다.', 'acf-code-snippets-box' ),
            __( '조건 빌더로 모바일에서만 실행되는 코드를 만들 수 있습니다.', 'acf-code-snippets-box' ),
            __( '프리셋 라이브러리에 유용한 코드들이 준비되어 있습니다.', 'acf-code-snippets-box' ),
            __( 'PHP 스니펫은 보안을 위해 설정에서 활성화해야 합니다.', 'acf-code-snippets-box' ),
            __( 'ACF CSS 플러그인과 함께 사용하면 스타일 변수를 활용할 수 있습니다.', 'acf-code-snippets-box' ),
            __( '스니펫을 폴더로 정리하면 관리가 더 쉬워집니다.', 'acf-code-snippets-box' ),
            __( 'Pro 버전에서는 스니펫을 내보내기/가져오기할 수 있습니다.', 'acf-code-snippets-box' ),
        );

        return $tips[ array_rand( $tips ) ];
    }

    /**
     * 넛지 컨테이너 렌더링
     */
    public function render_nudge_container() {
        ?>
        <div id="acf-csb-nudge-container" class="acf-csb-nudge-container"></div>
        <?php
    }

    /**
     * 프론트엔드 넛지 표시 여부
     */
    private function should_show_frontend_nudges() {
        // 관리자만 프론트엔드 넛지 볼 수 있음
        return current_user_can( 'manage_options' );
    }

    /**
     * 프론트엔드 넛지 렌더링
     */
    public function render_frontend_nudges() {
        if ( ! $this->should_show_frontend_nudges() ) {
            return;
        }
        ?>
        <div id="acf-csb-frontend-nudge-container" class="acf-csb-frontend-nudge"></div>
        <?php
    }

    /**
     * AJAX: 넛지 닫기
     */
    public function ajax_dismiss_nudge() {
        check_ajax_referer( 'acf_csb_nudge_nonce', 'nonce' );

        $nudge_id = isset( $_POST['nudge_id'] ) ? sanitize_text_field( $_POST['nudge_id'] ) : '';
        
        if ( empty( $nudge_id ) ) {
            wp_send_json_error();
        }

        $user_id = get_current_user_id();
        $dismissed = get_user_meta( $user_id, 'acf_csb_dismissed_nudges', true );
        $dismissed = is_array( $dismissed ) ? $dismissed : array();
        
        if ( ! in_array( $nudge_id, $dismissed, true ) ) {
            $dismissed[] = $nudge_id;
            update_user_meta( $user_id, 'acf_csb_dismissed_nudges', $dismissed );
        }

        wp_send_json_success();
    }

    /**
     * AJAX: 넛지 목록 가져오기
     */
    public function ajax_get_nudges() {
        check_ajax_referer( 'acf_csb_nudge_nonce', 'nonce' );

        wp_send_json_success( $this->get_active_nudges() );
    }

    /**
     * AJAX: 넛지 트래킹
     */
    public function ajax_track_nudge() {
        check_ajax_referer( 'acf_csb_nudge_nonce', 'nonce' );

        $nudge_id = isset( $_POST['nudge_id'] ) ? sanitize_text_field( $_POST['nudge_id'] ) : '';
        $action   = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : 'view';

        // 트래킹 데이터 저장 (분석용)
        $tracking = get_option( 'acf_csb_nudge_tracking', array() );
        
        if ( ! isset( $tracking[ $nudge_id ] ) ) {
            $tracking[ $nudge_id ] = array(
                'views'  => 0,
                'clicks' => 0,
                'dismisses' => 0,
            );
        }

        switch ( $action ) {
            case 'view':
                $tracking[ $nudge_id ]['views']++;
                break;
            case 'click':
                $tracking[ $nudge_id ]['clicks']++;
                break;
            case 'dismiss':
                $tracking[ $nudge_id ]['dismisses']++;
                break;
        }

        update_option( 'acf_csb_nudge_tracking', $tracking );

        // 일일 팁 표시 시간 업데이트
        if ( $nudge_id === 'tip_daily_productivity' ) {
            update_user_meta( get_current_user_id(), 'acf_csb_last_tip_shown', current_time( 'timestamp' ) );
        }

        wp_send_json_success();
    }
}
