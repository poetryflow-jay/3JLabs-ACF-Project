<?php
/**
 * WP Bulk SEO - Featured Snippet Optimizer
 *
 * Optimizes content for Google Featured Snippets and AI-generated answers.
 * Supports paragraph, list, table, and definition snippets.
 *
 * @package OneClick_SEO_Pro
 * @subpackage AEO
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class OneClick_SEO_Pro_Featured_Snippet_Optimizer {

    /**
     * Snippet types
     */
    const TYPE_PARAGRAPH = 'paragraph';
    const TYPE_LIST = 'list';
    const TYPE_TABLE = 'table';
    const TYPE_DEFINITION = 'definition';
    const TYPE_STEPS = 'steps';

    /**
     * Optimal lengths
     */
    const OPTIMAL_PARAGRAPH_LENGTH = [40, 60]; // words
    const OPTIMAL_LIST_ITEMS = [4, 8];
    const OPTIMAL_DEFINITION_LENGTH = [30, 50]; // words

    /**
     * Constructor
     */
    public function __construct() {
        // Initialize
    }

    /**
     * Analyze content for featured snippet optimization
     *
     * @param int|WP_Post $post Post to analyze
     * @return array Analysis results
     */
    public function analyze($post) {
        if (is_numeric($post)) {
            $post = get_post($post);
        }

        if (!$post) {
            return ['error' => 'Post not found'];
        }

        $content = $post->post_content;
        $title = get_the_title($post);

        $analysis = [
            'post_id' => $post->ID,
            'snippet_types' => [],
            'potential_snippets' => [],
            'optimization_score' => 0,
            'recommendations' => [],
        ];

        // Detect which snippet types the content could qualify for
        $analysis['snippet_types'] = $this->detect_snippet_types($content, $title);

        // Extract potential snippet content
        $analysis['potential_snippets'] = $this->extract_potential_snippets($content, $title);

        // Analyze each potential snippet
        foreach ($analysis['potential_snippets'] as &$snippet) {
            $snippet['quality_score'] = $this->evaluate_snippet_quality($snippet);
            $snippet['improvements'] = $this->get_snippet_improvements($snippet);
        }

        // Calculate overall optimization score
        $analysis['optimization_score'] = $this->calculate_optimization_score($analysis);

        // Generate recommendations
        $analysis['recommendations'] = $this->generate_recommendations($analysis);

        return $analysis;
    }

    /**
     * Detect possible snippet types based on content
     */
    private function detect_snippet_types($content, $title) {
        $types = [];
        $title_lower = strtolower($title);

        // Definition snippet (What is...)
        if (preg_match('/what (is|are)/i', $title) ||
            preg_match('/definition|meaning|means/i', $title)) {
            $types[] = self::TYPE_DEFINITION;
        }

        // List snippet (How to..., Best..., Top...)
        if (preg_match('/how to|ways to|tips|steps|best|top \d+|reasons/i', $title)) {
            $types[] = self::TYPE_LIST;
        }

        // Steps snippet
        if (preg_match('/how to|guide|tutorial|instructions/i', $title)) {
            $types[] = self::TYPE_STEPS;
        }

        // Table snippet (comparison, vs, prices)
        if (preg_match('/vs|versus|compare|comparison|prices?|specifications?/i', $title)) {
            $types[] = self::TYPE_TABLE;
        }

        // Paragraph snippet (most common fallback)
        $types[] = self::TYPE_PARAGRAPH;

        return array_unique($types);
    }

    /**
     * Extract potential snippet content
     */
    private function extract_potential_snippets($content, $title) {
        $snippets = [];

        // Extract definition snippet
        $definition = $this->extract_definition_snippet($content, $title);
        if ($definition) {
            $snippets[] = $definition;
        }

        // Extract list snippets
        $lists = $this->extract_list_snippets($content);
        foreach ($lists as $list) {
            $snippets[] = $list;
        }

        // Extract table snippets
        $tables = $this->extract_table_snippets($content);
        foreach ($tables as $table) {
            $snippets[] = $table;
        }

        // Extract paragraph snippets
        $paragraphs = $this->extract_paragraph_snippets($content, $title);
        foreach ($paragraphs as $paragraph) {
            $snippets[] = $paragraph;
        }

        // Extract steps
        $steps = $this->extract_steps_snippet($content);
        if ($steps) {
            $snippets[] = $steps;
        }

        return $snippets;
    }

    /**
     * Extract definition snippet
     */
    private function extract_definition_snippet($content, $title) {
        // Look for "X is" or "X are" patterns near the beginning
        $patterns = [
            '/(?:<p[^>]*>)?([^.]+(?:is|are|refers to|means|defined as)[^.]+\.)/is',
            '/<p[^>]*>(.{20,200}?(?:is a|is an|is the|are the)[^.]+\.)/is',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $match)) {
                $definition = trim(strip_tags($match[1]));
                $word_count = str_word_count($definition);

                if ($word_count >= 20 && $word_count <= 80) {
                    return [
                        'type' => self::TYPE_DEFINITION,
                        'content' => $definition,
                        'word_count' => $word_count,
                        'position' => strpos($content, $match[0]),
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Extract list snippets
     */
    private function extract_list_snippets($content) {
        $snippets = [];

        // Find all lists
        preg_match_all('/<(ul|ol)[^>]*>(.*?)<\/\1>/is', $content, $lists, PREG_SET_ORDER);

        foreach ($lists as $list) {
            $list_type = $list[1];
            $list_content = $list[2];

            // Extract list items
            preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $list_content, $items);

            if (count($items[1]) >= 3) {
                $cleaned_items = array_map(function($item) {
                    return trim(strip_tags($item));
                }, $items[1]);

                $snippets[] = [
                    'type' => self::TYPE_LIST,
                    'list_type' => $list_type, // ul or ol
                    'items' => $cleaned_items,
                    'item_count' => count($cleaned_items),
                    'position' => strpos($content, $list[0]),
                    'content' => implode("\n", $cleaned_items),
                ];
            }
        }

        return $snippets;
    }

    /**
     * Extract table snippets
     */
    private function extract_table_snippets($content) {
        $snippets = [];

        preg_match_all('/<table[^>]*>(.*?)<\/table>/is', $content, $tables, PREG_SET_ORDER);

        foreach ($tables as $table) {
            $table_html = $table[0];
            $table_content = $table[1];

            // Extract headers
            preg_match_all('/<th[^>]*>(.*?)<\/th>/is', $table_content, $headers);
            $header_texts = array_map(fn($h) => trim(strip_tags($h)), $headers[1]);

            // Extract rows
            preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $table_content, $rows);
            $row_count = count($rows[0]);

            // Extract cells
            preg_match_all('/<td[^>]*>(.*?)<\/td>/is', $table_content, $cells);
            $cell_count = count($cells[1]);

            if ($row_count >= 2 && count($header_texts) >= 2) {
                $snippets[] = [
                    'type' => self::TYPE_TABLE,
                    'headers' => $header_texts,
                    'row_count' => $row_count,
                    'column_count' => count($header_texts),
                    'cell_count' => $cell_count,
                    'position' => strpos($content, $table[0]),
                    'content' => strip_tags($table_content),
                ];
            }
        }

        return $snippets;
    }

    /**
     * Extract paragraph snippets
     */
    private function extract_paragraph_snippets($content, $title) {
        $snippets = [];

        // Find paragraphs that directly answer the title question
        preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $content, $paragraphs, PREG_SET_ORDER);

        $title_keywords = $this->extract_keywords($title);

        foreach ($paragraphs as $index => $para) {
            $text = trim(strip_tags($para[1]));
            $word_count = str_word_count($text);

            // Skip too short or too long paragraphs
            if ($word_count < 30 || $word_count > 100) continue;

            // Score based on keyword relevance
            $relevance = $this->calculate_keyword_relevance($text, $title_keywords);

            // Check if it looks like an answer
            $starts_with_answer = preg_match('/^(yes|no|the|a|an|it|you|this|there|in|to|for)/i', $text);

            if ($relevance > 0.3 || ($starts_with_answer && $index < 5)) {
                $snippets[] = [
                    'type' => self::TYPE_PARAGRAPH,
                    'content' => $text,
                    'word_count' => $word_count,
                    'relevance' => $relevance,
                    'position' => strpos($content, $para[0]),
                    'paragraph_index' => $index,
                ];
            }
        }

        // Sort by relevance
        usort($snippets, fn($a, $b) => $b['relevance'] <=> $a['relevance']);

        return array_slice($snippets, 0, 3);
    }

    /**
     * Extract steps snippet
     */
    private function extract_steps_snippet($content) {
        $steps = [];

        // Look for numbered headings
        preg_match_all('/<h[2-4][^>]*>.*?(?:step\s*\d+|^\d+[\.\):])/im', $content, $step_headings);

        if (count($step_headings[0]) >= 3) {
            // Extract step content
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);
            $headings = $xpath->query('//h2|//h3|//h4');

            foreach ($headings as $heading) {
                $text = trim($heading->textContent);
                if (preg_match('/step\s*\d+|^\d+[\.\):]/i', $text)) {
                    $steps[] = $text;
                }
            }
        }

        // Look for ordered list at the beginning
        if (empty($steps) && preg_match('/<ol[^>]*>(.*?)<\/ol>/is', $content, $ol_match)) {
            preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $ol_match[1], $items);
            if (count($items[1]) >= 3) {
                $steps = array_map(fn($i) => trim(strip_tags($i)), $items[1]);
            }
        }

        if (count($steps) >= 3) {
            return [
                'type' => self::TYPE_STEPS,
                'steps' => $steps,
                'step_count' => count($steps),
                'content' => implode("\n", $steps),
            ];
        }

        return null;
    }

    /**
     * Extract keywords from text
     */
    private function extract_keywords($text) {
        $text = strtolower($text);
        $words = preg_split('/\s+/', $text);

        // Filter stop words
        $stop_words = ['the', 'a', 'an', 'is', 'are', 'was', 'were', 'to', 'of', 'in', 'for', 'on', 'with', 'as', 'by', 'at', 'and', 'or', 'but', 'not', 'you', 'your', 'how', 'what', 'when', 'where', 'why', 'which'];

        return array_filter($words, function($word) use ($stop_words) {
            return strlen($word) > 2 && !in_array($word, $stop_words);
        });
    }

    /**
     * Calculate keyword relevance
     */
    private function calculate_keyword_relevance($text, $keywords) {
        if (empty($keywords)) return 0;

        $text_lower = strtolower($text);
        $matches = 0;

        foreach ($keywords as $keyword) {
            if (strpos($text_lower, $keyword) !== false) {
                $matches++;
            }
        }

        return $matches / count($keywords);
    }

    /**
     * Evaluate snippet quality
     */
    private function evaluate_snippet_quality($snippet) {
        $score = 0;

        switch ($snippet['type']) {
            case self::TYPE_DEFINITION:
                $word_count = $snippet['word_count'];
                if ($word_count >= self::OPTIMAL_DEFINITION_LENGTH[0] && $word_count <= self::OPTIMAL_DEFINITION_LENGTH[1]) {
                    $score = 100;
                } elseif ($word_count >= 20 && $word_count <= 80) {
                    $score = 70;
                } else {
                    $score = 40;
                }

                // Bonus for being early in content
                if ($snippet['position'] < 500) $score += 10;
                break;

            case self::TYPE_LIST:
                $item_count = $snippet['item_count'];
                if ($item_count >= self::OPTIMAL_LIST_ITEMS[0] && $item_count <= self::OPTIMAL_LIST_ITEMS[1]) {
                    $score = 100;
                } elseif ($item_count >= 3 && $item_count <= 10) {
                    $score = 80;
                } else {
                    $score = 50;
                }

                // Check item consistency
                $avg_length = array_sum(array_map('strlen', $snippet['items'])) / count($snippet['items']);
                if ($avg_length >= 20 && $avg_length <= 100) $score += 10;
                break;

            case self::TYPE_TABLE:
                if ($snippet['row_count'] >= 3 && $snippet['column_count'] >= 2) {
                    $score = 90;
                } else {
                    $score = 60;
                }

                // Bonus for having clear headers
                if (count($snippet['headers']) >= 2) $score += 10;
                break;

            case self::TYPE_PARAGRAPH:
                $word_count = $snippet['word_count'];
                if ($word_count >= self::OPTIMAL_PARAGRAPH_LENGTH[0] && $word_count <= self::OPTIMAL_PARAGRAPH_LENGTH[1]) {
                    $score = 100;
                } elseif ($word_count >= 30 && $word_count <= 80) {
                    $score = 75;
                } else {
                    $score = 50;
                }

                // Relevance bonus
                $score += ($snippet['relevance'] ?? 0) * 20;

                // Position bonus (earlier is better)
                if (($snippet['paragraph_index'] ?? 10) < 3) $score += 10;
                break;

            case self::TYPE_STEPS:
                $step_count = $snippet['step_count'];
                if ($step_count >= 3 && $step_count <= 8) {
                    $score = 90;
                } else {
                    $score = 60;
                }
                break;
        }

        return min(100, $score);
    }

    /**
     * Get improvements for a snippet
     */
    private function get_snippet_improvements($snippet) {
        $improvements = [];

        switch ($snippet['type']) {
            case self::TYPE_DEFINITION:
                if ($snippet['word_count'] < 30) {
                    $improvements[] = 'Expand the definition to 30-50 words for optimal snippet length';
                }
                if ($snippet['word_count'] > 60) {
                    $improvements[] = 'Shorten the definition to 40-60 words';
                }
                if ($snippet['position'] > 1000) {
                    $improvements[] = 'Move the definition closer to the beginning of the content';
                }
                break;

            case self::TYPE_LIST:
                if ($snippet['item_count'] < 4) {
                    $improvements[] = 'Add more list items (optimal: 4-8 items)';
                }
                if ($snippet['item_count'] > 8) {
                    $improvements[] = 'Consider splitting into multiple lists or using subheadings';
                }

                // Check for consistency
                $lengths = array_map('strlen', $snippet['items']);
                $variance = $this->calculate_variance($lengths);
                if ($variance > 2000) {
                    $improvements[] = 'Make list items more consistent in length';
                }
                break;

            case self::TYPE_TABLE:
                if (count($snippet['headers']) < 2) {
                    $improvements[] = 'Add clear column headers to the table';
                }
                if ($snippet['row_count'] < 3) {
                    $improvements[] = 'Add more rows to make the table more comprehensive';
                }
                break;

            case self::TYPE_PARAGRAPH:
                if ($snippet['word_count'] < 40) {
                    $improvements[] = 'Expand the answer paragraph to 40-60 words';
                }
                if ($snippet['word_count'] > 80) {
                    $improvements[] = 'Shorten the paragraph to 50-70 words';
                }
                if (($snippet['paragraph_index'] ?? 0) > 3) {
                    $improvements[] = 'Move the key answer to the first few paragraphs';
                }
                break;

            case self::TYPE_STEPS:
                if ($snippet['step_count'] < 4) {
                    $improvements[] = 'Break down the process into more detailed steps';
                }
                break;
        }

        return $improvements;
    }

    /**
     * Calculate variance
     */
    private function calculate_variance($numbers) {
        if (count($numbers) < 2) return 0;

        $mean = array_sum($numbers) / count($numbers);
        $variance = 0;

        foreach ($numbers as $num) {
            $variance += pow($num - $mean, 2);
        }

        return $variance / count($numbers);
    }

    /**
     * Calculate overall optimization score
     */
    private function calculate_optimization_score($analysis) {
        if (empty($analysis['potential_snippets'])) {
            return 0;
        }

        // Get best snippet score for each type
        $best_scores = [];
        foreach ($analysis['potential_snippets'] as $snippet) {
            $type = $snippet['type'];
            $score = $snippet['quality_score'];

            if (!isset($best_scores[$type]) || $score > $best_scores[$type]) {
                $best_scores[$type] = $score;
            }
        }

        // Weight different snippet types
        $weights = [
            self::TYPE_DEFINITION => 1.2,
            self::TYPE_LIST => 1.1,
            self::TYPE_TABLE => 1.0,
            self::TYPE_PARAGRAPH => 0.9,
            self::TYPE_STEPS => 1.1,
        ];

        $weighted_sum = 0;
        $total_weight = 0;

        foreach ($best_scores as $type => $score) {
            $weight = $weights[$type] ?? 1.0;
            $weighted_sum += $score * $weight;
            $total_weight += $weight;
        }

        return $total_weight > 0 ? round($weighted_sum / $total_weight) : 0;
    }

    /**
     * Generate recommendations
     */
    private function generate_recommendations($analysis) {
        $recommendations = [];

        // Check for missing snippet types
        if (!in_array(self::TYPE_DEFINITION, $analysis['snippet_types'])) {
            // Check if title suggests definition content
            if (preg_match('/what (is|are)/i', '')) {
                $recommendations[] = [
                    'priority' => 'high',
                    'type' => 'definition',
                    'message' => 'Add a clear definition in the first paragraph',
                    'message_kr' => '첫 번째 단락에 명확한 정의를 추가하세요',
                ];
            }
        }

        // Check for lists
        $has_good_list = false;
        foreach ($analysis['potential_snippets'] as $snippet) {
            if ($snippet['type'] === self::TYPE_LIST && $snippet['quality_score'] >= 70) {
                $has_good_list = true;
                break;
            }
        }

        if (!$has_good_list) {
            $recommendations[] = [
                'priority' => 'medium',
                'type' => 'list',
                'message' => 'Add a numbered or bulleted list with 4-8 items',
                'message_kr' => '4-8개 항목의 번호 또는 글머리 기호 목록을 추가하세요',
            ];
        }

        // Collect snippet-specific improvements
        foreach ($analysis['potential_snippets'] as $snippet) {
            foreach ($snippet['improvements'] ?? [] as $improvement) {
                $recommendations[] = [
                    'priority' => 'medium',
                    'type' => $snippet['type'],
                    'message' => $improvement,
                ];
            }
        }

        // Check overall score
        if ($analysis['optimization_score'] < 60) {
            array_unshift($recommendations, [
                'priority' => 'critical',
                'type' => 'overall',
                'message' => 'Content needs significant optimization for featured snippets',
                'message_kr' => '추천 스니펫을 위해 콘텐츠의 중요한 최적화가 필요합니다',
            ]);
        }

        return $recommendations;
    }

    /**
     * Optimize content for featured snippets
     *
     * @param string $content Original content
     * @param string $title Post title
     * @param array $options Optimization options
     * @return string Optimized content
     */
    public function optimize($content, $title, $options = []) {
        $options = wp_parse_args($options, [
            'add_definition' => true,
            'format_lists' => true,
            'add_summary' => true,
        ]);

        // Add definition if missing and title is a "what is" question
        if ($options['add_definition'] && preg_match('/what (is|are) (.+)/i', $title, $match)) {
            $topic = trim($match[2], '?');
            $has_definition = preg_match('/^.{0,100}' . preg_quote($topic) . '.{0,20}(is|are)/im', $content);

            if (!$has_definition) {
                // Try to extract or generate definition
                $definition = $this->extract_or_generate_definition($content, $topic);
                if ($definition) {
                    $content = $this->insert_definition($content, $definition);
                }
            }
        }

        // Format lists properly
        if ($options['format_lists']) {
            $content = $this->format_lists_for_snippets($content);
        }

        // Add summary/TL;DR if not present
        if ($options['add_summary']) {
            $has_summary = preg_match('/summary|tldr|tl;dr|key takeaways|in short|conclusion/i', $content);
            if (!$has_summary) {
                // Add summary markers that won't affect content
                // This is a placeholder - actual summary generation would use AI
            }
        }

        return $content;
    }

    /**
     * Extract or generate definition
     */
    private function extract_or_generate_definition($content, $topic) {
        // Try to find existing definition
        $pattern = '/' . preg_quote($topic) . '[^.]*(?:is|are|refers to)[^.]+\./i';
        if (preg_match($pattern, $content, $match)) {
            return trim($match[0]);
        }

        // Extract from first paragraph
        if (preg_match('/<p[^>]*>(.+?)<\/p>/is', $content, $match)) {
            $first_para = trim(strip_tags($match[1]));
            if (strlen($first_para) >= 50 && strlen($first_para) <= 300) {
                return $first_para;
            }
        }

        return null;
    }

    /**
     * Insert definition at the beginning
     */
    private function insert_definition($content, $definition) {
        // Wrap definition in a paragraph with definition styling
        $def_html = '<p class="featured-definition"><strong>' . esc_html($definition) . '</strong></p>';

        // Find first paragraph and insert before
        if (preg_match('/^(.*?)(<p[^>]*>)/is', $content, $match)) {
            return $match[1] . $def_html . $match[2];
        }

        return $def_html . $content;
    }

    /**
     * Format lists for better snippet capture
     */
    private function format_lists_for_snippets($content) {
        // Ensure lists have proper semantic structure
        $content = preg_replace('/<li>\s*<p>/', '<li>', $content);
        $content = preg_replace('/<\/p>\s*<\/li>/', '</li>', $content);

        // Add aria labels to lists if missing
        $content = preg_replace('/<ul(?![^>]*role=)/', '<ul role="list"', $content);
        $content = preg_replace('/<ol(?![^>]*role=)/', '<ol role="list"', $content);

        return $content;
    }

    /**
     * Generate snippet preview
     *
     * @param array $snippet Snippet data
     * @return string HTML preview
     */
    public function generate_preview($snippet) {
        $html = '<div class="snippet-preview snippet-type-' . esc_attr($snippet['type']) . '">';

        switch ($snippet['type']) {
            case self::TYPE_DEFINITION:
            case self::TYPE_PARAGRAPH:
                $html .= '<p>' . esc_html($snippet['content']) . '</p>';
                break;

            case self::TYPE_LIST:
                $tag = $snippet['list_type'] ?? 'ul';
                $html .= '<' . $tag . '>';
                foreach ($snippet['items'] as $item) {
                    $html .= '<li>' . esc_html($item) . '</li>';
                }
                $html .= '</' . $tag . '>';
                break;

            case self::TYPE_TABLE:
                $html .= '<table><thead><tr>';
                foreach ($snippet['headers'] as $header) {
                    $html .= '<th>' . esc_html($header) . '</th>';
                }
                $html .= '</tr></thead></table>';
                break;

            case self::TYPE_STEPS:
                $html .= '<ol>';
                foreach ($snippet['steps'] as $step) {
                    $html .= '<li>' . esc_html($step) . '</li>';
                }
                $html .= '</ol>';
                break;
        }

        $html .= '<div class="snippet-score">Quality Score: ' . esc_html($snippet['quality_score']) . '/100</div>';
        $html .= '</div>';

        return $html;
    }
}
