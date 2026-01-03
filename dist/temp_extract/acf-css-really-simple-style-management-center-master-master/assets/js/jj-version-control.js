/**
 * JJ Version Control
 * 
 * [Phase 9.3] 버전 관리 및 히스토리 시스템
 * 
 * @since 9.3.0
 */

(function($) {
    'use strict';
    
    const VersionControl = {
        init: function() {
            this.createUI();
            this.loadVersions();
            this.bindEvents();
        },
        
        createUI: function() {
            // UI가 이미 있으면 스킵
            if ($('#jj-version-control').length > 0) {
                return;
            }
            
            const uiHtml = `
                <div id="jj-version-control" style="margin: 20px 0; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                    <h3 style="margin-top: 0;">
                        <span class="dashicons dashicons-backup" style="vertical-align: middle; margin-right: 5px;"></span>
                        버전 관리
                    </h3>
                    
                    <div style="margin: 20px 0;">
                        <button type="button" class="button button-primary" id="jj-create-snapshot">
                            <span class="dashicons dashicons-camera" style="vertical-align: middle; margin-right: 5px;"></span>
                            스냅샷 생성
                        </button>
                    </div>
                    
                    <div id="jj-snapshot-form" style="display: none; margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 4px;">
                        <h4>새 스냅샷</h4>
                        <p>
                            <label>
                                <strong>이름:</strong><br>
                                <input type="text" id="jj-snapshot-name" style="width: 100%; padding: 8px; margin-top: 5px;" placeholder="스냅샷 이름">
                            </label>
                        </p>
                        <p>
                            <label>
                                <strong>설명:</strong><br>
                                <textarea id="jj-snapshot-description" style="width: 100%; padding: 8px; margin-top: 5px; min-height: 60px;" placeholder="스냅샷 설명 (선택적)"></textarea>
                            </label>
                        </p>
                        <p>
                            <button type="button" class="button button-primary" id="jj-save-snapshot">생성</button>
                            <button type="button" class="button" id="jj-cancel-snapshot">취소</button>
                        </p>
                    </div>
                    
                    <div id="jj-versions-list" style="margin-top: 20px;">
                        <h4>스냅샷 목록</h4>
                        <div class="jj-loading" style="text-align: center; padding: 20px;">
                            <span class="spinner is-active"></span> 로딩 중...
                        </div>
                    </div>
                </div>
            `;
            
            // Admin Center의 Tools 탭이나 적절한 위치에 삽입
            $('.jj-admin-center-tab-content[data-tab="tools"]').append(uiHtml);
        },
        
        bindEvents: function() {
            const self = this;
            
            // 스냅샷 생성 폼 표시
            $(document).on('click', '#jj-create-snapshot', function() {
                $('#jj-snapshot-form').slideToggle();
                $('#jj-snapshot-name').focus();
            });
            
            // 스냅샷 생성 취소
            $(document).on('click', '#jj-cancel-snapshot', function() {
                $('#jj-snapshot-form').slideUp();
                $('#jj-snapshot-name, #jj-snapshot-description').val('');
            });
            
            // 스냅샷 저장
            $(document).on('click', '#jj-save-snapshot', function() {
                self.createSnapshot();
            });
            
            // 스냅샷 복원
            $(document).on('click', '.jj-restore-snapshot', function() {
                const snapshotId = $(this).data('snapshot-id');
                self.restoreSnapshot(snapshotId);
            });
            
            // 스냅샷 삭제
            $(document).on('click', '.jj-delete-snapshot', function() {
                const snapshotId = $(this).data('snapshot-id');
                if (confirm('이 스냅샷을 삭제하시겠습니까?')) {
                    self.deleteSnapshot(snapshotId);
                }
            });
        },
        
        loadVersions: function() {
            const self = this;
            
            $.ajax({
                url: jjVersionControl.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_get_version_history',
                    nonce: jjVersionControl.nonce,
                    limit: 20
                },
                success: function(response) {
                    if (response.success) {
                        self.renderVersions(response.data.versions);
                    }
                },
                error: function() {
                    $('#jj-versions-list').html('<p style="color: #d63638;">로딩 중 오류가 발생했습니다.</p>');
                }
            });
        },
        
        renderVersions: function(versions) {
            if (!versions || versions.length === 0) {
                $('#jj-versions-list').html('<p style="color: #666;">스냅샷이 없습니다.</p>');
                return;
            }
            
            let html = '<table class="widefat" style="margin-top: 10px;"><thead><tr>';
            html += '<th>이름</th><th>설명</th><th>생성일</th><th>크기</th><th>작업</th>';
            html += '</tr></thead><tbody>';
            
            versions.forEach(function(version) {
                const date = new Date(version.created_at);
                const dateStr = date.toLocaleString('ko-KR');
                const size = (version.size / 1024).toFixed(2) + ' KB';
                const autoBadge = version.auto ? '<span class="dashicons dashicons-update" style="color: #666; font-size: 16px;" title="자동 생성"></span>' : '';
                
                html += `<tr>
                    <td><strong>${autoBadge} ${version.name}</strong></td>
                    <td>${version.description || '-'}</td>
                    <td>${dateStr}</td>
                    <td>${size}</td>
                    <td>
                        <button type="button" class="button button-small jj-restore-snapshot" data-snapshot-id="${version.id}">
                            복원
                        </button>
                        ${!version.auto ? `<button type="button" class="button button-small jj-delete-snapshot" data-snapshot-id="${version.id}" style="color: #d63638;">
                            삭제
                        </button>` : ''}
                    </td>
                </tr>`;
            });
            
            html += '</tbody></table>';
            $('#jj-versions-list').html(html);
        },
        
        createSnapshot: function() {
            const self = this;
            const name = $('#jj-snapshot-name').val().trim();
            const description = $('#jj-snapshot-description').val().trim();
            
            if (!name) {
                if (JJUtils && JJUtils.showToast) {
                    JJUtils.showToast('스냅샷 이름을 입력하세요.', 'warning');
                }
                return;
            }
            
            $.ajax({
                url: jjVersionControl.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_create_snapshot',
                    nonce: jjVersionControl.nonce,
                    name: name,
                    description: description
                },
                success: function(response) {
                    if (response.success) {
                        $('#jj-snapshot-form').slideUp();
                        $('#jj-snapshot-name, #jj-snapshot-description').val('');
                        self.loadVersions();
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(jjVersionControl.strings.snapshot_created, 'success');
                        }
                    } else {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '생성 실패', 'error');
                        }
                    }
                },
                error: function() {
                    if (JJUtils && JJUtils.showToast) {
                        JJUtils.showToast('오류가 발생했습니다.', 'error');
                    }
                }
            });
        },
        
        restoreSnapshot: function(snapshotId) {
            const self = this;
            
            if (!confirm(jjVersionControl.strings.confirm_restore)) {
                return;
            }
            
            $.ajax({
                url: jjVersionControl.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_restore_snapshot',
                    nonce: jjVersionControl.nonce,
                    snapshot_id: snapshotId
                },
                success: function(response) {
                    if (response.success) {
                        self.loadVersions();
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(jjVersionControl.strings.snapshot_restored, 'success');
                        }
                        // 페이지 새로고침 (설정이 변경되었으므로)
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        if (JJUtils && JJUtils.showToast) {
                            JJUtils.showToast(response.data.message || '복원 실패', 'error');
                        }
                    }
                },
                error: function() {
                    if (JJUtils && JJUtils.showToast) {
                        JJUtils.showToast('오류가 발생했습니다.', 'error');
                    }
                }
            });
        },
        
        deleteSnapshot: function(snapshotId) {
            // 삭제 기능은 향후 구현
            if (JJUtils && JJUtils.showToast) {
                JJUtils.showToast('삭제 기능은 향후 구현 예정입니다.', 'info');
            }
        }
    };
    
    // 초기화
    $(document).ready(function() {
        if (typeof jjVersionControl !== 'undefined') {
            VersionControl.init();
        }
    });
    
})(jQuery);
