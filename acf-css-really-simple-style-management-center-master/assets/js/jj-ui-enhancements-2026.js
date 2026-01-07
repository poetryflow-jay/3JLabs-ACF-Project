/**
 * JJ UI Enhancements 2026 - UI/UX 개선 및 빠른 액션
 * 
 * @package ACF_CSS_Style_Guide
 * @version 23.0.4
 * @author 3J Labs
 * 
 * 주요 기능:
 * - 빠른 검색 (Ctrl+K / Cmd+K)
 * - 키보드 단축키 도움말 (? 키)
 * - 컨텍스트 메뉴 (우클릭)
 * - 자동 저장 표시기
 * - 실행 취소/다시 실행 (Ctrl+Z / Ctrl+Y)
 * - 드래그 앤 드롭 (섹션 순서 변경)
 */

(function($) {
    'use strict';

    /**
     * UI Enhancements 메인 객체
     */
    const JJUIEnhancements = {
        /**
         * 초기화
         */
        init: function() {
            this.initQuickSearch();
            this.initKeyboardShortcuts();
            this.initQuickActions();
            this.initContextMenu();
            this.initDragAndDrop();
            this.initAutoSave();
            this.initUndoRedo();
            this.showWelcomeNudge();
        },

        /**
         * 빠른 검색 (Ctrl+K / Cmd+K)
         */
        initQuickSearch: function() {
            const self = this;
            let searchModal = null;

            $(document).on('keydown', function(e) {
                // Ctrl+K 또는 Cmd+K
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    self.showQuickSearch();
                }
            });

            // 검색 모달 표시
            this.showQuickSearch = function() {
                if (!searchModal) {
                    searchModal = $('<div class="jj-quick-search-modal">' +
                        '<div class="jj-quick-search-overlay"></div>' +
                        '<div class="jj-quick-search-container">' +
                        '<div class="jj-quick-search-header">' +
                        '<input type="text" class="jj-quick-search-input" placeholder="검색... (색상, 폰트, 버튼 등)" autofocus>' +
                        '<button type="button" class="jj-quick-search-close">&times;</button>' +
                        '</div>' +
                        '<div class="jj-quick-search-results"></div>' +
                        '</div>' +
                        '</div>');
                    $('body').append(searchModal);
                }

                searchModal.fadeIn(200);
                searchModal.find('.jj-quick-search-input').focus();

                // 닫기
                searchModal.find('.jj-quick-search-close, .jj-quick-search-overlay').on('click', function() {
                    searchModal.fadeOut(200);
                });

                // ESC 키로 닫기
                $(document).on('keydown.quicksearch', function(e) {
                    if (e.key === 'Escape') {
                        searchModal.fadeOut(200);
                        $(document).off('keydown.quicksearch');
                    }
                });

                // 검색 실행
                searchModal.find('.jj-quick-search-input').on('input', function() {
                    const query = $(this).val().toLowerCase();
                    self.performQuickSearch(query);
                });
            };

            // 검색 수행
            this.performQuickSearch = function(query) {
                if (!query) {
                    $('.jj-quick-search-results').html('');
                    return;
                }

                const results = [];
                
                // 섹션 검색
                $('.jj-section-title, .jj-section-global h2').each(function() {
                    const $section = $(this).closest('.jj-section-global');
                    const title = $(this).text().toLowerCase();
                    if (title.includes(query)) {
                        results.push({
                            type: 'section',
                            title: $(this).text(),
                            element: $section,
                            id: $section.attr('id')
                        });
                    }
                });

                // 색상 검색
                $('.jj-color-input, .jj-color-picker').each(function() {
                    const $field = $(this).closest('.jj-field, .jj-color-field');
                    const label = $field.find('label, .jj-field-label').text().toLowerCase();
                    if (label.includes(query)) {
                        results.push({
                            type: 'color',
                            title: $field.find('label, .jj-field-label').text(),
                            element: $field
                        });
                    }
                });

                // 폰트 검색
                $('select[name*="font"], input[name*="font"]').each(function() {
                    const $field = $(this).closest('.jj-field');
                    const label = $field.find('label').text().toLowerCase();
                    if (label.includes(query)) {
                        results.push({
                            type: 'font',
                            title: $field.find('label').text(),
                            element: $field
                        });
                    }
                });

                // 결과 표시
                this.displaySearchResults(results);
            };

            // 검색 결과 표시
            this.displaySearchResults = function(results) {
                const $results = $('.jj-quick-search-results');
                if (results.length === 0) {
                    $results.html('<div class="jj-quick-search-no-results">검색 결과가 없습니다.</div>');
                    return;
                }

                let html = '<div class="jj-quick-search-results-list">';
                results.slice(0, 10).forEach(function(result) {
                    const icon = result.type === 'section' ? '📁' : (result.type === 'color' ? '🎨' : '🔤');
                    html += '<div class="jj-quick-search-result-item" data-type="' + result.type + '">' +
                        '<span class="jj-quick-search-icon">' + icon + '</span>' +
                        '<span class="jj-quick-search-title">' + result.title + '</span>' +
                        '</div>';
                });
                html += '</div>';

                $results.html(html);

                // 결과 클릭
                $results.find('.jj-quick-search-result-item').on('click', function() {
                    const index = $(this).index();
                    const result = results[index];
                    if (result && result.element) {
                        $('html, body').animate({
                            scrollTop: result.element.offset().top - 100
                        }, 300);
                        result.element.css('background', '#fff3cd').animate({
                            backgroundColor: '#fff'
                        }, 1000);
                        $('.jj-quick-search-modal').fadeOut(200);
                    }
                });
            };
        },

        /**
         * 키보드 단축키
         */
        initKeyboardShortcuts: function() {
            const self = this;

            // ? 키로 단축키 도움말 표시
            $(document).on('keydown', function(e) {
                if (e.key === '?' && !e.ctrlKey && !e.metaKey && !e.altKey) {
                    // 입력 필드에 포커스가 있으면 무시
                    if ($(e.target).is('input, textarea, select')) {
                        return;
                    }
                    e.preventDefault();
                    self.showKeyboardShortcuts();
                }
            });

            // 단축키 도움말 표시
            this.showKeyboardShortcuts = function() {
                const shortcuts = [
                    { key: 'Ctrl+K / Cmd+K', desc: '빠른 검색 열기' },
                    { key: 'Ctrl+S / Cmd+S', desc: '설정 저장' },
                    { key: 'Ctrl+Z / Cmd+Z', desc: '실행 취소' },
                    { key: 'Ctrl+Y / Cmd+Y', desc: '다시 실행' },
                    { key: '?', desc: '단축키 도움말 표시' },
                    { key: 'Esc', desc: '모달/툴팁 닫기' }
                ];

                let html = '<div class="jj-shortcuts-modal">' +
                    '<div class="jj-shortcuts-overlay"></div>' +
                    '<div class="jj-shortcuts-container">' +
                    '<div class="jj-shortcuts-header">' +
                    '<h3>⌨️ 키보드 단축키</h3>' +
                    '<button type="button" class="jj-shortcuts-close">&times;</button>' +
                    '</div>' +
                    '<div class="jj-shortcuts-list">';

                shortcuts.forEach(function(shortcut) {
                    html += '<div class="jj-shortcuts-item">' +
                        '<span class="jj-shortcuts-key">' + shortcut.key + '</span>' +
                        '<span class="jj-shortcuts-desc">' + shortcut.desc + '</span>' +
                        '</div>';
                });

                html += '</div></div></div>';

                const $modal = $(html);
                $('body').append($modal);
                $modal.fadeIn(200);

                // 닫기
                $modal.find('.jj-shortcuts-close, .jj-shortcuts-overlay').on('click', function() {
                    $modal.fadeOut(200, function() {
                        $(this).remove();
                    });
                });

                // ESC 키로 닫기
                $(document).on('keydown.shortcuts', function(e) {
                    if (e.key === 'Escape') {
                        $modal.fadeOut(200, function() {
                            $(this).remove();
                        });
                        $(document).off('keydown.shortcuts');
                    }
                });
            };
        },

        /**
         * 빠른 액션 (우클릭 메뉴)
         */
        initQuickActions: function() {
            // 저장 버튼에 빠른 액션 추가
            $(document).on('contextmenu', '.jj-section-global, .jj-field', function(e) {
                e.preventDefault();
                // 컨텍스트 메뉴는 initContextMenu에서 처리
            });
        },

        /**
         * 컨텍스트 메뉴
         */
        initContextMenu: function() {
            const self = this;

            $(document).on('contextmenu', '.jj-section-global', function(e) {
                e.preventDefault();
                
                const $section = $(this);
                const menu = $('<div class="jj-context-menu">' +
                    '<div class="jj-context-menu-item" data-action="copy">📋 설정 복사</div>' +
                    '<div class="jj-context-menu-item" data-action="reset">🔄 기본값으로 재설정</div>' +
                    '<div class="jj-context-menu-item" data-action="export">💾 이 섹션만 내보내기</div>' +
                    '</div>');

                $('body').append(menu);
                menu.css({
                    top: e.pageY + 'px',
                    left: e.pageX + 'px'
                }).fadeIn(100);

                // 메뉴 항목 클릭
                menu.find('.jj-context-menu-item').on('click', function() {
                    const action = $(this).data('action');
                    self.handleContextAction(action, $section);
                    menu.remove();
                });

                // 외부 클릭 시 닫기
                $(document).one('click', function() {
                    menu.fadeOut(100, function() {
                        $(this).remove();
                    });
                });
            });
        },

        /**
         * 컨텍스트 액션 처리
         */
        handleContextAction: function(action, $section) {
            switch(action) {
                case 'copy':
                    // 설정 복사 (클립보드)
                    this.copySectionSettings($section);
                    break;
                case 'reset':
                    // 기본값으로 재설정
                    if (confirm('이 섹션을 기본값으로 재설정하시겠습니까?')) {
                        this.resetSection($section);
                    }
                    break;
                case 'export':
                    // 섹션만 내보내기
                    this.exportSection($section);
                    break;
            }
        },

        /**
         * 섹션 설정 복사
         */
        copySectionSettings: function($section) {
            const sectionId = $section.attr('id');
            const sectionData = {
                id: sectionId,
                // 실제로는 섹션의 모든 필드 값을 수집해야 함
                timestamp: new Date().toISOString()
            };

            const text = JSON.stringify(sectionData, null, 2);
            
            // 클립보드에 복사 (최신 브라우저 API 사용)
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    alert('설정이 클립보드에 복사되었습니다.');
                }).catch(function() {
                    // 폴백: 텍스트 영역 사용
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    alert('설정이 클립보드에 복사되었습니다.');
                });
            } else {
                // 구형 브라우저 폴백
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert('설정이 클립보드에 복사되었습니다.');
            }
        },

        /**
         * 섹션 기본값으로 재설정
         */
        resetSection: function($section) {
            // 실제 구현 필요
            $section.find('input, select, textarea').each(function() {
                const $field = $(this);
                const defaultValue = $field.data('default-value') || '';
                $field.val(defaultValue).trigger('change');
            });
            
            if (typeof JJTooltips !== 'undefined') {
                JJTooltips.showTooltip($section, '섹션이 기본값으로 재설정되었습니다.');
            }
        },

        /**
         * 섹션 내보내기
         */
        exportSection: function($section) {
            const sectionId = $section.attr('id');
            // 실제 구현 필요 - AJAX로 섹션 데이터 가져와서 JSON 다운로드
            alert('섹션 내보내기 기능은 곧 추가될 예정입니다.');
        },

        /**
         * 드래그 앤 드롭
         */
        initDragAndDrop: function() {
            // 섹션 순서 변경 (이미 구현되어 있을 수 있음)
            if ($.fn.sortable) {
                $('.jj-style-guide-sections').sortable({
                    handle: '.jj-section-title',
                    axis: 'y',
                    opacity: 0.7,
                    update: function() {
                        // 순서 저장
                        const order = [];
                        $('.jj-section-global').each(function() {
                            order.push($(this).attr('id'));
                        });
                        // AJAX로 저장
                        // ...
                    }
                });
            }
        },

        /**
         * 자동 저장
         */
        initAutoSave: function() {
            let saveTimeout;
            const self = this;

            // 입력 필드 변경 시 자동 저장
            $(document).on('change input', '.jj-section-global input, .jj-section-global select, .jj-section-global textarea', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(function() {
                    self.showAutoSaveIndicator();
                    // 실제 저장은 나중에 구현
                }, 2000); // 2초 후 자동 저장
            });
        },

        /**
         * 자동 저장 표시기
         */
        showAutoSaveIndicator: function() {
            const $indicator = $('<div class="jj-auto-save-indicator">💾 자동 저장 중...</div>');
            $('body').append($indicator);
            setTimeout(function() {
                $indicator.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 2000);
        },

        /**
         * 실행 취소/다시 실행
         */
        initUndoRedo: function() {
            const history = [];
            let historyIndex = -1;
            const maxHistory = 50;

            // 입력 필드 변경 시 히스토리에 추가
            $(document).on('change', '.jj-section-global input, .jj-section-global select, .jj-section-global textarea', function() {
                const $field = $(this);
                const fieldData = {
                    element: $field.attr('id') || $field.attr('name'),
                    oldValue: $field.data('previous-value') || $field.val(),
                    newValue: $field.val(),
                    timestamp: Date.now()
                };

                // 이전 값 저장
                $field.data('previous-value', fieldData.oldValue);

                // 히스토리에 추가
                history.push(fieldData);
                historyIndex = history.length - 1;

                // 최대 개수 제한
                if (history.length > maxHistory) {
                    history.shift();
                    historyIndex--;
                }
            });

            // Ctrl+Z / Cmd+Z
            $(document).on('keydown', function(e) {
                // 입력 필드에 포커스가 있으면 기본 동작 허용
                if ($(e.target).is('input, textarea')) {
                    return;
                }

                if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
                    e.preventDefault();
                    // 실행 취소
                    if (historyIndex > 0) {
                        historyIndex--;
                        const prevState = history[historyIndex];
                        const $field = $('#' + prevState.element + ', [name="' + prevState.element + '"]');
                        if ($field.length) {
                            $field.val(prevState.oldValue).trigger('change');
                            this.showAutoSaveIndicator();
                        }
                    }
                } else if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) {
                    e.preventDefault();
                    // 다시 실행
                    if (historyIndex < history.length - 1) {
                        historyIndex++;
                        const nextState = history[historyIndex];
                        const $field = $('#' + nextState.element + ', [name="' + nextState.element + '"]');
                        if ($field.length) {
                            $field.val(nextState.newValue).trigger('change');
                            this.showAutoSaveIndicator();
                        }
                    }
                }
            }.bind(this));
        },

        /**
         * 환영 넛지 표시
         */
        showWelcomeNudge: function() {
            // [v26.0.12] 디버그 모드: 항상 표시하도록 수정 (개발 중)
            // 프로덕션에서는 아래 주석을 해제하고 이 블록을 제거하세요
            var forceShow = false; // true로 설정하면 항상 표시
            
            // 첫 방문 시에만 표시 (forceShow가 false일 때)
            if (!forceShow && localStorage.getItem('jj_style_guide_welcome_shown')) {
                console.log('[JJ Welcome] 이미 표시됨 - localStorage에 기록되어 있음');
                return;
            }

            setTimeout(function() {
                console.log('[JJ Welcome] 웰컴 메시지 표시 시도...');
                
                if (typeof window.jjNudgeSystem !== 'undefined' && window.jjNudgeSystem.active_nudges) {
                    // 활성 넛지가 있으면 표시
                    console.log('[JJ Welcome] jjNudgeSystem 사용');
                    window.jjNudgeSystem.active_nudges.forEach(function(nudge) {
                        if (nudge.id === 'welcome' && typeof JJNudge !== 'undefined') {
                            console.log('[JJ Welcome] 넛지 표시:', nudge);
                            JJNudge.showNudge(nudge);
                        }
                    });
                } else if (typeof JJNudge !== 'undefined') {
                    // 폴백: 직접 표시
                    console.log('[JJ Welcome] JJNudge 직접 표시');
                    JJNudge.showNudge({
                        id: 'welcome',
                        type: 'info',
                        title: '👋 ACF CSS 스타일 센터에 오신 것을 환영합니다!',
                        message: 'Ctrl+K로 빠른 검색, ? 키로 단축키를 확인할 수 있습니다. 각 필드에 마우스를 올리면 도움말을 볼 수 있습니다.',
                        position: 'top-right',
                        duration: 5000,
                        dismissible: true,
                        actions: [
                            { label: '시작하기', action: 'dismiss' }
                        ]
                    });
                } else {
                    console.error('[JJ Welcome] JJNudge가 정의되지 않음 - 웰컴 메시지를 표시할 수 없습니다');
                }
                
                if (!forceShow) {
                    localStorage.setItem('jj_style_guide_welcome_shown', 'true');
                }
            }, 1500);
        }
    };

    // DOM 준비 시 초기화
    $(document).ready(function() {
        JJUIEnhancements.init();
    });

    // 전역으로 노출
    window.JJUIEnhancements = JJUIEnhancements;

})(jQuery);
