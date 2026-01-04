<?php
/**
 * Sitemap Manager Class
 *
 * Manages XML sitemap generation and submission
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Sitemap_Manager {

    /**
     * Generate XML sitemap
     *
     * @return string XML sitemap content
     */
    public function generate_sitemap() {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Get all public post types
        $post_types = get_post_types(['public' => true], 'objects');

        foreach ($post_types as $post_type) {
            $posts = get_posts([
                'post_type' => $post_type->name,
                'post_status' => 'publish',
                'numberposts' => -1,
                'orderby' => 'modified',
                'order' => 'DESC',
            ]);

            foreach ($posts as $post) {
                $sitemap .= $this->generate_url_entry($post);
            }
        }

        $sitemap .= '</urlset>';

        return $sitemap;
    }

    /**
     * Generate URL entry for sitemap
     *
     * @param WP_Post $post Post object
     * @return string URL entry XML
     */
    private function generate_url_entry($post) {
        $url = get_permalink($post->ID);
        $modified = get_the_modified_date('c', $post->ID);
        $priority = $this->calculate_priority($post);
        $changefreq = $this->calculate_changefreq($post);

        $entry = "  <url>\n";
        $entry .= "    <loc>" . esc_url($url) . "</loc>\n";
        $entry .= "    <lastmod>" . esc_html($modified) . "</lastmod>\n";
        $entry .= "    <changefreq>" . esc_html($changefreq) . "</changefreq>\n";
        $entry .= "    <priority>" . esc_html($priority) . "</priority>\n";
        $entry .= "  </url>\n";

        return $entry;
    }

    /**
     * Calculate priority for sitemap entry
     *
     * @param WP_Post $post Post object
     * @return float Priority (0.0 to 1.0)
     */
    private function calculate_priority($post) {
        // Homepage
        if ($post->ID == get_option('page_on_front')) {
            return 1.0;
        }

        // Pages
        if ($post->post_type === 'page') {
            return 0.8;
        }

        // Recent posts
        $days_old = (time() - strtotime($post->post_date)) / (60 * 60 * 24);
        if ($days_old < 30) {
            return 0.9;
        } elseif ($days_old < 90) {
            return 0.7;
        }

        return 0.5;
    }

    /**
     * Calculate changefreq for sitemap entry
     *
     * @param WP_Post $post Post object
     * @return string Changefreq value
     */
    private function calculate_changefreq($post) {
        $days_old = (time() - strtotime($post->post_modified)) / (60 * 60 * 24);

        if ($days_old < 7) {
            return 'daily';
        } elseif ($days_old < 30) {
            return 'weekly';
        } elseif ($days_old < 90) {
            return 'monthly';
        }

        return 'yearly';
    }

    /**
     * Save sitemap to file
     *
     * @param string $filename Filename (default: sitemap.xml)
     * @return bool|WP_Error Success or error
     */
    public function save_sitemap($filename = 'sitemap.xml') {
        $sitemap_content = $this->generate_sitemap();
        $upload_dir = wp_upload_dir();
        $sitemap_path = $upload_dir['basedir'] . '/' . $filename;

        $result = file_put_contents($sitemap_path, $sitemap_content);

        if ($result === false) {
            return new WP_Error('sitemap_save_failed', 'Failed to save sitemap file');
        }

        return true;
    }

    /**
     * Get sitemap URL
     *
     * @param string $filename Filename
     * @return string Sitemap URL
     */
    public function get_sitemap_url($filename = 'sitemap.xml') {
        $upload_dir = wp_upload_dir();
        return $upload_dir['baseurl'] . '/' . $filename;
    }

    /**
     * Submit sitemap to Google Search Console
     *
     * @param string $sitemap_url Sitemap URL
     * @return bool|WP_Error Success or error
     */
    public function submit_to_google($sitemap_url) {
        // This would require Google Search Console API integration
        // For now, return success (actual implementation would use the API class)
        return true;
    }
}
