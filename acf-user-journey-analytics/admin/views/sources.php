<?php
/**
 * 트래픽 소스 분석 뷰
 *
 * @package ACF_User_Journey_Analytics
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap acf-uja-wrap">
    <h1>
        <span class="dashicons dashicons-share"></span>
        트래픽 소스 분석
    </h1>

    <!-- 프로모션 배너 -->
    <div class="acf-uja-promo-banner promo-compact">
        <div class="promo-content">
            <strong>트래픽 소스별 맞춤 마케팅 자동화</strong>
            <span>Nudge Flow로 방문자 세그먼트별 개인화된 넛지를 제공하세요.</span>
            <a href="https://wordpress.org/plugins/acf-nudge-flow/" target="_blank" class="button button-primary button-small">자세히 보기</a>
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

    <!-- 리퍼러 타입별 분석 -->
    <div class="acf-uja-section">
        <h2>리퍼러 유형별 분석</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>소스명</th>
                    <th>유형</th>
                    <th>방문자</th>
                    <th>세션</th>
                    <th>전환</th>
                    <th>매출</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $referrer_breakdown ) ) : ?>
                <tr><td colspan="6" style="text-align: center;">데이터가 없습니다.</td></tr>
                <?php else : ?>
                <?php foreach ( $referrer_breakdown as $ref ) : ?>
                <tr>
                    <td>
                        <strong><?php echo esc_html( $ref->referrer_name ?: $ref->referrer_source ?: '(알 수 없음)' ); ?></strong>
                    </td>
                    <td><span class="type-badge type-<?php echo esc_attr( $ref->referrer_type ); ?>"><?php echo esc_html( $ref->referrer_type ); ?></span></td>
                    <td><?php echo number_format( $ref->visitors ); ?></td>
                    <td><?php echo number_format( $ref->sessions ); ?></td>
                    <td><?php echo number_format( $ref->conversions ); ?></td>
                    <td><?php echo number_format( $ref->revenue, 0 ); ?>원</td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- UTM 캠페인 분석 -->
    <div class="acf-uja-section">
        <h2>UTM 캠페인 분석</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>소스</th>
                    <th>매체</th>
                    <th>캠페인</th>
                    <th>방문자</th>
                    <th>세션</th>
                    <th>전환</th>
                    <th>매출</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $utm_stats ) ) : ?>
                <tr><td colspan="7" style="text-align: center;">UTM 파라미터가 있는 트래픽이 없습니다.</td></tr>
                <?php else : ?>
                <?php foreach ( $utm_stats as $utm ) : ?>
                <tr>
                    <td><?php echo esc_html( $utm->utm_source ); ?></td>
                    <td><?php echo esc_html( $utm->utm_medium ); ?></td>
                    <td><?php echo esc_html( $utm->utm_campaign ?: '-' ); ?></td>
                    <td><?php echo number_format( $utm->visitors ); ?></td>
                    <td><?php echo number_format( $utm->sessions ); ?></td>
                    <td><?php echo number_format( $utm->conversions ); ?></td>
                    <td><?php echo number_format( $utm->revenue, 0 ); ?>원</td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 광고 플랫폼 분석 -->
    <div class="acf-uja-section">
        <h2>광고 플랫폼별 분석</h2>
        <p class="description">클릭 ID 및 광고 파라미터를 통해 자동 감지된 유료 트래픽입니다.</p>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>플랫폼</th>
                    <th>소스</th>
                    <th>방문자</th>
                    <th>세션</th>
                    <th>전환</th>
                    <th>매출</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $ad_platform_stats ) ) : ?>
                <tr><td colspan="6" style="text-align: center;">감지된 광고 트래픽이 없습니다.</td></tr>
                <?php else : ?>
                <?php foreach ( $ad_platform_stats as $ad ) : ?>
                <tr>
                    <td><strong><?php echo esc_html( $ad->platform ?: '(알 수 없음)' ); ?></strong></td>
                    <td><?php echo esc_html( $ad->source ); ?></td>
                    <td><?php echo number_format( $ad->visitors ); ?></td>
                    <td><?php echo number_format( $ad->sessions ); ?></td>
                    <td><?php echo number_format( $ad->conversions ); ?></td>
                    <td><?php echo number_format( $ad->revenue, 0 ); ?>원</td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 지원 광고 플랫폼 안내 -->
    <div class="acf-uja-info-box">
        <h3>지원하는 광고 플랫폼</h3>
        <div class="platform-grid">
            <div class="platform-item">
                <strong>글로벌</strong>
                <span>Google Ads, Meta (Facebook/Instagram), TikTok, Microsoft Ads, LinkedIn, Pinterest, Twitter(X), Snapchat</span>
            </div>
            <div class="platform-item">
                <strong>한국</strong>
                <span>네이버 검색광고, 네이버 쇼핑검색, 네이버 GFA, 카카오 모먼트, 카카오 키워드광고, 데이블, 모비온, 크리테오</span>
            </div>
            <div class="platform-item">
                <strong>AI 검색</strong>
                <span>ChatGPT, Perplexity, Google Gemini, Claude AI, Microsoft Copilot, Phind, You.com, Kagi</span>
            </div>
        </div>
    </div>
</div>
