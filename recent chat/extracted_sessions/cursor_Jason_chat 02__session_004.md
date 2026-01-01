---

**User**

고맙네, 제이슨. 다음 단계의 작업도 진행을 부탁하네. 중요한 작업이지. 바로 진행해주시게.


---

**Cursor**

좋습니다. “다음 단계”를 **AI Extension의 WebGPU(WebLLM) 로컬 실행을 실제로 동작하는 MVP로 완성**하는 걸로 잡고 바로 들어가겠습니다. 먼저 AI Extension 현재 구조/placeholder 지점을 확인한 뒤, 관리자 화면에서 **모델 로드 → 프롬프트 입력 → 브라우저에서 즉시 생성**까지 연결하겠습니다.

좋습니다. 지금 AI Extension은 **UI에 ‘WebGPU(Gemma 3)’ 옵션은 있는데, 실제 생성은 아직 PHP(OpenAI) 경로로만 타고 있어서** webgpu 선택 시 막힐 가능성이 큽니다. 다음은 “브라우저에서 바로 생성”이 되도록 **JS에서 WebLLM 엔진 로드/생성/JSON 패치 파싱**을 붙이고, 서버는 저장(적용)만 담당하도록 분리하겠습니다.

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once dirname( __FILE__ ) . '/interface-ai-provider.php';

/**
 * WebGPU Provider (Browser-side via WebLLM)
 *
 * - 실제 추론은 브라우저(JS)에서 수행됩니다.
 * - 서버(AJAX) 경로로 호출되면 안내 메시지를 반환합니다.
 */
class JJ_AI_Provider_WebGPU implements JJ_AI_Provider_Interface {

    public function get_id() {
        return 'webgpu';
    }

    public function get_name() {
        return 'WebGPU (Browser Local / WebLLM)';
    }

    public function validate_key( $api_key ) {
        // WebGPU는 API Key가 필요 없음
        return true;
    }

    public function generate_style( $prompt, $context, $api_key ) {
        return new WP_Error(
            'webgpu_client_side',
            'WebGPU(브라우저 로컬) 모드는 서버에서 생성하지 않습니다. 이 페이지에서 “Gemma 모델 로드” 후 “AI 제안 생성”을 눌러주세요.'
        );
    }
}


```

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Phase 7.1: AI Extension Skeleton (Mock Provider)
 */
final class JJ_ACF_CSS_AI_Extension {

    private static $instance = null;

    private $menu_slug = 'acf-css-ai-extension';
    private $option_key = 'jj_acf_css_ai_settings';
    private $providers = array();

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_providers();
        
        add_action( 'admin_menu', array( $this, 'register_admin_page' ), 30 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        add_action( 'wp_ajax_jj_ai_generate_style', array( $this, 'ajax_generate' ) );
        add_action( 'wp_ajax_jj_ai_apply_style', array( $this, 'ajax_apply' ) );
        add_action( 'wp_ajax_jj_ai_save_settings', array( $this, 'ajax_save_settings' ) );
        add_action( 'wp_ajax_jj_ai_export_cloud', array( $this, 'ajax_export_cloud' ) );
    }

    private function load_providers() {
        require_once dirname( __FILE__ ) . '/providers/class-jj-ai-provider-openai.php';
        $openai = new JJ_AI_Provider_OpenAI();
        $this->providers[ $openai->get_id() ] = $openai;

        // [Phase 7.5] WebGPU Provider (Browser-side)
        require_once dirname( __FILE__ ) . '/providers/class-jj-ai-provider-webgpu.php';
        $webgpu = new JJ_AI_Provider_WebGPU();
        $this->providers[ $webgpu->get_id() ] = $webgpu;

        // [Phase 7.2] Local Provider (Gemma 3) 추가
        if ( file_exists( dirname( __FILE__ ) . '/providers/class-jj-ai-provider-local.php' ) ) {
            require_once dirname( __FILE__ ) . '/providers/class-jj-ai-provider-local.php';
            $local = new JJ_AI_Provider_Local();
            $this->providers[ $local->get_id() ] = $local;
        }
    }

    // ===== Extension Interface (Phase 5.3) 호환 =====
    public function get_id() {
        return 'acf-css-ai-extension';
    }

    public function get_name() {
        return 'ACF CSS AI Extension';
    }

    public function get_min_version() {
        return '6.0.3';
    }

    public function get_required_capability() {
        // 코어 쪽 capability 체계가 정립되면 이 값을 사용하도록 확장
        return '';
    }

    public function init() {
        // 여기서는 이미 constructor에서 훅을 걸었으므로 별도 동작 없음
    }

    // ===== Admin UI =====
    public function register_admin_page() {
        add_options_page(
            __( 'ACF CSS AI', 'acf-css-ai-extension' ),
            __( 'ACF CSS AI', 'acf-css-ai-extension' ),
            'manage_options',
            $this->menu_slug,
            array( $this, 'render_page' )
        );
    }

    public function enqueue_assets( $hook ) {
        if ( 'settings_page_' . $this->menu_slug !== $hook ) {
            return;
        }

        // [v2.2.0] WebLLM (WebGPU) 라이브러리 로드 (Module)
        add_action('admin_head', function() {
            echo '<script type="module">
                import * as webllm from "https://esm.run/@mlc-ai/web-llm";
                window.webllm = webllm;
            </script>';
        });

        wp_enqueue_style(
            'jj-acf-css-ai-ext',
            JJ_ACF_CSS_AI_EXT_URL . 'assets/ai-extension.css',
            array(),
            JJ_ACF_CSS_AI_EXT_VERSION
        );

        wp_enqueue_script(
            'jj-acf-css-ai-ext',
            JJ_ACF_CSS_AI_EXT_URL . 'assets/ai-extension.js',
            array( 'jquery' ),
            JJ_ACF_CSS_AI_EXT_VERSION,
            true
        );

        // [Phase 7.3] Diff Lib
        wp_enqueue_script(
            'jj-jsondiffpatch-lite',
            JJ_ACF_CSS_AI_EXT_URL . 'assets/jsondiffpatch-lite.js',
            array(),
            JJ_ACF_CSS_AI_EXT_VERSION,
            true
        );

        wp_localize_script(
            'jj-acf-css-ai-ext',
            'jjAiExt',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_ai_ext_nonce' ),
                'is_core_present' => class_exists( 'JJ_Extension_Manager' ) || defined( 'JJ_STYLE_GUIDE_VERSION' ),
                'current_settings' => defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? get_option( JJ_STYLE_GUIDE_OPTIONS_KEY, array() ) : array(),
            )
        );
    }

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // [v2.2.1] Master Edition 독립 실행 모드 (Core 없이도 실행)
        $is_master = ( defined( 'JJ_AI_EXTENSION_LICENSE' ) && 'MASTER' === JJ_AI_EXTENSION_LICENSE );
        
        $core_ok = defined( 'JJ_STYLE_GUIDE_VERSION' );
        if ( $is_master ) $core_ok = true; // Master는 Core 체크 패스

        $settings = get_option( $this->option_key, array() );
        $active_provider = isset( $settings['provider'] ) ? $settings['provider'] : 'openai';
        $api_key = isset( $settings['api_key'] ) ? $settings['api_key'] : '';
        ?>
        <div class="wrap jj-ai-ext-wrap">
            <h1><?php echo esc_html__( 'ACF CSS AI', 'acf-css-ai-extension' ); ?> 
                <small><?php echo $is_master ? '(Master)' : ( defined('JJ_ACF_CSS_AI_EXT_VERSION') ? 'v'.JJ_ACF_CSS_AI_EXT_VERSION : '' ); ?></small>
            </h1>
            <p class="description">
                <?php echo esc_html__( '프롬프트를 입력하면 AI가 스타일 변경안을 생성하고, 적용 전에 변경점을 확인할 수 있습니다.', 'acf-css-ai-extension' ); ?>
            </p>

            <?php if ( ! $core_ok ) : ?>
                <div class="notice notice-warning">
                    <p>
                        <strong><?php echo esc_html__( '코어 플러그인이 감지되지 않았습니다.', 'acf-css-ai-extension' ); ?></strong><br>
                        <?php echo esc_html__( 'ACF CSS Manager(메인 플러그인)를 먼저 활성화해주세요.', 'acf-css-ai-extension' ); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- 설정 카드 (API Key) -->
            <div class="jj-ai-ext-card">
                <h2><?php echo esc_html__( 'AI 설정', 'acf-css-ai-extension' ); ?></h2>
                <form id="jj-ai-settings-form">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="jj-ai-provider"><?php echo esc_html__( 'AI 엔진 선택', 'acf-css-ai-extension' ); ?></label></th>
                            <td>
                                <select id="jj-ai-provider" name="provider">
                                    <option value="openai" <?php selected( $active_provider, 'openai' ); ?>>OpenAI (Cloud)</option>
                                    <option value="webgpu" <?php selected( $active_provider, 'webgpu' ); ?>>Gemma 3 (Local Browser / Free)</option>
                                    <?php if ( isset( $this->providers['local'] ) ) : ?>
                                        <option value="local" <?php selected( $active_provider, 'local' ); ?>>Gemma 3 (Local Server / 127.0.0.1)</option>
                                    <?php endif; ?>
                                </select>
                                <p class="description" id="jj-provider-desc">
                                    <span class="desc-openai" style="display:none;">OpenAI API를 사용합니다. 과금이 발생할 수 있습니다.</span>
                                    <span class="desc-webgpu" style="display:none;">
                                        <strong>WebGPU (Edge AI):</strong> 사용자 브라우저의 GPU를 사용하여 무료로 작동합니다.<br>
                                        최초 1회 모델 다운로드(약 2GB)가 필요하며, PC 사양에 따라 속도가 다를 수 있습니다.
                                    </span>
                                    <span class="desc-local" style="display:none;">
                                        <strong>Local Server:</strong> 동일 PC의 로컬 서버(127.0.0.1)를 통해 실행합니다.<br>
                                        플러그인 폴더의 <code>local-server/run_server_with_launcher_env.bat</code> 실행이 필요합니다.
                                    </span>
                                </p>
                            </td>
                        </tr>
                        <tr class="jj-api-key-row">
                            <th scope="row"><label for="jj-ai-api-key"><?php echo esc_html__( 'API Key', 'acf-css-ai-extension' ); ?></label></th>
                            <td>
                                <input type="password" id="jj-ai-api-key" name="api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" />
                                <p class="description"><?php echo esc_html__( 'OpenAI API Key (sk-...)를 입력하세요.', 'acf-css-ai-extension' ); ?></p>
                            </td>
                        </tr>
                        <!-- WebGPU 모델 관리 (JS로 제어) -->
                        <tr class="jj-webgpu-row" style="display:none;">
                            <th scope="row"><?php echo esc_html__( '로컬 모델 상태', 'acf-css-ai-extension' ); ?></th>
                            <td>
                                <div id="jj-webgpu-status">
                                    <span class="dashicons dashicons-warning" style="color:#f39c12;"></span> 모델이 로드되지 않았습니다.
                                </div>
                                <button type="button" class="button button-secondary" id="jj-webgpu-load" style="margin-top:5px;">
                                    <span class="dashicons dashicons-download"></span> Gemma 3 모델 로드 (브라우저 캐시)
                                </button>
                                <div id="jj-webgpu-progress" style="margin-top:10px; display:none;">
                                    <div class="jj-progress-bar" style="background:#ddd; height:10px; border-radius:5px; overflow:hidden;">
                                        <div class="jj-progress-fill" style="background:#2271b1; width:0%; height:100%;"></div>
                                    </div>
                                    <span class="jj-progress-text">0%</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div style="display:flex; align-items:center; gap:10px; margin-top:15px;">
                        <button type="submit" class="button button-primary" id="jj-ai-save-settings">
                            <?php echo esc_html__( '설정 저장', 'acf-css-ai-extension' ); ?>
                        </button>
                        <span id="jj-ai-settings-msg" style="color: #00a32a; font-weight: bold;"></span>
                    </div>
                </form>
            </div>

            <div class="jj-ai-ext-card">
                <label for="jj-ai-prompt" class="jj-ai-label"><?php echo esc_html__( '프롬프트', 'acf-css-ai-extension' ); ?></label>
                <textarea id="jj-ai-prompt" class="large-text" rows="3" placeholder="<?php echo esc_attr__( '예) 고급스러운 블랙&골드, 법률사무소 느낌. 버튼은 선명하게.', 'acf-css-ai-extension' ); ?>"></textarea>

                <div class="jj-ai-actions">
                    <button type="button" class="button button-primary" id="jj-ai-generate">
                        <?php echo esc_html__( 'AI 제안 생성', 'acf-css-ai-extension' ); ?>
                    </button>
                    <button type="button" class="button button-secondary" id="jj-ai-apply" disabled>
                        <?php echo esc_html__( '적용(저장)', 'acf-css-ai-extension' ); ?>
                    </button>
                    <span class="spinner" id="jj-ai-spinner" style="float:none; visibility:hidden;"></span>
                </div>

                <div id="jj-ai-result" class="jj-ai-result" style="display:none;">
                    <h2><?php echo esc_html__( '제안 결과', 'acf-css-ai-extension' ); ?></h2>
                    <div id="jj-ai-explanation" class="jj-ai-explanation"></div>
                    <pre id="jj-ai-patch" class="jj-ai-patch"></pre>
                </div>
            </div>
        </div>
        <?php
    }

    // ===== AJAX =====
    public function ajax_generate() {
        check_ajax_referer( 'jj_ai_ext_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        $prompt = isset( $_POST['prompt'] ) ? sanitize_text_field( wp_unslash( $_POST['prompt'] ) ) : '';
        if ( '' === $prompt ) {
            wp_send_json_error( array( 'message' => '프롬프트를 입력하세요.' ) );
        }

        // 설정 로드
        $settings = get_option( $this->option_key, array() );
        $provider_id = isset( $settings['provider'] ) ? $settings['provider'] : 'openai';
        $api_key = isset( $settings['api_key'] ) ? $settings['api_key'] : '';

        if ( ! isset( $this->providers[ $provider_id ] ) ) {
            wp_send_json_error( array( 'message' => '유효하지 않은 Provider입니다.' ) );
        }

        $provider = $this->providers[ $provider_id ];
        
        // 컨텍스트(현재 설정) 준비
        $context_key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $context = get_option( $context_key, array() );

        // 실제 생성 요청
        $result = $provider->generate_style( $prompt, $context, $api_key );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( $result );
    }

    public function ajax_apply() {
        check_ajax_referer( 'jj_ai_ext_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        // 멀티사이트 네트워크 전용 모드에서는 차단
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $ms = JJ_Multisite_Controller::instance();
                if ( method_exists( $ms, 'is_enabled' ) && method_exists( $ms, 'allow_site_override' ) ) {
                    if ( $ms->is_enabled() && ! $ms->allow_site_override() ) {
                        wp_send_json_error( array( 'message' => '이 사이트는 네트워크에서 스타일을 관리 중입니다. 네트워크 관리자에서 변경해주세요.' ) );
                    }
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        $patch = isset( $_POST['settings_patch'] ) && is_array( $_POST['settings_patch'] )
            ? wp_unslash( $_POST['settings_patch'] )
            : null;

        if ( ! is_array( $patch ) ) {
            wp_send_json_error( array( 'message' => 'settings_patch가 올바르지 않습니다.' ) );
        }

        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $old = get_option( $key, array() );
        $new = $this->merge_recursive( (array) $old, $this->sanitize_recursive( $patch ) );

        update_option( $key, $new );

        // 캐시 플러시
        if ( class_exists( 'JJ_CSS_Cache' ) && method_exists( 'JJ_CSS_Cache', 'instance' ) ) {
            try {
                $cache = JJ_CSS_Cache::instance();
                if ( $cache && method_exists( $cache, 'flush' ) ) {
                    $cache->flush();
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        // 자동화 이벤트
        do_action( 'jj_style_guide_settings_updated', $new, (array) $old, 'ai_extension' );

        wp_send_json_success( array( 'message' => 'AI 제안이 적용되었습니다.' ) );
    }

    public function ajax_save_settings() {
        check_ajax_referer( 'jj_ai_ext_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        $provider = isset( $_POST['provider'] ) ? sanitize_text_field( $_POST['provider'] ) : 'openai';
        $api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( $_POST['api_key'] ) : '';

        // 키 검증
        if ( isset( $this->providers[ $provider ] ) ) {
            $valid = $this->providers[ $provider ]->validate_key( $api_key );
            if ( is_wp_error( $valid ) ) {
                wp_send_json_error( array( 'message' => $valid->get_error_message() ) );
            }
        }

        $settings = array(
            'provider' => $provider,
            'api_key'  => $api_key,
        );

        update_option( $this->option_key, $settings );
        wp_send_json_success( array( 'message' => '설정이 저장되었습니다.' ) );
    }

    public function ajax_export_cloud() {
        check_ajax_referer( 'jj_ai_ext_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        // Cloud Manager 체크 (Core Plugin Module)
        if ( ! class_exists( 'JJ_Cloud_Manager' ) ) {
            wp_send_json_error( array( 'message' => 'Cloud Manager 모듈이 활성화되지 않았습니다. (Premium 이상 필요)' ) );
        }

        $patch = isset( $_POST['settings_patch'] ) && is_array( $_POST['settings_patch'] )
            ? wp_unslash( $_POST['settings_patch'] )
            : null;
        $prompt = isset( $_POST['prompt'] ) ? sanitize_text_field( wp_unslash( $_POST['prompt'] ) ) : '';

        if ( ! is_array( $patch ) ) {
            wp_send_json_error( array( 'message' => '저장할 데이터가 없습니다.' ) );
        }

        // 현재 설정 + Patch 병합
        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $current = get_option( $key, array() );
        $merged = $this->merge_recursive( (array) $current, $this->sanitize_recursive( $patch ) );

        // 임시 저장용 데이터 구성
        $export_data = array(
            'version' => defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '0.0.0',
            'timestamp' => current_time( 'mysql' ),
            'options' => $merged,
            'ai_prompt' => $prompt, // AI 메타데이터
            'source' => 'ai_extension',
        );

        // Cloud API 호출 (JJ_Cloud_Manager 로직 재사용 불가 시 직접 호출)
        // JJ_Cloud_Manager::ajax_export()는 AJAX용이라 직접 호출 어려움.
        // 여기서는 Cloud API URL을 직접 호출 (Core의 Cloud Manager 설정을 참고)
        
        $api_base_url = 'https://j-j-labs.com/wp-json/jj-neural-link/v1/cloud';
        if ( has_filter( 'jj_cloud_api_base_url' ) ) {
            $api_base_url = apply_filters( 'jj_cloud_api_base_url', $api_base_url );
        }
        $sslverify = true;
        if ( has_filter( 'jj_cloud_sslverify' ) ) {
            $sslverify = apply_filters( 'jj_cloud_sslverify', $sslverify );
        }

        $response = wp_remote_post( $api_base_url . '/store', array(
            'timeout' => 15,
            'sslverify' => $sslverify,
            'body' => array(
                'license_key' => get_option( 'jj_style_guide_license_key', '' ),
                'data' => wp_json_encode( $export_data )
            )
        ) );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => '서버 통신 오류: ' . $response->get_error_message() ) );
        }

        $body = wp_remote_retrieve_body( $response );
        $result = json_decode( $body, true );

        if ( isset( $result['success'] ) && $result['success'] ) {
            wp_send_json_success( array( 
                'share_code' => $result['share_code'],
                'message' => $result['message']
            ) );
        } else {
            $msg = isset( $result['message'] ) ? $result['message'] : '알 수 없는 오류';
            wp_send_json_error( array( 'message' => $msg ) );
        }
    }

    private function merge_recursive( array $base, array $patch ) {
        foreach ( $patch as $k => $v ) {
            if ( is_array( $v ) && isset( $base[ $k ] ) && is_array( $base[ $k ] ) ) {
                $base[ $k ] = $this->merge_recursive( $base[ $k ], $v );
                continue;
            }
            $base[ $k ] = $v;
        }
        return $base;
    }

    private function sanitize_recursive( $input ) {
        $out = array();
        foreach ( (array) $input as $k => $v ) {
            $safe_k = function_exists( 'sanitize_key' ) ? sanitize_key( (string) $k ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $k ) );
            if ( is_array( $v ) ) {
                $out[ $safe_k ] = $this->sanitize_recursive( $v );
                continue;
            }
            // 색상 키는 hex로
            if ( strpos( $safe_k, 'color' ) !== false && function_exists( 'sanitize_hex_color' ) ) {
                $hex = sanitize_hex_color( (string) $v );
                $out[ $safe_k ] = $hex ? $hex : '';
                continue;
            }
            $out[ $safe_k ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( (string) $v ) : strip_tags( (string) $v );
        }
        return $out;
    }
}

```

```javascript
(function ($) {
  $(function () {
    const $provider = $('#jj-ai-provider');
    const $prompt = $('#jj-ai-prompt');
    const $gen = $('#jj-ai-generate');
    const $apply = $('#jj-ai-apply');
    const $spinner = $('#jj-ai-spinner');
    const $result = $('#jj-ai-result');
    const $exp = $('#jj-ai-explanation');
    const $patch = $('#jj-ai-patch');

    const $apiKeyRow = $('.jj-api-key-row');
    const $webgpuRow = $('.jj-webgpu-row');
    const $descOpenai = $('.desc-openai');
    const $descWebgpu = $('.desc-webgpu');
    const $descLocal = $('.desc-local');
    const $webgpuStatus = $('#jj-webgpu-status');
    const $webgpuLoad = $('#jj-webgpu-load');
    const $webgpuProgress = $('#jj-webgpu-progress');
    const $webgpuProgressFill = $('#jj-webgpu-progress .jj-progress-fill');
    const $webgpuProgressText = $('#jj-webgpu-progress .jj-progress-text');
    
    // Settings form
    const $settingsForm = $('#jj-ai-settings-form');
    const $settingsMsg = $('#jj-ai-settings-msg');

    let lastPatch = null;
    let lastPrompt = '';

    // WebGPU state
    const webgpuState = {
      engine: null,
      loading: false,
      loadingPromise: null,
      modelId: null,
    };

    function setLoading(on) {
      $spinner.css('visibility', on ? 'visible' : 'hidden');
      $gen.prop('disabled', on);
      if (on) $apply.prop('disabled', true);
    }

    function getSelectedProvider() {
      return ($provider.val() || 'openai').toString();
    }

    function syncProviderUi() {
      const p = getSelectedProvider();
      // Descriptions
      $descOpenai.hide();
      $descWebgpu.hide();
      $descLocal.hide();

      // Rows
      if (p === 'openai') {
        $apiKeyRow.show();
        $webgpuRow.hide();
        $descOpenai.show();
      } else if (p === 'webgpu') {
        $apiKeyRow.hide();
        $webgpuRow.show();
        $descWebgpu.show();
      } else if (p === 'local') {
        $apiKeyRow.hide();
        $webgpuRow.hide();
        $descLocal.show();
      } else {
        // Unknown provider: default to openai UI
        $apiKeyRow.show();
        $webgpuRow.hide();
        $descOpenai.show();
      }

      // WebGPU availability hint
      if (p === 'webgpu') {
        if (!isWebGpuAvailable()) {
          setWebGpuStatus('error', '이 브라우저에서는 WebGPU를 사용할 수 없습니다. (Chrome/Edge 최신 버전 권장)');
          $webgpuLoad.prop('disabled', true);
        } else {
          $webgpuLoad.prop('disabled', false);
          if (webgpuState.engine) {
            setWebGpuStatus('ok', '모델 로드됨: ' + (webgpuState.modelId || '알 수 없음'));
          } else {
            setWebGpuStatus('warn', '모델이 로드되지 않았습니다.');
          }
        }
      }
    }

    function setWebGpuStatus(kind, text) {
      if ($webgpuStatus.length === 0) return;
      let icon = 'info';
      let color = '#2271b1';
      if (kind === 'ok') { icon = 'yes-alt'; color = '#00a32a'; }
      if (kind === 'warn') { icon = 'warning'; color = '#f39c12'; }
      if (kind === 'error') { icon = 'dismiss'; color = '#d63638'; }

      $webgpuStatus.html(
        '<span class="dashicons dashicons-' + icon + '" style="color:' + color + ';"></span> ' +
        $('<div/>').text(text).html()
      );
    }

    function setWebGpuProgress(percent, text) {
      if ($webgpuProgress.length === 0) return;
      $webgpuProgress.show();
      const pct = Math.max(0, Math.min(100, Math.round(percent)));
      $webgpuProgressFill.css('width', pct + '%');
      if (text) {
        $webgpuProgressText.text(text + ' (' + pct + '%)');
      } else {
        $webgpuProgressText.text(pct + '%');
      }
    }

    function hideWebGpuProgress() {
      if ($webgpuProgress.length === 0) return;
      $webgpuProgress.hide();
    }

    function isWebGpuAvailable() {
      return typeof navigator !== 'undefined' && !!navigator.gpu;
    }

    async function waitForWebllm(timeoutMs) {
      const start = Date.now();
      while (Date.now() - start < timeoutMs) {
        if (window.webllm) return window.webllm;
        await new Promise((r) => setTimeout(r, 100));
      }
      return null;
    }

    function pickContextForAi(current) {
      const ctx = current || {};
      // 너무 큰 컨텍스트는 모델/브라우저에 부담이므로 핵심만 전달
      return {
        palettes: ctx.palettes || {},
        typography: ctx.typography || {},
        buttons: ctx.buttons || {},
      };
    }

    function buildSystemPrompt(context) {
      const contextJson = JSON.stringify(context || {});
      return [
        "너는 WordPress 'ACF CSS Manager' 플러그인의 스타일 전문가 AI야.",
        "사용자의 프롬프트(분위기, 브랜드, 업종 등)를 분석해서 JSON 포맷으로 스타일 설정(settings_patch)을 제안해야 해.",
        "",
        "[제약 사항]",
        "1. 반드시 유효한 JSON 객체만 출력할 것. (마크다운/설명/코드블록 금지)",
        "2. 최상위 키는 explanation(한국어 요약)과 settings_patch(설정 객체)만 허용.",
        "3. settings_patch는 아래 구조를 따름 (일부만 포함 가능):",
        "   - palettes: { brand: { primary_color, primary_color_hover, secondary_color, ... } }",
        "   - typography: { h1: { font_family, font_size, ... }, body: { ... } }",
        "   - buttons: { primary: { bg_color, text_color, ... } }",
        "4. 색상은 반드시 Hex Code(#RRGGBB) 형태여야 함.",
        "5. 현재 설정 컨텍스트를 참고하여, 변경이 필요한 부분만 settings_patch에 포함할 것.",
        "",
        "[현재 설정 컨텍스트]",
        contextJson
      ].join("\n");
    }

    function extractJson(text) {
      if (!text) return '';
      const m = String(text).match(/```json\s*([\s\S]*?)\s*```/i);
      if (m && m[1]) return m[1].trim();
      const s = String(text);
      const start = s.indexOf('{');
      const end = s.lastIndexOf('}');
      if (start !== -1 && end !== -1 && end > start) return s.slice(start, end + 1);
      return s.trim();
    }

    function parseAiJsonResponse(rawText) {
      const jsonStr = extractJson(rawText);
      let parsed = null;
      try {
        parsed = JSON.parse(jsonStr);
      } catch (e) {
        throw new Error('AI 응답 JSON 파싱 실패: ' + e.message);
      }
      if (!parsed || typeof parsed !== 'object') {
        throw new Error('AI 응답이 올바른 JSON 객체가 아닙니다.');
      }
      if (!parsed.settings_patch || typeof parsed.settings_patch !== 'object') {
        throw new Error('AI 응답에 settings_patch가 없습니다.');
      }
      return {
        explanation: parsed.explanation || '',
        settings_patch: parsed.settings_patch
      };
    }

    function getDefaultModelId(webllm) {
      const list = webllm?.prebuiltAppConfig?.model_list || webllm?.prebuiltAppConfig?.modelList || [];
      if (Array.isArray(list) && list.length) {
        // Gemma 계열 우선
        const gemma = list.find((m) => String(m.model_id || m.modelId || '').toLowerCase().includes('gemma'));
        if (gemma) return gemma.model_id || gemma.modelId;
        const first = list[0];
        return first.model_id || first.modelId;
      }
      // Fallback (구버전/커스텀)
      return 'Llama-3.2-1B-Instruct-q4f16_1-MLC';
    }

    async function ensureWebGpuEngine() {
      if (webgpuState.engine) return webgpuState.engine;
      if (webgpuState.loading && webgpuState.loadingPromise) return webgpuState.loadingPromise;

      if (!isWebGpuAvailable()) {
        throw new Error('WebGPU를 사용할 수 없습니다.');
      }

      const webllm = await waitForWebllm(10000);
      if (!webllm) {
        throw new Error('WebLLM 라이브러리를 불러오지 못했습니다. 네트워크/차단 여부를 확인해주세요.');
      }

      const modelId = getDefaultModelId(webllm);
      webgpuState.modelId = modelId;

      webgpuState.loading = true;
      setWebGpuStatus('info', '모델 로드 중... (최초 1회 다운로드 필요)');
      setWebGpuProgress(1, '초기화');
      $webgpuLoad.prop('disabled', true);

      const progressCb = (report) => {
        // report: {progress: 0~1, text: string} 형태가 일반적
        const p = (report && typeof report.progress === 'number') ? report.progress : null;
        const txt = (report && report.text) ? report.text : '';
        if (p !== null) setWebGpuProgress(p * 100, txt || '다운로드/초기화');
      };

      const create = webllm.CreateMLCEngine || webllm.createMLCEngine;
      if (!create) {
        throw new Error('WebLLM API(CreateMLCEngine)를 찾을 수 없습니다. 라이브러리 버전을 확인해주세요.');
      }

      webgpuState.loadingPromise = create(modelId, {
        initProgressCallback: progressCb
      });

      try {
        webgpuState.engine = await webgpuState.loadingPromise;
        setWebGpuProgress(100, '완료');
        setWebGpuStatus('ok', '모델 로드 완료: ' + modelId);
        hideWebGpuProgress();
        return webgpuState.engine;
      } finally {
        webgpuState.loading = false;
        $webgpuLoad.prop('disabled', false);
      }
    }

    async function generateWithWebGpu(prompt) {
      const webllm = await waitForWebllm(10000);
      if (!webllm) {
        throw new Error('WebLLM을 불러오지 못했습니다.');
      }

      const engine = await ensureWebGpuEngine();
      const current = jjAiExt.current_settings || {};
      const context = pickContextForAi(current);

      const systemPrompt = buildSystemPrompt(context);
      const userPrompt = String(prompt);

      // WebLLM Chat Completions
      const chat = engine?.chat?.completions;
      if (!chat || typeof chat.create !== 'function') {
        throw new Error('WebLLM 엔진 API(chat.completions.create)를 찾을 수 없습니다.');
      }

      const res = await chat.create({
        messages: [
          { role: 'system', content: systemPrompt },
          { role: 'user', content: userPrompt },
        ],
        temperature: 0.7,
        max_tokens: 1200,
      });

      const content = res?.choices?.[0]?.message?.content || '';
      return parseAiJsonResponse(content);
    }

    function renderAiResult(settingsPatch, explanation, promptForCloud) {
      lastPatch = settingsPatch || null;

      // [Phase 7.3] Visual Diff
      const current = jjAiExt.current_settings || {};
      let diffHtml = '';

      if (window.jjJsonDiff && lastPatch) {
        try {
          // Simple merge for visualization
          const merged = $.extend(true, {}, current, lastPatch);
          const delta = window.jjJsonDiff.diff(current, merged);
          diffHtml = window.jjJsonDiff.formatHtml(delta);
        } catch (e) {
          console.error('Diff error:', e);
          diffHtml = '<pre>' + JSON.stringify(lastPatch, null, 2) + '</pre>';
        }
      } else {
        diffHtml = '<pre>' + JSON.stringify(lastPatch, null, 2) + '</pre>';
      }

      $exp.text(explanation || '');
      $patch.html(diffHtml);

      // Add Cloud Export Button if not exists
      if ($('#jj-ai-cloud-export').length === 0) {
        const $cloudBtn = $('<button type="button" class="button button-secondary" id="jj-ai-cloud-export" style="margin-left:10px;">☁️ Cloud 저장</button>');
        $apply.after($cloudBtn);

        // [Phase 7.4] Cloud Export Handler
        $cloudBtn.on('click', function() {
          if (!lastPatch) return;
          if (!confirm('이 스타일을 클라우드(내 템플릿)에 저장하시겠습니까?')) return;

          const btn = $(this);
          btn.prop('disabled', true).text('저장 중...');

          $.ajax({
            url: jjAiExt.ajax_url,
            type: 'POST',
            data: {
              action: 'jj_ai_export_cloud',
              nonce: jjAiExt.nonce,
              settings_patch: lastPatch,
              prompt: promptForCloud || lastPrompt || ''
            },
            success: function(res) {
              if (res.success) {
                alert('클라우드에 저장되었습니다! 공유 코드: ' + res.data.share_code);
              } else {
                alert(res.data.message || '저장 실패');
              }
            },
            error: function(xhr, status, error) {
              alert('오류: ' + error);
            },
            complete: function() {
              btn.prop('disabled', false).text('☁️ Cloud 저장');
            }
          });
        });
      }

      $result.show();
      $apply.prop('disabled', !lastPatch);
    }

    // Save Settings
    $settingsForm.on('submit', function(e) {
      e.preventDefault();
      const data = $(this).serialize();
      
      $settingsMsg.text('저장 중...').css('color', '#666');
      
      $.ajax({
        url: jjAiExt.ajax_url,
        type: 'POST',
        data: data + '&action=jj_ai_save_settings&nonce=' + jjAiExt.nonce,
        success: function (res) {
          if (!res || !res.success) {
            $settingsMsg.text(res?.data?.message || '저장 실패').css('color', '#d63638');
            return;
          }
          $settingsMsg.text(res.data.message || '저장 완료').css('color', '#00a32a');
          setTimeout(() => $settingsMsg.text(''), 3000);
        },
        error: function (_, __, err) {
          $settingsMsg.text('AJAX 오류: ' + err).css('color', '#d63638');
        }
      });
    });

    $provider.on('change', function() {
      syncProviderUi();
    });
    syncProviderUi();

    $webgpuLoad.on('click', async function() {
      if (!isWebGpuAvailable()) {
        alert('이 브라우저는 WebGPU를 지원하지 않습니다. Chrome/Edge 최신 버전을 사용해주세요.');
        return;
      }
      if (!confirm('최초 1회 모델 다운로드가 필요할 수 있습니다(약 2GB). 지금 로드할까요?')) {
        return;
      }

      try {
        await ensureWebGpuEngine();
        syncProviderUi();
      } catch (e) {
        console.error(e);
        setWebGpuStatus('error', e.message || String(e));
        alert(e.message || '모델 로드 실패');
      }
    });

    $gen.on('click', async function () {
      const prompt = ($prompt.val() || '').trim();
      if (!prompt) {
        alert('프롬프트를 입력하세요.');
        return;
      }

      setLoading(true);
      lastPrompt = prompt;

      const provider = getSelectedProvider();
      if (provider === 'webgpu') {
        try {
          if (!webgpuState.engine) {
            const ok = confirm('WebGPU 모델이 아직 로드되지 않았습니다. 지금 로드할까요? (최초 1회 다운로드 필요)');
            if (!ok) {
              return;
            }
            await ensureWebGpuEngine();
          }

          const out = await generateWithWebGpu(prompt);
          renderAiResult(out.settings_patch, out.explanation, prompt);
        } catch (e) {
          console.error(e);
          alert(e.message || 'WebGPU 생성 실패');
        } finally {
          setLoading(false);
        }
        return;
      }

      $.ajax({
        url: jjAiExt.ajax_url,
        type: 'POST',
        data: {
          action: 'jj_ai_generate_style',
          nonce: jjAiExt.nonce,
          prompt,
        },
        success: function (res) {
          if (!res || !res.success) {
            alert(res?.data?.message || '생성 실패');
            return;
          }
          renderAiResult(res.data.settings_patch || null, res.data.explanation || '', prompt);
        },
        error: function (_, __, err) {
          alert('AJAX 오류: ' + err);
        },
        complete: function () {
          setLoading(false);
        },
      });
    });

    $apply.on('click', function () {
      if (!lastPatch) return;
      if (!confirm('현재 설정에 AI 제안을 적용(저장)합니다. 계속할까요?')) return;

      setLoading(true);

      $.ajax({
        url: jjAiExt.ajax_url,
        type: 'POST',
        data: {
          action: 'jj_ai_apply_style',
          nonce: jjAiExt.nonce,
          settings_patch: lastPatch,
        },
        success: function (res) {
          if (!res || !res.success) {
            alert(res?.data?.message || '적용 실패');
            return;
          }
          alert(res.data.message || '적용 완료');
          $apply.prop('disabled', true);
          location.reload(); // Refresh to see changes
        },
        error: function (_, __, err) {
          alert('AJAX 오류: ' + err);
        },
        complete: function () {
          setLoading(false);
        },
      });
    });
  });
})(jQuery);

```

```css
.jj-ai-ext-wrap .jj-ai-ext-card {
  max-width: 900px;
  background: #fff;
  border: 1px solid #c3c4c7;
  border-radius: 6px;
  padding: 18px 18px;
  margin-top: 16px;
}

.jj-ai-label {
  font-weight: 700;
  display: block;
  margin-bottom: 6px;
}

.jj-ai-actions {
  margin-top: 12px;
  display: flex;
  gap: 10px;
  align-items: center;
}

.jj-ai-result {
  margin-top: 18px;
  border-top: 1px solid #e5e5e5;
  padding-top: 14px;
}

.jj-ai-explanation {
  margin-bottom: 10px;
  color: #1d2327;
}

.jj-ai-patch {
  background: #0b1020;
  color: #e6edf3;
  padding: 12px;
  border-radius: 6px;
  overflow: auto;
  max-height: 340px;
}

.jj-ai-ext-wrap #jj-webgpu-status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 10px;
  border: 1px solid #dcdcde;
  border-radius: 6px;
  background: #f6f7f7;
}

.jj-ai-ext-wrap #jj-webgpu-progress {
  max-width: 520px;
}

.jj-ai-ext-wrap #jj-webgpu-progress .jj-progress-text {
  display: inline-block;
  margin-top: 6px;
  color: #646970;
  font-size: 12px;
}


```

```php
<?php
/**
 * Plugin Name:       ACF CSS AI Extension - Intelligent Style Generator
 * Description:       ACF CSS Manager의 강력한 확장 플러그인입니다. AI를 활용하여 웹사이트의 스타일을 자동으로 제안하고 생성하며, 로컬 AI 모델(Gemma 3)과의 연동을 지원합니다.
 * Version:           2.0.1
 * Author:            3J Labs
 * Created by:        Jay & Jason & Jenny
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Text Domain:       acf-css-ai-extension
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'JJ_ACF_CSS_AI_EXT_VERSION', '2.0.1' );
define( 'JJ_ACF_CSS_AI_EXT_PATH', plugin_dir_path( __FILE__ ) );
define( 'JJ_ACF_CSS_AI_EXT_URL', plugin_dir_url( __FILE__ ) );

require_once JJ_ACF_CSS_AI_EXT_PATH . 'includes/class-jj-acf-css-ai-extension.php';

// 확장 매니저(Phase 5.3)로 등록
add_filter( 'jj_style_guide_extensions', function ( $items ) {
    if ( ! is_array( $items ) ) {
        $items = array();
    }
    $items[] = function () {
        return JJ_ACF_CSS_AI_Extension::instance();
    };
    return $items;
} );

// 코어가 없어도 “관리자 페이지에서 안내”는 가능하게 하기 위해 직접 로드도 시도
add_action( 'plugins_loaded', function () {
    if ( class_exists( 'JJ_ACF_CSS_AI_Extension' ) ) {
        JJ_ACF_CSS_AI_Extension::instance();
    }
}, 30 );



```

```python
import os
import shutil
import zipfile
import re
import json
import subprocess
from datetime import datetime
import time

# ============================================================
# JJ Deployment System (JDS) Configuration
# ============================================================
BASE_VERSION = '8.0.0'
VERSIONS = {
    'core': BASE_VERSION,
    'neural': '3.9.9',
    'ai': '2.0.1',
    'woo': '2.0.0',
    'bulk': '2.2.2',
    'menu': '2.0.0'
}

# Output Directory
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
OUTPUT_ROOT = os.path.join(os.environ['USERPROFILE'], 'Desktop', f'JJ_Distributions_v{BASE_VERSION}_Master_Control')
LOG_DIR = os.path.join(OUTPUT_ROOT, 'deploy_logs')

# Source Maps
SOURCES = {
    'core': 'acf-css-really-simple-style-management-center-master',
    'ai': 'acf-css-ai-extension',
    'neural': 'acf-css-neural-link',
    'woo': 'marketing/wordpress-plugins/acf-css-woo-license',
    'bulk': 'marketing/wordpress-plugins/wp-bulk-installer',
    'menu': 'marketing/wordpress-plugins/admin-menu-editor-lite'
}

# Edition Configuration
EDITIONS = {
    'free':      {'suffix': '',             'license': 'FREE',      'channels': ['stable', 'beta', 'staging']},
    'basic':     {'suffix': '-Pro-Basic',   'license': 'BASIC',     'channels': ['stable', 'beta', 'staging']},
    'premium':   {'suffix': '-Pro-Premium', 'license': 'PREMIUM',   'channels': ['stable', 'beta', 'staging']},
    'unlimited': {'suffix': '-Pro-Unlimited','license': 'UNLIMITED','channels': ['stable', 'beta', 'staging']},
    'partner':   {'suffix': '-Partner',     'license': 'PARTNER',   'channels': ['stable', 'staging']},
    'master':    {'suffix': '-Master',      'license': 'MASTER',    'channels': ['stable', 'staging']}
}

# Add-on Editions (Channels)
ADDON_CHANNELS = {
    'stable': '',
    'beta': '-beta',
    'staging': '-staging',
    'master': '-master' # Special channel for Master Unlocked Addons
}

# Exclude Patterns
EXCLUDE_PATTERNS = [
    r'^\.git', r'^\.vscode', r'^\.idea', r'__pycache__', r'\.DS_Store$',
    r'^tests', r'^phpunit\.xml', r'^composer\.json', r'node_modules',
    r'^package\.json', r'^package-lock\.json', r'^gulpfile\.js', 
    r'^\.editorconfig', r'^README\.md', r'\.bak$', r'local-server/venv', r'\.py$'
]

class JJ_Deployment_Engine:
    def __init__(self):
        self.build_time = datetime.now()
        self.build_id = self.build_time.strftime('%Y%m%d-%H%M%S')
        self.log_data = {
            'build_id': self.build_id,
            'timestamp': self.build_time.isoformat(),
            'builds': []
        }
        self._prepare_directories()

    def _prepare_directories(self):
        if not os.path.exists(OUTPUT_ROOT):
            os.makedirs(OUTPUT_ROOT)
        if not os.path.exists(LOG_DIR):
            os.makedirs(LOG_DIR)

    def _get_git_info(self):
        try:
            # Get last commit message
            msg = subprocess.check_output(['git', 'log', '-1', '--pretty=%B'], stderr=subprocess.STDOUT).decode().strip()
            # Get hash
            sha = subprocess.check_output(['git', 'rev-parse', '--short', 'HEAD'], stderr=subprocess.STDOUT).decode().strip()
            return {'commit': msg, 'hash': sha}
        except:
            return {'commit': 'Manual Build', 'hash': 'none'}

    def copy_files(self, src, dst):
        count = 0
        for root, dirs, files in os.walk(src):
            rel_root = os.path.relpath(root, src)
            if rel_root == ".": rel_root = ""
            
            dirs[:] = [d for d in dirs if not any(re.search(p, os.path.join(rel_root, d).replace('\\', '/')) for p in EXCLUDE_PATTERNS)]
            
            for file in files:
                rel_file_path = os.path.join(rel_root, file).replace('\\', '/')
                if any(re.search(p, rel_file_path) for p in EXCLUDE_PATTERNS):
                    continue
                    
                src_file = os.path.join(root, file)
                dst_file = os.path.join(dst, rel_file_path)
                
                os.makedirs(os.path.dirname(dst_file), exist_ok=True)
                shutil.copy2(src_file, dst_file)
                count += 1
        return count

    def process_core_file(self, file_path, edition, channel, version):
        if not os.path.exists(file_path): return
        
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()

        name_suffix = ""
        if edition != 'free':
            if edition == 'master': name_suffix = " (Master)"
            elif edition in ['basic', 'premium', 'unlimited']: name_suffix = " PRO"
            else: name_suffix = f" ({edition.capitalize()})"
        
        channel_label = ""
        if channel == 'beta': channel_label = " [BETA]"
        elif channel == 'staging': channel_label = " [STAGING]"

        new_name = f"ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager{name_suffix}{channel_label}"
        content = re.sub(r"Plugin Name:.*", f"Plugin Name:       {new_name}", content)

        content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_EDITION',\s*'[^']+'\s*\);", 
                        f"define( 'JJ_STYLE_GUIDE_EDITION', '{edition}' );", content)
        license_type = EDITIONS[edition]['license']
        content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_LICENSE_TYPE',\s*'[^']+'\s*\);", 
                        f"define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', '{license_type}' );", content)
        content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_VERSION',\s*'[^']+'\s*\);", 
                        f"define( 'JJ_STYLE_GUIDE_VERSION', '{version}' );", content)
        
        channel_const = f"define( 'JJ_STYLE_GUIDE_UPDATE_CHANNEL', '{channel}' );"
        if "JJ_STYLE_GUIDE_UPDATE_CHANNEL" in content:
            content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_UPDATE_CHANNEL',\s*'[^']+'\s*\);", channel_const, content)
        else:
            content = re.sub(r"(define\(\s*'JJ_STYLE_GUIDE_VERSION'.*?;)", f"\\1\n{channel_const}", content)

        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)

    def process_addon_file(self, file_path, channel, plugin_key):
        if not os.path.exists(file_path): return
        
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()

        # Inject Master Mode Constant for Master Channel
        if channel == 'master':
            master_const = ""
            if plugin_key == 'bulk':
                master_const = "if ( ! defined( 'JJ_BULK_INSTALLER_LICENSE' ) ) define( 'JJ_BULK_INSTALLER_LICENSE', 'MASTER' );"
            elif plugin_key == 'menu':
                master_const = "if ( ! defined( 'JJ_ADMIN_MENU_EDITOR_LICENSE' ) ) define( 'JJ_ADMIN_MENU_EDITOR_LICENSE', 'MASTER' );"
            elif plugin_key == 'ai':
                master_const = "if ( ! defined( 'JJ_AI_EXTENSION_LICENSE' ) ) define( 'JJ_AI_EXTENSION_LICENSE', 'MASTER' );"
            elif plugin_key == 'woo':
                master_const = "if ( ! defined( 'JJ_WOO_LICENSE_LICENSE' ) ) define( 'JJ_WOO_LICENSE_LICENSE', 'MASTER' );"
            elif plugin_key == 'neural':
                master_const = "if ( ! defined( 'JJ_NEURAL_LINK_LICENSE' ) ) define( 'JJ_NEURAL_LINK_LICENSE', 'MASTER' );"

            if master_const:
                # Insert after the first <?php
                if "LICENSE', 'MASTER'" not in content:
                    content = re.sub(r"(<\?php)", f"\\1\n{master_const}", content, count=1)
            
            # Change Plugin Name
            content = re.sub(r"Plugin Name:(.*)", r"Plugin Name:\1 (Master)", content)

        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)

    def build_core(self):
        print(f"📦 Building Core Editions...")
        
        for edition, config in EDITIONS.items():
            for channel in config['channels']:
                ver = VERSIONS['core']
                if channel == 'beta': ver += '-beta.1'
                elif channel == 'staging': ver += f'-staging.{self.build_id}'

                channel_dir = os.path.join(OUTPUT_ROOT, channel.capitalize())
                temp_dir = os.path.join(channel_dir, 'temp', f'acf-css-manager-{edition}')
                zip_name = f"ACF-CSS{config['suffix']}-v{ver}.zip"
                zip_path = os.path.join(channel_dir, zip_name)

                if os.path.exists(temp_dir): shutil.rmtree(temp_dir)
                os.makedirs(temp_dir)
                
                self.copy_files(SOURCES['core'], temp_dir)
                
                main_file = os.path.join(temp_dir, 'acf-css-really-simple-style-guide.php')
                self.process_core_file(main_file, edition, channel, ver)

                with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
                    for root, dirs, files in os.walk(temp_dir):
                        for file in files:
                            file_path = os.path.join(root, file)
                            arcname = os.path.relpath(file_path, os.path.dirname(temp_dir))
                            zf.write(file_path, arcname)
                
                shutil.rmtree(os.path.dirname(temp_dir))

                self.log_data['builds'].append({
                    'type': 'core',
                    'edition': edition,
                    'channel': channel,
                    'version': ver,
                    'file': zip_name
                })
                print(f"  ✓ {edition.upper()} [{channel}] -> {zip_name}")

    def build_addons(self):
        print(f"📦 Building Add-ons...")
        addons = [
            ('ai', 'ACF-CSS-AI-Extension', 'acf-css-ai-extension', 'acf-css-ai-extension.php'),
            ('neural', 'ACF-CSS-Neural-Link', 'acf-css-neural-link', 'acf-css-neural-link.php'),
            ('woo', 'ACF-CSS-Woo-License', 'acf-css-woo-license', 'acf-css-woo-license.php'),
            ('bulk', 'WP-Bulk-Manager', 'wp-bulk-installer', 'wp-bulk-installer.php'),
            ('menu', 'Admin-Menu-Editor-Lite', 'admin-menu-editor-lite', 'admin-menu-editor-lite.php')
        ]

        # Channels to build for addons
        channels = ['stable', 'beta', 'staging', 'master']

        for key, name_base, folder_name, main_filename in addons:
            for channel in channels:
                ver = VERSIONS[key]
                
                # Suffix for master
                suffix = ""
                if channel == 'master':
                    suffix = "-Master"
                
                # Skip beta for some addons if not needed, but keeping for consistency
                
                channel_dir = os.path.join(OUTPUT_ROOT, channel.capitalize())
                if not os.path.exists(channel_dir): os.makedirs(channel_dir)
                
                temp_dir = os.path.join(channel_dir, 'temp_addon', folder_name)
                if os.path.exists(temp_dir): shutil.rmtree(temp_dir)
                os.makedirs(temp_dir)
                
                # Copy Files
                self.copy_files(SOURCES[key], temp_dir)
                
                # Process File (Inject Master Constant)
                main_file_path = os.path.join(temp_dir, main_filename)
                if os.path.exists(main_file_path):
                    self.process_addon_file(main_file_path, channel, key)
                
                # Zip
                zip_name = f"{name_base}{suffix}-v{ver}.zip"
                zip_path = os.path.join(channel_dir, zip_name)
                
                with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
                    for root, dirs, files in os.walk(temp_dir):
                        for file in files:
                            file_path = os.path.join(root, file)
                            arcname = os.path.relpath(file_path, os.path.dirname(temp_dir))
                            zf.write(file_path, arcname)
                
                shutil.rmtree(os.path.dirname(temp_dir))
                
                self.log_data['builds'].append({
                    'type': 'addon',
                    'edition': key,
                    'channel': channel,
                    'version': ver,
                    'file': zip_name
                })
                
            print(f"  ✓ {name_base} -> Built all channels (incl. Master)")

    def generate_dashboard(self):
        git_info = self._get_git_info()
        
        html_content = f"""
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>JJ Deployment Center - Build {self.build_id}</title>
            <style>
                body {{ font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f0f2f5; padding: 40px; }}
                .container {{ max-width: 1200px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }}
                h1 {{ color: #1a1a1a; border-bottom: 2px solid #eee; padding-bottom: 20px; }}
                .meta {{ color: #666; margin-bottom: 30px; font-size: 0.9em; }}
                .channel-section {{ margin-bottom: 40px; }}
                .channel-title {{ font-size: 1.5em; font-weight: bold; color: #2271b1; margin-bottom: 15px; display: flex; align-items: center; }}
                .channel-badge {{ font-size: 0.5em; text-transform: uppercase; background: #2271b1; color: white; padding: 4px 8px; border-radius: 4px; margin-left: 10px; }}
                .beta .channel-badge {{ background: #e67e22; }}
                .staging .channel-badge {{ background: #8e44ad; }}
                .master .channel-badge {{ background: #c0392b; }}
                table {{ width: 100%; border-collapse: collapse; margin-top: 10px; }}
                th, td {{ text-align: left; padding: 12px; border-bottom: 1px solid #eee; }}
                th {{ background: #f9f9f9; font-weight: 600; color: #444; }}
                tr:hover {{ background: #f8f9fa; }}
                .file-link {{ color: #2271b1; text-decoration: none; font-weight: 500; }}
                .file-link:hover {{ text-decoration: underline; }}
                .log-section {{ background: #2d3436; color: #dfe6e9; padding: 20px; border-radius: 8px; font-family: monospace; margin-top: 40px; }}
            </style>
        </head>
        <body>
            <div class="container">
                <h1>🚀 JJ Deployment Command Center</h1>
                <div class="meta">
                    <strong>Build ID:</strong> {self.build_id}<br>
                    <strong>Timestamp:</strong> {self.build_time.strftime('%Y-%m-%d %H:%M:%S')}<br>
                    <strong>Last Commit:</strong> [{git_info['hash']}] {git_info['commit']}
                </div>
        """

        channels = ['Stable', 'Beta', 'Staging', 'Master']
        for channel in channels:
            class_name = channel.lower()
            html_content += f"""
                <div class="channel-section {class_name}">
                    <div class="channel-title">{channel} Channel <span class="channel-badge">{channel}</span></div>
                    <table>
                        <thead>
                            <tr>
                                <th>Edition/Plugin</th>
                                <th>Version</th>
                                <th>Filename</th>
                            </tr>
                        </thead>
                        <tbody>
            """
            
            # Filter builds for this channel
            channel_builds = [b for b in self.log_data['builds'] if b['channel'] == channel.lower()]
            
            for build in channel_builds:
                html_content += f"""
                            <tr>
                                <td>{build['edition'].upper()}</td>
                                <td>{build['version']}</td>
                                <td><a href="{channel}/{build['file']}" class="file-link">{build['file']}</a></td>
                            </tr>
                """
            
            html_content += """
                        </tbody>
                    </table>
                </div>
            """

        html_content += f"""
                <div class="log-section">
                    <h3>📜 System Logs</h3>
                    <pre>{json.dumps(self.log_data, indent=2)}</pre>
                </div>
            </div>
        </body>
        </html>
        """

        dashboard_path = os.path.join(OUTPUT_ROOT, 'dashboard.html')
        with open(dashboard_path, 'w', encoding='utf-8') as f:
            f.write(html_content)
        
        # Save JSON log
        json_log_path = os.path.join(LOG_DIR, f'build_{self.build_id}.json')
        with open(json_log_path, 'w', encoding='utf-8') as f:
            json.dump(self.log_data, f, indent=4)

        print(f"\n📊 Dashboard Generated: {dashboard_path}")

    def run(self):
        print("="*60)
        print("🔥 JJ DEPLOYMENT SYSTEM (JDS) - INITIATING LAUNCH SEQUENCE")
        print("="*60)
        print(f"Target: {OUTPUT_ROOT}")
        
        self.build_core()
        self.build_addons()
        self.generate_dashboard()
        
        print("="*60)
        print("🚀 MISSION ACCOMPLISHED. READY FOR DEPLOYMENT.")
        print("="*60)

if __name__ == '__main__':
    engine = JJ_Deployment_Engine()
    engine.run()

```

```python
import os
import shutil
import zipfile
import re
from datetime import datetime

# ============================================================
# 설정 (Configuration)
# ============================================================
VERSION_CORE = '8.0.0'
VERSION_NEURAL = '3.9.9'
VERSION_AI = '2.0.1'
VERSION_WOO = '2.0.0'

BASE_OUTPUT_DIR = os.path.join(os.environ['USERPROFILE'], 'Desktop', f'JJ_Distributions_v{VERSION_CORE}_Final')

# 소스 디렉토리 맵핑
SOURCES = {
    'core': 'acf-css-really-simple-style-management-center-master',
    'ai': 'acf-css-ai-extension',
    'neural': 'acf-css-neural-link',
    'woo': 'marketing/wordpress-plugins/acf-css-woo-license',
    'bulk': 'marketing/wordpress-plugins/wp-bulk-installer',
    'menu': 'marketing/wordpress-plugins/admin-menu-editor-lite'
}

# Core Edition 목록 (파일명 접미사 및 내부 코드 치환용)
CORE_EDITIONS = {
    'free':      {'suffix': '',             'license': 'FREE'},
    'basic':     {'suffix': '-Pro-Basic',   'license': 'BASIC'},
    'premium':   {'suffix': '-Pro-Premium', 'license': 'PREMIUM'},
    'unlimited': {'suffix': '-Pro-Unlimited','license': 'UNLIMITED'},
    'partner':   {'suffix': '-Partner',     'license': 'PARTNER'},
    'master':    {'suffix': '-Master',      'license': 'MASTER'}
}

# 제외할 파일/폴더 패턴 (소스 루트 기준 상대 경로로 매칭)
EXCLUDE_PATTERNS = [
    r'^\.git', r'^\.vscode', r'^\.idea', r'__pycache__', r'\.DS_Store$',
    r'^tests', r'^phpunit\.xml', r'^composer\.json', r'node_modules',
    r'^package\.json', r'^package-lock\.json', r'^gulpfile\.js', 
    r'^\.editorconfig', r'^README\.md', r'\.bak$', r'local-server/venv',
]

# ============================================================
# 유틸리티 함수
# ============================================================
def clean_create_dir(path):
    if os.path.exists(path):
        try:
            shutil.rmtree(path, ignore_errors=True)
        except: pass
    os.makedirs(path, exist_ok=True)

def copy_files(src, dst, excludes=None):
    if excludes is None: excludes = EXCLUDE_PATTERNS
    
    print(f"  - Copying from {src} to {dst}...")
    count = 0
    for root, dirs, files in os.walk(src):
        # 소스 루트 기준 상대 경로 계산
        rel_root = os.path.relpath(root, src)
        if rel_root == ".": rel_root = ""
        
        # 제외 폴더 필터링 (패턴은 rel_root 기준)
        dirs[:] = [d for d in dirs if not any(re.search(p, os.path.join(rel_root, d).replace('\\', '/')) for p in excludes)]
        
        for file in files:
            rel_file_path = os.path.join(rel_root, file).replace('\\', '/')
            if any(re.search(p, rel_file_path) for p in excludes):
                continue
                
            src_file = os.path.join(root, file)
            dst_file = os.path.join(dst, rel_file_path)
            
            os.makedirs(os.path.dirname(dst_file), exist_ok=True)
            shutil.copy2(src_file, dst_file)
            count += 1
    print(f"    ✓ Copied {count} files.")
    
    # 필수 폴더 체크 (Core의 경우)
    if 'acf-css-manager' in dst and count < 10:
        print(f"    ⚠️ WARNING: Very few files copied to {dst}. Potential exclusion bug!")

def create_zip(source, zip_path, folder_name_in_zip):
    print(f"  - Zipping {source} to {zip_path} (Folder: {folder_name_in_zip})")
    with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for root, dirs, files in os.walk(source):
            for file in files:
                file_path = os.path.join(root, file)
                # ZIP 내부의 상대 경로: folder_name_in_zip / relative_path_from_source
                rel_path = os.path.relpath(file_path, source)
                arcname = os.path.join(folder_name_in_zip, rel_path)
                zipf.write(file_path, arcname)

# ============================================================
# 빌드 실행
# ============================================================
def main():
    print("=" * 60)
    print(f"🚀 3J Labs Deployment System (Robust Build)")
    print(f"   Target: {BASE_OUTPUT_DIR}")
    print("=" * 60)
    
    clean_create_dir(BASE_OUTPUT_DIR)
    temp_dir = os.path.join(BASE_OUTPUT_DIR, "temp")
    os.makedirs(temp_dir, exist_ok=True)
    
    # 1. Core Editions
    print("\n📦 [Core] Building Editions...")
    for edition, config in CORE_EDITIONS.items():
        print(f"  Processing {edition}...")
        
        # 폴더명 결정
        folder_name = f"acf-css-manager-{edition}" if edition != 'free' else "acf-css-manager"
        work_dir = os.path.join(temp_dir, folder_name)
        
        # 파일 복사
        copy_files(SOURCES['core'], work_dir)
        
        # 메인 파일 수정
        main_file = os.path.join(work_dir, 'acf-css-really-simple-style-guide.php')
        if os.path.exists(main_file):
            with open(main_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # 플러그인 이름 변경
            name_suffix = ""
            if edition != 'free':
                if edition == 'master': name_suffix = " (Master)"
                elif edition in ['basic', 'premium', 'unlimited']: name_suffix = " PRO"
                else: name_suffix = f" ({edition.capitalize()})"
            
            new_name = f"ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager{name_suffix}"
            content = re.sub(r"Plugin Name:.*", f"Plugin Name:       {new_name}", content)
            
            # 상수 변경
            content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_EDITION',\s*'[^']+'\s*\);", f"define( 'JJ_STYLE_GUIDE_EDITION', '{edition}' );", content)
            content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_LICENSE_TYPE',\s*'[^']+'\s*\);", f"define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', '{config['license']}' );", content)
            
            with open(main_file, 'w', encoding='utf-8') as f:
                f.write(content)
        
        # ZIP 생성
        zip_name = f"ACF-CSS{config['suffix']}-v{VERSION_CORE}.zip"
        create_zip(work_dir, os.path.join(BASE_OUTPUT_DIR, zip_name), folder_name)

    # 2. Add-ons & Infrastructure
    addons = [
        ('ai', 'ACF-CSS-AI-Extension', VERSION_AI, 'acf-css-ai-extension'),
        ('neural', 'ACF-CSS-Neural-Link', VERSION_NEURAL, 'acf-css-neural-link'),
        ('woo', 'ACF-CSS-Woo-License', VERSION_WOO, 'acf-css-woo-license'),
        ('bulk', 'WP-Bulk-Manager', '2.2.2', 'wp-bulk-installer'),
        ('menu', 'Admin-Menu-Editor-Lite', '2.0.0', 'admin-menu-editor-lite')
    ]
    
    for key, zip_base, ver, target_folder in addons:
        print(f"\n📦 [{key.upper()}] Building...")
        src = SOURCES[key]
        work_dir = os.path.join(temp_dir, target_folder)
        
        # 복사 (Add-on의 경우 소스 경로 자체가 marketing을 포함하므로 제외 패턴이 rel_root 기준임을 보장)
        copy_files(src, work_dir)
        
        # ZIP 생성
        zip_name = f"{zip_base}-v{ver}.zip"
        create_zip(work_dir, os.path.join(BASE_OUTPUT_DIR, zip_name), target_folder)

    # Cleanup
    try:
        shutil.rmtree(temp_dir, ignore_errors=True)
    except: pass
    
    print("\n" + "=" * 60)
    print(f"🎉 All Systems Go! Build Verified.")
    print("=" * 60)

if __name__ == '__main__':
    main()

```

```python
import os
import re

# 파일 경로 및 새 버전 설정
FILES = {
    'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php': '8.0.0',
    'acf-css-neural-link/acf-css-neural-link.php': '3.9.9',
    'acf-css-ai-extension/acf-css-ai-extension.php': '2.0.1',
    'marketing/wordpress-plugins/acf-css-woo-license/acf-css-woo-license.php': '2.0.0',
    'marketing/wordpress-plugins/wp-bulk-installer/wp-bulk-installer.php': '2.2.2',
    'marketing/wordpress-plugins/admin-menu-editor-lite/admin-menu-editor-lite.php': '2.0.0',
    'marketing/wordpress-plugins/acf-css-brevo-integration/acf-css-brevo-integration.php': '2.0.0',
    'marketing/wordpress-theme/acf-css-landing/style.css': '2.0.0'
}

AUTHOR_OLD_PATTERNS = [
    r'Author:\s+Jay & Jenny Labs',
    r'Author:\s+J&J Labs',
    r'Author:\s+JJ Labs',
    r'Author: Jay & Jenny Labs',
    r'Author: J&J Labs',
    r'Author: JJ Labs'
]

NEW_AUTHOR = 'Author:            3J Labs'
CREDIT_COMMENT = '\n * Created by:        Jay & Jason & Jenny'

def update_file(path, new_version):
    if not os.path.exists(path):
        print(f"❌ File not found: {path}")
        return

    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Author 수정
    for pattern in AUTHOR_OLD_PATTERNS:
        content = re.sub(pattern, NEW_AUTHOR, content)
    
    # 2. Version 수정
    content = re.sub(r'Version:\s*[\d\.]+', f'Version:           {new_version}', content)
    
    # 3. 상수 버전 수정 (PHP 파일만)
    if path.endswith('.php'):
        content = re.sub(r"define\(\s*'[^']+_VERSION',\s*'[^']+'\s*\);", lambda m: re.sub(r"'[^']+'", f"'{new_version}'", m.group(0)), content)

    # 4. Credit 추가 (Author 밑에 없으면 추가)
    if 'Created by:' not in content:
        content = content.replace(NEW_AUTHOR, NEW_AUTHOR + CREDIT_COMMENT)

    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✅ Updated {os.path.basename(path)} to v{new_version}")

if __name__ == "__main__":
    print("🚀 Starting Update Sequence...")
    for path, ver in FILES.items():
        update_file(path, ver)
    print("🎉 Update Complete.")


```

### 다음 단계(중요 작업) 완료: **AI Extension WebGPU(WebLLM) “진짜 동작” MVP**

이번 단계에서 **“WebGPU 선택 시 유효하지 않은 Provider”로 막히던 구조적 문제**를 근본적으로 해소하고, **브라우저에서 바로 생성**되도록 연결했습니다.

## 핵심 변경 사항
- **WebGPU Provider 루트 정리(근본 원인 해결)**  
  - 기존: UI는 `webgpu`인데 PHP Provider는 없어서 생성 시 **“유효하지 않은 Provider”** 오류 가능  
  - 수정: `JJ_AI_Provider_WebGPU` 추가(서버로 호출되면 안내 메시지 반환)
  - 파일: `acf-css-ai-extension/includes/providers/class-jj-ai-provider-webgpu.php`

- **브라우저 로컬 생성(WebLLM) 실제 구현**
  - **모델 로드 버튼**으로 WebLLM 엔진을 로드(진행률 표시 포함)
  - “AI 제안 생성” 클릭 시, Provider가 `webgpu`면 **AJAX 대신 브라우저에서 바로 생성**
  - 응답을 **JSON으로 파싱**(코드블록/잡텍스트 섞여도 JSON 추출 시도) → `settings_patch` 검증 → 기존 Diff/적용 흐름 그대로 사용
  - 파일: `acf-css-ai-extension/assets/ai-extension.js`

- **UI/UX 정리**
  - Provider 전환 시 **API Key 행/로컬(WebGPU) 행/설명 문구** 자동 전환
  - WebGPU 미지원 브라우저는 즉시 안내 및 로드 버튼 비활성화
  - 파일: `acf-css-ai-extension/assets/ai-extension.css`

## 버전/배포 반영
- AI Extension 버전 **2.0.0 → 2.0.1**
  - 파일: `acf-css-ai-extension/acf-css-ai-extension.php`
- 배포 시스템 반영 및 재빌드 완료:
  - `jj_deployment_system.py`에서 AI 버전 **2.0.1**로 반영 후 빌드 재실행
  - 대시보드에 **`ACF-CSS-AI-Extension-v2.0.1.zip`**로 갱신됨  
  - 위치: `C:\Users\computer\Desktop\JJ_Distributions_v8.0.0_Master_Control\dashboard.html`

원하시면 다음 단계로는 **(1) WebGPU 모델 선택 UI(“Gemma 계열 우선/경량 모델 자동 선택”을 실제 드롭다운으로 노출)** 또는 **(2) 생성된 팔레트를 ACF CSS의 추천 팔레트/버튼/폼까지 자동 매핑하는 ‘원클릭 적용 프리셋’**으로 바로 이어가겠습니다.
