/**
 * JJ Dark Mode System v25.0.0
 * 다크 모드 지원 시스템
 * 
 * @package ACF_CSS_Style_Guide
 * @version 25.0.0
 * @author 3J Labs
 * 
 * 주요 기능:
 * - 자동/수동 다크 모드 전환
 * - 시스템 설정 감지
 * - 부드러운 전환 애니메이션
 * - 테마 일관성 유지
 * - 사용자 설정 저장
 */

(function($) {
    'use strict';

    /**
     * Dark Mode Manager
     */
    const DarkMode = {
        /**
         * 초기화
         */
        init: function() {
            this.loadSettings();
            this.initThemeToggle();
            this.detectSystemPreference();
            this.applyTheme();
            this.bindEvents();
        },

        /**
         * 설정 로드
         */
        loadSettings: function() {
            this.settings = {
                mode: localStorage.getItem('jj_dark_mode') || 'auto', // auto, light, dark
                transition: true,
                persist: true
            };

            // 사용자 설정이 없으면 시스템 설정 사용
            if (this.settings.mode === 'auto') {
                this.settings.mode = this.detectSystemPreference() ? 'dark' : 'light';
            }
        },

        /**
         * 시스템 설정 감지
         */
        detectSystemPreference: function() {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return true;
            }
            return false;
        },

        /**
         * 테마 적용
         */
        applyTheme: function() {
            const isDark = this.settings.mode === 'dark';
            const $body = $('body');
            const $html = $('html');

            if (isDark) {
                $body.addClass('jj-theme-dark').attr('data-theme', 'dark');
                $html.addClass('jj-theme-dark').attr('data-theme', 'dark');
            } else {
                $body.removeClass('jj-theme-dark').attr('data-theme', 'light');
                $html.removeClass('jj-theme-dark').attr('data-theme', 'light');
            }

            // CSS 변수 업데이트
            this.updateCSSVariables(isDark);
        },

        /**
         * CSS 변수 업데이트
         */
        updateCSSVariables: function(isDark) {
            const root = document.documentElement;
            
            if (isDark) {
                root.style.setProperty('--jj-bg-primary', '#0F172A');
                root.style.setProperty('--jj-bg-secondary', '#1E293B');
                root.style.setProperty('--jj-text-primary', '#F1F5F9');
                root.style.setProperty('--jj-text-secondary', '#CBD5E1');
            } else {
                root.style.setProperty('--jj-bg-primary', '#FFFFFF');
                root.style.setProperty('--jj-bg-secondary', '#F5F5F5');
                root.style.setProperty('--jj-text-primary', '#262626');
                root.style.setProperty('--jj-text-secondary', '#525252');
            }
        },

        /**
         * 테마 토글 초기화
         */
        initThemeToggle: function() {
            // 관리자 페이지에서만 토글 버튼 표시
            if (!$('body').hasClass('wp-admin')) {
                return;
            }

            // 토글 버튼이 없으면 생성
            if (!$('.jj-dark-mode-toggle').length) {
                const currentIcon = this.settings.mode === 'dark' ? '☀️' : '🌙';
                const $toggle = $('<button type="button" class="jj-dark-mode-toggle" aria-label="다크 모드 전환" title="다크 모드 전환 (Ctrl+Shift+D)">' +
                    '<span class="jj-dark-mode-icon">' + currentIcon + '</span>' +
                    '</button>');
                $('body').append($toggle);
            } else {
                // 아이콘 업데이트
                const currentIcon = this.settings.mode === 'dark' ? '☀️' : '🌙';
                $('.jj-dark-mode-icon').text(currentIcon);
            }
        },

        /**
         * 이벤트 바인딩
         */
        bindEvents: function() {
            const self = this;

            // 토글 버튼 클릭
            $(document).on('click', '.jj-dark-mode-toggle', function() {
                self.toggleTheme();
            });

            // 시스템 설정 변경 감지
            if (window.matchMedia) {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                mediaQuery.addListener(function(e) {
                    if (self.settings.mode === 'auto') {
                        self.settings.mode = e.matches ? 'dark' : 'light';
                        self.applyTheme();
                    }
                });
            }

            // 키보드 단축키 (Ctrl+Shift+D / Cmd+Shift+D)
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
                    e.preventDefault();
                    self.toggleTheme();
                }
            });
        },

        /**
         * 테마 전환
         */
        toggleTheme: function() {
            const currentMode = this.settings.mode === 'dark' ? 'dark' : 'light';
            const newMode = currentMode === 'dark' ? 'light' : 'dark';

            this.settings.mode = newMode;
            
            if (this.settings.persist) {
                localStorage.setItem('jj_dark_mode', newMode);
            }

            // 부드러운 전환
            if (this.settings.transition) {
                $('body').addClass('jj-theme-transitioning');
            }

            this.applyTheme();

            // 아이콘 업데이트
            const newIcon = newMode === 'dark' ? '☀️' : '🌙';
            $('.jj-dark-mode-icon').text(newIcon);

            // 전환 완료 후 클래스 제거
            setTimeout(function() {
                $('body').removeClass('jj-theme-transitioning');
            }, 300);

            // 인웹 팝업 표시 (v25.0.0)
            if (typeof JJInWebPopup !== 'undefined') {
                JJInWebPopup.show({
                    type: 'info',
                    icon: newMode === 'dark' ? '🌙' : '☀️',
                    title: newMode === 'dark' ? '다크 모드 활성화' : '라이트 모드 활성화',
                    message: '테마가 ' + (newMode === 'dark' ? '다크 모드' : '라이트 모드') + '로 전환되었습니다.',
                    position: 'top-right',
                    duration: 2000,
                    dismissible: true
                });
            } else if (typeof JJNudge !== 'undefined') {
                // 폴백: 넛지 시스템 사용
                JJNudge.showNudge({
                    id: 'dark-mode-toggled',
                    type: 'info',
                    title: newMode === 'dark' ? '🌙 다크 모드 활성화' : '☀️ 라이트 모드 활성화',
                    message: '테마가 ' + (newMode === 'dark' ? '다크 모드' : '라이트 모드') + '로 전환되었습니다.',
                    position: 'top-right',
                    duration: 2000,
                    dismissible: true
                });
            }
        }
    };

    // DOM 준비 시 초기화
    $(document).ready(function() {
        DarkMode.init();
    });

    // 전역으로 노출
    window.JJDarkMode = DarkMode;

})(jQuery);
