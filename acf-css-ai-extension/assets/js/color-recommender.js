/**
 * JJ AI Color Recommender - Frontend JavaScript
 * Phase 49-1: AI 컬러 팔레트 추천 기능
 *
 * @package ACF_CSS_AI_Extension
 * @since 3.4.0
 */
(function($) {
    'use strict';

    // State
    const state = {
        selectedIndustry: null,
        selectedMood: 'professional',
        currentPalette: null,
        variations: [],
        harmonyScore: null
    };

    // DOM Cache
    let $container, $industryCards, $moodSelect, $palettePreview,
        $harmonyScore, $variationsGrid, $applyBtn, $exportBtn;

    /**
     * Initialize the Color Recommender UI
     */
    function init() {
        $container = $('#jj-color-recommender');
        if (!$container.length) return;

        cacheElements();
        bindEvents();
        loadIndustryPalettes();
    }

    /**
     * Cache DOM elements
     */
    function cacheElements() {
        $industryCards = $container.find('.jj-industry-cards');
        $moodSelect = $container.find('#jj-color-mood');
        $palettePreview = $container.find('.jj-palette-preview');
        $harmonyScore = $container.find('.jj-harmony-score');
        $variationsGrid = $container.find('.jj-variations-grid');
        $applyBtn = $container.find('#jj-apply-palette');
        $exportBtn = $container.find('#jj-export-palette');
    }

    /**
     * Bind event handlers
     */
    function bindEvents() {
        // Industry card selection
        $container.on('click', '.jj-industry-card', function() {
            const $card = $(this);
            const industry = $card.data('industry');

            // Toggle selection
            $container.find('.jj-industry-card').removeClass('selected');
            $card.addClass('selected');

            state.selectedIndustry = industry;
            recommendPalette();
        });

        // Mood change
        $moodSelect.on('change', function() {
            state.selectedMood = $(this).val();
            if (state.selectedIndustry) {
                recommendPalette();
            }
        });

        // Apply palette
        $applyBtn.on('click', function() {
            if (!state.currentPalette) {
                alert(jjColorRec.i18n.no_palette || '팔레트를 먼저 선택해주세요.');
                return;
            }
            applyPalette();
        });

        // Export palette
        $exportBtn.on('click', function() {
            if (!state.currentPalette) {
                alert(jjColorRec.i18n.no_palette || '팔레트를 먼저 선택해주세요.');
                return;
            }
            exportPalette();
        });

        // Variation click
        $container.on('click', '.jj-variation-card', function() {
            const $card = $(this);
            const variationType = $card.data('type');
            const baseColor = state.currentPalette?.primary || '#0073e6';

            generateVariations(baseColor, variationType);
        });

        // Color swatch click (copy to clipboard)
        $container.on('click', '.jj-color-swatch', function() {
            const color = $(this).data('color');
            copyToClipboard(color);
            showToast(jjColorRec.i18n.copied || '복사됨: ' + color);
        });
    }

    /**
     * Load industry palettes from server
     */
    function loadIndustryPalettes() {
        $.ajax({
            url: jjColorRec.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_ai_get_industry_palettes',
                nonce: jjColorRec.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    renderIndustryCards(response.data);
                }
            },
            error: function() {
                console.error('Failed to load industry palettes');
            }
        });
    }

    /**
     * Render industry selection cards
     */
    function renderIndustryCards(palettes) {
        const industries = {
            'law': { name: '법률', icon: 'dashicons-clipboard' },
            'medical': { name: '의료/헬스케어', icon: 'dashicons-heart' },
            'tech': { name: 'IT/테크', icon: 'dashicons-laptop' },
            'finance': { name: '금융', icon: 'dashicons-chart-bar' },
            'fashion': { name: '패션/뷰티', icon: 'dashicons-art' },
            'food': { name: '음식/레스토랑', icon: 'dashicons-carrot' },
            'education': { name: '교육', icon: 'dashicons-welcome-learn-more' },
            'creative': { name: '크리에이티브', icon: 'dashicons-admin-customizer' },
            'realestate': { name: '부동산', icon: 'dashicons-building' },
            'nonprofit': { name: '비영리', icon: 'dashicons-groups' }
        };

        let html = '';

        for (const [key, info] of Object.entries(industries)) {
            const palette = palettes[key] || {};
            const primary = palette.primary || '#666666';
            const secondary = palette.secondary || '#999999';

            html += `
                <div class="jj-industry-card" data-industry="${key}">
                    <div class="jj-industry-icon">
                        <span class="dashicons ${info.icon}"></span>
                    </div>
                    <div class="jj-industry-name">${info.name}</div>
                    <div class="jj-industry-colors">
                        <span class="jj-mini-swatch" style="background-color: ${primary}"></span>
                        <span class="jj-mini-swatch" style="background-color: ${secondary}"></span>
                    </div>
                </div>
            `;
        }

        $industryCards.html(html);
    }

    /**
     * Request palette recommendation from server
     */
    function recommendPalette() {
        if (!state.selectedIndustry) return;

        setLoading(true);

        $.ajax({
            url: jjColorRec.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_ai_recommend_palette',
                nonce: jjColorRec.nonce,
                industry: state.selectedIndustry,
                mood: state.selectedMood,
                preferences: {}
            },
            success: function(response) {
                if (response.success && response.data) {
                    state.currentPalette = response.data.palette;
                    state.harmonyScore = response.data.harmony_score;

                    renderPalettePreview(response.data);
                    analyzeHarmony();
                    generateVariations(state.currentPalette.primary, 'complementary');
                }
            },
            error: function() {
                alert(jjColorRec.i18n.error || '팔레트 추천 중 오류가 발생했습니다.');
            },
            complete: function() {
                setLoading(false);
            }
        });
    }

    /**
     * Render palette preview
     */
    function renderPalettePreview(data) {
        const palette = data.palette;
        const description = data.description || '';

        let html = `
            <div class="jj-palette-info">
                <h4>${getIndustryName(state.selectedIndustry)} - ${getMoodName(state.selectedMood)}</h4>
                <p class="description">${description}</p>
            </div>
            <div class="jj-palette-swatches">
        `;

        // Main colors
        const colors = [
            { key: 'primary', label: 'Primary' },
            { key: 'secondary', label: 'Secondary' },
            { key: 'accent', label: 'Accent' },
            { key: 'background', label: 'Background' },
            { key: 'text', label: 'Text' }
        ];

        for (const color of colors) {
            const value = palette[color.key] || '#CCCCCC';
            html += `
                <div class="jj-color-swatch" data-color="${value}" title="${color.label}: ${value}">
                    <div class="jj-swatch-color" style="background-color: ${value}"></div>
                    <div class="jj-swatch-info">
                        <span class="jj-swatch-label">${color.label}</span>
                        <span class="jj-swatch-value">${value}</span>
                    </div>
                </div>
            `;
        }

        html += '</div>';

        // Contrast info
        if (data.wcag_compliance) {
            html += `
                <div class="jj-wcag-info">
                    <span class="dashicons dashicons-${data.wcag_compliance.passes_aa ? 'yes' : 'no'}"></span>
                    WCAG AA: ${data.wcag_compliance.passes_aa ? '통과' : '미달'}
                    (대비율: ${data.wcag_compliance.ratio.toFixed(2)}:1)
                </div>
            `;
        }

        $palettePreview.html(html).show();
        $applyBtn.prop('disabled', false);
        $exportBtn.prop('disabled', false);
    }

    /**
     * Analyze color harmony
     */
    function analyzeHarmony() {
        if (!state.currentPalette) return;

        const colors = [
            state.currentPalette.primary,
            state.currentPalette.secondary,
            state.currentPalette.accent
        ].filter(Boolean);

        $.ajax({
            url: jjColorRec.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_ai_analyze_harmony',
                nonce: jjColorRec.nonce,
                colors: colors
            },
            success: function(response) {
                if (response.success && response.data) {
                    renderHarmonyScore(response.data);
                }
            }
        });
    }

    /**
     * Render harmony score
     */
    function renderHarmonyScore(data) {
        const score = data.overall_score || 0;
        const type = data.harmony_type || 'unknown';
        const scorePercent = Math.round(score * 100);

        let scoreClass = 'low';
        if (score >= 0.7) scoreClass = 'high';
        else if (score >= 0.4) scoreClass = 'medium';

        const html = `
            <div class="jj-harmony-meter">
                <div class="jj-harmony-bar">
                    <div class="jj-harmony-fill ${scoreClass}" style="width: ${scorePercent}%"></div>
                </div>
                <div class="jj-harmony-label">
                    <span class="score">${scorePercent}%</span>
                    <span class="type">${getHarmonyTypeName(type)}</span>
                </div>
            </div>
            <div class="jj-harmony-details">
                ${data.details ? `<p>${data.details}</p>` : ''}
            </div>
        `;

        $harmonyScore.html(html).show();
    }

    /**
     * Generate color variations
     */
    function generateVariations(baseColor, type) {
        $.ajax({
            url: jjColorRec.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_ai_generate_variations',
                nonce: jjColorRec.nonce,
                base_color: baseColor,
                variation_type: type
            },
            success: function(response) {
                if (response.success && response.data) {
                    state.variations = response.data.variations;
                    renderVariations(response.data);
                }
            }
        });
    }

    /**
     * Render color variations
     */
    function renderVariations(data) {
        const variations = data.variations || [];
        const type = data.type || 'complementary';

        let html = `
            <div class="jj-variations-header">
                <h4>색상 변형</h4>
                <div class="jj-variation-types">
                    <button class="jj-variation-btn ${type === 'complementary' ? 'active' : ''}" data-type="complementary">보색</button>
                    <button class="jj-variation-btn ${type === 'analogous' ? 'active' : ''}" data-type="analogous">유사색</button>
                    <button class="jj-variation-btn ${type === 'triadic' ? 'active' : ''}" data-type="triadic">삼원색</button>
                    <button class="jj-variation-btn ${type === 'shades' ? 'active' : ''}" data-type="shades">명도 변형</button>
                </div>
            </div>
            <div class="jj-variations-list">
        `;

        for (const color of variations) {
            html += `
                <div class="jj-variation-swatch" data-color="${color}">
                    <div class="jj-var-color" style="background-color: ${color}"></div>
                    <span class="jj-var-value">${color}</span>
                </div>
            `;
        }

        html += '</div>';

        $variationsGrid.html(html).show();

        // Rebind variation type buttons
        $variationsGrid.find('.jj-variation-btn').on('click', function() {
            const newType = $(this).data('type');
            const baseColor = state.currentPalette?.primary || '#0073e6';

            $variationsGrid.find('.jj-variation-btn').removeClass('active');
            $(this).addClass('active');

            generateVariations(baseColor, newType);
        });
    }

    /**
     * Apply palette to ACF CSS Master settings
     */
    function applyPalette() {
        if (!state.currentPalette) return;

        if (!confirm(jjColorRec.i18n.apply_confirm || '이 팔레트를 사이트에 적용하시겠습니까?')) {
            return;
        }

        setLoading(true);

        $.ajax({
            url: jjColorRec.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_ai_apply_style',
                nonce: jjColorRec.nonce,
                settings_patch: buildSettingsPatch()
            },
            success: function(response) {
                if (response.success) {
                    alert(jjColorRec.i18n.applied || '팔레트가 적용되었습니다!');
                    // Optionally reload to see changes
                    if (confirm(jjColorRec.i18n.reload_confirm || '변경사항을 보려면 페이지를 새로고침하세요. 지금 새로고침할까요?')) {
                        location.reload();
                    }
                } else {
                    alert(response.data?.message || jjColorRec.i18n.error);
                }
            },
            error: function() {
                alert(jjColorRec.i18n.error || '적용 중 오류가 발생했습니다.');
            },
            complete: function() {
                setLoading(false);
            }
        });
    }

    /**
     * Build settings patch from current palette
     */
    function buildSettingsPatch() {
        const p = state.currentPalette;
        if (!p) return {};

        return {
            palettes: {
                brand: {
                    primary_color: p.primary,
                    primary_color_hover: p.primary_hover || shadeColor(p.primary, -15),
                    secondary_color: p.secondary,
                    secondary_color_hover: p.secondary_hover || shadeColor(p.secondary, -15)
                },
                system: {
                    site_bg: p.background,
                    content_bg: p.background,
                    text_color: p.text,
                    link_color: p.primary
                }
            },
            buttons: {
                primary: {
                    background_color: p.primary,
                    background_color_hover: p.primary_hover || shadeColor(p.primary, -15),
                    border_color: p.primary,
                    border_color_hover: p.primary_hover || shadeColor(p.primary, -15)
                }
            },
            forms: {
                field: {
                    border_color_focus: p.primary
                }
            }
        };
    }

    /**
     * Export palette as JSON/CSS
     */
    function exportPalette() {
        if (!state.currentPalette) return;

        const format = prompt(jjColorRec.i18n.export_format || '내보내기 형식을 선택하세요:\n1. JSON\n2. CSS Variables\n3. SCSS Variables', '1');

        let output = '';
        const p = state.currentPalette;

        switch(format) {
            case '1':
            case 'json':
                output = JSON.stringify(p, null, 2);
                break;
            case '2':
            case 'css':
                output = `:root {\n` +
                    `  --color-primary: ${p.primary};\n` +
                    `  --color-secondary: ${p.secondary};\n` +
                    `  --color-accent: ${p.accent};\n` +
                    `  --color-background: ${p.background};\n` +
                    `  --color-text: ${p.text};\n` +
                    `}`;
                break;
            case '3':
            case 'scss':
                output = `$color-primary: ${p.primary};\n` +
                    `$color-secondary: ${p.secondary};\n` +
                    `$color-accent: ${p.accent};\n` +
                    `$color-background: ${p.background};\n` +
                    `$color-text: ${p.text};`;
                break;
            default:
                return;
        }

        // Copy to clipboard
        copyToClipboard(output);
        alert(jjColorRec.i18n.exported || '팔레트가 클립보드에 복사되었습니다!');
    }

    // ========== Utility Functions ==========

    function setLoading(on) {
        if (on) {
            $container.addClass('loading');
        } else {
            $container.removeClass('loading');
        }
    }

    function getIndustryName(key) {
        const names = {
            'law': '법률',
            'medical': '의료/헬스케어',
            'tech': 'IT/테크',
            'finance': '금융',
            'fashion': '패션/뷰티',
            'food': '음식/레스토랑',
            'education': '교육',
            'creative': '크리에이티브',
            'realestate': '부동산',
            'nonprofit': '비영리'
        };
        return names[key] || key;
    }

    function getMoodName(key) {
        const names = {
            'professional': '프로페셔널',
            'playful': '플레이풀',
            'elegant': '우아함',
            'warm': '따뜻함',
            'cool': '차분함',
            'bold': '대담함',
            'soft': '부드러움',
            'dark': '다크'
        };
        return names[key] || key;
    }

    function getHarmonyTypeName(type) {
        const names = {
            'complementary': '보색 조화',
            'analogous': '유사색 조화',
            'triadic': '삼원색 조화',
            'split_complementary': '분할 보색 조화',
            'monochromatic': '단색 조화',
            'unknown': '분석 중'
        };
        return names[type] || type;
    }

    function shadeColor(color, percent) {
        const num = parseInt(color.replace('#', ''), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) + amt;
        const G = (num >> 8 & 0x00FF) + amt;
        const B = (num & 0x0000FF) + amt;

        return '#' + (0x1000000 +
            (R < 255 ? (R < 1 ? 0 : R) : 255) * 0x10000 +
            (G < 255 ? (G < 1 ? 0 : G) : 255) * 0x100 +
            (B < 255 ? (B < 1 ? 0 : B) : 255)
        ).toString(16).slice(1).toUpperCase();
    }

    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text);
        } else {
            // Fallback
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        }
    }

    function showToast(message) {
        const $toast = $('<div class="jj-toast">' + message + '</div>');
        $('body').append($toast);

        setTimeout(function() {
            $toast.addClass('show');
        }, 10);

        setTimeout(function() {
            $toast.removeClass('show');
            setTimeout(function() {
                $toast.remove();
            }, 300);
        }, 2000);
    }

    // Initialize on document ready
    $(document).ready(init);

})(jQuery);
