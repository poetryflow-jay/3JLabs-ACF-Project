/**
 * JJ License Manager - Admin Scripts
 * 
 * @version 2.0.0
 */

(function($) {
    'use strict';
    
    // 문서 준비 완료 시 실행
    $(document).ready(function() {
        initLicenseManager();
    });
    
    /**
     * 라이센스 매니저 초기화
     */
    function initLicenseManager() {
        // 동적으로 생성된 요소에도 이벤트 바인딩 (이벤트 위임 사용)
        $(document).on('click', '.delete-license, a.delete', function(e) {
            e.preventDefault();
            var $link = $(this);
            var href = $link.attr('href');
            
            if (!href || !confirm(jjLicenseManager.strings.confirmDelete)) {
                return false;
            }
            
            // 버튼 비활성화 및 로딩 표시
            $link.addClass('disabled').text('삭제 중...');
            
            // 페이지 이동
            window.location.href = href;
        });
        
        // 라이센스 비활성화 확인 (동적 요소 지원)
        $(document).on('click', '.deactivate-license', function(e) {
            if (!confirm(jjLicenseManager.strings.confirmDeactivate)) {
                e.preventDefault();
                return false;
            }
            
            // 버튼 피드백
            var $button = $(this);
            $button.addClass('disabled').text('비활성화 중...');
        });
        
        // 라이센스 키 복사 (동적 요소 지원)
        $(document).on('click', '.copy-license-key', function(e) {
            e.preventDefault();
            var $button = $(this);
            var licenseKey = $button.data('license-key') || $button.closest('tr').find('strong').text().trim();
            
            if (!licenseKey) {
                alert('라이센스 키를 찾을 수 없습니다.');
                return false;
            }
            
            // 클립보드 복사 (최신 API 사용)
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(licenseKey).then(function() {
                    showCopyFeedback($button);
                }).catch(function() {
                    // 폴백: 기존 방식 사용
                    fallbackCopyToClipboard(licenseKey, $button);
                });
            } else {
                // 폴백: 기존 방식 사용
                fallbackCopyToClipboard(licenseKey, $button);
            }
        });
        
        // 폼 제출 시 버튼 비활성화 및 로딩 표시
        $(document).on('submit', 'form', function() {
            var $form = $(this);
            var $submitButton = $form.find('input[type="submit"], button[type="submit"]');
            
            if ($submitButton.length) {
                $submitButton.prop('disabled', true).each(function() {
                    var $btn = $(this);
                    if (!$btn.data('original-text')) {
                        $btn.data('original-text', $btn.val() || $btn.text());
                    }
                    $btn.val('처리 중...').text('처리 중...');
                });
            }
        });
        
        // 필수 필드 검증 강화
        $(document).on('submit', 'form', function(e) {
            var $form = $(this);
            var $requiredFields = $form.find('[required]');
            var hasError = false;
            
            $requiredFields.each(function() {
                var $field = $(this);
                var value = $field.val();
                
                // 값이 비어있거나 공백만 있는 경우
                if (!value || value.trim() === '') {
                    hasError = true;
                    $field.addClass('error');
                    
                    // 에러 메시지 표시
                    if (!$field.next('.error-message').length) {
                        $field.after('<span class="error-message" style="color: #d63638; display: block; margin-top: 5px;">필수 필드입니다.</span>');
                    }
                } else {
                    $field.removeClass('error');
                    $field.next('.error-message').remove();
                }
            });
            
            if (hasError) {
                e.preventDefault();
                showNotice('error', '필수 필드를 모두 입력해주세요.');
                return false;
            }
        });
        
        // 필드 포커스 시 에러 상태 제거
        $(document).on('focus', 'input, select, textarea', function() {
            $(this).removeClass('error').next('.error-message').remove();
        });
        
        // AJAX 요청 에러 처리
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            console.error('AJAX Error:', thrownError);
            showNotice('error', '요청 처리 중 오류가 발생했습니다. 페이지를 새로고침해주세요.');
        });
    }
    
    /**
     * 클립보드 복사 피드백 표시
     */
    function showCopyFeedback($button) {
        var originalText = $button.text();
        $button.text('복사됨!').addClass('button-primary');
        
        setTimeout(function() {
            $button.text(originalText).removeClass('button-primary');
        }, 2000);
    }
    
    /**
     * 폴백 클립보드 복사 방법
     */
    function fallbackCopyToClipboard(text, $button) {
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(text).select();
        
        try {
            var successful = document.execCommand('copy');
            if (successful) {
                showCopyFeedback($button);
            } else {
                alert('클립보드 복사에 실패했습니다. 수동으로 복사해주세요.');
            }
        } catch (err) {
            console.error('클립보드 복사 실패:', err);
            alert('클립보드 복사에 실패했습니다. 수동으로 복사해주세요.');
        }
        
        $temp.remove();
    }
    
    /**
     * 알림 메시지 표시
     */
    function showNotice(type, message) {
        type = type || 'info';
        var noticeClass = 'notice notice-' + type + ' is-dismissible';
        var $notice = $('<div class="' + noticeClass + '"><p>' + message + '</p></div>');
        
        $('.wrap h1').after($notice);
        
        // 5초 후 자동 제거
        setTimeout(function() {
            $notice.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    /**
     * 플러그인 자동 업데이트 토글
     */
    $(document).on('click', '.jj-toggle-plugin-auto-update', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var pluginFile = $button.data('plugin-file');
        var pluginKey = $button.data('plugin-key');
        var currentState = $button.data('current-state') === '1';
        var newState = !currentState;
        
        if (!pluginFile) {
            showNotice('error', '플러그인 정보를 찾을 수 없습니다.');
            return;
        }
        
        // 버튼 비활성화
        $button.prop('disabled', true).text('처리 중...');
        
        // AJAX 요청
        $.ajax({
            url: (typeof ajaxurl !== 'undefined' ? ajaxurl : (jjLicenseManager && jjLicenseManager.ajaxUrl ? jjLicenseManager.ajaxUrl : '/wp-admin/admin-ajax.php')),
            type: 'POST',
            data: {
                action: 'jj_toggle_plugin_auto_update',
                nonce: (jjLicenseManager && jjLicenseManager.nonce ? jjLicenseManager.nonce : ''),
                plugin_file: pluginFile,
                enable: newState ? '1' : '0'
            },
            success: function(response) {
                if (response.success) {
                    // 상태 업데이트
                    $button.data('current-state', newState ? '1' : '0');
                    $button.text(newState ? '비활성화' : '활성화');
                    
                    // 상태 표시 업데이트
                    var $row = $button.closest('tr');
                    var $status = $row.find('.jj-auto-update-status');
                    if (newState) {
                        $status.removeClass('disabled').addClass('enabled').text('활성화됨');
                    } else {
                        $status.removeClass('enabled').addClass('disabled').text('비활성화됨');
                    }
                    
                    showNotice('success', response.data.message || '설정이 저장되었습니다.');
                } else {
                    showNotice('error', response.data.message || '설정 저장에 실패했습니다.');
                }
            },
            error: function() {
                showNotice('error', '요청 처리 중 오류가 발생했습니다.');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
    
    // 전역 함수로 노출 (다른 스크립트에서 사용 가능)
    window.JJLicenseManager = {
        showNotice: showNotice
    };
})(jQuery);

