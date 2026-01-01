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
         * 자동 저장 기능
         */
        initAutoSave: function() {
            const $wrap = $('.jj-admin-center-wrap');
            if (!$wrap.length) return;

            // 저장된 데이터 복원
            this.restoreAutoSavedData();

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
         * 저장된 데이터 복원
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
                            $field.prop('checked', data.value === 'on' || data.value === $field.val());
                        } else {
                            $field.val(data.value);
                        }

                        // 저장된 데이터 표시
                        $field.addClass('jj-autosaved');
                        restoredCount++;
                    }
                });

                if (restoredCount > 0) {
                    this.showRestoreNotice(restoredCount);
                }
            } catch (e) {
                console.warn('Auto-restore failed:', e);
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
                $('.jj-autosaved').removeClass('jj-autosaved');
            } catch (e) {
                // ignore
            }
        },

        /**
         * 자동 저장 표시기
         */
        showAutoSaveIndicator: function() {
            let $indicator = $('#jj-autosave-indicator');
            if (!$indicator.length) {
                $indicator = $('<div id="jj-autosave-indicator" style="position:fixed; bottom:20px; right:20px; padding:8px 15px; background:#2271b1; color:#fff; border-radius:4px; font-size:12px; z-index:100000; display:none;"><span class="dashicons dashicons-yes" style="vertical-align:middle; margin-right:5px;"></span>자동 저장됨</div>');
                $('body').append($indicator);
            }

            $indicator.fadeIn(200);
            setTimeout(function() {
                $indicator.fadeOut(300);
            }, 2000);
        },

        /**
         * 복원 알림 표시
         */
        showRestoreNotice: function(count) {
            const $notice = $('<div class="notice notice-info" style="margin:15px 0;"><p><strong>자동 저장된 내용이 복원되었습니다.</strong> (' + count + '개 필드) <button type="button" class="button button-small jj-clear-autosave" style="margin-left:10px;">초기화</button></p></div>');
            $('.jj-admin-center-wrap').prepend($notice);

            $notice.find('.jj-clear-autosave').on('click', function() {
                FormEnhancer.clearAutoSavedData();
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            });

            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        },

        /**
         * 실시간 유효성 검사
         */
        initValidation: function() {
            const $wrap = $('.jj-admin-center-wrap');
            if (!$wrap.length) return;

            // 색상 입력 필드 검증
            $wrap.on('blur', '.jj-color-picker, input[type="text"][placeholder*="#"]', function() {
                const $field = $(this);
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
            $field.addClass('jj-field-error');
            let $error = $field.siblings('.jj-field-error-message');
            
            if (!$error.length) {
                $error = $('<span class="jj-field-error-message" style="display:block; color:#d63638; font-size:12px; margin-top:4px;">' + message + '</span>');
                $field.after($error);
            } else {
                $error.text(message).show();
            }
        },

        /**
         * 필드 오류 제거
         */
        clearFieldError: function($field) {
            $field.removeClass('jj-field-error');
            $field.siblings('.jj-field-error-message').hide();
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

            // [data-tooltip] 속성이 있는 요소에 툴팁 추가
            $wrap.find('[data-tooltip]').each(function() {
                const $elem = $(this);
                const tooltip = $elem.data('tooltip');

                $elem.on('mouseenter', function() {
                    const $tooltip = $('<div class="jj-tooltip" style="position:absolute; padding:8px 12px; background:#1d2327; color:#fff; border-radius:4px; font-size:12px; z-index:100001; max-width:300px; pointer-events:none;">' + tooltip + '</div>');
                    $('body').append($tooltip);

                    const offset = $elem.offset();
                    const tooltipTop = offset.top - $tooltip.outerHeight() - 8;
                    const tooltipLeft = offset.left + ($elem.outerWidth() / 2) - ($tooltip.outerWidth() / 2);

                    $tooltip.css({
                        top: tooltipTop,
                        left: tooltipLeft
                    });

                    $elem.data('jj-tooltip-element', $tooltip);
                }).on('mouseleave', function() {
                    const $tooltip = $elem.data('jj-tooltip-element');
                    if ($tooltip) {
                        $tooltip.remove();
                        $elem.removeData('jj-tooltip-element');
                    }
                });
            });

            // 도움말 아이콘 자동 생성
            $wrap.find('.description, p.description').each(function() {
                const $desc = $(this);
                const text = $desc.text().trim();
                
                if (text && !$desc.prev('.jj-help-icon').length) {
                    const $icon = $('<span class="dashicons dashicons-editor-help jj-help-icon" style="color:#2271b1; cursor:help; vertical-align:middle; margin-right:5px;" data-tooltip="' + text.replace(/"/g, '&quot;') + '"></span>');
                    $desc.before($icon);
                    $desc.hide();
                }
            });
        }
    };

    // 초기화
    $(document).ready(function() {
        FormEnhancer.init();
    });

})(jQuery);
