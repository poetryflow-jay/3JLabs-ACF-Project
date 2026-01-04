<?php
/**
 * Benchmark Engine Class
 *
 * Analyzes top-ranking pages and matches patterns for bulk optimization
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Benchmark_Engine {

    /**
     * Analyze top-ranking pages
     *
     * @param string $keyword Target keyword
     * @param int $top_n Number of top pages to analyze
     * @return array Benchmark data
     */
    public function analyze_top_rankings($keyword, $top_n = 10) {
        $rankings = $this->get_top_rankings($keyword, $top_n);
        
        if (empty($rankings)) {
            return [
                'success' => false,
                'message' => 'No ranking data available',
            ];
        }

        $patterns = [];
        $analyzer = new ThreeJ_SEO_Analyzer();

        foreach ($rankings as $ranking) {
            $url = $ranking['page_url'] ?? '';
            if (empty($url)) continue;

            // Analyze the page
            $page_data = $analyzer->analyze_url($url);
            
            if (isset($page_data['error'])) {
                continue;
            }

            // Extract patterns
            $patterns[] = $this->extract_patterns($page_data);
        }

        // Aggregate patterns
        $aggregated = $this->aggregate_patterns($patterns);

        return [
            'success' => true,
            'keyword' => $keyword,
            'analyzed_pages' => count($patterns),
            'patterns' => $aggregated,
            'recommendations' => $this->generate_recommendations($aggregated),
        ];
    }

    /**
     * Get top rankings for keyword
     *
     * @param string $keyword Keyword
     * @param int $top_n Number of results
     * @return array Rankings
     */
    private function get_top_rankings($keyword, $top_n = 10) {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_rankings';

        // Get recent rankings for keyword
        $rankings = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
            WHERE keyword = %s 
            AND position <= %d 
            ORDER BY position ASC, tracked_at DESC 
            LIMIT %d",
            $keyword,
            $top_n,
            $top_n
        ), ARRAY_A);

        return $rankings;
    }

    /**
     * Extract patterns from page data
     *
     * @param array $page_data Page analysis data
     * @return array Patterns
     */
    private function extract_patterns($page_data) {
        return [
            'title' => [
                'length' => mb_strlen($page_data['title'] ?? ''),
                'has_keyword' => !empty($page_data['focus_keyword']),
                'keyword_position' => $this->get_keyword_position($page_data['title'] ?? '', $page_data['focus_keyword'] ?? ''),
            ],
            'meta_description' => [
                'length' => mb_strlen($page_data['meta_description'] ?? ''),
                'has_keyword' => !empty($page_data['focus_keyword']),
            ],
            'headings' => [
                'h1_count' => $page_data['h1_count'] ?? 0,
                'h2_count' => $page_data['h2_count'] ?? 0,
                'h3_count' => $page_data['h3_count'] ?? 0,
            ],
            'content' => [
                'word_count' => $page_data['word_count'] ?? 0,
                'has_lists' => $page_data['has_lists'] ?? false,
                'has_tables' => $page_data['has_tables'] ?? false,
                'image_count' => $page_data['image_count'] ?? 0,
            ],
            'links' => [
                'internal_count' => $page_data['internal_link_count'] ?? 0,
                'external_count' => $page_data['external_link_count'] ?? 0,
            ],
            'images' => [
                'total' => $page_data['image_count'] ?? 0,
                'with_alt' => $page_data['images_alt_ratio'] ?? 0,
            ],
            'schema' => [
                'types' => $page_data['schema_types'] ?? [],
                'has_article' => $page_data['has_article_schema'] ?? false,
                'has_faq' => $page_data['has_faq_schema'] ?? false,
            ],
            'technical' => [
                'is_https' => $page_data['is_https'] ?? false,
                'mobile_score' => $page_data['mobile_score'] ?? 100,
            ],
        ];
    }

    /**
     * Get keyword position in text
     *
     * @param string $text Text to search
     * @param string $keyword Keyword
     * @return int Position (0 = start, -1 = not found)
     */
    private function get_keyword_position($text, $keyword) {
        if (empty($keyword)) return -1;
        
        $pos = stripos($text, $keyword);
        return $pos !== false ? $pos : -1;
    }

    /**
     * Aggregate patterns from multiple pages
     *
     * @param array $patterns Array of pattern arrays
     * @return array Aggregated patterns
     */
    private function aggregate_patterns($patterns) {
        if (empty($patterns)) {
            return [];
        }

        $aggregated = [
            'title' => [
                'avg_length' => 0,
                'keyword_in_title_ratio' => 0,
                'avg_keyword_position' => 0,
            ],
            'meta_description' => [
                'avg_length' => 0,
                'keyword_in_desc_ratio' => 0,
            ],
            'headings' => [
                'avg_h1' => 0,
                'avg_h2' => 0,
                'avg_h3' => 0,
            ],
            'content' => [
                'avg_word_count' => 0,
                'has_lists_ratio' => 0,
                'has_tables_ratio' => 0,
                'avg_images' => 0,
            ],
            'links' => [
                'avg_internal' => 0,
                'avg_external' => 0,
            ],
            'images' => [
                'avg_total' => 0,
                'avg_alt_ratio' => 0,
            ],
            'schema' => [
                'common_types' => [],
                'article_ratio' => 0,
                'faq_ratio' => 0,
            ],
            'technical' => [
                'https_ratio' => 0,
                'avg_mobile_score' => 0,
            ],
        ];

        $count = count($patterns);
        $title_lengths = [];
        $desc_lengths = [];
        $keyword_in_title = 0;
        $keyword_in_desc = 0;
        $keyword_positions = [];
        $h1_counts = [];
        $h2_counts = [];
        $h3_counts = [];
        $word_counts = [];
        $has_lists = 0;
        $has_tables = 0;
        $image_counts = [];
        $internal_links = [];
        $external_links = [];
        $image_totals = [];
        $alt_ratios = [];
        $has_article = 0;
        $has_faq = 0;
        $is_https = 0;
        $mobile_scores = [];
        $schema_types_all = [];

        foreach ($patterns as $pattern) {
            // Title
            if (isset($pattern['title']['length'])) {
                $title_lengths[] = $pattern['title']['length'];
            }
            if ($pattern['title']['has_keyword'] ?? false) {
                $keyword_in_title++;
            }
            if (($pattern['title']['keyword_position'] ?? -1) >= 0) {
                $keyword_positions[] = $pattern['title']['keyword_position'];
            }

            // Meta description
            if (isset($pattern['meta_description']['length'])) {
                $desc_lengths[] = $pattern['meta_description']['length'];
            }
            if ($pattern['meta_description']['has_keyword'] ?? false) {
                $keyword_in_desc++;
            }

            // Headings
            $h1_counts[] = $pattern['headings']['h1_count'] ?? 0;
            $h2_counts[] = $pattern['headings']['h2_count'] ?? 0;
            $h3_counts[] = $pattern['headings']['h3_count'] ?? 0;

            // Content
            $word_counts[] = $pattern['content']['word_count'] ?? 0;
            if ($pattern['content']['has_lists'] ?? false) {
                $has_lists++;
            }
            if ($pattern['content']['has_tables'] ?? false) {
                $has_tables++;
            }
            $image_counts[] = $pattern['content']['image_count'] ?? 0;

            // Links
            $internal_links[] = $pattern['links']['internal_count'] ?? 0;
            $external_links[] = $pattern['links']['external_count'] ?? 0;

            // Images
            $image_totals[] = $pattern['images']['total'] ?? 0;
            $alt_ratios[] = $pattern['images']['with_alt'] ?? 0;

            // Schema
            if ($pattern['schema']['has_article'] ?? false) {
                $has_article++;
            }
            if ($pattern['schema']['has_faq'] ?? false) {
                $has_faq++;
            }
            if (!empty($pattern['schema']['types'])) {
                $schema_types_all = array_merge($schema_types_all, $pattern['schema']['types']);
            }

            // Technical
            if ($pattern['technical']['is_https'] ?? false) {
                $is_https++;
            }
            $mobile_scores[] = $pattern['technical']['mobile_score'] ?? 100;
        }

        // Calculate averages
        $aggregated['title']['avg_length'] = $this->average($title_lengths);
        $aggregated['title']['keyword_in_title_ratio'] = ($keyword_in_title / $count) * 100;
        $aggregated['title']['avg_keyword_position'] = $this->average($keyword_positions);

        $aggregated['meta_description']['avg_length'] = $this->average($desc_lengths);
        $aggregated['meta_description']['keyword_in_desc_ratio'] = ($keyword_in_desc / $count) * 100;

        $aggregated['headings']['avg_h1'] = $this->average($h1_counts);
        $aggregated['headings']['avg_h2'] = $this->average($h2_counts);
        $aggregated['headings']['avg_h3'] = $this->average($h3_counts);

        $aggregated['content']['avg_word_count'] = $this->average($word_counts);
        $aggregated['content']['has_lists_ratio'] = ($has_lists / $count) * 100;
        $aggregated['content']['has_tables_ratio'] = ($has_tables / $count) * 100;
        $aggregated['content']['avg_images'] = $this->average($image_counts);

        $aggregated['links']['avg_internal'] = $this->average($internal_links);
        $aggregated['links']['avg_external'] = $this->average($external_links);

        $aggregated['images']['avg_total'] = $this->average($image_totals);
        $aggregated['images']['avg_alt_ratio'] = $this->average($alt_ratios);

        $aggregated['schema']['common_types'] = $this->get_common_items($schema_types_all);
        $aggregated['schema']['article_ratio'] = ($has_article / $count) * 100;
        $aggregated['schema']['faq_ratio'] = ($has_faq / $count) * 100;

        $aggregated['technical']['https_ratio'] = ($is_https / $count) * 100;
        $aggregated['technical']['avg_mobile_score'] = $this->average($mobile_scores);

        return $aggregated;
    }

    /**
     * Calculate average
     *
     * @param array $values Values
     * @return float Average
     */
    private function average($values) {
        if (empty($values)) return 0;
        return round(array_sum($values) / count($values), 2);
    }

    /**
     * Get common items from array
     *
     * @param array $items Items
     * @return array Common items
     */
    private function get_common_items($items) {
        $counts = array_count_values($items);
        arsort($counts);
        return array_slice(array_keys($counts), 0, 5);
    }

    /**
     * Generate recommendations based on patterns
     *
     * @param array $patterns Aggregated patterns
     * @return array Recommendations
     */
    private function generate_recommendations($patterns) {
        $recommendations = [];

        // Title recommendations
        if ($patterns['title']['avg_length'] > 0) {
            $recommendations[] = [
                'type' => 'title',
                'priority' => 'high',
                'message' => sprintf('최적 제목 길이: 약 %d자 (현재 상위 페이지 평균)', round($patterns['title']['avg_length'])),
                'message_kr' => sprintf('최적 제목 길이: 약 %d자 (현재 상위 페이지 평균)', round($patterns['title']['avg_length'])),
            ];
        }

        if ($patterns['title']['keyword_in_title_ratio'] > 80) {
            $recommendations[] = [
                'type' => 'title_keyword',
                'priority' => 'high',
                'message' => sprintf('%d%%의 상위 페이지가 제목에 키워드를 포함합니다.', round($patterns['title']['keyword_in_title_ratio'])),
                'message_kr' => sprintf('%d%%의 상위 페이지가 제목에 키워드를 포함합니다.', round($patterns['title']['keyword_in_title_ratio'])),
            ];
        }

        // Content recommendations
        if ($patterns['content']['avg_word_count'] > 0) {
            $recommendations[] = [
                'type' => 'content_length',
                'priority' => 'medium',
                'message' => sprintf('권장 콘텐츠 길이: 약 %d 단어 (상위 페이지 평균)', round($patterns['content']['avg_word_count'])),
                'message_kr' => sprintf('권장 콘텐츠 길이: 약 %d 단어 (상위 페이지 평균)', round($patterns['content']['avg_word_count'])),
            ];
        }

        // Schema recommendations
        if ($patterns['schema']['article_ratio'] > 70) {
            $recommendations[] = [
                'type' => 'schema_article',
                'priority' => 'high',
                'message' => sprintf('%d%%의 상위 페이지가 Article Schema를 사용합니다.', round($patterns['schema']['article_ratio'])),
                'message_kr' => sprintf('%d%%의 상위 페이지가 Article Schema를 사용합니다.', round($patterns['schema']['article_ratio'])),
            ];
        }

        return $recommendations;
    }

    /**
     * Apply benchmark patterns to post
     *
     * @param int $post_id Post ID
     * @param array $patterns Benchmark patterns
     * @param bool $auto_apply Auto apply or suggest only
     * @return array Results
     */
    public function apply_patterns($post_id, $patterns, $auto_apply = false) {
        $results = [
            'success' => true,
            'changes' => [],
            'suggestions' => [],
        ];

        $post = get_post($post_id);
        if (!$post) {
            return ['success' => false, 'message' => 'Post not found'];
        }

        // Title optimization
        $title_result = $this->optimize_title($post_id, $patterns, $auto_apply);
        if ($title_result) {
            $results['changes'][] = $title_result;
        }

        // Meta description optimization
        $meta_result = $this->optimize_meta_description($post_id, $patterns, $auto_apply);
        if ($meta_result) {
            $results['changes'][] = $meta_result;
        }

        // Content structure suggestions
        $content_suggestions = $this->suggest_content_structure($post_id, $patterns);
        $results['suggestions'] = array_merge($results['suggestions'], $content_suggestions);

        return $results;
    }

    /**
     * Optimize title based on patterns
     *
     * @param int $post_id Post ID
     * @param array $patterns Patterns
     * @param bool $auto_apply Auto apply
     * @return array|null Result
     */
    private function optimize_title($post_id, $patterns, $auto_apply) {
        $current_title = get_the_title($post_id);
        $current_length = mb_strlen($current_title);
        $optimal_length = round($patterns['title']['avg_length'] ?? 60);

        if (abs($current_length - $optimal_length) > 10) {
            if ($auto_apply) {
                // Auto-optimize title length (would need AI or manual rules)
                return [
                    'type' => 'title',
                    'action' => 'suggested',
                    'message' => sprintf('제목 길이 최적화 제안: %d자 → 약 %d자', $current_length, $optimal_length),
                ];
            } else {
                return [
                    'type' => 'title',
                    'action' => 'suggestion',
                    'message' => sprintf('제목 길이 최적화 제안: %d자 → 약 %d자', $current_length, $optimal_length),
                ];
            }
        }

        return null;
    }

    /**
     * Optimize meta description based on patterns
     *
     * @param int $post_id Post ID
     * @param array $patterns Patterns
     * @param bool $auto_apply Auto apply
     * @return array|null Result
     */
    private function optimize_meta_description($post_id, $patterns, $auto_apply) {
        $current_desc = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
        if (empty($current_desc)) {
            $current_desc = get_the_excerpt($post_id);
        }

        $current_length = mb_strlen($current_desc);
        $optimal_length = round($patterns['meta_description']['avg_length'] ?? 155);

        if (abs($current_length - $optimal_length) > 20) {
            return [
                'type' => 'meta_description',
                'action' => 'suggestion',
                'message' => sprintf('메타 설명 길이 최적화 제안: %d자 → 약 %d자', $current_length, $optimal_length),
            ];
        }

        return null;
    }

    /**
     * Suggest content structure
     *
     * @param int $post_id Post ID
     * @param array $patterns Patterns
     * @return array Suggestions
     */
    private function suggest_content_structure($post_id, $patterns) {
        $suggestions = [];
        $post = get_post($post_id);
        $content = $post->post_content;

        // Word count suggestion
        $word_count = str_word_count(strip_tags($content));
        $optimal_words = round($patterns['content']['avg_word_count'] ?? 1000);

        if ($word_count < $optimal_words * 0.8) {
            $suggestions[] = [
                'type' => 'content_length',
                'priority' => 'medium',
                'message' => sprintf('콘텐츠 길이 증가 제안: 현재 %d 단어, 권장 %d 단어', $word_count, $optimal_words),
            ];
        }

        // Lists suggestion
        if (($patterns['content']['has_lists_ratio'] ?? 0) > 70) {
            $has_lists = strpos($content, '<ul>') !== false || strpos($content, '<ol>') !== false;
            if (!$has_lists) {
                $suggestions[] = [
                    'type' => 'content_structure',
                    'priority' => 'medium',
                    'message' => '상위 페이지의 70% 이상이 목록을 사용합니다. 목록 추가를 고려하세요.',
                ];
            }
        }

        return $suggestions;
    }
}
