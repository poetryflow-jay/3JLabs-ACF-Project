/**
 * JJ AI Debugger
 * 
 * [Phase 9.4] AI 기반 디버깅 시스템
 * 
 * @since 9.4.0
 */

(function($) {
    'use strict';
    
    const AIDebugger = {
        init: function() {
            this.bindEvents();
        },
        
        bindEvents: function() {
            const self = this;
            
            // 충돌 감지 버튼
            $(document).on('click', '.jj-detect-conflicts', function() {
                self.detectConflicts();
            });
            
            // 에러 분석 버튼
            $(document).on('click', '.jj-analyze-errors', function() {
                self.analyzeErrors();
            });
        },
        
        detectConflicts: function() {
            const self = this;
            
            if (JJUtils && JJUtils.showToast) {
                JJUtils.showToast(jjAIDebugger.strings.detecting, 'info');
            }
            
            $.ajax({
                url: jjAIDebugger.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_detect_style_conflicts',
                    nonce: jjAIDebugger.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.displayConflicts(response.data);
                    } else {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '감지 실패', 'error');
                        }
                    }
                },
                error: function() {
                    if (JJUtils && JJUtils.showToast) {
                        JJUtils.showToast('오류가 발생했습니다.', 'error');
                    }
                }
            });
        },
        
        analyzeErrors: function() {
            const self = this;
            
            if (JJUtils && JJUtils.showToast) {
                JJUtils.showToast(jjAIDebugger.strings.analyzing, 'info');
            }
            
            $.ajax({
                url: jjAIDebugger.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_analyze_errors',
                    nonce: jjAIDebugger.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.displayErrorAnalysis(response.data);
                    } else {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '분석 실패', 'error');
                        }
                    }
                },
                error: function() {
                    if (JJUtils && JJUtils.showToast) {
                        JJUtils.showToast('오류가 발생했습니다.', 'error');
                    }
                }
            });
        },
        
        displayConflicts: function(data) {
            // 충돌 결과 표시 로직
            console.log('Conflicts detected:', data);
        },
        
        displayErrorAnalysis: function(data) {
            // 에러 분석 결과 표시 로직
            console.log('Error analysis:', data);
        }
    };
    
    // 초기화
    $(document).ready(function() {
        if (typeof jjAIDebugger !== 'undefined') {
            AIDebugger.init();
        }
    });
    
})(jQuery);
