<?php
/**
 * JJ Master AI - 마스터 버전 통합 AI 모듈
 * 
 * ACF CSS AI Extension의 핵심 기능을 마스터 버전에 통합합니다.
 * 
 * @since 13.4.4
 * @package ACF_CSS
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Master_AI {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    private function init() {
        // 관리자 메뉴
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_ai_analyze_colors', array( $this, 'ajax_analyze_colors' ) );
        add_action( 'wp_ajax_jj_ai_suggest_palette', array( $this, 'ajax_suggest_palette' ) );
        add_action( 'wp_ajax_jj_ai_check_accessibility', array( $this, 'ajax_check_accessibility' ) );
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_submenu_page(
            'jj-admin-center',
            __( 'AI 스타일 인텔리전스', 'acf-css-really-simple-style-management-center' ),
            __( '🤖 AI 어시스턴트', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            'jj-ai-assistant',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * 관리자 페이지 렌더링
     */
    public function render_admin_page() {
        $api_key = get_option( 'jj_ai_api_key', '' );
        $api_provider = get_option( 'jj_ai_provider', 'openai' );
        ?>
        <div class="wrap jj-ai-wrap">
            <h1><?php esc_html_e( 'ACF CSS AI 스타일 인텔리전스', 'acf-css-really-simple-style-management-center' ); ?></h1>
            
            <div class="notice notice-info">
                <p>
                    <?php esc_html_e( 'AI 기능을 사용하려면 API 키가 필요합니다. OpenAI, Anthropic, 또는 로컬 LLM을 연결하세요.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
            </div>

            <div class="jj-ai-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                <!-- API 설정 카드 -->
                <div class="jj-ai-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h2><?php esc_html_e( '🔑 API 설정', 'acf-css-really-simple-style-management-center' ); ?></h2>
                    
                    <form method="post" action="options.php">
                        <?php settings_fields( 'jj_ai_options' ); ?>
                        
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e( 'AI 제공자', 'acf-css-really-simple-style-management-center' ); ?></th>
                                <td>
                                    <select name="jj_ai_provider">
                                        <option value="openai" <?php selected( $api_provider, 'openai' ); ?>>OpenAI (GPT)</option>
                                        <option value="anthropic" <?php selected( $api_provider, 'anthropic' ); ?>>Anthropic (Claude)</option>
                                        <option value="local" <?php selected( $api_provider, 'local' ); ?>>로컬 LLM (Ollama 등)</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e( 'API 키', 'acf-css-really-simple-style-management-center' ); ?></th>
                                <td>
                                    <input type="password" name="jj_ai_api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text">
                                </td>
                            </tr>
                        </table>

                        <?php submit_button( __( 'API 설정 저장', 'acf-css-really-simple-style-management-center' ) ); ?>
                    </form>
                </div>

                <!-- AI 기능 카드 -->
                <div class="jj-ai-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h2><?php esc_html_e( '🎨 AI 기능', 'acf-css-really-simple-style-management-center' ); ?></h2>
                    
                    <div class="jj-ai-features">
                        <button type="button" class="button button-primary jj-ai-btn" data-action="analyze_colors" <?php disabled( empty( $api_key ) ); ?>>
                            <?php esc_html_e( '🔍 현재 색상 분석', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        
                        <button type="button" class="button jj-ai-btn" data-action="suggest_palette" <?php disabled( empty( $api_key ) ); ?>>
                            <?php esc_html_e( '🎨 팔레트 추천', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        
                        <button type="button" class="button jj-ai-btn" data-action="check_accessibility" <?php disabled( empty( $api_key ) ); ?>>
                            <?php esc_html_e( '♿ 접근성 검사', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>

                    <div class="jj-ai-result" style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 5px; display: none;">
                        <h4><?php esc_html_e( '결과', 'acf-css-really-simple-style-management-center' ); ?></h4>
                        <div class="jj-ai-result-content"></div>
                    </div>
                </div>
            </div>

            <script>
            jQuery(document).ready(function($) {
                $('.jj-ai-btn').on('click', function() {
                    var action = $(this).data('action');
                    var $result = $('.jj-ai-result');
                    var $content = $result.find('.jj-ai-result-content');
                    
                    $result.show();
                    $content.html('<p>⏳ AI가 분석 중입니다...</p>');
                    
                    $.post(ajaxurl, {
                        action: 'jj_ai_' + action,
                        nonce: '<?php echo wp_create_nonce( 'jj_ai_nonce' ); ?>'
                    }, function(response) {
                        if (response.success) {
                            $content.html(response.data.html);
                        } else {
                            $content.html('<p style="color: red;">오류: ' + response.data.message + '</p>');
                        }
                    });
                });
            });
            </script>
        </div>
        <?php
    }

    /**
     * AJAX: 색상 분석
     */
    public function ajax_analyze_colors() {
        check_ajax_referer( 'jj_ai_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        // 현재 색상 가져오기
        $options = get_option( 'jj_style_guide_options', array() );
        $colors = isset( $options['colors'] ) ? $options['colors'] : array();

        $html = '<ul>';
        foreach ( $colors as $key => $value ) {
            $html .= sprintf( '<li><span style="display: inline-block; width: 20px; height: 20px; background: %s; border-radius: 3px; vertical-align: middle;"></span> %s: %s</li>', esc_attr( $value ), esc_html( $key ), esc_html( $value ) );
        }
        $html .= '</ul>';
        $html .= '<p>' . __( 'AI 분석 기능은 API 키 연결 후 활성화됩니다.', 'acf-css-really-simple-style-management-center' ) . '</p>';

        wp_send_json_success( array( 'html' => $html ) );
    }

    /**
     * AJAX: 팔레트 추천
     */
    public function ajax_suggest_palette() {
        check_ajax_referer( 'jj_ai_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        // 스마트 팔레트 기능 활용
        $html = '<p>' . __( '🎨 AI 기반 팔레트 추천은 API 키를 설정하면 사용할 수 있습니다.', 'acf-css-really-simple-style-management-center' ) . '</p>';
        $html .= '<p>' . __( '지금은 스마트 팔레트 기능을 사용해보세요: 스타일 센터 → 색상 팔레트 → 스마트 생성', 'acf-css-really-simple-style-management-center' ) . '</p>';

        wp_send_json_success( array( 'html' => $html ) );
    }

    /**
     * AJAX: 접근성 검사
     */
    public function ajax_check_accessibility() {
        check_ajax_referer( 'jj_ai_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        $options = get_option( 'jj_style_guide_options', array() );
        $primary = isset( $options['colors']['primary'] ) ? $options['colors']['primary'] : '#2196f3';
        $bg = isset( $options['colors']['background'] ) ? $options['colors']['background'] : '#ffffff';

        // 간단한 대비율 계산
        $contrast = $this->calculate_contrast( $primary, $bg );
        $wcag_aa = $contrast >= 4.5 ? '✅ 통과' : '❌ 미달';
        $wcag_aaa = $contrast >= 7 ? '✅ 통과' : '❌ 미달';

        $html = '<table class="widefat">';
        $html .= '<tr><th>기준</th><th>결과</th></tr>';
        $html .= sprintf( '<tr><td>대비율</td><td>%.2f:1</td></tr>', $contrast );
        $html .= sprintf( '<tr><td>WCAG AA (4.5:1)</td><td>%s</td></tr>', $wcag_aa );
        $html .= sprintf( '<tr><td>WCAG AAA (7:1)</td><td>%s</td></tr>', $wcag_aaa );
        $html .= '</table>';

        wp_send_json_success( array( 'html' => $html ) );
    }

    /**
     * 대비율 계산
     */
    private function calculate_contrast( $color1, $color2 ) {
        $l1 = $this->get_luminance( $color1 );
        $l2 = $this->get_luminance( $color2 );
        
        $lighter = max( $l1, $l2 );
        $darker = min( $l1, $l2 );
        
        return ( $lighter + 0.05 ) / ( $darker + 0.05 );
    }

    /**
     * 상대 휘도 계산
     */
    private function get_luminance( $hex ) {
        $hex = ltrim( $hex, '#' );
        
        $r = hexdec( substr( $hex, 0, 2 ) ) / 255;
        $g = hexdec( substr( $hex, 2, 2 ) ) / 255;
        $b = hexdec( substr( $hex, 4, 2 ) ) / 255;

        $r = $r <= 0.03928 ? $r / 12.92 : pow( ( $r + 0.055 ) / 1.055, 2.4 );
        $g = $g <= 0.03928 ? $g / 12.92 : pow( ( $g + 0.055 ) / 1.055, 2.4 );
        $b = $b <= 0.03928 ? $b / 12.92 : pow( ( $b + 0.055 ) / 1.055, 2.4 );

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }
}
