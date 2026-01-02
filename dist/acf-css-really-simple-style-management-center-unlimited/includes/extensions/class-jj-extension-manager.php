<?php
/**
 * JJ Extension Manager
 *
 * Phase 5.3: 확장 플러그인 시스템
 * - 서드파티 확장 플러그인이 필터/액션으로 확장을 등록하고, 본 플러그인이 안전하게 초기화합니다.
 *
 * @since 6.0.3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_Extension_Manager' ) ) {
    final class JJ_Extension_Manager {

        private static $instance = null;

        /**
         * @var array<string, object> id => extension instance
         */
        private $extensions = array();

        public static function instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
            if ( function_exists( 'add_action' ) ) {
                // 모든 플러그인이 로드된 뒤 확장 등록/초기화
                add_action( 'plugins_loaded', array( $this, 'load_extensions' ), 20 );
            }
        }

        /**
         * 확장 등록
         *
         * @param object $extension
         * @return bool
         */
        public function register( $extension ) {
            if ( ! is_object( $extension ) ) {
                return false;
            }

            if ( ! method_exists( $extension, 'get_id' ) ) {
                return false;
            }

            $id = (string) $extension->get_id();
            if ( '' === $id ) {
                return false;
            }

            $safe_id = function_exists( 'sanitize_key' ) ? sanitize_key( $id ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( $id ) );
            if ( '' === $safe_id ) {
                return false;
            }

            if ( isset( $this->extensions[ $safe_id ] ) ) {
                return false;
            }

            $this->extensions[ $safe_id ] = $extension;
            return true;
        }

        /**
         * 등록된 확장 목록 반환
         *
         * @return array<string, object>
         */
        public function get_extensions() {
            return $this->extensions;
        }

        /**
         * 확장 로드/초기화
         *
         * 확장 등록 방식:
         * - (권장) filter: jj_style_guide_extensions => [ 'My_Ext_Class', $instance, function(){ return new ...; } ]
         * - action: jj_style_guide_register_extensions (JJ_Extension_Manager $manager를 받아 register 호출)
         *
         * @return void
         */
        public function load_extensions() {
            // 1) Action 기반 등록
            if ( function_exists( 'do_action' ) ) {
                do_action( 'jj_style_guide_register_extensions', $this );
            }

            // 2) Filter 기반 등록
            $items = array();
            if ( function_exists( 'apply_filters' ) ) {
                $items = apply_filters( 'jj_style_guide_extensions', array() );
            }
            if ( ! is_array( $items ) ) {
                $items = array();
            }

            foreach ( $items as $item ) {
                $this->register_from_item( $item );
            }

            // 3) 확장 초기화
            foreach ( $this->extensions as $id => $ext ) {
                $this->init_extension( $id, $ext );
            }

            // 4) 완료 알림
            if ( function_exists( 'do_action' ) ) {
                do_action( 'jj_style_guide_extensions_loaded', $this->extensions );
            }
        }

        /**
         * @param mixed $item
         * @return void
         */
        private function register_from_item( $item ) {
            // 이미 인스턴스
            if ( is_object( $item ) ) {
                $this->register( $item );
                return;
            }

            // callable factory
            if ( is_callable( $item ) ) {
                try {
                    $result = call_user_func( $item );
                    if ( is_object( $result ) ) {
                        $this->register( $result );
                    }
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Extension Manager: factory error - ' . $e->getMessage() );
                    }
                } catch ( Error $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Extension Manager: factory fatal error - ' . $e->getMessage() );
                    }
                }
                return;
            }

            // class name string
            if ( is_string( $item ) && class_exists( $item ) ) {
                try {
                    if ( method_exists( $item, 'instance' ) ) {
                        $obj = call_user_func( array( $item, 'instance' ) );
                    } else {
                        $obj = new $item();
                    }
                    if ( is_object( $obj ) ) {
                        $this->register( $obj );
                    }
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Extension Manager: instantiate error - ' . $e->getMessage() );
                    }
                } catch ( Error $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Extension Manager: instantiate fatal error - ' . $e->getMessage() );
                    }
                }
            }
        }

        /**
         * @param string $id
         * @param object $ext
         * @return void
         */
        private function init_extension( $id, $ext ) {
            // 호환성 체크: 최소 버전
            if ( method_exists( $ext, 'get_min_version' ) && defined( 'JJ_STYLE_GUIDE_VERSION' ) ) {
                $min = (string) $ext->get_min_version();
                if ( '' !== $min && function_exists( 'version_compare' ) ) {
                    if ( version_compare( JJ_STYLE_GUIDE_VERSION, $min, '<' ) ) {
                        return;
                    }
                }
            }

            // 에디션 capability 체크
            if ( method_exists( $ext, 'get_required_capability' ) && class_exists( 'JJ_Edition_Controller' ) ) {
                $cap = (string) $ext->get_required_capability();
                if ( '' !== $cap ) {
                    try {
                        if ( ! JJ_Edition_Controller::instance()->has_capability( $cap ) ) {
                            return;
                        }
                    } catch ( Exception $e ) {
                        // ignore
                    } catch ( Error $e ) {
                        // ignore
                    }
                }
            }

            // 실제 init 호출
            if ( method_exists( $ext, 'init' ) ) {
                try {
                    $ext->init();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Extension Manager: init error (' . $id . ') - ' . $e->getMessage() );
                    }
                } catch ( Error $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Extension Manager: init fatal (' . $id . ') - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
}


