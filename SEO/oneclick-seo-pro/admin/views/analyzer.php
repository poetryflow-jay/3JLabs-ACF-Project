<?php
/**
 * WP Bulk SEO - Analyzer View
 *
 * @package OneClick_SEO_Pro
 * @subpackage Admin/Views
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap oneclick-seo-pro-analyzer">
    <h1><?php esc_html_e('SEO Analyzer', 'oneclick-seo-pro'); ?></h1>

    <div class="analyzer-container">
        <!-- Single URL Analyzer -->
        <div class="analyzer-card">
            <h2><?php esc_html_e('Analyze URL', 'oneclick-seo-pro'); ?></h2>
            <form id="url-analyzer-form" class="analyzer-form">
                <div class="form-group">
                    <label for="analyze-url"><?php esc_html_e('Enter URL to analyze', 'oneclick-seo-pro'); ?></label>
                    <div class="input-group">
                        <input type="url" id="analyze-url" name="url" placeholder="https://example.com/page" required>
                        <button type="submit" class="button button-primary">
                            <span class="dashicons dashicons-search"></span>
                            <?php esc_html_e('Analyze', 'oneclick-seo-pro'); ?>
                        </button>
                    </div>
                </div>
                <div class="options-row">
                    <label>
                        <input type="checkbox" name="use_pagespeed" value="1" checked>
                        <?php esc_html_e('Include PageSpeed API data', 'oneclick-seo-pro'); ?>
                    </label>
                </div>
            </form>
        </div>

        <!-- Post Selector -->
        <div class="analyzer-card">
            <h2><?php esc_html_e('Analyze Content', 'oneclick-seo-pro'); ?></h2>
            <form id="post-analyzer-form" class="analyzer-form">
                <div class="form-group">
                    <label for="post-type-select"><?php esc_html_e('Select Post Type', 'oneclick-seo-pro'); ?></label>
                    <select id="post-type-select" name="post_type">
                        <?php foreach ($post_types as $type): ?>
                        <option value="<?php echo esc_attr($type->name); ?>">
                            <?php echo esc_html($type->labels->name); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="post-select"><?php esc_html_e('Select Content', 'oneclick-seo-pro'); ?></label>
                    <select id="post-select" name="post_id">
                        <option value=""><?php esc_html_e('Select...', 'oneclick-seo-pro'); ?></option>
                    </select>
                </div>

                <button type="submit" class="button button-primary" disabled>
                    <span class="dashicons dashicons-chart-bar"></span>
                    <?php esc_html_e('Analyze Selected', 'oneclick-seo-pro'); ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Analysis Results -->
    <div id="analysis-results" class="analysis-results" style="display: none;">
        <h2><?php esc_html_e('Analysis Results', 'oneclick-seo-pro'); ?></h2>

        <!-- Score Overview -->
        <div class="result-header">
            <div class="overall-score">
                <div class="score-circle">
                    <span id="result-score">0</span>
                </div>
                <div class="grade-badge" id="result-grade">-</div>
            </div>
            <div class="result-meta">
                <h3 id="result-title"></h3>
                <p id="result-url"></p>
            </div>
        </div>

        <!-- Module Scores -->
        <div class="module-scores">
            <h3><?php esc_html_e('Score Breakdown', 'oneclick-seo-pro'); ?></h3>
            <div id="module-scores-grid" class="scores-grid"></div>
        </div>

        <!-- Recommendations -->
        <div class="recommendations-section">
            <h3><?php esc_html_e('Recommendations', 'oneclick-seo-pro'); ?></h3>
            <ul id="recommendations-list" class="recommendations-list"></ul>
        </div>

        <!-- Priority Actions -->
        <div class="priority-actions-section" id="priority-actions-section" style="display: none;">
            <h3><?php esc_html_e('Priority Actions', 'oneclick-seo-pro'); ?></h3>
            <ul id="priority-actions-list" class="priority-list"></ul>
        </div>

        <!-- Actions -->
        <div class="result-actions">
            <button type="button" id="optimize-btn" class="button button-primary">
                <span class="dashicons dashicons-admin-tools"></span>
                <?php esc_html_e('Optimize with AI', 'oneclick-seo-pro'); ?>
            </button>
            <button type="button" id="export-btn" class="button">
                <span class="dashicons dashicons-download"></span>
                <?php esc_html_e('Export Report', 'oneclick-seo-pro'); ?>
            </button>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay" style="display: none;">
        <div class="loading-content">
            <span class="spinner is-active"></span>
            <p><?php esc_html_e('Analyzing...', 'oneclick-seo-pro'); ?></p>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Load posts when post type changes
    $('#post-type-select').on('change', function() {
        var postType = $(this).val();
        var $postSelect = $('#post-select');

        $postSelect.html('<option value=""><?php esc_html_e('Loading...', 'oneclick-seo-pro'); ?></option>');

        $.get(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_get_posts',
            nonce: wpBulkSeo.nonce,
            post_type: postType
        }).done(function(response) {
            if (response.success) {
                var options = '<option value=""><?php esc_html_e('Select...', 'oneclick-seo-pro'); ?></option>';
                response.data.forEach(function(post) {
                    options += '<option value="' + post.ID + '">' + post.post_title + '</option>';
                });
                $postSelect.html(options);
            }
        });
    }).trigger('change');

    // Enable/disable analyze button
    $('#post-select').on('change', function() {
        $('#post-analyzer-form button[type="submit"]').prop('disabled', !$(this).val());
    });

    // URL Analyzer
    $('#url-analyzer-form').on('submit', function(e) {
        e.preventDefault();
        var url = $('#analyze-url').val();
        var usePageSpeed = $('input[name="use_pagespeed"]').is(':checked');

        analyzeUrl(url, usePageSpeed);
    });

    // Post Analyzer
    $('#post-analyzer-form').on('submit', function(e) {
        e.preventDefault();
        var postId = $('#post-select').val();

        if (postId) {
            analyzePost(postId);
        }
    });

    function analyzeUrl(url, usePageSpeed) {
        showLoading();

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_analyze_url',
            nonce: wpBulkSeo.nonce,
            url: url,
            use_pagespeed: usePageSpeed ? 1 : 0
        }).done(function(response) {
            if (response.success) {
                displayResults(response.data);
            } else {
                alert(response.data.message || wpBulkSeo.strings.error);
            }
        }).fail(function() {
            alert(wpBulkSeo.strings.error);
        }).always(hideLoading);
    }

    function analyzePost(postId) {
        showLoading();

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_analyze',
            nonce: wpBulkSeo.nonce,
            post_id: postId
        }).done(function(response) {
            if (response.success) {
                displayResults(response.data);
            } else {
                alert(response.data.message || wpBulkSeo.strings.error);
            }
        }).fail(function() {
            alert(wpBulkSeo.strings.error);
        }).always(hideLoading);
    }

    function displayResults(data) {
        $('#result-score').text(data.score || data.overall_score);
        $('#result-grade').text(data.grade).removeClass().addClass('grade-badge grade-' + (data.grade || 'f').toLowerCase());
        $('#result-title').text(data.title || '');
        $('#result-url').text(data.url || '');

        // Module scores
        var scoresHtml = '';
        if (data.modules) {
            for (var module in data.modules) {
                var m = data.modules[module];
                scoresHtml += '<div class="module-score">';
                scoresHtml += '<span class="module-name">' + (m.category || module) + '</span>';
                scoresHtml += '<div class="score-bar"><div class="score-fill" style="width:' + m.score + '%"></div></div>';
                scoresHtml += '<span class="module-value">' + m.score + '</span>';
                scoresHtml += '</div>';
            }
        }
        $('#module-scores-grid').html(scoresHtml);

        // Recommendations
        var recsHtml = '';
        if (data.recommendations && data.recommendations.length) {
            data.recommendations.forEach(function(rec) {
                recsHtml += '<li class="priority-' + rec.priority + '">';
                recsHtml += '<span class="priority-badge">' + rec.priority + '</span>';
                recsHtml += '<span class="rec-text">' + rec.message + '</span>';
                recsHtml += '</li>';
            });
        } else {
            recsHtml = '<li class="no-issues"><?php esc_html_e('No recommendations - great job!', 'oneclick-seo-pro'); ?></li>';
        }
        $('#recommendations-list').html(recsHtml);

        // Priority actions
        if (data.priority_actions && data.priority_actions.length) {
            var actionsHtml = '';
            data.priority_actions.forEach(function(action) {
                actionsHtml += '<li class="action-' + action.type + '">';
                actionsHtml += '<span class="dashicons dashicons-warning"></span>';
                actionsHtml += action.message;
                actionsHtml += '</li>';
            });
            $('#priority-actions-list').html(actionsHtml);
            $('#priority-actions-section').show();
        } else {
            $('#priority-actions-section').hide();
        }

        $('#analysis-results').fadeIn();

        // Scroll to results
        $('html, body').animate({
            scrollTop: $('#analysis-results').offset().top - 50
        }, 300);
    }

    function showLoading() {
        $('#loading-overlay').fadeIn(200);
    }

    function hideLoading() {
        $('#loading-overlay').fadeOut(200);
    }

    // Optimize button
    $('#optimize-btn').on('click', function() {
        var postId = $('#post-select').val();
        if (!postId) {
            alert('<?php esc_html_e('Please select a post to optimize', 'oneclick-seo-pro'); ?>');
            return;
        }

        showLoading();

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_optimize',
            nonce: wpBulkSeo.nonce,
            post_id: postId,
            type: 'all'
        }).done(function(response) {
            if (response.success) {
                // Show optimization suggestions modal
                showOptimizationModal(response.data.optimizations);
            } else {
                alert(response.data.message || wpBulkSeo.strings.error);
            }
        }).fail(function() {
            alert(wpBulkSeo.strings.error);
        }).always(hideLoading);
    });

    function showOptimizationModal(optimizations) {
        var modal = '<div class="optimization-modal">';
        modal += '<div class="modal-content">';
        modal += '<h3><?php esc_html_e('AI Optimization Suggestions', 'oneclick-seo-pro'); ?></h3>';

        if (optimizations.title) {
            modal += '<div class="optimization-item">';
            modal += '<h4><?php esc_html_e('Title', 'oneclick-seo-pro'); ?></h4>';
            modal += '<p class="original"><strong><?php esc_html_e('Original:', 'oneclick-seo-pro'); ?></strong> ' + optimizations.title.original + '</p>';
            modal += '<p class="optimized"><strong><?php esc_html_e('Optimized:', 'oneclick-seo-pro'); ?></strong> ' + optimizations.title.optimized + '</p>';
            modal += '<button class="button apply-optimization" data-type="title"><?php esc_html_e('Apply', 'oneclick-seo-pro'); ?></button>';
            modal += '</div>';
        }

        if (optimizations.meta_description) {
            modal += '<div class="optimization-item">';
            modal += '<h4><?php esc_html_e('Meta Description', 'oneclick-seo-pro'); ?></h4>';
            modal += '<p class="original"><strong><?php esc_html_e('Original:', 'oneclick-seo-pro'); ?></strong> ' + optimizations.meta_description.original + '</p>';
            modal += '<p class="optimized"><strong><?php esc_html_e('Optimized:', 'oneclick-seo-pro'); ?></strong> ' + optimizations.meta_description.optimized + '</p>';
            modal += '<button class="button apply-optimization" data-type="meta"><?php esc_html_e('Apply', 'oneclick-seo-pro'); ?></button>';
            modal += '</div>';
        }

        modal += '<button class="button modal-close"><?php esc_html_e('Close', 'oneclick-seo-pro'); ?></button>';
        modal += '</div></div>';

        $('body').append(modal);

        $('.modal-close').on('click', function() {
            $('.optimization-modal').remove();
        });
    }

    // Export button
    $('#export-btn').on('click', function() {
        // Simple JSON export
        var results = {
            score: $('#result-score').text(),
            grade: $('#result-grade').text(),
            title: $('#result-title').text(),
            url: $('#result-url').text(),
            exported_at: new Date().toISOString()
        };

        var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(results, null, 2));
        var downloadAnchor = document.createElement('a');
        downloadAnchor.setAttribute("href", dataStr);
        downloadAnchor.setAttribute("download", "seo-report.json");
        document.body.appendChild(downloadAnchor);
        downloadAnchor.click();
        downloadAnchor.remove();
    });
});
</script>
