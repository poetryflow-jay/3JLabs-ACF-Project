(function($) {
    'use strict';

    function cfg() {
        return window.jjStyleGuideLive || null;
    }

    function esc(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function setStatus(text, type) {
        var $s = $('#jj-sg-live-status');
        if (!$s.length) return;
        $s.removeClass('is-ok is-warn is-err');
        if (type === 'ok') $s.addClass('is-ok');
        if (type === 'warn') $s.addClass('is-warn');
        if (type === 'err') $s.addClass('is-err');
        $s.text(text || '');
    }

    function getActiveDevice() {
        var v = $('#jj-sg-live-device').val();
        return v || 'desktop';
    }

    function getUnitSettings(data) {
        var unit = (data && data.typography_settings && data.typography_settings.unit) ? String(data.typography_settings.unit) : 'px';
        if (unit !== 'px' && unit !== 'rem' && unit !== 'em') unit = 'px';
        var basePx = 16;
        if (data && data.typography_settings && data.typography_settings.base_px) {
            var b = parseFloat(data.typography_settings.base_px);
            if (!isNaN(b) && b > 0) basePx = b;
        }
        return { unit: unit, basePx: basePx };
    }

    function formatFontSize(pxVal, unit, basePx) {
        var s = (pxVal !== undefined && pxVal !== null) ? String(pxVal) : '';
        if (!/^-?\d+(\.\d+)?$/.test(s)) return '';
        var px = parseFloat(s);
        if (isNaN(px)) return '';
        if (unit === 'rem' || unit === 'em') {
            var ratio = Math.round((px / basePx) * 10000) / 10000;
            return ratio + unit;
        }
        return px + 'px';
    }

    function getFontSizeForDevice(t, deviceKey) {
        if (!t) return '';
        if (t.font_sizes && t.font_sizes[deviceKey] !== undefined && t.font_sizes[deviceKey] !== null && String(t.font_sizes[deviceKey]) !== '') {
            return String(t.font_sizes[deviceKey]);
        }
        // 폴백: desktop → legacy font_size
        if (t.font_sizes && t.font_sizes.desktop !== undefined && t.font_sizes.desktop !== null && String(t.font_sizes.desktop) !== '') {
            return String(t.font_sizes.desktop);
        }
        if (t.font_size !== undefined && t.font_size !== null) {
            return String(t.font_size);
        }
        return '';
    }

    function applyCssVars(data) {
        if (!data || !data.colors) return;
        var root = document.documentElement;
        root.style.setProperty('--jj-sg-primary', data.colors.primary || '#2271b1');
        root.style.setProperty('--jj-sg-secondary', data.colors.secondary || '#50575e');
        root.style.setProperty('--jj-sg-accent', data.colors.accent || data.colors.primary || '#2271b1');
        root.style.setProperty('--jj-sg-bg', data.colors.bg || '#0b0c10');
        root.style.setProperty('--jj-sg-text', data.colors.text || '#ffffff');

        // Typography (preview)
        if (data.typography) {
            var activeDevice = getActiveDevice();
            var us = getUnitSettings(data);
            ['h1','h2','h3','p'].forEach(function(k) {
                var t = data.typography[k] || {};
                var fs = getFontSizeForDevice(t, activeDevice);
                var lh = (t.line_height !== undefined && t.line_height !== null) ? String(t.line_height) : '';
                var ls = (t.letter_spacing !== undefined && t.letter_spacing !== null) ? String(t.letter_spacing) : '';

                var fsOut = formatFontSize(fs, us.unit, us.basePx);
                var lsPx = (/^-?\d+(\.\d+)?$/.test(ls)) ? (ls + 'px') : ls;

                root.style.setProperty('--jj-sg-' + k + '-size', fsOut || '');
                root.style.setProperty('--jj-sg-' + k + '-lh', lh || '');
                root.style.setProperty('--jj-sg-' + k + '-ls', lsPx || '');
                root.style.setProperty('--jj-sg-' + k + '-ff', t.font_family || '');
                root.style.setProperty('--jj-sg-' + k + '-fw', t.font_weight || '');
            });
        }
    }

    function renderColors(data) {
        $('.jj-sg-color-card').each(function() {
            var $card = $(this);
            var key = $card.data('color-key');
            var val = (data.colors && data.colors[key]) ? data.colors[key] : '';
            $card.find('.jj-sg-swatch').css('background', val || '#000');
            $card.find('.jj-sg-color-value').text(val || '');

            var $editor = $card.find('.jj-sg-color-editor');
            $editor.empty();
            if (cfg().can_edit) {
                var $input = $('<input type="color" class="jj-sg-color-input" />');
                $input.val(val || '#000000');
                $input.attr('data-color-key', key);
                $editor.append($input);
            } else {
                $editor.append('<div class="jj-sg-readonly">' + esc('읽기 전용') + '</div>');
            }
        });
    }

    function renderTypography(data) {
        var activeDevice = getActiveDevice();
        $('.jj-sg-typo-table').each(function() {
            var $box = $(this);
            var key = $box.data('typo-key');
            var t = (data.typography && data.typography[key]) ? data.typography[key] : {};
            var label = key.toUpperCase();
            if (key === 'p') label = 'Paragraph';
            var sizeForDevice = getFontSizeForDevice(t, activeDevice);

            var html = '';
            html += '<div class="jj-sg-typo-row">';
            html += '<div class="jj-sg-typo-left"><div class="jj-sg-typo-title">' + esc(label) + '</div></div>';
            html += '<div class="jj-sg-typo-right">';
            html += '<div class="jj-sg-typo-meta">Font: <span>' + esc(t.font_family || '-') + '</span></div>';
            html += '<div class="jj-sg-typo-meta">Size (' + esc(activeDevice) + '): <span>' + esc(sizeForDevice || '-') + '</span></div>';
            html += '<div class="jj-sg-typo-meta">Line height: <span>' + esc(t.line_height || '-') + '</span></div>';
            html += '<div class="jj-sg-typo-meta">Letter spacing: <span>' + esc(t.letter_spacing || '-') + '</span></div>';
            html += '</div>';
            html += '</div>';

            if (cfg().can_edit) {
                html += '<div class="jj-sg-typo-editor" data-typo-key="' + esc(key) + '">';
                html += '<label>Font family <input type="text" data-k="font_family" value="' + esc(t.font_family || '') + '" placeholder="Inter Tight, system-ui" /></label>';
                html += '<label>Weight <input type="text" data-k="font_weight" value="' + esc(t.font_weight || '') + '" placeholder="400/500/600" /></label>';
                html += '<label>Size (' + esc(activeDevice) + ', px) <input type="text" data-k="font_size" value="' + esc(sizeForDevice || '') + '" placeholder="40" /></label>';
                html += '<label>Line height <input type="text" data-k="line_height" value="' + esc(t.line_height || '') + '" placeholder="1.3" /></label>';
                html += '<label>Letter spacing <input type="text" data-k="letter_spacing" value="' + esc(t.letter_spacing || '') + '" placeholder="-0.5" /></label>';
                html += '</div>';
            }

            $box.html(html);
        });
    }

    function collectPayload() {
        var data = $.extend(true, {}, cfg().data || {});
        var activeDevice = getActiveDevice();

        // colors
        $('.jj-sg-color-input').each(function() {
            var k = $(this).attr('data-color-key');
            var v = $(this).val();
            if (!data.colors) data.colors = {};
            data.colors[k] = v;
        });

        // typography
        $('.jj-sg-typo-editor').each(function() {
            var k = $(this).attr('data-typo-key');
            if (!data.typography) data.typography = {};
            if (!data.typography[k]) data.typography[k] = {};
            $(this).find('input').each(function() {
                var kk = $(this).attr('data-k');
                data.typography[k][kk] = $(this).val();
            });

            // Live preview 즉시 반영: 현재 디바이스의 font_size는 font_sizes에도 같이 반영
            if (data.typography[k] && typeof data.typography[k].font_size !== 'undefined') {
                if (!data.typography[k].font_sizes) data.typography[k].font_sizes = {};
                data.typography[k].font_sizes[activeDevice] = data.typography[k].font_size;
            }
        });

        // typography settings + active device
        data.active_device = activeDevice;
        if (!data.typography_settings) data.typography_settings = {};
        data.typography_settings.base_px = $('#jj-sg-typo-base-px').val();
        data.typography_settings.unit = $('#jj-sg-typo-unit').val();

        return data;
    }

    function renderAll(data) {
        renderColors(data);
        renderTypography(data);
        applyCssVars(data);
        applySandboxCss(data);
    }

    function buildSandboxCss(data) {
        if (!data) return '';
        var c = data.colors || {};
        var t = data.typography || {};
        var activeDevice = getActiveDevice();
        var us = getUnitSettings(data);
        function num(v) { return (v !== undefined && v !== null) ? String(v) : ''; }
        function numPx(v) { var s = num(v); return (/^-?\d+(\.\d+)?$/.test(s)) ? (s + 'px') : ''; }
        function numEm(v) { var s = num(v); return (/^-?\d+(\.\d+)?$/.test(s)) ? (s + 'em') : ''; }

        var css = ':root{';
        if (c.primary) css += '--jj-primary-color:' + c.primary + ';';
        if (c.accent) css += '--jj-primary-color-hover:' + c.accent + ';';
        if (c.secondary) css += '--jj-secondary-color:' + c.secondary + ';';
        if (c.bg) { css += '--jj-sys-site-bg:' + c.bg + ';--jj-sys-content-bg:' + c.bg + ';'; }
        if (c.text) css += '--jj-sys-text:' + c.text + ';';

        ['h1','h2','h3','p'].forEach(function(k) {
            var tk = t[k] || {};
            if (tk.font_family) css += '--jj-font-' + k + '-family:' + tk.font_family + ';';
            if (tk.font_weight) css += '--jj-font-' + k + '-weight:' + tk.font_weight + ';';
            var lh = numEm(tk.line_height);
            var ls = numPx(tk.letter_spacing);
            var fsNum = getFontSizeForDevice(tk, activeDevice);
            var fs = formatFontSize(fsNum, us.unit, us.basePx);
            if (lh) css += '--jj-font-' + k + '-line-height:' + lh + ';';
            if (ls) css += '--jj-font-' + k + '-letter-spacing:' + ls + ';';
            if (fs) css += '--jj-font-' + k + '-size:' + fs + ';';
        });
        css += '}';
        return css;
    }

    function applySandboxCss(data) {
        var iframe = document.getElementById('jj-sg-sandbox-frame');
        if (!iframe) return;
        try {
            var doc = iframe.contentDocument;
            if (!doc || !doc.documentElement) return;
            var head = doc.head || doc.getElementsByTagName('head')[0];
            if (!head) return;
            var id = 'jj-sg-sandbox-live-vars';
            var style = doc.getElementById(id);
            if (!style) {
                style = doc.createElement('style');
                style.id = id;
                head.appendChild(style);
            }
            style.textContent = buildSandboxCss(data);
        } catch (e) {
            // cross-origin / not ready: ignore
        }
    }

    // ===== Typography presets (Live) =====
    var typoUndo = null;

    function deepClone(obj) {
        try { return JSON.parse(JSON.stringify(obj || {})); } catch (e) { return $.extend(true, {}, obj || {}); }
    }

    function baseSizeMap() {
        // px 기준 권장 스케일 (8K 포함)
        return {
            h1: { desktop: 40, laptop: 38, tablet: 36, phablet: 32, mobile: 30, phone_small: 28, desktop_qhd: 44, desktop_uhd: 48, desktop_5k: 52, desktop_8k: 60 },
            h2: { desktop: 32, laptop: 30, tablet: 28, phablet: 26, mobile: 24, phone_small: 22, desktop_qhd: 36, desktop_uhd: 40, desktop_5k: 44, desktop_8k: 52 },
            h3: { desktop: 26, laptop: 24, tablet: 22, phablet: 20, mobile: 19, phone_small: 18, desktop_qhd: 28, desktop_uhd: 30, desktop_5k: 32, desktop_8k: 36 },
            p:  { desktop: 16, laptop: 16, tablet: 16, phablet: 15, mobile: 15, phone_small: 14, desktop_qhd: 16, desktop_uhd: 18, desktop_5k: 18, desktop_8k: 20 }
        };
    }

    function shiftSizes(map, delta) {
        var out = {};
        Object.keys(map || {}).forEach(function(tag){
            out[tag] = {};
            Object.keys(map[tag] || {}).forEach(function(dev){
                out[tag][dev] = Math.max(10, Math.round((map[tag][dev] || 0) + delta));
            });
        });
        return out;
    }

    function getTypoPresets() {
        var s = baseSizeMap();
        return [
            {
                id: 'saas-modern',
                name: 'SaaS Modern (Rem)',
                tags: ['SaaS','모던','QHD/4K/5K/8K'],
                settings: { base_px: 16, unit: 'rem' },
                tag: {
                    _all: { font_family: "Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans KR', sans-serif", font_weight: '400', line_height: '1.7', letter_spacing: '0' },
                    h1: { font_weight: '800', line_height: '1.15', letter_spacing: '-0.6' },
                    h2: { font_weight: '800', line_height: '1.2',  letter_spacing: '-0.4' },
                    h3: { font_weight: '700', line_height: '1.3',  letter_spacing: '-0.2' },
                    p:  { font_weight: '400', line_height: '1.75', letter_spacing: '0' }
                },
                sizes: s
            },
            {
                id: 'editorial-readable',
                name: 'Editorial (Readable)',
                tags: ['콘텐츠','매거진','읽기'],
                settings: { base_px: 18, unit: 'rem' },
                tag: {
                    _all: { font_family: "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans KR', sans-serif", font_weight: '400', line_height: '1.85', letter_spacing: '0' },
                    h1: { font_family: "Georgia, 'Times New Roman', Times, serif", font_weight: '700', line_height: '1.15', letter_spacing: '-0.3' },
                    h2: { font_family: "Georgia, 'Times New Roman', Times, serif", font_weight: '700', line_height: '1.2',  letter_spacing: '-0.2' },
                    h3: { font_family: "Georgia, 'Times New Roman', Times, serif", font_weight: '700', line_height: '1.25', letter_spacing: '-0.1' }
                },
                sizes: shiftSizes(s, 2)
            },
            {
                id: 'minimal-clean',
                name: 'Minimal Clean',
                tags: ['미니멀','깔끔'],
                settings: { base_px: 16, unit: 'rem' },
                tag: {
                    _all: { font_family: "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans KR', sans-serif", font_weight: '400', line_height: '1.65', letter_spacing: '0' },
                    h1: { font_weight: '700', line_height: '1.2', letter_spacing: '-0.4' },
                    h2: { font_weight: '700', line_height: '1.25', letter_spacing: '-0.2' },
                    h3: { font_weight: '600', line_height: '1.3', letter_spacing: '-0.1' }
                },
                sizes: shiftSizes(s, -1)
            },
            {
                id: 'cta-bold',
                name: 'CTA / Conversion',
                tags: ['전환','CTA'],
                settings: { base_px: 16, unit: 'rem' },
                tag: {
                    _all: { font_family: "Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans KR', sans-serif", font_weight: '400', line_height: '1.7', letter_spacing: '0' },
                    h1: { font_weight: '900', line_height: '1.1', letter_spacing: '-0.8' },
                    h2: { font_weight: '800', line_height: '1.15', letter_spacing: '-0.5' },
                    h3: { font_weight: '700', line_height: '1.2', letter_spacing: '-0.2' }
                },
                sizes: s
            }
        ];
    }

    function applyTypoPreset(preset) {
        var c = cfg();
        if (!c || !c.data) return;
        if (!c.can_edit) {
            setStatus((c.i18n && c.i18n.no_permission) ? c.i18n.no_permission : '권한이 없습니다.', 'err');
            return;
        }

        typoUndo = deepClone(c.data);
        $('#jj-sg-typo-preset-undo').prop('disabled', false);

        // settings
        if (!c.data.typography_settings) c.data.typography_settings = {};
        if (preset.settings) {
            c.data.typography_settings.base_px = String(preset.settings.base_px || '16');
            c.data.typography_settings.unit = String(preset.settings.unit || 'px');
            $('#jj-sg-typo-base-px').val(c.data.typography_settings.base_px);
            $('#jj-sg-typo-unit').val(c.data.typography_settings.unit);
        }

        // tags (live page: h1/h2/h3/p)
        ['h1','h2','h3','p'].forEach(function(tag){
            if (!c.data.typography) c.data.typography = {};
            if (!c.data.typography[tag]) c.data.typography[tag] = {};
            var t = c.data.typography[tag];

            var all = (preset.tag && preset.tag._all) ? preset.tag._all : {};
            var own = (preset.tag && preset.tag[tag]) ? preset.tag[tag] : {};
            var merged = $.extend({}, all, own);

            if (merged.font_family !== undefined) t.font_family = String(merged.font_family);
            if (merged.font_weight !== undefined) t.font_weight = String(merged.font_weight);
            if (merged.line_height !== undefined) t.line_height = String(merged.line_height);
            if (merged.letter_spacing !== undefined) t.letter_spacing = String(merged.letter_spacing);

            // sizes
            if (!t.font_sizes) t.font_sizes = {};
            var sizeMap = (preset.sizes && preset.sizes[tag]) ? preset.sizes[tag] : null;
            if (sizeMap) {
                Object.keys(sizeMap).forEach(function(dev){
                    t.font_sizes[dev] = String(sizeMap[dev]);
                });
            }

            // 현재 선택 디바이스 input 값도 갱신
            var devKey = getActiveDevice();
            t.font_size = getFontSizeForDevice(t, devKey);
        });

        renderAll(c.data);
        setStatus('프리셋 적용됨: ' + (preset.name || ''), 'ok');
    }

    function renderTypoPresets() {
        var $wrap = $('#jj-sg-typo-presets');
        if (!$wrap.length) return;
        var presets = getTypoPresets();
        var q = String($('#jj-sg-typo-preset-search').val() || '').toLowerCase().trim();
        if (q) {
            presets = presets.filter(function(p){
                var hay = (p.name || '') + ' ' + (Array.isArray(p.tags) ? p.tags.join(' ') : '');
                return hay.toLowerCase().indexOf(q) !== -1;
            });
        }

        var html = '<div class="jj-sg-preset-grid">';
        presets.forEach(function(p){
            html += '<div class="jj-sg-preset-card">';
            html += ' <div class="jj-sg-preset-title">' + esc(p.name || '') + '</div>';
            if (Array.isArray(p.tags) && p.tags.length) {
                html += ' <div class="jj-sg-preset-tags">' + p.tags.map(function(t){ return '<span class="jj-sg-tag">' + esc(t) + '</span>'; }).join('') + '</div>';
            }
            html += ' <button type="button" class="jj-sg-btn jj-sg-btn-primary jj-sg-apply-typo-preset" data-id="' + esc(p.id) + '">적용</button>';
            html += '</div>';
        });
        html += '</div>';
        $wrap.html(html);

        // click bind (delegated)
        $wrap.off('click.jjSgPreset').on('click.jjSgPreset', '.jj-sg-apply-typo-preset', function(){
            var id = String($(this).data('id') || '');
            var list = getTypoPresets();
            var target = null;
            list.forEach(function(p){ if (p.id === id) target = p; });
            if (target) {
                applyTypoPreset(target);
            }
        });
    }

    function ajaxSave() {
        var c = cfg();
        if (!c || !c.can_edit) {
            setStatus(c && c.i18n ? c.i18n.no_permission : '권한이 없습니다.', 'err');
            return;
        }
        setStatus(c.i18n ? c.i18n.saving : '저장 중...', 'warn');
        $('#jj-sg-live-save').prop('disabled', true);

        $.post(c.ajax_url, {
            action: 'jj_style_guide_live_save',
            nonce: c.nonce,
            payload: collectPayload()
        }).done(function(resp) {
            if (resp && resp.success) {
                c.data = (resp.data && resp.data.data) ? resp.data.data : c.data;
                renderAll(c.data);
                setStatus(c.i18n ? c.i18n.saved : '저장되었습니다.', 'ok');
            } else {
                var msg = (resp && resp.data && resp.data.message) ? resp.data.message : (c.i18n ? c.i18n.save_failed : '저장 실패');
                setStatus(msg, 'err');
            }
        }).fail(function() {
            setStatus(c.i18n ? c.i18n.save_failed : '저장 실패', 'err');
        }).always(function() {
            $('#jj-sg-live-save').prop('disabled', false);
        });
    }

    function ajaxReload() {
        var c = cfg();
        if (!c) return;
        $.post(c.ajax_url, {
            action: 'jj_style_guide_live_get',
            nonce: c.nonce
        }).done(function(resp) {
            if (resp && resp.success && resp.data && resp.data.data) {
                c.data = resp.data.data;
                renderAll(c.data);
                setStatus((c.i18n && c.i18n.reloaded) ? c.i18n.reloaded : '불러오기 완료', 'ok');
            }
        });
    }

    function bind() {
        $(document).on('change', '.jj-sg-color-input', function() {
            var c = cfg();
            if (!c) return;
            var data = collectPayload();
            applyCssVars(data);
            // 카드 값 갱신
            var key = $(this).attr('data-color-key');
            $(this).closest('.jj-sg-color-card').find('.jj-sg-color-value').text(data.colors[key] || '');
            $(this).closest('.jj-sg-color-card').find('.jj-sg-swatch').css('background', data.colors[key] || '#000');
        });
        $(document).on('input', '.jj-sg-typo-editor input', function() {
            applyCssVars(collectPayload());
        });
        $(document).on('change', '#jj-sg-typo-unit, #jj-sg-typo-base-px', function() {
            applyCssVars(collectPayload());
            applySandboxCss(collectPayload());
        });
        $(document).on('change', '#jj-sg-live-device', function() {
            // 디바이스 변경 시: 폼/미리보기 갱신
            renderTypography(collectPayload());
            applyCssVars(collectPayload());
            applySandboxCss(collectPayload());
        });
        $(document).on('click', '#jj-sg-live-preview', function() {
            setStatus('미리보기 적용(저장 없음)', 'ok');
            renderAll(collectPayload());
        });
        $(document).on('click', '#jj-sg-live-reload', function() {
            ajaxReload();
        });
        $(document).on('click', '#jj-sg-live-save', function() {
            ajaxSave();
        });

        // Presets
        $(document).on('input', '#jj-sg-typo-preset-search', function(){
            renderTypoPresets();
        });
        $(document).on('click', '#jj-sg-typo-preset-undo', function(){
            var c = cfg();
            if (!c || !typoUndo) return;
            c.data = deepClone(typoUndo);
            // UI sync
            if (c.data.typography_settings) {
                $('#jj-sg-typo-base-px').val(c.data.typography_settings.base_px || '16');
                $('#jj-sg-typo-unit').val(c.data.typography_settings.unit || 'px');
            }
            renderAll(c.data);
            setStatus('되돌리기 완료', 'ok');
        });

        // Export dropdown toggle
        $(document).on('click', '#jj-sg-export-toggle', function(e) {
            e.stopPropagation();
            var $menu = $('#jj-sg-export-menu');
            $menu.toggle();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.jj-sg-export-dropdown').length) {
                $('#jj-sg-export-menu').hide();
            }
        });

        // Export actions
        $(document).on('click', '.jj-sg-export-item', function() {
            var format = $(this).data('format');
            $('#jj-sg-export-menu').hide();
            handleExport(format);
        });
    }

    function handleExport(format) {
        var c = cfg();
        if (!c) return;

        var i18n = c.i18n || {};
        setStatus(i18n.exporting || '내보내는 중...', 'warn');

        if (format === 'pdf') {
            // PDF: 브라우저 print 사용
            window.print();
            setStatus(i18n.export_done || '내보내기 완료!', 'ok');
            return;
        }

        if (format === 'png') {
            // PNG: html2canvas 사용 (CDN에서 로드)
            exportAsImage();
            return;
        }

        // AJAX 기반 내보내기 (html, css, json, zip)
        var actionMap = {
            'html': 'jj_export_style_guide_html',
            'css': 'jj_export_style_guide_css',
            'json': 'jj_export_style_guide_json',
            'zip': 'jj_export_style_guide_zip'
        };

        var action = actionMap[format];
        if (!action) {
            setStatus(i18n.export_failed || '내보내기 실패', 'err');
            return;
        }

        $.post(c.ajax_url, {
            action: action,
            nonce: c.exporter_nonce
        }).done(function(resp) {
            if (resp && resp.success && resp.data) {
                if (format === 'zip' && resp.data.url) {
                    // ZIP: 다운로드 링크로 이동
                    var a = document.createElement('a');
                    a.href = resp.data.url;
                    a.download = resp.data.filename || 'style-guide.zip';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                } else if (resp.data.content) {
                    // 텍스트 파일 다운로드
                    downloadTextFile(resp.data.content, resp.data.filename, resp.data.mime);
                }
                setStatus(i18n.export_done || '내보내기 완료!', 'ok');
            } else {
                var msg = (resp && resp.data && resp.data.message) ? resp.data.message : (i18n.export_failed || '내보내기 실패');
                setStatus(msg, 'err');
            }
        }).fail(function() {
            setStatus(i18n.export_failed || '내보내기 실패', 'err');
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

    function exportAsImage() {
        var c = cfg();
        var i18n = c && c.i18n ? c.i18n : {};

        // html2canvas CDN 로드
        if (typeof html2canvas === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
            script.onload = function() {
                captureAndDownload();
            };
            script.onerror = function() {
                setStatus(i18n.export_failed || '이미지 라이브러리 로드 실패', 'err');
            };
            document.head.appendChild(script);
        } else {
            captureAndDownload();
        }

        function captureAndDownload() {
            var target = document.getElementById('jj-style-guide-live');
            if (!target) {
                setStatus(i18n.export_failed || '캡처 대상 없음', 'err');
                return;
            }
            html2canvas(target, {
                backgroundColor: '#ffffff',
                scale: 2,
                useCORS: true,
                logging: false
            }).then(function(canvas) {
                var link = document.createElement('a');
                link.download = 'style-guide.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                setStatus(i18n.export_done || '내보내기 완료!', 'ok');
            }).catch(function() {
                setStatus(i18n.export_failed || '이미지 캡처 실패', 'err');
            });
        }
    }

    $(document).ready(function() {
        if (!$('#jj-style-guide-live').length) return;
        var c = cfg();
        if (!c || !c.data) return;
        // 초기 컨트롤 값 세팅
        if (c.data.typography_settings) {
            $('#jj-sg-typo-base-px').val(c.data.typography_settings.base_px || '16');
            $('#jj-sg-typo-unit').val(c.data.typography_settings.unit || 'px');
        }
        // 디바이스 셀렉트 구성
        var $dev = $('#jj-sg-live-device');
        if ($dev.length) {
            $dev.empty();
            var devices = (c.data.devices && c.data.devices.length) ? c.data.devices : [{key:'desktop',label:'Desktop'}];
            devices.forEach(function(d) {
                $dev.append('<option value="' + esc(d.key) + '">' + esc(d.label || d.key) + '</option>');
            });
            if (!$dev.val()) $dev.val('desktop');
        }
        renderAll(c.data);
        renderTypoPresets();
        bind();

        var $iframe = $('#jj-sg-sandbox-frame');
        if ($iframe.length) {
            $iframe.on('load', function() {
                var cc = cfg();
                if (cc && cc.data) {
                    applySandboxCss(cc.data);
                }
            });
        }
    });
})(jQuery);

