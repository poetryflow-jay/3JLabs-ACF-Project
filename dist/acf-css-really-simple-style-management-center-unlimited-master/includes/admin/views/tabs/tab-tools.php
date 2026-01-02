<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="jj-admin-center-tab-content" data-tab="tools">
    <div class="jj-admin-center-general-form">
        <h3><?php esc_html_e( '도구 (Tools)', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <p class="description">
            <?php esc_html_e( '플러그인 및 테마 대량 설치 도구입니다.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>

        <div class="jj-bulk-container" style="margin-top: 20px;">
            <div class="jj-dropzone" id="jj-dropzone" style="border: 3px dashed #c3c4c7; padding: 40px; text-align: center; cursor: pointer; background: #fff;">
                <div class="jj-dropzone-content">
                    <span class="dashicons dashicons-cloud-upload" style="font-size: 64px; height: 64px; width: 64px; color: #2271b1;"></span>
                    <h3><?php esc_html_e( 'ZIP 파일을 여기에 드래그하세요', 'acf-css-really-simple-style-management-center' ); ?></h3>
                    <p><?php esc_html_e( '또는 클릭하여 파일 선택 (최대 30개)', 'acf-css-really-simple-style-management-center' ); ?></p>
                    <input type="file" id="jj-file-input" multiple accept=".zip" style="display: none;">
                </div>
            </div>

            <div class="jj-options" style="margin: 15px 0;">
                <label>
                    <input type="checkbox" id="jj-auto-activate" value="1" checked>
                    <?php esc_html_e( '설치 후 자동 활성화', 'acf-css-really-simple-style-management-center' ); ?>
                </label>
            </div>

            <div class="jj-file-list" id="jj-file-list" style="margin-bottom: 20px;"></div>

            <button id="jj-start-install" class="button button-primary button-hero" disabled>
                <?php esc_html_e( '설치 시작하기', 'acf-css-really-simple-style-management-center' ); ?>
            </button>

            <div class="jj-progress-area" id="jj-progress-area" style="display: none; margin-top: 20px; border: 1px solid #ccd0d4; padding: 15px; background: #fff;">
                <div class="jj-progress-bar" style="height: 20px; background: #f0f0f1; border-radius: 10px; overflow: hidden; margin-bottom: 10px;">
                    <div class="jj-progress-fill" style="width: 0%; height: 100%; background: #2271b1; transition: width 0.3s;"></div>
                </div>
                <div class="jj-status-text" style="font-weight: bold;"><?php esc_html_e( '준비 중...', 'acf-css-really-simple-style-management-center' ); ?></div>
                <ul class="jj-log-list" style="margin-top: 10px; max-height: 200px; overflow-y: auto; font-size: 12px; color: #666; list-style: none; padding: 0;"></ul>
            </div>
        </div>

        <hr style="margin: 30px 0;">

        <!-- [Phase 10.4] Builder 1-click Sync UI -->
        <div id="jj-builder-sync" style="margin-top: 10px; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 6px;">
            <h3 style="margin-top: 0;"><?php esc_html_e( '빌더 글로벌 토큰 동기화 (1-click)', 'acf-css-really-simple-style-management-center' ); ?></h3>
            <p class="description" style="margin-top: 6px;">
                <?php esc_html_e( '선택한 빌더의 글로벌 컬러/타이포 설정을 JJ 토큰으로 동기화합니다. 동기화 전 자동 백업이 생성되며, 언제든 롤백할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
            </p>

            <div style="margin-top: 15px;">
                <h4 style="margin: 0 0 8px 0;"><?php esc_html_e( '대상 빌더', 'acf-css-really-simple-style-management-center' ); ?></h4>
                <div style="display:flex; flex-wrap: wrap; gap: 14px;">
                    <label style="display:inline-flex; gap:6px; align-items:center;">
                        <input type="checkbox" class="jj-bsync-builder" value="elementor" />
                        <span class="jj-bsync-builder-label">Elementor</span>
                        <span class="jj-bsync-status" style="color:#666; font-size:12px;"></span>
                    </label>
                    <label style="display:inline-flex; gap:6px; align-items:center;">
                        <input type="checkbox" class="jj-bsync-builder" value="bricks" />
                        <span class="jj-bsync-builder-label">Bricks</span>
                        <span class="jj-bsync-status" style="color:#666; font-size:12px;"></span>
                    </label>
                    <label style="display:inline-flex; gap:6px; align-items:center;">
                        <input type="checkbox" class="jj-bsync-builder" value="beaver" />
                        <span class="jj-bsync-builder-label">Beaver Builder</span>
                        <span class="jj-bsync-status" style="color:#666; font-size:12px;"></span>
                    </label>
                    <label style="display:inline-flex; gap:6px; align-items:center;">
                        <input type="checkbox" class="jj-bsync-builder" value="divi" />
                        <span class="jj-bsync-builder-label">Divi</span>
                        <span class="jj-bsync-status" style="color:#666; font-size:12px;"></span>
                    </label>
                    <label style="display:inline-flex; gap:6px; align-items:center;">
                        <input type="checkbox" class="jj-bsync-builder" value="wpbakery" />
                        <span class="jj-bsync-builder-label">WPBakery</span>
                        <span class="jj-bsync-status" style="color:#666; font-size:12px;"></span>
                    </label>
                </div>
            </div>

            <div style="margin-top: 15px;">
                <h4 style="margin: 0 0 8px 0;"><?php esc_html_e( '동기화 범위', 'acf-css-really-simple-style-management-center' ); ?></h4>
                <label style="margin-right: 12px;">
                    <input type="checkbox" id="jj-bsync-scope-colors" checked />
                    <?php esc_html_e( '컬러', 'acf-css-really-simple-style-management-center' ); ?>
                </label>
                <label>
                    <input type="checkbox" id="jj-bsync-scope-typography" checked />
                    <?php esc_html_e( '타이포그래피', 'acf-css-really-simple-style-management-center' ); ?>
                </label>
            </div>

            <div id="jj-builder-sync-elementor-mapping" style="display:none; margin-top: 18px; padding: 14px; background:#f6f7f7; border: 1px solid #c3c4c7; border-radius: 6px;">
                <h4 style="margin:0 0 10px 0;"><?php esc_html_e( 'Elementor 타이포그래피 매핑', 'acf-css-really-simple-style-management-center' ); ?></h4>
                <p class="description" style="margin-top:0;">
                    <?php esc_html_e( 'Elementor의 Primary/Secondary/Text/Accent 글로벌 타이포를 JJ 타이포 태그에 연결합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <table class="form-table" role="presentation" style="margin-top: 0;">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="jj-bsync-el-typo-primary">Primary</label></th>
                        <td><select id="jj-bsync-el-typo-primary"></select></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="jj-bsync-el-typo-secondary">Secondary</label></th>
                        <td><select id="jj-bsync-el-typo-secondary"></select></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="jj-bsync-el-typo-text">Text</label></th>
                        <td><select id="jj-bsync-el-typo-text"></select></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="jj-bsync-el-typo-accent">Accent</label></th>
                        <td><select id="jj-bsync-el-typo-accent"></select></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 15px;">
                <button type="button" class="button" id="jj-bsync-preview">
                    <?php esc_html_e( '변경 미리보기', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
                <button type="button" class="button button-primary" id="jj-bsync-run">
                    <?php esc_html_e( '백업 후 동기화 실행', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
                <span class="spinner" id="jj-bsync-spinner" style="float:none; margin-left: 6px; display:none;"></span>
            </div>

            <div id="jj-bsync-result" style="margin-top: 12px;"></div>

            <hr style="margin: 18px 0;">

            <h4 style="margin: 0 0 8px 0;"><?php esc_html_e( '롤백 (최근 백업)', 'acf-css-really-simple-style-management-center' ); ?></h4>
            <div id="jj-bsync-backups"></div>
        </div>
    </div>
</div>

<!-- Bulk Installer용 간단 스크립트 (이미 admin-center.js가 로드되지만, 특화 기능을 위해 인라인 추가) -->
<script>
jQuery(document).ready(function($) {
    if ($('#jj-dropzone').length === 0) return;

    var filesQueue = [];
    var isProcessing = false;
    var dropzone = $('#jj-dropzone');
    var fileInput = $('#jj-file-input');

    dropzone.on('click', function() { fileInput.click(); });
    dropzone.on('dragover', function(e) { e.preventDefault(); dropzone.css('background', '#f0f6fc'); });
    dropzone.on('dragleave', function(e) { e.preventDefault(); dropzone.css('background', '#fff'); });
    dropzone.on('drop', function(e) {
        e.preventDefault();
        dropzone.css('background', '#fff');
        handleFiles(e.originalEvent.dataTransfer.files);
    });
    fileInput.on('change', function() { handleFiles(this.files); });

    function handleFiles(files) {
        if (files.length + filesQueue.length > 30) {
            alert('<?php esc_html_e( '최대 30개까지만 가능합니다.', 'acf-css-really-simple-style-management-center' ); ?>');
            return;
        }
        $.each(files, function(i, file) {
            if (file.name.split('.').pop().toLowerCase() !== 'zip') return;
            filesQueue.push(file);
            $('#jj-file-list').append('<div style="padding:5px; border-bottom:1px solid #eee;">' + file.name + ' <span style="float:right; color:#666;">대기 중</span></div>');
        });
        if (filesQueue.length > 0) $('#jj-start-install').prop('disabled', false).text('<?php esc_html_e( '설치 시작', 'acf-css-really-simple-style-management-center' ); ?> (' + filesQueue.length + ')');
    }

    // 설치 로직은 별도 AJAX 액션이 필요함 (jj_bulk_install_upload 등)
    // Core Plugin에 해당 액션 핸들러를 추가하거나, 
    // 여기서는 UI만 보여주고 실제 동작은 추후 구현할 수도 있음.
    // 하지만 "통합"이 목표이므로 Core에 핸들러도 넣어야 함.
});
</script>

