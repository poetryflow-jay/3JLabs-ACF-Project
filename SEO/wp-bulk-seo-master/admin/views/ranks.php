<?php
if (!defined('ABSPATH')) exit;

$tracker = WP_Bulk_SEO_Rank_Tracker::get_instance();
$keywords = $tracker->get_keywords();
$stats = $tracker->get_statistics();
?>
<div class="wrap seo-master-wrap">
    <h1>
        <?php esc_html_e('Rank Tracker', 'wp-bulk-seo-master'); ?>
        <button type="button" class="page-title-action" id="add-keyword-btn"><?php esc_html_e('Add Keyword', 'wp-bulk-seo-master'); ?></button>
    </h1>

    <!-- Stats -->
    <div class="rank-stats">
        <div class="stat-item">
            <span class="stat-value"><?php echo esc_html($stats['total_keywords']); ?></span>
            <span class="stat-label"><?php esc_html_e('Total Keywords', 'wp-bulk-seo-master'); ?></span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?php echo esc_html($stats['in_top_10']); ?></span>
            <span class="stat-label"><?php esc_html_e('In Top 10', 'wp-bulk-seo-master'); ?></span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?php echo esc_html($stats['improved']); ?></span>
            <span class="stat-label"><?php esc_html_e('Improved', 'wp-bulk-seo-master'); ?></span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?php echo esc_html($stats['average_position'] ?: '-'); ?></span>
            <span class="stat-label"><?php esc_html_e('Avg. Position', 'wp-bulk-seo-master'); ?></span>
        </div>
    </div>

    <!-- Chart -->
    <div class="rank-chart-container">
        <canvas id="rank-history-chart" height="100"></canvas>
    </div>

    <!-- Keywords Table -->
    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Keyword', 'wp-bulk-seo-master'); ?></th>
                <th><?php esc_html_e('Position', 'wp-bulk-seo-master'); ?></th>
                <th><?php esc_html_e('Change', 'wp-bulk-seo-master'); ?></th>
                <th><?php esc_html_e('Target URL', 'wp-bulk-seo-master'); ?></th>
                <th><?php esc_html_e('Last Checked', 'wp-bulk-seo-master'); ?></th>
                <th><?php esc_html_e('Actions', 'wp-bulk-seo-master'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($keywords)): ?>
            <tr><td colspan="6"><?php esc_html_e('No keywords tracked', 'wp-bulk-seo-master'); ?></td></tr>
            <?php else: ?>
            <?php foreach ($keywords as $kw): 
                $change = $kw->previous_position ? ($kw->previous_position - $kw->current_position) : 0;
                $changeClass = $change > 0 ? 'up' : ($change < 0 ? 'down' : 'same');
            ?>
            <tr data-id="<?php echo esc_attr($kw->id); ?>">
                <td><strong><?php echo esc_html($kw->keyword); ?></strong></td>
                <td>
                    <?php if ($kw->current_position): ?>
                    <span class="position-badge">#<?php echo esc_html($kw->current_position); ?></span>
                    <?php else: ?>
                    <span class="not-ranked"><?php esc_html_e('Not ranked', 'wp-bulk-seo-master'); ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="rank-change <?php echo esc_attr($changeClass); ?>">
                        <?php if ($change > 0): ?>
                            <span class="dashicons dashicons-arrow-up-alt"></span>+<?php echo esc_html($change); ?>
                        <?php elseif ($change < 0): ?>
                            <span class="dashicons dashicons-arrow-down-alt"></span><?php echo esc_html($change); ?>
                        <?php else: ?>
                            <span class="dashicons dashicons-minus"></span>
                        <?php endif; ?>
                    </span>
                </td>
                <td><a href="<?php echo esc_url($kw->target_url); ?>" target="_blank"><?php echo esc_html(wp_trim_words($kw->target_url, 5)); ?></a></td>
                <td><?php echo $kw->last_checked ? esc_html(human_time_diff(strtotime($kw->last_checked))) . ' ago' : '-'; ?></td>
                <td>
                    <button type="button" class="button button-small check-rank" data-id="<?php echo esc_attr($kw->id); ?>"><?php esc_html_e('Check', 'wp-bulk-seo-master'); ?></button>
                    <button type="button" class="button button-small view-history" data-id="<?php echo esc_attr($kw->id); ?>"><?php esc_html_e('History', 'wp-bulk-seo-master'); ?></button>
                    <button type="button" class="button button-small remove-keyword" data-id="<?php echo esc_attr($kw->id); ?>"><?php esc_html_e('Remove', 'wp-bulk-seo-master'); ?></button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Keyword Modal -->
<div id="add-keyword-modal" class="seo-master-modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2><?php esc_html_e('Add Keyword', 'wp-bulk-seo-master'); ?></h2>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="add-keyword-form">
                <p>
                    <label><?php esc_html_e('Keyword', 'wp-bulk-seo-master'); ?> *</label>
                    <input type="text" name="keyword" required class="regular-text">
                </p>
                <p>
                    <label><?php esc_html_e('Target URL', 'wp-bulk-seo-master'); ?></label>
                    <input type="url" name="target_url" class="regular-text" placeholder="https://...">
                </p>
                <p>
                    <label><?php esc_html_e('Country', 'wp-bulk-seo-master'); ?></label>
                    <select name="country">
                        <option value="us">United States</option>
                        <option value="kr">South Korea</option>
                        <option value="jp">Japan</option>
                        <option value="gb">United Kingdom</option>
                        <option value="de">Germany</option>
                    </select>
                </p>
                <p>
                    <label><?php esc_html_e('Device', 'wp-bulk-seo-master'); ?></label>
                    <select name="device">
                        <option value="desktop">Desktop</option>
                        <option value="mobile">Mobile</option>
                    </select>
                </p>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="button modal-cancel"><?php esc_html_e('Cancel', 'wp-bulk-seo-master'); ?></button>
            <button type="button" class="button button-primary" id="submit-keyword"><?php esc_html_e('Add & Check Rank', 'wp-bulk-seo-master'); ?></button>
        </div>
    </div>
</div>

<!-- History Modal -->
<div id="history-modal" class="seo-master-modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content modal-wide">
        <div class="modal-header">
            <h2><?php esc_html_e('Rank History', 'wp-bulk-seo-master'); ?></h2>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <canvas id="keyword-history-chart" height="200"></canvas>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var historyChart = null;

    // Open add modal
    $('#add-keyword-btn').on('click', function() {
        $('#add-keyword-modal').show();
    });

    // Close modals
    $('.modal-close, .modal-cancel, .modal-overlay').on('click', function() {
        $('.seo-master-modal').hide();
    });

    // Submit keyword
    $('#submit-keyword').on('click', function() {
        var $btn = $(this);
        var $form = $('#add-keyword-form');
        
        $btn.prop('disabled', true).text('<?php esc_html_e('Checking...', 'wp-bulk-seo-master'); ?>');

        $.post(seoMaster.ajaxUrl, {
            action: 'add_keyword',
            nonce: seoMaster.nonce,
            keyword: $form.find('[name="keyword"]').val(),
            target_url: $form.find('[name="target_url"]').val(),
            country: $form.find('[name="country"]').val(),
            device: $form.find('[name="device"]').val()
        }).done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data.message);
            }
        }).always(function() {
            $btn.prop('disabled', false).text('<?php esc_html_e('Add & Check Rank', 'wp-bulk-seo-master'); ?>');
        });
    });

    // Check rank
    $('.check-rank').on('click', function() {
        var $btn = $(this);
        var keywordId = $btn.data('id');
        
        $btn.prop('disabled', true).text('<?php esc_html_e('Checking...', 'wp-bulk-seo-master'); ?>');

        $.post(seoMaster.ajaxUrl, {
            action: 'check_keyword_rank',
            nonce: seoMaster.nonce,
            keyword_id: keywordId
        }).done(function(response) {
            if (response.success) {
                location.reload();
            }
        }).always(function() {
            $btn.prop('disabled', false).text('<?php esc_html_e('Check', 'wp-bulk-seo-master'); ?>');
        });
    });

    // View history
    $('.view-history').on('click', function() {
        var keywordId = $(this).data('id');

        $.post(seoMaster.ajaxUrl, {
            action: 'get_rank_history',
            nonce: seoMaster.nonce,
            keyword_id: keywordId,
            days: 30
        }).done(function(response) {
            if (response.success) {
                renderHistoryChart(response.data.history);
                $('#history-modal').show();
            }
        });
    });

    function renderHistoryChart(history) {
        var ctx = document.getElementById('keyword-history-chart');
        
        if (historyChart) {
            historyChart.destroy();
        }

        var labels = history.map(function(h) { return h.checked_at.split(' ')[0]; });
        var data = history.map(function(h) { return h.position; });

        historyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '<?php esc_html_e('Position', 'wp-bulk-seo-master'); ?>',
                    data: data,
                    borderColor: '#2271b1',
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        reverse: true,
                        min: 1,
                        title: { display: true, text: '<?php esc_html_e('Position', 'wp-bulk-seo-master'); ?>' }
                    }
                }
            }
        });
    }

    // Remove keyword
    $('.remove-keyword').on('click', function() {
        if (!confirm('<?php esc_html_e('Remove this keyword?', 'wp-bulk-seo-master'); ?>')) return;
        
        var keywordId = $(this).data('id');

        $.post(seoMaster.ajaxUrl, {
            action: 'remove_keyword',
            nonce: seoMaster.nonce,
            keyword_id: keywordId
        }).done(function() {
            location.reload();
        });
    });
});
</script>
