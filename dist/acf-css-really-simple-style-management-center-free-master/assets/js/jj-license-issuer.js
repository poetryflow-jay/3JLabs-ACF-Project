/**
 * J&J 라이센스 발행 관리 JavaScript
 * 
 * @version 3.8.0
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // 라이센스 생성 폼 제출
        $('#jj-generate-license-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $btn = $('#jj-generate-license-btn');
            const $spinner = $form.find('.spinner');
            const licenseType = $('#jj-license-type').val();
            const customer = $('#jj-license-customer').val();
            const expires = $('#jj-license-expires').val();
            
            if (!licenseType) {
                alert(jjLicenseIssuer.i18n.error + ': ' + '라이센스 타입을 선택하세요.');
                return;
            }
            
            $btn.prop('disabled', true);
            $spinner.addClass('is-active');
            
            $.ajax({
                url: jjLicenseIssuer.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_generate_license',
                    nonce: jjLicenseIssuer.nonce,
                    license_type: licenseType,
                    customer: customer,
                    expires: expires
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    $spinner.removeClass('is-active');
                    
                    if (response.success) {
                        showMessage('success', response.data.message + ' 생성된 라이센스 키: ' + response.data.license_key);
                        
                        // 폼 초기화
                        $form[0].reset();
                        
                        // 페이지 새로고침 (목록 업데이트)
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showMessage('error', response.data.message || jjLicenseIssuer.i18n.error);
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    $spinner.removeClass('is-active');
                    showMessage('error', jjLicenseIssuer.i18n.error);
                }
            });
        });
        
        // 라이센스 비활성화/활성화
        $(document).on('click', '.jj-deactivate-license, .jj-activate-license', function(e) {
            e.preventDefault();
            
            if (!confirm(jjLicenseIssuer.i18n.confirm_deactivate)) {
                return;
            }
            
            const $btn = $(this);
            const licenseKey = $btn.data('license-key');
            const status = $btn.hasClass('jj-deactivate-license') ? 'inactive' : 'active';
            const $row = $btn.closest('tr');
            
            $btn.prop('disabled', true);
            
            $.ajax({
                url: jjLicenseIssuer.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_deactivate_license',
                    nonce: jjLicenseIssuer.nonce,
                    license_key: licenseKey,
                    status: status
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    
                    if (response.success) {
                        showMessage('success', response.data.message);
                        
                        // 페이지 새로고침 (상태 업데이트)
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showMessage('error', response.data.message || jjLicenseIssuer.i18n.error);
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    showMessage('error', jjLicenseIssuer.i18n.error);
                }
            });
        });
        
        // 라이센스 상태 확인
        $(document).on('click', '.jj-check-license-status', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const licenseKey = $btn.data('license-key');
            
            $btn.prop('disabled', true);
            
            $.ajax({
                url: jjLicenseIssuer.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_check_license_status',
                    nonce: jjLicenseIssuer.nonce,
                    license_key: licenseKey
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    
                    if (response.success) {
                        const license = response.data.license;
                        const activations = response.data.activations;
                        
                        let message = '라이센스 상태:\n';
                        message += '타입: ' + license.type + '\n';
                        message += '상태: ' + (license.status === 'active' ? '활성화됨' : '비활성화됨') + '\n';
                        message += '고객: ' + (license.customer || '-') + '\n';
                        message += '활성화된 사이트 수: ' + (activations ? activations.length : 0) + '개\n';
                        
                        if (activations && activations.length > 0) {
                            message += '\n활성화된 사이트:\n';
                            activations.forEach(function(activation) {
                                message += '- ' + activation.site_url + '\n';
                            });
                        }
                        
                        alert(message);
                    } else {
                        showMessage('error', response.data.message || jjLicenseIssuer.i18n.error);
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    showMessage('error', jjLicenseIssuer.i18n.error);
                }
            });
        });
        
        // 라이센스 삭제
        $(document).on('click', '.jj-delete-license', function(e) {
            e.preventDefault();
            
            if (!confirm(jjLicenseIssuer.i18n.confirm_delete)) {
                return;
            }
            
            const $btn = $(this);
            const licenseKey = $btn.data('license-key');
            const $row = $btn.closest('tr');
            
            $btn.prop('disabled', true);
            
            $.ajax({
                url: jjLicenseIssuer.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_delete_license',
                    nonce: jjLicenseIssuer.nonce,
                    license_key: licenseKey
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    
                    if (response.success) {
                        showMessage('success', response.data.message);
                        
                        // 행 제거 (페이드 아웃 효과)
                        $row.fadeOut(300, function() {
                            $(this).remove();
                            
                            // 목록이 비었으면 메시지 표시
                            if ($('#jj-license-list-body tr').length === 0) {
                                $('#jj-license-list-body').html(
                                    '<tr><td colspan="8" style="text-align: center; padding: 30px;">발행된 라이센스가 없습니다.</td></tr>'
                                );
                            }
                        });
                    } else {
                        showMessage('error', response.data.message || jjLicenseIssuer.i18n.error);
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    showMessage('error', jjLicenseIssuer.i18n.error);
                }
            });
        });
        
        // 라이센스 키 복사
        $(document).on('click', '.jj-copy-license-key', function(e) {
            e.preventDefault();
            
            const licenseKey = $(this).data('license-key');
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(licenseKey).select();
            document.execCommand('copy');
            $temp.remove();
            
            // 복사 확인 메시지
            const $btn = $(this);
            const originalText = $btn.html();
            $btn.html('<span class="dashicons dashicons-yes" style="font-size: 12px; margin-top: 2px; color: #00a32a;"></span> 복사됨');
            
            setTimeout(function() {
                $btn.html(originalText);
            }, 2000);
        });
        
        // 메시지 표시 함수
        function showMessage(type, message) {
            let $message = $('.jj-license-message.' + type);
            
            if ($message.length === 0) {
                $message = $('<div class="jj-license-message ' + type + '"></div>');
                $('.jj-license-issuer-wrap h1').after($message);
            }
            
            $message.html(message).fadeIn(300);
            
            setTimeout(function() {
                $message.fadeOut(300);
            }, 5000);
        }
    });
})(jQuery);

