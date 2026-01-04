/**
 * 분석 도구 통합 JavaScript
 * 
 * Google Analytics, MonsterInsights, Microsoft Clarity, Hotjar 등
 * 다양한 분석 도구의 데이터를 수집하여 서버로 전송
 * 
 * @package ACF_Nudge_Flow
 * @since 22.5.2
 */

(function($) {
    'use strict';

    /**
     * 분석 도구 통합 클래스
     */
    var AnalyticsIntegration = {
        
        /**
         * 초기화
         */
        init: function() {
            this.detectAnalyticsTools();
            this.collectAnalyticsData();
            this.sendToServer();
        },

        /**
         * 활성화된 분석 도구 감지
         */
        detectAnalyticsTools: function() {
            this.activeTools = {
                'google_analytics': this.isGoogleAnalyticsActive(),
                'monsterinsights': this.isMonsterInsightsActive(),
                'microsoft_clarity': this.isMicrosoftClarityActive(),
                'hotjar': this.isHotjarActive(),
            };
        },

        /**
         * Google Analytics 활성화 확인
         */
        isGoogleAnalyticsActive: function() {
            // gtag 확인
            if ( typeof gtag !== 'undefined' || typeof ga !== 'undefined' || typeof _gaq !== 'undefined' ) {
                return true;
            }
            
            // 스크립트 태그 확인
            if ( $('script[src*="google-analytics"], script[src*="gtag"], script[src*="googletagmanager"]').length > 0 ) {
                return true;
            }
            
            return false;
        },

        /**
         * MonsterInsights 활성화 확인
         */
        isMonsterInsightsActive: function() {
            if ( typeof MonsterInsights !== 'undefined' ) {
                return true;
            }
            
            if ( $('script[src*="monsterinsights"]').length > 0 ) {
                return true;
            }
            
            return false;
        },

        /**
         * Microsoft Clarity 활성화 확인
         */
        isMicrosoftClarityActive: function() {
            if ( typeof clarity !== 'undefined' ) {
                return true;
            }
            
            if ( $('script[src*="clarity.ms"]').length > 0 ) {
                return true;
            }
            
            return false;
        },

        /**
         * Hotjar 활성화 확인
         */
        isHotjarActive: function() {
            if ( typeof hj !== 'undefined' || typeof _hjSettings !== 'undefined' ) {
                return true;
            }
            
            if ( $('script[src*="hotjar"], script[src*="hjs"]').length > 0 ) {
                return true;
            }
            
            return false;
        },

        /**
         * 분석 데이터 수집
         */
        collectAnalyticsData: function() {
            this.analyticsData = {
                'pageviews': this.getPageviews(),
                'session_duration': this.getSessionDuration(),
                'scroll_depth': this.getScrollDepth(),
                'time_on_page': this.getTimeOnPage(),
                'bounce_risk': this.calculateBounceRisk(),
                'heatmap_data': this.getHeatmapData(),
                'recordings': this.getRecordings(),
            };

            // Google Analytics 데이터
            if ( this.activeTools.google_analytics ) {
                this.analyticsData.google_analytics = this.getGoogleAnalyticsData();
            }

            // MonsterInsights 데이터
            if ( this.activeTools.monsterinsights ) {
                this.analyticsData.monsterinsights = this.getMonsterInsightsData();
            }

            // Microsoft Clarity 데이터
            if ( this.activeTools.microsoft_clarity ) {
                this.analyticsData.microsoft_clarity = this.getMicrosoftClarityData();
            }

            // Hotjar 데이터
            if ( this.activeTools.hotjar ) {
                this.analyticsData.hotjar = this.getHotjarData();
            }
        },

        /**
         * 페이지뷰 수 조회
         */
        getPageviews: function() {
            // 세션 스토리지에서 페이지뷰 카운트
            var pageviews = sessionStorage.getItem('acf_nudge_pageviews') || 0;
            pageviews = parseInt(pageviews) + 1;
            sessionStorage.setItem('acf_nudge_pageviews', pageviews);
            return pageviews;
        },

        /**
         * 세션 지속 시간 조회
         */
        getSessionDuration: function() {
            var sessionStart = sessionStorage.getItem('acf_nudge_session_start');
            if ( !sessionStart ) {
                sessionStart = Date.now();
                sessionStorage.setItem('acf_nudge_session_start', sessionStart);
            }
            return Math.floor((Date.now() - parseInt(sessionStart)) / 1000); // 초 단위
        },

        /**
         * 스크롤 깊이 조회
         */
        getScrollDepth: function() {
            var scrollDepth = sessionStorage.getItem('acf_nudge_scroll_depth') || 0;
            var currentScroll = Math.round(($(window).scrollTop() / ($(document).height() - $(window).height())) * 100);
            if ( currentScroll > parseInt(scrollDepth) ) {
                sessionStorage.setItem('acf_nudge_scroll_depth', currentScroll);
                return currentScroll;
            }
            return parseInt(scrollDepth);
        },

        /**
         * 페이지 체류 시간 조회
         */
        getTimeOnPage: function() {
            var pageStart = sessionStorage.getItem('acf_nudge_page_start');
            if ( !pageStart ) {
                pageStart = Date.now();
                sessionStorage.setItem('acf_nudge_page_start', pageStart);
            }
            return Math.floor((Date.now() - parseInt(pageStart)) / 1000); // 초 단위
        },

        /**
         * 이탈 위험도 계산
         */
        calculateBounceRisk: function() {
            var timeOnPage = this.getTimeOnPage();
            var scrollDepth = this.getScrollDepth();
            
            // 30초 이내이고 스크롤이 10% 미만이면 이탈 위험
            if ( timeOnPage < 30 && scrollDepth < 10 ) {
                return 'high';
            }
            
            // 60초 이내이고 스크롤이 30% 미만이면 중간 위험
            if ( timeOnPage < 60 && scrollDepth < 30 ) {
                return 'medium';
            }
            
            return 'low';
        },

        /**
         * 히트맵 데이터 수집 (Clarity/Hotjar)
         */
        getHeatmapData: function() {
            // 클릭 이벤트 추적
            var clicks = JSON.parse(sessionStorage.getItem('acf_nudge_clicks') || '[]');
            
            // 클릭 위치 저장
            $(document).on('click', function(e) {
                var clickData = {
                    'x': e.pageX,
                    'y': e.pageY,
                    'element': e.target.tagName,
                    'timestamp': Date.now()
                };
                clicks.push(clickData);
                
                // 최근 100개만 유지
                if ( clicks.length > 100 ) {
                    clicks = clicks.slice(-100);
                }
                
                sessionStorage.setItem('acf_nudge_clicks', JSON.stringify(clicks));
            });
            
            return clicks;
        },

        /**
         * 세션 녹화 데이터 (Clarity/Hotjar)
         */
        getRecordings: function() {
            // 세션 녹화는 주로 서버 측에서 처리되므로
            // 여기서는 세션 ID만 반환
            return {
                'session_id': this.getSessionId(),
                'recording_available': this.activeTools.microsoft_clarity || this.activeTools.hotjar
            };
        },

        /**
         * 세션 ID 생성
         */
        getSessionId: function() {
            var sessionId = sessionStorage.getItem('acf_nudge_session_id');
            if ( !sessionId ) {
                sessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                sessionStorage.setItem('acf_nudge_session_id', sessionId);
            }
            return sessionId;
        },

        /**
         * Google Analytics 데이터 조회
         */
        getGoogleAnalyticsData: function() {
            var data = {};
            
            // gtag API 사용
            if ( typeof gtag !== 'undefined' ) {
                // Google Analytics 4
                data.version = 'ga4';
            } else if ( typeof ga !== 'undefined' ) {
                // Universal Analytics
                data.version = 'ua';
                if ( typeof ga.getAll === 'function' ) {
                    var trackers = ga.getAll();
                    if ( trackers.length > 0 ) {
                        data.tracking_id = trackers[0].get('trackingId');
                    }
                }
            } else if ( typeof _gaq !== 'undefined' ) {
                // Classic Analytics
                data.version = 'classic';
            }
            
            return data;
        },

        /**
         * MonsterInsights 데이터 조회
         */
        getMonsterInsightsData: function() {
            var data = {};
            
            if ( typeof MonsterInsights !== 'undefined' ) {
                // MonsterInsights 객체에서 데이터 추출
                if ( MonsterInsights.tracking_id ) {
                    data.tracking_id = MonsterInsights.tracking_id;
                }
            }
            
            return data;
        },

        /**
         * Microsoft Clarity 데이터 조회
         */
        getMicrosoftClarityData: function() {
            var data = {};
            
            if ( typeof clarity !== 'undefined' ) {
                // Clarity API 사용 가능
                data.active = true;
            }
            
            return data;
        },

        /**
         * Hotjar 데이터 조회
         */
        getHotjarData: function() {
            var data = {};
            
            if ( typeof hj !== 'undefined' || typeof _hjSettings !== 'undefined' ) {
                data.active = true;
                
                if ( typeof _hjSettings !== 'undefined' ) {
                    data.site_id = _hjSettings.hjid || null;
                }
            }
            
            return data;
        },

        /**
         * 서버로 데이터 전송
         */
        sendToServer: function() {
            if ( !acfNudgeFlow || !acfNudgeFlow.ajaxUrl ) {
                return;
            }

            var self = this;
            
            // 페이지 언로드 시 데이터 전송
            $(window).on('beforeunload', function() {
                // navigator.sendBeacon 사용 (비동기 전송)
                if ( navigator.sendBeacon ) {
                    var data = JSON.stringify({
                        'action': 'acf_nudge_track',
                        'event_type': 'analytics_data',
                        'event_data': self.analyticsData,
                        'nonce': acfNudgeFlow.nonce
                    });
                    
                    navigator.sendBeacon(
                        acfNudgeFlow.ajaxUrl,
                        new Blob([data], {type: 'application/json'})
                    );
                }
            });

            // 주기적으로 데이터 전송 (30초마다)
            setInterval(function() {
                $.ajax({
                    url: acfNudgeFlow.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'acf_nudge_track',
                        event_type: 'analytics_data',
                        event_data: self.analyticsData,
                        nonce: acfNudgeFlow.nonce
                    },
                    success: function(response) {
                        // 성공적으로 전송됨
                    }
                });
            }, 30000); // 30초
        }
    };

    /**
     * DOM 로드 완료 시 초기화
     */
    $(document).ready(function() {
        AnalyticsIntegration.init();
    });

    /**
     * 전역으로 노출 (다른 스크립트에서 접근 가능)
     */
    window.ACFNudgeFlowAnalytics = AnalyticsIntegration;

})(jQuery);
