<?php
/**
 * WP Bulk SEO - AI Provider
 *
 * Handles AI API integrations for content optimization
 *
 * @package WP_Bulk_SEO
 * @subpackage API
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AI Provider Class
 */
class WP_Bulk_SEO_AI_Provider {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Current provider
     */
    private $provider = 'openai';

    /**
     * API Keys
     */
    private $api_keys = [];

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $aeo_settings = get_option('wp_bulk_seo_aeo', []);
        $this->provider = $aeo_settings['ai_provider'] ?? 'openai';

        $this->api_keys = [
            'openai' => get_option('wp_bulk_seo_openai_api_key', ''),
            'anthropic' => get_option('wp_bulk_seo_anthropic_api_key', ''),
        ];
    }

    /**
     * Check if AI is available
     */
    public function is_available() {
        return !empty($this->api_keys[$this->provider]);
    }

    /**
     * Generate title suggestions
     */
    public function generate_title_suggestions($post, $count = 3) {
        if (!$this->is_available()) {
            return $this->generate_fallback_titles($post);
        }

        $focus_keyword = get_post_meta($post->ID, '_wp_bulk_seo_focus_keyword', true);
        $content_excerpt = wp_trim_words(strip_tags($post->post_content), 200);

        $prompt = "Generate $count SEO-optimized title suggestions for the following content.\n\n";
        $prompt .= "Current title: {$post->post_title}\n";
        if ($focus_keyword) {
            $prompt .= "Focus keyword: $focus_keyword\n";
        }
        $prompt .= "Content: $content_excerpt\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Each title should be 50-60 characters\n";
        $prompt .= "- Include the focus keyword naturally\n";
        $prompt .= "- Make titles compelling and click-worthy\n";
        $prompt .= "- Use power words when appropriate\n\n";
        $prompt .= "Return only the titles, one per line, without numbering or explanations.";

        $response = $this->call_ai($prompt);

        if (is_wp_error($response)) {
            return $this->generate_fallback_titles($post);
        }

        $titles = array_filter(array_map('trim', explode("\n", $response)));
        return array_slice($titles, 0, $count);
    }

    /**
     * Generate description suggestions
     */
    public function generate_description_suggestions($post, $count = 3) {
        if (!$this->is_available()) {
            return $this->generate_fallback_descriptions($post);
        }

        $focus_keyword = get_post_meta($post->ID, '_wp_bulk_seo_focus_keyword', true);
        $content_excerpt = wp_trim_words(strip_tags($post->post_content), 300);

        $prompt = "Generate $count SEO-optimized meta description suggestions for the following content.\n\n";
        $prompt .= "Title: {$post->post_title}\n";
        if ($focus_keyword) {
            $prompt .= "Focus keyword: $focus_keyword\n";
        }
        $prompt .= "Content: $content_excerpt\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Each description should be 150-160 characters\n";
        $prompt .= "- Include the focus keyword naturally in the first description\n";
        $prompt .= "- Make descriptions compelling with a clear value proposition\n";
        $prompt .= "- Include a call-to-action when appropriate\n\n";
        $prompt .= "Return only the descriptions, one per line, without numbering or explanations.";

        $response = $this->call_ai($prompt);

        if (is_wp_error($response)) {
            return $this->generate_fallback_descriptions($post);
        }

        $descriptions = array_filter(array_map('trim', explode("\n", $response)));
        return array_slice($descriptions, 0, $count);
    }

    /**
     * Generate FAQ from content
     */
    public function generate_faq($content, $count = 5) {
        if (!$this->is_available()) {
            return [];
        }

        $content_excerpt = wp_trim_words(strip_tags($content), 500);

        $prompt = "Based on the following content, generate $count frequently asked questions with answers.\n\n";
        $prompt .= "Content: $content_excerpt\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Questions should be natural and what users would actually search for\n";
        $prompt .= "- Answers should be concise but complete (2-3 sentences)\n";
        $prompt .= "- Include a mix of how, what, why questions\n";
        $prompt .= "- Make answers conversational for AI search engines\n\n";
        $prompt .= "Format each FAQ as:\n";
        $prompt .= "Q: [question]\n";
        $prompt .= "A: [answer]\n\n";

        $response = $this->call_ai($prompt);

        if (is_wp_error($response)) {
            return [];
        }

        return $this->parse_faq_response($response);
    }

    /**
     * Optimize content for AEO
     */
    public function optimize_for_aeo($content, $target_questions = []) {
        if (!$this->is_available()) {
            return $content;
        }

        $content_excerpt = wp_trim_words(strip_tags($content), 1000);

        $prompt = "Analyze the following content and suggest improvements for AI search engine optimization (AEO).\n\n";
        $prompt .= "Content: $content_excerpt\n\n";

        if (!empty($target_questions)) {
            $prompt .= "Target questions to answer:\n";
            foreach ($target_questions as $q) {
                $prompt .= "- $q\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "Provide specific suggestions to:\n";
        $prompt .= "1. Make content more conversational and directly answer questions\n";
        $prompt .= "2. Add structured data opportunities (lists, tables, definitions)\n";
        $prompt .= "3. Improve citation-worthiness with facts and statistics\n";
        $prompt .= "4. Enhance for featured snippets\n\n";
        $prompt .= "Format: Return a JSON object with keys 'suggestions' (array of specific improvements) and 'priority' (high/medium/low for each).";

        $response = $this->call_ai($prompt);

        if (is_wp_error($response)) {
            return [];
        }

        $json = json_decode($response, true);
        return $json ?: [];
    }

    /**
     * Generate structured data content
     */
    public function suggest_structured_content($topic, $type = 'how-to') {
        if (!$this->is_available()) {
            return [];
        }

        $prompts = [
            'how-to' => "Generate a step-by-step guide for: $topic\n\nFormat as numbered steps with clear, actionable instructions. Each step should be one sentence.",
            'faq' => "Generate 5 common questions and answers about: $topic\n\nFormat as Q: and A: pairs.",
            'definition' => "Provide a concise definition and explanation of: $topic\n\nStart with 'X is...' format, keep it under 50 words for the definition, then add 2-3 sentences of context.",
            'comparison' => "Create a comparison for: $topic\n\nFormat as a bullet list highlighting key differences and similarities.",
        ];

        $prompt = $prompts[$type] ?? $prompts['how-to'];

        $response = $this->call_ai($prompt);

        if (is_wp_error($response)) {
            return [];
        }

        return [
            'type' => $type,
            'content' => $response,
        ];
    }

    /**
     * Call AI API
     */
    private function call_ai($prompt, $max_tokens = 1000) {
        if ($this->provider === 'anthropic') {
            return $this->call_anthropic($prompt, $max_tokens);
        }
        return $this->call_openai($prompt, $max_tokens);
    }

    /**
     * Call OpenAI API
     */
    private function call_openai($prompt, $max_tokens = 1000) {
        $api_key = $this->api_keys['openai'];

        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('OpenAI API key not configured', 'wp-bulk-seo'));
        }

        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
            'timeout' => 60,
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an SEO expert specializing in content optimization for both traditional search engines and AI-powered search (AEO). Provide practical, actionable suggestions.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => $max_tokens,
                'temperature' => 0.7,
            ]),
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['error'])) {
            return new WP_Error('api_error', $body['error']['message'] ?? __('OpenAI API error', 'wp-bulk-seo'));
        }

        return $body['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Call Anthropic API
     */
    private function call_anthropic($prompt, $max_tokens = 1000) {
        $api_key = $this->api_keys['anthropic'];

        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Anthropic API key not configured', 'wp-bulk-seo'));
        }

        $response = wp_remote_post('https://api.anthropic.com/v1/messages', [
            'timeout' => 60,
            'headers' => [
                'x-api-key' => $api_key,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'model' => 'claude-3-haiku-20240307',
                'max_tokens' => $max_tokens,
                'system' => 'You are an SEO expert specializing in content optimization for both traditional search engines and AI-powered search (AEO). Provide practical, actionable suggestions.',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]),
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['error'])) {
            return new WP_Error('api_error', $body['error']['message'] ?? __('Anthropic API error', 'wp-bulk-seo'));
        }

        return $body['content'][0]['text'] ?? '';
    }

    /**
     * Parse FAQ response
     */
    private function parse_faq_response($response) {
        $faqs = [];
        $lines = explode("\n", $response);
        $current_q = '';

        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match('/^Q:\s*(.+)$/i', $line, $matches)) {
                $current_q = $matches[1];
            } elseif (preg_match('/^A:\s*(.+)$/i', $line, $matches) && $current_q) {
                $faqs[] = [
                    'question' => $current_q,
                    'answer' => $matches[1],
                ];
                $current_q = '';
            }
        }

        return $faqs;
    }

    /**
     * Generate fallback titles
     */
    private function generate_fallback_titles($post) {
        $title = $post->post_title;
        $focus_keyword = get_post_meta($post->ID, '_wp_bulk_seo_focus_keyword', true);

        $templates = [
            '%s: Complete Guide [%year%]',
            'How to %s - Step by Step',
            '%s: Everything You Need to Know',
            'The Ultimate %s Guide',
            '%s Tips & Best Practices',
        ];

        $titles = [];
        foreach ($templates as $template) {
            $new_title = str_replace(
                ['%s', '%year%'],
                [$focus_keyword ?: $title, date('Y')],
                $template
            );

            if (strlen($new_title) <= 60) {
                $titles[] = $new_title;
            }
        }

        return array_slice($titles, 0, 3);
    }

    /**
     * Generate fallback descriptions
     */
    private function generate_fallback_descriptions($post) {
        $title = $post->post_title;
        $excerpt = wp_trim_words(strip_tags($post->post_content), 20);
        $focus_keyword = get_post_meta($post->ID, '_wp_bulk_seo_focus_keyword', true);

        $templates = [
            'Learn about %s. %excerpt% Read more to discover practical tips and insights.',
            'Discover everything you need to know about %s. Expert advice and actionable tips inside.',
            '%s explained simply. Get practical guidance and proven strategies in this comprehensive guide.',
        ];

        $descriptions = [];
        foreach ($templates as $template) {
            $desc = str_replace(
                ['%s', '%excerpt%'],
                [$focus_keyword ?: $title, $excerpt],
                $template
            );

            // Truncate to 160 characters
            if (strlen($desc) > 160) {
                $desc = substr($desc, 0, 157) . '...';
            }

            $descriptions[] = $desc;
        }

        return $descriptions;
    }

    /**
     * Get provider name
     */
    public function get_provider() {
        return $this->provider;
    }

    /**
     * Set provider
     */
    public function set_provider($provider) {
        if (in_array($provider, ['openai', 'anthropic'])) {
            $this->provider = $provider;
        }
    }
}
