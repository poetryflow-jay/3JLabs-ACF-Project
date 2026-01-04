<?php
/**
 * Schema Generator Class
 *
 * Generates Schema.org structured data for posts and pages
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Schema_Generator {

    /**
     * Generate schema for a post
     *
     * @param int $post_id Post ID
     * @return array Schema data
     */
    public function generate_for_post($post_id) {
        $post = get_post($post_id);
        if (!$post) {
            return [];
        }

        $schema = [];

        // Article Schema
        if (in_array($post->post_type, ['post', 'news'])) {
            $schema['@context'] = 'https://schema.org';
            $schema['@type'] = 'Article';
            $schema['headline'] = get_the_title($post_id);
            $schema['description'] = $this->get_description($post_id);
            $schema['datePublished'] = get_the_date('c', $post_id);
            $schema['dateModified'] = get_the_modified_date('c', $post_id);

            // Author
            $author_id = $post->post_author;
            if ($author_id) {
                $schema['author'] = [
                    '@type' => 'Person',
                    'name' => get_the_author_meta('display_name', $author_id),
                    'url' => get_author_posts_url($author_id),
                ];
            }

            // Publisher
            $schema['publisher'] = [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url(),
            ];

            // Featured Image
            $thumbnail_id = get_post_thumbnail_id($post_id);
            if ($thumbnail_id) {
                $image_url = wp_get_attachment_image_url($thumbnail_id, 'full');
                if ($image_url) {
                    $schema['image'] = [
                        '@type' => 'ImageObject',
                        'url' => $image_url,
                    ];
                }
            }
        }

        // WebPage Schema (for pages)
        if ($post->post_type === 'page') {
            $schema['@context'] = 'https://schema.org';
            $schema['@type'] = 'WebPage';
            $schema['name'] = get_the_title($post_id);
            $schema['description'] = $this->get_description($post_id);
            $schema['url'] = get_permalink($post_id);
            $schema['datePublished'] = get_the_date('c', $post_id);
            $schema['dateModified'] = get_the_modified_date('c', $post_id);
        }

        return $schema;
    }

    /**
     * Output schema JSON-LD
     *
     * @param int $post_id Post ID
     */
    public function output_schema($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        if (!$post_id) {
            return;
        }

        $schema = $this->generate_for_post($post_id);

        if (empty($schema)) {
            return;
        }

        // Add BreadcrumbList if available
        $breadcrumb = $this->generate_breadcrumb($post_id);
        if (!empty($breadcrumb)) {
            $schemas = [$schema, $breadcrumb];
        } else {
            $schemas = [$schema];
        }

        foreach ($schemas as $schema_item) {
            echo '<script type="application/ld+json">' . "\n";
            echo wp_json_encode($schema_item, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
            echo '</script>' . "\n";
        }
    }

    /**
     * Generate breadcrumb schema
     *
     * @param int $post_id Post ID
     * @return array Breadcrumb schema
     */
    private function generate_breadcrumb($post_id) {
        $breadcrumb = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        // Home
        $breadcrumb['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => get_bloginfo('name'),
            'item' => home_url(),
        ];

        // Post type archive (if applicable)
        $post = get_post($post_id);
        $post_type_obj = get_post_type_object($post->post_type);
        if ($post_type_obj && $post_type_obj->has_archive) {
            $archive_url = get_post_type_archive_link($post->post_type);
            if ($archive_url) {
                $breadcrumb['itemListElement'][] = [
                    '@type' => 'ListItem',
                    'position' => count($breadcrumb['itemListElement']) + 1,
                    'name' => $post_type_obj->label,
                    'item' => $archive_url,
                ];
            }
        }

        // Categories/Terms
        $terms = get_the_terms($post_id, 'category');
        if ($terms && !is_wp_error($terms)) {
            $term = reset($terms);
            $breadcrumb['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => count($breadcrumb['itemListElement']) + 1,
                'name' => $term->name,
                'item' => get_term_link($term),
            ];
        }

        // Current page
        $breadcrumb['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => count($breadcrumb['itemListElement']) + 1,
            'name' => get_the_title($post_id),
            'item' => get_permalink($post_id),
        ];

        return $breadcrumb;
    }

    /**
     * Get description for schema
     *
     * @param int $post_id Post ID
     * @return string Description
     */
    private function get_description($post_id) {
        // Try meta description from SEO plugins
        $meta_desc = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
        if ($meta_desc) return $meta_desc;

        $meta_desc = get_post_meta($post_id, 'rank_math_description', true);
        if ($meta_desc) return $meta_desc;

        $meta_desc = get_post_meta($post_id, '_aioseo_description', true);
        if ($meta_desc) return $meta_desc;

        // Fallback to excerpt
        $excerpt = get_the_excerpt($post_id);
        if ($excerpt) return $excerpt;

        // Fallback to content excerpt
        $post = get_post($post_id);
        return wp_trim_words(strip_shortcodes($post->post_content), 30);
    }
}
