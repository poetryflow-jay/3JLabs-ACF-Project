/**
 * JJ AI Optimizer
 * 
 * [Phase 9.4] 자동 스타일 최적화 시스템
 * 
 * @since 9.4.0
 */

(function($) {
    'use strict';
    
    const AIOptimizer = {
        init: function() {
            this.bindEvents();
        },
        
        bindEvents: function() {
            const self = this;
            
            // 성능 최적화 버튼
            $(document).on('click', '.jj-optimize-performance', function() {
                self.optimizePerformance();
            });
            
            // 접근성 개선 버튼
            $(document).on('click', '.jj-improve-accessibility', function() {
                const autoApply = $(this).data('auto-apply') || false;
                self.improveAccessibility(autoApply);
            });
        },
        
        optimizePerformance: function() {
            const self = this;
            
            if (JJUtils && JJUtils.showToast) {
                JJUtils.showToast(jjAIOptimizer.strings.optimizing, 'info');
            }
            
            $.ajax({
                url: jjAIOptimizer.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_optimize_performance',
                    nonce: jjAIOptimizer.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.displayOptimizationResults(response.data);
                    } else {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '최적화 실패', 'error');
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
        
        improveAccessibility: function(autoApply) {
            const self = this;
            
            if (JJUtils && JJUtils.showToast) {
                JJUtils.showToast('접근성 개선 중...', 'info');
            }
            
            $.ajax({
                url: jjAIOptimizer.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_improve_accessibility',
                    nonce: jjAIOptimizer.nonce,
                    auto_apply: autoApply
                },
                success: function(response) {
                    if (response.success) {
                        self.displayAccessibilityResults(response.data, autoApply);
                    } else {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '개선 실패', 'error');
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
        
        displayOptimizationResults: function(data) {
            // 최적화 결과 표시 로직
            if (JJUtils && JJUtils.showToast) {
                JJUtils.showToast(jjAIOptimizer.strings.optimized, 'success');
            }
        },
        
        displayAccessibilityResults: function(data, autoApply) {
            // 접근성 개선 결과 표시 로직
            if (autoApply && JJUtils && JJUtils.showToast) {
                JJUtils.showToast('접근성이 자동으로 개선되었습니다.', 'success');
            }
        }
    };
    
    // 초기화
    $(document).ready(function() {
        if (typeof jjAIOptimizer !== 'undefined') {
            AIOptimizer.init();
        }
    });
    
})(jQuery);
