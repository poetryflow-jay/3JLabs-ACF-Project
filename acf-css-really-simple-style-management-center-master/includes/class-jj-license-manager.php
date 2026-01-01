<?php
/**
 * J&J 라이센스 관리자
 * 
 * 라이센스 키 검증, 사이트 바인딩, 기능 제한 등을 관리합니다.
 * 
 * @version 3.8.0
 * - 라이센스 키 구조: JJ-[VERSION]-[TYPE]-[RANDOM]-[CHECKSUM]
 * - 온라인/오프라인 검증 방식
 * - 사이트 바인딩 및 보안 장치
 */
if ( ! defined( 'ABSPATH' ) ) exit;

final class JJ_License_Manager {

    private static $instance = null;
    private $license_key_option = 'jj_style_guide_license_key';
    private $license_status_option = 'jj_style_guide_license_status';
    private $license_data_option = 'jj_style_guide_license_data';
    private $site_binding_option = 'jj_style_guide_site_binding';
    
    // 라이센스 타입 상수
    const TYPE_FREE = 'FREE';
    const TYPE_BASIC = 'BASIC';
    const TYPE_PREMIUM = 'PREM';
    const TYPE_UNLIMITED = 'UNLIM';
    
    // 라이센스 검증 서버 URL (설정에서 가져오거나 기본값 사용)
    private $license_server_url = '';
    
    // 오프라인 검증 캐시 기간 (일)
    private $offline_cache_days = 30;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 라이센스 서버 URL 초기화
        $this->init_license_server_url();
        
        // 라이센스 타입 상수 정의 (이미 정의되어 있지 않은 경우)
        if ( ! defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            // 라이센스 키에서 타입 읽기
            $license_key = get_option( $this->license_key_option, '' );
            if ( $license_key ) {
                $license_type = $this->parse_license_type( $license_key );
                
                // 보안 강화: 외부 서버 검증 결과와 일치하는지 확인
                $verified_type = $this->verify_license_type_with_server( $license_type );
                define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', $verified_type );
            } else {
                // 라이센스 키가 없으면 FREE 버전
                define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', self::TYPE_FREE );
            }
        } else {
            // 이미 정의된 경우에도 검증 수행 (보안 강화)
            $hardcoded_type = JJ_STYLE_GUIDE_LICENSE_TYPE;
            $verified_type = $this->verify_license_type_with_server( $hardcoded_type );
            
            // 검증 결과가 다르면 재정의 (서버 검증 결과 우선)
            if ( $verified_type !== $hardcoded_type ) {
                // 상수는 재정의할 수 없으므로, 런타임 검증 강제
                // 실제 기능 제한은 get_current_license_type()에서 검증된 값 사용
            }
        }
    }
    
    /**
     * 외부 서버에서 라이센스 타입 검증
     * 
     * @param string $local_type 로컬에서 파싱한 라이센스 타입
     * @return string 검증된 라이센스 타입
     */
    private function verify_license_type_with_server( $local_type ) {
        // Partner/Master(내부/파트너용)에서는 서버 검증을 건너뜁니다.
        // - 배포/실험 과정에서 라이센스 타입이 고정될 수 있으며, 내부 툴이 영향을 받지 않도록 함
        $is_partner_or_higher = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        }
        if ( $is_partner_or_higher ) {
            return $local_type;
        }
        
        $license_key = get_option( $this->license_key_option, '' );
        if ( empty( $license_key ) ) {
            return self::TYPE_FREE;
        }
        
        // 외부 서버에서 검증 (캐시 사용)
        $license_status = $this->get_license_status();
        
        // 서버 검증 결과가 있으면 우선 사용
        if ( isset( $license_status['type'] ) && $license_status['valid'] ) {
            $server_type = $license_status['type'];
            
            // 서버 타입과 로컬 타입이 다르면 서버 결과 우선
            // 이는 파일이 수정되었을 가능성을 나타냄
            if ( $server_type !== $local_type ) {
                // 보안 로그 기록 (보안 강화 클래스가 있으면 사용)
                if ( class_exists( 'JJ_License_Security_Hardening' ) ) {
                    $security = JJ_License_Security_Hardening::instance();
                    // 로그는 클래스 내부에서 기록됨
                }
                
                return $server_type; // 서버 검증 결과 우선
            }
        }
        
        return $local_type;
    }
    
    /**
     * 라이센스 서버 URL 초기화
     */
    private function init_license_server_url() {
        // 기본값: https://j-j-labs.com/
        $default_server_url = 'https://j-j-labs.com/';
        
        // 설정에서 개발자 서버 URL 가져오기
        $developer_server_url = get_option( 'jj_license_manager_server_url', $default_server_url );
        
        // URL이 비어있거나 기본값이면 기본값 사용
        if ( empty( $developer_server_url ) ) {
            $developer_server_url = $default_server_url;
        }
        
        // URL 끝에 슬래시가 없으면 추가
        $developer_server_url = untrailingslashit( $developer_server_url ) . '/';
        
        // API 엔드포인트 경로 추가
        $this->license_server_url = trailingslashit( $developer_server_url ) . 'wp-json/jj-license/v1/verify';
    }

    /**
     * 라이센스 타입 파싱
     * 
     * @param string $license_key
     * @return string
     */
    public function parse_license_type( $license_key ) {
        if ( empty( $license_key ) ) {
            return self::TYPE_FREE;
        }
        
        // 라이센스 키 형식: JJ-[VERSION]-[TYPE]-[RANDOM]-[CHECKSUM]
        $parts = explode( '-', $license_key );
        if ( count( $parts ) >= 3 && isset( $parts[2] ) ) {
            $type = strtoupper( $parts[2] );
            if ( in_array( $type, array( self::TYPE_FREE, self::TYPE_BASIC, self::TYPE_PREMIUM, self::TYPE_UNLIMITED ) ) ) {
                return $type;
            }
        }
        
        return self::TYPE_FREE;
    }

    /**
     * 라이센스 키 검증 (최소한의 형식만 검증, 실제 검증은 외부 서버에서 수행)
     * 
     * @param string $license_key
     * @return bool
     */
    public function verify_license_key_format( $license_key ) {
        if ( empty( $license_key ) ) {
            return false;
        }
        
        // 최소한의 형식만 검증 (실제 검증은 외부 서버에서 수행)
        // 라이센스 키 형식: JJ-[VERSION]-[TYPE]-[RANDOM]-[CHECKSUM]
        // 체크섬 검증은 제거하여 플러그인 내부에서 확인 불가능하도록 함
        if ( ! preg_match( '/^JJ-\d+\.\d+-(FREE|BASIC|PREM|UNLIM)-[A-Z0-9]{8}-[A-Z0-9]{8}$/i', $license_key ) ) {
            return false;
        }
        
        // 형식만 확인하고, 실제 유효성은 외부 서버 검증에 의존
        return true;
    }

    /**
     * 사이트 고유 ID 생성
     * 
     * @return string
     */
    public function get_site_id() {
        $site_url = home_url();
        $site_path = ABSPATH;
        $install_timestamp = get_option( 'jj_style_guide_install_timestamp', time() );
        
        return md5( $site_url . $site_path . $install_timestamp );
    }

    /**
     * 라이센스 상태 가져오기
     * 
     * @return array
     */
    public function get_license_status() {
        // [Safety Lock] MASTER 버전은 항상 유효한 것으로 간주
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) === 'MASTER' ) {
            return array(
                'valid' => true,
                'type' => 'MASTER',
                'message' => __( 'Master 에디션이 활성화되었습니다.', 'acf-css-really-simple-style-management-center' ),
                'valid_until' => time() + ( 10 * 365 * 24 * 60 * 60 ), // 10년
            );
        }

        $cached_status = get_option( $this->license_status_option, null );
        
        // 캐시된 상태가 있고 유효하면 반환
        if ( $cached_status && isset( $cached_status['valid_until'] ) ) {
            if ( time() < $cached_status['valid_until'] ) {
                return $cached_status;
            }
        }
        
        // 캐시가 만료되었거나 없으면 재검증
        return $this->verify_license( true );
    }

    /**
     * 라이센스 검증 (온라인/오프라인)
     * 모든 검증은 외부 서버에서 수행되며, 플러그인 내부에서는 최소한의 형식만 확인
     * 
     * @param bool $force_online
     * @return array
     */
    public function verify_license( $force_online = false ) {
        // 코드 무결성 모니터 확인
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                return array(
                    'valid' => false,
                    'type' => self::TYPE_FREE,
                    'message' => __( '플러그인이 잠금되었습니다. 코드 무결성 위반이 감지되었습니다.', 'acf-css-really-simple-style-management-center' ),
                    'activation_required' => true,
                    'locked' => true,
                );
            }
        }
        
        $license_key = get_option( $this->license_key_option, '' );
        
        if ( empty( $license_key ) ) {
            return array(
                'valid' => false,
                'type' => self::TYPE_FREE,
                'message' => __( '라이센스 키가 설정되지 않았습니다. Free 버전으로 실행됩니다.', 'acf-css-really-simple-style-management-center' ),
                'activation_required' => true,
            );
        }
        
        // 가짜 라이센스 키 감지 (보안 강화)
        if ( class_exists( 'JJ_License_Enforcement' ) ) {
            $enforcement = JJ_License_Enforcement::instance();
            if ( $enforcement->detect_fake_license_key( $license_key ) ) {
                return array(
                    'valid' => false,
                    'type' => self::TYPE_FREE,
                    'message' => __( '유효하지 않은 라이센스 키입니다. 서버에서 확인할 수 없습니다.', 'acf-css-really-simple-style-management-center' ),
                    'activation_required' => true,
                    'fake_key' => true,
                );
            }
        }
        
        // 최소한의 형식만 검증 (실제 검증은 외부 서버에서 수행)
        if ( ! $this->verify_license_key_format( $license_key ) ) {
            return array(
                'valid' => false,
                'type' => self::TYPE_FREE,
                'message' => __( '유효하지 않은 라이센스 키 형식입니다.', 'acf-css-really-simple-style-management-center' ),
                'activation_required' => true,
            );
        }
        
        $license_type = $this->parse_license_type( $license_key );
        
        // [v3.8.0] 마스터 버전: 라이센스 발행 관리자에서 상태 확인 (우선순위)
        $is_master = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $is_master = JJ_Edition_Controller::instance()->is_at_least( 'master' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_master = ( 'MASTER' === JJ_STYLE_GUIDE_LICENSE_TYPE );
        }

        if ( $is_master && class_exists( 'JJ_License_Issuer' ) ) {
            $issuer = JJ_License_Issuer::instance();
            if ( method_exists( $issuer, 'get_license' ) ) {
                $issued_license = $issuer->get_license( $license_key );
                if ( $issued_license ) {
                    // 발행된 라이센스가 비활성화 상태인지 확인
                    if ( isset( $issued_license['status'] ) && $issued_license['status'] !== 'active' ) {
                        return array(
                            'valid' => false,
                            'type' => $license_type,
                            'message' => __( '이 라이센스는 비활성화되었습니다.', 'acf-css-really-simple-style-management-center' ),
                            'activation_required' => true,
                        );
                    }
                    
                    // 만료일 확인
                    if ( isset( $issued_license['expires_timestamp'] ) && $issued_license['expires_timestamp'] < time() ) {
                        return array(
                            'valid' => false,
                            'type' => $license_type,
                            'message' => __( '이 라이센스는 만료되었습니다.', 'acf-css-really-simple-style-management-center' ),
                            'expired' => true,
                            'activation_required' => true,
                        );
                    }
                    
                    // Premium 버전: 사이트 바인딩 확인
                    if ( in_array( $license_type, array( self::TYPE_PREMIUM, self::TYPE_UNLIMITED ) ) ) {
                        $site_id = $this->get_site_id();
                        $activations = $issuer->get_license_activations( $license_key );
                        
                        if ( ! empty( $activations ) ) {
                            // 이미 다른 사이트에서 사용 중인지 확인
                            $bound_to_other_site = false;
                            foreach ( $activations as $activation ) {
                                if ( $activation['site_id'] !== $site_id ) {
                                    $bound_to_other_site = true;
                                    break;
                                }
                            }
                            
                            if ( $bound_to_other_site && $license_type === self::TYPE_PREMIUM ) {
                                // Premium은 1개 사이트만 사용 가능
                                return array(
                                    'valid' => false,
                                    'type' => $license_type,
                                    'message' => __( '이 라이센스는 다른 사이트에서 사용 중입니다. Premium 버전은 1개 사이트에서만 사용 가능합니다.', 'acf-css-really-simple-style-management-center' ),
                                    'activation_required' => true,
                                );
                            }
                        }
                        
                        // 사이트 바인딩 저장
                        $this->bind_site( $license_key, $site_id );
                        
                        // 활성화 기록
                        if ( method_exists( $issuer, 'record_activation' ) ) {
                            $issuer->record_activation( $license_key, $site_id, home_url() );
                        }
                    }
                    
                    // 마스터 버전에서 발행된 라이센스가 유효하면 바로 반환
                    $expires_timestamp = isset( $issued_license['expires_timestamp'] ) ? $issued_license['expires_timestamp'] : time() + ( 365 * 24 * 60 * 60 );
                    $days_until_expiry = ( $expires_timestamp - time() ) / ( 24 * 60 * 60 );
                    
                    return array(
                        'valid' => true,
                        'type' => $license_type,
                        'message' => __( '라이센스가 활성화되었습니다.', 'acf-css-really-simple-style-management-center' ),
                        'valid_until' => $expires_timestamp,
                        'days_until_expiry' => $days_until_expiry,
                        'expiring_soon' => $days_until_expiry <= 14,
                    );
                }
            }
        }
        
        // FREE 버전은 추가 검증 불필요
        if ( $license_type === self::TYPE_FREE ) {
            return array(
                'valid' => true,
                'type' => self::TYPE_FREE,
                'message' => __( 'Free 버전이 활성화되었습니다.', 'acf-css-really-simple-style-management-center' ),
                'valid_until' => time() + ( 365 * 24 * 60 * 60 ), // 1년
            );
        }
        
        // 모든 상용 버전은 외부 서버에서 검증 (필수)
        // 온라인 검증 시도 (항상 시도)
        $online_result = $this->verify_license_online( $license_key );
        
        if ( $online_result['valid'] ) {
            // 검증 성공 시 캐시 저장
            $this->cache_license_status( $online_result );
            return $online_result;
        }
        
        // 외부 서버 검증 실패 시 (네트워크 오류 등)
        // 오프라인 검증으로 폴백 (제한된 기간만 유효)
        return $this->verify_license_offline( $license_key, $license_type );
    }

    /**
     * 온라인 검증 필요 여부 확인
     * 
     * @return bool
     */
    private function should_verify_online() {
        $cached_status = get_option( $this->license_status_option, null );
        
        // 캐시가 없거나 만료되었으면 온라인 검증 필요
        if ( ! $cached_status || ! isset( $cached_status['valid_until'] ) ) {
            return true;
        }
        
        // 캐시가 만료되었으면 온라인 검증 필요
        if ( time() >= $cached_status['valid_until'] ) {
            return true;
        }
        
        // 마지막 검증 후 30일이 지났으면 온라인 검증 필요
        $last_verified = isset( $cached_status['last_verified'] ) ? $cached_status['last_verified'] : 0;
        if ( time() - $last_verified > ( $this->offline_cache_days * 24 * 60 * 60 ) ) {
            return true;
        }
        
        return false;
    }

    /**
     * 온라인 라이센스 검증 (외부 서버에서 모든 검증 수행)
     * 
     * @param string $license_key
     * @return array
     */
    private function verify_license_online( $license_key ) {
        // 입력값 검증
        if ( empty( $license_key ) || ! is_string( $license_key ) || strlen( $license_key ) > 100 ) {
            return array(
                'valid' => false,
                'message' => __( '유효하지 않은 라이센스 키 형식입니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }
        
        // 라이센스 키 형식 검증
        if ( ! $this->verify_license_key_format( $license_key ) ) {
            return array(
                'valid' => false,
                'message' => __( '유효하지 않은 라이센스 키 형식입니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }
        
        $site_id = $this->get_site_id();
        $license_type = $this->parse_license_type( $license_key );
        
        // 사이트 ID 검증 (MD5 해시 형식)
        if ( ! preg_match( '/^[a-f0-9]{32}$/i', $site_id ) ) {
            return array(
                'valid' => false,
                'message' => __( '유효하지 않은 사이트 ID입니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }
        
        // POST 요청 데이터 (sanitization)
        $data = array(
            'license_key' => sanitize_text_field( $license_key ),
            'site_id' => sanitize_text_field( $site_id ),
            'site_url' => esc_url_raw( home_url() ),
            'action' => 'verify',
            'plugin_version' => sanitize_text_field( defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.1.6' ),
            'timestamp' => time(), // 타임스탬프 추가 (서버 측 검증용)
        );
        
        // WordPress HTTP API 사용 (보안 강화)
        $response = wp_remote_post( esc_url_raw( $this->license_server_url ), array(
            'timeout' => 10,
            'sslverify' => true, // SSL 검증 활성화
            'body' => $data,
            'headers' => array(
                'User-Agent' => 'JJ-Style-Guide/' . sanitize_text_field( defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.1.6' ),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'redirection' => 0, // 리다이렉션 방지
        ) );
        
        // 네트워크 오류 처리
        if ( is_wp_error( $response ) ) {
            error_log( 'JJ License Manager: 네트워크 오류 - ' . $response->get_error_message() );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        // HTTP 상태 코드 확인
        $response_code = wp_remote_retrieve_response_code( $response );
        if ( $response_code !== 200 ) {
            error_log( 'JJ License Manager: 서버 응답 오류 - HTTP ' . $response_code );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        $body = wp_remote_retrieve_body( $response );
        
        // 응답 본문 검증
        if ( empty( $body ) || strlen( $body ) > 10000 ) { // 응답 크기 제한
            error_log( 'JJ License Manager: 유효하지 않은 응답 본문' );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        $result = json_decode( $body, true );
        
        // JSON 파싱 오류 확인
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            error_log( 'JJ License Manager: JSON 파싱 오류 - ' . json_last_error_msg() );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        if ( ! $result || ! isset( $result['valid'] ) || ! is_bool( $result['valid'] ) ) {
            error_log( 'JJ License Manager: 유효하지 않은 응답 형식' );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        // 외부 서버에서 반환된 결과 사용
        // 서버에서 다음 정보를 반환해야 함:
        // - valid: 라이센스 유효 여부
        // - type: 라이센스 타입
        // - message: 상태 메시지
        // - valid_until: 만료일 (타임스탬프)
        // - status: 활성화 상태 ('active', 'inactive', 'expired')
        // - expires_timestamp: 만료 타임스탬프
        
        // 만료일 확인
        if ( isset( $result['expires_timestamp'] ) && $result['expires_timestamp'] < time() ) {
            $result['valid'] = false;
            $result['expired'] = true;
            $result['message'] = __( '이 라이센스는 만료되었습니다.', 'acf-css-really-simple-style-management-center' );
            $result['activation_required'] = true;
        }
        
        // 만료 임박 확인 (14일 이하)
        if ( isset( $result['expires_timestamp'] ) && $result['expires_timestamp'] > time() ) {
            $days_until_expiry = ( $result['expires_timestamp'] - time() ) / ( 24 * 60 * 60 );
            $result['days_until_expiry'] = $days_until_expiry;
            $result['expiring_soon'] = $days_until_expiry <= 14;
        }
        
        // 활성화 필요 여부 확인
        if ( ! $result['valid'] || ( isset( $result['status'] ) && $result['status'] !== 'active' ) ) {
            $result['activation_required'] = true;
        }
        
        // 사이트 바인딩 확인 (Premium, Unlimited만)
        if ( in_array( $license_type, array( self::TYPE_PREMIUM, self::TYPE_UNLIMITED ) ) ) {
            if ( isset( $result['bound_site_id'] ) && $result['bound_site_id'] !== $site_id ) {
                // Premium 버전은 1개 사이트만 사용 가능
                if ( $license_type === self::TYPE_PREMIUM ) {
                    return array(
                        'valid' => false,
                        'type' => $license_type,
                        'message' => __( '이 라이센스는 다른 사이트에서 사용 중입니다. Premium 버전은 1개 사이트에서만 사용 가능합니다.', 'acf-css-really-simple-style-management-center' ),
                        'activation_required' => true,
                    );
                }
            }
            
            // 사이트 바인딩 저장
            $this->bind_site( $license_key, $site_id );
        }
        
        // 검증 시간 기록
        $result['last_verified'] = time();
        
        // valid_until이 없으면 서버에서 반환된 expires_timestamp 사용
        if ( ! isset( $result['valid_until'] ) && isset( $result['expires_timestamp'] ) ) {
            $result['valid_until'] = $result['expires_timestamp'];
        }
        
        // valid_until이 여전히 없으면 제한된 기간만 유효
        if ( ! isset( $result['valid_until'] ) ) {
            $result['valid_until'] = time() + ( 7 * 24 * 60 * 60 ); // 7일만 유효
        }

        return $result;
    }

    /**
     * 오프라인 라이센스 검증 (캐시 사용)
     * 
     * @param string $license_key
     * @param string $license_type
     * @return array
     */
    private function verify_license_offline( $license_key, $license_type ) {
        $cached_status = get_option( $this->license_status_option, null );
        
        // 캐시된 상태가 있고 유효하면 반환
        if ( $cached_status && isset( $cached_status['valid_until'] ) ) {
            if ( time() < $cached_status['valid_until'] ) {
                return $cached_status;
            }
        }
        
        // 캐시가 없거나 만료되었으면 라이센스 키 형식만 검증
        return array(
            'valid' => true,
            'type' => $license_type,
            'message' => __( '오프라인 모드로 실행 중입니다. 온라인 검증이 필요할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
            'valid_until' => time() + ( 7 * 24 * 60 * 60 ), // 7일만 유효
            'offline' => true,
        );
    }

    /**
     * 라이센스 상태 캐시 저장
     * 
     * @param array $status
     */
    private function cache_license_status( $status ) {
        update_option( $this->license_status_option, $status );
    }

    /**
     * 사이트 바인딩
     * 
     * @param string $license_key
     * @param string $site_id
     */
    private function bind_site( $license_key, $site_id ) {
        update_option( $this->site_binding_option, array(
            'license_key' => $license_key,
            'site_id' => $site_id,
            'bound_at' => time(),
        ) );
    }

    /**
     * 현재 라이센스 타입 가져오기
     * 
     * @return string
     */
    public function get_current_license_type() {
        // [Safety Lock] MASTER 버전은 무조건 MASTER 반환
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) === 'MASTER' ) {
            return 'MASTER';
        }

        // 코드 무결성 모니터 확인
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                // 잠금 상태면 FREE로 강제
                return self::TYPE_FREE;
            }
        }
        
        // 라이센스 강제 실행 클래스 확인
        if ( class_exists( 'JJ_License_Enforcement' ) ) {
            $enforcement = JJ_License_Enforcement::instance();
            $license_type = defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ? JJ_STYLE_GUIDE_LICENSE_TYPE : self::TYPE_FREE;
            return apply_filters( 'jj_get_license_type', $license_type );
        }
        
        // Pro 버전 활성화 코드 확인 (우선순위)
        if ( function_exists( 'get_option' ) ) {
            $pro_activation_data = get_option( 'jj_style_guide_pro_activation_data', array() );
            if ( ! empty( $pro_activation_data ) && isset( $pro_activation_data['license_type'] ) ) {
                $pro_license_type = $pro_activation_data['license_type'];
                
                // 활성화 코드 유효성 확인
                $pro_activation_code = get_option( 'jj_style_guide_pro_activation_code', '' );
                if ( ! empty( $pro_activation_code ) ) {
                    // 만료 확인
                    if ( isset( $pro_activation_data['expires_timestamp'] ) && $pro_activation_data['expires_timestamp'] > 0 ) {
                        if ( time() > $pro_activation_data['expires_timestamp'] ) {
                            return self::TYPE_FREE; // 만료됨
                        }
                    }
                    
                    // Pro 버전의 라이센스 타입 반환
                    return apply_filters( 'jj_get_license_type', $pro_license_type );
                }
            }
        }
        
        // 외부 서버 검증 결과 우선 사용 (보안 강화)
        $license_status = $this->get_license_status();
        if ( isset( $license_status['type'] ) && $license_status['valid'] ) {
            return $license_status['type'];
        }
        
        // 서버 검증 실패 시 하드코딩된 상수 사용 (하지만 검증됨)
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            // 보안 강화: 하드코딩된 값도 서버와 재검증
            return $this->verify_license_type_with_server( JJ_STYLE_GUIDE_LICENSE_TYPE );
        }
        
        $license_key = get_option( $this->license_key_option, '' );
        return $this->parse_license_type( $license_key );
    }

    /**
     * 기능 사용 가능 여부 확인
     * 
     * @param string $feature
     * @return bool
     */
    public function can_use_feature( $feature ) {
        // [Safety Lock] MASTER 버전은 모든 기능 사용 가능
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) === 'MASTER' ) {
            return true;
        }

        // 코드 무결성 모니터 확인 (최우선)
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                return false; // 잠금 상태면 모든 기능 비활성화
            }
        }
        
        $license_type = $this->get_current_license_type();
        $status = $this->get_license_status();
        
        // 서버 검증 실패 시 기능 비활성화 (보안 강화)
        if ( ! $status['valid'] || ! isset( $status['type'] ) ) {
            return false;
        }
        
        // 서버 검증 결과와 로컬 라이센스 타입 불일치 시 비활성화
        if ( isset( $status['type'] ) && $status['type'] !== $license_type ) {
            // 불일치 감지 시 개발자에게 알림 (비동기)
            $this->report_license_mismatch( $license_type, $status['type'] );
            return false;
        }
        
        // 기능별 라이센스 요구사항 확인
        $feature_requirements = $this->get_feature_requirements();
        
        if ( ! isset( $feature_requirements[ $feature ] ) ) {
            // 정의되지 않은 기능은 모든 라이센스에서 사용 가능
            return true;
        }
        
        $required_type = $feature_requirements[ $feature ];
        $type_hierarchy = array(
            self::TYPE_FREE => 1,
            self::TYPE_BASIC => 2,
            self::TYPE_PREMIUM => 3,
            self::TYPE_UNLIMITED => 4,
        );
        
        $current_level = isset( $type_hierarchy[ $license_type ] ) ? $type_hierarchy[ $license_type ] : 0;
        $required_level = isset( $type_hierarchy[ $required_type ] ) ? $type_hierarchy[ $required_type ] : 999;
        
        return $current_level >= $required_level;
    }
    
    /**
     * 라이센스 타입 불일치 보고
     * 
     * @param string $local_type 로컬 라이센스 타입
     * @param string $server_type 서버 검증 라이센스 타입
     */
    private function report_license_mismatch( $local_type, $server_type ) {
        // 이미 보고된 경우 중복 방지 (24시간 내)
        $last_report = get_option( 'jj_license_mismatch_last_report', 0 );
        if ( time() - $last_report < 24 * 60 * 60 ) {
            return;
        }
        
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            $monitor->notify_developer( array(
                array(
                    'type' => 'license_type_mismatch',
                    'severity' => 'critical',
                    'local_type' => $local_type,
                    'server_type' => $server_type,
                ),
            ), array() );
        }
        
        update_option( 'jj_license_mismatch_last_report', time() );
    }

    /**
     * 기능별 라이센스 요구사항
     * 
     * @return array
     */
    private function get_feature_requirements() {
        return array(
            // 팔레트 시스템
            'palette_brand' => self::TYPE_FREE,
            'palette_system' => self::TYPE_BASIC,
            'palette_alternative' => self::TYPE_PREMIUM,
            'palette_another' => self::TYPE_PREMIUM,
            'palette_temp' => self::TYPE_BASIC,
            
            // 타이포그래피
            'typography_h1_h2' => self::TYPE_FREE,
            'typography_h3_h4' => self::TYPE_BASIC,
            'typography_h5_h6' => self::TYPE_PREMIUM,
            
            // 버튼
            'button_primary' => self::TYPE_FREE,
            'button_secondary' => self::TYPE_BASIC,
            'button_text' => self::TYPE_PREMIUM,
            
            // 폼
            'form_basic' => self::TYPE_FREE,
            'form_advanced' => self::TYPE_BASIC,
            
            // 내보내기/불러오기
            'export_import_full' => self::TYPE_BASIC,
            'export_import_section' => self::TYPE_PREMIUM,
            
            // 관리자 센터
            'admin_center_general' => self::TYPE_BASIC,
            'admin_center_full' => self::TYPE_PREMIUM,
            
            // 실험실 센터
            'labs_center_list' => self::TYPE_BASIC,
            'labs_center_scanner' => self::TYPE_PREMIUM,
            'labs_center_full' => self::TYPE_UNLIMITED,
            
            // Customizer 통합
            'customizer_basic' => self::TYPE_FREE,
            'customizer_full' => self::TYPE_PREMIUM,
            
            // 어댑터 (개수 제한)
            'adapter_themes' => self::TYPE_FREE, // 개수는 버전별로 다름
            'adapter_plugins' => self::TYPE_FREE, // 개수는 버전별로 다름
            
            // 고급 기능
            'eyedropper' => self::TYPE_BASIC,
            'palette_load' => self::TYPE_PREMIUM,
            'customizer_sync' => self::TYPE_PREMIUM,
        );
    }

    /**
     * 라이센스 키 저장
     * 
     * @param string $license_key
     * @return array
     */
    public function save_license_key( $license_key ) {
        // 라이센스 키 제거 (빈 문자열인 경우)
        if ( empty( $license_key ) ) {
            delete_option( $this->license_key_option );
            delete_option( $this->license_status_option );
            delete_option( $this->license_data_option );
            delete_option( 'jj_style_guide_license_type_override' );
            
            return array(
                'success' => true,
                'message' => __( '라이센스 키가 제거되었습니다. Free 버전으로 실행됩니다.', 'acf-css-really-simple-style-management-center' ),
                'status' => array(
                    'valid' => false,
                    'type' => self::TYPE_FREE,
                    'message' => __( '라이센스 키가 설정되지 않았습니다. Free 버전으로 실행됩니다.', 'acf-css-really-simple-style-management-center' ),
                ),
            );
        }
        
        // 라이센스 키 형식 검증
        if ( ! $this->verify_license_key_format( $license_key ) ) {
            return array(
                'success' => false,
                'message' => __( '유효하지 않은 라이센스 키 형식입니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }
        
        // 라이센스 키 저장
        update_option( $this->license_key_option, $license_key );
        
        // 라이센스 타입 업데이트
        $license_type = $this->parse_license_type( $license_key );
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            // 상수는 재정의할 수 없으므로 옵션에 저장
            update_option( 'jj_style_guide_license_type_override', $license_type );
        }
        
        // 라이센스 검증
        $status = $this->verify_license( true );
        
        return array(
            'success' => $status['valid'],
            'message' => $status['message'],
            'status' => $status,
        );
    }

    /**
     * 결제 페이지 URL 가져오기
     * 
     * @param string $action (upgrade, renew)
     * @return string
     */
    public function get_purchase_url( $action = 'upgrade' ) {
        // 결제 페이지 주소 (고정)
        return 'https://j-j-labs.com/?product=plugin-jj-center-of-style-setting';
    }

    /**
     * 업그레이드 프롬프트 메시지 생성
     * 
     * @param string $feature
     * @return string
     */
    public function get_upgrade_prompt( $feature ) {
        $license_type = $this->get_current_license_type();

        // [Safety Lock] MASTER 버전은 업그레이드 프롬프트 표시 안 함
        if ( 'MASTER' === $license_type || ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && 'MASTER' === strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) ) ) {
            return '';
        }

        $feature_requirements = $this->get_feature_requirements();
        
        if ( ! isset( $feature_requirements[ $feature ] ) ) {
            return '';
        }
        
        $required_type = $feature_requirements[ $feature ];
        
        if ( $this->can_use_feature( $feature ) ) {
            return '';
        }
        
        $type_names = array(
            self::TYPE_FREE => __( 'Free', 'acf-css-really-simple-style-management-center' ),
            self::TYPE_BASIC => __( 'Basic', 'acf-css-really-simple-style-management-center' ),
            self::TYPE_PREMIUM => __( 'Premium', 'acf-css-really-simple-style-management-center' ),
            self::TYPE_UNLIMITED => __( 'Unlimited', 'acf-css-really-simple-style-management-center' ),
        );
        
        $current_name = isset( $type_names[ $license_type ] ) ? $type_names[ $license_type ] : $license_type;
        $required_name = isset( $type_names[ $required_type ] ) ? $type_names[ $required_type ] : $required_type;
        
        // 라이센스 상태 확인 (만료 여부)
        $license_status = $this->get_license_status();
        $is_expired = false;
        $action_text = __( '업그레이드하기', 'acf-css-really-simple-style-management-center' );
        
        if ( isset( $license_status['valid_until'] ) && $license_status['valid_until'] < time() ) {
            $is_expired = true;
            $action_text = __( '기한 연장하기', 'acf-css-really-simple-style-management-center' );
        } elseif ( ! $license_status['valid'] ) {
            // 라이센스가 유효하지 않은 경우도 기한 연장 고려
            if ( in_array( $license_type, array( self::TYPE_BASIC, self::TYPE_PREMIUM, self::TYPE_UNLIMITED ) ) ) {
                $is_expired = true;
                $action_text = __( '기한 연장하기', 'acf-css-really-simple-style-management-center' );
            }
        }
        
        $purchase_url = $this->get_purchase_url( $is_expired ? 'renew' : 'upgrade' );
        
        return sprintf(
            __( '이 기능은 %s 버전 이상에서 사용할 수 있습니다. 현재 버전: %s. <a href="%s" target="_blank" rel="noopener noreferrer" style="font-weight: 600; color: #2271b1; text-decoration: underline;">%s</a>', 'acf-css-really-simple-style-management-center' ),
            $required_name,
            $current_name,
            esc_url( $purchase_url ),
            $action_text
        );
    }
}
