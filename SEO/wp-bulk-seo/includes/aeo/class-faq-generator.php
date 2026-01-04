<?php
/**
 * WP Bulk SEO - FAQ Generator
 *
 * Generates FAQ content and schema markup for AEO optimization.
 * Uses AI to create relevant questions and answers.
 *
 * @package WP_Bulk_SEO
 * @subpackage AEO
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_FAQ_Generator {

    /**
     * AI Engine instance
     */
    private $ai_engine;

    /**
     * Settings
     */
    private $settings;

    /**
     * Common question templates by category
     */
    private const QUESTION_TEMPLATES = [
        'general' => [
            'What is {topic}?',
            'How does {topic} work?',
            'Why is {topic} important?',
            'What are the benefits of {topic}?',
            'Who should use {topic}?',
        ],
        'how_to' => [
            'How to {action}?',
            'What is the best way to {action}?',
            'How long does it take to {action}?',
            'What do I need to {action}?',
            'Can beginners {action}?',
        ],
        'comparison' => [
            'What is the difference between {a} and {b}?',
            'Is {a} better than {b}?',
            'When should I choose {a} over {b}?',
            '{a} vs {b}: which is right for me?',
        ],
        'product' => [
            'What is {product}?',
            'How much does {product} cost?',
            'Is {product} worth it?',
            'What are the alternatives to {product}?',
            'How do I use {product}?',
        ],
        'troubleshooting' => [
            'Why is {problem} happening?',
            'How do I fix {problem}?',
            'What causes {problem}?',
            'How to prevent {problem}?',
            'Is {problem} normal?',
        ],
    ];

    /**
     * Constructor
     */
    public function __construct() {
        $this->settings = get_option('wp_bulk_seo_faq', [
            'auto_generate' => false,
            'min_questions' => 5,
            'max_questions' => 10,
            'use_ai' => true,
        ]);

        if (class_exists('WP_Bulk_SEO_AI_Engine')) {
            $this->ai_engine = new WP_Bulk_SEO_AI_Engine();
        }
    }

    /**
     * Generate FAQ items from content
     *
     * @param int|WP_Post $post Post to generate FAQs for
     * @param array $options Generation options
     * @return array FAQ items
     */
    public function generate($post, $options = []) {
        if (is_numeric($post)) {
            $post = get_post($post);
        }

        if (!$post) {
            return [];
        }

        $options = wp_parse_args($options, [
            'count' => $this->settings['min_questions'],
            'use_ai' => $this->settings['use_ai'],
            'include_existing' => true,
        ]);

        $faq_items = [];

        // Extract existing FAQ items from content
        if ($options['include_existing']) {
            $existing = $this->extract_existing_faqs($post->post_content);
            $faq_items = array_merge($faq_items, $existing);
        }

        // Generate additional FAQs if needed
        $needed = $options['count'] - count($faq_items);

        if ($needed > 0) {
            if ($options['use_ai'] && $this->ai_engine) {
                $ai_faqs = $this->generate_with_ai($post, $needed);
                $faq_items = array_merge($faq_items, $ai_faqs);
            } else {
                $template_faqs = $this->generate_from_templates($post, $needed);
                $faq_items = array_merge($faq_items, $template_faqs);
            }
        }

        // Deduplicate and limit
        $faq_items = $this->deduplicate_faqs($faq_items);
        $faq_items = array_slice($faq_items, 0, $this->settings['max_questions']);

        return $faq_items;
    }

    /**
     * Extract existing FAQ items from content
     */
    public function extract_existing_faqs($content) {
        $faq_items = [];

        // Method 1: Yoast/Rank Math FAQ blocks
        if (preg_match_all('/<!-- wp:(yoast|rank-math)\/faq-block.*?-->(.+?)<!-- \/wp:\1\/faq-block -->/s', $content, $blocks)) {
            foreach ($blocks[2] as $block) {
                if (preg_match_all('/"question":"([^"]+)".*?"answer":"([^"]+)"/s', $block, $items, PREG_SET_ORDER)) {
                    foreach ($items as $item) {
                        $faq_items[] = [
                            'question' => wp_strip_all_tags(html_entity_decode($item[1])),
                            'answer' => wp_strip_all_tags(html_entity_decode($item[2])),
                            'source' => 'block',
                        ];
                    }
                }
            }
        }

        // Method 2: Question headings + paragraphs
        if (empty($faq_items)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            @$dom->loadHTML(mb_convert_encoding('<div>' . $content . '</div>', 'HTML-ENTITIES', 'UTF-8'));
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);
            $headings = $xpath->query('//h2|//h3|//h4');

            foreach ($headings as $heading) {
                $question = trim($heading->textContent);

                if (strpos($question, '?') !== false || $this->looks_like_question($question)) {
                    $answer = $this->get_answer_after_heading($heading);

                    if (!empty($answer)) {
                        $faq_items[] = [
                            'question' => $question,
                            'answer' => $answer,
                            'source' => 'heading',
                        ];
                    }
                }
            }
        }

        // Method 3: Definition list (dt/dd)
        if (preg_match_all('/<dt[^>]*>([^<]+)<\/dt>\s*<dd[^>]*>([^<]+)<\/dd>/is', $content, $defs, PREG_SET_ORDER)) {
            foreach ($defs as $def) {
                $faq_items[] = [
                    'question' => trim($def[1]),
                    'answer' => trim($def[2]),
                    'source' => 'definition_list',
                ];
            }
        }

        return $faq_items;
    }

    /**
     * Check if text looks like a question
     */
    private function looks_like_question($text) {
        $question_starters = [
            'what', 'how', 'why', 'when', 'where', 'who', 'which',
            'can', 'should', 'is', 'are', 'does', 'do', 'will',
            '무엇', '어떻게', '왜', '언제', '어디', '누가',
        ];

        $text_lower = strtolower(trim($text));

        foreach ($question_starters as $starter) {
            if (strpos($text_lower, $starter) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get answer content after a heading
     */
    private function get_answer_after_heading($heading) {
        $answer = '';
        $next = $heading->nextSibling;
        $char_limit = 500;

        while ($next) {
            // Stop at next heading
            if (preg_match('/^h[1-6]$/i', $next->nodeName)) {
                break;
            }

            // Collect text from paragraphs, lists
            if (in_array($next->nodeName, ['p', 'ul', 'ol'])) {
                $text = trim($next->textContent);
                if (!empty($text)) {
                    $answer .= $text . ' ';
                }
            }

            // Limit length
            if (strlen($answer) > $char_limit) {
                break;
            }

            $next = $next->nextSibling;
        }

        return trim($answer);
    }

    /**
     * Generate FAQs using AI
     */
    public function generate_with_ai($post, $count) {
        if (!$this->ai_engine) {
            return [];
        }

        $title = get_the_title($post);
        $content = wp_strip_all_tags($post->post_content);
        $excerpt = wp_trim_words($content, 200);

        $prompt = "Based on this content, generate {$count} frequently asked questions and their answers.

Title: {$title}

Content excerpt:
{$excerpt}

Generate questions that users would commonly ask about this topic. Each answer should be 2-3 sentences, clear and informative.

Return as JSON array:
[
  {\"question\": \"Question text?\", \"answer\": \"Answer text.\"},
  ...
]";

        $response = $this->ai_engine->complete($prompt, [
            'max_tokens' => 1500,
            'temperature' => 0.7,
        ]);

        if (empty($response)) {
            return [];
        }

        // Parse JSON response
        $faq_items = [];

        // Try to extract JSON from response
        if (preg_match('/\[[\s\S]*\]/m', $response, $matches)) {
            $json = json_decode($matches[0], true);
            if (is_array($json)) {
                foreach ($json as $item) {
                    if (isset($item['question']) && isset($item['answer'])) {
                        $faq_items[] = [
                            'question' => sanitize_text_field($item['question']),
                            'answer' => sanitize_textarea_field($item['answer']),
                            'source' => 'ai_generated',
                        ];
                    }
                }
            }
        }

        return $faq_items;
    }

    /**
     * Generate FAQs from templates
     */
    public function generate_from_templates($post, $count) {
        $title = get_the_title($post);
        $content = wp_strip_all_tags($post->post_content);

        // Extract main topic from title
        $topic = $this->extract_topic($title);

        // Determine content category
        $category = $this->detect_category($title, $content);

        // Get templates for category
        $templates = self::QUESTION_TEMPLATES[$category] ?? self::QUESTION_TEMPLATES['general'];

        $faq_items = [];
        $used_templates = [];

        foreach ($templates as $template) {
            if (count($faq_items) >= $count) break;

            // Skip if already used
            if (in_array($template, $used_templates)) continue;
            $used_templates[] = $template;

            // Generate question from template
            $question = str_replace('{topic}', $topic, $template);
            $question = str_replace('{action}', $topic, $question);
            $question = str_replace('{product}', $topic, $question);
            $question = str_replace('{problem}', $topic, $question);

            // Generate a basic answer from content
            $answer = $this->extract_answer_from_content($content, $question);

            if (!empty($answer)) {
                $faq_items[] = [
                    'question' => $question,
                    'answer' => $answer,
                    'source' => 'template',
                ];
            }
        }

        return $faq_items;
    }

    /**
     * Extract topic from title
     */
    private function extract_topic($title) {
        // Remove common prefixes
        $prefixes = [
            'how to', 'what is', 'why', 'when', 'where', 'who',
            'the complete guide to', 'ultimate guide to', 'beginner\'s guide to',
            'a guide to', 'guide to', 'introduction to', 'understanding',
        ];

        $topic = strtolower($title);

        foreach ($prefixes as $prefix) {
            if (strpos($topic, $prefix) === 0) {
                $topic = trim(substr($topic, strlen($prefix)));
                break;
            }
        }

        // Remove trailing punctuation and year
        $topic = preg_replace('/[\?\!\.]$/', '', $topic);
        $topic = preg_replace('/\s+\d{4}$/', '', $topic);

        return ucfirst(trim($topic));
    }

    /**
     * Detect content category
     */
    private function detect_category($title, $content) {
        $title_lower = strtolower($title);
        $content_lower = strtolower($content);

        // How-to content
        if (preg_match('/how to|guide|tutorial|steps|instructions/i', $title_lower)) {
            return 'how_to';
        }

        // Comparison content
        if (preg_match('/\bvs\b|versus|compare|comparison|difference|better/i', $title_lower)) {
            return 'comparison';
        }

        // Product content
        if (preg_match('/review|price|cost|buy|purchase|product/i', $title_lower)) {
            return 'product';
        }

        // Troubleshooting
        if (preg_match('/fix|solve|problem|issue|error|not working/i', $title_lower)) {
            return 'troubleshooting';
        }

        return 'general';
    }

    /**
     * Extract answer from content
     */
    private function extract_answer_from_content($content, $question) {
        // Extract keywords from question
        $keywords = array_filter(explode(' ', strtolower($question)), function($word) {
            return strlen($word) > 3 && !in_array($word, ['what', 'how', 'does', 'the', 'this', 'that', 'with']);
        });

        if (empty($keywords)) {
            return '';
        }

        // Split content into sentences
        $sentences = preg_split('/[.!?]+/', $content);

        // Score sentences by keyword relevance
        $scored = [];
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) < 20 || strlen($sentence) > 300) continue;

            $score = 0;
            $sentence_lower = strtolower($sentence);

            foreach ($keywords as $keyword) {
                if (strpos($sentence_lower, $keyword) !== false) {
                    $score++;
                }
            }

            if ($score > 0) {
                $scored[] = [
                    'sentence' => $sentence,
                    'score' => $score,
                ];
            }
        }

        // Sort by score
        usort($scored, fn($a, $b) => $b['score'] - $a['score']);

        // Return top 2-3 sentences
        $answer_sentences = array_slice($scored, 0, 3);
        if (empty($answer_sentences)) {
            return '';
        }

        return implode('. ', array_column($answer_sentences, 'sentence')) . '.';
    }

    /**
     * Deduplicate FAQ items
     */
    private function deduplicate_faqs($faq_items) {
        $unique = [];
        $seen_questions = [];

        foreach ($faq_items as $item) {
            $normalized = strtolower(preg_replace('/[^a-z0-9]/', '', $item['question']));

            if (!in_array($normalized, $seen_questions)) {
                $seen_questions[] = $normalized;
                $unique[] = $item;
            }
        }

        return $unique;
    }

    /**
     * Generate FAQ schema markup
     *
     * @param array $faq_items FAQ items
     * @return string JSON-LD script tag
     */
    public function generate_faq_schema($faq_items) {
        if (empty($faq_items)) {
            return '';
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [],
        ];

        foreach ($faq_items as $item) {
            $schema['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $item['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['answer'],
                ],
            ];
        }

        return '<script type="application/ld+json">' . "\n" .
               wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) .
               "\n</script>";
    }

    /**
     * Generate FAQ HTML block
     *
     * @param array $faq_items FAQ items
     * @param array $options Display options
     * @return string HTML
     */
    public function generate_faq_html($faq_items, $options = []) {
        if (empty($faq_items)) {
            return '';
        }

        $options = wp_parse_args($options, [
            'accordion' => true,
            'schema' => true,
            'heading_tag' => 'h3',
            'container_class' => 'wp-bulk-seo-faq',
        ]);

        $html = '<div class="' . esc_attr($options['container_class']) . '">';
        $html .= '<h2>' . esc_html__('Frequently Asked Questions', 'wp-bulk-seo') . '</h2>';

        if ($options['accordion']) {
            $html .= '<div class="faq-accordion">';
            foreach ($faq_items as $index => $item) {
                $id = 'faq-' . $index;
                $html .= '<div class="faq-item">';
                $html .= '<' . $options['heading_tag'] . ' class="faq-question">';
                $html .= '<button type="button" aria-expanded="false" aria-controls="' . esc_attr($id) . '">';
                $html .= esc_html($item['question']);
                $html .= '</button>';
                $html .= '</' . $options['heading_tag'] . '>';
                $html .= '<div id="' . esc_attr($id) . '" class="faq-answer" hidden>';
                $html .= '<p>' . wp_kses_post($item['answer']) . '</p>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        } else {
            foreach ($faq_items as $item) {
                $html .= '<div class="faq-item">';
                $html .= '<' . $options['heading_tag'] . ' class="faq-question">';
                $html .= esc_html($item['question']);
                $html .= '</' . $options['heading_tag'] . '>';
                $html .= '<div class="faq-answer">';
                $html .= '<p>' . wp_kses_post($item['answer']) . '</p>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }

        $html .= '</div>';

        // Add schema if requested
        if ($options['schema']) {
            $html .= $this->generate_faq_schema($faq_items);
        }

        // Add basic CSS
        $html .= $this->get_faq_styles();

        // Add accordion JS if needed
        if ($options['accordion']) {
            $html .= $this->get_accordion_script();
        }

        return $html;
    }

    /**
     * Get FAQ styles
     */
    private function get_faq_styles() {
        return '<style>
            .wp-bulk-seo-faq { margin: 2em 0; }
            .wp-bulk-seo-faq h2 { margin-bottom: 1em; }
            .faq-item { border-bottom: 1px solid #eee; padding: 1em 0; }
            .faq-question { margin: 0; }
            .faq-question button {
                background: none;
                border: none;
                text-align: left;
                width: 100%;
                padding: 0.5em 0;
                font-size: inherit;
                font-weight: 600;
                cursor: pointer;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .faq-question button::after {
                content: "+";
                font-size: 1.2em;
                transition: transform 0.3s;
            }
            .faq-question button[aria-expanded="true"]::after {
                content: "-";
            }
            .faq-answer { padding: 0.5em 0 0 1em; }
            .faq-answer[hidden] { display: none; }
        </style>';
    }

    /**
     * Get accordion script
     */
    private function get_accordion_script() {
        return '<script>
            document.querySelectorAll(".faq-question button").forEach(function(button) {
                button.addEventListener("click", function() {
                    var expanded = this.getAttribute("aria-expanded") === "true";
                    var target = document.getElementById(this.getAttribute("aria-controls"));

                    this.setAttribute("aria-expanded", !expanded);
                    target.hidden = expanded;
                });
            });
        </script>';
    }

    /**
     * Save FAQ items to post meta
     */
    public function save_to_post($post_id, $faq_items) {
        update_post_meta($post_id, '_wp_bulk_seo_faq_items', $faq_items);
    }

    /**
     * Get FAQ items from post meta
     */
    public function get_from_post($post_id) {
        return get_post_meta($post_id, '_wp_bulk_seo_faq_items', true) ?: [];
    }

    /**
     * Auto-insert FAQ into content
     */
    public function auto_insert($content, $faq_items, $position = 'end') {
        $faq_html = $this->generate_faq_html($faq_items);

        switch ($position) {
            case 'start':
                return $faq_html . $content;

            case 'middle':
                $paragraphs = preg_split('/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
                $middle = ceil(count($paragraphs) / 2);
                array_splice($paragraphs, $middle, 0, $faq_html);
                return implode('', $paragraphs);

            case 'end':
            default:
                return $content . $faq_html;
        }
    }
}
