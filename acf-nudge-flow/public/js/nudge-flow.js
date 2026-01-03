/**
 * ACF Nudge Flow - Frontend JavaScript
 * 
 * @package ACF_Nudge_Flow
 */

(function($) {
    'use strict';

    // 전역 객체
    window.ACFNudgeFlow = window.ACFNudgeFlow || {};

    const NudgeFlow = {
        config: {},
        visitorData: {},
        workflows: [],
        displayedNudges: [],
        startTime: Date.now(),
        scrollDepth: 0,

        /**
         * 초기화
         */
        init: function() {
            if (typeof acfNudgeFlow === 'undefined') {
                return;
            }

            this.config = acfNudgeFlow;
            this.visitorData = acfNudgeFlow.visitorData || {};
            this.workflows = acfNudgeFlow.workflows || [];

            this.bindEvents();
            this.evaluateWorkflows();
        },

        /**
         * 이벤트 바인딩
         */
        bindEvents: function() {
            const self = this;

            // 스크롤 깊이 추적
            $(window).on('scroll.acfNudge', $.throttle(200, function() {
                const scrollTop = $(window).scrollTop();
                const docHeight = $(document).height() - $(window).height();
                self.scrollDepth = Math.round((scrollTop / docHeight) * 100);
                self.checkScrollTriggers();
            }));

            // 이탈 의도 감지
            $(document).on('mouseleave.acfNudge', function(e) {
                if (e.clientY < 10) {
                    self.checkExitIntentTriggers();
                }
            });

            // 시간 기반 트리거
            this.startTimeBasedTriggers();

            // 닫기 버튼
            $(document).on('click', '.acf-nudge-close, .acf-nudge-dismiss-link', function(e) {
                e.preventDefault();
                const $container = $(this).closest('[data-nudge-type]');
                self.hideNudge($container);
            });

            // 오버레이 클릭
            $(document).on('click', '.acf-nudge-modal-overlay', function() {
                self.hideNudge($('.acf-nudge-modal'));
                $(this).removeClass('active');
            });

            // CTA 클릭 추적
            $(document).on('click', '.acf-nudge-cta-button, .acf-nudge-bar-cta', function() {
                const $nudge = $(this).closest('[data-nudge-type]');
                const workflowId = $nudge.data('workflow-id');
                
                // 일반 이벤트 추적
                self.trackEvent('conversion', {
                    nudge_type: $nudge.data('nudge-type'),
                    workflow_id: workflowId
                });
                
                // MAB 최적화 엔진에 전환 기록
                self.recordConversion(workflowId, true);
            });
        },

        /**
         * 워크플로우 평가
         */
        evaluateWorkflows: function() {
            const self = this;

            this.workflows.forEach(function(workflow) {
                if (self.shouldShowWorkflow(workflow)) {
                    self.executeWorkflow(workflow);
                }
            });
        },

        /**
         * 워크플로우 표시 여부 확인
         */
        shouldShowWorkflow: function(workflow) {
            // 이미 표시된 워크플로우 확인
            if (this.displayedNudges.includes(workflow.id)) {
                return false;
            }

            // 닫은 넛지 확인
            if (this.visitorData.dismissed && this.visitorData.dismissed.includes(workflow.id)) {
                return false;
            }

            // 서버에서 전달된 워크플로우는 이미 트리거가 평가됨
            return true;
        },

        /**
         * 워크플로우 실행
         */
        executeWorkflow: function(workflow) {
            const self = this;

            // 각 노드 처리
            workflow.nodes.forEach(function(node) {
                if (node.type === 'action') {
                    self.executeAction(node.data.action_id, node.data.settings, workflow.id);
                }
            });
        },

        /**
         * 액션 실행
         */
        executeAction: function(actionId, settings, workflowId) {
            const self = this;

            switch (actionId) {
                case 'popup_center':
                    this.showModal(settings, workflowId);
                    break;

                case 'popup_slide_in':
                    this.showSlideIn(settings, workflowId);
                    break;

                case 'bar_top':
                    this.showBar('top', settings, workflowId);
                    break;

                case 'bar_bottom':
                    this.showBar('bottom', settings, workflowId);
                    break;

                case 'toast':
                    this.showToast(settings, workflowId);
                    break;

                case 'redirect':
                    this.redirect(settings);
                    break;

                case 'show_element':
                    this.showElement(settings);
                    break;

                default:
                    console.log('Unknown action:', actionId);
            }

            // 노출 추적
            this.trackEvent('impression', {
                action_id: actionId,
                workflow_id: workflowId
            });
        },

        /**
         * 모달 표시
         */
        showModal: function(settings, workflowId) {
            const template = this.getTemplate('modal', settings);
            const $modal = $('.acf-nudge-modal');
            const $overlay = $('.acf-nudge-modal-overlay');

            $modal.find('.acf-nudge-modal-content').html(template);
            $modal.attr('data-workflow-id', workflowId);

            setTimeout(function() {
                $overlay.addClass('active');
                $modal.addClass('active');
            }, settings.delay ? settings.delay * 1000 : 0);

            this.displayedNudges.push(workflowId);
        },

        /**
         * 슬라이드 인 표시
         */
        showSlideIn: function(settings, workflowId) {
            const template = this.getTemplate('slide-in', settings);
            const $slideIn = $('.acf-nudge-slide-in');

            $slideIn.find('.acf-nudge-slide-in-content').html(template);
            $slideIn.attr('data-workflow-id', workflowId);

            if (settings.position) {
                $slideIn.addClass('position-' + settings.position);
            }

            setTimeout(function() {
                $slideIn.addClass('active');
            }, settings.delay ? settings.delay * 1000 : 0);

            this.displayedNudges.push(workflowId);
        },

        /**
         * 바 표시
         */
        showBar: function(position, settings, workflowId) {
            const template = this.getTemplate('bar', settings);
            const $bar = $('.acf-nudge-bar-' + position);

            $bar.find('.acf-nudge-bar-content').html(template);
            $bar.attr('data-workflow-id', workflowId);

            if (settings.bg_color) {
                $bar.css('background', settings.bg_color);
            }
            if (settings.text_color) {
                $bar.css('color', settings.text_color);
            }

            setTimeout(function() {
                $bar.addClass('active');
            }, settings.delay ? settings.delay * 1000 : 0);

            this.displayedNudges.push(workflowId);
        },

        /**
         * 토스트 표시
         */
        showToast: function(settings, workflowId) {
            const template = this.getTemplate('toast', settings);
            const $container = $('.acf-nudge-toast-container');

            if (settings.position) {
                $container.addClass('position-' + settings.position);
            }

            const $toast = $(template);
            $toast.attr('data-workflow-id', workflowId);
            $container.append($toast);

            // 자동 닫기
            const duration = (settings.duration || 5) * 1000;
            setTimeout(function() {
                $toast.fadeOut(300, function() {
                    $(this).remove();
                });
            }, duration);

            this.displayedNudges.push(workflowId);
        },

        /**
         * 리다이렉트
         */
        redirect: function(settings) {
            if (!settings.url) return;

            const delay = (settings.delay || 0) * 1000;

            setTimeout(function() {
                if (settings.new_tab) {
                    window.open(settings.url, '_blank');
                } else {
                    window.location.href = settings.url;
                }
            }, delay);
        },

        /**
         * 요소 표시
         */
        showElement: function(settings) {
            if (!settings.selector) return;

            const $element = $(settings.selector);
            const animation = settings.animation || 'fade';

            switch (animation) {
                case 'fade':
                    $element.fadeIn(300);
                    break;
                case 'slide':
                    $element.slideDown(300);
                    break;
                case 'zoom':
                    $element.css('transform', 'scale(0)').show().animate({
                        opacity: 1
                    }, {
                        step: function(now) {
                            $(this).css('transform', 'scale(' + now + ')');
                        },
                        duration: 300
                    });
                    break;
                default:
                    $element.show();
            }
        },

        /**
         * 넛지 숨기기
         */
        hideNudge: function($container) {
            const type = $container.data('nudge-type');
            const workflowId = $container.data('workflow-id');

            $container.removeClass('active');

            // 닫기 상태 저장
            this.dismissNudge(workflowId);
        },

        /**
         * 넛지 닫기 저장
         */
        dismissNudge: function(nudgeId) {
            $.ajax({
                url: this.config.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'acf_nudge_dismiss',
                    nonce: this.config.nonce,
                    nudge_id: nudgeId,
                    dismiss_type: 'session'
                }
            });
        },

        /**
         * 템플릿 렌더링
         */
        getTemplate: function(type, data) {
            const $template = $('#acf-nudge-template-' + type);
            if (!$template.length) return '';

            let html = $template.html();

            // 간단한 Mustache 스타일 치환
            Object.keys(data).forEach(function(key) {
                const regex = new RegExp('\\{\\{' + key + '\\}\\}', 'g');
                const tripleRegex = new RegExp('\\{\\{\\{' + key + '\\}\\}\\}', 'g');
                
                html = html.replace(tripleRegex, data[key] || '');
                html = html.replace(regex, self.escapeHtml(data[key] || ''));
            });

            // 조건부 블록 처리 (간단한 버전)
            html = html.replace(/\{\{#(\w+)\}\}([\s\S]*?)\{\{\/\1\}\}/g, function(match, key, content) {
                return data[key] ? content : '';
            });

            return html;
        },

        /**
         * HTML 이스케이프
         */
        escapeHtml: function(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        /**
         * 스크롤 트리거 확인
         */
        checkScrollTriggers: function() {
            // 스크롤 깊이 트리거 처리
        },

        /**
         * 이탈 의도 트리거 확인
         */
        checkExitIntentTriggers: function() {
            // 이탈 의도 트리거 처리
        },

        /**
         * 시간 기반 트리거 시작
         */
        startTimeBasedTriggers: function() {
            // 시간 기반 트리거 처리
        },

        /**
         * 이벤트 추적
         */
        trackEvent: function(eventType, eventData) {
            $.ajax({
                url: this.config.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'acf_nudge_track',
                    nonce: this.config.nonce,
                    event_type: eventType,
                    event_data: eventData
                }
            });
        },

        /**
         * MAB 최적화 엔진에 전환 기록
         */
        recordConversion: function(nudgeId, success) {
            $.ajax({
                url: this.config.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'acf_nudge_conversion',
                    nonce: this.config.nonce,
                    nudge_id: nudgeId,
                    success: success ? 'true' : 'false'
                },
                success: function(response) {
                    if (response.success && window.console) {
                        console.log('MAB: 전환 기록됨', response.data);
                    }
                }
            });
        }
    };

    // jQuery throttle 폴리필
    if (!$.throttle) {
        $.throttle = function(delay, callback) {
            let lastCall = 0;
            return function() {
                const now = Date.now();
                if (now - lastCall >= delay) {
                    lastCall = now;
                    callback.apply(this, arguments);
                }
            };
        };
    }

    // DOM Ready
    $(document).ready(function() {
        NudgeFlow.init();
    });

    // 전역 접근
    window.ACFNudgeFlow = NudgeFlow;

})(jQuery);
