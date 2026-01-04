<?php
/**
 * WP Bulk SEO - Auto Optimizer
 *
 * 자동 SEO 최적화 엔진
 * - AI 기반 제목/메타 설명 최적화
 * - 자동 Schema 마크업 생성
 * - 이미지 최적화 제안
 * - 내부 링크 제안
 * - 키워드 밀도 최적화
 *
 * @package WP_Bulk_SEO
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Auto_Optimizer {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * AI Engine instance
     */
    private $ai_engine;

    /**
     * Analyzer instance
     */
    private $analyzer;

    /**
     * Scorer instance
     */
    private $scorer;

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
        $this->init_components();
    }

    /**
     * Initialize components
     */
    private function init_components() {
        if (class_exists('WP_Bulk_SEO_AI_Engine')) {
            $this->ai_engine = new WP_Bulk_SEO_AI_Engine();
        }

        if (class_exists('WP_Bulk_SEO_Analyzer')) {
            $this->analyzer = new WP_Bulk_SEO_Analyzer();
        }

        if (class_exists('WP_Bulk_SEO_Scorer')) {
            $this->scorer = new WP_Bulk_SEO_Scorer();
        }
    }

    /**
     * Auto-optimize a post
     *
     * @param int $post_id Post ID
     * @param array $options Optimization options
     * @return array Optimization results
     */
    public function auto_optimize($post_id, $options = []) {
        $defaults = [
            'optimize_title' => true,
            'optimize_meta' => true,
            'optimize_headings' => false,
            'add_schema' => true,
            'suggest_images' => true,
            'suggest_links' => true,
            'optimize_keywords' => true,
            'use_ai' => true,
        ];

        $options = wp_parse_args($options, $defaults);

        $post = get_post($post_id);
        if (!$post) {
            return ['success' => false, 'error' => 'Post not found'];
        }

        // Analyze current state
        $analysis = $this->analyzer->analyze_post($post_id);
        $score_before = $this->scorer->calculate_score($analysis);

        $optimizations = [];
        $improvements = [];

        // 1. Title optimization
        if ($options['optimize_title']) {
            $title_result = $this->optimize_title($post_id, $options['use_ai']);
            if ($title_result['optimized']) {
                $optimizations['title'] = $title_result;
                $improvements[] = '제목 최적화 완료';
            }
        }

        // 2. Meta description optimization
        if ($options['optimize_meta']) {
            $meta_result = $this->optimize_meta_description($post_id, $options['use_ai']);
            if ($meta_result['optimized']) {
                $optimizations['meta_description'] = $meta_result;
                $improvements[] = '메타 설명 최적화 완료';
            }
        }

        // 3. Schema markup
        if ($options['add_schema']) {
            $schema_result = $this->add_schema_markup($post_id);
            if ($schema_result['added']) {
                $optimizations['schema'] = $schema_result;
                $improvements[] = 'Schema 마크업 추가 완료';
            }
        }

        // 4. Image optimization suggestions
        if ($options['suggest_images']) {
            $image_suggestions = $this->suggest_image_optimizations($post_id, $analysis);
            if (!empty($image_suggestions)) {
                $optimizations['image_suggestions'] = $image_suggestions;
            }
        }

        // 5. Internal linking suggestions
        if ($options['suggest_links']) {
            $link_suggestions = $this->suggest_internal_links($post_id, $analysis);
            if (!empty($link_suggestions)) {
                $optimizations['link_suggestions'] = $link_suggestions;
            }
        }

        // 6. Keyword optimization
        if ($options['optimize_keywords']) {
            $keyword_result = $this->optimize_keyword_usage($post_id, $analysis);
            if ($keyword_result['optimized']) {
                $optimizations['keywords'] = $keyword_result;
                $improvements[] = '키워드 사용 최적화 완료';
            }
        }

        // Re-analyze after optimization
        $new_analysis = $this->analyzer->analyze_post($post_id);
        $score_after = $this->scorer->calculate_score($new_analysis);

        $score_improvement = $score_after['overall_score'] - $score_before['overall_score'];

        return [
            'success' => true,
            'post_id' => $post_id,
            'optimizations' => $optimizations,
            'improvements' => $improvements,
            'score_before' => $score_before['overall_score'],
            'score_after' => $score_after['overall_score'],
            'score_improvement' => $score_improvement,
            'grade_before' => $score_before['grade'],
            'grade_after' => $score_after['grade'],
        ];
    }

    /**
     * Optimize title
     */
    private function optimize_title($post_id, $use_ai = true) {
        $post = get_post($post_id);
        $current_title = $post->post_title;
        $focus_keyword = $this->get_focus_keyword($post_id);

        // Check if title needs optimization
        $title_length = mb_strlen($current_title);
        $needs_optimization = false;

        if ($title_length < 30 || $title_length > 70) {
            $needs_optimization = true;
        }

        if (!empty($focus_keyword) && stripos($current_title, $focus_keyword) === false) {
            $needs_optimization = true;
        }

        if (!$needs_optimization) {
            return ['optimized' => false, 'title' => $current_title, 'reason' => 'Already optimized'];
        }

        // Use AI if available
        if ($use_ai && $this->ai_engine) {
            $optimized_title = $this->ai_engine->optimize_title($current_title, $focus_keyword, $post->post_content);

            if ($optimized_title && $optimized_title !== $current_title) {
                // Save as SEO title
                update_post_meta($post_id, '_wp_bulk_seo_title', $optimized_title);
                return [
                    'optimized' => true,
                    'title' => $optimized_title,
                    'original' => $current_title,
                ];
            }
        }

        // Fallback: Manual optimization
        $optimized_title = $this->manually_optimize_title($current_title, $focus_keyword);
        if ($optimized_title !== $current_title) {
            update_post_meta($post_id, '_wp_bulk_seo_title', $optimized_title);
            return [
                'optimized' => true,
                'title' => $optimized_title,
                'original' => $current_title,
            ];
        }

        return ['optimized' => false, 'title' => $current_title];
    }

    /**
     * Manually optimize title
     */
    private function manually_optimize_title($title, $keyword = '') {
        $title = trim($title);
        $length = mb_strlen($title);

        // If too short, add site name
        if ($length < 30) {
            $site_name = get_bloginfo('name');
            $title = $title . ' - ' . $site_name;
        }

        // If too long, truncate
        if ($length > 70) {
            $title = mb_substr($title, 0, 67) . '...';
        }

        // Add keyword if missing and provided
        if (!empty($keyword) && stripos($title, $keyword) === false) {
            $keyword_length = mb_strlen($keyword);
            $remaining = 60 - $keyword_length;
            if ($remaining > 0) {
                $title = $keyword . ' - ' . mb_substr($title, 0, $remaining);
            }
        }

        return $title;
    }

    /**
     * Optimize meta description
     */
    private function optimize_meta_description($post_id, $use_ai = true) {
        $post = get_post($post_id);
        $current_desc = get_post_meta($post_id, '_wp_bulk_seo_description', true);

        if (empty($current_desc)) {
            $current_desc = wp_trim_words($post->post_content, 30, '...');
        }

        $desc_length = mb_strlen($current_desc);
        $needs_optimization = false;

        if ($desc_length < 120 || $desc_length > 160) {
            $needs_optimization = true;
        }

        if (!$needs_optimization) {
            return ['optimized' => false, 'description' => $current_desc];
        }

        // Use AI if available
        if ($use_ai && $this->ai_engine) {
            $focus_keyword = $this->get_focus_keyword($post_id);
            $optimized_desc = $this->ai_engine->optimize_meta_description($current_desc, $focus_keyword, $post->post_content);

            if ($optimized_desc && $optimized_desc !== $current_desc) {
                update_post_meta($post_id, '_wp_bulk_seo_description', $optimized_desc);
                return [
                    'optimized' => true,
                    'description' => $optimized_desc,
                    'original' => $current_desc,
                ];
            }
        }

        // Fallback: Manual optimization
        $optimized_desc = $this->manually_optimize_meta_description($current_desc, $post->post_content);
        if ($optimized_desc !== $current_desc) {
            update_post_meta($post_id, '_wp_bulk_seo_description', $optimized_desc);
            return [
                'optimized' => true,
                'description' => $optimized_desc,
                'original' => $current_desc,
            ];
        }

        return ['optimized' => false, 'description' => $current_desc];
    }

    /**
     * Manually optimize meta description
     */
    private function manually_optimize_meta_description($desc, $content) {
        $desc = trim($desc);
        $length = mb_strlen($desc);

        // If too short, extract from content
        if ($length < 120) {
            $extracted = wp_trim_words($content, 25, '...');
            if (mb_strlen($extracted) >= 120 && mb_strlen($extracted) <= 160) {
                return $extracted;
            }
        }

        // If too long, truncate
        if ($length > 160) {
            return mb_substr($desc, 0, 157) . '...';
        }

        return $desc;
    }

    /**
     * Add schema markup
     */
    private function add_schema_markup($post_id) {
        if (!class_exists('WP_Bulk_SEO_Schema_Generator')) {
            return ['added' => false];
        }

        $schema_generator = WP_Bulk_SEO_Schema_Generator::instance();
        $schema = $schema_generator->generate_for_post($post_id);

        if ($schema) {
            update_post_meta($post_id, '_wp_bulk_seo_schema', wp_json_encode($schema));
            return ['added' => true, 'schema' => $schema];
        }

        return ['added' => false];
    }

    /**
     * Suggest image optimizations
     */
    private function suggest_image_optimizations($post_id, $analysis) {
        $suggestions = [];

        if (!isset($analysis['images']) || empty($analysis['images'])) {
            return $suggestions;
        }

        $images = $analysis['images'];
        $missing_alt = $analysis['images_missing_alt'] ?? 0;
        $missing_dimensions = count($images) - ($analysis['images_with_dimensions'] ?? 0);

        if ($missing_alt > 0) {
            $suggestions[] = [
                'type' => 'missing_alt',
                'count' => $missing_alt,
                'message' => sprintf('%d개의 이미지에 alt 텍스트가 없습니다.', $missing_alt),
                'priority' => 'high',
            ];
        }

        if ($missing_dimensions > 0) {
            $suggestions[] = [
                'type' => 'missing_dimensions',
                'count' => $missing_dimensions,
                'message' => sprintf('%d개의 이미지에 width/height 속성이 없습니다.', $missing_dimensions),
                'priority' => 'medium',
            ];
        }

        // Check for large images
        foreach ($images as $img) {
            if (isset($img['src'])) {
                $image_id = attachment_url_to_postid($img['src']);
                if ($image_id) {
                    $file_path = get_attached_file($image_id);
                    if ($file_path && file_exists($file_path)) {
                        $file_size = filesize($file_path);
                        if ($file_size > 200 * 1024) { // > 200KB
                            $suggestions[] = [
                                'type' => 'large_image',
                                'image' => $img['src'],
                                'size_kb' => round($file_size / 1024, 1),
                                'message' => sprintf('이미지가 큽니다 (%s KB). 압축을 권장합니다.', round($file_size / 1024, 1)),
                                'priority' => 'medium',
                            ];
                        }
                    }
                }
            }
        }

        return $suggestions;
    }

    /**
     * Suggest internal links
     */
    private function suggest_internal_links($post_id, $analysis) {
        $suggestions = [];
        $internal_link_count = $analysis['internal_link_count'] ?? 0;

        // Check if more internal links are needed
        if ($internal_link_count < 5) {
            // Find related posts
            $related_posts = $this->find_related_posts($post_id, 10);

            if (!empty($related_posts)) {
                $suggestions[] = [
                    'type' => 'add_internal_links',
                    'current_count' => $internal_link_count,
                    'recommended_count' => 5,
                    'related_posts' => array_slice($related_posts, 0, 5),
                    'message' => sprintf('내부 링크가 %d개입니다. 관련 포스트로 링크를 추가하는 것을 권장합니다.', $internal_link_count),
                    'priority' => 'high',
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Find related posts
     */
    private function find_related_posts($post_id, $limit = 10) {
        $post = get_post($post_id);
        $categories = wp_get_post_categories($post_id);
        $tags = wp_get_post_tags($post_id, ['fields' => 'ids']);

        $args = [
            'post_type' => $post->post_type,
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'post__not_in' => [$post_id],
            'orderby' => 'relevance',
        ];

        if (!empty($categories)) {
            $args['category__in'] = $categories;
        }

        if (!empty($tags)) {
            $args['tag__in'] = $tags;
        }

        $related = get_posts($args);

        return array_map(function($p) {
            return [
                'id' => $p->ID,
                'title' => $p->post_title,
                'url' => get_permalink($p->ID),
            ];
        }, $related);
    }

    /**
     * Optimize keyword usage
     */
    private function optimize_keyword_usage($post_id, $analysis) {
        $focus_keyword = $this->get_focus_keyword($post_id);
        if (empty($focus_keyword)) {
            return ['optimized' => false];
        }

        $keyword_density = $analysis['keyword_density'] ?? 0;
        $word_count = $analysis['word_count'] ?? 0;

        $suggestions = [];

        // Check keyword density
        if ($keyword_density < 1) {
            $suggestions[] = [
                'type' => 'low_density',
                'current' => $keyword_density,
                'recommended' => '1-2.5%',
                'message' => '키워드 밀도가 낮습니다. 콘텐츠에 키워드를 더 자연스럽게 추가하세요.',
                'priority' => 'medium',
            ];
        } elseif ($keyword_density > 3) {
            $suggestions[] = [
                'type' => 'high_density',
                'current' => $keyword_density,
                'recommended' => '1-2.5%',
                'message' => '키워드 밀도가 높습니다. 키워드 스터핑을 피하세요.',
                'priority' => 'high',
            ];
        }

        // Check keyword in title
        if (isset($analysis['title']) && stripos($analysis['title'], $focus_keyword) === false) {
            $suggestions[] = [
                'type' => 'keyword_in_title',
                'message' => '제목에 포커스 키워드를 포함하는 것을 권장합니다.',
                'priority' => 'high',
            ];
        }

        // Check keyword in H1
        if (isset($analysis['h1']) && is_array($analysis['h1'])) {
            $has_keyword_in_h1 = false;
            foreach ($analysis['h1'] as $h1) {
                if (stripos($h1, $focus_keyword) !== false) {
                    $has_keyword_in_h1 = true;
                    break;
                }
            }

            if (!$has_keyword_in_h1) {
                $suggestions[] = [
                    'type' => 'keyword_in_h1',
                    'message' => 'H1 태그에 포커스 키워드를 포함하는 것을 권장합니다.',
                    'priority' => 'high',
                ];
            }
        }

        if (!empty($suggestions)) {
            update_post_meta($post_id, '_wp_bulk_seo_keyword_suggestions', $suggestions);
            return ['optimized' => true, 'suggestions' => $suggestions];
        }

        return ['optimized' => false];
    }

    /**
     * Get focus keyword
     */
    private function get_focus_keyword($post_id) {
        // Check various SEO plugins
        $keyword = get_post_meta($post_id, '_yoast_wpseo_focuskw', true);
        if ($keyword) return $keyword;

        $keyword = get_post_meta($post_id, 'rank_math_focus_keyword', true);
        if ($keyword) return $keyword;

        $keyword = get_post_meta($post_id, '_aioseo_keywords', true);
        if ($keyword) return $keyword;

        $keyword = get_post_meta($post_id, '_wp_bulk_seo_focus_keyword', true);
        if ($keyword) return $keyword;

        return '';
    }

    /**
     * Bulk auto-optimize
     */
    public function bulk_auto_optimize($post_ids, $options = []) {
        $results = [];
        $total = count($post_ids);
        $success = 0;
        $failed = 0;

        foreach ($post_ids as $post_id) {
            try {
                $result = $this->auto_optimize($post_id, $options);
                if ($result['success']) {
                    $success++;
                } else {
                    $failed++;
                }
                $results[$post_id] = $result;
            } catch (Exception $e) {
                $failed++;
                $results[$post_id] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'total' => $total,
            'success' => $success,
            'failed' => $failed,
            'results' => $results,
        ];
    }
}
