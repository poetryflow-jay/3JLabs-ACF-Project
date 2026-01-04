<?php
/**
 * WP Bulk SEO - Keywords Management View
 *
 * @package WP_Bulk_SEO
 * @subpackage Admin/Views
 * @version 2.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
$post = $post_id ? get_post($post_id) : null;
?>
<div class="wrap wp-bulk-seo-keywords">
    <h1>
        <?php esc_html_e('🔑 키워드 관리', 'wp-bulk-seo'); ?>
        <span class="version-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 12px; margin-left: 10px;">
            v2.1.0
        </span>
    </h1>
    <p class="description">
        <?php esc_html_e('콘텐츠에서 키워드를 자동 추출하고, 데이터베이스 알고리즘 기반으로 최적의 키워드를 추천받으세요.', 'wp-bulk-seo'); ?>
    </p>

    <!-- Post Selector -->
    <div class="keyword-post-selector" style="
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin: 20px 0;
    ">
        <h2 style="margin-top: 0;"><?php esc_html_e('포스트 선택', 'wp-bulk-seo'); ?></h2>
        <div style="display: flex; gap: 15px; align-items: center;">
            <select id="keyword-post-select" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                <option value=""><?php esc_html_e('포스트를 선택하세요...', 'wp-bulk-seo'); ?></option>
                <?php
                $posts = get_posts([
                    'post_type' => ['post', 'page'],
                    'post_status' => 'publish',
                    'posts_per_page' => 100,
                    'orderby' => 'date',
                    'order' => 'DESC',
                ]);

                foreach ($posts as $p) {
                    $selected = ($post_id === $p->ID) ? 'selected' : '';
                    echo '<option value="' . esc_attr($p->ID) . '" ' . $selected . '>' . esc_html($p->post_title) . '</option>';
                }
                ?>
            </select>
            <button type="button" class="button button-primary" id="load-post-keywords">
                <?php esc_html_e('로드', 'wp-bulk-seo'); ?>
            </button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="keywords-main-content" style="display: <?php echo $post_id ? 'block' : 'none'; ?>;">
        <!-- Tabs -->
        <div class="keywords-tabs" style="
            background: #fff;
            border-radius: 12px 12px 0 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            gap: 0;
            border-bottom: 2px solid #f0f0f1;
        ">
            <button type="button" class="keyword-tab active" data-tab="extract" style="
                padding: 15px 25px;
                background: none;
                border: none;
                border-bottom: 3px solid #2271b1;
                font-weight: 600;
                cursor: pointer;
            ">
                <?php esc_html_e('📊 키워드 추출', 'wp-bulk-seo'); ?>
            </button>
            <button type="button" class="keyword-tab" data-tab="analyze" style="
                padding: 15px 25px;
                background: none;
                border: none;
                border-bottom: 3px solid transparent;
                font-weight: 600;
                cursor: pointer;
            ">
                <?php esc_html_e('🔍 키워드 분석', 'wp-bulk-seo'); ?>
            </button>
            <button type="button" class="keyword-tab" data-tab="recommend" style="
                padding: 15px 25px;
                background: none;
                border: none;
                border-bottom: 3px solid transparent;
                font-weight: 600;
                cursor: pointer;
            ">
                <?php esc_html_e('💡 키워드 추천', 'wp-bulk-seo'); ?>
            </button>
            <button type="button" class="keyword-tab" data-tab="suggestions" style="
                padding: 15px 25px;
                background: none;
                border: none;
                border-bottom: 3px solid transparent;
                font-weight: 600;
                cursor: pointer;
            ">
                <?php esc_html_e('✨ 콘텐츠 기반 제안', 'wp-bulk-seo'); ?>
            </button>
        </div>

        <!-- Tab Content -->
        <div class="keywords-content" style="
            background: #fff;
            padding: 30px;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        ">
            <!-- Extract Tab -->
            <div class="tab-panel active" id="tab-extract">
                <h2><?php esc_html_e('콘텐츠에서 키워드 자동 추출', 'wp-bulk-seo'); ?></h2>
                <p class="description">
                    <?php esc_html_e('포스트의 제목과 본문을 분석하여 가장 중요한 키워드를 자동으로 추출합니다.', 'wp-bulk-seo'); ?>
                </p>

                <div style="margin: 20px 0;">
                    <button type="button" class="button button-primary button-large" id="extract-keywords-btn">
                        <?php esc_html_e('🔍 키워드 추출 시작', 'wp-bulk-seo'); ?>
                    </button>
                </div>

                <div id="extracted-keywords-results" style="display: none;">
                    <h3><?php esc_html_e('추출된 키워드', 'wp-bulk-seo'); ?></h3>
                    <div class="keywords-grid" style="
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                        gap: 15px;
                        margin-top: 20px;
                    "></div>
                </div>
            </div>

            <!-- Analyze Tab -->
            <div class="tab-panel" id="tab-analyze" style="display: none;">
                <h2><?php esc_html_e('목표 키워드 분석', 'wp-bulk-seo'); ?></h2>
                <p class="description">
                    <?php esc_html_e('목표 키워드를 입력하면 데이터베이스 알고리즘에 근거하여 상세 분석을 제공합니다.', 'wp-bulk-seo'); ?>
                </p>

                <div style="margin: 20px 0;">
                    <label for="target-keyword-input" style="display: block; margin-bottom: 10px; font-weight: 600;">
                        <?php esc_html_e('목표 키워드', 'wp-bulk-seo'); ?>
                    </label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="target-keyword-input" class="regular-text" style="flex: 1; padding: 10px;" 
                               placeholder="<?php esc_attr_e('예: WordPress SEO 최적화', 'wp-bulk-seo'); ?>">
                        <button type="button" class="button button-primary" id="analyze-keyword-btn">
                            <?php esc_html_e('분석', 'wp-bulk-seo'); ?>
                        </button>
                    </div>
                </div>

                <div id="keyword-analysis-results" style="display: none;">
                    <div class="analysis-card" style="
                        background: #f9f9f9;
                        padding: 20px;
                        border-radius: 8px;
                        margin-top: 20px;
                    ">
                        <h3><?php esc_html_e('키워드 분석 결과', 'wp-bulk-seo'); ?></h3>
                        <div class="analysis-details"></div>
                    </div>
                </div>
            </div>

            <!-- Recommend Tab -->
            <div class="tab-panel" id="tab-recommend" style="display: none;">
                <h2><?php esc_html_e('관련 키워드 추천', 'wp-bulk-seo'); ?></h2>
                <p class="description">
                    <?php esc_html_e('목표 키워드를 기반으로 데이터베이스 알고리즘에 근거하여 관련 키워드를 추천합니다.', 'wp-bulk-seo'); ?>
                </p>

                <div style="margin: 20px 0;">
                    <label for="recommend-keyword-input" style="display: block; margin-bottom: 10px; font-weight: 600;">
                        <?php esc_html_e('기준 키워드', 'wp-bulk-seo'); ?>
                    </label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="recommend-keyword-input" class="regular-text" style="flex: 1; padding: 10px;"
                               placeholder="<?php esc_attr_e('예: SEO 최적화', 'wp-bulk-seo'); ?>">
                        <button type="button" class="button button-primary" id="recommend-keywords-btn">
                            <?php esc_html_e('추천 받기', 'wp-bulk-seo'); ?>
                        </button>
                    </div>
                </div>

                <div id="recommended-keywords-results" style="display: none;">
                    <h3><?php esc_html_e('추천 키워드', 'wp-bulk-seo'); ?></h3>
                    <div class="recommendations-list" style="
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                        gap: 15px;
                        margin-top: 20px;
                    "></div>
                </div>
            </div>

            <!-- Suggestions Tab -->
            <div class="tab-panel" id="tab-suggestions" style="display: none;">
                <h2><?php esc_html_e('콘텐츠 기반 키워드 제안', 'wp-bulk-seo'); ?></h2>
                <p class="description">
                    <?php esc_html_e('포스트의 콘텐츠를 분석하여 추출된 키워드를 기반으로 최적의 키워드를 제안합니다.', 'wp-bulk-seo'); ?>
                </p>

                <div style="margin: 20px 0;">
                    <button type="button" class="button button-primary button-large" id="get-suggestions-btn">
                        <?php esc_html_e('✨ 제안 받기', 'wp-bulk-seo'); ?>
                    </button>
                </div>

                <div id="suggestions-results" style="display: none;">
                    <div class="suggestions-content"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.keyword-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.2s;
}
.keyword-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}
.keyword-card.primary {
    border-color: #2271b1;
    background: #f0f6fc;
}
.keyword-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.keyword-name {
    font-weight: 600;
    font-size: 16px;
    color: #1e3a5f;
}
.keyword-score {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}
.keyword-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-top: 10px;
    font-size: 12px;
    color: #666;
}
.keyword-stat {
    text-align: center;
}
.keyword-stat-value {
    font-weight: 600;
    color: #2271b1;
    display: block;
}
.recommendation-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    position: relative;
}
.recommendation-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #46b450;
    color: #fff;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
}
.relevance-bar {
    height: 6px;
    background: #f0f0f1;
    border-radius: 3px;
    margin-top: 10px;
    overflow: hidden;
}
.relevance-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    transition: width 0.3s;
}
</style>

<script>
jQuery(document).ready(function($) {
    var currentPostId = <?php echo $post_id; ?>;

    // Tab switching
    $('.keyword-tab').on('click', function() {
        var tab = $(this).data('tab');
        
        $('.keyword-tab').removeClass('active').css({
            'border-bottom-color': 'transparent'
        });
        $(this).addClass('active').css({
            'border-bottom-color': '#2271b1'
        });

        $('.tab-panel').hide();
        $('#tab-' + tab).show();
    });

    // Post selector
    $('#keyword-post-select').on('change', function() {
        currentPostId = $(this).val();
    });

    $('#load-post-keywords').on('click', function() {
        if (!currentPostId) {
            alert('<?php esc_html_e('포스트를 선택하세요.', 'wp-bulk-seo'); ?>');
            return;
        }
        $('.keywords-main-content').show();
        // Load first tab
        $('.keyword-tab[data-tab="extract"]').click();
    });

    // Extract keywords
    $('#extract-keywords-btn').on('click', function() {
        if (!currentPostId) {
            alert('<?php esc_html_e('포스트를 선택하세요.', 'wp-bulk-seo'); ?>');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php esc_html_e('추출 중...', 'wp-bulk-seo'); ?>');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'wp_bulk_seo_extract_keywords',
            nonce: wpBulkSeo.nonce,
            post_id: currentPostId
        }).done(function(response) {
            if (response.success) {
                displayExtractedKeywords(response.data.keywords);
            } else {
                alert('<?php esc_html_e('오류:', 'wp-bulk-seo'); ?> ' + (response.data.message || '<?php esc_html_e('알 수 없는 오류', 'wp-bulk-seo'); ?>'));
            }
        }).always(function() {
            $btn.prop('disabled', false).text('<?php esc_html_e('🔍 키워드 추출 시작', 'wp-bulk-seo'); ?>');
        });
    });

    function displayExtractedKeywords(keywords) {
        var $grid = $('.keywords-grid').empty();
        $('#extracted-keywords-results').show();

        keywords.forEach(function(kw, index) {
            var isPrimary = index === 0;
            var card = $('<div class="keyword-card' + (isPrimary ? ' primary' : '') + '">');
            
            card.append('<div class="keyword-header">');
            card.find('.keyword-header').append(
                '<span class="keyword-name">' + escapeHtml(kw.keyword) + '</span>',
                '<span class="keyword-score">점수: ' + kw.score.toFixed(1) + '</span>'
            );

            card.append('<div class="keyword-stats">');
            card.find('.keyword-stats').append(
                '<div class="keyword-stat">' +
                    '<span class="keyword-stat-value">' + kw.frequency + '</span>' +
                    '<span>빈도</span>' +
                '</div>',
                '<div class="keyword-stat">' +
                    '<span class="keyword-stat-value">' + (kw.tfidf * 100).toFixed(2) + '%</span>' +
                    '<span>TF-IDF</span>' +
                '</div>',
                '<div class="keyword-stat">' +
                    '<span class="keyword-stat-value">' + kw.positions.length + '</span>' +
                    '<span>위치</span>' +
                '</div>'
            );

            if (isPrimary) {
                card.append('<div style="margin-top: 10px; padding: 10px; background: #fff; border-radius: 4px;">' +
                    '<strong>✓ 주요 키워드로 추천</strong>' +
                '</div>');
            }

            $grid.append(card);
        });
    }

    // Analyze keyword
    $('#analyze-keyword-btn').on('click', function() {
        var keyword = $('#target-keyword-input').val().trim();
        if (!keyword) {
            alert('<?php esc_html_e('키워드를 입력하세요.', 'wp-bulk-seo'); ?>');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php esc_html_e('분석 중...', 'wp-bulk-seo'); ?>');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'wp_bulk_seo_analyze_keyword',
            nonce: wpBulkSeo.nonce,
            keyword: keyword,
            post_id: currentPostId || null
        }).done(function(response) {
            if (response.success) {
                displayKeywordAnalysis(response.data);
            } else {
                alert('<?php esc_html_e('오류:', 'wp-bulk-seo'); ?> ' + (response.data.message || '<?php esc_html_e('알 수 없는 오류', 'wp-bulk-seo'); ?>'));
            }
        }).always(function() {
            $btn.prop('disabled', false).text('<?php esc_html_e('분석', 'wp-bulk-seo'); ?>');
        });
    });

    function displayKeywordAnalysis(analysis) {
        var $details = $('.analysis-details').empty();
        $('#keyword-analysis-results').show();

        $details.append(
            '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">' +
                '<div><strong>키워드:</strong> ' + escapeHtml(analysis.keyword) + '</div>' +
                '<div><strong>길이:</strong> ' + analysis.length + '자</div>' +
                '<div><strong>단어 수:</strong> ' + analysis.word_count + '</div>' +
                '<div><strong>언어:</strong> ' + analysis.language + '</div>' +
                '<div><strong>난이도:</strong> ' + analysis.difficulty + '/100</div>' +
            '</div>'
        );

        if (analysis.in_content) {
            var inContent = analysis.in_content;
            $details.append(
                '<div style="background: #fff; padding: 15px; border-radius: 6px; margin-top: 15px;">' +
                    '<h4>콘텐츠 내 분석</h4>' +
                    '<ul>' +
                        '<li>등장 횟수: ' + inContent.count + '회</li>' +
                        '<li>키워드 밀도: ' + inContent.density + '% ' + 
                            (inContent.optimal_density ? '<span style="color: #46b450;">✓ 최적</span>' : '<span style="color: #dc3232;">✗ 최적 아님</span>') +
                        '</li>' +
                        '<li>제목 포함: ' + (inContent.in_title ? '✓' : '✗') + '</li>' +
                        '<li>첫 문단 포함: ' + (inContent.in_first_paragraph ? '✓' : '✗') + '</li>' +
                    '</ul>' +
                '</div>'
            );
        }

        if (analysis.recommendations && analysis.recommendations.length > 0) {
            $details.append('<h4 style="margin-top: 20px;">관련 키워드 추천</h4>');
            var $recList = $('<div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;"></div>');
            analysis.recommendations.forEach(function(rec) {
                $recList.append(
                    '<span style="background: #f0f0f1; padding: 5px 12px; border-radius: 15px; font-size: 13px;">' +
                        escapeHtml(rec.keyword) +
                    '</span>'
                );
            });
            $details.append($recList);
        }
    }

    // Recommend keywords
    $('#recommend-keywords-btn').on('click', function() {
        var keyword = $('#recommend-keyword-input').val().trim();
        if (!keyword) {
            alert('<?php esc_html_e('기준 키워드를 입력하세요.', 'wp-bulk-seo'); ?>');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php esc_html_e('추천 중...', 'wp-bulk-seo'); ?>');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'wp_bulk_seo_recommend_keywords',
            nonce: wpBulkSeo.nonce,
            keyword: keyword
        }).done(function(response) {
            if (response.success) {
                displayRecommendations(response.data.recommendations);
            } else {
                alert('<?php esc_html_e('오류:', 'wp-bulk-seo'); ?> ' + (response.data.message || '<?php esc_html_e('알 수 없는 오류', 'wp-bulk-seo'); ?>'));
            }
        }).always(function() {
            $btn.prop('disabled', false).text('<?php esc_html_e('추천 받기', 'wp-bulk-seo'); ?>');
        });
    });

    function displayRecommendations(recommendations) {
        var $list = $('.recommendations-list').empty();
        $('#recommended-keywords-results').show();

        recommendations.forEach(function(rec) {
            var card = $('<div class="recommendation-card">');
            
            var typeLabels = {
                'synonym': '동의어',
                'related': '관련',
                'long_tail': '롱테일',
                'ranking_factor': '랭킹 요소',
            };

            card.append(
                '<span class="recommendation-badge">' + (typeLabels[rec.type] || rec.type) + '</span>',
                '<div style="font-weight: 600; font-size: 16px; margin-bottom: 10px;">' + escapeHtml(rec.keyword) + '</div>',
                '<div style="font-size: 12px; color: #666; margin-bottom: 10px;">관련도 점수: ' + rec.relevance_score + '/10</div>',
                '<div class="relevance-bar">' +
                    '<div class="relevance-fill" style="width: ' + (rec.relevance_score * 10) + '%;"></div>' +
                '</div>'
            );

            $list.append(card);
        });
    }

    // Get content-based suggestions
    $('#get-suggestions-btn').on('click', function() {
        if (!currentPostId) {
            alert('<?php esc_html_e('포스트를 선택하세요.', 'wp-bulk-seo'); ?>');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php esc_html_e('제안 생성 중...', 'wp-bulk-seo'); ?>');

        $.post(wpBulkSeo.ajaxUrl, {
            action: 'wp_bulk_seo_get_content_suggestions',
            nonce: wpBulkSeo.nonce,
            post_id: currentPostId
        }).done(function(response) {
            if (response.success) {
                displaySuggestions(response.data);
            } else {
                alert('<?php esc_html_e('오류:', 'wp-bulk-seo'); ?> ' + (response.data.message || '<?php esc_html_e('알 수 없는 오류', 'wp-bulk-seo'); ?>'));
            }
        }).always(function() {
            $btn.prop('disabled', false).text('<?php esc_html_e('✨ 제안 받기', 'wp-bulk-seo'); ?>');
        });
    });

    function displaySuggestions(data) {
        var $content = $('.suggestions-content').empty();
        $('#suggestions-results').show();

        if (data.primary_keyword) {
            $content.append(
                '<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px;">' +
                    '<h3 style="margin: 0 0 10px 0; color: #fff;">주요 키워드</h3>' +
                    '<div style="font-size: 24px; font-weight: 600;">' + escapeHtml(data.primary_keyword) + '</div>' +
                '</div>'
            );
        }

        if (data.extracted_keywords && data.extracted_keywords.length > 0) {
            $content.append('<h3>추출된 키워드</h3>');
            var $extracted = $('<div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;"></div>');
            data.extracted_keywords.slice(0, 10).forEach(function(kw) {
                $extracted.append(
                    '<span style="background: #f0f0f1; padding: 8px 15px; border-radius: 20px; font-size: 14px;">' +
                        escapeHtml(kw.keyword) + ' <small style="color: #666;">(' + kw.score.toFixed(1) + ')</small>' +
                    '</span>'
                );
            });
            $content.append($extracted);
        }

        if (data.recommendations && data.recommendations.length > 0) {
            $content.append('<h3>추천 키워드</h3>');
            var $recGrid = $('<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;"></div>');
            data.recommendations.forEach(function(rec) {
                $recGrid.append(
                    '<div style="background: #f9f9f9; padding: 15px; border-radius: 8px;">' +
                        '<div style="font-weight: 600; margin-bottom: 5px;">' + escapeHtml(rec.keyword) + '</div>' +
                        '<div style="font-size: 12px; color: #666;">관련도: ' + rec.relevance_score + '/10</div>' +
                    '</div>'
                );
            });
            $content.append($recGrid);
        }
    }

    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
</script>
