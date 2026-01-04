<?php
/**
 * JJ Shared Loader - 공통 유틸리티 로더
 *
 * 3J Labs 공통 PHP 유틸리티 클래스들을 로드하는 헬퍼 클래스입니다.
 * 각 플러그인에서 이 로더를 통해 공통 클래스들을 사용할 수 있습니다.
 *
 * 사용 예시:
 * ```php
 * // 플러그인 초기화 시
 * $shared_path = WP_PLUGIN_DIR . '/acf-css-really-simple-style-management-center-master/shared-ui-assets/php/';
 * if ( file_exists( $shared_path . 'class-jj-shared-loader.php' ) ) {
 *     require_once $shared_path . 'class-jj-shared-loader.php';
 *     JJ_Shared_Loader::load_all();
 * }
 * ```
 *
 * @package    3J_Labs_Shared
 * @subpackage Core
 * @since      1.0.0
 * @author     3J Labs (제이x제니x제이슨 연구소)
 * @link       https://3j-labs.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_Shared_Loader' ) ) {

    /**
     * 공통 유틸리티 로더 클래스
     *
     * @since 1.0.0
     */
    class JJ_Shared_Loader {

        /**
         * 공유 라이브러리 버전
         *
         * @var string
         */
        const VERSION = '1.0.0';

        /**
         * 로드된 파일 목록
         *
         * @var array
         */
        private static $loaded = array();

        /**
         * 기본 경로
         *
         * @var string|null
         */
        private static $base_path = null;

        /**
         * 기본 경로 설정
         *
         * @since 1.0.0
         * @param string $path 공유 PHP 파일들이 위치한 디렉토리 경로
         */
        public static function set_base_path( $path ) {
            self::$base_path = trailingslashit( $path );
        }

        /**
         * 기본 경로 반환
         *
         * @since 1.0.0
         * @return string
         */
        public static function get_base_path() {
            if ( is_null( self::$base_path ) ) {
                self::$base_path = plugin_dir_path( __FILE__ );
            }
            return self::$base_path;
        }

        /**
         * 모든 공통 유틸리티 로드
         *
         * @since 1.0.0
         */
        public static function load_all() {
            self::load( 'trait-jj-singleton' );
            self::load( 'class-jj-ajax-helper' );
            self::load( 'class-jj-file-validator' );
        }

        /**
         * 특정 유틸리티 로드
         *
         * @since 1.0.0
         * @param string $name 파일 이름 (확장자 제외)
         * @return bool 로드 성공 여부
         */
        public static function load( $name ) {
            // 이미 로드됨
            if ( isset( self::$loaded[ $name ] ) ) {
                return true;
            }

            $file_path = self::get_base_path() . $name . '.php';

            if ( file_exists( $file_path ) ) {
                require_once $file_path;
                self::$loaded[ $name ] = true;
                return true;
            }

            return false;
        }

        /**
         * 특정 유틸리티가 로드되었는지 확인
         *
         * @since 1.0.0
         * @param string $name 파일 이름 (확장자 제외)
         * @return bool
         */
        public static function is_loaded( $name ) {
            return isset( self::$loaded[ $name ] );
        }

        /**
         * 로드된 유틸리티 목록 반환
         *
         * @since 1.0.0
         * @return array
         */
        public static function get_loaded() {
            return array_keys( self::$loaded );
        }

        /**
         * AJAX 헬퍼 인스턴스 반환 (편의 메서드)
         *
         * @since 1.0.0
         * @return JJ_Ajax_Helper|null
         */
        public static function ajax() {
            if ( ! self::is_loaded( 'class-jj-ajax-helper' ) ) {
                self::load( 'class-jj-ajax-helper' );
            }

            if ( class_exists( 'JJ_Ajax_Helper' ) ) {
                return JJ_Ajax_Helper::instance();
            }

            return null;
        }

        /**
         * 파일 검증기 인스턴스 반환 (편의 메서드)
         *
         * @since 1.0.0
         * @return JJ_File_Validator|null
         */
        public static function file_validator() {
            if ( ! self::is_loaded( 'class-jj-file-validator' ) ) {
                self::load( 'class-jj-file-validator' );
            }

            if ( class_exists( 'JJ_File_Validator' ) ) {
                return JJ_File_Validator::instance();
            }

            return null;
        }
    }
}
