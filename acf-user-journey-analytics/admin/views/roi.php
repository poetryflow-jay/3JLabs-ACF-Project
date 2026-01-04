<?php
/**
 * ROI 분석 뷰
 *
 * @package ACF_User_Journey_Analytics
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap acf-uja-wrap">
    <h1>
        <span class="dashicons dashicons-chart-pie"></span>
        광고 ROI 분석
    </h1>

    <!-- 프로모션 배너 -->
    <div class="acf-uja-promo-banner promo-compact">
        <div class="promo-content">
            <strong>ROI 높은 트래픽 소스에 집중하세요</strong>
            <span>1-Click SEO로 고품질 오가닉 트래픽을 늘리고 광고비를 절감하세요.</span>
            <a href="https://wordpress.org/plugins/oneclick-seo-pro/" target="_blank" class="button button-primary button-small">1-Click SEO 설치</a>
        </div>
    </div>

    <!-- 기간 선택 -->
    <div class="acf-uja-period-selector">
        <a href="<?php echo esc_url( add_query_arg( 'period', 'today' ) ); ?>" class="<?php echo $period === 'today' ? 'active' : ''; ?>">오늘</a>
        <a href="<?php echo esc_url( add_query_arg( 'period', 'yesterday' ) ); ?>" class="<?php echo $period === 'yesterday' ? 'active' : ''; ?>">어제</a>
        <a href="<?php echo esc_url( add_query_arg( 'period', '7days' ) ); ?>" class="<?php echo $period === '7days' ? 'active' : ''; ?>">7일</a>
        <a href="<?php echo esc_url( add_query_arg( 'period', '30days' ) ); ?>" class="<?php echo $period === '30days' ? 'active' : ''; ?>">30일</a>
        <a href="<?php echo esc_url( add_query_arg( 'period', '90days' ) ); ?>" class="<?php echo $period === '90days' ? 'active' : ''; ?>">90일</a>
    </div>

    <!-- ROI 요약 카드 -->
    <?php
    $total_cost = 0;
    $total_revenue = 0;
    $total_conversions = 0;
    foreach ( $roi_data as $row ) {
        $total_cost += $row['cost'];
        $total_revenue += $row['revenue'];
        $total_conversions += $row['conversions'];
    }
    $total_roi = $total_cost > 0 ? ( ( $total_revenue - $total_cost ) / $total_cost ) * 100 : 0;
    $total_roas = $total_cost > 0 ? $total_revenue / $total_cost : 0;
    ?>
    <div class="acf-uja-summary-cards roi-cards">
        <div class="summary-card">
            <div class="card-icon" style="background: #ef4444;">
                <span class="dashicons dashicons-money-alt"></span>
            </div>
            <div class="card-content">
                <h3><?php echo number_format( $total_cost, 0 ); ?>원</h3>
                <p>총 광고비</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon" style="background: #22c55e;">
                <span class="dashicons dashicons-cart"></span>
            </div>
            <div class="card-content">
                <h3><?php echo number_format( $total_revenue, 0 ); ?>원</h3>
                <p>총 매출</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon" style="background: #6366f1;">
                <span class="dashicons dashicons-trending-up"></span>
            </div>
            <div class="card-content">
                <h3><?php echo round( $total_roi, 1 ); ?>%</h3>
                <p>ROI</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon" style="background: #f59e0b;">
                <span class="dashicons dashicons-performance"></span>
            </div>
            <div class="card-content">
                <h3><?php echo round( $total_roas, 2 ); ?>x</h3>
                <p>ROAS</p>
            </div>
        </div>
    </div>

    <!-- 플랫폼별 ROI 테이블 -->
    <div class="acf-uja-section">
        <h2>플랫폼별 광고 성과</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>플랫폼</th>
                    <th>광고비</th>
                    <th>매출</th>
                    <th>전환</th>
                    <th>ROI</th>
                    <th>ROAS</th>
                    <th>CPA</th>
                    <th>CPC</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $roi_data ) ) : ?>
                <tr><td colspan="8" style="text-align: center;">광고 데이터가 없습니다. 광고 비용 데이터를 입력하면 ROI를 계산할 수 있습니다.</td></tr>
                <?php else : ?>
                <?php foreach ( $roi_data as $row ) : ?>
                <tr>
                    <td><strong><?php echo esc_html( $row['platform'] ); ?></strong></td>
                    <td><?php echo number_format( $row['cost'], 0 ); ?>원</td>
                    <td><?php echo number_format( $row['revenue'], 0 ); ?>원</td>
                    <td><?php echo number_format( $row['conversions'] ); ?></td>
                    <td>
                        <span class="roi-badge <?php echo $row['roi'] >= 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $row['roi'] >= 0 ? '+' : ''; ?><?php echo $row['roi']; ?>%
                        </span>
                    </td>
                    <td><?php echo $row['roas']; ?>x</td>
                    <td><?php echo number_format( $row['cpa'], 0 ); ?>원</td>
                    <td><?php echo number_format( $row['cpc'], 0 ); ?>원</td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 광고 플랫폼 성과 (비용 없이) -->
    <div class="acf-uja-section">
        <h2>광고 플랫폼 트래픽 성과</h2>
        <p class="description">자동 감지된 유료 트래픽의 전환 성과입니다. 광고 비용을 입력하면 ROI를 계산할 수 있습니다.</p>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>플랫폼</th>
                    <th>소스</th>
                    <th>방문자</th>
                    <th>세션</th>
                    <th>전환</th>
                    <th>매출</th>
                    <th>전환율</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $ad_platform_stats ) ) : ?>
                <tr><td colspan="7" style="text-align: center;">감지된 광고 트래픽이 없습니다.</td></tr>
                <?php else : ?>
                <?php foreach ( $ad_platform_stats as $ad ) :
                    $conv_rate = $ad->sessions > 0 ? round( ( $ad->conversions / $ad->sessions ) * 100, 2 ) : 0;
                ?>
                <tr>
                    <td><strong><?php echo esc_html( $ad->platform ?: '(알 수 없음)' ); ?></strong></td>
                    <td><?php echo esc_html( $ad->source ); ?></td>
                    <td><?php echo number_format( $ad->visitors ); ?></td>
                    <td><?php echo number_format( $ad->sessions ); ?></td>
                    <td><?php echo number_format( $ad->conversions ); ?></td>
                    <td><?php echo number_format( $ad->revenue, 0 ); ?>원</td>
                    <td><?php echo $conv_rate; ?>%</td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ROI 용어 설명 -->
    <div class="acf-uja-info-box">
        <h3>ROI 지표 설명</h3>
        <ul>
            <li><strong>ROI (Return on Investment):</strong> 투자 수익률. (매출 - 광고비) / 광고비 x 100</li>
            <li><strong>ROAS (Return on Ad Spend):</strong> 광고비 대비 매출. 매출 / 광고비 (예: 3x = 1원당 3원 매출)</li>
            <li><strong>CPA (Cost per Acquisition):</strong> 전환당 비용. 광고비 / 전환 수</li>
            <li><strong>CPC (Cost per Click):</strong> 클릭당 비용. 광고비 / 클릭(세션) 수</li>
        </ul>
    </div>
</div>

<style>
.roi-cards { margin-bottom: 30px; }
.roi-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-weight: 600; font-size: 13px; }
.roi-badge.positive { background: #dcfce7; color: #16a34a; }
.roi-badge.negative { background: #fee2e2; color: #dc2626; }
</style>
