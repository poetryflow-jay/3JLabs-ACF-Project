<?php
/**
 * Redirections View
 * 
 * Rank Math Pro 스타일의 리다이렉션 관리 페이지
 * 
 * @package WP_Bulk_SEO_AEO
 * @version 2.1.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

$redirections_module = WP_Bulk_SEO_AEO_Redirections_Module::instance();
$stats = $redirections_module->get_redirect_stats();
?>

<div class="wrap wp-bulk-seo-redirections-v25">
    <h1 class="wp-bulk-seo-header-v25">
        <?php esc_html_e('리다이렉션 관리', 'wp-bulk-seo-aeo'); ?>
    </h1>

    <div class="wp-bulk-seo-stats-grid-v25">
        <div class="wp-bulk-seo-stat-card-v25">
            <div class="stat-value"><?php echo esc_html($stats['total_redirects']); ?></div>
            <div class="stat-label"><?php esc_html_e('활성 리다이렉션', 'wp-bulk-seo-aeo'); ?></div>
        </div>
        <div class="wp-bulk-seo-stat-card-v25">
            <div class="stat-value"><?php echo esc_html($stats['total_hits']); ?></div>
            <div class="stat-label"><?php esc_html_e('총 히트 수', 'wp-bulk-seo-aeo'); ?></div>
        </div>
        <div class="wp-bulk-seo-stat-card-v25">
            <div class="stat-value"><?php echo esc_html($stats['total_404s']); ?></div>
            <div class="stat-label"><?php esc_html_e('404 오류', 'wp-bulk-seo-aeo'); ?></div>
        </div>
    </div>

    <div class="wp-bulk-seo-tabs-v25">
        <button class="tab-button active" data-tab="redirects"><?php esc_html_e('리다이렉션', 'wp-bulk-seo-aeo'); ?></button>
        <button class="tab-button" data-tab="404s"><?php esc_html_e('404 모니터링', 'wp-bulk-seo-aeo'); ?></button>
    </div>

    <div class="tab-content active" id="redirects-tab">
        <div class="wp-bulk-seo-card-v25">
            <h2><?php esc_html_e('리다이렉션 추가', 'wp-bulk-seo-aeo'); ?></h2>
            <form id="wp-bulk-seo-add-redirect-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="source_url"><?php esc_html_e('소스 URL', 'wp-bulk-seo-aeo'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="source_url" name="source_url" class="regular-text" 
                                   placeholder="/old-page/" required />
                            <p class="description"><?php esc_html_e('리다이렉션할 URL 경로', 'wp-bulk-seo-aeo'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="target_url"><?php esc_html_e('타겟 URL', 'wp-bulk-seo-aeo'); ?></label>
                        </th>
                        <td>
                            <input type="url" id="target_url" name="target_url" class="regular-text" 
                                   placeholder="https://example.com/new-page/" required />
                            <p class="description"><?php esc_html_e('리다이렉션할 대상 URL', 'wp-bulk-seo-aeo'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="redirect_type"><?php esc_html_e('리다이렉션 타입', 'wp-bulk-seo-aeo'); ?></label>
                        </th>
                        <td>
                            <select id="redirect_type" name="redirect_type">
                                <option value="301">301 - 영구 리다이렉션</option>
                                <option value="302">302 - 임시 리다이렉션</option>
                                <option value="307">307 - 임시 리다이렉션 (POST 보존)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="match_type"><?php esc_html_e('매칭 타입', 'wp-bulk-seo-aeo'); ?></label>
                        </th>
                        <td>
                            <select id="match_type" name="match_type">
                                <option value="exact">정확한 매칭</option>
                                <option value="regex">정규식 매칭</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-primary">
                        <?php esc_html_e('리다이렉션 추가', 'wp-bulk-seo-aeo'); ?>
                    </button>
                </p>
            </form>
        </div>

        <div class="wp-bulk-seo-card-v25">
            <h2><?php esc_html_e('리다이렉션 목록', 'wp-bulk-seo-aeo'); ?></h2>
            <div id="redirects-list">
                <p><?php esc_html_e('로딩 중...', 'wp-bulk-seo-aeo'); ?></p>
            </div>
        </div>
    </div>

    <div class="tab-content" id="404s-tab">
        <div class="wp-bulk-seo-card-v25">
            <h2><?php esc_html_e('404 오류 모니터링', 'wp-bulk-seo-aeo'); ?></h2>
            <div id="404s-list">
                <p><?php esc_html_e('로딩 중...', 'wp-bulk-seo-aeo'); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // 탭 전환
    $('.tab-button').on('click', function() {
        var tab = $(this).data('tab');
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').removeClass('active');
        $('#' + tab + '-tab').addClass('active');
        
        if (tab === 'redirects') {
            loadRedirects();
        } else if (tab === '404s') {
            load404s();
        }
    });

    // 리다이렉션 추가
    $('#wp-bulk-seo-add-redirect-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_bulk_seo_add_redirect',
                source_url: $('#source_url').val(),
                target_url: $('#target_url').val(),
                redirect_type: $('#redirect_type').val(),
                match_type: $('#match_type').val(),
                nonce: '<?php echo wp_create_nonce('wp_bulk_seo_redirect'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    $('#wp-bulk-seo-add-redirect-form')[0].reset();
                    loadRedirects();
                } else {
                    alert(response.data.message || '<?php esc_html_e('오류가 발생했습니다.', 'wp-bulk-seo-aeo'); ?>');
                }
            }
        });
    });

    // 리다이렉션 목록 로드
    function loadRedirects() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_bulk_seo_get_redirects',
                nonce: '<?php echo wp_create_nonce('wp_bulk_seo_redirect'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    var html = '<table class="wp-list-table widefat fixed striped">';
                    html += '<thead><tr>';
                    html += '<th><?php esc_html_e('소스 URL', 'wp-bulk-seo-aeo'); ?></th>';
                    html += '<th><?php esc_html_e('타겟 URL', 'wp-bulk-seo-aeo'); ?></th>';
                    html += '<th><?php esc_html_e('타입', 'wp-bulk-seo-aeo'); ?></th>';
                    html += '<th><?php esc_html_e('히트 수', 'wp-bulk-seo-aeo'); ?></th>';
                    html += '<th><?php esc_html_e('작업', 'wp-bulk-seo-aeo'); ?></th>';
                    html += '</tr></thead><tbody>';
                    
                    if (response.data.redirects && response.data.redirects.length > 0) {
                        response.data.redirects.forEach(function(redirect) {
                            html += '<tr>';
                            html += '<td>' + redirect.source_url + '</td>';
                            html += '<td>' + redirect.target_url + '</td>';
                            html += '<td>' + redirect.redirect_type + '</td>';
                            html += '<td>' + redirect.hit_count + '</td>';
                            html += '<td><button class="button delete-redirect" data-id="' + redirect.id + '"><?php esc_html_e('삭제', 'wp-bulk-seo-aeo'); ?></button></td>';
                            html += '</tr>';
                        });
                    } else {
                        html += '<tr><td colspan="5"><?php esc_html_e('리다이렉션이 없습니다.', 'wp-bulk-seo-aeo'); ?></td></tr>';
                    }
                    
                    html += '</tbody></table>';
                    $('#redirects-list').html(html);
                }
            }
        });
    }

    // 404 목록 로드
    function load404s() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_bulk_seo_get_404s',
                limit: 50,
                nonce: '<?php echo wp_create_nonce('wp_bulk_seo_redirect'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    var html = '<table class="wp-list-table widefat fixed striped">';
                    html += '<thead><tr>';
                    html += '<th><?php esc_html_e('URL', 'wp-bulk-seo-aeo'); ?></th>';
                    html += '<th><?php esc_html_e('히트 수', 'wp-bulk-seo-aeo'); ?></th>';
                    html += '<th><?php esc_html_e('제안된 리다이렉션', 'wp-bulk-seo-aeo'); ?></th>';
                    html += '<th><?php esc_html_e('작업', 'wp-bulk-seo-aeo'); ?></th>';
                    html += '</tr></thead><tbody>';
                    
                    if (response.data['404s'] && response.data['404s'].length > 0) {
                        response.data['404s'].forEach(function(four) {
                            html += '<tr>';
                            html += '<td>' + four.url + '</td>';
                            html += '<td>' + four.hit_count + '</td>';
                            html += '<td>' + (four.suggested_redirect || '<?php esc_html_e('없음', 'wp-bulk-seo-aeo'); ?>') + '</td>';
                            html += '<td>';
                            if (four.suggested_redirect) {
                                html += '<button class="button create-redirect" data-source="' + four.url + '" data-target="' + four.suggested_redirect + '"><?php esc_html_e('리다이렉션 생성', 'wp-bulk-seo-aeo'); ?></button>';
                            }
                            html += '</td>';
                            html += '</tr>';
                        });
                    } else {
                        html += '<tr><td colspan="4"><?php esc_html_e('404 오류가 없습니다.', 'wp-bulk-seo-aeo'); ?></td></tr>';
                    }
                    
                    html += '</tbody></table>';
                    $('#404s-list').html(html);
                }
            }
        });
    }

    // 리다이렉션 삭제
    $(document).on('click', '.delete-redirect', function() {
        if (!confirm('<?php esc_html_e('리다이렉션을 삭제하시겠습니까?', 'wp-bulk-seo-aeo'); ?>')) {
            return;
        }
        
        var redirectId = $(this).data('id');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_bulk_seo_delete_redirect',
                redirect_id: redirectId,
                nonce: '<?php echo wp_create_nonce('wp_bulk_seo_redirect'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    loadRedirects();
                } else {
                    alert(response.data.message || '<?php esc_html_e('오류가 발생했습니다.', 'wp-bulk-seo-aeo'); ?>');
                }
            }
        });
    });

    // 404에서 리다이렉션 생성
    $(document).on('click', '.create-redirect', function() {
        var source = $(this).data('source');
        var target = $(this).data('target');
        
        $('#source_url').val(source);
        $('#target_url').val(target);
        $('.tab-button[data-tab="redirects"]').click();
    });

    // 초기 로드
    loadRedirects();
});
</script>
