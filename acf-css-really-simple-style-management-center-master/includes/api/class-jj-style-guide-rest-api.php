<?php
/**
 * JJ Style Guide REST API
 *
 * Phase 5.2: API 및 통합 (REST API)
 * Phase 9.3: REST API 확장
 * - 플러그인 설정을 REST API로 조회/업데이트할 수 있도록 제공합니다.
 * - 모든 기능 API화 (팔레트, 스냅샷, 스타일 가이드 생성 등)
 *
 * @since 6.0.2
 * @since 9.3.0 - API 확장
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_Style_Guide_REST_API' ) ) {
    final class JJ_Style_Guide_REST_API {

        private static $instance = null;

        /** @var string */
        private $namespace = 'jj-style-guide/v1';

        public static function instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
            if ( function_exists( 'add_action' ) ) {
                add_action( 'rest_api_init', array( $this, 'register_routes' ) );
            }
        }

        /**
         * REST 라우트 등록
         */
        public function register_routes() {
            if ( ! function_exists( 'register_rest_route' ) ) {
                return;
            }

            register_rest_route(
                $this->namespace,
                '/info',
                array(
                    array(
                        'methods'             => 'GET',
                        'callback'            => array( $this, 'get_info' ),
                        'permission_callback' => array( $this, 'can_manage' ),
                    ),
                )
            );

            register_rest_route(
                $this->namespace,
                '/settings',
                array(
                    array(
                        'methods'             => 'GET',
                        'callback'            => array( $this, 'get_settings' ),
                        'permission_callback' => array( $this, 'can_manage' ),
                    ),
                    array(
                        'methods'             => 'POST',
                        'callback'            => array( $this, 'update_settings' ),
                        'permission_callback' => array( $this, 'can_manage' ),
                        'args'                => array(
                            'settings' => array(
                                'required' => true,
                            ),
                        ),
                    ),
                )
            );

            // [Phase 9.3] 추가 API 엔드포인트
            register_rest_route(
                $this->namespace,
                '/palettes',
                array(
                    array(
                        'methods'             => 'GET',
                        'callback'            => array( $this, 'get_palettes' ),
                        'permission_callback' => array( $this, 'can_manage' ),
                    ),
                    array(
                        'methods'             => 'POST',
                        'callback'            => array( $this, 'update_palette' ),
                        'permission_callback' => array( $this, 'can_manage' ),
                    ),
                )
            );

            register_rest_route(
                $this->namespace,
                '/snapshots',
                array(
                    array(
                        'methods'             => 'GET',
                        'callback'            => array( $this, 'get_snapshots' ),
                        'permission_callback' => array( $this, 'can_manage' ),
                    ),
                    array(
                        'methods'             => 'POST',
                        'callback'            => array( $this, 'create_snapshot' ),
                        'permission_callback' => array( $this, 'can_manage' ),
                    ),
                )
            );

            register_rest_route(
                $this->namespace,
                '/snapshots/(?P<id>[a-zA-Z0-9_-]+)',
                array(
                    array(
                        'methods'             => 'GET',
                        'callback'            => array( $this, 'get_snapshot' ),
                        'permission_callback' => array( $this, 'can_manage' ),
                    ),
                    array(
                        'methods'             => 'POST',
                        'callback'            => array( $this, 'restore_snapshot' ),
                        'permission_callback' => array( $this, 'can_manage' ),
                    ),
                )
            );

            register_rest_route(
                $this->namespace,
                '/style-guide/generate',
                array(
                    array(
                        'methods'             => 'POST',
                        'callback'            => array( $this, 'generate_style_guide' ),
                        'permission_callback' => array( $this, 'can_manage' ),
                    ),
                )
            );

            register_rest_route(
                $this->namespace,
                '/style-guide/analyze',
                array(
                    array(
                        'methods'             => 'GET',
                        'callback'            => array( $this, 'analyze_consistency' ),
                        'permission_callback' => array( $this, 'can_manage' ),
                    ),
                )
            );
        }

        /**
         * 권한 체크: 관리자만 허용
         */
        public function can_manage() {
            return function_exists( 'current_user_can' ) && current_user_can( 'manage_options' );
        }

        /**
         * 플러그인 정보 조회
         */
        public function get_info( $request ) {
            $edition = defined( 'JJ_STYLE_GUIDE_EDITION' ) ? JJ_STYLE_GUIDE_EDITION : '';
            $license = defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ? JJ_STYLE_GUIDE_LICENSE_TYPE : '';
            $version = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '';

            $data = array(
                'plugin'  => 'acf-css-style-guide',
                'version' => $version,
                'edition' => $edition,
                'license' => $license,
                'site'    => function_exists( 'home_url' ) ? home_url() : '',
            );

            if ( class_exists( 'WP_REST_Response' ) ) {
                return new WP_REST_Response( $data, 200 );
            }
            return $data;
        }

        /**
         * 설정 조회
         */
        public function get_settings( $request ) {
            $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
            $settings = function_exists( 'get_option' ) ? get_option( $key, array() ) : array();

            $payload = array(
                'settings' => is_array( $settings ) ? $settings : array(),
            );

            if ( class_exists( 'WP_REST_Response' ) ) {
                return new WP_REST_Response( $payload, 200 );
            }
            return $payload;
        }

        /**
         * 설정 업데이트
         */
        public function update_settings( $request ) {
            $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';

            $old = function_exists( 'get_option' ) ? get_option( $key, array() ) : array();
            $incoming = $request->get_param( 'settings' );

            if ( ! is_array( $incoming ) ) {
                return $this->error( 'invalid_settings', __( 'settings 파라미터는 배열(JSON Object)이어야 합니다.', 'acf-css-really-simple-style-management-center' ), 400 );
            }

            // [Phase 5 B] 멀티사이트 네트워크 전용 모드에서는 사이트별 REST 업데이트 차단
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $ms = JJ_Multisite_Controller::instance();
                    if ( method_exists( $ms, 'is_enabled' ) && method_exists( $ms, 'allow_site_override' ) ) {
                        if ( $ms->is_enabled() && ! $ms->allow_site_override() ) {
                            return $this->error( 'network_locked', __( '이 사이트는 네트워크에서 스타일을 관리 중입니다. 네트워크 관리자에서 변경해주세요.', 'acf-css-really-simple-style-management-center' ), 403 );
                        }
                    }
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }

            $new = $this->sanitize_recursive( $incoming );

            if ( function_exists( 'update_option' ) ) {
                update_option( $key, $new );
            }

            // CSS 캐시 플러시
            if ( class_exists( 'JJ_CSS_Cache' ) && method_exists( 'JJ_CSS_Cache', 'instance' ) ) {
                try {
                    $cache = JJ_CSS_Cache::instance();
                    if ( $cache && method_exists( $cache, 'flush' ) ) {
                        $cache->flush();
                    }
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ REST API: CSS cache flush error - ' . $e->getMessage() );
                    }
                } catch ( Error $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ REST API: CSS cache flush fatal error - ' . $e->getMessage() );
                    }
                }
            }

            if ( function_exists( 'do_action' ) ) {
                do_action( 'jj_style_guide_settings_updated', $new, $old, 'rest_api' );
            }

            $payload = array(
                'success'  => true,
                'message'  => __( '설정이 업데이트되었습니다.', 'acf-css-really-simple-style-management-center' ),
                'settings' => $new,
            );

            if ( class_exists( 'WP_REST_Response' ) ) {
                return new WP_REST_Response( $payload, 200 );
            }
            return $payload;
        }

        private function error( $code, $message, $status ) {
            if ( class_exists( 'WP_Error' ) ) {
                return new WP_Error( $code, $message, array( 'status' => $status ) );
            }
            return array(
                'success' => false,
                'code'    => $code,
                'message' => $message,
            );
        }

        /**
         * 간단/안전 중심 재귀 sanitize
         * - color 키: sanitize_hex_color
         * - css 키: wp_kses_post
         * - 나머지: sanitize_text_field
         */
        private function sanitize_recursive( $input ) {
            $output = array();

            foreach ( (array) $input as $key => $value ) {
                $raw_key = (string) $key;
                $safe_key = function_exists( 'sanitize_key' ) ? sanitize_key( $raw_key ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( $raw_key ) );

                if ( is_array( $value ) ) {
                    $output[ $safe_key ] = $this->sanitize_recursive( $value );
                    continue;
                }

                $string_value = is_scalar( $value ) ? (string) $value : '';

                if ( strpos( $safe_key, 'color' ) !== false ) {
                    $san = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( $string_value ) : '';
                    $output[ $safe_key ] = $san ? $san : '';
                    continue;
                }

                if ( strpos( $safe_key, 'css' ) !== false ) {
                    $output[ $safe_key ] = function_exists( 'wp_kses_post' ) ? wp_kses_post( $string_value ) : strip_tags( $string_value );
                    continue;
                }

                $output[ $safe_key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $string_value ) : strip_tags( $string_value );
            }

            return $output;
        }

        /**
         * [Phase 9.3] 팔레트 조회
         */
        public function get_palettes( $request ) {
            $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
            $options = get_option( $key, array() );
            $palettes = isset( $options['palettes'] ) ? $options['palettes'] : array();

            return new WP_REST_Response( array( 'palettes' => $palettes ), 200 );
        }

        /**
         * [Phase 9.3] 팔레트 업데이트
         */
        public function update_palette( $request ) {
            $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
            $options = get_option( $key, array() );
            
            $palette_key = $request->get_param( 'key' );
            $palette_data = $request->get_param( 'data' );

            if ( ! $palette_key || ! is_array( $palette_data ) ) {
                return $this->error( 'invalid_params', __( 'key와 data 파라미터가 필요합니다.', 'acf-css-really-simple-style-management-center' ), 400 );
            }

            if ( ! isset( $options['palettes'] ) ) {
                $options['palettes'] = array();
            }

            $options['palettes'][ $palette_key ] = $this->sanitize_recursive( $palette_data );
            update_option( $key, $options );

            return new WP_REST_Response( array(
                'success' => true,
                'message' => __( '팔레트가 업데이트되었습니다.', 'acf-css-really-simple-style-management-center' ),
            ), 200 );
        }

        /**
         * [Phase 9.3] 스냅샷 목록 조회
         */
        public function get_snapshots( $request ) {
            if ( ! class_exists( 'JJ_Version_Control' ) ) {
                return $this->error( 'not_available', __( '버전 관리 기능을 사용할 수 없습니다.', 'acf-css-really-simple-style-management-center' ), 503 );
            }

            $version_control = JJ_Version_Control::instance();
            $limit = $request->get_param( 'limit' ) ? intval( $request->get_param( 'limit' ) ) : 20;
            $versions = $version_control->get_versions( $limit );

            return new WP_REST_Response( array( 'snapshots' => $versions ), 200 );
        }

        /**
         * [Phase 9.3] 스냅샷 생성
         */
        public function create_snapshot( $request ) {
            if ( ! class_exists( 'JJ_Version_Control' ) ) {
                return $this->error( 'not_available', __( '버전 관리 기능을 사용할 수 없습니다.', 'acf-css-really-simple-style-management-center' ), 503 );
            }

            $version_control = JJ_Version_Control::instance();
            $name = $request->get_param( 'name' );
            $description = $request->get_param( 'description' );

            $snapshot = $version_control->create_snapshot( $name, $description );

            return new WP_REST_Response( array(
                'success'  => true,
                'snapshot' => $snapshot,
                'message'  => __( '스냅샷이 생성되었습니다.', 'acf-css-really-simple-style-management-center' ),
            ), 200 );
        }

        /**
         * [Phase 9.3] 스냅샷 조회
         */
        public function get_snapshot( $request ) {
            if ( ! class_exists( 'JJ_Version_Control' ) ) {
                return $this->error( 'not_available', __( '버전 관리 기능을 사용할 수 없습니다.', 'acf-css-really-simple-style-management-center' ), 503 );
            }

            $snapshot_id = $request->get_param( 'id' );
            $version_control = JJ_Version_Control::instance();
            $versions = $version_control->get_versions();

            foreach ( $versions as $version ) {
                if ( $version['id'] === $snapshot_id ) {
                    return new WP_REST_Response( array( 'snapshot' => $version ), 200 );
                }
            }

            return $this->error( 'not_found', __( '스냅샷을 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ), 404 );
        }

        /**
         * [Phase 9.3] 스냅샷 복원
         */
        public function restore_snapshot( $request ) {
            if ( ! class_exists( 'JJ_Version_Control' ) ) {
                return $this->error( 'not_available', __( '버전 관리 기능을 사용할 수 없습니다.', 'acf-css-really-simple-style-management-center' ), 503 );
            }

            $snapshot_id = $request->get_param( 'id' );
            $version_control = JJ_Version_Control::instance();
            $result = $version_control->restore_snapshot( $snapshot_id );

            if ( is_wp_error( $result ) ) {
                return $this->error( $result->get_error_code(), $result->get_error_message(), 400 );
            }

            return new WP_REST_Response( array(
                'success' => true,
                'message' => __( '스냅샷이 복원되었습니다.', 'acf-css-really-simple-style-management-center' ),
            ), 200 );
        }

        /**
         * [Phase 9.3] 스타일 가이드 생성
         */
        public function generate_style_guide( $request ) {
            if ( ! class_exists( 'JJ_Style_Guide_Generator' ) ) {
                return $this->error( 'not_available', __( '스타일 가이드 생성 기능을 사용할 수 없습니다.', 'acf-css-really-simple-style-management-center' ), 503 );
            }

            $generator = JJ_Style_Guide_Generator::instance();
            $options = array(
                'include_palettes'  => $request->get_param( 'include_palettes' ) !== false,
                'include_typography' => $request->get_param( 'include_typography' ) !== false,
                'include_buttons'  => $request->get_param( 'include_buttons' ) !== false,
                'include_forms'    => $request->get_param( 'include_forms' ) !== false,
                'format'           => $request->get_param( 'format' ) ?: 'html',
            );

            $result = $generator->generate_style_guide( $options );

            return new WP_REST_Response( $result, 200 );
        }

        /**
         * [Phase 9.3] 일관성 분석
         */
        public function analyze_consistency( $request ) {
            if ( ! class_exists( 'JJ_Style_Guide_Generator' ) ) {
                return $this->error( 'not_available', __( '일관성 분석 기능을 사용할 수 없습니다.', 'acf-css-really-simple-style-management-center' ), 503 );
            }

            $generator = JJ_Style_Guide_Generator::instance();
            $analysis = $generator->analyze_consistency();

            return new WP_REST_Response( $analysis, 200 );
        }
    }
}


