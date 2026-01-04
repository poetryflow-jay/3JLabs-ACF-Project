<?php
/**
 * 대시보드 뷰
 *
 * @package ACF_User_Journey_Analytics
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 차트 데이터 준비
$chart_labels = array();
$chart_sessions = array();
$chart_visitors = array();
foreach ( $daily_traffic as $day ) {
    $chart_labels[] = date( 'm/d', strtotime( $day->date ) );
    $chart_sessions[] = (int) $day->sessions;
    $chart_visitors[] = (int) $day->visitors;
}

// 리퍼러 타입별 데이터
$referrer_labels = array();
$referrer_data = array();
$referrer_colors = array(
    'direct' => '#6366f1',
    'organic' => '#22c55e',
    'paid' => '#f59e0b',
    'social' => '#ec4899',
    'referral' => '#8b5cf6',
    'email' => '#06b6d4',
    'ai_search' => '#10b981',
    'ai_chatbot' => '#14b8a6',
);
$colors = array();

foreach ( $referrer_breakdown as $ref ) {
    $type = $ref->referrer_type;
    if ( ! isset( $referrer_labels[ $type ] ) ) {
        $referrer_labels[ $type ] = 0;
    }
    $referrer_labels[ $type ] += (int) $ref->sessions;
}

foreach ( $referrer_labels as $type => $count ) {
    $referrer_data[] = $count;
    $colors[] = isset( $referrer_colors[ $type ] ) ? $referrer_colors[ $type ] : '#9ca3af';
}
$referrer_labels = array_keys( $referrer_labels );
?>

<div class="wrap acf-uja-wrap">
    <h1>
        <span class="dashicons dashicons-chart-line"></span>
        User Journey Analytics Dashboard
    </h1>

    <!-- 프로모션 배너 -->
    <div class="acf-uja-promo-banner">
        <div class="promo-content">
            <h3>이 데이터를 마케팅에 활용하고 싶으신가요?</h3>
            <p>트래픽 분석 데이터를 기반으로 자동화된 마케팅 캠페인을 실행하세요.</p>
            <div class="promo-buttons">
                <a href="https://wordpress.org/plugins/acf-nudge-flow/" target="_blank" class="button button-primary">
                    Nudge Flow 설치하기
                </a>
                <a href="https://wordpress.org/plugins/oneclick-seo-pro/" target="_blank" class="button">
                    1-Click SEO 설치하기
                </a>
            </div>
        </div>
        <button type="button" class="promo-dismiss" title="닫기">&times;</button>
    </div>

    <!-- 기간 선택 -->
    <div class="acf-uja-period-selector">
        <a href="<?php echo esc_url( add_query_arg( 'period', 'today' ) ); ?>" class="<?php echo $period === 'today' ? 'active' : ''; ?>">오늘</a>
        <a href="<?php echo esc_url( add_query_arg( 'period', 'yesterday' ) ); ?>" class="<?php echo $period === 'yesterday' ? 'active' : ''; ?>">어제</a>
        <a href="<?php echo esc_url( add_query_arg( 'period', '7days' ) ); ?>" class="<?php echo $period === '7days' ? 'active' : ''; ?>">7일</a>
        <a href="<?php echo esc_url( add_query_arg( 'period', '30days' ) ); ?>" class="<?php echo $period === '30days' ? 'active' : ''; ?>">30일</a>
        <a href="<?php echo esc_url( add_query_arg( 'period', '90days' ) ); ?>" class="<?php echo $period === '90days' ? 'active' : ''; ?>">90일</a>
    </div>

    <!-- 실시간 위젯 -->
    <div class="acf-uja-realtime-bar">
        <div class="realtime-item">
            <span class="realtime-dot"></span>
            <strong><?php echo number_format( $realtime['realtime_users'] ); ?></strong>
            <span>현재 활성 사용자</span>
        </div>
        <div class="realtime-item">
            <strong><?php echo number_format( $realtime['today_visitors'] ); ?></strong>
            <span>오늘 방문자</span>
        </div>
        <div class="realtime-item">
            <strong><?php echo number_format( $realtime['today_sessions'] ); ?></strong>
            <span>오늘 세션</span>
        </div>
    </div>

    <!-- 요약 카드 -->
    <div class="acf-uja-summary-cards">
        <div class="summary-card">
            <div class="card-icon" style="background: #6366f1;">
                <span class="dashicons dashicons-groups"></span>
            </div>
            <div class="card-content">
                <h3><?php echo number_format( $summary['unique_visitors'] ); ?></h3>
                <p>순 방문자</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon" style="background: #22c55e;">
                <span class="dashicons dashicons-chart-bar"></span>
            </div>
            <div class="card-content">
                <h3><?php echo number_format( $summary['sessions'] ); ?></h3>
                <p>세션</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon" style="background: #f59e0b;">
                <span class="dashicons dashicons-visibility"></span>
            </div>
            <div class="card-content">
                <h3><?php echo number_format( $summary['pageviews'] ); ?></h3>
                <p>페이지뷰</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon" style="background: #ec4899;">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <div class="card-content">
                <h3><?php echo $summary['conversion_rate']; ?>%</h3>
                <p>전환율</p>
            </div>
        </div>
    </div>

    <!-- 차트 영역 -->
    <div class="acf-uja-charts">
        <div class="chart-container">
            <h3>일별 트래픽 추이</h3>
            <canvas id="dailyTrafficChart"></canvas>
        </div>
        <div class="chart-container chart-half">
            <h3>트래픽 소스 분포</h3>
            <canvas id="referrerChart"></canvas>
        </div>
        <div class="chart-container chart-half">
            <h3>디바이스별 세션</h3>
            <canvas id="deviceChart"></canvas>
        </div>
    </div>

    <!-- 상위 트래픽 소스 테이블 -->
    <div class="acf-uja-table-container">
        <h3>상위 트래픽 소스</h3>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>소스</th>
                    <th>유형</th>
                    <th>방문자</th>
                    <th>세션</th>
                    <th>전환</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( array_slice( $referrer_breakdown, 0, 10 ) as $ref ) : ?>
                <tr>
                    <td><?php echo esc_html( $ref->referrer_name ?: $ref->referrer_source ); ?></td>
                    <td><span class="type-badge type-<?php echo esc_attr( $ref->referrer_type ); ?>"><?php echo esc_html( $ref->referrer_type ); ?></span></td>
                    <td><?php echo number_format( $ref->visitors ); ?></td>
                    <td><?php echo number_format( $ref->sessions ); ?></td>
                    <td><?php echo number_format( $ref->conversions ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 일별 트래픽 차트
    new Chart(document.getElementById('dailyTrafficChart'), {
        type: 'line',
        data: {
            labels: <?php echo wp_json_encode( $chart_labels ); ?>,
            datasets: [
                {
                    label: '세션',
                    data: <?php echo wp_json_encode( $chart_sessions ); ?>,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: '방문자',
                    data: <?php echo wp_json_encode( $chart_visitors ); ?>,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });

    // 리퍼러 차트
    new Chart(document.getElementById('referrerChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo wp_json_encode( $referrer_labels ); ?>,
            datasets: [{
                data: <?php echo wp_json_encode( $referrer_data ); ?>,
                backgroundColor: <?php echo wp_json_encode( $colors ); ?>
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right' }
            }
        }
    });

    // 디바이스 차트
    <?php
    $device_labels = array();
    $device_data = array();
    $device_colors = array( 'desktop' => '#6366f1', 'mobile' => '#22c55e', 'tablet' => '#f59e0b' );
    foreach ( $device_stats as $device ) {
        $device_labels[] = $device->device_type;
        $device_data[] = (int) $device->sessions;
    }
    ?>
    new Chart(document.getElementById('deviceChart'), {
        type: 'bar',
        data: {
            labels: <?php echo wp_json_encode( $device_labels ); ?>,
            datasets: [{
                label: '세션',
                data: <?php echo wp_json_encode( $device_data ); ?>,
                backgroundColor: ['#6366f1', '#22c55e', '#f59e0b']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // 프로모션 닫기
    document.querySelector('.promo-dismiss')?.addEventListener('click', function() {
        this.closest('.acf-uja-promo-banner').style.display = 'none';
        localStorage.setItem('acf_uja_promo_dismissed', '1');
    });

    if (localStorage.getItem('acf_uja_promo_dismissed') === '1') {
        document.querySelector('.acf-uja-promo-banner').style.display = 'none';
    }
});
</script>
