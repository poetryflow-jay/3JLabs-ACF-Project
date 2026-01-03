jQuery(document).ready(function($) {
    
    // --- [ 1. 설정 로드 및 초기화 (v3.4.9 '유지') ] ---
    var currentSettings = jj_admin_params.settings || {};

    // [v1.8.0-beta4 '재탄생'] 1a. 컬러 피커 초기화 로직
    function initColorPickers($container) {
        $container.find('.jj-color-field, .jj-color-picker').each(function() {
            var $input = $(this);
            
            // [v1.9.0 '갱신'] 이미 초기화되었다면 파괴하고 다시 생성
            if ( $input.closest('.wp-picker-container').length > 0 ) {
                $input.wpColorPicker('destroy');
            }

            var settingKey = $input.data('setting-key');
            if (!settingKey) {
                settingKey = $input.data('id');
                if (settingKey) { settingKey = settingKey.replace('-', '[') + ']'; } else { return; }
            }
            var value = getNestedObject(currentSettings, settingKey.split(/\[|\]/).filter(Boolean));
            
            $input.wpColorPicker({
                change: function(event, ui) {
                    var newColor = ui.color.toString();
                    var keyParts = $(this).data('setting-key').split(/\[|\]/).filter(Boolean);
                    if (keyParts.length === 0) { keyParts = $(this).data('id').replace('-', '[').split(/\[|\]/).filter(Boolean); }
                    setNestedObject(currentSettings, keyParts, newColor);
                    
                    // [v3.3 '제련'] 미리보기 '즉시' '갱신'
                    $input.closest('.jj-color-card').find('.jj-color-preview').css('background-color', newColor);
                    if (settingKey.startsWith('buttons')) {
                        updateButtonPreview();
                    } else if (settingKey.startsWith('forms')) {
                        updateFormPreview();
                    }
                    // [v5.0.3] 실시간 프리뷰 CSS 업데이트 (새로고침 없이)
                    if (typeof window.JJLivePreview !== 'undefined' && window.JJLivePreview.isPreviewOpen()) {
                        window.JJLivePreview.handleSettingChange();
                    } else {
                        // 폴백: 기존 새로고침 방식
                        refreshPreviewIfOpen();
                    }
                }
            });

            if (value) {
                $input.wpColorPicker('color', value);
                $input.closest('.jj-control-group').find('.jj-color-preview').css('background-color', value);
            }
        });
    }

    // [v1.9.0-beta4 '제련'] 1b. '설정 갱신'을 위해 '초기화' 로직을 'initializeAllFields' 함수로 '재탄생'
    // [v3.5.0-dev10 '최적화'] 선택적인 루트 컨테이너를 받아 부분 초기화 가능
    function initializeAllFields($root) {
        $root = $root || $('#jj-style-guide-form');

        // 1b-1. '전역' 및 '컨텍스트' 컬러 피커 '갱신'
        initColorPickers( $root );

        // 1b-2. '일반 필드'(data-setting-key) '갱신'
        $root.find('.jj-data-field').each(function() {
            var $input = $(this);
            if ($input.hasClass('jj-color-field') || $input.hasClass('jj-color-picker')) return; 
            var settingKey = $input.data('setting-key'); 
            if (!settingKey) return; 
            var value = getNestedObject(currentSettings, settingKey.split(/\[|\]/).filter(Boolean));
            
            if ($input.is(':checkbox') || $input.is(':radio')) {
                // (토글 스위치는 '1b-3'에서 별도 처리)
                if (!$input.hasClass('jj-toggle-switch')) {
                    $input.prop('checked', $input.val() == value);
                }
            } else {
                 if (value !== undefined) {
                    $input.val(value);
                }
            }
        });
        
        // 1b-3. '컨텍스트' 토글 스위치 '갱신'
        $root.find('.jj-toggle-switch').each(function() {
            var $checkbox = $(this);
            var $target = $($checkbox.data('toggle-target'));
            var keyParts = $checkbox.data('setting-key').split(/\[|\]/).filter(Boolean);
            var value = getNestedObject(currentSettings, keyParts);

            if (value == '1') { // '1' (활성화)
                $checkbox.prop('checked', true);
                $target.show();
                // 토글이 열릴 때, 그 안의 컬러 피커를 '활성화'
                if ( !$target.data('color-pickers-initialized') ) {
                    initColorPickers( $target );
                    $target.data('color-pickers-initialized', true);
                }
            } else { // '0' 또는 undefined (비활성화)
                $checkbox.prop('checked', false);
                $target.hide();
            }
        }); 

        // 1b-4. 컬러 도구(스포이드 / 현재 색상 불러오기) UI 보강
        enhanceColorTools($root);

        // 1b-5. '미리보기' 갱신
        updateButtonPreview();
        $root.find('.jj-typography-row').each(function() {
            updateTypographyPreview($(this).data('type'));
        });
        
        // [v3.3 '신설'] '폼' 미리보기 '최초' '갱신'
        updateFormPreview();
        
        // [v5.0.3] 실시간 프리뷰 CSS 업데이트 (새로고침 없이)
        if (typeof window.JJLivePreview !== 'undefined' && window.JJLivePreview.isPreviewOpen()) {
            window.JJLivePreview.handleSettingChange();
        } else {
            // 폴백: 기존 새로고침 방식
            refreshPreviewIfOpen();
        }
    }

    // [v3.6.0 '신규'] 1c. 컬러 도구(스포이드 / 현재 색상 불러오기) 구성
    function enhanceColorTools($root) {
        $root = $root || $('#jj-style-guide-form');

        $root.find('.jj-color-field').each(function() {
            var $input = $(this);
            if ($input.data('jj-color-tools-initialized')) {
                return;
            }
            $input.data('jj-color-tools-initialized', true);

            var $group = $input.closest('.jj-control-group');
            if ( !$group.length ) return;

            var targetId = $input.attr('id');
            var $tools = $('<div class="jj-color-tools" style="margin-top:6px; display:flex; gap:6px; flex-wrap:wrap;"></div>');

            // 스포이드 버튼
            var $eyedropperBtn = $('<button type="button" class="button button-small jj-color-eyedropper"></button>')
                .text(jj_admin_params.i18n && jj_admin_params.i18n.eyedropper ? jj_admin_params.i18n.eyedropper : '스포이드')
                .attr('data-target', targetId ? ('#' + targetId) : '');

            // 현재 색상 불러오기 버튼 (프리뷰 박스 또는 기존 값 기준)
            var $currentBtn = $('<button type="button" class="button button-small jj-color-use-current"></button>')
                .text(jj_admin_params.i18n && jj_admin_params.i18n.use_current ? jj_admin_params.i18n.use_current : '현재 색상 불러오기')
                .attr('data-target', targetId ? ('#' + targetId) : '');

            $tools.append($eyedropperBtn).append($currentBtn);
            $group.append($tools);
        });
    }

    // 1d. 페이지 '최초' 로드 시: 모든 필드 '초기화'
    initializeAllFields();
    
    // [v3.5.0-dev10 '레이지 로딩'] 스포크/테마 UI 지연 로드 (초기 로드 후 500ms 지연)
    setTimeout(function() {
        loadSpokesAndThemesUI();
    }, 500);
    
    // [v3.5.0-dev10 '레이지 로딩'] 스포크/테마 UI 동적 로드
    function loadSpokesAndThemesUI() {
        var $spokesContainer = $('#jj-lazy-load-spokes');
        var $themesContainer = $('#jj-lazy-load-themes');
        
        // 스포크 UI 로드
        if ($spokesContainer.length && $spokesContainer.data('lazy-loaded') !== true) {
            $spokesContainer.data('lazy-loaded', true);
            $.ajax({
                url: jj_admin_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_load_adapters_ui',
                    security: jj_admin_params.nonce,
                    section: 'spokes'
                },
                success: function(response) {
                    if (response.success && response.data.html) {
                        $spokesContainer.html(response.data.html);
                        // 동적으로 로드된 스포크 UI 필드만 부분 초기화
                        initializeAllFields($spokesContainer);
                    }
                }
            });
        }
        
        // 테마 UI 로드
        if ($themesContainer.length && $themesContainer.data('lazy-loaded') !== true) {
            $themesContainer.data('lazy-loaded', true);
            $.ajax({
                url: jj_admin_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_load_adapters_ui',
                    security: jj_admin_params.nonce,
                    section: 'themes'
                },
                success: function(response) {
                    if (response.success && response.data.html) {
                        $themesContainer.html(response.data.html);
                        // 동적으로 로드된 테마 UI 필드만 부분 초기화
                        initializeAllFields($themesContainer);
                    }
                }
            });
        }
    }


    // --- [ 2. 실시간 프리뷰 함수 (v3.3 '제련') ] ---
    
    // 2a. 버튼 미리보기 (v3.3 '확장' 완료)
    function updateButtonPreview() {
        var root = document.documentElement;
        var shadow_x, shadow_y, shadow_blur, shadow_spread, shadow_color; 

        // --- 1. 'Primary' 버튼 (v1.8.0 '유지') ---
        root.style.setProperty('--jj-btn-primary-bg', getNestedObject(currentSettings, ['buttons', 'primary', 'background_color']) || '#0073e6');
        root.style.setProperty('--jj-btn-primary-text', getNestedObject(currentSettings, ['buttons', 'primary', 'text_color']) || '#ffffff');
        root.style.setProperty('--jj-btn-primary-border', getNestedObject(currentSettings, ['buttons', 'primary', 'border_color']) || '#0073e6');
        root.style.setProperty('--jj-btn-primary-bg-hover', getNestedObject(currentSettings, ['buttons', 'primary', 'background_color_hover']) || '#0051a3');
        root.style.setProperty('--jj-btn-primary-text-hover', getNestedObject(currentSettings, ['buttons', 'primary', 'text_color_hover']) || '#ffffff');
        root.style.setProperty('--jj-btn-primary-border-hover', getNestedObject(currentSettings, ['buttons', 'primary', 'border_color_hover']) || '#0051a3');
        root.style.setProperty('--jj-btn-primary-border-radius', (getNestedObject(currentSettings, ['buttons', 'primary', 'border_radius']) || '4') + 'px');
        root.style.setProperty('--jj-btn-primary-padding-top', (getNestedObject(currentSettings, ['buttons', 'primary', 'padding', 'top']) || '12') + 'px');
        root.style.setProperty('--jj-btn-primary-padding-right', (getNestedObject(currentSettings, ['buttons', 'primary', 'padding', 'right']) || '24') + 'px');
        root.style.setProperty('--jj-btn-primary-padding-bottom', (getNestedObject(currentSettings, ['buttons', 'primary', 'padding', 'bottom']) || '12') + 'px');
        root.style.setProperty('--jj-btn-primary-padding-left', (getNestedObject(currentSettings, ['buttons', 'primary', 'padding', 'left']) || '24') + 'px');
        shadow_x = getNestedObject(currentSettings, ['buttons', 'primary', 'shadow', 'x']) || '0';
        shadow_y = getNestedObject(currentSettings, ['buttons', 'primary', 'shadow', 'y']) || '10';
        shadow_blur = getNestedObject(currentSettings, ['buttons', 'primary', 'shadow', 'blur']) || '15';
        shadow_spread = getNestedObject(currentSettings, ['buttons', 'primary', 'shadow', 'spread']) || '-5';
        shadow_color = getNestedObject(currentSettings, ['buttons', 'primary', 'shadow', 'color']) || 'rgba(0,0,0,0.1)';
        root.style.setProperty('--jj-btn-primary-shadow', `${shadow_x}px ${shadow_y}px ${shadow_blur}px ${shadow_spread}px ${shadow_color}`);

        // --- 2. 'Secondary' 버튼 [v3.3 '신설'] ---
        root.style.setProperty('--jj-btn-secondary-bg', getNestedObject(currentSettings, ['buttons', 'secondary', 'background_color']) || '#FFFFFF');
        root.style.setProperty('--jj-btn-secondary-text', getNestedObject(currentSettings, ['buttons', 'secondary', 'text_color']) || '#333333');
        root.style.setProperty('--jj-btn-secondary-border', getNestedObject(currentSettings, ['buttons', 'secondary', 'border_color']) || '#CCCCCC');
        root.style.setProperty('--jj-btn-secondary-bg-hover', getNestedObject(currentSettings, ['buttons', 'secondary', 'background_color_hover']) || '#F0F0F0');
        root.style.setProperty('--jj-btn-secondary-text-hover', getNestedObject(currentSettings, ['buttons', 'secondary', 'text_color_hover']) || '#333333');
        root.style.setProperty('--jj-btn-secondary-border-hover', getNestedObject(currentSettings, ['buttons', 'secondary', 'border_color_hover']) || '#AAAAAA');
        root.style.setProperty('--jj-btn-secondary-border-radius', (getNestedObject(currentSettings, ['buttons', 'secondary', 'border_radius']) || '4') + 'px');
        root.style.setProperty('--jj-btn-secondary-padding-top', (getNestedObject(currentSettings, ['buttons', 'secondary', 'padding', 'top']) || '10') + 'px');
        root.style.setProperty('--jj-btn-secondary-padding-right', (getNestedObject(currentSettings, ['buttons', 'secondary', 'padding', 'right']) || '20') + 'px');
        root.style.setProperty('--jj-btn-secondary-padding-bottom', (getNestedObject(currentSettings, ['buttons', 'secondary', 'padding', 'bottom']) || '10') + 'px');
        root.style.setProperty('--jj-btn-secondary-padding-left', (getNestedObject(currentSettings, ['buttons', 'secondary', 'padding', 'left']) || '20') + 'px');
        shadow_x = getNestedObject(currentSettings, ['buttons', 'secondary', 'shadow', 'x']) || '0';
        shadow_y = getNestedObject(currentSettings, ['buttons', 'secondary', 'shadow', 'y']) || '0';
        shadow_blur = getNestedObject(currentSettings, ['buttons', 'secondary', 'shadow', 'blur']) || '0';
        shadow_spread = getNestedObject(currentSettings, ['buttons', 'secondary', 'shadow', 'spread']) || '0';
        shadow_color = getNestedObject(currentSettings, ['buttons', 'secondary', 'shadow', 'color']) || 'rgba(0,0,0,0)';
        root.style.setProperty('--jj-btn-secondary-shadow', `${shadow_x}px ${shadow_y}px ${shadow_blur}px ${shadow_spread}px ${shadow_color}`);

        // --- 3. 'Text' 버튼 [v3.3 '신설'] ---
        var text_btn_color = getNestedObject(currentSettings, ['buttons', 'text', 'text_color']) || (getNestedObject(currentSettings, ['palettes', 'brand', 'primary_color']) || '#0073e6');
        var text_btn_color_hover = getNestedObject(currentSettings, ['buttons', 'text', 'text_color_hover']) || (getNestedObject(currentSettings, ['palettes', 'brand', 'primary_color_hover']) || '#0051a3');
        
        root.style.setProperty('--jj-btn-text-bg', getNestedObject(currentSettings, ['buttons', 'text', 'background_color']) || 'transparent');
        root.style.setProperty('--jj-btn-text-text', text_btn_color);
        root.style.setProperty('--jj-btn-text-border', getNestedObject(currentSettings, ['buttons', 'text', 'border_color']) || 'transparent');
        root.style.setProperty('--jj-btn-text-bg-hover', getNestedObject(currentSettings, ['buttons', 'text', 'background_color_hover']) || 'transparent');
        root.style.setProperty('--jj-btn-text-text-hover', text_btn_color_hover);
        root.style.setProperty('--jj-btn-text-border-hover', getNestedObject(currentSettings, ['buttons', 'text', 'border_color_hover']) || 'transparent');
        root.style.setProperty('--jj-btn-text-border-radius', (getNestedObject(currentSettings, ['buttons', 'text', 'border_radius']) || '4') + 'px');
        root.style.setProperty('--jj-btn-text-padding-top', (getNestedObject(currentSettings, ['buttons', 'text', 'padding', 'top']) || '8') + 'px');
        root.style.setProperty('--jj-btn-text-padding-right', (getNestedObject(currentSettings, ['buttons', 'text', 'padding', 'right']) || '16') + 'px');
        root.style.setProperty('--jj-btn-text-padding-bottom', (getNestedObject(currentSettings, ['buttons', 'text', 'padding', 'bottom']) || '8') + 'px');
        root.style.setProperty('--jj-btn-text-padding-left', (getNestedObject(currentSettings, ['buttons', 'text', 'padding', 'left']) || '16') + 'px');
        shadow_x = getNestedObject(currentSettings, ['buttons', 'text', 'shadow', 'x']) || '0';
        shadow_y = getNestedObject(currentSettings, ['buttons', 'text', 'shadow', 'y']) || '0';
        shadow_blur = getNestedObject(currentSettings, ['buttons', 'text', 'shadow', 'blur']) || '0';
        shadow_spread = getNestedObject(currentSettings, ['buttons', 'text', 'shadow', 'spread']) || '0';
        shadow_color = getNestedObject(currentSettings, ['buttons', 'text', 'shadow', 'color']) || 'rgba(0,0,0,0)';
        root.style.setProperty('--jj-btn-text-shadow', `${shadow_x}px ${shadow_y}px ${shadow_blur}px ${shadow_spread}px ${shadow_color}`);
    }

    // 2b. 타이포그래피 미리보기
    function updateTypographyPreview(tag) {
        var $row = $('.jj-typography-row[data-type="' + tag + '"]');
        var $previewTag = $row.find('.tag');
        var settings = (currentSettings.typography && currentSettings.typography[tag]) ? currentSettings.typography[tag] : {};
        var activeDevice = $row.find('.jj-responsive-tab-button.is-active').data('device') || 'desktop';
        var sizeValue = (settings.font_size && settings.font_size[activeDevice]) 
                        ? settings.font_size[activeDevice] 
                        : (settings.font_size && settings.font_size.desktop) 
                        ? settings.font_size.desktop 
                        : '20'; 

        $previewTag.css({
            'font-family': settings.font_family || 'inherit',
            'font-weight': settings.font_weight || '400',
            'font-style': settings.font_style || 'normal',
            'line-height': (settings.line_height || '1.5') + 'em',
            'letter-spacing': (settings.letter_spacing || '0') + 'px',
            'text-transform': settings.text_transform || 'none',
            'font-size': sizeValue + 'px'
        });
    }

    // 2c. [v3.3 '신설'] 폼 & 필드 미리보기
    function updateFormPreview() {
        var $styleTag = $('#jj-form-preview-style');
        if (!$styleTag.length) return; // '폼' 섹션이 '로드'되지 '않았'으면 '종료'

        var label = currentSettings.forms ? (currentSettings.forms.label || {}) : {};
        var field = currentSettings.forms ? (currentSettings.forms.field || {}) : {};

        // 'Fallback' 값 '정의'
        var label_weight = label.font_weight || 'var(--jj-font-p-weight, 400)';
        var label_style = label.font_style || 'var(--jj-font-p-style, normal)';
        var label_transform = label.text_transform || 'var(--jj-font-p-text-transform, none)';
        var label_size = label.font_size ? (label.font_size + 'px') : 'var(--jj-font-p-size, 16px)';
        var label_color = label.text_color || 'var(--jj-sys-text, inherit)';

        var field_bg = field.background_color || '#fff';
        var field_text = field.text_color || '#333';
        var field_border = field.border_color || '#ccc';
        var field_border_focus = field.border_color_focus || 'var(--jj-primary-color, #0073e6)';
        var field_radius = (field.border_radius || '4') + 'px';
        var field_border_width = (field.border_width || '1') + 'px';
        var field_pad_top = (field.padding && field.padding.top) ? (field.padding.top + 'px') : '10px';
        var field_pad_right = (field.padding && field.padding.right) ? (field.padding.right + 'px') : '12px';
        var field_pad_bottom = (field.padding && field.padding.bottom) ? (field.padding.bottom + 'px') : '10px';
        var field_pad_left = (field.padding && field.padding.left) ? (field.padding.left + 'px') : '12px';

        // '미리보기' CSS '생성'
        var css_rules = `
            .jj-form-preview-label {
                font-weight: ${label_weight};
                font-style: ${label_style};
                text-transform: ${label_transform};
                font-size: ${label_size};
                color: ${label_color};
            }
            .jj-form-preview-input {
                background-color: ${field_bg};
                color: ${field_text};
                border-color: ${field_border};
                border-radius: ${field_radius};
                border-width: ${field_border_width};
                padding-top: ${field_pad_top};
                padding-right: ${field_pad_right};
                padding-bottom: ${field_pad_bottom};
                padding-left: ${field_pad_left};
            }
            .jj-form-preview-input:focus,
            .jj-form-preview-wrap:focus-within .jj-form-preview-input {
                border-color: ${field_border_focus};
            }
        `;
        $styleTag.html(css_rules);
    }


    // --- [ 3. UI 이벤트 핸들러 (v3.3 '제련') ] ---
    
    // 3a. 'v1.8.0 탭' 클릭 (모바일 터치 이벤트 지원 + 레이지 로딩)
    $('#jj-style-guide-wrapper').on('click touchend', '.jj-tab-button', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $button = $(this);
        var $nav = $button.closest('.jj-tabs-nav');
        var $container = $button.closest('.jj-tabs-container');
        var tabId = $button.data('tab');

        if (!tabId) {
            return;
        }

        $nav.find('.jj-tab-button').removeClass('is-active');
        $button.addClass('is-active');

        $container.find('.jj-tab-content').removeClass('is-active');
        var $targetContent = $container.find('.jj-tab-content[data-tab-content="' + tabId + '"]');
        if ($targetContent.length) {
            $targetContent.addClass('is-active');
        }
        
        // [v3.7.0] Labs → 실험실 센터로 분리됨 (별도 페이지)
        // Labs 탭 클릭 핸들러 제거 (더 이상 스타일 센터에 Labs 탭이 없음)
        
        // 동적으로 로드된 컨텐츠의 필드 초기화
        setTimeout(function() {
            initColorPickers($targetContent);
            enhanceColorTools($targetContent);
        }, 100);
    });
    
    // [v3.5.0-dev10 '레이지 로딩'] Labs 섹션 동적 로드
    function loadLabsSection() {
        var $labsContainer = $('#jj-lazy-load-labs');
        if ($labsContainer.length && $labsContainer.data('lazy-loaded') !== true) {
            $labsContainer.data('lazy-loaded', true);
            $.ajax({
                url: jj_admin_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_load_labs_section',
                    security: jj_admin_params.nonce
                },
                success: function(response) {
                    if (response.success && response.data.html) {
                        $labsContainer.html(response.data.html);
                        // Labs 영역만 부분 초기화
                        initializeAllFields($labsContainer);
                    }
                },
                error: function() {
                    $labsContainer.html('<p class="description" style="color: #d63638;">' + 
                        jj_admin_params.i18n.error + ': ' + jj_admin_params.i18n.labs_load_failed + '</p>');
                }
            });
        }
    }

    // 3b. 컬러 스포이드 버튼 클릭 (EyeDropper API 사용)
    $('#jj-style-guide-form').on('click', '.jj-color-eyedropper', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var targetSelector = $btn.attr('data-target');
        var $input = targetSelector ? $(targetSelector) : $btn.closest('.jj-control-group').find('.jj-color-field').first();
        if ( !$input.length ) return;

        if (typeof window.EyeDropper === 'undefined') {
            alert(jj_admin_params.i18n && jj_admin_params.i18n.eyedropper_not_supported ? jj_admin_params.i18n.eyedropper_not_supported : '브라우저에서 스포이드 기능을 지원하지 않습니다.');
            return;
        }

        var eyedropper = new window.EyeDropper();
        eyedropper.open().then(function(result) {
            var color = result.sRGBHex;
            if (!color) return;
            // wpColorPicker와 currentSettings 둘 다 갱신
            $input.val(color).trigger('change');
            if ($input.data('wpWpColorPicker')) {
                $input.wpColorPicker('color', color);
            }
        }).catch(function() {
            // 사용자가 취소한 경우 등은 조용히 무시
        });
    });

    // 3c. 현재 색상 불러오기 버튼 클릭 (프리뷰 박스 또는 입력 값 기준)
    $('#jj-style-guide-form').on('click', '.jj-color-use-current', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var targetSelector = $btn.attr('data-target');
        var $input = targetSelector ? $(targetSelector) : $btn.closest('.jj-control-group').find('.jj-color-field').first();
        if ( !$input.length ) return;

        var $preview = $input.closest('.jj-color-card, .jj-control-group').find('.jj-color-preview').first();
        var currentColor = '';

        if ($preview.length) {
            currentColor = $preview.css('background-color') || '';
        }

        if (!currentColor) {
            currentColor = $input.val() || '';
        }

        if (!currentColor) {
            return;
        }

        // rgb(...) 형식을 hex 로 단순 변환 시도
        if (currentColor.indexOf('rgb') === 0) {
            currentColor = rgbToHex(currentColor);
        }

        if (!currentColor) return;

        $input.val(currentColor).trigger('change');
        if ($input.data('wpWpColorPicker')) {
            $input.wpColorPicker('color', currentColor);
        }
    });

    // 3d. 임시 팔레트 "중간 저장" 버튼 클릭 (현재 브랜드 팔레트를 임시 팔레트로 복사)
    $('#jj-style-guide-form').on('click', '.jj-save-to-temp-button', function(e) {
        e.preventDefault();

        var $button = $(this);
        var $spinner = $button.siblings('.spinner');
        var $successMsg = $('#jj-save-success');

        setSpinnerState($spinner, $button, true);
        $successMsg.hide();

        var data = {
            action: 'jj_save_temp_palette',
            security: jj_admin_params.temp_palette_nonce
        };

        $.post(jj_admin_params.ajax_url, data, function(response) {
            setSpinnerState($spinner, $button, false);

            if (response.success) {
                $successMsg.text(response.data.message || jj_admin_params.i18n.saved).fadeIn();
                setTimeout(function() { $successMsg.fadeOut(); }, 4000);

                // 임시 팔레트 미리보기 박스 갱신
                if (response.data.temp_palettes && response.data.temp_palettes.brand) {
                    var brand = response.data.temp_palettes.brand;
                    $('.jj-temp-palette-preview').each(function() {
                        var $preview = $(this).find('.jj-color-preview-box');
                        var $code = $(this).find('code');
                        var label = $(this).find('label').text();
                        var key = null;
                        if (label.indexOf('Primary Color') !== -1) {
                            key = 'primary_color';
                        } else if (label.indexOf('Primary (Hover)') !== -1) {
                            key = 'primary_color_hover';
                        } else if (label.indexOf('Secondary Color') !== -1) {
                            key = 'secondary_color';
                        } else if (label.indexOf('Secondary (Hover)') !== -1) {
                            key = 'secondary_color_hover';
                        }
                        if (key && brand[key]) {
                            $preview.css('background-color', brand[key]);
                            $code.text(brand[key]);
                        }
                    });
                }
            } else {
                showAjaxError(response, 'generic');
            }
        }).fail(function() {
            setSpinnerState($spinner, $button, false);
            showAjaxError(null, 'network');
        });
    });

    // 3e. Labs 탭 - 공식 지원 목록에서 "더 보기 / 전체 보기" 버튼
    $('#jj-style-guide-form').on('click', '.jj-labs-show-more', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var target = $btn.data('target'); // 'themes' 또는 'spokes'
        var $wrapper = $btn.closest('.jj-labs-supported-item');
        var $list = $wrapper.find('.jj-labs-supported-list');
        var initialCount = parseInt($list.data('initial-count'), 10) || 6;

        var $hiddenItems = $list.find('.jj-labs-supported-list-item.is-hidden');
        if (!$hiddenItems.length) {
            return;
        }

        // 한 번 클릭할 때마다 initialCount 개수만큼 추가로 노출
        $hiddenItems.slice(0, initialCount).removeClass('is-hidden');

        // 클릭 횟수 추적 (3번까지)
        var clickCount = parseInt($btn.data('click-count') || 0, 10) + 1;
        $btn.data('click-count', clickCount);

        var $showAll = $wrapper.find('.jj-labs-show-all');

        if ($list.find('.jj-labs-supported-list-item.is-hidden').length === 0) {
            // 더 이상 숨겨진 항목이 없으면 "더 보기" 비활성화
            $btn.prop('disabled', true);
        }

        if (clickCount >= 2) {
            // 두 번째 이후부터는 "전체 보기" 버튼도 함께 노출
            $showAll.show();
        }

        if (clickCount >= 3) {
            // 세 번째 클릭 이후에는 "더 보기" 버튼 비활성화
            $btn.prop('disabled', true);
        }
    });

    $('#jj-style-guide-form').on('click', '.jj-labs-show-all', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $wrapper = $btn.closest('.jj-labs-supported-item');
        var $list = $wrapper.find('.jj-labs-supported-list');

        $list.find('.jj-labs-supported-list-item.is-hidden').removeClass('is-hidden');

        // 더 보기/전체 보기 모두 비활성화
        $wrapper.find('.jj-labs-show-more').prop('disabled', true);
        $btn.prop('disabled', true);
    });

    // 3f. 커스텀 웹 폰트 업로드 (미디어 라이브러리)
    $('#jj-style-guide-form').on('click', '.jj-font-upload-button', function(e) {
        e.preventDefault();

        var $button   = $(this);
        var $roleWrap = $button.closest('.jj-custom-font-role');
        var $hidden   = $roleWrap.find('.jj-font-attachment-id');
        var $label    = $roleWrap.find('.jj-font-file-label');

        var frame = wp.media({
            title: '폰트 파일 선택',
            button: { text: '이 폰트 사용' },
            library: { type: ['font/woff2', 'font/woff', 'font/ttf', 'font/otf', 'application/font-woff2', 'application/font-woff', 'application/x-font-ttf', 'application/x-font-otf'] },
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            if (!attachment || !attachment.id) {
                return;
            }

            $hidden.val(attachment.id).trigger('change');
            $label.val(attachment.title || attachment.filename || '').trigger('change');
        });

        frame.open();
    });

    // 3g. 브랜드 팔레트 → 버튼/폼/링크 일괄 적용 버튼
    // [v3.8.0 신규] 새로고침 버튼: Customizer에서 현재 색상 불러오기
    $('#jj-style-guide-form').on('click', '.jj-refresh-colors', function(e) {
        e.preventDefault();
        var $button = $(this);
        var $control = $button.closest('.jj-section-refresh-control');
        var $spinner = $control.find('.spinner');
        var paletteType = $button.data('palette-type'); // 'brand' 또는 'system'
        
        if (!paletteType) {
            console.error('팔레트 타입이 지정되지 않았습니다.');
            return;
        }
        
        setSpinnerState($spinner, $button, true);
        
        var data = {
            action: 'jj_load_current_colors',
            nonce: jj_admin_params.nonce,
            palette_type: paletteType,
            force_update: true // 새로고침은 강제 업데이트
        };
        
        $.post(jj_admin_params.ajax_url, data, function(response) {
            setSpinnerState($spinner, $button, false);
            
            if (response.success && response.data && response.data.colors) {
                var colors = response.data.colors;
                var updated = 0;
                
                // 받은 색상 값을 해당 필드에 적용
                $.each(colors, function(key, value) {
                    if (!value) return;
                    
                    var settingKey = 'palettes[' + paletteType + '][' + key + ']';
                    var $field = $('#jj-style-guide-form').find('[data-setting-key="' + settingKey + '"]');
                    
                    if ($field.length) {
                        $field.val(value).trigger('change');
                        
                        // wpColorPicker가 활성화되어 있으면 색상도 업데이트
                        if ($field.closest('.wp-picker-container').length) {
                            $field.wpColorPicker('color', value);
                        }
                        
                        // 색상 미리보기 업데이트
                        var $preview = $field.closest('.jj-control-group').find('.jj-color-preview');
                        if ($preview.length) {
                            $preview.css('background-color', value);
                        }
                        
                        updated++;
                    }
                });
                
                if (updated > 0) {
                    // 성공 메시지 표시
                    var $msg = $('<div class="notice notice-success is-dismissible" style="margin-top: 10px;"><p>' + 
                        (response.data.message || '색상이 성공적으로 불러와졌습니다.') + ' (' + updated + '개 색상 업데이트됨)</p></div>');
                    $control.after($msg);
                    
                    // 3초 후 메시지 제거
                    setTimeout(function() {
                        $msg.fadeOut(300, function() {
                            $(this).remove();
                        });
                    }, 3000);
                    
                    // 페이지 새로고침 없이 옵션 저장 (선택 사항)
                    // $('#jj-style-guide-form').submit();
                } else {
                    // 색상은 성공적으로 불러왔지만 필드에 적용할 수 없는 경우
                    var infoMsg = '현재 테마의 Customizer에서 불러올 색상이 없습니다.\n\n';
                    infoMsg += '다음 방법을 시도해보세요:\n';
                    infoMsg += '• 테마 Customizer에서 색상을 먼저 설정하세요\n';
                    infoMsg += '• 또는 아래 필드에 직접 색상을 입력하세요\n';
                    infoMsg += '• 추천 팔레트에서 원하는 프리셋을 선택할 수도 있습니다';
                    alert(infoMsg);
                }
            } else {
                var errorMsg = response.data && response.data.message ? response.data.message : '색상을 불러오는데 실패했습니다.';
                alert(errorMsg);
            }
        }).fail(function() {
            setSpinnerState($spinner, $button, false);
            alert('네트워크 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
        });
    });
    
    // [v3.8.0 신규] 섹션별 내보내기 버튼
    $('#jj-style-guide-form').on('click', '.jj-export-section', function(e) {
        e.preventDefault();
        var $button = $(this);
        var sectionType = $button.data('section-type');
        var sectionSubtype = $button.data('section-subtype');

        if (!sectionType || !sectionSubtype) {
            alert('섹션 정보가 없습니다.');
            return;
        }

        var data = {
            action: 'jj_export_section',
            security: jj_admin_params.nonce,
            section_type: sectionType,
            section_subtype: sectionSubtype
        };

        // 폼 생성 및 제출 (파일 다운로드)
        var $form = $('<form>', {
            method: 'POST',
            action: jj_admin_params.ajax_url,
            target: '_blank'
        });

        $.each(data, function(key, value) {
            $form.append($('<input>', {
                type: 'hidden',
                name: key,
                value: value
            }));
        });

        $('body').append($form);
        $form.submit();
        $form.remove();
    });

    // [v3.8.0 신규] 섹션별 불러오기 버튼
    // 섹션 불러오기 (동적 요소 지원)
    $(document).on('click', '#jj-style-guide-form .jj-import-section, .jj-import-settings', function(e) {
        e.preventDefault();
        var $button = $(this);
        var sectionType = $button.data('section-type');
        var sectionSubtype = $button.data('section-subtype');
        var $control = $button.closest('.jj-section-export-import-control');
        var $fileInput = $control.find('.jj-section-import-file[data-section-type="' + sectionType + '"][data-section-subtype="' + sectionSubtype + '"]');
        var $spinner = $control.find('.spinner');

        if (!$fileInput.length) {
            alert('파일 입력 요소를 찾을 수 없습니다.');
            return;
        }

        // 파일 선택 다이얼로그 열기
        $fileInput.click();
    });

    // [v3.8.0 신규] 섹션별 불러오기 파일 선택 이벤트
    $('#jj-style-guide-form').on('change', '.jj-section-import-file', function(e) {
        var $fileInput = $(this);
        var file = $fileInput[0].files[0];

        if (!file) {
            return;
        }

        var sectionType = $fileInput.data('section-type');
        var sectionSubtype = $fileInput.data('section-subtype');
        var $control = $fileInput.closest('.jj-section-export-import-control');
        var $spinner = $control.find('.spinner');
        var $button = $control.find('.jj-import-section[data-section-type="' + sectionType + '"][data-section-subtype="' + sectionSubtype + '"]');

        if (!sectionType || !sectionSubtype) {
            alert('섹션 정보가 없습니다.');
            return;
        }

        // 파일 확장자 확인
        if (!file.name.toLowerCase().endsWith('.json')) {
            alert('JSON 파일만 업로드할 수 있습니다.');
            $fileInput.val('');
            return;
        }

        // FormData 생성
        var formData = new FormData();
        formData.append('action', 'jj_import_section');
        formData.append('security', jj_admin_params.nonce);
        formData.append('section_type', sectionType);
        formData.append('section_subtype', sectionSubtype);
        formData.append('import_file', file);

        setSpinnerState($spinner, $button, true);

        $.ajax({
            url: jj_admin_params.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                setSpinnerState($spinner, $button, false);
                $fileInput.val(''); // 파일 입력 초기화

                if (response.success) {
                    // 성공 메시지 표시
                    var $msg = $('<div class="notice notice-success is-dismissible" style="margin-top: 10px;"><p>' + 
                        (response.data.message || '설정이 성공적으로 불러와졌습니다.') + '</p></div>');
                    $control.after($msg);

                    // 3초 후 메시지 제거
                    setTimeout(function() {
                        $msg.fadeOut(300, function() {
                            $(this).remove();
                        });
                    }, 3000);

                    // 페이지 새로고침하여 변경된 설정 반영
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    var errorMsg = response.data && response.data.message ? response.data.message : '설정을 불러오는데 실패했습니다.';
                    alert(errorMsg);
                }
            },
            error: function() {
                setSpinnerState($spinner, $button, false);
                $fileInput.val('');
                alert('네트워크 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
            }
        });
    });

    $('#jj-style-guide-form').on('click', '.jj-apply-brand-palette-to-components', function(e) {
        e.preventDefault();

        var $button = $(this);
        var $spinner = $button.siblings('.spinner');
        var $successMsg = $('#jj-save-success');

        setSpinnerState($spinner, $button, true);
        $successMsg.hide();

        var primary   = getNestedObject(currentSettings, ['palettes', 'brand', 'primary_color']) || '#0073e6';
        var primaryH  = getNestedObject(currentSettings, ['palettes', 'brand', 'primary_color_hover']) || '#0051a3';
        var secondary = getNestedObject(currentSettings, ['palettes', 'brand', 'secondary_color']) || '#444444';

        // 버튼 기본값 동기화
        setNestedObject(currentSettings, ['buttons', 'primary', 'background_color'], primary);
        setNestedObject(currentSettings, ['buttons', 'primary', 'background_color_hover'], primaryH);
        setNestedObject(currentSettings, ['buttons', 'primary', 'border_color'], primary);
        setNestedObject(currentSettings, ['buttons', 'primary', 'border_color_hover'], primaryH);

        // 텍스트 버튼 색상
        setNestedObject(currentSettings, ['buttons', 'text', 'text_color'], primary);
        setNestedObject(currentSettings, ['buttons', 'text', 'text_color_hover'], primaryH);

        // 시스템 링크 색상
        setNestedObject(currentSettings, ['palettes', 'system', 'link_color'], primary);

        // 폼 포커스 보더 색상
        setNestedObject(currentSettings, ['forms', 'field', 'border_color_focus'], primary);

        // UI 필드 값도 즉시 동기화
        $('#jj-style-guide-form')
            .find('[data-setting-key="buttons[primary][background_color]"]').val(primary).trigger('change').end()
            .find('[data-setting-key="buttons[primary][background_color_hover]"]').val(primaryH).trigger('change').end()
            .find('[data-setting-key="buttons[primary][border_color]"]').val(primary).trigger('change').end()
            .find('[data-setting-key="buttons[primary][border_color_hover]"]').val(primaryH).trigger('change').end()
            .find('[data-setting-key="buttons[text][text_color]"]').val(primary).trigger('change').end()
            .find('[data-setting-key="buttons[text][text_color_hover]"]').val(primaryH).trigger('change').end()
            .find('[data-setting-key="palettes[system][link_color]"]').val(primary).trigger('change').end()
            .find('[data-setting-key="forms[field][border_color_focus]"]').val(primary).trigger('change');

        updateButtonPreview();
        updateFormPreview();

        setSpinnerState($spinner, $button, false);
        $successMsg.text(jj_admin_params.i18n && jj_admin_params.i18n.applied ? jj_admin_params.i18n.applied : '브랜드 팔레트가 적용되었습니다.').fadeIn();
        setTimeout(function() { $successMsg.fadeOut(); }, 4000);
    });

    // 3h. 일반 필드(input, select) 변경
    // [v5.0.4] 성능 최적화: 디바운싱 적용
    var dataFieldInputTimer = null;
    $('#jj-style-guide-form').on('input', '.jj-data-field', function() {
        var $input = $(this);
        if ($input.hasClass('jj-color-field') || $input.hasClass('jj-color-picker') || $input.hasClass('jj-toggle-switch')) return; 

        // [v5.0.4] 디바운싱: 300ms 지연
        clearTimeout(dataFieldInputTimer);
        dataFieldInputTimer = setTimeout(function() {
            var keyString = $input.data('setting-key');
            if (!keyString) return; 

            var keyParts = keyString.split(/\[|\]/).filter(Boolean);
            setNestedObject(currentSettings, keyParts, $input.val());

            // [v3.3 '제련'] '폼' 미리보기 '갱신' '추가'
            if (keyString.startsWith('buttons')) {
                updateButtonPreview();
            } else if (keyString.startsWith('typography')) {
                var tag = keyParts[1]; 
                updateTypographyPreview(tag);
            } else if (keyString.startsWith('forms')) {
                updateFormPreview();
            }
            
            // [v5.0.3] 실시간 프리뷰 CSS 업데이트 (새로고침 없이)
            if (typeof window.JJLivePreview !== 'undefined' && window.JJLivePreview.isPreviewOpen()) {
                window.JJLivePreview.handleSettingChange();
            } else {
                // 폴백: 기존 새로고침 방식
                refreshPreviewIfOpen();
            }
        }, 300);
    });
    
    // change 이벤트는 즉시 처리 (선택 완료 시)
    $('#jj-style-guide-form').on('change', '.jj-data-field', function() {
        var $input = $(this);
        if ($input.hasClass('jj-color-field') || $input.hasClass('jj-color-picker') || $input.hasClass('jj-toggle-switch')) return; 

        var keyString = $input.data('setting-key');
        if (!keyString) return; 

        var keyParts = keyString.split(/\[|\]/).filter(Boolean);
        setNestedObject(currentSettings, keyParts, $input.val());

        // [v3.3 '제련'] '폼' 미리보기 '갱신' '추가'
        if (keyString.startsWith('buttons')) {
            updateButtonPreview();
        } else if (keyString.startsWith('typography')) {
            var tag = keyParts[1]; 
            updateTypographyPreview(tag);
        } else if (keyString.startsWith('forms')) {
            updateFormPreview();
        }
        
        // [v5.0.3] 실시간 프리뷰 CSS 업데이트 (새로고침 없이)
        if (typeof window.JJLivePreview !== 'undefined' && window.JJLivePreview.isPreviewOpen()) {
            window.JJLivePreview.handleSettingChange();
        } else {
            // 폴백: 기존 새로고침 방식
            refreshPreviewIfOpen();
        }
    });

    // 3f. 반응형 탭 클릭 (모바일 터치 이벤트 지원)
    $('#jj-style-guide-wrapper').on('click touchend', '.jj-responsive-tab-button', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $button = $(this);
        var $group = $button.closest('.jj-responsive-control');
        var $tabs = $group.find('.jj-responsive-tabs');
        var $contents = $group.find('.jj-responsive-tab-content');
        var device = $button.data('device');
        
        if (!device) {
            return;
        }
        
        $tabs.find('.jj-responsive-tab-button').removeClass('is-active');
        $button.addClass('is-active');
        $contents.removeClass('is-active');
        var $targetContent = $group.find('.jj-responsive-tab-content[data-device="' + device + '"]');
        if ($targetContent.length) {
            $targetContent.addClass('is-active');
        }

        var $typographyRow = $button.closest('.jj-typography-row');
        if ($typographyRow.length) {
            var tag = $typographyRow.data('type');
            if (tag) {
                updateTypographyPreview(tag);
            }
        }
    });
    
    // 3g. '스타일 저장' 버튼 클릭 (동적 요소 지원)
    $(document).on('click', '#jj-save-style-guide, #jj-style-guide-footer-button, .jj-save-button', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $button = $(this);
        
        // 이미 처리 중이면 중복 실행 방지
        if ($button.hasClass('processing') || $button.prop('disabled')) {
            return false;
        }
        
        var $spinner = $button.siblings('.spinner');
        if (!$spinner.length) {
            $spinner = $button.closest('.jj-header-actions, .jj-style-guide-footer').find('.spinner').first();
        }
        var $successMsg = $('#jj-save-success');
        
        // 버튼 비활성화 및 처리 중 표시
        $button.addClass('processing').prop('disabled', true);
        setSpinnerState($spinner, $button, true);
        $successMsg.hide();

        var data = {
            action: 'jj_save_style_guide',
            security: jj_admin_params.nonce,
            settings: currentSettings 
        };

        $.post(jj_admin_params.ajax_url, data, function(response) {
            setSpinnerState($spinner, $button, false);
            $button.removeClass('processing').prop('disabled', false);
            
            if (response.success) {
                var successText = (jj_admin_params.i18n && jj_admin_params.i18n.saved) ? jj_admin_params.i18n.saved : '설정이 저장되었습니다.';
                $successMsg.text(successText).fadeIn();
                setTimeout(function() { $successMsg.fadeOut(); }, 3000);
            } else {
                showAjaxError(response, 'generic');
            }
        }).fail(function(xhr, status, error) {
            setSpinnerState($spinner, $button, false);
            $button.removeClass('processing').prop('disabled', false);
            console.error('Save error:', xhr, status, error);
            showAjaxError(null, 'network');
        });
    });

    // 3h. '컨텍스트' 토글 스위치 UI 이벤트
    function toggleContextControls($checkbox) {
        var $target = $($checkbox.data('toggle-target'));
        var keyParts = $checkbox.data('setting-key').split(/\[|\]/).filter(Boolean);
        
        if ($checkbox.is(':checked')) {
            $target.slideDown(200);
            setNestedObject(currentSettings, keyParts, '1');
            
            if ( !$target.data('color-pickers-initialized') ) {
                initColorPickers( $target );
                $target.data('color-pickers-initialized', true);
            }
        } else {
            $target.slideUp(200);
            setNestedObject(currentSettings, keyParts, '0');
        }
    }
    
    // 클릭 이벤트 핸들러
    $('#jj-style-guide-form').on('click', '.jj-toggle-switch', function() {
        toggleContextControls($(this));
    });

    // [v1.9.0-beta5 '수리']
    // 3i. '임시 팔레트 적용' AJAX
    $('#jj-style-guide-form').on('click', '.jj-apply-palette-button', function(e) {
        e.preventDefault();

        if ( !confirm( jj_admin_params.i18n.confirm_apply ) ) {
            return;
        }

        var $button = $(this);
        var $spinner = $button.siblings('.spinner');
        var $successMsg = $('#jj-save-success'); 
        
        setSpinnerState($spinner, $button, true);
        $successMsg.hide();

        var data = {
            action: 'jj_apply_temp_palette',
            security: jj_admin_params.temp_palette_nonce,
            target_palette: $button.data('apply-target') // 'brand'
        };

        $.post(jj_admin_params.ajax_url, data, function(response) {
            setSpinnerState($spinner, $button, false);
            
            if (response.success) {
                currentSettings = response.data.new_settings;
                jj_admin_params.settings = response.data.new_settings;
                initializeAllFields();
                $('.jj-tabs-nav .jj-tab-button[data-tab="brand"]').trigger('click');
                $successMsg.text(jj_admin_params.i18n.applied).fadeIn();
                setTimeout(function() { $successMsg.fadeOut(); }, 5000);
            } else {
                showAjaxError(response, 'generic');
            }
        }).fail(function() {
            setSpinnerState($spinner, $button, false);
            showAjaxError(null, 'network');
        });
    });
    
    
    // --- [ 4. [v3.5.0-dev6 '신규'] 'Labs' '스캐너' 'AJAX' ] ---
    
    $('#jj-style-guide-form').on('click', '#jj-labs-start-scan', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $spinner = $button.siblings('.spinner'); // (UI '뼈대'에 '스피너' '추가' '필요')
        var $url_input = $('#jj-labs-scan-url');
        var $results_textarea = $('#jj-labs-scan-results textarea');
        
        var scan_url = $url_input.val();
        if ( !scan_url ) {
            alert('분석할 URL을 입력해주세요.'); // (향후 'i18n' '적용')
            $url_input.focus();
            return;
        }

        setSpinnerState($spinner, $button, true);
        $results_textarea.val('분석 중입니다. 잠시만 기다려주세요...\nURL: ' + scan_url);

        var data = {
            action: 'jj_scan_page_for_css',
            security: jj_admin_params.nonce, // '기존' 'nonce' '공유'
            scan_url: scan_url 
        };

        $.post(jj_admin_params.ajax_url, data, function(response) {
            setSpinnerState($spinner, $button, false);
            
            if (response.success) {
                // '성공' '시' '결과' '표시' (v3.5.0-dev6 '임시' '응답')
                $results_textarea.val('AJAX 연결 성공!\n백엔드에서 받은 응답:\n\n' + JSON.stringify(response.data, null, 2));
            } else {
                var msg = getAjaxErrorMessage(response, 'scanner');
                $results_textarea.val(msg);
            }
        }).fail(function() {
            setSpinnerState($spinner, $button, false);
            $results_textarea.val('치명적인 AJAX 오류가 발생했습니다. 서버 로그를 확인해주세요.');
        });
    });
    
    // --- [ 5. 헬퍼 함수 (v1.7.4 계승) ] ---
    
    function getNestedObject(obj, keys) {
        return keys.reduce(function(acc, key) {
            return (acc && acc[key] !== undefined) ? acc[key] : undefined;
        }, obj);
    }
    
    function setNestedObject(obj, keys, value) {
        var current = obj;
        for (var i = 0; i < keys.length - 1; i++) {
            var key = keys[i];
            if (!current[key] || typeof current[key] !== 'object') {
                current[key] = {};
            }
            current = current[key];
        }
        current[keys[keys.length - 1]] = value;
    }

    // 공통 스피너/버튼 상태 제어 헬퍼
    function setSpinnerState($spinner, $button, isActive) {
        if (!$spinner || !$spinner.length) return;
        if (isActive) {
            $spinner.addClass('is-active');
            if ($button && $button.length) {
                $button.prop('disabled', true);
            }
        } else {
            $spinner.removeClass('is-active');
            if ($button && $button.length) {
                $button.prop('disabled', false);
            }
        }
    }

    // AJAX 공통 에러 메시지 생성 헬퍼
    function getAjaxErrorMessage(response, context) {
        var i18n = jj_admin_params.i18n || {};
        var baseError = i18n.error || '오류가 발생했습니다.';

        if (!response || typeof response !== 'object') {
            if (context === 'network') {
                return baseError + '\n네트워크 통신 중 문제가 발생했습니다. 잠시 후 다시 시도해주세요.';
            }
            return baseError;
        }

        var message = (response.data && response.data.message) ? response.data.message : '';

        if (!message) {
            if (context === 'scanner') {
                return baseError + '\n스캔 요청 처리 중 문제가 발생했습니다.';
            }
            return baseError;
        }

        return baseError + ': ' + message;
    }

    // AJAX 공통 에러 alert 래퍼
    function showAjaxError(response, context) {
        var msg;
        if (context === 'network') {
            msg = getAjaxErrorMessage(null, 'network');
        } else {
            msg = getAjaxErrorMessage(response, context);
        }
        alert(msg);
    }

    // rgb() → #rrggbb 단순 변환 유틸
    function rgbToHex(rgbString) {
        var result = /^rgba?\((\d+),\s*(\d+),\s*(\d+)/i.exec(rgbString);
        if (!result) return '';
        var r = parseInt(result[1], 10);
        var g = parseInt(result[2], 10);
        var b = parseInt(result[3], 10);
        return '#' + [r, g, b].map(function(x) {
            var hex = x.toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        }).join('');
    }

    // [v3.7.0] 드래그 앤 드롭 섹션 재정렬 기능
    var $sectionsContainer = $('#jj-sections-sortable');
    if ($sectionsContainer.length && $.fn.sortable) {
        // 각 섹션에 드래그 핸들 추가 (.jj-section-wrapper 안의 .jj-section-global)
        $sectionsContainer.find('.jj-section-wrapper').each(function() {
            var $wrapper = $(this);
            var $section = $wrapper.find('.jj-section-global, .jj-section-spoke').first();
            if ($section.length && !$section.find('.jj-section-drag-handle').length) {
                var $handle = $('<span class="jj-section-drag-handle" title="드래그하여 순서 변경"><span class="dashicons dashicons-menu"></span></span>');
                var $title = $section.find('.jj-section-title');
                if ($title.length) {
                    $title.prepend($handle);
                }
            }
        });

        // 이미 sortable이 초기화되어 있으면 destroy 후 다시 초기화
        if ($sectionsContainer.hasClass('ui-sortable')) {
            try {
                $sectionsContainer.sortable('destroy');
            } catch (e) {
                console.warn('Sections sortable destroy failed:', e);
            }
        }

        // Sortable 초기화 (items는 .jj-section-wrapper를 대상으로)
        $sectionsContainer.sortable({
            items: '.jj-section-wrapper',
            handle: '.jj-section-drag-handle',
            placeholder: 'jj-section-placeholder',
            tolerance: 'pointer',
            cursor: 'move',
            opacity: 0.8,
            axis: 'y',
            containment: 'parent',
            revert: 100,
            distance: 5,
            cancel: '.jj-section-title, input, button, .jj-control-group',
            start: function(e, ui) {
                ui.placeholder.css({
                    'height': ui.item.outerHeight(),
                    'background-color': '#f0f0f1',
                    'border': '2px dashed #c3c4c7',
                    'border-radius': '4px',
                    'margin': '10px 0'
                });
            },
            stop: function(e, ui) {
                // 섹션 순서 저장
                var sectionOrder = [];
                $sectionsContainer.find('.jj-section-wrapper').each(function() {
                    var slug = $(this).data('section-slug');
                    if (slug) {
                        sectionOrder.push(slug);
                    }
                });

                // AJAX로 순서 저장
                $.ajax({
                    url: jj_admin_params.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'jj_save_section_order',
                        security: jj_admin_params.nonce,
                        section_order: sectionOrder
                    },
                    success: function(response) {
                        if (response.success) {
                            // 성공 메시지 표시 (선택적)
                            var $success = $('#jj-save-success');
                            if ($success.length) {
                                $success.text('섹션 순서가 저장되었습니다.').fadeIn();
                                setTimeout(function() {
                                    $success.fadeOut();
                                }, 2000);
                            }
                            
                            // 섹션 번호 업데이트
                            $sectionsContainer.find('.jj-section-wrapper').each(function(index) {
                                var $wrapper = $(this);
                                var $sectionTitle = $wrapper.find('.jj-section-global .jj-section-title .jj-section-index, .jj-section-spoke .jj-section-title .jj-section-index');
                                if ($sectionTitle.length) {
                                    $sectionTitle.text((index + 1) + '.');
                                }
                            });
                        }
                    },
                    error: function() {
                        alert('섹션 순서 저장 중 오류가 발생했습니다.');
                    }
                });
            }
        });
    }

    // [v3.7.0] 색상 피커 개선: 모든 색상 필드에 wpColorPicker가 제대로 적용되도록 보장
    // initColorPickers 함수가 이미 존재하지만, 추가 개선사항 적용
    function enhanceColorPicker() {
        $('#jj-style-guide-form').find('.jj-color-field, .jj-color-picker').each(function() {
            var $input = $(this);
            
            // wpColorPicker가 없으면 초기화
            if (!$input.data('wpWpColorPicker')) {
                var settingKey = $input.data('setting-key');
                if (!settingKey) {
                    return;
                }
                
                var value = getNestedObject(currentSettings, settingKey.split(/\[|\]/).filter(Boolean));
                
                $input.wpColorPicker({
                    change: function(event, ui) {
                        var newColor = ui.color.toString();
                        var keyParts = $(this).data('setting-key').split(/\[|\]/).filter(Boolean);
                        setNestedObject(currentSettings, keyParts, newColor);
                        
                        // 미리보기 업데이트
                        var $preview = $input.closest('.jj-color-card, .jj-control-group').find('.jj-color-preview');
                        if ($preview.length) {
                            $preview.css('background-color', newColor);
                        }
                        
                        // 버튼/폼 미리보기 업데이트
                        if (settingKey.startsWith('buttons')) {
                            updateButtonPreview();
                        } else if (settingKey.startsWith('forms')) {
                            updateFormPreview();
                        }
                    }
                });
                
                // 초기값 설정
                if (value) {
                    $input.wpColorPicker('color', value);
                }
            }
        });
    }

    // 페이지 로드 시 및 동적 컨텐츠 추가 시 색상 피커 개선 적용
    enhanceColorPicker();
    
    // 동적으로 추가되는 컨텐츠에도 적용 (Labs 섹션 등)
    $(document).on('jjSectionLoaded', function() {
        enhanceColorPicker();
    });

    // [v3.7.0 '신규'] 키보드 단축키 지원
    $(document).on('keydown', function(e) {
        // 텍스트 입력 중이거나 select, textarea가 포커스된 경우 단축키 무시
        var $target = $(e.target);
        if ($target.is('input[type="text"], input[type="number"], textarea, select, input[type="search"]')) {
            // Ctrl/Cmd + S는 저장이므로 입력 필드에서도 허용
            if ((e.ctrlKey || e.metaKey) && e.key === 's' && !e.shiftKey) {
                e.preventDefault();
                // 저장 버튼 클릭
                var $saveBtn = $('#jj-save-style-guide, #jj-style-guide-footer-button');
                if ($saveBtn.length && !$saveBtn.prop('disabled')) {
                    $saveBtn.trigger('click');
                }
            }
            // 나머지 단축키는 입력 필드에서는 무시
            if (!((e.ctrlKey || e.metaKey) && e.key === 's' && !e.shiftKey)) {
                return;
            }
        }

        // Ctrl/Cmd + S: 저장
        if ((e.ctrlKey || e.metaKey) && e.key === 's' && !e.shiftKey) {
            e.preventDefault();
            var $saveBtn = $('#jj-save-style-guide, #jj-style-guide-footer-button');
            if ($saveBtn.length && !$saveBtn.prop('disabled')) {
                $saveBtn.trigger('click');
            }
            return;
        }

        // Ctrl/Cmd + Shift + R: 기본값으로 되돌리기 (페이지 새로고침 대신 사용자 확인 후 폼 리셋)
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'R') {
            e.preventDefault();
            if (confirm('모든 설정을 기본값으로 되돌리시겠습니까? (페이지가 새로고침됩니다)')) {
                // 폼 초기화를 위해 페이지 새로고침
                window.location.reload();
            }
            return;
        }

        // Ctrl/Cmd + Z: 실행 취소 (향후 구현)
        // 히스토리 관리 시스템이 필요하므로 현재는 주석 처리
        /*
        if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
            e.preventDefault();
            // TODO: 실행 취소 기능 구현 (설정 변경 히스토리 관리 필요)
            console.log('Undo 기능은 향후 구현 예정입니다.');
        }
        */
    });

    // [v3.7.0 '신규'] 실시간 미리보기 기능
    var previewWindow = null;
    var previewRefreshTimer = null;
    var currentPreviewViewport = 'desktop'; // 현재 프리뷰 뷰포트: desktop, tablet, mobile
    
    // 뷰포트 크기 정의
    var viewportSizes = {
        'desktop': { width: 1200, height: 800, name: '데스크톱' },
        'tablet': { width: 768, height: 1024, name: '태블릿' },
        'mobile': { width: 375, height: 667, name: '모바일' }
    };
    
    // 프리뷰 창 열기/관리
    function openPreviewWindow() {
        var previewUrl = jj_admin_params.preview_url;
        if (!previewUrl) {
            alert('프리뷰 페이지가 설정되지 않았습니다. 스타일 가이드 설정 페이지가 생성되었는지 확인하세요.');
            return;
        }
        
        // 기존 창이 있으면 포커스, 없으면 새로 열기
        if (previewWindow && !previewWindow.closed) {
            previewWindow.focus();
            // 기존 창에 뷰포트 전환 버튼 추가 (한 번만)
            if (!previewWindow.jjPreviewControlsAdded) {
                addPreviewViewportControls(previewWindow);
            }
        } else {
            var viewport = viewportSizes[currentPreviewViewport];
            var width = Math.min(viewport.width + 100, window.screen.width * 0.95); // 컨트롤 공간 포함
            var height = Math.min(viewport.height + 100, window.screen.height * 0.95);
            var left = (window.screen.width - width) / 2;
            var top = (window.screen.height - height) / 2;
            
            previewWindow = window.open(
                previewUrl,
                'jj-style-preview',
                'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',toolbar=no,menubar=no,scrollbars=yes,resizable=yes'
            );
            
            if (previewWindow) {
                $('#jj-preview-button').addClass('active').html('<span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> 프리뷰 닫기');
                
                // 프리뷰 창이 로드되면 뷰포트 전환 컨트롤 추가
                previewWindow.addEventListener('load', function() {
                    addPreviewViewportControls(previewWindow);
                    adjustPreviewViewport(currentPreviewViewport);
                });
                
                // 뷰포트 전환 버튼 추가 (스타일 센터 측)
                if (!$('#jj-preview-viewport-controls').length) {
                    var $viewportControls = $('<div id="jj-preview-viewport-controls" style="display: inline-flex; gap: 5px; margin-left: 10px; align-items: center;"></div>');
                    var $desktopBtn = $('<button type="button" class="button button-small jj-preview-viewport-btn" data-viewport="desktop" title="데스크톱 뷰"><span class="dashicons dashicons-desktop"></span></button>');
                    var $tabletBtn = $('<button type="button" class="button button-small jj-preview-viewport-btn" data-viewport="tablet" title="태블릿 뷰"><span class="dashicons dashicons-tablet"></span></button>');
                    var $mobileBtn = $('<button type="button" class="button button-small jj-preview-viewport-btn" data-viewport="mobile" title="모바일 뷰"><span class="dashicons dashicons-smartphone"></span></button>');
                    
                    $viewportControls.append($desktopBtn, $tabletBtn, $mobileBtn);
                    $('#jj-preview-button').after($viewportControls);
                    
                    // 뷰포트 전환 버튼 클릭 이벤트
                    $viewportControls.on('click', '.jj-preview-viewport-btn', function() {
                        var viewport = $(this).data('viewport');
                        changePreviewViewport(viewport);
                    });
                    
                    // 초기 활성 버튼 설정
                    $viewportControls.find('[data-viewport="' + currentPreviewViewport + '"]').addClass('active');
                }
            }
        }
    }
    
    // 프리뷰 창에 뷰포트 전환 컨트롤 추가 (프리뷰 창 내부)
    function addPreviewViewportControls(win) {
        try {
            if (!win || !win.document || win.jjPreviewControlsAdded) {
                return;
            }
            
            var controlsHtml = '<div id="jj-preview-viewport-overlay" style="position: fixed; top: 10px; right: 10px; z-index: 999999; background: rgba(0,0,0,0.8); padding: 8px; border-radius: 4px; display: flex; gap: 5px;">' +
                '<button type="button" class="jj-preview-viewport-btn" data-viewport="desktop" style="background: #2271b1; color: #fff; border: none; padding: 6px 10px; border-radius: 3px; cursor: pointer;" title="데스크톱"><span class="dashicons dashicons-desktop"></span></button>' +
                '<button type="button" class="jj-preview-viewport-btn" data-viewport="tablet" style="background: #2271b1; color: #fff; border: none; padding: 6px 10px; border-radius: 3px; cursor: pointer;" title="태블릿"><span class="dashicons dashicons-tablet"></span></button>' +
                '<button type="button" class="jj-preview-viewport-btn" data-viewport="mobile" style="background: #2271b1; color: #fff; border: none; padding: 6px 10px; border-radius: 3px; cursor: pointer;" title="모바일"><span class="dashicons dashicons-smartphone"></span></button>' +
                '</div>';
            
            var tempDiv = win.document.createElement('div');
            tempDiv.innerHTML = controlsHtml;
            var $controls = $(tempDiv.firstElementChild);
            $(win.document.body).append($controls);
            
            // 뷰포트 전환 버튼 클릭 이벤트 (프리뷰 창 내부)
            $controls.find('.jj-preview-viewport-btn').on('click', function() {
                var viewport = $(this).data('viewport');
                // 부모 창에 메시지 전송
                if (win.opener) {
                    win.opener.postMessage({ type: 'jj-preview-viewport-change', viewport: viewport }, '*');
                }
            });
            
            win.jjPreviewControlsAdded = true;
        } catch (e) {
            console.warn('프리뷰 컨트롤 추가 실패 (크로스 오리진):', e);
        }
    }
    
    // 뷰포트 변경 함수
    function changePreviewViewport(viewport) {
        if (!viewportSizes[viewport]) {
            return;
        }
        
        currentPreviewViewport = viewport;
        
        // 버튼 상태 업데이트 (스타일 센터 측)
        $('#jj-preview-viewport-controls .jj-preview-viewport-btn').removeClass('active');
        $('#jj-preview-viewport-controls [data-viewport="' + viewport + '"]').addClass('active');
        
        // 프리뷰 창이 열려있으면 크기 조절 및 뷰포트 메타 태그 변경
        if (previewWindow && !previewWindow.closed) {
            adjustPreviewViewport(viewport);
        }
    }
    
    // 프리뷰 창 뷰포트 조절
    function adjustPreviewViewport(viewport) {
        if (!previewWindow || previewWindow.closed) {
            return;
        }
        
        try {
            var viewportSize = viewportSizes[viewport];
            var width = viewportSize.width + 100; // 컨트롤 공간 포함
            var height = viewportSize.height + 100;
            
            // 창 크기 조절
            previewWindow.resizeTo(width, height);
            
            // 뷰포트 메타 태그 업데이트 (프리뷰 창 내부)
            var doc = previewWindow.document;
            if (doc) {
                var $viewportMeta = $(doc).find('meta[name="viewport"]');
                if ($viewportMeta.length) {
                    $viewportMeta.attr('content', 'width=' + viewportSize.width + ', initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
                } else {
                    var meta = doc.createElement('meta');
                    meta.name = 'viewport';
                    meta.content = 'width=' + viewportSize.width + ', initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
                    doc.getElementsByTagName('head')[0].appendChild(meta);
                }
                
                // 컨테이너 크기 조절 스타일 추가
                var $style = $(doc).find('#jj-preview-viewport-style');
                if (!$style.length) {
                    $style = $('<style id="jj-preview-viewport-style"></style>');
                    $(doc.head).append($style);
                }
                $style.html('body { max-width: ' + viewportSize.width + 'px; margin: 0 auto; box-shadow: 0 0 20px rgba(0,0,0,0.3); }');
                
                // 프리뷰 창 내부 버튼 상태 업데이트
                $(doc).find('.jj-preview-viewport-btn').removeClass('active');
                $(doc).find('[data-viewport="' + viewport + '"]').addClass('active').css('background', '#135e96');
            }
        } catch (e) {
            console.warn('프리뷰 뷰포트 조절 실패 (크로스 오리진):', e);
        }
    }
    
    // postMessage 리스너 (프리뷰 창에서 뷰포트 변경 요청 받기)
    window.addEventListener('message', function(e) {
        if (e.data && e.data.type === 'jj-preview-viewport-change' && e.data.viewport) {
            changePreviewViewport(e.data.viewport);
        }
    });
    
    // 프리뷰 창 닫기
    function closePreviewWindow() {
        if (previewWindow && !previewWindow.closed) {
            previewWindow.close();
            previewWindow = null;
        }
        $('#jj-preview-button').removeClass('active').html('<span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> 실시간 미리보기');
        // 뷰포트 컨트롤 숨기기 (선택적)
        $('#jj-preview-viewport-controls').fadeOut();
    }
    
    // 프리뷰 페이지 새로고침 (디바운싱 적용)
    function refreshPreviewIfOpen() {
        if (!previewWindow || previewWindow.closed) {
            return;
        }
        
        // 디바운싱: 500ms 이내 여러 변경이 있으면 마지막에 한 번만 새로고침
        clearTimeout(previewRefreshTimer);
        previewRefreshTimer = setTimeout(function() {
            try {
                if (previewWindow && !previewWindow.closed) {
                    previewWindow.location.reload();
                }
            } catch (e) {
                // 크로스 오리진 오류 등은 무시
                console.warn('프리뷰 새로고침 실패:', e);
            }
        }, 500);
    }
    
    // 프리뷰 버튼 클릭 이벤트
    $('#jj-preview-button').on('click', function() {
        if ($(this).hasClass('active')) {
            closePreviewWindow();
        } else {
            openPreviewWindow();
            // 뷰포트 컨트롤 표시
            $('#jj-preview-viewport-controls').fadeIn();
        }
    });
    
    // 페이지 언로드 시 프리뷰 창 닫기 (선택적)
    $(window).on('beforeunload', function() {
        if (previewWindow && !previewWindow.closed) {
            previewWindow.close();
        }
    });

    // 단축키 안내 툴팁 추가 (선택적)
    if (jj_admin_params.show_shortcuts_hint !== false) {
        $(document).ready(function() {
            var $shortcutHint = $('<div class="jj-shortcuts-hint" style="position:fixed; bottom:20px; right:20px; background:#fff; border:1px solid #c3c4c7; padding:10px 15px; border-radius:4px; box-shadow:0 2px 8px rgba(0,0,0,0.1); z-index:10000; font-size:12px; display:none;"><strong>키보드 단축키:</strong><br>Ctrl/Cmd + S: 저장<br>Ctrl/Cmd + Shift + R: 기본값으로 되돌리기<span class="dashicons dashicons-no-alt" style="float:right; cursor:pointer; margin-left:10px; color:#666;"></span></div>');
            $('body').append($shortcutHint);

            // 처음 로드 시 3초간 표시 후 자동 숨김
            setTimeout(function() {
                $shortcutHint.fadeIn(300);
            }, 1000);
            
            setTimeout(function() {
                $shortcutHint.fadeOut(300);
            }, 4000);

            // 닫기 버튼 클릭
            $shortcutHint.find('.dashicons-no-alt').on('click', function() {
                $shortcutHint.fadeOut(300);
            });

            // 단축키로 다시 표시 (Ctrl/Cmd + ?)
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === '?') {
                    e.preventDefault();
                    $shortcutHint.fadeToggle(300);
                }
            });
        });
    }

    // [v3.7.0 '신규'] 스타일 센터 설정 내보내기/불러오기
    $(document).on('click', '.jj-export-settings[data-center="style-center"]', function() {
        const $button = $(this);
        const $spinner = $('<span class="spinner is-active" style="float: none; margin: 0 5px;"></span>');
        
        $button.prop('disabled', true).after($spinner);
        
        // AJAX 요청으로 다운로드 트리거
        const form = $('<form>').attr({
            method: 'POST',
            action: jj_admin_params.ajax_url || ajaxurl,
            style: 'display: none;'
        });
        
        form.append($('<input>').attr({ type: 'hidden', name: 'action', value: 'jj_export_center_settings' }));
        form.append($('<input>').attr({ type: 'hidden', name: 'security', value: jj_admin_params.nonce || '' }));
        form.append($('<input>').attr({ type: 'hidden', name: 'center', value: 'style-center' }));
        
        $('body').append(form);
        form.submit();
        
        // 폼 제거 및 버튼 복구 (다운로드가 시작되면 페이지가 다시 로드되지 않을 수 있음)
        setTimeout(function() {
            form.remove();
            $spinner.remove();
            $button.prop('disabled', false);
        }, 1000);
    });

    $(document).on('click', '.jj-import-settings[data-center="style-center"]', function() {
        const $button = $(this);
        const $fileInput = $('<input>').attr({
            type: 'file',
            accept: '.json',
            style: 'display: none;'
        });

        $fileInput.on('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                return;
            }

            if (!file.name.toLowerCase().endsWith('.json')) {
                alert('JSON 파일만 업로드할 수 있습니다.');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'jj_import_center_settings');
            formData.append('security', jj_admin_params.nonce || '');
            formData.append('center', 'style-center');
            formData.append('import_file', file);

            const $spinner = $('<span class="spinner is-active" style="float: none; margin: 0 5px;"></span>');
            $button.prop('disabled', true).after($spinner);

            $.ajax({
                url: jj_admin_params.ajax_url || ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $spinner.remove();
                    $button.prop('disabled', false);
                    
                    if (response.success) {
                        alert(response.data.message || '설정이 성공적으로 불러와졌습니다.');
                        location.reload();
                    } else {
                        alert(response.data.message || '설정 불러오기에 실패했습니다.');
                    }
                },
                error: function() {
                    $spinner.remove();
                    $button.prop('disabled', false);
                    alert('네트워크 오류가 발생했습니다.');
                }
            });

            $fileInput.remove();
        });

        $('body').append($fileInput);
        $fileInput.trigger('click');
    });

    // 다크 모드/라이트 모드 색상 계산 함수
    function calculateDarkModeColor(hex) {
        if (!hex || hex.length !== 7 || hex[0] !== '#') return '';
        var r = parseInt(hex.substr(1, 2), 16);
        var g = parseInt(hex.substr(3, 2), 16);
        var b = parseInt(hex.substr(5, 2), 16);
        r = Math.max(0, Math.min(255, Math.round(r * 0.8)));
        g = Math.max(0, Math.min(255, Math.round(g * 0.8)));
        b = Math.max(0, Math.min(255, Math.round(b * 0.8)));
        return '#' + [r, g, b].map(function(x) {
            return ('0' + x.toString(16)).slice(-2);
        }).join('');
    }
    
    function calculateLightModeColor(hex) {
        if (!hex || hex.length !== 7 || hex[0] !== '#') return '';
        var r = parseInt(hex.substr(1, 2), 16);
        var g = parseInt(hex.substr(3, 2), 16);
        var b = parseInt(hex.substr(5, 2), 16);
        r = Math.max(0, Math.min(255, Math.round(r + (255 - r) * 0.2)));
        g = Math.max(0, Math.min(255, Math.round(g + (255 - g) * 0.2)));
        b = Math.max(0, Math.min(255, Math.round(b + (255 - b) * 0.2)));
        return '#' + [r, g, b].map(function(x) {
            return ('0' + x.toString(16)).slice(-2);
        }).join('');
    }

    // 다크 모드 색상 적용
    $('#jj-style-guide-form').on('click', '.jj-apply-dark-mode', function(e) {
        e.preventDefault();
        var $button = $(this);
        var sourceColor = $button.data('source');
        var targetSelector = $button.data('target');
        var $target = $(targetSelector);
        
        if (!sourceColor || !$target.length) return;
        
        var darkColor = calculateDarkModeColor(sourceColor);
        if (darkColor) {
            $target.val(darkColor);
            if ($target.hasClass('jj-color-field')) {
                // wpColorPicker가 있으면 업데이트
                if ($target.closest('.wp-picker-container').length) {
                    $target.wpColorPicker('color', darkColor);
                } else {
                    $target.trigger('change');
                }
            }
            $target.closest('.jj-color-card').find('.jj-color-preview').css('background-color', darkColor);
        }
    });

    // 라이트 모드 색상 적용
    $('#jj-style-guide-form').on('click', '.jj-apply-light-mode', function(e) {
        e.preventDefault();
        var $button = $(this);
        var sourceColor = $button.data('source');
        var targetSelector = $button.data('target');
        var $target = $(targetSelector);
        
        if (!sourceColor || !$target.length) return;
        
        var lightColor = calculateLightModeColor(sourceColor);
        if (lightColor) {
            $target.val(lightColor);
            if ($target.hasClass('jj-color-field')) {
                // wpColorPicker가 있으면 업데이트
                if ($target.closest('.wp-picker-container').length) {
                    $target.wpColorPicker('color', lightColor);
                } else {
                    $target.trigger('change');
                }
            }
            $target.closest('.jj-color-card').find('.jj-color-preview').css('background-color', lightColor);
        }
    });

    // [v22.1.2 '신규'] 디자인 프리셋 적용 (Jenny x Jason)
    $('#jj-style-guide-wrapper').on('click', '.jj-import-preset-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $card = $btn.closest('.jj-preset-card');
        var presetId = $card.data('preset-id');
        var presetName = $card.find('h3').text();

        if (!confirm('"' + presetName + '" 프리셋을 적용하시겠습니까?\n기존의 일부 스타일 설정이 덮어씌워질 수 있습니다.')) {
            return;
        }

        $btn.prop('disabled', true).text('적용 중...');
        $card.css('opacity', '0.7');

        $.ajax({
            url: jj_admin_params.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_import_style_preset',
                security: jj_admin_params.nonce,
                preset_id: presetId
            },
            success: function(response) {
                if (response.success) {
                    // 성공 효과: 화면 반짝임 후 새로고침
                    $btn.text('적용 완료!').css('background', '#10b981');
                    $('body').fadeOut(500, function() {
                        location.reload();
                    });
                } else {
                    alert(response.data.message || '프리셋 적용에 실패했습니다.');
                    $btn.prop('disabled', false).text('프리셋 적용하기');
                    $card.css('opacity', '1');
                }
            },
            error: function() {
                alert('네트워크 오류가 발생했습니다.');
                $btn.prop('disabled', false).text('프리셋 적용하기');
                $card.css('opacity', '1');
            }
        });
    });

});