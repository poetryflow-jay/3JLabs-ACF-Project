<?php
/**
 * 기존 플러그인 마이그레이션 관리 클래스
 * 
 * 기존 설치된 플러그인(특히 4.0.1 버전)의 데이터를 새 버전으로 안전하게 마이그레이션합니다.
 * 
 * @package JJ_Style_Guide
 * @since v5.3.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // 직접 접근 차단
}

/**
 * 기존 플러그인 마이그레이션 관리자 클래스
 * 
 * [v5.3.6] 신규 생성
 * - 기존 플러그인 감지 및 데이터 마이그레이션
 * - 안전한 백업 및 롤백 기능
 * - 관리자 알림 및 UI 제공
 */
class JJ_Migration_Manager {
    
    /**
     * 싱글톤 인스턴스
     * 
     * @var JJ_Migration_Manager|null
     */
    private static $instance = null;
    
    /**
     * 기존 플러그인 정보
     * 
     * @var array
     */
    private $old_plugin_info = array();
    
    /**
     * 마이그레이션 상태
     * 
     * @var array
     */
    private $migration_status = array();
    
    /**
     * 백업 데이터
     * 
     * @var array
     */
    private $backup_data = array();
    
    /**
     * 싱글톤 인스턴스 반환
     * 
     * @return JJ_Migration_Manager
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 생성자
     * 
     * [v5.3.6] WordPress가 완전히 로드된 후에만 초기화
     */
    private function __construct() {
        // WordPress가 완전히 로드된 후에만 초기화
        if ( did_action( 'plugins_loaded' ) ) {
            $this->init();
        } else {
            add_action( 'plugins_loaded', array( $this, 'init' ), 15 );
        }
    }
    
    /**
     * 초기화
     * 
     * [v5.3.6] 기존 플러그인 감지 및 마이그레이션 체크
     */
    public function init() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'is_plugin_active' ) ) {
            return;
        }
        
        // 기존 플러그인 감지
        $this->detect_old_plugin();
        
        // 기존 플러그인이 감지된 경우 마이그레이션 체크
        if ( ! empty( $this->old_plugin_info ) ) {
            // 관리자 알림 추가
            add_action( 'admin_notices', array( $this, 'show_migration_notice' ) );
            
            // AJAX 핸들러 등록
            add_action( 'wp_ajax_jj_migrate_old_plugin', array( $this, 'ajax_migrate_old_plugin' ) );
            add_action( 'wp_ajax_jj_skip_migration', array( $this, 'ajax_skip_migration' ) );
        }
    }
    
    /**
     * 기존 플러그인 감지
     * 
     * [v5.3.6] 기존 설치된 플러그인을 감지하고 정보를 수집합니다.
     * 
     * @return bool 기존 플러그인 감지 여부
     */
    private function detect_old_plugin() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        // 모든 활성화된 플러그인 목록 가져오기
        $active_plugins = get_option( 'active_plugins', array() );
        if ( empty( $active_plugins ) || ! is_array( $active_plugins ) ) {
            return false;
        }
        
        // 모든 플러그인 목록 가져오기
        $all_plugins = get_plugins();
        if ( empty( $all_plugins ) || ! is_array( $all_plugins ) ) {
            return false;
        }
        
        // 기존 플러그인 패턴들 (다양한 이름 패턴 지원)
        $old_plugin_patterns = array(
            'jj.*center.*style.*setting.*premium',  // Premium Version
            'jj.*center.*style.*setting',          // 일반 버전
            'acf.*css.*really.*simple.*style.*management.*center.*premium', // 전체 이름
        );
        
        // 활성화된 플러그인 중 기존 플러그인 찾기
        foreach ( $active_plugins as $plugin_file ) {
            // 플러그인 파일명에서 플러그인 정보 가져오기
            if ( ! isset( $all_plugins[ $plugin_file ] ) ) {
                continue;
            }
            
            $plugin_data = $all_plugins[ $plugin_file ];
            $plugin_name = isset( $plugin_data['Name'] ) ? $plugin_data['Name'] : '';
            $plugin_version = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : '';
            
            // 기존 플러그인 패턴과 매칭 확인
            foreach ( $old_plugin_patterns as $pattern ) {
                if ( preg_match( '/' . $pattern . '/i', $plugin_name ) ) {
                    // 버전 4.0.1 또는 그 이전 버전인지 확인
                    if ( ! empty( $plugin_version ) && version_compare( $plugin_version, '5.0.0', '<' ) ) {
                        $this->old_plugin_info = array(
                            'file' => $plugin_file,
                            'name' => $plugin_name,
                            'version' => $plugin_version,
                            'path' => dirname( WP_PLUGIN_DIR . '/' . $plugin_file ),
                        );
                        
                        // 기존 옵션 데이터 확인
                        $this->old_plugin_info['has_data'] = $this->check_old_plugin_data();
                        
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * 기존 플러그인 데이터 확인
     * 
     * [v5.3.6] 기존 플러그인의 옵션 데이터가 있는지 확인합니다.
     * 
     * @return bool 데이터 존재 여부
     */
    private function check_old_plugin_data() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'get_option' ) ) {
            return false;
        }
        
        // 기존 옵션 키들 (다양한 패턴 지원)
        $old_option_keys = array(
            'jj_style_guide_options',           // 표준 옵션 키
            'jj_style_guide_settings',          // 대체 옵션 키
            'jj_center_style_setting_options', // 구버전 옵션 키
        );
        
        // 각 옵션 키 확인
        foreach ( $old_option_keys as $option_key ) {
            $option_value = get_option( $option_key, false );
            if ( false !== $option_value && ! empty( $option_value ) ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 기존 플러그인 데이터 백업
     * 
     * [v5.3.6] 기존 플러그인의 모든 옵션 데이터를 백업합니다.
     * 
     * @return bool 백업 성공 여부
     */
    private function backup_old_plugin_data() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'update_option' ) ) {
            return false;
        }
        
        try {
            // 백업 데이터 초기화
            $this->backup_data = array(
                'timestamp' => function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
                'old_plugin_info' => $this->old_plugin_info,
                'options' => array(),
            );
            
            // 기존 옵션 키들
            $old_option_keys = array(
                'jj_style_guide_options',
                'jj_style_guide_temp_options',
                'jj_style_guide_admin_texts',
                'jj_style_guide_cockpit_page_id',
                'jj_style_guide_settings',
                'jj_center_style_setting_options',
            );
            
            // 각 옵션 데이터 백업
            foreach ( $old_option_keys as $option_key ) {
                $option_value = get_option( $option_key, false );
                if ( false !== $option_value ) {
                    $this->backup_data['options'][ $option_key ] = $option_value;
                }
            }
            
            // 백업 데이터를 옵션에 저장 (롤백용)
            $backup_key = 'jj_style_guide_migration_backup_' . time();
            update_option( $backup_key, $this->backup_data );
            
            // 최신 백업 키 저장 (하나만 유지)
            $previous_backup_key = get_option( 'jj_style_guide_migration_backup_key', '' );
            if ( ! empty( $previous_backup_key ) && $previous_backup_key !== $backup_key ) {
                delete_option( $previous_backup_key );
            }
            update_option( 'jj_style_guide_migration_backup_key', $backup_key );
            
            return true;
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Migration Manager: 백업 중 오류 - ' . $e->getMessage() );
            }
            return false;
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Migration Manager: 백업 중 치명적 오류 - ' . $e->getMessage() );
            }
            return false;
        }
    }
    
    /**
     * 데이터 마이그레이션 실행
     * 
     * [v5.3.6] 기존 플러그인의 데이터를 새 버전으로 마이그레이션합니다.
     * 
     * @return array 마이그레이션 결과
     */
    public function migrate_data() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'update_option' ) ) {
            return array(
                'success' => false,
                'message' => __( 'WordPress 함수를 사용할 수 없습니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }
        
        // 기존 플러그인 정보 확인
        if ( empty( $this->old_plugin_info ) ) {
            $this->detect_old_plugin();
        }
        
        if ( empty( $this->old_plugin_info ) ) {
            return array(
                'success' => false,
                'message' => __( '기존 플러그인을 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }
        
        try {
            // 1. 백업 생성
            if ( ! $this->backup_old_plugin_data() ) {
                return array(
                    'success' => false,
                    'message' => __( '백업 생성에 실패했습니다.', 'acf-css-really-simple-style-management-center' ),
                );
            }
            
            // 2. 기존 옵션 데이터 가져오기
            $old_options = array();
            $old_option_keys = array(
                'jj_style_guide_options' => 'jj_style_guide_options',
                'jj_style_guide_temp_options' => 'jj_style_guide_temp_options',
                'jj_style_guide_admin_texts' => 'jj_style_guide_admin_texts',
                'jj_style_guide_cockpit_page_id' => 'jj_style_guide_cockpit_page_id',
            );
            
            foreach ( $old_option_keys as $old_key => $new_key ) {
                $old_value = get_option( $old_key, false );
                if ( false !== $old_value ) {
                    $old_options[ $new_key ] = $old_value;
                }
            }
            
            // 3. 새 옵션 키로 데이터 마이그레이션
            $migrated_count = 0;
            foreach ( $old_options as $new_key => $value ) {
                // 새 옵션 키가 정의되어 있는지 확인
                if ( defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) && $new_key === 'jj_style_guide_options' ) {
                    $final_key = JJ_STYLE_GUIDE_OPTIONS_KEY;
                } else {
                    $final_key = $new_key;
                }
                
                // 데이터 마이그레이션 (버전 호환성 처리)
                $migrated_value = $this->migrate_option_data( $value, $this->old_plugin_info['version'] );
                
                // 새 옵션에 저장
                update_option( $final_key, $migrated_value );
                $migrated_count++;
            }
            
            // 4. 마이그레이션 완료 플래그 설정
            update_option( 'jj_style_guide_migration_completed', true );
            update_option( 'jj_style_guide_migration_from_version', $this->old_plugin_info['version'] );
            update_option( 'jj_style_guide_migration_timestamp', function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ) );
            
            // 5. 결과 반환
            return array(
                'success' => true,
                'message' => sprintf(
                    __( '성공적으로 마이그레이션되었습니다. %d개의 옵션이 마이그레이션되었습니다.', 'acf-css-really-simple-style-management-center' ),
                    $migrated_count
                ),
                'migrated_count' => $migrated_count,
            );
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Migration Manager: 마이그레이션 중 오류 - ' . $e->getMessage() );
            }
            return array(
                'success' => false,
                'message' => sprintf(
                    __( '마이그레이션 중 오류가 발생했습니다: %s', 'acf-css-really-simple-style-management-center' ),
                    $e->getMessage()
                ),
            );
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Migration Manager: 마이그레이션 중 치명적 오류 - ' . $e->getMessage() );
            }
            return array(
                'success' => false,
                'message' => sprintf(
                    __( '마이그레이션 중 치명적 오류가 발생했습니다: %s', 'acf-css-really-simple-style-management-center' ),
                    $e->getMessage()
                ),
            );
        }
    }
    
    /**
     * 옵션 데이터 마이그레이션 (버전 호환성 처리)
     * 
     * [v5.3.6] 기존 버전의 데이터 구조를 새 버전 구조로 변환합니다.
     * 
     * @param mixed $old_data 기존 데이터
     * @param string $old_version 기존 버전
     * @return mixed 마이그레이션된 데이터
     */
    private function migrate_option_data( $old_data, $old_version ) {
        // 데이터가 배열이 아닌 경우 그대로 반환
        if ( ! is_array( $old_data ) ) {
            return $old_data;
        }
        
        // 버전별 마이그레이션 로직
        if ( version_compare( $old_version, '4.0.0', '<' ) ) {
            // 4.0.0 이전 버전 마이그레이션
            return $this->migrate_from_v3( $old_data );
        } elseif ( version_compare( $old_version, '5.0.0', '<' ) ) {
            // 4.x 버전 마이그레이션
            return $this->migrate_from_v4( $old_data );
        }
        
        // 버전이 5.0.0 이상이면 그대로 반환
        return $old_data;
    }
    
    /**
     * v3.x 버전에서 마이그레이션
     * 
     * [v5.3.6] v3.x 버전의 데이터 구조를 v5.x 구조로 변환합니다.
     * 
     * @param array $old_data 기존 데이터
     * @return array 마이그레이션된 데이터
     */
    private function migrate_from_v3( $old_data ) {
        $new_data = $old_data;
        
        // v3.x에서 v5.x로의 주요 변경사항 처리
        // (필요시 구체적인 변환 로직 추가)
        
        return $new_data;
    }
    
    /**
     * v4.x 버전에서 마이그레이션
     * 
     * [v5.3.6] v4.x 버전의 데이터 구조를 v5.x 구조로 변환합니다.
     * 
     * @param array $old_data 기존 데이터
     * @return array 마이그레이션된 데이터
     */
    private function migrate_from_v4( $old_data ) {
        $new_data = $old_data;
        
        // v4.x에서 v5.x로의 주요 변경사항 처리
        // (필요시 구체적인 변환 로직 추가)
        
        // 마이그레이션 메타데이터 추가
        if ( ! isset( $new_data['_migration_meta'] ) ) {
            $new_data['_migration_meta'] = array(
                'migrated_from' => '4.x',
                'migrated_at' => function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
            );
        }
        
        return $new_data;
    }
    
    /**
     * 관리자 알림 표시
     * 
     * [v5.3.6] 기존 플러그인이 감지된 경우 관리자에게 마이그레이션 알림을 표시합니다.
     */
    public function show_migration_notice() {
        // 관리자 권한 확인
        if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        // 이미 마이그레이션이 완료된 경우 표시하지 않음
        if ( get_option( 'jj_style_guide_migration_completed', false ) ) {
            return;
        }
        
        // 마이그레이션을 건너뛴 경우 표시하지 않음
        if ( get_option( 'jj_style_guide_migration_skipped', false ) ) {
            return;
        }
        
        // 기존 플러그인 정보
        $old_plugin_name = isset( $this->old_plugin_info['name'] ) ? $this->old_plugin_info['name'] : __( '기존 플러그인', 'acf-css-really-simple-style-management-center' );
        $old_plugin_version = isset( $this->old_plugin_info['version'] ) ? $this->old_plugin_info['version'] : '';
        
        ?>
        <div class="notice notice-warning is-dismissible jj-migration-notice">
            <p>
                <strong><?php esc_html_e( 'JJ\'s Center of Style Setting: 기존 플러그인 감지', 'acf-css-really-simple-style-management-center' ); ?></strong>
            </p>
            <p>
                <?php
                printf(
                    esc_html__( '기존 플러그인 "%s" (버전 %s)이(가) 감지되었습니다. 데이터를 새 버전으로 마이그레이션하시겠습니까?', 'acf-css-really-simple-style-management-center' ),
                    esc_html( $old_plugin_name ),
                    esc_html( $old_plugin_version )
                );
                ?>
            </p>
            <p>
                <button type="button" class="button button-primary jj-migrate-button" data-nonce="<?php echo esc_attr( wp_create_nonce( 'jj_migrate_old_plugin' ) ); ?>">
                    <?php esc_html_e( '마이그레이션 실행', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
                <button type="button" class="button jj-skip-migration-button" data-nonce="<?php echo esc_attr( wp_create_nonce( 'jj_skip_migration' ) ); ?>">
                    <?php esc_html_e( '건너뛰기', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
            </p>
        </div>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.jj-migrate-button').on('click', function() {
                var button = $(this);
                var nonce = button.data('nonce');
                
                button.prop('disabled', true).text('<?php esc_html_e( '마이그레이션 중...', 'acf-css-really-simple-style-management-center' ); ?>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'jj_migrate_old_plugin',
                        nonce: nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.jj-migration-notice').removeClass('notice-warning').addClass('notice-success');
                            $('.jj-migration-notice p').first().html('<strong><?php esc_html_e( '마이그레이션 완료', 'acf-css-really-simple-style-management-center' ); ?></strong>');
                            $('.jj-migration-notice p').eq(1).text(response.data.message);
                            $('.jj-migration-notice p').last().remove();
                        } else {
                            $('.jj-migration-notice').removeClass('notice-warning').addClass('notice-error');
                            $('.jj-migration-notice p').eq(1).text(response.data.message || '<?php esc_html_e( '마이그레이션 중 오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ); ?>');
                            button.prop('disabled', false).text('<?php esc_html_e( '마이그레이션 실행', 'acf-css-really-simple-style-management-center' ); ?>');
                        }
                    },
                    error: function() {
                        $('.jj-migration-notice').removeClass('notice-warning').addClass('notice-error');
                        $('.jj-migration-notice p').eq(1).text('<?php esc_html_e( '마이그레이션 중 오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ); ?>');
                        button.prop('disabled', false).text('<?php esc_html_e( '마이그레이션 실행', 'acf-css-really-simple-style-management-center' ); ?>');
                    }
                });
            });
            
            $('.jj-skip-migration-button').on('click', function() {
                var button = $(this);
                var nonce = button.data('nonce');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'jj_skip_migration',
                        nonce: nonce
                    },
                    success: function() {
                        $('.jj-migration-notice').fadeOut();
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * AJAX: 기존 플러그인 마이그레이션 실행
     * 
     * [v5.3.6] AJAX 요청을 통해 마이그레이션을 실행합니다.
     */
    public function ajax_migrate_old_plugin() {
        // Nonce 검증
        if ( ! function_exists( 'check_ajax_referer' ) ) {
            wp_send_json_error( array( 'message' => __( '보안 검증에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }
        
        check_ajax_referer( 'jj_migrate_old_plugin', 'nonce' );
        
        // 권한 확인
        if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }
        
        // 마이그레이션 실행
        $result = $this->migrate_data();
        
        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }
    
    /**
     * AJAX: 마이그레이션 건너뛰기
     * 
     * [v5.3.6] 사용자가 마이그레이션을 건너뛰는 경우 플래그를 설정합니다.
     */
    public function ajax_skip_migration() {
        // Nonce 검증
        if ( ! function_exists( 'check_ajax_referer' ) ) {
            wp_send_json_error( array( 'message' => __( '보안 검증에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }
        
        check_ajax_referer( 'jj_skip_migration', 'nonce' );
        
        // 권한 확인
        if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }
        
        // 건너뛰기 플래그 설정
        if ( function_exists( 'update_option' ) ) {
            update_option( 'jj_style_guide_migration_skipped', true );
            wp_send_json_success( array( 'message' => __( '마이그레이션이 건너뛰어졌습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( '설정 저장에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
    }
    
    /**
     * 롤백 실행
     * 
     * [v5.3.6] 마이그레이션된 데이터를 원래 상태로 되돌립니다.
     * 
     * @return array 롤백 결과
     */
    public function rollback() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'update_option' ) || ! function_exists( 'delete_option' ) ) {
            return array(
                'success' => false,
                'message' => __( 'WordPress 함수를 사용할 수 없습니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }
        
        try {
            // 백업 키 가져오기
            $backup_key = get_option( 'jj_style_guide_migration_backup_key', '' );
            if ( empty( $backup_key ) ) {
                return array(
                    'success' => false,
                    'message' => __( '백업 데이터를 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ),
                );
            }
            
            // 백업 데이터 가져오기
            $backup_data = get_option( $backup_key, false );
            if ( false === $backup_data || ! isset( $backup_data['options'] ) ) {
                return array(
                    'success' => false,
                    'message' => __( '백업 데이터가 유효하지 않습니다.', 'acf-css-really-simple-style-management-center' ),
                );
            }
            
            // 옵션 데이터 복원
            $restored_count = 0;
            foreach ( $backup_data['options'] as $option_key => $option_value ) {
                update_option( $option_key, $option_value );
                $restored_count++;
            }
            
            // 마이그레이션 플래그 제거
            delete_option( 'jj_style_guide_migration_completed' );
            delete_option( 'jj_style_guide_migration_from_version' );
            delete_option( 'jj_style_guide_migration_timestamp' );
            
            return array(
                'success' => true,
                'message' => sprintf(
                    __( '성공적으로 롤백되었습니다. %d개의 옵션이 복원되었습니다.', 'acf-css-really-simple-style-management-center' ),
                    $restored_count
                ),
                'restored_count' => $restored_count,
            );
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Migration Manager: 롤백 중 오류 - ' . $e->getMessage() );
            }
            return array(
                'success' => false,
                'message' => sprintf(
                    __( '롤백 중 오류가 발생했습니다: %s', 'acf-css-really-simple-style-management-center' ),
                    $e->getMessage()
                ),
            );
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Migration Manager: 롤백 중 치명적 오류 - ' . $e->getMessage() );
            }
            return array(
                'success' => false,
                'message' => sprintf(
                    __( '롤백 중 치명적 오류가 발생했습니다: %s', 'acf-css-really-simple-style-management-center' ),
                    $e->getMessage()
                ),
            );
        }
    }
}

