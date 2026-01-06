<?php
/**
 * 라이센스 캐싱 클래스
 *
 * [v8.1.0] 스마트 캐싱 시스템 추가
 * - 만료일 기반 동적 캐시 시간
 * - 오프라인 그레이스 기간 지원
 * - 캐시 상태 모니터링
 *
 * @package JJ_LicenseManagerincludes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Cache {

    /**
     * 캐시 그룹
     */
    private static $cache_group = 'jj_license';

    /**
     * 기본 캐시 만료 시간 (초)
     */
    private static $cache_expiration = 3600; // 1시간

    /**
     * [v8.1.0] 오프라인 그레이스 기간 (초)
     * 서버 연결 실패 시에도 캐시된 유효한 라이센스를 인정하는 기간
     */
    private static $offline_grace_period = 259200; // 3일

    /**
     * [v8.1.0] 캐시 통계 옵션명
     */
    private static $stats_option = 'jj_license_cache_stats';
    
    /**
     * 라이센스 검증 결과 캐싱
     * 
     * @param string $license_key 라이센스 키
     * @param string $site_id 사이트 ID
     * @param array $result 검증 결과
     * @param int $expiration 만료 시간 (초)
     */
    public static function set_verification_result( $license_key, $site_id, $result, $expiration = null ) {
        if ( $expiration === null ) {
            $expiration = self::$cache_expiration;
        }
        
        $cache_key = self::get_cache_key( 'verification', $license_key, $site_id );
        wp_cache_set( $cache_key, $result, self::$cache_group, $expiration );
        
        // 옵션에도 저장 (객체 캐시가 없는 경우 대비)
        $option_key = 'jj_license_cache_' . md5( $license_key . $site_id );
        update_option( $option_key, array(
            'result' => $result,
            'expires' => time() + $expiration,
        ), false );
    }
    
    /**
     * 라이센스 검증 결과 가져오기
     * 
     * @param string $license_key 라이센스 키
     * @param string $site_id 사이트 ID
     * @return array|false 검증 결과 또는 false
     */
    public static function get_verification_result( $license_key, $site_id ) {
        $cache_key = self::get_cache_key( 'verification', $license_key, $site_id );
        $result = wp_cache_get( $cache_key, self::$cache_group );
        
        if ( $result !== false ) {
            return $result;
        }
        
        // 옵션에서 확인
        $option_key = 'jj_license_cache_' . md5( $license_key . $site_id );
        $cached = get_option( $option_key, false );
        
        if ( $cached && isset( $cached['expires'] ) && $cached['expires'] > time() ) {
            // 객체 캐시에도 저장
            wp_cache_set( $cache_key, $cached['result'], self::$cache_group, $cached['expires'] - time() );
            return $cached['result'];
        }
        
        // 만료된 캐시 삭제
        if ( $cached ) {
            delete_option( $option_key );
        }
        
        return false;
    }
    
    /**
     * 라이센스 정보 캐싱
     * 
     * @param string $license_key 라이센스 키
     * @param array $license_data 라이센스 데이터
     * @param int $expiration 만료 시간 (초)
     */
    public static function set_license_data( $license_key, $license_data, $expiration = null ) {
        if ( $expiration === null ) {
            $expiration = self::$cache_expiration;
        }
        
        $cache_key = self::get_cache_key( 'license', $license_key );
        wp_cache_set( $cache_key, $license_data, self::$cache_group, $expiration );
    }
    
    /**
     * 라이센스 정보 가져오기
     * 
     * @param string $license_key 라이센스 키
     * @return array|false 라이센스 데이터 또는 false
     */
    public static function get_license_data( $license_key ) {
        $cache_key = self::get_cache_key( 'license', $license_key );
        return wp_cache_get( $cache_key, self::$cache_group );
    }
    
    /**
     * 캐시 삭제
     * 
     * @param string $license_key 라이센스 키
     * @param string|null $site_id 사이트 ID (선택사항)
     */
    public static function delete_cache( $license_key, $site_id = null ) {
        // 검증 결과 캐시 삭제
        if ( $site_id ) {
            $cache_key = self::get_cache_key( 'verification', $license_key, $site_id );
            wp_cache_delete( $cache_key, self::$cache_group );
            
            $option_key = 'jj_license_cache_' . md5( $license_key . $site_id );
            delete_option( $option_key );
        }
        
        // 라이센스 데이터 캐시 삭제
        $cache_key = self::get_cache_key( 'license', $license_key );
        wp_cache_delete( $cache_key, self::$cache_group );
    }
    
    /**
     * 모든 캐시 삭제
     */
    public static function flush_cache() {
        global $wpdb;
        
        // 옵션 테이블에서 캐시 삭제
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'jj_license_cache_%'" );
        
        // 객체 캐시는 자동으로 만료됨
    }
    
    /**
     * 캐시 키 생성
     *
     * @param string $type 타입
     * @param string $license_key 라이센스 키
     * @param string|null $site_id 사이트 ID
     * @return string 캐시 키
     */
    private static function get_cache_key( $type, $license_key, $site_id = null ) {
        $key = $type . '_' . md5( $license_key );
        if ( $site_id ) {
            $key .= '_' . md5( $site_id );
        }
        return $key;
    }

    /**
     * [v8.1.0] 스마트 캐시 시간 계산
     * 만료일이 가까우면 캐시 시간을 짧게, 멀면 길게 설정
     *
     * @param array $result 검증 결과
     * @return int 캐시 만료 시간 (초)
     */
    public static function calculate_smart_expiration( $result ) {
        // 유효하지 않은 라이센스는 짧은 캐시
        if ( empty( $result['valid'] ) ) {
            return 300; // 5분
        }

        // 만료일이 없으면 (무기한) 긴 캐시
        if ( empty( $result['expires_timestamp'] ) ) {
            return 86400; // 24시간
        }

        $expires_timestamp = $result['expires_timestamp'];
        $days_until_expiry = ( $expires_timestamp - time() ) / 86400;

        // 만료일 기반 동적 캐시 시간
        if ( $days_until_expiry <= 1 ) {
            return 300; // 1일 이내: 5분
        } elseif ( $days_until_expiry <= 7 ) {
            return 1800; // 1주일 이내: 30분
        } elseif ( $days_until_expiry <= 30 ) {
            return 3600; // 1달 이내: 1시간
        } elseif ( $days_until_expiry <= 90 ) {
            return 7200; // 3달 이내: 2시간
        } else {
            return 21600; // 3달 이상: 6시간
        }
    }

    /**
     * [v8.1.0] 오프라인 그레이스 기간 확인
     * 서버 연결 실패 시 캐시된 유효한 라이센스를 인정
     *
     * @param string $license_key 라이센스 키
     * @param string $site_id 사이트 ID
     * @return array|false 그레이스 기간 내의 캐시 또는 false
     */
    public static function get_grace_period_cache( $license_key, $site_id ) {
        $option_key = 'jj_license_cache_' . md5( $license_key . $site_id );
        $cached = get_option( $option_key, false );

        if ( ! $cached || ! isset( $cached['result'] ) ) {
            return false;
        }

        // 캐시된 결과가 유효하고, 그레이스 기간 내인지 확인
        if ( ! empty( $cached['result']['valid'] ) ) {
            $cache_age = time() - ( $cached['expires'] - self::$cache_expiration );
            if ( $cache_age < self::$offline_grace_period ) {
                // 그레이스 모드 플래그 추가
                $cached['result']['grace_mode'] = true;
                $cached['result']['grace_expires'] = $cached['expires'] - self::$cache_expiration + self::$offline_grace_period;
                return $cached['result'];
            }
        }

        return false;
    }

    /**
     * [v8.1.0] 캐시 통계 업데이트
     *
     * @param string $event 이벤트 (hit, miss, grace_hit, expired)
     */
    public static function update_stats( $event ) {
        $stats = get_option( self::$stats_option, array(
            'hits'        => 0,
            'misses'      => 0,
            'grace_hits'  => 0,
            'expired'     => 0,
            'last_reset'  => time(),
            'last_update' => time(),
        ) );

        switch ( $event ) {
            case 'hit':
                $stats['hits']++;
                break;
            case 'miss':
                $stats['misses']++;
                break;
            case 'grace_hit':
                $stats['grace_hits']++;
                break;
            case 'expired':
                $stats['expired']++;
                break;
        }

        $stats['last_update'] = time();
        update_option( self::$stats_option, $stats, false );
    }

    /**
     * [v8.1.0] 캐시 통계 가져오기
     *
     * @return array 캐시 통계
     */
    public static function get_stats() {
        $stats = get_option( self::$stats_option, array(
            'hits'        => 0,
            'misses'      => 0,
            'grace_hits'  => 0,
            'expired'     => 0,
            'last_reset'  => time(),
            'last_update' => time(),
        ) );

        // 히트율 계산
        $total = $stats['hits'] + $stats['misses'];
        $stats['hit_rate'] = $total > 0 ? round( ( $stats['hits'] / $total ) * 100, 1 ) : 0;

        return $stats;
    }

    /**
     * [v8.1.0] 캐시 통계 초기화
     */
    public static function reset_stats() {
        update_option( self::$stats_option, array(
            'hits'        => 0,
            'misses'      => 0,
            'grace_hits'  => 0,
            'expired'     => 0,
            'last_reset'  => time(),
            'last_update' => time(),
        ), false );
    }

    /**
     * [v8.1.0] 라이센스 상태 대시보드 데이터 가져오기
     *
     * @return array 대시보드 데이터
     */
    public static function get_dashboard_data() {
        global $wpdb;

        $data = array(
            'cache_stats' => self::get_stats(),
            'licenses'    => array(
                'total'    => 0,
                'active'   => 0,
                'inactive' => 0,
                'expired'  => 0,
                'expiring_soon' => 0,
            ),
            'activations' => array(
                'total'  => 0,
                'active' => 0,
            ),
        );

        // 라이센스 테이블이 있는지 확인
        $table_licenses = $wpdb->prefix . 'jj_licenses';
        $table_activations = $wpdb->prefix . 'jj_license_activations';

        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_licenses'" ) === $table_licenses ) {
            // 라이센스 통계
            $data['licenses']['total'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_licenses}" );
            $data['licenses']['active'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_licenses} WHERE status = 'active'" );
            $data['licenses']['inactive'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_licenses} WHERE status = 'inactive'" );
            $data['licenses']['expired'] = (int) $wpdb->get_var(
                "SELECT COUNT(*) FROM {$table_licenses} WHERE expires_at IS NOT NULL AND expires_at < NOW()"
            );
            // 14일 이내 만료 예정
            $data['licenses']['expiring_soon'] = (int) $wpdb->get_var(
                "SELECT COUNT(*) FROM {$table_licenses}
                WHERE status = 'active'
                AND expires_at IS NOT NULL
                AND expires_at > NOW()
                AND expires_at < DATE_ADD(NOW(), INTERVAL 14 DAY)"
            );
        }

        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_activations'" ) === $table_activations ) {
            // 활성화 통계
            $data['activations']['total'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_activations}" );
            $data['activations']['active'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_activations} WHERE is_active = 1" );
        }

        return $data;
    }

    /**
     * [v8.1.0] 캐시된 라이센스 목록 가져오기
     *
     * @return array 캐시된 라이센스 목록
     */
    public static function get_cached_licenses_list() {
        global $wpdb;

        $cached_options = $wpdb->get_results(
            "SELECT option_name, option_value FROM {$wpdb->options}
            WHERE option_name LIKE 'jj_license_cache_%'
            ORDER BY option_name",
            ARRAY_A
        );

        $licenses = array();
        foreach ( $cached_options as $option ) {
            $data = maybe_unserialize( $option['option_value'] );
            if ( is_array( $data ) && isset( $data['result'] ) ) {
                $licenses[] = array(
                    'option_name' => $option['option_name'],
                    'valid'       => ! empty( $data['result']['valid'] ),
                    'type'        => $data['result']['type'] ?? 'UNKNOWN',
                    'expires'     => isset( $data['expires'] ) ? date( 'Y-m-d H:i:s', $data['expires'] ) : 'N/A',
                    'is_expired'  => isset( $data['expires'] ) && $data['expires'] < time(),
                );
            }
        }

        return $licenses;
    }
}

