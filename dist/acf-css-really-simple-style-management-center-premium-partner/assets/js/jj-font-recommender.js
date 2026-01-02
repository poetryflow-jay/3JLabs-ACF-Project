/**
 * JJ Font Recommender
 * [Phase 12] 한국어/영문 폰트 추천 + 원클릭 적용
 */
(function($) {
    'use strict';

    var $container = null;
    var fontData = null;
    var ajaxUrl = '';
    var nonce = '';

    function init() {
        $container = $('#jj-font-recommender-container');
        if (!$container.length) return;

        // localized data
        if (typeof jjFontRecommender !== 'undefined') {
            ajaxUrl = jjFontRecommender.ajax_url || '';
            nonce = jjFontRecommender.nonce || '';
        }

        if (!ajaxUrl || !nonce) {
            $container.html('<p class="jj-font-error">폰트 추천 기능을 사용할 수 없습니다.</p>');
            return;
        }

        loadFonts();
    }

    function loadFonts() {
        $.post(ajaxUrl, {
            action: 'jj_get_font_recommendations',
            nonce: nonce
        }).done(function(resp) {
            if (resp && resp.success && resp.data) {
                fontData = resp.data;
                renderFonts();
            } else {
                $container.html('<p class="jj-font-error">폰트 목록을 불러올 수 없습니다.</p>');
            }
        }).fail(function() {
            $container.html('<p class="jj-font-error">서버와 통신할 수 없습니다.</p>');
        });
    }

    function esc(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderFonts() {
        if (!fontData) return;

        var html = '';
        html += '<div class="jj-font-search-wrap" style="margin-bottom: 16px;">';
        html += '<input type="text" id="jj-font-search" class="jj-font-search" placeholder="폰트 검색..." style="width: 100%; max-width: 400px; height: 38px; padding: 0 12px; border-radius: 8px; border: 1px solid #c3c4c7;" />';
        html += '</div>';

        // 무료 한국어
        if (fontData.free_korean) {
            html += renderCategory(fontData.free_korean, 'korean');
        }

        // 무료 영문
        if (fontData.free_english) {
            html += renderCategory(fontData.free_english, 'english');
        }

        // 유료/프리미엄
        if (fontData.premium) {
            html += renderCategory(fontData.premium, 'premium');
        }

        $container.html(html);

        // 검색 바인드
        $('#jj-font-search').on('input', function() {
            var q = $(this).val().toLowerCase().trim();
            $('.jj-font-card').each(function() {
                var name = $(this).data('font-name') || '';
                if (!q || name.toLowerCase().indexOf(q) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // 적용 버튼
        $(document).on('click', '.jj-font-apply', function(e) {
            e.preventDefault();
            var $card = $(this).closest('.jj-font-card');
            var family = $card.data('font-family') || '';
            var url = $card.data('font-url') || '';
            var target = $card.data('font-target') || 'korean';
            applyFont(family, url, target, $(this));
        });
    }

    function renderCategory(cat, target) {
        if (!cat || !cat.fonts || !cat.fonts.length) return '';

        var html = '';
        html += '<div class="jj-font-category" data-category="' + esc(target) + '">';
        html += '<h4 class="jj-font-category-title" style="margin: 20px 0 10px 0; font-size: 15px; font-weight: 800;">' + esc(cat.title || '') + '</h4>';
        if (cat.description) {
            html += '<p class="jj-font-category-desc" style="margin: 0 0 12px 0; font-size: 13px; color: #50575e;">' + esc(cat.description) + '</p>';
        }
        html += '<div class="jj-font-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px;">';

        cat.fonts.forEach(function(font) {
            html += renderFontCard(font, target);
        });

        html += '</div></div>';
        return html;
    }

    function renderFontCard(font, target) {
        var url = font.google_url || font.cdn_url || '';
        var html = '';
        html += '<div class="jj-font-card" data-font-name="' + esc(font.name) + '" data-font-family="' + esc(font.family) + '" data-font-url="' + esc(url) + '" data-font-target="' + esc(target) + '" style="border: 1px solid #c3c4c7; border-radius: 10px; padding: 14px; background: #fff;">';
        
        html += '<div class="jj-font-card-head" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">';
        html += '<div class="jj-font-card-name" style="font-weight: 800; font-size: 14px;">' + esc(font.name) + '</div>';
        html += '<div class="jj-font-card-type" style="font-size: 11px; color: #50575e; padding: 2px 8px; background: #f0f0f1; border-radius: 999px;">' + esc(font.type || 'sans-serif') + '</div>';
        html += '</div>';

        if (font.note) {
            html += '<div class="jj-font-card-note" style="font-size: 12px; color: #50575e; margin-bottom: 10px; line-height: 1.5;">' + esc(font.note) + '</div>';
        }

        html += '<div class="jj-font-card-meta" style="font-size: 11px; color: #888; margin-bottom: 10px;">';
        if (font.weights) {
            html += '<span>Weights: ' + esc(font.weights) + '</span>';
        }
        if (font.license) {
            html += '<span style="margin-left: 10px;">License: ' + esc(font.license) + '</span>';
        }
        html += '</div>';

        html += '<div class="jj-font-card-actions" style="display: flex; gap: 8px; flex-wrap: wrap;">';
        
        if (font.is_free !== false && url) {
            html += '<button type="button" class="button button-primary jj-font-apply" style="font-size: 12px; height: 30px; line-height: 28px; padding: 0 12px;">적용</button>';
        }
        
        if (font.download_url) {
            html += '<a href="' + esc(font.download_url) + '" class="button" target="_blank" rel="noopener" style="font-size: 12px; height: 30px; line-height: 28px; padding: 0 12px;">다운로드</a>';
        }
        
        if (font.purchase_url) {
            html += '<a href="' + esc(font.purchase_url) + '" class="button" target="_blank" rel="noopener" style="font-size: 12px; height: 30px; line-height: 28px; padding: 0 12px;">구매</a>';
        }

        html += '</div>';
        html += '</div>';
        return html;
    }

    function applyFont(family, url, target, $btn) {
        if (!family) {
            alert('폰트 정보가 없습니다.');
            return;
        }

        $btn.prop('disabled', true).text('적용 중...');

        $.post(ajaxUrl, {
            action: 'jj_apply_google_font',
            nonce: nonce,
            font_family: family,
            font_url: url,
            target: target
        }).done(function(resp) {
            if (resp && resp.success) {
                $btn.text('적용됨 ✓').addClass('button-success');
                
                // 해당 역할의 Font Family 입력 필드에 값 채우기
                var $familyInput = $('#jj-font-' + target + '-family');
                if ($familyInput.length) {
                    $familyInput.val(family).trigger('change');
                }
                
                // 토스트 메시지
                showToast(resp.data.message || '폰트가 적용되었습니다.', 'success');
            } else {
                $btn.prop('disabled', false).text('적용');
                var msg = (resp && resp.data && resp.data.message) ? resp.data.message : '적용 실패';
                showToast(msg, 'error');
            }
        }).fail(function() {
            $btn.prop('disabled', false).text('적용');
            showToast('서버 오류', 'error');
        });
    }

    function showToast(msg, type) {
        if (window.JJUtils && typeof window.JJUtils.showToast === 'function') {
            window.JJUtils.showToast(msg, type);
            return;
        }
        // 폴백
        alert(msg);
    }

    $(document).ready(function() {
        init();
    });

})(jQuery);
