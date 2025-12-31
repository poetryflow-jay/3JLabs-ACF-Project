<?php
/**
 * 실험실 센터 - 스캐너 탭
 * CSS/HTML/JS 스캐너 기능
 *
 * @version 3.7.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$labs_options = $options['labs'] ?? array();
?>
<div class="jj-labs-scanner-tab">
    <h2><?php _e( 'CSS/HTML/JS 스캐너', 'jj-style-guide' ); ?></h2>
    <p class="description jj-labs-tab-description" data-tab-type="scanner" data-tooltip="labs-tab-scanner">
        <?php _e( '분석하고 싶은 페이지의 URL을 입력하거나, 활성 플러그인/테마 목록에서 대상을 선택한 뒤 스캔을 시작하세요. CSS 선택자, HTML 구조, JavaScript 정보를 추출하여 표시합니다.', 'jj-style-guide' ); ?>
        <span class="dashicons dashicons-editor-help" style="margin-left: 5px; cursor: help;" aria-label="<?php esc_attr_e( '도움말', 'jj-style-guide' ); ?>"></span>
    </p>

    <div class="jj-style-guide-grid jj-grid-2-col" style="margin-top: 20px;">
        <!-- URL 스캔 -->
        <div class="jj-control-group">
            <label for="jj-labs-scan-url">
                <strong><?php _e( 'URL로 스캔하기', 'jj-style-guide' ); ?></strong>
            </label>
            <input type="url" 
                   id="jj-labs-scan-url" 
                   class="jj-data-field" 
                   data-setting-key="labs[scan_url]" 
                   value="<?php echo esc_attr( $labs_options['scan_url'] ?? '' ); ?>"
                   placeholder="https://example.com/page"
                   style="width: 100%; margin-top: 8px;">
            <button type="button" class="button button-primary" id="jj-labs-start-scan-url" style="margin-top: 10px;">
                <?php _e( '분석 시작', 'jj-style-guide' ); ?>
            </button>
            <span class="spinner jj-labs-scan-spinner" style="float: none; margin-left: 5px; display: none;"></span>
        </div>

        <!-- 활성 플러그인/테마 스캔 -->
        <div class="jj-control-group">
            <label for="jj-labs-scan-target">
                <strong><?php _e( '활성 플러그인/테마로 스캔하기', 'jj-style-guide' ); ?></strong>
            </label>
            <select id="jj-labs-scan-target" 
                    class="jj-data-field" 
                    data-setting-key="labs[scan_target]"
                    style="width: 100%; margin-top: 8px;">
                <option value=""><?php _e( '-- 대상 선택 --', 'jj-style-guide' ); ?></option>
                <option value="theme_active"><?php _e( '현재 활성 테마', 'jj-style-guide' ); ?></option>
                <optgroup label="<?php _e( '활성 플러그인', 'jj-style-guide' ); ?>">
                    <?php
                    $active_plugins = (array) get_option( 'active_plugins', array() );
                    foreach ( $active_plugins as $plugin_file ) {
                        $plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file, false, false );
                        $label       = $plugin_data['Name'] ?: $plugin_file;
                        printf(
                            '<option value="%1$s">%2$s</option>',
                            esc_attr( $plugin_file ),
                            esc_html( $label )
                        );
                    }
                    ?>
                </optgroup>
            </select>
            <button type="button" class="button button-primary" id="jj-labs-start-scan-target" style="margin-top: 10px;">
                <?php _e( '분석 시작', 'jj-style-guide' ); ?>
            </button>
            <span class="spinner jj-labs-scan-spinner" style="float: none; margin-left: 5px; display: none;"></span>
            <p class="description" style="margin-top: 8px;">
                <?php _e( '현재 활성 테마 또는 플러그인을 선택하면, 해당 리소스의 스타일 구조를 파악하는 데 도움이 되는 기본 정보를 함께 확인하실 수 있습니다.', 'jj-style-guide' ); ?>
            </p>
        </div>
    </div>

    <!-- 스캔 결과 -->
    <div class="jj-labs-scan-results" id="jj-labs-scan-results" style="margin-top: 30px;">
        <h3><?php _e( '분석 결과', 'jj-style-guide' ); ?></h3>
        <div class="jj-labs-results-empty" style="padding: 40px; text-align: center; background: #f9f9f9; border: 1px dashed #c3c4c7; border-radius: 4px;">
            <p class="description" style="color: #8c8f94;">
                <?php _e( '분석이 완료되면, CSS 선택자 목록, HTML 구조, JavaScript 정보가 이 영역에 표시됩니다.', 'jj-style-guide' ); ?>
            </p>
        </div>
        
        <!-- 결과 탭 -->
        <div class="jj-labs-results-tabs" style="display: none; margin-top: 20px;">
            <div class="jj-tabs-nav">
                <button type="button" class="jj-tab-button is-active" data-tab="results-css">
                    <?php _e( 'CSS 선택자', 'jj-style-guide' ); ?>
                </button>
                <button type="button" class="jj-tab-button" data-tab="results-html">
                    <?php _e( 'HTML 구조', 'jj-style-guide' ); ?>
                </button>
                <button type="button" class="jj-tab-button" data-tab="results-js">
                    <?php _e( 'JavaScript', 'jj-style-guide' ); ?>
                </button>
            </div>

            <!-- CSS 선택자 결과 -->
            <div class="jj-tab-content is-active" data-tab-content="results-css">
                <div class="jj-labs-results-css">
                    <!-- [v3.7.0 '신규'] 색상/폰트 추출 및 충돌 감지 결과 -->
                    <div id="jj-labs-scan-insights" style="display: none; margin-bottom: 25px; padding: 15px; background: #f0f6fc; border: 1px solid #c3c4c7; border-radius: 4px;">
                        <h4 style="margin-top: 0; margin-bottom: 15px;">
                            <span class="dashicons dashicons-lightbulb" style="margin-right: 5px;"></span>
                            <?php _e( '스캔 인사이트', 'jj-style-guide' ); ?>
                        </h4>
                        
                        <!-- 추출된 색상 -->
                        <div id="jj-labs-extracted-colors" style="display: none; margin-bottom: 15px;">
                            <strong><?php _e( '추출된 색상', 'jj-style-guide' ); ?>:</strong>
                            <div id="jj-labs-colors-list" style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;">
                                <!-- 동적으로 생성 -->
                            </div>
                            <button type="button" class="button button-small" id="jj-labs-apply-colors" style="margin-top: 10px; display: none;">
                                <?php _e( '상위 색상을 브랜드 팔레트에 적용', 'jj-style-guide' ); ?>
                            </button>
                        </div>
                        
                        <!-- 추출된 폰트 -->
                        <div id="jj-labs-extracted-fonts" style="display: none; margin-bottom: 15px;">
                            <strong><?php _e( '추출된 폰트', 'jj-style-guide' ); ?>:</strong>
                            <ul id="jj-labs-fonts-list" style="margin: 8px 0 0 0; padding-left: 20px;">
                                <!-- 동적으로 생성 -->
                            </ul>
                            <button type="button" class="button button-small" id="jj-labs-apply-fonts" style="margin-top: 10px; display: none;">
                                <?php _e( '상위 폰트를 타이포그래피에 적용', 'jj-style-guide' ); ?>
                            </button>
                        </div>
                        
                        <!-- 충돌/문제점 -->
                        <div id="jj-labs-conflicts" style="display: none; margin-top: 15px;">
                            <strong><?php _e( '감지된 문제점', 'jj-style-guide' ); ?>:</strong>
                            <div id="jj-labs-conflicts-list" style="margin-top: 8px;">
                                <!-- 동적으로 생성 -->
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <strong><?php _e( 'Class 선택자', 'jj-style-guide' ); ?></strong>
                        <span class="jj-labs-results-count" id="jj-labs-css-classes-count"></span>
                    </div>
                    <textarea id="jj-labs-results-css-classes" 
                              readonly 
                              style="width: 100%; height: 300px; font-family: monospace; font-size: 12px; background: #f9f9f9; border: 1px solid #c3c4c7; padding: 10px;"></textarea>
                    
                    <div style="margin-top: 20px;">
                        <strong><?php _e( 'ID 선택자', 'jj-style-guide' ); ?></strong>
                        <span class="jj-labs-results-count" id="jj-labs-css-ids-count"></span>
                    </div>
                    <textarea id="jj-labs-results-css-ids" 
                              readonly 
                              style="width: 100%; height: 200px; font-family: monospace; font-size: 12px; background: #f9f9f9; border: 1px solid #c3c4c7; padding: 10px;"></textarea>

                    <div style="margin-top: 20px;">
                        <strong><?php _e( '발견된 CSS 스타일 블록', 'jj-style-guide' ); ?></strong>
                    </div>
                    <textarea id="jj-labs-results-css-blocks" 
                              readonly 
                              style="width: 100%; height: 250px; font-family: monospace; font-size: 12px; background: #f9f9f9; border: 1px solid #c3c4c7; padding: 10px;"></textarea>
                </div>
            </div>

            <!-- HTML 구조 결과 -->
            <div class="jj-tab-content" data-tab-content="results-html">
                <div class="jj-labs-results-html">
                    <div style="margin-bottom: 15px;">
                        <strong><?php _e( 'HTML 구조 미리보기', 'jj-style-guide' ); ?></strong>
                        <p class="description"><?php _e( '스캔한 페이지의 HTML 구조 일부입니다. (처음 2000자)', 'jj-style-guide' ); ?></p>
                    </div>
                    <textarea id="jj-labs-results-html" 
                              readonly 
                              style="width: 100%; height: 500px; font-family: monospace; font-size: 12px; background: #f9f9f9; border: 1px solid #c3c4c7; padding: 10px;"></textarea>
                </div>
            </div>

            <!-- JavaScript 결과 -->
            <div class="jj-tab-content" data-tab-content="results-js">
                <div class="jj-labs-results-js">
                    <div style="margin-bottom: 15px;">
                        <strong><?php _e( 'JavaScript 정보', 'jj-style-guide' ); ?></strong>
                        <p class="description"><?php _e( '스캔한 페이지에서 발견된 JavaScript 파일 및 인라인 스크립트 정보입니다.', 'jj-style-guide' ); ?></p>
                    </div>
                    <textarea id="jj-labs-results-js" 
                              readonly 
                              style="width: 100%; height: 500px; font-family: monospace; font-size: 12px; background: #f9f9f9; border: 1px solid #c3c4c7; padding: 10px;"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

