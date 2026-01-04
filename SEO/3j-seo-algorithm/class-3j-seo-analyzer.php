<?php
/**
 * 3J Labs SEO Page Analyzer
 *
 * Collects page data for SEO scoring analysis.
 * Integrates with WordPress to extract all relevant SEO signals.
 *
 * @package 3J_SEO_Algorithm
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class ThreeJ_SEO_Analyzer {

    /**
     * DOM Parser instance
     */
    private $dom;

    /**
     * URL being analyzed
     */
    private $url;

    /**
     * Raw HTML content
     */
    private $html;

    /**
     * Analyze a URL and return structured data
     *
     * @param string $url URL to analyze
     * @return array Page analysis data
     */
    public function analyze_url($url) {
        $this->url = $url;

        // Fetch HTML
        $response = wp_remote_get($url, [
            'timeout' => 30,
            'user-agent' => 'Mozilla/5.0 (compatible; 3J-SEO-Bot/1.0)',
        ]);

        if (is_wp_error($response)) {
            return [
                'error' => true,
                'message' => $response->get_error_message(),
            ];
        }

        $this->html = wp_remote_retrieve_body($response);
        $http_status = wp_remote_retrieve_response_code($response);

        // Parse HTML
        $this->dom = new DOMDocument();
        @$this->dom->loadHTML(mb_convert_encoding($this->html, 'HTML-ENTITIES', 'UTF-8'));

        return $this->extract_all_data($http_status);
    }

    /**
     * Analyze a WordPress post/page
     *
     * @param int $post_id Post ID to analyze
     * @return array Page analysis data
     */
    public function analyze_post($post_id) {
        $post = get_post($post_id);
        if (!$post) {
            return ['error' => true, 'message' => 'Post not found'];
        }

        $this->url = get_permalink($post_id);
        $this->html = apply_filters('the_content', $post->post_content);

        // Create full HTML structure
        $full_html = $this->build_post_html($post);

        $this->dom = new DOMDocument();
        @$this->dom->loadHTML(mb_convert_encoding($full_html, 'HTML-ENTITIES', 'UTF-8'));

        $data = $this->extract_all_data(200);
        $data['post_id'] = $post_id;
        $data['post_type'] = $post->post_type;
        $data['post_status'] = $post->post_status;

        return $data;
    }

    /**
     * Build HTML from WordPress post
     */
    private function build_post_html($post) {
        $html = '<!DOCTYPE html><html><head>';
        $html .= '<title>' . esc_html(get_the_title($post)) . '</title>';

        // Meta description
        $meta_desc = $this->get_meta_description($post->ID);
        if ($meta_desc) {
            $html .= '<meta name="description" content="' . esc_attr($meta_desc) . '">';
        }

        // Canonical
        $canonical = wp_get_canonical_url($post->ID);
        if ($canonical) {
            $html .= '<link rel="canonical" href="' . esc_url($canonical) . '">';
        }

        // Robots meta
        $robots = $this->get_robots_meta($post->ID);
        if ($robots) {
            $html .= '<meta name="robots" content="' . esc_attr($robots) . '">';
        }

        $html .= '</head><body>';
        $html .= '<h1>' . esc_html(get_the_title($post)) . '</h1>';
        $html .= apply_filters('the_content', $post->post_content);
        $html .= '</body></html>';

        return $html;
    }

    /**
     * Get meta description from various SEO plugins
     */
    private function get_meta_description($post_id) {
        // Yoast SEO
        $yoast_desc = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
        if ($yoast_desc) return $yoast_desc;

        // Rank Math
        $rankmath_desc = get_post_meta($post_id, 'rank_math_description', true);
        if ($rankmath_desc) return $rankmath_desc;

        // AIOSEO
        $aioseo_desc = get_post_meta($post_id, '_aioseo_description', true);
        if ($aioseo_desc) return $aioseo_desc;

        // Excerpt as fallback
        return get_the_excerpt($post_id);
    }

    /**
     * Get robots meta from various SEO plugins
     */
    private function get_robots_meta($post_id) {
        // Check Yoast
        $noindex = get_post_meta($post_id, '_yoast_wpseo_meta-robots-noindex', true);
        $nofollow = get_post_meta($post_id, '_yoast_wpseo_meta-robots-nofollow', true);

        if ($noindex === '1') {
            return $nofollow === '1' ? 'noindex, nofollow' : 'noindex';
        }
        if ($nofollow === '1') {
            return 'nofollow';
        }

        return 'index, follow';
    }

    /**
     * Extract all SEO-relevant data from the page
     */
    private function extract_all_data($http_status) {
        $data = [
            'url' => $this->url,
            'http_status' => $http_status,
            'analyzed_at' => current_time('mysql'),
        ];

        // Basic Meta
        $data = array_merge($data, $this->extract_meta_data());

        // Headings
        $data = array_merge($data, $this->extract_headings());

        // Content Analysis
        $data = array_merge($data, $this->extract_content_data());

        // Links
        $data = array_merge($data, $this->extract_links());

        // Images
        $data = array_merge($data, $this->extract_images());

        // Schema/Structured Data
        $data = array_merge($data, $this->extract_schema());

        // Technical
        $data = array_merge($data, $this->extract_technical());

        return $data;
    }

    /**
     * Extract meta data
     */
    private function extract_meta_data() {
        $xpath = new DOMXPath($this->dom);
        $data = [];

        // Title
        $title_nodes = $xpath->query('//title');
        $data['title'] = $title_nodes->length > 0 ? trim($title_nodes->item(0)->textContent) : '';

        // Meta Description
        $desc_nodes = $xpath->query('//meta[@name="description"]/@content');
        $data['meta_description'] = $desc_nodes->length > 0 ? $desc_nodes->item(0)->textContent : '';

        // Meta Keywords (legacy but still checked)
        $keywords_nodes = $xpath->query('//meta[@name="keywords"]/@content');
        $data['meta_keywords'] = $keywords_nodes->length > 0 ? $keywords_nodes->item(0)->textContent : '';

        // Canonical
        $canonical_nodes = $xpath->query('//link[@rel="canonical"]/@href');
        $data['canonical'] = $canonical_nodes->length > 0 ? $canonical_nodes->item(0)->textContent : '';

        // Robots
        $robots_nodes = $xpath->query('//meta[@name="robots"]/@content');
        $data['robots_meta'] = $robots_nodes->length > 0 ? $robots_nodes->item(0)->textContent : '';

        // Open Graph
        $data['og_title'] = $this->get_meta_property($xpath, 'og:title');
        $data['og_description'] = $this->get_meta_property($xpath, 'og:description');
        $data['og_image'] = $this->get_meta_property($xpath, 'og:image');
        $data['og_type'] = $this->get_meta_property($xpath, 'og:type');

        // Twitter Card
        $data['twitter_card'] = $this->get_meta_name($xpath, 'twitter:card');
        $data['twitter_title'] = $this->get_meta_name($xpath, 'twitter:title');

        // Viewport (mobile)
        $viewport_nodes = $xpath->query('//meta[@name="viewport"]/@content');
        $data['has_viewport'] = $viewport_nodes->length > 0;

        // Language
        $html_lang = $xpath->query('//html/@lang');
        $data['language'] = $html_lang->length > 0 ? $html_lang->item(0)->textContent : '';

        return $data;
    }

    /**
     * Extract heading structure
     */
    private function extract_headings() {
        $xpath = new DOMXPath($this->dom);
        $data = [];

        // H1
        $h1_nodes = $xpath->query('//h1');
        $data['h1_count'] = $h1_nodes->length;
        $data['h1'] = [];
        foreach ($h1_nodes as $node) {
            $data['h1'][] = trim($node->textContent);
        }

        // H2
        $h2_nodes = $xpath->query('//h2');
        $data['h2_count'] = $h2_nodes->length;
        $data['h2'] = [];
        foreach ($h2_nodes as $node) {
            $data['h2'][] = trim($node->textContent);
        }

        // H3
        $h3_nodes = $xpath->query('//h3');
        $data['h3_count'] = $h3_nodes->length;

        // H4-H6
        $data['h4_count'] = $xpath->query('//h4')->length;
        $data['h5_count'] = $xpath->query('//h5')->length;
        $data['h6_count'] = $xpath->query('//h6')->length;

        // Heading hierarchy validation
        $data['heading_hierarchy_valid'] = $this->validate_heading_hierarchy($data);

        return $data;
    }

    /**
     * Validate heading hierarchy
     */
    private function validate_heading_hierarchy($heading_data) {
        // Must have exactly one H1
        if ($heading_data['h1_count'] !== 1) return false;

        // If H3 exists, H2 must exist
        if ($heading_data['h3_count'] > 0 && $heading_data['h2_count'] === 0) return false;

        // If H4 exists, H3 must exist
        if ($heading_data['h4_count'] > 0 && $heading_data['h3_count'] === 0) return false;

        return true;
    }

    /**
     * Extract content data
     */
    private function extract_content_data() {
        $xpath = new DOMXPath($this->dom);
        $data = [];

        // Get body text content
        $body = $xpath->query('//body');
        $content = $body->length > 0 ? $body->item(0)->textContent : '';

        // Clean content (remove extra whitespace)
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);

        $data['content'] = $content;
        $data['word_count'] = str_word_count($content);
        $data['character_count'] = mb_strlen($content);

        // Sentence analysis
        $sentences = preg_split('/[.!?]+/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $data['sentence_count'] = count($sentences);
        $data['avg_sentence_length'] = $data['sentence_count'] > 0
            ? round($data['word_count'] / $data['sentence_count'], 1)
            : 0;

        // Paragraph count
        $data['paragraph_count'] = $xpath->query('//p')->length;

        // Lists
        $data['has_lists'] = $xpath->query('//ul | //ol')->length > 0;
        $data['list_count'] = $xpath->query('//ul | //ol')->length;

        // Tables
        $data['has_tables'] = $xpath->query('//table')->length > 0;
        $data['table_count'] = $xpath->query('//table')->length;

        // Video
        $data['has_video'] = $xpath->query('//video | //iframe[contains(@src, "youtube") or contains(@src, "vimeo")]')->length > 0;

        // Reading time (average 200 words per minute)
        $data['reading_time_minutes'] = ceil($data['word_count'] / 200);

        // Flesch Reading Ease estimation
        $data['flesch_reading_ease'] = $this->estimate_flesch_score($content);

        return $data;
    }

    /**
     * Estimate Flesch Reading Ease score
     */
    private function estimate_flesch_score($content) {
        $word_count = str_word_count($content);
        if ($word_count === 0) return 0;

        $sentences = preg_split('/[.!?]+/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $sentence_count = max(1, count($sentences));

        // Estimate syllables (rough approximation)
        $syllable_count = $this->count_syllables($content);

        // Flesch formula: 206.835 - 1.015*(words/sentences) - 84.6*(syllables/words)
        $asl = $word_count / $sentence_count;
        $asw = $syllable_count / $word_count;

        $score = 206.835 - (1.015 * $asl) - (84.6 * $asw);

        return max(0, min(100, round($score)));
    }

    /**
     * Count syllables in text (approximate)
     */
    private function count_syllables($text) {
        $text = strtolower($text);
        $words = str_word_count($text, 1);
        $syllables = 0;

        foreach ($words as $word) {
            // Count vowel groups as syllables
            $word = preg_replace('/[^a-z]/', '', $word);
            $word = preg_replace('/e$/', '', $word); // Silent e
            $vowel_count = preg_match_all('/[aeiouy]+/', $word, $matches);
            $syllables += max(1, $vowel_count);
        }

        return $syllables;
    }

    /**
     * Extract link data
     */
    private function extract_links() {
        $xpath = new DOMXPath($this->dom);
        $data = [];

        $links = $xpath->query('//a[@href]');
        $internal = [];
        $external = [];
        $anchors = [];

        $site_host = parse_url($this->url, PHP_URL_HOST);

        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $anchor = trim($link->textContent);
            $rel = $link->getAttribute('rel');

            // Skip empty/anchor-only links
            if (empty($href) || $href[0] === '#') continue;

            // Parse link
            $link_host = parse_url($href, PHP_URL_HOST);

            if ($link_host === null || $link_host === $site_host) {
                $internal[] = [
                    'href' => $href,
                    'anchor' => $anchor,
                    'nofollow' => strpos($rel, 'nofollow') !== false,
                ];
            } else {
                $external[] = [
                    'href' => $href,
                    'anchor' => $anchor,
                    'nofollow' => strpos($rel, 'nofollow') !== false,
                    'domain' => $link_host,
                ];
            }

            if (!empty($anchor)) {
                $anchors[] = $anchor;
            }
        }

        $data['internal_links'] = $internal;
        $data['internal_link_count'] = count($internal);
        $data['external_links'] = $external;
        $data['external_link_count'] = count($external);
        $data['anchor_texts'] = $anchors;
        $data['has_authority_external_links'] = $this->has_authority_links($external);

        return $data;
    }

    /**
     * Check if external links include authority sites
     */
    private function has_authority_links($external_links) {
        $authority_domains = [
            'wikipedia.org', 'gov', 'edu', 'bbc.com', 'nytimes.com',
            'theguardian.com', 'reuters.com', 'forbes.com', 'harvard.edu',
        ];

        foreach ($external_links as $link) {
            foreach ($authority_domains as $domain) {
                if (strpos($link['domain'], $domain) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Extract image data
     */
    private function extract_images() {
        $xpath = new DOMXPath($this->dom);
        $data = [];

        $images = $xpath->query('//img');
        $image_data = [];
        $missing_alt = 0;

        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            $alt = $img->getAttribute('alt');
            $loading = $img->getAttribute('loading');
            $width = $img->getAttribute('width');
            $height = $img->getAttribute('height');

            $image_data[] = [
                'src' => $src,
                'alt' => $alt,
                'has_alt' => !empty($alt),
                'lazy_loading' => $loading === 'lazy',
                'has_dimensions' => !empty($width) && !empty($height),
            ];

            if (empty($alt)) {
                $missing_alt++;
            }
        }

        $data['images'] = $image_data;
        $data['image_count'] = count($image_data);
        $data['images_missing_alt'] = $missing_alt;
        $data['images_alt_ratio'] = count($image_data) > 0
            ? round((count($image_data) - $missing_alt) / count($image_data) * 100, 1)
            : 100;

        return $data;
    }

    /**
     * Extract structured data (Schema.org)
     */
    private function extract_schema() {
        $xpath = new DOMXPath($this->dom);
        $data = [];

        // JSON-LD Schema
        $json_ld_nodes = $xpath->query('//script[@type="application/ld+json"]');
        $schema_types = [];
        $all_schemas = [];

        foreach ($json_ld_nodes as $node) {
            $json = json_decode($node->textContent, true);
            if ($json) {
                $all_schemas[] = $json;
                if (isset($json['@type'])) {
                    if (is_array($json['@type'])) {
                        $schema_types = array_merge($schema_types, $json['@type']);
                    } else {
                        $schema_types[] = $json['@type'];
                    }
                }
                // Check for @graph structure
                if (isset($json['@graph'])) {
                    foreach ($json['@graph'] as $item) {
                        if (isset($item['@type'])) {
                            $schema_types[] = $item['@type'];
                        }
                    }
                }
            }
        }

        $data['schema_types'] = array_unique($schema_types);
        $data['schema_count'] = count($all_schemas);
        $data['has_schema'] = count($schema_types) > 0;
        $data['schema_valid'] = $this->validate_schema($all_schemas);

        // Check specific schema types
        $data['has_article_schema'] = in_array('Article', $schema_types) || in_array('BlogPosting', $schema_types) || in_array('NewsArticle', $schema_types);
        $data['has_product_schema'] = in_array('Product', $schema_types);
        $data['has_faq_schema'] = in_array('FAQPage', $schema_types);
        $data['has_howto_schema'] = in_array('HowTo', $schema_types);
        $data['has_breadcrumb_schema'] = in_array('BreadcrumbList', $schema_types);
        $data['has_local_business_schema'] = in_array('LocalBusiness', $schema_types) || in_array('Organization', $schema_types);
        $data['has_review_schema'] = in_array('Review', $schema_types) || in_array('AggregateRating', $schema_types);
        $data['has_person_schema'] = in_array('Person', $schema_types);
        $data['has_author_schema'] = $data['has_person_schema'];
        $data['has_date_schema'] = $this->has_date_in_schema($all_schemas);

        // Rich results potential
        $data['rich_results_eligible'] = $data['has_faq_schema'] || $data['has_howto_schema'] ||
            $data['has_product_schema'] || $data['has_review_schema'];

        return $data;
    }

    /**
     * Validate schema structure
     */
    private function validate_schema($schemas) {
        foreach ($schemas as $schema) {
            // Must have @context and @type
            if (!isset($schema['@context']) && !isset($schema['@graph'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if schema has date information
     */
    private function has_date_in_schema($schemas) {
        foreach ($schemas as $schema) {
            if (isset($schema['datePublished']) || isset($schema['dateModified'])) {
                return true;
            }
            if (isset($schema['@graph'])) {
                foreach ($schema['@graph'] as $item) {
                    if (isset($item['datePublished']) || isset($item['dateModified'])) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Extract technical SEO data
     */
    private function extract_technical() {
        $data = [];

        // HTTPS check
        $data['is_https'] = parse_url($this->url, PHP_URL_SCHEME) === 'https';

        // Mobile viewport
        $xpath = new DOMXPath($this->dom);
        $viewport = $xpath->query('//meta[@name="viewport"]/@content');
        $data['has_mobile_viewport'] = $viewport->length > 0;
        $data['viewport_content'] = $viewport->length > 0 ? $viewport->item(0)->textContent : '';

        // Amp check
        $data['is_amp'] = $xpath->query('//html[@amp or @\u26a1]')->length > 0;

        // Hreflang
        $hreflang = $xpath->query('//link[@rel="alternate"][@hreflang]');
        $data['has_hreflang'] = $hreflang->length > 0;
        $data['hreflang_count'] = $hreflang->length;

        // Page size
        $data['page_size_bytes'] = strlen($this->html);
        $data['page_size_kb'] = round($data['page_size_bytes'] / 1024, 2);

        // Resource counts
        $data['script_count'] = $xpath->query('//script')->length;
        $data['stylesheet_count'] = $xpath->query('//link[@rel="stylesheet"]')->length;
        $data['inline_style_count'] = $xpath->query('//style')->length;

        // Check for lazy loading
        $data['uses_lazy_loading'] = $xpath->query('//*[@loading="lazy"]')->length > 0;

        // Check for preload/prefetch
        $data['has_preload'] = $xpath->query('//link[@rel="preload"]')->length > 0;
        $data['has_prefetch'] = $xpath->query('//link[@rel="prefetch"]')->length > 0;

        return $data;
    }

    /**
     * Helper: Get meta property value
     */
    private function get_meta_property($xpath, $property) {
        $nodes = $xpath->query('//meta[@property="' . $property . '"]/@content');
        return $nodes->length > 0 ? $nodes->item(0)->textContent : '';
    }

    /**
     * Helper: Get meta name value
     */
    private function get_meta_name($xpath, $name) {
        $nodes = $xpath->query('//meta[@name="' . $name . '"]/@content');
        return $nodes->length > 0 ? $nodes->item(0)->textContent : '';
    }
}
