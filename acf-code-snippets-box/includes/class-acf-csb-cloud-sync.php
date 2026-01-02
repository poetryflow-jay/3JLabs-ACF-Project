<?php
/**
 * ACF Code Snippets Box - Cloud Sync System
 *
 * 클라우드 동기화 시스템 - 여러 사이트 간 스니펫 동기화
 * Pro Premium 이상에서 사용 가능
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Cloud Sync 클래스
 */
class ACF_CSB_Cloud_Sync {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * API 엔드포인트
     */
    const API_BASE = 'https://api.3j-labs.com/v1/cloud-sync';

    /**
     * 동기화 상태
     */
    const STATUS_SYNCED    = 'synced';
    const STATUS_PENDING   = 'pending';
    const STATUS_CONFLICT  = 'conflict';
    const STATUS_ERROR     = 'error';

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
        // Pro Premium 이상에서만 사용 가능
        if ( ! ACF_CSB_License::has_access( 'premium' ) ) {
            return;
        }

        // 설정 페이지에 클라우드 섹션 추가
        add_action( 'acf_csb_settings_sections', array( $this, 'add_cloud_settings' ) );

        // AJAX 핸들러
        add_action( 'wp_ajax_acf_csb_cloud_connect', array( $this, 'ajax_connect' ) );
        add_action( 'wp_ajax_acf_csb_cloud_disconnect', array( $this, 'ajax_disconnect' ) );
        add_action( 'wp_ajax_acf_csb_cloud_sync', array( $this, 'ajax_sync' ) );
        add_action( 'wp_ajax_acf_csb_cloud_push', array( $this, 'ajax_push' ) );
        add_action( 'wp_ajax_acf_csb_cloud_pull', array( $this, 'ajax_pull' ) );

        // 자동 동기화 (스니펫 저장 시)
        if ( $this->is_auto_sync_enabled() ) {
            add_action( 'save_post_acf_code_snippet', array( $this, 'auto_sync_snippet' ), 20, 2 );
            add_action( 'before_delete_post', array( $this, 'auto_delete_cloud_snippet' ) );
        }

        // 크론 작업 (정기 동기화)
        add_action( 'acf_csb_cloud_sync_cron', array( $this, 'cron_sync' ) );
        
        if ( ! wp_next_scheduled( 'acf_csb_cloud_sync_cron' ) && $this->is_connected() ) {
            wp_schedule_event( time(), 'hourly', 'acf_csb_cloud_sync_cron' );
        }
    }

    /**
     * 클라우드 연결 상태 확인
     */
    public function is_connected() {
        $token = get_option( 'acf_csb_cloud_token' );
        return ! empty( $token );
    }

    /**
     * 자동 동기화 활성화 여부
     */
    private function is_auto_sync_enabled() {
        return get_option( 'acf_csb_cloud_auto_sync', 'yes' ) === 'yes';
    }

    /**
     * 클라우드 설정 섹션 추가
     */
    public function add_cloud_settings() {
        $is_connected = $this->is_connected();
        $cloud_info = $this->get_cloud_info();
        ?>
        <div class="acf-csb-cloud-settings">
            <h2>
                <span class="dashicons dashicons-cloud"></span>
                <?php esc_html_e( '클라우드 동기화', 'acf-code-snippets-box' ); ?>
                <span class="acf-csb-pro-badge">Premium</span>
            </h2>

            <p class="description">
                <?php esc_html_e( '스니펫을 클라우드에 저장하고 여러 워드프레스 사이트 간에 동기화합니다.', 'acf-code-snippets-box' ); ?>
            </p>

            <?php if ( $is_connected ) : ?>
                <!-- 연결된 상태 -->
                <div class="acf-csb-cloud-connected">
                    <div class="cloud-status">
                        <span class="status-icon">✅</span>
                        <div class="status-info">
                            <strong><?php esc_html_e( '클라우드 연결됨', 'acf-code-snippets-box' ); ?></strong>
                            <span class="account-email"><?php echo esc_html( $cloud_info['email'] ?? '' ); ?></span>
                        </div>
                    </div>

                    <div class="cloud-stats">
                        <div class="stat-item">
                            <span class="stat-value"><?php echo esc_html( $cloud_info['snippet_count'] ?? 0 ); ?></span>
                            <span class="stat-label"><?php esc_html_e( '클라우드 스니펫', 'acf-code-snippets-box' ); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo esc_html( $cloud_info['site_count'] ?? 1 ); ?></span>
                            <span class="stat-label"><?php esc_html_e( '연결된 사이트', 'acf-code-snippets-box' ); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo esc_html( $cloud_info['last_sync'] ?? '-' ); ?></span>
                            <span class="stat-label"><?php esc_html_e( '마지막 동기화', 'acf-code-snippets-box' ); ?></span>
                        </div>
                    </div>

                    <div class="cloud-actions">
                        <button type="button" class="button button-primary" id="cloud-sync-now">
                            <span class="dashicons dashicons-update"></span>
                            <?php esc_html_e( '지금 동기화', 'acf-code-snippets-box' ); ?>
                        </button>
                        <button type="button" class="button" id="cloud-push-all">
                            <span class="dashicons dashicons-upload"></span>
                            <?php esc_html_e( '모두 업로드', 'acf-code-snippets-box' ); ?>
                        </button>
                        <button type="button" class="button" id="cloud-pull-all">
                            <span class="dashicons dashicons-download"></span>
                            <?php esc_html_e( '모두 다운로드', 'acf-code-snippets-box' ); ?>
                        </button>
                        <button type="button" class="button cloud-disconnect" id="cloud-disconnect">
                            <?php esc_html_e( '연결 해제', 'acf-code-snippets-box' ); ?>
                        </button>
                    </div>

                    <div class="cloud-options">
                        <h3><?php esc_html_e( '동기화 옵션', 'acf-code-snippets-box' ); ?></h3>
                        
                        <label>
                            <input type="checkbox" name="acf_csb_cloud_auto_sync" value="yes" 
                                   <?php checked( $this->is_auto_sync_enabled() ); ?>>
                            <?php esc_html_e( '스니펫 저장 시 자동 동기화', 'acf-code-snippets-box' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="acf_csb_cloud_sync_conditions" value="yes"
                                   <?php checked( get_option( 'acf_csb_cloud_sync_conditions', 'yes' ), 'yes' ); ?>>
                            <?php esc_html_e( '조건 설정도 함께 동기화', 'acf-code-snippets-box' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="acf_csb_cloud_sync_disabled" value="yes"
                                   <?php checked( get_option( 'acf_csb_cloud_sync_disabled', 'no' ), 'yes' ); ?>>
                            <?php esc_html_e( '비활성화된 스니펫도 동기화', 'acf-code-snippets-box' ); ?>
                        </label>
                    </div>
                </div>

            <?php else : ?>
                <!-- 연결 안 된 상태 -->
                <div class="acf-csb-cloud-disconnected">
                    <div class="cloud-intro">
                        <div class="intro-icon">☁️</div>
                        <h3><?php esc_html_e( '클라우드에 연결하세요', 'acf-code-snippets-box' ); ?></h3>
                        <p><?php esc_html_e( '3J Labs 계정에 연결하면 스니펫을 클라우드에 백업하고 여러 사이트에서 공유할 수 있습니다.', 'acf-code-snippets-box' ); ?></p>
                        
                        <ul class="cloud-benefits">
                            <li>✓ <?php esc_html_e( '안전한 클라우드 백업', 'acf-code-snippets-box' ); ?></li>
                            <li>✓ <?php esc_html_e( '여러 사이트 간 동기화', 'acf-code-snippets-box' ); ?></li>
                            <li>✓ <?php esc_html_e( '버전 히스토리 관리', 'acf-code-snippets-box' ); ?></li>
                            <li>✓ <?php esc_html_e( '팀 공유 (Unlimited)', 'acf-code-snippets-box' ); ?></li>
                        </ul>
                    </div>

                    <form id="cloud-connect-form" class="cloud-connect-form">
                        <?php wp_nonce_field( 'acf_csb_cloud_connect', 'cloud_nonce' ); ?>
                        
                        <div class="form-row">
                            <label for="cloud-api-key"><?php esc_html_e( 'API 키', 'acf-code-snippets-box' ); ?></label>
                            <input type="text" id="cloud-api-key" name="api_key" 
                                   placeholder="<?php esc_attr_e( '3J Labs 계정의 API 키를 입력하세요', 'acf-code-snippets-box' ); ?>">
                            <p class="description">
                                <?php printf(
                                    esc_html__( '%s에서 API 키를 발급받을 수 있습니다.', 'acf-code-snippets-box' ),
                                    '<a href="https://3j-labs.com/account/api" target="_blank">3J Labs 계정</a>'
                                ); ?>
                            </p>
                        </div>

                        <button type="submit" class="button button-primary">
                            <span class="dashicons dashicons-cloud"></span>
                            <?php esc_html_e( '클라우드 연결', 'acf-code-snippets-box' ); ?>
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <style>
        .acf-csb-cloud-settings {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 25px;
            margin-top: 20px;
        }
        .acf-csb-cloud-settings h2 {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 10px;
        }
        .cloud-status {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            background: #d4edda;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .status-icon {
            font-size: 24px;
        }
        .status-info {
            display: flex;
            flex-direction: column;
        }
        .account-email {
            color: #666;
            font-size: 13px;
        }
        .cloud-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-item {
            text-align: center;
            padding: 15px 25px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        .stat-value {
            display: block;
            font-size: 24px;
            font-weight: 700;
            color: #0073aa;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
        }
        .cloud-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .cloud-disconnect {
            margin-left: auto !important;
            color: #d63638 !important;
        }
        .cloud-options {
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .cloud-options label {
            display: block;
            margin-bottom: 10px;
        }
        .cloud-intro {
            text-align: center;
            padding: 30px;
        }
        .intro-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .cloud-benefits {
            list-style: none;
            padding: 0;
            margin: 20px 0;
            text-align: left;
            display: inline-block;
        }
        .cloud-benefits li {
            padding: 5px 0;
        }
        .cloud-connect-form {
            max-width: 400px;
            margin: 0 auto;
        }
        .cloud-connect-form .form-row {
            margin-bottom: 15px;
        }
        .cloud-connect-form label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .cloud-connect-form input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        </style>
        <?php
    }

    /**
     * 클라우드 정보 가져오기
     */
    private function get_cloud_info() {
        if ( ! $this->is_connected() ) {
            return array();
        }

        $cached = get_transient( 'acf_csb_cloud_info' );
        if ( $cached ) {
            return $cached;
        }

        $response = $this->api_request( 'GET', '/account/info' );
        
        if ( ! is_wp_error( $response ) ) {
            set_transient( 'acf_csb_cloud_info', $response, HOUR_IN_SECONDS );
            return $response;
        }

        return array();
    }

    /**
     * API 요청
     */
    private function api_request( $method, $endpoint, $data = array() ) {
        $token = get_option( 'acf_csb_cloud_token' );
        
        $args = array(
            'method'  => $method,
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
                'X-Site-URL'    => home_url(),
            ),
            'timeout' => 30,
        );

        if ( ! empty( $data ) && in_array( $method, array( 'POST', 'PUT', 'PATCH' ), true ) ) {
            $args['body'] = wp_json_encode( $data );
        }

        $response = wp_remote_request( self::API_BASE . $endpoint, $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        return json_decode( $body, true );
    }

    /**
     * AJAX: 클라우드 연결
     */
    public function ajax_connect() {
        check_ajax_referer( 'acf_csb_cloud_connect', 'cloud_nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-code-snippets-box' ) );
        }

        $api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( $_POST['api_key'] ) : '';

        if ( empty( $api_key ) ) {
            wp_send_json_error( __( 'API 키를 입력하세요.', 'acf-code-snippets-box' ) );
        }

        // API 키로 토큰 교환
        $response = wp_remote_post( self::API_BASE . '/auth/token', array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode( array(
                'api_key'  => $api_key,
                'site_url' => home_url(),
            ) ),
            'timeout' => 30,
        ) );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( $response->get_error_message() );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( empty( $body['token'] ) ) {
            wp_send_json_error( $body['message'] ?? __( '연결에 실패했습니다.', 'acf-code-snippets-box' ) );
        }

        // 토큰 저장
        update_option( 'acf_csb_cloud_token', $body['token'] );
        update_option( 'acf_csb_cloud_email', $body['email'] ?? '' );

        // 캐시 삭제
        delete_transient( 'acf_csb_cloud_info' );

        wp_send_json_success( array(
            'message' => __( '클라우드에 연결되었습니다.', 'acf-code-snippets-box' ),
        ) );
    }

    /**
     * AJAX: 클라우드 연결 해제
     */
    public function ajax_disconnect() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-code-snippets-box' ) );
        }

        delete_option( 'acf_csb_cloud_token' );
        delete_option( 'acf_csb_cloud_email' );
        delete_transient( 'acf_csb_cloud_info' );

        // 크론 작업 제거
        wp_clear_scheduled_hook( 'acf_csb_cloud_sync_cron' );

        wp_send_json_success( array(
            'message' => __( '클라우드 연결이 해제되었습니다.', 'acf-code-snippets-box' ),
        ) );
    }

    /**
     * AJAX: 동기화 실행
     */
    public function ajax_sync() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-code-snippets-box' ) );
        }

        $result = $this->sync_all();

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        wp_send_json_success( $result );
    }

    /**
     * 전체 동기화
     */
    public function sync_all() {
        if ( ! $this->is_connected() ) {
            return new WP_Error( 'not_connected', __( '클라우드에 연결되어 있지 않습니다.', 'acf-code-snippets-box' ) );
        }

        // 로컬 스니펫 가져오기
        $local_snippets = $this->get_local_snippets();

        // 클라우드 스니펫 가져오기
        $cloud_response = $this->api_request( 'GET', '/snippets' );

        if ( is_wp_error( $cloud_response ) ) {
            return $cloud_response;
        }

        $cloud_snippets = $cloud_response['snippets'] ?? array();

        // 동기화 로직
        $results = array(
            'pushed'  => 0,
            'pulled'  => 0,
            'updated' => 0,
            'conflicts' => array(),
        );

        // 로컬 -> 클라우드 (푸시)
        foreach ( $local_snippets as $local ) {
            $cloud_match = $this->find_cloud_snippet( $local, $cloud_snippets );

            if ( ! $cloud_match ) {
                // 클라우드에 없음 -> 업로드
                $push_result = $this->push_snippet( $local );
                if ( ! is_wp_error( $push_result ) ) {
                    $results['pushed']++;
                }
            } elseif ( $this->is_local_newer( $local, $cloud_match ) ) {
                // 로컬이 더 최신 -> 업데이트
                $push_result = $this->push_snippet( $local, $cloud_match['id'] );
                if ( ! is_wp_error( $push_result ) ) {
                    $results['updated']++;
                }
            }
        }

        // 클라우드 -> 로컬 (풀)
        foreach ( $cloud_snippets as $cloud ) {
            $local_match = $this->find_local_snippet( $cloud, $local_snippets );

            if ( ! $local_match ) {
                // 로컬에 없음 -> 다운로드
                $pull_result = $this->pull_snippet( $cloud );
                if ( ! is_wp_error( $pull_result ) ) {
                    $results['pulled']++;
                }
            }
        }

        // 마지막 동기화 시간 저장
        update_option( 'acf_csb_last_sync', current_time( 'mysql' ) );
        delete_transient( 'acf_csb_cloud_info' );

        return $results;
    }

    /**
     * 로컬 스니펫 목록
     */
    private function get_local_snippets() {
        $args = array(
            'post_type'      => 'acf_code_snippet',
            'posts_per_page' => -1,
            'post_status'    => 'any',
        );

        $include_disabled = get_option( 'acf_csb_cloud_sync_disabled', 'no' ) === 'yes';
        if ( ! $include_disabled ) {
            $args['post_status'] = 'publish';
        }

        $posts = get_posts( $args );
        $snippets = array();

        foreach ( $posts as $post ) {
            $snippets[] = array(
                'id'            => $post->ID,
                'title'         => $post->post_title,
                'code'          => get_post_meta( $post->ID, '_acf_csb_code', true ),
                'code_type'     => get_post_meta( $post->ID, '_acf_csb_code_type', true ),
                'status'        => $post->post_status,
                'modified'      => $post->post_modified_gmt,
                'cloud_id'      => get_post_meta( $post->ID, '_acf_csb_cloud_id', true ),
                'conditions'    => get_post_meta( $post->ID, '_acf_csb_conditions', true ),
            );
        }

        return $snippets;
    }

    /**
     * 클라우드에서 일치하는 스니펫 찾기
     */
    private function find_cloud_snippet( $local, $cloud_snippets ) {
        // 클라우드 ID로 먼저 검색
        if ( ! empty( $local['cloud_id'] ) ) {
            foreach ( $cloud_snippets as $cloud ) {
                if ( $cloud['id'] === $local['cloud_id'] ) {
                    return $cloud;
                }
            }
        }

        // 제목으로 검색
        foreach ( $cloud_snippets as $cloud ) {
            if ( $cloud['title'] === $local['title'] ) {
                return $cloud;
            }
        }

        return null;
    }

    /**
     * 로컬에서 일치하는 스니펫 찾기
     */
    private function find_local_snippet( $cloud, $local_snippets ) {
        foreach ( $local_snippets as $local ) {
            if ( ! empty( $local['cloud_id'] ) && $local['cloud_id'] === $cloud['id'] ) {
                return $local;
            }
            if ( $local['title'] === $cloud['title'] ) {
                return $local;
            }
        }

        return null;
    }

    /**
     * 로컬이 더 최신인지 확인
     */
    private function is_local_newer( $local, $cloud ) {
        $local_time = strtotime( $local['modified'] );
        $cloud_time = strtotime( $cloud['modified'] ?? '0' );

        return $local_time > $cloud_time;
    }

    /**
     * 스니펫 푸시 (업로드)
     */
    private function push_snippet( $snippet, $cloud_id = null ) {
        $endpoint = $cloud_id ? '/snippets/' . $cloud_id : '/snippets';
        $method = $cloud_id ? 'PUT' : 'POST';

        $data = array(
            'title'      => $snippet['title'],
            'code'       => $snippet['code'],
            'code_type'  => $snippet['code_type'],
            'status'     => $snippet['status'],
        );

        if ( get_option( 'acf_csb_cloud_sync_conditions', 'yes' ) === 'yes' ) {
            $data['conditions'] = $snippet['conditions'];
        }

        $response = $this->api_request( $method, $endpoint, $data );

        if ( ! is_wp_error( $response ) && ! empty( $response['id'] ) ) {
            update_post_meta( $snippet['id'], '_acf_csb_cloud_id', $response['id'] );
            update_post_meta( $snippet['id'], '_acf_csb_sync_status', self::STATUS_SYNCED );
        }

        return $response;
    }

    /**
     * 스니펫 풀 (다운로드)
     */
    private function pull_snippet( $cloud ) {
        // 중복 확인
        $existing = get_posts( array(
            'post_type'  => 'acf_code_snippet',
            'title'      => $cloud['title'],
            'meta_query' => array(
                array(
                    'key'   => '_acf_csb_cloud_id',
                    'value' => $cloud['id'],
                ),
            ),
            'posts_per_page' => 1,
        ) );

        if ( ! empty( $existing ) ) {
            return $existing[0]->ID;
        }

        // 새 스니펫 생성
        $post_id = wp_insert_post( array(
            'post_title'   => $cloud['title'],
            'post_content' => '',
            'post_status'  => $cloud['status'] ?? 'draft',
            'post_type'    => 'acf_code_snippet',
        ) );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        update_post_meta( $post_id, '_acf_csb_code', $cloud['code'] );
        update_post_meta( $post_id, '_acf_csb_code_type', $cloud['code_type'] );
        update_post_meta( $post_id, '_acf_csb_cloud_id', $cloud['id'] );
        update_post_meta( $post_id, '_acf_csb_sync_status', self::STATUS_SYNCED );

        if ( ! empty( $cloud['conditions'] ) ) {
            update_post_meta( $post_id, '_acf_csb_conditions', $cloud['conditions'] );
        }

        return $post_id;
    }

    /**
     * 자동 동기화 (스니펫 저장 시)
     */
    public function auto_sync_snippet( $post_id, $post ) {
        if ( $post->post_type !== 'acf_code_snippet' ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        $snippet = array(
            'id'         => $post_id,
            'title'      => $post->post_title,
            'code'       => get_post_meta( $post_id, '_acf_csb_code', true ),
            'code_type'  => get_post_meta( $post_id, '_acf_csb_code_type', true ),
            'status'     => $post->post_status,
            'conditions' => get_post_meta( $post_id, '_acf_csb_conditions', true ),
        );

        $cloud_id = get_post_meta( $post_id, '_acf_csb_cloud_id', true );

        $this->push_snippet( $snippet, $cloud_id ?: null );
    }

    /**
     * 크론 동기화
     */
    public function cron_sync() {
        if ( ! $this->is_connected() ) {
            return;
        }

        $this->sync_all();
    }
}
