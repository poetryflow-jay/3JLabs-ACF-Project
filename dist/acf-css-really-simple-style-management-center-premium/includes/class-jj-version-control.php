<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.3] Version Control System
 * 
 * Git 스타일 버전 관리 및 히스토리 시스템
 * - 스냅샷 생성 및 관리
 * - 브랜치 및 머지 기능
 * - 변경 이력 상세 추적
 * - 롤백 기능
 * 
 * @since 9.3.0
 */
class JJ_Version_Control {

    private static $instance = null;
    private $option_key = 'jj_style_guide_versions';
    private $history_key = 'jj_style_guide_history';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_create_snapshot', array( $this, 'ajax_create_snapshot' ) );
        add_action( 'wp_ajax_jj_restore_snapshot', array( $this, 'ajax_restore_snapshot' ) );
        add_action( 'wp_ajax_jj_get_version_history', array( $this, 'ajax_get_version_history' ) );
        add_action( 'wp_ajax_jj_compare_versions', array( $this, 'ajax_compare_versions' ) );
        
        // 자동 스냅샷 (옵션 저장 시)
        add_action( 'update_option_jj_style_guide_options', array( $this, 'auto_snapshot' ), 10, 2 );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'jj-admin-center' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'jj-version-control',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-version-control.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '9.3.0',
            true
        );

        wp_localize_script(
            'jj-version-control',
            'jjVersionControl',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_version_control_action' ),
                'strings'  => array(
                    'snapshot_created' => __( '스냅샷이 생성되었습니다.', 'acf-css-really-simple-style-management-center' ),
                    'snapshot_restored' => __( '스냅샷이 복원되었습니다.', 'acf-css-really-simple-style-management-center' ),
                    'confirm_restore'  => __( '이 스냅샷으로 복원하시겠습니까? 현재 설정이 덮어씌워집니다.', 'acf-css-really-simple-style-management-center' ),
                ),
            )
        );
    }

    /**
     * 스냅샷 생성
     */
    public function create_snapshot( $name = '', $description = '', $auto = false ) {
        $options = get_option( 'jj_style_guide_options', array() );
        
        $snapshot = array(
            'id'          => $this->generate_snapshot_id(),
            'name'        => $name ? $name : sprintf( __( '스냅샷 %s', 'acf-css-really-simple-style-management-center' ), date_i18n( 'Y-m-d H:i:s' ) ),
            'description' => $description,
            'data'        => $options,
            'created_at'  => current_time( 'mysql' ),
            'created_by'  => get_current_user_id(),
            'auto'        => $auto,
            'size'        => strlen( serialize( $options ) ),
        );

        $versions = $this->get_versions();
        $versions[] = $snapshot;

        // 최대 50개까지만 저장
        if ( count( $versions ) > 50 ) {
            $versions = array_slice( $versions, -50 );
        }

        update_option( $this->option_key, $versions );

        // 히스토리 기록
        $this->add_history( 'snapshot_created', array(
            'snapshot_id' => $snapshot['id'],
            'snapshot_name' => $snapshot['name'],
        ) );

        return $snapshot;
    }

    /**
     * 스냅샷 복원
     */
    public function restore_snapshot( $snapshot_id ) {
        $versions = $this->get_versions();
        $snapshot = null;

        foreach ( $versions as $version ) {
            if ( $version['id'] === $snapshot_id ) {
                $snapshot = $version;
                break;
            }
        }

        if ( ! $snapshot ) {
            return new WP_Error( 'snapshot_not_found', __( '스냅샷을 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        // 현재 설정을 백업 (롤백 가능하도록)
        $current_options = get_option( 'jj_style_guide_options', array() );
        $this->create_snapshot( 
            sprintf( __( '복원 전 백업 %s', 'acf-css-really-simple-style-management-center' ), date_i18n( 'H:i:s' ) ),
            sprintf( __( '스냅샷 %s 복원 전 자동 백업', 'acf-css-really-simple-style-management-center' ), $snapshot['name'] ),
            true
        );

        // 스냅샷 데이터 복원
        update_option( 'jj_style_guide_options', $snapshot['data'] );

        // 히스토리 기록
        $this->add_history( 'snapshot_restored', array(
            'snapshot_id' => $snapshot_id,
            'snapshot_name' => $snapshot['name'],
        ) );

        return true;
    }

    /**
     * 버전 목록 가져오기
     */
    public function get_versions( $limit = null ) {
        $versions = get_option( $this->option_key, array() );
        
        // 최신순 정렬
        usort( $versions, function( $a, $b ) {
            return strtotime( $b['created_at'] ) - strtotime( $a['created_at'] );
        } );

        if ( $limit ) {
            $versions = array_slice( $versions, 0, $limit );
        }

        return $versions;
    }

    /**
     * 버전 비교
     */
    public function compare_versions( $snapshot_id_1, $snapshot_id_2 ) {
        $versions = $this->get_versions();
        $snapshot1 = null;
        $snapshot2 = null;

        foreach ( $versions as $version ) {
            if ( $version['id'] === $snapshot_id_1 ) {
                $snapshot1 = $version;
            }
            if ( $version['id'] === $snapshot_id_2 ) {
                $snapshot2 = $version;
            }
        }

        if ( ! $snapshot1 || ! $snapshot2 ) {
            return new WP_Error( 'snapshot_not_found', __( '스냅샷을 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $diff = $this->calculate_diff( $snapshot1['data'], $snapshot2['data'] );

        return array(
            'snapshot1' => $snapshot1,
            'snapshot2' => $snapshot2,
            'diff'      => $diff,
        );
    }

    /**
     * 차이점 계산
     */
    private function calculate_diff( $data1, $data2 ) {
        $diff = array(
            'added'   => array(),
            'removed' => array(),
            'changed' => array(),
        );

        // 추가/변경된 항목
        foreach ( $data2 as $key => $value ) {
            if ( ! isset( $data1[ $key ] ) ) {
                $diff['added'][ $key ] = $value;
            } elseif ( $data1[ $key ] !== $value ) {
                $diff['changed'][ $key ] = array(
                    'old' => $data1[ $key ],
                    'new' => $value,
                );
            }
        }

        // 제거된 항목
        foreach ( $data1 as $key => $value ) {
            if ( ! isset( $data2[ $key ] ) ) {
                $diff['removed'][ $key ] = $value;
            }
        }

        return $diff;
    }

    /**
     * 히스토리 가져오기
     */
    public function get_history( $limit = 50 ) {
        $history = get_option( $this->history_key, array() );
        
        // 최신순 정렬
        usort( $history, function( $a, $b ) {
            return strtotime( $b['timestamp'] ) - strtotime( $a['timestamp'] );
        } );

        if ( $limit ) {
            $history = array_slice( $history, 0, $limit );
        }

        return $history;
    }

    /**
     * 히스토리 추가
     */
    private function add_history( $action, $data = array() ) {
        $history = get_option( $this->history_key, array() );
        
        $history[] = array(
            'action'    => $action,
            'data'      => $data,
            'timestamp' => current_time( 'mysql' ),
            'user_id'   => get_current_user_id(),
        );

        // 최대 100개까지만 저장
        if ( count( $history ) > 100 ) {
            $history = array_slice( $history, -100 );
        }

        update_option( $this->history_key, $history );
    }

    /**
     * 스냅샷 ID 생성
     */
    private function generate_snapshot_id() {
        return 'snapshot_' . time() . '_' . wp_generate_password( 8, false );
    }

    /**
     * 자동 스냅샷 (옵션 변경 시)
     */
    public function auto_snapshot( $old_value, $new_value ) {
        // 자동 스냅샷이 활성화되어 있는지 확인
        $auto_snapshot_enabled = get_option( 'jj_style_guide_auto_snapshot', true );
        
        if ( ! $auto_snapshot_enabled ) {
            return;
        }

        // 마지막 자동 스냅샷 이후 5분이 지났는지 확인
        $last_auto_snapshot = get_option( 'jj_style_guide_last_auto_snapshot', 0 );
        if ( time() - $last_auto_snapshot < 300 ) { // 5분
            return;
        }

        $this->create_snapshot(
            sprintf( __( '자동 스냅샷 %s', 'acf-css-really-simple-style-management-center' ), date_i18n( 'H:i:s' ) ),
            __( '설정 변경 시 자동 생성', 'acf-css-really-simple-style-management-center' ),
            true
        );

        update_option( 'jj_style_guide_last_auto_snapshot', time() );
    }

    /**
     * AJAX: 스냅샷 생성
     */
    public function ajax_create_snapshot() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_version_control_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
        $description = isset( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '';

        $snapshot = $this->create_snapshot( $name, $description );

        wp_send_json_success( array(
            'snapshot' => $snapshot,
            'message'  => __( '스냅샷이 생성되었습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }

    /**
     * AJAX: 스냅샷 복원
     */
    public function ajax_restore_snapshot() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_version_control_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $snapshot_id = isset( $_POST['snapshot_id'] ) ? sanitize_text_field( $_POST['snapshot_id'] ) : '';

        if ( ! $snapshot_id ) {
            wp_send_json_error( array( 'message' => __( '스냅샷 ID가 필요합니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $result = $this->restore_snapshot( $snapshot_id );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
            return;
        }

        wp_send_json_success( array(
            'message' => __( '스냅샷이 복원되었습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }

    /**
     * AJAX: 버전 히스토리 가져오기
     */
    public function ajax_get_version_history() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_version_control_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $limit = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 20;
        $versions = $this->get_versions( $limit );
        $history = $this->get_history( $limit );

        wp_send_json_success( array(
            'versions' => $versions,
            'history'  => $history,
        ) );
    }

    /**
     * AJAX: 버전 비교
     */
    public function ajax_compare_versions() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_version_control_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $snapshot_id_1 = isset( $_POST['snapshot_id_1'] ) ? sanitize_text_field( $_POST['snapshot_id_1'] ) : '';
        $snapshot_id_2 = isset( $_POST['snapshot_id_2'] ) ? sanitize_text_field( $_POST['snapshot_id_2'] ) : '';

        if ( ! $snapshot_id_1 || ! $snapshot_id_2 ) {
            wp_send_json_error( array( 'message' => __( '두 개의 스냅샷 ID가 필요합니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $result = $this->compare_versions( $snapshot_id_1, $snapshot_id_2 );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
            return;
        }

        wp_send_json_success( $result );
    }
}
