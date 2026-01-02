<?php
/**
 * [Phase 13] Figma Connector Tab
 * 
 * Figma API 연동 설정 UI
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="jj-admin-center-tab-content" data-tab="figma">
    <div class="jj-tab-header">
        <h2><?php esc_html_e( 'Figma 연동', 'acf-css-really-simple-style-management-center' ); ?></h2>
        <p class="description">
            <?php esc_html_e( 'Figma API를 통해 디자인 토큰(색상, 타이포그래피)을 가져오거나, JJ 스타일 토큰을 Figma Variables 형식으로 내보낼 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>
    </div>

    <div class="jj-figma-settings" style="max-width: 800px;">
        
        <!-- API 설정 -->
        <div class="jj-figma-section" style="background: #fff; border: 1px solid #c3c4c7; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
            <h3 style="margin-top: 0;"><?php esc_html_e( 'API 설정', 'acf-css-really-simple-style-management-center' ); ?></h3>
            
            <div class="jj-figma-field" style="margin-bottom: 16px;">
                <label for="jj-figma-api-token" style="display: block; font-weight: 600; margin-bottom: 6px;">
                    <?php esc_html_e( 'Figma Personal Access Token', 'acf-css-really-simple-style-management-center' ); ?>
                </label>
                <input type="password" 
                       id="jj-figma-api-token" 
                       class="regular-text" 
                       placeholder="figd_xxxxx..."
                       style="width: 100%; max-width: 400px;" />
                <p class="description">
                    <?php echo wp_kses_post( sprintf(
                        /* translators: %s: Figma settings URL */
                        __( 'Figma 계정 설정 > Personal access tokens에서 발급받으세요. <a href="%s" target="_blank" rel="noopener">Figma 설정 열기</a>', 'acf-css-really-simple-style-management-center' ),
                        'https://www.figma.com/settings'
                    ) ); ?>
                </p>
            </div>

            <div class="jj-figma-field" style="margin-bottom: 16px;">
                <label for="jj-figma-file-key" style="display: block; font-weight: 600; margin-bottom: 6px;">
                    <?php esc_html_e( 'Figma 파일 키', 'acf-css-really-simple-style-management-center' ); ?>
                </label>
                <input type="text" 
                       id="jj-figma-file-key" 
                       class="regular-text" 
                       placeholder="ABC123xyz..."
                       style="width: 100%; max-width: 400px;" />
                <p class="description">
                    <?php esc_html_e( 'Figma 파일 URL에서 /file/ 또는 /design/ 뒤의 키를 입력하세요. (예: figma.com/file/ABC123xyz/...)', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
            </div>

            <div class="jj-figma-actions" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="button" class="button button-primary" id="jj-figma-test-connection">
                    <span class="dashicons dashicons-admin-plugins" style="margin-top: 4px;"></span>
                    <?php esc_html_e( '연결 테스트', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
                <button type="button" class="button" id="jj-figma-save-settings">
                    <span class="dashicons dashicons-saved" style="margin-top: 4px;"></span>
                    <?php esc_html_e( '설정 저장', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
            </div>

            <div id="jj-figma-connection-status" style="margin-top: 12px;"></div>
        </div>

        <!-- 가져오기 -->
        <div class="jj-figma-section" style="background: #fff; border: 1px solid #c3c4c7; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
            <h3 style="margin-top: 0;"><?php esc_html_e( 'Figma에서 가져오기', 'acf-css-really-simple-style-management-center' ); ?></h3>
            <p class="description" style="margin-bottom: 16px;">
                <?php esc_html_e( 'Figma 파일의 스타일(색상, 텍스트)을 가져와서 미리보기합니다. 적용하려면 "JJ 스타일에 적용" 버튼을 클릭하세요.', 'acf-css-really-simple-style-management-center' ); ?>
            </p>

            <div class="jj-figma-actions" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px;">
                <button type="button" class="button button-primary" id="jj-figma-import-tokens">
                    <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                    <?php esc_html_e( 'Figma 스타일 가져오기', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
            </div>

            <div id="jj-figma-import-preview" style="display: none;">
                <h4><?php esc_html_e( '가져온 스타일 미리보기', 'acf-css-really-simple-style-management-center' ); ?></h4>
                <div id="jj-figma-import-colors" style="margin-bottom: 16px;"></div>
                <div id="jj-figma-import-typography" style="margin-bottom: 16px;"></div>
                <button type="button" class="button button-primary" id="jj-figma-apply-tokens" style="display: none;">
                    <?php esc_html_e( 'JJ 스타일에 적용', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
            </div>
        </div>

        <!-- 내보내기 -->
        <div class="jj-figma-section" style="background: #fff; border: 1px solid #c3c4c7; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
            <h3 style="margin-top: 0;"><?php esc_html_e( 'Figma로 내보내기', 'acf-css-really-simple-style-management-center' ); ?></h3>
            <p class="description" style="margin-bottom: 16px;">
                <?php esc_html_e( 'JJ 스타일 허브의 디자인 토큰을 Figma Variables JSON 형식으로 내보냅니다. Figma에서 Tokens Studio 등의 플러그인으로 가져올 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
            </p>

            <div class="jj-figma-actions" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="button" class="button" id="jj-figma-export-tokens">
                    <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span>
                    <?php esc_html_e( 'Figma Variables JSON 다운로드', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
            </div>
        </div>

        <!-- 가이드 -->
        <div class="jj-figma-section" style="background: #f8fafc; border: 1px solid #c3c4c7; border-radius: 10px; padding: 20px;">
            <h3 style="margin-top: 0;"><?php esc_html_e( '사용 가이드', 'acf-css-really-simple-style-management-center' ); ?></h3>
            <ol style="margin: 0; padding-left: 18px; line-height: 1.8;">
                <li><?php esc_html_e( 'Figma 계정에서 Personal Access Token을 발급받습니다.', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php esc_html_e( '동기화할 Figma 파일의 URL에서 파일 키를 복사합니다.', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php esc_html_e( '"연결 테스트"로 API 연결을 확인합니다.', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php esc_html_e( '"Figma 스타일 가져오기"로 색상/타이포그래피를 가져옵니다.', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php esc_html_e( '반대로 JJ 스타일을 Figma로 내보내려면 JSON을 다운로드하세요.', 'acf-css-really-simple-style-management-center' ); ?></li>
            </ol>
            <p style="margin-top: 12px; font-size: 12px; color: #50575e;">
                <strong><?php esc_html_e( '참고:', 'acf-css-really-simple-style-management-center' ); ?></strong>
                <?php esc_html_e( 'API 토큰은 암호화되어 저장되며, 플러그인에는 어떤 API 키도 내장되어 있지 않습니다.', 'acf-css-really-simple-style-management-center' ); ?>
            </p>
        </div>
    </div>
</div>
