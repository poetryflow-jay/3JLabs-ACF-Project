<?php
/**
 * WP Bulk SEO - Bulk Optimizer View
 *
 * @package WP_Bulk_SEO
 * @subpackage Admin/Views
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$posts_data = $posts;
$posts = $posts_data['posts'] ?? [];
$total = $posts_data['total'] ?? 0;
$paged = $posts_data['paged'] ?? 1;
$total_pages = $posts_data['total_pages'] ?? 1;
?>
<div class="wrap wp-bulk-seo-optimizer">
    <h1>
        <?php esc_html_e('Bulk SEO Optimizer', 'wp-bulk-seo'); ?>
        <span class="version-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 12px; margin-left: 10px;">
            v2.0.0
        </span>
    </h1>
    <p class="description">
        <?php esc_html_e('여러 콘텐츠를 한 번에 분석하고 자동으로 최적화합니다.', 'wp-bulk-seo'); ?>
    </p>

    <!-- [v2.0.0] Auto Optimize Settings -->
    <div class="optimizer-settings-card" style="
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin: 20px 0;
    ">
        <h2 style="margin-top: 0;">⚡ 자동 최적화 설정</h2>
        <div class="optimization-options" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <label style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="opt-title" checked>
                <span><?php esc_html_e('제목 최적화', 'wp-bulk-seo'); ?></span>
            </label>
            <label style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="opt-meta" checked>
                <span><?php esc_html_e('메타 설명 최적화', 'wp-bulk-seo'); ?></span>
            </label>
            <label style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="opt-schema">
                <span><?php esc_html_e('Schema 마크업 추가', 'wp-bulk-seo'); ?></span>
            </label>
            <label style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="opt-keywords" checked>
                <span><?php esc_html_e('키워드 최적화', 'wp-bulk-seo'); ?></span>
            </label>
            <label style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="opt-use-ai" checked>
                <span><?php esc_html_e('AI 사용', 'wp-bulk-seo'); ?></span>
            </label>
        </div>
    </div>

    <!-- Filters -->
    <div class="optimizer-filters" style="
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        margin: 20px 0;
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    ">
        <select id="filter-post-type" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
            <option value="all"><?php esc_html_e('모든 타입', 'wp-bulk-seo'); ?></option>
            <option value="post"><?php esc_html_e('포스트', 'wp-bulk-seo'); ?></option>
            <option value="page"><?php esc_html_e('페이지', 'wp-bulk-seo'); ?></option>
        </select>

        <select id="filter-grade" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
            <option value="all"><?php esc_html_e('모든 등급', 'wp-bulk-seo'); ?></option>
            <option value="f"><?php esc_html_e('F 등급만', 'wp-bulk-seo'); ?></option>
            <option value="d"><?php esc_html_e('D 등급만', 'wp-bulk-seo'); ?></option>
            <option value="c"><?php esc_html_e('C 등급만', 'wp-bulk-seo'); ?></option>
        </select>

        <button type="button" class="button button-primary" id="bulk-analyze-btn">
            <?php esc_html_e('📊 선택 항목 분석', 'wp-bulk-seo'); ?>
        </button>
        <button type="button" class="button button-secondary" id="bulk-optimize-btn">
            <?php esc_html_e('⚡ 선택 항목 자동 최적화', 'wp-bulk-seo'); ?>
        </button>
        <button type="button" class="button" id="select-all-btn">
            <?php esc_html_e('전체 선택', 'wp-bulk-seo'); ?>
        </button>
    </div>

    <!-- Posts Table -->
    <div class="optimizer-table-card" style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 30px;">
                        <input type="checkbox" id="select-all-checkbox">
                    </th>
                    <th><?php esc_html_e('제목', 'wp-bulk-seo'); ?></th>
                    <th style="width: 100px;"><?php esc_html_e('타입', 'wp-bulk-seo'); ?></th>
                    <th style="width: 120px;"><?php esc_html_e('SEO 점수', 'wp-bulk-seo'); ?></th>
                    <th style="width: 80px;"><?php esc_html_e('등급', 'wp-bulk-seo'); ?></th>
                    <th style="width: 150px;"><?php esc_html_e('분석일', 'wp-bulk-seo'); ?></th>
                    <th style="width: 200px;"><?php esc_html_e('작업', 'wp-bulk-seo'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                    <tr data-post-id="<?php echo esc_attr($post->ID); ?>">
                        <td>
                            <input type="checkbox" class="post-checkbox" value="<?php echo esc_attr($post->ID); ?>">
                        </td>
                        <td>
                            <strong>
                                <a href="<?php echo esc_url(get_edit_post_link($post->ID)); ?>">
                                    <?php echo esc_html($post->post_title ?: __('(no title)', 'wp-bulk-seo')); ?>
                                </a>
                            </strong>
                        </td>
                        <td>
                            <span class="post-type-badge" style="
                                background: #f0f0f1;
                                padding: 4px 8px;
                                border-radius: 4px;
                                font-size: 11px;
                                text-transform: uppercase;
                            ">
                                <?php echo esc_html($post->post_type); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (isset($post->overall_score)): ?>
                                <div class="score-bar" style="
                                    position: relative;
                                    width: 100px;
                                    height: 20px;
                                    background: #f0f0f1;
                                    border-radius: 10px;
                                    overflow: hidden;
                                ">
                                    <div class="score-fill" style="
                                        position: absolute;
                                        left: 0;
                                        top: 0;
                                        height: 100%;
                                        width: <?php echo esc_attr($post->overall_score); ?>%;
                                        background: <?php
                                            $score = (float) $post->overall_score;
                                            if ($score >= 80) echo '#46b450';
                                            elseif ($score >= 60) echo '#00a0d2';
                                            elseif ($score >= 40) echo '#ffb900';
                                            else echo '#dc3232';
                                        ?>;
                                        transition: width 0.3s;
                                    "></div>
                                    <span class="score-text" style="
                                        position: absolute;
                                        left: 50%;
                                        top: 50%;
                                        transform: translate(-50%, -50%);
                                        font-size: 11px;
                                        font-weight: 600;
                                        color: <?php echo $score >= 50 ? '#fff' : '#333'; ?>;
                                        z-index: 1;
                                    ">
                                        <?php echo esc_html($post->overall_score); ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <span style="color: #999;"><?php esc_html_e('미분석', 'wp-bulk-seo'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($post->grade)): ?>
                                <span class="grade-badge grade-<?php echo esc_attr(strtolower($post->grade)); ?>" style="
                                    display: inline-block;
                                    padding: 4px 10px;
                                    border-radius: 4px;
                                    font-weight: 600;
                                    font-size: 12px;
                                    <?php
                                    $grade_colors = [
                                        'a+' => ['bg' => '#00a32a', 'color' => '#fff'],
                                        'a' => ['bg' => '#46b450', 'color' => '#fff'],
                                        'b' => ['bg' => '#00a0d2', 'color' => '#fff'],
                                        'c' => ['bg' => '#ffb900', 'color' => '#333'],
                                        'd' => ['bg' => '#dc3232', 'color' => '#fff'],
                                        'f' => ['bg' => '#8b0000', 'color' => '#fff'],
                                    ];
                                    $grade = strtolower($post->grade);
                                    $colors = $grade_colors[$grade] ?? ['bg' => '#999', 'color' => '#fff'];
                                    echo 'background: ' . $colors['bg'] . '; color: ' . $colors['color'] . ';';
                                    ?>
                                ">
                                    <?php echo esc_html($post->grade); ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($post->analyzed_at)): ?>
                                <?php echo esc_html(human_time_diff(strtotime($post->analyzed_at)) . ' 전'); ?>
                            <?php else: ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button type="button" class="button button-small analyze-single" data-post-id="<?php echo esc_attr($post->ID); ?>">
                                    <?php esc_html_e('분석', 'wp-bulk-seo'); ?>
                                </button>
                                <button type="button" class="button button-small optimize-single" data-post-id="<?php echo esc_attr($post->ID); ?>">
                                    <?php esc_html_e('최적화', 'wp-bulk-seo'); ?>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #666;">
                            <?php esc_html_e('최적화할 콘텐츠가 없습니다.', 'wp-bulk-seo'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="tablenav bottom" style="margin-top: 20px;">
            <div class="tablenav-pages">
                <?php
                echo paginate_links([
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total' => $total_pages,
                    'current' => $paged,
                ]);
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- [v2.0.0] Optimization Progress Modal -->
    <div id="optimization-progress-modal" style="
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        z-index: 999999;
        align-items: center;
        justify-content: center;
    ">
        <div style="
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        ">
            <h2 style="margin-top: 0;"><?php esc_html_e('최적화 진행 중...', 'wp-bulk-seo'); ?></h2>
            <div class="progress-bar-container" style="
                width: 100%;
                height: 30px;
                background: #f0f0f1;
                border-radius: 15px;
                overflow: hidden;
                margin: 20px 0;
            ">
                <div class="progress-bar-fill" style="
                    height: 100%;
                    width: 0%;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    transition: width 0.3s;
                "></div>
            </div>
            <div class="progress-text" style="text-align: center; color: #666; margin-bottom: 20px;">
                <span class="current">0</span> / <span class="total">0</span>
            </div>
            <div class="progress-details" style="
                max-height: 200px;
                overflow-y: auto;
                background: #f9f9f9;
                padding: 15px;
                border-radius: 6px;
                font-size: 13px;
            ">
                <div class="progress-items"></div>
            </div>
            <button type="button" class="button button-secondary" id="cancel-optimization" style="margin-top: 20px; width: 100%;">
                <?php esc_html_e('취소', 'wp-bulk-seo'); ?>
            </button>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var selectedPosts = [];
    var isOptimizing = false;

    // Select all checkbox
    $('#select-all-checkbox, #select-all-btn').on('click', function() {
        var checked = $('#select-all-checkbox').is(':checked');
        $('.post-checkbox').prop('checked', checked);
        updateSelectedPosts();
    });

    // Individual checkbox
    $('.post-checkbox').on('change', function() {
        updateSelectedPosts();
        $('#select-all-checkbox').prop('checked', $('.post-checkbox:checked').length === $('.post-checkbox').length);
    });

    function updateSelectedPosts() {
        selectedPosts = $('.post-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();
    }

    // Bulk analyze
    $('#bulk-analyze-btn').on('click', function() {
        if (selectedPosts.length === 0) {
            alert('<?php esc_html_e('분석할 항목을 선택하세요.', 'wp-bulk-seo'); ?>');
            return;
        }

        if (!confirm('<?php esc_html_e('선택한 항목을 분석하시겠습니까?', 'wp-bulk-seo'); ?>')) {
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php esc_html_e('분석 중...', 'wp-bulk-seo'); ?>');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'wp_bulk_seo_bulk_analyze',
            nonce: wpBulkSeo.nonce,
            post_ids: selectedPosts
        }).done(function(response) {
            if (response.success) {
                alert('<?php esc_html_e('분석 완료:', 'wp-bulk-seo'); ?> ' + response.data.analyzed + '개');
                location.reload();
            } else {
                alert('<?php esc_html_e('오류:', 'wp-bulk-seo'); ?> ' + (response.data.message || '<?php esc_html_e('알 수 없는 오류', 'wp-bulk-seo'); ?>'));
            }
        }).fail(function() {
            alert('<?php esc_html_e('서버 통신 오류', 'wp-bulk-seo'); ?>');
        }).always(function() {
            $btn.prop('disabled', false).text('<?php esc_html_e('📊 선택 항목 분석', 'wp-bulk-seo'); ?>');
        });
    });

    // Bulk optimize
    $('#bulk-optimize-btn').on('click', function() {
        if (selectedPosts.length === 0) {
            alert('<?php esc_html_e('최적화할 항목을 선택하세요.', 'wp-bulk-seo'); ?>');
            return;
        }

        if (!confirm('<?php esc_html_e('선택한 항목을 자동으로 최적화하시겠습니까?', 'wp-bulk-seo'); ?>')) {
            return;
        }

        var options = {
            optimize_title: $('#opt-title').is(':checked'),
            optimize_meta: $('#opt-meta').is(':checked'),
            add_schema: $('#opt-schema').is(':checked'),
            optimize_keywords: $('#opt-keywords').is(':checked'),
            use_ai: $('#opt-use-ai').is(':checked'),
        };

        startBulkOptimization(selectedPosts, options);
    });

    // Single analyze
    $('.analyze-single').on('click', function() {
        var postId = $(this).data('post-id');
        var $btn = $(this);

        $btn.prop('disabled', true).text('<?php esc_html_e('분석 중...', 'wp-bulk-seo'); ?>');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'wp_bulk_seo_analyze',
            nonce: wpBulkSeo.nonce,
            post_id: postId
        }).done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('<?php esc_html_e('오류:', 'wp-bulk-seo'); ?> ' + (response.data.message || '<?php esc_html_e('알 수 없는 오류', 'wp-bulk-seo'); ?>'));
            }
        }).always(function() {
            $btn.prop('disabled', false).text('<?php esc_html_e('분석', 'wp-bulk-seo'); ?>');
        });
    });

    // Single optimize
    $('.optimize-single').on('click', function() {
        var postId = $(this).data('post-id');
        var $btn = $(this);

        if (!confirm('<?php esc_html_e('이 포스트를 자동으로 최적화하시겠습니까?', 'wp-bulk-seo'); ?>')) {
            return;
        }

        var options = {
            optimize_title: $('#opt-title').is(':checked'),
            optimize_meta: $('#opt-meta').is(':checked'),
            add_schema: $('#opt-schema').is(':checked'),
            optimize_keywords: $('#opt-keywords').is(':checked'),
            use_ai: $('#opt-use-ai').is(':checked'),
        };

        $btn.prop('disabled', true).text('<?php esc_html_e('최적화 중...', 'wp-bulk-seo'); ?>');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'wp_bulk_seo_auto_optimize',
            nonce: wpBulkSeo.nonce,
            post_id: postId,
            options: options
        }).done(function(response) {
            if (response.success) {
                var result = response.data;
                var message = '<?php esc_html_e('최적화 완료!', 'wp-bulk-seo'); ?>\n';
                message += '<?php esc_html_e('점수:', 'wp-bulk-seo'); ?> ' + result.score_before + ' → ' + result.score_after;
                message += ' (' + (result.score_improvement > 0 ? '+' : '') + result.score_improvement + ')';
                alert(message);
                location.reload();
            } else {
                alert('<?php esc_html_e('오류:', 'wp-bulk-seo'); ?> ' + (response.data.error || '<?php esc_html_e('알 수 없는 오류', 'wp-bulk-seo'); ?>'));
            }
        }).always(function() {
            $btn.prop('disabled', false).text('<?php esc_html_e('최적화', 'wp-bulk-seo'); ?>');
        });
    });

    // Bulk optimization with progress
    function startBulkOptimization(postIds, options) {
        if (isOptimizing) return;
        isOptimizing = true;

        var total = postIds.length;
        var processed = 0;
        var success = 0;
        var failed = 0;

        $('#optimization-progress-modal').show();
        $('.progress-bar-fill').css('width', '0%');
        $('.progress-text .total').text(total);
        $('.progress-items').empty();

        function processNext() {
            if (processed >= total) {
                completeOptimization();
                return;
            }

            var postId = postIds[processed];
            var $item = $('<div class="progress-item" data-post-id="' + postId + '">처리 중: ' + postId + '...</div>');
            $('.progress-items').append($item);

            $.post(wpBulkSeo.ajaxUrl, {
                action: 'wp_bulk_seo_auto_optimize',
                nonce: wpBulkSeo.nonce,
                post_id: postId,
                options: options
            }).done(function(response) {
                processed++;
                if (response.success) {
                    success++;
                    $item.html('✓ 완료: ' + postId + ' (점수: ' + response.data.score_before + ' → ' + response.data.score_after + ')').css('color', '#46b450');
                } else {
                    failed++;
                    $item.html('✗ 실패: ' + postId).css('color', '#dc3232');
                }
            }).fail(function() {
                processed++;
                failed++;
                $item.html('✗ 오류: ' + postId).css('color', '#dc3232');
            }).always(function() {
                var progress = (processed / total) * 100;
                $('.progress-bar-fill').css('width', progress + '%');
                $('.progress-text .current').text(processed);

                // Continue with next
                setTimeout(processNext, 500);
            });
        }

        function completeOptimization() {
            isOptimizing = false;
            $('.progress-items').append('<div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd; font-weight: 600;">완료: ' + success + '개 성공, ' + failed + '개 실패</div>');
            
            setTimeout(function() {
                $('#optimization-progress-modal').hide();
                location.reload();
            }, 2000);
        }

        $('#cancel-optimization').on('click', function() {
            if (confirm('<?php esc_html_e('최적화를 취소하시겠습니까?', 'wp-bulk-seo'); ?>')) {
                isOptimizing = false;
                $('#optimization-progress-modal').hide();
            }
        });

        processNext();
    }
});
</script>
