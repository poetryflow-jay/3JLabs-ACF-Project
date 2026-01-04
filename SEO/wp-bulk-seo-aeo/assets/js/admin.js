/**
 * WP Bulk SEO & AEO - Admin JavaScript
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // 1-Click SEO
        $('#wp-bulk-seo-aeo-1click').on('click', function() {
            var $button = $(this);
            var originalText = $button.text();
            $button.prop('disabled', true).text(wpBulkSEOAEO.i18n.optimizing || '최적화 중...');

            $.ajax({
                url: wpBulkSEOAEO.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'wp_bulk_seo_aeo_1click_setup',
                    nonce: wpBulkSEOAEO.nonce
                },
                success: function(response) {
                    if (response && response.success) {
                        alert(response.data && response.data.message ? response.data.message : (wpBulkSEOAEO.i18n.success || '성공!'));
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        var errorMsg = '알 수 없는 오류가 발생했습니다.';
                        if (response && response.data && response.data.message) {
                            errorMsg = response.data.message;
                        } else if (response && response.data) {
                            errorMsg = response.data;
                        }
                        alert((wpBulkSEOAEO.i18n.error || '오류 발생') + ': ' + errorMsg);
                        $button.prop('disabled', false).text(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    var errorMsg = wpBulkSEOAEO.i18n.error || '오류 발생';
                    if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                        errorMsg += ': ' + xhr.responseJSON.data.message;
                    } else if (xhr.responseText) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.data && response.data.message) {
                                errorMsg += ': ' + response.data.message;
                            }
                        } catch (e) {
                            errorMsg += ': ' + error;
                        }
                    } else {
                        errorMsg += ': ' + error;
                    }
                    alert(errorMsg);
                    $button.prop('disabled', false).text(originalText);
                },
                complete: function() {
                    // complete는 success/error 후에 호출되므로 여기서는 처리하지 않음
                }
            });
        });

        // Bulk Analyzer
        $('#wp-bulk-seo-aeo-start-analysis').on('click', function() {
            var $button = $(this);
            var $form = $('#wp-bulk-seo-aeo-analyzer-form');
            var $progress = $('#wp-bulk-seo-aeo-progress');
            var $results = $('#wp-bulk-seo-aeo-results');

            $button.prop('disabled', true).text(wpBulkSEOAEO.i18n.analyzing);
            $progress.show();
            $results.hide();

            var formData = {
                action: 'wp_bulk_seo_aeo_bulk_analyze',
                nonce: wpBulkSEOAEO.nonce,
                post_types: $form.find('input[name="post_types[]"]:checked').map(function() {
                    return $(this).val();
                }).get(),
                batch_size: parseInt($form.find('#batch_size').val()) || 10
            };

            $.ajax({
                url: wpBulkSEOAEO.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        updateProgress(100);
                        displayResults(response.data);
                    } else {
                        alert(wpBulkSEOAEO.i18n.error + ': ' + (response.data || 'Unknown error'));
                    }
                },
                error: function() {
                    alert(wpBulkSEOAEO.i18n.error);
                },
                complete: function() {
                    $button.prop('disabled', false).text('분석 시작');
                }
            });
        });

        // Bulk Optimizer
        $('#wp-bulk-seo-aeo-start-optimization').on('click', function() {
            var $button = $(this);
            var $form = $('#wp-bulk-seo-aeo-optimizer-form');
            var $results = $('#wp-bulk-seo-aeo-optimizer-results');

            $button.prop('disabled', true).text(wpBulkSEOAEO.i18n.optimizing);
            $results.hide();

            var formData = {
                action: 'wp_bulk_seo_aeo_bulk_optimize',
                nonce: wpBulkSEOAEO.nonce,
                optimizations: $form.find('input[name="optimizations[]"]:checked').map(function() {
                    return $(this).val();
                }).get()
            };

            $.ajax({
                url: wpBulkSEOAEO.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        displayOptimizerResults(response.data);
                    } else {
                        alert(wpBulkSEOAEO.i18n.error + ': ' + (response.data || 'Unknown error'));
                    }
                },
                error: function() {
                    alert(wpBulkSEOAEO.i18n.error);
                },
                complete: function() {
                    $button.prop('disabled', false).text('최적화 시작');
                }
            });
        });

        function updateProgress(percent) {
            $('#wp-bulk-seo-aeo-progress-fill').css('width', percent + '%');
            $('#wp-bulk-seo-aeo-progress-text').text(percent + '% 완료');
        }

        function displayResults(data) {
            var $results = $('#wp-bulk-seo-aeo-results');
            var $content = $('#wp-bulk-seo-aeo-results-content');

            var html = '<table class="wp-list-table widefat fixed striped">';
            html += '<thead><tr><th>포스트 ID</th><th>상태</th><th>점수</th><th>등급</th></tr></thead>';
            html += '<tbody>';

            if (data.results) {
                $.each(data.results, function(postId, result) {
                    html += '<tr>';
                    html += '<td>' + postId + '</td>';
                    html += '<td>' + result.status + '</td>';
                    html += '<td>' + (result.score || '-') + '</td>';
                    html += '<td>' + (result.grade || '-') + '</td>';
                    html += '</tr>';
                });
            }

            html += '</tbody></table>';
            html += '<p><strong>총계:</strong> ' + data.total + '개 | ';
            html += '<strong>성공:</strong> ' + data.success + '개 | ';
            html += '<strong>실패:</strong> ' + data.failed + '개</p>';

            $content.html(html);
            $results.show();
        }

        function displayOptimizerResults(data) {
            var $results = $('#wp-bulk-seo-aeo-optimizer-results');
            var $content = $('#wp-bulk-seo-aeo-optimizer-results-content');

            var html = '<div class="notice notice-success"><p>';
            html += '최적화가 완료되었습니다. ';
            html += '적용된 최적화: ' + (data.optimizations ? data.optimizations.join(', ') : '없음');
            if (data.improvement) {
                html += ' | 점수 개선: +' + data.improvement;
            }
            html += '</p></div>';

            $content.html(html);
            $results.show();
        }
    });

})(jQuery);
