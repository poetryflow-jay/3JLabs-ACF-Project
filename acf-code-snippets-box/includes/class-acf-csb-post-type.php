<?php
/**
 * ACF Code Snippets Box - Custom Post Type
 * 
 * 코드 스니펫을 저장하는 커스텀 포스트 타입 등록
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Post Type 클래스
 */
class ACF_CSB_Post_Type {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * Post Type 슬러그
     */
    const POST_TYPE = 'acf_code_snippet';

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
     * 생성자
     */
    private function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_meta_boxes' ), 10, 2 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }

    /**
     * Post Type 등록
     */
    public function register() {
        $labels = array(
            'name'                  => _x( '코드 스니펫', 'Post Type 이름', 'acf-code-snippets-box' ),
            'singular_name'         => _x( '코드 스니펫', 'Post Type 단수 이름', 'acf-code-snippets-box' ),
            'menu_name'             => __( 'Code Snippets', 'acf-code-snippets-box' ),
            'add_new'               => __( '새 스니펫 추가', 'acf-code-snippets-box' ),
            'add_new_item'          => __( '새 코드 스니펫 추가', 'acf-code-snippets-box' ),
            'edit_item'             => __( '코드 스니펫 편집', 'acf-code-snippets-box' ),
            'new_item'              => __( '새 코드 스니펫', 'acf-code-snippets-box' ),
            'view_item'             => __( '코드 스니펫 보기', 'acf-code-snippets-box' ),
            'search_items'          => __( '코드 스니펫 검색', 'acf-code-snippets-box' ),
            'not_found'             => __( '스니펫을 찾을 수 없습니다.', 'acf-code-snippets-box' ),
            'not_found_in_trash'    => __( '휴지통에 스니펫이 없습니다.', 'acf-code-snippets-box' ),
            'all_items'             => __( '모든 스니펫', 'acf-code-snippets-box' ),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => false, // 커스텀 메뉴에 표시
            'query_var'           => false,
            'rewrite'             => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => null,
            'supports'            => array( 'title' ),
            'show_in_rest'        => false,
        );

        register_post_type( self::POST_TYPE, $args );

        // 카테고리 택소노미 등록
        $this->register_taxonomy();
    }

    /**
     * 택소노미 등록
     */
    private function register_taxonomy() {
        $labels = array(
            'name'              => _x( '스니펫 카테고리', 'taxonomy 이름', 'acf-code-snippets-box' ),
            'singular_name'     => _x( '카테고리', 'taxonomy 단수 이름', 'acf-code-snippets-box' ),
            'search_items'      => __( '카테고리 검색', 'acf-code-snippets-box' ),
            'all_items'         => __( '모든 카테고리', 'acf-code-snippets-box' ),
            'edit_item'         => __( '카테고리 편집', 'acf-code-snippets-box' ),
            'update_item'       => __( '카테고리 업데이트', 'acf-code-snippets-box' ),
            'add_new_item'      => __( '새 카테고리 추가', 'acf-code-snippets-box' ),
            'new_item_name'     => __( '새 카테고리 이름', 'acf-code-snippets-box' ),
            'menu_name'         => __( '카테고리', 'acf-code-snippets-box' ),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => false,
            'rewrite'           => false,
        );

        register_taxonomy( 'snippet_category', self::POST_TYPE, $args );
    }

    /**
     * 메타 박스 추가
     */
    public function add_meta_boxes() {
        // 코드 에디터
        add_meta_box(
            'acf_csb_code_editor',
            __( '코드 에디터', 'acf-code-snippets-box' ),
            array( $this, 'render_code_editor' ),
            self::POST_TYPE,
            'normal',
            'high'
        );

        // 코드 타입 및 설정
        add_meta_box(
            'acf_csb_settings',
            __( '스니펫 설정', 'acf-code-snippets-box' ),
            array( $this, 'render_settings_meta_box' ),
            self::POST_TYPE,
            'side',
            'high'
        );

        // 트리거 조건
        add_meta_box(
            'acf_csb_triggers',
            __( '실행 조건 (트리거)', 'acf-code-snippets-box' ),
            array( $this, 'render_triggers_meta_box' ),
            self::POST_TYPE,
            'normal',
            'default'
        );

        // ACF CSS 연동 (활성화된 경우)
        if ( ACF_Code_Snippets_Box::is_acf_css_active() ) {
            add_meta_box(
                'acf_csb_acf_css',
                __( 'ACF CSS 연동', 'acf-code-snippets-box' ),
                array( $this, 'render_acf_css_meta_box' ),
                self::POST_TYPE,
                'side',
                'default'
            );
        }
    }

    /**
     * 코드 에디터 렌더링
     */
    public function render_code_editor( $post ) {
        wp_nonce_field( 'acf_csb_save_meta', 'acf_csb_nonce' );
        $code = get_post_meta( $post->ID, '_acf_csb_code', true );
        ?>
        <div class="acf-csb-code-editor-wrapper">
            <textarea 
                id="acf_csb_code" 
                name="acf_csb_code" 
                rows="20" 
                style="width: 100%; font-family: 'Fira Code', 'Monaco', 'Consolas', monospace; font-size: 14px; tab-size: 4;"
            ><?php echo esc_textarea( $code ); ?></textarea>
            <p class="description">
                <?php esc_html_e( 'PHP 코드는 <?php ?> 태그 없이 작성하세요. 보안상 PHP 실행은 설정에서 활성화해야 합니다.', 'acf-code-snippets-box' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * 설정 메타 박스 렌더링
     */
    public function render_settings_meta_box( $post ) {
        $code_type = get_post_meta( $post->ID, '_acf_csb_code_type', true ) ?: 'css';
        $is_active = get_post_meta( $post->ID, '_acf_csb_active', true );
        $priority  = get_post_meta( $post->ID, '_acf_csb_priority', true ) ?: 10;
        $description = get_post_meta( $post->ID, '_acf_csb_description', true );
        ?>
        <p>
            <label for="acf_csb_code_type"><strong><?php esc_html_e( '코드 타입', 'acf-code-snippets-box' ); ?></strong></label><br>
            <select id="acf_csb_code_type" name="acf_csb_code_type" style="width: 100%;">
                <option value="css" <?php selected( $code_type, 'css' ); ?>>CSS</option>
                <option value="js" <?php selected( $code_type, 'js' ); ?>>JavaScript</option>
                <option value="html" <?php selected( $code_type, 'html' ); ?>>HTML</option>
                <option value="php" <?php selected( $code_type, 'php' ); ?>>PHP</option>
            </select>
        </p>

        <p>
            <label>
                <input type="checkbox" name="acf_csb_active" value="1" <?php checked( $is_active, '1' ); ?>>
                <strong><?php esc_html_e( '활성화', 'acf-code-snippets-box' ); ?></strong>
            </label>
        </p>

        <p>
            <label for="acf_csb_priority"><strong><?php esc_html_e( '우선순위', 'acf-code-snippets-box' ); ?></strong></label><br>
            <input type="number" id="acf_csb_priority" name="acf_csb_priority" value="<?php echo esc_attr( $priority ); ?>" min="1" max="100" style="width: 100%;">
            <span class="description"><?php esc_html_e( '숫자가 낮을수록 먼저 실행됩니다.', 'acf-code-snippets-box' ); ?></span>
        </p>

        <p>
            <label for="acf_csb_description"><strong><?php esc_html_e( '설명', 'acf-code-snippets-box' ); ?></strong></label><br>
            <textarea id="acf_csb_description" name="acf_csb_description" rows="3" style="width: 100%;"><?php echo esc_textarea( $description ); ?></textarea>
        </p>
        <?php
    }

    /**
     * 트리거 조건 메타 박스 렌더링
     */
    public function render_triggers_meta_box( $post ) {
        $triggers = get_post_meta( $post->ID, '_acf_csb_triggers', true ) ?: array();
        $location = isset( $triggers['location'] ) ? $triggers['location'] : 'everywhere';
        $pages    = isset( $triggers['pages'] ) ? $triggers['pages'] : '';
        $posts    = isset( $triggers['posts'] ) ? $triggers['posts'] : '';
        $user_roles = isset( $triggers['user_roles'] ) ? $triggers['user_roles'] : array();
        $device   = isset( $triggers['device'] ) ? $triggers['device'] : 'all';
        ?>
        <div class="acf-csb-triggers-wrapper">
            <table class="form-table">
                <tr>
                    <th><label><?php esc_html_e( '실행 위치', 'acf-code-snippets-box' ); ?></label></th>
                    <td>
                        <select name="acf_csb_triggers[location]" style="width: 100%;">
                            <option value="everywhere" <?php selected( $location, 'everywhere' ); ?>><?php esc_html_e( '모든 페이지', 'acf-code-snippets-box' ); ?></option>
                            <option value="frontend" <?php selected( $location, 'frontend' ); ?>><?php esc_html_e( '프론트엔드만', 'acf-code-snippets-box' ); ?></option>
                            <option value="admin" <?php selected( $location, 'admin' ); ?>><?php esc_html_e( '관리자 페이지만', 'acf-code-snippets-box' ); ?></option>
                            <option value="specific_pages" <?php selected( $location, 'specific_pages' ); ?>><?php esc_html_e( '특정 페이지', 'acf-code-snippets-box' ); ?></option>
                            <option value="specific_posts" <?php selected( $location, 'specific_posts' ); ?>><?php esc_html_e( '특정 포스트', 'acf-code-snippets-box' ); ?></option>
                            <option value="post_types" <?php selected( $location, 'post_types' ); ?>><?php esc_html_e( '특정 포스트 타입', 'acf-code-snippets-box' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="acf-csb-specific-pages" style="<?php echo $location !== 'specific_pages' ? 'display:none;' : ''; ?>">
                    <th><label><?php esc_html_e( '페이지 ID (쉼표 구분)', 'acf-code-snippets-box' ); ?></label></th>
                    <td>
                        <input type="text" name="acf_csb_triggers[pages]" value="<?php echo esc_attr( $pages ); ?>" style="width: 100%;" placeholder="1, 2, 3">
                    </td>
                </tr>
                <tr class="acf-csb-specific-posts" style="<?php echo $location !== 'specific_posts' ? 'display:none;' : ''; ?>">
                    <th><label><?php esc_html_e( '포스트 ID (쉼표 구분)', 'acf-code-snippets-box' ); ?></label></th>
                    <td>
                        <input type="text" name="acf_csb_triggers[posts]" value="<?php echo esc_attr( $posts ); ?>" style="width: 100%;" placeholder="10, 20, 30">
                    </td>
                </tr>
                <tr>
                    <th><label><?php esc_html_e( '사용자 역할', 'acf-code-snippets-box' ); ?></label></th>
                    <td>
                        <?php
                        $all_roles = wp_roles()->get_names();
                        foreach ( $all_roles as $role_key => $role_name ) :
                        ?>
                        <label style="display: block; margin-bottom: 5px;">
                            <input type="checkbox" name="acf_csb_triggers[user_roles][]" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( in_array( $role_key, $user_roles, true ) ); ?>>
                            <?php echo esc_html( $role_name ); ?>
                        </label>
                        <?php endforeach; ?>
                        <p class="description"><?php esc_html_e( '선택하지 않으면 모든 사용자에게 적용됩니다.', 'acf-code-snippets-box' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label><?php esc_html_e( '디바이스', 'acf-code-snippets-box' ); ?></label></th>
                    <td>
                        <select name="acf_csb_triggers[device]" style="width: 100%;">
                            <option value="all" <?php selected( $device, 'all' ); ?>><?php esc_html_e( '모든 디바이스', 'acf-code-snippets-box' ); ?></option>
                            <option value="desktop" <?php selected( $device, 'desktop' ); ?>><?php esc_html_e( '데스크탑만', 'acf-code-snippets-box' ); ?></option>
                            <option value="mobile" <?php selected( $device, 'mobile' ); ?>><?php esc_html_e( '모바일만', 'acf-code-snippets-box' ); ?></option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /**
     * ACF CSS 연동 메타 박스 렌더링
     */
    public function render_acf_css_meta_box( $post ) {
        $use_css_vars = get_post_meta( $post->ID, '_acf_csb_use_css_vars', true );
        ?>
        <p>
            <label>
                <input type="checkbox" name="acf_csb_use_css_vars" value="1" <?php checked( $use_css_vars, '1' ); ?>>
                <?php esc_html_e( 'ACF CSS 변수 사용', 'acf-code-snippets-box' ); ?>
            </label>
        </p>
        <p class="description">
            <?php esc_html_e( 'CSS 코드에서 var(--jj-*) 변수를 자동완성합니다.', 'acf-code-snippets-box' ); ?>
        </p>
        <hr>
        <p><strong><?php esc_html_e( '사용 가능한 변수:', 'acf-code-snippets-box' ); ?></strong></p>
        <ul style="font-size: 12px; margin-left: 15px;">
            <li><code>--jj-primary-color</code></li>
            <li><code>--jj-secondary-color</code></li>
            <li><code>--jj-font-family-primary</code></li>
            <li><code>--jj-font-size-base</code></li>
            <li><?php esc_html_e( '... 더 많은 변수', 'acf-code-snippets-box' ); ?></li>
        </ul>
        <?php
    }

    /**
     * 메타 박스 저장
     */
    public function save_meta_boxes( $post_id, $post ) {
        // Nonce 확인
        if ( ! isset( $_POST['acf_csb_nonce'] ) || ! wp_verify_nonce( $_POST['acf_csb_nonce'], 'acf_csb_save_meta' ) ) {
            return;
        }

        // 권한 확인
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // 자동 저장 방지
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // 코드 저장
        if ( isset( $_POST['acf_csb_code'] ) ) {
            update_post_meta( $post_id, '_acf_csb_code', wp_unslash( $_POST['acf_csb_code'] ) );
        }

        // 코드 타입
        if ( isset( $_POST['acf_csb_code_type'] ) ) {
            update_post_meta( $post_id, '_acf_csb_code_type', sanitize_text_field( $_POST['acf_csb_code_type'] ) );
        }

        // 활성화 상태
        update_post_meta( $post_id, '_acf_csb_active', isset( $_POST['acf_csb_active'] ) ? '1' : '0' );

        // 우선순위
        if ( isset( $_POST['acf_csb_priority'] ) ) {
            update_post_meta( $post_id, '_acf_csb_priority', absint( $_POST['acf_csb_priority'] ) );
        }

        // 설명
        if ( isset( $_POST['acf_csb_description'] ) ) {
            update_post_meta( $post_id, '_acf_csb_description', sanitize_textarea_field( $_POST['acf_csb_description'] ) );
        }

        // 트리거 조건
        if ( isset( $_POST['acf_csb_triggers'] ) ) {
            $triggers = array_map( 'sanitize_text_field', wp_unslash( $_POST['acf_csb_triggers'] ) );
            // user_roles는 배열
            if ( isset( $_POST['acf_csb_triggers']['user_roles'] ) ) {
                $triggers['user_roles'] = array_map( 'sanitize_text_field', $_POST['acf_csb_triggers']['user_roles'] );
            }
            update_post_meta( $post_id, '_acf_csb_triggers', $triggers );
        }

        // ACF CSS 연동
        update_post_meta( $post_id, '_acf_csb_use_css_vars', isset( $_POST['acf_csb_use_css_vars'] ) ? '1' : '0' );
    }

    /**
     * 관리자 에셋 로드
     */
    public function enqueue_admin_assets( $hook ) {
        global $post_type;

        if ( $post_type !== self::POST_TYPE ) {
            return;
        }

        // CodeMirror (WordPress 내장)
        $settings = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );

        if ( false !== $settings ) {
            wp_enqueue_script( 'acf-csb-editor', ACF_CSB_URL . 'assets/js/editor.js', array( 'jquery', 'wp-theme-plugin-editor' ), ACF_CSB_VERSION, true );
            wp_localize_script( 'acf-csb-editor', 'acfCsbEditor', array(
                'codeEditorSettings' => $settings,
                'nonce'              => wp_create_nonce( 'acf_csb_nonce' ),
                'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
            ) );
        }

        // 관리자 스타일
        wp_enqueue_style( 'acf-csb-admin', ACF_CSB_URL . 'assets/css/admin.css', array(), ACF_CSB_VERSION );
    }
}
