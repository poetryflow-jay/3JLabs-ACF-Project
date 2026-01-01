<?php
/**
 * Safe Loader Class
 * 
 * Prevents fatal errors during file loading by checking existence and handling exceptions.
 * 
 * @package JJ_Style_Guide
 * @since 5.4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_Safe_Loader' ) ) {
    class JJ_Safe_Loader {
        private static $instance = null;
        private $loaded_files = array();

        /**
         * 로더 진단 정보 (런타임 누적)
         * - safe_require는 static이므로 static 저장소로 기록합니다.
         *
         * @var array<string,array{path:string,required:bool,loaded:bool,error:string}>
         */
        private static $attempts = array();

        /**
         * @var array<string,array{path:string,required:bool,count:int}>
         */
        private static $missing_files = array();

        /**
         * @var array<string,array{path:string,required:bool,error:string}>
         */
        private static $load_errors = array();

        /**
         * [Phase 8.0] 필수 파일 매니페스트
         * 로드 순서를 보장하고 누락 시 자가진단 제공
         * 
         * @var array<int,array{path:string,required:bool,order:int,reason:string}>
         */
        private static $file_manifest = array();

        /**
         * [Phase 8.0] 로드 순서 정의
         * 
         * @var array<string,int>
         */
        private static $load_order = array(
            // 최우선: 기본 상수 및 Safe Loader
            'includes/class-jj-safe-loader.php' => 10,
            // 핵심 인프라
            'includes/class-jj-edition-controller.php' => 20,
            'includes/class-jj-version-features.php' => 21,
            'includes/class-jj-error-handler.php' => 22,
            'includes/class-jj-error-logger.php' => 23,
            // 캐시 및 메모리 관리
            'includes/class-jj-memory-manager.php' => 30,
            'includes/class-jj-options-cache.php' => 31,
            'includes/class-jj-css-cache.php' => 32,
            // CSS 인젝션
            'includes/class-jj-css-injector.php' => 40,
            'includes/class-jj-selector-registry.php' => 41,
            // 전략 클래스들
            'includes/class-jj-strategy-1-css-vars.php' => 50,
            'includes/class-jj-strategy-2-php-filters.php' => 51,
            'includes/class-jj-strategy-3-dequeue.php' => 52,
            'includes/class-jj-strategy-0-customizer.php' => 53,
            // 관리자 인터페이스
            'includes/class-jj-admin-center.php' => 60,
            // API 및 확장
            'includes/api/class-jj-style-guide-rest-api.php' => 70,
            'includes/extensions/interface-jj-style-guide-extension.php' => 71,
            'includes/extensions/class-jj-extension-manager.php' => 72,
            // 통합
            'includes/integrations/class-jj-webhook-manager.php' => 80,
            'includes/multisite/class-jj-multisite-controller.php' => 81,
        );

        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public static function safe_require( $path, $required = true ) {
            $path = (string) $path;
            $required = (bool) $required;

            $attempt = array(
                'path'     => $path,
                'required' => $required,
                'loaded'   => false,
                'error'    => '',
            );

            if ( ! file_exists( $path ) ) {
                $key = md5( $path . '|' . ( $required ? '1' : '0' ) );
                if ( ! isset( self::$missing_files[ $key ] ) ) {
                    self::$missing_files[ $key ] = array(
                        'path'     => $path,
                        'required' => $required,
                        'count'    => 1,
                    );
                } else {
                    self::$missing_files[ $key ]['count']++;
                }

                $attempt['error'] = 'file_not_found';
                self::$attempts[ $path ] = $attempt;

                if ( $required && function_exists( 'error_log' ) ) {
                    error_log( 'JJ Safe Loader: File not found - ' . $path );
                }
                return false;
            }

            try {
                require_once $path;
                $attempt['loaded'] = true;
                self::$attempts[ $path ] = $attempt;
                return true;
            } catch ( Exception $e ) {
                $attempt['error'] = $e->getMessage();
                self::$attempts[ $path ] = $attempt;
                self::$load_errors[ $path ] = array(
                    'path'     => $path,
                    'required' => $required,
                    'error'    => $attempt['error'],
                );
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'JJ Safe Loader: Exception loading ' . $path . ' - ' . $e->getMessage() );
                }
                return false;
            } catch ( Error $e ) {
                $attempt['error'] = $e->getMessage();
                self::$attempts[ $path ] = $attempt;
                self::$load_errors[ $path ] = array(
                    'path'     => $path,
                    'required' => $required,
                    'error'    => $attempt['error'],
                );
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'JJ Safe Loader: Fatal Error loading ' . $path . ' - ' . $e->getMessage() );
                }
                return false;
            }
        }

        /**
         * @return array<int,array{path:string,required:bool,loaded:bool,error:string}>
         */
        public static function get_attempts() {
            return array_values( self::$attempts );
        }

        /**
         * @return array<int,array{path:string,required:bool,count:int}>
         */
        public static function get_missing_files() {
            return array_values( self::$missing_files );
        }

        /**
         * @return array<int,array{path:string,required:bool,error:string}>
         */
        public static function get_load_errors() {
            return array_values( self::$load_errors );
        }

        /**
         * [Phase 8.0] 필수 파일 매니페스트 검증
         * 
         * @param string $base_path 플러그인 기본 경로
         * @return array{valid:bool,missing_required:array,missing_optional:array,errors:array}
         */
        public static function validate_manifest( $base_path = '' ) {
            if ( empty( $base_path ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
                $base_path = JJ_STYLE_GUIDE_PATH;
            }
            if ( empty( $base_path ) ) {
                return array(
                    'valid' => false,
                    'missing_required' => array(),
                    'missing_optional' => array(),
                    'errors' => array( 'Base path not defined' ),
                );
            }

            $base_path = rtrim( $base_path, '/\\' ) . DIRECTORY_SEPARATOR;
            $missing_required = array();
            $missing_optional = array();
            $errors = array();

            // 매니페스트 순서대로 검증
            $manifest_files = array();
            foreach ( self::$load_order as $rel_path => $order ) {
                $full_path = $base_path . str_replace( '/', DIRECTORY_SEPARATOR, $rel_path );
                $required = ( $order <= 60 ); // 관리자 인터페이스 이전은 필수
                
                if ( ! file_exists( $full_path ) ) {
                    $item = array(
                        'path' => $rel_path,
                        'full_path' => $full_path,
                        'required' => $required,
                        'order' => $order,
                        'reason' => 'file_not_found',
                    );
                    
                    if ( $required ) {
                        $missing_required[] = $item;
                    } else {
                        $missing_optional[] = $item;
                    }
                } else {
                    // 파일은 있지만 읽을 수 있는지 확인
                    if ( ! is_readable( $full_path ) ) {
                        $errors[] = array(
                            'path' => $rel_path,
                            'full_path' => $full_path,
                            'error' => 'file_not_readable',
                        );
                    }
                }
            }

            return array(
                'valid' => empty( $missing_required ) && empty( $errors ),
                'missing_required' => $missing_required,
                'missing_optional' => $missing_optional,
                'errors' => $errors,
                'checked_count' => count( self::$load_order ),
                'missing_count' => count( $missing_required ) + count( $missing_optional ),
            );
        }

        /**
         * [Phase 8.0] 자가진단 리포트 생성
         * 누락 파일, 오류 정보, 해결 방법 제안
         * 
         * @return array{diagnosis:array,solutions:array,can_repair:bool}
         */
        public static function generate_diagnosis() {
            $diagnosis = array();
            $solutions = array();
            
            // 매니페스트 검증
            $manifest_result = self::validate_manifest();
            $diagnosis['manifest'] = $manifest_result;
            
            // 누락된 필수 파일이 있으면 해결 방법 제안
            if ( ! empty( $manifest_result['missing_required'] ) ) {
                $solutions[] = array(
                    'type' => 'repair',
                    'title' => __( '누락된 필수 파일 복구', 'acf-css-really-simple-style-management-center' ),
                    'description' => sprintf(
                        __( '%d개의 필수 파일이 누락되었습니다. 복구를 시도할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                        count( $manifest_result['missing_required'] )
                    ),
                    'action' => 'repair_missing_files',
                    'action_url' => '',
                );
                
                $solutions[] = array(
                    'type' => 'reinstall',
                    'title' => __( '플러그인 재설치', 'acf-css-really-simple-style-management-center' ),
                    'description' => __( '플러그인을 비활성화하고 최신 버전으로 재설치하세요.', 'acf-css-really-simple-style-management-center' ),
                    'action' => 'reinstall_plugin',
                    'action_url' => admin_url( 'plugins.php' ),
                );
            }

            // 로드 오류 분석
            $load_errors = self::get_load_errors();
            if ( ! empty( $load_errors ) ) {
                $diagnosis['load_errors'] = $load_errors;
                $solutions[] = array(
                    'type' => 'check_syntax',
                    'title' => __( 'PHP 문법 오류 확인', 'acf-css-really-simple-style-management-center' ),
                    'description' => __( '로드 오류가 있는 파일의 PHP 문법을 확인하세요.', 'acf-css-really-simple-style-management-center' ),
                    'action' => 'check_php_syntax',
                    'action_url' => '',
                );
            }

            // 복구 가능 여부 판단
            $can_repair = ! empty( $manifest_result['missing_required'] ) && 
                         defined( 'JJ_STYLE_GUIDE_PATH' ) &&
                         is_writable( JJ_STYLE_GUIDE_PATH . 'includes' );

            return array(
                'diagnosis' => $diagnosis,
                'solutions' => $solutions,
                'can_repair' => $can_repair,
                'timestamp' => current_time( 'mysql' ),
            );
        }

        /**
         * [Phase 8.0] 최소 부팅 경로 확인
         * Activation/Update 시 최소한의 파일만 로드하여 사이트 다운 방지
         * 
         * @return array{files:array,can_boot:bool,reason:string}
         */
        public static function get_minimal_boot_path() {
            $base_path = defined( 'JJ_STYLE_GUIDE_PATH' ) ? JJ_STYLE_GUIDE_PATH : '';
            if ( empty( $base_path ) ) {
                return array(
                    'files' => array(),
                    'can_boot' => false,
                    'reason' => 'JJ_STYLE_GUIDE_PATH not defined',
                );
            }

            // 최소 부팅에 필요한 파일만 (순서 중요)
            $minimal_files = array(
                'includes/class-jj-safe-loader.php',
                'includes/class-jj-error-handler.php',
                'includes/class-jj-error-logger.php',
            );

            $can_boot = true;
            $missing = array();
            
            foreach ( $minimal_files as $rel_path ) {
                $full_path = $base_path . str_replace( '/', DIRECTORY_SEPARATOR, $rel_path );
                if ( ! file_exists( $full_path ) || ! is_readable( $full_path ) ) {
                    $can_boot = false;
                    $missing[] = $rel_path;
                }
            }

            return array(
                'files' => $minimal_files,
                'can_boot' => $can_boot,
                'missing' => $missing,
                'reason' => $can_boot ? 'ok' : ( 'Missing: ' . implode( ', ', $missing ) ),
            );
        }
    }
}

