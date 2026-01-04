<?php
/**
 * WP Bulk SEO - Schema.org Generator
 *
 * Generates structured data for various content types.
 * Supports JSON-LD format for optimal SEO.
 *
 * @package WP_Bulk_SEO
 * @subpackage Schema
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Schema_Generator {

    /**
     * Schema templates
     */
    private $templates = [];

    /**
     * Organization data
     */
    private $organization = [];

    /**
     * Website data
     */
    private $website = [];

    /**
     * Constructor
     */
    public function __construct() {
        $this->load_settings();
        $this->init_hooks();
    }

    /**
     * Load settings
     */
    private function load_settings() {
        $this->organization = get_option('wp_bulk_seo_organization', [
            'name' => get_bloginfo('name'),
            'url' => home_url(),
            'logo' => '',
            'same_as' => [],
        ]);

        $this->website = [
            'name' => get_bloginfo('name'),
            'url' => home_url(),
            'description' => get_bloginfo('description'),
        ];
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('wp_head', [$this, 'output_schema'], 1);
    }

    /**
     * Output schema markup
     */
    public function output_schema() {
        $schemas = [];

        // Website Schema
        $schemas[] = $this->generate_website_schema();

        // Organization Schema
        if (!empty($this->organization['name'])) {
            $schemas[] = $this->generate_organization_schema();
        }

        // Page-specific schemas
        if (is_singular()) {
            $post = get_queried_object();

            if ($post instanceof WP_Post) {
                // Article/Blog schema
                if (in_array($post->post_type, ['post', 'page'])) {
                    $schemas[] = $this->generate_article_schema($post);
                }

                // Breadcrumb schema
                $schemas[] = $this->generate_breadcrumb_schema($post);

                // Check for FAQ content
                if ($this->has_faq_content($post)) {
                    $schemas[] = $this->generate_faq_schema($post);
                }

                // Check for HowTo content
                if ($this->has_howto_content($post)) {
                    $schemas[] = $this->generate_howto_schema($post);
                }

                // Product schema (for WooCommerce)
                if ($post->post_type === 'product' && class_exists('WooCommerce')) {
                    $schemas[] = $this->generate_product_schema($post);
                }
            }
        } elseif (is_home() || is_front_page()) {
            $schemas[] = $this->generate_webpage_schema('CollectionPage');
        } elseif (is_author()) {
            $author = get_queried_object();
            if ($author instanceof WP_User) {
                $schemas[] = $this->generate_person_schema($author);
            }
        } elseif (is_search()) {
            $schemas[] = $this->generate_search_results_schema();
        }

        // Filter schemas
        $schemas = apply_filters('wp_bulk_seo_schemas', $schemas);

        // Remove empty schemas
        $schemas = array_filter($schemas);

        // Output as JSON-LD
        if (!empty($schemas)) {
            foreach ($schemas as $schema) {
                if (!empty($schema)) {
                    echo '<script type="application/ld+json">' . "\n";
                    echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    echo "\n</script>\n";
                }
            }
        }
    }

    /**
     * Generate Website schema
     */
    public function generate_website_schema() {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => home_url('/#website'),
            'name' => $this->website['name'],
            'url' => $this->website['url'],
        ];

        if (!empty($this->website['description'])) {
            $schema['description'] = $this->website['description'];
        }

        // Add search action
        $schema['potentialAction'] = [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => home_url('/?s={search_term_string}'),
            ],
            'query-input' => 'required name=search_term_string',
        ];

        // Link to organization
        if (!empty($this->organization['name'])) {
            $schema['publisher'] = [
                '@id' => home_url('/#organization'),
            ];
        }

        return $schema;
    }

    /**
     * Generate Organization schema
     */
    public function generate_organization_schema() {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => home_url('/#organization'),
            'name' => $this->organization['name'],
            'url' => $this->organization['url'] ?: home_url(),
        ];

        if (!empty($this->organization['logo'])) {
            $schema['logo'] = [
                '@type' => 'ImageObject',
                '@id' => home_url('/#logo'),
                'url' => $this->organization['logo'],
                'contentUrl' => $this->organization['logo'],
            ];
            $schema['image'] = ['@id' => home_url('/#logo')];
        }

        // Social profiles
        if (!empty($this->organization['same_as']) && is_array($this->organization['same_as'])) {
            $schema['sameAs'] = array_filter($this->organization['same_as']);
        }

        // Contact point
        $contact_email = get_option('admin_email');
        if ($contact_email) {
            $schema['contactPoint'] = [
                '@type' => 'ContactPoint',
                'email' => $contact_email,
                'contactType' => 'customer service',
            ];
        }

        return $schema;
    }

    /**
     * Generate Article schema
     */
    public function generate_article_schema($post) {
        $author = get_userdata($post->post_author);
        $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $post->post_type === 'post' ? 'BlogPosting' : 'Article',
            '@id' => get_permalink($post->ID) . '#article',
            'headline' => get_the_title($post),
            'url' => get_permalink($post->ID),
            'datePublished' => get_the_date('c', $post),
            'dateModified' => get_the_modified_date('c', $post),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => get_permalink($post->ID),
            ],
        ];

        // Author
        if ($author) {
            $schema['author'] = [
                '@type' => 'Person',
                '@id' => get_author_posts_url($author->ID) . '#author',
                'name' => $author->display_name,
                'url' => get_author_posts_url($author->ID),
            ];

            $author_avatar = get_avatar_url($author->ID, ['size' => 96]);
            if ($author_avatar) {
                $schema['author']['image'] = [
                    '@type' => 'ImageObject',
                    'url' => $author_avatar,
                ];
            }
        }

        // Publisher
        $schema['publisher'] = [
            '@id' => home_url('/#organization'),
        ];

        // Featured image
        if ($thumbnail_url) {
            $thumbnail_id = get_post_thumbnail_id($post->ID);
            $thumbnail_meta = wp_get_attachment_metadata($thumbnail_id);

            $schema['image'] = [
                '@type' => 'ImageObject',
                'url' => $thumbnail_url,
            ];

            if ($thumbnail_meta) {
                $schema['image']['width'] = $thumbnail_meta['width'] ?? '';
                $schema['image']['height'] = $thumbnail_meta['height'] ?? '';
            }
        }

        // Description
        $description = $this->get_post_description($post->ID);
        if ($description) {
            $schema['description'] = $description;
        }

        // Word count
        $content = wp_strip_all_tags($post->post_content);
        $word_count = str_word_count($content);
        if ($word_count > 0) {
            $schema['wordCount'] = $word_count;
        }

        // Article section (category)
        $categories = get_the_category($post->ID);
        if (!empty($categories)) {
            $schema['articleSection'] = $categories[0]->name;
        }

        // Keywords (tags)
        $tags = get_the_tags($post->ID);
        if ($tags) {
            $schema['keywords'] = implode(', ', wp_list_pluck($tags, 'name'));
        }

        // Comments
        if ($post->comment_count > 0) {
            $schema['commentCount'] = (int) $post->comment_count;
        }

        return $schema;
    }

    /**
     * Generate Breadcrumb schema
     */
    public function generate_breadcrumb_schema($post) {
        $breadcrumbs = [];
        $position = 1;

        // Home
        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => __('Home', 'wp-bulk-seo'),
            'item' => home_url('/'),
        ];

        // Categories for posts
        if ($post->post_type === 'post') {
            $categories = get_the_category($post->ID);
            if (!empty($categories)) {
                $category = $categories[0];
                $breadcrumbs[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $category->name,
                    'item' => get_category_link($category->term_id),
                ];
            }
        }

        // Parent pages
        if ($post->post_type === 'page' && $post->post_parent) {
            $ancestors = get_post_ancestors($post->ID);
            $ancestors = array_reverse($ancestors);

            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_post($ancestor_id);
                $breadcrumbs[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => get_the_title($ancestor),
                    'item' => get_permalink($ancestor),
                ];
            }
        }

        // Current page
        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title($post),
            'item' => get_permalink($post->ID),
        ];

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs,
        ];
    }

    /**
     * Generate FAQ schema
     */
    public function generate_faq_schema($post) {
        $faq_items = $this->extract_faq_items($post);

        if (empty($faq_items)) {
            return null;
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

        return $schema;
    }

    /**
     * Generate HowTo schema
     */
    public function generate_howto_schema($post) {
        $steps = $this->extract_howto_steps($post);

        if (empty($steps)) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => get_the_title($post),
            'step' => [],
        ];

        $description = $this->get_post_description($post->ID);
        if ($description) {
            $schema['description'] = $description;
        }

        // Featured image
        $thumbnail = get_the_post_thumbnail_url($post->ID, 'large');
        if ($thumbnail) {
            $schema['image'] = $thumbnail;
        }

        foreach ($steps as $index => $step) {
            $step_data = [
                '@type' => 'HowToStep',
                'position' => $index + 1,
                'name' => $step['name'],
            ];

            if (!empty($step['text'])) {
                $step_data['text'] = $step['text'];
            }

            if (!empty($step['image'])) {
                $step_data['image'] = $step['image'];
            }

            $schema['step'][] = $step_data;
        }

        // Estimate total time
        $word_count = str_word_count(wp_strip_all_tags($post->post_content));
        $estimated_minutes = max(1, ceil($word_count / 200));
        $schema['totalTime'] = 'PT' . $estimated_minutes . 'M';

        return $schema;
    }

    /**
     * Generate Product schema (WooCommerce)
     */
    public function generate_product_schema($post) {
        if (!function_exists('wc_get_product')) {
            return null;
        }

        $product = wc_get_product($post->ID);
        if (!$product) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            '@id' => get_permalink($post->ID) . '#product',
            'name' => $product->get_name(),
            'url' => get_permalink($post->ID),
            'description' => wp_strip_all_tags($product->get_short_description() ?: $product->get_description()),
        ];

        // Images
        $image_id = $product->get_image_id();
        if ($image_id) {
            $schema['image'] = wp_get_attachment_url($image_id);
        }

        // SKU
        $sku = $product->get_sku();
        if ($sku) {
            $schema['sku'] = $sku;
        }

        // Brand
        $brand = $this->get_product_brand($product);
        if ($brand) {
            $schema['brand'] = [
                '@type' => 'Brand',
                'name' => $brand,
            ];
        }

        // Offers
        $schema['offers'] = [
            '@type' => 'Offer',
            'url' => get_permalink($post->ID),
            'priceCurrency' => get_woocommerce_currency(),
            'price' => $product->get_price(),
            'availability' => $product->is_in_stock()
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'seller' => [
                '@id' => home_url('/#organization'),
            ],
        ];

        // Sale dates
        $sale_start = $product->get_date_on_sale_from();
        $sale_end = $product->get_date_on_sale_to();
        if ($sale_start) {
            $schema['offers']['priceValidFrom'] = $sale_start->format('c');
        }
        if ($sale_end) {
            $schema['offers']['priceValidUntil'] = $sale_end->format('c');
        }

        // Reviews/Ratings
        $review_count = $product->get_review_count();
        $average_rating = $product->get_average_rating();

        if ($review_count > 0 && $average_rating > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $average_rating,
                'reviewCount' => $review_count,
                'bestRating' => '5',
                'worstRating' => '1',
            ];
        }

        return $schema;
    }

    /**
     * Generate Person schema
     */
    public function generate_person_schema($user) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            '@id' => get_author_posts_url($user->ID) . '#person',
            'name' => $user->display_name,
            'url' => get_author_posts_url($user->ID),
        ];

        // Avatar
        $avatar = get_avatar_url($user->ID, ['size' => 256]);
        if ($avatar) {
            $schema['image'] = $avatar;
        }

        // Bio
        $description = get_the_author_meta('description', $user->ID);
        if ($description) {
            $schema['description'] = $description;
        }

        // Social links
        $social_urls = [];
        $social_fields = ['twitter', 'facebook', 'linkedin', 'instagram', 'youtube'];

        foreach ($social_fields as $field) {
            $url = get_the_author_meta($field, $user->ID);
            if ($url) {
                $social_urls[] = $url;
            }
        }

        // User website
        $user_url = get_the_author_meta('url', $user->ID);
        if ($user_url) {
            $social_urls[] = $user_url;
        }

        if (!empty($social_urls)) {
            $schema['sameAs'] = $social_urls;
        }

        return $schema;
    }

    /**
     * Generate WebPage schema
     */
    public function generate_webpage_schema($type = 'WebPage') {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => wp_get_document_title(),
            'url' => home_url(add_query_arg(null, null)),
        ];

        if (is_front_page()) {
            $schema['@id'] = home_url('/#webpage');
            $schema['isPartOf'] = ['@id' => home_url('/#website')];
        }

        return $schema;
    }

    /**
     * Generate Search Results schema
     */
    public function generate_search_results_schema() {
        global $wp_query;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'SearchResultsPage',
            'name' => sprintf(__('Search Results for: %s', 'wp-bulk-seo'), get_search_query()),
            'url' => get_search_link(),
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => $wp_query->found_posts,
            ],
        ];
    }

    /**
     * Generate Local Business schema
     */
    public function generate_local_business_schema($data = []) {
        $defaults = [
            'type' => 'LocalBusiness',
            'name' => get_bloginfo('name'),
            'address' => [],
            'telephone' => '',
            'opening_hours' => [],
            'geo' => [],
        ];

        $data = wp_parse_args($data, $defaults);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $data['type'],
            'name' => $data['name'],
            'url' => home_url(),
        ];

        // Logo
        if (!empty($this->organization['logo'])) {
            $schema['image'] = $this->organization['logo'];
        }

        // Address
        if (!empty($data['address'])) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $data['address']['street'] ?? '',
                'addressLocality' => $data['address']['city'] ?? '',
                'addressRegion' => $data['address']['region'] ?? '',
                'postalCode' => $data['address']['postal_code'] ?? '',
                'addressCountry' => $data['address']['country'] ?? '',
            ];
        }

        // Telephone
        if (!empty($data['telephone'])) {
            $schema['telephone'] = $data['telephone'];
        }

        // Geo coordinates
        if (!empty($data['geo']['latitude']) && !empty($data['geo']['longitude'])) {
            $schema['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $data['geo']['latitude'],
                'longitude' => $data['geo']['longitude'],
            ];
        }

        // Opening hours
        if (!empty($data['opening_hours'])) {
            $schema['openingHoursSpecification'] = [];
            foreach ($data['opening_hours'] as $day => $hours) {
                $schema['openingHoursSpecification'][] = [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => $day,
                    'opens' => $hours['opens'],
                    'closes' => $hours['closes'],
                ];
            }
        }

        return $schema;
    }

    /**
     * Check if post has FAQ content
     */
    private function has_faq_content($post) {
        $content = strtolower($post->post_content);

        // Check for FAQ block patterns
        $patterns = [
            'wp:yoast/faq-block',
            'wp:rank-math/faq-block',
            'faq',
            'frequently asked',
            'q&a',
            'q:',
            'a:',
        ];

        foreach ($patterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                return true;
            }
        }

        // Check for question headings
        if (preg_match_all('/<h[2-4][^>]*>.*\?.*<\/h[2-4]>/i', $post->post_content, $matches)) {
            return count($matches[0]) >= 2;
        }

        return false;
    }

    /**
     * Extract FAQ items from post
     */
    private function extract_faq_items($post) {
        $faq_items = [];
        $content = $post->post_content;

        // Try to find FAQ blocks (Yoast, Rank Math format)
        if (preg_match_all('/<!-- wp:yoast\/faq-block.*?-->(.+?)<!-- \/wp:yoast\/faq-block -->/s', $content, $blocks)) {
            foreach ($blocks[1] as $block) {
                // Parse Yoast FAQ block format
                if (preg_match_all('/"question":"([^"]+)".*?"answer":"([^"]+)"/s', $block, $items, PREG_SET_ORDER)) {
                    foreach ($items as $item) {
                        $faq_items[] = [
                            'question' => wp_strip_all_tags(html_entity_decode($item[1])),
                            'answer' => wp_strip_all_tags(html_entity_decode($item[2])),
                        ];
                    }
                }
            }
        }

        // Fallback: Extract from heading + paragraph patterns
        if (empty($faq_items)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);
            $headings = $xpath->query('//h2|//h3|//h4');

            foreach ($headings as $heading) {
                $text = trim($heading->textContent);
                if (strpos($text, '?') !== false) {
                    // Find next sibling paragraph
                    $next = $heading->nextSibling;
                    while ($next && $next->nodeName !== 'p') {
                        $next = $next->nextSibling;
                    }

                    if ($next && $next->nodeName === 'p') {
                        $faq_items[] = [
                            'question' => $text,
                            'answer' => trim($next->textContent),
                        ];
                    }
                }
            }
        }

        return array_slice($faq_items, 0, 10); // Limit to 10 FAQs
    }

    /**
     * Check if post has HowTo content
     */
    private function has_howto_content($post) {
        $content = strtolower($post->post_content);

        $patterns = [
            'how to',
            'step 1',
            'step by step',
            'tutorial',
            'guide',
            'wp:yoast/how-to-block',
        ];

        foreach ($patterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract HowTo steps from post
     */
    private function extract_howto_steps($post) {
        $steps = [];
        $content = $post->post_content;

        // Try to find HowTo blocks
        if (preg_match_all('/<!-- wp:yoast\/how-to-block.*?-->(.+?)<!-- \/wp:yoast\/how-to-block -->/s', $content, $blocks)) {
            // Parse Yoast HowTo block
            foreach ($blocks[1] as $block) {
                if (preg_match_all('/"name":"([^"]+)".*?"text":"([^"]+)"/s', $block, $items, PREG_SET_ORDER)) {
                    foreach ($items as $item) {
                        $steps[] = [
                            'name' => wp_strip_all_tags(html_entity_decode($item[1])),
                            'text' => wp_strip_all_tags(html_entity_decode($item[2])),
                        ];
                    }
                }
            }
        }

        // Fallback: Extract from numbered headings or lists
        if (empty($steps)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);

            // Try ordered lists
            $ols = $xpath->query('//ol');
            foreach ($ols as $ol) {
                $lis = $xpath->query('.//li', $ol);
                foreach ($lis as $li) {
                    $text = trim($li->textContent);
                    if (strlen($text) > 10) {
                        $steps[] = [
                            'name' => mb_substr($text, 0, 100),
                            'text' => $text,
                        ];
                    }
                }
                if (!empty($steps)) break;
            }

            // Try numbered headings (Step 1, Step 2, etc.)
            if (empty($steps)) {
                $headings = $xpath->query('//h2|//h3|//h4');
                foreach ($headings as $heading) {
                    $text = trim($heading->textContent);
                    if (preg_match('/^(step\s*\d+|단계\s*\d+|\d+\.)/i', $text)) {
                        $next = $heading->nextSibling;
                        $step_text = '';

                        while ($next && !preg_match('/^h[2-4]$/i', $next->nodeName)) {
                            if ($next->nodeName === 'p') {
                                $step_text .= trim($next->textContent) . ' ';
                            }
                            $next = $next->nextSibling;
                        }

                        $steps[] = [
                            'name' => preg_replace('/^(step\s*\d+[:\.\-\s]*|단계\s*\d+[:\.\-\s]*|\d+\.\s*)/i', '', $text),
                            'text' => trim($step_text),
                        ];
                    }
                }
            }
        }

        return array_slice($steps, 0, 20); // Limit to 20 steps
    }

    /**
     * Get post description
     */
    private function get_post_description($post_id) {
        // Try SEO plugins first
        $desc = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
        if (!empty($desc)) return $desc;

        $desc = get_post_meta($post_id, 'rank_math_description', true);
        if (!empty($desc)) return $desc;

        $desc = get_post_meta($post_id, '_aioseo_description', true);
        if (!empty($desc)) return $desc;

        // Fallback to excerpt
        return get_the_excerpt($post_id);
    }

    /**
     * Get product brand
     */
    private function get_product_brand($product) {
        // Try common brand attributes/taxonomies
        $brand_sources = ['brand', 'pa_brand', 'product_brand'];

        foreach ($brand_sources as $source) {
            $terms = get_the_terms($product->get_id(), $source);
            if ($terms && !is_wp_error($terms)) {
                return $terms[0]->name;
            }
        }

        // Try product attribute
        $brand_attr = $product->get_attribute('brand');
        if ($brand_attr) return $brand_attr;

        return '';
    }

    /**
     * Add custom schema
     */
    public function add_custom_schema($schema) {
        add_filter('wp_bulk_seo_schemas', function($schemas) use ($schema) {
            $schemas[] = $schema;
            return $schemas;
        });
    }

    /**
     * Generate Review schema
     */
    public function generate_review_schema($data) {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'itemReviewed' => [
                '@type' => $data['item_type'] ?? 'Product',
                'name' => $data['item_name'],
            ],
            'author' => [
                '@type' => 'Person',
                'name' => $data['author_name'],
            ],
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $data['rating'],
                'bestRating' => '5',
            ],
            'reviewBody' => $data['review_text'],
            'datePublished' => $data['date'],
        ];
    }

    /**
     * Generate Event schema
     */
    public function generate_event_schema($data) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $data['name'],
            'startDate' => $data['start_date'],
            'location' => [
                '@type' => $data['location_type'] ?? 'Place',
                'name' => $data['location_name'],
            ],
        ];

        if (!empty($data['end_date'])) {
            $schema['endDate'] = $data['end_date'];
        }

        if (!empty($data['description'])) {
            $schema['description'] = $data['description'];
        }

        if (!empty($data['image'])) {
            $schema['image'] = $data['image'];
        }

        if (!empty($data['url'])) {
            $schema['url'] = $data['url'];
        }

        // Online event
        if (isset($data['online']) && $data['online']) {
            $schema['eventAttendanceMode'] = 'https://schema.org/OnlineEventAttendanceMode';
            $schema['location'] = [
                '@type' => 'VirtualLocation',
                'url' => $data['online_url'] ?? $data['url'],
            ];
        }

        // Offers
        if (!empty($data['price'])) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $data['price'],
                'priceCurrency' => $data['currency'] ?? 'USD',
                'availability' => 'https://schema.org/InStock',
                'url' => $data['ticket_url'] ?? $data['url'],
            ];
        }

        return $schema;
    }

    /**
     * Generate Video schema
     */
    public function generate_video_schema($data) {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => $data['name'],
            'description' => $data['description'],
            'thumbnailUrl' => $data['thumbnail'],
            'uploadDate' => $data['upload_date'],
            'duration' => $data['duration'] ?? '',
            'contentUrl' => $data['content_url'] ?? '',
            'embedUrl' => $data['embed_url'] ?? '',
        ];
    }

    /**
     * Generate Course schema
     */
    public function generate_course_schema($data) {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $data['name'],
            'description' => $data['description'],
            'provider' => [
                '@type' => 'Organization',
                'name' => $data['provider_name'] ?? $this->organization['name'],
            ],
            'hasCourseInstance' => [
                '@type' => 'CourseInstance',
                'courseMode' => $data['mode'] ?? 'online',
            ],
        ];
    }

    /**
     * Validate schema
     */
    public function validate_schema($schema) {
        $errors = [];

        // Check required fields
        if (!isset($schema['@context'])) {
            $errors[] = 'Missing @context';
        }

        if (!isset($schema['@type'])) {
            $errors[] = 'Missing @type';
        }

        // Type-specific validation
        $type = $schema['@type'] ?? '';

        switch ($type) {
            case 'Article':
            case 'BlogPosting':
                if (empty($schema['headline'])) {
                    $errors[] = 'Article missing headline';
                }
                if (empty($schema['author'])) {
                    $errors[] = 'Article missing author';
                }
                break;

            case 'Product':
                if (empty($schema['name'])) {
                    $errors[] = 'Product missing name';
                }
                if (empty($schema['offers'])) {
                    $errors[] = 'Product missing offers';
                }
                break;

            case 'FAQPage':
                if (empty($schema['mainEntity'])) {
                    $errors[] = 'FAQPage missing mainEntity';
                }
                break;
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
