<?php
/**
 * Neural Link License REST API
 * 
 * WooCommerce 연동을 위한 라이센스 발행/검증 REST API 엔드포인트
 * 
 * @package ACF_CSS_Neural_Link
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_Neural_Link_Woo_API' ) ) {

    class JJ_Neural_Link_Woo_API {

        /**
         * 싱글톤 인스턴스
         */
        private static $instance = null;

        /**
         * REST API 네임스페이스
         */
        private $namespace = 'acf-neural-link/v1';

        /**
         * 인스턴스 반환
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * 생성자
         */
        private function __construct() {
            add_action( 'rest_api_init', array( $this, 'register_routes' ) );
        }

        /**
         * REST 라우트 등록
         */
        public function register_routes() {
            // 서버 상태 확인 (ping)
            register_rest_route( $this->namespace, '/ping', array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'ping' ),
                'permission_callback' => array( $this, 'api_key_permission_check' ),
            ) );

            // 라이센스 발행
            register_rest_route( $this->namespace, '/license/issue', array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'issue_license' ),
                'permission_callback' => array( $this, 'api_key_permission_check' ),
                'args'                => $this->get_issue_license_args(),
            ) );

            // 라이센스 검증
            register_rest_route( $this->namespace, '/license/verify', array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'verify_license' ),
                'permission_callback' => '__return_true', // 공개 엔드포인트
                'args'                => array(
                    'license_key' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'site_url' => array(
                        'required'          => true,
                        'sanitize_callback' => 'esc_url_raw',
                    ),
                ),
            ) );

            // 라이센스 활성화
            register_rest_route( $this->namespace, '/license/activate', array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'activate_license' ),
                'permission_callback' => '__return_true',
                'args'                => array(
                    'license_key' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'site_url' => array(
                        'required'          => true,
                        'sanitize_callback' => 'esc_url_raw',
                    ),
                ),
            ) );

            // 라이센스 비활성화
            register_rest_route( $this->namespace, '/license/deactivate', array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'deactivate_license' ),
                'permission_callback' => '__return_true',
                'args'                => array(
                    'license_key' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'site_url' => array(
                        'required'          => true,
                        'sanitize_callback' => 'esc_url_raw',
                    ),
                ),
            ) );

            // 라이센스 정보 조회
            register_rest_route( $this->namespace, '/license/info', array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_license_info' ),
                'permission_callback' => array( $this, 'api_key_permission_check' ),
                'args'                => array(
                    'license_key' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            ) );

            // 라이센스 목록 조회 (관리자용)
            register_rest_route( $this->namespace, '/licenses', array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'list_licenses' ),
                'permission_callback' => array( $this, 'api_key_permission_check' ),
                'args'                => array(
                    'page'     => array(
                        'default'           => 1,
                        'sanitize_callback' => 'absint',
                    ),
                    'per_page' => array(
                        'default'           => 20,
                        'sanitize_callback' => 'absint',
                    ),
                    'email'    => array(
                        'sanitize_callback' => 'sanitize_email',
                    ),
                    'edition'  => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'status'   => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            ) );
        }

        /**
         * API Key 권한 확인
         */
        public function api_key_permission_check( WP_REST_Request $request ) {
            $auth_header = $request->get_header( 'Authorization' );
            
            if ( empty( $auth_header ) ) {
                return new WP_Error( 
                    'missing_api_key', 
                    __( 'API Key가 필요합니다.', 'acf-css-neural-link' ), 
                    array( 'status' => 401 ) 
                );
            }
            
            // Bearer 토큰 추출
            if ( preg_match( '/Bearer\s+(.+)$/i', $auth_header, $matches ) ) {
                $provided_key = $matches[1];
            } else {
                $provided_key = $auth_header;
            }
            
            $stored_key = get_option( 'jj_neural_link_api_key', '' );
            
            if ( empty( $stored_key ) || ! hash_equals( $stored_key, $provided_key ) ) {
                return new WP_Error( 
                    'invalid_api_key', 
                    __( '유효하지 않은 API Key입니다.', 'acf-css-neural-link' ), 
                    array( 'status' => 403 ) 
                );
            }
            
            return true;
        }

        /**
         * 라이센스 발행 인자
         */
        private function get_issue_license_args() {
            return array(
                'email' => array(
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_email',
                    'validate_callback' => function( $value ) {
                        return is_email( $value );
                    },
                ),
                'edition' => array(
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function( $value ) {
                        return in_array( $value, array( 'free', 'basic', 'premium', 'unlimited', 'partner', 'master' ), true );
                    },
                ),
                'duration' => array(
                    'default'           => 365,
                    'sanitize_callback' => 'absint',
                ),
                'site_limit' => array(
                    'default'           => 0,
                    'sanitize_callback' => 'absint',
                ),
                'order_id' => array(
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'source' => array(
                    'default'           => 'api',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            );
        }

        /**
         * Ping - 서버 상태 확인
         */
        public function ping( WP_REST_Request $request ) {
            return new WP_REST_Response( array(
                'success' => true,
                'message' => 'Neural Link Server is running',
                'version' => defined( 'JJ_NEURAL_LINK_VERSION' ) ? JJ_NEURAL_LINK_VERSION : '3.2.0',
                'time'    => current_time( 'mysql' ),
            ), 200 );
        }

        /**
         * 라이센스 발행
         */
        public function issue_license( WP_REST_Request $request ) {
            $email      = $request->get_param( 'email' );
            $edition    = $request->get_param( 'edition' );
            $duration   = (int) $request->get_param( 'duration' );
            $site_limit = (int) $request->get_param( 'site_limit' );
            $order_id   = $request->get_param( 'order_id' );
            $source     = $request->get_param( 'source' );

            // 라이센스 키 생성
            $license_key = $this->generate_license_key( $edition );

            // 만료일 계산
            $expires_at = null;
            if ( $duration > 0 ) {
                $expires_at = date( 'Y-m-d H:i:s', strtotime( "+{$duration} days" ) );
            }

            // 데이터베이스에 저장
            global $wpdb;
            $table_name = $wpdb->prefix . 'jj_licenses';

            // 테이블 존재 확인 및 생성
            $this->maybe_create_table();

            $result = $wpdb->insert(
                $table_name,
                array(
                    'license_key'   => $license_key,
                    'email'         => $email,
                    'edition'       => $edition,
                    'status'        => 'active',
                    'site_limit'    => $site_limit,
                    'sites_count'   => 0,
                    'order_id'      => $order_id,
                    'source'        => $source,
                    'created_at'    => current_time( 'mysql' ),
                    'expires_at'    => $expires_at,
                ),
                array( '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s' )
            );

            if ( false === $result ) {
                return new WP_REST_Response( array(
                    'success' => false,
                    'message' => __( '라이센스 저장 실패', 'acf-css-neural-link' ),
                    'error'   => $wpdb->last_error,
                ), 500 );
            }

            // 발행 로그 기록
            $this->log_license_event( $license_key, 'issued', array(
                'email'    => $email,
                'edition'  => $edition,
                'order_id' => $order_id,
                'source'   => $source,
            ) );

            return new WP_REST_Response( array(
                'success'     => true,
                'license_key' => $license_key,
                'edition'     => $edition,
                'expires_at'  => $expires_at,
                'site_limit'  => $site_limit,
            ), 200 );
        }

        /**
         * 라이센스 검증
         */
        public function verify_license( WP_REST_Request $request ) {
            $license_key = $request->get_param( 'license_key' );
            $site_url    = $request->get_param( 'site_url' );

            $license = $this->get_license( $license_key );

            if ( ! $license ) {
                return new WP_REST_Response( array(
                    'valid'   => false,
                    'message' => __( '라이센스를 찾을 수 없습니다.', 'acf-css-neural-link' ),
                ), 200 );
            }

            // 상태 확인
            if ( $license->status !== 'active' ) {
                return new WP_REST_Response( array(
                    'valid'   => false,
                    'message' => __( '비활성화된 라이센스입니다.', 'acf-css-neural-link' ),
                    'status'  => $license->status,
                ), 200 );
            }

            // 만료 확인
            if ( $license->expires_at && strtotime( $license->expires_at ) < time() ) {
                return new WP_REST_Response( array(
                    'valid'      => false,
                    'message'    => __( '만료된 라이센스입니다.', 'acf-css-neural-link' ),
                    'expired_at' => $license->expires_at,
                ), 200 );
            }

            // 사이트 활성화 여부 확인
            $activated_sites = $this->get_activated_sites( $license_key );
            $is_activated = in_array( $this->normalize_url( $site_url ), array_map( array( $this, 'normalize_url' ), $activated_sites ), true );

            return new WP_REST_Response( array(
                'valid'           => true,
                'activated'       => $is_activated,
                'edition'         => $license->edition,
                'expires_at'      => $license->expires_at,
                'site_limit'      => (int) $license->site_limit,
                'sites_count'     => (int) $license->sites_count,
                'activated_sites' => $activated_sites,
            ), 200 );
        }

        /**
         * 라이센스 활성화
         */
        public function activate_license( WP_REST_Request $request ) {
            $license_key = $request->get_param( 'license_key' );
            $site_url    = $request->get_param( 'site_url' );

            $license = $this->get_license( $license_key );

            if ( ! $license ) {
                return new WP_REST_Response( array(
                    'success' => false,
                    'message' => __( '라이센스를 찾을 수 없습니다.', 'acf-css-neural-link' ),
                ), 200 );
            }

            // 상태 확인
            if ( $license->status !== 'active' ) {
                return new WP_REST_Response( array(
                    'success' => false,
                    'message' => __( '비활성화된 라이센스입니다.', 'acf-css-neural-link' ),
                ), 200 );
            }

            // 만료 확인
            if ( $license->expires_at && strtotime( $license->expires_at ) < time() ) {
                return new WP_REST_Response( array(
                    'success' => false,
                    'message' => __( '만료된 라이센스입니다.', 'acf-css-neural-link' ),
                ), 200 );
            }

            // 사이트 제한 확인
            $activated_sites = $this->get_activated_sites( $license_key );
            $normalized_site = $this->normalize_url( $site_url );

            // 이미 활성화된 사이트인지 확인
            if ( in_array( $normalized_site, array_map( array( $this, 'normalize_url' ), $activated_sites ), true ) ) {
                return new WP_REST_Response( array(
                    'success' => true,
                    'message' => __( '이미 활성화된 사이트입니다.', 'acf-css-neural-link' ),
                    'edition' => $license->edition,
                ), 200 );
            }

            // 사이트 수 제한 확인 (0 = 무제한)
            if ( $license->site_limit > 0 && count( $activated_sites ) >= $license->site_limit ) {
                return new WP_REST_Response( array(
                    'success'     => false,
                    'message'     => __( '사이트 활성화 한도에 도달했습니다.', 'acf-css-neural-link' ),
                    'site_limit'  => (int) $license->site_limit,
                    'sites_count' => count( $activated_sites ),
                ), 200 );
            }

            // 사이트 활성화
            $activated_sites[] = $site_url;
            $this->update_activated_sites( $license_key, $activated_sites );

            // 사이트 카운트 업데이트
            global $wpdb;
            $wpdb->update(
                $wpdb->prefix . 'jj_licenses',
                array( 'sites_count' => count( $activated_sites ) ),
                array( 'license_key' => $license_key ),
                array( '%d' ),
                array( '%s' )
            );

            // 로그 기록
            $this->log_license_event( $license_key, 'activated', array(
                'site_url' => $site_url,
            ) );

            return new WP_REST_Response( array(
                'success'     => true,
                'message'     => __( '라이센스가 활성화되었습니다.', 'acf-css-neural-link' ),
                'edition'     => $license->edition,
                'expires_at'  => $license->expires_at,
                'sites_count' => count( $activated_sites ),
            ), 200 );
        }

        /**
         * 라이센스 비활성화
         */
        public function deactivate_license( WP_REST_Request $request ) {
            $license_key = $request->get_param( 'license_key' );
            $site_url    = $request->get_param( 'site_url' );

            $license = $this->get_license( $license_key );

            if ( ! $license ) {
                return new WP_REST_Response( array(
                    'success' => false,
                    'message' => __( '라이센스를 찾을 수 없습니다.', 'acf-css-neural-link' ),
                ), 200 );
            }

            $activated_sites = $this->get_activated_sites( $license_key );
            $normalized_site = $this->normalize_url( $site_url );

            // 활성화된 사이트 목록에서 제거
            $activated_sites = array_filter( $activated_sites, function( $s ) use ( $normalized_site ) {
                return $this->normalize_url( $s ) !== $normalized_site;
            } );
            $activated_sites = array_values( $activated_sites );

            $this->update_activated_sites( $license_key, $activated_sites );

            // 사이트 카운트 업데이트
            global $wpdb;
            $wpdb->update(
                $wpdb->prefix . 'jj_licenses',
                array( 'sites_count' => count( $activated_sites ) ),
                array( 'license_key' => $license_key ),
                array( '%d' ),
                array( '%s' )
            );

            // 로그 기록
            $this->log_license_event( $license_key, 'deactivated', array(
                'site_url' => $site_url,
            ) );

            return new WP_REST_Response( array(
                'success'     => true,
                'message'     => __( '라이센스가 비활성화되었습니다.', 'acf-css-neural-link' ),
                'sites_count' => count( $activated_sites ),
            ), 200 );
        }

        /**
         * 라이센스 정보 조회
         */
        public function get_license_info( WP_REST_Request $request ) {
            $license_key = $request->get_param( 'license_key' );
            $license = $this->get_license( $license_key );

            if ( ! $license ) {
                return new WP_REST_Response( array(
                    'success' => false,
                    'message' => __( '라이센스를 찾을 수 없습니다.', 'acf-css-neural-link' ),
                ), 404 );
            }

            return new WP_REST_Response( array(
                'success'         => true,
                'license_key'     => $license->license_key,
                'email'           => $license->email,
                'edition'         => $license->edition,
                'status'          => $license->status,
                'site_limit'      => (int) $license->site_limit,
                'sites_count'     => (int) $license->sites_count,
                'activated_sites' => $this->get_activated_sites( $license_key ),
                'order_id'        => $license->order_id,
                'source'          => $license->source,
                'created_at'      => $license->created_at,
                'expires_at'      => $license->expires_at,
            ), 200 );
        }

        /**
         * 라이센스 목록 조회
         */
        public function list_licenses( WP_REST_Request $request ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'jj_licenses';

            $page     = max( 1, (int) $request->get_param( 'page' ) );
            $per_page = min( 100, max( 1, (int) $request->get_param( 'per_page' ) ) );
            $offset   = ( $page - 1 ) * $per_page;

            $where = array( '1=1' );
            $values = array();

            if ( $request->get_param( 'email' ) ) {
                $where[] = 'email = %s';
                $values[] = $request->get_param( 'email' );
            }

            if ( $request->get_param( 'edition' ) ) {
                $where[] = 'edition = %s';
                $values[] = $request->get_param( 'edition' );
            }

            if ( $request->get_param( 'status' ) ) {
                $where[] = 'status = %s';
                $values[] = $request->get_param( 'status' );
            }

            $where_sql = implode( ' AND ', $where );

            // 총 개수
            $count_sql = "SELECT COUNT(*) FROM $table_name WHERE $where_sql";
            if ( ! empty( $values ) ) {
                $count_sql = $wpdb->prepare( $count_sql, $values );
            }
            $total = (int) $wpdb->get_var( $count_sql );

            // 데이터 조회
            $sql = "SELECT * FROM $table_name WHERE $where_sql ORDER BY created_at DESC LIMIT %d OFFSET %d";
            $values[] = $per_page;
            $values[] = $offset;
            $licenses = $wpdb->get_results( $wpdb->prepare( $sql, $values ) );

            return new WP_REST_Response( array(
                'success'    => true,
                'total'      => $total,
                'page'       => $page,
                'per_page'   => $per_page,
                'total_pages' => ceil( $total / $per_page ),
                'licenses'   => $licenses,
            ), 200 );
        }

        /**
         * 라이센스 키 생성
         */
        private function generate_license_key( $edition ) {
            $prefix = strtoupper( substr( $edition, 0, 3 ) );
            $random = strtoupper( wp_generate_password( 20, false ) );
            return sprintf( '%s-%s-%s-%s-%s', 
                $prefix,
                substr( $random, 0, 5 ),
                substr( $random, 5, 5 ),
                substr( $random, 10, 5 ),
                substr( $random, 15, 5 )
            );
        }

        /**
         * URL 정규화
         */
        private function normalize_url( $url ) {
            $url = strtolower( trim( $url ) );
            $url = preg_replace( '#^https?://#', '', $url );
            $url = preg_replace( '#^www\.#', '', $url );
            $url = rtrim( $url, '/' );
            return $url;
        }

        /**
         * 라이센스 조회
         */
        private function get_license( $license_key ) {
            global $wpdb;
            return $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}jj_licenses WHERE license_key = %s",
                $license_key
            ) );
        }

        /**
         * 활성화된 사이트 목록 조회
         */
        private function get_activated_sites( $license_key ) {
            $sites = get_option( 'jj_license_sites_' . md5( $license_key ), array() );
            return is_array( $sites ) ? $sites : array();
        }

        /**
         * 활성화된 사이트 목록 업데이트
         */
        private function update_activated_sites( $license_key, $sites ) {
            update_option( 'jj_license_sites_' . md5( $license_key ), $sites );
        }

        /**
         * 라이센스 이벤트 로그
         */
        private function log_license_event( $license_key, $event, $data = array() ) {
            $log = array(
                'license_key' => $license_key,
                'event'       => $event,
                'data'        => $data,
                'ip'          => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '',
                'user_agent'  => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',
                'timestamp'   => current_time( 'mysql' ),
            );

            $logs = get_option( 'jj_license_logs', array() );
            array_unshift( $logs, $log );
            $logs = array_slice( $logs, 0, 1000 ); // 최근 1000개만 유지
            update_option( 'jj_license_logs', $logs );
        }

        /**
         * 라이센스 테이블 생성
         */
        private function maybe_create_table() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'jj_licenses';
            $charset_collate = $wpdb->get_charset_collate();

            if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
                $sql = "CREATE TABLE $table_name (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    license_key varchar(50) NOT NULL,
                    email varchar(100) NOT NULL,
                    edition varchar(20) NOT NULL,
                    status varchar(20) NOT NULL DEFAULT 'active',
                    site_limit int(11) NOT NULL DEFAULT 0,
                    sites_count int(11) NOT NULL DEFAULT 0,
                    order_id varchar(50) DEFAULT NULL,
                    source varchar(50) DEFAULT 'manual',
                    created_at datetime NOT NULL,
                    expires_at datetime DEFAULT NULL,
                    PRIMARY KEY (id),
                    UNIQUE KEY license_key (license_key),
                    KEY email (email),
                    KEY edition (edition),
                    KEY status (status)
                ) $charset_collate;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );
            }
        }
    }
}

// 초기화
add_action( 'init', array( 'JJ_Neural_Link_Woo_API', 'instance' ) );

