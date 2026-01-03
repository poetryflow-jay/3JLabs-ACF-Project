<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Team Collaboration Section
 * 
 * @package ACF_CSS_Manager
 * @version 22.1.5
 */

if ( ! class_exists( 'JJ_Team_Sync' ) ) {
    echo '<div class="notice notice-error"><p>' . esc_html__( 'Team Sync가 로드되지 않았습니다.', 'acf-css-really-simple-style-management-center' ) . '</p></div>';
    return;
}

$team_sync = JJ_Team_Sync::instance();
?>

<div class="jj-team-sync-section">
    <h2>👥 <?php esc_html_e( 'Team Collaboration', 'acf-css-really-simple-style-management-center' ); ?></h2>
    <p class="description">
        <?php esc_html_e( '팀원들과 스타일 설정을 공유하고 버전을 관리하세요.', 'acf-css-really-simple-style-management-center' ); ?>
    </p>

    <style>
        .jj-team-sync-section {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .jj-sync-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin: 20px 0;
        }
        .jj-sync-panel {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
        }
        .jj-sync-panel h3 {
            margin: 0 0 16px 0;
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }
        .jj-form-field {
            margin-bottom: 16px;
        }
        .jj-form-field label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 14px;
            color: #374151;
        }
        .jj-form-field input[type="text"],
        .jj-form-field textarea {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
        }
        .jj-form-field textarea {
            min-height: 80px;
            font-family: monospace;
        }
        .jj-export-history {
            margin-top: 20px;
            max-height: 300px;
            overflow-y: auto;
        }
        .jj-history-item {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 8px;
            font-size: 13px;
        }
        .jj-history-item strong {
            color: #111827;
        }
        .jj-history-item .version-tag {
            display: inline-block;
            padding: 2px 8px;
            background: #3b82f6;
            color: #fff;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            margin-left: 8px;
        }
        .jj-conflict-warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            margin: 16px 0;
            border-radius: 4px;
        }
    </style>

    <div class="jj-sync-grid">
        <!-- Export Panel -->
        <div class="jj-sync-panel">
            <h3>📤 <?php esc_html_e( 'Export Settings', 'acf-css-really-simple-style-management-center' ); ?></h3>
            
            <div class="jj-form-field">
                <label><?php esc_html_e( 'Version Tag (예: v2.1)', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" id="jj-export-version" placeholder="v1.0">
            </div>
            
            <div class="jj-form-field">
                <label><?php esc_html_e( 'Description', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="text" id="jj-export-description" placeholder="<?php esc_attr_e( '변경 사항 설명', 'acf-css-really-simple-style-management-center' ); ?>">
            </div>
            
            <div class="jj-form-field">
                <label><?php esc_html_e( 'Changelog', 'acf-css-really-simple-style-management-center' ); ?></label>
                <textarea id="jj-export-changelog" placeholder="<?php esc_attr_e( '- 메인 컬러 업데이트&#10;- 버튼 스타일 개선', 'acf-css-really-simple-style-management-center' ); ?>"></textarea>
            </div>
            
            <button type="button" class="button button-primary" id="jj-export-btn">
                <?php esc_html_e( 'Export & Download', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
        </div>

        <!-- Import Panel -->
        <div class="jj-sync-panel">
            <h3>📥 <?php esc_html_e( 'Import Settings', 'acf-css-really-simple-style-management-center' ); ?></h3>
            
            <div class="jj-form-field">
                <label><?php esc_html_e( 'JSON 파일 선택', 'acf-css-really-simple-style-management-center' ); ?></label>
                <input type="file" id="jj-import-file" accept=".json">
            </div>
            
            <div class="jj-form-field">
                <label>
                    <input type="checkbox" id="jj-import-overwrite">
                    <?php esc_html_e( '기존 설정 덮어쓰기 (병합 안 함)', 'acf-css-really-simple-style-management-center' ); ?>
                </label>
            </div>
            
            <div id="jj-import-conflicts" style="display:none;"></div>
            
            <button type="button" class="button button-primary" id="jj-import-btn" disabled>
                <?php esc_html_e( 'Import Settings', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
        </div>
    </div>

    <div class="jj-export-history">
        <h3><?php esc_html_e( '📜 Export History', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <div id="jj-history-list"></div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var importData = null;

        // Export
        $('#jj-export-btn').on('click', function() {
            var metadata = {
                version_tag: $('#jj-export-version').val(),
                description: $('#jj-export-description').val(),
                changelog: $('#jj-export-changelog').val()
            };

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'jj_export_settings',
                    security: '<?php echo wp_create_nonce( 'jj_team_sync_nonce' ); ?>',
                    version_tag: metadata.version_tag,
                    description: metadata.description,
                    changelog: metadata.changelog
                },
                success: function(response) {
                    if (response.success) {
                        var blob = new Blob([JSON.stringify(response.data.export_data, null, 2)], { type: 'application/json' });
                        var url = URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = response.data.filename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                        
                        alert('<?php esc_html_e( 'Export 완료!', 'acf-css-really-simple-style-management-center' ); ?>');
                        loadHistory();
                    }
                }
            });
        });

        // Import file selection
        $('#jj-import-file').on('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function(event) {
                try {
                    importData = JSON.parse(event.target.result);
                    $('#jj-import-btn').prop('disabled', false);
                    
                    var info = '<div class="notice notice-info inline"><p>';
                    info += '<strong><?php esc_html_e( '파일 정보:', 'acf-css-really-simple-style-management-center' ); ?></strong><br>';
                    info += '<?php esc_html_e( 'Export 날짜:', 'acf-css-really-simple-style-management-center' ); ?> ' + importData.exported_at + '<br>';
                    info += '<?php esc_html_e( 'Export 작성자:', 'acf-css-really-simple-style-management-center' ); ?> ' + importData.exported_by.display_name;
                    if (importData.metadata.version_tag) {
                        info += ' <span class="version-tag">' + importData.metadata.version_tag + '</span>';
                    }
                    info += '</p></div>';
                    
                    $('#jj-import-conflicts').html(info).show();
                } catch (err) {
                    alert('<?php esc_html_e( 'JSON 파싱 오류', 'acf-css-really-simple-style-management-center' ); ?>');
                    importData = null;
                    $('#jj-import-btn').prop('disabled', true);
                }
            };
            reader.readAsText(file);
        });

        // Import
        $('#jj-import-btn').on('click', function() {
            if (!importData) {
                alert('<?php esc_html_e( '파일을 먼저 선택하세요.', 'acf-css-really-simple-style-management-center' ); ?>');
                return;
            }

            var overwrite = $('#jj-import-overwrite').is(':checked');
            
            if (!confirm('<?php esc_html_e( '정말 가져오시겠습니까?', 'acf-css-really-simple-style-management-center' ); ?>')) {
                return;
            }

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'jj_import_settings',
                    security: '<?php echo wp_create_nonce( 'jj_team_sync_nonce' ); ?>',
                    import_data: JSON.stringify(importData),
                    overwrite: overwrite
                },
                success: function(response) {
                    if (response.success) {
                        var msg = response.data.message;
                        if (response.data.conflicts.length > 0) {
                            msg += '\n\n<?php esc_html_e( '충돌 발견:', 'acf-css-really-simple-style-management-center' ); ?> ' + response.data.conflicts.length;
                        }
                        alert(msg);
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        });

        // Load history
        function loadHistory() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'jj_get_export_history',
                    security: '<?php echo wp_create_nonce( 'jj_team_sync_nonce' ); ?>'
                },
                success: function(response) {
                    if (response.success && response.data.history.length > 0) {
                        var html = '';
                        response.data.history.forEach(function(item) {
                            html += '<div class="jj-history-item">';
                            html += '<strong>' + item.exported_at + '</strong> <?php esc_html_e( 'by', 'acf-css-really-simple-style-management-center' ); ?> ' + item.exported_by;
                            if (item.version_tag) {
                                html += '<span class="version-tag">' + item.version_tag + '</span>';
                            }
                            if (item.description) {
                                html += '<br><small>' + item.description + '</small>';
                            }
                            html += '</div>';
                        });
                        $('#jj-history-list').html(html);
                    } else {
                        $('#jj-history-list').html('<p><?php esc_html_e( '히스토리가 없습니다.', 'acf-css-really-simple-style-management-center' ); ?></p>');
                    }
                }
            });
        }

        loadHistory();
    });
    </script>
</div>
