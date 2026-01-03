<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Design Conflict Detector
 * - Marketing Value: "Peace of mind" for users
 * - Detects theme overrides and plugin conflicts
 */
class JJ_Conflict_Detector {
    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 현재 환경의 잠재적 충돌 진단
     */
    public function run_diagnosis() {
        $issues = array();

        // 1. 타사 CSS 플러그인 확인
        $plugins = get_option( 'active_plugins' );
        $conflict_plugins = array(
            'simple-custom-css/simple-custom-css.php' => 'Simple Custom CSS',
            'custom-css-js/custom-css-js.php'         => 'Simple Custom CSS and JS',
            'yellow-pencil-visual-theme-customizer/yellow-pencil.php' => 'YellowPencil'
        );

        foreach ( $conflict_plugins as $slug => $name ) {
            if ( in_array( $slug, $plugins ) ) {
                $issues[] = array(
                    'type'    => 'warning',
                    'title'   => '플러그인 중복 감지',
                    'message' => sprintf( '"%s" 플러그인이 활성화되어 있어 스타일 충돌이 발생할 수 있습니다.', $name ),
                    'fix'     => '설정에서 우선순위(Priority)를 높여보세요.'
                );
            }
        }

        // 2. 테마 !important 남용 체크 (가상 체크)
        // 실제로는 스타일시트를 파싱해야 하지만, 여기선 데모용으로 간단히 구현
        
        return $issues;
    }
}
