/**
 * 3J Labs Section Enhancer 2026
 * Progressive enhancement for existing section views
 * 
 * @version 1.0.0
 * @author Jason x Jenny
 * @created 2026-01-03
 * 
 * Purpose: Apply UI System 2026 enhancements without breaking existing functionality
 */

(function($) {
    'use strict';

    // Namespace
    window.JJ_SectionEnhancer = window.JJ_SectionEnhancer || {};

    /**
     * Enhance Colors Section
     */
    JJ_SectionEnhancer.Colors = {
        init: function() {
            this.enhancePaletteDisplay();
            this.addColorCopyOnClick();
            this.addPaletteSummary();
        },

        enhancePaletteDisplay: function() {
            // Find all color input groups and enhance them
            $('.jj-section-global input[type="text"][name*="palettes"]').each(function() {
                const $input = $(this);
                const $parent = $input.closest('tr, .form-field, .color-field');
                
                if ($parent.length && !$parent.hasClass('jj-enhanced')) {
                    $parent.addClass('jj-enhanced jj-palette-item');
                    
                    // Add color preview if wp-color-picker exists
                    const $colorResult = $parent.find('.wp-color-result');
                    if ($colorResult.length) {
                        $colorResult.addClass('jj-palette-color-preview');
                    }
                }
            });
        },

        addColorCopyOnClick: function() {
            // Copy color value to clipboard on click
            $(document).on('click', '.jj-palette-color-preview', function(e) {
                const $this = $(this);
                const $input = $this.siblings('input[type="text"]');
                
                if ($input.length) {
                    const colorValue = $input.val();
                    if (colorValue && window.JJ_UI && window.JJ_UI.CopyToClipboard) {
                        JJ_UI.CopyToClipboard(colorValue, this);
                        
                        // Visual feedback
                        $this.css('transform', 'scale(1.15)');
                        setTimeout(function() {
                            $this.css('transform', '');
                        }, 200);
                    }
                }
            });
        },

        addPaletteSummary: function() {
            // Add summary cards for each palette type
            const paletteTabs = ['brand', 'system', 'alternative', 'another'];
            
            paletteTabs.forEach(function(paletteType) {
                const $tabContent = $('.jj-tab-content[data-tab="' + paletteType + '"]');
                
                if ($tabContent.length && !$tabContent.find('.jj-palette-summary').length) {
                    const colorCount = $tabContent.find('input[type="text"][name*="palettes"]').length;
                    
                    if (colorCount > 0) {
                        const summaryHtml = '<div class="jj-palette-summary" style="margin-bottom: 20px;">' +
                            '<div class="jj-stat-box" style="display: inline-block; padding: 16px 24px;">' +
                                '<div class="jj-stat-value">' + colorCount + '</div>' +
                                '<div class="jj-stat-label">Colors Defined</div>' +
                            '</div>' +
                        '</div>';
                        
                        $tabContent.prepend(summaryHtml);
                    }
                }
            });
        }
    };

    /**
     * Enhance Typography Section
     */
    JJ_SectionEnhancer.Typography = {
        init: function() {
            this.enhanceFontPreviews();
            this.addLiveFontPreview();
        },

        enhanceFontPreviews: function() {
            // Find font family inputs and add live previews
            $('input[name*="fonts"], select[name*="fonts"]').each(function() {
                const $input = $(this);
                const $parent = $input.closest('.form-field, tr');
                
                if ($parent.length && !$parent.hasClass('jj-font-enhanced')) {
                    $parent.addClass('jj-font-enhanced jj-font-preview-card');
                }
            });
        },

        addLiveFontPreview: function() {
            // Live preview for font changes
            $('input[name*="font_family"], select[name*="font_family"]').on('change keyup', function() {
                const $this = $(this);
                const fontFamily = $this.val();
                const $previewArea = $this.siblings('.jj-font-sample-text');
                
                if ($previewArea.length && fontFamily) {
                    $previewArea.css('font-family', fontFamily);
                }
            });
        }
    };

    /**
     * Enhance Buttons Section
     */
    JJ_SectionEnhancer.Buttons = {
        init: function() {
            this.enhanceButtonPreviews();
            this.addLiveButtonPreview();
        },

        enhanceButtonPreviews: function() {
            // Enhance button preview areas
            $('.jj-section-global .button-preview-area').each(function() {
                const $area = $(this);
                if (!$area.hasClass('jj-enhanced')) {
                    $area.addClass('jj-enhanced jj-button-preview-grid');
                }
            });
        },

        addLiveButtonPreview: function() {
            // Live preview for button style changes
            $('input[name*="button_"], select[name*="button_"]').on('change keyup', function() {
                const $this = $(this);
                const $previewButton = $this.closest('.form-field').find('.button-preview');
                
                if ($previewButton.length) {
                    // Update preview in real-time
                    const inputName = $this.attr('name');
                    const value = $this.val();
                    
                    if (inputName.includes('bg_color')) {
                        $previewButton.css('background-color', value);
                    } else if (inputName.includes('text_color')) {
                        $previewButton.css('color', value);
                    } else if (inputName.includes('border_radius')) {
                        $previewButton.css('border-radius', value + 'px');
                    }
                    
                    // Add pulse animation
                    $previewButton.addClass('jj-pulse');
                    setTimeout(function() {
                        $previewButton.removeClass('jj-pulse');
                    }, 300);
                }
            });
        }
    };

    /**
     * Enhance Stats Section
     */
    JJ_SectionEnhancer.Stats = {
        init: function() {
            this.animateNumbers();
            this.enhanceCharts();
        },

        animateNumbers: function() {
            // Animate stat numbers on page load
            $('.jj-stat-value').each(function() {
                const $this = $(this);
                const target = parseInt($this.text());
                
                if (!isNaN(target)) {
                    $this.text('0');
                    
                    $({ count: 0 }).animate({ count: target }, {
                        duration: 1000,
                        easing: 'swing',
                        step: function() {
                            $this.text(Math.floor(this.count));
                        },
                        complete: function() {
                            $this.text(target);
                        }
                    });
                }
            });
        },

        enhanceCharts: function() {
            // Add loading indicator for charts
            $('.chart-container').each(function() {
                const $container = $(this);
                if (!$container.find('canvas').length) {
                    $container.html('<div class="jj-spinner jj-spinner-lg" style="margin: 40px auto;"></div>');
                }
            });
        }
    };

    /**
     * Enhance All Sections
     */
    JJ_SectionEnhancer.initAll = function() {
        // Check if we're on a style guide page
        if (!$('.jj-section-global').length) {
            return;
        }

        // Initialize all enhancers
        if ($('#jj-section-palettes').length) {
            JJ_SectionEnhancer.Colors.init();
        }

        if ($('#jj-section-typography').length || $('body').hasClass('typography-section')) {
            JJ_SectionEnhancer.Typography.init();
        }

        if ($('#jj-section-buttons').length || $('body').hasClass('buttons-section')) {
            JJ_SectionEnhancer.Buttons.init();
        }

        if ($('#jj-section-stats').length || $('body').hasClass('stats-section')) {
            JJ_SectionEnhancer.Stats.init();
        }

        // Global enhancements
        this.enhanceActionButtons();
        this.enhanceSectionHeaders();
        this.addScrollToTop();
    };

    /**
     * Enhance Action Buttons
     */
    JJ_SectionEnhancer.enhanceActionButtons = function() {
        // Add loading state to save buttons
        $('button[type="submit"], input[type="submit"]').on('click', function() {
            const $btn = $(this);
            if (!$btn.hasClass('jj-btn')) {
                $btn.addClass('jj-btn jj-btn-primary');
            }
            
            // Add spinner on submit
            const originalText = $btn.html();
            $btn.prop('disabled', true);
            $btn.html('<span class="jj-spinner" style="display: inline-block; margin-right: 8px;"></span> Saving...');
            
            // Reset after form submission (or timeout)
            setTimeout(function() {
                $btn.prop('disabled', false);
                $btn.html(originalText);
            }, 3000);
        });
    };

    /**
     * Enhance Section Headers
     */
    JJ_SectionEnhancer.enhanceSectionHeaders = function() {
        // Add icons to section titles if they don't have them
        const sectionIcons = {
            'palettes': 'dashicons-art',
            'typography': 'dashicons-editor-textcolor',
            'buttons': 'dashicons-admin-settings',
            'forms': 'dashicons-feedback',
            'stats': 'dashicons-chart-bar',
            'presets': 'dashicons-admin-appearance',
            'optimizer': 'dashicons-performance',
            'team-sync': 'dashicons-groups',
            'labs': 'dashicons-superhero-alt'
        };

        $('.jj-section-title').each(function() {
            const $title = $(this);
            const sectionId = $title.closest('[id^="jj-section-"]').attr('id');
            
            if (sectionId && !$title.find('.dashicons').length) {
                const sectionKey = sectionId.replace('jj-section-', '');
                const iconClass = sectionIcons[sectionKey] || 'dashicons-admin-generic';
                
                $title.prepend('<span class="dashicons ' + iconClass + '" style="margin-right: 8px; color: var(--jj-primary);"></span>');
            }
        });
    };

    /**
     * Add Scroll to Top Button
     */
    JJ_SectionEnhancer.addScrollToTop = function() {
        if ($('#jj-scroll-to-top').length) {
            return;
        }

        const scrollBtn = $('<button id="jj-scroll-to-top" class="jj-btn jj-btn-primary" style="position: fixed; bottom: 32px; right: 32px; z-index: 9999; display: none; border-radius: 50%; width: 48px; height: 48px; padding: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">' +
            '<span class="dashicons dashicons-arrow-up-alt2" style="font-size: 24px;"></span>' +
        '</button>');

        $('body').append(scrollBtn);

        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                scrollBtn.fadeIn(200);
            } else {
                scrollBtn.fadeOut(200);
            }
        });

        scrollBtn.on('click', function() {
            $('html, body').animate({ scrollTop: 0 }, 600);
        });
    };

    /**
     * Initialize on Document Ready
     */
    $(document).ready(function() {
        // Small delay to ensure all assets are loaded
        setTimeout(function() {
            JJ_SectionEnhancer.initAll();
        }, 100);

        // Re-enhance after AJAX updates
        $(document).ajaxComplete(function() {
            setTimeout(function() {
                JJ_SectionEnhancer.initAll();
            }, 100);
        });
    });

})(jQuery);
