<?php
/**
 * GTM-Style One-Click Optimizer
 *
 * Like Google Tag Manager, one line of code enables automatic optimizations
 * Analyzes pages and applies optimizations without manual code changes
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_GTM_Style_Optimizer {

    /**
     * Initialize GTM-style optimization
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Auto-optimize on page load (frontend)
        add_action('wp_head', [$this, 'inject_optimization_script'], 1);
        
        // Auto-optimize meta tags
        add_filter('wp_title', [$this, 'optimize_title'], 10, 2);
        add_filter('document_title_parts', [$this, 'optimize_document_title'], 10, 1);
        
        // Auto-optimize meta description
        add_filter('get_the_excerpt', [$this, 'optimize_meta_description'], 10, 2);
        
        // Auto-inject schema
        add_action('wp_head', [$this, 'inject_schema'], 5);
        
        // Auto-optimize images
        add_filter('wp_get_attachment_image_attributes', [$this, 'optimize_image_attributes'], 10, 3);
    }

    /**
     * Inject optimization script (GTM-style)
     */
    public function inject_optimization_script() {
        if (!get_option('wp_bulk_seo_aeo_gtm_enabled', true)) {
            return;
        }

        // One-line script injection point
        $site_id = get_option('wp_bulk_seo_aeo_site_id', '');
        if (empty($site_id)) {
            return;
        }

        ?>
        <!-- WP Bulk SEO & AEO - GTM Style Optimizer -->
        <script>
        (function(w,d,s,o,r,a,m){w['WPBulkSEOAEO']=r;w[r]=w[r]||function(){(w[r].q=w[r].q||[]).push(arguments)};a=d.createElement(s),m=d.getElementsByTagName(s)[0];a.async=1;a.src=o+'?site='+r;a.charset='UTF-8';m.parentNode.insertBefore(a,m)})(window,document,'script','<?php echo esc_url(WP_BULK_SEO_AEO_URL . 'assets/js/gtm-optimizer.js'); ?>','<?php echo esc_js($site_id); ?>');
        </script>
        <!-- End WP Bulk SEO & AEO -->
        <?php
    }

    /**
     * Optimize title automatically
     *
     * @param string $title Title
     * @param string $sep Separator
     * @return string Optimized title
     */
    public function optimize_title($title, $sep = '') {
        if (!is_singular()) {
            return $title;
        }

        $post_id = get_the_ID();
        $optimized_title = get_post_meta($post_id, '_wp_bulk_seo_aeo_optimized_title', true);

        if ($optimized_title) {
            return $optimized_title;
        }

        // Auto-optimize if enabled
        if (get_option('wp_bulk_seo_aeo_auto_optimize_title', true)) {
            $optimized_title = $this->auto_optimize_title($post_id, $title);
            if ($optimized_title && $optimized_title !== $title) {
                update_post_meta($post_id, '_wp_bulk_seo_aeo_optimized_title', $optimized_title);
                return $optimized_title;
            }
        }

        return $title;
    }

    /**
     * Optimize document title parts
     *
     * @param array $title_parts Title parts
     * @return array Optimized title parts
     */
    public function optimize_document_title($title_parts) {
        if (!is_singular()) {
            return $title_parts;
        }

        $post_id = get_the_ID();
        $optimized_title = get_post_meta($post_id, '_wp_bulk_seo_aeo_optimized_title', true);

        if ($optimized_title) {
            $title_parts['title'] = $optimized_title;
        }

        return $title_parts;
    }

    /**
     * Auto-optimize title
     *
     * @param int $post_id Post ID
     * @param string $current_title Current title
     * @return string Optimized title
     */
    private function auto_optimize_title($post_id, $current_title) {
        // Get focus keyword
        $keyword = get_post_meta($post_id, '_wp_bulk_seo_aeo_focus_keyword', true);
        if (empty($keyword)) {
            return $current_title;
        }

        // Check if keyword is in title
        if (stripos($current_title, $keyword) === false) {
            // Add keyword to title (at the beginning if possible)
            $optimized = $keyword . ' - ' . $current_title;
            
            // Ensure length is optimal (50-60 chars)
            if (mb_strlen($optimized) > 60) {
                $optimized = mb_substr($optimized, 0, 57) . '...';
            }
            
            return $optimized;
        }

        // Title already has keyword, just ensure optimal length
        if (mb_strlen($current_title) > 60) {
            return mb_substr($current_title, 0, 57) . '...';
        }

        return $current_title;
    }

    /**
     * Optimize meta description
     *
     * @param string $excerpt Excerpt
     * @param WP_Post $post Post object
     * @return string Optimized description
     */
    public function optimize_meta_description($excerpt, $post = null) {
        if (!is_singular() || !$post) {
            return $excerpt;
        }

        $post_id = $post->ID;
        $optimized_desc = get_post_meta($post_id, '_wp_bulk_seo_aeo_optimized_description', true);

        if ($optimized_desc) {
            return $optimized_desc;
        }

        // Auto-optimize if enabled
        if (get_option('wp_bulk_seo_aeo_auto_optimize_meta', true)) {
            $optimized_desc = $this->auto_optimize_description($post_id, $excerpt);
            if ($optimized_desc && $optimized_desc !== $excerpt) {
                update_post_meta($post_id, '_wp_bulk_seo_aeo_optimized_description', $optimized_desc);
                return $optimized_desc;
            }
        }

        return $excerpt;
    }

    /**
     * Auto-optimize description
     *
     * @param int $post_id Post ID
     * @param string $current_desc Current description
     * @return string Optimized description
     */
    private function auto_optimize_description($post_id, $current_desc) {
        // Ensure optimal length (150-160 chars)
        $length = mb_strlen($current_desc);

        if ($length < 120) {
            // Too short - extend with content
            $post = get_post($post_id);
            $content = wp_strip_all_tags($post->post_content);
            $extended = wp_trim_words($content, 25, '...');
            
            if (mb_strlen($extended) > 160) {
                $extended = mb_substr($extended, 0, 157) . '...';
            }
            
            return $extended;
        } elseif ($length > 160) {
            // Too long - trim
            return mb_substr($current_desc, 0, 157) . '...';
        }

        return $current_desc;
    }

    /**
     * Inject schema automatically
     */
    public function inject_schema() {
        if (!get_option('wp_bulk_seo_aeo_auto_schema', true)) {
            return;
        }

        if (!is_singular()) {
            return;
        }

        $plugin = WP_Bulk_SEO_AEO::instance();
        if ($plugin->schema_generator) {
            $plugin->schema_generator->output_schema();
        }
    }

    /**
     * Optimize image attributes
     *
     * @param array $attr Image attributes
     * @param WP_Post $attachment Attachment post
     * @param string|array $size Image size
     * @return array Optimized attributes
     */
    public function optimize_image_attributes($attr, $attachment, $size) {
        if (!get_option('wp_bulk_seo_aeo_auto_optimize_images', true)) {
            return $attr;
        }

        // Ensure alt text exists
        if (empty($attr['alt'])) {
            $attr['alt'] = get_the_title($attachment->ID);
            
            // If still empty, use attachment filename
            if (empty($attr['alt'])) {
                $filename = basename(get_attached_file($attachment->ID));
                $attr['alt'] = preg_replace('/\.[^.]+$/', '', $filename);
            }
        }

        // Add loading="lazy" if not present
        if (!isset($attr['loading'])) {
            $attr['loading'] = 'lazy';
        }

        return $attr;
    }

    /**
     * Generate site ID for GTM-style tracking
     *
     * @return string Site ID
     */
    public function generate_site_id() {
        $site_id = get_option('wp_bulk_seo_aeo_site_id', '');
        
        if (empty($site_id)) {
            $site_id = 'seo_' . md5(get_site_url() . time());
            update_option('wp_bulk_seo_aeo_site_id', $site_id);
        }

        return $site_id;
    }

    /**
     * Get installation code (one line)
     *
     * @return string Installation code
     */
    public function get_installation_code() {
        $site_id = $this->generate_site_id();
        $plugin_url = WP_BULK_SEO_AEO_URL;

        return sprintf(
            '<script>(function(w,d,s,o,r,a,m){w["WPBulkSEOAEO"]=r;w[r]=w[r]||function(){(w[r].q=w[r].q||[]).push(arguments)};a=d.createElement(s),m=d.getElementsByTagName(s)[0];a.async=1;a.src="%sassets/js/gtm-optimizer.js?site=%s";a.charset="UTF-8";m.parentNode.insertBefore(a,m)})(window,document,"script","","%s");</script>',
            esc_url($plugin_url),
            esc_js($site_id),
            esc_js($site_id)
        );
    }
}
