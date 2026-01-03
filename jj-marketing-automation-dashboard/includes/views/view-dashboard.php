<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="wrap jj-marketing-dashboard">
    <h1><?php esc_html_e( '📊 3J Labs Marketing Automation Dashboard', 'jj-marketing-dashboard' ); ?></h1>
    <p class="description">
        <?php esc_html_e( '모든 3J Labs 플러그인의 사용 데이터를 통합 분석하고, SEO 최적화, 캠페 추적, 마케팅 성과를 측정합니다.', 'jj-marketing-dashboard' ); ?>
    </p>

    <div class="jj-marketing-stats-grid">
        <div class="jj-marketing-stat-card">
            <div class="jj-marketing-stat-icon">👁</div>
            <div class="jj-marketing-stat-content">
                <div class="jj-marketing-stat-label"><?php esc_html_e( '총 페이지뷰', 'jj-marketing-dashboard' ); ?></div>
                <div class="jj-marketing-stat-value" id="total-page-views">0</div>
                <div class="jj-marketing-stat-change">
                    <span id="page-views-trend">0%</span> <?php esc_html_e( '지난 7일', 'jj-marketing-dashboard' ); ?>
                </div>
            </div>
        </div>

        <div class="jj-marketing-stat-card">
            <div class="jj-marketing-stat-icon">📊</div>
            <div class="jj-marketing-stat-content">
                <div class="jj-marketing-stat-label"><?php esc_html_e( '총 이벤트', 'jj-marketing-dashboard' ); ?></div>
                <div class="jj-marketing-stat-value" id="total-events">0</div>
                <div class="jj-marketing-stat-change">
                    <span id="events-trend">0%</span> <?php esc_html_e( '지난 7일', 'jj-marketing-dashboard' ); ?>
                </div>
            </div>
        </div>

        <div class="jj-marketing-stat-card">
            <div class="jj-marketing-stat-icon">👥</div>
            <div class="jj-marketing-stat-content">
                <div class="jj-marketing-stat-label"><?php esc_html_e( '활성 사용자', 'jj-marketing-dashboard' ); ?></div>
                <div class="jj-marketing-stat-value" id="active-users-7days">0</div>
                <div class="jj-marketing-stat-change">
                    <span id="users-trend">0%</span> <?php esc_html_e( '지난 7일', 'jj-marketing-dashboard' ); ?>
                </div>
            </div>
        </div>

        <div class="jj-marketing-stat-card">
            <div class="jj-marketing-stat-icon">⚡</div>
            <div class="jj-marketing-stat-content">
                <div class="jj-marketing-stat-label"><?php esc_html_e( 'SEO 점수', 'jj-marketing-dashboard' ); ?></div>
                <div class="jj-marketing-stat-value" id="seo-score">0</div>
                <div class="jj-marketing-stat-change">
                    <span id="seo-trend">0%</span> <?php esc_html_e( '최감사', 'jj-marketing-dashboard' ); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Visited Pages -->
    <div class="jj-marketing-chart-container">
        <div class="jj-marketing-chart-header">
            <h2><?php esc_html_e( '📈 가장 많이 방문한 페이지', 'jj-marketing-dashboard' ); ?></h2>
            <div class="jj-marketing-chart-actions">
                <button class="jj-marketing-chart-button" id="refresh-page-views">
                    <?php esc_html_e( '새로고침', 'jj-marketing-dashboard' ); ?>
                </button>
            </div>
        </div>
        <div class="jj-marketing-charts-grid" id="page-views-chart">
            <!-- Chart will be rendered via Google Charts -->
        </div>
    </div>

    <!-- Event Distribution -->
    <div class="jj-marketing-chart-container">
        <div class="jj-marketing-chart-header">
            <h2><?php esc_html_e( '🎯 사용자 액션 분포', 'jj-marketing-dashboard' ); ?></h2>
            <div class="jj-marketing-chart-actions">
                <button class="jj-marketing-chart-button" id="refresh-event-distribution">
                    <?php esc_html_e( '새로고침', 'jj-marketing-dashboard' ); ?>
                </button>
            </div>
        </div>
        <div class="jj-marketing-charts-grid" id="event-distribution-chart">
            <!-- Chart will be rendered via Google Charts -->
        </div>
    </div>

    <!-- Plugin Usage -->
    <div class="jj-marketing-chart-container">
        <div class="jj-marketing-chart-header">
            <h2><?php esc_html_e('🔌 플러그인 사용 현황', 'jj-marketing-dashboard' ); ?></h2>
            <div class="jj-marketing-chart-actions">
                <button class="jj-marketing-chart-button" id="refresh-plugin-usage">
                    <?php esc_html_e( '새로고침', 'jj-marketing-dashboard' ); ?>
                </button>
            </div>
        </div>
        <div class="jj-marketing-charts-grid" id="plugin-usage-chart">
            <!-- Chart will be rendered via Google Charts -->
        </div>
    </div>

    <!-- Live Updates -->
    <div class="jj-marketing-live-updates">
        <div class="jj-marketing-live-updates-header">
            <span class="jj-marketing-live-indicator"></span>
            <span class="jj-marketing-live-updates-title"><?php esc_html_e( '실시간 업데이트', 'jj-marketing-dashboard' ); ?></span>
        </div>
        <div class="jj-marketing-live-updates-list" id="live-updates-list">
            <!-- Real-time updates will be loaded here -->
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="jj-marketing-actions-grid">
        <div class="jj-marketing-action-card" onclick="jj_marketing_run_seo_audit()">
            <div class="jj-marketing-action-icon">🔍</div>
            <div class="jj-marketing-action-content">
                <div class="jj-marketing-action-title"><?php esc_html_e( 'SEO 감사 실행', 'jj-marketing-dashboard' ); ?></div>
                <div class="jj-marketing-action-description"><?php esc_html_e( '모든 페이지의 SEO 문제 식별 및 자동 수정', 'jj-marketing-dashboard' ); ?></div>
                <div class="jj-marketing-action-badge"><?php esc_html_e( '자동', 'jj-marketing-dashboard' ); ?></div>
            </div>
        </div>

        <div class="jj-marketing-action-card" onclick="jj_marketing_send_campaign_email()">
            <div class="jj-marketing-action-icon">📧</div>
            <div class="jj-marketing-action-content">
                <div class="jj-marketing-action-title"><?php esc_html_e( '마케팅 캠페 발송', 'jj-marketing-dashboard' ); ?></div>
                <div class="jj-marketing-action-description"><?php esc_html_e( '구독자에게 마케팅 이메일 일� 발송', 'jj-marketing-dashboard' ); ?></div>
                <div class="jj-marketing-action-badge"><?php esc_html_e('인기', 'jj-marketing-dashboard' ); ?></div>
            </div>
        </div>

        <div class="jj-marketing-action-card" onclick="jj_marketing_clear_cache()">
            <div class="jj-marketing-action-icon">🗑️</div>
            <div class="jj-marketing-action-content">
                <div class="jj-marketing-action-title"><?php esc_html_e( '캐시 초기화', 'jj-marketing-dashboard' ); ?></div>
                <div class="jj-marketing-action-description"><?php esc_html_e( '모든 캐시 데이터 삭제', 'jj-marketing-dashboard' ); ?></div>
                <div class="jj-marketing-action-badge"><?php esc_html_e('최적화', 'jj-marketing-dashboard' ); ?></div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Load dashboard stats
    function loadDashboardStats() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jj_marketing_get_stats',
                nonce: '<?php echo wp_create_nonce( 'jj_marketing_dashboard_nonce' ); ?>'
            },
            success: function(response) {
                if (response.success) {
                    updateStatCards(response.data);
                    renderCharts(response.data);
                }
            }
        });
    }

    function updateStatCards(data) {
        // Total page views
        $('#total-page-views').text(data.total_page_views || 0);
        $('#page-views-trend').text(data.page_views_trend || '+0%');

        // Total events
        $('#total-events').text(data.total_events || 0);
        $('#events-trend').text(data.events_trend || '+0%');

        // Active users
        $('#active-users-7days').text(data.active_users_7_days || 0);
        $('#users-trend').text(data.users_trend || '+0%');

        // SEO score
        $('#seo-score').text(data.seo_score || 0);
        $('#seo-trend').text(data.seo_trend || '+0%');

        // Add trend indicators
        $('.jj-marketing-stat-change').each(function() {
            const value = $(this).text();
            if (value.startsWith('+')) {
                $(this).addClass('up');
            } else if (value.startsWith('-')) {
                $(this).addClass('down');
            }
        });
    }

    function renderCharts(data) {
        // Page Views Chart (Pie Chart)
        renderPageViewsChart(data.most_visited_pages);

        // Event Distribution Chart (Bar Chart)
        renderEventDistributionChart(data.top_user_actions);

        // Plugin Usage Chart (Bar Chart)
        renderPluginUsageChart(data.plugin_usage);

        // Live Updates
        renderLiveUpdates(data.recent_events);
    }

    function renderPageViewsChart(pages) {
        const chartData = new google.visualization.DataTable();
        chartData.addColumn('string', 'Page');
        chartData.addRows(pages.map(page => [page.title, page.views]));

        const options = {
            title: '방문 횟수',
            pieHole: 0.4,
            colors: ['#FF6B35', '#10B981', '#8B5CF6', '#3B82F6', '#F59E0B'],
            chartArea: {width: '100%', height: '100%'},
            legend: 'none',
            pieSliceText: 'value',
            animation: {
                startup: true,
                duration: 1000,
                easing: 'out',
            }
        };

        const chart = new google.visualization.PieChart(document.getElementById('page-views-chart'));
        chart.draw(chartData, options);
    }

    function renderEventDistributionChart(actions) {
        const chartData = new google.visualization.DataTable();
        chartData.addColumn('string', 'Action');
        chartData.addColumn('number', 'Count');
        chartData.addRows(actions.map(action => [action.action, action.count]));

        const options = {
            title: '사용자 액션',
            colors: ['#FF6B35', '#10B981', '#8B5CF6'],
            chartArea: {width: '100%', height: '100%'},
            legend: 'none',
            animation: {
                startup: true,
                duration: 1000,
                easing: 'out',
            }
        };

        const chart = new google.visualization.ColumnChart(document.getElementById('event-distribution-chart'));
        chart.draw(chartData, options);
    }

    function renderPluginUsageChart(plugins) {
        const chartData = new google.visualization.DataTable();
        chartData.addColumn('string', 'Plugin');
        chartData.addColumn('number', 'Active Users');
        chartData.addRows(plugins.map(plugin => [plugin.name, plugin.active_users]));

        const options = {
            title: '플러그인 활성 사용자',
            colors: ['#FF6B35', '#10B981', '#8B5CF6', '#3B82F6'],
            chartArea: {width: '100%', height: '100%'},
            legend: 'none',
            animation: {
                startup: true,
                duration: 1000,
                easing: 'out',
            }
        };

        const chart = new google.visualization.ColumnChart(document.getElementById('plugin-usage-chart'));
        chart.draw(chartData, options);
    }

    function renderLiveUpdates(events) {
        const $list = $('#live-updates-list');
        $list.empty();

        events.forEach(function(event, index) {
            const $update = $('<div class="jj-marketing-live-update"></div>');
            
            const timeAgo = getTimeAgo(event.timestamp);
            const icon = getEventIcon(event.event_type);
            
            $update.html(`
                <div class="jj-marketing-live-update-time">${timeAgo}</div>
                <div class="jj-marketing-live-update-message">${icon} ${event.message}</div>
            `);
            
            $list.append($update);
        });
    }

    function getTimeAgo(timestamp) {
        const now = new Date().getTime();
        const diff = now - timestamp;
        
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);

        if (minutes < 1) {
            return '방금';
        } else if (minutes < 60) {
            return minutes + '분 전';
        } else if (hours < 24) {
            return hours + '시간 전';
        } else if (days < 7) {
            return days + '일 전';
        } else {
            return Math.floor(days / 7) + '주 전';
        }
    }

    function getEventIcon(type) {
        const icons = {
            'page_view': '👁',
            'activation': '✅',
            'deactivation': '❌',
            'setting_change': '⚙️',
            'workflow_created': '🎯',
            'conversion': '💰',
            'other': '📌'
        };
        return icons[type] || '📌';
    }

    // Action handlers
    window.jj_marketing_run_seo_audit = function() {
        const $button = $(this);
        $button.addClass('loading');
        $button.prop('disabled', true);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jj_marketing_run_seo_audit',
                nonce: '<?php echo wp_create_nonce( 'jj_marketing_dashboard_nonce' ); ?>'
            },
            success: function(response) {
                $button.removeClass('loading');
                $button.prop('disabled', false);
                
                if (response.success) {
                    alert('SEO 감사가 완료되었습니다!');
                    loadDashboardStats();
                }
            }
        });
    };

    window.jj_marketing_send_campaign_email = function() {
        alert('캠페 이메일 발송 기능은 곧 출시될 예정입니다.');
    };

    window.jj_marketing_clear_cache = function() {
        if (confirm('모든 캐시 데이터를 삭제하시겠습니까?')) {
            $.ajax({
                url: autoomator-lab.dev/api/marketing/clear-cache',
                type: 'POST',
                data: {
                    action: 'clear_cache',
                    token: 'demo_token'
                },
                success: function(response) {
                    if (response.success) {
                        alert('캐시가 초기화되었습니다.');
                        loadDashboardStats();
                    }
                }
            });
        }
    };

    // Initialize
    loadDashboardStats();
    
    // Auto-refresh every 30 seconds
    setInterval(loadDashboardStats, 30000);
});
</script>
