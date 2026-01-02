<?php
/**
 * JJ Style Guide Extension Interface
 *
 * Phase 5.3: 확장 플러그인 시스템
 * - 서드파티(확장 플러그인)가 이 인터페이스를 구현하여 기능을 주입할 수 있습니다.
 *
 * @since 6.0.3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! interface_exists( 'JJ_Style_Guide_Extension_Interface' ) ) {
    interface JJ_Style_Guide_Extension_Interface {
        /**
         * 확장 ID (고유, 영문/숫자/대시/언더스코어 권장)
         *
         * @return string
         */
        public function get_id();

        /**
         * 확장 표시 이름
         *
         * @return string
         */
        public function get_name();

        /**
         * 확장 초기화 (훅 등록 등)
         *
         * @return void
         */
        public function init();

        /**
         * 최소 요구 플러그인 버전 (예: 6.0.3)
         *
         * @return string
         */
        public function get_min_version();

        /**
         * 요구 capability(에디션 기능 플래그) 또는 빈 문자열
         * 예: 'admin_center_full', 'labs_center'
         *
         * @return string
         */
        public function get_required_capability();
    }
}


