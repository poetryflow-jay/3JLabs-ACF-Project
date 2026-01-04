<?php
/**
 * FAQ Generator Class
 *
 * Generates FAQ schema and content for AEO
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_FAQ_Generator {

    /**
     * Generate FAQ schema from post content
     *
     * @param int $post_id Post ID
     * @return array FAQ schema
     */
    public function generate_faq_schema($post_id) {
        $post = get_post($post_id);
        if (!$post) {
            return [];
        }

        $faq_schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [],
        ];

        // Extract Q&A pairs from content
        $qa_pairs = $this->extract_qa_pairs($post->post_content);

        foreach ($qa_pairs as $qa) {
            $faq_schema['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $qa['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $qa['answer'],
                ],
            ];
        }

        return $faq_schema;
    }

    /**
     * Extract Q&A pairs from content
     *
     * @param string $content Post content
     * @return array Q&A pairs
     */
    private function extract_qa_pairs($content) {
        $qa_pairs = [];

        // Look for FAQ shortcodes or structured content
        // Pattern: Q: question A: answer
        preg_match_all('/Q:\s*(.+?)\s*A:\s*(.+?)(?=Q:|$)/is', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (isset($match[1]) && isset($match[2])) {
                $qa_pairs[] = [
                    'question' => trim(strip_tags($match[1])),
                    'answer' => trim(strip_tags($match[2])),
                ];
            }
        }

        // Look for definition lists
        preg_match_all('/<dt>(.+?)<\/dt>\s*<dd>(.+?)<\/dd>/is', $content, $dl_matches, PREG_SET_ORDER);
        foreach ($dl_matches as $match) {
            if (isset($match[1]) && isset($match[2])) {
                $qa_pairs[] = [
                    'question' => trim(strip_tags($match[1])),
                    'answer' => trim(strip_tags($match[2])),
                ];
            }
        }

        return $qa_pairs;
    }

    /**
     * Output FAQ schema JSON-LD
     *
     * @param int $post_id Post ID
     */
    public function output_faq_schema($post_id) {
        $faq_schema = $this->generate_faq_schema($post_id);

        if (empty($faq_schema['mainEntity'])) {
            return;
        }

        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode($faq_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
        echo '</script>' . "\n";
    }
}
