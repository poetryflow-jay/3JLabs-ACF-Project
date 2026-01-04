/**
 * [v22.4.7] 스타일 센터 GUI 개선 - 실시간 미리보기, 퀵 네비게이션, 스크롤 최적화
 * Phase 37.3: GUI 품질 개선
 */

(function($) {
    'use strict';

    /**
     * 탭 내부 퀵 네비게이션 생성
     */
    function initQuickNavigation() {
        $('.jj-tab-content.is-active').each(function() {
            var $tabContent = $(this);
            var $quickNav = $tabContent.find('.jj-tab-quick-nav');
            
            // 이미 생성되어 있으면 스킵
            if ($quickNav.length > 0) return;
            
            // 섹션 찾기
            var $sections = $tabContent.find('.jj-section-subsection, .jj-fieldset-group, .jj-control-group:has(legend)');
            if ($sections.length < 2) return; // 섹션이 2개 미만이면 퀵 네비게이션 불필요
            
            // 퀵 네비게이션 HTML 생성
            var $nav = $('<div class="jj-tab-quick-nav"><ul class="jj-tab-quick-nav-list"></ul></div>');
            var $navList = $nav.find('ul');
            
            $sections.each(function(index) {
                var $section = $(this);
                var title = '';
                
                // 제목 찾기
                var $title = $section.find('h3, h4, legend').first();
                if ($title.length) {
                    title = $title.text().trim();
                } else {
                    // 레이블이나 설명에서 제목 찾기
                    var $label = $section.find('label').first();
                    if ($label.length) {
                        title = $label.text().trim();
                    } else {
                        title = '섹션 ' + (index + 1);
                    }
                }
                
                if (!title) return;
                
                // 앵커 ID 생성
                var anchorId = 'jj-section-' + $tabContent.data('tab-content') + '-' + index;
                $section.attr('id', anchorId).addClass('jj-section-anchor');
                
                // 네비게이션 링크 추가
                var $link = $('<li class="jj-tab-quick-nav-item"><a href="#' + anchorId + '" class="jj-tab-quick-nav-link">' + title + '</a></li>');
                $navList.append($link);
            });
            
            // 탭 콘텐츠 시작 부분에 삽입
            if ($navList.children().length > 0) {
                $tabContent.prepend($nav);
            }
        });
    }

    /**
     * 스크롤 인디케이터 생성
     */
    function initScrollIndicator() {
        if ($('.jj-scroll-indicator').length > 0) return;
        
        var $indicator = $('<div class="jj-scroll-indicator"><div class="jj-scroll-indicator-bar"></div></div>');
        $('body').append($indicator);
        
        var $bar = $indicator.find('.jj-scroll-indicator-bar');
        
        function updateScrollIndicator() {
            var windowHeight = $(window).height();
            var documentHeight = $(document).height();
            var scrollTop = $(window).scrollTop();
            var scrollPercent = (scrollTop / (documentHeight - windowHeight)) * 100;
            $bar.css('width', scrollPercent + '%');
        }
        
        $(window).on('scroll', updateScrollIndicator);
        updateScrollIndicator();
    }

    /**
     * 실시간 미리보기 업데이트
     */
    function updateLivePreview() {
        // 팔레트 미리보기 업데이트
        updatePalettePreview();
        
        // 버튼 미리보기 업데이트
        updateButtonPreview();
        
        // 타이포그래피 미리보기 업데이트
        updateTypographyPreview();
    }

    /**
     * 팔레트 미리보기 업데이트
     */
    function updatePalettePreview() {
        var $preview = $('#jj-palette-inline-preview');
        if ($preview.length === 0) return;
        
        var primaryColor = $('[data-setting-key="palettes[brand][primary_color]"]').val() || '#667eea';
        var secondaryColor = $('[data-setting-key="palettes[brand][secondary_color]"]').val() || '#94a3b8';
        var bgColor = $('[data-setting-key="palettes[system][site_bg]"]').val() || '#ffffff';
        var textColor = $('[data-setting-key="palettes[system][text_color]"]').val() || '#1e293b';
        
        // CSS 변수 설정
        $preview.find('.jj-prev-surface').css({
            'background-color': bgColor,
            'color': textColor
        });
        
        $preview.find('.jj-prev-btn.primary').css({
            'background-color': primaryColor,
            '--jj-preview-primary-bg': primaryColor
        });
        
        $preview.find('.jj-prev-btn.secondary').css({
            'background-color': secondaryColor,
            '--jj-preview-secondary-bg': secondaryColor
        });
        
        $preview.find('[data-jj-color="primary"]').text(primaryColor);
        $preview.find('[data-jj-color="secondary"]').text(secondaryColor);
        $preview.find('[data-jj-color="bg"]').text(bgColor);
        $preview.find('[data-jj-color="text"]').text(textColor);
    }

    /**
     * 버튼 미리보기 업데이트
     */
    function updateButtonPreview() {
        $('.jj-button-preview').each(function() {
            var $preview = $(this);
            var $btn = $preview.find('.jj-button-preview-btn');
            var buttonType = '';
            
            if ($btn.hasClass('jj-preview-primary')) {
                buttonType = 'primary';
            } else if ($btn.hasClass('jj-preview-secondary')) {
                buttonType = 'secondary';
            } else if ($btn.hasClass('jj-preview-text')) {
                buttonType = 'text';
            }
            
            if (!buttonType) return;
            
            var bgColor = $('[data-setting-key="buttons[' + buttonType + '][background_color]"]').val() || '';
            var textColor = $('[data-setting-key="buttons[' + buttonType + '][text_color]"]').val() || '';
            var borderColor = $('[data-setting-key="buttons[' + buttonType + '][border_color]"]').val() || '';
            var borderRadius = $('[data-setting-key="buttons[' + buttonType + '][border_radius]"]').val() || '4';
            var paddingTop = $('[data-setting-key="buttons[' + buttonType + '][padding][top]"]').val() || '12';
            var paddingRight = $('[data-setting-key="buttons[' + buttonType + '][padding][right]"]').val() || '24';
            var paddingBottom = $('[data-setting-key="buttons[' + buttonType + '][padding][bottom]"]').val() || '12';
            var paddingLeft = $('[data-setting-key="buttons[' + buttonType + '][padding][left]"]').val() || '24';
            var shadowColor = $('[data-setting-key="buttons[' + buttonType + '][shadow][color]"]').val() || '';
            var shadowX = $('[data-setting-key="buttons[' + buttonType + '][shadow][x]"]').val() || '0';
            var shadowY = $('[data-setting-key="buttons[' + buttonType + '][shadow][y]"]').val() || '2';
            var shadowBlur = $('[data-setting-key="buttons[' + buttonType + '][shadow][blur]"]').val() || '5';
            var shadowSpread = $('[data-setting-key="buttons[' + buttonType + '][shadow][spread]"]').val() || '0';
            
            var styles = {};
            
            if (buttonType === 'text') {
                styles['background-color'] = 'transparent';
                styles['color'] = textColor || borderColor || '#667eea';
                if (borderColor) {
                    styles['border'] = '2px solid ' + borderColor;
                }
            } else {
                if (bgColor) styles['background-color'] = bgColor;
                if (textColor) styles['color'] = textColor;
                if (borderColor) styles['border-color'] = borderColor;
            }
            
            if (borderRadius) styles['border-radius'] = borderRadius + 'px';
            if (paddingTop) styles['padding-top'] = paddingTop + 'px';
            if (paddingRight) styles['padding-right'] = paddingRight + 'px';
            if (paddingBottom) styles['padding-bottom'] = paddingBottom + 'px';
            if (paddingLeft) styles['padding-left'] = paddingLeft + 'px';
            
            if (shadowColor) {
                var shadow = shadowX + 'px ' + shadowY + 'px ' + shadowBlur + 'px ' + shadowSpread + 'px ' + shadowColor;
                styles['box-shadow'] = shadow;
            }
            
            $btn.css(styles);
        });
    }

    /**
     * 타이포그래피 미리보기 업데이트
     */
    function updateTypographyPreview() {
        $('.jj-typography-preview').each(function() {
            var $preview = $(this);
            var tag = $preview.data('tag') || 'h1';
            
            var fontFamily = $('[data-setting-key="typography[' + tag + '][font_family]"]').val() || '';
            var fontWeight = $('[data-setting-key="typography[' + tag + '][font_weight]"]').val() || '400';
            var fontStyle = $('[data-setting-key="typography[' + tag + '][font_style]"]').val() || 'normal';
            var lineHeight = $('[data-setting-key="typography[' + tag + '][line_height]"]').val() || '';
            var letterSpacing = $('[data-setting-key="typography[' + tag + '][letter_spacing]"]').val() || '';
            var fontSize = $('[data-setting-key="typography[' + tag + '][font_size][desktop]"]').val() || '';
            
            var $element = $preview.find(tag).first();
            if ($element.length === 0) return;
            
            var styles = {};
            if (fontFamily) styles['font-family'] = fontFamily;
            if (fontWeight) styles['font-weight'] = fontWeight;
            if (fontStyle) styles['font-style'] = fontStyle;
            if (lineHeight) styles['line-height'] = lineHeight;
            if (letterSpacing) styles['letter-spacing'] = letterSpacing + 'px';
            if (fontSize) styles['font-size'] = fontSize + 'px';
            
            $element.css(styles);
        });
    }

    /**
     * 퀵 네비게이션 스크롤 처리
     */
    function initQuickNavScroll() {
        $(document).on('click', '.jj-tab-quick-nav-link', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            if (target) {
                var $target = $(target);
                if ($target.length) {
                    $('html, body').animate({
                        scrollTop: $target.offset().top - 100
                    }, 500);
                    
                    // 활성 상태 업데이트
                    $('.jj-tab-quick-nav-link').removeClass('active');
                    $(this).addClass('active');
                }
            }
        });
        
        // 스크롤 시 활성 링크 업데이트
        $(window).on('scroll', function() {
            var scrollTop = $(window).scrollTop() + 120;
            
            $('.jj-section-anchor').each(function() {
                var $section = $(this);
                var sectionTop = $section.offset().top;
                var sectionBottom = sectionTop + $section.outerHeight();
                var anchorId = $section.attr('id');
                
                if (scrollTop >= sectionTop && scrollTop < sectionBottom) {
                    $('.jj-tab-quick-nav-link').removeClass('active');
                    $('.jj-tab-quick-nav-link[href="#' + anchorId + '"]').addClass('active');
                }
            });
        });
    }

    /**
     * 색상 선택기 초기화 개선
     */
    function enhanceColorPickers() {
        // Spectrum.js가 이미 초기화되어 있으면 스킵
        if (typeof $.fn.spectrum === 'undefined') return;
        
        $('.jj-color-field').each(function() {
            var $input = $(this);
            
            // 이미 초기화되어 있으면 스킵
            if ($input.data('spectrum')) return;
            
            // 래퍼 생성
            if (!$input.closest('.jj-color-field-wrapper').length) {
                $input.wrap('<div class="jj-color-field-wrapper"></div>');
                $input.after('<div class="jj-color-preview"></div>');
            }
            
            var $preview = $input.siblings('.jj-color-preview');
            var currentColor = $input.val() || '#667eea';
            
            // 미리보기 색상 설정
            $preview.css('background-color', currentColor);
            
            // Spectrum.js 초기화 (이미 다른 곳에서 초기화되면 스킵)
            if (!$input.data('spectrum')) {
                $input.spectrum({
                    color: currentColor,
                    preferredFormat: "hex",
                    showInput: true,
                    showInitial: true,
                    showPalette: true,
                    showSelectionPalette: true,
                    maxSelectionSize: 10,
                    change: function(color) {
                        var newColor = color.toHexString();
                        $input.val(newColor);
                        $preview.css('background-color', newColor);
                        updateLivePreview();
                    }
                });
            }
        });
    }

    /**
     * 초기화
     */
    function init() {
        // 탭 전환 시 퀵 네비게이션 생성
        $(document).on('click', '.jj-tab-button', function() {
            setTimeout(function() {
                initQuickNavigation();
            }, 100);
        });
        
        // 초기 퀵 네비게이션 생성
        initQuickNavigation();
        
        // 스크롤 인디케이터
        initScrollIndicator();
        
        // 퀵 네비게이션 스크롤 처리
        initQuickNavScroll();
        
        // 색상 선택기 개선
        enhanceColorPickers();
        
        // 실시간 미리보기 업데이트
        $(document).on('input change', '.jj-data-field', function() {
            updateLivePreview();
        });
        
        // 초기 미리보기 업데이트
        setTimeout(function() {
            updateLivePreview();
        }, 500);
    }

    // DOM 로드 완료 시 초기화
    $(document).ready(function() {
        init();
    });
    
    // AJAX 콘텐츠 로드 후 재초기화
    $(document).on('jj-content-loaded', function() {
        init();
    });

})(jQuery);
