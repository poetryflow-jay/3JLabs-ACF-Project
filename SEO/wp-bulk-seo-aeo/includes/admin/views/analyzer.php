<?php
/**
 * Bulk Analyzer View
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap wp-bulk-seo-aeo-analyzer">
    <h1><?php esc_html_e('Bulk SEO Analyzer', 'wp-bulk-seo-aeo'); ?></h1>

    <div class="analyzer-controls">
        <h2><?php esc_html_e('분석 설정', 'wp-bulk-seo-aeo'); ?></h2>
        <form id="wp-bulk-seo-aeo-analyzer-form">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="post_types"><?php esc_html_e('포스트 타입', 'wp-bulk-seo-aeo'); ?></label>
                    </th>
                    <td>
                        <?php
                        $post_types = get_post_types(['public' => true], 'objects');
                        foreach ($post_types as $post_type) {
                            ?>
                            <label>
                                <input type="checkbox" name="post_types[]" value="<?php echo esc_attr($post_type->name); ?>" checked />
                                <?php echo esc_html($post_type->label); ?>
                            </label><br />
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="batch_size"><?php esc_html_e('배치 크기', 'wp-bulk-seo-aeo'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="batch_size" name="batch_size" value="10" min="1" max="50" />
                        <p class="description"><?php esc_html_e('한 번에 분석할 페이지 수', 'wp-bulk-seo-aeo'); ?></p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="button" class="button button-primary" id="wp-bulk-seo-aeo-start-analysis">
                    <?php esc_html_e('분석 시작', 'wp-bulk-seo-aeo'); ?>
                </button>
            </p>
        </form>
    </div>

    <div class="analyzer-progress" id="wp-bulk-seo-aeo-progress" style="display: none;">
        <h2><?php esc_html_e('분석 진행 상황', 'wp-bulk-seo-aeo'); ?></h2>
        <div class="progress-bar">
            <div class="progress-fill" id="wp-bulk-seo-aeo-progress-fill" style="width: 0%;"></div>
        </div>
        <p id="wp-bulk-seo-aeo-progress-text"><?php esc_html_e('준비 중...', 'wp-bulk-seo-aeo'); ?></p>
    </div>

    <div class="analyzer-results" id="wp-bulk-seo-aeo-results" style="display: none;">
        <h2><?php esc_html_e('분석 결과', 'wp-bulk-seo-aeo'); ?></h2>
        <div id="wp-bulk-seo-aeo-results-content"></div>
    </div>
</div>

<style>
.wp-bulk-seo-aeo-analyzer {
    max-width: 1200px;
}
.analyzer-controls {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
.progress-bar {
    width: 100%;
    height: 30px;
    background: #f0f0f1;
    border-radius: 4px;
    overflow: hidden;
    margin: 15px 0;
}
.progress-fill {
    height: 100%;
    background: #2271b1;
    transition: width 0.3s ease;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('#wp-bulk-seo-aeo-start-analysis').on('click', function() {
        // TODO: Implement AJAX analysis
        alert('분석 기능은 곧 구현될 예정입니다.');
    });
});
</script>
