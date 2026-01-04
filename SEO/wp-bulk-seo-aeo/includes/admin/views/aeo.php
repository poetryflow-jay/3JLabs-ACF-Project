<?php
/**
 * AEO Settings View
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap wp-bulk-seo-aeo-aeo">
    <h1><?php esc_html_e('AEO (Answer Engine Optimization) Settings', 'wp-bulk-seo-aeo'); ?></h1>

    <div class="aeo-info">
        <h2><?php esc_html_e('Answer Engine Optimization', 'wp-bulk-seo-aeo'); ?></h2>
        <p><?php esc_html_e('AEO는 AI 검색 엔진(예: ChatGPT, Google Bard)에서 콘텐츠가 답변으로 표시되도록 최적화하는 기능입니다.', 'wp-bulk-seo-aeo'); ?></p>
    </div>

    <div class="aeo-features">
        <h2><?php esc_html_e('주요 기능', 'wp-bulk-seo-aeo'); ?></h2>
        <ul>
            <li><?php esc_html_e('FAQ Schema 자동 생성', 'wp-bulk-seo-aeo'); ?></li>
            <li><?php esc_html_e('Featured Snippet 최적화', 'wp-bulk-seo-aeo'); ?></li>
            <li><?php esc_html_e('구조화된 답변 형식', 'wp-bulk-seo-aeo'); ?></li>
            <li><?php esc_html_e('AI 친화적 콘텐츠 포맷팅', 'wp-bulk-seo-aeo'); ?></li>
        </ul>
    </div>

    <p class="description">
        <?php esc_html_e('AEO 기능은 곧 구현될 예정입니다.', 'wp-bulk-seo-aeo'); ?>
    </p>
</div>

<style>
.wp-bulk-seo-aeo-aeo {
    max-width: 1200px;
}
.aeo-info {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
.aeo-features {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
.aeo-features ul {
    list-style: disc;
    margin-left: 20px;
}
</style>
