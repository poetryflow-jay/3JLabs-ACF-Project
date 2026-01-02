<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [v2.0.0-alpha2 '재탄생'] '전략 3: Dequeue' ('폐기')
 * - [원칙 1] 'v2.0.0' '설계'에 따라 '힘에 의한 장악'('!important')을 '폐기'
 * - 'jj-main-styles.css' 파일의 'Enqueue' 로직을 '제거' (주석 처리)
 * - '모든' '장악' 로직은 'v2.0.0'의 '핵심 심장'인 '전략 1'('class-jj-strategy-1-css-vars.php')로 '통합'
 */
final class JJ_Strategy_3_Dequeue {
    private static $instance = null;
    private $options = array();

    public static function instance() {
        if ( is_null( self::$instance ) ) self::$instance = new self();
        return self::$instance;
    }

    public function init( $options ) {
        $this->options = $options;
        // [v2.0.0 '수정'] '우선순위 999'로 '실행'은 '유지'하되, '내용'을 '제거'
        add_action( 'wp_enqueue_scripts', array( $this, 'strategy_3_dequeue_and_enqueue' ), 999 );
    }

    public function strategy_3_dequeue_and_enqueue() {
        
        // === [ 1. Dequeue (제거) ] ===
        // (v1.9.0과 동일: '장악' 전략을 '우선'시)
        

        // === [ 2. Enqueue (우리의 '무기'를 '장착') ] ===
        
        // [v2.0.0 '폐기'] 'jj-main-styles.css' 파일을 '비활성화'합니다.
        // 'v2.0.0'부터는 '모든' CSS '장악' 로직이
        // 'class-jj-strategy-1-css-vars.php'의 '인라인 스타일'로 '통합'됩니다.
        
        /*
        $dependency_handles = array(
            'kadence-global',       // Kadence 테마
            'woocommerce-general',  // WooCommerce
            'learndash-front',      // LearnDash
            'learndash_style',      // LearnDash
            'um_styles',            // Ultimate Member
            'wp-block-library',     // 워드프레스 코어 블록
        );

        wp_enqueue_style(
            'jj-style-guide-main-override', // 핸들 이름
            JJ_STYLE_GUIDE_URL . 'assets/css/jj-main-styles.css',
            $dependency_handles, // '의존성': 이 스타일들이 로드된 '후에' 우리 파일을 로드
            JJ_STYLE_GUIDE_VERSION
        );
        */
    }
}