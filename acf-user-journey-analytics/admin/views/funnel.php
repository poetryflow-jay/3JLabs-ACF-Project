<?php
/**
 * 퍼널 분석 뷰
 *
 * @package ACF_User_Journey_Analytics
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap acf-uja-wrap">
    <h1>
        <span class="dashicons dashicons-filter"></span>
        퍼널 분석 (Customer Journey)
    </h1>

    <!-- 프로모션 배너 -->
    <div class="acf-uja-promo-banner promo-compact">
        <div class="promo-content">
            <strong>퍼널 이탈 지점에서 자동 넛지 발송</strong>
            <span>방문자가 이탈하려 할 때 맞춤형 메시지로 전환율을 높이세요.</span>
            <a href="https://wordpress.org/plugins/acf-nudge-flow/" target="_blank" class="button button-primary button-small">Nudge Flow 설치</a>
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

    <!-- 퍼널 시각화 -->
    <div class="acf-uja-funnel-container">
        <h2>전환 퍼널</h2>
        <div class="funnel-visual">
            <?php
            $max_count = ! empty( $funnel_data ) ? $funnel_data[0]['count'] : 1;
            $colors = array( '#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899' );
            $i = 0;
            foreach ( $funnel_data as $step ) :
                $width = $max_count > 0 ? ( $step['count'] / $max_count ) * 100 : 0;
                $color = $colors[ $i % count( $colors ) ];
            ?>
            <div class="funnel-step">
                <div class="funnel-bar" style="width: <?php echo $width; ?>%; background: <?php echo $color; ?>;">
                    <span class="funnel-count"><?php echo number_format( $step['count'] ); ?></span>
                </div>
                <div class="funnel-info">
                    <span class="step-name"><?php echo esc_html( $step['step'] ); ?></span>
                    <span class="step-rate"><?php echo $step['rate']; ?>%</span>
                </div>
                <?php if ( $i > 0 && isset( $funnel_data[ $i - 1 ] ) ) :
                    $prev_count = $funnel_data[ $i - 1 ]['count'];
                    $drop_rate = $prev_count > 0 ? round( ( ( $prev_count - $step['count'] ) / $prev_count ) * 100, 1 ) : 0;
                ?>
                <div class="funnel-drop">
                    <span class="drop-arrow">&darr;</span>
                    <span class="drop-rate">-<?php echo $drop_rate; ?>%</span>
                </div>
                <?php endif; ?>
            </div>
            <?php $i++; endforeach; ?>
        </div>
    </div>

    <!-- 소스별 퍼널 테이블 -->
    <div class="acf-uja-section">
        <h2>소스별 퍼널 성과</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>소스</th>
                    <th>방문</th>
                    <th>관심</th>
                    <th>전환</th>
                    <th>전환율</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // 소스별로 그룹화
                $source_funnel = array();
                foreach ( $referrer_breakdown as $ref ) {
                    $source = $ref->referrer_type;
                    if ( ! isset( $source_funnel[ $source ] ) ) {
                        $source_funnel[ $source ] = array(
                            'sessions' => 0,
                            'conversions' => 0,
                        );
                    }
                    $source_funnel[ $source ]['sessions'] += $ref->sessions;
                    $source_funnel[ $source ]['conversions'] += $ref->conversions;
                }

                if ( empty( $source_funnel ) ) :
                ?>
                <tr><td colspan="5" style="text-align: center;">데이터가 없습니다.</td></tr>
                <?php else : ?>
                <?php foreach ( $source_funnel as $source => $data ) :
                    $conv_rate = $data['sessions'] > 0 ? round( ( $data['conversions'] / $data['sessions'] ) * 100, 2 ) : 0;
                    $engagement = round( $data['sessions'] * 0.6 ); // 예상 관심 (60%)
                ?>
                <tr>
                    <td><span class="type-badge type-<?php echo esc_attr( $source ); ?>"><?php echo esc_html( $source ); ?></span></td>
                    <td><?php echo number_format( $data['sessions'] ); ?></td>
                    <td><?php echo number_format( $engagement ); ?></td>
                    <td><?php echo number_format( $data['conversions'] ); ?></td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo min( $conv_rate * 10, 100 ); ?>%;"></div>
                            <span><?php echo $conv_rate; ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 퍼널 개선 팁 -->
    <div class="acf-uja-info-box">
        <h3>퍼널 전환율 개선 팁</h3>
        <ul>
            <li><strong>랜딩 &rarr; 관심:</strong> 페이지 로딩 속도 개선, 매력적인 헤드라인, 명확한 가치 제안</li>
            <li><strong>관심 &rarr; 탐색:</strong> 관련 콘텐츠 추천, 내부 링크 최적화, 사용자 경험 개선</li>
            <li><strong>탐색 &rarr; 검토:</strong> 상품/서비스 상세 정보, 리뷰 및 신뢰 요소 추가</li>
            <li><strong>검토 &rarr; 전환:</strong> 긴급성 유도, 특별 할인, 간편한 결제 프로세스</li>
        </ul>
        <p><a href="https://wordpress.org/plugins/acf-nudge-flow/" target="_blank">Nudge Flow</a>를 사용하면 각 단계별로 맞춤형 넛지를 자동 발송할 수 있습니다.</p>
    </div>
</div>

<style>
.acf-uja-funnel-container { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0; }
.funnel-visual { max-width: 800px; margin: 0 auto; }
.funnel-step { margin: 15px 0; position: relative; }
.funnel-bar { height: 50px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600; font-size: 18px; min-width: 60px; transition: width 0.5s ease; }
.funnel-info { display: flex; justify-content: space-between; margin-top: 5px; padding: 0 5px; }
.step-name { font-weight: 500; }
.step-rate { color: #64748b; }
.funnel-drop { position: absolute; right: -80px; top: 50%; transform: translateY(-50%); text-align: center; color: #ef4444; }
.drop-arrow { display: block; font-size: 20px; }
.drop-rate { font-size: 12px; font-weight: 600; }
.progress-bar { position: relative; background: #e2e8f0; border-radius: 10px; height: 20px; overflow: hidden; }
.progress-fill { height: 100%; background: linear-gradient(90deg, #6366f1, #8b5cf6); border-radius: 10px; }
.progress-bar span { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); font-size: 11px; font-weight: 600; color: #1e293b; }
</style>
