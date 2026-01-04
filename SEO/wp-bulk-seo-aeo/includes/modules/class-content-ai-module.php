<?php
/**
 * Content AI Module
 * 
 * Rank Math Pro 스타일의 AI 기반 콘텐츠 최적화 모듈
 * 키워드 제안, 콘텐츠 제안, 관련 키워드 추천, 스마트 링크 제안
 * 
 * @package WP_Bulk_SEO_AEO
 * @version 2.1.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Content_AI_Module {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 인스턴스 반환
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // AJAX 핸들러
        add_action('wp_ajax_wp_bulk_seo_suggest_keywords', array($this, 'ajax_suggest_keywords'));
        add_action('wp_ajax_wp_bulk_seo_suggest_content', array($this, 'ajax_suggest_content'));
        add_action('wp_ajax_wp_bulk_seo_suggest_links', array($this, 'ajax_suggest_links'));
        add_action('wp_ajax_wp_bulk_seo_optimize_title', array($this, 'ajax_optimize_title'));
        add_action('wp_ajax_wp_bulk_seo_optimize_meta', array($this, 'ajax_optimize_meta'));
    }

    /**
     * 키워드 제안 (AJAX)
     */
    public function ajax_suggest_keywords() {
        check_ajax_referer('wp_bulk_seo_content_ai', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('권한이 없습니다.', 'wp-bulk-seo-aeo')));
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $focus_keyword = isset($_POST['focus_keyword']) ? sanitize_text_field($_POST['focus_keyword']) : '';

        if (!$post_id) {
            wp_send_json_error(array('message' => __('잘못된 포스트 ID입니다.', 'wp-bulk-seo-aeo')));
        }

        $post = get_post($post_id);
        $content = strip_tags($post->post_content);
        $title = get_the_title($post_id);

        // LSI 키워드 추출
        $lsi_keywords = $this->extract_lsi_keywords($content, $focus_keyword);

        // 관련 키워드 제안
        $related_keywords = $this->suggest_related_keywords($focus_keyword, $content);

        // 경쟁 키워드 분석
        $competitor_keywords = $this->analyze_competitor_keywords($title, $content);

        wp_send_json_success(array(
            'lsi_keywords' => $lsi_keywords,
            'related_keywords' => $related_keywords,
            'competitor_keywords' => $competitor_keywords,
        ));
    }

    /**
     * 콘텐츠 제안 (AJAX)
     */
    public function ajax_suggest_content() {
        check_ajax_referer('wp_bulk_seo_content_ai', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('권한이 없습니다.', 'wp-bulk-seo-aeo')));
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $focus_keyword = isset($_POST['focus_keyword']) ? sanitize_text_field($_POST['focus_keyword']) : '';
        $suggestion_type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'improve';

        if (!$post_id) {
            wp_send_json_error(array('message' => __('잘못된 포스트 ID입니다.', 'wp-bulk-seo-aeo')));
        }

        $post = get_post($post_id);
        $content = $post->post_content;

        $suggestions = array();

        switch ($suggestion_type) {
            case 'improve':
                $suggestions = $this->suggest_content_improvements($content, $focus_keyword);
                break;
            case 'expand':
                $suggestions = $this->suggest_content_expansion($content, $focus_keyword);
                break;
            case 'optimize':
                $suggestions = $this->suggest_optimization($content, $focus_keyword);
                break;
        }

        wp_send_json_success(array('suggestions' => $suggestions));
    }

    /**
     * 링크 제안 (AJAX)
     */
    public function ajax_suggest_links() {
        check_ajax_referer('wp_bulk_seo_content_ai', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('권한이 없습니다.', 'wp-bulk-seo-aeo')));
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';

        if (!$post_id) {
            wp_send_json_error(array('message' => __('잘못된 포스트 ID입니다.', 'wp-bulk-seo-aeo')));
        }

        // 내부 링크 제안
        $internal_links = $this->suggest_internal_links($post_id, $keyword);

        // 외부 링크 제안
        $external_links = $this->suggest_external_links($keyword);

        wp_send_json_success(array(
            'internal_links' => $internal_links,
            'external_links' => $external_links,
        ));
    }

    /**
     * 제목 최적화 (AJAX)
     */
    public function ajax_optimize_title() {
        check_ajax_referer('wp_bulk_seo_content_ai', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('권한이 없습니다.', 'wp-bulk-seo-aeo')));
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $focus_keyword = isset($_POST['focus_keyword']) ? sanitize_text_field($_POST['focus_keyword']) : '';

        if (!$post_id) {
            wp_send_json_error(array('message' => __('잘못된 포스트 ID입니다.', 'wp-bulk-seo-aeo')));
        }

        $current_title = get_the_title($post_id);
        $optimized_titles = $this->optimize_title($current_title, $focus_keyword);

        wp_send_json_success(array('titles' => $optimized_titles));
    }

    /**
     * 메타 설명 최적화 (AJAX)
     */
    public function ajax_optimize_meta() {
        check_ajax_referer('wp_bulk_seo_content_ai', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('권한이 없습니다.', 'wp-bulk-seo-aeo')));
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $focus_keyword = isset($_POST['focus_keyword']) ? sanitize_text_field($_POST['focus_keyword']) : '';

        if (!$post_id) {
            wp_send_json_error(array('message' => __('잘못된 포스트 ID입니다.', 'wp-bulk-seo-aeo')));
        }

        $post = get_post($post_id);
        $current_meta = get_post_meta($post_id, '_wp_bulk_seo_meta_description', true);
        
        if (empty($current_meta)) {
            $current_meta = wp_trim_words(strip_tags($post->post_content), 25);
        }

        $optimized_meta = $this->optimize_meta_description($current_meta, $focus_keyword, $post);

        wp_send_json_success(array('meta_description' => $optimized_meta));
    }

    /**
     * LSI 키워드 추출
     */
    private function extract_lsi_keywords($content, $focus_keyword) {
        // 간단한 LSI 키워드 추출 (실제로는 AI API 사용)
        $words = str_word_count(strtolower($content), 1);
        $word_freq = array_count_values($words);
        arsort($word_freq);

        $lsi_keywords = array();
        $stop_words = array('the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by');

        foreach ($word_freq as $word => $freq) {
            if ($freq >= 3 && !in_array($word, $stop_words) && strlen($word) > 3) {
                if (stripos($word, $focus_keyword) === false) {
                    $lsi_keywords[] = array(
                        'keyword' => $word,
                        'frequency' => $freq,
                        'relevance' => $this->calculate_relevance($word, $focus_keyword)
                    );
                }
            }
            if (count($lsi_keywords) >= 10) {
                break;
            }
        }

        return $lsi_keywords;
    }

    /**
     * 관련 키워드 제안
     */
    private function suggest_related_keywords($focus_keyword, $content) {
        // 실제로는 Google Keyword Planner API 또는 AI 서비스 사용
        // 여기서는 기본 제안만 제공
        
        $related = array();
        
        // 콘텐츠에서 유사 키워드 찾기
        $words = explode(' ', $focus_keyword);
        foreach ($words as $word) {
            if (stripos($content, $word) !== false) {
                $related[] = array(
                    'keyword' => $word,
                    'relevance' => 'high',
                    'search_volume' => 'N/A'
                );
            }
        }

        return $related;
    }

    /**
     * 경쟁 키워드 분석
     */
    private function analyze_competitor_keywords($title, $content) {
        // 실제로는 경쟁사 분석 API 사용
        return array();
    }

    /**
     * 콘텐츠 개선 제안
     */
    private function suggest_content_improvements($content, $focus_keyword) {
        $suggestions = array();

        // 키워드 밀도 체크
        $keyword_count = mb_substr_count(mb_strtolower($content), mb_strtolower($focus_keyword));
        $word_count = str_word_count(strip_tags($content));
        $density = ($keyword_count / max($word_count, 1)) * 100;

        if ($density < 0.5) {
            $suggestions[] = array(
                'type' => 'keyword_density',
                'message' => __('키워드 밀도가 낮습니다. 콘텐츠에 키워드를 더 자연스럽게 추가하세요.', 'wp-bulk-seo-aeo'),
                'priority' => 'high'
            );
        }

        // 제목 구조 체크
        preg_match_all('/<h[2-6][^>]*>/i', $content, $headings);
        if (count($headings[0]) < 2) {
            $suggestions[] = array(
                'type' => 'heading_structure',
                'message' => __('더 많은 소제목을 추가하여 콘텐츠를 구조화하세요.', 'wp-bulk-seo-aeo'),
                'priority' => 'medium'
            );
        }

        // 이미지 Alt 태그 체크
        preg_match_all('/<img[^>]+>/i', $content, $images);
        $images_without_alt = 0;
        foreach ($images[0] as $img) {
            if (!preg_match('/alt=["\']([^"\']*)["\']/i', $img)) {
                $images_without_alt++;
            }
        }

        if ($images_without_alt > 0) {
            $suggestions[] = array(
                'type' => 'image_alt',
                'message' => sprintf(__('%d개의 이미지에 Alt 태그를 추가하세요.', 'wp-bulk-seo-aeo'), $images_without_alt),
                'priority' => 'high'
            );
        }

        return $suggestions;
    }

    /**
     * 콘텐츠 확장 제안
     */
    private function suggest_content_expansion($content, $focus_keyword) {
        $suggestions = array();
        $word_count = str_word_count(strip_tags($content));

        if ($word_count < 300) {
            $suggestions[] = array(
                'type' => 'length',
                'message' => __('콘텐츠를 더 길게 작성하세요. (권장: 300자 이상)', 'wp-bulk-seo-aeo'),
                'sections' => array(
                    __('FAQ 섹션 추가', 'wp-bulk-seo-aeo'),
                    __('관련 주제 섹션 추가', 'wp-bulk-seo-aeo'),
                    __('예시 및 사례 추가', 'wp-bulk-seo-aeo'),
                )
            );
        }

        return $suggestions;
    }

    /**
     * 최적화 제안
     */
    private function suggest_optimization($content, $focus_keyword) {
        $suggestions = array();

        // 첫 문단에 키워드 포함 여부
        $first_paragraph = substr(strip_tags($content), 0, 200);
        if (stripos($first_paragraph, $focus_keyword) === false) {
            $suggestions[] = array(
                'type' => 'first_paragraph',
                'message' => __('첫 문단에 키워드를 포함하세요.', 'wp-bulk-seo-aeo'),
                'priority' => 'high'
            );
        }

        return $suggestions;
    }

    /**
     * 내부 링크 제안
     */
    private function suggest_internal_links($post_id, $keyword) {
        $suggestions = array();

        // 관련 포스트 검색
        $related_posts = get_posts(array(
            's' => $keyword,
            'post_type' => array('post', 'page'),
            'posts_per_page' => 5,
            'post__not_in' => array($post_id),
            'post_status' => 'publish'
        ));

        foreach ($related_posts as $related_post) {
            $suggestions[] = array(
                'url' => get_permalink($related_post->ID),
                'title' => $related_post->post_title,
                'relevance' => $this->calculate_relevance($related_post->post_title, $keyword),
                'anchor_text' => $this->suggest_anchor_text($related_post->post_title, $keyword)
            );
        }

        return $suggestions;
    }

    /**
     * 외부 링크 제안
     */
    private function suggest_external_links($keyword) {
        // 실제로는 신뢰할 수 있는 소스 데이터베이스 사용
        return array();
    }

    /**
     * 제목 최적화
     */
    private function optimize_title($current_title, $focus_keyword) {
        $optimized = array();

        // 키워드가 포함된 제목
        if (stripos($current_title, $focus_keyword) === false) {
            $optimized[] = array(
                'title' => $focus_keyword . ' - ' . $current_title,
                'score' => 90,
                'reason' => __('키워드가 포함되었습니다.', 'wp-bulk-seo-aeo')
            );
        }

        // 길이 최적화
        $length = mb_strlen($current_title);
        if ($length < 30) {
            $optimized[] = array(
                'title' => $current_title . ' | ' . get_bloginfo('name'),
                'score' => 85,
                'reason' => __('제목 길이를 늘렸습니다.', 'wp-bulk-seo-aeo')
            );
        } elseif ($length > 60) {
            $shortened = mb_substr($current_title, 0, 57) . '...';
            $optimized[] = array(
                'title' => $shortened,
                'score' => 80,
                'reason' => __('제목 길이를 줄였습니다.', 'wp-bulk-seo-aeo')
            );
        }

        return $optimized;
    }

    /**
     * 메타 설명 최적화
     */
    private function optimize_meta_description($current_meta, $focus_keyword, $post) {
        $length = mb_strlen($current_meta);

        // 키워드 포함 여부
        if (stripos($current_meta, $focus_keyword) === false) {
            $current_meta = $focus_keyword . '. ' . $current_meta;
        }

        // 길이 조정
        if ($length < 120) {
            $excerpt = wp_trim_words(strip_tags($post->post_content), 20);
            $current_meta = $current_meta . ' ' . $excerpt;
        } elseif ($length > 160) {
            $current_meta = mb_substr($current_meta, 0, 157) . '...';
        }

        return mb_substr($current_meta, 0, 160);
    }

    /**
     * 관련성 계산
     */
    private function calculate_relevance($text, $keyword) {
        $similarity = 0;
        similar_text(strtolower($text), strtolower($keyword), $similarity);
        return round($similarity);
    }

    /**
     * 앵커 텍스트 제안
     */
    private function suggest_anchor_text($title, $keyword) {
        if (stripos($title, $keyword) !== false) {
            return $keyword;
        }
        return $title;
    }
}
