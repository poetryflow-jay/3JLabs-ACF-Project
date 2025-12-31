<?php
/**
 * 라이센스 통계 클래스
 * 
 * @package JJ_LicenseManagerincludesAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Stats {
    
    /**
     * 통계 데이터 가져오기
     * 
     * @return array 통계 데이터
     */
    public static function get_stats() {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_activations = JJ_License_Database::get_table_name( 'activations' );
        
        // 전체 라이센스 수
        $total_licenses = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_licenses}" );
        
        // 활성 라이센스 수
        $active_licenses = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_licenses} WHERE status = 'active'" );
        
        // 비활성 라이센스 수
        $inactive_licenses = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_licenses} WHERE status = 'inactive'" );
        
        // 만료된 라이센스 수
        $expired_licenses = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_licenses} 
            WHERE status = 'active' AND expires_at IS NOT NULL AND expires_at < NOW()"
        );
        
        // 만료 임박 라이센스 수 (14일 이내)
        $expiring_soon = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_licenses} 
            WHERE status = 'active' 
            AND expires_at IS NOT NULL 
            AND expires_at > NOW() 
            AND expires_at <= DATE_ADD(NOW(), INTERVAL 14 DAY)"
        );
        
        // 활성화된 사이트 수
        $active_sites = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_activations} WHERE is_active = 1" );
        
        // 타입별 통계
        $type_stats = $wpdb->get_results(
            "SELECT license_type, COUNT(*) as count, 
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count
             FROM {$table_licenses} 
             GROUP BY license_type",
            ARRAY_A
        );
        
        // 최근 30일간 생성된 라이센스 수
        $recent_licenses = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_licenses} 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
        
        // 이번 달 매출 (주문 기반)
        $monthly_revenue = $wpdb->get_var(
            "SELECT SUM(meta_value) 
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$table_licenses} l ON pm.post_id = l.order_id
             WHERE pm.meta_key = '_order_total'
             AND l.created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')"
        );
        
        return array(
            'total_licenses' => intval( $total_licenses ),
            'active_licenses' => intval( $active_licenses ),
            'inactive_licenses' => intval( $inactive_licenses ),
            'expired_licenses' => intval( $expired_licenses ),
            'expiring_soon' => intval( $expiring_soon ),
            'active_sites' => intval( $active_sites ),
            'type_stats' => $type_stats,
            'recent_licenses' => intval( $recent_licenses ),
            'monthly_revenue' => floatval( $monthly_revenue ),
        );
    }
}

