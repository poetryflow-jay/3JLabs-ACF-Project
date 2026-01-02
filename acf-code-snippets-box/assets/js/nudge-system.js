/**
 * ACF Code Snippets Box - Nudge System
 *
 * 넛지 마케팅 시스템 UI
 *
 * @package ACF_Code_Snippets_Box
 */

(function($) {
    'use strict';

    /**
     * 넛지 시스템 클래스
     */
    class ACFCSBNudgeSystem {
        constructor() {
            this.$container = $('#acf-csb-nudge-container');
            this.nudges = window.acfCsbNudge?.nudges || {};
            this.i18n = window.acfCsbNudge?.i18n || {};
            this.activeNudges = [];
            this.walkthroughStep = 0;
            
            this.init();
        }

        init() {
            this.processNudges();
            this.bindGlobalEvents();
        }

        /**
         * 넛지 처리
         */
        processNudges() {
            const self = this;
            const sortedNudges = Object.entries(this.nudges)
                .sort((a, b) => (b[1].priority || 0) - (a[1].priority || 0));

            sortedNudges.forEach(([id, nudge]) => {
                // 타입별 표시
                switch (nudge.type) {
                    case 'toast':
                        self.showToast(id, nudge);
                        break;
                    case 'banner':
                        self.showBanner(id, nudge);
                        break;
                    case 'modal':
                        self.showModal(id, nudge);
                        break;
                    case 'tooltip':
                        self.showTooltip(id, nudge);
                        break;
                    case 'spotlight':
                        self.showSpotlight(id, nudge);
                        break;
                    case 'inline':
                        self.showInline(id, nudge);
                        break;
                    case 'walkthrough':
                        self.startWalkthrough(id, nudge);
                        break;
                }
            });
        }

        /**
         * 글로벌 이벤트 바인딩
         */
        bindGlobalEvents() {
            const self = this;

            // 닫기 버튼
            $(document).on('click', '.acf-csb-nudge-close, .acf-csb-nudge-dismiss', function(e) {
                e.preventDefault();
                const nudgeId = $(this).closest('[data-nudge-id]').data('nudge-id');
                self.dismissNudge(nudgeId);
            });

            // CTA 클릭 트래킹
            $(document).on('click', '.acf-csb-nudge-cta', function(e) {
                const nudgeId = $(this).closest('[data-nudge-id]').data('nudge-id');
                self.trackNudge(nudgeId, 'click');
            });

            // 모달 오버레이 클릭
            $(document).on('click', '.acf-csb-nudge-modal-overlay', function(e) {
                if ($(e.target).hasClass('acf-csb-nudge-modal-overlay')) {
                    const nudgeId = $(this).find('[data-nudge-id]').data('nudge-id');
                    const nudge = self.nudges[nudgeId];
                    if (nudge && nudge.dismissible !== false) {
                        self.dismissNudge(nudgeId);
                    }
                }
            });

            // ESC 키로 모달 닫기
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('.acf-csb-nudge-modal-overlay').each(function() {
                        const nudgeId = $(this).find('[data-nudge-id]').data('nudge-id');
                        const nudge = self.nudges[nudgeId];
                        if (nudge && nudge.dismissible !== false) {
                            self.dismissNudge(nudgeId);
                        }
                    });
                }
            });
        }

        /**
         * 토스트 표시
         */
        showToast(id, nudge) {
            const self = this;
            const categoryClass = nudge.category || 'feature';
            
            const html = `
                <div class="acf-csb-nudge-toast ${categoryClass}" data-nudge-id="${id}">
                    <span class="acf-csb-nudge-toast-icon">${this.getCategoryIcon(nudge.category)}</span>
                    <div class="acf-csb-nudge-toast-content">
                        <div class="acf-csb-nudge-toast-title">${this.escapeHtml(nudge.title)}</div>
                        <div class="acf-csb-nudge-toast-message">${this.escapeHtml(nudge.message)}</div>
                    </div>
                    <button class="acf-csb-nudge-toast-close acf-csb-nudge-close">×</button>
                </div>
            `;

            this.$container.append(html);
            this.trackNudge(id, 'view');

            // 자동 숨김 (10초)
            setTimeout(() => {
                self.dismissNudge(id, false);
            }, 10000);
        }

        /**
         * 배너 표시
         */
        showBanner(id, nudge) {
            const categoryClass = nudge.category || 'feature';
            
            const html = `
                <div class="acf-csb-nudge-banner ${categoryClass}" data-nudge-id="${id}">
                    <div class="acf-csb-nudge-banner-content">
                        <div class="acf-csb-nudge-banner-title">${this.escapeHtml(nudge.title)}</div>
                        <div class="acf-csb-nudge-banner-message">${this.escapeHtml(nudge.message)}</div>
                    </div>
                    ${nudge.cta_url ? `
                        <a href="${this.escapeHtml(nudge.cta_url)}" class="acf-csb-nudge-banner-cta acf-csb-nudge-cta" target="_blank">
                            ${this.escapeHtml(nudge.cta_text || this.i18n.learnMore)}
                        </a>
                    ` : ''}
                    <button class="acf-csb-nudge-banner-close acf-csb-nudge-close">×</button>
                </div>
            `;

            // 페이지 상단에 삽입
            $('.wrap').first().prepend(html);
            this.trackNudge(id, 'view');
        }

        /**
         * 모달 표시
         */
        showModal(id, nudge) {
            const html = `
                <div class="acf-csb-nudge-modal-overlay">
                    <div class="acf-csb-nudge-modal" data-nudge-id="${id}">
                        <div class="acf-csb-nudge-modal-header">
                            <div class="acf-csb-nudge-modal-icon">${this.getCategoryIcon(nudge.category)}</div>
                            <div class="acf-csb-nudge-modal-title">${this.escapeHtml(nudge.title)}</div>
                        </div>
                        <div class="acf-csb-nudge-modal-body">
                            ${this.escapeHtml(nudge.message)}
                        </div>
                        <div class="acf-csb-nudge-modal-footer">
                            ${nudge.dismissible !== false ? `
                                <button class="acf-csb-nudge-modal-btn acf-csb-nudge-modal-btn-secondary acf-csb-nudge-close">
                                    ${this.i18n.dismiss}
                                </button>
                            ` : ''}
                            ${nudge.cta_url ? `
                                <a href="${this.escapeHtml(nudge.cta_url)}" 
                                   class="acf-csb-nudge-modal-btn acf-csb-nudge-modal-btn-primary acf-csb-nudge-cta" 
                                   target="_blank">
                                    ${this.escapeHtml(nudge.cta_text || this.i18n.upgrade)}
                                </a>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;

            $('body').append(html);
            this.trackNudge(id, 'view');
        }

        /**
         * 툴팁 표시
         */
        showTooltip(id, nudge) {
            const $target = $(nudge.target);
            if ($target.length === 0) return;

            const position = nudge.position || 'bottom';
            
            const html = `
                <div class="acf-csb-nudge-tooltip ${position}" data-nudge-id="${id}">
                    <div class="acf-csb-nudge-tooltip-title">${this.escapeHtml(nudge.title)}</div>
                    <div class="acf-csb-nudge-tooltip-message">${this.escapeHtml(nudge.message)}</div>
                    <button class="acf-csb-nudge-tooltip-close acf-csb-nudge-close">×</button>
                </div>
            `;

            const $tooltip = $(html);
            $('body').append($tooltip);

            // 위치 계산
            this.positionTooltip($tooltip, $target, position);
            this.trackNudge(id, 'view');
        }

        /**
         * 툴팁 위치 지정
         */
        positionTooltip($tooltip, $target, position) {
            const targetOffset = $target.offset();
            const targetWidth = $target.outerWidth();
            const targetHeight = $target.outerHeight();
            const tooltipWidth = $tooltip.outerWidth();
            const tooltipHeight = $tooltip.outerHeight();

            let top, left;

            switch (position) {
                case 'top':
                    top = targetOffset.top - tooltipHeight - 15;
                    left = targetOffset.left + (targetWidth / 2) - (tooltipWidth / 2);
                    break;
                case 'bottom':
                    top = targetOffset.top + targetHeight + 15;
                    left = targetOffset.left + (targetWidth / 2) - (tooltipWidth / 2);
                    break;
                case 'left':
                    top = targetOffset.top + (targetHeight / 2) - (tooltipHeight / 2);
                    left = targetOffset.left - tooltipWidth - 15;
                    break;
                case 'right':
                    top = targetOffset.top + (targetHeight / 2) - (tooltipHeight / 2);
                    left = targetOffset.left + targetWidth + 15;
                    break;
            }

            $tooltip.css({ top: top, left: left });
        }

        /**
         * 스포트라이트 표시
         */
        showSpotlight(id, nudge) {
            const $target = $(nudge.target);
            if ($target.length === 0) return;

            // 오버레이 추가
            $('body').append('<div class="acf-csb-nudge-spotlight-overlay"></div>');
            
            // 타겟 강조
            $target.addClass('acf-csb-nudge-spotlight-target');

            // 툴팁도 함께 표시
            this.showTooltip(id, nudge);
        }

        /**
         * 인라인 메시지 표시
         */
        showInline(id, nudge) {
            const $target = nudge.target ? $(nudge.target) : null;
            const categoryClass = nudge.category || 'feature';
            
            const html = `
                <div class="acf-csb-nudge-inline ${categoryClass}" data-nudge-id="${id}">
                    <span class="acf-csb-nudge-inline-icon">${this.getCategoryIcon(nudge.category)}</span>
                    <div class="acf-csb-nudge-inline-content">
                        <div class="acf-csb-nudge-inline-title">${this.escapeHtml(nudge.title)}</div>
                        <div class="acf-csb-nudge-inline-message">${this.escapeHtml(nudge.message)}</div>
                        ${nudge.cta_url ? `
                            <div class="acf-csb-nudge-inline-cta">
                                <a href="${this.escapeHtml(nudge.cta_url)}" class="acf-csb-nudge-cta" target="_blank">
                                    ${this.escapeHtml(nudge.cta_text || this.i18n.learnMore)} →
                                </a>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;

            if ($target && $target.length) {
                $target.after(html);
            }
            
            this.trackNudge(id, 'view');
        }

        /**
         * 워크스루 시작
         */
        startWalkthrough(id, nudge) {
            this.currentWalkthrough = { id, nudge };
            this.walkthroughStep = 0;
            this.showWalkthroughStep();
        }

        /**
         * 워크스루 스텝 표시
         */
        showWalkthroughStep() {
            const { id, nudge } = this.currentWalkthrough;
            const steps = nudge.steps || [];
            
            if (this.walkthroughStep >= steps.length) {
                this.dismissNudge(id);
                return;
            }

            const step = steps[this.walkthroughStep];
            const totalSteps = steps.length;

            // 기존 워크스루 제거
            $('.acf-csb-nudge-walkthrough').remove();

            // 진행 표시 점
            let progressDots = '';
            for (let i = 0; i < totalSteps; i++) {
                const dotClass = i < this.walkthroughStep ? 'completed' : (i === this.walkthroughStep ? 'active' : '');
                progressDots += `<span class="acf-csb-nudge-walkthrough-progress-dot ${dotClass}"></span>`;
            }

            const html = `
                <div class="acf-csb-nudge-walkthrough" data-nudge-id="${id}">
                    <div class="acf-csb-nudge-walkthrough-header">
                        <div class="acf-csb-nudge-walkthrough-progress">
                            ${progressDots}
                        </div>
                        <div class="acf-csb-nudge-walkthrough-title">${this.escapeHtml(step.title)}</div>
                    </div>
                    <div class="acf-csb-nudge-walkthrough-body">
                        <div class="acf-csb-nudge-walkthrough-message">${this.escapeHtml(step.message)}</div>
                    </div>
                    <div class="acf-csb-nudge-walkthrough-footer">
                        <button class="acf-csb-nudge-walkthrough-skip acf-csb-nudge-dismiss">${this.i18n.skip}</button>
                        <div class="acf-csb-nudge-walkthrough-nav">
                            ${this.walkthroughStep > 0 ? `
                                <button class="acf-csb-nudge-walkthrough-btn acf-csb-nudge-walkthrough-btn-prev">
                                    ${this.i18n.prev}
                                </button>
                            ` : ''}
                            <button class="acf-csb-nudge-walkthrough-btn acf-csb-nudge-walkthrough-btn-next">
                                ${this.walkthroughStep < totalSteps - 1 ? this.i18n.next : this.i18n.gotIt}
                            </button>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(html);

            // 타겟 강조
            if (step.target) {
                $('.acf-csb-nudge-spotlight-target').removeClass('acf-csb-nudge-spotlight-target');
                $(step.target).addClass('acf-csb-nudge-spotlight-target');
            }

            // 버튼 이벤트
            this.bindWalkthroughEvents();
        }

        /**
         * 워크스루 이벤트 바인딩
         */
        bindWalkthroughEvents() {
            const self = this;

            $('.acf-csb-nudge-walkthrough-btn-next').off('click').on('click', function() {
                self.walkthroughStep++;
                self.showWalkthroughStep();
            });

            $('.acf-csb-nudge-walkthrough-btn-prev').off('click').on('click', function() {
                self.walkthroughStep--;
                self.showWalkthroughStep();
            });
        }

        /**
         * 넛지 닫기
         */
        dismissNudge(nudgeId, saveToServer = true) {
            // UI에서 제거
            $(`[data-nudge-id="${nudgeId}"]`).fadeOut(300, function() {
                $(this).remove();
            });

            // 스포트라이트 정리
            $('.acf-csb-nudge-spotlight-overlay').remove();
            $('.acf-csb-nudge-spotlight-target').removeClass('acf-csb-nudge-spotlight-target');

            // 모달 오버레이 제거
            $('.acf-csb-nudge-modal-overlay').has(`[data-nudge-id="${nudgeId}"]`).fadeOut(300, function() {
                $(this).remove();
            });

            // 서버에 저장
            if (saveToServer) {
                this.trackNudge(nudgeId, 'dismiss');
                
                $.ajax({
                    url: acfCsbNudge.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'acf_csb_dismiss_nudge',
                        nonce: acfCsbNudge.nonce,
                        nudge_id: nudgeId
                    }
                });
            }
        }

        /**
         * 넛지 트래킹
         */
        trackNudge(nudgeId, actionType) {
            $.ajax({
                url: acfCsbNudge.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'acf_csb_track_nudge',
                    nonce: acfCsbNudge.nonce,
                    nudge_id: nudgeId,
                    action_type: actionType
                }
            });
        }

        /**
         * 카테고리 아이콘
         */
        getCategoryIcon(category) {
            const icons = {
                onboarding: '🎉',
                feature: '💡',
                upgrade: '⭐',
                tip: '💡',
                promo: '🎁',
                warning: '⚠️',
                success: '✅'
            };
            return icons[category] || '💡';
        }

        /**
         * HTML 이스케이프
         */
        escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }
    }

    /**
     * DOM Ready
     */
    $(document).ready(function() {
        if (window.acfCsbNudge) {
            window.acfCsbNudgeSystem = new ACFCSBNudgeSystem();
        }
    });

})(jQuery);
