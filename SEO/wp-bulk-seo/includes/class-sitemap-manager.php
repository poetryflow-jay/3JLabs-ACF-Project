<?php
/**
 * WP Bulk SEO - Sitemap Manager
 *
 * Generates and manages XML sitemaps for optimal search engine crawling.
 * Supports posts, pages, taxonomies, images, and custom post types.
 *
 * @package WP_Bulk_SEO
 * @subpackage Sitemap
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Sitemap_Manager {

    /**
     * Sitemap settings
     */
    private $settings = [];

    /**
     * Items per sitemap
     */
    private const ITEMS_PER_SITEMAP = 1000;

    /**
     * Sitemap types enabled
     */
    private $enabled_types = [
        'post' => true,
        'page' => true,
        'product' => true,
        'category' => true,
        'post_tag' => true,
    ];

    /**
     * Constructor
     */
    public function __construct() {
        $this->load_settings();
        $this->init_hooks();
    }

    /**
     * Load settings
     */
    private function load_settings() {
        $this->settings = get_option('wp_bulk_seo_sitemap', [
            'enabled' => true,
            'include_images' => true,
            'include_lastmod' => true,
            'include_news' => false,
            'exclude_noindex' => true,
            'ping_search_engines' => true,
            'post_types' => ['post', 'page', 'product'],
            'taxonomies' => ['category', 'post_tag', 'product_cat'],
        ]);

        // Merge enabled types from settings
        if (!empty($this->settings['post_types'])) {
            foreach ($this->settings['post_types'] as $type) {
                $this->enabled_types[$type] = true;
            }
        }
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        if (empty($this->settings['enabled'])) {
            return;
        }

        // Register sitemap routes
        add_action('init', [$this, 'register_rewrite_rules']);
        add_filter('query_vars', [$this, 'add_query_vars']);
        add_action('template_redirect', [$this, 'handle_sitemap_request']);

        // Flush rewrite rules on activation
        add_action('wp_bulk_seo_activated', [$this, 'flush_rewrite_rules']);

        // Ping search engines on post publish
        if (!empty($this->settings['ping_search_engines'])) {
            add_action('publish_post', [$this, 'schedule_ping'], 10, 2);
            add_action('publish_page', [$this, 'schedule_ping'], 10, 2);
        }

        // Clear sitemap cache on content changes
        add_action('save_post', [$this, 'clear_sitemap_cache']);
        add_action('deleted_post', [$this, 'clear_sitemap_cache']);
        add_action('created_term', [$this, 'clear_sitemap_cache']);
        add_action('delete_term', [$this, 'clear_sitemap_cache']);
    }

    /**
     * Register rewrite rules for sitemaps
     */
    public function register_rewrite_rules() {
        // Main sitemap index
        add_rewrite_rule(
            '^sitemap\.xml$',
            'index.php?wp_bulk_seo_sitemap=index',
            'top'
        );

        // Post type sitemaps
        add_rewrite_rule(
            '^sitemap-([a-z0-9_-]+)-?(\d*)\.xml$',
            'index.php?wp_bulk_seo_sitemap=$matches[1]&wp_bulk_seo_sitemap_page=$matches[2]',
            'top'
        );

        // XSL stylesheet
        add_rewrite_rule(
            '^sitemap\.xsl$',
            'index.php?wp_bulk_seo_sitemap=xsl',
            'top'
        );
    }

    /**
     * Add query vars
     */
    public function add_query_vars($vars) {
        $vars[] = 'wp_bulk_seo_sitemap';
        $vars[] = 'wp_bulk_seo_sitemap_page';
        return $vars;
    }

    /**
     * Handle sitemap requests
     */
    public function handle_sitemap_request() {
        $sitemap_type = get_query_var('wp_bulk_seo_sitemap');

        if (empty($sitemap_type)) {
            return;
        }

        $page = (int) get_query_var('wp_bulk_seo_sitemap_page', 1);
        if ($page < 1) $page = 1;

        // Set headers
        header('Content-Type: application/xml; charset=utf-8');
        header('X-Robots-Tag: noindex, follow');

        switch ($sitemap_type) {
            case 'index':
                echo $this->generate_sitemap_index();
                break;

            case 'xsl':
                echo $this->generate_xsl_stylesheet();
                break;

            case 'post':
            case 'page':
            case 'product':
                echo $this->generate_post_type_sitemap($sitemap_type, $page);
                break;

            case 'category':
            case 'post_tag':
            case 'product_cat':
                echo $this->generate_taxonomy_sitemap($sitemap_type, $page);
                break;

            default:
                // Custom post type or taxonomy
                if (post_type_exists($sitemap_type)) {
                    echo $this->generate_post_type_sitemap($sitemap_type, $page);
                } elseif (taxonomy_exists($sitemap_type)) {
                    echo $this->generate_taxonomy_sitemap($sitemap_type, $page);
                } else {
                    status_header(404);
                    echo '<?xml version="1.0" encoding="UTF-8"?>';
                    echo '<error>Sitemap not found</error>';
                }
        }

        exit;
    }

    /**
     * Generate sitemap index
     */
    public function generate_sitemap_index() {
        $cache_key = 'wp_bulk_seo_sitemap_index';
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . home_url('/sitemap.xsl') . '"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Post type sitemaps
        $post_types = $this->settings['post_types'] ?? ['post', 'page'];

        foreach ($post_types as $post_type) {
            if (!post_type_exists($post_type)) continue;

            $count = $this->get_post_type_count($post_type);
            $pages = ceil($count / self::ITEMS_PER_SITEMAP);

            if ($count === 0) continue;

            for ($i = 1; $i <= $pages; $i++) {
                $suffix = $pages > 1 ? '-' . $i : '';
                $xml .= $this->sitemap_entry(
                    home_url("/sitemap-{$post_type}{$suffix}.xml"),
                    $this->get_post_type_lastmod($post_type)
                );
            }
        }

        // Taxonomy sitemaps
        $taxonomies = $this->settings['taxonomies'] ?? ['category', 'post_tag'];

        foreach ($taxonomies as $taxonomy) {
            if (!taxonomy_exists($taxonomy)) continue;

            $count = wp_count_terms(['taxonomy' => $taxonomy, 'hide_empty' => true]);
            if (is_wp_error($count) || $count === 0) continue;

            $pages = ceil($count / self::ITEMS_PER_SITEMAP);

            for ($i = 1; $i <= $pages; $i++) {
                $suffix = $pages > 1 ? '-' . $i : '';
                $xml .= $this->sitemap_entry(
                    home_url("/sitemap-{$taxonomy}{$suffix}.xml"),
                    $this->get_taxonomy_lastmod($taxonomy)
                );
            }
        }

        $xml .= '</sitemapindex>';

        set_transient($cache_key, $xml, HOUR_IN_SECONDS);

        return $xml;
    }

    /**
     * Generate post type sitemap
     */
    public function generate_post_type_sitemap($post_type, $page = 1) {
        $cache_key = "wp_bulk_seo_sitemap_{$post_type}_{$page}";
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        $offset = ($page - 1) * self::ITEMS_PER_SITEMAP;

        $args = [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => self::ITEMS_PER_SITEMAP,
            'offset' => $offset,
            'orderby' => 'modified',
            'order' => 'DESC',
            'no_found_rows' => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => false,
        ];

        // Exclude noindex posts if setting enabled
        if (!empty($this->settings['exclude_noindex'])) {
            $args['meta_query'] = [
                'relation' => 'OR',
                [
                    'key' => '_yoast_wpseo_meta-robots-noindex',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key' => '_yoast_wpseo_meta-robots-noindex',
                    'value' => '1',
                    'compare' => '!=',
                ],
            ];
        }

        $posts = get_posts($args);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . home_url('/sitemap.xsl') . '"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';

        if (!empty($this->settings['include_images'])) {
            $xml .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
        }

        $xml .= '>' . "\n";

        foreach ($posts as $post) {
            $xml .= $this->generate_url_entry($post);
        }

        $xml .= '</urlset>';

        set_transient($cache_key, $xml, HOUR_IN_SECONDS);

        return $xml;
    }

    /**
     * Generate taxonomy sitemap
     */
    public function generate_taxonomy_sitemap($taxonomy, $page = 1) {
        $cache_key = "wp_bulk_seo_sitemap_{$taxonomy}_{$page}";
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        $offset = ($page - 1) * self::ITEMS_PER_SITEMAP;

        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
            'number' => self::ITEMS_PER_SITEMAP,
            'offset' => $offset,
            'orderby' => 'count',
            'order' => 'DESC',
        ]);

        if (is_wp_error($terms)) {
            return '<?xml version="1.0" encoding="UTF-8"?><urlset></urlset>';
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . home_url('/sitemap.xsl') . '"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($terms as $term) {
            $url = get_term_link($term);
            if (is_wp_error($url)) continue;

            $lastmod = $this->get_term_lastmod($term);

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . esc_url($url) . "</loc>\n";

            if ($lastmod) {
                $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
            }

            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.6</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        set_transient($cache_key, $xml, HOUR_IN_SECONDS);

        return $xml;
    }

    /**
     * Generate URL entry for a post
     */
    private function generate_url_entry($post) {
        $url = get_permalink($post);
        $lastmod = get_the_modified_date('c', $post);
        $priority = $this->calculate_priority($post);
        $changefreq = $this->calculate_changefreq($post);

        $xml = "  <url>\n";
        $xml .= "    <loc>" . esc_url($url) . "</loc>\n";

        if (!empty($this->settings['include_lastmod']) && $lastmod) {
            $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
        }

        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";

        // Include images
        if (!empty($this->settings['include_images'])) {
            $images = $this->get_post_images($post);
            foreach ($images as $image) {
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . esc_url($image['url']) . "</image:loc>\n";
                if (!empty($image['title'])) {
                    $xml .= "      <image:title>" . esc_xml($image['title']) . "</image:title>\n";
                }
                if (!empty($image['alt'])) {
                    $xml .= "      <image:caption>" . esc_xml($image['alt']) . "</image:caption>\n";
                }
                $xml .= "    </image:image>\n";
            }
        }

        $xml .= "  </url>\n";

        return $xml;
    }

    /**
     * Generate sitemap entry for index
     */
    private function sitemap_entry($url, $lastmod = null) {
        $xml = "  <sitemap>\n";
        $xml .= "    <loc>" . esc_url($url) . "</loc>\n";

        if ($lastmod) {
            $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
        }

        $xml .= "  </sitemap>\n";

        return $xml;
    }

    /**
     * Generate XSL stylesheet for human-readable sitemap
     */
    public function generate_xsl_stylesheet() {
        return '<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>

<xsl:template match="/">
<html>
<head>
    <title>XML Sitemap - ' . esc_html(get_bloginfo('name')) . '</title>
    <meta name="robots" content="noindex, nofollow"/>
    <style type="text/css">
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; }
        h1 { color: #1e3a5f; font-size: 24px; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { text-align: left; padding: 12px 15px; border-bottom: 1px solid #e5e5e5; }
        th { background: #f8f9fa; font-weight: 600; color: #1e3a5f; }
        tr:hover { background: #f8f9fa; }
        a { color: #2271b1; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .info { background: #f0f6fc; border-left: 4px solid #2271b1; padding: 15px; margin-bottom: 20px; }
        .count { color: #666; font-size: 13px; }
    </style>
</head>
<body>
    <h1>XML Sitemap</h1>
    <div class="info">
        This is an XML Sitemap generated by WP Bulk SEO for <strong>' . esc_html(get_bloginfo('name')) . '</strong>.
        It helps search engines like Google discover and index pages on this website.
    </div>

    <xsl:choose>
        <xsl:when test="sitemap:sitemapindex">
            <p class="count">This sitemap index contains <strong><xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"/></strong> sitemaps.</p>
            <table>
                <tr>
                    <th>Sitemap URL</th>
                    <th>Last Modified</th>
                </tr>
                <xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
                    <tr>
                        <td><a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a></td>
                        <td><xsl:value-of select="sitemap:lastmod"/></td>
                    </tr>
                </xsl:for-each>
            </table>
        </xsl:when>
        <xsl:otherwise>
            <p class="count">This sitemap contains <strong><xsl:value-of select="count(sitemap:urlset/sitemap:url)"/></strong> URLs.</p>
            <table>
                <tr>
                    <th>URL</th>
                    <th>Last Modified</th>
                    <th>Change Freq</th>
                    <th>Priority</th>
                    <th>Images</th>
                </tr>
                <xsl:for-each select="sitemap:urlset/sitemap:url">
                    <tr>
                        <td><a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a></td>
                        <td><xsl:value-of select="sitemap:lastmod"/></td>
                        <td><xsl:value-of select="sitemap:changefreq"/></td>
                        <td><xsl:value-of select="sitemap:priority"/></td>
                        <td><xsl:value-of select="count(image:image)"/></td>
                    </tr>
                </xsl:for-each>
            </table>
        </xsl:otherwise>
    </xsl:choose>
</body>
</html>
</xsl:template>
</xsl:stylesheet>';
    }

    /**
     * Calculate priority for a post
     */
    private function calculate_priority($post) {
        // Front page gets highest priority
        if ($post->ID === (int) get_option('page_on_front')) {
            return '1.0';
        }

        // Blog page
        if ($post->ID === (int) get_option('page_for_posts')) {
            return '0.9';
        }

        // Pages generally get higher priority
        if ($post->post_type === 'page') {
            // Top-level pages
            if ($post->post_parent === 0) {
                return '0.8';
            }
            return '0.7';
        }

        // Recent posts get higher priority
        $post_age_days = (time() - strtotime($post->post_date)) / DAY_IN_SECONDS;

        if ($post_age_days < 7) {
            return '0.8';
        } elseif ($post_age_days < 30) {
            return '0.7';
        } elseif ($post_age_days < 90) {
            return '0.6';
        }

        return '0.5';
    }

    /**
     * Calculate change frequency for a post
     */
    private function calculate_changefreq($post) {
        $modified_age_days = (time() - strtotime($post->post_modified)) / DAY_IN_SECONDS;

        if ($modified_age_days < 1) {
            return 'daily';
        } elseif ($modified_age_days < 7) {
            return 'weekly';
        } elseif ($modified_age_days < 30) {
            return 'monthly';
        }

        return 'yearly';
    }

    /**
     * Get images from a post
     */
    private function get_post_images($post) {
        $images = [];

        // Featured image
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        if ($thumbnail_id) {
            $images[] = [
                'url' => wp_get_attachment_url($thumbnail_id),
                'title' => get_the_title($thumbnail_id),
                'alt' => get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true),
            ];
        }

        // Images in content
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $post->post_content, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $src) {
                // Skip data URIs and external images
                if (strpos($src, 'data:') === 0) continue;
                if (strpos($src, home_url()) === false && strpos($src, '/') !== 0) continue;

                // Avoid duplicates
                $found = false;
                foreach ($images as $img) {
                    if ($img['url'] === $src) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $images[] = [
                        'url' => $src,
                        'title' => '',
                        'alt' => '',
                    ];
                }
            }
        }

        return array_slice($images, 0, 10); // Limit to 10 images per URL
    }

    /**
     * Get post type count
     */
    private function get_post_type_count($post_type) {
        $counts = wp_count_posts($post_type);
        return $counts->publish ?? 0;
    }

    /**
     * Get post type last modification date
     */
    private function get_post_type_lastmod($post_type) {
        global $wpdb;

        $result = $wpdb->get_var($wpdb->prepare(
            "SELECT MAX(post_modified_gmt) FROM {$wpdb->posts}
             WHERE post_type = %s AND post_status = 'publish'",
            $post_type
        ));

        if ($result) {
            return gmdate('c', strtotime($result));
        }

        return null;
    }

    /**
     * Get taxonomy last modification date
     */
    private function get_taxonomy_lastmod($taxonomy) {
        global $wpdb;

        // Get the most recently modified post in this taxonomy
        $result = $wpdb->get_var($wpdb->prepare(
            "SELECT MAX(p.post_modified_gmt)
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
             INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
             WHERE tt.taxonomy = %s AND p.post_status = 'publish'",
            $taxonomy
        ));

        if ($result) {
            return gmdate('c', strtotime($result));
        }

        return null;
    }

    /**
     * Get term last modification date
     */
    private function get_term_lastmod($term) {
        global $wpdb;

        $result = $wpdb->get_var($wpdb->prepare(
            "SELECT MAX(p.post_modified_gmt)
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
             WHERE tr.term_taxonomy_id = %d AND p.post_status = 'publish'",
            $term->term_taxonomy_id
        ));

        if ($result) {
            return gmdate('c', strtotime($result));
        }

        return null;
    }

    /**
     * Clear sitemap cache
     */
    public function clear_sitemap_cache() {
        global $wpdb;

        // Delete all sitemap transients
        $wpdb->query(
            "DELETE FROM {$wpdb->options}
             WHERE option_name LIKE '_transient_wp_bulk_seo_sitemap%'
             OR option_name LIKE '_transient_timeout_wp_bulk_seo_sitemap%'"
        );

        // Clear object cache if available
        wp_cache_flush();
    }

    /**
     * Schedule ping to search engines
     */
    public function schedule_ping($post_id, $post) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!wp_next_scheduled('wp_bulk_seo_ping_search_engines')) {
            wp_schedule_single_event(time() + 60, 'wp_bulk_seo_ping_search_engines');
        }
    }

    /**
     * Ping search engines
     */
    public function ping_search_engines() {
        $sitemap_url = home_url('/sitemap.xml');

        // Google
        wp_remote_get('https://www.google.com/ping?sitemap=' . urlencode($sitemap_url), [
            'timeout' => 10,
            'blocking' => false,
        ]);

        // Bing (also covers Yahoo)
        wp_remote_get('https://www.bing.com/ping?sitemap=' . urlencode($sitemap_url), [
            'timeout' => 10,
            'blocking' => false,
        ]);

        // IndexNow (modern protocol)
        $this->ping_indexnow($sitemap_url);
    }

    /**
     * Ping IndexNow API
     */
    private function ping_indexnow($url) {
        $api_key = get_option('wp_bulk_seo_indexnow_key', '');

        if (empty($api_key)) {
            return;
        }

        $host = parse_url(home_url(), PHP_URL_HOST);

        wp_remote_post('https://api.indexnow.org/indexnow', [
            'timeout' => 10,
            'blocking' => false,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => wp_json_encode([
                'host' => $host,
                'key' => $api_key,
                'urlList' => [$url],
            ]),
        ]);
    }

    /**
     * Flush rewrite rules
     */
    public function flush_rewrite_rules() {
        $this->register_rewrite_rules();
        flush_rewrite_rules();
    }

    /**
     * Get sitemap URL
     */
    public function get_sitemap_url() {
        return home_url('/sitemap.xml');
    }

    /**
     * Get sitemap stats
     */
    public function get_sitemap_stats() {
        $stats = [
            'sitemaps' => 0,
            'urls' => 0,
            'post_types' => [],
            'taxonomies' => [],
        ];

        // Count post types
        foreach ($this->settings['post_types'] ?? [] as $post_type) {
            if (!post_type_exists($post_type)) continue;

            $count = $this->get_post_type_count($post_type);
            if ($count > 0) {
                $stats['post_types'][$post_type] = $count;
                $stats['urls'] += $count;
                $stats['sitemaps'] += ceil($count / self::ITEMS_PER_SITEMAP);
            }
        }

        // Count taxonomies
        foreach ($this->settings['taxonomies'] ?? [] as $taxonomy) {
            if (!taxonomy_exists($taxonomy)) continue;

            $count = wp_count_terms(['taxonomy' => $taxonomy, 'hide_empty' => true]);
            if (!is_wp_error($count) && $count > 0) {
                $stats['taxonomies'][$taxonomy] = $count;
                $stats['urls'] += $count;
                $stats['sitemaps'] += ceil($count / self::ITEMS_PER_SITEMAP);
            }
        }

        return $stats;
    }

    /**
     * Exclude URL from sitemap
     */
    public function exclude_url($post_id) {
        update_post_meta($post_id, '_wp_bulk_seo_sitemap_exclude', '1');
        $this->clear_sitemap_cache();
    }

    /**
     * Include URL in sitemap
     */
    public function include_url($post_id) {
        delete_post_meta($post_id, '_wp_bulk_seo_sitemap_exclude');
        $this->clear_sitemap_cache();
    }

    /**
     * Check if URL is excluded
     */
    public function is_excluded($post_id) {
        return get_post_meta($post_id, '_wp_bulk_seo_sitemap_exclude', true) === '1';
    }

    /**
     * Generate news sitemap
     */
    public function generate_news_sitemap() {
        if (empty($this->settings['include_news'])) {
            return '<?xml version="1.0" encoding="UTF-8"?><urlset></urlset>';
        }

        // Get posts from last 48 hours (Google News requirement)
        $posts = get_posts([
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 1000,
            'date_query' => [
                [
                    'after' => '48 hours ago',
                ],
            ],
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        $publication_name = get_bloginfo('name');
        $language = substr(get_bloginfo('language'), 0, 2);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
                         xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

        foreach ($posts as $post) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . esc_url(get_permalink($post)) . "</loc>\n";
            $xml .= "    <news:news>\n";
            $xml .= "      <news:publication>\n";
            $xml .= "        <news:name>" . esc_xml($publication_name) . "</news:name>\n";
            $xml .= "        <news:language>" . esc_xml($language) . "</news:language>\n";
            $xml .= "      </news:publication>\n";
            $xml .= "      <news:publication_date>" . get_the_date('c', $post) . "</news:publication_date>\n";
            $xml .= "      <news:title>" . esc_xml(get_the_title($post)) . "</news:title>\n";
            $xml .= "    </news:news>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}

/**
 * Helper function for escaping XML
 */
if (!function_exists('esc_xml')) {
    function esc_xml($text) {
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        return htmlspecialchars($text, ENT_XML1, 'UTF-8');
    }
}
