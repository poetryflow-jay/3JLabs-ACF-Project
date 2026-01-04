<?php
/**
 * Redirections Module
 * 
 * Rank Math Pro 스타일의 리다이렉션 관리 모듈
 * 301/302/307 리다이렉션, 404 모니터링, 자동 리다이렉션 제안
 * 
 * @package WP_Bulk_SEO_AEO
 * @version 2.1.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Redirections_Module {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 인스턴스 반환
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->init_hooks();
        $this->create_tables();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 리다이렉션 처리
        add_action('template_redirect', array($this, 'handle_redirects'), 1);
        
        // 404 모니터링
        add_action('wp', array($this, 'track_404'));
        
        // AJAX 핸들러
        add_action('wp_ajax_wp_bulk_seo_add_redirect', array($this, 'ajax_add_redirect'));
        add_action('wp_ajax_wp_bulk_seo_delete_redirect', array($this, 'ajax_delete_redirect'));
        add_action('wp_ajax_wp_bulk_seo_get_redirects', array($this, 'ajax_get_redirects'));
        add_action('wp_ajax_wp_bulk_seo_get_404s', array($this, 'ajax_get_404s'));
    }

    /**
     * 데이터베이스 테이블 생성
     */
    private function create_tables() {
        global $wpdb;

        $table_redirects = $wpdb->prefix . 'bulk_seo_aeo_redirects';
        $table_404s = $wpdb->prefix . 'bulk_seo_aeo_404s';

        $charset_collate = $wpdb->get_charset_collate();

        // 리다이렉션 테이블
        $sql_redirects = "CREATE TABLE IF NOT EXISTS $table_redirects (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            source_url varchar(500) NOT NULL,
            target_url varchar(500) NOT NULL,
            redirect_type varchar(10) DEFAULT '301',
            match_type varchar(20) DEFAULT 'exact',
            status varchar(20) DEFAULT 'active',
            hit_count int(11) DEFAULT 0,
            last_hit datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY source_url (source_url(191)),
            KEY status (status)
        ) $charset_collate;";

        // 404 테이블
        $sql_404s = "CREATE TABLE IF NOT EXISTS $table_404s (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            url varchar(500) NOT NULL,
            referrer varchar(500) DEFAULT '',
            user_agent varchar(500) DEFAULT '',
            ip_address varchar(45) DEFAULT '',
            hit_count int(11) DEFAULT 1,
            first_seen datetime DEFAULT CURRENT_TIMESTAMP,
            last_seen datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            is_resolved tinyint(1) DEFAULT 0,
            suggested_redirect varchar(500) DEFAULT '',
            PRIMARY KEY (id),
            KEY url (url(191)),
            KEY is_resolved (is_resolved),
            KEY hit_count (hit_count)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_redirects);
        dbDelta($sql_404s);
    }

    /**
     * 리다이렉션 처리
     */
    public function handle_redirects() {
        if (is_admin()) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_redirects';
        
        $request_uri = $_SERVER['REQUEST_URI'];
        $request_uri = strtok($request_uri, '?'); // 쿼리 스트링 제거

        // 정확한 매칭
        $redirect = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table 
            WHERE source_url = %s 
            AND status = 'active' 
            AND match_type = 'exact'
            LIMIT 1",
            $request_uri
        ));

        if (!$redirect) {
            // 정규식 매칭
            $redirects = $wpdb->get_results(
                "SELECT * FROM $table 
                WHERE status = 'active' 
                AND match_type = 'regex'
                ORDER BY id ASC"
            );

            foreach ($redirects as $redirect_item) {
                if (preg_match('#' . $redirect_item->source_url . '#', $request_uri)) {
                    $redirect = $redirect_item;
                    break;
                }
            }
        }

        if ($redirect) {
            // 히트 카운트 증가
            $wpdb->update(
                $table,
                array(
                    'hit_count' => $redirect->hit_count + 1,
                    'last_hit' => current_time('mysql')
                ),
                array('id' => $redirect->id)
            );

            // 리다이렉션 실행
            wp_redirect($redirect->target_url, intval($redirect->redirect_type));
            exit;
        }
    }

    /**
     * 404 모니터링
     */
    public function track_404() {
        if (!is_404()) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_404s';

        $url = $_SERVER['REQUEST_URI'];
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $ip_address = $this->get_client_ip();

        // 기존 404 확인
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE url = %s AND is_resolved = 0 LIMIT 1",
            $url
        ));

        if ($existing) {
            // 히트 카운트 증가
            $wpdb->update(
                $table,
                array(
                    'hit_count' => $existing->hit_count + 1,
                    'last_seen' => current_time('mysql')
                ),
                array('id' => $existing->id)
            );
        } else {
            // 새 404 기록
            $wpdb->insert(
                $table,
                array(
                    'url' => $url,
                    'referrer' => $referrer,
                    'user_agent' => $user_agent,
                    'ip_address' => $ip_address,
                    'hit_count' => 1,
                    'first_seen' => current_time('mysql'),
                    'last_seen' => current_time('mysql')
                )
            );
        }
    }

    /**
     * 클라이언트 IP 주소 가져오기
     */
    private function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }

    /**
     * 리다이렉션 추가 (AJAX)
     */
    public function ajax_add_redirect() {
        check_ajax_referer('wp_bulk_seo_redirect', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('권한이 없습니다.', 'wp-bulk-seo-aeo')));
        }

        $source_url = isset($_POST['source_url']) ? sanitize_text_field($_POST['source_url']) : '';
        $target_url = isset($_POST['target_url']) ? esc_url_raw($_POST['target_url']) : '';
        $redirect_type = isset($_POST['redirect_type']) ? sanitize_text_field($_POST['redirect_type']) : '301';
        $match_type = isset($_POST['match_type']) ? sanitize_text_field($_POST['match_type']) : 'exact';

        if (empty($source_url) || empty($target_url)) {
            wp_send_json_error(array('message' => __('소스 URL과 타겟 URL을 입력하세요.', 'wp-bulk-seo-aeo')));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_redirects';

        $result = $wpdb->insert(
            $table,
            array(
                'source_url' => $source_url,
                'target_url' => $target_url,
                'redirect_type' => $redirect_type,
                'match_type' => $match_type,
                'status' => 'active',
                'created_at' => current_time('mysql')
            )
        );

        if ($result) {
            wp_send_json_success(array('message' => __('리다이렉션이 추가되었습니다.', 'wp-bulk-seo-aeo')));
        } else {
            wp_send_json_error(array('message' => __('리다이렉션 추가에 실패했습니다.', 'wp-bulk-seo-aeo')));
        }
    }

    /**
     * 리다이렉션 삭제 (AJAX)
     */
    public function ajax_delete_redirect() {
        check_ajax_referer('wp_bulk_seo_redirect', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('권한이 없습니다.', 'wp-bulk-seo-aeo')));
        }

        $redirect_id = isset($_POST['redirect_id']) ? intval($_POST['redirect_id']) : 0;

        if (!$redirect_id) {
            wp_send_json_error(array('message' => __('잘못된 리다이렉션 ID입니다.', 'wp-bulk-seo-aeo')));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_redirects';

        $result = $wpdb->delete($table, array('id' => $redirect_id));

        if ($result) {
            wp_send_json_success(array('message' => __('리다이렉션이 삭제되었습니다.', 'wp-bulk-seo-aeo')));
        } else {
            wp_send_json_error(array('message' => __('리다이렉션 삭제에 실패했습니다.', 'wp-bulk-seo-aeo')));
        }
    }

    /**
     * 리다이렉션 목록 가져오기 (AJAX)
     */
    public function ajax_get_redirects() {
        check_ajax_referer('wp_bulk_seo_redirect', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('권한이 없습니다.', 'wp-bulk-seo-aeo')));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_redirects';

        $redirects = $wpdb->get_results(
            "SELECT * FROM $table ORDER BY created_at DESC LIMIT 100",
            ARRAY_A
        );

        wp_send_json_success(array('redirects' => $redirects));
    }

    /**
     * 404 목록 가져오기 (AJAX)
     */
    public function ajax_get_404s() {
        check_ajax_referer('wp_bulk_seo_redirect', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('권한이 없습니다.', 'wp-bulk-seo-aeo')));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_404s';

        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 50;
        $order_by = isset($_POST['order_by']) ? sanitize_text_field($_POST['order_by']) : 'hit_count';
        $order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'DESC';

        $order_by = in_array($order_by, array('hit_count', 'last_seen', 'first_seen')) ? $order_by : 'hit_count';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $fours = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
            WHERE is_resolved = 0 
            ORDER BY %s %s 
            LIMIT %d",
            $order_by,
            $order,
            $limit
        ), ARRAY_A);

        // 제안된 리다이렉션 생성
        foreach ($fours as &$four) {
            if (empty($four['suggested_redirect'])) {
                $four['suggested_redirect'] = $this->suggest_redirect($four['url']);
            }
        }

        wp_send_json_success(array('404s' => $fours));
    }

    /**
     * 리다이렉션 제안
     */
    private function suggest_redirect($url) {
        // URL에서 키워드 추출
        $url_parts = parse_url($url);
        $path = isset($url_parts['path']) ? trim($url_parts['path'], '/') : '';
        
        if (empty($path)) {
            return '';
        }

        // WordPress 포스트/페이지 검색
        $path_segments = explode('/', $path);
        $slug = end($path_segments);

        // 슬러그로 포스트 검색
        $post = get_page_by_path($slug, OBJECT, array('post', 'page'));
        
        if ($post) {
            return get_permalink($post->ID);
        }

        // 제목으로 검색
        $posts = get_posts(array(
            's' => $slug,
            'post_type' => array('post', 'page'),
            'posts_per_page' => 1,
            'post_status' => 'publish'
        ));

        if (!empty($posts)) {
            return get_permalink($posts[0]->ID);
        }

        return '';
    }

    /**
     * 리다이렉션 통계 가져오기
     */
    public function get_redirect_stats() {
        global $wpdb;
        $table_redirects = $wpdb->prefix . 'bulk_seo_aeo_redirects';
        $table_404s = $wpdb->prefix . 'bulk_seo_aeo_404s';

        $total_redirects = $wpdb->get_var("SELECT COUNT(*) FROM $table_redirects WHERE status = 'active'");
        $total_hits = $wpdb->get_var("SELECT SUM(hit_count) FROM $table_redirects");
        $total_404s = $wpdb->get_var("SELECT COUNT(*) FROM $table_404s WHERE is_resolved = 0");
        $top_404s = $wpdb->get_results(
            "SELECT url, hit_count FROM $table_404s 
            WHERE is_resolved = 0 
            ORDER BY hit_count DESC 
            LIMIT 10",
            ARRAY_A
        );

        return array(
            'total_redirects' => intval($total_redirects),
            'total_hits' => intval($total_hits),
            'total_404s' => intval($total_404s),
            'top_404s' => $top_404s
        );
    }
}
