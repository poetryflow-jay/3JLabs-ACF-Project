<?php
/**
 * 프론트엔드 클래스
 *
 * @package ACF_Nudge_Flow
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 프론트엔드 렌더링
 * [v22.4.3] 클래스 중복 선언 방지 추가
 */
if ( ! class_exists( 'ACF_Nudge_Flow_Frontend' ) ) {
class ACF_Nudge_Flow_Frontend {

    /**
     * 생성자
     */
    public function __construct() {
        add_action( 'wp_footer', array( $this, 'render_nudge_container' ) );
        add_action( 'wp', array( $this, 'record_visit' ) );
    }

    /**
     * 방문 기록
     */
    public function record_visit() {
        if ( is_admin() || wp_doing_ajax() ) {
            return;
        }

        $tracker = new ACF_Nudge_Visitor_Tracker();
        $tracker->record_visit();
    }

    /**
     * 넛지 컨테이너 렌더링
     */
    public function render_nudge_container() {
        // 설정 확인
        $settings = get_option( 'acf_nudge_flow_settings', array() );
        
        if ( empty( $settings['enabled'] ) ) {
            return;
        }

        // 제외 역할 확인
        $excluded_roles = $settings['excluded_roles'] ?? array();
        if ( is_user_logged_in() ) {
            $user = wp_get_current_user();
            if ( ! empty( array_intersect( $user->roles, $excluded_roles ) ) ) {
                return;
            }
        }

        ?>
        <!-- ACF Nudge Flow Container -->
        <div id="acf-nudge-flow-container" style="display: none;">
            <!-- 팝업 모달 -->
            <div class="acf-nudge-modal-overlay" data-nudge-type="modal"></div>
            <div class="acf-nudge-modal" data-nudge-type="modal">
                <button class="acf-nudge-close" aria-label="<?php esc_attr_e( '닫기', 'acf-nudge-flow' ); ?>">&times;</button>
                <div class="acf-nudge-modal-content"></div>
            </div>
            
            <!-- 슬라이드 인 팝업 -->
            <div class="acf-nudge-slide-in" data-nudge-type="slide-in">
                <button class="acf-nudge-close" aria-label="<?php esc_attr_e( '닫기', 'acf-nudge-flow' ); ?>">&times;</button>
                <div class="acf-nudge-slide-in-content"></div>
            </div>
            
            <!-- 상단 바 -->
            <div class="acf-nudge-bar acf-nudge-bar-top" data-nudge-type="bar-top">
                <div class="acf-nudge-bar-content"></div>
                <button class="acf-nudge-close" aria-label="<?php esc_attr_e( '닫기', 'acf-nudge-flow' ); ?>">&times;</button>
            </div>
            
            <!-- 하단 바 -->
            <div class="acf-nudge-bar acf-nudge-bar-bottom" data-nudge-type="bar-bottom">
                <div class="acf-nudge-bar-content"></div>
                <button class="acf-nudge-close" aria-label="<?php esc_attr_e( '닫기', 'acf-nudge-flow' ); ?>">&times;</button>
            </div>
            
            <!-- 토스트 컨테이너 -->
            <div class="acf-nudge-toast-container" data-nudge-type="toast"></div>
        </div>
        
        <!-- 넛지 템플릿 -->
        <script type="text/template" id="acf-nudge-template-modal">
            <div class="acf-nudge-modal-inner {{style}}">
                {{#image}}
                <div class="acf-nudge-modal-image">
                    <img src="{{image}}" alt="">
                </div>
                {{/image}}
                <div class="acf-nudge-modal-body">
                    {{#title}}
                    <h3 class="acf-nudge-modal-title">{{title}}</h3>
                    {{/title}}
                    <div class="acf-nudge-modal-text">{{{content}}}</div>
                    {{#cta_text}}
                    <a href="{{cta_url}}" class="acf-nudge-cta-button">{{cta_text}}</a>
                    {{/cta_text}}
                    {{#close_text}}
                    <button class="acf-nudge-dismiss-link">{{close_text}}</button>
                    {{/close_text}}
                </div>
            </div>
        </script>
        
        <script type="text/template" id="acf-nudge-template-toast">
            <div class="acf-nudge-toast acf-nudge-toast-{{type}}">
                <span class="acf-nudge-toast-message">{{message}}</span>
                <button class="acf-nudge-toast-close">&times;</button>
            </div>
        </script>
        
        <script type="text/template" id="acf-nudge-template-bar">
            <span class="acf-nudge-bar-text">{{text}}</span>
            {{#cta_text}}
            <a href="{{cta_url}}" class="acf-nudge-bar-cta">{{cta_text}}</a>
            {{/cta_text}}
        </script>
        <?php
    }
} // End of class ACF_Nudge_Flow_Frontend

} // End of class_exists check

// 프론트엔드 인스턴스 생성
if ( class_exists( 'ACF_Nudge_Flow_Frontend' ) ) {
    new ACF_Nudge_Flow_Frontend();
}
