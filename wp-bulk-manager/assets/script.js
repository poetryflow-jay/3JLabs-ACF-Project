jQuery(document).ready(function($) {
    var filesQueue = [];
    var isProcessing = false;
    var installedPlugins = []; // 설치된 플러그인 정보 저장

    var bulkCache = {
        plugins: null,
        themes: null
    };
    var bulkUi = {
        editorInitialized: false,
        currentSubtab: 'plugins'
    };

    function escapeHtml(str) {
        if (str === null || typeof str === 'undefined') return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showNotice(type, message, extraHtml) {
        var $wrap = $('#jj-bulk-notices');
        if ($wrap.length === 0) return;

        var html = '<div class="notice notice-' + escapeHtml(type) + ' is-dismissible">' +
            '<p>' + message + '</p>' +
            (extraHtml ? extraHtml : '') +
            '</div>';
        $wrap.html(html);
    }

    function initTabs() {
        var storageKey = 'jj_bulk_manager_active_tab';
        var savedTab = null;
        try {
            savedTab = window.localStorage ? window.localStorage.getItem(storageKey) : null;
        } catch (e) {}

        function activateTab(tab) {
            $('.jj-bulk-tab').removeClass('is-active').attr('aria-selected', 'false');
            $('.jj-bulk-tab[data-tab="' + tab + '"]').addClass('is-active').attr('aria-selected', 'true');

            $('.jj-bulk-tab-panel').hide().removeClass('is-active');
            $('.jj-bulk-tab-panel[data-tab-panel="' + tab + '"]').show().addClass('is-active');

            try {
                if (window.localStorage) window.localStorage.setItem(storageKey, tab);
            } catch (e) {}

            if (tab === 'editor') {
                initBulkEditorOnce();
            } else if (tab === 'multisite-installer' || tab === 'multisite-editor') {
                initMultisiteUI();
                if (tab === 'multisite-editor') initMultisiteEditor();
            } else if (tab === 'remote-installer' || tab === 'remote-editor') {
                initRemoteUI();
                initRemoteConnection();
                if (tab === 'remote-editor') initRemoteEditor();
            }
        }

        $('.jj-bulk-tab').on('click', function() {
            activateTab($(this).data('tab'));
        });

        if (savedTab && $('.jj-bulk-tab[data-tab="' + savedTab + '"]').length) {
            activateTab(savedTab);
        }
    }

    // ==============================
    // [v5.0.0] Multisite & Remote Management
    // ==============================
    function initMultisiteUI() {
        // 멀티사이트 전체 선택/해제
        $('#jj-multisite-select-all').on('click', function() {
            $('input[name="multisite_target[]"]').prop('checked', true);
        });
        $('#jj-multisite-select-none').on('click', function() {
            $('input[name="multisite_target[]"]').prop('checked', false);
        });
    }

    function initRemoteUI() {
        // 원격 사이트 전체 선택/해제
        $('#jj-remote-select-all').on('click', function() {
            $('input[name="remote_target[]"]').prop('checked', true);
        });
        $('#jj-remote-select-none').on('click', function() {
            $('input[name="remote_target[]"]').prop('checked', false);
        });

        // 원격 사이트 연결 해제 (삭제)
        $(document).on('click', '.jj-remote-disconnect', function() {
            var url = $(this).data('url');
            if (!confirm(url + ' 연결을 해제하시겠습니까?')) return;

            var $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_bulk_remote_disconnect',
                    nonce: jjBulk.nonce,
                    remote_url: url
                },
                success: function(resp) {
                    if (resp.success) {
                        showNotice('success', '연결 해제 완료: ' + url);
                        location.reload(); // 리스트 갱신을 위해 새로고침 (간단한 구현)
                    } else {
                        showNotice('error', '해제 실패: ' + (resp.data || '알 수 없는 오류'));
                        $btn.prop('disabled', false);
                    }
                }
            });
        });
    }

    function initMultisiteEditor() {
        if ($('#jj-ms-site-select').length) return;

        var html = '<div style="margin-bottom: 20px; padding: 15px; background: #f4f4f4; border: 1px solid #ddd; border-radius: 6px;">' +
            '<h4 style="margin-top:0;">🔍 관리할 사이트 선택</h4>' +
            '<div style="display:flex; gap:10px;">' +
            '<select id="jj-ms-site-select" style="flex:1; max-width: 400px;">' +
            '<option value="">사이트를 선택하세요...</option>';
        
        $('input[name="multisite_target[]"]').each(function() {
            var id = $(this).val();
            var name = $(this).closest('label').text().trim();
            html += '<option value="' + id + '">' + name + '</option>';
        });
        
        html += '</select>' +
            '<button type="button" id="jj-ms-load-btn" class="button">조회하기</button>' +
            '</div></div>' +
            '<div id="jj-ms-editor-list"></div>';
        
        $('#jj-multisite-editor-content').html(html);

        $('#jj-ms-load-btn').on('click', function() {
            var siteId = $('#jj-ms-site-select').val();
            if (!siteId) {
                alert('사이트를 선택해주세요.');
                return;
            }
            loadCrossSiteItems('multisite', siteId);
        });
    }

    function initRemoteEditor() {
        if ($('#jj-remote-site-select').length) return;

        var html = '<div style="margin-bottom: 20px; padding: 15px; background: #f4f4f4; border: 1px solid #ddd; border-radius: 6px;">' +
            '<h4 style="margin-top:0;">🔍 관리할 원격 사이트 선택</h4>' +
            '<div style="display:flex; gap:10px;">' +
            '<select id="jj-remote-site-select" style="flex:1; max-width: 400px;">' +
            '<option value="">원격 사이트를 선택하세요...</option>';
        
        $('input[name="remote_target[]"]').each(function() {
            var url = $(this).val();
            html += '<option value="' + url + '">' + url + '</option>';
        });
        
        html += '</select>' +
            '<button type="button" id="jj-remote-load-btn" class="button">조회하기</button>' +
            '</div></div>' +
            '<div id="jj-remote-editor-list"></div>';
        
        $('#jj-remote-editor-content').html(html);

        $('#jj-remote-load-btn').on('click', function() {
            var url = $('#jj-remote-site-select').val();
            if (!url) {
                alert('원격 사이트를 선택해주세요.');
                return;
            }
            loadCrossSiteItems('remote', url);
        });
    }

    function loadCrossSiteItems(type, target) {
        var $container = (type === 'multisite') ? $('#jj-ms-editor-list') : $('#jj-remote-editor-list');
        var action = (type === 'multisite') ? 'jj_bulk_manage_get_items' : 'jj_bulk_remote_get_items';
        
        $container.html('<p>항목을 불러오는 중...</p>');

        var ajaxData = {
            action: action,
            nonce: jjBulk.nonce,
            item_type: 'plugin'
        };

        if (type === 'multisite') {
            ajaxData.site_id = target;
        } else {
            ajaxData.remote_url = target;
        }

        $.ajax({
            url: jjBulk.ajax_url,
            type: 'POST',
            data: ajaxData,
            success: function(resp) {
                if (resp.success) {
                    var items = (resp.data && resp.data.items) ? resp.data.items : [];
                    renderCrossSiteTable($container, items, type, target);
                } else {
                    $container.html('<p style="color:red;">오류: ' + (resp.data || '알 수 없는 오류') + '</p>');
                }
            },
            error: function() {
                $container.html('<p style="color:red;">통신 오류가 발생했습니다.</p>');
            }
        });
    }

    function renderCrossSiteTable($container, items, type, target) {
        if (!items || items.length === 0) {
            $container.html('<p>설치된 항목이 없습니다.</p>');
            return;
        }

        var html = '<table class="wp-list-table widefat fixed striped">' +
            '<thead><tr>' +
            '<th class="check-column"><input type="checkbox" class="jj-cs-select-all"></th>' +
            '<th>이름</th><th>버전</th><th>상태</th><th>작업</th>' +
            '</tr></thead><tbody>';

        items.forEach(function(item) {
            var statusLabel = item.active ? '활성' : '비활성';
            var statusClass = item.active ? 'jj-pill-good' : 'jj-pill-muted';
            
            html += '<tr>' +
                '<td><input type="checkbox" class="jj-cs-item-check" data-id="' + escapeHtml(item.id) + '"></td>' +
                '<td><strong>' + escapeHtml(item.name) + '</strong></td>' +
                '<td>' + escapeHtml(item.version) + '</td>' +
                '<td><span class="jj-pill ' + statusClass + '">' + statusLabel + '</span></td>' +
                '<td>';
            
            if (item.active) {
                html += '<button type="button" class="button button-small jj-cs-action" data-action="deactivate" data-id="' + escapeHtml(item.id) + '">비활성화</button> ';
            } else {
                html += '<button type="button" class="button button-small jj-cs-action" data-action="activate" data-id="' + escapeHtml(item.id) + '">활성화</button> ';
            }
            html += '<button type="button" class="button button-small jj-cs-action delete" data-action="delete" data-id="' + escapeHtml(item.id) + '" style="color:red;">삭제</button>';
            html += '</td></tr>';
        });

        html += '</tbody></table>' +
            '<div style="margin-top:10px;">' +
            '<button type="button" class="button jj-cs-bulk-action" data-action="activate">선택 활성화</button> ' +
            '<button type="button" class="button jj-cs-bulk-action" data-action="deactivate">선택 비활성화</button> ' +
            '<button type="button" class="button jj-cs-bulk-action delete" data-action="delete" style="color:red;">선택 삭제</button>' +
            '</div>';

        $container.html(html);

        // 이벤트 바인딩
        $container.find('.jj-cs-select-all').on('change', function() {
            $container.find('.jj-cs-item-check').prop('checked', $(this).is(':checked'));
        });

        $container.find('.jj-cs-action').on('click', function() {
            var action = $(this).data('action');
            var id = $(this).data('id');
            if (action === 'delete' && !confirm('정말 삭제하시겠습니까?')) return;
            runCrossSiteAction(type, target, action, [id], $container);
        });

        $container.find('.jj-cs-bulk-action').on('click', function() {
            var action = $(this).data('action');
            var ids = [];
            $container.find('.jj-cs-item-check:checked').each(function() {
                ids.push($(this).data('id'));
            });

            if (ids.length === 0) {
                alert('항목을 선택해주세요.');
                return;
            }

            if (action === 'delete' && !confirm('정말 삭제하시겠습니까?')) return;
            runCrossSiteAction(type, target, action, ids, $container);
        });
    }

    function runCrossSiteAction(type, target, action, ids, $container) {
        var ajaxAction = (type === 'multisite') ? 'jj_bulk_multisite_bulk_action' : 'jj_bulk_remote_bulk_action';
        var data = {
            action: ajaxAction,
            nonce: jjBulk.nonce,
            operation: action,
            items: ids,
            item_type: 'plugin'
        };

        if (type === 'multisite') {
            data.site_ids = [target];
        } else {
            data.remote_urls = [target];
        }

        $container.css('opacity', '0.5');

        $.ajax({
            url: jjBulk.ajax_url,
            type: 'POST',
            data: data,
            success: function(resp) {
                if (resp.success) {
                    showNotice('success', '작업이 완료되었습니다.');
                    loadCrossSiteItems(type, target); // 새로고침
                } else {
                    showNotice('error', '작업 실패: ' + (resp.data || '알 수 없는 오류'));
                }
            },
            complete: function() {
                $container.css('opacity', '1');
            }
        });
    }

    function loadRemoteItems(url) {
        var $list = $('#jj-remote-editor-list');
        $list.html('<p>원격 사이트 정보를 불러오는 중...</p>');

        $.ajax({
            url: jjBulk.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_bulk_remote_bulk_action',
                nonce: jjBulk.nonce,
                remote_urls: [url],
                operation: 'get_items',
                item_type: 'plugin'
            },
            success: function(resp) {
                // 원격 응답 구조에 맞춰 렌더링
                if (resp.success && resp.data.results[url].success) {
                    renderMsItems(resp.data.results[url], url, true);
                } else {
                    $list.html('<p class="error">로드 실패</p>');
                }
            }
        });
    }

    function renderMsItems(data, targetId, isRemote) {
        // 간단한 테이블 형식 렌더링
        var html = '<table class="wp-list-table widefat fixed striped">' +
            '<thead><tr><th>이름</th><th>버전</th><th>상태</th></tr></thead>' +
            '<tbody>';
        
        data.items.forEach(function(item) {
            html += '<tr>' +
                '<td>' + escapeHtml(item.name) + '</td>' +
                '<td>' + escapeHtml(item.version) + '</td>' +
                '<td>' + (item.active ? '✅ 활성' : '💤 비활성') + '</td>' +
                '</tr>';
        });
        
        html += '</tbody></table>';
        if (isRemote) $('#jj-remote-editor-list').html(html);
        else $('#jj-ms-editor-list').html(html);
    }

    function initRemoteConnection() {
        $('#jj-remote-connect').on('click', function() {
            var url = $('#jj-remote-url').val();
            var key = $('#jj-remote-key').val();
            var oneWay = $('#jj-remote-one-way').is(':checked');

            if (!url || !key) {
                alert('URL과 시크릿 키를 모두 입력해주세요.');
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).text('연결 중...');

            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_bulk_remote_connect',
                    nonce: jjBulk.nonce,
                    remote_url: url,
                    remote_key: key,
                    one_way: oneWay
                },
                success: function(resp) {
                    if (resp.success) {
                        showNotice('success', '원격 사이트 연결 성공: ' + url);
                        location.reload(); // 리스트 갱신을 위해 새로고침
                    } else {
                        showNotice('error', '연결 실패: ' + (resp.data || '알 수 없는 오류'));
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).text('연결하기');
                }
            });
        });
    }

    // ==============================
    // Installer (기존 기능 유지)
    // ==============================
    // ==============================
    // 툴팁/팝업 시스템
    // ==============================
    function initTooltipSystem() {
        // 자세히 보기 링크 클릭 시 팝업 표시
        $(document).on('click', '.jj-show-tooltip', function(e) {
            e.preventDefault();
            var tooltipId = $(this).data('tooltip');
            showTooltipPopup(tooltipId);
        });
        
        // 팝업 닫기
        $(document).on('click', '.jj-popup-close, .jj-popup-overlay', function() {
            closeTooltipPopup();
        });
        
        // ESC 키로 닫기
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTooltipPopup();
            }
        });
    }
    
    function showTooltipPopup(tooltipId) {
        var content = '';
        
        if (tooltipId === 'selection-help') {
            content = '<h3>🖱️ 선택 방법 안내</h3>' +
                '<div class="jj-popup-section">' +
                '<h4>Ctrl (⌘) + 클릭</h4>' +
                '<p>여러 개의 항목을 개별적으로 선택할 수 있습니다.<br>이미 선택된 항목을 Ctrl+클릭하면 선택이 해제됩니다.</p>' +
                '</div>' +
                '<div class="jj-popup-section">' +
                '<h4>Shift + 클릭</h4>' +
                '<p>마지막으로 클릭한 항목부터 현재 클릭한 항목까지의 범위를 한 번에 선택합니다.</p>' +
                '</div>' +
                '<div class="jj-popup-section">' +
                '<h4>전체 선택 / 선택 해제</h4>' +
                '<p>버튼을 클릭하면 모든 항목을 한 번에 선택하거나 해제할 수 있습니다.</p>' +
                '</div>' +
                '<div class="jj-popup-footer">' +
                '<label><input type="checkbox" class="jj-dont-show-again" data-key="selection-help-3days"> 3일간 보지 않기</label>' +
                '<label><input type="checkbox" class="jj-dont-show-again" data-key="selection-help-forever"> 다시 보지 않기</label>' +
                '</div>';
        }
        
        // 다시 보지 않기 체크 확인
        var dontShowKey = 'jj_tooltip_' + tooltipId;
        try {
            var dontShow = localStorage.getItem(dontShowKey);
            if (dontShow) {
                var dontShowData = JSON.parse(dontShow);
                if (dontShowData.forever) return;
                if (dontShowData.until && new Date(dontShowData.until) > new Date()) return;
            }
        } catch (e) {}
        
        // 팝업 생성
        var popup = '<div class="jj-popup-overlay"></div>' +
            '<div class="jj-popup-container">' +
            '<button type="button" class="jj-popup-close" aria-label="닫기">&times;</button>' +
            '<div class="jj-popup-content">' + content + '</div>' +
            '</div>';
        
        $('body').append(popup);
        
        // 다시 보지 않기 체크박스 이벤트
        $('.jj-dont-show-again').on('change', function() {
            var key = $(this).data('key');
            var parts = key.split('-');
            var baseKey = parts.slice(0, -1).join('-');
            var duration = parts[parts.length - 1];
            
            var storageKey = 'jj_tooltip_' + baseKey.replace('-3days', '').replace('-forever', '');
            storageKey = 'jj_tooltip_selection-help';
            
            try {
                if (duration === '3days') {
                    var until = new Date();
                    until.setDate(until.getDate() + 3);
                    localStorage.setItem(storageKey, JSON.stringify({ until: until.toISOString() }));
                } else if (duration === 'forever') {
                    localStorage.setItem(storageKey, JSON.stringify({ forever: true }));
                }
            } catch (e) {}
            
            closeTooltipPopup();
        });
    }
    
    function closeTooltipPopup() {
        $('.jj-popup-overlay, .jj-popup-container').remove();
    }
    
    // 선택 정보 업데이트
    function updateSelectionInfo() {
        // 완료 목록의 선택된 항목 수
        var completedCheckedCount = $('.jj-file-item-completed .jj-file-checkbox:checked').length;
        
        // 설치 전 목록의 선택된 항목 수
        var pendingCheckedCount = $('.jj-file-item-pending .jj-file-checkbox:checked').length;
        
        // 전체 선택된 항목 수
        var totalCheckedCount = completedCheckedCount + pendingCheckedCount;
        
        // 선택 정보 표시 업데이트
        $('#jj-selection-info').text(totalCheckedCount + '개 선택됨');
        
        // 완료 목록에 선택된 항목이 있으면 활성화 버튼 표시
        if (completedCheckedCount > 0) {
            $('#jj-activate-selected-plugins').show().text('선택한 플러그인 활성화 (' + completedCheckedCount + '개)');
        } else {
            $('#jj-activate-selected-plugins').hide();
        }
        
        // 설치 전 목록에 선택된 항목이 있으면 설치 시작 버튼 업데이트
        if (pendingCheckedCount > 0) {
            $('#jj-start-install').prop('disabled', false).text('설치 시작 (' + pendingCheckedCount + '개)');
        } else if (filesQueue.length > 0) {
            // 선택된 항목이 없어도 대기 목록이 있으면 전체 개수 표시
            $('#jj-start-install').prop('disabled', false).text('설치 시작 (' + filesQueue.length + '개)');
        } else {
            $('#jj-start-install').prop('disabled', true).text('설치 시작하기');
        }
    }
    
    // 완료 알림 표시 [v22.3.1] 개선된 성공 메시지 - 플러그인 목록 링크 포함
    function showCompletionNotice() {
        var pluginsUrl = (jjBulk.admin_urls && jjBulk.admin_urls.plugins) ? jjBulk.admin_urls.plugins : 'plugins.php';
        var message = '모든 설치가 완료되었습니다! 🎉 ';
        var extraHtml = '<p style="margin-top: 10px;">' +
            '<a href="' + pluginsUrl + '" class="button button-primary" style="margin-right: 10px;">📦 플러그인 목록에서 확인</a>' +
            '<span class="description">설치된 플러그인을 관리하려면 플러그인 목록 페이지를 방문하세요.</span>' +
            '</p>';
        showNotice('success', message, extraHtml);
    }

    function initInstaller() {
        var dropzone = $('#jj-dropzone');
        var fileInput = $('#jj-file-input');

        if (dropzone.length === 0 || fileInput.length === 0) return;
        
        // ==============================
        // 전체 선택 / 선택 해제 버튼 이벤트
        // ==============================
        $('#jj-select-all').on('click', function() {
            $('.jj-file-checkbox:not(:disabled)').prop('checked', true);
            updateSelectionInfo();
        });
        
        $('#jj-select-none').on('click', function() {
            $('.jj-file-checkbox').prop('checked', false);
            updateSelectionInfo();
        });
        
        // 체크박스 변경 시 선택 정보 업데이트
        $(document).on('change', '.jj-file-checkbox', function() {
            updateSelectionInfo();
        });
        
        // [v22.3.1] 개별 플러그인 활성화 버튼 클릭 핸들러
        $(document).on('click', '.jj-activate-single', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var slug = $btn.data('slug');
            var $item = $btn.closest('.jj-file-item');
            var pluginsUrl = (jjBulk.admin_urls && jjBulk.admin_urls.plugins) ? jjBulk.admin_urls.plugins : 'plugins.php';
            
            $btn.prop('disabled', true).text('활성화 중...');
            
            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_bulk_activate_plugin',
                    nonce: jjBulk.nonce,
                    slug: slug
                },
                success: function(response) {
                    if (response.success) {
                        // [v22.4.4] 설치 및 활성화 완료로 상태 변경 (자동 활성화와 동일한 형식)
                        $item.find('.status').html(
                            '✅ 설치 및 활성화 완료! ' +
                            '<a href="' + pluginsUrl + '" class="button button-small" style="margin-left: 8px; font-size: 11px;">플러그인 목록 보기</a>'
                        );
                        $item.addClass('jj-file-item-activated');
                        // 활성화 버튼 제거
                        $btn.remove();
                        showNotice('success', '플러그인이 활성화되었습니다! <a href="' + pluginsUrl + '">플러그인 목록에서 확인</a>');
                    } else {
                        $btn.prop('disabled', false).text('🚀 활성화');
                        showNotice('error', '활성화 실패: ' + (response.data || '알 수 없는 오류'));
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('🚀 활성화');
                    showNotice('error', '서버 통신 오류가 발생했습니다.');
                }
            });
        });
        
        // 선택한 플러그인 활성화 버튼
        $('#jj-activate-selected-plugins').on('click', function() {
            var $btn = $(this);
            var selectedItems = [];
            
            $('.jj-file-item-completed .jj-file-checkbox:checked').each(function() {
                var $item = $(this).closest('.jj-file-item');
                var slug = $item.data('slug');
                if (slug) {
                    selectedItems.push({ slug: slug, item: $item });
                }
            });
            
            if (selectedItems.length === 0) {
                alert('활성화할 플러그인을 선택해주세요.');
                return;
            }
            
            $btn.prop('disabled', true).text('활성화 중...');
            processActivation(selectedItems, 0, $btn);
        });
        
        // Ctrl/Shift 키 선택 기능 (인스톨러)
        var lastCheckedFile = null;
        $(document).on('click', '.jj-file-checkbox', function(e) {
            var $checkbox = $(this);
            var $item = $checkbox.closest('.jj-file-item');
            
            // Shift 키: 범위 선택
            if (e.shiftKey && lastCheckedFile !== null) {
                var $items = $('.jj-file-item');
                var startIdx = $items.index(lastCheckedFile);
                var endIdx = $items.index($item);
                var start = Math.min(startIdx, endIdx);
                var end = Math.max(startIdx, endIdx);
                
                $items.slice(start, end + 1).find('.jj-file-checkbox:not(:disabled)').prop('checked', true);
                updateSelectionInfo();
            }
            
            lastCheckedFile = $item;
        });

        // 1. 파일 선택 트리거 수정 (클릭 이벤트 버블링 방지)
        dropzone.on('click', function(e) {
            if (e.target !== fileInput[0]) {
                fileInput.click();
            }
        });

        dropzone.on('dragover', function(e) {
            e.preventDefault();
            dropzone.addClass('dragover');
        });

        dropzone.on('dragleave drop', function(e) {
            e.preventDefault();
            dropzone.removeClass('dragover');
        });

        dropzone.on('drop', function(e) {
            var files = e.originalEvent.dataTransfer.files;
            handleFiles(files);
        });

        fileInput.on('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            var maxFiles = (jjBulk && jjBulk.limits && jjBulk.limits.max_files) ? jjBulk.limits.max_files : 3;

            if (files.length + filesQueue.length > maxFiles) {
                alert(jjBulk.i18n.limit_reached + '\n' + jjBulk.i18n.upgrade_msg);
                return;
            }

            $.each(files, function(i, file) {
                if (file.name.split('.').pop().toLowerCase() !== 'zip') return;

                // 중복 체크
                var isDuplicate = filesQueue.some(function(f) { return f.name === file.name; });
                if (isDuplicate) return;

                filesQueue.push(file);
                addFileToList(file, filesQueue.length - 1);
            });

            if (filesQueue.length > 0) {
                $('#jj-actions-area').show();
                updateSelectionInfo(); // 선택 정보 업데이트 (실시간 반영)
            }
        }

        function addFileToList(file, index) {
            var sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            var html = '<div class="jj-file-item jj-file-item-pending" id="file-' + index + '" data-index="' + index + '" data-file-name="' + escapeHtml(file.name) + '">' +
                       '<div class="file-info">' +
                       '<input type="checkbox" class="jj-file-checkbox" data-index="' + index + '"> ' +
                       '<span class="name">' + escapeHtml(file.name) + '</span> ' +
                       '<span class="size">(' + sizeMB + ' MB)</span>' +
                       '</div>' +
                       '<span class="status">대기 중</span>' +
                       '</div>';
            $('#jj-file-list').append(html);
            updatePendingCount();
        }
        
        // 대기 목록 개수 업데이트
        function updatePendingCount() {
            var count = $('.jj-file-item-pending').length;
            $('#jj-pending-count').text(count + '개');
            if (count > 0) {
                $('#jj-selection-controls').show();
            }
        }
        
        // 완료 목록 개수 업데이트
        function updateCompletedCount() {
            var count = $('.jj-file-item-completed').length;
            var $countElement = $('#jj-completed-count');
            if ($countElement.length) {
                $countElement.text(count + '개');
            }
            if (count > 0) {
                $('#jj-file-list-completed').show();
                // [v22.4.0] 완료 목록이 있으면 자동으로 스크롤
                $('html, body').animate({
                    scrollTop: $('#jj-file-list-completed').offset().top - 100
                }, 500);
            }
        }

        // 설치 시작
        $('#jj-start-install').on('click', function() {
            if (isProcessing) return;

            // [Grand Upgrade] 대상 사이트 식별
            var activeTab = $('.jj-bulk-tab.is-active').data('tab');
            var multisiteIds = [];
            var remoteUrls = [];

            if (activeTab === 'multisite-installer') {
                $('input[name="multisite_target[]"]:checked').each(function() {
                    multisiteIds.push($(this).val());
                });
                if (multisiteIds.length === 0) {
                    if (!confirm('대상 사이트가 선택되지 않았습니다. 현재 사이트(메인)에만 설치하시겠습니까?')) return;
                }
            } else if (activeTab === 'remote-installer') {
                $('input[name="remote_target[]"]:checked').each(function() {
                    remoteUrls.push($(this).val());
                });
                if (remoteUrls.length === 0) {
                    if (!confirm('원격 사이트가 선택되지 않았습니다. 현재 사이트(메인)에만 설치하시겠습니까?')) return;
                }
            }

            var targets = {
                multisite: multisiteIds,
                remote: remoteUrls
            };

            isProcessing = true;
            $(this).prop('disabled', true);
            $('#jj-progress-area').show();

            processQueue(0, targets);
        });

        function processQueue(index, targets) {
            if (index >= filesQueue.length) {
                isProcessing = false;
                updateSelectionInfo(); // 선택 정보 업데이트 (실시간 반영)
                
                $('.jj-progress-fill').css('width', '100%');
                $('.jj-status-text').text('모든 작업 완료 (' + filesQueue.length + '/' + filesQueue.length + ')');
                $('#jj-add-more-files').show();
                
                if (installedPlugins.length > 0) {
                    $('#jj-selection-controls').show();
                    updateSelectionInfo();
                }

                showCompletionNotice();
                return;
            }

            var file = filesQueue[index];
            var item = $('#file-' + index);
            var autoActivate = (jjBulk && jjBulk.limits && jjBulk.limits.can_auto_activate) && $('#jj-auto-activate-all').is(':checked');

            item.addClass('uploading').find('.status').text('업로드 중...');
            updateProgress(index, filesQueue.length, '업로드 중: ' + file.name, false);

            var formData = new FormData();
            formData.append('action', 'jj_bulk_install_upload');
            formData.append('nonce', jjBulk.nonce);
            formData.append('file', file);

            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        item.find('.status').text('설치 중...');
                        // [Grand Upgrade] 로컬/멀티/원격 순차 설치 시작
                        startCrossSiteInstall(response.data, item, index, autoActivate, targets);
                    } else {
                        var errorMsg = response.data || '알 수 없는 오류';
                        item.addClass('error').find('.status').text('업로드 실패: ' + errorMsg);
                        updateProgress(index, filesQueue.length, '업로드 실패', true);
                        processQueue(index + 1, targets);
                    }
                },
                error: function(jqXHR, textStatus) {
                    var errorMsg = '서버 오류 (' + jqXHR.status + ')';
                    item.addClass('error').find('.status').text(errorMsg);
                    updateProgress(index, filesQueue.length, errorMsg, true);
                    processQueue(index + 1, targets);
                }
            });
        }

        // [Grand Upgrade] 여러 사이트에 순차적으로 설치하는 로직
        function startCrossSiteInstall(fileData, item, index, autoActivate, targets) {
            // 1. 현재 사이트(로컬) 설치
            installPlugin(fileData, item, index, autoActivate, function() {
                // 2. 멀티사이트(서브사이트) 설치
                processMultisiteInstall(fileData, item, index, autoActivate, targets.multisite, 0, function() {
                    // 3. 원격 사이트 설치
                    processRemoteInstall(fileData, item, index, autoActivate, targets.remote, 0, function() {
                        // 모든 설치 완료 후 다음 파일로
                        updateProgress(index, filesQueue.length, '완료: ' + fileData.name, true);
                        processQueue(index + 1, targets);
                    });
                });
            });
        }

        function installPlugin(data, item, index, autoActivate, callback) {
            updateProgress(index, filesQueue.length, '로컬 설치 중: ' + data.name, false);

            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_bulk_install_process',
                    nonce: jjBulk.nonce,
                    path: data.path,
                    type: data.type,
                    activate: autoActivate
                },
                success: function(response) {
                    if (response.success) {
                        var pluginsUrl = (jjBulk.admin_urls && jjBulk.admin_urls.plugins) ? jjBulk.admin_urls.plugins : 'plugins.php';
                        
                        // [v22.4.0] Phase 37: UI/UX 개선 - 완료 목록으로 이동 및 메시지 개선
                        if (data.type === 'plugin' && response.data.slug) {
                            item.data('slug', response.data.slug);
                            item.find('.jj-file-checkbox').prop('disabled', false);
                            installedPlugins.push(response.data.slug);
                            
                            // 자동 활성화가 성공한 경우
                            if (response.data.activated) {
                                item.find('.status').html(
                                    '✅ 설치 및 활성화 완료! ' +
                                    '<a href="' + pluginsUrl + '" class="button button-small" style="margin-left: 8px; font-size: 11px;">플러그인 목록 보기</a>'
                                );
                                item.addClass('jj-file-item-activated');
                                // [v22.4.0] 자동 활성화 성공 시 성공 메시지 표시
                                showNotice('success', '플러그인 "' + escapeHtml(data.name) + '"이(가) 설치 및 활성화되었습니다! <a href="' + pluginsUrl + '">플러그인 목록에서 확인</a>');
                            } else {
                                // 자동 활성화가 아닌 경우, 개별 활성화 버튼 추가
                                item.find('.status').html(
                                    '설치 완료 ' +
                                    '<button type="button" class="button button-small button-primary jj-activate-single" ' +
                                    'data-slug="' + escapeHtml(response.data.slug) + '" ' +
                                    'style="margin-left: 8px; font-size: 11px;">🚀 활성화</button>'
                                );
                            }
                        } else {
                            item.find('.status').text('설치 완료');
                        }
                        
                        // [v22.4.0] 완료된 항목을 완료 목록으로 이동
                        item.removeClass('jj-file-item-pending').addClass('jj-file-item-completed');
                        moveToCompletedList(item);
                        updateCompletedCount();
                    } else {
                        item.addClass('error').find('.status').text('로컬 설치 실패: ' + response.data);
                    }
                    if (callback) callback();
                },
                error: function() {
                    item.addClass('error').find('.status').text('로컬 통신 오류');
                    if (callback) callback();
                }
            });
        }
        
        // [v22.4.0] 완료된 항목을 완료 목록으로 이동하는 함수
        function moveToCompletedList(item) {
            var $completedList = $('#jj-file-list-completed-items');
            if ($completedList.length === 0) {
                // 완료 목록이 없으면 생성
                var $completedSection = $('#jj-file-list-completed');
                if ($completedSection.length === 0) {
                    // 완료 섹션이 없으면 생성
                    var completedHtml = '<div class="jj-file-list-section" id="jj-file-list-completed" style="display: block; margin-top: 20px;">' +
                        '<h3 class="jj-section-title">' +
                        '✅ 완료 목록 ' +
                        '<span class="jj-section-count" id="jj-completed-count">0개</span>' +
                        '</h3>' +
                        '<div class="jj-file-list" id="jj-file-list-completed-items"></div>' +
                        '</div>';
                    $('#jj-file-list').after(completedHtml);
                    $completedList = $('#jj-file-list-completed-items');
                } else {
                    $completedList = $completedSection.find('.jj-file-list');
                    if ($completedList.length === 0) {
                        $completedSection.append('<div class="jj-file-list" id="jj-file-list-completed-items"></div>');
                        $completedList = $('#jj-file-list-completed-items');
                    }
                }
            }
            
            // 항목을 완료 목록으로 이동
            item.detach().appendTo($completedList);
            
            // 완료 목록 섹션 표시
            $('#jj-file-list-completed').show();
        }

        function processMultisiteInstall(fileData, item, index, autoActivate, siteIds, siteIdx, callback) {
            if (!siteIds || siteIdx >= siteIds.length) {
                if (callback) callback();
                return;
            }

            var siteId = siteIds[siteIdx];
            updateProgress(index, filesQueue.length, '멀티사이트(' + siteId + ') 설치 중...', false);
            item.find('.status').text('멀티사이트(' + siteId + ') 설치 중...');

            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_bulk_multisite_install',
                    nonce: jjBulk.nonce,
                    site_id: siteId,
                    path: fileData.path,
                    type: fileData.type,
                    activate: autoActivate
                },
                success: function(response) {
                    if (!response.success) {
                        console.error('Multisite Install Failed (' + siteId + '):', response.data);
                    }
                    processMultisiteInstall(fileData, item, index, autoActivate, siteIds, siteIdx + 1, callback);
                },
                error: function() {
                    processMultisiteInstall(fileData, item, index, autoActivate, siteIds, siteIdx + 1, callback);
                }
            });
        }

        function processRemoteInstall(fileData, item, index, autoActivate, urls, urlIdx, callback) {
            if (!urls || urlIdx >= urls.length) {
                if (callback) callback();
                return;
            }

            var url = urls[urlIdx];
            updateProgress(index, filesQueue.length, '원격(' + url + ') 설치 중...', false);
            item.find('.status').text('원격(' + url + ') 전송 중...');

            // 원격 전송을 위해 실제 파일을 FormData에 다시 담아야 함 (보안 및 REST API 제약)
            // 브라우저 캐시 등을 활용하거나, 서버 사이드에서 Proxy 전송 가능
            // 여기서는 서버 사이드(PHP)에서 해당 파일을 원격으로 쏘는 Proxy 핸들러를 새로 만드는 것이 효율적임
            
            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_bulk_remote_install_proxy', // PHP에 구현 필요
                    nonce: jjBulk.nonce,
                    remote_url: url,
                    path: fileData.path,
                    type: fileData.type,
                    activate: autoActivate
                },
                success: function(response) {
                    if (!response.success) {
                        console.error('Remote Install Failed (' + url + '):', response.data);
                    }
                    processRemoteInstall(fileData, item, index, autoActivate, urls, urlIdx + 1, callback);
                },
                error: function() {
                    processRemoteInstall(fileData, item, index, autoActivate, urls, urlIdx + 1, callback);
                }
            });
        }

        function updateProgress(current, total, text, isComplete) {
            // current: 현재 처리 중인 인덱스 (0-based)
            // isComplete: 해당 파일 처리가 완료되었는지 여부
            var completedCount = isComplete ? (current + 1) : current;
            var percent = Math.round((completedCount / total) * 100);
            
            // 최소 표시 (시작 시 0%가 아닌 작은 값으로 시작)
            if (percent === 0 && current === 0 && !isComplete) {
                percent = 2; // 시작 시 최소 2%
            }
            
            // 100% 보장
            if (completedCount >= total) {
                percent = 100;
            }
            
            $('.jj-progress-fill').css('width', percent + '%');
            $('.jj-status-text').text(text + ' (' + completedCount + '/' + total + ')');
        }

        function processActivation(list, index, btn) {
            if (index >= list.length) {
                // [v22.3.1] 개선된 활성화 완료 메시지 - 플러그인 목록 링크 포함
                var pluginsUrl = (jjBulk.admin_urls && jjBulk.admin_urls.plugins) ? jjBulk.admin_urls.plugins : 'plugins.php';
                var message = '활성화 작업 완료! (' + list.length + '개) ';
                var extraHtml = '<p style="margin-top: 10px;">' +
                    '<a href="' + pluginsUrl + '" class="button button-primary">📦 플러그인 목록에서 확인</a>' +
                    '</p>';
                showNotice('success', message, extraHtml);
                
                if (btn) {
                    btn.prop('disabled', false);
                    updateSelectionInfo();
                }
                // 체크박스 해제
                $('.jj-file-checkbox:checked').prop('checked', false);
                updateSelectionInfo();
                return;
            }

            var target = list[index];
            target.item.find('.status').text('활성화 중...');

            $.ajax({
                url: jjBulk.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_bulk_activate_plugin',
                    nonce: jjBulk.nonce,
                    slug: target.slug
                },
                success: function(response) {
                    if (response.success) {
                        target.item.find('.status').text('설치 완료 (활성)');
                    } else {
                        target.item.find('.status').text('활성화 실패: ' + (response.data || '알 수 없는 오류'));
                    }
                    processActivation(list, index + 1, btn);
                },
                error: function() {
                    target.item.find('.status').text('활성화 오류');
                    processActivation(list, index + 1, btn);
                }
            });
        }
    }

    // ==============================
    // Bulk Editor (관리)
    // ==============================
    function initBulkEditorOnce() {
        if (bulkUi.editorInitialized) return;
        bulkUi.editorInitialized = true;

        // Subtabs
        $('.jj-subtab').on('click', function() {
            var subtab = $(this).data('subtab');
            bulkUi.currentSubtab = subtab;

            $('.jj-subtab').removeClass('is-active').attr('aria-selected', 'false');
            $(this).addClass('is-active').attr('aria-selected', 'true');

            $('[data-subtab-panel]').hide();
            $('[data-subtab-panel="' + subtab + '"]').show();

            // action buttons toggle
            if (subtab === 'themes') {
                $('#jj-bulk-action-activate').hide();
                $('#jj-bulk-action-deactivate').hide();
                $('#jj-bulk-action-update').show();
                $('#jj-bulk-action-rollback').hide();
                $('#jj-bulk-action-delete').hide();
                $('#jj-bulk-action-deactivate-delete').hide();
                $('#jj-bulk-action-auto-update-enable').hide();
                $('#jj-bulk-action-auto-update-disable').hide();
                $('#jj-bulk-action-theme-delete').show();
            } else {
                $('#jj-bulk-action-activate').show();
                $('#jj-bulk-action-deactivate').show();
                $('#jj-bulk-action-update').show();
                $('#jj-bulk-action-rollback').show();
                $('#jj-bulk-action-delete').show();
                $('#jj-bulk-action-deactivate-delete').show();
                $('#jj-bulk-action-auto-update-enable').show();
                $('#jj-bulk-action-auto-update-disable').show();
                $('#jj-bulk-action-theme-delete').hide();
            }

            applyFilters();
        });

        // Refresh
        $('#jj-bulk-refresh').on('click', function() {
            loadInstalledItems('plugin', true);
            loadInstalledItems('theme', true);
        });

        // Filters
        $('#jj-bulk-search').on('input', function() {
            applyFilters();
        });
        $('#jj-bulk-filter-status').on('change', function() {
            applyFilters();
        });

        // Select all
        $('#jj-bulk-select-all-plugins').on('change', function() {
            var checked = $(this).is(':checked');
            $('#jj-bulk-table-plugins tbody tr.jj-bulk-row:visible .jj-bulk-row-check:not(:disabled)').prop('checked', checked);
        });
        $('#jj-bulk-select-all-themes').on('change', function() {
            var checked = $(this).is(':checked');
            $('#jj-bulk-table-themes tbody tr.jj-bulk-row:visible .jj-bulk-row-check:not(:disabled)').prop('checked', checked);
        });
        
        // Ctrl/Shift 키 선택 기능 (벌크 에디터)
        var lastCheckedRow = null;
        $(document).on('click', '.jj-bulk-row-check', function(e) {
            var $checkbox = $(this);
            var $row = $checkbox.closest('tr');
            
            // Ctrl 키: 여러 개 선택
            if (e.ctrlKey || e.metaKey) {
                $checkbox.prop('checked', !$checkbox.prop('checked'));
                lastCheckedRow = $row;
            }
            // Shift 키: 범위 선택
            else if (e.shiftKey && lastCheckedRow !== null) {
                var $rows = $checkbox.closest('tbody').find('tr.jj-bulk-row');
                var startIdx = $rows.index(lastCheckedRow);
                var endIdx = $rows.index($row);
                var start = Math.min(startIdx, endIdx);
                var end = Math.max(startIdx, endIdx);
                
                $rows.slice(start, end + 1).find('.jj-bulk-row-check:not(:disabled)').prop('checked', true);
            }
            // 일반 클릭: 단일 선택
            else {
                lastCheckedRow = $row;
            }
        });

        // Actions (plugins / themes)
        $('#jj-bulk-action-activate, #jj-bulk-action-deactivate, #jj-bulk-action-update, #jj-bulk-action-rollback, #jj-bulk-action-delete, #jj-bulk-action-deactivate-delete, #jj-bulk-action-auto-update-enable, #jj-bulk-action-auto-update-disable, #jj-bulk-action-theme-delete').on('click', function() {
            var op = $(this).data('op');
            var type = $(this).data('type');
            runBulkOperation(type, op);
        });

        // Initial load
        loadInstalledItems('plugin', true);
        loadInstalledItems('theme', true);
    }

    function loadInstalledItems(itemType, forceReload) {
        if (!forceReload && itemType === 'plugin' && bulkCache.plugins) {
            renderPluginsTable(bulkCache.plugins.items || []);
            updateStats();
            return;
        }
        if (!forceReload && itemType === 'theme' && bulkCache.themes) {
            renderThemesTable(bulkCache.themes.items || []);
            updateStats();
            return;
        }

        var $tbody = (itemType === 'plugin') ? $('#jj-bulk-table-plugins tbody') : $('#jj-bulk-table-themes tbody');
        if ($tbody.length) {
            $tbody.html('<tr><td colspan="6">목록을 불러오는 중...</td></tr>');
        }

        $.ajax({
            url: jjBulk.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_bulk_manage_get_items',
                nonce: jjBulk.nonce,
                item_type: itemType
            },
            success: function(resp) {
                if (!resp || !resp.success) {
                    showNotice('error', '목록을 불러오지 못했습니다: ' + escapeHtml(resp && resp.data ? resp.data : '알 수 없는 오류'));
                    return;
                }

                if (itemType === 'plugin') {
                    bulkCache.plugins = resp.data;
                    renderPluginsTable(resp.data.items || []);
                } else {
                    bulkCache.themes = resp.data;
                    renderThemesTable(resp.data.items || []);
                }
                updateStats();
                applyFilters();
            },
            error: function() {
                showNotice('error', '서버 통신 오류로 목록을 불러오지 못했습니다.');
            }
        });
    }

    function updateStats() {
        if (bulkCache.plugins && bulkCache.plugins.counts) {
            $('#jj-count-plugins').text(bulkCache.plugins.counts.total || 0);
            $('#jj-count-plugins-active').text(bulkCache.plugins.counts.active || 0);
            $('#jj-count-plugins-update').text(bulkCache.plugins.counts.updates || 0);
        }
        if (bulkCache.themes && bulkCache.themes.counts) {
            $('#jj-count-themes').text(bulkCache.themes.counts.total || 0);
        }
    }

    function renderPluginsTable(items) {
        var $tbody = $('#jj-bulk-table-plugins tbody');
        if ($tbody.length === 0) return;

        if (!items || items.length === 0) {
            $tbody.html('<tr><td colspan="6">설치된 플러그인이 없습니다.</td></tr>');
            return;
        }

        var rows = [];
        items.forEach(function(p) {
            var status = p.network_active ? 'network_active' : (p.active ? 'active' : 'inactive');
            var statusLabel = p.network_active ? '네트워크' : (p.active ? '활성' : '비활성');
            var statusClass = p.network_active ? 'jj-pill-neutral' : (p.active ? 'jj-pill-good' : 'jj-pill-muted');
            var rowStatus = (p.active || p.network_active) ? 'active' : 'inactive';

            var auLabel = p.auto_update ? 'ON' : 'OFF';
            var auClass = p.auto_update ? 'jj-pill-good' : 'jj-pill-muted';

            var updHtml = '<span class="jj-pill jj-pill-muted">없음</span>';
            if (p.update_available) {
                updHtml = '<span class="jj-pill jj-pill-warn">업데이트</span>' + (p.new_version ? ' <code>' + escapeHtml(p.new_version) + '</code>' : '');
            }

            var requires = '';
            if (p.requires_plugins && p.requires_plugins.length) {
                requires = '<div class="jj-inline-tags">' +
                    p.requires_plugins.map(function(x) {
                        return '<span class="jj-tag">필요: ' + escapeHtml(x) + '</span>';
                    }).join(' ') +
                    '</div>';
            }

            var checkboxAttrs = '';
            if (p.network_active) {
                checkboxAttrs = ' disabled title="네트워크 활성 플러그인은 네트워크 관리자에서 관리하세요."';
            }

            // [v5.0.1] 다국어 이름 표시
            var displayName = '<strong class="jj-item-name">' + escapeHtml(p.name) + '</strong>';
            if (p.name_translated && p.name_translated !== p.name) {
                displayName += ' <span class="jj-name-translated" style="color: #646970; font-size: 0.9em;">(' + escapeHtml(p.name_translated) + ')</span>';
            }

            rows.push(
                '<tr class="jj-bulk-row" data-status="' + escapeHtml(rowStatus) + '" data-search="' + escapeHtml((p.name + ' ' + (p.name_translated || '') + ' ' + (p.author || '') + ' ' + p.id).toLowerCase()) + '">' +
                    '<th scope="row" class="check-column"><input type="checkbox" class="jj-bulk-row-check" data-id="' + escapeHtml(p.id) + '"' + checkboxAttrs + '></th>' +
                    '<td>' +
                        displayName + ' <span class="description">v' + escapeHtml(p.version || '-') + '</span>' +
                        (p.author ? '<div class="description">작성자: ' + escapeHtml(p.author) + '</div>' : '') +
                        requires +
                    '</td>' +
                    '<td><span class="jj-pill ' + statusClass + '">' + escapeHtml(statusLabel) + '</span></td>' +
                    '<td><span class="jj-pill ' + auClass + '">' + escapeHtml(auLabel) + '</span></td>' +
                    '<td>' + updHtml + '</td>' +
                    '<td><code>' + escapeHtml(p.id) + '</code></td>' +
                '</tr>'
            );
        });

        $tbody.html(rows.join(''));
    }

    function renderThemesTable(items) {
        var $tbody = $('#jj-bulk-table-themes tbody');
        if ($tbody.length === 0) return;

        if (!items || items.length === 0) {
            $tbody.html('<tr><td colspan="6">설치된 테마가 없습니다.</td></tr>');
            return;
        }

        var rows = [];
        items.forEach(function(t) {
            var statusLabel = t.active ? '활성' : '비활성';
            var statusClass = t.active ? 'jj-pill-good' : 'jj-pill-muted';

            var auLabel = t.auto_update ? 'ON' : 'OFF';
            var auClass = t.auto_update ? 'jj-pill-good' : 'jj-pill-muted';

            var updHtml = '<span class="jj-pill jj-pill-muted">없음</span>';
            if (t.update_available) {
                updHtml = '<span class="jj-pill jj-pill-warn">업데이트</span>' + (t.new_version ? ' <code>' + escapeHtml(t.new_version) + '</code>' : '');
            }

            var checkboxAttrs = '';
            if (t.active) {
                checkboxAttrs = ' disabled title="현재 사용 중인(활성) 테마는 삭제할 수 없습니다."';
            }

            rows.push(
                '<tr class="jj-bulk-row" data-status="' + escapeHtml(t.active ? 'active' : 'inactive') + '" data-search="' + escapeHtml((t.name + ' ' + (t.author || '') + ' ' + t.id).toLowerCase()) + '">' +
                    '<th scope="row" class="check-column"><input type="checkbox" class="jj-bulk-row-check" data-id="' + escapeHtml(t.id) + '"' + checkboxAttrs + '></th>' +
                    '<td>' +
                        '<strong>' + escapeHtml(t.name) + '</strong> <span class="description">v' + escapeHtml(t.version || '-') + '</span>' +
                        (t.author ? '<div class="description">' + escapeHtml(t.author) + '</div>' : '') +
                    '</td>' +
                    '<td><span class="jj-pill ' + statusClass + '">' + escapeHtml(statusLabel) + '</span></td>' +
                    '<td><span class="jj-pill ' + auClass + '">' + escapeHtml(auLabel) + '</span></td>' +
                    '<td>' + updHtml + '</td>' +
                    '<td><code>' + escapeHtml(t.id) + '</code></td>' +
                '</tr>'
            );
        });

        $tbody.html(rows.join(''));
    }

    function applyFilters() {
        var query = ($('#jj-bulk-search').val() || '').toLowerCase().trim();
        var status = $('#jj-bulk-filter-status').val() || 'all';

        var $table = (bulkUi.currentSubtab === 'themes') ? $('#jj-bulk-table-themes') : $('#jj-bulk-table-plugins');
        $table.find('tbody tr.jj-bulk-row').each(function() {
            var $tr = $(this);
            var rowStatus = $tr.attr('data-status');
            var hay = $tr.attr('data-search') || '';

            var okStatus = (status === 'all') || (status === rowStatus);
            var okQuery = (!query) || (hay.indexOf(query) !== -1);

            $tr.toggle(okStatus && okQuery);
        });
    }

    function getSelectedIds(itemType) {
        var $table = (itemType === 'theme') ? $('#jj-bulk-table-themes') : $('#jj-bulk-table-plugins');
        var ids = [];
        $table.find('tbody tr.jj-bulk-row .jj-bulk-row-check:checked').each(function() {
            var id = $(this).data('id');
            if (id) ids.push(String(id));
        });
        return ids;
    }

    function runBulkOperation(itemType, operation) {
        var ids = getSelectedIds(itemType);
        if (ids.length === 0) {
            showNotice('warning', '선택된 항목이 없습니다.');
            return;
        }

        var maxManage = (jjBulk && jjBulk.limits && jjBulk.limits.max_manage_items) ? parseInt(jjBulk.limits.max_manage_items, 10) : 3;
        if (ids.length > maxManage) {
            alert(jjBulk.i18n.manage_limit_reached);
            return;
        }

        // client-side gating (server-side도 동일하게 검증)
        if ((operation === 'delete' || operation === 'deactivate_delete') && jjBulk && jjBulk.limits && !jjBulk.limits.can_bulk_delete) {
            alert(jjBulk.i18n.delete_locked);
            return;
        }
        if (operation === 'deactivate_delete' && jjBulk && jjBulk.limits && !jjBulk.limits.can_deactivate_then_delete) {
            alert(jjBulk.i18n.deactivate_delete_locked);
            return;
        }

        var confirmText = '';
        if (operation === 'activate') {
            confirmText = '선택한 ' + ids.length + '개를 활성화할까요?';
        } else if (operation === 'deactivate') {
            confirmText = '선택한 ' + ids.length + '개를 비활성화할까요?';
        } else if (operation === 'update') {
            confirmText = '선택한 ' + ids.length + '개를 업데이트할까요?';
        } else if (operation === 'rollback') {
            confirmText = '선택한 ' + ids.length + '개를 롤백할까요? (지원되는 항목만 적용됩니다)';
        } else if (operation === 'delete') {
            confirmText = '정말로 선택한 ' + ids.length + '개를 삭제할까요?\n삭제는 되돌릴 수 없습니다.';
        } else if (operation === 'deactivate_delete') {
            confirmText = '선택한 ' + ids.length + '개를 비활성화한 뒤 즉시 삭제할까요?\n삭제는 되돌릴 수 없습니다.';
        } else if (operation === 'auto_update_enable') {
            confirmText = '선택한 ' + ids.length + '개의 자동 업데이트를 허용할까요?';
        } else if (operation === 'auto_update_disable') {
            confirmText = '선택한 ' + ids.length + '개의 자동 업데이트를 비허용할까요?';
        }

        if (confirmText && !window.confirm(confirmText)) {
            return;
        }

        showNotice('info', '작업을 진행 중입니다...');

        $.ajax({
            url: jjBulk.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_bulk_manage_action',
                nonce: jjBulk.nonce,
                item_type: itemType,
                operation: operation,
                items: ids
            },
            success: function(resp) {
                if (!resp || !resp.success) {
                    showNotice('error', '작업 실패: ' + escapeHtml(resp && resp.data ? resp.data : '알 수 없는 오류'));
                    return;
                }

                var results = (resp.data && resp.data.results) ? resp.data.results : [];
                var ok = results.filter(function(r) { return r.ok; }).length;
                var fail = results.length - ok;

                var extra = '';
                if (fail > 0) {
                    var listItems = results.filter(function(r) { return !r.ok; }).map(function(r) {
                        return '<li><code>' + escapeHtml(r.id) + '</code> - ' + escapeHtml(r.message) + '</li>';
                    }).join('');
                    extra = '<ul style="margin: 0.5em 0 0.2em 1.2em; list-style: disc;">' + listItems + '</ul>';
                }

                showNotice(fail === 0 ? 'success' : 'warning', '완료: 성공 ' + ok + '개 / 실패 ' + fail + '개', extra);

                // refresh the relevant list
                if (itemType === 'theme') {
                    loadInstalledItems('theme', true);
                } else {
                    loadInstalledItems('plugin', true);
                }
            },
            error: function() {
                showNotice('error', '서버 통신 오류로 작업에 실패했습니다.');
            }
        });
    }

    initTabs();
    initInstaller();
    initTooltipSystem();
    initRemoteConnection();
});
