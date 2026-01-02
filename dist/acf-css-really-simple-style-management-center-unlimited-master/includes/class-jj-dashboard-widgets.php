<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.2] Dashboard Widgets
 * 
 * 대시보드 위젯 및 개인화 기능
 * 
 * @since 9.2.0
 */
class JJ_Dashboard_Widgets {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_init', array( $this, 'init' ) );
        add_action( 'wp_ajax_jj_save_dashboard_layout', array( $this, 'ajax_save_dashboard_layout' ) );
        add_action( 'wp_ajax_jj_toggle_favorite', array( $this, 'ajax_toggle_favorite' ) );
        add_action( 'wp_ajax_jj_get_recent_activity', array( $this, 'ajax_get_recent_activity' ) );
    }

    /**
     * 초기화
     */
    public function init() {
        // Admin Center 페이지에서만 작동
        if ( ! $this->is_admin_center_page() ) {
            return;
        }

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * Admin Center 페이지 확인
     */
    private function is_admin_center_page() {
        $screen = get_current_screen();
        return $screen && ( strpos( $screen->id, 'jj-admin-center' ) !== false );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        if ( ! $this->is_admin_center_page() ) {
            return;
        }

        wp_enqueue_script(
            'jj-dashboard-widgets',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-dashboard-widgets.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '9.2.0',
            true
        );

        wp_localize_script(
            'jj-dashboard-widgets',
            'jjDashboardWidgets',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_dashboard_widgets_action' ),
                'strings'  => array(
                    'recent_activity' => __( '최근 활동', 'acf-css-really-simple-style-management-center' ),
                    'quick_actions'   => __( '빠른 액션', 'acf-css-really-simple-style-management-center' ),
                    'statistics'      => __( '통계', 'acf-css-really-simple-style-management-center' ),
                    'favorites'       => __( '즐겨찾기', 'acf-css-really-simple-style-management-center' ),
                    'no_activity'    => __( '최근 활동이 없습니다.', 'acf-css-really-simple-style-management-center' ),
                    'add_favorite'   => __( '즐겨찾기에 추가', 'acf-css-really-simple-style-management-center' ),
                    'remove_favorite' => __( '즐겨찾기에서 제거', 'acf-css-really-simple-style-management-center' ),
                ),
            )
        );
    }

    /**
     * 최근 활동 가져오기
     */
    public function get_recent_activity( $limit = 10 ) {
        $activities = array();

        // 옵션 변경 이력 (간단한 버전)
        $recent_changes = get_option( 'jj_style_guide_recent_changes', array() );
        
        if ( ! empty( $recent_changes ) ) {
            $activities = array_slice( $recent_changes, 0, $limit );
        } else {
            // 기본 활동 데이터
            $activities = array(
                array(
                    'type'    => 'palette',
                    'action'  => 'updated',
                    'message' => __( '팔레트가 업데이트되었습니다.', 'acf-css-really-simple-style-management-center' ),
                    'time'    => current_time( 'mysql' ),
                ),
            );
        }

        return $activities;
    }

    /**
     * 통계 데이터 가져오기
     */
    public function get_statistics() {
        $options = get_option( 'jj_style_guide_options', array() );

        $stats = array(
            'palettes' => array(
                'label' => __( '팔레트', 'acf-css-really-simple-style-management-center' ),
                'count' => isset( $options['palettes'] ) ? count( $options['palettes'] ) : 0,
                'icon'  => 'dashicons-art',
            ),
            'buttons' => array(
                'label' => __( '버튼 스타일', 'acf-css-really-simple-style-management-center' ),
                'count' => isset( $options['buttons'] ) ? count( $options['buttons'] ) : 0,
                'icon'  => 'dashicons-admin-appearance',
            ),
            'forms' => array(
                'label' => __( '폼 스타일', 'acf-css-really-simple-style-management-center' ),
                'count' => isset( $options['forms'] ) ? count( $options['forms'] ) : 0,
                'icon'  => 'dashicons-edit',
            ),
            'typography' => array(
                'label' => __( '타이포그래피', 'acf-css-really-simple-style-management-center' ),
                'count' => isset( $options['typography'] ) ? 1 : 0,
                'icon'  => 'dashicons-editor-textcolor',
            ),
        );

        return $stats;
    }

    /**
     * 빠른 액션 목록 가져오기
     */
    public function get_quick_actions() {
        $actions = array(
            array(
                'label' => __( '새 팔레트 추가', 'acf-css-really-simple-style-management-center' ),
                'url'   => admin_url( 'options-general.php?page=acf-css-really-simple-style-guide#colors' ),
                'icon'  => 'dashicons-plus-alt',
            ),
            array(
                'label' => __( '스타일 가이드 편집', 'acf-css-really-simple-style-management-center' ),
                'url'   => admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ),
                'icon'  => 'dashicons-edit',
            ),
            array(
                'label' => __( '백업 생성', 'acf-css-really-simple-style-management-center' ),
                'url'   => admin_url( 'options-general.php?page=jj-admin-center#backup' ),
                'icon'  => 'dashicons-backup',
            ),
            array(
                'label' => __( '시스템 상태', 'acf-css-really-simple-style-management-center' ),
                'url'   => admin_url( 'options-general.php?page=jj-admin-center#system-status' ),
                'icon'  => 'dashicons-info',
            ),
        );

        return $actions;
    }

    /**
     * 즐겨찾기 가져오기
     */
    public function get_favorites( $user_id = null ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        return get_user_meta( $user_id, 'jj_style_guide_favorites', true ) ?: array();
    }

    /**
     * 즐겨찾기 추가/제거
     */
    public function toggle_favorite( $item_id, $item_type, $user_id = null ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $favorites = $this->get_favorites( $user_id );
        $key = $item_type . ':' . $item_id;

        if ( isset( $favorites[ $key ] ) ) {
            unset( $favorites[ $key ] );
            $added = false;
        } else {
            $favorites[ $key ] = array(
                'type' => $item_type,
                'id'   => $item_id,
                'time' => current_time( 'mysql' ),
            );
            $added = true;
        }

        update_user_meta( $user_id, 'jj_style_guide_favorites', $favorites );

        return $added;
    }

    /**
     * AJAX: 대시보드 레이아웃 저장
     */
    public function ajax_save_dashboard_layout() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_dashboard_widgets_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $user_id = get_current_user_id();
        $layout = isset( $_POST['layout'] ) ? json_decode( stripslashes( $_POST['layout'] ), true ) : array();

        if ( ! is_array( $layout ) ) {
            wp_send_json_error( array( 'message' => __( '잘못된 레이아웃 데이터입니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        update_user_meta( $user_id, 'jj_style_guide_dashboard_layout', $layout );

        wp_send_json_success( array(
            'message' => __( '레이아웃이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }

    /**
     * AJAX: 즐겨찾기 토글
     */
    public function ajax_toggle_favorite() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_dashboard_widgets_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $item_id = isset( $_POST['item_id'] ) ? sanitize_text_field( $_POST['item_id'] ) : '';
        $item_type = isset( $_POST['item_type'] ) ? sanitize_text_field( $_POST['item_type'] ) : '';

        if ( ! $item_id || ! $item_type ) {
            wp_send_json_error( array( 'message' => __( '필수 파라미터가 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $added = $this->toggle_favorite( $item_id, $item_type );

        wp_send_json_success( array(
            'added'   => $added,
            'message' => $added ? __( '즐겨찾기에 추가되었습니다.', 'acf-css-really-simple-style-management-center' ) : __( '즐겨찾기에서 제거되었습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }

    /**
     * AJAX: 최근 활동 가져오기
     */
    public function ajax_get_recent_activity() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_dashboard_widgets_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $limit = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 10;
        $activities = $this->get_recent_activity( $limit );

        // 시간 형식화
        foreach ( $activities as &$activity ) {
            if ( isset( $activity['time'] ) ) {
                $activity['time_formatted'] = JJ_Localization::format_relative_time( $activity['time'] );
            }
        }

        wp_send_json_success( array(
            'activities' => $activities,
        ) );
    }
}
