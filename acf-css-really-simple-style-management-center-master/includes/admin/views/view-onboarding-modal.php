<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Onboarding Welcome Modal
 * - Jenny's Marketing Strategy: emotional connection + quick start
 */
?>
<div id="jj-onboarding-modal" style="display: none; position: fixed; z-index: 999999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div class="jj-modal-content" style="background: #fff; padding: 40px; border-radius: 20px; max-width: 600px; width: 90%; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); text-align: center;">
        <span class="jj-modal-close dashicons dashicons-no-alt" style="position: absolute; right: 20px; top: 20px; cursor: pointer; color: #94a3b8; font-size: 24px;"></span>
        
        <div class="jj-welcome-icon" style="font-size: 60px; margin-bottom: 20px;">🎨</div>
        <h2 style="font-size: 28px; color: #1e293b; margin-bottom: 15px;"><?php _e( '3J Labs에 오신 것을 환영합니다!', 'acf-css-really-simple-style-management-center' ); ?></h2>
        <p style="font-size: 16px; color: #64748b; line-height: 1.6; margin-bottom: 30px;">
            <?php _e( 'ACF CSS는 단순한 플러그인을 넘어, 당신의 웹사이트에 생명력을 불어넣는 디자인 시스템입니다. 복잡한 설정 없이도 전문가의 감성을 그대로 느껴보세요.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>

        <div class="jj-onboarding-steps" style="display: flex; justify-content: space-between; margin-bottom: 40px; gap: 15px;">
            <div class="step" style="flex: 1;">
                <div style="font-weight: 700; color: #3b82f6; margin-bottom: 5px;">Step 1</div>
                <div style="font-size: 13px;"><?php _e( '프리셋 선택', 'acf-css-really-simple-style-management-center' ); ?></div>
            </div>
            <div style="align-self: center; color: #e2e8f0;">➜</div>
            <div class="step" style="flex: 1;">
                <div style="font-weight: 700; color: #3b82f6; margin-bottom: 5px;">Step 2</div>
                <div style="font-size: 13px;"><?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?></div>
            </div>
            <div style="align-self: center; color: #e2e8f0;">➜</div>
            <div class="step" style="flex: 1;">
                <div style="font-weight: 700; color: #3b82f6; margin-bottom: 5px;">Step 3</div>
                <div style="font-size: 13px;"><?php _e( '실시간 적용', 'acf-css-really-simple-style-management-center' ); ?></div>
            </div>
        </div>

        <div class="jj-modal-actions" style="display: flex; flex-direction: column; gap: 10px;">
            <button type="button" class="button button-primary button-hero jj-start-now" style="height: 50px; border-radius: 12px; font-size: 18px; font-weight: 700;">
                <?php _e( '지금 바로 시작하기', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" class="button button-link jj-modal-skip" style="color: #94a3b8;"><?php _e( '나중에 둘러볼게요', 'acf-css-really-simple-style-management-center' ); ?></button>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // 1회성 노출 로직 (Local Storage 사용)
    if (!localStorage.getItem('jj_onboarding_shown')) {
        $('#jj-onboarding-modal').css('display', 'flex').hide().fadeIn(500);
    }

    $('.jj-modal-close, .jj-modal-skip').on('click', function() {
        $('#jj-onboarding-modal').fadeOut(300);
        localStorage.setItem('jj_onboarding_shown', 'true');
    });

    $('.jj-start-now').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).text('추천 디자인 시스템 구축 중...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jj_apply_recommended_setup',
                security: jj_admin_params.nonce
            },
            success: function(response) {
                if (response.success) {
                    $btn.text('구축 완료! 이동 중...');
                    localStorage.setItem('jj_onboarding_shown', 'true');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert(response.data.message || '오류가 발생했습니다.');
                    $btn.prop('disabled', false).text('지금 바로 시작하기');
                }
            },
            error: function() {
                alert('네트워크 오류가 발생했습니다.');
                $btn.prop('disabled', false).text('지금 바로 시작하기');
            }
        });
    });
});
</script>
