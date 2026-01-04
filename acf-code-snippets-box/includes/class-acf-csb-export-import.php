<?php
/**
 * ACF Code Snippets Box - Export/Import System
 *
 * 스니펫 내보내기/가져오기 기능
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Export/Import 클래스
 */
class ACF_CSB_Export_Import {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 내보내기 형식
     */
    const FORMAT_JSON = 'json';
    const FORMAT_ZIP  = 'zip';
    const FORMAT_XML  = 'xml';

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
        // 관리자 메뉴 추가
        add_action( 'admin_menu', array( $this, 'add_submenu' ), 20 );

        // AJAX 핸들러
        add_action( 'wp_ajax_acf_csb_export_snippets', array( $this, 'ajax_export_snippets' ) );
        add_action( 'wp_ajax_acf_csb_import_snippets', array( $this, 'ajax_import_snippets' ) );
        add_action( 'wp_ajax_acf_csb_export_single', array( $this, 'ajax_export_single' ) );

        // 대량 작업 추가
        add_filter( 'bulk_actions-edit-acf_code_snippet', array( $this, 'add_bulk_actions' ) );
        add_filter( 'handle_bulk_actions-edit-acf_code_snippet', array( $this, 'handle_bulk_export' ), 10, 3 );
    }

    /**
     * 서브메뉴 추가
     */
    public function add_submenu() {
        add_submenu_page(
            'edit.php?post_type=acf_code_snippet',
            __( '내보내기/가져오기', 'acf-code-snippets-box' ),
            __( '📤 내보내기/가져오기', 'acf-code-snippets-box' ),
            'manage_options',
            'acf-csb-export-import',
            array( $this, 'render_page' )
        );
    }

    /**
     * 내보내기/가져오기 페이지 렌더링
     */
    public function render_page() {
        // Pro 버전 체크
        $is_pro = ACF_CSB_License::is_pro();
        ?>
        <div class="wrap acf-csb-export-import-page">
            <h1><?php esc_html_e( '스니펫 내보내기/가져오기', 'acf-code-snippets-box' ); ?></h1>

            <?php if ( ! $is_pro ) : ?>
                <div class="notice notice-warning">
                    <p>
                        <?php esc_html_e( '내보내기/가져오기 기능은 Pro 버전에서 사용할 수 있습니다.', 'acf-code-snippets-box' ); ?>
                        <a href="https://3j-labs.com/pricing" target="_blank" class="button button-primary" style="margin-left: 10px;">
                            <?php esc_html_e( 'Pro 업그레이드', 'acf-code-snippets-box' ); ?>
                        </a>
                    </p>
                </div>
            <?php endif; ?>

            <div class="acf-csb-export-import-wrapper <?php echo ! $is_pro ? 'disabled' : ''; ?>">
                <!-- 내보내기 섹션 -->
                <div class="acf-csb-card">
                    <h2>
                        <span class="dashicons dashicons-upload"></span>
                        <?php esc_html_e( '내보내기', 'acf-code-snippets-box' ); ?>
                    </h2>

                    <p class="description">
                        <?php esc_html_e( '스니펫을 JSON 파일로 내보내 다른 사이트에서 사용하거나 백업할 수 있습니다.', 'acf-code-snippets-box' ); ?>
                    </p>

                    <form id="acf-csb-export-form" class="acf-csb-export-form">
                        <?php wp_nonce_field( 'acf_csb_export', 'export_nonce' ); ?>

                        <div class="acf-csb-form-row">
                            <label><?php esc_html_e( '내보낼 스니펫 선택', 'acf-code-snippets-box' ); ?></label>
                            <div class="acf-csb-snippet-select">
                                <label>
                                    <input type="checkbox" id="select-all-snippets" checked>
                                    <?php esc_html_e( '모두 선택', 'acf-code-snippets-box' ); ?>
                                </label>
                                
                                <div class="acf-csb-snippet-list">
                                    <?php
                                    $snippets = get_posts( array(
                                        'post_type'      => 'acf_code_snippet',
                                        'posts_per_page' => -1,
                                        'post_status'    => 'any',
                                    ) );

                                    if ( $snippets ) :
                                        foreach ( $snippets as $snippet ) :
                                            $code_type = get_post_meta( $snippet->ID, '_acf_csb_code_type', true );
                                            ?>
                                            <label>
                                                <input type="checkbox" name="snippet_ids[]" 
                                                       value="<?php echo esc_attr( $snippet->ID ); ?>" checked>
                                                <span class="snippet-type snippet-type-<?php echo esc_attr( $code_type ); ?>">
                                                    <?php echo esc_html( strtoupper( $code_type ) ); ?>
                                                </span>
                                                <?php echo esc_html( $snippet->post_title ); ?>
                                                <span class="snippet-status status-<?php echo esc_attr( $snippet->post_status ); ?>">
                                                    <?php echo esc_html( $snippet->post_status ); ?>
                                                </span>
                                            </label>
                                        <?php
                                        endforeach;
                                    else :
                                        ?>
                                        <p class="no-snippets"><?php esc_html_e( '내보낼 스니펫이 없습니다.', 'acf-code-snippets-box' ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="acf-csb-form-row">
                            <label><?php esc_html_e( '내보내기 형식', 'acf-code-snippets-box' ); ?></label>
                            <select name="export_format">
                                <option value="json"><?php esc_html_e( 'JSON (.json)', 'acf-code-snippets-box' ); ?></option>
                                <option value="zip"><?php esc_html_e( 'ZIP 압축 파일 (.zip)', 'acf-code-snippets-box' ); ?></option>
                            </select>
                        </div>

                        <div class="acf-csb-form-row">
                            <label>
                                <input type="checkbox" name="include_conditions" value="1" checked>
                                <?php esc_html_e( '조건 설정 포함', 'acf-code-snippets-box' ); ?>
                            </label>
                        </div>

                        <button type="submit" class="button button-primary" <?php echo ! $is_pro ? 'disabled' : ''; ?>>
                            <span class="dashicons dashicons-download"></span>
                            <?php esc_html_e( '내보내기', 'acf-code-snippets-box' ); ?>
                        </button>
                    </form>
                </div>

                <!-- 가져오기 섹션 -->
                <div class="acf-csb-card">
                    <h2>
                        <span class="dashicons dashicons-download"></span>
                        <?php esc_html_e( '가져오기', 'acf-code-snippets-box' ); ?>
                    </h2>

                    <p class="description">
                        <?php esc_html_e( '이전에 내보낸 스니펫 파일을 가져옵니다.', 'acf-code-snippets-box' ); ?>
                    </p>

                    <form id="acf-csb-import-form" class="acf-csb-import-form" enctype="multipart/form-data">
                        <?php wp_nonce_field( 'acf_csb_import', 'import_nonce' ); ?>

                        <div class="acf-csb-form-row">
                            <label><?php esc_html_e( '파일 선택', 'acf-code-snippets-box' ); ?></label>
                            <div class="acf-csb-file-upload">
                                <input type="file" name="import_file" id="import-file" 
                                       accept=".json,.zip" <?php echo ! $is_pro ? 'disabled' : ''; ?>>
                                <label for="import-file" class="acf-csb-file-label">
                                    <span class="dashicons dashicons-upload"></span>
                                    <?php esc_html_e( '파일을 선택하거나 여기에 드래그하세요', 'acf-code-snippets-box' ); ?>
                                </label>
                                <span id="selected-file-name"></span>
                            </div>
                        </div>

                        <div class="acf-csb-form-row">
                            <label><?php esc_html_e( '중복 처리', 'acf-code-snippets-box' ); ?></label>
                            <select name="duplicate_handling">
                                <option value="skip"><?php esc_html_e( '건너뛰기 (기존 유지)', 'acf-code-snippets-box' ); ?></option>
                                <option value="replace"><?php esc_html_e( '덮어쓰기', 'acf-code-snippets-box' ); ?></option>
                                <option value="rename"><?php esc_html_e( '이름 변경 후 추가', 'acf-code-snippets-box' ); ?></option>
                            </select>
                        </div>

                        <div class="acf-csb-form-row">
                            <label>
                                <input type="checkbox" name="activate_imported" value="1">
                                <?php esc_html_e( '가져온 스니펫 자동 활성화', 'acf-code-snippets-box' ); ?>
                            </label>
                        </div>

                        <button type="submit" class="button button-primary" <?php echo ! $is_pro ? 'disabled' : ''; ?>>
                            <span class="dashicons dashicons-upload"></span>
                            <?php esc_html_e( '가져오기', 'acf-code-snippets-box' ); ?>
                        </button>
                    </form>

                    <div id="import-progress" class="acf-csb-progress" style="display: none;">
                        <div class="acf-csb-progress-bar">
                            <div class="acf-csb-progress-fill"></div>
                        </div>
                        <div class="acf-csb-progress-text"></div>
                    </div>

                    <div id="import-results" class="acf-csb-results" style="display: none;"></div>
                </div>

                <!-- 클라우드 동기화 (Pro Premium+) -->
                <div class="acf-csb-card acf-csb-cloud-sync">
                    <h2>
                        <span class="dashicons dashicons-cloud"></span>
                        <?php esc_html_e( '클라우드 동기화', 'acf-code-snippets-box' ); ?>
                        <span class="acf-csb-pro-badge">Premium</span>
                    </h2>

                    <p class="description">
                        <?php esc_html_e( '스니펫을 클라우드에 저장하고 여러 사이트 간에 동기화합니다.', 'acf-code-snippets-box' ); ?>
                    </p>

                    <?php if ( ACF_CSB_License::has_access( 'premium' ) ) : ?>
                        <div class="acf-csb-cloud-status">
                            <span class="status-icon">🔗</span>
                            <span class="status-text"><?php esc_html_e( '클라우드 연결됨', 'acf-code-snippets-box' ); ?></span>
                        </div>
                        
                        <div class="acf-csb-cloud-actions">
                            <button class="button" id="cloud-sync-now">
                                <span class="dashicons dashicons-update"></span>
                                <?php esc_html_e( '지금 동기화', 'acf-code-snippets-box' ); ?>
                            </button>
                            <button class="button" id="cloud-settings">
                                <span class="dashicons dashicons-admin-generic"></span>
                                <?php esc_html_e( '설정', 'acf-code-snippets-box' ); ?>
                            </button>
                        </div>
                    <?php else : ?>
                        <div class="acf-csb-upgrade-notice">
                            <p><?php esc_html_e( '클라우드 동기화는 Pro Premium 이상에서 사용할 수 있습니다.', 'acf-code-snippets-box' ); ?></p>
                            <a href="https://3j-labs.com/pricing" class="button button-primary">
                                <?php esc_html_e( '업그레이드하기', 'acf-code-snippets-box' ); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <style>
        .acf-csb-export-import-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .acf-csb-export-import-wrapper.disabled .acf-csb-card {
            opacity: 0.6;
            pointer-events: none;
        }
        .acf-csb-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 25px;
        }
        .acf-csb-card h2 {
            margin: 0 0 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .acf-csb-form-row {
            margin-bottom: 20px;
        }
        .acf-csb-form-row > label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .acf-csb-snippet-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
        }
        .acf-csb-snippet-list label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 0;
            cursor: pointer;
        }
        .snippet-type {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
        }
        .snippet-type-css { background: #3498db; color: #fff; }
        .snippet-type-js { background: #f1c40f; color: #333; }
        .snippet-type-php { background: #8e44ad; color: #fff; }
        .snippet-type-html { background: #e74c3c; color: #fff; }
        .snippet-status {
            font-size: 11px;
            color: #999;
            margin-left: auto;
        }
        .acf-csb-file-upload {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .acf-csb-file-upload:hover {
            border-color: #0073aa;
            background: #f0f6fc;
        }
        .acf-csb-file-upload input[type="file"] {
            display: none;
        }
        .acf-csb-file-label {
            cursor: pointer;
            color: #666;
        }
        .acf-csb-file-label .dashicons {
            font-size: 32px;
            display: block;
            margin-bottom: 10px;
            color: #999;
        }
        .acf-csb-pro-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
            margin-left: 10px;
        }
        .acf-csb-cloud-sync {
            grid-column: 1 / -1;
        }
        .acf-csb-cloud-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 15px;
            background: #d4edda;
            border-radius: 6px;
            margin: 15px 0;
        }
        .acf-csb-cloud-actions {
            display: flex;
            gap: 10px;
        }
        .acf-csb-upgrade-notice {
            background: #fff3cd;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        .acf-csb-progress {
            margin-top: 20px;
        }
        .acf-csb-progress-bar {
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }
        .acf-csb-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.3s ease;
        }
        .acf-csb-progress-text {
            margin-top: 10px;
            font-size: 13px;
            color: #666;
        }
        .acf-csb-results {
            margin-top: 20px;
            padding: 15px;
            border-radius: 6px;
        }
        .acf-csb-results.success {
            background: #d4edda;
            color: #155724;
        }
        .acf-csb-results.error {
            background: #f8d7da;
            color: #721c24;
        }
        </style>
        <?php
    }

    /**
     * 대량 작업 추가
     */
    public function add_bulk_actions( $actions ) {
        if ( ACF_CSB_License::is_pro() ) {
            $actions['export'] = __( '내보내기', 'acf-code-snippets-box' );
        }
        return $actions;
    }

    /**
     * 대량 내보내기 처리
     */
    public function handle_bulk_export( $redirect_to, $action, $post_ids ) {
        if ( 'export' !== $action ) {
            return $redirect_to;
        }

        $export_data = $this->prepare_export_data( $post_ids );
        
        // 다운로드 URL 생성
        $upload_dir = wp_upload_dir();
        $filename = 'acf-csb-export-' . date( 'Y-m-d-His' ) . '.json';
        $filepath = $upload_dir['basedir'] . '/acf-csb-exports/' . $filename;

        wp_mkdir_p( dirname( $filepath ) );
        file_put_contents( $filepath, wp_json_encode( $export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );

        $redirect_to = add_query_arg( array(
            'acf_csb_export' => urlencode( $upload_dir['baseurl'] . '/acf-csb-exports/' . $filename ),
            'count'          => count( $post_ids ),
        ), $redirect_to );

        return $redirect_to;
    }

    /**
     * AJAX: 스니펫 내보내기
     */
    public function ajax_export_snippets() {
        check_ajax_referer( 'acf_csb_export', 'export_nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-code-snippets-box' ) );
        }

        if ( ! ACF_CSB_License::is_pro() ) {
            wp_send_json_error( __( 'Pro 버전이 필요합니다.', 'acf-code-snippets-box' ) );
        }

        $snippet_ids = isset( $_POST['snippet_ids'] ) ? array_map( 'absint', $_POST['snippet_ids'] ) : array();
        $format = isset( $_POST['export_format'] ) ? sanitize_text_field( $_POST['export_format'] ) : 'json';
        $include_conditions = isset( $_POST['include_conditions'] );

        if ( empty( $snippet_ids ) ) {
            wp_send_json_error( __( '내보낼 스니펫을 선택하세요.', 'acf-code-snippets-box' ) );
        }

        $export_data = $this->prepare_export_data( $snippet_ids, $include_conditions );

        if ( 'zip' === $format ) {
            $file_url = $this->create_zip_export( $export_data );
        } else {
            $file_url = $this->create_json_export( $export_data );
        }

        wp_send_json_success( array(
            'file_url' => $file_url,
            'count'    => count( $snippet_ids ),
        ) );
    }

    /**
     * 내보내기 데이터 준비
     */
    private function prepare_export_data( $snippet_ids, $include_conditions = true ) {
        $snippets = array();

        foreach ( $snippet_ids as $id ) {
            $post = get_post( $id );
            
            if ( ! $post || $post->post_type !== 'acf_code_snippet' ) {
                continue;
            }

            $snippet = array(
                'title'       => $post->post_title,
                'code'        => get_post_meta( $id, '_acf_csb_code', true ),
                'code_type'   => get_post_meta( $id, '_acf_csb_code_type', true ),
                'status'      => $post->post_status,
                'description' => $post->post_excerpt,
                'priority'    => get_post_meta( $id, '_acf_csb_priority', true ),
                'location'    => get_post_meta( $id, '_acf_csb_location', true ),
            );

            if ( $include_conditions ) {
                $snippet['conditions'] = get_post_meta( $id, '_acf_csb_conditions', true );
            }

            $snippets[] = $snippet;
        }

        return array(
            'version'   => ACF_CSB_VERSION,
            'exported'  => current_time( 'mysql' ),
            'site_url'  => home_url(),
            'snippets'  => $snippets,
        );
    }

    /**
     * JSON 내보내기 파일 생성
     */
    private function create_json_export( $data ) {
        $upload_dir = wp_upload_dir();
        $filename = 'acf-csb-export-' . date( 'Y-m-d-His' ) . '.json';
        $filepath = $upload_dir['basedir'] . '/acf-csb-exports/' . $filename;

        wp_mkdir_p( dirname( $filepath ) );
        file_put_contents( $filepath, wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );

        return $upload_dir['baseurl'] . '/acf-csb-exports/' . $filename;
    }

    /**
     * ZIP 내보내기 파일 생성
     */
    private function create_zip_export( $data ) {
        $upload_dir = wp_upload_dir();
        $zip_filename = 'acf-csb-export-' . date( 'Y-m-d-His' ) . '.zip';
        $zip_filepath = $upload_dir['basedir'] . '/acf-csb-exports/' . $zip_filename;

        wp_mkdir_p( dirname( $zip_filepath ) );

        $zip = new ZipArchive();
        if ( $zip->open( $zip_filepath, ZipArchive::CREATE ) !== true ) {
            return false;
        }

        // 메타 데이터
        $zip->addFromString( 'export.json', wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );

        // 개별 스니펫 파일
        foreach ( $data['snippets'] as $index => $snippet ) {
            $ext = $this->get_file_extension( $snippet['code_type'] );
            $filename = sanitize_file_name( $snippet['title'] ) . '.' . $ext;
            $zip->addFromString( 'snippets/' . $filename, $snippet['code'] );
        }

        $zip->close();

        return $upload_dir['baseurl'] . '/acf-csb-exports/' . $zip_filename;
    }

    /**
     * 코드 타입별 파일 확장자
     */
    private function get_file_extension( $code_type ) {
        $extensions = array(
            'css'  => 'css',
            'js'   => 'js',
            'php'  => 'php',
            'html' => 'html',
        );

        return isset( $extensions[ $code_type ] ) ? $extensions[ $code_type ] : 'txt';
    }

    /**
     * AJAX: 스니펫 가져오기
     */
    public function ajax_import_snippets() {
        check_ajax_referer( 'acf_csb_import', 'import_nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-code-snippets-box' ) );
        }

        if ( ! ACF_CSB_License::is_pro() ) {
            wp_send_json_error( __( 'Pro 버전이 필요합니다.', 'acf-code-snippets-box' ) );
        }

        if ( empty( $_FILES['import_file'] ) ) {
            wp_send_json_error( __( '파일을 선택하세요.', 'acf-code-snippets-box' ) );
        }

        $file = $_FILES['import_file'];
        $duplicate_handling = isset( $_POST['duplicate_handling'] ) ? sanitize_text_field( $_POST['duplicate_handling'] ) : 'skip';
        $activate_imported = isset( $_POST['activate_imported'] );

        // 파일 타입 확인
        $file_ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        
        if ( ! in_array( $file_ext, array( 'json', 'zip' ), true ) ) {
            wp_send_json_error( __( 'JSON 또는 ZIP 파일만 가져올 수 있습니다.', 'acf-code-snippets-box' ) );
        }

        // 파일 읽기
        if ( 'zip' === $file_ext ) {
            $import_data = $this->read_zip_import( $file['tmp_name'] );
        } else {
            $content = file_get_contents( $file['tmp_name'] );
            $import_data = json_decode( $content, true );
        }

        if ( ! $import_data || ! isset( $import_data['snippets'] ) ) {
            wp_send_json_error( __( '유효하지 않은 내보내기 파일입니다.', 'acf-code-snippets-box' ) );
        }

        // 가져오기 실행
        $results = $this->process_import( $import_data['snippets'], $duplicate_handling, $activate_imported );

        wp_send_json_success( $results );
    }

    /**
     * ZIP 파일에서 가져오기 데이터 읽기
     */
    private function read_zip_import( $zip_path ) {
        $zip = new ZipArchive();
        
        if ( $zip->open( $zip_path ) !== true ) {
            return false;
        }

        $export_json = $zip->getFromName( 'export.json' );
        $zip->close();

        if ( ! $export_json ) {
            return false;
        }

        return json_decode( $export_json, true );
    }

    /**
     * 가져오기 처리
     */
    private function process_import( $snippets, $duplicate_handling, $activate ) {
        $results = array(
            'imported' => 0,
            'skipped'  => 0,
            'updated'  => 0,
            'errors'   => array(),
        );

        foreach ( $snippets as $snippet ) {
            // 중복 확인
            $existing = get_page_by_title( $snippet['title'], OBJECT, 'acf_code_snippet' );

            if ( $existing ) {
                switch ( $duplicate_handling ) {
                    case 'skip':
                        $results['skipped']++;
                        continue 2;

                    case 'replace':
                        wp_delete_post( $existing->ID, true );
                        break;

                    case 'rename':
                        $snippet['title'] .= ' (가져옴)';
                        break;
                }
            }

            // 스니펫 생성
            $post_id = wp_insert_post( array(
                'post_title'   => sanitize_text_field( $snippet['title'] ),
                'post_content' => '',
                'post_excerpt' => isset( $snippet['description'] ) ? sanitize_textarea_field( $snippet['description'] ) : '',
                'post_status'  => $activate ? 'publish' : ( isset( $snippet['status'] ) ? $snippet['status'] : 'draft' ),
                'post_type'    => 'acf_code_snippet',
            ) );

            if ( is_wp_error( $post_id ) ) {
                $results['errors'][] = $snippet['title'] . ': ' . $post_id->get_error_message();
                continue;
            }

            // 메타 데이터 저장
            update_post_meta( $post_id, '_acf_csb_code', $snippet['code'] );
            update_post_meta( $post_id, '_acf_csb_code_type', $snippet['code_type'] );
            
            if ( isset( $snippet['priority'] ) ) {
                update_post_meta( $post_id, '_acf_csb_priority', $snippet['priority'] );
            }
            
            if ( isset( $snippet['location'] ) ) {
                update_post_meta( $post_id, '_acf_csb_location', $snippet['location'] );
            }
            
            if ( isset( $snippet['conditions'] ) ) {
                update_post_meta( $post_id, '_acf_csb_conditions', $snippet['conditions'] );
            }

            if ( $existing && 'replace' === $duplicate_handling ) {
                $results['updated']++;
            } else {
                $results['imported']++;
            }
        }

        return $results;
    }

    /**
     * AJAX: 단일 스니펫 내보내기
     */
    public function ajax_export_single() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        $snippet_id = isset( $_POST['snippet_id'] ) ? absint( $_POST['snippet_id'] ) : 0;

        if ( ! $snippet_id ) {
            wp_send_json_error();
        }

        $export_data = $this->prepare_export_data( array( $snippet_id ) );
        
        wp_send_json_success( $export_data );
    }
}
