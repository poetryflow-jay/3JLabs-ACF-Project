<?php
/**
 * Dashboard View
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

$plugin = WP_Bulk_SEO_AEO::instance();
?>

<div class="wrap wp-bulk-seo-dashboard-v25">
    <div class="wp-bulk-seo-header-v25">
        <h1><?php esc_html_e('WP Bulk SEO & AEO Dashboard', 'wp-bulk-seo-aeo'); ?></h1>
        <div class="header-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page=wp-bulk-seo-aeo-settings')); ?>" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-secondary">
                <?php esc_html_e('설정', 'wp-bulk-seo-aeo'); ?>
            </a>
        </div>
    </div>

    <div class="wp-bulk-seo-aeo-welcome">
        <h2><?php esc_html_e('환영합니다!', 'wp-bulk-seo-aeo'); ?></h2>
        <p><?php esc_html_e('WP Bulk SEO & AEO는 Google 알고리즘 유출 문서와 Airtable 데이터베이스를 기반으로 한 고급 SEO 최적화 플러그인입니다.', 'wp-bulk-seo-aeo'); ?></p>
    </div>

    <?php
    global $wpdb;
    $table = $wpdb->prefix . 'bulk_seo_aeo_scores';
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
    $avg_score = $wpdb->get_var("SELECT AVG(overall_score) FROM $table");
    $keywords_table = $wpdb->prefix . 'bulk_seo_aeo_keywords';
    $keyword_count = $wpdb->get_var("SELECT COUNT(*) FROM $keywords_table WHERE is_active = 1");
    $total_pages = wp_count_posts()->publish + wp_count_posts('page')->publish;
    ?>

    <!-- SEO 점수 카드 v25 -->
    <div class="wp-bulk-seo-score-grid-v25">
        <div class="wp-bulk-seo-score-card-v25 overall">
            <div class="score-icon">📊</div>
            <div class="score-value"><?php echo $avg_score ? esc_html(round($avg_score)) : '0'; ?></div>
            <div class="score-label"><?php esc_html_e('종합 SEO 점수', 'wp-bulk-seo-aeo'); ?></div>
            <div class="score-trend up">
                <span>↑</span>
                <span><?php esc_html_e('최적화 진행 중', 'wp-bulk-seo-aeo'); ?></span>
            </div>
        </div>
        <div class="wp-bulk-seo-score-card-v25 content">
            <div class="score-icon">📝</div>
            <div class="score-value"><?php echo esc_html($total_pages); ?></div>
            <div class="score-label"><?php esc_html_e('총 페이지', 'wp-bulk-seo-aeo'); ?></div>
        </div>
        <div class="wp-bulk-seo-score-card-v25 technical">
            <div class="score-icon">✅</div>
            <div class="score-value"><?php echo esc_html($count); ?></div>
            <div class="score-label"><?php esc_html_e('분석 완료', 'wp-bulk-seo-aeo'); ?></div>
        </div>
        <div class="wp-bulk-seo-score-card-v25 user-experience">
            <div class="score-icon">🔑</div>
            <div class="score-value"><?php echo esc_html($keyword_count); ?></div>
            <div class="score-label"><?php esc_html_e('모니터링 키워드', 'wp-bulk-seo-aeo'); ?></div>
        </div>
    </div>

    <!-- 빠른 시작 액션 카드 v25 -->
    <div class="wp-bulk-seo-suggestions-grid-v25">
        <div class="wp-bulk-seo-suggestion-card-v25 priority-high">
            <div class="suggestion-icon">⚡</div>
            <div class="suggestion-title"><?php esc_html_e('1-Click SEO', 'wp-bulk-seo-aeo'); ?></div>
            <div class="suggestion-description"><?php esc_html_e('기본 SEO 설정을 자동으로 적용합니다.', 'wp-bulk-seo-aeo'); ?></div>
            <div class="suggestion-impact"><?php esc_html_e('높은 영향', 'wp-bulk-seo-aeo'); ?></div>
            <div class="suggestion-actions">
                <button class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-primary" id="wp-bulk-seo-aeo-1click">
                    <?php esc_html_e('지금 시작', 'wp-bulk-seo-aeo'); ?>
                </button>
            </div>
        </div>
        <div class="wp-bulk-seo-suggestion-card-v25 priority-medium">
            <div class="suggestion-icon">🔍</div>
            <div class="suggestion-title"><?php esc_html_e('Bulk Analyzer', 'wp-bulk-seo-aeo'); ?></div>
            <div class="suggestion-description"><?php esc_html_e('모든 페이지를 일괄 분석합니다.', 'wp-bulk-seo-aeo'); ?></div>
            <div class="suggestion-impact"><?php esc_html_e('중간 영향', 'wp-bulk-seo-aeo'); ?></div>
            <div class="suggestion-actions">
                <a href="<?php echo esc_url(admin_url('admin.php?page=wp-bulk-seo-aeo-analyzer')); ?>" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-secondary">
                    <?php esc_html_e('분석 시작', 'wp-bulk-seo-aeo'); ?>
                </a>
            </div>
        </div>
        <div class="wp-bulk-seo-suggestion-card-v25 priority-low">
            <div class="suggestion-icon">⚙️</div>
            <div class="suggestion-title"><?php esc_html_e('Settings', 'wp-bulk-seo-aeo'); ?></div>
            <div class="suggestion-description"><?php esc_html_e('API 키 및 플러그인 설정을 구성합니다.', 'wp-bulk-seo-aeo'); ?></div>
            <div class="suggestion-impact"><?php esc_html_e('낮은 영향', 'wp-bulk-seo-aeo'); ?></div>
            <div class="suggestion-actions">
                <a href="<?php echo esc_url(admin_url('admin.php?page=wp-bulk-seo-aeo-settings')); ?>" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-secondary">
                    <?php esc_html_e('설정 열기', 'wp-bulk-seo-aeo'); ?>
                </a>
            </div>
        </div>
    </div>

    <div class="wp-bulk-seo-aeo-ranking-dashboard">
        <h2><?php esc_html_e('순위 모니터링', 'wp-bulk-seo-aeo'); ?></h2>
        
        <?php
        $ranking_monitor = new WP_Bulk_SEO_AEO_Ranking_Monitor();
        $keywords_table = $wpdb->prefix . 'bulk_seo_aeo_keywords';
        $active_keywords = $wpdb->get_results("SELECT * FROM $keywords_table WHERE is_active = 1 LIMIT 10", ARRAY_A);
        ?>

        <?php if (empty($active_keywords)): ?>
            <p><?php esc_html_e('모니터링 중인 키워드가 없습니다. 키워드를 추가하세요.', 'wp-bulk-seo-aeo'); ?></p>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('키워드', 'wp-bulk-seo-aeo'); ?></th>
                        <th><?php esc_html_e('현재 순위', 'wp-bulk-seo-aeo'); ?></th>
                        <th><?php esc_html_e('최고 순위', 'wp-bulk-seo-aeo'); ?></th>
                        <th><?php esc_html_e('트렌드', 'wp-bulk-seo-aeo'); ?></th>
                        <th><?php esc_html_e('시간대별 평균', 'wp-bulk-seo-aeo'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ranking_predictor = new WP_Bulk_SEO_AEO_Ranking_Predictor();
                    foreach ($active_keywords as $keyword_data):
                        $trends = $ranking_monitor->get_ranking_trends($keyword_data['id']);
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html($keyword_data['keyword']); ?></strong></td>
                            <td>
                                <?php
                                if ($trends['current_position']):
                                    echo '<span class="position-badge">' . esc_html($trends['current_position']) . '위</span>';
                                else:
                                    echo '<span class="no-data">-</span>';
                                endif;
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($trends['best_position']):
                                    echo '<span class="best-position">' . esc_html($trends['best_position']) . '위</span>';
                                else:
                                    echo '-';
                                endif;
                                ?>
                            </td>
                            <td>
                                <?php
                                $trend = $trends['trend'] ?? 'stable';
                                $trend_class = $trend === 'improving' ? 'trend-up' : ($trend === 'declining' ? 'trend-down' : 'trend-stable');
                                $trend_icon = $trend === 'improving' ? '↑' : ($trend === 'declining' ? '↓' : '→');
                                echo '<span class="trend-indicator ' . esc_attr($trend_class) . '">' . esc_html($trend_icon) . ' ' . esc_html($trend) . '</span>';
                                ?>
                            </td>
                            <td>
                                <?php
                                if (!empty($trends['by_time_of_day'])):
                                    $times = $trends['by_time_of_day'];
                                    echo '오전: ' . ($times['morning']['average'] ?? '-') . ' | ';
                                    echo '오후: ' . ($times['afternoon']['average'] ?? '-') . ' | ';
                                    echo '저녁: ' . ($times['evening']['average'] ?? '-');
                                else:
                                    echo '-';
                                endif;
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="wp-bulk-seo-aeo-predictions">
        <h2><?php esc_html_e('순위 예측', 'wp-bulk-seo-aeo'); ?></h2>
        <p><?php esc_html_e('벌크 최적화 적용 시 예상되는 순위 상승을 확인하세요.', 'wp-bulk-seo-aeo'); ?></p>
        
        <div class="prediction-calculator">
            <button type="button" class="button button-primary" id="wp-bulk-seo-aeo-calculate-prediction">
                <?php esc_html_e('순위 예측 계산', 'wp-bulk-seo-aeo'); ?>
            </button>
            <div id="wp-bulk-seo-aeo-prediction-results" style="display: none; margin-top: 20px;"></div>
        </div>
    </div>
</div>

<style>
.wp-bulk-seo-aeo-dashboard {
    max-width: 1200px;
}
.wp-bulk-seo-aeo-welcome {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
.wp-bulk-seo-aeo-quick-actions {
    margin: 20px 0;
}
.action-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 15px;
}
.action-card {
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
.action-card h3 {
    margin-top: 0;
}
.wp-bulk-seo-aeo-stats {
    margin: 20px 0;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 15px;
}
.stat-card {
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    text-align: center;
}
.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #2271b1;
    margin: 10px 0;
}
.wp-bulk-seo-aeo-ranking-dashboard,
.wp-bulk-seo-aeo-predictions {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    border-radius: 4px;
}
.position-badge {
    display: inline-block;
    padding: 4px 8px;
    background: #2271b1;
    color: #fff;
    border-radius: 3px;
    font-weight: bold;
}
.best-position {
    color: #00a32a;
    font-weight: bold;
}
.trend-indicator {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    font-weight: bold;
}
.trend-indicator.trend-up {
    background: #00a32a;
    color: #fff;
}
.trend-indicator.trend-down {
    background: #d63638;
    color: #fff;
}
.trend-indicator.trend-stable {
    background: #dba617;
    color: #fff;
}
.no-data {
    color: #999;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('#wp-bulk-seo-aeo-calculate-prediction').on('click', function() {
        var $button = $(this);
        var $results = $('#wp-bulk-seo-aeo-prediction-results');
        
        $button.prop('disabled', true).text('계산 중...');
        $results.hide();

        $.ajax({
            url: wpBulkSEOAEO.restUrl + 'predictions',
            type: 'GET',
            headers: {
                'X-WP-Nonce': wpBulkSEOAEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    var html = '<div class="prediction-results">';
                    html += '<h3>예상 순위 개선</h3>';
                    html += '<ul>';
                    if (response.data.predictions) {
                        response.data.predictions.forEach(function(pred) {
                            html += '<li>';
                            html += '<strong>' + pred.keyword + ':</strong> ';
                            html += '현재 ' + (pred.current_position || '?') + '위 → ';
                            html += '예상 ' + pred.predicted_position + '위 ';
                            html += '(' + (pred.position_change > 0 ? '+' : '') + pred.position_change + '위)';
                            html += ' <span class="confidence-' + pred.confidence + '">[' + pred.confidence + ' 신뢰도]</span>';
                            html += '</li>';
                        });
                    }
                    html += '</ul>';
                    html += '</div>';
                    $results.html(html).show();
                } else {
                    alert('예측 계산 실패: ' + (response.message || '알 수 없는 오류'));
                }
            },
            error: function() {
                alert('예측 계산 중 오류가 발생했습니다.');
            },
            complete: function() {
                $button.prop('disabled', false).text('순위 예측 계산');
            }
        });
    });
});
</script>
