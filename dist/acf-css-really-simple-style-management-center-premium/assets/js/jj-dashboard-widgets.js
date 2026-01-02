/**
 * JJ Dashboard Widgets
 * 
 * [Phase 9.2] 대시보드 위젯 및 개인화 기능
 * 
 * @since 9.2.0
 */

(function($) {
    'use strict';
    
    const DashboardWidgets = {
        init: function() {
            this.renderWidgets();
            this.bindEvents();
            this.loadRecentActivity();
        },
        
        renderWidgets: function() {
            // 대시보드 위젯 컨테이너가 없으면 생성
            if ($('#jj-dashboard-widgets').length === 0) {
                const widgetsHtml = `
                    <div id="jj-dashboard-widgets" class="jj-dashboard-widgets-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">
                        <div class="jj-widget jj-widget-recent-activity" style="background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); padding: 15px;">
                            <h3 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600;">
                                <span class="dashicons dashicons-clock" style="vertical-align: middle; margin-right: 5px;"></span>
                                ${jjDashboardWidgets.strings.recent_activity}
                            </h3>
                            <div class="jj-recent-activity-list" style="min-height: 100px;">
                                <p style="color: #666; font-style: italic;">${jjDashboardWidgets.strings.no_activity}</p>
                            </div>
                        </div>
                        
                        <div class="jj-widget jj-widget-quick-actions" style="background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); padding: 15px;">
                            <h3 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600;">
                                <span class="dashicons dashicons-admin-generic" style="vertical-align: middle; margin-right: 5px;"></span>
                                ${jjDashboardWidgets.strings.quick_actions}
                            </h3>
                            <div class="jj-quick-actions-list">
                                ${this.renderQuickActions()}
                            </div>
                        </div>
                        
                        <div class="jj-widget jj-widget-statistics" style="background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); padding: 15px;">
                            <h3 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600;">
                                <span class="dashicons dashicons-chart-bar" style="vertical-align: middle; margin-right: 5px;"></span>
                                ${jjDashboardWidgets.strings.statistics}
                            </h3>
                            <div class="jj-statistics-list">
                                ${this.renderStatistics()}
                            </div>
                        </div>
                    </div>
                `;
                
                // Admin Center 탭 컨텐츠 앞에 삽입
                $('.jj-admin-center-tab-content').first().before(widgetsHtml);
            }
        },
        
        renderQuickActions: function() {
            const actions = [
                {
                    label: jjDashboardWidgets.strings.quick_actions,
                    url: jjAdminCenter?.style_guide_url || '#',
                    icon: 'dashicons-plus-alt'
                }
            ];
            
            return actions.map(action => `
                <a href="${action.url}" class="button button-secondary" style="display: block; margin-bottom: 10px; text-align: left;">
                    <span class="dashicons ${action.icon}" style="vertical-align: middle; margin-right: 5px;"></span>
                    ${action.label}
                </a>
            `).join('');
        },
        
        renderStatistics: function() {
            // 통계는 서버에서 가져와야 하므로 기본 구조만 제공
            return '<p style="color: #666;">통계 데이터 로딩 중...</p>';
        },
        
        loadRecentActivity: function() {
            $.ajax({
                url: jjDashboardWidgets.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_get_recent_activity',
                    nonce: jjDashboardWidgets.nonce,
                    limit: 5
                },
                success: function(response) {
                    if (response.success && response.data.activities) {
                        DashboardWidgets.renderRecentActivity(response.data.activities);
                    }
                },
                error: function() {
                    console.error('최근 활동 로드 실패');
                }
            });
        },
        
        renderRecentActivity: function(activities) {
            if (!activities || activities.length === 0) {
                $('.jj-recent-activity-list').html(`<p style="color: #666; font-style: italic;">${jjDashboardWidgets.strings.no_activity}</p>`);
                return;
            }
            
            const activitiesHtml = activities.map(activity => `
                <div style="padding: 8px 0; border-bottom: 1px solid #eee;">
                    <div style="font-size: 13px; color: #333;">${activity.message || activity.action}</div>
                    <div style="font-size: 11px; color: #999; margin-top: 4px;">
                        ${activity.time_formatted || '방금 전'}
                    </div>
                </div>
            `).join('');
            
            $('.jj-recent-activity-list').html(activitiesHtml);
        },
        
        bindEvents: function() {
            // 즐겨찾기 토글
            $(document).on('click', '.jj-favorite-toggle', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const itemId = $btn.data('item-id');
                const itemType = $btn.data('item-type');
                
                $.ajax({
                    url: jjDashboardWidgets.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'jj_toggle_favorite',
                        nonce: jjDashboardWidgets.nonce,
                        item_id: itemId,
                        item_type: itemType
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.data.added) {
                                $btn.addClass('is-favorite').attr('title', jjDashboardWidgets.strings.remove_favorite);
                            } else {
                                $btn.removeClass('is-favorite').attr('title', jjDashboardWidgets.strings.add_favorite);
                            }
                            if (JJUtils && JJUtils.showToast) {
                                JJUtils.showToast(response.data.message, 'success');
                            }
                        }
                    }
                });
            });
        }
    };
    
    // 초기화
    $(document).ready(function() {
        if (typeof jjDashboardWidgets !== 'undefined') {
            DashboardWidgets.init();
        }
    });
    
})(jQuery);
