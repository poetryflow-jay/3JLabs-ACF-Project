/**
 * JJ Analytics Dashboard - Realtime Dashboard Widget
 *
 * Phase 49-3: 실시간 데이터 업데이트 대시보드
 *
 * @package JJ_Analytics_Dashboard
 * @version 1.1.0
 * @since Phase 49-3
 */

(function($) {
    'use strict';

    const JJRealtimeDashboard = {
        config: {
            ajaxUrl: jjRealtimeData.ajaxUrl || ajaxurl,
            restUrl: jjRealtimeData.restUrl || '',
            nonce: jjRealtimeData.nonce || '',
            restNonce: jjRealtimeData.restNonce || '',
            refreshInterval: jjRealtimeData.refreshInterval || 30000,
            enableRealtime: jjRealtimeData.enableRealtime !== false,
            i18n: jjRealtimeData.i18n || {}
        },

        state: {
            isLoading: false,
            lastUpdate: null,
            refreshTimer: null,
            charts: {},
            currentTab: 'overview'
        },

        /**
         * 초기화
         */
        init: function() {
            this.bindEvents();
            this.loadInitialData();

            if (this.config.enableRealtime) {
                this.startAutoRefresh();
            }

            this.initRealtimeWidgets();
        },

        /**
         * 이벤트 바인딩
         */
        bindEvents: function() {
            const self = this;

            // 탭 전환
            $(document).on('click', '.jj-analytics-tab', function() {
                self.state.currentTab = $(this).data('tab');
            });

            // 수동 새로고침 버튼
            $(document).on('click', '.jj-refresh-btn', function(e) {
                e.preventDefault();
                self.refreshData();
            });

            // 차트 기간 선택
            $(document).on('change', '.jj-chart-period', function() {
                const period = $(this).val();
                const chartType = $(this).data('chart-type');
                self.loadChartData(chartType, period);
            });

            // 데이터 타입 선택
            $(document).on('click', '.jj-data-type-btn', function() {
                $(this).siblings().removeClass('active');
                $(this).addClass('active');
                const type = $(this).data('type');
                self.loadChartData(type, 7);
            });
        },

        /**
         * 실시간 위젯 초기화
         */
        initRealtimeWidgets: function() {
            this.initOverviewStats();
            this.initPluginStatus();
            this.initActivityFeed();
            this.initPerformanceMetrics();
        },

        /**
         * 개요 통계 위젯 초기화
         */
        initOverviewStats: function() {
            const container = $('#jj-realtime-overview');
            if (container.length === 0) return;

            container.html(this.renderOverviewSkeleton());
        },

        /**
         * 플러그인 상태 위젯 초기화
         */
        initPluginStatus: function() {
            const container = $('#jj-realtime-plugins');
            if (container.length === 0) return;

            container.html(this.renderPluginsSkeleton());
        },

        /**
         * 활동 피드 위젯 초기화
         */
        initActivityFeed: function() {
            const container = $('#jj-realtime-activity');
            if (container.length === 0) return;

            container.html(this.renderActivitySkeleton());
        },

        /**
         * 성능 메트릭 위젯 초기화
         */
        initPerformanceMetrics: function() {
            const container = $('#jj-realtime-performance');
            if (container.length === 0) return;

            container.html(this.renderPerformanceSkeleton());
        },

        /**
         * 초기 데이터 로드
         */
        loadInitialData: function() {
            this.refreshData();
        },

        /**
         * 데이터 새로고침
         */
        refreshData: function() {
            const self = this;

            if (this.state.isLoading) return;

            this.state.isLoading = true;
            this.showLoading();

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'jj_realtime_get_stats',
                    nonce: this.config.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.updateDashboard(response.data);
                        self.state.lastUpdate = new Date();
                        self.updateLastUpdateTime();
                    } else {
                        self.showError(response.data.message || self.config.i18n.error);
                    }
                },
                error: function() {
                    self.showError(self.config.i18n.error);
                },
                complete: function() {
                    self.state.isLoading = false;
                    self.hideLoading();
                }
            });
        },

        /**
         * 대시보드 업데이트
         */
        updateDashboard: function(data) {
            if (data.overview) {
                this.updateOverview(data.overview);
            }
            if (data.plugins) {
                this.updatePlugins(data.plugins);
            }
            if (data.activity) {
                this.updateActivity(data.activity);
            }
            if (data.performance) {
                this.updatePerformance(data.performance);
            }
        },

        /**
         * 개요 업데이트
         */
        updateOverview: function(overview) {
            const container = $('#jj-realtime-overview');
            if (container.length === 0) return;

            const html = `
                <div class="jj-stat-grid">
                    <div class="jj-stat-card">
                        <div class="jj-stat-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                            <span class="dashicons dashicons-admin-plugins"></span>
                        </div>
                        <div class="jj-stat-content">
                            <span class="jj-stat-value">${overview.active_plugins}/${overview.total_plugins}</span>
                            <span class="jj-stat-label">활성 플러그인</span>
                        </div>
                    </div>
                    <div class="jj-stat-card">
                        <div class="jj-stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <span class="dashicons dashicons-email-alt"></span>
                        </div>
                        <div class="jj-stat-content">
                            <span class="jj-stat-value">${this.formatNumber(overview.emails_sent)}</span>
                            <span class="jj-stat-label">이메일 발송</span>
                            <span class="jj-stat-sub">오늘: ${overview.emails_today}</span>
                        </div>
                    </div>
                    <div class="jj-stat-card">
                        <div class="jj-stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <span class="dashicons dashicons-feedback"></span>
                        </div>
                        <div class="jj-stat-content">
                            <span class="jj-stat-value">${this.formatNumber(overview.submissions)}</span>
                            <span class="jj-stat-label">폼 제출</span>
                            <span class="jj-stat-sub">폼: ${overview.forms_total}개</span>
                        </div>
                    </div>
                    <div class="jj-stat-card">
                        <div class="jj-stat-icon" style="background: linear-gradient(135deg, #ec4899, #db2777);">
                            <span class="dashicons dashicons-art"></span>
                        </div>
                        <div class="jj-stat-content">
                            <span class="jj-stat-value">${overview.presets_used}</span>
                            <span class="jj-stat-label">AI 프리셋</span>
                        </div>
                    </div>
                </div>
            `;

            container.html(html);
            this.animateStats();
        },

        /**
         * 플러그인 상태 업데이트
         */
        updatePlugins: function(plugins) {
            const container = $('#jj-realtime-plugins');
            if (container.length === 0) return;

            let html = '<div class="jj-plugin-list">';

            for (const [id, plugin] of Object.entries(plugins)) {
                const statusClass = plugin.health === 'healthy' ? 'status-healthy' :
                                   plugin.health === 'inactive' ? 'status-inactive' : 'status-warning';
                const statusText = plugin.health === 'healthy' ? this.config.i18n.healthy :
                                  plugin.health === 'inactive' ? this.config.i18n.inactive : this.config.i18n.warning;

                html += `
                    <div class="jj-plugin-item ${plugin.active ? '' : 'inactive'}">
                        <div class="jj-plugin-info">
                            <span class="jj-plugin-name">${this.escapeHtml(plugin.name)}</span>
                            <span class="jj-plugin-version">v${plugin.version}</span>
                        </div>
                        <div class="jj-plugin-status ${statusClass}">
                            <span class="status-dot"></span>
                            ${statusText}
                        </div>
                    </div>
                `;
            }

            html += '</div>';
            container.html(html);
        },

        /**
         * 활동 업데이트
         */
        updateActivity: function(activities) {
            const container = $('#jj-realtime-activity');
            if (container.length === 0) return;

            if (!activities || activities.length === 0) {
                container.html('<div class="jj-no-activity">최근 활동이 없습니다.</div>');
                return;
            }

            let html = '<div class="jj-activity-list">';

            activities.forEach(function(activity) {
                html += `
                    <div class="jj-activity-item">
                        <span class="dashicons dashicons-${activity.icon}"></span>
                        <div class="jj-activity-content">
                            <span class="jj-activity-message">${this.escapeHtml(activity.message)}</span>
                            <span class="jj-activity-time">${this.formatTimeAgo(activity.timestamp)}</span>
                        </div>
                    </div>
                `;
            }.bind(this));

            html += '</div>';
            container.html(html);
        },

        /**
         * 성능 업데이트
         */
        updatePerformance: function(performance) {
            const container = $('#jj-realtime-performance');
            if (container.length === 0) return;

            const html = `
                <div class="jj-performance-grid">
                    <div class="jj-perf-item">
                        <div class="jj-perf-header">
                            <span class="jj-perf-label">메모리 사용량</span>
                            <span class="jj-perf-value">${performance.memory_usage}</span>
                        </div>
                        <div class="jj-perf-bar">
                            <div class="jj-perf-fill" style="width: ${performance.memory_percent}%; background: ${this.getPerformanceColor(performance.memory_percent)}"></div>
                        </div>
                        <span class="jj-perf-sub">${performance.memory_percent}% (${performance.memory_limit} 중)</span>
                    </div>
                    <div class="jj-perf-item">
                        <div class="jj-perf-header">
                            <span class="jj-perf-label">DB 쿼리</span>
                            <span class="jj-perf-value">${performance.query_count}</span>
                        </div>
                    </div>
                    <div class="jj-perf-item">
                        <div class="jj-perf-header">
                            <span class="jj-perf-label">로드 시간</span>
                            <span class="jj-perf-value">${performance.load_time}</span>
                        </div>
                    </div>
                    <div class="jj-perf-item">
                        <div class="jj-perf-header">
                            <span class="jj-perf-label">PHP 버전</span>
                            <span class="jj-perf-value">${performance.php_version}</span>
                        </div>
                    </div>
                    <div class="jj-perf-item">
                        <div class="jj-perf-header">
                            <span class="jj-perf-label">WP 버전</span>
                            <span class="jj-perf-value">${performance.wp_version}</span>
                        </div>
                    </div>
                </div>
            `;

            container.html(html);
        },

        /**
         * 차트 데이터 로드
         */
        loadChartData: function(type, period) {
            const self = this;

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'jj_realtime_get_chart_data',
                    nonce: this.config.nonce,
                    type: type,
                    period: period
                },
                success: function(response) {
                    if (response.success) {
                        self.updateChart(type, response.data);
                    }
                }
            });
        },

        /**
         * 차트 업데이트
         */
        updateChart: function(type, data) {
            const canvasId = 'jj-chart-' + type;
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;

            if (this.state.charts[type]) {
                this.state.charts[type].destroy();
            }

            const ctx = canvas.getContext('2d');
            this.state.charts[type] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: type === 'emails' ? '이메일' : '폼 제출',
                        data: data.values,
                        borderColor: type === 'emails' ? '#6366f1' : '#10b981',
                        backgroundColor: type === 'emails' ? 'rgba(99, 102, 241, 0.1)' : 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        },

        /**
         * 자동 새로고침 시작
         */
        startAutoRefresh: function() {
            const self = this;

            if (this.state.refreshTimer) {
                clearInterval(this.state.refreshTimer);
            }

            this.state.refreshTimer = setInterval(function() {
                // 현재 탭이 보이는 경우에만 새로고침
                if (!document.hidden) {
                    self.refreshData();
                }
            }, this.config.refreshInterval);

            // 탭 가시성 변경 시 처리
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    self.refreshData();
                }
            });
        },

        /**
         * 자동 새로고침 중지
         */
        stopAutoRefresh: function() {
            if (this.state.refreshTimer) {
                clearInterval(this.state.refreshTimer);
                this.state.refreshTimer = null;
            }
        },

        // ===== Utility Methods =====

        /**
         * 로딩 표시
         */
        showLoading: function() {
            $('.jj-realtime-loading').addClass('active');
        },

        /**
         * 로딩 숨김
         */
        hideLoading: function() {
            $('.jj-realtime-loading').removeClass('active');
        },

        /**
         * 에러 표시
         */
        showError: function(message) {
            const toast = $('<div class="jj-toast error"></div>').text(message);
            $('body').append(toast);
            setTimeout(function() { toast.addClass('show'); }, 10);
            setTimeout(function() {
                toast.removeClass('show');
                setTimeout(function() { toast.remove(); }, 300);
            }, 3000);
        },

        /**
         * 마지막 업데이트 시간 표시
         */
        updateLastUpdateTime: function() {
            const el = $('.jj-last-update');
            if (el.length && this.state.lastUpdate) {
                el.text('마지막 업데이트: ' + this.formatTime(this.state.lastUpdate));
            }
        },

        /**
         * 스켈레톤 렌더링 - 개요
         */
        renderOverviewSkeleton: function() {
            return `
                <div class="jj-stat-grid">
                    ${this.renderSkeletonCard()}
                    ${this.renderSkeletonCard()}
                    ${this.renderSkeletonCard()}
                    ${this.renderSkeletonCard()}
                </div>
            `;
        },

        /**
         * 스켈레톤 렌더링 - 플러그인
         */
        renderPluginsSkeleton: function() {
            return `
                <div class="jj-plugin-list">
                    ${this.renderSkeletonItem()}
                    ${this.renderSkeletonItem()}
                    ${this.renderSkeletonItem()}
                </div>
            `;
        },

        /**
         * 스켈레톤 렌더링 - 활동
         */
        renderActivitySkeleton: function() {
            return `
                <div class="jj-activity-list">
                    ${this.renderSkeletonItem()}
                    ${this.renderSkeletonItem()}
                    ${this.renderSkeletonItem()}
                </div>
            `;
        },

        /**
         * 스켈레톤 렌더링 - 성능
         */
        renderPerformanceSkeleton: function() {
            return `
                <div class="jj-performance-grid">
                    ${this.renderSkeletonItem()}
                    ${this.renderSkeletonItem()}
                </div>
            `;
        },

        /**
         * 스켈레톤 카드 렌더링
         */
        renderSkeletonCard: function() {
            return `
                <div class="jj-stat-card skeleton">
                    <div class="skeleton-box" style="width: 40px; height: 40px; border-radius: 8px;"></div>
                    <div class="skeleton-text" style="width: 80%; height: 16px;"></div>
                </div>
            `;
        },

        /**
         * 스켈레톤 아이템 렌더링
         */
        renderSkeletonItem: function() {
            return `
                <div class="jj-skeleton-item">
                    <div class="skeleton-box" style="width: 100%; height: 40px; border-radius: 4px;"></div>
                </div>
            `;
        },

        /**
         * 통계 애니메이션
         */
        animateStats: function() {
            $('.jj-stat-value').each(function() {
                $(this).addClass('animate');
                setTimeout(() => $(this).removeClass('animate'), 500);
            });
        },

        /**
         * 숫자 포맷
         */
        formatNumber: function(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            }
            if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'K';
            }
            return num.toString();
        },

        /**
         * 시간 포맷
         */
        formatTime: function(date) {
            const h = date.getHours().toString().padStart(2, '0');
            const m = date.getMinutes().toString().padStart(2, '0');
            const s = date.getSeconds().toString().padStart(2, '0');
            return `${h}:${m}:${s}`;
        },

        /**
         * 상대 시간 포맷
         */
        formatTimeAgo: function(timestamp) {
            const now = new Date();
            const then = new Date(timestamp);
            const diff = Math.floor((now - then) / 1000);

            if (diff < 60) return '방금 전';
            if (diff < 3600) return Math.floor(diff / 60) + '분 전';
            if (diff < 86400) return Math.floor(diff / 3600) + '시간 전';
            return Math.floor(diff / 86400) + '일 전';
        },

        /**
         * 성능 색상 반환
         */
        getPerformanceColor: function(percent) {
            if (percent < 50) return '#10b981';
            if (percent < 80) return '#f59e0b';
            return '#ef4444';
        },

        /**
         * HTML 이스케이프
         */
        escapeHtml: function(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // 초기화
    $(document).ready(function() {
        JJRealtimeDashboard.init();
    });

    // 전역 접근용
    window.JJRealtimeDashboard = JJRealtimeDashboard;

})(jQuery);
