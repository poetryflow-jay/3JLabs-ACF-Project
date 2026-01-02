<?php
/**
 * ACF Code Snippets Box - Advanced Condition Builder
 *
 * WPCODEBOX2 스타일의 고급 조건 빌더
 * 스니펫 실행 조건을 세밀하게 제어합니다.
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Condition Builder 클래스
 */
class ACF_CSB_Condition_Builder {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * WordPress 컨텍스트
     */
    private $context = null;

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
        add_action( 'wp_ajax_acf_csb_get_condition_data', array( $this, 'ajax_get_condition_data' ) );
        add_action( 'wp_ajax_acf_csb_validate_condition', array( $this, 'ajax_validate_condition' ) );
    }

    /**
     * 사용 가능한 조건 유형 목록
     * 
     * @return array
     */
    public static function get_condition_types() {
        $free_conditions = array(
            'location' => array(
                'name'        => __( '위치', 'acf-code-snippets-box' ),
                'description' => __( '프론트엔드, 백엔드, 로그인 페이지', 'acf-code-snippets-box' ),
                'pro_only'    => false,
                'category'    => 'basic',
            ),
            'post_type' => array(
                'name'        => __( '포스트 타입', 'acf-code-snippets-box' ),
                'description' => __( 'page, post, product 등', 'acf-code-snippets-box' ),
                'pro_only'    => false,
                'category'    => 'basic',
            ),
            'user_logged_in' => array(
                'name'        => __( '로그인 상태', 'acf-code-snippets-box' ),
                'description' => __( '로그인 여부 확인', 'acf-code-snippets-box' ),
                'pro_only'    => false,
                'category'    => 'basic',
            ),
            'device_type' => array(
                'name'        => __( '기기 유형', 'acf-code-snippets-box' ),
                'description' => __( '모바일, 데스크톱', 'acf-code-snippets-box' ),
                'pro_only'    => false,
                'category'    => 'basic',
            ),
        );

        $pro_basic_conditions = array(
            'specific_post' => array(
                'name'        => __( '특정 포스트/페이지', 'acf-code-snippets-box' ),
                'description' => __( '특정 ID의 포스트에서만', 'acf-code-snippets-box' ),
                'pro_only'    => true,
                'min_tier'    => 'basic',
                'category'    => 'content',
            ),
            'taxonomy' => array(
                'name'        => __( '분류(카테고리/태그)', 'acf-code-snippets-box' ),
                'description' => __( '특정 분류에 속하는 콘텐츠', 'acf-code-snippets-box' ),
                'pro_only'    => true,
                'min_tier'    => 'basic',
                'category'    => 'content',
            ),
            'url_pattern' => array(
                'name'        => __( 'URL 패턴', 'acf-code-snippets-box' ),
                'description' => __( 'URL에 특정 문자열 포함', 'acf-code-snippets-box' ),
                'pro_only'    => true,
                'min_tier'    => 'basic',
                'category'    => 'url',
            ),
            'user_role' => array(
                'name'        => __( '사용자 역할', 'acf-code-snippets-box' ),
                'description' => __( '관리자, 편집자, 구독자 등', 'acf-code-snippets-box' ),
                'pro_only'    => true,
                'min_tier'    => 'basic',
                'category'    => 'user',
            ),
        );

        $pro_premium_conditions = array(
            'time_range' => array(
                'name'        => __( '시간 범위', 'acf-code-snippets-box' ),
                'description' => __( '특정 시간대에만 실행', 'acf-code-snippets-box' ),
                'pro_only'    => true,
                'min_tier'    => 'premium',
                'category'    => 'schedule',
            ),
            'day_of_week' => array(
                'name'        => __( '요일', 'acf-code-snippets-box' ),
                'description' => __( '특정 요일에만 실행', 'acf-code-snippets-box' ),
                'pro_only'    => true,
                'min_tier'    => 'premium',
                'category'    => 'schedule',
            ),
            'custom_php' => array(
                'name'        => __( '커스텀 PHP 조건', 'acf-code-snippets-box' ),
                'description' => __( 'PHP 코드로 조건 정의', 'acf-code-snippets-box' ),
                'pro_only'    => true,
                'min_tier'    => 'premium',
                'category'    => 'advanced',
            ),
            'specific_user' => array(
                'name'        => __( '특정 사용자', 'acf-code-snippets-box' ),
                'description' => __( '특정 사용자에게만', 'acf-code-snippets-box' ),
                'pro_only'    => true,
                'min_tier'    => 'premium',
                'category'    => 'user',
            ),
            'post_parent' => array(
                'name'        => __( '부모 포스트', 'acf-code-snippets-box' ),
                'description' => __( '특정 부모의 자식 포스트', 'acf-code-snippets-box' ),
                'pro_only'    => true,
                'min_tier'    => 'premium',
                'category'    => 'content',
            ),
        );

        $pro_unlimited_conditions = array(
            'woocommerce_cart' => array(
                'name'        => __( 'WooCommerce 장바구니', 'acf-code-snippets-box' ),
                'description' => __( '장바구니 내용 기반', 'acf-code-snippets-box' ),
                'pro_only'    => true,
                'min_tier'    => 'unlimited',
                'category'    => 'woocommerce',
            ),
            'woocommerce_customer' => array(
                'name'        => __( 'WooCommerce 고객', 'acf-code-snippets-box' ),
                'description' => __( '고객 구매 이력 기반', 'acf-code-snippets-box' ),
                'pro_only'    => true,
                'min_tier'    => 'unlimited',
                'category'    => 'woocommerce',
            ),
        );

        return array_merge( 
            $free_conditions, 
            $pro_basic_conditions, 
            $pro_premium_conditions, 
            $pro_unlimited_conditions 
        );
    }

    /**
     * 조건 카테고리 목록
     *
     * @return array
     */
    public static function get_condition_categories() {
        return array(
            'basic'       => __( '기본', 'acf-code-snippets-box' ),
            'content'     => __( '콘텐츠', 'acf-code-snippets-box' ),
            'url'         => __( 'URL', 'acf-code-snippets-box' ),
            'user'        => __( '사용자', 'acf-code-snippets-box' ),
            'schedule'    => __( '일정', 'acf-code-snippets-box' ),
            'advanced'    => __( '고급', 'acf-code-snippets-box' ),
            'woocommerce' => __( 'WooCommerce', 'acf-code-snippets-box' ),
        );
    }

    /**
     * 조건 평가
     *
     * @param array $conditions 조건 그룹 배열
     * @return bool
     */
    public function evaluate_conditions( $conditions ) {
        if ( empty( $conditions ) ) {
            return true; // 조건이 없으면 항상 실행
        }

        // 조건 그룹은 OR 논리로 연결
        foreach ( $conditions as $condition_group ) {
            if ( $this->evaluate_condition_group( $condition_group ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * 조건 그룹 평가 (그룹 내 조건들은 AND 논리)
     *
     * @param array $condition_group 조건 배열
     * @return bool
     */
    private function evaluate_condition_group( $condition_group ) {
        if ( empty( $condition_group['conditions'] ) ) {
            return true;
        }

        foreach ( $condition_group['conditions'] as $condition ) {
            if ( ! $this->evaluate_single_condition( $condition ) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * 단일 조건 평가
     *
     * @param array $condition 조건 데이터
     * @return bool
     */
    private function evaluate_single_condition( $condition ) {
        $type   = isset( $condition['type'] ) ? $condition['type'] : '';
        $verb   = isset( $condition['verb'] ) ? $condition['verb'] : 'is';
        $value  = isset( $condition['value'] ) ? $condition['value'] : '';
        $value2 = isset( $condition['value2'] ) ? $condition['value2'] : '';

        $result = false;

        switch ( $type ) {
            case 'location':
                $result = $this->check_location( $value );
                break;

            case 'post_type':
                $result = $this->check_post_type( $value );
                break;

            case 'user_logged_in':
                $result = $this->check_user_logged_in( $value );
                break;

            case 'device_type':
                $result = $this->check_device_type( $value );
                break;

            case 'specific_post':
                $result = $this->check_specific_post( $value );
                break;

            case 'taxonomy':
                $result = $this->check_taxonomy( $value, $value2 );
                break;

            case 'url_pattern':
                $result = $this->check_url_pattern( $value );
                break;

            case 'user_role':
                $result = $this->check_user_role( $value );
                break;

            case 'time_range':
                $result = $this->check_time_range( $value, $value2 );
                break;

            case 'day_of_week':
                $result = $this->check_day_of_week( $value );
                break;

            case 'custom_php':
                $result = $this->check_custom_php( $value );
                break;

            default:
                $result = true;
        }

        // verb가 'is_not'이면 결과 반전
        if ( $verb === 'is_not' ) {
            $result = ! $result;
        }

        return $result;
    }

    // ========================================
    // 조건 체크 메서드들
    // ========================================

    /**
     * 위치 조건 체크
     */
    private function check_location( $location ) {
        switch ( $location ) {
            case 'frontend':
                return ! is_admin() && ! wp_doing_ajax();
            case 'backend':
                return is_admin() && ! wp_doing_ajax();
            case 'login':
                global $pagenow;
                return $pagenow === 'wp-login.php';
            case 'ajax':
                return wp_doing_ajax();
            default:
                return true;
        }
    }

    /**
     * 포스트 타입 조건 체크
     */
    private function check_post_type( $post_type ) {
        if ( is_admin() ) {
            global $post;
            return isset( $post ) && $post->post_type === $post_type;
        }
        return get_post_type() === $post_type;
    }

    /**
     * 로그인 상태 조건 체크
     */
    private function check_user_logged_in( $value ) {
        return $value === 'logged_in' ? is_user_logged_in() : ! is_user_logged_in();
    }

    /**
     * 기기 유형 조건 체크
     */
    private function check_device_type( $device ) {
        if ( ! function_exists( 'wp_is_mobile' ) ) {
            return true;
        }
        return $device === 'mobile' ? wp_is_mobile() : ! wp_is_mobile();
    }

    /**
     * 특정 포스트 조건 체크
     */
    private function check_specific_post( $post_ids ) {
        $current_id = get_the_ID();
        if ( ! $current_id ) {
            return false;
        }
        
        $ids = array_map( 'intval', explode( ',', $post_ids ) );
        return in_array( $current_id, $ids, true );
    }

    /**
     * 분류 조건 체크
     */
    private function check_taxonomy( $taxonomy, $term_id ) {
        $current_id = get_the_ID();
        if ( ! $current_id ) {
            return false;
        }
        return has_term( intval( $term_id ), $taxonomy, $current_id );
    }

    /**
     * URL 패턴 조건 체크
     */
    private function check_url_pattern( $pattern ) {
        $current_url = home_url( add_query_arg( array() ) );
        return strpos( $current_url, $pattern ) !== false;
    }

    /**
     * 사용자 역할 조건 체크
     */
    private function check_user_role( $role ) {
        if ( ! is_user_logged_in() ) {
            return false;
        }
        $user = wp_get_current_user();
        return in_array( $role, $user->roles, true );
    }

    /**
     * 시간 범위 조건 체크
     */
    private function check_time_range( $start_time, $end_time ) {
        $current_time = current_time( 'H:i' );
        return $current_time >= $start_time && $current_time <= $end_time;
    }

    /**
     * 요일 조건 체크
     */
    private function check_day_of_week( $days ) {
        $current_day = strtolower( current_time( 'l' ) );
        $days_array = array_map( 'trim', explode( ',', strtolower( $days ) ) );
        return in_array( $current_day, $days_array, true );
    }

    /**
     * 커스텀 PHP 조건 체크
     */
    private function check_custom_php( $code ) {
        if ( empty( $code ) ) {
            return true;
        }

        // 보안: eval 사용 전 코드 검증
        if ( ! current_user_can( 'manage_options' ) ) {
            return false;
        }

        try {
            // phpcs:ignore WordPress.PHP.RestrictedPHPFunctions.eval_eval
            return (bool) eval( 'return ' . $code . ';' );
        } catch ( \Throwable $e ) {
            return false;
        }
    }

    /**
     * AJAX: 조건 데이터 가져오기
     */
    public function ajax_get_condition_data() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-code-snippets-box' ) );
        }

        $condition_type = isset( $_POST['condition_type'] ) ? sanitize_text_field( $_POST['condition_type'] ) : '';

        $data = array();

        switch ( $condition_type ) {
            case 'post_type':
                $post_types = get_post_types( array( 'public' => true ), 'objects' );
                foreach ( $post_types as $pt ) {
                    $data[] = array( 'value' => $pt->name, 'label' => $pt->labels->singular_name );
                }
                break;

            case 'taxonomy':
                $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
                foreach ( $taxonomies as $tax ) {
                    $data[] = array( 'value' => $tax->name, 'label' => $tax->labels->singular_name );
                }
                break;

            case 'user_role':
                global $wp_roles;
                foreach ( $wp_roles->role_names as $role => $name ) {
                    $data[] = array( 'value' => $role, 'label' => $name );
                }
                break;

            case 'day_of_week':
                $data = array(
                    array( 'value' => 'monday', 'label' => __( '월요일', 'acf-code-snippets-box' ) ),
                    array( 'value' => 'tuesday', 'label' => __( '화요일', 'acf-code-snippets-box' ) ),
                    array( 'value' => 'wednesday', 'label' => __( '수요일', 'acf-code-snippets-box' ) ),
                    array( 'value' => 'thursday', 'label' => __( '목요일', 'acf-code-snippets-box' ) ),
                    array( 'value' => 'friday', 'label' => __( '금요일', 'acf-code-snippets-box' ) ),
                    array( 'value' => 'saturday', 'label' => __( '토요일', 'acf-code-snippets-box' ) ),
                    array( 'value' => 'sunday', 'label' => __( '일요일', 'acf-code-snippets-box' ) ),
                );
                break;
        }

        wp_send_json_success( $data );
    }

    /**
     * AJAX: 조건 유효성 검증
     */
    public function ajax_validate_condition() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-code-snippets-box' ) );
        }

        $conditions = isset( $_POST['conditions'] ) ? json_decode( stripslashes( $_POST['conditions'] ), true ) : array();

        if ( empty( $conditions ) ) {
            wp_send_json_success( array( 'valid' => true, 'message' => __( '조건이 없습니다.', 'acf-code-snippets-box' ) ) );
        }

        $result = $this->evaluate_conditions( $conditions );

        wp_send_json_success( array(
            'valid'   => true,
            'result'  => $result,
            'message' => $result 
                ? __( '현재 상태에서 조건이 충족됩니다.', 'acf-code-snippets-box' )
                : __( '현재 상태에서 조건이 충족되지 않습니다.', 'acf-code-snippets-box' ),
        ) );
    }
}

// 인스턴스 초기화
ACF_CSB_Condition_Builder::instance()->init();
