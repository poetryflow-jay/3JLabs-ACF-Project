<?php
/**
 * 광고 픽셀 설정 페이지
 * 
 * @package ACF_Nudge_Flow
 * @since 22.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$pixel_manager = ACF_Nudge_Flow_Ad_Pixel_Manager::instance();
$platforms = $pixel_manager->get_supported_platforms();
$saved_pixels = get_option( 'acf_nudge_flow_ad_pixels', array() );
$utm_settings = get_option( 'acf_nudge_flow_utm_settings', array(
    'enabled' => true,
    'store_duration' => 30,
    'attribution_model' => 'last_click',
) );

// 저장 처리
if ( isset( $_POST['save_ad_pixels'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'acf_nudge_flow_ad_pixels' ) ) {
    $new_pixels = array();
    
    foreach ( $platforms as $platform_id => $platform ) {
        if ( isset( $_POST['pixels'][ $platform_id ] ) ) {
            $pixel_data = $_POST['pixels'][ $platform_id ];
            $new_pixels[ $platform_id ] = array(
                'enabled' => isset( $pixel_data['enabled'] ) ? true : false,
                'pixel_id' => sanitize_text_field( $pixel_data['pixel_id'] ?? '' ),
                'custom_script' => wp_kses( $pixel_data['custom_script'] ?? '', array(
                    'script' => array( 'type' => array(), 'src' => array(), 'async' => array(), 'defer' => array() ),
                ) ),
                'conversion_events' => array_map( 'sanitize_text_field', $pixel_data['conversion_events'] ?? array() ),
            );
            
            // 플랫폼별 추가 필드
            if ( $platform_id === 'ga4' && isset( $pixel_data['measurement_id'] ) ) {
                $new_pixels[ $platform_id ]['measurement_id'] = sanitize_text_field( $pixel_data['measurement_id'] );
            }
            if ( $platform_id === 'google_ads' && isset( $pixel_data['conversion_label'] ) ) {
                $new_pixels[ $platform_id ]['conversion_label'] = sanitize_text_field( $pixel_data['conversion_label'] );
            }
        }
    }
    
    update_option( 'acf_nudge_flow_ad_pixels', $new_pixels );
    $saved_pixels = $new_pixels;
    
    // UTM 설정 저장
    if ( isset( $_POST['utm_settings'] ) ) {
        $new_utm_settings = array(
            'enabled' => isset( $_POST['utm_settings']['enabled'] ),
            'store_duration' => absint( $_POST['utm_settings']['store_duration'] ?? 30 ),
            'attribution_model' => sanitize_text_field( $_POST['utm_settings']['attribution_model'] ?? 'last_click' ),
        );
        update_option( 'acf_nudge_flow_utm_settings', $new_utm_settings );
        $utm_settings = $new_utm_settings;
    }
    
    echo '<div class="notice notice-success"><p>' . esc_html__( '광고 픽셀 설정이 저장되었습니다.', 'acf-nudge-flow' ) . '</p></div>';
}

// 플랫폼 카테고리 분류
$platform_categories = array(
    'global' => array(
        'label' => __( '글로벌 광고 플랫폼', 'acf-nudge-flow' ),
        'icon' => '🌍',
        'platforms' => array( 'meta', 'google_ads', 'ga4', 'tiktok', 'twitter', 'linkedin', 'pinterest', 'microsoft', 'criteo', 'taboola' ),
    ),
    'korea' => array(
        'label' => __( '한국 광고 플랫폼', 'acf-nudge-flow' ),
        'icon' => '🇰🇷',
        'platforms' => array( 'naver', 'kakao', 'toss', 'coupang' ),
    ),
    'japan' => array(
        'label' => __( '일본 광고 플랫폼', 'acf-nudge-flow' ),
        'icon' => '🇯🇵',
        'platforms' => array( 'line', 'yahoo_japan' ),
    ),
    'china' => array(
        'label' => __( '중국 광고 플랫폼', 'acf-nudge-flow' ),
        'icon' => '🇨🇳',
        'platforms' => array( 'baidu', 'weibo' ),
    ),
);
?>

<style>
.ad-pixels-wrap {
    max-width: 1200px;
    margin: 20px auto;
}

.ad-pixels-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.ad-pixels-header h1 {
    margin: 0 0 10px;
    font-size: 28px;
    font-weight: 700;
}

.ad-pixels-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 15px;
}

.ad-pixels-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 10px;
}

.ad-pixels-tab {
    padding: 12px 24px;
    background: #f3f4f6;
    border: none;
    border-radius: 8px 8px 0 0;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    transition: all 0.2s;
}

.ad-pixels-tab:hover {
    background: #e5e7eb;
}

.ad-pixels-tab.active {
    background: #667eea;
    color: #fff;
}

.ad-pixels-section {
    display: none;
    animation: fadeIn 0.3s ease;
}

.ad-pixels-section.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.platform-category {
    margin-bottom: 40px;
}

.platform-category-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e5e7eb;
}

.platform-category-header .icon {
    font-size: 24px;
}

.platform-category-header h3 {
    margin: 0;
    font-size: 18px;
    color: #111827;
}

.platforms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}

.platform-card {
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.2s;
}

.platform-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.platform-card.enabled {
    border-color: #10b981;
    background: linear-gradient(to bottom, #ecfdf5 0%, #fff 100%);
}

.platform-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}

.platform-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.platform-icon {
    width: 40px;
    height: 40px;
    background: #f3f4f6;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.platform-name {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
}

.platform-category-tag {
    font-size: 11px;
    color: #6b7280;
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 4px;
}

.platform-toggle {
    position: relative;
}

.platform-toggle input {
    opacity: 0;
    position: absolute;
}

.platform-toggle label {
    display: block;
    width: 50px;
    height: 26px;
    background: #d1d5db;
    border-radius: 13px;
    cursor: pointer;
    transition: background 0.2s;
}

.platform-toggle label::after {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 20px;
    height: 20px;
    background: #fff;
    border-radius: 50%;
    transition: transform 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.platform-toggle input:checked + label {
    background: #10b981;
}

.platform-toggle input:checked + label::after {
    transform: translateX(24px);
}

.platform-fields {
    display: none;
}

.platform-card.enabled .platform-fields {
    display: block;
}

.field-group {
    margin-bottom: 15px;
}

.field-group label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 5px;
}

.field-group input[type="text"],
.field-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.field-group input:focus,
.field-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.field-group textarea {
    min-height: 100px;
    font-family: monospace;
    font-size: 12px;
}

.field-hint {
    font-size: 11px;
    color: #6b7280;
    margin-top: 4px;
}

.conversion-events {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.conversion-event {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
}

.conversion-event input[type="checkbox"] {
    width: 16px;
    height: 16px;
}

/* UTM 설정 섹션 */
.utm-settings-card {
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 30px;
}

.utm-settings-card h3 {
    margin: 0 0 20px;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.utm-params-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.utm-params-table th,
.utm-params-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.utm-params-table th {
    background: #f9fafb;
    font-weight: 600;
    font-size: 13px;
    color: #374151;
}

.utm-params-table code {
    background: #f3f4f6;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
}

.attribution-models {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.attribution-model {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.2s;
}

.attribution-model:hover {
    border-color: #667eea;
}

.attribution-model.selected {
    border-color: #667eea;
    background: #f0f4ff;
}

.attribution-model input {
    display: none;
}

.attribution-model h4 {
    margin: 0 0 5px;
    font-size: 14px;
    color: #111827;
}

.attribution-model p {
    margin: 0;
    font-size: 12px;
    color: #6b7280;
}

/* 트래픽 소스 분석 미리보기 */
.traffic-preview {
    background: #f9fafb;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
}

.traffic-preview h4 {
    margin: 0 0 15px;
    font-size: 14px;
    color: #374151;
}

.traffic-sources-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
}

.traffic-source-item {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 10px;
    text-align: center;
}

.traffic-source-item .icon {
    font-size: 24px;
    margin-bottom: 5px;
}

.traffic-source-item .name {
    font-size: 13px;
    font-weight: 500;
    color: #111827;
}

/* 저장 버튼 */
.save-section {
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    bottom: 20px;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
}

.save-section .status {
    font-size: 14px;
    color: #6b7280;
}

.save-section .status .count {
    font-weight: 600;
    color: #10b981;
}

.btn-save {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

/* 빠른 설정 */
.quick-setup {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
}

.quick-setup h3 {
    margin: 0 0 15px;
    font-size: 16px;
    color: #92400e;
    display: flex;
    align-items: center;
    gap: 8px;
}

.quick-setup-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-quick-setup {
    background: #fff;
    border: 1px solid #d97706;
    color: #92400e;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-quick-setup:hover {
    background: #fffbeb;
    border-color: #b45309;
}
</style>

<div class="ad-pixels-wrap">
    <div class="ad-pixels-header">
        <h1><?php esc_html_e( '광고 픽셀 & 전환 추적', 'acf-nudge-flow' ); ?></h1>
        <p><?php esc_html_e( '광고 매체별 전환 픽셀을 설정하고 UTM 매개변수 추적을 관리합니다. 픽셀 ID만 입력하면 자동으로 추적 스크립트가 설치됩니다.', 'acf-nudge-flow' ); ?></p>
    </div>

    <form method="post" id="ad-pixels-form">
        <?php wp_nonce_field( 'acf_nudge_flow_ad_pixels' ); ?>
        
        <!-- 탭 네비게이션 -->
        <div class="ad-pixels-tabs">
            <button type="button" class="ad-pixels-tab active" data-tab="pixels">
                📊 <?php esc_html_e( '광고 픽셀 설정', 'acf-nudge-flow' ); ?>
            </button>
            <button type="button" class="ad-pixels-tab" data-tab="utm">
                🔗 <?php esc_html_e( 'UTM & 트래픽 분석', 'acf-nudge-flow' ); ?>
            </button>
            <button type="button" class="ad-pixels-tab" data-tab="funnel">
                📈 <?php esc_html_e( '퍼널 분석', 'acf-nudge-flow' ); ?>
            </button>
        </div>

        <!-- 픽셀 설정 섹션 -->
        <div class="ad-pixels-section active" id="section-pixels">
            
            <!-- 빠른 설정 -->
            <div class="quick-setup">
                <h3>⚡ <?php esc_html_e( '빠른 설정', 'acf-nudge-flow' ); ?></h3>
                <div class="quick-setup-buttons">
                    <button type="button" class="btn-quick-setup" data-preset="korea">
                        🇰🇷 <?php esc_html_e( '한국 광고 플랫폼 전체 활성화', 'acf-nudge-flow' ); ?>
                    </button>
                    <button type="button" class="btn-quick-setup" data-preset="global">
                        🌍 <?php esc_html_e( '글로벌 광고 플랫폼 전체 활성화', 'acf-nudge-flow' ); ?>
                    </button>
                    <button type="button" class="btn-quick-setup" data-preset="ecommerce">
                        🛒 <?php esc_html_e( '이커머스 전환 추적 권장 설정', 'acf-nudge-flow' ); ?>
                    </button>
                </div>
            </div>

            <?php foreach ( $platform_categories as $cat_id => $category ) : ?>
            <div class="platform-category">
                <div class="platform-category-header">
                    <span class="icon"><?php echo esc_html( $category['icon'] ); ?></span>
                    <h3><?php echo esc_html( $category['label'] ); ?></h3>
                </div>
                
                <div class="platforms-grid">
                    <?php foreach ( $category['platforms'] as $platform_id ) : 
                        if ( ! isset( $platforms[ $platform_id ] ) ) continue;
                        $platform = $platforms[ $platform_id ];
                        $saved = $saved_pixels[ $platform_id ] ?? array();
                        $is_enabled = ! empty( $saved['enabled'] );
                    ?>
                    <div class="platform-card <?php echo $is_enabled ? 'enabled' : ''; ?>" data-platform="<?php echo esc_attr( $platform_id ); ?>">
                        <div class="platform-header">
                            <div class="platform-info">
                                <div class="platform-icon"><?php echo esc_html( $platform['icon'] ?? '📊' ); ?></div>
                                <div>
                                    <div class="platform-name"><?php echo esc_html( $platform['name'] ); ?></div>
                                    <div class="platform-category-tag"><?php echo esc_html( $platform['category'] ?? '' ); ?></div>
                                </div>
                            </div>
                            <div class="platform-toggle">
                                <input type="checkbox" 
                                       id="pixel-enabled-<?php echo esc_attr( $platform_id ); ?>" 
                                       name="pixels[<?php echo esc_attr( $platform_id ); ?>][enabled]" 
                                       value="1"
                                       <?php checked( $is_enabled ); ?>>
                                <label for="pixel-enabled-<?php echo esc_attr( $platform_id ); ?>"></label>
                            </div>
                        </div>
                        
                        <div class="platform-fields">
                            <div class="field-group">
                                <label><?php echo esc_html( $platform['id_label'] ?? __( '픽셀 ID', 'acf-nudge-flow' ) ); ?></label>
                                <input type="text" 
                                       name="pixels[<?php echo esc_attr( $platform_id ); ?>][pixel_id]" 
                                       value="<?php echo esc_attr( $saved['pixel_id'] ?? '' ); ?>"
                                       placeholder="<?php echo esc_attr( $platform['id_placeholder'] ?? '' ); ?>">
                                <?php if ( ! empty( $platform['id_hint'] ) ) : ?>
                                <div class="field-hint"><?php echo esc_html( $platform['id_hint'] ); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ( $platform_id === 'ga4' ) : ?>
                            <div class="field-group">
                                <label><?php esc_html_e( 'Measurement ID', 'acf-nudge-flow' ); ?></label>
                                <input type="text" 
                                       name="pixels[<?php echo esc_attr( $platform_id ); ?>][measurement_id]" 
                                       value="<?php echo esc_attr( $saved['measurement_id'] ?? '' ); ?>"
                                       placeholder="G-XXXXXXXXXX">
                            </div>
                            <?php endif; ?>
                            
                            <?php if ( $platform_id === 'google_ads' ) : ?>
                            <div class="field-group">
                                <label><?php esc_html_e( '전환 라벨', 'acf-nudge-flow' ); ?></label>
                                <input type="text" 
                                       name="pixels[<?php echo esc_attr( $platform_id ); ?>][conversion_label]" 
                                       value="<?php echo esc_attr( $saved['conversion_label'] ?? '' ); ?>"
                                       placeholder="AbCdEfGhIjKlMnOp">
                            </div>
                            <?php endif; ?>
                            
                            <div class="field-group">
                                <label><?php esc_html_e( '추적할 전환 이벤트', 'acf-nudge-flow' ); ?></label>
                                <div class="conversion-events">
                                    <?php 
                                    $events = array(
                                        'page_view' => __( '페이지 뷰', 'acf-nudge-flow' ),
                                        'view_content' => __( '콘텐츠 조회', 'acf-nudge-flow' ),
                                        'add_to_cart' => __( '장바구니 추가', 'acf-nudge-flow' ),
                                        'begin_checkout' => __( '결제 시작', 'acf-nudge-flow' ),
                                        'purchase' => __( '구매 완료', 'acf-nudge-flow' ),
                                        'signup' => __( '회원가입', 'acf-nudge-flow' ),
                                        'lead' => __( '리드 생성', 'acf-nudge-flow' ),
                                    );
                                    $saved_events = $saved['conversion_events'] ?? array( 'page_view', 'purchase' );
                                    foreach ( $events as $event_key => $event_label ) : 
                                    ?>
                                    <label class="conversion-event">
                                        <input type="checkbox" 
                                               name="pixels[<?php echo esc_attr( $platform_id ); ?>][conversion_events][]" 
                                               value="<?php echo esc_attr( $event_key ); ?>"
                                               <?php checked( in_array( $event_key, $saved_events ) ); ?>>
                                        <?php echo esc_html( $event_label ); ?>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label><?php esc_html_e( '커스텀 스크립트 (선택)', 'acf-nudge-flow' ); ?></label>
                                <textarea name="pixels[<?php echo esc_attr( $platform_id ); ?>][custom_script]" 
                                          placeholder="<?php esc_attr_e( '<!-- 광고 플랫폼에서 제공한 스크립트를 붙여넣기 하세요 -->', 'acf-nudge-flow' ); ?>"><?php echo esc_textarea( $saved['custom_script'] ?? '' ); ?></textarea>
                                <div class="field-hint"><?php esc_html_e( '픽셀 ID 대신 전체 스크립트를 직접 붙여넣을 수 있습니다.', 'acf-nudge-flow' ); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- UTM & 트래픽 분석 섹션 -->
        <div class="ad-pixels-section" id="section-utm">
            <div class="utm-settings-card">
                <h3>🔗 <?php esc_html_e( 'UTM 매개변수 추적', 'acf-nudge-flow' ); ?></h3>
                
                <div class="field-group">
                    <label class="conversion-event">
                        <input type="checkbox" 
                               name="utm_settings[enabled]" 
                               value="1"
                               <?php checked( $utm_settings['enabled'] ?? true ); ?>>
                        <?php esc_html_e( 'UTM 매개변수 자동 추적 활성화', 'acf-nudge-flow' ); ?>
                    </label>
                </div>
                
                <table class="utm-params-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( '매개변수', 'acf-nudge-flow' ); ?></th>
                            <th><?php esc_html_e( '설명', 'acf-nudge-flow' ); ?></th>
                            <th><?php esc_html_e( '예시', 'acf-nudge-flow' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>utm_source</code></td>
                            <td><?php esc_html_e( '트래픽 유입 출처', 'acf-nudge-flow' ); ?></td>
                            <td>google, facebook, naver, kakao</td>
                        </tr>
                        <tr>
                            <td><code>utm_medium</code></td>
                            <td><?php esc_html_e( '마케팅 매체 유형', 'acf-nudge-flow' ); ?></td>
                            <td>cpc, email, social, banner</td>
                        </tr>
                        <tr>
                            <td><code>utm_campaign</code></td>
                            <td><?php esc_html_e( '캠페인 이름', 'acf-nudge-flow' ); ?></td>
                            <td>spring_sale, black_friday</td>
                        </tr>
                        <tr>
                            <td><code>utm_term</code></td>
                            <td><?php esc_html_e( '검색 키워드', 'acf-nudge-flow' ); ?></td>
                            <td>running_shoes, summer_dress</td>
                        </tr>
                        <tr>
                            <td><code>utm_content</code></td>
                            <td><?php esc_html_e( '광고 콘텐츠 구분', 'acf-nudge-flow' ); ?></td>
                            <td>banner_a, text_link</td>
                        </tr>
                    </tbody>
                </table>
                
                <h4><?php esc_html_e( '광고 매체별 자동 인식 매개변수', 'acf-nudge-flow' ); ?></h4>
                <table class="utm-params-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( '광고 매체', 'acf-nudge-flow' ); ?></th>
                            <th><?php esc_html_e( '매개변수', 'acf-nudge-flow' ); ?></th>
                            <th><?php esc_html_e( '용도', 'acf-nudge-flow' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>🇰🇷 <?php esc_html_e( '네이버', 'acf-nudge-flow' ); ?></td>
                            <td><code>n_media</code>, <code>n_query</code>, <code>n_ad_group</code></td>
                            <td><?php esc_html_e( '네이버 검색광고 추적', 'acf-nudge-flow' ); ?></td>
                        </tr>
                        <tr>
                            <td>🇰🇷 <?php esc_html_e( '카카오', 'acf-nudge-flow' ); ?></td>
                            <td><code>kakao_campaign</code>, <code>kakao_adgrp</code>, <code>kakao_creative</code></td>
                            <td><?php esc_html_e( '카카오 모먼트 추적', 'acf-nudge-flow' ); ?></td>
                        </tr>
                        <tr>
                            <td>🌍 Meta</td>
                            <td><code>fbclid</code></td>
                            <td><?php esc_html_e( 'Facebook/Instagram 광고 추적', 'acf-nudge-flow' ); ?></td>
                        </tr>
                        <tr>
                            <td>🌍 Google</td>
                            <td><code>gclid</code>, <code>gad_source</code></td>
                            <td><?php esc_html_e( 'Google Ads 자동 태깅', 'acf-nudge-flow' ); ?></td>
                        </tr>
                        <tr>
                            <td>🌍 TikTok</td>
                            <td><code>ttclid</code></td>
                            <td><?php esc_html_e( 'TikTok 광고 추적', 'acf-nudge-flow' ); ?></td>
                        </tr>
                        <tr>
                            <td>🌍 Microsoft</td>
                            <td><code>msclkid</code></td>
                            <td><?php esc_html_e( 'Microsoft Ads 추적', 'acf-nudge-flow' ); ?></td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="field-group" style="margin-top: 20px;">
                    <label><?php esc_html_e( 'UTM 데이터 저장 기간 (일)', 'acf-nudge-flow' ); ?></label>
                    <input type="number" 
                           name="utm_settings[store_duration]" 
                           value="<?php echo esc_attr( $utm_settings['store_duration'] ?? 30 ); ?>"
                           min="1" max="365" style="width: 100px;">
                    <div class="field-hint"><?php esc_html_e( '방문자의 UTM 정보를 쿠키에 저장하는 기간입니다.', 'acf-nudge-flow' ); ?></div>
                </div>
            </div>
            
            <div class="utm-settings-card">
                <h3>📊 <?php esc_html_e( '전환 기여 모델', 'acf-nudge-flow' ); ?></h3>
                <p style="color: #6b7280; margin-bottom: 20px;">
                    <?php esc_html_e( '여러 광고 채널을 거쳐 전환이 발생했을 때, 어떤 채널에 전환 공로를 부여할지 선택합니다.', 'acf-nudge-flow' ); ?>
                </p>
                
                <div class="attribution-models">
                    <label class="attribution-model <?php echo ( $utm_settings['attribution_model'] ?? 'last_click' ) === 'last_click' ? 'selected' : ''; ?>">
                        <input type="radio" name="utm_settings[attribution_model]" value="last_click" 
                               <?php checked( $utm_settings['attribution_model'] ?? 'last_click', 'last_click' ); ?>>
                        <h4><?php esc_html_e( '마지막 클릭', 'acf-nudge-flow' ); ?></h4>
                        <p><?php esc_html_e( '전환 직전 마지막 터치포인트에 100% 기여', 'acf-nudge-flow' ); ?></p>
                    </label>
                    
                    <label class="attribution-model <?php echo ( $utm_settings['attribution_model'] ?? '' ) === 'first_click' ? 'selected' : ''; ?>">
                        <input type="radio" name="utm_settings[attribution_model]" value="first_click"
                               <?php checked( $utm_settings['attribution_model'] ?? '', 'first_click' ); ?>>
                        <h4><?php esc_html_e( '첫 번째 클릭', 'acf-nudge-flow' ); ?></h4>
                        <p><?php esc_html_e( '최초 유입 채널에 100% 기여', 'acf-nudge-flow' ); ?></p>
                    </label>
                    
                    <label class="attribution-model <?php echo ( $utm_settings['attribution_model'] ?? '' ) === 'linear' ? 'selected' : ''; ?>">
                        <input type="radio" name="utm_settings[attribution_model]" value="linear"
                               <?php checked( $utm_settings['attribution_model'] ?? '', 'linear' ); ?>>
                        <h4><?php esc_html_e( '선형', 'acf-nudge-flow' ); ?></h4>
                        <p><?php esc_html_e( '모든 터치포인트에 동일하게 분배', 'acf-nudge-flow' ); ?></p>
                    </label>
                    
                    <label class="attribution-model <?php echo ( $utm_settings['attribution_model'] ?? '' ) === 'time_decay' ? 'selected' : ''; ?>">
                        <input type="radio" name="utm_settings[attribution_model]" value="time_decay"
                               <?php checked( $utm_settings['attribution_model'] ?? '', 'time_decay' ); ?>>
                        <h4><?php esc_html_e( '시간 가중', 'acf-nudge-flow' ); ?></h4>
                        <p><?php esc_html_e( '전환에 가까울수록 높은 기여도 부여', 'acf-nudge-flow' ); ?></p>
                    </label>
                </div>
            </div>
            
            <div class="utm-settings-card">
                <h3>🌐 <?php esc_html_e( '자동 인식 트래픽 소스', 'acf-nudge-flow' ); ?></h3>
                <p style="color: #6b7280; margin-bottom: 20px;">
                    <?php esc_html_e( '다음 트래픽 소스는 리퍼러 정보를 통해 자동으로 인식됩니다.', 'acf-nudge-flow' ); ?>
                </p>
                
                <div class="traffic-preview">
                    <div class="traffic-sources-list">
                        <div class="traffic-source-item">
                            <div class="icon">🔍</div>
                            <div class="name">Google</div>
                        </div>
                        <div class="traffic-source-item">
                            <div class="icon">🔍</div>
                            <div class="name">Naver</div>
                        </div>
                        <div class="traffic-source-item">
                            <div class="icon">🔍</div>
                            <div class="name">Daum</div>
                        </div>
                        <div class="traffic-source-item">
                            <div class="icon">🔍</div>
                            <div class="name">Bing</div>
                        </div>
                        <div class="traffic-source-item">
                            <div class="icon">📱</div>
                            <div class="name">Facebook</div>
                        </div>
                        <div class="traffic-source-item">
                            <div class="icon">📸</div>
                            <div class="name">Instagram</div>
                        </div>
                        <div class="traffic-source-item">
                            <div class="icon">🐦</div>
                            <div class="name">Twitter</div>
                        </div>
                        <div class="traffic-source-item">
                            <div class="icon">💼</div>
                            <div class="name">LinkedIn</div>
                        </div>
                        <div class="traffic-source-item">
                            <div class="icon">🎵</div>
                            <div class="name">TikTok</div>
                        </div>
                        <div class="traffic-source-item">
                            <div class="icon">💬</div>
                            <div class="name">KakaoTalk</div>
                        </div>
                        <div class="traffic-source-item">
                            <div class="icon">📺</div>
                            <div class="name">YouTube</div>
                        </div>
                        <div class="traffic-source-item">
                            <div class="icon">✉️</div>
                            <div class="name">Email</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 퍼널 분석 섹션 -->
        <div class="ad-pixels-section" id="section-funnel">
            <div class="utm-settings-card">
                <h3>📈 <?php esc_html_e( '전환 퍼널 분석', 'acf-nudge-flow' ); ?></h3>
                <p style="color: #6b7280; margin-bottom: 20px;">
                    <?php esc_html_e( '방문자가 사이트에서 거치는 주요 단계를 분석하여 이탈 지점을 파악합니다.', 'acf-nudge-flow' ); ?>
                </p>
                
                <div style="background: #f9fafb; border-radius: 12px; padding: 30px; text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 15px;">📊</div>
                    <h4 style="margin: 0 0 10px; color: #374151;"><?php esc_html_e( '퍼널 분석 대시보드', 'acf-nudge-flow' ); ?></h4>
                    <p style="color: #6b7280; margin-bottom: 20px;">
                        <?php esc_html_e( '이 기능은 "분석 통계" 메뉴에서 자세히 확인할 수 있습니다.', 'acf-nudge-flow' ); ?>
                    </p>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-nudge-flow-analytics' ) ); ?>" 
                       class="button button-primary" style="padding: 10px 24px;">
                        <?php esc_html_e( '분석 대시보드로 이동', 'acf-nudge-flow' ); ?> →
                    </a>
                </div>
                
                <div style="margin-top: 30px;">
                    <h4><?php esc_html_e( '기본 이커머스 퍼널', 'acf-nudge-flow' ); ?></h4>
                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
                        <div style="background: #dbeafe; color: #1e40af; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                            🏠 <?php esc_html_e( '홈페이지', 'acf-nudge-flow' ); ?>
                        </div>
                        <div style="color: #9ca3af;">→</div>
                        <div style="background: #dbeafe; color: #1e40af; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                            📦 <?php esc_html_e( '상품 목록', 'acf-nudge-flow' ); ?>
                        </div>
                        <div style="color: #9ca3af;">→</div>
                        <div style="background: #dbeafe; color: #1e40af; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                            👁️ <?php esc_html_e( '상품 상세', 'acf-nudge-flow' ); ?>
                        </div>
                        <div style="color: #9ca3af;">→</div>
                        <div style="background: #fef3c7; color: #92400e; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                            🛒 <?php esc_html_e( '장바구니', 'acf-nudge-flow' ); ?>
                        </div>
                        <div style="color: #9ca3af;">→</div>
                        <div style="background: #dcfce7; color: #166534; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                            💳 <?php esc_html_e( '결제', 'acf-nudge-flow' ); ?>
                        </div>
                        <div style="color: #9ca3af;">→</div>
                        <div style="background: #d1fae5; color: #065f46; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                            ✅ <?php esc_html_e( '완료', 'acf-nudge-flow' ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 저장 버튼 -->
        <div class="save-section">
            <div class="status">
                <span class="count" id="enabled-count">0</span> <?php esc_html_e( '개의 광고 플랫폼 활성화됨', 'acf-nudge-flow' ); ?>
            </div>
            <button type="submit" name="save_ad_pixels" class="btn-save">
                💾 <?php esc_html_e( '설정 저장', 'acf-nudge-flow' ); ?>
            </button>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // 탭 전환
    $('.ad-pixels-tab').on('click', function() {
        var tab = $(this).data('tab');
        $('.ad-pixels-tab').removeClass('active');
        $(this).addClass('active');
        $('.ad-pixels-section').removeClass('active');
        $('#section-' + tab).addClass('active');
    });
    
    // 플랫폼 토글
    $('.platform-toggle input').on('change', function() {
        var card = $(this).closest('.platform-card');
        if ($(this).is(':checked')) {
            card.addClass('enabled');
        } else {
            card.removeClass('enabled');
        }
        updateEnabledCount();
    });
    
    // 활성화된 플랫폼 수 업데이트
    function updateEnabledCount() {
        var count = $('.platform-card.enabled').length;
        $('#enabled-count').text(count);
    }
    updateEnabledCount();
    
    // 기여 모델 선택
    $('.attribution-model').on('click', function() {
        $('.attribution-model').removeClass('selected');
        $(this).addClass('selected');
    });
    
    // 빠른 설정
    $('.btn-quick-setup').on('click', function() {
        var preset = $(this).data('preset');
        
        if (preset === 'korea') {
            $('[data-platform="naver"], [data-platform="kakao"], [data-platform="toss"], [data-platform="coupang"]')
                .find('.platform-toggle input').prop('checked', true).trigger('change');
        } else if (preset === 'global') {
            $('[data-platform="meta"], [data-platform="google_ads"], [data-platform="ga4"], [data-platform="tiktok"]')
                .find('.platform-toggle input').prop('checked', true).trigger('change');
        } else if (preset === 'ecommerce') {
            // 주요 이커머스 플랫폼 활성화
            $('[data-platform="meta"], [data-platform="google_ads"], [data-platform="ga4"], [data-platform="naver"], [data-platform="kakao"]')
                .find('.platform-toggle input').prop('checked', true).trigger('change');
            
            // 이커머스 관련 이벤트 체크
            $('.platform-card.enabled').each(function() {
                $(this).find('input[value="add_to_cart"], input[value="begin_checkout"], input[value="purchase"]')
                    .prop('checked', true);
            });
        }
    });
});
</script>

<!-- [v22.8.0] 트래픽 소스 파라미터 가이드 섹션 -->
<div class="traffic-source-guide" style="margin-top: 40px;">
    <div style="background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h2 style="margin: 0 0 20px; font-size: 20px; display: flex; align-items: center; gap: 10px;">
            <span>📚</span> 광고 매체별 URL 파라미터 가이드
        </h2>
        <p style="color: #6b7280; margin-bottom: 25px;">
            UTM 파라미터를 설정하지 않더라도, 각 광고 매체의 자동 태깅 파라미터로 트래픽 소스가 자동 추적됩니다.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">

            <!-- 네이버 -->
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 20px;">
                <h3 style="margin: 0 0 15px; color: #03c75a; font-size: 16px;">🟢 네이버 광고</h3>
                <div style="font-size: 13px; color: #374151;">
                    <p><strong>검색광고:</strong> <code style="background: #dcfce7; padding: 2px 6px; border-radius: 4px;">n_media, n_query, n_keyword, n_rank</code></p>
                    <p><strong>쇼핑검색:</strong> <code style="background: #dcfce7; padding: 2px 6px; border-radius: 4px;">n_mall_pid, n_mall_id</code></p>
                    <p><strong>GFA (성과형DA):</strong> <code style="background: #dcfce7; padding: 2px 6px; border-radius: 4px;">na_source=gfa, na_campaign</code></p>
                    <p><strong>브랜드검색:</strong> <code style="background: #dcfce7; padding: 2px 6px; border-radius: 4px;">n_campaign_type=brand</code></p>
                </div>
            </div>

            <!-- 카카오 -->
            <div style="background: #fefce8; border: 1px solid #fef08a; border-radius: 10px; padding: 20px;">
                <h3 style="margin: 0 0 15px; color: #ca8a04; font-size: 16px;">💛 카카오 광고</h3>
                <div style="font-size: 13px; color: #374151;">
                    <p><strong>카카오 모먼트:</strong> <code style="background: #fef9c3; padding: 2px 6px; border-radius: 4px;">kakao_campaign, kakao_adgrp, kakao_creative</code></p>
                    <p><strong>다음 키워드광고:</strong> <code style="background: #fef9c3; padding: 2px 6px; border-radius: 4px;">dkwid, dkw</code></p>
                    <p style="margin-top: 10px; font-size: 12px; color: #6b7280;">
                        <strong>매크로:</strong> {click_id}, {campaign_id}, {adgroup_id}, {creative_id}
                    </p>
                </div>
            </div>

            <!-- Google -->
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 20px;">
                <h3 style="margin: 0 0 15px; color: #2563eb; font-size: 16px;">🔵 Google Ads</h3>
                <div style="font-size: 13px; color: #374151;">
                    <p><strong>클릭 ID:</strong> <code style="background: #dbeafe; padding: 2px 6px; border-radius: 4px;">gclid</code> (자동 태깅)</p>
                    <p><strong>앱 캠페인:</strong> <code style="background: #dbeafe; padding: 2px 6px; border-radius: 4px;">gbraid, wbraid</code></p>
                    <p style="margin-top: 10px; font-size: 12px; color: #6b7280;">
                        <strong>ValueTrack:</strong> {campaignid}, {keyword}, {matchtype}, {device}
                    </p>
                </div>
            </div>

            <!-- Meta -->
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 20px;">
                <h3 style="margin: 0 0 15px; color: #dc2626; font-size: 16px;">🔴 Meta (Facebook/Instagram)</h3>
                <div style="font-size: 13px; color: #374151;">
                    <p><strong>클릭 ID:</strong> <code style="background: #fee2e2; padding: 2px 6px; border-radius: 4px;">fbclid</code> (자동 태깅)</p>
                    <p style="margin-top: 10px; font-size: 12px; color: #6b7280;">
                        <strong>동적 매개변수:</strong> {{campaign.name}}, {{site_source_name}}, {{placement}}
                    </p>
                </div>
            </div>

            <!-- TikTok -->
            <div style="background: #fdf4ff; border: 1px solid #f5d0fe; border-radius: 10px; padding: 20px;">
                <h3 style="margin: 0 0 15px; color: #a855f7; font-size: 16px;">🎵 TikTok</h3>
                <div style="font-size: 13px; color: #374151;">
                    <p><strong>클릭 ID:</strong> <code style="background: #fae8ff; padding: 2px 6px; border-radius: 4px;">ttclid</code> (자동 태깅)</p>
                    <p style="margin-top: 10px; font-size: 12px; color: #6b7280;">
                        <strong>매크로:</strong> __CAMPAIGN_ID__, __PLACEMENT__, __ADGROUP_ID__
                    </p>
                </div>
            </div>

            <!-- Microsoft -->
            <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 20px;">
                <h3 style="margin: 0 0 15px; color: #0284c7; font-size: 16px;">🌐 Microsoft Ads</h3>
                <div style="font-size: 13px; color: #374151;">
                    <p><strong>클릭 ID:</strong> <code style="background: #e0f2fe; padding: 2px 6px; border-radius: 4px;">msclkid</code> (자동 태깅)</p>
                    <p style="margin-top: 10px; font-size: 12px; color: #6b7280;">
                        <strong>매크로:</strong> {Campaign}, {Keyword}, {MatchType}, {Device}
                    </p>
                </div>
            </div>

        </div>

        <!-- AI 검색엔진 -->
        <div style="margin-top: 25px; background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%); border: 1px solid #667eea40; border-radius: 10px; padding: 20px;">
            <h3 style="margin: 0 0 15px; color: #667eea; font-size: 16px;">🤖 AI 검색엔진 / 챗봇 트래픽</h3>
            <p style="font-size: 13px; color: #374151; margin-bottom: 10px;">
                다음 AI 플랫폼에서의 유입이 자동으로 추적됩니다 (리퍼러 기반):
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <?php
                $ai_platforms = array(
                    'ChatGPT', 'Perplexity AI', 'Google Gemini', 'Claude AI',
                    'Microsoft Copilot', 'You.com', 'Phind', 'Kagi', 'SearchGPT'
                );
                foreach ( $ai_platforms as $platform ) :
                ?>
                <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 4px 12px; border-radius: 15px; font-size: 12px; font-weight: 500;">
                    <?php echo esc_html( $platform ); ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 기타 광고 매체 -->
        <div style="margin-top: 25px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 20px;">
            <h3 style="margin: 0 0 15px; color: #374151; font-size: 16px;">📋 기타 지원 광고 매체</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; font-size: 13px;">
                <div>
                    <strong>LinkedIn:</strong> <code style="background: #e5e7eb; padding: 2px 6px; border-radius: 4px;">li_fat_id</code>
                </div>
                <div>
                    <strong>Pinterest:</strong> <code style="background: #e5e7eb; padding: 2px 6px; border-radius: 4px;">epik</code>
                </div>
                <div>
                    <strong>Snapchat:</strong> <code style="background: #e5e7eb; padding: 2px 6px; border-radius: 4px;">ScCid</code>
                </div>
                <div>
                    <strong>Twitter (X):</strong> <code style="background: #e5e7eb; padding: 2px 6px; border-radius: 4px;">twclid</code>
                </div>
                <div>
                    <strong>데이블:</strong> <code style="background: #e5e7eb; padding: 2px 6px; border-radius: 4px;">dablead</code>
                </div>
                <div>
                    <strong>모비온/타겟팅게이츠:</strong> <code style="background: #e5e7eb; padding: 2px 6px; border-radius: 4px;">ti</code>
                </div>
            </div>
        </div>

        <div style="margin-top: 25px; text-align: center;">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-nudge-flow-traffic' ) ); ?>"
               style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: transform 0.2s, box-shadow 0.2s;">
                <span>📊</span> 트래픽 분석 대시보드 바로가기
            </a>
        </div>
    </div>
</div>
