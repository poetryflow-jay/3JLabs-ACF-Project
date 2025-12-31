<?php
/**
 * [v1.9.0-beta1] '스포크' UI: Ultimate Member '컨텍스트' '재탄생'
 * - [신규] 깨진 이미지 아이콘을 텍스트('UM')로 교체
 * - [수정] v1.8.1-alpha의 탭 UI 및 제어판 계승
 */
if ( ! defined( 'ABSPATH' ) ) exit; // 직접 접근 차단

// v1.8.1-alpha: 'contexts' DB 경로에서 'um' 설정을 가져옵니다.
$um_contexts = $options['contexts']['um'] ?? array();
$um_korea = $um_contexts['korea'] ?? array();
$um_optimize = $um_contexts['optimize'] ?? array();
?>

<div class="jj-section-spoke" id="jj-section-spoke-um">
    <h2>
        <span class="jj-spoke-icon-text">UM</span>
        Ultimate Member (컨텍스트 제어)
    </h2>
    <p class="description">
        'Ultimate Member' 및 관련 '스포크'(UM Korea, UM Optimize 등)의 특정 영역(컨텍스트) 스타일을 '장악'합니다.
    </p>

    <div class="jj-tabs-container">
        <div class="jj-tabs-nav">
            <button type="button" class="jj-tab-button is-active" data-tab="um-core">
                <?php _e( '코어 / 기본', 'jj-style-guide' ); ?>
            </button>
            <button type="button" class="jj-tab-button" data-tab="um-korea">
                <?php _e( 'UM Korea (단비)', 'jj-style-guide' ); ?>
            </button>
            <button type="button" class="jj-tab-button" data-tab="um-optimize">
                <?php _e( 'UM Optimize', 'jj-style-guide' ); ?>
            </button>
        </div>

        <div class="jj-tab-content is-active" data-tab-content="um-core">
            <fieldset class="jj-fieldset-group">
                <legend><?php _e( 'UM 코어 색상', 'jj-style-guide' ); ?></legend>
                <div class="jj-style-guide-grid jj-grid-4-col">
                    
                    <div class="jj-control-group jj-color-card">
                        <label for="jj-um-primary_color">Primary Color (UM)</label>
                        <input type="text" 
                               id="jj-um-primary_color" 
                               class="jj-color-field jj-data-field" 
                               data-setting-key="palettes[brand][primary_color]" 
                               value="<?php echo esc_attr( $options['palettes']['brand']['primary_color'] ?? '' ); ?>">
                        <div class="jj-color-preview"></div>
                        <p class="description">
                            UM의 '기본 색상' 입니다. '1. 브랜드 팔레트'의 Primary Color와 동일한 값을 공유합니다. (양방향 동기화)
                        </p>
                    </div>

                </div> 
            </fieldset>
        </div>

        <div class="jj-tab-content" data-tab-content="um-korea">
            <fieldset class="jj-fieldset-group">
                <legend><?php _e( 'UM Korea (단비스토어) 스타일', 'jj-style-guide' ); ?></legend>
                <div class="jj-style-guide-grid jj-grid-4-col">
                    
                    <div class="jj-control-group jj-color-card">
                        <label for="jj-um-korea-social-bg"><?php _e( '소셜 로그인 버튼 배경', 'jj-style-guide' ); ?></label>
                        <input type="text" 
                               id="jj-um-korea-social-bg" 
                               class="jj-color-field jj-data-field" 
                               data-setting-key="contexts[um][korea][social_button_bg]" 
                               value="<?php echo esc_attr( $um_korea['social_button_bg'] ?? '' ); ?>">
                        <div class="jj-color-preview"></div>
                        <p class="description">
                            'UM Korea' 플러그인이 추가하는 '소셜 로그인' 버튼의 배경색을 '장악'합니다. (예: 네이버, 카카오)
                        </p>
                    </div>
                    
                    <div class="jj-control-group jj-color-card">
                        <label for="jj-um-korea-social-text"><?php _e( '소셜 로그인 버튼 텍스트', 'jj-style-guide' ); ?></label>
                        <input type="text" 
                               id="jj-um-korea-social-text" 
                               class="jj-color-field jj-data-field" 
                               data-setting-key="contexts[um][korea][social_button_text]" 
                               value="<?php echo esc_attr( $um_korea['social_button_text'] ?? '' ); ?>">
                        <div class="jj-color-preview"></div>
                        <p class="description">
                            'UM Korea' '소셜 로그인' 버튼의 텍스트 색상을 '장악'합니다.
                        </p>
                    </div>

                </div>
            </fieldset>
        </div>
        
        <div class="jj-tab-content" data-tab-content="um-optimize">
             <fieldset class="jj-fieldset-group">
                <legend><?php _e( 'UM Optimize 스타일', 'jj-style-guide' ); ?></legend>
                 <div class="jj-style-guide-grid jj-grid-4-col">
                    
                    <div class="jj-control-group jj-color-card">
                        <label for="jj-um-optimize-header"><?php _e( '프로필 헤더 배경', 'jj-style-guide' ); ?></label>
                        <input type="text" 
                               id="jj-um-optimize-header" 
                               class="jj-color-field jj-data-field" 
                               data-setting-key="contexts[um][optimize][profile_header_color]" 
                               value="<?php echo esc_attr( $um_optimize['profile_header_color'] ?? '' ); ?>">
                        <div class="jj-color-preview"></div>
                        <p class="description">
                            'UM Optimize' 플러그인이 제어하는 '프로필 헤더' 등의 추가 색상을 '장악'합니다.
                        </p>
                    </div>

                </div>
            </fieldset>
        </div>

    </div> 
</div>