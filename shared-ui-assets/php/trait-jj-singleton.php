<?php
/**
 * JJ Singleton Trait - 싱글톤 패턴 재사용 트레이트
 *
 * 모든 3J Labs 플러그인에서 싱글톤 패턴을 구현할 때 사용하는 트레이트입니다.
 * 중복된 싱글톤 코드를 제거하고 일관된 인스턴스 관리를 제공합니다.
 *
 * 사용 예시:
 * ```php
 * class My_Plugin_Class {
 *     use JJ_Singleton_Trait;
 *
 *     protected function __construct() {
 *         // 초기화 로직
 *     }
 * }
 *
 * // 사용
 * $instance = My_Plugin_Class::instance();
 * ```
 *
 * @package    3J_Labs_Shared
 * @subpackage Traits
 * @since      1.0.0
 * @author     3J Labs (제이x제니x제이슨 연구소)
 * @link       https://3j-labs.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! trait_exists( 'JJ_Singleton_Trait' ) ) {

    /**
     * 싱글톤 패턴 트레이트
     *
     * 이 트레이트를 사용하면 클래스가 애플리케이션 전체에서
     * 단 하나의 인스턴스만 가지도록 보장합니다.
     *
     * @since 1.0.0
     */
    trait JJ_Singleton_Trait {

        /**
         * 싱글톤 인스턴스 저장소
         *
         * @var static|null
         */
        private static $instance = null;

        /**
         * 싱글톤 인스턴스 반환
         *
         * 인스턴스가 존재하지 않으면 새로 생성하고,
         * 존재하면 기존 인스턴스를 반환합니다.
         *
         * @since 1.0.0
         * @return static 클래스 인스턴스
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new static();
            }
            return self::$instance;
        }

        /**
         * 인스턴스 존재 여부 확인
         *
         * @since 1.0.0
         * @return bool 인스턴스 존재 여부
         */
        public static function has_instance() {
            return ! is_null( self::$instance );
        }

        /**
         * 인스턴스 초기화 (테스트용)
         *
         * 주의: 이 메서드는 단위 테스트에서만 사용해야 합니다.
         * 운영 환경에서 사용하면 예기치 않은 동작이 발생할 수 있습니다.
         *
         * @since 1.0.0
         * @internal
         */
        public static function reset_instance() {
            self::$instance = null;
        }

        /**
         * 복제 방지
         *
         * 싱글톤 인스턴스의 복제를 방지합니다.
         *
         * @since 1.0.0
         */
        private function __clone() {
            // 복제 방지
        }

        /**
         * 역직렬화 방지
         *
         * 싱글톤 인스턴스의 역직렬화를 방지합니다.
         *
         * @since 1.0.0
         * @throws Exception 항상 예외 발생
         */
        public function __wakeup() {
            throw new Exception( 'Cannot unserialize a singleton.' );
        }
    }
}
