/**
 * JJ Style Guide - Palette Presets & Inline Preview
 *
 * 목표:
 * - 텍스트 위주의 팔레트 UI를 "보여주는" UI로 보강
 * - 2-Color 조합(Primary/Secondary) + 권장 System 팔레트까지 프리셋으로 제공
 * - 클릭 1~2번으로 즉시 적용 + 즉시 미리보기
 *
 * 주의:
 * - 기존 `jj-style-guide-editor.js`의 currentSettings/필드 바인딩 로직을 존중합니다.
 * - 실제 값 적용은 `data-setting-key` 필드 값을 변경(trigger change)하는 방식으로 통일합니다.
 */
(function($){
  'use strict';

  function normalizeHex(hex) {
    if (!hex || typeof hex !== 'string') return '';
    var h = hex.trim();
    if (!h) return '';
    if (h[0] !== '#') h = '#' + h;
    if (h.length === 4) {
      // #RGB -> #RRGGBB
      h = '#' + h[1] + h[1] + h[2] + h[2] + h[3] + h[3];
    }
    return h.toUpperCase();
  }

  function clamp(n, min, max) { return Math.max(min, Math.min(max, n)); }

  function hexToRgb(hex) {
    var h = normalizeHex(hex);
    if (!h || h.length !== 7) return null;
    return {
      r: parseInt(h.slice(1, 3), 16),
      g: parseInt(h.slice(3, 5), 16),
      b: parseInt(h.slice(5, 7), 16),
    };
  }

  function rgbToHex(r, g, b) {
    var rr = clamp(Math.round(r), 0, 255).toString(16).padStart(2, '0');
    var gg = clamp(Math.round(g), 0, 255).toString(16).padStart(2, '0');
    var bb = clamp(Math.round(b), 0, 255).toString(16).padStart(2, '0');
    return ('#' + rr + gg + bb).toUpperCase();
  }

  // amount: -1.0 ~ +1.0 (음수: 어둡게, 양수: 밝게)
  function shade(hex, amount) {
    var rgb = hexToRgb(hex);
    if (!rgb) return '';
    var t = amount < 0 ? 0 : 255;
    var p = Math.abs(amount);
    return rgbToHex(
      (t - rgb.r) * p + rgb.r,
      (t - rgb.g) * p + rgb.g,
      (t - rgb.b) * p + rgb.b
    );
  }

  function setField(settingKey, value) {
    if (!settingKey) return;
    var v = normalizeHex(value);
    var $field = $('#jj-style-guide-form').find('[data-setting-key="' + settingKey + '"]');
    if (!$field.length) return;

    $field.val(v).trigger('change');
    // wpColorPicker가 있으면 색상도 동기화
    if ($field.closest('.wp-picker-container').length) {
      try { $field.wpColorPicker('color', v); } catch (e) {}
    }
    // 카드 미리보기(기존 DOM)
    var $preview = $field.closest('.jj-control-group').find('.jj-color-preview').first();
    if ($preview.length) $preview.css('background-color', v);
  }

  function readField(settingKey, fallback) {
    var $field = $('#jj-style-guide-form').find('[data-setting-key="' + settingKey + '"]');
    var v = $field.length ? normalizeHex($field.val()) : '';
    return v || normalizeHex(fallback) || '';
  }

  function updateInlinePreview() {
    var $preview = $('#jj-palette-inline-preview');
    if (!$preview.length) return;

    var primary = readField('palettes[brand][primary_color]', '#2271B1');
    var primaryHover = readField('palettes[brand][primary_color_hover]', shade(primary, -0.12));
    var secondary = readField('palettes[brand][secondary_color]', '#444444');
    var secondaryHover = readField('palettes[brand][secondary_color_hover]', shade(secondary, -0.12));

    var bg = readField('palettes[system][site_bg]', '#F6F7F7');
    var surface = readField('palettes[system][content_bg]', '#FFFFFF');
    var text = readField('palettes[system][text_color]', '#1D2327');
    var link = readField('palettes[system][link_color]', primary);

    $preview.css('--jj-prev-bg', bg);
    $preview.css('--jj-prev-surface', surface);
    $preview.css('--jj-prev-text', text);
    $preview.css('--jj-prev-link', link);
    $preview.css('--jj-prev-primary', primary);
    $preview.css('--jj-prev-primary-hover', primaryHover);
    $preview.css('--jj-prev-secondary', secondary);
    $preview.css('--jj-prev-secondary-hover', secondaryHover);

    $preview.find('[data-jj-color="primary"]').text(primary);
    $preview.find('[data-jj-color="secondary"]').text(secondary);
    $preview.find('[data-jj-color="bg"]').text(bg);
    $preview.find('[data-jj-color="text"]').text(text);
  }

  $(document).ready(function(){
    var $mount = $('#jj-palette-presets');
    if (!$mount.length) return;

    var BASE_PRESETS = [
      {
        id: 'luxury-black-gold',
        name: 'Black & Gold',
        tags: ['럭셔리', '다크', '고대비', '프리미엄'],
        note: '고급·권위·집중(CTA 강함). 법률/컨설팅/하이엔드에 적합.',
        brand: { primary: '#C9A227', secondary: '#0B0D0F' },
        system: { site_bg: '#0B0D0F', content_bg: '#101418', text_color: '#EAEAEA', link_color: '#C9A227' }
      },
      {
        id: 'trust-navy-cyan',
        name: 'Navy & Cyan',
        tags: ['신뢰', '테크', '차분함'],
        note: '신뢰·안정·정교함. SaaS/데이터/기업 사이트에 적합.',
        brand: { primary: '#1F3C88', secondary: '#00B8D9' },
        system: { site_bg: '#F7FAFC', content_bg: '#FFFFFF', text_color: '#1D2327', link_color: '#1F3C88' }
      },
      {
        id: 'modern-coral-slate',
        name: 'Coral & Slate',
        tags: ['모던', '따뜻함', '크리에이티브'],
        note: '친근·활기. 브랜딩/포트폴리오/커머스에 적합.',
        brand: { primary: '#FF6B6B', secondary: '#334E68' },
        system: { site_bg: '#FFFFFF', content_bg: '#F7FAFC', text_color: '#102A43', link_color: '#334E68' }
      },
      {
        id: 'minimal-mono',
        name: 'Minimal Mono',
        tags: ['미니멀', '라이트', '가독성'],
        note: '콘텐츠 중심·가독성 강화. 블로그/매거진에 적합.',
        brand: { primary: '#111827', secondary: '#6B7280' },
        system: { site_bg: '#F9FAFB', content_bg: '#FFFFFF', text_color: '#111827', link_color: '#111827' }
      },
      {
        id: 'emerald-charcoal',
        name: 'Emerald & Charcoal',
        tags: ['헬스', '자연', '안정'],
        note: '안정·신선. 헬스/웰니스/교육에 적합.',
        brand: { primary: '#10B981', secondary: '#111827' },
        system: { site_bg: '#F7FAFC', content_bg: '#FFFFFF', text_color: '#111827', link_color: '#10B981' }
      },
      {
        id: 'royal-purple-ice',
        name: 'Royal Purple',
        tags: ['프리미엄', '창의', '브랜딩'],
        note: '독창·세련. 이벤트/크리에이티브 스튜디오에 적합.',
        brand: { primary: '#6D28D9', secondary: '#0EA5E9' },
        system: { site_bg: '#F8FAFC', content_bg: '#FFFFFF', text_color: '#0F172A', link_color: '#6D28D9' }
      },
      {
        id: 'warm-brown-cream',
        name: 'Brown & Cream',
        tags: ['따뜻함', '클래식', '브랜드'],
        note: '따뜻·신뢰. 카페/식품/라이프스타일에 적합.',
        brand: { primary: '#8B5E34', secondary: '#D4A373' },
        system: { site_bg: '#FFF7ED', content_bg: '#FFFFFF', text_color: '#1F2937', link_color: '#8B5E34' }
      },
      {
        id: 'contrast-blue-yellow',
        name: 'Blue & Yellow (CTA)',
        tags: ['고대비', '전환', 'CTA'],
        note: '버튼/링크가 눈에 띄게. 랜딩/광고 페이지에 적합.',
        brand: { primary: '#2563EB', secondary: '#F59E0B' },
        system: { site_bg: '#FFFFFF', content_bg: '#F8FAFC', text_color: '#0F172A', link_color: '#2563EB' }
      }
    ];

    var PRESETS = [];
    var bulkMode = false;
    var bulkSelected = {};

    function isAiPreset(p) {
      if (!p || typeof p !== 'object') return false;
      if (p.source === 'ai_extension') return true;
      return typeof p.id === 'string' && p.id.indexOf('ai-') === 0;
    }

    function sortAiPresets(a, b) {
      var ap = !!(a && a.pinned);
      var bp = !!(b && b.pinned);
      if (ap !== bp) return ap ? -1 : 1;
      var at = (a && a.created_at) ? String(a.created_at) : '';
      var bt = (b && b.created_at) ? String(b.created_at) : '';
      if (at === bt) return 0;
      return bt < at ? -1 : 1; // desc
    }

    function loadAiPresets() {
      var aiPresets = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
        ? window.jj_admin_params.ai_palette_presets
        : [];
      if (!aiPresets || !aiPresets.length) return [];

      var cleaned = [];
      aiPresets.forEach(function(p){
        if (!p || typeof p !== 'object') return;
        if (!p.id || !p.name || !p.brand || !p.system) return;

        // tags 정규화
        if (!Array.isArray(p.tags)) {
          p.tags = [];
        }
        if (p.tags.indexOf('AI') === -1) {
          p.tags.unshift('AI');
        }

        // note 보정
        if (!p.note) {
          p.note = 'AI로 저장된 팔레트';
        }

        // shape 보정(brand/system)
        p.brand = p.brand || {};
        p.system = p.system || {};
        cleaned.push(p);
      });

      cleaned.sort(sortAiPresets);
      return cleaned;
    }

    function normalizePresetColors(p) {
      p.brand.primary = normalizeHex(p.brand.primary);
      p.brand.secondary = normalizeHex(p.brand.secondary);
      p.brand.primary_hover = normalizeHex(p.brand.primary_hover || shade(p.brand.primary, -0.12));
      p.brand.secondary_hover = normalizeHex(p.brand.secondary_hover || shade(p.brand.secondary, -0.12));
      p.system.site_bg = normalizeHex(p.system.site_bg);
      p.system.content_bg = normalizeHex(p.system.content_bg);
      p.system.text_color = normalizeHex(p.system.text_color);
      p.system.link_color = normalizeHex(p.system.link_color || p.brand.primary);
      return p;
    }

    function rebuildPresets() {
      var ai = loadAiPresets();
      PRESETS = ai.concat(BASE_PRESETS);
      PRESETS.forEach(function(p){
        try { normalizePresetColors(p); } catch (e) {}
      });
    }

    rebuildPresets();

    var selectedId = null;

    function getBulkSelectedIds() {
      return Object.keys(bulkSelected).filter(function(id){ return !!bulkSelected[id]; });
    }

    function clearBulkSelection() {
      bulkSelected = {};
    }

    function toggleBulkSelected(id, on) {
      if (!id) return;
      bulkSelected[id] = !!on;
    }

    function isBulkSelected(id) {
      return !!bulkSelected[id];
    }

    function render(filterText) {
      var q = (filterText || '').toLowerCase().trim();
      $mount.empty();

      var $toolbar = $('<div class="jj-preset-toolbar"></div>');
      var $search = $('<input type="search" class="jj-preset-search" placeholder="프리셋 검색 (예: 럭셔리, 신뢰, 다크)"/>');
      $search.val(filterText || '');

      var $includeSystem = $('<label class="jj-preset-include-system"><input type="checkbox" checked> 시스템 팔레트도 함께 적용</label>');
      var $includeComponents = $('<label class="jj-preset-include-system"><input type="checkbox" checked> 버튼/폼/링크 자동 맞추기</label>');

      // AI 프리셋 관리(내보내기/불러오기)
      var aiCount = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
        ? window.jj_admin_params.ai_palette_presets.length
        : 0;
      var $aiTools = $('<div class="jj-preset-ai-tools"></div>');
      if (aiCount > 0) {
        $aiTools.append($('<span class="jj-preset-ai-count"></span>').text('AI 프리셋: ' + aiCount + '개'));
      }
      var $bulkToggle = $('<label class="jj-preset-ai-bulk-toggle"><input type="checkbox"> 대량 선택</label>');
      $bulkToggle.find('input').prop('checked', !!bulkMode);
      var $bulkCount = $('<span class="jj-preset-ai-bulk-count"></span>');
      var $bulkSelectVisible = $('<button type="button" class="button jj-preset-ai-bulk-select-visible" style="display:none;">보이는 항목 전체 선택</button>');
      var $bulkInvert = $('<button type="button" class="button jj-preset-ai-bulk-invert" style="display:none;">선택 반전</button>');
      var $bulkPin = $('<button type="button" class="button jj-preset-ai-bulk-pin" style="display:none;">선택 고정</button>');
      var $bulkUnpin = $('<button type="button" class="button jj-preset-ai-bulk-unpin" style="display:none;">선택 해제</button>');
      var $bulkDelete = $('<button type="button" class="button button-link-delete jj-preset-ai-bulk-delete" style="display:none;">선택 삭제</button>');
      var $bulkExport = $('<button type="button" class="button jj-preset-ai-bulk-export" style="display:none;">선택 내보내기</button>');
      var $bulkShare = $('<button type="button" class="button jj-preset-ai-bulk-share" style="display:none;">선택 공유 코드</button>');
      var $bulkClear = $('<button type="button" class="button jj-preset-ai-bulk-clear" style="display:none;">선택 해제</button>');

      var $aiExportAll = $('<button type="button" class="button jj-preset-ai-export-all">AI 전체 내보내기</button>');
      var $aiImport = $('<button type="button" class="button jj-preset-ai-import">AI 불러오기</button>');
      var $aiImportInput = $('<input type="file" accept=".json" class="jj-preset-ai-import-input" style="display:none;">');
      var $aiImportCode = $('<button type="button" class="button jj-preset-ai-import-code">코드로 가져오기</button>');
      $aiTools.append($bulkToggle).append($bulkCount).append($bulkSelectVisible).append($bulkInvert).append($bulkPin).append($bulkUnpin).append($bulkDelete).append($bulkExport).append($bulkShare).append($bulkClear);
      $aiTools.append($aiExportAll).append($aiImport).append($aiImportCode).append($aiImportInput);

      $toolbar.append($search).append($includeSystem).append($includeComponents).append($aiTools);
      $mount.append($toolbar);

      var $grid = $('<div class="jj-preset-grid" role="list"></div>');
      var shown = 0;

      PRESETS.forEach(function(p){
        var hay = (p.name + ' ' + p.tags.join(' ') + ' ' + p.note).toLowerCase();
        if (q && hay.indexOf(q) === -1) return;
        shown++;

        var isActive = selectedId === p.id;
        var isAI = isAiPreset(p);
        var isPinned = !!(p && p.pinned);

        var $card = $('<div class="jj-preset-card" role="listitem"></div>');
        $card.toggleClass('is-active', !!isActive);
        $card.toggleClass('is-ai', !!isAI);
        $card.toggleClass('is-pinned', !!isPinned);
        $card.attr('data-preset-id', p.id);

        var swatches = [
          { c: p.brand.primary, label: 'Primary' },
          { c: p.brand.secondary, label: 'Secondary' },
          { c: p.system.site_bg, label: 'BG' },
          { c: p.system.text_color, label: 'Text' }
        ];

        var $sw = $('<div class="jj-preset-swatches" aria-hidden="true"></div>');
        swatches.forEach(function(s){
          var $s = $('<span class="jj-preset-swatch"></span>');
          $s.css('background-color', s.c);
          $s.attr('title', s.label + ': ' + s.c);
          $sw.append($s);
        });

        var $meta = $('<div class="jj-preset-meta"></div>');
        $meta.append($('<div class="jj-preset-title"></div>').text(p.name));
        $meta.append($('<div class="jj-preset-tags"></div>').text(p.tags.join(' · ')));
        $meta.append($('<div class="jj-preset-note"></div>').text(p.note));

        var $main = $('<button type="button" class="jj-preset-card-main"></button>');
        $main.append($sw).append($meta);
        $card.append($main);

        // AI 프리셋: 관리 액션(핀/이름/삭제/복사/내보내기)
        if (isAI) {
          var $cardActions = $('<div class="jj-preset-card-actions" aria-label="AI 프리셋 관리"></div>');

          var $pin = $('<button type="button" class="jj-preset-action jj-preset-ai-pin"><span class="dashicons dashicons-admin-post"></span></button>');
          $pin.attr('title', isPinned ? '상단 고정 해제' : '상단 고정');
          $pin.attr('data-op', isPinned ? 'unpin' : 'pin');

          var $rename = $('<button type="button" class="jj-preset-action jj-preset-ai-rename"><span class="dashicons dashicons-edit"></span></button>');
          $rename.attr('title', '이름 변경');

          var $copy = $('<button type="button" class="jj-preset-action jj-preset-ai-copy"><span class="dashicons dashicons-admin-page"></span></button>');
          $copy.attr('title', '팔레트 JSON 복사');

          var $export = $('<button type="button" class="jj-preset-action jj-preset-ai-export"><span class="dashicons dashicons-download"></span></button>');
          $export.attr('title', '팔레트 JSON 다운로드');

          var $del = $('<button type="button" class="jj-preset-action jj-preset-ai-delete"><span class="dashicons dashicons-trash"></span></button>');
          $del.attr('title', '삭제');

          $cardActions.append($pin, $rename, $copy, $export, $del);
          $card.append($cardActions);
        }

        // Bulk selection checkbox (AI only)
        if (isAI && bulkMode) {
          var $bulkCheck = $('<label class="jj-preset-bulk-check" title="대량 선택"><input type="checkbox" class="jj-ai-bulk-checkbox"></label>');
          $bulkCheck.find('input').attr('data-id', p.id).prop('checked', isBulkSelected(p.id));
          $card.append($bulkCheck);
        }

        $grid.append($card);
      });

      if (shown === 0) {
        $grid.append($('<div class="jj-preset-empty"></div>').text('검색 결과가 없습니다.'));
      }

      var $actions = $('<div class="jj-preset-actions"></div>');
      var $apply = $('<button type="button" class="button button-primary jj-preset-apply" disabled>선택한 프리셋 적용</button>');
      var $hint = $('<span class="jj-preset-hint">클릭 → 미리보기 → 적용 (언제든 수정 가능)</span>');
      $actions.append($apply).append($hint);

      $mount.append($grid).append($actions);

      // events
      $search.on('input', function(){ render($(this).val()); });

      $aiExportAll.on('click', function(){
        var list = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
          ? window.jj_admin_params.ai_palette_presets
          : [];
        if (!list.length) {
          alert('내보낼 AI 프리셋이 없습니다.');
          return;
        }
        var payload = {
          exported_at: new Date().toISOString(),
          items: list
        };
        try {
          var data = JSON.stringify(payload, null, 2);
          var blob = new Blob([data], { type: 'application/json' });
          var url = URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.href = url;
          a.download = 'ai-palette-presets-' + (new Date().toISOString().slice(0,10)) + '.json';
          document.body.appendChild(a);
          a.click();
          a.remove();
          setTimeout(function(){ URL.revokeObjectURL(url); }, 1000);
        } catch (e) {
          alert('내보내기에 실패했습니다.');
        }
      });

      $aiImport.on('click', function(){
        $aiImportInput.trigger('click');
      });

      $aiImportInput.on('change', function(){
        var file = this.files && this.files[0];
        if (!file) return;
        if (!file.name.toLowerCase().endsWith('.json')) {
          alert('JSON 파일만 업로드할 수 있습니다.');
          $(this).val('');
          return;
        }
        var reader = new FileReader();
        reader.onload = function(){
          var txt = String(reader.result || '');
          // Allow wrapped export format {items:[...]} or direct array/object
          var payloadTxt = txt;
          try {
            var parsed = JSON.parse(txt);
            if (parsed && parsed.items && Array.isArray(parsed.items)) {
              payloadTxt = JSON.stringify(parsed.items);
            }
          } catch (e) {
            // keep raw -> server will validate
          }

          if (!confirm('AI 팔레트 프리셋을 불러올까요? (중복 항목은 자동으로 건너뜁니다)')) {
            $(this).val('');
            return;
          }

          ajaxManageAiPreset('import', 'ai-import', null, payloadTxt).then(function(resp){
            if (!resp) return;
            showToast(resp.data.message || '가져오기 완료', 'success');
            render($search.val());
          });
        }.bind(this);
        reader.onerror = function(){
          alert('파일을 읽을 수 없습니다.');
          $(this).val('');
        }.bind(this);
        reader.readAsText(file);
      });

      function showToast(msg, kind) {
        try {
          $('.jj-preset-toast').remove();
          var $t = $('<div class="jj-preset-toast"></div>');
          $t.addClass(kind ? ('is-' + kind) : 'is-info');
          $t.text(msg || '');
          $mount.prepend($t);
          setTimeout(function(){
            $t.fadeOut(200, function(){ $(this).remove(); });
          }, 2600);
        } catch (e) {}
      }

      function ajaxManageAiPreset(op, id, name, payloadJson, ids) {
        var ajaxUrl = (window.jj_admin_params && window.jj_admin_params.ajax_url) ? window.jj_admin_params.ajax_url : (window.ajaxurl || '');
        var security = (window.jj_admin_params && window.jj_admin_params.nonce) ? window.jj_admin_params.nonce : '';
        if (!ajaxUrl || !security) {
          alert('AJAX 설정이 초기화되지 않았습니다.');
          return;
        }

        return $.ajax({
          url: ajaxUrl,
          type: 'POST',
          data: {
            action: 'jj_ai_palette_preset_manage',
            security: security,
            op: op,
            id: id,
            name: name || '',
            payload_json: payloadJson || '',
            ids: Array.isArray(ids) ? ids : []
          }
        }).then(function(resp){
          if (!resp || !resp.success) {
            alert((resp && resp.data && resp.data.message) ? resp.data.message : '요청 실패');
            return null;
          }
          // 갱신된 목록을 localize 영역에 반영 후 rebuild
          if (window.jj_admin_params) {
            window.jj_admin_params.ai_palette_presets = resp.data.items || [];
          }
          rebuildPresets();
          return resp;
        });
      }

      function copyToClipboard(text, fallbackLabel) {
        var t = String(text || '');
        if (!t) return;
        try {
          if (navigator && navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
            navigator.clipboard.writeText(t).then(function(){
              showToast('클립보드에 복사되었습니다.', 'success');
            }).catch(function(){
              window.prompt(fallbackLabel || '복사하세요:', t);
            });
            return;
          }
        } catch (e) {}
        window.prompt(fallbackLabel || '복사하세요:', t);
      }

      function downloadJson(filename, obj) {
        try {
          var data = JSON.stringify(obj, null, 2);
          var blob = new Blob([data], { type: 'application/json' });
          var url = URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.href = url;
          a.download = filename;
          document.body.appendChild(a);
          a.click();
          a.remove();
          setTimeout(function(){ URL.revokeObjectURL(url); }, 1000);
        } catch (e) {
          alert('다운로드에 실패했습니다.');
        }
      }

      function encodeBase64UrlUtf8(str) {
        var bytes;
        try {
          if (window.TextEncoder) {
            bytes = new TextEncoder().encode(String(str));
          }
        } catch (e) {}
        var bin = '';
        if (bytes) {
          for (var i = 0; i < bytes.length; i++) bin += String.fromCharCode(bytes[i]);
        } else {
          // fallback
          bin = unescape(encodeURIComponent(String(str)));
        }
        var b64 = btoa(bin);
        return b64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
      }

      function decodeBase64UrlUtf8(b64url) {
        var b64 = String(b64url || '').replace(/-/g, '+').replace(/_/g, '/');
        while (b64.length % 4) b64 += '=';
        var bin = atob(b64);
        // decode utf-8
        try {
          if (window.TextDecoder) {
            var bytes = new Uint8Array(bin.length);
            for (var i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
            return new TextDecoder().decode(bytes);
          }
        } catch (e) {}
        return decodeURIComponent(escape(bin));
      }

      function showCodeModal(title, code, onImport) {
        $(document).off('keydown.jjPresetModal');
        $('.jj-preset-modal').remove();
        var $modal = $('<div class="jj-preset-modal" role="dialog" aria-modal="true"></div>');
        var $back = $('<div class="jj-preset-modal-backdrop"></div>');
        var $card = $('<div class="jj-preset-modal-card"></div>');
        var $h = $('<div class="jj-preset-modal-title"></div>').text(title || '공유 코드');
        var $ta = $('<textarea class="jj-preset-modal-text" rows="6"></textarea>');
        $ta.val(code || '');
        var $btns = $('<div class="jj-preset-modal-actions"></div>');
        var $copy = $('<button type="button" class="button button-primary">복사</button>');
        var $close = $('<button type="button" class="button">닫기</button>');
        $btns.append($copy);
        if (typeof onImport === 'function') {
          var $import = $('<button type="button" class="button button-secondary">가져오기</button>');
          $btns.append($import);
          $import.on('click', function(){
            onImport($ta.val() || '');
          });
        }
        $btns.append($close);
        $card.append($h).append($ta).append($btns);
        $modal.append($back).append($card);
        $('body').append($modal);
        $ta.focus().select();

        function closeModal() {
          $(document).off('keydown.jjPresetModal');
          $modal.remove();
        }

        $(document).on('keydown.jjPresetModal', function(e){
          if (e && (e.key === 'Escape' || e.keyCode === 27)) {
            closeModal();
          }
        });

        $copy.on('click', function(){
          copyToClipboard($ta.val() || '', '아래 코드를 복사하세요:');
        });
        $close.on('click', function(){
          closeModal();
        });
        $back.on('click', function(){
          closeModal();
        });
      }

      function buildShareCodeFromItems(items) {
        var payload = {
          v: 1,
          exported_at: new Date().toISOString(),
          items: items || []
        };
        var json = JSON.stringify(payload);
        return 'JJAI1:' + encodeBase64UrlUtf8(json);
      }

      function parseShareCode(code) {
        var c = String(code || '').trim();
        if (!c) throw new Error('코드가 비어있습니다.');
        if (c.length > 120000) throw new Error('공유 코드가 너무 깁니다. (안전상 제한) JSON 파일로 가져오기 기능을 사용하세요.');
        if (c.indexOf('JJAI1:') !== 0) throw new Error('지원하지 않는 코드 형식입니다. (JJAI1: 로 시작해야 합니다)');
        var b64 = c.slice('JJAI1:'.length).trim();
        if (b64.length > 110000) throw new Error('공유 코드 데이터가 너무 큽니다. JSON 파일로 가져오기 기능을 사용하세요.');
        var json = decodeBase64UrlUtf8(b64);
        var parsed = JSON.parse(json);
        if (parsed && Array.isArray(parsed.items)) return parsed.items;
        if (Array.isArray(parsed)) return parsed;
        if (parsed && typeof parsed === 'object') return [parsed];
        throw new Error('코드 데이터 형식이 올바르지 않습니다.');
      }

      function findPreset(id) {
        return PRESETS.find(function(x){ return x && x.id === id; }) || null;
      }

      $grid.on('click', '.jj-preset-card-main', function(){
        var $card = $(this).closest('.jj-preset-card');
        var id = $card.attr('data-preset-id');
        if (bulkMode && $card.hasClass('is-ai')) {
          toggleBulkSelected(id, !isBulkSelected(id));
          $card.find('.jj-ai-bulk-checkbox').prop('checked', isBulkSelected(id));
          $bulkCount.text('선택: ' + getBulkSelectedIds().length + '개');
          return;
        }
        selectedId = id;
        render($search.val());
      });

      $grid.on('click', '.jj-preset-ai-pin', function(e){
        e.preventDefault(); e.stopPropagation();
        var $c = $(this).closest('.jj-preset-card');
        var id = $c.data('preset-id');
        var op = $(this).attr('data-op') || 'pin';
        ajaxManageAiPreset(op, id).then(function(resp){
          if (!resp) return;
          showToast(resp.data.message || '완료', 'info');
          render($search.val());
        });
      });

      $grid.on('click', '.jj-preset-ai-rename', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        var preset = findPreset(id);
        if (!preset) return;
        var next = window.prompt('새 이름을 입력하세요:', preset.name || '');
        if (!next) return;
        ajaxManageAiPreset('rename', id, next).then(function(resp){
          if (!resp) return;
          showToast(resp.data.message || '완료', 'success');
          render($search.val());
        });
      });

      $grid.on('click', '.jj-preset-ai-delete', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        if (!confirm('이 AI 프리셋을 삭제할까요?')) return;
        ajaxManageAiPreset('delete', id).then(function(resp){
          if (!resp) return;
          if (selectedId === id) selectedId = null;
          showToast(resp.data.message || '삭제됨', 'success');
          render($search.val());
        });
      });

      $grid.on('click', '.jj-preset-ai-copy', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        var preset = findPreset(id);
        if (!preset) return;
        var payload = {
          id: preset.id,
          name: preset.name,
          tags: preset.tags,
          note: preset.note,
          brand: preset.brand,
          system: preset.system,
          pinned: !!preset.pinned,
          created_at: preset.created_at || ''
        };
        copyToClipboard(JSON.stringify(payload, null, 2), '아래 JSON을 복사하세요:');
      });

      $grid.on('click', '.jj-preset-ai-export', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        var preset = findPreset(id);
        if (!preset) return;
        var payload = {
          id: preset.id,
          name: preset.name,
          tags: preset.tags,
          note: preset.note,
          brand: preset.brand,
          system: preset.system,
          pinned: !!preset.pinned,
          created_at: preset.created_at || ''
        };
        downloadJson('ai-palette-' + id + '.json', payload);
      });

      $grid.on('change', '.jj-ai-bulk-checkbox', function(e){
        var id = $(this).attr('data-id');
        toggleBulkSelected(id, $(this).is(':checked'));
        // bulk count update (no full rerender)
        $bulkCount.text('선택: ' + getBulkSelectedIds().length + '개');
      });

      // Bulk UI toggling
      function refreshBulkUi() {
        var ids = getBulkSelectedIds();
        var show = !!bulkMode;
        $bulkSelectVisible.toggle(show);
        $bulkInvert.toggle(show);
        $bulkPin.toggle(show);
        $bulkUnpin.toggle(show);
        $bulkDelete.toggle(show);
        $bulkExport.toggle(show);
        $bulkShare.toggle(show);
        $bulkClear.toggle(show);
        $bulkCount.toggle(show);
        if (show) {
          $bulkCount.text('선택: ' + ids.length + '개');
        }
      }
      refreshBulkUi();

      $bulkToggle.on('change', function(){
        bulkMode = $(this).find('input').is(':checked');
        if (!bulkMode) {
          clearBulkSelection();
        }
        render($search.val());
      });

      function getAiPresetItemsByIds(ids) {
        var list = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
          ? window.jj_admin_params.ai_palette_presets
          : [];
        var map = {};
        list.forEach(function(p){ if (p && p.id) map[p.id] = p; });
        return (ids || []).map(function(id){ return map[id]; }).filter(function(x){ return !!x; });
      }

      function downloadSelectedPresets(ids) {
        var items = getAiPresetItemsByIds(ids);
        if (!items.length) {
          alert('선택된 AI 프리셋이 없습니다.');
          return;
        }
        downloadJson('ai-palette-selected-' + (new Date().toISOString().slice(0,10)) + '.json', { exported_at: new Date().toISOString(), items: items });
      }

      function doBulkOp(op) {
        var ids = getBulkSelectedIds();
        if (!ids.length) {
          alert('선택된 항목이 없습니다.');
          return;
        }
        var confirmMsg = '작업을 진행할까요?';
        if (op === 'bulk_delete') confirmMsg = '선택한 ' + ids.length + '개를 삭제할까요? (되돌릴 수 없습니다)';
        if (op === 'bulk_pin') confirmMsg = '선택한 ' + ids.length + '개를 상단 고정할까요?';
        if (op === 'bulk_unpin') confirmMsg = '선택한 ' + ids.length + '개의 고정을 해제할까요?';
        if (!confirm(confirmMsg)) return;
        ajaxManageAiPreset(op, 'ai-bulk', null, null, ids).then(function(resp){
          if (!resp) return;
          clearBulkSelection();
          showToast(resp.data.message || '완료', 'success');
          render($search.val());
        });
      }

      $bulkPin.on('click', function(){ doBulkOp('bulk_pin'); });
      $bulkUnpin.on('click', function(){ doBulkOp('bulk_unpin'); });
      $bulkDelete.on('click', function(){ doBulkOp('bulk_delete'); });
      $bulkExport.on('click', function(){ downloadSelectedPresets(getBulkSelectedIds()); });
      $bulkClear.on('click', function(){ clearBulkSelection(); render($search.val()); });

      $bulkSelectVisible.on('click', function(){
        // 현재 화면에 렌더된 AI 프리셋 전체 선택(검색 결과 기준)
        var $aiCards = $grid.find('.jj-preset-card.is-ai');
        if (!$aiCards.length) {
          alert('선택할 AI 프리셋이 없습니다.');
          return;
        }
        $aiCards.each(function(){
          var id = $(this).attr('data-preset-id');
          toggleBulkSelected(id, true);
          $(this).find('.jj-ai-bulk-checkbox').prop('checked', true);
        });
        $bulkCount.text('선택: ' + getBulkSelectedIds().length + '개');
      });

      $bulkInvert.on('click', function(){
        var $aiCards = $grid.find('.jj-preset-card.is-ai');
        if (!$aiCards.length) {
          alert('선택할 AI 프리셋이 없습니다.');
          return;
        }
        $aiCards.each(function(){
          var id = $(this).attr('data-preset-id');
          var next = !isBulkSelected(id);
          toggleBulkSelected(id, next);
          $(this).find('.jj-ai-bulk-checkbox').prop('checked', next);
        });
        $bulkCount.text('선택: ' + getBulkSelectedIds().length + '개');
      });

      $bulkShare.on('click', function(){
        var ids = getBulkSelectedIds();
        if (!ids.length) {
          alert('선택된 항목이 없습니다.');
          return;
        }
        var items = getAiPresetItemsByIds(ids);
        if (!items.length) {
          alert('선택된 AI 프리셋을 찾을 수 없습니다.');
          return;
        }
        // 공유 코드에는 최소 필드만 넣어 사이즈/호환성을 안정화
        var payloadItems = items.map(function(p){
          return {
            name: p.name,
            tags: p.tags,
            note: p.note,
            brand: p.brand,
            system: p.system,
            pinned: !!p.pinned
          };
        });
        var code = buildShareCodeFromItems(payloadItems);
        showCodeModal('AI 팔레트 공유 코드 (JJAI1)', code);
        showToast('공유 코드가 생성되었습니다. (복사 버튼을 눌러 공유)', 'info');
      });

      $aiImportCode.on('click', function(){
        showCodeModal('공유 코드 붙여넣기 (JJAI1)', '', function(text){
          try {
            var items = parseShareCode(text);
            if (!items || !items.length) {
              alert('가져올 항목이 없습니다.');
              return;
            }
            if (items.length > 100) {
              alert('가져올 항목이 너무 많습니다. (안전상 제한: 100개) JSON 파일로 나눠서 가져오세요.');
              return;
            }
            var names = items.map(function(x){ return x && x.name ? String(x.name) : ''; }).filter(Boolean).slice(0, 6);
            var preview = '가져오기: ' + items.length + '개';
            if (names.length) {
              preview += '\n\n미리보기:\n- ' + names.join('\n- ');
              if (items.length > names.length) {
                preview += '\n- ...';
              }
            }
            if (items.length > 20) {
              preview += '\n\n참고: 저장 한도는 최대 20개이며, 초과분은 자동으로 제외될 수 있습니다.';
            }
            if (!confirm(preview + '\n\n가져올까요?')) {
              return;
            }
            ajaxManageAiPreset('import', 'ai-import', null, JSON.stringify(items)).then(function(resp){
              if (!resp) return;
              showToast(resp.data.message || '가져오기 완료', 'success');
              render($search.val());
              $('.jj-preset-modal').remove();
            });
          } catch (e) {
            alert(e && e.message ? e.message : '코드 파싱 실패');
          }
        });
      });

      $apply.prop('disabled', !selectedId);
      $apply.on('click', function(){
        var preset = PRESETS.find(function(x){ return x.id === selectedId; });
        if (!preset) return;

        var includeSystem = $includeSystem.find('input').is(':checked');
        var includeComponents = $includeComponents.find('input').is(':checked');

        // brand
        setField('palettes[brand][primary_color]', preset.brand.primary);
        setField('palettes[brand][primary_color_hover]', preset.brand.primary_hover);
        setField('palettes[brand][secondary_color]', preset.brand.secondary);
        setField('palettes[brand][secondary_color_hover]', preset.brand.secondary_hover);

        // system (옵션)
        if (includeSystem) {
          setField('palettes[system][site_bg]', preset.system.site_bg);
          setField('palettes[system][content_bg]', preset.system.content_bg);
          setField('palettes[system][text_color]', preset.system.text_color);
          setField('palettes[system][link_color]', preset.system.link_color);
        }

        // 미리보기 갱신
        updateInlinePreview();

        // 버튼/폼/링크 자동 매핑 (기존 "일괄 적용" 로직 재사용)
        if (includeComponents) {
          var $btn = $('#jj-style-guide-form').find('.jj-apply-brand-palette-to-components').first();
          if ($btn.length) {
            $btn.trigger('click');
          }
        }
      });
    }

    render('');
    updateInlinePreview();

    // 색상 변경 시 미리보기 자동 갱신
    $('#jj-style-guide-form').on('change input', '.jj-color-field', function(){
      updateInlinePreview();
    });
  });
})(jQuery);

