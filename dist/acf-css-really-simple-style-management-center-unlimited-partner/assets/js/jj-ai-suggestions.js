/**
 * JJ AI Suggestions
 * 
 * [Phase 9.4] 컨텍스트 인식 AI 제안 시스템
 * 
 * @since 9.4.0
 */

(function($) {
    'use strict';
    
    const AISuggestions = {
        $container: null,
        currentSuggestions: [],
        debounceTimer: null,
        
        init: function() {
            this.createUI();
            this.bindEvents();
            
            // 실시간 제안 (입력 필드 모니터링)
            this.setupRealtimeSuggestions();
        },
        
        createUI: function() {
            // AI 제안 컨테이너가 이미 있으면 스킵
            if ($('#jj-ai-suggestions').length > 0) {
                return;
            }
            
            const uiHtml = `
                <div id="jj-ai-suggestions" style="position: fixed; bottom: 20px; right: 20px; width: 320px; max-height: 500px; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 999999; display: none;">
                    <div style="padding: 15px; border-bottom: 1px solid #eee; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-radius: 8px 8px 0 0;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <h4 style="margin: 0; font-size: 14px; font-weight: 600;">
                                    <span class="dashicons dashicons-lightbulb" style="vertical-align: middle; margin-right: 5px;"></span>
                                    AI 제안
                                </h4>
                            </div>
                            <button type="button" class="jj-ai-suggestions-close" style="background: none; border: none; color: #fff; cursor: pointer; padding: 0; width: 24px; height: 24px;">
                                <span class="dashicons dashicons-no-alt"></span>
                            </button>
                        </div>
                    </div>
                    <div id="jj-ai-suggestions-content" style="max-height: 400px; overflow-y: auto; padding: 15px;">
                        <div class="jj-ai-loading" style="text-align: center; padding: 20px; display: none;">
                            <span class="spinner is-active"></span>
                            <p style="margin: 10px 0 0 0; color: #666; font-size: 12px;">${jjAISuggestions.strings.suggesting}</p>
                        </div>
                        <div class="jj-ai-suggestions-list">
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(uiHtml);
            this.$container = $('#jj-ai-suggestions');
        },
        
        bindEvents: function() {
            const self = this;
            
            // 닫기 버튼
            $(document).on('click', '.jj-ai-suggestions-close', function() {
                self.hide();
            });
            
            // 제안 적용
            $(document).on('click', '.jj-apply-suggestion', function() {
                const suggestionId = $(this).data('suggestion-id');
                const suggestionData = self.currentSuggestions.find(s => s.id === suggestionId);
                if (suggestionData) {
                    self.applySuggestion(suggestionData);
                }
            });
            
            // 제안 피드백
            $(document).on('click', '.jj-feedback-suggestion', function() {
                const suggestionId = $(this).data('suggestion-id');
                const feedback = $(this).data('feedback'); // 'like' or 'dislike'
                self.sendFeedback(suggestionId, feedback);
            });
        },
        
        setupRealtimeSuggestions: function() {
            const self = this;
            
            // 입력 필드 모니터링 (색상, 폰트 등)
            $(document).on('input', 'input[type="text"][name*="color"], input[type="text"][name*="font"], input[type="text"][name*="size"]', function() {
                clearTimeout(self.debounceTimer);
                
                self.debounceTimer = setTimeout(function() {
                    const $input = $(this);
                    const context = {
                        type: self.detectContextType($input),
                        input: $input.val()
                    };
                    
                    if (context.input && context.input.length > 2) {
                        self.showSuggestions(context);
                    }
                }.bind(this), 1000); // 1초 디바운스
            });
        },
        
        detectContextType: function($input) {
            const name = $input.attr('name') || '';
            if (name.indexOf('color') !== -1) return 'palette';
            if (name.indexOf('font') !== -1) return 'typography';
            if (name.indexOf('button') !== -1) return 'button';
            return 'general';
        },
        
        showSuggestions: function(context) {
            const self = this;
            
            if (!jjAISuggestions.ai_extension_active) {
                // AI Extension이 없으면 기본 제안만 표시
                return;
            }
            
            self.$container.find('.jj-ai-loading').show();
            self.$container.find('.jj-ai-suggestions-list').empty();
            self.$container.slideDown();
            
            $.ajax({
                url: jjAISuggestions.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_get_ai_suggestions',
                    nonce: jjAISuggestions.nonce,
                    type: context.type,
                    input: context.input
                },
                success: function(response) {
                    self.$container.find('.jj-ai-loading').hide();
                    
                    if (response.success && response.data.suggestions.length > 0) {
                        self.currentSuggestions = response.data.suggestions.map((s, i) => ({
                            ...s,
                            id: 'suggestion_' + Date.now() + '_' + i
                        }));
                        self.renderSuggestions(self.currentSuggestions);
                    } else {
                        self.$container.find('.jj-ai-suggestions-list').html(
                            '<p style="text-align: center; color: #666; padding: 20px;">' + 
                            jjAISuggestions.strings.no_suggestions + 
                            '</p>'
                        );
                    }
                },
                error: function() {
                    self.$container.find('.jj-ai-loading').hide();
                    self.hide();
                }
            });
        },
        
        renderSuggestions: function(suggestions) {
            const self = this;
            let html = '';
            
            suggestions.forEach(function(suggestion) {
                const confidence = Math.round((suggestion.confidence || 0.5) * 100);
                const badge = suggestion.personalized ? 
                    '<span style="background: #00a32a; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-left: 5px;">개인화</span>' :
                    (suggestion.best_practice ? 
                    '<span style="background: #0073aa; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-left: 5px;">베스트</span>' : '');
                
                html += `
                    <div class="jj-suggestion-item" style="padding: 12px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;">
                        <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 8px;">
                            <h5 style="margin: 0; font-size: 13px; font-weight: 600;">
                                ${suggestion.title} ${badge}
                            </h5>
                            <span style="font-size: 11px; color: #666;">${confidence}%</span>
                        </div>
                        <p style="margin: 0 0 10px 0; font-size: 12px; color: #666; line-height: 1.4;">
                            ${suggestion.description}
                        </p>
                        <div style="display: flex; gap: 5px;">
                            <button type="button" class="button button-small button-primary jj-apply-suggestion" data-suggestion-id="${suggestion.id}">
                                ${jjAISuggestions.strings.apply_suggestion}
                            </button>
                            <button type="button" class="button button-small jj-feedback-suggestion" data-suggestion-id="${suggestion.id}" data-feedback="like" title="좋아요">
                                <span class="dashicons dashicons-thumbs-up" style="font-size: 14px;"></span>
                            </button>
                            <button type="button" class="button button-small jj-feedback-suggestion" data-suggestion-id="${suggestion.id}" data-feedback="dislike" title="싫어요">
                                <span class="dashicons dashicons-thumbs-down" style="font-size: 14px;"></span>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            this.$container.find('.jj-ai-suggestions-list').html(html);
        },
        
        applySuggestion: function(suggestion) {
            const self = this;
            
            $.ajax({
                url: jjAISuggestions.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_apply_ai_suggestion',
                    nonce: jjAISuggestions.nonce,
                    suggestion_id: suggestion.id,
                    suggestion_data: JSON.stringify(suggestion)
                },
                success: function(response) {
                    if (response.success) {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '제안이 적용되었습니다.', 'success');
                        }
                        self.hide();
                        // 페이지 새로고침 (설정이 변경되었으므로)
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
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
        
        sendFeedback: function(suggestionId, feedback) {
            $.ajax({
                url: jjAISuggestions.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_feedback_suggestion',
                    nonce: jjAISuggestions.nonce,
                    suggestion_id: suggestionId,
                    feedback: feedback
                },
                success: function(response) {
                    if (response.success && JJUtils && JJUtils.showToast) {
                        JJUtils.showToast('피드백 감사합니다!', 'success', { duration: 2000 });
                    }
                }
            });
        },
        
        hide: function() {
            this.$container.slideUp();
        }
    };
    
    // 초기화
    $(document).ready(function() {
        if (typeof jjAISuggestions !== 'undefined') {
            AISuggestions.init();
        }
    });
    
})(jQuery);
