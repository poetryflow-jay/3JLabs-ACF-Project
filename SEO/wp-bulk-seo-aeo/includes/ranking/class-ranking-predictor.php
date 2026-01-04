<?php
/**
 * Ranking Predictor Class
 *
 * Predicts ranking improvement based on bulk optimizations
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Ranking_Predictor {

    /**
     * Predict ranking improvement
     *
     * @param int $post_id Post ID
     * @param array $optimizations Planned optimizations
     * @return array Prediction
     */
    public function predict_improvement($post_id, $optimizations = []) {
        // Get current SEO score
        $plugin = WP_Bulk_SEO_AEO::instance();
        $current_score = $this->get_current_score($post_id);

        // Calculate expected score after optimizations
        $expected_score = $this->calculate_expected_score($current_score, $optimizations);

        // Estimate ranking improvement
        $improvement = $this->estimate_ranking_change($current_score, $expected_score);

        return [
            'current_score' => $current_score,
            'expected_score' => $expected_score,
            'score_improvement' => $expected_score - $current_score,
            'predicted_position_change' => $improvement['position_change'],
            'predicted_position' => $improvement['new_position'],
            'confidence' => $improvement['confidence'],
            'factors' => $this->get_impact_factors($optimizations),
        ];
    }

    /**
     * Get current SEO score
     *
     * @param int $post_id Post ID
     * @return float Current score
     */
    private function get_current_score($post_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_scores';

        $score = $wpdb->get_var($wpdb->prepare(
            "SELECT overall_score FROM $table WHERE post_id = %d",
            $post_id
        ));

        return floatval($score ?? 0);
    }

    /**
     * Calculate expected score after optimizations
     *
     * @param float $current_score Current score
     * @param array $optimizations Optimizations
     * @return float Expected score
     */
    private function calculate_expected_score($current_score, $optimizations) {
        $expected = $current_score;
        $max_improvement = 100 - $current_score; // Maximum possible improvement

        // Weight factors for each optimization type
        $weights = [
            'meta_tags' => 0.15,      // 15% potential improvement
            'title' => 0.10,          // 10% potential improvement
            'content' => 0.20,        // 20% potential improvement
            'images' => 0.05,         // 5% potential improvement
            'internal_links' => 0.10, // 10% potential improvement
            'schema' => 0.10,         // 10% potential improvement
            'technical' => 0.15,     // 15% potential improvement
            'page_speed' => 0.10,     // 10% potential improvement
            'mobile' => 0.05,         // 5% potential improvement
        ];

        foreach ($optimizations as $opt) {
            if (isset($weights[$opt])) {
                $expected += $max_improvement * $weights[$opt];
            }
        }

        // Cap at 100
        return min(100, $expected);
    }

    /**
     * Estimate ranking position change
     *
     * @param float $current_score Current score
     * @param float $expected_score Expected score
     * @return array Improvement estimate
     */
    private function estimate_ranking_change($current_score, $expected_score) {
        $score_diff = $expected_score - $current_score;

        // Rough estimation: 1 point score improvement ≈ 0.5-2 position improvement
        // This is a simplified model - real ranking depends on competition
        $position_change = round($score_diff * 1.5);

        // Confidence based on score improvement magnitude
        $confidence = 'low';
        if ($score_diff >= 20) {
            $confidence = 'high';
        } elseif ($score_diff >= 10) {
            $confidence = 'medium';
        }

        // Estimate new position (assuming current position is unknown, use average)
        $current_position = 50; // Default assumption
        $new_position = max(1, $current_position - $position_change);

        return [
            'position_change' => $position_change,
            'new_position' => $new_position,
            'confidence' => $confidence,
        ];
    }

    /**
     * Get impact factors for optimizations
     *
     * @param array $optimizations Optimizations
     * @return array Impact factors
     */
    private function get_impact_factors($optimizations) {
        $factors = [];

        $impact_map = [
            'meta_tags' => [
                'impact' => 'high',
                'description' => '메타 태그 최적화는 CTR 향상에 직접적인 영향을 미칩니다.',
                'estimated_improvement' => '5-15점',
            ],
            'title' => [
                'impact' => 'high',
                'description' => '제목 최적화는 검색 결과에서의 클릭률을 크게 향상시킬 수 있습니다.',
                'estimated_improvement' => '3-10점',
            ],
            'content' => [
                'impact' => 'very_high',
                'description' => '콘텐츠 품질 개선은 Google의 핵심 랭킹 요소입니다.',
                'estimated_improvement' => '10-20점',
            ],
            'schema' => [
                'impact' => 'medium',
                'description' => 'Schema 마크업은 리치 결과 표시 가능성을 높입니다.',
                'estimated_improvement' => '3-8점',
            ],
            'page_speed' => [
                'impact' => 'high',
                'description' => '페이지 속도는 Core Web Vitals의 핵심 요소입니다.',
                'estimated_improvement' => '5-15점',
            ],
        ];

        foreach ($optimizations as $opt) {
            if (isset($impact_map[$opt])) {
                $factors[] = array_merge(
                    ['type' => $opt],
                    $impact_map[$opt]
                );
            }
        }

        return $factors;
    }

    /**
     * Predict ranking for keyword
     *
     * @param string $keyword Keyword
     * @param string $url URL
     * @param array $optimizations Optimizations
     * @return array Prediction
     */
    public function predict_keyword_ranking($keyword, $url, $optimizations = []) {
        // Get current ranking
        $current_ranking = $this->get_current_ranking($keyword, $url);

        // Get competition data
        $competition = $this->get_competition_data($keyword);

        // Calculate predicted improvement
        $prediction = $this->predict_improvement_by_keyword($keyword, $url, $optimizations, $competition);

        return [
            'keyword' => $keyword,
            'url' => $url,
            'current_position' => $current_ranking['position'] ?? null,
            'predicted_position' => $prediction['new_position'],
            'position_change' => $prediction['position_change'],
            'confidence' => $prediction['confidence'],
            'competition_level' => $competition['level'] ?? 'unknown',
            'estimated_time' => $prediction['estimated_time'],
        ];
    }

    /**
     * Get current ranking
     *
     * @param string $keyword Keyword
     * @param string $url URL
     * @return array Current ranking
     */
    private function get_current_ranking($keyword, $url) {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_ranking_history';

        $ranking = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table 
            WHERE keyword = %s 
            AND url = %s 
            ORDER BY checked_at DESC 
            LIMIT 1",
            $keyword,
            $url
        ), ARRAY_A);

        return $ranking ?: ['position' => null];
    }

    /**
     * Get competition data
     *
     * @param string $keyword Keyword
     * @return array Competition data
     */
    private function get_competition_data($keyword) {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_competition';

        $competition = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE keyword = %s ORDER BY updated_at DESC LIMIT 1",
            $keyword
        ), ARRAY_A);

        return $competition ?: ['level' => 'medium', 'score' => 50];
    }

    /**
     * Predict improvement by keyword
     *
     * @param string $keyword Keyword
     * @param string $url URL
     * @param array $optimizations Optimizations
     * @param array $competition Competition data
     * @return array Prediction
     */
    private function predict_improvement_by_keyword($keyword, $url, $optimizations, $competition) {
        // Base improvement from optimizations
        $base_improvement = count($optimizations) * 2; // Rough estimate: 2 positions per optimization

        // Adjust based on competition
        $competition_score = floatval($competition['score'] ?? 50);
        $competition_factor = 1 - ($competition_score / 100); // Higher competition = less improvement

        $position_change = round($base_improvement * $competition_factor);

        // Estimate time to see results (in days)
        $estimated_time = $this->estimate_time_to_results($optimizations, $competition_score);

        return [
            'new_position' => null, // Would need current position to calculate
            'position_change' => $position_change,
            'confidence' => $this->calculate_confidence($optimizations, $competition_score),
            'estimated_time' => $estimated_time,
        ];
    }

    /**
     * Estimate time to see results
     *
     * @param array $optimizations Optimizations
     * @param float $competition_score Competition score
     * @return string Estimated time
     */
    private function estimate_time_to_results($optimizations, $competition_score) {
        // Base time: 7-30 days depending on optimization type
        $base_days = 14;

        // Technical optimizations show faster results
        $technical_optimizations = ['page_speed', 'mobile', 'technical'];
        $has_technical = !empty(array_intersect($optimizations, $technical_optimizations));

        if ($has_technical) {
            $base_days = 7;
        }

        // High competition = longer time
        if ($competition_score > 70) {
            $base_days += 14;
        } elseif ($competition_score > 50) {
            $base_days += 7;
        }

        return sprintf('%d-%d일', $base_days, $base_days + 14);
    }

    /**
     * Calculate confidence level
     *
     * @param array $optimizations Optimizations
     * @param float $competition_score Competition score
     * @return string Confidence
     */
    private function calculate_confidence($optimizations, $competition_score) {
        $confidence_score = 0;

        // More optimizations = higher confidence
        $confidence_score += count($optimizations) * 10;

        // Lower competition = higher confidence
        $confidence_score += (100 - $competition_score) * 0.3;

        if ($confidence_score >= 70) return 'high';
        if ($confidence_score >= 40) return 'medium';
        return 'low';
    }
}
