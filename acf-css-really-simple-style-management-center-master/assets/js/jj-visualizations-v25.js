/**
 * JJ Visualizations System v25.0.0
 * 고급 시각화 시스템
 * 
 * @package ACF_CSS_Style_Guide
 * @version 25.0.0
 * @author 3J Labs
 * 
 * 주요 기능:
 * - 통계 대시보드
 * - 차트 및 그래프
 * - 실시간 데이터 시각화
 * - 인터랙티브 차트
 */

(function($) {
    'use strict';

    /**
     * Visualizations Manager
     */
    const Visualizations = {
        charts: {},
        
        /**
         * 초기화
         */
        init: function() {
            this.initCharts();
            this.initDashboard();
            this.bindEvents();
        },

        /**
         * 차트 초기화
         */
        initCharts: function() {
            // Chart.js가 로드되어 있는지 확인
            if (typeof Chart === 'undefined') {
                // Chart.js 로드
                this.loadChartJS();
                return;
            }

            // 기존 차트 초기화
            $('.jj-chart-container').each(function() {
                const $container = $(this);
                const chartType = $container.data('chart-type') || 'line';
                const chartData = $container.data('chart-data') || {};
                
                Visualizations.createChart($container, chartType, chartData);
            });
        },

        /**
         * Chart.js 로드
         */
        loadChartJS: function() {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
            script.onload = function() {
                Visualizations.initCharts();
            };
            document.head.appendChild(script);
        },

        /**
         * 차트 생성
         */
        createChart: function($container, type, data) {
            const canvas = $container.find('canvas')[0] || document.createElement('canvas');
            if (!$container.find('canvas').length) {
                $container.append(canvas);
            }

            const ctx = canvas.getContext('2d');
            const chartId = $container.attr('id') || 'chart-' + Date.now();

            // 기존 차트가 있으면 제거
            if (this.charts[chartId]) {
                this.charts[chartId].destroy();
            }

            // 새 차트 생성
            this.charts[chartId] = new Chart(ctx, {
                type: type,
                data: data.data || {},
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            enabled: true,
                        }
                    },
                    ...data.options
                }
            });
        },

        /**
         * 대시보드 초기화
         */
        initDashboard: function() {
            // 통계 카드 생성
            this.createStatsCards();
            
            // 실시간 업데이트
            this.startRealTimeUpdates();
        },

        /**
         * 통계 카드 생성
         */
        createStatsCards: function() {
            const stats = this.getStats();
            
            if (!$('.jj-stats-dashboard').length) {
                return;
            }

            const $dashboard = $('.jj-stats-dashboard');
            let html = '<div class="jj-stats-grid">';

            stats.forEach(function(stat) {
                html += '<div class="jj-stat-card">' +
                    '<div class="jj-stat-icon">' + stat.icon + '</div>' +
                    '<div class="jj-stat-content">' +
                    '<div class="jj-stat-value">' + stat.value + '</div>' +
                    '<div class="jj-stat-label">' + stat.label + '</div>' +
                    '</div>' +
                    '<div class="jj-stat-trend ' + (stat.trend > 0 ? 'positive' : 'negative') + '">' +
                    (stat.trend > 0 ? '↑' : '↓') + ' ' + Math.abs(stat.trend) + '%' +
                    '</div>' +
                    '</div>';
            });

            html += '</div>';
            $dashboard.html(html);
        },

        /**
         * 통계 데이터 가져오기
         */
        getStats: function() {
            // 실제로는 AJAX로 서버에서 가져옴
            return [
                {
                    icon: '🎨',
                    value: '1,234',
                    label: '색상 팔레트',
                    trend: 12
                },
                {
                    icon: '🔤',
                    value: '567',
                    label: '폰트 설정',
                    trend: 8
                },
                {
                    icon: '🔘',
                    value: '890',
                    label: '버튼 스타일',
                    trend: -3
                },
                {
                    icon: '📝',
                    value: '1,012',
                    label: '폼 설정',
                    trend: 15
                }
            ];
        },

        /**
         * 실시간 업데이트 시작
         */
        startRealTimeUpdates: function() {
            setInterval(function() {
                Visualizations.updateCharts();
            }, 5000); // 5초마다 업데이트
        },

        /**
         * 차트 업데이트
         */
        updateCharts: function() {
            // AJAX로 최신 데이터 가져와서 차트 업데이트
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'jj_get_chart_data',
                    nonce: jj_admin_params.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // 차트 데이터 업데이트
                        Object.keys(Visualizations.charts).forEach(function(chartId) {
                            const chart = Visualizations.charts[chartId];
                            if (chart && response.data[chartId]) {
                                chart.data = response.data[chartId];
                                chart.update();
                            }
                        });
                    }
                }
            });
        },

        /**
         * 이벤트 바인딩
         */
        bindEvents: function() {
            // 차트 컨테이너 동적 추가 시 자동 초기화
            $(document).on('jj-chart-container-added', function(e, $container) {
                const chartType = $container.data('chart-type') || 'line';
                const chartData = $container.data('chart-data') || {};
                Visualizations.createChart($container, chartType, chartData);
            });
        }
    };

    // DOM 준비 시 초기화
    $(document).ready(function() {
        Visualizations.init();
    });

    // 전역으로 노출
    window.JJVisualizations = Visualizations;

})(jQuery);
