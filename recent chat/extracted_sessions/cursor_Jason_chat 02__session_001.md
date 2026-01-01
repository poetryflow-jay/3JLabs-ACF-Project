---

**User**

스타일 센터의 직관적이지 않은 UI/UX를 개선하는 작업, 그리고 실험실 센터 등 다른 플러그인 기능 페이지 내에서의 UI가 전반적으로 텍스트 위주이고 버튼이나 색상으로 '보여주기'가 잘 안 되어 있다. 시각화가 잘 되어 있지 않아서, 기능을 일일이 스크롤해서 내려 보거나, 지원되는 스타일 수정 영역을 하나하나 맨 아래까지 내려서 확인해야 한다는 문제점도 있고, 현재 컬러 값을 자동으로 불러오지 못한다거나, 또한 앞으로 설정해야 하는 컬러 값의 추천 값이나 좋은 조합도 알려주지 못한다. Two Color Combinations나 기타 색상 조합 추천, 심리학과 디자인을 연결하여 분석하는 다양한 정보를 검색해 보면 어떤 색상들을 배경, 버튼, 텍스트, 제목, 작은 텍스트로 조합해야 하는지는 사실 이미 정해진 패턴이 여러 가지 있으므로 그런 걸 각기 다른 톤으로 조정해서 다양하게 프리셋만 해두어도 사용자들이 굉장히 편리할 것이다. 물론 플러그인 기능을 발휘하는 그 설정 페이지에서 실시간 미리보기와 적용 예시 버튼이나 배경, 텍스트 등이 보여야 함은 물론이다. 우선 내가 지금 든 이러한 예시를 통해 유추할 수 있는 현재 플러그인의 여러 문제점을 개선하여, 텍스트 한 단어 버튼 하나 허투루 만드는 일이 없도록 '모범적이고' 훌륭한 UI를 만들어 보세, 제이슨.

Request ID: a58331e6-3642-459e-af47-19aaa159bbd0
{"error":"ERROR_OPENAI","details":{"title":"Unable to reach the model provider","detail":"We're having trouble connecting to the model provider. This might be temporary - please try again in a moment.","additionalInfo":{},"buttons":[],"planChoices":[]},"isExpected":false}

스타일 센터의 직관적이지 않은 UI/UX를 개선하는 작업, 그리고 실험실 센터 등 다른 플러그인 기능 페이지 내에서의 UI가 전반적으로 텍스트 위주이고 버튼이나 색상으로 '보여주기'가 잘 안 되어 있다. 시각화가 잘 되어 있지 않아서, 기능을 일일이 스크롤해서 내려 보거나, 지원되는 스타일 수정 영역을 하나하나 맨 아래까지 내려서 확인해야 한다는 문제점도 있고, 현재 컬러 값을 자동으로 불러오지 못한다거나, 또한 앞으로 설정해야 하는 컬러 값의 추천 값이나 좋은 조합도 알려주지 못한다. Two Color Combinations나 기타 색상 조합 추천, 심리학과 디자인을 연결하여 분석하는 다양한 정보를 검색해 보면 어떤 색상들을 배경, 버튼, 텍스트, 제목, 작은 텍스트로 조합해야 하는지는 사실 이미 정해진 패턴이 여러 가지 있으므로 그런 걸 각기 다른 톤으로 조정해서 다양하게 프리셋만 해두어도 사용자들이 굉장히 편리할 것이다. 물론 플러그인 기능을 발휘하는 그 설정 페이지에서 실시간 미리보기와 적용 예시 버튼이나 배경, 텍스트 등이 보여야 함은 물론이다. 우선 내가 지금 든 이러한 예시를 통해 유추할 수 있는 현재 플러그인의 여러 문제점을 개선하여, 텍스트 한 단어 버튼 하나 허투루 만드는 일이 없도록 '모범적이고' 훌륭한 UI를 만들어 보세, 제이슨.


---

**Cursor**

말씀 주신 UX 문제(텍스트 위주/미리보기 부재/스크롤 과다/현재 값 자동 로딩 실패/추천 팔레트 부재)를 **‘근본 원인’ 기준으로 UI 구조부터 재정비**하겠습니다. 우선 (1) 컬러 섹션에 **프리셋 팔레트 + 실시간 프리뷰**를 넣고, (2) 현재 저장된 값/테마 값 로딩 경로를 정리한 뒤, (3) Labs/다른 탭은 **카드형 내비+검색/필터**로 스크롤을 줄이는 순서로 단계적으로 진행하겠습니다. 지금 바로 관련 화면 파일부터 찾아 들어가겠습니다.

좋습니다. 컬러 UI는 `includes/editor-views/view-section-colors.php`(실제 스타일 센터 화면)와 `includes/admin/views/tabs/tab-colors.php`(관리자 센터 설정 탭) 두 축이네요. 먼저 “사용자가 실제로 만지는 화면”인 `view-section-colors.php`에 **프리셋 팔레트 + 실시간 미리보기 + 현재값 자동 로딩**을 넣고, 그다음 Labs 센터(`class-jj-labs-center.php`)의 텍스트 위주 UI를 카드/필터형으로 바꾸겠습니다.

좋은 소식이 있습니다. `view-section-colors.php` 자체는 이미 “탭/컬러카드/새로고침/일괄 적용” 구조를 갖추고 있어서, **UI가 직관적이지 않은 ‘근본 원인’은 화면 구조보다는 “정보 요약/프리셋/실시간 프리뷰/탐색(스크롤) UX”가 빠진 것**에 가깝습니다. 그래서 이번 단계에서는 (1) 컬러 섹션 상단에 **프리셋 갤러리 + 즉시 적용 + 미리보기**를 넣고, (2) Labs 센터의 **업그레이드 노출 로직(마스터에서도 보일 수 있는 조건)을 교정**한 뒤, (3) Labs도 카드/검색/필터로 스크롤을 줄이겠습니다.

```php
<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * J&J 실험실
 * - CSS/HTML/JS 스캐너 및 수동 오버라이드 기능을 위한 별도 페이지
 * - Admin Center와 유사한 구조로 분리된 고급 기능 영역
 * 
 * @version 4.2.2
 * - "실험실 센터" → "실험실" 명칭 변경
 * - Labs → 실험실 명칭 변경
 * - 별도 페이지로 분리 (스타일 센터에서 독립)
 * - CSS/HTML/JS 스캐너 기능 강화
 * - 탭 기반 인터페이스 (스캐너, 수동 재정의, 공식 지원 목록)
 */
final class JJ_Labs_Center {

    private static $instance = null;
    private $option_key = 'jj_style_guide_options'; // labs 섹션 포함
    private $labs_tabs_option_key = 'jj_style_guide_labs_tabs_layout'; // [v5.0.0] 실험실 탭 활성화/비활성화
    private $adapters_config = array();
    private $options = array();
    
    // [v5.0.0] 성능 최적화: 실험실 탭 레이아웃 캐싱
    private static $labs_tabs_layout_cache = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
            if ( file_exists( $config_file ) ) {
                $this->adapters_config = include $config_file;
            }
        }
    }

    /**
     * 초기화: 메뉴 등록 훅 연결
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_labs_center_assets' ) );
        add_action( 'admin_init', array( $this, 'save_labs_tabs_layout' ) ); // [v5.0.0] 실험실 탭 활성화/비활성화 저장
    }

    /**
     * 실험실 전용 CSS/JS enqueue
     */
    public function enqueue_labs_center_assets( $hook ) {
        // 실험실 페이지에서만 로드
        if ( 'settings_page_jj-labs-center' !== $hook ) {
            return;
        }

        wp_enqueue_media(); // 미디어 업로더 (향후 확장용)
        wp_enqueue_style( 'wp-color-picker' );
        
        // [v3.7.0 '신규'] WordPress CodeMirror for CSS 에디터
        $code_editor_settings = wp_get_code_editor_settings( array( 'type' => 'text/css' ) );
        wp_enqueue_code_editor( $code_editor_settings );
        
        // [Safety] 상수 미정의(로드 순서 문제) 대비
        $base_url = defined( 'JJ_STYLE_GUIDE_URL' ) ? JJ_STYLE_GUIDE_URL : plugin_dir_url( dirname( __FILE__ ) ) . '../';
        $version  = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0';

        $css_url = $base_url . 'assets/css/jj-labs-center.css';
        $js_url  = $base_url . 'assets/js/jj-labs-center.js';

        wp_enqueue_style(
            'jj-labs-center',
            $css_url,
            array( 'wp-color-picker', 'code-editor' ),
            $version
        );

        wp_enqueue_script(
            'jj-labs-center',
            $js_url,
            array( 'jquery', 'wp-color-picker', 'code-editor' ),
            $version,
            true
        );

        // AJAX 파라미터 로컬라이즈
        wp_localize_script(
            'jj-labs-center',
            'jjLabsCenter',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
            )
        );
        
        // 스타일 가이드 에디터 스크립트도 로드 (스타일 저장 기능용)
        wp_enqueue_script(
            'jj-style-guide-editor',
            $base_url . 'assets/js/jj-style-guide-editor.js',
            array( 'jquery', 'wp-color-picker' ),
            $version,
            true
        );
        
        // [v5.0.3] 키보드 단축키 시스템 로드
        wp_enqueue_script( 'jj-keyboard-shortcuts', $base_url . 'assets/js/jj-keyboard-shortcuts.js', array( 'jquery' ), $version, true );
        
        // [v5.0.3] 툴팁 시스템 로드
        wp_enqueue_script( 'jj-tooltips', $base_url . 'assets/js/jj-tooltips.js', array( 'jquery' ), $version, true );
        
        $this->options = (array) get_option( $this->option_key );
        wp_localize_script(
            'jj-style-guide-editor',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'temp_palette_nonce' => wp_create_nonce( 'jj_temp_palette_nonce' ),
                'settings' => $this->options,
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'locale'   => get_locale(), // [v5.0.3] 다국어 지원을 위한 로케일 전달
                'i18n'     => array(
                    'saving' => __( '저장 중...', 'jj-style-guide' ),
                    'saved'  => __( '스타일 저장됨', 'jj-style-guide' ),
                ),
            )
        );
    }

    /**
     * "J&J 실험실" 서브메뉴 추가
     */
    public function add_admin_menu_page() {
        // [v5.5.0] 권한 체크 (Edition Controller)
        $capability = 'manage_options';
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            // 최소 Basic 이상 접근 가능하도록 (실제 기능 제한은 내부에서 처리)
            if ( ! JJ_Edition_Controller::instance()->is_at_least( 'basic' ) ) {
                return;
            }
        }

        $page_title = __( 'J&J 실험실 센터', 'jj-style-guide' );
        $menu_title = __( '실험실 센터', 'jj-style-guide' );

        // 1. 설정 메뉴 하단
        add_options_page(
            $page_title,
            $menu_title,
            $capability,
            'jj-labs-center',
            array( $this, 'render_labs_center_page' )
        );

        // 2. 모양(Appearance) 및 도구(Tools) 메뉴 추가 (접근성 강화)
        add_theme_page( $page_title, $menu_title, $capability, 'jj-labs-center', array( $this, 'render_labs_center_page' ) );
        add_management_page( $page_title, $menu_title, $capability, 'jj-labs-center', array( $this, 'render_labs_center_page' ) );
    }

    /**
     * 실험실 화면 렌더링
     */
    public function render_labs_center_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $this->options = (array) get_option( $this->option_key );
        $options = $this->options;
        
        // [v5.1.6] Master/Partner에서는 업그레이드 유도 UI를 절대 표시하지 않음
        $is_master_version = false;
        $is_partner_or_higher = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $edition = JJ_Edition_Controller::instance();
                $is_master_version = $edition->is_at_least( 'master' );
                $is_partner_or_higher = $edition->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $is_partner_or_higher && defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        }
        $purchase_url = '';
        if ( ! $is_partner_or_higher ) {
            $license_manager = null;
            $purchase_url = 'https://j-j-labs.com'; // 기본값
            if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                if ( class_exists( 'JJ_License_Manager' ) ) {
                    $license_manager = JJ_License_Manager::instance();
                    if ( $license_manager ) {
                        $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                    }
                }
            }
        }
        $labs_options = $options['labs'] ?? array();
        $adapters_config = $this->adapters_config;

        ?>
        <?php if ( ! $is_partner_or_higher && $purchase_url ) : ?>
        <div class="notice notice-info" style="margin: 20px 20px 0 0;">
            <p style="display: flex; align-items: center; gap: 10px;">
                <strong><?php esc_html_e( '더 많은 기능을 사용하려면 업그레이드하세요!', 'jj-style-guide' ); ?></strong>
                <a href="<?php echo esc_url( $purchase_url ); ?>" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="button button-primary" 
                   style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px; font-weight: 600;">
                    <span class="dashicons dashicons-cart" style="margin-top: 4px;"></span> <?php esc_html_e( '업그레이드하기', 'jj-style-guide' ); ?>
                </a>
            </p>
        </div>
        <?php endif; ?>
        <div class="wrap jj-labs-center-wrap">
            <div class="jj-labs-center-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 8px;">
                        <h1 style="margin: 0;"><?php _e( 'J&J 실험실 센터', 'jj-style-guide' ); ?> <span class="version">v<?php echo esc_html( defined('JJ_STYLE_GUIDE_VERSION') ? JJ_STYLE_GUIDE_VERSION : '8.0.0' ); ?></span></h1>
                        <a href="<?php echo esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ); ?>" 
                           class="button button-secondary" 
                           style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <?php _e( '스타일 센터', 'jj-style-guide' ); ?>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ); ?>" 
                           class="button button-secondary" 
                           style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <?php _e( '관리자 센터', 'jj-style-guide' ); ?>
                        </a>
                        <?php
                        // [v5.1.6] 마스터 버전이 아닌 경우 결제 유도 문구 표시
                        if ( ! $is_partner_or_higher && $purchase_url ) {
                            ?>
                            <a href="<?php echo esc_url( $purchase_url ); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="button button-primary" 
                               style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px; font-weight: 600;">
                                <span class="dashicons dashicons-cart" style="margin-top: 4px;"></span> <?php _e( '업그레이드하기', 'jj-style-guide' ); ?>
                            </a>
                            <?php
                        }
                        ?>
                    </div>
                    <p class="description" style="margin: 0;">
                        <span class="jj-labs-tab-description" data-tab-type="scanner" data-tooltip="labs-tab-scanner">
                            <?php _e( '실험실은 아직 공식 지원되지 않는 테마나 플러그인의 스타일을 분석하고 조정하기 위한 고급 도구입니다. CSS/HTML/JS 스캐너와 수동 재정의 기능을 제공합니다.', 'jj-style-guide' ); ?>
                        </span>
                    </p>
                </div>
                <div class="jj-header-actions">
                    <!-- [v4.2.2] 실험실 설정 내보내기/불러오기 -->
                    <button type="button" class="button button-secondary jj-export-settings" data-center="labs-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px; margin-right: 8px;">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php _e( '내보내기', 'jj-style-guide' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-settings" data-center="labs-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span> <?php _e( '불러오기', 'jj-style-guide' ); ?>
                    </button>
                </div>
            </div>

            <!-- [v5.0.0] 실험실 탭 활성화/비활성화 설정 -->
            <form method="post" action="" style="margin-bottom: 20px;">
                <?php wp_nonce_field( 'jj_labs_tabs_save_action' ); ?>
                <div style="padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                    <h3 style="margin-top: 0;"><?php _e( '실험실 탭 활성화/비활성화', 'jj-style-guide' ); ?></h3>
                    <p class="description" style="margin-bottom: 15px;">
                        <?php _e( '실험실의 각 탭을 활성화 또는 비활성화할 수 있습니다.', 'jj-style-guide' ); ?>
                        <span class="jj-tooltip" style="margin-left: 5px;">
                            <span class="dashicons dashicons-editor-help"></span>
                            <span class="jj-tooltip-content">
                                <?php _e( '비활성화된 탭은 실험실에서 숨겨지지만 설정 데이터는 보존됩니다.', 'jj-style-guide' ); ?>
                            </span>
                        </span>
                    </p>
                    
                    <!-- [v5.0.0] 일괄 작업 버튼 -->
                    <div class="jj-bulk-actions" style="margin-bottom: 15px;">
                        <button type="button" class="button button-small jj-bulk-enable-all-labs">
                            <span class="dashicons dashicons-yes-alt" style="font-size: 16px; margin-top: 3px;"></span>
                            <?php _e( '모두 활성화', 'jj-style-guide' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-bulk-disable-all-labs">
                            <span class="dashicons dashicons-dismiss" style="font-size: 16px; margin-top: 3px;"></span>
                            <?php _e( '모두 비활성화', 'jj-style-guide' ); ?>
                        </button>
                    </div>
                    
                    <!-- [v5.0.0] 검색/필터 -->
                    <div class="jj-section-filter" style="margin-bottom: 15px;">
                        <input type="search" 
                               id="jj-labs-tabs-search" 
                               placeholder="<?php esc_attr_e( '탭 검색...', 'jj-style-guide' ); ?>"
                               style="width: 100%; max-width: 400px; padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 3px; font-size: 13px;" />
                    </div>

                    <!-- [v5.0.0] 경고 메시지 -->
                    <div class="notice notice-warning inline jj-labs-tabs-disable-warning" style="display: none; margin: 15px 0;">
                        <p>
                            <strong><?php _e( '주의:', 'jj-style-guide' ); ?></strong>
                            <?php _e( '탭을 비활성화하면 실험실에서 해당 탭이 표시되지 않습니다. 비활성화된 탭의 설정 데이터는 보존되지만 UI에서 접근할 수 없게 됩니다.', 'jj-style-guide' ); ?>
                        </p>
                    </div>

                    <?php
                    $labs_tabs_layout = $this->get_labs_tabs_layout();
                    $labs_tabs_default = $this->get_default_labs_tabs_layout();
                    ?>
                    <table class="form-table" style="margin: 0;">
                        <thead>
                        <tr>
                            <th style="width: 30px;">
                                <input type="checkbox" 
                                       id="jj-select-all-labs-tabs" 
                                       class="jj-select-all-checkbox" 
                                       title="<?php esc_attr_e( '전체 선택/해제', 'jj-style-guide' ); ?>" />
                            </th>
                            <th><?php _e( '탭', 'jj-style-guide' ); ?></th>
                            <th><?php _e( '활성화', 'jj-style-guide' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ( $labs_tabs_default as $tab_slug => $tab_meta ) :
                            $tab_current = isset( $labs_tabs_layout[ $tab_slug ] ) ? $labs_tabs_layout[ $tab_slug ] : $tab_meta;
                            $tab_enabled = ! empty( $tab_current['enabled'] );
                            ?>
                            <tr class="jj-labs-tab-row <?php echo $tab_enabled ? '' : 'jj-tab-row-disabled'; ?>"
                                data-tab-slug="<?php echo esc_attr( $tab_slug ); ?>"
                                data-tab-label="<?php echo esc_attr( strtolower( $tab_meta['label'] ) ); ?>">
                                <td style="width: 30px; text-align: center; padding: 8px 0;">
                                    <input type="checkbox" 
                                           class="jj-labs-tab-checkbox" 
                                           data-tab="<?php echo esc_attr( $tab_slug ); ?>"
                                           <?php checked( $tab_enabled ); ?> />
                                </td>
                                <th scope="row" style="padding: 8px 0; font-weight: normal;">
                                    <?php echo esc_html( $tab_meta['label'] ); ?>
                                    <?php if ( ! $tab_enabled ) : ?>
                                        <span class="jj-disabled-icon" title="<?php esc_attr_e( '비활성화됨', 'jj-style-guide' ); ?>"></span>
                                    <?php else : ?>
                                        <span class="jj-enabled-icon" title="<?php esc_attr_e( '활성화됨', 'jj-style-guide' ); ?>"></span>
                                    <?php endif; ?>
                                    <div style="font-size:10px;color:#999;margin-top:2px;">
                                        <code><?php echo esc_html( $tab_slug ); ?></code>
                                    </div>
                                </th>
                                <td style="padding: 8px 0;">
                                    <label>
                                        <input type="checkbox"
                                               name="jj_labs_tabs_layout[<?php echo esc_attr( $tab_slug ); ?>][enabled]"
                                               value="1"
                                               class="jj-labs-tab-enabled-checkbox"
                                               data-tab="<?php echo esc_attr( $tab_slug ); ?>"
                                            <?php checked( $tab_enabled ); ?> />
                                        <?php _e( '활성화', 'jj-style-guide' ); ?>
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p style="margin-top: 15px; margin-bottom: 0;">
                        <button type="submit" class="button button-primary">
                            <?php _e( '저장', 'jj-style-guide' ); ?>
                        </button>
                    </p>
                </div>
            </form>

            <div class="jj-labs-center-tabs-container">
                <ul class="jj-labs-center-tabs">
                    <?php 
                    // [v5.0.0] 탭 활성화/비활성화 체크
                    $tab_enabled_scanner = ! empty( $labs_tabs_layout['scanner']['enabled'] ?? true );
                    $tab_enabled_overrides = ! empty( $labs_tabs_layout['overrides']['enabled'] ?? true );
                    $tab_enabled_supported = ! empty( $labs_tabs_layout['supported']['enabled'] ?? true );
                    
                    // 활성화된 탭이 하나도 없으면 스캐너 탭을 기본으로 활성화
                    $has_enabled_tabs = $tab_enabled_scanner || $tab_enabled_overrides || $tab_enabled_supported;
                    if ( ! $has_enabled_tabs ) {
                        $tab_enabled_scanner = true;
                    }
                    
                    $first_enabled_tab = $tab_enabled_scanner ? 'scanner' : ( $tab_enabled_overrides ? 'overrides' : 'supported' );
                    ?>
                    <?php if ( $tab_enabled_scanner ) : ?>
                    <li class="<?php echo ( $first_enabled_tab === 'scanner' ) ? 'active' : ''; ?>" data-tab="scanner">
                        <a href="#scanner"><?php _e( '스캐너', 'jj-style-guide' ); ?></a>
                    </li>
                    <?php endif; ?>
                    <?php if ( $tab_enabled_overrides ) : ?>
                    <li class="<?php echo ( $first_enabled_tab === 'overrides' ) ? 'active' : ''; ?>" data-tab="overrides">
                        <a href="#overrides"><?php _e( '수동 재정의', 'jj-style-guide' ); ?></a>
                    </li>
                    <?php endif; ?>
                    <?php if ( $tab_enabled_supported ) : ?>
                    <li class="<?php echo ( $first_enabled_tab === 'supported' ) ? 'active' : ''; ?>" data-tab="supported">
                        <a href="#supported"><?php _e( '공식 지원 목록', 'jj-style-guide' ); ?></a>
                    </li>
                    <?php endif; ?>
                </ul>

                <!-- 스캐너 탭 -->
                <?php if ( $tab_enabled_scanner ) : ?>
                <div class="jj-labs-center-tab-content <?php echo ( $first_enabled_tab === 'scanner' ) ? 'active' : ''; ?>" data-tab="scanner">
                    <?php
                    $options = $this->options;
                    include JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-labs-scanner.php';
                    ?>
                </div>
                <?php endif; ?>

                <!-- 수동 재정의 탭 -->
                <?php if ( $tab_enabled_overrides ) : ?>
                <div class="jj-labs-center-tab-content <?php echo ( $first_enabled_tab === 'overrides' ) ? 'active' : ''; ?>" data-tab="overrides">
                    <?php
                    $options = $this->options;
                    include JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-labs-overrides.php';
                    ?>
                </div>
                <?php endif; ?>

                <!-- 공식 지원 목록 탭 -->
                <?php if ( $tab_enabled_supported ) : ?>
                <div class="jj-labs-center-tab-content <?php echo ( $first_enabled_tab === 'supported' ) ? 'active' : ''; ?>" data-tab="supported">
                    <?php
                    // 실험실 섹션의 공식 지원 목록 부분만 재사용
                    $themes_config_raw = $adapters_config['themes'] ?? array();
                    $spokes_config_raw = $adapters_config['spokes'] ?? array();
                    include JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-labs-supported-list.php';
                    ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- [v4.2.2] 실험실 설정 내보내기/불러오기 (하단) -->
            <div class="jj-labs-center-footer" style="margin-top: 30px; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <button type="button" class="button button-secondary jj-export-settings" data-center="labs-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px; margin-right: 8px;">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php _e( '내보내기', 'jj-style-guide' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-settings" data-center="labs-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span> <?php _e( '불러오기', 'jj-style-guide' ); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * [v5.0.0] 실험실 탭 레이아웃 기본값
     *
     * @return array
     */
    public function get_default_labs_tabs_layout() {
        return array(
            'scanner'   => array(
                'label'   => __( '스캐너', 'jj-style-guide' ),
                'enabled' => 1,
            ),
            'overrides' => array(
                'label'   => __( '수동 재정의', 'jj-style-guide' ),
                'enabled' => 1,
            ),
            'supported' => array(
                'label'   => __( '공식 지원 목록', 'jj-style-guide' ),
                'enabled' => 1,
            ),
        );
    }
    
    /**
     * [v5.0.0] 저장된 실험실 탭 레이아웃 + 기본값 병합
     * [v5.0.0] 성능 최적화: static 캐싱 추가
     *
     * @return array
     */
    public function get_labs_tabs_layout() {
        // [v5.0.0] 캐시된 값이 있으면 반환
        if ( self::$labs_tabs_layout_cache !== null ) {
            return self::$labs_tabs_layout_cache;
        }
        
        $stored   = (array) get_option( $this->labs_tabs_option_key );
        $defaults = $this->get_default_labs_tabs_layout();
        $result   = array();

        foreach ( $defaults as $slug => $meta ) {
            $enabled = isset( $stored[ $slug ]['enabled'] ) ? (bool) $stored[ $slug ]['enabled'] : (bool) ( $meta['enabled'] ?? true );
            $result[ $slug ] = array(
                'label'   => $meta['label'],
                'enabled' => $enabled ? 1 : 0,
            );
        }

        // [v5.0.0] 캐시에 저장
        self::$labs_tabs_layout_cache = $result;
        
        return $result;
    }
    
    /**
     * 실험실 탭 레이아웃 캐시 플러시
     * [v5.0.0] 성능 최적화: 옵션이 업데이트되면 호출하여 캐시를 무효화
     *
     * @return void
     */
    public static function flush_labs_tabs_layout_cache() {
        self::$labs_tabs_layout_cache = null;
    }
    
    /**
     * [v5.0.0] 실험실 탭 활성화/비활성화 저장
     */
    public function save_labs_tabs_layout() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        // 실험실 페이지에서만 저장
        if ( ! isset( $_GET['page'] ) || 'jj-labs-center' !== $_GET['page'] ) {
            return;
        }
        
        if ( ! isset( $_POST['jj_labs_tabs_layout'] ) || ! isset( $_POST['_wpnonce'] ) ) {
            return;
        }
        
        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'jj_labs_tabs_save_action' ) ) {
            return;
        }
        
        $raw_tabs   = isset( $_POST['jj_labs_tabs_layout'] ) && is_array( $_POST['jj_labs_tabs_layout'] ) ? wp_unslash( $_POST['jj_labs_tabs_layout'] ) : array();
        $clean_tabs = array();
        $defaults   = $this->get_default_labs_tabs_layout();
        
        foreach ( $defaults as $slug => $meta ) {
            $enabled = isset( $raw_tabs[ $slug ]['enabled'] ) && '1' === $raw_tabs[ $slug ]['enabled'];
            $clean_tabs[ $slug ] = array(
                'enabled' => $enabled ? 1 : 0,
            );
        }
        
        update_option( $this->labs_tabs_option_key, $clean_tabs );
        
        // [v5.0.0] 캐시 플러시
        self::flush_labs_tabs_layout_cache();
    }
}


```

```php
<?php
/**
 * Plugin Name:       ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager (Master)
 * Plugin URI:        https://j-j-labs.com
 * Description:       WordPress 웹사이트의 모든 스타일 요소(색상 팔레트, 타이포그래피, 버튼, 폼)를 중앙에서 일관되게 관리하는 통합 스타일 관리 플러그인입니다. Free 버전은 기본적인 스타일 관리 기능을 제공하며, 브랜드 일관성을 유지하고 디자인 시스템을 효율적으로 운영할 수 있습니다. Pro 버전 플러그인을 함께 설치하면 Basic, Premium, Unlimited 기능을 사용할 수 있습니다. WordPress Customizer와 완벽 통합되어 실시간 미리보기와 함께 직관적인 스타일 관리가 가능합니다.
 * Version:           8.0.0
 * Author:            3J Labs
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://poetryflow.blog
 * Text Domain:       acf-css-really-simple-style-management-center
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ⚠️ 중요: Git 경로와 로컬 경로는 완전히 별개입니다
 * 
 * - 로컬 작업 경로: C:\Users\computer\OneDrive\Desktop\클라우드 백업\OneDrive\문서\jj-css-premium
 * - Git worktree 경로: C:\Users\computer\.cursor\worktrees\jj-css-premium\tci
 * - 두 경로는 서로 독립적이며, Git에서 작업한 내용은 반드시 로컬 경로에 동기화해야 함
 * - 자세한 내용은 DEVELOPMENT_PRINCIPLES.md의 "Git 및 로컬 경로 관리 원칙" 섹션 참조
 */

// 플러그인 상수 정의
// [v5.1.6] 전면 재검토 및 오류 방지: 안전한 파일 로더 추가, 모든 버전의 require_once 안전 처리, 결제 유도 문구 추가, 플러그인 목록 페이지 바로가기 링크 추가
// [v5.1.6] Comprehensive review and error prevention: Safe file loader added, all versions' require_once safely handled, purchase prompts added, plugin list page quick links added
// [v1.0.2] 모든 버전 플러그인 활성화 안전성 최종 확보, WordPress 함수 호출 안전 처리
define( 'JJ_STYLE_GUIDE_VERSION', '8.0.0' ); // [v7.0.0] Operation Deep Dive - Stability & Reliability Overhaul

// WordPress 함수가 로드되었는지 확인 후 상수 정의
if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    if ( function_exists( 'plugin_dir_path' ) ) {
        define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 직접 경로 계산
        define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
    }
}

if ( ! defined( 'JJ_STYLE_GUIDE_URL' ) ) {
    if ( function_exists( 'plugin_dir_url' ) ) {
        define( 'JJ_STYLE_GUIDE_URL', plugin_dir_url( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 기본값 사용 (나중에 업데이트됨)
        define( 'JJ_STYLE_GUIDE_URL', '' );
    }
}
define( 'JJ_STYLE_GUIDE_OPTIONS_KEY', 'jj_style_guide_options' );
define( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY', 'jj_style_guide_temp_options' );
define( 'JJ_STYLE_GUIDE_ADMIN_TEXT_KEY', 'jj_style_guide_admin_texts' );
define( 'JJ_STYLE_GUIDE_PAGE_SLUG', 'acf-css-really-simple-style-guide' );
define( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY', 'jj_style_guide_cockpit_page_id' );
define( 'JJ_STYLE_GUIDE_DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
// Pro 버전 활성화 코드 확인 (plugins_loaded 훅에서 처리)
// WordPress가 로드되기 전에는 기본값 사용
define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', 'MASTER' ); // 기본값, plugins_loaded에서 업데이트됨
define( 'JJ_STYLE_GUIDE_EDITION', 'master' ); // 기본 에디션은 free

// [v3.8.0] 환경 구분
if ( ! defined( 'JJ_STYLE_GUIDE_ENV' ) ) {
    define( 'JJ_STYLE_GUIDE_ENV', 'live' );
}

// [v5.3.7] Free 버전은 독립적으로 작동합니다. Master 버전 의존성 제거
// Free 버전은 자체 includes 폴더에 필요한 모든 파일을 포함합니다.

// [v5.4.1] 안전한 파일 로더 적용
$safe_loader = null;
try {
    $safe_loader_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-loader.php';
    if ( file_exists( $safe_loader_path ) ) {
        require_once $safe_loader_path;
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'instance' ) ) {
            $safe_loader = JJ_Safe_Loader::instance();
        }
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Failed: ' . $e->getMessage() );
} catch ( Error $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Fatal Error: ' . $e->getMessage() );
}

// 라이센스 매니저 로드
if ( $safe_loader ) {
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php', false );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-version-features.php', true );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-code-integrity-monitor.php', true );
}

// 핵심 로직 파일 로드 (Master 전용 파일은 아직 비활성화)
try {
    if ( $safe_loader && method_exists( $safe_loader, 'safe_require' ) ) {
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-handler.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-logger.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-admin-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-backup-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/visual-command-center/class-jj-visual-command-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/ai-style-intelligence/class-jj-smart-palette.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-cloud-manager.php', true );
        // [Phase 3] 템플릿 마켓
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-template-market.php', true );
        // [Phase 5] Partner Hub
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/partner-hub/class-jj-partner-hub.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-labs-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-selector-registry.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-injector.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-options-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-edition-controller.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-theme-metadata.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-0-customizer.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-1-css-vars.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-2-php-filters.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-3-dequeue.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-sync.php', false );
        // [Phase 5.2] REST API (설정 조회/업데이트)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/api/class-jj-style-guide-rest-api.php', true );
        // [Phase 5.3] 확장 플러그인 시스템 (Extension API)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/interface-jj-style-guide-extension.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/class-jj-extension-manager.php', true );
        // [Phase 5.2] Webhooks (자동화)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/integrations/class-jj-webhook-manager.php', true );
        // [Phase 5 B] Multisite Network Control
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/multisite/class-jj-multisite-controller.php', true );
        
        // Master 전용 파일들 (일단 주석 처리)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-sync-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-memory-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-mode.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-optimizer.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-issuer.php', false );
    } else {
        // Fallback for Safe Loader missing
        require_once JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
        // ... (Fallback은 일단 생략, Safe Loader가 있다고 가정)
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Master Load Error: ' . $e->getMessage() );
}



/**
 * 활성화 훅 래퍼 함수 (치명적 오류 방지)
 * [v5.1.7] 전체 함수를 try-catch로 감싸고 상수 정의 확인 추가
 */
function jj_simple_style_guide_master_activate() {
    try {
        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // 상수가 정의되지 않은 경우 기본 경로 사용
            if ( function_exists( 'plugin_dir_path' ) ) {
                define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
            } else {
                define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
            }
        }
        
        // 활성화 매니저 클래스가 로드되지 않은 경우 로드 시도
        
        if ( ! class_exists( 'JJ_Activation_Manager' ) ) {
            $activation_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php';
            if ( file_exists( $activation_file ) ) {
                require_once $activation_file;
            } else {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 파일을 찾을 수 없습니다. 기본 활성화만 수행합니다.' );
                }
                return;
            }
        }
        
        // 클래스가 존재하는지 확인 후 활성화 실행
        if ( class_exists( 'JJ_Activation_Manager' ) && method_exists( 'JJ_Activation_Manager', 'activate' ) ) {
            try {
                JJ_Activation_Manager::activate();
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 오류 발생 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 치명적 오류 발생 - ' . $e->getMessage() );
                }
            }
        } else {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 클래스를 사용할 수 없습니다.' );
            }
        }
    } catch ( Exception $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 예외 발생 - ' . $e->getMessage() );
        }
    } catch ( Error $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 치명적 오류 발생 - ' . $e->getMessage() );
        }
    }
}
register_activation_hook( __FILE__, 'jj_simple_style_guide_master_activate' );

/**
 * Free 버전 전용 메인 컨트롤 클래스
 */
final class JJ_Simple_Style_Guide_Master {

    private static $instance = null;
    private $options = array();
    private $cockpit_page_id = 0;
    private $options_key = '';
    private $brand_colors = array();
    private $environment_map = array();
    private $adapters_config = array();
    private $license_manager = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 상수 정의 확인 후 할당
        if ( defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) {
            $this->options_key = JJ_STYLE_GUIDE_OPTIONS_KEY;
        }
        
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $this->license_manager = JJ_License_Manager::instance();
        }
        
        if ( defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) && function_exists( 'get_option' ) ) {
            $this->cockpit_page_id = (int) get_option( JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY );
        }

        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // [v5.3.7] Free 버전은 자체 config 폴더에서 로드
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
            if ( file_exists( $config_file ) ) {
                $this->adapters_config = include( $config_file );
                // [v5.5.0] Edition Controller를 통한 동적 어댑터 필터링
                if ( class_exists( 'JJ_Edition_Controller' ) ) {
                    $this->adapters_config = JJ_Edition_Controller::instance()->filter_adapters( $this->adapters_config );
                }
                
                
            }
        }

        // WordPress 함수가 로드된 후에만 훅 등록
        // [v5.1.9] plugin_basename() 안전 호출 추가
        // [v5.3.9] 모든 훅 등록을 조건문 안으로 이동하여 안전성 확보
        if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) && function_exists( 'plugin_basename' ) ) {
            add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
            add_action( 'admin_menu', array( $this, 'add_admin_menu_links' ) );
            $plugin_basename = function_exists( 'plugin_basename' ) ? plugin_basename( __FILE__ ) : basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );
            add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_plugin_settings_link' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            add_action( 'wp_ajax_jj_save_style_guide', array( $this, 'save_settings_ajax' ) );
            add_action( 'wp_ajax_jj_apply_temp_palette', array( $this, 'apply_temp_palette_ajax' ) );
            add_action( 'wp_ajax_jj_save_temp_palette', array( $this, 'save_temp_palette_ajax' ) );
            add_action( 'init', array( $this, 'load_options_and_init_strategies' ), 5 );
            add_action( 'init', array( $this, 'init_sync_manager' ), 10 );
            
            
            if ( JJ_STYLE_GUIDE_DEBUG ) {
                add_action( 'wp_footer', array( $this, 'render_debug_info' ), 999 );
                add_action( 'admin_footer', array( $this, 'render_debug_info' ), 999 );
            }
        }

        // Customizer 통합 (제한적)
        
        try {
            $customizer_manager_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php';
            if ( file_exists( $customizer_manager_path ) ) {
                require_once $customizer_manager_path;
                if ( class_exists( 'JJ_Customizer_Manager' ) ) {
                    $customizer_manager = JJ_Customizer_Manager::instance();
                    if ( method_exists( $customizer_manager, 'init' ) ) {
                        $customizer_manager->init();
                    }
                }
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }

    }

    /**
     * Free 버전: 어댑터 제한
     * 
     * @param array $config
     * @return array
     */
    private function limit_adapters_for_free( $config ) {
        // Free 버전: 테마 2개만 지원 (Twenty Twenty-Four, Twenty Twenty-Five)
        $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
        if ( isset( $config['themes'] ) && is_array( $config['themes'] ) ) {
            $config['themes'] = array_intersect_key( $config['themes'], array_flip( $allowed_themes ) );
        }

        // Free 버전: 플러그인 1개만 지원 (WooCommerce)
        $allowed_plugins = array( 'woocommerce' );
        if ( isset( $config['spokes'] ) && is_array( $config['spokes'] ) ) {
            $config['spokes'] = array_intersect_key( $config['spokes'], array_flip( $allowed_plugins ) );
        }

        return $config;
    }

    /**
     * Free 버전 워터마크 추가
     */
    public function add_free_watermark() {
        if ( ! is_admin() && current_user_can( 'manage_options' ) ) {
            echo '<div style="position: fixed; bottom: 10px; right: 10px; background: rgba(0, 0, 0, 0.7); color: #fff; padding: 8px 12px; font-size: 11px; border-radius: 4px; z-index: 99999; font-family: Arial, sans-serif;">';
            echo '<a href="' . esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ) . '" style="color: #fff; text-decoration: none;">JJ Style Guide Free</a>';
            echo '</div>';
        }
    }

    /**
     * 업그레이드 프롬프트 표시
     */
    public function show_upgrade_prompt() {
        // [Safety Lock] MASTER 또는 PARTNER 버전은 업그레이드 프롬프트 표시 안 함
        if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'partner' ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        $license_manager = null;
        $upgrade_url = 'https://j-j-labs.com'; // 기본값
        
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
                if ( $license_manager ) {
                    $upgrade_url = $license_manager->get_purchase_url( 'upgrade' );
                }
            }
        }
        ?>
        <div class="notice notice-info is-dismissible" style="background: #f0f6fc; border-left-color: #2271b1;">
            <p style="font-size: 14px; line-height: 1.8;">
                <strong><?php _e( '현재 버전을 사용 중입니다.', 'acf-css-really-simple-style-management-center' ); ?></strong><br>
                <?php _e( '더 많은 기능을 이용하려면 업그레이드하세요:', 'acf-css-really-simple-style-management-center' ); ?>
                <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" class="button button-primary" style="margin-left: 10px;">
                    <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
            </p>
            <ul style="margin: 10px 0; padding-left: 20px; list-style: disc;">
                <li><?php _e( '모든 팔레트 사용 가능 (브랜드, 시스템, 얼터너티브, 어나더, 임시)', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '모든 테마 및 플러그인 어댑터 지원', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '관리자 센터 및 실험실 센터 전체 기능', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '섹션별 내보내기/불러오기', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '고급 색상 선택 기능 (스포이드, 팔레트 불러오기)', 'acf-css-really-simple-style-management-center' ); ?></li>
            </ul>
        </div>
        <?php
    }

    public function init_sync_manager() {
        add_action( 'customize_save_after', array( $this, 'sync_from_customizer' ) );
        JJ_Sync_Manager::instance()->init( $this->options, $this->cockpit_page_id );
    }

    /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * Free 버전: 스타일 센터만 표시
     * [v5.1.9] 안전성 강화 - try-catch 추가
     */
            /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * [Phase 4.99] 퀵 링크 확장: 설정 | 스타일 | 메뉴 | 비주얼
     */
    public function add_plugin_settings_link( $links ) {
        try {
            if ( ! function_exists( 'admin_url' ) || ! function_exists( 'esc_url' ) ) {
                return $links;
            }
            
            $new_links = array();
            
            // 1. 설정 (Admin Center 메인)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ) . '" style="font-weight: 600;">' . __( 'Settings', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 2. 스타일 (Style Guide)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ) ) . '">' . __( 'Styles', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 3. 어드민 메뉴 (Admin Menu Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#admin-menu' ) ) . '">' . __( 'Menu', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 4. 비주얼 (Visual Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#visual' ) ) . '">' . __( 'Visual', 'acf-css-really-simple-style-management-center' ) . '</a>';

            // 기존 링크와 병합
            return array_merge( $new_links, $links );
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 오류 - ' . $e->getMessage() );
            }
            return $links;
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 치명적 오류 - ' . $e->getMessage() );
            }
            return $links;
        }
    }

    public function add_admin_menu_page() {
        add_options_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );
    }

    public function add_admin_menu_links() {
        // [Phase 4.5/4.99] "모양/도구/설정" 어디서나 동일한 화면으로 진입할 수 있도록 서브메뉴 추가
        add_theme_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        add_management_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        if ( $this->cockpit_page_id ) {
            add_submenu_page(
                'themes.php',
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                'manage_options',
                'post.php?post=' . $this->cockpit_page_id . '&action=edit',
                '',
                100
            );
        }
    }

    public function enqueue_admin_scripts( $hook_suffix ) {
        $is_settings_page = in_array(
            $hook_suffix,
            array(
                'settings_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'appearance_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'tools_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
            ),
            true
        );

        $is_cockpit_page = false;
        if ( 'post.php' === $hook_suffix ) {
            global $post;
            if ( $post && (int) $post->ID === $this->cockpit_page_id ) {
                $is_cockpit_page = true;
            }
        }

        if ( ! $is_settings_page && ! $is_cockpit_page ) {
            return;
        }

        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;

        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        // [v5.3.7] Free 버전은 자체 assets 폴더에서 로드
        wp_enqueue_style( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-editor.css', array( 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION );
        wp_enqueue_script( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-editor.js', array( 'jquery', 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION, true );

        $preview_page_id = $this->cockpit_page_id;
        $preview_url = $preview_page_id ? get_permalink( $preview_page_id ) : '';

        wp_localize_script(
            'jj-style-guide-editor',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'temp_palette_nonce' => wp_create_nonce( 'jj_temp_palette_nonce' ),
                'settings' => $this->options,
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'edition'  => 'free', // Free 버전 표시
                'i18n'     => array(
                    'saving' => __( '저장 중...', 'acf-css-really-simple-style-management-center' ),
                    'saved'  => __( '스타일 저장됨', 'acf-css-really-simple-style-management-center' ),
                    'applied' => __( '임시 팔레트 적용됨! \'스타일 저장\'을 눌러야 최종 반영됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'error'  => __( '오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
                    'confirm_apply' => __( '정말로 \'임시 팔레트\'의 값으로 덮어쓰시겠습니까?\n이 작업은 즉시 UI에 반영되지만, \n\'스타일 저장\' 버튼을 눌러야 최종 저장됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'preview_url' => $preview_url ? esc_url( $preview_url ) : '',
                    'preview_page_id' => $preview_page_id,
                ),
            )
        );
    }

    public function render_style_guide_page() {
        try {
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options      = $options;
            $temp_options       = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY );
            $options            = $this->options;
            $cockpit_page_id    = $this->cockpit_page_id;
            $preview_url        = $cockpit_page_id ? get_permalink( $cockpit_page_id ) : '';

        // 옵션이 비어있으면 기본값 설정
        if ( empty( $this->options ) ) {
            $this->options = array(
                'palettes' => array(
                    'brand' => array(
                        'primary_color' => '',
                        'primary_color_hover' => '',
                        'secondary_color' => '',
                        'secondary_color_hover' => '',
                    ),
                    'system' => array(), // Free 버전: 시스템 팔레트 비활성화
                    'alternative' => array(), // Free 버전: 얼터너티브 팔레트 비활성화
                    'another' => array(), // Free 버전: 어나더 팔레트 비활성화
                ),
                'typography' => array(),
                'buttons' => array(),
                'forms' => array(),
                'contexts' => array(),
            );
            $options = $this->options;
        }
        ?>
        <div id="jj-style-guide-wrapper" class="wrap jj-style-guide-wrap" style="background: #f0f0f1; margin: 0; padding: 0;">
            <form id="jj-style-guide-form">

                <div class="jj-style-guide-header" style="top: 32px; background: #fff; padding: 15px 25px; border-bottom: 1px solid #c3c4c7; z-index: 999; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h1 style="margin: 0; font-size: 24px;">
                            <?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?> (v<?php echo esc_html( JJ_STYLE_GUIDE_VERSION ); ?>) 
                            <?php if ( ! class_exists("JJ_Edition_Controller") || JJ_Edition_Controller::instance()->get_current_edition() === "free" ) : ?><span style="background: #d63638; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 14px; font-weight: 600;">Free</span><?php endif; ?>
                        </h1>
                        <?php
                        $license_manager = null;
                        $purchase_url = 'https://j-j-labs.com'; // 기본값
                        
                        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                            if ( class_exists( 'JJ_License_Manager' ) ) {
                                $license_manager = JJ_License_Manager::instance();
                                if ( $license_manager ) {
                                    $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                                }
                            }
                        }
                        ?>
                        <?php if ( ! class_exists("JJ_Edition_Controller") || ! JJ_Edition_Controller::instance()->is_at_least("partner") ) : ?><a href="<?php echo esc_url( $purchase_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary" style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px;">
                            <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                        </a><?php endif; ?>
                        <?php if ( $preview_url ) : ?>
                        <button type="button" 
                                class="button button-secondary" 
                                id="jj-preview-button"
                                style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> <?php _e( '실시간 미리보기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="jj-header-actions" style="display: flex; align-items: center; gap: 10px;">
                        <span id="jj-save-success" style="display: none; vertical-align: middle; color: #2271b1; font-weight: 600; margin-right: 15px;"></span>
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-save-style-guide" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
                <div class="jj-style-guide-content" style="margin: 20px auto; background: #ffffff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 1px;">
                    <?php
                    // Free 버전: 제한된 섹션만 표시
                    // [v5.3.7] Free 버전은 자체 includes/editor-views 폴더에서 로드
                    $sections_map = array(
                        'colors'     => 'includes/editor-views/view-section-colors.php',
                        'typography' => 'includes/editor-views/view-section-typography.php',
                        'buttons'    => 'includes/editor-views/view-section-buttons.php',
                        'forms'      => 'includes/editor-views/view-section-forms.php',
                    );

                    // Free 버전: 섹션 순서 고정
                    $sections_to_render = array();
                    foreach ( $sections_map as $slug => $relative_path ) {
                        $sections_to_render[] = array(
                            'slug' => $slug,
                            'file' => JJ_STYLE_GUIDE_PATH . $relative_path,
                            'order'=> 0, // 순서는 UI에서만 표시
                        );
                    }
                    ?>
                    <div id="jj-sections-sortable" class="jj-sections-sortable">
                        <?php
                        foreach ( $sections_to_render as $section ) {
                            // Free 버전: 기본 뷰 파일 사용 (제한 로직은 뷰 파일 내부에서 처리)
                            $default_file = JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-section-' . $section['slug'] . '.php';
                            if ( file_exists( $section['file'] ) ) {
                                echo '<div class="jj-section-wrapper" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $section['file'];
                                echo '</div>';
                            } elseif ( file_exists( $default_file ) ) {
                                // Free 버전 전용 뷰가 없으면 기본 뷰 사용 (제한 로직 적용 필요)
                                echo '<div class="jj-section-wrapper jj-section-free-version" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $default_file;
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="jj-style-guide-footer" style="margin: 20px auto; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: #666; font-size: 13px;">
                        <?php
                        // Master/Partner에서는 업그레이드 유도 문구를 절대 노출하지 않음
                        $show_upgrade_footer = true;
                        if ( class_exists( 'JJ_Edition_Controller' ) ) {
                            try {
                                $show_upgrade_footer = ! JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                            } catch ( Exception $e ) {
                                // ignore
                            } catch ( Error $e ) {
                                // ignore
                            }
                        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
                            $show_upgrade_footer = ! in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
                        }

                        if ( $show_upgrade_footer ) {
                            _e( '더 많은 기능을 이용하려면 업그레이드하세요.', 'acf-css-really-simple-style-management-center' );
                        }
                        ?>
                    </div>
                    <div style="text-align: right;">
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-style-guide-footer-button" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        } catch ( Exception $e ) {
            // 오류 발생 시에도 기본 페이지 구조는 표시
            ?>
            <div class="wrap" style="padding: 20px;">
                <h1><?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?></h1>
                <div class="notice notice-error">
                    <p><strong><?php _e( '오류가 발생했습니다:', 'acf-css-really-simple-style-management-center' ); ?></strong> <?php echo esc_html( $e->getMessage() ); ?></p>
                </div>
            </div>
            <?php
        }
    }

    // 나머지 메서드들은 dev 버전과 동일하지만, 기능 제한 로직 추가 필요
    // save_settings_ajax, apply_temp_palette_ajax, save_temp_palette_ajax 등
    
    public function save_settings_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        if ( ! isset( $_POST['settings'] ) || ! is_array( $_POST['settings'] ) ) {
            wp_send_json_error( array( 'message' => __( '잘못된 데이터입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // [Phase 5 B] 멀티사이트 네트워크 전용 모드에서는 사이트별 저장 차단
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $ms = JJ_Multisite_Controller::instance();
                if ( method_exists( $ms, 'is_enabled' ) && method_exists( $ms, 'allow_site_override' ) ) {
                    if ( $ms->is_enabled() && ! $ms->allow_site_override() ) {
                        wp_send_json_error( array( 'message' => __( '이 사이트는 네트워크에서 스타일을 관리 중입니다. 네트워크 관리자에서 변경해주세요.', 'acf-css-really-simple-style-management-center' ) ) );
                    }
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        $old_settings = array();
        if ( function_exists( 'get_option' ) ) {
            $old_settings = (array) get_option( $this->options_key, array() );
        }

        $raw_settings = wp_unslash( $_POST['settings'] );
        
        // Free 버전: 제한된 설정만 저장
        $raw_settings = $this->filter_settings_for_free( $raw_settings );
        
        $sanitized_settings = $this->sanitize_options_recursive( $raw_settings );
        update_option( $this->options_key, $sanitized_settings );

        // [Phase 5.2] Webhook/자동화용 이벤트
        if ( function_exists( 'do_action' ) ) {
            do_action( 'jj_style_guide_settings_updated', $sanitized_settings, $old_settings, 'style_center_ajax' );
        }

        $this->sync_to_spokes( $sanitized_settings );

        JJ_CSS_Cache::instance()->flush();
        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * Free 버전: 저장 가능한 설정 필터링
     */
    private function filter_settings_for_free( $settings ) {
        // Free 버전: 시스템 팔레트, 얼터너티브 팔레트, 어나더 팔레트 제거
        if ( isset( $settings['palettes'] ) ) {
            unset( $settings['palettes']['system'] );
            unset( $settings['palettes']['alternative'] );
            unset( $settings['palettes']['another'] );
        }
        
        // Free 버전: 타이포그래피 H5, H6 제거
        if ( isset( $settings['typography'] ) ) {
            unset( $settings['typography']['h5'] );
            unset( $settings['typography']['h6'] );
        }
        
        // Free 버전: Secondary, Text 버튼 제거
        if ( isset( $settings['buttons'] ) ) {
            unset( $settings['buttons']['secondary'] );
            unset( $settings['buttons']['text'] );
        }
        
        return $settings;
    }

    public function apply_temp_palette_ajax() {
        // Free 버전: 임시 팔레트 사용 불가
        $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
        if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
            $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
        }
        wp_send_json_error( array( 
            'message' => $message
        ) );
    }

    public function save_temp_palette_ajax() {
        // Free 버전: 임시 팔레트 사용 불가
        $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
        if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
            $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
        }
        wp_send_json_error( array( 
            'message' => $message
        ) );
    }

    // 나머지 메서드들은 dev 버전과 동일 (sync_to_spokes, sync_from_customizer, sanitize_options_recursive 등)
    // 생략하거나 dev 버전 파일을 복사하여 수정

    private function sync_to_spokes( $hub_options ) {
        // Free 버전: WooCommerce만 동기화
        $brand_colors = $hub_options['palettes']['brand'] ?? array();
        
        // WooCommerce 동기화
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! is_array( $kwe ) ) {
            $kwe = array();
        }
        if ( isset( $brand_colors['primary_color'] ) ) {
            $kwe['product_quickview_button_color'] = $brand_colors['primary_color'];
        }
        if ( isset( $brand_colors['primary_color_hover'] ) ) {
            $kwe['product_quickview_button_color_hover'] = $brand_colors['primary_color_hover'];
        }
        update_option( 'kt_woo_extras', $kwe );
    }

    public function sync_from_customizer( $manager ) {
        $hub_options = (array) get_option( $this->options_key );
        $panel_settings = $manager->settings();
        
        foreach ( $panel_settings as $setting_id => $setting_obj ) {
            if ( strpos( $setting_id, $this->options_key . '[' ) === 0 ) {
                $keys = str_replace( array( $this->options_key, '[', ']' ), '', $setting_id );
                $keys = explode( '][', $keys );
                $this->set_nested_object_value( $hub_options, $keys, $setting_obj->post_value() );
            }
        }

        update_option( $this->options_key, $hub_options );
        JJ_CSS_Cache::instance()->flush();
    }

    private function set_nested_object_value( &$obj, $keys, $value ) {
        $current = &$obj;
        foreach ( $keys as $key ) {
            if ( ! isset( $current[$key] ) || ! is_array( $current[$key] ) ) {
                $current[$key] = array();
            }
            $current = &$current[$key];
        }
        $current = $value;
    }

    private function sanitize_options_recursive( $input ) {
        $output = array();
        foreach ( (array) $input as $key => $value ) {
            $raw_key = (string) $key;
            // [v5.3.8] WordPress 함수 안전 호출
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( $raw_key ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( $raw_key ) );
            if ( is_array( $value ) ) {
                $output[ $key ] = $this->sanitize_options_recursive( $value );
                continue;
            }
            // [v5.3.8] PHP 7.4 호환성: str_contains() 대신 strpos() 사용
            if ( strpos( $key, 'color' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $sanitized = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( $value ) : '';
                $output[ $key ] = $sanitized ? $sanitized : '';
                continue;
            }
            if ( strpos( $key, 'custom_css' ) !== false || strpos( $key, 'additional_css' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'wp_kses_post' ) ? wp_kses_post( $value ) : wp_strip_all_tags( $value );
                continue;
            }
            if ( strpos( $key, 'font_family' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
                continue;
            }
            // [v5.3.8] WordPress 함수 안전 호출
            $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
        }
        return $output;
    }

    public function load_options_and_init_strategies() {
        // [v5.3.5] 전체 메서드를 try-catch로 보호하여 치명적 오류 방지
        try {
            $this->environment_map = $this->scan_environment();
            $this->load_dynamic_adapters( $this->environment_map );
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options = $options;
            $this->sync_existing_settings();
            $this->brand_colors = $this->options['palettes']['brand'] ?? array();

            // [v5.3.5] 모든 전략 클래스 호출에 class_exists() 확인 추가
            // 전략 1: CSS 변수
            if ( class_exists( 'JJ_Strategy_1_CSS_Vars' ) ) {
                JJ_Strategy_1_CSS_Vars::instance()->init( $this->options );
            }
            
            // 전략 2: PHP 필터
            if ( class_exists( 'JJ_Strategy_2_PHP_Filters' ) ) {
                JJ_Strategy_2_PHP_Filters::instance()->init( $this->options, null, null, null, null );
            }
            
            // 전략 3: Dequeue (CSS 재정의)
            if ( class_exists( 'JJ_Strategy_3_Dequeue' ) ) {
                JJ_Strategy_3_Dequeue::instance()->init( $this->options );
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    private function sync_existing_settings() {
        $active_theme = wp_get_theme()->get_template();
        $needs_update = false;

        if ( ! isset( $this->options['palettes'] ) ) {
            $this->options['palettes'] = array();
        }
        if ( ! isset( $this->options['palettes']['brand'] ) ) {
            $this->options['palettes']['brand'] = array();
        }
        if ( ! isset( $this->options['typography'] ) ) {
            $this->options['typography'] = array();
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five 테마만 동기화
        if ( in_array( $active_theme, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
            // 기본 테마는 별도 설정이 없으므로 Customizer에서 가져오기
        }

        // WooCommerce 동기화 (Free 버전 지원)
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! empty( $kwe['product_quickview_button_color'] ) && empty( $this->options['palettes']['brand']['primary_color'] ) ) {
            $this->options['palettes']['brand']['primary_color'] = $kwe['product_quickview_button_color'];
            $needs_update = true;
        }
        if ( ! empty( $kwe['product_quickview_button_color_hover'] ) && empty( $this->options['palettes']['brand']['primary_color_hover'] ) ) {
            $this->options['palettes']['brand']['primary_color_hover'] = $kwe['product_quickview_button_color_hover'];
            $needs_update = true;
        }

        if ( $needs_update ) {
            update_option( $this->options_key, $this->options );
        }
    }

    private function load_dynamic_adapters( $environment_map ) {
        $active_adapters = $environment_map;
        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;
        $error_handler = JJ_Error_Handler::instance();

        // Free 버전: 테마 어댑터 제한 (Twenty Twenty-Four, Twenty Twenty-Five만)
        if ( ! empty( $active_adapters['themes'] ) ) {
            $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
            $active_adapters['themes'] = array_intersect( $active_adapters['themes'], $allowed_themes );
            
            foreach ( $active_adapters['themes'] as $theme_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-theme-' . sanitize_file_name( $theme_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Theme_' . str_replace( '-', '_', ucwords( $theme_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }

        // Free 버전: 플러그인 어댑터 제한 (WooCommerce만)
        if ( ! empty( $active_adapters['spokes'] ) ) {
            $allowed_plugins = array( 'woocommerce' );
            $active_adapters['spokes'] = array_intersect( $active_adapters['spokes'], $allowed_plugins );
            
            foreach ( $active_adapters['spokes'] as $spoke_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-spoke-' . sanitize_file_name( $spoke_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Spoke_' . str_replace( '-', '_', ucwords( $spoke_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }
    }

    private function scan_environment() {
        $config = $this->adapters_config;
        $spokes_to_check = $config['spokes'] ?? array();
        $themes_to_check = $config['themes'] ?? array();

        $active_plugins = (array) get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        $active_spoke_adapters = array();
        $active_theme_adapters = array();
        $current_theme_path = get_template_directory();
        $theme_dir_name = basename( $current_theme_path );

        // Free 버전: WooCommerce만 체크
        foreach ( $spokes_to_check as $adapter_slug => $plugin_path ) {
            if ( in_array( $plugin_path, $active_plugins ) && $adapter_slug === 'woocommerce' ) {
                $active_spoke_adapters[] = $adapter_slug;
            }
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five만 체크
        foreach ( $themes_to_check as $adapter_slug => $theme_path_suffix ) {
            if ( $theme_dir_name === $adapter_slug && in_array( $adapter_slug, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
                if ( file_exists( $current_theme_path . '/' . $theme_path_suffix ) ) {
                    $active_theme_adapters[] = $adapter_slug;
                }
            }
        }

        return array(
            'themes' => $active_theme_adapters,
            'spokes' => $active_spoke_adapters,
        );
    }

    public function render_debug_info() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $errors = JJ_Error_Handler::instance()->get_errors();

        echo '<div style="background: #001a33; color: #00ff8c; padding: 20px; font-family: monospace; font-size: 14px; line-height: 1.6; z-index: 99999; position: relative; margin: 20px; border-radius: 5px; white-space: pre-wrap; word-break: break-all; opacity: 0.9;">';
        echo '<strong>[ACF CSS DEBUG - FREE VERSION]</strong><br>';
        echo '====================<br>';
        echo '<strong>Edition:</strong> Free<br>';
        echo '<strong>Loaded Adapters:</strong><br>';
        if ( ! empty( $this->environment_map['themes'] ) ) {
            echo ' - Themes: ' . esc_html( implode( ', ', $this->environment_map['themes'] ) ) . '<br>';
        } else {
            echo ' - Themes: None<br>';
        }
        if ( ! empty( $this->environment_map['spokes'] ) ) {
            echo ' - Spokes: ' . esc_html( implode( ', ', $this->environment_map['spokes'] ) ) . '<br>';
        } else {
            echo ' - Spokes: None<br>';
        }
        echo '</div>';
    }
}

function jj_simple_style_guide_master_run() {
    // [Phase 5] 다국어 지원 로드
    load_plugin_textdomain( 'acf-css-really-simple-style-management-center', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // WordPress 함수가 로드되었는지 확인
    if ( ! function_exists( 'class_exists' ) || ! function_exists( 'file_exists' ) ) {
        return;
    }
    
    // [v5.3.9] 코드 무결성 모니터 초기화 (plugins_loaded 시점)
    
    // [Phase 4.5/4.99] Admin Center 초기화 (메뉴/상단바/관리자 UI)
    if ( class_exists( 'JJ_Admin_Center' ) ) {
        try {
            $admin_center = JJ_Admin_Center::instance();
            if ( method_exists( $admin_center, 'init' ) ) {
                $admin_center->init();
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v3.7.0] 백업 관리자 초기화
    if ( class_exists( 'JJ_Backup_Manager' ) ) {
        try {
            JJ_Backup_Manager::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] REST API 초기화
    if ( class_exists( 'JJ_Style_Guide_REST_API' ) ) {
        try {
            JJ_Style_Guide_REST_API::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.3] Extension Manager 초기화
    if ( class_exists( 'JJ_Extension_Manager' ) ) {
        try {
            JJ_Extension_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] Webhook Manager 초기화
    if ( class_exists( 'JJ_Webhook_Manager' ) ) {
        try {
            JJ_Webhook_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5 B] Multisite Controller 초기화
    if ( class_exists( 'JJ_Multisite_Controller' ) ) {
        try {
            JJ_Multisite_Controller::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v5.6.0] Visual Command Center 초기화
    if ( class_exists( 'JJ_Visual_Command_Center' ) ) {
        JJ_Visual_Command_Center::instance();
    }

    // [v5.7.0] AI Style Intelligence 초기화
    if ( class_exists( 'JJ_Smart_Palette' ) ) {
        JJ_Smart_Palette::instance();
    }

    // [v5.9.0] CSS Optimizer 초기화
    if ( class_exists( 'JJ_CSS_Optimizer' ) ) {
        JJ_CSS_Optimizer::instance();
    }

    // [v5.8.0] Cloud Manager 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Cloud_Manager' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) { // Premium 이상 체크 대용
            JJ_Cloud_Manager::instance();
        }
    }
    
    // [Phase 3] 템플릿 마켓 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Template_Market' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) {
            JJ_Template_Market::instance();
        }
    }
    
    // [Phase 5] Partner Hub 초기화 (Partner/Master만)
    if ( class_exists( 'JJ_Partner_Hub' ) ) {
        JJ_Partner_Hub::instance();
    }

    // [v4.2.2] 실험실(Labs) 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Labs_Center' ) ) {
        try {
            JJ_Labs_Center::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }


    if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
        try {
            JJ_Code_Integrity_Monitor::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Code Integrity Monitor: 초기화 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.9] 라이센스 강제 실행 초기화 (plugins_loaded 시점)
    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $enforcement_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-enforcement.php';
        if ( file_exists( $enforcement_file ) ) {
            require_once $enforcement_file;
            if ( class_exists( 'JJ_License_Enforcement' ) ) {
                try {
                    JJ_License_Enforcement::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ License Enforcement: 초기화 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // [v5.3.7] 안전 모드 플래그 확인 및 활성화
    if ( function_exists( 'get_option' ) && class_exists( 'JJ_Safe_Mode' ) ) {
        $safe_mode_enabled = get_option( 'jj_style_guide_safe_mode', false );
        if ( $safe_mode_enabled ) {
            $safe_mode_reason = get_option( 'jj_style_guide_safe_mode_reason', '활성화 중 오류 발생' );
            try {
                $safe_mode = JJ_Safe_Mode::instance();
                if ( method_exists( $safe_mode, 'enable' ) ) {
                    $safe_mode->enable( $safe_mode_reason );
                }
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 오류 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
    
    // 메인 클래스 인스턴스 생성
    if ( class_exists( 'JJ_Simple_Style_Guide_Master' ) ) {
        try {
            JJ_Simple_Style_Guide_Master::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.6] 기존 플러그인 마이그레이션 매니저 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $migration_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php';
        if ( file_exists( $migration_file ) ) {
            // 마이그레이션 매니저는 이미 safe_loader를 통해 로드되었을 수 있음
            // 클래스가 존재하는 경우에만 초기화
            if ( class_exists( 'JJ_Migration_Manager' ) ) {
                try {
                    JJ_Migration_Manager::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 오류 - ' . $e->getMessage() );
                    }
                } catch ( Error $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // 플러그인 업데이트 체크 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $updater_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php';
        if ( file_exists( $updater_file ) ) {
            require_once $updater_file;
            if ( class_exists( 'JJ_Plugin_Updater' ) ) {
                try {
                    new JJ_Plugin_Updater(
                        __FILE__,
                        'advanced-custom-style-manage-center',
                        "ACF CSS Master",
                        defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.4.0',
                        'DEV' // 마스터 버전
                    );
                } catch ( Exception $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 오류 - ' . $e->getMessage() );
                } catch ( Error $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
}

// [v5.3.8] plugins_loaded 훅 활성화
if ( function_exists( 'add_action' ) ) {
    add_action( 'plugins_loaded', 'jj_simple_style_guide_master_run', 10 );
}

/**
 * [v8.0.0] 안전 장치: 필수 헬퍼 함수 Fallback 정의
 * class-jj-admin-center.php 로드 실패 시에도 사이트가 멈추지 않도록 보장합니다.
 */
if ( ! function_exists( 'jj_style_guide_text' ) ) {
    function jj_style_guide_text( $key, $fallback = '' ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_text( $key, $fallback );
        }
        return $fallback;
    }
}

if ( ! function_exists( 'jj_style_guide_sections_layout' ) ) {
    function jj_style_guide_sections_layout() {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_sections_layout();
        }
        return array();
    }
}

if ( ! function_exists( 'jj_style_guide_section_index' ) ) {
    function jj_style_guide_section_index( $slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_section_index( $slug );
        }
        return null;
    }
}

if ( ! function_exists( 'jj_style_guide_is_tab_enabled' ) ) {
    function jj_style_guide_is_tab_enabled( $section_slug, $tab_slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->is_tab_enabled( $section_slug, $tab_slug );
        }
        return true;
    }
}

```

```php
<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Customizer 색상 동기화 클래스
 * 
 * WordPress Customizer에서 색상 값을 가져와서 플러그인 옵션으로 동기화합니다.
 * - 플러그인 활성화 시 자동 동기화
 * - 수동 새로고침 버튼을 통한 동기화
 * 
 * @since v3.8.0
 */
class JJ_Customizer_Sync {
    
    private static $instance = null;
    private $active_theme;
    private $theme_slug;
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->active_theme = wp_get_theme();
        $this->theme_slug = $this->active_theme->get_template();
    }
    
    /**
     * Customizer에서 브랜드 팔레트 색상 가져오기
     * 
     * @param bool $force_update 기존 값이 있어도 강제 업데이트 여부
     * @return array 동기화된 브랜드 팔레트 배열
     */
    public function sync_brand_palette_from_customizer( $force_update = false ) {
        $synced = array();
        
        // WordPress 기본 Customizer 색상
        $background_color = get_theme_mod( 'background_color', '' );
        $header_textcolor = get_theme_mod( 'header_textcolor', '' );
        
        // 테마별 Customizer 색상 가져오기
        if ( 'kadence' === $this->theme_slug ) {
            $synced = $this->sync_kadence_brand_palette( $force_update );
        } else if ( 'astra' === $this->theme_slug ) {
            $synced = $this->sync_astra_brand_palette( $force_update );
        } else {
            // 기본 WordPress Customizer 색상 사용
            $synced = $this->sync_default_brand_palette( $force_update );
        }
        
        return $synced;
    }
    
    /**
     * Customizer에서 시스템 팔레트 색상 가져오기
     * 
     * @param bool $force_update 기존 값이 있어도 강제 업데이트 여부
     * @return array 동기화된 시스템 팔레트 배열
     */
    public function sync_system_palette_from_customizer( $force_update = false ) {
        $synced = array();
        
        // WordPress 기본 Customizer 색상
        $background_color = get_theme_mod( 'background_color', '' );
        $header_textcolor = get_theme_mod( 'header_textcolor', '' );
        
        // 테마별 Customizer 색상 가져오기
        if ( 'kadence' === $this->theme_slug ) {
            $synced = $this->sync_kadence_system_palette( $force_update );
        } else if ( 'astra' === $this->theme_slug ) {
            $synced = $this->sync_astra_system_palette( $force_update );
        } else {
            // 기본 WordPress Customizer 색상 사용
            $synced = $this->sync_default_system_palette( $force_update );
        }
        
        return $synced;
    }
    
    /**
     * Kadence 테마 브랜드 팔레트 동기화
     */
    private function sync_kadence_brand_palette( $force_update = false ) {
        $kt_options = get_option( 'kadence_theme_options', array() );
        $synced = array();
        
        if ( ! empty( $kt_options['global_palette']['palette1'] ) ) {
            $synced['primary_color'] = $kt_options['global_palette']['palette1'];
        }
        if ( ! empty( $kt_options['global_palette']['palette2'] ) ) {
            $synced['primary_color_hover'] = $kt_options['global_palette']['palette2'];
        }
        if ( ! empty( $kt_options['global_palette']['palette3'] ) ) {
            $synced['secondary_color'] = $kt_options['global_palette']['palette3'];
        }
        if ( ! empty( $kt_options['global_palette']['palette4'] ) ) {
            $synced['secondary_color_hover'] = $kt_options['global_palette']['palette4'];
        }
        
        // Customizer에서 직접 가져오기 (Kadence Customizer API)
        $customizer_colors = $this->get_kadence_customizer_colors();
        if ( ! empty( $customizer_colors['palette_1'] ) && ( $force_update || empty( $synced['primary_color'] ) ) ) {
            $synced['primary_color'] = $customizer_colors['palette_1'];
        }
        if ( ! empty( $customizer_colors['palette_2'] ) && ( $force_update || empty( $synced['primary_color_hover'] ) ) ) {
            $synced['primary_color_hover'] = $customizer_colors['palette_2'];
        }
        
        return $synced;
    }
    
    /**
     * Kadence 테마 시스템 팔레트 동기화
     */
    private function sync_kadence_system_palette( $force_update = false ) {
        $kt_options = get_option( 'kadence_theme_options', array() );
        $synced = array();
        
        // 사이트 배경색
        if ( ! empty( $kt_options['site_background'] ) ) {
            $synced['site_bg'] = $kt_options['site_background'];
        } else if ( ! empty( $kt_options['global_palette']['palette5'] ) ) {
            $synced['site_bg'] = $kt_options['global_palette']['palette5'];
        } else {
            $background_color = get_theme_mod( 'background_color', '' );
            if ( $background_color ) {
                $synced['site_bg'] = '#' . $background_color;
            }
        }
        
        // 콘텐츠 배경색
        if ( ! empty( $kt_options['content_background'] ) ) {
            $synced['content_bg'] = $kt_options['content_background'];
        } else if ( ! empty( $synced['site_bg'] ) ) {
            $synced['content_bg'] = $synced['site_bg']; // Fallback
        }
        
        // 텍스트 색상
        if ( ! empty( $kt_options['global_palette']['palette6'] ) ) {
            $synced['text_color'] = $kt_options['global_palette']['palette6'];
        }
        
        // 링크 색상
        if ( ! empty( $kt_options['link_color'] ) ) {
            $synced['link_color'] = $kt_options['link_color'];
        } else if ( ! empty( $kt_options['global_palette']['palette9'] ) ) {
            $synced['link_color'] = $kt_options['global_palette']['palette9'];
        }
        
        // Customizer에서 직접 가져오기
        $customizer_colors = $this->get_kadence_customizer_colors();
        if ( ! empty( $customizer_colors['site_background'] ) && ( $force_update || empty( $synced['site_bg'] ) ) ) {
            $synced['site_bg'] = $customizer_colors['site_background'];
        }
        if ( ! empty( $customizer_colors['content_background'] ) && ( $force_update || empty( $synced['content_bg'] ) ) ) {
            $synced['content_bg'] = $customizer_colors['content_background'];
        }
        if ( ! empty( $customizer_colors['text_color'] ) && ( $force_update || empty( $synced['text_color'] ) ) ) {
            $synced['text_color'] = $customizer_colors['text_color'];
        }
        if ( ! empty( $customizer_colors['link_color'] ) && ( $force_update || empty( $synced['link_color'] ) ) ) {
            $synced['link_color'] = $customizer_colors['link_color'];
        }
        
        return $synced;
    }
    
    /**
     * Kadence Customizer 색상 직접 가져오기
     */
    private function get_kadence_customizer_colors() {
        $colors = array();
        
        // Kadence는 Customizer에서 직접 색상을 저장하는 방식
        // get_theme_mod를 통해 접근 가능한 경우
        $kt_customizer = get_theme_mod( 'kadence_theme_options', array() );
        
        if ( is_array( $kt_customizer ) && ! empty( $kt_customizer ) ) {
            if ( ! empty( $kt_customizer['global_palette'] ) ) {
                $palette = $kt_customizer['global_palette'];
                $colors['palette_1'] = $palette['palette1'] ?? '';
                $colors['palette_2'] = $palette['palette2'] ?? '';
                $colors['site_background'] = $palette['palette5'] ?? '';
                $colors['text_color'] = $palette['palette6'] ?? '';
                $colors['link_color'] = $palette['palette9'] ?? '';
            }
        }
        
        // get_option으로도 시도
        $kt_options = get_option( 'kadence_theme_options', array() );
        if ( ! empty( $kt_options['global_palette'] ) ) {
            $palette = $kt_options['global_palette'];
            if ( empty( $colors['palette_1'] ) && ! empty( $palette['palette1'] ) ) {
                $colors['palette_1'] = $palette['palette1'];
            }
            if ( empty( $colors['palette_2'] ) && ! empty( $palette['palette2'] ) ) {
                $colors['palette_2'] = $palette['palette2'];
            }
            if ( empty( $colors['site_background'] ) && ! empty( $palette['palette5'] ) ) {
                $colors['site_background'] = $palette['palette5'];
            }
            if ( empty( $colors['text_color'] ) && ! empty( $palette['palette6'] ) ) {
                $colors['text_color'] = $palette['palette6'];
            }
            if ( empty( $colors['link_color'] ) && ! empty( $palette['palette9'] ) ) {
                $colors['link_color'] = $palette['palette9'];
            }
        }
        
        return $colors;
    }
    
    /**
     * Astra 테마 브랜드 팔레트 동기화
     */
    private function sync_astra_brand_palette( $force_update = false ) {
        $synced = array();
        $astra_options = get_option( 'astra-settings', array() );
        
        if ( ! empty( $astra_options['theme-color'] ) ) {
            $synced['primary_color'] = $astra_options['theme-color'];
        }
        
        return $synced;
    }
    
    /**
     * Astra 테마 시스템 팔레트 동기화
     */
    private function sync_astra_system_palette( $force_update = false ) {
        $synced = array();
        $astra_options = get_option( 'astra-settings', array() );
        
        if ( ! empty( $astra_options['background-color'] ) ) {
            $synced['site_bg'] = $astra_options['background-color'];
        }
        if ( ! empty( $astra_options['text-color'] ) ) {
            $synced['text_color'] = $astra_options['text-color'];
        }
        if ( ! empty( $astra_options['link-color'] ) ) {
            $synced['link_color'] = $astra_options['link-color'];
        }
        
        return $synced;
    }
    
    /**
     * 기본 WordPress Customizer 브랜드 팔레트 동기화
     */
    private function sync_default_brand_palette( $force_update = false ) {
        $synced = array();
        
        // WordPress 기본 Customizer 색상 사용
        $header_textcolor = get_theme_mod( 'header_textcolor', '' );
        if ( $header_textcolor && $header_textcolor !== 'blank' ) {
            $synced['primary_color'] = '#' . $header_textcolor;
        }
        
        return $synced;
    }
    
    /**
     * 기본 WordPress Customizer 시스템 팔레트 동기화
     */
    private function sync_default_system_palette( $force_update = false ) {
        $synced = array();
        
        // WordPress 기본 Customizer 색상 사용
        $background_color = get_theme_mod( 'background_color', '' );
        if ( $background_color ) {
            $synced['site_bg'] = '#' . $background_color;
            $synced['content_bg'] = '#' . $background_color;
        }
        
        return $synced;
    }
    
    /**
     * 플러그인 옵션에 동기화된 색상 저장
     * 
     * @param string $palette_type 'brand' 또는 'system'
     * @param array $colors 동기화된 색상 배열
     * @param bool $merge 기존 값과 병합할지 여부
     */
    public function save_synced_colors( $palette_type, $colors, $merge = true ) {
        if ( ! in_array( $palette_type, array( 'brand', 'system' ) ) ) {
            return false;
        }
        
        $options = get_option( JJ_STYLE_GUIDE_OPTIONS_KEY, array() );
        
        if ( ! isset( $options['palettes'] ) ) {
            $options['palettes'] = array();
        }
        if ( ! isset( $options['palettes'][ $palette_type ] ) ) {
            $options['palettes'][ $palette_type ] = array();
        }
        
        if ( $merge ) {
            $options['palettes'][ $palette_type ] = array_merge( $options['palettes'][ $palette_type ], $colors );
        } else {
            $options['palettes'][ $palette_type ] = $colors;
        }
        
        return update_option( JJ_STYLE_GUIDE_OPTIONS_KEY, $options );
    }
    
    /**
     * AJAX 핸들러: 현재 색상 불러오기
     */
    public static function ajax_load_current_colors() {
        // [Safety] nonce 파라미터 키/액션명 불일치로 인한 실패 방지
        // - style-guide-editor.js: nonce 키를 사용
        // - 일부 AJAX: security 키를 사용
        if ( isset( $_POST['security'] ) ) {
            check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        } else {
            check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $palette_type = isset( $_POST['palette_type'] ) ? sanitize_text_field( $_POST['palette_type'] ) : 'brand';
        $force_update = isset( $_POST['force_update'] ) ? (bool) $_POST['force_update'] : false;
        
        $sync = self::instance();
        
        if ( 'brand' === $palette_type ) {
            $colors = $sync->sync_brand_palette_from_customizer( $force_update );
        } else if ( 'system' === $palette_type ) {
            $colors = $sync->sync_system_palette_from_customizer( $force_update );
        } else {
            wp_send_json_error( array( 'message' => __( '잘못된 팔레트 타입입니다.', 'jj-style-guide' ) ) );
        }
        
        // 옵션에 저장
        if ( ! empty( $colors ) ) {
            $sync->save_synced_colors( $palette_type, $colors, true );
        }
        
        wp_send_json_success( array(
            'palette_type' => $palette_type,
            'colors' => $colors,
            'message' => __( '색상이 성공적으로 불러와졌습니다.', 'jj-style-guide' ),
        ) );
    }
}

```

```php
<?php
/**
 * Plugin Name:       ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager (Master)
 * Plugin URI:        https://j-j-labs.com
 * Description:       WordPress 웹사이트의 모든 스타일 요소(색상 팔레트, 타이포그래피, 버튼, 폼)를 중앙에서 일관되게 관리하는 통합 스타일 관리 플러그인입니다. Free 버전은 기본적인 스타일 관리 기능을 제공하며, 브랜드 일관성을 유지하고 디자인 시스템을 효율적으로 운영할 수 있습니다. Pro 버전 플러그인을 함께 설치하면 Basic, Premium, Unlimited 기능을 사용할 수 있습니다. WordPress Customizer와 완벽 통합되어 실시간 미리보기와 함께 직관적인 스타일 관리가 가능합니다.
 * Version:           8.0.0
 * Author:            3J Labs
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://poetryflow.blog
 * Text Domain:       acf-css-really-simple-style-management-center
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ⚠️ 중요: Git 경로와 로컬 경로는 완전히 별개입니다
 * 
 * - 로컬 작업 경로: C:\Users\computer\OneDrive\Desktop\클라우드 백업\OneDrive\문서\jj-css-premium
 * - Git worktree 경로: C:\Users\computer\.cursor\worktrees\jj-css-premium\tci
 * - 두 경로는 서로 독립적이며, Git에서 작업한 내용은 반드시 로컬 경로에 동기화해야 함
 * - 자세한 내용은 DEVELOPMENT_PRINCIPLES.md의 "Git 및 로컬 경로 관리 원칙" 섹션 참조
 */

// 플러그인 상수 정의
// [v5.1.6] 전면 재검토 및 오류 방지: 안전한 파일 로더 추가, 모든 버전의 require_once 안전 처리, 결제 유도 문구 추가, 플러그인 목록 페이지 바로가기 링크 추가
// [v5.1.6] Comprehensive review and error prevention: Safe file loader added, all versions' require_once safely handled, purchase prompts added, plugin list page quick links added
// [v1.0.2] 모든 버전 플러그인 활성화 안전성 최종 확보, WordPress 함수 호출 안전 처리
define( 'JJ_STYLE_GUIDE_VERSION', '8.0.0' ); // [v7.0.0] Operation Deep Dive - Stability & Reliability Overhaul

// WordPress 함수가 로드되었는지 확인 후 상수 정의
if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    if ( function_exists( 'plugin_dir_path' ) ) {
        define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 직접 경로 계산
        define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
    }
}

if ( ! defined( 'JJ_STYLE_GUIDE_URL' ) ) {
    if ( function_exists( 'plugin_dir_url' ) ) {
        define( 'JJ_STYLE_GUIDE_URL', plugin_dir_url( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 기본값 사용 (나중에 업데이트됨)
        define( 'JJ_STYLE_GUIDE_URL', '' );
    }
}
define( 'JJ_STYLE_GUIDE_OPTIONS_KEY', 'jj_style_guide_options' );
define( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY', 'jj_style_guide_temp_options' );
define( 'JJ_STYLE_GUIDE_ADMIN_TEXT_KEY', 'jj_style_guide_admin_texts' );
define( 'JJ_STYLE_GUIDE_PAGE_SLUG', 'acf-css-really-simple-style-guide' );
define( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY', 'jj_style_guide_cockpit_page_id' );
define( 'JJ_STYLE_GUIDE_DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
// Pro 버전 활성화 코드 확인 (plugins_loaded 훅에서 처리)
// WordPress가 로드되기 전에는 기본값 사용
define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', 'MASTER' ); // 기본값, plugins_loaded에서 업데이트됨
define( 'JJ_STYLE_GUIDE_EDITION', 'master' ); // 기본 에디션은 free

// [v3.8.0] 환경 구분
if ( ! defined( 'JJ_STYLE_GUIDE_ENV' ) ) {
    define( 'JJ_STYLE_GUIDE_ENV', 'live' );
}

// [v5.3.7] Free 버전은 독립적으로 작동합니다. Master 버전 의존성 제거
// Free 버전은 자체 includes 폴더에 필요한 모든 파일을 포함합니다.

// [v5.4.1] 안전한 파일 로더 적용
$safe_loader = null;
try {
    $safe_loader_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-loader.php';
    if ( file_exists( $safe_loader_path ) ) {
        require_once $safe_loader_path;
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'instance' ) ) {
            $safe_loader = JJ_Safe_Loader::instance();
        }
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Failed: ' . $e->getMessage() );
} catch ( Error $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Fatal Error: ' . $e->getMessage() );
}

// 라이센스 매니저 로드
if ( $safe_loader ) {
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php', false );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-version-features.php', true );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-code-integrity-monitor.php', true );
}

// 핵심 로직 파일 로드 (Master 전용 파일은 아직 비활성화)
try {
    if ( $safe_loader && method_exists( $safe_loader, 'safe_require' ) ) {
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-handler.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-logger.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-admin-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-backup-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/visual-command-center/class-jj-visual-command-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/ai-style-intelligence/class-jj-smart-palette.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-cloud-manager.php', true );
        // [Phase 3] 템플릿 마켓
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-template-market.php', true );
        // [Phase 5] Partner Hub
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/partner-hub/class-jj-partner-hub.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-labs-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-selector-registry.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-injector.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-options-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-edition-controller.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-theme-metadata.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-0-customizer.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-1-css-vars.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-2-php-filters.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-3-dequeue.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-sync.php', false );
        // [Phase 5.2] REST API (설정 조회/업데이트)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/api/class-jj-style-guide-rest-api.php', true );
        // [Phase 5.3] 확장 플러그인 시스템 (Extension API)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/interface-jj-style-guide-extension.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/class-jj-extension-manager.php', true );
        // [Phase 5.2] Webhooks (자동화)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/integrations/class-jj-webhook-manager.php', true );
        // [Phase 5 B] Multisite Network Control
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/multisite/class-jj-multisite-controller.php', true );
        
        // Master 전용 파일들 (일단 주석 처리)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-sync-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-memory-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-mode.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-optimizer.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-issuer.php', false );
    } else {
        // Fallback for Safe Loader missing
        require_once JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
        // ... (Fallback은 일단 생략, Safe Loader가 있다고 가정)
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Master Load Error: ' . $e->getMessage() );
}



/**
 * 활성화 훅 래퍼 함수 (치명적 오류 방지)
 * [v5.1.7] 전체 함수를 try-catch로 감싸고 상수 정의 확인 추가
 */
function jj_simple_style_guide_master_activate() {
    try {
        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // 상수가 정의되지 않은 경우 기본 경로 사용
            if ( function_exists( 'plugin_dir_path' ) ) {
                define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
            } else {
                define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
            }
        }
        
        // 활성화 매니저 클래스가 로드되지 않은 경우 로드 시도
        
        if ( ! class_exists( 'JJ_Activation_Manager' ) ) {
            $activation_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php';
            if ( file_exists( $activation_file ) ) {
                require_once $activation_file;
            } else {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 파일을 찾을 수 없습니다. 기본 활성화만 수행합니다.' );
                }
                return;
            }
        }
        
        // 클래스가 존재하는지 확인 후 활성화 실행
        if ( class_exists( 'JJ_Activation_Manager' ) && method_exists( 'JJ_Activation_Manager', 'activate' ) ) {
            try {
                JJ_Activation_Manager::activate();
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 오류 발생 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 치명적 오류 발생 - ' . $e->getMessage() );
                }
            }
        } else {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 클래스를 사용할 수 없습니다.' );
            }
        }
    } catch ( Exception $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 예외 발생 - ' . $e->getMessage() );
        }
    } catch ( Error $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 치명적 오류 발생 - ' . $e->getMessage() );
        }
    }
}
register_activation_hook( __FILE__, 'jj_simple_style_guide_master_activate' );

/**
 * Free 버전 전용 메인 컨트롤 클래스
 */
final class JJ_Simple_Style_Guide_Master {

    private static $instance = null;
    private $options = array();
    private $cockpit_page_id = 0;
    private $options_key = '';
    private $brand_colors = array();
    private $environment_map = array();
    private $adapters_config = array();
    private $license_manager = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 상수 정의 확인 후 할당
        if ( defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) {
            $this->options_key = JJ_STYLE_GUIDE_OPTIONS_KEY;
        }
        
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $this->license_manager = JJ_License_Manager::instance();
        }
        
        if ( defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) && function_exists( 'get_option' ) ) {
            $this->cockpit_page_id = (int) get_option( JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY );
        }

        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // [v5.3.7] Free 버전은 자체 config 폴더에서 로드
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
            if ( file_exists( $config_file ) ) {
                $this->adapters_config = include( $config_file );
                // [v5.5.0] Edition Controller를 통한 동적 어댑터 필터링
                if ( class_exists( 'JJ_Edition_Controller' ) ) {
                    $this->adapters_config = JJ_Edition_Controller::instance()->filter_adapters( $this->adapters_config );
                }
                
                
            }
        }

        // WordPress 함수가 로드된 후에만 훅 등록
        // [v5.1.9] plugin_basename() 안전 호출 추가
        // [v5.3.9] 모든 훅 등록을 조건문 안으로 이동하여 안전성 확보
        if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) && function_exists( 'plugin_basename' ) ) {
            add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
            add_action( 'admin_menu', array( $this, 'add_admin_menu_links' ) );
            $plugin_basename = function_exists( 'plugin_basename' ) ? plugin_basename( __FILE__ ) : basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );
            add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_plugin_settings_link' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            add_action( 'wp_ajax_jj_save_style_guide', array( $this, 'save_settings_ajax' ) );
            add_action( 'wp_ajax_jj_apply_temp_palette', array( $this, 'apply_temp_palette_ajax' ) );
            add_action( 'wp_ajax_jj_save_temp_palette', array( $this, 'save_temp_palette_ajax' ) );
            // [v3.8.0] Customizer 색상 새로고침 (팔레트 동기화)
            if ( class_exists( 'JJ_Customizer_Sync' ) ) {
                add_action( 'wp_ajax_jj_load_current_colors', array( 'JJ_Customizer_Sync', 'ajax_load_current_colors' ) );
            }
            add_action( 'init', array( $this, 'load_options_and_init_strategies' ), 5 );
            add_action( 'init', array( $this, 'init_sync_manager' ), 10 );
            
            
            if ( JJ_STYLE_GUIDE_DEBUG ) {
                add_action( 'wp_footer', array( $this, 'render_debug_info' ), 999 );
                add_action( 'admin_footer', array( $this, 'render_debug_info' ), 999 );
            }
        }

        // Customizer 통합 (제한적)
        
        try {
            $customizer_manager_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php';
            if ( file_exists( $customizer_manager_path ) ) {
                require_once $customizer_manager_path;
                if ( class_exists( 'JJ_Customizer_Manager' ) ) {
                    $customizer_manager = JJ_Customizer_Manager::instance();
                    if ( method_exists( $customizer_manager, 'init' ) ) {
                        $customizer_manager->init();
                    }
                }
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }

    }

    /**
     * Free 버전: 어댑터 제한
     * 
     * @param array $config
     * @return array
     */
    private function limit_adapters_for_free( $config ) {
        // Free 버전: 테마 2개만 지원 (Twenty Twenty-Four, Twenty Twenty-Five)
        $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
        if ( isset( $config['themes'] ) && is_array( $config['themes'] ) ) {
            $config['themes'] = array_intersect_key( $config['themes'], array_flip( $allowed_themes ) );
        }

        // Free 버전: 플러그인 1개만 지원 (WooCommerce)
        $allowed_plugins = array( 'woocommerce' );
        if ( isset( $config['spokes'] ) && is_array( $config['spokes'] ) ) {
            $config['spokes'] = array_intersect_key( $config['spokes'], array_flip( $allowed_plugins ) );
        }

        return $config;
    }

    /**
     * Free 버전 워터마크 추가
     */
    public function add_free_watermark() {
        if ( ! is_admin() && current_user_can( 'manage_options' ) ) {
            echo '<div style="position: fixed; bottom: 10px; right: 10px; background: rgba(0, 0, 0, 0.7); color: #fff; padding: 8px 12px; font-size: 11px; border-radius: 4px; z-index: 99999; font-family: Arial, sans-serif;">';
            echo '<a href="' . esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ) . '" style="color: #fff; text-decoration: none;">JJ Style Guide Free</a>';
            echo '</div>';
        }
    }

    /**
     * 업그레이드 프롬프트 표시
     */
    public function show_upgrade_prompt() {
        // [Safety Lock] MASTER 또는 PARTNER 버전은 업그레이드 프롬프트 표시 안 함
        if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'partner' ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        $license_manager = null;
        $upgrade_url = 'https://j-j-labs.com'; // 기본값
        
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
                if ( $license_manager ) {
                    $upgrade_url = $license_manager->get_purchase_url( 'upgrade' );
                }
            }
        }
        ?>
        <div class="notice notice-info is-dismissible" style="background: #f0f6fc; border-left-color: #2271b1;">
            <p style="font-size: 14px; line-height: 1.8;">
                <strong><?php _e( '현재 버전을 사용 중입니다.', 'acf-css-really-simple-style-management-center' ); ?></strong><br>
                <?php _e( '더 많은 기능을 이용하려면 업그레이드하세요:', 'acf-css-really-simple-style-management-center' ); ?>
                <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" class="button button-primary" style="margin-left: 10px;">
                    <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
            </p>
            <ul style="margin: 10px 0; padding-left: 20px; list-style: disc;">
                <li><?php _e( '모든 팔레트 사용 가능 (브랜드, 시스템, 얼터너티브, 어나더, 임시)', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '모든 테마 및 플러그인 어댑터 지원', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '관리자 센터 및 실험실 센터 전체 기능', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '섹션별 내보내기/불러오기', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '고급 색상 선택 기능 (스포이드, 팔레트 불러오기)', 'acf-css-really-simple-style-management-center' ); ?></li>
            </ul>
        </div>
        <?php
    }

    public function init_sync_manager() {
        add_action( 'customize_save_after', array( $this, 'sync_from_customizer' ) );
        JJ_Sync_Manager::instance()->init( $this->options, $this->cockpit_page_id );
    }

    /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * Free 버전: 스타일 센터만 표시
     * [v5.1.9] 안전성 강화 - try-catch 추가
     */
            /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * [Phase 4.99] 퀵 링크 확장: 설정 | 스타일 | 메뉴 | 비주얼
     */
    public function add_plugin_settings_link( $links ) {
        try {
            if ( ! function_exists( 'admin_url' ) || ! function_exists( 'esc_url' ) ) {
                return $links;
            }
            
            $new_links = array();
            
            // 1. 설정 (Admin Center 메인)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ) . '" style="font-weight: 600;">' . __( 'Settings', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 2. 스타일 (Style Guide)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ) ) . '">' . __( 'Styles', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 3. 어드민 메뉴 (Admin Menu Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#admin-menu' ) ) . '">' . __( 'Menu', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 4. 비주얼 (Visual Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#visual' ) ) . '">' . __( 'Visual', 'acf-css-really-simple-style-management-center' ) . '</a>';

            // 기존 링크와 병합
            return array_merge( $new_links, $links );
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 오류 - ' . $e->getMessage() );
            }
            return $links;
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 치명적 오류 - ' . $e->getMessage() );
            }
            return $links;
        }
    }

    public function add_admin_menu_page() {
        add_options_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );
    }

    public function add_admin_menu_links() {
        // [Phase 4.5/4.99] "모양/도구/설정" 어디서나 동일한 화면으로 진입할 수 있도록 서브메뉴 추가
        add_theme_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        add_management_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        if ( $this->cockpit_page_id ) {
            add_submenu_page(
                'themes.php',
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                'manage_options',
                'post.php?post=' . $this->cockpit_page_id . '&action=edit',
                '',
                100
            );
        }
    }

    public function enqueue_admin_scripts( $hook_suffix ) {
        $is_settings_page = in_array(
            $hook_suffix,
            array(
                'settings_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'appearance_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'tools_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
            ),
            true
        );

        $is_cockpit_page = false;
        if ( 'post.php' === $hook_suffix ) {
            global $post;
            if ( $post && (int) $post->ID === $this->cockpit_page_id ) {
                $is_cockpit_page = true;
            }
        }

        if ( ! $is_settings_page && ! $is_cockpit_page ) {
            return;
        }

        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;

        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        // [v5.3.7] Free 버전은 자체 assets 폴더에서 로드
        wp_enqueue_style( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-editor.css', array( 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION );
        wp_enqueue_script( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-editor.js', array( 'jquery', 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION, true );

        $preview_page_id = $this->cockpit_page_id;
        $preview_url = $preview_page_id ? get_permalink( $preview_page_id ) : '';

        wp_localize_script(
            'jj-style-guide-editor',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'temp_palette_nonce' => wp_create_nonce( 'jj_temp_palette_nonce' ),
                'settings' => $this->options,
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'edition'  => 'free', // Free 버전 표시
                'i18n'     => array(
                    'saving' => __( '저장 중...', 'acf-css-really-simple-style-management-center' ),
                    'saved'  => __( '스타일 저장됨', 'acf-css-really-simple-style-management-center' ),
                    'applied' => __( '임시 팔레트 적용됨! \'스타일 저장\'을 눌러야 최종 반영됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'error'  => __( '오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
                    'confirm_apply' => __( '정말로 \'임시 팔레트\'의 값으로 덮어쓰시겠습니까?\n이 작업은 즉시 UI에 반영되지만, \n\'스타일 저장\' 버튼을 눌러야 최종 저장됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'preview_url' => $preview_url ? esc_url( $preview_url ) : '',
                    'preview_page_id' => $preview_page_id,
                ),
            )
        );
    }

    public function render_style_guide_page() {
        try {
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options      = $options;
            $temp_options       = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY );
            $options            = $this->options;
            $cockpit_page_id    = $this->cockpit_page_id;
            $preview_url        = $cockpit_page_id ? get_permalink( $cockpit_page_id ) : '';

        // 옵션이 비어있으면 기본값 설정
        if ( empty( $this->options ) ) {
            $this->options = array(
                'palettes' => array(
                    'brand' => array(
                        'primary_color' => '',
                        'primary_color_hover' => '',
                        'secondary_color' => '',
                        'secondary_color_hover' => '',
                    ),
                    'system' => array(), // Free 버전: 시스템 팔레트 비활성화
                    'alternative' => array(), // Free 버전: 얼터너티브 팔레트 비활성화
                    'another' => array(), // Free 버전: 어나더 팔레트 비활성화
                ),
                'typography' => array(),
                'buttons' => array(),
                'forms' => array(),
                'contexts' => array(),
            );
            $options = $this->options;
        }
        ?>
        <div id="jj-style-guide-wrapper" class="wrap jj-style-guide-wrap" style="background: #f0f0f1; margin: 0; padding: 0;">
            <form id="jj-style-guide-form">

                <div class="jj-style-guide-header" style="top: 32px; background: #fff; padding: 15px 25px; border-bottom: 1px solid #c3c4c7; z-index: 999; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h1 style="margin: 0; font-size: 24px;">
                            <?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?> (v<?php echo esc_html( JJ_STYLE_GUIDE_VERSION ); ?>) 
                            <?php if ( ! class_exists("JJ_Edition_Controller") || JJ_Edition_Controller::instance()->get_current_edition() === "free" ) : ?><span style="background: #d63638; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 14px; font-weight: 600;">Free</span><?php endif; ?>
                        </h1>
                        <?php
                        $license_manager = null;
                        $purchase_url = 'https://j-j-labs.com'; // 기본값
                        
                        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                            if ( class_exists( 'JJ_License_Manager' ) ) {
                                $license_manager = JJ_License_Manager::instance();
                                if ( $license_manager ) {
                                    $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                                }
                            }
                        }
                        ?>
                        <?php if ( ! class_exists("JJ_Edition_Controller") || ! JJ_Edition_Controller::instance()->is_at_least("partner") ) : ?><a href="<?php echo esc_url( $purchase_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary" style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px;">
                            <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                        </a><?php endif; ?>
                        <?php if ( $preview_url ) : ?>
                        <button type="button" 
                                class="button button-secondary" 
                                id="jj-preview-button"
                                style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> <?php _e( '실시간 미리보기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="jj-header-actions" style="display: flex; align-items: center; gap: 10px;">
                        <span id="jj-save-success" style="display: none; vertical-align: middle; color: #2271b1; font-weight: 600; margin-right: 15px;"></span>
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-save-style-guide" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
                <div class="jj-style-guide-content" style="margin: 20px auto; background: #ffffff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 1px;">
                    <?php
                    // Free 버전: 제한된 섹션만 표시
                    // [v5.3.7] Free 버전은 자체 includes/editor-views 폴더에서 로드
                    $sections_map = array(
                        'colors'     => 'includes/editor-views/view-section-colors.php',
                        'typography' => 'includes/editor-views/view-section-typography.php',
                        'buttons'    => 'includes/editor-views/view-section-buttons.php',
                        'forms'      => 'includes/editor-views/view-section-forms.php',
                    );

                    // Free 버전: 섹션 순서 고정
                    $sections_to_render = array();
                    foreach ( $sections_map as $slug => $relative_path ) {
                        $sections_to_render[] = array(
                            'slug' => $slug,
                            'file' => JJ_STYLE_GUIDE_PATH . $relative_path,
                            'order'=> 0, // 순서는 UI에서만 표시
                        );
                    }
                    ?>
                    <div id="jj-sections-sortable" class="jj-sections-sortable">
                        <?php
                        foreach ( $sections_to_render as $section ) {
                            // Free 버전: 기본 뷰 파일 사용 (제한 로직은 뷰 파일 내부에서 처리)
                            $default_file = JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-section-' . $section['slug'] . '.php';
                            if ( file_exists( $section['file'] ) ) {
                                echo '<div class="jj-section-wrapper" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $section['file'];
                                echo '</div>';
                            } elseif ( file_exists( $default_file ) ) {
                                // Free 버전 전용 뷰가 없으면 기본 뷰 사용 (제한 로직 적용 필요)
                                echo '<div class="jj-section-wrapper jj-section-free-version" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $default_file;
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="jj-style-guide-footer" style="margin: 20px auto; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: #666; font-size: 13px;">
                        <?php
                        // Master/Partner에서는 업그레이드 유도 문구를 절대 노출하지 않음
                        $show_upgrade_footer = true;
                        if ( class_exists( 'JJ_Edition_Controller' ) ) {
                            try {
                                $show_upgrade_footer = ! JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                            } catch ( Exception $e ) {
                                // ignore
                            } catch ( Error $e ) {
                                // ignore
                            }
                        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
                            $show_upgrade_footer = ! in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
                        }

                        if ( $show_upgrade_footer ) {
                            _e( '더 많은 기능을 이용하려면 업그레이드하세요.', 'acf-css-really-simple-style-management-center' );
                        }
                        ?>
                    </div>
                    <div style="text-align: right;">
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-style-guide-footer-button" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        } catch ( Exception $e ) {
            // 오류 발생 시에도 기본 페이지 구조는 표시
            ?>
            <div class="wrap" style="padding: 20px;">
                <h1><?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?></h1>
                <div class="notice notice-error">
                    <p><strong><?php _e( '오류가 발생했습니다:', 'acf-css-really-simple-style-management-center' ); ?></strong> <?php echo esc_html( $e->getMessage() ); ?></p>
                </div>
            </div>
            <?php
        }
    }

    // 나머지 메서드들은 dev 버전과 동일하지만, 기능 제한 로직 추가 필요
    // save_settings_ajax, apply_temp_palette_ajax, save_temp_palette_ajax 등
    
    public function save_settings_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        if ( ! isset( $_POST['settings'] ) || ! is_array( $_POST['settings'] ) ) {
            wp_send_json_error( array( 'message' => __( '잘못된 데이터입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // [Phase 5 B] 멀티사이트 네트워크 전용 모드에서는 사이트별 저장 차단
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $ms = JJ_Multisite_Controller::instance();
                if ( method_exists( $ms, 'is_enabled' ) && method_exists( $ms, 'allow_site_override' ) ) {
                    if ( $ms->is_enabled() && ! $ms->allow_site_override() ) {
                        wp_send_json_error( array( 'message' => __( '이 사이트는 네트워크에서 스타일을 관리 중입니다. 네트워크 관리자에서 변경해주세요.', 'acf-css-really-simple-style-management-center' ) ) );
                    }
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        $old_settings = array();
        if ( function_exists( 'get_option' ) ) {
            $old_settings = (array) get_option( $this->options_key, array() );
        }

        $raw_settings = wp_unslash( $_POST['settings'] );
        
        // Free 버전: 제한된 설정만 저장
        $raw_settings = $this->filter_settings_for_free( $raw_settings );
        
        $sanitized_settings = $this->sanitize_options_recursive( $raw_settings );
        update_option( $this->options_key, $sanitized_settings );

        // [Phase 5.2] Webhook/자동화용 이벤트
        if ( function_exists( 'do_action' ) ) {
            do_action( 'jj_style_guide_settings_updated', $sanitized_settings, $old_settings, 'style_center_ajax' );
        }

        $this->sync_to_spokes( $sanitized_settings );

        JJ_CSS_Cache::instance()->flush();
        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * Free 버전: 저장 가능한 설정 필터링
     */
    private function filter_settings_for_free( $settings ) {
        // Free 버전: 시스템 팔레트, 얼터너티브 팔레트, 어나더 팔레트 제거
        if ( isset( $settings['palettes'] ) ) {
            unset( $settings['palettes']['system'] );
            unset( $settings['palettes']['alternative'] );
            unset( $settings['palettes']['another'] );
        }
        
        // Free 버전: 타이포그래피 H5, H6 제거
        if ( isset( $settings['typography'] ) ) {
            unset( $settings['typography']['h5'] );
            unset( $settings['typography']['h6'] );
        }
        
        // Free 버전: Secondary, Text 버튼 제거
        if ( isset( $settings['buttons'] ) ) {
            unset( $settings['buttons']['secondary'] );
            unset( $settings['buttons']['text'] );
        }
        
        return $settings;
    }

    public function apply_temp_palette_ajax() {
        // Free 버전: 임시 팔레트 사용 불가
        $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
        if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
            $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
        }
        wp_send_json_error( array( 
            'message' => $message
        ) );
    }

    public function save_temp_palette_ajax() {
        // Free 버전: 임시 팔레트 사용 불가
        $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
        if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
            $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
        }
        wp_send_json_error( array( 
            'message' => $message
        ) );
    }

    // 나머지 메서드들은 dev 버전과 동일 (sync_to_spokes, sync_from_customizer, sanitize_options_recursive 등)
    // 생략하거나 dev 버전 파일을 복사하여 수정

    private function sync_to_spokes( $hub_options ) {
        // Free 버전: WooCommerce만 동기화
        $brand_colors = $hub_options['palettes']['brand'] ?? array();
        
        // WooCommerce 동기화
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! is_array( $kwe ) ) {
            $kwe = array();
        }
        if ( isset( $brand_colors['primary_color'] ) ) {
            $kwe['product_quickview_button_color'] = $brand_colors['primary_color'];
        }
        if ( isset( $brand_colors['primary_color_hover'] ) ) {
            $kwe['product_quickview_button_color_hover'] = $brand_colors['primary_color_hover'];
        }
        update_option( 'kt_woo_extras', $kwe );
    }

    public function sync_from_customizer( $manager ) {
        $hub_options = (array) get_option( $this->options_key );
        $panel_settings = $manager->settings();
        
        foreach ( $panel_settings as $setting_id => $setting_obj ) {
            if ( strpos( $setting_id, $this->options_key . '[' ) === 0 ) {
                $keys = str_replace( array( $this->options_key, '[', ']' ), '', $setting_id );
                $keys = explode( '][', $keys );
                $this->set_nested_object_value( $hub_options, $keys, $setting_obj->post_value() );
            }
        }

        update_option( $this->options_key, $hub_options );
        JJ_CSS_Cache::instance()->flush();
    }

    private function set_nested_object_value( &$obj, $keys, $value ) {
        $current = &$obj;
        foreach ( $keys as $key ) {
            if ( ! isset( $current[$key] ) || ! is_array( $current[$key] ) ) {
                $current[$key] = array();
            }
            $current = &$current[$key];
        }
        $current = $value;
    }

    private function sanitize_options_recursive( $input ) {
        $output = array();
        foreach ( (array) $input as $key => $value ) {
            $raw_key = (string) $key;
            // [v5.3.8] WordPress 함수 안전 호출
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( $raw_key ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( $raw_key ) );
            if ( is_array( $value ) ) {
                $output[ $key ] = $this->sanitize_options_recursive( $value );
                continue;
            }
            // [v5.3.8] PHP 7.4 호환성: str_contains() 대신 strpos() 사용
            if ( strpos( $key, 'color' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $sanitized = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( $value ) : '';
                $output[ $key ] = $sanitized ? $sanitized : '';
                continue;
            }
            if ( strpos( $key, 'custom_css' ) !== false || strpos( $key, 'additional_css' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'wp_kses_post' ) ? wp_kses_post( $value ) : wp_strip_all_tags( $value );
                continue;
            }
            if ( strpos( $key, 'font_family' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
                continue;
            }
            // [v5.3.8] WordPress 함수 안전 호출
            $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
        }
        return $output;
    }

    public function load_options_and_init_strategies() {
        // [v5.3.5] 전체 메서드를 try-catch로 보호하여 치명적 오류 방지
        try {
            $this->environment_map = $this->scan_environment();
            $this->load_dynamic_adapters( $this->environment_map );
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options = $options;
            $this->sync_existing_settings();
            $this->brand_colors = $this->options['palettes']['brand'] ?? array();

            // [v5.3.5] 모든 전략 클래스 호출에 class_exists() 확인 추가
            // 전략 1: CSS 변수
            if ( class_exists( 'JJ_Strategy_1_CSS_Vars' ) ) {
                JJ_Strategy_1_CSS_Vars::instance()->init( $this->options );
            }
            
            // 전략 2: PHP 필터
            if ( class_exists( 'JJ_Strategy_2_PHP_Filters' ) ) {
                JJ_Strategy_2_PHP_Filters::instance()->init( $this->options, null, null, null, null );
            }
            
            // 전략 3: Dequeue (CSS 재정의)
            if ( class_exists( 'JJ_Strategy_3_Dequeue' ) ) {
                JJ_Strategy_3_Dequeue::instance()->init( $this->options );
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    private function sync_existing_settings() {
        $active_theme = wp_get_theme()->get_template();
        $needs_update = false;

        if ( ! isset( $this->options['palettes'] ) ) {
            $this->options['palettes'] = array();
        }
        if ( ! isset( $this->options['palettes']['brand'] ) ) {
            $this->options['palettes']['brand'] = array();
        }
        if ( ! isset( $this->options['typography'] ) ) {
            $this->options['typography'] = array();
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five 테마만 동기화
        if ( in_array( $active_theme, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
            // 기본 테마는 별도 설정이 없으므로 Customizer에서 가져오기
        }

        // WooCommerce 동기화 (Free 버전 지원)
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! empty( $kwe['product_quickview_button_color'] ) && empty( $this->options['palettes']['brand']['primary_color'] ) ) {
            $this->options['palettes']['brand']['primary_color'] = $kwe['product_quickview_button_color'];
            $needs_update = true;
        }
        if ( ! empty( $kwe['product_quickview_button_color_hover'] ) && empty( $this->options['palettes']['brand']['primary_color_hover'] ) ) {
            $this->options['palettes']['brand']['primary_color_hover'] = $kwe['product_quickview_button_color_hover'];
            $needs_update = true;
        }

        if ( $needs_update ) {
            update_option( $this->options_key, $this->options );
        }
    }

    private function load_dynamic_adapters( $environment_map ) {
        $active_adapters = $environment_map;
        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;
        $error_handler = JJ_Error_Handler::instance();

        // Free 버전: 테마 어댑터 제한 (Twenty Twenty-Four, Twenty Twenty-Five만)
        if ( ! empty( $active_adapters['themes'] ) ) {
            $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
            $active_adapters['themes'] = array_intersect( $active_adapters['themes'], $allowed_themes );
            
            foreach ( $active_adapters['themes'] as $theme_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-theme-' . sanitize_file_name( $theme_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Theme_' . str_replace( '-', '_', ucwords( $theme_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }

        // Free 버전: 플러그인 어댑터 제한 (WooCommerce만)
        if ( ! empty( $active_adapters['spokes'] ) ) {
            $allowed_plugins = array( 'woocommerce' );
            $active_adapters['spokes'] = array_intersect( $active_adapters['spokes'], $allowed_plugins );
            
            foreach ( $active_adapters['spokes'] as $spoke_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-spoke-' . sanitize_file_name( $spoke_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Spoke_' . str_replace( '-', '_', ucwords( $spoke_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }
    }

    private function scan_environment() {
        $config = $this->adapters_config;
        $spokes_to_check = $config['spokes'] ?? array();
        $themes_to_check = $config['themes'] ?? array();

        $active_plugins = (array) get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        $active_spoke_adapters = array();
        $active_theme_adapters = array();
        $current_theme_path = get_template_directory();
        $theme_dir_name = basename( $current_theme_path );

        // Free 버전: WooCommerce만 체크
        foreach ( $spokes_to_check as $adapter_slug => $plugin_path ) {
            if ( in_array( $plugin_path, $active_plugins ) && $adapter_slug === 'woocommerce' ) {
                $active_spoke_adapters[] = $adapter_slug;
            }
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five만 체크
        foreach ( $themes_to_check as $adapter_slug => $theme_path_suffix ) {
            if ( $theme_dir_name === $adapter_slug && in_array( $adapter_slug, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
                if ( file_exists( $current_theme_path . '/' . $theme_path_suffix ) ) {
                    $active_theme_adapters[] = $adapter_slug;
                }
            }
        }

        return array(
            'themes' => $active_theme_adapters,
            'spokes' => $active_spoke_adapters,
        );
    }

    public function render_debug_info() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $errors = JJ_Error_Handler::instance()->get_errors();

        echo '<div style="background: #001a33; color: #00ff8c; padding: 20px; font-family: monospace; font-size: 14px; line-height: 1.6; z-index: 99999; position: relative; margin: 20px; border-radius: 5px; white-space: pre-wrap; word-break: break-all; opacity: 0.9;">';
        echo '<strong>[ACF CSS DEBUG - FREE VERSION]</strong><br>';
        echo '====================<br>';
        echo '<strong>Edition:</strong> Free<br>';
        echo '<strong>Loaded Adapters:</strong><br>';
        if ( ! empty( $this->environment_map['themes'] ) ) {
            echo ' - Themes: ' . esc_html( implode( ', ', $this->environment_map['themes'] ) ) . '<br>';
        } else {
            echo ' - Themes: None<br>';
        }
        if ( ! empty( $this->environment_map['spokes'] ) ) {
            echo ' - Spokes: ' . esc_html( implode( ', ', $this->environment_map['spokes'] ) ) . '<br>';
        } else {
            echo ' - Spokes: None<br>';
        }
        echo '</div>';
    }
}

function jj_simple_style_guide_master_run() {
    // [Phase 5] 다국어 지원 로드
    load_plugin_textdomain( 'acf-css-really-simple-style-management-center', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // WordPress 함수가 로드되었는지 확인
    if ( ! function_exists( 'class_exists' ) || ! function_exists( 'file_exists' ) ) {
        return;
    }
    
    // [v5.3.9] 코드 무결성 모니터 초기화 (plugins_loaded 시점)
    
    // [Phase 4.5/4.99] Admin Center 초기화 (메뉴/상단바/관리자 UI)
    if ( class_exists( 'JJ_Admin_Center' ) ) {
        try {
            $admin_center = JJ_Admin_Center::instance();
            if ( method_exists( $admin_center, 'init' ) ) {
                $admin_center->init();
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v3.7.0] 백업 관리자 초기화
    if ( class_exists( 'JJ_Backup_Manager' ) ) {
        try {
            JJ_Backup_Manager::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] REST API 초기화
    if ( class_exists( 'JJ_Style_Guide_REST_API' ) ) {
        try {
            JJ_Style_Guide_REST_API::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.3] Extension Manager 초기화
    if ( class_exists( 'JJ_Extension_Manager' ) ) {
        try {
            JJ_Extension_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] Webhook Manager 초기화
    if ( class_exists( 'JJ_Webhook_Manager' ) ) {
        try {
            JJ_Webhook_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5 B] Multisite Controller 초기화
    if ( class_exists( 'JJ_Multisite_Controller' ) ) {
        try {
            JJ_Multisite_Controller::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v5.6.0] Visual Command Center 초기화
    if ( class_exists( 'JJ_Visual_Command_Center' ) ) {
        JJ_Visual_Command_Center::instance();
    }

    // [v5.7.0] AI Style Intelligence 초기화
    if ( class_exists( 'JJ_Smart_Palette' ) ) {
        JJ_Smart_Palette::instance();
    }

    // [v5.9.0] CSS Optimizer 초기화
    if ( class_exists( 'JJ_CSS_Optimizer' ) ) {
        JJ_CSS_Optimizer::instance();
    }

    // [v5.8.0] Cloud Manager 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Cloud_Manager' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) { // Premium 이상 체크 대용
            JJ_Cloud_Manager::instance();
        }
    }
    
    // [Phase 3] 템플릿 마켓 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Template_Market' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) {
            JJ_Template_Market::instance();
        }
    }
    
    // [Phase 5] Partner Hub 초기화 (Partner/Master만)
    if ( class_exists( 'JJ_Partner_Hub' ) ) {
        JJ_Partner_Hub::instance();
    }

    // [v4.2.2] 실험실(Labs) 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Labs_Center' ) ) {
        try {
            JJ_Labs_Center::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }


    if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
        try {
            JJ_Code_Integrity_Monitor::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Code Integrity Monitor: 초기화 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.9] 라이센스 강제 실행 초기화 (plugins_loaded 시점)
    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $enforcement_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-enforcement.php';
        if ( file_exists( $enforcement_file ) ) {
            require_once $enforcement_file;
            if ( class_exists( 'JJ_License_Enforcement' ) ) {
                try {
                    JJ_License_Enforcement::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ License Enforcement: 초기화 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // [v5.3.7] 안전 모드 플래그 확인 및 활성화
    if ( function_exists( 'get_option' ) && class_exists( 'JJ_Safe_Mode' ) ) {
        $safe_mode_enabled = get_option( 'jj_style_guide_safe_mode', false );
        if ( $safe_mode_enabled ) {
            $safe_mode_reason = get_option( 'jj_style_guide_safe_mode_reason', '활성화 중 오류 발생' );
            try {
                $safe_mode = JJ_Safe_Mode::instance();
                if ( method_exists( $safe_mode, 'enable' ) ) {
                    $safe_mode->enable( $safe_mode_reason );
                }
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 오류 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
    
    // 메인 클래스 인스턴스 생성
    if ( class_exists( 'JJ_Simple_Style_Guide_Master' ) ) {
        try {
            JJ_Simple_Style_Guide_Master::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.6] 기존 플러그인 마이그레이션 매니저 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $migration_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php';
        if ( file_exists( $migration_file ) ) {
            // 마이그레이션 매니저는 이미 safe_loader를 통해 로드되었을 수 있음
            // 클래스가 존재하는 경우에만 초기화
            if ( class_exists( 'JJ_Migration_Manager' ) ) {
                try {
                    JJ_Migration_Manager::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 오류 - ' . $e->getMessage() );
                    }
                } catch ( Error $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // 플러그인 업데이트 체크 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $updater_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php';
        if ( file_exists( $updater_file ) ) {
            require_once $updater_file;
            if ( class_exists( 'JJ_Plugin_Updater' ) ) {
                try {
                    new JJ_Plugin_Updater(
                        __FILE__,
                        'advanced-custom-style-manage-center',
                        "ACF CSS Master",
                        defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.4.0',
                        'DEV' // 마스터 버전
                    );
                } catch ( Exception $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 오류 - ' . $e->getMessage() );
                } catch ( Error $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
}

// [v5.3.8] plugins_loaded 훅 활성화
if ( function_exists( 'add_action' ) ) {
    add_action( 'plugins_loaded', 'jj_simple_style_guide_master_run', 10 );
}

/**
 * [v8.0.0] 안전 장치: 필수 헬퍼 함수 Fallback 정의
 * class-jj-admin-center.php 로드 실패 시에도 사이트가 멈추지 않도록 보장합니다.
 */
if ( ! function_exists( 'jj_style_guide_text' ) ) {
    function jj_style_guide_text( $key, $fallback = '' ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_text( $key, $fallback );
        }
        return $fallback;
    }
}

if ( ! function_exists( 'jj_style_guide_sections_layout' ) ) {
    function jj_style_guide_sections_layout() {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_sections_layout();
        }
        return array();
    }
}

if ( ! function_exists( 'jj_style_guide_section_index' ) ) {
    function jj_style_guide_section_index( $slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_section_index( $slug );
        }
        return null;
    }
}

if ( ! function_exists( 'jj_style_guide_is_tab_enabled' ) ) {
    function jj_style_guide_is_tab_enabled( $section_slug, $tab_slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->is_tab_enabled( $section_slug, $tab_slug );
        }
        return true;
    }
}

```

좋습니다. 지금까지 확인해보니 “UI가 텍스트처럼 보이고 자동 불러오기/버튼들이 제대로 안 도는” 핵심 원인은 **(1) 필수 JS/CSS 자산이 ZIP에 빠져 있었고, (2) 프론트(JS)는 호출하는데 백엔드(AJAX 훅/nonce)가 연결되지 않은 기능들이 있었기 때문**이었습니다. 방금 자산을 복원했고, 컬러 ‘새로고침’(Customizer 동기화) AJAX도 훅/nonce 불일치까지 같이 바로잡았습니다. 이제 다음으로 **섹션 Export/Import와 Labs 스캐너 백엔드**를 붙인 뒤, 그 위에 **추천 팔레트+인라인 프리뷰 UI**를 얹겠습니다.

```php
<?php
/**
 * Plugin Name:       ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager (Master)
 * Plugin URI:        https://j-j-labs.com
 * Description:       WordPress 웹사이트의 모든 스타일 요소(색상 팔레트, 타이포그래피, 버튼, 폼)를 중앙에서 일관되게 관리하는 통합 스타일 관리 플러그인입니다. Free 버전은 기본적인 스타일 관리 기능을 제공하며, 브랜드 일관성을 유지하고 디자인 시스템을 효율적으로 운영할 수 있습니다. Pro 버전 플러그인을 함께 설치하면 Basic, Premium, Unlimited 기능을 사용할 수 있습니다. WordPress Customizer와 완벽 통합되어 실시간 미리보기와 함께 직관적인 스타일 관리가 가능합니다.
 * Version:           8.0.0
 * Author:            3J Labs
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://poetryflow.blog
 * Text Domain:       acf-css-really-simple-style-management-center
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ⚠️ 중요: Git 경로와 로컬 경로는 완전히 별개입니다
 * 
 * - 로컬 작업 경로: C:\Users\computer\OneDrive\Desktop\클라우드 백업\OneDrive\문서\jj-css-premium
 * - Git worktree 경로: C:\Users\computer\.cursor\worktrees\jj-css-premium\tci
 * - 두 경로는 서로 독립적이며, Git에서 작업한 내용은 반드시 로컬 경로에 동기화해야 함
 * - 자세한 내용은 DEVELOPMENT_PRINCIPLES.md의 "Git 및 로컬 경로 관리 원칙" 섹션 참조
 */

// 플러그인 상수 정의
// [v5.1.6] 전면 재검토 및 오류 방지: 안전한 파일 로더 추가, 모든 버전의 require_once 안전 처리, 결제 유도 문구 추가, 플러그인 목록 페이지 바로가기 링크 추가
// [v5.1.6] Comprehensive review and error prevention: Safe file loader added, all versions' require_once safely handled, purchase prompts added, plugin list page quick links added
// [v1.0.2] 모든 버전 플러그인 활성화 안전성 최종 확보, WordPress 함수 호출 안전 처리
define( 'JJ_STYLE_GUIDE_VERSION', '8.0.0' ); // [v7.0.0] Operation Deep Dive - Stability & Reliability Overhaul

// WordPress 함수가 로드되었는지 확인 후 상수 정의
if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    if ( function_exists( 'plugin_dir_path' ) ) {
        define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 직접 경로 계산
        define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
    }
}

if ( ! defined( 'JJ_STYLE_GUIDE_URL' ) ) {
    if ( function_exists( 'plugin_dir_url' ) ) {
        define( 'JJ_STYLE_GUIDE_URL', plugin_dir_url( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 기본값 사용 (나중에 업데이트됨)
        define( 'JJ_STYLE_GUIDE_URL', '' );
    }
}
define( 'JJ_STYLE_GUIDE_OPTIONS_KEY', 'jj_style_guide_options' );
define( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY', 'jj_style_guide_temp_options' );
define( 'JJ_STYLE_GUIDE_ADMIN_TEXT_KEY', 'jj_style_guide_admin_texts' );
define( 'JJ_STYLE_GUIDE_PAGE_SLUG', 'acf-css-really-simple-style-guide' );
define( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY', 'jj_style_guide_cockpit_page_id' );
define( 'JJ_STYLE_GUIDE_DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
// Pro 버전 활성화 코드 확인 (plugins_loaded 훅에서 처리)
// WordPress가 로드되기 전에는 기본값 사용
define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', 'MASTER' ); // 기본값, plugins_loaded에서 업데이트됨
define( 'JJ_STYLE_GUIDE_EDITION', 'master' ); // 기본 에디션은 free

// [v3.8.0] 환경 구분
if ( ! defined( 'JJ_STYLE_GUIDE_ENV' ) ) {
    define( 'JJ_STYLE_GUIDE_ENV', 'live' );
}

// [v5.3.7] Free 버전은 독립적으로 작동합니다. Master 버전 의존성 제거
// Free 버전은 자체 includes 폴더에 필요한 모든 파일을 포함합니다.

// [v5.4.1] 안전한 파일 로더 적용
$safe_loader = null;
try {
    $safe_loader_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-loader.php';
    if ( file_exists( $safe_loader_path ) ) {
        require_once $safe_loader_path;
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'instance' ) ) {
            $safe_loader = JJ_Safe_Loader::instance();
        }
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Failed: ' . $e->getMessage() );
} catch ( Error $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Fatal Error: ' . $e->getMessage() );
}

// 라이센스 매니저 로드
if ( $safe_loader ) {
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php', false );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-version-features.php', true );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-code-integrity-monitor.php', true );
}

// 핵심 로직 파일 로드 (Master 전용 파일은 아직 비활성화)
try {
    if ( $safe_loader && method_exists( $safe_loader, 'safe_require' ) ) {
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-handler.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-logger.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-admin-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-backup-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/visual-command-center/class-jj-visual-command-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/ai-style-intelligence/class-jj-smart-palette.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-cloud-manager.php', true );
        // [Phase 3] 템플릿 마켓
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-template-market.php', true );
        // [Phase 5] Partner Hub
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/partner-hub/class-jj-partner-hub.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-labs-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-selector-registry.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-injector.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-options-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-edition-controller.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-theme-metadata.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-0-customizer.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-1-css-vars.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-2-php-filters.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-3-dequeue.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-sync.php', false );
        // [Phase 5.2] REST API (설정 조회/업데이트)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/api/class-jj-style-guide-rest-api.php', true );
        // [Phase 5.3] 확장 플러그인 시스템 (Extension API)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/interface-jj-style-guide-extension.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/class-jj-extension-manager.php', true );
        // [Phase 5.2] Webhooks (자동화)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/integrations/class-jj-webhook-manager.php', true );
        // [Phase 5 B] Multisite Network Control
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/multisite/class-jj-multisite-controller.php', true );
        
        // Master 전용 파일들 (일단 주석 처리)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-sync-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-memory-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-mode.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-optimizer.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-issuer.php', false );
    } else {
        // Fallback for Safe Loader missing
        require_once JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
        // ... (Fallback은 일단 생략, Safe Loader가 있다고 가정)
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Master Load Error: ' . $e->getMessage() );
}



/**
 * 활성화 훅 래퍼 함수 (치명적 오류 방지)
 * [v5.1.7] 전체 함수를 try-catch로 감싸고 상수 정의 확인 추가
 */
function jj_simple_style_guide_master_activate() {
    try {
        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // 상수가 정의되지 않은 경우 기본 경로 사용
            if ( function_exists( 'plugin_dir_path' ) ) {
                define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
            } else {
                define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
            }
        }
        
        // 활성화 매니저 클래스가 로드되지 않은 경우 로드 시도
        
        if ( ! class_exists( 'JJ_Activation_Manager' ) ) {
            $activation_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php';
            if ( file_exists( $activation_file ) ) {
                require_once $activation_file;
            } else {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 파일을 찾을 수 없습니다. 기본 활성화만 수행합니다.' );
                }
                return;
            }
        }
        
        // 클래스가 존재하는지 확인 후 활성화 실행
        if ( class_exists( 'JJ_Activation_Manager' ) && method_exists( 'JJ_Activation_Manager', 'activate' ) ) {
            try {
                JJ_Activation_Manager::activate();
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 오류 발생 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 치명적 오류 발생 - ' . $e->getMessage() );
                }
            }
        } else {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 클래스를 사용할 수 없습니다.' );
            }
        }
    } catch ( Exception $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 예외 발생 - ' . $e->getMessage() );
        }
    } catch ( Error $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 치명적 오류 발생 - ' . $e->getMessage() );
        }
    }
}
register_activation_hook( __FILE__, 'jj_simple_style_guide_master_activate' );

/**
 * Free 버전 전용 메인 컨트롤 클래스
 */
final class JJ_Simple_Style_Guide_Master {

    private static $instance = null;
    private $options = array();
    private $cockpit_page_id = 0;
    private $options_key = '';
    private $brand_colors = array();
    private $environment_map = array();
    private $adapters_config = array();
    private $license_manager = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 상수 정의 확인 후 할당
        if ( defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) {
            $this->options_key = JJ_STYLE_GUIDE_OPTIONS_KEY;
        }
        
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $this->license_manager = JJ_License_Manager::instance();
        }
        
        if ( defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) && function_exists( 'get_option' ) ) {
            $this->cockpit_page_id = (int) get_option( JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY );
        }

        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // [v5.3.7] Free 버전은 자체 config 폴더에서 로드
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
            if ( file_exists( $config_file ) ) {
                $this->adapters_config = include( $config_file );
                // [v5.5.0] Edition Controller를 통한 동적 어댑터 필터링
                if ( class_exists( 'JJ_Edition_Controller' ) ) {
                    $this->adapters_config = JJ_Edition_Controller::instance()->filter_adapters( $this->adapters_config );
                }
                
                
            }
        }

        // WordPress 함수가 로드된 후에만 훅 등록
        // [v5.1.9] plugin_basename() 안전 호출 추가
        // [v5.3.9] 모든 훅 등록을 조건문 안으로 이동하여 안전성 확보
        if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) && function_exists( 'plugin_basename' ) ) {
            add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
            add_action( 'admin_menu', array( $this, 'add_admin_menu_links' ) );
            $plugin_basename = function_exists( 'plugin_basename' ) ? plugin_basename( __FILE__ ) : basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );
            add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_plugin_settings_link' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            add_action( 'wp_ajax_jj_save_style_guide', array( $this, 'save_settings_ajax' ) );
            add_action( 'wp_ajax_jj_apply_temp_palette', array( $this, 'apply_temp_palette_ajax' ) );
            add_action( 'wp_ajax_jj_save_temp_palette', array( $this, 'save_temp_palette_ajax' ) );
            // [v3.8.0] Customizer 색상 새로고침 (팔레트 동기화)
            if ( class_exists( 'JJ_Customizer_Sync' ) ) {
                add_action( 'wp_ajax_jj_load_current_colors', array( 'JJ_Customizer_Sync', 'ajax_load_current_colors' ) );
            }
            // [v3.8.0] 섹션별 내보내기/불러오기 (팔레트 등)
            add_action( 'wp_ajax_jj_export_section', array( $this, 'ajax_export_section' ) );
            add_action( 'wp_ajax_jj_import_section', array( $this, 'ajax_import_section' ) );
            add_action( 'init', array( $this, 'load_options_and_init_strategies' ), 5 );
            add_action( 'init', array( $this, 'init_sync_manager' ), 10 );
            
            
            if ( JJ_STYLE_GUIDE_DEBUG ) {
                add_action( 'wp_footer', array( $this, 'render_debug_info' ), 999 );
                add_action( 'admin_footer', array( $this, 'render_debug_info' ), 999 );
            }
        }

        // Customizer 통합 (제한적)
        
        try {
            $customizer_manager_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php';
            if ( file_exists( $customizer_manager_path ) ) {
                require_once $customizer_manager_path;
                if ( class_exists( 'JJ_Customizer_Manager' ) ) {
                    $customizer_manager = JJ_Customizer_Manager::instance();
                    if ( method_exists( $customizer_manager, 'init' ) ) {
                        $customizer_manager->init();
                    }
                }
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }

    }

    /**
     * Free 버전: 어댑터 제한
     * 
     * @param array $config
     * @return array
     */
    private function limit_adapters_for_free( $config ) {
        // Free 버전: 테마 2개만 지원 (Twenty Twenty-Four, Twenty Twenty-Five)
        $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
        if ( isset( $config['themes'] ) && is_array( $config['themes'] ) ) {
            $config['themes'] = array_intersect_key( $config['themes'], array_flip( $allowed_themes ) );
        }

        // Free 버전: 플러그인 1개만 지원 (WooCommerce)
        $allowed_plugins = array( 'woocommerce' );
        if ( isset( $config['spokes'] ) && is_array( $config['spokes'] ) ) {
            $config['spokes'] = array_intersect_key( $config['spokes'], array_flip( $allowed_plugins ) );
        }

        return $config;
    }

    /**
     * Free 버전 워터마크 추가
     */
    public function add_free_watermark() {
        if ( ! is_admin() && current_user_can( 'manage_options' ) ) {
            echo '<div style="position: fixed; bottom: 10px; right: 10px; background: rgba(0, 0, 0, 0.7); color: #fff; padding: 8px 12px; font-size: 11px; border-radius: 4px; z-index: 99999; font-family: Arial, sans-serif;">';
            echo '<a href="' . esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ) . '" style="color: #fff; text-decoration: none;">JJ Style Guide Free</a>';
            echo '</div>';
        }
    }

    /**
     * 업그레이드 프롬프트 표시
     */
    public function show_upgrade_prompt() {
        // [Safety Lock] MASTER 또는 PARTNER 버전은 업그레이드 프롬프트 표시 안 함
        if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'partner' ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        $license_manager = null;
        $upgrade_url = 'https://j-j-labs.com'; // 기본값
        
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
                if ( $license_manager ) {
                    $upgrade_url = $license_manager->get_purchase_url( 'upgrade' );
                }
            }
        }
        ?>
        <div class="notice notice-info is-dismissible" style="background: #f0f6fc; border-left-color: #2271b1;">
            <p style="font-size: 14px; line-height: 1.8;">
                <strong><?php _e( '현재 버전을 사용 중입니다.', 'acf-css-really-simple-style-management-center' ); ?></strong><br>
                <?php _e( '더 많은 기능을 이용하려면 업그레이드하세요:', 'acf-css-really-simple-style-management-center' ); ?>
                <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" class="button button-primary" style="margin-left: 10px;">
                    <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
            </p>
            <ul style="margin: 10px 0; padding-left: 20px; list-style: disc;">
                <li><?php _e( '모든 팔레트 사용 가능 (브랜드, 시스템, 얼터너티브, 어나더, 임시)', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '모든 테마 및 플러그인 어댑터 지원', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '관리자 센터 및 실험실 센터 전체 기능', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '섹션별 내보내기/불러오기', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '고급 색상 선택 기능 (스포이드, 팔레트 불러오기)', 'acf-css-really-simple-style-management-center' ); ?></li>
            </ul>
        </div>
        <?php
    }

    public function init_sync_manager() {
        add_action( 'customize_save_after', array( $this, 'sync_from_customizer' ) );
        JJ_Sync_Manager::instance()->init( $this->options, $this->cockpit_page_id );
    }

    /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * Free 버전: 스타일 센터만 표시
     * [v5.1.9] 안전성 강화 - try-catch 추가
     */
            /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * [Phase 4.99] 퀵 링크 확장: 설정 | 스타일 | 메뉴 | 비주얼
     */
    public function add_plugin_settings_link( $links ) {
        try {
            if ( ! function_exists( 'admin_url' ) || ! function_exists( 'esc_url' ) ) {
                return $links;
            }
            
            $new_links = array();
            
            // 1. 설정 (Admin Center 메인)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ) . '" style="font-weight: 600;">' . __( 'Settings', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 2. 스타일 (Style Guide)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ) ) . '">' . __( 'Styles', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 3. 어드민 메뉴 (Admin Menu Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#admin-menu' ) ) . '">' . __( 'Menu', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 4. 비주얼 (Visual Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#visual' ) ) . '">' . __( 'Visual', 'acf-css-really-simple-style-management-center' ) . '</a>';

            // 기존 링크와 병합
            return array_merge( $new_links, $links );
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 오류 - ' . $e->getMessage() );
            }
            return $links;
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 치명적 오류 - ' . $e->getMessage() );
            }
            return $links;
        }
    }

    public function add_admin_menu_page() {
        add_options_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );
    }

    public function add_admin_menu_links() {
        // [Phase 4.5/4.99] "모양/도구/설정" 어디서나 동일한 화면으로 진입할 수 있도록 서브메뉴 추가
        add_theme_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        add_management_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        if ( $this->cockpit_page_id ) {
            add_submenu_page(
                'themes.php',
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                'manage_options',
                'post.php?post=' . $this->cockpit_page_id . '&action=edit',
                '',
                100
            );
        }
    }

    public function enqueue_admin_scripts( $hook_suffix ) {
        $is_settings_page = in_array(
            $hook_suffix,
            array(
                'settings_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'appearance_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'tools_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
            ),
            true
        );

        $is_cockpit_page = false;
        if ( 'post.php' === $hook_suffix ) {
            global $post;
            if ( $post && (int) $post->ID === $this->cockpit_page_id ) {
                $is_cockpit_page = true;
            }
        }

        if ( ! $is_settings_page && ! $is_cockpit_page ) {
            return;
        }

        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;

        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        // [v5.3.7] Free 버전은 자체 assets 폴더에서 로드
        wp_enqueue_style( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-editor.css', array( 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION );
        wp_enqueue_script( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-editor.js', array( 'jquery', 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION, true );

        $preview_page_id = $this->cockpit_page_id;
        $preview_url = $preview_page_id ? get_permalink( $preview_page_id ) : '';

        wp_localize_script(
            'jj-style-guide-editor',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'temp_palette_nonce' => wp_create_nonce( 'jj_temp_palette_nonce' ),
                'settings' => $this->options,
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'edition'  => 'free', // Free 버전 표시
                'i18n'     => array(
                    'saving' => __( '저장 중...', 'acf-css-really-simple-style-management-center' ),
                    'saved'  => __( '스타일 저장됨', 'acf-css-really-simple-style-management-center' ),
                    'applied' => __( '임시 팔레트 적용됨! \'스타일 저장\'을 눌러야 최종 반영됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'error'  => __( '오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
                    'confirm_apply' => __( '정말로 \'임시 팔레트\'의 값으로 덮어쓰시겠습니까?\n이 작업은 즉시 UI에 반영되지만, \n\'스타일 저장\' 버튼을 눌러야 최종 저장됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'preview_url' => $preview_url ? esc_url( $preview_url ) : '',
                    'preview_page_id' => $preview_page_id,
                ),
            )
        );
    }

    public function render_style_guide_page() {
        try {
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options      = $options;
            $temp_options       = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY );
            $options            = $this->options;
            $cockpit_page_id    = $this->cockpit_page_id;
            $preview_url        = $cockpit_page_id ? get_permalink( $cockpit_page_id ) : '';

        // 옵션이 비어있으면 기본값 설정
        if ( empty( $this->options ) ) {
            $this->options = array(
                'palettes' => array(
                    'brand' => array(
                        'primary_color' => '',
                        'primary_color_hover' => '',
                        'secondary_color' => '',
                        'secondary_color_hover' => '',
                    ),
                    'system' => array(), // Free 버전: 시스템 팔레트 비활성화
                    'alternative' => array(), // Free 버전: 얼터너티브 팔레트 비활성화
                    'another' => array(), // Free 버전: 어나더 팔레트 비활성화
                ),
                'typography' => array(),
                'buttons' => array(),
                'forms' => array(),
                'contexts' => array(),
            );
            $options = $this->options;
        }
        ?>
        <div id="jj-style-guide-wrapper" class="wrap jj-style-guide-wrap" style="background: #f0f0f1; margin: 0; padding: 0;">
            <form id="jj-style-guide-form">

                <div class="jj-style-guide-header" style="top: 32px; background: #fff; padding: 15px 25px; border-bottom: 1px solid #c3c4c7; z-index: 999; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h1 style="margin: 0; font-size: 24px;">
                            <?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?> (v<?php echo esc_html( JJ_STYLE_GUIDE_VERSION ); ?>) 
                            <?php if ( ! class_exists("JJ_Edition_Controller") || JJ_Edition_Controller::instance()->get_current_edition() === "free" ) : ?><span style="background: #d63638; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 14px; font-weight: 600;">Free</span><?php endif; ?>
                        </h1>
                        <?php
                        $license_manager = null;
                        $purchase_url = 'https://j-j-labs.com'; // 기본값
                        
                        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                            if ( class_exists( 'JJ_License_Manager' ) ) {
                                $license_manager = JJ_License_Manager::instance();
                                if ( $license_manager ) {
                                    $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                                }
                            }
                        }
                        ?>
                        <?php if ( ! class_exists("JJ_Edition_Controller") || ! JJ_Edition_Controller::instance()->is_at_least("partner") ) : ?><a href="<?php echo esc_url( $purchase_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary" style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px;">
                            <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                        </a><?php endif; ?>
                        <?php if ( $preview_url ) : ?>
                        <button type="button" 
                                class="button button-secondary" 
                                id="jj-preview-button"
                                style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> <?php _e( '실시간 미리보기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="jj-header-actions" style="display: flex; align-items: center; gap: 10px;">
                        <span id="jj-save-success" style="display: none; vertical-align: middle; color: #2271b1; font-weight: 600; margin-right: 15px;"></span>
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-save-style-guide" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
                <div class="jj-style-guide-content" style="margin: 20px auto; background: #ffffff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 1px;">
                    <?php
                    // Free 버전: 제한된 섹션만 표시
                    // [v5.3.7] Free 버전은 자체 includes/editor-views 폴더에서 로드
                    $sections_map = array(
                        'colors'     => 'includes/editor-views/view-section-colors.php',
                        'typography' => 'includes/editor-views/view-section-typography.php',
                        'buttons'    => 'includes/editor-views/view-section-buttons.php',
                        'forms'      => 'includes/editor-views/view-section-forms.php',
                    );

                    // Free 버전: 섹션 순서 고정
                    $sections_to_render = array();
                    foreach ( $sections_map as $slug => $relative_path ) {
                        $sections_to_render[] = array(
                            'slug' => $slug,
                            'file' => JJ_STYLE_GUIDE_PATH . $relative_path,
                            'order'=> 0, // 순서는 UI에서만 표시
                        );
                    }
                    ?>
                    <div id="jj-sections-sortable" class="jj-sections-sortable">
                        <?php
                        foreach ( $sections_to_render as $section ) {
                            // Free 버전: 기본 뷰 파일 사용 (제한 로직은 뷰 파일 내부에서 처리)
                            $default_file = JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-section-' . $section['slug'] . '.php';
                            if ( file_exists( $section['file'] ) ) {
                                echo '<div class="jj-section-wrapper" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $section['file'];
                                echo '</div>';
                            } elseif ( file_exists( $default_file ) ) {
                                // Free 버전 전용 뷰가 없으면 기본 뷰 사용 (제한 로직 적용 필요)
                                echo '<div class="jj-section-wrapper jj-section-free-version" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $default_file;
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="jj-style-guide-footer" style="margin: 20px auto; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: #666; font-size: 13px;">
                        <?php
                        // Master/Partner에서는 업그레이드 유도 문구를 절대 노출하지 않음
                        $show_upgrade_footer = true;
                        if ( class_exists( 'JJ_Edition_Controller' ) ) {
                            try {
                                $show_upgrade_footer = ! JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                            } catch ( Exception $e ) {
                                // ignore
                            } catch ( Error $e ) {
                                // ignore
                            }
                        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
                            $show_upgrade_footer = ! in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
                        }

                        if ( $show_upgrade_footer ) {
                            _e( '더 많은 기능을 이용하려면 업그레이드하세요.', 'acf-css-really-simple-style-management-center' );
                        }
                        ?>
                    </div>
                    <div style="text-align: right;">
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-style-guide-footer-button" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        } catch ( Exception $e ) {
            // 오류 발생 시에도 기본 페이지 구조는 표시
            ?>
            <div class="wrap" style="padding: 20px;">
                <h1><?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?></h1>
                <div class="notice notice-error">
                    <p><strong><?php _e( '오류가 발생했습니다:', 'acf-css-really-simple-style-management-center' ); ?></strong> <?php echo esc_html( $e->getMessage() ); ?></p>
                </div>
            </div>
            <?php
        }
    }

    // 나머지 메서드들은 dev 버전과 동일하지만, 기능 제한 로직 추가 필요
    // save_settings_ajax, apply_temp_palette_ajax, save_temp_palette_ajax 등
    
    public function save_settings_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        if ( ! isset( $_POST['settings'] ) || ! is_array( $_POST['settings'] ) ) {
            wp_send_json_error( array( 'message' => __( '잘못된 데이터입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // [Phase 5 B] 멀티사이트 네트워크 전용 모드에서는 사이트별 저장 차단
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $ms = JJ_Multisite_Controller::instance();
                if ( method_exists( $ms, 'is_enabled' ) && method_exists( $ms, 'allow_site_override' ) ) {
                    if ( $ms->is_enabled() && ! $ms->allow_site_override() ) {
                        wp_send_json_error( array( 'message' => __( '이 사이트는 네트워크에서 스타일을 관리 중입니다. 네트워크 관리자에서 변경해주세요.', 'acf-css-really-simple-style-management-center' ) ) );
                    }
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        $old_settings = array();
        if ( function_exists( 'get_option' ) ) {
            $old_settings = (array) get_option( $this->options_key, array() );
        }

        $raw_settings = wp_unslash( $_POST['settings'] );
        
        // Free 버전: 제한된 설정만 저장
        $raw_settings = $this->filter_settings_for_free( $raw_settings );
        
        $sanitized_settings = $this->sanitize_options_recursive( $raw_settings );
        update_option( $this->options_key, $sanitized_settings );

        // [Phase 5.2] Webhook/자동화용 이벤트
        if ( function_exists( 'do_action' ) ) {
            do_action( 'jj_style_guide_settings_updated', $sanitized_settings, $old_settings, 'style_center_ajax' );
        }

        $this->sync_to_spokes( $sanitized_settings );

        JJ_CSS_Cache::instance()->flush();
        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * Free 버전: 저장 가능한 설정 필터링
     */
    private function filter_settings_for_free( $settings ) {
        // Free 버전: 시스템 팔레트, 얼터너티브 팔레트, 어나더 팔레트 제거
        if ( isset( $settings['palettes'] ) ) {
            unset( $settings['palettes']['system'] );
            unset( $settings['palettes']['alternative'] );
            unset( $settings['palettes']['another'] );
        }
        
        // Free 버전: 타이포그래피 H5, H6 제거
        if ( isset( $settings['typography'] ) ) {
            unset( $settings['typography']['h5'] );
            unset( $settings['typography']['h6'] );
        }
        
        // Free 버전: Secondary, Text 버튼 제거
        if ( isset( $settings['buttons'] ) ) {
            unset( $settings['buttons']['secondary'] );
            unset( $settings['buttons']['text'] );
        }
        
        return $settings;
    }

    public function apply_temp_palette_ajax() {
        // Free 버전: 임시 팔레트 사용 불가
        $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
        if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
            $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
        }
        wp_send_json_error( array( 
            'message' => $message
        ) );
    }

    public function save_temp_palette_ajax() {
        // Free 버전: 임시 팔레트 사용 불가
        $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
        if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
            $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
        }
        wp_send_json_error( array( 
            'message' => $message
        ) );
    }

    // 나머지 메서드들은 dev 버전과 동일 (sync_to_spokes, sync_from_customizer, sanitize_options_recursive 등)
    // 생략하거나 dev 버전 파일을 복사하여 수정

    private function sync_to_spokes( $hub_options ) {
        // Free 버전: WooCommerce만 동기화
        $brand_colors = $hub_options['palettes']['brand'] ?? array();
        
        // WooCommerce 동기화
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! is_array( $kwe ) ) {
            $kwe = array();
        }
        if ( isset( $brand_colors['primary_color'] ) ) {
            $kwe['product_quickview_button_color'] = $brand_colors['primary_color'];
        }
        if ( isset( $brand_colors['primary_color_hover'] ) ) {
            $kwe['product_quickview_button_color_hover'] = $brand_colors['primary_color_hover'];
        }
        update_option( 'kt_woo_extras', $kwe );
    }

    public function sync_from_customizer( $manager ) {
        $hub_options = (array) get_option( $this->options_key );
        $panel_settings = $manager->settings();
        
        foreach ( $panel_settings as $setting_id => $setting_obj ) {
            if ( strpos( $setting_id, $this->options_key . '[' ) === 0 ) {
                $keys = str_replace( array( $this->options_key, '[', ']' ), '', $setting_id );
                $keys = explode( '][', $keys );
                $this->set_nested_object_value( $hub_options, $keys, $setting_obj->post_value() );
            }
        }

        update_option( $this->options_key, $hub_options );
        JJ_CSS_Cache::instance()->flush();
    }

    private function set_nested_object_value( &$obj, $keys, $value ) {
        $current = &$obj;
        foreach ( $keys as $key ) {
            if ( ! isset( $current[$key] ) || ! is_array( $current[$key] ) ) {
                $current[$key] = array();
            }
            $current = &$current[$key];
        }
        $current = $value;
    }

    private function sanitize_options_recursive( $input ) {
        $output = array();
        foreach ( (array) $input as $key => $value ) {
            $raw_key = (string) $key;
            // [v5.3.8] WordPress 함수 안전 호출
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( $raw_key ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( $raw_key ) );
            if ( is_array( $value ) ) {
                $output[ $key ] = $this->sanitize_options_recursive( $value );
                continue;
            }
            // [v5.3.8] PHP 7.4 호환성: str_contains() 대신 strpos() 사용
            if ( strpos( $key, 'color' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $sanitized = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( $value ) : '';
                $output[ $key ] = $sanitized ? $sanitized : '';
                continue;
            }
            if ( strpos( $key, 'custom_css' ) !== false || strpos( $key, 'additional_css' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'wp_kses_post' ) ? wp_kses_post( $value ) : wp_strip_all_tags( $value );
                continue;
            }
            if ( strpos( $key, 'font_family' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
                continue;
            }
            // [v5.3.8] WordPress 함수 안전 호출
            $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
        }
        return $output;
    }

    public function load_options_and_init_strategies() {
        // [v5.3.5] 전체 메서드를 try-catch로 보호하여 치명적 오류 방지
        try {
            $this->environment_map = $this->scan_environment();
            $this->load_dynamic_adapters( $this->environment_map );
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options = $options;
            $this->sync_existing_settings();
            $this->brand_colors = $this->options['palettes']['brand'] ?? array();

            // [v5.3.5] 모든 전략 클래스 호출에 class_exists() 확인 추가
            // 전략 1: CSS 변수
            if ( class_exists( 'JJ_Strategy_1_CSS_Vars' ) ) {
                JJ_Strategy_1_CSS_Vars::instance()->init( $this->options );
            }
            
            // 전략 2: PHP 필터
            if ( class_exists( 'JJ_Strategy_2_PHP_Filters' ) ) {
                JJ_Strategy_2_PHP_Filters::instance()->init( $this->options, null, null, null, null );
            }
            
            // 전략 3: Dequeue (CSS 재정의)
            if ( class_exists( 'JJ_Strategy_3_Dequeue' ) ) {
                JJ_Strategy_3_Dequeue::instance()->init( $this->options );
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    private function sync_existing_settings() {
        $active_theme = wp_get_theme()->get_template();
        $needs_update = false;

        if ( ! isset( $this->options['palettes'] ) ) {
            $this->options['palettes'] = array();
        }
        if ( ! isset( $this->options['palettes']['brand'] ) ) {
            $this->options['palettes']['brand'] = array();
        }
        if ( ! isset( $this->options['typography'] ) ) {
            $this->options['typography'] = array();
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five 테마만 동기화
        if ( in_array( $active_theme, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
            // 기본 테마는 별도 설정이 없으므로 Customizer에서 가져오기
        }

        // WooCommerce 동기화 (Free 버전 지원)
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! empty( $kwe['product_quickview_button_color'] ) && empty( $this->options['palettes']['brand']['primary_color'] ) ) {
            $this->options['palettes']['brand']['primary_color'] = $kwe['product_quickview_button_color'];
            $needs_update = true;
        }
        if ( ! empty( $kwe['product_quickview_button_color_hover'] ) && empty( $this->options['palettes']['brand']['primary_color_hover'] ) ) {
            $this->options['palettes']['brand']['primary_color_hover'] = $kwe['product_quickview_button_color_hover'];
            $needs_update = true;
        }

        if ( $needs_update ) {
            update_option( $this->options_key, $this->options );
        }
    }

    private function load_dynamic_adapters( $environment_map ) {
        $active_adapters = $environment_map;
        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;
        $error_handler = JJ_Error_Handler::instance();

        // Free 버전: 테마 어댑터 제한 (Twenty Twenty-Four, Twenty Twenty-Five만)
        if ( ! empty( $active_adapters['themes'] ) ) {
            $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
            $active_adapters['themes'] = array_intersect( $active_adapters['themes'], $allowed_themes );
            
            foreach ( $active_adapters['themes'] as $theme_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-theme-' . sanitize_file_name( $theme_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Theme_' . str_replace( '-', '_', ucwords( $theme_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }

        // Free 버전: 플러그인 어댑터 제한 (WooCommerce만)
        if ( ! empty( $active_adapters['spokes'] ) ) {
            $allowed_plugins = array( 'woocommerce' );
            $active_adapters['spokes'] = array_intersect( $active_adapters['spokes'], $allowed_plugins );
            
            foreach ( $active_adapters['spokes'] as $spoke_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-spoke-' . sanitize_file_name( $spoke_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Spoke_' . str_replace( '-', '_', ucwords( $spoke_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }
    }

    private function scan_environment() {
        $config = $this->adapters_config;
        $spokes_to_check = $config['spokes'] ?? array();
        $themes_to_check = $config['themes'] ?? array();

        $active_plugins = (array) get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        $active_spoke_adapters = array();
        $active_theme_adapters = array();
        $current_theme_path = get_template_directory();
        $theme_dir_name = basename( $current_theme_path );

        // Free 버전: WooCommerce만 체크
        foreach ( $spokes_to_check as $adapter_slug => $plugin_path ) {
            if ( in_array( $plugin_path, $active_plugins ) && $adapter_slug === 'woocommerce' ) {
                $active_spoke_adapters[] = $adapter_slug;
            }
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five만 체크
        foreach ( $themes_to_check as $adapter_slug => $theme_path_suffix ) {
            if ( $theme_dir_name === $adapter_slug && in_array( $adapter_slug, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
                if ( file_exists( $current_theme_path . '/' . $theme_path_suffix ) ) {
                    $active_theme_adapters[] = $adapter_slug;
                }
            }
        }

        return array(
            'themes' => $active_theme_adapters,
            'spokes' => $active_spoke_adapters,
        );
    }

    public function render_debug_info() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $errors = JJ_Error_Handler::instance()->get_errors();

        echo '<div style="background: #001a33; color: #00ff8c; padding: 20px; font-family: monospace; font-size: 14px; line-height: 1.6; z-index: 99999; position: relative; margin: 20px; border-radius: 5px; white-space: pre-wrap; word-break: break-all; opacity: 0.9;">';
        echo '<strong>[ACF CSS DEBUG - FREE VERSION]</strong><br>';
        echo '====================<br>';
        echo '<strong>Edition:</strong> Free<br>';
        echo '<strong>Loaded Adapters:</strong><br>';
        if ( ! empty( $this->environment_map['themes'] ) ) {
            echo ' - Themes: ' . esc_html( implode( ', ', $this->environment_map['themes'] ) ) . '<br>';
        } else {
            echo ' - Themes: None<br>';
        }
        if ( ! empty( $this->environment_map['spokes'] ) ) {
            echo ' - Spokes: ' . esc_html( implode( ', ', $this->environment_map['spokes'] ) ) . '<br>';
        } else {
            echo ' - Spokes: None<br>';
        }
        echo '</div>';
    }
}

function jj_simple_style_guide_master_run() {
    // [Phase 5] 다국어 지원 로드
    load_plugin_textdomain( 'acf-css-really-simple-style-management-center', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // WordPress 함수가 로드되었는지 확인
    if ( ! function_exists( 'class_exists' ) || ! function_exists( 'file_exists' ) ) {
        return;
    }
    
    // [v5.3.9] 코드 무결성 모니터 초기화 (plugins_loaded 시점)
    
    // [Phase 4.5/4.99] Admin Center 초기화 (메뉴/상단바/관리자 UI)
    if ( class_exists( 'JJ_Admin_Center' ) ) {
        try {
            $admin_center = JJ_Admin_Center::instance();
            if ( method_exists( $admin_center, 'init' ) ) {
                $admin_center->init();
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v3.7.0] 백업 관리자 초기화
    if ( class_exists( 'JJ_Backup_Manager' ) ) {
        try {
            JJ_Backup_Manager::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] REST API 초기화
    if ( class_exists( 'JJ_Style_Guide_REST_API' ) ) {
        try {
            JJ_Style_Guide_REST_API::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.3] Extension Manager 초기화
    if ( class_exists( 'JJ_Extension_Manager' ) ) {
        try {
            JJ_Extension_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] Webhook Manager 초기화
    if ( class_exists( 'JJ_Webhook_Manager' ) ) {
        try {
            JJ_Webhook_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5 B] Multisite Controller 초기화
    if ( class_exists( 'JJ_Multisite_Controller' ) ) {
        try {
            JJ_Multisite_Controller::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v5.6.0] Visual Command Center 초기화
    if ( class_exists( 'JJ_Visual_Command_Center' ) ) {
        JJ_Visual_Command_Center::instance();
    }

    // [v5.7.0] AI Style Intelligence 초기화
    if ( class_exists( 'JJ_Smart_Palette' ) ) {
        JJ_Smart_Palette::instance();
    }

    // [v5.9.0] CSS Optimizer 초기화
    if ( class_exists( 'JJ_CSS_Optimizer' ) ) {
        JJ_CSS_Optimizer::instance();
    }

    // [v5.8.0] Cloud Manager 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Cloud_Manager' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) { // Premium 이상 체크 대용
            JJ_Cloud_Manager::instance();
        }
    }
    
    // [Phase 3] 템플릿 마켓 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Template_Market' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) {
            JJ_Template_Market::instance();
        }
    }
    
    // [Phase 5] Partner Hub 초기화 (Partner/Master만)
    if ( class_exists( 'JJ_Partner_Hub' ) ) {
        JJ_Partner_Hub::instance();
    }

    // [v4.2.2] 실험실(Labs) 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Labs_Center' ) ) {
        try {
            JJ_Labs_Center::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }


    if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
        try {
            JJ_Code_Integrity_Monitor::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Code Integrity Monitor: 초기화 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.9] 라이센스 강제 실행 초기화 (plugins_loaded 시점)
    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $enforcement_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-enforcement.php';
        if ( file_exists( $enforcement_file ) ) {
            require_once $enforcement_file;
            if ( class_exists( 'JJ_License_Enforcement' ) ) {
                try {
                    JJ_License_Enforcement::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ License Enforcement: 초기화 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // [v5.3.7] 안전 모드 플래그 확인 및 활성화
    if ( function_exists( 'get_option' ) && class_exists( 'JJ_Safe_Mode' ) ) {
        $safe_mode_enabled = get_option( 'jj_style_guide_safe_mode', false );
        if ( $safe_mode_enabled ) {
            $safe_mode_reason = get_option( 'jj_style_guide_safe_mode_reason', '활성화 중 오류 발생' );
            try {
                $safe_mode = JJ_Safe_Mode::instance();
                if ( method_exists( $safe_mode, 'enable' ) ) {
                    $safe_mode->enable( $safe_mode_reason );
                }
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 오류 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
    
    // 메인 클래스 인스턴스 생성
    if ( class_exists( 'JJ_Simple_Style_Guide_Master' ) ) {
        try {
            JJ_Simple_Style_Guide_Master::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.6] 기존 플러그인 마이그레이션 매니저 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $migration_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php';
        if ( file_exists( $migration_file ) ) {
            // 마이그레이션 매니저는 이미 safe_loader를 통해 로드되었을 수 있음
            // 클래스가 존재하는 경우에만 초기화
            if ( class_exists( 'JJ_Migration_Manager' ) ) {
                try {
                    JJ_Migration_Manager::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 오류 - ' . $e->getMessage() );
                    }
                } catch ( Error $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // 플러그인 업데이트 체크 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $updater_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php';
        if ( file_exists( $updater_file ) ) {
            require_once $updater_file;
            if ( class_exists( 'JJ_Plugin_Updater' ) ) {
                try {
                    new JJ_Plugin_Updater(
                        __FILE__,
                        'advanced-custom-style-manage-center',
                        "ACF CSS Master",
                        defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.4.0',
                        'DEV' // 마스터 버전
                    );
                } catch ( Exception $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 오류 - ' . $e->getMessage() );
                } catch ( Error $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
}

// [v5.3.8] plugins_loaded 훅 활성화
if ( function_exists( 'add_action' ) ) {
    add_action( 'plugins_loaded', 'jj_simple_style_guide_master_run', 10 );
}

/**
 * [v8.0.0] 안전 장치: 필수 헬퍼 함수 Fallback 정의
 * class-jj-admin-center.php 로드 실패 시에도 사이트가 멈추지 않도록 보장합니다.
 */
if ( ! function_exists( 'jj_style_guide_text' ) ) {
    function jj_style_guide_text( $key, $fallback = '' ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_text( $key, $fallback );
        }
        return $fallback;
    }
}

if ( ! function_exists( 'jj_style_guide_sections_layout' ) ) {
    function jj_style_guide_sections_layout() {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_sections_layout();
        }
        return array();
    }
}

if ( ! function_exists( 'jj_style_guide_section_index' ) ) {
    function jj_style_guide_section_index( $slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_section_index( $slug );
        }
        return null;
    }
}

if ( ! function_exists( 'jj_style_guide_is_tab_enabled' ) ) {
    function jj_style_guide_is_tab_enabled( $section_slug, $tab_slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->is_tab_enabled( $section_slug, $tab_slug );
        }
        return true;
    }
}

```

```php
<?php
/**
 * Plugin Name:       ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager (Master)
 * Plugin URI:        https://j-j-labs.com
 * Description:       WordPress 웹사이트의 모든 스타일 요소(색상 팔레트, 타이포그래피, 버튼, 폼)를 중앙에서 일관되게 관리하는 통합 스타일 관리 플러그인입니다. Free 버전은 기본적인 스타일 관리 기능을 제공하며, 브랜드 일관성을 유지하고 디자인 시스템을 효율적으로 운영할 수 있습니다. Pro 버전 플러그인을 함께 설치하면 Basic, Premium, Unlimited 기능을 사용할 수 있습니다. WordPress Customizer와 완벽 통합되어 실시간 미리보기와 함께 직관적인 스타일 관리가 가능합니다.
 * Version:           8.0.0
 * Author:            3J Labs
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://poetryflow.blog
 * Text Domain:       acf-css-really-simple-style-management-center
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ⚠️ 중요: Git 경로와 로컬 경로는 완전히 별개입니다
 * 
 * - 로컬 작업 경로: C:\Users\computer\OneDrive\Desktop\클라우드 백업\OneDrive\문서\jj-css-premium
 * - Git worktree 경로: C:\Users\computer\.cursor\worktrees\jj-css-premium\tci
 * - 두 경로는 서로 독립적이며, Git에서 작업한 내용은 반드시 로컬 경로에 동기화해야 함
 * - 자세한 내용은 DEVELOPMENT_PRINCIPLES.md의 "Git 및 로컬 경로 관리 원칙" 섹션 참조
 */

// 플러그인 상수 정의
// [v5.1.6] 전면 재검토 및 오류 방지: 안전한 파일 로더 추가, 모든 버전의 require_once 안전 처리, 결제 유도 문구 추가, 플러그인 목록 페이지 바로가기 링크 추가
// [v5.1.6] Comprehensive review and error prevention: Safe file loader added, all versions' require_once safely handled, purchase prompts added, plugin list page quick links added
// [v1.0.2] 모든 버전 플러그인 활성화 안전성 최종 확보, WordPress 함수 호출 안전 처리
define( 'JJ_STYLE_GUIDE_VERSION', '8.0.0' ); // [v7.0.0] Operation Deep Dive - Stability & Reliability Overhaul

// WordPress 함수가 로드되었는지 확인 후 상수 정의
if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    if ( function_exists( 'plugin_dir_path' ) ) {
        define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 직접 경로 계산
        define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
    }
}

if ( ! defined( 'JJ_STYLE_GUIDE_URL' ) ) {
    if ( function_exists( 'plugin_dir_url' ) ) {
        define( 'JJ_STYLE_GUIDE_URL', plugin_dir_url( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 기본값 사용 (나중에 업데이트됨)
        define( 'JJ_STYLE_GUIDE_URL', '' );
    }
}
define( 'JJ_STYLE_GUIDE_OPTIONS_KEY', 'jj_style_guide_options' );
define( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY', 'jj_style_guide_temp_options' );
define( 'JJ_STYLE_GUIDE_ADMIN_TEXT_KEY', 'jj_style_guide_admin_texts' );
define( 'JJ_STYLE_GUIDE_PAGE_SLUG', 'acf-css-really-simple-style-guide' );
define( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY', 'jj_style_guide_cockpit_page_id' );
define( 'JJ_STYLE_GUIDE_DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
// Pro 버전 활성화 코드 확인 (plugins_loaded 훅에서 처리)
// WordPress가 로드되기 전에는 기본값 사용
define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', 'MASTER' ); // 기본값, plugins_loaded에서 업데이트됨
define( 'JJ_STYLE_GUIDE_EDITION', 'master' ); // 기본 에디션은 free

// [v3.8.0] 환경 구분
if ( ! defined( 'JJ_STYLE_GUIDE_ENV' ) ) {
    define( 'JJ_STYLE_GUIDE_ENV', 'live' );
}

// [v5.3.7] Free 버전은 독립적으로 작동합니다. Master 버전 의존성 제거
// Free 버전은 자체 includes 폴더에 필요한 모든 파일을 포함합니다.

// [v5.4.1] 안전한 파일 로더 적용
$safe_loader = null;
try {
    $safe_loader_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-loader.php';
    if ( file_exists( $safe_loader_path ) ) {
        require_once $safe_loader_path;
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'instance' ) ) {
            $safe_loader = JJ_Safe_Loader::instance();
        }
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Failed: ' . $e->getMessage() );
} catch ( Error $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Fatal Error: ' . $e->getMessage() );
}

// 라이센스 매니저 로드
if ( $safe_loader ) {
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php', false );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-version-features.php', true );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-code-integrity-monitor.php', true );
}

// 핵심 로직 파일 로드 (Master 전용 파일은 아직 비활성화)
try {
    if ( $safe_loader && method_exists( $safe_loader, 'safe_require' ) ) {
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-handler.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-logger.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-admin-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-backup-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/visual-command-center/class-jj-visual-command-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/ai-style-intelligence/class-jj-smart-palette.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-cloud-manager.php', true );
        // [Phase 3] 템플릿 마켓
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-template-market.php', true );
        // [Phase 5] Partner Hub
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/partner-hub/class-jj-partner-hub.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-labs-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-selector-registry.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-injector.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-options-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-edition-controller.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-theme-metadata.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-0-customizer.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-1-css-vars.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-2-php-filters.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-3-dequeue.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-sync.php', false );
        // [Phase 5.2] REST API (설정 조회/업데이트)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/api/class-jj-style-guide-rest-api.php', true );
        // [Phase 5.3] 확장 플러그인 시스템 (Extension API)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/interface-jj-style-guide-extension.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/class-jj-extension-manager.php', true );
        // [Phase 5.2] Webhooks (자동화)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/integrations/class-jj-webhook-manager.php', true );
        // [Phase 5 B] Multisite Network Control
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/multisite/class-jj-multisite-controller.php', true );
        
        // Master 전용 파일들 (일단 주석 처리)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-sync-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-memory-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-mode.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-optimizer.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-issuer.php', false );
    } else {
        // Fallback for Safe Loader missing
        require_once JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
        // ... (Fallback은 일단 생략, Safe Loader가 있다고 가정)
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Master Load Error: ' . $e->getMessage() );
}



/**
 * 활성화 훅 래퍼 함수 (치명적 오류 방지)
 * [v5.1.7] 전체 함수를 try-catch로 감싸고 상수 정의 확인 추가
 */
function jj_simple_style_guide_master_activate() {
    try {
        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // 상수가 정의되지 않은 경우 기본 경로 사용
            if ( function_exists( 'plugin_dir_path' ) ) {
                define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
            } else {
                define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
            }
        }
        
        // 활성화 매니저 클래스가 로드되지 않은 경우 로드 시도
        
        if ( ! class_exists( 'JJ_Activation_Manager' ) ) {
            $activation_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php';
            if ( file_exists( $activation_file ) ) {
                require_once $activation_file;
            } else {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 파일을 찾을 수 없습니다. 기본 활성화만 수행합니다.' );
                }
                return;
            }
        }
        
        // 클래스가 존재하는지 확인 후 활성화 실행
        if ( class_exists( 'JJ_Activation_Manager' ) && method_exists( 'JJ_Activation_Manager', 'activate' ) ) {
            try {
                JJ_Activation_Manager::activate();
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 오류 발생 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 치명적 오류 발생 - ' . $e->getMessage() );
                }
            }
        } else {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 클래스를 사용할 수 없습니다.' );
            }
        }
    } catch ( Exception $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 예외 발생 - ' . $e->getMessage() );
        }
    } catch ( Error $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 치명적 오류 발생 - ' . $e->getMessage() );
        }
    }
}
register_activation_hook( __FILE__, 'jj_simple_style_guide_master_activate' );

/**
 * Free 버전 전용 메인 컨트롤 클래스
 */
final class JJ_Simple_Style_Guide_Master {

    private static $instance = null;
    private $options = array();
    private $cockpit_page_id = 0;
    private $options_key = '';
    private $brand_colors = array();
    private $environment_map = array();
    private $adapters_config = array();
    private $license_manager = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 상수 정의 확인 후 할당
        if ( defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) {
            $this->options_key = JJ_STYLE_GUIDE_OPTIONS_KEY;
        }
        
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $this->license_manager = JJ_License_Manager::instance();
        }
        
        if ( defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) && function_exists( 'get_option' ) ) {
            $this->cockpit_page_id = (int) get_option( JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY );
        }

        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // [v5.3.7] Free 버전은 자체 config 폴더에서 로드
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
            if ( file_exists( $config_file ) ) {
                $this->adapters_config = include( $config_file );
                // [v5.5.0] Edition Controller를 통한 동적 어댑터 필터링
                if ( class_exists( 'JJ_Edition_Controller' ) ) {
                    $this->adapters_config = JJ_Edition_Controller::instance()->filter_adapters( $this->adapters_config );
                }
                
                
            }
        }

        // WordPress 함수가 로드된 후에만 훅 등록
        // [v5.1.9] plugin_basename() 안전 호출 추가
        // [v5.3.9] 모든 훅 등록을 조건문 안으로 이동하여 안전성 확보
        if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) && function_exists( 'plugin_basename' ) ) {
            add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
            add_action( 'admin_menu', array( $this, 'add_admin_menu_links' ) );
            $plugin_basename = function_exists( 'plugin_basename' ) ? plugin_basename( __FILE__ ) : basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );
            add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_plugin_settings_link' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            add_action( 'wp_ajax_jj_save_style_guide', array( $this, 'save_settings_ajax' ) );
            add_action( 'wp_ajax_jj_apply_temp_palette', array( $this, 'apply_temp_palette_ajax' ) );
            add_action( 'wp_ajax_jj_save_temp_palette', array( $this, 'save_temp_palette_ajax' ) );
            // [v3.8.0] Customizer 색상 새로고침 (팔레트 동기화)
            if ( class_exists( 'JJ_Customizer_Sync' ) ) {
                add_action( 'wp_ajax_jj_load_current_colors', array( 'JJ_Customizer_Sync', 'ajax_load_current_colors' ) );
            }
            // [v3.8.0] 섹션별 내보내기/불러오기 (팔레트 등)
            add_action( 'wp_ajax_jj_export_section', array( $this, 'ajax_export_section' ) );
            add_action( 'wp_ajax_jj_import_section', array( $this, 'ajax_import_section' ) );
            add_action( 'init', array( $this, 'load_options_and_init_strategies' ), 5 );
            add_action( 'init', array( $this, 'init_sync_manager' ), 10 );
            
            
            if ( JJ_STYLE_GUIDE_DEBUG ) {
                add_action( 'wp_footer', array( $this, 'render_debug_info' ), 999 );
                add_action( 'admin_footer', array( $this, 'render_debug_info' ), 999 );
            }
        }

        // Customizer 통합 (제한적)
        
        try {
            $customizer_manager_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php';
            if ( file_exists( $customizer_manager_path ) ) {
                require_once $customizer_manager_path;
                if ( class_exists( 'JJ_Customizer_Manager' ) ) {
                    $customizer_manager = JJ_Customizer_Manager::instance();
                    if ( method_exists( $customizer_manager, 'init' ) ) {
                        $customizer_manager->init();
                    }
                }
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }

    }

    /**
     * Free 버전: 어댑터 제한
     * 
     * @param array $config
     * @return array
     */
    private function limit_adapters_for_free( $config ) {
        // Free 버전: 테마 2개만 지원 (Twenty Twenty-Four, Twenty Twenty-Five)
        $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
        if ( isset( $config['themes'] ) && is_array( $config['themes'] ) ) {
            $config['themes'] = array_intersect_key( $config['themes'], array_flip( $allowed_themes ) );
        }

        // Free 버전: 플러그인 1개만 지원 (WooCommerce)
        $allowed_plugins = array( 'woocommerce' );
        if ( isset( $config['spokes'] ) && is_array( $config['spokes'] ) ) {
            $config['spokes'] = array_intersect_key( $config['spokes'], array_flip( $allowed_plugins ) );
        }

        return $config;
    }

    /**
     * Free 버전 워터마크 추가
     */
    public function add_free_watermark() {
        if ( ! is_admin() && current_user_can( 'manage_options' ) ) {
            echo '<div style="position: fixed; bottom: 10px; right: 10px; background: rgba(0, 0, 0, 0.7); color: #fff; padding: 8px 12px; font-size: 11px; border-radius: 4px; z-index: 99999; font-family: Arial, sans-serif;">';
            echo '<a href="' . esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ) . '" style="color: #fff; text-decoration: none;">JJ Style Guide Free</a>';
            echo '</div>';
        }
    }

    /**
     * 업그레이드 프롬프트 표시
     */
    public function show_upgrade_prompt() {
        // [Safety Lock] MASTER 또는 PARTNER 버전은 업그레이드 프롬프트 표시 안 함
        if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'partner' ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        $license_manager = null;
        $upgrade_url = 'https://j-j-labs.com'; // 기본값
        
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
                if ( $license_manager ) {
                    $upgrade_url = $license_manager->get_purchase_url( 'upgrade' );
                }
            }
        }
        ?>
        <div class="notice notice-info is-dismissible" style="background: #f0f6fc; border-left-color: #2271b1;">
            <p style="font-size: 14px; line-height: 1.8;">
                <strong><?php _e( '현재 버전을 사용 중입니다.', 'acf-css-really-simple-style-management-center' ); ?></strong><br>
                <?php _e( '더 많은 기능을 이용하려면 업그레이드하세요:', 'acf-css-really-simple-style-management-center' ); ?>
                <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" class="button button-primary" style="margin-left: 10px;">
                    <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
            </p>
            <ul style="margin: 10px 0; padding-left: 20px; list-style: disc;">
                <li><?php _e( '모든 팔레트 사용 가능 (브랜드, 시스템, 얼터너티브, 어나더, 임시)', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '모든 테마 및 플러그인 어댑터 지원', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '관리자 센터 및 실험실 센터 전체 기능', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '섹션별 내보내기/불러오기', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '고급 색상 선택 기능 (스포이드, 팔레트 불러오기)', 'acf-css-really-simple-style-management-center' ); ?></li>
            </ul>
        </div>
        <?php
    }

    public function init_sync_manager() {
        add_action( 'customize_save_after', array( $this, 'sync_from_customizer' ) );
        JJ_Sync_Manager::instance()->init( $this->options, $this->cockpit_page_id );
    }

    /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * Free 버전: 스타일 센터만 표시
     * [v5.1.9] 안전성 강화 - try-catch 추가
     */
            /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * [Phase 4.99] 퀵 링크 확장: 설정 | 스타일 | 메뉴 | 비주얼
     */
    public function add_plugin_settings_link( $links ) {
        try {
            if ( ! function_exists( 'admin_url' ) || ! function_exists( 'esc_url' ) ) {
                return $links;
            }
            
            $new_links = array();
            
            // 1. 설정 (Admin Center 메인)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ) . '" style="font-weight: 600;">' . __( 'Settings', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 2. 스타일 (Style Guide)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ) ) . '">' . __( 'Styles', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 3. 어드민 메뉴 (Admin Menu Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#admin-menu' ) ) . '">' . __( 'Menu', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 4. 비주얼 (Visual Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#visual' ) ) . '">' . __( 'Visual', 'acf-css-really-simple-style-management-center' ) . '</a>';

            // 기존 링크와 병합
            return array_merge( $new_links, $links );
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 오류 - ' . $e->getMessage() );
            }
            return $links;
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 치명적 오류 - ' . $e->getMessage() );
            }
            return $links;
        }
    }

    public function add_admin_menu_page() {
        add_options_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );
    }

    public function add_admin_menu_links() {
        // [Phase 4.5/4.99] "모양/도구/설정" 어디서나 동일한 화면으로 진입할 수 있도록 서브메뉴 추가
        add_theme_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        add_management_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        if ( $this->cockpit_page_id ) {
            add_submenu_page(
                'themes.php',
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                'manage_options',
                'post.php?post=' . $this->cockpit_page_id . '&action=edit',
                '',
                100
            );
        }
    }

    public function enqueue_admin_scripts( $hook_suffix ) {
        $is_settings_page = in_array(
            $hook_suffix,
            array(
                'settings_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'appearance_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'tools_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
            ),
            true
        );

        $is_cockpit_page = false;
        if ( 'post.php' === $hook_suffix ) {
            global $post;
            if ( $post && (int) $post->ID === $this->cockpit_page_id ) {
                $is_cockpit_page = true;
            }
        }

        if ( ! $is_settings_page && ! $is_cockpit_page ) {
            return;
        }

        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;

        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        // [v5.3.7] Free 버전은 자체 assets 폴더에서 로드
        wp_enqueue_style( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-editor.css', array( 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION );
        wp_enqueue_script( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-editor.js', array( 'jquery', 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION, true );

        $preview_page_id = $this->cockpit_page_id;
        $preview_url = $preview_page_id ? get_permalink( $preview_page_id ) : '';

        wp_localize_script(
            'jj-style-guide-editor',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'temp_palette_nonce' => wp_create_nonce( 'jj_temp_palette_nonce' ),
                'settings' => $this->options,
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'edition'  => 'free', // Free 버전 표시
                'i18n'     => array(
                    'saving' => __( '저장 중...', 'acf-css-really-simple-style-management-center' ),
                    'saved'  => __( '스타일 저장됨', 'acf-css-really-simple-style-management-center' ),
                    'applied' => __( '임시 팔레트 적용됨! \'스타일 저장\'을 눌러야 최종 반영됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'error'  => __( '오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
                    'confirm_apply' => __( '정말로 \'임시 팔레트\'의 값으로 덮어쓰시겠습니까?\n이 작업은 즉시 UI에 반영되지만, \n\'스타일 저장\' 버튼을 눌러야 최종 저장됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'preview_url' => $preview_url ? esc_url( $preview_url ) : '',
                    'preview_page_id' => $preview_page_id,
                ),
            )
        );
    }

    public function render_style_guide_page() {
        try {
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options      = $options;
            $temp_options       = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY );
            $options            = $this->options;
            $cockpit_page_id    = $this->cockpit_page_id;
            $preview_url        = $cockpit_page_id ? get_permalink( $cockpit_page_id ) : '';

        // 옵션이 비어있으면 기본값 설정
        if ( empty( $this->options ) ) {
            $this->options = array(
                'palettes' => array(
                    'brand' => array(
                        'primary_color' => '',
                        'primary_color_hover' => '',
                        'secondary_color' => '',
                        'secondary_color_hover' => '',
                    ),
                    'system' => array(), // Free 버전: 시스템 팔레트 비활성화
                    'alternative' => array(), // Free 버전: 얼터너티브 팔레트 비활성화
                    'another' => array(), // Free 버전: 어나더 팔레트 비활성화
                ),
                'typography' => array(),
                'buttons' => array(),
                'forms' => array(),
                'contexts' => array(),
            );
            $options = $this->options;
        }
        ?>
        <div id="jj-style-guide-wrapper" class="wrap jj-style-guide-wrap" style="background: #f0f0f1; margin: 0; padding: 0;">
            <form id="jj-style-guide-form">

                <div class="jj-style-guide-header" style="top: 32px; background: #fff; padding: 15px 25px; border-bottom: 1px solid #c3c4c7; z-index: 999; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h1 style="margin: 0; font-size: 24px;">
                            <?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?> (v<?php echo esc_html( JJ_STYLE_GUIDE_VERSION ); ?>) 
                            <?php if ( ! class_exists("JJ_Edition_Controller") || JJ_Edition_Controller::instance()->get_current_edition() === "free" ) : ?><span style="background: #d63638; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 14px; font-weight: 600;">Free</span><?php endif; ?>
                        </h1>
                        <?php
                        $license_manager = null;
                        $purchase_url = 'https://j-j-labs.com'; // 기본값
                        
                        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                            if ( class_exists( 'JJ_License_Manager' ) ) {
                                $license_manager = JJ_License_Manager::instance();
                                if ( $license_manager ) {
                                    $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                                }
                            }
                        }
                        ?>
                        <?php if ( ! class_exists("JJ_Edition_Controller") || ! JJ_Edition_Controller::instance()->is_at_least("partner") ) : ?><a href="<?php echo esc_url( $purchase_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary" style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px;">
                            <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                        </a><?php endif; ?>
                        <?php if ( $preview_url ) : ?>
                        <button type="button" 
                                class="button button-secondary" 
                                id="jj-preview-button"
                                style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> <?php _e( '실시간 미리보기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="jj-header-actions" style="display: flex; align-items: center; gap: 10px;">
                        <span id="jj-save-success" style="display: none; vertical-align: middle; color: #2271b1; font-weight: 600; margin-right: 15px;"></span>
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-save-style-guide" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
                <div class="jj-style-guide-content" style="margin: 20px auto; background: #ffffff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 1px;">
                    <?php
                    // Free 버전: 제한된 섹션만 표시
                    // [v5.3.7] Free 버전은 자체 includes/editor-views 폴더에서 로드
                    $sections_map = array(
                        'colors'     => 'includes/editor-views/view-section-colors.php',
                        'typography' => 'includes/editor-views/view-section-typography.php',
                        'buttons'    => 'includes/editor-views/view-section-buttons.php',
                        'forms'      => 'includes/editor-views/view-section-forms.php',
                    );

                    // Free 버전: 섹션 순서 고정
                    $sections_to_render = array();
                    foreach ( $sections_map as $slug => $relative_path ) {
                        $sections_to_render[] = array(
                            'slug' => $slug,
                            'file' => JJ_STYLE_GUIDE_PATH . $relative_path,
                            'order'=> 0, // 순서는 UI에서만 표시
                        );
                    }
                    ?>
                    <div id="jj-sections-sortable" class="jj-sections-sortable">
                        <?php
                        foreach ( $sections_to_render as $section ) {
                            // Free 버전: 기본 뷰 파일 사용 (제한 로직은 뷰 파일 내부에서 처리)
                            $default_file = JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-section-' . $section['slug'] . '.php';
                            if ( file_exists( $section['file'] ) ) {
                                echo '<div class="jj-section-wrapper" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $section['file'];
                                echo '</div>';
                            } elseif ( file_exists( $default_file ) ) {
                                // Free 버전 전용 뷰가 없으면 기본 뷰 사용 (제한 로직 적용 필요)
                                echo '<div class="jj-section-wrapper jj-section-free-version" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $default_file;
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="jj-style-guide-footer" style="margin: 20px auto; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: #666; font-size: 13px;">
                        <?php
                        // Master/Partner에서는 업그레이드 유도 문구를 절대 노출하지 않음
                        $show_upgrade_footer = true;
                        if ( class_exists( 'JJ_Edition_Controller' ) ) {
                            try {
                                $show_upgrade_footer = ! JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                            } catch ( Exception $e ) {
                                // ignore
                            } catch ( Error $e ) {
                                // ignore
                            }
                        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
                            $show_upgrade_footer = ! in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
                        }

                        if ( $show_upgrade_footer ) {
                            _e( '더 많은 기능을 이용하려면 업그레이드하세요.', 'acf-css-really-simple-style-management-center' );
                        }
                        ?>
                    </div>
                    <div style="text-align: right;">
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-style-guide-footer-button" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        } catch ( Exception $e ) {
            // 오류 발생 시에도 기본 페이지 구조는 표시
            ?>
            <div class="wrap" style="padding: 20px;">
                <h1><?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?></h1>
                <div class="notice notice-error">
                    <p><strong><?php _e( '오류가 발생했습니다:', 'acf-css-really-simple-style-management-center' ); ?></strong> <?php echo esc_html( $e->getMessage() ); ?></p>
                </div>
            </div>
            <?php
        }
    }

    // 나머지 메서드들은 dev 버전과 동일하지만, 기능 제한 로직 추가 필요
    // save_settings_ajax, apply_temp_palette_ajax, save_temp_palette_ajax 등
    
    public function save_settings_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        if ( ! isset( $_POST['settings'] ) || ! is_array( $_POST['settings'] ) ) {
            wp_send_json_error( array( 'message' => __( '잘못된 데이터입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // [Phase 5 B] 멀티사이트 네트워크 전용 모드에서는 사이트별 저장 차단
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $ms = JJ_Multisite_Controller::instance();
                if ( method_exists( $ms, 'is_enabled' ) && method_exists( $ms, 'allow_site_override' ) ) {
                    if ( $ms->is_enabled() && ! $ms->allow_site_override() ) {
                        wp_send_json_error( array( 'message' => __( '이 사이트는 네트워크에서 스타일을 관리 중입니다. 네트워크 관리자에서 변경해주세요.', 'acf-css-really-simple-style-management-center' ) ) );
                    }
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        $old_settings = array();
        if ( function_exists( 'get_option' ) ) {
            $old_settings = (array) get_option( $this->options_key, array() );
        }

        $raw_settings = wp_unslash( $_POST['settings'] );
        
        // Free 버전: 제한된 설정만 저장
        $raw_settings = $this->filter_settings_for_free( $raw_settings );
        
        $sanitized_settings = $this->sanitize_options_recursive( $raw_settings );
        update_option( $this->options_key, $sanitized_settings );

        // [Phase 5.2] Webhook/자동화용 이벤트
        if ( function_exists( 'do_action' ) ) {
            do_action( 'jj_style_guide_settings_updated', $sanitized_settings, $old_settings, 'style_center_ajax' );
        }

        $this->sync_to_spokes( $sanitized_settings );

        JJ_CSS_Cache::instance()->flush();
        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * Free 버전: 저장 가능한 설정 필터링
     */
    private function filter_settings_for_free( $settings ) {
        // Free 버전: 시스템 팔레트, 얼터너티브 팔레트, 어나더 팔레트 제거
        if ( isset( $settings['palettes'] ) ) {
            unset( $settings['palettes']['system'] );
            unset( $settings['palettes']['alternative'] );
            unset( $settings['palettes']['another'] );
        }
        
        // Free 버전: 타이포그래피 H5, H6 제거
        if ( isset( $settings['typography'] ) ) {
            unset( $settings['typography']['h5'] );
            unset( $settings['typography']['h6'] );
        }
        
        // Free 버전: Secondary, Text 버튼 제거
        if ( isset( $settings['buttons'] ) ) {
            unset( $settings['buttons']['secondary'] );
            unset( $settings['buttons']['text'] );
        }
        
        return $settings;
    }

    public function apply_temp_palette_ajax() {
        // Free 버전: 임시 팔레트 사용 불가
        $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
        if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
            $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
        }
        wp_send_json_error( array( 
            'message' => $message
        ) );
    }

    public function save_temp_palette_ajax() {
        // Free 버전: 임시 팔레트 사용 불가
        $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
        if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
            $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
        }
        wp_send_json_error( array( 
            'message' => $message
        ) );
    }

    /**
     * [v3.8.0] 섹션별 내보내기
     * - 현재는 팔레트(brand/system/alternative/another/temp-palette)만 지원
     * - 관리자에서 파일 다운로드(attachment)로 전달
     */
    public function ajax_export_section() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $section_type = isset( $_POST['section_type'] ) ? sanitize_key( wp_unslash( $_POST['section_type'] ) ) : '';
        $section_subtype = isset( $_POST['section_subtype'] ) ? sanitize_key( wp_unslash( $_POST['section_subtype'] ) ) : '';

        if ( 'palette' !== $section_type ) {
            wp_die( esc_html__( '지원하지 않는 섹션 타입입니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $allowed_subtypes = array( 'brand', 'system', 'alternative', 'another', 'temp' );
        if ( ! in_array( $section_subtype, $allowed_subtypes, true ) ) {
            wp_die( esc_html__( '지원하지 않는 섹션 서브타입입니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        $data = array();

        if ( 'temp' === $section_subtype ) {
            $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
            $data = isset( $temp_options['palettes'] ) ? (array) $temp_options['palettes'] : array();
        } else {
            $data = isset( $options['palettes'][ $section_subtype ] ) ? (array) $options['palettes'][ $section_subtype ] : array();
        }

        $payload = array(
            'exported_at'   => function_exists( 'current_time' ) ? current_time( 'mysql' ) : gmdate( 'c' ),
            'plugin'        => 'acf-css-really-simple-style-management-center',
            'plugin_version'=> defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '',
            'section_type'  => $section_type,
            'section_subtype' => $section_subtype,
            'data'          => $data,
        );

        if ( function_exists( 'nocache_headers' ) ) {
            nocache_headers();
        }

        $filename = sprintf(
            'acf-css-%s-%s-%s.json',
            $section_type,
            $section_subtype,
            gmdate( 'Ymd-His' )
        );

        // 다운로드 헤더
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

        echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        exit;
    }

    /**
     * [v3.8.0] 섹션별 불러오기
     * - JSON 업로드 후 옵션에 병합(키 기준 덮어쓰기)
     */
    public function ajax_import_section() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $section_type = isset( $_POST['section_type'] ) ? sanitize_key( wp_unslash( $_POST['section_type'] ) ) : '';
        $section_subtype = isset( $_POST['section_subtype'] ) ? sanitize_key( wp_unslash( $_POST['section_subtype'] ) ) : '';

        if ( 'palette' !== $section_type ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 섹션 타입입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $allowed_subtypes = array( 'brand', 'system', 'alternative', 'another', 'temp' );
        if ( ! in_array( $section_subtype, $allowed_subtypes, true ) ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 섹션 서브타입입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        if ( empty( $_FILES['import_file'] ) || empty( $_FILES['import_file']['tmp_name'] ) ) {
            wp_send_json_error( array( 'message' => __( '업로드된 파일이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $tmp_name = $_FILES['import_file']['tmp_name'];
        $raw = file_get_contents( $tmp_name );
        if ( ! $raw ) {
            wp_send_json_error( array( 'message' => __( '파일을 읽을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $decoded = json_decode( $raw, true );
        if ( ! is_array( $decoded ) ) {
            wp_send_json_error( array( 'message' => __( 'JSON 형식이 올바르지 않습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // payload 형식({data:{...}})과 순수 데이터({..}) 둘 다 허용
        $import_data = isset( $decoded['data'] ) && is_array( $decoded['data'] ) ? (array) $decoded['data'] : $decoded;

        // 간단 정리(색상 키는 hex로)
        $sanitized = array();
        foreach ( (array) $import_data as $k => $v ) {
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( (string) $k ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $k ) );
            if ( is_array( $v ) ) {
                // 팔레트는 1depth만 정리 (필요 시 확장)
                $sanitized[ $key ] = $v;
                continue;
            }
            if ( strpos( $key, 'color' ) !== false ) {
                $hex = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( (string) $v ) : '';
                $sanitized[ $key ] = $hex ? $hex : '';
                continue;
            }
            $sanitized[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( (string) $v ) : strip_tags( (string) $v );
        }

        if ( 'temp' === $section_subtype ) {
            $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
            if ( ! isset( $temp_options['palettes'] ) || ! is_array( $temp_options['palettes'] ) ) {
                $temp_options['palettes'] = array();
            }
            $temp_options['palettes'] = array_merge( (array) $temp_options['palettes'], $sanitized );
            update_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, $temp_options );
        } else {
            $options = (array) get_option( $this->options_key, array() );
            if ( ! isset( $options['palettes'] ) || ! is_array( $options['palettes'] ) ) {
                $options['palettes'] = array();
            }
            $options['palettes'][ $section_subtype ] = array_merge(
                (array) ( $options['palettes'][ $section_subtype ] ?? array() ),
                $sanitized
            );
            update_option( $this->options_key, $options );
        }

        wp_send_json_success( array(
            'message' => __( '설정이 성공적으로 불러와졌습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }

    // 나머지 메서드들은 dev 버전과 동일 (sync_to_spokes, sync_from_customizer, sanitize_options_recursive 등)
    // 생략하거나 dev 버전 파일을 복사하여 수정

    private function sync_to_spokes( $hub_options ) {
        // Free 버전: WooCommerce만 동기화
        $brand_colors = $hub_options['palettes']['brand'] ?? array();
        
        // WooCommerce 동기화
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! is_array( $kwe ) ) {
            $kwe = array();
        }
        if ( isset( $brand_colors['primary_color'] ) ) {
            $kwe['product_quickview_button_color'] = $brand_colors['primary_color'];
        }
        if ( isset( $brand_colors['primary_color_hover'] ) ) {
            $kwe['product_quickview_button_color_hover'] = $brand_colors['primary_color_hover'];
        }
        update_option( 'kt_woo_extras', $kwe );
    }

    public function sync_from_customizer( $manager ) {
        $hub_options = (array) get_option( $this->options_key );
        $panel_settings = $manager->settings();
        
        foreach ( $panel_settings as $setting_id => $setting_obj ) {
            if ( strpos( $setting_id, $this->options_key . '[' ) === 0 ) {
                $keys = str_replace( array( $this->options_key, '[', ']' ), '', $setting_id );
                $keys = explode( '][', $keys );
                $this->set_nested_object_value( $hub_options, $keys, $setting_obj->post_value() );
            }
        }

        update_option( $this->options_key, $hub_options );
        JJ_CSS_Cache::instance()->flush();
    }

    private function set_nested_object_value( &$obj, $keys, $value ) {
        $current = &$obj;
        foreach ( $keys as $key ) {
            if ( ! isset( $current[$key] ) || ! is_array( $current[$key] ) ) {
                $current[$key] = array();
            }
            $current = &$current[$key];
        }
        $current = $value;
    }

    private function sanitize_options_recursive( $input ) {
        $output = array();
        foreach ( (array) $input as $key => $value ) {
            $raw_key = (string) $key;
            // [v5.3.8] WordPress 함수 안전 호출
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( $raw_key ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( $raw_key ) );
            if ( is_array( $value ) ) {
                $output[ $key ] = $this->sanitize_options_recursive( $value );
                continue;
            }
            // [v5.3.8] PHP 7.4 호환성: str_contains() 대신 strpos() 사용
            if ( strpos( $key, 'color' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $sanitized = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( $value ) : '';
                $output[ $key ] = $sanitized ? $sanitized : '';
                continue;
            }
            if ( strpos( $key, 'custom_css' ) !== false || strpos( $key, 'additional_css' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'wp_kses_post' ) ? wp_kses_post( $value ) : wp_strip_all_tags( $value );
                continue;
            }
            if ( strpos( $key, 'font_family' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
                continue;
            }
            // [v5.3.8] WordPress 함수 안전 호출
            $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
        }
        return $output;
    }

    public function load_options_and_init_strategies() {
        // [v5.3.5] 전체 메서드를 try-catch로 보호하여 치명적 오류 방지
        try {
            $this->environment_map = $this->scan_environment();
            $this->load_dynamic_adapters( $this->environment_map );
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options = $options;
            $this->sync_existing_settings();
            $this->brand_colors = $this->options['palettes']['brand'] ?? array();

            // [v5.3.5] 모든 전략 클래스 호출에 class_exists() 확인 추가
            // 전략 1: CSS 변수
            if ( class_exists( 'JJ_Strategy_1_CSS_Vars' ) ) {
                JJ_Strategy_1_CSS_Vars::instance()->init( $this->options );
            }
            
            // 전략 2: PHP 필터
            if ( class_exists( 'JJ_Strategy_2_PHP_Filters' ) ) {
                JJ_Strategy_2_PHP_Filters::instance()->init( $this->options, null, null, null, null );
            }
            
            // 전략 3: Dequeue (CSS 재정의)
            if ( class_exists( 'JJ_Strategy_3_Dequeue' ) ) {
                JJ_Strategy_3_Dequeue::instance()->init( $this->options );
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    private function sync_existing_settings() {
        $active_theme = wp_get_theme()->get_template();
        $needs_update = false;

        if ( ! isset( $this->options['palettes'] ) ) {
            $this->options['palettes'] = array();
        }
        if ( ! isset( $this->options['palettes']['brand'] ) ) {
            $this->options['palettes']['brand'] = array();
        }
        if ( ! isset( $this->options['typography'] ) ) {
            $this->options['typography'] = array();
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five 테마만 동기화
        if ( in_array( $active_theme, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
            // 기본 테마는 별도 설정이 없으므로 Customizer에서 가져오기
        }

        // WooCommerce 동기화 (Free 버전 지원)
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! empty( $kwe['product_quickview_button_color'] ) && empty( $this->options['palettes']['brand']['primary_color'] ) ) {
            $this->options['palettes']['brand']['primary_color'] = $kwe['product_quickview_button_color'];
            $needs_update = true;
        }
        if ( ! empty( $kwe['product_quickview_button_color_hover'] ) && empty( $this->options['palettes']['brand']['primary_color_hover'] ) ) {
            $this->options['palettes']['brand']['primary_color_hover'] = $kwe['product_quickview_button_color_hover'];
            $needs_update = true;
        }

        if ( $needs_update ) {
            update_option( $this->options_key, $this->options );
        }
    }

    private function load_dynamic_adapters( $environment_map ) {
        $active_adapters = $environment_map;
        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;
        $error_handler = JJ_Error_Handler::instance();

        // Free 버전: 테마 어댑터 제한 (Twenty Twenty-Four, Twenty Twenty-Five만)
        if ( ! empty( $active_adapters['themes'] ) ) {
            $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
            $active_adapters['themes'] = array_intersect( $active_adapters['themes'], $allowed_themes );
            
            foreach ( $active_adapters['themes'] as $theme_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-theme-' . sanitize_file_name( $theme_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Theme_' . str_replace( '-', '_', ucwords( $theme_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }

        // Free 버전: 플러그인 어댑터 제한 (WooCommerce만)
        if ( ! empty( $active_adapters['spokes'] ) ) {
            $allowed_plugins = array( 'woocommerce' );
            $active_adapters['spokes'] = array_intersect( $active_adapters['spokes'], $allowed_plugins );
            
            foreach ( $active_adapters['spokes'] as $spoke_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-spoke-' . sanitize_file_name( $spoke_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Spoke_' . str_replace( '-', '_', ucwords( $spoke_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }
    }

    private function scan_environment() {
        $config = $this->adapters_config;
        $spokes_to_check = $config['spokes'] ?? array();
        $themes_to_check = $config['themes'] ?? array();

        $active_plugins = (array) get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        $active_spoke_adapters = array();
        $active_theme_adapters = array();
        $current_theme_path = get_template_directory();
        $theme_dir_name = basename( $current_theme_path );

        // Free 버전: WooCommerce만 체크
        foreach ( $spokes_to_check as $adapter_slug => $plugin_path ) {
            if ( in_array( $plugin_path, $active_plugins ) && $adapter_slug === 'woocommerce' ) {
                $active_spoke_adapters[] = $adapter_slug;
            }
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five만 체크
        foreach ( $themes_to_check as $adapter_slug => $theme_path_suffix ) {
            if ( $theme_dir_name === $adapter_slug && in_array( $adapter_slug, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
                if ( file_exists( $current_theme_path . '/' . $theme_path_suffix ) ) {
                    $active_theme_adapters[] = $adapter_slug;
                }
            }
        }

        return array(
            'themes' => $active_theme_adapters,
            'spokes' => $active_spoke_adapters,
        );
    }

    public function render_debug_info() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $errors = JJ_Error_Handler::instance()->get_errors();

        echo '<div style="background: #001a33; color: #00ff8c; padding: 20px; font-family: monospace; font-size: 14px; line-height: 1.6; z-index: 99999; position: relative; margin: 20px; border-radius: 5px; white-space: pre-wrap; word-break: break-all; opacity: 0.9;">';
        echo '<strong>[ACF CSS DEBUG - FREE VERSION]</strong><br>';
        echo '====================<br>';
        echo '<strong>Edition:</strong> Free<br>';
        echo '<strong>Loaded Adapters:</strong><br>';
        if ( ! empty( $this->environment_map['themes'] ) ) {
            echo ' - Themes: ' . esc_html( implode( ', ', $this->environment_map['themes'] ) ) . '<br>';
        } else {
            echo ' - Themes: None<br>';
        }
        if ( ! empty( $this->environment_map['spokes'] ) ) {
            echo ' - Spokes: ' . esc_html( implode( ', ', $this->environment_map['spokes'] ) ) . '<br>';
        } else {
            echo ' - Spokes: None<br>';
        }
        echo '</div>';
    }
}

function jj_simple_style_guide_master_run() {
    // [Phase 5] 다국어 지원 로드
    load_plugin_textdomain( 'acf-css-really-simple-style-management-center', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // WordPress 함수가 로드되었는지 확인
    if ( ! function_exists( 'class_exists' ) || ! function_exists( 'file_exists' ) ) {
        return;
    }
    
    // [v5.3.9] 코드 무결성 모니터 초기화 (plugins_loaded 시점)
    
    // [Phase 4.5/4.99] Admin Center 초기화 (메뉴/상단바/관리자 UI)
    if ( class_exists( 'JJ_Admin_Center' ) ) {
        try {
            $admin_center = JJ_Admin_Center::instance();
            if ( method_exists( $admin_center, 'init' ) ) {
                $admin_center->init();
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v3.7.0] 백업 관리자 초기화
    if ( class_exists( 'JJ_Backup_Manager' ) ) {
        try {
            JJ_Backup_Manager::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] REST API 초기화
    if ( class_exists( 'JJ_Style_Guide_REST_API' ) ) {
        try {
            JJ_Style_Guide_REST_API::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.3] Extension Manager 초기화
    if ( class_exists( 'JJ_Extension_Manager' ) ) {
        try {
            JJ_Extension_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] Webhook Manager 초기화
    if ( class_exists( 'JJ_Webhook_Manager' ) ) {
        try {
            JJ_Webhook_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5 B] Multisite Controller 초기화
    if ( class_exists( 'JJ_Multisite_Controller' ) ) {
        try {
            JJ_Multisite_Controller::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v5.6.0] Visual Command Center 초기화
    if ( class_exists( 'JJ_Visual_Command_Center' ) ) {
        JJ_Visual_Command_Center::instance();
    }

    // [v5.7.0] AI Style Intelligence 초기화
    if ( class_exists( 'JJ_Smart_Palette' ) ) {
        JJ_Smart_Palette::instance();
    }

    // [v5.9.0] CSS Optimizer 초기화
    if ( class_exists( 'JJ_CSS_Optimizer' ) ) {
        JJ_CSS_Optimizer::instance();
    }

    // [v5.8.0] Cloud Manager 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Cloud_Manager' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) { // Premium 이상 체크 대용
            JJ_Cloud_Manager::instance();
        }
    }
    
    // [Phase 3] 템플릿 마켓 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Template_Market' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) {
            JJ_Template_Market::instance();
        }
    }
    
    // [Phase 5] Partner Hub 초기화 (Partner/Master만)
    if ( class_exists( 'JJ_Partner_Hub' ) ) {
        JJ_Partner_Hub::instance();
    }

    // [v4.2.2] 실험실(Labs) 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Labs_Center' ) ) {
        try {
            JJ_Labs_Center::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }


    if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
        try {
            JJ_Code_Integrity_Monitor::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Code Integrity Monitor: 초기화 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.9] 라이센스 강제 실행 초기화 (plugins_loaded 시점)
    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $enforcement_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-enforcement.php';
        if ( file_exists( $enforcement_file ) ) {
            require_once $enforcement_file;
            if ( class_exists( 'JJ_License_Enforcement' ) ) {
                try {
                    JJ_License_Enforcement::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ License Enforcement: 초기화 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // [v5.3.7] 안전 모드 플래그 확인 및 활성화
    if ( function_exists( 'get_option' ) && class_exists( 'JJ_Safe_Mode' ) ) {
        $safe_mode_enabled = get_option( 'jj_style_guide_safe_mode', false );
        if ( $safe_mode_enabled ) {
            $safe_mode_reason = get_option( 'jj_style_guide_safe_mode_reason', '활성화 중 오류 발생' );
            try {
                $safe_mode = JJ_Safe_Mode::instance();
                if ( method_exists( $safe_mode, 'enable' ) ) {
                    $safe_mode->enable( $safe_mode_reason );
                }
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 오류 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
    
    // 메인 클래스 인스턴스 생성
    if ( class_exists( 'JJ_Simple_Style_Guide_Master' ) ) {
        try {
            JJ_Simple_Style_Guide_Master::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.6] 기존 플러그인 마이그레이션 매니저 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $migration_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php';
        if ( file_exists( $migration_file ) ) {
            // 마이그레이션 매니저는 이미 safe_loader를 통해 로드되었을 수 있음
            // 클래스가 존재하는 경우에만 초기화
            if ( class_exists( 'JJ_Migration_Manager' ) ) {
                try {
                    JJ_Migration_Manager::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 오류 - ' . $e->getMessage() );
                    }
                } catch ( Error $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // 플러그인 업데이트 체크 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $updater_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php';
        if ( file_exists( $updater_file ) ) {
            require_once $updater_file;
            if ( class_exists( 'JJ_Plugin_Updater' ) ) {
                try {
                    new JJ_Plugin_Updater(
                        __FILE__,
                        'advanced-custom-style-manage-center',
                        "ACF CSS Master",
                        defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.4.0',
                        'DEV' // 마스터 버전
                    );
                } catch ( Exception $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 오류 - ' . $e->getMessage() );
                } catch ( Error $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
}

// [v5.3.8] plugins_loaded 훅 활성화
if ( function_exists( 'add_action' ) ) {
    add_action( 'plugins_loaded', 'jj_simple_style_guide_master_run', 10 );
}

/**
 * [v8.0.0] 안전 장치: 필수 헬퍼 함수 Fallback 정의
 * class-jj-admin-center.php 로드 실패 시에도 사이트가 멈추지 않도록 보장합니다.
 */
if ( ! function_exists( 'jj_style_guide_text' ) ) {
    function jj_style_guide_text( $key, $fallback = '' ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_text( $key, $fallback );
        }
        return $fallback;
    }
}

if ( ! function_exists( 'jj_style_guide_sections_layout' ) ) {
    function jj_style_guide_sections_layout() {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_sections_layout();
        }
        return array();
    }
}

if ( ! function_exists( 'jj_style_guide_section_index' ) ) {
    function jj_style_guide_section_index( $slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_section_index( $slug );
        }
        return null;
    }
}

if ( ! function_exists( 'jj_style_guide_is_tab_enabled' ) ) {
    function jj_style_guide_is_tab_enabled( $section_slug, $tab_slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->is_tab_enabled( $section_slug, $tab_slug );
        }
        return true;
    }
}

```

```php
<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * J&J 실험실
 * - CSS/HTML/JS 스캐너 및 수동 오버라이드 기능을 위한 별도 페이지
 * - Admin Center와 유사한 구조로 분리된 고급 기능 영역
 * 
 * @version 4.2.2
 * - "실험실 센터" → "실험실" 명칭 변경
 * - Labs → 실험실 명칭 변경
 * - 별도 페이지로 분리 (스타일 센터에서 독립)
 * - CSS/HTML/JS 스캐너 기능 강화
 * - 탭 기반 인터페이스 (스캐너, 수동 재정의, 공식 지원 목록)
 */
final class JJ_Labs_Center {

    private static $instance = null;
    private $option_key = 'jj_style_guide_options'; // labs 섹션 포함
    private $labs_tabs_option_key = 'jj_style_guide_labs_tabs_layout'; // [v5.0.0] 실험실 탭 활성화/비활성화
    private $adapters_config = array();
    private $options = array();
    
    // [v5.0.0] 성능 최적화: 실험실 탭 레이아웃 캐싱
    private static $labs_tabs_layout_cache = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
            if ( file_exists( $config_file ) ) {
                $this->adapters_config = include $config_file;
            }
        }
    }

    /**
     * 초기화: 메뉴 등록 훅 연결
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_labs_center_assets' ) );
        add_action( 'admin_init', array( $this, 'save_labs_tabs_layout' ) ); // [v5.0.0] 실험실 탭 활성화/비활성화 저장
        // [v3.7.0] 스캐너 AJAX
        add_action( 'wp_ajax_jj_scan_page_for_css', array( $this, 'ajax_scan_page_for_css' ) );
    }

    /**
     * [v3.7.0] AJAX: 페이지 스캔(최소 유용 버전)
     * - URL의 HTML을 가져와 class/id, style block, 주요 색상/폰트를 추출합니다.
     * - 크로스오리진/로그인/차단 환경에서는 일부 정보만 제공될 수 있습니다.
     */
    public function ajax_scan_page_for_css() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }

        $scan_url = isset( $_POST['scan_url'] ) ? esc_url_raw( wp_unslash( $_POST['scan_url'] ) ) : '';
        if ( empty( $scan_url ) ) {
            wp_send_json_error( array( 'message' => __( 'URL을 입력해주세요.', 'jj-style-guide' ) ) );
        }

        if ( function_exists( 'wp_http_validate_url' ) && ! wp_http_validate_url( $scan_url ) ) {
            wp_send_json_error( array( 'message' => __( '유효하지 않은 URL입니다.', 'jj-style-guide' ) ) );
        }

        $response = wp_remote_get(
            $scan_url,
            array(
                'timeout'     => 15,
                'redirection' => 3,
                'user-agent'  => 'JJ-Labs-Scanner/1.0 (+WordPress)',
            )
        );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => __( '서버 통신 오류: ', 'jj-style-guide' ) . $response->get_error_message() ) );
        }

        $html = wp_remote_retrieve_body( $response );
        if ( empty( $html ) ) {
            wp_send_json_error( array( 'message' => __( '페이지 내용을 가져오지 못했습니다.', 'jj-style-guide' ) ) );
        }

        // 1) style blocks
        $style_blocks = array();
        if ( preg_match_all( '/<style[^>]*>(.*?)<\\/style>/is', $html, $m ) ) {
            foreach ( (array) ( $m[1] ?? array() ) as $block ) {
                $block = trim( (string) $block );
                if ( '' !== $block ) {
                    $style_blocks[] = $block;
                }
            }
        }
        $style_blocks = array_slice( $style_blocks, 0, 10 );

        // 2) class selectors
        $class_selectors = array();
        if ( preg_match_all( '/class\\s*=\\s*[\"\\\']([^\"\\\']+)[\"\\\']/i', $html, $cm ) ) {
            foreach ( (array) ( $cm[1] ?? array() ) as $class_attr ) {
                $parts = preg_split( '/\\s+/', trim( (string) $class_attr ) );
                foreach ( (array) $parts as $cls ) {
                    $cls = preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $cls );
                    if ( '' !== $cls ) {
                        $class_selectors[] = '.' . $cls;
                    }
                }
            }
        }
        $class_selectors = array_values( array_unique( $class_selectors ) );
        sort( $class_selectors );

        // 3) id selectors
        $id_selectors = array();
        if ( preg_match_all( '/id\\s*=\\s*[\"\\\']([^\"\\\']+)[\"\\\']/i', $html, $im ) ) {
            foreach ( (array) ( $im[1] ?? array() ) as $id ) {
                $id = preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $id );
                if ( '' !== $id ) {
                    $id_selectors[] = '#' . $id;
                }
            }
        }
        $id_selectors = array_values( array_unique( $id_selectors ) );
        sort( $id_selectors );

        // 4) colors (hex + rgb)
        $scan_text = $html . "\n\n" . implode( "\n\n", $style_blocks );
        $color_counts = array();

        // hex colors
        if ( preg_match_all( '/#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})\\b/', $scan_text, $hm ) ) {
            foreach ( (array) ( $hm[0] ?? array() ) as $hex ) {
                $hex = strtoupper( $hex );
                if ( strlen( $hex ) === 4 ) {
                    // #RGB -> #RRGGBB
                    $hex = '#' . $hex[1] . $hex[1] . $hex[2] . $hex[2] . $hex[3] . $hex[3];
                }
                $color_counts[ $hex ] = ( $color_counts[ $hex ] ?? 0 ) + 1;
            }
        }

        // rgb/rgba colors
        if ( preg_match_all( '/rgba?\\(\\s*(\\d{1,3})\\s*,\\s*(\\d{1,3})\\s*,\\s*(\\d{1,3})/i', $scan_text, $rm, PREG_SET_ORDER ) ) {
            foreach ( $rm as $match ) {
                $r = max( 0, min( 255, intval( $match[1] ) ) );
                $g = max( 0, min( 255, intval( $match[2] ) ) );
                $b = max( 0, min( 255, intval( $match[3] ) ) );
                $hex = sprintf( '#%02X%02X%02X', $r, $g, $b );
                $color_counts[ $hex ] = ( $color_counts[ $hex ] ?? 0 ) + 1;
            }
        }

        arsort( $color_counts );
        $colors = array_slice( array_keys( $color_counts ), 0, 50 );

        // 5) fonts
        $fonts = array();
        if ( preg_match_all( '/font-family\\s*:\\s*([^;\\}\\n]+)[;\\}]/i', $scan_text, $fm ) ) {
            foreach ( (array) ( $fm[1] ?? array() ) as $font_list ) {
                $parts = explode( ',', (string) $font_list );
                foreach ( $parts as $font ) {
                    $font = trim( (string) $font );
                    $font = trim( $font, "\"'" );
                    if ( '' === $font ) {
                        continue;
                    }
                    $fonts[] = $font;
                }
            }
        }
        $fonts = array_values( array_unique( $fonts ) );
        $fonts = array_slice( $fonts, 0, 20 );

        // 6) conflicts (간단)
        $conflicts = array();
        if ( count( $colors ) >= 25 ) {
            $conflicts[] = array(
                'type' => 'warning',
                'message' => __( '색상이 너무 다양합니다.', 'jj-style-guide' ),
                'suggestion' => __( '상위 2~4개 색상만 브랜드/시스템 팔레트로 정리하면 UI 일관성이 좋아집니다.', 'jj-style-guide' ),
            );
        }

        $html_snippet = substr( $html, 0, 2000 );

        wp_send_json_success(
            array(
                'scan_url' => $scan_url,
                'class_selectors' => $class_selectors,
                'id_selectors' => $id_selectors,
                'style_blocks' => $style_blocks,
                'html_snippet' => $html_snippet,
                'colors' => $colors,
                'fonts' => $fonts,
                'conflicts' => $conflicts,
                'suggestions' => array(
                    'primary_colors' => array_slice( $colors, 0, 6 ),
                    'font_families' => array_slice( $fonts, 0, 6 ),
                ),
            )
        );
    }

    /**
     * 실험실 전용 CSS/JS enqueue
     */
    public function enqueue_labs_center_assets( $hook ) {
        // 실험실 페이지에서만 로드
        if ( 'settings_page_jj-labs-center' !== $hook ) {
            return;
        }

        wp_enqueue_media(); // 미디어 업로더 (향후 확장용)
        wp_enqueue_style( 'wp-color-picker' );
        
        // [v3.7.0 '신규'] WordPress CodeMirror for CSS 에디터
        $code_editor_settings = wp_get_code_editor_settings( array( 'type' => 'text/css' ) );
        wp_enqueue_code_editor( $code_editor_settings );
        
        // [Safety] 상수 미정의(로드 순서 문제) 대비
        $base_url = defined( 'JJ_STYLE_GUIDE_URL' ) ? JJ_STYLE_GUIDE_URL : plugin_dir_url( dirname( __FILE__ ) ) . '../';
        $version  = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0';

        $css_url = $base_url . 'assets/css/jj-labs-center.css';
        $js_url  = $base_url . 'assets/js/jj-labs-center.js';

        wp_enqueue_style(
            'jj-labs-center',
            $css_url,
            array( 'wp-color-picker', 'code-editor' ),
            $version
        );

        wp_enqueue_script(
            'jj-labs-center',
            $js_url,
            array( 'jquery', 'wp-color-picker', 'code-editor' ),
            $version,
            true
        );

        // AJAX 파라미터 로컬라이즈
        wp_localize_script(
            'jj-labs-center',
            'jjLabsCenter',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
            )
        );
        
        // 스타일 가이드 에디터 스크립트도 로드 (스타일 저장 기능용)
        wp_enqueue_script(
            'jj-style-guide-editor',
            $base_url . 'assets/js/jj-style-guide-editor.js',
            array( 'jquery', 'wp-color-picker' ),
            $version,
            true
        );
        
        // [v5.0.3] 키보드 단축키 시스템 로드
        wp_enqueue_script( 'jj-keyboard-shortcuts', $base_url . 'assets/js/jj-keyboard-shortcuts.js', array( 'jquery' ), $version, true );
        
        // [v5.0.3] 툴팁 시스템 로드
        wp_enqueue_script( 'jj-tooltips', $base_url . 'assets/js/jj-tooltips.js', array( 'jquery' ), $version, true );
        
        $this->options = (array) get_option( $this->option_key );
        wp_localize_script(
            'jj-style-guide-editor',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'temp_palette_nonce' => wp_create_nonce( 'jj_temp_palette_nonce' ),
                'settings' => $this->options,
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'locale'   => get_locale(), // [v5.0.3] 다국어 지원을 위한 로케일 전달
                'i18n'     => array(
                    'saving' => __( '저장 중...', 'jj-style-guide' ),
                    'saved'  => __( '스타일 저장됨', 'jj-style-guide' ),
                ),
            )
        );
    }

    /**
     * "J&J 실험실" 서브메뉴 추가
     */
    public function add_admin_menu_page() {
        // [v5.5.0] 권한 체크 (Edition Controller)
        $capability = 'manage_options';
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            // 최소 Basic 이상 접근 가능하도록 (실제 기능 제한은 내부에서 처리)
            if ( ! JJ_Edition_Controller::instance()->is_at_least( 'basic' ) ) {
                return;
            }
        }

        $page_title = __( 'J&J 실험실 센터', 'jj-style-guide' );
        $menu_title = __( '실험실 센터', 'jj-style-guide' );

        // 1. 설정 메뉴 하단
        add_options_page(
            $page_title,
            $menu_title,
            $capability,
            'jj-labs-center',
            array( $this, 'render_labs_center_page' )
        );

        // 2. 모양(Appearance) 및 도구(Tools) 메뉴 추가 (접근성 강화)
        add_theme_page( $page_title, $menu_title, $capability, 'jj-labs-center', array( $this, 'render_labs_center_page' ) );
        add_management_page( $page_title, $menu_title, $capability, 'jj-labs-center', array( $this, 'render_labs_center_page' ) );
    }

    /**
     * 실험실 화면 렌더링
     */
    public function render_labs_center_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $this->options = (array) get_option( $this->option_key );
        $options = $this->options;
        
        // [v5.1.6] Master/Partner에서는 업그레이드 유도 UI를 절대 표시하지 않음
        $is_master_version = false;
        $is_partner_or_higher = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $edition = JJ_Edition_Controller::instance();
                $is_master_version = $edition->is_at_least( 'master' );
                $is_partner_or_higher = $edition->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $is_partner_or_higher && defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        }
        $purchase_url = '';
        if ( ! $is_partner_or_higher ) {
            $license_manager = null;
            $purchase_url = 'https://j-j-labs.com'; // 기본값
            if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                if ( class_exists( 'JJ_License_Manager' ) ) {
                    $license_manager = JJ_License_Manager::instance();
                    if ( $license_manager ) {
                        $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                    }
                }
            }
        }
        $labs_options = $options['labs'] ?? array();
        $adapters_config = $this->adapters_config;

        ?>
        <?php if ( ! $is_partner_or_higher && $purchase_url ) : ?>
        <div class="notice notice-info" style="margin: 20px 20px 0 0;">
            <p style="display: flex; align-items: center; gap: 10px;">
                <strong><?php esc_html_e( '더 많은 기능을 사용하려면 업그레이드하세요!', 'jj-style-guide' ); ?></strong>
                <a href="<?php echo esc_url( $purchase_url ); ?>" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="button button-primary" 
                   style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px; font-weight: 600;">
                    <span class="dashicons dashicons-cart" style="margin-top: 4px;"></span> <?php esc_html_e( '업그레이드하기', 'jj-style-guide' ); ?>
                </a>
            </p>
        </div>
        <?php endif; ?>
        <div class="wrap jj-labs-center-wrap">
            <div class="jj-labs-center-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 8px;">
                        <h1 style="margin: 0;"><?php _e( 'J&J 실험실 센터', 'jj-style-guide' ); ?> <span class="version">v<?php echo esc_html( defined('JJ_STYLE_GUIDE_VERSION') ? JJ_STYLE_GUIDE_VERSION : '8.0.0' ); ?></span></h1>
                        <a href="<?php echo esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ); ?>" 
                           class="button button-secondary" 
                           style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <?php _e( '스타일 센터', 'jj-style-guide' ); ?>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ); ?>" 
                           class="button button-secondary" 
                           style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <?php _e( '관리자 센터', 'jj-style-guide' ); ?>
                        </a>
                        <?php
                        // [v5.1.6] 마스터 버전이 아닌 경우 결제 유도 문구 표시
                        if ( ! $is_partner_or_higher && $purchase_url ) {
                            ?>
                            <a href="<?php echo esc_url( $purchase_url ); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="button button-primary" 
                               style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px; font-weight: 600;">
                                <span class="dashicons dashicons-cart" style="margin-top: 4px;"></span> <?php _e( '업그레이드하기', 'jj-style-guide' ); ?>
                            </a>
                            <?php
                        }
                        ?>
                    </div>
                    <p class="description" style="margin: 0;">
                        <span class="jj-labs-tab-description" data-tab-type="scanner" data-tooltip="labs-tab-scanner">
                            <?php _e( '실험실은 아직 공식 지원되지 않는 테마나 플러그인의 스타일을 분석하고 조정하기 위한 고급 도구입니다. CSS/HTML/JS 스캐너와 수동 재정의 기능을 제공합니다.', 'jj-style-guide' ); ?>
                        </span>
                    </p>
                </div>
                <div class="jj-header-actions">
                    <!-- [v4.2.2] 실험실 설정 내보내기/불러오기 -->
                    <button type="button" class="button button-secondary jj-export-settings" data-center="labs-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px; margin-right: 8px;">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php _e( '내보내기', 'jj-style-guide' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-settings" data-center="labs-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span> <?php _e( '불러오기', 'jj-style-guide' ); ?>
                    </button>
                </div>
            </div>

            <!-- [v5.0.0] 실험실 탭 활성화/비활성화 설정 -->
            <form method="post" action="" style="margin-bottom: 20px;">
                <?php wp_nonce_field( 'jj_labs_tabs_save_action' ); ?>
                <div style="padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                    <h3 style="margin-top: 0;"><?php _e( '실험실 탭 활성화/비활성화', 'jj-style-guide' ); ?></h3>
                    <p class="description" style="margin-bottom: 15px;">
                        <?php _e( '실험실의 각 탭을 활성화 또는 비활성화할 수 있습니다.', 'jj-style-guide' ); ?>
                        <span class="jj-tooltip" style="margin-left: 5px;">
                            <span class="dashicons dashicons-editor-help"></span>
                            <span class="jj-tooltip-content">
                                <?php _e( '비활성화된 탭은 실험실에서 숨겨지지만 설정 데이터는 보존됩니다.', 'jj-style-guide' ); ?>
                            </span>
                        </span>
                    </p>
                    
                    <!-- [v5.0.0] 일괄 작업 버튼 -->
                    <div class="jj-bulk-actions" style="margin-bottom: 15px;">
                        <button type="button" class="button button-small jj-bulk-enable-all-labs">
                            <span class="dashicons dashicons-yes-alt" style="font-size: 16px; margin-top: 3px;"></span>
                            <?php _e( '모두 활성화', 'jj-style-guide' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-bulk-disable-all-labs">
                            <span class="dashicons dashicons-dismiss" style="font-size: 16px; margin-top: 3px;"></span>
                            <?php _e( '모두 비활성화', 'jj-style-guide' ); ?>
                        </button>
                    </div>
                    
                    <!-- [v5.0.0] 검색/필터 -->
                    <div class="jj-section-filter" style="margin-bottom: 15px;">
                        <input type="search" 
                               id="jj-labs-tabs-search" 
                               placeholder="<?php esc_attr_e( '탭 검색...', 'jj-style-guide' ); ?>"
                               style="width: 100%; max-width: 400px; padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 3px; font-size: 13px;" />
                    </div>

                    <!-- [v5.0.0] 경고 메시지 -->
                    <div class="notice notice-warning inline jj-labs-tabs-disable-warning" style="display: none; margin: 15px 0;">
                        <p>
                            <strong><?php _e( '주의:', 'jj-style-guide' ); ?></strong>
                            <?php _e( '탭을 비활성화하면 실험실에서 해당 탭이 표시되지 않습니다. 비활성화된 탭의 설정 데이터는 보존되지만 UI에서 접근할 수 없게 됩니다.', 'jj-style-guide' ); ?>
                        </p>
                    </div>

                    <?php
                    $labs_tabs_layout = $this->get_labs_tabs_layout();
                    $labs_tabs_default = $this->get_default_labs_tabs_layout();
                    ?>
                    <table class="form-table" style="margin: 0;">
                        <thead>
                        <tr>
                            <th style="width: 30px;">
                                <input type="checkbox" 
                                       id="jj-select-all-labs-tabs" 
                                       class="jj-select-all-checkbox" 
                                       title="<?php esc_attr_e( '전체 선택/해제', 'jj-style-guide' ); ?>" />
                            </th>
                            <th><?php _e( '탭', 'jj-style-guide' ); ?></th>
                            <th><?php _e( '활성화', 'jj-style-guide' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ( $labs_tabs_default as $tab_slug => $tab_meta ) :
                            $tab_current = isset( $labs_tabs_layout[ $tab_slug ] ) ? $labs_tabs_layout[ $tab_slug ] : $tab_meta;
                            $tab_enabled = ! empty( $tab_current['enabled'] );
                            ?>
                            <tr class="jj-labs-tab-row <?php echo $tab_enabled ? '' : 'jj-tab-row-disabled'; ?>"
                                data-tab-slug="<?php echo esc_attr( $tab_slug ); ?>"
                                data-tab-label="<?php echo esc_attr( strtolower( $tab_meta['label'] ) ); ?>">
                                <td style="width: 30px; text-align: center; padding: 8px 0;">
                                    <input type="checkbox" 
                                           class="jj-labs-tab-checkbox" 
                                           data-tab="<?php echo esc_attr( $tab_slug ); ?>"
                                           <?php checked( $tab_enabled ); ?> />
                                </td>
                                <th scope="row" style="padding: 8px 0; font-weight: normal;">
                                    <?php echo esc_html( $tab_meta['label'] ); ?>
                                    <?php if ( ! $tab_enabled ) : ?>
                                        <span class="jj-disabled-icon" title="<?php esc_attr_e( '비활성화됨', 'jj-style-guide' ); ?>"></span>
                                    <?php else : ?>
                                        <span class="jj-enabled-icon" title="<?php esc_attr_e( '활성화됨', 'jj-style-guide' ); ?>"></span>
                                    <?php endif; ?>
                                    <div style="font-size:10px;color:#999;margin-top:2px;">
                                        <code><?php echo esc_html( $tab_slug ); ?></code>
                                    </div>
                                </th>
                                <td style="padding: 8px 0;">
                                    <label>
                                        <input type="checkbox"
                                               name="jj_labs_tabs_layout[<?php echo esc_attr( $tab_slug ); ?>][enabled]"
                                               value="1"
                                               class="jj-labs-tab-enabled-checkbox"
                                               data-tab="<?php echo esc_attr( $tab_slug ); ?>"
                                            <?php checked( $tab_enabled ); ?> />
                                        <?php _e( '활성화', 'jj-style-guide' ); ?>
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p style="margin-top: 15px; margin-bottom: 0;">
                        <button type="submit" class="button button-primary">
                            <?php _e( '저장', 'jj-style-guide' ); ?>
                        </button>
                    </p>
                </div>
            </form>

            <div class="jj-labs-center-tabs-container">
                <ul class="jj-labs-center-tabs">
                    <?php 
                    // [v5.0.0] 탭 활성화/비활성화 체크
                    $tab_enabled_scanner = ! empty( $labs_tabs_layout['scanner']['enabled'] ?? true );
                    $tab_enabled_overrides = ! empty( $labs_tabs_layout['overrides']['enabled'] ?? true );
                    $tab_enabled_supported = ! empty( $labs_tabs_layout['supported']['enabled'] ?? true );
                    
                    // 활성화된 탭이 하나도 없으면 스캐너 탭을 기본으로 활성화
                    $has_enabled_tabs = $tab_enabled_scanner || $tab_enabled_overrides || $tab_enabled_supported;
                    if ( ! $has_enabled_tabs ) {
                        $tab_enabled_scanner = true;
                    }
                    
                    $first_enabled_tab = $tab_enabled_scanner ? 'scanner' : ( $tab_enabled_overrides ? 'overrides' : 'supported' );
                    ?>
                    <?php if ( $tab_enabled_scanner ) : ?>
                    <li class="<?php echo ( $first_enabled_tab === 'scanner' ) ? 'active' : ''; ?>" data-tab="scanner">
                        <a href="#scanner"><?php _e( '스캐너', 'jj-style-guide' ); ?></a>
                    </li>
                    <?php endif; ?>
                    <?php if ( $tab_enabled_overrides ) : ?>
                    <li class="<?php echo ( $first_enabled_tab === 'overrides' ) ? 'active' : ''; ?>" data-tab="overrides">
                        <a href="#overrides"><?php _e( '수동 재정의', 'jj-style-guide' ); ?></a>
                    </li>
                    <?php endif; ?>
                    <?php if ( $tab_enabled_supported ) : ?>
                    <li class="<?php echo ( $first_enabled_tab === 'supported' ) ? 'active' : ''; ?>" data-tab="supported">
                        <a href="#supported"><?php _e( '공식 지원 목록', 'jj-style-guide' ); ?></a>
                    </li>
                    <?php endif; ?>
                </ul>

                <!-- 스캐너 탭 -->
                <?php if ( $tab_enabled_scanner ) : ?>
                <div class="jj-labs-center-tab-content <?php echo ( $first_enabled_tab === 'scanner' ) ? 'active' : ''; ?>" data-tab="scanner">
                    <?php
                    $options = $this->options;
                    include JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-labs-scanner.php';
                    ?>
                </div>
                <?php endif; ?>

                <!-- 수동 재정의 탭 -->
                <?php if ( $tab_enabled_overrides ) : ?>
                <div class="jj-labs-center-tab-content <?php echo ( $first_enabled_tab === 'overrides' ) ? 'active' : ''; ?>" data-tab="overrides">
                    <?php
                    $options = $this->options;
                    include JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-labs-overrides.php';
                    ?>
                </div>
                <?php endif; ?>

                <!-- 공식 지원 목록 탭 -->
                <?php if ( $tab_enabled_supported ) : ?>
                <div class="jj-labs-center-tab-content <?php echo ( $first_enabled_tab === 'supported' ) ? 'active' : ''; ?>" data-tab="supported">
                    <?php
                    // 실험실 섹션의 공식 지원 목록 부분만 재사용
                    $themes_config_raw = $adapters_config['themes'] ?? array();
                    $spokes_config_raw = $adapters_config['spokes'] ?? array();
                    include JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-labs-supported-list.php';
                    ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- [v4.2.2] 실험실 설정 내보내기/불러오기 (하단) -->
            <div class="jj-labs-center-footer" style="margin-top: 30px; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <button type="button" class="button button-secondary jj-export-settings" data-center="labs-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px; margin-right: 8px;">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php _e( '내보내기', 'jj-style-guide' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-settings" data-center="labs-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span> <?php _e( '불러오기', 'jj-style-guide' ); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * [v5.0.0] 실험실 탭 레이아웃 기본값
     *
     * @return array
     */
    public function get_default_labs_tabs_layout() {
        return array(
            'scanner'   => array(
                'label'   => __( '스캐너', 'jj-style-guide' ),
                'enabled' => 1,
            ),
            'overrides' => array(
                'label'   => __( '수동 재정의', 'jj-style-guide' ),
                'enabled' => 1,
            ),
            'supported' => array(
                'label'   => __( '공식 지원 목록', 'jj-style-guide' ),
                'enabled' => 1,
            ),
        );
    }
    
    /**
     * [v5.0.0] 저장된 실험실 탭 레이아웃 + 기본값 병합
     * [v5.0.0] 성능 최적화: static 캐싱 추가
     *
     * @return array
     */
    public function get_labs_tabs_layout() {
        // [v5.0.0] 캐시된 값이 있으면 반환
        if ( self::$labs_tabs_layout_cache !== null ) {
            return self::$labs_tabs_layout_cache;
        }
        
        $stored   = (array) get_option( $this->labs_tabs_option_key );
        $defaults = $this->get_default_labs_tabs_layout();
        $result   = array();

        foreach ( $defaults as $slug => $meta ) {
            $enabled = isset( $stored[ $slug ]['enabled'] ) ? (bool) $stored[ $slug ]['enabled'] : (bool) ( $meta['enabled'] ?? true );
            $result[ $slug ] = array(
                'label'   => $meta['label'],
                'enabled' => $enabled ? 1 : 0,
            );
        }

        // [v5.0.0] 캐시에 저장
        self::$labs_tabs_layout_cache = $result;
        
        return $result;
    }
    
    /**
     * 실험실 탭 레이아웃 캐시 플러시
     * [v5.0.0] 성능 최적화: 옵션이 업데이트되면 호출하여 캐시를 무효화
     *
     * @return void
     */
    public static function flush_labs_tabs_layout_cache() {
        self::$labs_tabs_layout_cache = null;
    }
    
    /**
     * [v5.0.0] 실험실 탭 활성화/비활성화 저장
     */
    public function save_labs_tabs_layout() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        // 실험실 페이지에서만 저장
        if ( ! isset( $_GET['page'] ) || 'jj-labs-center' !== $_GET['page'] ) {
            return;
        }
        
        if ( ! isset( $_POST['jj_labs_tabs_layout'] ) || ! isset( $_POST['_wpnonce'] ) ) {
            return;
        }
        
        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'jj_labs_tabs_save_action' ) ) {
            return;
        }
        
        $raw_tabs   = isset( $_POST['jj_labs_tabs_layout'] ) && is_array( $_POST['jj_labs_tabs_layout'] ) ? wp_unslash( $_POST['jj_labs_tabs_layout'] ) : array();
        $clean_tabs = array();
        $defaults   = $this->get_default_labs_tabs_layout();
        
        foreach ( $defaults as $slug => $meta ) {
            $enabled = isset( $raw_tabs[ $slug ]['enabled'] ) && '1' === $raw_tabs[ $slug ]['enabled'];
            $clean_tabs[ $slug ] = array(
                'enabled' => $enabled ? 1 : 0,
            );
        }
        
        update_option( $this->labs_tabs_option_key, $clean_tabs );
        
        // [v5.0.0] 캐시 플러시
        self::flush_labs_tabs_layout_cache();
    }
}


```

```php
<?php
/**
 * Plugin Name:       ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager (Master)
 * Plugin URI:        https://j-j-labs.com
 * Description:       WordPress 웹사이트의 모든 스타일 요소(색상 팔레트, 타이포그래피, 버튼, 폼)를 중앙에서 일관되게 관리하는 통합 스타일 관리 플러그인입니다. Free 버전은 기본적인 스타일 관리 기능을 제공하며, 브랜드 일관성을 유지하고 디자인 시스템을 효율적으로 운영할 수 있습니다. Pro 버전 플러그인을 함께 설치하면 Basic, Premium, Unlimited 기능을 사용할 수 있습니다. WordPress Customizer와 완벽 통합되어 실시간 미리보기와 함께 직관적인 스타일 관리가 가능합니다.
 * Version:           8.0.0
 * Author:            3J Labs
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://poetryflow.blog
 * Text Domain:       acf-css-really-simple-style-management-center
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ⚠️ 중요: Git 경로와 로컬 경로는 완전히 별개입니다
 * 
 * - 로컬 작업 경로: C:\Users\computer\OneDrive\Desktop\클라우드 백업\OneDrive\문서\jj-css-premium
 * - Git worktree 경로: C:\Users\computer\.cursor\worktrees\jj-css-premium\tci
 * - 두 경로는 서로 독립적이며, Git에서 작업한 내용은 반드시 로컬 경로에 동기화해야 함
 * - 자세한 내용은 DEVELOPMENT_PRINCIPLES.md의 "Git 및 로컬 경로 관리 원칙" 섹션 참조
 */

// 플러그인 상수 정의
// [v5.1.6] 전면 재검토 및 오류 방지: 안전한 파일 로더 추가, 모든 버전의 require_once 안전 처리, 결제 유도 문구 추가, 플러그인 목록 페이지 바로가기 링크 추가
// [v5.1.6] Comprehensive review and error prevention: Safe file loader added, all versions' require_once safely handled, purchase prompts added, plugin list page quick links added
// [v1.0.2] 모든 버전 플러그인 활성화 안전성 최종 확보, WordPress 함수 호출 안전 처리
define( 'JJ_STYLE_GUIDE_VERSION', '8.0.0' ); // [v7.0.0] Operation Deep Dive - Stability & Reliability Overhaul

// WordPress 함수가 로드되었는지 확인 후 상수 정의
if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    if ( function_exists( 'plugin_dir_path' ) ) {
        define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 직접 경로 계산
        define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
    }
}

if ( ! defined( 'JJ_STYLE_GUIDE_URL' ) ) {
    if ( function_exists( 'plugin_dir_url' ) ) {
        define( 'JJ_STYLE_GUIDE_URL', plugin_dir_url( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 기본값 사용 (나중에 업데이트됨)
        define( 'JJ_STYLE_GUIDE_URL', '' );
    }
}
define( 'JJ_STYLE_GUIDE_OPTIONS_KEY', 'jj_style_guide_options' );
define( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY', 'jj_style_guide_temp_options' );
define( 'JJ_STYLE_GUIDE_ADMIN_TEXT_KEY', 'jj_style_guide_admin_texts' );
define( 'JJ_STYLE_GUIDE_PAGE_SLUG', 'acf-css-really-simple-style-guide' );
define( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY', 'jj_style_guide_cockpit_page_id' );
define( 'JJ_STYLE_GUIDE_DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
// Pro 버전 활성화 코드 확인 (plugins_loaded 훅에서 처리)
// WordPress가 로드되기 전에는 기본값 사용
define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', 'MASTER' ); // 기본값, plugins_loaded에서 업데이트됨
define( 'JJ_STYLE_GUIDE_EDITION', 'master' ); // 기본 에디션은 free

// [v3.8.0] 환경 구분
if ( ! defined( 'JJ_STYLE_GUIDE_ENV' ) ) {
    define( 'JJ_STYLE_GUIDE_ENV', 'live' );
}

// [v5.3.7] Free 버전은 독립적으로 작동합니다. Master 버전 의존성 제거
// Free 버전은 자체 includes 폴더에 필요한 모든 파일을 포함합니다.

// [v5.4.1] 안전한 파일 로더 적용
$safe_loader = null;
try {
    $safe_loader_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-loader.php';
    if ( file_exists( $safe_loader_path ) ) {
        require_once $safe_loader_path;
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'instance' ) ) {
            $safe_loader = JJ_Safe_Loader::instance();
        }
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Failed: ' . $e->getMessage() );
} catch ( Error $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Fatal Error: ' . $e->getMessage() );
}

// 라이센스 매니저 로드
if ( $safe_loader ) {
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php', false );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-version-features.php', true );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-code-integrity-monitor.php', true );
}

// 핵심 로직 파일 로드 (Master 전용 파일은 아직 비활성화)
try {
    if ( $safe_loader && method_exists( $safe_loader, 'safe_require' ) ) {
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-handler.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-logger.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-admin-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-backup-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/visual-command-center/class-jj-visual-command-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/ai-style-intelligence/class-jj-smart-palette.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-cloud-manager.php', true );
        // [Phase 3] 템플릿 마켓
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-template-market.php', true );
        // [Phase 5] Partner Hub
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/partner-hub/class-jj-partner-hub.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-labs-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-selector-registry.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-injector.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-options-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-edition-controller.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-theme-metadata.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-0-customizer.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-1-css-vars.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-2-php-filters.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-3-dequeue.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-sync.php', false );
        // [Phase 5.2] REST API (설정 조회/업데이트)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/api/class-jj-style-guide-rest-api.php', true );
        // [Phase 5.3] 확장 플러그인 시스템 (Extension API)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/interface-jj-style-guide-extension.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/class-jj-extension-manager.php', true );
        // [Phase 5.2] Webhooks (자동화)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/integrations/class-jj-webhook-manager.php', true );
        // [Phase 5 B] Multisite Network Control
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/multisite/class-jj-multisite-controller.php', true );
        
        // Master 전용 파일들 (일단 주석 처리)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-sync-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-memory-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-mode.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-optimizer.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-issuer.php', false );
    } else {
        // Fallback for Safe Loader missing
        require_once JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
        // ... (Fallback은 일단 생략, Safe Loader가 있다고 가정)
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Master Load Error: ' . $e->getMessage() );
}



/**
 * 활성화 훅 래퍼 함수 (치명적 오류 방지)
 * [v5.1.7] 전체 함수를 try-catch로 감싸고 상수 정의 확인 추가
 */
function jj_simple_style_guide_master_activate() {
    try {
        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // 상수가 정의되지 않은 경우 기본 경로 사용
            if ( function_exists( 'plugin_dir_path' ) ) {
                define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
            } else {
                define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
            }
        }
        
        // 활성화 매니저 클래스가 로드되지 않은 경우 로드 시도
        
        if ( ! class_exists( 'JJ_Activation_Manager' ) ) {
            $activation_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php';
            if ( file_exists( $activation_file ) ) {
                require_once $activation_file;
            } else {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 파일을 찾을 수 없습니다. 기본 활성화만 수행합니다.' );
                }
                return;
            }
        }
        
        // 클래스가 존재하는지 확인 후 활성화 실행
        if ( class_exists( 'JJ_Activation_Manager' ) && method_exists( 'JJ_Activation_Manager', 'activate' ) ) {
            try {
                JJ_Activation_Manager::activate();
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 오류 발생 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 치명적 오류 발생 - ' . $e->getMessage() );
                }
            }
        } else {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 클래스를 사용할 수 없습니다.' );
            }
        }
    } catch ( Exception $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 예외 발생 - ' . $e->getMessage() );
        }
    } catch ( Error $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 치명적 오류 발생 - ' . $e->getMessage() );
        }
    }
}
register_activation_hook( __FILE__, 'jj_simple_style_guide_master_activate' );

/**
 * Free 버전 전용 메인 컨트롤 클래스
 */
final class JJ_Simple_Style_Guide_Master {

    private static $instance = null;
    private $options = array();
    private $cockpit_page_id = 0;
    private $options_key = '';
    private $brand_colors = array();
    private $environment_map = array();
    private $adapters_config = array();
    private $license_manager = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 상수 정의 확인 후 할당
        if ( defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) {
            $this->options_key = JJ_STYLE_GUIDE_OPTIONS_KEY;
        }
        
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $this->license_manager = JJ_License_Manager::instance();
        }
        
        if ( defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) && function_exists( 'get_option' ) ) {
            $this->cockpit_page_id = (int) get_option( JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY );
        }

        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // [v5.3.7] Free 버전은 자체 config 폴더에서 로드
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
            if ( file_exists( $config_file ) ) {
                $this->adapters_config = include( $config_file );
                // [v5.5.0] Edition Controller를 통한 동적 어댑터 필터링
                if ( class_exists( 'JJ_Edition_Controller' ) ) {
                    $this->adapters_config = JJ_Edition_Controller::instance()->filter_adapters( $this->adapters_config );
                }
                
                
            }
        }

        // WordPress 함수가 로드된 후에만 훅 등록
        // [v5.1.9] plugin_basename() 안전 호출 추가
        // [v5.3.9] 모든 훅 등록을 조건문 안으로 이동하여 안전성 확보
        if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) && function_exists( 'plugin_basename' ) ) {
            add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
            add_action( 'admin_menu', array( $this, 'add_admin_menu_links' ) );
            $plugin_basename = function_exists( 'plugin_basename' ) ? plugin_basename( __FILE__ ) : basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );
            add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_plugin_settings_link' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            add_action( 'wp_ajax_jj_save_style_guide', array( $this, 'save_settings_ajax' ) );
            add_action( 'wp_ajax_jj_apply_temp_palette', array( $this, 'apply_temp_palette_ajax' ) );
            add_action( 'wp_ajax_jj_save_temp_palette', array( $this, 'save_temp_palette_ajax' ) );
            // [v3.8.0] Customizer 색상 새로고침 (팔레트 동기화)
            if ( class_exists( 'JJ_Customizer_Sync' ) ) {
                add_action( 'wp_ajax_jj_load_current_colors', array( 'JJ_Customizer_Sync', 'ajax_load_current_colors' ) );
            }
            // [v3.8.0] 섹션별 내보내기/불러오기 (팔레트 등)
            add_action( 'wp_ajax_jj_export_section', array( $this, 'ajax_export_section' ) );
            add_action( 'wp_ajax_jj_import_section', array( $this, 'ajax_import_section' ) );
            add_action( 'init', array( $this, 'load_options_and_init_strategies' ), 5 );
            add_action( 'init', array( $this, 'init_sync_manager' ), 10 );
            
            
            if ( JJ_STYLE_GUIDE_DEBUG ) {
                add_action( 'wp_footer', array( $this, 'render_debug_info' ), 999 );
                add_action( 'admin_footer', array( $this, 'render_debug_info' ), 999 );
            }
        }

        // Customizer 통합 (제한적)
        
        try {
            $customizer_manager_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php';
            if ( file_exists( $customizer_manager_path ) ) {
                require_once $customizer_manager_path;
                if ( class_exists( 'JJ_Customizer_Manager' ) ) {
                    $customizer_manager = JJ_Customizer_Manager::instance();
                    if ( method_exists( $customizer_manager, 'init' ) ) {
                        $customizer_manager->init();
                    }
                }
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }

    }

    /**
     * Free 버전: 어댑터 제한
     * 
     * @param array $config
     * @return array
     */
    private function limit_adapters_for_free( $config ) {
        // Free 버전: 테마 2개만 지원 (Twenty Twenty-Four, Twenty Twenty-Five)
        $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
        if ( isset( $config['themes'] ) && is_array( $config['themes'] ) ) {
            $config['themes'] = array_intersect_key( $config['themes'], array_flip( $allowed_themes ) );
        }

        // Free 버전: 플러그인 1개만 지원 (WooCommerce)
        $allowed_plugins = array( 'woocommerce' );
        if ( isset( $config['spokes'] ) && is_array( $config['spokes'] ) ) {
            $config['spokes'] = array_intersect_key( $config['spokes'], array_flip( $allowed_plugins ) );
        }

        return $config;
    }

    /**
     * Free 버전 워터마크 추가
     */
    public function add_free_watermark() {
        if ( ! is_admin() && current_user_can( 'manage_options' ) ) {
            echo '<div style="position: fixed; bottom: 10px; right: 10px; background: rgba(0, 0, 0, 0.7); color: #fff; padding: 8px 12px; font-size: 11px; border-radius: 4px; z-index: 99999; font-family: Arial, sans-serif;">';
            echo '<a href="' . esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ) . '" style="color: #fff; text-decoration: none;">JJ Style Guide Free</a>';
            echo '</div>';
        }
    }

    /**
     * 업그레이드 프롬프트 표시
     */
    public function show_upgrade_prompt() {
        // [Safety Lock] MASTER 또는 PARTNER 버전은 업그레이드 프롬프트 표시 안 함
        if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'partner' ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        $license_manager = null;
        $upgrade_url = 'https://j-j-labs.com'; // 기본값
        
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
                if ( $license_manager ) {
                    $upgrade_url = $license_manager->get_purchase_url( 'upgrade' );
                }
            }
        }
        ?>
        <div class="notice notice-info is-dismissible" style="background: #f0f6fc; border-left-color: #2271b1;">
            <p style="font-size: 14px; line-height: 1.8;">
                <strong><?php _e( '현재 버전을 사용 중입니다.', 'acf-css-really-simple-style-management-center' ); ?></strong><br>
                <?php _e( '더 많은 기능을 이용하려면 업그레이드하세요:', 'acf-css-really-simple-style-management-center' ); ?>
                <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" class="button button-primary" style="margin-left: 10px;">
                    <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
            </p>
            <ul style="margin: 10px 0; padding-left: 20px; list-style: disc;">
                <li><?php _e( '모든 팔레트 사용 가능 (브랜드, 시스템, 얼터너티브, 어나더, 임시)', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '모든 테마 및 플러그인 어댑터 지원', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '관리자 센터 및 실험실 센터 전체 기능', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '섹션별 내보내기/불러오기', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '고급 색상 선택 기능 (스포이드, 팔레트 불러오기)', 'acf-css-really-simple-style-management-center' ); ?></li>
            </ul>
        </div>
        <?php
    }

    public function init_sync_manager() {
        add_action( 'customize_save_after', array( $this, 'sync_from_customizer' ) );
        JJ_Sync_Manager::instance()->init( $this->options, $this->cockpit_page_id );
    }

    /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * Free 버전: 스타일 센터만 표시
     * [v5.1.9] 안전성 강화 - try-catch 추가
     */
            /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * [Phase 4.99] 퀵 링크 확장: 설정 | 스타일 | 메뉴 | 비주얼
     */
    public function add_plugin_settings_link( $links ) {
        try {
            if ( ! function_exists( 'admin_url' ) || ! function_exists( 'esc_url' ) ) {
                return $links;
            }
            
            $new_links = array();
            
            // 1. 설정 (Admin Center 메인)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ) . '" style="font-weight: 600;">' . __( 'Settings', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 2. 스타일 (Style Guide)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ) ) . '">' . __( 'Styles', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 3. 어드민 메뉴 (Admin Menu Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#admin-menu' ) ) . '">' . __( 'Menu', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 4. 비주얼 (Visual Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#visual' ) ) . '">' . __( 'Visual', 'acf-css-really-simple-style-management-center' ) . '</a>';

            // 기존 링크와 병합
            return array_merge( $new_links, $links );
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 오류 - ' . $e->getMessage() );
            }
            return $links;
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 치명적 오류 - ' . $e->getMessage() );
            }
            return $links;
        }
    }

    public function add_admin_menu_page() {
        add_options_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );
    }

    public function add_admin_menu_links() {
        // [Phase 4.5/4.99] "모양/도구/설정" 어디서나 동일한 화면으로 진입할 수 있도록 서브메뉴 추가
        add_theme_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        add_management_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        if ( $this->cockpit_page_id ) {
            add_submenu_page(
                'themes.php',
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                'manage_options',
                'post.php?post=' . $this->cockpit_page_id . '&action=edit',
                '',
                100
            );
        }
    }

    public function enqueue_admin_scripts( $hook_suffix ) {
        $is_settings_page = in_array(
            $hook_suffix,
            array(
                'settings_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'appearance_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'tools_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
            ),
            true
        );

        $is_cockpit_page = false;
        if ( 'post.php' === $hook_suffix ) {
            global $post;
            if ( $post && (int) $post->ID === $this->cockpit_page_id ) {
                $is_cockpit_page = true;
            }
        }

        if ( ! $is_settings_page && ! $is_cockpit_page ) {
            return;
        }

        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;

        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        // [v5.3.7] Free 버전은 자체 assets 폴더에서 로드
        wp_enqueue_style( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-editor.css', array( 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION );
        wp_enqueue_script( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-editor.js', array( 'jquery', 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION, true );

        $preview_page_id = $this->cockpit_page_id;
        $preview_url = $preview_page_id ? get_permalink( $preview_page_id ) : '';

        wp_localize_script(
            'jj-style-guide-editor',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'temp_palette_nonce' => wp_create_nonce( 'jj_temp_palette_nonce' ),
                'settings' => $this->options,
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'edition'  => 'free', // Free 버전 표시
                'i18n'     => array(
                    'saving' => __( '저장 중...', 'acf-css-really-simple-style-management-center' ),
                    'saved'  => __( '스타일 저장됨', 'acf-css-really-simple-style-management-center' ),
                    'applied' => __( '임시 팔레트 적용됨! \'스타일 저장\'을 눌러야 최종 반영됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'error'  => __( '오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
                    'confirm_apply' => __( '정말로 \'임시 팔레트\'의 값으로 덮어쓰시겠습니까?\n이 작업은 즉시 UI에 반영되지만, \n\'스타일 저장\' 버튼을 눌러야 최종 저장됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'preview_url' => $preview_url ? esc_url( $preview_url ) : '',
                    'preview_page_id' => $preview_page_id,
                ),
            )
        );
    }

    public function render_style_guide_page() {
        try {
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options      = $options;
            $temp_options       = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY );
            $options            = $this->options;
            $cockpit_page_id    = $this->cockpit_page_id;
            $preview_url        = $cockpit_page_id ? get_permalink( $cockpit_page_id ) : '';

        // 옵션이 비어있으면 기본값 설정
        if ( empty( $this->options ) ) {
            $this->options = array(
                'palettes' => array(
                    'brand' => array(
                        'primary_color' => '',
                        'primary_color_hover' => '',
                        'secondary_color' => '',
                        'secondary_color_hover' => '',
                    ),
                    'system' => array(), // Free 버전: 시스템 팔레트 비활성화
                    'alternative' => array(), // Free 버전: 얼터너티브 팔레트 비활성화
                    'another' => array(), // Free 버전: 어나더 팔레트 비활성화
                ),
                'typography' => array(),
                'buttons' => array(),
                'forms' => array(),
                'contexts' => array(),
            );
            $options = $this->options;
        }
        ?>
        <div id="jj-style-guide-wrapper" class="wrap jj-style-guide-wrap" style="background: #f0f0f1; margin: 0; padding: 0;">
            <form id="jj-style-guide-form">

                <div class="jj-style-guide-header" style="top: 32px; background: #fff; padding: 15px 25px; border-bottom: 1px solid #c3c4c7; z-index: 999; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h1 style="margin: 0; font-size: 24px;">
                            <?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?> (v<?php echo esc_html( JJ_STYLE_GUIDE_VERSION ); ?>) 
                            <?php if ( ! class_exists("JJ_Edition_Controller") || JJ_Edition_Controller::instance()->get_current_edition() === "free" ) : ?><span style="background: #d63638; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 14px; font-weight: 600;">Free</span><?php endif; ?>
                        </h1>
                        <?php
                        $license_manager = null;
                        $purchase_url = 'https://j-j-labs.com'; // 기본값
                        
                        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                            if ( class_exists( 'JJ_License_Manager' ) ) {
                                $license_manager = JJ_License_Manager::instance();
                                if ( $license_manager ) {
                                    $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                                }
                            }
                        }
                        ?>
                        <?php if ( ! class_exists("JJ_Edition_Controller") || ! JJ_Edition_Controller::instance()->is_at_least("partner") ) : ?><a href="<?php echo esc_url( $purchase_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary" style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px;">
                            <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                        </a><?php endif; ?>
                        <?php if ( $preview_url ) : ?>
                        <button type="button" 
                                class="button button-secondary" 
                                id="jj-preview-button"
                                style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> <?php _e( '실시간 미리보기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="jj-header-actions" style="display: flex; align-items: center; gap: 10px;">
                        <span id="jj-save-success" style="display: none; vertical-align: middle; color: #2271b1; font-weight: 600; margin-right: 15px;"></span>
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-save-style-guide" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
                <div class="jj-style-guide-content" style="margin: 20px auto; background: #ffffff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 1px;">
                    <?php
                    // Free 버전: 제한된 섹션만 표시
                    // [v5.3.7] Free 버전은 자체 includes/editor-views 폴더에서 로드
                    $sections_map = array(
                        'colors'     => 'includes/editor-views/view-section-colors.php',
                        'typography' => 'includes/editor-views/view-section-typography.php',
                        'buttons'    => 'includes/editor-views/view-section-buttons.php',
                        'forms'      => 'includes/editor-views/view-section-forms.php',
                    );

                    // Free 버전: 섹션 순서 고정
                    $sections_to_render = array();
                    foreach ( $sections_map as $slug => $relative_path ) {
                        $sections_to_render[] = array(
                            'slug' => $slug,
                            'file' => JJ_STYLE_GUIDE_PATH . $relative_path,
                            'order'=> 0, // 순서는 UI에서만 표시
                        );
                    }
                    ?>
                    <div id="jj-sections-sortable" class="jj-sections-sortable">
                        <?php
                        foreach ( $sections_to_render as $section ) {
                            // Free 버전: 기본 뷰 파일 사용 (제한 로직은 뷰 파일 내부에서 처리)
                            $default_file = JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-section-' . $section['slug'] . '.php';
                            if ( file_exists( $section['file'] ) ) {
                                echo '<div class="jj-section-wrapper" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $section['file'];
                                echo '</div>';
                            } elseif ( file_exists( $default_file ) ) {
                                // Free 버전 전용 뷰가 없으면 기본 뷰 사용 (제한 로직 적용 필요)
                                echo '<div class="jj-section-wrapper jj-section-free-version" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $default_file;
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="jj-style-guide-footer" style="margin: 20px auto; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: #666; font-size: 13px;">
                        <?php
                        // Master/Partner에서는 업그레이드 유도 문구를 절대 노출하지 않음
                        $show_upgrade_footer = true;
                        if ( class_exists( 'JJ_Edition_Controller' ) ) {
                            try {
                                $show_upgrade_footer = ! JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                            } catch ( Exception $e ) {
                                // ignore
                            } catch ( Error $e ) {
                                // ignore
                            }
                        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
                            $show_upgrade_footer = ! in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
                        }

                        if ( $show_upgrade_footer ) {
                            _e( '더 많은 기능을 이용하려면 업그레이드하세요.', 'acf-css-really-simple-style-management-center' );
                        }
                        ?>
                    </div>
                    <div style="text-align: right;">
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-style-guide-footer-button" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        } catch ( Exception $e ) {
            // 오류 발생 시에도 기본 페이지 구조는 표시
            ?>
            <div class="wrap" style="padding: 20px;">
                <h1><?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?></h1>
                <div class="notice notice-error">
                    <p><strong><?php _e( '오류가 발생했습니다:', 'acf-css-really-simple-style-management-center' ); ?></strong> <?php echo esc_html( $e->getMessage() ); ?></p>
                </div>
            </div>
            <?php
        }
    }

    // 나머지 메서드들은 dev 버전과 동일하지만, 기능 제한 로직 추가 필요
    // save_settings_ajax, apply_temp_palette_ajax, save_temp_palette_ajax 등
    
    public function save_settings_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        if ( ! isset( $_POST['settings'] ) || ! is_array( $_POST['settings'] ) ) {
            wp_send_json_error( array( 'message' => __( '잘못된 데이터입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // [Phase 5 B] 멀티사이트 네트워크 전용 모드에서는 사이트별 저장 차단
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $ms = JJ_Multisite_Controller::instance();
                if ( method_exists( $ms, 'is_enabled' ) && method_exists( $ms, 'allow_site_override' ) ) {
                    if ( $ms->is_enabled() && ! $ms->allow_site_override() ) {
                        wp_send_json_error( array( 'message' => __( '이 사이트는 네트워크에서 스타일을 관리 중입니다. 네트워크 관리자에서 변경해주세요.', 'acf-css-really-simple-style-management-center' ) ) );
                    }
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        $old_settings = array();
        if ( function_exists( 'get_option' ) ) {
            $old_settings = (array) get_option( $this->options_key, array() );
        }

        $raw_settings = wp_unslash( $_POST['settings'] );
        
        // 에디션별 저장 제한 (Free만 제한, Partner/Master는 무제한)
        $current_edition = 'free';
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $current_edition = (string) JJ_Edition_Controller::instance()->get_current_edition();
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_EDITION' ) ) {
            $current_edition = (string) JJ_STYLE_GUIDE_EDITION;
        }

        if ( 'free' === $current_edition ) {
            $raw_settings = $this->filter_settings_for_free( $raw_settings );
        }
        
        $sanitized_settings = $this->sanitize_options_recursive( $raw_settings );
        update_option( $this->options_key, $sanitized_settings );

        // [Phase 5.2] Webhook/자동화용 이벤트
        if ( function_exists( 'do_action' ) ) {
            do_action( 'jj_style_guide_settings_updated', $sanitized_settings, $old_settings, 'style_center_ajax' );
        }

        $this->sync_to_spokes( $sanitized_settings );

        JJ_CSS_Cache::instance()->flush();
        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * Free 버전: 저장 가능한 설정 필터링
     */
    private function filter_settings_for_free( $settings ) {
        // Free 버전: 시스템 팔레트, 얼터너티브 팔레트, 어나더 팔레트 제거
        if ( isset( $settings['palettes'] ) ) {
            unset( $settings['palettes']['system'] );
            unset( $settings['palettes']['alternative'] );
            unset( $settings['palettes']['another'] );
        }
        
        // Free 버전: 타이포그래피 H5, H6 제거
        if ( isset( $settings['typography'] ) ) {
            unset( $settings['typography']['h5'] );
            unset( $settings['typography']['h6'] );
        }
        
        // Free 버전: Secondary, Text 버튼 제거
        if ( isset( $settings['buttons'] ) ) {
            unset( $settings['buttons']['secondary'] );
            unset( $settings['buttons']['text'] );
        }
        
        return $settings;
    }

    public function apply_temp_palette_ajax() {
        check_ajax_referer( 'jj_temp_palette_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 기능 허용 여부(버전 기능/에디션)
        $can_use_temp = false;
        if ( class_exists( 'JJ_Version_Features' ) ) {
            try {
                $vf = JJ_Version_Features::instance();
                $can_use_temp = $vf ? $vf->can_use_palette( 'temp' ) : false;
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $can_use_temp = JJ_Edition_Controller::instance()->is_at_least( 'premium' ) || JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $can_use_temp = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }

        if ( ! $can_use_temp ) {
            $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
            if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
                $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
            }
            wp_send_json_error( array( 'message' => $message ) );
        }

        $target_palette = isset( $_POST['target_palette'] ) ? sanitize_key( wp_unslash( $_POST['target_palette'] ) ) : 'brand';
        if ( 'brand' !== $target_palette ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 대상 팔레트입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
        $temp_palettes = isset( $temp_options['palettes'] ) && is_array( $temp_options['palettes'] ) ? $temp_options['palettes'] : array();
        $temp_brand = isset( $temp_palettes['brand'] ) && is_array( $temp_palettes['brand'] ) ? $temp_palettes['brand'] : array();

        if ( empty( $temp_brand ) ) {
            wp_send_json_error( array( 'message' => __( '임시 팔레트에 저장된 값이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        if ( ! isset( $options['palettes'] ) || ! is_array( $options['palettes'] ) ) {
            $options['palettes'] = array();
        }
        if ( ! isset( $options['palettes']['brand'] ) || ! is_array( $options['palettes']['brand'] ) ) {
            $options['palettes']['brand'] = array();
        }

        $options['palettes']['brand'] = array_merge( (array) $options['palettes']['brand'], (array) $temp_brand );
        update_option( $this->options_key, $options );

        if ( class_exists( 'JJ_CSS_Cache' ) && method_exists( 'JJ_CSS_Cache', 'instance' ) ) {
            try {
                JJ_CSS_Cache::instance()->flush();
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        wp_send_json_success( array(
            'message' => __( '임시 팔레트가 브랜드 팔레트에 적용되었습니다.', 'acf-css-really-simple-style-management-center' ),
            'new_settings' => $options,
        ) );
    }

    public function save_temp_palette_ajax() {
        check_ajax_referer( 'jj_temp_palette_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 기능 허용 여부(버전 기능/에디션)
        $can_use_temp = false;
        if ( class_exists( 'JJ_Version_Features' ) ) {
            try {
                $vf = JJ_Version_Features::instance();
                $can_use_temp = $vf ? $vf->can_use_palette( 'temp' ) : false;
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $can_use_temp = JJ_Edition_Controller::instance()->is_at_least( 'premium' ) || JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $can_use_temp = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }

        if ( ! $can_use_temp ) {
            $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
            if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
                $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
            }
            wp_send_json_error( array( 'message' => $message ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        $brand = isset( $options['palettes']['brand'] ) && is_array( $options['palettes']['brand'] ) ? $options['palettes']['brand'] : array();

        if ( empty( $brand ) ) {
            wp_send_json_error( array( 'message' => __( '브랜드 팔레트 값이 비어 있습니다. 먼저 브랜드 색상을 입력해 주세요.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
        if ( ! isset( $temp_options['palettes'] ) || ! is_array( $temp_options['palettes'] ) ) {
            $temp_options['palettes'] = array();
        }
        $temp_options['palettes']['brand'] = $brand;

        update_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, $temp_options );

        wp_send_json_success( array(
            'message' => __( '현재 브랜드 팔레트가 임시 팔레트로 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ),
            'temp_palettes' => $temp_options['palettes'],
        ) );
    }

    /**
     * [v3.8.0] 섹션별 내보내기
     * - 현재는 팔레트(brand/system/alternative/another/temp-palette)만 지원
     * - 관리자에서 파일 다운로드(attachment)로 전달
     */
    public function ajax_export_section() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $section_type = isset( $_POST['section_type'] ) ? sanitize_key( wp_unslash( $_POST['section_type'] ) ) : '';
        $section_subtype = isset( $_POST['section_subtype'] ) ? sanitize_key( wp_unslash( $_POST['section_subtype'] ) ) : '';

        if ( 'palette' !== $section_type ) {
            wp_die( esc_html__( '지원하지 않는 섹션 타입입니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $allowed_subtypes = array( 'brand', 'system', 'alternative', 'another', 'temp' );
        if ( ! in_array( $section_subtype, $allowed_subtypes, true ) ) {
            wp_die( esc_html__( '지원하지 않는 섹션 서브타입입니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        $data = array();

        if ( 'temp' === $section_subtype ) {
            $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
            $data = isset( $temp_options['palettes'] ) ? (array) $temp_options['palettes'] : array();
        } else {
            $data = isset( $options['palettes'][ $section_subtype ] ) ? (array) $options['palettes'][ $section_subtype ] : array();
        }

        $payload = array(
            'exported_at'   => function_exists( 'current_time' ) ? current_time( 'mysql' ) : gmdate( 'c' ),
            'plugin'        => 'acf-css-really-simple-style-management-center',
            'plugin_version'=> defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '',
            'section_type'  => $section_type,
            'section_subtype' => $section_subtype,
            'data'          => $data,
        );

        if ( function_exists( 'nocache_headers' ) ) {
            nocache_headers();
        }

        $filename = sprintf(
            'acf-css-%s-%s-%s.json',
            $section_type,
            $section_subtype,
            gmdate( 'Ymd-His' )
        );

        // 다운로드 헤더
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

        echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        exit;
    }

    /**
     * [v3.8.0] 섹션별 불러오기
     * - JSON 업로드 후 옵션에 병합(키 기준 덮어쓰기)
     */
    public function ajax_import_section() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $section_type = isset( $_POST['section_type'] ) ? sanitize_key( wp_unslash( $_POST['section_type'] ) ) : '';
        $section_subtype = isset( $_POST['section_subtype'] ) ? sanitize_key( wp_unslash( $_POST['section_subtype'] ) ) : '';

        if ( 'palette' !== $section_type ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 섹션 타입입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $allowed_subtypes = array( 'brand', 'system', 'alternative', 'another', 'temp' );
        if ( ! in_array( $section_subtype, $allowed_subtypes, true ) ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 섹션 서브타입입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        if ( empty( $_FILES['import_file'] ) || empty( $_FILES['import_file']['tmp_name'] ) ) {
            wp_send_json_error( array( 'message' => __( '업로드된 파일이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $tmp_name = $_FILES['import_file']['tmp_name'];
        $raw = file_get_contents( $tmp_name );
        if ( ! $raw ) {
            wp_send_json_error( array( 'message' => __( '파일을 읽을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $decoded = json_decode( $raw, true );
        if ( ! is_array( $decoded ) ) {
            wp_send_json_error( array( 'message' => __( 'JSON 형식이 올바르지 않습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // payload 형식({data:{...}})과 순수 데이터({..}) 둘 다 허용
        $import_data = isset( $decoded['data'] ) && is_array( $decoded['data'] ) ? (array) $decoded['data'] : $decoded;

        // 간단 정리(색상 키는 hex로)
        $sanitized = array();
        foreach ( (array) $import_data as $k => $v ) {
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( (string) $k ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $k ) );
            if ( is_array( $v ) ) {
                // 팔레트는 1depth만 정리 (필요 시 확장)
                $sanitized[ $key ] = $v;
                continue;
            }
            if ( strpos( $key, 'color' ) !== false ) {
                $hex = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( (string) $v ) : '';
                $sanitized[ $key ] = $hex ? $hex : '';
                continue;
            }
            $sanitized[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( (string) $v ) : strip_tags( (string) $v );
        }

        if ( 'temp' === $section_subtype ) {
            $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
            if ( ! isset( $temp_options['palettes'] ) || ! is_array( $temp_options['palettes'] ) ) {
                $temp_options['palettes'] = array();
            }
            $temp_options['palettes'] = array_merge( (array) $temp_options['palettes'], $sanitized );
            update_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, $temp_options );
        } else {
            $options = (array) get_option( $this->options_key, array() );
            if ( ! isset( $options['palettes'] ) || ! is_array( $options['palettes'] ) ) {
                $options['palettes'] = array();
            }
            $options['palettes'][ $section_subtype ] = array_merge(
                (array) ( $options['palettes'][ $section_subtype ] ?? array() ),
                $sanitized
            );
            update_option( $this->options_key, $options );
        }

        wp_send_json_success( array(
            'message' => __( '설정이 성공적으로 불러와졌습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }

    // 나머지 메서드들은 dev 버전과 동일 (sync_to_spokes, sync_from_customizer, sanitize_options_recursive 등)
    // 생략하거나 dev 버전 파일을 복사하여 수정

    private function sync_to_spokes( $hub_options ) {
        // Free 버전: WooCommerce만 동기화
        $brand_colors = $hub_options['palettes']['brand'] ?? array();
        
        // WooCommerce 동기화
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! is_array( $kwe ) ) {
            $kwe = array();
        }
        if ( isset( $brand_colors['primary_color'] ) ) {
            $kwe['product_quickview_button_color'] = $brand_colors['primary_color'];
        }
        if ( isset( $brand_colors['primary_color_hover'] ) ) {
            $kwe['product_quickview_button_color_hover'] = $brand_colors['primary_color_hover'];
        }
        update_option( 'kt_woo_extras', $kwe );
    }

    public function sync_from_customizer( $manager ) {
        $hub_options = (array) get_option( $this->options_key );
        $panel_settings = $manager->settings();
        
        foreach ( $panel_settings as $setting_id => $setting_obj ) {
            if ( strpos( $setting_id, $this->options_key . '[' ) === 0 ) {
                $keys = str_replace( array( $this->options_key, '[', ']' ), '', $setting_id );
                $keys = explode( '][', $keys );
                $this->set_nested_object_value( $hub_options, $keys, $setting_obj->post_value() );
            }
        }

        update_option( $this->options_key, $hub_options );
        JJ_CSS_Cache::instance()->flush();
    }

    private function set_nested_object_value( &$obj, $keys, $value ) {
        $current = &$obj;
        foreach ( $keys as $key ) {
            if ( ! isset( $current[$key] ) || ! is_array( $current[$key] ) ) {
                $current[$key] = array();
            }
            $current = &$current[$key];
        }
        $current = $value;
    }

    private function sanitize_options_recursive( $input ) {
        $output = array();
        foreach ( (array) $input as $key => $value ) {
            $raw_key = (string) $key;
            // [v5.3.8] WordPress 함수 안전 호출
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( $raw_key ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( $raw_key ) );
            if ( is_array( $value ) ) {
                $output[ $key ] = $this->sanitize_options_recursive( $value );
                continue;
            }
            // [v5.3.8] PHP 7.4 호환성: str_contains() 대신 strpos() 사용
            if ( strpos( $key, 'color' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $sanitized = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( $value ) : '';
                $output[ $key ] = $sanitized ? $sanitized : '';
                continue;
            }
            if ( strpos( $key, 'custom_css' ) !== false || strpos( $key, 'additional_css' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'wp_kses_post' ) ? wp_kses_post( $value ) : wp_strip_all_tags( $value );
                continue;
            }
            if ( strpos( $key, 'font_family' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
                continue;
            }
            // [v5.3.8] WordPress 함수 안전 호출
            $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
        }
        return $output;
    }

    public function load_options_and_init_strategies() {
        // [v5.3.5] 전체 메서드를 try-catch로 보호하여 치명적 오류 방지
        try {
            $this->environment_map = $this->scan_environment();
            $this->load_dynamic_adapters( $this->environment_map );
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options = $options;
            $this->sync_existing_settings();
            $this->brand_colors = $this->options['palettes']['brand'] ?? array();

            // [v5.3.5] 모든 전략 클래스 호출에 class_exists() 확인 추가
            // 전략 1: CSS 변수
            if ( class_exists( 'JJ_Strategy_1_CSS_Vars' ) ) {
                JJ_Strategy_1_CSS_Vars::instance()->init( $this->options );
            }
            
            // 전략 2: PHP 필터
            if ( class_exists( 'JJ_Strategy_2_PHP_Filters' ) ) {
                JJ_Strategy_2_PHP_Filters::instance()->init( $this->options, null, null, null, null );
            }
            
            // 전략 3: Dequeue (CSS 재정의)
            if ( class_exists( 'JJ_Strategy_3_Dequeue' ) ) {
                JJ_Strategy_3_Dequeue::instance()->init( $this->options );
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    private function sync_existing_settings() {
        $active_theme = wp_get_theme()->get_template();
        $needs_update = false;

        if ( ! isset( $this->options['palettes'] ) ) {
            $this->options['palettes'] = array();
        }
        if ( ! isset( $this->options['palettes']['brand'] ) ) {
            $this->options['palettes']['brand'] = array();
        }
        if ( ! isset( $this->options['typography'] ) ) {
            $this->options['typography'] = array();
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five 테마만 동기화
        if ( in_array( $active_theme, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
            // 기본 테마는 별도 설정이 없으므로 Customizer에서 가져오기
        }

        // WooCommerce 동기화 (Free 버전 지원)
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! empty( $kwe['product_quickview_button_color'] ) && empty( $this->options['palettes']['brand']['primary_color'] ) ) {
            $this->options['palettes']['brand']['primary_color'] = $kwe['product_quickview_button_color'];
            $needs_update = true;
        }
        if ( ! empty( $kwe['product_quickview_button_color_hover'] ) && empty( $this->options['palettes']['brand']['primary_color_hover'] ) ) {
            $this->options['palettes']['brand']['primary_color_hover'] = $kwe['product_quickview_button_color_hover'];
            $needs_update = true;
        }

        if ( $needs_update ) {
            update_option( $this->options_key, $this->options );
        }
    }

    private function load_dynamic_adapters( $environment_map ) {
        $active_adapters = $environment_map;
        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;
        $error_handler = JJ_Error_Handler::instance();

        // Free 버전: 테마 어댑터 제한 (Twenty Twenty-Four, Twenty Twenty-Five만)
        if ( ! empty( $active_adapters['themes'] ) ) {
            $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
            $active_adapters['themes'] = array_intersect( $active_adapters['themes'], $allowed_themes );
            
            foreach ( $active_adapters['themes'] as $theme_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-theme-' . sanitize_file_name( $theme_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Theme_' . str_replace( '-', '_', ucwords( $theme_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }

        // Free 버전: 플러그인 어댑터 제한 (WooCommerce만)
        if ( ! empty( $active_adapters['spokes'] ) ) {
            $allowed_plugins = array( 'woocommerce' );
            $active_adapters['spokes'] = array_intersect( $active_adapters['spokes'], $allowed_plugins );
            
            foreach ( $active_adapters['spokes'] as $spoke_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-spoke-' . sanitize_file_name( $spoke_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Spoke_' . str_replace( '-', '_', ucwords( $spoke_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }
    }

    private function scan_environment() {
        $config = $this->adapters_config;
        $spokes_to_check = $config['spokes'] ?? array();
        $themes_to_check = $config['themes'] ?? array();

        $active_plugins = (array) get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        $active_spoke_adapters = array();
        $active_theme_adapters = array();
        $current_theme_path = get_template_directory();
        $theme_dir_name = basename( $current_theme_path );

        // Free 버전: WooCommerce만 체크
        foreach ( $spokes_to_check as $adapter_slug => $plugin_path ) {
            if ( in_array( $plugin_path, $active_plugins ) && $adapter_slug === 'woocommerce' ) {
                $active_spoke_adapters[] = $adapter_slug;
            }
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five만 체크
        foreach ( $themes_to_check as $adapter_slug => $theme_path_suffix ) {
            if ( $theme_dir_name === $adapter_slug && in_array( $adapter_slug, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
                if ( file_exists( $current_theme_path . '/' . $theme_path_suffix ) ) {
                    $active_theme_adapters[] = $adapter_slug;
                }
            }
        }

        return array(
            'themes' => $active_theme_adapters,
            'spokes' => $active_spoke_adapters,
        );
    }

    public function render_debug_info() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $errors = JJ_Error_Handler::instance()->get_errors();

        echo '<div style="background: #001a33; color: #00ff8c; padding: 20px; font-family: monospace; font-size: 14px; line-height: 1.6; z-index: 99999; position: relative; margin: 20px; border-radius: 5px; white-space: pre-wrap; word-break: break-all; opacity: 0.9;">';
        echo '<strong>[ACF CSS DEBUG - FREE VERSION]</strong><br>';
        echo '====================<br>';
        echo '<strong>Edition:</strong> Free<br>';
        echo '<strong>Loaded Adapters:</strong><br>';
        if ( ! empty( $this->environment_map['themes'] ) ) {
            echo ' - Themes: ' . esc_html( implode( ', ', $this->environment_map['themes'] ) ) . '<br>';
        } else {
            echo ' - Themes: None<br>';
        }
        if ( ! empty( $this->environment_map['spokes'] ) ) {
            echo ' - Spokes: ' . esc_html( implode( ', ', $this->environment_map['spokes'] ) ) . '<br>';
        } else {
            echo ' - Spokes: None<br>';
        }
        echo '</div>';
    }
}

function jj_simple_style_guide_master_run() {
    // [Phase 5] 다국어 지원 로드
    load_plugin_textdomain( 'acf-css-really-simple-style-management-center', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // WordPress 함수가 로드되었는지 확인
    if ( ! function_exists( 'class_exists' ) || ! function_exists( 'file_exists' ) ) {
        return;
    }
    
    // [v5.3.9] 코드 무결성 모니터 초기화 (plugins_loaded 시점)
    
    // [Phase 4.5/4.99] Admin Center 초기화 (메뉴/상단바/관리자 UI)
    if ( class_exists( 'JJ_Admin_Center' ) ) {
        try {
            $admin_center = JJ_Admin_Center::instance();
            if ( method_exists( $admin_center, 'init' ) ) {
                $admin_center->init();
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v3.7.0] 백업 관리자 초기화
    if ( class_exists( 'JJ_Backup_Manager' ) ) {
        try {
            JJ_Backup_Manager::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] REST API 초기화
    if ( class_exists( 'JJ_Style_Guide_REST_API' ) ) {
        try {
            JJ_Style_Guide_REST_API::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.3] Extension Manager 초기화
    if ( class_exists( 'JJ_Extension_Manager' ) ) {
        try {
            JJ_Extension_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] Webhook Manager 초기화
    if ( class_exists( 'JJ_Webhook_Manager' ) ) {
        try {
            JJ_Webhook_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5 B] Multisite Controller 초기화
    if ( class_exists( 'JJ_Multisite_Controller' ) ) {
        try {
            JJ_Multisite_Controller::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v5.6.0] Visual Command Center 초기화
    if ( class_exists( 'JJ_Visual_Command_Center' ) ) {
        JJ_Visual_Command_Center::instance();
    }

    // [v5.7.0] AI Style Intelligence 초기화
    if ( class_exists( 'JJ_Smart_Palette' ) ) {
        JJ_Smart_Palette::instance();
    }

    // [v5.9.0] CSS Optimizer 초기화
    if ( class_exists( 'JJ_CSS_Optimizer' ) ) {
        JJ_CSS_Optimizer::instance();
    }

    // [v5.8.0] Cloud Manager 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Cloud_Manager' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) { // Premium 이상 체크 대용
            JJ_Cloud_Manager::instance();
        }
    }
    
    // [Phase 3] 템플릿 마켓 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Template_Market' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) {
            JJ_Template_Market::instance();
        }
    }
    
    // [Phase 5] Partner Hub 초기화 (Partner/Master만)
    if ( class_exists( 'JJ_Partner_Hub' ) ) {
        JJ_Partner_Hub::instance();
    }

    // [v4.2.2] 실험실(Labs) 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Labs_Center' ) ) {
        try {
            JJ_Labs_Center::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }


    if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
        try {
            JJ_Code_Integrity_Monitor::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Code Integrity Monitor: 초기화 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.9] 라이센스 강제 실행 초기화 (plugins_loaded 시점)
    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $enforcement_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-enforcement.php';
        if ( file_exists( $enforcement_file ) ) {
            require_once $enforcement_file;
            if ( class_exists( 'JJ_License_Enforcement' ) ) {
                try {
                    JJ_License_Enforcement::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ License Enforcement: 초기화 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // [v5.3.7] 안전 모드 플래그 확인 및 활성화
    if ( function_exists( 'get_option' ) && class_exists( 'JJ_Safe_Mode' ) ) {
        $safe_mode_enabled = get_option( 'jj_style_guide_safe_mode', false );
        if ( $safe_mode_enabled ) {
            $safe_mode_reason = get_option( 'jj_style_guide_safe_mode_reason', '활성화 중 오류 발생' );
            try {
                $safe_mode = JJ_Safe_Mode::instance();
                if ( method_exists( $safe_mode, 'enable' ) ) {
                    $safe_mode->enable( $safe_mode_reason );
                }
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 오류 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
    
    // 메인 클래스 인스턴스 생성
    if ( class_exists( 'JJ_Simple_Style_Guide_Master' ) ) {
        try {
            JJ_Simple_Style_Guide_Master::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.6] 기존 플러그인 마이그레이션 매니저 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $migration_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php';
        if ( file_exists( $migration_file ) ) {
            // 마이그레이션 매니저는 이미 safe_loader를 통해 로드되었을 수 있음
            // 클래스가 존재하는 경우에만 초기화
            if ( class_exists( 'JJ_Migration_Manager' ) ) {
                try {
                    JJ_Migration_Manager::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 오류 - ' . $e->getMessage() );
                    }
                } catch ( Error $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // 플러그인 업데이트 체크 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $updater_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php';
        if ( file_exists( $updater_file ) ) {
            require_once $updater_file;
            if ( class_exists( 'JJ_Plugin_Updater' ) ) {
                try {
                    new JJ_Plugin_Updater(
                        __FILE__,
                        'advanced-custom-style-manage-center',
                        "ACF CSS Master",
                        defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.4.0',
                        'DEV' // 마스터 버전
                    );
                } catch ( Exception $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 오류 - ' . $e->getMessage() );
                } catch ( Error $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
}

// [v5.3.8] plugins_loaded 훅 활성화
if ( function_exists( 'add_action' ) ) {
    add_action( 'plugins_loaded', 'jj_simple_style_guide_master_run', 10 );
}

/**
 * [v8.0.0] 안전 장치: 필수 헬퍼 함수 Fallback 정의
 * class-jj-admin-center.php 로드 실패 시에도 사이트가 멈추지 않도록 보장합니다.
 */
if ( ! function_exists( 'jj_style_guide_text' ) ) {
    function jj_style_guide_text( $key, $fallback = '' ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_text( $key, $fallback );
        }
        return $fallback;
    }
}

if ( ! function_exists( 'jj_style_guide_sections_layout' ) ) {
    function jj_style_guide_sections_layout() {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_sections_layout();
        }
        return array();
    }
}

if ( ! function_exists( 'jj_style_guide_section_index' ) ) {
    function jj_style_guide_section_index( $slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_section_index( $slug );
        }
        return null;
    }
}

if ( ! function_exists( 'jj_style_guide_is_tab_enabled' ) ) {
    function jj_style_guide_is_tab_enabled( $section_slug, $tab_slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->is_tab_enabled( $section_slug, $tab_slug );
        }
        return true;
    }
}

```

```javascript
/**
 * JJ Style Guide - Palette Presets & Inline Preview
 *
 * 목표:
 * - 텍스트 위주의 팔레트 UI를 "보여주는" UI로 보강
 * - 2-Color 조합(Primary/Secondary) + 권장 System 팔레트까지 프리셋으로 제공
 * - 클릭 1~2번으로 즉시 적용 + 즉시 미리보기
 *
 * 주의:
 * - 기존 `jj-style-guide-editor.js`의 currentSettings/필드 바인딩 로직을 존중합니다.
 * - 실제 값 적용은 `data-setting-key` 필드 값을 변경(trigger change)하는 방식으로 통일합니다.
 */
(function($){
  'use strict';

  function normalizeHex(hex) {
    if (!hex || typeof hex !== 'string') return '';
    var h = hex.trim();
    if (!h) return '';
    if (h[0] !== '#') h = '#' + h;
    if (h.length === 4) {
      // #RGB -> #RRGGBB
      h = '#' + h[1] + h[1] + h[2] + h[2] + h[3] + h[3];
    }
    return h.toUpperCase();
  }

  function clamp(n, min, max) { return Math.max(min, Math.min(max, n)); }

  function hexToRgb(hex) {
    var h = normalizeHex(hex);
    if (!h || h.length !== 7) return null;
    return {
      r: parseInt(h.slice(1, 3), 16),
      g: parseInt(h.slice(3, 5), 16),
      b: parseInt(h.slice(5, 7), 16),
    };
  }

  function rgbToHex(r, g, b) {
    var rr = clamp(Math.round(r), 0, 255).toString(16).padStart(2, '0');
    var gg = clamp(Math.round(g), 0, 255).toString(16).padStart(2, '0');
    var bb = clamp(Math.round(b), 0, 255).toString(16).padStart(2, '0');
    return ('#' + rr + gg + bb).toUpperCase();
  }

  // amount: -1.0 ~ +1.0 (음수: 어둡게, 양수: 밝게)
  function shade(hex, amount) {
    var rgb = hexToRgb(hex);
    if (!rgb) return '';
    var t = amount < 0 ? 0 : 255;
    var p = Math.abs(amount);
    return rgbToHex(
      (t - rgb.r) * p + rgb.r,
      (t - rgb.g) * p + rgb.g,
      (t - rgb.b) * p + rgb.b
    );
  }

  function setField(settingKey, value) {
    if (!settingKey) return;
    var v = normalizeHex(value);
    var $field = $('#jj-style-guide-form').find('[data-setting-key="' + settingKey + '"]');
    if (!$field.length) return;

    $field.val(v).trigger('change');
    // wpColorPicker가 있으면 색상도 동기화
    if ($field.closest('.wp-picker-container').length) {
      try { $field.wpColorPicker('color', v); } catch (e) {}
    }
    // 카드 미리보기(기존 DOM)
    var $preview = $field.closest('.jj-control-group').find('.jj-color-preview').first();
    if ($preview.length) $preview.css('background-color', v);
  }

  function readField(settingKey, fallback) {
    var $field = $('#jj-style-guide-form').find('[data-setting-key="' + settingKey + '"]');
    var v = $field.length ? normalizeHex($field.val()) : '';
    return v || normalizeHex(fallback) || '';
  }

  function updateInlinePreview() {
    var $preview = $('#jj-palette-inline-preview');
    if (!$preview.length) return;

    var primary = readField('palettes[brand][primary_color]', '#2271B1');
    var primaryHover = readField('palettes[brand][primary_color_hover]', shade(primary, -0.12));
    var secondary = readField('palettes[brand][secondary_color]', '#444444');
    var secondaryHover = readField('palettes[brand][secondary_color_hover]', shade(secondary, -0.12));

    var bg = readField('palettes[system][site_bg]', '#F6F7F7');
    var surface = readField('palettes[system][content_bg]', '#FFFFFF');
    var text = readField('palettes[system][text_color]', '#1D2327');
    var link = readField('palettes[system][link_color]', primary);

    $preview.css('--jj-prev-bg', bg);
    $preview.css('--jj-prev-surface', surface);
    $preview.css('--jj-prev-text', text);
    $preview.css('--jj-prev-link', link);
    $preview.css('--jj-prev-primary', primary);
    $preview.css('--jj-prev-primary-hover', primaryHover);
    $preview.css('--jj-prev-secondary', secondary);
    $preview.css('--jj-prev-secondary-hover', secondaryHover);

    $preview.find('[data-jj-color="primary"]').text(primary);
    $preview.find('[data-jj-color="secondary"]').text(secondary);
    $preview.find('[data-jj-color="bg"]').text(bg);
    $preview.find('[data-jj-color="text"]').text(text);
  }

  $(document).ready(function(){
    var $mount = $('#jj-palette-presets');
    if (!$mount.length) return;

    var PRESETS = [
      {
        id: 'luxury-black-gold',
        name: 'Black & Gold',
        tags: ['럭셔리', '다크', '고대비', '프리미엄'],
        note: '고급·권위·집중(CTA 강함). 법률/컨설팅/하이엔드에 적합.',
        brand: { primary: '#C9A227', secondary: '#0B0D0F' },
        system: { site_bg: '#0B0D0F', content_bg: '#101418', text_color: '#EAEAEA', link_color: '#C9A227' }
      },
      {
        id: 'trust-navy-cyan',
        name: 'Navy & Cyan',
        tags: ['신뢰', '테크', '차분함'],
        note: '신뢰·안정·정교함. SaaS/데이터/기업 사이트에 적합.',
        brand: { primary: '#1F3C88', secondary: '#00B8D9' },
        system: { site_bg: '#F7FAFC', content_bg: '#FFFFFF', text_color: '#1D2327', link_color: '#1F3C88' }
      },
      {
        id: 'modern-coral-slate',
        name: 'Coral & Slate',
        tags: ['모던', '따뜻함', '크리에이티브'],
        note: '친근·활기. 브랜딩/포트폴리오/커머스에 적합.',
        brand: { primary: '#FF6B6B', secondary: '#334E68' },
        system: { site_bg: '#FFFFFF', content_bg: '#F7FAFC', text_color: '#102A43', link_color: '#334E68' }
      },
      {
        id: 'minimal-mono',
        name: 'Minimal Mono',
        tags: ['미니멀', '라이트', '가독성'],
        note: '콘텐츠 중심·가독성 강화. 블로그/매거진에 적합.',
        brand: { primary: '#111827', secondary: '#6B7280' },
        system: { site_bg: '#F9FAFB', content_bg: '#FFFFFF', text_color: '#111827', link_color: '#111827' }
      },
      {
        id: 'emerald-charcoal',
        name: 'Emerald & Charcoal',
        tags: ['헬스', '자연', '안정'],
        note: '안정·신선. 헬스/웰니스/교육에 적합.',
        brand: { primary: '#10B981', secondary: '#111827' },
        system: { site_bg: '#F7FAFC', content_bg: '#FFFFFF', text_color: '#111827', link_color: '#10B981' }
      },
      {
        id: 'royal-purple-ice',
        name: 'Royal Purple',
        tags: ['프리미엄', '창의', '브랜딩'],
        note: '독창·세련. 이벤트/크리에이티브 스튜디오에 적합.',
        brand: { primary: '#6D28D9', secondary: '#0EA5E9' },
        system: { site_bg: '#F8FAFC', content_bg: '#FFFFFF', text_color: '#0F172A', link_color: '#6D28D9' }
      },
      {
        id: 'warm-brown-cream',
        name: 'Brown & Cream',
        tags: ['따뜻함', '클래식', '브랜드'],
        note: '따뜻·신뢰. 카페/식품/라이프스타일에 적합.',
        brand: { primary: '#8B5E34', secondary: '#D4A373' },
        system: { site_bg: '#FFF7ED', content_bg: '#FFFFFF', text_color: '#1F2937', link_color: '#8B5E34' }
      },
      {
        id: 'contrast-blue-yellow',
        name: 'Blue & Yellow (CTA)',
        tags: ['고대비', '전환', 'CTA'],
        note: '버튼/링크가 눈에 띄게. 랜딩/광고 페이지에 적합.',
        brand: { primary: '#2563EB', secondary: '#F59E0B' },
        system: { site_bg: '#FFFFFF', content_bg: '#F8FAFC', text_color: '#0F172A', link_color: '#2563EB' }
      }
    ];

    // hover 자동 생성(없는 경우)
    PRESETS.forEach(function(p){
      p.brand.primary = normalizeHex(p.brand.primary);
      p.brand.secondary = normalizeHex(p.brand.secondary);
      p.brand.primary_hover = normalizeHex(p.brand.primary_hover || shade(p.brand.primary, -0.12));
      p.brand.secondary_hover = normalizeHex(p.brand.secondary_hover || shade(p.brand.secondary, -0.12));
      p.system.site_bg = normalizeHex(p.system.site_bg);
      p.system.content_bg = normalizeHex(p.system.content_bg);
      p.system.text_color = normalizeHex(p.system.text_color);
      p.system.link_color = normalizeHex(p.system.link_color || p.brand.primary);
    });

    var selectedId = null;

    function render(filterText) {
      var q = (filterText || '').toLowerCase().trim();
      $mount.empty();

      var $toolbar = $('<div class="jj-preset-toolbar"></div>');
      var $search = $('<input type="search" class="jj-preset-search" placeholder="프리셋 검색 (예: 럭셔리, 신뢰, 다크)"/>');
      $search.val(filterText || '');

      var $includeSystem = $('<label class="jj-preset-include-system"><input type="checkbox" checked> 시스템 팔레트도 함께 적용</label>');

      $toolbar.append($search).append($includeSystem);
      $mount.append($toolbar);

      var $grid = $('<div class="jj-preset-grid" role="list"></div>');
      var shown = 0;

      PRESETS.forEach(function(p){
        var hay = (p.name + ' ' + p.tags.join(' ') + ' ' + p.note).toLowerCase();
        if (q && hay.indexOf(q) === -1) return;
        shown++;

        var isActive = selectedId === p.id;
        var $card = $('<button type="button" class="jj-preset-card" role="listitem"></button>');
        $card.toggleClass('is-active', !!isActive);
        $card.attr('data-preset-id', p.id);

        var swatches = [
          { c: p.brand.primary, label: 'Primary' },
          { c: p.brand.secondary, label: 'Secondary' },
          { c: p.system.site_bg, label: 'BG' },
          { c: p.system.text_color, label: 'Text' }
        ];

        var $sw = $('<div class="jj-preset-swatches" aria-hidden="true"></div>');
        swatches.forEach(function(s){
          var $s = $('<span class="jj-preset-swatch"></span>');
          $s.css('background-color', s.c);
          $s.attr('title', s.label + ': ' + s.c);
          $sw.append($s);
        });

        var $meta = $('<div class="jj-preset-meta"></div>');
        $meta.append($('<div class="jj-preset-title"></div>').text(p.name));
        $meta.append($('<div class="jj-preset-tags"></div>').text(p.tags.join(' · ')));
        $meta.append($('<div class="jj-preset-note"></div>').text(p.note));

        $card.append($sw).append($meta);
        $grid.append($card);
      });

      if (shown === 0) {
        $grid.append($('<div class="jj-preset-empty"></div>').text('검색 결과가 없습니다.'));
      }

      var $actions = $('<div class="jj-preset-actions"></div>');
      var $apply = $('<button type="button" class="button button-primary jj-preset-apply" disabled>선택한 프리셋 적용</button>');
      var $hint = $('<span class="jj-preset-hint">클릭 → 미리보기 → 적용 (언제든 수정 가능)</span>');
      $actions.append($apply).append($hint);

      $mount.append($grid).append($actions);

      // events
      $search.on('input', function(){ render($(this).val()); });

      $grid.on('click', '.jj-preset-card', function(){
        selectedId = $(this).data('preset-id');
        render($search.val());

        // 선택 즉시 "필드에 적용하지 않고" 미리보기만 업데이트하려면 별도 상태가 필요합니다.
        // 현재 단계에서는 선택 후 적용 버튼을 누르게 하고, 인라인 프리뷰는 현재 필드값을 보여줍니다.
      });

      $apply.prop('disabled', !selectedId);
      $apply.on('click', function(){
        var preset = PRESETS.find(function(x){ return x.id === selectedId; });
        if (!preset) return;

        var includeSystem = $includeSystem.find('input').is(':checked');

        // brand
        setField('palettes[brand][primary_color]', preset.brand.primary);
        setField('palettes[brand][primary_color_hover]', preset.brand.primary_hover);
        setField('palettes[brand][secondary_color]', preset.brand.secondary);
        setField('palettes[brand][secondary_color_hover]', preset.brand.secondary_hover);

        // system (옵션)
        if (includeSystem) {
          setField('palettes[system][site_bg]', preset.system.site_bg);
          setField('palettes[system][content_bg]', preset.system.content_bg);
          setField('palettes[system][text_color]', preset.system.text_color);
          setField('palettes[system][link_color]', preset.system.link_color);
        }

        // 미리보기 갱신
        updateInlinePreview();
      });
    }

    render('');
    updateInlinePreview();

    // 색상 변경 시 미리보기 자동 갱신
    $('#jj-style-guide-form').on('change input', '.jj-color-field', function(){
      updateInlinePreview();
    });
  });
})(jQuery);


```

```css
/* JJ Style Guide - Palette Presets & Inline Preview */

#jj-style-guide-wrapper .jj-palette-quickstart {
  margin: 18px 0 22px;
  padding: 16px;
  border: 1px solid #c3c4c7;
  border-radius: 8px;
  background: #f8fafc;
}

#jj-style-guide-wrapper .jj-palette-quickstart h3 {
  margin: 0 0 6px;
  font-size: 16px;
}

#jj-style-guide-wrapper .jj-preset-toolbar {
  display: flex;
  gap: 10px;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  margin: 12px 0;
}

#jj-style-guide-wrapper .jj-preset-search {
  flex: 1;
  min-width: 240px;
  max-width: 520px;
  height: 36px;
  padding: 0 10px;
  border-radius: 6px;
}

#jj-style-guide-wrapper .jj-preset-include-system {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #1d2327;
}

#jj-style-guide-wrapper .jj-preset-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 10px;
}

@media (max-width: 1024px) {
  #jj-style-guide-wrapper .jj-preset-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
@media (max-width: 640px) {
  #jj-style-guide-wrapper .jj-preset-grid {
    grid-template-columns: 1fr;
  }
}

#jj-style-guide-wrapper .jj-preset-card {
  text-align: left;
  border: 1px solid #c3c4c7;
  background: #fff;
  border-radius: 10px;
  padding: 12px;
  cursor: pointer;
  transition: transform .08s ease, box-shadow .12s ease, border-color .12s ease;
}

#jj-style-guide-wrapper .jj-preset-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(0,0,0,.08);
  border-color: #8c8f94;
}

#jj-style-guide-wrapper .jj-preset-card.is-active {
  border-color: #ff6400;
  box-shadow: 0 0 0 2px rgba(255,100,0,.15);
}

#jj-style-guide-wrapper .jj-preset-swatches {
  display: flex;
  gap: 6px;
  margin-bottom: 10px;
}

#jj-style-guide-wrapper .jj-preset-swatch {
  width: 100%;
  height: 14px;
  border-radius: 999px;
  border: 1px solid rgba(0,0,0,.12);
}

#jj-style-guide-wrapper .jj-preset-meta {
  display: grid;
  gap: 4px;
}

#jj-style-guide-wrapper .jj-preset-title {
  font-weight: 800;
  color: #0f172a;
}

#jj-style-guide-wrapper .jj-preset-tags {
  font-size: 12px;
  color: #475569;
}

#jj-style-guide-wrapper .jj-preset-note {
  font-size: 12px;
  color: #64748b;
  line-height: 1.35;
}

#jj-style-guide-wrapper .jj-preset-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 12px;
}

#jj-style-guide-wrapper .jj-preset-hint {
  font-size: 12px;
  color: #64748b;
}

#jj-style-guide-wrapper .jj-preset-empty {
  grid-column: 1 / -1;
  padding: 18px;
  text-align: center;
  color: #64748b;
  background: rgba(255,255,255,.6);
  border: 1px dashed #c3c4c7;
  border-radius: 10px;
}

/* Inline preview */
#jj-style-guide-wrapper .jj-palette-inline-preview {
  margin-top: 14px;
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,.12);
  overflow: hidden;
  background: var(--jj-prev-bg, #f6f7f7);
  color: var(--jj-prev-text, #1d2327);
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-surface {
  background: var(--jj-prev-surface, #fff);
  margin: 14px;
  border-radius: 12px;
  padding: 16px;
  border: 1px solid rgba(0,0,0,.08);
}

#jj-style-guide-wrapper .jj-palette-inline-preview a {
  color: var(--jj-prev-link, var(--jj-prev-primary, #2271b1));
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-title {
  font-size: 16px;
  font-weight: 900;
  margin: 0 0 6px;
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-desc {
  margin: 0 0 12px;
  font-size: 13px;
  line-height: 1.45;
  opacity: .95;
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 10px;
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-btn {
  appearance: none;
  border: 1px solid rgba(0,0,0,.12);
  border-radius: 10px;
  padding: 10px 14px;
  font-weight: 800;
  cursor: pointer;
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-btn.primary {
  background: var(--jj-prev-primary, #2271b1);
  border-color: var(--jj-prev-primary, #2271b1);
  color: #fff;
}
#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-btn.primary:hover {
  background: var(--jj-prev-primary-hover, #135e96);
  border-color: var(--jj-prev-primary-hover, #135e96);
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-btn.secondary {
  background: var(--jj-prev-secondary, #444);
  border-color: var(--jj-prev-secondary, #444);
  color: #fff;
}
#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-btn.secondary:hover {
  background: var(--jj-prev-secondary-hover, #333);
  border-color: var(--jj-prev-secondary-hover, #333);
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-kv {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  font-size: 12px;
  color: rgba(0,0,0,.55);
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-kv code {
  background: rgba(0,0,0,.05);
  padding: 2px 6px;
  border-radius: 6px;
}


```

```php
<?php
/**
 * [v3.5.0-dev5 '제련'] '1. 팔레트 시스템' 섹션 UI 템플릿
 * - [신규] '5. 실험실 (Labs)' '탭' '버튼' '추가'
 * - [수정] 탭 번호 '재정렬'
 */
if ( ! defined( 'ABSPATH' ) ) exit; // 직접 접근 차단

// [v3.1 '신규'] '시스템' 팔레트 옵션 로드
$system_palettes = $options['palettes']['system'] ?? array();

// [v3.8.0] 버전별 기능 제한 체크
$version_features = class_exists( 'JJ_Version_Features' ) ? JJ_Version_Features::instance() : null;

// [v5.0.0] 탭 활성화/비활성화 체크 (안전하게 처리)
$tabs_layout = array();
if ( function_exists( 'jj_style_guide_sections_layout' ) ) {
    $section_layout = jj_style_guide_sections_layout();
    if ( isset( $section_layout['colors'] ) && is_array( $section_layout['colors'] ) ) {
        $colors_layout = $section_layout['colors'];
        $tabs_layout = isset( $colors_layout['tabs'] ) && is_array( $colors_layout['tabs'] ) ? $colors_layout['tabs'] : array();
    }
}
?>

<div class="jj-section-global" id="jj-section-palettes">
    <h2 class="jj-section-title">
        <?php
        $jj_section_index = function_exists( 'jj_style_guide_section_index' ) ? jj_style_guide_section_index( 'colors' ) : null;
        if ( $jj_section_index ) :
            ?>
            <span class="jj-section-index"><?php echo intval( $jj_section_index ); ?>.</span>
        <?php endif; ?>
        <?php echo esc_html( jj_style_guide_text( 'section_palettes_title', __( '팔레트 시스템 (Palette System)', 'jj-style-guide' ) ) ); ?>
    </h2>
    <p class="description">
        <?php
        echo wp_kses_post(
            jj_style_guide_text(
                'section_palettes_description',
                __(
                    '웹사이트 전반에서 사용할 기본 색상을 정리하는 공간입니다. <strong>브랜드 팔레트</strong>는 버튼, 폼, 링크 등 주요 요소의 기준이 되며, <strong>시스템 팔레트</strong>는 사이트의 배경과 본문 텍스트, 링크 색상을 차분하게 정렬해 줍니다. 필요하다면 얼터너티브/임시 팔레트를 활용해 실험적인 조합도 안전하게 시도하실 수 있습니다.',
                    'jj-style-guide'
                )
            )
        );
        ?>
    </p>

    <div class="jj-tabs-container">
        <div class="jj-tabs-nav">
            <?php 
            // [v5.0.0] 탭 활성화/비활성화 체크
            $tab_enabled_brand = ! isset( $tabs_layout['brand'] ) || ! empty( $tabs_layout['brand']['enabled'] );
            $tab_enabled_system = ( ! isset( $tabs_layout['system'] ) || ! empty( $tabs_layout['system']['enabled'] ) ) && ( ! $version_features || $version_features->can_use_palette( 'system' ) );
            $tab_enabled_alternative = ( ! isset( $tabs_layout['alternative'] ) || ! empty( $tabs_layout['alternative']['enabled'] ) ) && ( ! $version_features || $version_features->can_use_palette( 'alternative' ) );
            $tab_enabled_another = ( ! isset( $tabs_layout['another'] ) || ! empty( $tabs_layout['another']['enabled'] ) ) && ( ! $version_features || $version_features->can_use_palette( 'another' ) );
            $tab_enabled_temp = ( ! isset( $tabs_layout['temp-palette'] ) || ! empty( $tabs_layout['temp-palette']['enabled'] ) ) && ( ! $version_features || $version_features->can_use_palette( 'temp' ) );
            
            // 활성화된 탭이 하나도 없으면 브랜드 팔레트를 기본으로 활성화
            $has_enabled_tabs = $tab_enabled_brand || $tab_enabled_system || $tab_enabled_alternative || $tab_enabled_another || $tab_enabled_temp;
            if ( ! $has_enabled_tabs ) {
                $tab_enabled_brand = true;
            }
            
            $first_enabled_tab = $tab_enabled_brand ? 'brand' : ( $tab_enabled_system ? 'system' : ( $tab_enabled_alternative ? 'alternative' : ( $tab_enabled_another ? 'another' : 'temp-palette' ) ) );
            ?>
            <?php if ( $tab_enabled_brand ) : ?>
            <button type="button" class="jj-tab-button <?php echo ( $first_enabled_tab === 'brand' ) ? 'is-active' : ''; ?>" data-tab="brand">
                <?php _e( '1. 브랜드 팔레트', 'jj-style-guide' ); ?>
            </button>
            <?php endif; ?>
            <?php if ( $tab_enabled_system ) : ?>
            <button type="button" class="jj-tab-button <?php echo ( $first_enabled_tab === 'system' ) ? 'is-active' : ''; ?>" data-tab="system">
                <?php _e( '2. 시스템 팔레트', 'jj-style-guide' ); ?>
            </button>
            <?php endif; ?>
            <?php if ( $tab_enabled_alternative ) : ?>
            <button type="button" class="jj-tab-button <?php echo ( $first_enabled_tab === 'alternative' ) ? 'is-active' : ''; ?>" data-tab="alternative">
                <?php _e( '3. 얼터너티브 팔레트', 'jj-style-guide' ); ?>
            </button>
            <?php endif; ?>
            <?php if ( $tab_enabled_another ) : ?>
            <button type="button" class="jj-tab-button <?php echo ( $first_enabled_tab === 'another' ) ? 'is-active' : ''; ?>" data-tab="another">
                <?php _e( '4. 어나더 팔레트', 'jj-style-guide' ); ?>
            </button>
            <?php endif; ?>
            <?php if ( $tab_enabled_temp ) : ?>
            <button type="button" class="jj-tab-button jj-tab-button-temp <?php echo ( $first_enabled_tab === 'temp-palette' ) ? 'is-active' : ''; ?>" data-tab="temp-palette">
                <?php _e( '5. 임시 팔레트', 'jj-style-guide' ); ?>
            </button>
            <?php endif; ?>
        </div>

        <?php if ( $tab_enabled_brand ) : ?>
        <div class="jj-tab-content <?php echo ( $first_enabled_tab === 'brand' ) ? 'is-active' : ''; ?>" data-tab-content="brand">
            <?php if ( ! $version_features || $version_features->can_use_section_refresh() ) : ?>
            <div class="jj-section-refresh-control" style="margin-bottom: 20px; padding: 15px; background: #f0f6fc; border: 1px solid #c3c4c7; border-radius: 4px;">
                <button type="button" class="button button-secondary jj-refresh-colors" data-palette-type="brand" style="margin-bottom: 8px;">
                    <span class="dashicons dashicons-update" style="margin-top: 4px;"></span>
                    <?php _e( '새로고침', 'jj-style-guide' ); ?>
                </button>
                <p class="description" style="margin: 0; font-size: 13px;">
                    <?php _e( '새로고침을 눌러 현재 Customizer에서 설정된 브랜드 팔레트 색상을 한꺼번에 불러옵니다.', 'jj-style-guide' ); ?>
                </p>
                <span class="spinner" style="float: none; margin-left: 10px;"></span>
            </div>
            <?php endif; ?>

            <!-- [UI/UX] 빠른 시작: 추천 팔레트 + 인라인 프리뷰 -->
            <div class="jj-palette-quickstart">
                <h3><?php _e( '빠른 시작: 추천 팔레트', 'jj-style-guide' ); ?></h3>
                <p class="description" style="margin-top: 6px;">
                    <?php _e( '텍스트 입력보다 “보이는 팔레트”로 시작하세요. 프리셋을 선택하고 적용하면, 버튼/링크/배경/텍스트 조합이 즉시 정렬됩니다. (언제든 개별 필드에서 미세 조정 가능)', 'jj-style-guide' ); ?>
                </p>

                <div id="jj-palette-presets"></div>

                <div id="jj-palette-inline-preview" class="jj-palette-inline-preview" aria-label="<?php esc_attr_e( '팔레트 미리보기', 'jj-style-guide' ); ?>">
                    <div class="jj-prev-surface">
                        <div class="jj-prev-title"><?php _e( '실시간 미리보기', 'jj-style-guide' ); ?></div>
                        <p class="jj-prev-desc">
                            <?php _e( '배경/본문/링크/버튼이 실제로 어떻게 보이는지 이 카드에서 즉시 확인하세요. 링크는 ', 'jj-style-guide' ); ?>
                            <a href="#" onclick="return false;"><?php _e( '이런 느낌', 'jj-style-guide' ); ?></a>
                            <?php _e( '입니다.', 'jj-style-guide' ); ?>
                        </p>
                        <div class="jj-prev-actions">
                            <button type="button" class="jj-prev-btn primary" onclick="return false;"><?php _e( 'Primary 버튼', 'jj-style-guide' ); ?></button>
                            <button type="button" class="jj-prev-btn secondary" onclick="return false;"><?php _e( 'Secondary 버튼', 'jj-style-guide' ); ?></button>
                        </div>
                        <div class="jj-prev-kv">
                            <span><?php _e( 'Primary', 'jj-style-guide' ); ?> <code data-jj-color="primary">#</code></span>
                            <span><?php _e( 'Secondary', 'jj-style-guide' ); ?> <code data-jj-color="secondary">#</code></span>
                            <span><?php _e( 'BG', 'jj-style-guide' ); ?> <code data-jj-color="bg">#</code></span>
                            <span><?php _e( 'Text', 'jj-style-guide' ); ?> <code data-jj-color="text">#</code></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="jj-style-guide-grid jj-grid-4-col">
                <div class="jj-control-group jj-color-card">
                    <label for="jj-brand-primary_color">Primary Color</label>
                    <input type="text" 
                           id="jj-brand-primary_color" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[brand][primary_color]" 
                           value="<?php echo esc_attr( $options['palettes']['brand']['primary_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <p class="description">
                        <?php _e( '브랜드의 핵심 색상입니다. 버튼, 링크, 폼 등 주요 구성 요소의 기본 기준이 됩니다.', 'jj-style-guide' ); ?>
                    </p>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-brand-primary_color_hover">Primary Color (Hover)</label>
                    <input type="text" 
                           id="jj-brand-primary_color_hover" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[brand][primary_color_hover]" 
                           value="<?php echo esc_attr( $options['palettes']['brand']['primary_color_hover'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-brand-secondary_color">Secondary Color</label>
                    <input type="text" 
                           id="jj-brand-secondary_color" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[brand][secondary_color]" 
                           value="<?php echo esc_attr( $options['palettes']['brand']['secondary_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-brand-secondary_color_hover">Secondary Color (Hover)</label>
                    <input type="text" 
                           id="jj-brand-secondary_color_hover" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[brand][secondary_color_hover]" 
                           value="<?php echo esc_attr( $options['palettes']['brand']['secondary_color_hover'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                </div>
            </div>

            <div class="jj-fieldset-group jj-brand-palette-tools" style="margin-top: 24px;">
                <h3><?php _e( '브랜드 팔레트 일괄 적용', 'jj-style-guide' ); ?></h3>
                <p class="description">
                    <?php _e( '아래 버튼을 사용하면 현재 설정된 브랜드 팔레트 값을 기반으로 버튼, 폼, 링크 색상을 한 번에 맞출 수 있습니다. 적용 후에도 세부 값은 개별 섹션에서 자유롭게 조정하실 수 있습니다.', 'jj-style-guide' ); ?>
                </p>
                <button type="button" class="button button-secondary jj-apply-brand-palette-to-components">
                    <?php _e( '브랜드 팔레트 기준으로 버튼/폼/링크 색상 맞추기', 'jj-style-guide' ); ?>
                </button>
                <span class="spinner"></span>
            </div>

            <?php if ( ! $version_features || $version_features->can_use_section_export_import() ) : ?>
            <div class="jj-section-export-import-control" style="margin-top: 24px; padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
                <h3 style="margin-top: 0;"><?php _e( '브랜드 팔레트 내보내기/불러오기', 'jj-style-guide' ); ?></h3>
                <p class="description" style="margin-bottom: 10px;">
                    <?php _e( '브랜드 팔레트 설정만 선택적으로 내보내거나 불러올 수 있습니다.', 'jj-style-guide' ); ?>
                </p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="button" class="button button-secondary jj-export-section" data-section-type="palette" data-section-subtype="brand">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                        <?php _e( '브랜드 팔레트 내보내기', 'jj-style-guide' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-section" data-section-type="palette" data-section-subtype="brand">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span>
                        <?php _e( '브랜드 팔레트 불러오기', 'jj-style-guide' ); ?>
                    </button>
                </div>
                <input type="file" class="jj-section-import-file" data-section-type="palette" data-section-subtype="brand" accept=".json" style="display: none;">
                <span class="spinner" style="float: none; margin-left: 10px;"></span>
            </div>
            <?php elseif ( $version_features ) : ?>
            <div class="jj-upgrade-prompt" style="margin-top: 24px; padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
                <p class="description" style="margin: 0;">
                    <?php echo wp_kses_post( $version_features->get_upgrade_prompt( 'export_import_section' ) ); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( $tab_enabled_system ) : ?>
        <div class="jj-tab-content <?php echo ( $first_enabled_tab === 'system' ) ? 'is-active' : ''; ?>" data-tab-content="system">
            <?php if ( ! $version_features || $version_features->can_use_section_refresh() ) : ?>
            <div class="jj-section-refresh-control" style="margin-bottom: 20px; padding: 15px; background: #f0f6fc; border: 1px solid #c3c4c7; border-radius: 4px;">
                <button type="button" class="button button-secondary jj-refresh-colors" data-palette-type="system" style="margin-bottom: 8px;">
                    <span class="dashicons dashicons-update" style="margin-top: 4px;"></span>
                    <?php _e( '새로고침', 'jj-style-guide' ); ?>
                </button>
                <p class="description" style="margin: 0; font-size: 13px;">
                    <?php _e( '새로고침을 눌러 현재 Customizer에서 설정된 시스템 팔레트 색상을 한꺼번에 불러옵니다.', 'jj-style-guide' ); ?>
                </p>
                <span class="spinner" style="float: none; margin-left: 10px;"></span>
            </div>
            <?php endif; ?>
            <div class="jj-style-guide-grid jj-grid-4-col">
                <div class="jj-control-group jj-color-card">
                    <label for="jj-sys-site_bg"><?php _e( '사이트 배경 (Site Background)', 'jj-style-guide' ); ?></label>
                    <input type="text" 
                           id="jj-sys-site_bg" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[system][site_bg]" 
                           value="<?php echo esc_attr( $system_palettes['site_bg'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <p class="description">
                        <?php
                        /* translators: %s: active theme name */
                        printf(
                            __( 'body 태그의 배경색입니다. (예: 현재 활성화된 \'%s\' 테마의 사이트 배경 색상입니다. \'사용자 정의 > 색상\'에서 확인하실 수 있습니다.)', 'jj-style-guide' ),
                            esc_html( $jj_active_theme_name ?? '' )
                        );
                        ?>
                    </p>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-sys-content_bg"><?php _e( '콘텐츠 배경 (Content Background)', 'jj-style-guide' ); ?></label>
                    <input type="text" 
                           id="jj-sys-content_bg" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[system][content_bg]" 
                           value="<?php echo esc_attr( $system_palettes['content_bg'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <p class="description">
                        <?php
                        /* translators: %s: active theme name */
                        printf(
                            __( '메인 콘텐츠 영역의 배경색입니다. (예: 현재 활성화된 \'%s\' 테마의 콘텐츠/본문 배경 색상입니다. \'사용자 정의 > 색상\'에서 확인하실 수 있습니다.)', 'jj-style-guide' ),
                            esc_html( $jj_active_theme_name ?? '' )
                        );
                        ?>
                    </p>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-sys-text"><?php _e( '본문 텍스트 (Base Text)', 'jj-style-guide' ); ?></label>
                    <input type="text" 
                           id="jj-sys-text" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[system][text_color]" 
                           value="<?php echo esc_attr( $system_palettes['text_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <p class="description">
                        <?php
                        /* translators: %s: active theme name */
                        printf(
                            __( '기본 텍스트(p, li 등)의 색상입니다. (예: 현재 활성화된 \'%s\' 테마의 기본 텍스트/본문 색상입니다. \'사용자 정의 > 색상\'에서 확인하실 수 있습니다.)', 'jj-style-guide' ),
                            esc_html( $jj_active_theme_name ?? '' )
                        );
                        ?>
                    </p>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-sys-link"><?php _e( '링크 (Links Color)', 'jj-style-guide' ); ?></label>
                    <input type="text" 
                           id="jj-sys-link" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[system][link_color]" 
                           value="<?php echo esc_attr( $system_palettes['link_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <p class="description">
                        <?php
                        /* translators: %s: active theme name */
                        printf(
                            __( '기본 링크(a) 태그의 색상입니다. (예: 현재 활성화된 \'%s\' 테마의 링크/포인트 색상입니다. \'사용자 정의 > 색상\'에서 확인하실 수 있습니다.)', 'jj-style-guide' ),
                            esc_html( $jj_active_theme_name ?? '' )
                        );
                        ?>
                    </p>
                </div>
            </div>

            <?php if ( ! $version_features || $version_features->can_use_section_export_import() ) : ?>
            <div class="jj-section-export-import-control" style="margin-top: 24px; padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
                <h3 style="margin-top: 0;"><?php _e( '시스템 팔레트 내보내기/불러오기', 'jj-style-guide' ); ?></h3>
                <p class="description" style="margin-bottom: 10px;">
                    <?php _e( '시스템 팔레트 설정만 선택적으로 내보내거나 불러올 수 있습니다.', 'jj-style-guide' ); ?>
                </p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="button" class="button button-secondary jj-export-section" data-section-type="palette" data-section-subtype="system">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                        <?php _e( '시스템 팔레트 내보내기', 'jj-style-guide' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-section" data-section-type="palette" data-section-subtype="system">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span>
                        <?php _e( '시스템 팔레트 불러오기', 'jj-style-guide' ); ?>
                    </button>
                </div>
                <input type="file" class="jj-section-import-file" data-section-type="palette" data-section-subtype="system" accept=".json" style="display: none;">
                <span class="spinner" style="float: none; margin-left: 10px;"></span>
            </div>
            <?php elseif ( $version_features ) : ?>
            <div class="jj-upgrade-prompt" style="margin-top: 24px; padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
                <p class="description" style="margin: 0;">
                    <?php echo wp_kses_post( $version_features->get_upgrade_prompt( 'export_import_section' ) ); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( $tab_enabled_alternative ) : ?>
        <div class="jj-tab-content <?php echo ( $first_enabled_tab === 'alternative' ) ? 'is-active' : ''; ?>" data-tab-content="alternative">
            <?php
            // 브랜드/시스템 팔레트 값 가져오기
            $brand_palettes = $options['palettes']['brand'] ?? array();
            $alternative_palettes = $options['palettes']['alternative'] ?? array();
            
            // 다크 모드/라이트 모드 색상 계산 함수
            if ( ! function_exists( 'jj_calculate_dark_mode_color' ) ) {
                function jj_calculate_dark_mode_color( $hex ) {
                    if ( empty( $hex ) ) return '';
                    $hex = str_replace( '#', '', $hex );
                    if ( strlen( $hex ) !== 6 ) return '';
                    $r = hexdec( substr( $hex, 0, 2 ) );
                    $g = hexdec( substr( $hex, 2, 2 ) );
                    $b = hexdec( substr( $hex, 4, 2 ) );
                    // 어두운 색상으로 변환 (20% 어둡게)
                    $r = max( 0, min( 255, $r * 0.8 ) );
                    $g = max( 0, min( 255, $g * 0.8 ) );
                    $b = max( 0, min( 255, $b * 0.8 ) );
                    return '#' . str_pad( dechex( round( $r ) ), 2, '0', STR_PAD_LEFT ) . 
                           str_pad( dechex( round( $g ) ), 2, '0', STR_PAD_LEFT ) . 
                           str_pad( dechex( round( $b ) ), 2, '0', STR_PAD_LEFT );
                }
            }
            if ( ! function_exists( 'jj_calculate_light_mode_color' ) ) {
                function jj_calculate_light_mode_color( $hex ) {
                    if ( empty( $hex ) ) return '';
                    $hex = str_replace( '#', '', $hex );
                    if ( strlen( $hex ) !== 6 ) return '';
                    $r = hexdec( substr( $hex, 0, 2 ) );
                    $g = hexdec( substr( $hex, 2, 2 ) );
                    $b = hexdec( substr( $hex, 4, 2 ) );
                    // 밝은 색상으로 변환 (20% 밝게)
                    $r = max( 0, min( 255, $r + (255 - $r) * 0.2 ) );
                    $g = max( 0, min( 255, $g + (255 - $g) * 0.2 ) );
                    $b = max( 0, min( 255, $b + (255 - $b) * 0.2 ) );
                    return '#' . str_pad( dechex( round( $r ) ), 2, '0', STR_PAD_LEFT ) . 
                           str_pad( dechex( round( $g ) ), 2, '0', STR_PAD_LEFT ) . 
                           str_pad( dechex( round( $b ) ), 2, '0', STR_PAD_LEFT );
                }
            }
            ?>
            <div class="jj-alternative-palette-info" style="margin-bottom: 24px; padding: 15px; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 4px;">
                <h3 style="margin-top: 0;"><?php _e( '브랜드/시스템 팔레트 참조', 'jj-style-guide' ); ?></h3>
                <p class="description" style="margin-bottom: 15px;">
                    <?php _e( '현재 설정된 브랜드 및 시스템 팔레트 값을 참고하여 대안 색상을 설정할 수 있습니다. 다크 모드/라이트 모드 색상은 자동 계산되며, 아래 버튼을 클릭하여 적용하거나 수동으로 입력할 수 있습니다.', 'jj-style-guide' ); ?>
                </p>
                <div class="jj-palette-reference" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <?php if ( ! empty( $brand_palettes['primary_color'] ) ) : ?>
                    <div style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 3px;">
                        <strong><?php _e( '브랜드 Primary', 'jj-style-guide' ); ?></strong>
                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                            <div style="width: 30px; height: 30px; background: <?php echo esc_attr( $brand_palettes['primary_color'] ); ?>; border: 1px solid #ddd; border-radius: 3px;"></div>
                            <code style="font-size: 12px;"><?php echo esc_html( $brand_palettes['primary_color'] ); ?></code>
                        </div>
                        <button type="button" class="button button-small jj-copy-color" data-color="<?php echo esc_attr( $brand_palettes['primary_color'] ); ?>" style="margin-top: 5px; font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '복사', 'jj-style-guide' ); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $system_palettes['site_bg'] ) ) : ?>
                    <div style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 3px;">
                        <strong><?php _e( '시스템 Site BG', 'jj-style-guide' ); ?></strong>
                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                            <div style="width: 30px; height: 30px; background: <?php echo esc_attr( $system_palettes['site_bg'] ); ?>; border: 1px solid #ddd; border-radius: 3px;"></div>
                            <code style="font-size: 12px;"><?php echo esc_html( $system_palettes['site_bg'] ); ?></code>
                        </div>
                        <button type="button" class="button button-small jj-copy-color" data-color="<?php echo esc_attr( $system_palettes['site_bg'] ); ?>" style="margin-top: 5px; font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '복사', 'jj-style-guide' ); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="jj-style-guide-grid jj-grid-4-col">
                <div class="jj-control-group jj-color-card">
                    <label for="jj-alt-primary"><?php _e( 'Primary Color', 'jj-style-guide' ); ?></label>
                    <input type="text" 
                           id="jj-alt-primary" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[alternative][primary_color]" 
                           value="<?php echo esc_attr( $alternative_palettes['primary_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['primary_color'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color'] ); ?>"
                                data-target="#jj-alt-primary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'jj-style-guide' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color'] ); ?>"
                                data-target="#jj-alt-primary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'jj-style-guide' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <p class="description" style="margin-top: 8px;">
                        <?php _e( '브랜드 Primary의 대안 색상입니다. 다크 모드/라이트 모드 또는 특정 페이지용으로 사용할 수 있습니다.', 'jj-style-guide' ); ?>
                    </p>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-alt-primary-hover"><?php _e( 'Primary Color (Hover)', 'jj-style-guide' ); ?></label>
                    <input type="text" 
                           id="jj-alt-primary-hover" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[alternative][primary_color_hover]" 
                           value="<?php echo esc_attr( $alternative_palettes['primary_color_hover'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['primary_color_hover'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color_hover'] ); ?>"
                                data-target="#jj-alt-primary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'jj-style-guide' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color_hover'] ); ?>"
                                data-target="#jj-alt-primary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'jj-style-guide' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-alt-secondary"><?php _e( 'Secondary Color', 'jj-style-guide' ); ?></label>
                    <input type="text" 
                           id="jj-alt-secondary" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[alternative][secondary_color]" 
                           value="<?php echo esc_attr( $alternative_palettes['secondary_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['secondary_color'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color'] ); ?>"
                                data-target="#jj-alt-secondary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'jj-style-guide' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color'] ); ?>"
                                data-target="#jj-alt-secondary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'jj-style-guide' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-alt-secondary-hover"><?php _e( 'Secondary Color (Hover)', 'jj-style-guide' ); ?></label>
                    <input type="text" 
                           id="jj-alt-secondary-hover" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[alternative][secondary_color_hover]" 
                           value="<?php echo esc_attr( $alternative_palettes['secondary_color_hover'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['secondary_color_hover'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color_hover'] ); ?>"
                                data-target="#jj-alt-secondary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'jj-style-guide' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color_hover'] ); ?>"
                                data-target="#jj-alt-secondary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'jj-style-guide' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ( $tab_enabled_another ) : ?>
        <div class="jj-tab-content <?php echo ( $first_enabled_tab === 'another' ) ? 'is-active' : ''; ?>" data-tab-content="another">
            <?php
            $another_palettes = $options['palettes']['another'] ?? array();
            ?>
            <div class="jj-alternative-palette-info" style="margin-bottom: 24px; padding: 15px; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 4px;">
                <h3 style="margin-top: 0;"><?php _e( '브랜드/시스템 팔레트 참조', 'jj-style-guide' ); ?></h3>
                <p class="description" style="margin-bottom: 15px;">
                    <?php _e( '특정 페이지나 플러그인 전용 색상으로 사용할 수 있는 팔레트입니다. 브랜드/시스템 팔레트 값을 기반으로 대안 색상을 설정하거나 후보군으로 컬러셋을 매핑해 둘 수 있습니다.', 'jj-style-guide' ); ?>
                </p>
                <div class="jj-palette-reference" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <?php if ( ! empty( $brand_palettes['primary_color'] ) ) : ?>
                    <div style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 3px;">
                        <strong><?php _e( '브랜드 Primary', 'jj-style-guide' ); ?></strong>
                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                            <div style="width: 30px; height: 30px; background: <?php echo esc_attr( $brand_palettes['primary_color'] ); ?>; border: 1px solid #ddd; border-radius: 3px;"></div>
                            <code style="font-size: 12px;"><?php echo esc_html( $brand_palettes['primary_color'] ); ?></code>
                        </div>
                        <button type="button" class="button button-small jj-copy-color" data-color="<?php echo esc_attr( $brand_palettes['primary_color'] ); ?>" style="margin-top: 5px; font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '복사', 'jj-style-guide' ); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $system_palettes['site_bg'] ) ) : ?>
                    <div style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 3px;">
                        <strong><?php _e( '시스템 Site BG', 'jj-style-guide' ); ?></strong>
                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                            <div style="width: 30px; height: 30px; background: <?php echo esc_attr( $system_palettes['site_bg'] ); ?>; border: 1px solid #ddd; border-radius: 3px;"></div>
                            <code style="font-size: 12px;"><?php echo esc_html( $system_palettes['site_bg'] ); ?></code>
                        </div>
                        <button type="button" class="button button-small jj-copy-color" data-color="<?php echo esc_attr( $system_palettes['site_bg'] ); ?>" style="margin-top: 5px; font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '복사', 'jj-style-guide' ); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="jj-style-guide-grid jj-grid-4-col">
                <div class="jj-control-group jj-color-card">
                    <label for="jj-another-primary"><?php _e( 'Primary Color', 'jj-style-guide' ); ?></label>
                    <input type="text" 
                           id="jj-another-primary" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[another][primary_color]" 
                           value="<?php echo esc_attr( $another_palettes['primary_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['primary_color'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color'] ); ?>"
                                data-target="#jj-another-primary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'jj-style-guide' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color'] ); ?>"
                                data-target="#jj-another-primary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'jj-style-guide' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <p class="description" style="margin-top: 8px;">
                        <?php _e( '특정 페이지나 플러그인용 Primary 색상 후보입니다.', 'jj-style-guide' ); ?>
                    </p>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-another-primary-hover"><?php _e( 'Primary Color (Hover)', 'jj-style-guide' ); ?></label>
                    <input type="text" 
                           id="jj-another-primary-hover" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[another][primary_color_hover]" 
                           value="<?php echo esc_attr( $another_palettes['primary_color_hover'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['primary_color_hover'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color_hover'] ); ?>"
                                data-target="#jj-another-primary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'jj-style-guide' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['primary_color_hover'] ); ?>"
                                data-target="#jj-another-primary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'jj-style-guide' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-another-secondary"><?php _e( 'Secondary Color', 'jj-style-guide' ); ?></label>
                    <input type="text" 
                           id="jj-another-secondary" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[another][secondary_color]" 
                           value="<?php echo esc_attr( $another_palettes['secondary_color'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['secondary_color'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color'] ); ?>"
                                data-target="#jj-another-secondary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'jj-style-guide' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color'] ); ?>"
                                data-target="#jj-another-secondary"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'jj-style-guide' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="jj-control-group jj-color-card">
                    <label for="jj-another-secondary-hover"><?php _e( 'Secondary Color (Hover)', 'jj-style-guide' ); ?></label>
                    <input type="text" 
                           id="jj-another-secondary-hover" 
                           class="jj-color-field jj-data-field" 
                           data-setting-key="palettes[another][secondary_color_hover]" 
                           value="<?php echo esc_attr( $another_palettes['secondary_color_hover'] ?? '' ); ?>">
                    <div class="jj-color-preview"></div>
                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                        <?php if ( ! empty( $brand_palettes['secondary_color_hover'] ) ) : ?>
                        <button type="button" class="button button-small jj-apply-dark-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color_hover'] ); ?>"
                                data-target="#jj-another-secondary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '다크 모드', 'jj-style-guide' ); ?>
                        </button>
                        <button type="button" class="button button-small jj-apply-light-mode" 
                                data-source="<?php echo esc_attr( $brand_palettes['secondary_color_hover'] ); ?>"
                                data-target="#jj-another-secondary-hover"
                                style="font-size: 11px; padding: 2px 8px; height: 24px;">
                            <?php _e( '라이트 모드', 'jj-style-guide' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php
        // [v5.0.0] '임시' '팔레트' '탭' '내용' '로드' - 탭 활성화/비활성화 체크
        if ( $tab_enabled_temp ) {
            $temp_palette_path = defined( 'JJ_STYLE_GUIDE_PATH' ) ? JJ_STYLE_GUIDE_PATH : plugin_dir_path( __FILE__ ) . '../../../';
            $temp_palette_file = $temp_palette_path . 'includes/editor-views/view-section-temp-palette.php';
            if ( file_exists( $temp_palette_file ) ) {
                include_once $temp_palette_file;
            } else {
                // 상대 경로로 다시 시도
                include_once __DIR__ . '/view-section-temp-palette.php';
            }
        }
        // [v5.0.0] 탭 활성화/비활성화가 적용되어 else 블록은 제거
        ?>
    </div>
</div>
```

```php
<?php
/**
 * Plugin Name:       ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager (Master)
 * Plugin URI:        https://j-j-labs.com
 * Description:       WordPress 웹사이트의 모든 스타일 요소(색상 팔레트, 타이포그래피, 버튼, 폼)를 중앙에서 일관되게 관리하는 통합 스타일 관리 플러그인입니다. Free 버전은 기본적인 스타일 관리 기능을 제공하며, 브랜드 일관성을 유지하고 디자인 시스템을 효율적으로 운영할 수 있습니다. Pro 버전 플러그인을 함께 설치하면 Basic, Premium, Unlimited 기능을 사용할 수 있습니다. WordPress Customizer와 완벽 통합되어 실시간 미리보기와 함께 직관적인 스타일 관리가 가능합니다.
 * Version:           8.0.0
 * Author:            3J Labs
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://poetryflow.blog
 * Text Domain:       acf-css-really-simple-style-management-center
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ⚠️ 중요: Git 경로와 로컬 경로는 완전히 별개입니다
 * 
 * - 로컬 작업 경로: C:\Users\computer\OneDrive\Desktop\클라우드 백업\OneDrive\문서\jj-css-premium
 * - Git worktree 경로: C:\Users\computer\.cursor\worktrees\jj-css-premium\tci
 * - 두 경로는 서로 독립적이며, Git에서 작업한 내용은 반드시 로컬 경로에 동기화해야 함
 * - 자세한 내용은 DEVELOPMENT_PRINCIPLES.md의 "Git 및 로컬 경로 관리 원칙" 섹션 참조
 */

// 플러그인 상수 정의
// [v5.1.6] 전면 재검토 및 오류 방지: 안전한 파일 로더 추가, 모든 버전의 require_once 안전 처리, 결제 유도 문구 추가, 플러그인 목록 페이지 바로가기 링크 추가
// [v5.1.6] Comprehensive review and error prevention: Safe file loader added, all versions' require_once safely handled, purchase prompts added, plugin list page quick links added
// [v1.0.2] 모든 버전 플러그인 활성화 안전성 최종 확보, WordPress 함수 호출 안전 처리
define( 'JJ_STYLE_GUIDE_VERSION', '8.0.0' ); // [v7.0.0] Operation Deep Dive - Stability & Reliability Overhaul

// WordPress 함수가 로드되었는지 확인 후 상수 정의
if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    if ( function_exists( 'plugin_dir_path' ) ) {
        define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 직접 경로 계산
        define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
    }
}

if ( ! defined( 'JJ_STYLE_GUIDE_URL' ) ) {
    if ( function_exists( 'plugin_dir_url' ) ) {
        define( 'JJ_STYLE_GUIDE_URL', plugin_dir_url( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 기본값 사용 (나중에 업데이트됨)
        define( 'JJ_STYLE_GUIDE_URL', '' );
    }
}
define( 'JJ_STYLE_GUIDE_OPTIONS_KEY', 'jj_style_guide_options' );
define( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY', 'jj_style_guide_temp_options' );
define( 'JJ_STYLE_GUIDE_ADMIN_TEXT_KEY', 'jj_style_guide_admin_texts' );
define( 'JJ_STYLE_GUIDE_PAGE_SLUG', 'acf-css-really-simple-style-guide' );
define( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY', 'jj_style_guide_cockpit_page_id' );
define( 'JJ_STYLE_GUIDE_DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
// Pro 버전 활성화 코드 확인 (plugins_loaded 훅에서 처리)
// WordPress가 로드되기 전에는 기본값 사용
define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', 'MASTER' ); // 기본값, plugins_loaded에서 업데이트됨
define( 'JJ_STYLE_GUIDE_EDITION', 'master' ); // 기본 에디션은 free

// [v3.8.0] 환경 구분
if ( ! defined( 'JJ_STYLE_GUIDE_ENV' ) ) {
    define( 'JJ_STYLE_GUIDE_ENV', 'live' );
}

// [v5.3.7] Free 버전은 독립적으로 작동합니다. Master 버전 의존성 제거
// Free 버전은 자체 includes 폴더에 필요한 모든 파일을 포함합니다.

// [v5.4.1] 안전한 파일 로더 적용
$safe_loader = null;
try {
    $safe_loader_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-loader.php';
    if ( file_exists( $safe_loader_path ) ) {
        require_once $safe_loader_path;
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'instance' ) ) {
            $safe_loader = JJ_Safe_Loader::instance();
        }
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Failed: ' . $e->getMessage() );
} catch ( Error $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Fatal Error: ' . $e->getMessage() );
}

// 라이센스 매니저 로드
if ( $safe_loader ) {
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php', false );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-version-features.php', true );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-code-integrity-monitor.php', true );
}

// 핵심 로직 파일 로드 (Master 전용 파일은 아직 비활성화)
try {
    if ( $safe_loader && method_exists( $safe_loader, 'safe_require' ) ) {
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-handler.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-logger.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-admin-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-backup-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/visual-command-center/class-jj-visual-command-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/ai-style-intelligence/class-jj-smart-palette.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-cloud-manager.php', true );
        // [Phase 3] 템플릿 마켓
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-template-market.php', true );
        // [Phase 5] Partner Hub
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/partner-hub/class-jj-partner-hub.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-labs-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-selector-registry.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-injector.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-options-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-edition-controller.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-theme-metadata.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-0-customizer.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-1-css-vars.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-2-php-filters.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-3-dequeue.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-sync.php', false );
        // [Phase 5.2] REST API (설정 조회/업데이트)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/api/class-jj-style-guide-rest-api.php', true );
        // [Phase 5.3] 확장 플러그인 시스템 (Extension API)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/interface-jj-style-guide-extension.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/class-jj-extension-manager.php', true );
        // [Phase 5.2] Webhooks (자동화)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/integrations/class-jj-webhook-manager.php', true );
        // [Phase 5 B] Multisite Network Control
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/multisite/class-jj-multisite-controller.php', true );
        
        // Master 전용 파일들 (일단 주석 처리)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-sync-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-memory-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-mode.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-optimizer.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-issuer.php', false );
    } else {
        // Fallback for Safe Loader missing
        require_once JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
        // ... (Fallback은 일단 생략, Safe Loader가 있다고 가정)
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Master Load Error: ' . $e->getMessage() );
}



/**
 * 활성화 훅 래퍼 함수 (치명적 오류 방지)
 * [v5.1.7] 전체 함수를 try-catch로 감싸고 상수 정의 확인 추가
 */
function jj_simple_style_guide_master_activate() {
    try {
        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // 상수가 정의되지 않은 경우 기본 경로 사용
            if ( function_exists( 'plugin_dir_path' ) ) {
                define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
            } else {
                define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
            }
        }
        
        // 활성화 매니저 클래스가 로드되지 않은 경우 로드 시도
        
        if ( ! class_exists( 'JJ_Activation_Manager' ) ) {
            $activation_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php';
            if ( file_exists( $activation_file ) ) {
                require_once $activation_file;
            } else {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 파일을 찾을 수 없습니다. 기본 활성화만 수행합니다.' );
                }
                return;
            }
        }
        
        // 클래스가 존재하는지 확인 후 활성화 실행
        if ( class_exists( 'JJ_Activation_Manager' ) && method_exists( 'JJ_Activation_Manager', 'activate' ) ) {
            try {
                JJ_Activation_Manager::activate();
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 오류 발생 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 치명적 오류 발생 - ' . $e->getMessage() );
                }
            }
        } else {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 클래스를 사용할 수 없습니다.' );
            }
        }
    } catch ( Exception $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 예외 발생 - ' . $e->getMessage() );
        }
    } catch ( Error $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 치명적 오류 발생 - ' . $e->getMessage() );
        }
    }
}
register_activation_hook( __FILE__, 'jj_simple_style_guide_master_activate' );

/**
 * Free 버전 전용 메인 컨트롤 클래스
 */
final class JJ_Simple_Style_Guide_Master {

    private static $instance = null;
    private $options = array();
    private $cockpit_page_id = 0;
    private $options_key = '';
    private $brand_colors = array();
    private $environment_map = array();
    private $adapters_config = array();
    private $license_manager = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 상수 정의 확인 후 할당
        if ( defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) {
            $this->options_key = JJ_STYLE_GUIDE_OPTIONS_KEY;
        }
        
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $this->license_manager = JJ_License_Manager::instance();
        }
        
        if ( defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) && function_exists( 'get_option' ) ) {
            $this->cockpit_page_id = (int) get_option( JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY );
        }

        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // [v5.3.7] Free 버전은 자체 config 폴더에서 로드
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
            if ( file_exists( $config_file ) ) {
                $this->adapters_config = include( $config_file );
                // [v5.5.0] Edition Controller를 통한 동적 어댑터 필터링
                if ( class_exists( 'JJ_Edition_Controller' ) ) {
                    $this->adapters_config = JJ_Edition_Controller::instance()->filter_adapters( $this->adapters_config );
                }
                
                
            }
        }

        // WordPress 함수가 로드된 후에만 훅 등록
        // [v5.1.9] plugin_basename() 안전 호출 추가
        // [v5.3.9] 모든 훅 등록을 조건문 안으로 이동하여 안전성 확보
        if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) && function_exists( 'plugin_basename' ) ) {
            add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
            add_action( 'admin_menu', array( $this, 'add_admin_menu_links' ) );
            $plugin_basename = function_exists( 'plugin_basename' ) ? plugin_basename( __FILE__ ) : basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );
            add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_plugin_settings_link' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            add_action( 'wp_ajax_jj_save_style_guide', array( $this, 'save_settings_ajax' ) );
            add_action( 'wp_ajax_jj_apply_temp_palette', array( $this, 'apply_temp_palette_ajax' ) );
            add_action( 'wp_ajax_jj_save_temp_palette', array( $this, 'save_temp_palette_ajax' ) );
            // [v3.8.0] Customizer 색상 새로고침 (팔레트 동기화)
            if ( class_exists( 'JJ_Customizer_Sync' ) ) {
                add_action( 'wp_ajax_jj_load_current_colors', array( 'JJ_Customizer_Sync', 'ajax_load_current_colors' ) );
            }
            // [v3.8.0] 섹션별 내보내기/불러오기 (팔레트 등)
            add_action( 'wp_ajax_jj_export_section', array( $this, 'ajax_export_section' ) );
            add_action( 'wp_ajax_jj_import_section', array( $this, 'ajax_import_section' ) );
            add_action( 'init', array( $this, 'load_options_and_init_strategies' ), 5 );
            add_action( 'init', array( $this, 'init_sync_manager' ), 10 );
            
            
            if ( JJ_STYLE_GUIDE_DEBUG ) {
                add_action( 'wp_footer', array( $this, 'render_debug_info' ), 999 );
                add_action( 'admin_footer', array( $this, 'render_debug_info' ), 999 );
            }
        }

        // Customizer 통합 (제한적)
        
        try {
            $customizer_manager_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php';
            if ( file_exists( $customizer_manager_path ) ) {
                require_once $customizer_manager_path;
                if ( class_exists( 'JJ_Customizer_Manager' ) ) {
                    $customizer_manager = JJ_Customizer_Manager::instance();
                    if ( method_exists( $customizer_manager, 'init' ) ) {
                        $customizer_manager->init();
                    }
                }
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }

    }

    /**
     * Free 버전: 어댑터 제한
     * 
     * @param array $config
     * @return array
     */
    private function limit_adapters_for_free( $config ) {
        // Free 버전: 테마 2개만 지원 (Twenty Twenty-Four, Twenty Twenty-Five)
        $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
        if ( isset( $config['themes'] ) && is_array( $config['themes'] ) ) {
            $config['themes'] = array_intersect_key( $config['themes'], array_flip( $allowed_themes ) );
        }

        // Free 버전: 플러그인 1개만 지원 (WooCommerce)
        $allowed_plugins = array( 'woocommerce' );
        if ( isset( $config['spokes'] ) && is_array( $config['spokes'] ) ) {
            $config['spokes'] = array_intersect_key( $config['spokes'], array_flip( $allowed_plugins ) );
        }

        return $config;
    }

    /**
     * Free 버전 워터마크 추가
     */
    public function add_free_watermark() {
        if ( ! is_admin() && current_user_can( 'manage_options' ) ) {
            echo '<div style="position: fixed; bottom: 10px; right: 10px; background: rgba(0, 0, 0, 0.7); color: #fff; padding: 8px 12px; font-size: 11px; border-radius: 4px; z-index: 99999; font-family: Arial, sans-serif;">';
            echo '<a href="' . esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ) . '" style="color: #fff; text-decoration: none;">JJ Style Guide Free</a>';
            echo '</div>';
        }
    }

    /**
     * 업그레이드 프롬프트 표시
     */
    public function show_upgrade_prompt() {
        // [Safety Lock] MASTER 또는 PARTNER 버전은 업그레이드 프롬프트 표시 안 함
        if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'partner' ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        $license_manager = null;
        $upgrade_url = 'https://j-j-labs.com'; // 기본값
        
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
                if ( $license_manager ) {
                    $upgrade_url = $license_manager->get_purchase_url( 'upgrade' );
                }
            }
        }
        ?>
        <div class="notice notice-info is-dismissible" style="background: #f0f6fc; border-left-color: #2271b1;">
            <p style="font-size: 14px; line-height: 1.8;">
                <strong><?php _e( '현재 버전을 사용 중입니다.', 'acf-css-really-simple-style-management-center' ); ?></strong><br>
                <?php _e( '더 많은 기능을 이용하려면 업그레이드하세요:', 'acf-css-really-simple-style-management-center' ); ?>
                <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" class="button button-primary" style="margin-left: 10px;">
                    <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
            </p>
            <ul style="margin: 10px 0; padding-left: 20px; list-style: disc;">
                <li><?php _e( '모든 팔레트 사용 가능 (브랜드, 시스템, 얼터너티브, 어나더, 임시)', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '모든 테마 및 플러그인 어댑터 지원', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '관리자 센터 및 실험실 센터 전체 기능', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '섹션별 내보내기/불러오기', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '고급 색상 선택 기능 (스포이드, 팔레트 불러오기)', 'acf-css-really-simple-style-management-center' ); ?></li>
            </ul>
        </div>
        <?php
    }

    public function init_sync_manager() {
        add_action( 'customize_save_after', array( $this, 'sync_from_customizer' ) );
        JJ_Sync_Manager::instance()->init( $this->options, $this->cockpit_page_id );
    }

    /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * Free 버전: 스타일 센터만 표시
     * [v5.1.9] 안전성 강화 - try-catch 추가
     */
            /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * [Phase 4.99] 퀵 링크 확장: 설정 | 스타일 | 메뉴 | 비주얼
     */
    public function add_plugin_settings_link( $links ) {
        try {
            if ( ! function_exists( 'admin_url' ) || ! function_exists( 'esc_url' ) ) {
                return $links;
            }
            
            $new_links = array();
            
            // 1. 설정 (Admin Center 메인)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ) . '" style="font-weight: 600;">' . __( 'Settings', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 2. 스타일 (Style Guide)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ) ) . '">' . __( 'Styles', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 3. 어드민 메뉴 (Admin Menu Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#admin-menu' ) ) . '">' . __( 'Menu', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 4. 비주얼 (Visual Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#visual' ) ) . '">' . __( 'Visual', 'acf-css-really-simple-style-management-center' ) . '</a>';

            // 기존 링크와 병합
            return array_merge( $new_links, $links );
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 오류 - ' . $e->getMessage() );
            }
            return $links;
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 치명적 오류 - ' . $e->getMessage() );
            }
            return $links;
        }
    }

    public function add_admin_menu_page() {
        add_options_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );
    }

    public function add_admin_menu_links() {
        // [Phase 4.5/4.99] "모양/도구/설정" 어디서나 동일한 화면으로 진입할 수 있도록 서브메뉴 추가
        add_theme_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        add_management_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        if ( $this->cockpit_page_id ) {
            add_submenu_page(
                'themes.php',
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                'manage_options',
                'post.php?post=' . $this->cockpit_page_id . '&action=edit',
                '',
                100
            );
        }
    }

    public function enqueue_admin_scripts( $hook_suffix ) {
        $is_settings_page = in_array(
            $hook_suffix,
            array(
                'settings_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'appearance_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'tools_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
            ),
            true
        );

        $is_cockpit_page = false;
        if ( 'post.php' === $hook_suffix ) {
            global $post;
            if ( $post && (int) $post->ID === $this->cockpit_page_id ) {
                $is_cockpit_page = true;
            }
        }

        if ( ! $is_settings_page && ! $is_cockpit_page ) {
            return;
        }

        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;

        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        // [v5.3.7] Free 버전은 자체 assets 폴더에서 로드
        wp_enqueue_style( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-editor.css', array( 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION );
        wp_enqueue_script( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-editor.js', array( 'jquery', 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION, true );
        // [UI/UX] 팔레트 프리셋 + 인라인 프리뷰
        wp_enqueue_style( 'jj-style-guide-presets', JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-presets.css', array( 'jj-style-guide-editor' ), JJ_STYLE_GUIDE_VERSION );
        wp_enqueue_script( 'jj-style-guide-presets', JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-presets.js', array( 'jj-style-guide-editor', 'jquery' ), JJ_STYLE_GUIDE_VERSION, true );

        $preview_page_id = $this->cockpit_page_id;
        $preview_url = $preview_page_id ? get_permalink( $preview_page_id ) : '';

        wp_localize_script(
            'jj-style-guide-editor',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'temp_palette_nonce' => wp_create_nonce( 'jj_temp_palette_nonce' ),
                'settings' => $this->options,
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'edition'  => 'free', // Free 버전 표시
                'i18n'     => array(
                    'saving' => __( '저장 중...', 'acf-css-really-simple-style-management-center' ),
                    'saved'  => __( '스타일 저장됨', 'acf-css-really-simple-style-management-center' ),
                    'applied' => __( '임시 팔레트 적용됨! \'스타일 저장\'을 눌러야 최종 반영됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'error'  => __( '오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
                    'confirm_apply' => __( '정말로 \'임시 팔레트\'의 값으로 덮어쓰시겠습니까?\n이 작업은 즉시 UI에 반영되지만, \n\'스타일 저장\' 버튼을 눌러야 최종 저장됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'preview_url' => $preview_url ? esc_url( $preview_url ) : '',
                    'preview_page_id' => $preview_page_id,
                ),
            )
        );
    }

    public function render_style_guide_page() {
        try {
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options      = $options;
            $temp_options       = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY );
            $options            = $this->options;
            $cockpit_page_id    = $this->cockpit_page_id;
            $preview_url        = $cockpit_page_id ? get_permalink( $cockpit_page_id ) : '';

        // 옵션이 비어있으면 기본값 설정
        if ( empty( $this->options ) ) {
            $this->options = array(
                'palettes' => array(
                    'brand' => array(
                        'primary_color' => '',
                        'primary_color_hover' => '',
                        'secondary_color' => '',
                        'secondary_color_hover' => '',
                    ),
                    'system' => array(), // Free 버전: 시스템 팔레트 비활성화
                    'alternative' => array(), // Free 버전: 얼터너티브 팔레트 비활성화
                    'another' => array(), // Free 버전: 어나더 팔레트 비활성화
                ),
                'typography' => array(),
                'buttons' => array(),
                'forms' => array(),
                'contexts' => array(),
            );
            $options = $this->options;
        }
        ?>
        <div id="jj-style-guide-wrapper" class="wrap jj-style-guide-wrap" style="background: #f0f0f1; margin: 0; padding: 0;">
            <form id="jj-style-guide-form">

                <div class="jj-style-guide-header" style="top: 32px; background: #fff; padding: 15px 25px; border-bottom: 1px solid #c3c4c7; z-index: 999; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h1 style="margin: 0; font-size: 24px;">
                            <?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?> (v<?php echo esc_html( JJ_STYLE_GUIDE_VERSION ); ?>) 
                            <?php if ( ! class_exists("JJ_Edition_Controller") || JJ_Edition_Controller::instance()->get_current_edition() === "free" ) : ?><span style="background: #d63638; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 14px; font-weight: 600;">Free</span><?php endif; ?>
                        </h1>
                        <?php
                        $license_manager = null;
                        $purchase_url = 'https://j-j-labs.com'; // 기본값
                        
                        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                            if ( class_exists( 'JJ_License_Manager' ) ) {
                                $license_manager = JJ_License_Manager::instance();
                                if ( $license_manager ) {
                                    $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                                }
                            }
                        }
                        ?>
                        <?php if ( ! class_exists("JJ_Edition_Controller") || ! JJ_Edition_Controller::instance()->is_at_least("partner") ) : ?><a href="<?php echo esc_url( $purchase_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary" style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px;">
                            <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                        </a><?php endif; ?>
                        <?php if ( $preview_url ) : ?>
                        <button type="button" 
                                class="button button-secondary" 
                                id="jj-preview-button"
                                style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> <?php _e( '실시간 미리보기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="jj-header-actions" style="display: flex; align-items: center; gap: 10px;">
                        <span id="jj-save-success" style="display: none; vertical-align: middle; color: #2271b1; font-weight: 600; margin-right: 15px;"></span>
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-save-style-guide" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
                <div class="jj-style-guide-content" style="margin: 20px auto; background: #ffffff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 1px;">
                    <?php
                    // Free 버전: 제한된 섹션만 표시
                    // [v5.3.7] Free 버전은 자체 includes/editor-views 폴더에서 로드
                    $sections_map = array(
                        'colors'     => 'includes/editor-views/view-section-colors.php',
                        'typography' => 'includes/editor-views/view-section-typography.php',
                        'buttons'    => 'includes/editor-views/view-section-buttons.php',
                        'forms'      => 'includes/editor-views/view-section-forms.php',
                    );

                    // Free 버전: 섹션 순서 고정
                    $sections_to_render = array();
                    foreach ( $sections_map as $slug => $relative_path ) {
                        $sections_to_render[] = array(
                            'slug' => $slug,
                            'file' => JJ_STYLE_GUIDE_PATH . $relative_path,
                            'order'=> 0, // 순서는 UI에서만 표시
                        );
                    }
                    ?>
                    <div id="jj-sections-sortable" class="jj-sections-sortable">
                        <?php
                        foreach ( $sections_to_render as $section ) {
                            // Free 버전: 기본 뷰 파일 사용 (제한 로직은 뷰 파일 내부에서 처리)
                            $default_file = JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-section-' . $section['slug'] . '.php';
                            if ( file_exists( $section['file'] ) ) {
                                echo '<div class="jj-section-wrapper" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $section['file'];
                                echo '</div>';
                            } elseif ( file_exists( $default_file ) ) {
                                // Free 버전 전용 뷰가 없으면 기본 뷰 사용 (제한 로직 적용 필요)
                                echo '<div class="jj-section-wrapper jj-section-free-version" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $default_file;
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="jj-style-guide-footer" style="margin: 20px auto; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: #666; font-size: 13px;">
                        <?php
                        // Master/Partner에서는 업그레이드 유도 문구를 절대 노출하지 않음
                        $show_upgrade_footer = true;
                        if ( class_exists( 'JJ_Edition_Controller' ) ) {
                            try {
                                $show_upgrade_footer = ! JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                            } catch ( Exception $e ) {
                                // ignore
                            } catch ( Error $e ) {
                                // ignore
                            }
                        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
                            $show_upgrade_footer = ! in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
                        }

                        if ( $show_upgrade_footer ) {
                            _e( '더 많은 기능을 이용하려면 업그레이드하세요.', 'acf-css-really-simple-style-management-center' );
                        }
                        ?>
                    </div>
                    <div style="text-align: right;">
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-style-guide-footer-button" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        } catch ( Exception $e ) {
            // 오류 발생 시에도 기본 페이지 구조는 표시
            ?>
            <div class="wrap" style="padding: 20px;">
                <h1><?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?></h1>
                <div class="notice notice-error">
                    <p><strong><?php _e( '오류가 발생했습니다:', 'acf-css-really-simple-style-management-center' ); ?></strong> <?php echo esc_html( $e->getMessage() ); ?></p>
                </div>
            </div>
            <?php
        }
    }

    // 나머지 메서드들은 dev 버전과 동일하지만, 기능 제한 로직 추가 필요
    // save_settings_ajax, apply_temp_palette_ajax, save_temp_palette_ajax 등
    
    public function save_settings_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        if ( ! isset( $_POST['settings'] ) || ! is_array( $_POST['settings'] ) ) {
            wp_send_json_error( array( 'message' => __( '잘못된 데이터입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // [Phase 5 B] 멀티사이트 네트워크 전용 모드에서는 사이트별 저장 차단
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $ms = JJ_Multisite_Controller::instance();
                if ( method_exists( $ms, 'is_enabled' ) && method_exists( $ms, 'allow_site_override' ) ) {
                    if ( $ms->is_enabled() && ! $ms->allow_site_override() ) {
                        wp_send_json_error( array( 'message' => __( '이 사이트는 네트워크에서 스타일을 관리 중입니다. 네트워크 관리자에서 변경해주세요.', 'acf-css-really-simple-style-management-center' ) ) );
                    }
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        $old_settings = array();
        if ( function_exists( 'get_option' ) ) {
            $old_settings = (array) get_option( $this->options_key, array() );
        }

        $raw_settings = wp_unslash( $_POST['settings'] );
        
        // 에디션별 저장 제한 (Free만 제한, Partner/Master는 무제한)
        $current_edition = 'free';
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $current_edition = (string) JJ_Edition_Controller::instance()->get_current_edition();
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_EDITION' ) ) {
            $current_edition = (string) JJ_STYLE_GUIDE_EDITION;
        }

        if ( 'free' === $current_edition ) {
            $raw_settings = $this->filter_settings_for_free( $raw_settings );
        }
        
        $sanitized_settings = $this->sanitize_options_recursive( $raw_settings );
        update_option( $this->options_key, $sanitized_settings );

        // [Phase 5.2] Webhook/자동화용 이벤트
        if ( function_exists( 'do_action' ) ) {
            do_action( 'jj_style_guide_settings_updated', $sanitized_settings, $old_settings, 'style_center_ajax' );
        }

        $this->sync_to_spokes( $sanitized_settings );

        JJ_CSS_Cache::instance()->flush();
        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * Free 버전: 저장 가능한 설정 필터링
     */
    private function filter_settings_for_free( $settings ) {
        // Free 버전: 시스템 팔레트, 얼터너티브 팔레트, 어나더 팔레트 제거
        if ( isset( $settings['palettes'] ) ) {
            unset( $settings['palettes']['system'] );
            unset( $settings['palettes']['alternative'] );
            unset( $settings['palettes']['another'] );
        }
        
        // Free 버전: 타이포그래피 H5, H6 제거
        if ( isset( $settings['typography'] ) ) {
            unset( $settings['typography']['h5'] );
            unset( $settings['typography']['h6'] );
        }
        
        // Free 버전: Secondary, Text 버튼 제거
        if ( isset( $settings['buttons'] ) ) {
            unset( $settings['buttons']['secondary'] );
            unset( $settings['buttons']['text'] );
        }
        
        return $settings;
    }

    public function apply_temp_palette_ajax() {
        check_ajax_referer( 'jj_temp_palette_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 기능 허용 여부(버전 기능/에디션)
        $can_use_temp = false;
        if ( class_exists( 'JJ_Version_Features' ) ) {
            try {
                $vf = JJ_Version_Features::instance();
                $can_use_temp = $vf ? $vf->can_use_palette( 'temp' ) : false;
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $can_use_temp = JJ_Edition_Controller::instance()->is_at_least( 'premium' ) || JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $can_use_temp = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }

        if ( ! $can_use_temp ) {
            $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
            if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
                $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
            }
            wp_send_json_error( array( 'message' => $message ) );
        }

        $target_palette = isset( $_POST['target_palette'] ) ? sanitize_key( wp_unslash( $_POST['target_palette'] ) ) : 'brand';
        if ( 'brand' !== $target_palette ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 대상 팔레트입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
        $temp_palettes = isset( $temp_options['palettes'] ) && is_array( $temp_options['palettes'] ) ? $temp_options['palettes'] : array();
        $temp_brand = isset( $temp_palettes['brand'] ) && is_array( $temp_palettes['brand'] ) ? $temp_palettes['brand'] : array();

        if ( empty( $temp_brand ) ) {
            wp_send_json_error( array( 'message' => __( '임시 팔레트에 저장된 값이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        if ( ! isset( $options['palettes'] ) || ! is_array( $options['palettes'] ) ) {
            $options['palettes'] = array();
        }
        if ( ! isset( $options['palettes']['brand'] ) || ! is_array( $options['palettes']['brand'] ) ) {
            $options['palettes']['brand'] = array();
        }

        $options['palettes']['brand'] = array_merge( (array) $options['palettes']['brand'], (array) $temp_brand );
        update_option( $this->options_key, $options );

        if ( class_exists( 'JJ_CSS_Cache' ) && method_exists( 'JJ_CSS_Cache', 'instance' ) ) {
            try {
                JJ_CSS_Cache::instance()->flush();
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        wp_send_json_success( array(
            'message' => __( '임시 팔레트가 브랜드 팔레트에 적용되었습니다.', 'acf-css-really-simple-style-management-center' ),
            'new_settings' => $options,
        ) );
    }

    public function save_temp_palette_ajax() {
        check_ajax_referer( 'jj_temp_palette_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 기능 허용 여부(버전 기능/에디션)
        $can_use_temp = false;
        if ( class_exists( 'JJ_Version_Features' ) ) {
            try {
                $vf = JJ_Version_Features::instance();
                $can_use_temp = $vf ? $vf->can_use_palette( 'temp' ) : false;
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $can_use_temp = JJ_Edition_Controller::instance()->is_at_least( 'premium' ) || JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $can_use_temp = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }

        if ( ! $can_use_temp ) {
            $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
            if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
                $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
            }
            wp_send_json_error( array( 'message' => $message ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        $brand = isset( $options['palettes']['brand'] ) && is_array( $options['palettes']['brand'] ) ? $options['palettes']['brand'] : array();

        if ( empty( $brand ) ) {
            wp_send_json_error( array( 'message' => __( '브랜드 팔레트 값이 비어 있습니다. 먼저 브랜드 색상을 입력해 주세요.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
        if ( ! isset( $temp_options['palettes'] ) || ! is_array( $temp_options['palettes'] ) ) {
            $temp_options['palettes'] = array();
        }
        $temp_options['palettes']['brand'] = $brand;

        update_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, $temp_options );

        wp_send_json_success( array(
            'message' => __( '현재 브랜드 팔레트가 임시 팔레트로 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ),
            'temp_palettes' => $temp_options['palettes'],
        ) );
    }

    /**
     * [v3.8.0] 섹션별 내보내기
     * - 현재는 팔레트(brand/system/alternative/another/temp-palette)만 지원
     * - 관리자에서 파일 다운로드(attachment)로 전달
     */
    public function ajax_export_section() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $section_type = isset( $_POST['section_type'] ) ? sanitize_key( wp_unslash( $_POST['section_type'] ) ) : '';
        $section_subtype = isset( $_POST['section_subtype'] ) ? sanitize_key( wp_unslash( $_POST['section_subtype'] ) ) : '';

        if ( 'palette' !== $section_type ) {
            wp_die( esc_html__( '지원하지 않는 섹션 타입입니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $allowed_subtypes = array( 'brand', 'system', 'alternative', 'another', 'temp' );
        if ( ! in_array( $section_subtype, $allowed_subtypes, true ) ) {
            wp_die( esc_html__( '지원하지 않는 섹션 서브타입입니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        $data = array();

        if ( 'temp' === $section_subtype ) {
            $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
            $data = isset( $temp_options['palettes'] ) ? (array) $temp_options['palettes'] : array();
        } else {
            $data = isset( $options['palettes'][ $section_subtype ] ) ? (array) $options['palettes'][ $section_subtype ] : array();
        }

        $payload = array(
            'exported_at'   => function_exists( 'current_time' ) ? current_time( 'mysql' ) : gmdate( 'c' ),
            'plugin'        => 'acf-css-really-simple-style-management-center',
            'plugin_version'=> defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '',
            'section_type'  => $section_type,
            'section_subtype' => $section_subtype,
            'data'          => $data,
        );

        if ( function_exists( 'nocache_headers' ) ) {
            nocache_headers();
        }

        $filename = sprintf(
            'acf-css-%s-%s-%s.json',
            $section_type,
            $section_subtype,
            gmdate( 'Ymd-His' )
        );

        // 다운로드 헤더
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

        echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        exit;
    }

    /**
     * [v3.8.0] 섹션별 불러오기
     * - JSON 업로드 후 옵션에 병합(키 기준 덮어쓰기)
     */
    public function ajax_import_section() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $section_type = isset( $_POST['section_type'] ) ? sanitize_key( wp_unslash( $_POST['section_type'] ) ) : '';
        $section_subtype = isset( $_POST['section_subtype'] ) ? sanitize_key( wp_unslash( $_POST['section_subtype'] ) ) : '';

        if ( 'palette' !== $section_type ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 섹션 타입입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $allowed_subtypes = array( 'brand', 'system', 'alternative', 'another', 'temp' );
        if ( ! in_array( $section_subtype, $allowed_subtypes, true ) ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 섹션 서브타입입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        if ( empty( $_FILES['import_file'] ) || empty( $_FILES['import_file']['tmp_name'] ) ) {
            wp_send_json_error( array( 'message' => __( '업로드된 파일이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $tmp_name = $_FILES['import_file']['tmp_name'];
        $raw = file_get_contents( $tmp_name );
        if ( ! $raw ) {
            wp_send_json_error( array( 'message' => __( '파일을 읽을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $decoded = json_decode( $raw, true );
        if ( ! is_array( $decoded ) ) {
            wp_send_json_error( array( 'message' => __( 'JSON 형식이 올바르지 않습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // payload 형식({data:{...}})과 순수 데이터({..}) 둘 다 허용
        $import_data = isset( $decoded['data'] ) && is_array( $decoded['data'] ) ? (array) $decoded['data'] : $decoded;

        // 간단 정리(색상 키는 hex로)
        $sanitized = array();
        foreach ( (array) $import_data as $k => $v ) {
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( (string) $k ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $k ) );
            if ( is_array( $v ) ) {
                // 팔레트는 1depth만 정리 (필요 시 확장)
                $sanitized[ $key ] = $v;
                continue;
            }
            if ( strpos( $key, 'color' ) !== false ) {
                $hex = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( (string) $v ) : '';
                $sanitized[ $key ] = $hex ? $hex : '';
                continue;
            }
            $sanitized[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( (string) $v ) : strip_tags( (string) $v );
        }

        if ( 'temp' === $section_subtype ) {
            $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
            if ( ! isset( $temp_options['palettes'] ) || ! is_array( $temp_options['palettes'] ) ) {
                $temp_options['palettes'] = array();
            }
            $temp_options['palettes'] = array_merge( (array) $temp_options['palettes'], $sanitized );
            update_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, $temp_options );
        } else {
            $options = (array) get_option( $this->options_key, array() );
            if ( ! isset( $options['palettes'] ) || ! is_array( $options['palettes'] ) ) {
                $options['palettes'] = array();
            }
            $options['palettes'][ $section_subtype ] = array_merge(
                (array) ( $options['palettes'][ $section_subtype ] ?? array() ),
                $sanitized
            );
            update_option( $this->options_key, $options );
        }

        wp_send_json_success( array(
            'message' => __( '설정이 성공적으로 불러와졌습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }

    // 나머지 메서드들은 dev 버전과 동일 (sync_to_spokes, sync_from_customizer, sanitize_options_recursive 등)
    // 생략하거나 dev 버전 파일을 복사하여 수정

    private function sync_to_spokes( $hub_options ) {
        // Free 버전: WooCommerce만 동기화
        $brand_colors = $hub_options['palettes']['brand'] ?? array();
        
        // WooCommerce 동기화
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! is_array( $kwe ) ) {
            $kwe = array();
        }
        if ( isset( $brand_colors['primary_color'] ) ) {
            $kwe['product_quickview_button_color'] = $brand_colors['primary_color'];
        }
        if ( isset( $brand_colors['primary_color_hover'] ) ) {
            $kwe['product_quickview_button_color_hover'] = $brand_colors['primary_color_hover'];
        }
        update_option( 'kt_woo_extras', $kwe );
    }

    public function sync_from_customizer( $manager ) {
        $hub_options = (array) get_option( $this->options_key );
        $panel_settings = $manager->settings();
        
        foreach ( $panel_settings as $setting_id => $setting_obj ) {
            if ( strpos( $setting_id, $this->options_key . '[' ) === 0 ) {
                $keys = str_replace( array( $this->options_key, '[', ']' ), '', $setting_id );
                $keys = explode( '][', $keys );
                $this->set_nested_object_value( $hub_options, $keys, $setting_obj->post_value() );
            }
        }

        update_option( $this->options_key, $hub_options );
        JJ_CSS_Cache::instance()->flush();
    }

    private function set_nested_object_value( &$obj, $keys, $value ) {
        $current = &$obj;
        foreach ( $keys as $key ) {
            if ( ! isset( $current[$key] ) || ! is_array( $current[$key] ) ) {
                $current[$key] = array();
            }
            $current = &$current[$key];
        }
        $current = $value;
    }

    private function sanitize_options_recursive( $input ) {
        $output = array();
        foreach ( (array) $input as $key => $value ) {
            $raw_key = (string) $key;
            // [v5.3.8] WordPress 함수 안전 호출
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( $raw_key ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( $raw_key ) );
            if ( is_array( $value ) ) {
                $output[ $key ] = $this->sanitize_options_recursive( $value );
                continue;
            }
            // [v5.3.8] PHP 7.4 호환성: str_contains() 대신 strpos() 사용
            if ( strpos( $key, 'color' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $sanitized = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( $value ) : '';
                $output[ $key ] = $sanitized ? $sanitized : '';
                continue;
            }
            if ( strpos( $key, 'custom_css' ) !== false || strpos( $key, 'additional_css' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'wp_kses_post' ) ? wp_kses_post( $value ) : wp_strip_all_tags( $value );
                continue;
            }
            if ( strpos( $key, 'font_family' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
                continue;
            }
            // [v5.3.8] WordPress 함수 안전 호출
            $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
        }
        return $output;
    }

    public function load_options_and_init_strategies() {
        // [v5.3.5] 전체 메서드를 try-catch로 보호하여 치명적 오류 방지
        try {
            $this->environment_map = $this->scan_environment();
            $this->load_dynamic_adapters( $this->environment_map );
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options = $options;
            $this->sync_existing_settings();
            $this->brand_colors = $this->options['palettes']['brand'] ?? array();

            // [v5.3.5] 모든 전략 클래스 호출에 class_exists() 확인 추가
            // 전략 1: CSS 변수
            if ( class_exists( 'JJ_Strategy_1_CSS_Vars' ) ) {
                JJ_Strategy_1_CSS_Vars::instance()->init( $this->options );
            }
            
            // 전략 2: PHP 필터
            if ( class_exists( 'JJ_Strategy_2_PHP_Filters' ) ) {
                JJ_Strategy_2_PHP_Filters::instance()->init( $this->options, null, null, null, null );
            }
            
            // 전략 3: Dequeue (CSS 재정의)
            if ( class_exists( 'JJ_Strategy_3_Dequeue' ) ) {
                JJ_Strategy_3_Dequeue::instance()->init( $this->options );
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    private function sync_existing_settings() {
        $active_theme = wp_get_theme()->get_template();
        $needs_update = false;

        if ( ! isset( $this->options['palettes'] ) ) {
            $this->options['palettes'] = array();
        }
        if ( ! isset( $this->options['palettes']['brand'] ) ) {
            $this->options['palettes']['brand'] = array();
        }
        if ( ! isset( $this->options['typography'] ) ) {
            $this->options['typography'] = array();
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five 테마만 동기화
        if ( in_array( $active_theme, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
            // 기본 테마는 별도 설정이 없으므로 Customizer에서 가져오기
        }

        // WooCommerce 동기화 (Free 버전 지원)
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! empty( $kwe['product_quickview_button_color'] ) && empty( $this->options['palettes']['brand']['primary_color'] ) ) {
            $this->options['palettes']['brand']['primary_color'] = $kwe['product_quickview_button_color'];
            $needs_update = true;
        }
        if ( ! empty( $kwe['product_quickview_button_color_hover'] ) && empty( $this->options['palettes']['brand']['primary_color_hover'] ) ) {
            $this->options['palettes']['brand']['primary_color_hover'] = $kwe['product_quickview_button_color_hover'];
            $needs_update = true;
        }

        if ( $needs_update ) {
            update_option( $this->options_key, $this->options );
        }
    }

    private function load_dynamic_adapters( $environment_map ) {
        $active_adapters = $environment_map;
        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;
        $error_handler = JJ_Error_Handler::instance();

        // Free 버전: 테마 어댑터 제한 (Twenty Twenty-Four, Twenty Twenty-Five만)
        if ( ! empty( $active_adapters['themes'] ) ) {
            $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
            $active_adapters['themes'] = array_intersect( $active_adapters['themes'], $allowed_themes );
            
            foreach ( $active_adapters['themes'] as $theme_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-theme-' . sanitize_file_name( $theme_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Theme_' . str_replace( '-', '_', ucwords( $theme_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }

        // Free 버전: 플러그인 어댑터 제한 (WooCommerce만)
        if ( ! empty( $active_adapters['spokes'] ) ) {
            $allowed_plugins = array( 'woocommerce' );
            $active_adapters['spokes'] = array_intersect( $active_adapters['spokes'], $allowed_plugins );
            
            foreach ( $active_adapters['spokes'] as $spoke_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-spoke-' . sanitize_file_name( $spoke_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Spoke_' . str_replace( '-', '_', ucwords( $spoke_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }
    }

    private function scan_environment() {
        $config = $this->adapters_config;
        $spokes_to_check = $config['spokes'] ?? array();
        $themes_to_check = $config['themes'] ?? array();

        $active_plugins = (array) get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        $active_spoke_adapters = array();
        $active_theme_adapters = array();
        $current_theme_path = get_template_directory();
        $theme_dir_name = basename( $current_theme_path );

        // Free 버전: WooCommerce만 체크
        foreach ( $spokes_to_check as $adapter_slug => $plugin_path ) {
            if ( in_array( $plugin_path, $active_plugins ) && $adapter_slug === 'woocommerce' ) {
                $active_spoke_adapters[] = $adapter_slug;
            }
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five만 체크
        foreach ( $themes_to_check as $adapter_slug => $theme_path_suffix ) {
            if ( $theme_dir_name === $adapter_slug && in_array( $adapter_slug, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
                if ( file_exists( $current_theme_path . '/' . $theme_path_suffix ) ) {
                    $active_theme_adapters[] = $adapter_slug;
                }
            }
        }

        return array(
            'themes' => $active_theme_adapters,
            'spokes' => $active_spoke_adapters,
        );
    }

    public function render_debug_info() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $errors = JJ_Error_Handler::instance()->get_errors();

        echo '<div style="background: #001a33; color: #00ff8c; padding: 20px; font-family: monospace; font-size: 14px; line-height: 1.6; z-index: 99999; position: relative; margin: 20px; border-radius: 5px; white-space: pre-wrap; word-break: break-all; opacity: 0.9;">';
        echo '<strong>[ACF CSS DEBUG - FREE VERSION]</strong><br>';
        echo '====================<br>';
        echo '<strong>Edition:</strong> Free<br>';
        echo '<strong>Loaded Adapters:</strong><br>';
        if ( ! empty( $this->environment_map['themes'] ) ) {
            echo ' - Themes: ' . esc_html( implode( ', ', $this->environment_map['themes'] ) ) . '<br>';
        } else {
            echo ' - Themes: None<br>';
        }
        if ( ! empty( $this->environment_map['spokes'] ) ) {
            echo ' - Spokes: ' . esc_html( implode( ', ', $this->environment_map['spokes'] ) ) . '<br>';
        } else {
            echo ' - Spokes: None<br>';
        }
        echo '</div>';
    }
}

function jj_simple_style_guide_master_run() {
    // [Phase 5] 다국어 지원 로드
    load_plugin_textdomain( 'acf-css-really-simple-style-management-center', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // WordPress 함수가 로드되었는지 확인
    if ( ! function_exists( 'class_exists' ) || ! function_exists( 'file_exists' ) ) {
        return;
    }
    
    // [v5.3.9] 코드 무결성 모니터 초기화 (plugins_loaded 시점)
    
    // [Phase 4.5/4.99] Admin Center 초기화 (메뉴/상단바/관리자 UI)
    if ( class_exists( 'JJ_Admin_Center' ) ) {
        try {
            $admin_center = JJ_Admin_Center::instance();
            if ( method_exists( $admin_center, 'init' ) ) {
                $admin_center->init();
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v3.7.0] 백업 관리자 초기화
    if ( class_exists( 'JJ_Backup_Manager' ) ) {
        try {
            JJ_Backup_Manager::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] REST API 초기화
    if ( class_exists( 'JJ_Style_Guide_REST_API' ) ) {
        try {
            JJ_Style_Guide_REST_API::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.3] Extension Manager 초기화
    if ( class_exists( 'JJ_Extension_Manager' ) ) {
        try {
            JJ_Extension_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] Webhook Manager 초기화
    if ( class_exists( 'JJ_Webhook_Manager' ) ) {
        try {
            JJ_Webhook_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5 B] Multisite Controller 초기화
    if ( class_exists( 'JJ_Multisite_Controller' ) ) {
        try {
            JJ_Multisite_Controller::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v5.6.0] Visual Command Center 초기화
    if ( class_exists( 'JJ_Visual_Command_Center' ) ) {
        JJ_Visual_Command_Center::instance();
    }

    // [v5.7.0] AI Style Intelligence 초기화
    if ( class_exists( 'JJ_Smart_Palette' ) ) {
        JJ_Smart_Palette::instance();
    }

    // [v5.9.0] CSS Optimizer 초기화
    if ( class_exists( 'JJ_CSS_Optimizer' ) ) {
        JJ_CSS_Optimizer::instance();
    }

    // [v5.8.0] Cloud Manager 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Cloud_Manager' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) { // Premium 이상 체크 대용
            JJ_Cloud_Manager::instance();
        }
    }
    
    // [Phase 3] 템플릿 마켓 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Template_Market' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) {
            JJ_Template_Market::instance();
        }
    }
    
    // [Phase 5] Partner Hub 초기화 (Partner/Master만)
    if ( class_exists( 'JJ_Partner_Hub' ) ) {
        JJ_Partner_Hub::instance();
    }

    // [v4.2.2] 실험실(Labs) 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Labs_Center' ) ) {
        try {
            JJ_Labs_Center::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }


    if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
        try {
            JJ_Code_Integrity_Monitor::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Code Integrity Monitor: 초기화 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.9] 라이센스 강제 실행 초기화 (plugins_loaded 시점)
    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $enforcement_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-enforcement.php';
        if ( file_exists( $enforcement_file ) ) {
            require_once $enforcement_file;
            if ( class_exists( 'JJ_License_Enforcement' ) ) {
                try {
                    JJ_License_Enforcement::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ License Enforcement: 초기화 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // [v5.3.7] 안전 모드 플래그 확인 및 활성화
    if ( function_exists( 'get_option' ) && class_exists( 'JJ_Safe_Mode' ) ) {
        $safe_mode_enabled = get_option( 'jj_style_guide_safe_mode', false );
        if ( $safe_mode_enabled ) {
            $safe_mode_reason = get_option( 'jj_style_guide_safe_mode_reason', '활성화 중 오류 발생' );
            try {
                $safe_mode = JJ_Safe_Mode::instance();
                if ( method_exists( $safe_mode, 'enable' ) ) {
                    $safe_mode->enable( $safe_mode_reason );
                }
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 오류 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
    
    // 메인 클래스 인스턴스 생성
    if ( class_exists( 'JJ_Simple_Style_Guide_Master' ) ) {
        try {
            JJ_Simple_Style_Guide_Master::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.6] 기존 플러그인 마이그레이션 매니저 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $migration_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php';
        if ( file_exists( $migration_file ) ) {
            // 마이그레이션 매니저는 이미 safe_loader를 통해 로드되었을 수 있음
            // 클래스가 존재하는 경우에만 초기화
            if ( class_exists( 'JJ_Migration_Manager' ) ) {
                try {
                    JJ_Migration_Manager::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 오류 - ' . $e->getMessage() );
                    }
                } catch ( Error $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // 플러그인 업데이트 체크 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $updater_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php';
        if ( file_exists( $updater_file ) ) {
            require_once $updater_file;
            if ( class_exists( 'JJ_Plugin_Updater' ) ) {
                try {
                    new JJ_Plugin_Updater(
                        __FILE__,
                        'advanced-custom-style-manage-center',
                        "ACF CSS Master",
                        defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.4.0',
                        'DEV' // 마스터 버전
                    );
                } catch ( Exception $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 오류 - ' . $e->getMessage() );
                } catch ( Error $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
}

// [v5.3.8] plugins_loaded 훅 활성화
if ( function_exists( 'add_action' ) ) {
    add_action( 'plugins_loaded', 'jj_simple_style_guide_master_run', 10 );
}

/**
 * [v8.0.0] 안전 장치: 필수 헬퍼 함수 Fallback 정의
 * class-jj-admin-center.php 로드 실패 시에도 사이트가 멈추지 않도록 보장합니다.
 */
if ( ! function_exists( 'jj_style_guide_text' ) ) {
    function jj_style_guide_text( $key, $fallback = '' ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_text( $key, $fallback );
        }
        return $fallback;
    }
}

if ( ! function_exists( 'jj_style_guide_sections_layout' ) ) {
    function jj_style_guide_sections_layout() {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_sections_layout();
        }
        return array();
    }
}

if ( ! function_exists( 'jj_style_guide_section_index' ) ) {
    function jj_style_guide_section_index( $slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_section_index( $slug );
        }
        return null;
    }
}

if ( ! function_exists( 'jj_style_guide_is_tab_enabled' ) ) {
    function jj_style_guide_is_tab_enabled( $section_slug, $tab_slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->is_tab_enabled( $section_slug, $tab_slug );
        }
        return true;
    }
}

```

```php
<?php
/**
 * 실험실 센터 - 공식 지원 목록 탭
 * 공식 지원 테마 및 플러그인 목록 표시
 *
 * @version 3.7.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// 인기/가중치 기반 우선 순위 정의 (v3.7.0 업데이트)
$theme_popularity_order = array(
    // 상위 우선순위 (가장 인기 있는 테마)
    'astra', 'kadence', 'generatepress', 'oceanwp', 'divi', // [v3.7.0] oceanwp, divi 추가
    'storefront', 'neve', 'spectra-one', 'nexter', 
    'hello-elementor', 'hello-biz',
    // 후순위 (기본 테마 및 기타)
    'twentytwentyfive', 'twentytwentyfour', 'frost', 'onepress', 'hestia',
);

$spoke_popularity_order = array(
    // 전자상거래 및 코어
    'woocommerce',
    
    // 페이지 빌더 / 블록
    'elementor', 'beaver-builder', // [v3.7.0] beaver-builder 추가
    'generateblocks', 'spectra',
    'kadence-blocks', 'otter-blocks', 'essential-blocks', 'nexter-blocks',
    
    // 폼 플러그인
    'contact-form-7', 'wpforms', // [v3.7.0] wpforms 추가
    'fluentform', 'gravityforms', 'bitform',
    
    // SEO 및 마케팅
    'yoast-seo', 'rankmath', // [v3.7.0] yoast-seo 추가
    'monsterinsights', 'jetpack', // [v3.7.0] monsterinsights, jetpack 추가
    
    // LMS
    'learndash', 'tutor-lms', 'learnpress', 'lifterlms',
    'sensei-lms', 'masterstudy-lms',
    
    // 커뮤니티 / 회원
    'ultimatemember', 'advanced-members',
    'buddypress', 'fluent-community',
    
    // 기타 도구
    'acf-extended', 'gamipress',
    
    // 백업 / 보안 (후순위)
    'updraftplus', 'wordfence', 'all-in-one-wp-migration', // [v3.7.0] 추가
);

// 한 번에 노출할 기본 개수
$initial_theme_count = 6;
$initial_spoke_count = 6;
$priority_pages = 3;

// 정렬 함수 (이미 정의되어 있을 수 있으므로 확인)
if ( ! function_exists( 'jj_style_guide_sort_by_popularity_then_random' ) ) {
    function jj_style_guide_sort_by_popularity_then_random( $config, $popularity_order, $initial_count, $priority_pages ) {
        $slugs = array_keys( $config );
        $seen = array();
        $ordered_slugs = array();

        foreach ( $popularity_order as $slug ) {
            if ( isset( $config[ $slug ] ) && ! isset( $seen[ $slug ] ) ) {
                $ordered_slugs[] = $slug;
                $seen[ $slug ] = true;
            }
        }

        $remaining = array();
        foreach ( $slugs as $slug ) {
            if ( ! isset( $seen[ $slug ] ) ) {
                $remaining[] = $slug;
            }
        }
        sort( $remaining, SORT_STRING );
        $ordered_slugs = array_merge( $ordered_slugs, $remaining );

        $priority_limit = $initial_count * $priority_pages;
        if ( count( $ordered_slugs ) > $priority_limit ) {
            $head = array_slice( $ordered_slugs, 0, $priority_limit );
            $tail = array_slice( $ordered_slugs, $priority_limit );
            shuffle( $tail );
            $ordered_slugs = array_merge( $head, $tail );
        }

        $sorted_config = array();
        foreach ( $ordered_slugs as $slug ) {
            $sorted_config[ $slug ] = $config[ $slug ];
        }
        return $sorted_config;
    }
}

// 테마/스포크 이름 매핑 함수
if ( ! function_exists( 'jj_style_guide_get_theme_label' ) ) {
    function jj_style_guide_get_theme_label( $slug, $path ) {
        $map = array(
            'astra' => 'Astra', 'spectra-one' => 'Spectra One',
            'kadence' => 'Kadence', 'generatepress' => 'GeneratePress',
            'nexter' => 'Nexter', 'neve' => 'Neve', 'hestia' => 'Hestia',
            'storefront' => 'Storefront', 'frost' => 'Frost',
            'twentytwentyfive' => 'Twenty Twenty-Five',
            'twentytwentyfour' => 'Twenty Twenty-Four',
            'onepress' => 'OnePress', 'hello-elementor' => 'Hello Elementor',
            'hello-biz' => 'Hello Biz',
        );
        return isset( $map[ $slug ] ) ? $map[ $slug ] : ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
    }
}

if ( ! function_exists( 'jj_style_guide_get_spoke_label' ) ) {
    function jj_style_guide_get_spoke_label( $slug, $path ) {
        $map = array(
            'woocommerce' => 'WooCommerce', 'learndash' => 'LearnDash',
            'ultimatemember' => 'Ultimate Member', 'advanced-members' => 'Advanced Members',
            'acf-extended' => 'ACF Extended', 'buddypress' => 'BuddyPress',
            'gamipress' => 'GamiPress', 'rankmath' => 'Rank Math SEO',
            'elementor' => 'Elementor', 'generateblocks' => 'GenerateBlocks',
            'spectra' => 'Spectra (Ultimate Addons for Gutenberg)',
            'kadence-blocks' => 'Kadence Blocks', 'otter-blocks' => 'Otter Blocks',
            'essential-blocks' => 'Essential Blocks', 'nexter-blocks' => 'Nexter Blocks',
            'tutor-lms' => 'Tutor LMS', 'learnpress' => 'LearnPress',
            'masterstudy-lms' => 'MasterStudy LMS', 'sensei-lms' => 'Sensei LMS',
            'lifterlms' => 'LifterLMS', 'bbpress' => 'bbPress',
            'fluent-community' => 'Fluent Community', 'fluentform' => 'Fluent Forms',
            'gravityforms' => 'Gravity Forms', 'contact-form-7' => 'Contact Form 7',
            'bitform' => 'Bit Form',
        );
        if ( isset( $map[ $slug ] ) ) {
            return $map[ $slug ];
        }
        if ( strpos( $path, '/' ) !== false ) {
            $parts = explode( '/', $path );
            return ucwords( str_replace( array( '-', '_' ), ' ', reset( $parts ) ) );
        }
        return ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
    }
}

$themes_config = jj_style_guide_sort_by_popularity_then_random(
    $themes_config_raw,
    $theme_popularity_order,
    $initial_theme_count,
    $priority_pages
);

$spokes_config = jj_style_guide_sort_by_popularity_then_random(
    $spokes_config_raw,
    $spoke_popularity_order,
    $initial_spoke_count,
    $priority_pages
);
?>
<div class="jj-labs-supported-tab">
    <h2><?php _e( '공식 지원 테마 및 플러그인', 'jj-style-guide' ); ?></h2>
    <p class="description jj-labs-tab-description" data-tab-type="supported" data-tooltip="labs-tab-supported-list">
        <?php _e( '현재 어댑터가 제공되는 공식 지원 테마와 플러그인 목록입니다. 이 목록은 어댑터 설정에 따라 자동으로 갱신되며, 인기 순서에 따라 우선적으로 표시됩니다.', 'jj-style-guide' ); ?>
        <span class="dashicons dashicons-editor-help" style="margin-left: 5px; cursor: help;" aria-label="<?php esc_attr_e( '도움말', 'jj-style-guide' ); ?>"></span>
    </p>

    <div class="jj-labs-supported-toolbar" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin: 15px 0 5px;">
        <input type="search"
               id="jj-labs-supported-search"
               placeholder="<?php echo esc_attr__( '검색: 테마/플러그인 이름', 'jj-style-guide' ); ?>"
               style="min-width: 240px; height: 34px; padding: 0 10px; border-radius: 6px; border: 1px solid #c3c4c7;" />
        <span class="description" style="margin:0;">
            <?php
            printf(
                /* translators: 1: theme count, 2: plugin count */
                __( '총 %1$d개 테마 · %2$d개 플러그인', 'jj-style-guide' ),
                count( $themes_config ),
                count( $spokes_config )
            );
            ?>
        </span>
    </div>

    <div class="jj-labs-supported-blocks" style="margin-top: 20px;">
        <!-- 공식 지원 테마 -->
        <div class="jj-labs-supported-item jj-labs-supported-themes" data-type="themes" style="margin-bottom: 30px;">
            <h3><?php _e( '공식 지원 테마', 'jj-style-guide' ); ?></h3>
            <p class="description">
                <?php _e( '아래 목록은 현재 개발된 모든 공식 지원 테마입니다.', 'jj-style-guide' ); ?>
            </p>
            <ul class="jj-labs-supported-list" data-initial-count="<?php echo esc_attr( $initial_theme_count ); ?>" style="list-style: none; padding: 0; margin: 15px 0;">
                <?php
                $theme_index = 0;
                foreach ( $themes_config as $slug => $path ) :
                    $label = jj_style_guide_get_theme_label( $slug, $path );
                    $is_hidden = ( $theme_index >= $initial_theme_count );
                    ?>
                    <li class="jj-labs-supported-list-item <?php echo $is_hidden ? 'is-hidden' : ''; ?>"
                        style="padding: 8px 12px; margin: 5px 0; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 3px;">
                        <?php echo esc_html( $label ); ?>
                    </li>
                    <?php $theme_index++; ?>
                <?php endforeach; ?>
            </ul>
            <?php if ( count( $themes_config ) > $initial_theme_count ) : ?>
            <div class="jj-labs-supported-actions">
                <button type="button" class="button button-secondary jj-labs-show-more" data-target="themes"><?php _e( '더 보기', 'jj-style-guide' ); ?></button>
                <button type="button" class="button button-secondary jj-labs-show-all" data-target="themes" style="display:none;"><?php _e( '전체 보기', 'jj-style-guide' ); ?></button>
            </div>
            <?php endif; ?>
        </div>

        <!-- 공식 지원 플러그인 -->
        <div class="jj-labs-supported-item jj-labs-supported-spokes" data-type="spokes">
            <h3><?php _e( '공식 지원 플러그인', 'jj-style-guide' ); ?></h3>
            <p class="description">
                <?php _e( '아래 목록은 현재 개발된 모든 공식 지원 플러그인입니다.', 'jj-style-guide' ); ?>
            </p>
            <ul class="jj-labs-supported-list" data-initial-count="<?php echo esc_attr( $initial_spoke_count ); ?>" style="list-style: none; padding: 0; margin: 15px 0;">
                <?php
                $spoke_index = 0;
                foreach ( $spokes_config as $slug => $path ) :
                    $label = jj_style_guide_get_spoke_label( $slug, $path );
                    $is_hidden = ( $spoke_index >= $initial_spoke_count );
                    ?>
                    <li class="jj-labs-supported-list-item <?php echo $is_hidden ? 'is-hidden' : ''; ?>"
                        style="padding: 8px 12px; margin: 5px 0; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 3px;">
                        <?php echo esc_html( $label ); ?>
                    </li>
                    <?php $spoke_index++; ?>
                <?php endforeach; ?>
            </ul>
            <?php if ( count( $spokes_config ) > $initial_spoke_count ) : ?>
            <div class="jj-labs-supported-actions">
                <button type="button" class="button button-secondary jj-labs-show-more" data-target="spokes"><?php _e( '더 보기', 'jj-style-guide' ); ?></button>
                <button type="button" class="button button-secondary jj-labs-show-all" data-target="spokes" style="display:none;"><?php _e( '전체 보기', 'jj-style-guide' ); ?></button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>


```

```javascript
/**
 * J&J 실험실 센터 JavaScript - v3.7.0
 * 
 * 실험실 센터 전용 JavaScript
 * - 탭 전환 기능
 * - CSS/HTML/JS 스캐너 기능
 * - 수동 재정의 저장 기능
 * - 공식 지원 목록 확장 기능
 * 
 * @since 3.7.0
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const $wrap = $('.jj-labs-center-wrap');
        if (!$wrap.length) {
            return;
        }

        // [v5.0.0] 실험실 탭 활성화/비활성화 관련 UI 개선
        // 전체 탭 선택/해제
        $(document).on('change', '#jj-select-all-labs-tabs', function() {
            const isChecked = $(this).is(':checked');
            $('.jj-labs-tab-enabled-checkbox').prop('checked', isChecked).trigger('change');
            $('.jj-labs-tab-checkbox').prop('checked', isChecked);
            updateLabsWarningMessage();
        });

        // 탭 체크박스와 enabled 체크박스 동기화
        $(document).on('change', '.jj-labs-tab-enabled-checkbox', function() {
            const $checkbox = $(this);
            const $row = $checkbox.closest('.jj-labs-tab-row');
            const tabSlug = $checkbox.data('tab');
            const isChecked = $checkbox.is(':checked');
            
            // [v5.0.0] 확인 다이얼로그 (비활성화 시)
            if (!isChecked) {
                const tabLabel = $row.data('tab-label') || $row.find('th').text().trim();
                if (!confirm('탭 "' + tabLabel + '"을(를) 비활성화하시겠습니까?\n\n비활성화하면 실험실에서 해당 탭이 표시되지 않습니다.')) {
                    $checkbox.prop('checked', true);
                    return;
                }
            }
            
            // 탭 체크박스도 동기화
            $('.jj-labs-tab-checkbox[data-tab="' + tabSlug + '"]').prop('checked', isChecked);
            
            // 시각적 상태 업데이트
            if (isChecked) {
                $row.removeClass('jj-tab-row-disabled');
            } else {
                $row.addClass('jj-tab-row-disabled');
            }
            
            // 전체 선택 체크박스 상태 업데이트
            updateLabsSelectAllCheckbox();
            updateLabsWarningMessage();
        });

        // 탭 체크박스 클릭 시 enabled 체크박스도 동기화
        $(document).on('change', '.jj-labs-tab-checkbox', function() {
            const tabSlug = $(this).data('tab');
            const isChecked = $(this).is(':checked');
            $('.jj-labs-tab-enabled-checkbox[data-tab="' + tabSlug + '"]')
                .prop('checked', isChecked)
                .trigger('change');
        });

        // 전체 선택 체크박스 상태 업데이트 함수
        function updateLabsSelectAllCheckbox() {
            const $allTabCheckboxes = $('.jj-labs-tab-enabled-checkbox');
            const $checkedTabCheckboxes = $allTabCheckboxes.filter(':checked');
            const allChecked = $allTabCheckboxes.length > 0 && 
                             $allTabCheckboxes.length === $checkedTabCheckboxes.length;
            $('#jj-select-all-labs-tabs').prop('checked', allChecked);
        }

        // 경고 메시지 표시/숨김 함수
        function updateLabsWarningMessage() {
            const $disabledTabs = $('.jj-labs-tab-enabled-checkbox:not(:checked)');
            const $warning = $('.jj-labs-tabs-disable-warning');
            
            if ($disabledTabs.length > 0) {
                $warning.slideDown(200);
            } else {
                $warning.slideUp(200);
            }
        }

        // 일괄 작업 버튼 (실험실)
        $(document).on('click', '.jj-bulk-enable-all-labs', function() {
            if (confirm('모든 탭을 활성화하시겠습니까?')) {
                $('.jj-labs-tab-enabled-checkbox').prop('checked', true).trigger('change');
                $('#jj-select-all-labs-tabs').prop('checked', true);
                $('.jj-labs-tab-row').removeClass('jj-tab-row-disabled');
            }
        });
        
        $(document).on('click', '.jj-bulk-disable-all-labs', function() {
            if (confirm('모든 탭을 비활성화하시겠습니까?\n\n주의: 모든 탭이 비활성화되면 실험실에서 아무것도 표시되지 않습니다.')) {
                $('.jj-labs-tab-enabled-checkbox').prop('checked', false).trigger('change');
                $('#jj-select-all-labs-tabs').prop('checked', false);
                $('.jj-labs-tab-row').addClass('jj-tab-row-disabled');
            }
        });
        
        // 검색/필터 기능 (실험실)
        // [v5.0.4] 성능 최적화: 디바운싱 적용
        let labsSearchTimeout = null;
        $(document).on('input', '#jj-labs-tabs-search', function() {
            const $input = $(this);
            const searchTerm = $input.val().toLowerCase().trim();
            
            // [v5.0.4] 디바운싱: 300ms 지연
            clearTimeout(labsSearchTimeout);
            labsSearchTimeout = setTimeout(function() {
                if (searchTerm === '') {
                    $('.jj-labs-tab-row').removeClass('jj-section-row-filtered');
                    return;
                }
                
                // [v5.0.4] 성능 최적화: DOM 조회를 한 번만 수행하고 배치로 처리
                const tabRows = $('.jj-labs-tab-row').toArray();
                const updates = [];
                
                tabRows.forEach(function(row) {
                    const $row = $(row);
                    const tabLabel = $row.data('tab-label') || '';
                    const tabSlug = $row.data('tab-slug') || '';
                    const matches = tabLabel.includes(searchTerm) || tabSlug.includes(searchTerm);
                    updates.push({ $row: $row, matches: matches });
                });
                
                // 배치 업데이트
                updates.forEach(function(item) {
                    if (item.matches) {
                        item.$row.removeClass('jj-section-row-filtered');
                    } else {
                        item.$row.addClass('jj-section-row-filtered');
                    }
                });
            }, 300);
        });
        
        // 페이지 로드 시 초기 상태 확인
        updateLabsSelectAllCheckbox();
        updateLabsWarningMessage();

        // [v6.0.0] 공식 지원 목록 검색(테마/플러그인)
        let supportedSearchTimeout = null;

        function resetSupportedLists() {
            $wrap.find('.jj-labs-supported-item').each(function() {
                const $itemWrap = $(this);
                const $list = $itemWrap.find('.jj-labs-supported-list');
                const initialCount = parseInt($list.data('initial-count') || 6, 10);
                const $items = $list.find('.jj-labs-supported-list-item');

                $items.each(function(idx) {
                    const $li = $(this);
                    if (idx >= initialCount) {
                        $li.addClass('is-hidden').hide();
                    } else {
                        $li.removeClass('is-hidden').show();
                    }
                });

                const hasHidden = $items.length > initialCount;
                $itemWrap.find('.jj-labs-supported-actions').toggle(hasHidden);
                $itemWrap.find('.jj-labs-show-all').hide();
                $itemWrap.find('.jj-labs-show-more').show().data('click-count', 0);
            });
        }

        function filterSupportedLists(term) {
            const q = (term || '').toLowerCase().trim();
            if (!q) {
                resetSupportedLists();
                return;
            }

            $wrap.find('.jj-labs-supported-item').each(function() {
                const $itemWrap = $(this);
                const $list = $itemWrap.find('.jj-labs-supported-list');
                const $items = $list.find('.jj-labs-supported-list-item');

                $items.each(function() {
                    const $li = $(this);
                    const label = ($li.text() || '').toLowerCase();
                    if (label.indexOf(q) !== -1) {
                        $li.removeClass('is-hidden').show();
                    } else {
                        $li.addClass('is-hidden').hide();
                    }
                });

                // 검색 중에는 더보기/전체보기 버튼 숨김 (검색 결과가 곧 전체보기)
                $itemWrap.find('.jj-labs-supported-actions').hide();
            });
        }

        $(document).on('input', '#jj-labs-supported-search', function() {
            const term = $(this).val();
            clearTimeout(supportedSearchTimeout);
            supportedSearchTimeout = setTimeout(function() {
                filterSupportedLists(term);
            }, 200);
        });

        // 최초 상태 세팅
        resetSupportedLists();
        
        // 시각적 상태 초기화
        $('.jj-labs-tab-enabled-checkbox').each(function() {
            const $checkbox = $(this);
            const $row = $checkbox.closest('.jj-labs-tab-row');
            const isChecked = $checkbox.is(':checked');
            
            if (!isChecked) {
                $row.addClass('jj-tab-row-disabled');
            }
        });

        // 탭 전환
        $wrap.on('click', '.jj-labs-center-tabs a', function(e) {
            e.preventDefault();
            const $tab = $(this).closest('li');
            const tabId = $tab.data('tab');

            // 탭 활성화
            $wrap.find('.jj-labs-center-tabs li').removeClass('active');
            $tab.addClass('active');

            // 컨텐츠 전환
            $wrap.find('.jj-labs-center-tab-content').removeClass('active');
            $wrap.find('.jj-labs-center-tab-content[data-tab="' + tabId + '"]').addClass('active');
        });

        // URL 스캔 시작
        $('#jj-labs-start-scan-url').on('click', function() {
            const $button = $(this);
            const $spinner = $('.jj-labs-scan-spinner').first();
            const $urlInput = $('#jj-labs-scan-url');
            const scanUrl = $urlInput.val().trim();

            if (!scanUrl) {
                alert('URL을 입력해주세요.');
                return;
            }

            setSpinnerState($spinner, $button, true);
            showScanResults(null);

            $.ajax({
                url: jjLabsCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_scan_page_for_css',
                    security: jjLabsCenter.nonce,
                    scan_url: scanUrl
                },
                success: function(response) {
                    setSpinnerState($spinner, $button, false);
                    if (response.success) {
                        displayScanResults(response.data);
                    } else {
                        showScanError(response.data?.message || '스캔 중 오류가 발생했습니다.');
                    }
                },
                error: function() {
                    setSpinnerState($spinner, $button, false);
                    showScanError('스캔 요청 중 네트워크 오류가 발생했습니다.');
                }
            });
        });

        // 활성 플러그인/테마 스캔 시작
        $('#jj-labs-start-scan-target').on('click', function() {
            const $button = $(this);
            const $spinner = $('.jj-labs-scan-spinner').last();
            const $select = $('#jj-labs-scan-target');
            const target = $select.val();

            if (!target) {
                alert('대상을 선택해주세요.');
                return;
            }

            setSpinnerState($spinner, $button, true);
            showScanResults(null);

            // 활성 테마/플러그인 스캔 (향후 구현)
            // 현재는 URL 스캔과 동일한 방식으로 처리
            const scanUrl = target === 'theme_active' ? window.location.origin : window.location.origin;
            
            $.ajax({
                url: jjLabsCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_scan_page_for_css',
                    security: jjLabsCenter.nonce,
                    scan_url: scanUrl,
                    scan_target: target
                },
                success: function(response) {
                    setSpinnerState($spinner, $button, false);
                    if (response.success) {
                        displayScanResults(response.data);
                    } else {
                        showScanError(response.data?.message || '스캔 중 오류가 발생했습니다.');
                    }
                },
                error: function() {
                    setSpinnerState($spinner, $button, false);
                    showScanError('스캔 요청 중 네트워크 오류가 발생했습니다.');
                }
            });
        });

        // 수동 재정의 저장
        $('#jj-labs-save-overrides').on('click', function() {
            const $button = $(this);
            const $spinner = $('.jj-labs-save-spinner');
            const $success = $('.jj-labs-save-success');
            const $textarea = $('#jj-labs-override-css');
            const overrideCss = $textarea.val();

            setSpinnerState($spinner, $button, true);
            $success.hide();

            $.ajax({
                url: jjLabsCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_save_style_guide',
                    security: jjLabsCenter.nonce,
                    settings: {
                        labs: {
                            override_css: overrideCss
                        }
                    }
                },
                success: function(response) {
                    setSpinnerState($spinner, $button, false);
                    if (response.success) {
                        $success.text('CSS 재정의가 저장되었습니다.').fadeIn();
                        setTimeout(function() {
                            $success.fadeOut();
                        }, 3000);
                    } else {
                        alert(response.data?.message || '저장 중 오류가 발생했습니다.');
                    }
                },
                error: function() {
                    setSpinnerState($spinner, $button, false);
                    alert('저장 요청 중 네트워크 오류가 발생했습니다.');
                }
            });
        });

        // 더 보기 / 전체 보기 (공식 지원 목록)
        $wrap.on('click', '.jj-labs-show-more, .jj-labs-show-all', function(e) {
            e.preventDefault();
            const $button = $(this);
            const target = $button.data('target');
            
            // 올바른 선택자: .jj-labs-supported-item[data-type="..."] 안의 .jj-labs-supported-list
            const $item = $button.closest('.jj-labs-supported-item[data-type="' + target + '"]');
            const $list = $item.find('.jj-labs-supported-list');
            
            if (!$list.length) {
                console.error('목록을 찾을 수 없습니다:', target);
                return;
            }
            
            const initialCount = parseInt($list.data('initial-count') || 6);
            const isShowAll = $button.hasClass('jj-labs-show-all');
            const $hiddenItems = $list.find('.jj-labs-supported-list-item.is-hidden');
            
            if ($hiddenItems.length === 0) {
                // 더 이상 숨겨진 항목이 없으면 버튼 숨김
                $button.hide();
                $button.siblings('.jj-labs-show-more').hide();
                return;
            }
            
            // 숨겨진 항목들을 표시
            $hiddenItems.each(function(index) {
                const $item = $(this);
                if (isShowAll || index < initialCount) {
                    // is-hidden 클래스 제거 및 표시
                    $item.removeClass('is-hidden');
                    // display: none이면 slideDown이 작동하지 않으므로 먼저 block으로 변경
                    if ($item.css('display') === 'none') {
                        $item.css('display', 'list-item').hide().slideDown(200);
                    } else {
                        $item.slideDown(200);
                    }
                }
            });
            
            // 버튼 상태 업데이트
            const $remainingHidden = $list.find('.jj-labs-supported-list-item.is-hidden');
            
            if ($remainingHidden.length === 0) {
                // 모든 항목이 표시되었으면 모든 버튼 숨김
                $item.find('.jj-labs-show-more, .jj-labs-show-all').hide();
            } else if (isShowAll) {
                // 전체 보기 버튼을 누르면 더 보기 버튼 숨김
                $item.find('.jj-labs-show-more').hide();
            } else {
                // 더 보기 버튼을 누르면 전체 보기 버튼 표시 (2번째 클릭부터)
                const clickCount = parseInt($button.data('click-count') || 0) + 1;
                $button.data('click-count', clickCount);
                if (clickCount >= 2) {
                    $item.find('.jj-labs-show-all').show();
                }
            }
        });

        // 스캔 결과 표시
        function displayScanResults(data) {
            const $results = $('#jj-labs-scan-results');
            const $tabs = $('.jj-labs-results-tabs');

            // CSS 선택자 결과
            if (data.class_selectors && data.class_selectors.length > 0) {
                $('#jj-labs-results-css-classes').val(data.class_selectors.join('\n'));
                $('#jj-labs-css-classes-count').text(' (' + data.class_selectors.length + '개)');
            }

            if (data.id_selectors && data.id_selectors.length > 0) {
                $('#jj-labs-results-css-ids').val(data.id_selectors.join('\n'));
                $('#jj-labs-css-ids-count').text(' (' + data.id_selectors.length + '개)');
            }

            if (data.style_blocks && data.style_blocks.length > 0) {
                $('#jj-labs-results-css-blocks').val(data.style_blocks.join('\n\n---\n\n'));
            }

            // HTML 결과
            if (data.html_snippet) {
                $('#jj-labs-results-html').val(data.html_snippet);
            }

            // JavaScript 결과 (향후 확장)
            $('#jj-labs-results-js').val('JavaScript 스캔 기능은 향후 업데이트에서 제공될 예정입니다.');

            // [v3.7.0 '신규'] 색상/폰트 추출 및 충돌 감지 결과 표시
            const $insights = $('#jj-labs-scan-insights');
            const $colorsDiv = $('#jj-labs-extracted-colors');
            const $fontsDiv = $('#jj-labs-extracted-fonts');
            const $conflictsDiv = $('#jj-labs-conflicts');
            const $colorsList = $('#jj-labs-colors-list');
            const $fontsList = $('#jj-labs-fonts-list');
            const $conflictsList = $('#jj-labs-conflicts-list');
            
            // 색상 표시
            if (data.colors && data.colors.length > 0) {
                $colorsList.empty();
                data.colors.slice(0, 20).forEach(function(color) {
                    const $colorItem = $('<div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 8px; background: #fff; border: 1px solid #c3c4c7; border-radius: 3px; cursor: pointer;" title="클릭하여 색상 피커로 복사"></div>');
                    const $colorSwatch = $('<span style="display: inline-block; width: 20px; height: 20px; background-color: ' + color + '; border: 1px solid #ddd; border-radius: 2px; vertical-align: middle;"></span>');
                    const $colorCode = $('<span style="font-family: monospace; font-size: 12px;">' + color + '</span>');
                    $colorItem.append($colorSwatch, $colorCode);
                    $colorItem.on('click', function() {
                        // 색상 피커로 복사 (향후 구현: 클립보드 복사 또는 색상 피커에 직접 적용)
                        prompt('색상 코드를 복사하세요:', color);
                    });
                    $colorsList.append($colorItem);
                });
                $colorsDiv.show();
                
                // 제안 색상이 있으면 적용 버튼 표시
                if (data.suggestions && data.suggestions.primary_colors && data.suggestions.primary_colors.length > 0) {
                    $('#jj-labs-apply-colors').show().off('click').on('click', function() {
                        if (confirm('상위 2개 색상을 브랜드 팔레트 Primary 색상으로 적용하시겠습니까?')) {
                            // 스타일 센터 페이지로 이동하여 팔레트 적용 (향후 구현)
                            alert('팔레트 적용 기능은 향후 업데이트에서 제공될 예정입니다. 색상 코드: ' + data.suggestions.primary_colors.slice(0, 2).join(', '));
                        }
                    });
                }
            } else {
                $colorsDiv.hide();
            }
            
            // 폰트 표시
            if (data.fonts && data.fonts.length > 0) {
                $fontsList.empty();
                data.fonts.forEach(function(font) {
                    $fontsList.append($('<li style="font-family: ' + font + ', sans-serif; margin: 4px 0;">' + font + '</li>'));
                });
                $fontsDiv.show();
                
                // 제안 폰트가 있으면 적용 버튼 표시
                if (data.suggestions && data.suggestions.font_families && data.suggestions.font_families.length > 0) {
                    $('#jj-labs-apply-fonts').show().off('click').on('click', function() {
                        if (confirm('상위 폰트를 타이포그래피에 적용하시겠습니까?')) {
                            // 스타일 센터 페이지로 이동하여 타이포그래피 적용 (향후 구현)
                            alert('타이포그래피 적용 기능은 향후 업데이트에서 제공될 예정입니다. 폰트: ' + data.suggestions.font_families.join(', '));
                        }
                    });
                }
            } else {
                $fontsDiv.hide();
            }
            
            // 충돌/문제점 표시
            if (data.conflicts && data.conflicts.length > 0) {
                $conflictsList.empty();
                data.conflicts.forEach(function(conflict) {
                    const conflictType = conflict.type || 'info';
                    const $conflictItem = $('<div class="notice notice-' + conflictType + '" style="margin: 8px 0; padding: 10px;"><p style="margin: 0 0 5px 0;"><strong>' + conflict.message + '</strong></p><p style="margin: 0; font-size: 13px;">' + (conflict.suggestion || '') + '</p></div>');
                    $conflictsList.append($conflictItem);
                });
                $conflictsDiv.show();
            } else {
                $conflictsDiv.hide();
            }
            
            // 인사이트 섹션 표시 (색상, 폰트, 충돌 중 하나라도 있으면)
            if ((data.colors && data.colors.length > 0) || (data.fonts && data.fonts.length > 0) || (data.conflicts && data.conflicts.length > 0)) {
                $insights.show();
            } else {
                $insights.hide();
            }

            // 결과 탭 표시
            $results.find('.jj-labs-results-empty').hide();
            $tabs.show();
        }

        // 스캔 오류 표시
        function showScanError(message) {
            const $results = $('#jj-labs-scan-results');
            $results.find('.jj-labs-results-empty').html(
                '<p class="description" style="color: #d63638;">' + message + '</p>'
            );
            $results.find('.jj-labs-results-tabs').hide();
        }

        // 스캔 결과 초기화
        function showScanResults(data) {
            const $results = $('#jj-labs-scan-results');
            if (!data) {
                $results.find('.jj-labs-results-empty').show();
                $results.find('.jj-labs-results-tabs').hide();
            }
        }

        // 스피너 상태 관리
        function setSpinnerState($spinner, $button, isActive) {
            if (isActive) {
                $spinner.show().addClass('is-active');
                $button.prop('disabled', true);
            } else {
                $spinner.hide().removeClass('is-active');
                $button.prop('disabled', false);
            }
        }

        // 초기화: 첫 번째 탭 활성화
        const $firstTab = $wrap.find('.jj-labs-center-tabs li:first-child');
        if ($firstTab.length) {
            $firstTab.addClass('active');
            const firstTabId = $firstTab.data('tab');
            $wrap.find('.jj-labs-center-tab-content[data-tab="' + firstTabId + '"]').addClass('active');
        }

        // [v3.7.0 '신규'] CSS 에디터 초기화 (WordPress CodeMirror)
        if (window.wp && window.wp.codeEditor) {
            const $textarea = $('#jj-labs-override-css');
            if ($textarea.length) {
                const editorSettings = wp.codeEditor.defaultSettings ? wp.codeEditor.defaultSettings.css : {};
                editorSettings.codemirror = {
                    ...editorSettings.codemirror,
                    mode: 'css',
                    lineNumbers: true,
                    indentUnit: 4,
                    indentWithTabs: false,
                    lineWrapping: true,
                    autoCloseBrackets: true,
                    matchBrackets: true,
                    foldGutter: true,
                    gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
                    extraKeys: {
                        'Ctrl-Space': 'autocomplete',
                        'Ctrl-/': 'toggleComment',
                        'Shift-Ctrl-/': 'toggleComment',
                    },
                    hintOptions: {
                        completeSingle: false,
                        // CSS 변수 자동 완성
                        templates: [
                            'var(--jj-primary-color)',
                            'var(--jj-btn-primary-bg)',
                            'var(--jj-btn-primary-text)',
                            'var(--jj-btn-secondary-bg)',
                            'var(--jj-btn-secondary-text)',
                            'var(--jj-form-input-border)',
                            'var(--jj-form-input-border-radius)',
                            'var(--jj-font-ko-family)',
                            'var(--jj-font-en-family)',
                        ]
                    }
                };

                const editor = wp.codeEditor.initialize($textarea[0], editorSettings);
                
                // 에디터 인스턴스 저장 (나중에 사용 가능)
                $textarea.data('code-editor', editor);
                
                // 실시간 CSS 검증 (간단한 구문 검사)
                editor.codemirror.on('change', function(cm) {
                    const value = cm.getValue();
                    const $errorDiv = $('#jj-labs-css-errors');
                    
                    // 기본적인 CSS 검증 (중괄호 매칭)
                    let openBraces = (value.match(/{/g) || []).length;
                    let closeBraces = (value.match(/}/g) || []).length;
                    
                    if (openBraces !== closeBraces) {
                        if (!$errorDiv.length) {
                            const $errorContainer = $('<div id="jj-labs-css-errors" style="margin-top: 10px; padding: 10px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404;"></div>');
                            $textarea.after($errorContainer);
                        }
                        $('#jj-labs-css-errors').html('<strong>경고:</strong> 중괄호가 일치하지 않습니다. (열림: ' + openBraces + ', 닫힘: ' + closeBraces + ')');
                    } else {
                        $('#jj-labs-css-errors').remove();
                    }
                });
            }
        }
    });

    // [v3.7.0 '신규'] 실험실 센터 설정 내보내기/불러오기
    $(document).on('click', '.jj-export-settings[data-center="labs-center"]', function() {
        const $button = $(this);
        const $spinner = $('<span class="spinner is-active" style="float: none; margin: 0 5px;"></span>');
        
        $button.prop('disabled', true).after($spinner);
        
        // AJAX 요청으로 다운로드 트리거
        const form = $('<form>').attr({
            method: 'POST',
            action: jjLabsCenter.ajax_url || ajaxurl,
            style: 'display: none;'
        });
        
        form.append($('<input>').attr({ type: 'hidden', name: 'action', value: 'jj_export_center_settings' }));
        form.append($('<input>').attr({ type: 'hidden', name: 'security', value: jjLabsCenter.nonce || '' }));
        form.append($('<input>').attr({ type: 'hidden', name: 'center', value: 'labs-center' }));
        
        $('body').append(form);
        form.submit();
        
        setTimeout(function() {
            form.remove();
            $spinner.remove();
            $button.prop('disabled', false);
        }, 1000);
    });

    $(document).on('click', '.jj-import-settings[data-center="labs-center"]', function() {
        const $button = $(this);
        const $fileInput = $('<input>').attr({
            type: 'file',
            accept: '.json',
            style: 'display: none;'
        });

        $fileInput.on('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                return;
            }

            if (!file.name.toLowerCase().endsWith('.json')) {
                alert('JSON 파일만 업로드할 수 있습니다.');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'jj_import_center_settings');
            formData.append('security', jjLabsCenter.nonce || '');
            formData.append('center', 'labs-center');
            formData.append('import_file', file);

            const $spinner = $('<span class="spinner is-active" style="float: none; margin: 0 5px;"></span>');
            $button.prop('disabled', true).after($spinner);

            $.ajax({
                url: jjLabsCenter.ajax_url || ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $spinner.remove();
                    $button.prop('disabled', false);
                    
                    if (response.success) {
                        alert(response.data.message || '설정이 성공적으로 불러와졌습니다.');
                        location.reload();
                    } else {
                        alert(response.data.message || '설정 불러오기에 실패했습니다.');
                    }
                },
                error: function() {
                    $spinner.remove();
                    $button.prop('disabled', false);
                    alert('네트워크 오류가 발생했습니다.');
                }
            });

            $fileInput.remove();
        });

        $('body').append($fileInput);
        $fileInput.trigger('click');
    });

})(jQuery);


```

### 진행 결과(근본 원인부터 정리)

이번 UI/UX 문제의 1차 **근본 원인**은 두 가지였습니다.

- **필수 UI 자산(JS/CSS) 누락**: 스타일 센터/실험실 센터가 참조하는 `jj-style-guide-editor.js`, `jj-labs-center.js` 등 핵심 파일이 실제 플러그인 폴더에 없어서 UI가 “텍스트만 있는 화면”처럼 보이고 버튼/자동 기능이 작동하지 않는 상태였습니다. → **`ezh` worktree에서 누락 파일을 복원해 Master 코어에 포함**했습니다.
- **프론트(JS) ↔ 백엔드(AJAX) 단절**: “현재 색상 불러오기(새로고침)” 같은 기능이 JS에서는 호출되는데, 서버측 훅/nonce가 맞지 않거나 등록이 누락되어 있었습니다. → **AJAX 훅 등록 + nonce 불일치 해결**했습니다.
