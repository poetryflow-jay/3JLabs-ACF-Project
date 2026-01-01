/**
 * ACF Code Snippets Box - Code Editor
 * 
 * WordPress CodeMirror 통합 및 자동완성
 * 
 * @package ACF_Code_Snippets_Box
 */

(function($) {
    'use strict';

    let editor = null;

    /**
     * 코드 에디터 초기화
     */
    function initCodeEditor() {
        const textarea = document.getElementById('acf_csb_code');
        if (!textarea) return;

        // CodeMirror 설정
        const settings = acfCsbEditor.codeEditorSettings || {};
        
        // 코드 타입에 따른 모드 설정
        const codeType = $('#acf_csb_code_type').val() || 'css';
        const modeMap = {
            'css': 'text/css',
            'js': 'javascript',
            'php': 'application/x-httpd-php',
            'html': 'htmlmixed'
        };

        settings.codemirror = settings.codemirror || {};
        settings.codemirror.mode = modeMap[codeType] || 'text/css';
        settings.codemirror.lineNumbers = true;
        settings.codemirror.lineWrapping = true;
        settings.codemirror.indentUnit = 4;
        settings.codemirror.tabSize = 4;
        settings.codemirror.indentWithTabs = false;
        settings.codemirror.matchBrackets = true;
        settings.codemirror.autoCloseBrackets = true;
        settings.codemirror.autoCloseTags = true;
        settings.codemirror.extraKeys = {
            'Ctrl-Space': 'autocomplete',
            'Ctrl-S': function(cm) {
                $('#publish, #save-post').click();
            }
        };

        // 에디터 초기화
        editor = wp.codeEditor.initialize(textarea, settings);

        // 코드 타입 변경 시 모드 업데이트
        $('#acf_csb_code_type').on('change', function() {
            const newType = $(this).val();
            const newMode = modeMap[newType] || 'text/css';
            editor.codemirror.setOption('mode', newMode);
        });

        // ACF CSS 변수 자동완성
        if (typeof acfCsbCssVars !== 'undefined' && acfCsbCssVars.enabled) {
            initCssVarsAutocomplete();
        }
    }

    /**
     * CSS 변수 자동완성 초기화
     */
    function initCssVarsAutocomplete() {
        if (!editor || !acfCsbCssVars.variables) return;

        CodeMirror.registerHelper('hint', 'css', function(cm) {
            const cur = cm.getCursor();
            const token = cm.getTokenAt(cur);
            const start = token.start;
            const end = cur.ch;
            const currentWord = token.string;

            // var( 입력 시 자동완성
            if (currentWord.includes('var(') || currentWord.includes('--jj-')) {
                const list = acfCsbCssVars.variables.map(function(v) {
                    return {
                        text: 'var(' + v.name + ')',
                        displayText: v.name + ' → ' + v.value,
                        className: 'acf-csb-hint-' + v.type
                    };
                });

                return {
                    list: list.filter(function(item) {
                        return item.text.toLowerCase().includes(currentWord.toLowerCase());
                    }),
                    from: CodeMirror.Pos(cur.line, start),
                    to: CodeMirror.Pos(cur.line, end)
                };
            }

            return null;
        });
    }

    /**
     * 트리거 조건 UI 토글
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
     * 프리셋 자동 로드
     */
    function loadPresetIfExists() {
        const urlParams = new URLSearchParams(window.location.search);
        const presetType = urlParams.get('preset_type');
        const presetId = urlParams.get('preset_id');

        if (presetType && presetId) {
            // AJAX로 프리셋 코드 가져오기
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'acf_csb_apply_preset',
                    nonce: acfCsbEditor.nonce || '',
                    type: presetType,
                    preset_id: presetId
                },
                success: function(response) {
                    if (response.success && response.data) {
                        // 제목 설정
                        $('#title').val(response.data.name);
                        
                        // 코드 타입 설정
                        $('#acf_csb_code_type').val(presetType).trigger('change');
                        
                        // 코드 설정
                        if (editor && editor.codemirror) {
                            editor.codemirror.setValue(response.data.code);
                        } else {
                            $('#acf_csb_code').val(response.data.code);
                        }

                        // URL 정리
                        const cleanUrl = window.location.href.split('?')[0] + '?post_type=acf_code_snippet';
                        window.history.replaceState({}, document.title, cleanUrl);
                    }
                }
            });
        }
    }

    /**
     * 문서 준비 시 초기화
     */
    $(document).ready(function() {
        initCodeEditor();
        initTriggerToggles();
        loadPresetIfExists();

        // 코드 복사 버튼
        $(document).on('click', '.acf-csb-copy-code', function() {
            const code = $(this).data('code');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(code).then(() => {
                    const $btn = $(this);
                    const originalText = $btn.text();
                    $btn.text('복사됨!');
                    setTimeout(() => $btn.text(originalText), 2000);
                });
            }
        });
    });

})(jQuery);
