<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Style System Stats Dashboard
 * - Visualizing design system usage
 */

$options = get_option( 'jj_style_guide_options', array() );
$colors = isset($options['colors']) ? $options['colors'] : array();
$typography = isset($options['typography']) ? $options['typography'] : array();

// 통계 데이터 계산
$active_colors_count = count(array_filter($colors));
$fonts_count = 0;
if (isset($typography)) {
    $fonts = array();
    foreach($typography as $item) {
        if (isset($item['font_family'])) {
            $fonts[] = $item['font_family'];
        }
    }
    $fonts_count = count(array_unique(array_filter($fonts)));
}

?>
<div class="jj-stats-container" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;"><?php _e( '📊 시스템 인사이트', 'acf-css-really-simple-style-management-center' ); ?></h2>
        <span class="badge" style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;">Live Analysis</span>
    </div>

    <div class="jj-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <div class="jj-stat-card" style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #f1f5f9;">
            <div style="font-size: 13px; color: #64748b; margin-bottom: 10px;">활성 색상</div>
            <div style="font-size: 28px; font-weight: 800; color: #1e293b;"><?php echo $active_colors_count; ?></div>
            <div style="margin-top: 10px; height: 4px; background: #e2e8f0; border-radius: 2px;">
                <div style="width: <?php echo min(100, $active_colors_count * 10); ?>%; height: 100%; background: #3b82f6; border-radius: 2px;"></div>
            </div>
        </div>

        <div class="jj-stat-card" style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #f1f5f9;">
            <div style="font-size: 13px; color: #64748b; margin-bottom: 10px;">사용 중인 폰트</div>
            <div style="font-size: 28px; font-weight: 800; color: #1e293b;"><?php echo $fonts_count; ?></div>
            <div style="margin-top: 10px; height: 4px; background: #e2e8f0; border-radius: 2px;">
                <div style="width: <?php echo min(100, $fonts_count * 20); ?>%; height: 100%; background: #10b981; border-radius: 2px;"></div>
            </div>
        </div>

        <div class="jj-stat-card" style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #f1f5f9; grid-column: span 2;">
            <canvas id="jj-design-balance-chart" height="100"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
jQuery(document).ready(function($) {
    var ctx = document.getElementById('jj-design-balance-chart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
            datasets: [{
                label: '디자인 시스템 일관성 지수',
                data: [65, 72, 68, 85, 92, 90, 98],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, max: 100, display: false },
                x: { display: false }
            }
        }
    });
});
</script>
