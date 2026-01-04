/**
 * WP Bulk SEO Master - Admin JavaScript
 *
 * @package WP_Bulk_SEO_Master
 * @version 1.0.0
 */

(function($) {
    'use strict';

    /**
     * SEO Master Admin Object
     */
    const SEOMasterAdmin = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initCharts();
            this.initTabs();
            this.initModals();
            this.initClipboard();
            this.initTooltips();
        },

        /**
         * Bind Events
         */
        bindEvents: function() {
            // License generation
            $(document).on('click', '.seo-generate-license', this.generateLicense.bind(this));
            $(document).on('click', '.seo-revoke-license', this.revokeLicense.bind(this));
            $(document).on('click', '.seo-copy-license', this.copyLicense.bind(this));

            // Site management
            $(document).on('click', '.seo-add-site', this.showAddSiteModal.bind(this));
            $(document).on('click', '.seo-remove-site', this.removeSite.bind(this));
            $(document).on('click', '.seo-sync-site', this.syncSite.bind(this));
            $(document).on('click', '.seo-view-snippet', this.viewSnippet.bind(this));

            // Rank tracker
            $(document).on('click', '.seo-add-keyword', this.showAddKeywordModal.bind(this));
            $(document).on('click', '.seo-remove-keyword', this.removeKeyword.bind(this));
            $(document).on('click', '.seo-check-rank', this.checkRank.bind(this));
            $(document).on('click', '.seo-view-history', this.viewRankHistory.bind(this));

            // Settings
            $(document).on('submit', '#seo-settings-form', this.saveSettings.bind(this));

            // Search and filters
            $(document).on('input', '.seo-search-input', this.debounce(this.handleSearch.bind(this), 300));
            $(document).on('change', '.seo-filter-select', this.handleFilter.bind(this));

            // Bulk actions
            $(document).on('click', '.seo-select-all', this.toggleSelectAll.bind(this));
            $(document).on('click', '.seo-bulk-action', this.doBulkAction.bind(this));
        },

        /**
         * Initialize Charts (Chart.js)
         */
        initCharts: function() {
            // Rank History Chart
            if ($('#rankHistoryChart').length && typeof Chart !== 'undefined') {
                this.initRankHistoryChart();
            }

            // Sites Overview Chart
            if ($('#sitesOverviewChart').length && typeof Chart !== 'undefined') {
                this.initSitesOverviewChart();
            }

            // License Distribution Chart
            if ($('#licenseDistChart').length && typeof Chart !== 'undefined') {
                this.initLicenseDistChart();
            }
        },

        /**
         * Init Rank History Chart
         */
        initRankHistoryChart: function() {
            const ctx = document.getElementById('rankHistoryChart').getContext('2d');
            const data = window.seoRankData || {
                labels: [],
                datasets: []
            };

            new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            reverse: true,
                            min: 1,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return '#' + value;
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        },

        /**
         * Init Sites Overview Chart
         */
        initSitesOverviewChart: function() {
            const ctx = document.getElementById('sitesOverviewChart').getContext('2d');
            const data = window.seoSitesData || {
                labels: ['WordPress', 'Webflow', 'Framer', 'Ghost', 'Other'],
                datasets: [{
                    data: [0, 0, 0, 0, 0],
                    backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#6b7280']
                }]
            };

            new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        },

        /**
         * Init License Distribution Chart
         */
        initLicenseDistChart: function() {
            const ctx = document.getElementById('licenseDistChart').getContext('2d');
            const data = window.seoLicenseData || {
                labels: ['Starter', 'Professional', 'Agency', 'Enterprise'],
                datasets: [{
                    data: [0, 0, 0, 0],
                    backgroundColor: ['#6b7280', '#2563eb', '#10b981', '#f59e0b']
                }]
            };

            new Chart(ctx, {
                type: 'pie',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        },

        /**
         * Initialize Tabs
         */
        initTabs: function() {
            $('.seo-tab').on('click', function() {
                const target = $(this).data('tab');

                // Update tab buttons
                $(this).siblings().removeClass('active');
                $(this).addClass('active');

                // Update tab content
                $(this).closest('.seo-tabs-container')
                    .find('.seo-tab-content')
                    .removeClass('active');
                $('#' + target).addClass('active');
            });
        },

        /**
         * Initialize Modals
         */
        initModals: function() {
            // Open modal
            $(document).on('click', '[data-modal]', function(e) {
                e.preventDefault();
                const modalId = $(this).data('modal');
                $('#' + modalId).addClass('active');
            });

            // Close modal
            $(document).on('click', '.seo-modal-close, .seo-modal-overlay', function(e) {
                if (e.target === this) {
                    $(this).closest('.seo-modal-overlay').removeClass('active');
                }
            });

            // Close on escape
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('.seo-modal-overlay.active').removeClass('active');
                }
            });
        },

        /**
         * Initialize Clipboard
         */
        initClipboard: function() {
            $(document).on('click', '.copy-btn, .seo-copy-btn', function() {
                const $btn = $(this);
                const text = $btn.data('copy') || $btn.siblings('code').text();

                navigator.clipboard.writeText(text).then(function() {
                    const originalText = $btn.text();
                    $btn.text('Copied!').addClass('copied');

                    setTimeout(function() {
                        $btn.text(originalText).removeClass('copied');
                    }, 2000);
                });
            });
        },

        /**
         * Initialize Tooltips
         */
        initTooltips: function() {
            // Simple tooltip on hover
            $(document).on('mouseenter', '[data-tooltip]', function() {
                const text = $(this).data('tooltip');
                const $tooltip = $('<div class="seo-tooltip-popup">' + text + '</div>');

                $('body').append($tooltip);

                const rect = this.getBoundingClientRect();
                $tooltip.css({
                    position: 'fixed',
                    top: rect.top - $tooltip.outerHeight() - 8,
                    left: rect.left + (rect.width / 2) - ($tooltip.outerWidth() / 2),
                    background: '#1f2937',
                    color: '#fff',
                    padding: '6px 12px',
                    borderRadius: '6px',
                    fontSize: '12px',
                    zIndex: 100001
                });
            });

            $(document).on('mouseleave', '[data-tooltip]', function() {
                $('.seo-tooltip-popup').remove();
            });
        },

        /**
         * Generate License
         */
        generateLicense: function(e) {
            e.preventDefault();

            const $form = $('#generate-license-form');
            const data = {
                action: 'seo_master_generate_license',
                nonce: seoMasterAdmin.nonce,
                license_type: $form.find('[name="license_type"]').val(),
                email: $form.find('[name="email"]').val(),
                expires_days: $form.find('[name="expires_days"]').val()
            };

            this.showLoading($form);

            $.post(ajaxurl, data, function(response) {
                if (response.success) {
                    SEOMasterAdmin.showNotice('success', response.data.message);
                    SEOMasterAdmin.hideModal('generate-license-modal');
                    location.reload();
                } else {
                    SEOMasterAdmin.showNotice('error', response.data.message);
                }
            }).always(function() {
                SEOMasterAdmin.hideLoading($form);
            });
        },

        /**
         * Revoke License
         */
        revokeLicense: function(e) {
            e.preventDefault();

            if (!confirm(seoMasterAdmin.strings.confirmRevoke)) {
                return;
            }

            const $btn = $(e.currentTarget);
            const licenseId = $btn.data('id');

            const data = {
                action: 'seo_master_revoke_license',
                nonce: seoMasterAdmin.nonce,
                license_id: licenseId
            };

            $btn.prop('disabled', true);

            $.post(ajaxurl, data, function(response) {
                if (response.success) {
                    $btn.closest('.seo-license-card').fadeOut(function() {
                        $(this).remove();
                    });
                    SEOMasterAdmin.showNotice('success', response.data.message);
                } else {
                    SEOMasterAdmin.showNotice('error', response.data.message);
                }
            }).always(function() {
                $btn.prop('disabled', false);
            });
        },

        /**
         * Copy License Key
         */
        copyLicense: function(e) {
            e.preventDefault();
            const licenseKey = $(e.currentTarget).data('key');

            navigator.clipboard.writeText(licenseKey).then(function() {
                SEOMasterAdmin.showNotice('success', seoMasterAdmin.strings.copied);
            });
        },

        /**
         * Show Add Site Modal
         */
        showAddSiteModal: function(e) {
            e.preventDefault();
            $('#add-site-modal').addClass('active');
        },

        /**
         * Remove Site
         */
        removeSite: function(e) {
            e.preventDefault();

            if (!confirm(seoMasterAdmin.strings.confirmRemove)) {
                return;
            }

            const $btn = $(e.currentTarget);
            const siteId = $btn.data('id');

            const data = {
                action: 'seo_master_remove_site',
                nonce: seoMasterAdmin.nonce,
                site_id: siteId
            };

            $btn.prop('disabled', true);

            $.post(ajaxurl, data, function(response) {
                if (response.success) {
                    $btn.closest('.seo-site-card').fadeOut(function() {
                        $(this).remove();
                    });
                    SEOMasterAdmin.showNotice('success', response.data.message);
                } else {
                    SEOMasterAdmin.showNotice('error', response.data.message);
                }
            }).always(function() {
                $btn.prop('disabled', false);
            });
        },

        /**
         * Sync Site
         */
        syncSite: function(e) {
            e.preventDefault();

            const $btn = $(e.currentTarget);
            const siteId = $btn.data('id');

            const data = {
                action: 'seo_master_sync_site',
                nonce: seoMasterAdmin.nonce,
                site_id: siteId
            };

            $btn.prop('disabled', true).addClass('syncing');

            $.post(ajaxurl, data, function(response) {
                if (response.success) {
                    SEOMasterAdmin.showNotice('success', response.data.message);
                    // Update site card with new data
                    if (response.data.site) {
                        SEOMasterAdmin.updateSiteCard(siteId, response.data.site);
                    }
                } else {
                    SEOMasterAdmin.showNotice('error', response.data.message);
                }
            }).always(function() {
                $btn.prop('disabled', false).removeClass('syncing');
            });
        },

        /**
         * View Snippet
         */
        viewSnippet: function(e) {
            e.preventDefault();

            const siteId = $(e.currentTarget).data('id');
            const $modal = $('#snippet-modal');

            // Fetch snippet
            const data = {
                action: 'seo_master_get_snippet',
                nonce: seoMasterAdmin.nonce,
                site_id: siteId
            };

            $.post(ajaxurl, data, function(response) {
                if (response.success) {
                    $modal.find('.seo-code-block code').text(response.data.snippet);
                    $modal.addClass('active');
                } else {
                    SEOMasterAdmin.showNotice('error', response.data.message);
                }
            });
        },

        /**
         * Show Add Keyword Modal
         */
        showAddKeywordModal: function(e) {
            e.preventDefault();
            $('#add-keyword-modal').addClass('active');
        },

        /**
         * Remove Keyword
         */
        removeKeyword: function(e) {
            e.preventDefault();

            if (!confirm(seoMasterAdmin.strings.confirmRemoveKeyword)) {
                return;
            }

            const $btn = $(e.currentTarget);
            const keywordId = $btn.data('id');

            const data = {
                action: 'seo_master_remove_keyword',
                nonce: seoMasterAdmin.nonce,
                keyword_id: keywordId
            };

            $btn.prop('disabled', true);

            $.post(ajaxurl, data, function(response) {
                if (response.success) {
                    $btn.closest('tr').fadeOut(function() {
                        $(this).remove();
                    });
                    SEOMasterAdmin.showNotice('success', response.data.message);
                } else {
                    SEOMasterAdmin.showNotice('error', response.data.message);
                }
            }).always(function() {
                $btn.prop('disabled', false);
            });
        },

        /**
         * Check Rank
         */
        checkRank: function(e) {
            e.preventDefault();

            const $btn = $(e.currentTarget);
            const keywordId = $btn.data('id');

            const data = {
                action: 'seo_master_check_rank',
                nonce: seoMasterAdmin.nonce,
                keyword_id: keywordId
            };

            $btn.prop('disabled', true).addClass('checking');

            $.post(ajaxurl, data, function(response) {
                if (response.success) {
                    SEOMasterAdmin.showNotice('success', response.data.message);
                    // Update rank display
                    if (response.data.rank) {
                        SEOMasterAdmin.updateRankDisplay(keywordId, response.data.rank);
                    }
                } else {
                    SEOMasterAdmin.showNotice('error', response.data.message);
                }
            }).always(function() {
                $btn.prop('disabled', false).removeClass('checking');
            });
        },

        /**
         * View Rank History
         */
        viewRankHistory: function(e) {
            e.preventDefault();

            const keywordId = $(e.currentTarget).data('id');
            const $modal = $('#rank-history-modal');

            // Fetch history
            const data = {
                action: 'seo_master_get_rank_history',
                nonce: seoMasterAdmin.nonce,
                keyword_id: keywordId
            };

            $.post(ajaxurl, data, function(response) {
                if (response.success) {
                    // Render history chart
                    SEOMasterAdmin.renderRankHistoryChart(response.data.history);
                    $modal.addClass('active');
                } else {
                    SEOMasterAdmin.showNotice('error', response.data.message);
                }
            });
        },

        /**
         * Save Settings
         */
        saveSettings: function(e) {
            e.preventDefault();

            const $form = $(e.currentTarget);
            const data = $form.serialize() + '&action=seo_master_save_settings&nonce=' + seoMasterAdmin.nonce;

            this.showLoading($form);

            $.post(ajaxurl, data, function(response) {
                if (response.success) {
                    SEOMasterAdmin.showNotice('success', response.data.message);
                } else {
                    SEOMasterAdmin.showNotice('error', response.data.message);
                }
            }).always(function() {
                SEOMasterAdmin.hideLoading($form);
            });
        },

        /**
         * Handle Search
         */
        handleSearch: function(e) {
            const query = $(e.currentTarget).val().toLowerCase();
            const $container = $(e.currentTarget).closest('.seo-panel').find('.seo-searchable');

            $container.each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(query) > -1);
            });
        },

        /**
         * Handle Filter
         */
        handleFilter: function(e) {
            const value = $(e.currentTarget).val();
            const filterType = $(e.currentTarget).data('filter');
            const $container = $(e.currentTarget).closest('.seo-panel').find('.seo-filterable');

            $container.each(function() {
                if (!value || $(this).data(filterType) === value) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        },

        /**
         * Toggle Select All
         */
        toggleSelectAll: function(e) {
            const checked = $(e.currentTarget).prop('checked');
            $(e.currentTarget).closest('table').find('.seo-checkbox-item').prop('checked', checked);
        },

        /**
         * Do Bulk Action
         */
        doBulkAction: function(e) {
            e.preventDefault();

            const action = $(e.currentTarget).siblings('.seo-bulk-select').val();
            const ids = [];

            $('.seo-checkbox-item:checked').each(function() {
                ids.push($(this).val());
            });

            if (!action) {
                this.showNotice('error', 'Please select an action.');
                return;
            }

            if (ids.length === 0) {
                this.showNotice('error', 'Please select at least one item.');
                return;
            }

            if (action === 'delete' && !confirm('Are you sure you want to delete the selected items?')) {
                return;
            }

            const data = {
                action: 'seo_master_bulk_action',
                nonce: seoMasterAdmin.nonce,
                bulk_action: action,
                ids: ids
            };

            $.post(ajaxurl, data, function(response) {
                if (response.success) {
                    SEOMasterAdmin.showNotice('success', response.data.message);
                    location.reload();
                } else {
                    SEOMasterAdmin.showNotice('error', response.data.message);
                }
            });
        },

        /**
         * Update Site Card
         */
        updateSiteCard: function(siteId, site) {
            const $card = $('.seo-site-card[data-id="' + siteId + '"]');
            if ($card.length) {
                $card.find('.seo-score').text(site.score);
                $card.find('.pages-count').text(site.pages);
                $card.find('.last-sync').text(site.last_sync);
            }
        },

        /**
         * Update Rank Display
         */
        updateRankDisplay: function(keywordId, rank) {
            const $row = $('tr[data-keyword-id="' + keywordId + '"]');
            if ($row.length) {
                const $position = $row.find('.seo-rank-position');
                $position.text(rank.position);

                // Update class based on position
                $position.removeClass('top-3 top-10 top-20 below-20');
                if (rank.position <= 3) {
                    $position.addClass('top-3');
                } else if (rank.position <= 10) {
                    $position.addClass('top-10');
                } else if (rank.position <= 20) {
                    $position.addClass('top-20');
                } else {
                    $position.addClass('below-20');
                }

                // Update change
                const $change = $row.find('.seo-rank-change');
                $change.removeClass('up down stable');
                if (rank.change > 0) {
                    $change.addClass('up').html('&#9650; ' + Math.abs(rank.change));
                } else if (rank.change < 0) {
                    $change.addClass('down').html('&#9660; ' + Math.abs(rank.change));
                } else {
                    $change.addClass('stable').html('&#8212;');
                }
            }
        },

        /**
         * Render Rank History Chart
         */
        renderRankHistoryChart: function(history) {
            const ctx = document.getElementById('modalRankChart');
            if (!ctx) return;

            // Destroy existing chart
            if (this.modalChart) {
                this.modalChart.destroy();
            }

            const labels = history.map(h => h.date);
            const data = history.map(h => h.position);

            this.modalChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Position',
                        data: data,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            reverse: true,
                            min: 1
                        }
                    }
                }
            });
        },

        /**
         * Show Notice
         */
        showNotice: function(type, message) {
            const $notice = $('<div class="seo-alert seo-alert-' + type + '">' +
                '<div class="seo-alert-content">' + message + '</div>' +
                '</div>');

            $('.seo-master-wrap').prepend($notice);

            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },

        /**
         * Show Loading
         */
        showLoading: function($container) {
            $container.addClass('seo-loading-state');
            $container.find('button[type="submit"]').prop('disabled', true);
        },

        /**
         * Hide Loading
         */
        hideLoading: function($container) {
            $container.removeClass('seo-loading-state');
            $container.find('button[type="submit"]').prop('disabled', false);
        },

        /**
         * Hide Modal
         */
        hideModal: function(modalId) {
            $('#' + modalId).removeClass('active');
        },

        /**
         * Debounce Utility
         */
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func.apply(this, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        SEOMasterAdmin.init();
    });

    // Expose globally
    window.SEOMasterAdmin = SEOMasterAdmin;

})(jQuery);
