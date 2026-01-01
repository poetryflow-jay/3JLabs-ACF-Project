/**
 * JJ Style Guide - Typography Presets (1-click)
 *
 * - Style Center(설정 페이지)의 Typography 섹션에서 프리셋을 한 번에 적용합니다.
 * - 적용은 data-setting-key 필드 값을 변경(trigger input/change)하는 방식으로 통일합니다.
 * - “스타일 저장” 버튼을 눌러야 DB에 저장됩니다(프리셋 적용만으로는 저장하지 않음).
 */
(function($){
  'use strict';

  var $form = null;
  var $mount = null;
  var undoSnapshot = null;
  var undoLabel = '';

  var TAGS = ['h1','h2','h3','h4','h5','h6','p'];
  var DEVICES = ['desktop','laptop','tablet','phablet','mobile','phone_small','desktop_qhd','desktop_uhd','desktop_5k','desktop_8k'];

  function toast(msg, type) {
    if (window.JJUtils && typeof window.JJUtils.showToast === 'function') {
      window.JJUtils.showToast(msg, type || 'info');
      return;
    }
    var $t = $mount.find('.jj-typo-preset-toast');
    if (!$t.length) return;
    $t.removeClass('is-success is-info is-error');
    if (type === 'success') $t.addClass('is-success');
    if (type === 'error') $t.addClass('is-error');
    if (!type || type === 'info') $t.addClass('is-info');
    $t.text(msg).show();
    setTimeout(function(){ $t.fadeOut(200); }, 2500);
  }

  function setField(settingKey, value) {
    if (!settingKey) return;
    var $field = $form.find('[data-setting-key="' + settingKey + '"]');
    if (!$field.length) return;
    $field.val(value);
    $field.trigger('input');
    $field.trigger('change');
  }

  function getField(settingKey) {
    var $field = $form.find('[data-setting-key="' + settingKey + '"]');
    return $field.length ? String($field.val() || '') : '';
  }

  function buildKeyList() {
    var keys = [];
    keys.push('typography_settings[base_px]');
    keys.push('typography_settings[unit]');

    TAGS.forEach(function(tag){
      keys.push('typography[' + tag + '][font_family]');
      keys.push('typography[' + tag + '][font_weight]');
      keys.push('typography[' + tag + '][font_style]');
      keys.push('typography[' + tag + '][line_height]');
      keys.push('typography[' + tag + '][letter_spacing]');
      keys.push('typography[' + tag + '][text_transform]');
      DEVICES.forEach(function(dev){
        keys.push('typography[' + tag + '][font_size][' + dev + ']');
      });
    });
    return keys;
  }

  function takeSnapshot() {
    var snap = {};
    buildKeyList().forEach(function(k){
      snap[k] = getField(k);
    });
    return snap;
  }

  function applySnapshot(snap) {
    if (!snap) return;
    Object.keys(snap).forEach(function(k){
      setField(k, snap[k]);
    });
  }

  function baseSizes() {
    // 기준(px) 권장값(설정 UI placeholder와 동일 계열)
    return {
      h1: { desktop: 40, laptop: 38, tablet: 36, phablet: 32, mobile: 30, phone_small: 28, desktop_qhd: 44, desktop_uhd: 48, desktop_5k: 52, desktop_8k: 60 },
      h2: { desktop: 32, laptop: 30, tablet: 28, phablet: 26, mobile: 24, phone_small: 22, desktop_qhd: 36, desktop_uhd: 40, desktop_5k: 44, desktop_8k: 52 },
      h3: { desktop: 26, laptop: 24, tablet: 22, phablet: 20, mobile: 19, phone_small: 18, desktop_qhd: 28, desktop_uhd: 30, desktop_5k: 32, desktop_8k: 36 },
      h4: { desktop: 22, laptop: 20, tablet: 20, phablet: 18, mobile: 18, phone_small: 16, desktop_qhd: 24, desktop_uhd: 26, desktop_5k: 28, desktop_8k: 32 },
      h5: { desktop: 18, laptop: 18, tablet: 18, phablet: 16, mobile: 16, phone_small: 15, desktop_qhd: 20, desktop_uhd: 22, desktop_5k: 24, desktop_8k: 28 },
      h6: { desktop: 16, laptop: 16, tablet: 16, phablet: 15, mobile: 15, phone_small: 14, desktop_qhd: 18, desktop_uhd: 20, desktop_5k: 22, desktop_8k: 24 },
      p:  { desktop: 16, laptop: 16, tablet: 16, phablet: 15, mobile: 15, phone_small: 14, desktop_qhd: 16, desktop_uhd: 18, desktop_5k: 18, desktop_8k: 20 }
    };
  }

  function scaleSizes(map, delta) {
    var out = {};
    Object.keys(map).forEach(function(tag){
      out[tag] = {};
      Object.keys(map[tag]).forEach(function(dev){
        out[tag][dev] = Math.max(10, Math.round(map[tag][dev] + delta));
      });
    });
    return out;
  }

  function applyPreset(p) {
    undoSnapshot = takeSnapshot();
    undoLabel = p.name || '';
    $mount.find('.jj-typo-undo').prop('disabled', false);

    // typography_settings
    if (p.settings) {
      if (p.settings.base_px) setField('typography_settings[base_px]', String(p.settings.base_px));
      if (p.settings.unit) setField('typography_settings[unit]', String(p.settings.unit));
    }

    // per-tag settings
    TAGS.forEach(function(tag){
      var st = (p.tag && p.tag[tag]) ? p.tag[tag] : (p.tag && p.tag._all ? p.tag._all : {});
      if (!st) st = {};

      if (st.font_family !== undefined) setField('typography[' + tag + '][font_family]', String(st.font_family));
      if (st.font_weight !== undefined) setField('typography[' + tag + '][font_weight]', String(st.font_weight));
      if (st.font_style !== undefined) setField('typography[' + tag + '][font_style]', String(st.font_style));
      if (st.line_height !== undefined) setField('typography[' + tag + '][line_height]', String(st.line_height));
      if (st.letter_spacing !== undefined) setField('typography[' + tag + '][letter_spacing]', String(st.letter_spacing));
      if (st.text_transform !== undefined) setField('typography[' + tag + '][text_transform]', String(st.text_transform));
    });

    // sizes
    if (p.sizes) {
      Object.keys(p.sizes).forEach(function(tag){
        DEVICES.forEach(function(dev){
          if (p.sizes[tag] && p.sizes[tag][dev] !== undefined) {
            setField('typography[' + tag + '][font_size][' + dev + ']', String(p.sizes[tag][dev]));
          }
        });
      });
    }

    toast('타이포그래피 프리셋 적용됨: ' + (p.name || '' ) + ' (저장하려면 “스타일 저장” 클릭)', 'success');
  }

  function renderUI(presets) {
    var html = '';
    html += '<div class="jj-typo-quickstart">';
    html += '  <div class="jj-typo-toolbar">';
    html += '    <input type="text" class="jj-typo-search" placeholder="프리셋 검색(예: SaaS, Editorial, 8K)" />';
    html += '    <button type="button" class="button button-secondary jj-typo-undo" disabled>되돌리기</button>';
    html += '  </div>';
    html += '  <div class="jj-typo-preset-grid"></div>';
    html += '  <div class="jj-typo-preset-toast" style="display:none;"></div>';
    html += '</div>';
    $mount.html(html);

    function draw(list) {
      var $grid = $mount.find('.jj-typo-preset-grid');
      var cards = '';
      list.forEach(function(p){
        var tags = Array.isArray(p.tags) ? p.tags : [];
        cards += '<div class="jj-typo-card">';
        cards += '  <div class="jj-typo-card-head">';
        cards += '    <div class="jj-typo-card-title">' + String(p.name || '') + '</div>';
        cards += '    <button type="button" class="button button-primary jj-typo-apply" data-id="' + String(p.id || '') + '">적용</button>';
        cards += '  </div>';
        if (p.note) {
          cards += '  <div class="jj-typo-card-note">' + String(p.note) + '</div>';
        }
        if (tags.length) {
          cards += '  <div class="jj-typo-card-tags">' + tags.map(function(t){ return '<span class="jj-typo-tag">' + String(t) + '</span>'; }).join('') + '</div>';
        }
        cards += '</div>';
      });
      $grid.html(cards);
    }

    draw(presets);

    $mount.on('click', '.jj-typo-apply', function(){
      var id = String($(this).data('id') || '');
      var p = null;
      presets.forEach(function(x){ if (x.id === id) p = x; });
      if (!p) return;
      applyPreset(p);
    });

    $mount.on('click', '.jj-typo-undo', function(){
      if (!undoSnapshot) return;
      applySnapshot(undoSnapshot);
      toast('되돌리기 완료' + (undoLabel ? (': ' + undoLabel) : ''), 'info');
    });

    $mount.on('input', '.jj-typo-search', function(){
      var q = String($(this).val() || '').toLowerCase().trim();
      if (!q) {
        draw(presets);
        return;
      }
      var filtered = presets.filter(function(p){
        var hay = (p.name || '') + ' ' + (p.note || '') + ' ' + (Array.isArray(p.tags) ? p.tags.join(' ') : '');
        return hay.toLowerCase().indexOf(q) !== -1;
      });
      draw(filtered);
    });
  }

  $(document).ready(function(){
    $mount = $('#jj-typography-presets');
    $form = $('#jj-style-guide-form');
    if (!$mount.length || !$form.length) return;

    var s = baseSizes();

    var presets = [
      {
        id: 'saas-modern',
        name: 'SaaS Modern (Rem)',
        tags: ['SaaS', '모던', '가독성', 'QHD/4K/5K/8K'],
        note: '전환/제품 랜딩에 잘 어울리는 모던 스케일. rem 기준(기본 16).',
        settings: { base_px: 16, unit: 'rem' },
        tag: {
          _all: {
            font_family: "Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans KR', sans-serif",
            font_style: 'normal',
            text_transform: 'none',
            letter_spacing: '0'
          },
          h1: { font_weight: '800', line_height: '1.15', letter_spacing: '-0.6' },
          h2: { font_weight: '800', line_height: '1.2',  letter_spacing: '-0.4' },
          h3: { font_weight: '700', line_height: '1.3',  letter_spacing: '-0.2' },
          h4: { font_weight: '700', line_height: '1.35', letter_spacing: '-0.1' },
          p:  { font_weight: '400', line_height: '1.7',  letter_spacing: '0' }
        },
        sizes: s
      },
      {
        id: 'editorial-serif',
        name: 'Editorial (Readable)',
        tags: ['블로그', '매거진', '콘텐츠', '읽기'],
        note: '본문 가독성(행간) 강화 + 헤딩은 세리프로 대비를 주는 구성. rem 기준(18).',
        settings: { base_px: 18, unit: 'rem' },
        tag: {
          _all: { font_style: 'normal', text_transform: 'none', letter_spacing: '0' },
          h1: { font_family: "Georgia, 'Times New Roman', Times, serif", font_weight: '700', line_height: '1.15', letter_spacing: '-0.3' },
          h2: { font_family: "Georgia, 'Times New Roman', Times, serif", font_weight: '700', line_height: '1.2',  letter_spacing: '-0.2' },
          h3: { font_family: "Georgia, 'Times New Roman', Times, serif", font_weight: '700', line_height: '1.25', letter_spacing: '-0.1' },
          h4: { font_family: "Georgia, 'Times New Roman', Times, serif", font_weight: '700', line_height: '1.3',  letter_spacing: '0' },
          p:  { font_family: "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans KR', sans-serif", font_weight: '400', line_height: '1.85', letter_spacing: '0' }
        },
        sizes: scaleSizes(s, 2)
      },
      {
        id: 'minimal-clean',
        name: 'Minimal Clean',
        tags: ['미니멀', '라이트', '깔끔함'],
        note: '불필요한 대비를 줄이고, 적당한 스케일로 정돈된 느낌. rem 기준(16).',
        settings: { base_px: 16, unit: 'rem' },
        tag: {
          _all: {
            font_family: "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans KR', sans-serif",
            font_style: 'normal',
            text_transform: 'none',
            letter_spacing: '0'
          },
          h1: { font_weight: '700', line_height: '1.2', letter_spacing: '-0.4' },
          h2: { font_weight: '700', line_height: '1.25', letter_spacing: '-0.2' },
          h3: { font_weight: '600', line_height: '1.3', letter_spacing: '-0.1' },
          p:  { font_weight: '400', line_height: '1.65', letter_spacing: '0' }
        },
        sizes: scaleSizes(s, -1)
      },
      {
        id: 'ecommerce-cta',
        name: 'E-commerce / CTA',
        tags: ['커머스', 'CTA', '전환'],
        note: '버튼/카드/가격표 같은 UI에 잘 맞는 굵기 중심 스케일. rem 기준(16).',
        settings: { base_px: 16, unit: 'rem' },
        tag: {
          _all: {
            font_family: "Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans KR', sans-serif",
            font_style: 'normal',
            text_transform: 'none'
          },
          h1: { font_weight: '900', line_height: '1.1', letter_spacing: '-0.8' },
          h2: { font_weight: '800', line_height: '1.15', letter_spacing: '-0.5' },
          h3: { font_weight: '700', line_height: '1.2', letter_spacing: '-0.2' },
          p:  { font_weight: '400', line_height: '1.7', letter_spacing: '0' }
        },
        sizes: scaleSizes(s, 0)
      }
    ];

    renderUI(presets);
  });

})(jQuery);

