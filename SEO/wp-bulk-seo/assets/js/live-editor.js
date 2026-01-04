/**
 * WP Bulk SEO - Live Editor
 * Alli AI 스타일의 실시간 SEO 편집기
 *
 * @package WP_Bulk_SEO
 * @version 2.1.0
 */

(function($) {
    'use strict';

    var LiveEditor = {
        init: function() {
            this.bindEvents();
            this.initLivePreview();
        },

        bindEvents: function() {
            // Title live edit
            $(document).on('input', '#wp-bulk-seo-live-title', function() {
                LiveEditor.updatePreview('title', $(this).val());
                LiveEditor.debounceSave('title', $(this).val());
            });

            // Description live edit
            $(document).on('input', '#wp-bulk-seo-live-description', function() {
                LiveEditor.updatePreview('description', $(this).val());
                LiveEditor.debounceSave('description', $(this).val());
            });

            // Focus keyword live edit
            $(document).on('input', '#wp-bulk-seo-live-keyword', function() {
                LiveEditor.debounceSave('focus_keyword', $(this).val());
            });
        },

        initLivePreview: function() {
            // Add live editor meta box if not exists
            if ($('#wp-bulk-seo-live-editor').length === 0) {
                this.addLiveEditorMetaBox();
            }
        },

        addLiveEditorMetaBox: function() {
            // This would be added via PHP hook in post editor
            // For now, we'll handle it via JavaScript if needed
        },

        updatePreview: function(field, value) {
            var $preview = $('#wp-bulk-seo-serp-preview');
            if ($preview.length === 0) return;

            switch (field) {
                case 'title':
                    $preview.find('.serp-title').text(value || '제목을 입력하세요');
                    this.updateTitleLength(value);
                    break;
                case 'description':
                    $preview.find('.serp-description').text(value || '설명을 입력하세요');
                    this.updateDescriptionLength(value);
                    break;
            }

            // Update score indicator
            this.updateScoreIndicator();
        },

        updateTitleLength: function(title) {
            var length = title ? title.length : 0;
            var $indicator = $('#title-length-indicator');
            
            if ($indicator.length === 0) {
                $indicator = $('<span id="title-length-indicator" style="font-size: 12px; color: #666;"></span>');
                $('#wp-bulk-seo-live-title').after($indicator);
            }

            var color = '#46b450'; // Green
            if (length < 30) color = '#ffb900'; // Yellow
            if (length > 60) color = '#dc3232'; // Red

            $indicator.css('color', color).text(length + '/60');
        },

        updateDescriptionLength: function(description) {
            var length = description ? description.length : 0;
            var $indicator = $('#description-length-indicator');
            
            if ($indicator.length === 0) {
                $indicator = $('<span id="description-length-indicator" style="font-size: 12px; color: #666;"></span>');
                $('#wp-bulk-seo-live-description').after($indicator);
            }

            var color = '#46b450'; // Green
            if (length < 120) color = '#ffb900'; // Yellow
            if (length > 160) color = '#dc3232'; // Red

            $indicator.css('color', color).text(length + '/160');
        },

        debounceSave: function(field, value) {
            clearTimeout(this.saveTimeout);
            var postId = $('#post_ID').val();

            if (!postId) return;

            this.saveTimeout = setTimeout(function() {
                LiveEditor.saveField(postId, field, value);
            }, 1000); // 1 second debounce
        },

        saveField: function(postId, field, value) {
            $.post(wpBulkSeoLive.ajaxUrl, {
                action: 'wp_bulk_seo_live_edit',
                nonce: wpBulkSeoLive.nonce,
                post_id: postId,
                field: field,
                value: value
            }).done(function(response) {
                if (response.success) {
                    LiveEditor.updateScoreDisplay(response.data.score, response.data.grade);
                    LiveEditor.updatePreviewHTML(response.data.preview);
                }
            });
        },

        updateScoreDisplay: function(score, grade) {
            var $scoreBadge = $('#wp-bulk-seo-live-score');
            if ($scoreBadge.length === 0) return;

            $scoreBadge.text(score + ' (' + grade + ')');

            // Update color based on score
            var color = '#dc3232'; // Red
            if (score >= 80) color = '#46b450'; // Green
            else if (score >= 60) color = '#00a0d2'; // Blue
            else if (score >= 40) color = '#ffb900'; // Yellow

            $scoreBadge.css('color', color);
        },

        updatePreviewHTML: function(html) {
            var $preview = $('#wp-bulk-seo-serp-preview');
            if ($preview.length && html) {
                $preview.html(html);
            }
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        LiveEditor.init();
    });

})(jQuery);
