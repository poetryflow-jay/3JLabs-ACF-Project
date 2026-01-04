<?php
/**
 * Content Optimizer Class
 *
 * Optimizes content for SEO based on analysis results
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Content_Optimizer {

    /**
     * Optimize post content
     *
     * @param int $post_id Post ID
     * @param array $optimizations Optimization options
     * @return array Results
     */
    public function optimize_post($post_id, $optimizations = []) {
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

        // Meta tags optimization
        if (in_array('meta_tags', $optimizations) || empty($optimizations)) {
            $meta_result = $this->optimize_meta_tags($post_id);
            if ($meta_result['success']) {
                $results['changes'] = array_merge($results['changes'], $meta_result['changes']);
            } else {
                $results['errors'] = array_merge($results['errors'], $meta_result['errors']);
            }
        }

        // Image optimization
        if (in_array('images', $optimizations)) {
            $image_result = $this->optimize_images($post_id);
            if ($image_result['success']) {
                $results['changes'] = array_merge($results['changes'], $image_result['changes']);
            } else {
                $results['errors'] = array_merge($results['errors'], $image_result['errors']);
            }
        }

        // Internal linking
        if (in_array('internal_links', $optimizations)) {
            $link_result = $this->optimize_internal_links($post_id);
            if ($link_result['success']) {
                $results['changes'] = array_merge($results['changes'], $link_result['changes']);
            } else {
                $results['errors'] = array_merge($results['errors'], $link_result['errors']);
            }
        }

        return $results;
    }

    /**
     * Optimize meta tags
     *
     * @param int $post_id Post ID
     * @return array Results
     */
    private function optimize_meta_tags($post_id) {
        $results = [
            'success' => true,
            'changes' => [],
            'errors' => [],
        ];

        $post = get_post($post_id);
        $title = get_the_title($post_id);

        // Generate meta description if missing
        $meta_desc = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
        if (empty($meta_desc)) {
            $meta_desc = $this->generate_meta_description($post);
            if ($meta_desc) {
                update_post_meta($post_id, '_yoast_wpseo_metadesc', $meta_desc);
                $results['changes'][] = 'Meta description generated';
            }
        }

        // Optimize title length
        if (mb_strlen($title) > 60) {
            $results['changes'][] = 'Title is too long (' . mb_strlen($title) . ' characters)';
        }

        return $results;
    }

    /**
     * Generate meta description from content
     *
     * @param WP_Post $post Post object
     * @return string Meta description
     */
    private function generate_meta_description($post) {
        $content = strip_shortcodes($post->post_content);
        $content = wp_strip_all_tags($content);
        $excerpt = wp_trim_words($content, 25);

        // Ensure length is between 120-160 characters
        if (mb_strlen($excerpt) < 120) {
            $excerpt = wp_trim_words($content, 30);
        }
        if (mb_strlen($excerpt) > 160) {
            $excerpt = mb_substr($excerpt, 0, 157) . '...';
        }

        return $excerpt;
    }

    /**
     * Optimize images
     *
     * @param int $post_id Post ID
     * @return array Results
     */
    private function optimize_images($post_id) {
        $results = [
            'success' => true,
            'changes' => [],
            'errors' => [],
        ];

        $post = get_post($post_id);
        $content = $post->post_content;

        // Find images without alt text
        preg_match_all('/<img[^>]+>/i', $content, $matches);
        $images_without_alt = 0;

        foreach ($matches[0] as $img_tag) {
            if (stripos($img_tag, 'alt=') === false) {
                $images_without_alt++;
            }
        }

        if ($images_without_alt > 0) {
            $results['changes'][] = sprintf('%d images missing alt text', $images_without_alt);
        }

        return $results;
    }

    /**
     * Optimize internal links
     *
     * @param int $post_id Post ID
     * @return array Results
     */
    private function optimize_internal_links($post_id) {
        $results = [
            'success' => true,
            'changes' => [],
            'errors' => [],
        ];

        $post = get_post($post_id);
        $content = $post->post_content;

        // Count internal links
        preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);
        $internal_links = 0;
        $home_url = home_url();

        foreach ($matches[1] as $url) {
            if (strpos($url, $home_url) === 0 || strpos($url, '/') === 0) {
                $internal_links++;
            }
        }

        if ($internal_links < 3) {
            $results['changes'][] = sprintf('Only %d internal links found (recommended: 3+)', $internal_links);
        }

        return $results;
    }
}
