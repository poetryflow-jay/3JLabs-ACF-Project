/**
 * J&J Live Preview System - v5.0.3
 * 
 * 고급 실시간 미리보기 시스템
 * - 새로고침 없이 실시간 CSS 업데이트
 * - iframe 기반 프리뷰 옵션
 * - 모바일/태블릿/데스크톱 뷰 전환
 * - postMessage 기반 통신
 * 
 * @since 5.0.3
 * @version 5.0.3
 */
(function($) {
    'use strict';

    /**
     * 실시간 미리보기 매니저
     */
    const LivePreview = {
        // 프리뷰 창/iframe 참조
        previewWindow: null,
        previewIframe: null,
        
        // 프리뷰 모드: 'window' (새 창) 또는 'iframe' (인라인)
        previewMode: 'window',
        
        // 현재 뷰포트
        currentViewport: 'desktop',
        
        // 뷰포트 크기 정의
        viewportSizes: {
            desktop: { width: 1200, height: 800, name: '데스크톱' },
            tablet: { width: 768, height: 1024, name: '태블릿' },
            mobile: { width: 375, height: 667, name: '모바일' }
        },
        
        // CSS 업데이트 디바운싱 타이머
        cssUpdateTimer: null,
        
        // 현재 CSS 변수 상태
        currentCSSVars: {},
        
        // 프리뷰 URL
        previewUrl: '',
        
        // 현재 로케일
        currentLocale: 'en_US',
        
        /**
         * 초기화
         */
        init: function() {
            // WordPress 로케일 가져오기
            if (typeof jj_admin_params !== 'undefined' && jj_admin_params.locale) {
                this.currentLocale = jj_admin_params.locale;
            }
            
            // 프리뷰 URL 가져오기
            if (typeof jj_admin_params !== 'undefined' && jj_admin_params.preview_url) {
                this.previewUrl = jj_admin_params.preview_url;
            }
            
            // 프리뷰 모드 설정 (기본값: window)
            const savedMode = localStorage.getItem('jj_preview_mode');
            if (savedMode === 'iframe' || savedMode === 'window') {
                this.previewMode = savedMode;
            }
            
            // 이벤트 바인딩
            this.bindEvents();
            
            // 프리뷰 버튼 생성
            this.createPreviewButton();
            
            // postMessage 리스너 등록
            this.setupMessageListener();
        },
        
        /**
         * 이벤트 바인딩
         */
        bindEvents: function() {
            const self = this;
            
            // 색상 변경 감지
            $(document).on('change', '.jj-color-field, .jj-data-field', function() {
                self.handleSettingChange();
            });
            
            // 입력 필드 변경 감지 (디바운싱)
            let inputTimer = null;
            $(document).on('input', '.jj-data-field', function() {
                clearTimeout(inputTimer);
                inputTimer = setTimeout(function() {
                    self.handleSettingChange();
                }, 300);
            });
        },
        
        /**
         * 설정 변경 처리
         */
        handleSettingChange: function() {
            // 현재 설정에서 CSS 변수 추출
            const cssVars = this.extractCSSVariables();
            
            // 변경사항이 있으면 프리뷰 업데이트
            if (this.hasCSSChanged(cssVars)) {
                this.currentCSSVars = cssVars;
                this.updatePreviewCSS(cssVars);
            }
        },
        
        /**
         * CSS 변수 추출
         */
        extractCSSVariables: function() {
            const vars = {};
            const root = document.documentElement;
            
            // 모든 CSS 변수 수집
            const computedStyle = window.getComputedStyle(root);
            const allVars = {};
            
            // 현재 설정에서 CSS 변수 생성
            // currentSettings는 jj-style-guide-editor.js에서 정의됨
            const settings = typeof currentSettings !== 'undefined' ? currentSettings : 
                           (typeof jj_admin_params !== 'undefined' && jj_admin_params.settings ? jj_admin_params.settings : {});
            
            if (settings && Object.keys(settings).length > 0) {
                // 팔레트 시스템
                if (settings.palettes) {
                    if (settings.palettes.brand) {
                        vars['--jj-primary-color'] = settings.palettes.brand.primary_color || '';
                        vars['--jj-primary-color-hover'] = settings.palettes.brand.primary_color_hover || '';
                        vars['--jj-secondary-color'] = settings.palettes.brand.secondary_color || '';
                        vars['--jj-secondary-color-hover'] = settings.palettes.brand.secondary_color_hover || '';
                    }
                    if (settings.palettes.system) {
                        vars['--jj-bg-color'] = settings.palettes.system.background_color || '';
                        vars['--jj-text-color'] = settings.palettes.system.text_color || '';
                        vars['--jj-link-color'] = settings.palettes.system.link_color || '';
                    }
                }
                
                // 버튼 스타일
                if (settings.buttons) {
                    ['primary', 'secondary', 'text'].forEach(function(type) {
                        if (settings.buttons[type]) {
                            const btn = settings.buttons[type];
                            vars['--jj-btn-' + type + '-bg'] = btn.background_color || '';
                            vars['--jj-btn-' + type + '-text'] = btn.text_color || '';
                            vars['--jj-btn-' + type + '-border'] = btn.border_color || '';
                            vars['--jj-btn-' + type + '-bg-hover'] = btn.background_color_hover || '';
                            vars['--jj-btn-' + type + '-text-hover'] = btn.text_color_hover || '';
                            vars['--jj-btn-' + type + '-border-hover'] = btn.border_color_hover || '';
                            vars['--jj-btn-' + type + '-border-radius'] = (btn.border_radius || '4') + 'px';
                            
                            // Shadow
                            if (btn.shadow) {
                                const shadow = btn.shadow;
                                vars['--jj-btn-' + type + '-shadow'] = 
                                    (shadow.x || '0') + 'px ' +
                                    (shadow.y || '0') + 'px ' +
                                    (shadow.blur || '0') + 'px ' +
                                    (shadow.spread || '0') + 'px ' +
                                    (shadow.color || 'rgba(0,0,0,0)');
                            }
                        }
                    });
                }
                
                // 타이포그래피
                if (settings.typography) {
                    ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'].forEach(function(tag) {
                        if (settings.typography[tag]) {
                            const typo = settings.typography[tag];
                            vars['--jj-font-' + tag + '-family'] = typo.font_family || '';
                            vars['--jj-font-' + tag + '-size'] = (typo.font_size && typo.font_size.desktop ? typo.font_size.desktop : '') + 'px';
                            vars['--jj-font-' + tag + '-weight'] = typo.font_weight || '';
                            vars['--jj-font-' + tag + '-line-height'] = (typo.line_height || '1.5') + 'em';
                        }
                    });
                }
                
                // 폼 스타일
                if (settings.forms) {
                    if (settings.forms.label) {
                        const label = settings.forms.label;
                        vars['--jj-form-label-color'] = label.color || '';
                        vars['--jj-form-label-size'] = (label.font_size || '14') + 'px';
                    }
                    if (settings.forms.input) {
                        const input = settings.forms.input;
                        vars['--jj-form-input-border'] = input.border_color || '';
                        vars['--jj-form-input-border-radius'] = (input.border_radius || '4') + 'px';
                        vars['--jj-form-input-padding'] = (input.padding || '8') + 'px';
                    }
                }
            }
            
            return vars;
        },
        
        /**
         * CSS 변경 여부 확인
         */
        hasCSSChanged: function(newVars) {
            const oldVars = this.currentCSSVars;
            
            // 키 개수 비교
            const oldKeys = Object.keys(oldVars).sort();
            const newKeys = Object.keys(newVars).sort();
            
            if (oldKeys.length !== newKeys.length) {
                return true;
            }
            
            // 값 비교
            for (let i = 0; i < oldKeys.length; i++) {
                if (oldKeys[i] !== newKeys[i] || oldVars[oldKeys[i]] !== newVars[newKeys[i]]) {
                    return true;
                }
            }
            
            return false;
        },
        
        /**
         * 프리뷰 CSS 업데이트 (새로고침 없이)
         */
        updatePreviewCSS: function(cssVars) {
            if (!this.isPreviewOpen()) {
                return;
            }
            
            // 디바운싱: 100ms 이내 여러 변경이 있으면 마지막에 한 번만 업데이트
            clearTimeout(this.cssUpdateTimer);
            this.cssUpdateTimer = setTimeout(() => {
                this.applyCSSToPreview(cssVars);
            }, 100);
        },
        
        /**
         * 프리뷰에 CSS 적용
         */
        applyCSSToPreview: function(cssVars) {
            if (!this.isPreviewOpen()) {
                return;
            }
            
            // CSS 변수를 문자열로 변환
            let cssText = ':root {\n';
            for (const key in cssVars) {
                if (cssVars[key]) {
                    cssText += '  ' + key + ': ' + cssVars[key] + ';\n';
                }
            }
            cssText += '}';
            
            // postMessage로 프리뷰 창에 전송
            const message = {
                type: 'jj-preview-css-update',
                css: cssText,
                cssVars: cssVars
            };
            
            try {
                if (this.previewMode === 'window' && this.previewWindow && !this.previewWindow.closed) {
                    this.previewWindow.postMessage(message, '*');
                } else if (this.previewMode === 'iframe' && this.previewIframe && this.previewIframe.contentWindow) {
                    this.previewIframe.contentWindow.postMessage(message, '*');
                }
            } catch (e) {
                console.warn('프리뷰 CSS 업데이트 실패:', e);
                // 폴백: 새로고침
                this.refreshPreview();
            }
        },
        
        /**
         * 프리뷰 새로고침 (폴백)
         */
        refreshPreview: function() {
            if (!this.isPreviewOpen()) {
                return;
            }
            
            try {
                if (this.previewMode === 'window' && this.previewWindow && !this.previewWindow.closed) {
                    this.previewWindow.location.reload();
                } else if (this.previewMode === 'iframe' && this.previewIframe) {
                    this.previewIframe.src = this.previewIframe.src;
                }
            } catch (e) {
                console.warn('프리뷰 새로고침 실패:', e);
            }
        },
        
        /**
         * 프리뷰 열기
         */
        openPreview: function(mode) {
            if (mode) {
                this.previewMode = mode;
                localStorage.setItem('jj_preview_mode', mode);
            }
            
            if (!this.previewUrl) {
                const msg = this.currentLocale === 'ko_KR' 
                    ? '프리뷰 페이지가 설정되지 않았습니다. 스타일 가이드 설정 페이지가 생성되었는지 확인하세요.'
                    : 'Preview page is not configured. Please check if the style guide settings page has been created.';
                alert(msg);
                return;
            }
            
            if (this.previewMode === 'iframe') {
                this.openPreviewIframe();
            } else {
                this.openPreviewWindow();
            }
        },
        
        /**
         * 새 창으로 프리뷰 열기
         */
        openPreviewWindow: function() {
            // 기존 창이 있으면 포커스
            if (this.previewWindow && !this.previewWindow.closed) {
                this.previewWindow.focus();
                return;
            }
            
            const viewport = this.viewportSizes[this.currentViewport];
            const width = Math.min(viewport.width + 100, window.screen.width * 0.95);
            const height = Math.min(viewport.height + 100, window.screen.height * 0.95);
            const left = (window.screen.width - width) / 2;
            const top = (window.screen.height - height) / 2;
            
            this.previewWindow = window.open(
                this.previewUrl,
                'jj-style-preview',
                'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',toolbar=no,menubar=no,scrollbars=yes,resizable=yes'
            );
            
            if (this.previewWindow) {
                // 프리뷰 창이 로드되면 초기 CSS 전송
                const self = this;
                this.previewWindow.addEventListener('load', function() {
                    setTimeout(function() {
                        const cssVars = self.extractCSSVariables();
                        self.currentCSSVars = cssVars;
                        self.applyCSSToPreview(cssVars);
                        self.addViewportControlsToWindow(self.previewWindow);
                    }, 500);
                });
                
                this.updatePreviewButtonState(true);
            }
        },
        
        /**
         * iframe으로 프리뷰 열기
         */
        openPreviewIframe: function() {
            // 기존 iframe이 있으면 제거
            if (this.previewIframe) {
                this.previewIframe.remove();
            }
            
            // iframe 컨테이너 생성
            if (!$('#jj-preview-iframe-container').length) {
                const container = $('<div id="jj-preview-iframe-container" style="position: fixed; top: 32px; right: 0; width: 50%; height: calc(100vh - 32px); background: #fff; border-left: 2px solid #c3c4c7; z-index: 100000; display: flex; flex-direction: column; box-shadow: -2px 0 10px rgba(0,0,0,0.1);"></div>');
                
                // 헤더
                const header = $('<div style="padding: 10px 15px; background: #f0f0f1; border-bottom: 1px solid #c3c4c7; display: flex; justify-content: space-between; align-items: center;">' +
                    '<h3 style="margin: 0; font-size: 14px; font-weight: 600;">' + 
                    (this.currentLocale === 'ko_KR' ? '실시간 미리보기' : 'Live Preview') +
                    '</h3>' +
                    '<button type="button" class="jj-preview-close" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #50575e;">×</button>' +
                    '</div>');
                
                // iframe
                const iframe = $('<iframe id="jj-preview-iframe" style="flex: 1; border: none; width: 100%;"></iframe>');
                
                // 뷰포트 컨트롤
                const viewportControls = this.createViewportControls();
                
                container.append(header, viewportControls, iframe);
                $('body').append(container);
                
                // 닫기 버튼 이벤트
                header.find('.jj-preview-close').on('click', () => {
                    this.closePreview();
                });
                
                this.previewIframe = iframe[0];
            }
            
            // iframe 로드
            this.previewIframe.src = this.previewUrl;
            
            // iframe 로드 완료 후 초기 CSS 전송
            const self = this;
            $(this.previewIframe).on('load', function() {
                setTimeout(function() {
                    const cssVars = self.extractCSSVariables();
                    self.currentCSSVars = cssVars;
                    self.applyCSSToPreview(cssVars);
                }, 500);
            });
            
            this.updatePreviewButtonState(true);
        },
        
        /**
         * 프리뷰 닫기
         */
        closePreview: function() {
            if (this.previewMode === 'window' && this.previewWindow && !this.previewWindow.closed) {
                this.previewWindow.close();
                this.previewWindow = null;
            } else if (this.previewMode === 'iframe' && $('#jj-preview-iframe-container').length) {
                $('#jj-preview-iframe-container').remove();
                this.previewIframe = null;
            }
            
            this.updatePreviewButtonState(false);
        },
        
        /**
         * 프리뷰 열림 여부 확인
         */
        isPreviewOpen: function() {
            if (this.previewMode === 'window') {
                return this.previewWindow && !this.previewWindow.closed;
            } else {
                return this.previewIframe && $('#jj-preview-iframe-container').is(':visible');
            }
        },
        
        /**
         * 뷰포트 변경
         */
        changeViewport: function(viewport) {
            if (!this.viewportSizes[viewport]) {
                return;
            }
            
            this.currentViewport = viewport;
            
            if (this.previewMode === 'window' && this.previewWindow && !this.previewWindow.closed) {
                const size = this.viewportSizes[viewport];
                const width = Math.min(size.width + 100, window.screen.width * 0.95);
                const height = Math.min(size.height + 100, window.screen.height * 0.95);
                this.previewWindow.resizeTo(width, height);
                this.adjustViewportMeta(this.previewWindow, viewport);
            } else if (this.previewMode === 'iframe' && this.previewIframe) {
                this.adjustViewportMeta(this.previewIframe.contentWindow, viewport);
            }
            
            // 뷰포트 컨트롤 업데이트
            this.updateViewportControls();
        },
        
        /**
         * 뷰포트 메타 태그 조정
         */
        adjustViewportMeta: function(win, viewport) {
            if (!win || !win.document) {
                return;
            }
            
            try {
                const size = this.viewportSizes[viewport];
                const $doc = $(win.document);
                
                // viewport 메타 태그 업데이트
                let $viewportMeta = $doc.find('meta[name="viewport"]');
                if ($viewportMeta.length) {
                    $viewportMeta.attr('content', 'width=' + size.width + ', initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
                } else {
                    const meta = win.document.createElement('meta');
                    meta.name = 'viewport';
                    meta.content = 'width=' + size.width + ', initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
                    win.document.head.appendChild(meta);
                }
                
                // body 최대 너비 조정
                let $style = $doc.find('#jj-preview-viewport-style');
                if (!$style.length) {
                    $style = $('<style id="jj-preview-viewport-style"></style>');
                    $doc.find('head').append($style);
                }
                $style.html('body { max-width: ' + size.width + 'px; margin: 0 auto; box-shadow: 0 0 20px rgba(0,0,0,0.3); }');
            } catch (e) {
                console.warn('뷰포트 조정 실패:', e);
            }
        },
        
        /**
         * 뷰포트 컨트롤 생성
         */
        createViewportControls: function() {
            const self = this;
            const controls = $('<div class="jj-preview-viewport-controls" style="padding: 8px 15px; background: #f0f0f1; border-bottom: 1px solid #c3c4c7; display: flex; gap: 5px; align-items: center;"></div>');
            
            ['desktop', 'tablet', 'mobile'].forEach(function(viewport) {
                const size = self.viewportSizes[viewport];
                const isActive = viewport === self.currentViewport;
                const btn = $('<button type="button" class="button button-small jj-preview-viewport-btn" data-viewport="' + viewport + '" ' +
                    'title="' + size.name + '" ' +
                    (isActive ? 'style="background: #2271b1; color: #fff;"' : '') +
                    '><span class="dashicons dashicons-' + 
                    (viewport === 'desktop' ? 'desktop' : viewport === 'tablet' ? 'tablet' : 'smartphone') +
                    '"></span></button>');
                
                btn.on('click', function() {
                    self.changeViewport(viewport);
                });
                
                controls.append(btn);
            });
            
            return controls;
        },
        
        /**
         * 새 창에 뷰포트 컨트롤 추가
         */
        addViewportControlsToWindow: function(win) {
            if (!win || !win.document || win.jjPreviewControlsAdded) {
                return;
            }
            
            try {
                const controlsHtml = '<div id="jj-preview-viewport-overlay" style="position: fixed; top: 10px; right: 10px; z-index: 999999; background: rgba(0,0,0,0.8); padding: 8px; border-radius: 4px; display: flex; gap: 5px;">' +
                    '<button type="button" class="jj-preview-viewport-btn" data-viewport="desktop" style="background: #2271b1; color: #fff; border: none; padding: 6px 10px; border-radius: 3px; cursor: pointer;" title="데스크톱"><span class="dashicons dashicons-desktop"></span></button>' +
                    '<button type="button" class="jj-preview-viewport-btn" data-viewport="tablet" style="background: #2271b1; color: #fff; border: none; padding: 6px 10px; border-radius: 3px; cursor: pointer;" title="태블릿"><span class="dashicons dashicons-tablet"></span></button>' +
                    '<button type="button" class="jj-preview-viewport-btn" data-viewport="mobile" style="background: #2271b1; color: #fff; border: none; padding: 6px 10px; border-radius: 3px; cursor: pointer;" title="모바일"><span class="dashicons dashicons-smartphone"></span></button>' +
                    '</div>';
                
                const tempDiv = win.document.createElement('div');
                tempDiv.innerHTML = controlsHtml;
                const $controls = $(tempDiv.firstElementChild);
                $(win.document.body).append($controls);
                
                const self = this;
                $controls.find('.jj-preview-viewport-btn').on('click', function() {
                    const viewport = $(this).data('viewport');
                    if (win.opener) {
                        win.opener.postMessage({ type: 'jj-preview-viewport-change', viewport: viewport }, '*');
                    }
                });
                
                win.jjPreviewControlsAdded = true;
            } catch (e) {
                console.warn('뷰포트 컨트롤 추가 실패:', e);
            }
        },
        
        /**
         * 뷰포트 컨트롤 업데이트
         */
        updateViewportControls: function() {
            $('.jj-preview-viewport-btn').removeClass('active');
            $('.jj-preview-viewport-btn[data-viewport="' + this.currentViewport + '"]').addClass('active')
                .css('background', '#2271b1').css('color', '#fff');
            $('.jj-preview-viewport-btn').not('[data-viewport="' + this.currentViewport + '"]')
                .css('background', '').css('color', '');
        },
        
        /**
         * 프리뷰 버튼 생성
         */
        createPreviewButton: function() {
            // 기존 버튼이 있으면 제거
            $('#jj-preview-button').remove();
            
            const button = $('<button type="button" id="jj-preview-button" class="button button-primary" ' +
                'style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">' +
                '<span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> ' +
                (this.currentLocale === 'ko_KR' ? '실시간 미리보기' : 'Live Preview') +
                '</button>');
            
            button.on('click', () => {
                if (this.isPreviewOpen()) {
                    this.closePreview();
                } else {
                    // 모드 선택 다이얼로그
                    this.showPreviewModeDialog();
                }
            });
            
            $('body').append(button);
        },
        
        /**
         * 프리뷰 모드 선택 다이얼로그
         */
        showPreviewModeDialog: function() {
            const self = this;
            const dialog = $('<div id="jj-preview-mode-dialog" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 100001; display: flex; align-items: center; justify-content: center;">' +
                '<div style="background: #fff; padding: 30px; border-radius: 8px; max-width: 500px; width: 90%;">' +
                '<h2 style="margin-top: 0;">' + (this.currentLocale === 'ko_KR' ? '미리보기 모드 선택' : 'Select Preview Mode') + '</h2>' +
                '<p>' + (this.currentLocale === 'ko_KR' ? '새 창으로 열기 또는 페이지 내 iframe으로 표시할 수 있습니다.' : 'You can open in a new window or display in an iframe on the page.') + '</p>' +
                '<div style="display: flex; gap: 10px; margin-top: 20px;">' +
                '<button type="button" class="button button-primary jj-preview-mode-btn" data-mode="window" style="flex: 1;">' +
                '<span class="dashicons dashicons-external" style="margin-top: 4px;"></span><br>' +
                (this.currentLocale === 'ko_KR' ? '새 창' : 'New Window') +
                '</button>' +
                '<button type="button" class="button button-primary jj-preview-mode-btn" data-mode="iframe" style="flex: 1;">' +
                '<span class="dashicons dashicons-editor-code" style="margin-top: 4px;"></span><br>' +
                (this.currentLocale === 'ko_KR' ? '인라인 (iframe)' : 'Inline (iframe)') +
                '</button>' +
                '</div>' +
                '<button type="button" class="button jj-preview-mode-cancel" style="margin-top: 15px; width: 100%;">' +
                (this.currentLocale === 'ko_KR' ? '취소' : 'Cancel') +
                '</button>' +
                '</div></div>');
            
            dialog.find('.jj-preview-mode-btn').on('click', function() {
                const mode = $(this).data('mode');
                dialog.remove();
                self.openPreview(mode);
            });
            
            dialog.find('.jj-preview-mode-cancel, .jj-preview-mode-dialog').on('click', function(e) {
                if ($(e.target).closest('.jj-preview-mode-dialog > div').length === 0) {
                    dialog.remove();
                }
            });
            
            $('body').append(dialog);
        },
        
        /**
         * 프리뷰 버튼 상태 업데이트
         */
        updatePreviewButtonState: function(isOpen) {
            const button = $('#jj-preview-button');
            if (isOpen) {
                button.addClass('active')
                    .html('<span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> ' +
                          (this.currentLocale === 'ko_KR' ? '프리뷰 닫기' : 'Close Preview'));
            } else {
                button.removeClass('active')
                    .html('<span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> ' +
                          (this.currentLocale === 'ko_KR' ? '실시간 미리보기' : 'Live Preview'));
            }
        },
        
        /**
         * postMessage 리스너 설정
         */
        setupMessageListener: function() {
            const self = this;
            
            window.addEventListener('message', function(e) {
                // 보안: 같은 도메인에서만 메시지 수신
                if (e.origin !== window.location.origin) {
                    return;
                }
                
                if (e.data && e.data.type === 'jj-preview-viewport-change' && e.data.viewport) {
                    self.changeViewport(e.data.viewport);
                } else if (e.data && e.data.type === 'jj-preview-ready') {
                    // 프리뷰 페이지가 준비되면 초기 CSS 전송
                    const cssVars = self.extractCSSVariables();
                    self.currentCSSVars = cssVars;
                    self.applyCSSToPreview(cssVars);
                }
            });
        }
    };
    
    // DOM 준비 시 초기화
    $(document).ready(function() {
        LivePreview.init();
        
        // 기존 프리뷰 시스템과 통합
        // refreshPreviewIfOpen 함수가 있으면 LivePreview 시스템 사용
        if (typeof refreshPreviewIfOpen === 'function') {
            // 기존 함수를 래핑하여 LivePreview 시스템 사용
            const originalRefreshPreview = window.refreshPreviewIfOpen;
            window.refreshPreviewIfOpen = function() {
                // LivePreview 시스템이 활성화되어 있으면 사용
                if (LivePreview.isPreviewOpen()) {
                    LivePreview.handleSettingChange();
                } else {
                    // 기존 시스템 사용 (폴백)
                    if (originalRefreshPreview) {
                        originalRefreshPreview();
                    }
                }
            };
        } else {
            // refreshPreviewIfOpen 함수가 없으면 생성
            window.refreshPreviewIfOpen = function() {
                if (LivePreview.isPreviewOpen()) {
                    LivePreview.handleSettingChange();
                }
            };
        }
    });
    
    // 전역으로 노출
    window.JJLivePreview = LivePreview;
    
})(jQuery);

