<?php
/**
 * WP Bulk SEO - Dashboard View
 *
 * @package OneClick_SEO_Pro
 * @subpackage Admin/Views
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap oneclick-seo-pro-dashboard">
    <h1><?php esc_html_e('SEO Dashboard', 'oneclick-seo-pro'); ?></h1>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-admin-page"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html($stats['total_posts']); ?></span>
                <span class="stat-label"><?php esc_html_e('Total Content', 'oneclick-seo-pro'); ?></span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon analyzed">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html($stats['analyzed_posts']); ?></span>
                <span class="stat-label"><?php esc_html_e('Analyzed', 'oneclick-seo-pro'); ?></span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon score">
                <span class="dashicons dashicons-chart-bar"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html(round($stats['average_score'])); ?></span>
                <span class="stat-label"><?php esc_html_e('Average Score', 'oneclick-seo-pro'); ?></span>
            </div>
        </div>

        <div class="stat-card <?php echo $stats['critical_issues'] > 0 ? 'has-issues' : ''; ?>">
            <div class="stat-icon issues">
                <span class="dashicons dashicons-warning"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html($stats['issues_count']); ?></span>
                <span class="stat-label"><?php esc_html_e('Open Issues', 'oneclick-seo-pro'); ?></span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="dashboard-row">
        <!-- Grade Distribution -->
        <div class="dashboard-card">
            <h3><?php esc_html_e('Grade Distribution', 'oneclick-seo-pro'); ?></h3>
            <div class="chart-container">
                <canvas id="gradeChart"></canvas>
            </div>
        </div>

        <!-- Top Issues -->
        <div class="dashboard-card">
            <h3><?php esc_html_e('Top Issues', 'oneclick-seo-pro'); ?></h3>
            <?php if (!empty($stats['top_issues'])): ?>
            <ul class="issues-list">
                <?php foreach ($stats['top_issues'] as $issue): ?>
                <li>
                    <span class="issue-type"><?php echo esc_html(ucfirst(str_replace('_', ' ', $issue['issue_type']))); ?></span>
                    <span class="issue-count"><?php echo esc_html($issue['count']); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p class="no-data"><?php esc_html_e('No issues found. Great job!', 'oneclick-seo-pro'); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Scores -->
    <div class="dashboard-card full-width">
        <h3><?php esc_html_e('Recently Analyzed', 'oneclick-seo-pro'); ?></h3>
        <?php if (!empty($stats['recent_scores'])): ?>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Title', 'oneclick-seo-pro'); ?></th>
                    <th><?php esc_html_e('Score', 'oneclick-seo-pro'); ?></th>
                    <th><?php esc_html_e('Grade', 'oneclick-seo-pro'); ?></th>
                    <th><?php esc_html_e('Analyzed', 'oneclick-seo-pro'); ?></th>
                    <th><?php esc_html_e('Actions', 'oneclick-seo-pro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['recent_scores'] as $score): ?>
                <tr>
                    <td>
                        <a href="<?php echo esc_url(get_edit_post_link($score['post_id'])); ?>">
                            <?php echo esc_html($score['post_title'] ?: __('(no title)', 'oneclick-seo-pro')); ?>
                        </a>
                    </td>
                    <td>
                        <div class="score-bar">
                            <div class="score-fill" style="width: <?php echo esc_attr($score['overall_score']); ?>%"></div>
                            <span class="score-text"><?php echo esc_html($score['overall_score']); ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="grade-badge grade-<?php echo esc_attr(strtolower($score['grade'])); ?>">
                            <?php echo esc_html($score['grade']); ?>
                        </span>
                    </td>
                    <td><?php echo esc_html(human_time_diff(strtotime($score['analyzed_at'])) . ' ago'); ?></td>
                    <td>
                        <button type="button" class="button button-small analyze-btn" data-post-id="<?php echo esc_attr($score['post_id']); ?>">
                            <?php esc_html_e('Re-analyze', 'oneclick-seo-pro'); ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="no-data">
            <?php esc_html_e('No content analyzed yet.', 'oneclick-seo-pro'); ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-analyzer')); ?>">
                <?php esc_html_e('Start analyzing', 'oneclick-seo-pro'); ?>
            </a>
        </p>
        <?php endif; ?>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-card full-width">
        <h3><?php esc_html_e('Quick Actions', 'oneclick-seo-pro'); ?></h3>
        <div class="quick-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-analyzer')); ?>" class="quick-action">
                <span class="dashicons dashicons-search"></span>
                <?php esc_html_e('Analyze Content', 'oneclick-seo-pro'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-optimizer')); ?>" class="quick-action">
                <span class="dashicons dashicons-admin-tools"></span>
                <?php esc_html_e('Bulk Optimize', 'oneclick-seo-pro'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-aeo')); ?>" class="quick-action">
                <span class="dashicons dashicons-admin-comments"></span>
                <?php esc_html_e('AEO Analysis', 'oneclick-seo-pro'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-settings')); ?>" class="quick-action">
                <span class="dashicons dashicons-admin-generic"></span>
                <?php esc_html_e('Settings', 'oneclick-seo-pro'); ?>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grade distribution chart
    var gradeCtx = document.getElementById('gradeChart');
    if (gradeCtx) {
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
                        '#00a32a',
                        '#46b450',
                        '#00a0d2',
                        '#ffb900',
                        '#dc3232',
                        '#8b0000'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Analyze buttons
    document.querySelectorAll('.analyze-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var postId = this.dataset.postId;
            var button = this;

            button.disabled = true;
            button.textContent = wpBulkSeo.strings.analyzing;

            jQuery.post(wpBulkSeo.ajaxUrl, {
                action: 'oneclick_seo_pro_analyze',
                nonce: wpBulkSeo.nonce,
                post_id: postId
            }).done(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || wpBulkSeo.strings.error);
                }
            }).fail(function() {
                alert(wpBulkSeo.strings.error);
            }).always(function() {
                button.disabled = false;
                button.textContent = '<?php esc_html_e('Re-analyze', 'oneclick-seo-pro'); ?>';
            });
        });
    });
});
</script>
