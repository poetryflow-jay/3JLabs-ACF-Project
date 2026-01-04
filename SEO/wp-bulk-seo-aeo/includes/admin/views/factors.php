<?php
/**
 * Ranking Factors View
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap wp-bulk-seo-aeo-factors">
    <h1><?php esc_html_e('Ranking Factors', 'wp-bulk-seo-aeo'); ?></h1>

    <div class="factors-info">
        <h2><?php esc_html_e('SEO 랭킹 요소', 'wp-bulk-seo-aeo'); ?></h2>
        <p><?php esc_html_e('Google 알고리즘 유출 문서와 Airtable 데이터베이스를 기반으로 한 SEO 랭킹 요소 목록입니다.', 'wp-bulk-seo-aeo'); ?></p>
    </div>

    <div class="factors-table">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('요소명', 'wp-bulk-seo-aeo'); ?></th>
                    <th><?php esc_html_e('카테고리', 'wp-bulk-seo-aeo'); ?></th>
                    <th><?php esc_html_e('가중치', 'wp-bulk-seo-aeo'); ?></th>
                    <th><?php esc_html_e('영향도', 'wp-bulk-seo-aeo'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">
                        <?php esc_html_e('랭킹 요소 데이터는 데이터베이스에서 로드됩니다.', 'wp-bulk-seo-aeo'); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
.wp-bulk-seo-aeo-factors {
    max-width: 1200px;
}
.factors-info {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
</style>
