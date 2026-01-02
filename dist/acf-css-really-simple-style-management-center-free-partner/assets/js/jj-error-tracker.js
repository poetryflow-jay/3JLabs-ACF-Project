/**
 * JJ Error Tracker
 * 
 * [Phase 9.5] 에러 추적 시스템
 * 
 * @since 9.5.0
 */

(function($) {
    'use strict';
    
    const ErrorTracker = {
        init: function() {
            this.bindEvents();
            this.loadErrorLog();
        },
        
        bindEvents: function() {
            const self = this;
            
            // 에러 로그 새로고침
            $(document).on('click', '.jj-refresh-error-log', function() {
                self.loadErrorLog();
            });
            
            // 에러 패턴 분석
            $(document).on('click', '.jj-analyze-error-patterns', function() {
                self.analyzePatterns();
            });
            
            // 심각도 필터
            $(document).on('change', '.jj-error-severity-filter', function() {
                const severity = $(this).val();
                self.filterBySeverity(severity);
            });
        },
        
        loadErrorLog: function() {
            const self = this;
            const limit = $('.jj-error-limit').val() || 50;
            const severity = $('.jj-error-severity-filter').val() || '';
            
            $.ajax({
                url: jjErrorTracker.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_get_error_log',
                    nonce: jjErrorTracker.nonce,
                    limit: limit,
                    severity: severity
                },
                success: function(response) {
                    if (response.success) {
                        self.displayErrorLog(response.data.errors);
                    }
                },
                error: function() {
                    if (JJUtils && JJUtils.showToast) {
                        JJUtils.showToast('에러 로그를 불러올 수 없습니다.', 'error');
                    }
                }
            });
        },
        
        analyzePatterns: function() {
            const self = this;
            
            $.ajax({
                url: jjErrorTracker.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_analyze_error_patterns',
                    nonce: jjErrorTracker.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.displayPatternAnalysis(response.data);
                    }
                },
                error: function() {
                    if (JJUtils && JJUtils.showToast) {
                        JJUtils.showToast('패턴 분석에 실패했습니다.', 'error');
                    }
                }
            });
        },
        
        filterBySeverity: function(severity) {
            this.loadErrorLog();
        },
        
        displayErrorLog: function(errors) {
            // 에러 로그 표시 로직
            console.log('Error log:', errors);
        },
        
        displayPatternAnalysis: function(data) {
            // 패턴 분석 결과 표시 로직
            console.log('Pattern analysis:', data);
        }
    };
    
    // 초기화
    $(document).ready(function() {
        if (typeof jjErrorTracker !== 'undefined') {
            ErrorTracker.init();
        }
    });
    
})(jQuery);
