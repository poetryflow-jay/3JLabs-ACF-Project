<?php
/**
 * WP Bulk SEO - AEO (Answer Engine Optimization) Engine
 *
 * Optimizes content for AI search engines like:
 * - ChatGPT (OpenAI)
 * - Perplexity AI
 * - Claude (Anthropic)
 * - Google SGE (Search Generative Experience)
 * - Bing Copilot
 *
 * @package WP_Bulk_SEO
 * @subpackage AEO
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Engine {

    /**
     * AI Engine instance
     */
    private $ai_engine;

    /**
     * FAQ Generator instance
     */
    private $faq_generator;

    /**
     * Featured Snippet Optimizer instance
     */
    private $snippet_optimizer;

    /**
     * AEO optimization settings
     */
    private $settings = [];

    /**
     * Question patterns for different languages
     */
    private const QUESTION_PATTERNS = [
        'en' => [
            'what is', 'what are', 'what does', 'what do',
            'how to', 'how do', 'how does', 'how can',
            'why is', 'why are', 'why do', 'why does',
            'when is', 'when are', 'when do', 'when does',
            'where is', 'where are', 'where can', 'where do',
            'who is', 'who are', 'who does', 'who can',
            'which is', 'which are', 'which do',
            'can i', 'can you', 'should i', 'should you',
            'is it', 'are there', 'does it', 'do you',
        ],
        'ko' => [
            '무엇', '뭐', '어떻게', '왜', '언제', '어디', '누가', '누구',
            '어떤', '얼마', '몇', '이유', '방법', '하는법', '차이',
            '뜻', '의미', '장점', '단점', '비교', '추천', '가격',
            '할 수', '해야', '인가', '인지', '인가요', '인지요',
        ],
    ];

    /**
     * Constructor
     */
    public function __construct() {
        $this->load_settings();
        $this->init_dependencies();
        $this->init_hooks();
    }

    /**
     * Load settings
     */
    private function load_settings() {
        $this->settings = get_option('wp_bulk_seo_aeo', [
            'enabled' => true,
            'auto_generate_faq' => true,
            'auto_optimize_snippets' => true,
            'conversational_content' => true,
            'entity_optimization' => true,
            'citation_formatting' => true,
            'ai_provider' => 'openai',
            'language' => 'en',
        ]);
    }

    /**
     * Initialize dependencies
     */
    private function init_dependencies() {
        // Initialize sub-components
        if (class_exists('WP_Bulk_SEO_FAQ_Generator')) {
            $this->faq_generator = new WP_Bulk_SEO_FAQ_Generator();
        }

        if (class_exists('WP_Bulk_SEO_Featured_Snippet_Optimizer')) {
            $this->snippet_optimizer = new WP_Bulk_SEO_Featured_Snippet_Optimizer();
        }

        if (class_exists('WP_Bulk_SEO_AI_Engine')) {
            $this->ai_engine = new WP_Bulk_SEO_AI_Engine();
        }
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        if (empty($this->settings['enabled'])) {
            return;
        }

        // Content filters
        add_filter('the_content', [$this, 'enhance_content_for_aeo'], 20);

        // Meta box for AEO settings
        add_action('add_meta_boxes', [$this, 'add_aeo_meta_box']);
        add_action('save_post', [$this, 'save_aeo_meta']);

        // REST API for AEO analysis
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    /**
     * Analyze content for AEO optimization
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
            'overall_score' => 0,
            'factors' => [],
            'recommendations' => [],
        ];

        // Factor 1: Question Coverage
        $question_analysis = $this->analyze_question_coverage($content);
        $analysis['factors']['question_coverage'] = $question_analysis;

        // Factor 2: Direct Answer Presence
        $direct_answer = $this->analyze_direct_answers($content, $title);
        $analysis['factors']['direct_answers'] = $direct_answer;

        // Factor 3: Conversational Tone
        $conversational = $this->analyze_conversational_content($content);
        $analysis['factors']['conversational_tone'] = $conversational;

        // Factor 4: FAQ Structure
        $faq_analysis = $this->analyze_faq_structure($content);
        $analysis['factors']['faq_structure'] = $faq_analysis;

        // Factor 5: Citation & Source Quality
        $citation_analysis = $this->analyze_citations($content);
        $analysis['factors']['citations'] = $citation_analysis;

        // Factor 6: Entity Recognition
        $entity_analysis = $this->analyze_entities($content);
        $analysis['factors']['entities'] = $entity_analysis;

        // Factor 7: Featured Snippet Potential
        $snippet_analysis = $this->analyze_snippet_potential($content);
        $analysis['factors']['snippet_potential'] = $snippet_analysis;

        // Factor 8: Content Structure
        $structure_analysis = $this->analyze_content_structure($content);
        $analysis['factors']['content_structure'] = $structure_analysis;

        // Calculate overall score
        $weights = [
            'question_coverage' => 15,
            'direct_answers' => 20,
            'conversational_tone' => 10,
            'faq_structure' => 15,
            'citations' => 10,
            'entities' => 10,
            'snippet_potential' => 10,
            'content_structure' => 10,
        ];

        $total_weight = array_sum($weights);
        $weighted_score = 0;

        foreach ($analysis['factors'] as $key => $factor) {
            $weight = $weights[$key] ?? 10;
            $score = $factor['score'] ?? 0;
            $weighted_score += $score * $weight;

            // Collect recommendations
            if (!empty($factor['recommendations'])) {
                $analysis['recommendations'] = array_merge(
                    $analysis['recommendations'],
                    $factor['recommendations']
                );
            }
        }

        $analysis['overall_score'] = round($weighted_score / $total_weight);
        $analysis['grade'] = $this->calculate_grade($analysis['overall_score']);

        // Sort recommendations by priority
        usort($analysis['recommendations'], function($a, $b) {
            $priority_order = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
            $a_priority = $priority_order[$a['priority'] ?? 'medium'] ?? 2;
            $b_priority = $priority_order[$b['priority'] ?? 'medium'] ?? 2;
            return $a_priority - $b_priority;
        });

        return $analysis;
    }

    /**
     * Analyze question coverage in content
     */
    private function analyze_question_coverage($content) {
        $content_lower = strtolower($content);
        $lang = $this->settings['language'] ?? 'en';
        $patterns = self::QUESTION_PATTERNS[$lang] ?? self::QUESTION_PATTERNS['en'];

        $found_patterns = [];
        $question_count = 0;

        foreach ($patterns as $pattern) {
            if (strpos($content_lower, $pattern) !== false) {
                $found_patterns[] = $pattern;
                $question_count += substr_count($content_lower, $pattern);
            }
        }

        // Count actual question marks
        $question_marks = substr_count($content, '?');

        // Check headings for questions
        preg_match_all('/<h[2-4][^>]*>([^<]+\?)[^<]*<\/h[2-4]>/i', $content, $heading_questions);
        $heading_question_count = count($heading_questions[0]);

        $score = 0;
        $recommendations = [];

        if ($question_marks >= 5 && $heading_question_count >= 2) {
            $score = 100;
        } elseif ($question_marks >= 3 || $heading_question_count >= 1) {
            $score = 70;
        } elseif ($question_marks >= 1) {
            $score = 40;
        } else {
            $recommendations[] = [
                'type' => 'question_coverage',
                'priority' => 'high',
                'message' => 'Add question-based headings to improve AI search visibility',
                'message_kr' => '질문 형식의 제목을 추가하여 AI 검색 가시성을 높이세요',
            ];
        }

        return [
            'score' => $score,
            'question_marks' => $question_marks,
            'heading_questions' => $heading_question_count,
            'patterns_found' => count($found_patterns),
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Analyze direct answer presence
     */
    private function analyze_direct_answers($content, $title) {
        $score = 0;
        $recommendations = [];

        // Check for definition patterns
        $has_definition = preg_match('/^.{0,100}(is|are|refers to|means|defined as)/im', $content);

        // Check for numbered/bulleted lists
        preg_match_all('/<(ul|ol)[^>]*>.*?<\/\1>/is', $content, $lists);
        $list_count = count($lists[0]);

        // Check for short answer paragraphs (under 300 chars)
        preg_match_all('/<p[^>]*>(.{10,300})<\/p>/i', $content, $paragraphs);
        $short_paragraphs = count(array_filter($paragraphs[1], function($p) {
            return strlen(strip_tags($p)) <= 300;
        }));

        // Check for bold/strong emphasis on key terms
        preg_match_all('/<(strong|b)[^>]*>([^<]+)<\/\1>/i', $content, $bolds);
        $bold_count = count($bolds[0]);

        // Check if first paragraph answers the title question
        preg_match('/<p[^>]*>(.+?)<\/p>/is', $content, $first_para);
        $first_para_text = $first_para[1] ?? '';
        $answers_title = false;

        if (strpos($title, '?') !== false) {
            // Title is a question
            $first_words = array_slice(explode(' ', strip_tags($first_para_text)), 0, 20);
            $answer_starters = ['yes', 'no', 'the', 'a', 'it', 'you', 'this'];
            $answers_title = in_array(strtolower($first_words[0] ?? ''), $answer_starters);
        }

        // Calculate score
        if ($has_definition) $score += 25;
        if ($list_count >= 2) $score += 25;
        if ($short_paragraphs >= 3) $score += 25;
        if ($bold_count >= 5) $score += 15;
        if ($answers_title) $score += 10;

        $score = min(100, $score);

        if ($score < 50) {
            $recommendations[] = [
                'type' => 'direct_answers',
                'priority' => 'critical',
                'message' => 'Add clear, concise direct answers at the beginning of your content',
                'message_kr' => '콘텐츠 시작 부분에 명확하고 간결한 직접 답변을 추가하세요',
            ];
        }

        if ($list_count < 2) {
            $recommendations[] = [
                'type' => 'direct_answers',
                'priority' => 'medium',
                'message' => 'Add numbered or bulleted lists to structure key information',
                'message_kr' => '핵심 정보를 구조화하기 위해 번호 또는 글머리 기호 목록을 추가하세요',
            ];
        }

        return [
            'score' => $score,
            'has_definition' => (bool) $has_definition,
            'list_count' => $list_count,
            'short_paragraphs' => $short_paragraphs,
            'bold_terms' => $bold_count,
            'answers_title' => $answers_title,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Analyze conversational content
     */
    private function analyze_conversational_content($content) {
        $content_lower = strtolower($content);
        $score = 40; // Base score
        $recommendations = [];

        // Conversational patterns
        $patterns = [
            'you can' => 10,
            'you should' => 10,
            'you need' => 10,
            'you\'ll' => 5,
            'you\'re' => 5,
            'we recommend' => 10,
            'let\'s' => 5,
            'here\'s' => 10,
            'for example' => 10,
            'in other words' => 5,
            'think of' => 5,
            'imagine' => 5,
            'consider' => 5,
        ];

        $found_patterns = [];
        foreach ($patterns as $pattern => $points) {
            if (strpos($content_lower, $pattern) !== false) {
                $found_patterns[] = $pattern;
                $score += $points;
            }
        }

        // Check for second-person pronouns
        $you_count = substr_count($content_lower, ' you ') + substr_count($content_lower, 'your ');
        if ($you_count >= 10) $score += 10;
        elseif ($you_count >= 5) $score += 5;

        // Check for first-person plural (inclusive)
        $we_count = substr_count($content_lower, ' we ') + substr_count($content_lower, 'our ');
        if ($we_count >= 3) $score += 5;

        $score = min(100, $score);

        if ($score < 60) {
            $recommendations[] = [
                'type' => 'conversational_tone',
                'priority' => 'medium',
                'message' => 'Make content more conversational using "you" and direct address',
                'message_kr' => '"당신"과 같은 직접적인 표현을 사용하여 콘텐츠를 더 대화체로 만드세요',
            ];
        }

        return [
            'score' => $score,
            'patterns_found' => $found_patterns,
            'you_pronouns' => $you_count,
            'we_pronouns' => $we_count,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Analyze FAQ structure
     */
    private function analyze_faq_structure($content) {
        $score = 0;
        $recommendations = [];
        $faq_items = [];

        // Check for FAQ schema markup
        $has_faq_schema = strpos($content, 'FAQPage') !== false ||
                          strpos($content, 'yoast/faq-block') !== false ||
                          strpos($content, 'rank-math/faq-block') !== false;

        if ($has_faq_schema) $score += 40;

        // Check for Q&A patterns in headings
        preg_match_all('/<h[2-4][^>]*>([^<]*\?[^<]*)<\/h[2-4]>/i', $content, $question_headings);
        $question_heading_count = count($question_headings[0]);

        if ($question_heading_count >= 5) $score += 30;
        elseif ($question_heading_count >= 3) $score += 20;
        elseif ($question_heading_count >= 1) $score += 10;

        // Extract FAQ items for structured data
        foreach ($question_headings[1] as $question) {
            $faq_items[] = [
                'question' => trim(strip_tags($question)),
                'has_answer' => true, // Assume answer follows
            ];
        }

        // Check for accordion/toggle patterns
        $has_accordion = strpos($content, 'accordion') !== false ||
                         strpos($content, 'toggle') !== false ||
                         strpos($content, 'collapse') !== false;

        if ($has_accordion) $score += 15;

        // Check for "Q:" and "A:" patterns
        $qa_pattern_count = preg_match_all('/\b(Q|A):\s/i', $content);
        if ($qa_pattern_count >= 4) $score += 15;

        $score = min(100, $score);

        if (!$has_faq_schema && $question_heading_count >= 3) {
            $recommendations[] = [
                'type' => 'faq_structure',
                'priority' => 'high',
                'message' => 'Add FAQ schema markup to existing Q&A content',
                'message_kr' => '기존 Q&A 콘텐츠에 FAQ 스키마 마크업을 추가하세요',
            ];
        }

        if ($question_heading_count < 3) {
            $recommendations[] = [
                'type' => 'faq_structure',
                'priority' => 'medium',
                'message' => 'Add frequently asked questions section',
                'message_kr' => '자주 묻는 질문(FAQ) 섹션을 추가하세요',
            ];
        }

        return [
            'score' => $score,
            'has_faq_schema' => $has_faq_schema,
            'question_headings' => $question_heading_count,
            'has_accordion' => $has_accordion,
            'faq_items' => $faq_items,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Analyze citations and sources
     */
    private function analyze_citations($content) {
        $score = 40; // Base score
        $recommendations = [];
        $citations = [];

        // Check for external links
        preg_match_all('/<a[^>]+href=["\']https?:\/\/([^"\'\/]+)[^"\']*["\'][^>]*>/i', $content, $external_links);
        $external_domains = array_unique($external_links[1] ?? []);

        // Filter out social and common non-citation domains
        $ignore_domains = ['facebook.com', 'twitter.com', 'instagram.com', 'linkedin.com', 'youtube.com', 'amazon.com'];
        $citation_domains = array_filter($external_domains, function($domain) use ($ignore_domains) {
            foreach ($ignore_domains as $ignore) {
                if (strpos($domain, $ignore) !== false) return false;
            }
            return true;
        });

        $citation_count = count($citation_domains);
        $citations = array_values($citation_domains);

        if ($citation_count >= 5) $score += 30;
        elseif ($citation_count >= 3) $score += 20;
        elseif ($citation_count >= 1) $score += 10;

        // Check for authority domains
        $authority_domains = ['.gov', '.edu', 'wikipedia.org', 'britannica.com', 'nature.com', 'sciencedirect.com'];
        $has_authority = false;
        foreach ($citation_domains as $domain) {
            foreach ($authority_domains as $auth) {
                if (strpos($domain, $auth) !== false) {
                    $has_authority = true;
                    $score += 15;
                    break 2;
                }
            }
        }

        // Check for citation patterns
        $citation_patterns = [
            'according to',
            'research shows',
            'studies indicate',
            'experts say',
            'source:',
            'reference:',
        ];

        $citation_text_count = 0;
        $content_lower = strtolower($content);
        foreach ($citation_patterns as $pattern) {
            if (strpos($content_lower, $pattern) !== false) {
                $citation_text_count++;
            }
        }

        if ($citation_text_count >= 2) $score += 15;

        $score = min(100, $score);

        if ($citation_count < 3) {
            $recommendations[] = [
                'type' => 'citations',
                'priority' => 'medium',
                'message' => 'Add citations to authoritative sources to build trust',
                'message_kr' => '신뢰 구축을 위해 권위 있는 출처의 인용을 추가하세요',
            ];
        }

        if (!$has_authority) {
            $recommendations[] = [
                'type' => 'citations',
                'priority' => 'low',
                'message' => 'Include references to .gov, .edu, or academic sources',
                'message_kr' => '.gov, .edu 또는 학술 출처에 대한 참조를 포함하세요',
            ];
        }

        return [
            'score' => $score,
            'citation_count' => $citation_count,
            'citations' => $citations,
            'has_authority_sources' => $has_authority,
            'citation_text_patterns' => $citation_text_count,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Analyze entity recognition
     */
    private function analyze_entities($content) {
        $score = 50; // Base score
        $recommendations = [];
        $entities = [];

        // Check for proper nouns (capitalized words)
        preg_match_all('/\b[A-Z][a-z]+(?:\s+[A-Z][a-z]+)*\b/', strip_tags($content), $proper_nouns);
        $unique_proper_nouns = array_unique($proper_nouns[0] ?? []);

        // Filter common words
        $common_words = ['The', 'This', 'That', 'These', 'Those', 'Here', 'There', 'When', 'Where', 'Why', 'How', 'What'];
        $entities = array_filter($unique_proper_nouns, function($noun) use ($common_words) {
            return !in_array($noun, $common_words) && strlen($noun) > 2;
        });

        $entity_count = count($entities);

        if ($entity_count >= 15) $score += 30;
        elseif ($entity_count >= 10) $score += 20;
        elseif ($entity_count >= 5) $score += 10;

        // Check for schema.org entity markup
        $has_entity_schema = preg_match('/@type["\']?\s*:\s*["\']?(Person|Organization|Product|Place|Event)/i', $content);
        if ($has_entity_schema) $score += 20;

        $score = min(100, $score);

        if ($entity_count < 10) {
            $recommendations[] = [
                'type' => 'entities',
                'priority' => 'low',
                'message' => 'Include more specific entities (names, places, products)',
                'message_kr' => '더 많은 구체적 엔티티(이름, 장소, 제품)를 포함하세요',
            ];
        }

        return [
            'score' => $score,
            'entity_count' => $entity_count,
            'sample_entities' => array_slice(array_values($entities), 0, 10),
            'has_entity_schema' => (bool) $has_entity_schema,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Analyze featured snippet potential
     */
    private function analyze_snippet_potential($content) {
        $score = 0;
        $recommendations = [];
        $snippet_types = [];

        // Definition snippet potential
        if (preg_match('/^.{0,50}(is|are|refers to|means|defined as)\s/im', $content)) {
            $score += 25;
            $snippet_types[] = 'definition';
        }

        // List snippet potential
        preg_match_all('/<(ul|ol)[^>]*>.*?<\/\1>/is', $content, $lists);
        if (count($lists[0]) >= 1) {
            // Check if lists have 3+ items
            preg_match_all('/<li[^>]*>/i', $content, $list_items);
            if (count($list_items[0]) >= 3) {
                $score += 25;
                $snippet_types[] = 'list';
            }
        }

        // Table snippet potential
        if (preg_match('/<table[^>]*>.*?<\/table>/is', $content)) {
            $score += 25;
            $snippet_types[] = 'table';
        }

        // Step-by-step potential
        $step_patterns = ['step 1', 'step one', 'first,', 'first step', 'to start', 'begin by'];
        $has_steps = false;
        foreach ($step_patterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                $has_steps = true;
                break;
            }
        }
        if ($has_steps) {
            $score += 15;
            $snippet_types[] = 'how-to';
        }

        // Paragraph snippet (concise answer)
        preg_match('/<p[^>]*>(.{40,300})<\/p>/i', $content, $first_para);
        if (!empty($first_para[1]) && strlen(strip_tags($first_para[1])) <= 300) {
            $score += 10;
            $snippet_types[] = 'paragraph';
        }

        $score = min(100, $score);

        if (empty($snippet_types)) {
            $recommendations[] = [
                'type' => 'snippet_potential',
                'priority' => 'high',
                'message' => 'Format content for featured snippets (definitions, lists, or tables)',
                'message_kr' => '추천 스니펫을 위해 콘텐츠를 형식화하세요 (정의, 목록 또는 표)',
            ];
        }

        return [
            'score' => $score,
            'snippet_types' => $snippet_types,
            'has_lists' => count($lists[0] ?? []) > 0,
            'has_tables' => strpos($content, '<table') !== false,
            'has_steps' => $has_steps,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Analyze content structure
     */
    private function analyze_content_structure($content) {
        $score = 0;
        $recommendations = [];

        // Heading hierarchy
        preg_match_all('/<h([1-6])[^>]*>/i', $content, $headings);
        $heading_count = count($headings[0]);
        $heading_levels = $headings[1] ?? [];

        if ($heading_count >= 5) $score += 25;
        elseif ($heading_count >= 3) $score += 15;

        // Check for H2 usage
        $h2_count = count(array_filter($heading_levels, fn($h) => $h == '2'));
        if ($h2_count >= 3) $score += 15;

        // Paragraph structure
        preg_match_all('/<p[^>]*>/i', $content, $paragraphs);
        $paragraph_count = count($paragraphs[0]);

        // Short paragraphs are better for AI consumption
        $word_count = str_word_count(strip_tags($content));
        $avg_words_per_para = $paragraph_count > 0 ? $word_count / $paragraph_count : 0;

        if ($avg_words_per_para > 0 && $avg_words_per_para <= 100) $score += 20;
        elseif ($avg_words_per_para <= 150) $score += 10;

        // Content length
        if ($word_count >= 1000) $score += 15;
        elseif ($word_count >= 500) $score += 10;

        // Visual elements
        $has_images = preg_match('/<img[^>]+>/i', $content);
        if ($has_images) $score += 10;

        $has_lists = preg_match('/<(ul|ol)[^>]*>/i', $content);
        if ($has_lists) $score += 10;

        // Summary/TL;DR section
        $has_summary = preg_match('/summary|tldr|tl;dr|key takeaways|in short|bottom line/i', $content);
        if ($has_summary) $score += 5;

        $score = min(100, $score);

        if ($h2_count < 3) {
            $recommendations[] = [
                'type' => 'content_structure',
                'priority' => 'medium',
                'message' => 'Add more H2 headings to organize content sections',
                'message_kr' => '콘텐츠 섹션을 정리하기 위해 더 많은 H2 제목을 추가하세요',
            ];
        }

        if ($avg_words_per_para > 150) {
            $recommendations[] = [
                'type' => 'content_structure',
                'priority' => 'medium',
                'message' => 'Break long paragraphs into shorter ones for better readability',
                'message_kr' => '가독성을 위해 긴 단락을 더 짧게 나누세요',
            ];
        }

        return [
            'score' => $score,
            'heading_count' => $heading_count,
            'h2_count' => $h2_count,
            'paragraph_count' => $paragraph_count,
            'word_count' => $word_count,
            'avg_words_per_paragraph' => round($avg_words_per_para),
            'has_summary' => (bool) $has_summary,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Calculate grade from score
     */
    private function calculate_grade($score) {
        if ($score >= 90) return 'A+';
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'F';
    }

    /**
     * Enhance content for AEO
     */
    public function enhance_content_for_aeo($content) {
        if (!is_singular() || is_admin()) {
            return $content;
        }

        $post = get_queried_object();
        if (!($post instanceof WP_Post)) {
            return $content;
        }

        // Check if AEO enhancement is enabled for this post
        $aeo_enabled = get_post_meta($post->ID, '_wp_bulk_seo_aeo_enabled', true);
        if ($aeo_enabled === '0') {
            return $content;
        }

        // Add AI-friendly data attributes
        $content = $this->add_semantic_markup($content);

        // Add FAQ schema if not present and FAQ content detected
        if (!strpos($content, 'FAQPage') && strpos($content, '?') !== false) {
            $faq_items = $this->extract_faq_items($content);
            if (count($faq_items) >= 2 && $this->faq_generator) {
                $content .= $this->faq_generator->generate_faq_schema($faq_items);
            }
        }

        return $content;
    }

    /**
     * Add semantic markup
     */
    private function add_semantic_markup($content) {
        // Add data-content-type to sections
        $content = preg_replace(
            '/<(section|article|div)([^>]*)class="([^"]*)"/',
            '<$1$2class="$3" data-content-type="$3"',
            $content
        );

        return $content;
    }

    /**
     * Extract FAQ items from content
     */
    private function extract_faq_items($content) {
        $faq_items = [];

        // Find question headings and following paragraphs
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $headings = $xpath->query('//h2|//h3|//h4');

        foreach ($headings as $heading) {
            $text = trim($heading->textContent);
            if (strpos($text, '?') !== false) {
                $next = $heading->nextSibling;
                $answer = '';

                while ($next && in_array($next->nodeName, ['p', '#text', 'ul', 'ol'])) {
                    if ($next->nodeName === 'p' || $next->nodeName === 'ul' || $next->nodeName === 'ol') {
                        $answer .= trim($next->textContent) . ' ';
                    }
                    $next = $next->nextSibling;

                    // Limit answer length
                    if (strlen($answer) > 500) break;
                }

                if (!empty($answer)) {
                    $faq_items[] = [
                        'question' => $text,
                        'answer' => trim($answer),
                    ];
                }
            }
        }

        return array_slice($faq_items, 0, 10);
    }

    /**
     * Generate AI-optimized content suggestions
     */
    public function generate_suggestions($post_id) {
        if (!$this->ai_engine) {
            return ['error' => 'AI engine not available'];
        }

        $post = get_post($post_id);
        if (!$post) {
            return ['error' => 'Post not found'];
        }

        $analysis = $this->analyze($post);
        $suggestions = [];

        // Generate suggestions based on analysis
        foreach ($analysis['recommendations'] as $rec) {
            if ($rec['priority'] === 'critical' || $rec['priority'] === 'high') {
                // Use AI to generate specific improvements
                $suggestion = $this->ai_engine->generate_improvement_suggestion(
                    $rec['type'],
                    $post->post_content,
                    $post->post_title
                );

                if ($suggestion) {
                    $suggestions[] = [
                        'type' => $rec['type'],
                        'priority' => $rec['priority'],
                        'original_message' => $rec['message'],
                        'ai_suggestion' => $suggestion,
                    ];
                }
            }
        }

        return $suggestions;
    }

    /**
     * Add AEO meta box
     */
    public function add_aeo_meta_box() {
        $post_types = ['post', 'page'];

        foreach ($post_types as $post_type) {
            add_meta_box(
                'wp_bulk_seo_aeo',
                __('AEO Analysis', 'wp-bulk-seo'),
                [$this, 'render_aeo_meta_box'],
                $post_type,
                'normal',
                'default'
            );
        }
    }

    /**
     * Render AEO meta box
     */
    public function render_aeo_meta_box($post) {
        wp_nonce_field('wp_bulk_seo_aeo_nonce', 'wp_bulk_seo_aeo_nonce_field');

        $aeo_enabled = get_post_meta($post->ID, '_wp_bulk_seo_aeo_enabled', true);
        if ($aeo_enabled === '') $aeo_enabled = '1';

        $analysis = $this->analyze($post);
        ?>
        <div class="wp-bulk-seo-aeo-meta">
            <p>
                <label>
                    <input type="checkbox" name="wp_bulk_seo_aeo_enabled" value="1" <?php checked($aeo_enabled, '1'); ?>>
                    <?php esc_html_e('Enable AEO optimization for this content', 'wp-bulk-seo'); ?>
                </label>
            </p>

            <div class="aeo-score-display">
                <h4><?php esc_html_e('AEO Score', 'wp-bulk-seo'); ?>:
                    <span class="score-value grade-<?php echo esc_attr(strtolower($analysis['grade'])); ?>">
                        <?php echo esc_html($analysis['overall_score']); ?>/100 (<?php echo esc_html($analysis['grade']); ?>)
                    </span>
                </h4>
            </div>

            <?php if (!empty($analysis['recommendations'])): ?>
            <div class="aeo-recommendations">
                <h4><?php esc_html_e('Top Recommendations', 'wp-bulk-seo'); ?></h4>
                <ul>
                    <?php foreach (array_slice($analysis['recommendations'], 0, 5) as $rec): ?>
                    <li class="priority-<?php echo esc_attr($rec['priority']); ?>">
                        <span class="priority-badge"><?php echo esc_html(ucfirst($rec['priority'])); ?></span>
                        <?php echo esc_html($rec['message']); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>

        <style>
            .wp-bulk-seo-aeo-meta { padding: 10px 0; }
            .aeo-score-display { margin: 15px 0; padding: 10px; background: #f9f9f9; border-radius: 4px; }
            .score-value { font-weight: bold; }
            .score-value.grade-a, .score-value.grade-a\+ { color: #46b450; }
            .score-value.grade-b { color: #00a0d2; }
            .score-value.grade-c { color: #ffb900; }
            .score-value.grade-d, .score-value.grade-f { color: #dc3232; }
            .aeo-recommendations ul { margin: 10px 0; padding: 0; list-style: none; }
            .aeo-recommendations li { padding: 8px 10px; margin: 5px 0; background: #fff; border-left: 3px solid #ccc; }
            .aeo-recommendations li.priority-critical { border-left-color: #dc3232; }
            .aeo-recommendations li.priority-high { border-left-color: #ffb900; }
            .aeo-recommendations li.priority-medium { border-left-color: #00a0d2; }
            .aeo-recommendations li.priority-low { border-left-color: #46b450; }
            .priority-badge { display: inline-block; padding: 2px 6px; font-size: 10px; border-radius: 3px; background: #eee; margin-right: 8px; text-transform: uppercase; }
        </style>
        <?php
    }

    /**
     * Save AEO meta
     */
    public function save_aeo_meta($post_id) {
        if (!isset($_POST['wp_bulk_seo_aeo_nonce_field'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['wp_bulk_seo_aeo_nonce_field'], 'wp_bulk_seo_aeo_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $aeo_enabled = isset($_POST['wp_bulk_seo_aeo_enabled']) ? '1' : '0';
        update_post_meta($post_id, '_wp_bulk_seo_aeo_enabled', $aeo_enabled);
    }

    /**
     * Register REST routes
     */
    public function register_rest_routes() {
        register_rest_route('wp-bulk-seo/v1', '/aeo/analyze/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_analyze'],
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            },
        ]);

        register_rest_route('wp-bulk-seo/v1', '/aeo/suggestions/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_suggestions'],
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            },
        ]);
    }

    /**
     * REST endpoint: Analyze
     */
    public function rest_analyze($request) {
        $post_id = $request->get_param('id');
        return rest_ensure_response($this->analyze($post_id));
    }

    /**
     * REST endpoint: Suggestions
     */
    public function rest_suggestions($request) {
        $post_id = $request->get_param('id');
        return rest_ensure_response($this->generate_suggestions($post_id));
    }
}
