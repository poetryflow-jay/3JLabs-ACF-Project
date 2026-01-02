/**
 * [Phase 8.3.1] 키보드 단축키 및 네비게이션 개선
 * 
 * 키보드 단축키:
 * - Ctrl/Cmd + K: 탭 검색/빠른 이동
 * - Ctrl/Cmd + 1-9: 특정 탭으로 이동
 * - Esc: 모달 닫기, 검색 닫기
 * - /: 검색 포커스
 */

(function($) {
    'use strict';

    const KeyboardNav = {
        init: function() {
            this.bindShortcuts();
            this.initQuickSearch();
            this.initTabNavigation();
        },

        /**
         * 키보드 단축키 바인딩
         */
        bindShortcuts: function() {
            $(document).on('keydown', function(e) {
                // Ctrl/Cmd + K: 빠른 검색
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    KeyboardNav.openQuickSearch();
                    return false;
                }

                // Esc: 모달/검색 닫기
                if (e.key === 'Escape') {
                    KeyboardNav.closeModals();
                }

                // /: 검색 포커스 (입력 필드에 포커스가 없을 때만)
                if (e.key === '/' && !$(e.target).is('input, textarea, select')) {
                    e.preventDefault();
                    KeyboardNav.focusSearch();
                }

                // Ctrl/Cmd + 1-9: 탭 이동
                if ((e.ctrlKey || e.metaKey) && e.key >= '1' && e.key <= '9') {
                    e.preventDefault();
                    const tabIndex = parseInt(e.key, 10) - 1;
                    KeyboardNav.navigateToTab(tabIndex);
                }
            });
        },

        /**
         * 빠른 검색 초기화
         */
        initQuickSearch: function() {
            const $wrap = $('.jj-admin-center-wrap');
            if (!$wrap.length) return;

            // 검색 오버레이 생성
            if ($('#jj-quick-search-overlay').length === 0) {
                $('body').append(`
                    <div id="jj-quick-search-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999998;">
                        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:600px; max-width:90vw; background:#fff; border-radius:8px; box-shadow:0 4px 20px rgba(0,0,0,0.15);">
                            <div style="padding:20px; border-bottom:1px solid #ddd;">
                                <input type="text" id="jj-quick-search-input" placeholder="탭 검색... (예: colors, updates, system)" 
                                       style="width:100%; padding:12px; font-size:16px; border:2px solid #2271b1; border-radius:4px; outline:none;">
                            </div>
                            <div id="jj-quick-search-results" style="max-height:400px; overflow-y:auto; padding:10px;">
                                <!-- 검색 결과가 여기에 표시됨 -->
                            </div>
                        </div>
                    </div>
                `);
            }

            const $overlay = $('#jj-quick-search-overlay');
            const $input = $('#jj-quick-search-input');
            const $results = $('#jj-quick-search-results');

            // 검색 입력 이벤트
            $input.on('input', function() {
                const query = $(this).val().toLowerCase().trim();
                if (query.length === 0) {
                    $results.html('');
                    return;
                }
                KeyboardNav.performQuickSearch(query);
            });

            // Enter: 첫 번째 결과로 이동
            $input.on('keydown', function(e) {
                if (e.key === 'Enter') {
                    const $firstResult = $results.find('.jj-quick-search-item').first();
                    if ($firstResult.length) {
                        $firstResult.click();
                    }
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    $results.find('.jj-quick-search-item').first().focus();
                }
            });

            // 오버레이 클릭 시 닫기
            $overlay.on('click', function(e) {
                if ($(e.target).is('#jj-quick-search-overlay')) {
                    KeyboardNav.closeQuickSearch();
                }
            });
        },

        /**
         * 빠른 검색 실행
         */
        performQuickSearch: function(query) {
            const $results = $('#jj-quick-search-results');
            const $tabs = $('.jj-admin-center-tabs a, .jj-admin-center-sidebar-nav a');
            const matches = [];

            $tabs.each(function() {
                const $tab = $(this);
                const tabText = $tab.text().toLowerCase();
                const tabId = $tab.data('tab') || $tab.attr('href') || '';
                const tabLabel = $tab.text().trim();

                if (tabText.includes(query) || tabId.includes(query)) {
                    matches.push({
                        label: tabLabel,
                        element: $tab,
                        href: tabId || $tab.attr('href')
                    });
                }
            });

            if (matches.length === 0) {
                $results.html('<div style="padding:20px; text-align:center; color:#666;">검색 결과가 없습니다.</div>');
                return;
            }

            let html = '';
            matches.slice(0, 10).forEach(function(match, index) {
                html += `
                    <div class="jj-quick-search-item" data-index="${index}" 
                         style="padding:12px 20px; cursor:pointer; border-bottom:1px solid #eee; transition:background 0.2s;"
                         onmouseover="this.style.background='#f0f0f1'"
                         onmouseout="this.style.background=''">
                        <div style="font-weight:600; color:#1d2327;">${match.label}</div>
                        <div style="font-size:12px; color:#666; margin-top:4px;">${match.href}</div>
                    </div>
                `;
            });

            $results.html(html);

            // 클릭 이벤트
            $results.find('.jj-quick-search-item').on('click', function() {
                const index = $(this).data('index');
                matches[index].element.click();
                KeyboardNav.closeQuickSearch();
            });
        },

        /**
         * 빠른 검색 열기
         */
        openQuickSearch: function() {
            $('#jj-quick-search-overlay').fadeIn(200);
            $('#jj-quick-search-input').focus();
        },

        /**
         * 빠른 검색 닫기
         */
        closeQuickSearch: function() {
            $('#jj-quick-search-overlay').fadeOut(200);
            $('#jj-quick-search-input').val('');
            $('#jj-quick-search-results').html('');
        },

        /**
         * 탭 네비게이션 초기화
         */
        initTabNavigation: function() {
            // 사이드바 네비게이션과 탭 네비게이션 동기화
            $(document).on('click', '.jj-admin-center-sidebar-nav a, .jj-admin-center-tabs a', function(e) {
                const $link = $(this);
                const tabId = $link.data('tab') || $link.attr('href').replace('#', '');

                // 사이드바와 탭 네비게이션 모두 업데이트
                $('.jj-admin-center-sidebar-nav a, .jj-admin-center-tabs a').removeClass('active');
                $(`.jj-admin-center-sidebar-nav a[data-tab="${tabId}"], .jj-admin-center-tabs a[data-tab="${tabId}"]`).addClass('active');
            });
        },

        /**
         * 특정 탭으로 이동
         */
        navigateToTab: function(index) {
            const $tabs = $('.jj-admin-center-tabs a');
            if ($tabs.length > index) {
                $tabs.eq(index).click();
            }
        },

        /**
         * 검색 필드에 포커스
         */
        focusSearch: function() {
            const $searchInputs = $('.jj-admin-center-wrap input[type="search"], .jj-admin-center-wrap input[placeholder*="검색"], .jj-admin-center-wrap input[placeholder*="Search"]');
            if ($searchInputs.length) {
                $searchInputs.first().focus();
            } else {
                KeyboardNav.openQuickSearch();
            }
        },

        /**
         * 모든 모달 닫기
         */
        closeModals: function() {
            KeyboardNav.closeQuickSearch();
            $('.jj-modal, .jj-changes-preview-modal').fadeOut(200);
        }
    };

    // DOM 준비 시 초기화
    $(document).ready(function() {
        KeyboardNav.init();
    });

})(jQuery);
