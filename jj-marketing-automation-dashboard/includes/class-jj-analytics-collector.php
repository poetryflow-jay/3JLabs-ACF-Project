<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Analytics Collector
 * 
 * 모든 3J Labs 플러그인의 사용 데이터를 수집하고 통계를 제공합니다.
 * 
 * @package JJ_Marketing_Dashboard
 * @version 1.0.0
 */

class JJ_Analytics_Collector {

    private static $instance = null;
    private $option_key = 'jj_marketing_analytics_data';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 데이터 수집 훅
        add_action( 'admin_init', array( $this, 'start_tracking' ) );
        add_action( 'shutdown', array( $this, 'save_tracking' ) );
        
        // 사용자 활동 추적
        add_action( 'wp_ajax_jj_marketing_track_event', array( $this, 'ajax_track_event' ) );
        add_action( 'wp_ajax_nopriv_jj_marketing_track_event', array( $this, 'ajax_track_event' ) );
    }

    public function start_tracking() {
        if ( ! is_admin() ) {
            return;
        }

        $this->track_pageview();
    }

    public function track_pageview() {
        $current_screen = get_current_screen();
        if ( ! $current_screen ) {
            return;
        }

        $data = $this->get_analytics_data();
        
        // 페이지뷰 추적
        $data['page_views'][] = array(
            'screen_id' => $current_screen->id,
            'screen_base' => $current_screen->base,
            'post_type' => $current_screen->post_type,
            'timestamp' => current_time( 'mysql' ),
            'user_id' => get_current_user_id(),
            'ip_address' => $this->get_client_ip(),
        );
        
        update_option( $this->option_key, $data );
    }

    public function ajax_track_event() {
        check_ajax_referer( 'jj_marketing_dashboard_nonce', 'nonce' );

        $event_type = isset( $_POST['event_type'] ) ? sanitize_text_field( $_POST['event_type'] ) : '';
        $event_data = isset( $_POST['event_data'] ) ? map_deep( stripslashes( $_POST['event_data'] ), 'sanitize_text_field' ) : array();

        if ( empty( $event_type ) ) {
            wp_send_json_error( array( 'message' => '이벤트 타입이 필요합니다.' ) );
        }

        $data = $this->get_analytics_data();
        $data['events'][] = array_merge( $event_data, array(
            'event_type' => $event_type,
            'timestamp' => current_time( 'mysql' ),
            'user_id' => get_current_user_id(),
            'ip_address' => $this->get_client_ip(),
        ) );

        update_option( $this->option_key, $data );

        wp_send_json_success( array( 'message' => '이벤트가 추적되었습니다.' ) );
    }

    public function get_comprehensive_stats() {
        $data = $this->get_analytics_data();
        
        // 기본 통계
        $stats = array(
            'total_page_views' => isset( $data['page_views'] ) ? count( $data['page_views'] ) : 0,
            'total_events' => isset( $data['events'] ) ? count( $data['events'] ) : 0,
            'most_visited_pages' => array(),
            'top_user_actions' => array(),
            'active_users_last_7_days' => $this->get_active_users(),
            'plugin_usage' => $this->get_plugin_usage(),
            'daily_stats' => $this->get_daily_stats(),
        );

        // 가장 많이 방문한 페이지
        if ( ! empty( $data['page_views'] ) ) {
            $page_counts = array();
            foreach ( $data['page_views'] as $view ) {
                $page_key = isset( $view['screen_base'] ) ? $view['screen_base'] : $view['screen_id'];
                $page_counts[ $page_key ] = isset( $page_counts[ $page_key ] ) ? $page_counts[ $page_key ] + 1 : 1;
            }

            arsort( $page_counts );
            $stats['most_visited_pages'] = array_slice( $page_counts, 0, 10, true );
        }

        // 사용자 액션 통계
        if ( ! empty( $data['events'] ) ) {
            $action_counts = array();
            foreach ( $data['events'] as $event ) {
                $action_key = isset( $event['action'] ) ? $event['action'] : $event['event_type'];
                $action_counts[ $action_key ] = isset( $action_counts[ $action_key ] ) ? $action_counts[ $action_key ] + 1 : 1;
            }

            arsort( $action_counts );
            $stats['top_user_actions'] = array_slice( $action_counts, 0, 10, true );
        }

        return $stats;
    }

    private function get_plugin_usage() {
        $plugins = array(
            'acf_css_manager' => defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : null,
            'neural_link' => defined( 'JJ_NEURAL_LINK_VERSION' ) ? JJ_NEURAL_LINK_VERSION : null,
            'woo_toolkit' => defined( 'ACF_CSS_WC_VERSION' ) ? ACF_CSS_WC_VERSION : null,
            'nudge_flow' => defined( 'ACF_NUDGE_FLOW_VERSION' ) ? ACF_NUDGE_FLOW_VERSION : null,
            'code_snippets' => defined( 'ACF_CSB_VERSION' ) ? ACF_CSB_VERSION : null,
            'ai_extension' => defined( 'JJ_ACF_CSS_AI_EXT_VERSION' ) ? JJ_ACF_CSS_AI_EXT_VERSION : null,
            'bulk_manager' => defined( 'WP_BULK_MANAGER_VERSION' ) ? WP_BULK_MANAGER_VERSION : null,
        );

        $active_plugins = array();
        foreach ( $plugins as $slug => $version ) {
            if ( $version !== null ) {
                $active_plugins[ $slug ] = array(
                    'version' => $version,
                    'name' => $this->get_plugin_name( $slug ),
                    'last_used' => $this->get_last_used_time( $slug ),
                );
            }
        }

        return $active_plugins;
    }

    private function get_plugin_name( $slug ) {
        $names = array(
            'acf_css_manager' => 'ACF CSS Manager',
            'neural_link' => 'Neural Link',
            'woo_toolkit' => 'WooCommerce Toolkit',
            'nudge_flow' => 'Nudge Flow',
            'code_snippets' => 'Code Snippets Box',
            'ai_extension' => 'AI Extension',
            'bulk_manager' => 'Bulk Manager',
        );

        return isset( $names[ $slug ] ) ? $names[ $slug ] : $slug;
    }

    private function get_last_used_time( $plugin_slug ) {
        // 마지막 사용 시간 추적 (간소화)
        return current_time( 'mysql' );
    }

    private function get_active_users() {
        $users = get_users( array(
            'number' => 10,
            'orderby' => 'registered',
            'order' => 'DESC',
            'fields' => array( 'ID', 'user_login', 'user_email', 'user_registered' ),
        ) );

        return array_map( function( $user ) {
            return array(
                'id' => $user->ID,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'registered' => $user->user_registered,
                'last_login' => get_user_meta( $user->ID, 'last_login', true ),
            );
        }, $users );
    }

    private function get_daily_stats() {
        $data = $this->get_analytics_data();
        $daily_stats = array();

        // 일별 페이지뷰
        if ( ! empty( $data['page_views'] ) ) {
            $daily_page_views = array();
            foreach ( $data['page_views'] as $view ) {
                $date = date( 'Y-m-d', strtotime( $view['timestamp'] ) );
                $daily_page_views[ $date ] = isset( $daily_page_views[ $date ] ) ? $daily_page_views[ $date ] + 1 : 1;
            }

            $daily_stats['page_views'] = $daily_page_views;
        }

        return $daily_stats;
    }

    private function get_analytics_data() {
        $data = get_option( $this->option_key );
        if ( ! is_array( $data ) ) {
            $data = array(
                'page_views' => array(),
                'events' => array(),
            );
            update_option( $this->option_key, $data );
        }

        return $data;
    }

    private function get_client_ip() {
        // 프라이버시 보호를 위해 IP 주소 익명화
        $ip = '';
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED'] ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif ( ! empty( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif ( ! empty( $_SERVER['HTTP_FORWARDED'] ) ) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // 익명화
        return $this->anonymize_ip( $ip );
    }

    private function anonymize_ip( $ip ) {
        // IPv4 마지막 옥텑트 익명화
        $ip_parts = explode( '.', $ip );
        $ip_parts[3] = '0';
        return implode( '.', $ip_parts );
    }

    public function clear_old_data( $days_old = 30 ) {
        $data = $this->get_analytics_data();
        $cutoff_time = date( 'Y-m-d H:i:s', strtotime( "-{$days_old} days" ) );

        if ( ! empty( $data['page_views'] ) ) {
            $data['page_views'] = array_filter( $data['page_views'], function( $view ) use ( $cutoff_time ) {
                return $view['timestamp'] >= $cutoff_time;
            } );
        }

        if ( ! empty( $data['events'] ) ) {
            $data['events'] = array_filter( $data['events'], function( $event ) use ( $cutoff_time ) {
                return $event['timestamp'] >= $cutoff_time;
            } );
        }

        update_option( $this->option_key, $data );
    }
}
