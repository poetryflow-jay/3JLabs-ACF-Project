<?php
/**
 * Featured Snippet Optimizer Class
 *
 * Optimizes content for Google Featured Snippets
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Featured_Snippet_Optimizer {

    /**
     * Optimize content for featured snippets
     *
     * @param int $post_id Post ID
     * @return array Optimization suggestions
     */
    public function optimize($post_id) {
        $suggestions = [];

        $post = get_post($post_id);
        if (!$post) {
            return $suggestions;
        }

        // Check for paragraph snippets (most common)
        $paragraph_snippet = $this->check_paragraph_snippet($post);
        if ($paragraph_snippet) {
            $suggestions[] = $paragraph_snippet;
        }

        // Check for list snippets
        $list_snippet = $this->check_list_snippet($post);
        if ($list_snippet) {
            $suggestions[] = $list_snippet;
        }

        // Check for table snippets
        $table_snippet = $this->check_table_snippet($post);
        if ($table_snippet) {
            $suggestions[] = $table_snippet;
        }

        return $suggestions;
    }

    /**
     * Check paragraph snippet optimization
     *
     * @param WP_Post $post Post object
     * @return array|null Suggestion
     */
    private function check_paragraph_snippet($post) {
        $content = strip_shortcodes($post->post_content);
        $content = wp_strip_all_tags($content);

        // First paragraph should be 40-60 words and answer the question
        $first_paragraph = wp_trim_words($content, 60, '');
        $word_count = str_word_count($first_paragraph);

        if ($word_count < 40) {
            return [
                'type' => 'paragraph',
                'priority' => 'high',
                'message' => 'First paragraph is too short for featured snippet (recommended: 40-60 words)',
                'message_kr' => '첫 문단이 너무 짧습니다 (권장: 40-60 단어)',
            ];
        }

        if ($word_count > 60) {
            return [
                'type' => 'paragraph',
                'priority' => 'medium',
                'message' => 'First paragraph is too long for featured snippet (recommended: 40-60 words)',
                'message_kr' => '첫 문단이 너무 깁니다 (권장: 40-60 단어)',
            ];
        }

        return null;
    }

    /**
     * Check list snippet optimization
     *
     * @param WP_Post $post Post object
     * @return array|null Suggestion
     */
    private function check_list_snippet($post) {
        $content = $post->post_content;

        $has_lists = strpos($content, '<ul>') !== false || strpos($content, '<ol>') !== false;

        if (!$has_lists) {
            return [
                'type' => 'list',
                'priority' => 'medium',
                'message' => 'Consider adding a numbered or bulleted list for list-type featured snippets',
                'message_kr' => '목록형 피처드 스니펫을 위해 번호 목록이나 불릿 목록 추가를 고려하세요',
            ];
        }

        return null;
    }

    /**
     * Check table snippet optimization
     *
     * @param WP_Post $post Post object
     * @return array|null Suggestion
     */
    private function check_table_snippet($post) {
        $content = $post->post_content;

        $has_tables = strpos($content, '<table>') !== false;

        if (!$has_tables && $this->is_comparison_content($post)) {
            return [
                'type' => 'table',
                'priority' => 'medium',
                'message' => 'Consider adding a comparison table for table-type featured snippets',
                'message_kr' => '표형 피처드 스니펫을 위해 비교 표 추가를 고려하세요',
            ];
        }

        return null;
    }

    /**
     * Check if content is comparison-type
     *
     * @param WP_Post $post Post object
     * @return bool
     */
    private function is_comparison_content($post) {
        $comparison_keywords = ['vs', 'versus', 'compare', 'comparison', 'difference', '비교', '차이'];
        $content = strtolower($post->post_title . ' ' . $post->post_content);

        foreach ($comparison_keywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }
}
