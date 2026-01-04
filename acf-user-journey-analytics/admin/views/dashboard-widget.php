<?php
/**
 * 워드프레스 대시보드 위젯
 *
 * @package ACF_User_Journey_Analytics
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="acf-uja-dashboard-widget">
    <div class="widget-realtime">
        <div class="realtime-number">
            <span class="pulse"></span>
            <strong id="acf-uja-realtime-count"><?php echo number_format( $data['realtime_users'] ); ?></strong>
        </div>
        <p>현재 활성 사용자 (5분 이내)</p>
    </div>

    <div class="widget-stats">
        <div class="stat-item">
            <span class="stat-value"><?php echo number_format( $data['today_visitors'] ); ?></span>
            <span class="stat-label">오늘 방문자</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?php echo number_format( $data['today_sessions'] ); ?></span>
            <span class="stat-label">오늘 세션</span>
        </div>
    </div>

    <?php if ( ! empty( $data['top_sources'] ) ) : ?>
    <div class="widget-sources">
        <h4>상위 트래픽 소스</h4>
        <ul>
            <?php foreach ( $data['top_sources'] as $source ) : ?>
            <li>
                <span class="source-name"><?php echo esc_html( $source->referrer_type ); ?></span>
                <span class="source-count"><?php echo number_format( $source->count ); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="widget-footer">
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-user-journey' ) ); ?>">전체 대시보드 보기 &rarr;</a>
    </div>
</div>

<style>
.acf-uja-dashboard-widget { padding: 10px 0; }
.widget-realtime { text-align: center; padding: 15px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 8px; color: #fff; margin-bottom: 15px; }
.realtime-number { display: flex; align-items: center; justify-content: center; gap: 10px; }
.realtime-number strong { font-size: 36px; }
.pulse { width: 12px; height: 12px; background: #22c55e; border-radius: 50%; animation: pulse 1.5s infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.2); } }
.widget-stats { display: flex; gap: 10px; margin-bottom: 15px; }
.stat-item { flex: 1; text-align: center; padding: 10px; background: #f8fafc; border-radius: 6px; }
.stat-value { display: block; font-size: 24px; font-weight: 600; color: #1e293b; }
.stat-label { font-size: 12px; color: #64748b; }
.widget-sources h4 { margin: 0 0 10px; font-size: 13px; color: #64748b; }
.widget-sources ul { margin: 0; padding: 0; list-style: none; }
.widget-sources li { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e2e8f0; }
.widget-sources li:last-child { border-bottom: 0; }
.source-name { text-transform: capitalize; }
.source-count { font-weight: 600; color: #6366f1; }
.widget-footer { margin-top: 15px; text-align: center; }
.widget-footer a { color: #6366f1; text-decoration: none; font-weight: 500; }
.widget-footer a:hover { text-decoration: underline; }
</style>

<script>
(function() {
    function updateRealtime() {
        jQuery.post(ajaxurl, {
            action: 'acf_uja_realtime_data',
            nonce: '<?php echo wp_create_nonce( 'acf_uja_realtime' ); ?>'
        }, function(response) {
            if (response.success) {
                document.getElementById('acf-uja-realtime-count').textContent = response.data.realtime_users;
            }
        });
    }
    setInterval(updateRealtime, 30000);
})();
</script>
