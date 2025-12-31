<?php
/**
 * Template Name: ACF CSS Landing Page
 * Template Post Type: page
 * 
 * ACF CSS Manager 랜딩 페이지 전용 템플릿
 * 
 * @package ACF_CSS_Landing
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// 커스터마이저 옵션
$hero_badge_text = acf_css_get_option( 'acf_hero_badge_text', '🎉 v6.2.1 출시 — AI 스타일 생성 기능 추가' );
$cta_button_url = acf_css_get_option( 'acf_cta_button_url', '/shop/' );
$beta_form_url = acf_css_get_option( 'acf_beta_form_url', '#beta' );
?>

<!-- Hero Section -->
<section class="acf-hero">
    <div class="acf-hero-content">
        <div class="acf-hero-badge">
            <?php echo esc_html( $hero_badge_text ); ?>
        </div>
        <h1>
            코딩 없이<br>
            <span class="highlight">전문가 수준의</span><br>
            웹사이트 디자인
        </h1>
        <p>
            AI가 당신의 아이디어를 CSS 스타일로 변환합니다.
            색상, 폰트, 버튼 — 모든 것을 한 곳에서 관리하세요.
        </p>
        <div class="acf-hero-buttons">
            <a href="<?php echo esc_url( $cta_button_url ); ?>" class="acf-btn acf-btn-primary">
                🚀 무료로 시작하기
            </a>
            <a href="#features" class="acf-btn acf-btn-secondary">
                자세히 알아보기 →
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="acf-features" id="features">
    <div class="acf-section-header">
        <h2>모든 스타일을 한 곳에서</h2>
        <p>더 이상 CSS 파일을 뒤지지 마세요. ACF CSS Manager가 모든 것을 해결합니다.</p>
    </div>
    <div class="acf-features-grid">
        <div class="acf-feature-card">
            <div class="acf-feature-icon">🎨</div>
            <h3>색상 팔레트 관리</h3>
            <p>브랜드 컬러부터 시스템 컬러까지, 일관된 색상 체계를 쉽게 관리하세요.</p>
        </div>
        <div class="acf-feature-card">
            <div class="acf-feature-icon">🔤</div>
            <h3>타이포그래피 시스템</h3>
            <p>제목, 본문, 버튼 폰트를 체계적으로 설정하고 전체 사이트에 적용하세요.</p>
        </div>
        <div class="acf-feature-card">
            <div class="acf-feature-icon">🤖</div>
            <h3>AI 스타일 생성</h3>
            <p>"고급스러운 블랙&골드"라고 말하면, AI가 완벽한 팔레트를 만들어줍니다.</p>
        </div>
        <div class="acf-feature-card">
            <div class="acf-feature-icon">☁️</div>
            <h3>클라우드 동기화</h3>
            <p>설정을 클라우드에 저장하고, 다른 사이트에 한 번에 복제하세요.</p>
        </div>
        <div class="acf-feature-card">
            <div class="acf-feature-icon">⚡</div>
            <h3>성능 최적화</h3>
            <p>CSS Tree Shaking으로 불필요한 코드를 제거하고, PageSpeed 점수를 높이세요.</p>
        </div>
        <div class="acf-feature-card">
            <div class="acf-feature-icon">🏢</div>
            <h3>에이전시 모드</h3>
            <p>Partner Hub에서 수십 개의 고객 사이트를 중앙에서 관리하세요.</p>
        </div>
    </div>
</section>

<!-- AI Section -->
<section class="acf-ai-section" id="ai">
    <div class="acf-ai-content">
        <div class="acf-ai-text">
            <h2>AI가 <span>스타일을 생성</span>합니다</h2>
            <p>
                복잡한 CSS 지식이 없어도 괜찮습니다. 
                원하는 분위기를 설명하면, AI가 전문가 수준의 
                색상 팔레트와 타이포그래피를 자동으로 생성합니다.
            </p>
            <ul class="acf-pricing-features">
                <li>자연어 프롬프트 지원</li>
                <li>Before/After 시각적 비교</li>
                <li>원클릭 적용 및 되돌리기</li>
                <li>클라우드에 즉시 저장</li>
            </ul>
        </div>
        <div class="acf-ai-demo">
            <div class="acf-ai-demo-header">
                <div class="acf-ai-demo-dot red"></div>
                <div class="acf-ai-demo-dot yellow"></div>
                <div class="acf-ai-demo-dot green"></div>
            </div>
            <div class="acf-ai-demo-content">
                <div class="acf-ai-prompt">
                    > "고급스러운 블랙&골드, 법률사무소 느낌"
                </div>
                <div class="acf-ai-result">
                    <div class="acf-ai-result-item">
                        <div class="acf-color-swatch" style="background: #1a1a1a;"></div>
                        <span>Primary: #1a1a1a (딥 블랙)</span>
                    </div>
                    <div class="acf-ai-result-item">
                        <div class="acf-color-swatch" style="background: #d4af37;"></div>
                        <span>Secondary: #d4af37 (골드)</span>
                    </div>
                    <div class="acf-ai-result-item">
                        <span>📝 Heading: Playfair Display</span>
                    </div>
                    <div class="acf-ai-result-item">
                        <span>📝 Body: Noto Sans KR</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="acf-pricing" id="pricing">
    <div class="acf-section-header">
        <h2>심플한 가격 정책</h2>
        <p>필요한 기능만큼만 선택하세요. 모든 플랜에 30일 환불 보장.</p>
    </div>
    <div class="acf-pricing-grid">
        <?php
        // WooCommerce 상품 연동
        if ( class_exists( 'WooCommerce' ) ) {
            // 'acf-css-free', 'acf-css-pro', 'acf-css-partner' SKU로 상품 검색
            $products = array(
                array(
                    'name'     => 'Free',
                    'price'    => '$0',
                    'period'   => '/영구',
                    'features' => array( '색상 팔레트 관리', '타이포그래피 설정', '3개 테마/플러그인 어댑터', '기본 지원' ),
                    'featured' => false,
                    'sku'      => 'acf-css-free',
                    'btn_text' => '무료 다운로드',
                ),
                array(
                    'name'     => 'PRO',
                    'price'    => '$49',
                    'period'   => '/년',
                    'features' => array( 'Free 모든 기능', '🤖 AI 스타일 생성', '☁️ 클라우드 동기화', 'CSS Tree Shaking', '무제한 어댑터', '우선 지원' ),
                    'featured' => true,
                    'sku'      => 'acf-css-pro',
                    'btn_text' => 'PRO 구매하기',
                ),
                array(
                    'name'     => 'Partner',
                    'price'    => '$199',
                    'period'   => '/년',
                    'features' => array( 'PRO 모든 기능', 'Partner Hub (중앙 관제)', '멀티사이트 네트워크', 'Webhook 자동화', 'REST API', '전용 지원' ),
                    'featured' => false,
                    'sku'      => 'acf-css-partner',
                    'btn_text' => '문의하기',
                ),
            );
            
            foreach ( $products as $product ) :
                $product_obj = wc_get_product( wc_get_product_id_by_sku( $product['sku'] ) );
                $product_url = $product_obj ? $product_obj->get_permalink() : $cta_button_url;
                $btn_class = $product['featured'] ? 'acf-btn-primary' : 'acf-btn-secondary';
        ?>
        <div class="acf-pricing-card <?php echo $product['featured'] ? 'featured' : ''; ?>">
            <?php if ( $product['featured'] ) : ?>
            <div class="acf-pricing-badge">인기</div>
            <?php endif; ?>
            <h3><?php echo esc_html( $product['name'] ); ?></h3>
            <div class="price"><?php echo esc_html( $product['price'] ); ?><span><?php echo esc_html( $product['period'] ); ?></span></div>
            <ul class="acf-pricing-features">
                <?php foreach ( $product['features'] as $feature ) : ?>
                <li><?php echo esc_html( $feature ); ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="<?php echo esc_url( $product_url ); ?>" class="acf-btn <?php echo esc_attr( $btn_class ); ?>" style="width: 100%;">
                <?php echo esc_html( $product['btn_text'] ); ?>
            </a>
        </div>
        <?php 
            endforeach;
        } else {
            // WooCommerce 비활성화 시 정적 콘텐츠
        ?>
        <div class="acf-pricing-card">
            <h3>Free</h3>
            <div class="price">$0<span>/영구</span></div>
            <ul class="acf-pricing-features">
                <li>색상 팔레트 관리</li>
                <li>타이포그래피 설정</li>
                <li>3개 테마/플러그인 어댑터</li>
                <li>기본 지원</li>
            </ul>
            <a href="<?php echo esc_url( $cta_button_url ); ?>" class="acf-btn acf-btn-secondary" style="width: 100%;">무료 다운로드</a>
        </div>
        <div class="acf-pricing-card featured">
            <div class="acf-pricing-badge">인기</div>
            <h3>PRO</h3>
            <div class="price">$49<span>/년</span></div>
            <ul class="acf-pricing-features">
                <li>Free 모든 기능</li>
                <li>🤖 AI 스타일 생성</li>
                <li>☁️ 클라우드 동기화</li>
                <li>CSS Tree Shaking</li>
                <li>무제한 어댑터</li>
                <li>우선 지원</li>
            </ul>
            <a href="<?php echo esc_url( $cta_button_url ); ?>" class="acf-btn acf-btn-primary" style="width: 100%;">PRO 구매하기</a>
        </div>
        <div class="acf-pricing-card">
            <h3>Partner</h3>
            <div class="price">$199<span>/년</span></div>
            <ul class="acf-pricing-features">
                <li>PRO 모든 기능</li>
                <li>Partner Hub (중앙 관제)</li>
                <li>멀티사이트 네트워크</li>
                <li>Webhook 자동화</li>
                <li>REST API</li>
                <li>전용 지원</li>
            </ul>
            <a href="<?php echo esc_url( $cta_button_url ); ?>" class="acf-btn acf-btn-secondary" style="width: 100%;">문의하기</a>
        </div>
        <?php } ?>
    </div>
</section>

<!-- CTA Section -->
<section class="acf-cta" id="beta">
    <h2>지금 베타 테스터로 참여하세요</h2>
    <p>베타 테스터에게는 정식 출시 시 50% 할인 쿠폰을 드립니다.</p>
    <a href="<?php echo esc_url( $beta_form_url ); ?>" class="acf-btn acf-btn-primary">
        ✨ 베타 테스터 신청
    </a>
</section>

<?php
get_footer();

