<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Team Collaboration Sync
 * - Export/Import settings between team members
 * - Version tagging and changelog
 * - Conflict resolution
 * 
 * @package ACF_CSS_Manager
 * @version 22.1.5
 * @author Jenny (UX) + Jason (Implementation)
 */
class JJ_Team_Sync {
    private static $instance = null;
    private $export_history_key = 'jj_team_sync_exports';
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'wp_ajax_jj_export_settings', array( $this, 'ajax_export_settings' ) );
        add_action( 'wp_ajax_jj_import_settings', array( $this, 'ajax_import_settings' ) );
        add_action( 'wp_ajax_jj_get_export_history', array( $this, 'ajax_get_export_history' ) );
    }

    /**
     * Export settings with metadata
     */
    public function export_settings( $metadata = array() ) {
        $settings = get_option( 'jj_style_guide_settings', array() );
        
        $current_user = wp_get_current_user();
        
        $export_data = array(
            'version' => '1.0',
            'plugin_version' => defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '22.1.0',
            'exported_at' => current_time( 'mysql' ),
            'exported_by' => array(
                'user_id' => $current_user->ID,
                'user_login' => $current_user->user_login,
                'user_email' => $current_user->user_email,
                'display_name' => $current_user->display_name,
            ),
            'metadata' => array_merge( array(
                'version_tag' => '',
                'changelog' => '',
                'description' => '',
            ), $metadata ),
            'settings' => $settings,
            'site_info' => array(
                'site_name' => get_bloginfo( 'name' ),
                'site_url' => get_site_url(),
                'wp_version' => get_bloginfo( 'version' ),
            ),
        );
        
        // Record export to history
        $this->record_export( $export_data );
        
        return $export_data;
    }

    /**
     * Import settings with conflict detection
     */
    public function import_settings( $import_data, $options = array() ) {
        $defaults = array(
            'overwrite' => false,
            'merge' => true,
            'backup_before' => true,
        );
        
        $options = array_merge( $defaults, $options );
        
        // Validate import data
        if ( ! $this->validate_import_data( $import_data ) ) {
            return new WP_Error( 'invalid_data', __( '유효하지 않은 데이터입니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        // Backup current settings
        if ( $options['backup_before'] ) {
            $this->create_backup();
        }
        
        $current_settings = get_option( 'jj_style_guide_settings', array() );
        $import_settings = $import_data['settings'];
        
        // Detect conflicts
        $conflicts = $this->detect_conflicts( $current_settings, $import_settings );
        
        // Merge or overwrite
        if ( $options['merge'] && ! $options['overwrite'] ) {
            $new_settings = array_merge( $current_settings, $import_settings );
        } elseif ( $options['overwrite'] ) {
            $new_settings = $import_settings;
        } else {
            $new_settings = $current_settings;
        }
        
        // Save
        update_option( 'jj_style_guide_settings', $new_settings );
        
        return array(
            'success' => true,
            'conflicts' => $conflicts,
            'changes_count' => count( array_diff_assoc( $new_settings, $current_settings ) ),
            'metadata' => $import_data['metadata'],
        );
    }

    /**
     * Validate import data structure
     */
    private function validate_import_data( $data ) {
        if ( ! is_array( $data ) ) {
            return false;
        }
        
        $required_keys = array( 'version', 'settings', 'exported_at' );
        
        foreach ( $required_keys as $key ) {
            if ( ! isset( $data[ $key ] ) ) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Detect conflicts between current and import settings
     */
    private function detect_conflicts( $current, $import ) {
        $conflicts = array();
        
        foreach ( $import as $key => $value ) {
            if ( isset( $current[ $key ] ) && $current[ $key ] !== $value ) {
                $conflicts[] = array(
                    'key' => $key,
                    'current_value' => $current[ $key ],
                    'import_value' => $value,
                );
            }
        }
        
        return $conflicts;
    }

    /**
     * Create backup before import
     */
    private function create_backup() {
        if ( class_exists( 'JJ_History_Manager' ) ) {
            $history = JJ_History_Manager::instance();
            $history->create_snapshot( 'Before Team Import' );
        }
    }

    /**
     * Record export to history
     */
    private function record_export( $export_data ) {
        $history = get_option( $this->export_history_key, array() );
        
        array_unshift( $history, array(
            'exported_at' => $export_data['exported_at'],
            'exported_by' => $export_data['exported_by']['display_name'],
            'version_tag' => $export_data['metadata']['version_tag'],
            'description' => $export_data['metadata']['description'],
        ) );
        
        // Keep last 20 exports
        if ( count( $history ) > 20 ) {
            $history = array_slice( $history, 0, 20 );
        }
        
        update_option( $this->export_history_key, $history, false );
    }

    /**
     * AJAX: Export settings
     */
    public function ajax_export_settings() {
        check_ajax_referer( 'jj_team_sync_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $metadata = array(
            'version_tag' => isset( $_POST['version_tag'] ) ? sanitize_text_field( $_POST['version_tag'] ) : '',
            'changelog' => isset( $_POST['changelog'] ) ? sanitize_textarea_field( $_POST['changelog'] ) : '',
            'description' => isset( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '',
        );

        $export = $this->export_settings( $metadata );
        
        wp_send_json_success( array( 
            'export_data' => $export,
            'filename' => 'acf-css-settings-' . date( 'Y-m-d-His' ) . '.json'
        ) );
    }

    /**
     * AJAX: Import settings
     */
    public function ajax_import_settings() {
        check_ajax_referer( 'jj_team_sync_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $import_json = isset( $_POST['import_data'] ) ? wp_unslash( $_POST['import_data'] ) : '';
        $overwrite = isset( $_POST['overwrite'] ) && $_POST['overwrite'] === 'true';

        if ( empty( $import_json ) ) {
            wp_send_json_error( array( 'message' => __( '데이터가 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $import_data = json_decode( $import_json, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            wp_send_json_error( array( 'message' => __( 'JSON 파싱 오류', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $result = $this->import_settings( $import_data, array(
            'overwrite' => $overwrite,
            'merge' => ! $overwrite,
            'backup_before' => true,
        ) );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 
            'message' => sprintf( __( '설정을 가져왔습니다. (%d개 변경)', 'acf-css-really-simple-style-management-center' ), $result['changes_count'] ),
            'conflicts' => $result['conflicts'],
            'metadata' => $result['metadata']
        ) );
    }

    /**
     * AJAX: Get export history
     */
    public function ajax_get_export_history() {
        check_ajax_referer( 'jj_team_sync_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $history = get_option( $this->export_history_key, array() );
        
        wp_send_json_success( array( 'history' => $history ) );
    }
}
