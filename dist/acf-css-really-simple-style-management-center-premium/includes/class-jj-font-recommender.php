<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 12] Font Recommender
 * 
 * 한국어/영문 무료 및 유료 폰트 추천 목록 제공
 * 원클릭 Google Fonts 적용 기능
 * 
 * @since 11.0.0
 */
class JJ_Font_Recommender {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function init() {
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_apply_google_font', array( $this, 'ajax_apply_google_font' ) );
        add_action( 'wp_ajax_jj_get_font_recommendations', array( $this, 'ajax_get_font_recommendations' ) );
    }

    /**
     * 폰트 추천 목록 가져오기
     */
    public function get_font_recommendations() {
        return array(
            'free_korean' => array(
                'title' => __( '한국어 무료 폰트 (Google Fonts)', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '원클릭으로 바로 적용 가능한 무료 한국어 폰트입니다. 상업적 이용이 가능합니다.', 'acf-css-really-simple-style-management-center' ),
                'fonts' => array(
                    array(
                        'name' => 'Pretendard',
                        'family' => 'Pretendard Variable',
                        'weights' => '100..900',
                        'type' => 'sans-serif',
                        'note' => __( '가장 인기 있는 현대적 한국어 폰트. 가독성 뛰어남.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => '',
                        'cdn_url' => 'https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/variable/pretendardvariable-dynamic-subset.min.css',
                        'download_url' => 'https://github.com/orioncactus/pretendard/releases/download/v1.3.9/Pretendard-1.3.9.zip',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Noto Sans KR',
                        'family' => 'Noto Sans KR',
                        'weights' => '100,300,400,500,700,900',
                        'type' => 'sans-serif',
                        'note' => __( 'Google의 공식 한국어 폰트. 모든 웹에서 안정적.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100;300;400;500;700;900&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Noto%20Sans%20KR',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Noto Serif KR',
                        'family' => 'Noto Serif KR',
                        'weights' => '200,300,400,500,600,700,900',
                        'type' => 'serif',
                        'note' => __( '격식 있는 문서, 블로그, 출판물에 적합.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Noto+Serif+KR:wght@200;300;400;500;600;700;900&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Noto%20Serif%20KR',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'IBM Plex Sans KR',
                        'family' => 'IBM Plex Sans KR',
                        'weights' => '100,200,300,400,500,600,700',
                        'type' => 'sans-serif',
                        'note' => __( 'IBM 공식 폰트. 기술 문서, 개발자 블로그에 적합.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+KR:wght@100;200;300;400;500;600;700&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=IBM%20Plex%20Sans%20KR',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Gothic A1',
                        'family' => 'Gothic A1',
                        'weights' => '100,200,300,400,500,600,700,800,900',
                        'type' => 'sans-serif',
                        'note' => __( '깔끔한 고딕체. 다양한 두께 지원.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Gothic+A1:wght@100;200;300;400;500;600;700;800;900&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Gothic%20A1',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Nanum Gothic',
                        'family' => 'Nanum Gothic',
                        'weights' => '400,700,800',
                        'type' => 'sans-serif',
                        'note' => __( '네이버 공식 폰트. 한국에서 가장 널리 사용됨.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Nanum%20Gothic',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Nanum Myeongjo',
                        'family' => 'Nanum Myeongjo',
                        'weights' => '400,700,800',
                        'type' => 'serif',
                        'note' => __( '전통적인 명조체. 소설, 에세이, 문학 콘텐츠에 적합.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700;800&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Nanum%20Myeongjo',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Black Han Sans',
                        'family' => 'Black Han Sans',
                        'weights' => '400',
                        'type' => 'sans-serif',
                        'note' => __( '강렬한 제목용 폰트. 배너, 포스터에 적합.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Black+Han+Sans&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Black%20Han%20Sans',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Jua',
                        'family' => 'Jua',
                        'weights' => '400',
                        'type' => 'sans-serif',
                        'note' => __( '귀여운 손글씨 느낌. 어린이 콘텐츠, 캐주얼 사이트에 적합.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Jua&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Jua',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Sunflower',
                        'family' => 'Sunflower',
                        'weights' => '300,500,700',
                        'type' => 'sans-serif',
                        'note' => __( '부드럽고 친근한 폰트. 라이프스타일 블로그에 적합.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Sunflower:wght@300;500;700&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Sunflower',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                ),
            ),
            'free_english' => array(
                'title' => __( '영문 무료 폰트 (Google Fonts)', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '전 세계에서 가장 많이 사용되는 무료 영문 폰트입니다.', 'acf-css-really-simple-style-management-center' ),
                'fonts' => array(
                    array(
                        'name' => 'Inter',
                        'family' => 'Inter',
                        'weights' => '100..900',
                        'type' => 'sans-serif',
                        'note' => __( '가장 인기 있는 UI 폰트. 가독성과 현대적 디자인 최고.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Inter',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Poppins',
                        'family' => 'Poppins',
                        'weights' => '100,200,300,400,500,600,700,800,900',
                        'type' => 'sans-serif',
                        'note' => __( '기하학적 산세리프. 현대적인 웹사이트에 적합.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Poppins',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Montserrat',
                        'family' => 'Montserrat',
                        'weights' => '100..900',
                        'type' => 'sans-serif',
                        'note' => __( '우아한 기하학적 폰트. 고급스러운 브랜드에 적합.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Montserrat',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Roboto',
                        'family' => 'Roboto',
                        'weights' => '100,300,400,500,700,900',
                        'type' => 'sans-serif',
                        'note' => __( 'Google Material Design 공식 폰트.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Roboto',
                        'license' => 'Apache-2.0',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Open Sans',
                        'family' => 'Open Sans',
                        'weights' => '300..800',
                        'type' => 'sans-serif',
                        'note' => __( '가장 널리 사용되는 웹 폰트 중 하나. 가독성 최고.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300..800&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Open%20Sans',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Lato',
                        'family' => 'Lato',
                        'weights' => '100,300,400,700,900',
                        'type' => 'sans-serif',
                        'note' => __( '따뜻하고 안정적인 폰트. 다목적 사용.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Lato',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Nunito',
                        'family' => 'Nunito',
                        'weights' => '200..1000',
                        'type' => 'sans-serif',
                        'note' => __( '둥글고 친근한 폰트. 캐주얼한 분위기에 적합.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Nunito:wght@200..1000&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Nunito',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Playfair Display',
                        'family' => 'Playfair Display',
                        'weights' => '400..900',
                        'type' => 'serif',
                        'note' => __( '고급스러운 세리프. 제목, 럭셔리 브랜드에 적합.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400..900&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Playfair%20Display',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'Source Serif Pro',
                        'family' => 'Source Serif Pro',
                        'weights' => '200,300,400,600,700,900',
                        'type' => 'serif',
                        'note' => __( 'Adobe 공식 세리프. 긴 글 읽기에 최적화.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=Source+Serif+Pro:wght@200;300;400;600;700;900&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=Source%20Serif%20Pro',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                    array(
                        'name' => 'JetBrains Mono',
                        'family' => 'JetBrains Mono',
                        'weights' => '100..800',
                        'type' => 'monospace',
                        'note' => __( '개발자용 모노스페이스. 코드 블록에 최적.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => 'https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@100..800&display=swap',
                        'cdn_url' => '',
                        'download_url' => 'https://fonts.google.com/download?family=JetBrains%20Mono',
                        'license' => 'OFL-1.1',
                        'is_free' => true,
                    ),
                ),
            ),
            'premium' => array(
                'title' => __( '유료/프리미엄 폰트 (참조용)', 'acf-css-really-simple-style-management-center' ),
                'description' => __( '상업용 라이선스가 필요한 고품질 폰트입니다. 직접 구매 후 사용하세요.', 'acf-css-really-simple-style-management-center' ),
                'fonts' => array(
                    array(
                        'name' => 'Sandoll Gothic Neo',
                        'family' => 'Sandoll Gothic Neo',
                        'weights' => '100,200,300,400,500,600,700,800,900',
                        'type' => 'sans-serif',
                        'note' => __( '한국 대표 고딕체. 삼성, 현대 등 대기업 사용.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => '',
                        'cdn_url' => '',
                        'download_url' => '',
                        'purchase_url' => 'https://sandollcloud.com/font/gothic-neo/',
                        'license' => __( '상업용 라이선스 필요', 'acf-css-really-simple-style-management-center' ),
                        'is_free' => false,
                    ),
                    array(
                        'name' => 'Yoon Gothic',
                        'family' => 'Yoon Gothic',
                        'weights' => '100,200,300,400,500,600,700,800,900',
                        'type' => 'sans-serif',
                        'note' => __( '윤디자인 대표 고딕. 가독성 뛰어남.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => '',
                        'cdn_url' => '',
                        'download_url' => '',
                        'purchase_url' => 'https://yoonfont.co.kr/',
                        'license' => __( '상업용 라이선스 필요', 'acf-css-really-simple-style-management-center' ),
                        'is_free' => false,
                    ),
                    array(
                        'name' => 'Helvetica Neue',
                        'family' => 'Helvetica Neue',
                        'weights' => '100,200,300,400,500,600,700,800,900',
                        'type' => 'sans-serif',
                        'note' => __( '세계에서 가장 유명한 산세리프. Apple 공식 폰트.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => '',
                        'cdn_url' => '',
                        'download_url' => '',
                        'purchase_url' => 'https://www.myfonts.com/fonts/linotype/helvetica-neue/',
                        'license' => __( '상업용 라이선스 필요', 'acf-css-really-simple-style-management-center' ),
                        'is_free' => false,
                    ),
                    array(
                        'name' => 'Proxima Nova',
                        'family' => 'Proxima Nova',
                        'weights' => '100,200,300,400,500,600,700,800,900',
                        'type' => 'sans-serif',
                        'note' => __( '가장 인기 있는 유료 웹폰트. Spotify, BuzzFeed 사용.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => '',
                        'cdn_url' => '',
                        'download_url' => '',
                        'purchase_url' => 'https://www.myfonts.com/fonts/marksimonson/proxima-nova/',
                        'license' => __( '상업용 라이선스 필요', 'acf-css-really-simple-style-management-center' ),
                        'is_free' => false,
                    ),
                    array(
                        'name' => 'Futura',
                        'family' => 'Futura',
                        'weights' => '300,400,500,700',
                        'type' => 'sans-serif',
                        'note' => __( '바우하우스 스타일 기하학적 폰트. 럭셔리 브랜드 사용.', 'acf-css-really-simple-style-management-center' ),
                        'google_url' => '',
                        'cdn_url' => '',
                        'download_url' => '',
                        'purchase_url' => 'https://www.myfonts.com/fonts/bitstream/futura/',
                        'license' => __( '상업용 라이선스 필요', 'acf-css-really-simple-style-management-center' ),
                        'is_free' => false,
                    ),
                ),
            ),
        );
    }

    /**
     * Google Font 적용 (CSS 링크 저장)
     */
    public function ajax_apply_google_font() {
        check_ajax_referer( 'jj_font_recommender_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $font_family = isset( $_POST['font_family'] ) ? sanitize_text_field( wp_unslash( $_POST['font_family'] ) ) : '';
        $font_url = isset( $_POST['font_url'] ) ? esc_url_raw( wp_unslash( $_POST['font_url'] ) ) : '';
        $target = isset( $_POST['target'] ) ? sanitize_text_field( wp_unslash( $_POST['target'] ) ) : 'korean'; // korean, english, buttons, forms

        if ( empty( $font_family ) ) {
            wp_send_json_error( array( 'message' => __( '폰트 이름이 필요합니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 옵션 업데이트
        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $opt = (array) get_option( $key, array() );

        if ( ! isset( $opt['fonts'] ) || ! is_array( $opt['fonts'] ) ) {
            $opt['fonts'] = array();
        }
        if ( ! isset( $opt['fonts'][ $target ] ) || ! is_array( $opt['fonts'][ $target ] ) ) {
            $opt['fonts'][ $target ] = array();
        }

        $opt['fonts'][ $target ]['family'] = $font_family;
        $opt['fonts'][ $target ]['url'] = $font_url;

        // 활성 폰트 URL 목록에 추가
        if ( ! isset( $opt['active_font_urls'] ) || ! is_array( $opt['active_font_urls'] ) ) {
            $opt['active_font_urls'] = array();
        }
        if ( ! empty( $font_url ) && ! in_array( $font_url, $opt['active_font_urls'], true ) ) {
            $opt['active_font_urls'][] = $font_url;
        }

        update_option( $key, $opt );

        // CSS 캐시 플러시
        if ( class_exists( 'JJ_CSS_Cache' ) ) {
            try {
                JJ_CSS_Cache::instance()->flush();
            } catch ( Exception $e ) {
                // ignore
            }
        }

        wp_send_json_success( array(
            'message' => sprintf(
                /* translators: %s: font family name */
                __( '%s 폰트가 적용되었습니다.', 'acf-css-really-simple-style-management-center' ),
                $font_family
            ),
            'font_family' => $font_family,
            'font_url' => $font_url,
        ) );
    }

    /**
     * AJAX: 폰트 추천 목록 가져오기
     */
    public function ajax_get_font_recommendations() {
        check_ajax_referer( 'jj_font_recommender_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        wp_send_json_success( $this->get_font_recommendations() );
    }

    /**
     * 활성 폰트 URL 목록 가져오기 (프런트엔드 로드용)
     */
    public function get_active_font_urls() {
        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $opt = (array) get_option( $key, array() );
        return isset( $opt['active_font_urls'] ) && is_array( $opt['active_font_urls'] ) ? $opt['active_font_urls'] : array();
    }

    /**
     * 폰트 설치 가이드 HTML 생성
     */
    public function get_installation_guide_html() {
        ob_start();
        ?>
        <div class="jj-font-guide">
            <h3><?php esc_html_e( '폰트 설치 및 사용 가이드', 'acf-css-really-simple-style-management-center' ); ?></h3>
            
            <div class="jj-font-guide-section">
                <h4><?php esc_html_e( '1. 원클릭 적용 (Google Fonts)', 'acf-css-really-simple-style-management-center' ); ?></h4>
                <p><?php esc_html_e( '위 목록에서 "적용" 버튼을 클릭하면 바로 사이트에 적용됩니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>

            <div class="jj-font-guide-section">
                <h4><?php esc_html_e( '2. 수동 설치 (로컬 폰트)', 'acf-css-really-simple-style-management-center' ); ?></h4>
                <ol>
                    <li><?php esc_html_e( '다운로드 버튼을 클릭하여 폰트 파일(TTF/OTF/WOFF2)을 받습니다.', 'acf-css-really-simple-style-management-center' ); ?></li>
                    <li><?php esc_html_e( 'WordPress 미디어 라이브러리에 폰트 파일을 업로드합니다.', 'acf-css-really-simple-style-management-center' ); ?></li>
                    <li><?php esc_html_e( '스타일 센터 > 타이포그래피 > 커스텀 폰트에서 업로드한 폰트를 연결합니다.', 'acf-css-really-simple-style-management-center' ); ?></li>
                </ol>
            </div>

            <div class="jj-font-guide-section">
                <h4><?php esc_html_e( '3. 유료 폰트 사용', 'acf-css-really-simple-style-management-center' ); ?></h4>
                <p><?php esc_html_e( '유료 폰트는 해당 제공업체에서 라이선스를 구매한 후 위의 수동 설치 방법으로 적용하세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>

            <div class="jj-font-guide-section">
                <h4><?php esc_html_e( '4. 팁', 'acf-css-really-simple-style-management-center' ); ?></h4>
                <ul>
                    <li><?php esc_html_e( '한국어 폰트는 파일 크기가 크므로 subset(필요한 글자만)을 사용하면 성능이 향상됩니다.', 'acf-css-really-simple-style-management-center' ); ?></li>
                    <li><?php esc_html_e( 'font-display: swap을 사용하면 폰트 로딩 중에도 텍스트가 표시됩니다.', 'acf-css-really-simple-style-management-center' ); ?></li>
                    <li><?php esc_html_e( 'WOFF2 형식이 가장 효율적입니다 (최신 브라우저 지원).', 'acf-css-really-simple-style-management-center' ); ?></li>
                </ul>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
