<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="jj-admin-center-tab-content" data-tab="tools">
    <div class="jj-admin-center-general-form">
        <h3><?php esc_html_e( '도구 (Tools)', 'jj-style-guide' ); ?></h3>
        <p class="description">
            <?php esc_html_e( '플러그인 및 테마 대량 설치 도구입니다.', 'jj-style-guide' ); ?>
        </p>

        <div class="jj-bulk-container" style="margin-top: 20px;">
            <div class="jj-dropzone" id="jj-dropzone" style="border: 3px dashed #c3c4c7; padding: 40px; text-align: center; cursor: pointer; background: #fff;">
                <div class="jj-dropzone-content">
                    <span class="dashicons dashicons-cloud-upload" style="font-size: 64px; height: 64px; width: 64px; color: #2271b1;"></span>
                    <h3><?php esc_html_e( 'ZIP 파일을 여기에 드래그하세요', 'jj-style-guide' ); ?></h3>
                    <p><?php esc_html_e( '또는 클릭하여 파일 선택 (최대 30개)', 'jj-style-guide' ); ?></p>
                    <input type="file" id="jj-file-input" multiple accept=".zip" style="display: none;">
                </div>
            </div>

            <div class="jj-options" style="margin: 15px 0;">
                <label>
                    <input type="checkbox" id="jj-auto-activate" value="1" checked>
                    <?php esc_html_e( '설치 후 자동 활성화', 'jj-style-guide' ); ?>
                </label>
            </div>

            <div class="jj-file-list" id="jj-file-list" style="margin-bottom: 20px;"></div>

            <button id="jj-start-install" class="button button-primary button-hero" disabled>
                <?php esc_html_e( '설치 시작하기', 'jj-style-guide' ); ?>
            </button>

            <div class="jj-progress-area" id="jj-progress-area" style="display: none; margin-top: 20px; border: 1px solid #ccd0d4; padding: 15px; background: #fff;">
                <div class="jj-progress-bar" style="height: 20px; background: #f0f0f1; border-radius: 10px; overflow: hidden; margin-bottom: 10px;">
                    <div class="jj-progress-fill" style="width: 0%; height: 100%; background: #2271b1; transition: width 0.3s;"></div>
                </div>
                <div class="jj-status-text" style="font-weight: bold;"><?php esc_html_e( '준비 중...', 'jj-style-guide' ); ?></div>
                <ul class="jj-log-list" style="margin-top: 10px; max-height: 200px; overflow-y: auto; font-size: 12px; color: #666; list-style: none; padding: 0;"></ul>
            </div>
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
            alert('<?php esc_html_e( '최대 30개까지만 가능합니다.', 'jj-style-guide' ); ?>');
            return;
        }
        $.each(files, function(i, file) {
            if (file.name.split('.').pop().toLowerCase() !== 'zip') return;
            filesQueue.push(file);
            $('#jj-file-list').append('<div style="padding:5px; border-bottom:1px solid #eee;">' + file.name + ' <span style="float:right; color:#666;">대기 중</span></div>');
        });
        if (filesQueue.length > 0) $('#jj-start-install').prop('disabled', false).text('<?php esc_html_e( '설치 시작', 'jj-style-guide' ); ?> (' + filesQueue.length + ')');
    }

    // 설치 로직은 별도 AJAX 액션이 필요함 (jj_bulk_install_upload 등)
    // Core Plugin에 해당 액션 핸들러를 추가하거나, 
    // 여기서는 UI만 보여주고 실제 동작은 추후 구현할 수도 있음.
    // 하지만 "통합"이 목표이므로 Core에 핸들러도 넣어야 함.
});
</script>

