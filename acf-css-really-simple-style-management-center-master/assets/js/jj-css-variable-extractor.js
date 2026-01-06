/**
 * [v25.2.0] CSS Variable Extractor & Manager
 *
 * CSS 변수 자동 추출/생성 시스템 프론트엔드
 */
(function($) {
    'use strict';

    if (typeof jjCSSVariableExtractor === 'undefined') {
        return;
    }

    var config = jjCSSVariableExtractor;
    var currentVariables = {};
    var extractedVariables = {};

    /**
     * 초기화
     */
    function init() {
        bindEvents();
        loadCurrentVariables();
    }

    /**
     * 이벤트 바인딩
     */
    function bindEvents() {
        // 서브탭 전환
        $(document).on('click', '.jj-css-vars-tab', function() {
            var subtab = $(this).data('subtab');
            switchSubtab(subtab);
        });

        // 카테고리 필터
        $('#jj-css-vars-category-filter').on('change', function() {
            filterVariables($(this).val());
        });

        // 검색
        $('#jj-css-vars-search').on('input', debounce(function() {
            searchVariables($(this).val());
        }, 300));

        // 새로고침
        $('#jj-css-vars-refresh').on('click', function() {
            loadCurrentVariables();
        });

        // 빠른 복사 버튼
        $(document).on('click', '[data-copy-format]', function() {
            var format = $(this).data('copy-format');
            quickCopy(format);
        });

        // 추출 소스 변경
        $('input[name="jj_extract_source"]').on('change', function() {
            var source = $(this).val();
            $('.jj-css-vars-extract-input').hide();
            $('.jj-css-vars-extract-input[data-source="' + source + '"]').show();
        });

        // 추출 버튼
        $('#jj-btn-extract').on('click', extractVariables);

        // 추출된 변수 적용
        $('#jj-btn-apply-extracted').on('click', applyExtractedVariables);

        // 내보내기
        $('#jj-btn-export').on('click', exportVariables);
        $('#jj-btn-copy-export').on('click', copyExportOutput);
        $('#jj-btn-download-export').on('click', downloadExportOutput);

        // 가져오기
        $('#jj-btn-import-preview').on('click', previewImport);
        $('#jj-btn-import-apply').on('click', applyImport);

        // 변수 클릭 시 복사
        $(document).on('click', '.jj-css-var-item', function() {
            var varName = $(this).data('var-name');
            var varValue = $(this).data('var-value');
            copyToClipboard(varName + ': ' + varValue + ';');
            showNotice(config.strings.copy_success, 'success');
        });
    }

    /**
     * 서브탭 전환
     */
    function switchSubtab(subtab) {
        $('.jj-css-vars-tab').removeClass('active');
        $('.jj-css-vars-tab[data-subtab="' + subtab + '"]').addClass('active');

        $('.jj-css-vars-panel').removeClass('active');
        $('.jj-css-vars-panel[data-panel="' + subtab + '"]').addClass('active');
    }

    /**
     * 현재 변수 로드
     */
    function loadCurrentVariables() {
        var $list = $('#jj-css-vars-list');
        $list.html('<div class="jj-css-vars-loading"><span class="spinner is-active"></span> ' + config.strings.extracting + '</div>');

        $.ajax({
            url: config.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_get_current_variables',
                nonce: config.nonce
            },
            success: function(response) {
                if (response.success) {
                    currentVariables = response.data.variables;
                    renderVariablesList($list, currentVariables);
                    $('#jj-css-vars-total').text(response.data.count);
                } else {
                    $list.html('<div class="jj-css-vars-error">' + (response.data.message || 'Error') + '</div>');
                }
            },
            error: function() {
                $list.html('<div class="jj-css-vars-error">Network error</div>');
            }
        });
    }

    /**
     * 변수 목록 렌더링
     */
    function renderVariablesList($container, variables, options) {
        options = options || {};
        var html = '';
        var grouped = {};

        // 카테고리별 그룹화
        $.each(variables, function(name, data) {
            var category = data.category || 'other';
            if (!grouped[category]) {
                grouped[category] = {};
            }
            grouped[category][name] = data;
        });

        // 카테고리 순서
        var categoryOrder = ['colors', 'typography', 'buttons', 'forms', 'system', 'spacing', 'other'];

        $.each(categoryOrder, function(i, category) {
            if (!grouped[category]) return;

            var catLabel = config.categories[category] ? config.categories[category].label : category;

            html += '<div class="jj-css-vars-category" data-category="' + category + '">';
            html += '<h5 class="jj-css-vars-category-title">' + escapeHtml(catLabel) + ' <span class="count">(' + Object.keys(grouped[category]).length + ')</span></h5>';
            html += '<div class="jj-css-vars-category-items">';

            $.each(grouped[category], function(name, data) {
                var value = data.value || '';
                var label = data.label || name;
                var isColor = isColorValue(value);

                html += '<div class="jj-css-var-item' + (isColor ? ' has-color-preview' : '') + '" data-var-name="' + escapeHtml(name) + '" data-var-value="' + escapeHtml(value) + '" data-category="' + category + '">';

                if (isColor) {
                    html += '<span class="jj-css-var-color-preview" style="background-color: ' + escapeHtml(value) + ';"></span>';
                }

                html += '<span class="jj-css-var-name">' + escapeHtml(name) + '</span>';
                html += '<span class="jj-css-var-value">' + escapeHtml(value) + '</span>';

                if (options.showSource && data.source_file) {
                    html += '<span class="jj-css-var-source">' + escapeHtml(data.source_file) + '</span>';
                }

                html += '<span class="jj-css-var-copy-hint"><span class="dashicons dashicons-clipboard"></span></span>';
                html += '</div>';
            });

            html += '</div></div>';
        });

        if (html === '') {
            html = '<div class="jj-css-vars-empty">' + config.strings.no_variables + '</div>';
        }

        $container.html(html);
    }

    /**
     * 카테고리 필터
     */
    function filterVariables(category) {
        if (category === 'all') {
            $('.jj-css-vars-category').show();
        } else {
            $('.jj-css-vars-category').hide();
            $('.jj-css-vars-category[data-category="' + category + '"]').show();
        }
    }

    /**
     * 검색
     */
    function searchVariables(query) {
        query = query.toLowerCase().trim();

        if (!query) {
            $('.jj-css-var-item').show();
            $('.jj-css-vars-category').show();
            return;
        }

        $('.jj-css-var-item').each(function() {
            var $item = $(this);
            var varName = ($item.data('var-name') || '').toLowerCase();
            var varValue = ($item.data('var-value') || '').toLowerCase();

            if (varName.indexOf(query) !== -1 || varValue.indexOf(query) !== -1) {
                $item.show();
            } else {
                $item.hide();
            }
        });

        // 빈 카테고리 숨기기
        $('.jj-css-vars-category').each(function() {
            var $cat = $(this);
            var visibleItems = $cat.find('.jj-css-var-item:visible').length;
            $cat.toggle(visibleItems > 0);
        });
    }

    /**
     * 빠른 복사
     */
    function quickCopy(format) {
        $.ajax({
            url: config.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_export_css_variables',
                nonce: config.nonce,
                format: format
            },
            success: function(response) {
                if (response.success) {
                    copyToClipboard(response.data.output);
                    showNotice(config.strings.copy_success, 'success');
                }
            }
        });
    }

    /**
     * 변수 추출
     */
    function extractVariables() {
        var source = $('input[name="jj_extract_source"]:checked').val();
        var $btn = $('#jj-btn-extract');
        var $result = $('#jj-extract-result');

        $btn.prop('disabled', true).find('.dashicons').removeClass('dashicons-search').addClass('dashicons-update spin');

        var data = {
            action: source === 'theme' ? 'jj_scan_theme_variables' : 'jj_extract_css_variables',
            nonce: config.nonce
        };

        if (source === 'url') {
            data.url = $('#jj-extract-url').val();
        } else if (source === 'css') {
            data.css = $('#jj-extract-css').val();
        }

        $.ajax({
            url: config.ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
                $btn.prop('disabled', false).find('.dashicons').removeClass('dashicons-update spin').addClass('dashicons-search');

                if (response.success) {
                    extractedVariables = response.data.variables;
                    $result.show();
                    $result.find('.jj-extract-count').text('(' + response.data.count + '개)');
                    renderVariablesList($('#jj-extracted-vars-list'), extractedVariables, { showSource: true });
                    showNotice(config.strings.extracted, 'success');
                } else {
                    showNotice(response.data.message || 'Error', 'error');
                }
            },
            error: function() {
                $btn.prop('disabled', false).find('.dashicons').removeClass('dashicons-update spin').addClass('dashicons-search');
                showNotice('Network error', 'error');
            }
        });
    }

    /**
     * 추출된 변수 적용
     */
    function applyExtractedVariables() {
        if (!confirm(config.strings.confirm_import)) {
            return;
        }

        var merge = $('#jj-extract-merge').is(':checked');

        $.ajax({
            url: config.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_import_css_variables',
                nonce: config.nonce,
                content: JSON.stringify({ variables: extractedVariables }),
                format: 'json',
                apply: 'true',
                merge: merge ? 'true' : 'false'
            },
            success: function(response) {
                if (response.success) {
                    showNotice(config.strings.imported, 'success');
                    loadCurrentVariables();
                } else {
                    showNotice(response.data.message || 'Error', 'error');
                }
            }
        });
    }

    /**
     * 내보내기
     */
    function exportVariables() {
        var format = $('input[name="jj_export_format"]:checked').val();
        var $btn = $('#jj-btn-export');
        var $result = $('#jj-export-result');

        $btn.prop('disabled', true);

        $.ajax({
            url: config.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_export_css_variables',
                nonce: config.nonce,
                format: format
            },
            success: function(response) {
                $btn.prop('disabled', false);

                if (response.success) {
                    $result.show();
                    $('#jj-export-output').val(response.data.output);
                    showNotice(config.strings.exported, 'success');
                } else {
                    showNotice(response.data.message || 'Error', 'error');
                }
            },
            error: function() {
                $btn.prop('disabled', false);
                showNotice('Network error', 'error');
            }
        });
    }

    /**
     * 내보내기 결과 복사
     */
    function copyExportOutput() {
        var output = $('#jj-export-output').val();
        copyToClipboard(output);
        showNotice(config.strings.copy_success, 'success');
    }

    /**
     * 내보내기 결과 다운로드
     */
    function downloadExportOutput() {
        var output = $('#jj-export-output').val();
        var format = $('input[name="jj_export_format"]:checked').val();

        var ext = format;
        if (format === 'design-tokens') ext = 'tokens.json';

        var filename = 'acf-css-variables.' + ext;
        var blob = new Blob([output], { type: 'text/plain;charset=utf-8' });

        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    }

    /**
     * 가져오기 미리보기
     */
    function previewImport() {
        var content = $('#jj-import-content').val();

        if (!content.trim()) {
            showNotice('Content is empty', 'error');
            return;
        }

        $.ajax({
            url: config.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_import_css_variables',
                nonce: config.nonce,
                content: content,
                format: 'auto',
                apply: 'false'
            },
            success: function(response) {
                if (response.success) {
                    var $result = $('#jj-import-preview-result');
                    $result.show();
                    $result.find('.jj-import-count').text('(' + response.data.count + '개)');
                    renderVariablesList($('#jj-import-vars-list'), response.data.variables);
                } else {
                    showNotice(response.data.message || 'Error', 'error');
                }
            }
        });
    }

    /**
     * 가져오기 적용
     */
    function applyImport() {
        var content = $('#jj-import-content').val();
        var merge = $('#jj-import-merge').is(':checked');
        var preview = $('#jj-import-preview').is(':checked');

        if (!content.trim()) {
            showNotice('Content is empty', 'error');
            return;
        }

        if (preview && $('#jj-import-preview-result').is(':hidden')) {
            previewImport();
            return;
        }

        if (!confirm(config.strings.confirm_import)) {
            return;
        }

        $.ajax({
            url: config.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_import_css_variables',
                nonce: config.nonce,
                content: content,
                format: 'auto',
                apply: 'true',
                merge: merge ? 'true' : 'false'
            },
            success: function(response) {
                if (response.success) {
                    showNotice(config.strings.imported, 'success');
                    loadCurrentVariables();
                    switchSubtab('current');
                } else {
                    showNotice(response.data.message || 'Error', 'error');
                }
            }
        });
    }

    // ========== 유틸리티 함수 ==========

    /**
     * 클립보드 복사
     */
    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text);
        } else {
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
        }
    }

    /**
     * 색상 값인지 확인
     */
    function isColorValue(value) {
        if (!value) return false;
        value = value.toLowerCase().trim();
        return /^#[0-9a-f]{3,8}$/i.test(value) ||
               /^rgba?\(/i.test(value) ||
               /^hsla?\(/i.test(value) ||
               /^(transparent|currentColor|inherit)$/i.test(value);
    }

    /**
     * HTML 이스케이프
     */
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /**
     * 알림 표시
     */
    function showNotice(message, type) {
        type = type || 'info';

        // 기존 알림 제거
        $('.jj-css-vars-notice').remove();

        var $notice = $('<div class="jj-css-vars-notice jj-css-vars-notice-' + type + '">' + escapeHtml(message) + '</div>');
        $('[data-tab="css-variables"]').prepend($notice);

        setTimeout(function() {
            $notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    /**
     * 디바운스
     */
    function debounce(func, wait) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }

    // 초기화
    $(document).ready(init);

})(jQuery);
