<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * J&J 백업 관리자
 * - 자동 백업 (매일/주간/월간)
 * - 변경 이력 추적 및 롤백 기능
 * - 버전별 설정 비교
 * 
 * @version 3.7.0
 */
final class JJ_Backup_Manager {

    private static $instance = null;
    private $backup_option_key = 'jj_style_guide_backups';
    private $backup_history_key = 'jj_style_guide_backup_history';
    private $max_backups = 50; // 최대 백업 개수

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    /**
     * 초기화
     */
    public function init() {
        // 자동 백업 스케줄 등록
        add_action( 'admin_init', array( $this, 'schedule_auto_backups' ) );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_create_backup', array( $this, 'create_backup_ajax' ) );
        add_action( 'wp_ajax_jj_restore_backup', array( $this, 'restore_backup_ajax' ) );
        add_action( 'wp_ajax_jj_delete_backup', array( $this, 'delete_backup_ajax' ) );
        add_action( 'wp_ajax_jj_get_backup_history', array( $this, 'get_backup_history_ajax' ) );
        add_action( 'wp_ajax_jj_get_backup_list', array( $this, 'get_backup_list_ajax' ) );
        add_action( 'wp_ajax_jj_compare_backups', array( $this, 'compare_backups_ajax' ) );
        add_action( 'wp_ajax_jj_save_backup_settings', array( $this, 'save_backup_settings_ajax' ) );
        
        // 설정 변경 시 자동 백업 (선택적)
        add_action( 'update_option_' . JJ_STYLE_GUIDE_OPTIONS_KEY, array( $this, 'auto_backup_on_change' ), 10, 2 );
    }

    /**
     * 자동 백업 스케줄 등록
     */
    public function schedule_auto_backups() {
        if ( ! wp_next_scheduled( 'jj_style_guide_daily_backup' ) ) {
            wp_schedule_event( time(), 'daily', 'jj_style_guide_daily_backup' );
        }
        if ( ! wp_next_scheduled( 'jj_style_guide_weekly_backup' ) ) {
            wp_schedule_event( time(), 'weekly', 'jj_style_guide_weekly_backup' );
        }
        if ( ! wp_next_scheduled( 'jj_style_guide_monthly_backup' ) ) {
            wp_schedule_event( time(), 'monthly', 'jj_style_guide_monthly_backup' );
        }

        add_action( 'jj_style_guide_daily_backup', array( $this, 'do_auto_backup' ) );
        add_action( 'jj_style_guide_weekly_backup', array( $this, 'do_auto_backup' ) );
        add_action( 'jj_style_guide_monthly_backup', array( $this, 'do_auto_backup' ) );
    }

    /**
     * 자동 백업 실행
     */
    public function do_auto_backup() {
        $type = 'auto';
        $label = '';
        
        // 현재 실행 중인 액션에 따라 타입 결정
        $current_hook = current_action();
        if ( 'jj_style_guide_daily_backup' === $current_hook ) {
            $type = 'daily';
            $label = __( '일일 자동 백업', 'jj-style-guide' );
        } elseif ( 'jj_style_guide_weekly_backup' === $current_hook ) {
            $type = 'weekly';
            $label = __( '주간 자동 백업', 'jj-style-guide' );
        } elseif ( 'jj_style_guide_monthly_backup' === $current_hook ) {
            $type = 'monthly';
            $label = __( '월간 자동 백업', 'jj-style-guide' );
        }
        
        $this->create_backup( $type, $label );
    }

    /**
     * 설정 변경 시 자동 백업 (선택적)
     */
    public function auto_backup_on_change( $old_value, $value ) {
        // 설정 변경 자동 백업 활성화 여부 확인
        $auto_backup_enabled = get_option( 'jj_style_guide_auto_backup_on_change', false );
        if ( ! $auto_backup_enabled ) {
            return;
        }

        // 변경 사항이 있는 경우에만 백업
        if ( $old_value !== $value ) {
            $this->create_backup( 'auto', __( '설정 변경 자동 백업', 'jj-style-guide' ) );
        }
    }

    /**
     * 백업 생성
     * 
     * @param string $type 백업 타입: 'manual', 'auto', 'daily', 'weekly', 'monthly'
     * @param string $label 백업 레이블
     * @return array|false 백업 정보 또는 false
     */
    public function create_backup( $type = 'manual', $label = '' ) {
        // [v5.1.7] WordPress 함수 안전 호출
        $timestamp = function_exists( 'current_time' ) ? current_time( 'timestamp' ) : time();
        $backup_id = 'backup_' . $timestamp . '_' . ( function_exists( 'wp_generate_password' ) ? wp_generate_password( 8, false ) : substr( md5( uniqid() ), 0, 8 ) );
        
        // [v5.3.7] 배치 옵션 로드로 데이터베이스 쿼리 최적화
        $option_names = array(
            JJ_STYLE_GUIDE_OPTIONS_KEY,
            JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY,
            JJ_STYLE_GUIDE_ADMIN_TEXT_KEY,
            'jj_style_guide_section_layout',
            'jj_style_guide_admin_menu_layout',
            'jj_style_guide_admin_menu_colors',
        );
        
        $loaded_options = array();
        if ( class_exists( 'JJ_Options_Cache' ) && method_exists( 'JJ_Options_Cache', 'instance' ) ) {
            $options_cache = JJ_Options_Cache::instance();
            if ( method_exists( $options_cache, 'get_batch' ) ) {
                $loaded_options = $options_cache->get_batch( $option_names );
            }
        }
        
        // 폴백: 개별 로드
        if ( empty( $loaded_options ) && function_exists( 'get_option' ) ) {
            foreach ( $option_names as $option_name ) {
                $loaded_options[ $option_name ] = get_option( $option_name, array() );
            }
        }
        
        // 모든 설정 수집
        $settings = array(
            'style_guide'   => (array) ( isset( $loaded_options[ JJ_STYLE_GUIDE_OPTIONS_KEY ] ) ? $loaded_options[ JJ_STYLE_GUIDE_OPTIONS_KEY ] : array() ),
            'temp_palette'  => (array) ( isset( $loaded_options[ JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY ] ) ? $loaded_options[ JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY ] : array() ),
            'admin_texts'   => (array) ( isset( $loaded_options[ JJ_STYLE_GUIDE_ADMIN_TEXT_KEY ] ) ? $loaded_options[ JJ_STYLE_GUIDE_ADMIN_TEXT_KEY ] : array() ),
            'section_layout' => (array) ( isset( $loaded_options['jj_style_guide_section_layout'] ) ? $loaded_options['jj_style_guide_section_layout'] : array() ),
            'admin_menu'    => (array) ( isset( $loaded_options['jj_style_guide_admin_menu_layout'] ) ? $loaded_options['jj_style_guide_admin_menu_layout'] : array() ),
            'admin_colors'  => (array) ( isset( $loaded_options['jj_style_guide_admin_menu_colors'] ) ? $loaded_options['jj_style_guide_admin_menu_colors'] : array() ),
        );

        $backup_data = array(
            'id'          => $backup_id,
            'type'        => $type,
            'label'       => $label ?: __( '수동 백업', 'jj-style-guide' ),
            'timestamp'   => $timestamp,
            'date'        => date_i18n( 'Y-m-d H:i:s', $timestamp ),
            'user_id'     => get_current_user_id(),
            'user_name'   => wp_get_current_user()->display_name,
            'settings'    => $settings,
            'version'     => JJ_STYLE_GUIDE_VERSION,
        );

        // 백업 목록 가져오기
        $backups = (array) get_option( $this->backup_option_key, array() );
        
        // 최대 개수 초과 시 오래된 백업 삭제
        if ( count( $backups ) >= $this->max_backups ) {
            // 타입별로 오래된 백업 유지 (자동 백업은 최신 10개만 유지)
            $auto_backups = array_filter( $backups, function( $b ) {
                return in_array( $b['type'], array( 'auto', 'daily', 'weekly', 'monthly' ), true );
            } );
            
            if ( count( $auto_backups ) > 10 ) {
                // 타임스탬프로 정렬
                uasort( $auto_backups, function( $a, $b ) {
                    return (int) $a['timestamp'] <=> (int) $b['timestamp'];
                } );
                
                // 오래된 자동 백업 삭제
                $auto_backups = array_slice( $auto_backups, 0, 10, true );
                $backups = array_merge( $auto_backups, array_filter( $backups, function( $b ) {
                    return ! in_array( $b['type'], array( 'auto', 'daily', 'weekly', 'monthly' ), true );
                } ) );
            }
            
            // 그래도 최대 개수 초과 시 가장 오래된 것 삭제
            if ( count( $backups ) >= $this->max_backups ) {
                uasort( $backups, function( $a, $b ) {
                    return (int) $a['timestamp'] <=> (int) $b['timestamp'];
                } );
                $backups = array_slice( $backups, -$this->max_backups + 1, null, true );
            }
        }

        // 새 백업 추가
        $backups[ $backup_id ] = $backup_data;
        
        // 타임스탬프로 정렬 (최신순)
        uasort( $backups, function( $a, $b ) {
            return (int) $b['timestamp'] <=> (int) $a['timestamp'];
        } );

        update_option( $this->backup_option_key, $backups );

        // 변경 이력 기록
        $this->add_to_history( $backup_id, 'created', $backup_data );

        return $backup_data;
    }

    /**
     * 백업 복원
     * 
     * @param string $backup_id 백업 ID
     * @return bool 성공 여부
     */
    public function restore_backup( $backup_id ) {
        $backups = (array) get_option( $this->backup_option_key, array() );
        
        if ( ! isset( $backups[ $backup_id ] ) ) {
            return false;
        }

        $backup = $backups[ $backup_id ];
        
        if ( ! isset( $backup['settings'] ) || ! is_array( $backup['settings'] ) ) {
            return false;
        }

        $settings = $backup['settings'];

        // 복원 전 현재 설정 백업 (롤백을 위해)
        $this->create_backup( 'auto', __( '복원 전 자동 백업', 'jj-style-guide' ) );

        // [v5.3.7] 배치 옵션 설정으로 복원 성능 최적화
        $options_to_restore = array();
        
        if ( isset( $settings['style_guide'] ) ) {
            $options_to_restore[ JJ_STYLE_GUIDE_OPTIONS_KEY ] = $settings['style_guide'];
        }
        if ( isset( $settings['temp_palette'] ) ) {
            $options_to_restore[ JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY ] = $settings['temp_palette'];
        }
        if ( isset( $settings['admin_texts'] ) ) {
            $options_to_restore[ JJ_STYLE_GUIDE_ADMIN_TEXT_KEY ] = $settings['admin_texts'];
        }
        if ( isset( $settings['section_layout'] ) ) {
            $options_to_restore['jj_style_guide_section_layout'] = $settings['section_layout'];
        }
        if ( isset( $settings['admin_menu'] ) ) {
            $options_to_restore['jj_style_guide_admin_menu_layout'] = $settings['admin_menu'];
        }
        if ( isset( $settings['admin_colors'] ) ) {
            $options_to_restore['jj_style_guide_admin_menu_colors'] = $settings['admin_colors'];
        }

        // 배치 옵션 설정 사용 (성능 최적화)
        if ( ! empty( $options_to_restore ) ) {
            if ( class_exists( 'JJ_Options_Cache' ) && method_exists( 'JJ_Options_Cache', 'instance' ) ) {
                $options_cache = JJ_Options_Cache::instance();
                if ( method_exists( $options_cache, 'set_batch' ) ) {
                    $options_cache->set_batch( $options_to_restore );
                } else {
                    // 폴백: 개별 설정
                    foreach ( $options_to_restore as $option_name => $option_value ) {
                        if ( function_exists( 'update_option' ) ) {
                            update_option( $option_name, $option_value );
                        }
                    }
                }
            } else {
                // 폴백: 개별 설정
                foreach ( $options_to_restore as $option_name => $option_value ) {
                    if ( function_exists( 'update_option' ) ) {
                        update_option( $option_name, $option_value );
                    }
                }
            }
        }

        // CSS 캐시 플러시
        if ( class_exists( 'JJ_CSS_Cache' ) ) {
            JJ_CSS_Cache::instance()->flush();
        }

        // 변경 이력 기록
        $this->add_to_history( $backup_id, 'restored', $backup );

        return true;
    }

    /**
     * 백업 삭제
     * 
     * @param string $backup_id 백업 ID
     * @return bool 성공 여부
     */
    public function delete_backup( $backup_id ) {
        $backups = (array) get_option( $this->backup_option_key, array() );
        
        if ( ! isset( $backups[ $backup_id ] ) ) {
            return false;
        }

        $deleted_backup = $backups[ $backup_id ];
        unset( $backups[ $backup_id ] );

        update_option( $this->backup_option_key, $backups );

        // 변경 이력 기록
        $this->add_to_history( $backup_id, 'deleted', $deleted_backup );

        return true;
    }

    /**
     * 백업 목록 가져오기
     * 
     * @param int $limit 최대 개수
     * @param string $type 백업 타입 필터
     * @return array
     */
    public function get_backups( $limit = 0, $type = '' ) {
        $backups = (array) get_option( $this->backup_option_key, array() );
        
        // 타입 필터
        if ( ! empty( $type ) ) {
            $backups = array_filter( $backups, function( $backup ) use ( $type ) {
                return isset( $backup['type'] ) && $backup['type'] === $type;
            } );
        }

        // 타임스탬프로 정렬 (최신순)
        uasort( $backups, function( $a, $b ) {
            return (int) $b['timestamp'] <=> (int) $a['timestamp'];
        } );

        // 개수 제한
        if ( $limit > 0 ) {
            $backups = array_slice( $backups, 0, $limit, true );
        }

        return $backups;
    }

    /**
     * 변경 이력에 추가
     */
    private function add_to_history( $backup_id, $action, $data = array() ) {
        // [v5.1.7] WordPress 함수 안전 호출
        $history = (array) ( function_exists( 'get_option' ) ? get_option( $this->backup_history_key, array() ) : array() );
        
        $timestamp = function_exists( 'current_time' ) ? current_time( 'timestamp' ) : time();
        $date = function_exists( 'date_i18n' ) ? date_i18n( 'Y-m-d H:i:s' ) : date( 'Y-m-d H:i:s' );
        $user_id = function_exists( 'get_current_user_id' ) ? get_current_user_id() : 0;
        $user_name = '';
        if ( function_exists( 'wp_get_current_user' ) ) {
            $current_user = wp_get_current_user();
            $user_name = $current_user ? $current_user->display_name : '';
        }
        
        $history[] = array(
            'backup_id'  => $backup_id,
            'action'     => $action, // 'created', 'restored', 'deleted'
            'timestamp'  => $timestamp,
            'date'       => $date,
            'user_id'    => $user_id,
            'user_name'  => $user_name,
            'data'       => $data,
        );

        // 이력은 최대 200개만 유지
        if ( count( $history ) > 200 ) {
            $history = array_slice( $history, -200 );
        }

        update_option( $this->backup_history_key, $history );
    }

    /**
     * 변경 이력 가져오기
     * 
     * @param int $limit 최대 개수
     * @return array
     */
    public function get_history( $limit = 0 ) {
        $history = (array) get_option( $this->backup_history_key, array() );
        
        // 타임스탬프로 정렬 (최신순)
        usort( $history, function( $a, $b ) {
            return (int) $b['timestamp'] <=> (int) $a['timestamp'];
        } );

        if ( $limit > 0 ) {
            $history = array_slice( $history, 0, $limit );
        }

        return $history;
    }

    /**
     * 두 백업 비교
     * 
     * @param string $backup_id_1 첫 번째 백업 ID
     * @param string $backup_id_2 두 번째 백업 ID
     * @return array 차이점 목록
     */
    public function compare_backups( $backup_id_1, $backup_id_2 ) {
        $backups = (array) get_option( $this->backup_option_key, array() );
        
        if ( ! isset( $backups[ $backup_id_1 ] ) || ! isset( $backups[ $backup_id_2 ] ) ) {
            return array();
        }

        $backup1 = $backups[ $backup_id_1 ];
        $backup2 = $backups[ $backup_id_2 ];

        $settings1 = isset( $backup1['settings'] ) ? $backup1['settings'] : array();
        $settings2 = isset( $backup2['settings'] ) ? $backup2['settings'] : array();

        $differences = array();

        // 각 설정 섹션 비교
        foreach ( array( 'style_guide', 'temp_palette', 'admin_texts', 'section_layout', 'admin_menu', 'admin_colors' ) as $section ) {
            $value1 = isset( $settings1[ $section ] ) ? $settings1[ $section ] : array();
            $value2 = isset( $settings2[ $section ] ) ? $settings2[ $section ] : array();
            
            if ( $value1 !== $value2 ) {
                $differences[ $section ] = array(
                    'backup1' => $value1,
                    'backup2' => $value2,
                );
            }
        }

        return $differences;
    }

    /**
     * 백업 생성 AJAX 핸들러
     */
    public function create_backup_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }

        $type  = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'manual';
        $label = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';

        $backup = $this->create_backup( $type, $label );

        if ( $backup ) {
            wp_send_json_success( array(
                'message' => __( '백업이 생성되었습니다.', 'jj-style-guide' ),
                'backup'  => $backup,
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( '백업 생성에 실패했습니다.', 'jj-style-guide' ) ) );
        }
    }

    /**
     * 백업 복원 AJAX 핸들러
     */
    public function restore_backup_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }

        $backup_id = isset( $_POST['backup_id'] ) ? sanitize_text_field( wp_unslash( $_POST['backup_id'] ) ) : '';

        if ( empty( $backup_id ) ) {
            wp_send_json_error( array( 'message' => __( '백업 ID가 필요합니다.', 'jj-style-guide' ) ) );
        }

        $restored = $this->restore_backup( $backup_id );

        if ( $restored ) {
            wp_send_json_success( array(
                'message' => __( '백업이 복원되었습니다.', 'jj-style-guide' ),
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( '백업 복원에 실패했습니다.', 'jj-style-guide' ) ) );
        }
    }

    /**
     * 백업 삭제 AJAX 핸들러
     */
    public function delete_backup_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }

        $backup_id = isset( $_POST['backup_id'] ) ? sanitize_text_field( wp_unslash( $_POST['backup_id'] ) ) : '';

        if ( empty( $backup_id ) ) {
            wp_send_json_error( array( 'message' => __( '백업 ID가 필요합니다.', 'jj-style-guide' ) ) );
        }

        $deleted = $this->delete_backup( $backup_id );

        if ( $deleted ) {
            wp_send_json_success( array(
                'message' => __( '백업이 삭제되었습니다.', 'jj-style-guide' ),
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( '백업 삭제에 실패했습니다.', 'jj-style-guide' ) ) );
        }
    }

    /**
     * 백업 이력 가져오기 AJAX 핸들러
     */
    public function get_backup_history_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }

        $limit = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 50;

        $history = $this->get_history( $limit );

        wp_send_json_success( array(
            'history' => $history,
        ) );
    }

    /**
     * 백업 비교 AJAX 핸들러
     */
    public function compare_backups_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }

        $backup_id_1 = isset( $_POST['backup_id_1'] ) ? sanitize_text_field( wp_unslash( $_POST['backup_id_1'] ) ) : '';
        $backup_id_2 = isset( $_POST['backup_id_2'] ) ? sanitize_text_field( wp_unslash( $_POST['backup_id_2'] ) ) : '';

        if ( empty( $backup_id_1 ) || empty( $backup_id_2 ) ) {
            wp_send_json_error( array( 'message' => __( '두 백업 ID가 필요합니다.', 'jj-style-guide' ) ) );
        }

        $differences = $this->compare_backups( $backup_id_1, $backup_id_2 );

        wp_send_json_success( array(
            'differences' => $differences,
        ) );
    }

    /**
     * 백업 목록 가져오기 AJAX 핸들러
     */
    public function get_backup_list_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }

        $limit = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 50;
        $type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';

        $backups = $this->get_backups( $limit, $type );

        wp_send_json_success( array(
            'backups' => $backups,
        ) );
    }

    /**
     * 백업 설정 저장 AJAX 핸들러
     */
    public function save_backup_settings_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }

        $auto_backup_on_change = isset( $_POST['auto_backup_on_change'] ) && '1' === $_POST['auto_backup_on_change'];
        update_option( 'jj_style_guide_auto_backup_on_change', $auto_backup_on_change ? 1 : 0 );

        wp_send_json_success( array(
            'message' => __( '설정이 저장되었습니다.', 'jj-style-guide' ),
        ) );
    }
}

