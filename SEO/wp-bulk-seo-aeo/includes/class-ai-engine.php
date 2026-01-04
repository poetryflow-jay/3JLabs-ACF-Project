<?php
/**
 * WP Bulk SEO & AEO - AI Engine
 *
 * AI-powered content optimization using OpenAI, Anthropic Claude, or local models.
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_AI_Engine {

    /**
     * API provider
     */
    private $provider;

    /**
     * API key
     */
    private $api_key;

    /**
     * Model to use
     */
    private $model;

    /**
     * Constructor
     */
    public function __construct() {
        $this->provider = get_option('wp_bulk_seo_aeo_ai_provider', 'openai');
        $this->api_key = get_option('wp_bulk_seo_aeo_ai_api_key', '');
        $this->model = get_option('wp_bulk_seo_aeo_ai_model', 'gpt-4o-mini');
    }

    /**
     * Check if AI is configured
     */
    public function is_configured() {
        return !empty($this->api_key);
    }

    /**
     * Optimize title for SEO
     *
     * @param string $current_title Current title
     * @param string $focus_keyword Focus keyword
     * @return string|null Optimized title or null on failure
     */
    public function optimize_title($current_title, $focus_keyword = '') {
        if (!$this->is_configured()) {
            return null;
        }

        $prompt = $this->build_title_prompt($current_title, $focus_keyword);
        return $this->call_api($prompt, 'title_optimization');
    }

    /**
     * Optimize meta description
     *
     * @param string $content Content or current description
     * @param string $focus_keyword Focus keyword
     * @return string|null Optimized description or null on failure
     */
    public function optimize_meta_description($content, $focus_keyword = '') {
        if (!$this->is_configured()) {
            return null;
        }

        $prompt = $this->build_meta_description_prompt($content, $focus_keyword);
        return $this->call_api($prompt, 'meta_optimization');
    }

    /**
     * Generate FAQ schema content
     *
     * @param string $content Page content
     * @param int $count Number of FAQs to generate
     * @return array|null FAQ data or null on failure
     */
    public function generate_faqs($content, $count = 5) {
        if (!$this->is_configured()) {
            return null;
        }

        $prompt = $this->build_faq_prompt($content, $count);
        $response = $this->call_api($prompt, 'faq_generation');

        if ($response) {
            return $this->parse_faq_response($response);
        }

        return null;
    }

    /**
     * Suggest focus keywords
     *
     * @param string $content Page content
     * @param string $title Page title
     * @return array|null Suggested keywords or null on failure
     */
    public function suggest_keywords($content, $title = '') {
        if (!$this->is_configured()) {
            return null;
        }

        $prompt = $this->build_keyword_prompt($content, $title);
        $response = $this->call_api($prompt, 'keyword_suggestion');

        if ($response) {
            return $this->parse_keyword_response($response);
        }

        return null;
    }

    /**
     * Generate content improvements
     *
     * @param string $content Current content
     * @param array $issues SEO issues to address
     * @return string|null Improved content suggestions or null on failure
     */
    public function generate_improvements($content, $issues = []) {
        if (!$this->is_configured()) {
            return null;
        }

        $prompt = $this->build_improvement_prompt($content, $issues);
        return $this->call_api($prompt, 'content_improvement');
    }

    /**
     * Generate structured data suggestions
     *
     * @param array $page_data Page analysis data
     * @return array|null Schema suggestions or null on failure
     */
    public function suggest_schema($page_data) {
        if (!$this->is_configured()) {
            return null;
        }

        $prompt = $this->build_schema_prompt($page_data);
        $response = $this->call_api($prompt, 'schema_suggestion');

        if ($response) {
            return $this->parse_schema_response($response);
        }

        return null;
    }

    /**
     * Build title optimization prompt
     */
    private function build_title_prompt($title, $keyword) {
        $prompt = "You are an SEO expert. Optimize the following title for search engines.\n\n";
        $prompt .= "Current title: \"$title\"\n";

        if ($keyword) {
            $prompt .= "Focus keyword: \"$keyword\"\n";
        }

        $prompt .= "\nRequirements:\n";
        $prompt .= "- Keep it under 60 characters\n";
        $prompt .= "- Place the keyword near the beginning if provided\n";
        $prompt .= "- Make it compelling and click-worthy\n";
        $prompt .= "- Maintain the original meaning\n\n";
        $prompt .= "Return ONLY the optimized title, nothing else.";

        return $prompt;
    }

    /**
     * Build meta description prompt
     */
    private function build_meta_description_prompt($content, $keyword) {
        $excerpt = wp_trim_words($content, 100, '...');

        $prompt = "You are an SEO expert. Create an optimized meta description.\n\n";
        $prompt .= "Content summary: \"$excerpt\"\n";

        if ($keyword) {
            $prompt .= "Focus keyword: \"$keyword\"\n";
        }

        $prompt .= "\nRequirements:\n";
        $prompt .= "- Keep it between 150-160 characters\n";
        $prompt .= "- Include the focus keyword naturally if provided\n";
        $prompt .= "- Include a call-to-action\n";
        $prompt .= "- Make it compelling and informative\n\n";
        $prompt .= "Return ONLY the meta description, nothing else.";

        return $prompt;
    }

    /**
     * Build FAQ generation prompt
     */
    private function build_faq_prompt($content, $count) {
        $excerpt = wp_trim_words($content, 500, '...');

        $prompt = "You are an SEO expert specializing in FAQ schema optimization.\n\n";
        $prompt .= "Based on the following content, generate $count relevant FAQ questions and answers.\n\n";
        $prompt .= "Content: \"$excerpt\"\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Questions should be natural, as users would ask\n";
        $prompt .= "- Answers should be concise but comprehensive (50-150 words)\n";
        $prompt .= "- Target featured snippet opportunities\n";
        $prompt .= "- Include relevant keywords naturally\n\n";
        $prompt .= "Format as JSON array:\n";
        $prompt .= "[{\"question\": \"...\", \"answer\": \"...\"}]";

        return $prompt;
    }

    /**
     * Build keyword suggestion prompt
     */
    private function build_keyword_prompt($content, $title) {
        $excerpt = wp_trim_words($content, 300, '...');

        $prompt = "You are an SEO keyword researcher.\n\n";
        $prompt .= "Title: \"$title\"\n";
        $prompt .= "Content: \"$excerpt\"\n\n";
        $prompt .= "Suggest 5 focus keywords for this content.\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Include a mix of short-tail and long-tail keywords\n";
        $prompt .= "- Focus on search intent\n";
        $prompt .= "- Consider ranking difficulty\n\n";
        $prompt .= "Format as JSON array:\n";
        $prompt .= "[{\"keyword\": \"...\", \"type\": \"short-tail|long-tail\", \"intent\": \"informational|transactional|navigational\"}]";

        return $prompt;
    }

    /**
     * Build content improvement prompt
     */
    private function build_improvement_prompt($content, $issues) {
        $excerpt = wp_trim_words($content, 500, '...');
        $issues_text = implode(', ', array_column($issues, 'label'));

        $prompt = "You are an SEO content optimizer.\n\n";
        $prompt .= "Current content: \"$excerpt\"\n\n";
        $prompt .= "SEO issues to address: $issues_text\n\n";
        $prompt .= "Provide specific, actionable recommendations to improve this content for SEO.\n";
        $prompt .= "Focus on:\n";
        $prompt .= "- Content structure improvements\n";
        $prompt .= "- Keyword optimization opportunities\n";
        $prompt .= "- User engagement enhancements\n";
        $prompt .= "- E-E-A-T improvements\n";

        return $prompt;
    }

    /**
     * Build schema suggestion prompt
     */
    private function build_schema_prompt($page_data) {
        $prompt = "You are a structured data expert.\n\n";
        $prompt .= "Page type: " . ($page_data['post_type'] ?? 'page') . "\n";
        $prompt .= "Title: " . ($page_data['title'] ?? '') . "\n";
        $prompt .= "Has product content: " . (($page_data['has_product_content'] ?? false) ? 'yes' : 'no') . "\n";
        $prompt .= "Has how-to content: " . (($page_data['has_howto_content'] ?? false) ? 'yes' : 'no') . "\n";
        $prompt .= "Has FAQ content: " . (($page_data['has_faq_content'] ?? false) ? 'yes' : 'no') . "\n\n";
        $prompt .= "Recommend appropriate Schema.org types for this page.\n";
        $prompt .= "Format as JSON array of schema type names: [\"Article\", \"FAQPage\", ...]";

        return $prompt;
    }

    /**
     * Call AI API
     *
     * @param string $prompt The prompt to send
     * @param string $task_type Type of task for caching/logging
     * @return string|null Response or null on failure
     */
    private function call_api($prompt, $task_type = 'general') {
        switch ($this->provider) {
            case 'openai':
                return $this->call_openai($prompt);
            case 'anthropic':
                return $this->call_anthropic($prompt);
            default:
                return null;
        }
    }

    /**
     * Call OpenAI API
     */
    private function call_openai($prompt) {
        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
            ],
            'body' => wp_json_encode([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ]),
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            error_log('WP Bulk SEO - OpenAI API Error: ' . $response->get_error_message());
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['choices'][0]['message']['content'])) {
            return trim($body['choices'][0]['message']['content']);
        }

        return null;
    }

    /**
     * Call Anthropic API
     */
    private function call_anthropic($prompt) {
        $response = wp_remote_post('https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key' => $this->api_key,
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01',
            ],
            'body' => wp_json_encode([
                'model' => $this->model ?: 'claude-3-haiku-20240307',
                'max_tokens' => 1000,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]),
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            error_log('WP Bulk SEO - Anthropic API Error: ' . $response->get_error_message());
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['content'][0]['text'])) {
            return trim($body['content'][0]['text']);
        }

        return null;
    }

    /**
     * Parse FAQ response
     */
    private function parse_faq_response($response) {
        // Try to extract JSON from response
        if (preg_match('/\[.*\]/s', $response, $matches)) {
            $faqs = json_decode($matches[0], true);
            if (is_array($faqs)) {
                return $faqs;
            }
        }

        // Try direct JSON decode
        $faqs = json_decode($response, true);
        if (is_array($faqs)) {
            return $faqs;
        }

        return null;
    }

    /**
     * Parse keyword response
     */
    private function parse_keyword_response($response) {
        // Try to extract JSON from response
        if (preg_match('/\[.*\]/s', $response, $matches)) {
            $keywords = json_decode($matches[0], true);
            if (is_array($keywords)) {
                return $keywords;
            }
        }

        // Try direct JSON decode
        $keywords = json_decode($response, true);
        if (is_array($keywords)) {
            return $keywords;
        }

        return null;
    }

    /**
     * Parse schema response
     */
    private function parse_schema_response($response) {
        // Try to extract JSON array from response
        if (preg_match('/\[.*\]/s', $response, $matches)) {
            $schemas = json_decode($matches[0], true);
            if (is_array($schemas)) {
                return $schemas;
            }
        }

        return null;
    }

    /**
     * Get available models for provider
     */
    public function get_available_models() {
        switch ($this->provider) {
            case 'openai':
                return [
                    'gpt-4o' => 'GPT-4o (Best quality)',
                    'gpt-4o-mini' => 'GPT-4o Mini (Fast & affordable)',
                    'gpt-4-turbo' => 'GPT-4 Turbo',
                    'gpt-3.5-turbo' => 'GPT-3.5 Turbo (Economy)',
                ];
            case 'anthropic':
                return [
                    'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet (Best quality)',
                    'claude-3-5-haiku-20241022' => 'Claude 3.5 Haiku (Fast)',
                    'claude-3-opus-20240229' => 'Claude 3 Opus (Most capable)',
                ];
            default:
                return [];
        }
    }

    /**
     * Test API connection
     */
    public function test_connection() {
        $test_prompt = "Respond with 'OK' if you receive this message.";
        $response = $this->call_api($test_prompt, 'connection_test');

        return $response !== null;
    }
}
