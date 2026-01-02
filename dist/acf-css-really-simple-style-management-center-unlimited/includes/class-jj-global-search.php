<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.2] Global Search
 * 
 * 전역 검색 기능 - 모든 설정, 히스토리, 빠른 액션 검색
 * 
 * @since 9.2.0
 */
class JJ_Global_Search {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_global_search', array( $this, 'ajax_global_search' ) );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        // Admin Center 및 관련 페이지에서만 로드
        if ( strpos( $hook, 'jj-admin-center' ) === false && 
             strpos( $hook, 'jj-labs-center' ) === false &&
             strpos( $hook, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'jj-global-search',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-global-search.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '9.2.0',
            true
        );

        wp_localize_script(
            'jj-global-search',
            'jjGlobalSearch',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_global_search_action' ),
                'strings'  => array(
                    'search_placeholder' => __( '검색... (예: 팔레트, 버튼, 색상)', 'acf-css-really-simple-style-management-center' ),
                    'no_results'        => __( '검색 결과가 없습니다.', 'acf-css-really-simple-style-management-center' ),
                    'searching'         => __( '검색 중...', 'acf-css-really-simple-style-management-center' ),
                ),
            )
        );
    }

    /**
     * 검색 실행
     */
    public function search( $query, $filters = array() ) {
        if ( empty( $query ) ) {
            return array();
        }

        $results = array();

        // 1. 설정 검색
        if ( ! isset( $filters['type'] ) || $filters['type'] === 'settings' ) {
            $results['settings'] = $this->search_settings( $query );
        }

        // 2. 히스토리 검색
        if ( ! isset( $filters['type'] ) || $filters['type'] === 'history' ) {
            $results['history'] = $this->search_history( $query );
        }

        // 3. 빠른 액션 검색
        if ( ! isset( $filters['type'] ) || $filters['type'] === 'actions' ) {
            $results['actions'] = $this->search_actions( $query );
        }

        return $results;
    }

    /**
     * 설정 검색
     */
    private function search_settings( $query ) {
        $results = array();
        $options = get_option( 'jj_style_guide_options', array() );
        $query_lower = strtolower( $query );

        // 팔레트 검색
        if ( isset( $options['palettes'] ) ) {
            foreach ( $options['palettes'] as $palette_key => $palette ) {
                $palette_name = isset( $palette['name'] ) ? $palette['name'] : $palette_key;
                if ( stripos( $palette_name, $query ) !== false ) {
                    $results[] = array(
                        'type'    => 'palette',
                        'title'   => $palette_name,
                        'url'     => admin_url( 'options-general.php?page=acf-css-really-simple-style-guide#colors' ),
                        'icon'    => 'dashicons-art',
                        'context' => __( '팔레트', 'acf-css-really-simple-style-management-center' ),
                    );
                }
            }
        }

        // 버튼 스타일 검색
        if ( isset( $options['buttons'] ) ) {
            foreach ( $options['buttons'] as $button_key => $button ) {
                if ( stripos( $button_key, $query ) !== false || 
                     ( isset( $button['label'] ) && stripos( $button['label'], $query ) !== false ) ) {
                    $results[] = array(
                        'type'    => 'button',
                        'title'   => isset( $button['label'] ) ? $button['label'] : $button_key,
                        'url'     => admin_url( 'options-general.php?page=acf-css-really-simple-style-guide#buttons' ),
                        'icon'    => 'dashicons-admin-appearance',
                        'context' => __( '버튼 스타일', 'acf-css-really-simple-style-management-center' ),
                    );
                }
            }
        }

        // 타이포그래피 검색
        if ( isset( $options['typography'] ) && stripos( 'typography', $query ) !== false ) {
            $results[] = array(
                'type'    => 'typography',
                'title'   => __( '타이포그래피', 'acf-css-really-simple-style-management-center' ),
                'url'     => admin_url( 'options-general.php?page=acf-css-really-simple-style-guide#typography' ),
                'icon'    => 'dashicons-editor-textcolor',
                'context' => __( '타이포그래피', 'acf-css-really-simple-style-management-center' ),
            );
        }

        return $results;
    }

    /**
     * 히스토리 검색
     */
    private function search_history( $query ) {
        $results = array();
        $recent_changes = get_option( 'jj_style_guide_recent_changes', array() );

        foreach ( $recent_changes as $change ) {
            if ( isset( $change['message'] ) && stripos( $change['message'], $query ) !== false ) {
                $results[] = array(
                    'type'    => 'history',
                    'title'   => $change['message'],
                    'time'    => isset( $change['time'] ) ? $change['time'] : '',
                    'icon'    => 'dashicons-clock',
                    'context' => __( '히스토리', 'acf-css-really-simple-style-management-center' ),
                );
            }
        }

        return $results;
    }

    /**
     * 빠른 액션 검색
     */
    private function search_actions( $query ) {
        $actions = array(
            array(
                'label' => __( '팔레트 추가', 'acf-css-really-simple-style-management-center' ),
                'url'   => admin_url( 'options-general.php?page=acf-css-really-simple-style-guide#colors' ),
            ),
            array(
                'label' => __( '버튼 스타일 편집', 'acf-css-really-simple-style-management-center' ),
                'url'   => admin_url( 'options-general.php?page=acf-css-really-simple-style-guide#buttons' ),
            ),
            array(
                'label' => __( '폼 스타일 편집', 'acf-css-really-simple-style-management-center' ),
                'url'   => admin_url( 'options-general.php?page=acf-css-really-simple-style-guide#forms' ),
            ),
            array(
                'label' => __( '백업 생성', 'acf-css-really-simple-style-management-center' ),
                'url'   => admin_url( 'options-general.php?page=jj-admin-center#backup' ),
            ),
        );

        $results = array();
        foreach ( $actions as $action ) {
            if ( stripos( $action['label'], $query ) !== false ) {
                $results[] = array(
                    'type'    => 'action',
                    'title'   => $action['label'],
                    'url'     => $action['url'],
                    'icon'    => 'dashicons-admin-generic',
                    'context' => __( '빠른 액션', 'acf-css-really-simple-style-management-center' ),
                );
            }
        }

        return $results;
    }

    /**
     * AJAX: 전역 검색
     */
    public function ajax_global_search() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_global_search_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $query = isset( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : '';
        $filters = isset( $_POST['filters'] ) ? json_decode( stripslashes( $_POST['filters'] ), true ) : array();

        if ( empty( $query ) ) {
            wp_send_json_error( array( 'message' => __( '검색어를 입력하세요.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $results = $this->search( $query, $filters );

        // 결과 정리
        $total = 0;
        $all_results = array();
        foreach ( $results as $type => $type_results ) {
            $total += count( $type_results );
            $all_results = array_merge( $all_results, $type_results );
        }

        wp_send_json_success( array(
            'results' => $all_results,
            'total'   => $total,
            'by_type' => $results,
        ) );
    }
}
