/**
 * ACF User Journey Analytics - Frontend Tracker
 *
 * 방문자 행동 추적 스크립트
 */

(function() {
    'use strict';

    // 설정 확인
    if (typeof acf_uja_config === 'undefined') {
        return;
    }

    var config = acf_uja_config;
    var startTime = Date.now();
    var pageViews = 1;

    /**
     * 방문자 ID 생성/가져오기
     */
    function getVisitorId() {
        var visitorId = getCookie('acf_uja_visitor');
        if (!visitorId) {
            visitorId = generateUUID();
            setCookie('acf_uja_visitor', visitorId, 365);
        }
        return visitorId;
    }

    /**
     * 세션 ID 생성/가져오기
     */
    function getSessionId() {
        var sessionId = sessionStorage.getItem('acf_uja_session');
        if (!sessionId) {
            sessionId = generateUUID();
            sessionStorage.setItem('acf_uja_session', sessionId);
        }
        return sessionId;
    }

    /**
     * UUID 생성
     */
    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0;
            var v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    /**
     * 쿠키 설정
     */
    function setCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/; SameSite=Lax';
    }

    /**
     * 쿠키 가져오기
     */
    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) {
                return decodeURIComponent(c.substring(nameEQ.length));
            }
        }
        return null;
    }

    /**
     * URL 파라미터 가져오기
     */
    function getUrlParams() {
        var params = {};
        var search = window.location.search.substring(1);
        if (search) {
            var pairs = search.split('&');
            for (var i = 0; i < pairs.length; i++) {
                var pair = pairs[i].split('=');
                if (pair.length === 2) {
                    params[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
                }
            }
        }
        return params;
    }

    /**
     * 디바이스 유형 감지
     */
    function getDeviceType() {
        var ua = navigator.userAgent.toLowerCase();
        if (/mobile|android|iphone|phone/i.test(ua)) {
            if (/ipad|tablet/i.test(ua)) {
                return 'tablet';
            }
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * 화면 해상도
     */
    function getScreenResolution() {
        return screen.width + 'x' + screen.height;
    }

    /**
     * 뷰포트 크기
     */
    function getViewport() {
        return window.innerWidth + 'x' + window.innerHeight;
    }

    /**
     * 추적 데이터 전송
     */
    function trackVisit(eventType) {
        eventType = eventType || 'pageview';

        var data = {
            action: 'acf_uja_track_visit',
            nonce: config.nonce,
            visitor_id: getVisitorId(),
            session_id: getSessionId(),
            user_id: config.userId || 0,
            event_type: eventType,
            landing_page: window.location.href,
            landing_path: window.location.pathname,
            referrer: document.referrer,
            device_type: getDeviceType(),
            screen_resolution: getScreenResolution(),
            viewport: getViewport(),
            user_language: navigator.language || navigator.userLanguage,
            dwell_time: Math.round((Date.now() - startTime) / 1000),
            page_views: pageViews
        };

        // URL 파라미터 추가
        var params = getUrlParams();
        for (var key in params) {
            if (params.hasOwnProperty(key)) {
                data[key] = params[key];
            }
        }

        // AJAX 전송
        var xhr = new XMLHttpRequest();
        xhr.open('POST', config.ajaxUrl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        var formData = [];
        for (var key in data) {
            if (data.hasOwnProperty(key)) {
                formData.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
            }
        }

        xhr.send(formData.join('&'));
    }

    /**
     * 페이지 이탈 시 체류시간 전송
     */
    function trackExit() {
        var data = {
            action: 'acf_uja_track_visit',
            nonce: config.nonce,
            visitor_id: getVisitorId(),
            session_id: getSessionId(),
            event_type: 'exit',
            dwell_time: Math.round((Date.now() - startTime) / 1000)
        };

        // Beacon API 사용 (브라우저 종료 시에도 전송 보장)
        if (navigator.sendBeacon) {
            var formData = new FormData();
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    formData.append(key, data[key]);
                }
            }
            navigator.sendBeacon(config.ajaxUrl, formData);
        } else {
            // Fallback: 동기 XHR
            var xhr = new XMLHttpRequest();
            xhr.open('POST', config.ajaxUrl, false);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            var formDataStr = [];
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    formDataStr.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
                }
            }
            xhr.send(formDataStr.join('&'));
        }
    }

    /**
     * 스크롤 깊이 추적
     */
    var maxScrollDepth = 0;
    function trackScrollDepth() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        var docHeight = document.documentElement.scrollHeight - window.innerHeight;
        var scrollPercent = docHeight > 0 ? Math.round((scrollTop / docHeight) * 100) : 0;

        if (scrollPercent > maxScrollDepth) {
            maxScrollDepth = scrollPercent;
        }
    }

    /**
     * 이벤트 리스너 등록
     */
    function init() {
        // 페이지뷰 추적
        if (document.readyState === 'complete') {
            trackVisit('pageview');
        } else {
            window.addEventListener('load', function() {
                trackVisit('pageview');
            });
        }

        // 페이지 이탈 추적
        window.addEventListener('beforeunload', trackExit);
        window.addEventListener('pagehide', trackExit);

        // 스크롤 깊이 추적
        window.addEventListener('scroll', trackScrollDepth, { passive: true });

        // SPA 히스토리 변경 감지
        if (window.history && window.history.pushState) {
            var originalPushState = window.history.pushState;
            window.history.pushState = function() {
                originalPushState.apply(this, arguments);
                pageViews++;
                trackVisit('pageview');
            };

            window.addEventListener('popstate', function() {
                pageViews++;
                trackVisit('pageview');
            });
        }
    }

    // 초기화
    init();

})();
