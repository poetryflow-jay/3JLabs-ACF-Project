/**
 * JJ Nudge System - 사용자 행동 유도 및 온보딩 시스템
 * 
 * @since 13.2.0
 */
(function($) {
    'use strict';

    /**
     * 넛지 시스템 메인 객체
     */
    const JJNudge = {
        config: null,
        activeNudges: [],
        onboardingModal: null,
        currentOnboardingStep: 0,

        /**
         * 초기화
         */
        init: function() {
            this.config = window.jjNudgeSystem || {};
            
            if (!this.config.ajax_url) {
                console.warn('JJ Nudge System: Configuration not found');
                return;
            }

            this.activeNudges = this.config.active_nudges || [];
            this.currentOnboardingStep = this.config.onboarding?.current_step || 0;

            this.createContainers();
            this.bindEvents();
            this.showActiveNudges();
        },

        /**
         * 컨테이너 생성
         */
        createContainers: function() {
            const positions = ['top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right'];
            
            positions.forEach(function(pos) {
                if (!$('.jj-nudge-container.' + pos).length) {
                    $('body').append('<div class="jj-nudge-container ' + pos + '"></div>');
                }
            });

            // 온보딩 오버레이
            if (!$('.jj-onboarding-overlay').length) {
                $('body').append('<div class="jj-onboarding-overlay"></div>');
            }
        },

        /**
         * 이벤트 바인딩
         */
        bindEvents: function() {
            const self = this;

            // 넛지 닫기
            $(document).on('click', '.jj-nudge-close', function(e) {
                e.preventDefault();
                const $nudge = $(this).closest('.jj-nudge');
                const nudgeId = $nudge.data('nudge-id');
                self.dismissNudge(nudgeId, $nudge);
            });

            // 넛지 액션 클릭
            $(document).on('click', '.jj-nudge-action', function(e) {
                const $btn = $(this);
                const action = $btn.data('action');
                const $nudge = $btn.closest('.jj-nudge');
                const nudgeId = $nudge.data('nudge-id');

                switch (action) {
                    case 'dismiss':
                        e.preventDefault();
                        self.dismissNudge(nudgeId, $nudge);
                        break;
                    case 'navigate':
                        // 기본 링크 동작 허용
                        break;
                    case 'start_onboarding':
                        e.preventDefault();
                        self.dismissNudge(nudgeId, $nudge);
                        self.startOnboarding();
                        break;
                    default:
                        e.preventDefault();
                        self.handleNudgeAction(action, nudgeId, $nudge);
                }
            });

            // 온보딩 버튼들
            $(document).on('click', '.jj-onboarding-btn-next', function(e) {
                e.preventDefault();
                self.nextOnboardingStep();
            });

            $(document).on('click', '.jj-onboarding-btn-prev', function(e) {
                e.preventDefault();
                self.prevOnboardingStep();
            });

            $(document).on('click', '.jj-onboarding-btn-skip', function(e) {
                e.preventDefault();
                self.closeOnboarding();
            });

            $(document).on('click', '.jj-onboarding-btn-complete', function(e) {
                e.preventDefault();
                self.completeOnboarding();
            });

            $(document).on('click', '.jj-onboarding-btn-action', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                if (url) {
                    window.location.href = url;
                }
            });

            // 오버레이 클릭 시 닫기
            $(document).on('click', '.jj-onboarding-overlay', function(e) {
                if ($(e.target).is('.jj-onboarding-overlay')) {
                    self.closeOnboarding();
                }
            });

            // ESC 키로 닫기
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('.jj-onboarding-overlay.active').length) {
                    self.closeOnboarding();
                }
            });
        },

        /**
         * 활성 넛지 표시
         */
        showActiveNudges: function() {
            const self = this;

            // 우선순위로 정렬
            this.activeNudges.sort(function(a, b) {
                return (b.priority || 0) - (a.priority || 0);
            });

            // 최대 3개만 표시
            const nudgesToShow = this.activeNudges.slice(0, 3);

            nudgesToShow.forEach(function(nudge, index) {
                setTimeout(function() {
                    self.showNudge(nudge);
                }, index * 300);
            });
        },

        /**
         * 넛지 표시
         */
        showNudge: function(nudge) {
            const position = nudge.position || 'bottom-right';
            const $container = $('.jj-nudge-container.' + position);

            if (!$container.length) {
                return;
            }

            const $nudge = this.createNudgeElement(nudge);
            $container.append($nudge);

            // 하이라이트 타겟이 있으면 강조
            if (nudge.target) {
                $(nudge.target).addClass('jj-nudge-highlight');
            }
        },

        /**
         * 넛지 요소 생성
         */
        createNudgeElement: function(nudge) {
            const typeBadgeText = this.getTypeBadgeText(nudge.type);
            
            let actionsHtml = '';
            if (nudge.actions && nudge.actions.length) {
                actionsHtml = '<div class="jj-nudge-actions">';
                nudge.actions.forEach(function(action) {
                    const style = action.style || 'secondary';
                    const url = action.url || '#';
                    const actionAttr = action.action ? ' data-action="' + action.action + '"' : '';
                    
                    if (action.action === 'navigate' && action.url) {
                        actionsHtml += '<a href="' + action.url + '" class="jj-nudge-action ' + style + '"' + actionAttr + '>' + action.label + '</a>';
                    } else {
                        actionsHtml += '<button type="button" class="jj-nudge-action ' + style + '"' + actionAttr + '>' + action.label + '</button>';
                    }
                });
                actionsHtml += '</div>';
            }

            const dismissible = nudge.dismissible !== false;
            const closeBtn = dismissible ? '<button type="button" class="jj-nudge-close" aria-label="' + (this.config.strings?.dismiss || 'Close') + '"><span class="dashicons dashicons-no-alt"></span></button>' : '';

            return $(`
                <div class="jj-nudge" data-nudge-id="${nudge.id}" data-type="${nudge.type}">
                    <div class="jj-nudge-header">
                        <div class="jj-nudge-header-content">
                            <h4 class="jj-nudge-title">${this.escapeHtml(nudge.title)}</h4>
                            <span class="jj-nudge-type-badge">${typeBadgeText}</span>
                        </div>
                        ${closeBtn}
                    </div>
                    <div class="jj-nudge-body">
                        <p class="jj-nudge-message">${this.escapeHtml(nudge.message)}</p>
                    </div>
                    ${actionsHtml}
                </div>
            `);
        },

        /**
         * 넛지 유형 뱃지 텍스트
         */
        getTypeBadgeText: function(type) {
            const types = {
                'onboarding': '시작 가이드',
                'incomplete': '미완료 설정',
                'optimization': '최적화 팁',
                'new_feature': '새 기능',
                'tip': '팁'
            };
            return types[type] || type;
        },

        /**
         * 넛지 닫기
         */
        dismissNudge: function(nudgeId, $nudge) {
            const self = this;

            // 애니메이션
            $nudge.addClass('jj-nudge-exiting');

            // 하이라이트 제거
            $('.jj-nudge-highlight').removeClass('jj-nudge-highlight');

            setTimeout(function() {
                $nudge.remove();
            }, 300);

            // 서버에 닫힘 상태 저장
            $.post(this.config.ajax_url, {
                action: 'jj_nudge_dismiss',
                nonce: this.config.nonce,
                nudge_id: nudgeId
            });

            // 활성 목록에서 제거
            this.activeNudges = this.activeNudges.filter(function(n) {
                return n.id !== nudgeId;
            });
        },

        /**
         * 넛지 액션 처리
         */
        handleNudgeAction: function(action, nudgeId, $nudge) {
            const self = this;

            $.post(this.config.ajax_url, {
                action: 'jj_nudge_action',
                nonce: this.config.nonce,
                action_type: action,
                nudge_id: nudgeId
            }).done(function(response) {
                if (response.success) {
                    if (response.data.action === 'start_onboarding') {
                        self.startOnboarding();
                    }
                }
            });

            self.dismissNudge(nudgeId, $nudge);
        },

        /**
         * 온보딩 시작
         */
        startOnboarding: function() {
            this.currentOnboardingStep = 0;
            this.renderOnboardingModal();
            $('.jj-onboarding-overlay').addClass('active');
        },

        /**
         * 온보딩 모달 렌더링
         */
        renderOnboardingModal: function() {
            const steps = this.config.onboarding?.steps || [];
            const currentStep = steps[this.currentOnboardingStep] || {};
            const totalSteps = steps.length;
            const progress = ((this.currentOnboardingStep + 1) / totalSteps) * 100;

            const isFirst = this.currentOnboardingStep === 0;
            const isLast = this.currentOnboardingStep === totalSteps - 1;

            const prevBtn = isFirst ? '' : '<button type="button" class="jj-onboarding-btn jj-onboarding-btn-secondary jj-onboarding-btn-prev">' + (this.config.strings?.previous || '이전') + '</button>';
            
            let nextBtn = '';
            if (isLast) {
                nextBtn = '<button type="button" class="jj-onboarding-btn jj-onboarding-btn-primary jj-onboarding-btn-complete">' + (this.config.strings?.complete || '완료') + '</button>';
            } else {
                nextBtn = '<button type="button" class="jj-onboarding-btn jj-onboarding-btn-primary jj-onboarding-btn-next">' + (this.config.strings?.next || '다음') + '</button>';
            }

            let actionBtn = '';
            if (currentStep.action_url) {
                actionBtn = '<button type="button" class="jj-onboarding-btn jj-onboarding-btn-secondary jj-onboarding-btn-action" data-url="' + currentStep.action_url + '">설정 페이지로 이동</button>';
            }

            const modalHtml = `
                <div class="jj-onboarding-modal">
                    <div class="jj-onboarding-header">
                        <h2>🚀 JJ CSS Premium 시작하기</h2>
                        <p>간단한 설정으로 사이트 스타일을 통합 관리하세요</p>
                    </div>
                    <div class="jj-onboarding-progress">
                        <div class="jj-onboarding-progress-bar">
                            <div class="jj-onboarding-progress-fill" style="width: ${progress}%"></div>
                        </div>
                        <div class="jj-onboarding-progress-text">
                            ${this.currentOnboardingStep + 1} / ${totalSteps}
                        </div>
                    </div>
                    <div class="jj-onboarding-body">
                        <div class="jj-onboarding-step-icon">
                            <span class="dashicons ${currentStep.icon || 'dashicons-admin-generic'}"></span>
                        </div>
                        <h3 class="jj-onboarding-step-title">${this.escapeHtml(currentStep.title || '')}</h3>
                        <p class="jj-onboarding-step-description">${this.escapeHtml(currentStep.description || '')}</p>
                    </div>
                    <div class="jj-onboarding-footer">
                        <div class="jj-onboarding-footer-left">
                            <button type="button" class="jj-onboarding-btn jj-onboarding-btn-skip">${this.config.strings?.skip || '건너뛰기'}</button>
                        </div>
                        <div class="jj-onboarding-footer-right">
                            ${actionBtn}
                            ${prevBtn}
                            ${nextBtn}
                        </div>
                    </div>
                </div>
            `;

            $('.jj-onboarding-overlay').html(modalHtml);

            // 타겟 요소 하이라이트
            if (currentStep.target) {
                $(currentStep.target).addClass('jj-nudge-highlight');
            } else {
                $('.jj-nudge-highlight').removeClass('jj-nudge-highlight');
            }
        },

        /**
         * 다음 온보딩 단계
         */
        nextOnboardingStep: function() {
            const self = this;
            const steps = this.config.onboarding?.steps || [];

            // 현재 단계 완료 저장
            $.post(this.config.ajax_url, {
                action: 'jj_complete_onboarding_step',
                nonce: this.config.nonce,
                step: this.currentOnboardingStep
            }).done(function(response) {
                if (response.success) {
                    if (response.data.complete) {
                        self.closeOnboarding();
                        self.showCompletionMessage();
                    } else {
                        self.currentOnboardingStep = response.data.current_step;
                        self.renderOnboardingModal();
                    }
                }
            });
        },

        /**
         * 이전 온보딩 단계
         */
        prevOnboardingStep: function() {
            if (this.currentOnboardingStep > 0) {
                this.currentOnboardingStep--;
                this.renderOnboardingModal();
            }
        },

        /**
         * 온보딩 완료
         */
        completeOnboarding: function() {
            const self = this;

            $.post(this.config.ajax_url, {
                action: 'jj_complete_onboarding_step',
                nonce: this.config.nonce,
                step: this.currentOnboardingStep
            }).done(function(response) {
                self.closeOnboarding();
                self.showCompletionMessage();
            });
        },

        /**
         * 온보딩 닫기
         */
        closeOnboarding: function() {
            $('.jj-onboarding-overlay').removeClass('active');
            $('.jj-nudge-highlight').removeClass('jj-nudge-highlight');
        },

        /**
         * 완료 메시지 표시
         */
        showCompletionMessage: function() {
            const nudge = {
                id: 'onboarding_complete',
                type: 'tip',
                title: '🎉 설정 완료!',
                message: '기본 설정이 완료되었습니다. 이제 사이트 전체에 일관된 스타일이 적용됩니다.',
                position: 'top-center',
                dismissible: true,
                actions: [
                    {
                        label: '스타일 가이드 보기',
                        action: 'navigate',
                        url: this.config.style_guide_url || (window.location.origin + '/wp-admin/admin.php?page=jj-style-guide'),
                        style: 'primary'
                    }
                ]
            };

            this.showNudge(nudge);
        },

        /**
         * HTML 이스케이프
         */
        escapeHtml: function(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // DOM 준비 시 초기화
    $(document).ready(function() {
        JJNudge.init();
    });

    // 전역 노출
    window.JJNudge = JJNudge;

})(jQuery);
