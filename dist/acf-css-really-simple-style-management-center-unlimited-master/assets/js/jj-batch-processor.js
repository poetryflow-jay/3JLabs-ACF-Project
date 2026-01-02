/**
 * JJ Batch Processor
 * 
 * [Phase 9.4] 배치 처리 및 자동화 시스템
 * 
 * @since 9.4.0
 */

(function($) {
    'use strict';
    
    const BatchProcessor = {
        init: function() {
            this.bindEvents();
            this.loadJobs();
        },
        
        bindEvents: function() {
            const self = this;
            
            // 배치 작업 생성
            $(document).on('click', '.jj-create-batch-job', function() {
                self.createBatchJob();
            });
            
            // 작업 취소
            $(document).on('click', '.jj-cancel-batch-job', function() {
                const jobId = $(this).data('job-id');
                self.cancelJob(jobId);
            });
            
            // 작업 새로고침
            $(document).on('click', '.jj-refresh-jobs', function() {
                self.loadJobs();
            });
        },
        
        createBatchJob: function() {
            const self = this;
            
            const jobData = {
                type: $('#jj-batch-job-type').val(),
                targets: JSON.parse($('#jj-batch-targets').val() || '[]'),
                styles: JSON.parse($('#jj-batch-styles').val() || '{}'),
                scheduled_at: $('#jj-batch-scheduled-at').val() || null
            };
            
            $.ajax({
                url: jjBatchProcessor.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_create_batch_job',
                    nonce: jjBatchProcessor.nonce,
                    type: jobData.type,
                    targets: JSON.stringify(jobData.targets),
                    styles: JSON.stringify(jobData.styles),
                    scheduled_at: jobData.scheduled_at
                },
                success: function(response) {
                    if (response.success) {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '작업이 생성되었습니다.', 'success');
                        }
                        self.loadJobs();
                    } else {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '작업 생성 실패', 'error');
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
        
        cancelJob: function(jobId) {
            const self = this;
            
            if (!confirm('작업을 취소하시겠습니까?')) {
                return;
            }
            
            $.ajax({
                url: jjBatchProcessor.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_cancel_batch_job',
                    nonce: jjBatchProcessor.nonce,
                    job_id: jobId
                },
                success: function(response) {
                    if (response.success) {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '작업이 취소되었습니다.', 'success');
                        }
                        self.loadJobs();
                    } else {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '취소 실패', 'error');
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
        
        loadJobs: function() {
            const self = this;
            
            $.ajax({
                url: jjBatchProcessor.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_get_batch_jobs',
                    nonce: jjBatchProcessor.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.displayJobs(response.data.jobs);
                    }
                }
            });
        },
        
        displayJobs: function(jobs) {
            // 작업 목록 표시 로직
            console.log('Jobs:', jobs);
        }
    };
    
    // 초기화
    $(document).ready(function() {
        if (typeof jjBatchProcessor !== 'undefined') {
            BatchProcessor.init();
        }
    });
    
})(jQuery);
