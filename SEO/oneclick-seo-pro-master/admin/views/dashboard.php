<?php
if (!defined('ABSPATH')) exit;

global $wpdb;

// Get stats
$total_licenses = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}oneclick_master_licenses");
$active_licenses = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}oneclick_master_licenses WHERE status = 'active'");
$total_sites = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}oneclick_master_remote_sites");
$total_keywords = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}oneclick_master_keywords WHERE status = 'active'");

$tracker = OneClick_SEO_Rank_Tracker::get_instance();
$rank_stats = $tracker->get_statistics();
?>
<div class="wrap seo-master-wrap">
    <h1><?php esc_html_e('SEO Master Dashboard', 'oneclick-seo-master'); ?></h1>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><span class="dashicons dashicons-admin-network"></span></div>
            <div class="stat-value"><?php echo esc_html($active_licenses); ?></div>
            <div class="stat-label"><?php esc_html_e('Active Licenses', 'oneclick-seo-master'); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><span class="dashicons dashicons-admin-multisite"></span></div>
            <div class="stat-value"><?php echo esc_html($total_sites); ?></div>
            <div class="stat-label"><?php esc_html_e('Managed Sites', 'oneclick-seo-master'); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><span class="dashicons dashicons-chart-line"></span></div>
            <div class="stat-value"><?php echo esc_html($total_keywords); ?></div>
            <div class="stat-label"><?php esc_html_e('Tracked Keywords', 'oneclick-seo-master'); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><span class="dashicons dashicons-awards"></span></div>
            <div class="stat-value"><?php echo esc_html($rank_stats['in_top_10']); ?></div>
            <div class="stat-label"><?php esc_html_e('Keywords in Top 10', 'oneclick-seo-master'); ?></div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Rank Overview Chart -->
        <div class="dashboard-card">
            <h2><?php esc_html_e('Ranking Overview', 'oneclick-seo-master'); ?></h2>
            <div class="chart-container">
                <canvas id="rank-overview-chart"></canvas>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="dashboard-card">
            <h2><?php esc_html_e('Recent Activity', 'oneclick-seo-master'); ?></h2>
            <div class="activity-list" id="activity-list">
                <p class="loading"><?php esc_html_e('Loading...', 'oneclick-seo-master'); ?></p>
            </div>
        </div>

        <!-- Site Health -->
        <div class="dashboard-card full-width">
            <h2><?php esc_html_e('Site Health Overview', 'oneclick-seo-master'); ?></h2>
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Site', 'oneclick-seo-master'); ?></th>
                        <th><?php esc_html_e('Type', 'oneclick-seo-master'); ?></th>
                        <th><?php esc_html_e('SEO Score', 'oneclick-seo-master'); ?></th>
                        <th><?php esc_html_e('Last Crawl', 'oneclick-seo-master'); ?></th>
                        <th><?php esc_html_e('Status', 'oneclick-seo-master'); ?></th>
                    </tr>
                </thead>
                <tbody id="sites-health-table">
                    <tr><td colspan="5" class="loading"><?php esc_html_e('Loading...', 'oneclick-seo-master'); ?></td></tr>
                </tbody>
            </table>
        </div>

        <!-- Top Movers -->
        <div class="dashboard-card">
            <h2><?php esc_html_e('Top Movers (7 days)', 'oneclick-seo-master'); ?></h2>
            <div class="movers-list" id="top-movers">
                <p class="loading"><?php esc_html_e('Loading...', 'oneclick-seo-master'); ?></p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card">
            <h2><?php esc_html_e('Quick Actions', 'oneclick-seo-master'); ?></h2>
            <div class="quick-actions">
                <a href="<?php echo esc_url(admin_url('admin.php?page=seo-master-licenses&action=new')); ?>" class="action-btn">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php esc_html_e('Generate License', 'oneclick-seo-master'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=seo-master-sites&action=add')); ?>" class="action-btn">
                    <span class="dashicons dashicons-admin-site"></span>
                    <?php esc_html_e('Add Remote Site', 'oneclick-seo-master'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=seo-master-ranks&action=add')); ?>" class="action-btn">
                    <span class="dashicons dashicons-chart-area"></span>
                    <?php esc_html_e('Track Keyword', 'oneclick-seo-master'); ?>
                </a>
                <button type="button" class="action-btn" id="crawl-all-sites">
                    <span class="dashicons dashicons-update"></span>
                    <?php esc_html_e('Crawl All Sites', 'oneclick-seo-master'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Load dashboard data
    loadDashboardData();

    function loadDashboardData() {
        // Load sites health
        $.get(seoMaster.restUrl + 'sites', {}, function(response) {
            renderSitesHealth(response.sites || []);
        });

        // Load rank chart
        $.get(seoMaster.restUrl + 'rankings', {}, function(response) {
            renderRankChart(response.keywords || []);
            renderTopMovers(response.keywords || []);
        });
    }

    function renderSitesHealth(sites) {
        var $tbody = $('#sites-health-table');
        if (sites.length === 0) {
            $tbody.html('<tr><td colspan="5"><?php esc_html_e('No sites found', 'oneclick-seo-master'); ?></td></tr>');
            return;
        }

        var html = '';
        sites.slice(0, 10).forEach(function(site) {
            var scoreClass = site.seo_score >= 80 ? 'good' : (site.seo_score >= 60 ? 'ok' : 'poor');
            html += '<tr>' +
                '<td><a href="' + site.site_url + '" target="_blank">' + (site.site_name || site.site_url) + '</a></td>' +
                '<td><span class="site-type site-type-' + site.site_type + '">' + site.site_type + '</span></td>' +
                '<td><span class="score-badge ' + scoreClass + '">' + (site.seo_score || '-') + '</span></td>' +
                '<td>' + (site.last_crawl || '-') + '</td>' +
                '<td><span class="status-' + site.status + '">' + site.status + '</span></td>' +
            '</tr>';
        });
        $tbody.html(html);
    }

    function renderRankChart(keywords) {
        var ctx = document.getElementById('rank-overview-chart');
        if (!ctx) return;

        var distribution = { '1-3': 0, '4-10': 0, '11-20': 0, '21-50': 0, '51-100': 0, '100+': 0 };
        
        keywords.forEach(function(kw) {
            var pos = kw.current_position;
            if (!pos) return;
            if (pos <= 3) distribution['1-3']++;
            else if (pos <= 10) distribution['4-10']++;
            else if (pos <= 20) distribution['11-20']++;
            else if (pos <= 50) distribution['21-50']++;
            else if (pos <= 100) distribution['51-100']++;
            else distribution['100+']++;
        });

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(distribution),
                datasets: [{
                    data: Object.values(distribution),
                    backgroundColor: ['#00a32a', '#4ab866', '#7ad03a', '#dba617', '#f56e28', '#d63638']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'right' } }
            }
        });
    }

    function renderTopMovers(keywords) {
        var $container = $('#top-movers');
        
        var movers = keywords.filter(function(kw) {
            return kw.current_position && kw.previous_position;
        }).map(function(kw) {
            return {
                keyword: kw.keyword,
                change: kw.previous_position - kw.current_position,
                position: kw.current_position
            };
        }).sort(function(a, b) { return Math.abs(b.change) - Math.abs(a.change); }).slice(0, 5);

        if (movers.length === 0) {
            $container.html('<p><?php esc_html_e('No rank changes yet', 'oneclick-seo-master'); ?></p>');
            return;
        }

        var html = '<ul class="movers-list">';
        movers.forEach(function(m) {
            var changeClass = m.change > 0 ? 'up' : (m.change < 0 ? 'down' : 'same');
            var arrow = m.change > 0 ? '↑' : (m.change < 0 ? '↓' : '−');
            html += '<li class="mover-item ' + changeClass + '">' +
                '<span class="mover-keyword">' + m.keyword + '</span>' +
                '<span class="mover-change">' + arrow + Math.abs(m.change) + '</span>' +
                '<span class="mover-position">#' + m.position + '</span>' +
            '</li>';
        });
        html += '</ul>';
        $container.html(html);
    }

    // Crawl all sites
    $('#crawl-all-sites').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).find('.dashicons').addClass('spin');

        $.post(seoMaster.ajaxUrl, {
            action: 'crawl_all_sites',
            nonce: seoMaster.nonce
        }).always(function() {
            $btn.prop('disabled', false).find('.dashicons').removeClass('spin');
            loadDashboardData();
        });
    });
});
</script>
