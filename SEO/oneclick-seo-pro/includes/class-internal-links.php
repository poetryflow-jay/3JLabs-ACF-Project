<?php
/**
 * OneClick SEO Pro - Internal Link Assistant
 *
 * Smart internal linking suggestions and automation:
 * - Auto-link suggestions based on content
 * - Orphan content detection
 * - Link distribution analysis
 * - Anchor text optimization
 * - Link count monitoring
 *
 * @package OneClick_SEO_Pro
 * @subpackage InternalLinks
 * @version 2.1.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Internal Links Class
 */
class OneClick_SEO_Pro_Internal_Links {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Settings
     */
    private $settings = [];

    /**
     * Link cache
     */
    private $link_cache = [];

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
        $this->settings = get_option('oneclick_seo_pro_internal_links', $this->get_default_settings());
        $this->init_hooks();
    }

    /**
     * Get default settings
     */
    private function get_default_settings() {
        return [
            'enabled' => true,
            'auto_suggestions' => true,
            'min_word_count' => 300,
            'max_links_per_post' => 10,
            'exclude_categories' => [],
            'exclude_post_types' => [],
            'link_keywords' => [],
            'open_external_new_tab' => true,
            'nofollow_external' => false,
        ];
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        if (empty($this->settings['enabled'])) {
            return;
        }

        // Admin hooks
        if (is_admin()) {
            // Meta box for suggestions
            add_action('add_meta_boxes', [$this, 'add_meta_boxes']);

            // Save post hook to update link data
            add_action('save_post', [$this, 'analyze_post_links'], 20, 2);

            // AJAX handlers
            add_action('wp_ajax_oneclick_seo_get_link_suggestions', [$this, 'ajax_get_suggestions']);
            add_action('wp_ajax_oneclick_seo_insert_link', [$this, 'ajax_insert_link']);
            add_action('wp_ajax_oneclick_seo_get_orphan_content', [$this, 'ajax_get_orphan_content']);
            add_action('wp_ajax_oneclick_seo_analyze_link_structure', [$this, 'ajax_analyze_structure']);
        }

        // Content filter for external link handling
        add_filter('the_content', [$this, 'process_external_links'], 99);

        // REST API
        add_action('rest_api_init', [$this, 'register_rest_routes']);

        // Cron for orphan content detection
        add_action('oneclick_seo_pro_analyze_links_cron', [$this, 'cron_analyze_all_links']);
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        $post_types = get_post_types(['public' => true], 'names');
        $excluded = !empty($this->settings['exclude_post_types']) ? $this->settings['exclude_post_types'] : [];

        foreach ($post_types as $post_type) {
            if (in_array($post_type, $excluded)) {
                continue;
            }

            add_meta_box(
                'oneclick_seo_internal_links',
                __('Internal Link Assistant', 'oneclick-seo-pro'),
                [$this, 'render_meta_box'],
                $post_type,
                'side',
                'default'
            );
        }
    }

    /**
     * Render meta box
     *
     * @param WP_Post $post Post object
     */
    public function render_meta_box($post) {
        wp_nonce_field('oneclick_seo_internal_links', 'oneclick_seo_links_nonce');

        // Get current link stats
        $stats = $this->get_post_link_stats($post->ID);
        $suggestions = $this->get_link_suggestions($post->ID, 5);

        ?>
        <div class="oneclick-seo-links-box">
            <style>
                .oneclick-seo-links-box { font-size: 13px; }
                .oneclick-seo-links-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; }
                .oneclick-seo-links-stat { background: #f9f9f9; padding: 10px; border-radius: 4px; text-align: center; }
                .oneclick-seo-links-stat-value { font-size: 20px; font-weight: 600; color: #1e3a5f; }
                .oneclick-seo-links-stat-label { font-size: 11px; color: #666; }
                .oneclick-seo-suggestion-item { padding: 8px; margin: 5px 0; background: #f0f7ff; border-radius: 4px; border-left: 3px solid #3b82f6; }
                .oneclick-seo-suggestion-title { font-weight: 500; margin-bottom: 3px; }
                .oneclick-seo-suggestion-keyword { font-size: 11px; color: #666; }
                .oneclick-seo-btn-sm { padding: 3px 8px; font-size: 11px; cursor: pointer; }
                .oneclick-seo-links-health { padding: 10px; border-radius: 4px; margin-bottom: 15px; }
                .oneclick-seo-links-health.good { background: #d1fae5; border-left: 3px solid #10b981; }
                .oneclick-seo-links-health.warning { background: #fef3c7; border-left: 3px solid #f59e0b; }
                .oneclick-seo-links-health.bad { background: #fee2e2; border-left: 3px solid #ef4444; }
            </style>

            <!-- Link Stats -->
            <div class="oneclick-seo-links-stats">
                <div class="oneclick-seo-links-stat">
                    <div class="oneclick-seo-links-stat-value"><?php echo esc_html($stats['outbound_internal']); ?></div>
                    <div class="oneclick-seo-links-stat-label"><?php esc_html_e('Internal Links', 'oneclick-seo-pro'); ?></div>
                </div>
                <div class="oneclick-seo-links-stat">
                    <div class="oneclick-seo-links-stat-value"><?php echo esc_html($stats['inbound']); ?></div>
                    <div class="oneclick-seo-links-stat-label"><?php esc_html_e('Inbound Links', 'oneclick-seo-pro'); ?></div>
                </div>
            </div>

            <!-- Health Indicator -->
            <?php
            $health_class = 'good';
            $health_message = __('Good internal linking!', 'oneclick-seo-pro');

            if ($stats['outbound_internal'] < 2) {
                $health_class = 'warning';
                $health_message = __('Add more internal links', 'oneclick-seo-pro');
            }
            if ($stats['outbound_internal'] === 0 && $stats['inbound'] === 0) {
                $health_class = 'bad';
                $health_message = __('Orphan content - no links!', 'oneclick-seo-pro');
            }
            ?>
            <div class="oneclick-seo-links-health <?php echo esc_attr($health_class); ?>">
                <?php echo esc_html($health_message); ?>
            </div>

            <!-- Suggestions -->
            <?php if (!empty($suggestions)): ?>
                <h4 style="margin: 0 0 10px;"><?php esc_html_e('Suggested Links', 'oneclick-seo-pro'); ?></h4>
                <div class="oneclick-seo-suggestions-list">
                    <?php foreach ($suggestions as $suggestion): ?>
                        <div class="oneclick-seo-suggestion-item" data-post-id="<?php echo esc_attr($suggestion['post_id']); ?>">
                            <div class="oneclick-seo-suggestion-title">
                                <?php echo esc_html(wp_trim_words($suggestion['title'], 6)); ?>
                            </div>
                            <div class="oneclick-seo-suggestion-keyword">
                                <?php esc_html_e('Keyword:', 'oneclick-seo-pro'); ?>
                                <strong><?php echo esc_html($suggestion['keyword']); ?></strong>
                            </div>
                            <button type="button" class="button oneclick-seo-btn-sm oneclick-seo-copy-link"
                                    data-url="<?php echo esc_url($suggestion['url']); ?>"
                                    data-title="<?php echo esc_attr($suggestion['title']); ?>">
                                <?php esc_html_e('Copy Link', 'oneclick-seo-pro'); ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #666; font-style: italic;">
                    <?php esc_html_e('No suggestions available. Write more content to see link suggestions.', 'oneclick-seo-pro'); ?>
                </p>
            <?php endif; ?>

            <script>
            jQuery(document).ready(function($) {
                $('.oneclick-seo-copy-link').on('click', function() {
                    var url = $(this).data('url');
                    var title = $(this).data('title');
                    var linkHtml = '<a href="' + url + '">' + title + '</a>';

                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(linkHtml).then(function() {
                            alert('<?php esc_html_e('Link HTML copied!', 'oneclick-seo-pro'); ?>');
                        });
                    }
                });
            });
            </script>
        </div>
        <?php
    }

    /**
     * Analyze post links on save
     *
     * @param int $post_id Post ID
     * @param WP_Post $post Post object
     */
    public function analyze_post_links($post_id, $post) {
        // Skip autosave, revisions, etc.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }

        if ($post->post_status !== 'publish') {
            return;
        }

        // Analyze and store link data
        $link_data = $this->analyze_content_links($post->post_content, $post_id);

        update_post_meta($post_id, '_oneclick_seo_link_data', $link_data);
        update_post_meta($post_id, '_oneclick_seo_link_analyzed', current_time('mysql'));

        // Update inbound link counts for linked posts
        foreach ($link_data['internal_links'] as $link) {
            if (!empty($link['post_id'])) {
                $this->increment_inbound_count($link['post_id'], $post_id);
            }
        }
    }

    /**
     * Analyze content for links
     *
     * @param string $content Post content
     * @param int $current_post_id Current post ID
     * @return array Link analysis data
     */
    public function analyze_content_links($content, $current_post_id = 0) {
        $data = [
            'internal_links' => [],
            'external_links' => [],
            'outbound_internal_count' => 0,
            'outbound_external_count' => 0,
            'anchor_texts' => [],
        ];

        if (empty($content)) {
            return $data;
        }

        $site_url = home_url();
        $site_host = parse_url($site_url, PHP_URL_HOST);

        // Find all links
        preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $url = $match[1];
            $anchor = wp_strip_all_tags($match[2]);
            $full_tag = $match[0];

            // Skip anchors and javascript
            if (strpos($url, '#') === 0 || strpos($url, 'javascript:') === 0) {
                continue;
            }

            // Parse URL
            $parsed = parse_url($url);
            $link_host = !empty($parsed['host']) ? $parsed['host'] : $site_host;

            // Check if internal
            if ($link_host === $site_host || strpos($url, '/') === 0) {
                // Get post ID from URL
                $linked_post_id = url_to_postid($url);

                $data['internal_links'][] = [
                    'url' => $url,
                    'anchor' => $anchor,
                    'post_id' => $linked_post_id,
                    'post_title' => $linked_post_id ? get_the_title($linked_post_id) : '',
                ];
                $data['outbound_internal_count']++;

                if ($anchor) {
                    $data['anchor_texts'][] = $anchor;
                }
            } else {
                $data['external_links'][] = [
                    'url' => $url,
                    'anchor' => $anchor,
                    'domain' => $link_host,
                ];
                $data['outbound_external_count']++;
            }
        }

        return $data;
    }

    /**
     * Get post link stats
     *
     * @param int $post_id Post ID
     * @return array Link stats
     */
    public function get_post_link_stats($post_id) {
        $link_data = get_post_meta($post_id, '_oneclick_seo_link_data', true);
        $inbound_posts = get_post_meta($post_id, '_oneclick_seo_inbound_links', true);

        return [
            'outbound_internal' => !empty($link_data['outbound_internal_count']) ? $link_data['outbound_internal_count'] : 0,
            'outbound_external' => !empty($link_data['outbound_external_count']) ? $link_data['outbound_external_count'] : 0,
            'inbound' => is_array($inbound_posts) ? count($inbound_posts) : 0,
            'internal_links' => !empty($link_data['internal_links']) ? $link_data['internal_links'] : [],
            'external_links' => !empty($link_data['external_links']) ? $link_data['external_links'] : [],
        ];
    }

    /**
     * Get link suggestions for a post
     *
     * @param int $post_id Post ID
     * @param int $limit Number of suggestions
     * @return array Suggestions
     */
    public function get_link_suggestions($post_id, $limit = 10) {
        $post = get_post($post_id);
        if (!$post) {
            return [];
        }

        $suggestions = [];

        // Get existing internal links to avoid duplicates
        $link_data = get_post_meta($post_id, '_oneclick_seo_link_data', true);
        $existing_links = [];
        if (!empty($link_data['internal_links'])) {
            foreach ($link_data['internal_links'] as $link) {
                if (!empty($link['post_id'])) {
                    $existing_links[] = $link['post_id'];
                }
            }
        }

        // Extract keywords from current post
        $content = strip_tags($post->post_content . ' ' . $post->post_title);
        $keywords = $this->extract_keywords($content);

        // Find posts with matching keywords
        global $wpdb;

        foreach ($keywords as $keyword) {
            if (count($suggestions) >= $limit) {
                break;
            }

            // Search for posts containing this keyword
            $found_posts = $wpdb->get_results($wpdb->prepare(
                "SELECT ID, post_title FROM {$wpdb->posts}
                 WHERE post_status = 'publish'
                 AND post_type IN ('post', 'page')
                 AND ID != %d
                 AND (post_title LIKE %s OR post_content LIKE %s)
                 LIMIT 5",
                $post_id,
                '%' . $wpdb->esc_like($keyword) . '%',
                '%' . $wpdb->esc_like($keyword) . '%'
            ));

            foreach ($found_posts as $found_post) {
                if (count($suggestions) >= $limit) {
                    break;
                }

                // Skip if already linked
                if (in_array($found_post->ID, $existing_links)) {
                    continue;
                }

                // Skip if already in suggestions
                $already_suggested = false;
                foreach ($suggestions as $s) {
                    if ($s['post_id'] === $found_post->ID) {
                        $already_suggested = true;
                        break;
                    }
                }

                if ($already_suggested) {
                    continue;
                }

                $suggestions[] = [
                    'post_id' => $found_post->ID,
                    'title' => $found_post->post_title,
                    'url' => get_permalink($found_post->ID),
                    'keyword' => $keyword,
                    'relevance' => $this->calculate_relevance($post->post_content, $found_post->post_title),
                ];

                $existing_links[] = $found_post->ID;
            }
        }

        // Sort by relevance
        usort($suggestions, function($a, $b) {
            return $b['relevance'] - $a['relevance'];
        });

        return array_slice($suggestions, 0, $limit);
    }

    /**
     * Extract keywords from content
     *
     * @param string $content Content to analyze
     * @return array Keywords
     */
    private function extract_keywords($content) {
        // Common stop words
        $stop_words = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from', 'as', 'is', 'was', 'are', 'were', 'been', 'be', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'can', 'this', 'that', 'these', 'those', 'it', 'its'];

        // Korean stop words
        $korean_stop_words = ['은', '는', '이', '가', '을', '를', '의', '에', '에서', '로', '으로', '와', '과', '도', '만', '까지', '부터', '라고', '이라고', '다', '고', '하다', '있다', '되다', '것', '수'];

        $stop_words = array_merge($stop_words, $korean_stop_words);

        // Clean content
        $content = strtolower($content);
        $content = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $content);
        $content = preg_replace('/\s+/', ' ', $content);

        // Get words
        $words = explode(' ', $content);

        // Filter and count
        $word_count = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) < 3 || in_array($word, $stop_words)) {
                continue;
            }
            if (!isset($word_count[$word])) {
                $word_count[$word] = 0;
            }
            $word_count[$word]++;
        }

        // Sort by frequency
        arsort($word_count);

        // Get top keywords
        $keywords = array_slice(array_keys($word_count), 0, 20);

        // Also look for 2-word phrases
        preg_match_all('/\b(\w+\s+\w+)\b/u', $content, $phrases);
        $phrase_count = [];
        foreach ($phrases[1] as $phrase) {
            $phrase = trim($phrase);
            $phrase_words = explode(' ', $phrase);
            $has_stop_word = false;
            foreach ($phrase_words as $pw) {
                if (in_array($pw, $stop_words)) {
                    $has_stop_word = true;
                    break;
                }
            }
            if ($has_stop_word) {
                continue;
            }
            if (!isset($phrase_count[$phrase])) {
                $phrase_count[$phrase] = 0;
            }
            $phrase_count[$phrase]++;
        }
        arsort($phrase_count);

        // Merge phrases with keywords
        $top_phrases = array_slice(array_keys($phrase_count), 0, 10);
        $keywords = array_merge($top_phrases, $keywords);

        return array_unique(array_slice($keywords, 0, 30));
    }

    /**
     * Calculate relevance score
     *
     * @param string $source_content Source content
     * @param string $target_title Target post title
     * @return float Relevance score
     */
    private function calculate_relevance($source_content, $target_title) {
        $source = strtolower($source_content);
        $title_words = explode(' ', strtolower($target_title));

        $score = 0;
        foreach ($title_words as $word) {
            if (strlen($word) > 3) {
                $count = substr_count($source, $word);
                $score += min($count, 5); // Cap at 5 occurrences
            }
        }

        return $score;
    }

    /**
     * Increment inbound link count
     *
     * @param int $target_post_id Post being linked to
     * @param int $source_post_id Post containing the link
     */
    private function increment_inbound_count($target_post_id, $source_post_id) {
        $inbound_links = get_post_meta($target_post_id, '_oneclick_seo_inbound_links', true);

        if (!is_array($inbound_links)) {
            $inbound_links = [];
        }

        if (!in_array($source_post_id, $inbound_links)) {
            $inbound_links[] = $source_post_id;
            update_post_meta($target_post_id, '_oneclick_seo_inbound_links', $inbound_links);
        }
    }

    /**
     * Get orphan content (posts with no internal links)
     *
     * @param int $limit Number of results
     * @return array Orphan posts
     */
    public function get_orphan_content($limit = 50) {
        global $wpdb;

        // Get posts with no inbound links
        $orphans = $wpdb->get_results($wpdb->prepare(
            "SELECT p.ID, p.post_title, p.post_type, p.post_date
             FROM {$wpdb->posts} p
             LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_oneclick_seo_inbound_links'
             WHERE p.post_status = 'publish'
             AND p.post_type IN ('post', 'page')
             AND (pm.meta_value IS NULL OR pm.meta_value = '' OR pm.meta_value = 'a:0:{}')
             ORDER BY p.post_date DESC
             LIMIT %d",
            $limit
        ));

        $results = [];
        foreach ($orphans as $orphan) {
            $link_data = get_post_meta($orphan->ID, '_oneclick_seo_link_data', true);

            $results[] = [
                'ID' => $orphan->ID,
                'title' => $orphan->post_title,
                'post_type' => $orphan->post_type,
                'date' => $orphan->post_date,
                'url' => get_permalink($orphan->ID),
                'edit_url' => get_edit_post_link($orphan->ID),
                'outbound_links' => !empty($link_data['outbound_internal_count']) ? $link_data['outbound_internal_count'] : 0,
            ];
        }

        return $results;
    }

    /**
     * Get link distribution stats
     *
     * @return array Distribution stats
     */
    public function get_link_distribution_stats() {
        global $wpdb;

        $total_posts = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_status = 'publish' AND post_type IN ('post', 'page')"
        );

        // Posts with internal outbound links
        $with_outbound = (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
             WHERE p.post_status = 'publish'
             AND p.post_type IN ('post', 'page')
             AND pm.meta_key = '_oneclick_seo_link_data'
             AND pm.meta_value LIKE '%outbound_internal_count\";i:%'
             AND pm.meta_value NOT LIKE '%outbound_internal_count\";i:0%'"
        );

        // Posts with inbound links
        $with_inbound = (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
             WHERE p.post_status = 'publish'
             AND p.post_type IN ('post', 'page')
             AND pm.meta_key = '_oneclick_seo_inbound_links'
             AND pm.meta_value != ''
             AND pm.meta_value != 'a:0:{}'"
        );

        // Top linked posts
        $top_linked = $wpdb->get_results(
            "SELECT p.ID, p.post_title, LENGTH(pm.meta_value) - LENGTH(REPLACE(pm.meta_value, 'i:', '')) as link_count
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
             WHERE p.post_status = 'publish'
             AND pm.meta_key = '_oneclick_seo_inbound_links'
             ORDER BY link_count DESC
             LIMIT 10"
        );

        return [
            'total_posts' => $total_posts,
            'with_outbound_links' => $with_outbound,
            'without_outbound_links' => $total_posts - $with_outbound,
            'with_inbound_links' => $with_inbound,
            'orphan_count' => $total_posts - $with_inbound,
            'coverage_percentage' => $total_posts > 0 ? round(($with_inbound / $total_posts) * 100) : 0,
            'top_linked_posts' => $top_linked,
        ];
    }

    /**
     * Process external links in content
     *
     * @param string $content Post content
     * @return string Modified content
     */
    public function process_external_links($content) {
        if (empty($content)) {
            return $content;
        }

        $site_host = parse_url(home_url(), PHP_URL_HOST);

        // Find and modify external links
        $content = preg_replace_callback(
            '/<a([^>]+)href=["\']([^"\']+)["\']([^>]*)>/i',
            function($matches) use ($site_host) {
                $before = $matches[1];
                $url = $matches[2];
                $after = $matches[3];

                // Parse URL
                $parsed = parse_url($url);
                $link_host = !empty($parsed['host']) ? $parsed['host'] : '';

                // Skip internal links
                if (empty($link_host) || $link_host === $site_host) {
                    return $matches[0];
                }

                $new_attrs = '';

                // Add target="_blank" for external
                if (!empty($this->settings['open_external_new_tab'])) {
                    if (strpos($before . $after, 'target=') === false) {
                        $new_attrs .= ' target="_blank"';
                    }
                    if (strpos($before . $after, 'rel=') === false) {
                        $new_attrs .= ' rel="noopener noreferrer';
                        if (!empty($this->settings['nofollow_external'])) {
                            $new_attrs .= ' nofollow';
                        }
                        $new_attrs .= '"';
                    }
                }

                return '<a' . $before . 'href="' . $url . '"' . $after . $new_attrs . '>';
            },
            $content
        );

        return $content;
    }

    /**
     * AJAX: Get link suggestions
     */
    public function ajax_get_suggestions() {
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('Permission denied', 'oneclick-seo-pro')]);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;

        if (!$post_id) {
            wp_send_json_error(['message' => __('Invalid post ID', 'oneclick-seo-pro')]);
        }

        $suggestions = $this->get_link_suggestions($post_id, $limit);

        wp_send_json_success([
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * AJAX: Get orphan content
     */
    public function ajax_get_orphan_content() {
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('Permission denied', 'oneclick-seo-pro')]);
        }

        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 50;
        $orphans = $this->get_orphan_content($limit);

        wp_send_json_success([
            'orphans' => $orphans,
            'count' => count($orphans),
        ]);
    }

    /**
     * AJAX: Analyze link structure
     */
    public function ajax_analyze_structure() {
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'oneclick-seo-pro')]);
        }

        $stats = $this->get_link_distribution_stats();

        wp_send_json_success($stats);
    }

    /**
     * Cron: Analyze all links
     */
    public function cron_analyze_all_links() {
        global $wpdb;

        // Get posts not analyzed recently
        $posts = $wpdb->get_results(
            "SELECT p.ID, p.post_content FROM {$wpdb->posts} p
             LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_oneclick_seo_link_analyzed'
             WHERE p.post_status = 'publish'
             AND p.post_type IN ('post', 'page')
             AND (pm.meta_value IS NULL OR pm.meta_value < DATE_SUB(NOW(), INTERVAL 7 DAY))
             LIMIT 50"
        );

        foreach ($posts as $post) {
            $link_data = $this->analyze_content_links($post->post_content, $post->ID);
            update_post_meta($post->ID, '_oneclick_seo_link_data', $link_data);
            update_post_meta($post->ID, '_oneclick_seo_link_analyzed', current_time('mysql'));
        }
    }

    /**
     * Register REST routes
     */
    public function register_rest_routes() {
        register_rest_route('oneclick-seo-pro/v1', '/internal-links/stats', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_get_stats'],
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            },
        ]);

        register_rest_route('oneclick-seo-pro/v1', '/internal-links/suggestions/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_get_suggestions'],
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            },
        ]);

        register_rest_route('oneclick-seo-pro/v1', '/internal-links/orphans', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_get_orphans'],
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            },
        ]);
    }

    /**
     * REST: Get stats
     */
    public function rest_get_stats($request) {
        return rest_ensure_response($this->get_link_distribution_stats());
    }

    /**
     * REST: Get suggestions
     */
    public function rest_get_suggestions($request) {
        $post_id = $request->get_param('id');
        $limit = $request->get_param('limit') ?: 10;

        return rest_ensure_response([
            'suggestions' => $this->get_link_suggestions($post_id, $limit),
        ]);
    }

    /**
     * REST: Get orphans
     */
    public function rest_get_orphans($request) {
        $limit = $request->get_param('limit') ?: 50;

        return rest_ensure_response([
            'orphans' => $this->get_orphan_content($limit),
        ]);
    }
}
