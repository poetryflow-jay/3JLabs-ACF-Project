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
    private $ai_palette_presets_option_key = 'jj_style_guide_ai_palette_presets';
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
        add_action( 'wp_ajax_jj_ai_save_palette_preset', array( $this, 'ajax_save_palette_preset' ) );
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
        
        // [v3.2.0] UI System 2026 Enhancement
        $enhanced_css_path = JJ_ACF_CSS_AI_EXT_PATH . 'assets/css/jj-ai-extension-enhanced-2026.css';
        if ( file_exists( $enhanced_css_path ) ) {
            $css_version = JJ_ACF_CSS_AI_EXT_VERSION . '.' . filemtime( $enhanced_css_path );
            wp_enqueue_style(
                'jj-ai-extension-enhanced-2026',
                JJ_ACF_CSS_AI_EXT_URL . 'assets/css/jj-ai-extension-enhanced-2026.css',
                array( 'jj-acf-css-ai-ext' ),
                $css_version
            );
        }

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
                // API Key는 노출하지 않음 (필요한 최소 설정만 제공)
                'ai_settings' => array(
                    'provider' => (string) ( get_option( $this->option_key, array() )['provider'] ?? 'openai' ),
                    'webgpu_model_id' => (string) ( get_option( $this->option_key, array() )['webgpu_model_id'] ?? '' ),
                ),
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
        $webgpu_model_id = isset( $settings['webgpu_model_id'] ) ? $settings['webgpu_model_id'] : '';
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
                                    <span class="dashicons dashicons-download"></span> 선택한 모델 로드 (브라우저 캐시)
                                </button>
                                <div style="margin-top:12px;">
                                    <label for="jj-webgpu-model"><strong><?php echo esc_html__( '모델 선택', 'acf-css-ai-extension' ); ?></strong></label><br>
                                    <select id="jj-webgpu-model" name="webgpu_model_id" class="regular-text" style="max-width: 520px;">
                                        <option value="<?php echo esc_attr( $webgpu_model_id ); ?>">
                                            <?php echo esc_html__( '모델 목록 로딩 중...', 'acf-css-ai-extension' ); ?>
                                        </option>
                                    </select>
                                    <p class="description" style="margin-top:6px;">
                                        <?php echo esc_html__( '권장: Gemma 계열. 모델 변경 후에는 다시 로드가 필요할 수 있습니다.', 'acf-css-ai-extension' ); ?>
                                    </p>
                                </div>
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
                    <button type="button" class="button" id="jj-ai-cancel" style="display:none;">
                        <?php echo esc_html__( '생성 중지', 'acf-css-ai-extension' ); ?>
                    </button>
                    <span class="spinner" id="jj-ai-spinner" style="float:none; visibility:hidden;"></span>
                </div>
                <div id="jj-ai-stream-area" style="display:none; margin-top: 12px;">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                        <strong><?php echo esc_html__( '스트리밍 출력(진행 로그)', 'acf-css-ai-extension' ); ?></strong>
                        <span class="description"><?php echo esc_html__( 'WebGPU 모드에서 모델 출력이 실시간으로 표시됩니다. (완료 후 자동으로 JSON을 파싱합니다.)', 'acf-css-ai-extension' ); ?></span>
                    </div>
                    <div class="jj-ai-stream-toolbar">
                        <span id="jj-ai-stream-stage" class="jj-ai-stage" data-kind="idle" aria-live="polite">
                            <?php echo esc_html__( '대기', 'acf-css-ai-extension' ); ?>
                        </span>
                        <span class="jj-ai-metric"><strong><?php echo esc_html__( '경과', 'acf-css-ai-extension' ); ?></strong> <span id="jj-ai-stream-elapsed">00:00</span></span>
                        <span class="jj-ai-metric"><strong><?php echo esc_html__( '속도', 'acf-css-ai-extension' ); ?></strong> <span id="jj-ai-stream-speed">-</span></span>
                        <span class="jj-ai-metric"><strong><?php echo esc_html__( 'ETA', 'acf-css-ai-extension' ); ?></strong> <span id="jj-ai-stream-eta">-</span></span>
                        <span class="jj-ai-metric"><strong><?php echo esc_html__( '출력', 'acf-css-ai-extension' ); ?></strong> <span id="jj-ai-stream-out">0</span></span>
                        <label class="jj-ai-autoscroll">
                            <input type="checkbox" id="jj-ai-stream-autoscroll" checked>
                            <?php echo esc_html__( '자동 스크롤', 'acf-css-ai-extension' ); ?>
                        </label>
                        <button type="button" class="button" id="jj-ai-stream-copy"><?php echo esc_html__( '복사', 'acf-css-ai-extension' ); ?></button>
                        <button type="button" class="button" id="jj-ai-stream-clear"><?php echo esc_html__( '지우기', 'acf-css-ai-extension' ); ?></button>
                        <button type="button" class="button" id="jj-ai-stream-retry"><?php echo esc_html__( '재시도', 'acf-css-ai-extension' ); ?></button>
                        <button type="button" class="button" id="jj-ai-stream-reparse"><?php echo esc_html__( 'JSON 재파싱', 'acf-css-ai-extension' ); ?></button>
                    </div>
                    <pre id="jj-ai-stream" class="jj-ai-stream"></pre>
                </div>
                <div class="jj-ai-apply-presets" style="margin-top: 10px;">
                    <label style="display:inline-flex; align-items:center; gap:8px;">
                        <input type="checkbox" id="jj-ai-apply-brand-preset" checked>
                        <strong><?php echo esc_html__( '브랜드 팔레트 기준으로 버튼/폼/링크까지 자동 맞춤(권장)', 'acf-css-ai-extension' ); ?></strong>
                    </label>
                    <p class="description" style="margin-top:6px;">
                        <?php echo esc_html__( '코어의 “브랜드 팔레트 일괄 적용”과 동일한 규칙으로, 브랜드 Primary 색상을 기준으로 버튼/링크/폼 포커스를 정렬합니다.', 'acf-css-ai-extension' ); ?>
                    </p>
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
        $webgpu_model_id = isset( $_POST['webgpu_model_id'] ) ? sanitize_text_field( $_POST['webgpu_model_id'] ) : '';

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
            'webgpu_model_id' => $webgpu_model_id,
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

    /**
     * AI가 생성한 팔레트를 "추천 팔레트 카드"로 저장 (코어 presets.js에서 재사용)
     *
     * @return void
     */
    public function ajax_save_palette_preset() {
        check_ajax_referer( 'jj_ai_ext_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        $name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
        $note = isset( $_POST['note'] ) ? sanitize_text_field( wp_unslash( $_POST['note'] ) ) : '';
        $preset_json = isset( $_POST['preset_json'] ) ? wp_unslash( $_POST['preset_json'] ) : '';

        if ( '' === $name ) {
            wp_send_json_error( array( 'message' => '프리셋 이름이 비어있습니다.' ) );
        }
        if ( '' === $preset_json ) {
            wp_send_json_error( array( 'message' => 'preset_json이 비어있습니다.' ) );
        }

        $preset = json_decode( $preset_json, true );
        if ( ! is_array( $preset ) ) {
            wp_send_json_error( array( 'message' => 'preset_json 파싱에 실패했습니다.' ) );
        }

        $brand = isset( $preset['brand'] ) && is_array( $preset['brand'] ) ? $preset['brand'] : array();
        $system = isset( $preset['system'] ) && is_array( $preset['system'] ) ? $preset['system'] : array();

        $sanitize_hex = function( $value, $fallback = '' ) {
            $v = is_string( $value ) ? $value : '';
            $v = trim( $v );
            if ( '' === $v ) {
                $v = (string) $fallback;
            }
            if ( function_exists( 'sanitize_hex_color' ) ) {
                $hex = sanitize_hex_color( $v );
                return $hex ? strtoupper( $hex ) : '';
            }
            return $v;
        };

        $primary = $sanitize_hex( $brand['primary'] ?? '' );
        $secondary = $sanitize_hex( $brand['secondary'] ?? '' );

        if ( '' === $primary ) {
            wp_send_json_error( array( 'message' => '브랜드 Primary 색상이 비어있습니다.' ) );
        }
        if ( '' === $secondary ) {
            // secondary는 없을 수 있으므로 primary 기반으로 폴백
            $secondary = $primary;
        }

        $new_id = 'ai-' . gmdate( 'Ymd-His' ) . '-' . substr( md5( wp_generate_uuid4() . microtime( true ) ), 0, 8 );
        $created_at = function_exists( 'current_time' ) ? current_time( 'mysql' ) : gmdate( 'Y-m-d H:i:s' );

        $saved = array(
            'id' => $new_id,
            'name' => $name,
            'tags' => array( 'AI', '저장됨' ),
            'note' => $note,
            'brand' => array(
                'primary' => $primary,
                'secondary' => $secondary,
                'primary_hover' => $sanitize_hex( $brand['primary_hover'] ?? '', $primary ),
                'secondary_hover' => $sanitize_hex( $brand['secondary_hover'] ?? '', $secondary ),
            ),
            'system' => array(
                'site_bg' => $sanitize_hex( $system['site_bg'] ?? '' ),
                'content_bg' => $sanitize_hex( $system['content_bg'] ?? '' ),
                'text_color' => $sanitize_hex( $system['text_color'] ?? '' ),
                'link_color' => $sanitize_hex( $system['link_color'] ?? '', $primary ),
            ),
            'source' => 'ai_extension',
            'created_at' => $created_at,
            'prompt' => $note,
        );

        $existing = get_option( $this->ai_palette_presets_option_key, array() );
        if ( ! is_array( $existing ) ) {
            $existing = array();
        }

        array_unshift( $existing, $saved );

        // 최대 20개 유지
        $existing = array_slice( $existing, 0, 20 );

        update_option( $this->ai_palette_presets_option_key, $existing, false );

        wp_send_json_success(
            array(
                'message' => '추천 팔레트에 저장되었습니다.',
                'preset' => $saved,
                'count' => count( $existing ),
            )
        );
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
