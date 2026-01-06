<?php
/**
 * Dynamic Price Display
 *
 * 동적 가격 표시 기능 - 실시간 할인 타이머, 재고 기반 가격, 사용자별 가격
 *
 * @package ACF_CSS_WooCommerce_Toolkit
 * @version 2.5.0
 * @since Phase 49-4
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_WC_Dynamic_Price_Display {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 설정
     */
    private $settings = array();

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->load_settings();
        $this->init_hooks();
    }

    /**
     * 설정 로드
     */
    private function load_settings() {
        $defaults = array(
            'enable_countdown'          => true,
            'enable_stock_urgency'      => true,
            'enable_bulk_pricing'       => true,
            'enable_user_pricing'       => true,
            'enable_price_history'      => true,
            'countdown_style'           => 'modern',
            'urgency_threshold'         => 5,
            'show_savings_percentage'   => true,
            'show_price_per_unit'       => true,
            'animate_price_changes'     => true,
        );

        $saved = get_option( 'acf_wc_dynamic_price_settings', array() );
        $this->settings = wp_parse_args( $saved, $defaults );
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 프론트엔드 가격 필터
        add_filter( 'woocommerce_get_price_html', array( $this, 'enhance_price_html' ), 100, 2 );

        // 상품 페이지 추가 정보
        add_action( 'woocommerce_single_product_summary', array( $this, 'display_dynamic_price_info' ), 15 );

        // 루프 내 추가 정보
        add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'display_loop_price_badges' ), 15 );

        // AJAX 핸들러
        add_action( 'wp_ajax_jj_get_dynamic_price', array( $this, 'ajax_get_dynamic_price' ) );
        add_action( 'wp_ajax_nopriv_jj_get_dynamic_price', array( $this, 'ajax_get_dynamic_price' ) );
        add_action( 'wp_ajax_jj_get_bulk_pricing', array( $this, 'ajax_get_bulk_pricing' ) );
        add_action( 'wp_ajax_nopriv_jj_get_bulk_pricing', array( $this, 'ajax_get_bulk_pricing' ) );

        // 에셋 로드
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // 관리자 설정
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        // 상품 메타 필드
        add_action( 'woocommerce_product_options_pricing', array( $this, 'add_dynamic_price_fields' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_dynamic_price_fields' ) );

        // REST API
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets() {
        if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
            return;
        }

        wp_enqueue_style(
            'acf-wc-dynamic-price',
            ACF_CSS_WC_PLUGIN_URL . 'assets/css/dynamic-price.css',
            array(),
            ACF_CSS_WC_VERSION
        );

        wp_enqueue_script(
            'acf-wc-dynamic-price',
            ACF_CSS_WC_PLUGIN_URL . 'assets/js/dynamic-price.js',
            array( 'jquery' ),
            ACF_CSS_WC_VERSION,
            true
        );

        wp_localize_script( 'acf-wc-dynamic-price', 'acfDynamicPrice', array(
            'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
            'nonce'            => wp_create_nonce( 'acf_dynamic_price_nonce' ),
            'currency'         => get_woocommerce_currency_symbol(),
            'decimals'         => wc_get_price_decimals(),
            'decimalSep'       => wc_get_price_decimal_separator(),
            'thousandSep'      => wc_get_price_thousand_separator(),
            'priceFormat'      => get_woocommerce_price_format(),
            'animatePrices'    => $this->settings['animate_price_changes'],
            'i18n'             => array(
                'days'         => __( '일', 'acf-css-woocommerce-toolkit' ),
                'hours'        => __( '시간', 'acf-css-woocommerce-toolkit' ),
                'minutes'      => __( '분', 'acf-css-woocommerce-toolkit' ),
                'seconds'      => __( '초', 'acf-css-woocommerce-toolkit' ),
                'saleEnds'     => __( '할인 종료까지', 'acf-css-woocommerce-toolkit' ),
                'saleEnded'    => __( '할인이 종료되었습니다', 'acf-css-woocommerce-toolkit' ),
                'onlyLeft'     => __( '개 남음!', 'acf-css-woocommerce-toolkit' ),
                'hurry'        => __( '서두르세요!', 'acf-css-woocommerce-toolkit' ),
                'perUnit'      => __( '개당', 'acf-css-woocommerce-toolkit' ),
                'bulkDiscount' => __( '대량 구매 할인', 'acf-css-woocommerce-toolkit' ),
                'youSave'      => __( '절약', 'acf-css-woocommerce-toolkit' ),
                'lowestPrice'  => __( '최저가', 'acf-css-woocommerce-toolkit' ),
            ),
        ) );
    }

    /**
     * 가격 HTML 향상
     */
    public function enhance_price_html( $price_html, $product ) {
        if ( ! $product || is_admin() ) {
            return $price_html;
        }

        $enhancements = array();

        // 할인율 배지
        if ( $this->settings['show_savings_percentage'] && $product->is_on_sale() ) {
            $percentage = $this->calculate_discount_percentage( $product );
            if ( $percentage > 0 ) {
                $enhancements[] = sprintf(
                    '<span class="acf-discount-badge">-%d%%</span>',
                    $percentage
                );
            }
        }

        // 단위당 가격
        if ( $this->settings['show_price_per_unit'] ) {
            $unit_price = $this->get_unit_price_html( $product );
            if ( $unit_price ) {
                $enhancements[] = $unit_price;
            }
        }

        if ( ! empty( $enhancements ) ) {
            $price_html = '<div class="acf-enhanced-price">' .
                          implode( '', $enhancements ) .
                          $price_html .
                          '</div>';
        }

        return $price_html;
    }

    /**
     * 동적 가격 정보 표시 (상품 페이지)
     */
    public function display_dynamic_price_info() {
        global $product;

        if ( ! $product ) {
            return;
        }

        $output = '<div class="acf-dynamic-price-info" data-product-id="' . esc_attr( $product->get_id() ) . '">';

        // 세일 카운트다운
        if ( $this->settings['enable_countdown'] && $product->is_on_sale() ) {
            $countdown_html = $this->get_countdown_html( $product );
            if ( $countdown_html ) {
                $output .= $countdown_html;
            }
        }

        // 재고 긴급성
        if ( $this->settings['enable_stock_urgency'] ) {
            $urgency_html = $this->get_stock_urgency_html( $product );
            if ( $urgency_html ) {
                $output .= $urgency_html;
            }
        }

        // 대량 구매 가격표
        if ( $this->settings['enable_bulk_pricing'] ) {
            $bulk_html = $this->get_bulk_pricing_html( $product );
            if ( $bulk_html ) {
                $output .= $bulk_html;
            }
        }

        // 가격 히스토리
        if ( $this->settings['enable_price_history'] ) {
            $history_html = $this->get_price_history_html( $product );
            if ( $history_html ) {
                $output .= $history_html;
            }
        }

        $output .= '</div>';

        echo $output;
    }

    /**
     * 루프 가격 배지 표시
     */
    public function display_loop_price_badges() {
        global $product;

        if ( ! $product ) {
            return;
        }

        $badges = array();

        // 세일 종료 임박
        if ( $this->settings['enable_countdown'] && $product->is_on_sale() ) {
            $sale_end = $this->get_sale_end_date( $product );
            if ( $sale_end ) {
                $time_left = $sale_end - current_time( 'timestamp' );
                if ( $time_left > 0 && $time_left < 86400 ) { // 24시간 이내
                    $badges[] = '<span class="acf-badge acf-badge-urgent">' .
                                esc_html__( '오늘까지!', 'acf-css-woocommerce-toolkit' ) .
                                '</span>';
                }
            }
        }

        // 재고 부족
        if ( $this->settings['enable_stock_urgency'] && $product->managing_stock() ) {
            $stock = $product->get_stock_quantity();
            if ( $stock > 0 && $stock <= $this->settings['urgency_threshold'] ) {
                $badges[] = '<span class="acf-badge acf-badge-stock">' .
                            sprintf( esc_html__( '%d개 남음', 'acf-css-woocommerce-toolkit' ), $stock ) .
                            '</span>';
            }
        }

        // 대량 할인 가능
        if ( $this->settings['enable_bulk_pricing'] ) {
            $bulk_rules = $this->get_bulk_pricing_rules( $product->get_id() );
            if ( ! empty( $bulk_rules ) ) {
                $badges[] = '<span class="acf-badge acf-badge-bulk">' .
                            esc_html__( '대량할인', 'acf-css-woocommerce-toolkit' ) .
                            '</span>';
            }
        }

        if ( ! empty( $badges ) ) {
            echo '<div class="acf-price-badges">' . implode( '', $badges ) . '</div>';
        }
    }

    /**
     * 카운트다운 HTML 생성
     */
    private function get_countdown_html( $product ) {
        $sale_end = $this->get_sale_end_date( $product );

        if ( ! $sale_end ) {
            return '';
        }

        $time_left = $sale_end - current_time( 'timestamp' );

        if ( $time_left <= 0 ) {
            return '';
        }

        $style = $this->settings['countdown_style'];

        return sprintf(
            '<div class="acf-sale-countdown %s" data-end="%d">
                <div class="countdown-label">%s</div>
                <div class="countdown-timer">
                    <div class="countdown-unit"><span class="countdown-days">00</span><span class="unit-label">%s</span></div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-unit"><span class="countdown-hours">00</span><span class="unit-label">%s</span></div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-unit"><span class="countdown-minutes">00</span><span class="unit-label">%s</span></div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-unit"><span class="countdown-seconds">00</span><span class="unit-label">%s</span></div>
                </div>
            </div>',
            esc_attr( $style ),
            esc_attr( $sale_end ),
            esc_html__( '할인 종료까지', 'acf-css-woocommerce-toolkit' ),
            esc_html__( '일', 'acf-css-woocommerce-toolkit' ),
            esc_html__( '시', 'acf-css-woocommerce-toolkit' ),
            esc_html__( '분', 'acf-css-woocommerce-toolkit' ),
            esc_html__( '초', 'acf-css-woocommerce-toolkit' )
        );
    }

    /**
     * 재고 긴급성 HTML 생성
     */
    private function get_stock_urgency_html( $product ) {
        if ( ! $product->managing_stock() ) {
            return '';
        }

        $stock = $product->get_stock_quantity();
        $threshold = $this->settings['urgency_threshold'];

        if ( $stock <= 0 || $stock > $threshold ) {
            return '';
        }

        $urgency_level = $stock <= 2 ? 'critical' : ( $stock <= $threshold / 2 ? 'high' : 'medium' );

        return sprintf(
            '<div class="acf-stock-urgency %s">
                <span class="urgency-icon">🔥</span>
                <span class="urgency-text">%s <strong>%d%s</strong></span>
            </div>',
            esc_attr( $urgency_level ),
            esc_html__( '재고', 'acf-css-woocommerce-toolkit' ),
            $stock,
            esc_html__( '개 남음!', 'acf-css-woocommerce-toolkit' )
        );
    }

    /**
     * 대량 구매 가격표 HTML 생성
     */
    private function get_bulk_pricing_html( $product ) {
        $rules = $this->get_bulk_pricing_rules( $product->get_id() );

        if ( empty( $rules ) ) {
            return '';
        }

        $base_price = $product->is_on_sale() ? $product->get_sale_price() : $product->get_regular_price();

        $html = '<div class="acf-bulk-pricing">';
        $html .= '<div class="bulk-pricing-header">';
        $html .= '<span class="bulk-icon">📦</span>';
        $html .= '<span class="bulk-title">' . esc_html__( '대량 구매 할인', 'acf-css-woocommerce-toolkit' ) . '</span>';
        $html .= '</div>';
        $html .= '<table class="bulk-pricing-table">';
        $html .= '<thead><tr>';
        $html .= '<th>' . esc_html__( '수량', 'acf-css-woocommerce-toolkit' ) . '</th>';
        $html .= '<th>' . esc_html__( '단가', 'acf-css-woocommerce-toolkit' ) . '</th>';
        $html .= '<th>' . esc_html__( '절약', 'acf-css-woocommerce-toolkit' ) . '</th>';
        $html .= '</tr></thead>';
        $html .= '<tbody>';

        foreach ( $rules as $rule ) {
            $discounted_price = $base_price * ( 1 - $rule['discount'] / 100 );
            $savings = ( $base_price - $discounted_price ) * $rule['min_qty'];

            $html .= '<tr>';
            $html .= '<td>' . sprintf( esc_html__( '%d개 이상', 'acf-css-woocommerce-toolkit' ), $rule['min_qty'] ) . '</td>';
            $html .= '<td>' . wc_price( $discounted_price ) . ' <span class="discount-percent">(-' . $rule['discount'] . '%)</span></td>';
            $html .= '<td class="savings">' . wc_price( $savings ) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    /**
     * 가격 히스토리 HTML 생성
     */
    private function get_price_history_html( $product ) {
        $history = get_post_meta( $product->get_id(), '_price_history', true );

        if ( empty( $history ) || ! is_array( $history ) ) {
            return '';
        }

        // 최근 30일 최저가 찾기
        $thirty_days_ago = strtotime( '-30 days' );
        $lowest_price = PHP_INT_MAX;
        $lowest_date = '';

        foreach ( $history as $entry ) {
            if ( $entry['timestamp'] >= $thirty_days_ago && $entry['price'] < $lowest_price ) {
                $lowest_price = $entry['price'];
                $lowest_date = $entry['timestamp'];
            }
        }

        if ( $lowest_price === PHP_INT_MAX ) {
            return '';
        }

        $current_price = $product->get_price();

        // 현재 가격이 최저가인지 확인
        $is_lowest = $current_price <= $lowest_price;

        return sprintf(
            '<div class="acf-price-history %s">
                <span class="history-icon">📊</span>
                <span class="history-text">%s: %s</span>
                %s
            </div>',
            $is_lowest ? 'is-lowest' : '',
            esc_html__( '30일 최저가', 'acf-css-woocommerce-toolkit' ),
            wc_price( $lowest_price ),
            $is_lowest ? '<span class="lowest-badge">' . esc_html__( '지금이 최저가!', 'acf-css-woocommerce-toolkit' ) . '</span>' : ''
        );
    }

    /**
     * 단위당 가격 HTML 생성
     */
    private function get_unit_price_html( $product ) {
        $unit = get_post_meta( $product->get_id(), '_unit_measure', true );
        $unit_amount = get_post_meta( $product->get_id(), '_unit_amount', true );

        if ( empty( $unit ) || empty( $unit_amount ) || $unit_amount <= 0 ) {
            return '';
        }

        $price = $product->get_price();
        $unit_price = $price / $unit_amount;

        return sprintf(
            '<span class="acf-unit-price">(%s %s/%s)</span>',
            wc_price( $unit_price ),
            esc_html__( '당', 'acf-css-woocommerce-toolkit' ),
            esc_html( $unit )
        );
    }

    /**
     * 할인율 계산
     */
    private function calculate_discount_percentage( $product ) {
        $regular = (float) $product->get_regular_price();
        $sale = (float) $product->get_sale_price();

        if ( $regular <= 0 || $sale <= 0 ) {
            return 0;
        }

        return round( ( ( $regular - $sale ) / $regular ) * 100 );
    }

    /**
     * 세일 종료일 가져오기
     */
    private function get_sale_end_date( $product ) {
        $sale_to = $product->get_date_on_sale_to();

        if ( $sale_to ) {
            return $sale_to->getTimestamp();
        }

        // 커스텀 메타에서 가져오기
        $custom_end = get_post_meta( $product->get_id(), '_sale_countdown_end', true );

        if ( $custom_end ) {
            return strtotime( $custom_end );
        }

        return false;
    }

    /**
     * 대량 구매 규칙 가져오기
     */
    private function get_bulk_pricing_rules( $product_id ) {
        $rules = get_post_meta( $product_id, '_bulk_pricing_rules', true );

        if ( empty( $rules ) || ! is_array( $rules ) ) {
            return array();
        }

        // 수량 기준 정렬
        usort( $rules, function( $a, $b ) {
            return $a['min_qty'] - $b['min_qty'];
        } );

        return $rules;
    }

    /**
     * 사용자별 가격 가져오기
     */
    public function get_user_price( $product, $user_id = null ) {
        if ( ! $this->settings['enable_user_pricing'] ) {
            return $product->get_price();
        }

        if ( null === $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( ! $user_id ) {
            return $product->get_price();
        }

        $user = get_userdata( $user_id );
        $base_price = $product->get_price();

        // 역할 기반 할인
        $role_discounts = get_option( 'acf_wc_role_discounts', array() );

        foreach ( $user->roles as $role ) {
            if ( isset( $role_discounts[ $role ] ) && $role_discounts[ $role ] > 0 ) {
                $discount = $role_discounts[ $role ];
                $base_price = $base_price * ( 1 - $discount / 100 );
                break;
            }
        }

        return $base_price;
    }

    /**
     * 상품 메타 필드 추가
     */
    public function add_dynamic_price_fields() {
        global $post;

        echo '<div class="options_group acf-dynamic-price-fields">';

        // 세일 카운트다운 종료일
        woocommerce_wp_text_input( array(
            'id'          => '_sale_countdown_end',
            'label'       => __( '할인 카운트다운 종료', 'acf-css-woocommerce-toolkit' ),
            'placeholder' => 'YYYY-MM-DD HH:MM:SS',
            'desc_tip'    => true,
            'description' => __( '세일 종료일을 지정하면 카운트다운 타이머가 표시됩니다.', 'acf-css-woocommerce-toolkit' ),
            'type'        => 'datetime-local',
        ) );

        // 단위 측정
        woocommerce_wp_text_input( array(
            'id'          => '_unit_measure',
            'label'       => __( '단위 (예: kg, L, m)', 'acf-css-woocommerce-toolkit' ),
            'desc_tip'    => true,
            'description' => __( '단위당 가격 표시를 위한 단위입니다.', 'acf-css-woocommerce-toolkit' ),
        ) );

        woocommerce_wp_text_input( array(
            'id'          => '_unit_amount',
            'label'       => __( '단위 수량', 'acf-css-woocommerce-toolkit' ),
            'desc_tip'    => true,
            'description' => __( '상품에 포함된 단위 수량입니다.', 'acf-css-woocommerce-toolkit' ),
            'type'        => 'number',
            'custom_attributes' => array(
                'step' => '0.01',
                'min'  => '0',
            ),
        ) );

        echo '</div>';

        // 대량 구매 규칙 섹션
        $this->render_bulk_pricing_fields( $post->ID );
    }

    /**
     * 대량 구매 규칙 필드 렌더링
     */
    private function render_bulk_pricing_fields( $product_id ) {
        $rules = $this->get_bulk_pricing_rules( $product_id );

        echo '<div class="options_group acf-bulk-pricing-admin">';
        echo '<p class="form-field"><label>' . esc_html__( '대량 구매 할인 규칙', 'acf-css-woocommerce-toolkit' ) . '</label></p>';

        echo '<div id="acf-bulk-rules-container">';

        if ( ! empty( $rules ) ) {
            foreach ( $rules as $index => $rule ) {
                $this->render_bulk_rule_row( $index, $rule );
            }
        } else {
            $this->render_bulk_rule_row( 0, array( 'min_qty' => '', 'discount' => '' ) );
        }

        echo '</div>';

        echo '<p class="form-field">';
        echo '<button type="button" class="button acf-add-bulk-rule">' . esc_html__( '+ 규칙 추가', 'acf-css-woocommerce-toolkit' ) . '</button>';
        echo '</p>';
        echo '</div>';

        // 인라인 JS
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var ruleIndex = <?php echo count( $rules ); ?>;

            $('.acf-add-bulk-rule').on('click', function() {
                var html = '<div class="acf-bulk-rule-row">' +
                    '<input type="number" name="_bulk_pricing_rules[' + ruleIndex + '][min_qty]" placeholder="<?php esc_attr_e( '최소 수량', 'acf-css-woocommerce-toolkit' ); ?>" min="1" style="width: 100px;">' +
                    '<input type="number" name="_bulk_pricing_rules[' + ruleIndex + '][discount]" placeholder="<?php esc_attr_e( '할인율 %', 'acf-css-woocommerce-toolkit' ); ?>" min="0" max="100" step="0.1" style="width: 100px;">' +
                    '<button type="button" class="button acf-remove-bulk-rule">&times;</button>' +
                    '</div>';
                $('#acf-bulk-rules-container').append(html);
                ruleIndex++;
            });

            $(document).on('click', '.acf-remove-bulk-rule', function() {
                $(this).closest('.acf-bulk-rule-row').remove();
            });
        });
        </script>
        <style>
        .acf-bulk-rule-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        .acf-bulk-rule-row input {
            max-width: 120px;
        }
        .acf-remove-bulk-rule {
            color: #a00 !important;
        }
        </style>
        <?php
    }

    /**
     * 대량 규칙 행 렌더링
     */
    private function render_bulk_rule_row( $index, $rule ) {
        ?>
        <div class="acf-bulk-rule-row">
            <input type="number"
                   name="_bulk_pricing_rules[<?php echo esc_attr( $index ); ?>][min_qty]"
                   value="<?php echo esc_attr( $rule['min_qty'] ); ?>"
                   placeholder="<?php esc_attr_e( '최소 수량', 'acf-css-woocommerce-toolkit' ); ?>"
                   min="1"
                   style="width: 100px;">
            <input type="number"
                   name="_bulk_pricing_rules[<?php echo esc_attr( $index ); ?>][discount]"
                   value="<?php echo esc_attr( $rule['discount'] ); ?>"
                   placeholder="<?php esc_attr_e( '할인율 %', 'acf-css-woocommerce-toolkit' ); ?>"
                   min="0"
                   max="100"
                   step="0.1"
                   style="width: 100px;">
            <button type="button" class="button acf-remove-bulk-rule">&times;</button>
        </div>
        <?php
    }

    /**
     * 상품 메타 필드 저장
     */
    public function save_dynamic_price_fields( $post_id ) {
        // 세일 카운트다운
        if ( isset( $_POST['_sale_countdown_end'] ) ) {
            update_post_meta( $post_id, '_sale_countdown_end', sanitize_text_field( $_POST['_sale_countdown_end'] ) );
        }

        // 단위
        if ( isset( $_POST['_unit_measure'] ) ) {
            update_post_meta( $post_id, '_unit_measure', sanitize_text_field( $_POST['_unit_measure'] ) );
        }

        if ( isset( $_POST['_unit_amount'] ) ) {
            update_post_meta( $post_id, '_unit_amount', floatval( $_POST['_unit_amount'] ) );
        }

        // 대량 구매 규칙
        if ( isset( $_POST['_bulk_pricing_rules'] ) && is_array( $_POST['_bulk_pricing_rules'] ) ) {
            $rules = array();
            foreach ( $_POST['_bulk_pricing_rules'] as $rule ) {
                if ( ! empty( $rule['min_qty'] ) && ! empty( $rule['discount'] ) ) {
                    $rules[] = array(
                        'min_qty'  => absint( $rule['min_qty'] ),
                        'discount' => floatval( $rule['discount'] ),
                    );
                }
            }
            update_post_meta( $post_id, '_bulk_pricing_rules', $rules );
        }

        // 가격 히스토리 기록
        $this->record_price_history( $post_id );
    }

    /**
     * 가격 히스토리 기록
     */
    private function record_price_history( $product_id ) {
        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            return;
        }

        $history = get_post_meta( $product_id, '_price_history', true );

        if ( ! is_array( $history ) ) {
            $history = array();
        }

        $current_price = $product->get_price();
        $timestamp = current_time( 'timestamp' );

        // 같은 날짜에 이미 기록이 있으면 업데이트
        $today = date( 'Y-m-d', $timestamp );
        $found = false;

        foreach ( $history as &$entry ) {
            if ( date( 'Y-m-d', $entry['timestamp'] ) === $today ) {
                $entry['price'] = $current_price;
                $found = true;
                break;
            }
        }

        if ( ! $found ) {
            $history[] = array(
                'timestamp' => $timestamp,
                'price'     => $current_price,
            );
        }

        // 90일 이상 된 기록 삭제
        $ninety_days_ago = strtotime( '-90 days' );
        $history = array_filter( $history, function( $entry ) use ( $ninety_days_ago ) {
            return $entry['timestamp'] >= $ninety_days_ago;
        } );

        update_post_meta( $product_id, '_price_history', array_values( $history ) );
    }

    /**
     * AJAX: 동적 가격 가져오기
     */
    public function ajax_get_dynamic_price() {
        check_ajax_referer( 'acf_dynamic_price_nonce', 'nonce' );

        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
        $quantity = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;

        if ( ! $product_id ) {
            wp_send_json_error( array( 'message' => __( '상품 ID가 필요합니다.', 'acf-css-woocommerce-toolkit' ) ) );
        }

        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            wp_send_json_error( array( 'message' => __( '상품을 찾을 수 없습니다.', 'acf-css-woocommerce-toolkit' ) ) );
        }

        $base_price = $product->get_price();
        $final_price = $base_price;

        // 대량 구매 할인 적용
        if ( $quantity > 1 && $this->settings['enable_bulk_pricing'] ) {
            $rules = $this->get_bulk_pricing_rules( $product_id );

            foreach ( array_reverse( $rules ) as $rule ) {
                if ( $quantity >= $rule['min_qty'] ) {
                    $final_price = $base_price * ( 1 - $rule['discount'] / 100 );
                    break;
                }
            }
        }

        // 사용자별 가격 적용
        $final_price = $this->get_user_price( $product );

        wp_send_json_success( array(
            'base_price'       => $base_price,
            'final_price'      => $final_price,
            'total_price'      => $final_price * $quantity,
            'savings'          => ( $base_price - $final_price ) * $quantity,
            'formatted_price'  => wc_price( $final_price ),
            'formatted_total'  => wc_price( $final_price * $quantity ),
        ) );
    }

    /**
     * AJAX: 대량 구매 가격 정보 가져오기
     */
    public function ajax_get_bulk_pricing() {
        check_ajax_referer( 'acf_dynamic_price_nonce', 'nonce' );

        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

        if ( ! $product_id ) {
            wp_send_json_error( array( 'message' => __( '상품 ID가 필요합니다.', 'acf-css-woocommerce-toolkit' ) ) );
        }

        $rules = $this->get_bulk_pricing_rules( $product_id );

        wp_send_json_success( array( 'rules' => $rules ) );
    }

    /**
     * 설정 등록
     */
    public function register_settings() {
        register_setting( 'acf_wc_dynamic_price', 'acf_wc_dynamic_price_settings', array(
            'type'              => 'array',
            'sanitize_callback' => array( $this, 'sanitize_settings' ),
        ) );
    }

    /**
     * 설정 정제
     */
    public function sanitize_settings( $input ) {
        $sanitized = array();

        $sanitized['enable_countdown'] = ! empty( $input['enable_countdown'] );
        $sanitized['enable_stock_urgency'] = ! empty( $input['enable_stock_urgency'] );
        $sanitized['enable_bulk_pricing'] = ! empty( $input['enable_bulk_pricing'] );
        $sanitized['enable_user_pricing'] = ! empty( $input['enable_user_pricing'] );
        $sanitized['enable_price_history'] = ! empty( $input['enable_price_history'] );
        $sanitized['countdown_style'] = sanitize_text_field( $input['countdown_style'] ?? 'modern' );
        $sanitized['urgency_threshold'] = absint( $input['urgency_threshold'] ?? 5 );
        $sanitized['show_savings_percentage'] = ! empty( $input['show_savings_percentage'] );
        $sanitized['show_price_per_unit'] = ! empty( $input['show_price_per_unit'] );
        $sanitized['animate_price_changes'] = ! empty( $input['animate_price_changes'] );

        return $sanitized;
    }

    /**
     * REST API 라우트 등록
     */
    public function register_rest_routes() {
        register_rest_route( 'acf-wc/v1', '/dynamic-price/(?P<id>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'rest_get_dynamic_price' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'id' => array(
                    'required'          => true,
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param );
                    },
                ),
                'quantity' => array(
                    'default'           => 1,
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param ) && $param > 0;
                    },
                ),
            ),
        ) );
    }

    /**
     * REST: 동적 가격 가져오기
     */
    public function rest_get_dynamic_price( $request ) {
        $product_id = absint( $request['id'] );
        $quantity = absint( $request['quantity'] ?? 1 );

        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            return new WP_Error( 'product_not_found', __( '상품을 찾을 수 없습니다.', 'acf-css-woocommerce-toolkit' ), array( 'status' => 404 ) );
        }

        $base_price = $product->get_price();
        $final_price = $base_price;

        // 대량 구매 할인
        if ( $quantity > 1 && $this->settings['enable_bulk_pricing'] ) {
            $rules = $this->get_bulk_pricing_rules( $product_id );

            foreach ( array_reverse( $rules ) as $rule ) {
                if ( $quantity >= $rule['min_qty'] ) {
                    $final_price = $base_price * ( 1 - $rule['discount'] / 100 );
                    break;
                }
            }
        }

        return rest_ensure_response( array(
            'product_id'   => $product_id,
            'quantity'     => $quantity,
            'base_price'   => $base_price,
            'final_price'  => $final_price,
            'total_price'  => $final_price * $quantity,
            'savings'      => ( $base_price - $final_price ) * $quantity,
            'currency'     => get_woocommerce_currency(),
            'is_on_sale'   => $product->is_on_sale(),
            'sale_end'     => $this->get_sale_end_date( $product ),
        ) );
    }
}

// 인스턴스 초기화
ACF_WC_Dynamic_Price_Display::get_instance();
