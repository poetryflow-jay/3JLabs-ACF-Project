/**
 * 3J Labs Onboarding Tour
 * - Jenny's Marketing UX: guiding users through the "WOW" moments
 */
(function($) {
    'use strict';

    var JJTour = {
        currentStep: 0,
        steps: [
            {
                element: '.jj-section-wrapper[data-section="presets"]',
                title: 'Step 1: 빠른 스타일 적용 🎨',
                content: '전문가가 설계한 프리셋으로 시작해보세요. 클릭 한 번으로 사이트 분위기가 바뀝니다.',
                position: 'bottom'
            },
            {
                element: '.jj-style-guide-sections',
                title: 'Step 2: 세부 디자인 커스텀 🛠️',
                content: '색상, 타이포그래피, 버튼 스타일을 원하는 대로 정밀하게 조정할 수 있습니다.',
                position: 'top'
            },
            {
                element: '#jj-save-style-guide',
                title: 'Step 3: 저장 및 확인 ✅',
                content: '설정이 마음에 드신다면 저장 버튼을 눌러주세요. 실시간으로 반영됩니다!',
                position: 'top'
            }
        ],

        init: function() {
            var self = this;
            $(document).on('click', '.jj-start-now', function() {
                self.start();
            });

            // ESC 키로 투어 종료
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    self.end();
                }
            });
        },

        start: function() {
            this.currentStep = 0;
            this.createOverlay();
            this.showStep(this.currentStep);
            $('html, body').animate({ scrollTop: 0 }, 500);
        },

        createOverlay: function() {
            if (!$('#jj-tour-overlay').length) {
                $('body').append('<div id="jj-tour-overlay"></div>');
                $('#jj-tour-overlay').css({
                    'position': 'fixed',
                    'top': 0,
                    'left': 0,
                    'width': '100%',
                    'height': '100%',
                    'background': 'rgba(0,0,0,0.5)',
                    'z-index': '999998',
                    'display': 'none'
                }).fadeIn(300);
            }
        },

        showStep: function(index) {
            var self = this;
            var step = this.steps[index];
            var $el = $(step.element);

            if (!$el.length) {
                this.next();
                return;
            }

            $('.jj-tour-tooltip').remove();
            $('.jj-tour-highlight').removeClass('jj-tour-highlight');

            $el.addClass('jj-tour-highlight');
            $el.css({
                'position': 'relative',
                'z-index': '999999',
                'box-shadow': '0 0 0 9999px rgba(0,0,0,0.5), 0 0 15px rgba(255,255,255,0.5)'
            });

            var offset = $el.offset();
            var tooltipHtml = `
                <div class="jj-tour-tooltip" style="position: absolute; z-index: 1000000; background: #fff; padding: 20px; border-radius: 12px; width: 300px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                    <h3 style="margin: 0 0 10px 0; color: #1e293b;">${step.title}</h3>
                    <p style="margin: 0 0 20px 0; color: #64748b; font-size: 14px;">${step.content}</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 12px; color: #94a3b8;">${index + 1} / ${this.steps.length}</span>
                        <div>
                            ${index > 0 ? '<button class="jj-tour-prev button">이전</button>' : ''}
                            <button class="jj-tour-next button button-primary">${index === this.steps.length - 1 ? '완료' : '다음'}</button>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(tooltipHtml);
            var $tooltip = $('.jj-tour-tooltip');

            var top, left;
            if (step.position === 'bottom') {
                top = offset.top + $el.outerHeight() + 20;
                left = offset.left + ($el.outerWidth() / 2) - ($tooltip.outerWidth() / 2);
            } else {
                top = offset.top - $tooltip.outerHeight() - 20;
                left = offset.left + ($el.outerWidth() / 2) - ($tooltip.outerWidth() / 2);
            }

            $tooltip.css({ 'top': top, 'left': left });

            // 스크롤 이동
            $('html, body').animate({
                scrollTop: top - 100
            }, 500);

            $('.jj-tour-next').on('click', function() {
                self.next();
            });

            $('.jj-tour-prev').on('click', function() {
                self.prev();
            });
        },

        next: function() {
            this.currentStep++;
            if (this.currentStep < this.steps.length) {
                this.showStep(this.currentStep);
            } else {
                this.end();
            }
        },

        prev: function() {
            this.currentStep--;
            if (this.currentStep >= 0) {
                this.showStep(this.currentStep);
            }
        },

        end: function() {
            $('.jj-tour-tooltip').remove();
            $('#jj-tour-overlay').fadeOut(300, function() { $(this).remove(); });
            $('.jj-tour-highlight').css({
                'position': '',
                'z-index': '',
                'box-shadow': ''
            }).removeClass('jj-tour-highlight');
        }
    };

    $(document).ready(function() {
        JJTour.init();
    });

})(jQuery);
