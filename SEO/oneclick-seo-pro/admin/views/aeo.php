<?php
/**
 * WP Bulk SEO - AEO (Answer Engine Optimization) View
 *
 * @package OneClick_SEO_Pro
 * @subpackage Admin/Views
 */

if (!defined('ABSPATH')) {
    exit;
}

$aeo_settings = get_option('oneclick_seo_pro_aeo', []);
?>
<div class="wrap oneclick-seo-pro-aeo">
    <h1><?php esc_html_e('Answer Engine Optimization (AEO)', 'oneclick-seo-pro'); ?></h1>

    <p class="description">
        <?php esc_html_e('Optimize your content for AI-powered search engines like ChatGPT, Perplexity, Claude, and Google SGE.', 'oneclick-seo-pro'); ?>
    </p>

    <!-- AEO Overview Cards -->
    <div class="aeo-overview">
        <div class="aeo-card">
            <div class="aeo-icon">
                <span class="dashicons dashicons-format-chat"></span>
            </div>
            <div class="aeo-content">
                <h3><?php esc_html_e('Conversational Readiness', 'oneclick-seo-pro'); ?></h3>
                <div class="aeo-score" id="conversational-score">--</div>
                <p><?php esc_html_e('How well your content answers natural language questions', 'oneclick-seo-pro'); ?></p>
            </div>
        </div>

        <div class="aeo-card">
            <div class="aeo-icon">
                <span class="dashicons dashicons-editor-help"></span>
            </div>
            <div class="aeo-content">
                <h3><?php esc_html_e('FAQ Coverage', 'oneclick-seo-pro'); ?></h3>
                <div class="aeo-score" id="faq-score">--</div>
                <p><?php esc_html_e('Question-answer structured content availability', 'oneclick-seo-pro'); ?></p>
            </div>
        </div>

        <div class="aeo-card">
            <div class="aeo-icon">
                <span class="dashicons dashicons-excerpt-view"></span>
            </div>
            <div class="aeo-content">
                <h3><?php esc_html_e('Snippet Potential', 'oneclick-seo-pro'); ?></h3>
                <div class="aeo-score" id="snippet-score">--</div>
                <p><?php esc_html_e('Likelihood of appearing in featured snippets', 'oneclick-seo-pro'); ?></p>
            </div>
        </div>

        <div class="aeo-card">
            <div class="aeo-icon">
                <span class="dashicons dashicons-admin-links"></span>
            </div>
            <div class="aeo-content">
                <h3><?php esc_html_e('Citation Worthiness', 'oneclick-seo-pro'); ?></h3>
                <div class="aeo-score" id="citation-score">--</div>
                <p><?php esc_html_e('How likely AI will cite your content as a source', 'oneclick-seo-pro'); ?></p>
            </div>
        </div>
    </div>

    <!-- Content Analysis Section -->
    <div class="aeo-section">
        <h2><?php esc_html_e('Content AEO Analysis', 'oneclick-seo-pro'); ?></h2>

        <div class="aeo-analyzer">
            <div class="analyzer-controls">
                <select id="aeo-post-select" class="regular-text">
                    <option value=""><?php esc_html_e('Select a post to analyze...', 'oneclick-seo-pro'); ?></option>
                </select>
                <button type="button" id="analyze-aeo" class="button button-primary" disabled>
                    <span class="dashicons dashicons-search"></span>
                    <?php esc_html_e('Analyze AEO', 'oneclick-seo-pro'); ?>
                </button>
            </div>

            <div class="aeo-results" style="display: none;">
                <div class="results-header">
                    <h3 id="analyzed-post-title"></h3>
                    <div class="overall-aeo-score">
                        <span class="score-label"><?php esc_html_e('AEO Score', 'oneclick-seo-pro'); ?></span>
                        <span class="score-value" id="overall-aeo-score">--</span>
                        <span class="score-grade" id="aeo-grade">--</span>
                    </div>
                </div>

                <div class="results-grid">
                    <!-- Factor Scores -->
                    <div class="results-card factors-card">
                        <h4><?php esc_html_e('AEO Factors', 'oneclick-seo-pro'); ?></h4>
                        <div class="factors-list" id="aeo-factors"></div>
                    </div>

                    <!-- Recommendations -->
                    <div class="results-card recommendations-card">
                        <h4><?php esc_html_e('Recommendations', 'oneclick-seo-pro'); ?></h4>
                        <ul class="recommendations-list" id="aeo-recommendations"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Generator Section -->
    <div class="aeo-section">
        <h2><?php esc_html_e('FAQ Generator', 'oneclick-seo-pro'); ?></h2>
        <p class="description">
            <?php esc_html_e('Generate FAQ schema markup to improve your chances of appearing in AI search results.', 'oneclick-seo-pro'); ?>
        </p>

        <div class="faq-generator">
            <div class="generator-controls">
                <select id="faq-post-select" class="regular-text">
                    <option value=""><?php esc_html_e('Select a post...', 'oneclick-seo-pro'); ?></option>
                </select>

                <div class="generator-options">
                    <label>
                        <input type="checkbox" id="faq-extract-existing" checked>
                        <?php esc_html_e('Extract existing Q&A from content', 'oneclick-seo-pro'); ?>
                    </label>
                    <label>
                        <input type="checkbox" id="faq-generate-ai" <?php checked(!empty($aeo_settings['auto_generate_faq'])); ?>>
                        <?php esc_html_e('Generate additional FAQs with AI', 'oneclick-seo-pro'); ?>
                    </label>
                </div>

                <button type="button" id="generate-faq" class="button button-primary" disabled>
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php esc_html_e('Generate FAQ', 'oneclick-seo-pro'); ?>
                </button>
            </div>

            <div class="faq-preview" style="display: none;">
                <h4><?php esc_html_e('Generated FAQ Items', 'oneclick-seo-pro'); ?></h4>
                <div class="faq-items" id="faq-items"></div>

                <div class="faq-actions">
                    <button type="button" id="add-faq-item" class="button">
                        <span class="dashicons dashicons-plus"></span>
                        <?php esc_html_e('Add FAQ Item', 'oneclick-seo-pro'); ?>
                    </button>
                    <button type="button" id="save-faq" class="button button-primary">
                        <span class="dashicons dashicons-saved"></span>
                        <?php esc_html_e('Save FAQ Schema', 'oneclick-seo-pro'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Snippet Optimizer -->
    <div class="aeo-section">
        <h2><?php esc_html_e('Featured Snippet Optimizer', 'oneclick-seo-pro'); ?></h2>
        <p class="description">
            <?php esc_html_e('Optimize your content structure to win featured snippets (position zero).', 'oneclick-seo-pro'); ?>
        </p>

        <div class="snippet-optimizer">
            <div class="optimizer-controls">
                <select id="snippet-post-select" class="regular-text">
                    <option value=""><?php esc_html_e('Select a post...', 'oneclick-seo-pro'); ?></option>
                </select>
                <button type="button" id="analyze-snippets" class="button button-primary" disabled>
                    <span class="dashicons dashicons-visibility"></span>
                    <?php esc_html_e('Analyze Snippet Potential', 'oneclick-seo-pro'); ?>
                </button>
            </div>

            <div class="snippet-results" style="display: none;">
                <div class="snippet-types">
                    <div class="snippet-type" data-type="paragraph">
                        <div class="snippet-icon"><span class="dashicons dashicons-editor-paragraph"></span></div>
                        <div class="snippet-info">
                            <h5><?php esc_html_e('Paragraph Snippet', 'oneclick-seo-pro'); ?></h5>
                            <div class="snippet-potential" id="paragraph-potential">--</div>
                        </div>
                    </div>
                    <div class="snippet-type" data-type="list">
                        <div class="snippet-icon"><span class="dashicons dashicons-editor-ul"></span></div>
                        <div class="snippet-info">
                            <h5><?php esc_html_e('List Snippet', 'oneclick-seo-pro'); ?></h5>
                            <div class="snippet-potential" id="list-potential">--</div>
                        </div>
                    </div>
                    <div class="snippet-type" data-type="table">
                        <div class="snippet-icon"><span class="dashicons dashicons-editor-table"></span></div>
                        <div class="snippet-info">
                            <h5><?php esc_html_e('Table Snippet', 'oneclick-seo-pro'); ?></h5>
                            <div class="snippet-potential" id="table-potential">--</div>
                        </div>
                    </div>
                    <div class="snippet-type" data-type="steps">
                        <div class="snippet-icon"><span class="dashicons dashicons-editor-ol"></span></div>
                        <div class="snippet-info">
                            <h5><?php esc_html_e('How-to Steps', 'oneclick-seo-pro'); ?></h5>
                            <div class="snippet-potential" id="steps-potential">--</div>
                        </div>
                    </div>
                </div>

                <div class="snippet-suggestions" id="snippet-suggestions"></div>
            </div>
        </div>
    </div>

    <!-- Bulk AEO Analysis -->
    <div class="aeo-section">
        <h2><?php esc_html_e('Bulk AEO Analysis', 'oneclick-seo-pro'); ?></h2>

        <div class="bulk-aeo-controls">
            <select id="bulk-post-type" class="regular-text">
                <option value="post"><?php esc_html_e('Posts', 'oneclick-seo-pro'); ?></option>
                <option value="page"><?php esc_html_e('Pages', 'oneclick-seo-pro'); ?></option>
                <?php
                $custom_types = get_post_types(['public' => true, '_builtin' => false], 'objects');
                foreach ($custom_types as $type):
                ?>
                <option value="<?php echo esc_attr($type->name); ?>"><?php echo esc_html($type->labels->name); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" id="run-bulk-aeo" class="button button-primary">
                <span class="dashicons dashicons-admin-generic"></span>
                <?php esc_html_e('Run Bulk Analysis', 'oneclick-seo-pro'); ?>
            </button>
        </div>

        <div class="bulk-progress" style="display: none;">
            <div class="progress-info">
                <span class="progress-text"><?php esc_html_e('Analyzing...', 'oneclick-seo-pro'); ?></span>
                <span class="progress-count">0/0</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
        </div>

        <table class="wp-list-table widefat striped" id="bulk-aeo-table" style="display: none;">
            <thead>
                <tr>
                    <th><?php esc_html_e('Title', 'oneclick-seo-pro'); ?></th>
                    <th><?php esc_html_e('AEO Score', 'oneclick-seo-pro'); ?></th>
                    <th><?php esc_html_e('FAQ', 'oneclick-seo-pro'); ?></th>
                    <th><?php esc_html_e('Snippet', 'oneclick-seo-pro'); ?></th>
                    <th><?php esc_html_e('Conversational', 'oneclick-seo-pro'); ?></th>
                    <th><?php esc_html_e('Actions', 'oneclick-seo-pro'); ?></th>
                </tr>
            </thead>
            <tbody id="bulk-aeo-results"></tbody>
        </table>
    </div>
</div>

<!-- FAQ Item Template -->
<script type="text/template" id="faq-item-template">
<div class="faq-item" data-index="{{index}}">
    <div class="faq-item-header">
        <span class="faq-number">Q{{number}}</span>
        <button type="button" class="faq-remove" title="<?php esc_attr_e('Remove', 'oneclick-seo-pro'); ?>">&times;</button>
    </div>
    <div class="faq-fields">
        <div class="faq-field">
            <label><?php esc_html_e('Question', 'oneclick-seo-pro'); ?></label>
            <input type="text" class="faq-question large-text" value="{{question}}">
        </div>
        <div class="faq-field">
            <label><?php esc_html_e('Answer', 'oneclick-seo-pro'); ?></label>
            <textarea class="faq-answer large-text" rows="3">{{answer}}</textarea>
        </div>
    </div>
</div>
</script>

<script>
jQuery(document).ready(function($) {
    // Load posts for all selects
    loadPostsForSelect();

    function loadPostsForSelect() {
        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_get_posts_simple',
            nonce: wpBulkSeo.nonce
        }).done(function(response) {
            if (response.success) {
                var options = '<option value=""><?php esc_html_e('Select a post...', 'oneclick-seo-pro'); ?></option>';
                response.data.forEach(function(post) {
                    options += '<option value="' + post.id + '">' + post.title + ' (' + post.type + ')</option>';
                });
                $('#aeo-post-select, #faq-post-select, #snippet-post-select').html(options);
            }
        });
    }

    // Enable buttons when post selected
    $('#aeo-post-select').on('change', function() {
        $('#analyze-aeo').prop('disabled', !$(this).val());
    });
    $('#faq-post-select').on('change', function() {
        $('#generate-faq').prop('disabled', !$(this).val());
    });
    $('#snippet-post-select').on('change', function() {
        $('#analyze-snippets').prop('disabled', !$(this).val());
    });

    // AEO Analysis
    $('#analyze-aeo').on('click', function() {
        var $btn = $(this);
        var postId = $('#aeo-post-select').val();

        if (!postId) return;

        $btn.prop('disabled', true);
        $btn.find('.dashicons').addClass('spin');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_analyze_aeo',
            nonce: wpBulkSeo.nonce,
            post_id: postId
        }).done(function(response) {
            if (response.success) {
                displayAEOResults(response.data);
            } else {
                alert(response.data.message || '<?php esc_html_e('Analysis failed', 'oneclick-seo-pro'); ?>');
            }
        }).always(function() {
            $btn.prop('disabled', false);
            $btn.find('.dashicons').removeClass('spin');
        });
    });

    function displayAEOResults(data) {
        $('#analyzed-post-title').text(data.post_title);
        $('#overall-aeo-score').text(data.overall_score);
        $('#aeo-grade').text(data.grade).attr('class', 'score-grade grade-' + data.grade.toLowerCase());

        // Update overview scores
        $('#conversational-score').text(data.factors.conversational_tone || '--');
        $('#faq-score').text(data.factors.faq_structure || '--');
        $('#snippet-score').text(data.factors.snippet_potential || '--');
        $('#citation-score').text(data.factors.citations || '--');

        // Display factors
        var factorsHtml = '';
        for (var factor in data.factors) {
            var score = data.factors[factor];
            var scoreClass = score >= 80 ? 'good' : (score >= 60 ? 'ok' : 'poor');
            factorsHtml += '<div class="factor-item">' +
                '<span class="factor-name">' + formatFactorName(factor) + '</span>' +
                '<div class="factor-bar"><div class="factor-fill ' + scoreClass + '" style="width: ' + score + '%"></div></div>' +
                '<span class="factor-score">' + score + '</span>' +
            '</div>';
        }
        $('#aeo-factors').html(factorsHtml);

        // Display recommendations
        var recsHtml = '';
        if (data.recommendations && data.recommendations.length) {
            data.recommendations.forEach(function(rec) {
                recsHtml += '<li class="rec-' + rec.priority + '">' +
                    '<span class="rec-icon"></span>' +
                    '<span class="rec-text">' + rec.message + '</span>' +
                '</li>';
            });
        } else {
            recsHtml = '<li class="no-recs"><?php esc_html_e('Great job! No major improvements needed.', 'oneclick-seo-pro'); ?></li>';
        }
        $('#aeo-recommendations').html(recsHtml);

        $('.aeo-results').show();
    }

    function formatFactorName(factor) {
        return factor.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
    }

    // FAQ Generator
    var faqItems = [];

    $('#generate-faq').on('click', function() {
        var $btn = $(this);
        var postId = $('#faq-post-select').val();

        if (!postId) return;

        $btn.prop('disabled', true);
        $btn.find('.dashicons').addClass('spin');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_generate_faq',
            nonce: wpBulkSeo.nonce,
            post_id: postId,
            extract_existing: $('#faq-extract-existing').is(':checked') ? 1 : 0,
            generate_ai: $('#faq-generate-ai').is(':checked') ? 1 : 0
        }).done(function(response) {
            if (response.success) {
                faqItems = response.data.faqs || [];
                renderFAQItems();
                $('.faq-preview').show();
            } else {
                alert(response.data.message || '<?php esc_html_e('Generation failed', 'oneclick-seo-pro'); ?>');
            }
        }).always(function() {
            $btn.prop('disabled', false);
            $btn.find('.dashicons').removeClass('spin');
        });
    });

    function renderFAQItems() {
        var template = $('#faq-item-template').html();
        var html = '';

        faqItems.forEach(function(item, index) {
            html += template
                .replace(/{{index}}/g, index)
                .replace(/{{number}}/g, index + 1)
                .replace(/{{question}}/g, escapeHtml(item.question || ''))
                .replace(/{{answer}}/g, escapeHtml(item.answer || ''));
        });

        $('#faq-items').html(html);
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    $('#add-faq-item').on('click', function() {
        faqItems.push({ question: '', answer: '' });
        renderFAQItems();
    });

    $(document).on('click', '.faq-remove', function() {
        var index = $(this).closest('.faq-item').data('index');
        faqItems.splice(index, 1);
        renderFAQItems();
    });

    $(document).on('input', '.faq-question, .faq-answer', function() {
        var $item = $(this).closest('.faq-item');
        var index = $item.data('index');

        faqItems[index] = {
            question: $item.find('.faq-question').val(),
            answer: $item.find('.faq-answer').val()
        };
    });

    $('#save-faq').on('click', function() {
        var $btn = $(this);
        var postId = $('#faq-post-select').val();

        // Validate
        var validFaqs = faqItems.filter(function(item) {
            return item.question && item.answer;
        });

        if (validFaqs.length === 0) {
            alert('<?php esc_html_e('Please add at least one FAQ with both question and answer.', 'oneclick-seo-pro'); ?>');
            return;
        }

        $btn.prop('disabled', true);

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_save_faq',
            nonce: wpBulkSeo.nonce,
            post_id: postId,
            faqs: JSON.stringify(validFaqs)
        }).done(function(response) {
            if (response.success) {
                alert('<?php esc_html_e('FAQ schema saved successfully!', 'oneclick-seo-pro'); ?>');
            } else {
                alert(response.data.message || '<?php esc_html_e('Save failed', 'oneclick-seo-pro'); ?>');
            }
        }).always(function() {
            $btn.prop('disabled', false);
        });
    });

    // Snippet Analyzer
    $('#analyze-snippets').on('click', function() {
        var $btn = $(this);
        var postId = $('#snippet-post-select').val();

        if (!postId) return;

        $btn.prop('disabled', true);
        $btn.find('.dashicons').addClass('spin');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_analyze_snippets',
            nonce: wpBulkSeo.nonce,
            post_id: postId
        }).done(function(response) {
            if (response.success) {
                displaySnippetResults(response.data);
            } else {
                alert(response.data.message || '<?php esc_html_e('Analysis failed', 'oneclick-seo-pro'); ?>');
            }
        }).always(function() {
            $btn.prop('disabled', false);
            $btn.find('.dashicons').removeClass('spin');
        });
    });

    function displaySnippetResults(data) {
        // Update potentials
        $('#paragraph-potential').text(data.paragraph || '--').attr('class', 'snippet-potential ' + getPotentialClass(data.paragraph));
        $('#list-potential').text(data.list || '--').attr('class', 'snippet-potential ' + getPotentialClass(data.list));
        $('#table-potential').text(data.table || '--').attr('class', 'snippet-potential ' + getPotentialClass(data.table));
        $('#steps-potential').text(data.steps || '--').attr('class', 'snippet-potential ' + getPotentialClass(data.steps));

        // Display suggestions
        var suggestionsHtml = '';
        if (data.suggestions && data.suggestions.length) {
            suggestionsHtml = '<h4><?php esc_html_e('Improvement Suggestions', 'oneclick-seo-pro'); ?></h4><ul>';
            data.suggestions.forEach(function(suggestion) {
                suggestionsHtml += '<li>' + suggestion + '</li>';
            });
            suggestionsHtml += '</ul>';
        }
        $('#snippet-suggestions').html(suggestionsHtml);

        $('.snippet-results').show();
    }

    function getPotentialClass(score) {
        if (!score || score === '--') return '';
        var num = parseInt(score);
        if (num >= 70) return 'high';
        if (num >= 40) return 'medium';
        return 'low';
    }

    // Bulk AEO Analysis
    $('#run-bulk-aeo').on('click', function() {
        var $btn = $(this);
        var postType = $('#bulk-post-type').val();

        $btn.prop('disabled', true);
        $('.bulk-progress').show();
        $('#bulk-aeo-table').show();
        $('#bulk-aeo-results').empty();

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_get_posts_for_aeo',
            nonce: wpBulkSeo.nonce,
            post_type: postType
        }).done(function(response) {
            if (response.success && response.data.length) {
                runBulkAEOAnalysis(response.data);
            } else {
                alert('<?php esc_html_e('No posts found', 'oneclick-seo-pro'); ?>');
                $btn.prop('disabled', false);
                $('.bulk-progress').hide();
            }
        });
    });

    function runBulkAEOAnalysis(posts) {
        var total = posts.length;
        var processed = 0;
        var queue = posts.slice();

        function processNext() {
            if (queue.length === 0) {
                $('.bulk-progress').hide();
                $('#run-bulk-aeo').prop('disabled', false);
                return;
            }

            var post = queue.shift();

            $.post(wpBulkSeo.ajaxUrl, {
                action: 'oneclick_seo_pro_analyze_aeo',
                nonce: wpBulkSeo.nonce,
                post_id: post.id
            }).done(function(response) {
                if (response.success) {
                    addBulkResultRow(post, response.data);
                }
            }).always(function() {
                processed++;
                var percent = Math.round((processed / total) * 100);
                $('.progress-count').text(processed + '/' + total);
                $('.progress-fill').css('width', percent + '%');
                processNext();
            });
        }

        processNext();
    }

    function addBulkResultRow(post, data) {
        var gradeClass = 'grade-' + (data.grade || 'f').toLowerCase();
        var row = '<tr>' +
            '<td><a href="' + post.edit_link + '" target="_blank">' + post.title + '</a></td>' +
            '<td><span class="score-badge ' + gradeClass + '">' + (data.overall_score || '--') + '</span></td>' +
            '<td>' + (data.factors.faq_structure || '--') + '</td>' +
            '<td>' + (data.factors.snippet_potential || '--') + '</td>' +
            '<td>' + (data.factors.conversational_tone || '--') + '</td>' +
            '<td>' +
                '<button type="button" class="button button-small view-aeo-details" data-post-id="' + post.id + '"><?php esc_html_e('Details', 'oneclick-seo-pro'); ?></button>' +
            '</td>' +
        '</tr>';
        $('#bulk-aeo-results').append(row);
    }

    $(document).on('click', '.view-aeo-details', function() {
        var postId = $(this).data('post-id');
        $('#aeo-post-select').val(postId).trigger('change');
        $('#analyze-aeo').trigger('click');
        $('html, body').animate({ scrollTop: $('.aeo-analyzer').offset().top - 50 }, 500);
    });
});
</script>
