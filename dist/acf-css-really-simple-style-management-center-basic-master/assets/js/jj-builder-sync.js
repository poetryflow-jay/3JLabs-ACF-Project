/**
 * JJ Builder Sync
 *
 * [Phase 10.4] Builder 1-click Sync UI (Admin Center > Tools)
 *
 * - Elementor Kit: Global Colors / Global Typography DB 동기화
 * - 백업 목록/롤백 UI
 *
 * @since 10.4.0
 */
(function($) {
    'use strict';

    function toast(message, type) {
        if (window.JJUtils && typeof window.JJUtils.showToast === 'function') {
            window.JJUtils.showToast(message, type || 'info');
            return;
        }
        // fallback
        alert(message);
    }

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getConfig() {
        return window.jjBuilderSync || null;
    }

    function getTypographyKeys(cfg) {
        if (cfg && Array.isArray(cfg.typography_keys) && cfg.typography_keys.length) {
            return cfg.typography_keys;
        }
        return ['h1', 'h2', 'h3', 'p'];
    }

    function buildSelectOptions(keys, selected) {
        var html = '';
        keys.forEach(function(k) {
            var label = String(k).toUpperCase();
            html += '<option value="' + escapeHtml(k) + '"' + (k === selected ? ' selected' : '') + '>' + escapeHtml(label) + '</option>';
        });
        return html;
    }

    function renderBuilderSyncUI() {
        var cfg = getConfig();
        if (!cfg) return;

        var $root = $('#jj-builder-sync');
        if ($root.length === 0) return;

        // Populate builders (enable/disable)
        $root.find('.jj-bsync-builder').each(function() {
            var $cb = $(this);
            var key = $cb.val();
            var meta = (cfg.builders && cfg.builders[key]) ? cfg.builders[key] : null;
            if (!meta) return;

            // 표시 텍스트
            $cb.closest('label').find('.jj-bsync-builder-label').text(meta.label || key);

            // 활성화/비활성화
            if (meta.active === false) {
                $cb.prop('checked', false).prop('disabled', true);
                $cb.closest('label').addClass('jj-bsync-disabled');
                $cb.closest('label').find('.jj-bsync-status').text('(' + '미감지' + ')');
            } else {
                $cb.prop('disabled', false);
                $cb.closest('label').removeClass('jj-bsync-disabled');
                $cb.closest('label').find('.jj-bsync-status').text(meta.db_sync ? '(DB 동기화 지원)' : '(alias만)');
            }
        });

        // Typography mapping selects
        var keys = getTypographyKeys(cfg);
        $('#jj-bsync-el-typo-primary').html(buildSelectOptions(keys, 'h2'));
        $('#jj-bsync-el-typo-secondary').html(buildSelectOptions(keys, 'h3'));
        $('#jj-bsync-el-typo-text').html(buildSelectOptions(keys, 'p'));
        $('#jj-bsync-el-typo-accent').html(buildSelectOptions(keys, 'h4'));
    }

    function selectedBuilders() {
        var builders = [];
        $('#jj-builder-sync .jj-bsync-builder:checked').each(function() {
            builders.push($(this).val());
        });
        return builders;
    }

    function getScope() {
        return {
            colors: !!$('#jj-bsync-scope-colors').prop('checked'),
            typography: !!$('#jj-bsync-scope-typography').prop('checked')
        };
    }

    function getMapping() {
        return {
            elementor: {
                primary: $('#jj-bsync-el-typo-primary').val() || 'h2',
                secondary: $('#jj-bsync-el-typo-secondary').val() || 'h3',
                text: $('#jj-bsync-el-typo-text').val() || 'p',
                accent: $('#jj-bsync-el-typo-accent').val() || 'h4'
            }
        };
    }

    function setBusy(isBusy) {
        $('#jj-bsync-run, #jj-bsync-preview').prop('disabled', !!isBusy);
        if (isBusy) {
            $('#jj-bsync-spinner').show().addClass('is-active');
        } else {
            $('#jj-bsync-spinner').hide().removeClass('is-active');
        }
    }

    function renderResults(results) {
        var $out = $('#jj-bsync-result');
        if ($out.length === 0) return;

        if (!results) {
            $out.html('');
            return;
        }

        var html = '<div style="padding:12px; border:1px solid #c3c4c7; background:#fff; border-radius:6px;">';
        html += '<h4 style="margin:0 0 10px 0;">결과</h4>';
        html += '<ul style="margin:0 0 0 18px; list-style:disc;">';

        Object.keys(results).forEach(function(builder) {
            var r = results[builder] || {};
            var status = r.status || 'unknown';
            var msg = r.message || '';
            var extra = '';
            if (r.backup_id) {
                extra = ' (backup: ' + escapeHtml(r.backup_id) + ')';
            }

            var changes = '';
            if (r.diff && r.diff.counts && typeof r.diff.counts.total !== 'undefined') {
                changes = ' (changes: ' + escapeHtml(r.diff.counts.total) + ')';
            }

            html += '<li style="margin-bottom:8px;">';
            html += '<div><strong>' + escapeHtml(builder) + '</strong>: ' + escapeHtml(status) + ' - ' + escapeHtml(msg) + extra + changes + '</div>';

            if (r.diff && ( (r.diff.colors && r.diff.colors.length) || (r.diff.typography && r.diff.typography.length) )) {
                html += '<details style="margin-top:6px;">';
                html += '<summary style="cursor:pointer;">변경 diff 보기</summary>';

                if (r.diff.colors && r.diff.colors.length) {
                    html += '<div style="margin-top:8px;"><strong>Colors</strong><ul style="margin:6px 0 0 18px; list-style:disc;">';
                    r.diff.colors.forEach(function(c) {
                        html += '<li>' + escapeHtml(c.title || c.id || '') + ': <code>' + escapeHtml(c.before || '') + '</code> → <code>' + escapeHtml(c.after || '') + '</code></li>';
                    });
                    html += '</ul></div>';
                }

                if (r.diff.typography && r.diff.typography.length) {
                    html += '<div style="margin-top:10px;"><strong>Typography</strong><ul style="margin:6px 0 0 18px; list-style:disc;">';
                    r.diff.typography.forEach(function(t) {
                        html += '<li>';
                        html += '<div><strong>' + escapeHtml(t.title || t.id || '') + '</strong> (JJ: ' + escapeHtml(t.jj_tag || '') + ')</div>';
                        if (t.changes && t.changes.length) {
                            html += '<ul style="margin:6px 0 0 18px; list-style:circle;">';
                            t.changes.forEach(function(ch) {
                                html += '<li>' + escapeHtml(ch.key || '') + ': <code>' + escapeHtml(ch.before || '') + '</code> → <code>' + escapeHtml(ch.after || '') + '</code></li>';
                            });
                            html += '</ul>';
                        }
                        html += '</li>';
                    });
                    html += '</ul></div>';
                }

                html += '</details>';
            }

            html += '</li>';
        });

        html += '</ul></div>';
        $out.html(html);
    }

    function loadBackups() {
        var cfg = getConfig();
        if (!cfg) return;

        $.ajax({
            url: cfg.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_builder_sync_list_backups',
                nonce: cfg.nonce
            },
            success: function(resp) {
                if (!resp || !resp.success) {
                    return;
                }
                renderBackups(resp.data && resp.data.backups ? resp.data.backups : []);
            }
        });
    }

    function renderBackups(backups) {
        var $wrap = $('#jj-bsync-backups');
        if ($wrap.length === 0) return;

        if (!backups || backups.length === 0) {
            $wrap.html('<p style="color:#666; margin:0;">백업이 없습니다.</p>');
            return;
        }

        var html = '<table class="widefat" style="margin-top:10px;"><thead><tr>';
        html += '<th>일시</th><th>빌더</th><th>범위</th><th>사용자</th><th>작업</th>';
        html += '</tr></thead><tbody>';

        backups.forEach(function(b) {
            var scope = [];
            if (b.scope && b.scope.colors) scope.push('컬러');
            if (b.scope && b.scope.typography) scope.push('타이포');
            if (scope.length === 0) scope.push('-');

            html += '<tr>';
            html += '<td><code>' + escapeHtml(b.date || '') + '</code></td>';
            html += '<td>' + escapeHtml(b.builder || '') + '</td>';
            html += '<td>' + escapeHtml(scope.join(', ')) + '</td>';
            html += '<td>' + escapeHtml(b.user_name || '') + '</td>';
            html += '<td><button type="button" class="button button-small jj-bsync-rollback" data-backup-id="' + escapeHtml(b.id) + '">롤백</button></td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        $wrap.html(html);
    }

    function bindEvents() {
        var cfg = getConfig();
        if (!cfg) return;

        // Elementor 선택 시 매핑 패널 표시
        $(document).on('change', '#jj-builder-sync .jj-bsync-builder', function() {
            var builders = selectedBuilders();
            var showElementor = builders.indexOf('elementor') !== -1;
            $('#jj-builder-sync-elementor-mapping').toggle(showElementor);
        });

        // 미리보기 (dry-run)
        $(document).on('click', '#jj-bsync-preview', function() {
            var builders = selectedBuilders();
            var scope = getScope();

            if (!builders.length) {
                toast('동기화할 빌더를 선택하세요.', 'warning');
                return;
            }
            if (!scope.colors && !scope.typography) {
                toast('동기화 범위(컬러/타이포)를 선택하세요.', 'warning');
                return;
            }

            setBusy(true);
            renderResults(null);

            $.ajax({
                url: cfg.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_builder_sync_run',
                    nonce: cfg.nonce,
                    dry_run: '1',
                    builders: builders,
                    scope: scope,
                    mapping: getMapping()
                },
                success: function(resp) {
                    setBusy(false);
                    if (resp && resp.success) {
                        renderResults(resp.data && resp.data.results ? resp.data.results : {});
                        toast((cfg.strings && cfg.strings.preview_success) ? cfg.strings.preview_success : '미리보기가 완료되었습니다.', 'success');
                    } else {
                        var msg = (resp && resp.data && resp.data.message) ? resp.data.message : ((cfg.strings && cfg.strings.preview_error) ? cfg.strings.preview_error : '미리보기 중 오류');
                        toast(msg, 'error');
                    }
                },
                error: function() {
                    setBusy(false);
                    toast((cfg.strings && cfg.strings.preview_error) ? cfg.strings.preview_error : '미리보기 중 오류', 'error');
                }
            });
        });

        $(document).on('click', '#jj-bsync-run', function() {
            var builders = selectedBuilders();
            var scope = getScope();

            if (!builders.length) {
                toast('동기화할 빌더를 선택하세요.', 'warning');
                return;
            }
            if (!scope.colors && !scope.typography) {
                toast('동기화 범위(컬러/타이포)를 선택하세요.', 'warning');
                return;
            }

            if (!confirm((cfg.strings && cfg.strings.confirm_run) ? cfg.strings.confirm_run : '실행할까요?')) {
                return;
            }

            setBusy(true);
            renderResults(null);

            $.ajax({
                url: cfg.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_builder_sync_run',
                    nonce: cfg.nonce,
                    builders: builders,
                    scope: scope,
                    mapping: getMapping()
                },
                success: function(resp) {
                    setBusy(false);
                    if (resp && resp.success) {
                        renderResults(resp.data && resp.data.results ? resp.data.results : {});
                        toast((cfg.strings && cfg.strings.run_success) ? cfg.strings.run_success : '완료', 'success');
                        loadBackups();
                    } else {
                        var msg = (resp && resp.data && resp.data.message) ? resp.data.message : ((cfg.strings && cfg.strings.run_error) ? cfg.strings.run_error : '오류');
                        toast(msg, 'error');
                    }
                },
                error: function(xhr) {
                    setBusy(false);
                    toast((cfg.strings && cfg.strings.run_error) ? cfg.strings.run_error : '오류', 'error');
                }
            });
        });

        $(document).on('click', '.jj-bsync-rollback', function() {
            var backupId = $(this).data('backup-id');
            if (!backupId) return;

            if (!confirm((cfg.strings && cfg.strings.confirm_rollback) ? cfg.strings.confirm_rollback : '롤백할까요?')) {
                return;
            }

            $(this).prop('disabled', true);

            $.ajax({
                url: cfg.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_builder_sync_rollback',
                    nonce: cfg.nonce,
                    backup_id: backupId
                },
                success: function(resp) {
                    $('.jj-bsync-rollback').prop('disabled', false);
                    if (resp && resp.success) {
                        toast((cfg.strings && cfg.strings.rollback_success) ? cfg.strings.rollback_success : '롤백 완료', 'success');
                        loadBackups();
                    } else {
                        var msg = (resp && resp.data && resp.data.message) ? resp.data.message : ((cfg.strings && cfg.strings.rollback_error) ? cfg.strings.rollback_error : '오류');
                        toast(msg, 'error');
                    }
                },
                error: function() {
                    $('.jj-bsync-rollback').prop('disabled', false);
                    toast((cfg.strings && cfg.strings.rollback_error) ? cfg.strings.rollback_error : '오류', 'error');
                }
            });
        });
    }

    $(document).ready(function() {
        if (typeof window.jjBuilderSync === 'undefined') {
            return;
        }
        renderBuilderSyncUI();
        bindEvents();
        loadBackups();
    });
})(jQuery);

