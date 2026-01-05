<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Neural Link Version Manager
 *
 * 관리자가 새 버전을 등록하고 에디션별 ZIP 파일을 업로드하는 관리자 UI입니다.
 *
 * [v6.4.0] 다중 플러그인 지원 추가 - 3J Labs 패밀리 전체 관리
 *
 * @since v3.0.0
 * @version 6.4.0
 */
class JJ_Neural_Link_Version_Manager {

    private static $instance = null;

    /**
     * 관리 대상 플러그인 목록
     * @var array
     */
    private $plugins = array();

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_plugins();
        add_action( 'admin_menu', array( $this, 'add_menu_page' ), 20 );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_jj_save_plugin_version', array( $this, 'ajax_save_plugin_version' ) );
    }

    /**
     * 관리 대상 플러그인 초기화
     * [v6.4.0] 3J Labs 패밀리 전체 플러그인 목록
     */
    private function init_plugins() {
        $this->plugins = array(
            'acf-css' => array(
                'name' => 'ACF CSS Style Management Center',
                'slug' => 'acf-css-really-simple-style-management-center-master',
                'icon' => '🎨',
                'category' => 'core',
                'editions' => array( 'free', 'basic', 'premium', 'unlimited', 'partner', 'master' ),
            ),
            'wp-bulk-manager' => array(
                'name' => 'WP Bulk Manager',
                'slug' => 'wp-bulk-manager',
                'icon' => '🚀',
                'category' => 'family',
                'editions' => array( 'free', 'master' ),
            ),
            'neural-link' => array(
                'name' => 'ACF CSS Neural Link',
                'slug' => 'acf-css-neural-link',
                'icon' => '🔗',
                'category' => 'addon',
                'editions' => array( 'master' ),
            ),
            'nudge-flow' => array(
                'name' => '넛지 플로우 (Nudge Flow)',
                'slug' => 'acf-nudge-flow',
                'icon' => '🎯',
                'category' => 'family',
                'editions' => array( 'free', 'master' ),
            ),
            'code-snippets' => array(
                'name' => 'ACF Code Snippets Box',
                'slug' => 'acf-code-snippets-box',
                'icon' => '📦',
                'category' => 'family',
                'editions' => array( 'free', 'master' ),
            ),
            'user-journey' => array(
                'name' => 'ACF User Journey Analytics',
                'slug' => 'acf-user-journey-analytics',
                'icon' => '📊',
                'category' => 'free',
                'editions' => array( 'free' ),
            ),
            'mail-smtp' => array(
                'name' => 'ACF Mail SMTP',
                'slug' => 'acf-mail-smtp',
                'icon' => '📧',
                'category' => 'family',
                'editions' => array( 'free', 'master' ),
            ),
            'marketing-dashboard' => array(
                'name' => '3J Labs Marketing Dashboard',
                'slug' => 'jj-marketing-automation-dashboard',
                'icon' => '📈',
                'category' => 'family',
                'editions' => array( 'master' ),
            ),
            'seo-aeo' => array(
                'name' => 'WP Bulk SEO & AEO',
                'slug' => 'wp-bulk-seo-aeo',
                'icon' => '🔍',
                'category' => 'seo',
                'editions' => array( 'free', 'master' ),
            ),
            'woo-toolkit' => array(
                'name' => 'ACF CSS WooCommerce Toolkit',
                'slug' => 'acf-css-woocommerce-toolkit',
                'icon' => '🛒',
                'category' => 'family',
                'editions' => array( 'master' ),
            ),
            'ai-extension' => array(
                'name' => 'ACF CSS AI Extension',
                'slug' => 'acf-css-ai-extension',
                'icon' => '🤖',
                'category' => 'addon',
                'editions' => array( 'master' ),
            ),
            'menu-editor' => array(
                'name' => 'ACF Admin Menu Editor Pro',
                'slug' => 'admin-menu-editor-pro',
                'icon' => '🛠️',
                'category' => 'family',
                'editions' => array( 'master' ),
            ),
            'woo-license' => array(
                'name' => 'ACF CSS Woo License Bridge',
                'slug' => 'acf-css-woo-license',
                'icon' => '🔑',
                'category' => 'addon',
                'editions' => array( 'master' ),
            ),
            'analytics-dashboard' => array(
                'name' => 'JJ Analytics Dashboard',
                'slug' => 'jj-analytics-dashboard',
                'icon' => '📉',
                'category' => 'family',
                'editions' => array( 'master' ),
            ),
        );
    }

    public function add_menu_page() {
        add_submenu_page(
            'jj-license-manager',
            '플러그인 배포 관리',
            '📦 배포 관리',
            'manage_options',
            'jj-neural-link-versions',
            array( $this, 'render_page' )
        );
    }

    public function register_settings() {
        // 각 플러그인별 버전 및 파일 URL 설정 등록
        foreach ( $this->plugins as $key => $plugin ) {
            register_setting( 'jj_neural_link_versions', "jj_plugin_{$key}_version" );
            foreach ( $plugin['editions'] as $edition ) {
                register_setting( 'jj_neural_link_versions', "jj_plugin_{$key}_file_{$edition}" );
            }
        }

        // 레거시 호환 (기존 ACF CSS 설정)
        register_setting( 'jj_neural_link_versions', 'jj_neural_link_latest_version' );
        $editions = array( 'free', 'basic', 'premium', 'unlimited', 'partner', 'master' );
        foreach ( $editions as $edition ) {
            register_setting( 'jj_neural_link_versions', "jj_neural_link_file_{$edition}" );
        }
    }

    public function enqueue_scripts( $hook ) {
        if ( 'jj_license_page_jj-neural-link-versions' !== $hook ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style( 'jj-version-manager', plugins_url( '../../assets/css/version-manager.css', __FILE__ ), array(), '6.4.0' );
    }

    public function render_page() {
        $current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'all';
        ?>
        <div class="wrap jj-version-manager">
            <h1>📦 Neural Link: 플러그인 배포 관리</h1>
            <p>3J Labs 플러그인 패밀리의 버전과 배포 파일을 중앙에서 관리합니다.</p>

            <!-- 카테고리 탭 -->
            <nav class="nav-tab-wrapper">
                <a href="?post_type=jj_license&page=jj-neural-link-versions&tab=all"
                   class="nav-tab <?php echo $current_tab === 'all' ? 'nav-tab-active' : ''; ?>">
                    📋 전체 (<?php echo count( $this->plugins ); ?>)
                </a>
                <a href="?post_type=jj_license&page=jj-neural-link-versions&tab=core"
                   class="nav-tab <?php echo $current_tab === 'core' ? 'nav-tab-active' : ''; ?>">
                    🛡️ Core
                </a>
                <a href="?post_type=jj_license&page=jj-neural-link-versions&tab=addon"
                   class="nav-tab <?php echo $current_tab === 'addon' ? 'nav-tab-active' : ''; ?>">
                    🧩 Addon
                </a>
                <a href="?post_type=jj_license&page=jj-neural-link-versions&tab=family"
                   class="nav-tab <?php echo $current_tab === 'family' ? 'nav-tab-active' : ''; ?>">
                    👨‍👩‍👧‍👦 Family
                </a>
                <a href="?post_type=jj_license&page=jj-neural-link-versions&tab=free"
                   class="nav-tab <?php echo $current_tab === 'free' ? 'nav-tab-active' : ''; ?>">
                    🎁 Free
                </a>
                <a href="?post_type=jj_license&page=jj-neural-link-versions&tab=seo"
                   class="nav-tab <?php echo $current_tab === 'seo' ? 'nav-tab-active' : ''; ?>">
                    🔍 SEO
                </a>
            </nav>

            <div class="jj-plugins-grid">
                <?php foreach ( $this->plugins as $key => $plugin ) :
                    if ( $current_tab !== 'all' && $plugin['category'] !== $current_tab ) continue;
                    $version = get_option( "jj_plugin_{$key}_version", '' );
                ?>
                <div class="jj-plugin-card" data-plugin="<?php echo esc_attr( $key ); ?>">
                    <div class="jj-plugin-header">
                        <span class="jj-plugin-icon"><?php echo $plugin['icon']; ?></span>
                        <div class="jj-plugin-info">
                            <h3><?php echo esc_html( $plugin['name'] ); ?></h3>
                            <span class="jj-plugin-slug"><?php echo esc_html( $plugin['slug'] ); ?></span>
                        </div>
                        <span class="jj-category-badge jj-cat-<?php echo esc_attr( $plugin['category'] ); ?>">
                            <?php echo strtoupper( $plugin['category'] ); ?>
                        </span>
                    </div>

                    <div class="jj-plugin-body">
                        <div class="jj-version-row">
                            <label>현재 버전:</label>
                            <input type="text"
                                   class="jj-version-input"
                                   data-plugin="<?php echo esc_attr( $key ); ?>"
                                   value="<?php echo esc_attr( $version ); ?>"
                                   placeholder="예: 23.0.9" />
                            <?php if ( $version ) : ?>
                                <span class="jj-version-badge">v<?php echo esc_html( $version ); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="jj-editions-list">
                            <?php foreach ( $plugin['editions'] as $edition ) :
                                $file_url = get_option( "jj_plugin_{$key}_file_{$edition}", '' );
                            ?>
                            <div class="jj-edition-row">
                                <span class="jj-edition-label jj-ed-<?php echo esc_attr( $edition ); ?>">
                                    <?php echo strtoupper( $edition ); ?>
                                </span>
                                <input type="text"
                                       class="jj-file-input"
                                       id="jj_plugin_<?php echo esc_attr( $key ); ?>_file_<?php echo esc_attr( $edition ); ?>"
                                       data-plugin="<?php echo esc_attr( $key ); ?>"
                                       data-edition="<?php echo esc_attr( $edition ); ?>"
                                       value="<?php echo esc_attr( $file_url ); ?>"
                                       placeholder="ZIP 파일 URL" />
                                <button type="button" class="button jj-upload-btn"
                                        data-target="jj_plugin_<?php echo esc_attr( $key ); ?>_file_<?php echo esc_attr( $edition ); ?>">
                                    📁
                                </button>
                                <?php if ( $file_url ) : ?>
                                    <a href="<?php echo esc_url( $file_url ); ?>" target="_blank" class="jj-download-link">✓</a>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="jj-plugin-actions">
                            <button type="button" class="button button-primary jj-save-plugin-btn"
                                    data-plugin="<?php echo esc_attr( $key ); ?>">
                                💾 저장
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- 일괄 업로드 섹션 -->
            <div class="jj-bulk-section">
                <h2>🚀 일괄 배포</h2>
                <p>모든 플러그인을 한 번에 저장하거나, 일괄 업로드합니다.</p>
                <button type="button" class="button button-hero jj-save-all-btn">
                    💾 모든 변경사항 저장
                </button>
            </div>
        </div>

        <style>
        .jj-version-manager { max-width: 1400px; }
        .jj-plugins-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .jj-plugin-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .jj-plugin-header {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        .jj-plugin-icon { font-size: 2rem; margin-right: 12px; }
        .jj-plugin-info { flex: 1; }
        .jj-plugin-info h3 { margin: 0 0 4px 0; font-size: 14px; }
        .jj-plugin-slug { font-size: 11px; color: #666; font-family: monospace; }
        .jj-category-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .jj-cat-core { background: #d4edda; color: #155724; }
        .jj-cat-addon { background: #f8d7da; color: #721c24; }
        .jj-cat-family { background: #cce5ff; color: #004085; }
        .jj-cat-free { background: #d1ecf1; color: #0c5460; }
        .jj-cat-seo { background: #fff3cd; color: #856404; }

        .jj-plugin-body { padding: 15px; }
        .jj-version-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        .jj-version-input { width: 100px; }
        .jj-version-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .jj-editions-list { margin-bottom: 15px; }
        .jj-edition-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        .jj-edition-label {
            width: 70px;
            text-align: center;
            padding: 4px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .jj-ed-free { background: #28a745; color: #fff; }
        .jj-ed-basic { background: #17a2b8; color: #fff; }
        .jj-ed-premium { background: #ffc107; color: #000; }
        .jj-ed-unlimited { background: #fd7e14; color: #fff; }
        .jj-ed-partner { background: #6f42c1; color: #fff; }
        .jj-ed-master { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; }

        .jj-file-input { flex: 1; font-size: 12px; }
        .jj-download-link { color: #28a745; font-weight: bold; text-decoration: none; }

        .jj-plugin-actions { text-align: right; }

        .jj-bulk-section {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
        }
        .button-hero { font-size: 16px !important; padding: 12px 30px !important; }
        </style>

        <script>
        jQuery(document).ready(function($) {
            // 미디어 업로더
            $('.jj-upload-btn').click(function(e) {
                e.preventDefault();
                var targetId = $(this).data('target');
                var $input = $('#' + targetId);

                var frame = wp.media({
                    title: 'ZIP 파일 선택',
                    multiple: false,
                    library: { type: 'application/zip' }
                });

                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $input.val(attachment.url);
                });

                frame.open();
            });

            // 개별 플러그인 저장
            $('.jj-save-plugin-btn').click(function() {
                var pluginKey = $(this).data('plugin');
                var $card = $(this).closest('.jj-plugin-card');
                var $btn = $(this);

                var data = {
                    action: 'jj_save_plugin_version',
                    plugin_key: pluginKey,
                    version: $card.find('.jj-version-input').val(),
                    files: {},
                    nonce: '<?php echo wp_create_nonce( 'jj_save_plugin_version' ); ?>'
                };

                $card.find('.jj-file-input').each(function() {
                    var edition = $(this).data('edition');
                    data.files[edition] = $(this).val();
                });

                $btn.prop('disabled', true).text('저장 중...');

                $.post(ajaxurl, data, function(response) {
                    if (response.success) {
                        $btn.text('✓ 저장됨');
                        setTimeout(function() {
                            $btn.prop('disabled', false).text('💾 저장');
                        }, 2000);
                    } else {
                        alert('저장 실패: ' + response.data.message);
                        $btn.prop('disabled', false).text('💾 저장');
                    }
                });
            });

            // 전체 저장
            $('.jj-save-all-btn').click(function() {
                $('.jj-save-plugin-btn').each(function(i) {
                    var $btn = $(this);
                    setTimeout(function() {
                        $btn.click();
                    }, i * 300);
                });
            });
        });
        </script>
        <?php
    }

    /**
     * AJAX: 플러그인 버전 정보 저장
     */
    public function ajax_save_plugin_version() {
        check_ajax_referer( 'jj_save_plugin_version', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        $plugin_key = sanitize_text_field( $_POST['plugin_key'] );
        $version = sanitize_text_field( $_POST['version'] );
        $files = isset( $_POST['files'] ) ? $_POST['files'] : array();

        if ( ! isset( $this->plugins[ $plugin_key ] ) ) {
            wp_send_json_error( array( 'message' => '알 수 없는 플러그인입니다.' ) );
        }

        // 버전 저장
        update_option( "jj_plugin_{$plugin_key}_version", $version );

        // 파일 URL 저장
        foreach ( $files as $edition => $url ) {
            $edition = sanitize_text_field( $edition );
            $url = esc_url_raw( $url );
            update_option( "jj_plugin_{$plugin_key}_file_{$edition}", $url );
        }

        // ACF CSS인 경우 레거시 옵션도 동기화
        if ( $plugin_key === 'acf-css' ) {
            update_option( 'jj_neural_link_latest_version', $version );
            foreach ( $files as $edition => $url ) {
                update_option( "jj_neural_link_file_{$edition}", esc_url_raw( $url ) );
            }
        }

        wp_send_json_success( array(
            'message' => '저장되었습니다.',
            'plugin' => $plugin_key,
            'version' => $version
        ) );
    }

    /**
     * 플러그인 다운로드 URL 가져오기
     *
     * @param string $plugin_key 플러그인 키
     * @param string $edition 에디션
     * @return string|false URL 또는 false
     */
    public function get_download_url( $plugin_key, $edition = 'master' ) {
        $url = get_option( "jj_plugin_{$plugin_key}_file_{$edition}", '' );
        return ! empty( $url ) ? $url : false;
    }

    /**
     * 플러그인 최신 버전 가져오기
     *
     * @param string $plugin_key 플러그인 키
     * @return string 버전 문자열
     */
    public function get_latest_version( $plugin_key ) {
        return get_option( "jj_plugin_{$plugin_key}_version", '' );
    }

    /**
     * 전체 플러그인 목록 가져오기
     *
     * @return array
     */
    public function get_plugins() {
        return $this->plugins;
    }
}
