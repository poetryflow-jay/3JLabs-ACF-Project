/**
 * JJ Animations System v25.0.0
 * 고급 애니메이션 및 전환 효과 관리
 * 
 * @package ACF_CSS_Style_Guide
 * @version 25.0.0
 * @author 3J Labs
 * 
 * 주요 기능:
 * - 스크롤 애니메이션
 * - 페이지 전환 효과
 * - 마이크로 인터랙션
 * - 성능 최적화
 */

(function($) {
    'use strict';

    /**
     * Animations Manager
     */
    const Animations = {
        /**
         * 초기화
         */
        init: function() {
            this.initScrollAnimations();
            this.initMicroInteractions();
            this.initPageTransitions();
            this.initLazyAnimations();
            this.bindEvents();
        },

        /**
         * 스크롤 애니메이션 초기화
         */
        initScrollAnimations: function() {
            // Intersection Observer 사용 (성능 최적화)
            if (typeof IntersectionObserver === 'undefined') {
                // 폴백: 스크롤 이벤트 사용
                this.initScrollAnimationsFallback();
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        $(entry.target).addClass('jj-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // 애니메이션 대상 요소 관찰
            $('.jj-fade-in-on-scroll, .jj-scale-in-on-scroll').each(function() {
                observer.observe(this);
            });
        },

        /**
         * 스크롤 애니메이션 폴백
         */
        initScrollAnimationsFallback: function() {
            const self = this;
            let ticking = false;

            function checkScroll() {
                $('.jj-fade-in-on-scroll, .jj-scale-in-on-scroll').each(function() {
                    const $el = $(this);
                    const elementTop = $el.offset().top;
                    const elementBottom = elementTop + $el.outerHeight();
                    const viewportTop = $(window).scrollTop();
                    const viewportBottom = viewportTop + $(window).height();

                    if (elementBottom > viewportTop && elementTop < viewportBottom) {
                        $el.addClass('jj-visible');
                    }
                });

                ticking = false;
            }

            $(window).on('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(checkScroll);
                    ticking = true;
                }
            });

            // 초기 체크
            checkScroll();
        },

        /**
         * 마이크로 인터랙션 초기화
         */
        initMicroInteractions: function() {
            // 버튼 리플 효과
            $(document).on('click', '.jj-btn-ripple', function(e) {
                const $btn = $(this);
                const $ripple = $('<span class="jj-ripple-effect"></span>');
                
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                $ripple.css({
                    width: size + 'px',
                    height: size + 'px',
                    left: x + 'px',
                    top: y + 'px'
                });

                $btn.append($ripple);

                setTimeout(() => {
                    $ripple.remove();
                }, 600);
            });

            // 카드 클릭 효과
            $(document).on('click', '.jj-card-hover', function() {
                $(this).addClass('jj-card-clicked');
                setTimeout(() => {
                    $(this).removeClass('jj-card-clicked');
                }, 200);
            });
        },

        /**
         * 페이지 전환 효과 초기화
         */
        initPageTransitions: function() {
            // 탭 전환
            $(document).on('click', '.jj-tab-button', function() {
                const $tab = $(this);
                const targetTab = $tab.data('tab');
                const $content = $('.jj-tab-content[data-tab="' + targetTab + '"]');

                // 현재 활성 탭 숨기기
                $('.jj-tab-pane.jj-active').removeClass('jj-active').addClass('jj-tab-pane-exit');
                
                setTimeout(() => {
                    $('.jj-tab-pane-exit').removeClass('jj-tab-pane-exit');
                    $content.addClass('jj-active jj-page-transition-enter');
                }, 150);
            });
        },

        /**
         * 지연 로딩 애니메이션
         */
        initLazyAnimations: function() {
            // 이미지 로딩 애니메이션
            $('img[data-lazy]').each(function() {
                const $img = $(this);
                const src = $img.data('lazy');

                $img.on('load', function() {
                    $img.addClass('jj-fade-in-on-scroll');
                });

                $img.attr('src', src);
            });
        },

        /**
         * 이벤트 바인딩
         */
        bindEvents: function() {
            // 모달 열기/닫기
            $(document).on('click', '.jj-modal-trigger', function(e) {
                e.preventDefault();
                const target = $(this).data('modal');
                Animations.openModal(target);
            });

            $(document).on('click', '.jj-modal-close, .jj-modal-backdrop', function() {
                Animations.closeModal();
            });

            // 드롭다운 토글
            $(document).on('click', '.jj-dropdown-toggle', function(e) {
                e.stopPropagation();
                const $dropdown = $(this).closest('.jj-dropdown');
                $dropdown.toggleClass('jj-open');
            });

            $(document).on('click', function() {
                $('.jj-dropdown.jj-open').removeClass('jj-open');
            });
        },

        /**
         * 모달 열기
         */
        openModal: function(modalId) {
            const $modal = $('#' + modalId);
            if ($modal.length) {
                $('body').append('<div class="jj-modal-backdrop"></div>');
                $modal.addClass('jj-modal-visible');
                $('body').css('overflow', 'hidden');
            }
        },

        /**
         * 모달 닫기
         */
        closeModal: function() {
            $('.jj-modal-visible').removeClass('jj-modal-visible');
            $('.jj-modal-backdrop').fadeOut(200, function() {
                $(this).remove();
            });
            $('body').css('overflow', '');
        }
    };

    // DOM 준비 시 초기화
    $(document).ready(function() {
        Animations.init();
    });

    // 전역으로 노출
    window.JJAnimations = Animations;

})(jQuery);
