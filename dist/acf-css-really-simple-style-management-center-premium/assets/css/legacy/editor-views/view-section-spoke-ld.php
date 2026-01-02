<?php
/**
 * [v1.9.0-beta1] '스포크' UI: LearnDash '컨텍스트' '재탄생'
 * - [신규] 깨진 이미지 아이콘을 텍스트('LD')로 교체
 * - [수정] v1.8.1-alpha의 탭 UI 및 제어판 계승
 */
if ( ! defined( 'ABSPATH' ) ) exit; // 직접 접근 차단

// v1.8.1-alpha: 'contexts' DB 경로에서 'learndash' 설정을 가져옵니다.
$ld_contexts = $options['contexts']['learndash'] ?? array();
$ld_dashboard = $ld_contexts['dashboard'] ?? array();
$ld_propanel = $ld_contexts['propanel'] ?? array();
$ld_korea = $ld_contexts['korea'] ?? array();
?>

<div class="jj-section-spoke" id="jj-section-spoke-ld">
    <h2>
        <span class="jj-spoke-icon-text">LD</span>
        LearnDash (컨텍스트 제어)
    </h2>
    <p class="description">
        'LearnDash' 및 관련 '스포크'(ProPanel, LD K 등)의 특정 영역(컨텍스트) 스타일을 '장악'합니다.
    </p>

    <div class="jj-tabs-container">
        <div class="jj-tabs-nav">
            <button type="button" class="jj-tab-button is-active" data-tab="ld-dashboard">
                <?php _e( '코어 / 대시보드', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" class="jj-tab-button" data-tab="ld-propanel">
                <?php _e( 'ProPanel', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" class="jj-tab-button" data-tab="ld-korea">
                <?php _e( 'LearnDash K', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
        </div>

        <div class="jj-tab-content is-active" data-tab-content="ld-dashboard">
            <fieldset class="jj-fieldset-group">
                <legend><?php _e( 'LearnDash Dashboard 색상', 'acf-css-really-simple-style-management-center' ); ?></legend>
                <div class="jj-style-guide-grid jj-grid-4-col">
                    
                    <div class="jj-control-group jj-color-card">
                        <label for="jj-ld-primary_color">Focus Color (LearnDash)</label>
                        <input type="text" 
                               id="jj-ld-primary_color" 
                               class="jj-color-field jj-data-field" 
                               data-setting-key="palettes[brand][primary_color]" 
                               value="<?php echo esc_attr( $options['palettes']['brand']['primary_color'] ?? '' ); ?>">
                        <div class="jj-color-preview"></div>
                        <p class="description">
                            LearnDash 코어 및 'LearnDash Dashboard'의 핵심 색상입니다. '1. 브랜드 팔레트'의 Primary Color와 동일한 값을 공유합니다. (양방향 동기화)
                        </p>
                    </div>

                    <div class="jj-control-group jj-color-card">
                        <label for="jj-ld-primary_color_hover">Focus Color (Hover)</label>
                        <input type="text" 
                               id="jj-ld-primary_color_hover" 
                               class="jj-color-field jj-data-field" 
                               data-setting-key="palettes[brand][primary_color_hover]" 
                               value="<?php echo esc_attr( $options['palettes']['brand']['primary_color_hover'] ?? '' ); ?>">
                        <div class="jj-color-preview"></div>
                        <p class="description">
                            LearnDash 코어 및 'LearnDash Dashboard'의 호버 색상입니다. '1. 브랜드 팔레트'의 Primary Color (Hover)와 동일한 값을 공유합니다. (양방향 동기화)
                        </p>
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="jj-tab-content" data-tab-content="ld-propanel">
            <fieldset class="jj-fieldset-group">
                <legend><?php _e( 'LearnDash ProPanel 스타일', 'acf-css-really-simple-style-management-center' ); ?></legend>
                <div class="jj-style-guide-grid jj-grid-4-col">
                    
                    <div class="jj-control-group jj-color-card">
                        <label for="jj-ld-propanel-progress"><?php _e( '진행도 바 색상 (Progress Bar)', 'acf-css-really-simple-style-management-center' ); ?></label>
                        <input type="text" 
                               id="jj-ld-propanel-progress" 
                               class="jj-color-field jj-data-field" 
                               data-setting-key="contexts[learndash][propanel][progress_bar_color]" 
                               value="<?php echo esc_attr( $ld_propanel['progress_bar_color'] ?? '' ); ?>">
                        <div class="jj-color-preview"></div>
                        <p class="description">
                            'LearnDash ProPanel' 리포팅 위젯의 '진행도 바' 색상을 '장악'합니다.
                        </p>
                    </div>

                </div>
            </fieldset>
        </div>
        
        <div class="jj-tab-content" data-tab-content="ld-korea">
            <fieldset class="jj-fieldset-group">
                <legend><?php _e( 'LearnDash K (단비스토어) 스타일', 'acf-css-really-simple-style-management-center' ); ?></legend>
                 <div class="jj-style-guide-grid jj-grid-4-col">
                    
                    <div class="jj-control-group jj-color-card">
                        <label for="jj-ld-korea-button"><?php _e( '단비 버튼 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                        <input type="text" 
                               id="jj-ld-korea-button" 
                               class="jj-color-field jj-data-field" 
                               data-setting-key="contexts[learndash][korea][button_color]" 
                               value="<?php echo esc_attr( $ld_korea['button_color'] ?? '' ); ?>">
                        <div class="jj-color-preview"></div>
                        <p class="description">
                            'LearnDash K' 플러그인이 추가하는 '수료증', '포인트' 관련 한국형 버튼의 기본 색상을 '장악'합니다.
                        </p>
                    </div>

                </div>
            </fieldset>
        </div>

    </div> 
</div>