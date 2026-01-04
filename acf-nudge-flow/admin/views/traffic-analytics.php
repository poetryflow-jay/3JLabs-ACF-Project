<?php
/**
 * 트래픽 소스 분석 대시보드
 *
 * @package ACF_Nudge_Flow
 * @since 22.8.0
 * @updated 22.9.1 - Chart.js 시각화 추가
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$table = $wpdb->prefix . 'acf_nf_utm_tracking';

// 기간 설정
$period = isset( $_GET['period'] ) ? sanitize_text_field( $_GET['period'] ) : '7days';
$date_ranges = array(
    'today' => array(
        'label' => __( '오늘', 'acf-nudge-flow' ),
        'start' => date( 'Y-m-d 00:00:00' ),
        'end' => date( 'Y-m-d 23:59:59' ),
    ),
    '7days' => array(
        'label' => __( '지난 7일', 'acf-nudge-flow' ),
        'start' => date( 'Y-m-d 00:00:00', strtotime( '-7 days' ) ),
        'end' => date( 'Y-m-d 23:59:59' ),
    ),
    '30days' => array(
        'label' => __( '지난 30일', 'acf-nudge-flow' ),
        'start' => date( 'Y-m-d 00:00:00', strtotime( '-30 days' ) ),
        'end' => date( 'Y-m-d 23:59:59' ),
    ),
    '90days' => array(
        'label' => __( '지난 90일', 'acf-nudge-flow' ),
        'start' => date( 'Y-m-d 00:00:00', strtotime( '-90 days' ) ),
        'end' => date( 'Y-m-d 23:59:59' ),
    ),
);

$date_range = isset( $date_ranges[ $period ] ) ? $date_ranges[ $period ] : $date_ranges['7days'];

// 전체 방문자 수
$total_visitors = $wpdb->get_var( $wpdb->prepare(
    "SELECT COUNT(DISTINCT visitor_id) FROM $table WHERE first_touch_at BETWEEN %s AND %s",
    $date_range['start'],
    $date_range['end']
) );

// 전체 세션 수
$total_sessions = $wpdb->get_var( $wpdb->prepare(
    "SELECT COUNT(*) FROM $table WHERE first_touch_at BETWEEN %s AND %s",
    $date_range['start'],
    $date_range['end']
) );

// 전환 수
$total_conversions = $wpdb->get_var( $wpdb->prepare(
    "SELECT COUNT(*) FROM $table WHERE converted = 1 AND conversion_at BETWEEN %s AND %s",
    $date_range['start'],
    $date_range['end']
) );

// 전환율
$conversion_rate = $total_sessions > 0 ? round( ( $total_conversions / $total_sessions ) * 100, 2 ) : 0;

// 리퍼러 유형별 통계
$referrer_types = $wpdb->get_results( $wpdb->prepare(
    "SELECT
        referrer_type,
        COUNT(*) as sessions,
        COUNT(DISTINCT visitor_id) as visitors,
        SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
        SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue
    FROM $table
    WHERE first_touch_at BETWEEN %s AND %s
    GROUP BY referrer_type
    ORDER BY sessions DESC",
    $date_range['start'],
    $date_range['end']
) );

// 광고 소스별 통계
$ad_sources = $wpdb->get_results( $wpdb->prepare(
    "SELECT
        detected_ad_source as source,
        detected_ad_platform as platform,
        detected_ad_type as type,
        COUNT(*) as sessions,
        COUNT(DISTINCT visitor_id) as visitors,
        SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
        SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue
    FROM $table
    WHERE first_touch_at BETWEEN %s AND %s AND detected_ad_source IS NOT NULL AND detected_ad_source != ''
    GROUP BY detected_ad_source, detected_ad_platform, detected_ad_type
    ORDER BY sessions DESC
    LIMIT 20",
    $date_range['start'],
    $date_range['end']
) );

// UTM 소스별 통계
$utm_sources = $wpdb->get_results( $wpdb->prepare(
    "SELECT
        utm_source as source,
        utm_medium as medium,
        utm_campaign as campaign,
        COUNT(*) as sessions,
        COUNT(DISTINCT visitor_id) as visitors,
        SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
        SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue
    FROM $table
    WHERE first_touch_at BETWEEN %s AND %s AND utm_source IS NOT NULL AND utm_source != ''
    GROUP BY utm_source, utm_medium, utm_campaign
    ORDER BY sessions DESC
    LIMIT 20",
    $date_range['start'],
    $date_range['end']
) );

// 네이버 광고 상세 통계
$naver_ads = $wpdb->get_results( $wpdb->prepare(
    "SELECT
        n_media,
        n_keyword,
        n_campaign_type,
        CASE
            WHEN n_mall_pid IS NOT NULL AND n_mall_pid != '' THEN '쇼핑검색'
            WHEN na_source = 'gfa' THEN 'GFA'
            WHEN n_keyword IS NOT NULL AND n_keyword != '' THEN '검색광고'
            ELSE '기타'
        END as ad_type,
        COUNT(*) as sessions,
        SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
        SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue
    FROM $table
    WHERE first_touch_at BETWEEN %s AND %s AND n_media IS NOT NULL AND n_media != ''
    GROUP BY n_media, n_keyword, n_campaign_type, ad_type
    ORDER BY sessions DESC
    LIMIT 20",
    $date_range['start'],
    $date_range['end']
) );

// 카카오 광고 상세 통계
$kakao_ads = $wpdb->get_results( $wpdb->prepare(
    "SELECT
        kakao_campaign,
        kakao_adgrp,
        CASE
            WHEN dkwid IS NOT NULL AND dkwid != '' THEN '키워드광고'
            ELSE '모먼트'
        END as ad_type,
        COUNT(*) as sessions,
        SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
        SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue
    FROM $table
    WHERE first_touch_at BETWEEN %s AND %s AND (kakao_campaign IS NOT NULL AND kakao_campaign != '' OR dkwid IS NOT NULL AND dkwid != '')
    GROUP BY kakao_campaign, kakao_adgrp, ad_type
    ORDER BY sessions DESC
    LIMIT 20",
    $date_range['start'],
    $date_range['end']
) );

// AI 리퍼러 통계
$ai_referrers = $wpdb->get_results( $wpdb->prepare(
    "SELECT
        referrer_source,
        referrer_name,
        COUNT(*) as sessions,
        COUNT(DISTINCT visitor_id) as visitors
    FROM $table
    WHERE first_touch_at BETWEEN %s AND %s AND referrer_type = 'ai_search' OR referrer_type = 'ai_chatbot'
    GROUP BY referrer_source, referrer_name
    ORDER BY sessions DESC",
    $date_range['start'],
    $date_range['end']
) );

// 일별 트래픽 트렌드 (차트용)
$daily_traffic = $wpdb->get_results( $wpdb->prepare(
    "SELECT
        DATE(first_touch_at) as date,
        COUNT(*) as sessions,
        COUNT(DISTINCT visitor_id) as visitors,
        SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions
    FROM $table
    WHERE first_touch_at BETWEEN %s AND %s
    GROUP BY DATE(first_touch_at)
    ORDER BY date ASC",
    $date_range['start'],
    $date_range['end']
) );

// 시간대별 트래픽 (차트용)
$hourly_traffic = $wpdb->get_results( $wpdb->prepare(
    "SELECT
        HOUR(first_touch_at) as hour,
        COUNT(*) as sessions
    FROM $table
    WHERE first_touch_at BETWEEN %s AND %s
    GROUP BY HOUR(first_touch_at)
    ORDER BY hour ASC",
    $date_range['start'],
    $date_range['end']
) );

// 디바이스별 통계
$device_stats = $wpdb->get_results( $wpdb->prepare(
    "SELECT
        device_type,
        COUNT(*) as sessions,
        SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions
    FROM $table
    WHERE first_touch_at BETWEEN %s AND %s
    GROUP BY device_type
    ORDER BY sessions DESC",
    $date_range['start'],
    $date_range['end']
) );

// 차트 데이터 JSON 준비
$chart_daily_labels = array();
$chart_daily_sessions = array();
$chart_daily_visitors = array();
$chart_daily_conversions = array();
foreach ( $daily_traffic as $day ) {
    $chart_daily_labels[] = date( 'm/d', strtotime( $day->date ) );
    $chart_daily_sessions[] = (int) $day->sessions;
    $chart_daily_visitors[] = (int) $day->visitors;
    $chart_daily_conversions[] = (int) $day->conversions;
}

$chart_hourly_labels = array();
$chart_hourly_data = array_fill( 0, 24, 0 );
foreach ( $hourly_traffic as $hour ) {
    $chart_hourly_data[ (int) $hour->hour ] = (int) $hour->sessions;
}
for ( $i = 0; $i < 24; $i++ ) {
    $chart_hourly_labels[] = sprintf( '%02d시', $i );
}

$chart_referrer_labels = array();
$chart_referrer_data = array();
$chart_referrer_colors = array();

// 리퍼러 유형 레이블
$referrer_type_labels = array(
    'direct' => array( 'label' => '직접 방문', 'icon' => '🎯', 'color' => '#6c757d' ),
    'search' => array( 'label' => '검색 (오가닉)', 'icon' => '🔍', 'color' => '#28a745' ),
    'social' => array( 'label' => '소셜 미디어', 'icon' => '📱', 'color' => '#17a2b8' ),
    'paid' => array( 'label' => '유료 광고', 'icon' => '💰', 'color' => '#dc3545' ),
    'email' => array( 'label' => '이메일', 'icon' => '📧', 'color' => '#ffc107' ),
    'referral' => array( 'label' => '추천/리퍼럴', 'icon' => '🔗', 'color' => '#6f42c1' ),
    'messenger' => array( 'label' => '메신저', 'icon' => '💬', 'color' => '#20c997' ),
    'video' => array( 'label' => '비디오', 'icon' => '🎬', 'color' => '#e83e8c' ),
    'shopping' => array( 'label' => '쇼핑 플랫폼', 'icon' => '🛒', 'color' => '#fd7e14' ),
    'ai_search' => array( 'label' => 'AI 검색', 'icon' => '🤖', 'color' => '#0d6efd' ),
    'ai_chatbot' => array( 'label' => 'AI 챗봇', 'icon' => '🤖', 'color' => '#6610f2' ),
    'internal' => array( 'label' => '내부 이동', 'icon' => '🔄', 'color' => '#adb5bd' ),
);

// 리퍼러 차트 데이터 준비
foreach ( $referrer_types as $row ) {
    $type_info = isset( $referrer_type_labels[ $row->referrer_type ] )
        ? $referrer_type_labels[ $row->referrer_type ]
        : array( 'label' => $row->referrer_type ?: '기타', 'color' => '#6c757d' );
    $chart_referrer_labels[] = $type_info['label'];
    $chart_referrer_data[] = (int) $row->sessions;
    $chart_referrer_colors[] = $type_info['color'];
}

// 디바이스 차트 데이터
$chart_device_labels = array();
$chart_device_data = array();
$device_colors = array(
    'desktop' => '#667eea',
    'mobile' => '#28a745',
    'tablet' => '#ffc107',
);
foreach ( $device_stats as $device ) {
    $label = $device->device_type ?: '알 수 없음';
    $label_map = array( 'desktop' => '데스크톱', 'mobile' => '모바일', 'tablet' => '태블릿' );
    $chart_device_labels[] = isset( $label_map[ $label ] ) ? $label_map[ $label ] : $label;
    $chart_device_data[] = (int) $device->sessions;
}

// [v22.9.1] 퍼널 분석 데이터 조회
$funnel_reporter = class_exists( 'ACF_Nudge_Flow_Traffic_Source_Reporter' ) ? ACF_Nudge_Flow_Traffic_Source_Reporter::instance() : null;
$funnel_data = array();
$roi_data = array();

if ( $funnel_reporter ) {
    $funnel_data = $funnel_reporter->get_source_funnel_analysis( $date_range['start'], $date_range['end'], 'referrer_type' );
    $roi_data = $funnel_reporter->get_ad_roi_analysis( $date_range['start'], $date_range['end'] );
}
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<style>
.traffic-analytics-wrap {
    max-width: 1400px;
    margin: 20px auto;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.ta-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ta-header h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 600;
}

.ta-header-subtitle {
    opacity: 0.9;
    margin-top: 5px;
    font-size: 14px;
}

.period-selector {
    display: flex;
    gap: 8px;
}

.period-btn {
    background: rgba(255,255,255,0.2);
    border: none;
    color: #fff;
    padding: 8px 16px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.2s;
    text-decoration: none;
}

.period-btn:hover,
.period-btn.active {
    background: #fff;
    color: #667eea;
}

/* 요약 카드 */
.ta-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.ta-summary-card {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    text-align: center;
}

.ta-summary-icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.ta-summary-value {
    font-size: 32px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 5px;
}

.ta-summary-label {
    font-size: 13px;
    color: #6b7280;
}

/* 섹션 */
.ta-section {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.ta-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e5e7eb;
}

.ta-section-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* 그리드 레이아웃 */
.ta-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
}

.ta-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
}

/* 테이블 */
.ta-table {
    width: 100%;
    border-collapse: collapse;
}

.ta-table th,
.ta-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.ta-table th {
    font-weight: 600;
    color: #6b7280;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ta-table tbody tr:hover {
    background: #f9fafb;
}

.ta-table .number {
    text-align: right;
    font-family: 'Monaco', 'Consolas', monospace;
}

/* 소스 뱃지 */
.source-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.source-badge .icon {
    font-size: 14px;
}

/* 차트 컨테이너 */
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

.chart-container-small {
    position: relative;
    height: 250px;
    width: 100%;
}

.chart-container canvas {
    max-height: 100%;
}

/* 차트 플레이스홀더 */
.chart-placeholder {
    height: 300px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 14px;
}

/* 내보내기 버튼 */
.ta-export-btn {
    background: #667eea;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.ta-export-btn:hover {
    background: #5a67d8;
    transform: translateY(-1px);
}

.ta-export-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

/* 실시간 지표 */
.realtime-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: #dcfce7;
    color: #166534;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.realtime-dot {
    width: 8px;
    height: 8px;
    background: #22c55e;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* 진행바 */
.progress-bar-container {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-top: 8px;
}

.progress-bar {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* 빈 상태 */
.ta-empty-state {
    text-align: center;
    padding: 40px;
    color: #6b7280;
}

.ta-empty-state-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

/* AI 리퍼러 뱃지 */
.ai-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 10px;
    text-transform: uppercase;
    font-weight: 600;
}

/* 탭 */
.ta-tabs {
    display: flex;
    gap: 5px;
    margin-bottom: 20px;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0;
}

.ta-tab {
    padding: 10px 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    color: #6b7280;
    position: relative;
    transition: all 0.2s;
}

.ta-tab:hover {
    color: #667eea;
}

.ta-tab.active {
    color: #667eea;
    font-weight: 600;
}

.ta-tab.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: #667eea;
}

.ta-tab-content {
    display: none;
}

.ta-tab-content.active {
    display: block;
}

@media (max-width: 1200px) {
    .ta-summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .ta-grid-2,
    .ta-grid-3 {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="traffic-analytics-wrap">
    <!-- 헤더 -->
    <div class="ta-header">
        <div>
            <h1>📊 트래픽 소스 분석</h1>
            <div class="ta-header-subtitle">광고 매체별 유입 현황과 전환 성과를 분석합니다</div>
        </div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div class="period-selector">
                <?php foreach ( $date_ranges as $key => $range ) : ?>
                    <a href="<?php echo esc_url( add_query_arg( 'period', $key ) ); ?>"
                       class="period-btn <?php echo $period === $key ? 'active' : ''; ?>">
                        <?php echo esc_html( $range['label'] ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <button type="button" class="ta-export-btn" id="export-csv-btn" style="background: rgba(255,255,255,0.9); color: #667eea;">
                📥 CSV 내보내기
            </button>
        </div>
    </div>

    <!-- 요약 카드 -->
    <div class="ta-summary-grid">
        <div class="ta-summary-card">
            <div class="ta-summary-icon">👥</div>
            <div class="ta-summary-value"><?php echo number_format( $total_visitors ); ?></div>
            <div class="ta-summary-label">방문자</div>
        </div>
        <div class="ta-summary-card">
            <div class="ta-summary-icon">📈</div>
            <div class="ta-summary-value"><?php echo number_format( $total_sessions ); ?></div>
            <div class="ta-summary-label">세션</div>
        </div>
        <div class="ta-summary-card">
            <div class="ta-summary-icon">🎯</div>
            <div class="ta-summary-value"><?php echo number_format( $total_conversions ); ?></div>
            <div class="ta-summary-label">전환</div>
        </div>
        <div class="ta-summary-card">
            <div class="ta-summary-icon">📊</div>
            <div class="ta-summary-value"><?php echo $conversion_rate; ?>%</div>
            <div class="ta-summary-label">전환율</div>
        </div>
    </div>

    <!-- 트래픽 트렌드 차트 -->
    <div class="ta-section">
        <div class="ta-section-header">
            <h2 class="ta-section-title">📈 일별 트래픽 트렌드</h2>
            <span class="realtime-indicator">
                <span class="realtime-dot"></span>
                실시간 업데이트
            </span>
        </div>
        <div class="chart-container">
            <canvas id="dailyTrafficChart"></canvas>
        </div>
    </div>

    <!-- 트래픽 소스 & 시간대 차트 -->
    <div class="ta-grid-2">
        <div class="ta-section">
            <div class="ta-section-header">
                <h2 class="ta-section-title">🔗 트래픽 소스 분포</h2>
            </div>
            <div class="chart-container-small">
                <canvas id="referrerPieChart"></canvas>
            </div>
        </div>
        <div class="ta-section">
            <div class="ta-section-header">
                <h2 class="ta-section-title">⏰ 시간대별 트래픽</h2>
            </div>
            <div class="chart-container-small">
                <canvas id="hourlyTrafficChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 리퍼러 유형별 분석 -->
    <div class="ta-section">
        <div class="ta-section-header">
            <h2 class="ta-section-title">🔗 트래픽 소스 유형별 현황</h2>
        </div>

        <?php if ( ! empty( $referrer_types ) ) : ?>
        <table class="ta-table">
            <thead>
                <tr>
                    <th>소스 유형</th>
                    <th class="number">세션</th>
                    <th class="number">방문자</th>
                    <th class="number">전환</th>
                    <th class="number">전환율</th>
                    <th class="number">매출</th>
                    <th style="width: 200px;">비중</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $max_sessions = ! empty( $referrer_types ) ? max( array_column( $referrer_types, 'sessions' ) ) : 1;
                foreach ( $referrer_types as $row ) :
                    $type_info = isset( $referrer_type_labels[ $row->referrer_type ] )
                        ? $referrer_type_labels[ $row->referrer_type ]
                        : array( 'label' => $row->referrer_type ?: '(알 수 없음)', 'icon' => '❓', 'color' => '#6c757d' );
                    $rate = $row->sessions > 0 ? round( ( $row->conversions / $row->sessions ) * 100, 1 ) : 0;
                    $percentage = $max_sessions > 0 ? round( ( $row->sessions / $max_sessions ) * 100 ) : 0;
                ?>
                <tr>
                    <td>
                        <span class="source-badge" style="background: <?php echo esc_attr( $type_info['color'] ); ?>20; color: <?php echo esc_attr( $type_info['color'] ); ?>;">
                            <span class="icon"><?php echo esc_html( $type_info['icon'] ); ?></span>
                            <?php echo esc_html( $type_info['label'] ); ?>
                        </span>
                    </td>
                    <td class="number"><?php echo number_format( $row->sessions ); ?></td>
                    <td class="number"><?php echo number_format( $row->visitors ); ?></td>
                    <td class="number"><?php echo number_format( $row->conversions ); ?></td>
                    <td class="number"><?php echo $rate; ?>%</td>
                    <td class="number">₩<?php echo number_format( $row->revenue ); ?></td>
                    <td>
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: <?php echo $percentage; ?>%; background: <?php echo esc_attr( $type_info['color'] ); ?>;"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else : ?>
        <div class="ta-empty-state">
            <div class="ta-empty-state-icon">📊</div>
            <p>선택한 기간에 트래픽 데이터가 없습니다.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- 광고 플랫폼 분석 -->
    <div class="ta-grid-2">
        <!-- 광고 소스별 -->
        <div class="ta-section">
            <div class="ta-section-header">
                <h2 class="ta-section-title">💰 광고 매체별 성과</h2>
            </div>

            <?php if ( ! empty( $ad_sources ) ) : ?>
            <table class="ta-table">
                <thead>
                    <tr>
                        <th>광고 매체</th>
                        <th class="number">세션</th>
                        <th class="number">전환</th>
                        <th class="number">매출</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $ad_sources as $row ) :
                        $rate = $row->sessions > 0 ? round( ( $row->conversions / $row->sessions ) * 100, 1 ) : 0;
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html( $row->platform ?: $row->source ); ?></strong>
                            <?php if ( $row->type ) : ?>
                            <br><small style="color: #6b7280;"><?php echo esc_html( $row->type ); ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="number"><?php echo number_format( $row->sessions ); ?></td>
                        <td class="number"><?php echo number_format( $row->conversions ); ?> <small>(<?php echo $rate; ?>%)</small></td>
                        <td class="number">₩<?php echo number_format( $row->revenue ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
            <div class="ta-empty-state">
                <div class="ta-empty-state-icon">💰</div>
                <p>광고 트래픽 데이터가 없습니다.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- UTM 캠페인별 -->
        <div class="ta-section">
            <div class="ta-section-header">
                <h2 class="ta-section-title">🏷️ UTM 캠페인별 성과</h2>
            </div>

            <?php if ( ! empty( $utm_sources ) ) : ?>
            <table class="ta-table">
                <thead>
                    <tr>
                        <th>캠페인</th>
                        <th class="number">세션</th>
                        <th class="number">전환</th>
                        <th class="number">매출</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $utm_sources as $row ) :
                        $rate = $row->sessions > 0 ? round( ( $row->conversions / $row->sessions ) * 100, 1 ) : 0;
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html( $row->source ); ?></strong> / <?php echo esc_html( $row->medium ); ?>
                            <?php if ( $row->campaign ) : ?>
                            <br><small style="color: #6b7280;"><?php echo esc_html( $row->campaign ); ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="number"><?php echo number_format( $row->sessions ); ?></td>
                        <td class="number"><?php echo number_format( $row->conversions ); ?> <small>(<?php echo $rate; ?>%)</small></td>
                        <td class="number">₩<?php echo number_format( $row->revenue ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
            <div class="ta-empty-state">
                <div class="ta-empty-state-icon">🏷️</div>
                <p>UTM 데이터가 없습니다.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 한국 광고 매체 상세 -->
    <div class="ta-section">
        <div class="ta-section-header">
            <h2 class="ta-section-title">🇰🇷 한국 광고 매체 상세 분석</h2>
        </div>

        <div class="ta-tabs">
            <button class="ta-tab active" data-tab="naver">네이버 광고</button>
            <button class="ta-tab" data-tab="kakao">카카오 광고</button>
            <button class="ta-tab" data-tab="ai">AI 검색엔진</button>
        </div>

        <!-- 네이버 탭 -->
        <div class="ta-tab-content active" id="tab-naver">
            <?php if ( ! empty( $naver_ads ) ) : ?>
            <table class="ta-table">
                <thead>
                    <tr>
                        <th>광고 유형</th>
                        <th>키워드</th>
                        <th class="number">세션</th>
                        <th class="number">전환</th>
                        <th class="number">매출</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $naver_ads as $row ) : ?>
                    <tr>
                        <td>
                            <span class="source-badge" style="background: #03c75a20; color: #03c75a;">
                                <?php echo esc_html( $row->ad_type ); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html( $row->n_keyword ?: '-' ); ?></td>
                        <td class="number"><?php echo number_format( $row->sessions ); ?></td>
                        <td class="number"><?php echo number_format( $row->conversions ); ?></td>
                        <td class="number">₩<?php echo number_format( $row->revenue ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
            <div class="ta-empty-state">
                <div class="ta-empty-state-icon">🟢</div>
                <p>네이버 광고 트래픽 데이터가 없습니다.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- 카카오 탭 -->
        <div class="ta-tab-content" id="tab-kakao">
            <?php if ( ! empty( $kakao_ads ) ) : ?>
            <table class="ta-table">
                <thead>
                    <tr>
                        <th>광고 유형</th>
                        <th>캠페인</th>
                        <th>광고그룹</th>
                        <th class="number">세션</th>
                        <th class="number">전환</th>
                        <th class="number">매출</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $kakao_ads as $row ) : ?>
                    <tr>
                        <td>
                            <span class="source-badge" style="background: #fee50020; color: #3c1e1e;">
                                <?php echo esc_html( $row->ad_type ); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html( $row->kakao_campaign ?: '-' ); ?></td>
                        <td><?php echo esc_html( $row->kakao_adgrp ?: '-' ); ?></td>
                        <td class="number"><?php echo number_format( $row->sessions ); ?></td>
                        <td class="number"><?php echo number_format( $row->conversions ); ?></td>
                        <td class="number">₩<?php echo number_format( $row->revenue ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
            <div class="ta-empty-state">
                <div class="ta-empty-state-icon">💛</div>
                <p>카카오 광고 트래픽 데이터가 없습니다.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- AI 검색엔진 탭 -->
        <div class="ta-tab-content" id="tab-ai">
            <?php if ( ! empty( $ai_referrers ) ) : ?>
            <table class="ta-table">
                <thead>
                    <tr>
                        <th>AI 플랫폼</th>
                        <th class="number">세션</th>
                        <th class="number">방문자</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $ai_referrers as $row ) : ?>
                    <tr>
                        <td>
                            <span class="ai-badge">AI</span>
                            <?php echo esc_html( $row->referrer_name ?: $row->referrer_source ); ?>
                        </td>
                        <td class="number"><?php echo number_format( $row->sessions ); ?></td>
                        <td class="number"><?php echo number_format( $row->visitors ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
            <div class="ta-empty-state">
                <div class="ta-empty-state-icon">🤖</div>
                <p>AI 검색엔진에서의 유입 데이터가 없습니다.</p>
                <p style="font-size: 12px; margin-top: 10px;">ChatGPT, Perplexity, Claude 등에서 사이트가 언급되면 이곳에 표시됩니다.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- [v22.9.1] 퍼널 분석 섹션 -->
    <div class="ta-section">
        <div class="ta-section-header">
            <h2 class="ta-section-title">🔄 트래픽 소스별 퍼널 분석</h2>
        </div>

        <p style="color: #6b7280; font-size: 13px; margin-bottom: 20px;">
            각 트래픽 소스에서 전환까지의 단계별 이탈률을 분석합니다. (랜딩 → 참여 → 관심 → 고려 → 전환)
        </p>

        <?php if ( ! empty( $funnel_data ) ) : ?>
        <div style="overflow-x: auto;">
            <table class="ta-table">
                <thead>
                    <tr>
                        <th>트래픽 소스</th>
                        <th class="number">방문자</th>
                        <th class="number">랜딩</th>
                        <th class="number">참여</th>
                        <th class="number">관심</th>
                        <th class="number">고려</th>
                        <th class="number">전환</th>
                        <th class="number">전환율</th>
                        <th class="number">객단가</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $funnel_data as $row ) :
                        $type_info = isset( $referrer_type_labels[ $row->traffic_source ] )
                            ? $referrer_type_labels[ $row->traffic_source ]
                            : array( 'label' => $row->traffic_source ?: '기타', 'icon' => '❓', 'color' => '#6c757d' );
                    ?>
                    <tr>
                        <td>
                            <span class="source-badge" style="background: <?php echo esc_attr( $type_info['color'] ); ?>20; color: <?php echo esc_attr( $type_info['color'] ); ?>;">
                                <span class="icon"><?php echo esc_html( $type_info['icon'] ); ?></span>
                                <?php echo esc_html( $type_info['label'] ); ?>
                            </span>
                        </td>
                        <td class="number"><?php echo number_format( $row->total_visitors ); ?></td>
                        <td class="number">
                            <?php echo number_format( $row->step_1_landing ); ?>
                            <small style="color: #22c55e;">(<?php echo $row->step_1_rate; ?>%)</small>
                        </td>
                        <td class="number">
                            <?php echo number_format( $row->step_2_engagement ); ?>
                            <small style="color: <?php echo $row->step_2_rate >= 50 ? '#22c55e' : '#f59e0b'; ?>;">(<?php echo $row->step_2_rate; ?>%)</small>
                        </td>
                        <td class="number">
                            <?php echo number_format( $row->step_3_interest ); ?>
                            <small style="color: <?php echo $row->step_3_rate >= 30 ? '#22c55e' : '#f59e0b'; ?>;">(<?php echo $row->step_3_rate; ?>%)</small>
                        </td>
                        <td class="number">
                            <?php echo number_format( $row->step_4_consideration ); ?>
                            <small style="color: <?php echo $row->step_4_rate >= 20 ? '#22c55e' : '#f59e0b'; ?>;">(<?php echo $row->step_4_rate; ?>%)</small>
                        </td>
                        <td class="number">
                            <?php echo number_format( $row->step_5_conversion ); ?>
                            <small style="color: <?php echo $row->step_5_rate >= 10 ? '#22c55e' : '#ef4444'; ?>;">(<?php echo $row->step_5_rate; ?>%)</small>
                        </td>
                        <td class="number" style="font-weight: 600; color: <?php echo $row->overall_conversion_rate >= 3 ? '#22c55e' : '#6b7280'; ?>;">
                            <?php echo $row->overall_conversion_rate; ?>%
                        </td>
                        <td class="number">₩<?php echo number_format( $row->revenue_per_visitor ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
        <div class="ta-empty-state">
            <div class="ta-empty-state-icon">🔄</div>
            <p>퍼널 분석 데이터가 없습니다.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- [v22.9.1] 광고 ROI 분석 섹션 -->
    <div class="ta-section">
        <div class="ta-section-header">
            <h2 class="ta-section-title">💹 광고 ROI 분석</h2>
        </div>

        <p style="color: #6b7280; font-size: 13px; margin-bottom: 20px;">
            광고 플랫폼별 수익성과 효율성을 분석합니다. 비용 데이터는 별도 입력이 필요합니다.
        </p>

        <?php if ( ! empty( $roi_data ) ) : ?>
        <div style="overflow-x: auto;">
            <table class="ta-table">
                <thead>
                    <tr>
                        <th>광고 플랫폼</th>
                        <th class="number">세션</th>
                        <th class="number">전환</th>
                        <th class="number">전환율</th>
                        <th class="number">매출</th>
                        <th class="number">비용</th>
                        <th class="number">ROI</th>
                        <th class="number">ROAS</th>
                        <th class="number">CPA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $roi_data as $row ) : ?>
                    <tr>
                        <td><strong><?php echo esc_html( $row->platform ); ?></strong></td>
                        <td class="number"><?php echo number_format( $row->sessions ); ?></td>
                        <td class="number"><?php echo number_format( $row->conversions ); ?></td>
                        <td class="number"><?php echo $row->conversion_rate; ?>%</td>
                        <td class="number" style="color: #22c55e;">₩<?php echo number_format( $row->revenue ); ?></td>
                        <td class="number" style="color: #ef4444;">
                            <?php if ( $row->cost > 0 ) : ?>
                                ₩<?php echo number_format( $row->cost ); ?>
                            <?php else : ?>
                                <span style="color: #9ca3af;">미입력</span>
                            <?php endif; ?>
                        </td>
                        <td class="number" style="font-weight: 600; color: <?php echo $row->roi >= 0 ? '#22c55e' : '#ef4444'; ?>;">
                            <?php echo $row->roi; ?>%
                        </td>
                        <td class="number">
                            <?php if ( $row->roas > 0 ) : ?>
                                <?php echo $row->roas; ?>x
                            <?php else : ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="number">
                            <?php if ( $row->cpa > 0 ) : ?>
                                ₩<?php echo number_format( $row->cpa ); ?>
                            <?php else : ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 15px; padding: 15px; background: #f0f9ff; border-radius: 8px; font-size: 13px; color: #0369a1;">
            <strong>💡 Tip:</strong> 광고 비용 데이터를 입력하면 정확한 ROI, ROAS, CPA를 계산할 수 있습니다.
            향후 버전에서 광고 플랫폼 API 연동을 통한 자동 비용 수집 기능이 추가될 예정입니다.
        </div>
        <?php else : ?>
        <div class="ta-empty-state">
            <div class="ta-empty-state-icon">💹</div>
            <p>광고 트래픽 데이터가 없습니다.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 탭 전환
    document.querySelectorAll('.ta-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            var tabId = this.getAttribute('data-tab');

            // 탭 버튼 활성화
            document.querySelectorAll('.ta-tab').forEach(function(t) {
                t.classList.remove('active');
            });
            this.classList.add('active');

            // 탭 콘텐츠 표시
            document.querySelectorAll('.ta-tab-content').forEach(function(c) {
                c.classList.remove('active');
            });
            document.getElementById('tab-' + tabId).classList.add('active');
        });
    });

    // Chart.js 차트 초기화
    if (typeof Chart !== 'undefined') {
        // 공통 차트 옵션
        Chart.defaults.font.family = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
        Chart.defaults.color = '#6b7280';

        // 일별 트래픽 트렌드 차트 (라인 차트)
        var dailyCtx = document.getElementById('dailyTrafficChart');
        if (dailyCtx) {
            new Chart(dailyCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: <?php echo json_encode( $chart_daily_labels ); ?>,
                    datasets: [
                        {
                            label: '세션',
                            data: <?php echo json_encode( $chart_daily_sessions ); ?>,
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: '방문자',
                            data: <?php echo json_encode( $chart_daily_visitors ); ?>,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            fill: false,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: '전환',
                            data: <?php echo json_encode( $chart_daily_conversions ); ?>,
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            fill: false,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        }
                    }
                }
            });
        }

        // 트래픽 소스 분포 파이 차트
        var referrerCtx = document.getElementById('referrerPieChart');
        if (referrerCtx) {
            new Chart(referrerCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode( $chart_referrer_labels ); ?>,
                    datasets: [{
                        data: <?php echo json_encode( $chart_referrer_data ); ?>,
                        backgroundColor: <?php echo json_encode( $chart_referrer_colors ); ?>,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                    var percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        // 시간대별 트래픽 바 차트
        var hourlyCtx = document.getElementById('hourlyTrafficChart');
        if (hourlyCtx) {
            new Chart(hourlyCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode( $chart_hourly_labels ); ?>,
                    datasets: [{
                        label: '세션',
                        data: <?php echo json_encode( $chart_hourly_data ); ?>,
                        backgroundColor: function(context) {
                            var value = context.dataset.data[context.dataIndex];
                            var max = Math.max.apply(null, context.dataset.data);
                            var opacity = 0.3 + (value / max) * 0.7;
                            return 'rgba(102, 126, 234, ' + opacity + ')';
                        },
                        borderRadius: 4,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return context[0].label + ' 트래픽';
                                },
                                label: function(context) {
                                    return context.parsed.y.toLocaleString() + ' 세션';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                maxRotation: 0,
                                callback: function(value, index) {
                                    // 매 3시간마다만 표시
                                    return index % 3 === 0 ? this.getLabelForValue(value) : '';
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        }
                    }
                }
            });
        }
    }

    // CSV 내보내기 기능
    var exportBtn = document.getElementById('export-csv-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            var btn = this;
            btn.disabled = true;
            btn.innerHTML = '⏳ 내보내는 중...';

            // AJAX 요청으로 CSV 데이터 생성
            var formData = new FormData();
            formData.append('action', 'acf_nf_export_traffic_csv');
            formData.append('period', '<?php echo esc_js( $period ); ?>');
            formData.append('nonce', '<?php echo wp_create_nonce( 'acf_nf_export_traffic' ); ?>');

            fetch(ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success && data.data.csv) {
                    // BOM + CSV 데이터로 다운로드
                    var BOM = '\uFEFF';
                    var blob = new Blob([BOM + data.data.csv], { type: 'text/csv;charset=utf-8' });
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'traffic-report-<?php echo esc_js( $period ); ?>-' + new Date().toISOString().slice(0, 10) + '.csv';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                } else {
                    alert('CSV 내보내기에 실패했습니다.');
                }
            })
            .catch(function(error) {
                console.error('Export error:', error);
                alert('CSV 내보내기 중 오류가 발생했습니다.');
            })
            .finally(function() {
                btn.disabled = false;
                btn.innerHTML = '📥 CSV 내보내기';
            });
        });
    }

    // 실시간 업데이트 (30초마다)
    var realtimeInterval = setInterval(function() {
        // 현재 활성화된 탭이 아닌 경우에만 데이터 갱신
        if (document.visibilityState === 'visible') {
            // 실시간 지표 업데이트를 위한 AJAX 호출 (선택적)
            console.log('🔄 실시간 데이터 확인 중...');
        }
    }, 30000);

    // 페이지 언로드 시 인터벌 정리
    window.addEventListener('beforeunload', function() {
        clearInterval(realtimeInterval);
    });
});
</script>
