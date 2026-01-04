<?php
/**
 * WP Bulk SEO - Page Analyzer
 *
 * Collects all SEO-relevant data from pages for scoring analysis.
 * Integrates with WordPress, PageSpeed API, and Search Console API.
 *
 * @package WP_Bulk_SEO
 * @subpackage Algorithm
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Analyzer {

    /**
     * DOM Parser instance
     */
    private $dom;

    /**
     * XPath instance
     */
    private $xpath;

    /**
     * URL being analyzed
     */
    private $url;

    /**
     * Raw HTML content
     */
    private $html;

    /**
     * PageSpeed API key
     */
    private $pagespeed_api_key;

    /**
     * Search Console credentials
     */
    private $search_console_credentials;

    /**
     * Cache duration in seconds
     */
    private const CACHE_DURATION = 3600; // 1 hour

    /**
     * Constructor
     */
    public function __construct() {
        $this->pagespeed_api_key = get_option('wp_bulk_seo_pagespeed_api_key', '');
        $this->search_console_credentials = get_option('wp_bulk_seo_search_console_credentials', []);
    }

    /**
     * Analyze a URL with optional API enrichment
     *
     * @param string $url URL to analyze
     * @param array $options Analysis options
     * @return array Comprehensive analysis data
     */
    public function analyze_url($url, $options = []) {
        $this->url = $url;

        // Check cache first
        $cache_key = 'wp_bulk_seo_analysis_' . md5($url);
        $cached = get_transient($cache_key);

        if ($cached !== false && empty($options['force_refresh'])) {
            return $cached;
        }

        // Fetch HTML
        $response = wp_remote_get($url, [
            'timeout' => 30,
            'user-agent' => 'Mozilla/5.0 (compatible; WP-Bulk-SEO-Bot/2.0; +https://3jlabs.com)',
            'sslverify' => true,
        ]);

        if (is_wp_error($response)) {
            return [
                'error' => true,
                'message' => $response->get_error_message(),
                'url' => $url,
            ];
        }

        $this->html = wp_remote_retrieve_body($response);
        $http_status = wp_remote_retrieve_response_code($response);
        $headers = wp_remote_retrieve_headers($response);

        // Parse HTML
        $this->dom = new DOMDocument();
        libxml_use_internal_errors(true);
        @$this->dom->loadHTML(mb_convert_encoding($this->html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $this->xpath = new DOMXPath($this->dom);

        // Extract all data
        $data = $this->extract_all_data($http_status, $headers);

        // Enrich with external APIs if enabled
        if (!empty($options['use_pagespeed_api']) && !empty($this->pagespeed_api_key)) {
            $data['pagespeed_data'] = $this->fetch_pagespeed_data($url);
        }

        if (!empty($options['use_search_console']) && !empty($this->search_console_credentials)) {
            $data['search_console_data'] = $this->fetch_search_console_data($url);
        }

        // Cache result
        set_transient($cache_key, $data, self::CACHE_DURATION);

        return $data;
    }

    /**
     * Analyze a WordPress post/page
     *
     * @param int|WP_Post $post Post ID or object
     * @param array $options Analysis options
     * @return array Analysis data
     */
    public function analyze_post($post, $options = []) {
        if (is_numeric($post)) {
            $post = get_post($post);
        }

        if (!$post || !($post instanceof WP_Post)) {
            return [
                'error' => true,
                'message' => 'Post not found',
            ];
        }

        $this->url = get_permalink($post->ID);

        // Build full HTML from post
        $full_html = $this->build_post_html($post);

        // Parse HTML
        $this->dom = new DOMDocument();
        libxml_use_internal_errors(true);
        @$this->dom->loadHTML(mb_convert_encoding($full_html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $this->xpath = new DOMXPath($this->dom);

        // Extract data
        $data = $this->extract_all_data(200, []);

        // Add WordPress-specific data
        $data = $this->add_wordpress_data($data, $post);

        // API enrichment if requested
        if (!empty($options['use_pagespeed_api']) && !empty($this->pagespeed_api_key) && $post->post_status === 'publish') {
            $data['pagespeed_data'] = $this->fetch_pagespeed_data($this->url);
        }

        if (!empty($options['use_search_console']) && !empty($this->search_console_credentials)) {
            $data['search_console_data'] = $this->fetch_search_console_data($this->url);
        }

        return $data;
    }

    /**
     * Build HTML from WordPress post
     */
    private function build_post_html($post) {
        $html = '<!DOCTYPE html><html lang="' . esc_attr(get_bloginfo('language')) . '"><head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>' . esc_html($this->get_post_title($post)) . '</title>';

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

        // Mobile viewport
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1">';

        // Open Graph
        $og = $this->get_open_graph_tags($post);
        $html .= $og;

        // Schema.org JSON-LD
        $schema = $this->get_schema_markup($post);
        if ($schema) {
            $html .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
        }

        $html .= '</head><body>';
        $html .= '<article>';
        $html .= '<h1>' . esc_html(get_the_title($post)) . '</h1>';

        // Author info
        $author = get_userdata($post->post_author);
        if ($author) {
            $html .= '<div class="author-info">';
            $html .= '<span class="author-name">' . esc_html($author->display_name) . '</span>';
            $bio = get_the_author_meta('description', $post->post_author);
            if ($bio) {
                $html .= '<p class="author-bio">' . esc_html($bio) . '</p>';
            }
            $html .= '</div>';
        }

        // Dates
        $html .= '<time datetime="' . esc_attr(get_the_date('c', $post)) . '">' . esc_html(get_the_date('', $post)) . '</time>';

        // Content
        $content = apply_filters('the_content', $post->post_content);
        $html .= $content;

        $html .= '</article>';
        $html .= '</body></html>';

        return $html;
    }

    /**
     * Extract all SEO-relevant data from the page
     */
    private function extract_all_data($http_status, $headers) {
        $data = [
            'url' => $this->url,
            'http_status' => $http_status,
            'analyzed_at' => current_time('mysql'),
            'is_crawlable' => $http_status === 200,
        ];

        // Extract all data types
        $data = array_merge($data, $this->extract_meta_data());
        $data = array_merge($data, $this->extract_headings());
        $data = array_merge($data, $this->extract_content_data());
        $data = array_merge($data, $this->extract_links());
        $data = array_merge($data, $this->extract_images());
        $data = array_merge($data, $this->extract_schema());
        $data = array_merge($data, $this->extract_technical($headers));
        $data = array_merge($data, $this->extract_social_meta());

        return $data;
    }

    /**
     * Extract meta data
     */
    private function extract_meta_data() {
        $data = [];

        // Title
        $title_nodes = $this->xpath->query('//title');
        $data['title'] = $title_nodes->length > 0 ? trim($title_nodes->item(0)->textContent) : '';
        $data['title_length'] = mb_strlen($data['title']);

        // Meta Description
        $desc_nodes = $this->xpath->query('//meta[@name="description"]/@content');
        $data['meta_description'] = $desc_nodes->length > 0 ? trim($desc_nodes->item(0)->textContent) : '';
        $data['meta_description_length'] = mb_strlen($data['meta_description']);

        // Meta Keywords (legacy)
        $keywords_nodes = $this->xpath->query('//meta[@name="keywords"]/@content');
        $data['meta_keywords'] = $keywords_nodes->length > 0 ? $keywords_nodes->item(0)->textContent : '';

        // Canonical
        $canonical_nodes = $this->xpath->query('//link[@rel="canonical"]/@href');
        $data['canonical'] = $canonical_nodes->length > 0 ? $canonical_nodes->item(0)->textContent : '';
        $data['has_canonical'] = !empty($data['canonical']);

        // Robots
        $robots_nodes = $this->xpath->query('//meta[@name="robots"]/@content');
        $data['robots_meta'] = $robots_nodes->length > 0 ? $robots_nodes->item(0)->textContent : '';

        // X-Robots-Tag (from header is handled separately)

        // Language
        $html_lang = $this->xpath->query('//html/@lang');
        $data['language'] = $html_lang->length > 0 ? $html_lang->item(0)->textContent : '';

        // Charset
        $charset = $this->xpath->query('//meta[@charset]/@charset');
        $data['charset'] = $charset->length > 0 ? $charset->item(0)->textContent : '';

        return $data;
    }

    /**
     * Extract heading structure
     */
    private function extract_headings() {
        $data = [];

        // H1
        $h1_nodes = $this->xpath->query('//h1');
        $data['h1_count'] = $h1_nodes->length;
        $data['h1'] = [];
        foreach ($h1_nodes as $node) {
            $text = trim($node->textContent);
            if (!empty($text)) {
                $data['h1'][] = $text;
            }
        }

        // H2
        $h2_nodes = $this->xpath->query('//h2');
        $data['h2_count'] = $h2_nodes->length;
        $data['h2'] = [];
        foreach ($h2_nodes as $node) {
            $text = trim($node->textContent);
            if (!empty($text)) {
                $data['h2'][] = $text;
            }
        }

        // H3
        $h3_nodes = $this->xpath->query('//h3');
        $data['h3_count'] = $h3_nodes->length;
        $data['h3'] = [];
        foreach ($h3_nodes as $node) {
            $text = trim($node->textContent);
            if (!empty($text)) {
                $data['h3'][] = $text;
            }
        }

        // H4-H6 counts
        $data['h4_count'] = $this->xpath->query('//h4')->length;
        $data['h5_count'] = $this->xpath->query('//h5')->length;
        $data['h6_count'] = $this->xpath->query('//h6')->length;

        // Heading hierarchy validation
        $data['heading_hierarchy_valid'] = $this->validate_heading_hierarchy($data);

        // Total headings
        $data['total_headings'] = $data['h1_count'] + $data['h2_count'] + $data['h3_count'] +
                                   $data['h4_count'] + $data['h5_count'] + $data['h6_count'];

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
        $data = [];

        // Get body text content
        $body = $this->xpath->query('//body');
        $raw_content = $body->length > 0 ? $body->item(0)->textContent : '';

        // Clean content
        $content = preg_replace('/\s+/', ' ', $raw_content);
        $content = trim($content);

        $data['content'] = $content;
        $data['word_count'] = str_word_count($content);
        $data['character_count'] = mb_strlen($content);

        // Sentence analysis
        $sentences = preg_split('/[.!?]+/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $data['sentence_count'] = count(array_filter($sentences, function($s) {
            return strlen(trim($s)) > 5;
        }));
        $data['avg_sentence_length'] = $data['sentence_count'] > 0
            ? round($data['word_count'] / $data['sentence_count'], 1)
            : 0;

        // Paragraph count
        $data['paragraph_count'] = $this->xpath->query('//p')->length;

        // Lists
        $ul_count = $this->xpath->query('//ul')->length;
        $ol_count = $this->xpath->query('//ol')->length;
        $data['has_lists'] = ($ul_count + $ol_count) > 0;
        $data['list_count'] = $ul_count + $ol_count;
        $data['ul_count'] = $ul_count;
        $data['ol_count'] = $ol_count;

        // Tables
        $data['table_count'] = $this->xpath->query('//table')->length;
        $data['has_tables'] = $data['table_count'] > 0;

        // Video
        $video_count = $this->xpath->query('//video')->length;
        $youtube_count = $this->xpath->query('//iframe[contains(@src, "youtube")]')->length;
        $vimeo_count = $this->xpath->query('//iframe[contains(@src, "vimeo")]')->length;
        $data['has_video'] = ($video_count + $youtube_count + $vimeo_count) > 0;
        $data['video_count'] = $video_count + $youtube_count + $vimeo_count;

        // Audio
        $data['audio_count'] = $this->xpath->query('//audio')->length;
        $data['has_audio'] = $data['audio_count'] > 0;

        // Blockquotes
        $data['blockquote_count'] = $this->xpath->query('//blockquote')->length;
        $data['has_citations'] = $data['blockquote_count'] > 0;

        // Code blocks
        $data['code_count'] = $this->xpath->query('//code | //pre')->length;

        // Reading time (average 200-250 words per minute)
        $data['reading_time_minutes'] = ceil($data['word_count'] / 225);

        // Flesch Reading Ease
        $data['flesch_reading_ease'] = $this->calculate_flesch_score($content);

        // Keyword density (requires focus keyword to be set externally)
        $data['keyword_density'] = null;

        return $data;
    }

    /**
     * Calculate Flesch Reading Ease score
     */
    private function calculate_flesch_score($content) {
        $word_count = str_word_count($content);
        if ($word_count === 0) return 0;

        $sentences = preg_split('/[.!?]+/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $sentence_count = max(1, count(array_filter($sentences)));

        // Estimate syllables
        $syllable_count = $this->count_syllables($content);

        // Flesch formula
        $asl = $word_count / $sentence_count;
        $asw = $syllable_count / max(1, $word_count);

        $score = 206.835 - (1.015 * $asl) - (84.6 * $asw);

        return max(0, min(100, round($score)));
    }

    /**
     * Count syllables in text (approximation)
     */
    private function count_syllables($text) {
        $text = strtolower($text);
        $words = str_word_count($text, 1);
        $syllables = 0;

        foreach ($words as $word) {
            $word = preg_replace('/[^a-z]/', '', $word);
            if (strlen($word) === 0) continue;

            // Remove silent e
            $word = preg_replace('/e$/', '', $word);

            // Count vowel groups
            $vowel_count = preg_match_all('/[aeiouy]+/', $word, $matches);
            $syllables += max(1, $vowel_count);
        }

        return $syllables;
    }

    /**
     * Extract link data
     */
    private function extract_links() {
        $data = [];
        $links = $this->xpath->query('//a[@href]');

        $internal = [];
        $external = [];
        $anchors = [];

        $site_host = parse_url($this->url, PHP_URL_HOST);

        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $anchor = trim($link->textContent);
            $rel = $link->getAttribute('rel');
            $title = $link->getAttribute('title');
            $target = $link->getAttribute('target');

            // Skip empty/anchor-only/javascript links
            if (empty($href) || $href[0] === '#' || strpos($href, 'javascript:') === 0) {
                continue;
            }

            // Skip mailto/tel links
            if (strpos($href, 'mailto:') === 0 || strpos($href, 'tel:') === 0) {
                continue;
            }

            // Parse link
            $link_host = parse_url($href, PHP_URL_HOST);

            $link_data = [
                'href' => $href,
                'anchor' => $anchor,
                'title' => $title,
                'nofollow' => strpos($rel, 'nofollow') !== false,
                'sponsored' => strpos($rel, 'sponsored') !== false,
                'ugc' => strpos($rel, 'ugc') !== false,
                'new_window' => $target === '_blank',
            ];

            if ($link_host === null || $link_host === $site_host) {
                $internal[] = $link_data;
            } else {
                $link_data['domain'] = $link_host;
                $external[] = $link_data;
            }

            if (!empty($anchor)) {
                $anchors[] = $anchor;
            }
        }

        $data['internal_links'] = $internal;
        $data['internal_link_count'] = count($internal);
        $data['external_links'] = $external;
        $data['external_link_count'] = count($external);
        $data['total_links'] = count($internal) + count($external);
        $data['anchor_texts'] = $anchors;
        $data['has_authority_external_links'] = $this->has_authority_links($external);

        // Unique domains in external links
        $unique_domains = array_unique(array_column($external, 'domain'));
        $data['unique_external_domains'] = count($unique_domains);

        // Nofollow ratio
        $nofollow_count = count(array_filter($external, fn($l) => $l['nofollow']));
        $data['nofollow_ratio'] = count($external) > 0 ? round($nofollow_count / count($external) * 100, 1) : 0;

        return $data;
    }

    /**
     * Check for authority links
     */
    private function has_authority_links($external_links) {
        $authority_patterns = [
            'wikipedia.org',
            '.gov',
            '.edu',
            'bbc.com',
            'nytimes.com',
            'theguardian.com',
            'reuters.com',
            'forbes.com',
            'harvard.edu',
            'stanford.edu',
            'mit.edu',
            'nature.com',
            'sciencedirect.com',
            'pubmed.ncbi.nlm.nih.gov',
        ];

        foreach ($external_links as $link) {
            $domain = $link['domain'] ?? '';
            foreach ($authority_patterns as $pattern) {
                if (strpos($domain, $pattern) !== false) {
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
        $data = [];
        $images = $this->xpath->query('//img');
        $image_data = [];
        $missing_alt = 0;
        $lazy_loaded = 0;
        $with_dimensions = 0;

        foreach ($images as $img) {
            $src = $img->getAttribute('src') ?: $img->getAttribute('data-src');
            $alt = $img->getAttribute('alt');
            $loading = $img->getAttribute('loading');
            $width = $img->getAttribute('width');
            $height = $img->getAttribute('height');
            $srcset = $img->getAttribute('srcset');
            $sizes = $img->getAttribute('sizes');

            $img_info = [
                'src' => $src,
                'alt' => $alt,
                'has_alt' => !empty($alt),
                'lazy_loading' => $loading === 'lazy' || $img->getAttribute('data-lazy') !== '',
                'has_dimensions' => !empty($width) && !empty($height),
                'has_srcset' => !empty($srcset),
                'has_sizes' => !empty($sizes),
            ];

            $image_data[] = $img_info;

            if (empty($alt)) $missing_alt++;
            if ($img_info['lazy_loading']) $lazy_loaded++;
            if ($img_info['has_dimensions']) $with_dimensions++;
        }

        $total = count($image_data);
        $data['images'] = $image_data;
        $data['image_count'] = $total;
        $data['images_missing_alt'] = $missing_alt;
        $data['images_alt_ratio'] = $total > 0 ? round(($total - $missing_alt) / $total * 100, 1) : 100;
        $data['images_lazy_loaded'] = $lazy_loaded;
        $data['images_lazy_ratio'] = $total > 0 ? round($lazy_loaded / $total * 100, 1) : 100;
        $data['images_with_dimensions'] = $with_dimensions;

        return $data;
    }

    /**
     * Extract structured data (Schema.org)
     */
    private function extract_schema() {
        $data = [];

        // JSON-LD Schema
        $json_ld_nodes = $this->xpath->query('//script[@type="application/ld+json"]');
        $schema_types = [];
        $all_schemas = [];
        $schema_errors = [];

        foreach ($json_ld_nodes as $node) {
            $raw_json = trim($node->textContent);
            $json = json_decode($raw_json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $schema_errors[] = json_last_error_msg();
                continue;
            }

            if ($json) {
                $all_schemas[] = $json;

                // Extract types
                if (isset($json['@type'])) {
                    $types = is_array($json['@type']) ? $json['@type'] : [$json['@type']];
                    $schema_types = array_merge($schema_types, $types);
                }

                // Check for @graph structure
                if (isset($json['@graph'])) {
                    foreach ($json['@graph'] as $item) {
                        if (isset($item['@type'])) {
                            $types = is_array($item['@type']) ? $item['@type'] : [$item['@type']];
                            $schema_types = array_merge($schema_types, $types);
                        }
                    }
                }
            }
        }

        $schema_types = array_unique($schema_types);

        $data['schema_types'] = $schema_types;
        $data['schema_count'] = count($all_schemas);
        $data['has_schema'] = count($schema_types) > 0;
        $data['schema_valid'] = count($schema_errors) === 0 && $this->validate_schema($all_schemas);
        $data['schema_errors'] = $schema_errors;

        // Check specific schema types
        $data['has_article_schema'] = $this->has_schema_type($schema_types, ['Article', 'BlogPosting', 'NewsArticle', 'TechArticle']);
        $data['has_product_schema'] = $this->has_schema_type($schema_types, ['Product']);
        $data['has_faq_schema'] = $this->has_schema_type($schema_types, ['FAQPage']);
        $data['has_howto_schema'] = $this->has_schema_type($schema_types, ['HowTo']);
        $data['has_breadcrumb_schema'] = $this->has_schema_type($schema_types, ['BreadcrumbList']);
        $data['has_local_business_schema'] = $this->has_schema_type($schema_types, ['LocalBusiness', 'Organization', 'Store', 'Restaurant']);
        $data['has_review_schema'] = $this->has_schema_type($schema_types, ['Review', 'AggregateRating']);
        $data['has_person_schema'] = $this->has_schema_type($schema_types, ['Person']);
        $data['has_author_schema'] = $data['has_person_schema'];
        $data['has_video_schema'] = $this->has_schema_type($schema_types, ['VideoObject']);
        $data['has_recipe_schema'] = $this->has_schema_type($schema_types, ['Recipe']);
        $data['has_event_schema'] = $this->has_schema_type($schema_types, ['Event']);
        $data['has_course_schema'] = $this->has_schema_type($schema_types, ['Course']);
        $data['has_job_posting_schema'] = $this->has_schema_type($schema_types, ['JobPosting']);

        // Check for dates in schema
        $data['has_date_schema'] = $this->has_date_in_schema($all_schemas);

        // Rich results potential
        $data['rich_results_eligible'] = $data['has_faq_schema'] || $data['has_howto_schema'] ||
            $data['has_product_schema'] || $data['has_review_schema'] ||
            $data['has_recipe_schema'] || $data['has_video_schema'];

        return $data;
    }

    /**
     * Check if schema types array contains any of the target types
     */
    private function has_schema_type($schema_types, $target_types) {
        foreach ($target_types as $type) {
            if (in_array($type, $schema_types)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Validate schema structure
     */
    private function validate_schema($schemas) {
        foreach ($schemas as $schema) {
            if (!isset($schema['@context']) && !isset($schema['@graph'])) {
                return false;
            }
        }
        return !empty($schemas);
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
    private function extract_technical($headers = []) {
        $data = [];

        // HTTPS check
        $data['is_https'] = parse_url($this->url, PHP_URL_SCHEME) === 'https';

        // Mobile viewport
        $viewport = $this->xpath->query('//meta[@name="viewport"]/@content');
        $data['has_mobile_viewport'] = $viewport->length > 0;
        $data['viewport_content'] = $viewport->length > 0 ? $viewport->item(0)->textContent : '';

        // Check viewport validity
        if ($data['has_mobile_viewport']) {
            $data['viewport_valid'] = strpos($data['viewport_content'], 'width=device-width') !== false;
        } else {
            $data['viewport_valid'] = false;
        }

        // AMP check
        $amp_html = $this->xpath->query('//html[@amp or @\u26a1]');
        $data['is_amp'] = $amp_html->length > 0;

        // AMP link
        $amp_link = $this->xpath->query('//link[@rel="amphtml"]/@href');
        $data['has_amp_version'] = $amp_link->length > 0;

        // Hreflang
        $hreflang = $this->xpath->query('//link[@rel="alternate"][@hreflang]');
        $data['has_hreflang'] = $hreflang->length > 0;
        $data['hreflang_count'] = $hreflang->length;
        $hreflang_langs = [];
        foreach ($hreflang as $node) {
            $hreflang_langs[] = $node->getAttribute('hreflang');
        }
        $data['hreflang_languages'] = $hreflang_langs;

        // Page size
        $data['page_size_bytes'] = strlen($this->html);
        $data['page_size_kb'] = round($data['page_size_bytes'] / 1024, 2);

        // Resource counts
        $data['script_count'] = $this->xpath->query('//script')->length;
        $data['external_script_count'] = $this->xpath->query('//script[@src]')->length;
        $data['inline_script_count'] = $data['script_count'] - $data['external_script_count'];
        $data['stylesheet_count'] = $this->xpath->query('//link[@rel="stylesheet"]')->length;
        $data['inline_style_count'] = $this->xpath->query('//style')->length;

        // Lazy loading
        $data['uses_lazy_loading'] = $this->xpath->query('//*[@loading="lazy"]')->length > 0;

        // Preload/Prefetch
        $data['has_preload'] = $this->xpath->query('//link[@rel="preload"]')->length > 0;
        $data['has_prefetch'] = $this->xpath->query('//link[@rel="prefetch"]')->length > 0;
        $data['has_dns_prefetch'] = $this->xpath->query('//link[@rel="dns-prefetch"]')->length > 0;
        $data['has_preconnect'] = $this->xpath->query('//link[@rel="preconnect"]')->length > 0;

        // HTTP Headers analysis (if available)
        if (!empty($headers)) {
            $data['x_robots_tag'] = isset($headers['x-robots-tag']) ? $headers['x-robots-tag'] : '';
            $data['cache_control'] = isset($headers['cache-control']) ? $headers['cache-control'] : '';
            $data['content_type'] = isset($headers['content-type']) ? $headers['content-type'] : '';
        }

        // iFrames count (can indicate ads or embedded content)
        $data['iframe_count'] = $this->xpath->query('//iframe')->length;

        return $data;
    }

    /**
     * Extract social meta tags
     */
    private function extract_social_meta() {
        $data = [];

        // Open Graph
        $data['og_title'] = $this->get_meta_property('og:title');
        $data['og_description'] = $this->get_meta_property('og:description');
        $data['og_image'] = $this->get_meta_property('og:image');
        $data['og_type'] = $this->get_meta_property('og:type');
        $data['og_url'] = $this->get_meta_property('og:url');
        $data['og_site_name'] = $this->get_meta_property('og:site_name');

        $data['has_open_graph'] = !empty($data['og_title']) || !empty($data['og_image']);

        // Twitter Card
        $data['twitter_card'] = $this->get_meta_name('twitter:card');
        $data['twitter_title'] = $this->get_meta_name('twitter:title');
        $data['twitter_description'] = $this->get_meta_name('twitter:description');
        $data['twitter_image'] = $this->get_meta_name('twitter:image');
        $data['twitter_site'] = $this->get_meta_name('twitter:site');
        $data['twitter_creator'] = $this->get_meta_name('twitter:creator');

        $data['has_twitter_cards'] = !empty($data['twitter_card']);

        return $data;
    }

    /**
     * Add WordPress-specific data
     */
    private function add_wordpress_data($data, $post) {
        $data['post_id'] = $post->ID;
        $data['post_type'] = $post->post_type;
        $data['post_status'] = $post->post_status;
        $data['post_date'] = $post->post_date;
        $data['post_modified'] = $post->post_modified;

        // Author data
        $author = get_userdata($post->post_author);
        if ($author) {
            $data['author_name'] = $author->display_name;
            $data['author_id'] = $author->ID;
            $bio = get_the_author_meta('description', $post->post_author);
            $data['author_bio'] = $bio;
            $data['has_author_bio'] = !empty($bio);
        }

        // Featured image
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        $data['has_featured_image'] = !empty($thumbnail_id);
        if ($thumbnail_id) {
            $data['featured_image_url'] = get_the_post_thumbnail_url($post->ID, 'full');
            $data['featured_image_alt'] = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
        }

        // Categories & Tags
        $categories = get_the_category($post->ID);
        $data['categories'] = wp_list_pluck($categories, 'name');
        $data['category_count'] = count($categories);

        $tags = get_the_tags($post->ID);
        $data['tags'] = $tags ? wp_list_pluck($tags, 'name') : [];
        $data['tag_count'] = $tags ? count($tags) : 0;

        // Focus keyword from SEO plugins
        $data['focus_keyword'] = $this->get_focus_keyword($post->ID);

        // SEO plugin data
        $data['seo_plugin_data'] = $this->get_seo_plugin_data($post->ID);

        // Comments
        $data['comment_count'] = $post->comment_count;
        $data['comments_open'] = comments_open($post->ID);

        return $data;
    }

    /**
     * Get focus keyword from various SEO plugins
     */
    private function get_focus_keyword($post_id) {
        // Yoast SEO
        $yoast_keyword = get_post_meta($post_id, '_yoast_wpseo_focuskw', true);
        if (!empty($yoast_keyword)) return $yoast_keyword;

        // Rank Math
        $rankmath_keyword = get_post_meta($post_id, 'rank_math_focus_keyword', true);
        if (!empty($rankmath_keyword)) {
            // Rank Math can have multiple keywords
            $keywords = explode(',', $rankmath_keyword);
            return trim($keywords[0]);
        }

        // AIOSEO
        $aioseo_keyword = get_post_meta($post_id, '_aioseo_keyphrases', true);
        if (!empty($aioseo_keyword)) {
            $keyphrases = json_decode($aioseo_keyword, true);
            if (isset($keyphrases['focus']['keyphrase'])) {
                return $keyphrases['focus']['keyphrase'];
            }
        }

        // SEOPress
        $seopress_keyword = get_post_meta($post_id, '_seopress_analysis_target_kw', true);
        if (!empty($seopress_keyword)) return $seopress_keyword;

        return '';
    }

    /**
     * Get data from installed SEO plugins
     */
    private function get_seo_plugin_data($post_id) {
        $seo_data = [];

        // Yoast SEO
        $yoast_score = get_post_meta($post_id, '_yoast_wpseo_linkdex', true);
        if (!empty($yoast_score)) {
            $seo_data['yoast'] = [
                'seo_score' => $yoast_score,
                'title' => get_post_meta($post_id, '_yoast_wpseo_title', true),
                'description' => get_post_meta($post_id, '_yoast_wpseo_metadesc', true),
                'focus_keyword' => get_post_meta($post_id, '_yoast_wpseo_focuskw', true),
            ];
        }

        // Rank Math
        $rankmath_score = get_post_meta($post_id, 'rank_math_seo_score', true);
        if (!empty($rankmath_score)) {
            $seo_data['rank_math'] = [
                'seo_score' => $rankmath_score,
                'title' => get_post_meta($post_id, 'rank_math_title', true),
                'description' => get_post_meta($post_id, 'rank_math_description', true),
                'focus_keyword' => get_post_meta($post_id, 'rank_math_focus_keyword', true),
            ];
        }

        // AIOSEO
        $aioseo_data = get_post_meta($post_id, '_aioseo_og_article_tags', true);
        if (!empty($aioseo_data)) {
            $seo_data['aioseo'] = [
                'title' => get_post_meta($post_id, '_aioseo_title', true),
                'description' => get_post_meta($post_id, '_aioseo_description', true),
            ];
        }

        return $seo_data;
    }

    /**
     * Get meta description from various sources
     */
    private function get_meta_description($post_id) {
        // Yoast SEO
        $yoast_desc = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
        if (!empty($yoast_desc)) return $yoast_desc;

        // Rank Math
        $rankmath_desc = get_post_meta($post_id, 'rank_math_description', true);
        if (!empty($rankmath_desc)) return $rankmath_desc;

        // AIOSEO
        $aioseo_desc = get_post_meta($post_id, '_aioseo_description', true);
        if (!empty($aioseo_desc)) return $aioseo_desc;

        // SEOPress
        $seopress_desc = get_post_meta($post_id, '_seopress_titles_desc', true);
        if (!empty($seopress_desc)) return $seopress_desc;

        // Fallback to excerpt
        return get_the_excerpt($post_id);
    }

    /**
     * Get robots meta from SEO plugins
     */
    private function get_robots_meta($post_id) {
        // Yoast
        $noindex = get_post_meta($post_id, '_yoast_wpseo_meta-robots-noindex', true);
        $nofollow = get_post_meta($post_id, '_yoast_wpseo_meta-robots-nofollow', true);

        if ($noindex === '1') {
            return $nofollow === '1' ? 'noindex, nofollow' : 'noindex';
        }
        if ($nofollow === '1') {
            return 'nofollow';
        }

        // Rank Math
        $robots = get_post_meta($post_id, 'rank_math_robots', true);
        if (!empty($robots) && is_array($robots)) {
            return implode(', ', $robots);
        }

        return 'index, follow';
    }

    /**
     * Get SEO-optimized post title
     */
    private function get_post_title($post) {
        // Yoast
        $yoast_title = get_post_meta($post->ID, '_yoast_wpseo_title', true);
        if (!empty($yoast_title)) return $this->replace_title_vars($yoast_title, $post);

        // Rank Math
        $rm_title = get_post_meta($post->ID, 'rank_math_title', true);
        if (!empty($rm_title)) return $this->replace_title_vars($rm_title, $post);

        // AIOSEO
        $aio_title = get_post_meta($post->ID, '_aioseo_title', true);
        if (!empty($aio_title)) return $aio_title;

        return get_the_title($post);
    }

    /**
     * Replace common title variables
     */
    private function replace_title_vars($title, $post) {
        $replacements = [
            '%%title%%' => get_the_title($post),
            '%%sitename%%' => get_bloginfo('name'),
            '%%sep%%' => '-',
            '%%page%%' => '',
            '%%primary_category%%' => '',
        ];

        // Get primary category
        $categories = get_the_category($post->ID);
        if (!empty($categories)) {
            $replacements['%%primary_category%%'] = $categories[0]->name;
        }

        return str_replace(array_keys($replacements), array_values($replacements), $title);
    }

    /**
     * Generate Open Graph tags from post
     */
    private function get_open_graph_tags($post) {
        $html = '';

        $og_title = $this->get_post_title($post);
        $html .= '<meta property="og:title" content="' . esc_attr($og_title) . '">';
        $html .= '<meta property="og:type" content="article">';
        $html .= '<meta property="og:url" content="' . esc_url(get_permalink($post->ID)) . '">';

        $desc = $this->get_meta_description($post->ID);
        if ($desc) {
            $html .= '<meta property="og:description" content="' . esc_attr($desc) . '">';
        }

        $thumbnail = get_the_post_thumbnail_url($post->ID, 'large');
        if ($thumbnail) {
            $html .= '<meta property="og:image" content="' . esc_url($thumbnail) . '">';
        }

        $html .= '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">';

        return $html;
    }

    /**
     * Generate Schema.org markup for post
     */
    private function get_schema_markup($post) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title($post),
            'url' => get_permalink($post->ID),
            'datePublished' => get_the_date('c', $post),
            'dateModified' => get_the_modified_date('c', $post),
        ];

        // Author
        $author = get_userdata($post->post_author);
        if ($author) {
            $schema['author'] = [
                '@type' => 'Person',
                'name' => $author->display_name,
            ];
        }

        // Publisher
        $schema['publisher'] = [
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
        ];

        // Featured image
        $thumbnail = get_the_post_thumbnail_url($post->ID, 'full');
        if ($thumbnail) {
            $schema['image'] = $thumbnail;
        }

        // Description
        $desc = $this->get_meta_description($post->ID);
        if ($desc) {
            $schema['description'] = $desc;
        }

        return $schema;
    }

    /**
     * Fetch data from PageSpeed API v5
     */
    public function fetch_pagespeed_data($url, $strategy = 'mobile') {
        if (empty($this->pagespeed_api_key)) {
            return ['error' => 'PageSpeed API key not configured'];
        }

        $cache_key = 'wp_bulk_seo_psi_' . md5($url . $strategy);
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        $api_url = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
        $api_url = add_query_arg([
            'url' => urlencode($url),
            'key' => $this->pagespeed_api_key,
            'strategy' => $strategy,
            'category' => ['performance', 'seo', 'accessibility', 'best-practices'],
        ], $api_url);

        $response = wp_remote_get($api_url, [
            'timeout' => 60,
        ]);

        if (is_wp_error($response)) {
            return ['error' => $response->get_error_message()];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            return ['error' => $data['error']['message'] ?? 'API error'];
        }

        // Extract key metrics
        $result = [
            'strategy' => $strategy,
            'fetch_time' => current_time('mysql'),
        ];

        // Loading Experience (field data)
        if (isset($data['loadingExperience']['metrics'])) {
            $metrics = $data['loadingExperience']['metrics'];
            $result['field_data'] = [
                'lcp' => $metrics['LARGEST_CONTENTFUL_PAINT_MS']['percentile'] ?? null,
                'fid' => $metrics['FIRST_INPUT_DELAY_MS']['percentile'] ?? null,
                'cls' => isset($metrics['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'])
                    ? $metrics['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'] / 100
                    : null,
                'fcp' => $metrics['FIRST_CONTENTFUL_PAINT_MS']['percentile'] ?? null,
                'ttfb' => $metrics['EXPERIMENTAL_TIME_TO_FIRST_BYTE']['percentile'] ?? null,
            ];
            $result['overall_category'] = $data['loadingExperience']['overall_category'] ?? 'NONE';
        }

        // Lighthouse Results (lab data)
        if (isset($data['lighthouseResult'])) {
            $lr = $data['lighthouseResult'];

            // Category scores
            if (isset($lr['categories'])) {
                $result['lighthouse_scores'] = [
                    'performance' => isset($lr['categories']['performance']['score'])
                        ? round($lr['categories']['performance']['score'] * 100)
                        : null,
                    'seo' => isset($lr['categories']['seo']['score'])
                        ? round($lr['categories']['seo']['score'] * 100)
                        : null,
                    'accessibility' => isset($lr['categories']['accessibility']['score'])
                        ? round($lr['categories']['accessibility']['score'] * 100)
                        : null,
                    'best_practices' => isset($lr['categories']['best-practices']['score'])
                        ? round($lr['categories']['best-practices']['score'] * 100)
                        : null,
                ];
            }

            // Lab metrics
            if (isset($lr['audits'])) {
                $audits = $lr['audits'];
                $result['lab_data'] = [
                    'fcp' => $audits['first-contentful-paint']['numericValue'] ?? null,
                    'lcp' => $audits['largest-contentful-paint']['numericValue'] ?? null,
                    'tbt' => $audits['total-blocking-time']['numericValue'] ?? null,
                    'cls' => $audits['cumulative-layout-shift']['numericValue'] ?? null,
                    'speed_index' => $audits['speed-index']['numericValue'] ?? null,
                    'tti' => $audits['interactive']['numericValue'] ?? null,
                ];
            }

            // Opportunities and diagnostics
            $result['opportunities'] = [];
            $result['diagnostics'] = [];

            foreach ($lr['audits'] as $audit_id => $audit) {
                if (isset($audit['details']['overallSavingsMs']) && $audit['details']['overallSavingsMs'] > 0) {
                    $result['opportunities'][] = [
                        'id' => $audit_id,
                        'title' => $audit['title'],
                        'savings_ms' => $audit['details']['overallSavingsMs'],
                    ];
                }
            }
        }

        // Cache for 1 hour
        set_transient($cache_key, $result, 3600);

        return $result;
    }

    /**
     * Fetch data from Search Console API
     */
    public function fetch_search_console_data($url) {
        if (empty($this->search_console_credentials)) {
            return ['error' => 'Search Console not configured'];
        }

        // This requires OAuth setup - placeholder implementation
        // In production, use Google API PHP Client

        $cache_key = 'wp_bulk_seo_gsc_' . md5($url);
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        // TODO: Implement actual Search Console API call
        // Requires: google/apiclient-services
        // - searchanalytics.query for CTR, impressions, position
        // - urlInspection.index.inspect for indexing status

        return [
            'error' => 'Search Console API integration pending',
            'note' => 'Configure OAuth credentials in settings',
        ];
    }

    /**
     * Helper: Get meta property value
     */
    private function get_meta_property($property) {
        $nodes = $this->xpath->query('//meta[@property="' . $property . '"]/@content');
        return $nodes->length > 0 ? $nodes->item(0)->textContent : '';
    }

    /**
     * Helper: Get meta name value
     */
    private function get_meta_name($name) {
        $nodes = $this->xpath->query('//meta[@name="' . $name . '"]/@content');
        return $nodes->length > 0 ? $nodes->item(0)->textContent : '';
    }

    /**
     * Set focus keyword for analysis
     */
    public function set_focus_keyword(&$data, $keyword) {
        $data['focus_keyword'] = $keyword;

        // Calculate keyword density if content exists
        if (!empty($data['content']) && !empty($keyword)) {
            $content = strtolower($data['content']);
            $keyword_lower = strtolower($keyword);
            $word_count = $data['word_count'] ?? str_word_count($content);

            if ($word_count > 0) {
                $keyword_count = substr_count($content, $keyword_lower);
                $data['keyword_count'] = $keyword_count;
                $data['keyword_density'] = round(($keyword_count / $word_count) * 100, 2);
            }
        }
    }
}
