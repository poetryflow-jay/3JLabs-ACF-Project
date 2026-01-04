// JJ Analytics Dashboard - Main JavaScript

(function($) {
    'use strict';

    /**
     * JJ Analytics Dashboard - Main Application Script
     * @version 1.0.0
     * @author 3J Labs (Jason + Jenny + Mikael)
     */

    const JJAnalytics = {
        state: {
            currentTab: 'overview',
            data: null,
            refreshInterval: 30000, // 30 seconds
            lastRefresh: null
        },

        /**
         * Tab switching
         */
        switchTab: function(tabId) {
            this.state.currentTab = tabId;
            
            $('.jj-analytics-tab').removeClass('active');
            $('.jj-analytics-tab[data-tab="' + tabId + '"]').addClass('active');
            
            $('.jj-analytics-tab-pane').removeClass('active');
            $('#jj-tab-' + tabId).addClass('active');
            
            // Load tab data
            this.loadTabData(tabId);
        },

        /**
         * Load data for current tab
         */
        loadTabData: function() {
            switch(this.state.currentTab) {
                case 'overview':
                    this.loadOverview();
                    break;
                case 'metrics':
                    this.loadMetrics();
                    break;
                case 'trends':
                    this.loadTrends();
                    break;
                case 'system':
                    this.loadSystem();
                    break;
            }
        },

        /**
         * Load overview data
         */
        loadOverview: function() {
            this.showLoading();
            
            $.ajax({
                url: jjAnalytics.ajaxurl,
                type: 'POST',
                data: {
                    action: 'jj_analytics_get_overview',
                    nonce: jjAnalytics.nonce
                },
                success: function(response) {
                    JJAnalytics.hideLoading();
                    if (response.success) {
                        JJAnalytics.renderOverview(response.data);
                    } else {
                        JJAnalytics.showError(response.data || '데이터 로드 실패');
                    }
                },
                error: function() {
                    JJAnalytics.hideLoading();
                    JJAnalytics.showError('서버 오류가 발생했습니다.');
                }
            });
        },

        /**
         * Load metrics data
         */
        loadMetrics: function() {
            this.showLoading();
            
            $.ajax({
                url: jjAnalytics.ajaxurl,
                type: 'POST',
                data: {
                    action: 'jj_analytics_get_plugin_metrics',
                    nonce: jjAnalytics.nonce,
                    plugin_slug: $('#jj-metrics-plugin-select').val()
                },
                success: function(response) {
                    JJAnalytics.hideLoading();
                    if (response.success) {
                        JJAnalytics.renderPluginMetrics(response.data);
                    } else {
                        JJAnalytics.showError(response.data || '데이터 로드 실패');
                    }
                },
                error: function() {
                    JJAnalytics.hideLoading();
                    JJAnalytics.showError('서버 오류가 발생했습니다.');
                }
            });
        },

        /**
         * Load trends data
         */
        loadTrends: function() {
            this.showLoading();
            
            $.ajax({
                url: jjAnalytics.ajaxurl,
                type: 'POST',
                data: {
                    action: 'jj_analytics_get_trends',
                    nonce: jjAnalytics.nonce
                },
                success: function(response) {
                    JJAnalytics.hideLoading();
                    if (response.success) {
                        JJAnalytics.renderTrendsCharts(response.data);
                    } else {
                        JJAnalytics.showError(response.data || '데이터 로드 실패');
                    }
                },
                error: function() {
                    JJAnalytics.hideLoading();
                    JJAnalytics.showError('서버 오류가 발생했습니다.');
                }
            });
        },

        /**
         * Load system data
         */
        loadSystem: function() {
            this.showLoading();
            
            $.ajax({
                url: jjAnalytics.ajaxurl,
                type: 'POST',
                data: {
                    action: 'jj_analytics_get_comparison',
                    nonce: jjAnalytics.nonce
                },
                success: function(response) {
                    JJAnalytics.hideLoading();
                    if (response.success) {
                        JJAnalytics.renderComparisonChart(response.data);
                    } else {
                        JJAnalytics.showError(response.data || '데이터 로드 실패');
                    }
                },
                error: function() {
                    JJAnalytics.hideLoading();
                    JJAnalytics.showError('서버 오류가 발생했습니다.');
                }
            });
        },

        /**
         * Render overview section
         */
        renderOverview: function(data) {
            const html = `
                <div class="jj-stats-grid">
                    <!-- Total Installations -->
                    <div class="jj-stat-card" style="background: linear-gradient(135deg, #6366f1 0%, #764ba2 100%); padding: 25px; border-radius: 12px; color: #fff; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1);">
                        <h3 style="margin: 0 0 15px 0;">총 설치 수</h3>
                        <div class="jj-stat-value" style="font-size: 42px; font-weight: 700; margin-bottom: 8px;">${data.total_installations}</div>
                        <div class="jj-stat-label">모든 플러그인 설치</div>
                    </div>

                    <!-- Active Plugins -->
                    <div class="jj-stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 25px; border-radius: 12px; color: #fff; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.1);">
                        <h3 style="margin: 0 0 15px 0;">활성화된 플러그인</h3>
                        <div class="jj-stat-value" style="font-size: 42px; font-weight: 700; margin-bottom: 8px;">${data.active_plugins}</div>
                        <div class="jj-stat-label">현재 사용 중인 플러그인</div>
                    </div>

                    <!-- License Distribution -->
                    <div class="jj-stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); padding: 25px; border-radius: 12px; color: #fff; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                        <h3 style="margin: 0 0 15px 0;">라이선스 분포</h3>
                        <div class="jj-metrics-container" style="margin: 20px 0;">
                            ${data.license_distribution.free || 0} FREE
                            ${data.license_distribution.basic || 0} BASIC
                            ${data.license_distribution.premium || 0} PREMIUM
                            ${data.license_distribution.unlimited || 0} UNLIMITED
                        </div>
                    </div>
                </div>
            `;

            $('#jj-overview-section').html(html);
        },

        /**
         * Render plugin metrics
         */
        renderPluginMetrics: function(data) {
            let html = '';
            
            if (data.installs) {
                html += `
                    <div style="margin-bottom: 20px;">
                        <h3 style="color: var(--jj-indigo);">📦 설치 통계</h3>
                        <div class="jj-metrics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 15px;">
                `;
                
                data.installs.forEach(function(plugin) {
                    html += `
                        <div class="jj-metric-card">
                            <div style="font-size: 18px; font-weight: 600; color: var(--jj-indigo); margin-bottom: 10px;">${plugin.name}</div>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin-bottom: 15px;">
                                <div>
                                    <strong>${plugin.installs.toLocaleString()}</strong>
                                </div>
                                <div style="font-size: 14px; color: var(--jj-gray);">설치 수</div>
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin-bottom: 15px;">
                                <strong>${plugin.updates.toLocaleString()}</strong>
                                </div>
                                <div style="font-size: 14px; color: var(--jj-gray);">업데이트 수</div>
                            </div>
                        </div>
                    `;
                });
                
                html += `</div>`;
            } else {
                html = '<p>설치 데이터가 없습니다.</p>';
            }

            $('#jj-metrics-section').html(html);
        },

        /**
         * Render trends charts
         */
        renderTrendsCharts: function(data) {
            // Installations chart
            this.createChart('jj-installations-chart', 'line', data.installations.labels, [{
                label: '설치수',
                data: data.installations.installs,
                borderColor: 'rgba(102, 126, 234, 1)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]);

            // Performance chart
            if (data.performance) {
                this.createChart('jj-performance-chart', 'line', data.performance.labels, [{
                    label: '평균 성과',
                    data: data.performance.scores,
                    borderColor: 'rgba(16, 185, 129, 1)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]);
            }
        },

        /**
         * Render comparison chart
         */
        renderComparisonChart: function(data) {
            this.createChart('jj-comparison-chart', 'doughnut', data.labels, [{
                label: data.datasets[0].label,
                data: data.datasets[0].data,
                backgroundColor: [
                    'rgba(99, 102, 241, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(245, 158, 11, 1)'
                ],
                borderWidth: 0
            }]);
        },

        /**
         * Create Chart.js chart
         */
        createChart: function(canvasId, type, labels, datasets) {
            const ctx = document.getElementById(canvasId);
            if (!ctx) return;

            const config = {
                type: type,
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    family: 'Noto Sans KR'
                                }
                            }
                        }
                    }
                }
            }
            };

            new Chart(ctx, config);
        },

        /**
         * Loading indicators
         */
        showLoading: function() {
            $('.jj-analytics-app').addClass('loading');
            $('.jj-analytics-tab-content').append('<div class="jj-loading-overlay"><div class="jj-loading-spinner"></div>데이터 로드 중...</div></div>');
        },

        hideLoading: function() {
            $('.jj-analytics-app').removeClass('loading');
            $('.jj-loading-overlay').remove();
        },

        /**
         * Error display
         */
        showError: function(message) {
            alert(message);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        // Localize variables
        jjAnalytics.ajaxurl = window.ajaxurl || (typeof jjAnalyticsSettings !== 'undefined' ? jjAnalyticsSettings.ajaxurl : '/wp-admin/admin-ajax.php');
        jjAnalytics.nonce = window.jjAnalyticsNonce || (typeof jjAnalyticsSettings !== 'undefined' ? jjAnalyticsSettings.nonce : '');

        // Tab switching
        $('.jj-analytics-tab').on('click', function(e) {
            e.preventDefault();
            JJAnalytics.switchTab($(this).data('tab'));
        });
    });

    console.log('JJ Analytics Dashboard initialized');
})(jQuery);
