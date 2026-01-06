/**
 * 3J Labs Unified Dashboard JavaScript
 * Phase 50B - Command Center UI Interactions
 *
 * @package JJ_Analytics_Dashboard
 * @version 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Unified Dashboard Controller
     */
    var UnifiedDashboard = {

        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initAutoRefresh();
        },

        /**
         * Bind Events
         */
        bindEvents: function() {
            // Refresh buttons
            $(document).on('click', '.jj-refresh-btn', this.handleRefresh.bind(this));

            // Quick actions
            $(document).on('click', '.jj-quick-action', this.handleQuickAction.bind(this));

            // Plugin item click
            $(document).on('click', '.jj-plugin-item', this.handlePluginClick.bind(this));
        },

        /**
         * Handle Refresh
         * @param {Event} e
         */
        handleRefresh: function(e) {
            e.preventDefault();
            var $btn = $(e.currentTarget);
            var target = $btn.data('target');

            $btn.addClass('loading');

            switch (target) {
                case 'plugins':
                    this.refreshPlugins($btn);
                    break;
                case 'health':
                    this.refreshHealth($btn);
                    break;
                default:
                    this.refreshOverview($btn);
            }
        },

        /**
         * Refresh Plugins
         * @param {jQuery} $btn
         */
        refreshPlugins: function($btn) {
            var self = this;

            $.post(JJ_Unified.ajax_url, {
                action: '3j_unified_get_overview',
                nonce: JJ_Unified.nonce
            }, function(response) {
                $btn.removeClass('loading');
                if (response.success) {
                    self.updatePluginsList(response.data.plugins);
                    self.updateStats(response.data);
                } else {
                    self.showNotice('error', response.data || JJ_Unified.i18n.error);
                }
            }).fail(function() {
                $btn.removeClass('loading');
                self.showNotice('error', JJ_Unified.i18n.error);
            });
        },

        /**
         * Refresh Health
         * @param {jQuery} $btn
         */
        refreshHealth: function($btn) {
            var self = this;

            $.post(JJ_Unified.ajax_url, {
                action: '3j_unified_get_health',
                nonce: JJ_Unified.nonce
            }, function(response) {
                $btn.removeClass('loading');
                if (response.success) {
                    self.updateHealthStatus(response.data);
                } else {
                    self.showNotice('error', response.data || JJ_Unified.i18n.error);
                }
            }).fail(function() {
                $btn.removeClass('loading');
                self.showNotice('error', JJ_Unified.i18n.error);
            });
        },

        /**
         * Refresh Overview
         * @param {jQuery} $btn
         */
        refreshOverview: function($btn) {
            var self = this;

            $.post(JJ_Unified.ajax_url, {
                action: '3j_unified_get_overview',
                nonce: JJ_Unified.nonce
            }, function(response) {
                $btn.removeClass('loading');
                if (response.success) {
                    self.updatePluginsList(response.data.plugins);
                    self.updateHealthStatus(response.data.health);
                    self.updateStats(response.data);
                    self.updateActivity(response.data.recent_activity);
                } else {
                    self.showNotice('error', response.data || JJ_Unified.i18n.error);
                }
            }).fail(function() {
                $btn.removeClass('loading');
                self.showNotice('error', JJ_Unified.i18n.error);
            });
        },

        /**
         * Update Plugins List
         * @param {Array} plugins
         */
        updatePluginsList: function(plugins) {
            var $list = $('#jj-plugins-list');
            if (!$list.length || !plugins) return;

            var html = '';
            plugins.forEach(function(plugin) {
                var statusClass = plugin.active ? 'active' : 'inactive';
                var statusText = plugin.active ? JJ_Unified.i18n.active : JJ_Unified.i18n.inactive;

                html += '<div class="jj-plugin-item jj-plugin-' + statusClass + '" data-slug="' + plugin.slug + '">';
                html += '<div class="jj-plugin-info">';
                html += '<span class="jj-plugin-name">' + plugin.name + '</span>';
                html += '<span class="jj-plugin-version">v' + plugin.version + '</span>';
                html += '</div>';
                html += '<div class="jj-plugin-meta">';
                html += '<span class="jj-plugin-category">' + plugin.category + '</span>';
                html += '<span class="jj-plugin-status jj-status-' + statusClass + '">' + statusText + '</span>';
                html += '</div>';
                html += '</div>';
            });

            $list.html(html);
        },

        /**
         * Update Health Status
         * @param {Object} health
         */
        updateHealthStatus: function(health) {
            var $status = $('#jj-health-status');
            if (!$status.length || !health) return;

            var html = '';

            // Health items
            var items = [
                { label: 'PHP 버전', value: health.php_version, status: parseFloat(health.php_version) >= 7.4 ? 'good' : 'warning' },
                { label: 'WordPress 버전', value: health.wp_version, status: 'good' },
                { label: '메모리 사용', value: health.memory_usage, status: health.memory_status },
                { label: '데이터베이스', value: health.db_version, status: 'good' }
            ];

            items.forEach(function(item) {
                html += '<div class="jj-health-item">';
                html += '<span class="jj-health-label">' + item.label + '</span>';
                html += '<span class="jj-health-value">' + item.value + '</span>';
                html += '<span class="jj-health-indicator jj-indicator-' + item.status + '"></span>';
                html += '</div>';
            });

            // Issues
            if (health.issues && health.issues.length > 0) {
                html += '<div class="jj-health-issues">';
                html += '<h4>발견된 문제</h4>';
                health.issues.forEach(function(issue) {
                    html += '<div class="jj-issue jj-issue-' + issue.type + '">' + issue.message + '</div>';
                });
                html += '</div>';
            }

            $status.html(html);

            // Update stat card
            var $statHealth = $('#stat-health');
            if ($statHealth.length) {
                $statHealth.removeClass('jj-health-healthy jj-health-warning jj-health-critical')
                    .addClass('jj-health-' + health.status)
                    .text(health.status.charAt(0).toUpperCase() + health.status.slice(1));
            }
        },

        /**
         * Update Stats
         * @param {Object} data
         */
        updateStats: function(data) {
            if (data.total !== undefined) {
                $('#stat-total').text(data.total);
            }
            if (data.active !== undefined) {
                $('#stat-active').text(data.active);
            }
            if (data.events_today !== undefined) {
                $('#stat-events').text(data.events_today);
            }
        },

        /**
         * Update Activity
         * @param {Array} activities
         */
        updateActivity: function(activities) {
            var $activity = $('#jj-recent-activity');
            if (!$activity.length || !activities) return;

            if (activities.length === 0) {
                $activity.html('<p class="jj-empty">최근 활동이 없습니다.</p>');
                return;
            }

            var html = '<ul class="jj-activity-list">';
            var now = Math.floor(Date.now() / 1000);

            activities.slice(0, 10).forEach(function(activity) {
                var timeAgo = this.timeAgo(activity.timestamp, now);
                var icon = this.getActivityIcon(activity.type);
                var text = this.getActivityText(activity);

                html += '<li class="jj-activity-item">';
                html += '<span class="jj-activity-icon dashicons ' + icon + '"></span>';
                html += '<span class="jj-activity-text">' + text + '</span>';
                html += '<span class="jj-activity-time">' + timeAgo + '</span>';
                html += '</li>';
            }.bind(this));

            html += '</ul>';
            $activity.html(html);
        },

        /**
         * Get Activity Icon
         * @param {string} type
         * @returns {string}
         */
        getActivityIcon: function(type) {
            var icons = {
                'plugin_activated': 'dashicons-yes',
                'plugin_deactivated': 'dashicons-no',
                'plugin_updated': 'dashicons-update',
                'plugin_status_changed': 'dashicons-admin-plugins',
                'settings_changed': 'dashicons-admin-generic',
                'check_updates': 'dashicons-update',
                'clear_cache': 'dashicons-trash'
            };
            return icons[type] || 'dashicons-marker';
        },

        /**
         * Get Activity Text
         * @param {Object} activity
         * @returns {string}
         */
        getActivityText: function(activity) {
            var texts = {
                'plugin_activated': '플러그인 활성화됨',
                'plugin_deactivated': '플러그인 비활성화됨',
                'plugin_updated': '플러그인 업데이트됨',
                'plugin_status_changed': '플러그인 상태 변경',
                'settings_changed': '설정 변경됨',
                'check_updates': '업데이트 확인',
                'clear_cache': '캐시 초기화'
            };

            var text = texts[activity.type] || activity.type;

            if (activity.data && activity.data.plugin) {
                text += ': ' + activity.data.plugin;
            }

            return text;
        },

        /**
         * Time Ago
         * @param {number} timestamp
         * @param {number} now
         * @returns {string}
         */
        timeAgo: function(timestamp, now) {
            var diff = now - timestamp;

            if (diff < 60) {
                return '방금';
            } else if (diff < 3600) {
                return Math.floor(diff / 60) + '분 전';
            } else if (diff < 86400) {
                return Math.floor(diff / 3600) + '시간 전';
            } else {
                return Math.floor(diff / 86400) + '일 전';
            }
        },

        /**
         * Handle Quick Action
         * @param {Event} e
         */
        handleQuickAction: function(e) {
            e.preventDefault();
            var $btn = $(e.currentTarget);
            var action = $btn.data('action');

            if (!action) return;

            // Confirmation for destructive actions
            if (action === 'clear_cache') {
                if (!confirm(JJ_Unified.i18n.confirm_action)) {
                    return;
                }
            }

            $btn.prop('disabled', true);
            var self = this;

            $.post(JJ_Unified.ajax_url, {
                action: '3j_unified_quick_action',
                nonce: JJ_Unified.nonce,
                quick_action: action
            }, function(response) {
                $btn.prop('disabled', false);

                if (response.success) {
                    if (typeof response.data === 'string') {
                        self.showNotice('success', response.data);
                    } else if (action === 'export_settings') {
                        // Download settings as JSON
                        self.downloadJSON(response.data, '3j-labs-settings.json');
                    }

                    // Refresh overview after certain actions
                    if (['check_updates', 'clear_cache'].indexOf(action) !== -1) {
                        setTimeout(function() {
                            self.refreshOverview($('.jj-refresh-btn').first());
                        }, 500);
                    }
                } else {
                    self.showNotice('error', response.data || JJ_Unified.i18n.error);
                }
            }).fail(function() {
                $btn.prop('disabled', false);
                self.showNotice('error', JJ_Unified.i18n.error);
            });
        },

        /**
         * Handle Plugin Click
         * @param {Event} e
         */
        handlePluginClick: function(e) {
            var $item = $(e.currentTarget);
            var slug = $item.data('slug');

            if (!slug) return;

            // Show plugin details modal (future enhancement)
            console.log('Plugin clicked:', slug);
        },

        /**
         * Download JSON
         * @param {Object} data
         * @param {string} filename
         */
        downloadJSON: function(data, filename) {
            var blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        },

        /**
         * Show Notice
         * @param {string} type - 'success' or 'error'
         * @param {string} message
         */
        showNotice: function(type, message) {
            var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            $('.jj-unified-dashboard h1').after($notice);

            // Auto dismiss
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },

        /**
         * Initialize Auto Refresh
         */
        initAutoRefresh: function() {
            var self = this;
            var refreshInterval = 60000; // 1 minute

            // Auto refresh in background
            setInterval(function() {
                if (document.hidden) return;

                $.post(JJ_Unified.ajax_url, {
                    action: '3j_unified_get_overview',
                    nonce: JJ_Unified.nonce
                }, function(response) {
                    if (response.success) {
                        self.updateStats(response.data);
                    }
                });
            }, refreshInterval);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        UnifiedDashboard.init();
    });

})(jQuery);
