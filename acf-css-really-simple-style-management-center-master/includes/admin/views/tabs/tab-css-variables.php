<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="jj-admin-center-tab-content" data-tab="css-variables">
    <div class="jj-css-vars-header">
        <h3><?php esc_html_e( 'CSS 변수 관리', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <p class="description">
            <?php esc_html_e( '현재 설정된 CSS 변수를 확인하고, 외부 CSS/테마에서 변수를 추출하거나, 다양한 포맷으로 내보내기/가져오기 할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>
    </div>

    <!-- 탭 네비게이션 -->
    <div class="jj-css-vars-tabs">
        <button type="button" class="jj-css-vars-tab active" data-subtab="current">
            <span class="dashicons dashicons-visibility"></span>
            <?php esc_html_e( '현재 변수', 'acf-css-really-simple-style-management-center' ); ?>
        </button>
        <button type="button" class="jj-css-vars-tab" data-subtab="extract">
            <span class="dashicons dashicons-download"></span>
            <?php esc_html_e( '추출', 'acf-css-really-simple-style-management-center' ); ?>
        </button>
        <button type="button" class="jj-css-vars-tab" data-subtab="export">
            <span class="dashicons dashicons-upload"></span>
            <?php esc_html_e( '내보내기', 'acf-css-really-simple-style-management-center' ); ?>
        </button>
        <button type="button" class="jj-css-vars-tab" data-subtab="import">
            <span class="dashicons dashicons-migrate"></span>
            <?php esc_html_e( '가져오기', 'acf-css-really-simple-style-management-center' ); ?>
        </button>
    </div>

    <!-- 현재 변수 뷰어 -->
    <div class="jj-css-vars-panel active" data-panel="current">
        <div class="jj-css-vars-toolbar">
            <div class="jj-css-vars-filter">
                <label><?php esc_html_e( '카테고리:', 'acf-css-really-simple-style-management-center' ); ?></label>
                <select id="jj-css-vars-category-filter">
                    <option value="all"><?php esc_html_e( '전체', 'acf-css-really-simple-style-management-center' ); ?></option>
                    <option value="colors"><?php esc_html_e( '색상', 'acf-css-really-simple-style-management-center' ); ?></option>
                    <option value="typography"><?php esc_html_e( '타이포그래피', 'acf-css-really-simple-style-management-center' ); ?></option>
                    <option value="buttons"><?php esc_html_e( '버튼', 'acf-css-really-simple-style-management-center' ); ?></option>
                    <option value="forms"><?php esc_html_e( '폼', 'acf-css-really-simple-style-management-center' ); ?></option>
                    <option value="system"><?php esc_html_e( '시스템', 'acf-css-really-simple-style-management-center' ); ?></option>
                </select>
            </div>
            <div class="jj-css-vars-search">
                <input type="text" id="jj-css-vars-search" placeholder="<?php esc_attr_e( '변수명 검색...', 'acf-css-really-simple-style-management-center' ); ?>" />
            </div>
            <button type="button" class="button button-secondary" id="jj-css-vars-refresh">
                <span class="dashicons dashicons-update"></span>
                <?php esc_html_e( '새로고침', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
        </div>

        <div class="jj-css-vars-stats">
            <span class="jj-css-vars-count">
                <strong id="jj-css-vars-total">0</strong> <?php esc_html_e( '개 변수', 'acf-css-really-simple-style-management-center' ); ?>
            </span>
        </div>

        <div class="jj-css-vars-list" id="jj-css-vars-list">
            <div class="jj-css-vars-loading">
                <span class="spinner is-active"></span>
                <?php esc_html_e( 'CSS 변수 로드 중...', 'acf-css-really-simple-style-management-center' ); ?>
            </div>
        </div>

        <div class="jj-css-vars-quick-copy">
            <h4><?php esc_html_e( '빠른 복사', 'acf-css-really-simple-style-management-center' ); ?></h4>
            <div class="jj-css-vars-copy-buttons">
                <button type="button" class="button" data-copy-format="css">
                    <span class="dashicons dashicons-editor-code"></span> CSS
                </button>
                <button type="button" class="button" data-copy-format="json">
                    <span class="dashicons dashicons-media-code"></span> JSON
                </button>
                <button type="button" class="button" data-copy-format="scss">
                    <span class="dashicons dashicons-admin-customizer"></span> SCSS
                </button>
            </div>
        </div>
    </div>

    <!-- 추출 패널 -->
    <div class="jj-css-vars-panel" data-panel="extract">
        <div class="jj-css-vars-extract-options">
            <h4><?php esc_html_e( 'CSS 변수 추출', 'acf-css-really-simple-style-management-center' ); ?></h4>
            <p class="description">
                <?php esc_html_e( '외부 CSS 파일이나 테마에서 CSS 변수를 추출합니다.', 'acf-css-really-simple-style-management-center' ); ?>
            </p>

            <div class="jj-css-vars-extract-source">
                <label>
                    <input type="radio" name="jj_extract_source" value="url" checked />
                    <?php esc_html_e( 'URL에서 추출', 'acf-css-really-simple-style-management-center' ); ?>
                </label>
                <label>
                    <input type="radio" name="jj_extract_source" value="css" />
                    <?php esc_html_e( 'CSS 직접 입력', 'acf-css-really-simple-style-management-center' ); ?>
                </label>
                <label>
                    <input type="radio" name="jj_extract_source" value="theme" />
                    <?php esc_html_e( '현재 테마 스캔', 'acf-css-really-simple-style-management-center' ); ?>
                </label>
            </div>

            <div class="jj-css-vars-extract-input" data-source="url">
                <input type="url" id="jj-extract-url" class="regular-text" placeholder="https://example.com/style.css" />
            </div>

            <div class="jj-css-vars-extract-input" data-source="css" style="display: none;">
                <textarea id="jj-extract-css" rows="10" placeholder=":root {&#10;  --color-primary: #2271b1;&#10;  --font-size-base: 16px;&#10;}"></textarea>
            </div>

            <div class="jj-css-vars-extract-input" data-source="theme" style="display: none;">
                <p class="jj-theme-info">
                    <strong><?php esc_html_e( '현재 테마:', 'acf-css-really-simple-style-management-center' ); ?></strong>
                    <?php echo esc_html( wp_get_theme()->get( 'Name' ) ); ?>
                </p>
                <p class="description">
                    <?php esc_html_e( '테마의 CSS 파일들을 스캔하여 정의된 CSS 변수를 자동으로 찾습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
            </div>

            <div class="jj-css-vars-extract-actions">
                <button type="button" class="button button-primary" id="jj-btn-extract">
                    <span class="dashicons dashicons-search"></span>
                    <?php esc_html_e( '변수 추출', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
            </div>
        </div>

        <div class="jj-css-vars-extract-result" id="jj-extract-result" style="display: none;">
            <h4>
                <?php esc_html_e( '추출된 변수', 'acf-css-really-simple-style-management-center' ); ?>
                <span class="jj-extract-count"></span>
            </h4>
            <div class="jj-css-vars-list" id="jj-extracted-vars-list"></div>
            <div class="jj-css-vars-extract-apply">
                <label>
                    <input type="checkbox" id="jj-extract-merge" checked />
                    <?php esc_html_e( '기존 설정과 병합 (체크 해제 시 덮어쓰기)', 'acf-css-really-simple-style-management-center' ); ?>
                </label>
                <button type="button" class="button button-primary" id="jj-btn-apply-extracted">
                    <span class="dashicons dashicons-yes"></span>
                    <?php esc_html_e( '설정에 적용', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- 내보내기 패널 -->
    <div class="jj-css-vars-panel" data-panel="export">
        <h4><?php esc_html_e( '내보내기 포맷', 'acf-css-really-simple-style-management-center' ); ?></h4>
        <p class="description">
            <?php esc_html_e( '현재 설정된 CSS 변수를 다양한 포맷으로 내보냅니다.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>

        <div class="jj-css-vars-export-formats">
            <label class="jj-export-format-option">
                <input type="radio" name="jj_export_format" value="css" checked />
                <span class="jj-format-card">
                    <span class="dashicons dashicons-editor-code"></span>
                    <strong>CSS</strong>
                    <span class="description"><?php esc_html_e( '표준 CSS 변수 형식', 'acf-css-really-simple-style-management-center' ); ?></span>
                </span>
            </label>
            <label class="jj-export-format-option">
                <input type="radio" name="jj_export_format" value="json" />
                <span class="jj-format-card">
                    <span class="dashicons dashicons-media-code"></span>
                    <strong>JSON</strong>
                    <span class="description"><?php esc_html_e( '구조화된 JSON 데이터', 'acf-css-really-simple-style-management-center' ); ?></span>
                </span>
            </label>
            <label class="jj-export-format-option">
                <input type="radio" name="jj_export_format" value="scss" />
                <span class="jj-format-card">
                    <span class="dashicons dashicons-admin-customizer"></span>
                    <strong>SCSS</strong>
                    <span class="description"><?php esc_html_e( 'SCSS 변수 + CSS 매핑', 'acf-css-really-simple-style-management-center' ); ?></span>
                </span>
            </label>
            <label class="jj-export-format-option">
                <input type="radio" name="jj_export_format" value="design-tokens" />
                <span class="jj-format-card">
                    <span class="dashicons dashicons-layout"></span>
                    <strong>Design Tokens</strong>
                    <span class="description"><?php esc_html_e( 'W3C 표준 디자인 토큰', 'acf-css-really-simple-style-management-center' ); ?></span>
                </span>
            </label>
        </div>

        <div class="jj-css-vars-export-actions">
            <button type="button" class="button button-primary" id="jj-btn-export">
                <span class="dashicons dashicons-upload"></span>
                <?php esc_html_e( '내보내기', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
        </div>

        <div class="jj-css-vars-export-result" id="jj-export-result" style="display: none;">
            <div class="jj-export-header">
                <h4><?php esc_html_e( '내보내기 결과', 'acf-css-really-simple-style-management-center' ); ?></h4>
                <div class="jj-export-actions">
                    <button type="button" class="button button-small" id="jj-btn-copy-export">
                        <span class="dashicons dashicons-clipboard"></span>
                        <?php esc_html_e( '복사', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                    <button type="button" class="button button-small" id="jj-btn-download-export">
                        <span class="dashicons dashicons-download"></span>
                        <?php esc_html_e( '다운로드', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                </div>
            </div>
            <textarea id="jj-export-output" rows="15" readonly></textarea>
        </div>
    </div>

    <!-- 가져오기 패널 -->
    <div class="jj-css-vars-panel" data-panel="import">
        <h4><?php esc_html_e( '가져오기', 'acf-css-really-simple-style-management-center' ); ?></h4>
        <p class="description">
            <?php esc_html_e( 'CSS, JSON, 또는 Design Tokens 포맷의 변수를 가져와 현재 설정에 적용합니다.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>

        <div class="jj-css-vars-import-input">
            <label for="jj-import-content"><?php esc_html_e( '가져올 내용', 'acf-css-really-simple-style-management-center' ); ?></label>
            <textarea id="jj-import-content" rows="12" placeholder="<?php esc_attr_e( 'CSS 변수, JSON, 또는 Design Tokens 포맷을 붙여넣기 하세요...', 'acf-css-really-simple-style-management-center' ); ?>"></textarea>
        </div>

        <div class="jj-css-vars-import-options">
            <label>
                <input type="checkbox" id="jj-import-merge" checked />
                <?php esc_html_e( '기존 설정과 병합 (체크 해제 시 덮어쓰기)', 'acf-css-really-simple-style-management-center' ); ?>
            </label>
            <label>
                <input type="checkbox" id="jj-import-preview" checked />
                <?php esc_html_e( '적용 전 미리보기', 'acf-css-really-simple-style-management-center' ); ?>
            </label>
        </div>

        <div class="jj-css-vars-import-actions">
            <button type="button" class="button button-secondary" id="jj-btn-import-preview">
                <span class="dashicons dashicons-visibility"></span>
                <?php esc_html_e( '미리보기', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" class="button button-primary" id="jj-btn-import-apply">
                <span class="dashicons dashicons-yes-alt"></span>
                <?php esc_html_e( '가져오기 및 적용', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
        </div>

        <div class="jj-css-vars-import-preview" id="jj-import-preview-result" style="display: none;">
            <h4>
                <?php esc_html_e( '가져올 변수 미리보기', 'acf-css-really-simple-style-management-center' ); ?>
                <span class="jj-import-count"></span>
            </h4>
            <div class="jj-css-vars-list" id="jj-import-vars-list"></div>
        </div>
    </div>
</div>
