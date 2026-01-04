<?php
/**
 * 트래픽 소스 및 광고 매체 매개변수 정의
 *
 * 글로벌 및 한국 광고 매체들의 공식 URL 매개변수를 정의
 * UTM 없이도 자동 추적 가능하도록 매체별 고유 파라미터 지원
 *
 * @package ACF_User_Journey_Analytics
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_UJA_Traffic_Source_Definitions {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 광고 매체별 클릭 ID 파라미터 (자동 태깅)
     */
    public function get_click_id_parameters() {
        return array(
            // Google
            'gclid' => array(
                'platform' => 'google_ads',
                'name' => 'Google Click ID',
                'description' => 'Google Ads 자동 태깅 클릭 ID',
            ),
            'gad_source' => array(
                'platform' => 'google_ads',
                'name' => 'Google Ads Source',
                'description' => 'Google Ads 광고 소스 식별자',
            ),
            'gbraid' => array(
                'platform' => 'google_ads',
                'name' => 'Google App Campaign ID',
                'description' => 'Google 앱 캠페인 클릭 ID (iOS)',
            ),
            'wbraid' => array(
                'platform' => 'google_ads',
                'name' => 'Google Web-to-App ID',
                'description' => 'Google 웹-앱 전환 클릭 ID',
            ),

            // Meta (Facebook/Instagram)
            'fbclid' => array(
                'platform' => 'meta',
                'name' => 'Facebook Click ID',
                'description' => 'Meta (Facebook/Instagram) 클릭 ID',
            ),

            // TikTok
            'ttclid' => array(
                'platform' => 'tiktok',
                'name' => 'TikTok Click ID',
                'description' => 'TikTok 광고 클릭 ID (30일 유효)',
            ),

            // Twitter (X)
            'twclid' => array(
                'platform' => 'twitter',
                'name' => 'Twitter Click ID',
                'description' => 'Twitter(X) 광고 클릭 ID',
            ),

            // Microsoft (Bing)
            'msclkid' => array(
                'platform' => 'microsoft',
                'name' => 'Microsoft Click ID',
                'description' => 'Microsoft Ads (Bing) 클릭 ID',
            ),

            // LinkedIn
            'li_fat_id' => array(
                'platform' => 'linkedin',
                'name' => 'LinkedIn Click ID',
                'description' => 'LinkedIn 광고 클릭 ID',
            ),

            // Pinterest
            'epik' => array(
                'platform' => 'pinterest',
                'name' => 'Pinterest Click ID',
                'description' => 'Pinterest 광고 클릭 ID',
            ),

            // Snapchat
            'ScCid' => array(
                'platform' => 'snapchat',
                'name' => 'Snapchat Click ID',
                'description' => 'Snapchat 광고 클릭 ID',
            ),
            'sccid' => array(
                'platform' => 'snapchat',
                'name' => 'Snapchat Click ID (lowercase)',
                'description' => 'Snapchat 광고 클릭 ID',
            ),
        );
    }

    /**
     * 네이버 광고 매개변수
     */
    public function get_naver_parameters() {
        return array(
            'search_ad' => array(
                'name' => '네이버 검색광고 (파워링크)',
                'auto_params' => array( 'n_media', 'n_query', 'n_rank', 'n_ad_group', 'n_ad', 'n_keyword_id', 'n_keyword', 'n_campaign_type', 'n_ad_group_type', 'n_match' ),
            ),
            'shopping_search' => array(
                'name' => '네이버 쇼핑검색광고',
                'auto_params' => array( 'n_media', 'n_query', 'n_rank', 'n_ad_group', 'n_ad', 'n_mall_pid', 'n_mall_id', 'n_keyword_id', 'n_keyword', 'n_campaign_type', 'n_ad_group_type', 'n_match' ),
            ),
            'gfa' => array(
                'name' => '네이버 성과형 DA (GFA)',
                'auto_params' => array( 'na_source', 'na_medium', 'na_campaign', 'na_adset', 'na_ad' ),
            ),
        );
    }

    /**
     * 카카오 광고 매개변수
     */
    public function get_kakao_parameters() {
        return array(
            'moment' => array(
                'name' => '카카오 모먼트',
                'auto_params' => array( 'kakao_campaign', 'kakao_adgrp', 'kakao_creative', 'kakao_keyword' ),
            ),
            'keyword' => array(
                'name' => '카카오 키워드광고 (다음)',
                'auto_params' => array( 'dkwid', 'dkw' ),
            ),
        );
    }

    /**
     * AI 검색 엔진/챗봇 리퍼러 도메인
     */
    public function get_ai_referrer_domains() {
        return array(
            'chat.openai.com' => array( 'source' => 'chatgpt', 'name' => 'ChatGPT', 'type' => 'ai_chatbot' ),
            'chatgpt.com' => array( 'source' => 'chatgpt', 'name' => 'ChatGPT', 'type' => 'ai_chatbot' ),
            'perplexity.ai' => array( 'source' => 'perplexity', 'name' => 'Perplexity AI', 'type' => 'ai_search' ),
            'www.perplexity.ai' => array( 'source' => 'perplexity', 'name' => 'Perplexity AI', 'type' => 'ai_search' ),
            'gemini.google.com' => array( 'source' => 'gemini', 'name' => 'Google Gemini', 'type' => 'ai_chatbot' ),
            'bard.google.com' => array( 'source' => 'gemini', 'name' => 'Google Bard', 'type' => 'ai_chatbot' ),
            'claude.ai' => array( 'source' => 'claude', 'name' => 'Claude AI', 'type' => 'ai_chatbot' ),
            'copilot.microsoft.com' => array( 'source' => 'copilot', 'name' => 'Microsoft Copilot', 'type' => 'ai_chatbot' ),
            'you.com' => array( 'source' => 'you', 'name' => 'You.com AI', 'type' => 'ai_search' ),
            'phind.com' => array( 'source' => 'phind', 'name' => 'Phind', 'type' => 'ai_search' ),
            'kagi.com' => array( 'source' => 'kagi', 'name' => 'Kagi Search', 'type' => 'ai_search' ),
            'search.openai.com' => array( 'source' => 'searchgpt', 'name' => 'SearchGPT', 'type' => 'ai_search' ),
        );
    }

    /**
     * 검색 엔진 리퍼러 도메인
     */
    public function get_search_engine_domains() {
        return array(
            'google.' => array( 'source' => 'google', 'name' => 'Google', 'type' => 'search', 'query_param' => 'q' ),
            'search.naver.com' => array( 'source' => 'naver', 'name' => '네이버', 'type' => 'search', 'query_param' => 'query' ),
            'm.search.naver.com' => array( 'source' => 'naver', 'name' => '네이버 (모바일)', 'type' => 'search', 'query_param' => 'query' ),
            'search.daum.net' => array( 'source' => 'daum', 'name' => '다음', 'type' => 'search', 'query_param' => 'q' ),
            'm.search.daum.net' => array( 'source' => 'daum', 'name' => '다음 (모바일)', 'type' => 'search', 'query_param' => 'q' ),
            'bing.com' => array( 'source' => 'bing', 'name' => 'Bing', 'type' => 'search', 'query_param' => 'q' ),
            'search.yahoo.' => array( 'source' => 'yahoo', 'name' => 'Yahoo', 'type' => 'search', 'query_param' => 'p' ),
            'baidu.com' => array( 'source' => 'baidu', 'name' => 'Baidu', 'type' => 'search', 'query_param' => 'wd' ),
            'duckduckgo.com' => array( 'source' => 'duckduckgo', 'name' => 'DuckDuckGo', 'type' => 'search', 'query_param' => 'q' ),
            'search.zum.com' => array( 'source' => 'zum', 'name' => 'ZUM', 'type' => 'search', 'query_param' => 'query' ),
        );
    }

    /**
     * 소셜 미디어 리퍼러 도메인
     */
    public function get_social_media_domains() {
        return array(
            'facebook.com' => array( 'source' => 'facebook', 'name' => 'Facebook', 'type' => 'social' ),
            'l.facebook.com' => array( 'source' => 'facebook', 'name' => 'Facebook', 'type' => 'social' ),
            'm.facebook.com' => array( 'source' => 'facebook', 'name' => 'Facebook (모바일)', 'type' => 'social' ),
            'instagram.com' => array( 'source' => 'instagram', 'name' => 'Instagram', 'type' => 'social' ),
            't.co' => array( 'source' => 'twitter', 'name' => 'Twitter (X)', 'type' => 'social' ),
            'twitter.com' => array( 'source' => 'twitter', 'name' => 'Twitter', 'type' => 'social' ),
            'x.com' => array( 'source' => 'twitter', 'name' => 'X (Twitter)', 'type' => 'social' ),
            'linkedin.com' => array( 'source' => 'linkedin', 'name' => 'LinkedIn', 'type' => 'social' ),
            'tiktok.com' => array( 'source' => 'tiktok', 'name' => 'TikTok', 'type' => 'social' ),
            'youtube.com' => array( 'source' => 'youtube', 'name' => 'YouTube', 'type' => 'video' ),
            'youtu.be' => array( 'source' => 'youtube', 'name' => 'YouTube', 'type' => 'video' ),
            'pinterest.com' => array( 'source' => 'pinterest', 'name' => 'Pinterest', 'type' => 'social' ),
            'reddit.com' => array( 'source' => 'reddit', 'name' => 'Reddit', 'type' => 'social' ),
            'threads.net' => array( 'source' => 'threads', 'name' => 'Threads', 'type' => 'social' ),
            'blog.naver.com' => array( 'source' => 'naver_blog', 'name' => '네이버 블로그', 'type' => 'social' ),
            'cafe.naver.com' => array( 'source' => 'naver_cafe', 'name' => '네이버 카페', 'type' => 'social' ),
            'tistory.com' => array( 'source' => 'tistory', 'name' => '티스토리', 'type' => 'social' ),
            'brunch.co.kr' => array( 'source' => 'brunch', 'name' => '브런치', 'type' => 'social' ),
        );
    }

    /**
     * 메신저 리퍼러 도메인
     */
    public function get_messenger_domains() {
        return array(
            'talk.kakao.com' => array( 'source' => 'kakaotalk', 'name' => '카카오톡', 'type' => 'messenger' ),
            'pf.kakao.com' => array( 'source' => 'kakaotalk', 'name' => '카카오톡 채널', 'type' => 'messenger' ),
            'open.kakao.com' => array( 'source' => 'kakaotalk', 'name' => '카카오톡 오픈채팅', 'type' => 'messenger' ),
            'line.me' => array( 'source' => 'line', 'name' => 'LINE', 'type' => 'messenger' ),
            'l.messenger.com' => array( 'source' => 'messenger', 'name' => 'Messenger', 'type' => 'messenger' ),
            'wa.me' => array( 'source' => 'whatsapp', 'name' => 'WhatsApp', 'type' => 'messenger' ),
            't.me' => array( 'source' => 'telegram', 'name' => 'Telegram', 'type' => 'messenger' ),
            'discord.com' => array( 'source' => 'discord', 'name' => 'Discord', 'type' => 'messenger' ),
            'slack.com' => array( 'source' => 'slack', 'name' => 'Slack', 'type' => 'messenger' ),
        );
    }

    /**
     * 이메일 클라이언트 리퍼러 도메인
     */
    public function get_email_domains() {
        return array(
            'mail.google.com' => array( 'source' => 'gmail', 'name' => 'Gmail', 'type' => 'email' ),
            'mail.naver.com' => array( 'source' => 'naver_mail', 'name' => '네이버 메일', 'type' => 'email' ),
            'mail.daum.net' => array( 'source' => 'daum_mail', 'name' => '다음 메일', 'type' => 'email' ),
            'outlook.live.com' => array( 'source' => 'outlook', 'name' => 'Outlook', 'type' => 'email' ),
            'outlook.office.com' => array( 'source' => 'outlook', 'name' => 'Outlook 365', 'type' => 'email' ),
        );
    }

    /**
     * 쇼핑 플랫폼 리퍼러 도메인
     */
    public function get_shopping_domains() {
        return array(
            'coupang.com' => array( 'source' => 'coupang', 'name' => '쿠팡', 'type' => 'shopping' ),
            'gmarket.co.kr' => array( 'source' => 'gmarket', 'name' => 'G마켓', 'type' => 'shopping' ),
            '11st.co.kr' => array( 'source' => '11st', 'name' => '11번가', 'type' => 'shopping' ),
            'auction.co.kr' => array( 'source' => 'auction', 'name' => '옥션', 'type' => 'shopping' ),
            'shopping.naver.com' => array( 'source' => 'naver_shopping', 'name' => '네이버 쇼핑', 'type' => 'shopping' ),
            'musinsa.com' => array( 'source' => 'musinsa', 'name' => '무신사', 'type' => 'shopping' ),
            'amazon.' => array( 'source' => 'amazon', 'name' => 'Amazon', 'type' => 'shopping' ),
        );
    }

    /**
     * URL 파라미터에서 광고 소스 감지
     */
    public function detect_ad_source_from_params( $params ) {
        $detected = array(
            'source' => null,
            'platform' => null,
            'type' => null,
            'details' => array(),
        );

        // 클릭 ID로 감지
        $click_ids = $this->get_click_id_parameters();
        foreach ( $click_ids as $param => $info ) {
            if ( ! empty( $params[ $param ] ) ) {
                $detected['source'] = $info['platform'];
                $detected['platform'] = $info['name'];
                $detected['type'] = 'paid';
                $detected['details']['click_id'] = $params[ $param ];
                $detected['details']['click_id_type'] = $param;
                return $detected;
            }
        }

        // 네이버 광고 파라미터 감지
        if ( ! empty( $params['n_media'] ) || ! empty( $params['n_query'] ) ) {
            $detected['source'] = 'naver';
            $detected['platform'] = '네이버 광고';
            $detected['type'] = 'paid';

            if ( ! empty( $params['n_mall_pid'] ) ) {
                $detected['details']['ad_type'] = 'shopping_search';
            } elseif ( ! empty( $params['n_keyword'] ) ) {
                $detected['details']['ad_type'] = 'search_ad';
            }

            foreach ( array( 'n_media', 'n_query', 'n_rank', 'n_ad_group', 'n_keyword' ) as $key ) {
                if ( ! empty( $params[ $key ] ) ) {
                    $detected['details'][ $key ] = $params[ $key ];
                }
            }
            return $detected;
        }

        // 카카오 광고 파라미터 감지
        if ( ! empty( $params['kakao_campaign'] ) || ! empty( $params['kakao_adgrp'] ) ) {
            $detected['source'] = 'kakao';
            $detected['platform'] = '카카오 모먼트';
            $detected['type'] = 'paid';

            foreach ( array( 'kakao_campaign', 'kakao_adgrp', 'kakao_creative', 'kakao_keyword' ) as $key ) {
                if ( ! empty( $params[ $key ] ) ) {
                    $detected['details'][ $key ] = $params[ $key ];
                }
            }
            return $detected;
        }

        // 데이블 광고 파라미터 감지
        if ( ! empty( $params['dablead'] ) ) {
            $detected['source'] = 'dable';
            $detected['platform'] = '데이블';
            $detected['type'] = 'paid';
            $detected['details']['dablead'] = $params['dablead'];
            return $detected;
        }

        return $detected;
    }

    /**
     * 리퍼러에서 트래픽 소스 감지
     */
    public function detect_source_from_referrer( $referrer_url ) {
        if ( empty( $referrer_url ) ) {
            return array(
                'source' => 'direct',
                'name' => 'Direct',
                'type' => 'direct',
            );
        }

        $parsed = wp_parse_url( $referrer_url );
        $host = isset( $parsed['host'] ) ? strtolower( $parsed['host'] ) : '';
        $host = preg_replace( '/^www\./', '', $host );

        $all_domains = array_merge(
            $this->get_ai_referrer_domains(),
            $this->get_search_engine_domains(),
            $this->get_social_media_domains(),
            $this->get_messenger_domains(),
            $this->get_email_domains(),
            $this->get_shopping_domains()
        );

        if ( isset( $all_domains[ $host ] ) ) {
            return $all_domains[ $host ];
        }

        foreach ( $all_domains as $domain => $info ) {
            if ( strpos( $host, $domain ) !== false ) {
                return $info;
            }
        }

        if ( strpos( $host, 'google.' ) !== false ) {
            return array( 'source' => 'google', 'name' => 'Google', 'type' => 'search' );
        }

        return array(
            'source' => $host,
            'name' => $host,
            'type' => 'referral',
        );
    }
}

function acf_uja_traffic_sources() {
    return ACF_UJA_Traffic_Source_Definitions::instance();
}
