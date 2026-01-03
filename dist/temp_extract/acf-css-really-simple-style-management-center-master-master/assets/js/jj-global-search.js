/**
 * JJ Global Search
 * 
 * [Phase 9.2] 전역 검색 기능
 * 
 * @since 9.2.0
 */

(function($) {
    'use strict';
    
    const GlobalSearch = {
        $searchInput: null,
        $searchResults: null,
        searchTimeout: null,
        activeIndex: -1,
        lastResults: [],
        
        init: function() {
            this.createSearchUI();
            this.bindEvents();
        },
        
        createSearchUI: function() {
            // 검색 UI가 이미 있으면 스킵
            if ($('#jj-global-search').length > 0) {
                return;
            }
            
            const searchHtml = `
                <div id="jj-global-search" style="position: relative; margin: 20px 0;">
                    <div style="position: relative;">
                        <label class="screen-reader-text" for="jj-global-search-input">전역 검색</label>
                        <input 
                            type="text" 
                            id="jj-global-search-input" 
                            class="jj-global-search-input" 
                            placeholder="${jjGlobalSearch.strings.search_placeholder}"
                            role="combobox"
                            aria-autocomplete="list"
                            aria-expanded="false"
                            aria-controls="jj-global-search-results"
                            aria-haspopup="listbox"
                            aria-label="${jjGlobalSearch.strings.search_placeholder}"
                            style="width: 100%; padding: 10px 40px 10px 15px; font-size: 14px; border: 1px solid #ddd; border-radius: 4px;"
                        />
                        <span class="dashicons dashicons-search" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #666; pointer-events: none;"></span>
                    </div>
                    <div id="jj-global-search-results" class="jj-global-search-results" role="listbox" aria-label="검색 결과" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #ddd; border-top: none; border-radius: 0 0 4px 4px; max-height: 400px; overflow-y: auto; z-index: 1000; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-top: -1px;">
                    </div>
                </div>
            `;
            
            // Admin Center 헤더에 삽입
            $('.jj-admin-center-header').first().after(searchHtml);
            
            this.$searchInput = $('#jj-global-search-input');
            this.$searchResults = $('#jj-global-search-results');
        },
        
        bindEvents: function() {
            const self = this;
            
            // 검색 입력
            $(document).on('input', '#jj-global-search-input', function() {
                const query = $(this).val().trim();
                
                clearTimeout(self.searchTimeout);
                
                if (query.length < 2) {
                    self.hideResults();
                    return;
                }
                
                // 디바운스 (300ms)
                self.searchTimeout = setTimeout(function() {
                    self.performSearch(query);
                }, 300);
            });
            
            // 검색 결과 외부 클릭 시 닫기
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#jj-global-search').length) {
                    self.hideResults();
                }
            });
            
            // 검색 결과 항목 클릭
            $(document).on('click', '.jj-search-result-item', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                if (url) {
                    window.location.href = url;
                }
            });
            
            // 키보드 단축키 (Ctrl/Cmd + K)
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k' && !$(e.target).is('input, textarea')) {
                    e.preventDefault();
                    self.$searchInput.focus();
                }
            });
            
            // 키보드 탐색 (Esc/Enter/Arrow)
            $(document).on('keydown', '#jj-global-search-input', function(e) {
                if (e.key === 'Escape') {
                    self.hideResults();
                    $(this).blur();
                    return;
                }

                if (!self.$searchResults.is(':visible') || !self.lastResults || self.lastResults.length === 0) {
                    return;
                }

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    self.setActiveIndex(self.activeIndex + 1);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    self.setActiveIndex(self.activeIndex - 1);
                } else if (e.key === 'Enter') {
                    if (self.activeIndex >= 0 && self.lastResults[self.activeIndex]) {
                        e.preventDefault();
                        const url = self.lastResults[self.activeIndex].url;
                        if (url) {
                            window.location.href = url;
                        }
                    }
                }
            });
        },
        
        performSearch: function(query) {
            const self = this;
            
            // 로딩 표시
            this.showLoading();
            
            $.ajax({
                url: jjGlobalSearch.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_global_search',
                    nonce: jjGlobalSearch.nonce,
                    query: query
                },
                success: function(response) {
                    if (response.success) {
                        self.renderResults(response.data.results, response.data.total);
                    } else {
                        self.showError(response.data.message || '검색 실패');
                    }
                },
                error: function() {
                    self.showError('검색 중 오류가 발생했습니다.');
                }
            });
        },
        
        showLoading: function() {
            this.$searchResults.html(`
                <div style="padding: 20px; text-align: center; color: #666;">
                    <span class="spinner is-active" style="float: none; margin: 0 auto;"></span>
                    <p style="margin: 10px 0 0 0;">${jjGlobalSearch.strings.searching}</p>
                </div>
            `).show();
            this.$searchInput.attr('aria-expanded', 'true');
            this.activeIndex = -1;
            this.lastResults = [];
        },
        
        showError: function(message) {
            this.$searchResults.html(`
                <div style="padding: 20px; text-align: center; color: #d63638;">
                    <span class="dashicons dashicons-warning" style="vertical-align: middle; margin-right: 5px;"></span>
                    ${message}
                </div>
            `).show();
        },
        
        renderResults: function(results, total) {
            if (!results || results.length === 0) {
                this.$searchResults.html(`
                    <div style="padding: 20px; text-align: center; color: #666;">
                        ${jjGlobalSearch.strings.no_results}
                    </div>
                `).show();
                this.$searchInput.attr('aria-expanded', 'true');
                this.$searchInput.removeAttr('aria-activedescendant');
                this.activeIndex = -1;
                this.lastResults = [];
                return;
            }
            
            let resultsHtml = `<div style="padding: 10px; border-bottom: 1px solid #eee; background: #f5f5f5; font-size: 12px; color: #666;">
                총 ${total}개 결과
            </div>`;
            
            this.lastResults = results;
            this.activeIndex = -1;

            results.forEach(function(result) {
                const icon = result.icon || 'dashicons-admin-generic';
                const context = result.context || '';
                const time = result.time ? ` <span style="color: #999; font-size: 11px;">${result.time}</span>` : '';
                const optionId = 'jj-search-option-' + (result.type || 'item') + '-' + Math.random().toString(36).slice(2);
                
                resultsHtml += `
                    <a id="${optionId}" role="option" aria-selected="false" href="${result.url || '#'}" class="jj-search-result-item" data-url="${result.url || '#'}" style="display: block; padding: 12px 15px; border-bottom: 1px solid #eee; text-decoration: none; color: #333; transition: background 0.2s;">
                        <div style="display: flex; align-items: center;">
                            <span class="dashicons ${icon}" style="margin-right: 10px; color: #666;"></span>
                            <div style="flex: 1;">
                                <div style="font-weight: 500; margin-bottom: 2px;">${result.title}</div>
                                <div style="font-size: 11px; color: #999;">
                                    ${context}${time}
                                </div>
                            </div>
                        </div>
                    </a>
                `;
            });
            
            this.$searchResults.html(resultsHtml).show();
            this.$searchInput.attr('aria-expanded', 'true');
            
            // 호버 효과
            $('.jj-search-result-item').on('mouseenter', function() {
                const $items = $('.jj-search-result-item');
                GlobalSearch.setActiveIndex($items.index(this));
            }).on('mouseleave', function() {
                // no-op
            });
        },
        
        setActiveIndex: function(index) {
            const $items = this.$searchResults.find('.jj-search-result-item');
            if (!$items.length) return;

            // 헤더(총 결과 div) 때문에 index가 음수가 될 수 있음
            const max = $items.length - 1;
            if (index < 0) index = max;
            if (index > max) index = 0;

            this.activeIndex = index;

            $items.each(function(i) {
                const $item = $(this);
                const active = i === index;
                $item.attr('aria-selected', active ? 'true' : 'false');
                $item.css('background', active ? '#f0f0f1' : '#fff');
            });

            const $active = $items.eq(index);
            const activeId = $active.attr('id');
            if (activeId) {
                this.$searchInput.attr('aria-activedescendant', activeId);
            }
        },

        hideResults: function() {
            this.$searchResults.hide();
            this.$searchInput.attr('aria-expanded', 'false');
            this.$searchInput.removeAttr('aria-activedescendant');
            this.activeIndex = -1;
            this.lastResults = [];
        }
    };
    
    // 초기화
    $(document).ready(function() {
        if (typeof jjGlobalSearch !== 'undefined') {
            GlobalSearch.init();
        }
    });
    
})(jQuery);
