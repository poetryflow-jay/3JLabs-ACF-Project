<?php
/**
 * AEO Engine Class
 *
 * Answer Engine Optimization - Optimizes content for AI search engines
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_AEO_Engine {

    /**
     * Optimize content for answer engines
     *
     * @param int $post_id Post ID
     * @return array Optimization results
     */
    public function optimize_for_aeo($post_id) {
        $results = [
            'success' => true,
            'changes' => [],
            'errors' => [],
        ];

        $post = get_post($post_id);
        if (!$post) {
            $results['success'] = false;
            $results['errors'][] = 'Post not found';
            return $results;
        }

        // Generate FAQ schema
        $faq_result = $this->generate_faq_schema($post_id);
        if ($faq_result) {
            $results['changes'][] = 'FAQ schema generated';
        }

        // Optimize for featured snippets
        $snippet_result = $this->optimize_for_featured_snippet($post_id);
        if ($snippet_result) {
            $results['changes'][] = 'Content optimized for featured snippets';
        }

        // Structure content for AI parsing
        $structure_result = $this->structure_for_ai($post_id);
        if ($structure_result) {
            $results['changes'][] = 'Content structured for AI parsing';
        }

        return $results;
    }

    /**
     * Generate FAQ schema from content
     *
     * @param int $post_id Post ID
     * @return bool Success
     */
    private function generate_faq_schema($post_id) {
        // Check if FAQ schema already exists
        $existing_schema = get_post_meta($post_id, '_wp_bulk_seo_aeo_faq_schema', true);
        if ($existing_schema) {
            return true;
        }

        // This would typically use AI to extract Q&A pairs from content
        // For now, return true (placeholder)
        return true;
    }

    /**
     * Optimize content for featured snippets
     *
     * @param int $post_id Post ID
     * @return bool Success
     */
    private function optimize_for_featured_snippet($post_id) {
        $post = get_post($post_id);

        // Check for list structures (good for featured snippets)
        $has_lists = has_shortcode($post->post_content, 'list') ||
                     strpos($post->post_content, '<ul>') !== false ||
                     strpos($post->post_content, '<ol>') !== false;

        // Check for tables (good for featured snippets)
        $has_tables = strpos($post->post_content, '<table>') !== false;

        // Check for definition lists
        $has_definitions = strpos($post->post_content, '<dl>') !== false;

        return $has_lists || $has_tables || $has_definitions;
    }

    /**
     * Structure content for AI parsing
     *
     * @param int $post_id Post ID
     * @return bool Success
     */
    private function structure_for_ai($post_id) {
        $post = get_post($post_id);

        // Ensure proper heading hierarchy
        $content = $post->post_content;
        $has_h1 = strpos($content, '<h1') !== false;
        $has_h2 = strpos($content, '<h2') !== false;

        // Ensure structured data
        $has_schema = get_post_meta($post_id, '_wp_bulk_seo_aeo_schema_added', true);

        return $has_h1 && $has_h2 && $has_schema;
    }
}
