/**
 * [Phase 8.5] 접근성(a11y) 개선
 * 
 * 기능:
 * - ARIA 레이블 자동 추가
 * - 키보드 네비게이션 강화
 * - 스크린 리더 지원
 * - 포커스 관리
 */

(function($) {
    'use strict';

    const A11yEnhancer = {
        init: function() {
            this.addAriaLabels();
            this.enhanceKeyboardNavigation();
            this.enhanceScreenReaderSupport();
            this.manageFocus();
        },

        /**
         * ARIA 레이블 자동 추가
         */
        addAriaLabels: function() {
            const $wrap = $('.jj-admin-center-wrap');
            if (!$wrap.length) return;

            // 버튼에 ARIA 레이블 추가
            $wrap.find('button:not([aria-label]):not([aria-labelledby])').each(function() {
                const $btn = $(this);
                const text = $btn.text().trim();
                const icon = $btn.find('.dashicons').attr('class');
                
                if (text || icon) {
                    let label = text;
                    if (icon && !text) {
                        // 아이콘만 있는 경우 아이콘 클래스에서 레이블 추론
                        label = A11yEnhancer.getIconLabel(icon);
                    }
                    
                    if (label) {
                        $btn.attr('aria-label', label);
                    }
                }
            });

            // 링크에 ARIA 레이블 추가
            $wrap.find('a:not([aria-label]):not([aria-labelledby])').each(function() {
                const $link = $(this);
                const text = $link.text().trim();
                const title = $link.attr('title');
                
                if (!text && title) {
                    $link.attr('aria-label', title);
                } else if (!text && $link.find('.dashicons').length) {
                    const icon = $link.find('.dashicons').attr('class');
                    $link.attr('aria-label', A11yEnhancer.getIconLabel(icon));
                }
            });

            // 입력 필드에 ARIA 레이블 추가
            $wrap.find('input, select, textarea').each(function() {
                const $field = $(this);
                if ($field.attr('aria-label') || $field.attr('aria-labelledby')) {
                    return;
                }

                // 라벨 찾기
                const id = $field.attr('id');
                if (id) {
                    const $label = $wrap.find('label[for="' + id + '"]');
                    if ($label.length) {
                        $field.attr('aria-labelledby', id + '-label');
                        $label.attr('id', id + '-label');
                    }
                } else {
                    // 이름으로 찾기
                    const name = $field.attr('name');
                    if (name) {
                        const $label = $wrap.find('label').filter(function() {
                            return $(this).text().trim() && $(this).next().is($field);
                        });
                        if ($label.length) {
                            $field.attr('aria-label', $label.text().trim());
                        }
                    }
                }
            });

            // 탭에 ARIA 속성 추가
            $wrap.find('.jj-admin-center-tabs, .jj-admin-center-sidebar-nav').each(function() {
                const $nav = $(this);
                if (!$nav.attr('role')) {
                    $nav.attr('role', 'tablist');
                }
                
                $nav.find('a').each(function(index) {
                    const $tab = $(this);
                    if (!$tab.attr('role')) {
                        $tab.attr('role', 'tab');
                    }
                    if (!$tab.attr('aria-selected')) {
                        $tab.attr('aria-selected', $tab.hasClass('active') ? 'true' : 'false');
                    }
                    if (!$tab.attr('tabindex')) {
                        $tab.attr('tabindex', $tab.hasClass('active') ? '0' : '-1');
                    }
                });
            });

            // 모달에 ARIA 속성 추가
            $wrap.find('.jj-modal, [class*="modal"]').each(function() {
                const $modal = $(this);
                if (!$modal.attr('role')) {
                    $modal.attr('role', 'dialog');
                    $modal.attr('aria-modal', 'true');
                }
                
                // 모달 헤더 찾기
                const $header = $modal.find('h1, h2, h3, h4, h5, h6, .modal-header').first();
                if ($header.length && !$modal.attr('aria-labelledby')) {
                    const headerId = 'modal-header-' + Date.now();
                    $header.attr('id', headerId);
                    $modal.attr('aria-labelledby', headerId);
                }
            });
        },

        /**
         * 아이콘 클래스에서 레이블 추론
         */
        getIconLabel: function(iconClass) {
            const iconMap = {
                'dashicons-yes': '완료',
                'dashicons-no': '취소',
                'dashicons-edit': '편집',
                'dashicons-trash': '삭제',
                'dashicons-plus': '추가',
                'dashicons-minus': '제거',
                'dashicons-admin-settings': '설정',
                'dashicons-admin-appearance': '외관',
                'dashicons-art': '스타일',
                'dashicons-palmtree': '팔레트',
                'dashicons-undo': '되돌리기',
                'dashicons-redo': '다시 실행',
                'dashicons-menu-alt': '메뉴',
                'dashicons-admin-plugins': '플러그인',
                'dashicons-download': '다운로드',
                'dashicons-editor-help': '도움말'
            };

            if (typeof iconClass === 'string') {
                for (const icon in iconMap) {
                    if (iconClass.indexOf(icon) !== -1) {
                        return iconMap[icon];
                    }
                }
            }

            return '';
        },

        /**
         * 키보드 네비게이션 강화
         */
        enhanceKeyboardNavigation: function() {
            const $wrap = $('.jj-admin-center-wrap');
            if (!$wrap.length) return;

            // 탭 키보드 네비게이션 (화살표 키)
            $wrap.on('keydown', '[role="tab"]', function(e) {
                const $tabs = $wrap.find('[role="tab"]');
                const currentIndex = $tabs.index(this);
                let targetIndex = currentIndex;

                switch (e.key) {
                    case 'ArrowRight':
                    case 'ArrowDown':
                        e.preventDefault();
                        targetIndex = (currentIndex + 1) % $tabs.length;
                        break;
                    case 'ArrowLeft':
                    case 'ArrowUp':
                        e.preventDefault();
                        targetIndex = currentIndex - 1;
                        if (targetIndex < 0) targetIndex = $tabs.length - 1;
                        break;
                    case 'Home':
                        e.preventDefault();
                        targetIndex = 0;
                        break;
                    case 'End':
                        e.preventDefault();
                        targetIndex = $tabs.length - 1;
                        break;
                }

                if (targetIndex !== currentIndex) {
                    const $target = $tabs.eq(targetIndex);
                    $tabs.attr('aria-selected', 'false').attr('tabindex', '-1');
                    $target.attr('aria-selected', 'true').attr('tabindex', '0').focus().click();
                }
            });

            // 모달 내 포커스 트랩
            $wrap.on('keydown', '[role="dialog"]', function(e) {
                if (e.key === 'Tab') {
                    const $modal = $(this);
                    const $focusable = $modal.find('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
                    const $first = $focusable.first();
                    const $last = $focusable.last();

                    if (e.shiftKey) {
                        // Shift + Tab
                        if (document.activeElement === $first[0]) {
                            e.preventDefault();
                            $last.focus();
                        }
                    } else {
                        // Tab
                        if (document.activeElement === $last[0]) {
                            e.preventDefault();
                            $first.focus();
                        }
                    }
                }
            });
        },

        /**
         * 스크린 리더 지원 강화
         */
        enhanceScreenReaderSupport: function() {
            const $wrap = $('.jj-admin-center-wrap');
            if (!$wrap.length) return;

            // 라이브 영역 생성 (동적 콘텐츠 알림용)
            if ($('#jj-a11y-live-region').length === 0) {
                $('body').append('<div id="jj-a11y-live-region" class="screen-reader-text" aria-live="polite" aria-atomic="true"></div>');
            }

            // Toast 알림 시 스크린 리더에 알림
            $(document).on('jj:toast:shown', function(e, message) {
                A11yEnhancer.announceToScreenReader(message);
            });

            // 탭 전환 시 스크린 리더에 알림
            $wrap.on('click', '[role="tab"]', function() {
                const tabName = $(this).text().trim();
                A11yEnhancer.announceToScreenReader(tabName + ' 탭으로 전환됨');
            });
        },

        /**
         * 스크린 리더에 메시지 전달
         */
        announceToScreenReader: function(message) {
            const $liveRegion = $('#jj-a11y-live-region');
            if ($liveRegion.length) {
                $liveRegion.text(message);
                setTimeout(function() {
                    $liveRegion.text('');
                }, 1000);
            }
        },

        /**
         * 포커스 관리
         */
        manageFocus: function() {
            const $wrap = $('.jj-admin-center-wrap');
            if (!$wrap.length) return;

            // 모달 열릴 때 첫 번째 포커스 가능한 요소에 포커스
            $(document).on('jj:modal:open', function(e, $modal) {
                const $firstFocusable = $modal.find('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])').first();
                if ($firstFocusable.length) {
                    setTimeout(function() {
                        $firstFocusable.focus();
                    }, 100);
                }
            });

            // 모달 닫힐 때 이전 포커스 복원
            let previousFocus = null;
            $(document).on('jj:modal:open', function() {
                previousFocus = document.activeElement;
            });
            
            $(document).on('jj:modal:close', function() {
                if (previousFocus && $(previousFocus).length) {
                    setTimeout(function() {
                        $(previousFocus).focus();
                    }, 100);
                }
            });
        }
    };

    // 초기화
    $(document).ready(function() {
        A11yEnhancer.init();
    });

    // 전역 접근
    window.JJA11y = A11yEnhancer;

})(jQuery);
