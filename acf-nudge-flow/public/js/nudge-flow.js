/**
 * ACF Nudge Flow - Frontend JavaScript
 *
 * v22.5.0: 완전한 트리거 구현, 고급 분석, 성능 최적화
 *
 * @package ACF_Nudge_Flow
 * @since 22.5.0
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
        pendingNudges: [],
        startTime: Date.now(),
        scrollDepth: 0,
        maxScrollDepth: 0,
        timeOnSite: 0,
        isExitIntentEnabled: true,
        exitIntentFired: false,
        scrollTriggersFired: {},
        timeTriggersFired: {},
        mousePosition: { x: 0, y: 0 },
        lastActivity: Date.now(),
        idleTimeout: null,
        analytics: {
            pageViews: 0,
            interactions: 0,
            conversions: 0
        },

        /**
         * 초기화
         */
        init: function() {
            if (typeof acfNudgeFlow === 'undefined') {
                console.warn('[NudgeFlow] Configuration not found');
                return;
            }

            this.config = acfNudgeFlow;
            this.visitorData = acfNudgeFlow.visitorData || {};
            this.workflows = acfNudgeFlow.workflows || [];

            // 초기화 설정
            this.startTime = Date.now();
            this.analytics.pageViews++;

            // 이벤트 바인딩
            this.bindEvents();

            // 저장된 상태 복원
            this.restoreState();

            // 워크플로우 평가
            this.evaluateWorkflows();

            // 디버그 모드
            if (this.config.debug) {
                console.log('[NudgeFlow] Initialized', {
                    workflows: this.workflows.length,
                    visitor: this.visitorData
                });
            }
        },

        /**
         * 이벤트 바인딩
         */
        bindEvents: function() {
            const self = this;

            // ===================
            // 스크롤 트래킹
            // ===================
            let scrollThrottle = null;
            $(window).on('scroll.acfNudge', function() {
                if (scrollThrottle) return;
                scrollThrottle = setTimeout(function() {
                    scrollThrottle = null;
                    self.handleScroll();
                }, 100);
            });

            // ===================
            // 이탈 의도 감지
            // ===================
            $(document).on('mouseleave.acfNudge', function(e) {
                if (e.clientY < 10) {
                    self.handleExitIntent(e);
                }
            });

            // 마우스 위치 추적 (이탈 의도 개선용)
            $(document).on('mousemove.acfNudge', function(e) {
                self.mousePosition = { x: e.clientX, y: e.clientY };
                self.lastActivity = Date.now();
            });

            // ===================
            // 시간 기반 트리거
            // ===================
            this.startTimeBasedTriggers();

            // ===================
            // 클릭/인터랙션 트래킹
            // ===================
            $(document).on('click.acfNudge', function(e) {
                self.analytics.interactions++;
                self.lastActivity = Date.now();

                // 특정 요소 클릭 트리거
                self.handleClickTrigger(e);
            });

            // ===================
            // 유휴 상태 감지
            // ===================
            this.startIdleDetection();

            // ===================
            // 가시성 변경 (탭 전환)
            // ===================
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    self.handleTabLeave();
                } else {
                    self.handleTabReturn();
                }
            });

            // ===================
            // 페이지 이탈 전 처리
            // ===================
            $(window).on('beforeunload.acfNudge', function() {
                self.saveState();
                self.sendAnalytics();
            });

            // ===================
            // 넛지 UI 이벤트
            // ===================
            // 닫기 버튼
            $(document).on('click', '.acf-nudge-close, .acf-nudge-dismiss-link, [data-nudge-dismiss]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const $container = $(this).closest('[data-nudge-type]');
                self.hideNudge($container);
            });

            // 오버레이 클릭
            $(document).on('click', '.acf-nudge-modal-overlay', function(e) {
                if ($(e.target).is('.acf-nudge-modal-overlay')) {
                    self.hideNudge($('.acf-nudge-modal'));
                    $(this).removeClass('active');
                }
            });

            // ESC 키로 모달 닫기
            $(document).on('keydown.acfNudge', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    const $activeModal = $('.acf-nudge-modal.active');
                    if ($activeModal.length) {
                        self.hideNudge($activeModal);
                        $('.acf-nudge-modal-overlay').removeClass('active');
                    }
                }
            });

            // CTA 버튼 클릭
            $(document).on('click', '.acf-nudge-cta-button, .acf-nudge-bar-cta, [data-nudge-cta]', function(e) {
                const $nudge = $(this).closest('[data-nudge-type]');
                const workflowId = $nudge.data('workflow-id');
                const ctaUrl = $(this).attr('href') || $(this).data('url');

                // 전환 추적
                self.analytics.conversions++;
                self.trackEvent('conversion', {
                    nudge_type: $nudge.data('nudge-type'),
                    workflow_id: workflowId,
                    cta_url: ctaUrl
                });

                // MAB 전환 기록
                self.recordConversion(workflowId, true);
            });

            // 폼 제출 추적
            $(document).on('submit', '.acf-nudge-form, [data-nudge-form]', function(e) {
                const $form = $(this);
                const $nudge = $form.closest('[data-nudge-type]');
                const workflowId = $nudge.data('workflow-id');

                self.trackEvent('form_submit', {
                    workflow_id: workflowId,
                    form_id: $form.attr('id') || 'nudge-form'
                });
            });
        },

        /**
         * 스크롤 처리
         */
        handleScroll: function() {
            const scrollTop = $(window).scrollTop();
            const docHeight = $(document).height() - $(window).height();
            const currentDepth = docHeight > 0 ? Math.round((scrollTop / docHeight) * 100) : 0;

            this.scrollDepth = currentDepth;
            this.maxScrollDepth = Math.max(this.maxScrollDepth, currentDepth);

            // 스크롤 트리거 확인
            this.checkScrollTriggers();
        },

        /**
         * 스크롤 트리거 확인
         */
        checkScrollTriggers: function() {
            const self = this;
            const currentDepth = this.scrollDepth;

            this.workflows.forEach(function(workflow) {
                if (self.displayedNudges.includes(workflow.id)) return;
                if (self.scrollTriggersFired[workflow.id]) return;

                workflow.nodes.forEach(function(node) {
                    if (node.type !== 'trigger') return;

                    const triggerId = node.data.trigger_id;
                    const settings = node.data.settings || {};

                    if (triggerId === 'scroll_depth') {
                        const targetDepth = parseInt(settings.depth || settings.percentage || 50, 10);

                        if (currentDepth >= targetDepth) {
                            self.scrollTriggersFired[workflow.id] = true;

                            if (self.config.debug) {
                                console.log('[NudgeFlow] Scroll trigger fired:', {
                                    workflow: workflow.id,
                                    target: targetDepth,
                                    current: currentDepth
                                });
                            }

                            // 다른 조건 확인 후 실행
                            if (self.checkAllConditions(workflow)) {
                                self.executeWorkflow(workflow);
                            }
                        }
                    }
                });
            });
        },

        /**
         * 이탈 의도 처리
         */
        handleExitIntent: function(e) {
            const self = this;

            // 이미 발동됨
            if (this.exitIntentFired || !this.isExitIntentEnabled) return;

            // 마우스 이동 속도 확인 (빠르게 위로 이동할 때만)
            if (e.clientY >= 10) return;

            // 페이지에 충분히 머물렀는지 확인 (최소 3초)
            const timeOnPage = Date.now() - this.startTime;
            if (timeOnPage < 3000) return;

            this.exitIntentFired = true;

            if (this.config.debug) {
                console.log('[NudgeFlow] Exit intent detected', {
                    mouseY: e.clientY,
                    timeOnPage: timeOnPage
                });
            }

            // 이탈 의도 트리거가 있는 워크플로우 찾기
            this.workflows.forEach(function(workflow) {
                if (self.displayedNudges.includes(workflow.id)) return;

                let hasExitIntent = false;
                workflow.nodes.forEach(function(node) {
                    if (node.type === 'trigger' && node.data.trigger_id === 'exit_intent') {
                        hasExitIntent = true;
                    }
                });

                if (hasExitIntent && self.checkAllConditions(workflow)) {
                    self.executeWorkflow(workflow);
                }
            });

            // 재발동 방지 (10초 후 리셋)
            setTimeout(function() {
                self.exitIntentFired = false;
            }, 10000);
        },

        /**
         * 시간 기반 트리거 시작
         */
        startTimeBasedTriggers: function() {
            const self = this;

            // 1초마다 시간 체크
            setInterval(function() {
                self.timeOnSite = Math.floor((Date.now() - self.startTime) / 1000);
                self.checkTimeBasedTriggers();
            }, 1000);
        },

        /**
         * 시간 기반 트리거 확인
         */
        checkTimeBasedTriggers: function() {
            const self = this;
            const currentTime = this.timeOnSite;

            this.workflows.forEach(function(workflow) {
                if (self.displayedNudges.includes(workflow.id)) return;
                if (self.timeTriggersFired[workflow.id]) return;

                workflow.nodes.forEach(function(node) {
                    if (node.type !== 'trigger') return;

                    const triggerId = node.data.trigger_id;
                    const settings = node.data.settings || {};

                    // 페이지 체류 시간
                    if (triggerId === 'time_on_site' || triggerId === 'time_on_page') {
                        const targetSeconds = parseInt(settings.seconds || settings.time || 30, 10);

                        if (currentTime >= targetSeconds) {
                            self.timeTriggersFired[workflow.id] = true;

                            if (self.config.debug) {
                                console.log('[NudgeFlow] Time trigger fired:', {
                                    workflow: workflow.id,
                                    target: targetSeconds,
                                    current: currentTime
                                });
                            }

                            if (self.checkAllConditions(workflow)) {
                                self.executeWorkflow(workflow);
                            }
                        }
                    }

                    // 지연 표시
                    if (triggerId === 'delay' || triggerId === 'timed_delay') {
                        const delaySeconds = parseInt(settings.delay || settings.seconds || 5, 10);

                        if (currentTime >= delaySeconds) {
                            self.timeTriggersFired[workflow.id] = true;

                            if (self.checkAllConditions(workflow)) {
                                self.executeWorkflow(workflow);
                            }
                        }
                    }
                });
            });
        },

        /**
         * 유휴 상태 감지 시작
         */
        startIdleDetection: function() {
            const self = this;
            const idleThreshold = 30000; // 30초

            this.idleTimeout = setInterval(function() {
                const idleTime = Date.now() - self.lastActivity;

                if (idleTime >= idleThreshold) {
                    self.handleIdleState();
                }
            }, 5000);
        },

        /**
         * 유휴 상태 처리
         */
        handleIdleState: function() {
            const self = this;

            this.workflows.forEach(function(workflow) {
                if (self.displayedNudges.includes(workflow.id)) return;

                let hasIdleTrigger = false;
                workflow.nodes.forEach(function(node) {
                    if (node.type === 'trigger' && node.data.trigger_id === 'idle_user') {
                        hasIdleTrigger = true;
                    }
                });

                if (hasIdleTrigger && self.checkAllConditions(workflow)) {
                    self.executeWorkflow(workflow);
                }
            });
        },

        /**
         * 클릭 트리거 처리
         */
        handleClickTrigger: function(e) {
            const self = this;
            const $target = $(e.target);

            this.workflows.forEach(function(workflow) {
                if (self.displayedNudges.includes(workflow.id)) return;

                workflow.nodes.forEach(function(node) {
                    if (node.type !== 'trigger') return;

                    const triggerId = node.data.trigger_id;
                    const settings = node.data.settings || {};

                    if (triggerId === 'click_element') {
                        const selector = settings.selector || settings.element;
                        if (selector && $target.is(selector) || $target.closest(selector).length) {
                            if (self.checkAllConditions(workflow)) {
                                self.executeWorkflow(workflow);
                            }
                        }
                    }
                });
            });
        },

        /**
         * 탭 이탈 처리
         */
        handleTabLeave: function() {
            if (this.config.debug) {
                console.log('[NudgeFlow] Tab leave detected');
            }
            this.saveState();
        },

        /**
         * 탭 복귀 처리
         */
        handleTabReturn: function() {
            if (this.config.debug) {
                console.log('[NudgeFlow] Tab return detected');
            }
            this.lastActivity = Date.now();

            // 탭 복귀 트리거 확인
            const self = this;
            this.workflows.forEach(function(workflow) {
                if (self.displayedNudges.includes(workflow.id)) return;

                let hasTabReturnTrigger = false;
                workflow.nodes.forEach(function(node) {
                    if (node.type === 'trigger' && node.data.trigger_id === 'tab_return') {
                        hasTabReturnTrigger = true;
                    }
                });

                if (hasTabReturnTrigger && self.checkAllConditions(workflow)) {
                    self.executeWorkflow(workflow);
                }
            });
        },

        /**
         * 모든 조건 확인
         */
        checkAllConditions: function(workflow) {
            const self = this;
            let allConditionsMet = true;

            workflow.nodes.forEach(function(node) {
                if (node.type === 'condition') {
                    if (!self.evaluateCondition(node.data)) {
                        allConditionsMet = false;
                    }
                }
            });

            return allConditionsMet;
        },

        /**
         * 조건 평가
         */
        evaluateCondition: function(conditionData) {
            const conditionId = conditionData.condition_id;
            const settings = conditionData.settings || {};

            switch (conditionId) {
                case 'device_type':
                    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    return settings.device === 'mobile' ? isMobile : !isMobile;

                case 'page_url':
                    const currentUrl = window.location.href;
                    const pattern = settings.pattern || settings.url;
                    if (settings.match_type === 'contains') {
                        return currentUrl.includes(pattern);
                    } else if (settings.match_type === 'regex') {
                        return new RegExp(pattern).test(currentUrl);
                    }
                    return currentUrl === pattern;

                case 'cookie_exists':
                    const cookieName = settings.cookie_name;
                    return document.cookie.includes(cookieName + '=');

                case 'local_storage':
                    const key = settings.key;
                    const value = localStorage.getItem(key);
                    if (settings.operator === 'exists') {
                        return value !== null;
                    }
                    return value === settings.value;

                case 'referrer':
                    const referrer = document.referrer;
                    const refPattern = settings.pattern || settings.source;
                    return referrer.includes(refPattern);

                case 'time_of_day':
                    const now = new Date();
                    const currentHour = now.getHours();
                    const startHour = parseInt(settings.start_hour || 0, 10);
                    const endHour = parseInt(settings.end_hour || 24, 10);
                    return currentHour >= startHour && currentHour < endHour;

                case 'day_of_week':
                    const today = new Date().getDay();
                    const allowedDays = settings.days || [];
                    return allowedDays.includes(today) || allowedDays.includes(today.toString());

                default:
                    // 서버 측 조건은 이미 평가됨
                    return true;
            }
        },

        /**
         * 워크플로우 평가
         */
        evaluateWorkflows: function() {
            const self = this;

            this.workflows.forEach(function(workflow) {
                // 즉시 실행 워크플로우 (서버에서 트리거 통과)
                if (workflow.immediate && self.shouldShowWorkflow(workflow)) {
                    self.executeWorkflow(workflow);
                }
            });
        },

        /**
         * 워크플로우 표시 여부 확인
         */
        shouldShowWorkflow: function(workflow) {
            // 이미 표시됨
            if (this.displayedNudges.includes(workflow.id)) {
                return false;
            }

            // 닫은 넛지 확인
            if (this.visitorData.dismissed && this.visitorData.dismissed.includes(workflow.id)) {
                return false;
            }

            // 최대 표시 횟수 확인
            const maxNudges = this.config.max_nudges_per_visit || 3;
            if (this.displayedNudges.length >= maxNudges) {
                return false;
            }

            // 지연 간격 확인
            const delay = this.config.nudge_delay || 0;
            if (delay > 0 && this.displayedNudges.length > 0) {
                // 다음 넛지 대기열에 추가
                if (!this.pendingNudges.includes(workflow.id)) {
                    this.pendingNudges.push(workflow.id);
                    setTimeout(() => {
                        const idx = this.pendingNudges.indexOf(workflow.id);
                        if (idx > -1) {
                            this.pendingNudges.splice(idx, 1);
                            if (this.shouldShowWorkflow(workflow)) {
                                this.executeWorkflow(workflow);
                            }
                        }
                    }, delay * 1000);
                }
                return false;
            }

            return true;
        },

        /**
         * 워크플로우 실행
         */
        executeWorkflow: function(workflow) {
            const self = this;

            if (this.config.debug) {
                console.log('[NudgeFlow] Executing workflow:', workflow.id, workflow.title);
            }

            // 액션 노드 찾기 및 실행
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
            settings = settings || {};

            // 지연 처리
            const delay = (settings.delay || 0) * 1000;

            setTimeout(function() {
                switch (actionId) {
                    case 'popup_center':
                    case 'modal':
                        self.showModal(settings, workflowId);
                        break;

                    case 'popup_slide_in':
                    case 'slide_in':
                        self.showSlideIn(settings, workflowId);
                        break;

                    case 'popup_fullscreen':
                    case 'fullscreen':
                        self.showFullscreen(settings, workflowId);
                        break;

                    case 'bar_top':
                        self.showBar('top', settings, workflowId);
                        break;

                    case 'bar_bottom':
                        self.showBar('bottom', settings, workflowId);
                        break;

                    case 'bar_countdown':
                        self.showCountdownBar(settings, workflowId);
                        break;

                    case 'toast':
                    case 'notification':
                        self.showToast(settings, workflowId);
                        break;

                    case 'social_proof':
                        self.showSocialProof(settings, workflowId);
                        break;

                    case 'redirect':
                        self.redirect(settings);
                        break;

                    case 'show_element':
                        self.showElement(settings);
                        break;

                    case 'hide_element':
                        self.hideElement(settings);
                        break;

                    case 'add_class':
                        self.addClass(settings);
                        break;

                    case 'trigger_event':
                        self.triggerEvent(settings);
                        break;

                    case 'set_cookie':
                        self.setCookie(settings);
                        break;

                    case 'set_storage':
                        self.setStorage(settings);
                        break;

                    default:
                        if (self.config.debug) {
                            console.warn('[NudgeFlow] Unknown action:', actionId);
                        }
                }

                // 노출 추적
                self.trackEvent('impression', {
                    action_id: actionId,
                    workflow_id: workflowId
                });
            }, delay);
        },

        /**
         * 모달 표시
         */
        showModal: function(settings, workflowId) {
            const template = this.getTemplate('modal', settings);
            let $modal = $('.acf-nudge-modal');
            const $overlay = $('.acf-nudge-modal-overlay');

            // 모달이 없으면 생성
            if (!$modal.length) {
                $('body').append('<div class="acf-nudge-modal-overlay"></div><div class="acf-nudge-modal" data-nudge-type="modal"><div class="acf-nudge-modal-content"></div></div>');
                $modal = $('.acf-nudge-modal');
            }

            $modal.find('.acf-nudge-modal-content').html(template);
            $modal.attr('data-workflow-id', workflowId);

            // 스타일 적용
            if (settings.style) {
                $modal.addClass('style-' + settings.style);
            }

            // 애니메이션
            setTimeout(function() {
                $('.acf-nudge-modal-overlay').addClass('active');
                $modal.addClass('active');
            }, 50);

            this.displayedNudges.push(workflowId);
        },

        /**
         * 슬라이드인 표시
         */
        showSlideIn: function(settings, workflowId) {
            const template = this.getTemplate('slide-in', settings);
            let $slideIn = $('.acf-nudge-slide-in');

            if (!$slideIn.length) {
                $('body').append('<div class="acf-nudge-slide-in" data-nudge-type="slide-in"><div class="acf-nudge-slide-in-content"></div></div>');
                $slideIn = $('.acf-nudge-slide-in');
            }

            $slideIn.find('.acf-nudge-slide-in-content').html(template);
            $slideIn.attr('data-workflow-id', workflowId);

            // 위치 클래스
            $slideIn.removeClass('position-top-left position-top-right position-bottom-left position-bottom-right');
            if (settings.position) {
                $slideIn.addClass('position-' + settings.position);
            } else {
                $slideIn.addClass('position-bottom-right');
            }

            setTimeout(function() {
                $slideIn.addClass('active');
            }, 50);

            this.displayedNudges.push(workflowId);
        },

        /**
         * 전체 화면 표시
         */
        showFullscreen: function(settings, workflowId) {
            const template = this.getTemplate('fullscreen', settings);
            let $fullscreen = $('.acf-nudge-fullscreen');

            if (!$fullscreen.length) {
                $('body').append('<div class="acf-nudge-fullscreen" data-nudge-type="fullscreen"><div class="acf-nudge-fullscreen-content"></div></div>');
                $fullscreen = $('.acf-nudge-fullscreen');
            }

            $fullscreen.find('.acf-nudge-fullscreen-content').html(template);
            $fullscreen.attr('data-workflow-id', workflowId);

            // 스크롤 방지
            $('body').addClass('nudge-no-scroll');

            setTimeout(function() {
                $fullscreen.addClass('active');
            }, 50);

            this.displayedNudges.push(workflowId);
        },

        /**
         * 바 표시
         */
        showBar: function(position, settings, workflowId) {
            const template = this.getTemplate('bar', settings);
            let $bar = $('.acf-nudge-bar-' + position);

            if (!$bar.length) {
                $('body').append('<div class="acf-nudge-bar acf-nudge-bar-' + position + '" data-nudge-type="bar"><div class="acf-nudge-bar-content"></div></div>');
                $bar = $('.acf-nudge-bar-' + position);
            }

            $bar.find('.acf-nudge-bar-content').html(template);
            $bar.attr('data-workflow-id', workflowId);

            // 스타일 적용
            if (settings.bg_color) {
                $bar.css('background', settings.bg_color);
            }
            if (settings.text_color) {
                $bar.css('color', settings.text_color);
            }
            if (settings.gradient) {
                $bar.css('background', 'linear-gradient(90deg, ' + settings.gradient_start + ', ' + settings.gradient_end + ')');
            }

            setTimeout(function() {
                $bar.addClass('active');
            }, 50);

            this.displayedNudges.push(workflowId);
        },

        /**
         * 카운트다운 바 표시
         */
        showCountdownBar: function(settings, workflowId) {
            const self = this;
            const endTime = settings.end_time ? new Date(settings.end_time).getTime() : Date.now() + (settings.duration || 3600) * 1000;

            // 바 표시
            this.showBar(settings.position || 'top', settings, workflowId);

            // 카운트다운 업데이트
            const $countdown = $('.acf-nudge-bar-' + (settings.position || 'top') + ' .countdown-timer');

            const updateCountdown = function() {
                const now = Date.now();
                const remaining = endTime - now;

                if (remaining <= 0) {
                    $countdown.text('종료됨');
                    return;
                }

                const hours = Math.floor(remaining / 3600000);
                const minutes = Math.floor((remaining % 3600000) / 60000);
                const seconds = Math.floor((remaining % 60000) / 1000);

                $countdown.text(
                    String(hours).padStart(2, '0') + ':' +
                    String(minutes).padStart(2, '0') + ':' +
                    String(seconds).padStart(2, '0')
                );

                setTimeout(updateCountdown, 1000);
            };

            updateCountdown();
        },

        /**
         * 토스트 표시
         */
        showToast: function(settings, workflowId) {
            const template = this.getTemplate('toast', settings);
            let $container = $('.acf-nudge-toast-container');

            if (!$container.length) {
                $('body').append('<div class="acf-nudge-toast-container"></div>');
                $container = $('.acf-nudge-toast-container');
            }

            // 위치 설정
            $container.removeClass('position-top-left position-top-right position-bottom-left position-bottom-right');
            $container.addClass('position-' + (settings.position || 'bottom-right'));

            const $toast = $(template);
            $toast.attr('data-workflow-id', workflowId);
            $toast.attr('data-nudge-type', 'toast');

            // 스타일 적용
            if (settings.style) {
                $toast.addClass('style-' + settings.style);
            }

            $container.append($toast);

            // 애니메이션
            setTimeout(function() {
                $toast.addClass('active');
            }, 50);

            // 자동 닫기
            const duration = (settings.duration || 5) * 1000;
            setTimeout(function() {
                $toast.removeClass('active');
                setTimeout(function() {
                    $toast.remove();
                }, 300);
            }, duration);

            this.displayedNudges.push(workflowId);
        },

        /**
         * 소셜 프루프 표시
         */
        showSocialProof: function(settings, workflowId) {
            const messages = settings.messages || [
                '방금 서울에서 구매가 완료되었습니다.',
                '5분 전 대구에서 주문이 접수되었습니다.',
                '현재 12명이 이 상품을 보고 있습니다.'
            ];

            const randomMessage = messages[Math.floor(Math.random() * messages.length)];

            this.showToast({
                title: settings.title || '알림',
                message: randomMessage,
                icon: settings.icon || '🔔',
                position: settings.position || 'bottom-left',
                duration: settings.duration || 5,
                style: 'social-proof'
            }, workflowId);
        },

        /**
         * 리다이렉트
         */
        redirect: function(settings) {
            if (!settings.url) return;

            if (settings.new_tab) {
                window.open(settings.url, '_blank');
            } else {
                window.location.href = settings.url;
            }
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
                    $element.css({ transform: 'scale(0)', display: 'block' }).animate({ opacity: 1 }, {
                        duration: 300,
                        step: function(now) {
                            $(this).css('transform', 'scale(' + now + ')');
                        }
                    });
                    break;
                default:
                    $element.show();
            }
        },

        /**
         * 요소 숨기기
         */
        hideElement: function(settings) {
            if (!settings.selector) return;
            $(settings.selector).hide();
        },

        /**
         * 클래스 추가
         */
        addClass: function(settings) {
            if (!settings.selector || !settings.class_name) return;
            $(settings.selector).addClass(settings.class_name);
        },

        /**
         * 이벤트 트리거
         */
        triggerEvent: function(settings) {
            if (!settings.event_name) return;
            $(document).trigger(settings.event_name, settings.data || {});
        },

        /**
         * 쿠키 설정
         */
        setCookie: function(settings) {
            if (!settings.name) return;
            const expires = settings.days ? '; expires=' + new Date(Date.now() + settings.days * 864e5).toUTCString() : '';
            document.cookie = settings.name + '=' + encodeURIComponent(settings.value || '') + expires + '; path=/';
        },

        /**
         * 로컬 스토리지 설정
         */
        setStorage: function(settings) {
            if (!settings.key) return;
            localStorage.setItem(settings.key, settings.value || '');
        },

        /**
         * 넛지 숨기기
         */
        hideNudge: function($container) {
            const type = $container.data('nudge-type');
            const workflowId = $container.data('workflow-id');

            $container.removeClass('active');

            // 전체 화면이면 스크롤 복원
            if (type === 'fullscreen') {
                $('body').removeClass('nudge-no-scroll');
            }

            // 닫기 상태 저장
            this.dismissNudge(workflowId);

            // 추적
            this.trackEvent('dismiss', {
                nudge_type: type,
                workflow_id: workflowId
            });
        },

        /**
         * 넛지 닫기 저장
         */
        dismissNudge: function(nudgeId) {
            if (!nudgeId) return;

            // 로컬에 저장
            let dismissed = JSON.parse(localStorage.getItem('acf_nudge_dismissed') || '[]');
            if (!dismissed.includes(nudgeId)) {
                dismissed.push(nudgeId);
                localStorage.setItem('acf_nudge_dismissed', JSON.stringify(dismissed));
            }

            // 서버에 저장
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

            // 기본 템플릿
            if (!$template.length) {
                return this.getDefaultTemplate(type, data);
            }

            let html = $template.html();

            // Mustache 스타일 치환
            const self = this;
            Object.keys(data).forEach(function(key) {
                const tripleRegex = new RegExp('\\{\\{\\{' + key + '\\}\\}\\}', 'g');
                const regex = new RegExp('\\{\\{' + key + '\\}\\}', 'g');

                html = html.replace(tripleRegex, data[key] || '');
                html = html.replace(regex, self.escapeHtml(data[key] || ''));
            });

            // 조건부 블록
            html = html.replace(/\{\{#(\w+)\}\}([\s\S]*?)\{\{\/\1\}\}/g, function(match, key, content) {
                return data[key] ? content : '';
            });

            // 부정 조건
            html = html.replace(/\{\{\^(\w+)\}\}([\s\S]*?)\{\{\/\1\}\}/g, function(match, key, content) {
                return !data[key] ? content : '';
            });

            return html;
        },

        /**
         * 기본 템플릿
         */
        getDefaultTemplate: function(type, data) {
            const templates = {
                modal: `
                    <div class="nudge-modal-inner">
                        <button class="acf-nudge-close">&times;</button>
                        ${data.image ? '<img src="' + data.image + '" alt="" class="nudge-image">' : ''}
                        <h2 class="nudge-title">${this.escapeHtml(data.title || '')}</h2>
                        <p class="nudge-message">${this.escapeHtml(data.message || '')}</p>
                        ${data.cta_text ? '<a href="' + (data.cta_url || '#') + '" class="acf-nudge-cta-button">' + this.escapeHtml(data.cta_text) + '</a>' : ''}
                        ${data.dismiss_text ? '<a href="#" class="acf-nudge-dismiss-link">' + this.escapeHtml(data.dismiss_text) + '</a>' : ''}
                    </div>
                `,
                'slide-in': `
                    <div class="nudge-slide-inner">
                        <button class="acf-nudge-close">&times;</button>
                        ${data.icon ? '<span class="nudge-icon">' + data.icon + '</span>' : ''}
                        <h3 class="nudge-title">${this.escapeHtml(data.title || '')}</h3>
                        <p class="nudge-message">${this.escapeHtml(data.message || '')}</p>
                        ${data.cta_text ? '<a href="' + (data.cta_url || '#') + '" class="acf-nudge-cta-button">' + this.escapeHtml(data.cta_text) + '</a>' : ''}
                    </div>
                `,
                bar: `
                    <div class="nudge-bar-inner">
                        <span class="nudge-bar-text">${this.escapeHtml(data.message || data.text || '')}</span>
                        ${data.cta_text ? '<a href="' + (data.cta_url || '#') + '" class="acf-nudge-bar-cta">' + this.escapeHtml(data.cta_text) + '</a>' : ''}
                        <button class="acf-nudge-close">&times;</button>
                    </div>
                `,
                toast: `
                    <div class="nudge-toast-inner">
                        ${data.icon ? '<span class="nudge-icon">' + data.icon + '</span>' : ''}
                        <div class="nudge-toast-content">
                            ${data.title ? '<strong class="nudge-title">' + this.escapeHtml(data.title) + '</strong>' : ''}
                            <p class="nudge-message">${this.escapeHtml(data.message || '')}</p>
                        </div>
                        <button class="acf-nudge-close">&times;</button>
                    </div>
                `,
                fullscreen: `
                    <div class="nudge-fullscreen-inner">
                        <button class="acf-nudge-close">&times;</button>
                        ${data.image ? '<img src="' + data.image + '" alt="" class="nudge-image">' : ''}
                        <h1 class="nudge-title">${this.escapeHtml(data.title || '')}</h1>
                        <p class="nudge-message">${this.escapeHtml(data.message || '')}</p>
                        ${data.cta_text ? '<a href="' + (data.cta_url || '#') + '" class="acf-nudge-cta-button">' + this.escapeHtml(data.cta_text) + '</a>' : ''}
                        ${data.dismiss_text ? '<a href="#" class="acf-nudge-dismiss-link">' + this.escapeHtml(data.dismiss_text) + '</a>' : ''}
                    </div>
                `
            };

            return templates[type] || '';
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
         * 상태 저장
         */
        saveState: function() {
            const state = {
                displayedNudges: this.displayedNudges,
                maxScrollDepth: this.maxScrollDepth,
                timeOnSite: this.timeOnSite,
                analytics: this.analytics,
                timestamp: Date.now()
            };
            sessionStorage.setItem('acf_nudge_state', JSON.stringify(state));
        },

        /**
         * 상태 복원
         */
        restoreState: function() {
            try {
                const state = JSON.parse(sessionStorage.getItem('acf_nudge_state') || '{}');

                // 5분 이내의 상태만 복원
                if (state.timestamp && Date.now() - state.timestamp < 300000) {
                    this.displayedNudges = state.displayedNudges || [];
                    this.maxScrollDepth = state.maxScrollDepth || 0;
                }

                // 닫은 넛지 복원
                const dismissed = JSON.parse(localStorage.getItem('acf_nudge_dismissed') || '[]');
                this.visitorData.dismissed = dismissed;
            } catch (e) {
                // 오류 무시
            }
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
         * 전환 기록 (MAB)
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
                        console.log('[NudgeFlow] MAB: 전환 기록됨', response.data);
                    }
                }
            });
        },

        /**
         * 분석 데이터 전송
         */
        sendAnalytics: function() {
            if (this.analytics.interactions === 0) return;

            $.ajax({
                url: this.config.ajaxUrl,
                method: 'POST',
                async: false, // beforeunload에서 동기 필요
                data: {
                    action: 'acf_nudge_analytics',
                    nonce: this.config.nonce,
                    data: {
                        page_views: this.analytics.pageViews,
                        interactions: this.analytics.interactions,
                        conversions: this.analytics.conversions,
                        time_on_site: this.timeOnSite,
                        max_scroll_depth: this.maxScrollDepth,
                        displayed_nudges: this.displayedNudges.length
                    }
                }
            });
        }
    };

    // DOM Ready
    $(document).ready(function() {
        NudgeFlow.init();
    });

    // 전역 접근
    window.ACFNudgeFlow = NudgeFlow;

})(jQuery);
