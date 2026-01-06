<?php
/**
 * Email Template Builder View
 *
 * @package ACF_Mail_SMTP
 * @version 2.3.0
 * @since Phase 49-2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$builder = ACF_Mail_SMTP_Email_Template_Builder::get_instance();
$templates = $builder->get_templates();
$block_types = $builder->get_block_types();
$presets = $builder->get_presets();
?>
<div class="wrap jj-wrap">
    <div class="jj-admin-header">
        <h1 class="jj-admin-title">
            <span class="dashicons dashicons-editor-table"></span>
            <?php esc_html_e( '이메일 템플릿 빌더', 'acf-mail-smtp' ); ?>
        </h1>
        <p class="jj-admin-subtitle">
            <?php esc_html_e( '드래그 앤 드롭으로 아름다운 이메일 템플릿을 만들어보세요.', 'acf-mail-smtp' ); ?>
        </p>
    </div>

    <div class="jj-template-builder-container">
        <!-- 템플릿 목록 사이드바 -->
        <div class="jj-template-sidebar">
            <div class="jj-sidebar-section">
                <h3>
                    <?php esc_html_e( '내 템플릿', 'acf-mail-smtp' ); ?>
                    <button type="button" class="button button-small" id="jj-new-template">
                        <span class="dashicons dashicons-plus-alt2"></span>
                    </button>
                </h3>
                <div class="jj-template-list" id="jj-template-list">
                    <?php if ( empty( $templates ) ) : ?>
                        <p class="jj-no-templates">
                            <?php esc_html_e( '저장된 템플릿이 없습니다.', 'acf-mail-smtp' ); ?>
                        </p>
                    <?php else : ?>
                        <?php foreach ( $templates as $template ) : ?>
                            <div class="jj-template-item" data-id="<?php echo esc_attr( $template['id'] ); ?>">
                                <span class="jj-template-name"><?php echo esc_html( $template['name'] ); ?></span>
                                <div class="jj-template-actions">
                                    <button type="button" class="jj-edit-template" title="<?php esc_attr_e( '편집', 'acf-mail-smtp' ); ?>">
                                        <span class="dashicons dashicons-edit"></span>
                                    </button>
                                    <button type="button" class="jj-duplicate-template" title="<?php esc_attr_e( '복제', 'acf-mail-smtp' ); ?>">
                                        <span class="dashicons dashicons-admin-page"></span>
                                    </button>
                                    <button type="button" class="jj-delete-template" title="<?php esc_attr_e( '삭제', 'acf-mail-smtp' ); ?>">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="jj-sidebar-section">
                <h3><?php esc_html_e( '프리셋 템플릿', 'acf-mail-smtp' ); ?></h3>
                <div class="jj-preset-list">
                    <?php foreach ( $presets as $preset_id => $preset ) : ?>
                        <div class="jj-preset-item" data-preset="<?php echo esc_attr( $preset_id ); ?>">
                            <span class="jj-preset-name"><?php echo esc_html( $preset['name'] ); ?></span>
                            <span class="jj-preset-desc"><?php echo esc_html( $preset['description'] ); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 메인 에디터 영역 -->
        <div class="jj-template-editor">
            <!-- 에디터 툴바 -->
            <div class="jj-editor-toolbar">
                <div class="jj-toolbar-left">
                    <input type="text" id="jj-template-name" class="jj-template-name-input" placeholder="<?php esc_attr_e( '템플릿 이름', 'acf-mail-smtp' ); ?>" />
                </div>
                <div class="jj-toolbar-right">
                    <button type="button" class="button" id="jj-preview-template">
                        <span class="dashicons dashicons-visibility"></span>
                        <?php esc_html_e( '미리보기', 'acf-mail-smtp' ); ?>
                    </button>
                    <button type="button" class="button button-primary" id="jj-save-template">
                        <span class="dashicons dashicons-saved"></span>
                        <?php esc_html_e( '저장', 'acf-mail-smtp' ); ?>
                    </button>
                </div>
            </div>

            <!-- 블록 팔레트 -->
            <div class="jj-block-palette">
                <h4><?php esc_html_e( '블록 추가', 'acf-mail-smtp' ); ?></h4>
                <div class="jj-block-types">
                    <?php
                    $categories = array(
                        'structure' => __( '구조', 'acf-mail-smtp' ),
                        'content'   => __( '콘텐츠', 'acf-mail-smtp' ),
                        'dynamic'   => __( '동적', 'acf-mail-smtp' ),
                        'advanced'  => __( '고급', 'acf-mail-smtp' ),
                    );

                    foreach ( $categories as $cat_id => $cat_name ) :
                        $cat_blocks = array_filter( $block_types, function( $b ) use ( $cat_id ) {
                            return isset( $b['category'] ) && $b['category'] === $cat_id;
                        } );

                        if ( empty( $cat_blocks ) ) continue;
                        ?>
                        <div class="jj-block-category">
                            <span class="jj-category-label"><?php echo esc_html( $cat_name ); ?></span>
                            <div class="jj-category-blocks">
                                <?php foreach ( $cat_blocks as $block_id => $block ) : ?>
                                    <div class="jj-block-type" data-block="<?php echo esc_attr( $block_id ); ?>" draggable="true">
                                        <span class="dashicons <?php echo esc_attr( $block['icon'] ); ?>"></span>
                                        <span class="jj-block-name"><?php echo esc_html( $block['name'] ); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 캔버스 영역 -->
            <div class="jj-canvas-container">
                <div class="jj-canvas" id="jj-canvas">
                    <div class="jj-canvas-empty">
                        <span class="dashicons dashicons-welcome-add-page"></span>
                        <p><?php esc_html_e( '블록을 드래그하여 템플릿을 구성하세요', 'acf-mail-smtp' ); ?></p>
                        <p class="description"><?php esc_html_e( '또는 프리셋을 선택하여 시작하세요', 'acf-mail-smtp' ); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 블록 설정 패널 -->
        <div class="jj-block-settings-panel" id="jj-block-settings" style="display: none;">
            <div class="jj-panel-header">
                <h3 id="jj-settings-title"><?php esc_html_e( '블록 설정', 'acf-mail-smtp' ); ?></h3>
                <button type="button" class="jj-close-panel">
                    <span class="dashicons dashicons-no-alt"></span>
                </button>
            </div>
            <div class="jj-panel-content" id="jj-settings-content">
                <!-- 동적으로 채워짐 -->
            </div>
        </div>
    </div>

    <!-- 글로벌 설정 -->
    <div class="jj-global-settings">
        <h3><?php esc_html_e( '템플릿 전역 설정', 'acf-mail-smtp' ); ?></h3>
        <div class="jj-settings-grid">
            <div class="jj-setting-item">
                <label for="jj-max-width"><?php esc_html_e( '최대 너비 (px)', 'acf-mail-smtp' ); ?></label>
                <input type="number" id="jj-max-width" value="600" min="400" max="800" />
            </div>
            <div class="jj-setting-item">
                <label for="jj-bg-color"><?php esc_html_e( '배경색', 'acf-mail-smtp' ); ?></label>
                <input type="color" id="jj-bg-color" value="#f4f4f4" />
            </div>
            <div class="jj-setting-item">
                <label for="jj-content-bg"><?php esc_html_e( '콘텐츠 배경색', 'acf-mail-smtp' ); ?></label>
                <input type="color" id="jj-content-bg" value="#ffffff" />
            </div>
            <div class="jj-setting-item">
                <label for="jj-font-family"><?php esc_html_e( '폰트', 'acf-mail-smtp' ); ?></label>
                <select id="jj-font-family">
                    <option value="Arial, sans-serif">Arial</option>
                    <option value="'Helvetica Neue', Helvetica, sans-serif">Helvetica</option>
                    <option value="Georgia, serif">Georgia</option>
                    <option value="'Times New Roman', serif">Times New Roman</option>
                    <option value="Verdana, sans-serif">Verdana</option>
                </select>
            </div>
            <div class="jj-setting-item">
                <label for="jj-border-radius"><?php esc_html_e( '모서리 둥글기 (px)', 'acf-mail-smtp' ); ?></label>
                <input type="number" id="jj-border-radius" value="8" min="0" max="30" />
            </div>
        </div>
    </div>

    <!-- 미리보기 모달 -->
    <div class="jj-modal" id="jj-preview-modal" style="display: none;">
        <div class="jj-modal-overlay"></div>
        <div class="jj-modal-content jj-modal-large">
            <div class="jj-modal-header">
                <h3><?php esc_html_e( '이메일 미리보기', 'acf-mail-smtp' ); ?></h3>
                <button type="button" class="jj-modal-close">
                    <span class="dashicons dashicons-no-alt"></span>
                </button>
            </div>
            <div class="jj-modal-body">
                <div class="jj-preview-tabs">
                    <button type="button" class="jj-preview-tab active" data-view="desktop">
                        <span class="dashicons dashicons-desktop"></span>
                        <?php esc_html_e( '데스크톱', 'acf-mail-smtp' ); ?>
                    </button>
                    <button type="button" class="jj-preview-tab" data-view="mobile">
                        <span class="dashicons dashicons-smartphone"></span>
                        <?php esc_html_e( '모바일', 'acf-mail-smtp' ); ?>
                    </button>
                </div>
                <div class="jj-preview-frame" id="jj-preview-frame">
                    <iframe id="jj-preview-iframe"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Template Builder Styles */
.jj-template-builder-container {
    display: flex;
    gap: 20px;
    min-height: 700px;
}

.jj-template-sidebar {
    width: 280px;
    flex-shrink: 0;
    background: #fff;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.jj-sidebar-section {
    margin-bottom: 20px;
}

.jj-sidebar-section h3 {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0 0 10px 0;
    font-size: 14px;
    font-weight: 600;
}

.jj-template-list {
    max-height: 200px;
    overflow-y: auto;
}

.jj-template-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 10px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    margin-bottom: 5px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.jj-template-item:hover {
    border-color: #2271b1;
    background: #f0f6fc;
}

.jj-template-item.active {
    border-color: #2271b1;
    background: #e7f3ff;
}

.jj-template-actions {
    display: flex;
    gap: 5px;
}

.jj-template-actions button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 2px;
    color: #666;
}

.jj-template-actions button:hover {
    color: #2271b1;
}

.jj-preset-item {
    padding: 10px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    margin-bottom: 5px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.jj-preset-item:hover {
    border-color: #2271b1;
    background: #f0f6fc;
}

.jj-preset-name {
    display: block;
    font-weight: 500;
    margin-bottom: 3px;
}

.jj-preset-desc {
    font-size: 12px;
    color: #666;
}

.jj-template-editor {
    flex: 1;
    background: #fff;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.jj-editor-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 15px;
    border-bottom: 1px solid #e0e0e0;
    margin-bottom: 15px;
}

.jj-template-name-input {
    font-size: 16px;
    font-weight: 500;
    padding: 8px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    width: 300px;
}

.jj-toolbar-right {
    display: flex;
    gap: 10px;
}

.jj-block-palette {
    margin-bottom: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
}

.jj-block-palette h4 {
    margin: 0 0 10px 0;
    font-size: 13px;
}

.jj-block-types {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.jj-block-category {
    flex: 1;
    min-width: 150px;
}

.jj-category-label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.jj-category-blocks {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.jj-block-type {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 6px 10px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    cursor: grab;
    font-size: 12px;
    transition: all 0.2s ease;
}

.jj-block-type:hover {
    border-color: #2271b1;
    background: #f0f6fc;
}

.jj-block-type .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}

.jj-canvas-container {
    background: #e5e5e5;
    border-radius: 6px;
    padding: 20px;
    min-height: 400px;
}

.jj-canvas {
    background: #f4f4f4;
    max-width: 600px;
    margin: 0 auto;
    min-height: 300px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.jj-canvas-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 300px;
    color: #666;
}

.jj-canvas-empty .dashicons {
    font-size: 48px;
    width: 48px;
    height: 48px;
    margin-bottom: 10px;
    opacity: 0.5;
}

.jj-block-settings-panel {
    width: 300px;
    background: #fff;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.jj-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 10px;
    border-bottom: 1px solid #e0e0e0;
    margin-bottom: 15px;
}

.jj-panel-header h3 {
    margin: 0;
    font-size: 14px;
}

.jj-close-panel {
    background: none;
    border: none;
    cursor: pointer;
    color: #666;
}

.jj-global-settings {
    background: #fff;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.jj-global-settings h3 {
    margin: 0 0 15px 0;
    font-size: 14px;
}

.jj-settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
}

.jj-setting-item label {
    display: block;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 5px;
}

.jj-setting-item input,
.jj-setting-item select {
    width: 100%;
    padding: 6px 10px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
}

/* Preview Modal */
.jj-modal-large .jj-modal-content {
    max-width: 900px;
}

.jj-preview-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.jj-preview-tab {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 8px 15px;
    border: 1px solid #e0e0e0;
    background: #fff;
    border-radius: 4px;
    cursor: pointer;
}

.jj-preview-tab.active {
    border-color: #2271b1;
    background: #e7f3ff;
}

.jj-preview-frame {
    background: #e5e5e5;
    padding: 20px;
    border-radius: 6px;
    min-height: 500px;
}

.jj-preview-frame iframe {
    width: 100%;
    height: 500px;
    border: none;
    background: #fff;
    border-radius: 4px;
}

.jj-preview-frame[data-view="mobile"] iframe {
    max-width: 375px;
    margin: 0 auto;
    display: block;
}

/* No templates message */
.jj-no-templates {
    color: #666;
    font-size: 13px;
    padding: 10px;
    text-align: center;
}
</style>

<script>
jQuery(document).ready(function($) {
    // 현재 템플릿 ID
    let currentTemplateId = null;
    let blocks = [];

    // 새 템플릿 버튼
    $('#jj-new-template').on('click', function() {
        currentTemplateId = null;
        blocks = [];
        $('#jj-template-name').val('');
        renderCanvas();
        $('.jj-template-item').removeClass('active');
    });

    // 템플릿 선택
    $(document).on('click', '.jj-edit-template', function(e) {
        e.stopPropagation();
        const $item = $(this).closest('.jj-template-item');
        const templateId = $item.data('id');
        loadTemplate(templateId);
    });

    // 템플릿 복제
    $(document).on('click', '.jj-duplicate-template', function(e) {
        e.stopPropagation();
        const templateId = $(this).closest('.jj-template-item').data('id');
        duplicateTemplate(templateId);
    });

    // 템플릿 삭제
    $(document).on('click', '.jj-delete-template', function(e) {
        e.stopPropagation();
        if (!confirm('<?php echo esc_js( __( '정말 삭제하시겠습니까?', 'acf-mail-smtp' ) ); ?>')) return;
        const templateId = $(this).closest('.jj-template-item').data('id');
        deleteTemplate(templateId);
    });

    // 프리셋 선택
    $(document).on('click', '.jj-preset-item', function() {
        const presetId = $(this).data('preset');
        loadPreset(presetId);
    });

    // 블록 드래그 앤 드롭
    $('.jj-block-type').on('dragstart', function(e) {
        e.originalEvent.dataTransfer.setData('blockType', $(this).data('block'));
    });

    $('#jj-canvas').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('drag-over');
    }).on('dragleave', function() {
        $(this).removeClass('drag-over');
    }).on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('drag-over');
        const blockType = e.originalEvent.dataTransfer.getData('blockType');
        if (blockType) {
            addBlock(blockType);
        }
    });

    // 저장
    $('#jj-save-template').on('click', function() {
        saveTemplate();
    });

    // 미리보기
    $('#jj-preview-template').on('click', function() {
        previewTemplate();
    });

    // 미리보기 탭
    $(document).on('click', '.jj-preview-tab', function() {
        const view = $(this).data('view');
        $('.jj-preview-tab').removeClass('active');
        $(this).addClass('active');
        $('#jj-preview-frame').attr('data-view', view);
    });

    // 모달 닫기
    $(document).on('click', '.jj-modal-close, .jj-modal-overlay', function() {
        $(this).closest('.jj-modal').hide();
    });

    // 함수들
    function loadTemplate(templateId) {
        // AJAX로 템플릿 로드
        currentTemplateId = templateId;
        $('.jj-template-item').removeClass('active');
        $('[data-id="' + templateId + '"]').addClass('active');
        // TODO: 실제 템플릿 데이터 로드
    }

    function loadPreset(presetId) {
        // 프리셋 적용
        currentTemplateId = null;
        $('#jj-template-name').val('새 템플릿');
        // TODO: 프리셋 블록 적용
    }

    function addBlock(blockType) {
        blocks.push({
            type: blockType,
            id: 'block_' + Date.now(),
            settings: {}
        });
        renderCanvas();
    }

    function renderCanvas() {
        const $canvas = $('#jj-canvas');
        if (blocks.length === 0) {
            $canvas.html(`
                <div class="jj-canvas-empty">
                    <span class="dashicons dashicons-welcome-add-page"></span>
                    <p><?php echo esc_js( __( '블록을 드래그하여 템플릿을 구성하세요', 'acf-mail-smtp' ) ); ?></p>
                    <p class="description"><?php echo esc_js( __( '또는 프리셋을 선택하여 시작하세요', 'acf-mail-smtp' ) ); ?></p>
                </div>
            `);
            return;
        }
        // TODO: 블록 렌더링
    }

    function saveTemplate() {
        const templateData = {
            id: currentTemplateId,
            name: $('#jj-template-name').val() || '<?php echo esc_js( __( '새 템플릿', 'acf-mail-smtp' ) ); ?>',
            blocks: blocks,
            settings: {
                max_width: parseInt($('#jj-max-width').val()),
                background_color: $('#jj-bg-color').val(),
                content_bg_color: $('#jj-content-bg').val(),
                font_family: $('#jj-font-family').val(),
                border_radius: parseInt($('#jj-border-radius').val())
            }
        };

        $.ajax({
            url: acfMailSmtp.ajaxUrl,
            type: 'POST',
            data: {
                action: 'acf_mail_smtp_save_template',
                nonce: acfMailSmtp.nonce,
                template: JSON.stringify(templateData)
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || '<?php echo esc_js( __( '오류가 발생했습니다.', 'acf-mail-smtp' ) ); ?>');
                }
            }
        });
    }

    function deleteTemplate(templateId) {
        $.ajax({
            url: acfMailSmtp.ajaxUrl,
            type: 'POST',
            data: {
                action: 'acf_mail_smtp_delete_template',
                nonce: acfMailSmtp.nonce,
                template_id: templateId
            },
            success: function(response) {
                if (response.success) {
                    $('[data-id="' + templateId + '"]').remove();
                } else {
                    alert(response.data.message);
                }
            }
        });
    }

    function duplicateTemplate(templateId) {
        $.ajax({
            url: acfMailSmtp.ajaxUrl,
            type: 'POST',
            data: {
                action: 'acf_mail_smtp_duplicate_template',
                nonce: acfMailSmtp.nonce,
                template_id: templateId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            }
        });
    }

    function previewTemplate() {
        $.ajax({
            url: acfMailSmtp.ajaxUrl,
            type: 'POST',
            data: {
                action: 'acf_mail_smtp_preview_template',
                nonce: acfMailSmtp.nonce,
                template_id: currentTemplateId || ''
            },
            success: function(response) {
                if (response.success) {
                    const iframe = document.getElementById('jj-preview-iframe');
                    const doc = iframe.contentDocument || iframe.contentWindow.document;
                    doc.open();
                    doc.write(response.data.html);
                    doc.close();
                    $('#jj-preview-modal').show();
                }
            }
        });
    }
});
</script>
