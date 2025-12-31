<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Neural Link Cloud API
 * 
 * 스타일 설정 데이터를 클라우드(서버)에 저장하고 불러오는 API입니다.
 * 
 * @since v3.1.0
 */
class JJ_Neural_Link_Cloud_API {

    private static $instance = null;
    private $namespace = 'jj-neural-link/v1';
    private $post_type = 'jj_cloud_item';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'register_cloud_post_type' ) );
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * 클라우드 데이터 저장용 CPT 등록
     */
    public function register_cloud_post_type() {
        register_post_type( $this->post_type, array(
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true, // 관리자가 볼 수 있게 함 (디버깅용)
            'show_in_menu' => 'edit.php?post_type=jj_license', // 라이센스 메뉴 하위에
            'label' => 'Cloud Items',
            'supports' => array( 'title', 'custom-fields' ),
            'capabilities' => array(
                'create_posts' => 'do_not_allow', // UI에서 생성 불가
            ),
            'map_meta_cap' => true,
        ) );
    }

    public function register_routes() {
        // 저장 (Export to Cloud)
        register_rest_route( $this->namespace, '/cloud/store', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'handle_store_request' ),
            'permission_callback' => '__return_true', // 라이센스 키 검증 로직 추가 가능
        ) );

        // 불러오기 (Import from Cloud)
        register_rest_route( $this->namespace, '/cloud/fetch', array(
            'methods'             => 'POST', // 보안상 POST 권장
            'callback'            => array( $this, 'handle_fetch_request' ),
            'permission_callback' => '__return_true',
        ) );
    }

    /**
     * 클라우드 저장 요청 처리
     */
    public function handle_store_request( $request ) {
        $params = $request->get_params();
        
        $license_key = isset( $params['license_key'] ) ? sanitize_text_field( $params['license_key'] ) : '';
        $data = isset( $params['data'] ) ? $params['data'] : ''; // JSON string or Array
        
        if ( empty( $data ) ) {
            return new WP_Error( 'no_data', '저장할 데이터가 없습니다.', array( 'status' => 400 ) );
        }

        // TODO: 라이센스 키 검증 (생략 - 누구에게나 열어둘지, 유료 회원만 할지 결정)
        
        // 공유 코드 생성 (랜덤 6자리 문자열)
        $share_code = strtoupper( wp_generate_password( 6, false ) );
        
        // 포스트 생성
        $post_id = wp_insert_post( array(
            'post_type'   => $this->post_type,
            'post_title'  => 'Cloud Item - ' . $share_code,
            'post_status' => 'publish',
            'post_content' => '', // 내용은 사용 안함
        ) );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        // 데이터 저장 (Meta Field)
        // JSON 문자열로 저장
        $json_data = is_array( $data ) ? wp_json_encode( $data ) : $data;
        update_post_meta( $post_id, '_jj_cloud_data', $json_data );
        update_post_meta( $post_id, '_jj_share_code', $share_code );
        update_post_meta( $post_id, '_jj_license_key', $license_key );

        return array(
            'success'    => true,
            'share_code' => $share_code,
            'message'    => '클라우드에 안전하게 저장되었습니다.',
        );
    }

    /**
     * 클라우드 데이터 조회 요청 처리
     */
    public function handle_fetch_request( $request ) {
        $params = $request->get_params();
        $share_code = isset( $params['share_code'] ) ? sanitize_text_field( $params['share_code'] ) : '';

        if ( empty( $share_code ) ) {
            return new WP_Error( 'no_code', '공유 코드가 필요합니다.', array( 'status' => 400 ) );
        }

        // 공유 코드로 포스트 찾기
        $args = array(
            'post_type'  => $this->post_type,
            'meta_key'   => '_jj_share_code',
            'meta_value' => $share_code,
            'posts_per_page' => 1,
            'post_status' => 'publish',
        );

        $query = new WP_Query( $args );

        if ( ! $query->have_posts() ) {
            return new WP_Error( 'not_found', '해당 코드로 저장된 데이터를 찾을 수 없습니다.', array( 'status' => 404 ) );
        }

        $post_id = $query->posts[0]->ID;
        $json_data = get_post_meta( $post_id, '_jj_cloud_data', true );
        
        // 데이터 파싱
        $data = json_decode( $json_data, true );

        return array(
            'success' => true,
            'data'    => $data,
            'message' => '데이터를 성공적으로 불러왔습니다.',
        );
    }
}

