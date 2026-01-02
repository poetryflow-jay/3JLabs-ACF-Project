/**
 * JJ Performance Monitor
 * 
 * [Phase 9.5] 성능 모니터링 시스템
 * 
 * @since 9.5.0
 */

(function($) {
    'use strict';
    
    const PerformanceMonitor = {
        init: function() {
            this.bindEvents();
            this.loadMetrics();
        },
        
        bindEvents: function() {
            const self = this;
            
            // 메트릭 새로고침
            $(document).on('click', '.jj-refresh-metrics', function() {
                self.loadMetrics();
            });
            
            // 성능 리포트 생성
            $(document).on('click', '.jj-generate-performance-report', function() {
                const period = $('.jj-report-period').val() || '7days';
                self.generateReport(period);
            });
            
            // 기간 필터
            $(document).on('change', '.jj-metrics-period', function() {
                const period = $(this).val();
                self.loadMetrics(period);
            });
        },
        
        loadMetrics: function(period) {
            const self = this;
            period = period || '7days';
            
            $.ajax({
                url: jjPerformanceMonitor.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_get_performance_metrics',
                    nonce: jjPerformanceMonitor.nonce,
                    period: period
                },
                success: function(response) {
                    if (response.success) {
                        self.displayMetrics(response.data);
                    }
                },
                error: function() {
                    if (JJUtils && JJUtils.showToast) {
                        JJUtils.showToast('메트릭을 불러올 수 없습니다.', 'error');
                    }
                }
            });
        },
        
        generateReport: function(period) {
            const self = this;
            
            $.ajax({
                url: jjPerformanceMonitor.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_generate_performance_report',
                    nonce: jjPerformanceMonitor.nonce,
                    period: period
                },
                success: function(response) {
                    if (response.success) {
                        self.displayReport(response.data);
                    } else {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '리포트 생성 실패', 'error');
                        }
                    }
                },
                error: function() {
                    if (JJUtils && JJUtils.showToast) {
                        JJUtils.showToast('리포트 생성 중 오류가 발생했습니다.', 'error');
                    }
                }
            });
        },
        
        displayMetrics: function(data) {
            // 메트릭 표시 로직
            console.log('Performance metrics:', data);
        },
        
        displayReport: function(data) {
            // 리포트 표시 로직
            console.log('Performance report:', data);
        }
    };
    
    // 초기화
    $(document).ready(function() {
        if (typeof jjPerformanceMonitor !== 'undefined') {
            PerformanceMonitor.init();
        }
    });
    
})(jQuery);
