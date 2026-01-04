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
     * [v2.3.3] 프리셋 토글 기능 추가
     */
    public function init() {
        add_action( 'wp_ajax_acf_csb_get_presets', array( $this, 'ajax_get_presets' ) );
        add_action( 'wp_ajax_acf_csb_apply_preset', array( $this, 'ajax_apply_preset' ) );
        add_action( 'wp_ajax_acf_csb_check_preset_exists', array( $this, 'ajax_check_preset_exists' ) );
        add_action( 'wp_ajax_acf_csb_create_preset_snippet', array( $this, 'ajax_create_preset_snippet' ) );
        add_action( 'wp_ajax_acf_csb_toggle_preset', array( $this, 'ajax_toggle_preset' ) );
    }
    
    /**
     * AJAX: 프리셋 존재 여부 확인
     * [v2.3.3] 기존 스니펫이 있는지 확인
     */
    public function ajax_check_preset_exists() {
        $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
        if ( ! wp_verify_nonce( $nonce, 'acf_csb_nonce' ) ) {
            wp_send_json_error( __( '보안 검증에 실패했습니다.', 'acf-code-snippets-box' ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-code-snippets-box' ) );
        }
        
        $preset_type = isset( $_POST['preset_type'] ) ? sanitize_text_field( $_POST['preset_type'] ) : '';
        $preset_id = isset( $_POST['preset_id'] ) ? sanitize_text_field( $_POST['preset_id'] ) : '';
        
        if ( empty( $preset_type ) || empty( $preset_id ) ) {
            wp_send_json_error( __( '잘못된 요청입니다.', 'acf-code-snippets-box' ) );
        }
        
        // 프리셋 ID로 기존 스니펫 검색
        $existing = get_posts( array(
            'post_type'      => 'acf_code_snippet',
            'meta_key'       => '_acf_csb_preset_id',
            'meta_value'     => $preset_id,
            'meta_compare'   => '=',
            'posts_per_page' => 1,
            'post_status'    => 'any',
        ) );
        
        if ( ! empty( $existing ) ) {
            wp_send_json_success( array(
                'exists'  => true,
                'post_id' => $existing[0]->ID,
                'title'   => $existing[0]->post_title,
            ) );
        }
        
        wp_send_json_success( array( 'exists' => false ) );
    }
    
    /**
     * AJAX: 프리셋 스니펫 생성
     * [v2.3.3] 프리셋으로 새 스니펫 생성
     */
    public function ajax_create_preset_snippet() {
        $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
        if ( ! wp_verify_nonce( $nonce, 'acf_csb_nonce' ) ) {
            wp_send_json_error( __( '보안 검증에 실패했습니다.', 'acf-code-snippets-box' ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-code-snippets-box' ) );
        }
        
        $preset_type = isset( $_POST['preset_type'] ) ? sanitize_text_field( $_POST['preset_type'] ) : '';
        $preset_id = isset( $_POST['preset_id'] ) ? sanitize_text_field( $_POST['preset_id'] ) : '';
        
        if ( empty( $preset_type ) || empty( $preset_id ) ) {
            wp_send_json_error( __( '잘못된 요청입니다.', 'acf-code-snippets-box' ) );
        }
        
        // 프리셋 데이터 가져오기
        $presets = array();
        switch ( $preset_type ) {
            case 'css':
                $presets = self::get_css_presets();
                break;
            case 'js':
                $presets = self::get_js_presets();
                break;
            case 'php':
                $presets = self::get_php_presets();
                break;
            case 'woocommerce_php':
                $presets = self::get_woocommerce_php_presets();
                break;
            case 'woocommerce_css':
                $presets = self::get_woocommerce_css_presets();
                break;
            case 'utility':
                $presets = self::get_utility_presets();
                break;
        }
        
        if ( ! isset( $presets[ $preset_id ] ) ) {
            wp_send_json_error( __( '프리셋을 찾을 수 없습니다.', 'acf-code-snippets-box' ) );
        }
        
        $preset = $presets[ $preset_id ];
        
        // 새 스니펫 생성
        $post_id = wp_insert_post( array(
            'post_title'   => $preset['name'],
            'post_type'    => 'acf_code_snippet',
            'post_status'  => 'draft', // 초기 비활성화 상태
        ) );
        
        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( $post_id->get_error_message() );
        }
        
        // 메타 데이터 저장
        update_post_meta( $post_id, '_acf_csb_code', $preset['code'] );
        update_post_meta( $post_id, '_acf_csb_code_type', $preset_type );
        update_post_meta( $post_id, '_acf_csb_preset_id', $preset_id );
        update_post_meta( $post_id, '_acf_csb_preset_type', $preset_type );
        update_post_meta( $post_id, '_acf_csb_enabled', '0' ); // 초기 비활성화
        
        wp_send_json_success( array( 'post_id' => $post_id ) );
    }
    
    /**
     * AJAX: 프리셋 토글 (활성화/비활성화)
     * [v2.3.3] 토글 방식으로 프리셋 활성화/비활성화
     */
    public function ajax_toggle_preset() {
        $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
        if ( ! wp_verify_nonce( $nonce, 'acf_csb_nonce' ) ) {
            wp_send_json_error( __( '보안 검증에 실패했습니다.', 'acf-code-snippets-box' ) );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-code-snippets-box' ) );
        }
        
        $preset_id = isset( $_POST['preset_id'] ) ? sanitize_text_field( $_POST['preset_id'] ) : '';
        $action = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : 'toggle'; // 'toggle', 'enable', 'disable'
        
        if ( empty( $preset_id ) ) {
            wp_send_json_error( __( '프리셋 ID가 필요합니다.', 'acf-code-snippets-box' ) );
        }
        
        // 프리셋 ID로 스니펫 찾기
        $snippets = get_posts( array(
            'post_type'      => 'acf_code_snippet',
            'meta_key'       => '_acf_csb_preset_id',
            'meta_value'     => $preset_id,
            'posts_per_page' => 1,
            'post_status'    => 'any',
        ) );
        
        if ( empty( $snippets ) ) {
            // 스니펫이 없으면 생성
            $preset_type = isset( $_POST['preset_type'] ) ? sanitize_text_field( $_POST['preset_type'] ) : 'css';
            $presets = array();
            switch ( $preset_type ) {
                case 'css': $presets = self::get_css_presets(); break;
                case 'js': $presets = self::get_js_presets(); break;
                case 'php': $presets = self::get_php_presets(); break;
            }
            
            if ( ! isset( $presets[ $preset_id ] ) ) {
                wp_send_json_error( __( '프리셋을 찾을 수 없습니다.', 'acf-code-snippets-box' ) );
            }
            
            $preset = $presets[ $preset_id ];
            $post_id = wp_insert_post( array(
                'post_title'   => $preset['name'],
                'post_type'    => 'acf_code_snippet',
                'post_status'  => 'publish',
            ) );
            
            if ( is_wp_error( $post_id ) ) {
                wp_send_json_error( $post_id->get_error_message() );
            }
            
            update_post_meta( $post_id, '_acf_csb_code', $preset['code'] );
            update_post_meta( $post_id, '_acf_csb_code_type', $preset_type );
            update_post_meta( $post_id, '_acf_csb_preset_id', $preset_id );
            update_post_meta( $post_id, '_acf_csb_enabled', '1' );
            
            wp_send_json_success( array( 
                'post_id' => $post_id,
                'enabled' => true,
                'message' => __( '프리셋이 활성화되었습니다.', 'acf-code-snippets-box' )
            ) );
        } else {
            // 기존 스니펫 토글
            $snippet = $snippets[0];
            $current_status = get_post_meta( $snippet->ID, '_acf_csb_enabled', true );
            
            if ( $action === 'toggle' ) {
                $new_status = ( $current_status === '1' ) ? '0' : '1';
            } elseif ( $action === 'enable' ) {
                $new_status = '1';
            } else {
                $new_status = '0';
            }
            
            update_post_meta( $snippet->ID, '_acf_csb_enabled', $new_status );
            
            wp_send_json_success( array(
                'post_id' => $snippet->ID,
                'enabled' => ( $new_status === '1' ),
                'message' => ( $new_status === '1' ) ? 
                    __( '프리셋이 활성화되었습니다.', 'acf-code-snippets-box' ) : 
                    __( '프리셋이 비활성화되었습니다.', 'acf-code-snippets-box' )
            ) );
        }
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
            // 스크린샷에서 발견한 추가 프리셋
            'korean-word-break' => array(
                'name'        => __( '한글 단어 단위 줄바꿈', 'acf-code-snippets-box' ),
                'description' => __( '한글 텍스트가 단어 단위로 줄바꿈됩니다.', 'acf-code-snippets-box' ),
                'category'    => 'typography',
                'code'        => "/* 한글 단어 단위 줄바꿈 */\np, li, td, th, span, div, h1, h2, h3, h4, h5, h6 {\n    word-break: keep-all;\n    overflow-wrap: break-word;\n    word-wrap: break-word;\n}",
            ),
            'slider-visibility' => array(
                'name'        => __( '슬라이더 가시성 설정', 'acf-code-snippets-box' ),
                'description' => __( '슬라이더 초기 로딩 시 깜빡임을 방지합니다.', 'acf-code-snippets-box' ),
                'category'    => 'ux',
                'code'        => "/* 슬라이더 FOUC 방지 */\n.slider-container {\n    opacity: 0;\n    transition: opacity 0.3s ease;\n}\n\n.slider-container.initialized,\n.slider-container.slick-initialized,\n.slider-container.swiper-initialized {\n    opacity: 1;\n}",
            ),
            'menu-icon-size' => array(
                'name'        => __( '메뉴 아이콘 크기 설정', 'acf-code-snippets-box' ),
                'description' => __( '메뉴 아이콘의 크기를 CSS 변수로 제어합니다.', 'acf-code-snippets-box' ),
                'category'    => 'design',
                'code'        => "/* 메뉴 아이콘 크기 설정 */\n:root {\n    --menu-icon-size: 20px;\n}\n\n.menu-item .menu-icon,\n.kadence-svg-iconset,\n.wp-block-navigation-item__icon {\n    width: var(--menu-icon-size) !important;\n    height: var(--menu-icon-size) !important;\n}\n\n.menu-item svg {\n    width: var(--menu-icon-size);\n    height: var(--menu-icon-size);\n}",
            ),
            'review-star-style' => array(
                'name'        => __( '리뷰 별점 스타일', 'acf-code-snippets-box' ),
                'description' => __( '사이트 리뷰 별점의 색상과 크기를 설정합니다.', 'acf-code-snippets-box' ),
                'category'    => 'design',
                'code'        => "/* 리뷰 별점 스타일 */\n:root {\n    --star-color: #FFB900;\n    --star-size: 16px;\n    --star-empty-color: #ddd;\n}\n\n.star-rating,\n.woocommerce-product-rating .star-rating {\n    color: var(--star-color);\n    font-size: var(--star-size);\n}\n\n.star-rating::before {\n    color: var(--star-empty-color);\n}\n\n/* Site Reviews 플러그인 호환 */\n.glsr-review .glsr-star {\n    font-size: var(--star-size);\n    color: var(--star-color);\n}\n\n.glsr-review .glsr-star-empty {\n    color: var(--star-empty-color);\n}",
            ),
            'card-hover-lift' => array(
                'name'        => __( '카드 호버 부상 효과', 'acf-code-snippets-box' ),
                'description' => __( '카드 요소에 마우스를 올리면 위로 떠오릅니다.', 'acf-code-snippets-box' ),
                'category'    => 'design',
                'code'        => "/* 카드 호버 부상 효과 */\n.card, .product, .post-card, .entry {\n    transition: transform 0.3s ease, box-shadow 0.3s ease;\n}\n\n.card:hover, .product:hover, .post-card:hover, .entry:hover {\n    transform: translateY(-5px);\n    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);\n}",
            ),
            'gradient-text' => array(
                'name'        => __( '그라데이션 텍스트', 'acf-code-snippets-box' ),
                'description' => __( '텍스트에 그라데이션 효과를 적용합니다.', 'acf-code-snippets-box' ),
                'category'    => 'design',
                'code'        => "/* 그라데이션 텍스트 */\n.gradient-text {\n    background: linear-gradient(135deg, var(--jj-primary-color, #667eea) 0%, var(--jj-secondary-color, #764ba2) 100%);\n    -webkit-background-clip: text;\n    -webkit-text-fill-color: transparent;\n    background-clip: text;\n}",
            ),
            'mobile-tap-highlight' => array(
                'name'        => __( '모바일 탭 하이라이트 제거', 'acf-code-snippets-box' ),
                'description' => __( '모바일에서 터치 시 나타나는 하이라이트를 제거합니다.', 'acf-code-snippets-box' ),
                'category'    => 'ux',
                'code'        => "/* 모바일 탭 하이라이트 제거 */\n* {\n    -webkit-tap-highlight-color: transparent;\n}\n\na, button, input, select, textarea {\n    -webkit-tap-highlight-color: transparent;\n    outline: none;\n}",
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
            'wc-minicart-ui-fix' => array(
                'name'        => __( 'RealDeal 미니카트 UI 최종 해결사 v15.0', 'acf-code-snippets-box' ),
                'description' => __( '미니카트의 내용물 과다 문제 해결 및 번역어 교체 (저장→절약).', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => false,
                'code'        => "jQuery(function($) {\n    function runFinalFix() {\n        const slideoutContainer = $('#generate-slideout-menu');\n        if (!slideoutContainer.length) return;\n\n        // 1. 내용물 과다 문제 해결 (외과수술)\n        const cartItems = slideoutContainer.find('.woocommerce-mini-cart-item');\n        cartItems.each(function() {\n            const item = $(this);\n            const productNameElement = item.find('.product-name');\n            if (productNameElement.length) {\n                const productLink = productNameElement.find('a').first();\n                if (productLink.length) {\n                    productNameElement.html(productLink);\n                }\n            }\n        });\n\n        // 2. '저장' -> '절약' 번역 문제 해결\n        slideoutContainer.find('*').contents().filter(function() {\n            return this.nodeType === 3 && this.nodeValue.includes('저장');\n        }).each(function() {\n            this.nodeValue = this.nodeValue.replace(/저장/g, '절약');\n        });\n    }\n\n    $(document.body).on('added_to_cart', function() {\n        setTimeout(runFinalFix, 150);\n    });\n\n    $(document.body).on('qaac_added_to_cart', function() {\n        setTimeout(runFinalFix, 150);\n    });\n    \n    $(document).on('click', '.slideout-toggle', function() {\n        setTimeout(runFinalFix, 250);\n    });\n\n    const observer = new MutationObserver(function() {\n        runFinalFix();\n    });\n    const targetNode = document.getElementById('generate-slideout-menu');\n    if (targetNode) {\n        observer.observe(targetNode, {\n            childList: true,\n            subtree: true\n        });\n    }\n\n    setTimeout(runFinalFix, 500);\n});",
            ),
            'wc-variation-selector' => array(
                'name'        => __( '옵션상품 변형 선택 기능', 'acf-code-snippets-box' ),
                'description' => __( 'WooCommerce 변형 상품의 옵션 선택 및 AJAX 장바구니 추가 기능.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => false,
                'code'        => "jQuery(document).ready(function($) {\n    // 옵션상품 변형 선택 기능\n    $('.variations_form').each(function() {\n        var $form = $(this);\n        var $variations = $form.find('.variations select');\n        var $variation_id = $form.find('input[name=\"variation_id\"]');\n        var $button = $form.find('.woo-add-to-cart');\n        var $price = $form.find('.woocommerce-variations-price .price');\n        var $availability = $form.find('.woocommerce-variation-availability');\n        var $description = $form.find('.woocommerce-variation-description');\n        \n        $variations.on('change', function() {\n            var allSelected = true;\n            var variationData = {};\n            \n            $variations.each(function() {\n                var $select = $(this);\n                var attribute = $select.attr('name');\n                var value = $select.val();\n                \n                if (!value) {\n                    allSelected = false;\n                } else {\n                    variationData[attribute] = value;\n                }\n            });\n            \n            if (allSelected) {\n                $.ajax({\n                    url: wc_add_to_cart_params.ajax_url,\n                    type: 'POST',\n                    data: {\n                        action: 'woo_get_variation',\n                        product_id: $form.data('product_id'),\n                        variation_data: variationData,\n                        nonce: $form.closest('.woo-add-to-cart-ajax-wrapper').find('input[name=\"woocommerce-cart-nonce\"]').val() || $form.closest('form').find('input[name=\"woocommerce-cart-nonce\"]').val()\n                    },\n                    success: function(response) {\n                        if (response.success) {\n                            var variation = response.data;\n                            $variation_id.val(variation.variation_id);\n                            $price.html(variation.price_html);\n                            $availability.html(variation.availability_html);\n                            $description.html(variation.description);\n                            \n                            if (variation.is_purchasable && variation.is_in_stock) {\n                                $button.prop('disabled', false);\n                            } else {\n                                $button.prop('disabled', true);\n                            }\n                        } else {\n                            console.log('Variation error:', response.data);\n                            $variation_id.val(0);\n                            $price.html('');\n                            $availability.html('');\n                            $description.html('');\n                            $button.prop('disabled', true);\n                        }\n                    },\n                    error: function() {\n                        console.log('AJAX error');\n                        $variation_id.val(0);\n                        $price.html('');\n                        $availability.html('');\n                        $description.html('');\n                        $button.prop('disabled', true);\n                    }\n                });\n            } else {\n                $variation_id.val(0);\n                $price.html('');\n                $availability.html('');\n                $description.html('');\n                $button.prop('disabled', true);\n            }\n        });\n        \n        $form.find('.reset_variations').on('click', function(e) {\n            e.preventDefault();\n            $variations.val('').trigger('change');\n        });\n    });\n    \n    // AJAX 장바구니 추가\n    $('.woo-add-to-cart-ajax-wrapper .woo-add-to-cart').on('click', function(e) {\n        e.preventDefault();\n        \n        var $button = $(this);\n        var $form = $button.closest('.woo-add-to-cart-ajax-wrapper');\n        var productId = $button.data('product_id');\n        var quantity = $form.find('input[name=\"quantity\"]').val() || 1;\n        var variationId = $form.find('input[name=\"variation_id\"]').val() || 0;\n        var variationData = {};\n        \n        $form.find('.variations select').each(function() {\n            var $select = $(this);\n            var attribute = $select.attr('name');\n            var value = $select.val();\n            if (value) {\n                variationData[attribute] = value;\n            }\n        });\n        \n        $.ajax({\n            url: wc_add_to_cart_params.ajax_url,\n            type: 'POST',\n            data: {\n                action: 'woo_add_to_cart_ajax',\n                product_id: productId,\n                quantity: quantity,\n                variation_id: variationId,\n                variation: variationData,\n                nonce: $form.find('input[name=\"woocommerce-cart-nonce\"]').val()\n            },\n            success: function(response) {\n                if (response.success) {\n                    $(document.body).trigger('added_to_cart', [response.data.fragments, response.data.cart_hash]);\n                    $button.text('추가됨').addClass('added');\n                    \n                    setTimeout(function() {\n                        $button.prop('disabled', false).text($button.data('original-text') || '장바구니').removeClass('added');\n                    }, 2000);\n                } else {\n                    alert(response.data.error || 'Error adding to cart');\n                    $button.prop('disabled', false).text($button.data('original-text') || '장바구니');\n                }\n            },\n            error: function() {\n                alert('장바구니 추가에 실패했습니다');\n                $button.prop('disabled', false).text($button.data('original-text') || '장바구니');\n            }\n        });\n    });\n});",
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
     * WooCommerce 전용 PHP 프리셋 목록
     * Pro 버전 이상 사용자 전용
     */
    public static function get_woocommerce_php_presets() {
        return array(
            'wc-discount-calculator' => array(
                'name'        => __( '상품 할인율 자동 계산기', 'acf-code-snippets-box' ),
                'description' => __( '상품 편집 화면에서 퍼센트/금액 기반 할인을 실시간으로 계산합니다.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => true,
                'code'        => "<?php\n// 상품 편집 메타박스에 할인 계산기 추가\nadd_action('woocommerce_product_options_pricing', 'acf_csb_add_discount_calculator');\nfunction acf_csb_add_discount_calculator() {\n    ?>\n    <div class=\"options_group pricing show_if_simple\">\n        <?php\n        // 할부 개월 수 설정\n        woocommerce_wp_select([\n            'id' => '_installment_months',\n            'label' => __('할부 개월 수', 'acf-code-snippets-box'),\n            'options' => [\n                '1' => '일시불',\n                '3' => '3개월',\n                '6' => '6개월',\n                '12' => '12개월',\n                '24' => '24개월'\n            ],\n            'desc_tip' => true,\n            'description' => __('정가와 할인가 모두에 적용됩니다', 'acf-code-snippets-box')\n        ]);\n        ?>\n        \n        <!-- 할인 계산기 섹션 -->\n        <div class=\"discount-calculator\" style=\"border: 1px solid #ddd; padding: 10px; margin: 10px 0;\">\n            <h4><?php esc_html_e('할인 계산기', 'acf-code-snippets-box'); ?></h4>\n            <p>\n                <label><?php esc_html_e('할인 적용:', 'acf-code-snippets-box'); ?></label>\n                <input type=\"number\" id=\"discount_percent\" placeholder=\"%\" style=\"width: 60px;\">\n                <button type=\"button\" class=\"button apply-percent-discount\"><?php esc_html_e('% 적용', 'acf-code-snippets-box'); ?></button>\n                <span style=\"margin: 0 10px;\"><?php esc_html_e('또는', 'acf-code-snippets-box'); ?></span>\n                <input type=\"number\" id=\"discount_amount\" placeholder=\"<?php esc_attr_e('원', 'acf-code-snippets-box'); ?>\" style=\"width: 100px;\">\n                <button type=\"button\" class=\"button apply-amount-discount\"><?php esc_html_e('금액 차감', 'acf-code-snippets-box'); ?></button>\n            </p>\n            <div id=\"discount-preview\" style=\"background: #f5f5f5; padding: 8px; margin-top: 10px; display: none;\">\n                <strong><?php esc_html_e('계산 결과:', 'acf-code-snippets-box'); ?></strong>\n                <span id=\"preview-text\"></span>\n            </div>\n        </div>\n    </div>\n    <?php\n}",
            ),
            'wc-price-engine' => array(
                'name'        => __( '가격 계산 엔진 & 숏코드', 'acf-code-snippets-box' ),
                'description' => __( '할인율, 절약금액, 할부가격을 계산하는 핵심 엔진과 모듈형 숏코드를 제공합니다.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => true,
                'code'        => "<?php\n/**\n * 가격 계산 엔진 & 숏코드\n * [realdeal_price] - 통합 가격 표시\n * [rd_badge] - 할인 배지\n * [rd_summary] - 절약 금액\n * [rd_installments] - 할부 정보\n */\n\n// 핵심 계산 엔진\nif ( ! function_exists( 'acf_csb_get_price_data' ) ) {\n    function acf_csb_get_price_data( \$product ) {\n        if ( ! is_a( \$product, 'WC_Product' ) ) return null;\n        \$data = [\n            'regular_price'       => (float) \$product->get_regular_price(),\n            'sale_price'          => (float) \$product->get_sale_price(),\n            'is_on_sale'          => \$product->is_on_sale(),\n            'saved_amount'        => 0,\n            'discount_percentage' => 0,\n            'installment_months'  => (int) get_post_meta( \$product->get_id(), 'installment_months', true ),\n            'installment_price'   => 0,\n        ];\n        if ( \$data['is_on_sale'] && ! empty( \$data['sale_price'] ) && \$data['regular_price'] > \$data['sale_price'] ) {\n            \$data['saved_amount'] = \$data['regular_price'] - \$data['sale_price'];\n            if (\$data['regular_price'] > 0) {\n                \$data['discount_percentage'] = round( ( \$data['saved_amount'] / \$data['regular_price'] ) * 100 );\n            }\n        }\n        if ( \$data['installment_months'] > 0 ) {\n            \$price_for_installment = \$data['is_on_sale'] && !empty(\$data['sale_price']) ? \$data['sale_price'] : \$data['regular_price'];\n            if ( \$price_for_installment > 0 ) {\n                \$data['installment_price'] = round( ( \$price_for_installment / \$data['installment_months'] ), -2 );\n            }\n        }\n        return \$data;\n    }\n}\n\n// 통합 숏코드 [realdeal_price]\nif ( ! shortcode_exists( 'realdeal_price' ) ) {\n    add_shortcode( 'realdeal_price', function() {\n        global \$product;\n        if ( ! \$product ) \$product = wc_get_product( get_the_ID() );\n        if ( ! \$product ) return '';\n        \$data = acf_csb_get_price_data( \$product );\n        if ( !\$data ) return '';\n        \$badge_html = \$data['discount_percentage'] > 0 ? '<span class=\"realdeal-discount-badge\">' . \$data['discount_percentage'] . '% OFF</span>' : '';\n        \$summary_html = \$data['saved_amount'] > 0 ? '<div class=\"realdeal-discount-summary\">✨ ' . wp_strip_all_tags( wc_price( \$data['saved_amount'] ) ) . ' 절약</div>' : '';\n        \$installments_html = \$data['installment_price'] > 0 ? '<br><small class=\"realdeal-installment-price\">(월 ' . number_format_i18n( \$data['installment_price'] ) . '원 / ' . \$data['installment_months'] . '개월)</small>' : '';\n        if ( \$data['is_on_sale'] ) {\n            \$price_html = '<del>' . wc_price( \$data['regular_price'] ) . '</del> <ins>' . wc_price( \$data['sale_price'] ) . '</ins>';\n        } else {\n            \$price_html = '<ins>' . wc_price( \$data['regular_price'] ) . '</ins>';\n        }\n        return sprintf('<div class=\"realdeal-price-wrapper price\">%s%s%s%s</div>', \$badge_html, \$summary_html, \$price_html, \$installments_html);\n    });\n}",
            ),
            'wc-quick-edit-fields' => array(
                'name'        => __( '빠른 편집 필드 확장', 'acf-code-snippets-box' ),
                'description' => __( '상품 목록의 빠른 편집에 할인가격과 할부 개월 수 필드를 추가합니다.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => true,
                'code'        => "<?php\n// '빠른 편집' 클릭 시 보일 필드들 추가\nadd_action( 'woocommerce_product_quick_edit_end', 'acf_csb_add_quick_edit_fields' );\nfunction acf_csb_add_quick_edit_fields() {\n    wp_nonce_field( 'acf_csb_quick_edit_nonce', 'acf_csb_quick_edit_nonce_field' );\n    ?>\n    <div class=\"quick-edit-custom-fields\">\n        <h4><?php esc_html_e('가격 상세 설정', 'acf-code-snippets-box'); ?></h4>\n        <label>\n            <span class=\"title\"><?php esc_html_e('할인 가격', 'acf-code-snippets-box'); ?></span>\n            <span class=\"input-text-wrap\">\n                <input type=\"text\" name=\"_sale_price\" class=\"wc_input_price\" placeholder=\"<?php esc_attr_e('할인 가격', 'acf-code-snippets-box'); ?>\">\n            </span>\n        </label>\n        <label>\n            <span class=\"title\"><?php esc_html_e('할부 개월 수', 'acf-code-snippets-box'); ?></span>\n            <span class=\"input-text-wrap\">\n                <input type=\"number\" name=\"installment_months\" placeholder=\"<?php esc_attr_e('개월 수 (숫자만)', 'acf-code-snippets-box'); ?>\">\n            </span>\n        </label>\n    </div>\n    <?php\n}\n\n// '빠른 편집' 저장 시 데이터 처리\nadd_action( 'woocommerce_product_quick_edit_save', 'acf_csb_save_quick_edit_fields' );\nfunction acf_csb_save_quick_edit_fields( \$product ) {\n    if ( ! isset( \$_POST['acf_csb_quick_edit_nonce_field'] ) || ! wp_verify_nonce( \$_POST['acf_csb_quick_edit_nonce_field'], 'acf_csb_quick_edit_nonce' ) ) return;\n    if ( isset( \$_POST['_sale_price'] ) ) {\n        \$product->set_sale_price( wc_clean( \$_POST['_sale_price'] ) );\n    }\n    if ( isset( \$_POST['installment_months'] ) ) {\n        \$product->update_meta_data( 'installment_months', absint( \$_POST['installment_months'] ) );\n    }\n    \$product->save();\n}",
            ),
            'wc-cart-cleanup' => array(
                'name'        => __( '장바구니 UI 정리', 'acf-code-snippets-box' ),
                'description' => __( '장바구니/미니카트의 상품명 영역에서 불필요한 요소를 제거합니다.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => true,
                'code'        => "<?php\n// 장바구니 UI 정리 필터\nadd_filter( 'woocommerce_cart_item_name', 'acf_csb_cleanup_cart_item_name', 100, 3 );\nfunction acf_csb_cleanup_cart_item_name( \$product_name, \$cart_item, \$cart_item_key ) {\n    if ( is_cart() || is_checkout() || ( defined('WOOCOMMERCE_CART') && WOOCOMMERCE_CART ) ) {\n        \$_product = apply_filters( 'woocommerce_cart_item_product', \$cart_item['data'], \$cart_item, \$cart_item_key );\n        \$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', \$_product->is_visible() ? \$_product->get_permalink( \$cart_item ) : '', \$cart_item, \$cart_item_key );\n        if ( \$product_permalink ) {\n            return sprintf( '<a href=\"%s\">%s</a>', esc_url( \$product_permalink ), \$_product->get_name() );\n        } else {\n            return \$_product->get_name();\n        }\n    }\n    return \$product_name;\n}",
            ),
            'wc-translation-fix' => array(
                'name'        => __( '번역 오류 수정 (저장→절약)', 'acf-code-snippets-box' ),
                'description' => __( 'Saved가 저장으로 잘못 번역된 것을 절약으로 교정합니다.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => false,
                'code'        => "<?php\n// 번역 오류 수정\nadd_filter( 'gettext', 'acf_csb_fix_weird_translation', 20, 3 );\nfunction acf_csb_fix_weird_translation( \$translated_text, \$text, \$domain ) {\n    if ( 'Saved' === \$text && '저장' === \$translated_text ) {\n        \$translated_text = '절약';\n    }\n    return \$translated_text;\n}",
            ),
            'wc-product-edit-installment-calculator' => array(
                'name'        => __( '상품 편집: 할부 개월 수 및 할인 계산기', 'acf-code-snippets-box' ),
                'description' => __( '상품 편집 메타박스에 할부 개월 수 설정과 할인 계산기를 추가합니다.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => false,
                'code'        => "<?php\n// 상품 편집 메타박스에 추가할 필드들\nadd_action('woocommerce_product_options_pricing', 'add_advanced_pricing_fields');\nfunction add_advanced_pricing_fields() {\n    ?>\n    <div class=\"options_group pricing show_if_simple\">\n        <?php\n        // 할부 개월 수 설정\n        woocommerce_wp_select([\n            'id' => '_installment_months',\n            'label' => __('할부 개월 수', 'realdeal'),\n            'options' => [\n                '1' => '일시불',\n                '3' => '3개월',\n                '6' => '6개월',\n                '12' => '12개월',\n                '24' => '24개월'\n            ],\n            'desc_tip' => true,\n            'description' => __('정가와 할인가 모두에 적용됩니다', 'realdeal')\n        ]);\n        ?>\n        \n        <!-- 할인 계산기 섹션 -->\n        <div class=\"discount-calculator\" style=\"border: 1px solid #ddd; padding: 10px; margin: 10px 0;\">\n            <h4>할인 계산기</h4>\n            <p>\n                <label>할인 적용:</label>\n                <input type=\"number\" id=\"discount_percent\" placeholder=\"%\" style=\"width: 60px;\">\n                <button type=\"button\" class=\"button apply-percent-discount\">% 적용</button>\n                <span style=\"margin: 0 10px;\">또는</span>\n                <input type=\"number\" id=\"discount_amount\" placeholder=\"원\" style=\"width: 100px;\">\n                <button type=\"button\" class=\"button apply-amount-discount\">금액 차감</button>\n            </p>\n            <div id=\"discount-preview\" style=\"background: #f5f5f5; padding: 8px; margin-top: 10px; display: none;\">\n                <strong>계산 결과:</strong>\n                <span id=\"preview-text\"></span>\n            </div>\n        </div>\n    </div>\n    \n    <script>\n    jQuery(document).ready(function(\$) {\n        // 퍼센트 할인 적용\n        \$('.apply-percent-discount').click(function() {\n            var regular = parseFloat(\$('#_regular_price').val());\n            var percent = parseFloat(\$('#discount_percent').val());\n            \n            if (regular && percent) {\n                var discount_amount = regular * (percent / 100);\n                var sale_price = regular - discount_amount;\n                \n                \$('#_sale_price').val(sale_price.toFixed(0));\n                \n                // 미리보기 업데이트\n                \$('#discount-preview').show();\n                \$('#preview-text').html(\n                    '정가 ' + regular.toLocaleString() + '원에서 ' +\n                    percent + '% 할인 → ' +\n                    '<strong style=\"color: #e74c3c;\">' + sale_price.toLocaleString() + '원</strong>' +\n                    ' (할인액: ' + discount_amount.toLocaleString() + '원)'\n                );\n                \n                // 할부 가격도 함께 표시\n                var months = \$('#_installment_months').val();\n                if (months > 1) {\n                    var monthly = sale_price / months;\n                    \$('#preview-text').append(\n                        '<br>월 ' + monthly.toLocaleString() + '원 × ' + months + '개월'\n                    );\n                }\n            }\n        });\n        \n        // 금액 할인 적용\n        \$('.apply-amount-discount').click(function() {\n            var regular = parseFloat(\$('#_regular_price').val());\n            var discount = parseFloat(\$('#discount_amount').val());\n            \n            if (regular && discount) {\n                var sale_price = regular - discount;\n                var percent = (discount / regular) * 100;\n                \n                \$('#_sale_price').val(sale_price.toFixed(0));\n                \n                \$('#discount-preview').show();\n                \$('#preview-text').html(\n                    '정가 ' + regular.toLocaleString() + '원에서 ' +\n                    discount.toLocaleString() + '원 할인 → ' +\n                    '<strong style=\"color: #e74c3c;\">' + sale_price.toLocaleString() + '원</strong>' +\n                    ' (' + percent.toFixed(1) + '% 할인)'\n                );\n            }\n        });\n    });\n    </script>\n    <?php\n}",
            ),
            'wc-realdeal-price-system' => array(
                'name'        => __( 'RealDeal 가격 시스템 v11.0 (통합)', 'acf-code-snippets-box' ),
                'description' => __( '빠른 편집, 가격 계산 엔진, 모듈형 숏코드, 통합 숏코드를 포함한 완전한 가격 시스템.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => true,
                'code'        => "<?php\n/**\n * RealDeal Platform: 가격 시스템 최종판 v11.0\n *\n * 이 코드는 관리자 경험과 고객 경험 모두를 위한 모든 가격 관련 기능을 포함합니다.\n * - PART 1: 관리자 '빠른 편집' 기능 강화\n * - PART 2: 모든 가격 계산을 처리하는 핵심 엔진 함수\n * - PART 3: 유연한 디자인을 위한 모듈형(Atomic) 숏코드\n * - PART 4: 빠르고 간편한 적용을 위한 통합(All-in-One) 숏코드\n */\n\n// PART 1: 관리자 '빠른 편집' 기능 강화 =================================\nadd_action( 'woocommerce_product_quick_edit_end', 'realdeal_add_quick_edit_fields' );\nif ( ! function_exists('realdeal_add_quick_edit_fields') ) {\n    function realdeal_add_quick_edit_fields() {\n        wp_nonce_field( 'realdeal_quick_edit_nonce', 'realdeal_quick_edit_nonce_field' );\n        ?>\n        <div class=\"quick-edit-custom-fields\">\n            <h4>가격 상세 설정</h4>\n            <label>\n                <span class=\"title\">할인 가격</span>\n                <span class=\"input-text-wrap\">\n                    <input type=\"text\" name=\"_sale_price\" class=\"wc_input_price\" placeholder=\"할인 가격\">\n                </span>\n            </label>\n            <label>\n                <span class=\"title\">할부 개월 수</span>\n                <span class=\"input-text-wrap\">\n                    <input type=\"number\" name=\"installment_months\" placeholder=\"개월 수 (숫자만)\">\n                </span>\n            </label>\n        </div>\n        <?php\n    }\n}\n\nadd_action( 'woocommerce_product_quick_edit_save', 'realdeal_save_quick_edit_fields' );\nif ( ! function_exists('realdeal_save_quick_edit_fields') ) {\n    function realdeal_save_quick_edit_fields( \$product ) {\n        if ( ! isset( \$_POST['realdeal_quick_edit_nonce_field'] ) || ! wp_verify_nonce( \$_POST['realdeal_quick_edit_nonce_field'], 'realdeal_quick_edit_nonce' ) ) return;\n        if ( isset( \$_POST['_sale_price'] ) ) {\n            \$product->set_sale_price( wc_clean( \$_POST['_sale_price'] ) );\n        }\n        if ( isset( \$_POST['installment_months'] ) ) {\n            \$product->update_meta_data( 'installment_months', absint( \$_POST['installment_months'] ) );\n        }\n        \$product->save();\n    }\n}\n\nadd_action( 'manage_product_posts_custom_column', 'realdeal_quick_edit_data_column', 10, 2 );\nif ( ! function_exists('realdeal_quick_edit_data_column') ) {\n    function realdeal_quick_edit_data_column( \$column, \$post_id ) {\n        if (\$column == 'price') {\n            \$product = wc_get_product(\$post_id);\n            if (\$product) {\n                echo '<div class=\"hidden installment_months\">' . esc_html(\$product->get_meta('installment_months')) . '</div>';\n                echo '<div class=\"hidden sale_price\">' . esc_html(\$product->get_sale_price()) . '</div>';\n            }\n        }\n    }\n}\n\n// PART 2: 핵심 계산 엔진 함수 ==========================================\nif ( ! function_exists( 'realdeal_get_price_data' ) ) {\n    function realdeal_get_price_data( \$product ) {\n        if ( ! is_a( \$product, 'WC_Product' ) ) return null;\n        \$data = [\n            'regular_price'       => (float) \$product->get_regular_price(),\n            'sale_price'          => (float) \$product->get_sale_price(),\n            'is_on_sale'          => \$product->is_on_sale(),\n            'saved_amount'        => 0,\n            'discount_percentage' => 0,\n            'installment_months'  => (int) get_post_meta( \$product->get_id(), 'installment_months', true ),\n            'installment_price'   => 0,\n        ];\n        if ( \$data['is_on_sale'] && ! empty( \$data['sale_price'] ) && \$data['regular_price'] > \$data['sale_price'] ) {\n            \$data['saved_amount'] = \$data['regular_price'] - \$data['sale_price'];\n            if (\$data['regular_price'] > 0) {\n                \$data['discount_percentage'] = round( ( \$data['saved_amount'] / \$data['regular_price'] ) * 100 );\n            }\n        }\n        if ( \$data['installment_months'] > 0 ) {\n            \$price_for_installment = \$data['is_on_sale'] && !empty(\$data['sale_price']) ? \$data['sale_price'] : \$data['regular_price'];\n            if ( \$price_for_installment > 0 ) {\n                \$data['installment_price'] = round( ( \$price_for_installment / \$data['installment_months'] ), -2 );\n            }\n        }\n        return \$data;\n    }\n}\n\n// PART 3: 유연한 디자인을 위한 모듈형(Atomic) 숏코드 ======================\n\$modular_shortcodes = [\n    'rd_badge' => function(\$data) {\n        if ( \$data['discount_percentage'] > 0 ) return '<span class=\"realdeal-discount-badge\">' . \$data['discount_percentage'] . '% OFF</span>';\n    },\n    'rd_summary' => function(\$data) {\n        if ( \$data['saved_amount'] > 0 ) return '<div class=\"realdeal-discount-summary\">✨ ' . wp_strip_all_tags( wc_price( \$data['saved_amount'] ) ) . ' 절약</div>';\n    },\n    'rd_regular_price' => function(\$data) {\n        if ( \$data['is_on_sale'] ) return '<del aria-hidden=\"true\">' . wc_price( \$data['regular_price'] ) . '</del>';\n    },\n    'rd_sale_price' => function(\$data) {\n        return '<ins>' . wc_price( \$data['is_on_sale'] ? \$data['sale_price'] : \$data['regular_price'] ) . '</ins>';\n    },\n    'rd_installments' => function(\$data) {\n        if ( \$data['installment_price'] > 0 ) return '<small class=\"realdeal-installment-price\">(월 ' . number_format_i18n( \$data['installment_price'] ) . '원 / ' . \$data['installment_months'] . '개월)</small>';\n    }\n];\n\nforeach ( \$modular_shortcodes as \$code => \$function ) {\n    if ( ! shortcode_exists( \$code ) ) {\n        add_shortcode( \$code, function() use ( \$function ) {\n            global \$product;\n            if ( ! \$product ) \$product = wc_get_product( get_the_ID() );\n            if ( ! \$product ) return '';\n            \$price_data = realdeal_get_price_data( \$product );\n            return \$price_data ? \$function( \$price_data ) : '';\n        });\n    }\n}\n\n// PART 4: 빠르고 간편한 적용을 위한 통합(All-in-One) 숏코드 ============\nif ( ! shortcode_exists( 'realdeal_price' ) ) {\n    add_shortcode( 'realdeal_price', function() {\n        global \$product;\n        if ( ! \$product ) \$product = wc_get_product( get_the_ID() );\n        if ( ! \$product ) return '';\n\n        \$data = realdeal_get_price_data( \$product );\n        if ( !\$data ) return '';\n\n        \$badge_html = \$data['discount_percentage'] > 0 ? '<span class=\"realdeal-discount-badge\">' . \$data['discount_percentage'] . '% OFF</span>' : '';\n        \$summary_html = \$data['saved_amount'] > 0 ? '<div class=\"realdeal-discount-summary\">✨ ' . wp_strip_all_tags( wc_price( \$data['saved_amount'] ) ) . ' 절약</div>' : '';\n        \$installments_html = \$data['installment_price'] > 0 ? '<br><small class=\"realdeal-installment-price\">(월 ' . number_format_i18n( \$data['installment_price'] ) . '원 / ' . \$data['installment_months'] . '개월)</small>' : '';\n\n        if ( \$data['is_on_sale'] ) {\n            \$price_html = '<del>' . wc_price( \$data['regular_price'] ) . '</del> <ins>' . wc_price( \$data['sale_price'] ) . '</ins>';\n        } else {\n            \$price_html = '<ins>' . wc_price( \$data['regular_price'] ) . '</ins>';\n        }\n        \n        return sprintf('<div class=\"realdeal-price-wrapper price\">%s%s%s%s</div>', \$badge_html, \$summary_html, \$price_html, \$installments_html);\n    });\n}\n\n// 번역어 교체\nadd_filter( 'gettext', 'realdeal_change_weird_translation', 20, 3 );\nfunction realdeal_change_weird_translation( \$translated_text, \$text, \$domain ) {\n    if ( 'Saved' === \$text && '저장' === \$translated_text ) {\n        \$translated_text = '절약';\n    }\n    return \$translated_text;\n}\n\n// 장바구니 UI 정리\nadd_filter( 'woocommerce_cart_item_name', 'realdeal_cleanup_cart_item_name', 100, 3 );\nfunction realdeal_cleanup_cart_item_name( \$product_name, \$cart_item, \$cart_item_key ) {\n    if ( is_cart() || is_checkout() || ( defined('WOOCOMMERCE_CART') && WOOCOMMERCE_CART ) ) {\n        \$_product = apply_filters( 'woocommerce_cart_item_product', \$cart_item['data'], \$cart_item, \$cart_item_key );\n        \$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', \$_product->is_visible() ? \$_product->get_permalink( \$cart_item ) : '', \$cart_item, \$cart_item_key );\n        if ( \$product_permalink ) {\n            return sprintf( '<a href=\"%s\">%s</a>', esc_url( \$product_permalink ), \$_product->get_name() );\n        } else {\n            return \$_product->get_name();\n        }\n    }\n    return \$product_name;\n}",
            ),
            'wc-universal-installment-display' => array(
                'name'        => __( 'RealDeal 할부 및 할인 표시 v6.0', 'acf-code-snippets-box' ),
                'description' => __( '할인 가격 기준 할부 계산, 할인율 자동 계산, 관리자 할인 정보 표시를 포함합니다.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => true,
                'code'        => "<?php\n/**\n * RealDeal Platform: Universal Installment & Discount Display v6.0\n */\n\n// 프론트엔드 가격 표시 로직 필터링\nadd_filter( 'woocommerce_get_price_html', 'realdeal_advanced_price_display_v6', 100, 2 );\n\nfunction realdeal_advanced_price_display_v6( \$price_html, \$product ) {\n    if ( is_admin() || \$product->is_type('variable') ) {\n        return \$price_html;\n    }\n\n    \$months = get_post_meta( \$product->get_id(), 'installment_months', true );\n    \$installment_html = '';\n\n    \$regular_price = (float) \$product->get_regular_price();\n    \$sale_price = (float) \$product->get_sale_price();\n\n    if ( ! empty( \$months ) && is_numeric( \$months ) && \$months > 0 ) {\n        \$price_for_installment = !empty(\$sale_price) && \$product->is_on_sale() ? \$sale_price : \$regular_price;\n        \n        if (\$price_for_installment > 0) {\n            \$installment_price = round( ( \$price_for_installment / \$months ), -2 );\n            \$installment_html = '<br><small class=\"realdeal-installment-price\">(월 ' . number_format_i18n( \$installment_price ) . '원 / ' . esc_html( \$months ) . '개월)</small>';\n        }\n    }\n\n    if ( \$product->is_on_sale() && !empty(\$sale_price) && \$regular_price > \$sale_price ) {\n        \$saved_amount = \$regular_price - \$sale_price;\n        \$discount_percentage = round( ( \$saved_amount / \$regular_price ) * 100 );\n\n        \$new_price_html = sprintf(\n            '<div class=\"realdeal-price-wrapper\">' .\n            '<span class=\"realdeal-discount-badge\">%s%% OFF</span>' .\n            '<del aria-hidden=\"true\">%s</del> <ins>%s</ins>' .\n            '%s' .\n            '</div>',\n            \$discount_percentage,\n            wc_price( \$regular_price ),\n            wc_price( \$sale_price ),\n            \$installment_html\n        );\n        return \$new_price_html;\n    } \n    elseif (\$regular_price > 0) {\n        return wc_price( \$regular_price ) . \$installment_html;\n    }\n\n    return \$price_html;\n}\n\n// 관리자 페이지에 할인 정보 표시\nadd_action( 'woocommerce_product_options_pricing', 'realdeal_display_discount_info_in_admin' );\n\nfunction realdeal_display_discount_info_in_admin() {\n    global \$product_object;\n\n    if ( ! \$product_object ) {\n        return;\n    }\n\n    \$regular_price = (float) \$product_object->get_regular_price();\n    \$sale_price = (float) \$product_object->get_sale_price();\n\n    echo '<div class=\"options_group show_if_simple\">';\n\n    if ( \$product_object->is_on_sale() && !empty(\$sale_price) && \$regular_price > \$sale_price ) {\n        \$saved_amount = \$regular_price - \$sale_price;\n        \$discount_percentage = round( ( \$saved_amount / \$regular_price ) * 100 );\n        \n        \$admin_info_html = sprintf(\n            '<p class=\"form-field\">' .\n            '<label style=\"color: #0383FE; font-weight: bold;\">할인 정보</label>' .\n            '<span style=\"display: block; padding-top: 5px;\">' .\n            '<strong>%d%% 할인</strong> / %s 절약' .\n            '</span>' .\n            '</p>',\n            \$discount_percentage,\n            wp_strip_all_tags( wc_price( \$saved_amount ) )\n        );\n        echo \$admin_info_html;\n    } else {\n        echo '<p class=\"form-field\"><label style=\"color: #6B7280;\">할인 정보</label><span style=\"display: block; padding-top: 5px;\">현재 할인 중인 상품이 아닙니다.</span></p>';\n    }\n\n    echo '</div>';\n}",
            ),
        );
    }

    /**
     * WooCommerce 전용 CSS 프리셋 목록
     * Pro 버전 이상 사용자 전용
     */
    public static function get_woocommerce_css_presets() {
        return array(
            'wc-price-installment-style' => array(
                'name'        => __( '할부 가격 표시 스타일', 'acf-code-snippets-box' ),
                'description' => __( '상품 목록에서 할부 가격과 할인 배지를 스타일링합니다.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => true,
                'code'        => "/* RealDeal 가격 표시 스타일 */\n.realdeal-price-wrapper {\n  line-height: 1.5;\n}\n\n.realdeal-discount-badge,\n.onsale {\n    display: inline-block !important;\n    padding: 6px 12px !important;\n    background-color: var(--accent-red, #FF0033) !important;\n    color: white !important;\n    font-size: 0.9em !important;\n    font-weight: 700 !important;\n    border-radius: 4px !important;\n    position: absolute !important;\n    top: 10px !important;\n    right: 10px !important;\n    z-index: 999 !important;\n}\n\n.price del {\n  opacity: 0.8;\n  font-size: 0.9em;\n  margin-right: 5px;\n}\n\n.price ins {\n  font-weight: bold;\n  font-size: 1em;\n  text-decoration: none;\n}\n\n.realdeal-installment-price {\n  display: block;\n  font-size: 15px;\n  font-weight: 400;\n  margin-top: 4px;\n}\n\n.realdeal-discount-summary {\n  font-size: 14px;\n  font-weight: 500;\n  background-color: rgba(3, 131, 254, 0.1);\n  padding: 4px 8px;\n  border-radius: 4px;\n  margin: 5px 0;\n  display: inline-block;\n}",
            ),
            'wc-button-style' => array(
                'name'        => __( 'WooCommerce 버튼 스타일', 'acf-code-snippets-box' ),
                'description' => __( '장바구니, 체크아웃, 미니카트 버튼 스타일을 통일합니다.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => true,
                'code'        => "/* WooCommerce 버튼 스타일 */\n\n/* 박스쉐도우 아웃라인 제거 */\nbody:not(.woocommerce-block-theme-has-button-styles)\n    .wc-block-components-button:not(.is-link):focus {\n    box-shadow: none !important;\n    outline: none !important;\n}\n\n/* 미니카트 스타일 */\n.wc-block-mini-cart__footer-cart {\n    background-color: var(--base-3);\n    color: currentColor;\n    display: inline-flex;\n    font-size: 0.875rem;\n    line-height: 0.875rem;\n    text-decoration: none;\n    border: 1px solid var(--contrast-3);\n    border-radius: 0.375rem;\n    padding: 0.5rem 0;\n}\n\n.wc-block-mini-cart__footer-cart:hover {\n    color: currentColor;\n    background-color: var(--base-2);\n}\n\n.wc-block-mini-cart__footer-checkout,\n.wc-block-cart__submit-button {\n    background-color: var(--contrast);\n    color: var(--base-3);\n    font-size: 0.875rem;\n    font-weight: 500;\n    line-height: 0.875rem;\n    border: 1px solid var(--contrast);\n    border-radius: 0.375rem;\n    padding: 0.5rem 0;\n}\n\n.wc-block-mini-cart__footer-checkout:hover,\n.wc-block-cart__submit-button:hover {\n    color: var(--base-3);\n    opacity: 0.95;\n}",
            ),
            'wc-product-list-style' => array(
                'name'        => __( '상품 목록 디자인 개선', 'acf-code-snippets-box' ),
                'description' => __( '상품 목록의 배경, 별점 정렬, 장바구니 버튼을 개선합니다.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => true,
                'code'        => "/* 상품 목록 디자인 개선 */\n\n/* 상품 상세 정보 컨테이너 배경색: 목록에서만 투명 유지 */\nbody:not(.single-product) .products .product .product-details,\nbody:not(.single-product) .products .product .product-details.content-bg {\n    background-color: transparent !important;\n}\n\n/* 별점 정렬 수정: 목록에만 적용 */\nbody:not(.single-product) .products .product .glsr {\n    text-align: left !important;\n    margin: 0 auto 0 0 !important;\n}\n\n/* 할인 배지 위치 수정: 목록에서만 적용 */\nbody:not(.single-product) .realdeal-discount-badge,\nbody:not(.single-product) .onsale {\n    position: absolute !important;\n    top: 10px !important;\n    right: 10px !important;\n    z-index: 999 !important;\n}\n\n/* 장바구니 버튼 수정: 목록에만 적용 */\n.products .product .add_to_cart_button {\n    background-color: var(--accent-orange, #FF6400) !important;\n    color: white !important;\n    border: none !important;\n    padding: 10px 15px !important;\n    font-weight: 600 !important;\n}\n\n/* 장바구니 버튼 내부 아이콘 색상도 흰색으로 통일 */\n.products .product .add_to_cart_button .kadence-svg-iconset svg {\n    fill: white !important;\n}",
            ),
            'wc-realdeal-integrated-css' => array(
                'name'        => __( 'RealDeal WooCommerce 통합 CSS v3.0', 'acf-code-snippets-box' ),
                'description' => __( '가격 표시, 할인 배지, 상품 목록 디자인, 장바구니 UI 개선을 포함한 통합 CSS.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => true,
                'code'        => "/* === RealDeal WooCommerce 통합 CSS (v3.0) === */\n\n/* [섹션 1]: RealDeal 가격 표시 v10.0 스타일 (목록에만 적용되도록 격리) */\n.realdeal-price-wrapper {\n  line-height: 1.5;\n}\n\n.realdeal-discount-badge, \n.onsale { \n    .products .product & {\n        display: inline-block !important; \n        padding: 6px 12px !important; \n        background-color: var(--accent-red, #FF0033) !important;\n        color: white !important;\n        font-size: 0.9em !important; \n        font-weight: 700 !important; \n        border-radius: 4px !important;\n        position: absolute !important; \n        top: 10px !important; \n        right: 10px !important; \n        z-index: 999 !important;\n    }\n}\n\n.price del {\n  opacity: 0.8;\n  font-size: 0.9em;\n  margin-right: 5px;\n}\n\n.price ins {\n  font-weight: bold;\n  font-size: 1em;\n  text-decoration: none;\n}\n\n.realdeal-installment-price {\n  display: block;\n  font-size: 15px;\n  font-weight: 400;\n  margin-top: 4px;\n}\n\n.realdeal-discount-summary {\n  font-size: 14px;\n  font-weight: 500;\n  background-color: rgba(3, 131, 254, 0.1);\n  padding: 4px 8px;\n  border-radius: 4px;\n  margin: 5px 0;\n  display: inline-block;\n}\n\n.single-product.post-type-course .summary > .price:not(.realdeal-price-wrapper) {\n    display: none !important;\n}\n\n/* [섹션 2]: WooCommerce 상품 목록 디자인 개선 (상세 페이지 격리) */\nbody:not(.single-product) .products .product .product-details,\nbody:not(.single-product) .products .product .product-details.content-bg {\n    background-color: transparent !important;\n}\n\nbody:not(.single-product) .products .product .glsr {\n    text-align: left !important;\n    margin: 0 auto 0 0 !important; \n}\n\nbody:not(.single-product) .realdeal-discount-badge, \nbody:not(.single-product) .onsale { \n    position: absolute !important; \n    top: 10px !important;\n    right: 10px !important; \n    z-index: 999 !important;\n}\n\n.products .product .add_to_cart_button {\n    background-color: var(--accent-orange, #FF6400) !important; \n    color: white !important; \n    border: none !important;\n    padding: 10px 15px !important;\n    font-weight: 600 !important;\n}\n\n.products .product .add_to_cart_button .kadence-svg-iconset svg {\n    fill: white !important;\n}\n\n/* [섹션 3]: 장바구니 및 미리보기 UI 개선 */\n.woocommerce-mini-cart-item .product-name img {\n    display: none !important;\n}\n\n.cart_item .product-name img,\n.cart_item .product-name p,\n.cart_item .product-name .wp-block-image {\n    display: none !important;\n}",
            ),
            'wc-cart-preview-ui-fix' => array(
                'name'        => __( '장바구니 및 미리보기 UI 개선 v11.1', 'acf-code-snippets-box' ),
                'description' => __( '상품 본문의 이미지가 장바구니에 표시되는 문제를 해결합니다.', 'acf-code-snippets-box' ),
                'category'    => 'woocommerce',
                'pro_only'    => false,
                'code'        => "/* 장바구니 및 미리보기 UI 개선 v11.1 */\n\n/* 1. 미니카트(슬라이드)에서 상품명 아래의 모든 이미지 숨기기 */\n.woocommerce-mini-cart-item .product-name img {\n    display: none !important;\n}\n\n/* 2. 장바구니 페이지에서 상품명 아래의 모든 이미지 숨기기 */\n.cart_item .product-name img,\n.cart_item .product-name p,\n.cart_item .product-name .wp-block-image {\n    display: none !important;\n}",
            ),
        );
    }

    /**
     * 유틸리티/디버깅 프리셋 목록
     */
    public static function get_utility_presets() {
        return array(
            'plugin-deactivation-logger' => array(
                'name'        => __( '플러그인 비활성화 로그 기록기', 'acf-code-snippets-box' ),
                'description' => __( '플러그인이 비활성화될 때 상세 로그를 기록합니다 (디버깅용).', 'acf-code-snippets-box' ),
                'category'    => 'debug',
                'pro_only'    => false,
                'code'        => "<?php\nfunction acf_csb_log_plugin_deactivation(\$plugin_name, \$is_network_wide) {\n    \$log_file = WP_CONTENT_DIR . '/plugin_deactivation.log';\n    \$timestamp = current_time('mysql');\n    \$request_uri = \$_SERVER['REQUEST_URI'];\n    \$referrer = isset(\$_SERVER['HTTP_REFERER']) ? \$_SERVER['HTTP_REFERER'] : 'N/A';\n    \$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);\n    \$call_stack = '';\n    foreach (\$backtrace as \$key => \$trace) {\n        \$file = isset(\$trace['file']) ? str_replace(ABSPATH, '', \$trace['file']) : 'N/A';\n        \$line = isset(\$trace['line']) ? \$trace['line'] : 'N/A';\n        \$function = isset(\$trace['function']) ? \$trace['function'] : 'N/A';\n        \$call_stack .= sprintf(\"  - [%d] %s() in %s:%s\\n\", \$key, \$function, \$file, \$line);\n    }\n    \$message = sprintf(\n        \"[%s] Plugin '%s' deactivated.\\n  - Request URI: %s\\n  - Referrer: %s\\n  - Call Stack:\\n%s\\n\",\n        \$timestamp, \$plugin_name, \$request_uri, \$referrer, \$call_stack\n    );\n    file_put_contents(\$log_file, \$message, FILE_APPEND);\n}\nadd_action('deactivated_plugin', 'acf_csb_log_plugin_deactivation', 10, 2);",
            ),
            'search-form-customizer' => array(
                'name'        => __( '검색 폼 URL 커스터마이저', 'acf-code-snippets-box' ),
                'description' => __( '검색 폼의 action URL과 파라미터명을 커스터마이즈합니다.', 'acf-code-snippets-box' ),
                'category'    => 'utility',
                'pro_only'    => false,
                'code'        => "<?php\n// 검색 폼 커스터마이저 (특정 페이지로 리다이렉트)\nadd_action( 'wp_footer', 'acf_csb_customize_search_form' );\nfunction acf_csb_customize_search_form() {\n    \$search_page_id = get_option('acf_csb_search_page_id', 0);\n    if (empty(\$search_page_id)) return;\n    \$search_page_url = get_permalink(\$search_page_id);\n    if (!\$search_page_url) return;\n    ?>\n    <script type=\"text/javascript\">\n    (function(\$) {\n        \$(document).ready(function() {\n            const searchForms = \$('form.wp-block-search');\n            searchForms.each(function(index, form) {\n                const searchInput = \$(form).find('input[name=\"s\"]');\n                if (searchInput.length) {\n                    searchInput.attr('name', 'searchstr');\n                }\n                form.action = '<?php echo esc_url(\$search_page_url); ?>';\n            });\n        });\n    })(jQuery);\n    </script>\n    <?php\n}",
            ),
        );
    }

    /**
     * 모든 프리셋 가져오기
     */
    public static function get_all_presets() {
        return array(
            'css'           => self::get_css_presets(),
            'js'            => self::get_js_presets(),
            'php'           => self::get_php_presets(),
            'woocommerce_php' => self::get_woocommerce_php_presets(),
            'woocommerce_css' => self::get_woocommerce_css_presets(),
            'utility'       => self::get_utility_presets(),
        );
    }

    /**
     * Pro 전용 프리셋 여부 확인
     */
    public static function is_pro_preset( $preset_id, $type ) {
        $presets = array();
        switch ( $type ) {
            case 'woocommerce_php':
                $presets = self::get_woocommerce_php_presets();
                break;
            case 'woocommerce_css':
                $presets = self::get_woocommerce_css_presets();
                break;
            case 'utility':
                $presets = self::get_utility_presets();
                break;
            default:
                return false;
        }
        
        if ( isset( $presets[ $preset_id ] ) && isset( $presets[ $preset_id ]['pro_only'] ) ) {
            return $presets[ $preset_id ]['pro_only'];
        }
        
        return false;
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
            case 'woocommerce_php':
                $presets = self::get_woocommerce_php_presets();
                break;
            case 'woocommerce_css':
                $presets = self::get_woocommerce_css_presets();
                break;
            case 'utility':
                $presets = self::get_utility_presets();
                break;
        }

        wp_send_json_success( $presets );
    }

    /**
     * AJAX: 프리셋 적용
     * [v2.3.3] nonce 검증 개선
     */
    public function ajax_apply_preset() {
        // [v2.3.3] nonce 검증 개선 - POST 또는 GET에서 nonce 확인
        $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : ( isset( $_GET['nonce'] ) ? $_GET['nonce'] : '' );
        if ( ! wp_verify_nonce( $nonce, 'acf_csb_nonce' ) ) {
            wp_send_json_error( __( '보안 검증에 실패했습니다.', 'acf-code-snippets-box' ) );
        }

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
            case 'woocommerce_php':
                $presets = self::get_woocommerce_php_presets();
                break;
            case 'woocommerce_css':
                $presets = self::get_woocommerce_css_presets();
                break;
            case 'utility':
                $presets = self::get_utility_presets();
                break;
        }

        if ( ! isset( $presets[ $preset_id ] ) ) {
            wp_send_json_error( __( '프리셋을 찾을 수 없습니다.', 'acf-code-snippets-box' ) );
        }

        // Pro 전용 프리셋 체크
        if ( self::is_pro_preset( $preset_id, $type ) && ! self::is_pro_user() ) {
            wp_send_json_error( __( '이 프리셋은 Pro 버전 이상의 사용자만 사용할 수 있습니다.', 'acf-code-snippets-box' ) );
        }

        wp_send_json_success( array(
            'code'     => $presets[ $preset_id ]['code'],
            'name'     => $presets[ $preset_id ]['name'],
            'pro_only' => isset( $presets[ $preset_id ]['pro_only'] ) ? $presets[ $preset_id ]['pro_only'] : false,
        ) );
    }

    /**
     * Pro 사용자 여부 확인
     */
    public static function is_pro_user() {
        // ACF CSS 메인 플러그인의 라이선스 타입 확인
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $license_type = JJ_STYLE_GUIDE_LICENSE_TYPE;
            return in_array( $license_type, array( 'BASIC', 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }
        
        // 사용자 타입 확인
        if ( defined( 'JJ_STYLE_GUIDE_USER_TYPE' ) ) {
            $user_type = JJ_STYLE_GUIDE_USER_TYPE;
            return in_array( $user_type, array( 'PARTNER', 'MASTER' ), true );
        }
        
        // 기본값: Free 사용자
        return false;
    }

    /**
     * 카테고리별 프리셋 목록 가져오기
     */
    public static function get_presets_by_category( $category ) {
        $all_presets = self::get_all_presets();
        $result = array();
        
        foreach ( $all_presets as $type => $presets ) {
            foreach ( $presets as $id => $preset ) {
                if ( isset( $preset['category'] ) && $preset['category'] === $category ) {
                    $result[ $type ][ $id ] = $preset;
                }
            }
        }
        
        return $result;
    }
}
