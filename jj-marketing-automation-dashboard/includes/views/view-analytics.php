<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap jj-marketing-analytics">
    <h1><?php esc_html_e( '📊 통계 분석', 'jj-marketing-dashboard' ); ?></h1>
    <p class="description">
        <?php esc_html_e( '모든 3J Labs 플러그인의 사용 데이터를 통합 분석합니다.', 'jj-marketing-dashboard' ); ?>
    </p>

    <div class="jj-marketing-analytics-container">
        <div id="analytics-chart-container">
            <!-- Analytics charts will be rendered here -->
        </div>
    </div>
</div>
