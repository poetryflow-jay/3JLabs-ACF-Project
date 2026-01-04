/**
 * ACF Nudge Flow - Analytics Integration
 * 
 * Google Analytics, Monster Insights, Microsoft Clarity, Hotjar 등
 * 다양한 분석 도구와 통합하여 사용자 행동 데이터를 수집합니다.
 * 
 * @package ACF_Nudge_Flow
 * @since 22.5.2
 */

(function(window, document) {
    'use strict';

    // 전역 네임스페이스
    window.ACF_NF_Analytics = window.ACF_NF_Analytics || {};

    /**
     * 분석 통합 클래스
     */
    class AnalyticsIntegration {
        constructor() {
            this.config = window.acf_nf_analytics_config || {};
            this.activeProviders = [];
            this.sessionData = {
                startTime: Date.now(),
                pageViews: 0,
                scrollDepth: 0,
                interactions: 0,
                exitIntent: false,
                bounceRisk: 'low'
            };
            this.dataQueue = [];
            this.sendInterval = 30000; // 30초
            this.lastSendTime = 0;

            this.init();
        }

        /**
         * 초기화
         */
        init() {
            this.detectProviders();
            this.setupTracking();
            this.setupEventListeners();
            this.startDataSync();

            console.log('[ACF NF Analytics] Initialized with providers:', this.activeProviders);
        }

        /**
         * 활성화된 분석 제공자 감지
         */
        detectProviders() {
            // Google Analytics (GA4)
            if (typeof gtag === 'function' || typeof ga === 'function' || window.dataLayer) {
                this.activeProviders.push('google_analytics');
            }

            // Monster Insights
            if (typeof __gaTracker === 'function' || window.MonsterInsights) {
                this.activeProviders.push('monster_insights');
            }

            // Microsoft Clarity
            if (typeof clarity === 'function' || window.clarity) {
                this.activeProviders.push('ms_clarity');
            }

            // Hotjar
            if (typeof hj === 'function' || window.hj) {
                this.activeProviders.push('hotjar');
            }

            // Mixpanel
            if (typeof mixpanel === 'object' && mixpanel.track) {
                this.activeProviders.push('mixpanel');
            }

            // Amplitude
            if (typeof amplitude === 'object' && amplitude.getInstance) {
                this.activeProviders.push('amplitude');
            }

            // Facebook Pixel
            if (typeof fbq === 'function') {
                this.activeProviders.push('facebook_pixel');
            }

            // WooCommerce 분석
            if (typeof wc_ga4 !== 'undefined' || window.woocommerce_params) {
                this.activeProviders.push('woocommerce_analytics');
            }
        }

        /**
         * 트래킹 설정
         */
        setupTracking() {
            // 페이지 뷰 트래킹
            this.trackPageView();

            // 스크롤 깊이 트래킹
            this.setupScrollTracking();

            // 세션 시간 트래킹
            this.setupSessionTracking();

            // Exit Intent 트래킹
            this.setupExitIntentTracking();

            // 클릭 트래킹
            this.setupClickTracking();

            // 폼 트래킹
            this.setupFormTracking();
        }

        /**
         * 이벤트 리스너 설정
         */
        setupEventListeners() {
            // 페이지 언로드 시 데이터 전송
            window.addEventListener('beforeunload', () => {
                this.sendDataImmediately();
            });

            // 가시성 변경 감지
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'hidden') {
                    this.sendDataImmediately();
                }
            });

            // 커스텀 이벤트 리스닝
            document.addEventListener('acf_nf_nudge_displayed', (e) => {
                this.trackNudgeEvent('displayed', e.detail);
            });

            document.addEventListener('acf_nf_nudge_clicked', (e) => {
                this.trackNudgeEvent('clicked', e.detail);
            });

            document.addEventListener('acf_nf_nudge_dismissed', (e) => {
                this.trackNudgeEvent('dismissed', e.detail);
            });

            document.addEventListener('acf_nf_nudge_converted', (e) => {
                this.trackNudgeEvent('converted', e.detail);
            });
        }

        /**
         * 페이지 뷰 트래킹
         */
        trackPageView() {
            this.sessionData.pageViews++;

            const pageData = {
                url: window.location.href,
                title: document.title,
                referrer: document.referrer,
                timestamp: Date.now()
            };

            this.queueData('page_view', pageData);

            // 제공자별 트래킹
            if (this.hasProvider('google_analytics')) {
                this.trackGA4Event('page_view', pageData);
            }
        }

        /**
         * 스크롤 깊이 트래킹
         */
        setupScrollTracking() {
            let maxScroll = 0;
            let scrollThresholds = [25, 50, 75, 90, 100];
            let triggeredThresholds = [];

            const trackScroll = this.throttle(() => {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
                const scrollPercent = Math.round((scrollTop / scrollHeight) * 100);

                if (scrollPercent > maxScroll) {
                    maxScroll = scrollPercent;
                    this.sessionData.scrollDepth = maxScroll;

                    // 임계값 트래킹
                    scrollThresholds.forEach(threshold => {
                        if (scrollPercent >= threshold && !triggeredThresholds.includes(threshold)) {
                            triggeredThresholds.push(threshold);
                            this.queueData('scroll_depth', {
                                depth: threshold,
                                timestamp: Date.now()
                            });

                            // Clarity 태그
                            if (this.hasProvider('ms_clarity') && typeof clarity === 'function') {
                                clarity('set', 'scroll_depth', threshold.toString());
                            }
                        }
                    });
                }
            }, 250);

            window.addEventListener('scroll', trackScroll, { passive: true });
        }

        /**
         * 세션 시간 트래킹
         */
        setupSessionTracking() {
            setInterval(() => {
                const sessionDuration = Math.round((Date.now() - this.sessionData.startTime) / 1000);
                
                // 이탈 위험도 계산
                this.calculateBounceRisk(sessionDuration);

                // 5분마다 세션 데이터 업데이트
                if (sessionDuration % 300 === 0) {
                    this.queueData('session_heartbeat', {
                        duration: sessionDuration,
                        scrollDepth: this.sessionData.scrollDepth,
                        interactions: this.sessionData.interactions,
                        bounceRisk: this.sessionData.bounceRisk
                    });
                }
            }, 1000);
        }

        /**
         * 이탈 위험도 계산
         */
        calculateBounceRisk(sessionDuration) {
            let risk = 'low';

            // 30초 미만 체류 + 스크롤 없음 = 높은 이탈 위험
            if (sessionDuration < 30 && this.sessionData.scrollDepth < 25) {
                risk = 'high';
            }
            // 60초 미만 체류 + 상호작용 없음 = 중간 이탈 위험
            else if (sessionDuration < 60 && this.sessionData.interactions === 0) {
                risk = 'medium';
            }
            // Exit Intent 감지됨 = 높은 이탈 위험
            else if (this.sessionData.exitIntent) {
                risk = 'high';
            }

            this.sessionData.bounceRisk = risk;
        }

        /**
         * Exit Intent 트래킹
         */
        setupExitIntentTracking() {
            let exitIntentTriggered = false;

            document.addEventListener('mouseout', (e) => {
                if (e.clientY <= 0 && !exitIntentTriggered) {
                    exitIntentTriggered = true;
                    this.sessionData.exitIntent = true;

                    this.queueData('exit_intent', {
                        timestamp: Date.now(),
                        scrollDepth: this.sessionData.scrollDepth,
                        sessionDuration: Math.round((Date.now() - this.sessionData.startTime) / 1000)
                    });

                    // 커스텀 이벤트 발생
                    document.dispatchEvent(new CustomEvent('acf_nf_exit_intent', {
                        detail: { bounceRisk: this.sessionData.bounceRisk }
                    }));

                    // 5초 후 리셋
                    setTimeout(() => {
                        exitIntentTriggered = false;
                    }, 5000);
                }
            });
        }

        /**
         * 클릭 트래킹
         */
        setupClickTracking() {
            document.addEventListener('click', (e) => {
                this.sessionData.interactions++;

                const target = e.target.closest('a, button, [data-acf-nf-track]');
                if (!target) return;

                const trackData = {
                    element: target.tagName,
                    text: target.textContent?.substring(0, 50) || '',
                    href: target.href || '',
                    classes: target.className,
                    dataTrack: target.dataset.acfNfTrack || null,
                    timestamp: Date.now()
                };

                // CTA 버튼 클릭
                if (target.matches('.cta, .btn-cta, [data-cta], .add-to-cart, .checkout-button')) {
                    this.queueData('cta_click', trackData);
                    
                    if (this.hasProvider('google_analytics')) {
                        this.trackGA4Event('cta_click', {
                            button_text: trackData.text,
                            button_url: trackData.href
                        });
                    }
                }

                // 일반 클릭
                if (target.dataset.acfNfTrack) {
                    this.queueData('tracked_click', trackData);
                }
            });
        }

        /**
         * 폼 트래킹
         */
        setupFormTracking() {
            // 폼 시작
            document.querySelectorAll('form').forEach(form => {
                let formStarted = false;

                form.addEventListener('focusin', () => {
                    if (!formStarted) {
                        formStarted = true;
                        this.queueData('form_start', {
                            formId: form.id || 'unknown',
                            formAction: form.action,
                            timestamp: Date.now()
                        });
                    }
                });

                // 폼 제출
                form.addEventListener('submit', () => {
                    this.queueData('form_submit', {
                        formId: form.id || 'unknown',
                        formAction: form.action,
                        timestamp: Date.now()
                    });
                });
            });
        }

        /**
         * 넛지 이벤트 트래킹
         */
        trackNudgeEvent(eventType, detail) {
            const eventData = {
                templateId: detail.templateId || '',
                segmentCode: detail.segmentCode || '',
                rfmCode: detail.rfmCode || '',
                eventType: eventType,
                timestamp: Date.now()
            };

            this.queueData('nudge_' + eventType, eventData);

            // Google Analytics 전송
            if (this.hasProvider('google_analytics')) {
                this.trackGA4Event('nudge_' + eventType, eventData);
            }

            // Microsoft Clarity 태그
            if (this.hasProvider('ms_clarity') && typeof clarity === 'function') {
                clarity('set', 'nudge_' + eventType, eventData.templateId);
            }

            // Hotjar 이벤트
            if (this.hasProvider('hotjar') && typeof hj === 'function') {
                hj('event', 'nudge_' + eventType);
            }
        }

        /**
         * GA4 이벤트 전송
         */
        trackGA4Event(eventName, params) {
            if (typeof gtag === 'function') {
                gtag('event', eventName, {
                    ...params,
                    send_to: 'acf_nudge_flow'
                });
            } else if (typeof ga === 'function') {
                ga('send', 'event', 'ACF_Nudge_Flow', eventName, JSON.stringify(params));
            }
        }

        /**
         * 데이터 큐잉
         */
        queueData(eventType, data) {
            this.dataQueue.push({
                event_type: eventType,
                data: data,
                user_id: this.config.userId || 0,
                visitor_id: this.getVisitorId(),
                session_id: this.getSessionId(),
                page_url: window.location.href,
                referrer: document.referrer,
                user_agent: navigator.userAgent,
                screen_size: `${window.screen.width}x${window.screen.height}`,
                viewport_size: `${window.innerWidth}x${window.innerHeight}`,
                device_type: this.detectDeviceType(),
                active_providers: this.activeProviders,
                timestamp: Date.now()
            });
        }

        /**
         * 데이터 동기화 시작
         */
        startDataSync() {
            setInterval(() => {
                this.sendQueuedData();
            }, this.sendInterval);
        }

        /**
         * 큐잉된 데이터 전송
         */
        sendQueuedData() {
            if (this.dataQueue.length === 0) return;

            const dataToSend = [...this.dataQueue];
            this.dataQueue = [];

            this.sendToServer(dataToSend);
        }

        /**
         * 즉시 데이터 전송 (페이지 언로드 시)
         */
        sendDataImmediately() {
            if (this.dataQueue.length === 0) return;

            // Beacon API 사용 (페이지 언로드 시에도 전송 보장)
            if (navigator.sendBeacon) {
                const payload = JSON.stringify({
                    action: 'acf_nf_save_analytics',
                    nonce: this.config.nonce || '',
                    analytics_data: this.dataQueue,
                    session_summary: this.sessionData
                });

                navigator.sendBeacon(
                    this.config.ajaxUrl || '/wp-admin/admin-ajax.php',
                    new Blob([`action=acf_nf_save_analytics&nonce=${this.config.nonce}&data=${encodeURIComponent(payload)}`], {
                        type: 'application/x-www-form-urlencoded'
                    })
                );
            } else {
                // Fallback to sync XHR
                this.sendToServer(this.dataQueue, true);
            }

            this.dataQueue = [];
        }

        /**
         * 서버로 데이터 전송
         */
        sendToServer(data, sync = false) {
            const formData = new FormData();
            formData.append('action', 'acf_nf_save_analytics');
            formData.append('nonce', this.config.nonce || '');
            formData.append('analytics_data', JSON.stringify(data));
            formData.append('session_summary', JSON.stringify(this.sessionData));

            if (sync) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', this.config.ajaxUrl || '/wp-admin/admin-ajax.php', false);
                xhr.send(formData);
            } else {
                fetch(this.config.ajaxUrl || '/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }).catch(err => {
                    console.warn('[ACF NF Analytics] Failed to send data:', err);
                    // 실패한 데이터 다시 큐에 추가
                    this.dataQueue = [...data, ...this.dataQueue];
                });
            }
        }

        /**
         * 방문자 ID 조회/생성
         */
        getVisitorId() {
            let visitorId = this.getCookie('acf_nf_visitor_id');
            
            if (!visitorId) {
                visitorId = 'v_' + this.generateUUID();
                this.setCookie('acf_nf_visitor_id', visitorId, 365);
            }

            return visitorId;
        }

        /**
         * 세션 ID 조회/생성
         */
        getSessionId() {
            let sessionId = sessionStorage.getItem('acf_nf_session_id');
            
            if (!sessionId) {
                sessionId = 's_' + this.generateUUID();
                sessionStorage.setItem('acf_nf_session_id', sessionId);
            }

            return sessionId;
        }

        /**
         * UUID 생성
         */
        generateUUID() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                const r = Math.random() * 16 | 0;
                const v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        /**
         * 디바이스 타입 감지
         */
        detectDeviceType() {
            const ua = navigator.userAgent;
            if (/tablet|ipad|playbook|silk/i.test(ua)) return 'tablet';
            if (/mobile|iphone|ipod|android|blackberry|opera|mini|windows\sce|palm|smartphone|iemobile/i.test(ua)) return 'mobile';
            return 'desktop';
        }

        /**
         * 제공자 확인
         */
        hasProvider(provider) {
            return this.activeProviders.includes(provider);
        }

        /**
         * 쿠키 설정
         */
        setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Lax`;
        }

        /**
         * 쿠키 조회
         */
        getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }

        /**
         * Throttle 함수
         */
        throttle(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        }

        /**
         * 현재 세션 데이터 조회
         */
        getSessionData() {
            return {
                ...this.sessionData,
                duration: Math.round((Date.now() - this.sessionData.startTime) / 1000),
                activeProviders: this.activeProviders
            };
        }

        /**
         * 커스텀 이벤트 트래킹
         */
        trackCustomEvent(eventName, eventData = {}) {
            this.queueData('custom_' + eventName, eventData);

            if (this.hasProvider('google_analytics')) {
                this.trackGA4Event(eventName, eventData);
            }
        }

        /**
         * 전자상거래 이벤트 트래킹
         */
        trackEcommerceEvent(eventName, items, value = 0, currency = 'KRW') {
            const eventData = {
                event_name: eventName,
                items: items,
                value: value,
                currency: currency,
                timestamp: Date.now()
            };

            this.queueData('ecommerce_' + eventName, eventData);

            // GA4 전자상거래 이벤트
            if (this.hasProvider('google_analytics') && typeof gtag === 'function') {
                gtag('event', eventName, {
                    items: items,
                    value: value,
                    currency: currency
                });
            }

            // Facebook Pixel
            if (this.hasProvider('facebook_pixel') && typeof fbq === 'function') {
                const fbEventMap = {
                    'view_item': 'ViewContent',
                    'add_to_cart': 'AddToCart',
                    'begin_checkout': 'InitiateCheckout',
                    'purchase': 'Purchase'
                };

                if (fbEventMap[eventName]) {
                    fbq('track', fbEventMap[eventName], {
                        content_ids: items.map(i => i.item_id),
                        content_type: 'product',
                        value: value,
                        currency: currency
                    });
                }
            }
        }
    }

    /**
     * 넛지 추적 헬퍼
     */
    class NudgeTracker {
        constructor(analytics) {
            this.analytics = analytics;
        }

        /**
         * 넛지 표시 추적
         */
        displayed(templateId, segmentCode = '', rfmCode = '') {
            document.dispatchEvent(new CustomEvent('acf_nf_nudge_displayed', {
                detail: { templateId, segmentCode, rfmCode }
            }));
        }

        /**
         * 넛지 클릭 추적
         */
        clicked(templateId, segmentCode = '', rfmCode = '') {
            document.dispatchEvent(new CustomEvent('acf_nf_nudge_clicked', {
                detail: { templateId, segmentCode, rfmCode }
            }));
        }

        /**
         * 넛지 닫기 추적
         */
        dismissed(templateId, segmentCode = '', rfmCode = '') {
            document.dispatchEvent(new CustomEvent('acf_nf_nudge_dismissed', {
                detail: { templateId, segmentCode, rfmCode }
            }));
        }

        /**
         * 넛지 전환 추적
         */
        converted(templateId, segmentCode = '', rfmCode = '', conversionValue = 0) {
            document.dispatchEvent(new CustomEvent('acf_nf_nudge_converted', {
                detail: { templateId, segmentCode, rfmCode, conversionValue }
            }));
        }
    }

    // DOM Ready 시 초기화
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAnalytics);
    } else {
        initAnalytics();
    }

    function initAnalytics() {
        const analytics = new AnalyticsIntegration();
        const nudgeTracker = new NudgeTracker(analytics);

        // 전역 접근
        window.ACF_NF_Analytics = analytics;
        window.ACF_NF_NudgeTracker = nudgeTracker;

        // 초기화 완료 이벤트
        document.dispatchEvent(new CustomEvent('acf_nf_analytics_ready', {
            detail: { analytics, nudgeTracker }
        }));
    }

})(window, document);
