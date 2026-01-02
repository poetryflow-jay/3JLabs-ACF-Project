/**
 * J&J Keyboard Shortcuts System - v5.0.3
 * 
 * 포괄적인 키보드 단축키 시스템
 * - 다국어 키보드 레이아웃 지원
 * - 자연스러운 키 조합
 * - 모든 페이지에 일관된 단축키
 * - 단축키 안내 UI
 * 
 * @since 5.0.3
 * @version 5.0.3
 * 
 * [v5.0.3 주요 기능]
 * - 다국어 키보드 레이아웃 대응 (QWERTY, QWERTZ, AZERTY, 한글 등)
 * - 키 코드 기반 감지로 레이아웃 독립적 동작
 * - 단축키 충돌 방지 (입력 필드, 에디터 등에서 적절히 처리)
 * - 단축키 안내 모달 및 툴팁
 * - 접근성 고려 (키보드만으로도 모든 기능 사용 가능)
 */
(function($) {
    'use strict';

    /**
     * 키보드 단축키 매니저
     */
    const KeyboardShortcuts = {
        // 단축키 정의 (키 코드 기반으로 레이아웃 독립적)
        shortcuts: {
            // 저장 (Ctrl/Cmd + S) - 모든 언어에서 'S' 키는 동일한 위치
            save: {
                key: 'KeyS', // 키 코드
                ctrl: true,
                meta: true, // Cmd (Mac)
                shift: false,
                alt: false,
                description: {
                    ko_KR: '저장',
                    en_US: 'Save',
                    zh_CN: '保存',
                    ja: '保存',
                    de_DE: 'Speichern',
                    fr_FR: 'Enregistrer',
                    it_IT: 'Salva',
                    es_ES: 'Guardar',
                    pt_PT: 'Guardar',
                    es_MX: 'Guardar',
                    pt_BR: 'Salvar',
                    tr_TR: 'Kaydet',
                    la: 'Servare',
                    he_IL: 'שמור'
                },
                handler: function() {
                    // 현재 페이지에 따라 적절한 저장 함수 호출
                    if ($('.jj-admin-center-wrap').length) {
                        if (typeof saveAdminCenterSettings === 'function') {
                            saveAdminCenterSettings();
                        } else {
                            // 폴백: 저장 버튼 클릭
                            const $saveBtn = $('[data-action="save"]');
                            if ($saveBtn.length && !$saveBtn.prop('disabled')) {
                                $saveBtn.trigger('click');
                            }
                        }
                    } else if ($('#jj-style-guide-form').length) {
                        // 스타일 가이드 에디터 저장 버튼 클릭
                        const $saveBtn = $('#jj-style-guide-form').find('.jj-save-button, button[type="submit"]');
                        if ($saveBtn.length && !$saveBtn.prop('disabled')) {
                            $saveBtn.trigger('click');
                        }
                    } else if ($('.jj-labs-center-wrap').length) {
                        if (typeof saveLabsSettings === 'function') {
                            saveLabsSettings();
                        } else {
                            // 폴백: 저장 버튼 클릭
                            const $saveBtn = $('.jj-labs-center-wrap').find('.jj-save-button, button[type="submit"]');
                            if ($saveBtn.length && !$saveBtn.prop('disabled')) {
                                $saveBtn.trigger('click');
                            }
                        }
                    }
                }
            },
            
            // 되돌리기 (Ctrl/Cmd + Z) - 모든 언어에서 'Z' 키는 동일한 위치
            undo: {
                key: 'KeyZ',
                ctrl: true,
                meta: true,
                shift: false,
                alt: false,
                description: {
                    ko_KR: '되돌리기',
                    en_US: 'Undo',
                    zh_CN: '撤销',
                    ja: '元に戻す',
                    de_DE: 'Rückgängig',
                    fr_FR: 'Annuler',
                    it_IT: 'Annulla',
                    es_ES: 'Deshacer',
                    pt_PT: 'Desfazer',
                    es_MX: 'Deshacer',
                    pt_BR: 'Desfazer',
                    tr_TR: 'Geri Al',
                    la: 'Reversus',
                    he_IL: 'בטל'
                },
                handler: function() {
                    if (typeof undoChange === 'function') {
                        const $undoBtn = $('.jj-undo-button.has-history');
                        if ($undoBtn.length) {
                            undoChange();
                        }
                    }
                }
            },
            
            // 기본값으로 되돌리기 (Ctrl/Cmd + Shift + R) - 'R' 키는 대부분 레이아웃에서 동일
            reset: {
                key: 'KeyR',
                ctrl: true,
                meta: true,
                shift: true,
                alt: false,
                description: {
                    ko_KR: '기본값으로 되돌리기',
                    en_US: 'Reset to Default',
                    zh_CN: '重置为默认值',
                    ja: 'デフォルトに戻す',
                    de_DE: 'Auf Standard zurücksetzen',
                    fr_FR: 'Réinitialiser aux valeurs par défaut',
                    it_IT: 'Ripristina predefiniti',
                    es_ES: 'Restablecer valores predeterminados',
                    pt_PT: 'Repor valores predefinidos',
                    es_MX: 'Restablecer valores predeterminados',
                    pt_BR: 'Redefinir valores padrão',
                    tr_TR: 'Varsayılana Sıfırla',
                    la: 'Redire ad praedefinitum',
                    he_IL: 'איפוס לברירת מחדל'
                },
                handler: function() {
                    // 확인 다이얼로그 표시
                    const confirmMsg = this.currentLocale === 'ko_KR' 
                        ? '모든 설정을 기본값으로 되돌리시겠습니까?\n\n이 작업은 되돌릴 수 없습니다.'
                        : 'Are you sure you want to reset all settings to default values?\n\nThis action cannot be undone.';
                    
                    if (confirm(confirmMsg)) {
                        // 관리자 센터 리셋
                        if ($('.jj-admin-center-wrap').length) {
                            if (typeof resetAdminCenterSettings === 'function') {
                                resetAdminCenterSettings();
                            } else {
                                const $resetBtn = $('.jj-reset-to-defaults, .jj-reset-button');
                                if ($resetBtn.length) {
                                    $resetBtn.first().trigger('click');
                                } else {
                                    location.reload();
                                }
                            }
                        } else {
                            // 스타일 가이드 에디터 리셋
                            location.reload();
                        }
                    }
                }
            },
            
            // 단축키 도움말 (Ctrl/Cmd + ? 또는 F1)
            help: {
                key: 'Slash', // '?' 키 (Shift + /)
                ctrl: true,
                meta: true,
                shift: false,
                alt: false,
                description: {
                    ko_KR: '단축키 도움말',
                    en_US: 'Keyboard Shortcuts',
                    zh_CN: '键盘快捷键',
                    ja: 'キーボードショートカット',
                    de_DE: 'Tastenkürzel',
                    fr_FR: 'Raccourcis clavier',
                    it_IT: 'Scorciatoie da tastiera',
                    es_ES: 'Atajos de teclado',
                    pt_PT: 'Atalhos de teclado',
                    es_MX: 'Atajos de teclado',
                    pt_BR: 'Atalhos de teclado',
                    tr_TR: 'Klavye Kısayolları',
                    la: 'Compendia Tastaturae',
                    he_IL: 'קיצורי מקלדת'
                },
                handler: function() {
                    KeyboardShortcuts.showHelpModal();
                }
            },
            
            // F1 키로도 도움말 열기
            helpF1: {
                key: 'F1',
                ctrl: false,
                meta: false,
                shift: false,
                alt: false,
                description: {
                    ko_KR: '도움말',
                    en_US: 'Help',
                    zh_CN: '帮助',
                    ja: 'ヘルプ',
                    de_DE: 'Hilfe',
                    fr_FR: 'Aide',
                    it_IT: 'Aiuto',
                    es_ES: 'Ayuda',
                    pt_PT: 'Ajuda',
                    es_MX: 'Ayuda',
                    pt_BR: 'Ajuda',
                    tr_TR: 'Yardım',
                    la: 'Auxilium',
                    he_IL: 'עזרה'
                },
                handler: function() {
                    KeyboardShortcuts.showHelpModal();
                }
            },
            
            // 검색 포커스 (Ctrl/Cmd + F) - 표준 검색 단축키
            search: {
                key: 'KeyF',
                ctrl: true,
                meta: true,
                shift: false,
                alt: false,
                description: {
                    ko_KR: '검색',
                    en_US: 'Search',
                    zh_CN: '搜索',
                    ja: '検索',
                    de_DE: 'Suchen',
                    fr_FR: 'Rechercher',
                    it_IT: 'Cerca',
                    es_ES: 'Buscar',
                    pt_PT: 'Pesquisar',
                    es_MX: 'Buscar',
                    pt_BR: 'Pesquisar',
                    tr_TR: 'Ara',
                    la: 'Quaere',
                    he_IL: 'חפש'
                },
                handler: function() {
                    // 검색 필드가 있으면 포커스
                    const $searchInput = $('.jj-search-input, #jj-sections-search, #jj-labs-tabs-search');
                    if ($searchInput.length) {
                        $searchInput.first().focus().select();
                        return true; // 기본 동작 방지
                    }
                    return false; // 기본 브라우저 검색 허용
                }
            },
            
            // 내보내기 (Ctrl/Cmd + E) - Export의 E
            export: {
                key: 'KeyE',
                ctrl: true,
                meta: true,
                shift: false,
                alt: false,
                description: {
                    ko_KR: '내보내기',
                    en_US: 'Export',
                    zh_CN: '导出',
                    ja: 'エクスポート',
                    de_DE: 'Exportieren',
                    fr_FR: 'Exporter',
                    it_IT: 'Esporta',
                    es_ES: 'Exportar',
                    pt_PT: 'Exportar',
                    es_MX: 'Exportar',
                    pt_BR: 'Exportar',
                    tr_TR: 'Dışa Aktar',
                    la: 'Exportare',
                    he_IL: 'ייצא'
                },
                handler: function() {
                    const $exportBtn = $('.jj-export-button, [data-action="export"]');
                    if ($exportBtn.length) {
                        $exportBtn.first().trigger('click');
                    }
                }
            },
            
            // 불러오기 (Ctrl/Cmd + I) - Import의 I
            import: {
                key: 'KeyI',
                ctrl: true,
                meta: true,
                shift: false,
                alt: false,
                description: {
                    ko_KR: '불러오기',
                    en_US: 'Import',
                    zh_CN: '导入',
                    ja: 'インポート',
                    de_DE: 'Importieren',
                    fr_FR: 'Importer',
                    it_IT: 'Importa',
                    es_ES: 'Importar',
                    pt_PT: 'Importar',
                    es_MX: 'Importar',
                    pt_BR: 'Importar',
                    tr_TR: 'İçe Aktar',
                    la: 'Importare',
                    he_IL: 'ייבא'
                },
                handler: function() {
                    const $importBtn = $('.jj-import-button, [data-action="import"]');
                    if ($importBtn.length) {
                        $importBtn.first().trigger('click');
                    }
                }
            },
            
            // 실시간 미리보기 토글 (Ctrl/Cmd + P) - Preview의 P
            preview: {
                key: 'KeyP',
                ctrl: true,
                meta: true,
                shift: false,
                alt: false,
                description: {
                    ko_KR: '실시간 미리보기',
                    en_US: 'Live Preview',
                    zh_CN: '实时预览',
                    ja: 'ライブプレビュー',
                    de_DE: 'Live-Vorschau',
                    fr_FR: 'Aperçu en direct',
                    it_IT: 'Anteprima in tempo reale',
                    es_ES: 'Vista previa en vivo',
                    pt_PT: 'Pré-visualização em tempo real',
                    es_MX: 'Vista previa en vivo',
                    pt_BR: 'Pré-visualização em tempo real',
                    tr_TR: 'Canlı Önizleme',
                    la: 'Praevisionem Vivam',
                    he_IL: 'תצוגה מקדימה חיה'
                },
                handler: function() {
                    const $previewBtn = $('.jj-preview-button, [data-action="preview"]');
                    if ($previewBtn.length) {
                        $previewBtn.first().trigger('click');
                    } else if (typeof refreshPreviewIfOpen === 'function') {
                        refreshPreviewIfOpen();
                    }
                }
            }
        },
        
        // 현재 언어 코드 (WordPress 로케일에서 가져옴)
        currentLocale: 'en_US',
        
        // 입력 필드에서 단축키를 허용할지 여부
        allowInInputFields: {
            save: true,      // 저장은 입력 필드에서도 허용
            undo: true,      // 되돌리기도 허용
            reset: false,    // 리셋은 위험하므로 입력 필드에서는 차단
            help: true,      // 도움말은 항상 허용
            search: true,    // 검색은 입력 필드에서도 허용
            export: false,
            import: false,
            preview: false
        },
        
        /**
         * 초기화
         */
        init: function() {
            // WordPress 로케일 가져오기
            if (typeof jj_admin_params !== 'undefined' && jj_admin_params.locale) {
                this.currentLocale = jj_admin_params.locale;
            } else if (typeof wp !== 'undefined' && wp.i18n && wp.i18n.getLocale) {
                this.currentLocale = wp.i18n.getLocale();
            }
            
            // 키보드 이벤트 리스너 등록
            this.bindEvents();
            
            // 단축키 안내 버튼 추가
            this.addHelpButton();
        },
        
        /**
         * 키보드 이벤트 바인딩
         */
        bindEvents: function() {
            const self = this;
            
            $(document).on('keydown', function(e) {
                self.handleKeydown(e);
            });
        },
        
        /**
         * 키다운 이벤트 처리
         */
        handleKeydown: function(e) {
            const $target = $(e.target);
            const isInputField = $target.is('input, textarea, select') && 
                                !$target.is('[type="checkbox"], [type="radio"]');
            const isCodeEditor = $target.closest('.CodeMirror').length > 0;
            const isContentEditable = $target.is('[contenteditable="true"]');
            
            // 각 단축키 확인
            for (const shortcutKey in this.shortcuts) {
                const shortcut = this.shortcuts[shortcutKey];
                
                // 키 코드 매칭
                if (e.code !== shortcut.key) {
                    continue;
                }
                
                // 수정자 키 확인
                const ctrlMatch = shortcut.ctrl ? (e.ctrlKey || e.metaKey) : (!e.ctrlKey && !e.metaKey);
                const shiftMatch = shortcut.shift === e.shiftKey;
                const altMatch = shortcut.alt === e.altKey;
                
                if (!ctrlMatch || !shiftMatch || !altMatch) {
                    continue;
                }
                
                // 입력 필드에서 허용 여부 확인
                if (isInputField || isCodeEditor || isContentEditable) {
                    if (!this.allowInInputFields[shortcutKey]) {
                        // 검색 단축키는 특별 처리
                        if (shortcutKey === 'search') {
                            const handled = shortcut.handler.call(this);
                            if (handled) {
                                e.preventDefault();
                                e.stopPropagation();
                                return false;
                            }
                        }
                        continue;
                    }
                }
                
                // 단축키 실행
                e.preventDefault();
                e.stopPropagation();
                
                try {
                    shortcut.handler.call(this);
                } catch (error) {
                    console.error('Keyboard shortcut error:', error);
                }
                
                return false;
            }
        },
        
        /**
         * 단축키 설명 가져오기 (현재 언어)
         */
        getDescription: function(shortcutKey) {
            const shortcut = this.shortcuts[shortcutKey];
            if (!shortcut || !shortcut.description) {
                return '';
            }
            
            // 현재 언어의 설명 반환, 없으면 영어
            return shortcut.description[this.currentLocale] || 
                   shortcut.description['en_US'] || 
                   shortcutKey;
        },
        
        /**
         * 단축키 표시 형식 (키 조합을 사용자 친화적으로 표시)
         */
        getKeyDisplay: function(shortcut) {
            const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
            const parts = [];
            
            if (shortcut.ctrl || shortcut.meta) {
                parts.push(isMac ? '⌘' : 'Ctrl');
            }
            if (shortcut.shift) {
                parts.push('Shift');
            }
            if (shortcut.alt) {
                parts.push('Alt');
            }
            
            // 키 이름 변환
            const keyNames = {
                'KeyS': 'S',
                'KeyZ': 'Z',
                'KeyR': 'R',
                'KeyF': 'F',
                'KeyE': 'E',
                'KeyI': 'I',
                'KeyP': 'P',
                'Slash': '?',
                'F1': 'F1'
            };
            
            parts.push(keyNames[shortcut.key] || shortcut.key);
            
            return parts.join(isMac ? '' : ' + ');
        },
        
        /**
         * 단축키 도움말 모달 표시
         */
        showHelpModal: function() {
            // 이미 모달이 열려있으면 닫기
            if ($('#jj-shortcuts-help-modal').length) {
                $('#jj-shortcuts-help-modal').remove();
                return;
            }
            
            const self = this;
            const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
            
            // 모달 HTML 생성
            let modalHTML = '<div id="jj-shortcuts-help-modal" class="jj-shortcuts-modal">';
            modalHTML += '<div class="jj-shortcuts-modal-overlay"></div>';
            modalHTML += '<div class="jj-shortcuts-modal-content">';
            modalHTML += '<div class="jj-shortcuts-modal-header">';
            modalHTML += '<h2>' + this.getDescription('help') + '</h2>';
            modalHTML += '<button class="jj-shortcuts-modal-close" aria-label="' + 
                        (this.currentLocale === 'ko_KR' ? '닫기' : 'Close') + '">×</button>';
            modalHTML += '</div>';
            modalHTML += '<div class="jj-shortcuts-modal-body">';
            modalHTML += '<table class="jj-shortcuts-table">';
            modalHTML += '<thead><tr>';
            modalHTML += '<th>' + (this.currentLocale === 'ko_KR' ? '단축키' : 'Shortcut') + '</th>';
            modalHTML += '<th>' + (this.currentLocale === 'ko_KR' ? '기능' : 'Action') + '</th>';
            modalHTML += '</tr></thead>';
            modalHTML += '<tbody>';
            
            // 각 단축키 표시
            const displayOrder = ['save', 'undo', 'reset', 'search', 'export', 'import', 'preview', 'help'];
            displayOrder.forEach(function(key) {
                if (!self.shortcuts[key]) return;
                
                const shortcut = self.shortcuts[key];
                const description = self.getDescription(key);
                const keyDisplay = self.getKeyDisplay(shortcut);
                
                modalHTML += '<tr>';
                modalHTML += '<td class="jj-shortcut-keys"><kbd>' + keyDisplay + '</kbd></td>';
                modalHTML += '<td class="jj-shortcut-description">' + description + '</td>';
                modalHTML += '</tr>';
            });
            
            modalHTML += '</tbody></table>';
            modalHTML += '<div class="jj-shortcuts-modal-footer">';
            modalHTML += '<p class="jj-shortcuts-hint">';
            if (this.currentLocale === 'ko_KR') {
                modalHTML += '💡 <strong>팁:</strong> 입력 필드에 포커스가 있을 때도 저장(Ctrl+S)과 되돌리기(Ctrl+Z) 단축키를 사용할 수 있습니다.';
            } else {
                modalHTML += '💡 <strong>Tip:</strong> You can use Save (Ctrl+S) and Undo (Ctrl+Z) shortcuts even when input fields are focused.';
            }
            modalHTML += '</p>';
            modalHTML += '</div>';
            modalHTML += '</div></div></div>';
            
            // 모달 추가
            $('body').append(modalHTML);
            
            // 모달 스타일 적용
            this.applyModalStyles();
            
            // 이벤트 바인딩
            $('#jj-shortcuts-help-modal .jj-shortcuts-modal-close, #jj-shortcuts-help-modal .jj-shortcuts-modal-overlay').on('click', function() {
                $('#jj-shortcuts-help-modal').fadeOut(200, function() {
                    $(this).remove();
                });
            });
            
            // ESC 키로 닫기
            $(document).on('keydown.jj-shortcuts-modal', function(e) {
                if (e.key === 'Escape' && $('#jj-shortcuts-help-modal').length) {
                    $('#jj-shortcuts-help-modal').fadeOut(200, function() {
                        $(this).remove();
                    });
                    $(document).off('keydown.jj-shortcuts-modal');
                }
            });
            
            // 모달 표시
            $('#jj-shortcuts-help-modal').fadeIn(200);
        },
        
        /**
         * 모달 스타일 적용
         */
        applyModalStyles: function() {
            if ($('#jj-shortcuts-modal-styles').length) {
                return; // 이미 스타일이 적용됨
            }
            
            const styles = `
                <style id="jj-shortcuts-modal-styles">
                .jj-shortcuts-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 100000;
                    display: none;
                }
                .jj-shortcuts-modal-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7);
                    backdrop-filter: blur(2px);
                }
                .jj-shortcuts-modal-content {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: #fff;
                    border-radius: 8px;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                    max-width: 600px;
                    width: 90%;
                    max-height: 80vh;
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                }
                .jj-shortcuts-modal-header {
                    padding: 20px 24px;
                    border-bottom: 1px solid #ddd;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    background: #f8f9fa;
                }
                .jj-shortcuts-modal-header h2 {
                    margin: 0;
                    font-size: 20px;
                    font-weight: 600;
                }
                .jj-shortcuts-modal-close {
                    background: none;
                    border: none;
                    font-size: 28px;
                    line-height: 1;
                    cursor: pointer;
                    color: #666;
                    padding: 0;
                    width: 32px;
                    height: 32px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 4px;
                    transition: all 0.2s;
                }
                .jj-shortcuts-modal-close:hover {
                    background: #e0e0e0;
                    color: #000;
                }
                .jj-shortcuts-modal-body {
                    padding: 24px;
                    overflow-y: auto;
                    flex: 1;
                }
                .jj-shortcuts-table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .jj-shortcuts-table thead {
                    background: #f8f9fa;
                }
                .jj-shortcuts-table th {
                    padding: 12px;
                    text-align: left;
                    font-weight: 600;
                    border-bottom: 2px solid #ddd;
                }
                .jj-shortcuts-table td {
                    padding: 12px;
                    border-bottom: 1px solid #eee;
                }
                .jj-shortcut-keys {
                    width: 40%;
                }
                .jj-shortcut-keys kbd {
                    display: inline-block;
                    padding: 4px 8px;
                    background: #f5f5f5;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    font-size: 13px;
                    font-weight: 600;
                    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
                    color: #333;
                }
                .jj-shortcut-description {
                    color: #555;
                }
                .jj-shortcuts-modal-footer {
                    padding: 16px 24px;
                    border-top: 1px solid #ddd;
                    background: #f8f9fa;
                }
                .jj-shortcuts-hint {
                    margin: 0;
                    font-size: 13px;
                    color: #666;
                    line-height: 1.6;
                }
                .jj-shortcuts-help-button {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    background: #2271b1;
                    color: #fff;
                    border: none;
                    border-radius: 50%;
                    width: 48px;
                    height: 48px;
                    font-size: 20px;
                    cursor: pointer;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    transition: all 0.3s;
                    z-index: 9999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .jj-shortcuts-help-button:hover {
                    background: #135e96;
                    transform: scale(1.1);
                    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
                }
                .jj-shortcuts-help-button:focus {
                    outline: 2px solid #2271b1;
                    outline-offset: 2px;
                }
                @media (max-width: 782px) {
                    .jj-shortcuts-modal-content {
                        width: 95%;
                        max-height: 90vh;
                    }
                    .jj-shortcuts-help-button {
                        bottom: 10px;
                        right: 10px;
                        width: 44px;
                        height: 44px;
                        font-size: 18px;
                    }
                }
                </style>
            `;
            
            $('head').append(styles);
        },
        
        /**
         * 단축키 도움말 버튼 추가
         */
        addHelpButton: function() {
            // 이미 버튼이 있으면 추가하지 않음
            if ($('.jj-shortcuts-help-button').length) {
                return;
            }
            
            // 관리자 페이지에서만 표시
            if (!$('body').hasClass('wp-admin')) {
                return;
            }
            
            // 관련 페이지에서만 표시
            const isRelevantPage = $('.jj-admin-center-wrap, #jj-style-guide-form, .jj-labs-center-wrap').length > 0;
            if (!isRelevantPage) {
                return;
            }
            
            const buttonHTML = '<button class="jj-shortcuts-help-button" ' +
                             'aria-label="' + this.getDescription('help') + '" ' +
                             'title="' + this.getDescription('help') + ' (Ctrl+?)">' +
                             '⌨️</button>';
            
            $('body').append(buttonHTML);
            
            $('.jj-shortcuts-help-button').on('click', function() {
                KeyboardShortcuts.showHelpModal();
            });
        }
    };
    
    // DOM 준비 시 초기화
    $(document).ready(function() {
        KeyboardShortcuts.init();
    });
    
    // 전역으로 노출 (다른 스크립트에서 사용 가능)
    window.JJKeyboardShortcuts = KeyboardShortcuts;
    
})(jQuery);

