<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 8.4] 사용자 피드백 수집 및 관리
 * 
 * - 사용자 피드백(평점, 의견)을 수집하여 서버로 전송
 * - 비동기(AJAX) 방식 처리
 * - 보안 검증 포함
 */
class JJ_Feedback_Collector {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'wp_ajax_jj_submit_feedback', array( $this, 'ajax_submit_feedback' ) );
        add_action( 'admin_footer', array( $this, 'render_feedback_modal' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * 피드백 스크립트 및 스타일 로드
     */
    public function enqueue_assets( $hook ) {
        // Admin Center 페이지에서만 로드
        if ( strpos( $hook, 'jj-admin-center' ) === false && strpos( $hook, 'jj-labs-center' ) === false ) {
            return;
        }

        wp_enqueue_script( 
            'jj-feedback', 
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-feedback.js', 
            array( 'jquery', 'jj-common-utils' ), 
            JJ_STYLE_GUIDE_VERSION, 
            true 
        );

        wp_localize_script( 'jj-feedback', 'jjFeedback', array(
            'nonce' => wp_create_nonce( 'jj_feedback_action' ),
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'strings' => array(
                'thank_you' => __( '소중한 의견 감사합니다!', 'acf-css-really-simple-style-management-center' ),
                'error' => __( '피드백 전송 중 오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
            )
        ));
    }

    /**
     * AJAX: 피드백 제출 처리
     */
    public function ajax_submit_feedback() {
        // 보안 검증
        if ( class_exists( 'JJ_Security_Hardener' ) ) {
            if ( ! JJ_Security_Hardener::verify_ajax_request( 'jj_submit_feedback', 'jj_feedback_action' ) ) {
                return;
            }
        } else {
            check_ajax_referer( 'jj_feedback_action', 'security' );
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }
        }

        $rating = isset( $_POST['rating'] ) ? intval( $_POST['rating'] ) : 0;
        $message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';
        $type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'general'; // general, bug, feature
        $include_system_info = isset( $_POST['include_system_info'] ) && $_POST['include_system_info'] === 'true';

        if ( $rating < 1 || $rating > 5 ) {
            wp_send_json_error( array( 'message' => __( '유효하지 않은 평점입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $system_info = null;
        if ( $include_system_info ) {
            $system_info = $this->get_system_info_snapshot();
        }

        $feedback_data = array(
            'rating' => $rating,
            'message' => $message,
            'type' => $type,
            'user_id' => get_current_user_id(),
            'timestamp' => current_time( 'mysql' ),
            'version' => JJ_STYLE_GUIDE_VERSION,
            'system_info' => $system_info
        );

        // 옵션에 누적 (임시) - 실제 운영 시에는 외부 API 전송 권장
        $all_feedbacks = get_option( 'jj_style_guide_feedbacks', array() );
        array_unshift( $all_feedbacks, $feedback_data );
        // 최대 100개까지만 보관
        if ( count( $all_feedbacks ) > 100 ) {
            $all_feedbacks = array_slice( $all_feedbacks, 0, 100 );
        }
        update_option( 'jj_style_guide_feedbacks', $all_feedbacks );

        // 마스터 버전일 경우 텔레메트리 전송 시도 (가상 코드)
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ) ) ) {
            // $this->send_to_telemetry_server( $feedback_data );
        }

        wp_send_json_success( array( 'message' => __( '피드백이 성공적으로 전송되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * 시스템 상태 스냅샷 가져오기
     */
    private function get_system_info_snapshot() {
        global $wp_version;
        
        $info = array(
            'wp_version' => $wp_version,
            'php_version' => phpversion(),
            'plugin_version' => JJ_STYLE_GUIDE_VERSION,
            'server_software' => isset($_SERVER['SERVER_SOFTWARE']) ? sanitize_text_field($_SERVER['SERVER_SOFTWARE']) : 'Unknown',
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : 'Unknown',
            'active_plugins' => count( (array) get_option( 'active_plugins', array() ) ),
            'error_logs' => array()
        );

        // 에러 로거가 있다면 최근 로그 20줄 가져오기
        if ( class_exists( 'JJ_Error_Logger' ) ) {
            $logger = JJ_Error_Logger::instance();
            if ( method_exists( $logger, 'get_logs' ) ) {
                $logs = $logger->get_logs( 20 );
                // 개인정보가 있을 수 있으므로 마스킹하거나 간단히 처리해야 함
                // 여기서는 그대로 가져오되, 실제 구현 시 주의 필요
                $info['error_logs'] = $logs;
            }
        }

        return $info;
    }

    /**
     * 피드백 모달 HTML 렌더링 (Admin Footer)
     */
    public function render_feedback_modal() {
        // Admin Center 페이지에서만 렌더링
        $screen = get_current_screen();
        if ( ! $screen || ( strpos( $screen->id, 'jj-admin-center' ) === false && strpos( $screen->id, 'jj-labs-center' ) === false ) ) {
            return;
        }
        ?>
        <div id="jj-feedback-modal" style="display:none;">
            <div class="jj-feedback-overlay"></div>
            <div class="jj-feedback-content">
                <div class="jj-feedback-header">
                    <h3><?php esc_html_e( '피드백 보내기', 'acf-css-really-simple-style-management-center' ); ?></h3>
                    <button type="button" class="jj-feedback-close">&times;</button>
                </div>
                <div class="jj-feedback-body">
                    <p class="jj-feedback-intro"><?php esc_html_e( '이 플러그인을 사용하면서 느낀 점을 알려주세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
                    
                    <div class="jj-feedback-rating">
                        <span class="dashicons dashicons-star-empty" data-value="1"></span>
                        <span class="dashicons dashicons-star-empty" data-value="2"></span>
                        <span class="dashicons dashicons-star-empty" data-value="3"></span>
                        <span class="dashicons dashicons-star-empty" data-value="4"></span>
                        <span class="dashicons dashicons-star-empty" data-value="5"></span>
                        <input type="hidden" id="jj-feedback-rating-value" value="0">
                    </div>

                    <div class="jj-feedback-type">
                        <label><input type="radio" name="jj_feedback_type" value="general" checked> <?php esc_html_e( '일반 의견', 'acf-css-really-simple-style-management-center' ); ?></label>
                        <label><input type="radio" name="jj_feedback_type" value="bug"> <?php esc_html_e( '버그 신고', 'acf-css-really-simple-style-management-center' ); ?></label>
                        <label><input type="radio" name="jj_feedback_type" value="feature"> <?php esc_html_e( '기능 제안', 'acf-css-really-simple-style-management-center' ); ?></label>
                    </div>

                    <textarea id="jj-feedback-message" placeholder="<?php esc_attr_e( '여기에 의견을 작성해주세요...', 'acf-css-really-simple-style-management-center' ); ?>" rows="4"></textarea>

                    <div class="jj-feedback-system-info" style="margin-top: 10px; font-size: 12px; color: #666;">
                        <label>
                            <input type="checkbox" id="jj-feedback-include-system"> 
                            <?php esc_html_e( '시스템 상태 및 에러 로그 포함하기', 'acf-css-really-simple-style-management-center' ); ?>
                        </label>
                        <p class="description" style="font-size: 11px; margin-top: 2px;">
                            <?php esc_html_e( 'PHP/WP 버전 및 최근 발생한 에러 로그가 전송되어 문제 해결에 도움이 됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
                        </p>
                    </div>
                </div>
                <div class="jj-feedback-footer">
                    <button type="button" class="button button-primary jj-feedback-submit" disabled>
                        <?php esc_html_e( '보내기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- 피드백 플로팅 버튼 -->
        <button id="jj-feedback-trigger" class="button button-secondary" title="<?php esc_attr_e( '피드백 보내기', 'acf-css-really-simple-style-management-center' ); ?>">
            <span class="dashicons dashicons-megaphone"></span>
        </button>
        <?php
    }
}

// 인스턴스 초기화
JJ_Feedback_Collector::instance();
