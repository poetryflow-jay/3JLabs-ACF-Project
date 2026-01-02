<?php
if ( ! defined( 'ABSPATH' ) ) exit; // 직접 접근 차단

/**
 * [v1.9.0-beta5 '재탄생'] '조종석' 양방향 동기화 관리자
 * - [v1.9.0-beta5 '수리'] '최종 제련' 오류: 파일 끝에 있던 불필요한 '}' 괄호 제거 (치명적인 오류 '진범')
 * - [v1.9.0-beta3] '조종석' (페이지) 저장 시: '블록 파싱' -> '임시' DB (Parse Out)
 */
final class JJ_Sync_Manager {

    private static $instance = null;
    private $options = array();
    private $cockpit_page_id = 0;
    
    // [v1.9.0] '허브' DB (읽기/쓰기)와 '임시' DB (쓰기 전용) 키
    private $hub_options_key = '';
    private $temp_options_key = '';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 생성자: 상수를 속성에 할당
     */
    private function __construct() {
        // 상수 정의 확인 후 할당
        if ( defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) {
            $this->hub_options_key = JJ_STYLE_GUIDE_OPTIONS_KEY;
        }
        if ( defined( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY' ) ) {
            $this->temp_options_key = JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY;
        }
    }

    /**
     * 동기화 엔진 초기화 (v1.9.0 '재탄생')
     */
    public function init( $options, $cockpit_page_id ) {
        $this->options = $options;
        $this->cockpit_page_id = $cockpit_page_id;

        // 3. '조종석' 페이지('Style-Guide-Setting') 저장(save_post_page) 시 -> '임시' DB로 쓰기 (Parse Out)
        add_action( 'save_post_page', array( $this, 'parse_from_cockpit' ), 20, 2 );
    }

    /**
     * [v1.9.0 - 핵심] '조종석' 페이지 저장 시 실행 (Parse Out: Cockpit -> Temp DB)
     */
    public function parse_from_cockpit( $post_id, $post ) {
        if ( $post_id !== $this->cockpit_page_id ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

        // 4. [v1.9.0 '제련'] 블록 HTML 콘텐츠를 파싱하여 $temp_options 배열로 변환
        $temp_options = $this->parse_block_content_to_options( $post->post_content );

        // 5. '임시' DB('jj_style_guide_temp_options')에 저장
        update_option( $this->temp_options_key, $temp_options );
    }

    /**
     * [v1.9.0 '제련'] '조종석' 페이지의 블록 HTML을 파싱하는 '블록 파서' 엔진
     */
    private function parse_block_content_to_options( $block_html ) {
        
        $blocks = parse_blocks( $block_html );
        
        // 2. '임시' 옵션 배열을 '현재 허브' 값으로 초기화 (파싱 실패 대비)
        $temp_options = (array) get_option( $this->hub_options_key, array() );

        // 3. 재귀적으로 블록을 탐색하며 스타일 값을 '추출'
        $parsed_values = $this->parse_blocks_recursive_helper( $blocks );

        // 4. [v1.9.0 '제련'] 추출된 'Gutenberg 스타일'을 '허브 DB 구조'로 '매핑'
        // (v1.9.0-beta3에서는 핵심 색상만 '매핑'하여 '개념 증명(POC)'을 수행합니다)
        
        if ( ! empty( $parsed_values['jj-preview-color-primary'] ) ) {
            $temp_options['palettes']['brand']['primary_color'] = $parsed_values['jj-preview-color-primary'];
        }
        if ( ! empty( $parsed_values['jj-preview-color-primary-hover'] ) ) {
            $temp_options['palettes']['brand']['primary_color_hover'] = $parsed_values['jj-preview-color-primary-hover'];
        }
        if ( ! empty( $parsed_values['jj-preview-color-secondary'] ) ) {
            $temp_options['palettes']['brand']['secondary_color'] = $parsed_values['jj-preview-color-secondary'];
        }
        if ( ! empty( $parsed_values['jj-preview-color-secondary-hover'] ) ) {
            $temp_options['palettes']['brand']['secondary_color_hover'] = $parsed_values['jj-preview-color-secondary-hover'];
        }
        
        // (향후 'jj-preview-h1' 블록의 'fontSize', 'fontFamily' 등을 파싱하여 $temp_options['typography']['h1']... 에 '매핑'하는 로직 추가)

        // 5. 파싱된 최종 옵션 배열을 반환
        return $temp_options;
    }

    /**
     * [v1.9.0 '제련'] '블록 파서'의 재귀 탐색기
     */
    private function parse_blocks_recursive_helper( $blocks ) {
        $extracted_values = array();

        foreach ( $blocks as $block ) {
            $class_name = $block['attrs']['className'] ?? '';

            // --- 1. 색상 팔레트 '파싱' ---
            if ( strpos( $class_name, 'jj-preview-color-primary' ) !== false ) {
                $bg_color = $block['attrs']['style']['color']['background'] ?? null;
                if ( $bg_color ) {
                    $extracted_values['jj-preview-color-primary'] = $bg_color;
                }
            }
            if ( strpos( $class_name, 'jj-preview-color-primary-hover' ) !== false ) {
                $bg_color = $block['attrs']['style']['color']['background'] ?? null;
                if ( $bg_color ) {
                    $extracted_values['jj-preview-color-primary-hover'] = $bg_color;
                }
            }
            if ( strpos( $class_name, 'jj-preview-color-secondary' ) !== false ) {
                $bg_color = $block['attrs']['style']['color']['background'] ?? null;
                if ( $bg_color ) {
                    $extracted_values['jj-preview-color-secondary'] = $bg_color;
                }
            }
            if ( strpos( $class_name, 'jj-preview-color-secondary-hover' ) !== false ) {
                $bg_color = $block['attrs']['style']['color']['background'] ?? null;
                if ( $bg_color ) {
                    $extracted_values['jj-preview-color-secondary-hover'] = $bg_color;
                }
            }

            // --- 2. 타이포그래피 '파싱' ---
            if ( strpos( $class_name, 'jj-preview-h1' ) !== false ) {
                // (향후 '제련' 예정)
            }
            
            // --- 3. 버튼 '파싱' ---
            if ( strpos( $class_name, 'jj-preview-button-primary' ) !== false ) {
                // (향후 '제련' 예정)
            }

            // --- 4. '내부 블록(InnerBlocks)' 재귀 탐색 ---
            if ( ! empty( $block['innerBlocks'] ) ) {
                $extracted_values = array_merge( $extracted_values, $this->parse_blocks_recursive_helper( $block['innerBlocks'] ) );
            }
        }

        return $extracted_values;
    }
}
// [v1.9.0-beta5 '수리'] '진범'이었던 불필요한 '}' 괄호 '제거'
// } // <-- '이' '괄호'를 '제거'했습니다.