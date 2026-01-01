<?php
/**
 * ACF Code Snippets Box - Presets
 * 
 * 프리셋 코드 라이브러리
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Presets 클래스
 */
class ACF_CSB_Presets {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

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
        add_action( 'wp_ajax_acf_csb_get_presets', array( $this, 'ajax_get_presets' ) );
        add_action( 'wp_ajax_acf_csb_apply_preset', array( $this, 'ajax_apply_preset' ) );
    }

    /**
     * CSS 프리셋 목록
     */
    public static function get_css_presets() {
        return array(
            'smooth-scroll' => array(
                'name'        => __( '부드러운 스크롤', 'acf-code-snippets-box' ),
                'description' => __( '페이지 스크롤을 부드럽게 만듭니다.', 'acf-code-snippets-box' ),
                'category'    => 'ux',
                'code'        => "html {\n    scroll-behavior: smooth;\n}",
            ),
            'hide-scrollbar' => array(
                'name'        => __( '스크롤바 숨기기', 'acf-code-snippets-box' ),
                'description' => __( '스크롤바를 숨기면서 스크롤은 유지합니다.', 'acf-code-snippets-box' ),
                'category'    => 'ux',
                'code'        => "/* 스크롤바 숨기기 */\n::-webkit-scrollbar {\n    display: none;\n}\nbody {\n    -ms-overflow-style: none;\n    scrollbar-width: none;\n}",
            ),
            'text-selection' => array(
                'name'        => __( '텍스트 선택 스타일', 'acf-code-snippets-box' ),
                'description' => __( '텍스트 선택 시 색상을 변경합니다.', 'acf-code-snippets-box' ),
                'category'    => 'design',
                'code'        => "::selection {\n    background-color: var(--jj-primary-color, #0073aa);\n    color: #fff;\n}",
            ),
            'focus-outline' => array(
                'name'        => __( '접근성 포커스 스타일', 'acf-code-snippets-box' ),
                'description' => __( '키보드 포커스 시 명확한 아웃라인을 표시합니다.', 'acf-code-snippets-box' ),
                'category'    => 'accessibility',
                'code'        => ":focus-visible {\n    outline: 3px solid var(--jj-primary-color, #0073aa);\n    outline-offset: 2px;\n}",
            ),
            'button-hover' => array(
                'name'        => __( '버튼 호버 효과', 'acf-code-snippets-box' ),
                'description' => __( '버튼에 부드러운 호버 전환 효과를 추가합니다.', 'acf-code-snippets-box' ),
                'category'    => 'design',
                'code'        => "button, .btn, [type=\"submit\"] {\n    transition: all 0.3s ease;\n}\n\nbutton:hover, .btn:hover, [type=\"submit\"]:hover {\n    transform: translateY(-2px);\n    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);\n}",
            ),
            'image-hover-zoom' => array(
                'name'        => __( '이미지 호버 줌', 'acf-code-snippets-box' ),
                'description' => __( '이미지에 마우스를 올리면 확대됩니다.', 'acf-code-snippets-box' ),
                'category'    => 'design',
                'code'        => ".image-zoom {\n    overflow: hidden;\n}\n\n.image-zoom img {\n    transition: transform 0.5s ease;\n}\n\n.image-zoom:hover img {\n    transform: scale(1.1);\n}",
            ),
        );
    }

    /**
     * JavaScript 프리셋 목록
     */
    public static function get_js_presets() {
        return array(
            'back-to-top' => array(
                'name'        => __( '맨 위로 버튼', 'acf-code-snippets-box' ),
                'description' => __( '스크롤 시 나타나는 맨 위로 버튼을 추가합니다.', 'acf-code-snippets-box' ),
                'category'    => 'ux',
                'code'        => "// 맨 위로 버튼 생성\nconst backToTop = document.createElement('button');\nbackToTop.innerHTML = '↑';\nbackToTop.className = 'acf-csb-back-to-top';\nbackToTop.style.cssText = 'position:fixed;bottom:20px;right:20px;width:50px;height:50px;border-radius:50%;background:#333;color:#fff;border:none;cursor:pointer;opacity:0;transition:opacity 0.3s;z-index:9999;';\ndocument.body.appendChild(backToTop);\n\nwindow.addEventListener('scroll', () => {\n    backToTop.style.opacity = window.scrollY > 300 ? '1' : '0';\n});\n\nbackToTop.addEventListener('click', () => {\n    window.scrollTo({ top: 0, behavior: 'smooth' });\n});",
            ),
            'copy-code' => array(
                'name'        => __( '코드 복사 버튼', 'acf-code-snippets-box' ),
                'description' => __( '코드 블록에 복사 버튼을 추가합니다.', 'acf-code-snippets-box' ),
                'category'    => 'utility',
                'code'        => "// 코드 블록에 복사 버튼 추가\ndocument.querySelectorAll('pre code').forEach((block) => {\n    const button = document.createElement('button');\n    button.textContent = '복사';\n    button.className = 'copy-code-btn';\n    button.onclick = () => {\n        navigator.clipboard.writeText(block.textContent);\n        button.textContent = '복사됨!';\n        setTimeout(() => button.textContent = '복사', 2000);\n    };\n    block.parentNode.style.position = 'relative';\n    block.parentNode.appendChild(button);\n});",
            ),
            'external-links' => array(
                'name'        => __( '외부 링크 새 탭에서 열기', 'acf-code-snippets-box' ),
                'description' => __( '외부 링크를 자동으로 새 탭에서 엽니다.', 'acf-code-snippets-box' ),
                'category'    => 'ux',
                'code'        => "// 외부 링크 새 탭에서 열기\ndocument.querySelectorAll('a').forEach(link => {\n    if (link.hostname !== window.location.hostname && link.href.startsWith('http')) {\n        link.target = '_blank';\n        link.rel = 'noopener noreferrer';\n    }\n});",
            ),
            'lazy-load-images' => array(
                'name'        => __( '이미지 지연 로딩', 'acf-code-snippets-box' ),
                'description' => __( '뷰포트에 들어올 때 이미지를 로드합니다.', 'acf-code-snippets-box' ),
                'category'    => 'performance',
                'code'        => "// 이미지 지연 로딩\nif ('IntersectionObserver' in window) {\n    const imgObserver = new IntersectionObserver((entries) => {\n        entries.forEach(entry => {\n            if (entry.isIntersecting) {\n                const img = entry.target;\n                if (img.dataset.src) {\n                    img.src = img.dataset.src;\n                    img.removeAttribute('data-src');\n                    imgObserver.unobserve(img);\n                }\n            }\n        });\n    });\n    \n    document.querySelectorAll('img[data-src]').forEach(img => {\n        imgObserver.observe(img);\n    });\n}",
            ),
        );
    }

    /**
     * PHP 프리셋 목록
     */
    public static function get_php_presets() {
        return array(
            'disable-comments' => array(
                'name'        => __( '댓글 비활성화', 'acf-code-snippets-box' ),
                'description' => __( '사이트 전체에서 댓글을 비활성화합니다.', 'acf-code-snippets-box' ),
                'category'    => 'admin',
                'code'        => "// 댓글 비활성화\nadd_action('admin_init', function () {\n    // 관리자 메뉴에서 댓글 제거\n    remove_menu_page('edit-comments.php');\n});\n\nadd_action('init', function () {\n    // 댓글 지원 제거\n    remove_post_type_support('post', 'comments');\n    remove_post_type_support('page', 'comments');\n});\n\nadd_filter('comments_open', '__return_false', 20, 2);\nadd_filter('pings_open', '__return_false', 20, 2);\nadd_filter('comments_array', '__return_empty_array', 10, 2);",
            ),
            'remove-wp-version' => array(
                'name'        => __( 'WordPress 버전 숨기기', 'acf-code-snippets-box' ),
                'description' => __( '보안을 위해 WordPress 버전을 숨깁니다.', 'acf-code-snippets-box' ),
                'category'    => 'security',
                'code'        => "// WordPress 버전 숨기기\nremove_action('wp_head', 'wp_generator');\nadd_filter('the_generator', '__return_empty_string');",
            ),
            'custom-login-logo' => array(
                'name'        => __( '로그인 로고 변경', 'acf-code-snippets-box' ),
                'description' => __( '로그인 페이지의 WordPress 로고를 사이트 로고로 변경합니다.', 'acf-code-snippets-box' ),
                'category'    => 'branding',
                'code'        => "// 로그인 페이지 로고 변경\nadd_action('login_enqueue_scripts', function() {\n    \$logo_url = get_site_icon_url();\n    if (\$logo_url) {\n        echo '<style>\n            #login h1 a {\n                background-image: url(' . esc_url(\$logo_url) . ') !important;\n                background-size: contain !important;\n                width: 100% !important;\n            }\n        </style>';\n    }\n});\n\nadd_filter('login_headerurl', function() {\n    return home_url();\n});\n\nadd_filter('login_headertext', function() {\n    return get_bloginfo('name');\n});",
            ),
            'disable-xmlrpc' => array(
                'name'        => __( 'XML-RPC 비활성화', 'acf-code-snippets-box' ),
                'description' => __( '보안을 위해 XML-RPC를 비활성화합니다.', 'acf-code-snippets-box' ),
                'category'    => 'security',
                'code'        => "// XML-RPC 비활성화\nadd_filter('xmlrpc_enabled', '__return_false');\nadd_filter('wp_headers', function(\$headers) {\n    unset(\$headers['X-Pingback']);\n    return \$headers;\n});",
            ),
            'admin-footer-text' => array(
                'name'        => __( '관리자 푸터 텍스트', 'acf-code-snippets-box' ),
                'description' => __( '관리자 페이지 하단 텍스트를 변경합니다.', 'acf-code-snippets-box' ),
                'category'    => 'branding',
                'code'        => "// 관리자 푸터 텍스트 변경\nadd_filter('admin_footer_text', function() {\n    return '© ' . date('Y') . ' ' . get_bloginfo('name') . ' - Powered by <a href=\"https://3j-labs.com\">3J Labs</a>';\n});",
            ),
        );
    }

    /**
     * 모든 프리셋 가져오기
     */
    public static function get_all_presets() {
        return array(
            'css' => self::get_css_presets(),
            'js'  => self::get_js_presets(),
            'php' => self::get_php_presets(),
        );
    }

    /**
     * AJAX: 프리셋 목록 반환
     */
    public function ajax_get_presets() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-code-snippets-box' ) );
        }

        $type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'all';

        if ( $type === 'all' ) {
            wp_send_json_success( self::get_all_presets() );
        }

        $presets = array();
        switch ( $type ) {
            case 'css':
                $presets = self::get_css_presets();
                break;
            case 'js':
                $presets = self::get_js_presets();
                break;
            case 'php':
                $presets = self::get_php_presets();
                break;
        }

        wp_send_json_success( $presets );
    }

    /**
     * AJAX: 프리셋 적용
     */
    public function ajax_apply_preset() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-code-snippets-box' ) );
        }

        $preset_id = isset( $_POST['preset_id'] ) ? sanitize_text_field( $_POST['preset_id'] ) : '';
        $type      = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';

        if ( empty( $preset_id ) || empty( $type ) ) {
            wp_send_json_error( __( '잘못된 요청입니다.', 'acf-code-snippets-box' ) );
        }

        $presets = array();
        switch ( $type ) {
            case 'css':
                $presets = self::get_css_presets();
                break;
            case 'js':
                $presets = self::get_js_presets();
                break;
            case 'php':
                $presets = self::get_php_presets();
                break;
        }

        if ( ! isset( $presets[ $preset_id ] ) ) {
            wp_send_json_error( __( '프리셋을 찾을 수 없습니다.', 'acf-code-snippets-box' ) );
        }

        wp_send_json_success( array(
            'code' => $presets[ $preset_id ]['code'],
            'name' => $presets[ $preset_id ]['name'],
        ) );
    }
}
