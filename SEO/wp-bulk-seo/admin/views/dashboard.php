<?php
/**
 * WP Bulk SEO - Dashboard View
 *
 * @package WP_Bulk_SEO
 * @subpackage Admin/Views
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap wp-bulk-seo-dashboard">
    <h1><?php esc_html_e('SEO Dashboard', 'wp-bulk-seo'); ?></h1>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-admin-page"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html($stats['total_posts']); ?></span>
                <span class="stat-label"><?php esc_html_e('Total Content', 'wp-bulk-seo'); ?></span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon analyzed">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html($stats['analyzed_posts']); ?></span>
                <span class="stat-label"><?php esc_html_e('Analyzed', 'wp-bulk-seo'); ?></span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon score">
                <span class="dashicons dashicons-chart-bar"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html(round($stats['average_score'])); ?></span>
                <span class="stat-label"><?php esc_html_e('Average Score', 'wp-bulk-seo'); ?></span>
            </div>
        </div>

        <div class="stat-card <?php echo $stats['critical_issues'] > 0 ? 'has-issues' : ''; ?>">
            <div class="stat-icon issues">
                <span class="dashicons dashicons-warning"></span>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html($stats['issues_count']); ?></span>
                <span class="stat-label"><?php esc_html_e('Open Issues', 'wp-bulk-seo'); ?></span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="dashboard-row">
        <!-- Grade Distribution -->
        <div class="dashboard-card">
            <h3><?php esc_html_e('Grade Distribution', 'wp-bulk-seo'); ?></h3>
            <div class="chart-container">
                <canvas id="gradeChart"></canvas>
            </div>
        </div>

        <!-- Top Issues -->
        <div class="dashboard-card">
            <h3><?php esc_html_e('Top Issues', 'wp-bulk-seo'); ?></h3>
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
            <p class="no-data"><?php esc_html_e('No issues found. Great job!', 'wp-bulk-seo'); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Scores -->
    <div class="dashboard-card full-width">
        <h3><?php esc_html_e('Recently Analyzed', 'wp-bulk-seo'); ?></h3>
        <?php if (!empty($stats['recent_scores'])): ?>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Title', 'wp-bulk-seo'); ?></th>
                    <th><?php esc_html_e('Score', 'wp-bulk-seo'); ?></th>
                    <th><?php esc_html_e('Grade', 'wp-bulk-seo'); ?></th>
                    <th><?php esc_html_e('Analyzed', 'wp-bulk-seo'); ?></th>
                    <th><?php esc_html_e('Actions', 'wp-bulk-seo'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['recent_scores'] as $score): ?>
                <tr>
                    <td>
                        <a href="<?php echo esc_url(get_edit_post_link($score['post_id'])); ?>">
                            <?php echo esc_html($score['post_title'] ?: __('(no title)', 'wp-bulk-seo')); ?>
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
                            <?php esc_html_e('Re-analyze', 'wp-bulk-seo'); ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="no-data">
            <?php esc_html_e('No content analyzed yet.', 'wp-bulk-seo'); ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=wp-bulk-seo-analyzer')); ?>">
                <?php esc_html_e('Start analyzing', 'wp-bulk-seo'); ?>
            </a>
        </p>
        <?php endif; ?>
    </div>

    <!-- [v2.0.0] Real-time Notifications -->
    <?php
    $monitor = WP_Bulk_SEO_Realtime_Monitor::instance();
    $notifications = $monitor->get_notifications(5);
    if (!empty($notifications)):
    ?>
    <div class="dashboard-card full-width">
        <h3>
            <?php esc_html_e('🔔 실시간 알림', 'wp-bulk-seo'); ?>
            <span class="notification-count" style="background: #dc3232; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 12px; margin-left: 10px;">
                <?php echo esc_html(count($notifications)); ?>
            </span>
        </h3>
        <div class="notifications-list">
            <?php foreach ($notifications as $notification): ?>
            <div class="notification-item severity-<?php echo esc_attr($notification['severity']); ?>" data-id="<?php echo esc_attr($notification['id']); ?>">
                <div class="notification-icon">
                    <?php
                    $icons = [
                        'critical' => '🔴',
                        'high' => '🟠',
                        'medium' => '🟡',
                        'low' => '🔵',
                    ];
                    echo $icons[$notification['severity']] ?? '⚪';
                    ?>
                </div>
                <div class="notification-content">
                    <div class="notification-message"><?php echo esc_html($notification['message']); ?></div>
                    <div class="notification-time"><?php echo esc_html(human_time_diff(strtotime($notification['created_at'])) . ' 전'); ?></div>
                </div>
                <button type="button" class="notification-dismiss" data-id="<?php echo esc_attr($notification['id']); ?>">×</button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="dashboard-card full-width">
        <h3><?php esc_html_e('Quick Actions', 'wp-bulk-seo'); ?></h3>
        <div class="quick-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page=wp-bulk-seo-analyzer')); ?>" class="quick-action">
                <span class="dashicons dashicons-search"></span>
                <?php esc_html_e('Analyze Content', 'wp-bulk-seo'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=wp-bulk-seo-optimizer')); ?>" class="quick-action">
                <span class="dashicons dashicons-admin-tools"></span>
                <?php esc_html_e('Bulk Optimize', 'wp-bulk-seo'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=wp-bulk-seo-aeo')); ?>" class="quick-action">
                <span class="dashicons dashicons-admin-comments"></span>
                <?php esc_html_e('AEO Analysis', 'wp-bulk-seo'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=wp-bulk-seo-settings')); ?>" class="quick-action">
                <span class="dashicons dashicons-admin-generic"></span>
                <?php esc_html_e('Settings', 'wp-bulk-seo'); ?>
            </a>
        </div>
    </div>
</div>

<style>
/* [v2.0.0] Notification Styles */
.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.notification-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #fff;
    border-left: 4px solid #ddd;
    border-radius: 4px;
    transition: all 0.2s;
}
.notification-item.severity-critical {
    border-left-color: #dc3232;
    background: #fef7f7;
}
.notification-item.severity-high {
    border-left-color: #ffb900;
    background: #fef7e1;
}
.notification-item.severity-medium {
    border-left-color: #00a0d2;
    background: #e7f3f9;
}
.notification-item.severity-low {
    border-left-color: #46b450;
    background: #f0f9f0;
}
.notification-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.notification-icon {
    font-size: 24px;
}
.notification-content {
    flex: 1;
}
.notification-message {
    font-weight: 500;
    margin-bottom: 5px;
}
.notification-time {
    font-size: 12px;
    color: #666;
}
.notification-dismiss {
    background: none;
    border: none;
    font-size: 20px;
    color: #999;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
    line-height: 1;
}
.notification-dismiss:hover {
    color: #dc3232;
}
</style>

<script>
// [v2.0.0] Notification dismissal
jQuery(document).ready(function($) {
    $('.notification-dismiss').on('click', function() {
        var notificationId = $(this).data('id');
        var $item = $(this).closest('.notification-item');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'wp_bulk_seo_dismiss_notification',
            nonce: wpBulkSeo.nonce,
            notification_id: notificationId
        }).done(function(response) {
            if (response.success) {
                $item.fadeOut(300, function() {
                    $(this).remove();
                    // Update count
                    var count = $('.notification-item').length;
                    $('.notification-count').text(count);
                    if (count === 0) {
                        $('.dashboard-card.full-width').first().fadeOut();
                    }
                });
            }
        });
    });
});
</script>

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
                action: 'wp_bulk_seo_analyze',
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
                button.textContent = '<?php esc_html_e('Re-analyze', 'wp-bulk-seo'); ?>';
            });
        });
    });
});
</script>
