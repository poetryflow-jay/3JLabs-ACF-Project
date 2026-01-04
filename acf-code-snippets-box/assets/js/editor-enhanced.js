/**
 * ACF Code Snippets Box - Enhanced Code Editor
 *
 * 고급 코드 에디터 기능: 문법 강조, 자동완성, 실시간 린팅
 *
 * @package ACF_Code_Snippets_Box
 * @since 4.1.0
 */

(function($) {
    'use strict';

    // 전역 네임스페이스
    window.ACF_CSB_Editor = window.ACF_CSB_Editor || {};

    /**
     * 에디터 인스턴스
     */
    let editor = null;
    let lintTimeout = null;
    let autoSaveTimeout = null;

    /**
     * 코드 타입별 모드 매핑
     */
    const MODE_MAP = {
        'css': { name: 'text/css', lint: true },
        'js': { name: 'javascript', lint: true },
        'php': { name: 'application/x-httpd-php', lint: false },
        'html': { name: 'htmlmixed', lint: true }
    };

    /**
     * 코드 타입별 아이콘 및 색상
     */
    const TYPE_STYLES = {
        'css': { icon: '🎨', color: '#264de4', bg: '#e7f1ff' },
        'js': { icon: '⚡', color: '#f0db4f', bg: '#fffef0' },
        'php': { icon: '🐘', color: '#777bb4', bg: '#f5f3ff' },
        'html': { icon: '📄', color: '#e34c26', bg: '#fff5f0' }
    };

    /**
     * 초기화
     */
    function init() {
        initCodeEditor();
        initToolbar();
        initTriggerToggles();
        initKeyboardShortcuts();
        initAutoSave();
        loadPresetIfExists();
        initMiniMap();
    }

    /**
     * 고급 코드 에디터 초기화
     */
    function initCodeEditor() {
        const textarea = document.getElementById('acf_csb_code');
        if (!textarea) return;

        const codeType = $('#acf_csb_code_type').val() || 'css';
        const modeConfig = MODE_MAP[codeType] || MODE_MAP.css;

        // CodeMirror 설정
        const settings = window.acfCsbEditor?.codeEditorSettings || {};

        settings.codemirror = Object.assign({}, settings.codemirror || {}, {
            mode: modeConfig.name,
            theme: 'material-darker',
            lineNumbers: true,
            lineWrapping: true,
            indentUnit: 4,
            tabSize: 4,
            indentWithTabs: false,
            matchBrackets: true,
            autoCloseBrackets: true,
            autoCloseTags: true,
            foldGutter: true,
            gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter', 'CodeMirror-lint-markers'],
            styleActiveLine: true,
            highlightSelectionMatches: { showToken: /\w/, annotateScrollbar: true },
            scrollbarStyle: 'overlay',
            extraKeys: {
                'Ctrl-Space': 'autocomplete',
                'Ctrl-S': saveSnippet,
                'Ctrl-/': 'toggleComment',
                'Ctrl-D': duplicateLine,
                'Ctrl-Shift-D': deleteLine,
                'Ctrl-F': 'findPersistent',
                'Ctrl-H': 'replace',
                'Ctrl-G': 'jumpToLine',
                'Alt-F': 'findPersistent',
                'Ctrl-[': 'indentLess',
                'Ctrl-]': 'indentMore',
                'F11': toggleFullscreen,
                'Esc': exitFullscreen,
                'Ctrl-Shift-F': formatCode
            }
        });

        // 에디터 초기화
        if (typeof wp !== 'undefined' && wp.codeEditor) {
            editor = wp.codeEditor.initialize(textarea, settings);

            // 추가 설정 적용
            const cm = editor.codemirror;

            // 변경 감지
            cm.on('change', debounce(function() {
                updateStatusBar();
                scheduleLint();
                scheduleAutoSave();
                scheduleQualityCheck(); // [v4.2.0] 품질 점수 자동 업데이트
            }, 300));

            // 커서 이동 감지
            cm.on('cursorActivity', updateCursorPosition);

            // 포커스 이벤트
            cm.on('focus', function() {
                $('.acf-csb-editor-container').addClass('focused');
            });
            cm.on('blur', function() {
                $('.acf-csb-editor-container').removeClass('focused');
            });

            // 초기 상태 업데이트
            updateStatusBar();
            updateCodeTypeIndicator(codeType);
        }

        // 코드 타입 변경 감지
        $('#acf_csb_code_type').on('change', function() {
            const newType = $(this).val();
            const newMode = MODE_MAP[newType] || MODE_MAP.css;

            if (editor && editor.codemirror) {
                editor.codemirror.setOption('mode', newMode.name);
                updateCodeTypeIndicator(newType);
            }
        });
    }

    /**
     * 툴바 초기화
     */
    function initToolbar() {
        // 툴바가 이미 있으면 건너뛰기
        if ($('.acf-csb-editor-toolbar').length) return;

        const toolbar = $(`
            <div class="acf-csb-editor-toolbar" style="
                display: flex;
                gap: 8px;
                padding: 10px 15px;
                background: linear-gradient(135deg, #1e1e1e 0%, #2d2d2d 100%);
                border-radius: 8px 8px 0 0;
                border-bottom: 1px solid #3c3c3c;
                flex-wrap: wrap;
                align-items: center;
            ">
                <div class="toolbar-group" style="display: flex; gap: 4px;">
                    <button type="button" class="toolbar-btn" data-action="undo" title="실행 취소 (Ctrl+Z)">↩</button>
                    <button type="button" class="toolbar-btn" data-action="redo" title="다시 실행 (Ctrl+Y)">↪</button>
                </div>
                <div class="toolbar-separator" style="width: 1px; height: 20px; background: #444;"></div>
                <div class="toolbar-group" style="display: flex; gap: 4px;">
                    <button type="button" class="toolbar-btn" data-action="format" title="코드 정리 (Ctrl+Shift+F)">🔧</button>
                    <button type="button" class="toolbar-btn" data-action="comment" title="주석 토글 (Ctrl+/)">💬</button>
                    <button type="button" class="toolbar-btn" data-action="fold-all" title="모두 접기">📁</button>
                    <button type="button" class="toolbar-btn" data-action="unfold-all" title="모두 펼치기">📂</button>
                </div>
                <div class="toolbar-separator" style="width: 1px; height: 20px; background: #444;"></div>
                <div class="toolbar-group" style="display: flex; gap: 4px;">
                    <button type="button" class="toolbar-btn" data-action="find" title="찾기 (Ctrl+F)">🔍</button>
                    <button type="button" class="toolbar-btn" data-action="replace" title="바꾸기 (Ctrl+H)">🔄</button>
                    <button type="button" class="toolbar-btn" data-action="goto" title="줄 이동 (Ctrl+G)">📍</button>
                </div>
                <div class="toolbar-separator" style="width: 1px; height: 20px; background: #444;"></div>
                <div class="toolbar-group" style="display: flex; gap: 4px;">
                    <button type="button" class="toolbar-btn" data-action="analyze" title="코드 분석 (Ctrl+Shift+A)">🔍</button>
                    <button type="button" class="toolbar-btn" data-action="optimize" title="코드 최적화 (Ctrl+Shift+O)">⚡</button>
                </div>
                <div class="toolbar-separator" style="width: 1px; height: 20px; background: #444;"></div>
                <div class="toolbar-group" style="display: flex; gap: 4px;">
                    <button type="button" class="toolbar-btn" data-action="fullscreen" title="전체 화면 (F11)">⛶</button>
                    <button type="button" class="toolbar-btn" data-action="minimap" title="미니맵 토글">🗺</button>
                </div>
                <div style="flex: 1;"></div>
                <div class="code-quality-score" style="
                    padding: 4px 12px;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: 600;
                    margin-right: 10px;
                    cursor: pointer;
                " title="코드 품질 점수 클릭하여 상세 분석 보기"></div>
                <div class="code-type-indicator" style="
                    padding: 4px 12px;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: 600;
                    text-transform: uppercase;
                "></div>
            </div>
        `);

        // 스타일 추가
        if (!$('#acf-csb-toolbar-styles').length) {
            $('head').append(`
                <style id="acf-csb-toolbar-styles">
                    .toolbar-btn {
                        padding: 6px 10px;
                        background: #3c3c3c;
                        border: 1px solid #555;
                        border-radius: 4px;
                        color: #ccc;
                        cursor: pointer;
                        font-size: 14px;
                        transition: all 0.2s ease;
                    }
                    .toolbar-btn:hover {
                        background: #4a4a4a;
                        color: #fff;
                        border-color: #667eea;
                    }
                    .toolbar-btn:active {
                        transform: scale(0.95);
                    }
                    .toolbar-btn-primary {
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                        color: #fff !important;
                        border-color: #667eea !important;
                        font-weight: 600;
                    }
                    .toolbar-btn-primary:hover {
                        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%) !important;
                        transform: translateY(-1px);
                        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
                    }
                    .acf-csb-editor-container.fullscreen {
                        position: fixed !important;
                        top: 0 !important;
                        left: 0 !important;
                        right: 0 !important;
                        bottom: 0 !important;
                        z-index: 999999 !important;
                        background: #1e1e1e;
                        padding: 0;
                        margin: 0;
                    }
                    .acf-csb-editor-container.fullscreen .CodeMirror {
                        height: calc(100vh - 100px) !important;
                    }
                    .acf-csb-editor-container.focused .CodeMirror {
                        border-color: #667eea;
                        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
                    }
                </style>
            `);
        }

        // 에디터 래퍼 찾기/생성
        let wrapper = $('.acf-csb-code-editor-wrapper');
        if (!wrapper.length) {
            wrapper = $('#acf_csb_code_editor .inside');
        }

        // 컨테이너로 감싸기
        const container = $('<div class="acf-csb-editor-container"></div>');
        wrapper.wrapInner(container);
        wrapper.find('.acf-csb-editor-container').prepend(toolbar);

        // 상태 바 추가
        const statusBar = $(`
            <div class="acf-csb-status-bar" style="
                display: flex;
                justify-content: space-between;
                padding: 8px 15px;
                background: #2d2d2d;
                border-radius: 0 0 8px 8px;
                border-top: 1px solid #3c3c3c;
                font-size: 12px;
                color: #888;
            ">
                <div class="status-left">
                    <span class="cursor-position">줄 1, 열 1</span>
                    <span class="selection-info" style="margin-left: 15px;"></span>
                </div>
                <div class="status-right">
                    <span class="code-stats"></span>
                    <span class="autosave-status" style="margin-left: 15px; color: #28a745;"></span>
                </div>
            </div>
        `);
        wrapper.find('.acf-csb-editor-container').append(statusBar);

        // 툴바 이벤트
        toolbar.on('click', '.toolbar-btn', function(e) {
            e.preventDefault();
            const action = $(this).data('action');
            handleToolbarAction(action);
        });
    }

    /**
     * 툴바 액션 처리
     */
    function handleToolbarAction(action) {
        if (!editor || !editor.codemirror) return;

        const cm = editor.codemirror;

        switch (action) {
            case 'undo':
                cm.undo();
                break;
            case 'redo':
                cm.redo();
                break;
            case 'format':
                formatCode(cm);
                break;
            case 'comment':
                cm.execCommand('toggleComment');
                break;
            case 'fold-all':
                cm.execCommand('foldAll');
                break;
            case 'unfold-all':
                cm.execCommand('unfoldAll');
                break;
            case 'find':
                cm.execCommand('findPersistent');
                break;
            case 'replace':
                cm.execCommand('replace');
                break;
            case 'goto':
                cm.execCommand('jumpToLine');
                break;
            case 'fullscreen':
                toggleFullscreen(cm);
                break;
            case 'minimap':
                toggleMinimap();
                break;
            case 'analyze':
                analyzeCode();
                break;
            case 'optimize':
                optimizeCode();
                break;
        }
    }

    /**
     * 코드 포맷팅
     */
    function formatCode(cm) {
        if (!cm && editor) cm = editor.codemirror;
        if (!cm) return;

        const code = cm.getValue();
        const codeType = $('#acf_csb_code_type').val();
        let formatted = code;

        try {
            switch (codeType) {
                case 'css':
                    formatted = formatCSS(code);
                    break;
                case 'js':
                    formatted = formatJS(code);
                    break;
                case 'html':
                    formatted = formatHTML(code);
                    break;
                case 'php':
                    // PHP 포맷팅은 복잡하므로 기본 들여쓰기만
                    formatted = formatPHP(code);
                    break;
            }
            cm.setValue(formatted);
            showNotification('코드가 정리되었습니다', 'success');
        } catch (e) {
            showNotification('코드 정리 중 오류가 발생했습니다', 'error');
            console.error('Format error:', e);
        }
    }

    /**
     * CSS 포맷팅
     */
    function formatCSS(code) {
        // 압축된 CSS를 보기 좋게 정리
        return code
            .replace(/\s*{\s*/g, ' {\n    ')
            .replace(/\s*}\s*/g, '\n}\n\n')
            .replace(/;\s*/g, ';\n    ')
            .replace(/\n    }/g, '\n}')
            .replace(/\n\n+/g, '\n\n')
            .trim();
    }

    /**
     * JavaScript 포맷팅
     */
    function formatJS(code) {
        // 간단한 JS 포맷팅
        let indent = 0;
        const lines = code.split('\n');
        const formatted = [];

        lines.forEach(line => {
            line = line.trim();
            if (!line) return formatted.push('');

            // 닫는 괄호로 시작하면 들여쓰기 감소
            if (/^[}\]\)]/.test(line)) {
                indent = Math.max(0, indent - 1);
            }

            formatted.push('    '.repeat(indent) + line);

            // 여는 괄호로 끝나면 들여쓰기 증가
            if (/[{\[\(]\s*$/.test(line) && !/[}\]\)]/.test(line)) {
                indent++;
            }
        });

        return formatted.join('\n');
    }

    /**
     * HTML 포맷팅
     */
    function formatHTML(code) {
        let indent = 0;
        const voidElements = ['br', 'hr', 'img', 'input', 'meta', 'link', 'area', 'base', 'col'];

        return code
            .replace(/>\s*</g, '>\n<')
            .split('\n')
            .map(line => {
                line = line.trim();
                if (!line) return '';

                const isClosing = /^<\//.test(line);
                const isSelfClosing = /\/>$/.test(line) || voidElements.some(el => new RegExp(`^<${el}[\\s>]`, 'i').test(line));

                if (isClosing) indent = Math.max(0, indent - 1);

                const formatted = '    '.repeat(indent) + line;

                if (!isClosing && !isSelfClosing && /^<[^!]/.test(line) && !/<\//.test(line)) {
                    indent++;
                }

                return formatted;
            })
            .join('\n');
    }

    /**
     * PHP 포맷팅 (기본)
     */
    function formatPHP(code) {
        let indent = 0;
        const lines = code.split('\n');
        const formatted = [];

        lines.forEach(line => {
            line = line.trim();
            if (!line) return formatted.push('');

            // 닫는 괄호로 시작
            if (/^[}\]\)]/.test(line)) {
                indent = Math.max(0, indent - 1);
            }

            formatted.push('    '.repeat(indent) + line);

            // 여는 괄호로 끝남 (중괄호, 콜론 for switch/case)
            if ((/[{\[\(]\s*$/.test(line) || /:\s*$/.test(line)) && !/[}\]\)]/.test(line)) {
                indent++;
            }
        });

        return formatted.join('\n');
    }

    /**
     * 전체 화면 토글
     */
    function toggleFullscreen(cm) {
        const container = $('.acf-csb-editor-container');
        container.toggleClass('fullscreen');

        if (container.hasClass('fullscreen')) {
            $('body').css('overflow', 'hidden');
            if (cm) cm.refresh();
        } else {
            $('body').css('overflow', '');
            if (cm) cm.refresh();
        }
    }

    /**
     * 전체 화면 종료
     */
    function exitFullscreen(cm) {
        const container = $('.acf-csb-editor-container');
        if (container.hasClass('fullscreen')) {
            container.removeClass('fullscreen');
            $('body').css('overflow', '');
            if (cm) cm.refresh();
        } else {
            // ESC 기본 동작
            return CodeMirror.Pass;
        }
    }

    /**
     * 줄 복제
     */
    function duplicateLine(cm) {
        const cursor = cm.getCursor();
        const line = cm.getLine(cursor.line);
        cm.replaceRange('\n' + line, { line: cursor.line, ch: line.length });
        cm.setCursor({ line: cursor.line + 1, ch: cursor.ch });
    }

    /**
     * 줄 삭제
     */
    function deleteLine(cm) {
        const cursor = cm.getCursor();
        cm.execCommand('deleteLine');
    }

    /**
     * 스니펫 저장
     */
    function saveSnippet() {
        $('#publish, #save-post').click();
        showNotification('저장 중...', 'info');
    }

    /**
     * 키보드 단축키 초기화
     */
    function initKeyboardShortcuts() {
        $(document).on('keydown', function(e) {
            // Ctrl+Shift+P: 명령 팔레트 (향후 구현)
            if (e.ctrlKey && e.shiftKey && e.key === 'P') {
                e.preventDefault();
                showCommandPalette();
            }
        });
    }

    /**
     * 명령 팔레트 표시
     */
    function showCommandPalette() {
        // 향후 구현: VSCode 스타일 명령 팔레트
        showNotification('명령 팔레트: 향후 업데이트에서 제공됩니다', 'info');
    }

    /**
     * 자동 저장 초기화
     */
    function initAutoSave() {
        // 자동 저장은 2분마다 (변경사항이 있을 때만)
    }

    /**
     * 자동 저장 스케줄
     */
    function scheduleAutoSave() {
        if (autoSaveTimeout) clearTimeout(autoSaveTimeout);

        autoSaveTimeout = setTimeout(function() {
            // 자동 저장 임시 저장
            const code = editor?.codemirror?.getValue();
            if (code && window.acfCsbEditor?.postId) {
                localStorage.setItem(`acf_csb_autosave_${window.acfCsbEditor.postId}`, JSON.stringify({
                    code: code,
                    timestamp: Date.now(),
                    codeType: $('#acf_csb_code_type').val()
                }));
                $('.autosave-status').text('✓ 자동 저장됨').show();
                setTimeout(() => $('.autosave-status').fadeOut(), 3000);
            }
        }, 30000); // 30초
    }

    /**
     * 린트 스케줄
     */
    function scheduleLint() {
        if (lintTimeout) clearTimeout(lintTimeout);

        lintTimeout = setTimeout(function() {
            // 향후: 실시간 린팅 구현
        }, 500);
    }

    /**
     * 품질 점수 자동 체크
     * [v4.2.0] 코드 변경 시 자동으로 품질 점수 업데이트
     */
    let qualityCheckTimeout = null;
    function scheduleQualityCheck() {
        if (qualityCheckTimeout) clearTimeout(qualityCheckTimeout);

        qualityCheckTimeout = setTimeout(function() {
            if (!editor || !editor.codemirror) return;

            const code = editor.codemirror.getValue();
            const codeType = $('#acf_csb_code_type').val() || 'css';

            if (!code.trim() || code.length < 10) {
                $('.code-quality-badge').text('-').css({
                    'background': '#f8f9fa',
                    'color': '#666',
                    'border': '1px solid #ddd'
                });
                return;
            }

            // 백그라운드에서 품질 점수만 가져오기
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'acf_csb_get_code_quality_score',
                    code: code,
                    code_type: codeType,
                    nonce: window.acfCsbEditor?.nonce || ''
                },
                success: function(response) {
                    if (response.success) {
                        updateQualityBadge(response.data.score);
                    }
                }
            });
        }, 2000); // 2초 후 체크 (디바운싱)
    }

    /**
     * 상태 바 업데이트
     */
    function updateStatusBar() {
        if (!editor || !editor.codemirror) return;

        const cm = editor.codemirror;
        const code = cm.getValue();
        const lines = code.split('\n').length;
        const chars = code.length;
        const words = code.trim().split(/\s+/).filter(w => w).length;

        $('.code-stats').text(`${lines}줄 | ${chars}자 | ${words}단어`);
    }

    /**
     * 커서 위치 업데이트
     */
    function updateCursorPosition() {
        if (!editor || !editor.codemirror) return;

        const cm = editor.codemirror;
        const cursor = cm.getCursor();
        const selection = cm.getSelection();

        $('.cursor-position').text(`줄 ${cursor.line + 1}, 열 ${cursor.ch + 1}`);

        if (selection) {
            $('.selection-info').text(`선택: ${selection.length}자`);
        } else {
            $('.selection-info').text('');
        }
    }

    /**
     * 코드 타입 인디케이터 업데이트
     */
    function updateCodeTypeIndicator(type) {
        const style = TYPE_STYLES[type] || TYPE_STYLES.css;
        $('.code-type-indicator')
            .html(`${style.icon} ${type.toUpperCase()}`)
            .css({
                'background': style.bg,
                'color': style.color,
                'border': `1px solid ${style.color}`
            });
    }

    /**
     * 미니맵 초기화
     */
    function initMiniMap() {
        // 향후 구현: 코드 미니맵
    }

    /**
     * 미니맵 토글
     */
    function toggleMinimap() {
        showNotification('미니맵: 향후 업데이트에서 제공됩니다', 'info');
    }

    /**
     * 코드 분석
     * [v4.2.0] 코드 분석기 통합
     */
    function analyzeCode() {
        if (!editor || !editor.codemirror) return;

        const code = editor.codemirror.getValue();
        const codeType = $('#acf_csb_code_type').val() || 'css';

        if (!code.trim()) {
            showNotification('분석할 코드가 없습니다', 'warning');
            return;
        }

        showNotification('코드 분석 중...', 'info');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'acf_csb_analyze_code',
                code: code,
                code_type: codeType,
                nonce: window.acfCsbEditor?.nonce || ''
            },
            success: function(response) {
                if (response.success) {
                    displayAnalysisResults(response.data);
                    updateQualityBadge(response.data.quality_score);
                } else {
                    showNotification('분석 중 오류가 발생했습니다', 'error');
                }
            },
            error: function() {
                showNotification('분석 요청 실패', 'error');
            }
        });
    }

    /**
     * 분석 결과 표시
     */
    function displayAnalysisResults(analysis) {
        // 기존 패널 제거
        $('.acf-csb-analysis-panel').remove();

        const panel = $(`
            <div class="acf-csb-analysis-panel" style="
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 90%;
                max-width: 800px;
                max-height: 80vh;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                z-index: 999998;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            ">
                <div class="panel-header" style="
                    padding: 20px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: #fff;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                ">
                    <h2 style="margin: 0; font-size: 20px;">📊 코드 분석 결과</h2>
                    <button type="button" class="close-panel" style="
                        background: rgba(255,255,255,0.2);
                        border: none;
                        color: #fff;
                        width: 32px;
                        height: 32px;
                        border-radius: 50%;
                        cursor: pointer;
                        font-size: 20px;
                        line-height: 1;
                    ">×</button>
                </div>
                <div class="panel-content" style="
                    padding: 20px;
                    overflow-y: auto;
                    flex: 1;
                ">
                    ${buildAnalysisHTML(analysis)}
                </div>
            </div>
        `);

        // 오버레이
        const overlay = $('<div class="acf-csb-panel-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999997;"></div>');

        $('body').append(overlay).append(panel);

        // 닫기 이벤트
        overlay.add(panel.find('.close-panel')).on('click', function() {
            panel.add(overlay).fadeOut(200, function() { $(this).remove(); });
        });
    }

    /**
     * 분석 결과 HTML 생성
     */
    function buildAnalysisHTML(analysis) {
        let html = '';

        // 품질 점수
        const grade = getQualityGrade(analysis.quality_score);
        const scoreColor = getScoreColor(analysis.quality_score);
        html += `
            <div style="text-align: center; padding: 20px; background: ${scoreColor.bg}; border-radius: 8px; margin-bottom: 20px;">
                <div style="font-size: 48px; font-weight: bold; color: ${scoreColor.color}; margin-bottom: 10px;">
                    ${analysis.quality_score}
                </div>
                <div style="font-size: 24px; color: ${scoreColor.color}; font-weight: 600;">${grade}</div>
                <div style="margin-top: 10px; color: #666;">
                    ${analysis.lines}줄 | ${analysis.characters}자
                </div>
            </div>
        `;

        // 통계
        html += '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px;">';
        if (analysis.selectors !== undefined) {
            html += `<div style="padding: 15px; background: #f8f9fa; border-radius: 8px; text-align: center;"><strong>${analysis.selectors}</strong><br><small>선택자</small></div>`;
        }
        if (analysis.functions !== undefined) {
            html += `<div style="padding: 15px; background: #f8f9fa; border-radius: 8px; text-align: center;"><strong>${analysis.functions}</strong><br><small>함수</small></div>`;
        }
        if (analysis.complexity !== undefined) {
            html += `<div style="padding: 15px; background: #f8f9fa; border-radius: 8px; text-align: center;"><strong>${analysis.complexity}</strong><br><small>복잡도</small></div>`;
        }
        html += '</div>';

        // 문제점
        if (analysis.issues && analysis.issues.length > 0) {
            html += '<div style="margin-bottom: 20px;"><h3 style="margin: 0 0 15px; color: #dc3545;">⚠ 문제점</h3>';
            analysis.issues.forEach(function(issue) {
                const icon = issue.type === 'critical' ? '🔴' : issue.type === 'warning' ? '🟡' : '🔵';
                html += `
                    <div style="padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; margin-bottom: 8px; border-radius: 4px;">
                        ${icon} <strong>${issue.type}</strong>${issue.line ? ` (줄 ${issue.line})` : ''}<br>
                        ${issue.message}
                    </div>
                `;
            });
            html += '</div>';
        }

        // 제안사항
        if (analysis.suggestions && analysis.suggestions.length > 0) {
            html += '<div style="margin-bottom: 20px;"><h3 style="margin: 0 0 15px; color: #007bff;">💡 제안사항</h3>';
            analysis.suggestions.forEach(function(suggestion) {
                html += `
                    <div style="padding: 12px; background: #d1ecf1; border-left: 4px solid #007bff; margin-bottom: 8px; border-radius: 4px;">
                        ${suggestion}
                    </div>
                `;
            });
            html += '</div>';
        }

        // 의존성
        if (analysis.dependencies && analysis.dependencies.length > 0) {
            html += '<div style="margin-bottom: 20px;"><h3 style="margin: 0 0 15px;">📦 의존성</h3>';
            html += '<div style="display: flex; gap: 8px; flex-wrap: wrap;">';
            analysis.dependencies.forEach(function(dep) {
                html += `<span style="padding: 6px 12px; background: #e7f3ff; border-radius: 4px; font-size: 12px;">${dep}</span>`;
            });
            html += '</div></div>';
        }

        // 성능 정보
        if (analysis.performance) {
            html += '<div style="margin-bottom: 20px;"><h3 style="margin: 0 0 15px;">⚡ 성능</h3>';
            html += `<div style="padding: 12px; background: #f8f9fa; border-radius: 8px;">`;
            if (analysis.performance.file_size_kb) {
                html += `<div>파일 크기: <strong>${analysis.performance.file_size_kb} KB</strong></div>`;
            }
            if (analysis.performance.execution_time_estimate) {
                html += `<div>예상 실행 시간: <strong>${analysis.performance.execution_time_estimate}ms</strong></div>`;
            }
            if (analysis.performance.recommendation && analysis.performance.recommendation.length > 0) {
                html += '<div style="margin-top: 10px;"><strong>권장사항:</strong><ul style="margin: 5px 0; padding-left: 20px;">';
                analysis.performance.recommendation.forEach(function(rec) {
                    html += `<li>${rec}</li>`;
                });
                html += '</ul></div>';
            }
            html += '</div></div>';
        }

        return html;
    }

    /**
     * 품질 등급 반환
     */
    function getQualityGrade(score) {
        if (score >= 90) return 'A+';
        if (score >= 80) return 'A';
        if (score >= 70) return 'B';
        if (score >= 60) return 'C';
        if (score >= 50) return 'D';
        return 'F';
    }

    /**
     * 점수에 따른 색상 반환
     */
    function getScoreColor(score) {
        if (score >= 80) return { color: '#28a745', bg: '#d4edda' };
        if (score >= 60) return { color: '#ffc107', bg: '#fff3cd' };
        return { color: '#dc3545', bg: '#f8d7da' };
    }

    /**
     * 품질 배지 업데이트
     */
    function updateQualityBadge(score) {
        const grade = getQualityGrade(score);
        const color = getScoreColor(score);
        $('.code-quality-badge')
            .text(`${grade} (${score})`)
            .css({
                'background': color.bg,
                'color': color.color,
                'border': `1px solid ${color.color}`
            })
            .attr('title', `코드 품질 점수: ${score}/100`);
    }

    /**
     * 코드 최적화
     * [v4.2.0] 코드 최적화기 통합
     */
    function optimizeCode() {
        if (!editor || !editor.codemirror) return;

        const code = editor.codemirror.getValue();
        const codeType = $('#acf_csb_code_type').val() || 'css';

        if (!code.trim()) {
            showNotification('최적화할 코드가 없습니다', 'warning');
            return;
        }

        showNotification('코드 최적화 중...', 'info');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'acf_csb_optimize_code',
                code: code,
                code_type: codeType,
                options: {
                    minify: true,
                    remove_comments: true,
                    remove_whitespace: true
                },
                nonce: window.acfCsbEditor?.nonce || ''
            },
            success: function(response) {
                if (response.success) {
                    displayOptimizationResults(response.data);
                } else {
                    showNotification('최적화 중 오류가 발생했습니다', 'error');
                }
            },
            error: function() {
                showNotification('최적화 요청 실패', 'error');
            }
        });
    }

    /**
     * 최적화 결과 표시
     */
    function displayOptimizationResults(result) {
        // 기존 패널 제거
        $('.acf-csb-optimization-panel').remove();

        const savingsPercent = result.savings_percent;
        const savingsColor = savingsPercent > 20 ? '#28a745' : savingsPercent > 10 ? '#ffc107' : '#dc3545';

        const panel = $(`
            <div class="acf-csb-optimization-panel" style="
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 90%;
                max-width: 700px;
                max-height: 80vh;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                z-index: 999998;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            ">
                <div class="panel-header" style="
                    padding: 20px;
                    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                    color: #fff;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                ">
                    <h2 style="margin: 0; font-size: 20px;">⚡ 코드 최적화 결과</h2>
                    <button type="button" class="close-panel" style="
                        background: rgba(255,255,255,0.2);
                        border: none;
                        color: #fff;
                        width: 32px;
                        height: 32px;
                        border-radius: 50%;
                        cursor: pointer;
                        font-size: 20px;
                        line-height: 1;
                    ">×</button>
                </div>
                <div class="panel-content" style="
                    padding: 20px;
                    overflow-y: auto;
                    flex: 1;
                ">
                    <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 20px;">
                        <div style="font-size: 36px; font-weight: bold; color: ${savingsColor}; margin-bottom: 10px;">
                            ${savingsPercent}% 절약
                        </div>
                        <div style="color: #666;">
                            ${formatBytes(result.original_size)} → ${formatBytes(result.optimized_size)}<br>
                            <small>${formatBytes(result.savings)} 절약</small>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <h3 style="margin: 0 0 15px;">최적화된 코드</h3>
                        <textarea readonly style="
                            width: 100%;
                            min-height: 200px;
                            padding: 15px;
                            font-family: 'Courier New', monospace;
                            font-size: 12px;
                            background: #1e1e1e;
                            color: #d4d4d4;
                            border: 1px solid #3c3c3c;
                            border-radius: 6px;
                            resize: vertical;
                        ">${escapeHtml(result.optimized_code)}</textarea>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="button button-primary copy-optimized-code" style="flex: 1;">
                            📋 최적화된 코드 복사
                        </button>
                        <button type="button" class="button button-secondary apply-optimized-code" style="flex: 1;">
                            ✅ 최적화된 코드 적용
                        </button>
                    </div>
                </div>
            </div>
        `);

        // 오버레이
        const overlay = $('<div class="acf-csb-panel-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999997;"></div>');

        $('body').append(overlay).append(panel);

        // 복사 버튼
        panel.find('.copy-optimized-code').on('click', function() {
            const textarea = panel.find('textarea')[0];
            textarea.select();
            document.execCommand('copy');
            showNotification('최적화된 코드가 클립보드에 복사되었습니다', 'success');
        });

        // 적용 버튼
        panel.find('.apply-optimized-code').on('click', function() {
            if (editor && editor.codemirror) {
                editor.codemirror.setValue(result.optimized_code);
                showNotification('최적화된 코드가 적용되었습니다', 'success');
                panel.add(overlay).fadeOut(200, function() { $(this).remove(); });
            }
        });

        // 닫기 이벤트
        overlay.add(panel.find('.close-panel')).on('click', function() {
            panel.add(overlay).fadeOut(200, function() { $(this).remove(); });
        });
    }

    /**
     * 바이트를 읽기 쉬운 형식으로 변환
     */
    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    /**
     * HTML 이스케이프
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * 알림 표시
     */
    function showNotification(message, type = 'info') {
        const colors = {
            success: { bg: '#d4edda', color: '#155724', border: '#c3e6cb' },
            error: { bg: '#f8d7da', color: '#721c24', border: '#f5c6cb' },
            warning: { bg: '#fff3cd', color: '#856404', border: '#ffeeba' },
            info: { bg: '#d1ecf1', color: '#0c5460', border: '#bee5eb' }
        };
        const style = colors[type] || colors.info;

        const $notification = $(`
            <div class="acf-csb-notification" style="
                position: fixed;
                bottom: 20px;
                right: 20px;
                padding: 12px 20px;
                background: ${style.bg};
                color: ${style.color};
                border: 1px solid ${style.border};
                border-radius: 6px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 999999;
                animation: slideIn 0.3s ease;
            ">${message}</div>
        `);

        $('body').append($notification);
        setTimeout(() => $notification.fadeOut(300, function() { $(this).remove(); }), 3000);
    }

    /**
     * 트리거 토글 초기화
     */
    function initTriggerToggles() {
        $('select[name="acf_csb_triggers[location]"]').on('change', function() {
            const val = $(this).val();

            $('.acf-csb-specific-pages, .acf-csb-specific-posts').hide();

            if (val === 'specific_pages') {
                $('.acf-csb-specific-pages').show();
            } else if (val === 'specific_posts') {
                $('.acf-csb-specific-posts').show();
            }
        });
    }

    /**
     * 프리셋 로드
     */
    function loadPresetIfExists() {
        const urlParams = new URLSearchParams(window.location.search);
        const presetType = urlParams.get('preset_type');
        const presetId = urlParams.get('preset_id');

        if (presetType && presetId) {
            const checkEditor = setInterval(function() {
                if (editor && editor.codemirror) {
                    clearInterval(checkEditor);

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'acf_csb_apply_preset',
                            nonce: window.acfCsbEditor?.nonce || '',
                            type: presetType,
                            preset_id: presetId
                        },
                        success: function(response) {
                            if (response.success && response.data) {
                                $('#title').val(response.data.name);
                                $('#acf_csb_code_type').val(presetType).trigger('change');

                                setTimeout(function() {
                                    if (editor && editor.codemirror) {
                                        editor.codemirror.setValue(response.data.code);
                                    }
                                }, 100);

                                showNotification('프리셋이 로드되었습니다', 'success');
                            }
                        }
                    });
                }
            }, 100);

            setTimeout(() => clearInterval(checkEditor), 5000);
        }
    }

    /**
     * 디바운스 유틸리티
     */
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // DOM Ready
    $(document).ready(init);

    // 공개 API
    ACF_CSB_Editor.getEditor = function() { return editor; };
    ACF_CSB_Editor.formatCode = formatCode;
    ACF_CSB_Editor.showNotification = showNotification;

})(jQuery);
