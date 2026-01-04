<?php
/**
 * Traffic Analytics Page - User Journey Analytics Integration
 *
 * @package OneClick_SEO_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if User Journey Analytics is active
$uja_active = class_exists('ACF_User_Journey_Analytics') || function_exists('acf_user_journey_analytics');

// Get period
$period = isset($_GET['period']) ? sanitize_text_field($_GET['period']) : '7days';

// Calculate date range
switch ($period) {
    case 'today':
        $start_date = date('Y-m-d 00:00:00');
        $end_date = date('Y-m-d 23:59:59');
        break;
    case '30days':
        $start_date = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $end_date = date('Y-m-d 23:59:59');
        break;
    case '90days':
        $start_date = date('Y-m-d 00:00:00', strtotime('-90 days'));
        $end_date = date('Y-m-d 23:59:59');
        break;
    default:
        $start_date = date('Y-m-d 00:00:00', strtotime('-7 days'));
        $end_date = date('Y-m-d 23:59:59');
}

// Get data from User Journey Analytics if available
$traffic_data = null;
$referrer_data = null;
$funnel_data = null;

if ($uja_active && method_exists('ACF_User_Journey_Analytics', 'get_traffic_data')) {
    $traffic_data = ACF_User_Journey_Analytics::get_traffic_data($start_date, $end_date, 'summary');
    $referrer_data = ACF_User_Journey_Analytics::get_traffic_data($start_date, $end_date, 'sources');
    $funnel_data = ACF_User_Journey_Analytics::get_traffic_data($start_date, $end_date, 'funnel');
}
?>

<div class="wrap oneclick-seo-analytics">
    <h1>
        <span class="dashicons dashicons-chart-area"></span>
        <?php esc_html_e('Traffic Analytics', 'oneclick-seo-pro'); ?>
    </h1>

    <?php if (!$uja_active): ?>
    <!-- User Journey Analytics Not Installed Banner -->
    <div class="uja-install-banner">
        <div class="banner-content">
            <div class="banner-icon">
                <span class="dashicons dashicons-chart-line"></span>
            </div>
            <div class="banner-text">
                <h2><?php esc_html_e('Unlock Full Traffic Analytics', 'oneclick-seo-pro'); ?></h2>
                <p><?php esc_html_e('Install ACF User Journey Analytics (FREE) to get detailed traffic analysis, conversion tracking, and ROI measurement integrated directly into your SEO workflow.', 'oneclick-seo-pro'); ?></p>
                <ul class="feature-list">
                    <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Real-time visitor monitoring', 'oneclick-seo-pro'); ?></li>
                    <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('UTM & ad platform tracking (Google, Meta, Naver, Kakao)', 'oneclick-seo-pro'); ?></li>
                    <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Conversion funnel analysis', 'oneclick-seo-pro'); ?></li>
                    <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('AI search engine referrer detection', 'oneclick-seo-pro'); ?></li>
                    <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('ROI/ROAS calculation', 'oneclick-seo-pro'); ?></li>
                </ul>
            </div>
            <div class="banner-action">
                <a href="https://wordpress.org/plugins/acf-user-journey-analytics/" target="_blank" class="button button-primary button-hero">
                    <span class="dashicons dashicons-download"></span>
                    <?php esc_html_e('Install Free Plugin', 'oneclick-seo-pro'); ?>
                </a>
                <p class="small-text"><?php esc_html_e('100% Free - No credit card required', 'oneclick-seo-pro'); ?></p>
            </div>
        </div>
    </div>

    <!-- Preview of what's available -->
    <div class="analytics-preview">
        <h3><?php esc_html_e('Preview: What You\'ll Get', 'oneclick-seo-pro'); ?></h3>
        <div class="preview-grid">
            <div class="preview-card">
                <div class="preview-header">
                    <span class="dashicons dashicons-visibility"></span>
                    <h4><?php esc_html_e('Real-time Monitoring', 'oneclick-seo-pro'); ?></h4>
                </div>
                <div class="preview-content placeholder">
                    <div class="placeholder-chart"></div>
                    <p><?php esc_html_e('See active visitors on your site right now', 'oneclick-seo-pro'); ?></p>
                </div>
            </div>
            <div class="preview-card">
                <div class="preview-header">
                    <span class="dashicons dashicons-share"></span>
                    <h4><?php esc_html_e('Traffic Sources', 'oneclick-seo-pro'); ?></h4>
                </div>
                <div class="preview-content placeholder">
                    <div class="placeholder-pie"></div>
                    <p><?php esc_html_e('Understand where your visitors come from', 'oneclick-seo-pro'); ?></p>
                </div>
            </div>
            <div class="preview-card">
                <div class="preview-header">
                    <span class="dashicons dashicons-filter"></span>
                    <h4><?php esc_html_e('Conversion Funnel', 'oneclick-seo-pro'); ?></h4>
                </div>
                <div class="preview-content placeholder">
                    <div class="placeholder-funnel"></div>
                    <p><?php esc_html_e('Track visitor journey to conversion', 'oneclick-seo-pro'); ?></p>
                </div>
            </div>
            <div class="preview-card">
                <div class="preview-header">
                    <span class="dashicons dashicons-chart-pie"></span>
                    <h4><?php esc_html_e('ROI Analysis', 'oneclick-seo-pro'); ?></h4>
                </div>
                <div class="preview-content placeholder">
                    <div class="placeholder-table"></div>
                    <p><?php esc_html_e('Measure advertising ROI and ROAS', 'oneclick-seo-pro'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- User Journey Analytics is Active - Show Data -->

    <!-- Period Selector -->
    <div class="period-selector">
        <a href="<?php echo esc_url(add_query_arg('period', 'today')); ?>" class="<?php echo $period === 'today' ? 'active' : ''; ?>"><?php esc_html_e('Today', 'oneclick-seo-pro'); ?></a>
        <a href="<?php echo esc_url(add_query_arg('period', '7days')); ?>" class="<?php echo $period === '7days' ? 'active' : ''; ?>"><?php esc_html_e('7 Days', 'oneclick-seo-pro'); ?></a>
        <a href="<?php echo esc_url(add_query_arg('period', '30days')); ?>" class="<?php echo $period === '30days' ? 'active' : ''; ?>"><?php esc_html_e('30 Days', 'oneclick-seo-pro'); ?></a>
        <a href="<?php echo esc_url(add_query_arg('period', '90days')); ?>" class="<?php echo $period === '90days' ? 'active' : ''; ?>"><?php esc_html_e('90 Days', 'oneclick-seo-pro'); ?></a>
    </div>

    <?php if ($traffic_data): ?>
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="card-icon visitors"><span class="dashicons dashicons-groups"></span></div>
            <div class="card-content">
                <h3><?php echo number_format($traffic_data['unique_visitors'] ?? 0); ?></h3>
                <p><?php esc_html_e('Unique Visitors', 'oneclick-seo-pro'); ?></p>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon sessions"><span class="dashicons dashicons-chart-bar"></span></div>
            <div class="card-content">
                <h3><?php echo number_format($traffic_data['sessions'] ?? 0); ?></h3>
                <p><?php esc_html_e('Sessions', 'oneclick-seo-pro'); ?></p>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon pageviews"><span class="dashicons dashicons-visibility"></span></div>
            <div class="card-content">
                <h3><?php echo number_format($traffic_data['pageviews'] ?? 0); ?></h3>
                <p><?php esc_html_e('Page Views', 'oneclick-seo-pro'); ?></p>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon conversion"><span class="dashicons dashicons-yes-alt"></span></div>
            <div class="card-content">
                <h3><?php echo ($traffic_data['conversion_rate'] ?? 0); ?>%</h3>
                <p><?php esc_html_e('Conversion Rate', 'oneclick-seo-pro'); ?></p>
            </div>
        </div>
    </div>

    <!-- SEO + Traffic Insights -->
    <div class="seo-traffic-insight">
        <h3><?php esc_html_e('SEO & Traffic Insights', 'oneclick-seo-pro'); ?></h3>
        <div class="insight-grid">
            <div class="insight-item">
                <span class="insight-icon organic"></span>
                <div class="insight-content">
                    <h4><?php esc_html_e('Organic Traffic', 'oneclick-seo-pro'); ?></h4>
                    <p><?php esc_html_e('Visitors from search engines. Improve with SEO optimization.', 'oneclick-seo-pro'); ?></p>
                    <?php
                    $organic_count = 0;
                    if ($referrer_data) {
                        foreach ($referrer_data as $ref) {
                            if ($ref->referrer_type === 'organic' || $ref->referrer_type === 'search') {
                                $organic_count += $ref->sessions;
                            }
                        }
                    }
                    ?>
                    <span class="insight-value"><?php echo number_format($organic_count); ?> sessions</span>
                </div>
            </div>
            <div class="insight-item">
                <span class="insight-icon ai"></span>
                <div class="insight-content">
                    <h4><?php esc_html_e('AI Search Traffic', 'oneclick-seo-pro'); ?></h4>
                    <p><?php esc_html_e('Visitors from AI assistants. Optimize with AEO features.', 'oneclick-seo-pro'); ?></p>
                    <?php
                    $ai_count = 0;
                    if ($referrer_data) {
                        foreach ($referrer_data as $ref) {
                            if ($ref->referrer_type === 'ai_search' || $ref->referrer_type === 'ai_chatbot') {
                                $ai_count += $ref->sessions;
                            }
                        }
                    }
                    ?>
                    <span class="insight-value"><?php echo number_format($ai_count); ?> sessions</span>
                </div>
            </div>
        </div>
        <p class="insight-cta">
            <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-analyzer')); ?>" class="button">
                <?php esc_html_e('Optimize SEO Now', 'oneclick-seo-pro'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-aeo')); ?>" class="button">
                <?php esc_html_e('Optimize for AI Search', 'oneclick-seo-pro'); ?>
            </a>
        </p>
    </div>

    <!-- Traffic Sources -->
    <?php if ($referrer_data && is_array($referrer_data)): ?>
    <div class="traffic-sources-section">
        <h3><?php esc_html_e('Traffic Sources', 'oneclick-seo-pro'); ?></h3>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Source', 'oneclick-seo-pro'); ?></th>
                    <th><?php esc_html_e('Type', 'oneclick-seo-pro'); ?></th>
                    <th><?php esc_html_e('Sessions', 'oneclick-seo-pro'); ?></th>
                    <th><?php esc_html_e('Conversions', 'oneclick-seo-pro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($referrer_data, 0, 10) as $ref): ?>
                <tr>
                    <td><?php echo esc_html($ref->referrer_name ?: $ref->referrer_source); ?></td>
                    <td><span class="type-badge type-<?php echo esc_attr($ref->referrer_type); ?>"><?php echo esc_html($ref->referrer_type); ?></span></td>
                    <td><?php echo number_format($ref->sessions); ?></td>
                    <td><?php echo number_format($ref->conversions); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Full Dashboard Link -->
    <div class="full-dashboard-link">
        <a href="<?php echo esc_url(admin_url('admin.php?page=acf-user-journey')); ?>" class="button button-primary">
            <?php esc_html_e('View Full Analytics Dashboard', 'oneclick-seo-pro'); ?> &rarr;
        </a>
    </div>

    <?php else: ?>
    <div class="notice notice-info">
        <p><?php esc_html_e('No traffic data available yet. Data will appear once visitors start coming to your site.', 'oneclick-seo-pro'); ?></p>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>

<style>
.oneclick-seo-analytics { max-width: 1200px; }
.oneclick-seo-analytics h1 { display: flex; align-items: center; gap: 10px; }
.oneclick-seo-analytics h1 .dashicons { color: #6366f1; }

/* Install Banner */
.uja-install-banner { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); border-radius: 16px; padding: 40px; margin: 20px 0; color: #fff; }
.uja-install-banner .banner-content { display: flex; gap: 40px; align-items: center; flex-wrap: wrap; }
.uja-install-banner .banner-icon { font-size: 60px; }
.uja-install-banner .banner-icon .dashicons { width: 80px; height: 80px; font-size: 80px; }
.uja-install-banner .banner-text { flex: 1; min-width: 300px; }
.uja-install-banner h2 { margin: 0 0 10px; font-size: 28px; }
.uja-install-banner p { font-size: 16px; opacity: 0.9; margin-bottom: 15px; }
.uja-install-banner .feature-list { margin: 0; padding: 0; list-style: none; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px; }
.uja-install-banner .feature-list li { display: flex; align-items: center; gap: 8px; }
.uja-install-banner .feature-list .dashicons { color: #22c55e; }
.uja-install-banner .banner-action { text-align: center; }
.uja-install-banner .button-hero { font-size: 18px; padding: 15px 30px; height: auto; display: flex; align-items: center; gap: 10px; }
.uja-install-banner .small-text { margin-top: 10px; font-size: 12px; opacity: 0.8; }

/* Preview */
.analytics-preview { margin: 30px 0; }
.analytics-preview h3 { margin-bottom: 20px; }
.preview-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
.preview-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
.preview-header { display: flex; align-items: center; gap: 10px; padding: 15px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
.preview-header .dashicons { color: #6366f1; }
.preview-header h4 { margin: 0; font-size: 14px; }
.preview-content { padding: 20px; text-align: center; }
.preview-content.placeholder { min-height: 150px; display: flex; flex-direction: column; justify-content: center; align-items: center; }
.placeholder-chart { width: 100%; height: 60px; background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 8px; }
.placeholder-pie { width: 80px; height: 80px; background: conic-gradient(#6366f1 30%, #22c55e 30% 55%, #f59e0b 55% 75%, #ec4899 75%); border-radius: 50%; opacity: 0.5; }
.placeholder-funnel { width: 100%; height: 80px; background: linear-gradient(180deg, rgba(99,102,241,0.3) 0%, rgba(99,102,241,0.1) 100%); clip-path: polygon(10% 0%, 90% 0%, 70% 100%, 30% 100%); }
.placeholder-table { width: 100%; height: 60px; background: repeating-linear-gradient(0deg, #e2e8f0, #e2e8f0 20px, #f8fafc 20px, #f8fafc 40px); border-radius: 4px; }
@keyframes shimmer { to { background-position: -200% 0; } }

/* Period Selector */
.period-selector { display: flex; gap: 5px; background: #fff; padding: 8px; border-radius: 8px; border: 1px solid #e2e8f0; width: fit-content; margin: 20px 0; }
.period-selector a { padding: 8px 16px; text-decoration: none; color: #64748b; border-radius: 6px; font-weight: 500; }
.period-selector a:hover { background: #f1f5f9; color: #1e293b; }
.period-selector a.active { background: #6366f1; color: #fff; }

/* Summary Cards */
.summary-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
.summary-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px; }
.card-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; }
.card-icon.visitors { background: #6366f1; }
.card-icon.sessions { background: #22c55e; }
.card-icon.pageviews { background: #f59e0b; }
.card-icon.conversion { background: #ec4899; }
.card-content h3 { margin: 0; font-size: 28px; font-weight: 700; color: #1e293b; }
.card-content p { margin: 5px 0 0; color: #64748b; font-size: 13px; }

/* SEO + Traffic Insights */
.seo-traffic-insight { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin: 20px 0; }
.seo-traffic-insight h3 { margin: 0 0 20px; }
.insight-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px; }
.insight-item { display: flex; gap: 15px; padding: 15px; background: #f8fafc; border-radius: 8px; }
.insight-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.insight-icon.organic { background: #dcfce7; }
.insight-icon.organic::before { content: ''; display: block; width: 20px; height: 20px; background: #22c55e; mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z'/%3E%3C/svg%3E") center/contain no-repeat; }
.insight-icon.ai { background: #e0e7ff; }
.insight-icon.ai::before { content: ''; display: block; width: 20px; height: 20px; background: #6366f1; mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z'/%3E%3C/svg%3E") center/contain no-repeat; }
.insight-content h4 { margin: 0 0 5px; font-size: 14px; }
.insight-content p { margin: 0 0 8px; font-size: 12px; color: #64748b; }
.insight-value { font-weight: 600; color: #6366f1; }
.insight-cta { display: flex; gap: 10px; flex-wrap: wrap; }

/* Traffic Sources */
.traffic-sources-section { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin: 20px 0; }
.traffic-sources-section h3 { margin: 0 0 15px; }
.type-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
.type-direct { background: #e0e7ff; color: #4338ca; }
.type-organic, .type-search { background: #dcfce7; color: #16a34a; }
.type-paid { background: #fef3c7; color: #d97706; }
.type-social { background: #fce7f3; color: #db2777; }
.type-referral { background: #ede9fe; color: #7c3aed; }
.type-ai_search, .type-ai_chatbot { background: #d1fae5; color: #059669; }

/* Full Dashboard Link */
.full-dashboard-link { text-align: center; margin: 30px 0; }
.full-dashboard-link .button-primary { font-size: 16px; padding: 12px 30px; height: auto; }
</style>
