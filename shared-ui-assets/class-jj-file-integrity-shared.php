<?php
/**
 * [v25.0.0] 공유 파일 무결성 모니터 (간소화 버전)
 * 
 * @package 3J_Labs_Shared
 * @version 25.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_File_Integrity_Shared {

    private static $instances = array();
    private $plugin_path = '';
    private $plugin_slug = '';

    public static function instance( $plugin_path, $plugin_slug ) {
        $key = $plugin_slug;
        if ( ! isset( self::$instances[ $key ] ) ) {
            self::$instances[ $key ] = new self( $plugin_path, $plugin_slug );
        }
        return self::$instances[ $key ];
    }

    private function __construct( $plugin_path, $plugin_slug ) {
        $this->plugin_path = $plugin_path;
        $this->plugin_slug = $plugin_slug;
        
        $this->init_hooks();
    }

    private function init_hooks() {
        // 주기적 무결성 검사 (매일)
        if ( ! wp_next_scheduled( 'jj_file_integrity_check_' . $this->plugin_slug ) ) {
            wp_schedule_event( time(), 'daily', 'jj_file_integrity_check_' . $this->plugin_slug );
        }
        add_action( 'jj_file_integrity_check_' . $this->plugin_slug, array( $this, 'check_integrity' ) );
    }

    public function check_integrity() {
        $main_file = $this->plugin_path . basename( $this->plugin_path ) . '.php';
        
        if ( ! file_exists( $main_file ) ) {
            return;
        }

        $stored_hash = get_option( 'jj_file_integrity_hash_' . $this->plugin_slug, '' );
        $current_hash = hash_file( 'sha512', $main_file );

        if ( ! empty( $stored_hash ) && $stored_hash !== $current_hash ) {
            // 파일 변경 감지
            $this->log_violation( $main_file, $stored_hash, $current_hash );
        } else {
            // 해시 저장
            update_option( 'jj_file_integrity_hash_' . $this->plugin_slug, $current_hash );
        }
    }

    private function log_violation( $file, $old_hash, $new_hash ) {
        $logs = get_option( 'jj_file_integrity_logs_' . $this->plugin_slug, array() );
        
        $logs[] = array(
            'timestamp' => current_time( 'mysql' ),
            'file' => $file,
            'old_hash' => substr( $old_hash, 0, 16 ) . '...',
            'new_hash' => substr( $new_hash, 0, 16 ) . '...',
        );

        if ( count( $logs ) > 100 ) {
            $logs = array_slice( $logs, -100 );
        }

        update_option( 'jj_file_integrity_logs_' . $this->plugin_slug, $logs );
    }
}
