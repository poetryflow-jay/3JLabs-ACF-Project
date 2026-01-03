<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Maintenance & Conflict View
 * - Rollback history
 * - Conflict detection
 */

$history = JJ_History_Manager::instance()->get_history();
$conflicts = JJ_Conflict_Detector::instance()->run_diagnosis();
?>
<div class="jj-maintenance-container" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-top: 30px;">
    <h2 style="margin-bottom: 20px;"><?php _e( '🔧 유지보수 및 보안', 'acf-css-really-simple-style-management-center' ); ?></h2>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <!-- Conflict Detector -->
        <div class="jj-maintenance-section">
            <h3 style="font-size: 16px; color: #1e293b; margin-bottom: 15px;">디자인 충돌 감지</h3>
            <?php if ( empty($conflicts) ) : ?>
                <div class="notice notice-success inline" style="margin: 0; padding: 10px; border-radius: 8px;">
                    <p style="margin: 0;">✅ 감지된 충돌이 없습니다. 디자인 시스템이 최상의 상태입니다.</p>
                </div>
            <?php else : ?>
                <?php foreach($conflicts as $issue) : ?>
                    <div class="jj-conflict-card" style="background: #fff9eb; border: 1px solid #fcd34d; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                        <div style="font-weight: 700; color: #92400e;"><?php echo esc_html($issue['title']); ?></div>
                        <div style="font-size: 13px; color: #78350f; margin: 5px 0;"><?php echo esc_html($issue['message']); ?></div>
                        <div style="font-size: 12px; color: #b45309; font-style: italic;">해결책: <?php echo esc_html($issue['fix']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- History / Rollback -->
        <div class="jj-maintenance-section">
            <h3 style="font-size: 16px; color: #1e293b; margin-bottom: 15px;">설정 변경 히스토리 (최근 10개)</h3>
            <div class="jj-history-list" style="max-height: 250px; overflow-y: auto; border: 1px solid #f1f5f9; border-radius: 8px;">
                <?php if ( empty($history) ) : ?>
                    <p style="padding: 20px; text-align: center; color: #94a3b8;">아직 변경 기록이 없습니다.</p>
                <?php else : ?>
                    <?php foreach($history as $item) : ?>
                        <div class="jj-history-item" style="padding: 12px 15px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; font-size: 13px;"><?php echo esc_html($item['reason']); ?></div>
                                <div style="font-size: 11px; color: #94a3b8;"><?php echo esc_html($item['timestamp']); ?></div>
                            </div>
                            <button type="button" class="button button-small jj-rollback-btn" data-id="<?php echo esc_attr($item['id']); ?>">롤백</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.jj-rollback-btn').on('click', function() {
        var id = $(this).data('id');
        if (!confirm('정말로 이 시점으로 모든 설정을 되돌리시겠습니까?')) return;

        $(this).prop('disabled', true).text('복구 중...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jj_rollback_settings',
                security: jj_admin_params.nonce,
                snapshot_id: id
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});
</script>
