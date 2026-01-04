<?php
/**
 * WP Bulk SEO - Frontend
 *
 * Handles frontend output (meta tags, schema, etc.)
 *
 * @package OneClick_SEO_Pro
 * @subpackage Frontend
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Frontend Class
 */
class OneClick_SEO_Pro_Frontend {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Get instance
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
        // Meta tags
        add_action('wp_head', [$this, 'output_meta_tags'], 1);
        add_action('wp_head', [$this, 'output_schema'], 99);

        // Title tag
        add_filter('pre_get_document_title', [$this, 'filter_document_title'], 15);
        add_filter('document_title_parts', [$this, 'filter_title_parts'], 15);

        // Canonical
        remove_action('wp_head', 'rel_canonical');
        add_action('wp_head', [$this, 'output_canonical'], 2);

        // Open Graph
        add_action('wp_head', [$this, 'output_open_graph'], 5);

        // Twitter Cards
        add_action('wp_head', [$this, 'output_twitter_cards'], 6);

        // Robots
        add_filter('wp_robots', [$this, 'filter_robots'], 15);
    }

    /**
     * Output meta tags
     */
    public function output_meta_tags() {
        if (!is_singular()) {
            return;
        }

        $post_id = get_the_ID();
        $description = get_post_meta($post_id, '_oneclick_seo_pro_description', true);

        if (empty($description)) {
            $description = $this->get_auto_description($post_id);
        }

        if ($description) {
            echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        }

        // Focus keyword
        $focus_keyword = get_post_meta($post_id, '_oneclick_seo_pro_focus_keyword', true);
        if ($focus_keyword) {
            // Note: keywords meta tag is largely ignored by search engines, but some tools still use it
            echo '<meta name="keywords" content="' . esc_attr($focus_keyword) . '">' . "\n";
        }
    }

    /**
     * Output canonical URL
     */
    public function output_canonical() {
        if (!is_singular()) {
            rel_canonical();
            return;
        }

        $post_id = get_the_ID();
        $canonical = get_post_meta($post_id, '_oneclick_seo_pro_canonical', true);

        if (empty($canonical)) {
            $canonical = get_permalink($post_id);
        }

        echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
    }

    /**
     * Filter document title
     */
    public function filter_document_title($title) {
        if (!is_singular()) {
            return $title;
        }

        $post_id = get_the_ID();
        $seo_title = get_post_meta($post_id, '_oneclick_seo_pro_title', true);

        if (!empty($seo_title)) {
            return $seo_title;
        }

        return $title;
    }

    /**
     * Filter title parts
     */
    public function filter_title_parts($title_parts) {
        if (!is_singular()) {
            return $title_parts;
        }

        $post_id = get_the_ID();
        $seo_title = get_post_meta($post_id, '_oneclick_seo_pro_title', true);

        if (!empty($seo_title)) {
            $title_parts['title'] = $seo_title;
            // Remove site name if SEO title is set (assumes it includes branding if desired)
            unset($title_parts['site']);
            unset($title_parts['tagline']);
        }

        return $title_parts;
    }

    /**
     * Output Open Graph tags
     */
    public function output_open_graph() {
        if (!is_singular()) {
            return;
        }

        $post = get_post();
        $post_id = $post->ID;

        // Basic OG tags
        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink($post_id)) . '">' . "\n";

        // Title
        $og_title = get_post_meta($post_id, '_oneclick_seo_pro_title', true) ?: get_the_title($post_id);
        echo '<meta property="og:title" content="' . esc_attr($og_title) . '">' . "\n";

        // Description
        $og_description = get_post_meta($post_id, '_oneclick_seo_pro_description', true);
        if (empty($og_description)) {
            $og_description = $this->get_auto_description($post_id);
        }
        if ($og_description) {
            echo '<meta property="og:description" content="' . esc_attr($og_description) . '">' . "\n";
        }

        // Image
        $og_image = $this->get_og_image($post_id);
        if ($og_image) {
            echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
        }

        // Site name
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";

        // Article specific
        echo '<meta property="article:published_time" content="' . esc_attr(get_the_date('c', $post_id)) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr(get_the_modified_date('c', $post_id)) . '">' . "\n";

        // Author
        $author_id = $post->post_author;
        $author_name = get_the_author_meta('display_name', $author_id);
        if ($author_name) {
            echo '<meta property="article:author" content="' . esc_attr($author_name) . '">' . "\n";
        }
    }

    /**
     * Output Twitter Cards
     */
    public function output_twitter_cards() {
        if (!is_singular()) {
            return;
        }

        $post_id = get_the_ID();

        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

        // Title
        $title = get_post_meta($post_id, '_oneclick_seo_pro_title', true) ?: get_the_title($post_id);
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";

        // Description
        $description = get_post_meta($post_id, '_oneclick_seo_pro_description', true);
        if (empty($description)) {
            $description = $this->get_auto_description($post_id);
        }
        if ($description) {
            echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
        }

        // Image
        $image = $this->get_og_image($post_id);
        if ($image) {
            echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
        }
    }

    /**
     * Output Schema markup
     */
    public function output_schema() {
        $schema_generator = OneClick_SEO_Pro_Schema_Generator::get_instance();

        if (is_front_page()) {
            // Website + Organization schema
            $website_schema = $schema_generator->generate_website_schema();
            $org_schema = $schema_generator->generate_organization_schema();

            if ($website_schema) {
                echo '<script type="application/ld+json">' . wp_json_encode($website_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
            }
            if ($org_schema) {
                echo '<script type="application/ld+json">' . wp_json_encode($org_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
            }
        } elseif (is_singular()) {
            $post = get_post();
            $schemas = $schema_generator->generate_all_schema($post);

            foreach ($schemas as $schema) {
                if (!empty($schema)) {
                    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
                }
            }
        }
    }

    /**
     * Filter robots meta
     */
    public function filter_robots($robots) {
        if (!is_singular()) {
            return $robots;
        }

        $post_id = get_the_ID();
        $robots_meta = get_post_meta($post_id, '_oneclick_seo_pro_robots', true);

        if (empty($robots_meta)) {
            return $robots;
        }

        $directives = array_map('trim', explode(',', $robots_meta));

        foreach ($directives as $directive) {
            if (strpos($directive, 'noindex') !== false) {
                $robots['noindex'] = true;
                unset($robots['index']);
            }
            if (strpos($directive, 'nofollow') !== false) {
                $robots['nofollow'] = true;
                unset($robots['follow']);
            }
            if (strpos($directive, 'noarchive') !== false) {
                $robots['noarchive'] = true;
            }
            if (strpos($directive, 'nosnippet') !== false) {
                $robots['nosnippet'] = true;
            }
            if (strpos($directive, 'noimageindex') !== false) {
                $robots['noimageindex'] = true;
            }
        }

        return $robots;
    }

    /**
     * Get auto-generated description
     */
    private function get_auto_description($post_id) {
        $post = get_post($post_id);

        if (!$post) {
            return '';
        }

        // Try excerpt first
        if (!empty($post->post_excerpt)) {
            return wp_trim_words($post->post_excerpt, 25, '...');
        }

        // Generate from content
        $content = strip_shortcodes($post->post_content);
        $content = wp_strip_all_tags($content);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);

        if (empty($content)) {
            return '';
        }

        // Limit to ~155 characters
        $description = wp_trim_words($content, 25, '...');

        if (strlen($description) > 160) {
            $description = substr($description, 0, 157) . '...';
        }

        return $description;
    }

    /**
     * Get Open Graph image
     */
    private function get_og_image($post_id) {
        // Custom OG image
        $og_image = get_post_meta($post_id, '_oneclick_seo_pro_og_image', true);
        if (!empty($og_image)) {
            return $og_image;
        }

        // Featured image
        if (has_post_thumbnail($post_id)) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large');
            if ($image) {
                return $image[0];
            }
        }

        // First image in content
        $post = get_post($post_id);
        if ($post && preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $post->post_content, $matches)) {
            return $matches[1];
        }

        // Site logo
        $org_settings = get_option('oneclick_seo_pro_organization', []);
        if (!empty($org_settings['logo'])) {
            return $org_settings['logo'];
        }

        return '';
    }
}
