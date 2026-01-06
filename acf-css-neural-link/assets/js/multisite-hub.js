/**
 * 3J Labs Multisite Hub JavaScript
 * Phase 50B - P50-4: 멀티사이트 중앙관리
 *
 * @package ACF_CSS_Neural_Link
 * @version 8.2.0
 */

(function($) {
    'use strict';

    /**
     * Multisite Hub Controller
     */
    var MultisiteHub = {

        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Bind Events
         */
        bindEvents: function() {
            // 사이트 목록 새로고침
            $(document).on('click', '.jj-refresh-sites', this.refreshSites.bind(this));

            // 전체 선택
            $(document).on('change', '#jj-select-all-sites', this.toggleAllSites.bind(this));

            // 사이트 상세 보기
            $(document).on('click', '.jj-view-site', this.viewSiteDetails.bind(this));

            // 개별 사이트 동기화
            $(document).on('click', '.jj-sync-site', this.syncSingleSite.bind(this));

            // 일괄 작업 적용
            $(document).on('click', '#jj-apply-bulk', this.applyBulkAction.bind(this));

            // 모달 닫기
            $(document).on('click', '.jj-modal-close', this.closeModal.bind(this));
            $(document).on('click', '.jj-modal', function(e) {
                if (e.target === this) {
                    MultisiteHub.closeModal();
                }
            });

            // 동기화 페이지: 모두 선택/해제
            $(document).on('click', '#jj-select-all', this.selectAllSyncSites.bind(this));
            $(document).on('click', '#jj-deselect-all', this.deselectAllSyncSites.bind(this));

            // 동기화 시작
            $(document).on('click', '#jj-start-sync', this.startSync.bind(this));
        },

        /**
         * 사이트 목록 새로고침
         */
        refreshSites: function(e) {
            e.preventDefault();
            var $btn = $(e.currentTarget);
            var self = this;

            $btn.addClass('updating-message');

            $.post(JJ_Multisite.ajax_url, {
                action: '3j_multisite_get_sites',
                nonce: JJ_Multisite.nonce
            }, function(response) {
                $btn.removeClass('updating-message');
                if (response.success) {
                    self.updateSitesList(response.data);
                    self.showNotice('success', '사이트 목록이 새로고침되었습니다.');
                } else {
                    self.showNotice('error', response.data || JJ_Multisite.i18n.error);
                }
            }).fail(function() {
                $btn.removeClass('updating-message');
                self.showNotice('error', JJ_Multisite.i18n.error);
            });
        },

        /**
         * 사이트 목록 업데이트
         * @param {Array} sites
         */
        updateSitesList: function(sites) {
            var $tbody = $('#jj-sites-list');
            if (!$tbody.length || !sites) return;

            var html = '';
            sites.forEach(function(site) {
                html += '<tr data-site-id="' + site.id + '">';
                html += '<td class="check-column">';
                html += '<input type="checkbox" class="jj-site-check" value="' + site.id + '">';
                html += '</td>';
                html += '<td>';
                html += '<strong>' + site.name + '</strong>';
                if (site.is_main) {
                    html += ' <span class="jj-badge jj-badge-primary">메인</span>';
                }
                html += '</td>';
                html += '<td>';
                html += '<a href="' + site.url + '" target="_blank">' + site.domain + '</a>';
                html += '</td>';
                html += '<td>';
                html += '<span class="jj-plugin-count">' + site.active_count + '</span> / ' + site.total_count;
                html += '</td>';
                html += '<td>';
                if (site.is_active) {
                    html += '<span class="jj-status jj-status-active">' + JJ_Multisite.i18n.active + '</span>';
                } else {
                    html += '<span class="jj-status jj-status-inactive">' + JJ_Multisite.i18n.inactive + '</span>';
                }
                html += '</td>';
                html += '<td><span class="jj-text-muted">-</span></td>';
                html += '<td>';
                html += '<button type="button" class="button button-small jj-view-site" data-site-id="' + site.id + '">';
                html += '<span class="dashicons dashicons-visibility"></span>';
                html += '</button> ';
                html += '<button type="button" class="button button-small jj-sync-site" data-site-id="' + site.id + '">';
                html += '<span class="dashicons dashicons-update"></span>';
                html += '</button>';
                html += '</td>';
                html += '</tr>';
            });

            $tbody.html(html);
        },

        /**
         * 전체 사이트 선택/해제
         */
        toggleAllSites: function(e) {
            var checked = $(e.currentTarget).prop('checked');
            $('.jj-site-check').prop('checked', checked);
        },

        /**
         * 사이트 상세 보기
         * @param {Event} e
         */
        viewSiteDetails: function(e) {
            e.preventDefault();
            var $btn = $(e.currentTarget);
            var siteId = $btn.data('site-id');
            var self = this;

            this.showModal('로딩 중...', '<div class="jj-loading" style="height: 200px;"></div>');

            $.post(JJ_Multisite.ajax_url, {
                action: '3j_multisite_get_site_details',
                nonce: JJ_Multisite.nonce,
                site_id: siteId
            }, function(response) {
                if (response.success) {
                    self.showSiteDetails(response.data);
                } else {
                    self.showModal('오류', '<p>' + (response.data || JJ_Multisite.i18n.error) + '</p>');
                }
            }).fail(function() {
                self.showModal('오류', '<p>' + JJ_Multisite.i18n.error + '</p>');
            });
        },

        /**
         * 사이트 상세 정보 표시
         * @param {Object} site
         */
        showSiteDetails: function(site) {
            var html = '';

            // 사이트 정보
            html += '<div class="jj-site-info">';
            html += '<p><strong>사이트명:</strong> ' + site.name + '</p>';
            html += '<p><strong>URL:</strong> <a href="' + site.url + '" target="_blank">' + site.url + '</a></p>';
            html += '<p><strong>마지막 동기화:</strong> ' + (site.last_sync || '없음') + '</p>';
            html += '</div>';

            // 플러그인 목록
            html += '<h4 style="margin-top: 20px;">플러그인 상태</h4>';
            html += '<div class="jj-site-detail-plugins">';

            if (site.plugins) {
                Object.keys(site.plugins).forEach(function(slug) {
                    var plugin = site.plugins[slug];
                    html += '<div class="jj-site-plugin-item">';
                    html += '<div class="jj-plugin-info">';
                    html += '<span class="jj-plugin-name">' + plugin.name + '</span>';
                    if (plugin.version) {
                        html += '<span class="jj-plugin-version">v' + plugin.version + '</span>';
                    }
                    html += '</div>';
                    if (plugin.active) {
                        html += '<span class="jj-status jj-status-active">' + JJ_Multisite.i18n.active + '</span>';
                    } else {
                        html += '<span class="jj-status jj-status-inactive">' + JJ_Multisite.i18n.inactive + '</span>';
                    }
                    html += '</div>';
                });
            }

            html += '</div>';

            this.showModal(site.name, html);
        },

        /**
         * 개별 사이트 동기화
         * @param {Event} e
         */
        syncSingleSite: function(e) {
            e.preventDefault();
            var $btn = $(e.currentTarget);
            var siteId = $btn.data('site-id');
            var self = this;

            if (!confirm(JJ_Multisite.i18n.confirm_sync)) {
                return;
            }

            $btn.addClass('updating-message').prop('disabled', true);

            $.post(JJ_Multisite.ajax_url, {
                action: '3j_multisite_sync_plugins',
                nonce: JJ_Multisite.nonce,
                site_ids: [siteId],
                activate: true
            }, function(response) {
                $btn.removeClass('updating-message').prop('disabled', false);
                if (response.success) {
                    self.showNotice('success', response.data.message);
                    self.refreshSites({ preventDefault: function() {} });
                } else {
                    self.showNotice('error', response.data || JJ_Multisite.i18n.error);
                }
            }).fail(function() {
                $btn.removeClass('updating-message').prop('disabled', false);
                self.showNotice('error', JJ_Multisite.i18n.error);
            });
        },

        /**
         * 일괄 작업 적용
         * @param {Event} e
         */
        applyBulkAction: function(e) {
            e.preventDefault();
            var action = $('#jj-bulk-action').val();
            var siteIds = [];
            var self = this;

            $('.jj-site-check:checked').each(function() {
                siteIds.push($(this).val());
            });

            if (!action) {
                this.showNotice('error', '작업을 선택해주세요.');
                return;
            }

            if (siteIds.length === 0) {
                this.showNotice('error', '사이트를 선택해주세요.');
                return;
            }

            if (!confirm('선택한 ' + siteIds.length + '개 사이트에 작업을 적용하시겠습니까?')) {
                return;
            }

            var $btn = $(e.currentTarget);
            $btn.addClass('updating-message').prop('disabled', true);

            $.post(JJ_Multisite.ajax_url, {
                action: '3j_multisite_bulk_action',
                nonce: JJ_Multisite.nonce,
                bulk_action: action,
                site_ids: siteIds
            }, function(response) {
                $btn.removeClass('updating-message').prop('disabled', false);
                if (response.success) {
                    self.showNotice('success', response.data.message);
                    self.refreshSites({ preventDefault: function() {} });
                } else {
                    self.showNotice('error', response.data || JJ_Multisite.i18n.error);
                }
            }).fail(function() {
                $btn.removeClass('updating-message').prop('disabled', false);
                self.showNotice('error', JJ_Multisite.i18n.error);
            });
        },

        /**
         * 동기화 페이지: 모두 선택
         */
        selectAllSyncSites: function(e) {
            e.preventDefault();
            $('.jj-sync-site').prop('checked', true);
        },

        /**
         * 동기화 페이지: 모두 해제
         */
        deselectAllSyncSites: function(e) {
            e.preventDefault();
            $('.jj-sync-site').prop('checked', false);
        },

        /**
         * 동기화 시작
         * @param {Event} e
         */
        startSync: function(e) {
            e.preventDefault();
            var self = this;

            // 선택된 플러그인
            var plugins = [];
            $('.jj-sync-plugin:checked').each(function() {
                plugins.push($(this).val());
            });

            // 선택된 사이트
            var siteIds = [];
            $('.jj-sync-site:checked').each(function() {
                siteIds.push($(this).val());
            });

            if (plugins.length === 0) {
                this.showNotice('error', '동기화할 플러그인을 선택해주세요.');
                return;
            }

            if (siteIds.length === 0) {
                this.showNotice('error', '대상 사이트를 선택해주세요.');
                return;
            }

            if (!confirm(plugins.length + '개 플러그인을 ' + siteIds.length + '개 사이트에 동기화하시겠습니까?')) {
                return;
            }

            // 진행 상황 UI 표시
            var $progress = $('#jj-sync-progress');
            $progress.show();
            $('#jj-progress-total').text(siteIds.length);
            $('#jj-progress-current').text(0);
            $('.jj-progress-fill').css('width', '0%');
            $('#jj-sync-log').empty();

            // 순차적으로 동기화 실행
            var completed = 0;
            var $log = $('#jj-sync-log');

            function syncNext(index) {
                if (index >= siteIds.length) {
                    self.addLogEntry($log, 'info', '동기화 완료!');
                    self.showNotice('success', siteIds.length + '개 사이트 동기화 완료');
                    return;
                }

                var siteId = siteIds[index];
                self.addLogEntry($log, 'info', '사이트 #' + siteId + ' 동기화 중...');

                $.post(JJ_Multisite.ajax_url, {
                    action: '3j_multisite_sync_plugins',
                    nonce: JJ_Multisite.nonce,
                    site_ids: [siteId],
                    plugins: plugins,
                    activate: true
                }, function(response) {
                    completed++;
                    var percent = Math.round((completed / siteIds.length) * 100);

                    $('#jj-progress-current').text(completed);
                    $('.jj-progress-fill').css('width', percent + '%');

                    if (response.success) {
                        var result = response.data.results[siteId];
                        if (result && result.success) {
                            self.addLogEntry($log, 'success', '사이트 #' + siteId + ': ' + result.synced.length + '개 플러그인 동기화 완료');
                        } else {
                            self.addLogEntry($log, 'error', '사이트 #' + siteId + ': 일부 플러그인 동기화 실패');
                        }
                    } else {
                        self.addLogEntry($log, 'error', '사이트 #' + siteId + ': 동기화 실패');
                    }

                    // 다음 사이트 처리
                    setTimeout(function() {
                        syncNext(index + 1);
                    }, 500);
                }).fail(function() {
                    completed++;
                    var percent = Math.round((completed / siteIds.length) * 100);

                    $('#jj-progress-current').text(completed);
                    $('.jj-progress-fill').css('width', percent + '%');

                    self.addLogEntry($log, 'error', '사이트 #' + siteId + ': 네트워크 오류');

                    setTimeout(function() {
                        syncNext(index + 1);
                    }, 500);
                });
            }

            // 동기화 시작
            syncNext(0);
        },

        /**
         * 로그 항목 추가
         * @param {jQuery} $log
         * @param {string} type
         * @param {string} message
         */
        addLogEntry: function($log, type, message) {
            var timestamp = new Date().toLocaleTimeString();
            var prefix = type === 'success' ? '[SUCCESS]' : (type === 'error' ? '[ERROR]' : '[INFO]');
            var html = '<div class="log-entry log-' + type + '">' + timestamp + ' ' + prefix + ' ' + message + '</div>';
            $log.append(html);
            $log.scrollTop($log[0].scrollHeight);
        },

        /**
         * 모달 표시
         * @param {string} title
         * @param {string} content
         */
        showModal: function(title, content) {
            $('#jj-modal-title').text(title);
            $('#jj-modal-body').html(content);
            $('#jj-site-modal').show();
        },

        /**
         * 모달 닫기
         */
        closeModal: function() {
            $('#jj-site-modal').hide();
        },

        /**
         * 알림 표시
         * @param {string} type - 'success' or 'error'
         * @param {string} message
         */
        showNotice: function(type, message) {
            var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            $('.jj-multisite-hub h1').after($notice);

            // 자동 제거
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        MultisiteHub.init();
    });

})(jQuery);
