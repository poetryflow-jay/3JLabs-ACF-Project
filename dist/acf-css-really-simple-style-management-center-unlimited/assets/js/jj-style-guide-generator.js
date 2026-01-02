/**
 * JJ Style Guide Generator
 * 
 * [Phase 9.3] 스타일 가이드 자동 생성
 * 
 * @since 9.3.0
 */

(function($) {
    'use strict';
    
    const StyleGuideGenerator = {
        init: function() {
            this.createUI();
            this.bindEvents();
        },
        
        createUI: function() {
            // UI가 이미 있으면 스킵
            if ($('#jj-style-guide-generator').length > 0) {
                return;
            }
            
            const uiHtml = `
                <div id="jj-style-guide-generator" style="margin: 20px 0; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                    <h3 style="margin-top: 0;">
                        <span class="dashicons dashicons-admin-appearance" style="vertical-align: middle; margin-right: 5px;"></span>
                        스타일 가이드 자동 생성
                    </h3>
                    <p class="description">현재 설정된 스타일을 기반으로 스타일 가이드 문서를 자동으로 생성합니다.</p>
                    
                    <div style="margin: 20px 0;">
                        <h4>포함할 항목</h4>
                        <label style="display: block; margin: 10px 0;">
                            <input type="checkbox" name="include_palettes" checked> 색상 팔레트
                        </label>
                        <label style="display: block; margin: 10px 0;">
                            <input type="checkbox" name="include_typography" checked> 타이포그래피
                        </label>
                        <label style="display: block; margin: 10px 0;">
                            <input type="checkbox" name="include_buttons" checked> 버튼 스타일
                        </label>
                        <label style="display: block; margin: 10px 0;">
                            <input type="checkbox" name="include_forms" checked> 폼 스타일
                        </label>
                    </div>
                    
                    <div style="margin: 20px 0;">
                        <h4>출력 형식</h4>
                        <label style="display: block; margin: 10px 0;">
                            <input type="radio" name="format" value="html" checked> HTML
                        </label>
                        <label style="display: block; margin: 10px 0;">
                            <input type="radio" name="format" value="markdown"> Markdown
                        </label>
                        <label style="display: block; margin: 10px 0;">
                            <input type="radio" name="format" value="json"> JSON
                        </label>
                    </div>
                    
                    <div style="margin: 20px 0;">
                        <button type="button" class="button button-primary" id="jj-generate-style-guide">
                            <span class="dashicons dashicons-admin-page" style="vertical-align: middle; margin-right: 5px;"></span>
                            스타일 가이드 생성
                        </button>
                        <button type="button" class="button" id="jj-analyze-consistency">
                            <span class="dashicons dashicons-chart-line" style="vertical-align: middle; margin-right: 5px;"></span>
                            일관성 분석
                        </button>
                    </div>
                    
                    <div id="jj-style-guide-result" style="display: none; margin-top: 20px; padding: 15px; background: #f5f5f5; border-radius: 4px;">
                    </div>
                </div>
            `;
            
            // Admin Center의 Tools 탭이나 적절한 위치에 삽입
            $('.jj-admin-center-tab-content[data-tab="tools"]').append(uiHtml);
        },
        
        bindEvents: function() {
            const self = this;
            
            // 스타일 가이드 생성
            $(document).on('click', '#jj-generate-style-guide', function() {
                self.generateStyleGuide();
            });
            
            // 일관성 분석
            $(document).on('click', '#jj-analyze-consistency', function() {
                self.analyzeConsistency();
            });
        },
        
        generateStyleGuide: function() {
            const self = this;
            const $btn = $('#jj-generate-style-guide');
            const $result = $('#jj-style-guide-result');
            
            const options = {
                include_palettes: $('input[name="include_palettes"]').is(':checked'),
                include_typography: $('input[name="include_typography"]').is(':checked'),
                include_buttons: $('input[name="include_buttons"]').is(':checked'),
                include_forms: $('input[name="include_forms"]').is(':checked'),
                format: $('input[name="format"]:checked').val()
            };
            
            $btn.prop('disabled', true).html('<span class="spinner is-active" style="float: none; margin: 0 5px 0 0;"></span> 생성 중...');
            $result.hide();
            
            $.ajax({
                url: jjStyleGuideGenerator.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_generate_style_guide',
                    nonce: jjStyleGuideGenerator.nonce,
                    ...options
                },
                success: function(response) {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-admin-page" style="vertical-align: middle; margin-right: 5px;"></span> 스타일 가이드 생성');
                    
                    if (response.success) {
                        self.showStyleGuideResult(response.data);
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(jjStyleGuideGenerator.strings.generated, 'success');
                        }
                    } else {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '생성 실패', 'error');
                        }
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-admin-page" style="vertical-align: middle; margin-right: 5px;"></span> 스타일 가이드 생성');
                    if (JJUtils && JJUtils.showToast) {
                        JJUtils.showToast('오류가 발생했습니다.', 'error');
                    }
                }
            });
        },
        
        showStyleGuideResult: function(data) {
            const $result = $('#jj-style-guide-result');
            let content = '';
            
            if (data.format === 'html') {
                // 새 창에서 HTML 표시
                const newWindow = window.open('', '_blank');
                newWindow.document.write(data.guide);
                newWindow.document.close();
                
                content = `
                    <div style="padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                        <h4 style="margin-top: 0;">생성 완료</h4>
                        <p>스타일 가이드가 새 창에서 열렸습니다.</p>
                        <button type="button" class="button" onclick="navigator.clipboard.writeText(\`${data.guide.replace(/`/g, '\\`').replace(/\$/g, '\\$')}\`)">
                            HTML 복사
                        </button>
                    </div>
                `;
            } else {
                content = `
                    <div style="padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                        <h4 style="margin-top: 0;">생성된 스타일 가이드 (${data.format.toUpperCase()})</h4>
                        <textarea readonly style="width: 100%; height: 300px; font-family: monospace; font-size: 12px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">${$('<div>').text(data.guide).html()}</textarea>
                        <button type="button" class="button" onclick="navigator.clipboard.writeText(\`${data.guide.replace(/`/g, '\\`').replace(/\$/g, '\\$')}\`)">
                            복사
                        </button>
                    </div>
                `;
            }
            
            $result.html(content).show();
        },
        
        analyzeConsistency: function() {
            const self = this;
            const $btn = $('#jj-analyze-consistency');
            const $result = $('#jj-style-guide-result');
            
            $btn.prop('disabled', true).html('<span class="spinner is-active" style="float: none; margin: 0 5px 0 0;"></span> 분석 중...');
            $result.hide();
            
            $.ajax({
                url: jjStyleGuideGenerator.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_analyze_style_consistency',
                    nonce: jjStyleGuideGenerator.nonce
                },
                success: function(response) {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-chart-line" style="vertical-align: middle; margin-right: 5px;"></span> 일관성 분석');
                    
                    if (response.success) {
                        self.showConsistencyResult(response.data);
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(jjStyleGuideGenerator.strings.analysis_complete, 'success');
                        }
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-chart-line" style="vertical-align: middle; margin-right: 5px;"></span> 일관성 분석');
                }
            });
        },
        
        showConsistencyResult: function(data) {
            const $result = $('#jj-style-guide-result');
            let issuesHtml = '';
            
            if (data.issues && Object.keys(data.issues).length > 0) {
                issuesHtml = '<h4>발견된 이슈</h4>';
                Object.keys(data.issues).forEach(category => {
                    issuesHtml += `<h5>${category}</h5><ul>`;
                    data.issues[category].forEach(issue => {
                        const severityClass = issue.severity === 'error' ? 'notice-error' : 
                                            issue.severity === 'warning' ? 'notice-warning' : 'notice-info';
                        issuesHtml += `<li class="notice ${severityClass}" style="padding: 10px; margin: 5px 0;">${issue.message}</li>`;
                    });
                    issuesHtml += '</ul>';
                });
            } else {
                issuesHtml = '<p style="color: #00a32a;">✓ 일관성 문제가 발견되지 않았습니다!</p>';
            }
            
            const content = `
                <div style="padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                    <h4 style="margin-top: 0;">일관성 분석 결과</h4>
                    <p><strong>일관성 점수:</strong> <span style="font-size: 24px; font-weight: bold; color: ${data.score >= 80 ? '#00a32a' : data.score >= 60 ? '#dba617' : '#d63638'}">${data.score}</span> / 100</p>
                    ${issuesHtml}
                </div>
            `;
            
            $result.html(content).show();
        }
    };
    
    // 초기화
    $(document).ready(function() {
        if (typeof jjStyleGuideGenerator !== 'undefined') {
            StyleGuideGenerator.init();
        }
    });
    
})(jQuery);
