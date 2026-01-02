<?php
/**
 * ACF Code Snippets Box - Third-Party Integrations
 *
 * FacetWP, Perfmatters, ACF (Advanced Custom Fields) 등
 * 서드파티 플러그인 연동 기능
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Integrations 클래스
 */
class ACF_CSB_Integrations {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 연동 가능한 플러그인 목록
     */
    private $available_integrations = array();

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 초기화
     */
    public function init() {
        $this->register_integrations();
        $this->load_active_integrations();

        // 설정 페이지에 연동 섹션 추가
        add_action( 'acf_csb_settings_sections', array( $this, 'add_integration_settings' ) );

        // AJAX
        add_action( 'wp_ajax_acf_csb_get_acf_fields', array( $this, 'ajax_get_acf_fields' ) );
        add_action( 'wp_ajax_acf_csb_get_facetwp_facets', array( $this, 'ajax_get_facetwp_facets' ) );
    }

    /**
     * 연동 플러그인 등록
     */
    private function register_integrations() {
        $this->available_integrations = array(
            // ========================================
            // Advanced Custom Fields (ACF)
            // ========================================
            'acf' => array(
                'name'        => 'Advanced Custom Fields',
                'slug'        => 'advanced-custom-fields',
                'description' => __( 'ACF 필드 값을 조건으로 사용하고, ACF 데이터를 스니펫에서 활용합니다.', 'acf-code-snippets-box' ),
                'icon'        => 'dashicons-forms',
                'pro_only'    => true,
                'min_tier'    => 'basic',
                'features'    => array(
                    __( 'ACF 필드 값 기반 조건', 'acf-code-snippets-box' ),
                    __( 'ACF 옵션 페이지 데이터 접근', 'acf-code-snippets-box' ),
                    __( 'ACF 블록 내 스니펫 실행', 'acf-code-snippets-box' ),
                    __( 'ACF 반복기/유연한 콘텐츠 지원', 'acf-code-snippets-box' ),
                ),
                'check'       => array( $this, 'is_acf_active' ),
                'init'        => array( $this, 'init_acf_integration' ),
            ),

            // ========================================
            // FacetWP
            // ========================================
            'facetwp' => array(
                'name'        => 'FacetWP',
                'slug'        => 'facetwp',
                'description' => __( 'FacetWP 필터 결과에 따른 조건부 스니펫 실행 및 스타일 적용.', 'acf-code-snippets-box' ),
                'icon'        => 'dashicons-filter',
                'pro_only'    => true,
                'min_tier'    => 'premium',
                'features'    => array(
                    __( 'Facet 선택값 기반 조건', 'acf-code-snippets-box' ),
                    __( 'FacetWP AJAX 리프레시 시 스니펫 재실행', 'acf-code-snippets-box' ),
                    __( 'Facet 결과 수 기반 조건', 'acf-code-snippets-box' ),
                    __( 'FacetWP 스타일 커스터마이징', 'acf-code-snippets-box' ),
                ),
                'check'       => array( $this, 'is_facetwp_active' ),
                'init'        => array( $this, 'init_facetwp_integration' ),
            ),

            // ========================================
            // Perfmatters
            // ========================================
            'perfmatters' => array(
                'name'        => 'Perfmatters',
                'slug'        => 'perfmatters',
                'description' => __( 'Perfmatters의 스크립트/스타일 관리와 통합하여 조건부 로딩을 최적화합니다.', 'acf-code-snippets-box' ),
                'icon'        => 'dashicons-performance',
                'pro_only'    => true,
                'min_tier'    => 'premium',
                'features'    => array(
                    __( 'Perfmatters 스크립트 매니저 연동', 'acf-code-snippets-box' ),
                    __( '조건부 스니펫 지연 로딩', 'acf-code-snippets-box' ),
                    __( '미사용 CSS 제거와 호환', 'acf-code-snippets-box' ),
                    __( 'JavaScript 지연 실행 설정', 'acf-code-snippets-box' ),
                ),
                'check'       => array( $this, 'is_perfmatters_active' ),
                'init'        => array( $this, 'init_perfmatters_integration' ),
            ),

            // ========================================
            // WooCommerce
            // ========================================
            'woocommerce' => array(
                'name'        => 'WooCommerce',
                'slug'        => 'woocommerce',
                'description' => __( 'WooCommerce 상품, 주문, 고객 데이터 기반 조건부 스니펫 실행.', 'acf-code-snippets-box' ),
                'icon'        => 'dashicons-cart',
                'pro_only'    => true,
                'min_tier'    => 'basic',
                'features'    => array(
                    __( '상품 카테고리/태그 기반 조건', 'acf-code-snippets-box' ),
                    __( '장바구니 내용 기반 조건', 'acf-code-snippets-box' ),
                    __( '고객 구매 이력 기반 조건', 'acf-code-snippets-box' ),
                    __( '주문 상태별 조건', 'acf-code-snippets-box' ),
                ),
                'check'       => array( $this, 'is_woocommerce_active' ),
                'init'        => array( $this, 'init_woocommerce_integration' ),
            ),

            // ========================================
            // Elementor
            // ========================================
            'elementor' => array(
                'name'        => 'Elementor',
                'slug'        => 'elementor',
                'description' => __( 'Elementor 편집기와 통합하여 위젯 내 스니펫 사용.', 'acf-code-snippets-box' ),
                'icon'        => 'dashicons-welcome-widgets-menus',
                'pro_only'    => true,
                'min_tier'    => 'basic',
                'features'    => array(
                    __( 'Elementor 위젯으로 스니펫 삽입', 'acf-code-snippets-box' ),
                    __( 'Dynamic Tags 지원', 'acf-code-snippets-box' ),
                    __( '편집기 내 실시간 미리보기', 'acf-code-snippets-box' ),
                ),
                'check'       => array( $this, 'is_elementor_active' ),
                'init'        => array( $this, 'init_elementor_integration' ),
            ),

            // ========================================
            // Gutenberg (블록 에디터)
            // ========================================
            'gutenberg' => array(
                'name'        => 'Gutenberg Blocks',
                'slug'        => 'gutenberg',
                'description' => __( '블록 에디터에서 스니펫을 블록으로 삽입합니다.', 'acf-code-snippets-box' ),
                'icon'        => 'dashicons-block-default',
                'pro_only'    => false,
                'min_tier'    => 'free',
                'features'    => array(
                    __( 'Code Snippet 블록', 'acf-code-snippets-box' ),
                    __( '조건부 블록 표시', 'acf-code-snippets-box' ),
                    __( '블록 스타일 옵션', 'acf-code-snippets-box' ),
                ),
                'check'       => '__return_true',
                'init'        => array( $this, 'init_gutenberg_integration' ),
            ),
        );

        // 필터로 추가 연동 등록 가능
        $this->available_integrations = apply_filters( 
            'acf_csb_available_integrations', 
            $this->available_integrations 
        );
    }

    /**
     * 활성 연동 로드
     */
    private function load_active_integrations() {
        foreach ( $this->available_integrations as $key => $integration ) {
            // 플러그인 활성화 확인
            $is_active = is_callable( $integration['check'] ) 
                ? call_user_func( $integration['check'] ) 
                : false;

            if ( ! $is_active ) {
                continue;
            }

            // 라이선스 확인
            if ( $integration['pro_only'] && ! ACF_CSB_License::has_access( $integration['min_tier'] ) ) {
                continue;
            }

            // 연동 초기화
            if ( is_callable( $integration['init'] ) ) {
                call_user_func( $integration['init'] );
            }
        }
    }

    // ========================================
    // 플러그인 활성화 체크 함수들
    // ========================================

    public function is_acf_active() {
        return class_exists( 'ACF' ) || function_exists( 'get_field' );
    }

    public function is_facetwp_active() {
        return class_exists( 'FacetWP' ) || function_exists( 'facetwp_display' );
    }

    public function is_perfmatters_active() {
        return class_exists( 'Perfmatters' ) || defined( 'PERFMATTERS_VERSION' );
    }

    public function is_woocommerce_active() {
        return class_exists( 'WooCommerce' );
    }

    public function is_elementor_active() {
        return did_action( 'elementor/loaded' );
    }

    // ========================================
    // ACF 연동
    // ========================================

    public function init_acf_integration() {
        // ACF 필드 기반 조건 추가
        add_filter( 'acf_csb_condition_types', array( $this, 'add_acf_condition_types' ) );

        // 조건 빌더에서 ACF 필드 선택 지원
        add_action( 'acf_csb_condition_builder_scripts', array( $this, 'acf_condition_scripts' ) );

        // ACF 필드 값 평가
        add_filter( 'acf_csb_evaluate_condition', array( $this, 'evaluate_acf_condition' ), 10, 2 );
    }

    public function add_acf_condition_types( $types ) {
        $types['acf_field_value'] = array(
            'name'        => __( 'ACF 필드 값', 'acf-code-snippets-box' ),
            'description' => __( 'ACF 필드의 값을 기준으로 조건 판단', 'acf-code-snippets-box' ),
            'pro_only'    => true,
            'min_tier'    => 'basic',
            'category'    => 'acf',
        );

        $types['acf_field_exists'] = array(
            'name'        => __( 'ACF 필드 존재 여부', 'acf-code-snippets-box' ),
            'description' => __( 'ACF 필드가 값을 가지고 있는지 확인', 'acf-code-snippets-box' ),
            'pro_only'    => true,
            'min_tier'    => 'basic',
            'category'    => 'acf',
        );

        $types['acf_repeater_count'] = array(
            'name'        => __( 'ACF 반복기 행 수', 'acf-code-snippets-box' ),
            'description' => __( 'ACF 반복기 필드의 행 개수 기준', 'acf-code-snippets-box' ),
            'pro_only'    => true,
            'min_tier'    => 'premium',
            'category'    => 'acf',
        );

        return $types;
    }

    public function evaluate_acf_condition( $result, $condition ) {
        if ( ! function_exists( 'get_field' ) ) {
            return $result;
        }

        $type = $condition['type'];
        $field_name = isset( $condition['value'] ) ? $condition['value'] : '';
        $expected = isset( $condition['value2'] ) ? $condition['value2'] : '';
        $operator = isset( $condition['operator'] ) ? $condition['operator'] : '==';

        switch ( $type ) {
            case 'acf_field_value':
                $field_value = get_field( $field_name );
                return $this->compare_acf_values( $field_value, $expected, $operator );

            case 'acf_field_exists':
                $field_value = get_field( $field_name );
                $exists = ! empty( $field_value );
                return $operator === 'is' ? $exists : ! $exists;

            case 'acf_repeater_count':
                $count = 0;
                if ( have_rows( $field_name ) ) {
                    while ( have_rows( $field_name ) ) {
                        the_row();
                        $count++;
                    }
                    reset_rows();
                }
                return $this->compare_acf_values( $count, intval( $expected ), $operator );
        }

        return $result;
    }

    private function compare_acf_values( $actual, $expected, $operator ) {
        switch ( $operator ) {
            case '==':
            case '=':
                return $actual == $expected;
            case '!=':
                return $actual != $expected;
            case '>':
                return $actual > $expected;
            case '>=':
                return $actual >= $expected;
            case '<':
                return $actual < $expected;
            case '<=':
                return $actual <= $expected;
            case 'contains':
                return is_string( $actual ) && strpos( $actual, $expected ) !== false;
            case 'not_contains':
                return is_string( $actual ) && strpos( $actual, $expected ) === false;
            default:
                return $actual == $expected;
        }
    }

    public function acf_condition_scripts() {
        ?>
        <script>
        // ACF 필드 목록 로드
        jQuery(document).ready(function($) {
            window.acfCsbLoadAcfFields = function(callback) {
                $.ajax({
                    url: acfCsbAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'acf_csb_get_acf_fields',
                        nonce: acfCsbAdmin.nonce
                    },
                    success: function(response) {
                        if (response.success && callback) {
                            callback(response.data);
                        }
                    }
                });
            };
        });
        </script>
        <?php
    }

    public function ajax_get_acf_fields() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! function_exists( 'acf_get_field_groups' ) ) {
            wp_send_json_error( __( 'ACF가 설치되어 있지 않습니다.', 'acf-code-snippets-box' ) );
        }

        $field_groups = acf_get_field_groups();
        $fields = array();

        foreach ( $field_groups as $group ) {
            $group_fields = acf_get_fields( $group['key'] );
            
            if ( ! $group_fields ) {
                continue;
            }

            foreach ( $group_fields as $field ) {
                $fields[] = array(
                    'name'  => $field['name'],
                    'label' => $field['label'],
                    'type'  => $field['type'],
                    'group' => $group['title'],
                );
            }
        }

        wp_send_json_success( $fields );
    }

    // ========================================
    // FacetWP 연동
    // ========================================

    public function init_facetwp_integration() {
        // FacetWP 조건 추가
        add_filter( 'acf_csb_condition_types', array( $this, 'add_facetwp_condition_types' ) );

        // FacetWP AJAX 리프레시 시 스니펫 재실행
        add_action( 'wp_footer', array( $this, 'facetwp_refresh_handler' ) );

        // FacetWP 스타일 프리셋 추가
        add_filter( 'acf_csb_css_presets', array( $this, 'add_facetwp_presets' ) );
    }

    public function add_facetwp_condition_types( $types ) {
        $types['facetwp_facet_value'] = array(
            'name'        => __( 'FacetWP Facet 값', 'acf-code-snippets-box' ),
            'description' => __( '특정 Facet의 선택된 값 기준', 'acf-code-snippets-box' ),
            'pro_only'    => true,
            'min_tier'    => 'premium',
            'category'    => 'facetwp',
        );

        $types['facetwp_has_selection'] = array(
            'name'        => __( 'FacetWP 선택 여부', 'acf-code-snippets-box' ),
            'description' => __( 'Facet에 선택된 값이 있는지 확인', 'acf-code-snippets-box' ),
            'pro_only'    => true,
            'min_tier'    => 'premium',
            'category'    => 'facetwp',
        );

        $types['facetwp_result_count'] = array(
            'name'        => __( 'FacetWP 결과 수', 'acf-code-snippets-box' ),
            'description' => __( '필터링된 결과 개수 기준', 'acf-code-snippets-box' ),
            'pro_only'    => true,
            'min_tier'    => 'premium',
            'category'    => 'facetwp',
        );

        return $types;
    }

    public function facetwp_refresh_handler() {
        if ( ! function_exists( 'facetwp_display' ) ) {
            return;
        }
        ?>
        <script>
        document.addEventListener('facetwp-loaded', function() {
            // FacetWP 로드 완료 후 조건부 스니펫 재평가
            if (typeof acfCsbRefreshConditionalSnippets === 'function') {
                acfCsbRefreshConditionalSnippets();
            }
        });
        </script>
        <?php
    }

    public function add_facetwp_presets( $presets ) {
        $presets['facetwp-loading-overlay'] = array(
            'name'        => __( 'FacetWP 로딩 오버레이', 'acf-code-snippets-box' ),
            'description' => __( 'FacetWP 필터 적용 시 로딩 오버레이 스타일', 'acf-code-snippets-box' ),
            'category'    => 'facetwp',
            'code'        => ".facetwp-loading {\n    opacity: 0.5;\n    pointer-events: none;\n    transition: opacity 0.3s ease;\n}\n\n.facetwp-overlay {\n    position: fixed;\n    top: 0;\n    left: 0;\n    width: 100%;\n    height: 100%;\n    background: rgba(255, 255, 255, 0.8);\n    z-index: 9999;\n    display: flex;\n    align-items: center;\n    justify-content: center;\n}",
        );

        $presets['facetwp-facet-style'] = array(
            'name'        => __( 'FacetWP Facet 스타일', 'acf-code-snippets-box' ),
            'description' => __( 'FacetWP Facet 체크박스/라디오 스타일', 'acf-code-snippets-box' ),
            'category'    => 'facetwp',
            'code'        => ".facetwp-facet {\n    margin-bottom: 20px;\n}\n\n.facetwp-facet .facetwp-checkbox,\n.facetwp-facet .facetwp-radio {\n    display: flex;\n    align-items: center;\n    padding: 8px 0;\n    cursor: pointer;\n    transition: background 0.2s ease;\n}\n\n.facetwp-facet .facetwp-checkbox:hover,\n.facetwp-facet .facetwp-radio:hover {\n    background: rgba(0, 0, 0, 0.05);\n}\n\n.facetwp-facet .facetwp-counter {\n    margin-left: auto;\n    color: #888;\n    font-size: 0.875em;\n}",
        );

        return $presets;
    }

    public function ajax_get_facetwp_facets() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! class_exists( 'FacetWP' ) ) {
            wp_send_json_error( __( 'FacetWP가 설치되어 있지 않습니다.', 'acf-code-snippets-box' ) );
        }

        $facets = FWP()->helper->get_facets();
        $result = array();

        foreach ( $facets as $facet ) {
            $result[] = array(
                'name'  => $facet['name'],
                'label' => $facet['label'],
                'type'  => $facet['type'],
            );
        }

        wp_send_json_success( $result );
    }

    // ========================================
    // Perfmatters 연동
    // ========================================

    public function init_perfmatters_integration() {
        // Perfmatters 스크립트 매니저와 호환
        add_filter( 'perfmatters_lazyload_youtube_thumbnail_resolution', array( $this, 'perfmatters_compatibility' ) );

        // 조건부 스니펫 지연 로딩 옵션
        add_action( 'acf_csb_snippet_options', array( $this, 'add_perfmatters_options' ) );

        // 스니펫 출력 시 Perfmatters 설정 적용
        add_filter( 'acf_csb_snippet_output', array( $this, 'apply_perfmatters_settings' ), 10, 2 );
    }

    public function perfmatters_compatibility( $resolution ) {
        // Perfmatters와 호환성 유지
        return $resolution;
    }

    public function add_perfmatters_options( $snippet_id ) {
        $delay_js = get_post_meta( $snippet_id, '_acf_csb_delay_js', true );
        $exclude_lazyload = get_post_meta( $snippet_id, '_acf_csb_exclude_lazyload', true );
        ?>
        <div class="acf-csb-perfmatters-options">
            <h4><?php esc_html_e( 'Perfmatters 연동', 'acf-code-snippets-box' ); ?></h4>
            
            <p>
                <label>
                    <input type="checkbox" name="acf_csb_delay_js" value="1" <?php checked( $delay_js, '1' ); ?>>
                    <?php esc_html_e( 'JavaScript 지연 실행', 'acf-code-snippets-box' ); ?>
                </label>
                <span class="description"><?php esc_html_e( '사용자 상호작용 시까지 스크립트 실행을 지연합니다.', 'acf-code-snippets-box' ); ?></span>
            </p>
            
            <p>
                <label>
                    <input type="checkbox" name="acf_csb_exclude_lazyload" value="1" <?php checked( $exclude_lazyload, '1' ); ?>>
                    <?php esc_html_e( 'Lazy Load 제외', 'acf-code-snippets-box' ); ?>
                </label>
                <span class="description"><?php esc_html_e( '이 스니펫의 이미지를 지연 로딩에서 제외합니다.', 'acf-code-snippets-box' ); ?></span>
            </p>
        </div>
        <?php
    }

    public function apply_perfmatters_settings( $output, $snippet_id ) {
        $delay_js = get_post_meta( $snippet_id, '_acf_csb_delay_js', true );

        if ( $delay_js && strpos( $output, '<script' ) !== false ) {
            // JavaScript 지연 실행 적용
            $output = str_replace( '<script', '<script type="pmdelayedscript"', $output );
        }

        return $output;
    }

    // ========================================
    // WooCommerce 연동
    // ========================================

    public function init_woocommerce_integration() {
        // WooCommerce 조건 추가
        add_filter( 'acf_csb_condition_types', array( $this, 'add_woocommerce_condition_types' ) );

        // WooCommerce 조건 평가
        add_filter( 'acf_csb_evaluate_condition', array( $this, 'evaluate_woocommerce_condition' ), 10, 2 );
    }

    public function add_woocommerce_condition_types( $types ) {
        $types['wc_product_category'] = array(
            'name'        => __( '상품 카테고리', 'acf-code-snippets-box' ),
            'description' => __( '현재 상품의 카테고리 기준', 'acf-code-snippets-box' ),
            'pro_only'    => true,
            'min_tier'    => 'basic',
            'category'    => 'woocommerce',
        );

        $types['wc_cart_total'] = array(
            'name'        => __( '장바구니 합계', 'acf-code-snippets-box' ),
            'description' => __( '장바구니 총액 기준', 'acf-code-snippets-box' ),
            'pro_only'    => true,
            'min_tier'    => 'premium',
            'category'    => 'woocommerce',
        );

        $types['wc_cart_items'] = array(
            'name'        => __( '장바구니 상품 수', 'acf-code-snippets-box' ),
            'description' => __( '장바구니 내 상품 개수 기준', 'acf-code-snippets-box' ),
            'pro_only'    => true,
            'min_tier'    => 'basic',
            'category'    => 'woocommerce',
        );

        $types['wc_customer_order_count'] = array(
            'name'        => __( '고객 주문 횟수', 'acf-code-snippets-box' ),
            'description' => __( '현재 고객의 총 주문 횟수 기준', 'acf-code-snippets-box' ),
            'pro_only'    => true,
            'min_tier'    => 'unlimited',
            'category'    => 'woocommerce',
        );

        return $types;
    }

    public function evaluate_woocommerce_condition( $result, $condition ) {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return $result;
        }

        $type = $condition['type'];
        $value = isset( $condition['value'] ) ? $condition['value'] : '';
        $operator = isset( $condition['operator'] ) ? $condition['operator'] : '==';

        switch ( $type ) {
            case 'wc_product_category':
                if ( is_product() ) {
                    global $product;
                    return has_term( $value, 'product_cat', $product->get_id() );
                }
                return false;

            case 'wc_cart_total':
                if ( WC()->cart ) {
                    $total = WC()->cart->get_total( 'edit' );
                    return $this->compare_acf_values( $total, floatval( $value ), $operator );
                }
                return false;

            case 'wc_cart_items':
                if ( WC()->cart ) {
                    $count = WC()->cart->get_cart_contents_count();
                    return $this->compare_acf_values( $count, intval( $value ), $operator );
                }
                return false;

            case 'wc_customer_order_count':
                if ( is_user_logged_in() ) {
                    $count = wc_get_customer_order_count( get_current_user_id() );
                    return $this->compare_acf_values( $count, intval( $value ), $operator );
                }
                return false;
        }

        return $result;
    }

    // ========================================
    // Elementor 연동
    // ========================================

    public function init_elementor_integration() {
        // Elementor 위젯 등록
        add_action( 'elementor/widgets/register', array( $this, 'register_elementor_widget' ) );
    }

    public function register_elementor_widget( $widgets_manager ) {
        // Elementor 위젯 클래스 로드
        require_once ACF_CSB_PATH . 'includes/integrations/class-elementor-widget.php';
        
        if ( class_exists( 'ACF_CSB_Elementor_Widget' ) ) {
            $widgets_manager->register( new ACF_CSB_Elementor_Widget() );
        }
    }

    // ========================================
    // Gutenberg 연동
    // ========================================

    public function init_gutenberg_integration() {
        // 블록 등록
        add_action( 'init', array( $this, 'register_gutenberg_block' ) );
    }

    public function register_gutenberg_block() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        register_block_type( 'acf-csb/code-snippet', array(
            'editor_script'   => 'acf-csb-block-editor',
            'editor_style'    => 'acf-csb-block-editor-style',
            'render_callback' => array( $this, 'render_gutenberg_block' ),
            'attributes'      => array(
                'snippetId' => array(
                    'type'    => 'number',
                    'default' => 0,
                ),
                'showTitle' => array(
                    'type'    => 'boolean',
                    'default' => false,
                ),
            ),
        ) );
    }

    public function render_gutenberg_block( $attributes ) {
        $snippet_id = isset( $attributes['snippetId'] ) ? intval( $attributes['snippetId'] ) : 0;
        
        if ( ! $snippet_id ) {
            return '';
        }

        $snippet = get_post( $snippet_id );
        
        if ( ! $snippet || $snippet->post_type !== 'acf_code_snippet' ) {
            return '';
        }

        // 스니펫 실행
        $output = ACF_CSB_Executor::instance()->execute_snippet( $snippet_id );

        if ( ! empty( $attributes['showTitle'] ) ) {
            $output = '<div class="acf-csb-snippet-block"><h4>' . esc_html( $snippet->post_title ) . '</h4>' . $output . '</div>';
        }

        return $output;
    }

    // ========================================
    // 설정 페이지 연동 섹션
    // ========================================

    public function add_integration_settings() {
        ?>
        <div class="acf-csb-integrations-section">
            <h2><?php esc_html_e( '플러그인 연동', 'acf-code-snippets-box' ); ?></h2>
            <p class="description">
                <?php esc_html_e( '다른 플러그인과의 연동을 통해 더 강력한 기능을 사용할 수 있습니다.', 'acf-code-snippets-box' ); ?>
            </p>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( '플러그인', 'acf-code-snippets-box' ); ?></th>
                        <th><?php esc_html_e( '상태', 'acf-code-snippets-box' ); ?></th>
                        <th><?php esc_html_e( '필요 요금제', 'acf-code-snippets-box' ); ?></th>
                        <th><?php esc_html_e( '주요 기능', 'acf-code-snippets-box' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $this->available_integrations as $key => $integration ) : 
                        $is_active = is_callable( $integration['check'] ) 
                            ? call_user_func( $integration['check'] ) 
                            : false;
                        $has_license = ! $integration['pro_only'] || ACF_CSB_License::has_access( $integration['min_tier'] );
                    ?>
                    <tr>
                        <td>
                            <span class="dashicons <?php echo esc_attr( $integration['icon'] ); ?>"></span>
                            <strong><?php echo esc_html( $integration['name'] ); ?></strong>
                            <p class="description"><?php echo esc_html( $integration['description'] ); ?></p>
                        </td>
                        <td>
                            <?php if ( $is_active && $has_license ) : ?>
                                <span class="acf-csb-status-active">✅ <?php esc_html_e( '활성', 'acf-code-snippets-box' ); ?></span>
                            <?php elseif ( $is_active && ! $has_license ) : ?>
                                <span class="acf-csb-status-locked">🔒 <?php esc_html_e( '업그레이드 필요', 'acf-code-snippets-box' ); ?></span>
                            <?php else : ?>
                                <span class="acf-csb-status-inactive">⚫ <?php esc_html_e( '미설치', 'acf-code-snippets-box' ); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $tier_names = array(
                                'free' => 'Free',
                                'basic' => 'Pro Basic',
                                'premium' => 'Pro Premium',
                                'unlimited' => 'Pro Unlimited',
                            );
                            echo esc_html( isset( $tier_names[ $integration['min_tier'] ] ) ? $tier_names[ $integration['min_tier'] ] : $integration['min_tier'] );
                            ?>
                        </td>
                        <td>
                            <ul class="acf-csb-feature-list">
                                <?php foreach ( array_slice( $integration['features'], 0, 2 ) as $feature ) : ?>
                                    <li><?php echo esc_html( $feature ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * 연동 정보 가져오기
     */
    public function get_integrations_info() {
        return $this->available_integrations;
    }
}
