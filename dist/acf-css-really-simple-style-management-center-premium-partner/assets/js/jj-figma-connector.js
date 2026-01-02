/**
 * JJ Figma Connector
 * [Phase 13] Figma API 연동 UI
 */
(function($) {
    'use strict';

    var ajaxUrl = '';
    var nonce = '';
    var importedTokens = null;

    function init() {
        if (typeof jjFigmaConnector !== 'undefined') {
            ajaxUrl = jjFigmaConnector.ajax_url || '';
            nonce = jjFigmaConnector.nonce || '';
        }

        if (!ajaxUrl || !nonce) {
            return;
        }

        bindEvents();
        loadSettings();
    }

    function bindEvents() {
        // 연결 테스트
        $(document).on('click', '#jj-figma-test-connection', function() {
            testConnection();
        });

        // 설정 저장
        $(document).on('click', '#jj-figma-save-settings', function() {
            saveSettings();
        });

        // 토큰 가져오기
        $(document).on('click', '#jj-figma-import-tokens', function() {
            importTokens();
        });

        // 토큰 내보내기
        $(document).on('click', '#jj-figma-export-tokens', function() {
            exportTokens();
        });

        // 토큰 적용
        $(document).on('click', '#jj-figma-apply-tokens', function() {
            applyTokens();
        });
    }

    function setStatus(msg, type) {
        var $status = $('#jj-figma-connection-status');
        var bgColor = '#f0f0f1';
        var textColor = '#1d2327';
        var borderColor = '#c3c4c7';

        if (type === 'success') {
            bgColor = '#d4edda';
            textColor = '#155724';
            borderColor = '#c3e6cb';
        } else if (type === 'error') {
            bgColor = '#f8d7da';
            textColor = '#721c24';
            borderColor = '#f5c6cb';
        } else if (type === 'loading') {
            bgColor = '#fff3cd';
            textColor = '#856404';
            borderColor = '#ffeeba';
        }

        $status.html('<div style="padding: 10px 14px; border-radius: 8px; background: ' + bgColor + '; color: ' + textColor + '; border: 1px solid ' + borderColor + ';">' + escapeHtml(msg) + '</div>');
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function loadSettings() {
        $.post(ajaxUrl, {
            action: 'jj_figma_get_settings',
            nonce: nonce
        }).done(function(resp) {
            if (resp && resp.success && resp.data) {
                var settings = resp.data;
                if (settings.file_key) {
                    $('#jj-figma-file-key').val(settings.file_key);
                }
                if (settings.has_api_token) {
                    $('#jj-figma-api-token').attr('placeholder', '(저장된 토큰 있음)');
                }
                if (settings.last_sync) {
                    setStatus('마지막 동기화: ' + settings.last_sync, 'success');
                }
            }
        });
    }

    function testConnection() {
        var token = $('#jj-figma-api-token').val().trim();
        
        setStatus('연결 테스트 중...', 'loading');

        $.post(ajaxUrl, {
            action: 'jj_figma_test_connection',
            nonce: nonce,
            api_token: token
        }).done(function(resp) {
            if (resp && resp.success && resp.data) {
                var user = resp.data.user || {};
                setStatus('연결 성공! 사용자: ' + (user.handle || user.email || user.id), 'success');
            } else {
                var msg = (resp && resp.data && resp.data.message) ? resp.data.message : '연결 실패';
                setStatus(msg, 'error');
            }
        }).fail(function() {
            setStatus('서버 오류', 'error');
        });
    }

    function saveSettings() {
        var token = $('#jj-figma-api-token').val().trim();
        var fileKey = $('#jj-figma-file-key').val().trim();

        setStatus('저장 중...', 'loading');

        $.post(ajaxUrl, {
            action: 'jj_figma_save_settings',
            nonce: nonce,
            api_token: token,
            file_key: fileKey
        }).done(function(resp) {
            if (resp && resp.success) {
                setStatus('설정이 저장되었습니다.', 'success');
                if (token) {
                    $('#jj-figma-api-token').val('').attr('placeholder', '(저장된 토큰 있음)');
                }
            } else {
                var msg = (resp && resp.data && resp.data.message) ? resp.data.message : '저장 실패';
                setStatus(msg, 'error');
            }
        }).fail(function() {
            setStatus('서버 오류', 'error');
        });
    }

    function importTokens() {
        var fileKey = $('#jj-figma-file-key').val().trim();

        setStatus('스타일 가져오는 중...', 'loading');
        $('#jj-figma-import-preview').hide();

        $.post(ajaxUrl, {
            action: 'jj_figma_import_tokens',
            nonce: nonce,
            file_key: fileKey
        }).done(function(resp) {
            if (resp && resp.success && resp.data) {
                importedTokens = resp.data;
                renderImportPreview(resp.data);
                setStatus('스타일을 가져왔습니다. 파일: ' + (resp.data.file_name || fileKey), 'success');
            } else {
                var msg = (resp && resp.data && resp.data.message) ? resp.data.message : '가져오기 실패';
                setStatus(msg, 'error');
            }
        }).fail(function() {
            setStatus('서버 오류', 'error');
        });
    }

    function renderImportPreview(tokens) {
        var $preview = $('#jj-figma-import-preview');
        var $colors = $('#jj-figma-import-colors');
        var $typo = $('#jj-figma-import-typography');

        // 색상
        if (tokens.colors && tokens.colors.length) {
            var colorsHtml = '<h5>색상 (' + tokens.colors.length + ')</h5>';
            colorsHtml += '<div style="display: flex; flex-wrap: wrap; gap: 10px;">';
            tokens.colors.forEach(function(c) {
                var hex = c.hex || '#ccc';
                colorsHtml += '<div style="text-align: center;">';
                colorsHtml += '<div style="width: 50px; height: 50px; background: ' + escapeHtml(hex) + '; border-radius: 8px; border: 1px solid #ccc;"></div>';
                colorsHtml += '<div style="font-size: 11px; margin-top: 4px;">' + escapeHtml(c.name) + '</div>';
                colorsHtml += '</div>';
            });
            colorsHtml += '</div>';
            $colors.html(colorsHtml);
        } else {
            $colors.html('<p style="color: #888;">색상 스타일 없음</p>');
        }

        // 타이포그래피
        if (tokens.typography && tokens.typography.length) {
            var typoHtml = '<h5>타이포그래피 (' + tokens.typography.length + ')</h5>';
            typoHtml += '<div style="display: grid; gap: 8px;">';
            tokens.typography.forEach(function(t) {
                typoHtml += '<div style="padding: 8px; background: #f0f0f1; border-radius: 6px;">';
                typoHtml += '<strong>' + escapeHtml(t.name) + '</strong>';
                if (t.font_family) typoHtml += '<span style="margin-left: 10px; font-size: 12px; color: #666;">' + escapeHtml(t.font_family) + ', ' + (t.font_weight || 400) + ', ' + (t.font_size || 16) + 'px</span>';
                typoHtml += '</div>';
            });
            typoHtml += '</div>';
            $typo.html(typoHtml);
        } else {
            $typo.html('<p style="color: #888;">텍스트 스타일 없음</p>');
        }

        $preview.show();
        if ((tokens.colors && tokens.colors.length) || (tokens.typography && tokens.typography.length)) {
            $('#jj-figma-apply-tokens').show();
        }
    }

    function applyTokens() {
        if (!importedTokens) {
            alert('가져온 토큰이 없습니다.');
            return;
        }

        // TODO: 실제로 JJ 스타일 옵션에 적용하는 로직
        // 여기서는 사용자에게 안내만 제공
        alert('이 기능은 추후 업데이트에서 제공될 예정입니다.\n\n현재는 가져온 색상/타이포그래피 값을 참고하여 스타일 센터에서 수동으로 입력해 주세요.');
    }

    function exportTokens() {
        setStatus('내보내는 중...', 'loading');

        $.post(ajaxUrl, {
            action: 'jj_figma_export_tokens',
            nonce: nonce
        }).done(function(resp) {
            if (resp && resp.success && resp.data) {
                downloadTextFile(resp.data.content, resp.data.filename, 'application/json');
                setStatus('JSON 파일이 다운로드되었습니다.', 'success');
            } else {
                var msg = (resp && resp.data && resp.data.message) ? resp.data.message : '내보내기 실패';
                setStatus(msg, 'error');
            }
        }).fail(function() {
            setStatus('서버 오류', 'error');
        });
    }

    function downloadTextFile(content, filename, mime) {
        var blob = new Blob([content], { type: mime || 'text/plain' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = filename || 'download.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    $(document).ready(function() {
        // Figma 탭이 있을 때만 초기화
        if ($('[data-tab="figma"]').length || $('#jj-figma-api-token').length) {
            init();
        }
    });

})(jQuery);
