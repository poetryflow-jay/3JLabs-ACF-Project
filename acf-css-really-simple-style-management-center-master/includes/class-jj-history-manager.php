<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Auto-Rollback & History Manager
 * - Saves setting snapshots before each update
 * - Enterprise-grade recovery system
 */
class JJ_History_Manager {
    private static $instance = null;
    private $history_option_key = 'jj_style_guide_history';
    private $max_history = 10;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        add_action( 'wp_ajax_jj_rollback_settings', array( $this, 'ajax_rollback_settings' ) );
    }

    /**
     * 현재 설정을 스냅샷으로 저장
     */
    public function create_snapshot( $reason = 'Manual Save' ) {
        $current_options = get_option( 'jj_style_guide_options', array() );
        $history = get_option( $this->history_option_key, array() );

        $snapshot = array(
            'id'        => uniqid(),
            'timestamp' => current_time( 'mysql' ),
            'reason'    => $reason,
            'data'      => $current_options,
            'user'      => get_current_user_id()
        );

        array_unshift( $history, $snapshot );
        
        // 개수 제한
        if ( count( $history ) > $this->max_history ) {
            $history = array_slice( $history, 0, $this->max_history );
        }

        update_option( $this->history_option_key, $history, false );
    }

    /**
     * AJAX: 특정 버전으로 롤백
     */
    public function ajax_rollback_settings() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        $snapshot_id = isset( $_POST['snapshot_id'] ) ? sanitize_key( $_POST['snapshot_id'] ) : '';
        $history = get_option( $this->history_option_key, array() );

        $target = null;
        foreach ( $history as $snapshot ) {
            if ( $snapshot['id'] === $snapshot_id ) {
                $target = $snapshot;
                break;
            }
        }

        if ( ! $target ) {
            wp_send_json_error( array( 'message' => '스냅샷을 찾을 수 없습니다.' ) );
        }

        // 롤백 전 현재 상태를 하나 더 저장 (Undo의 Undo 가능하게)
        $this->create_snapshot( 'Pre-Rollback Backup' );

        // 복구
        update_option( 'jj_style_guide_options', $target['data'] );

        wp_send_json_success( array( 'message' => '성공적으로 롤백되었습니다!' ) );
    }

    public function get_history() {
        return get_option( $this->history_option_key, array() );
    }
}
