<?php
/**
 * WP Bulk SEO - Optimizer View
 *
 * @package OneClick_SEO_Pro
 * @subpackage Admin/Views
 */

if (!defined('ABSPATH')) {
    exit;
}

$general_settings = get_option('oneclick_seo_pro_general', []);
$selected_post_types = $general_settings['post_types'] ?? ['post', 'page'];
?>
<div class="wrap oneclick-seo-pro-optimizer">
    <h1><?php esc_html_e('Bulk SEO Optimizer', 'oneclick-seo-pro'); ?></h1>

    <!-- Filter Section -->
    <div class="optimizer-filters">
        <div class="filter-row">
            <div class="filter-group">
                <label for="filter-post-type"><?php esc_html_e('Post Type', 'oneclick-seo-pro'); ?></label>
                <select id="filter-post-type" class="filter-select">
                    <option value=""><?php esc_html_e('All Types', 'oneclick-seo-pro'); ?></option>
                    <?php
                    $post_types = get_post_types(['public' => true], 'objects');
                    foreach ($post_types as $type):
                        if ($type->name === 'attachment') continue;
                        if (!in_array($type->name, $selected_post_types)) continue;
                    ?>
                    <option value="<?php echo esc_attr($type->name); ?>"><?php echo esc_html($type->labels->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-grade"><?php esc_html_e('SEO Grade', 'oneclick-seo-pro'); ?></label>
                <select id="filter-grade" class="filter-select">
                    <option value=""><?php esc_html_e('All Grades', 'oneclick-seo-pro'); ?></option>
                    <option value="A+"><?php esc_html_e('A+ (90-100)', 'oneclick-seo-pro'); ?></option>
                    <option value="A"><?php esc_html_e('A (80-89)', 'oneclick-seo-pro'); ?></option>
                    <option value="B"><?php esc_html_e('B (70-79)', 'oneclick-seo-pro'); ?></option>
                    <option value="C"><?php esc_html_e('C (60-69)', 'oneclick-seo-pro'); ?></option>
                    <option value="D"><?php esc_html_e('D (50-59)', 'oneclick-seo-pro'); ?></option>
                    <option value="F"><?php esc_html_e('F (0-49)', 'oneclick-seo-pro'); ?></option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-status"><?php esc_html_e('Analysis Status', 'oneclick-seo-pro'); ?></label>
                <select id="filter-status" class="filter-select">
                    <option value=""><?php esc_html_e('All', 'oneclick-seo-pro'); ?></option>
                    <option value="analyzed"><?php esc_html_e('Analyzed', 'oneclick-seo-pro'); ?></option>
                    <option value="not_analyzed"><?php esc_html_e('Not Analyzed', 'oneclick-seo-pro'); ?></option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-issue"><?php esc_html_e('Has Issue', 'oneclick-seo-pro'); ?></label>
                <select id="filter-issue" class="filter-select">
                    <option value=""><?php esc_html_e('All', 'oneclick-seo-pro'); ?></option>
                    <option value="missing_title"><?php esc_html_e('Missing Title', 'oneclick-seo-pro'); ?></option>
                    <option value="missing_description"><?php esc_html_e('Missing Description', 'oneclick-seo-pro'); ?></option>
                    <option value="short_content"><?php esc_html_e('Short Content', 'oneclick-seo-pro'); ?></option>
                    <option value="missing_images"><?php esc_html_e('Missing Images', 'oneclick-seo-pro'); ?></option>
                    <option value="missing_alt"><?php esc_html_e('Missing Alt Tags', 'oneclick-seo-pro'); ?></option>
                    <option value="no_internal_links"><?php esc_html_e('No Internal Links', 'oneclick-seo-pro'); ?></option>
                    <option value="no_external_links"><?php esc_html_e('No External Links', 'oneclick-seo-pro'); ?></option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="button" id="apply-filters" class="button button-primary">
                    <?php esc_html_e('Apply Filters', 'oneclick-seo-pro'); ?>
                </button>
                <button type="button" id="reset-filters" class="button">
                    <?php esc_html_e('Reset', 'oneclick-seo-pro'); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions-bar">
        <div class="bulk-select">
            <label>
                <input type="checkbox" id="select-all-posts">
                <?php esc_html_e('Select All', 'oneclick-seo-pro'); ?>
            </label>
            <span class="selected-count">0 <?php esc_html_e('selected', 'oneclick-seo-pro'); ?></span>
        </div>

        <div class="bulk-action-buttons">
            <button type="button" id="bulk-analyze" class="button" disabled>
                <span class="dashicons dashicons-search"></span>
                <?php esc_html_e('Analyze Selected', 'oneclick-seo-pro'); ?>
            </button>
            <button type="button" id="bulk-optimize-titles" class="button" disabled>
                <span class="dashicons dashicons-edit"></span>
                <?php esc_html_e('Optimize Titles', 'oneclick-seo-pro'); ?>
            </button>
            <button type="button" id="bulk-optimize-descriptions" class="button" disabled>
                <span class="dashicons dashicons-editor-alignleft"></span>
                <?php esc_html_e('Optimize Descriptions', 'oneclick-seo-pro'); ?>
            </button>
            <button type="button" id="bulk-generate-schema" class="button" disabled>
                <span class="dashicons dashicons-code-standards"></span>
                <?php esc_html_e('Generate Schema', 'oneclick-seo-pro'); ?>
            </button>
        </div>
    </div>

    <!-- Progress Bar (hidden by default) -->
    <div class="bulk-progress" style="display: none;">
        <div class="progress-info">
            <span class="progress-text"><?php esc_html_e('Processing...', 'oneclick-seo-pro'); ?></span>
            <span class="progress-count">0/0</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: 0%"></div>
        </div>
        <button type="button" id="cancel-bulk" class="button">
            <?php esc_html_e('Cancel', 'oneclick-seo-pro'); ?>
        </button>
    </div>

    <!-- Posts Table -->
    <div class="optimizer-table-wrapper">
        <table class="wp-list-table widefat striped optimizer-table">
            <thead>
                <tr>
                    <th class="check-column">
                        <input type="checkbox" id="select-all-table">
                    </th>
                    <th class="column-title"><?php esc_html_e('Title', 'oneclick-seo-pro'); ?></th>
                    <th class="column-type"><?php esc_html_e('Type', 'oneclick-seo-pro'); ?></th>
                    <th class="column-score"><?php esc_html_e('Score', 'oneclick-seo-pro'); ?></th>
                    <th class="column-issues"><?php esc_html_e('Issues', 'oneclick-seo-pro'); ?></th>
                    <th class="column-meta"><?php esc_html_e('Meta', 'oneclick-seo-pro'); ?></th>
                    <th class="column-actions"><?php esc_html_e('Actions', 'oneclick-seo-pro'); ?></th>
                </tr>
            </thead>
            <tbody id="posts-tbody">
                <tr class="loading-row">
                    <td colspan="7">
                        <span class="spinner is-active"></span>
                        <?php esc_html_e('Loading posts...', 'oneclick-seo-pro'); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="tablenav bottom">
        <div class="tablenav-pages">
            <span class="displaying-num"></span>
            <span class="pagination-links">
                <button type="button" class="button prev-page" disabled>&lsaquo;</button>
                <span class="paging-input">
                    <span class="current-page">1</span>
                    <?php esc_html_e('of', 'oneclick-seo-pro'); ?>
                    <span class="total-pages">1</span>
                </span>
                <button type="button" class="button next-page" disabled>&rsaquo;</button>
            </span>
        </div>
    </div>
</div>

<!-- Quick Edit Modal -->
<div id="quick-edit-modal" class="oneclick-seo-pro-modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2><?php esc_html_e('Quick Edit SEO', 'oneclick-seo-pro'); ?></h2>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="quick-edit-post-id">

            <div class="form-field">
                <label for="quick-edit-title"><?php esc_html_e('SEO Title', 'oneclick-seo-pro'); ?></label>
                <input type="text" id="quick-edit-title" class="large-text">
                <p class="description">
                    <span class="char-count">0</span>/60 <?php esc_html_e('characters', 'oneclick-seo-pro'); ?>
                </p>
            </div>

            <div class="form-field">
                <label for="quick-edit-description"><?php esc_html_e('Meta Description', 'oneclick-seo-pro'); ?></label>
                <textarea id="quick-edit-description" rows="3" class="large-text"></textarea>
                <p class="description">
                    <span class="char-count">0</span>/160 <?php esc_html_e('characters', 'oneclick-seo-pro'); ?>
                </p>
            </div>

            <div class="form-field">
                <label for="quick-edit-focus-keyword"><?php esc_html_e('Focus Keyword', 'oneclick-seo-pro'); ?></label>
                <input type="text" id="quick-edit-focus-keyword" class="regular-text">
            </div>

            <div class="ai-suggestions" style="display: none;">
                <h4><?php esc_html_e('AI Suggestions', 'oneclick-seo-pro'); ?></h4>
                <div class="suggestions-list"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="generate-ai-suggestions" class="button">
                <span class="dashicons dashicons-admin-comments"></span>
                <?php esc_html_e('Generate AI Suggestions', 'oneclick-seo-pro'); ?>
            </button>
            <button type="button" class="button modal-cancel"><?php esc_html_e('Cancel', 'oneclick-seo-pro'); ?></button>
            <button type="button" id="save-quick-edit" class="button button-primary"><?php esc_html_e('Save Changes', 'oneclick-seo-pro'); ?></button>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var currentPage = 1;
    var perPage = 20;
    var totalPosts = 0;
    var selectedPosts = [];
    var bulkProcessing = false;
    var cancelRequested = false;

    // Load posts on init
    loadPosts();

    // Filter handlers
    $('#apply-filters').on('click', function() {
        currentPage = 1;
        loadPosts();
    });

    $('#reset-filters').on('click', function() {
        $('.filter-select').val('');
        currentPage = 1;
        loadPosts();
    });

    // Pagination
    $('.prev-page').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadPosts();
        }
    });

    $('.next-page').on('click', function() {
        var totalPages = Math.ceil(totalPosts / perPage);
        if (currentPage < totalPages) {
            currentPage++;
            loadPosts();
        }
    });

    // Select all
    $('#select-all-posts, #select-all-table').on('change', function() {
        var checked = $(this).prop('checked');
        $('#select-all-posts, #select-all-table').prop('checked', checked);
        $('.post-checkbox').prop('checked', checked);
        updateSelectedCount();
    });

    $(document).on('change', '.post-checkbox', function() {
        updateSelectedCount();
    });

    function updateSelectedCount() {
        selectedPosts = [];
        $('.post-checkbox:checked').each(function() {
            selectedPosts.push($(this).val());
        });

        $('.selected-count').text(selectedPosts.length + ' <?php esc_html_e('selected', 'oneclick-seo-pro'); ?>');

        var hasSelection = selectedPosts.length > 0;
        $('#bulk-analyze, #bulk-optimize-titles, #bulk-optimize-descriptions, #bulk-generate-schema')
            .prop('disabled', !hasSelection);
    }

    // Load posts via AJAX
    function loadPosts() {
        var $tbody = $('#posts-tbody');
        $tbody.html('<tr class="loading-row"><td colspan="7"><span class="spinner is-active"></span> <?php esc_html_e('Loading posts...', 'oneclick-seo-pro'); ?></td></tr>');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_get_posts',
            nonce: wpBulkSeo.nonce,
            page: currentPage,
            per_page: perPage,
            post_type: $('#filter-post-type').val(),
            grade: $('#filter-grade').val(),
            status: $('#filter-status').val(),
            issue: $('#filter-issue').val()
        }).done(function(response) {
            if (response.success) {
                renderPosts(response.data.posts);
                totalPosts = response.data.total;
                updatePagination();
            } else {
                $tbody.html('<tr><td colspan="7"><?php esc_html_e('Error loading posts', 'oneclick-seo-pro'); ?></td></tr>');
            }
        }).fail(function() {
            $tbody.html('<tr><td colspan="7"><?php esc_html_e('Error loading posts', 'oneclick-seo-pro'); ?></td></tr>');
        });
    }

    function renderPosts(posts) {
        var $tbody = $('#posts-tbody');
        $tbody.empty();

        if (posts.length === 0) {
            $tbody.html('<tr><td colspan="7"><?php esc_html_e('No posts found', 'oneclick-seo-pro'); ?></td></tr>');
            return;
        }

        posts.forEach(function(post) {
            var issuesHtml = '';
            if (post.issues && post.issues.length > 0) {
                issuesHtml = '<ul class="issues-mini">';
                post.issues.slice(0, 3).forEach(function(issue) {
                    issuesHtml += '<li class="issue-' + issue.severity + '">' + issue.message + '</li>';
                });
                if (post.issues.length > 3) {
                    issuesHtml += '<li class="more">+' + (post.issues.length - 3) + ' more</li>';
                }
                issuesHtml += '</ul>';
            } else {
                issuesHtml = '<span class="no-issues"><?php esc_html_e('No issues', 'oneclick-seo-pro'); ?></span>';
            }

            var metaStatus = '';
            if (post.seo_title) {
                metaStatus += '<span class="meta-ok" title="<?php esc_attr_e('Title set', 'oneclick-seo-pro'); ?>">T</span>';
            } else {
                metaStatus += '<span class="meta-missing" title="<?php esc_attr_e('Title missing', 'oneclick-seo-pro'); ?>">T</span>';
            }
            if (post.meta_description) {
                metaStatus += '<span class="meta-ok" title="<?php esc_attr_e('Description set', 'oneclick-seo-pro'); ?>">D</span>';
            } else {
                metaStatus += '<span class="meta-missing" title="<?php esc_attr_e('Description missing', 'oneclick-seo-pro'); ?>">D</span>';
            }

            var scoreHtml = post.score !== null
                ? '<div class="score-badge score-' + post.grade.toLowerCase() + '">' + post.score + '</div>'
                : '<span class="not-analyzed"><?php esc_html_e('N/A', 'oneclick-seo-pro'); ?></span>';

            var row = '<tr data-post-id="' + post.id + '">' +
                '<td class="check-column"><input type="checkbox" class="post-checkbox" value="' + post.id + '"></td>' +
                '<td class="column-title"><a href="' + post.edit_link + '" target="_blank">' + post.title + '</a></td>' +
                '<td class="column-type">' + post.post_type + '</td>' +
                '<td class="column-score">' + scoreHtml + '</td>' +
                '<td class="column-issues">' + issuesHtml + '</td>' +
                '<td class="column-meta">' + metaStatus + '</td>' +
                '<td class="column-actions">' +
                    '<button type="button" class="button button-small analyze-single" data-post-id="' + post.id + '"><?php esc_html_e('Analyze', 'oneclick-seo-pro'); ?></button> ' +
                    '<button type="button" class="button button-small quick-edit-btn" data-post-id="' + post.id + '"><?php esc_html_e('Edit', 'oneclick-seo-pro'); ?></button>' +
                '</td>' +
            '</tr>';

            $tbody.append(row);
        });
    }

    function updatePagination() {
        var totalPages = Math.ceil(totalPosts / perPage);
        $('.displaying-num').text(totalPosts + ' <?php esc_html_e('items', 'oneclick-seo-pro'); ?>');
        $('.current-page').text(currentPage);
        $('.total-pages').text(totalPages);
        $('.prev-page').prop('disabled', currentPage <= 1);
        $('.next-page').prop('disabled', currentPage >= totalPages);
    }

    // Single analyze
    $(document).on('click', '.analyze-single', function() {
        var $btn = $(this);
        var postId = $btn.data('post-id');

        $btn.prop('disabled', true).text('<?php esc_html_e('Analyzing...', 'oneclick-seo-pro'); ?>');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_analyze',
            nonce: wpBulkSeo.nonce,
            post_id: postId
        }).done(function(response) {
            if (response.success) {
                loadPosts(); // Reload to show updated data
            } else {
                alert(response.data.message || '<?php esc_html_e('Analysis failed', 'oneclick-seo-pro'); ?>');
            }
        }).always(function() {
            $btn.prop('disabled', false).text('<?php esc_html_e('Analyze', 'oneclick-seo-pro'); ?>');
        });
    });

    // Bulk analyze
    $('#bulk-analyze').on('click', function() {
        if (selectedPosts.length === 0) return;
        runBulkAction('oneclick_seo_pro_bulk_analyze', selectedPosts, '<?php esc_html_e('Analyzing', 'oneclick-seo-pro'); ?>');
    });

    // Bulk optimize titles
    $('#bulk-optimize-titles').on('click', function() {
        if (selectedPosts.length === 0) return;
        if (!confirm('<?php esc_html_e('Generate AI-optimized titles for selected posts?', 'oneclick-seo-pro'); ?>')) return;
        runBulkAction('oneclick_seo_pro_bulk_optimize_titles', selectedPosts, '<?php esc_html_e('Optimizing titles', 'oneclick-seo-pro'); ?>');
    });

    // Bulk optimize descriptions
    $('#bulk-optimize-descriptions').on('click', function() {
        if (selectedPosts.length === 0) return;
        if (!confirm('<?php esc_html_e('Generate AI-optimized descriptions for selected posts?', 'oneclick-seo-pro'); ?>')) return;
        runBulkAction('oneclick_seo_pro_bulk_optimize_descriptions', selectedPosts, '<?php esc_html_e('Optimizing descriptions', 'oneclick-seo-pro'); ?>');
    });

    // Bulk generate schema
    $('#bulk-generate-schema').on('click', function() {
        if (selectedPosts.length === 0) return;
        runBulkAction('oneclick_seo_pro_bulk_generate_schema', selectedPosts, '<?php esc_html_e('Generating schema', 'oneclick-seo-pro'); ?>');
    });

    function runBulkAction(action, posts, progressText) {
        bulkProcessing = true;
        cancelRequested = false;

        $('.bulk-progress').show();
        $('.progress-text').text(progressText + '...');
        $('.progress-count').text('0/' + posts.length);
        $('.progress-fill').css('width', '0%');

        var processed = 0;
        var queue = posts.slice();

        function processNext() {
            if (cancelRequested || queue.length === 0) {
                bulkProcessing = false;
                $('.bulk-progress').hide();
                loadPosts();
                return;
            }

            var postId = queue.shift();

            $.post(wpBulkSeo.ajaxUrl, {
                action: action,
                nonce: wpBulkSeo.nonce,
                post_id: postId
            }).always(function() {
                processed++;
                var percent = Math.round((processed / posts.length) * 100);
                $('.progress-count').text(processed + '/' + posts.length);
                $('.progress-fill').css('width', percent + '%');
                processNext();
            });
        }

        processNext();
    }

    $('#cancel-bulk').on('click', function() {
        cancelRequested = true;
    });

    // Quick Edit Modal
    $(document).on('click', '.quick-edit-btn', function() {
        var postId = $(this).data('post-id');
        openQuickEdit(postId);
    });

    function openQuickEdit(postId) {
        $('#quick-edit-post-id').val(postId);
        $('#quick-edit-modal').show();

        // Load post SEO data
        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_get_post_seo',
            nonce: wpBulkSeo.nonce,
            post_id: postId
        }).done(function(response) {
            if (response.success) {
                $('#quick-edit-title').val(response.data.seo_title || '');
                $('#quick-edit-description').val(response.data.meta_description || '');
                $('#quick-edit-focus-keyword').val(response.data.focus_keyword || '');
                updateCharCounts();
            }
        });
    }

    $('.modal-close, .modal-cancel, .modal-overlay').on('click', function() {
        $('#quick-edit-modal').hide();
        $('.ai-suggestions').hide();
    });

    $('#quick-edit-title, #quick-edit-description').on('input', function() {
        updateCharCounts();
    });

    function updateCharCounts() {
        $('#quick-edit-title').siblings('.description').find('.char-count')
            .text($('#quick-edit-title').val().length);
        $('#quick-edit-description').siblings('.description').find('.char-count')
            .text($('#quick-edit-description').val().length);
    }

    // AI Suggestions
    $('#generate-ai-suggestions').on('click', function() {
        var $btn = $(this);
        var postId = $('#quick-edit-post-id').val();

        $btn.prop('disabled', true).find('.dashicons').addClass('spin');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_generate_suggestions',
            nonce: wpBulkSeo.nonce,
            post_id: postId
        }).done(function(response) {
            if (response.success) {
                var $list = $('.suggestions-list').empty();

                if (response.data.titles && response.data.titles.length) {
                    $list.append('<h5><?php esc_html_e('Title Suggestions', 'oneclick-seo-pro'); ?></h5>');
                    response.data.titles.forEach(function(title) {
                        $list.append('<div class="suggestion" data-type="title">' + title + '</div>');
                    });
                }

                if (response.data.descriptions && response.data.descriptions.length) {
                    $list.append('<h5><?php esc_html_e('Description Suggestions', 'oneclick-seo-pro'); ?></h5>');
                    response.data.descriptions.forEach(function(desc) {
                        $list.append('<div class="suggestion" data-type="description">' + desc + '</div>');
                    });
                }

                $('.ai-suggestions').show();
            } else {
                alert(response.data.message || '<?php esc_html_e('Failed to generate suggestions', 'oneclick-seo-pro'); ?>');
            }
        }).always(function() {
            $btn.prop('disabled', false).find('.dashicons').removeClass('spin');
        });
    });

    $(document).on('click', '.suggestion', function() {
        var type = $(this).data('type');
        var text = $(this).text();

        if (type === 'title') {
            $('#quick-edit-title').val(text);
        } else if (type === 'description') {
            $('#quick-edit-description').val(text);
        }
        updateCharCounts();
    });

    // Save Quick Edit
    $('#save-quick-edit').on('click', function() {
        var $btn = $(this);
        var postId = $('#quick-edit-post-id').val();

        $btn.prop('disabled', true).text('<?php esc_html_e('Saving...', 'oneclick-seo-pro'); ?>');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'oneclick_seo_pro_save_post_seo',
            nonce: wpBulkSeo.nonce,
            post_id: postId,
            seo_title: $('#quick-edit-title').val(),
            meta_description: $('#quick-edit-description').val(),
            focus_keyword: $('#quick-edit-focus-keyword').val()
        }).done(function(response) {
            if (response.success) {
                $('#quick-edit-modal').hide();
                loadPosts();
            } else {
                alert(response.data.message || '<?php esc_html_e('Failed to save', 'oneclick-seo-pro'); ?>');
            }
        }).always(function() {
            $btn.prop('disabled', false).text('<?php esc_html_e('Save Changes', 'oneclick-seo-pro'); ?>');
        });
    });
});
</script>
