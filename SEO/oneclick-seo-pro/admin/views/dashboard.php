<?php
/**
 * 1-Click SEO Pro - Dashboard View (v25 UI)
 *
 * @package OneClick_SEO_Pro
 * @subpackage Admin/Views
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get stats
global $wpdb;
$table_scores = $wpdb->prefix . 'oneclick_seo_scores';
$table_issues = $wpdb->prefix . 'oneclick_seo_issues';

// Check if tables exist
$table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_scores));

$stats = [
    'total_posts' => wp_count_posts('post')->publish + wp_count_posts('page')->publish,
    'analyzed_posts' => 0,
    'average_score' => 0,
    'issues_count' => 0,
    'critical_issues' => 0,
    'recent_scores' => [],
    'grade_distribution' => ['A+' => 0, 'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0],
    'top_issues' => [],
];

if ($table_exists) {
    $stats['analyzed_posts'] = (int) $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM $table_scores");
    $stats['average_score'] = (float) $wpdb->get_var("SELECT AVG(overall_score) FROM $table_scores");

    // Recent scores
    $stats['recent_scores'] = $wpdb->get_results("
        SELECT s.*, p.post_title
        FROM $table_scores s
        LEFT JOIN {$wpdb->posts} p ON s.post_id = p.ID
        ORDER BY s.analyzed_at DESC
        LIMIT 10
    ", ARRAY_A);

    // Grade distribution
    $grades = $wpdb->get_results("
        SELECT grade, COUNT(*) as count
        FROM $table_scores
        GROUP BY grade
    ", ARRAY_A);
    foreach ($grades as $grade) {
        $stats['grade_distribution'][$grade['grade']] = (int) $grade['count'];
    }

    // Issues
    $issues_table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_issues));
    if ($issues_table_exists) {
        $stats['issues_count'] = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_issues WHERE status = 'open'");
        $stats['critical_issues'] = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_issues WHERE status = 'open' AND priority = 'critical'");
    }
}

// Get license type
$license_type = 'FREE';
if (function_exists('oneclick_seo_security')) {
    $license_type = oneclick_seo_security()->get_license_type();
}
?>
<div class="wrap oneclick-seo-dashboard-v25">
    <!-- Header -->
    <div class="oneclick-seo-header-v25">
        <h1>
            1-Click SEO Pro
            <span class="header-badge"><?php echo esc_html($license_type); ?></span>
        </h1>
        <div class="header-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-settings')); ?>" class="oneclick-seo-btn-v25 oneclick-seo-btn-v25-secondary">
                <span class="dashicons dashicons-admin-generic"></span>
                <?php esc_html_e('Settings', 'oneclick-seo-pro'); ?>
            </a>
        </div>
    </div>

    <!-- Welcome Banner -->
    <div class="oneclick-seo-welcome-v25">
        <h2><?php esc_html_e('SEO 대시보드에 오신 것을 환영합니다!', 'oneclick-seo-pro'); ?></h2>
        <p><?php esc_html_e('Google 알고리즘 유출 문서와 200+ 랭킹 팩터를 기반으로 사이트의 SEO를 분석하고 최적화하세요.', 'oneclick-seo-pro'); ?></p>
    </div>

    <!-- Stats Cards Grid -->
    <div class="oneclick-seo-score-grid-v25">
        <div class="oneclick-seo-score-card-v25 overall">
            <div class="score-icon">📊</div>
            <div class="score-value"><?php echo esc_html(round($stats['average_score'])); ?></div>
            <div class="score-label"><?php esc_html_e('평균 SEO 점수', 'oneclick-seo-pro'); ?></div>
            <div class="score-trend up">
                <?php if ($stats['average_score'] >= 70): ?>
                    <span>↑</span> <span><?php esc_html_e('우수', 'oneclick-seo-pro'); ?></span>
                <?php elseif ($stats['average_score'] >= 50): ?>
                    <span>→</span> <span><?php esc_html_e('보통', 'oneclick-seo-pro'); ?></span>
                <?php else: ?>
                    <span>↓</span> <span><?php esc_html_e('개선 필요', 'oneclick-seo-pro'); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="oneclick-seo-score-card-v25 content">
            <div class="score-icon">📝</div>
            <div class="score-value"><?php echo esc_html($stats['total_posts']); ?></div>
            <div class="score-label"><?php esc_html_e('전체 콘텐츠', 'oneclick-seo-pro'); ?></div>
        </div>

        <div class="oneclick-seo-score-card-v25 technical">
            <div class="score-icon">✅</div>
            <div class="score-value"><?php echo esc_html($stats['analyzed_posts']); ?></div>
            <div class="score-label"><?php esc_html_e('분석 완료', 'oneclick-seo-pro'); ?></div>
        </div>

        <div class="oneclick-seo-score-card-v25 user-experience">
            <div class="score-icon"><?php echo $stats['critical_issues'] > 0 ? '⚠️' : '🎯'; ?></div>
            <div class="score-value"><?php echo esc_html($stats['issues_count']); ?></div>
            <div class="score-label"><?php esc_html_e('개선 필요 이슈', 'oneclick-seo-pro'); ?></div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="oneclick-seo-actions-grid-v25">
        <div class="oneclick-seo-action-card-v25 priority-critical">
            <div class="action-icon">⚡</div>
            <div class="action-title"><?php esc_html_e('1-Click SEO 설정', 'oneclick-seo-pro'); ?></div>
            <div class="action-description"><?php esc_html_e('기본 SEO 설정을 한 번의 클릭으로 자동 적용합니다. Schema, Sitemap, 메타 태그가 모두 설정됩니다.', 'oneclick-seo-pro'); ?></div>
            <div class="action-impact"><?php esc_html_e('높은 영향', 'oneclick-seo-pro'); ?></div>
            <div class="action-buttons">
                <button type="button" class="oneclick-seo-btn-v25 oneclick-seo-btn-v25-primary" id="oneclick-setup-btn">
                    <?php esc_html_e('지금 설정', 'oneclick-seo-pro'); ?>
                </button>
            </div>
        </div>

        <div class="oneclick-seo-action-card-v25 priority-high">
            <div class="action-icon">🔍</div>
            <div class="action-title"><?php esc_html_e('콘텐츠 분석', 'oneclick-seo-pro'); ?></div>
            <div class="action-description"><?php esc_html_e('모든 페이지와 포스트를 200+ 랭킹 팩터로 분석합니다.', 'oneclick-seo-pro'); ?></div>
            <div class="action-impact"><?php esc_html_e('중간 영향', 'oneclick-seo-pro'); ?></div>
            <div class="action-buttons">
                <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-analyzer')); ?>" class="oneclick-seo-btn-v25 oneclick-seo-btn-v25-secondary">
                    <?php esc_html_e('분석 시작', 'oneclick-seo-pro'); ?>
                </a>
            </div>
        </div>

        <div class="oneclick-seo-action-card-v25 priority-medium">
            <div class="action-icon">🤖</div>
            <div class="action-title"><?php esc_html_e('AEO 최적화', 'oneclick-seo-pro'); ?></div>
            <div class="action-description"><?php esc_html_e('AI 검색엔진 (ChatGPT, Perplexity)에 최적화된 콘텐츠로 변환합니다.', 'oneclick-seo-pro'); ?></div>
            <div class="action-impact"><?php esc_html_e('미래 대비', 'oneclick-seo-pro'); ?></div>
            <div class="action-buttons">
                <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-aeo')); ?>" class="oneclick-seo-btn-v25 oneclick-seo-btn-v25-secondary">
                    <?php esc_html_e('AEO 시작', 'oneclick-seo-pro'); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <!-- Grade Distribution Chart -->
        <div style="background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <h3 style="font-size: 18px; font-weight: 700; color: #1F2937; margin: 0 0 20px;"><?php esc_html_e('등급 분포', 'oneclick-seo-pro'); ?></h3>
            <div style="height: 250px;">
                <canvas id="gradeDistributionChart"></canvas>
            </div>
        </div>

        <!-- Module Scores -->
        <div class="oneclick-seo-module-scores-v25">
            <h3 style="font-size: 18px; font-weight: 700; color: #1F2937; margin: 0 0 16px; padding-bottom: 12px; border-bottom: 2px solid #E5E7EB;"><?php esc_html_e('모듈별 성능', 'oneclick-seo-pro'); ?></h3>

            <?php
            $modules = [
                ['name' => __('Core Web Vitals', 'oneclick-seo-pro'), 'icon' => '⚡', 'score' => 75],
                ['name' => __('On-Page SEO', 'oneclick-seo-pro'), 'icon' => '📄', 'score' => 82],
                ['name' => __('Content Quality', 'oneclick-seo-pro'), 'icon' => '📝', 'score' => 68],
                ['name' => __('Technical SEO', 'oneclick-seo-pro'), 'icon' => '🔧', 'score' => 90],
                ['name' => __('E-E-A-T', 'oneclick-seo-pro'), 'icon' => '🏆', 'score' => 55],
            ];
            foreach ($modules as $module):
                $fill_class = $module['score'] >= 80 ? 'excellent' : ($module['score'] >= 60 ? 'good' : ($module['score'] >= 40 ? 'average' : 'poor'));
            ?>
            <div class="oneclick-seo-module-item">
                <div class="module-icon"><?php echo esc_html($module['icon']); ?></div>
                <div class="module-info">
                    <div class="module-name"><?php echo esc_html($module['name']); ?></div>
                    <div class="module-bar">
                        <div class="module-bar-fill <?php echo esc_attr($fill_class); ?>" style="width: <?php echo esc_attr($module['score']); ?>%"></div>
                    </div>
                </div>
                <div class="module-score" style="color: <?php echo $module['score'] >= 70 ? '#10B981' : ($module['score'] >= 50 ? '#F59E0B' : '#EF4444'); ?>"><?php echo esc_html($module['score']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recent Scores Table -->
    <div style="background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 32px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #1F2937; margin: 0;"><?php esc_html_e('최근 분석된 콘텐츠', 'oneclick-seo-pro'); ?></h3>
            <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-analyzer')); ?>" class="oneclick-seo-btn-v25 oneclick-seo-btn-v25-sm oneclick-seo-btn-v25-secondary">
                <?php esc_html_e('전체 보기', 'oneclick-seo-pro'); ?>
            </a>
        </div>

        <?php if (!empty($stats['recent_scores'])): ?>
        <table class="oneclick-seo-table-v25">
            <thead>
                <tr>
                    <th><?php esc_html_e('콘텐츠', 'oneclick-seo-pro'); ?></th>
                    <th style="width: 150px;"><?php esc_html_e('점수', 'oneclick-seo-pro'); ?></th>
                    <th style="width: 80px;"><?php esc_html_e('등급', 'oneclick-seo-pro'); ?></th>
                    <th style="width: 120px;"><?php esc_html_e('분석일', 'oneclick-seo-pro'); ?></th>
                    <th style="width: 120px;"><?php esc_html_e('액션', 'oneclick-seo-pro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['recent_scores'] as $score):
                    $grade_class = 'grade-' . strtolower(str_replace('+', '-plus', $score['grade']));
                ?>
                <tr>
                    <td>
                        <a href="<?php echo esc_url(get_edit_post_link($score['post_id'])); ?>" style="color: #1F2937; text-decoration: none; font-weight: 500;">
                            <?php echo esc_html($score['post_title'] ?: __('(제목 없음)', 'oneclick-seo-pro')); ?>
                        </a>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="flex: 1; height: 8px; background: #E5E7EB; border-radius: 9999px; overflow: hidden;">
                                <div style="height: 100%; width: <?php echo esc_attr($score['overall_score']); ?>%; background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%); border-radius: 9999px;"></div>
                            </div>
                            <span style="font-weight: 600; font-size: 14px;"><?php echo esc_html($score['overall_score']); ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="oneclick-seo-grade-badge <?php echo esc_attr($grade_class); ?>">
                            <?php echo esc_html($score['grade']); ?>
                        </span>
                    </td>
                    <td style="color: #6B7280; font-size: 14px;">
                        <?php echo esc_html(human_time_diff(strtotime($score['analyzed_at'])) . ' ' . __('전', 'oneclick-seo-pro')); ?>
                    </td>
                    <td>
                        <button type="button" class="oneclick-seo-btn-v25 oneclick-seo-btn-v25-sm oneclick-seo-btn-v25-secondary analyze-btn" data-post-id="<?php echo esc_attr($score['post_id']); ?>">
                            <?php esc_html_e('재분석', 'oneclick-seo-pro'); ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; color: #6B7280;">
            <div style="font-size: 48px; margin-bottom: 16px;">📊</div>
            <h4 style="font-size: 18px; font-weight: 600; color: #1F2937; margin: 0 0 8px;"><?php esc_html_e('아직 분석된 콘텐츠가 없습니다', 'oneclick-seo-pro'); ?></h4>
            <p style="margin: 0 0 20px;"><?php esc_html_e('콘텐츠를 분석하여 SEO 점수를 확인하세요.', 'oneclick-seo-pro'); ?></p>
            <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-analyzer')); ?>" class="oneclick-seo-btn-v25 oneclick-seo-btn-v25-primary">
                <?php esc_html_e('분석 시작', 'oneclick-seo-pro'); ?>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Cross Promotion Banner -->
    <?php if (!is_plugin_active('acf-user-journey-analytics/acf-user-journey-analytics.php')): ?>
    <div style="background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); border-radius: 16px; padding: 24px; color: #fff; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h3 style="font-size: 20px; font-weight: 700; margin: 0 0 8px;">📊 <?php esc_html_e('무료 트래픽 분석 플러그인', 'oneclick-seo-pro'); ?></h3>
            <p style="margin: 0; opacity: 0.9;"><?php esc_html_e('ACF User Journey Analytics로 SEO 트래픽 성과를 추적하세요. 50+ 광고 플랫폼 지원!', 'oneclick-seo-pro'); ?></p>
        </div>
        <a href="<?php echo esc_url(admin_url('plugin-install.php?s=acf+user+journey+analytics&tab=search&type=term')); ?>" class="oneclick-seo-btn-v25" style="background: #fff; color: #3B82F6; font-weight: 600;">
            <?php esc_html_e('무료 설치', 'oneclick-seo-pro'); ?>
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grade Distribution Chart
    var gradeCtx = document.getElementById('gradeDistributionChart');
    if (gradeCtx && typeof Chart !== 'undefined') {
        new Chart(gradeCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['A+', 'A', 'B', 'C', 'D', 'F'],
                datasets: [{
                    data: [
                        <?php echo esc_js($stats['grade_distribution']['A+']); ?>,
                        <?php echo esc_js($stats['grade_distribution']['A']); ?>,
                        <?php echo esc_js($stats['grade_distribution']['B']); ?>,
                        <?php echo esc_js($stats['grade_distribution']['C']); ?>,
                        <?php echo esc_js($stats['grade_distribution']['D']); ?>,
                        <?php echo esc_js($stats['grade_distribution']['F']); ?>
                    ],
                    backgroundColor: [
                        '#059669',
                        '#10B981',
                        '#3B82F6',
                        '#F59E0B',
                        '#F97316',
                        '#EF4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }

    // 1-Click Setup Button
    var setupBtn = document.getElementById('oneclick-setup-btn');
    if (setupBtn) {
        setupBtn.addEventListener('click', function() {
            var button = this;
            button.disabled = true;
            button.textContent = '<?php esc_html_e('설정 중...', 'oneclick-seo-pro'); ?>';

            jQuery.post(oneclickSEO.ajaxUrl, {
                action: 'oneclick_seo_pro_1click_setup',
                nonce: oneclickSEO.nonce
            }).done(function(response) {
                if (response.success) {
                    alert(response.data.message || '<?php esc_html_e('설정이 완료되었습니다!', 'oneclick-seo-pro'); ?>');
                    location.reload();
                } else {
                    alert(response.data.message || '<?php esc_html_e('오류가 발생했습니다.', 'oneclick-seo-pro'); ?>');
                }
            }).fail(function() {
                alert('<?php esc_html_e('요청 처리 중 오류가 발생했습니다.', 'oneclick-seo-pro'); ?>');
            }).always(function() {
                button.disabled = false;
                button.textContent = '<?php esc_html_e('지금 설정', 'oneclick-seo-pro'); ?>';
            });
        });
    }

    // Re-analyze Buttons
    document.querySelectorAll('.analyze-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var postId = this.dataset.postId;
            var button = this;

            button.disabled = true;
            button.textContent = oneclickSEO.i18n.analyzing;

            jQuery.post(oneclickSEO.ajaxUrl, {
                action: 'oneclick_seo_pro_analyze',
                nonce: oneclickSEO.nonce,
                post_id: postId
            }).done(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || oneclickSEO.i18n.error);
                }
            }).fail(function() {
                alert(oneclickSEO.i18n.error);
            }).always(function() {
                button.disabled = false;
                button.textContent = '<?php esc_html_e('재분석', 'oneclick-seo-pro'); ?>';
            });
        });
    });
});
</script>
