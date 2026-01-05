<?php
/**
 * OneClick SEO Pro - Redirections & 404 Monitor
 *
 * Comprehensive redirection management and 404 error tracking:
 * - 301/302/307 redirects
 * - Regex support
 * - 404 error logging
 * - Auto-redirect suggestions
 * - Import/Export
 * - Bulk operations
 *
 * @package OneClick_SEO_Pro
 * @subpackage Redirections
 * @version 2.1.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Redirections Class
 */
class OneClick_SEO_Pro_Redirections {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Database table names
     */
    private $table_redirects;
    private $table_404_logs;

    /**
     * Settings
     */
    private $settings = [];

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        global $wpdb;

        $this->table_redirects = $wpdb->prefix . 'oneclick_seo_redirects';
        $this->table_404_logs = $wpdb->prefix . 'oneclick_seo_404_logs';

        $this->settings = get_option('oneclick_seo_pro_redirections', $this->get_default_settings());

        $this->init_hooks();
    }

    /**
     * Get default settings
     */
    private function get_default_settings() {
        return [
            'enabled' => true,
            'log_404' => true,
            'auto_redirect' => false,
            'redirect_attachment' => true,
            'preserve_query_string' => true,
            'log_retention_days' => 30,
            'max_404_logs' => 10000,
        ];
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Check redirects on template_redirect (early)
        add_action('template_redirect', [$this, 'check_redirects'], 1);

        // Log 404 errors
        if (!empty($this->settings['log_404'])) {
            add_action('template_redirect', [$this, 'log_404'], 99);
        }

        // Auto redirect attachments to parent post
        if (!empty($this->settings['redirect_attachment'])) {
            add_action('template_redirect', [$this, 'redirect_attachment'], 5);
        }

        // Admin hooks
        if (is_admin()) {
            add_action('admin_menu', [$this, 'add_admin_menu']);
            add_action('admin_init', [$this, 'process_form_actions']);

            // AJAX handlers
            add_action('wp_ajax_oneclick_seo_add_redirect', [$this, 'ajax_add_redirect']);
            add_action('wp_ajax_oneclick_seo_delete_redirect', [$this, 'ajax_delete_redirect']);
            add_action('wp_ajax_oneclick_seo_bulk_delete_redirects', [$this, 'ajax_bulk_delete_redirects']);
            add_action('wp_ajax_oneclick_seo_get_404_logs', [$this, 'ajax_get_404_logs']);
            add_action('wp_ajax_oneclick_seo_clear_404_logs', [$this, 'ajax_clear_404_logs']);
            add_action('wp_ajax_oneclick_seo_create_redirect_from_404', [$this, 'ajax_create_redirect_from_404']);
            add_action('wp_ajax_oneclick_seo_export_redirects', [$this, 'ajax_export_redirects']);
            add_action('wp_ajax_oneclick_seo_import_redirects', [$this, 'ajax_import_redirects']);
        }

        // REST API
        add_action('rest_api_init', [$this, 'register_rest_routes']);

        // Cron for cleanup
        add_action('oneclick_seo_pro_cleanup_404_logs', [$this, 'cleanup_old_logs']);
    }

    /**
     * Create database tables
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $table_redirects = $wpdb->prefix . 'oneclick_seo_redirects';
        $table_404_logs = $wpdb->prefix . 'oneclick_seo_404_logs';

        $sql_redirects = "CREATE TABLE IF NOT EXISTS $table_redirects (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            source_url varchar(2000) NOT NULL,
            target_url varchar(2000) NOT NULL,
            redirect_type smallint(4) NOT NULL DEFAULT 301,
            is_regex tinyint(1) NOT NULL DEFAULT 0,
            hits int(11) NOT NULL DEFAULT 0,
            last_hit datetime DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            notes text,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY source_url (source_url(191)),
            KEY status (status)
        ) $charset_collate;";

        $sql_404_logs = "CREATE TABLE IF NOT EXISTS $table_404_logs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            url varchar(2000) NOT NULL,
            referrer varchar(2000) DEFAULT NULL,
            user_agent varchar(500) DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            hits int(11) NOT NULL DEFAULT 1,
            first_hit datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            last_hit datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            redirect_id bigint(20) unsigned DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'new',
            PRIMARY KEY (id),
            KEY url (url(191)),
            KEY hits (hits),
            KEY status (status)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_redirects);
        dbDelta($sql_404_logs);
    }

    /**
     * Check and apply redirects
     */
    public function check_redirects() {
        if (is_admin()) {
            return;
        }

        global $wpdb;

        $current_url = $this->get_current_url();
        $current_path = $this->get_current_path();

        // Check exact matches first
        $redirect = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_redirects}
             WHERE status = 'active'
             AND is_regex = 0
             AND (source_url = %s OR source_url = %s)
             LIMIT 1",
            $current_url,
            $current_path
        ));

        if (!$redirect) {
            // Check regex matches
            $regex_redirects = $wpdb->get_results(
                "SELECT * FROM {$this->table_redirects}
                 WHERE status = 'active' AND is_regex = 1"
            );

            foreach ($regex_redirects as $regex_redirect) {
                $pattern = '@' . str_replace('@', '\@', $regex_redirect->source_url) . '@i';
                if (preg_match($pattern, $current_path, $matches)) {
                    $redirect = $regex_redirect;
                    // Replace capture groups in target
                    if (count($matches) > 1) {
                        $target = $redirect->target_url;
                        for ($i = 1; $i < count($matches); $i++) {
                            $target = str_replace('$' . $i, $matches[$i], $target);
                        }
                        $redirect->target_url = $target;
                    }
                    break;
                }
            }
        }

        if ($redirect) {
            // Update hit count
            $wpdb->update(
                $this->table_redirects,
                [
                    'hits' => $redirect->hits + 1,
                    'last_hit' => current_time('mysql'),
                ],
                ['id' => $redirect->id]
            );

            $target_url = $redirect->target_url;

            // Preserve query string if enabled
            if (!empty($this->settings['preserve_query_string']) && !empty($_SERVER['QUERY_STRING'])) {
                $separator = strpos($target_url, '?') !== false ? '&' : '?';
                $target_url .= $separator . $_SERVER['QUERY_STRING'];
            }

            // Perform redirect
            wp_redirect($target_url, intval($redirect->redirect_type));
            exit;
        }
    }

    /**
     * Log 404 errors
     */
    public function log_404() {
        if (!is_404()) {
            return;
        }

        global $wpdb;

        $url = $this->get_current_url();
        $url_hash = md5($url);

        // Check if URL already logged
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id, hits FROM {$this->table_404_logs} WHERE url = %s LIMIT 1",
            $url
        ));

        if ($existing) {
            // Update hit count
            $wpdb->update(
                $this->table_404_logs,
                [
                    'hits' => $existing->hits + 1,
                    'last_hit' => current_time('mysql'),
                    'referrer' => isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : null,
                ],
                ['id' => $existing->id]
            );
        } else {
            // Check max logs limit
            $count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_404_logs}");
            if ($count >= $this->settings['max_404_logs']) {
                // Delete oldest entries
                $delete_count = max(100, $count - $this->settings['max_404_logs'] + 100);
                $wpdb->query("DELETE FROM {$this->table_404_logs} ORDER BY last_hit ASC LIMIT $delete_count");
            }

            // Insert new log
            $wpdb->insert($this->table_404_logs, [
                'url' => $url,
                'referrer' => isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : null,
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(substr($_SERVER['HTTP_USER_AGENT'], 0, 500)) : null,
                'ip_address' => $this->get_client_ip(),
                'hits' => 1,
                'first_hit' => current_time('mysql'),
                'last_hit' => current_time('mysql'),
                'status' => 'new',
            ]);
        }
    }

    /**
     * Redirect attachment pages to parent post
     */
    public function redirect_attachment() {
        if (!is_attachment()) {
            return;
        }

        global $post;

        if ($post && $post->post_parent) {
            $parent_url = get_permalink($post->post_parent);
            if ($parent_url) {
                wp_redirect($parent_url, 301);
                exit;
            }
        }

        // Redirect to home if no parent
        wp_redirect(home_url(), 301);
        exit;
    }

    /**
     * Get current URL
     */
    private function get_current_url() {
        $protocol = is_ssl() ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Get current path
     */
    private function get_current_path() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return rtrim($path, '/');
    }

    /**
     * Get client IP
     */
    private function get_client_ip() {
        $ip = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return sanitize_text_field(trim($ip));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'oneclick-seo-pro',
            __('Redirections', 'oneclick-seo-pro'),
            __('Redirections', 'oneclick-seo-pro'),
            'manage_options',
            'oneclick-seo-pro-redirects',
            [$this, 'render_admin_page']
        );
    }

    /**
     * Process form actions
     */
    public function process_form_actions() {
        if (!isset($_POST['oneclick_seo_redirect_action'])) {
            return;
        }

        check_admin_referer('oneclick_seo_redirects', 'oneclick_seo_redirect_nonce');

        if (!current_user_can('manage_options')) {
            return;
        }

        $action = sanitize_key($_POST['oneclick_seo_redirect_action']);

        switch ($action) {
            case 'add':
                $this->add_redirect([
                    'source_url' => sanitize_text_field($_POST['source_url']),
                    'target_url' => esc_url_raw($_POST['target_url']),
                    'redirect_type' => intval($_POST['redirect_type']),
                    'is_regex' => isset($_POST['is_regex']) ? 1 : 0,
                    'notes' => sanitize_textarea_field($_POST['notes'] ?? ''),
                ]);
                break;

            case 'edit':
                $this->update_redirect(intval($_POST['redirect_id']), [
                    'source_url' => sanitize_text_field($_POST['source_url']),
                    'target_url' => esc_url_raw($_POST['target_url']),
                    'redirect_type' => intval($_POST['redirect_type']),
                    'is_regex' => isset($_POST['is_regex']) ? 1 : 0,
                    'status' => sanitize_key($_POST['status']),
                    'notes' => sanitize_textarea_field($_POST['notes'] ?? ''),
                ]);
                break;
        }

        wp_redirect(admin_url('admin.php?page=oneclick-seo-pro-redirects&updated=1'));
        exit;
    }

    /**
     * Add redirect
     */
    public function add_redirect($data) {
        global $wpdb;

        // Validate
        if (empty($data['source_url']) || empty($data['target_url'])) {
            return new WP_Error('missing_data', __('Source and target URLs are required', 'oneclick-seo-pro'));
        }

        // Check for duplicate
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$this->table_redirects} WHERE source_url = %s",
            $data['source_url']
        ));

        if ($existing) {
            return new WP_Error('duplicate', __('A redirect for this source URL already exists', 'oneclick-seo-pro'));
        }

        $result = $wpdb->insert($this->table_redirects, [
            'source_url' => $data['source_url'],
            'target_url' => $data['target_url'],
            'redirect_type' => isset($data['redirect_type']) ? intval($data['redirect_type']) : 301,
            'is_regex' => isset($data['is_regex']) ? intval($data['is_regex']) : 0,
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'status' => 'active',
            'created_at' => current_time('mysql'),
        ]);

        if ($result) {
            return $wpdb->insert_id;
        }

        return new WP_Error('insert_failed', __('Failed to create redirect', 'oneclick-seo-pro'));
    }

    /**
     * Update redirect
     */
    public function update_redirect($id, $data) {
        global $wpdb;

        return $wpdb->update(
            $this->table_redirects,
            $data,
            ['id' => $id]
        );
    }

    /**
     * Delete redirect
     */
    public function delete_redirect($id) {
        global $wpdb;

        return $wpdb->delete($this->table_redirects, ['id' => $id]);
    }

    /**
     * Get all redirects
     */
    public function get_redirects($args = []) {
        global $wpdb;

        $defaults = [
            'per_page' => 50,
            'page' => 1,
            'status' => '',
            'orderby' => 'created_at',
            'order' => 'DESC',
            'search' => '',
        ];

        $args = wp_parse_args($args, $defaults);

        $where = "WHERE 1=1";

        if (!empty($args['status'])) {
            $where .= $wpdb->prepare(" AND status = %s", $args['status']);
        }

        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where .= $wpdb->prepare(" AND (source_url LIKE %s OR target_url LIKE %s)", $search, $search);
        }

        $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);
        $offset = ($args['page'] - 1) * $args['per_page'];

        $items = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_redirects} $where ORDER BY $orderby LIMIT %d OFFSET %d",
            $args['per_page'],
            $offset
        ));

        $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_redirects} $where");

        return [
            'items' => $items,
            'total' => $total,
            'pages' => ceil($total / $args['per_page']),
        ];
    }

    /**
     * Get 404 logs
     */
    public function get_404_logs($args = []) {
        global $wpdb;

        $defaults = [
            'per_page' => 50,
            'page' => 1,
            'status' => '',
            'orderby' => 'hits',
            'order' => 'DESC',
            'search' => '',
        ];

        $args = wp_parse_args($args, $defaults);

        $where = "WHERE 1=1";

        if (!empty($args['status'])) {
            $where .= $wpdb->prepare(" AND status = %s", $args['status']);
        }

        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where .= $wpdb->prepare(" AND url LIKE %s", $search);
        }

        $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);
        $offset = ($args['page'] - 1) * $args['per_page'];

        $items = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_404_logs} $where ORDER BY $orderby LIMIT %d OFFSET %d",
            $args['per_page'],
            $offset
        ));

        $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_404_logs} $where");

        return [
            'items' => $items,
            'total' => $total,
            'pages' => ceil($total / $args['per_page']),
        ];
    }

    /**
     * Get 404 stats
     */
    public function get_404_stats() {
        global $wpdb;

        return [
            'total' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_404_logs}"),
            'total_hits' => (int) $wpdb->get_var("SELECT SUM(hits) FROM {$this->table_404_logs}"),
            'new' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_404_logs} WHERE status = 'new'"),
            'today' => (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_404_logs} WHERE DATE(last_hit) = %s",
                current_time('Y-m-d')
            )),
        ];
    }

    /**
     * Get redirect stats
     */
    public function get_redirect_stats() {
        global $wpdb;

        return [
            'total' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_redirects}"),
            'active' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_redirects} WHERE status = 'active'"),
            'total_hits' => (int) $wpdb->get_var("SELECT SUM(hits) FROM {$this->table_redirects}"),
        ];
    }

    /**
     * Suggest redirect target for 404 URL
     */
    public function suggest_redirect_target($url) {
        $path = parse_url($url, PHP_URL_PATH);
        $slug = basename($path);

        // Try to find similar post
        global $wpdb;

        // Search by slug
        $post = $wpdb->get_row($wpdb->prepare(
            "SELECT ID, post_title FROM {$wpdb->posts}
             WHERE post_status = 'publish'
             AND post_name LIKE %s
             LIMIT 1",
            '%' . $wpdb->esc_like($slug) . '%'
        ));

        if ($post) {
            return [
                'url' => get_permalink($post->ID),
                'title' => $post->post_title,
                'confidence' => 'high',
            ];
        }

        // Search by words in slug
        $words = explode('-', $slug);
        $words = array_filter($words, function($w) {
            return strlen($w) > 3;
        });

        if (!empty($words)) {
            $search_term = implode(' ', array_slice($words, 0, 3));

            $post = $wpdb->get_row($wpdb->prepare(
                "SELECT ID, post_title FROM {$wpdb->posts}
                 WHERE post_status = 'publish'
                 AND (post_title LIKE %s OR post_content LIKE %s)
                 LIMIT 1",
                '%' . $wpdb->esc_like($search_term) . '%',
                '%' . $wpdb->esc_like($search_term) . '%'
            ));

            if ($post) {
                return [
                    'url' => get_permalink($post->ID),
                    'title' => $post->post_title,
                    'confidence' => 'medium',
                ];
            }
        }

        return [
            'url' => home_url(),
            'title' => get_bloginfo('name'),
            'confidence' => 'low',
        ];
    }

    /**
     * Cleanup old 404 logs
     */
    public function cleanup_old_logs() {
        global $wpdb;

        $days = intval($this->settings['log_retention_days']);
        if ($days < 1) {
            $days = 30;
        }

        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$this->table_404_logs} WHERE last_hit < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ));
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        $tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'redirects';
        $redirect_stats = $this->get_redirect_stats();
        $error_stats = $this->get_404_stats();

        ?>
        <div class="wrap oneclick-seo-redirects-page">
            <h1><?php esc_html_e('Redirections & 404 Monitor', 'oneclick-seo-pro'); ?></h1>

            <style>
                .oneclick-seo-redirects-page .nav-tab-wrapper { margin-bottom: 20px; }
                .oneclick-seo-stats-bar { display: flex; gap: 20px; margin-bottom: 20px; padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 4px; }
                .oneclick-seo-stat { text-align: center; padding: 0 20px; border-right: 1px solid #eee; }
                .oneclick-seo-stat:last-child { border-right: none; }
                .oneclick-seo-stat-value { font-size: 28px; font-weight: 600; color: #1e3a5f; }
                .oneclick-seo-stat-label { font-size: 12px; color: #666; }
                .oneclick-seo-table-actions { margin-bottom: 15px; display: flex; gap: 10px; align-items: center; }
                .oneclick-seo-redirect-form { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px; }
                .oneclick-seo-redirect-form .form-row { display: flex; gap: 15px; margin-bottom: 15px; align-items: flex-end; }
                .oneclick-seo-redirect-form .form-field { flex: 1; }
                .oneclick-seo-redirect-form label { display: block; margin-bottom: 5px; font-weight: 500; }
                .oneclick-seo-redirect-form input[type="text"],
                .oneclick-seo-redirect-form select { width: 100%; }
                .redirect-type-301 { color: #10b981; }
                .redirect-type-302 { color: #f59e0b; }
                .redirect-type-307 { color: #3b82f6; }
                .status-active { color: #10b981; }
                .status-inactive { color: #ef4444; }
                .hits-count { background: #f3f4f6; padding: 2px 8px; border-radius: 10px; font-size: 12px; }
                .oneclick-seo-404-item { display: flex; align-items: center; padding: 10px; background: #fff; border: 1px solid #ddd; margin-bottom: 5px; border-radius: 4px; }
                .oneclick-seo-404-url { flex: 1; word-break: break-all; }
                .oneclick-seo-404-hits { padding: 0 15px; font-weight: 600; }
                .oneclick-seo-404-actions { display: flex; gap: 5px; }
            </style>

            <!-- Stats Bar -->
            <div class="oneclick-seo-stats-bar">
                <div class="oneclick-seo-stat">
                    <div class="oneclick-seo-stat-value"><?php echo esc_html($redirect_stats['active']); ?></div>
                    <div class="oneclick-seo-stat-label"><?php esc_html_e('Active Redirects', 'oneclick-seo-pro'); ?></div>
                </div>
                <div class="oneclick-seo-stat">
                    <div class="oneclick-seo-stat-value"><?php echo esc_html(number_format($redirect_stats['total_hits'])); ?></div>
                    <div class="oneclick-seo-stat-label"><?php esc_html_e('Redirect Hits', 'oneclick-seo-pro'); ?></div>
                </div>
                <div class="oneclick-seo-stat">
                    <div class="oneclick-seo-stat-value"><?php echo esc_html($error_stats['total']); ?></div>
                    <div class="oneclick-seo-stat-label"><?php esc_html_e('404 URLs Logged', 'oneclick-seo-pro'); ?></div>
                </div>
                <div class="oneclick-seo-stat">
                    <div class="oneclick-seo-stat-value"><?php echo esc_html($error_stats['today']); ?></div>
                    <div class="oneclick-seo-stat-label"><?php esc_html_e('404s Today', 'oneclick-seo-pro'); ?></div>
                </div>
            </div>

            <!-- Tabs -->
            <nav class="nav-tab-wrapper">
                <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-redirects&tab=redirects')); ?>"
                   class="nav-tab <?php echo $tab === 'redirects' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e('Redirections', 'oneclick-seo-pro'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-redirects&tab=404')); ?>"
                   class="nav-tab <?php echo $tab === '404' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e('404 Monitor', 'oneclick-seo-pro'); ?>
                    <?php if ($error_stats['new'] > 0): ?>
                        <span class="count" style="background: #ef4444; color: #fff; padding: 2px 6px; border-radius: 10px; font-size: 11px; margin-left: 5px;">
                            <?php echo esc_html($error_stats['new']); ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-redirects&tab=settings')); ?>"
                   class="nav-tab <?php echo $tab === 'settings' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e('Settings', 'oneclick-seo-pro'); ?>
                </a>
            </nav>

            <?php
            switch ($tab) {
                case '404':
                    $this->render_404_tab();
                    break;
                case 'settings':
                    $this->render_settings_tab();
                    break;
                default:
                    $this->render_redirects_tab();
                    break;
            }
            ?>
        </div>
        <?php
    }

    /**
     * Render redirects tab
     */
    private function render_redirects_tab() {
        $redirects = $this->get_redirects([
            'page' => isset($_GET['paged']) ? intval($_GET['paged']) : 1,
            'search' => isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '',
        ]);

        ?>
        <!-- Add New Redirect Form -->
        <div class="oneclick-seo-redirect-form">
            <h3><?php esc_html_e('Add New Redirect', 'oneclick-seo-pro'); ?></h3>
            <form method="post">
                <?php wp_nonce_field('oneclick_seo_redirects', 'oneclick_seo_redirect_nonce'); ?>
                <input type="hidden" name="oneclick_seo_redirect_action" value="add">

                <div class="form-row">
                    <div class="form-field">
                        <label><?php esc_html_e('Source URL', 'oneclick-seo-pro'); ?></label>
                        <input type="text" name="source_url" placeholder="/old-page" required>
                    </div>
                    <div class="form-field">
                        <label><?php esc_html_e('Target URL', 'oneclick-seo-pro'); ?></label>
                        <input type="text" name="target_url" placeholder="<?php echo esc_attr(home_url('/new-page')); ?>" required>
                    </div>
                    <div class="form-field" style="flex: 0 0 150px;">
                        <label><?php esc_html_e('Type', 'oneclick-seo-pro'); ?></label>
                        <select name="redirect_type">
                            <option value="301"><?php esc_html_e('301 Permanent', 'oneclick-seo-pro'); ?></option>
                            <option value="302"><?php esc_html_e('302 Temporary', 'oneclick-seo-pro'); ?></option>
                            <option value="307"><?php esc_html_e('307 Temporary', 'oneclick-seo-pro'); ?></option>
                        </select>
                    </div>
                    <div class="form-field" style="flex: 0 0 100px;">
                        <label>
                            <input type="checkbox" name="is_regex" value="1">
                            <?php esc_html_e('Regex', 'oneclick-seo-pro'); ?>
                        </label>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label><?php esc_html_e('Notes (optional)', 'oneclick-seo-pro'); ?></label>
                        <input type="text" name="notes" placeholder="<?php esc_attr_e('Why this redirect was created', 'oneclick-seo-pro'); ?>">
                    </div>
                    <div class="form-field" style="flex: 0 0 150px;">
                        <?php submit_button(__('Add Redirect', 'oneclick-seo-pro'), 'primary', 'submit', false); ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Redirects List -->
        <div class="oneclick-seo-table-actions">
            <form method="get" style="display: flex; gap: 10px;">
                <input type="hidden" name="page" value="oneclick-seo-pro-redirects">
                <input type="search" name="s" value="<?php echo esc_attr($_GET['s'] ?? ''); ?>" placeholder="<?php esc_attr_e('Search redirects...', 'oneclick-seo-pro'); ?>">
                <?php submit_button(__('Search', 'oneclick-seo-pro'), 'secondary', '', false); ?>
            </form>

            <button type="button" class="button" id="oneclick-seo-export-redirects">
                <?php esc_html_e('Export CSV', 'oneclick-seo-pro'); ?>
            </button>
            <button type="button" class="button" id="oneclick-seo-import-redirects">
                <?php esc_html_e('Import CSV', 'oneclick-seo-pro'); ?>
            </button>
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 30px;"><input type="checkbox" id="select-all-redirects"></th>
                    <th><?php esc_html_e('Source', 'oneclick-seo-pro'); ?></th>
                    <th><?php esc_html_e('Target', 'oneclick-seo-pro'); ?></th>
                    <th style="width: 80px;"><?php esc_html_e('Type', 'oneclick-seo-pro'); ?></th>
                    <th style="width: 80px;"><?php esc_html_e('Hits', 'oneclick-seo-pro'); ?></th>
                    <th style="width: 80px;"><?php esc_html_e('Status', 'oneclick-seo-pro'); ?></th>
                    <th style="width: 120px;"><?php esc_html_e('Actions', 'oneclick-seo-pro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($redirects['items'])): ?>
                    <tr>
                        <td colspan="7"><?php esc_html_e('No redirects found.', 'oneclick-seo-pro'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($redirects['items'] as $redirect): ?>
                        <tr>
                            <td><input type="checkbox" class="redirect-checkbox" value="<?php echo esc_attr($redirect->id); ?>"></td>
                            <td>
                                <code><?php echo esc_html($redirect->source_url); ?></code>
                                <?php if ($redirect->is_regex): ?>
                                    <span class="dashicons dashicons-editor-code" title="<?php esc_attr_e('Regex', 'oneclick-seo-pro'); ?>"></span>
                                <?php endif; ?>
                            </td>
                            <td><a href="<?php echo esc_url($redirect->target_url); ?>" target="_blank"><?php echo esc_html($redirect->target_url); ?></a></td>
                            <td><span class="redirect-type-<?php echo esc_attr($redirect->redirect_type); ?>"><?php echo esc_html($redirect->redirect_type); ?></span></td>
                            <td><span class="hits-count"><?php echo esc_html(number_format($redirect->hits)); ?></span></td>
                            <td><span class="status-<?php echo esc_attr($redirect->status); ?>"><?php echo esc_html(ucfirst($redirect->status)); ?></span></td>
                            <td>
                                <button type="button" class="button button-small oneclick-seo-edit-redirect" data-id="<?php echo esc_attr($redirect->id); ?>">
                                    <?php esc_html_e('Edit', 'oneclick-seo-pro'); ?>
                                </button>
                                <button type="button" class="button button-small oneclick-seo-delete-redirect" data-id="<?php echo esc_attr($redirect->id); ?>">
                                    <?php esc_html_e('Delete', 'oneclick-seo-pro'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($redirects['pages'] > 1): ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <?php
                    echo paginate_links([
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'current' => isset($_GET['paged']) ? intval($_GET['paged']) : 1,
                        'total' => $redirects['pages'],
                    ]);
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <script>
        jQuery(document).ready(function($) {
            $('.oneclick-seo-delete-redirect').on('click', function() {
                if (!confirm('<?php esc_html_e('Delete this redirect?', 'oneclick-seo-pro'); ?>')) return;

                var id = $(this).data('id');
                var row = $(this).closest('tr');

                $.post(ajaxurl, {
                    action: 'oneclick_seo_delete_redirect',
                    nonce: '<?php echo esc_js(wp_create_nonce('oneclick_seo_pro_nonce')); ?>',
                    id: id
                }, function(response) {
                    if (response.success) {
                        row.fadeOut(function() { $(this).remove(); });
                    } else {
                        alert(response.data.message);
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Render 404 tab
     */
    private function render_404_tab() {
        $logs = $this->get_404_logs([
            'page' => isset($_GET['paged']) ? intval($_GET['paged']) : 1,
            'search' => isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '',
        ]);

        ?>
        <div class="oneclick-seo-table-actions">
            <form method="get" style="display: flex; gap: 10px;">
                <input type="hidden" name="page" value="oneclick-seo-pro-redirects">
                <input type="hidden" name="tab" value="404">
                <input type="search" name="s" value="<?php echo esc_attr($_GET['s'] ?? ''); ?>" placeholder="<?php esc_attr_e('Search 404s...', 'oneclick-seo-pro'); ?>">
                <?php submit_button(__('Search', 'oneclick-seo-pro'), 'secondary', '', false); ?>
            </form>

            <button type="button" class="button" id="oneclick-seo-clear-404-logs">
                <?php esc_html_e('Clear All Logs', 'oneclick-seo-pro'); ?>
            </button>
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('URL', 'oneclick-seo-pro'); ?></th>
                    <th style="width: 200px;"><?php esc_html_e('Referrer', 'oneclick-seo-pro'); ?></th>
                    <th style="width: 80px;"><?php esc_html_e('Hits', 'oneclick-seo-pro'); ?></th>
                    <th style="width: 150px;"><?php esc_html_e('Last Hit', 'oneclick-seo-pro'); ?></th>
                    <th style="width: 200px;"><?php esc_html_e('Actions', 'oneclick-seo-pro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs['items'])): ?>
                    <tr>
                        <td colspan="5"><?php esc_html_e('No 404 errors logged.', 'oneclick-seo-pro'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs['items'] as $log): ?>
                        <?php $suggestion = $this->suggest_redirect_target($log->url); ?>
                        <tr>
                            <td>
                                <code style="word-break: break-all;"><?php echo esc_html($log->url); ?></code>
                            </td>
                            <td>
                                <?php if ($log->referrer): ?>
                                    <a href="<?php echo esc_url($log->referrer); ?>" target="_blank" title="<?php echo esc_attr($log->referrer); ?>">
                                        <?php echo esc_html(parse_url($log->referrer, PHP_URL_HOST)); ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color: #999;"><?php esc_html_e('Direct', 'oneclick-seo-pro'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><span class="hits-count"><?php echo esc_html(number_format($log->hits)); ?></span></td>
                            <td><?php echo esc_html(human_time_diff(strtotime($log->last_hit)) . ' ago'); ?></td>
                            <td>
                                <button type="button" class="button button-small oneclick-seo-create-redirect-from-404"
                                        data-source="<?php echo esc_attr(parse_url($log->url, PHP_URL_PATH)); ?>"
                                        data-target="<?php echo esc_attr($suggestion['url']); ?>"
                                        data-log-id="<?php echo esc_attr($log->id); ?>">
                                    <?php esc_html_e('Create Redirect', 'oneclick-seo-pro'); ?>
                                </button>
                                <button type="button" class="button button-small oneclick-seo-ignore-404" data-id="<?php echo esc_attr($log->id); ?>">
                                    <?php esc_html_e('Ignore', 'oneclick-seo-pro'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <script>
        jQuery(document).ready(function($) {
            $('.oneclick-seo-create-redirect-from-404').on('click', function() {
                var source = $(this).data('source');
                var target = prompt('<?php esc_html_e('Enter target URL:', 'oneclick-seo-pro'); ?>', $(this).data('target'));

                if (!target) return;

                var row = $(this).closest('tr');

                $.post(ajaxurl, {
                    action: 'oneclick_seo_create_redirect_from_404',
                    nonce: '<?php echo esc_js(wp_create_nonce('oneclick_seo_pro_nonce')); ?>',
                    source: source,
                    target: target,
                    log_id: $(this).data('log-id')
                }, function(response) {
                    if (response.success) {
                        row.css('background', '#d1fae5');
                        alert('<?php esc_html_e('Redirect created!', 'oneclick-seo-pro'); ?>');
                    } else {
                        alert(response.data.message);
                    }
                });
            });

            $('#oneclick-seo-clear-404-logs').on('click', function() {
                if (!confirm('<?php esc_html_e('Clear all 404 logs? This cannot be undone.', 'oneclick-seo-pro'); ?>')) return;

                $.post(ajaxurl, {
                    action: 'oneclick_seo_clear_404_logs',
                    nonce: '<?php echo esc_js(wp_create_nonce('oneclick_seo_pro_nonce')); ?>'
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Render settings tab
     */
    private function render_settings_tab() {
        if (isset($_POST['oneclick_seo_save_redirect_settings'])) {
            check_admin_referer('oneclick_seo_redirect_settings');

            $this->settings = [
                'enabled' => isset($_POST['enabled']),
                'log_404' => isset($_POST['log_404']),
                'auto_redirect' => isset($_POST['auto_redirect']),
                'redirect_attachment' => isset($_POST['redirect_attachment']),
                'preserve_query_string' => isset($_POST['preserve_query_string']),
                'log_retention_days' => intval($_POST['log_retention_days']),
                'max_404_logs' => intval($_POST['max_404_logs']),
            ];

            update_option('oneclick_seo_pro_redirections', $this->settings);

            echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved.', 'oneclick-seo-pro') . '</p></div>';
        }

        ?>
        <form method="post">
            <?php wp_nonce_field('oneclick_seo_redirect_settings'); ?>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('Enable Redirections', 'oneclick-seo-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="enabled" value="1" <?php checked($this->settings['enabled']); ?>>
                            <?php esc_html_e('Process redirects on page load', 'oneclick-seo-pro'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Log 404 Errors', 'oneclick-seo-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="log_404" value="1" <?php checked($this->settings['log_404']); ?>>
                            <?php esc_html_e('Track 404 errors for analysis', 'oneclick-seo-pro'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Redirect Attachments', 'oneclick-seo-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="redirect_attachment" value="1" <?php checked($this->settings['redirect_attachment']); ?>>
                            <?php esc_html_e('Redirect attachment pages to parent post', 'oneclick-seo-pro'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Preserve Query String', 'oneclick-seo-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="preserve_query_string" value="1" <?php checked($this->settings['preserve_query_string']); ?>>
                            <?php esc_html_e('Pass query parameters to redirect target', 'oneclick-seo-pro'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Log Retention (days)', 'oneclick-seo-pro'); ?></th>
                    <td>
                        <input type="number" name="log_retention_days" value="<?php echo esc_attr($this->settings['log_retention_days']); ?>" min="1" max="365" style="width: 100px;">
                        <p class="description"><?php esc_html_e('Delete 404 logs older than this many days', 'oneclick-seo-pro'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Max 404 Logs', 'oneclick-seo-pro'); ?></th>
                    <td>
                        <input type="number" name="max_404_logs" value="<?php echo esc_attr($this->settings['max_404_logs']); ?>" min="100" max="100000" style="width: 100px;">
                        <p class="description"><?php esc_html_e('Maximum number of 404 entries to keep', 'oneclick-seo-pro'); ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button(__('Save Settings', 'oneclick-seo-pro'), 'primary', 'oneclick_seo_save_redirect_settings'); ?>
        </form>
        <?php
    }

    // AJAX Handlers

    public function ajax_add_redirect() {
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'oneclick-seo-pro')]);
        }

        $result = $this->add_redirect([
            'source_url' => sanitize_text_field($_POST['source_url']),
            'target_url' => esc_url_raw($_POST['target_url']),
            'redirect_type' => intval($_POST['redirect_type'] ?? 301),
            'is_regex' => intval($_POST['is_regex'] ?? 0),
        ]);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success(['id' => $result]);
    }

    public function ajax_delete_redirect() {
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'oneclick-seo-pro')]);
        }

        $id = intval($_POST['id']);
        $this->delete_redirect($id);

        wp_send_json_success();
    }

    public function ajax_create_redirect_from_404() {
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'oneclick-seo-pro')]);
        }

        $result = $this->add_redirect([
            'source_url' => sanitize_text_field($_POST['source']),
            'target_url' => esc_url_raw($_POST['target']),
            'redirect_type' => 301,
            'notes' => __('Created from 404 monitor', 'oneclick-seo-pro'),
        ]);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        // Update 404 log status
        if (!empty($_POST['log_id'])) {
            global $wpdb;
            $wpdb->update(
                $this->table_404_logs,
                ['status' => 'redirected', 'redirect_id' => $result],
                ['id' => intval($_POST['log_id'])]
            );
        }

        wp_send_json_success(['id' => $result]);
    }

    public function ajax_clear_404_logs() {
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'oneclick-seo-pro')]);
        }

        global $wpdb;
        $wpdb->query("TRUNCATE TABLE {$this->table_404_logs}");

        wp_send_json_success();
    }

    public function ajax_export_redirects() {
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'oneclick-seo-pro')]);
        }

        global $wpdb;
        $redirects = $wpdb->get_results("SELECT source_url, target_url, redirect_type, is_regex, status, notes FROM {$this->table_redirects}");

        wp_send_json_success(['redirects' => $redirects]);
    }

    public function ajax_import_redirects() {
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'oneclick-seo-pro')]);
        }

        $data = isset($_POST['data']) ? json_decode(stripslashes($_POST['data']), true) : [];
        $imported = 0;

        foreach ($data as $item) {
            $result = $this->add_redirect([
                'source_url' => $item['source_url'],
                'target_url' => $item['target_url'],
                'redirect_type' => $item['redirect_type'] ?? 301,
                'is_regex' => $item['is_regex'] ?? 0,
                'notes' => $item['notes'] ?? '',
            ]);

            if (!is_wp_error($result)) {
                $imported++;
            }
        }

        wp_send_json_success(['imported' => $imported]);
    }

    /**
     * Register REST routes
     */
    public function register_rest_routes() {
        register_rest_route('oneclick-seo-pro/v1', '/redirects', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_get_redirects'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ]);

        register_rest_route('oneclick-seo-pro/v1', '/404-logs', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_get_404_logs'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ]);
    }

    public function rest_get_redirects($request) {
        return rest_ensure_response($this->get_redirects([
            'per_page' => $request->get_param('per_page') ?: 50,
            'page' => $request->get_param('page') ?: 1,
        ]));
    }

    public function rest_get_404_logs($request) {
        return rest_ensure_response($this->get_404_logs([
            'per_page' => $request->get_param('per_page') ?: 50,
            'page' => $request->get_param('page') ?: 1,
        ]));
    }
}
