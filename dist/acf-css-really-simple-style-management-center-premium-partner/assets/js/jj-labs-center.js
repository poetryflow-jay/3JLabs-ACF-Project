/**
 * J&J 실험실 센터 JavaScript - v3.7.0
 * 
 * 실험실 센터 전용 JavaScript
 * - 탭 전환 기능
 * - CSS/HTML/JS 스캐너 기능
 * - 수동 재정의 저장 기능
 * - 공식 지원 목록 확장 기능
 * 
 * @since 3.7.0
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const $wrap = $('.jj-labs-center-wrap');
        if (!$wrap.length) {
            return;
        }

        // [v5.0.0] 실험실 탭 활성화/비활성화 관련 UI 개선
        // 전체 탭 선택/해제
        $(document).on('change', '#jj-select-all-labs-tabs', function() {
            const isChecked = $(this).is(':checked');
            $('.jj-labs-tab-enabled-checkbox').prop('checked', isChecked).trigger('change');
            $('.jj-labs-tab-checkbox').prop('checked', isChecked);
            updateLabsWarningMessage();
        });

        // 탭 체크박스와 enabled 체크박스 동기화
        $(document).on('change', '.jj-labs-tab-enabled-checkbox', function() {
            const $checkbox = $(this);
            const $row = $checkbox.closest('.jj-labs-tab-row');
            const tabSlug = $checkbox.data('tab');
            const isChecked = $checkbox.is(':checked');
            
            // [v5.0.0] 확인 다이얼로그 (비활성화 시)
            if (!isChecked) {
                const tabLabel = $row.data('tab-label') || $row.find('th').text().trim();
                if (!confirm('탭 "' + tabLabel + '"을(를) 비활성화하시겠습니까?\n\n비활성화하면 실험실에서 해당 탭이 표시되지 않습니다.')) {
                    $checkbox.prop('checked', true);
                    return;
                }
            }
            
            // 탭 체크박스도 동기화
            $('.jj-labs-tab-checkbox[data-tab="' + tabSlug + '"]').prop('checked', isChecked);
            
            // 시각적 상태 업데이트
            if (isChecked) {
                $row.removeClass('jj-tab-row-disabled');
            } else {
                $row.addClass('jj-tab-row-disabled');
            }
            
            // 전체 선택 체크박스 상태 업데이트
            updateLabsSelectAllCheckbox();
            updateLabsWarningMessage();
        });

        // 탭 체크박스 클릭 시 enabled 체크박스도 동기화
        $(document).on('change', '.jj-labs-tab-checkbox', function() {
            const tabSlug = $(this).data('tab');
            const isChecked = $(this).is(':checked');
            $('.jj-labs-tab-enabled-checkbox[data-tab="' + tabSlug + '"]')
                .prop('checked', isChecked)
                .trigger('change');
        });

        // 전체 선택 체크박스 상태 업데이트 함수
        function updateLabsSelectAllCheckbox() {
            const $allTabCheckboxes = $('.jj-labs-tab-enabled-checkbox');
            const $checkedTabCheckboxes = $allTabCheckboxes.filter(':checked');
            const allChecked = $allTabCheckboxes.length > 0 && 
                             $allTabCheckboxes.length === $checkedTabCheckboxes.length;
            $('#jj-select-all-labs-tabs').prop('checked', allChecked);
        }

        // 경고 메시지 표시/숨김 함수
        function updateLabsWarningMessage() {
            const $disabledTabs = $('.jj-labs-tab-enabled-checkbox:not(:checked)');
            const $warning = $('.jj-labs-tabs-disable-warning');
            
            if ($disabledTabs.length > 0) {
                $warning.slideDown(200);
            } else {
                $warning.slideUp(200);
            }
        }

        // 일괄 작업 버튼 (실험실)
        $(document).on('click', '.jj-bulk-enable-all-labs', function() {
            if (confirm('모든 탭을 활성화하시겠습니까?')) {
                $('.jj-labs-tab-enabled-checkbox').prop('checked', true).trigger('change');
                $('#jj-select-all-labs-tabs').prop('checked', true);
                $('.jj-labs-tab-row').removeClass('jj-tab-row-disabled');
            }
        });
        
        $(document).on('click', '.jj-bulk-disable-all-labs', function() {
            if (confirm('모든 탭을 비활성화하시겠습니까?\n\n주의: 모든 탭이 비활성화되면 실험실에서 아무것도 표시되지 않습니다.')) {
                $('.jj-labs-tab-enabled-checkbox').prop('checked', false).trigger('change');
                $('#jj-select-all-labs-tabs').prop('checked', false);
                $('.jj-labs-tab-row').addClass('jj-tab-row-disabled');
            }
        });
        
        // 검색/필터 기능 (실험실)
        // [v5.0.4] 성능 최적화: 디바운싱 적용
        let labsSearchTimeout = null;
        $(document).on('input', '#jj-labs-tabs-search', function() {
            const $input = $(this);
            const searchTerm = $input.val().toLowerCase().trim();
            
            // [v5.0.4] 디바운싱: 300ms 지연
            clearTimeout(labsSearchTimeout);
            labsSearchTimeout = setTimeout(function() {
                if (searchTerm === '') {
                    $('.jj-labs-tab-row').removeClass('jj-section-row-filtered');
                    return;
                }
                
                // [v5.0.4] 성능 최적화: DOM 조회를 한 번만 수행하고 배치로 처리
                const tabRows = $('.jj-labs-tab-row').toArray();
                const updates = [];
                
                tabRows.forEach(function(row) {
                    const $row = $(row);
                    const tabLabel = $row.data('tab-label') || '';
                    const tabSlug = $row.data('tab-slug') || '';
                    const matches = tabLabel.includes(searchTerm) || tabSlug.includes(searchTerm);
                    updates.push({ $row: $row, matches: matches });
                });
                
                // 배치 업데이트
                updates.forEach(function(item) {
                    if (item.matches) {
                        item.$row.removeClass('jj-section-row-filtered');
                    } else {
                        item.$row.addClass('jj-section-row-filtered');
                    }
                });
            }, 300);
        });
        
        // 페이지 로드 시 초기 상태 확인
        updateLabsSelectAllCheckbox();
        updateLabsWarningMessage();

        // [v6.0.0] 공식 지원 목록 검색(테마/플러그인)
        let supportedSearchTimeout = null;

        function resetSupportedLists() {
            $wrap.find('.jj-labs-supported-item').each(function() {
                const $itemWrap = $(this);
                const $list = $itemWrap.find('.jj-labs-supported-list');
                const initialCount = parseInt($list.data('initial-count') || 6, 10);
                const $items = $list.find('.jj-labs-supported-list-item');

                $items.each(function(idx) {
                    const $li = $(this);
                    if (idx >= initialCount) {
                        $li.addClass('is-hidden').hide();
                    } else {
                        $li.removeClass('is-hidden').show();
                    }
                });

                const hasHidden = $items.length > initialCount;
                $itemWrap.find('.jj-labs-supported-actions').toggle(hasHidden);
                $itemWrap.find('.jj-labs-show-all').hide();
                $itemWrap.find('.jj-labs-show-more').show().data('click-count', 0);
            });
        }

        function filterSupportedLists(term) {
            const q = (term || '').toLowerCase().trim();
            if (!q) {
                resetSupportedLists();
                return;
            }

            $wrap.find('.jj-labs-supported-item').each(function() {
                const $itemWrap = $(this);
                const $list = $itemWrap.find('.jj-labs-supported-list');
                const $items = $list.find('.jj-labs-supported-list-item');

                $items.each(function() {
                    const $li = $(this);
                    const label = ($li.text() || '').toLowerCase();
                    if (label.indexOf(q) !== -1) {
                        $li.removeClass('is-hidden').show();
                    } else {
                        $li.addClass('is-hidden').hide();
                    }
                });

                // 검색 중에는 더보기/전체보기 버튼 숨김 (검색 결과가 곧 전체보기)
                $itemWrap.find('.jj-labs-supported-actions').hide();
            });
        }

        $(document).on('input', '#jj-labs-supported-search', function() {
            const term = $(this).val();
            clearTimeout(supportedSearchTimeout);
            supportedSearchTimeout = setTimeout(function() {
                filterSupportedLists(term);
            }, 200);
        });

        // 최초 상태 세팅
        resetSupportedLists();
        
        // 시각적 상태 초기화
        $('.jj-labs-tab-enabled-checkbox').each(function() {
            const $checkbox = $(this);
            const $row = $checkbox.closest('.jj-labs-tab-row');
            const isChecked = $checkbox.is(':checked');
            
            if (!isChecked) {
                $row.addClass('jj-tab-row-disabled');
            }
        });

        // 탭 전환
        $wrap.on('click', '.jj-labs-center-tabs a', function(e) {
            e.preventDefault();
            const $tab = $(this).closest('li');
            const tabId = $tab.data('tab');

            // 탭 활성화
            $wrap.find('.jj-labs-center-tabs li').removeClass('active');
            $tab.addClass('active');

            // 컨텐츠 전환
            $wrap.find('.jj-labs-center-tab-content').removeClass('active');
            $wrap.find('.jj-labs-center-tab-content[data-tab="' + tabId + '"]').addClass('active');
        });

        // URL 스캔 시작
        $('#jj-labs-start-scan-url').on('click', function() {
            const $button = $(this);
            const $spinner = $('.jj-labs-scan-spinner').first();
            const $urlInput = $('#jj-labs-scan-url');
            const scanUrl = $urlInput.val().trim();

            if (!scanUrl) {
                alert('URL을 입력해주세요.');
                return;
            }

            setSpinnerState($spinner, $button, true);
            showScanResults(null);

            $.ajax({
                url: jjLabsCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_scan_page_for_css',
                    security: jjLabsCenter.nonce,
                    scan_url: scanUrl
                },
                success: function(response) {
                    setSpinnerState($spinner, $button, false);
                    if (response.success) {
                        displayScanResults(response.data);
                    } else {
                        showScanError(response.data?.message || '스캔 중 오류가 발생했습니다.');
                    }
                },
                error: function() {
                    setSpinnerState($spinner, $button, false);
                    showScanError('스캔 요청 중 네트워크 오류가 발생했습니다.');
                }
            });
        });

        // 활성 플러그인/테마 스캔 시작
        $('#jj-labs-start-scan-target').on('click', function() {
            const $button = $(this);
            const $spinner = $('.jj-labs-scan-spinner').last();
            const $select = $('#jj-labs-scan-target');
            const target = $select.val();

            if (!target) {
                alert('대상을 선택해주세요.');
                return;
            }

            setSpinnerState($spinner, $button, true);
            showScanResults(null);

            // 활성 테마/플러그인 스캔 (향후 구현)
            // 현재는 URL 스캔과 동일한 방식으로 처리
            const scanUrl = target === 'theme_active' ? window.location.origin : window.location.origin;
            
            $.ajax({
                url: jjLabsCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_scan_page_for_css',
                    security: jjLabsCenter.nonce,
                    scan_url: scanUrl,
                    scan_target: target
                },
                success: function(response) {
                    setSpinnerState($spinner, $button, false);
                    if (response.success) {
                        displayScanResults(response.data);
                    } else {
                        showScanError(response.data?.message || '스캔 중 오류가 발생했습니다.');
                    }
                },
                error: function() {
                    setSpinnerState($spinner, $button, false);
                    showScanError('스캔 요청 중 네트워크 오류가 발생했습니다.');
                }
            });
        });

        // 수동 재정의 저장
        $('#jj-labs-save-overrides').on('click', function() {
            const $button = $(this);
            const $spinner = $('.jj-labs-save-spinner');
            const $success = $('.jj-labs-save-success');
            const $textarea = $('#jj-labs-override-css');
            const overrideCss = $textarea.val();

            setSpinnerState($spinner, $button, true);
            $success.hide();

            $.ajax({
                url: jjLabsCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_save_style_guide',
                    security: jjLabsCenter.nonce,
                    settings: {
                        labs: {
                            override_css: overrideCss
                        }
                    }
                },
                success: function(response) {
                    setSpinnerState($spinner, $button, false);
                    if (response.success) {
                        $success.text('CSS 재정의가 저장되었습니다.').fadeIn();
                        setTimeout(function() {
                            $success.fadeOut();
                        }, 3000);
                    } else {
                        alert(response.data?.message || '저장 중 오류가 발생했습니다.');
                    }
                },
                error: function() {
                    setSpinnerState($spinner, $button, false);
                    alert('저장 요청 중 네트워크 오류가 발생했습니다.');
                }
            });
        });

        // 더 보기 / 전체 보기 (공식 지원 목록)
        $wrap.on('click', '.jj-labs-show-more, .jj-labs-show-all', function(e) {
            e.preventDefault();
            const $button = $(this);
            const target = $button.data('target');
            
            // 올바른 선택자: .jj-labs-supported-item[data-type="..."] 안의 .jj-labs-supported-list
            const $item = $button.closest('.jj-labs-supported-item[data-type="' + target + '"]');
            const $list = $item.find('.jj-labs-supported-list');
            
            if (!$list.length) {
                console.error('목록을 찾을 수 없습니다:', target);
                return;
            }
            
            const initialCount = parseInt($list.data('initial-count') || 6);
            const isShowAll = $button.hasClass('jj-labs-show-all');
            const $hiddenItems = $list.find('.jj-labs-supported-list-item.is-hidden');
            
            if ($hiddenItems.length === 0) {
                // 더 이상 숨겨진 항목이 없으면 버튼 숨김
                $button.hide();
                $button.siblings('.jj-labs-show-more').hide();
                return;
            }
            
            // 숨겨진 항목들을 표시
            $hiddenItems.each(function(index) {
                const $item = $(this);
                if (isShowAll || index < initialCount) {
                    // is-hidden 클래스 제거 및 표시
                    $item.removeClass('is-hidden');
                    // display: none이면 slideDown이 작동하지 않으므로 먼저 block으로 변경
                    if ($item.css('display') === 'none') {
                        $item.css('display', 'list-item').hide().slideDown(200);
                    } else {
                        $item.slideDown(200);
                    }
                }
            });
            
            // 버튼 상태 업데이트
            const $remainingHidden = $list.find('.jj-labs-supported-list-item.is-hidden');
            
            if ($remainingHidden.length === 0) {
                // 모든 항목이 표시되었으면 모든 버튼 숨김
                $item.find('.jj-labs-show-more, .jj-labs-show-all').hide();
            } else if (isShowAll) {
                // 전체 보기 버튼을 누르면 더 보기 버튼 숨김
                $item.find('.jj-labs-show-more').hide();
            } else {
                // 더 보기 버튼을 누르면 전체 보기 버튼 표시 (2번째 클릭부터)
                const clickCount = parseInt($button.data('click-count') || 0) + 1;
                $button.data('click-count', clickCount);
                if (clickCount >= 2) {
                    $item.find('.jj-labs-show-all').show();
                }
            }
        });

        // 스캔 결과 표시
        function displayScanResults(data) {
            const $results = $('#jj-labs-scan-results');
            const $tabs = $('.jj-labs-results-tabs');

            // CSS 선택자 결과
            if (data.class_selectors && data.class_selectors.length > 0) {
                $('#jj-labs-results-css-classes').val(data.class_selectors.join('\n'));
                $('#jj-labs-css-classes-count').text(' (' + data.class_selectors.length + '개)');
            }

            if (data.id_selectors && data.id_selectors.length > 0) {
                $('#jj-labs-results-css-ids').val(data.id_selectors.join('\n'));
                $('#jj-labs-css-ids-count').text(' (' + data.id_selectors.length + '개)');
            }

            if (data.style_blocks && data.style_blocks.length > 0) {
                $('#jj-labs-results-css-blocks').val(data.style_blocks.join('\n\n---\n\n'));
            }

            // HTML 결과
            if (data.html_snippet) {
                $('#jj-labs-results-html').val(data.html_snippet);
            }

            // JavaScript 결과 (향후 확장)
            $('#jj-labs-results-js').val('JavaScript 스캔 기능은 향후 업데이트에서 제공될 예정입니다.');

            // [v3.7.0 '신규'] 색상/폰트 추출 및 충돌 감지 결과 표시
            const $insights = $('#jj-labs-scan-insights');
            const $colorsDiv = $('#jj-labs-extracted-colors');
            const $fontsDiv = $('#jj-labs-extracted-fonts');
            const $conflictsDiv = $('#jj-labs-conflicts');
            const $colorsList = $('#jj-labs-colors-list');
            const $fontsList = $('#jj-labs-fonts-list');
            const $conflictsList = $('#jj-labs-conflicts-list');
            
            // 색상 표시
            if (data.colors && data.colors.length > 0) {
                $colorsList.empty();
                data.colors.slice(0, 20).forEach(function(color) {
                    const $colorItem = $('<div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 8px; background: #fff; border: 1px solid #c3c4c7; border-radius: 3px; cursor: pointer;" title="클릭하여 색상 피커로 복사"></div>');
                    const $colorSwatch = $('<span style="display: inline-block; width: 20px; height: 20px; background-color: ' + color + '; border: 1px solid #ddd; border-radius: 2px; vertical-align: middle;"></span>');
                    const $colorCode = $('<span style="font-family: monospace; font-size: 12px;">' + color + '</span>');
                    $colorItem.append($colorSwatch, $colorCode);
                    $colorItem.on('click', function() {
                        // 색상 피커로 복사 (향후 구현: 클립보드 복사 또는 색상 피커에 직접 적용)
                        prompt('색상 코드를 복사하세요:', color);
                    });
                    $colorsList.append($colorItem);
                });
                $colorsDiv.show();
                
                // 제안 색상이 있으면 적용 버튼 표시
                if (data.suggestions && data.suggestions.primary_colors && data.suggestions.primary_colors.length > 0) {
                    $('#jj-labs-apply-colors').show().off('click').on('click', function() {
                        if (confirm('상위 2개 색상을 브랜드 팔레트 Primary 색상으로 적용하시겠습니까?')) {
                            // 스타일 센터 페이지로 이동하여 팔레트 적용 (향후 구현)
                            alert('팔레트 적용 기능은 향후 업데이트에서 제공될 예정입니다. 색상 코드: ' + data.suggestions.primary_colors.slice(0, 2).join(', '));
                        }
                    });
                }
            } else {
                $colorsDiv.hide();
            }
            
            // 폰트 표시
            if (data.fonts && data.fonts.length > 0) {
                $fontsList.empty();
                data.fonts.forEach(function(font) {
                    $fontsList.append($('<li style="font-family: ' + font + ', sans-serif; margin: 4px 0;">' + font + '</li>'));
                });
                $fontsDiv.show();
                
                // 제안 폰트가 있으면 적용 버튼 표시
                if (data.suggestions && data.suggestions.font_families && data.suggestions.font_families.length > 0) {
                    $('#jj-labs-apply-fonts').show().off('click').on('click', function() {
                        if (confirm('상위 폰트를 타이포그래피에 적용하시겠습니까?')) {
                            // 스타일 센터 페이지로 이동하여 타이포그래피 적용 (향후 구현)
                            alert('타이포그래피 적용 기능은 향후 업데이트에서 제공될 예정입니다. 폰트: ' + data.suggestions.font_families.join(', '));
                        }
                    });
                }
            } else {
                $fontsDiv.hide();
            }
            
            // 충돌/문제점 표시
            if (data.conflicts && data.conflicts.length > 0) {
                $conflictsList.empty();
                data.conflicts.forEach(function(conflict) {
                    const conflictType = conflict.type || 'info';
                    const $conflictItem = $('<div class="notice notice-' + conflictType + '" style="margin: 8px 0; padding: 10px;"><p style="margin: 0 0 5px 0;"><strong>' + conflict.message + '</strong></p><p style="margin: 0; font-size: 13px;">' + (conflict.suggestion || '') + '</p></div>');
                    $conflictsList.append($conflictItem);
                });
                $conflictsDiv.show();
            } else {
                $conflictsDiv.hide();
            }
            
            // 인사이트 섹션 표시 (색상, 폰트, 충돌 중 하나라도 있으면)
            if ((data.colors && data.colors.length > 0) || (data.fonts && data.fonts.length > 0) || (data.conflicts && data.conflicts.length > 0)) {
                $insights.show();
            } else {
                $insights.hide();
            }

            // 결과 탭 표시
            $results.find('.jj-labs-results-empty').hide();
            $tabs.show();
        }

        // 스캔 오류 표시
        function showScanError(message) {
            const $results = $('#jj-labs-scan-results');
            $results.find('.jj-labs-results-empty').html(
                '<p class="description" style="color: #d63638;">' + message + '</p>'
            );
            $results.find('.jj-labs-results-tabs').hide();
        }

        // 스캔 결과 초기화
        function showScanResults(data) {
            const $results = $('#jj-labs-scan-results');
            if (!data) {
                $results.find('.jj-labs-results-empty').show();
                $results.find('.jj-labs-results-tabs').hide();
            }
        }

        // 스피너 상태 관리
        function setSpinnerState($spinner, $button, isActive) {
            if (isActive) {
                $spinner.show().addClass('is-active');
                $button.prop('disabled', true);
            } else {
                $spinner.hide().removeClass('is-active');
                $button.prop('disabled', false);
            }
        }

        // 초기화: 첫 번째 탭 활성화
        const $firstTab = $wrap.find('.jj-labs-center-tabs li:first-child');
        if ($firstTab.length) {
            $firstTab.addClass('active');
            const firstTabId = $firstTab.data('tab');
            $wrap.find('.jj-labs-center-tab-content[data-tab="' + firstTabId + '"]').addClass('active');
        }

        // [v3.7.0 '신규'] CSS 에디터 초기화 (WordPress CodeMirror)
        if (window.wp && window.wp.codeEditor) {
            const $textarea = $('#jj-labs-override-css');
            if ($textarea.length) {
                const editorSettings = wp.codeEditor.defaultSettings ? wp.codeEditor.defaultSettings.css : {};
                editorSettings.codemirror = {
                    ...editorSettings.codemirror,
                    mode: 'css',
                    lineNumbers: true,
                    indentUnit: 4,
                    indentWithTabs: false,
                    lineWrapping: true,
                    autoCloseBrackets: true,
                    matchBrackets: true,
                    foldGutter: true,
                    gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
                    extraKeys: {
                        'Ctrl-Space': 'autocomplete',
                        'Ctrl-/': 'toggleComment',
                        'Shift-Ctrl-/': 'toggleComment',
                    },
                    hintOptions: {
                        completeSingle: false,
                        // CSS 변수 자동 완성
                        templates: [
                            'var(--jj-primary-color)',
                            'var(--jj-btn-primary-bg)',
                            'var(--jj-btn-primary-text)',
                            'var(--jj-btn-secondary-bg)',
                            'var(--jj-btn-secondary-text)',
                            'var(--jj-form-input-border)',
                            'var(--jj-form-input-border-radius)',
                            'var(--jj-font-ko-family)',
                            'var(--jj-font-en-family)',
                        ]
                    }
                };

                const editor = wp.codeEditor.initialize($textarea[0], editorSettings);
                
                // 에디터 인스턴스 저장 (나중에 사용 가능)
                $textarea.data('code-editor', editor);
                
                // 실시간 CSS 검증 (간단한 구문 검사)
                editor.codemirror.on('change', function(cm) {
                    const value = cm.getValue();
                    const $errorDiv = $('#jj-labs-css-errors');
                    
                    // 기본적인 CSS 검증 (중괄호 매칭)
                    let openBraces = (value.match(/{/g) || []).length;
                    let closeBraces = (value.match(/}/g) || []).length;
                    
                    if (openBraces !== closeBraces) {
                        if (!$errorDiv.length) {
                            const $errorContainer = $('<div id="jj-labs-css-errors" style="margin-top: 10px; padding: 10px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404;"></div>');
                            $textarea.after($errorContainer);
                        }
                        $('#jj-labs-css-errors').html('<strong>경고:</strong> 중괄호가 일치하지 않습니다. (열림: ' + openBraces + ', 닫힘: ' + closeBraces + ')');
                    } else {
                        $('#jj-labs-css-errors').remove();
                    }
                });
            }
        }
    });

    // [v3.7.0 '신규'] 실험실 센터 설정 내보내기/불러오기
    $(document).on('click', '.jj-export-settings[data-center="labs-center"]', function() {
        const $button = $(this);
        const $spinner = $('<span class="spinner is-active" style="float: none; margin: 0 5px;"></span>');
        
        $button.prop('disabled', true).after($spinner);
        
        // AJAX 요청으로 다운로드 트리거
        const form = $('<form>').attr({
            method: 'POST',
            action: jjLabsCenter.ajax_url || ajaxurl,
            style: 'display: none;'
        });
        
        form.append($('<input>').attr({ type: 'hidden', name: 'action', value: 'jj_export_center_settings' }));
        form.append($('<input>').attr({ type: 'hidden', name: 'security', value: jjLabsCenter.nonce || '' }));
        form.append($('<input>').attr({ type: 'hidden', name: 'center', value: 'labs-center' }));
        
        $('body').append(form);
        form.submit();
        
        setTimeout(function() {
            form.remove();
            $spinner.remove();
            $button.prop('disabled', false);
        }, 1000);
    });

    $(document).on('click', '.jj-import-settings[data-center="labs-center"]', function() {
        const $button = $(this);
        const $fileInput = $('<input>').attr({
            type: 'file',
            accept: '.json',
            style: 'display: none;'
        });

        $fileInput.on('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                return;
            }

            if (!file.name.toLowerCase().endsWith('.json')) {
                alert('JSON 파일만 업로드할 수 있습니다.');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'jj_import_center_settings');
            formData.append('security', jjLabsCenter.nonce || '');
            formData.append('center', 'labs-center');
            formData.append('import_file', file);

            const $spinner = $('<span class="spinner is-active" style="float: none; margin: 0 5px;"></span>');
            $button.prop('disabled', true).after($spinner);

            $.ajax({
                url: jjLabsCenter.ajax_url || ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $spinner.remove();
                    $button.prop('disabled', false);
                    
                    if (response.success) {
                        alert(response.data.message || '설정이 성공적으로 불러와졌습니다.');
                        location.reload();
                    } else {
                        alert(response.data.message || '설정 불러오기에 실패했습니다.');
                    }
                },
                error: function() {
                    $spinner.remove();
                    $button.prop('disabled', false);
                    alert('네트워크 오류가 발생했습니다.');
                }
            });

            $fileInput.remove();
        });

        $('body').append($fileInput);
        $fileInput.trigger('click');
    });

})(jQuery);

