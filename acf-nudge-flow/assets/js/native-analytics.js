/**
 * ACF Nudge Flow - Native Analytics System
 * 
 * 자체 애널리틱스 시스템 - 외부 도구 없이도 Clarity/GA 수준의 분석 제공
 * - 히트맵 (클릭/터치/호버/레이지 클릭/데드 클릭)
 * - 스크롤 맵 (구간별 체류 시간, 읽기 패턴)
 * - 유저 저니 맵 (페이지 이동 경로, 전환 퍼널)
 * - 이벤트 기반 분석 (커스텀 이벤트, 인터랙션)
 * 
 * @package ACF_Nudge_Flow
 * @since 22.6.0
 */

(function(window, document) {
    'use strict';

    // 전역 네임스페이스
    window.ACF_NF_NativeAnalytics = window.ACF_NF_NativeAnalytics || {};

    /**
     * 네이티브 애널리틱스 메인 클래스
     */
    class NativeAnalytics {
        constructor() {
            this.config = window.acf_nf_native_analytics_config || {};
            this.sessionId = this.getSessionId();
            this.visitorId = this.getVisitorId();
            this.userId = this.config.userId || 0;
            
            // 모듈 인스턴스
            this.heatmap = null;
            this.scrollmap = null;
            this.journey = null;
            this.events = null;

            // 데이터 큐
            this.dataQueue = {
                heatmap: [],
                scroll: [],
                journey: [],
                events: []
            };

            // 전송 설정
            this.sendInterval = 10000; // 10초
            this.maxQueueSize = 100;

            this.init();
        }

        /**
         * 초기화
         */
        init() {
            // 모듈 초기화
            this.heatmap = new HeatmapTracker(this);
            this.scrollmap = new ScrollmapTracker(this);
            this.journey = new JourneyTracker(this);
            this.events = new EventTracker(this);

            // 세션 시작
            this.startSession();

            // 데이터 전송 스케줄러
            this.startDataSync();

            // 페이지 언로드 핸들링
            this.setupUnloadHandler();

            console.log('[ACF NF Native Analytics] Initialized', {
                sessionId: this.sessionId,
                visitorId: this.visitorId
            });
        }

        /**
         * 세션 시작
         */
        startSession() {
            const sessionData = {
                session_id: this.sessionId,
                visitor_id: this.visitorId,
                user_id: this.userId,
                landing_page: window.location.href,
                referrer: document.referrer,
                traffic_source: this.detectTrafficSource(),
                utm_source: this.getUrlParam('utm_source'),
                utm_medium: this.getUrlParam('utm_medium'),
                utm_campaign: this.getUrlParam('utm_campaign'),
                device_type: this.detectDeviceType(),
                browser: this.detectBrowser(),
                os: this.detectOS(),
                screen_resolution: `${window.screen.width}x${window.screen.height}`
            };

            this.sendToServer('start_session', sessionData);
        }

        /**
         * 데이터 전송 시작
         */
        startDataSync() {
            setInterval(() => {
                this.flushQueues();
            }, this.sendInterval);
        }

        /**
         * 페이지 언로드 핸들러
         */
        setupUnloadHandler() {
            const handleUnload = () => {
                // 세션 종료 데이터 수집
                const sessionEndData = {
                    session_id: this.sessionId,
                    duration: Math.round((Date.now() - this.sessionStartTime) / 1000),
                    exit_page: window.location.href,
                    is_bounce: this.journey.pageCount <= 1,
                    has_conversion: this.journey.hasConversion,
                    conversion_count: this.journey.conversionCount,
                    conversion_value: this.journey.conversionValue,
                    journey_summary: this.journey.getSummary()
                };

                // 남은 데이터 모두 전송
                this.flushQueues(true);
                this.sendBeacon('end_session', sessionEndData);
            };

            window.addEventListener('beforeunload', handleUnload);
            window.addEventListener('pagehide', handleUnload);
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'hidden') {
                    this.flushQueues(true);
                }
            });
        }

        /**
         * 큐 비우기 (데이터 전송)
         */
        flushQueues(immediate = false) {
            // 히트맵 데이터
            if (this.dataQueue.heatmap.length > 0) {
                const heatmapData = [...this.dataQueue.heatmap];
                this.dataQueue.heatmap = [];
                this.sendToServer('record_heatmap', { heatmap: heatmapData }, immediate);
            }

            // 스크롤 데이터
            if (this.dataQueue.scroll.length > 0) {
                const scrollData = this.dataQueue.scroll[this.dataQueue.scroll.length - 1];
                this.dataQueue.scroll = [];
                this.sendToServer('record_scroll', { scroll: scrollData }, immediate);
            }

            // 저니 데이터
            if (this.dataQueue.journey.length > 0) {
                const journeyData = [...this.dataQueue.journey];
                this.dataQueue.journey = [];
                journeyData.forEach(step => {
                    this.sendToServer('record_journey', { journey: step }, immediate);
                });
            }

            // 이벤트 데이터
            if (this.dataQueue.events.length > 0) {
                const eventsData = [...this.dataQueue.events];
                this.dataQueue.events = [];
                eventsData.forEach(event => {
                    this.sendToServer('record_event', { event: event }, immediate);
                });
            }
        }

        /**
         * 큐에 데이터 추가
         */
        queueData(type, data) {
            if (this.dataQueue[type]) {
                this.dataQueue[type].push(data);

                // 최대 크기 초과 시 자동 전송
                if (this.dataQueue[type].length >= this.maxQueueSize) {
                    this.flushQueues();
                }
            }
        }

        /**
         * 서버로 데이터 전송
         */
        sendToServer(action, data, immediate = false) {
            const formData = new FormData();
            formData.append('action', 'acf_nf_' + action);
            formData.append('nonce', this.config.nonce || '');
            
            Object.keys(data).forEach(key => {
                if (typeof data[key] === 'object') {
                    formData.append(key, JSON.stringify(data[key]));
                } else {
                    formData.append(key, data[key]);
                }
            });

            if (immediate && navigator.sendBeacon) {
                this.sendBeacon(action, data);
            } else {
                fetch(this.config.ajaxUrl || '/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }).catch(err => {
                    console.warn('[ACF NF Analytics] Send failed:', err);
                });
            }
        }

        /**
         * Beacon API로 전송
         */
        sendBeacon(action, data) {
            if (!navigator.sendBeacon) return false;

            const payload = new URLSearchParams();
            payload.append('action', 'acf_nf_' + action);
            payload.append('nonce', this.config.nonce || '');
            
            Object.keys(data).forEach(key => {
                if (typeof data[key] === 'object') {
                    payload.append(key, JSON.stringify(data[key]));
                } else {
                    payload.append(key, data[key]);
                }
            });

            return navigator.sendBeacon(
                this.config.ajaxUrl || '/wp-admin/admin-ajax.php',
                payload
            );
        }

        // ==========================================
        // 유틸리티 메서드
        // ==========================================

        getSessionId() {
            let sessionId = sessionStorage.getItem('acf_nf_session_id');
            if (!sessionId) {
                sessionId = 's_' + this.generateUUID();
                sessionStorage.setItem('acf_nf_session_id', sessionId);
                this.sessionStartTime = Date.now();
                sessionStorage.setItem('acf_nf_session_start', this.sessionStartTime);
            } else {
                this.sessionStartTime = parseInt(sessionStorage.getItem('acf_nf_session_start')) || Date.now();
            }
            return sessionId;
        }

        getVisitorId() {
            let visitorId = this.getCookie('acf_nf_visitor_id');
            if (!visitorId) {
                visitorId = 'v_' + this.generateUUID();
                this.setCookie('acf_nf_visitor_id', visitorId, 365);
            }
            return visitorId;
        }

        generateUUID() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                const r = Math.random() * 16 | 0;
                const v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }

        setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Lax`;
        }

        getUrlParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param) || '';
        }

        detectTrafficSource() {
            const referrer = document.referrer;
            if (!referrer) return 'direct';
            
            const hostname = new URL(referrer).hostname;
            if (hostname.includes('google')) return 'google';
            if (hostname.includes('facebook') || hostname.includes('fb.')) return 'facebook';
            if (hostname.includes('instagram')) return 'instagram';
            if (hostname.includes('twitter') || hostname.includes('t.co')) return 'twitter';
            if (hostname.includes('linkedin')) return 'linkedin';
            if (hostname.includes('youtube')) return 'youtube';
            if (hostname.includes('naver')) return 'naver';
            if (hostname.includes('daum')) return 'daum';
            if (hostname === window.location.hostname) return 'internal';
            
            return 'referral';
        }

        detectDeviceType() {
            const ua = navigator.userAgent;
            if (/tablet|ipad|playbook|silk/i.test(ua)) return 'tablet';
            if (/mobile|iphone|ipod|android|blackberry|opera mini|iemobile/i.test(ua)) return 'mobile';
            return 'desktop';
        }

        detectBrowser() {
            const ua = navigator.userAgent;
            if (ua.includes('Chrome') && !ua.includes('Edg')) return 'Chrome';
            if (ua.includes('Firefox')) return 'Firefox';
            if (ua.includes('Safari') && !ua.includes('Chrome')) return 'Safari';
            if (ua.includes('Edg')) return 'Edge';
            if (ua.includes('MSIE') || ua.includes('Trident')) return 'IE';
            return 'Other';
        }

        detectOS() {
            const ua = navigator.userAgent;
            if (ua.includes('Windows')) return 'Windows';
            if (ua.includes('Mac')) return 'macOS';
            if (ua.includes('Linux')) return 'Linux';
            if (ua.includes('Android')) return 'Android';
            if (ua.includes('iOS') || ua.includes('iPhone') || ua.includes('iPad')) return 'iOS';
            return 'Other';
        }

        getBaseData() {
            return {
                session_id: this.sessionId,
                visitor_id: this.visitorId,
                user_id: this.userId,
                page_url: window.location.href,
                page_title: document.title,
                device_type: this.detectDeviceType(),
                viewport_width: window.innerWidth,
                viewport_height: window.innerHeight
            };
        }
    }

    /**
     * 히트맵 트래커
     */
    class HeatmapTracker {
        constructor(analytics) {
            this.analytics = analytics;
            this.clickBuffer = [];
            this.lastClickTime = 0;
            this.rageClickThreshold = 3; // 연속 클릭 횟수
            this.rageClickTimeWindow = 1000; // 1초 내
            
            this.init();
        }

        init() {
            // 클릭 트래킹
            document.addEventListener('click', (e) => this.trackClick(e), true);
            
            // 터치 트래킹 (모바일)
            document.addEventListener('touchend', (e) => this.trackTouch(e), true);
            
            // 호버 트래킹 (데스크톱)
            if (this.analytics.detectDeviceType() === 'desktop') {
                this.setupHoverTracking();
            }
        }

        trackClick(e) {
            const now = Date.now();
            const point = this.createHeatmapPoint(e, 'click');

            // 레이지 클릭 감지
            this.clickBuffer.push({ time: now, x: e.clientX, y: e.clientY });
            this.clickBuffer = this.clickBuffer.filter(c => now - c.time < this.rageClickTimeWindow);

            if (this.clickBuffer.length >= this.rageClickThreshold) {
                // 같은 위치에서 빠른 연속 클릭 = 레이지 클릭
                const sameArea = this.clickBuffer.every(c => 
                    Math.abs(c.x - e.clientX) < 50 && Math.abs(c.y - e.clientY) < 50
                );
                if (sameArea) {
                    point.interaction_type = 'rage_click';
                }
                this.clickBuffer = [];
            }

            // 데드 클릭 감지 (클릭 가능하지 않은 요소 클릭)
            if (!this.isClickable(e.target) && point.interaction_type === 'click') {
                point.interaction_type = 'dead_click';
            }

            this.analytics.queueData('heatmap', point);
            this.lastClickTime = now;
        }

        trackTouch(e) {
            if (e.changedTouches && e.changedTouches.length > 0) {
                const touch = e.changedTouches[0];
                const point = this.createHeatmapPoint({
                    clientX: touch.clientX,
                    clientY: touch.clientY,
                    target: e.target
                }, 'touch');
                
                this.analytics.queueData('heatmap', point);
            }
        }

        setupHoverTracking() {
            let hoverStart = 0;
            let hoverElement = null;

            document.addEventListener('mouseover', (e) => {
                if (e.target !== hoverElement) {
                    // 이전 호버 기록
                    if (hoverElement && hoverStart) {
                        const duration = Date.now() - hoverStart;
                        if (duration > 1000) { // 1초 이상 호버만 기록
                            const rect = hoverElement.getBoundingClientRect();
                            const point = {
                                ...this.analytics.getBaseData(),
                                interaction_type: 'hover',
                                x_absolute: Math.round(rect.left + rect.width / 2),
                                y_absolute: Math.round(rect.top + rect.height / 2 + window.scrollY),
                                x_relative: (rect.left + rect.width / 2) / document.documentElement.scrollWidth,
                                y_relative: (rect.top + rect.height / 2 + window.scrollY) / document.documentElement.scrollHeight,
                                page_width: document.documentElement.scrollWidth,
                                page_height: document.documentElement.scrollHeight,
                                element_selector: this.getSelector(hoverElement),
                                element_tag: hoverElement.tagName,
                                element_text: hoverElement.textContent?.substring(0, 255) || '',
                                hover_duration: duration,
                                is_clickable: this.isClickable(hoverElement) ? 1 : 0
                            };
                            this.analytics.queueData('heatmap', point);
                        }
                    }

                    hoverElement = e.target;
                    hoverStart = Date.now();
                }
            });
        }

        createHeatmapPoint(e, type) {
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const pageHeight = Math.max(
                document.body.scrollHeight,
                document.documentElement.scrollHeight
            );
            const pageWidth = Math.max(
                document.body.scrollWidth,
                document.documentElement.scrollWidth
            );

            return {
                ...this.analytics.getBaseData(),
                interaction_type: type,
                x_absolute: Math.round(e.clientX),
                y_absolute: Math.round(e.clientY + scrollTop),
                x_relative: e.clientX / pageWidth,
                y_relative: (e.clientY + scrollTop) / pageHeight,
                page_width: pageWidth,
                page_height: pageHeight,
                element_selector: this.getSelector(e.target),
                element_tag: e.target.tagName,
                element_text: e.target.textContent?.substring(0, 255) || '',
                is_clickable: this.isClickable(e.target) ? 1 : 0
            };
        }

        getSelector(element) {
            if (!element || element === document.body) return 'body';
            
            const parts = [];
            while (element && element !== document.body && parts.length < 5) {
                let selector = element.tagName.toLowerCase();
                
                if (element.id) {
                    selector += '#' + element.id;
                    parts.unshift(selector);
                    break;
                }
                
                if (element.className && typeof element.className === 'string') {
                    const classes = element.className.trim().split(/\s+/).slice(0, 2);
                    if (classes.length > 0 && classes[0]) {
                        selector += '.' + classes.join('.');
                    }
                }
                
                parts.unshift(selector);
                element = element.parentElement;
            }
            
            return parts.join(' > ');
        }

        isClickable(element) {
            if (!element) return false;
            
            const clickableTags = ['A', 'BUTTON', 'INPUT', 'SELECT', 'TEXTAREA', 'LABEL'];
            if (clickableTags.includes(element.tagName)) return true;
            
            const clickableRoles = ['button', 'link', 'checkbox', 'radio', 'tab', 'menuitem'];
            if (clickableRoles.includes(element.getAttribute('role'))) return true;
            
            if (element.onclick || element.hasAttribute('onclick')) return true;
            
            const style = window.getComputedStyle(element);
            if (style.cursor === 'pointer') return true;
            
            return false;
        }
    }

    /**
     * 스크롤맵 트래커
     */
    class ScrollmapTracker {
        constructor(analytics) {
            this.analytics = analytics;
            this.maxScrollDepth = 0;
            this.maxScrollPercent = 0;
            this.scrollSections = {}; // 구간별 체류 시간
            this.currentSection = 0;
            this.sectionStartTime = Date.now();
            this.scrollUps = 0;
            this.scrollDowns = 0;
            this.lastScrollTop = 0;
            this.scrollSpeeds = [];
            this.lastScrollTime = Date.now();
            this.readingStartTime = Date.now();
            this.isReading = false;
            this.attentionTime = 0;
            
            this.init();
        }

        init() {
            // 스크롤 이벤트
            window.addEventListener('scroll', this.throttle(() => this.trackScroll(), 100), { passive: true });
            
            // 페이지 가시성 변경 (탭 전환 등)
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'hidden') {
                    this.saveScrollData();
                }
            });

            // 읽기 패턴 감지 (마우스 움직임)
            if (this.analytics.detectDeviceType() === 'desktop') {
                document.addEventListener('mousemove', this.throttle(() => {
                    this.isReading = true;
                    this.readingStartTime = Date.now();
                }, 1000));
            }

            // 30초마다 스크롤 데이터 저장
            setInterval(() => this.saveScrollData(), 30000);
        }

        trackScroll() {
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const docHeight = Math.max(
                document.body.scrollHeight,
                document.documentElement.scrollHeight
            ) - windowHeight;
            
            const scrollPercent = docHeight > 0 ? Math.round((scrollTop / docHeight) * 100) : 0;
            const now = Date.now();

            // 최대 스크롤 깊이 업데이트
            if (scrollPercent > this.maxScrollPercent) {
                this.maxScrollPercent = scrollPercent;
                this.maxScrollDepth = scrollTop;
            }

            // 스크롤 방향
            if (scrollTop > this.lastScrollTop) {
                this.scrollDowns++;
            } else if (scrollTop < this.lastScrollTop) {
                this.scrollUps++;
            }

            // 스크롤 속도 계산
            const timeDiff = now - this.lastScrollTime;
            if (timeDiff > 0) {
                const speed = Math.abs(scrollTop - this.lastScrollTop) / timeDiff;
                this.scrollSpeeds.push(speed);
                if (this.scrollSpeeds.length > 100) this.scrollSpeeds.shift();
            }

            // 구간별 체류 시간 (10% 단위)
            const newSection = Math.floor(scrollPercent / 10) * 10;
            if (newSection !== this.currentSection) {
                const sectionKey = `${this.currentSection}-${this.currentSection + 10}`;
                this.scrollSections[sectionKey] = (this.scrollSections[sectionKey] || 0) + (now - this.sectionStartTime);
                this.currentSection = newSection;
                this.sectionStartTime = now;
            }

            // Attention time (읽기 중일 때만)
            if (this.isReading && (now - this.readingStartTime) > 2000) {
                this.attentionTime += now - this.lastScrollTime;
            }

            this.lastScrollTop = scrollTop;
            this.lastScrollTime = now;
        }

        saveScrollData() {
            // 현재 구간 시간 업데이트
            const now = Date.now();
            const sectionKey = `${this.currentSection}-${this.currentSection + 10}`;
            this.scrollSections[sectionKey] = (this.scrollSections[sectionKey] || 0) + (now - this.sectionStartTime);
            this.sectionStartTime = now;

            const scrollData = {
                ...this.analytics.getBaseData(),
                page_height: Math.max(document.body.scrollHeight, document.documentElement.scrollHeight),
                max_scroll_depth: this.maxScrollDepth,
                max_scroll_percent: this.maxScrollPercent,
                scroll_sections_time: this.scrollSections,
                fold_line: window.innerHeight,
                below_fold_time: this.getBelowFoldTime(),
                scroll_ups: this.scrollUps,
                scroll_downs: this.scrollDowns,
                scroll_speed_avg: this.getAverageScrollSpeed(),
                reading_time: Math.round((Date.now() - this.readingStartTime) / 1000),
                attention_time: Math.round(this.attentionTime / 1000)
            };

            this.analytics.queueData('scroll', scrollData);
        }

        getBelowFoldTime() {
            let belowFoldTime = 0;
            Object.keys(this.scrollSections).forEach(key => {
                const [start] = key.split('-').map(Number);
                // fold 아래 (대략 첫 화면 이후)
                if (start >= 10) {
                    belowFoldTime += this.scrollSections[key];
                }
            });
            return Math.round(belowFoldTime / 1000);
        }

        getAverageScrollSpeed() {
            if (this.scrollSpeeds.length === 0) return 0;
            return this.scrollSpeeds.reduce((a, b) => a + b, 0) / this.scrollSpeeds.length;
        }

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

        getData() {
            return {
                maxScrollPercent: this.maxScrollPercent,
                maxScrollDepth: this.maxScrollDepth,
                scrollSections: this.scrollSections,
                scrollUps: this.scrollUps,
                scrollDowns: this.scrollDowns
            };
        }
    }

    /**
     * 유저 저니 트래커
     */
    class JourneyTracker {
        constructor(analytics) {
            this.analytics = analytics;
            this.pageCount = 0;
            this.currentPageData = null;
            this.pageStartTime = Date.now();
            this.interactionsCount = 0;
            this.hasConversion = false;
            this.conversionCount = 0;
            this.conversionValue = 0;
            this.exitIntentDetected = false;
            this.journeyPath = [];
            
            this.init();
        }

        init() {
            // 페이지 진입 기록
            this.recordPageEntry();

            // 인터랙션 카운트
            document.addEventListener('click', () => this.interactionsCount++);

            // Exit Intent 감지
            document.addEventListener('mouseout', (e) => {
                if (e.clientY <= 0 && !this.exitIntentDetected) {
                    this.exitIntentDetected = true;
                }
            });

            // 페이지 이동 감지 (SPA 지원)
            this.setupNavigationTracking();
        }

        recordPageEntry() {
            this.pageCount++;
            this.pageStartTime = Date.now();
            this.interactionsCount = 0;
            this.exitIntentDetected = false;

            this.currentPageData = {
                ...this.analytics.getBaseData(),
                step_number: this.pageCount,
                page_type: this.detectPageType(),
                entry_source: this.pageCount === 1 ? this.analytics.detectTrafficSource() : 'internal',
                referrer: document.referrer,
                utm_source: this.analytics.getUrlParam('utm_source'),
                utm_medium: this.analytics.getUrlParam('utm_medium'),
                utm_campaign: this.analytics.getUrlParam('utm_campaign'),
                entered_at: new Date().toISOString()
            };

            this.journeyPath.push({
                url: window.location.href,
                title: document.title,
                type: this.currentPageData.page_type,
                enteredAt: Date.now()
            });
        }

        recordPageExit(nextPage = '', exitType = 'navigate') {
            if (!this.currentPageData) return;

            const exitData = {
                ...this.currentPageData,
                time_on_page: Math.round((Date.now() - this.pageStartTime) / 1000),
                scroll_depth: this.analytics.scrollmap?.maxScrollPercent || 0,
                interactions_count: this.interactionsCount,
                exit_type: exitType,
                next_page: nextPage,
                is_bounce: this.pageCount === 1 && (Date.now() - this.pageStartTime) < 10000,
                exit_intent_detected: this.exitIntentDetected,
                exited_at: new Date().toISOString()
            };

            // 마지막 저니 항목 업데이트
            if (this.journeyPath.length > 0) {
                const lastItem = this.journeyPath[this.journeyPath.length - 1];
                lastItem.timeOnPage = exitData.time_on_page;
                lastItem.scrollDepth = exitData.scroll_depth;
                lastItem.interactions = this.interactionsCount;
            }

            this.analytics.queueData('journey', exitData);
        }

        setupNavigationTracking() {
            // 링크 클릭 감지
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && link.href && !link.href.startsWith('javascript:')) {
                    const isExternal = new URL(link.href).hostname !== window.location.hostname;
                    this.recordPageExit(link.href, isExternal ? 'external' : 'navigate');
                }
            });

            // 브라우저 뒤로가기/앞으로가기
            window.addEventListener('popstate', () => {
                this.recordPageExit('', 'back');
                this.recordPageEntry();
            });

            // History API (SPA)
            const originalPushState = history.pushState;
            const originalReplaceState = history.replaceState;
            
            history.pushState = (...args) => {
                this.recordPageExit(args[2] || '', 'navigate');
                originalPushState.apply(history, args);
                this.recordPageEntry();
            };

            history.replaceState = (...args) => {
                originalReplaceState.apply(history, args);
            };
        }

        detectPageType() {
            const path = window.location.pathname;
            const url = window.location.href;

            // WooCommerce 페이지
            if (document.body.classList.contains('woocommerce')) {
                if (document.body.classList.contains('single-product')) return 'product';
                if (document.body.classList.contains('woocommerce-cart')) return 'cart';
                if (document.body.classList.contains('woocommerce-checkout')) return 'checkout';
                if (document.body.classList.contains('woocommerce-account')) return 'account';
                if (document.body.classList.contains('archive')) return 'category';
            }

            // 일반 페이지
            if (path === '/' || path === '') return 'home';
            if (path.includes('/blog') || document.body.classList.contains('blog')) return 'blog';
            if (document.body.classList.contains('single-post')) return 'post';
            if (document.body.classList.contains('page')) return 'page';
            if (path.includes('/search') || url.includes('?s=')) return 'search';
            if (path.includes('/contact')) return 'contact';
            if (path.includes('/about')) return 'about';

            return 'other';
        }

        /**
         * 전환 기록
         */
        recordConversion(type, value = 0) {
            this.hasConversion = true;
            this.conversionCount++;
            this.conversionValue += value;

            if (this.currentPageData) {
                this.currentPageData.is_conversion = true;
                this.currentPageData.conversion_type = type;
                this.currentPageData.conversion_value = value;
            }

            // 이벤트로도 기록
            this.analytics.events.track('conversion', {
                conversion_type: type,
                conversion_value: value,
                page_type: this.currentPageData?.page_type || 'unknown'
            });
        }

        /**
         * 저니 요약
         */
        getSummary() {
            return {
                total_pages: this.pageCount,
                path: this.journeyPath.map(p => ({
                    url: p.url,
                    type: p.type,
                    time: p.timeOnPage || 0
                })),
                total_time: Math.round((Date.now() - parseInt(sessionStorage.getItem('acf_nf_session_start'))) / 1000),
                conversions: this.conversionCount,
                conversion_value: this.conversionValue
            };
        }
    }

    /**
     * 이벤트 트래커
     */
    class EventTracker {
        constructor(analytics) {
            this.analytics = analytics;
            this.eventQueue = [];
            
            this.init();
        }

        init() {
            // 자동 이벤트 트래킹 설정
            this.setupAutoTracking();
        }

        /**
         * 커스텀 이벤트 트래킹
         */
        track(eventName, eventData = {}) {
            const event = {
                ...this.analytics.getBaseData(),
                event_name: eventName,
                event_category: eventData.category || 'general',
                event_action: eventData.action || eventName,
                event_label: eventData.label || '',
                event_value: eventData.value || 0,
                page_type: this.analytics.journey?.currentPageData?.page_type || 'unknown',
                time_on_page: Math.round((Date.now() - this.analytics.journey?.pageStartTime || Date.now()) / 1000),
                scroll_depth: this.analytics.scrollmap?.maxScrollPercent || 0,
                timestamp_ms: Date.now(),
                custom_data: eventData.custom || null
            };

            // 요소 관련 데이터
            if (eventData.element) {
                event.element_type = eventData.element.tagName;
                event.element_id = eventData.element.id || '';
                event.element_class = eventData.element.className || '';
                event.element_text = eventData.element.textContent?.substring(0, 500) || '';
                event.element_href = eventData.element.href || '';
            }

            // 위치 데이터
            if (eventData.position) {
                event.position_x = eventData.position.x;
                event.position_y = eventData.position.y;
            }

            this.analytics.queueData('events', event);
        }

        /**
         * 자동 이벤트 트래킹 설정
         */
        setupAutoTracking() {
            // 폼 제출
            document.addEventListener('submit', (e) => {
                const form = e.target;
                this.track('form_submit', {
                    category: 'form',
                    label: form.id || form.action || 'unknown',
                    element: form
                });
            });

            // 비디오 이벤트
            document.querySelectorAll('video').forEach(video => {
                video.addEventListener('play', () => {
                    this.track('video_play', {
                        category: 'video',
                        label: video.src || video.currentSrc
                    });
                });

                video.addEventListener('pause', () => {
                    this.track('video_pause', {
                        category: 'video',
                        label: video.src || video.currentSrc,
                        value: Math.round(video.currentTime)
                    });
                });

                video.addEventListener('ended', () => {
                    this.track('video_complete', {
                        category: 'video',
                        label: video.src || video.currentSrc
                    });
                });
            });

            // 파일 다운로드
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && link.href) {
                    const downloadExtensions = ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.zip', '.rar'];
                    const isDownload = downloadExtensions.some(ext => link.href.toLowerCase().includes(ext));
                    
                    if (isDownload) {
                        this.track('file_download', {
                            category: 'download',
                            label: link.href,
                            element: link
                        });
                    }
                }
            });

            // 외부 링크 클릭
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && link.href) {
                    try {
                        const linkUrl = new URL(link.href);
                        if (linkUrl.hostname !== window.location.hostname) {
                            this.track('outbound_link', {
                                category: 'outbound',
                                label: link.href,
                                element: link
                            });
                        }
                    } catch (err) {}
                }
            });

            // CTA 버튼 클릭
            document.addEventListener('click', (e) => {
                const cta = e.target.closest('.cta, .btn-cta, [data-cta], .add-to-cart, .checkout-button, .buy-now');
                if (cta) {
                    this.track('cta_click', {
                        category: 'cta',
                        label: cta.textContent?.substring(0, 100) || '',
                        element: cta
                    });
                }
            });

            // 검색
            document.querySelectorAll('input[type="search"], input[name="s"], input[name="q"]').forEach(input => {
                input.closest('form')?.addEventListener('submit', () => {
                    this.track('search', {
                        category: 'search',
                        label: input.value
                    });
                });
            });

            // 에러 트래킹
            window.addEventListener('error', (e) => {
                this.track('js_error', {
                    category: 'error',
                    label: e.message,
                    custom: {
                        filename: e.filename,
                        lineno: e.lineno,
                        colno: e.colno
                    }
                });
            });
        }
    }

    // ==========================================
    // 초기화
    // ==========================================

    function initNativeAnalytics() {
        const analytics = new NativeAnalytics();
        
        // 전역 접근
        window.ACF_NF_NativeAnalytics = analytics;

        // 초기화 완료 이벤트
        document.dispatchEvent(new CustomEvent('acf_nf_native_analytics_ready', {
            detail: { analytics }
        }));

        // 공개 API
        window.acfNFTrack = (eventName, eventData) => analytics.events.track(eventName, eventData);
        window.acfNFConversion = (type, value) => analytics.journey.recordConversion(type, value);
    }

    // DOM Ready 시 초기화
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNativeAnalytics);
    } else {
        initNativeAnalytics();
    }

})(window, document);
