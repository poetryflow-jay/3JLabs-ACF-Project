/**
 * UTM 및 트래픽 소스 추적 (Enhanced v22.8.0)
 *
 * 모든 글로벌 및 한국 광고 매체 파라미터 지원
 * UTM 없이도 자동 태깅 파라미터로 트래픽 소스 식별
 *
 * @package ACF_Nudge_Flow
 * @since 22.7.0
 * @updated 22.8.0 - 포괄적인 광고 매체 파라미터 추가
 */

(function() {
    'use strict';

    /**
     * 광고 매체별 클릭 ID 파라미터 (자동 태깅)
     */
    const CLICK_ID_PARAMS = {
        // Google
        gclid: { platform: 'google_ads', name: 'Google Ads' },
        gad_source: { platform: 'google_ads', name: 'Google Ads Source' },
        gbraid: { platform: 'google_ads', name: 'Google App Campaign' },
        wbraid: { platform: 'google_ads', name: 'Google Web-to-App' },

        // Meta (Facebook/Instagram)
        fbclid: { platform: 'meta', name: 'Meta (Facebook/Instagram)' },

        // TikTok
        ttclid: { platform: 'tiktok', name: 'TikTok' },

        // Twitter (X)
        twclid: { platform: 'twitter', name: 'Twitter (X)' },

        // Microsoft (Bing)
        msclkid: { platform: 'microsoft', name: 'Microsoft Ads' },

        // LinkedIn
        li_fat_id: { platform: 'linkedin', name: 'LinkedIn' },

        // Pinterest
        epik: { platform: 'pinterest', name: 'Pinterest' },

        // Snapchat
        ScCid: { platform: 'snapchat', name: 'Snapchat' },
        sccid: { platform: 'snapchat', name: 'Snapchat' },
    };

    /**
     * 네이버 광고 파라미터
     */
    const NAVER_AD_PARAMS = [
        'n_media',      // 매체 고유 ID
        'n_query',      // 이용자 검색어
        'n_rank',       // 광고 노출 순위
        'n_ad_group',   // 광고그룹 고유 ID
        'n_ad',         // 광고 소재 고유 ID
        'n_keyword_id', // 키워드 ID
        'n_keyword',    // 등록 키워드
        'n_campaign_type', // 캠페인 유형
        'n_ad_group_type', // 광고그룹 유형
        'n_match',      // 키워드 매칭 방식
        'n_mall_pid',   // 쇼핑몰 상품 ID
        'n_mall_id',    // 쇼핑몰 ID
        'n_contract',   // 계약 정보
        'n_cost',       // 비용 정보
        // GFA (성과형 DA) 파라미터
        'na_source',    // 트래픽 소스
        'na_medium',    // 매체 유형
        'na_campaign',  // 캠페인명
        'na_adset',     // 광고세트명
        'na_ad',        // 광고명
    ];

    /**
     * 카카오 광고 파라미터
     */
    const KAKAO_AD_PARAMS = [
        'kakao_campaign',  // 캠페인 ID/명
        'kakao_adgrp',     // 광고그룹 ID/명
        'kakao_creative',  // 소재 ID/명
        'kakao_keyword',   // 키워드 (검색광고)
        // 다음 키워드 광고
        'dkwid',           // 다음 키워드 ID
        'dkw',             // 검색 키워드
    ];

    /**
     * 기타 광고 플랫폼 파라미터
     */
    const OTHER_AD_PARAMS = {
        // 데이블 (Dable)
        dablead: { platform: 'dable', name: '데이블' },

        // 모비온 / 타겟팅게이츠 식별
        ti: { platform: 'mobon_tg', name: '모비온/타겟팅게이츠' },
    };

    /**
     * AI 검색 엔진 / 챗봇 리퍼러 도메인
     */
    const AI_REFERRER_DOMAINS = {
        // OpenAI / ChatGPT
        'chat.openai.com': { source: 'chatgpt', name: 'ChatGPT', type: 'ai_chatbot' },
        'chatgpt.com': { source: 'chatgpt', name: 'ChatGPT', type: 'ai_chatbot' },

        // Perplexity AI
        'perplexity.ai': { source: 'perplexity', name: 'Perplexity AI', type: 'ai_search' },
        'www.perplexity.ai': { source: 'perplexity', name: 'Perplexity AI', type: 'ai_search' },

        // Google Gemini / Bard
        'gemini.google.com': { source: 'gemini', name: 'Google Gemini', type: 'ai_chatbot' },
        'bard.google.com': { source: 'gemini', name: 'Google Bard', type: 'ai_chatbot' },

        // Anthropic Claude
        'claude.ai': { source: 'claude', name: 'Claude AI', type: 'ai_chatbot' },

        // Microsoft Copilot
        'copilot.microsoft.com': { source: 'copilot', name: 'Microsoft Copilot', type: 'ai_chatbot' },

        // You.com
        'you.com': { source: 'you', name: 'You.com AI', type: 'ai_search' },

        // Phind
        'phind.com': { source: 'phind', name: 'Phind', type: 'ai_search' },
        'www.phind.com': { source: 'phind', name: 'Phind', type: 'ai_search' },

        // Kagi
        'kagi.com': { source: 'kagi', name: 'Kagi Search', type: 'ai_search' },

        // SearchGPT
        'search.openai.com': { source: 'searchgpt', name: 'SearchGPT', type: 'ai_search' },
    };

    /**
     * 검색 엔진 도메인
     */
    const SEARCH_ENGINE_DOMAINS = {
        // Google (국가별)
        'google.': { source: 'google', name: 'Google', type: 'search', partial: true },

        // Naver
        'search.naver.com': { source: 'naver', name: '네이버', type: 'search' },
        'm.search.naver.com': { source: 'naver', name: '네이버 (모바일)', type: 'search' },

        // Daum
        'search.daum.net': { source: 'daum', name: '다음', type: 'search' },
        'm.search.daum.net': { source: 'daum', name: '다음 (모바일)', type: 'search' },

        // Bing
        'bing.com': { source: 'bing', name: 'Bing', type: 'search' },
        'www.bing.com': { source: 'bing', name: 'Bing', type: 'search' },

        // Yahoo
        'search.yahoo.': { source: 'yahoo', name: 'Yahoo', type: 'search', partial: true },

        // Baidu
        'baidu.com': { source: 'baidu', name: 'Baidu', type: 'search' },

        // DuckDuckGo
        'duckduckgo.com': { source: 'duckduckgo', name: 'DuckDuckGo', type: 'search' },

        // ZUM
        'search.zum.com': { source: 'zum', name: 'ZUM', type: 'search' },
        'zum.com': { source: 'zum', name: 'ZUM', type: 'search' },
    };

    /**
     * 소셜 미디어 도메인
     */
    const SOCIAL_MEDIA_DOMAINS = {
        // Facebook
        'facebook.com': { source: 'facebook', name: 'Facebook', type: 'social' },
        'l.facebook.com': { source: 'facebook', name: 'Facebook', type: 'social' },
        'lm.facebook.com': { source: 'facebook', name: 'Facebook (모바일)', type: 'social' },
        'm.facebook.com': { source: 'facebook', name: 'Facebook (모바일)', type: 'social' },
        'fb.com': { source: 'facebook', name: 'Facebook', type: 'social' },

        // Instagram
        'instagram.com': { source: 'instagram', name: 'Instagram', type: 'social' },
        'l.instagram.com': { source: 'instagram', name: 'Instagram', type: 'social' },

        // Twitter / X
        't.co': { source: 'twitter', name: 'Twitter (X)', type: 'social' },
        'twitter.com': { source: 'twitter', name: 'Twitter', type: 'social' },
        'x.com': { source: 'twitter', name: 'X (Twitter)', type: 'social' },

        // LinkedIn
        'linkedin.com': { source: 'linkedin', name: 'LinkedIn', type: 'social' },
        'lnkd.in': { source: 'linkedin', name: 'LinkedIn', type: 'social' },

        // TikTok
        'tiktok.com': { source: 'tiktok', name: 'TikTok', type: 'social' },
        'vm.tiktok.com': { source: 'tiktok', name: 'TikTok', type: 'social' },

        // YouTube
        'youtube.com': { source: 'youtube', name: 'YouTube', type: 'video' },
        'youtu.be': { source: 'youtube', name: 'YouTube', type: 'video' },
        'm.youtube.com': { source: 'youtube', name: 'YouTube (모바일)', type: 'video' },

        // Pinterest
        'pinterest.com': { source: 'pinterest', name: 'Pinterest', type: 'social' },
        'pinterest.co.kr': { source: 'pinterest', name: 'Pinterest (한국)', type: 'social' },

        // Reddit
        'reddit.com': { source: 'reddit', name: 'Reddit', type: 'social' },

        // Threads
        'threads.net': { source: 'threads', name: 'Threads', type: 'social' },

        // Snapchat
        'snapchat.com': { source: 'snapchat', name: 'Snapchat', type: 'social' },

        // 네이버 블로그/카페
        'blog.naver.com': { source: 'naver_blog', name: '네이버 블로그', type: 'social' },
        'm.blog.naver.com': { source: 'naver_blog', name: '네이버 블로그 (모바일)', type: 'social' },
        'cafe.naver.com': { source: 'naver_cafe', name: '네이버 카페', type: 'social' },

        // 티스토리
        'tistory.com': { source: 'tistory', name: '티스토리', type: 'social' },

        // 브런치
        'brunch.co.kr': { source: 'brunch', name: '브런치', type: 'social' },
    };

    /**
     * 메신저 도메인
     */
    const MESSENGER_DOMAINS = {
        // KakaoTalk
        'talk.kakao.com': { source: 'kakaotalk', name: '카카오톡', type: 'messenger' },
        'pf.kakao.com': { source: 'kakaotalk', name: '카카오톡 채널', type: 'messenger' },
        'open.kakao.com': { source: 'kakaotalk', name: '카카오톡 오픈채팅', type: 'messenger' },

        // LINE
        'line.me': { source: 'line', name: 'LINE', type: 'messenger' },
        'lin.ee': { source: 'line', name: 'LINE', type: 'messenger' },

        // Facebook Messenger
        'l.messenger.com': { source: 'messenger', name: 'Messenger', type: 'messenger' },
        'm.me': { source: 'messenger', name: 'Messenger', type: 'messenger' },

        // WhatsApp
        'wa.me': { source: 'whatsapp', name: 'WhatsApp', type: 'messenger' },
        'web.whatsapp.com': { source: 'whatsapp', name: 'WhatsApp Web', type: 'messenger' },
        'api.whatsapp.com': { source: 'whatsapp', name: 'WhatsApp', type: 'messenger' },

        // Telegram
        't.me': { source: 'telegram', name: 'Telegram', type: 'messenger' },
        'telegram.me': { source: 'telegram', name: 'Telegram', type: 'messenger' },

        // Discord
        'discord.com': { source: 'discord', name: 'Discord', type: 'messenger' },
        'discord.gg': { source: 'discord', name: 'Discord', type: 'messenger' },

        // Slack
        'slack.com': { source: 'slack', name: 'Slack', type: 'messenger' },
    };

    /**
     * 이메일 클라이언트 도메인
     */
    const EMAIL_DOMAINS = {
        'mail.google.com': { source: 'gmail', name: 'Gmail', type: 'email' },
        'mail.naver.com': { source: 'naver_mail', name: '네이버 메일', type: 'email' },
        'mail.daum.net': { source: 'daum_mail', name: '다음 메일', type: 'email' },
        'outlook.live.com': { source: 'outlook', name: 'Outlook', type: 'email' },
        'outlook.office.com': { source: 'outlook', name: 'Outlook 365', type: 'email' },
        'outlook.office365.com': { source: 'outlook', name: 'Outlook 365', type: 'email' },
        'mail.yahoo.com': { source: 'yahoo_mail', name: 'Yahoo Mail', type: 'email' },
    };

    /**
     * 쇼핑 플랫폼 도메인
     */
    const SHOPPING_DOMAINS = {
        'coupang.com': { source: 'coupang', name: '쿠팡', type: 'shopping' },
        'link.coupang.com': { source: 'coupang', name: '쿠팡', type: 'shopping' },
        'gmarket.co.kr': { source: 'gmarket', name: 'G마켓', type: 'shopping' },
        '11st.co.kr': { source: '11st', name: '11번가', type: 'shopping' },
        'auction.co.kr': { source: 'auction', name: '옥션', type: 'shopping' },
        'ssg.com': { source: 'ssg', name: 'SSG닷컴', type: 'shopping' },
        'tmon.co.kr': { source: 'tmon', name: '티몬', type: 'shopping' },
        'wemakeprice.com': { source: 'wemakeprice', name: '위메프', type: 'shopping' },
        'shopping.naver.com': { source: 'naver_shopping', name: '네이버 쇼핑', type: 'shopping' },
        'shopping.kakao.com': { source: 'kakao_shopping', name: '카카오 쇼핑', type: 'shopping' },
        'musinsa.com': { source: 'musinsa', name: '무신사', type: 'shopping' },
        'ohou.se': { source: 'ohouse', name: '오늘의집', type: 'shopping' },
        'daangn.com': { source: 'daangn', name: '당근마켓', type: 'shopping' },
        'amazon.': { source: 'amazon', name: 'Amazon', type: 'shopping', partial: true },
    };

    /**
     * UTM 트래커 클래스 (Enhanced)
     */
    class UTMTracker {
        constructor() {
            this.cookieName = 'acf_nf_utm';
            this.firstTouchCookieName = 'acf_nf_utm_first';
            this.sessionCookieName = 'acf_nf_session';
            this.cookieDuration = 30; // 일
            this.config = window.acf_nf_utm_config || {};

            this.init();
        }

        init() {
            // 세션 ID 생성 또는 가져오기
            this.sessionId = this.getOrCreateSessionId();

            // URL에서 모든 파라미터 추출
            this.extractAllParams();

            // 리퍼러 정보 추출 (AI 검색엔진 포함)
            this.extractReferrerInfo();

            // 광고 소스 자동 감지
            this.detectAdSource();

            // 데이터 저장
            this.saveTrackingData();

            // 서버로 전송
            this.sendToServer();
        }

        /**
         * 세션 ID 생성 또는 가져오기
         */
        getOrCreateSessionId() {
            let sessionId = this.getCookie(this.sessionCookieName);

            if (!sessionId) {
                sessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                this.setCookie(this.sessionCookieName, sessionId, 0); // 세션 쿠키
            }

            return sessionId;
        }

        /**
         * URL에서 모든 파라미터 추출
         */
        extractAllParams() {
            const urlParams = new URLSearchParams(window.location.search);

            // UTM 파라미터
            this.utmParams = {
                utm_source: urlParams.get('utm_source'),
                utm_medium: urlParams.get('utm_medium'),
                utm_campaign: urlParams.get('utm_campaign'),
                utm_term: urlParams.get('utm_term'),
                utm_content: urlParams.get('utm_content'),
            };

            // 클릭 ID 파라미터 추출
            this.clickIds = {};
            for (const [param, info] of Object.entries(CLICK_ID_PARAMS)) {
                const value = urlParams.get(param);
                if (value) {
                    this.clickIds[param] = value;
                }
            }

            // 네이버 광고 파라미터 추출
            this.naverParams = {};
            for (const param of NAVER_AD_PARAMS) {
                const value = urlParams.get(param);
                if (value) {
                    this.naverParams[param] = value;
                }
            }

            // 카카오 광고 파라미터 추출
            this.kakaoParams = {};
            for (const param of KAKAO_AD_PARAMS) {
                const value = urlParams.get(param);
                if (value) {
                    this.kakaoParams[param] = value;
                }
            }

            // 기타 광고 파라미터 추출
            this.otherAdParams = {};
            for (const [param, info] of Object.entries(OTHER_AD_PARAMS)) {
                const value = urlParams.get(param);
                if (value) {
                    this.otherAdParams[param] = {
                        value: value,
                        platform: info.platform,
                        name: info.name
                    };
                }
            }
        }

        /**
         * 리퍼러 정보 추출 (AI 검색엔진 및 모든 소스 포함)
         */
        extractReferrerInfo() {
            const referrer = document.referrer;

            this.referrerInfo = {
                url: referrer,
                domain: '',
                type: 'direct',
                source: 'direct',
                name: 'Direct',
            };

            if (!referrer) {
                return;
            }

            try {
                const referrerUrl = new URL(referrer);
                this.referrerInfo.domain = referrerUrl.hostname;

                // 트래픽 소스 유형 판별
                const sourceInfo = this.detectTrafficSource(referrerUrl.hostname);
                this.referrerInfo.type = sourceInfo.type;
                this.referrerInfo.source = sourceInfo.source;
                this.referrerInfo.name = sourceInfo.name;
            } catch (e) {
                // URL 파싱 실패
            }
        }

        /**
         * 트래픽 소스 유형 판별 (포괄적)
         */
        detectTrafficSource(hostname) {
            // www 제거
            const cleanHost = hostname.replace(/^www\./, '').toLowerCase();

            // 자사 사이트 확인
            if (this.isSameDomain(hostname)) {
                return { type: 'internal', source: 'internal', name: 'Internal' };
            }

            // AI 검색엔진 확인 (우선순위 높음)
            for (const [domain, info] of Object.entries(AI_REFERRER_DOMAINS)) {
                if (cleanHost === domain || hostname.includes(domain)) {
                    return info;
                }
            }

            // 검색 엔진 확인
            for (const [domain, info] of Object.entries(SEARCH_ENGINE_DOMAINS)) {
                if (info.partial) {
                    if (hostname.includes(domain)) {
                        return info;
                    }
                } else if (cleanHost === domain || hostname === domain) {
                    return info;
                }
            }

            // 소셜 미디어 확인
            for (const [domain, info] of Object.entries(SOCIAL_MEDIA_DOMAINS)) {
                if (cleanHost === domain || hostname.includes(domain)) {
                    return info;
                }
            }

            // 메신저 확인
            for (const [domain, info] of Object.entries(MESSENGER_DOMAINS)) {
                if (cleanHost === domain || hostname.includes(domain)) {
                    return info;
                }
            }

            // 이메일 확인
            for (const [domain, info] of Object.entries(EMAIL_DOMAINS)) {
                if (cleanHost === domain || hostname.includes(domain)) {
                    return info;
                }
            }

            // 쇼핑 플랫폼 확인
            for (const [domain, info] of Object.entries(SHOPPING_DOMAINS)) {
                if (info.partial) {
                    if (hostname.includes(domain)) {
                        return info;
                    }
                } else if (cleanHost === domain || hostname.includes(domain)) {
                    return info;
                }
            }

            // 기타 referral
            return { type: 'referral', source: cleanHost, name: cleanHost };
        }

        /**
         * 같은 도메인인지 확인
         */
        isSameDomain(hostname) {
            const currentHost = window.location.hostname;
            return hostname === currentHost ||
                   hostname.endsWith('.' + currentHost) ||
                   currentHost.endsWith('.' + hostname);
        }

        /**
         * 광고 소스 자동 감지
         */
        detectAdSource() {
            this.adSource = {
                detected: false,
                source: null,
                platform: null,
                type: null,
                details: {}
            };

            // 클릭 ID로 감지
            for (const [param, value] of Object.entries(this.clickIds)) {
                const info = CLICK_ID_PARAMS[param];
                if (info) {
                    this.adSource.detected = true;
                    this.adSource.source = info.platform;
                    this.adSource.platform = info.name;
                    this.adSource.type = 'paid';
                    this.adSource.details.click_id = value;
                    this.adSource.details.click_id_type = param;
                    return;
                }
            }

            // 네이버 광고 파라미터로 감지
            if (Object.keys(this.naverParams).length > 0) {
                this.adSource.detected = true;
                this.adSource.source = 'naver';
                this.adSource.type = 'paid';

                // 광고 유형 세분화
                if (this.naverParams.n_mall_pid) {
                    this.adSource.platform = '네이버 쇼핑검색광고';
                    this.adSource.details.ad_type = 'shopping_search';
                } else if (this.naverParams.na_source === 'gfa') {
                    this.adSource.platform = '네이버 성과형 DA (GFA)';
                    this.adSource.details.ad_type = 'gfa';
                } else if (this.naverParams.n_keyword) {
                    this.adSource.platform = '네이버 검색광고';
                    this.adSource.details.ad_type = 'search_ad';
                } else if (this.naverParams.n_campaign_type === 'brand') {
                    this.adSource.platform = '네이버 브랜드검색광고';
                    this.adSource.details.ad_type = 'brand_search';
                } else {
                    this.adSource.platform = '네이버 광고';
                    this.adSource.details.ad_type = 'unknown';
                }

                this.adSource.details = {...this.adSource.details, ...this.naverParams};
                return;
            }

            // 카카오 광고 파라미터로 감지
            if (Object.keys(this.kakaoParams).length > 0) {
                this.adSource.detected = true;
                this.adSource.source = 'kakao';
                this.adSource.type = 'paid';

                // 광고 유형 세분화
                if (this.kakaoParams.dkwid || this.kakaoParams.dkw) {
                    this.adSource.platform = '카카오 키워드광고 (다음)';
                    this.adSource.details.ad_type = 'keyword_ad';
                } else {
                    this.adSource.platform = '카카오 모먼트';
                    this.adSource.details.ad_type = 'moment';
                }

                this.adSource.details = {...this.adSource.details, ...this.kakaoParams};
                return;
            }

            // 기타 광고 파라미터로 감지
            for (const [param, data] of Object.entries(this.otherAdParams)) {
                this.adSource.detected = true;
                this.adSource.source = data.platform;
                this.adSource.platform = data.name;
                this.adSource.type = 'paid';
                this.adSource.details[param] = data.value;
                return;
            }
        }

        /**
         * 추적 데이터 저장
         */
        saveTrackingData() {
            const hasNewUTM = Object.values(this.utmParams).some(v => v);
            const hasNewClickId = Object.keys(this.clickIds).length > 0;
            const hasNaverParams = Object.keys(this.naverParams).length > 0;
            const hasKakaoParams = Object.keys(this.kakaoParams).length > 0;
            const hasOtherAdParams = Object.keys(this.otherAdParams).length > 0;
            const hasAdSource = this.adSource.detected;

            // 현재 트래킹 데이터
            const currentData = {
                // UTM 파라미터
                ...this.utmParams,

                // 클릭 ID
                ...this.clickIds,

                // 플랫폼별 파라미터
                naver_params: this.naverParams,
                kakao_params: this.kakaoParams,
                other_ad_params: this.otherAdParams,

                // 광고 소스 감지 결과
                ad_source: this.adSource,

                // 리퍼러 정보
                referrer_url: this.referrerInfo.url,
                referrer_domain: this.referrerInfo.domain,
                referrer_type: this.referrerInfo.type,
                referrer_source: this.referrerInfo.source,
                referrer_name: this.referrerInfo.name,

                // 랜딩 페이지
                landing_page: window.location.href,
                landing_path: window.location.pathname,

                // 메타 정보
                timestamp: Date.now(),
                session_id: this.sessionId,
                user_agent: navigator.userAgent,
                screen_resolution: `${screen.width}x${screen.height}`,
                viewport: `${window.innerWidth}x${window.innerHeight}`,
                language: navigator.language || navigator.userLanguage,
            };

            // 첫 번째 터치 저장 (없는 경우에만)
            const firstTouch = this.getCookie(this.firstTouchCookieName);
            if (!firstTouch && (hasNewUTM || hasNewClickId || hasNaverParams || hasKakaoParams || hasOtherAdParams || this.referrerInfo.type !== 'direct')) {
                this.setCookie(this.firstTouchCookieName, JSON.stringify(currentData), this.cookieDuration);
            }

            // 마지막 터치 업데이트 (새 광고 데이터가 있는 경우)
            if (hasNewUTM || hasNewClickId || hasNaverParams || hasKakaoParams || hasOtherAdParams) {
                this.setCookie(this.cookieName, JSON.stringify(currentData), this.cookieDuration);
            }

            // 로컬 스토리지에도 저장 (백업)
            try {
                localStorage.setItem('acf_nf_last_touch', JSON.stringify(currentData));

                // 터치 히스토리 저장 (최대 20개로 확장)
                let touchHistory = JSON.parse(localStorage.getItem('acf_nf_touch_history') || '[]');
                if (hasNewUTM || hasNewClickId || hasNaverParams || hasKakaoParams || hasOtherAdParams || (this.referrerInfo.type !== 'direct' && this.referrerInfo.type !== 'internal')) {
                    touchHistory.unshift({
                        ...currentData,
                        page_title: document.title,
                    });
                    touchHistory = touchHistory.slice(0, 20);
                    localStorage.setItem('acf_nf_touch_history', JSON.stringify(touchHistory));
                }
            } catch (e) {
                // 로컬 스토리지 사용 불가
            }

            this.currentData = currentData;
        }

        /**
         * 서버로 데이터 전송
         */
        sendToServer() {
            // 새로운 트래킹 데이터가 있을 때만 전송
            const hasNewData = Object.values(this.utmParams).some(v => v) ||
                              Object.keys(this.clickIds).length > 0 ||
                              Object.keys(this.naverParams).length > 0 ||
                              Object.keys(this.kakaoParams).length > 0 ||
                              Object.keys(this.otherAdParams).length > 0;

            if (!hasNewData && this.referrerInfo.type === 'internal') {
                return;
            }

            const data = {
                action: 'acf_nf_track_utm',
                nonce: this.config.nonce || '',
                visitor_id: this.getVisitorId(),
                session_id: this.sessionId,
                user_id: this.config.userId || 0,
                data: this.currentData,
            };

            // Beacon API 사용 (페이지 이탈 시에도 전송 보장)
            if (navigator.sendBeacon) {
                const formData = new FormData();
                Object.entries(data).forEach(([key, value]) => {
                    formData.append(key, typeof value === 'object' ? JSON.stringify(value) : value);
                });
                navigator.sendBeacon(this.config.ajaxUrl || '/wp-admin/admin-ajax.php', formData);
            } else {
                // Fallback: XMLHttpRequest
                const xhr = new XMLHttpRequest();
                xhr.open('POST', this.config.ajaxUrl || '/wp-admin/admin-ajax.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                const params = Object.entries(data)
                    .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(typeof value === 'object' ? JSON.stringify(value) : value)}`)
                    .join('&');

                xhr.send(params);
            }
        }

        /**
         * 방문자 ID 가져오기
         */
        getVisitorId() {
            let visitorId = this.getCookie('acf_nf_visitor_id');

            if (!visitorId) {
                visitorId = 'v_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                this.setCookie('acf_nf_visitor_id', visitorId, 365);
            }

            return visitorId;
        }

        /**
         * 쿠키 설정
         */
        setCookie(name, value, days) {
            let expires = '';
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/; SameSite=Lax';
        }

        /**
         * 쿠키 가져오기
         */
        getCookie(name) {
            const nameEQ = name + '=';
            const cookies = document.cookie.split(';');
            for (let cookie of cookies) {
                cookie = cookie.trim();
                if (cookie.indexOf(nameEQ) === 0) {
                    return decodeURIComponent(cookie.substring(nameEQ.length));
                }
            }
            return null;
        }

        /**
         * 현재 UTM 데이터 가져오기
         */
        getCurrentUTM() {
            const cookie = this.getCookie(this.cookieName);
            if (cookie) {
                try {
                    return JSON.parse(cookie);
                } catch (e) {
                    return null;
                }
            }
            return this.currentData;
        }

        /**
         * 첫 번째 터치 데이터 가져오기
         */
        getFirstTouch() {
            const cookie = this.getCookie(this.firstTouchCookieName);
            if (cookie) {
                try {
                    return JSON.parse(cookie);
                } catch (e) {
                    return null;
                }
            }
            return null;
        }

        /**
         * 터치 히스토리 가져오기
         */
        getTouchHistory() {
            try {
                return JSON.parse(localStorage.getItem('acf_nf_touch_history') || '[]');
            } catch (e) {
                return [];
            }
        }

        /**
         * 광고 소스 정보 가져오기
         */
        getAdSource() {
            return this.adSource;
        }

        /**
         * 리퍼러 정보 가져오기
         */
        getReferrerInfo() {
            return this.referrerInfo;
        }
    }

    /**
     * 페이지 체류 시간 추적
     */
    class DwellTimeTracker {
        constructor() {
            this.startTime = Date.now();
            this.isVisible = true;
            this.totalActiveTime = 0;
            this.lastActiveTime = this.startTime;
            this.config = window.acf_nf_utm_config || {};
            this.scrollDepth = 0;
            this.maxScrollDepth = 0;

            this.init();
        }

        init() {
            // 페이지 가시성 변경 추적
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.pauseTracking();
                } else {
                    this.resumeTracking();
                }
            });

            // 스크롤 깊이 추적
            window.addEventListener('scroll', () => {
                this.updateScrollDepth();
            });

            // 페이지 언로드 시 데이터 전송
            window.addEventListener('beforeunload', () => {
                this.sendDwellTime();
            });

            // 주기적으로 활성 시간 업데이트 (5분마다)
            setInterval(() => {
                if (this.isVisible) {
                    this.updateActiveTime();
                }
            }, 300000);
        }

        updateScrollDepth() {
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const scrollTop = window.scrollY;

            if (documentHeight > windowHeight) {
                this.scrollDepth = Math.round((scrollTop / (documentHeight - windowHeight)) * 100);
                this.maxScrollDepth = Math.max(this.maxScrollDepth, this.scrollDepth);
            }
        }

        pauseTracking() {
            this.isVisible = false;
            this.updateActiveTime();
        }

        resumeTracking() {
            this.isVisible = true;
            this.lastActiveTime = Date.now();
        }

        updateActiveTime() {
            const now = Date.now();
            if (this.isVisible) {
                this.totalActiveTime += now - this.lastActiveTime;
            }
            this.lastActiveTime = now;
        }

        getDwellTime() {
            this.updateActiveTime();
            return {
                total_time: Date.now() - this.startTime,
                active_time: this.totalActiveTime,
                page_url: window.location.href,
                page_path: window.location.pathname,
                page_title: document.title,
                max_scroll_depth: this.maxScrollDepth,
            };
        }

        sendDwellTime() {
            const data = this.getDwellTime();

            // 3초 이하는 바운스로 간주하고 전송하지 않음
            if (data.active_time < 3000) {
                return;
            }

            const payload = {
                action: 'acf_nf_track_dwell_time',
                nonce: this.config.nonce || '',
                data: data,
            };

            if (navigator.sendBeacon) {
                const formData = new FormData();
                Object.entries(payload).forEach(([key, value]) => {
                    formData.append(key, typeof value === 'object' ? JSON.stringify(value) : value);
                });
                navigator.sendBeacon(this.config.ajaxUrl || '/wp-admin/admin-ajax.php', formData);
            }
        }
    }

    /**
     * 퍼널 추적
     */
    class FunnelTracker {
        constructor() {
            this.config = window.acf_nf_utm_config || {};
            this.currentPage = window.location.pathname;

            this.init();
        }

        init() {
            // 현재 페이지 타입 감지
            this.detectPageType();

            // 퍼널 단계 기록
            this.recordFunnelStep();
        }

        detectPageType() {
            this.pageType = 'other';

            // WooCommerce 페이지 감지
            if (typeof wc_add_to_cart_params !== 'undefined' || document.body.classList.contains('woocommerce')) {
                if (document.body.classList.contains('single-product')) {
                    this.pageType = 'product';
                } else if (document.body.classList.contains('woocommerce-cart')) {
                    this.pageType = 'cart';
                } else if (document.body.classList.contains('woocommerce-checkout')) {
                    this.pageType = 'checkout';
                } else if (document.body.classList.contains('woocommerce-order-received')) {
                    this.pageType = 'thank_you';
                } else if (document.body.classList.contains('woocommerce-shop') || document.body.classList.contains('archive')) {
                    this.pageType = 'shop';
                }
            }

            // 홈페이지
            if (document.body.classList.contains('home')) {
                this.pageType = 'home';
            }
        }

        recordFunnelStep() {
            const data = {
                action: 'acf_nf_record_funnel_step',
                nonce: this.config.nonce || '',
                page_type: this.pageType,
                page_url: window.location.href,
                page_path: this.currentPage,
                referrer: document.referrer,
                timestamp: Date.now(),
            };

            // 비동기 전송
            if (typeof fetch !== 'undefined') {
                const formData = new FormData();
                Object.entries(data).forEach(([key, value]) => {
                    formData.append(key, value);
                });

                fetch(this.config.ajaxUrl || '/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    body: formData,
                    keepalive: true,
                });
            }
        }

        /**
         * 커스텀 이벤트 기록
         */
        recordEvent(eventName, eventData = {}) {
            const data = {
                action: 'acf_nf_record_funnel_event',
                nonce: this.config.nonce || '',
                event_name: eventName,
                event_data: JSON.stringify(eventData),
                page_url: window.location.href,
                timestamp: Date.now(),
            };

            if (navigator.sendBeacon) {
                const formData = new FormData();
                Object.entries(data).forEach(([key, value]) => {
                    formData.append(key, value);
                });
                navigator.sendBeacon(this.config.ajaxUrl || '/wp-admin/admin-ajax.php', formData);
            }
        }
    }

    // 초기화
    window.acfNFUTMTracker = null;
    window.acfNFDwellTracker = null;
    window.acfNFFunnelTracker = null;

    function initTrackers() {
        window.acfNFUTMTracker = new UTMTracker();
        window.acfNFDwellTracker = new DwellTimeTracker();
        window.acfNFFunnelTracker = new FunnelTracker();
    }

    // DOM 로드 후 초기화
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTrackers);
    } else {
        initTrackers();
    }

    // 전역 함수 노출
    window.acfNFTrackEvent = function(eventName, eventData) {
        if (window.acfNFFunnelTracker) {
            window.acfNFFunnelTracker.recordEvent(eventName, eventData);
        }
    };

    window.acfNFGetUTM = function() {
        if (window.acfNFUTMTracker) {
            return window.acfNFUTMTracker.getCurrentUTM();
        }
        return null;
    };

    window.acfNFGetFirstTouch = function() {
        if (window.acfNFUTMTracker) {
            return window.acfNFUTMTracker.getFirstTouch();
        }
        return null;
    };

    window.acfNFGetTouchHistory = function() {
        if (window.acfNFUTMTracker) {
            return window.acfNFUTMTracker.getTouchHistory();
        }
        return [];
    };

    window.acfNFGetAdSource = function() {
        if (window.acfNFUTMTracker) {
            return window.acfNFUTMTracker.getAdSource();
        }
        return null;
    };

    window.acfNFGetReferrer = function() {
        if (window.acfNFUTMTracker) {
            return window.acfNFUTMTracker.getReferrerInfo();
        }
        return null;
    };

    window.acfNFGetDwellTime = function() {
        if (window.acfNFDwellTracker) {
            return window.acfNFDwellTracker.getDwellTime();
        }
        return null;
    };

})();
