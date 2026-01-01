---

**User**

계속 진행하게. 앞으로는 내 허락을 받지 않고 진행해도 되네. 스스로 계획을 추가하고 더 나은 방향으로 이끌어주면 되네. 기본적으로 워드프레스 플러그인이라는 점과, 실제 사용자를 고려해서 개선한다고만 생각하면 자네의 판단은 정확할 걸세, 제이슨. 매번 커밋만 해두어도 우리는 언제든 특정 버전으로 돌아갈 수 있으니, 작업 완료 시에 버전을 업하고 커밋하는 걸 잊지 말고, 타임아웃을 원천 방지하면서 단계별로 분업화 작업을 하면 충분히 오늘 하루 내내 많은 작업을 훌륭히 처리할 수 있을 것이네. 3J 랩스의 CTO 제이슨, 자네에게 다시 한 번 전권을 위임하네. 오늘 자정까지의 모든 작업은 자네의 재량에 맡긴다. - Owner & CEO, Jay, 3J Labs


---

**Cursor**

단계별로 진행하며, 타임아웃을 방지하기 위해 작업을 분할합니다. 먼저 폼 UX 개선부터 완료하겠습니다.


```javascript
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

```

```php
               // [Phase 8.3.1] 키보드 네비게이션 개선
               wp_enqueue_script(
                   'jj-admin-center-keyboard',
                   $base_url . 'assets/js/jj-admin-center-keyboard.js',
                   array( 'jquery' ),
                   $version,
                   true
               );
               
               // [Phase 8.3.2] 폼 UX 개선
               wp_enqueue_script(
                   'jj-form-enhancer',
                   $base_url . 'assets/js/jj-form-enhancer.js',
                   array( 'jquery' ),
                   $version,
                   true
               );
```

```css
/**
 * J&J Admin Center Styles - v8.3.2
 * 
 * 탭 기반 인터페이스와 2패널 레이아웃 구현
 * - 탭 네비게이션 스타일
 * - 2패널 레이아웃 (왼쪽: 메뉴 목록, 오른쪽: 상세 설정)
 * - 사이드바 액션 버튼 스타일
 * - 반응형 디자인 (모바일/태블릿 최적화)
 * - 색상 입력 필드 및 미리보기 스타일
 * - [v3.8.0 신규] 라이센스 관리 스타일
 * - [v5.0.3] 애니메이션 개선 및 부드러운 전환 효과
 * - [v6.3.0 신규] 왼쪽 고정 사이드바 네비게이션 추가
 * - [v8.3.2] 폼 UX 개선 스타일
 * 
 * @since 3.7.0
 * @updated 8.3.2
 */

/* 컨테이너 */
.jj-admin-center-wrap {
    margin: 20px auto 0;
    max-width: 1600px;
    background: #fff;
    position: relative;
}

/* [v6.3.0] 왼쪽 고정 사이드바 네비게이션 */
.jj-admin-center-sidebar {
    position: fixed;
    left: 160px; /* WordPress 관리자 사이드바 너비 */
    top: 32px; /* WordPress 상단바 높이 */
    width: 240px;
    height: calc(100vh - 32px);
    background: #f6f7f7;
    border-right: 1px solid #c3c4c7;
    overflow-y: auto;
    z-index: 100;
    transition: transform 0.3s ease;
}

/* WordPress 관리자 사이드바 접힘 상태 대응 */
.folded .jj-admin-center-sidebar {
    left: 36px;
}

/* 모바일/태블릿에서 사이드바 숨김 */
@media screen and (max-width: 960px) {
    .jj-admin-center-sidebar {
        transform: translateX(-100%);
    }
    
    .jj-admin-center-sidebar.jj-sidebar-open {
        transform: translateX(0);
    }
}

.jj-admin-center-sidebar-header {
    padding: 15px 20px;
    background: #fff;
    border-bottom: 1px solid #c3c4c7;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.jj-admin-center-sidebar-header h2 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #1d2327;
}

.jj-sidebar-toggle {
    display: none;
    background: none;
    border: none;
    padding: 5px;
    cursor: pointer;
    color: #50575e;
}

@media screen and (max-width: 960px) {
    .jj-sidebar-toggle {
        display: block;
    }
}

.jj-admin-center-sidebar-nav {
    list-style: none;
    margin: 0;
    padding: 10px 0;
}

.jj-admin-center-sidebar-nav li {
    margin: 0;
    padding: 0;
}

.jj-admin-center-sidebar-nav a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #50575e;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s;
    border-left: 3px solid transparent;
}

.jj-admin-center-sidebar-nav a:hover {
    background: #fff;
    color: #2271b1;
    border-left-color: #2271b1;
}

.jj-admin-center-sidebar-nav a.active {
    background: #fff;
    color: #2271b1;
    border-left-color: #2271b1;
    font-weight: 600;
}

.jj-admin-center-sidebar-nav .dashicons {
    margin-right: 10px;
    font-size: 18px;
    width: 18px;
    height: 18px;
}

/* 사이드바가 있을 때 메인 컨텐츠 오프셋 */
.jj-admin-center-wrap.jj-has-sidebar {
    margin-left: 240px;
}

@media screen and (max-width: 960px) {
    .jj-admin-center-wrap.jj-has-sidebar {
        margin-left: 0;
    }
}

/* 헤더 */
.jj-admin-center-header {
    background: #fff;
    border-bottom: 1px solid #c3c4c7;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.jj-admin-center-header h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
}

/* 탭 네비게이션 */
.jj-admin-center-tabs {
    display: flex;
    background: #f6f7f7;
    border-bottom: 1px solid #c3c4c7;
    padding: 0 20px;
    margin: 0;
}

.jj-admin-center-tabs li {
    list-style: none;
    margin: 0;
    padding: 0;
}

.jj-admin-center-tabs li a {
    display: block;
    padding: 12px 20px;
    text-decoration: none;
    color: #50575e;
    font-size: 14px;
    font-weight: 500;
    border-bottom: 3px solid transparent;
    transition: all 0.2s;
}

.jj-admin-center-tabs li a:hover {
    color: #2271b1;
    background: #fff;
}

.jj-admin-center-tabs li.active a {
    color: #2271b1;
    border-bottom-color: #2271b1;
    background: #fff;
}

/* 탭 컨텐츠 */
.jj-admin-center-tab-content {
    display: none;
    padding: 25px;
    position: relative;
}

.jj-admin-center-tab-content.active {
    display: block;
}

/* 2패널 레이아웃 (Admin Menu, Meta Boxes 등) */
.jj-admin-center-2panel {
    display: grid;
    grid-template-columns: 350px 1fr 220px;
    gap: 25px;
    height: calc(100vh - 250px);
    min-height: 600px;
    position: relative;
}

/* 왼쪽 패널: 메뉴 목록 */
.jj-admin-center-left-panel {
    background: #f9f9f9;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    overflow-y: auto;
    padding: 15px;
}

.jj-admin-center-left-panel h3 {
    margin: 0 0 15px;
    font-size: 14px;
    font-weight: 600;
    color: #1d2327;
    padding-bottom: 10px;
    border-bottom: 1px solid #dcdcde;
}

.jj-admin-center-menu-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.jj-admin-center-menu-item {
    padding: 10px 12px;
    margin-bottom: 5px;
    background: #fff;
    border: 1px solid #dcdcde;
    border-radius: 3px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.jj-admin-center-menu-item.ui-sortable-helper {
    cursor: move !important;
    z-index: 99999 !important;
}

.jj-admin-center-menu-item.ui-sortable-helper * {
    pointer-events: none;
}

.jj-admin-center-menu-item.jj-dragging {
    opacity: 0.8;
}

.jj-admin-center-menu-item.jj-dragging * {
    pointer-events: none;
}

.jj-admin-center-menu-item:hover {
    border-color: #2271b1;
    background: #f0f6fc;
}

.jj-admin-center-menu-item.active {
    border-color: #2271b1;
    background: #e8f4f8;
    font-weight: 600;
}

.jj-admin-center-menu-item-title {
    flex: 1;
    font-size: 13px;
    color: #1d2327;
}

.jj-admin-center-menu-item-badge {
    font-size: 11px;
    color: #646970;
    background: #f0f0f1;
    padding: 2px 6px;
    border-radius: 2px;
    margin-left: 8px;
}

.jj-admin-center-menu-item.active .jj-admin-center-menu-item-badge {
    background: #2271b1;
    color: #fff;
}

/* 오른쪽 패널: 상세 설정 */
.jj-admin-center-right-panel {
    background: #fff;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    overflow-y: auto;
    padding: 25px;
}

.jj-admin-center-right-panel h3 {
    margin: 0 0 20px;
    font-size: 18px;
    font-weight: 600;
    color: #1d2327;
    padding-bottom: 15px;
    border-bottom: 2px solid #dcdcde;
}

.jj-admin-center-right-panel .description {
    margin: 0 0 20px;
    color: #646970;
    font-size: 13px;
    line-height: 1.6;
}

/* [v3.7.0] 메뉴 아이콘 미리보기 스타일 */
.jj-menu-icon-preview {
    margin-top: 8px;
    padding: 8px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    display: inline-block;
    min-width: 40px;
    min-height: 40px;
    text-align: center;
    vertical-align: middle;
    border-radius: 3px;
}

.jj-menu-icon-preview .dashicons,
.jj-menu-icon-preview img {
    vertical-align: middle;
}

.jj-menu-icon-preview img {
    max-width: 24px;
    max-height: 24px;
}

/* 서브메뉴 편집 영역 스타일 */
.jj-admin-center-item-details table table {
    margin: 0;
    background: transparent;
}

.jj-admin-center-item-details tr[colspan="2"] td {
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 10px;
}

.jj-admin-center-item-details tr[colspan="2"] td h5 {
    margin-top: 0;
    margin-bottom: 10px;
}

/* 사이드바 (오른쪽) */
.jj-admin-center-action-sidebar {
    position: fixed;
    top: 100px;
    right: 20px;
    width: 200px;
    background: #f9f9f9;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    padding: 20px;
    z-index: 100;
}

.jj-admin-center-action-sidebar h4 {
    margin: 0 0 15px;
    font-size: 14px;
    font-weight: 600;
    color: #1d2327;
}

.jj-admin-center-sidebar-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.jj-admin-center-sidebar-actions .button {
    width: 100%;
    text-align: center;
    justify-content: center;
}

.jj-admin-center-sidebar-actions .button-primary {
    background: #2271b1;
    border-color: #2271b1;
    color: #fff;
    font-weight: 600;
}

.jj-admin-center-sidebar-actions .button-secondary {
    background: #f6f7f7;
    border-color: #dcdcde;
    color: #50575e;
}

/* General 탭 (단일 패널) */
.jj-admin-center-general-form {
    max-width: 800px;
}

.jj-admin-center-general-form .form-table {
    margin-top: 20px;
}

.jj-admin-center-general-form .form-table th {
    width: 200px;
    font-weight: 600;
}

/* 테이블 스타일 개선 */
.jj-admin-center-form-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.jj-admin-center-form-table thead th {
    background: #f6f7f7;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: #1d2327;
    border-bottom: 2px solid #dcdcde;
}

.jj-admin-center-form-table tbody td,
.jj-admin-center-form-table tbody th {
    padding: 12px;
    border-bottom: 1px solid #dcdcde;
    vertical-align: middle;
}

.jj-admin-center-form-table tbody tr:hover {
    background: #f9f9f9;
}

/* 반응형 */
@media screen and (max-width: 1400px) {
    .jj-admin-center-2panel {
        grid-template-columns: 300px 1fr;
    }

    .jj-admin-center-action-sidebar {
        position: relative;
        top: auto;
        right: auto;
        width: 100%;
        margin-top: 25px;
    }
}

@media screen and (max-width: 1200px) {
    .jj-admin-center-2panel {
        grid-template-columns: 1fr;
        height: auto;
    }

    .jj-admin-center-left-panel {
        max-height: 300px;
    }
}

/* 로딩 상태 */
.jj-admin-center-loading {
    text-align: center;
    padding: 40px;
    color: #646970;
}

.jj-admin-center-loading .spinner {
    float: none;
    margin: 0 auto;
}

/* 빈 상태 */
.jj-admin-center-empty {
    text-align: center;
    padding: 60px 20px;
    color: #646970;
}

.jj-admin-center-empty-icon {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

.jj-admin-center-empty-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #1d2327;
}

.jj-admin-center-empty-desc {
    font-size: 13px;
    color: #646970;
}

/* 색상 입력 필드 */
.jj-admin-center-color-input {
    display: flex;
    align-items: center;
    gap: 10px;
}

.jj-admin-center-color-input input[type="text"] {
    width: 100px;
}

.jj-admin-center-color-preview {
    width: 40px;
    height: 30px;
    border: 1px solid #dcdcde;
    border-radius: 3px;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.jj-admin-center-color-preview:hover {
    border-color: #2271b1;
    box-shadow: 0 0 0 2px rgba(34, 113, 177, 0.2);
}

/* [v6.3.0] 색상 히스토리 */
.jj-color-history-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.jj-color-history-chip {
    width: 32px;
    height: 32px;
    border: 2px solid #c3c4c7;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    flex-shrink: 0;
}

.jj-color-history-chip:hover {
    border-color: #2271b1;
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    z-index: 10;
}

.jj-color-history-chip.active {
    border-color: #2271b1;
    border-width: 3px;
    box-shadow: 0 0 0 2px rgba(34, 113, 177, 0.2);
}

.jj-color-history-chip[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #1d2327;
    color: #fff;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 11px;
    white-space: nowrap;
    margin-bottom: 5px;
    z-index: 1000;
}

.jj-color-history-chip[title]:hover::before {
    content: '';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 4px solid transparent;
    border-top-color: #1d2327;
    margin-bottom: 1px;
    z-index: 1000;
}

/* [v6.3.0] 변경사항 미리보기 모달 */
.jj-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.jj-modal-content {
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    max-height: 90vh;
}

.jj-modal-header {
    padding: 20px 25px;
    border-bottom: 1px solid #c3c4c7;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.jj-modal-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
}

.jj-modal-close {
    background: none;
    border: none;
    padding: 5px;
    cursor: pointer;
    color: #50575e;
    font-size: 20px;
    line-height: 1;
}

.jj-modal-close:hover {
    color: #1d2327;
}

.jj-modal-body {
    padding: 25px;
    flex: 1;
    overflow-y: auto;
}

.jj-changes-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.jj-change-item {
    padding: 15px;
    background: #f6f7f7;
    border-left: 4px solid #2271b1;
    border-radius: 4px;
}

.jj-change-item-title {
    font-weight: 600;
    margin-bottom: 8px;
    color: #1d2327;
}

.jj-change-item-detail {
    font-size: 13px;
    color: #646970;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.jj-change-item-detail strong {
    color: #1d2327;
}

.jj-change-old-value {
    color: #d63638;
    text-decoration: line-through;
}

.jj-change-new-value {
    color: #00a32a;
    font-weight: 600;
}

.jj-modal-footer {
    padding: 20px 25px;
    border-top: 1px solid #c3c4c7;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* [v6.3.0] 모바일 최적화 강화 */
@media screen and (max-width: 960px) {
    .jj-admin-center-wrap {
        margin: 10px 0;
        padding: 0 10px;
    }
    
    .jj-admin-center-header {
        flex-direction: column;
        gap: 15px;
        padding: 15px;
    }
    
    .jj-admin-center-header h1 {
        font-size: 20px;
    }
    
    .jj-admin-center-tabs {
        flex-wrap: wrap;
        padding: 0 10px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .jj-admin-center-tabs li a {
        padding: 10px 15px;
        font-size: 13px;
        white-space: nowrap;
    }
    
    .jj-admin-center-tab-content {
        padding: 15px;
    }
    
    .jj-admin-center-2panel {
        grid-template-columns: 1fr;
        gap: 15px;
        height: auto;
        min-height: auto;
    }
    
    .jj-admin-center-left-panel,
    .jj-admin-center-right-panel {
        max-height: 400px;
    }
    
    .jj-admin-center-sidebar {
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .jj-admin-center-sidebar.jj-sidebar-open {
        transform: translateX(0);
    }
    
    /* 터치 친화적 버튼 크기 */
    .button {
        min-height: 44px;
        padding: 0 20px;
        font-size: 14px;
    }
    
    .jj-admin-center-color-preview {
        width: 50px;
        height: 50px;
    }
    
    .jj-color-history-chip {
        width: 40px;
        height: 40px;
    }
    
    /* 모달 모바일 최적화 */
    .jj-modal-content {
        margin: 10px;
        max-width: calc(100% - 20px);
        max-height: calc(100vh - 20px);
    }
    
    .jj-modal-header,
    .jj-modal-body,
    .jj-modal-footer {
        padding: 15px;
    }
    
    .jj-modal-footer {
        flex-direction: column;
    }
    
    .jj-modal-footer .button {
        width: 100%;
        margin: 5px 0;
    }
}

/* [v6.3.0] 터치 제스처 지원 */
.jj-admin-center-menu-item,
.jj-admin-center-submenu-item {
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}

.jj-admin-center-tabs a {
    -webkit-tap-highlight-color: rgba(34, 113, 177, 0.1);
    touch-action: manipulation;
}

.jj-admin-center-sidebar-nav a {
    -webkit-tap-highlight-color: rgba(34, 113, 177, 0.1);
    touch-action: manipulation;
    min-height: 44px;
}

/* [v6.3.0] 스와이프 제스처 힌트 */
@media (hover: none) and (pointer: coarse) {
    .jj-admin-center-sidebar {
        touch-action: pan-y;
    }
    
    .jj-admin-center-tabs {
        touch-action: pan-x;
        -webkit-overflow-scrolling: touch;
    }
}

/* 드래그 앤 드롭 스타일 */
.jj-admin-center-menu-item.ui-sortable-helper {
    background: #e8f4f8;
    border-color: #2271b1;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    opacity: 0.9;
    cursor: move;
}

.jj-admin-center-submenu-item.ui-sortable-helper {
    background: #f0f6fc;
    border-color: #2271b1;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    opacity: 0.9;
    cursor: move;
}

.jj-sortable-placeholder {
    background: #e8f4f8;
    border: 2px dashed #2271b1;
    border-radius: 3px;
    margin-bottom: 5px;
    min-height: 40px;
    visibility: visible !important;
}

.jj-admin-center-menu-item-handle {
    cursor: move !important;
    opacity: 0.5;
    transition: opacity 0.2s;
    pointer-events: auto !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
    touch-action: none !important;
}

.jj-admin-center-menu-item:hover .jj-admin-center-menu-item-handle,
.jj-admin-center-submenu-item:hover .jj-admin-center-menu-item-handle {
    opacity: 1;
}

/* 서브메뉴 스타일 */
.jj-admin-center-submenu-list {
    margin-top: 10px;
    padding-left: 20px;
}

.jj-admin-center-submenu-item {
    padding: 8px 10px;
    margin-bottom: 3px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 3px;
    font-size: 12px;
    display: flex;
    align-items: center;
    cursor: pointer;
    position: relative;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.jj-admin-center-submenu-item.ui-sortable-helper {
    cursor: move !important;
    z-index: 99999 !important;
}

.jj-admin-center-submenu-item.ui-sortable-helper * {
    pointer-events: none;
}

.jj-admin-center-submenu-item.jj-dragging {
    opacity: 0.8;
}

.jj-admin-center-submenu-item.jj-dragging * {
    pointer-events: none;
}

.jj-admin-center-submenu-item:hover {
    background: #f0f6fc;
    border-color: #2271b1;
}

/* 순서 번호 스타일 */
.jj-admin-center-menu-item-order {
    display: inline-block;
    min-width: 25px;
    text-align: center;
    font-weight: bold;
    color: #2271b1;
    background: #e8f4f8;
    padding: 2px 6px;
    border-radius: 3px;
    margin-right: 8px;
    font-size: 12px;
}

/* 순서 컨트롤 스타일 */
.jj-order-control {
    display: flex;
    align-items: center;
    gap: 10px;
}

.jj-order-display {
    margin-left: 10px;
    font-weight: bold;
    color: #2271b1;
}

.jj-order-number {
    font-size: 14px;
}

/* [v3.8.0 신규] 라이센스 관리 스타일 */
.jj-license-settings {
    max-width: 900px;
}

.jj-license-current-status {
    margin-bottom: 30px;
}

.jj-license-current-status h4 {
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 16px;
    font-weight: 600;
}

.jj-license-input-form {
    margin-bottom: 30px;
}

.jj-license-input-form h4 {
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 16px;
    font-weight: 600;
}

.jj-license-actions h4 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 16px;
    font-weight: 600;
}

#jj-license-key-input {
    font-family: 'Courier New', monospace;
    font-size: 13px;
    letter-spacing: 0.5px;
}

#jj-license-message {
    margin-top: 15px;
    padding: 12px 15px;
    border-radius: 4px;
    font-size: 14px;
    line-height: 1.6;
    display: none;
}

#jj-license-message.success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

#jj-license-message.error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.jj-license-type-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #fff;
}

.jj-license-type-badge.jj-license-type-free {
    background: #e5e5e5;
    color: #50575e;
}

.jj-license-type-badge.jj-license-type-basic {
    background: #2271b1;
}

.jj-license-type-badge.jj-license-type-prem {
    background: #0073aa;
}

.jj-license-type-badge.jj-license-type-unlim {
    background: #8b5cf6;
}

.jj-status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #fff;
}

.jj-status-badge.active {
    background: #00a32a;
}

.jj-status-badge.inactive {
    background: #d63638;
}

.jj-license-spinner {
    float: none;
    margin-left: 10px;
    vertical-align: middle;
}

.jj-copy-license-key-display {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* [v5.0.0] 섹션/탭 활성화/비활성화 UI 개선 */
.jj-section-disable-warning {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* 비활성화된 섹션 행 스타일 */
.jj-section-row-disabled {
    opacity: 0.6;
    background-color: #f9f9f9;
    position: relative;
}

.jj-section-row-disabled::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background-color: #d63638;
}

.jj-section-row-disabled th,
.jj-section-row-disabled td {
    color: #646970;
}

.jj-section-row-disabled code {
    background-color: #e5e5e5;
    color: #8c8f94;
}

/* 비활성화된 탭 행 스타일 */
.jj-tab-row-disabled {
    opacity: 0.6;
    background-color: #f5f5f5;
}

.jj-tab-row-disabled th,
.jj-tab-row-disabled td {
    color: #8c8f94;
}

.jj-tab-row-disabled code {
    background-color: #e5e5e5;
    color: #8c8f94;
}

/* 비활성화 아이콘 */
.jj-disabled-icon {
    display: inline-block;
    width: 16px;
    height: 16px;
    margin-left: 6px;
    vertical-align: middle;
    opacity: 0.6;
}

.jj-disabled-icon::before {
    content: '🚫';
    font-size: 12px;
}

/* 활성화 아이콘 */
.jj-enabled-icon {
    display: inline-block;
    width: 16px;
    height: 16px;
    margin-left: 6px;
    vertical-align: middle;
    opacity: 0.8;
}

.jj-enabled-icon::before {
    content: '✓';
    color: #00a32a;
    font-weight: bold;
    font-size: 14px;
}

/* 일괄 작업 버튼 그룹 */
.jj-bulk-actions {
    display: flex;
    gap: 8px;
    margin-bottom: 15px;
    padding: 10px;
    background: #f6f7f7;
    border: 1px solid #dcdcde;
    border-radius: 4px;
}

.jj-bulk-actions .button {
    font-size: 12px;
    padding: 4px 10px;
    height: 28px;
    line-height: 20px;
}

/* 검색/필터 영역 */
.jj-section-filter {
    margin-bottom: 15px;
    padding: 10px;
    background: #f6f7f7;
    border: 1px solid #dcdcde;
    border-radius: 4px;
}

.jj-section-filter input[type="search"] {
    width: 100%;
    max-width: 400px;
    padding: 8px 12px;
    border: 1px solid #8c8f94;
    border-radius: 3px;
    font-size: 13px;
}

.jj-section-filter input[type="search"]:focus {
    border-color: #2271b1;
    box-shadow: 0 0 0 1px #2271b1;
    outline: none;
}

/* 필터링된 항목 숨김 */
.jj-section-row-filtered {
    display: none;
}

/* 변경사항 감지 표시 */
.jj-has-changes {
    position: relative;
}

.jj-has-changes::after {
    content: '●';
    position: absolute;
    top: 5px;
    right: 5px;
    color: #f0b849;
    font-size: 8px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* 저장 버튼 상태 */
.jj-save-button.has-changes {
    background: #f0b849;
    border-color: #f0b849;
    color: #1d2327;
    font-weight: 600;
    animation: pulse-button 2s infinite;
}

.jj-save-button.has-changes:hover {
    background: #dba617;
    border-color: #dba617;
}

@keyframes pulse-button {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(240, 184, 73, 0.7);
    }
    50% {
        box-shadow: 0 0 0 4px rgba(240, 184, 73, 0);
    }
}

/* 툴팁 스타일 */
.jj-tooltip {
    position: relative;
    display: inline-block;
    cursor: help;
}

.jj-tooltip .dashicons {
    font-size: 16px;
    color: #646970;
    vertical-align: middle;
}

.jj-tooltip:hover .dashicons {
    color: #2271b1;
}

.jj-tooltip-content {
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-bottom: 8px;
    padding: 8px 12px;
    background: #1d2327;
    color: #fff;
    font-size: 12px;
    line-height: 1.5;
    white-space: nowrap;
    border-radius: 4px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s;
    z-index: 1000;
    max-width: 250px;
    white-space: normal;
}

.jj-tooltip-content::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: #1d2327;
}

.jj-tooltip:hover .jj-tooltip-content {
    opacity: 1;
    pointer-events: auto;
}

/* 되돌리기 버튼 */
.jj-undo-button {
    opacity: 0.5;
    pointer-events: none;
    transition: all 0.2s;
}

.jj-undo-button.has-history {
    opacity: 1;
    pointer-events: auto;
}

.jj-undo-button.has-history:hover {
    opacity: 0.8;
}





```

Toast 알림 시스템을 개선 중입니다.


```javascript
/**
 * JJ Common Utilities
 * 
 * Phase 8.1: 공통 JavaScript 유틸리티 함수 통합
 * 중복 코드 제거 및 재사용성 향상
 * 
 * @since 8.1.0
 */

(function($) {
    'use strict';
    
    // 전역 네임스페이스
    window.JJUtils = window.JJUtils || {};
    
    /**
     * Toast 알림 표시 (통합 및 개선)
     * [Phase 8.3.3] 개선: 애니메이션, 아이콘, 스택 관리
     * 
     * @param {string} message 메시지
     * @param {string} type 타입 (success, error, warning, info)
     * @param {object} options 옵션 {duration, persistent, icon, action}
     */
    JJUtils.showToast = function(message, type, options) {
        type = type || 'info';
        options = options || {};
        
        const duration = options.duration || 5000;
        const persistent = options.persistent || false;
        const icon = options.icon !== undefined ? options.icon : true;
        const action = options.action || null;
        
        // WordPress admin notice 스타일 활용
        const noticeClass = type === 'error' ? 'notice-error' : 
                          type === 'success' ? 'notice-success' :
                          type === 'warning' ? 'notice-warning' : 'notice-info';
        
        // 아이콘
        let iconHtml = '';
        if (icon) {
            const iconClass = type === 'success' ? 'dashicons-yes-alt' :
                            type === 'error' ? 'dashicons-warning' :
                            type === 'warning' ? 'dashicons-info' : 'dashicons-info';
            iconHtml = '<span class="dashicons ' + iconClass + '" style="vertical-align:middle; margin-right:8px; font-size:18px;"></span>';
        }
        
        // 액션 버튼
        let actionHtml = '';
        if (action && action.label) {
            actionHtml = '<button type="button" class="button button-small" style="margin-left:15px;" data-action="' + (action.name || '') + '">' + action.label + '</button>';
        }
        
        const $toast = $('<div class="notice ' + noticeClass + ' is-dismissible jj-toast" style="position:fixed;top:' + (32 + (JJUtils.toastStack.length * 70)) + 'px;right:20px;z-index:999999;min-width:300px;max-width:500px;box-shadow:0 2px 10px rgba(0,0,0,0.2);transform:translateX(400px);transition:transform 0.3s ease;"><p style="margin:0;display:flex;align-items:center;">' + 
            iconHtml + 
            '<span style="flex:1;">' + $('<div>').text(message).html() + '</span>' +
            actionHtml +
            '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
        
        $('body').append($toast);
        
        // 스택에 추가
        JJUtils.toastStack.push($toast);
        
        // 슬라이드 인 애니메이션
        setTimeout(function() {
            $toast.css('transform', 'translateX(0)');
        }, 10);
        
        // 액션 버튼 이벤트
        if (action && action.callback) {
            $toast.on('click', '[data-action]', function(e) {
                e.preventDefault();
                if (typeof action.callback === 'function') {
                    action.callback();
                }
                JJUtils.closeToast($toast);
            });
        }
        
        // 자동 닫힘
        if (!persistent && duration > 0) {
            const timeoutId = setTimeout(function() {
                JJUtils.closeToast($toast);
            }, duration);
            $toast.data('timeout-id', timeoutId);
        }
        
        // 수동 닫기
        $toast.on('click', '.notice-dismiss', function() {
            JJUtils.closeToast($toast);
        });
        
        // 호버 시 자동 닫힘 일시 정지
        $toast.on('mouseenter', function() {
            const timeoutId = $toast.data('timeout-id');
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
        }).on('mouseleave', function() {
            if (!persistent && duration > 0) {
                const timeoutId = setTimeout(function() {
                    JJUtils.closeToast($toast);
                }, duration);
                $toast.data('timeout-id', timeoutId);
            }
        });
        
        return $toast;
    };
    
    /**
     * Toast 스택 관리
     */
    JJUtils.toastStack = [];
    
    /**
     * Toast 닫기
     */
    JJUtils.closeToast = function($toast) {
        if (!$toast || !$toast.length) return;
        
        const index = JJUtils.toastStack.indexOf($toast);
        if (index > -1) {
            JJUtils.toastStack.splice(index, 1);
        }
        
        // 타임아웃 정리
        const timeoutId = $toast.data('timeout-id');
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        
        // 슬라이드 아웃 애니메이션
        $toast.css('transform', 'translateX(400px)');
        setTimeout(function() {
            $toast.remove();
            
            // 나머지 토스트 위치 재조정
            JJUtils.toastStack.forEach(function($item, idx) {
                $item.css('top', (32 + (idx * 70)) + 'px');
            });
        }, 300);
    };
    
    /**
     * 모든 Toast 닫기
     */
    JJUtils.closeAllToasts = function() {
        JJUtils.toastStack.slice().forEach(function($toast) {
            JJUtils.closeToast($toast);
        });
    };
    
    /**
     * 표준화된 성공 메시지
     */
    JJUtils.showSuccess = function(message, options) {
        return JJUtils.showToast(message, 'success', options);
    };
    
    /**
     * 표준화된 오류 메시지
     */
    JJUtils.showError = function(message, options) {
        return JJUtils.showToast(message, 'error', Object.assign({ duration: 7000 }, options || {}));
    };
    
    /**
     * 표준화된 경고 메시지
     */
    JJUtils.showWarning = function(message, options) {
        return JJUtils.showToast(message, 'warning', options);
    };
    
    /**
     * 표준화된 정보 메시지
     */
    JJUtils.showInfo = function(message, options) {
        return JJUtils.showToast(message, 'info', options);
    };
    
    /**
     * Debounce 함수 (통합)
     */
    JJUtils.debounce = function(func, wait, immediate) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };
    
    /**
     * Throttle 함수
     */
    JJUtils.throttle = function(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(function() {
                    inThrottle = false;
                }, limit);
            }
        };
    };
    
    /**
     * 색상 포맷팅 (HEX 정규화)
     */
    JJUtils.formatColor = function(color) {
        if (!color) return '';
        
        // HEX 색상 정규화
        if (/^#?([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/.test(color)) {
            color = color.replace('#', '');
            if (color.length === 3) {
                // 3자리 HEX를 6자리로 확장
                color = color.split('').map(function(c) { return c + c; }).join('');
            }
            return '#' + color.toUpperCase();
        }
        
        return color;
    };
    
    /**
     * 안전한 AJAX 요청 (에러 처리 포함)
     */
    JJUtils.ajaxRequest = function(options) {
        const defaults = {
            url: jjAdminCenter ? jjAdminCenter.ajax_url : ajaxurl,
            type: 'POST',
            data: {},
            beforeSend: function() {},
            success: function() {},
            error: function() {},
            complete: function() {}
        };
        
        const opts = $.extend({}, defaults, options);
        
        // Nonce 자동 추가
        if (jjAdminCenter && jjAdminCenter.nonce && !opts.data.security && !opts.data.nonce) {
            opts.data.security = jjAdminCenter.nonce;
        }
        
        return $.ajax(opts)
            .done(function(response) {
                if (response && response.success) {
                    opts.success(response.data, response, opts);
                } else {
                    const message = (response && response.data && response.data.message) 
                        ? response.data.message 
                        : '요청 처리 중 오류가 발생했습니다.';
                    JJUtils.showToast(message, 'error');
                    opts.error(response, 'error', message);
                }
            })
            .fail(function(xhr, status, error) {
                JJUtils.showToast('서버 통신 오류: ' + error, 'error');
                opts.error(xhr, status, error);
            })
            .always(function() {
                opts.complete();
            });
    };
    
    /**
     * 문서 준비 이벤트 래퍼 (중복 방지)
     */
    JJUtils.onReady = function(callback) {
        if (document.readyState === 'loading') {
            $(document).ready(callback);
        } else {
            callback();
        }
    };
    
    /**
     * 로컬 스토리지 헬퍼
     */
    JJUtils.storage = {
        set: function(key, value) {
            try {
                localStorage.setItem('jj_' + key, JSON.stringify(value));
                return true;
            } catch (e) {
                return false;
            }
        },
        get: function(key, defaultValue) {
            try {
                const item = localStorage.getItem('jj_' + key);
                return item ? JSON.parse(item) : defaultValue;
            } catch (e) {
                return defaultValue;
            }
        },
        remove: function(key) {
            try {
                localStorage.removeItem('jj_' + key);
                return true;
            } catch (e) {
                return false;
            }
        },
        clear: function() {
            try {
                Object.keys(localStorage).forEach(function(key) {
                    if (key.startsWith('jj_')) {
                        localStorage.removeItem(key);
                    }
                });
                return true;
            } catch (e) {
                return false;
            }
        }
    };
    
    /**
     * URL 파라미터 파싱
     */
    JJUtils.getUrlParams = function() {
        const params = {};
        const queryString = window.location.search.substring(1);
        const pairs = queryString.split('&');
        
        pairs.forEach(function(pair) {
            if (pair) {
                const parts = pair.split('=');
                params[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1] || '');
            }
        });
        
        return params;
    };
    
    /**
     * 복사 기능 (클립보드)
     */
    JJUtils.copyToClipboard = function(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            return navigator.clipboard.writeText(text).then(function() {
                return true;
            }).catch(function() {
                return false;
            });
        } else {
            // 폴백: textarea 사용
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            
            try {
                document.execCommand('copy');
                document.body.removeChild(textarea);
                return Promise.resolve(true);
            } catch (e) {
                document.body.removeChild(textarea);
                return Promise.resolve(false);
            }
        }
    };
    
})(jQuery);

```

Undo 기능을 팔레트/버튼/폼/링크 전체로 확장하고, 적용 전/후 diff 요약을 추가 중입니다.


```javascript
/**
 * [Phase 8.4] 적용 전/후 요약(diff) + Undo 시스템 확장
 * 
 * 전체 코어 설정(팔레트/버튼/폼/링크)에 대한 Undo 기능 제공
 */

(function($) {
    'use strict';

    const UndoSystem = {
        snapshots: [],
        maxSnapshots: 10,
        currentSnapshotIndex: -1,

        /**
         * 현재 모든 설정 스냅샷 생성
         */
        captureSnapshot: function(label) {
            const snapshot = {
                timestamp: Date.now(),
                label: label || '변경',
                data: this.getAllSettings()
            };

            // 현재 인덱스 이후의 스냅샷 제거 (재실행 시)
            if (this.currentSnapshotIndex < this.snapshots.length - 1) {
                this.snapshots = this.snapshots.slice(0, this.currentSnapshotIndex + 1);
            }

            // 새 스냅샷 추가
            this.snapshots.push(snapshot);

            // 최대 개수 제한
            if (this.snapshots.length > this.maxSnapshots) {
                this.snapshots.shift();
            } else {
                this.currentSnapshotIndex++;
            }

            // localStorage에 저장 (페이지 새로고침 시 복원 가능)
            this.saveToStorage();

            return snapshot;
        },

        /**
         * 모든 설정 가져오기
         */
        getAllSettings: function() {
            const $form = $('#jj-admin-center-form, #jj-style-guide-form');
            if (!$form.length) return {};

            const settings = {};

            // 팔레트 설정
            $form.find('[name*="palettes"], [data-setting-key*="palettes"]').each(function() {
                const $field = $(this);
                const name = $field.attr('name') || $field.data('setting-key') || '';
                if (name) {
                    if ($field.is(':checkbox, :radio')) {
                        settings[name] = $field.is(':checked') ? $field.val() : '';
                    } else {
                        settings[name] = $field.val();
                    }
                }
            });

            // 버튼 설정
            $form.find('[name*="btn"], [data-setting-key*="btn"], [name*="button"], [data-setting-key*="button"]').each(function() {
                const $field = $(this);
                const name = $field.attr('name') || $field.data('setting-key') || '';
                if (name) {
                    settings[name] = $field.val();
                }
            });

            // 폼 설정
            $form.find('[name*="form"], [data-setting-key*="form"], [name*="field"], [data-setting-key*="field"]').each(function() {
                const $field = $(this);
                const name = $field.attr('name') || $field.data('setting-key') || '';
                if (name) {
                    settings[name] = $field.val();
                }
            });

            // 링크 설정
            $form.find('[name*="link"], [data-setting-key*="link"]').each(function() {
                const $field = $(this);
                const name = $field.attr('name') || $field.data('setting-key') || '';
                if (name) {
                    settings[name] = $field.val();
                }
            });

            return settings;
        },

        /**
         * 스냅샷 복원
         */
        restoreSnapshot: function(snapshot) {
            if (!snapshot || !snapshot.data) return false;

            const $form = $('#jj-admin-center-form, #jj-style-guide-form');
            if (!$form.length) return false;

            let restoredCount = 0;

            Object.keys(snapshot.data).forEach(function(key) {
                const value = snapshot.data[key];
                
                // name 속성으로 찾기
                let $field = $form.find('[name="' + key + '"]');
                
                // data-setting-key로 찾기
                if (!$field.length) {
                    $field = $form.find('[data-setting-key="' + key + '"]');
                }

                if ($field.length) {
                    if ($field.is(':checkbox, :radio')) {
                        $field.prop('checked', $field.val() === value);
                    } else {
                        $field.val(value);
                        
                        // wpColorPicker 동기화
                        if ($field.closest('.wp-picker-container').length) {
                            try {
                                $field.wpColorPicker('color', value);
                            } catch (e) {}
                        }
                        
                        // change 이벤트 트리거
                        $field.trigger('change');
                    }
                    restoredCount++;
                }
            });

            // 미리보기 업데이트
            if (typeof updateInlinePreview === 'function') {
                updateInlinePreview();
            }

            return restoredCount > 0;
        },

        /**
         * Undo 실행
         */
        undo: function() {
            if (this.currentSnapshotIndex < 0) {
                return false;
            }

            const snapshot = this.snapshots[this.currentSnapshotIndex];
            if (this.restoreSnapshot(snapshot)) {
                this.currentSnapshotIndex--;
                this.saveToStorage();

                // diff 요약 표시
                this.showDiffSummary(snapshot, '복원됨');

                return true;
            }

            return false;
        },

        /**
         * Redo 실행
         */
        redo: function() {
            if (this.currentSnapshotIndex >= this.snapshots.length - 1) {
                return false;
            }

            this.currentSnapshotIndex++;
            const snapshot = this.snapshots[this.currentSnapshotIndex];
            
            if (this.restoreSnapshot(snapshot)) {
                this.saveToStorage();

                // diff 요약 표시
                this.showDiffSummary(snapshot, '재적용됨');

                return true;
            }

            return false;
        },

        /**
         * 변경사항 diff 요약 표시
         */
        showDiffSummary: function(snapshot, action) {
            if (!snapshot || !snapshot.data) return;

            const currentSettings = this.getAllSettings();
            const changes = [];

            // 변경된 항목 찾기
            Object.keys(snapshot.data).forEach(function(key) {
                const oldValue = snapshot.data[key];
                const newValue = currentSettings[key];

                if (oldValue !== newValue) {
                    changes.push({
                        key: key,
                        old: oldValue,
                        new: newValue,
                        label: UndoSystem.getFieldLabel(key)
                    });
                }
            });

            // Toast로 표시
            if (typeof JJUtils !== 'undefined' && JJUtils.showInfo) {
                const summary = changes.length > 0 
                    ? action + ': ' + changes.length + '개 항목 변경됨'
                    : action;
                
                JJUtils.showInfo(summary, {
                    duration: 3000,
                    action: changes.length > 0 ? {
                        label: '자세히',
                        callback: function() {
                            UndoSystem.showDetailedDiff(changes);
                        }
                    } : null
                });
            }
        },

        /**
         * 상세 diff 모달 표시
         */
        showDetailedDiff: function(changes) {
            if (!changes || changes.length === 0) return;

            let html = '<div class="jj-diff-modal" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;padding:20px;">';
            html += '<div style="background:#fff;border-radius:8px;max-width:600px;max-height:80vh;overflow:auto;box-shadow:0 4px 20px rgba(0,0,0,0.3);">';
            html += '<div style="padding:20px;border-bottom:1px solid #ddd;display:flex;justify-content:space-between;align-items:center;">';
            html += '<h3 style="margin:0;">변경사항 요약</h3>';
            html += '<button type="button" class="jj-close-diff" style="background:none;border:none;font-size:24px;cursor:pointer;">×</button>';
            html += '</div>';
            html += '<div style="padding:20px;">';
            
            changes.forEach(function(change) {
                html += '<div style="margin-bottom:15px;padding:12px;background:#f9f9f9;border-radius:4px;">';
                html += '<div style="font-weight:600;margin-bottom:8px;">' + change.label + '</div>';
                html += '<div style="display:flex;gap:15px;font-size:13px;">';
                html += '<div style="flex:1;"><strong>이전:</strong> <span style="color:#d63638;">' + (change.old || '(없음)') + '</span></div>';
                html += '<div style="flex:1;"><strong>변경:</strong> <span style="color:#2271b1;">' + (change.new || '(없음)') + '</span></div>';
                html += '</div>';
                html += '</div>';
            });

            html += '</div></div></div>';

            const $modal = $(html);
            $('body').append($modal);

            $modal.find('.jj-close-diff, .jj-diff-modal').on('click', function(e) {
                if (e.target === this) {
                    $modal.fadeOut(300, function() {
                        $(this).remove();
                    });
                }
            });
        },

        /**
         * 필드 레이블 가져오기
         */
        getFieldLabel: function(key) {
            // 키를 사람이 읽기 쉬운 레이블로 변환
            const labels = {
                'palettes[brand][primary_color]': '브랜드 Primary 색상',
                'palettes[brand][secondary_color]': '브랜드 Secondary 색상',
                'palettes[system][text_color]': '시스템 텍스트 색상',
                'palettes[system][link_color]': '시스템 링크 색상',
            };

            if (labels[key]) {
                return labels[key];
            }

            // 키에서 추론
            return key
                .replace(/\[/g, ' - ')
                .replace(/\]/g, '')
                .replace(/palettes|btn|form|link/gi, function(match) {
                    const map = {
                        'palettes': '팔레트',
                        'btn': '버튼',
                        'form': '폼',
                        'link': '링크'
                    };
                    return map[match.toLowerCase()] || match;
                });
        },

        /**
         * localStorage에 저장
         */
        saveToStorage: function() {
            try {
                localStorage.setItem('jj_undo_snapshots', JSON.stringify({
                    snapshots: this.snapshots,
                    currentIndex: this.currentSnapshotIndex
                }));
            } catch (e) {
                // ignore
            }
        },

        /**
         * localStorage에서 복원
         */
        loadFromStorage: function() {
            try {
                const data = localStorage.getItem('jj_undo_snapshots');
                if (data) {
                    const parsed = JSON.parse(data);
                    this.snapshots = parsed.snapshots || [];
                    this.currentSnapshotIndex = parsed.currentIndex || -1;
                }
            } catch (e) {
                // ignore
            }
        },

        /**
         * 초기화
         */
        init: function() {
            // localStorage에서 복원
            this.loadFromStorage();

            // 프리셋/팔레트 적용 시 자동 스냅샷
            $(document).on('jj:palette:applied jj:preset:applied', function(e, label) {
                UndoSystem.captureSnapshot(label || '팔레트 적용');
                UndoSystem.updateUndoButton();
            });

            // Undo/Redo 버튼 생성
            this.createUndoButtons();
        },

        /**
         * Undo/Redo 버튼 생성
         */
        createUndoButtons: function() {
            const $wrap = $('.jj-admin-center-wrap');
            if (!$wrap.length) return;

            // 기존 버튼 제거
            $('.jj-undo-redo-buttons').remove();

            const $buttons = $('<div class="jj-undo-redo-buttons" style="position:fixed;bottom:20px;left:20px;z-index:999998;display:flex;gap:8px;">' +
                '<button type="button" class="button jj-undo-btn" title="이전으로 (Ctrl+Z)">' +
                '<span class="dashicons dashicons-undo"></span> Undo</button>' +
                '<button type="button" class="button jj-redo-btn" title="다시 실행 (Ctrl+Y)">' +
                '<span class="dashicons dashicons-redo"></span> Redo</button>' +
                '</div>');

            $('body').append($buttons);

            // 클릭 이벤트
            $buttons.find('.jj-undo-btn').on('click', function() {
                UndoSystem.undo();
                UndoSystem.updateUndoButton();
            });

            $buttons.find('.jj-redo-btn').on('click', function() {
                UndoSystem.redo();
                UndoSystem.updateUndoButton();
            });

            // 키보드 단축키
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
                    e.preventDefault();
                    UndoSystem.undo();
                    UndoSystem.updateUndoButton();
                } else if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) {
                    e.preventDefault();
                    UndoSystem.redo();
                    UndoSystem.updateUndoButton();
                }
            });

            this.updateUndoButton();
        },

        /**
         * Undo/Redo 버튼 상태 업데이트
         */
        updateUndoButton: function() {
            const $undoBtn = $('.jj-undo-btn');
            const $redoBtn = $('.jj-redo-btn');

            $undoBtn.prop('disabled', this.currentSnapshotIndex < 0);
            $redoBtn.prop('disabled', this.currentSnapshotIndex >= this.snapshots.length - 1);
        }
    };

    // 초기화
    $(document).ready(function() {
        UndoSystem.init();
    });

    // 전역 접근
    window.JJUndoSystem = UndoSystem;

})(jQuery);

```

```php
               // [Phase 8.3.2] 폼 UX 개선
               wp_enqueue_script(
                   'jj-form-enhancer',
                   $base_url . 'assets/js/jj-form-enhancer.js',
                   array( 'jquery' ),
                   $version,
                   true
               );
               
               // [Phase 8.4] Undo 시스템 확장
               wp_enqueue_script(
                   'jj-undosystem',
                   $base_url . 'assets/js/jj-undosystem.js',
                   array( 'jquery' ),
                   $version,
                   true
               );
```

```javascript
/**
 * JJ Style Guide - Palette Presets & Inline Preview
 *
 * 목표:
 * - 텍스트 위주의 팔레트 UI를 "보여주는" UI로 보강
 * - 2-Color 조합(Primary/Secondary) + 권장 System 팔레트까지 프리셋으로 제공
 * - 클릭 1~2번으로 즉시 적용 + 즉시 미리보기
 *
 * 주의:
 * - 기존 `jj-style-guide-editor.js`의 currentSettings/필드 바인딩 로직을 존중합니다.
 * - 실제 값 적용은 `data-setting-key` 필드 값을 변경(trigger change)하는 방식으로 통일합니다.
 */
(function($){
  'use strict';

  function normalizeHex(hex) {
    if (!hex || typeof hex !== 'string') return '';
    var h = hex.trim();
    if (!h) return '';
    if (h[0] !== '#') h = '#' + h;
    if (h.length === 4) {
      // #RGB -> #RRGGBB
      h = '#' + h[1] + h[1] + h[2] + h[2] + h[3] + h[3];
    }
    return h.toUpperCase();
  }

  function clamp(n, min, max) { return Math.max(min, Math.min(max, n)); }

  function hexToRgb(hex) {
    var h = normalizeHex(hex);
    if (!h || h.length !== 7) return null;
    return {
      r: parseInt(h.slice(1, 3), 16),
      g: parseInt(h.slice(3, 5), 16),
      b: parseInt(h.slice(5, 7), 16),
    };
  }

  function rgbToHex(r, g, b) {
    var rr = clamp(Math.round(r), 0, 255).toString(16).padStart(2, '0');
    var gg = clamp(Math.round(g), 0, 255).toString(16).padStart(2, '0');
    var bb = clamp(Math.round(b), 0, 255).toString(16).padStart(2, '0');
    return ('#' + rr + gg + bb).toUpperCase();
  }

  // amount: -1.0 ~ +1.0 (음수: 어둡게, 양수: 밝게)
  function shade(hex, amount) {
    var rgb = hexToRgb(hex);
    if (!rgb) return '';
    var t = amount < 0 ? 0 : 255;
    var p = Math.abs(amount);
    return rgbToHex(
      (t - rgb.r) * p + rgb.r,
      (t - rgb.g) * p + rgb.g,
      (t - rgb.b) * p + rgb.b
    );
  }

  function setField(settingKey, value) {
    if (!settingKey) return;
    var v = normalizeHex(value);
    var $field = $('#jj-style-guide-form').find('[data-setting-key="' + settingKey + '"]');
    if (!$field.length) return;

    $field.val(v).trigger('change');
    // wpColorPicker가 있으면 색상도 동기화
    if ($field.closest('.wp-picker-container').length) {
      try { $field.wpColorPicker('color', v); } catch (e) {}
    }
    // 카드 미리보기(기존 DOM)
    var $preview = $field.closest('.jj-control-group').find('.jj-color-preview').first();
    if ($preview.length) $preview.css('background-color', v);
  }

  function readField(settingKey, fallback) {
    var $field = $('#jj-style-guide-form').find('[data-setting-key="' + settingKey + '"]');
    var v = $field.length ? normalizeHex($field.val()) : '';
    return v || normalizeHex(fallback) || '';
  }

  function updateInlinePreview() {
    var $preview = $('#jj-palette-inline-preview');
    if (!$preview.length) return;

    var primary = readField('palettes[brand][primary_color]', '#2271B1');
    var primaryHover = readField('palettes[brand][primary_color_hover]', shade(primary, -0.12));
    var secondary = readField('palettes[brand][secondary_color]', '#444444');
    var secondaryHover = readField('palettes[brand][secondary_color_hover]', shade(secondary, -0.12));

    var bg = readField('palettes[system][site_bg]', '#F6F7F7');
    var surface = readField('palettes[system][content_bg]', '#FFFFFF');
    var text = readField('palettes[system][text_color]', '#1D2327');
    var link = readField('palettes[system][link_color]', primary);

    $preview.css('--jj-prev-bg', bg);
    $preview.css('--jj-prev-surface', surface);
    $preview.css('--jj-prev-text', text);
    $preview.css('--jj-prev-link', link);
    $preview.css('--jj-prev-primary', primary);
    $preview.css('--jj-prev-primary-hover', primaryHover);
    $preview.css('--jj-prev-secondary', secondary);
    $preview.css('--jj-prev-secondary-hover', secondaryHover);

    $preview.find('[data-jj-color="primary"]').text(primary);
    $preview.find('[data-jj-color="secondary"]').text(secondary);
    $preview.find('[data-jj-color="bg"]').text(bg);
    $preview.find('[data-jj-color="text"]').text(text);
  }

  $(document).ready(function(){
    var $mount = $('#jj-palette-presets');
    if (!$mount.length) return;

    var BASE_PRESETS = [
      {
        id: 'luxury-black-gold',
        name: 'Black & Gold',
        tags: ['럭셔리', '다크', '고대비', '프리미엄'],
        note: '고급·권위·집중(CTA 강함). 법률/컨설팅/하이엔드에 적합.',
        brand: { primary: '#C9A227', secondary: '#0B0D0F' },
        system: { site_bg: '#0B0D0F', content_bg: '#101418', text_color: '#EAEAEA', link_color: '#C9A227' }
      },
      {
        id: 'trust-navy-cyan',
        name: 'Navy & Cyan',
        tags: ['신뢰', '테크', '차분함'],
        note: '신뢰·안정·정교함. SaaS/데이터/기업 사이트에 적합.',
        brand: { primary: '#1F3C88', secondary: '#00B8D9' },
        system: { site_bg: '#F7FAFC', content_bg: '#FFFFFF', text_color: '#1D2327', link_color: '#1F3C88' }
      },
      {
        id: 'modern-coral-slate',
        name: 'Coral & Slate',
        tags: ['모던', '따뜻함', '크리에이티브'],
        note: '친근·활기. 브랜딩/포트폴리오/커머스에 적합.',
        brand: { primary: '#FF6B6B', secondary: '#334E68' },
        system: { site_bg: '#FFFFFF', content_bg: '#F7FAFC', text_color: '#102A43', link_color: '#334E68' }
      },
      {
        id: 'minimal-mono',
        name: 'Minimal Mono',
        tags: ['미니멀', '라이트', '가독성'],
        note: '콘텐츠 중심·가독성 강화. 블로그/매거진에 적합.',
        brand: { primary: '#111827', secondary: '#6B7280' },
        system: { site_bg: '#F9FAFB', content_bg: '#FFFFFF', text_color: '#111827', link_color: '#111827' }
      },
      {
        id: 'emerald-charcoal',
        name: 'Emerald & Charcoal',
        tags: ['헬스', '자연', '안정'],
        note: '안정·신선. 헬스/웰니스/교육에 적합.',
        brand: { primary: '#10B981', secondary: '#111827' },
        system: { site_bg: '#F7FAFC', content_bg: '#FFFFFF', text_color: '#111827', link_color: '#10B981' }
      },
      {
        id: 'royal-purple-ice',
        name: 'Royal Purple',
        tags: ['프리미엄', '창의', '브랜딩'],
        note: '독창·세련. 이벤트/크리에이티브 스튜디오에 적합.',
        brand: { primary: '#6D28D9', secondary: '#0EA5E9' },
        system: { site_bg: '#F8FAFC', content_bg: '#FFFFFF', text_color: '#0F172A', link_color: '#6D28D9' }
      },
      {
        id: 'warm-brown-cream',
        name: 'Brown & Cream',
        tags: ['따뜻함', '클래식', '브랜드'],
        note: '따뜻·신뢰. 카페/식품/라이프스타일에 적합.',
        brand: { primary: '#8B5E34', secondary: '#D4A373' },
        system: { site_bg: '#FFF7ED', content_bg: '#FFFFFF', text_color: '#1F2937', link_color: '#8B5E34' }
      },
      {
        id: 'contrast-blue-yellow',
        name: 'Blue & Yellow (CTA)',
        tags: ['고대비', '전환', 'CTA'],
        note: '버튼/링크가 눈에 띄게. 랜딩/광고 페이지에 적합.',
        brand: { primary: '#2563EB', secondary: '#F59E0B' },
        system: { site_bg: '#FFFFFF', content_bg: '#F8FAFC', text_color: '#0F172A', link_color: '#2563EB' }
      }
    ];

    var PRESETS = [];
    var bulkMode = false;
    var bulkSelected = {};
    var lastUndoSnapshot = null;
    var lastUndoLabel = '';

    function isAiPreset(p) {
      if (!p || typeof p !== 'object') return false;
      if (p.source === 'ai_extension') return true;
      return typeof p.id === 'string' && p.id.indexOf('ai-') === 0;
    }

    function sortAiPresets(a, b) {
      var ap = !!(a && a.pinned);
      var bp = !!(b && b.pinned);
      if (ap !== bp) return ap ? -1 : 1;
      var at = (a && a.created_at) ? String(a.created_at) : '';
      var bt = (b && b.created_at) ? String(b.created_at) : '';
      if (at === bt) return 0;
      return bt < at ? -1 : 1; // desc
    }

    function loadAiPresets() {
      var aiPresets = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
        ? window.jj_admin_params.ai_palette_presets
        : [];
      if (!aiPresets || !aiPresets.length) return [];

      var cleaned = [];
      aiPresets.forEach(function(p){
        if (!p || typeof p !== 'object') return;
        if (!p.id || !p.name || !p.brand || !p.system) return;

        // tags 정규화
        if (!Array.isArray(p.tags)) {
          p.tags = [];
        }
        if (p.tags.indexOf('AI') === -1) {
          p.tags.unshift('AI');
        }

        // note 보정
        if (!p.note) {
          p.note = 'AI로 저장된 팔레트';
        }

        // shape 보정(brand/system)
        p.brand = p.brand || {};
        p.system = p.system || {};
        cleaned.push(p);
      });

      cleaned.sort(sortAiPresets);
      return cleaned;
    }

    function normalizePresetColors(p) {
      p.brand.primary = normalizeHex(p.brand.primary);
      p.brand.secondary = normalizeHex(p.brand.secondary);
      p.brand.primary_hover = normalizeHex(p.brand.primary_hover || shade(p.brand.primary, -0.12));
      p.brand.secondary_hover = normalizeHex(p.brand.secondary_hover || shade(p.brand.secondary, -0.12));
      p.system.site_bg = normalizeHex(p.system.site_bg);
      p.system.content_bg = normalizeHex(p.system.content_bg);
      p.system.text_color = normalizeHex(p.system.text_color);
      p.system.link_color = normalizeHex(p.system.link_color || p.brand.primary);
      return p;
    }

    function rebuildPresets() {
      var ai = loadAiPresets();
      PRESETS = ai.concat(BASE_PRESETS);
      PRESETS.forEach(function(p){
        try { normalizePresetColors(p); } catch (e) {}
      });
    }

    rebuildPresets();

    var selectedId = null;

    function getBulkSelectedIds() {
      return Object.keys(bulkSelected).filter(function(id){ return !!bulkSelected[id]; });
    }

    function clearBulkSelection() {
      bulkSelected = {};
    }

    function toggleBulkSelected(id, on) {
      if (!id) return;
      bulkSelected[id] = !!on;
    }

    function isBulkSelected(id) {
      return !!bulkSelected[id];
    }

    function render(filterText) {
      var q = (filterText || '').toLowerCase().trim();
      $mount.empty();

      var $toolbar = $('<div class="jj-preset-toolbar"></div>');
      var $search = $('<input type="search" class="jj-preset-search" placeholder="프리셋 검색 (예: 럭셔리, 신뢰, 다크)"/>');
      $search.val(filterText || '');

      var $includeSystem = $('<label class="jj-preset-include-system"><input type="checkbox" checked> 시스템 팔레트도 함께 적용</label>');
      var $includeComponents = $('<label class="jj-preset-include-system"><input type="checkbox" checked> 버튼/폼/링크 자동 맞추기</label>');

      // AI 프리셋 관리(내보내기/불러오기)
      var aiCount = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
        ? window.jj_admin_params.ai_palette_presets.length
        : 0;
      var $aiTools = $('<div class="jj-preset-ai-tools"></div>');
      if (aiCount > 0) {
        $aiTools.append($('<span class="jj-preset-ai-count"></span>').text('AI 프리셋: ' + aiCount + '개'));
      }
      var $bulkToggle = $('<label class="jj-preset-ai-bulk-toggle"><input type="checkbox"> 대량 선택</label>');
      $bulkToggle.find('input').prop('checked', !!bulkMode);
      var $bulkCount = $('<span class="jj-preset-ai-bulk-count"></span>');
      var $bulkSelectVisible = $('<button type="button" class="button jj-preset-ai-bulk-select-visible" style="display:none;">보이는 항목 전체 선택</button>');
      var $bulkInvert = $('<button type="button" class="button jj-preset-ai-bulk-invert" style="display:none;">선택 반전</button>');
      var $bulkPin = $('<button type="button" class="button jj-preset-ai-bulk-pin" style="display:none;">선택 고정</button>');
      var $bulkUnpin = $('<button type="button" class="button jj-preset-ai-bulk-unpin" style="display:none;">선택 해제</button>');
      var $bulkDelete = $('<button type="button" class="button button-link-delete jj-preset-ai-bulk-delete" style="display:none;">선택 삭제</button>');
      var $bulkExport = $('<button type="button" class="button jj-preset-ai-bulk-export" style="display:none;">선택 내보내기</button>');
      var $bulkShare = $('<button type="button" class="button jj-preset-ai-bulk-share" style="display:none;">선택 공유 코드</button>');
      var $bulkClear = $('<button type="button" class="button jj-preset-ai-bulk-clear" style="display:none;">선택 해제</button>');

      var $aiExportAll = $('<button type="button" class="button jj-preset-ai-export-all">AI 전체 내보내기</button>');
      var $aiImport = $('<button type="button" class="button jj-preset-ai-import">AI 불러오기</button>');
      var $aiImportInput = $('<input type="file" accept=".json" class="jj-preset-ai-import-input" style="display:none;">');
      var $aiImportCode = $('<button type="button" class="button jj-preset-ai-import-code">코드로 가져오기</button>');
      $aiTools.append($bulkToggle).append($bulkCount).append($bulkSelectVisible).append($bulkInvert).append($bulkPin).append($bulkUnpin).append($bulkDelete).append($bulkExport).append($bulkShare).append($bulkClear);
      $aiTools.append($aiExportAll).append($aiImport).append($aiImportCode).append($aiImportInput);

      $toolbar.append($search).append($includeSystem).append($includeComponents).append($aiTools);
      $mount.append($toolbar);

      var $grid = $('<div class="jj-preset-grid" role="list"></div>');
      var shown = 0;

      PRESETS.forEach(function(p){
        var hay = (p.name + ' ' + p.tags.join(' ') + ' ' + p.note).toLowerCase();
        if (q && hay.indexOf(q) === -1) return;
        shown++;

        var isActive = selectedId === p.id;
        var isAI = isAiPreset(p);
        var isPinned = !!(p && p.pinned);

        var $card = $('<div class="jj-preset-card" role="listitem"></div>');
        $card.toggleClass('is-active', !!isActive);
        $card.toggleClass('is-ai', !!isAI);
        $card.toggleClass('is-pinned', !!isPinned);
        $card.attr('data-preset-id', p.id);

        var swatches = [
          { c: p.brand.primary, label: 'Primary' },
          { c: p.brand.secondary, label: 'Secondary' },
          { c: p.system.site_bg, label: 'BG' },
          { c: p.system.text_color, label: 'Text' }
        ];

        var $sw = $('<div class="jj-preset-swatches" aria-hidden="true"></div>');
        swatches.forEach(function(s){
          var $s = $('<span class="jj-preset-swatch"></span>');
          $s.css('background-color', s.c);
          $s.attr('title', s.label + ': ' + s.c);
          $sw.append($s);
        });

        var $meta = $('<div class="jj-preset-meta"></div>');
        $meta.append($('<div class="jj-preset-title"></div>').text(p.name));
        $meta.append($('<div class="jj-preset-tags"></div>').text(p.tags.join(' · ')));
        $meta.append($('<div class="jj-preset-note"></div>').text(p.note));

        var $main = $('<button type="button" class="jj-preset-card-main"></button>');
        $main.append($sw).append($meta);
        $card.append($main);

        // AI 프리셋: 관리 액션(핀/이름/삭제/복사/내보내기)
        if (isAI) {
          var $cardActions = $('<div class="jj-preset-card-actions" aria-label="AI 프리셋 관리"></div>');

          var $pin = $('<button type="button" class="jj-preset-action jj-preset-ai-pin"><span class="dashicons dashicons-admin-post"></span></button>');
          $pin.attr('title', isPinned ? '상단 고정 해제' : '상단 고정');
          $pin.attr('data-op', isPinned ? 'unpin' : 'pin');

          var $rename = $('<button type="button" class="jj-preset-action jj-preset-ai-rename"><span class="dashicons dashicons-edit"></span></button>');
          $rename.attr('title', '이름 변경');

          var $copy = $('<button type="button" class="jj-preset-action jj-preset-ai-copy"><span class="dashicons dashicons-admin-page"></span></button>');
          $copy.attr('title', '팔레트 JSON 복사');

          var $export = $('<button type="button" class="jj-preset-action jj-preset-ai-export"><span class="dashicons dashicons-download"></span></button>');
          $export.attr('title', '팔레트 JSON 다운로드');

          var $del = $('<button type="button" class="jj-preset-action jj-preset-ai-delete"><span class="dashicons dashicons-trash"></span></button>');
          $del.attr('title', '삭제');

          $cardActions.append($pin, $rename, $copy, $export, $del);
          $card.append($cardActions);
        }

        // Bulk selection checkbox (AI only)
        if (isAI && bulkMode) {
          var $bulkCheck = $('<label class="jj-preset-bulk-check" title="대량 선택"><input type="checkbox" class="jj-ai-bulk-checkbox"></label>');
          $bulkCheck.find('input').attr('data-id', p.id).prop('checked', isBulkSelected(p.id));
          $card.append($bulkCheck);
        }

        $grid.append($card);
      });

      if (shown === 0) {
        $grid.append($('<div class="jj-preset-empty"></div>').text('검색 결과가 없습니다.'));
      }

      var $actions = $('<div class="jj-preset-actions"></div>');
      var $apply = $('<button type="button" class="button button-primary jj-preset-apply" disabled>선택한 프리셋 적용</button>');
      var $undo = $('<button type="button" class="button jj-preset-undo" disabled>되돌리기</button>');
      var $hint = $('<span class="jj-preset-hint">클릭 → 미리보기 → 적용 (언제든 수정 가능)</span>');
      $actions.append($apply).append($undo).append($hint);

      $mount.append($grid).append($actions);

      // events
      $search.on('input', function(){ render($(this).val()); });

      $aiExportAll.on('click', function(){
        var list = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
          ? window.jj_admin_params.ai_palette_presets
          : [];
        if (!list.length) {
          alert('내보낼 AI 프리셋이 없습니다.');
          return;
        }
        var payload = {
          exported_at: new Date().toISOString(),
          items: list
        };
        try {
          var data = JSON.stringify(payload, null, 2);
          var blob = new Blob([data], { type: 'application/json' });
          var url = URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.href = url;
          a.download = 'ai-palette-presets-' + (new Date().toISOString().slice(0,10)) + '.json';
          document.body.appendChild(a);
          a.click();
          a.remove();
          setTimeout(function(){ URL.revokeObjectURL(url); }, 1000);
        } catch (e) {
          alert('내보내기에 실패했습니다.');
        }
      });

      $aiImport.on('click', function(){
        $aiImportInput.trigger('click');
      });

      $aiImportInput.on('change', function(){
        var file = this.files && this.files[0];
        if (!file) return;
        if (!file.name.toLowerCase().endsWith('.json')) {
          alert('JSON 파일만 업로드할 수 있습니다.');
          $(this).val('');
          return;
        }
        var reader = new FileReader();
        reader.onload = function(){
          var txt = String(reader.result || '');
          // Allow wrapped export format {items:[...]} or direct array/object
          var payloadTxt = txt;
          try {
            var parsed = JSON.parse(txt);
            if (parsed && parsed.items && Array.isArray(parsed.items)) {
              payloadTxt = JSON.stringify(parsed.items);
            }
          } catch (e) {
            // keep raw -> server will validate
          }

          if (!confirm('AI 팔레트 프리셋을 불러올까요? (중복 항목은 자동으로 건너뜁니다)')) {
            $(this).val('');
            return;
          }

          ajaxManageAiPreset('import', 'ai-import', null, payloadTxt).then(function(resp){
            if (!resp) return;
            showToast(resp.data.message || '가져오기 완료', 'success');
            render($search.val());
          });
        }.bind(this);
        reader.onerror = function(){
          alert('파일을 읽을 수 없습니다.');
          $(this).val('');
        }.bind(this);
        reader.readAsText(file);
      });

      function showToast(msg, kind) {
        try {
          $('.jj-preset-toast').remove();
          var $t = $('<div class="jj-preset-toast"></div>');
          $t.addClass(kind ? ('is-' + kind) : 'is-info');
          $t.text(msg || '');
          $mount.prepend($t);
          setTimeout(function(){
            $t.fadeOut(200, function(){ $(this).remove(); });
          }, 2600);
        } catch (e) {}
      }

      function ajaxManageAiPreset(op, id, name, payloadJson, ids) {
        var ajaxUrl = (window.jj_admin_params && window.jj_admin_params.ajax_url) ? window.jj_admin_params.ajax_url : (window.ajaxurl || '');
        var security = (window.jj_admin_params && window.jj_admin_params.nonce) ? window.jj_admin_params.nonce : '';
        if (!ajaxUrl || !security) {
          alert('AJAX 설정이 초기화되지 않았습니다.');
          return;
        }

        return $.ajax({
          url: ajaxUrl,
          type: 'POST',
          data: {
            action: 'jj_ai_palette_preset_manage',
            security: security,
            op: op,
            id: id,
            name: name || '',
            payload_json: payloadJson || '',
            ids: Array.isArray(ids) ? ids : []
          }
        }).then(function(resp){
          if (!resp || !resp.success) {
            alert((resp && resp.data && resp.data.message) ? resp.data.message : '요청 실패');
            return null;
          }
          // 갱신된 목록을 localize 영역에 반영 후 rebuild
          if (window.jj_admin_params) {
            window.jj_admin_params.ai_palette_presets = resp.data.items || [];
          }
          rebuildPresets();
          return resp;
        });
      }

      function copyToClipboard(text, fallbackLabel) {
        var t = String(text || '');
        if (!t) return;
        try {
          if (navigator && navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
            navigator.clipboard.writeText(t).then(function(){
              showToast('클립보드에 복사되었습니다.', 'success');
            }).catch(function(){
              window.prompt(fallbackLabel || '복사하세요:', t);
            });
            return;
          }
        } catch (e) {}
        window.prompt(fallbackLabel || '복사하세요:', t);
      }

      function downloadJson(filename, obj) {
        try {
          var data = JSON.stringify(obj, null, 2);
          var blob = new Blob([data], { type: 'application/json' });
          var url = URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.href = url;
          a.download = filename;
          document.body.appendChild(a);
          a.click();
          a.remove();
          setTimeout(function(){ URL.revokeObjectURL(url); }, 1000);
        } catch (e) {
          alert('다운로드에 실패했습니다.');
        }
      }

      function encodeBase64UrlUtf8(str) {
        var bytes;
        try {
          if (window.TextEncoder) {
            bytes = new TextEncoder().encode(String(str));
          }
        } catch (e) {}
        var bin = '';
        if (bytes) {
          for (var i = 0; i < bytes.length; i++) bin += String.fromCharCode(bytes[i]);
        } else {
          // fallback
          bin = unescape(encodeURIComponent(String(str)));
        }
        var b64 = btoa(bin);
        return b64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
      }

      function decodeBase64UrlUtf8(b64url) {
        var b64 = String(b64url || '').replace(/-/g, '+').replace(/_/g, '/');
        while (b64.length % 4) b64 += '=';
        var bin = atob(b64);
        // decode utf-8
        try {
          if (window.TextDecoder) {
            var bytes = new Uint8Array(bin.length);
            for (var i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
            return new TextDecoder().decode(bytes);
          }
        } catch (e) {}
        return decodeURIComponent(escape(bin));
      }

      function showCodeModal(title, code, onImport) {
        $(document).off('keydown.jjPresetModal');
        $('.jj-preset-modal').remove();
        var $modal = $('<div class="jj-preset-modal" role="dialog" aria-modal="true"></div>');
        var $back = $('<div class="jj-preset-modal-backdrop"></div>');
        var $card = $('<div class="jj-preset-modal-card"></div>');
        var $h = $('<div class="jj-preset-modal-title"></div>').text(title || '공유 코드');
        var $ta = $('<textarea class="jj-preset-modal-text" rows="6"></textarea>');
        $ta.val(code || '');
        var $btns = $('<div class="jj-preset-modal-actions"></div>');
        var $copy = $('<button type="button" class="button button-primary">복사</button>');
        var $close = $('<button type="button" class="button">닫기</button>');
        $btns.append($copy);
        if (typeof onImport === 'function') {
          var $import = $('<button type="button" class="button button-secondary">가져오기</button>');
          $btns.append($import);
          $import.on('click', function(){
            onImport($ta.val() || '');
          });
        }
        $btns.append($close);
        $card.append($h).append($ta).append($btns);
        $modal.append($back).append($card);
        $('body').append($modal);
        $ta.focus().select();

        function closeModal() {
          $(document).off('keydown.jjPresetModal');
          $modal.remove();
        }

        $(document).on('keydown.jjPresetModal', function(e){
          if (e && (e.key === 'Escape' || e.keyCode === 27)) {
            closeModal();
          }
        });

        $copy.on('click', function(){
          copyToClipboard($ta.val() || '', '아래 코드를 복사하세요:');
        });
        $close.on('click', function(){
          closeModal();
        });
        $back.on('click', function(){
          closeModal();
        });
      }

      function buildShareCodeFromItems(items) {
        var payload = {
          v: 1,
          exported_at: new Date().toISOString(),
          items: items || []
        };
        var json = JSON.stringify(payload);
        return 'JJAI1:' + encodeBase64UrlUtf8(json);
      }

      function parseShareCode(code) {
        var c = String(code || '').trim();
        if (!c) throw new Error('코드가 비어있습니다.');
        if (c.length > 120000) throw new Error('공유 코드가 너무 깁니다. (안전상 제한) JSON 파일로 가져오기 기능을 사용하세요.');
        if (c.indexOf('JJAI1:') !== 0) throw new Error('지원하지 않는 코드 형식입니다. (JJAI1: 로 시작해야 합니다)');
        var b64 = c.slice('JJAI1:'.length).trim();
        if (b64.length > 110000) throw new Error('공유 코드 데이터가 너무 큽니다. JSON 파일로 가져오기 기능을 사용하세요.');
        var json = decodeBase64UrlUtf8(b64);
        var parsed = JSON.parse(json);
        if (parsed && Array.isArray(parsed.items)) return parsed.items;
        if (Array.isArray(parsed)) return parsed;
        if (parsed && typeof parsed === 'object') return [parsed];
        throw new Error('코드 데이터 형식이 올바르지 않습니다.');
      }

      function findPreset(id) {
        return PRESETS.find(function(x){ return x && x.id === id; }) || null;
      }

      $grid.on('click', '.jj-preset-card-main', function(){
        var $card = $(this).closest('.jj-preset-card');
        var id = $card.attr('data-preset-id');
        if (bulkMode && $card.hasClass('is-ai')) {
          toggleBulkSelected(id, !isBulkSelected(id));
          $card.find('.jj-ai-bulk-checkbox').prop('checked', isBulkSelected(id));
          $bulkCount.text('선택: ' + getBulkSelectedIds().length + '개');
          return;
        }
        selectedId = id;
        render($search.val());
      });

      $grid.on('click', '.jj-preset-ai-pin', function(e){
        e.preventDefault(); e.stopPropagation();
        var $c = $(this).closest('.jj-preset-card');
        var id = $c.data('preset-id');
        var op = $(this).attr('data-op') || 'pin';
        ajaxManageAiPreset(op, id).then(function(resp){
          if (!resp) return;
          showToast(resp.data.message || '완료', 'info');
          render($search.val());
        });
      });

      $grid.on('click', '.jj-preset-ai-rename', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        var preset = findPreset(id);
        if (!preset) return;
        var next = window.prompt('새 이름을 입력하세요:', preset.name || '');
        if (!next) return;
        ajaxManageAiPreset('rename', id, next).then(function(resp){
          if (!resp) return;
          showToast(resp.data.message || '완료', 'success');
          render($search.val());
        });
      });

      $grid.on('click', '.jj-preset-ai-delete', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        if (!confirm('이 AI 프리셋을 삭제할까요?')) return;
        ajaxManageAiPreset('delete', id).then(function(resp){
          if (!resp) return;
          if (selectedId === id) selectedId = null;
          showToast(resp.data.message || '삭제됨', 'success');
          render($search.val());
        });
      });

      $grid.on('click', '.jj-preset-ai-copy', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        var preset = findPreset(id);
        if (!preset) return;
        var payload = {
          id: preset.id,
          name: preset.name,
          tags: preset.tags,
          note: preset.note,
          brand: preset.brand,
          system: preset.system,
          pinned: !!preset.pinned,
          created_at: preset.created_at || ''
        };
        copyToClipboard(JSON.stringify(payload, null, 2), '아래 JSON을 복사하세요:');
      });

      $grid.on('click', '.jj-preset-ai-export', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        var preset = findPreset(id);
        if (!preset) return;
        var payload = {
          id: preset.id,
          name: preset.name,
          tags: preset.tags,
          note: preset.note,
          brand: preset.brand,
          system: preset.system,
          pinned: !!preset.pinned,
          created_at: preset.created_at || ''
        };
        downloadJson('ai-palette-' + id + '.json', payload);
      });

      $grid.on('change', '.jj-ai-bulk-checkbox', function(e){
        var id = $(this).attr('data-id');
        toggleBulkSelected(id, $(this).is(':checked'));
        // bulk count update (no full rerender)
        $bulkCount.text('선택: ' + getBulkSelectedIds().length + '개');
      });

      // Bulk UI toggling
      function refreshBulkUi() {
        var ids = getBulkSelectedIds();
        var show = !!bulkMode;
        $bulkSelectVisible.toggle(show);
        $bulkInvert.toggle(show);
        $bulkPin.toggle(show);
        $bulkUnpin.toggle(show);
        $bulkDelete.toggle(show);
        $bulkExport.toggle(show);
        $bulkShare.toggle(show);
        $bulkClear.toggle(show);
        $bulkCount.toggle(show);
        if (show) {
          $bulkCount.text('선택: ' + ids.length + '개');
        }
      }
      refreshBulkUi();

      $bulkToggle.on('change', function(){
        bulkMode = $(this).find('input').is(':checked');
        if (!bulkMode) {
          clearBulkSelection();
        }
        render($search.val());
      });

      function getAiPresetItemsByIds(ids) {
        var list = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
          ? window.jj_admin_params.ai_palette_presets
          : [];
        var map = {};
        list.forEach(function(p){ if (p && p.id) map[p.id] = p; });
        return (ids || []).map(function(id){ return map[id]; }).filter(function(x){ return !!x; });
      }

      function downloadSelectedPresets(ids) {
        var items = getAiPresetItemsByIds(ids);
        if (!items.length) {
          alert('선택된 AI 프리셋이 없습니다.');
          return;
        }
        downloadJson('ai-palette-selected-' + (new Date().toISOString().slice(0,10)) + '.json', { exported_at: new Date().toISOString(), items: items });
      }

      function doBulkOp(op) {
        var ids = getBulkSelectedIds();
        if (!ids.length) {
          alert('선택된 항목이 없습니다.');
          return;
        }
        var confirmMsg = '작업을 진행할까요?';
        if (op === 'bulk_delete') confirmMsg = '선택한 ' + ids.length + '개를 삭제할까요? (되돌릴 수 없습니다)';
        if (op === 'bulk_pin') confirmMsg = '선택한 ' + ids.length + '개를 상단 고정할까요?';
        if (op === 'bulk_unpin') confirmMsg = '선택한 ' + ids.length + '개의 고정을 해제할까요?';
        if (!confirm(confirmMsg)) return;
        ajaxManageAiPreset(op, 'ai-bulk', null, null, ids).then(function(resp){
          if (!resp) return;
          clearBulkSelection();
          showToast(resp.data.message || '완료', 'success');
          render($search.val());
        });
      }

      $bulkPin.on('click', function(){ doBulkOp('bulk_pin'); });
      $bulkUnpin.on('click', function(){ doBulkOp('bulk_unpin'); });
      $bulkDelete.on('click', function(){ doBulkOp('bulk_delete'); });
      $bulkExport.on('click', function(){ downloadSelectedPresets(getBulkSelectedIds()); });
      $bulkClear.on('click', function(){ clearBulkSelection(); render($search.val()); });

      $bulkSelectVisible.on('click', function(){
        // 현재 화면에 렌더된 AI 프리셋 전체 선택(검색 결과 기준)
        var $aiCards = $grid.find('.jj-preset-card.is-ai');
        if (!$aiCards.length) {
          alert('선택할 AI 프리셋이 없습니다.');
          return;
        }
        $aiCards.each(function(){
          var id = $(this).attr('data-preset-id');
          toggleBulkSelected(id, true);
          $(this).find('.jj-ai-bulk-checkbox').prop('checked', true);
        });
        $bulkCount.text('선택: ' + getBulkSelectedIds().length + '개');
      });

      $bulkInvert.on('click', function(){
        var $aiCards = $grid.find('.jj-preset-card.is-ai');
        if (!$aiCards.length) {
          alert('선택할 AI 프리셋이 없습니다.');
          return;
        }
        $aiCards.each(function(){
          var id = $(this).attr('data-preset-id');
          var next = !isBulkSelected(id);
          toggleBulkSelected(id, next);
          $(this).find('.jj-ai-bulk-checkbox').prop('checked', next);
        });
        $bulkCount.text('선택: ' + getBulkSelectedIds().length + '개');
      });

      $bulkShare.on('click', function(){
        var ids = getBulkSelectedIds();
        if (!ids.length) {
          alert('선택된 항목이 없습니다.');
          return;
        }
        var items = getAiPresetItemsByIds(ids);
        if (!items.length) {
          alert('선택된 AI 프리셋을 찾을 수 없습니다.');
          return;
        }
        // 공유 코드에는 최소 필드만 넣어 사이즈/호환성을 안정화
        var payloadItems = items.map(function(p){
          return {
            name: p.name,
            tags: p.tags,
            note: p.note,
            brand: p.brand,
            system: p.system,
            pinned: !!p.pinned
          };
        });
        var code = buildShareCodeFromItems(payloadItems);
        showCodeModal('AI 팔레트 공유 코드 (JJAI1)', code);
        showToast('공유 코드가 생성되었습니다. (복사 버튼을 눌러 공유)', 'info');
      });

      $aiImportCode.on('click', function(){
        showCodeModal('공유 코드 붙여넣기 (JJAI1)', '', function(text){
          try {
            var items = parseShareCode(text);
            if (!items || !items.length) {
              alert('가져올 항목이 없습니다.');
              return;
            }
            if (items.length > 100) {
              alert('가져올 항목이 너무 많습니다. (안전상 제한: 100개) JSON 파일로 나눠서 가져오세요.');
              return;
            }
            var names = items.map(function(x){ return x && x.name ? String(x.name) : ''; }).filter(Boolean).slice(0, 6);
            var preview = '가져오기: ' + items.length + '개';
            if (names.length) {
              preview += '\n\n미리보기:\n- ' + names.join('\n- ');
              if (items.length > names.length) {
                preview += '\n- ...';
              }
            }
            if (items.length > 20) {
              preview += '\n\n참고: 저장 한도는 최대 20개이며, 초과분은 자동으로 제외될 수 있습니다.';
            }
            if (!confirm(preview + '\n\n가져올까요?')) {
              return;
            }
            ajaxManageAiPreset('import', 'ai-import', null, JSON.stringify(items)).then(function(resp){
              if (!resp) return;
              showToast(resp.data.message || '가져오기 완료', 'success');
              render($search.val());
              $('.jj-preset-modal').remove();
            });
          } catch (e) {
            alert(e && e.message ? e.message : '코드 파싱 실패');
          }
        });
      });

      function captureColorSnapshot() {
        var snap = {};
        $('#jj-style-guide-form').find('.jj-color-field[data-setting-key]').each(function(){
          var k = $(this).attr('data-setting-key');
          if (!k) return;
          snap[k] = $(this).val();
        });
        return snap;
      }

      function restoreColorSnapshot(snap) {
        if (!snap) return;
        Object.keys(snap).forEach(function(k){
          setField(k, snap[k]);
        });
        updateInlinePreview();
      }

      $apply.prop('disabled', !selectedId);
      $apply.on('click', function(){
        var preset = PRESETS.find(function(x){ return x.id === selectedId; });
        if (!preset) return;

        var includeSystem = $includeSystem.find('input').is(':checked');
        var includeComponents = $includeComponents.find('input').is(':checked');

        // Undo snapshot (apply 전 상태 저장)
        lastUndoSnapshot = captureColorSnapshot();
        lastUndoLabel = preset.name || '프리셋';
        $undo.prop('disabled', false);

        // brand
        setField('palettes[brand][primary_color]', preset.brand.primary);
        setField('palettes[brand][primary_color_hover]', preset.brand.primary_hover);
        setField('palettes[brand][secondary_color]', preset.brand.secondary);
        setField('palettes[brand][secondary_color_hover]', preset.brand.secondary_hover);

        // system (옵션)
        if (includeSystem) {
          setField('palettes[system][site_bg]', preset.system.site_bg);
          setField('palettes[system][content_bg]', preset.system.content_bg);
          setField('palettes[system][text_color]', preset.system.text_color);
          setField('palettes[system][link_color]', preset.system.link_color);
        }

        // 미리보기 갱신
        updateInlinePreview();

        // 버튼/폼/링크 자동 매핑 (기존 "일괄 적용" 로직 재사용)
        if (includeComponents) {
          var $btn = $('#jj-style-guide-form').find('.jj-apply-brand-palette-to-components').first();
          if ($btn.length) {
            $btn.trigger('click');
          }
        }

        showToast('프리셋이 적용되었습니다. (되돌리기 가능)', 'success');
      });

      $undo.prop('disabled', !lastUndoSnapshot);
      $undo.on('click', function(){
        if (!lastUndoSnapshot) return;
        var label = lastUndoLabel ? ('"' + lastUndoLabel + '"') : '이전 상태';
        if (!confirm(label + ' 적용 전 상태로 되돌릴까요?')) return;
        restoreColorSnapshot(lastUndoSnapshot);
        lastUndoSnapshot = null;
        
        // [Phase 8.4] 전역 Undo 시스템에 알림
        if (typeof window.JJUndoSystem !== 'undefined') {
            window.JJUndoSystem.captureSnapshot('색상 복원');
            window.JJUndoSystem.updateUndoButton();
        }
        lastUndoLabel = '';
        $undo.prop('disabled', true);
        showToast('되돌렸습니다.', 'info');
      });
    }

    render('');
    updateInlinePreview();

    // 색상 변경 시 미리보기 자동 갱신
    $('#jj-style-guide-form').on('change input', '.jj-color-field', function(){
      updateInlinePreview();
    });
  });
})(jQuery);


```

프리셋 적용 시 전역 Undo 시스템과 통합되도록 수정하고, 버전 업데이트 및 커밋을 진행합니다.


```javascript
/**
 * JJ Style Guide - Palette Presets & Inline Preview
 *
 * 목표:
 * - 텍스트 위주의 팔레트 UI를 "보여주는" UI로 보강
 * - 2-Color 조합(Primary/Secondary) + 권장 System 팔레트까지 프리셋으로 제공
 * - 클릭 1~2번으로 즉시 적용 + 즉시 미리보기
 *
 * 주의:
 * - 기존 `jj-style-guide-editor.js`의 currentSettings/필드 바인딩 로직을 존중합니다.
 * - 실제 값 적용은 `data-setting-key` 필드 값을 변경(trigger change)하는 방식으로 통일합니다.
 */
(function($){
  'use strict';

  function normalizeHex(hex) {
    if (!hex || typeof hex !== 'string') return '';
    var h = hex.trim();
    if (!h) return '';
    if (h[0] !== '#') h = '#' + h;
    if (h.length === 4) {
      // #RGB -> #RRGGBB
      h = '#' + h[1] + h[1] + h[2] + h[2] + h[3] + h[3];
    }
    return h.toUpperCase();
  }

  function clamp(n, min, max) { return Math.max(min, Math.min(max, n)); }

  function hexToRgb(hex) {
    var h = normalizeHex(hex);
    if (!h || h.length !== 7) return null;
    return {
      r: parseInt(h.slice(1, 3), 16),
      g: parseInt(h.slice(3, 5), 16),
      b: parseInt(h.slice(5, 7), 16),
    };
  }

  function rgbToHex(r, g, b) {
    var rr = clamp(Math.round(r), 0, 255).toString(16).padStart(2, '0');
    var gg = clamp(Math.round(g), 0, 255).toString(16).padStart(2, '0');
    var bb = clamp(Math.round(b), 0, 255).toString(16).padStart(2, '0');
    return ('#' + rr + gg + bb).toUpperCase();
  }

  // amount: -1.0 ~ +1.0 (음수: 어둡게, 양수: 밝게)
  function shade(hex, amount) {
    var rgb = hexToRgb(hex);
    if (!rgb) return '';
    var t = amount < 0 ? 0 : 255;
    var p = Math.abs(amount);
    return rgbToHex(
      (t - rgb.r) * p + rgb.r,
      (t - rgb.g) * p + rgb.g,
      (t - rgb.b) * p + rgb.b
    );
  }

  function setField(settingKey, value) {
    if (!settingKey) return;
    var v = normalizeHex(value);
    var $field = $('#jj-style-guide-form').find('[data-setting-key="' + settingKey + '"]');
    if (!$field.length) return;

    $field.val(v).trigger('change');
    // wpColorPicker가 있으면 색상도 동기화
    if ($field.closest('.wp-picker-container').length) {
      try { $field.wpColorPicker('color', v); } catch (e) {}
    }
    // 카드 미리보기(기존 DOM)
    var $preview = $field.closest('.jj-control-group').find('.jj-color-preview').first();
    if ($preview.length) $preview.css('background-color', v);
  }

  function readField(settingKey, fallback) {
    var $field = $('#jj-style-guide-form').find('[data-setting-key="' + settingKey + '"]');
    var v = $field.length ? normalizeHex($field.val()) : '';
    return v || normalizeHex(fallback) || '';
  }

  function updateInlinePreview() {
    var $preview = $('#jj-palette-inline-preview');
    if (!$preview.length) return;

    var primary = readField('palettes[brand][primary_color]', '#2271B1');
    var primaryHover = readField('palettes[brand][primary_color_hover]', shade(primary, -0.12));
    var secondary = readField('palettes[brand][secondary_color]', '#444444');
    var secondaryHover = readField('palettes[brand][secondary_color_hover]', shade(secondary, -0.12));

    var bg = readField('palettes[system][site_bg]', '#F6F7F7');
    var surface = readField('palettes[system][content_bg]', '#FFFFFF');
    var text = readField('palettes[system][text_color]', '#1D2327');
    var link = readField('palettes[system][link_color]', primary);

    $preview.css('--jj-prev-bg', bg);
    $preview.css('--jj-prev-surface', surface);
    $preview.css('--jj-prev-text', text);
    $preview.css('--jj-prev-link', link);
    $preview.css('--jj-prev-primary', primary);
    $preview.css('--jj-prev-primary-hover', primaryHover);
    $preview.css('--jj-prev-secondary', secondary);
    $preview.css('--jj-prev-secondary-hover', secondaryHover);

    $preview.find('[data-jj-color="primary"]').text(primary);
    $preview.find('[data-jj-color="secondary"]').text(secondary);
    $preview.find('[data-jj-color="bg"]').text(bg);
    $preview.find('[data-jj-color="text"]').text(text);
  }

  $(document).ready(function(){
    var $mount = $('#jj-palette-presets');
    if (!$mount.length) return;

    var BASE_PRESETS = [
      {
        id: 'luxury-black-gold',
        name: 'Black & Gold',
        tags: ['럭셔리', '다크', '고대비', '프리미엄'],
        note: '고급·권위·집중(CTA 강함). 법률/컨설팅/하이엔드에 적합.',
        brand: { primary: '#C9A227', secondary: '#0B0D0F' },
        system: { site_bg: '#0B0D0F', content_bg: '#101418', text_color: '#EAEAEA', link_color: '#C9A227' }
      },
      {
        id: 'trust-navy-cyan',
        name: 'Navy & Cyan',
        tags: ['신뢰', '테크', '차분함'],
        note: '신뢰·안정·정교함. SaaS/데이터/기업 사이트에 적합.',
        brand: { primary: '#1F3C88', secondary: '#00B8D9' },
        system: { site_bg: '#F7FAFC', content_bg: '#FFFFFF', text_color: '#1D2327', link_color: '#1F3C88' }
      },
      {
        id: 'modern-coral-slate',
        name: 'Coral & Slate',
        tags: ['모던', '따뜻함', '크리에이티브'],
        note: '친근·활기. 브랜딩/포트폴리오/커머스에 적합.',
        brand: { primary: '#FF6B6B', secondary: '#334E68' },
        system: { site_bg: '#FFFFFF', content_bg: '#F7FAFC', text_color: '#102A43', link_color: '#334E68' }
      },
      {
        id: 'minimal-mono',
        name: 'Minimal Mono',
        tags: ['미니멀', '라이트', '가독성'],
        note: '콘텐츠 중심·가독성 강화. 블로그/매거진에 적합.',
        brand: { primary: '#111827', secondary: '#6B7280' },
        system: { site_bg: '#F9FAFB', content_bg: '#FFFFFF', text_color: '#111827', link_color: '#111827' }
      },
      {
        id: 'emerald-charcoal',
        name: 'Emerald & Charcoal',
        tags: ['헬스', '자연', '안정'],
        note: '안정·신선. 헬스/웰니스/교육에 적합.',
        brand: { primary: '#10B981', secondary: '#111827' },
        system: { site_bg: '#F7FAFC', content_bg: '#FFFFFF', text_color: '#111827', link_color: '#10B981' }
      },
      {
        id: 'royal-purple-ice',
        name: 'Royal Purple',
        tags: ['프리미엄', '창의', '브랜딩'],
        note: '독창·세련. 이벤트/크리에이티브 스튜디오에 적합.',
        brand: { primary: '#6D28D9', secondary: '#0EA5E9' },
        system: { site_bg: '#F8FAFC', content_bg: '#FFFFFF', text_color: '#0F172A', link_color: '#6D28D9' }
      },
      {
        id: 'warm-brown-cream',
        name: 'Brown & Cream',
        tags: ['따뜻함', '클래식', '브랜드'],
        note: '따뜻·신뢰. 카페/식품/라이프스타일에 적합.',
        brand: { primary: '#8B5E34', secondary: '#D4A373' },
        system: { site_bg: '#FFF7ED', content_bg: '#FFFFFF', text_color: '#1F2937', link_color: '#8B5E34' }
      },
      {
        id: 'contrast-blue-yellow',
        name: 'Blue & Yellow (CTA)',
        tags: ['고대비', '전환', 'CTA'],
        note: '버튼/링크가 눈에 띄게. 랜딩/광고 페이지에 적합.',
        brand: { primary: '#2563EB', secondary: '#F59E0B' },
        system: { site_bg: '#FFFFFF', content_bg: '#F8FAFC', text_color: '#0F172A', link_color: '#2563EB' }
      }
    ];

    var PRESETS = [];
    var bulkMode = false;
    var bulkSelected = {};
    var lastUndoSnapshot = null;
    var lastUndoLabel = '';

    function isAiPreset(p) {
      if (!p || typeof p !== 'object') return false;
      if (p.source === 'ai_extension') return true;
      return typeof p.id === 'string' && p.id.indexOf('ai-') === 0;
    }

    function sortAiPresets(a, b) {
      var ap = !!(a && a.pinned);
      var bp = !!(b && b.pinned);
      if (ap !== bp) return ap ? -1 : 1;
      var at = (a && a.created_at) ? String(a.created_at) : '';
      var bt = (b && b.created_at) ? String(b.created_at) : '';
      if (at === bt) return 0;
      return bt < at ? -1 : 1; // desc
    }

    function loadAiPresets() {
      var aiPresets = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
        ? window.jj_admin_params.ai_palette_presets
        : [];
      if (!aiPresets || !aiPresets.length) return [];

      var cleaned = [];
      aiPresets.forEach(function(p){
        if (!p || typeof p !== 'object') return;
        if (!p.id || !p.name || !p.brand || !p.system) return;

        // tags 정규화
        if (!Array.isArray(p.tags)) {
          p.tags = [];
        }
        if (p.tags.indexOf('AI') === -1) {
          p.tags.unshift('AI');
        }

        // note 보정
        if (!p.note) {
          p.note = 'AI로 저장된 팔레트';
        }

        // shape 보정(brand/system)
        p.brand = p.brand || {};
        p.system = p.system || {};
        cleaned.push(p);
      });

      cleaned.sort(sortAiPresets);
      return cleaned;
    }

    function normalizePresetColors(p) {
      p.brand.primary = normalizeHex(p.brand.primary);
      p.brand.secondary = normalizeHex(p.brand.secondary);
      p.brand.primary_hover = normalizeHex(p.brand.primary_hover || shade(p.brand.primary, -0.12));
      p.brand.secondary_hover = normalizeHex(p.brand.secondary_hover || shade(p.brand.secondary, -0.12));
      p.system.site_bg = normalizeHex(p.system.site_bg);
      p.system.content_bg = normalizeHex(p.system.content_bg);
      p.system.text_color = normalizeHex(p.system.text_color);
      p.system.link_color = normalizeHex(p.system.link_color || p.brand.primary);
      return p;
    }

    function rebuildPresets() {
      var ai = loadAiPresets();
      PRESETS = ai.concat(BASE_PRESETS);
      PRESETS.forEach(function(p){
        try { normalizePresetColors(p); } catch (e) {}
      });
    }

    rebuildPresets();

    var selectedId = null;

    function getBulkSelectedIds() {
      return Object.keys(bulkSelected).filter(function(id){ return !!bulkSelected[id]; });
    }

    function clearBulkSelection() {
      bulkSelected = {};
    }

    function toggleBulkSelected(id, on) {
      if (!id) return;
      bulkSelected[id] = !!on;
    }

    function isBulkSelected(id) {
      return !!bulkSelected[id];
    }

    function render(filterText) {
      var q = (filterText || '').toLowerCase().trim();
      $mount.empty();

      var $toolbar = $('<div class="jj-preset-toolbar"></div>');
      var $search = $('<input type="search" class="jj-preset-search" placeholder="프리셋 검색 (예: 럭셔리, 신뢰, 다크)"/>');
      $search.val(filterText || '');

      var $includeSystem = $('<label class="jj-preset-include-system"><input type="checkbox" checked> 시스템 팔레트도 함께 적용</label>');
      var $includeComponents = $('<label class="jj-preset-include-system"><input type="checkbox" checked> 버튼/폼/링크 자동 맞추기</label>');

      // AI 프리셋 관리(내보내기/불러오기)
      var aiCount = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
        ? window.jj_admin_params.ai_palette_presets.length
        : 0;
      var $aiTools = $('<div class="jj-preset-ai-tools"></div>');
      if (aiCount > 0) {
        $aiTools.append($('<span class="jj-preset-ai-count"></span>').text('AI 프리셋: ' + aiCount + '개'));
      }
      var $bulkToggle = $('<label class="jj-preset-ai-bulk-toggle"><input type="checkbox"> 대량 선택</label>');
      $bulkToggle.find('input').prop('checked', !!bulkMode);
      var $bulkCount = $('<span class="jj-preset-ai-bulk-count"></span>');
      var $bulkSelectVisible = $('<button type="button" class="button jj-preset-ai-bulk-select-visible" style="display:none;">보이는 항목 전체 선택</button>');
      var $bulkInvert = $('<button type="button" class="button jj-preset-ai-bulk-invert" style="display:none;">선택 반전</button>');
      var $bulkPin = $('<button type="button" class="button jj-preset-ai-bulk-pin" style="display:none;">선택 고정</button>');
      var $bulkUnpin = $('<button type="button" class="button jj-preset-ai-bulk-unpin" style="display:none;">선택 해제</button>');
      var $bulkDelete = $('<button type="button" class="button button-link-delete jj-preset-ai-bulk-delete" style="display:none;">선택 삭제</button>');
      var $bulkExport = $('<button type="button" class="button jj-preset-ai-bulk-export" style="display:none;">선택 내보내기</button>');
      var $bulkShare = $('<button type="button" class="button jj-preset-ai-bulk-share" style="display:none;">선택 공유 코드</button>');
      var $bulkClear = $('<button type="button" class="button jj-preset-ai-bulk-clear" style="display:none;">선택 해제</button>');

      var $aiExportAll = $('<button type="button" class="button jj-preset-ai-export-all">AI 전체 내보내기</button>');
      var $aiImport = $('<button type="button" class="button jj-preset-ai-import">AI 불러오기</button>');
      var $aiImportInput = $('<input type="file" accept=".json" class="jj-preset-ai-import-input" style="display:none;">');
      var $aiImportCode = $('<button type="button" class="button jj-preset-ai-import-code">코드로 가져오기</button>');
      $aiTools.append($bulkToggle).append($bulkCount).append($bulkSelectVisible).append($bulkInvert).append($bulkPin).append($bulkUnpin).append($bulkDelete).append($bulkExport).append($bulkShare).append($bulkClear);
      $aiTools.append($aiExportAll).append($aiImport).append($aiImportCode).append($aiImportInput);

      $toolbar.append($search).append($includeSystem).append($includeComponents).append($aiTools);
      $mount.append($toolbar);

      var $grid = $('<div class="jj-preset-grid" role="list"></div>');
      var shown = 0;

      PRESETS.forEach(function(p){
        var hay = (p.name + ' ' + p.tags.join(' ') + ' ' + p.note).toLowerCase();
        if (q && hay.indexOf(q) === -1) return;
        shown++;

        var isActive = selectedId === p.id;
        var isAI = isAiPreset(p);
        var isPinned = !!(p && p.pinned);

        var $card = $('<div class="jj-preset-card" role="listitem"></div>');
        $card.toggleClass('is-active', !!isActive);
        $card.toggleClass('is-ai', !!isAI);
        $card.toggleClass('is-pinned', !!isPinned);
        $card.attr('data-preset-id', p.id);

        var swatches = [
          { c: p.brand.primary, label: 'Primary' },
          { c: p.brand.secondary, label: 'Secondary' },
          { c: p.system.site_bg, label: 'BG' },
          { c: p.system.text_color, label: 'Text' }
        ];

        var $sw = $('<div class="jj-preset-swatches" aria-hidden="true"></div>');
        swatches.forEach(function(s){
          var $s = $('<span class="jj-preset-swatch"></span>');
          $s.css('background-color', s.c);
          $s.attr('title', s.label + ': ' + s.c);
          $sw.append($s);
        });

        var $meta = $('<div class="jj-preset-meta"></div>');
        $meta.append($('<div class="jj-preset-title"></div>').text(p.name));
        $meta.append($('<div class="jj-preset-tags"></div>').text(p.tags.join(' · ')));
        $meta.append($('<div class="jj-preset-note"></div>').text(p.note));

        var $main = $('<button type="button" class="jj-preset-card-main"></button>');
        $main.append($sw).append($meta);
        $card.append($main);

        // AI 프리셋: 관리 액션(핀/이름/삭제/복사/내보내기)
        if (isAI) {
          var $cardActions = $('<div class="jj-preset-card-actions" aria-label="AI 프리셋 관리"></div>');

          var $pin = $('<button type="button" class="jj-preset-action jj-preset-ai-pin"><span class="dashicons dashicons-admin-post"></span></button>');
          $pin.attr('title', isPinned ? '상단 고정 해제' : '상단 고정');
          $pin.attr('data-op', isPinned ? 'unpin' : 'pin');

          var $rename = $('<button type="button" class="jj-preset-action jj-preset-ai-rename"><span class="dashicons dashicons-edit"></span></button>');
          $rename.attr('title', '이름 변경');

          var $copy = $('<button type="button" class="jj-preset-action jj-preset-ai-copy"><span class="dashicons dashicons-admin-page"></span></button>');
          $copy.attr('title', '팔레트 JSON 복사');

          var $export = $('<button type="button" class="jj-preset-action jj-preset-ai-export"><span class="dashicons dashicons-download"></span></button>');
          $export.attr('title', '팔레트 JSON 다운로드');

          var $del = $('<button type="button" class="jj-preset-action jj-preset-ai-delete"><span class="dashicons dashicons-trash"></span></button>');
          $del.attr('title', '삭제');

          $cardActions.append($pin, $rename, $copy, $export, $del);
          $card.append($cardActions);
        }

        // Bulk selection checkbox (AI only)
        if (isAI && bulkMode) {
          var $bulkCheck = $('<label class="jj-preset-bulk-check" title="대량 선택"><input type="checkbox" class="jj-ai-bulk-checkbox"></label>');
          $bulkCheck.find('input').attr('data-id', p.id).prop('checked', isBulkSelected(p.id));
          $card.append($bulkCheck);
        }

        $grid.append($card);
      });

      if (shown === 0) {
        $grid.append($('<div class="jj-preset-empty"></div>').text('검색 결과가 없습니다.'));
      }

      var $actions = $('<div class="jj-preset-actions"></div>');
      var $apply = $('<button type="button" class="button button-primary jj-preset-apply" disabled>선택한 프리셋 적용</button>');
      var $undo = $('<button type="button" class="button jj-preset-undo" disabled>되돌리기</button>');
      var $hint = $('<span class="jj-preset-hint">클릭 → 미리보기 → 적용 (언제든 수정 가능)</span>');
      $actions.append($apply).append($undo).append($hint);

      $mount.append($grid).append($actions);

      // events
      $search.on('input', function(){ render($(this).val()); });

      $aiExportAll.on('click', function(){
        var list = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
          ? window.jj_admin_params.ai_palette_presets
          : [];
        if (!list.length) {
          alert('내보낼 AI 프리셋이 없습니다.');
          return;
        }
        var payload = {
          exported_at: new Date().toISOString(),
          items: list
        };
        try {
          var data = JSON.stringify(payload, null, 2);
          var blob = new Blob([data], { type: 'application/json' });
          var url = URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.href = url;
          a.download = 'ai-palette-presets-' + (new Date().toISOString().slice(0,10)) + '.json';
          document.body.appendChild(a);
          a.click();
          a.remove();
          setTimeout(function(){ URL.revokeObjectURL(url); }, 1000);
        } catch (e) {
          alert('내보내기에 실패했습니다.');
        }
      });

      $aiImport.on('click', function(){
        $aiImportInput.trigger('click');
      });

      $aiImportInput.on('change', function(){
        var file = this.files && this.files[0];
        if (!file) return;
        if (!file.name.toLowerCase().endsWith('.json')) {
          alert('JSON 파일만 업로드할 수 있습니다.');
          $(this).val('');
          return;
        }
        var reader = new FileReader();
        reader.onload = function(){
          var txt = String(reader.result || '');
          // Allow wrapped export format {items:[...]} or direct array/object
          var payloadTxt = txt;
          try {
            var parsed = JSON.parse(txt);
            if (parsed && parsed.items && Array.isArray(parsed.items)) {
              payloadTxt = JSON.stringify(parsed.items);
            }
          } catch (e) {
            // keep raw -> server will validate
          }

          if (!confirm('AI 팔레트 프리셋을 불러올까요? (중복 항목은 자동으로 건너뜁니다)')) {
            $(this).val('');
            return;
          }

          ajaxManageAiPreset('import', 'ai-import', null, payloadTxt).then(function(resp){
            if (!resp) return;
            showToast(resp.data.message || '가져오기 완료', 'success');
            render($search.val());
          });
        }.bind(this);
        reader.onerror = function(){
          alert('파일을 읽을 수 없습니다.');
          $(this).val('');
        }.bind(this);
        reader.readAsText(file);
      });

      function showToast(msg, kind) {
        try {
          $('.jj-preset-toast').remove();
          var $t = $('<div class="jj-preset-toast"></div>');
          $t.addClass(kind ? ('is-' + kind) : 'is-info');
          $t.text(msg || '');
          $mount.prepend($t);
          setTimeout(function(){
            $t.fadeOut(200, function(){ $(this).remove(); });
          }, 2600);
        } catch (e) {}
      }

      function ajaxManageAiPreset(op, id, name, payloadJson, ids) {
        var ajaxUrl = (window.jj_admin_params && window.jj_admin_params.ajax_url) ? window.jj_admin_params.ajax_url : (window.ajaxurl || '');
        var security = (window.jj_admin_params && window.jj_admin_params.nonce) ? window.jj_admin_params.nonce : '';
        if (!ajaxUrl || !security) {
          alert('AJAX 설정이 초기화되지 않았습니다.');
          return;
        }

        return $.ajax({
          url: ajaxUrl,
          type: 'POST',
          data: {
            action: 'jj_ai_palette_preset_manage',
            security: security,
            op: op,
            id: id,
            name: name || '',
            payload_json: payloadJson || '',
            ids: Array.isArray(ids) ? ids : []
          }
        }).then(function(resp){
          if (!resp || !resp.success) {
            alert((resp && resp.data && resp.data.message) ? resp.data.message : '요청 실패');
            return null;
          }
          // 갱신된 목록을 localize 영역에 반영 후 rebuild
          if (window.jj_admin_params) {
            window.jj_admin_params.ai_palette_presets = resp.data.items || [];
          }
          rebuildPresets();
          return resp;
        });
      }

      function copyToClipboard(text, fallbackLabel) {
        var t = String(text || '');
        if (!t) return;
        try {
          if (navigator && navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
            navigator.clipboard.writeText(t).then(function(){
              showToast('클립보드에 복사되었습니다.', 'success');
            }).catch(function(){
              window.prompt(fallbackLabel || '복사하세요:', t);
            });
            return;
          }
        } catch (e) {}
        window.prompt(fallbackLabel || '복사하세요:', t);
      }

      function downloadJson(filename, obj) {
        try {
          var data = JSON.stringify(obj, null, 2);
          var blob = new Blob([data], { type: 'application/json' });
          var url = URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.href = url;
          a.download = filename;
          document.body.appendChild(a);
          a.click();
          a.remove();
          setTimeout(function(){ URL.revokeObjectURL(url); }, 1000);
        } catch (e) {
          alert('다운로드에 실패했습니다.');
        }
      }

      function encodeBase64UrlUtf8(str) {
        var bytes;
        try {
          if (window.TextEncoder) {
            bytes = new TextEncoder().encode(String(str));
          }
        } catch (e) {}
        var bin = '';
        if (bytes) {
          for (var i = 0; i < bytes.length; i++) bin += String.fromCharCode(bytes[i]);
        } else {
          // fallback
          bin = unescape(encodeURIComponent(String(str)));
        }
        var b64 = btoa(bin);
        return b64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
      }

      function decodeBase64UrlUtf8(b64url) {
        var b64 = String(b64url || '').replace(/-/g, '+').replace(/_/g, '/');
        while (b64.length % 4) b64 += '=';
        var bin = atob(b64);
        // decode utf-8
        try {
          if (window.TextDecoder) {
            var bytes = new Uint8Array(bin.length);
            for (var i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
            return new TextDecoder().decode(bytes);
          }
        } catch (e) {}
        return decodeURIComponent(escape(bin));
      }

      function showCodeModal(title, code, onImport) {
        $(document).off('keydown.jjPresetModal');
        $('.jj-preset-modal').remove();
        var $modal = $('<div class="jj-preset-modal" role="dialog" aria-modal="true"></div>');
        var $back = $('<div class="jj-preset-modal-backdrop"></div>');
        var $card = $('<div class="jj-preset-modal-card"></div>');
        var $h = $('<div class="jj-preset-modal-title"></div>').text(title || '공유 코드');
        var $ta = $('<textarea class="jj-preset-modal-text" rows="6"></textarea>');
        $ta.val(code || '');
        var $btns = $('<div class="jj-preset-modal-actions"></div>');
        var $copy = $('<button type="button" class="button button-primary">복사</button>');
        var $close = $('<button type="button" class="button">닫기</button>');
        $btns.append($copy);
        if (typeof onImport === 'function') {
          var $import = $('<button type="button" class="button button-secondary">가져오기</button>');
          $btns.append($import);
          $import.on('click', function(){
            onImport($ta.val() || '');
          });
        }
        $btns.append($close);
        $card.append($h).append($ta).append($btns);
        $modal.append($back).append($card);
        $('body').append($modal);
        $ta.focus().select();

        function closeModal() {
          $(document).off('keydown.jjPresetModal');
          $modal.remove();
        }

        $(document).on('keydown.jjPresetModal', function(e){
          if (e && (e.key === 'Escape' || e.keyCode === 27)) {
            closeModal();
          }
        });

        $copy.on('click', function(){
          copyToClipboard($ta.val() || '', '아래 코드를 복사하세요:');
        });
        $close.on('click', function(){
          closeModal();
        });
        $back.on('click', function(){
          closeModal();
        });
      }

      function buildShareCodeFromItems(items) {
        var payload = {
          v: 1,
          exported_at: new Date().toISOString(),
          items: items || []
        };
        var json = JSON.stringify(payload);
        return 'JJAI1:' + encodeBase64UrlUtf8(json);
      }

      function parseShareCode(code) {
        var c = String(code || '').trim();
        if (!c) throw new Error('코드가 비어있습니다.');
        if (c.length > 120000) throw new Error('공유 코드가 너무 깁니다. (안전상 제한) JSON 파일로 가져오기 기능을 사용하세요.');
        if (c.indexOf('JJAI1:') !== 0) throw new Error('지원하지 않는 코드 형식입니다. (JJAI1: 로 시작해야 합니다)');
        var b64 = c.slice('JJAI1:'.length).trim();
        if (b64.length > 110000) throw new Error('공유 코드 데이터가 너무 큽니다. JSON 파일로 가져오기 기능을 사용하세요.');
        var json = decodeBase64UrlUtf8(b64);
        var parsed = JSON.parse(json);
        if (parsed && Array.isArray(parsed.items)) return parsed.items;
        if (Array.isArray(parsed)) return parsed;
        if (parsed && typeof parsed === 'object') return [parsed];
        throw new Error('코드 데이터 형식이 올바르지 않습니다.');
      }

      function findPreset(id) {
        return PRESETS.find(function(x){ return x && x.id === id; }) || null;
      }

      $grid.on('click', '.jj-preset-card-main', function(){
        var $card = $(this).closest('.jj-preset-card');
        var id = $card.attr('data-preset-id');
        if (bulkMode && $card.hasClass('is-ai')) {
          toggleBulkSelected(id, !isBulkSelected(id));
          $card.find('.jj-ai-bulk-checkbox').prop('checked', isBulkSelected(id));
          $bulkCount.text('선택: ' + getBulkSelectedIds().length + '개');
          return;
        }
        selectedId = id;
        render($search.val());
      });

      $grid.on('click', '.jj-preset-ai-pin', function(e){
        e.preventDefault(); e.stopPropagation();
        var $c = $(this).closest('.jj-preset-card');
        var id = $c.data('preset-id');
        var op = $(this).attr('data-op') || 'pin';
        ajaxManageAiPreset(op, id).then(function(resp){
          if (!resp) return;
          showToast(resp.data.message || '완료', 'info');
          render($search.val());
        });
      });

      $grid.on('click', '.jj-preset-ai-rename', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        var preset = findPreset(id);
        if (!preset) return;
        var next = window.prompt('새 이름을 입력하세요:', preset.name || '');
        if (!next) return;
        ajaxManageAiPreset('rename', id, next).then(function(resp){
          if (!resp) return;
          showToast(resp.data.message || '완료', 'success');
          render($search.val());
        });
      });

      $grid.on('click', '.jj-preset-ai-delete', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        if (!confirm('이 AI 프리셋을 삭제할까요?')) return;
        ajaxManageAiPreset('delete', id).then(function(resp){
          if (!resp) return;
          if (selectedId === id) selectedId = null;
          showToast(resp.data.message || '삭제됨', 'success');
          render($search.val());
        });
      });

      $grid.on('click', '.jj-preset-ai-copy', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        var preset = findPreset(id);
        if (!preset) return;
        var payload = {
          id: preset.id,
          name: preset.name,
          tags: preset.tags,
          note: preset.note,
          brand: preset.brand,
          system: preset.system,
          pinned: !!preset.pinned,
          created_at: preset.created_at || ''
        };
        copyToClipboard(JSON.stringify(payload, null, 2), '아래 JSON을 복사하세요:');
      });

      $grid.on('click', '.jj-preset-ai-export', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        var preset = findPreset(id);
        if (!preset) return;
        var payload = {
          id: preset.id,
          name: preset.name,
          tags: preset.tags,
          note: preset.note,
          brand: preset.brand,
          system: preset.system,
          pinned: !!preset.pinned,
          created_at: preset.created_at || ''
        };
        downloadJson('ai-palette-' + id + '.json', payload);
      });

      $grid.on('change', '.jj-ai-bulk-checkbox', function(e){
        var id = $(this).attr('data-id');
        toggleBulkSelected(id, $(this).is(':checked'));
        // bulk count update (no full rerender)
        $bulkCount.text('선택: ' + getBulkSelectedIds().length + '개');
      });

      // Bulk UI toggling
      function refreshBulkUi() {
        var ids = getBulkSelectedIds();
        var show = !!bulkMode;
        $bulkSelectVisible.toggle(show);
        $bulkInvert.toggle(show);
        $bulkPin.toggle(show);
        $bulkUnpin.toggle(show);
        $bulkDelete.toggle(show);
        $bulkExport.toggle(show);
        $bulkShare.toggle(show);
        $bulkClear.toggle(show);
        $bulkCount.toggle(show);
        if (show) {
          $bulkCount.text('선택: ' + ids.length + '개');
        }
      }
      refreshBulkUi();

      $bulkToggle.on('change', function(){
        bulkMode = $(this).find('input').is(':checked');
        if (!bulkMode) {
          clearBulkSelection();
        }
        render($search.val());
      });

      function getAiPresetItemsByIds(ids) {
        var list = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
          ? window.jj_admin_params.ai_palette_presets
          : [];
        var map = {};
        list.forEach(function(p){ if (p && p.id) map[p.id] = p; });
        return (ids || []).map(function(id){ return map[id]; }).filter(function(x){ return !!x; });
      }

      function downloadSelectedPresets(ids) {
        var items = getAiPresetItemsByIds(ids);
        if (!items.length) {
          alert('선택된 AI 프리셋이 없습니다.');
          return;
        }
        downloadJson('ai-palette-selected-' + (new Date().toISOString().slice(0,10)) + '.json', { exported_at: new Date().toISOString(), items: items });
      }

      function doBulkOp(op) {
        var ids = getBulkSelectedIds();
        if (!ids.length) {
          alert('선택된 항목이 없습니다.');
          return;
        }
        var confirmMsg = '작업을 진행할까요?';
        if (op === 'bulk_delete') confirmMsg = '선택한 ' + ids.length + '개를 삭제할까요? (되돌릴 수 없습니다)';
        if (op === 'bulk_pin') confirmMsg = '선택한 ' + ids.length + '개를 상단 고정할까요?';
        if (op === 'bulk_unpin') confirmMsg = '선택한 ' + ids.length + '개의 고정을 해제할까요?';
        if (!confirm(confirmMsg)) return;
        ajaxManageAiPreset(op, 'ai-bulk', null, null, ids).then(function(resp){
          if (!resp) return;
          clearBulkSelection();
          showToast(resp.data.message || '완료', 'success');
          render($search.val());
        });
      }

      $bulkPin.on('click', function(){ doBulkOp('bulk_pin'); });
      $bulkUnpin.on('click', function(){ doBulkOp('bulk_unpin'); });
      $bulkDelete.on('click', function(){ doBulkOp('bulk_delete'); });
      $bulkExport.on('click', function(){ downloadSelectedPresets(getBulkSelectedIds()); });
      $bulkClear.on('click', function(){ clearBulkSelection(); render($search.val()); });

      $bulkSelectVisible.on('click', function(){
        // 현재 화면에 렌더된 AI 프리셋 전체 선택(검색 결과 기준)
        var $aiCards = $grid.find('.jj-preset-card.is-ai');
        if (!$aiCards.length) {
          alert('선택할 AI 프리셋이 없습니다.');
          return;
        }
        $aiCards.each(function(){
          var id = $(this).attr('data-preset-id');
          toggleBulkSelected(id, true);
          $(this).find('.jj-ai-bulk-checkbox').prop('checked', true);
        });
        $bulkCount.text('선택: ' + getBulkSelectedIds().length + '개');
      });

      $bulkInvert.on('click', function(){
        var $aiCards = $grid.find('.jj-preset-card.is-ai');
        if (!$aiCards.length) {
          alert('선택할 AI 프리셋이 없습니다.');
          return;
        }
        $aiCards.each(function(){
          var id = $(this).attr('data-preset-id');
          var next = !isBulkSelected(id);
          toggleBulkSelected(id, next);
          $(this).find('.jj-ai-bulk-checkbox').prop('checked', next);
        });
        $bulkCount.text('선택: ' + getBulkSelectedIds().length + '개');
      });

      $bulkShare.on('click', function(){
        var ids = getBulkSelectedIds();
        if (!ids.length) {
          alert('선택된 항목이 없습니다.');
          return;
        }
        var items = getAiPresetItemsByIds(ids);
        if (!items.length) {
          alert('선택된 AI 프리셋을 찾을 수 없습니다.');
          return;
        }
        // 공유 코드에는 최소 필드만 넣어 사이즈/호환성을 안정화
        var payloadItems = items.map(function(p){
          return {
            name: p.name,
            tags: p.tags,
            note: p.note,
            brand: p.brand,
            system: p.system,
            pinned: !!p.pinned
          };
        });
        var code = buildShareCodeFromItems(payloadItems);
        showCodeModal('AI 팔레트 공유 코드 (JJAI1)', code);
        showToast('공유 코드가 생성되었습니다. (복사 버튼을 눌러 공유)', 'info');
      });

      $aiImportCode.on('click', function(){
        showCodeModal('공유 코드 붙여넣기 (JJAI1)', '', function(text){
          try {
            var items = parseShareCode(text);
            if (!items || !items.length) {
              alert('가져올 항목이 없습니다.');
              return;
            }
            if (items.length > 100) {
              alert('가져올 항목이 너무 많습니다. (안전상 제한: 100개) JSON 파일로 나눠서 가져오세요.');
              return;
            }
            var names = items.map(function(x){ return x && x.name ? String(x.name) : ''; }).filter(Boolean).slice(0, 6);
            var preview = '가져오기: ' + items.length + '개';
            if (names.length) {
              preview += '\n\n미리보기:\n- ' + names.join('\n- ');
              if (items.length > names.length) {
                preview += '\n- ...';
              }
            }
            if (items.length > 20) {
              preview += '\n\n참고: 저장 한도는 최대 20개이며, 초과분은 자동으로 제외될 수 있습니다.';
            }
            if (!confirm(preview + '\n\n가져올까요?')) {
              return;
            }
            ajaxManageAiPreset('import', 'ai-import', null, JSON.stringify(items)).then(function(resp){
              if (!resp) return;
              showToast(resp.data.message || '가져오기 완료', 'success');
              render($search.val());
              $('.jj-preset-modal').remove();
            });
          } catch (e) {
            alert(e && e.message ? e.message : '코드 파싱 실패');
          }
        });
      });

      function captureColorSnapshot() {
        var snap = {};
        $('#jj-style-guide-form').find('.jj-color-field[data-setting-key]').each(function(){
          var k = $(this).attr('data-setting-key');
          if (!k) return;
          snap[k] = $(this).val();
        });
        return snap;
      }

      function restoreColorSnapshot(snap) {
        if (!snap) return;
        Object.keys(snap).forEach(function(k){
          setField(k, snap[k]);
        });
        updateInlinePreview();
      }

      $apply.prop('disabled', !selectedId);
      $apply.on('click', function(){
        var preset = PRESETS.find(function(x){ return x.id === selectedId; });
        if (!preset) return;

        var includeSystem = $includeSystem.find('input').is(':checked');
        var includeComponents = $includeComponents.find('input').is(':checked');

        // Undo snapshot (apply 전 상태 저장)
        lastUndoSnapshot = captureColorSnapshot();
        lastUndoLabel = preset.name || '프리셋';
        $undo.prop('disabled', false);

        // brand
        setField('palettes[brand][primary_color]', preset.brand.primary);
        setField('palettes[brand][primary_color_hover]', preset.brand.primary_hover);
        setField('palettes[brand][secondary_color]', preset.brand.secondary);
        setField('palettes[brand][secondary_color_hover]', preset.brand.secondary_hover);

        // system (옵션)
        if (includeSystem) {
          setField('palettes[system][site_bg]', preset.system.site_bg);
          setField('palettes[system][content_bg]', preset.system.content_bg);
          setField('palettes[system][text_color]', preset.system.text_color);
          setField('palettes[system][link_color]', preset.system.link_color);
        }

        // 미리보기 갱신
        updateInlinePreview();

        // 버튼/폼/링크 자동 매핑 (기존 "일괄 적용" 로직 재사용)
        if (includeComponents) {
          var $btn = $('#jj-style-guide-form').find('.jj-apply-brand-palette-to-components').first();
          if ($btn.length) {
            $btn.trigger('click');
          }
        }

        showToast('프리셋이 적용되었습니다. (되돌리기 가능)', 'success');
        
        // [Phase 8.4] 전역 Undo 시스템에 알림
        if (typeof window.JJUndoSystem !== 'undefined') {
          window.JJUndoSystem.captureSnapshot(preset.name || '프리셋 적용');
          window.JJUndoSystem.updateUndoButton();
        }
        
        // [Phase 8.4] 커스텀 이벤트 발생 (다른 스크립트에서 감지 가능)
        $(document).trigger('jj:preset:applied', [preset.name || '프리셋']);
      });

      $undo.prop('disabled', !lastUndoSnapshot);
      $undo.on('click', function(){
        if (!lastUndoSnapshot) return;
        var label = lastUndoLabel ? ('"' + lastUndoLabel + '"') : '이전 상태';
        if (!confirm(label + ' 적용 전 상태로 되돌릴까요?')) return;
        restoreColorSnapshot(lastUndoSnapshot);
        lastUndoSnapshot = null;
        
        // [Phase 8.4] 전역 Undo 시스템에 알림
        if (typeof window.JJUndoSystem !== 'undefined') {
            window.JJUndoSystem.captureSnapshot('색상 복원');
            window.JJUndoSystem.updateUndoButton();
        }
        lastUndoLabel = '';
        $undo.prop('disabled', true);
        showToast('되돌렸습니다.', 'info');
      });
    }

    render('');
    updateInlinePreview();

    // 색상 변경 시 미리보기 자동 갱신
    $('#jj-style-guide-form').on('change input', '.jj-color-field', function(){
      updateInlinePreview();
    });
  });
})(jQuery);


```

```php
<?php
/**
 * Plugin Name:       ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager (Master)
 * Plugin URI:        https://j-j-labs.com
 * Description:       WordPress 웹사이트의 모든 스타일 요소(색상 팔레트, 타이포그래피, 버튼, 폼)를 중앙에서 일관되게 관리하는 통합 스타일 관리 플러그인입니다. Free 버전은 기본적인 스타일 관리 기능을 제공하며, 브랜드 일관성을 유지하고 디자인 시스템을 효율적으로 운영할 수 있습니다. Pro 버전 플러그인을 함께 설치하면 Basic, Premium, Unlimited 기능을 사용할 수 있습니다. WordPress Customizer와 완벽 통합되어 실시간 미리보기와 함께 직관적인 스타일 관리가 가능합니다.
 * Version:           8.3.2
 * Author:            3J Labs
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://poetryflow.blog
 * Text Domain:       acf-css-really-simple-style-management-center
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ⚠️ 중요: Git 경로와 로컬 경로는 완전히 별개입니다
 * 
 * - 로컬 작업 경로: C:\Users\computer\OneDrive\Desktop\클라우드 백업\OneDrive\문서\jj-css-premium
 * - Git worktree 경로: C:\Users\computer\.cursor\worktrees\jj-css-premium\tci
 * - 두 경로는 서로 독립적이며, Git에서 작업한 내용은 반드시 로컬 경로에 동기화해야 함
 * - 자세한 내용은 DEVELOPMENT_PRINCIPLES.md의 "Git 및 로컬 경로 관리 원칙" 섹션 참조
 */

// 플러그인 상수 정의
// [v5.1.6] 전면 재검토 및 오류 방지: 안전한 파일 로더 추가, 모든 버전의 require_once 안전 처리, 결제 유도 문구 추가, 플러그인 목록 페이지 바로가기 링크 추가
// [v5.1.6] Comprehensive review and error prevention: Safe file loader added, all versions' require_once safely handled, purchase prompts added, plugin list page quick links added
// [v1.0.2] 모든 버전 플러그인 활성화 안전성 최종 확보, WordPress 함수 호출 안전 처리
if ( ! defined( 'JJ_STYLE_GUIDE_VERSION' ) ) {
    define( 'JJ_STYLE_GUIDE_VERSION', '8.0.0' ); // [v7.0.0] Operation Deep Dive - Stability & Reliability Overhaul
}

// WordPress 함수가 로드되었는지 확인 후 상수 정의
if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    if ( function_exists( 'plugin_dir_path' ) ) {
        define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 직접 경로 계산
        define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
    }
}

if ( ! defined( 'JJ_STYLE_GUIDE_URL' ) ) {
    if ( function_exists( 'plugin_dir_url' ) ) {
        define( 'JJ_STYLE_GUIDE_URL', plugin_dir_url( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 기본값 사용 (나중에 업데이트됨)
        define( 'JJ_STYLE_GUIDE_URL', '' );
    }
}
if ( ! defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) define( 'JJ_STYLE_GUIDE_OPTIONS_KEY', 'jj_style_guide_options' );
if ( ! defined( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY' ) ) define( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY', 'jj_style_guide_temp_options' );
if ( ! defined( 'JJ_STYLE_GUIDE_ADMIN_TEXT_KEY' ) ) define( 'JJ_STYLE_GUIDE_ADMIN_TEXT_KEY', 'jj_style_guide_admin_texts' );
if ( ! defined( 'JJ_STYLE_GUIDE_PAGE_SLUG' ) ) define( 'JJ_STYLE_GUIDE_PAGE_SLUG', 'acf-css-really-simple-style-guide' );
if ( ! defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) ) define( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY', 'jj_style_guide_cockpit_page_id' );
if ( ! defined( 'JJ_STYLE_GUIDE_DEBUG' ) ) define( 'JJ_STYLE_GUIDE_DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
// Pro 버전 활성화 코드 확인 (plugins_loaded 훅에서 처리)
// WordPress가 로드되기 전에는 기본값 사용
// 기본값(빌드 스크립트에서 주입/대체될 수 있음). 이미 정의돼 있으면 건드리지 않습니다.
if ( ! defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', 'MASTER' );
if ( ! defined( 'JJ_STYLE_GUIDE_EDITION' ) ) define( 'JJ_STYLE_GUIDE_EDITION', 'master' );

// [v3.8.0] 환경 구분
if ( ! defined( 'JJ_STYLE_GUIDE_ENV' ) ) {
    define( 'JJ_STYLE_GUIDE_ENV', 'live' );
}

// [v5.3.7] Free 버전은 독립적으로 작동합니다. Master 버전 의존성 제거
// Free 버전은 자체 includes 폴더에 필요한 모든 파일을 포함합니다.

// [v5.4.1] 안전한 파일 로더 적용
$safe_loader = null;
try {
    $safe_loader_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-loader.php';
    if ( file_exists( $safe_loader_path ) ) {
        require_once $safe_loader_path;
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'instance' ) ) {
            $safe_loader = JJ_Safe_Loader::instance();
        }
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Failed: ' . $e->getMessage() );
} catch ( Error $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Fatal Error: ' . $e->getMessage() );
}

/**
 * [B: 안정성 방어막] Safe Loader 폴백
 * - class-jj-safe-loader.php 자체가 누락/손상돼도, 플러그인이 "부팅"은 되도록 합니다.
 * - (원인 파악/자가진단/업데이트 탭 접근) 경로를 확보하는 것이 목표입니다.
 */
$GLOBALS['jj_style_guide_boot_diag'] = isset( $GLOBALS['jj_style_guide_boot_diag'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag'] )
    ? $GLOBALS['jj_style_guide_boot_diag']
    : array(
        'missing_required' => array(),
        'missing_optional' => array(),
        'load_errors'      => array(),
    );

$jj_safe_require = function( $path, $required = true ) {
    $path = (string) $path;
    $required = (bool) $required;

    // 우선: Safe Loader가 있으면 그 진단/기록 기능을 그대로 사용
    if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'safe_require' ) ) {
        return JJ_Safe_Loader::safe_require( $path, $required );
    }

    // 폴백: 최소한의 safe require
    if ( ! file_exists( $path ) ) {
        $key = md5( $path . '|' . ( $required ? '1' : '0' ) );
        $bucket = $required ? 'missing_required' : 'missing_optional';
        if ( ! isset( $GLOBALS['jj_style_guide_boot_diag'][ $bucket ][ $key ] ) ) {
            $GLOBALS['jj_style_guide_boot_diag'][ $bucket ][ $key ] = array(
                'path'     => $path,
                'required' => $required,
                'count'    => 1,
                'reason'   => 'file_not_found',
            );
        } else {
            $GLOBALS['jj_style_guide_boot_diag'][ $bucket ][ $key ]['count']++;
        }

        if ( $required && function_exists( 'error_log' ) ) {
            error_log( 'JJ Fallback Loader: File not found - ' . $path );
        }
        return false;
    }
    try {
        require_once $path;
        return true;
    } catch ( Exception $e ) {
        $GLOBALS['jj_style_guide_boot_diag']['load_errors'][] = array(
            'path'     => $path,
            'required' => $required,
            'error'    => $e->getMessage(),
        );
        if ( function_exists( 'error_log' ) ) {
            error_log( 'JJ Fallback Loader: Exception loading ' . $path . ' - ' . $e->getMessage() );
        }
        return false;
    } catch ( Error $e ) {
        $GLOBALS['jj_style_guide_boot_diag']['load_errors'][] = array(
            'path'     => $path,
            'required' => $required,
            'error'    => $e->getMessage(),
        );
        if ( function_exists( 'error_log' ) ) {
            error_log( 'JJ Fallback Loader: Error loading ' . $path . ' - ' . $e->getMessage() );
        }
        return false;
    }
};

// 라이센스 매니저/에디션/무결성 관련: 가능한 한 일찍 로드
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php', false );
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-version-features.php', true );
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-code-integrity-monitor.php', true );

// 핵심 로직 파일 로드 (Master 전용 파일은 아직 비활성화)
try {
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-handler.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-logger.php', false );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-admin-center.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-backup-manager.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/visual-command-center/class-jj-visual-command-center.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/ai-style-intelligence/class-jj-smart-palette.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-cloud-manager.php', true );
    // [Phase 3] 템플릿 마켓
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-template-market.php', true );
    // [Phase 5] Partner Hub
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/partner-hub/class-jj-partner-hub.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-labs-center.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-selector-registry.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-cache.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-injector.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-options-cache.php', true );
    // [Phase 8.1] Transient Cache (자주 읽히는 옵션 캐싱)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-transient-cache.php', true );
    // [Phase 8.1] Asset Optimizer (성능 최적화)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-asset-optimizer.php', true );
    // [Phase 8.2] Security Hardener (보안 강화)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-security-hardener.php', true );
    // [Phase 8.5.1] AI Extension Promoter (AI Extension 감지 및 활성화 유도)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-ai-extension-promoter.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-edition-controller.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-theme-metadata.php', false );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-0-customizer.php', false );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-1-css-vars.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-2-php-filters.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-3-dequeue.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-sync.php', false );
    // [Phase 5.2] REST API (설정 조회/업데이트)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/api/class-jj-style-guide-rest-api.php', true );
    // [Phase 5.3] 확장 플러그인 시스템 (Extension API)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/interface-jj-style-guide-extension.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/class-jj-extension-manager.php', true );
    // [Phase 5.2] Webhooks (자동화)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/integrations/class-jj-webhook-manager.php', true );
    // [Phase 5 B] Multisite Network Control
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/multisite/class-jj-multisite-controller.php', true );
    
    // Master 전용 파일들
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-sync-manager.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php', false );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-memory-manager.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-mode.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-optimizer.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-issuer.php', false );
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Master Load Error: ' . $e->getMessage() );
}



/**
 * 활성화 훅 래퍼 함수 (치명적 오류 방지)
 * [v5.1.7] 전체 함수를 try-catch로 감싸고 상수 정의 확인 추가
 * [Phase 8.0] 최소 부팅 경로 적용: Activation 시 최소한의 파일만 로드
 */
function jj_simple_style_guide_master_activate() {
    try {
        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // 상수가 정의되지 않은 경우 기본 경로 사용
            if ( function_exists( 'plugin_dir_path' ) ) {
                define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
            } else {
                define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
            }
        }
        
        // [Phase 8.0] 최소 부팅 경로 확인
        // Safe Loader가 이미 로드되어 있어야 함 (최상단에서 로드됨)
        $minimal_boot = array( 'can_boot' => true, 'missing' => array() );
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'get_minimal_boot_path' ) ) {
            $minimal_boot = JJ_Safe_Loader::get_minimal_boot_path();
        }
        
        // 최소 부팅 실패 시 안전하게 종료
        if ( ! $minimal_boot['can_boot'] ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS: 최소 부팅 실패 - ' . ( isset( $minimal_boot['reason'] ) ? $minimal_boot['reason'] : 'Unknown' ) );
            }
            // 플러그인 옵션에 오류 기록 (Admin Center 접근 가능하도록)
            update_option( 'jj_style_guide_activation_error', array(
                'timestamp' => current_time( 'mysql' ),
                'reason' => 'minimal_boot_failed',
                'missing' => isset( $minimal_boot['missing'] ) ? $minimal_boot['missing'] : array(),
            ) );
            return;
        }
        
        // 활성화 매니저 클래스가 로드되지 않은 경우 로드 시도
        if ( ! class_exists( 'JJ_Activation_Manager' ) ) {
            $activation_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php';
            if ( file_exists( $activation_file ) ) {
                // Safe Loader 사용
                if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'safe_require' ) ) {
                    JJ_Safe_Loader::safe_require( $activation_file, true );
                } else {
                    require_once $activation_file;
                }
            } else {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 파일을 찾을 수 없습니다. 기본 활성화만 수행합니다.' );
                }
                // 최소한의 활성화 정보만 저장
                update_option( 'jj_style_guide_activated', current_time( 'mysql' ) );
                return;
            }
        }
        
        // 클래스가 존재하는지 확인 후 활성화 실행
        if ( class_exists( 'JJ_Activation_Manager' ) && method_exists( 'JJ_Activation_Manager', 'activate' ) ) {
            try {
                JJ_Activation_Manager::activate();
                // 활성화 성공 시 오류 정보 삭제
                delete_option( 'jj_style_guide_activation_error' );
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 오류 발생 - ' . $e->getMessage() );
                }
                // 오류 정보 저장
                update_option( 'jj_style_guide_activation_error', array(
                    'timestamp' => current_time( 'mysql' ),
                    'reason' => 'activation_exception',
                    'message' => $e->getMessage(),
                ) );
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 치명적 오류 발생 - ' . $e->getMessage() );
                }
                // 오류 정보 저장
                update_option( 'jj_style_guide_activation_error', array(
                    'timestamp' => current_time( 'mysql' ),
                    'reason' => 'activation_fatal_error',
                    'message' => $e->getMessage(),
                ) );
            }
        } else {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 클래스를 사용할 수 없습니다.' );
            }
        }
    } catch ( Exception $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 예외 발생 - ' . $e->getMessage() );
        }
    } catch ( Error $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 치명적 오류 발생 - ' . $e->getMessage() );
        }
    }
}
register_activation_hook( __FILE__, 'jj_simple_style_guide_master_activate' );

/**
 * Free 버전 전용 메인 컨트롤 클래스
 */
final class JJ_Simple_Style_Guide_Master {

    private static $instance = null;
    private $options = array();
    private $cockpit_page_id = 0;
    private $options_key = '';
    private $brand_colors = array();
    private $environment_map = array();
    private $adapters_config = array();
    private $license_manager = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 상수 정의 확인 후 할당
        if ( defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) {
            $this->options_key = JJ_STYLE_GUIDE_OPTIONS_KEY;
        }
        
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $this->license_manager = JJ_License_Manager::instance();
        }
        
        if ( defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) && function_exists( 'get_option' ) ) {
            $this->cockpit_page_id = (int) get_option( JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY );
        }

        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // [v5.3.7] Free 버전은 자체 config 폴더에서 로드
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
            if ( file_exists( $config_file ) ) {
                $this->adapters_config = include( $config_file );
                // [v5.5.0] Edition Controller를 통한 동적 어댑터 필터링
                if ( class_exists( 'JJ_Edition_Controller' ) ) {
                    $this->adapters_config = JJ_Edition_Controller::instance()->filter_adapters( $this->adapters_config );
                }
                
                
            }
        }

        // WordPress 함수가 로드된 후에만 훅 등록
        // [v5.1.9] plugin_basename() 안전 호출 추가
        // [v5.3.9] 모든 훅 등록을 조건문 안으로 이동하여 안전성 확보
        if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) && function_exists( 'plugin_basename' ) ) {
            add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
            add_action( 'admin_menu', array( $this, 'add_admin_menu_links' ) );
            $plugin_basename = function_exists( 'plugin_basename' ) ? plugin_basename( __FILE__ ) : basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );
            add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_plugin_settings_link' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            // [v8.x] 부팅 진단: Safe Loader에서 누락/로드 오류 감지 시 관리자에게 안내
            add_action( 'admin_notices', array( $this, 'maybe_show_boot_diagnostics_notice' ) );
            add_action( 'wp_ajax_jj_save_style_guide', array( $this, 'save_settings_ajax' ) );
            add_action( 'wp_ajax_jj_apply_temp_palette', array( $this, 'apply_temp_palette_ajax' ) );
            add_action( 'wp_ajax_jj_save_temp_palette', array( $this, 'save_temp_palette_ajax' ) );
            // [v3.8.0] Customizer 색상 새로고침 (팔레트 동기화)
            if ( class_exists( 'JJ_Customizer_Sync' ) ) {
                add_action( 'wp_ajax_jj_load_current_colors', array( 'JJ_Customizer_Sync', 'ajax_load_current_colors' ) );
            }
            // [v3.8.0] 섹션별 내보내기/불러오기 (팔레트 등)
            add_action( 'wp_ajax_jj_export_section', array( $this, 'ajax_export_section' ) );
            add_action( 'wp_ajax_jj_import_section', array( $this, 'ajax_import_section' ) );
            // [AI Presets] AI Extension에서 저장한 추천 팔레트 관리(핀/삭제 등)
            add_action( 'wp_ajax_jj_ai_palette_preset_manage', array( $this, 'ajax_ai_palette_preset_manage' ) );
            add_action( 'init', array( $this, 'load_options_and_init_strategies' ), 5 );
            add_action( 'init', array( $this, 'init_sync_manager' ), 10 );
            
            
            if ( JJ_STYLE_GUIDE_DEBUG ) {
                add_action( 'wp_footer', array( $this, 'render_debug_info' ), 999 );
                add_action( 'admin_footer', array( $this, 'render_debug_info' ), 999 );
            }
        }

        // Customizer 통합 (제한적)
        
        try {
            $customizer_manager_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php';
            if ( file_exists( $customizer_manager_path ) ) {
                require_once $customizer_manager_path;
                if ( class_exists( 'JJ_Customizer_Manager' ) ) {
                    $customizer_manager = JJ_Customizer_Manager::instance();
                    if ( method_exists( $customizer_manager, 'init' ) ) {
                        $customizer_manager->init();
                    }
                }
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }

    }

    /**
     * Free 버전: 어댑터 제한
     * 
     * @param array $config
     * @return array
     */
    private function limit_adapters_for_free( $config ) {
        // Free 버전: 테마 2개만 지원 (Twenty Twenty-Four, Twenty Twenty-Five)
        $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
        if ( isset( $config['themes'] ) && is_array( $config['themes'] ) ) {
            $config['themes'] = array_intersect_key( $config['themes'], array_flip( $allowed_themes ) );
        }

        // Free 버전: 플러그인 1개만 지원 (WooCommerce)
        $allowed_plugins = array( 'woocommerce' );
        if ( isset( $config['spokes'] ) && is_array( $config['spokes'] ) ) {
            $config['spokes'] = array_intersect_key( $config['spokes'], array_flip( $allowed_plugins ) );
        }

        return $config;
    }

    /**
     * Free 버전 워터마크 추가
     */
    public function add_free_watermark() {
        if ( ! is_admin() && current_user_can( 'manage_options' ) ) {
            echo '<div style="position: fixed; bottom: 10px; right: 10px; background: rgba(0, 0, 0, 0.7); color: #fff; padding: 8px 12px; font-size: 11px; border-radius: 4px; z-index: 99999; font-family: Arial, sans-serif;">';
            echo '<a href="' . esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ) . '" style="color: #fff; text-decoration: none;">JJ Style Guide Free</a>';
            echo '</div>';
        }
    }

    /**
     * 업그레이드 프롬프트 표시
     */
    public function show_upgrade_prompt() {
        // [Safety Lock] MASTER 또는 PARTNER 버전은 업그레이드 프롬프트 표시 안 함
        if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'partner' ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        $license_manager = null;
        $upgrade_url = 'https://j-j-labs.com'; // 기본값
        
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
                if ( $license_manager ) {
                    $upgrade_url = $license_manager->get_purchase_url( 'upgrade' );
                }
            }
        }
        ?>
        <div class="notice notice-info is-dismissible" style="background: #f0f6fc; border-left-color: #2271b1;">
            <p style="font-size: 14px; line-height: 1.8;">
                <strong><?php _e( '현재 버전을 사용 중입니다.', 'acf-css-really-simple-style-management-center' ); ?></strong><br>
                <?php _e( '더 많은 기능을 이용하려면 업그레이드하세요:', 'acf-css-really-simple-style-management-center' ); ?>
                <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" class="button button-primary" style="margin-left: 10px;">
                    <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
            </p>
            <ul style="margin: 10px 0; padding-left: 20px; list-style: disc;">
                <li><?php _e( '모든 팔레트 사용 가능 (브랜드, 시스템, 얼터너티브, 어나더, 임시)', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '모든 테마 및 플러그인 어댑터 지원', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '관리자 센터 및 실험실 센터 전체 기능', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '섹션별 내보내기/불러오기', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '고급 색상 선택 기능 (스포이드, 팔레트 불러오기)', 'acf-css-really-simple-style-management-center' ); ?></li>
            </ul>
        </div>
        <?php
    }

    public function init_sync_manager() {
        add_action( 'customize_save_after', array( $this, 'sync_from_customizer' ) );
        JJ_Sync_Manager::instance()->init( $this->options, $this->cockpit_page_id );
    }

    /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * Free 버전: 스타일 센터만 표시
     * [v5.1.9] 안전성 강화 - try-catch 추가
     */
            /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * [Phase 4.99] 퀵 링크 확장: 설정 | 스타일 | 메뉴 | 비주얼
     */
    public function add_plugin_settings_link( $links ) {
        try {
            if ( ! function_exists( 'admin_url' ) || ! function_exists( 'esc_url' ) ) {
                return $links;
            }
            
            $new_links = array();
            
            // 1. 설정 (Admin Center 메인)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ) . '" style="font-weight: 600;">' . __( 'Settings', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 2. 스타일 (Style Guide)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ) ) . '">' . __( 'Styles', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 3. 어드민 메뉴 (Admin Menu Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#admin-menu' ) ) . '">' . __( 'Menu', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 4. 비주얼 (Visual Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#visual' ) ) . '">' . __( 'Visual', 'acf-css-really-simple-style-management-center' ) . '</a>';

            // 기존 링크와 병합
            return array_merge( $new_links, $links );
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 오류 - ' . $e->getMessage() );
            }
            return $links;
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 치명적 오류 - ' . $e->getMessage() );
            }
            return $links;
        }
    }

    public function add_admin_menu_page() {
        add_options_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );
    }

    public function add_admin_menu_links() {
        // [Phase 4.5/4.99] "모양/도구/설정" 어디서나 동일한 화면으로 진입할 수 있도록 서브메뉴 추가
        add_theme_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        add_management_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        if ( $this->cockpit_page_id ) {
            add_submenu_page(
                'themes.php',
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                'manage_options',
                'post.php?post=' . $this->cockpit_page_id . '&action=edit',
                '',
                100
            );
        }
    }

    public function enqueue_admin_scripts( $hook_suffix ) {
        $is_settings_page = in_array(
            $hook_suffix,
            array(
                'settings_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'appearance_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'tools_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
            ),
            true
        );

        $is_cockpit_page = false;
        if ( 'post.php' === $hook_suffix ) {
            global $post;
            if ( $post && (int) $post->ID === $this->cockpit_page_id ) {
                $is_cockpit_page = true;
            }
        }

        if ( ! $is_settings_page && ! $is_cockpit_page ) {
            return;
        }

        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;

        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        // [v5.3.7] Free 버전은 자체 assets 폴더에서 로드
        wp_enqueue_style( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-editor.css', array( 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION );
        wp_enqueue_script( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-editor.js', array( 'jquery', 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION, true );
        // [UI/UX] 팔레트 프리셋 + 인라인 프리뷰
        $presets_css_ver = JJ_STYLE_GUIDE_VERSION;
        $presets_js_ver  = JJ_STYLE_GUIDE_VERSION;
        $presets_css_path = JJ_STYLE_GUIDE_PATH . 'assets/css/jj-style-guide-presets.css';
        $presets_js_path  = JJ_STYLE_GUIDE_PATH . 'assets/js/jj-style-guide-presets.js';
        if ( file_exists( $presets_css_path ) ) {
            $presets_css_ver .= '.' . (string) filemtime( $presets_css_path );
        }
        if ( file_exists( $presets_js_path ) ) {
            $presets_js_ver .= '.' . (string) filemtime( $presets_js_path );
        }
        wp_enqueue_style( 'jj-style-guide-presets', JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-presets.css', array( 'jj-style-guide-editor' ), $presets_css_ver );
        wp_enqueue_script( 'jj-style-guide-presets', JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-presets.js', array( 'jj-style-guide-editor', 'jquery' ), $presets_js_ver, true );

        $preview_page_id = $this->cockpit_page_id;
        $preview_url = $preview_page_id ? get_permalink( $preview_page_id ) : '';

        wp_localize_script(
            'jj-style-guide-editor',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'temp_palette_nonce' => wp_create_nonce( 'jj_temp_palette_nonce' ),
                'settings' => $this->options,
                // AI Extension이 저장한 "추천 팔레트 카드" 목록 (있으면 프리셋 UI에 합류)
                'ai_palette_presets' => (function() {
                    $items = get_option( 'jj_style_guide_ai_palette_presets', array() );
                    return is_array( $items ) ? $items : array();
                })(),
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'edition'  => 'free', // Free 버전 표시
                'i18n'     => array(
                    'saving' => __( '저장 중...', 'acf-css-really-simple-style-management-center' ),
                    'saved'  => __( '스타일 저장됨', 'acf-css-really-simple-style-management-center' ),
                    'applied' => __( '임시 팔레트 적용됨! \'스타일 저장\'을 눌러야 최종 반영됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'error'  => __( '오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
                    'confirm_apply' => __( '정말로 \'임시 팔레트\'의 값으로 덮어쓰시겠습니까?\n이 작업은 즉시 UI에 반영되지만, \n\'스타일 저장\' 버튼을 눌러야 최종 저장됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'preview_url' => $preview_url ? esc_url( $preview_url ) : '',
                    'preview_page_id' => $preview_page_id,
                ),
            )
        );
    }

    public function render_style_guide_page() {
        try {
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options      = $options;
            $temp_options       = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY );
            $options            = $this->options;
            $cockpit_page_id    = $this->cockpit_page_id;
            $preview_url        = $cockpit_page_id ? get_permalink( $cockpit_page_id ) : '';

        // 옵션이 비어있으면 기본값 설정
        if ( empty( $this->options ) ) {
            $this->options = array(
                'palettes' => array(
                    'brand' => array(
                        'primary_color' => '',
                        'primary_color_hover' => '',
                        'secondary_color' => '',
                        'secondary_color_hover' => '',
                    ),
                    'system' => array(), // Free 버전: 시스템 팔레트 비활성화
                    'alternative' => array(), // Free 버전: 얼터너티브 팔레트 비활성화
                    'another' => array(), // Free 버전: 어나더 팔레트 비활성화
                ),
                'typography' => array(),
                'buttons' => array(),
                'forms' => array(),
                'contexts' => array(),
            );
            $options = $this->options;
        }
        ?>
        <div id="jj-style-guide-wrapper" class="wrap jj-style-guide-wrap" style="background: #f0f0f1; margin: 0; padding: 0;">
            <form id="jj-style-guide-form">

                <div class="jj-style-guide-header" style="top: 32px; background: #fff; padding: 15px 25px; border-bottom: 1px solid #c3c4c7; z-index: 999; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h1 style="margin: 0; font-size: 24px;">
                            <?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?> (v<?php echo esc_html( JJ_STYLE_GUIDE_VERSION ); ?>) 
                            <?php if ( ! class_exists("JJ_Edition_Controller") || JJ_Edition_Controller::instance()->get_current_edition() === "free" ) : ?><span style="background: #d63638; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 14px; font-weight: 600;">Free</span><?php endif; ?>
                        </h1>
                        <?php
                        $license_manager = null;
                        $purchase_url = 'https://j-j-labs.com'; // 기본값
                        
                        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                            if ( class_exists( 'JJ_License_Manager' ) ) {
                                $license_manager = JJ_License_Manager::instance();
                                if ( $license_manager ) {
                                    $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                                }
                            }
                        }
                        ?>
                        <?php if ( ! class_exists("JJ_Edition_Controller") || ! JJ_Edition_Controller::instance()->is_at_least("partner") ) : ?><a href="<?php echo esc_url( $purchase_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary" style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px;">
                            <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                        </a><?php endif; ?>
                        <?php if ( $preview_url ) : ?>
                        <button type="button" 
                                class="button button-secondary" 
                                id="jj-preview-button"
                                style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> <?php _e( '실시간 미리보기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="jj-header-actions" style="display: flex; align-items: center; gap: 10px;">
                        <span id="jj-save-success" style="display: none; vertical-align: middle; color: #2271b1; font-weight: 600; margin-right: 15px;"></span>
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-save-style-guide" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
                <div class="jj-style-guide-content" style="margin: 20px auto; background: #ffffff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 1px;">
                    <?php
                    // Free 버전: 제한된 섹션만 표시
                    // [v5.3.7] Free 버전은 자체 includes/editor-views 폴더에서 로드
                    $sections_map = array(
                        'colors'     => 'includes/editor-views/view-section-colors.php',
                        'typography' => 'includes/editor-views/view-section-typography.php',
                        'buttons'    => 'includes/editor-views/view-section-buttons.php',
                        'forms'      => 'includes/editor-views/view-section-forms.php',
                    );

                    // Free 버전: 섹션 순서 고정
                    $sections_to_render = array();
                    foreach ( $sections_map as $slug => $relative_path ) {
                        $sections_to_render[] = array(
                            'slug' => $slug,
                            'file' => JJ_STYLE_GUIDE_PATH . $relative_path,
                            'order'=> 0, // 순서는 UI에서만 표시
                        );
                    }
                    ?>
                    <div id="jj-sections-sortable" class="jj-sections-sortable">
                        <?php
                        foreach ( $sections_to_render as $section ) {
                            // Free 버전: 기본 뷰 파일 사용 (제한 로직은 뷰 파일 내부에서 처리)
                            $default_file = JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-section-' . $section['slug'] . '.php';
                            if ( file_exists( $section['file'] ) ) {
                                echo '<div class="jj-section-wrapper" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $section['file'];
                                echo '</div>';
                            } elseif ( file_exists( $default_file ) ) {
                                // Free 버전 전용 뷰가 없으면 기본 뷰 사용 (제한 로직 적용 필요)
                                echo '<div class="jj-section-wrapper jj-section-free-version" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $default_file;
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="jj-style-guide-footer" style="margin: 20px auto; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: #666; font-size: 13px;">
                        <?php
                        // Master/Partner에서는 업그레이드 유도 문구를 절대 노출하지 않음
                        $show_upgrade_footer = true;
                        if ( class_exists( 'JJ_Edition_Controller' ) ) {
                            try {
                                $show_upgrade_footer = ! JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                            } catch ( Exception $e ) {
                                // ignore
                            } catch ( Error $e ) {
                                // ignore
                            }
                        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
                            $show_upgrade_footer = ! in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
                        }

                        if ( $show_upgrade_footer ) {
                            _e( '더 많은 기능을 이용하려면 업그레이드하세요.', 'acf-css-really-simple-style-management-center' );
                        }
                        ?>
                    </div>
                    <div style="text-align: right;">
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-style-guide-footer-button" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        } catch ( Exception $e ) {
            // 오류 발생 시에도 기본 페이지 구조는 표시
            ?>
            <div class="wrap" style="padding: 20px;">
                <h1><?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?></h1>
                <div class="notice notice-error">
                    <p><strong><?php _e( '오류가 발생했습니다:', 'acf-css-really-simple-style-management-center' ); ?></strong> <?php echo esc_html( $e->getMessage() ); ?></p>
                </div>
            </div>
            <?php
        }
    }

    // 나머지 메서드들은 dev 버전과 동일하지만, 기능 제한 로직 추가 필요
    // save_settings_ajax, apply_temp_palette_ajax, save_temp_palette_ajax 등
    
    public function save_settings_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        if ( ! isset( $_POST['settings'] ) || ! is_array( $_POST['settings'] ) ) {
            wp_send_json_error( array( 'message' => __( '잘못된 데이터입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // [Phase 5 B] 멀티사이트 네트워크 전용 모드에서는 사이트별 저장 차단
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $ms = JJ_Multisite_Controller::instance();
                if ( method_exists( $ms, 'is_enabled' ) && method_exists( $ms, 'allow_site_override' ) ) {
                    if ( $ms->is_enabled() && ! $ms->allow_site_override() ) {
                        wp_send_json_error( array( 'message' => __( '이 사이트는 네트워크에서 스타일을 관리 중입니다. 네트워크 관리자에서 변경해주세요.', 'acf-css-really-simple-style-management-center' ) ) );
                    }
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        $old_settings = array();
        if ( function_exists( 'get_option' ) ) {
            $old_settings = (array) get_option( $this->options_key, array() );
        }

        $raw_settings = wp_unslash( $_POST['settings'] );
        
        // 에디션별 저장 제한 (Free만 제한, Partner/Master는 무제한)
        $current_edition = 'free';
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $current_edition = (string) JJ_Edition_Controller::instance()->get_current_edition();
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_EDITION' ) ) {
            $current_edition = (string) JJ_STYLE_GUIDE_EDITION;
        }

        if ( 'free' === $current_edition ) {
            $raw_settings = $this->filter_settings_for_free( $raw_settings );
        }
        
        $sanitized_settings = $this->sanitize_options_recursive( $raw_settings );
        update_option( $this->options_key, $sanitized_settings );

        // [Phase 5.2] Webhook/자동화용 이벤트
        if ( function_exists( 'do_action' ) ) {
            do_action( 'jj_style_guide_settings_updated', $sanitized_settings, $old_settings, 'style_center_ajax' );
        }

        $this->sync_to_spokes( $sanitized_settings );

        JJ_CSS_Cache::instance()->flush();
        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * Free 버전: 저장 가능한 설정 필터링
     */
    private function filter_settings_for_free( $settings ) {
        // Free 버전: 시스템 팔레트, 얼터너티브 팔레트, 어나더 팔레트 제거
        if ( isset( $settings['palettes'] ) ) {
            unset( $settings['palettes']['system'] );
            unset( $settings['palettes']['alternative'] );
            unset( $settings['palettes']['another'] );
        }
        
        // Free 버전: 타이포그래피 H5, H6 제거
        if ( isset( $settings['typography'] ) ) {
            unset( $settings['typography']['h5'] );
            unset( $settings['typography']['h6'] );
        }
        
        // Free 버전: Secondary, Text 버튼 제거
        if ( isset( $settings['buttons'] ) ) {
            unset( $settings['buttons']['secondary'] );
            unset( $settings['buttons']['text'] );
        }
        
        return $settings;
    }

    public function apply_temp_palette_ajax() {
        check_ajax_referer( 'jj_temp_palette_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 기능 허용 여부(버전 기능/에디션)
        $can_use_temp = false;
        if ( class_exists( 'JJ_Version_Features' ) ) {
            try {
                $vf = JJ_Version_Features::instance();
                $can_use_temp = $vf ? $vf->can_use_palette( 'temp' ) : false;
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $can_use_temp = JJ_Edition_Controller::instance()->is_at_least( 'premium' ) || JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $can_use_temp = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }

        if ( ! $can_use_temp ) {
            $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
            if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
                $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
            }
            wp_send_json_error( array( 'message' => $message ) );
        }

        $target_palette = isset( $_POST['target_palette'] ) ? sanitize_key( wp_unslash( $_POST['target_palette'] ) ) : 'brand';
        if ( 'brand' !== $target_palette ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 대상 팔레트입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
        $temp_palettes = isset( $temp_options['palettes'] ) && is_array( $temp_options['palettes'] ) ? $temp_options['palettes'] : array();
        $temp_brand = isset( $temp_palettes['brand'] ) && is_array( $temp_palettes['brand'] ) ? $temp_palettes['brand'] : array();

        if ( empty( $temp_brand ) ) {
            wp_send_json_error( array( 'message' => __( '임시 팔레트에 저장된 값이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        if ( ! isset( $options['palettes'] ) || ! is_array( $options['palettes'] ) ) {
            $options['palettes'] = array();
        }
        if ( ! isset( $options['palettes']['brand'] ) || ! is_array( $options['palettes']['brand'] ) ) {
            $options['palettes']['brand'] = array();
        }

        $options['palettes']['brand'] = array_merge( (array) $options['palettes']['brand'], (array) $temp_brand );
        update_option( $this->options_key, $options );

        if ( class_exists( 'JJ_CSS_Cache' ) && method_exists( 'JJ_CSS_Cache', 'instance' ) ) {
            try {
                JJ_CSS_Cache::instance()->flush();
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        wp_send_json_success( array(
            'message' => __( '임시 팔레트가 브랜드 팔레트에 적용되었습니다.', 'acf-css-really-simple-style-management-center' ),
            'new_settings' => $options,
        ) );
    }

    public function save_temp_palette_ajax() {
        check_ajax_referer( 'jj_temp_palette_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 기능 허용 여부(버전 기능/에디션)
        $can_use_temp = false;
        if ( class_exists( 'JJ_Version_Features' ) ) {
            try {
                $vf = JJ_Version_Features::instance();
                $can_use_temp = $vf ? $vf->can_use_palette( 'temp' ) : false;
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $can_use_temp = JJ_Edition_Controller::instance()->is_at_least( 'premium' ) || JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $can_use_temp = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }

        if ( ! $can_use_temp ) {
            $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
            if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
                $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
            }
            wp_send_json_error( array( 'message' => $message ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        $brand = isset( $options['palettes']['brand'] ) && is_array( $options['palettes']['brand'] ) ? $options['palettes']['brand'] : array();

        if ( empty( $brand ) ) {
            wp_send_json_error( array( 'message' => __( '브랜드 팔레트 값이 비어 있습니다. 먼저 브랜드 색상을 입력해 주세요.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
        if ( ! isset( $temp_options['palettes'] ) || ! is_array( $temp_options['palettes'] ) ) {
            $temp_options['palettes'] = array();
        }
        $temp_options['palettes']['brand'] = $brand;

        update_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, $temp_options );

        wp_send_json_success( array(
            'message' => __( '현재 브랜드 팔레트가 임시 팔레트로 저장되었습니다.', 'acf-css-really-simple-style-management-center' ),
            'temp_palettes' => $temp_options['palettes'],
        ) );
    }

    /**
     * [v3.8.0] 섹션별 내보내기
     * - 현재는 팔레트(brand/system/alternative/another/temp-palette)만 지원
     * - 관리자에서 파일 다운로드(attachment)로 전달
     */
    public function ajax_export_section() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $section_type = isset( $_POST['section_type'] ) ? sanitize_key( wp_unslash( $_POST['section_type'] ) ) : '';
        $section_subtype = isset( $_POST['section_subtype'] ) ? sanitize_key( wp_unslash( $_POST['section_subtype'] ) ) : '';

        if ( 'palette' !== $section_type ) {
            wp_die( esc_html__( '지원하지 않는 섹션 타입입니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $allowed_subtypes = array( 'brand', 'system', 'alternative', 'another', 'temp' );
        if ( ! in_array( $section_subtype, $allowed_subtypes, true ) ) {
            wp_die( esc_html__( '지원하지 않는 섹션 서브타입입니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        $data = array();

        if ( 'temp' === $section_subtype ) {
            $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
            $data = isset( $temp_options['palettes'] ) ? (array) $temp_options['palettes'] : array();
        } else {
            $data = isset( $options['palettes'][ $section_subtype ] ) ? (array) $options['palettes'][ $section_subtype ] : array();
        }

        $payload = array(
            'exported_at'   => function_exists( 'current_time' ) ? current_time( 'mysql' ) : gmdate( 'c' ),
            'plugin'        => 'acf-css-really-simple-style-management-center',
            'plugin_version'=> defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '',
            'section_type'  => $section_type,
            'section_subtype' => $section_subtype,
            'data'          => $data,
        );

        if ( function_exists( 'nocache_headers' ) ) {
            nocache_headers();
        }

        $filename = sprintf(
            'acf-css-%s-%s-%s.json',
            $section_type,
            $section_subtype,
            gmdate( 'Ymd-His' )
        );

        // 다운로드 헤더
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

        echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        exit;
    }

    /**
     * [v3.8.0] 섹션별 불러오기
     * - JSON 업로드 후 옵션에 병합(키 기준 덮어쓰기)
     */
    public function ajax_import_section() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $section_type = isset( $_POST['section_type'] ) ? sanitize_key( wp_unslash( $_POST['section_type'] ) ) : '';
        $section_subtype = isset( $_POST['section_subtype'] ) ? sanitize_key( wp_unslash( $_POST['section_subtype'] ) ) : '';

        if ( 'palette' !== $section_type ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 섹션 타입입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $allowed_subtypes = array( 'brand', 'system', 'alternative', 'another', 'temp' );
        if ( ! in_array( $section_subtype, $allowed_subtypes, true ) ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 섹션 서브타입입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        if ( empty( $_FILES['import_file'] ) || empty( $_FILES['import_file']['tmp_name'] ) ) {
            wp_send_json_error( array( 'message' => __( '업로드된 파일이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $tmp_name = $_FILES['import_file']['tmp_name'];
        $raw = file_get_contents( $tmp_name );
        if ( ! $raw ) {
            wp_send_json_error( array( 'message' => __( '파일을 읽을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $decoded = json_decode( $raw, true );
        if ( ! is_array( $decoded ) ) {
            wp_send_json_error( array( 'message' => __( 'JSON 형식이 올바르지 않습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // payload 형식({data:{...}})과 순수 데이터({..}) 둘 다 허용
        $import_data = isset( $decoded['data'] ) && is_array( $decoded['data'] ) ? (array) $decoded['data'] : $decoded;

        // 간단 정리(색상 키는 hex로)
        $sanitized = array();
        foreach ( (array) $import_data as $k => $v ) {
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( (string) $k ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $k ) );
            if ( is_array( $v ) ) {
                // 팔레트는 1depth만 정리 (필요 시 확장)
                $sanitized[ $key ] = $v;
                continue;
            }
            if ( strpos( $key, 'color' ) !== false ) {
                $hex = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( (string) $v ) : '';
                $sanitized[ $key ] = $hex ? $hex : '';
                continue;
            }
            $sanitized[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( (string) $v ) : strip_tags( (string) $v );
        }

        if ( 'temp' === $section_subtype ) {
            $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
            if ( ! isset( $temp_options['palettes'] ) || ! is_array( $temp_options['palettes'] ) ) {
                $temp_options['palettes'] = array();
            }
            $temp_options['palettes'] = array_merge( (array) $temp_options['palettes'], $sanitized );
            update_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, $temp_options );
        } else {
            $options = (array) get_option( $this->options_key, array() );
            if ( ! isset( $options['palettes'] ) || ! is_array( $options['palettes'] ) ) {
                $options['palettes'] = array();
            }
            $options['palettes'][ $section_subtype ] = array_merge(
                (array) ( $options['palettes'][ $section_subtype ] ?? array() ),
                $sanitized
            );
            update_option( $this->options_key, $options );
        }

        wp_send_json_success( array(
            'message' => __( '설정이 성공적으로 불러와졌습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }

    /**
     * AI 저장 추천 팔레트 관리 (핀/삭제/이름변경)
     *
     * 요청:
     * - action: jj_ai_palette_preset_manage
     * - security: jj_style_guide_nonce
     * - op: pin|unpin|delete|rename
     * - id: preset id (ai- 로 시작)
     * - name: (rename 시) 새 이름
     *
     * @return void
     */
    public function ajax_ai_palette_preset_manage() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $op = isset( $_POST['op'] ) ? sanitize_key( wp_unslash( $_POST['op'] ) ) : '';
        $id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
        $name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';

        if ( '' === $op || '' === $id ) {
            wp_send_json_error( array( 'message' => __( '요청 파라미터가 올바르지 않습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 안전: AI 프리셋만 조작
        if ( 0 !== strpos( $id, 'ai-' ) ) {
            wp_send_json_error( array( 'message' => __( 'AI 프리셋만 관리할 수 있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $allowed = array( 'pin', 'unpin', 'delete', 'rename', 'import', 'bulk_pin', 'bulk_unpin', 'bulk_delete' );
        if ( ! in_array( $op, $allowed, true ) ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 작업입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $option_key = 'jj_style_guide_ai_palette_presets';
        $items = get_option( $option_key, array() );
        if ( ! is_array( $items ) ) {
            $items = array();
        }

        // Bulk ops: ids[] 목록 처리
        if ( in_array( $op, array( 'bulk_pin', 'bulk_unpin', 'bulk_delete' ), true ) ) {
            $ids = isset( $_POST['ids'] ) ? (array) wp_unslash( $_POST['ids'] ) : array();
            $ids = array_values(
                array_unique(
                    array_filter(
                        array_map(
                            function( $v ) {
                                return sanitize_text_field( (string) $v );
                            },
                            $ids
                        )
                    )
                )
            );
            if ( empty( $ids ) ) {
                wp_send_json_error( array( 'message' => __( '선택된 항목이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }
            if ( count( $ids ) > 20 ) {
                wp_send_json_error( array( 'message' => __( '한 번에 최대 20개까지만 처리할 수 있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }
            foreach ( $ids as $one ) {
                if ( 0 !== strpos( $one, 'ai-' ) ) {
                    wp_send_json_error( array( 'message' => __( 'AI 프리셋만 관리할 수 있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
                }
            }

            $set = array();
            foreach ( $ids as $one ) {
                $set[ $one ] = true;
            }

            $found = 0;
            $changed = 0;
            $unchanged = 0;
            $missing = 0;

            if ( 'bulk_delete' === $op ) {
                $before = count( $items );
                $items = array_values(
                    array_filter(
                        $items,
                        function( $p ) use ( $set, &$found, &$changed ) {
                            if ( ! is_array( $p ) ) {
                                return true;
                            }
                            $pid = (string) ( $p['id'] ?? '' );
                            if ( isset( $set[ $pid ] ) ) {
                                $found++;
                                $changed++;
                                return false;
                            }
                            return true;
                        }
                    )
                );
                $missing = max( 0, count( $ids ) - $found );
                // $before not used, but keep for clarity
                unset( $before );
            } else {
                foreach ( $items as &$p ) {
                    if ( ! is_array( $p ) ) {
                        continue;
                    }
                    $pid = (string) ( $p['id'] ?? '' );
                    if ( ! isset( $set[ $pid ] ) ) {
                        continue;
                    }
                    $found++;
                    if ( 'bulk_pin' === $op ) {
                        if ( empty( $p['pinned'] ) ) {
                            $p['pinned'] = true;
                            $changed++;
                        } else {
                            $unchanged++;
                        }
                    } elseif ( 'bulk_unpin' === $op ) {
                        if ( ! empty( $p['pinned'] ) ) {
                            $p['pinned'] = false;
                            $changed++;
                        } else {
                            $unchanged++;
                        }
                    }
                }
                unset( $p );
                $missing = max( 0, count( $ids ) - $found );
            }

            // 정렬: pinned 우선, 최신(created_at) 우선
            usort(
                $items,
                function( $a, $b ) {
                    $ap = ! empty( $a['pinned'] );
                    $bp = ! empty( $b['pinned'] );
                    if ( $ap !== $bp ) {
                        return $ap ? -1 : 1;
                    }
                    $at = (string) ( $a['created_at'] ?? '' );
                    $bt = (string) ( $b['created_at'] ?? '' );
                    if ( $at === $bt ) return 0;
                    return ( $bt < $at ) ? -1 : 1; // desc
                }
            );
            $items = array_slice( $items, 0, 20 );

            update_option( $option_key, $items, false );

            $msg = __( '완료되었습니다.', 'acf-css-really-simple-style-management-center' );
            if ( 'bulk_delete' === $op ) {
                $msg = sprintf( __( '삭제 완료: %d개 삭제, %d개 누락', 'acf-css-really-simple-style-management-center' ), $changed, $missing );
            } elseif ( 'bulk_pin' === $op ) {
                $msg = sprintf( __( '상단 고정: %d개 적용, %d개 이미 고정, %d개 누락', 'acf-css-really-simple-style-management-center' ), $changed, $unchanged, $missing );
            } elseif ( 'bulk_unpin' === $op ) {
                $msg = sprintf( __( '고정 해제: %d개 적용, %d개 이미 해제, %d개 누락', 'acf-css-really-simple-style-management-center' ), $changed, $unchanged, $missing );
            }

            wp_send_json_success(
                array(
                    'message' => $msg,
                    'items' => $items,
                )
            );
        }

        // Import: JSON payload -> presets append (dedupe)
        if ( 'import' === $op ) {
            $payload_json = isset( $_POST['payload_json'] ) ? wp_unslash( $_POST['payload_json'] ) : '';
            if ( '' === $payload_json ) {
                wp_send_json_error( array( 'message' => __( '불러올 JSON이 비어있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }
            if ( strlen( $payload_json ) > 200000 ) { // ~200KB
                wp_send_json_error( array( 'message' => __( '불러올 데이터가 너무 큽니다. (안전상 제한) JSON을 나눠서 가져오세요.', 'acf-css-really-simple-style-management-center' ) ) );
            }

            $payload = json_decode( $payload_json, true );
            if ( null === $payload ) {
                wp_send_json_error( array( 'message' => __( 'JSON 파싱에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }

            $incoming = array();
            if ( is_array( $payload ) && isset( $payload[0] ) ) {
                $incoming = $payload;
            } elseif ( is_array( $payload ) ) {
                $incoming = array( $payload );
            } else {
                wp_send_json_error( array( 'message' => __( 'JSON 형식이 올바르지 않습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }

            // 안전: 한 번에 너무 많은 항목을 처리하지 않음 (서버 부하 방지)
            $incoming_trimmed = 0;
            if ( count( $incoming ) > 50 ) {
                $incoming_trimmed = count( $incoming ) - 50;
                $incoming = array_slice( $incoming, 0, 50 );
            }

            $sanitize_hex = function( $value, $fallback = '' ) {
                $v = is_string( $value ) ? $value : '';
                $v = trim( $v );
                if ( '' === $v ) {
                    $v = (string) $fallback;
                }
                if ( function_exists( 'sanitize_hex_color' ) ) {
                    $hex = sanitize_hex_color( $v );
                    return $hex ? strtoupper( $hex ) : '';
                }
                return $v;
            };

            // existing signatures to dedupe
            $sigs = array();
            foreach ( $items as $ex ) {
                if ( ! is_array( $ex ) ) {
                    continue;
                }
                $b = isset( $ex['brand'] ) && is_array( $ex['brand'] ) ? $ex['brand'] : array();
                $s = isset( $ex['system'] ) && is_array( $ex['system'] ) ? $ex['system'] : array();
                $sig = md5(
                    (string) ( $b['primary'] ?? '' ) . '|' .
                    (string) ( $b['secondary'] ?? '' ) . '|' .
                    (string) ( $s['site_bg'] ?? '' ) . '|' .
                    (string) ( $s['content_bg'] ?? '' ) . '|' .
                    (string) ( $s['text_color'] ?? '' ) . '|' .
                    (string) ( $s['link_color'] ?? '' )
                );
                $sigs[ $sig ] = true;
            }

            $imported = 0;
            $skipped = (int) $incoming_trimmed;

            foreach ( $incoming as $row ) {
                if ( ! is_array( $row ) ) {
                    $skipped++;
                    continue;
                }

                $name_in = isset( $row['name'] ) ? sanitize_text_field( (string) $row['name'] ) : '';
                if ( '' === $name_in ) {
                    $name_in = __( '가져온 AI 팔레트', 'acf-css-really-simple-style-management-center' );
                }
                if ( function_exists( 'mb_substr' ) ) {
                    $name_in = mb_substr( $name_in, 0, 80 );
                } else {
                    $name_in = substr( $name_in, 0, 80 );
                }

                $note_in = isset( $row['note'] ) ? sanitize_text_field( (string) $row['note'] ) : '';
                if ( function_exists( 'mb_substr' ) ) {
                    $note_in = mb_substr( $note_in, 0, 220 );
                } else {
                    $note_in = substr( $note_in, 0, 220 );
                }

                $tags_in = array();
                if ( isset( $row['tags'] ) && is_array( $row['tags'] ) ) {
                    foreach ( $row['tags'] as $t ) {
                        $ts = sanitize_text_field( (string) $t );
                        if ( '' !== $ts ) {
                            $tags_in[] = $ts;
                        }
                    }
                }
                if ( ! in_array( 'AI', $tags_in, true ) ) {
                    array_unshift( $tags_in, 'AI' );
                }
                $tags_in = array_values( array_unique( $tags_in ) );
                $tags_in = array_slice( $tags_in, 0, 8 );

                $brand_in = isset( $row['brand'] ) && is_array( $row['brand'] ) ? $row['brand'] : array();
                $system_in = isset( $row['system'] ) && is_array( $row['system'] ) ? $row['system'] : array();

                $primary = $sanitize_hex( $brand_in['primary'] ?? '' );
                if ( '' === $primary ) {
                    $skipped++;
                    continue;
                }
                $secondary = $sanitize_hex( $brand_in['secondary'] ?? '', $primary );

                $site_bg = $sanitize_hex( $system_in['site_bg'] ?? '' );
                $content_bg = $sanitize_hex( $system_in['content_bg'] ?? '' );
                $text_color = $sanitize_hex( $system_in['text_color'] ?? '' );
                $link_color = $sanitize_hex( $system_in['link_color'] ?? '', $primary );

                $sig2 = md5( $primary . '|' . $secondary . '|' . $site_bg . '|' . $content_bg . '|' . $text_color . '|' . $link_color );
                if ( isset( $sigs[ $sig2 ] ) ) {
                    $skipped++;
                    continue;
                }
                $sigs[ $sig2 ] = true;

                $new_id = 'ai-' . gmdate( 'Ymd-His' ) . '-' . substr( md5( wp_generate_uuid4() . microtime( true ) ), 0, 8 );
                $created_at = function_exists( 'current_time' ) ? current_time( 'mysql' ) : gmdate( 'Y-m-d H:i:s' );

                $new_item = array(
                    'id' => $new_id,
                    'name' => $name_in,
                    'tags' => $tags_in,
                    'note' => $note_in,
                    'brand' => array(
                        'primary' => $primary,
                        'secondary' => $secondary,
                        'primary_hover' => $sanitize_hex( $brand_in['primary_hover'] ?? '', $primary ),
                        'secondary_hover' => $sanitize_hex( $brand_in['secondary_hover'] ?? '', $secondary ),
                    ),
                    'system' => array(
                        'site_bg' => $site_bg,
                        'content_bg' => $content_bg,
                        'text_color' => $text_color,
                        'link_color' => $link_color,
                    ),
                    'source' => 'ai_extension',
                    'created_at' => $created_at,
                    'pinned' => ! empty( $row['pinned'] ),
                );

                array_unshift( $items, $new_item );
                $imported++;
            }

            // 정렬: pinned 우선, 최신(created_at) 우선
            usort(
                $items,
                function( $a, $b ) {
                    $ap = ! empty( $a['pinned'] );
                    $bp = ! empty( $b['pinned'] );
                    if ( $ap !== $bp ) {
                        return $ap ? -1 : 1;
                    }
                    $at = (string) ( $a['created_at'] ?? '' );
                    $bt = (string) ( $b['created_at'] ?? '' );
                    if ( $at === $bt ) return 0;
                    return ( $bt < $at ) ? -1 : 1; // desc
                }
            );

            $items = array_slice( $items, 0, 20 );
            update_option( $option_key, $items, false );

            wp_send_json_success(
                array(
                    'message' => sprintf( __( '가져오기 완료: %d개 추가, %d개 건너뜀', 'acf-css-really-simple-style-management-center' ), $imported, $skipped ),
                    'items' => $items,
                )
            );
        }

        $found = false;
        if ( 'delete' === $op ) {
            $before = count( $items );
            $items = array_values(
                array_filter(
                    $items,
                    function( $p ) use ( $id ) {
                        return is_array( $p ) && ( (string) ( $p['id'] ?? '' ) !== (string) $id );
                    }
                )
            );
            $found = ( $before !== count( $items ) );
        } else {
            foreach ( $items as &$p ) {
                if ( ! is_array( $p ) ) {
                    continue;
                }
                if ( (string) ( $p['id'] ?? '' ) !== (string) $id ) {
                    continue;
                }
                $found = true;

                if ( 'pin' === $op ) {
                    $p['pinned'] = true;
                } elseif ( 'unpin' === $op ) {
                    $p['pinned'] = false;
                } elseif ( 'rename' === $op ) {
                    if ( '' === $name ) {
                        wp_send_json_error( array( 'message' => __( '이름이 비어있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
                    }
                    $p['name'] = $name;
                }
                break;
            }
            unset( $p );
        }

        if ( ! $found ) {
            wp_send_json_error( array( 'message' => __( '해당 프리셋을 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 정렬: pinned 우선, 최신(created_at) 우선
        usort(
            $items,
            function( $a, $b ) {
                $ap = ! empty( $a['pinned'] );
                $bp = ! empty( $b['pinned'] );
                if ( $ap !== $bp ) {
                    return $ap ? -1 : 1;
                }
                $at = (string) ( $a['created_at'] ?? '' );
                $bt = (string) ( $b['created_at'] ?? '' );
                if ( $at === $bt ) return 0;
                return ( $bt < $at ) ? -1 : 1; // desc
            }
        );

        // 최대 20개 유지
        $items = array_slice( $items, 0, 20 );

        update_option( $option_key, $items, false );

        $msg = __( '완료되었습니다.', 'acf-css-really-simple-style-management-center' );
        if ( 'delete' === $op ) $msg = __( '삭제되었습니다.', 'acf-css-really-simple-style-management-center' );
        if ( 'pin' === $op ) $msg = __( '상단 고정되었습니다.', 'acf-css-really-simple-style-management-center' );
        if ( 'unpin' === $op ) $msg = __( '고정이 해제되었습니다.', 'acf-css-really-simple-style-management-center' );
        if ( 'rename' === $op ) $msg = __( '이름이 변경되었습니다.', 'acf-css-really-simple-style-management-center' );

        wp_send_json_success(
            array(
                'message' => $msg,
                'items' => $items,
            )
        );
    }

    // 나머지 메서드들은 dev 버전과 동일 (sync_to_spokes, sync_from_customizer, sanitize_options_recursive 등)
    // 생략하거나 dev 버전 파일을 복사하여 수정

    private function sync_to_spokes( $hub_options ) {
        // Free 버전: WooCommerce만 동기화
        $brand_colors = $hub_options['palettes']['brand'] ?? array();
        
        // WooCommerce 동기화
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! is_array( $kwe ) ) {
            $kwe = array();
        }
        if ( isset( $brand_colors['primary_color'] ) ) {
            $kwe['product_quickview_button_color'] = $brand_colors['primary_color'];
        }
        if ( isset( $brand_colors['primary_color_hover'] ) ) {
            $kwe['product_quickview_button_color_hover'] = $brand_colors['primary_color_hover'];
        }
        update_option( 'kt_woo_extras', $kwe );
    }

    public function sync_from_customizer( $manager ) {
        $hub_options = (array) get_option( $this->options_key );
        $panel_settings = $manager->settings();
        
        foreach ( $panel_settings as $setting_id => $setting_obj ) {
            if ( strpos( $setting_id, $this->options_key . '[' ) === 0 ) {
                $keys = str_replace( array( $this->options_key, '[', ']' ), '', $setting_id );
                $keys = explode( '][', $keys );
                $this->set_nested_object_value( $hub_options, $keys, $setting_obj->post_value() );
            }
        }

        update_option( $this->options_key, $hub_options );
        JJ_CSS_Cache::instance()->flush();
    }

    private function set_nested_object_value( &$obj, $keys, $value ) {
        $current = &$obj;
        foreach ( $keys as $key ) {
            if ( ! isset( $current[$key] ) || ! is_array( $current[$key] ) ) {
                $current[$key] = array();
            }
            $current = &$current[$key];
        }
        $current = $value;
    }

    private function sanitize_options_recursive( $input ) {
        $output = array();
        foreach ( (array) $input as $key => $value ) {
            $raw_key = (string) $key;
            // [v5.3.8] WordPress 함수 안전 호출
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( $raw_key ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( $raw_key ) );
            if ( is_array( $value ) ) {
                $output[ $key ] = $this->sanitize_options_recursive( $value );
                continue;
            }
            // [v5.3.8] PHP 7.4 호환성: str_contains() 대신 strpos() 사용
            if ( strpos( $key, 'color' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $sanitized = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( $value ) : '';
                $output[ $key ] = $sanitized ? $sanitized : '';
                continue;
            }
            if ( strpos( $key, 'custom_css' ) !== false || strpos( $key, 'additional_css' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'wp_kses_post' ) ? wp_kses_post( $value ) : wp_strip_all_tags( $value );
                continue;
            }
            if ( strpos( $key, 'font_family' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
                continue;
            }
            // [v5.3.8] WordPress 함수 안전 호출
            $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
        }
        return $output;
    }

    public function load_options_and_init_strategies() {
        // [v5.3.5] 전체 메서드를 try-catch로 보호하여 치명적 오류 방지
        try {
            $this->environment_map = $this->scan_environment();
            $this->load_dynamic_adapters( $this->environment_map );
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options = $options;
            $this->sync_existing_settings();
            $this->brand_colors = $this->options['palettes']['brand'] ?? array();

            // [v5.3.5] 모든 전략 클래스 호출에 class_exists() 확인 추가
            // 전략 1: CSS 변수
            if ( class_exists( 'JJ_Strategy_1_CSS_Vars' ) ) {
                JJ_Strategy_1_CSS_Vars::instance()->init( $this->options );
            }
            
            // 전략 2: PHP 필터
            if ( class_exists( 'JJ_Strategy_2_PHP_Filters' ) ) {
                JJ_Strategy_2_PHP_Filters::instance()->init( $this->options, null, null, null, null );
            }
            
            // 전략 3: Dequeue (CSS 재정의)
            if ( class_exists( 'JJ_Strategy_3_Dequeue' ) ) {
                JJ_Strategy_3_Dequeue::instance()->init( $this->options );
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    private function sync_existing_settings() {
        $active_theme = wp_get_theme()->get_template();
        $needs_update = false;

        if ( ! isset( $this->options['palettes'] ) ) {
            $this->options['palettes'] = array();
        }
        if ( ! isset( $this->options['palettes']['brand'] ) ) {
            $this->options['palettes']['brand'] = array();
        }
        if ( ! isset( $this->options['typography'] ) ) {
            $this->options['typography'] = array();
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five 테마만 동기화
        if ( in_array( $active_theme, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
            // 기본 테마는 별도 설정이 없으므로 Customizer에서 가져오기
        }

        // WooCommerce 동기화 (Free 버전 지원)
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! empty( $kwe['product_quickview_button_color'] ) && empty( $this->options['palettes']['brand']['primary_color'] ) ) {
            $this->options['palettes']['brand']['primary_color'] = $kwe['product_quickview_button_color'];
            $needs_update = true;
        }
        if ( ! empty( $kwe['product_quickview_button_color_hover'] ) && empty( $this->options['palettes']['brand']['primary_color_hover'] ) ) {
            $this->options['palettes']['brand']['primary_color_hover'] = $kwe['product_quickview_button_color_hover'];
            $needs_update = true;
        }

        if ( $needs_update ) {
            update_option( $this->options_key, $this->options );
        }
    }

    private function load_dynamic_adapters( $environment_map ) {
        $active_adapters = $environment_map;
        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;
        $error_handler = JJ_Error_Handler::instance();

        // Free 버전: 테마 어댑터 제한 (Twenty Twenty-Four, Twenty Twenty-Five만)
        if ( ! empty( $active_adapters['themes'] ) ) {
            $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
            $active_adapters['themes'] = array_intersect( $active_adapters['themes'], $allowed_themes );
            
            foreach ( $active_adapters['themes'] as $theme_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-theme-' . sanitize_file_name( $theme_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Theme_' . str_replace( '-', '_', ucwords( $theme_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }

        // Free 버전: 플러그인 어댑터 제한 (WooCommerce만)
        if ( ! empty( $active_adapters['spokes'] ) ) {
            $allowed_plugins = array( 'woocommerce' );
            $active_adapters['spokes'] = array_intersect( $active_adapters['spokes'], $allowed_plugins );
            
            foreach ( $active_adapters['spokes'] as $spoke_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-spoke-' . sanitize_file_name( $spoke_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Spoke_' . str_replace( '-', '_', ucwords( $spoke_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }
    }

    private function scan_environment() {
        $config = $this->adapters_config;
        $spokes_to_check = $config['spokes'] ?? array();
        $themes_to_check = $config['themes'] ?? array();

        $active_plugins = (array) get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        $active_spoke_adapters = array();
        $active_theme_adapters = array();
        $current_theme_path = get_template_directory();
        $theme_dir_name = basename( $current_theme_path );

        // Free 버전: WooCommerce만 체크
        foreach ( $spokes_to_check as $adapter_slug => $plugin_path ) {
            if ( in_array( $plugin_path, $active_plugins ) && $adapter_slug === 'woocommerce' ) {
                $active_spoke_adapters[] = $adapter_slug;
            }
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five만 체크
        foreach ( $themes_to_check as $adapter_slug => $theme_path_suffix ) {
            if ( $theme_dir_name === $adapter_slug && in_array( $adapter_slug, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
                if ( file_exists( $current_theme_path . '/' . $theme_path_suffix ) ) {
                    $active_theme_adapters[] = $adapter_slug;
                }
            }
        }

        return array(
            'themes' => $active_theme_adapters,
            'spokes' => $active_spoke_adapters,
        );
    }

    /**
     * [v8.x] 부팅 진단: Safe Loader 누락/로드 오류를 관리자에게 안내
     * - 치명적 오류를 막기 위한 조기 경고(원인 파악 UX)
     */
    public function maybe_show_boot_diagnostics_notice() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // 플러그인 관련 화면에서만 표시(노이즈 최소화)
        $page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
        $allowed_pages = array( 'jj-admin-center', JJ_STYLE_GUIDE_PAGE_SLUG );
        if ( '' !== $page && ! in_array( $page, $allowed_pages, true ) ) {
            return;
        }

        // 1) Safe Loader 진단 우선
        $missing_required = array();
        $errors  = array();
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'get_missing_files' ) ) {
            $missing = (array) JJ_Safe_Loader::get_missing_files();
            $errors  = method_exists( 'JJ_Safe_Loader', 'get_load_errors' ) ? (array) JJ_Safe_Loader::get_load_errors() : array();

            foreach ( $missing as $m ) {
                if ( is_array( $m ) && ! empty( $m['required'] ) ) {
                    $missing_required[] = $m;
                }
            }
        } else {
            // 2) 폴백 로더 진단 (Safe Loader 자체 누락/손상 대비)
            if ( isset( $GLOBALS['jj_style_guide_boot_diag'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag'] ) ) {
                $mr = isset( $GLOBALS['jj_style_guide_boot_diag']['missing_required'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag']['missing_required'] )
                    ? $GLOBALS['jj_style_guide_boot_diag']['missing_required']
                    : array();
                $missing_required = array_values( $mr );

                $errors = isset( $GLOBALS['jj_style_guide_boot_diag']['load_errors'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag']['load_errors'] )
                    ? $GLOBALS['jj_style_guide_boot_diag']['load_errors']
                    : array();
            }
        }

        if ( empty( $missing_required ) && empty( $errors ) ) {
            return;
        }

        $admin_center_url = function_exists( 'admin_url' ) ? admin_url( 'options-general.php?page=jj-admin-center' ) : '';

        echo '<div class="notice notice-error jj-notice" style="border-left-color:#d63638;">';
        echo '<p><strong>ACF CSS:</strong> ' . esc_html__( '부팅 진단 경고가 감지되었습니다. (파일 누락/로드 오류)', 'acf-css-really-simple-style-management-center' ) . '</p>';
        echo '<ul style="margin: 6px 0 0 18px; list-style: disc;">';
        if ( ! empty( $missing_required ) ) {
            echo '<li>' . esc_html( sprintf( '필수 파일 누락: %d개', count( $missing_required ) ) ) . '</li>';
        }
        if ( ! empty( $errors ) ) {
            echo '<li>' . esc_html( sprintf( '로드 오류: %d개', count( $errors ) ) ) . '</li>';
        }
        echo '</ul>';
        if ( $admin_center_url ) {
            echo '<p style="margin: 8px 0 0 0;">' . sprintf(
                /* translators: %s admin center link */
                esc_html__( '원인 확인: %s → System Status 탭에서 “자가 진단”을 실행하세요.', 'acf-css-really-simple-style-management-center' ),
                '<a href="' . esc_url( $admin_center_url ) . '">' . esc_html__( 'Admin Center', 'acf-css-really-simple-style-management-center' ) . '</a>'
            ) . '</p>';
        }
        echo '</div>';
    }

    public function render_debug_info() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $errors = JJ_Error_Handler::instance()->get_errors();

        echo '<div style="background: #001a33; color: #00ff8c; padding: 20px; font-family: monospace; font-size: 14px; line-height: 1.6; z-index: 99999; position: relative; margin: 20px; border-radius: 5px; white-space: pre-wrap; word-break: break-all; opacity: 0.9;">';
        echo '<strong>[ACF CSS DEBUG - FREE VERSION]</strong><br>';
        echo '====================<br>';
        echo '<strong>Edition:</strong> Free<br>';
        echo '<strong>Loaded Adapters:</strong><br>';
        if ( ! empty( $this->environment_map['themes'] ) ) {
            echo ' - Themes: ' . esc_html( implode( ', ', $this->environment_map['themes'] ) ) . '<br>';
        } else {
            echo ' - Themes: None<br>';
        }
        if ( ! empty( $this->environment_map['spokes'] ) ) {
            echo ' - Spokes: ' . esc_html( implode( ', ', $this->environment_map['spokes'] ) ) . '<br>';
        } else {
            echo ' - Spokes: None<br>';
        }
        echo '</div>';
    }
}

function jj_simple_style_guide_master_run() {
    // [Phase 5] 다국어 지원 로드
    load_plugin_textdomain( 'acf-css-really-simple-style-management-center', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // WordPress 함수가 로드되었는지 확인
    if ( ! function_exists( 'class_exists' ) || ! function_exists( 'file_exists' ) ) {
        return;
    }
    
    // [v5.3.9] 코드 무결성 모니터 초기화 (plugins_loaded 시점)
    
    // [Phase 4.5/4.99] Admin Center 초기화 (메뉴/상단바/관리자 UI)
    if ( class_exists( 'JJ_Admin_Center' ) ) {
        try {
            $admin_center = JJ_Admin_Center::instance();
            if ( method_exists( $admin_center, 'init' ) ) {
                $admin_center->init();
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v3.7.0] 백업 관리자 초기화
    if ( class_exists( 'JJ_Backup_Manager' ) ) {
        try {
            JJ_Backup_Manager::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] REST API 초기화
    if ( class_exists( 'JJ_Style_Guide_REST_API' ) ) {
        try {
            JJ_Style_Guide_REST_API::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.3] Extension Manager 초기화
    if ( class_exists( 'JJ_Extension_Manager' ) ) {
        try {
            JJ_Extension_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] Webhook Manager 초기화
    if ( class_exists( 'JJ_Webhook_Manager' ) ) {
        try {
            JJ_Webhook_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5 B] Multisite Controller 초기화
    if ( class_exists( 'JJ_Multisite_Controller' ) ) {
        try {
            JJ_Multisite_Controller::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v5.6.0] Visual Command Center 초기화
    if ( class_exists( 'JJ_Visual_Command_Center' ) ) {
        JJ_Visual_Command_Center::instance();
    }

    // [v5.7.0] AI Style Intelligence 초기화
    if ( class_exists( 'JJ_Smart_Palette' ) ) {
        JJ_Smart_Palette::instance();
    }

    // [v5.9.0] CSS Optimizer 초기화
    if ( class_exists( 'JJ_CSS_Optimizer' ) ) {
        JJ_CSS_Optimizer::instance();
    }

    // [v5.8.0] Cloud Manager 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Cloud_Manager' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) { // Premium 이상 체크 대용
            JJ_Cloud_Manager::instance();
        }
    }
    
    // [Phase 3] 템플릿 마켓 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Template_Market' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) {
            JJ_Template_Market::instance();
        }
    }
    
    // [Phase 5] Partner Hub 초기화 (Partner/Master만)
    if ( class_exists( 'JJ_Partner_Hub' ) ) {
        JJ_Partner_Hub::instance();
    }

    // [v4.2.2] 실험실(Labs) 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Labs_Center' ) ) {
        try {
            JJ_Labs_Center::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }


    if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
        try {
            JJ_Code_Integrity_Monitor::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Code Integrity Monitor: 초기화 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.9] 라이센스 강제 실행 초기화 (plugins_loaded 시점)
    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $enforcement_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-enforcement.php';
        if ( file_exists( $enforcement_file ) ) {
            require_once $enforcement_file;
            if ( class_exists( 'JJ_License_Enforcement' ) ) {
                try {
                    JJ_License_Enforcement::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ License Enforcement: 초기화 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // [v5.3.7] 안전 모드 플래그 확인 및 활성화
    if ( function_exists( 'get_option' ) && class_exists( 'JJ_Safe_Mode' ) ) {
        $safe_mode_enabled = get_option( 'jj_style_guide_safe_mode', false );
        if ( $safe_mode_enabled ) {
            $safe_mode_reason = get_option( 'jj_style_guide_safe_mode_reason', '활성화 중 오류 발생' );
            try {
                $safe_mode = JJ_Safe_Mode::instance();
                if ( method_exists( $safe_mode, 'enable' ) ) {
                    $safe_mode->enable( $safe_mode_reason );
                }
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 오류 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
    
    // 메인 클래스 인스턴스 생성
    if ( class_exists( 'JJ_Simple_Style_Guide_Master' ) ) {
        try {
            JJ_Simple_Style_Guide_Master::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.6] 기존 플러그인 마이그레이션 매니저 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $migration_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php';
        if ( file_exists( $migration_file ) ) {
            // 마이그레이션 매니저는 이미 safe_loader를 통해 로드되었을 수 있음
            // 클래스가 존재하는 경우에만 초기화
            if ( class_exists( 'JJ_Migration_Manager' ) ) {
                try {
                    JJ_Migration_Manager::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 오류 - ' . $e->getMessage() );
                    }
                } catch ( Error $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // 플러그인 업데이트 체크 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $updater_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php';
        if ( file_exists( $updater_file ) ) {
            require_once $updater_file;
            if ( class_exists( 'JJ_Plugin_Updater' ) ) {
                try {
                    new JJ_Plugin_Updater(
                        __FILE__,
                        'advanced-custom-style-manage-center',
                        "ACF CSS Master",
                        defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.4.0',
                        'DEV' // 마스터 버전
                    );
                } catch ( Exception $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 오류 - ' . $e->getMessage() );
                } catch ( Error $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
}

// [v5.3.8] plugins_loaded 훅 활성화
if ( function_exists( 'add_action' ) ) {
    add_action( 'plugins_loaded', 'jj_simple_style_guide_master_run', 10 );
}

/**
 * [v8.0.0] 안전 장치: 필수 헬퍼 함수 Fallback 정의
 * class-jj-admin-center.php 로드 실패 시에도 사이트가 멈추지 않도록 보장합니다.
 */
if ( ! function_exists( 'jj_style_guide_text' ) ) {
    function jj_style_guide_text( $key, $fallback = '' ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_text( $key, $fallback );
        }
        return $fallback;
    }
}

if ( ! function_exists( 'jj_style_guide_sections_layout' ) ) {
    function jj_style_guide_sections_layout() {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_sections_layout();
        }
        return array();
    }
}

if ( ! function_exists( 'jj_style_guide_section_index' ) ) {
    function jj_style_guide_section_index( $slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_section_index( $slug );
        }
        return null;
    }
}

if ( ! function_exists( 'jj_style_guide_is_tab_enabled' ) ) {
    function jj_style_guide_is_tab_enabled( $section_slug, $tab_slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->is_tab_enabled( $section_slug, $tab_slug );
        }
        return true;
    }
}

```

```php
<?php
/**
 * Plugin Name:       ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager (Master)
 * Plugin URI:        https://j-j-labs.com
 * Description:       WordPress 웹사이트의 모든 스타일 요소(색상 팔레트, 타이포그래피, 버튼, 폼)를 중앙에서 일관되게 관리하는 통합 스타일 관리 플러그인입니다. Free 버전은 기본적인 스타일 관리 기능을 제공하며, 브랜드 일관성을 유지하고 디자인 시스템을 효율적으로 운영할 수 있습니다. Pro 버전 플러그인을 함께 설치하면 Basic, Premium, Unlimited 기능을 사용할 수 있습니다. WordPress Customizer와 완벽 통합되어 실시간 미리보기와 함께 직관적인 스타일 관리가 가능합니다.
 * Version:           8.3.2
 * Author:            3J Labs
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://poetryflow.blog
 * Text Domain:       acf-css-really-simple-style-management-center
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ⚠️ 중요: Git 경로와 로컬 경로는 완전히 별개입니다
 * 
 * - 로컬 작업 경로: C:\Users\computer\OneDrive\Desktop\클라우드 백업\OneDrive\문서\jj-css-premium
 * - Git worktree 경로: C:\Users\computer\.cursor\worktrees\jj-css-premium\tci
 * - 두 경로는 서로 독립적이며, Git에서 작업한 내용은 반드시 로컬 경로에 동기화해야 함
 * - 자세한 내용은 DEVELOPMENT_PRINCIPLES.md의 "Git 및 로컬 경로 관리 원칙" 섹션 참조
 */

// 플러그인 상수 정의
// [v5.1.6] 전면 재검토 및 오류 방지: 안전한 파일 로더 추가, 모든 버전의 require_once 안전 처리, 결제 유도 문구 추가, 플러그인 목록 페이지 바로가기 링크 추가
// [v5.1.6] Comprehensive review and error prevention: Safe file loader added, all versions' require_once safely handled, purchase prompts added, plugin list page quick links added
// [v1.0.2] 모든 버전 플러그인 활성화 안전성 최종 확보, WordPress 함수 호출 안전 처리
if ( ! defined( 'JJ_STYLE_GUIDE_VERSION' ) ) {
    define( 'JJ_STYLE_GUIDE_VERSION', '8.3.2' ); // [v8.3.2] Phase 8.2-8.4: 보안 강화, UX 개선, Undo 시스템 확장
}

// WordPress 함수가 로드되었는지 확인 후 상수 정의
if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    if ( function_exists( 'plugin_dir_path' ) ) {
        define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 직접 경로 계산
        define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
    }
}

if ( ! defined( 'JJ_STYLE_GUIDE_URL' ) ) {
    if ( function_exists( 'plugin_dir_url' ) ) {
        define( 'JJ_STYLE_GUIDE_URL', plugin_dir_url( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 기본값 사용 (나중에 업데이트됨)
        define( 'JJ_STYLE_GUIDE_URL', '' );
    }
}
if ( ! defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) define( 'JJ_STYLE_GUIDE_OPTIONS_KEY', 'jj_style_guide_options' );
if ( ! defined( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY' ) ) define( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY', 'jj_style_guide_temp_options' );
if ( ! defined( 'JJ_STYLE_GUIDE_ADMIN_TEXT_KEY' ) ) define( 'JJ_STYLE_GUIDE_ADMIN_TEXT_KEY', 'jj_style_guide_admin_texts' );
if ( ! defined( 'JJ_STYLE_GUIDE_PAGE_SLUG' ) ) define( 'JJ_STYLE_GUIDE_PAGE_SLUG', 'acf-css-really-simple-style-guide' );
if ( ! defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) ) define( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY', 'jj_style_guide_cockpit_page_id' );
if ( ! defined( 'JJ_STYLE_GUIDE_DEBUG' ) ) define( 'JJ_STYLE_GUIDE_DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
// Pro 버전 활성화 코드 확인 (plugins_loaded 훅에서 처리)
// WordPress가 로드되기 전에는 기본값 사용
// 기본값(빌드 스크립트에서 주입/대체될 수 있음). 이미 정의돼 있으면 건드리지 않습니다.
if ( ! defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', 'MASTER' );
if ( ! defined( 'JJ_STYLE_GUIDE_EDITION' ) ) define( 'JJ_STYLE_GUIDE_EDITION', 'master' );

// [v3.8.0] 환경 구분
if ( ! defined( 'JJ_STYLE_GUIDE_ENV' ) ) {
    define( 'JJ_STYLE_GUIDE_ENV', 'live' );
}

// [v5.3.7] Free 버전은 독립적으로 작동합니다. Master 버전 의존성 제거
// Free 버전은 자체 includes 폴더에 필요한 모든 파일을 포함합니다.

// [v5.4.1] 안전한 파일 로더 적용
$safe_loader = null;
try {
    $safe_loader_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-loader.php';
    if ( file_exists( $safe_loader_path ) ) {
        require_once $safe_loader_path;
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'instance' ) ) {
            $safe_loader = JJ_Safe_Loader::instance();
        }
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Failed: ' . $e->getMessage() );
} catch ( Error $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Fatal Error: ' . $e->getMessage() );
}

/**
 * [B: 안정성 방어막] Safe Loader 폴백
 * - class-jj-safe-loader.php 자체가 누락/손상돼도, 플러그인이 "부팅"은 되도록 합니다.
 * - (원인 파악/자가진단/업데이트 탭 접근) 경로를 확보하는 것이 목표입니다.
 */
$GLOBALS['jj_style_guide_boot_diag'] = isset( $GLOBALS['jj_style_guide_boot_diag'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag'] )
    ? $GLOBALS['jj_style_guide_boot_diag']
    : array(
        'missing_required' => array(),
        'missing_optional' => array(),
        'load_errors'      => array(),
    );

$jj_safe_require = function( $path, $required = true ) {
    $path = (string) $path;
    $required = (bool) $required;

    // 우선: Safe Loader가 있으면 그 진단/기록 기능을 그대로 사용
    if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'safe_require' ) ) {
        return JJ_Safe_Loader::safe_require( $path, $required );
    }

    // 폴백: 최소한의 safe require
    if ( ! file_exists( $path ) ) {
        $key = md5( $path . '|' . ( $required ? '1' : '0' ) );
        $bucket = $required ? 'missing_required' : 'missing_optional';
        if ( ! isset( $GLOBALS['jj_style_guide_boot_diag'][ $bucket ][ $key ] ) ) {
            $GLOBALS['jj_style_guide_boot_diag'][ $bucket ][ $key ] = array(
                'path'     => $path,
                'required' => $required,
                'count'    => 1,
                'reason'   => 'file_not_found',
            );
        } else {
            $GLOBALS['jj_style_guide_boot_diag'][ $bucket ][ $key ]['count']++;
        }

        if ( $required && function_exists( 'error_log' ) ) {
            error_log( 'JJ Fallback Loader: File not found - ' . $path );
        }
        return false;
    }
    try {
        require_once $path;
        return true;
    } catch ( Exception $e ) {
        $GLOBALS['jj_style_guide_boot_diag']['load_errors'][] = array(
            'path'     => $path,
            'required' => $required,
            'error'    => $e->getMessage(),
        );
        if ( function_exists( 'error_log' ) ) {
            error_log( 'JJ Fallback Loader: Exception loading ' . $path . ' - ' . $e->getMessage() );
        }
        return false;
    } catch ( Error $e ) {
        $GLOBALS['jj_style_guide_boot_diag']['load_errors'][] = array(
            'path'     => $path,
            'required' => $required,
            'error'    => $e->getMessage(),
        );
        if ( function_exists( 'error_log' ) ) {
            error_log( 'JJ Fallback Loader: Error loading ' . $path . ' - ' . $e->getMessage() );
        }
        return false;
    }
};

// 라이센스 매니저/에디션/무결성 관련: 가능한 한 일찍 로드
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php', false );
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-version-features.php', true );
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-code-integrity-monitor.php', true );

// 핵심 로직 파일 로드 (Master 전용 파일은 아직 비활성화)
try {
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-handler.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-logger.php', false );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-admin-center.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-backup-manager.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/visual-command-center/class-jj-visual-command-center.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/ai-style-intelligence/class-jj-smart-palette.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-cloud-manager.php', true );
    // [Phase 3] 템플릿 마켓
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-template-market.php', true );
    // [Phase 5] Partner Hub
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/partner-hub/class-jj-partner-hub.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-labs-center.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-selector-registry.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-cache.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-injector.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-options-cache.php', true );
    // [Phase 8.1] Transient Cache (자주 읽히는 옵션 캐싱)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-transient-cache.php', true );
    // [Phase 8.1] Asset Optimizer (성능 최적화)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-asset-optimizer.php', true );
    // [Phase 8.2] Security Hardener (보안 강화)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-security-hardener.php', true );
    // [Phase 8.5.1] AI Extension Promoter (AI Extension 감지 및 활성화 유도)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-ai-extension-promoter.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-edition-controller.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-theme-metadata.php', false );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-0-customizer.php', false );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-1-css-vars.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-2-php-filters.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-3-dequeue.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-sync.php', false );
    // [Phase 5.2] REST API (설정 조회/업데이트)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/api/class-jj-style-guide-rest-api.php', true );
    // [Phase 5.3] 확장 플러그인 시스템 (Extension API)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/interface-jj-style-guide-extension.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/class-jj-extension-manager.php', true );
    // [Phase 5.2] Webhooks (자동화)
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/integrations/class-jj-webhook-manager.php', true );
    // [Phase 5 B] Multisite Network Control
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/multisite/class-jj-multisite-controller.php', true );
    
    // Master 전용 파일들
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-sync-manager.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php', false );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-memory-manager.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-mode.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-optimizer.php', true );
    $jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-issuer.php', false );
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Master Load Error: ' . $e->getMessage() );
}



/**
 * 활성화 훅 래퍼 함수 (치명적 오류 방지)
 * [v5.1.7] 전체 함수를 try-catch로 감싸고 상수 정의 확인 추가
 * [Phase 8.0] 최소 부팅 경로 적용: Activation 시 최소한의 파일만 로드
 */
function jj_simple_style_guide_master_activate() {
    try {
        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // 상수가 정의되지 않은 경우 기본 경로 사용
            if ( function_exists( 'plugin_dir_path' ) ) {
                define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
            } else {
                define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
            }
        }
        
        // [Phase 8.0] 최소 부팅 경로 확인
        // Safe Loader가 이미 로드되어 있어야 함 (최상단에서 로드됨)
        $minimal_boot = array( 'can_boot' => true, 'missing' => array() );
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'get_minimal_boot_path' ) ) {
            $minimal_boot = JJ_Safe_Loader::get_minimal_boot_path();
        }
        
        // 최소 부팅 실패 시 안전하게 종료
        if ( ! $minimal_boot['can_boot'] ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS: 최소 부팅 실패 - ' . ( isset( $minimal_boot['reason'] ) ? $minimal_boot['reason'] : 'Unknown' ) );
            }
            // 플러그인 옵션에 오류 기록 (Admin Center 접근 가능하도록)
            update_option( 'jj_style_guide_activation_error', array(
                'timestamp' => current_time( 'mysql' ),
                'reason' => 'minimal_boot_failed',
                'missing' => isset( $minimal_boot['missing'] ) ? $minimal_boot['missing'] : array(),
            ) );
            return;
        }
        
        // 활성화 매니저 클래스가 로드되지 않은 경우 로드 시도
        if ( ! class_exists( 'JJ_Activation_Manager' ) ) {
            $activation_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php';
            if ( file_exists( $activation_file ) ) {
                // Safe Loader 사용
                if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'safe_require' ) ) {
                    JJ_Safe_Loader::safe_require( $activation_file, true );
                } else {
                    require_once $activation_file;
                }
            } else {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 파일을 찾을 수 없습니다. 기본 활성화만 수행합니다.' );
                }
                // 최소한의 활성화 정보만 저장
                update_option( 'jj_style_guide_activated', current_time( 'mysql' ) );
                return;
            }
        }
        
        // 클래스가 존재하는지 확인 후 활성화 실행
        if ( class_exists( 'JJ_Activation_Manager' ) && method_exists( 'JJ_Activation_Manager', 'activate' ) ) {
            try {
                JJ_Activation_Manager::activate();
                // 활성화 성공 시 오류 정보 삭제
                delete_option( 'jj_style_guide_activation_error' );
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 오류 발생 - ' . $e->getMessage() );
                }
                // 오류 정보 저장
                update_option( 'jj_style_guide_activation_error', array(
                    'timestamp' => current_time( 'mysql' ),
                    'reason' => 'activation_exception',
                    'message' => $e->getMessage(),
                ) );
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 치명적 오류 발생 - ' . $e->getMessage() );
                }
                // 오류 정보 저장
                update_option( 'jj_style_guide_activation_error', array(
                    'timestamp' => current_time( 'mysql' ),
                    'reason' => 'activation_fatal_error',
                    'message' => $e->getMessage(),
                ) );
            }
        } else {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 클래스를 사용할 수 없습니다.' );
            }
        }
    } catch ( Exception $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 예외 발생 - ' . $e->getMessage() );
        }
    } catch ( Error $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 치명적 오류 발생 - ' . $e->getMessage() );
        }
    }
}
register_activation_hook( __FILE__, 'jj_simple_style_guide_master_activate' );

/**
 * Free 버전 전용 메인 컨트롤 클래스
 */
final class JJ_Simple_Style_Guide_Master {

    private static $instance = null;
    private $options = array();
    private $cockpit_page_id = 0;
    private $options_key = '';
    private $brand_colors = array();
    private $environment_map = array();
    private $adapters_config = array();
    private $license_manager = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 상수 정의 확인 후 할당
        if ( defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) {
            $this->options_key = JJ_STYLE_GUIDE_OPTIONS_KEY;
        }
        
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $this->license_manager = JJ_License_Manager::instance();
        }
        
        if ( defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) && function_exists( 'get_option' ) ) {
            $this->cockpit_page_id = (int) get_option( JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY );
        }

        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // [v5.3.7] Free 버전은 자체 config 폴더에서 로드
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
            if ( file_exists( $config_file ) ) {
                $this->adapters_config = include( $config_file );
                // [v5.5.0] Edition Controller를 통한 동적 어댑터 필터링
                if ( class_exists( 'JJ_Edition_Controller' ) ) {
                    $this->adapters_config = JJ_Edition_Controller::instance()->filter_adapters( $this->adapters_config );
                }
                
                
            }
        }

        // WordPress 함수가 로드된 후에만 훅 등록
        // [v5.1.9] plugin_basename() 안전 호출 추가
        // [v5.3.9] 모든 훅 등록을 조건문 안으로 이동하여 안전성 확보
        if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) && function_exists( 'plugin_basename' ) ) {
            add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
            add_action( 'admin_menu', array( $this, 'add_admin_menu_links' ) );
            $plugin_basename = function_exists( 'plugin_basename' ) ? plugin_basename( __FILE__ ) : basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );
            add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_plugin_settings_link' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            // [v8.x] 부팅 진단: Safe Loader에서 누락/로드 오류 감지 시 관리자에게 안내
            add_action( 'admin_notices', array( $this, 'maybe_show_boot_diagnostics_notice' ) );
            add_action( 'wp_ajax_jj_save_style_guide', array( $this, 'save_settings_ajax' ) );
            add_action( 'wp_ajax_jj_apply_temp_palette', array( $this, 'apply_temp_palette_ajax' ) );
            add_action( 'wp_ajax_jj_save_temp_palette', array( $this, 'save_temp_palette_ajax' ) );
            // [v3.8.0] Customizer 색상 새로고침 (팔레트 동기화)
            if ( class_exists( 'JJ_Customizer_Sync' ) ) {
                add_action( 'wp_ajax_jj_load_current_colors', array( 'JJ_Customizer_Sync', 'ajax_load_current_colors' ) );
            }
            // [v3.8.0] 섹션별 내보내기/불러오기 (팔레트 등)
            add_action( 'wp_ajax_jj_export_section', array( $this, 'ajax_export_section' ) );
            add_action( 'wp_ajax_jj_import_section', array( $this, 'ajax_import_section' ) );
            // [AI Presets] AI Extension에서 저장한 추천 팔레트 관리(핀/삭제 등)
            add_action( 'wp_ajax_jj_ai_palette_preset_manage', array( $this, 'ajax_ai_palette_preset_manage' ) );
            add_action( 'init', array( $this, 'load_options_and_init_strategies' ), 5 );
            add_action( 'init', array( $this, 'init_sync_manager' ), 10 );
            
            
            if ( JJ_STYLE_GUIDE_DEBUG ) {
                add_action( 'wp_footer', array( $this, 'render_debug_info' ), 999 );
                add_action( 'admin_footer', array( $this, 'render_debug_info' ), 999 );
            }
        }

        // Customizer 통합 (제한적)
        
        try {
            $customizer_manager_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php';
            if ( file_exists( $customizer_manager_path ) ) {
                require_once $customizer_manager_path;
                if ( class_exists( 'JJ_Customizer_Manager' ) ) {
                    $customizer_manager = JJ_Customizer_Manager::instance();
                    if ( method_exists( $customizer_manager, 'init' ) ) {
                        $customizer_manager->init();
                    }
                }
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }

    }

    /**
     * Free 버전: 어댑터 제한
     * 
     * @param array $config
     * @return array
     */
    private function limit_adapters_for_free( $config ) {
        // Free 버전: 테마 2개만 지원 (Twenty Twenty-Four, Twenty Twenty-Five)
        $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
        if ( isset( $config['themes'] ) && is_array( $config['themes'] ) ) {
            $config['themes'] = array_intersect_key( $config['themes'], array_flip( $allowed_themes ) );
        }

        // Free 버전: 플러그인 1개만 지원 (WooCommerce)
        $allowed_plugins = array( 'woocommerce' );
        if ( isset( $config['spokes'] ) && is_array( $config['spokes'] ) ) {
            $config['spokes'] = array_intersect_key( $config['spokes'], array_flip( $allowed_plugins ) );
        }

        return $config;
    }

    /**
     * Free 버전 워터마크 추가
     */
    public function add_free_watermark() {
        if ( ! is_admin() && current_user_can( 'manage_options' ) ) {
            echo '<div style="position: fixed; bottom: 10px; right: 10px; background: rgba(0, 0, 0, 0.7); color: #fff; padding: 8px 12px; font-size: 11px; border-radius: 4px; z-index: 99999; font-family: Arial, sans-serif;">';
            echo '<a href="' . esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ) . '" style="color: #fff; text-decoration: none;">JJ Style Guide Free</a>';
            echo '</div>';
        }
    }

    /**
     * 업그레이드 프롬프트 표시
     */
    public function show_upgrade_prompt() {
        // [Safety Lock] MASTER 또는 PARTNER 버전은 업그레이드 프롬프트 표시 안 함
        if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'partner' ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        $license_manager = null;
        $upgrade_url = 'https://j-j-labs.com'; // 기본값
        
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
                if ( $license_manager ) {
                    $upgrade_url = $license_manager->get_purchase_url( 'upgrade' );
                }
            }
        }
        ?>
        <div class="notice notice-info is-dismissible" style="background: #f0f6fc; border-left-color: #2271b1;">
            <p style="font-size: 14px; line-height: 1.8;">
                <strong><?php _e( '현재 버전을 사용 중입니다.', 'acf-css-really-simple-style-management-center' ); ?></strong><br>
                <?php _e( '더 많은 기능을 이용하려면 업그레이드하세요:', 'acf-css-really-simple-style-management-center' ); ?>
                <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" class="button button-primary" style="margin-left: 10px;">
                    <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
            </p>
            <ul style="margin: 10px 0; padding-left: 20px; list-style: disc;">
                <li><?php _e( '모든 팔레트 사용 가능 (브랜드, 시스템, 얼터너티브, 어나더, 임시)', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '모든 테마 및 플러그인 어댑터 지원', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '관리자 센터 및 실험실 센터 전체 기능', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '섹션별 내보내기/불러오기', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '고급 색상 선택 기능 (스포이드, 팔레트 불러오기)', 'acf-css-really-simple-style-management-center' ); ?></li>
            </ul>
        </div>
        <?php
    }

    public function init_sync_manager() {
        add_action( 'customize_save_after', array( $this, 'sync_from_customizer' ) );
        JJ_Sync_Manager::instance()->init( $this->options, $this->cockpit_page_id );
    }

    /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * Free 버전: 스타일 센터만 표시
     * [v5.1.9] 안전성 강화 - try-catch 추가
     */
            /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * [Phase 4.99] 퀵 링크 확장: 설정 | 스타일 | 메뉴 | 비주얼
     */
    public function add_plugin_settings_link( $links ) {
        try {
            if ( ! function_exists( 'admin_url' ) || ! function_exists( 'esc_url' ) ) {
                return $links;
            }
            
            $new_links = array();
            
            // 1. 설정 (Admin Center 메인)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ) . '" style="font-weight: 600;">' . __( 'Settings', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 2. 스타일 (Style Guide)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ) ) . '">' . __( 'Styles', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 3. 어드민 메뉴 (Admin Menu Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#admin-menu' ) ) . '">' . __( 'Menu', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 4. 비주얼 (Visual Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#visual' ) ) . '">' . __( 'Visual', 'acf-css-really-simple-style-management-center' ) . '</a>';

            // 기존 링크와 병합
            return array_merge( $new_links, $links );
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 오류 - ' . $e->getMessage() );
            }
            return $links;
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 치명적 오류 - ' . $e->getMessage() );
            }
            return $links;
        }
    }

    public function add_admin_menu_page() {
        add_options_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );
    }

    public function add_admin_menu_links() {
        // [Phase 4.5/4.99] "모양/도구/설정" 어디서나 동일한 화면으로 진입할 수 있도록 서브메뉴 추가
        add_theme_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        add_management_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        if ( $this->cockpit_page_id ) {
            add_submenu_page(
                'themes.php',
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                'manage_options',
                'post.php?post=' . $this->cockpit_page_id . '&action=edit',
                '',
                100
            );
        }
    }

    public function enqueue_admin_scripts( $hook_suffix ) {
        $is_settings_page = in_array(
            $hook_suffix,
            array(
                'settings_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'appearance_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'tools_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
            ),
            true
        );

        $is_cockpit_page = false;
        if ( 'post.php' === $hook_suffix ) {
            global $post;
            if ( $post && (int) $post->ID === $this->cockpit_page_id ) {
                $is_cockpit_page = true;
            }
        }

        if ( ! $is_settings_page && ! $is_cockpit_page ) {
            return;
        }

        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;

        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        // [v5.3.7] Free 버전은 자체 assets 폴더에서 로드
        wp_enqueue_style( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-editor.css', array( 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION );
        wp_enqueue_script( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-editor.js', array( 'jquery', 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION, true );
        // [UI/UX] 팔레트 프리셋 + 인라인 프리뷰
        $presets_css_ver = JJ_STYLE_GUIDE_VERSION;
        $presets_js_ver  = JJ_STYLE_GUIDE_VERSION;
        $presets_css_path = JJ_STYLE_GUIDE_PATH . 'assets/css/jj-style-guide-presets.css';
        $presets_js_path  = JJ_STYLE_GUIDE_PATH . 'assets/js/jj-style-guide-presets.js';
        if ( file_exists( $presets_css_path ) ) {
            $presets_css_ver .= '.' . (string) filemtime( $presets_css_path );
        }
        if ( file_exists( $presets_js_path ) ) {
            $presets_js_ver .= '.' . (string) filemtime( $presets_js_path );
        }
        wp_enqueue_style( 'jj-style-guide-presets', JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-presets.css', array( 'jj-style-guide-editor' ), $presets_css_ver );
        wp_enqueue_script( 'jj-style-guide-presets', JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-presets.js', array( 'jj-style-guide-editor', 'jquery' ), $presets_js_ver, true );

        $preview_page_id = $this->cockpit_page_id;
        $preview_url = $preview_page_id ? get_permalink( $preview_page_id ) : '';

        wp_localize_script(
            'jj-style-guide-editor',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'temp_palette_nonce' => wp_create_nonce( 'jj_temp_palette_nonce' ),
                'settings' => $this->options,
                // AI Extension이 저장한 "추천 팔레트 카드" 목록 (있으면 프리셋 UI에 합류)
                'ai_palette_presets' => (function() {
                    $items = get_option( 'jj_style_guide_ai_palette_presets', array() );
                    return is_array( $items ) ? $items : array();
                })(),
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'edition'  => 'free', // Free 버전 표시
                'i18n'     => array(
                    'saving' => __( '저장 중...', 'acf-css-really-simple-style-management-center' ),
                    'saved'  => __( '스타일 저장됨', 'acf-css-really-simple-style-management-center' ),
                    'applied' => __( '임시 팔레트 적용됨! \'스타일 저장\'을 눌러야 최종 반영됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'error'  => __( '오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
                    'confirm_apply' => __( '정말로 \'임시 팔레트\'의 값으로 덮어쓰시겠습니까?\n이 작업은 즉시 UI에 반영되지만, \n\'스타일 저장\' 버튼을 눌러야 최종 저장됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'preview_url' => $preview_url ? esc_url( $preview_url ) : '',
                    'preview_page_id' => $preview_page_id,
                ),
            )
        );
    }

    public function render_style_guide_page() {
        try {
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options      = $options;
            $temp_options       = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY );
            $options            = $this->options;
            $cockpit_page_id    = $this->cockpit_page_id;
            $preview_url        = $cockpit_page_id ? get_permalink( $cockpit_page_id ) : '';

        // 옵션이 비어있으면 기본값 설정
        if ( empty( $this->options ) ) {
            $this->options = array(
                'palettes' => array(
                    'brand' => array(
                        'primary_color' => '',
                        'primary_color_hover' => '',
                        'secondary_color' => '',
                        'secondary_color_hover' => '',
                    ),
                    'system' => array(), // Free 버전: 시스템 팔레트 비활성화
                    'alternative' => array(), // Free 버전: 얼터너티브 팔레트 비활성화
                    'another' => array(), // Free 버전: 어나더 팔레트 비활성화
                ),
                'typography' => array(),
                'buttons' => array(),
                'forms' => array(),
                'contexts' => array(),
            );
            $options = $this->options;
        }
        ?>
        <div id="jj-style-guide-wrapper" class="wrap jj-style-guide-wrap" style="background: #f0f0f1; margin: 0; padding: 0;">
            <form id="jj-style-guide-form">

                <div class="jj-style-guide-header" style="top: 32px; background: #fff; padding: 15px 25px; border-bottom: 1px solid #c3c4c7; z-index: 999; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h1 style="margin: 0; font-size: 24px;">
                            <?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?> (v<?php echo esc_html( JJ_STYLE_GUIDE_VERSION ); ?>) 
                            <?php if ( ! class_exists("JJ_Edition_Controller") || JJ_Edition_Controller::instance()->get_current_edition() === "free" ) : ?><span style="background: #d63638; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 14px; font-weight: 600;">Free</span><?php endif; ?>
                        </h1>
                        <?php
                        $license_manager = null;
                        $purchase_url = 'https://j-j-labs.com'; // 기본값
                        
                        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                            if ( class_exists( 'JJ_License_Manager' ) ) {
                                $license_manager = JJ_License_Manager::instance();
                                if ( $license_manager ) {
                                    $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                                }
                            }
                        }
                        ?>
                        <?php if ( ! class_exists("JJ_Edition_Controller") || ! JJ_Edition_Controller::instance()->is_at_least("partner") ) : ?><a href="<?php echo esc_url( $purchase_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary" style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px;">
                            <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                        </a><?php endif; ?>
                        <?php if ( $preview_url ) : ?>
                        <button type="button" 
                                class="button button-secondary" 
                                id="jj-preview-button"
                                style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> <?php _e( '실시간 미리보기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="jj-header-actions" style="display: flex; align-items: center; gap: 10px;">
                        <span id="jj-save-success" style="display: none; vertical-align: middle; color: #2271b1; font-weight: 600; margin-right: 15px;"></span>
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-save-style-guide" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
                <div class="jj-style-guide-content" style="margin: 20px auto; background: #ffffff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 1px;">
                    <?php
                    // Free 버전: 제한된 섹션만 표시
                    // [v5.3.7] Free 버전은 자체 includes/editor-views 폴더에서 로드
                    $sections_map = array(
                        'colors'     => 'includes/editor-views/view-section-colors.php',
                        'typography' => 'includes/editor-views/view-section-typography.php',
                        'buttons'    => 'includes/editor-views/view-section-buttons.php',
                        'forms'      => 'includes/editor-views/view-section-forms.php',
                    );

                    // Free 버전: 섹션 순서 고정
                    $sections_to_render = array();
                    foreach ( $sections_map as $slug => $relative_path ) {
                        $sections_to_render[] = array(
                            'slug' => $slug,
                            'file' => JJ_STYLE_GUIDE_PATH . $relative_path,
                            'order'=> 0, // 순서는 UI에서만 표시
                        );
                    }
                    ?>
                    <div id="jj-sections-sortable" class="jj-sections-sortable">
                        <?php
                        foreach ( $sections_to_render as $section ) {
                            // Free 버전: 기본 뷰 파일 사용 (제한 로직은 뷰 파일 내부에서 처리)
                            $default_file = JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-section-' . $section['slug'] . '.php';
                            if ( file_exists( $section['file'] ) ) {
                                echo '<div class="jj-section-wrapper" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $section['file'];
                                echo '</div>';
                            } elseif ( file_exists( $default_file ) ) {
                                // Free 버전 전용 뷰가 없으면 기본 뷰 사용 (제한 로직 적용 필요)
                                echo '<div class="jj-section-wrapper jj-section-free-version" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $default_file;
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="jj-style-guide-footer" style="margin: 20px auto; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: #666; font-size: 13px;">
                        <?php
                        // Master/Partner에서는 업그레이드 유도 문구를 절대 노출하지 않음
                        $show_upgrade_footer = true;
                        if ( class_exists( 'JJ_Edition_Controller' ) ) {
                            try {
                                $show_upgrade_footer = ! JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                            } catch ( Exception $e ) {
                                // ignore
                            } catch ( Error $e ) {
                                // ignore
                            }
                        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
                            $show_upgrade_footer = ! in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
                        }

                        if ( $show_upgrade_footer ) {
                            _e( '더 많은 기능을 이용하려면 업그레이드하세요.', 'acf-css-really-simple-style-management-center' );
                        }
                        ?>
                    </div>
                    <div style="text-align: right;">
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-style-guide-footer-button" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        } catch ( Exception $e ) {
            // 오류 발생 시에도 기본 페이지 구조는 표시
            ?>
            <div class="wrap" style="padding: 20px;">
                <h1><?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?></h1>
                <div class="notice notice-error">
                    <p><strong><?php _e( '오류가 발생했습니다:', 'acf-css-really-simple-style-management-center' ); ?></strong> <?php echo esc_html( $e->getMessage() ); ?></p>
                </div>
            </div>
            <?php
        }
    }

    // 나머지 메서드들은 dev 버전과 동일하지만, 기능 제한 로직 추가 필요
    // save_settings_ajax, apply_temp_palette_ajax, save_temp_palette_ajax 등
    
    public function save_settings_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        if ( ! isset( $_POST['settings'] ) || ! is_array( $_POST['settings'] ) ) {
            wp_send_json_error( array( 'message' => __( '잘못된 데이터입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // [Phase 5 B] 멀티사이트 네트워크 전용 모드에서는 사이트별 저장 차단
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $ms = JJ_Multisite_Controller::instance();
                if ( method_exists( $ms, 'is_enabled' ) && method_exists( $ms, 'allow_site_override' ) ) {
                    if ( $ms->is_enabled() && ! $ms->allow_site_override() ) {
                        wp_send_json_error( array( 'message' => __( '이 사이트는 네트워크에서 스타일을 관리 중입니다. 네트워크 관리자에서 변경해주세요.', 'acf-css-really-simple-style-management-center' ) ) );
                    }
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        $old_settings = array();
        if ( function_exists( 'get_option' ) ) {
            $old_settings = (array) get_option( $this->options_key, array() );
        }

        $raw_settings = wp_unslash( $_POST['settings'] );
        
        // 에디션별 저장 제한 (Free만 제한, Partner/Master는 무제한)
        $current_edition = 'free';
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $current_edition = (string) JJ_Edition_Controller::instance()->get_current_edition();
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_EDITION' ) ) {
            $current_edition = (string) JJ_STYLE_GUIDE_EDITION;
        }

        if ( 'free' === $current_edition ) {
            $raw_settings = $this->filter_settings_for_free( $raw_settings );
        }
        
        $sanitized_settings = $this->sanitize_options_recursive( $raw_settings );
        update_option( $this->options_key, $sanitized_settings );

        // [Phase 5.2] Webhook/자동화용 이벤트
        if ( function_exists( 'do_action' ) ) {
            do_action( 'jj_style_guide_settings_updated', $sanitized_settings, $old_settings, 'style_center_ajax' );
        }

        $this->sync_to_spokes( $sanitized_settings );

        JJ_CSS_Cache::instance()->flush();
        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * Free 버전: 저장 가능한 설정 필터링
     */
    private function filter_settings_for_free( $settings ) {
        // Free 버전: 시스템 팔레트, 얼터너티브 팔레트, 어나더 팔레트 제거
        if ( isset( $settings['palettes'] ) ) {
            unset( $settings['palettes']['system'] );
            unset( $settings['palettes']['alternative'] );
            unset( $settings['palettes']['another'] );
        }
        
        // Free 버전: 타이포그래피 H5, H6 제거
        if ( isset( $settings['typography'] ) ) {
            unset( $settings['typography']['h5'] );
            unset( $settings['typography']['h6'] );
        }
        
        // Free 버전: Secondary, Text 버튼 제거
        if ( isset( $settings['buttons'] ) ) {
            unset( $settings['buttons']['secondary'] );
            unset( $settings['buttons']['text'] );
        }
        
        return $settings;
    }

    public function apply_temp_palette_ajax() {
        check_ajax_referer( 'jj_temp_palette_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 기능 허용 여부(버전 기능/에디션)
        $can_use_temp = false;
        if ( class_exists( 'JJ_Version_Features' ) ) {
            try {
                $vf = JJ_Version_Features::instance();
                $can_use_temp = $vf ? $vf->can_use_palette( 'temp' ) : false;
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $can_use_temp = JJ_Edition_Controller::instance()->is_at_least( 'premium' ) || JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $can_use_temp = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }

        if ( ! $can_use_temp ) {
            $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
            if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
                $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
            }
            wp_send_json_error( array( 'message' => $message ) );
        }

        $target_palette = isset( $_POST['target_palette'] ) ? sanitize_key( wp_unslash( $_POST['target_palette'] ) ) : 'brand';
        if ( 'brand' !== $target_palette ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 대상 팔레트입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
        $temp_palettes = isset( $temp_options['palettes'] ) && is_array( $temp_options['palettes'] ) ? $temp_options['palettes'] : array();
        $temp_brand = isset( $temp_palettes['brand'] ) && is_array( $temp_palettes['brand'] ) ? $temp_palettes['brand'] : array();

        if ( empty( $temp_brand ) ) {
            wp_send_json_error( array( 'message' => __( '임시 팔레트에 저장된 값이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        if ( ! isset( $options['palettes'] ) || ! is_array( $options['palettes'] ) ) {
            $options['palettes'] = array();
        }
        if ( ! isset( $options['palettes']['brand'] ) || ! is_array( $options['palettes']['brand'] ) ) {
            $options['palettes']['brand'] = array();
        }

        $options['palettes']['brand'] = array_merge( (array) $options['palettes']['brand'], (array) $temp_brand );
        update_option( $this->options_key, $options );

        if ( class_exists( 'JJ_CSS_Cache' ) && method_exists( 'JJ_CSS_Cache', 'instance' ) ) {
            try {
                JJ_CSS_Cache::instance()->flush();
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        wp_send_json_success( array(
            'message' => __( '임시 팔레트가 브랜드 팔레트에 적용되었습니다.', 'acf-css-really-simple-style-management-center' ),
            'new_settings' => $options,
        ) );
    }

    public function save_temp_palette_ajax() {
        check_ajax_referer( 'jj_temp_palette_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 기능 허용 여부(버전 기능/에디션)
        $can_use_temp = false;
        if ( class_exists( 'JJ_Version_Features' ) ) {
            try {
                $vf = JJ_Version_Features::instance();
                $can_use_temp = $vf ? $vf->can_use_palette( 'temp' ) : false;
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $can_use_temp = JJ_Edition_Controller::instance()->is_at_least( 'premium' ) || JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $can_use_temp = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }

        if ( ! $can_use_temp ) {
            $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
            if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
                $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
            }
            wp_send_json_error( array( 'message' => $message ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        $brand = isset( $options['palettes']['brand'] ) && is_array( $options['palettes']['brand'] ) ? $options['palettes']['brand'] : array();

        if ( empty( $brand ) ) {
            wp_send_json_error( array( 'message' => __( '브랜드 팔레트 값이 비어 있습니다. 먼저 브랜드 색상을 입력해 주세요.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
        if ( ! isset( $temp_options['palettes'] ) || ! is_array( $temp_options['palettes'] ) ) {
            $temp_options['palettes'] = array();
        }
        $temp_options['palettes']['brand'] = $brand;

        update_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, $temp_options );

        wp_send_json_success( array(
            'message' => __( '현재 브랜드 팔레트가 임시 팔레트로 저장되었습니다.', 'acf-css-really-simple-style-management-center' ),
            'temp_palettes' => $temp_options['palettes'],
        ) );
    }

    /**
     * [v3.8.0] 섹션별 내보내기
     * - 현재는 팔레트(brand/system/alternative/another/temp-palette)만 지원
     * - 관리자에서 파일 다운로드(attachment)로 전달
     */
    public function ajax_export_section() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $section_type = isset( $_POST['section_type'] ) ? sanitize_key( wp_unslash( $_POST['section_type'] ) ) : '';
        $section_subtype = isset( $_POST['section_subtype'] ) ? sanitize_key( wp_unslash( $_POST['section_subtype'] ) ) : '';

        if ( 'palette' !== $section_type ) {
            wp_die( esc_html__( '지원하지 않는 섹션 타입입니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $allowed_subtypes = array( 'brand', 'system', 'alternative', 'another', 'temp' );
        if ( ! in_array( $section_subtype, $allowed_subtypes, true ) ) {
            wp_die( esc_html__( '지원하지 않는 섹션 서브타입입니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        $data = array();

        if ( 'temp' === $section_subtype ) {
            $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
            $data = isset( $temp_options['palettes'] ) ? (array) $temp_options['palettes'] : array();
        } else {
            $data = isset( $options['palettes'][ $section_subtype ] ) ? (array) $options['palettes'][ $section_subtype ] : array();
        }

        $payload = array(
            'exported_at'   => function_exists( 'current_time' ) ? current_time( 'mysql' ) : gmdate( 'c' ),
            'plugin'        => 'acf-css-really-simple-style-management-center',
            'plugin_version'=> defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '',
            'section_type'  => $section_type,
            'section_subtype' => $section_subtype,
            'data'          => $data,
        );

        if ( function_exists( 'nocache_headers' ) ) {
            nocache_headers();
        }

        $filename = sprintf(
            'acf-css-%s-%s-%s.json',
            $section_type,
            $section_subtype,
            gmdate( 'Ymd-His' )
        );

        // 다운로드 헤더
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

        echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        exit;
    }

    /**
     * [v3.8.0] 섹션별 불러오기
     * - JSON 업로드 후 옵션에 병합(키 기준 덮어쓰기)
     */
    public function ajax_import_section() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $section_type = isset( $_POST['section_type'] ) ? sanitize_key( wp_unslash( $_POST['section_type'] ) ) : '';
        $section_subtype = isset( $_POST['section_subtype'] ) ? sanitize_key( wp_unslash( $_POST['section_subtype'] ) ) : '';

        if ( 'palette' !== $section_type ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 섹션 타입입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $allowed_subtypes = array( 'brand', 'system', 'alternative', 'another', 'temp' );
        if ( ! in_array( $section_subtype, $allowed_subtypes, true ) ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 섹션 서브타입입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        if ( empty( $_FILES['import_file'] ) || empty( $_FILES['import_file']['tmp_name'] ) ) {
            wp_send_json_error( array( 'message' => __( '업로드된 파일이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $tmp_name = $_FILES['import_file']['tmp_name'];
        $raw = file_get_contents( $tmp_name );
        if ( ! $raw ) {
            wp_send_json_error( array( 'message' => __( '파일을 읽을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $decoded = json_decode( $raw, true );
        if ( ! is_array( $decoded ) ) {
            wp_send_json_error( array( 'message' => __( 'JSON 형식이 올바르지 않습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // payload 형식({data:{...}})과 순수 데이터({..}) 둘 다 허용
        $import_data = isset( $decoded['data'] ) && is_array( $decoded['data'] ) ? (array) $decoded['data'] : $decoded;

        // 간단 정리(색상 키는 hex로)
        $sanitized = array();
        foreach ( (array) $import_data as $k => $v ) {
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( (string) $k ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $k ) );
            if ( is_array( $v ) ) {
                // 팔레트는 1depth만 정리 (필요 시 확장)
                $sanitized[ $key ] = $v;
                continue;
            }
            if ( strpos( $key, 'color' ) !== false ) {
                $hex = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( (string) $v ) : '';
                $sanitized[ $key ] = $hex ? $hex : '';
                continue;
            }
            $sanitized[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( (string) $v ) : strip_tags( (string) $v );
        }

        if ( 'temp' === $section_subtype ) {
            $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
            if ( ! isset( $temp_options['palettes'] ) || ! is_array( $temp_options['palettes'] ) ) {
                $temp_options['palettes'] = array();
            }
            $temp_options['palettes'] = array_merge( (array) $temp_options['palettes'], $sanitized );
            update_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, $temp_options );
        } else {
            $options = (array) get_option( $this->options_key, array() );
            if ( ! isset( $options['palettes'] ) || ! is_array( $options['palettes'] ) ) {
                $options['palettes'] = array();
            }
            $options['palettes'][ $section_subtype ] = array_merge(
                (array) ( $options['palettes'][ $section_subtype ] ?? array() ),
                $sanitized
            );
            update_option( $this->options_key, $options );
        }

        wp_send_json_success( array(
            'message' => __( '설정이 성공적으로 불러와졌습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }

    /**
     * AI 저장 추천 팔레트 관리 (핀/삭제/이름변경)
     *
     * 요청:
     * - action: jj_ai_palette_preset_manage
     * - security: jj_style_guide_nonce
     * - op: pin|unpin|delete|rename
     * - id: preset id (ai- 로 시작)
     * - name: (rename 시) 새 이름
     *
     * @return void
     */
    public function ajax_ai_palette_preset_manage() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $op = isset( $_POST['op'] ) ? sanitize_key( wp_unslash( $_POST['op'] ) ) : '';
        $id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
        $name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';

        if ( '' === $op || '' === $id ) {
            wp_send_json_error( array( 'message' => __( '요청 파라미터가 올바르지 않습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 안전: AI 프리셋만 조작
        if ( 0 !== strpos( $id, 'ai-' ) ) {
            wp_send_json_error( array( 'message' => __( 'AI 프리셋만 관리할 수 있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $allowed = array( 'pin', 'unpin', 'delete', 'rename', 'import', 'bulk_pin', 'bulk_unpin', 'bulk_delete' );
        if ( ! in_array( $op, $allowed, true ) ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 작업입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $option_key = 'jj_style_guide_ai_palette_presets';
        $items = get_option( $option_key, array() );
        if ( ! is_array( $items ) ) {
            $items = array();
        }

        // Bulk ops: ids[] 목록 처리
        if ( in_array( $op, array( 'bulk_pin', 'bulk_unpin', 'bulk_delete' ), true ) ) {
            $ids = isset( $_POST['ids'] ) ? (array) wp_unslash( $_POST['ids'] ) : array();
            $ids = array_values(
                array_unique(
                    array_filter(
                        array_map(
                            function( $v ) {
                                return sanitize_text_field( (string) $v );
                            },
                            $ids
                        )
                    )
                )
            );
            if ( empty( $ids ) ) {
                wp_send_json_error( array( 'message' => __( '선택된 항목이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }
            if ( count( $ids ) > 20 ) {
                wp_send_json_error( array( 'message' => __( '한 번에 최대 20개까지만 처리할 수 있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }
            foreach ( $ids as $one ) {
                if ( 0 !== strpos( $one, 'ai-' ) ) {
                    wp_send_json_error( array( 'message' => __( 'AI 프리셋만 관리할 수 있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
                }
            }

            $set = array();
            foreach ( $ids as $one ) {
                $set[ $one ] = true;
            }

            $found = 0;
            $changed = 0;
            $unchanged = 0;
            $missing = 0;

            if ( 'bulk_delete' === $op ) {
                $before = count( $items );
                $items = array_values(
                    array_filter(
                        $items,
                        function( $p ) use ( $set, &$found, &$changed ) {
                            if ( ! is_array( $p ) ) {
                                return true;
                            }
                            $pid = (string) ( $p['id'] ?? '' );
                            if ( isset( $set[ $pid ] ) ) {
                                $found++;
                                $changed++;
                                return false;
                            }
                            return true;
                        }
                    )
                );
                $missing = max( 0, count( $ids ) - $found );
                // $before not used, but keep for clarity
                unset( $before );
            } else {
                foreach ( $items as &$p ) {
                    if ( ! is_array( $p ) ) {
                        continue;
                    }
                    $pid = (string) ( $p['id'] ?? '' );
                    if ( ! isset( $set[ $pid ] ) ) {
                        continue;
                    }
                    $found++;
                    if ( 'bulk_pin' === $op ) {
                        if ( empty( $p['pinned'] ) ) {
                            $p['pinned'] = true;
                            $changed++;
                        } else {
                            $unchanged++;
                        }
                    } elseif ( 'bulk_unpin' === $op ) {
                        if ( ! empty( $p['pinned'] ) ) {
                            $p['pinned'] = false;
                            $changed++;
                        } else {
                            $unchanged++;
                        }
                    }
                }
                unset( $p );
                $missing = max( 0, count( $ids ) - $found );
            }

            // 정렬: pinned 우선, 최신(created_at) 우선
            usort(
                $items,
                function( $a, $b ) {
                    $ap = ! empty( $a['pinned'] );
                    $bp = ! empty( $b['pinned'] );
                    if ( $ap !== $bp ) {
                        return $ap ? -1 : 1;
                    }
                    $at = (string) ( $a['created_at'] ?? '' );
                    $bt = (string) ( $b['created_at'] ?? '' );
                    if ( $at === $bt ) return 0;
                    return ( $bt < $at ) ? -1 : 1; // desc
                }
            );
            $items = array_slice( $items, 0, 20 );

            update_option( $option_key, $items, false );

            $msg = __( '완료되었습니다.', 'acf-css-really-simple-style-management-center' );
            if ( 'bulk_delete' === $op ) {
                $msg = sprintf( __( '삭제 완료: %d개 삭제, %d개 누락', 'acf-css-really-simple-style-management-center' ), $changed, $missing );
            } elseif ( 'bulk_pin' === $op ) {
                $msg = sprintf( __( '상단 고정: %d개 적용, %d개 이미 고정, %d개 누락', 'acf-css-really-simple-style-management-center' ), $changed, $unchanged, $missing );
            } elseif ( 'bulk_unpin' === $op ) {
                $msg = sprintf( __( '고정 해제: %d개 적용, %d개 이미 해제, %d개 누락', 'acf-css-really-simple-style-management-center' ), $changed, $unchanged, $missing );
            }

            wp_send_json_success(
                array(
                    'message' => $msg,
                    'items' => $items,
                )
            );
        }

        // Import: JSON payload -> presets append (dedupe)
        if ( 'import' === $op ) {
            $payload_json = isset( $_POST['payload_json'] ) ? wp_unslash( $_POST['payload_json'] ) : '';
            if ( '' === $payload_json ) {
                wp_send_json_error( array( 'message' => __( '불러올 JSON이 비어있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }
            if ( strlen( $payload_json ) > 200000 ) { // ~200KB
                wp_send_json_error( array( 'message' => __( '불러올 데이터가 너무 큽니다. (안전상 제한) JSON을 나눠서 가져오세요.', 'acf-css-really-simple-style-management-center' ) ) );
            }

            $payload = json_decode( $payload_json, true );
            if ( null === $payload ) {
                wp_send_json_error( array( 'message' => __( 'JSON 파싱에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }

            $incoming = array();
            if ( is_array( $payload ) && isset( $payload[0] ) ) {
                $incoming = $payload;
            } elseif ( is_array( $payload ) ) {
                $incoming = array( $payload );
            } else {
                wp_send_json_error( array( 'message' => __( 'JSON 형식이 올바르지 않습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }

            // 안전: 한 번에 너무 많은 항목을 처리하지 않음 (서버 부하 방지)
            $incoming_trimmed = 0;
            if ( count( $incoming ) > 50 ) {
                $incoming_trimmed = count( $incoming ) - 50;
                $incoming = array_slice( $incoming, 0, 50 );
            }

            $sanitize_hex = function( $value, $fallback = '' ) {
                $v = is_string( $value ) ? $value : '';
                $v = trim( $v );
                if ( '' === $v ) {
                    $v = (string) $fallback;
                }
                if ( function_exists( 'sanitize_hex_color' ) ) {
                    $hex = sanitize_hex_color( $v );
                    return $hex ? strtoupper( $hex ) : '';
                }
                return $v;
            };

            // existing signatures to dedupe
            $sigs = array();
            foreach ( $items as $ex ) {
                if ( ! is_array( $ex ) ) {
                    continue;
                }
                $b = isset( $ex['brand'] ) && is_array( $ex['brand'] ) ? $ex['brand'] : array();
                $s = isset( $ex['system'] ) && is_array( $ex['system'] ) ? $ex['system'] : array();
                $sig = md5(
                    (string) ( $b['primary'] ?? '' ) . '|' .
                    (string) ( $b['secondary'] ?? '' ) . '|' .
                    (string) ( $s['site_bg'] ?? '' ) . '|' .
                    (string) ( $s['content_bg'] ?? '' ) . '|' .
                    (string) ( $s['text_color'] ?? '' ) . '|' .
                    (string) ( $s['link_color'] ?? '' )
                );
                $sigs[ $sig ] = true;
            }

            $imported = 0;
            $skipped = (int) $incoming_trimmed;

            foreach ( $incoming as $row ) {
                if ( ! is_array( $row ) ) {
                    $skipped++;
                    continue;
                }

                $name_in = isset( $row['name'] ) ? sanitize_text_field( (string) $row['name'] ) : '';
                if ( '' === $name_in ) {
                    $name_in = __( '가져온 AI 팔레트', 'acf-css-really-simple-style-management-center' );
                }
                if ( function_exists( 'mb_substr' ) ) {
                    $name_in = mb_substr( $name_in, 0, 80 );
                } else {
                    $name_in = substr( $name_in, 0, 80 );
                }

                $note_in = isset( $row['note'] ) ? sanitize_text_field( (string) $row['note'] ) : '';
                if ( function_exists( 'mb_substr' ) ) {
                    $note_in = mb_substr( $note_in, 0, 220 );
                } else {
                    $note_in = substr( $note_in, 0, 220 );
                }

                $tags_in = array();
                if ( isset( $row['tags'] ) && is_array( $row['tags'] ) ) {
                    foreach ( $row['tags'] as $t ) {
                        $ts = sanitize_text_field( (string) $t );
                        if ( '' !== $ts ) {
                            $tags_in[] = $ts;
                        }
                    }
                }
                if ( ! in_array( 'AI', $tags_in, true ) ) {
                    array_unshift( $tags_in, 'AI' );
                }
                $tags_in = array_values( array_unique( $tags_in ) );
                $tags_in = array_slice( $tags_in, 0, 8 );

                $brand_in = isset( $row['brand'] ) && is_array( $row['brand'] ) ? $row['brand'] : array();
                $system_in = isset( $row['system'] ) && is_array( $row['system'] ) ? $row['system'] : array();

                $primary = $sanitize_hex( $brand_in['primary'] ?? '' );
                if ( '' === $primary ) {
                    $skipped++;
                    continue;
                }
                $secondary = $sanitize_hex( $brand_in['secondary'] ?? '', $primary );

                $site_bg = $sanitize_hex( $system_in['site_bg'] ?? '' );
                $content_bg = $sanitize_hex( $system_in['content_bg'] ?? '' );
                $text_color = $sanitize_hex( $system_in['text_color'] ?? '' );
                $link_color = $sanitize_hex( $system_in['link_color'] ?? '', $primary );

                $sig2 = md5( $primary . '|' . $secondary . '|' . $site_bg . '|' . $content_bg . '|' . $text_color . '|' . $link_color );
                if ( isset( $sigs[ $sig2 ] ) ) {
                    $skipped++;
                    continue;
                }
                $sigs[ $sig2 ] = true;

                $new_id = 'ai-' . gmdate( 'Ymd-His' ) . '-' . substr( md5( wp_generate_uuid4() . microtime( true ) ), 0, 8 );
                $created_at = function_exists( 'current_time' ) ? current_time( 'mysql' ) : gmdate( 'Y-m-d H:i:s' );

                $new_item = array(
                    'id' => $new_id,
                    'name' => $name_in,
                    'tags' => $tags_in,
                    'note' => $note_in,
                    'brand' => array(
                        'primary' => $primary,
                        'secondary' => $secondary,
                        'primary_hover' => $sanitize_hex( $brand_in['primary_hover'] ?? '', $primary ),
                        'secondary_hover' => $sanitize_hex( $brand_in['secondary_hover'] ?? '', $secondary ),
                    ),
                    'system' => array(
                        'site_bg' => $site_bg,
                        'content_bg' => $content_bg,
                        'text_color' => $text_color,
                        'link_color' => $link_color,
                    ),
                    'source' => 'ai_extension',
                    'created_at' => $created_at,
                    'pinned' => ! empty( $row['pinned'] ),
                );

                array_unshift( $items, $new_item );
                $imported++;
            }

            // 정렬: pinned 우선, 최신(created_at) 우선
            usort(
                $items,
                function( $a, $b ) {
                    $ap = ! empty( $a['pinned'] );
                    $bp = ! empty( $b['pinned'] );
                    if ( $ap !== $bp ) {
                        return $ap ? -1 : 1;
                    }
                    $at = (string) ( $a['created_at'] ?? '' );
                    $bt = (string) ( $b['created_at'] ?? '' );
                    if ( $at === $bt ) return 0;
                    return ( $bt < $at ) ? -1 : 1; // desc
                }
            );

            $items = array_slice( $items, 0, 20 );
            update_option( $option_key, $items, false );

            wp_send_json_success(
                array(
                    'message' => sprintf( __( '가져오기 완료: %d개 추가, %d개 건너뜀', 'acf-css-really-simple-style-management-center' ), $imported, $skipped ),
                    'items' => $items,
                )
            );
        }

        $found = false;
        if ( 'delete' === $op ) {
            $before = count( $items );
            $items = array_values(
                array_filter(
                    $items,
                    function( $p ) use ( $id ) {
                        return is_array( $p ) && ( (string) ( $p['id'] ?? '' ) !== (string) $id );
                    }
                )
            );
            $found = ( $before !== count( $items ) );
        } else {
            foreach ( $items as &$p ) {
                if ( ! is_array( $p ) ) {
                    continue;
                }
                if ( (string) ( $p['id'] ?? '' ) !== (string) $id ) {
                    continue;
                }
                $found = true;

                if ( 'pin' === $op ) {
                    $p['pinned'] = true;
                } elseif ( 'unpin' === $op ) {
                    $p['pinned'] = false;
                } elseif ( 'rename' === $op ) {
                    if ( '' === $name ) {
                        wp_send_json_error( array( 'message' => __( '이름이 비어있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
                    }
                    $p['name'] = $name;
                }
                break;
            }
            unset( $p );
        }

        if ( ! $found ) {
            wp_send_json_error( array( 'message' => __( '해당 프리셋을 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 정렬: pinned 우선, 최신(created_at) 우선
        usort(
            $items,
            function( $a, $b ) {
                $ap = ! empty( $a['pinned'] );
                $bp = ! empty( $b['pinned'] );
                if ( $ap !== $bp ) {
                    return $ap ? -1 : 1;
                }
                $at = (string) ( $a['created_at'] ?? '' );
                $bt = (string) ( $b['created_at'] ?? '' );
                if ( $at === $bt ) return 0;
                return ( $bt < $at ) ? -1 : 1; // desc
            }
        );

        // 최대 20개 유지
        $items = array_slice( $items, 0, 20 );

        update_option( $option_key, $items, false );

        $msg = __( '완료되었습니다.', 'acf-css-really-simple-style-management-center' );
        if ( 'delete' === $op ) $msg = __( '삭제되었습니다.', 'acf-css-really-simple-style-management-center' );
        if ( 'pin' === $op ) $msg = __( '상단 고정되었습니다.', 'acf-css-really-simple-style-management-center' );
        if ( 'unpin' === $op ) $msg = __( '고정이 해제되었습니다.', 'acf-css-really-simple-style-management-center' );
        if ( 'rename' === $op ) $msg = __( '이름이 변경되었습니다.', 'acf-css-really-simple-style-management-center' );

        wp_send_json_success(
            array(
                'message' => $msg,
                'items' => $items,
            )
        );
    }

    // 나머지 메서드들은 dev 버전과 동일 (sync_to_spokes, sync_from_customizer, sanitize_options_recursive 등)
    // 생략하거나 dev 버전 파일을 복사하여 수정

    private function sync_to_spokes( $hub_options ) {
        // Free 버전: WooCommerce만 동기화
        $brand_colors = $hub_options['palettes']['brand'] ?? array();
        
        // WooCommerce 동기화
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! is_array( $kwe ) ) {
            $kwe = array();
        }
        if ( isset( $brand_colors['primary_color'] ) ) {
            $kwe['product_quickview_button_color'] = $brand_colors['primary_color'];
        }
        if ( isset( $brand_colors['primary_color_hover'] ) ) {
            $kwe['product_quickview_button_color_hover'] = $brand_colors['primary_color_hover'];
        }
        update_option( 'kt_woo_extras', $kwe );
    }

    public function sync_from_customizer( $manager ) {
        $hub_options = (array) get_option( $this->options_key );
        $panel_settings = $manager->settings();
        
        foreach ( $panel_settings as $setting_id => $setting_obj ) {
            if ( strpos( $setting_id, $this->options_key . '[' ) === 0 ) {
                $keys = str_replace( array( $this->options_key, '[', ']' ), '', $setting_id );
                $keys = explode( '][', $keys );
                $this->set_nested_object_value( $hub_options, $keys, $setting_obj->post_value() );
            }
        }

        update_option( $this->options_key, $hub_options );
        JJ_CSS_Cache::instance()->flush();
    }

    private function set_nested_object_value( &$obj, $keys, $value ) {
        $current = &$obj;
        foreach ( $keys as $key ) {
            if ( ! isset( $current[$key] ) || ! is_array( $current[$key] ) ) {
                $current[$key] = array();
            }
            $current = &$current[$key];
        }
        $current = $value;
    }

    private function sanitize_options_recursive( $input ) {
        $output = array();
        foreach ( (array) $input as $key => $value ) {
            $raw_key = (string) $key;
            // [v5.3.8] WordPress 함수 안전 호출
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( $raw_key ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( $raw_key ) );
            if ( is_array( $value ) ) {
                $output[ $key ] = $this->sanitize_options_recursive( $value );
                continue;
            }
            // [v5.3.8] PHP 7.4 호환성: str_contains() 대신 strpos() 사용
            if ( strpos( $key, 'color' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $sanitized = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( $value ) : '';
                $output[ $key ] = $sanitized ? $sanitized : '';
                continue;
            }
            if ( strpos( $key, 'custom_css' ) !== false || strpos( $key, 'additional_css' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'wp_kses_post' ) ? wp_kses_post( $value ) : wp_strip_all_tags( $value );
                continue;
            }
            if ( strpos( $key, 'font_family' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
                continue;
            }
            // [v5.3.8] WordPress 함수 안전 호출
            $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
        }
        return $output;
    }

    public function load_options_and_init_strategies() {
        // [v5.3.5] 전체 메서드를 try-catch로 보호하여 치명적 오류 방지
        try {
            $this->environment_map = $this->scan_environment();
            $this->load_dynamic_adapters( $this->environment_map );
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options = $options;
            $this->sync_existing_settings();
            $this->brand_colors = $this->options['palettes']['brand'] ?? array();

            // [v5.3.5] 모든 전략 클래스 호출에 class_exists() 확인 추가
            // 전략 1: CSS 변수
            if ( class_exists( 'JJ_Strategy_1_CSS_Vars' ) ) {
                JJ_Strategy_1_CSS_Vars::instance()->init( $this->options );
            }
            
            // 전략 2: PHP 필터
            if ( class_exists( 'JJ_Strategy_2_PHP_Filters' ) ) {
                JJ_Strategy_2_PHP_Filters::instance()->init( $this->options, null, null, null, null );
            }
            
            // 전략 3: Dequeue (CSS 재정의)
            if ( class_exists( 'JJ_Strategy_3_Dequeue' ) ) {
                JJ_Strategy_3_Dequeue::instance()->init( $this->options );
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    private function sync_existing_settings() {
        $active_theme = wp_get_theme()->get_template();
        $needs_update = false;

        if ( ! isset( $this->options['palettes'] ) ) {
            $this->options['palettes'] = array();
        }
        if ( ! isset( $this->options['palettes']['brand'] ) ) {
            $this->options['palettes']['brand'] = array();
        }
        if ( ! isset( $this->options['typography'] ) ) {
            $this->options['typography'] = array();
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five 테마만 동기화
        if ( in_array( $active_theme, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
            // 기본 테마는 별도 설정이 없으므로 Customizer에서 가져오기
        }

        // WooCommerce 동기화 (Free 버전 지원)
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! empty( $kwe['product_quickview_button_color'] ) && empty( $this->options['palettes']['brand']['primary_color'] ) ) {
            $this->options['palettes']['brand']['primary_color'] = $kwe['product_quickview_button_color'];
            $needs_update = true;
        }
        if ( ! empty( $kwe['product_quickview_button_color_hover'] ) && empty( $this->options['palettes']['brand']['primary_color_hover'] ) ) {
            $this->options['palettes']['brand']['primary_color_hover'] = $kwe['product_quickview_button_color_hover'];
            $needs_update = true;
        }

        if ( $needs_update ) {
            update_option( $this->options_key, $this->options );
        }
    }

    private function load_dynamic_adapters( $environment_map ) {
        $active_adapters = $environment_map;
        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;
        $error_handler = JJ_Error_Handler::instance();

        // Free 버전: 테마 어댑터 제한 (Twenty Twenty-Four, Twenty Twenty-Five만)
        if ( ! empty( $active_adapters['themes'] ) ) {
            $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
            $active_adapters['themes'] = array_intersect( $active_adapters['themes'], $allowed_themes );
            
            foreach ( $active_adapters['themes'] as $theme_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-theme-' . sanitize_file_name( $theme_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Theme_' . str_replace( '-', '_', ucwords( $theme_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }

        // Free 버전: 플러그인 어댑터 제한 (WooCommerce만)
        if ( ! empty( $active_adapters['spokes'] ) ) {
            $allowed_plugins = array( 'woocommerce' );
            $active_adapters['spokes'] = array_intersect( $active_adapters['spokes'], $allowed_plugins );
            
            foreach ( $active_adapters['spokes'] as $spoke_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-spoke-' . sanitize_file_name( $spoke_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Spoke_' . str_replace( '-', '_', ucwords( $spoke_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }
    }

    private function scan_environment() {
        $config = $this->adapters_config;
        $spokes_to_check = $config['spokes'] ?? array();
        $themes_to_check = $config['themes'] ?? array();

        $active_plugins = (array) get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        $active_spoke_adapters = array();
        $active_theme_adapters = array();
        $current_theme_path = get_template_directory();
        $theme_dir_name = basename( $current_theme_path );

        // Free 버전: WooCommerce만 체크
        foreach ( $spokes_to_check as $adapter_slug => $plugin_path ) {
            if ( in_array( $plugin_path, $active_plugins ) && $adapter_slug === 'woocommerce' ) {
                $active_spoke_adapters[] = $adapter_slug;
            }
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five만 체크
        foreach ( $themes_to_check as $adapter_slug => $theme_path_suffix ) {
            if ( $theme_dir_name === $adapter_slug && in_array( $adapter_slug, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
                if ( file_exists( $current_theme_path . '/' . $theme_path_suffix ) ) {
                    $active_theme_adapters[] = $adapter_slug;
                }
            }
        }

        return array(
            'themes' => $active_theme_adapters,
            'spokes' => $active_spoke_adapters,
        );
    }

    /**
     * [v8.x] 부팅 진단: Safe Loader 누락/로드 오류를 관리자에게 안내
     * - 치명적 오류를 막기 위한 조기 경고(원인 파악 UX)
     */
    public function maybe_show_boot_diagnostics_notice() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // 플러그인 관련 화면에서만 표시(노이즈 최소화)
        $page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
        $allowed_pages = array( 'jj-admin-center', JJ_STYLE_GUIDE_PAGE_SLUG );
        if ( '' !== $page && ! in_array( $page, $allowed_pages, true ) ) {
            return;
        }

        // 1) Safe Loader 진단 우선
        $missing_required = array();
        $errors  = array();
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'get_missing_files' ) ) {
            $missing = (array) JJ_Safe_Loader::get_missing_files();
            $errors  = method_exists( 'JJ_Safe_Loader', 'get_load_errors' ) ? (array) JJ_Safe_Loader::get_load_errors() : array();

            foreach ( $missing as $m ) {
                if ( is_array( $m ) && ! empty( $m['required'] ) ) {
                    $missing_required[] = $m;
                }
            }
        } else {
            // 2) 폴백 로더 진단 (Safe Loader 자체 누락/손상 대비)
            if ( isset( $GLOBALS['jj_style_guide_boot_diag'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag'] ) ) {
                $mr = isset( $GLOBALS['jj_style_guide_boot_diag']['missing_required'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag']['missing_required'] )
                    ? $GLOBALS['jj_style_guide_boot_diag']['missing_required']
                    : array();
                $missing_required = array_values( $mr );

                $errors = isset( $GLOBALS['jj_style_guide_boot_diag']['load_errors'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag']['load_errors'] )
                    ? $GLOBALS['jj_style_guide_boot_diag']['load_errors']
                    : array();
            }
        }

        if ( empty( $missing_required ) && empty( $errors ) ) {
            return;
        }

        $admin_center_url = function_exists( 'admin_url' ) ? admin_url( 'options-general.php?page=jj-admin-center' ) : '';

        echo '<div class="notice notice-error jj-notice" style="border-left-color:#d63638;">';
        echo '<p><strong>ACF CSS:</strong> ' . esc_html__( '부팅 진단 경고가 감지되었습니다. (파일 누락/로드 오류)', 'acf-css-really-simple-style-management-center' ) . '</p>';
        echo '<ul style="margin: 6px 0 0 18px; list-style: disc;">';
        if ( ! empty( $missing_required ) ) {
            echo '<li>' . esc_html( sprintf( '필수 파일 누락: %d개', count( $missing_required ) ) ) . '</li>';
        }
        if ( ! empty( $errors ) ) {
            echo '<li>' . esc_html( sprintf( '로드 오류: %d개', count( $errors ) ) ) . '</li>';
        }
        echo '</ul>';
        if ( $admin_center_url ) {
            echo '<p style="margin: 8px 0 0 0;">' . sprintf(
                /* translators: %s admin center link */
                esc_html__( '원인 확인: %s → System Status 탭에서 “자가 진단”을 실행하세요.', 'acf-css-really-simple-style-management-center' ),
                '<a href="' . esc_url( $admin_center_url ) . '">' . esc_html__( 'Admin Center', 'acf-css-really-simple-style-management-center' ) . '</a>'
            ) . '</p>';
        }
        echo '</div>';
    }

    public function render_debug_info() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $errors = JJ_Error_Handler::instance()->get_errors();

        echo '<div style="background: #001a33; color: #00ff8c; padding: 20px; font-family: monospace; font-size: 14px; line-height: 1.6; z-index: 99999; position: relative; margin: 20px; border-radius: 5px; white-space: pre-wrap; word-break: break-all; opacity: 0.9;">';
        echo '<strong>[ACF CSS DEBUG - FREE VERSION]</strong><br>';
        echo '====================<br>';
        echo '<strong>Edition:</strong> Free<br>';
        echo '<strong>Loaded Adapters:</strong><br>';
        if ( ! empty( $this->environment_map['themes'] ) ) {
            echo ' - Themes: ' . esc_html( implode( ', ', $this->environment_map['themes'] ) ) . '<br>';
        } else {
            echo ' - Themes: None<br>';
        }
        if ( ! empty( $this->environment_map['spokes'] ) ) {
            echo ' - Spokes: ' . esc_html( implode( ', ', $this->environment_map['spokes'] ) ) . '<br>';
        } else {
            echo ' - Spokes: None<br>';
        }
        echo '</div>';
    }
}

function jj_simple_style_guide_master_run() {
    // [Phase 5] 다국어 지원 로드
    load_plugin_textdomain( 'acf-css-really-simple-style-management-center', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // WordPress 함수가 로드되었는지 확인
    if ( ! function_exists( 'class_exists' ) || ! function_exists( 'file_exists' ) ) {
        return;
    }
    
    // [v5.3.9] 코드 무결성 모니터 초기화 (plugins_loaded 시점)
    
    // [Phase 4.5/4.99] Admin Center 초기화 (메뉴/상단바/관리자 UI)
    if ( class_exists( 'JJ_Admin_Center' ) ) {
        try {
            $admin_center = JJ_Admin_Center::instance();
            if ( method_exists( $admin_center, 'init' ) ) {
                $admin_center->init();
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v3.7.0] 백업 관리자 초기화
    if ( class_exists( 'JJ_Backup_Manager' ) ) {
        try {
            JJ_Backup_Manager::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] REST API 초기화
    if ( class_exists( 'JJ_Style_Guide_REST_API' ) ) {
        try {
            JJ_Style_Guide_REST_API::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.3] Extension Manager 초기화
    if ( class_exists( 'JJ_Extension_Manager' ) ) {
        try {
            JJ_Extension_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] Webhook Manager 초기화
    if ( class_exists( 'JJ_Webhook_Manager' ) ) {
        try {
            JJ_Webhook_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5 B] Multisite Controller 초기화
    if ( class_exists( 'JJ_Multisite_Controller' ) ) {
        try {
            JJ_Multisite_Controller::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v5.6.0] Visual Command Center 초기화
    if ( class_exists( 'JJ_Visual_Command_Center' ) ) {
        JJ_Visual_Command_Center::instance();
    }

    // [v5.7.0] AI Style Intelligence 초기화
    if ( class_exists( 'JJ_Smart_Palette' ) ) {
        JJ_Smart_Palette::instance();
    }

    // [v5.9.0] CSS Optimizer 초기화
    if ( class_exists( 'JJ_CSS_Optimizer' ) ) {
        JJ_CSS_Optimizer::instance();
    }

    // [v5.8.0] Cloud Manager 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Cloud_Manager' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) { // Premium 이상 체크 대용
            JJ_Cloud_Manager::instance();
        }
    }
    
    // [Phase 3] 템플릿 마켓 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Template_Market' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) {
            JJ_Template_Market::instance();
        }
    }
    
    // [Phase 5] Partner Hub 초기화 (Partner/Master만)
    if ( class_exists( 'JJ_Partner_Hub' ) ) {
        JJ_Partner_Hub::instance();
    }

    // [v4.2.2] 실험실(Labs) 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Labs_Center' ) ) {
        try {
            JJ_Labs_Center::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }


    if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
        try {
            JJ_Code_Integrity_Monitor::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Code Integrity Monitor: 초기화 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.9] 라이센스 강제 실행 초기화 (plugins_loaded 시점)
    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $enforcement_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-enforcement.php';
        if ( file_exists( $enforcement_file ) ) {
            require_once $enforcement_file;
            if ( class_exists( 'JJ_License_Enforcement' ) ) {
                try {
                    JJ_License_Enforcement::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ License Enforcement: 초기화 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // [v5.3.7] 안전 모드 플래그 확인 및 활성화
    if ( function_exists( 'get_option' ) && class_exists( 'JJ_Safe_Mode' ) ) {
        $safe_mode_enabled = get_option( 'jj_style_guide_safe_mode', false );
        if ( $safe_mode_enabled ) {
            $safe_mode_reason = get_option( 'jj_style_guide_safe_mode_reason', '활성화 중 오류 발생' );
            try {
                $safe_mode = JJ_Safe_Mode::instance();
                if ( method_exists( $safe_mode, 'enable' ) ) {
                    $safe_mode->enable( $safe_mode_reason );
                }
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 오류 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
    
    // 메인 클래스 인스턴스 생성
    if ( class_exists( 'JJ_Simple_Style_Guide_Master' ) ) {
        try {
            JJ_Simple_Style_Guide_Master::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.6] 기존 플러그인 마이그레이션 매니저 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $migration_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php';
        if ( file_exists( $migration_file ) ) {
            // 마이그레이션 매니저는 이미 safe_loader를 통해 로드되었을 수 있음
            // 클래스가 존재하는 경우에만 초기화
            if ( class_exists( 'JJ_Migration_Manager' ) ) {
                try {
                    JJ_Migration_Manager::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 오류 - ' . $e->getMessage() );
                    }
                } catch ( Error $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // 플러그인 업데이트 체크 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $updater_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php';
        if ( file_exists( $updater_file ) ) {
            require_once $updater_file;
            if ( class_exists( 'JJ_Plugin_Updater' ) ) {
                try {
                    new JJ_Plugin_Updater(
                        __FILE__,
                        'advanced-custom-style-manage-center',
                        "ACF CSS Master",
                        defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.4.0',
                        'DEV' // 마스터 버전
                    );
                } catch ( Exception $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 오류 - ' . $e->getMessage() );
                } catch ( Error $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
}

// [v5.3.8] plugins_loaded 훅 활성화
if ( function_exists( 'add_action' ) ) {
    add_action( 'plugins_loaded', 'jj_simple_style_guide_master_run', 10 );
}

/**
 * [v8.0.0] 안전 장치: 필수 헬퍼 함수 Fallback 정의
 * class-jj-admin-center.php 로드 실패 시에도 사이트가 멈추지 않도록 보장합니다.
 */
if ( ! function_exists( 'jj_style_guide_text' ) ) {
    function jj_style_guide_text( $key, $fallback = '' ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_text( $key, $fallback );
        }
        return $fallback;
    }
}

if ( ! function_exists( 'jj_style_guide_sections_layout' ) ) {
    function jj_style_guide_sections_layout() {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_sections_layout();
        }
        return array();
    }
}

if ( ! function_exists( 'jj_style_guide_section_index' ) ) {
    function jj_style_guide_section_index( $slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_section_index( $slug );
        }
        return null;
    }
}

if ( ! function_exists( 'jj_style_guide_is_tab_enabled' ) ) {
    function jj_style_guide_is_tab_enabled( $section_slug, $tab_slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->is_tab_enabled( $section_slug, $tab_slug );
        }
        return true;
    }
}

```

```markdown
### ✅ Phase 8.3.2: 폼 UX 개선 완료

#### 자동 저장 및 실시간 검증 ✅
- ✅ `jj-form-enhancer.js` 생성
  - localStorage 기반 자동 저장 (1초 debounce)
  - 페이지 새로고침 시 자동 복원
  - 실시간 유효성 검사 (HEX 색상, URL, 이메일)
  - 필드 오류 표시 시스템
  - 툴팁 및 도움말 자동 생성

### ✅ Phase 8.3.3: 피드백 시스템 개선 완료

#### Toast 알림 시스템 ✅
- ✅ 개선된 Toast 시스템
  - 슬라이드 애니메이션 (in/out)
  - 스택 관리 (여러 알림 동시 표시)
  - 호버 시 자동 닫힘 일시 정지
  - 액션 버튼 지원
  - 표준화된 메시지 함수 (showSuccess, showError, showWarning, showInfo)

### ✅ Phase 8.4: 적용 전/후 요약(diff) + Undo 확장 완료

#### 전역 Undo 시스템 ✅
- ✅ `jj-undosystem.js` 생성
  - 전체 설정(팔레트/버튼/폼/링크) Undo/Redo 지원
  - 적용 전/후 diff 요약 모달
  - localStorage 기반 스냅샷 저장 (최대 10개)
  - 키보드 단축키 (Ctrl+Z, Ctrl+Y)
  - 프리셋 적용 시 자동 스냅샷 생성

### ✅ Phase 8.5.1: AI Extension 감지 및 활성화 유도 완료

#### AI Extension Promoter ✅
- ✅ 설치/활성화 상태 자동 감지
- ✅ 원클릭 활성화 기능
- ✅ "다시 보지 않기" 기능 (일주일)
