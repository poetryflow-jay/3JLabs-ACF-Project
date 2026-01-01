/**
 * [Phase 8.3.2] 폼 UX 개선
 * 
 * 기능:
 * - 폼 필드 자동 저장 (localStorage)
 * - 실시간 유효성 검사
 * - 입력 힌트 및 도움말
 */

(function($) {
    'use strict';

    const FormEnhancer = {
        storageKey: 'jj_admin_center_auto_save',
        
        init: function() {
            this.initAutoSave();
            this.initValidation();
            this.initTooltips();
        },

        /**
         * 자동 저장 기능 초기화
         */
        initAutoSave: function() {
            const $wrap = $('.jj-admin-center-wrap');
            if (!$wrap.length) return;

            // 저장된 데이터 확인 및 복원 알림
            this.checkAutoSavedData();

            // 입력 필드 변경 감지 (debounce)
            let saveTimeout;
            $wrap.on('input change', 'input, select, textarea', function() {
                const $field = $(this);
                
                // 제외할 필드 (비밀번호, 파일 등)
                if ($field.attr('type') === 'password' || 
                    $field.attr('type') === 'file' ||
                    $field.hasClass('jj-no-autosave')) {
                    return;
                }

                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(function() {
                    FormEnhancer.saveField($field);
                    FormEnhancer.showAutoSaveIndicator();
                }, 1000); // 1초 후 저장
            });

            // 폼 제출 시 자동 저장 데이터 삭제
            $wrap.on('submit', 'form', function() {
                FormEnhancer.clearAutoSavedData();
            });
        },

        /**
         * 필드 저장
         */
        saveField: function($field) {
            try {
                const name = $field.attr('name');
                if (!name) return;

                const value = $field.val();
                const type = $field.attr('type') || $field.prop('tagName').toLowerCase();
                
                let savedData = this.getAutoSavedData();
                if (!savedData) {
                    savedData = {};
                }

                savedData[name] = {
                    value: value,
                    type: type,
                    timestamp: Date.now()
                };

                localStorage.setItem(this.storageKey, JSON.stringify(savedData));
            } catch (e) {
                // localStorage 사용 불가 시 무시
                console.warn('Auto-save failed:', e);
            }
        },

        /**
         * 저장된 데이터 확인
         */
        checkAutoSavedData: function() {
            const savedData = this.getAutoSavedData();
            if (!savedData || Object.keys(savedData).length === 0) return;

            // 데이터가 얼마나 오래되었는지 확인 (예: 24시간 이상 되면 무시할 수도 있음)
            // 여기서는 단순히 존재 여부만 확인

            this.showRestorePrompt(Object.keys(savedData).length);
        },

        /**
         * 복원 프롬프트 표시 (Notice)
         */
        showRestorePrompt: function(count) {
            const $wrap = $('.jj-admin-center-wrap');
            
            // 이미 존재하면 제거
            $('#jj-autosave-restore-notice').remove();

            const html = `
                <div id="jj-autosave-restore-notice" class="notice notice-info is-dismissible jj-notice" style="margin: 15px 0; border-left-color: #2271b1;">
                    <p>
                        <strong><span class="dashicons dashicons-backup" style="color: #2271b1; vertical-align: middle;"></span> 작성 중이던 내용이 발견되었습니다.</strong> 
                        (${count}개 필드)
                    </p>
                    <p>
                        <button type="button" class="button button-primary jj-restore-btn">
                            <span class="dashicons dashicons-undo" style="margin-top:4px;"></span> 지금 복원하기
                        </button>
                        <button type="button" class="button button-secondary jj-discard-btn" style="margin-left: 5px;">
                            삭제하기
                        </button>
                    </p>
                </div>
            `;
            
            $wrap.prepend(html);

            // 이벤트 바인딩
            const $notice = $('#jj-autosave-restore-notice');
            
            $notice.find('.jj-restore-btn').on('click', function() {
                FormEnhancer.restoreAutoSavedData();
                $notice.slideUp(200, function() { $(this).remove(); });
            });

            $notice.find('.jj-discard-btn').on('click', function() {
                if (confirm('저장된 내용을 정말 삭제하시겠습니까?')) {
                    FormEnhancer.clearAutoSavedData();
                    $notice.slideUp(200, function() { $(this).remove(); });
                }
            });

            // 워드프레스 기본 알림 닫기 버튼 처리
            $notice.on('click', '.notice-dismiss', function() {
                // 닫기만 하고 데이터는 유지? 아니면 삭제? -> 유지하는 것이 안전함. 다음 로드 시 다시 물어봄.
            });
        },

        /**
         * 저장된 데이터 복원 실행
         */
        restoreAutoSavedData: function() {
            try {
                const savedData = this.getAutoSavedData();
                if (!savedData) return;

                const $wrap = $('.jj-admin-center-wrap');
                let restoredCount = 0;

                Object.keys(savedData).forEach(function(name) {
                    const data = savedData[name];
                    const $field = $wrap.find('[name="' + name + '"]');
                    
                    if ($field.length && $field.attr('type') !== 'password') {
                        // 값 설정
                        if ($field.is('input[type="checkbox"]') || $field.is('input[type="radio"]')) {
                            // 체크박스/라디오 처리
                            if ($field.val() === data.value) {
                                $field.prop('checked', true).trigger('change');
                            } else if (data.value === 'on') {
                                // 단일 체크박스(value='on'이거나 생략된 경우)
                                $field.prop('checked', true).trigger('change');
                            }
                        } else {
                            $field.val(data.value).trigger('change'); // trigger change for other listeners
                        }

                        // 복원 효과 표시
                        $field.addClass('jj-restored-highlight');
                        setTimeout(function() {
                            $field.removeClass('jj-restored-highlight');
                        }, 2000);

                        restoredCount++;
                    }
                });

                if (restoredCount > 0) {
                    this.showToast(restoredCount + '개 항목이 복원되었습니다.');
                }
            } catch (e) {
                console.warn('Auto-restore failed:', e);
                this.showToast('복원 중 오류가 발생했습니다.', 'error');
            }
        },

        /**
         * 자동 저장 데이터 가져오기
         */
        getAutoSavedData: function() {
            try {
                const data = localStorage.getItem(this.storageKey);
                return data ? JSON.parse(data) : null;
            } catch (e) {
                return null;
            }
        },

        /**
         * 자동 저장 데이터 삭제
         */
        clearAutoSavedData: function() {
            try {
                localStorage.removeItem(this.storageKey);
                $('#jj-autosave-restore-notice').remove();
            } catch (e) {
                // ignore
            }
        },

        /**
         * 자동 저장 표시기 (우측 하단)
         */
        showAutoSaveIndicator: function() {
            let $indicator = $('#jj-autosave-indicator');
            if (!$indicator.length) {
                $indicator = $('<div id="jj-autosave-indicator" style="position:fixed; bottom:20px; right:20px; padding:8px 15px; background:#2271b1; color:#fff; border-radius:4px; font-size:12px; z-index:100000; display:none; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"><span class="dashicons dashicons-cloud-saved" style="vertical-align:middle; margin-right:5px; font-size:16px; width:16px; height:16px;"></span>자동 저장됨</div>');
                $('body').append($indicator);
            }

            $indicator.stop(true, true).fadeIn(200);
            
            // 기존 타이머 제거
            if (this.indicatorTimeout) clearTimeout(this.indicatorTimeout);
            
            this.indicatorTimeout = setTimeout(function() {
                $indicator.fadeOut(500);
            }, 2000);
        },

        /**
         * 토스트 메시지 표시
         */
        showToast: function(message, type = 'success') {
            // jj-common-utils.js의 토스트 기능이 있다면 사용, 없으면 폴백
            if (window.JJ_Common_Utils && window.JJ_Common_Utils.toast) {
                window.JJ_Common_Utils.toast(message, type);
                return;
            }

            // 폴백 토스트
            const $toast = $('<div class="jj-toast" style="position:fixed; top:50px; left:50%; transform:translateX(-50%); background:#333; color:#fff; padding:10px 20px; border-radius:4px; z-index:99999; font-size:14px;">' + message + '</div>');
            $('body').append($toast);
            setTimeout(function() {
                $toast.fadeOut(500, function() { $(this).remove(); });
            }, 3000);
        },

        /**
         * 실시간 유효성 검사
         */
        initValidation: function() {
            const $wrap = $('.jj-admin-center-wrap');
            if (!$wrap.length) return;

            // 색상 입력 필드 검증
            $wrap.on('blur input', '.jj-color-picker, input[type="text"][placeholder*="#"]', function() {
                const $field = $(this);
                // 빈 값은 허용 (필수가 아닐 경우)
                if ($field.val() === '') {
                    FormEnhancer.clearFieldError($field);
                    return;
                }
                
                const value = $field.val().trim();
                if (value && !FormEnhancer.isValidHexColor(value)) {
                    FormEnhancer.showFieldError($field, '올바른 HEX 색상 형식이 아닙니다. (예: #2271b1)');
                } else {
                    FormEnhancer.clearFieldError($field);
                }
            });

            // URL 입력 필드 검증
            $wrap.on('blur', 'input[type="url"], input[placeholder*="http"]', function() {
                const $field = $(this);
                if ($field.val() === '') {
                    FormEnhancer.clearFieldError($field);
                    return;
                }

                const value = $field.val().trim();
                if (value && !FormEnhancer.isValidUrl(value)) {
                    FormEnhancer.showFieldError($field, '올바른 URL 형식이 아닙니다.');
                } else {
                    FormEnhancer.clearFieldError($field);
                }
            });

            // 이메일 입력 필드 검증
            $wrap.on('blur', 'input[type="email"]', function() {
                const $field = $(this);
                if ($field.val() === '') {
                    FormEnhancer.clearFieldError($field);
                    return;
                }

                const value = $field.val().trim();
                if (value && !FormEnhancer.isValidEmail(value)) {
                    FormEnhancer.showFieldError($field, '올바른 이메일 형식이 아닙니다.');
                } else {
                    FormEnhancer.clearFieldError($field);
                }
            });
        },

        /**
         * 필드 오류 표시
         */
        showFieldError: function($field, message) {
            $field.addClass('jj-field-error').css('border-color', '#d63638');
            let $error = $field.siblings('.jj-field-error-message');
            
            if (!$error.length) {
                // wp-color-picker의 경우 구조가 다를 수 있음
                if ($field.closest('.wp-picker-container').length) {
                    $error = $('<span class="jj-field-error-message" style="display:block; color:#d63638; font-size:12px; margin-top:4px;">' + message + '</span>');
                    $field.closest('.wp-picker-container').after($error);
                } else {
                    $error = $('<span class="jj-field-error-message" style="display:block; color:#d63638; font-size:12px; margin-top:4px;">' + message + '</span>');
                    $field.after($error);
                }
            } else {
                $error.text(message).show();
            }
        },

        /**
         * 필드 오류 제거
         */
        clearFieldError: function($field) {
            $field.removeClass('jj-field-error').css('border-color', '');
            
            if ($field.closest('.wp-picker-container').length) {
                $field.closest('.wp-picker-container').siblings('.jj-field-error-message').hide();
            } else {
                $field.siblings('.jj-field-error-message').hide();
            }
        },

        /**
         * HEX 색상 검증
         */
        isValidHexColor: function(value) {
            return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(value);
        },

        /**
         * URL 검증
         */
        isValidUrl: function(value) {
            try {
                // 프로토콜이 없는 경우 http:// 추가하여 검사 (관대하게)
                if (!/^https?:\/\//i.test(value)) {
                    value = 'https://' + value;
                }
                new URL(value);
                return true;
            } catch (e) {
                return false;
            }
        },

        /**
         * 이메일 검증
         */
        isValidEmail: function(value) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        },

        /**
         * 툴팁 및 힌트
         */
        initTooltips: function() {
            const $wrap = $('.jj-admin-center-wrap');
            if (!$wrap.length) return;

            // 1. [data-tooltip] 속성이 있는 요소
            // 2. .help-tip 클래스가 있는 요소
            
            // CSS 스타일 주입 (동적 생성)
            if (!$('#jj-tooltip-styles').length) {
                const styles = `
                    <style id="jj-tooltip-styles">
                        .jj-tooltip { position:absolute; padding:8px 12px; background:#1d2327; color:#fff; border-radius:4px; font-size:12px; z-index:100001; max-width:300px; pointer-events:none; box-shadow: 0 2px 5px rgba(0,0,0,0.2); opacity: 0; transition: opacity 0.2s; }
                        .jj-tooltip.visible { opacity: 1; }
                        .jj-tooltip::after { content:''; position:absolute; bottom:-5px; left:50%; margin-left:-5px; border-width:5px 5px 0; border-style:solid; border-color:#1d2327 transparent transparent transparent; }
                        .jj-restored-highlight { animation: jj-flash-highlight 2s ease-out; }
                        @keyframes jj-flash-highlight {
                            0% { background-color: #f0f6fc; box-shadow: 0 0 0 2px #2271b1; }
                            100% { background-color: transparent; box-shadow: none; }
                        }
                    </style>
                `;
                $('head').append(styles);
            }

            // 도움말 아이콘 자동 생성 (기존 description 클래스 활용)
            $wrap.find('.description, p.description').each(function() {
                const $desc = $(this);
                // 이미 아이콘이 있거나, 텍스트가 너무 길면 패스 (선택적)
                const text = $desc.text().trim();
                
                // 짧은 설명만 툴팁으로 변환 (예: 50자 이내)
                // 긴 설명은 그대로 둠
                if (text && text.length < 100 && !$desc.prev('.jj-help-icon').length && !$desc.closest('.form-table').length) { 
                    // form-table 내부는 레이아웃 문제로 제외하거나 별도 처리
                    // 여기서는 간단하게 처리
                }
            });

            // 툴팁 이벤트 위임
            $(document).on('mouseenter', '[data-tooltip]', function() {
                const $elem = $(this);
                const text = $elem.data('tooltip');
                if (!text) return;

                const $tooltip = $('<div class="jj-tooltip">' + text + '</div>');
                $('body').append($tooltip);

                const offset = $elem.offset();
                const tooltipTop = offset.top - $tooltip.outerHeight() - 10;
                const tooltipLeft = offset.left + ($elem.outerWidth() / 2) - ($tooltip.outerWidth() / 2);

                $tooltip.css({
                    top: tooltipTop,
                    left: tooltipLeft
                });

                // 애니메이션
                requestAnimationFrame(() => $tooltip.addClass('visible'));

                $elem.data('jj-active-tooltip', $tooltip);
            }).on('mouseleave', '[data-tooltip]', function() {
                const $elem = $(this);
                const $tooltip = $elem.data('jj-active-tooltip');
                if ($tooltip) {
                    $tooltip.removeClass('visible');
                    setTimeout(() => $tooltip.remove(), 200);
                    $elem.removeData('jj-active-tooltip');
                }
            });
        }
    };

    // 초기화
    $(document).ready(function() {
        FormEnhancer.init();
    });

})(jQuery);
