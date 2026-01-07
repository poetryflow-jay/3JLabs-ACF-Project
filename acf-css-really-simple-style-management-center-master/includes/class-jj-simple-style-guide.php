<?php
/**
 * 3J Labs Simple Style Guide - 메인 스타일 가이드 클래스
 * 
 * WordPress 스타일 관리의 핵심 클래스입니다.
 * 옵션 관리, 스타일 적용, 프론트엔드/백엔드 통합을 담당합니다.
 * 
 * @package ACF_CSS_Style_Guide
 * @version 26.0.14
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class JJ_Simple_Style_Guide
 * 
 * 스타일 가이드의 메인 컨트롤러 클래스
 */
class JJ_Simple_Style_Guide {

    /**
     * 싱글톤 인스턴스
     * @var JJ_Simple_Style_Guide|null
     */
    private static $instance = null;

    /**
     * 플러그인 옵션
     * @var array
     */
    private $options = array();

    /**
     * 옵션 키
     * @var string
     */
    private $option_key = 'jj_style_guide_options';

    /**
     * 생성자
     */
    public function __construct() {
        $this->load_options();
        $this->init_hooks();
    }

    /**
     * 싱글톤 인스턴스 반환
     * @return JJ_Simple_Style_Guide
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 옵션 로드
     */
    private function load_options() {
        $this->options = get_option( $this->option_key, $this->get_default_options() );
    }

    /**
     * 기본 옵션 반환
     * [v26.0.2] 섹션 파일에서 기대하는 구조로 전면 재구성
     * @return array
     */
    public function get_default_options() {
        return array(
            // [v26.0.2] 팔레트 시스템 - 섹션 파일 구조에 맞춤
            'palettes' => array(
                'brand' => array(
                    'primary_color'          => '#3b82f6',
                    'primary_color_hover'    => '#2563eb',
                    'secondary_color'        => '#64748b',
                    'secondary_color_hover'  => '#475569',
                ),
                'system' => array(
                    'site_bg'      => '#ffffff',
                    'content_bg'   => '#ffffff',
                    'text_color'   => '#1e293b',
                    'link_color'   => '#3b82f6',
                ),
                'alternative' => array(
                    'primary_color'          => '',
                    'primary_color_hover'    => '',
                    'secondary_color'        => '',
                    'secondary_color_hover'  => '',
                ),
                'another' => array(
                    'primary_color'          => '',
                    'primary_color_hover'    => '',
                    'secondary_color'        => '',
                    'secondary_color_hover'  => '',
                ),
            ),
            // [v26.0.2] 타이포그래피 - 태그별 구조
            'typography' => array(
                'h1' => array(
                    'font_family'   => '',
                    'font_weight'   => '700',
                    'font_style'    => 'normal',
                    'line_height'   => '1.2',
                    'letter_spacing'=> '',
                    'text_transform'=> '',
                    'font_size'     => array(
                        'desktop' => '40',
                        'tablet'  => '36',
                        'mobile'  => '30',
                    ),
                ),
                'h2' => array(
                    'font_family'   => '',
                    'font_weight'   => '700',
                    'font_style'    => 'normal',
                    'line_height'   => '1.25',
                    'letter_spacing'=> '',
                    'text_transform'=> '',
                    'font_size'     => array(
                        'desktop' => '32',
                        'tablet'  => '28',
                        'mobile'  => '24',
                    ),
                ),
                'h3' => array(
                    'font_family'   => '',
                    'font_weight'   => '600',
                    'font_style'    => 'normal',
                    'line_height'   => '1.3',
                    'letter_spacing'=> '',
                    'text_transform'=> '',
                    'font_size'     => array(
                        'desktop' => '26',
                        'tablet'  => '22',
                        'mobile'  => '19',
                    ),
                ),
                'h4' => array(
                    'font_family'   => '',
                    'font_weight'   => '600',
                    'font_style'    => 'normal',
                    'line_height'   => '1.35',
                    'letter_spacing'=> '',
                    'text_transform'=> '',
                    'font_size'     => array(
                        'desktop' => '22',
                        'tablet'  => '20',
                        'mobile'  => '18',
                    ),
                ),
                'h5' => array(
                    'font_family'   => '',
                    'font_weight'   => '500',
                    'font_style'    => 'normal',
                    'line_height'   => '1.4',
                    'letter_spacing'=> '',
                    'text_transform'=> '',
                    'font_size'     => array(
                        'desktop' => '18',
                        'tablet'  => '18',
                        'mobile'  => '16',
                    ),
                ),
                'h6' => array(
                    'font_family'   => '',
                    'font_weight'   => '500',
                    'font_style'    => 'normal',
                    'line_height'   => '1.4',
                    'letter_spacing'=> '',
                    'text_transform'=> '',
                    'font_size'     => array(
                        'desktop' => '16',
                        'tablet'  => '16',
                        'mobile'  => '15',
                    ),
                ),
                'p' => array(
                    'font_family'   => '',
                    'font_weight'   => '400',
                    'font_style'    => 'normal',
                    'line_height'   => '1.6',
                    'letter_spacing'=> '',
                    'text_transform'=> '',
                    'font_size'     => array(
                        'desktop' => '16',
                        'tablet'  => '16',
                        'mobile'  => '15',
                    ),
                ),
            ),
            'typography_settings' => array(
                'unit'    => 'px',
                'base_px' => '16',
            ),
            'fonts' => array(
                'korean'  => array( 'family' => '', 'attachment_id' => 0, 'format' => '' ),
                'english' => array( 'family' => '', 'attachment_id' => 0, 'format' => '' ),
                'buttons' => array( 'family' => '', 'attachment_id' => 0, 'format' => '' ),
                'forms'   => array( 'family' => '', 'attachment_id' => 0, 'format' => '' ),
            ),
            // [v26.0.2] 버튼 스타일 - 3종
            'buttons' => array(
                'primary' => array(
                    'background_color'       => '#3b82f6',
                    'background_color_hover' => '#2563eb',
                    'text_color'             => '#ffffff',
                    'text_color_hover'       => '#ffffff',
                    'border_color'           => '#3b82f6',
                    'border_color_hover'     => '#2563eb',
                    'border_radius'          => '8',
                    'padding'                => array( 'top' => '12', 'right' => '24', 'bottom' => '12', 'left' => '24' ),
                    'shadow'                 => array( 'color' => '', 'x' => '0', 'y' => '4', 'blur' => '6', 'spread' => '0' ),
                ),
                'secondary' => array(
                    'background_color'       => '#f1f5f9',
                    'background_color_hover' => '#e2e8f0',
                    'text_color'             => '#1e293b',
                    'text_color_hover'       => '#1e293b',
                    'border_color'           => '#e2e8f0',
                    'border_color_hover'     => '#cbd5e1',
                    'border_radius'          => '8',
                    'padding'                => array( 'top' => '12', 'right' => '24', 'bottom' => '12', 'left' => '24' ),
                    'shadow'                 => array( 'color' => '', 'x' => '0', 'y' => '2', 'blur' => '4', 'spread' => '0' ),
                ),
                'text' => array(
                    'background_color'       => 'transparent',
                    'background_color_hover' => '#f1f5f9',
                    'text_color'             => '#3b82f6',
                    'text_color_hover'       => '#2563eb',
                    'border_color'           => 'transparent',
                    'border_color_hover'     => 'transparent',
                    'border_radius'          => '8',
                    'padding'                => array( 'top' => '8', 'right' => '16', 'bottom' => '8', 'left' => '16' ),
                    'shadow'                 => array( 'color' => '', 'x' => '0', 'y' => '0', 'blur' => '0', 'spread' => '0' ),
                ),
            ),
            // [v26.0.2] 폼 스타일 - 라벨 + 필드
            'forms' => array(
                'label' => array(
                    'font_weight'    => '500',
                    'font_style'     => 'normal',
                    'text_transform' => '',
                    'font_size'      => '14',
                    'text_color'     => '#374151',
                ),
                'field' => array(
                    'background_color'   => '#ffffff',
                    'text_color'         => '#1e293b',
                    'border_color'       => '#d1d5db',
                    'border_color_focus' => '#3b82f6',
                    'border_radius'      => '6',
                    'border_width'       => '1',
                    'padding'            => array( 'top' => '10', 'right' => '12', 'bottom' => '10', 'left' => '12' ),
                ),
            ),
            // 설정 (레거시 호환)
            'settings' => array(
                'apply_to_frontend'   => true,
                'apply_to_admin'      => false,
                'apply_to_customizer' => true,
                'css_output_method'   => 'inline',
                'cache_enabled'       => true,
            ),
        );
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 프론트엔드 스타일 적용
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ), 5 );
        
        // 관리자 스타일 적용 (옵션에 따라)
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ), 5 );
        
        // 커스터마이저 스타일
        add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_styles' ) );
        
        // REST API 엔드포인트
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_style_guide_save', array( $this, 'ajax_save_options' ) );
        add_action( 'wp_ajax_jj_style_guide_reset', array( $this, 'ajax_reset_options' ) );
        add_action( 'wp_ajax_jj_style_guide_export', array( $this, 'ajax_export_options' ) );
        add_action( 'wp_ajax_jj_style_guide_import', array( $this, 'ajax_import_options' ) );
        add_action( 'wp_ajax_jj_apply_recommended_setup', array( $this, 'ajax_apply_recommended_setup' ) );

        // [v22.1.2] 스타일 센터 에셋 로드 (Admin Center에서 로드하지 않을 경우 대비)
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_style_guide_assets' ) );
    }

    /**
     * [v22.1.2] 스타일 센터(Visual Editor) 페이지 렌더링
     */
    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        // [v26.0.14] 웰컴 대시보드 체크: 첫 방문 시 웰컴 대시보드 표시
        $is_first_time = ! get_user_meta( get_current_user_id(), 'jj_css_seen_welcome', true );
        $welcome_dashboard_path = JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-welcome-dashboard.php';
        
        // 첫 방문이고 웰컴 대시보드 파일이 존재하면 웰컴 대시보드 표시
        if ( $is_first_time && file_exists( $welcome_dashboard_path ) ) {
            try {
                // 옵션 로드 (웰컴 대시보드에서 사용)
                $this->options = (array) get_option( $this->option_key );
                $options = $this->options;
                
                // 웰컴 대시보드 include
                include $welcome_dashboard_path;
                
                // 첫 방문 플래그 설정 (웰컴 대시보드에서 이미 처리하지만 안전을 위해)
                if ( ! get_user_meta( get_current_user_id(), 'jj_css_seen_welcome', true ) ) {
                    update_user_meta( get_current_user_id(), 'jj_css_seen_welcome', time() );
                }
                
                return; // 웰컴 대시보드 표시 후 종료
            } catch ( Exception $e ) {
                error_log( '[JJ Style Guide v26.0.14] Welcome dashboard include failed: ' . $e->getMessage() );
                // 오류 발생 시 기존 Visual Command Center로 폴백
            } catch ( Error $e ) {
                error_log( '[JJ Style Guide v26.0.14] Welcome dashboard include fatal: ' . $e->getMessage() );
                // 오류 발생 시 기존 Visual Command Center로 폴백
            }
        }

        // [v22.4.2] 안전한 엔진 초기화 (오류 방지)
        try {
            if ( class_exists( 'JJ_Demo_Importer' ) ) {
                JJ_Demo_Importer::instance()->init();
            }
        } catch ( Exception $e ) {
            error_log( '[JJ Style Guide] JJ_Demo_Importer init failed: ' . $e->getMessage() );
        } catch ( Error $e ) {
            error_log( '[JJ Style Guide] JJ_Demo_Importer init fatal: ' . $e->getMessage() );
        }
        
        try {
            if ( class_exists( 'JJ_History_Manager' ) ) {
                JJ_History_Manager::instance()->init();
            }
        } catch ( Exception $e ) {
            error_log( '[JJ Style Guide] JJ_History_Manager init failed: ' . $e->getMessage() );
        } catch ( Error $e ) {
            error_log( '[JJ Style Guide] JJ_History_Manager init fatal: ' . $e->getMessage() );
        }

        // 옵션 로드
        $this->options = (array) get_option( $this->option_key );
        $options = $this->options; // 뷰 파일에서 $options 변수 사용

        // [v22.1.2] 온보딩 모달 로드 (안전한 include)
        $onboarding_path = JJ_STYLE_GUIDE_PATH . 'includes/admin/views/view-onboarding-modal.php';
        if ( file_exists( $onboarding_path ) ) {
            try {
                include $onboarding_path;
            } catch ( Exception $e ) {
                error_log( '[JJ Style Guide] Onboarding modal include failed: ' . $e->getMessage() );
            } catch ( Error $e ) {
                error_log( '[JJ Style Guide] Onboarding modal include fatal: ' . $e->getMessage() );
            }
        }

        ?>
        <div id="jj-style-guide-wrapper" class="wrap jj-style-guide-wrap">
            <div class="jj-card" style="margin-bottom: 30px; padding: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 16px; box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1 style="margin: 0; color: #fff; font-size: 28px; font-weight: 700; display: flex; align-items: center; gap: 12px;">
                            <span class="dashicons dashicons-art" style="font-size: 32px; width: 32px; height: 32px; line-height: 32px;"></span>
                            <?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?>
                        </h1>
                        <p style="margin: 8px 0 0 0; color: rgba(255, 255, 255, 0.9); font-size: 14px;">
                            <?php _e( '웹사이트의 모든 스타일을 중앙에서 일관되게 관리하세요', 'acf-css-really-simple-style-management-center' ); ?>
                        </p>
                    </div>
                    <div class="jj-header-actions" style="display: flex; gap: 12px;">
                        <button type="button" id="jj-live-preview-toggle" class="jj-btn-secondary" style="background: rgba(255, 255, 255, 0.2); color: #fff; border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(10px);">
                            <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> <?php _e( '실시간 미리보기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <button type="button" class="jj-btn-primary" id="jj-save-style-guide-header" style="background: #fff; color: #667eea; border: none; font-weight: 600;">
                            <span class="dashicons dashicons-yes-alt" style="margin-top: 4px;"></span> <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
            </div>
            <hr class="wp-header-end" style="margin: 0;">
            
            <?php 
            $presets_path = JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-section-presets.php';
            if ( file_exists( $presets_path ) ) {
                try {
                    include $presets_path;
                } catch ( Exception $e ) {
                    error_log( '[JJ Style Guide] Presets section include failed: ' . $e->getMessage() );
                } catch ( Error $e ) {
                    error_log( '[JJ Style Guide] Presets section include fatal: ' . $e->getMessage() );
                }
            }
            ?>
        </div>
        
        <?php
        $default_layout = array(
            'colors' => array( 'label' => '1. 팔레트 시스템', 'enabled' => 1, 'order' => 10 ),
            'typography' => array( 'label' => '2. 타이포그래피', 'enabled' => 1, 'order' => 20 ),
            'buttons' => array( 'label' => '3. 버튼', 'enabled' => 1, 'order' => 30 ),
            'forms' => array( 'label' => '4. 폼', 'enabled' => 1, 'order' => 40 ),
            'fields' => array( 'label' => '5. 필드', 'enabled' => 1, 'order' => 50 ),
        );

        $layout = array();
        try {
            if ( class_exists( 'JJ_Admin_Center' ) && method_exists( 'JJ_Admin_Center', 'instance' ) ) {
                $admin_center = JJ_Admin_Center::instance();
                if ( method_exists( $admin_center, 'get_sections_layout' ) ) {
                    $layout = $admin_center->get_sections_layout();
                }
            }
        } catch ( Exception $e ) {
            error_log( '[JJ Style Guide] Layout loading failed: ' . $e->getMessage() );
        } catch ( Error $e ) {
            error_log( '[JJ Style Guide] Layout loading fatal: ' . $e->getMessage() );
        }

        if ( empty( $layout ) || ! is_array( $layout ) ) {
            $layout = $default_layout;
        }

        uasort( $layout, function( $a, $b ) {
            $order_a = isset( $a['order'] ) ? (int) $a['order'] : 999;
            $order_b = isset( $b['order'] ) ? (int) $b['order'] : 999;
            return $order_a <=> $order_b;
        } );

        $section_files = array(
            'colors'        => 'includes/editor-views/view-section-colors.php',
            'typography'    => 'includes/editor-views/view-section-typography.php',
            'buttons'       => 'includes/editor-views/view-section-buttons.php',
            'forms'         => 'includes/editor-views/view-section-forms.php',
            'fields'        => 'includes/editor-views/view-section-fields.php',
            'temp-palette'  => 'includes/editor-views/view-section-temp-palette.php',
        );

        $enabled_sections = array();
        foreach ( $layout as $slug => $meta ) {
            $is_enabled = ! isset( $meta['enabled'] ) || ( isset( $meta['enabled'] ) && $meta['enabled'] );
            if ( $is_enabled && isset( $section_files[ $slug ] ) ) {
                $enabled_sections[ $slug ] = $meta;
            }
        }

        if ( empty( $enabled_sections ) ) {
            foreach ( $default_layout as $slug => $meta ) {
                if ( isset( $section_files[ $slug ] ) ) {
                    $enabled_sections[ $slug ] = $meta;
                }
            }
        }

        $nav_icons = array(
            'colors' => 'dashicons-art',
            'typography' => 'dashicons-editor-textcolor',
            'buttons' => 'dashicons-button',
            'forms' => 'dashicons-feedback',
            'fields' => 'dashicons-forms',
        );
        $nav_labels = array(
            'colors' => '팔레트 시스템',
            'typography' => '타이포그래피',
            'buttons' => '버튼 스타일',
            'forms' => '폼 스타일',
            'fields' => '필드 스타일',
        );
        ?>
        
        <div class="jj-command-center">
            <div class="jj-cc-sidebar">
                <div class="jj-cc-logo">
                    <span class="dashicons dashicons-admin-customizer"></span>
                    <span>Style Center</span>
                </div>
                <nav class="jj-cc-nav">
                    <?php
                    $is_first = true;
                    foreach ( $enabled_sections as $slug => $meta ) :
                        $icon = isset( $nav_icons[ $slug ] ) ? $nav_icons[ $slug ] : 'dashicons-marker';
                        $label = isset( $nav_labels[ $slug ] ) ? $nav_labels[ $slug ] : ucfirst( $slug );
                        $active_class = $is_first ? ' active' : '';
                    ?>
                    <div class="jj-cc-nav-item<?php echo $active_class; ?>" data-target="jj-content-<?php echo esc_attr( $slug ); ?>">
                        <span class="dashicons <?php echo esc_attr( $icon ); ?> jj-cc-nav-icon"></span>
                        <span><?php echo esc_html( $label ); ?></span>
                    </div>
                    <?php
                    $is_first = false;
                    endforeach;
                    ?>
                </nav>
                <div class="jj-cc-footer">
                    v<?php echo esc_html( defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '26.0.0' ); ?>
                </div>
            </div>
            
            <div class="jj-cc-main">
                <header class="jj-cc-header">
                    <div class="jj-cc-title">
                        <h1><?php _e( 'Visual Command Center', 'acf-css-really-simple-style-management-center' ); ?></h1>
                    </div>
                    <div class="jj-cc-actions">
                        <span class="spinner" style="float: none; margin: 0;"></span>
                        <button type="button" class="button button-primary button-large" id="jj-save-style-guide">
                            <span class="dashicons dashicons-saved" style="margin-top: 4px;"></span>
                            <?php _e( '저장하기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </header>
                
                <div class="jj-cc-content-area">
                    <?php
                    $is_first = true;
                    foreach ( $enabled_sections as $slug => $meta ) :
                        $rel_path = $section_files[ $slug ];
                        $file_path = JJ_STYLE_GUIDE_PATH . $rel_path;
                        $active_class = $is_first ? ' active' : '';
                        
                        if ( file_exists( $file_path ) ) :
                    ?>
                    <div class="jj-section-wrapper<?php echo $active_class; ?>" id="jj-content-<?php echo esc_attr( $slug ); ?>" data-section="<?php echo esc_attr( $slug ); ?>">
                        <?php
                        try {
                            $options = $this->options;
                            if ( ! is_array( $options ) ) $options = array();
                            include $file_path;
                        } catch ( Exception $e ) {
                            echo '<div class="jj-error-box"><p class="jj-error-title">' . esc_html__( '섹션 로드 실패', 'acf-css-really-simple-style-management-center' ) . '</p><p class="jj-error-detail">' . esc_html( $e->getMessage() ) . '</p></div>';
                        } catch ( Error $e ) {
                            echo '<div class="jj-error-box"><p class="jj-error-title">' . esc_html__( '치명적 오류', 'acf-css-really-simple-style-management-center' ) . '</p><p class="jj-error-detail">' . esc_html( $e->getMessage() ) . '</p></div>';
                        }
                        ?>
                    </div>
                    <?php
                        endif;
                        $is_first = false;
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[JJ Command Center v26.0.13] Initializing...');
            
            // [v26.0.1] 메인 네비게이션 (사이드바) - 향상된 버전
            var navItems = document.querySelectorAll('.jj-cc-nav-item');
            var sections = document.querySelectorAll('.jj-section-wrapper');
            
            console.log('[JJ Command Center] Found', navItems.length, 'nav items,', sections.length, 'sections');
            
            // [v26.0.13] 초기화: 첫 번째 섹션만 표시 (setProperty로 !important 오버라이드) + 강화된 디버깅
            sections.forEach(function(sec, index) {
                if (index === 0) {
                    sec.classList.add('active');
                    sec.style.setProperty('display', 'block', 'important');
                    sec.style.setProperty('opacity', '1', 'important');
                    sec.style.setProperty('visibility', 'visible', 'important');
                    
                    // [v26.0.12] 디버깅: 실제 적용된 스타일 확인
                    var computedDisplay = window.getComputedStyle(sec).display;
                    console.log('[JJ Command Center v26.0.13] 첫 번째 섹션 초기화:', {
                        id: sec.id,
                        hasActiveClass: sec.classList.contains('active'),
                        inlineDisplay: sec.style.display,
                        computedDisplay: computedDisplay,
                        isVisible: computedDisplay !== 'none'
                    });
                    
                    if (computedDisplay === 'none') {
                        console.error('[JJ Command Center] ⚠️ 경고: 첫 번째 섹션이 여전히 숨겨져 있습니다!', sec);
                    }
                } else {
                    sec.classList.remove('active');
                    sec.style.setProperty('display', 'none', 'important');
                }
            });
            
            // 네비게이션 클릭 이벤트
            navItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    var targetId = this.getAttribute('data-target');
                    console.log('[JJ Command Center] Nav clicked, target:', targetId);
                    
                    // 모든 네비게이션 비활성화
                    navItems.forEach(function(nav) { nav.classList.remove('active'); });
                    this.classList.add('active');
                    
                    // [v26.0.9] 모든 섹션 숨기기 (setProperty로 !important 오버라이드)
                    sections.forEach(function(sec) { 
                        sec.classList.remove('active');
                        sec.style.setProperty('display', 'none', 'important');
                    });
                    
                    // [v26.0.9] 타겟 섹션 표시 (setProperty로 !important 오버라이드)
                    var targetSection = document.getElementById(targetId);
                    if (targetSection) {
                        targetSection.classList.add('active');
                        targetSection.style.setProperty('display', 'block', 'important');
                        targetSection.style.setProperty('opacity', '1', 'important');
                        targetSection.style.setProperty('visibility', 'visible', 'important');
                        console.log('[JJ Command Center] Section activated:', targetId);
                        
                        // 스크롤 초기화
                        var contentArea = document.querySelector('.jj-cc-content-area');
                        if (contentArea) contentArea.scrollTop = 0;
                        
                        // 컬러 피커 재초기화 (jQuery 있으면)
                        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.spectrum !== 'undefined') {
                            setTimeout(function() {
                                jQuery(targetSection).find('.jj-color-field:not(.spectrum-initialized)').each(function() {
                                    jQuery(this).spectrum({
                                        preferredFormat: 'hex',
                                        showInput: true,
                                        showInitial: true,
                                        allowEmpty: true
                                    }).addClass('spectrum-initialized');
                                });
                            }, 100);
                        }
                    } else {
                        console.error('[JJ Command Center] Section not found:', targetId);
                    }
                });
            });
            
            // [v26.0.13] 섹션 내부 서브 탭 시스템 (팔레트, 버튼 등 내부 탭) - PHP 인라인 스타일 통일 및 부모 요소 확인
            var tabContainers = document.querySelectorAll('.jj-tabs-container');
            console.log('[JJ Command Center v26.0.13] Found', tabContainers.length, 'tab containers');
            
            tabContainers.forEach(function(container, containerIndex) {
                var subTabs = container.querySelectorAll('.jj-tabs-nav .jj-tab-button');
                var subContents = container.querySelectorAll('.jj-tab-content');
                
                console.log('[JJ Command Center] Container', containerIndex, ':', subTabs.length, 'tabs,', subContents.length, 'contents');
                
                // [v26.0.11] 초기화: 모든 탭 콘텐츠 숨기고, 활성 탭만 표시
                if (subTabs.length > 0 && subContents.length > 0) {
                    var activeTabName = null;
                    
                    // 이미 활성화된 탭 찾기
                    subTabs.forEach(function(tab) {
                        if (tab.classList.contains('is-active')) {
                            activeTabName = tab.getAttribute('data-tab');
                        }
                    });
                    
                    // 활성 탭이 없으면 첫 번째 탭 활성화
                    if (!activeTabName && subTabs.length > 0) {
                        activeTabName = subTabs[0].getAttribute('data-tab');
                        subTabs[0].classList.add('is-active');
                    }
                    
                    // [v26.0.11] 모든 탭 콘텐츠 강제 숨기기 + 활성 탭만 표시
                    // CSS 충돌 해결: setProperty + 클래스 추가 + 강제 리플로우 + 부모 요소 확인
                    subContents.forEach(function(content) {
                        var contentName = content.getAttribute('data-tab-content');
                        
                        // 부모 요소 상태 확인
                        var parentSection = content.closest('.jj-section-wrapper');
                        var parentGlobal = content.closest('.jj-section-global');
                        var parentContainer = content.closest('.jj-tabs-container');
                        
                        if (contentName === activeTabName) {
                            // 활성 탭: 클래스 추가 + 인라인 스타일 + 강제 리플로우
                            content.classList.add('is-active');
                            content.style.setProperty('display', 'block', 'important');
                            content.style.setProperty('opacity', '1', 'important');
                            content.style.setProperty('visibility', 'visible', 'important');
                            content.style.setProperty('height', 'auto', 'important');
                            content.style.setProperty('position', 'relative', 'important');
                            content.style.setProperty('z-index', '1', 'important');
                            content.style.setProperty('overflow', 'visible', 'important');
                            
                            // 부모 요소도 확인 및 수정
                            if (parentSection) {
                                parentSection.style.setProperty('display', 'block', 'important');
                            }
                            if (parentGlobal) {
                                parentGlobal.style.setProperty('display', 'block', 'important');
                            }
                            if (parentContainer) {
                                parentContainer.style.setProperty('display', 'block', 'important');
                            }
                            
                            // 강제 리플로우로 브라우저에 변경사항 적용
                            void content.offsetHeight;
                            var computedDisplay = window.getComputedStyle(content).display;
                            var computedVisibility = window.getComputedStyle(content).visibility;
                            console.log('[JJ Command Center] Initial tab shown:', contentName, '- display:', computedDisplay, '- visibility:', computedVisibility);
                            
                            if (computedDisplay === 'none' || computedVisibility === 'hidden') {
                                console.warn('[JJ Command Center] Warning: Tab content still hidden after activation! Parent states:', {
                                    section: parentSection ? window.getComputedStyle(parentSection).display : 'N/A',
                                    global: parentGlobal ? window.getComputedStyle(parentGlobal).display : 'N/A',
                                    container: parentContainer ? window.getComputedStyle(parentContainer).display : 'N/A'
                                });
                            }
                        } else {
                            // 비활성 탭: 클래스 제거 + 인라인 스타일
                            content.classList.remove('is-active');
                            content.style.setProperty('display', 'none', 'important');
                        }
                    });
                }
                
                subTabs.forEach(function(tab) {
                    tab.addEventListener('click', function(e) {
                        e.preventDefault();
                        var tabName = this.getAttribute('data-tab');
                        console.log('[JJ Command Center] Sub-tab clicked:', tabName);
                        
                        // 모든 탭 버튼 비활성화
                        subTabs.forEach(function(t) { t.classList.remove('is-active'); });
                        this.classList.add('is-active');
                        
                        // [v26.0.11] 모든 탭 콘텐츠 숨기기 (CSS 충돌 해결)
                        subContents.forEach(function(content) {
                            content.classList.remove('is-active');
                            content.style.setProperty('display', 'none', 'important');
                        });
                        
                        // [v26.0.11] 타겟 탭 콘텐츠 표시 (CSS 충돌 해결 + 강제 리플로우 + 부모 요소 확인)
                        var targetContent = container.querySelector('.jj-tab-content[data-tab-content="' + tabName + '"]');
                        if (targetContent) {
                            targetContent.classList.add('is-active');
                            targetContent.style.setProperty('display', 'block', 'important');
                            targetContent.style.setProperty('opacity', '1', 'important');
                            targetContent.style.setProperty('visibility', 'visible', 'important');
                            targetContent.style.setProperty('height', 'auto', 'important');
                            targetContent.style.setProperty('position', 'relative', 'important');
                            targetContent.style.setProperty('z-index', '1', 'important');
                            targetContent.style.setProperty('overflow', 'visible', 'important');
                            
                            // 부모 요소도 확인 및 수정
                            var parentSection = targetContent.closest('.jj-section-wrapper');
                            var parentGlobal = targetContent.closest('.jj-section-global');
                            var parentContainer = targetContent.closest('.jj-tabs-container');
                            
                            if (parentSection) {
                                parentSection.style.setProperty('display', 'block', 'important');
                            }
                            if (parentGlobal) {
                                parentGlobal.style.setProperty('display', 'block', 'important');
                            }
                            if (parentContainer) {
                                parentContainer.style.setProperty('display', 'block', 'important');
                            }
                            
                            // 강제 리플로우로 브라우저에 변경사항 적용
                            void targetContent.offsetHeight;
                            var computedDisplay = window.getComputedStyle(targetContent).display;
                            var computedVisibility = window.getComputedStyle(targetContent).visibility;
                            console.log('[JJ Command Center] Sub-tab content activated:', tabName, '- display:', computedDisplay, '- visibility:', computedVisibility);
                            
                            if (computedDisplay === 'none' || computedVisibility === 'hidden') {
                                console.warn('[JJ Command Center] Warning: Tab content still hidden after activation! Parent states:', {
                                    section: parentSection ? window.getComputedStyle(parentSection).display : 'N/A',
                                    global: parentGlobal ? window.getComputedStyle(parentGlobal).display : 'N/A',
                                    container: parentContainer ? window.getComputedStyle(parentContainer).display : 'N/A'
                                });
                            }
                        } else {
                            console.error('[JJ Command Center] Sub-tab content not found:', tabName);
                        }
                    });
                });
            });
            
            console.log('[JJ Command Center v26.0.10] Initialization complete!');
        });
        </script>
        <?php
    }

    /**
     * [v22.1.2] 스타일 센터 전용 에셋 로드
     */
    public function enqueue_style_guide_assets( $hook ) {
        // 스타일 센터 페이지인지 확인 (슬러그: jj-style-guide-cockpit)
        if ( strpos( $hook, 'jj-style-guide-cockpit' ) === false ) {
            return;
        }

        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_URL' ) || ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            return; // 상수가 정의되지 않았으면 에셋 로드 중단
        }

        $base_url = JJ_STYLE_GUIDE_URL;
        $version  = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '22.5.2';

        // [v22.1.2] Spectrum Color Picker (Modern Upgrade)
        wp_enqueue_style( 'spectrum-colorpicker', 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.css' );
        wp_enqueue_script( 'spectrum-colorpicker', 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.js', array( 'jquery' ), '1.8.1', true );

        // Editor JS
        wp_enqueue_script(
            'jj-style-guide-editor',
            $base_url . 'assets/js/jj-style-guide-editor.js',
            array( 'jquery', 'wp-color-picker', 'spectrum-colorpicker' ),
            $version,
            true
        );

        // [v22.1.2] Onboarding Tour JS
        wp_enqueue_script(
            'jj-onboarding-tour',
            $base_url . 'assets/js/jj-onboarding-tour.js',
            array( 'jquery' ),
            $version,
            true
        );

        wp_localize_script(
            'jj-style-guide-editor',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'settings' => $this->options,
                'locale'   => get_locale(),
                'show_shortcuts_hint' => true, // 키보드 단축키 힌트 표시 여부
                'i18n'     => array(
                    'saving' => __( '저장 중...', 'acf-css-really-simple-style-management-center' ),
                    'saved'  => __( '저장 완료!', 'acf-css-really-simple-style-management-center' ),
                ),
            )
        );
        
        // WordPress 기본 ajaxurl도 로드 (온보딩 모달에서 사용)
        wp_localize_script(
            'jj-onboarding-tour',
            'ajaxurl',
            admin_url( 'admin-ajax.php' )
        );
        
        // 온보딩 모달에서도 jj_admin_params 사용 가능하도록
        // 주의: wp_localize_script는 각 스크립트 핸들마다 별도 변수를 생성하므로
        // 같은 변수명을 사용해도 문제없지만, 일관성을 위해 동일한 데이터 구조 사용
        wp_localize_script(
            'jj-onboarding-tour',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'locale'   => get_locale(),
                'show_shortcuts_hint' => true,
            )
        );
        
        // 키보드 단축키 스크립트는 class-jj-admin-center.php에서 이미 로드되므로
        // 여기서는 중복 로드하지 않음 (충돌 방지)

        // [v22.2.1] UI System 2026 Enhanced CSS 로드
        if ( defined( 'JJ_STYLE_GUIDE_URL' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // 메인 UI 시스템 CSS
            $ui_system_css_url = JJ_STYLE_GUIDE_URL . 'assets/css/jj-ui-system-2026.css';
            $ui_system_css_path = JJ_STYLE_GUIDE_PATH . 'assets/css/jj-ui-system-2026.css';

            if ( file_exists( $ui_system_css_path ) ) {
                $css_version = $version . '.' . filemtime( $ui_system_css_path );
                wp_enqueue_style( 'jj-ui-system-2026', $ui_system_css_url, array(), $css_version );
            }

            // 섹션 강화 CSS
            $section_css_url = JJ_STYLE_GUIDE_URL . 'assets/css/jj-section-enhancements-2026.css';
            $section_css_path = JJ_STYLE_GUIDE_PATH . 'assets/css/jj-section-enhancements-2026.css';

            if ( file_exists( $section_css_path ) ) {
                $css_version = $version . '.' . filemtime( $section_css_path );
                wp_enqueue_style( 'jj-section-enhancements-2026', $section_css_url, array( 'jj-ui-system-2026' ), $css_version );
            }
        }

        // UI System JS
        $ui_system_js_url = defined( 'JJ_STYLE_GUIDE_URL' ) ? JJ_STYLE_GUIDE_URL . 'assets/js/jj-ui-system-2026.js' : '';
        $ui_system_js_path = defined( 'JJ_STYLE_GUIDE_PATH' ) ? JJ_STYLE_GUIDE_PATH . 'assets/js/jj-ui-system-2026.js' : '';

        if ( file_exists( $ui_system_js_path ) ) {
            $js_version = $version . '.' . filemtime( $ui_system_js_path );
            wp_enqueue_script( 'jj-ui-system-2026-js', $ui_system_js_url, array( 'jquery' ), $js_version, true );
        }

        // [v22.4.7] GUI 개선 CSS 및 JS
        $gui_enhancements_css_url = defined( 'JJ_STYLE_GUIDE_URL' ) ? JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-gui-enhancements.css' : '';
        $gui_enhancements_css_path = defined( 'JJ_STYLE_GUIDE_PATH' ) ? JJ_STYLE_GUIDE_PATH . 'assets/css/jj-style-guide-gui-enhancements.css' : '';

        if ( file_exists( $gui_enhancements_css_path ) ) {
            $css_version = $version . '.' . filemtime( $gui_enhancements_css_path );
            wp_enqueue_style( 'jj-style-guide-gui-enhancements', $gui_enhancements_css_url, array( 'jj-ui-system-2026', 'jj-section-enhancements-2026' ), $css_version );
        }

        $gui_enhancements_js_url = defined( 'JJ_STYLE_GUIDE_URL' ) ? JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-gui-enhancements.js' : '';
        $gui_enhancements_js_path = defined( 'JJ_STYLE_GUIDE_PATH' ) ? JJ_STYLE_GUIDE_PATH . 'assets/js/jj-style-guide-gui-enhancements.js' : '';

        if ( file_exists( $gui_enhancements_js_path ) ) {
            $js_version = $version . '.' . filemtime( $gui_enhancements_js_path );
            wp_enqueue_script( 'jj-style-guide-gui-enhancements', $gui_enhancements_js_url, array( 'jquery', 'spectrum-colorpicker', 'jj-style-guide-editor' ), $js_version, true );
        }
    }

    /**
     * 프론트엔드 스타일 enqueue
     */
    public function enqueue_frontend_styles() {
        if ( empty( $this->options['settings']['apply_to_frontend'] ) ) {
            return;
        }
        
        $css = $this->generate_css_variables();
        wp_add_inline_style( 'wp-block-library', $css );
    }

    /**
     * 관리자 스타일 enqueue
     */
    public function enqueue_admin_styles() {
        if ( empty( $this->options['settings']['apply_to_admin'] ) ) {
            return;
        }
        
        $css = $this->generate_css_variables();
        wp_add_inline_style( 'common', $css );
    }

    /**
     * 커스터마이저 스타일 enqueue
     */
    public function enqueue_customizer_styles() {
        if ( empty( $this->options['settings']['apply_to_customizer'] ) ) {
            return;
        }
        
        $css = $this->generate_css_variables();
        wp_add_inline_style( 'customize-controls', $css );
    }

    /**
     * CSS 변수 생성
     * @return string
     */
    public function generate_css_variables() {
        $css = ":root {\n";
        
        // 색상
        if ( ! empty( $this->options['colors'] ) ) {
            foreach ( $this->options['colors'] as $key => $value ) {
                $css .= "  --jj-color-{$key}: {$value};\n";
            }
        }
        
        // 타이포그래피
        if ( ! empty( $this->options['typography'] ) ) {
            foreach ( $this->options['typography'] as $key => $value ) {
                $var_name = str_replace( '_', '-', $key );
                $css .= "  --jj-{$var_name}: {$value};\n";
            }
        }
        
        // 간격
        if ( ! empty( $this->options['spacing'] ) ) {
            foreach ( $this->options['spacing'] as $key => $value ) {
                $css .= "  --jj-spacing-{$key}: {$value};\n";
            }
        }
        
        // 테두리
        if ( ! empty( $this->options['borders'] ) ) {
            foreach ( $this->options['borders'] as $key => $value ) {
                $var_name = str_replace( '_', '-', $key );
                $css .= "  --jj-border-{$var_name}: {$value};\n";
            }
        }
        
        // 그림자
        if ( ! empty( $this->options['shadows'] ) ) {
            foreach ( $this->options['shadows'] as $key => $value ) {
                $css .= "  --jj-shadow-{$key}: {$value};\n";
            }
        }
        
        $css .= "}\n";
        
        return $css;
    }

    /**
     * 옵션 가져오기
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get_option( $key = '', $default = null ) {
        if ( empty( $key ) ) {
            return $this->options;
        }
        
        // 점 표기법 지원 (예: 'colors.primary')
        $keys = explode( '.', $key );
        $value = $this->options;
        
        foreach ( $keys as $k ) {
            if ( isset( $value[ $k ] ) ) {
                $value = $value[ $k ];
            } else {
                return $default;
            }
        }
        
        return $value;
    }

    /**
     * 옵션 설정
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set_option( $key, $value ) {
        $keys = explode( '.', $key );
        $options = &$this->options;
        
        foreach ( $keys as $i => $k ) {
            if ( $i === count( $keys ) - 1 ) {
                $options[ $k ] = $value;
            } else {
                if ( ! isset( $options[ $k ] ) || ! is_array( $options[ $k ] ) ) {
                    $options[ $k ] = array();
                }
                $options = &$options[ $k ];
            }
        }
        
        return update_option( $this->option_key, $this->options );
    }

    /**
     * 전체 옵션 저장
     * @param array $options
     * @return bool
     */
    public function save_options( $options ) {
        $this->options = wp_parse_args( $options, $this->get_default_options() );
        return update_option( $this->option_key, $this->options );
    }

    /**
     * 옵션 초기화
     * @return bool
     */
    public function reset_options() {
        $this->options = $this->get_default_options();
        return update_option( $this->option_key, $this->options );
    }

    /**
     * REST API 라우트 등록
     */
    public function register_rest_routes() {
        register_rest_route( 'jj-style-guide/v1', '/options', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'rest_get_options' ),
                'permission_callback' => array( $this, 'rest_permission_check' ),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'rest_save_options' ),
                'permission_callback' => array( $this, 'rest_permission_check' ),
            ),
        ) );
        
        register_rest_route( 'jj-style-guide/v1', '/css', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'rest_get_css' ),
            'permission_callback' => '__return_true',
        ) );
    }

    /**
     * REST 권한 체크
     * @return bool
     */
    public function rest_permission_check() {
        return current_user_can( 'manage_options' );
    }

    /**
     * REST: 옵션 가져오기
     * @return WP_REST_Response
     */
    public function rest_get_options() {
        return rest_ensure_response( $this->options );
    }

    /**
     * REST: 옵션 저장
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function rest_save_options( $request ) {
        $options = $request->get_json_params();
        $result = $this->save_options( $options );
        
        return rest_ensure_response( array(
            'success' => $result,
            'options' => $this->options,
        ) );
    }

    /**
     * REST: CSS 가져오기
     * @return WP_REST_Response
     */
    public function rest_get_css() {
        return rest_ensure_response( array(
            'css' => $this->generate_css_variables(),
        ) );
    }

    /**
     * AJAX: 옵션 저장
     */
    public function ajax_save_options() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        // [v22.1.2] 저장 전 히스토리 스냅샷 생성
        if ( class_exists( 'JJ_History_Manager' ) ) {
            JJ_History_Manager::instance()->create_snapshot( 'AJAX Save' );
        }

        $options = isset( $_POST['options'] ) ? json_decode( stripslashes( $_POST['options'] ), true ) : array();
        $result = $this->save_options( $options );
        
        if ( $result ) {
            wp_send_json_success( array(
                'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ),
                'options' => $this->options,
            ) );
        } else {
            wp_send_json_error( __( '저장에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
    }

    /**
     * AJAX: 옵션 초기화
     */
    public function ajax_reset_options() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $result = $this->reset_options();
        
        if ( $result ) {
            wp_send_json_success( array(
                'message' => __( '설정이 초기화되었습니다.', 'acf-css-really-simple-style-management-center' ),
                'options' => $this->options,
            ) );
        } else {
            wp_send_json_error( __( '초기화에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
    }

    /**
     * AJAX: 옵션 내보내기
     */
    public function ajax_export_options() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        wp_send_json_success( array(
            'data' => $this->options,
            'filename' => 'jj-style-guide-export-' . date( 'Y-m-d' ) . '.json',
        ) );
    }

    /**
     * AJAX: 옵션 가져오기
     */
    public function ajax_import_options() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $import_data = isset( $_POST['data'] ) ? json_decode( stripslashes( $_POST['data'] ), true ) : null;
        
        if ( ! $import_data || ! is_array( $import_data ) ) {
            wp_send_json_error( __( '유효하지 않은 데이터입니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $result = $this->save_options( $import_data );
        
        if ( $result ) {
            wp_send_json_success( array(
                'message' => __( '설정을 가져왔습니다.', 'acf-css-really-simple-style-management-center' ),
                'options' => $this->options,
            ) );
        } else {
            wp_send_json_error( __( '가져오기에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
    }

    /**
     * AJAX: 추천 설정 적용
     */
    public function ajax_apply_recommended_setup() {
        // nonce 검증 (jj_admin_params.nonce 사용)
        if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'jj_style_guide_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( '보안 검증에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        // 추천 설정 적용 (기본 색상 팔레트 및 타이포그래피 설정)
        $recommended_options = array(
            'colors' => array(
                'primary' => '#3b82f6',
                'secondary' => '#8b5cf6',
                'accent' => '#f59e0b',
                'success' => '#10b981',
                'warning' => '#f59e0b',
                'error' => '#ef4444',
            ),
            'typography' => array(
                'font_family' => 'system-ui, -apple-system, sans-serif',
                'font_size_base' => '16px',
                'line_height' => '1.6',
            ),
        );
        
        // 기존 옵션과 병합
        $merged_options = array_merge( $this->options, $recommended_options );
        $result = $this->save_options( $merged_options );
        
        if ( $result ) {
            // 온보딩 완료 플래그 설정
            update_user_meta( get_current_user_id(), 'jj_css_onboarding_completed', time() );
            
            wp_send_json_success( array(
                'message' => __( '추천 디자인 시스템이 적용되었습니다!', 'acf-css-really-simple-style-management-center' ),
                'options' => $this->options,
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( '설정 적용에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
    }

    /**
     * 색상 팔레트 가져오기
     * @return array
     */
    public function get_colors() {
        return isset( $this->options['colors'] ) ? $this->options['colors'] : array();
    }

    /**
     * 타이포그래피 설정 가져오기
     * @return array
     */
    public function get_typography() {
        return isset( $this->options['typography'] ) ? $this->options['typography'] : array();
    }

    /**
     * 간격 설정 가져오기
     * @return array
     */
    public function get_spacing() {
        return isset( $this->options['spacing'] ) ? $this->options['spacing'] : array();
    }

    /**
     * 버튼 스타일 가져오기
     * @return array
     */
    public function get_button_styles() {
        return isset( $this->options['buttons'] ) ? $this->options['buttons'] : array();
    }

    /**
     * 폼 스타일 가져오기
     * @return array
     */
    public function get_form_styles() {
        return isset( $this->options['forms'] ) ? $this->options['forms'] : array();
    }
}
