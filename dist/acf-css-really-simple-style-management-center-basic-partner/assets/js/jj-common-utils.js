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
