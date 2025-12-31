<?php
/**
 * 실험실 센터 - 수동 재정의 탭
 * CSS 오버라이드 기능
 *
 * @version 3.7.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$labs_options = $options['labs'] ?? array();
?>
<div class="jj-labs-overrides-tab">
    <h2><?php _e( '수동 CSS 재정의 (Overrides)', 'jj-style-guide' ); ?></h2>
    <p class="description jj-labs-tab-description" data-tab-type="overrides" data-tooltip="labs-tab-overrides">
        <?php _e( '스캐너를 통해 발견된 선택자나 직접 지정한 선택자에 전역 스타일 변수를 적용할 수 있습니다. 여기에 입력한 CSS는 사이트 전역에 적용됩니다.', 'jj-style-guide' ); ?>
        <span class="dashicons dashicons-editor-help" style="margin-left: 5px; cursor: help;" aria-label="<?php esc_attr_e( '도움말', 'jj-style-guide' ); ?>"></span>
    </p>

    <div class="jj-control-group" style="margin-top: 20px;">
        <label for="jj-labs-override-css">
            <strong><?php _e( 'CSS 오버라이드 코드', 'jj-style-guide' ); ?></strong>
        </label>
        <p class="description" style="margin-bottom: 10px;">
            <?php _e( '아래 텍스트 영역에 CSS 코드를 입력하세요. 스타일 센터에서 정의한 CSS 변수(--jj-*)를 사용하여 일관된 스타일을 적용할 수 있습니다.', 'jj-style-guide' ); ?>
        </p>
        <textarea id="jj-labs-override-css" 
                  class="jj-data-field" 
                  data-setting-key="labs[override_css]"
                  style="width: 100%; height: 400px; font-family: monospace; font-size: 13px; line-height: 1.6;"
                  placeholder="<?php _e( "예:\n\n.some-plugin-button {\n    background-color: var(--jj-btn-primary-bg) !important;\n    color: var(--jj-btn-primary-text) !important;\n    border-radius: var(--jj-btn-primary-border-radius) !important;\n}\n\n.some-plugin-input {\n    border-color: var(--jj-form-input-border) !important;\n    border-radius: var(--jj-form-input-border-radius) !important;\n}\n\n", 'jj-style-guide' ); ?>"><?php echo esc_textarea( $labs_options['override_css'] ?? '' ); ?></textarea>
        
        <div style="margin-top: 15px;">
            <button type="button" class="button button-secondary" id="jj-labs-preview-overrides">
                <?php _e( '미리보기', 'jj-style-guide' ); ?>
            </button>
            <button type="button" class="button button-primary" id="jj-labs-save-overrides">
                <?php _e( '저장', 'jj-style-guide' ); ?>
            </button>
            <span class="spinner jj-labs-save-spinner" style="float: none; margin-left: 5px; display: none;"></span>
            <span class="jj-labs-save-success" style="display: none; color: #2271b1; font-weight: 600; margin-left: 10px;"></span>
        </div>

        <div class="jj-labs-css-variables-reference" style="margin-top: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 4px;">
            <h4 style="margin-top: 0;"><?php _e( '사용 가능한 CSS 변수', 'jj-style-guide' ); ?></h4>
            <p class="description" style="margin-bottom: 10px;">
                <?php _e( '스타일 센터에서 정의한 주요 CSS 변수 목록입니다:', 'jj-style-guide' ); ?>
            </p>
            <ul style="margin: 0; padding-left: 20px; font-family: monospace; font-size: 12px;">
                <li><code>var(--jj-primary-color)</code> - 브랜드 Primary 색상</li>
                <li><code>var(--jj-btn-primary-bg)</code> - 버튼 Primary 배경</li>
                <li><code>var(--jj-btn-primary-text)</code> - 버튼 Primary 텍스트</li>
                <li><code>var(--jj-form-input-border)</code> - 폼 입력 필드 테두리</li>
                <li><code>var(--jj-font-ko-family)</code> - 한국어 폰트 패밀리</li>
                <li><?php _e( '... 및 기타 변수들', 'jj-style-guide' ); ?></li>
            </ul>
        </div>
    </div>
</div>

