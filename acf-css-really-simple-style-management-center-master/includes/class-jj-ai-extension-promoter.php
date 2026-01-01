<?php
/**
 * AI Extension Promoter Class
 * 
 * [Phase 8.5.1] AI Extension 감지 및 활성화 유도
 * 
 * @package JJ_Style_Guide
 * @since 8.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_AI_Extension_Promoter {
    
    private static $instance = null;
    private $plugin_file = 'acf-css-ai-extension/acf-css-ai-extension.php';
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Admin Center에만 표시
        add_action( 'admin_notices', array( $this, 'maybe_show_promotion_notice' ) );
        
        // AJAX: 플러그인 활성화
        add_action( 'wp_ajax_jj_activate_ai_extension', array( $this, 'ajax_activate_extension' ) );
        add_action( 'wp_ajax_jj_install_ai_extension', array( $this, 'ajax_install_extension' ) );
        add_action( 'wp_ajax_jj_dismiss_ai_promo', array( $this, 'ajax_dismiss_ai_promo' ) );
    }
    
    /**
     * AI Extension 상태 확인
     * 
     * @return array 상태 정보
     */
    public function get_extension_status() {
        $status = array(
            'installed' => false,
            'active' => false,
            'can_activate' => false,
            'can_install' => false,
            'plugin_file' => $this->plugin_file,
        );
        
        // 플러그인 파일 경로
        $plugin_path = WP_PLUGIN_DIR . '/' . $this->plugin_file;
        
        // 설치 여부 확인
        if ( file_exists( $plugin_path ) ) {
            $status['installed'] = true;
        }
        
        // 활성화 여부 확인
        if ( is_plugin_active( $this->plugin_file ) ) {
            $status['active'] = true;
            return $status; // 활성화되어 있으면 더 이상 진행 불필요
        }
        
        // 설치되어 있으면 활성화 가능
        if ( $status['installed'] ) {
            $status['can_activate'] = current_user_can( 'activate_plugins' );
        } else {
            // 설치되지 않았으면 설치 가능 여부 확인
            $status['can_install'] = current_user_can( 'install_plugins' );
        }
        
        return $status;
    }
    
    /**
     * 프로모션 알림 표시 여부 결정
     */
    public function maybe_show_promotion_notice() {
        // Admin Center 페이지에서만 표시
        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'jj-admin-center' ) === false ) {
            return;
        }
        
        // 이미 활성화되어 있으면 표시하지 않음
        $status = $this->get_extension_status();
        if ( $status['active'] ) {
            return;
        }
        
        // 일주일 이내에 "다시 보지 않기"를 클릭했으면 표시하지 않음
        $dismissed = get_user_meta( get_current_user_id(), 'jj_ai_extension_promo_dismissed', true );
        if ( $dismissed && ( time() - intval( $dismissed ) ) < WEEK_IN_SECONDS ) {
            return;
        }
        
        // 프로모션 배너 표시
        $this->render_promotion_notice( $status );
    }
    
    /**
     * 프로모션 알림 렌더링
     */
    private function render_promotion_notice( $status ) {
        $nonce = wp_create_nonce( 'jj_ai_extension_action' );
        ?>
        <div class="notice notice-info jj-ai-extension-promo" style="border-left-color: #72aee6; padding: 15px 20px; position: relative;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="flex: 1;">
                    <h3 style="margin: 0 0 8px 0; font-size: 16px; color: #1d2327;">
                        <span class="dashicons dashicons-art" style="vertical-align: middle; color: #72aee6;"></span>
                        <?php esc_html_e( '🤖 AI 스타일 인텔리전스 활용하기', 'jj-style-guide' ); ?>
                    </h3>
                    <p style="margin: 0 0 12px 0; font-size: 14px; color: #50575e;">
                        <?php esc_html_e( 'AI Extension을 활성화하면 AI 기반 팔레트 자동 생성, 스마트 스타일 추천, 로컬 AI 모델(Gemma 3) 연동 등 고급 기능을 사용할 수 있습니다.', 'jj-style-guide' ); ?>
                    </p>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <?php if ( $status['installed'] && $status['can_activate'] ) : ?>
                            <button type="button" class="button button-primary jj-activate-ai-extension" 
                                    data-nonce="<?php echo esc_attr( $nonce ); ?>">
                                <span class="dashicons dashicons-admin-plugins" style="vertical-align: middle;"></span>
                                <?php esc_html_e( 'AI Extension 활성화', 'jj-style-guide' ); ?>
                            </button>
                        <?php elseif ( ! $status['installed'] && $status['can_install'] ) : ?>
                            <button type="button" class="button button-primary jj-install-ai-extension" 
                                    data-nonce="<?php echo esc_attr( $nonce ); ?>">
                                <span class="dashicons dashicons-download" style="vertical-align: middle;"></span>
                                <?php esc_html_e( 'AI Extension 설치', 'jj-style-guide' ); ?>
                            </button>
                            <p style="margin: 0; font-size: 12px; color: #666;">
                                <?php esc_html_e( '(수동 설치 필요: 플러그인 폴더에 AI Extension 파일이 있어야 합니다)', 'jj-style-guide' ); ?>
                            </p>
                        <?php else : ?>
                            <p style="margin: 0; font-size: 13px; color: #856404;">
                                <?php esc_html_e( '플러그인 설치/활성화 권한이 필요합니다.', 'jj-style-guide' ); ?>
                            </p>
                        <?php endif; ?>
                        <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button">
                            <?php esc_html_e( '플러그인 페이지로 이동', 'jj-style-guide' ); ?>
                        </a>
                    </div>
                </div>
                <button type="button" class="notice-dismiss jj-dismiss-ai-promo" 
                        data-nonce="<?php echo esc_attr( $nonce ); ?>"
                        style="position: absolute; top: 10px; right: 10px; padding: 5px;">
                    <span class="screen-reader-text"><?php esc_html_e( '다시 보지 않기', 'jj-style-guide' ); ?></span>
                </button>
            </div>
        </div>
        <script>
        jQuery(document).ready(function($) {
            // 활성화 버튼
            $('.jj-activate-ai-extension').on('click', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const nonce = $btn.data('nonce');
                $btn.prop('disabled', true).text('활성화 중...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'jj_activate_ai_extension',
                        nonce: nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.jj-ai-extension-promo').fadeOut(300, function() {
                                $(this).remove();
                            });
                            alert('AI Extension이 활성화되었습니다. 페이지를 새로고침합니다.');
                            window.location.reload();
                        } else {
                            alert('활성화 실패: ' + (response.data && response.data.message ? response.data.message : '알 수 없는 오류'));
                            $btn.prop('disabled', false).html('<span class="dashicons dashicons-admin-plugins" style="vertical-align: middle;"></span> AI Extension 활성화');
                        }
                    },
                    error: function() {
                        alert('서버 통신 오류가 발생했습니다.');
                        $btn.prop('disabled', false).html('<span class="dashicons dashicons-admin-plugins" style="vertical-align: middle;"></span> AI Extension 활성화');
                    }
                });
            });
            
            // 설치 버튼 (안내만)
            $('.jj-install-ai-extension').on('click', function(e) {
                e.preventDefault();
                alert('<?php esc_js_e( 'AI Extension은 수동으로 설치해야 합니다. 플러그인 폴더에 acf-css-ai-extension 폴더가 있는지 확인하세요.', 'jj-style-guide' ); ?>');
            });
            
            // 다시 보지 않기
            $('.jj-dismiss-ai-promo').on('click', function(e) {
                e.preventDefault();
                const nonce = $(this).data('nonce');
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'jj_dismiss_ai_promo',
                        nonce: nonce
                    }
                });
                $('.jj-ai-extension-promo').fadeOut(300, function() {
                    $(this).remove();
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * AJAX: AI Extension 활성화
     */
    public function ajax_activate_extension() {
        // 보안 검증
        if ( class_exists( 'JJ_Security_Hardener' ) ) {
            if ( ! JJ_Security_Hardener::verify_ajax_request( 'jj_activate_ai_extension', 'jj_ai_extension_action', 'activate_plugins' ) ) {
                return;
            }
        } else {
            check_ajax_referer( 'jj_ai_extension_action', 'nonce' );
            if ( ! current_user_can( 'activate_plugins' ) ) {
                wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
                return;
            }
        }
        
        $plugin_file = $this->plugin_file;
        
        // 플러그인 존재 확인
        if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
            wp_send_json_error( array( 'message' => __( 'AI Extension이 설치되어 있지 않습니다.', 'jj-style-guide' ) ) );
            return;
        }
        
        // 이미 활성화되어 있는지 확인
        if ( is_plugin_active( $plugin_file ) ) {
            wp_send_json_success( array( 'message' => __( '이미 활성화되어 있습니다.', 'jj-style-guide' ) ) );
            return;
        }
        
        // 활성화
        $result = activate_plugin( $plugin_file );
        
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        } else {
            wp_send_json_success( array( 'message' => __( 'AI Extension이 활성화되었습니다.', 'jj-style-guide' ) ) );
        }
    }
    
    /**
     * AJAX: AI Extension 설치 (안내만)
     */
    public function ajax_install_extension() {
        wp_send_json_error( array( 'message' => __( '수동 설치가 필요합니다. 플러그인 폴더에 AI Extension 파일이 있는지 확인하세요.', 'jj-style-guide' ) ) );
    }
    
    /**
     * AJAX: 프로모션 알림 해제
     */
    public function ajax_dismiss_ai_promo() {
        // 보안 검증
        if ( class_exists( 'JJ_Security_Hardener' ) ) {
            if ( ! JJ_Security_Hardener::verify_ajax_request( 'jj_dismiss_ai_promo', 'jj_ai_extension_action' ) ) {
                return;
            }
        } else {
            check_ajax_referer( 'jj_ai_extension_action', 'nonce' );
        }
        
        update_user_meta( get_current_user_id(), 'jj_ai_extension_promo_dismissed', time() );
        wp_send_json_success();
    }
}

// 초기화
add_action( 'plugins_loaded', function() {
    JJ_AI_Extension_Promoter::instance();
}, 20 );
