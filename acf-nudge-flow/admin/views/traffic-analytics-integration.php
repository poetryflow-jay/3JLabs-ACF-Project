<?php
/**
 * User Journey Analytics 연동 페이지
 *
 * Nudge Flow와 User Journey Analytics 플러그인 연동 안내 및 데이터 표시
 *
 * @package ACF_Nudge_Flow
 * @since 22.10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// User Journey Analytics 플러그인 활성화 확인
$uja_active = class_exists( 'ACF_User_Journey_Analytics' ) && function_exists( 'acf_user_journey_analytics' );

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
);

$date_range = isset( $date_ranges[ $period ] ) ? $date_ranges[ $period ] : $date_ranges['7days'];

// User Journey Analytics에서 데이터 가져오기
$traffic_data = null;
$source_data = null;
$funnel_data = null;

if ( $uja_active ) {
    $traffic_data = ACF_User_Journey_Analytics::get_traffic_data( $date_range['start'], $date_range['end'], 'summary' );
    $source_data = ACF_User_Journey_Analytics::get_traffic_data( $date_range['start'], $date_range['end'], 'sources' );
    $funnel_data = ACF_User_Journey_Analytics::get_traffic_data( $date_range['start'], $date_range['end'], 'funnel' );
}

// 리퍼러 유형 레이블
$referrer_type_labels = array(
    'direct' => array( 'label' => '직접 방문', 'icon' => '🎯', 'color' => '#6c757d' ),
    'search' => array( 'label' => '검색 (오가닉)', 'icon' => '🔍', 'color' => '#28a745' ),
    'social' => array( 'label' => '소셜 미디어', 'icon' => '📱', 'color' => '#17a2b8' ),
    'paid' => array( 'label' => '유료 광고', 'icon' => '💰', 'color' => '#dc3545' ),
    'email' => array( 'label' => '이메일', 'icon' => '📧', 'color' => '#ffc107' ),
    'referral' => array( 'label' => '추천/리퍼럴', 'icon' => '🔗', 'color' => '#6f42c1' ),
    'ai_search' => array( 'label' => 'AI 검색', 'icon' => '🤖', 'color' => '#0d6efd' ),
);
?>

<style>
.nf-integration-wrap {
    max-width: 1200px;
    margin: 20px auto;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.nf-integration-header {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: #fff;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nf-integration-header h1 {
    margin: 0;
    font-size: 26px;
    font-weight: 600;
}

.nf-integration-header-subtitle {
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
    color: #16a34a;
}

/* 설치 안내 배너 */
.nf-install-banner {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 2px solid #0ea5e9;
    border-radius: 16px;
    padding: 40px;
    text-align: center;
    margin-bottom: 25px;
}

.nf-install-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.nf-install-title {
    font-size: 24px;
    font-weight: 700;
    color: #0369a1;
    margin-bottom: 15px;
}

.nf-install-description {
    font-size: 16px;
    color: #6b7280;
    max-width: 600px;
    margin: 0 auto 25px;
    line-height: 1.6;
}

.nf-install-features {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.nf-install-feature {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #374151;
}

.nf-install-feature-icon {
    width: 24px;
    height: 24px;
    background: #dcfce7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.nf-install-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    color: #fff;
    padding: 14px 28px;
    border-radius: 30px;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
}

.nf-install-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
    color: #fff;
}

.nf-free-badge {
    background: #fef3c7;
    color: #92400e;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
}

/* 연동 상태 표시 */
.nf-connection-status {
    background: #dcfce7;
    border: 1px solid #22c55e;
    border-radius: 8px;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
}

.nf-connection-dot {
    width: 12px;
    height: 12px;
    background: #22c55e;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.1); }
}

.nf-connection-text {
    flex: 1;
    color: #166534;
    font-weight: 500;
}

.nf-dashboard-link {
    background: #22c55e;
    color: #fff;
    padding: 8px 16px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s;
}

.nf-dashboard-link:hover {
    background: #16a34a;
    color: #fff;
}

/* 요약 카드 */
.nf-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.nf-summary-card {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    text-align: center;
}

.nf-summary-icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.nf-summary-value {
    font-size: 32px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 5px;
}

.nf-summary-label {
    font-size: 13px;
    color: #6b7280;
}

/* 섹션 */
.nf-section {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.nf-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e5e7eb;
}

.nf-section-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* 테이블 */
.nf-table {
    width: 100%;
    border-collapse: collapse;
}

.nf-table th,
.nf-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.nf-table th {
    font-weight: 600;
    color: #6b7280;
    font-size: 12px;
    text-transform: uppercase;
}

.nf-table tbody tr:hover {
    background: #f9fafb;
}

.nf-table .number {
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

/* 빈 상태 */
.nf-empty-state {
    text-align: center;
    padding: 40px;
    color: #6b7280;
}

.nf-empty-state-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

/* 그리드 */
.nf-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
}

/* 진행바 */
.progress-bar-container {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    border-radius: 4px;
}

/* 연동 안내 */
.nf-integration-info {
    background: #fffbeb;
    border: 1px solid #f59e0b;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
}

.nf-integration-info h3 {
    margin: 0 0 10px;
    color: #92400e;
    font-size: 16px;
}

.nf-integration-info p {
    margin: 0;
    color: #78350f;
    font-size: 14px;
}

@media (max-width: 1000px) {
    .nf-summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .nf-grid-2 {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="nf-integration-wrap">
    <!-- 헤더 -->
    <div class="nf-integration-header">
        <div>
            <h1>📊 트래픽 분석 연동</h1>
            <div class="nf-integration-header-subtitle">User Journey Analytics와 연동하여 트래픽 데이터를 활용합니다</div>
        </div>
        <?php if ( $uja_active ) : ?>
        <div class="period-selector">
            <?php foreach ( $date_ranges as $key => $range ) : ?>
                <a href="<?php echo esc_url( add_query_arg( 'period', $key ) ); ?>"
                   class="period-btn <?php echo $period === $key ? 'active' : ''; ?>">
                    <?php echo esc_html( $range['label'] ); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <?php if ( $uja_active ) : ?>
        <!-- 연동 상태 -->
        <div class="nf-connection-status">
            <div class="nf-connection-dot"></div>
            <div class="nf-connection-text">
                <strong>User Journey Analytics</strong> 플러그인과 연동됨 - 실시간 트래픽 데이터를 수신 중입니다
            </div>
            <a href="<?php echo admin_url( 'admin.php?page=acf-user-journey' ); ?>" class="nf-dashboard-link">
                전체 대시보드 보기 →
            </a>
        </div>

        <?php if ( $traffic_data ) : ?>
        <!-- 요약 카드 -->
        <div class="nf-summary-grid">
            <div class="nf-summary-card">
                <div class="nf-summary-icon">👥</div>
                <div class="nf-summary-value"><?php echo number_format( $traffic_data['visitors'] ?? 0 ); ?></div>
                <div class="nf-summary-label">방문자</div>
            </div>
            <div class="nf-summary-card">
                <div class="nf-summary-icon">📈</div>
                <div class="nf-summary-value"><?php echo number_format( $traffic_data['sessions'] ?? 0 ); ?></div>
                <div class="nf-summary-label">세션</div>
            </div>
            <div class="nf-summary-card">
                <div class="nf-summary-icon">🎯</div>
                <div class="nf-summary-value"><?php echo number_format( $traffic_data['conversions'] ?? 0 ); ?></div>
                <div class="nf-summary-label">전환</div>
            </div>
            <div class="nf-summary-card">
                <div class="nf-summary-icon">📊</div>
                <div class="nf-summary-value"><?php echo $traffic_data['conversion_rate'] ?? 0; ?>%</div>
                <div class="nf-summary-label">전환율</div>
            </div>
        </div>
        <?php endif; ?>

        <!-- 트래픽 소스 요약 -->
        <?php if ( ! empty( $source_data ) ) : ?>
        <div class="nf-section">
            <div class="nf-section-header">
                <h2 class="nf-section-title">🔗 트래픽 소스별 현황</h2>
                <a href="<?php echo admin_url( 'admin.php?page=acf-user-journey-sources' ); ?>" style="color: #22c55e; text-decoration: none; font-size: 14px;">
                    상세 보기 →
                </a>
            </div>
            <table class="nf-table">
                <thead>
                    <tr>
                        <th>소스</th>
                        <th class="number">세션</th>
                        <th class="number">전환</th>
                        <th class="number">전환율</th>
                        <th style="width: 200px;">비중</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $max_sessions = ! empty( $source_data ) ? max( array_column( $source_data, 'sessions' ) ) : 1;
                    $count = 0;
                    foreach ( $source_data as $row ) :
                        if ( $count >= 5 ) break;
                        $type_info = isset( $referrer_type_labels[ $row->referrer_type ] )
                            ? $referrer_type_labels[ $row->referrer_type ]
                            : array( 'label' => $row->referrer_type ?: '기타', 'icon' => '❓', 'color' => '#6c757d' );
                        $rate = $row->sessions > 0 ? round( ( $row->conversions / $row->sessions ) * 100, 1 ) : 0;
                        $percentage = $max_sessions > 0 ? round( ( $row->sessions / $max_sessions ) * 100 ) : 0;
                        $count++;
                    ?>
                    <tr>
                        <td>
                            <span class="source-badge" style="background: <?php echo esc_attr( $type_info['color'] ); ?>20; color: <?php echo esc_attr( $type_info['color'] ); ?>;">
                                <?php echo esc_html( $type_info['icon'] ); ?>
                                <?php echo esc_html( $type_info['label'] ); ?>
                            </span>
                        </td>
                        <td class="number"><?php echo number_format( $row->sessions ); ?></td>
                        <td class="number"><?php echo number_format( $row->conversions ); ?></td>
                        <td class="number"><?php echo $rate; ?>%</td>
                        <td>
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: <?php echo $percentage; ?>%; background: <?php echo esc_attr( $type_info['color'] ); ?>;"></div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Nudge Flow 활용 안내 -->
        <div class="nf-integration-info">
            <h3>💡 Nudge Flow에서 트래픽 데이터 활용하기</h3>
            <p>
                User Journey Analytics의 트래픽 소스 데이터를 기반으로 <strong>워크플로우 트리거</strong>를 설정할 수 있습니다.
                예: "네이버 광고로 유입된 방문자에게 특별 할인 넛지 표시", "Google 검색 유입 고객에게 SEO 관련 컨텐츠 추천" 등
            </p>
        </div>

        <!-- 퍼널 분석 요약 -->
        <?php if ( ! empty( $funnel_data ) ) : ?>
        <div class="nf-section">
            <div class="nf-section-header">
                <h2 class="nf-section-title">🔄 전환 퍼널 요약</h2>
                <a href="<?php echo admin_url( 'admin.php?page=acf-user-journey-funnel' ); ?>" style="color: #22c55e; text-decoration: none; font-size: 14px;">
                    상세 분석 보기 →
                </a>
            </div>
            <table class="nf-table">
                <thead>
                    <tr>
                        <th>소스</th>
                        <th class="number">방문자</th>
                        <th class="number">참여</th>
                        <th class="number">관심</th>
                        <th class="number">전환</th>
                        <th class="number">전환율</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 0;
                    foreach ( $funnel_data as $row ) :
                        if ( $count >= 5 ) break;
                        $type_info = isset( $referrer_type_labels[ $row->traffic_source ] )
                            ? $referrer_type_labels[ $row->traffic_source ]
                            : array( 'label' => $row->traffic_source ?: '기타', 'icon' => '❓', 'color' => '#6c757d' );
                        $count++;
                    ?>
                    <tr>
                        <td>
                            <span class="source-badge" style="background: <?php echo esc_attr( $type_info['color'] ); ?>20; color: <?php echo esc_attr( $type_info['color'] ); ?>;">
                                <?php echo esc_html( $type_info['icon'] ); ?>
                                <?php echo esc_html( $type_info['label'] ); ?>
                            </span>
                        </td>
                        <td class="number"><?php echo number_format( $row->total_visitors ?? 0 ); ?></td>
                        <td class="number"><?php echo number_format( $row->step_2_engagement ?? 0 ); ?></td>
                        <td class="number"><?php echo number_format( $row->step_3_interest ?? 0 ); ?></td>
                        <td class="number"><?php echo number_format( $row->step_5_conversion ?? 0 ); ?></td>
                        <td class="number" style="font-weight: 600; color: <?php echo ( $row->overall_conversion_rate ?? 0 ) >= 3 ? '#22c55e' : '#6b7280'; ?>;">
                            <?php echo $row->overall_conversion_rate ?? 0; ?>%
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    <?php else : ?>
        <!-- User Journey Analytics 미설치 시 설치 안내 -->
        <div class="nf-install-banner">
            <div class="nf-install-icon">📊</div>
            <h2 class="nf-install-title">User Journey Analytics로 트래픽을 분석하세요</h2>
            <p class="nf-install-description">
                무료 User Journey Analytics 플러그인을 설치하면 Nudge Flow에서 트래픽 데이터를 활용하여
                더 정교한 마케팅 자동화를 구현할 수 있습니다.
            </p>

            <div class="nf-install-features">
                <div class="nf-install-feature">
                    <div class="nf-install-feature-icon">✓</div>
                    <span>50+ 광고 플랫폼 자동 감지</span>
                </div>
                <div class="nf-install-feature">
                    <div class="nf-install-feature-icon">✓</div>
                    <span>AI 검색엔진 트래픽 추적</span>
                </div>
                <div class="nf-install-feature">
                    <div class="nf-install-feature-icon">✓</div>
                    <span>5단계 전환 퍼널 분석</span>
                </div>
                <div class="nf-install-feature">
                    <div class="nf-install-feature-icon">✓</div>
                    <span>실시간 대시보드</span>
                </div>
            </div>

            <a href="<?php echo admin_url( 'plugin-install.php?s=acf-user-journey-analytics&tab=search&type=term' ); ?>" class="nf-install-btn">
                <span class="nf-free-badge">무료</span>
                User Journey Analytics 설치하기
            </a>
        </div>

        <!-- 기능 상세 안내 -->
        <div class="nf-grid-2">
            <div class="nf-section">
                <div class="nf-section-header">
                    <h2 class="nf-section-title">🎯 트래픽 기반 넛지 트리거</h2>
                </div>
                <p style="color: #6b7280; line-height: 1.6;">
                    User Journey Analytics와 연동하면 트래픽 소스를 기반으로 넛지를 표시할 수 있습니다.
                </p>
                <ul style="color: #374151; line-height: 1.8; margin-top: 15px;">
                    <li><strong>네이버 광고</strong> 유입 고객에게 특별 할인 알림</li>
                    <li><strong>Google 검색</strong> 유입 고객에게 관련 컨텐츠 추천</li>
                    <li><strong>소셜 미디어</strong> 유입 고객에게 팔로우 요청</li>
                    <li><strong>재방문 고객</strong>에게 맞춤형 프로모션</li>
                </ul>
            </div>

            <div class="nf-section">
                <div class="nf-section-header">
                    <h2 class="nf-section-title">📈 전환 퍼널 최적화</h2>
                </div>
                <p style="color: #6b7280; line-height: 1.6;">
                    각 트래픽 소스별 전환 퍼널을 분석하여 이탈이 발생하는 지점에 자동으로 넛지를 표시합니다.
                </p>
                <ul style="color: #374151; line-height: 1.8; margin-top: 15px;">
                    <li><strong>랜딩 → 참여</strong> 이탈 시 환영 팝업</li>
                    <li><strong>참여 → 관심</strong> 이탈 시 인기 상품 추천</li>
                    <li><strong>관심 → 고려</strong> 이탈 시 리뷰/후기 표시</li>
                    <li><strong>고려 → 전환</strong> 이탈 시 할인 쿠폰 제공</li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>
