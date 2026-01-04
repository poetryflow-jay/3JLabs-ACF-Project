<?php
/**
 * Schema Manager View
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap wp-bulk-seo-aeo-schema">
    <h1><?php esc_html_e('Schema Manager', 'wp-bulk-seo-aeo'); ?></h1>

    <div class="schema-info">
        <h2><?php esc_html_e('구조화된 데이터 (Schema.org)', 'wp-bulk-seo-aeo'); ?></h2>
        <p><?php esc_html_e('Schema.org 마크업을 사용하여 검색 엔진에 콘텐츠를 더 잘 이해시킬 수 있습니다.', 'wp-bulk-seo-aeo'); ?></p>
    </div>

    <p class="description">
        <?php esc_html_e('Schema Manager 기능은 곧 구현될 예정입니다.', 'wp-bulk-seo-aeo'); ?>
    </p>
</div>

<style>
.wp-bulk-seo-aeo-schema {
    max-width: 1200px;
}
.schema-info {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
</style>
