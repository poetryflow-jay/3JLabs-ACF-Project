/**
 * ACF Code Snippets Box - Export/Import System
 *
 * 내보내기/가져오기 관리자 인터페이스
 *
 * @package ACF_Code_Snippets_Box
 */

(function($) {
    'use strict';

    /**
     * Export/Import 클래스
     */
    class ACFCSBExportImport {
        constructor() {
            this.$exportForm = $('#acf-csb-export-form');
            this.$importForm = $('#acf-csb-import-form');
            this.$importProgress = $('#import-progress');
            this.$importResults = $('#import-results');
            
            this.init();
        }

        init() {
            this.bindEvents();
        }

        /**
         * 이벤트 바인딩
         */
        bindEvents() {
            const self = this;

            // 전체 선택 체크박스
            $('#select-all-snippets').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('input[name="snippet_ids[]"]').prop('checked', isChecked);
            });

            // 개별 체크박스 변경 시 전체 선택 상태 업데이트
            $('input[name="snippet_ids[]"]').on('change', function() {
                const total = $('input[name="snippet_ids[]"]').length;
                const checked = $('input[name="snippet_ids[]"]:checked').length;
                $('#select-all-snippets').prop('checked', total === checked);
            });

            // 내보내기 폼 제출
            this.$exportForm.on('submit', function(e) {
                e.preventDefault();
                self.handleExport();
            });

            // 가져오기 폼 제출
            this.$importForm.on('submit', function(e) {
                e.preventDefault();
                self.handleImport();
            });

            // 파일 선택 드래그 앤 드롭
            this.setupDragDrop();

            // 파일 선택 시 이름 표시
            $('#import-file').on('change', function() {
                const fileName = $(this).val().split('\\').pop();
                $('#selected-file-name').text(fileName || '');
            });

            // 클라우드 동기화 버튼
            $('#cloud-sync-now').on('click', function() {
                self.cloudSync();
            });

            $('#cloud-push-all').on('click', function() {
                self.cloudPushAll();
            });

            $('#cloud-pull-all').on('click', function() {
                self.cloudPullAll();
            });
        }

        /**
         * 드래그 앤 드롭 설정
         */
        setupDragDrop() {
            const $dropZone = $('.acf-csb-file-upload');

            $dropZone.on('dragover dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('drag-over');
            });

            $dropZone.on('dragleave dragend drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('drag-over');
            });

            $dropZone.on('drop', function(e) {
                const files = e.originalEvent.dataTransfer.files;
                if (files.length) {
                    $('#import-file')[0].files = files;
                    $('#selected-file-name').text(files[0].name);
                }
            });
        }

        /**
         * 내보내기 처리
         */
        handleExport() {
            const self = this;
            const $btn = this.$exportForm.find('button[type="submit"]');
            const originalHtml = $btn.html();

            // 선택된 스니펫 확인
            const selectedIds = $('input[name="snippet_ids[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedIds.length === 0) {
                alert('내보낼 스니펫을 선택하세요.');
                return;
            }

            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> 내보내는 중...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: this.$exportForm.serialize() + '&action=acf_csb_export_snippets',
                success: function(response) {
                    if (response.success) {
                        // 파일 다운로드
                        self.downloadFile(response.data.file_url);
                        self.showNotice('success', response.data.count + '개의 스니펫을 내보냈습니다.');
                    } else {
                        self.showNotice('error', response.data || '내보내기에 실패했습니다.');
                    }
                },
                error: function() {
                    self.showNotice('error', '서버 오류가 발생했습니다.');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        }

        /**
         * 파일 다운로드
         */
        downloadFile(url) {
            const link = document.createElement('a');
            link.href = url;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        /**
         * 가져오기 처리
         */
        handleImport() {
            const self = this;
            const $btn = this.$importForm.find('button[type="submit"]');
            const originalHtml = $btn.html();

            // 파일 확인
            const fileInput = $('#import-file')[0];
            if (!fileInput.files.length) {
                alert('파일을 선택하세요.');
                return;
            }

            const formData = new FormData(this.$importForm[0]);
            formData.append('action', 'acf_csb_import_snippets');

            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> 가져오는 중...');
            this.$importProgress.show();
            this.$importResults.hide();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percent = (e.loaded / e.total) * 100;
                            self.$importProgress.find('.acf-csb-progress-fill').css('width', percent + '%');
                            self.$importProgress.find('.acf-csb-progress-text').text('업로드 중... ' + Math.round(percent) + '%');
                        }
                    });
                    return xhr;
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        const resultsHtml = `
                            <h4>가져오기 완료!</h4>
                            <ul>
                                <li>✅ 가져옴: ${data.imported}개</li>
                                <li>🔄 업데이트: ${data.updated}개</li>
                                <li>⏭️ 건너뜀: ${data.skipped}개</li>
                                ${data.errors.length ? `<li>❌ 오류: ${data.errors.length}개</li>` : ''}
                            </ul>
                            ${data.errors.length ? `
                                <details>
                                    <summary>오류 상세</summary>
                                    <ul>${data.errors.map(e => `<li>${e}</li>`).join('')}</ul>
                                </details>
                            ` : ''}
                        `;
                        
                        self.$importResults.addClass('success').removeClass('error').html(resultsHtml).show();
                        
                        // 폼 초기화
                        fileInput.value = '';
                        $('#selected-file-name').text('');
                    } else {
                        self.$importResults.addClass('error').removeClass('success')
                            .html('<p>❌ ' + (response.data || '가져오기에 실패했습니다.') + '</p>').show();
                    }
                },
                error: function() {
                    self.$importResults.addClass('error').removeClass('success')
                        .html('<p>❌ 서버 오류가 발생했습니다.</p>').show();
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalHtml);
                    self.$importProgress.find('.acf-csb-progress-fill').css('width', '100%');
                    self.$importProgress.find('.acf-csb-progress-text').text('완료');
                }
            });
        }

        /**
         * 클라우드 동기화
         */
        cloudSync() {
            const self = this;
            const $btn = $('#cloud-sync-now');
            const originalHtml = $btn.html();

            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> 동기화 중...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'acf_csb_cloud_sync',
                    nonce: acfCsbAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        self.showNotice('success', 
                            `동기화 완료! 업로드: ${data.pushed}, 다운로드: ${data.pulled}, 업데이트: ${data.updated}`
                        );
                    } else {
                        self.showNotice('error', response.data || '동기화에 실패했습니다.');
                    }
                },
                error: function() {
                    self.showNotice('error', '서버 오류가 발생했습니다.');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        }

        /**
         * 클라우드 전체 업로드
         */
        cloudPushAll() {
            if (!confirm('모든 로컬 스니펫을 클라우드에 업로드하시겠습니까?')) {
                return;
            }

            const $btn = $('#cloud-push-all');
            $btn.prop('disabled', true).text('업로드 중...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'acf_csb_cloud_push',
                    nonce: acfCsbAdmin.nonce,
                    all: true
                },
                success: function(response) {
                    if (response.success) {
                        alert('업로드 완료: ' + response.data.count + '개 스니펫');
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-upload"></span> 모두 업로드');
                }
            });
        }

        /**
         * 클라우드 전체 다운로드
         */
        cloudPullAll() {
            if (!confirm('클라우드의 모든 스니펫을 다운로드하시겠습니까? 동일한 이름의 로컬 스니펫은 덮어씁니다.')) {
                return;
            }

            const $btn = $('#cloud-pull-all');
            $btn.prop('disabled', true).text('다운로드 중...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'acf_csb_cloud_pull',
                    nonce: acfCsbAdmin.nonce,
                    all: true
                },
                success: function(response) {
                    if (response.success) {
                        alert('다운로드 완료: ' + response.data.count + '개 스니펫');
                        location.reload();
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-download"></span> 모두 다운로드');
                }
            });
        }

        /**
         * 알림 표시
         */
        showNotice(type, message) {
            const $notice = $(`
                <div class="notice notice-${type} is-dismissible">
                    <p>${message}</p>
                </div>
            `);

            $('.wrap h1').first().after($notice);

            // 닫기 버튼 추가
            $notice.append('<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss</span></button>');
            $notice.find('.notice-dismiss').on('click', function() {
                $notice.fadeOut(300, function() { $(this).remove(); });
            });

            // 5초 후 자동 제거
            setTimeout(() => {
                $notice.fadeOut(300, function() { $(this).remove(); });
            }, 5000);
        }
    }

    /**
     * DOM Ready
     */
    $(document).ready(function() {
        if ($('#acf-csb-export-form').length || $('#acf-csb-import-form').length) {
            window.acfCsbExportImport = new ACFCSBExportImport();
        }
    });

    // 스핀 애니메이션 스타일
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .dashicons.spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                100% { transform: rotate(360deg); }
            }
            .acf-csb-file-upload.drag-over {
                border-color: #0073aa;
                background: #f0f6fc;
            }
        `)
        .appendTo('head');

})(jQuery);
