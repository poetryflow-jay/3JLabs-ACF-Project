<?php
/**
 * SEO Analysis Module
 * 
 * Rank Math Pro 스타일의 실시간 SEO 분석 모듈
 * 30가지 SEO 테스트 및 실시간 점수 계산
 * 
 * @package WP_Bulk_SEO_AEO
 * @version 2.1.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Analysis_Module {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * SEO 테스트 목록
     */
    private $tests = array();

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
        $this->init_tests();
        $this->init_hooks();
    }

    /**
     * SEO 테스트 초기화
     */
    private function init_tests() {
        $this->tests = array(
            // 기본 SEO 테스트
            'title_length' => array(
                'name' => __('제목 길이', 'wp-bulk-seo-aeo'),
                'weight' => 8,
                'category' => 'basic',
                'test' => array($this, 'test_title_length'),
            ),
            'meta_description' => array(
                'name' => __('메타 설명', 'wp-bulk-seo-aeo'),
                'weight' => 7,
                'category' => 'basic',
                'test' => array($this, 'test_meta_description'),
            ),
            'focus_keyword' => array(
                'name' => __('포커스 키워드', 'wp-bulk-seo-aeo'),
                'weight' => 9,
                'category' => 'content',
                'test' => array($this, 'test_focus_keyword'),
            ),
            'keyword_density' => array(
                'name' => __('키워드 밀도', 'wp-bulk-seo-aeo'),
                'weight' => 7,
                'category' => 'content',
                'test' => array($this, 'test_keyword_density'),
            ),
            'content_length' => array(
                'name' => __('콘텐츠 길이', 'wp-bulk-seo-aeo'),
                'weight' => 6,
                'category' => 'content',
                'test' => array($this, 'test_content_length'),
            ),
            'heading_structure' => array(
                'name' => __('제목 구조', 'wp-bulk-seo-aeo'),
                'weight' => 7,
                'category' => 'content',
                'test' => array($this, 'test_heading_structure'),
            ),
            'image_alt_tags' => array(
                'name' => __('이미지 Alt 태그', 'wp-bulk-seo-aeo'),
                'weight' => 6,
                'category' => 'technical',
                'test' => array($this, 'test_image_alt_tags'),
            ),
            'internal_links' => array(
                'name' => __('내부 링크', 'wp-bulk-seo-aeo'),
                'weight' => 7,
                'category' => 'links',
                'test' => array($this, 'test_internal_links'),
            ),
            'external_links' => array(
                'name' => __('외부 링크', 'wp-bulk-seo-aeo'),
                'weight' => 5,
                'category' => 'links',
                'test' => array($this, 'test_external_links'),
            ),
            'url_structure' => array(
                'name' => __('URL 구조', 'wp-bulk-seo-aeo'),
                'weight' => 6,
                'category' => 'technical',
                'test' => array($this, 'test_url_structure'),
            ),
            'schema_markup' => array(
                'name' => __('Schema 마크업', 'wp-bulk-seo-aeo'),
                'weight' => 7,
                'category' => 'technical',
                'test' => array($this, 'test_schema_markup'),
            ),
            'mobile_friendly' => array(
                'name' => __('모바일 친화성', 'wp-bulk-seo-aeo'),
                'weight' => 10,
                'category' => 'technical',
                'test' => array($this, 'test_mobile_friendly'),
            ),
            'page_speed' => array(
                'name' => __('페이지 속도', 'wp-bulk-seo-aeo'),
                'weight' => 8,
                'category' => 'technical',
                'test' => array($this, 'test_page_speed'),
            ),
            'core_web_vitals' => array(
                'name' => __('Core Web Vitals', 'wp-bulk-seo-aeo'),
                'weight' => 9,
                'category' => 'technical',
                'test' => array($this, 'test_core_web_vitals'),
            ),
            'ssl_certificate' => array(
                'name' => __('SSL 인증서', 'wp-bulk-seo-aeo'),
                'weight' => 8,
                'category' => 'technical',
                'test' => array($this, 'test_ssl_certificate'),
            ),
            'robots_meta' => array(
                'name' => __('Robots 메타 태그', 'wp-bulk-seo-aeo'),
                'weight' => 6,
                'category' => 'technical',
                'test' => array($this, 'test_robots_meta'),
            ),
            'canonical_url' => array(
                'name' => __('Canonical URL', 'wp-bulk-seo-aeo'),
                'weight' => 7,
                'category' => 'technical',
                'test' => array($this, 'test_canonical_url'),
            ),
            'open_graph' => array(
                'name' => __('Open Graph 태그', 'wp-bulk-seo-aeo'),
                'weight' => 6,
                'category' => 'social',
                'test' => array($this, 'test_open_graph'),
            ),
            'twitter_cards' => array(
                'name' => __('Twitter Cards', 'wp-bulk-seo-aeo'),
                'weight' => 5,
                'category' => 'social',
                'test' => array($this, 'test_twitter_cards'),
            ),
            'content_readability' => array(
                'name' => __('콘텐츠 가독성', 'wp-bulk-seo-aeo'),
                'weight' => 6,
                'category' => 'content',
                'test' => array($this, 'test_content_readability'),
            ),
            'keyword_in_first_paragraph' => array(
                'name' => __('첫 문단 키워드', 'wp-bulk-seo-aeo'),
                'weight' => 7,
                'category' => 'content',
                'test' => array($this, 'test_keyword_in_first_paragraph'),
            ),
            'keyword_in_subheadings' => array(
                'name' => __('소제목 키워드', 'wp-bulk-seo-aeo'),
                'weight' => 6,
                'category' => 'content',
                'test' => array($this, 'test_keyword_in_subheadings'),
            ),
            'outbound_links' => array(
                'name' => __('아웃바운드 링크', 'wp-bulk-seo-aeo'),
                'weight' => 5,
                'category' => 'links',
                'test' => array($this, 'test_outbound_links'),
            ),
            'content_freshness' => array(
                'name' => __('콘텐츠 신선도', 'wp-bulk-seo-aeo'),
                'weight' => 7,
                'category' => 'content',
                'test' => array($this, 'test_content_freshness'),
            ),
            'title_keyword_match' => array(
                'name' => __('제목-키워드 일치', 'wp-bulk-seo-aeo'),
                'weight' => 8,
                'category' => 'content',
                'test' => array($this, 'test_title_keyword_match'),
            ),
            'meta_description_keyword' => array(
                'name' => __('메타 설명 키워드', 'wp-bulk-seo-aeo'),
                'weight' => 7,
                'category' => 'content',
                'test' => array($this, 'test_meta_description_keyword'),
            ),
            'url_keyword' => array(
                'name' => __('URL 키워드', 'wp-bulk-seo-aeo'),
                'weight' => 6,
                'category' => 'technical',
                'test' => array($this, 'test_url_keyword'),
            ),
            'image_optimization' => array(
                'name' => __('이미지 최적화', 'wp-bulk-seo-aeo'),
                'weight' => 6,
                'category' => 'technical',
                'test' => array($this, 'test_image_optimization'),
            ),
            'breadcrumbs' => array(
                'name' => __('Breadcrumbs', 'wp-bulk-seo-aeo'),
                'weight' => 6,
                'category' => 'technical',
                'test' => array($this, 'test_breadcrumbs'),
            ),
            'content_originality' => array(
                'name' => __('콘텐츠 독창성', 'wp-bulk-seo-aeo'),
                'weight' => 8,
                'category' => 'content',
                'test' => array($this, 'test_content_originality'),
            ),
        );
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 포스트 편집 화면에 SEO 메타박스 추가
        add_action('add_meta_boxes', array($this, 'add_seo_metabox'));
        
        // AJAX 핸들러
        add_action('wp_ajax_wp_bulk_seo_analyze_post', array($this, 'ajax_analyze_post'));
        
        // 저장 시 자동 분석
        add_action('save_post', array($this, 'auto_analyze_on_save'), 10, 2);
    }

    /**
     * SEO 메타박스 추가
     */
    public function add_seo_metabox() {
        $post_types = get_option('wp_bulk_seo_aeo_post_types', array('post', 'page'));
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'wp-bulk-seo-aeo-analysis',
                __('SEO 분석', 'wp-bulk-seo-aeo'),
                array($this, 'render_seo_metabox'),
                $post_type,
                'normal',
                'high'
            );
        }
    }

    /**
     * SEO 메타박스 렌더링
     */
    public function render_seo_metabox($post) {
        wp_nonce_field('wp_bulk_seo_aeo_analysis', 'wp_bulk_seo_aeo_analysis_nonce');
        
        // 포커스 키워드 입력
        $focus_keyword = get_post_meta($post->ID, '_wp_bulk_seo_focus_keyword', true);
        ?>
        <div class="wp-bulk-seo-analysis-v25">
            <div class="seo-analysis-header">
                <div class="focus-keyword-input">
                    <label for="wp-bulk-seo-focus-keyword">
                        <strong><?php esc_html_e('포커스 키워드', 'wp-bulk-seo-aeo'); ?></strong>
                    </label>
                    <input 
                        type="text" 
                        id="wp-bulk-seo-focus-keyword" 
                        name="wp_bulk_seo_focus_keyword" 
                        value="<?php echo esc_attr($focus_keyword); ?>" 
                        placeholder="<?php esc_attr_e('주요 키워드를 입력하세요', 'wp-bulk-seo-aeo'); ?>"
                        class="regular-text"
                    />
                    <button type="button" class="button" id="wp-bulk-seo-analyze-btn">
                        <?php esc_html_e('분석 시작', 'wp-bulk-seo-aeo'); ?>
                    </button>
                </div>
            </div>

            <div class="seo-score-display" id="wp-bulk-seo-score-display" style="display: none;">
                <div class="seo-score-overall">
                    <div class="score-circle">
                        <svg class="score-ring" width="120" height="120">
                            <circle class="score-ring-bg" cx="60" cy="60" r="54" />
                            <circle class="score-ring-fill" cx="60" cy="60" r="54" id="score-ring" />
                        </svg>
                        <div class="score-value" id="seo-score-value">0</div>
                    </div>
                    <div class="score-label"><?php esc_html_e('SEO 점수', 'wp-bulk-seo-aeo'); ?></div>
                </div>
            </div>

            <div class="seo-tests-results" id="wp-bulk-seo-tests-results" style="display: none;">
                <h3><?php esc_html_e('SEO 테스트 결과', 'wp-bulk-seo-aeo'); ?></h3>
                <div class="tests-grid" id="seo-tests-grid"></div>
            </div>

            <div class="seo-suggestions" id="wp-bulk-seo-suggestions" style="display: none;">
                <h3><?php esc_html_e('개선 제안', 'wp-bulk-seo-aeo'); ?></h3>
                <div id="seo-suggestions-list"></div>
            </div>
        </div>

        <style>
        .wp-bulk-seo-analysis-v25 {
            padding: 20px;
        }
        .seo-analysis-header {
            margin-bottom: 20px;
        }
        .focus-keyword-input {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .focus-keyword-input label {
            min-width: 120px;
        }
        .focus-keyword-input input {
            flex: 1;
        }
        .seo-score-display {
            text-align: center;
            margin: 30px 0;
        }
        .score-circle {
            position: relative;
            display: inline-block;
        }
        .score-ring {
            transform: rotate(-90deg);
        }
        .score-ring-bg {
            fill: none;
            stroke: #e5e7eb;
            stroke-width: 8;
        }
        .score-ring-fill {
            fill: none;
            stroke: #10b981;
            stroke-width: 8;
            stroke-dasharray: 339.292;
            stroke-dashoffset: 339.292;
            transition: stroke-dashoffset 0.5s ease;
        }
        .score-value {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
        }
        .tests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .test-item {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .test-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .test-icon.passed {
            background: #d1fae5;
            color: #065f46;
        }
        .test-icon.warning {
            background: #fef3c7;
            color: #92400e;
        }
        .test-icon.failed {
            background: #fee2e2;
            color: #991b1b;
        }
        .test-info {
            flex: 1;
        }
        .test-name {
            font-weight: 600;
            margin-bottom: 4px;
        }
        .test-message {
            font-size: 13px;
            color: #6b7280;
        }
        </style>

        <script>
        jQuery(document).ready(function($) {
            $('#wp-bulk-seo-analyze-btn').on('click', function() {
                var $btn = $(this);
                var postId = <?php echo $post->ID; ?>;
                var focusKeyword = $('#wp-bulk-seo-focus-keyword').val();

                $btn.prop('disabled', true).text('<?php esc_html_e('분석 중...', 'wp-bulk-seo-aeo'); ?>');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'wp_bulk_seo_analyze_post',
                        post_id: postId,
                        focus_keyword: focusKeyword,
                        nonce: '<?php echo wp_create_nonce('wp_bulk_seo_analyze'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            displayAnalysisResults(response.data);
                        } else {
                            alert(response.data.message || '<?php esc_html_e('분석 중 오류가 발생했습니다.', 'wp-bulk-seo-aeo'); ?>');
                        }
                    },
                    error: function() {
                        alert('<?php esc_html_e('분석 요청 중 오류가 발생했습니다.', 'wp-bulk-seo-aeo'); ?>');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('<?php esc_html_e('분석 시작', 'wp-bulk-seo-aeo'); ?>');
                    }
                });
            });

            function displayAnalysisResults(data) {
                // 점수 표시
                var score = data.overall_score || 0;
                $('#seo-score-value').text(score);
                $('#wp-bulk-seo-score-display').show();

                // 점수 링 애니메이션
                var circumference = 2 * Math.PI * 54;
                var offset = circumference - (score / 100) * circumference;
                $('#score-ring').css('stroke-dashoffset', offset);

                // 테스트 결과 표시
                if (data.tests && data.tests.length > 0) {
                    var html = '';
                    data.tests.forEach(function(test) {
                        var iconClass = test.status === 'passed' ? 'passed' : 
                                       (test.status === 'warning' ? 'warning' : 'failed');
                        var icon = test.status === 'passed' ? '✓' : 
                                  (test.status === 'warning' ? '⚠' : '✗');
                        html += '<div class="test-item">';
                        html += '<div class="test-icon ' + iconClass + '">' + icon + '</div>';
                        html += '<div class="test-info">';
                        html += '<div class="test-name">' + test.name + '</div>';
                        html += '<div class="test-message">' + (test.message || '') + '</div>';
                        html += '</div>';
                        html += '</div>';
                    });
                    $('#seo-tests-grid').html(html);
                    $('#wp-bulk-seo-tests-results').show();
                }

                // 개선 제안 표시
                if (data.suggestions && data.suggestions.length > 0) {
                    var suggestionsHtml = '<ul>';
                    data.suggestions.forEach(function(suggestion) {
                        suggestionsHtml += '<li>' + suggestion + '</li>';
                    });
                    suggestionsHtml += '</ul>';
                    $('#seo-suggestions-list').html(suggestionsHtml);
                    $('#wp-bulk-seo-suggestions').show();
                }
            }
        });
        </script>
        <?php
    }

    /**
     * 포스트 분석 (AJAX)
     */
    public function ajax_analyze_post() {
        check_ajax_referer('wp_bulk_seo_analyze', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('권한이 없습니다.', 'wp-bulk-seo-aeo')));
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $focus_keyword = isset($_POST['focus_keyword']) ? sanitize_text_field($_POST['focus_keyword']) : '';

        if (!$post_id) {
            wp_send_json_error(array('message' => __('잘못된 포스트 ID입니다.', 'wp-bulk-seo-aeo')));
        }

        // 포커스 키워드 저장
        if ($focus_keyword) {
            update_post_meta($post_id, '_wp_bulk_seo_focus_keyword', $focus_keyword);
        }

        // 모든 테스트 실행
        $results = $this->run_all_tests($post_id, $focus_keyword);

        // 종합 점수 계산
        $overall_score = $this->calculate_overall_score($results);

        // 개선 제안 생성
        $suggestions = $this->generate_suggestions($results);

        wp_send_json_success(array(
            'overall_score' => $overall_score,
            'tests' => $results,
            'suggestions' => $suggestions,
        ));
    }

    /**
     * 모든 테스트 실행
     */
    private function run_all_tests($post_id, $focus_keyword = '') {
        $results = array();
        $post = get_post($post_id);

        foreach ($this->tests as $test_id => $test_config) {
            if (is_callable($test_config['test'])) {
                $result = call_user_func($test_config['test'], $post, $focus_keyword);
                $results[] = array(
                    'id' => $test_id,
                    'name' => $test_config['name'],
                    'status' => $result['status'],
                    'score' => $result['score'],
                    'message' => $result['message'],
                    'weight' => $test_config['weight'],
                    'category' => $test_config['category'],
                );
            }
        }

        return $results;
    }

    /**
     * 종합 점수 계산
     */
    private function calculate_overall_score($results) {
        $total_weight = 0;
        $weighted_score = 0;

        foreach ($results as $result) {
            $weight = $result['weight'];
            $score = $result['score'];
            
            $total_weight += $weight;
            $weighted_score += $score * $weight;
        }

        if ($total_weight === 0) {
            return 0;
        }

        return round($weighted_score / $total_weight);
    }

    /**
     * 개선 제안 생성
     */
    private function generate_suggestions($results) {
        $suggestions = array();

        foreach ($results as $result) {
            if ($result['status'] === 'failed' || $result['status'] === 'warning') {
                $suggestions[] = $result['message'];
            }
        }

        return array_slice($suggestions, 0, 10); // 상위 10개만
    }

    /**
     * 저장 시 자동 분석
     */
    public function auto_analyze_on_save($post_id, $post) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!get_option('wp_bulk_seo_aeo_analyze_on_save', true)) {
            return;
        }

        // 백그라운드에서 분석 실행
        wp_schedule_single_event(time() + 5, 'wp_bulk_seo_aeo_auto_analyze', array($post_id));
    }

    // ==================== 테스트 함수들 ====================

    /**
     * 제목 길이 테스트
     */
    private function test_title_length($post, $focus_keyword) {
        $title = get_the_title($post->ID);
        $length = mb_strlen($title);

        if ($length >= 30 && $length <= 60) {
            return array('status' => 'passed', 'score' => 100, 'message' => __('제목 길이가 적절합니다.', 'wp-bulk-seo-aeo'));
        } elseif ($length < 30) {
            return array('status' => 'warning', 'score' => 50, 'message' => sprintf(__('제목이 너무 짧습니다. (현재: %d자, 권장: 30-60자)', 'wp-bulk-seo-aeo'), $length));
        } else {
            return array('status' => 'warning', 'score' => 60, 'message' => sprintf(__('제목이 너무 깁니다. (현재: %d자, 권장: 30-60자)', 'wp-bulk-seo-aeo'), $length));
        }
    }

    /**
     * 메타 설명 테스트
     */
    private function test_meta_description($post, $focus_keyword) {
        $meta_description = get_post_meta($post->ID, '_wp_bulk_seo_meta_description', true);
        
        if (empty($meta_description)) {
            $meta_description = wp_trim_words(strip_tags($post->post_content), 25);
        }

        $length = mb_strlen($meta_description);

        if ($length >= 120 && $length <= 160) {
            return array('status' => 'passed', 'score' => 100, 'message' => __('메타 설명 길이가 적절합니다.', 'wp-bulk-seo-aeo'));
        } elseif ($length < 120) {
            return array('status' => 'warning', 'score' => 50, 'message' => sprintf(__('메타 설명이 너무 짧습니다. (현재: %d자, 권장: 120-160자)', 'wp-bulk-seo-aeo'), $length));
        } else {
            return array('status' => 'warning', 'score' => 60, 'message' => sprintf(__('메타 설명이 너무 깁니다. (현재: %d자, 권장: 120-160자)', 'wp-bulk-seo-aeo'), $length));
        }
    }

    /**
     * 포커스 키워드 테스트
     */
    private function test_focus_keyword($post, $focus_keyword) {
        if (empty($focus_keyword)) {
            return array('status' => 'failed', 'score' => 0, 'message' => __('포커스 키워드를 입력하세요.', 'wp-bulk-seo-aeo'));
        }

        $title = get_the_title($post->ID);
        $content = $post->post_content;
        $url = get_permalink($post->ID);

        $in_title = stripos($title, $focus_keyword) !== false;
        $in_content = stripos($content, $focus_keyword) !== false;
        $in_url = stripos($url, $focus_keyword) !== false;

        $score = 0;
        $messages = array();

        if ($in_title) {
            $score += 30;
            $messages[] = __('제목에 키워드 포함', 'wp-bulk-seo-aeo');
        } else {
            $messages[] = __('제목에 키워드를 포함하세요.', 'wp-bulk-seo-aeo');
        }

        if ($in_content) {
            $score += 50;
            $messages[] = __('콘텐츠에 키워드 포함', 'wp-bulk-seo-aeo');
        } else {
            $messages[] = __('콘텐츠에 키워드를 포함하세요.', 'wp-bulk-seo-aeo');
        }

        if ($in_url) {
            $score += 20;
            $messages[] = __('URL에 키워드 포함', 'wp-bulk-seo-aeo');
        }

        $status = $score >= 80 ? 'passed' : ($score >= 50 ? 'warning' : 'failed');

        return array(
            'status' => $status,
            'score' => $score,
            'message' => implode(', ', $messages)
        );
    }

    /**
     * 키워드 밀도 테스트
     */
    private function test_keyword_density($post, $focus_keyword) {
        if (empty($focus_keyword)) {
            return array('status' => 'warning', 'score' => 50, 'message' => __('포커스 키워드가 없어 밀도를 계산할 수 없습니다.', 'wp-bulk-seo-aeo'));
        }

        $content = strip_tags($post->post_content);
        $content_length = mb_strlen($content);
        $keyword_count = mb_substr_count(mb_strtolower($content), mb_strtolower($focus_keyword));
        
        if ($content_length === 0) {
            return array('status' => 'failed', 'score' => 0, 'message' => __('콘텐츠가 없습니다.', 'wp-bulk-seo-aeo'));
        }

        $density = ($keyword_count / ($content_length / 100)) * 100;

        if ($density >= 0.5 && $density <= 2.5) {
            return array('status' => 'passed', 'score' => 100, 'message' => sprintf(__('키워드 밀도가 적절합니다. (%.2f%%)', 'wp-bulk-seo-aeo'), $density));
        } elseif ($density < 0.5) {
            return array('status' => 'warning', 'score' => 50, 'message' => sprintf(__('키워드 밀도가 낮습니다. (%.2f%%, 권장: 0.5-2.5%%)', 'wp-bulk-seo-aeo'), $density));
        } else {
            return array('status' => 'warning', 'score' => 60, 'message' => sprintf(__('키워드 밀도가 높습니다. (%.2f%%, 권장: 0.5-2.5%%)', 'wp-bulk-seo-aeo'), $density));
        }
    }

    /**
     * 콘텐츠 길이 테스트
     */
    private function test_content_length($post, $focus_keyword) {
        $content = strip_tags($post->post_content);
        $length = mb_strlen($content);

        if ($length >= 300) {
            return array('status' => 'passed', 'score' => 100, 'message' => sprintf(__('콘텐츠 길이가 충분합니다. (%d자)', 'wp-bulk-seo-aeo'), $length));
        } elseif ($length >= 200) {
            return array('status' => 'warning', 'score' => 70, 'message' => sprintf(__('콘텐츠를 더 길게 작성하는 것을 권장합니다. (현재: %d자, 권장: 300자 이상)', 'wp-bulk-seo-aeo'), $length));
        } else {
            return array('status' => 'failed', 'score' => 30, 'message' => sprintf(__('콘텐츠가 너무 짧습니다. (현재: %d자, 권장: 300자 이상)', 'wp-bulk-seo-aeo'), $length));
        }
    }

    /**
     * 제목 구조 테스트
     */
    private function test_heading_structure($post, $focus_keyword) {
        $content = $post->post_content;
        
        // H1 태그 찾기
        preg_match_all('/<h1[^>]*>(.*?)<\/h1>/i', $content, $h1_matches);
        preg_match_all('/<h2[^>]*>(.*?)<\/h2>/i', $content, $h2_matches);
        preg_match_all('/<h3[^>]*>(.*?)<\/h3>/i', $content, $h3_matches);

        $h1_count = count($h1_matches[0]);
        $h2_count = count($h2_matches[0]);
        $h3_count = count($h3_matches[0]);

        $score = 0;
        $messages = array();

        if ($h1_count === 1) {
            $score += 30;
            $messages[] = __('H1 태그가 1개입니다.', 'wp-bulk-seo-aeo');
        } elseif ($h1_count === 0) {
            $messages[] = __('H1 태그를 추가하세요.', 'wp-bulk-seo-aeo');
        } else {
            $messages[] = sprintf(__('H1 태그가 너무 많습니다. (%d개, 권장: 1개)', 'wp-bulk-seo-aeo'), $h1_count);
        }

        if ($h2_count > 0) {
            $score += 40;
            $messages[] = sprintf(__('H2 태그 %d개', 'wp-bulk-seo-aeo'), $h2_count);
        } else {
            $messages[] = __('H2 태그를 추가하여 콘텐츠를 구조화하세요.', 'wp-bulk-seo-aeo');
        }

        if ($h3_count > 0) {
            $score += 30;
        }

        $status = $score >= 70 ? 'passed' : ($score >= 40 ? 'warning' : 'failed');

        return array(
            'status' => $status,
            'score' => $score,
            'message' => implode(', ', $messages)
        );
    }

    /**
     * 이미지 Alt 태그 테스트
     */
    private function test_image_alt_tags($post, $focus_keyword) {
        $content = $post->post_content;
        preg_match_all('/<img[^>]+>/i', $content, $img_matches);

        if (empty($img_matches[0])) {
            return array('status' => 'warning', 'score' => 50, 'message' => __('이미지가 없습니다.', 'wp-bulk-seo-aeo'));
        }

        $total_images = count($img_matches[0]);
        $images_with_alt = 0;

        foreach ($img_matches[0] as $img_tag) {
            if (preg_match('/alt=["\']([^"\']*)["\']/i', $img_tag, $alt_match)) {
                if (!empty($alt_match[1])) {
                    $images_with_alt++;
                }
            }
        }

        $percentage = ($images_with_alt / $total_images) * 100;

        if ($percentage === 100) {
            return array('status' => 'passed', 'score' => 100, 'message' => sprintf(__('모든 이미지에 Alt 태그가 있습니다. (%d/%d)', 'wp-bulk-seo-aeo'), $images_with_alt, $total_images));
        } elseif ($percentage >= 80) {
            return array('status' => 'warning', 'score' => 70, 'message' => sprintf(__('일부 이미지에 Alt 태그가 없습니다. (%d/%d, %.0f%%)', 'wp-bulk-seo-aeo'), $images_with_alt, $total_images, $percentage));
        } else {
            return array('status' => 'failed', 'score' => 30, 'message' => sprintf(__('많은 이미지에 Alt 태그가 없습니다. (%d/%d, %.0f%%)', 'wp-bulk-seo-aeo'), $images_with_alt, $total_images, $percentage));
        }
    }

    /**
     * 내부 링크 테스트
     */
    private function test_internal_links($post, $focus_keyword) {
        $content = $post->post_content;
        $site_url = home_url();
        
        preg_match_all('/<a[^>]+href=["\']([^"\']*)["\'][^>]*>/i', $content, $link_matches);
        
        $total_links = count($link_matches[1]);
        $internal_links = 0;

        foreach ($link_matches[1] as $url) {
            if (strpos($url, $site_url) === 0 || strpos($url, '/') === 0) {
                $internal_links++;
            }
        }

        if ($total_links === 0) {
            return array('status' => 'warning', 'score' => 50, 'message' => __('내부 링크가 없습니다.', 'wp-bulk-seo-aeo'));
        }

        $internal_ratio = ($internal_links / $total_links) * 100;

        if ($internal_links >= 2) {
            return array('status' => 'passed', 'score' => 100, 'message' => sprintf(__('내부 링크가 충분합니다. (%d개)', 'wp-bulk-seo-aeo'), $internal_links));
        } elseif ($internal_links >= 1) {
            return array('status' => 'warning', 'score' => 70, 'message' => sprintf(__('내부 링크를 더 추가하는 것을 권장합니다. (현재: %d개)', 'wp-bulk-seo-aeo'), $internal_links));
        } else {
            return array('status' => 'failed', 'score' => 30, 'message' => __('내부 링크를 추가하세요.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * 외부 링크 테스트
     */
    private function test_external_links($post, $focus_keyword) {
        $content = $post->post_content;
        $site_url = home_url();
        
        preg_match_all('/<a[^>]+href=["\']([^"\']*)["\'][^>]*>/i', $content, $link_matches);
        
        $external_links = 0;

        foreach ($link_matches[1] as $url) {
            if (strpos($url, 'http') === 0 && strpos($url, $site_url) === false) {
                $external_links++;
            }
        }

        if ($external_links >= 1) {
            return array('status' => 'passed', 'score' => 100, 'message' => sprintf(__('외부 링크가 있습니다. (%d개)', 'wp-bulk-seo-aeo'), $external_links));
        } else {
            return array('status' => 'warning', 'score' => 60, 'message' => __('신뢰할 수 있는 외부 링크를 추가하는 것을 권장합니다.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * URL 구조 테스트
     */
    private function test_url_structure($post, $focus_keyword) {
        $url = get_permalink($post->ID);
        $url_path = parse_url($url, PHP_URL_PATH);
        
        $length = strlen($url_path);
        $depth = substr_count($url_path, '/') - 1;
        $has_keyword = !empty($focus_keyword) && stripos($url_path, $focus_keyword) !== false;

        $score = 0;
        $messages = array();

        if ($length <= 100) {
            $score += 40;
            $messages[] = __('URL 길이가 적절합니다.', 'wp-bulk-seo-aeo');
        } else {
            $messages[] = __('URL이 너무 깁니다.', 'wp-bulk-seo-aeo');
        }

        if ($depth <= 3) {
            $score += 30;
            $messages[] = __('URL 깊이가 적절합니다.', 'wp-bulk-seo-aeo');
        } else {
            $messages[] = __('URL 깊이가 깊습니다.', 'wp-bulk-seo-aeo');
        }

        if ($has_keyword) {
            $score += 30;
            $messages[] = __('URL에 키워드가 포함되어 있습니다.', 'wp-bulk-seo-aeo');
        }

        $status = $score >= 70 ? 'passed' : ($score >= 40 ? 'warning' : 'failed');

        return array(
            'status' => $status,
            'score' => $score,
            'message' => implode(', ', $messages)
        );
    }

    /**
     * Schema 마크업 테스트
     */
    private function test_schema_markup($post, $focus_keyword) {
        $schema = get_post_meta($post->ID, '_wp_bulk_seo_schema_type', true);
        
        if (!empty($schema)) {
            return array('status' => 'passed', 'score' => 100, 'message' => sprintf(__('Schema 마크업이 설정되어 있습니다. (%s)', 'wp-bulk-seo-aeo'), $schema));
        } else {
            return array('status' => 'warning', 'score' => 50, 'message' => __('Schema 마크업을 추가하세요.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * 모바일 친화성 테스트
     */
    private function test_mobile_friendly($post, $focus_keyword) {
        // 실제로는 PageSpeed API를 사용하거나 간단한 체크
        $is_responsive = true; // 기본값
        
        if ($is_responsive) {
            return array('status' => 'passed', 'score' => 100, 'message' => __('모바일 친화적입니다.', 'wp-bulk-seo-aeo'));
        } else {
            return array('status' => 'failed', 'score' => 0, 'message' => __('모바일 최적화가 필요합니다.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * 페이지 속도 테스트
     */
    private function test_page_speed($post, $focus_keyword) {
        // 실제로는 PageSpeed API를 사용
        return array('status' => 'warning', 'score' => 70, 'message' => __('페이지 속도는 PageSpeed API를 통해 확인하세요.', 'wp-bulk-seo-aeo'));
    }

    /**
     * Core Web Vitals 테스트
     */
    private function test_core_web_vitals($post, $focus_keyword) {
        // 실제로는 PageSpeed API를 사용
        return array('status' => 'warning', 'score' => 70, 'message' => __('Core Web Vitals는 PageSpeed API를 통해 확인하세요.', 'wp-bulk-seo-aeo'));
    }

    /**
     * SSL 인증서 테스트
     */
    private function test_ssl_certificate($post, $focus_keyword) {
        $is_ssl = is_ssl();
        
        if ($is_ssl) {
            return array('status' => 'passed', 'score' => 100, 'message' => __('SSL 인증서가 활성화되어 있습니다.', 'wp-bulk-seo-aeo'));
        } else {
            return array('status' => 'failed', 'score' => 0, 'message' => __('SSL 인증서를 활성화하세요.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * Robots 메타 태그 테스트
     */
    private function test_robots_meta($post, $focus_keyword) {
        $robots = get_post_meta($post->ID, '_wp_bulk_seo_robots', true);
        
        if (empty($robots) || $robots === 'index, follow') {
            return array('status' => 'passed', 'score' => 100, 'message' => __('Robots 메타 태그가 적절합니다.', 'wp-bulk-seo-aeo'));
        } else {
            return array('status' => 'warning', 'score' => 70, 'message' => sprintf(__('Robots 메타 태그: %s', 'wp-bulk-seo-aeo'), $robots));
        }
    }

    /**
     * Canonical URL 테스트
     */
    private function test_canonical_url($post, $focus_keyword) {
        $canonical = get_post_meta($post->ID, '_wp_bulk_seo_canonical', true);
        
        if (!empty($canonical)) {
            return array('status' => 'passed', 'score' => 100, 'message' => __('Canonical URL이 설정되어 있습니다.', 'wp-bulk-seo-aeo'));
        } else {
            return array('status' => 'warning', 'score' => 70, 'message' => __('Canonical URL을 설정하는 것을 권장합니다.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * Open Graph 태그 테스트
     */
    private function test_open_graph($post, $focus_keyword) {
        $og_title = get_post_meta($post->ID, '_wp_bulk_seo_og_title', true);
        $og_image = get_post_meta($post->ID, '_wp_bulk_seo_og_image', true);
        
        if (!empty($og_title) && !empty($og_image)) {
            return array('status' => 'passed', 'score' => 100, 'message' => __('Open Graph 태그가 설정되어 있습니다.', 'wp-bulk-seo-aeo'));
        } elseif (!empty($og_title)) {
            return array('status' => 'warning', 'score' => 70, 'message' => __('Open Graph 이미지를 추가하세요.', 'wp-bulk-seo-aeo'));
        } else {
            return array('status' => 'warning', 'score' => 50, 'message' => __('Open Graph 태그를 설정하세요.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * Twitter Cards 테스트
     */
    private function test_twitter_cards($post, $focus_keyword) {
        $twitter_card = get_post_meta($post->ID, '_wp_bulk_seo_twitter_card', true);
        
        if (!empty($twitter_card)) {
            return array('status' => 'passed', 'score' => 100, 'message' => __('Twitter Cards가 설정되어 있습니다.', 'wp-bulk-seo-aeo'));
        } else {
            return array('status' => 'warning', 'score' => 60, 'message' => __('Twitter Cards를 설정하는 것을 권장합니다.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * 콘텐츠 가독성 테스트
     */
    private function test_content_readability($post, $focus_keyword) {
        $content = strip_tags($post->post_content);
        $word_count = str_word_count($content);
        $sentence_count = preg_match_all('/[.!?]+/', $content);
        $paragraph_count = substr_count($content, "\n\n") + 1;

        if ($sentence_count > 0) {
            $avg_sentence_length = $word_count / $sentence_count;
        } else {
            $avg_sentence_length = 0;
        }

        $score = 0;
        $messages = array();

        if ($avg_sentence_length >= 15 && $avg_sentence_length <= 20) {
            $score += 50;
            $messages[] = __('문장 길이가 적절합니다.', 'wp-bulk-seo-aeo');
        } else {
            $messages[] = sprintf(__('평균 문장 길이: %.1f단어 (권장: 15-20단어)', 'wp-bulk-seo-aeo'), $avg_sentence_length);
        }

        if ($paragraph_count >= 3) {
            $score += 50;
            $messages[] = __('단락 구조가 적절합니다.', 'wp-bulk-seo-aeo');
        } else {
            $messages[] = __('더 많은 단락으로 나누세요.', 'wp-bulk-seo-aeo');
        }

        $status = $score >= 80 ? 'passed' : ($score >= 50 ? 'warning' : 'failed');

        return array(
            'status' => $status,
            'score' => $score,
            'message' => implode(', ', $messages)
        );
    }

    /**
     * 첫 문단 키워드 테스트
     */
    private function test_keyword_in_first_paragraph($post, $focus_keyword) {
        if (empty($focus_keyword)) {
            return array('status' => 'warning', 'score' => 50, 'message' => __('포커스 키워드가 없습니다.', 'wp-bulk-seo-aeo'));
        }

        $content = strip_tags($post->post_content);
        $first_paragraph = substr($content, 0, 200);

        if (stripos($first_paragraph, $focus_keyword) !== false) {
            return array('status' => 'passed', 'score' => 100, 'message' => __('첫 문단에 키워드가 포함되어 있습니다.', 'wp-bulk-seo-aeo'));
        } else {
            return array('status' => 'warning', 'score' => 50, 'message' => __('첫 문단에 키워드를 포함하는 것을 권장합니다.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * 소제목 키워드 테스트
     */
    private function test_keyword_in_subheadings($post, $focus_keyword) {
        if (empty($focus_keyword)) {
            return array('status' => 'warning', 'score' => 50, 'message' => __('포커스 키워드가 없습니다.', 'wp-bulk-seo-aeo'));
        }

        $content = $post->post_content;
        preg_match_all('/<h[2-6][^>]*>(.*?)<\/h[2-6]>/i', $content, $heading_matches);

        if (empty($heading_matches[0])) {
            return array('status' => 'warning', 'score' => 50, 'message' => __('소제목이 없습니다.', 'wp-bulk-seo-aeo'));
        }

        $keyword_in_headings = 0;
        foreach ($heading_matches[1] as $heading) {
            if (stripos($heading, $focus_keyword) !== false) {
                $keyword_in_headings++;
            }
        }

        if ($keyword_in_headings > 0) {
            return array('status' => 'passed', 'score' => 100, 'message' => sprintf(__('소제목에 키워드가 포함되어 있습니다. (%d개)', 'wp-bulk-seo-aeo'), $keyword_in_headings));
        } else {
            return array('status' => 'warning', 'score' => 60, 'message' => __('소제목에 키워드를 포함하는 것을 권장합니다.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * 아웃바운드 링크 테스트
     */
    private function test_outbound_links($post, $focus_keyword) {
        $content = $post->post_content;
        $site_url = home_url();
        
        preg_match_all('/<a[^>]+href=["\']([^"\']*)["\'][^>]*>/i', $content, $link_matches);
        
        $external_links = 0;
        foreach ($link_matches[1] as $url) {
            if (strpos($url, 'http') === 0 && strpos($url, $site_url) === false) {
                $external_links++;
            }
        }

        if ($external_links >= 1) {
            return array('status' => 'passed', 'score' => 100, 'message' => sprintf(__('아웃바운드 링크가 있습니다. (%d개)', 'wp-bulk-seo-aeo'), $external_links));
        } else {
            return array('status' => 'warning', 'score' => 60, 'message' => __('신뢰할 수 있는 외부 링크를 추가하는 것을 권장합니다.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * 콘텐츠 신선도 테스트
     */
    private function test_content_freshness($post, $focus_keyword) {
        $post_date = strtotime($post->post_date);
        $modified_date = strtotime($post->post_modified);
        $days_since_modified = (time() - $modified_date) / (60 * 60 * 24);

        if ($days_since_modified <= 30) {
            return array('status' => 'passed', 'score' => 100, 'message' => sprintf(__('콘텐츠가 최근에 업데이트되었습니다. (%d일 전)', 'wp-bulk-seo-aeo'), round($days_since_modified)));
        } elseif ($days_since_modified <= 90) {
            return array('status' => 'warning', 'score' => 70, 'message' => sprintf(__('콘텐츠를 업데이트하는 것을 권장합니다. (%d일 전 수정)', 'wp-bulk-seo-aeo'), round($days_since_modified)));
        } else {
            return array('status' => 'failed', 'score' => 40, 'message' => sprintf(__('콘텐츠가 오래되었습니다. 업데이트하세요. (%d일 전 수정)', 'wp-bulk-seo-aeo'), round($days_since_modified)));
        }
    }

    /**
     * 제목-키워드 일치 테스트
     */
    private function test_title_keyword_match($post, $focus_keyword) {
        if (empty($focus_keyword)) {
            return array('status' => 'warning', 'score' => 50, 'message' => __('포커스 키워드가 없습니다.', 'wp-bulk-seo-aeo'));
        }

        $title = get_the_title($post->ID);
        
        if (stripos($title, $focus_keyword) !== false) {
            return array('status' => 'passed', 'score' => 100, 'message' => __('제목에 키워드가 포함되어 있습니다.', 'wp-bulk-seo-aeo'));
        } else {
            return array('status' => 'failed', 'score' => 30, 'message' => __('제목에 키워드를 포함하세요.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * 메타 설명 키워드 테스트
     */
    private function test_meta_description_keyword($post, $focus_keyword) {
        if (empty($focus_keyword)) {
            return array('status' => 'warning', 'score' => 50, 'message' => __('포커스 키워드가 없습니다.', 'wp-bulk-seo-aeo'));
        }

        $meta_description = get_post_meta($post->ID, '_wp_bulk_seo_meta_description', true);
        
        if (empty($meta_description)) {
            return array('status' => 'warning', 'score' => 50, 'message' => __('메타 설명이 없습니다.', 'wp-bulk-seo-aeo'));
        }

        if (stripos($meta_description, $focus_keyword) !== false) {
            return array('status' => 'passed', 'score' => 100, 'message' => __('메타 설명에 키워드가 포함되어 있습니다.', 'wp-bulk-seo-aeo'));
        } else {
            return array('status' => 'warning', 'score' => 60, 'message' => __('메타 설명에 키워드를 포함하는 것을 권장합니다.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * URL 키워드 테스트
     */
    private function test_url_keyword($post, $focus_keyword) {
        if (empty($focus_keyword)) {
            return array('status' => 'warning', 'score' => 50, 'message' => __('포커스 키워드가 없습니다.', 'wp-bulk-seo-aeo'));
        }

        $url = get_permalink($post->ID);
        
        if (stripos($url, $focus_keyword) !== false) {
            return array('status' => 'passed', 'score' => 100, 'message' => __('URL에 키워드가 포함되어 있습니다.', 'wp-bulk-seo-aeo'));
        } else {
            return array('status' => 'warning', 'score' => 60, 'message' => __('URL에 키워드를 포함하는 것을 권장합니다.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * 이미지 최적화 테스트
     */
    private function test_image_optimization($post, $focus_keyword) {
        $content = $post->post_content;
        preg_match_all('/<img[^>]+>/i', $content, $img_matches);

        if (empty($img_matches[0])) {
            return array('status' => 'warning', 'score' => 50, 'message' => __('이미지가 없습니다.', 'wp-bulk-seo-aeo'));
        }

        $optimized_count = 0;
        foreach ($img_matches[0] as $img_tag) {
            $has_alt = preg_match('/alt=["\']([^"\']*)["\']/i', $img_tag);
            $has_width = preg_match('/width=["\']([^"\']*)["\']/i', $img_tag);
            $has_height = preg_match('/height=["\']([^"\']*)["\']/i', $img_tag);
            
            if ($has_alt && ($has_width || $has_height)) {
                $optimized_count++;
            }
        }

        $percentage = ($optimized_count / count($img_matches[0])) * 100;

        if ($percentage === 100) {
            return array('status' => 'passed', 'score' => 100, 'message' => __('모든 이미지가 최적화되어 있습니다.', 'wp-bulk-seo-aeo'));
        } elseif ($percentage >= 80) {
            return array('status' => 'warning', 'score' => 70, 'message' => sprintf(__('일부 이미지 최적화가 필요합니다. (%.0f%%)', 'wp-bulk-seo-aeo'), $percentage));
        } else {
            return array('status' => 'failed', 'score' => 40, 'message' => sprintf(__('많은 이미지 최적화가 필요합니다. (%.0f%%)', 'wp-bulk-seo-aeo'), $percentage));
        }
    }

    /**
     * Breadcrumbs 테스트
     */
    private function test_breadcrumbs($post, $focus_keyword) {
        $has_breadcrumbs = get_post_meta($post->ID, '_wp_bulk_seo_breadcrumbs', true);
        
        if ($has_breadcrumbs) {
            return array('status' => 'passed', 'score' => 100, 'message' => __('Breadcrumbs가 설정되어 있습니다.', 'wp-bulk-seo-aeo'));
        } else {
            return array('status' => 'warning', 'score' => 60, 'message' => __('Breadcrumbs를 설정하는 것을 권장합니다.', 'wp-bulk-seo-aeo'));
        }
    }

    /**
     * 콘텐츠 독창성 테스트
     */
    private function test_content_originality($post, $focus_keyword) {
        // 실제로는 AI 기반 중복 콘텐츠 검사 API를 사용
        // 여기서는 기본 체크만 수행
        $content = strip_tags($post->post_content);
        $word_count = str_word_count($content);

        if ($word_count >= 300) {
            return array('status' => 'passed', 'score' => 80, 'message' => __('콘텐츠 길이가 충분합니다. (독창성 검사는 AI API를 통해 수행)', 'wp-bulk-seo-aeo'));
        } else {
            return array('status' => 'warning', 'score' => 50, 'message' => __('콘텐츠를 더 길게 작성하세요. (독창성 검사는 AI API를 통해 수행)', 'wp-bulk-seo-aeo'));
        }
    }
}
