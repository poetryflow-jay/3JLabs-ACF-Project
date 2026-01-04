<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap jj-marketing-seo">
    <h1><?php esc_html_e( '🔍 SEO 최적화', 'jj-marketing-dashboard' ); ?></h1>
    <p class="description">
        <?php esc_html_e( 'SEO 감사 및 최적화 기능을 제공합니다.', 'jj-marketing-dashboard' ); ?>
    </p>

    <div class="jj-marketing-seo-container">
        <div class="jj-marketing-seo-actions">
            <button class="button button-primary" onclick="jj_marketing_run_seo_audit()">
                <?php esc_html_e( 'SEO 감사 실행', 'jj-marketing-dashboard' ); ?>
            </button>
        </div>
        <div id="seo-results-container">
            <!-- SEO results will be displayed here -->
        </div>
    </div>
</div>
