<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tracker = new JJ_Campaign_Tracker();
$campaign_stats = $tracker->get_campaign_stats( 30 );
?>
<div class="wrap jj-marketing-campaigns">
    <h1><?php esc_html_e( '🎯 캠페 트래커', 'jj-marketing-dashboard' ); ?></h1>
    <p class="description">
        <?php esc_html_e( 'UTM 파라미터를 통한 캠페인 추적 및 분석', 'jj-marketing-dashboard' ); ?>
    </p>

    <div class="jj-marketing-campaigns-container">
        <div class="jj-marketing-campaign-stats">
            <div class="jj-marketing-stat-card">
                <div class="jj-marketing-stat-label"><?php esc_html_e( '총 방문', 'jj-marketing-dashboard' ); ?></div>
                <div class="jj-marketing-stat-value"><?php echo esc_html( $campaign_stats['total_visits'] ); ?></div>
            </div>
        </div>

        <div id="campaigns-chart-container">
            <!-- Campaign charts will be rendered here -->
        </div>
    </div>
</div>
