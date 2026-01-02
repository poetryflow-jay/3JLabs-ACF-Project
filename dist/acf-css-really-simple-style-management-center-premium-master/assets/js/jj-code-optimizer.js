/**
 * JJ Code Optimizer
 * 
 * [Phase 9.5] 코드 최적화 시스템
 * 
 * @since 9.5.0
 */

(function($) {
    'use strict';
    
    const CodeOptimizer = {
        init: function() {
            this.bindEvents();
        },
        
        bindEvents: function() {
            const self = this;
            
            // 코드 품질 분석
            $(document).on('click', '.jj-analyze-code-quality', function() {
                self.analyzeCodeQuality();
            });
            
            // 최적화 적용
            $(document).on('click', '.jj-apply-optimizations', function() {
                const optimizations = self.getSelectedOptimizations();
                self.applyOptimizations(optimizations);
            });
        },
        
        analyzeCodeQuality: function() {
            const self = this;
            
            $.ajax({
                url: jjCodeOptimizer.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_analyze_code_quality',
                    nonce: jjCodeOptimizer.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.displayAnalysisResults(response.data);
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
        
        applyOptimizations: function(optimizations) {
            const self = this;
            
            if (optimizations.length === 0) {
                if (JJUtils && JJUtils.showToast) {
                    JJUtils.showToast('적용할 최적화가 없습니다.', 'warning');
                }
                return;
            }
            
            $.ajax({
                url: jjCodeOptimizer.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_optimize_code',
                    nonce: jjCodeOptimizer.nonce,
                    optimizations: JSON.stringify(optimizations)
                },
                success: function(response) {
                    if (response.success) {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast('최적화가 적용되었습니다.', 'success');
                        }
                        self.analyzeCodeQuality(); // 재분석
                    } else {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '적용 실패', 'error');
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
        
        getSelectedOptimizations: function() {
            const optimizations = [];
            $('.jj-optimization-item:checked').each(function() {
                optimizations.push({
                    type: $(this).data('type'),
                    data: $(this).data('data')
                });
            });
            return optimizations;
        },
        
        displayAnalysisResults: function(data) {
            // 분석 결과 표시 로직
            console.log('Code quality analysis:', data);
        }
    };
    
    // 초기화
    $(document).ready(function() {
        if (typeof jjCodeOptimizer !== 'undefined') {
            CodeOptimizer.init();
        }
    });
    
})(jQuery);
