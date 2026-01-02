/**
 * JJ i18n (Internationalization) Utilities
 * 
 * [Phase 9.1] JavaScript 번역 지원 개선
 * WordPress wp.i18n 통합 및 동적 번역 로딩
 * 
 * @since 9.1.0
 */

(function($) {
    'use strict';
    
    // 전역 네임스페이스
    window.JJi18n = window.JJi18n || {};
    
    // 번역 캐시
    const translationCache = {};
    
    // 로케일 정보
    const locale = window.jjI18nData?.locale || 'en_US';
    const textDomain = window.jjI18nData?.textDomain || 'acf-css-really-simple-style-management-center';
    
    /**
     * 번역 함수 (WordPress wp.i18n.__와 호환)
     * 
     * @param {string} text 번역할 텍스트
     * @param {string} domain 텍스트 도메인 (선택적)
     * @returns {string} 번역된 텍스트
     */
    JJi18n.__ = function(text, domain) {
        domain = domain || textDomain;
        
        // WordPress wp.i18n 사용 가능한 경우
        if (window.wp && window.wp.i18n && window.wp.i18n.__) {
            return window.wp.i18n.__(text, domain);
        }
        
        // 캐시 확인
        const cacheKey = domain + ':' + text;
        if (translationCache[cacheKey]) {
            return translationCache[cacheKey];
        }
        
        // 전역 번역 데이터 확인
        if (window.jjI18nData && window.jjI18nData.translations) {
            const translations = window.jjI18nData.translations;
            if (translations[text]) {
                translationCache[cacheKey] = translations[text];
                return translations[text];
            }
        }
        
        // 번역 없음 - 원문 반환
        return text;
    };
    
    /**
     * 번역 및 출력 함수 (WordPress wp.i18n._e와 호환)
     * 
     * @param {string} text 번역할 텍스트
     * @param {string} domain 텍스트 도메인 (선택적)
     */
    JJi18n._e = function(text, domain) {
        console.log(JJi18n.__(text, domain));
    };
    
    /**
     * 복수형 번역 함수 (WordPress wp.i18n._n과 호환)
     * 
     * @param {string} single 단수형 텍스트
     * @param {string} plural 복수형 텍스트
     * @param {number} number 숫자
     * @param {string} domain 텍스트 도메인 (선택적)
     * @returns {string} 번역된 텍스트
     */
    JJi18n._n = function(single, plural, number, domain) {
        domain = domain || textDomain;
        
        // WordPress wp.i18n 사용 가능한 경우
        if (window.wp && window.wp.i18n && window.wp.i18n._n) {
            return window.wp.i18n._n(single, plural, number, domain);
        }
        
        // 간단한 복수형 처리
        return number === 1 ? JJi18n.__(single, domain) : JJi18n.__(plural, domain);
    };
    
    /**
     * 컨텍스트 번역 함수 (WordPress wp.i18n._x와 호환)
     * 
     * @param {string} text 번역할 텍스트
     * @param {string} context 컨텍스트
     * @param {string} domain 텍스트 도메인 (선택적)
     * @returns {string} 번역된 텍스트
     */
    JJi18n._x = function(text, context, domain) {
        domain = domain || textDomain;
        
        // WordPress wp.i18n 사용 가능한 경우
        if (window.wp && window.wp.i18n && window.wp.i18n._x) {
            return window.wp.i18n._x(text, context, domain);
        }
        
        // 컨텍스트 키 생성
        const contextKey = context + ':' + text;
        
        // 전역 번역 데이터 확인
        if (window.jjI18nData && window.jjI18nData.translations) {
            const translations = window.jjI18nData.translations;
            if (translations[contextKey]) {
                return translations[contextKey];
            }
        }
        
        // 컨텍스트 번역 없음 - 일반 번역 시도
        return JJi18n.__(text, domain);
    };
    
    /**
     * 날짜 형식화 (현지화)
     * 
     * @param {Date|string|number} date 날짜
     * @param {string} format 형식 (선택적)
     * @returns {string} 형식화된 날짜
     */
    JJi18n.formatDate = function(date, format) {
        if (!date) return '';
        
        const d = date instanceof Date ? date : new Date(date);
        if (isNaN(d.getTime())) return '';
        
        format = format || 'Y-m-d H:i:s';
        
        // WordPress 날짜 형식 사용 가능한 경우
        if (window.wp && window.wp.date && window.wp.date.i18n) {
            return window.wp.date.i18n(format, d);
        }
        
        // 기본 형식화
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        const seconds = String(d.getSeconds()).padStart(2, '0');
        
        return format
            .replace('Y', year)
            .replace('m', month)
            .replace('d', day)
            .replace('H', hours)
            .replace('i', minutes)
            .replace('s', seconds);
    };
    
    /**
     * 숫자 형식화 (현지화)
     * 
     * @param {number} number 숫자
     * @param {number} decimals 소수점 자릿수 (선택적)
     * @returns {string} 형식화된 숫자
     */
    JJi18n.formatNumber = function(number, decimals) {
        if (isNaN(number)) return '';
        
        decimals = decimals !== undefined ? decimals : 2;
        
        // Intl.NumberFormat 사용 (브라우저 지원)
        if (window.Intl && window.Intl.NumberFormat) {
            try {
                const formatter = new Intl.NumberFormat(locale, {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals,
                });
                return formatter.format(number);
            } catch (e) {
                // 폴백
            }
        }
        
        // 기본 형식화
        return parseFloat(number).toFixed(decimals);
    };
    
    /**
     * 통화 형식화 (현지화)
     * 
     * @param {number} amount 금액
     * @param {string} currency 통화 코드 (선택적, 기본: USD)
     * @returns {string} 형식화된 통화
     */
    JJi18n.formatCurrency = function(amount, currency) {
        if (isNaN(amount)) return '';
        
        currency = currency || 'USD';
        
        // Intl.NumberFormat 사용
        if (window.Intl && window.Intl.NumberFormat) {
            try {
                const formatter = new Intl.NumberFormat(locale, {
                    style: 'currency',
                    currency: currency,
                });
                return formatter.format(amount);
            } catch (e) {
                // 폴백
            }
        }
        
        // 기본 형식화
        return currency + ' ' + JJi18n.formatNumber(amount, 2);
    };
    
    /**
     * 번역 데이터 로드 (AJAX)
     * 
     * @param {string} domain 텍스트 도메인
     * @param {Function} callback 콜백 함수
     */
    JJi18n.loadTranslations = function(domain, callback) {
        domain = domain || textDomain;
        
        // 이미 로드된 경우
        if (translationCache[domain + ':loaded']) {
            if (callback) callback();
            return;
        }
        
        // AJAX로 번역 데이터 로드
        if (window.jjI18nData && window.jjI18nData.ajax_url) {
            $.ajax({
                url: window.jjI18nData.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_load_translations',
                    domain: domain,
                    locale: locale,
                    nonce: window.jjI18nData.nonce || '',
                },
                success: function(response) {
                    if (response.success && response.data && response.data.translations) {
                        // 번역 데이터 병합
                        if (!window.jjI18nData.translations) {
                            window.jjI18nData.translations = {};
                        }
                        Object.assign(window.jjI18nData.translations, response.data.translations);
                        translationCache[domain + ':loaded'] = true;
                    }
                    if (callback) callback();
                },
                error: function() {
                    if (callback) callback();
                }
            });
        } else {
            if (callback) callback();
        }
    };
    
    /**
     * 전역 별칭 (기존 코드 호환성)
     */
    window.__ = window.__ || JJi18n.__;
    window._e = window._e || JJi18n._e;
    window._n = window._n || JJi18n._n;
    window._x = window._x || JJi18n._x;
    
})(jQuery);
