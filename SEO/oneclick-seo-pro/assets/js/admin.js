/**
 * WP Bulk SEO - Admin JavaScript
 *
 * @package WP_Bulk_SEO
 * @version 1.0.0
 */

(function($) {
    'use strict';

    /**
     * SEO Admin Object
     */
    const SEOAdmin = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initMetaBox();
            this.initTabs();
            this.initBulkOptimizer();
            this.initAEO();
        },

        /**
         * Bind Events
         */
        bindEvents: function() {
            // Analyze button
            $(document).on('click', '.seo-analyze-btn', this.analyzePost.bind(this));

            // Quick edit
            $(document).on('click', '.seo-quick-edit', this.openQuickEdit.bind(this));
            $(document).on('click', '.seo-quick-save', this.saveQuickEdit.bind(this));

            // Generate suggestions
            $(document).on('click', '.seo-generate-title', this.generateTitleSuggestions.bind(this));
            $(document).on('click', '.seo-generate-desc', this.generateDescSuggestions.bind(this));

            // Apply suggestion
            $(document).on('click', '.seo-suggestion-item', this.applySuggestion.bind(this));

            // Character count
            $(document).on('input', '.seo-title-input, .seo-desc-input', this.updateCharCount.bind(this));

            // Settings save
            $(document).on('submit', '#seo-settings-form', this.saveSettings.bind(this));
        },

        /**
         * Initialize Meta Box
         */
        initMetaBox: function() {
            const $metabox = $('#wp_bulk_seo_metabox');
            if (!$metabox.length) return;

            // Update snippet preview
            this.updateSnippetPreview();

            // Watch for changes
            $metabox.on('input', '.seo-title-input, .seo-desc-input', this.debounce(function() {
                SEOAdmin.updateSnippetPreview();
            }, 300));

            // Watch WordPress title
            $('#title').on('input', this.debounce(function() {
                SEOAdmin.updateSnippetPreview();
            }, 300));
        },

        /**
         * Initialize Tabs
         */
        initTabs: function() {
            $('.seo-tabs-nav .tab-item').on('click', function() {
                const target = $(this).data('tab');

                // Update nav
                $(this).siblings().removeClass('active');
                $(this).addClass('active');

                // Update panels
                $(this).closest('.seo-tabs-container')
                    .find('.seo-tab-panel')
                    .removeClass('active');
                $('#' + target).addClass('active');
            });

            // Metabox tabs
            $('.seo-metabox-tab').on('click', function() {
                const target = $(this).data('tab');

                $(this).siblings().removeClass('active');
                $(this).addClass('active');

                $(this).closest('.seo-metabox')
                    .find('.seo-metabox-panel')
                    .removeClass('active');
                $('#' + target).addClass('active');
            });
        },

        /**
         * Initialize Bulk Optimizer
         */
        initBulkOptimizer: function() {
            const $bulkWrap = $('.seo-bulk-optimizer');
            if (!$bulkWrap.length) return;

            // Select all
            $bulkWrap.on('change', '.seo-select-all', function() {
                const checked = $(this).prop('checked');
                $bulkWrap.find('.seo-item-checkbox').prop('checked', checked);
                SEOAdmin.updateBulkActions();
            });

            // Individual checkbox
            $bulkWrap.on('change', '.seo-item-checkbox', function() {
                SEOAdmin.updateBulkActions();
            });

            // Bulk analyze
            $bulkWrap.on('click', '.seo-bulk-analyze', this.bulkAnalyze.bind(this));
        },

        /**
         * Initialize AEO
         */
        initAEO: function() {
            // FAQ accordion
            $(document).on('click', '.seo-faq-item .faq-question', function() {
                $(this).closest('.seo-faq-item').toggleClass('open');
            });

            // Generate FAQ
            $(document).on('click', '.seo-generate-faq', this.generateFAQ.bind(this));

            // Optimize for snippets
            $(document).on('click', '.seo-optimize-snippet', this.optimizeForSnippet.bind(this));
        },

        /**
         * Analyze Post
         */
        analyzePost: function(e) {
            e.preventDefault();

            const $btn = $(e.currentTarget);
            const postId = $btn.data('post-id') || $('#post_ID').val();

            if (!postId) return;

            $btn.addClass('loading').prop('disabled', true);

            $.ajax({
                url: wpBulkSeo.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'wp_bulk_seo_analyze',
                    nonce: wpBulkSeo.nonce,
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        SEOAdmin.updateScoreDisplay(response.data);
                        SEOAdmin.updateIssuesList(response.data.issues);
                        SEOAdmin.showNotice('success', 'Analysis complete!');
                    } else {
                        SEOAdmin.showNotice('error', response.data.message);
                    }
                },
                error: function() {
                    SEOAdmin.showNotice('error', 'Analysis failed. Please try again.');
                },
                complete: function() {
                    $btn.removeClass('loading').prop('disabled', false);
                }
            });
        },

        /**
         * Update Score Display
         */
        updateScoreDisplay: function(data) {
            const grade = this.getGrade(data.score);

            // Update circle score
            $('.seo-score-circle')
                .removeClass('grade-a grade-b grade-c grade-d grade-f')
                .addClass('grade-' + grade.toLowerCase())
                .css('--score', data.score)
                .find('span').text(data.score);

            // Update badge
            $('.seo-score-badge')
                .removeClass('grade-a grade-b grade-c grade-d grade-f')
                .addClass('grade-' + grade.toLowerCase())
                .text(grade + ' (' + data.score + ')');

            // Update module scores
            if (data.modules) {
                $.each(data.modules, function(module, score) {
                    const moduleGrade = SEOAdmin.getGrade(score);
                    const $module = $('.seo-module-score[data-module="' + module + '"]');

                    $module.find('.module-bar-fill')
                        .css('width', score + '%')
                        .removeClass('grade-a grade-b grade-c grade-d grade-f')
                        .addClass('grade-' + moduleGrade.toLowerCase());

                    $module.find('.module-value').text(score);
                });
            }
        },

        /**
         * Update Issues List
         */
        updateIssuesList: function(issues) {
            const $list = $('.seo-issues-list');
            $list.empty();

            if (!issues || issues.length === 0) {
                $list.html('<div class="seo-empty"><p>No issues found. Great job!</p></div>');
                return;
            }

            issues.forEach(function(issue) {
                const icon = issue.severity === 'critical' ? '!' :
                             issue.severity === 'warning' ? '!' : 'i';

                $list.append(
                    '<div class="seo-issue-item ' + issue.severity + '">' +
                        '<div class="issue-icon">' + icon + '</div>' +
                        '<div class="issue-content">' +
                            '<h4 class="issue-title">' + issue.title + '</h4>' +
                            '<p class="issue-desc">' + issue.description + '</p>' +
                        '</div>' +
                        (issue.fix_url ? '<a href="' + issue.fix_url + '" class="seo-btn seo-btn-sm seo-btn-outline issue-action">Fix</a>' : '') +
                    '</div>'
                );
            });
        },

        /**
         * Open Quick Edit
         */
        openQuickEdit: function(e) {
            e.preventDefault();

            const $btn = $(e.currentTarget);
            const postId = $btn.data('post-id');
            const $row = $btn.closest('tr');

            // Fetch current data
            $.ajax({
                url: wpBulkSeo.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'wp_bulk_seo_get_meta',
                    nonce: wpBulkSeo.nonce,
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        SEOAdmin.showQuickEditForm($row, response.data);
                    }
                }
            });
        },

        /**
         * Show Quick Edit Form
         */
        showQuickEditForm: function($row, data) {
            // Close any existing
            $('.seo-quick-edit-form').remove();

            const $form = $(
                '<tr class="seo-quick-edit-form">' +
                    '<td colspan="6">' +
                        '<div class="seo-card">' +
                            '<div class="seo-card-body">' +
                                '<div class="seo-field">' +
                                    '<label>SEO Title</label>' +
                                    '<input type="text" class="seo-title-input" value="' + (data.title || '') + '" />' +
                                    '<div class="char-count"><span class="count">0</span>/60</div>' +
                                '</div>' +
                                '<div class="seo-field">' +
                                    '<label>Meta Description</label>' +
                                    '<textarea class="seo-desc-input">' + (data.description || '') + '</textarea>' +
                                    '<div class="char-count"><span class="count">0</span>/160</div>' +
                                '</div>' +
                                '<div class="seo-field">' +
                                    '<label>Focus Keyword</label>' +
                                    '<input type="text" class="seo-keyword-input" value="' + (data.focus_keyword || '') + '" />' +
                                '</div>' +
                                '<div style="display: flex; gap: 8px; margin-top: 16px;">' +
                                    '<button type="button" class="seo-btn seo-btn-primary seo-quick-save" data-post-id="' + data.post_id + '">Save</button>' +
                                    '<button type="button" class="seo-btn seo-btn-outline seo-quick-cancel">Cancel</button>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</td>' +
                '</tr>'
            );

            $row.after($form);

            // Update char counts
            $form.find('.seo-title-input, .seo-desc-input').trigger('input');

            // Cancel button
            $form.find('.seo-quick-cancel').on('click', function() {
                $form.remove();
            });
        },

        /**
         * Save Quick Edit
         */
        saveQuickEdit: function(e) {
            e.preventDefault();

            const $btn = $(e.currentTarget);
            const postId = $btn.data('post-id');
            const $form = $btn.closest('.seo-quick-edit-form');

            const data = {
                action: 'wp_bulk_seo_save_meta',
                nonce: wpBulkSeo.nonce,
                post_id: postId,
                title: $form.find('.seo-title-input').val(),
                description: $form.find('.seo-desc-input').val(),
                focus_keyword: $form.find('.seo-keyword-input').val()
            };

            $btn.addClass('loading').prop('disabled', true);

            $.ajax({
                url: wpBulkSeo.ajaxUrl,
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        SEOAdmin.showNotice('success', 'SEO data saved!');
                        $form.remove();

                        // Update row if score changed
                        if (response.data.score) {
                            // Refresh the row
                            location.reload();
                        }
                    } else {
                        SEOAdmin.showNotice('error', response.data.message);
                    }
                },
                error: function() {
                    SEOAdmin.showNotice('error', 'Failed to save. Please try again.');
                },
                complete: function() {
                    $btn.removeClass('loading').prop('disabled', false);
                }
            });
        },

        /**
         * Generate Title Suggestions
         */
        generateTitleSuggestions: function(e) {
            e.preventDefault();

            const $btn = $(e.currentTarget);
            const postId = $('#post_ID').val();

            if (!postId) return;

            $btn.addClass('loading').prop('disabled', true);

            $.ajax({
                url: wpBulkSeo.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'wp_bulk_seo_generate_titles',
                    nonce: wpBulkSeo.nonce,
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        SEOAdmin.showSuggestions($btn, response.data.suggestions, 'title');
                    } else {
                        SEOAdmin.showNotice('error', response.data.message);
                    }
                },
                error: function() {
                    SEOAdmin.showNotice('error', 'Failed to generate suggestions.');
                },
                complete: function() {
                    $btn.removeClass('loading').prop('disabled', false);
                }
            });
        },

        /**
         * Generate Description Suggestions
         */
        generateDescSuggestions: function(e) {
            e.preventDefault();

            const $btn = $(e.currentTarget);
            const postId = $('#post_ID').val();

            if (!postId) return;

            $btn.addClass('loading').prop('disabled', true);

            $.ajax({
                url: wpBulkSeo.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'wp_bulk_seo_generate_descriptions',
                    nonce: wpBulkSeo.nonce,
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        SEOAdmin.showSuggestions($btn, response.data.suggestions, 'description');
                    } else {
                        SEOAdmin.showNotice('error', response.data.message);
                    }
                },
                error: function() {
                    SEOAdmin.showNotice('error', 'Failed to generate suggestions.');
                },
                complete: function() {
                    $btn.removeClass('loading').prop('disabled', false);
                }
            });
        },

        /**
         * Show Suggestions
         */
        showSuggestions: function($btn, suggestions, type) {
            // Remove existing
            $('.seo-suggestions-popup').remove();

            const $popup = $('<div class="seo-suggestions-popup"></div>');

            suggestions.forEach(function(suggestion) {
                $popup.append(
                    '<div class="seo-suggestion-item" data-type="' + type + '">' +
                        '<span>' + suggestion + '</span>' +
                    '</div>'
                );
            });

            $btn.after($popup);

            // Close on click outside
            $(document).on('click.suggestions', function(e) {
                if (!$(e.target).closest('.seo-suggestions-popup, .seo-generate-title, .seo-generate-desc').length) {
                    $popup.remove();
                    $(document).off('click.suggestions');
                }
            });
        },

        /**
         * Apply Suggestion
         */
        applySuggestion: function(e) {
            const $item = $(e.currentTarget);
            const type = $item.data('type');
            const value = $item.find('span').text();

            if (type === 'title') {
                $('.seo-title-input').val(value).trigger('input');
            } else {
                $('.seo-desc-input').val(value).trigger('input');
            }

            $item.closest('.seo-suggestions-popup').remove();
        },

        /**
         * Update Character Count
         */
        updateCharCount: function(e) {
            const $input = $(e.currentTarget);
            const length = $input.val().length;
            const $count = $input.siblings('.char-count').find('.count');
            const max = $input.hasClass('seo-title-input') ? 60 : 160;

            $count.text(length);

            $input.siblings('.char-count')
                .removeClass('warning error')
                .addClass(length > max ? 'error' : (length > max * 0.9 ? 'warning' : ''));
        },

        /**
         * Update Snippet Preview
         */
        updateSnippetPreview: function() {
            const $preview = $('.seo-snippet-preview');
            if (!$preview.length) return;

            const title = $('.seo-title-input').val() || $('#title').val() || 'Page Title';
            const desc = $('.seo-desc-input').val() || 'Your meta description will appear here...';
            const url = wpBulkSeo.siteUrl + '/example-page/';

            $preview.find('.preview-title').text(title);
            $preview.find('.preview-desc').text(desc);
            $preview.find('.preview-url').text(url);
        },

        /**
         * Bulk Analyze
         */
        bulkAnalyze: function(e) {
            e.preventDefault();

            const $btn = $(e.currentTarget);
            const postIds = [];

            $('.seo-item-checkbox:checked').each(function() {
                postIds.push($(this).val());
            });

            if (postIds.length === 0) {
                this.showNotice('error', 'Please select at least one post.');
                return;
            }

            const $progress = $('.seo-progress-bar');
            const $progressText = $('.seo-progress-text');
            let current = 0;
            const total = postIds.length;

            $btn.prop('disabled', true);
            $progress.show();

            const processNext = function() {
                if (current >= total) {
                    $btn.prop('disabled', false);
                    $progress.hide();
                    SEOAdmin.showNotice('success', 'Bulk analysis complete!');
                    location.reload();
                    return;
                }

                const postId = postIds[current];
                const percent = Math.round((current / total) * 100);

                $progress.find('.progress-fill').css('width', percent + '%');
                $progressText.find('.current').text(current);
                $progressText.find('.total').text(total);

                $.ajax({
                    url: wpBulkSeo.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: 'wp_bulk_seo_analyze',
                        nonce: wpBulkSeo.nonce,
                        post_id: postId
                    },
                    complete: function() {
                        current++;
                        processNext();
                    }
                });
            };

            processNext();
        },

        /**
         * Update Bulk Actions
         */
        updateBulkActions: function() {
            const checkedCount = $('.seo-item-checkbox:checked').length;
            $('.seo-bulk-count').text(checkedCount);
            $('.seo-bulk-actions').toggle(checkedCount > 0);
        },

        /**
         * Generate FAQ
         */
        generateFAQ: function(e) {
            e.preventDefault();

            const $btn = $(e.currentTarget);
            const postId = $('#post_ID').val();

            if (!postId) return;

            $btn.addClass('loading').prop('disabled', true);

            $.ajax({
                url: wpBulkSeo.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'wp_bulk_seo_generate_faq',
                    nonce: wpBulkSeo.nonce,
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        SEOAdmin.renderFAQList(response.data.faqs);
                        SEOAdmin.showNotice('success', 'FAQ generated successfully!');
                    } else {
                        SEOAdmin.showNotice('error', response.data.message);
                    }
                },
                error: function() {
                    SEOAdmin.showNotice('error', 'Failed to generate FAQ.');
                },
                complete: function() {
                    $btn.removeClass('loading').prop('disabled', false);
                }
            });
        },

        /**
         * Render FAQ List
         */
        renderFAQList: function(faqs) {
            const $list = $('.seo-faq-list');
            $list.empty();

            faqs.forEach(function(faq, index) {
                $list.append(
                    '<div class="seo-faq-item">' +
                        '<div class="faq-question">' +
                            '<span class="faq-number">' + (index + 1) + '</span>' +
                            '<span>' + faq.question + '</span>' +
                            '<span class="faq-toggle">&#9660;</span>' +
                        '</div>' +
                        '<div class="faq-answer">' + faq.answer + '</div>' +
                    '</div>'
                );
            });
        },

        /**
         * Optimize for Snippet
         */
        optimizeForSnippet: function(e) {
            e.preventDefault();

            const $btn = $(e.currentTarget);
            const postId = $('#post_ID').val();

            if (!postId) return;

            $btn.addClass('loading').prop('disabled', true);

            $.ajax({
                url: wpBulkSeo.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'wp_bulk_seo_optimize_snippet',
                    nonce: wpBulkSeo.nonce,
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        SEOAdmin.showOptimizationSuggestions(response.data.suggestions);
                    } else {
                        SEOAdmin.showNotice('error', response.data.message);
                    }
                },
                error: function() {
                    SEOAdmin.showNotice('error', 'Failed to optimize.');
                },
                complete: function() {
                    $btn.removeClass('loading').prop('disabled', false);
                }
            });
        },

        /**
         * Show Optimization Suggestions
         */
        showOptimizationSuggestions: function(suggestions) {
            const $container = $('.seo-optimization-suggestions');
            $container.empty();

            if (!suggestions || suggestions.length === 0) {
                $container.html('<p>No optimization suggestions available.</p>');
                return;
            }

            suggestions.forEach(function(suggestion) {
                $container.append(
                    '<div class="seo-issue-item info">' +
                        '<div class="issue-icon">i</div>' +
                        '<div class="issue-content">' +
                            '<p class="issue-desc">' + suggestion + '</p>' +
                        '</div>' +
                    '</div>'
                );
            });
        },

        /**
         * Save Settings
         */
        saveSettings: function(e) {
            e.preventDefault();

            const $form = $(e.currentTarget);
            const $btn = $form.find('button[type="submit"]');

            $btn.addClass('loading').prop('disabled', true);

            $.ajax({
                url: wpBulkSeo.ajaxUrl,
                method: 'POST',
                data: $form.serialize() + '&action=wp_bulk_seo_save_settings&nonce=' + wpBulkSeo.nonce,
                success: function(response) {
                    if (response.success) {
                        SEOAdmin.showNotice('success', 'Settings saved!');
                    } else {
                        SEOAdmin.showNotice('error', response.data.message);
                    }
                },
                error: function() {
                    SEOAdmin.showNotice('error', 'Failed to save settings.');
                },
                complete: function() {
                    $btn.removeClass('loading').prop('disabled', false);
                }
            });
        },

        /**
         * Get Grade from Score
         */
        getGrade: function(score) {
            if (score >= 90) return 'A';
            if (score >= 80) return 'B';
            if (score >= 70) return 'C';
            if (score >= 60) return 'D';
            return 'F';
        },

        /**
         * Show Notice
         */
        showNotice: function(type, message) {
            const $notice = $(
                '<div class="seo-notice seo-notice-' + type + '">' +
                    '<p>' + message + '</p>' +
                '</div>'
            );

            $('.seo-wrap, .seo-metabox').first().prepend($notice);

            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },

        /**
         * Debounce Utility
         */
        debounce: function(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }
    };

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        SEOAdmin.init();
    });

    // Expose globally
    window.SEOAdmin = SEOAdmin;

})(jQuery);
