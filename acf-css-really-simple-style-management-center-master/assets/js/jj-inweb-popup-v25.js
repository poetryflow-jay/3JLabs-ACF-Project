/**
 * JJ In-Web Popup System v25.0.0
 * 고도화된 인웹 팝업 시스템
 * 
 * @package ACF_CSS_Style_Guide
 * @version 25.0.0
 * @author 3J Labs
 * 
 * 주요 기능:
 * - 위치 기반 표시
 * - 스마트 타이밍
 * - 개인화된 메시지
 * - A/B 테스트 지원
 * - 스크롤 기반 트리거
 * - 시간 기반 트리거
 */

(function($) {
    'use strict';

    /**
     * In-Web Popup Manager
     */
    const InWebPopup = {
        popups: [],
        activePopups: [],
        settings: {
            maxConcurrent: 3,
            minTimeBetween: 5000,
            scrollThreshold: 0.5,
            timeThreshold: 30000
        },

        /**
         * 초기화
         */
        init: function() {
            this.loadSettings();
            this.initTriggers();
            this.bindEvents();
        },

        /**
         * 설정 로드
         */
        loadSettings: function() {
            const saved = localStorage.getItem('jj_inweb_popup_settings');
            if (saved) {
                this.settings = Object.assign(this.settings, JSON.parse(saved));
            }
        },

        /**
         * 트리거 초기화
         */
        initTriggers: function() {
            // 스크롤 트리거
            let scrollTriggered = false;
            $(window).on('scroll', () => {
                if (scrollTriggered) return;

                const scrollPercent = $(window).scrollTop() / ($(document).height() - $(window).height());
                if (scrollPercent >= this.settings.scrollThreshold) {
                    scrollTriggered = true;
                    this.triggerPopup('scroll', { percent: scrollPercent });
                }
            });

            // 시간 기반 트리거
            setTimeout(() => {
                this.triggerPopup('time', { elapsed: this.settings.timeThreshold });
            }, this.settings.timeThreshold);

            // 요소 가시성 트리거
            this.initElementVisibilityTriggers();
        },

        /**
         * 요소 가시성 트리거
         */
        initElementVisibilityTriggers: function() {
            if (typeof IntersectionObserver === 'undefined') {
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const trigger = entry.target.dataset.popupTrigger;
                        if (trigger) {
                            this.triggerPopup('element', { element: entry.target, trigger: trigger });
                            observer.unobserve(entry.target);
                        }
                    }
                });
            }, {
                threshold: 0.5
            });

            $('[data-popup-trigger]').each(function() {
                observer.observe(this);
            });
        },

        /**
         * 팝업 표시
         */
        show: function(config) {
            // 최대 동시 표시 개수 확인
            if (this.activePopups.length >= this.settings.maxConcurrent) {
                return;
            }

            // 최소 시간 간격 확인
            const lastPopupTime = localStorage.getItem('jj_last_popup_time');
            if (lastPopupTime && (Date.now() - parseInt(lastPopupTime)) < this.settings.minTimeBetween) {
                return;
            }

            const popup = this.createPopup(config);
            this.positionPopup(popup, config.position || 'center');
            this.animateIn(popup);
            
            this.activePopups.push(popup);
            localStorage.setItem('jj_last_popup_time', Date.now().toString());

            // 자동 닫기
            if (config.duration) {
                setTimeout(() => {
                    this.hide(popup);
                }, config.duration);
            }
        },

        /**
         * 팝업 생성
         */
        createPopup: function(config) {
            const id = 'jj-inweb-popup-' + Date.now();
            const type = config.type || 'info';
            const typeClass = 'jj-inweb-popup-' + type;

            const $popup = $('<div class="jj-inweb-popup ' + typeClass + '" id="' + id + '">' +
                '<div class="jj-inweb-popup-header">' +
                (config.icon ? '<span class="jj-inweb-popup-icon">' + config.icon + '</span>' : '') +
                '<h3 class="jj-inweb-popup-title">' + this.escapeHtml(config.title || '') + '</h3>' +
                (config.dismissible !== false ? '<button class="jj-inweb-popup-close" aria-label="닫기">×</button>' : '') +
                '</div>' +
                '<div class="jj-inweb-popup-body">' +
                '<p class="jj-inweb-popup-message">' + this.escapeHtml(config.message || '') + '</p>' +
                '</div>' +
                (config.actions ? '<div class="jj-inweb-popup-actions">' + this.renderActions(config.actions) + '</div>' : '') +
                '</div>');

            $('body').append($popup);
            return $popup[0];
        },

        /**
         * 액션 렌더링
         */
        renderActions: function(actions) {
            let html = '';
            actions.forEach(action => {
                const btnClass = action.primary ? 'jj-btn jj-btn-primary' : 'jj-btn jj-btn-secondary';
                html += '<button class="' + btnClass + '" data-action="' + this.escapeHtml(action.action || '') + '">' +
                    this.escapeHtml(action.label || '') +
                    '</button>';
            });
            return html;
        },

        /**
         * 팝업 위치 설정
         */
        positionPopup: function(popup, position) {
            const $popup = $(popup);
            const positions = {
                'top-left': { top: '20px', left: '20px', right: 'auto', bottom: 'auto' },
                'top-center': { top: '20px', left: '50%', right: 'auto', bottom: 'auto', transform: 'translateX(-50%)' },
                'top-right': { top: '20px', right: '20px', left: 'auto', bottom: 'auto' },
                'center': { top: '50%', left: '50%', right: 'auto', bottom: 'auto', transform: 'translate(-50%, -50%)' },
                'bottom-left': { bottom: '20px', left: '20px', right: 'auto', top: 'auto' },
                'bottom-center': { bottom: '20px', left: '50%', right: 'auto', top: 'auto', transform: 'translateX(-50%)' },
                'bottom-right': { bottom: '20px', right: '20px', left: 'auto', top: 'auto' }
            };

            const pos = positions[position] || positions.center;
            $popup.css(pos);
        },

        /**
         * 애니메이션 인
         */
        animateIn: function(popup) {
            const $popup = $(popup);
            $popup.addClass('jj-animate-scale-in');
            
            // 스포트라이트 효과
            if ($popup.data('spotlight')) {
                this.addSpotlightEffect($popup);
            }
        },

        /**
         * 스포트라이트 효과 추가
         */
        addSpotlightEffect: function($popup) {
            const $spotlight = $('<div class="jj-spotlight"></div>');
            $('body').append($spotlight);
            
            const rect = $popup[0].getBoundingClientRect();
            $spotlight.css({
                top: rect.top + 'px',
                left: rect.left + 'px',
                width: rect.width + 'px',
                height: rect.height + 'px'
            });

            setTimeout(() => {
                $spotlight.fadeOut(500, function() {
                    $(this).remove();
                });
            }, 2000);
        },

        /**
         * 팝업 숨기기
         */
        hide: function(popup) {
            const $popup = $(popup);
            $popup.addClass('jj-toast-exit');
            
            setTimeout(() => {
                $popup.remove();
                const index = this.activePopups.indexOf(popup);
                if (index > -1) {
                    this.activePopups.splice(index, 1);
                }
            }, 300);
        },

        /**
         * 트리거 팝업
         */
        triggerPopup: function(triggerType, data) {
            // 트리거된 팝업 목록 확인
            const triggered = localStorage.getItem('jj_triggered_popups') || '[]';
            const triggeredList = JSON.parse(triggered);

            // 이미 트리거된 팝업인지 확인
            const triggerKey = triggerType + '_' + (data.percent || data.elapsed || 'default');
            if (triggeredList.includes(triggerKey)) {
                return;
            }

            // 팝업 표시
            const config = this.getPopupConfig(triggerType, data);
            if (config) {
                this.show(config);
                triggeredList.push(triggerKey);
                localStorage.setItem('jj_triggered_popups', JSON.stringify(triggeredList));
            }
        },

        /**
         * 팝업 설정 가져오기
         */
        getPopupConfig: function(triggerType, data) {
            // 실제로는 서버에서 가져오거나 설정에서 로드
            const configs = {
                'scroll': {
                    type: 'info',
                    icon: '📜',
                    title: '스크롤 진행 중',
                    message: '페이지의 ' + Math.round(data.percent * 100) + '%를 읽으셨습니다. 계속 읽어보세요!',
                    position: 'bottom-right',
                    duration: 5000,
                    dismissible: true
                },
                'time': {
                    type: 'tip',
                    icon: '💡',
                    title: '팁',
                    message: '이 페이지에 ' + Math.round(data.elapsed / 1000) + '초 이상 머물고 계시네요. 도움이 필요하신가요?',
                    position: 'top-right',
                    duration: 6000,
                    dismissible: true,
                    actions: [
                        { label: '도움말 보기', action: 'help', primary: true },
                        { label: '나중에', action: 'dismiss' }
                    ]
                },
                'element': {
                    type: 'info',
                    icon: '👆',
                    title: '이 섹션을 확인하세요',
                    message: '이 부분에 중요한 정보가 있습니다.',
                    position: 'center',
                    duration: 4000,
                    dismissible: true
                }
            };

            return configs[triggerType];
        },

        /**
         * 이벤트 바인딩
         */
        bindEvents: function() {
            const self = this;

            // 닫기 버튼
            $(document).on('click', '.jj-inweb-popup-close', function() {
                const $popup = $(this).closest('.jj-inweb-popup');
                self.hide($popup[0]);
            });

            // 액션 버튼
            $(document).on('click', '.jj-inweb-popup-actions button', function() {
                const action = $(this).data('action');
                const $popup = $(this).closest('.jj-inweb-popup');
                
                if (action === 'dismiss') {
                    self.hide($popup[0]);
                } else if (action === 'help') {
                    // 도움말 페이지로 이동
                    window.location.href = '#help';
                    self.hide($popup[0]);
                }
            });

            // ESC 키로 닫기
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('.jj-inweb-popup').each(function() {
                        self.hide(this);
                    });
                }
            });
        },

        /**
         * HTML 이스케이프
         */
        escapeHtml: function(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // DOM 준비 시 초기화
    $(document).ready(function() {
        InWebPopup.init();
    });

    // 전역으로 노출
    window.JJInWebPopup = InWebPopup;

})(jQuery);
