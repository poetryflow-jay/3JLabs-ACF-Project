<?php
/**
 * WP Bulk SEO - Keyword Extractor & Recommender
 *
 * 키워드 추출 및 추천 시스템
 * - 페이지 텍스트에서 키워드 자동 추출
 * - 데이터베이스 알고리즘 기반 키워드 추천
 * - 목표 키워드 입력 및 분석
 * - 관련 키워드 제안
 * - 경쟁 키워드 분석
 *
 * @package WP_Bulk_SEO
 * @version 2.1.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Keyword_Extractor {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Ranking factors database
     */
    private $factors_db;

    /**
     * Stop words (불용어)
     */
    private $stop_words = [];

    /**
     * Get singleton instance
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->factors_db = new WP_Bulk_SEO_Ranking_Factors_DB();
        $this->load_stop_words();
    }

    /**
     * Load stop words
     */
    private function load_stop_words() {
        // 한국어 및 영어 불용어
        $this->stop_words = [
            // 한국어 불용어
            '이', '가', '을', '를', '에', '의', '와', '과', '도', '로', '으로',
            '은', '는', '에서', '에게', '께', '한테', '더', '또', '그리고', '또한',
            '그러나', '하지만', '그런데', '그래서', '따라서', '그러므로', '그러면',
            '이것', '그것', '저것', '이런', '그런', '저런', '이렇게', '그렇게',
            '있다', '없다', '되다', '하다', '이다', '아니다', '같다', '다르다',
            '좋다', '나쁘다', '크다', '작다', '많다', '적다', '높다', '낮다',
            '하나', '둘', '셋', '첫', '마지막', '새', '옛', '온', '전', '후',
            // 영어 불용어
            'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for',
            'of', 'with', 'by', 'from', 'as', 'is', 'was', 'are', 'were', 'be',
            'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will',
            'would', 'should', 'could', 'may', 'might', 'must', 'can', 'this',
            'that', 'these', 'those', 'i', 'you', 'he', 'she', 'it', 'we', 'they',
            'what', 'which', 'who', 'whom', 'whose', 'where', 'when', 'why', 'how',
        ];
    }

    /**
     * Extract keywords from content
     *
     * @param string $content Content text
     * @param array $options Extraction options
     * @return array Extracted keywords with scores
     */
    public function extract_keywords($content, $options = []) {
        $defaults = [
            'min_word_length' => 2,
            'max_keywords' => 20,
            'min_frequency' => 2,
            'exclude_stop_words' => true,
            'language' => 'auto', // 'ko', 'en', 'auto'
        ];

        $options = wp_parse_args($options, $defaults);

        // Clean content
        $content = $this->clean_text($content);

        // Detect language if auto
        if ($options['language'] === 'auto') {
            $options['language'] = $this->detect_language($content);
        }

        // Extract words
        $words = $this->tokenize($content, $options['language']);

        // Filter stop words
        if ($options['exclude_stop_words']) {
            $words = $this->filter_stop_words($words);
        }

        // Filter by length
        $words = array_filter($words, function($word) use ($options) {
            return mb_strlen($word) >= $options['min_word_length'];
        });

        // Count frequency
        $frequency = array_count_values($words);

        // Filter by minimum frequency
        $frequency = array_filter($frequency, function($count) use ($options) {
            return $count >= $options['min_frequency'];
        });

        // Calculate TF-IDF scores (simplified)
        $keywords = [];
        $total_words = count($words);
        $unique_words = count($frequency);

        foreach ($frequency as $word => $count) {
            // TF (Term Frequency)
            $tf = $count / $total_words;

            // Simplified IDF (Inverse Document Frequency)
            $idf = log($total_words / $count);

            // TF-IDF Score
            $tfidf = $tf * $idf;

            // Additional scoring factors
            $score = $this->calculate_keyword_score($word, $content, $tfidf);

            $keywords[$word] = [
                'keyword' => $word,
                'frequency' => $count,
                'tf' => round($tf, 4),
                'idf' => round($idf, 4),
                'tfidf' => round($tfidf, 4),
                'score' => round($score, 2),
                'positions' => $this->find_keyword_positions($word, $content),
            ];
        }

        // Sort by score
        uasort($keywords, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Limit results
        $keywords = array_slice($keywords, 0, $options['max_keywords']);

        return array_values($keywords);
    }

    /**
     * Recommend keywords based on target keyword
     *
     * @param string $target_keyword Target keyword
     * @param array $options Recommendation options
     * @return array Recommended keywords
     */
    public function recommend_keywords($target_keyword, $options = []) {
        $defaults = [
            'max_recommendations' => 10,
            'include_synonyms' => true,
            'include_related' => true,
            'include_long_tail' => true,
            'use_ranking_factors' => true,
        ];

        $options = wp_parse_args($options, $defaults);

        $recommendations = [];

        // 1. Extract from ranking factors database
        if ($options['use_ranking_factors']) {
            $factor_keywords = $this->get_keywords_from_factors($target_keyword);
            $recommendations = array_merge($recommendations, $factor_keywords);
        }

        // 2. Generate synonyms
        if ($options['include_synonyms']) {
            $synonyms = $this->generate_synonyms($target_keyword);
            $recommendations = array_merge($recommendations, $synonyms);
        }

        // 3. Generate related keywords
        if ($options['include_related']) {
            $related = $this->generate_related_keywords($target_keyword);
            $recommendations = array_merge($recommendations, $related);
        }

        // 4. Generate long-tail keywords
        if ($options['include_long_tail']) {
            $long_tail = $this->generate_long_tail_keywords($target_keyword);
            $recommendations = array_merge($recommendations, $long_tail);
        }

        // Score and rank recommendations
        $scored = $this->score_recommendations($recommendations, $target_keyword);

        // Remove duplicates and sort
        $scored = $this->deduplicate_keywords($scored);
        usort($scored, function($a, $b) {
            return $b['relevance_score'] <=> $a['relevance_score'];
        });

        // Limit results
        return array_slice($scored, 0, $options['max_recommendations']);
    }

    /**
     * Analyze target keyword
     *
     * @param string $keyword Target keyword
     * @param int|null $post_id Optional post ID for context
     * @return array Keyword analysis
     */
    public function analyze_keyword($keyword, $post_id = null) {
        $analysis = [
            'keyword' => $keyword,
            'length' => mb_strlen($keyword),
            'word_count' => str_word_count($keyword),
            'language' => $this->detect_language($keyword),
            'difficulty' => $this->estimate_difficulty($keyword),
            'search_volume' => null, // Would require external API
            'competition' => null, // Would require external API
            'cpc' => null, // Would require external API
        ];

        // Analyze in context of post if provided
        if ($post_id) {
            $post = get_post($post_id);
            if ($post) {
                $content = $post->post_content . ' ' . $post->post_title;
                $analysis['in_content'] = $this->analyze_keyword_in_content($keyword, $content);
            }
        }

        // Get ranking factors relevance
        $analysis['ranking_factors'] = $this->get_keyword_ranking_factors($keyword);

        // Generate recommendations
        $analysis['recommendations'] = $this->recommend_keywords($keyword, [
            'max_recommendations' => 5,
        ]);

        return $analysis;
    }

    /**
     * Extract keywords from post
     *
     * @param int $post_id Post ID
     * @param array $options Extraction options
     * @return array Extracted keywords
     */
    public function extract_from_post($post_id, $options = []) {
        $post = get_post($post_id);
        if (!$post) {
            return [];
        }

        // Combine title and content
        $content = $post->post_title . ' ' . $post->post_content;

        // Extract keywords
        $keywords = $this->extract_keywords($content, $options);

        // Enhance with post metadata
        foreach ($keywords as &$keyword_data) {
            $keyword_data['in_title'] = stripos($post->post_title, $keyword_data['keyword']) !== false;
            $keyword_data['in_excerpt'] = stripos($post->post_excerpt, $keyword_data['keyword']) !== false;
            $keyword_data['in_meta'] = $this->check_keyword_in_meta($post_id, $keyword_data['keyword']);
        }

        return $keywords;
    }

    /**
     * Get keyword suggestions based on content analysis
     *
     * @param int $post_id Post ID
     * @param array $options Options
     * @return array Suggestions
     */
    public function get_content_based_suggestions($post_id, $options = []) {
        $defaults = [
            'extract_count' => 10,
            'recommend_count' => 5,
        ];

        $options = wp_parse_args($options, $defaults);

        // Extract keywords from content
        $extracted = $this->extract_from_post($post_id, [
            'max_keywords' => $options['extract_count'],
        ]);

        if (empty($extracted)) {
            return [];
        }

        // Get top keyword
        $top_keyword = $extracted[0]['keyword'];

        // Recommend based on top keyword
        $recommendations = $this->recommend_keywords($top_keyword, [
            'max_recommendations' => $options['recommend_count'],
        ]);

        return [
            'extracted_keywords' => $extracted,
            'primary_keyword' => $top_keyword,
            'recommendations' => $recommendations,
        ];
    }

    // ========================================
    // Helper Methods
    // ========================================

    /**
     * Clean text for extraction
     */
    private function clean_text($text) {
        // Remove HTML tags
        $text = wp_strip_all_tags($text);

        // Remove special characters but keep spaces
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);

        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * Detect language
     */
    private function detect_language($text) {
        // Simple detection based on character ranges
        $korean_pattern = '/[\x{AC00}-\x{D7A3}]/u';
        $english_pattern = '/[a-zA-Z]/';

        $has_korean = preg_match($korean_pattern, $text);
        $has_english = preg_match($english_pattern, $text);

        if ($has_korean) {
            return 'ko';
        } elseif ($has_english) {
            return 'en';
        }

        return 'unknown';
    }

    /**
     * Tokenize text
     */
    private function tokenize($text, $language) {
        if ($language === 'ko') {
            // Korean: split by spaces and common delimiters
            $words = preg_split('/[\s,\.!?;:()\[\]{}"\']+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        } else {
            // English: split by spaces and punctuation
            $words = preg_split('/[\s,\.!?;:()\[\]{}"\']+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        }

        return array_map('mb_strtolower', $words);
    }

    /**
     * Filter stop words
     */
    private function filter_stop_words($words) {
        $stop_words_lower = array_map('mb_strtolower', $this->stop_words);
        return array_filter($words, function($word) use ($stop_words_lower) {
            return !in_array($word, $stop_words_lower);
        });
    }

    /**
     * Calculate keyword score
     */
    private function calculate_keyword_score($word, $content, $tfidf) {
        $score = $tfidf * 100; // Base score

        // Boost if in title
        if (preg_match('/<h[1-6][^>]*>.*' . preg_quote($word, '/') . '.*<\/h[1-6]>/i', $content)) {
            $score += 20;
        }

        // Boost if in first paragraph
        $first_paragraph = substr($content, 0, 200);
        if (stripos($first_paragraph, $word) !== false) {
            $score += 15;
        }

        // Boost if in last paragraph
        $last_paragraph = substr($content, -200);
        if (stripos($last_paragraph, $word) !== false) {
            $score += 10;
        }

        // Boost if longer (more specific)
        $length = mb_strlen($word);
        if ($length >= 3 && $length <= 5) {
            $score += 5;
        } elseif ($length > 5) {
            $score += 10; // Long-tail keywords
        }

        return $score;
    }

    /**
     * Find keyword positions in content
     */
    private function find_keyword_positions($keyword, $content) {
        $positions = [];
        $offset = 0;
        $keyword_lower = mb_strtolower($keyword);
        $content_lower = mb_strtolower($content);

        while (($pos = mb_strpos($content_lower, $keyword_lower, $offset)) !== false) {
            $positions[] = $pos;
            $offset = $pos + 1;
        }

        return $positions;
    }

    /**
     * Get keywords from ranking factors
     */
    private function get_keywords_from_factors($target_keyword) {
        // This would analyze the target keyword against ranking factors
        // and suggest related terms based on factor importance

        $keywords = [];

        // Get all factors from database (use static method)
        $factors = WP_Bulk_SEO_Ranking_Factors_DB::get_all_factors();

        if (is_array($factors)) {
            foreach ($factors as $factor) {
                $desc = $factor['description_kr'] ?? $factor['description_en'] ?? '';
                if (stripos($desc, $target_keyword) !== false) {
                    $keywords[] = [
                        'keyword' => $target_keyword,
                        'type' => 'ranking_factor',
                        'relevance_score' => isset($factor['weight']) ? min(10, $factor['weight']) : 5,
                    ];
                }
            }
        }

        return $keywords;
    }

    /**
     * Generate synonyms
     */
    private function generate_synonyms($keyword) {
        // This would ideally use a thesaurus API or database
        // For now, return common variations

        $synonyms = [];

        // Add variations
        $variations = [
            $keyword . ' 방법',
            $keyword . ' 가이드',
            $keyword . ' 팁',
            $keyword . ' 정보',
            $keyword . ' 알아보기',
        ];

        foreach ($variations as $variation) {
            $synonyms[] = [
                'keyword' => $variation,
                'type' => 'synonym',
                'relevance_score' => 7,
            ];
        }

        return $synonyms;
    }

    /**
     * Generate related keywords
     */
    private function generate_related_keywords($keyword) {
        $related = [];

        // Common related patterns
        $patterns = [
            'best ' . $keyword,
            'top ' . $keyword,
            $keyword . ' review',
            $keyword . ' comparison',
            'how to ' . $keyword,
            'what is ' . $keyword,
            'why ' . $keyword,
        ];

        foreach ($patterns as $pattern) {
            $related[] = [
                'keyword' => $pattern,
                'type' => 'related',
                'relevance_score' => 6,
            ];
        }

        return $related;
    }

    /**
     * Generate long-tail keywords
     */
    private function generate_long_tail_keywords($keyword) {
        $long_tail = [];

        $modifiers = [
            '가장 좋은',
            '최고의',
            '완벽한',
            '쉬운',
            '빠른',
            '저렴한',
            '무료',
            '프리미엄',
            '전문가',
            '초보자',
        ];

        foreach ($modifiers as $modifier) {
            $long_tail[] = [
                'keyword' => $modifier . ' ' . $keyword,
                'type' => 'long_tail',
                'relevance_score' => 8, // Long-tail often has less competition
            ];
        }

        return $long_tail;
    }

    /**
     * Score recommendations
     */
    private function score_recommendations($recommendations, $target_keyword) {
        foreach ($recommendations as &$rec) {
            // Base score from type
            $base_score = $rec['relevance_score'] ?? 5;

            // Boost if contains target keyword
            if (stripos($rec['keyword'], $target_keyword) !== false) {
                $base_score += 3;
            }

            // Boost if longer (more specific)
            $length = mb_strlen($rec['keyword']);
            if ($length > mb_strlen($target_keyword)) {
                $base_score += 2;
            }

            $rec['relevance_score'] = min(10, $base_score);
        }

        return $recommendations;
    }

    /**
     * Deduplicate keywords
     */
    private function deduplicate_keywords($keywords) {
        $seen = [];
        $unique = [];

        foreach ($keywords as $keyword_data) {
            $key = mb_strtolower($keyword_data['keyword']);
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $keyword_data;
            }
        }

        return $unique;
    }

    /**
     * Estimate keyword difficulty
     */
    private function estimate_difficulty($keyword) {
        // Simple estimation based on keyword characteristics
        $length = mb_strlen($keyword);
        $word_count = str_word_count($keyword);

        $difficulty = 30; // Base difficulty

        // Longer keywords = less competition
        if ($length > 20) {
            $difficulty -= 10;
        }

        // More words = long-tail = easier
        if ($word_count > 3) {
            $difficulty -= 15;
        }

        // Very short = competitive
        if ($length < 5) {
            $difficulty += 20;
        }

        return max(0, min(100, $difficulty));
    }

    /**
     * Analyze keyword in content
     */
    private function analyze_keyword_in_content($keyword, $content) {
        $keyword_lower = mb_strtolower($keyword);
        $content_lower = mb_strtolower($content);

        $count = substr_count($content_lower, $keyword_lower);
        $word_count = str_word_count($content);
        $density = $word_count > 0 ? ($count / $word_count) * 100 : 0;

        return [
            'count' => $count,
            'density' => round($density, 2),
            'in_title' => stripos($content, $keyword) !== false,
            'in_first_paragraph' => stripos(substr($content, 0, 200), $keyword) !== false,
            'optimal_density' => $density >= 1 && $density <= 2.5,
        ];
    }

    /**
     * Get keyword ranking factors
     */
    private function get_keyword_ranking_factors($keyword) {
        // Return relevant ranking factors for this keyword
        return [
            'on_page' => [
                'title_optimization' => 'High',
                'meta_description' => 'High',
                'heading_structure' => 'Medium',
                'keyword_density' => 'Medium',
            ],
            'content' => [
                'content_length' => 'High',
                'content_quality' => 'High',
                'readability' => 'Medium',
            ],
            'technical' => [
                'url_structure' => 'Medium',
                'internal_linking' => 'High',
                'external_linking' => 'Medium',
            ],
        ];
    }

    /**
     * Check keyword in meta
     */
    private function check_keyword_in_meta($post_id, $keyword) {
        $seo_title = get_post_meta($post_id, '_wp_bulk_seo_title', true);
        $seo_desc = get_post_meta($post_id, '_wp_bulk_seo_description', true);

        return [
            'in_seo_title' => stripos($seo_title, $keyword) !== false,
            'in_seo_description' => stripos($seo_desc, $keyword) !== false,
        ];
    }
}
