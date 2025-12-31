<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Template Market
 * 
 * 스타일 템플릿 마켓: 사용자들이 만든 예쁜 설정을 서로 사고파는 생태계
 * 
 * @since v6.1.0
 */
class JJ_Template_Market {
    
    private static $instance = null;
    private $api_base_url = 'https://j-j-labs.com/wp-json/jj-neural-link/v1/templates';
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'wp_ajax_jj_template_market_list', array( $this, 'ajax_list_templates' ) );
        add_action( 'wp_ajax_jj_template_market_preview', array( $this, 'ajax_preview_template' ) );
        add_action( 'wp_ajax_jj_template_market_purchase', array( $this, 'ajax_purchase_template' ) );
        add_action( 'wp_ajax_jj_template_market_publish', array( $this, 'ajax_publish_template' ) );
    }
    
    /**
     * 템플릿 목록 조회
     */
    public function ajax_list_templates() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }
        
        $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : 'all';
        $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
        
        // API 호출 (향후 실제 서버 연동)
        // 현재는 데모 데이터 반환
        $templates = $this->get_demo_templates( $category, $search );
        
        wp_send_json_success( array( 'templates' => $templates ) );
    }
    
    /**
     * 템플릿 미리보기
     */
    public function ajax_preview_template( $template_id ) {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }
        
        $template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';
        
        if ( empty( $template_id ) ) {
            wp_send_json_error( array( 'message' => '템플릿 ID가 필요합니다.' ) );
        }
        
        // API 호출로 템플릿 데이터 가져오기
        $template_data = $this->fetch_template_data( $template_id );
        
        if ( $template_data ) {
            wp_send_json_success( array( 'template' => $template_data ) );
        } else {
            wp_send_json_error( array( 'message' => '템플릿을 찾을 수 없습니다.' ) );
        }
    }
    
    /**
     * 템플릿 구매
     */
    public function ajax_purchase_template() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }
        
        $template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';
        
        if ( empty( $template_id ) ) {
            wp_send_json_error( array( 'message' => '템플릿 ID가 필요합니다.' ) );
        }
        
        // 구매 처리 (실제 결제 시스템 연동 필요)
        // 현재는 무료 템플릿만 지원
        $template_data = $this->fetch_template_data( $template_id );
        
        if ( $template_data && isset( $template_data['price'] ) && $template_data['price'] == 0 ) {
            // 무료 템플릿: 즉시 적용
            $this->apply_template( $template_data );
            wp_send_json_success( array( 'message' => '템플릿이 적용되었습니다.' ) );
        } else {
            wp_send_json_error( array( 'message' => '유료 템플릿은 결제가 필요합니다. (Coming Soon)' ) );
        }
    }
    
    /**
     * 템플릿 판매 등록
     */
    public function ajax_publish_template() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }
        
        $title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
        $description = isset( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '';
        $price = isset( $_POST['price'] ) ? floatval( $_POST['price'] ) : 0;
        $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : 'general';
        
        if ( empty( $title ) ) {
            wp_send_json_error( array( 'message' => '템플릿 제목이 필요합니다.' ) );
        }
        
        // 현재 설정 데이터 수집
        $export_data = array(
            'version' => JJ_STYLE_GUIDE_VERSION,
            'timestamp' => current_time( 'mysql' ),
            'options' => get_option( 'jj_style_guide_options', array() ),
            'visual_options' => get_option( 'jj_style_guide_visual_options', array() ),
        );
        
        // API 호출로 템플릿 등록 (향후 실제 서버 연동)
        // 현재는 로컬에만 저장
        $template_id = $this->save_template_locally( $title, $description, $price, $category, $export_data );
        
        wp_send_json_success( array( 
            'message' => '템플릿이 등록되었습니다. (로컬 저장 - 실제 마켓 연동은 Coming Soon)',
            'template_id' => $template_id
        ) );
    }
    
    /**
     * 데모 템플릿 데이터 (실제 API 연동 전까지)
     */
    private function get_demo_templates( $category = 'all', $search = '' ) {
        $templates = array(
            array(
                'id' => 'tpl_modern_biz',
                'title' => 'Modern Business',
                'author' => 'Jay Labs',
                'image' => 'https://via.placeholder.com/280x160/2271b1/ffffff?text=Modern+Business',
                'desc' => '신뢰감을 주는 블루 톤의 비즈니스 테마',
                'price' => 0,
                'category' => 'business',
                'rating' => 4.8,
                'downloads' => 1234
            ),
            array(
                'id' => 'tpl_warm_cafe',
                'title' => 'Warm Cafe',
                'author' => 'Jenny Design',
                'image' => 'https://via.placeholder.com/280x160/e67e22/ffffff?text=Warm+Cafe',
                'desc' => '따뜻한 오렌지와 브라운 컬러의 카페 테마',
                'price' => 0,
                'category' => 'cafe',
                'rating' => 4.6,
                'downloads' => 892
            ),
            array(
                'id' => 'tpl_dark_tech',
                'title' => 'Dark Tech',
                'author' => 'JJ Partners',
                'image' => 'https://via.placeholder.com/280x160/333333/ffffff?text=Dark+Tech',
                'desc' => '개발자 블로그를 위한 다크 모드 테마',
                'price' => 0,
                'category' => 'tech',
                'rating' => 4.9,
                'downloads' => 2156
            ),
        );
        
        // 카테고리 필터링
        if ( $category !== 'all' ) {
            $templates = array_filter( $templates, function( $tpl ) use ( $category ) {
                return $tpl['category'] === $category;
            } );
        }
        
        // 검색 필터링
        if ( ! empty( $search ) ) {
            $search_lower = strtolower( $search );
            $templates = array_filter( $templates, function( $tpl ) use ( $search_lower ) {
                return strpos( strtolower( $tpl['title'] ), $search_lower ) !== false ||
                       strpos( strtolower( $tpl['desc'] ), $search_lower ) !== false;
            } );
        }
        
        return array_values( $templates );
    }
    
    /**
     * 템플릿 데이터 가져오기
     */
    private function fetch_template_data( $template_id ) {
        // API 호출 (향후 실제 서버 연동)
        // 현재는 데모 데이터 반환
        $demo_templates = $this->get_demo_templates();
        foreach ( $demo_templates as $tpl ) {
            if ( $tpl['id'] === $template_id ) {
                // 실제 템플릿 데이터는 API에서 가져와야 함
                return array_merge( $tpl, array(
                    'data' => array(
                        'options' => get_option( 'jj_style_guide_options', array() ),
                        'visual_options' => get_option( 'jj_style_guide_visual_options', array() ),
                    )
                ) );
            }
        }
        return null;
    }
    
    /**
     * 템플릿 적용
     */
    private function apply_template( $template_data ) {
        if ( ! isset( $template_data['data'] ) ) {
            return false;
        }
        
        $data = $template_data['data'];
        
        if ( isset( $data['options'] ) ) {
            update_option( 'jj_style_guide_options', $data['options'] );
        }
        if ( isset( $data['visual_options'] ) ) {
            update_option( 'jj_style_guide_visual_options', $data['visual_options'] );
        }
        
        // CSS 캐시 플러시
        if ( class_exists( 'JJ_CSS_Cache' ) && method_exists( 'JJ_CSS_Cache', 'instance' ) ) {
            $cache = JJ_CSS_Cache::instance();
            if ( $cache && method_exists( $cache, 'flush' ) ) {
                $cache->flush();
            }
        }
        
        return true;
    }
    
    /**
     * 로컬에 템플릿 저장 (실제 마켓 연동 전까지)
     */
    private function save_template_locally( $title, $description, $price, $category, $data ) {
        $templates = get_option( 'jj_template_market_local', array() );
        $template_id = 'local_' . time() . '_' . wp_generate_password( 6, false );
        
        $templates[ $template_id ] = array(
            'title' => $title,
            'description' => $description,
            'price' => $price,
            'category' => $category,
            'data' => $data,
            'created' => current_time( 'mysql' ),
        );
        
        update_option( 'jj_template_market_local', $templates );
        
        return $template_id;
    }
}

