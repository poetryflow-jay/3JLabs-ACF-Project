<?php
/**
 * 트래픽 소스 및 광고 매체 매개변수 정의
 * 
 * 글로벌 및 한국 광고 매체들의 공식 URL 매개변수를 정의
 * UTM 없이도 자동 추적 가능하도록 매체별 고유 파라미터 지원
 * 
 * @package ACF_Nudge_Flow
 * @since 22.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Nudge_Flow_Traffic_Source_Definitions {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 광고 매체별 클릭 ID 파라미터 (자동 태깅)
     * 
     * 각 광고 플랫폼이 자동으로 추가하는 클릭 식별자
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
     * 
     * 검색광고, 쇼핑검색, 브랜드검색, 성과형 DA(GFA) 포함
     */
    public function get_naver_parameters() {
        return array(
            // === 네이버 검색광고 (파워링크) ===
            'search_ad' => array(
                'name' => '네이버 검색광고 (파워링크)',
                'auto_params' => array(
                    'n_media' => array(
                        'description' => '매체 고유 ID',
                        'example' => '8753',
                    ),
                    'n_query' => array(
                        'description' => '이용자 검색어',
                        'example' => '여성의류',
                    ),
                    'n_rank' => array(
                        'description' => '광고 노출 순위',
                        'example' => '1',
                    ),
                    'n_ad_group' => array(
                        'description' => '광고그룹 고유 ID',
                        'example' => 'grp-a001-01-000000000005304',
                    ),
                    'n_ad' => array(
                        'description' => '광고 소재 고유 ID',
                        'example' => 'nad-a001-01-000000000002274',
                    ),
                    'n_keyword_id' => array(
                        'description' => '키워드 ID',
                        'example' => 'nkw-a001-01-000000000015954',
                    ),
                    'n_keyword' => array(
                        'description' => '등록 키워드',
                        'example' => '여성의류',
                    ),
                    'n_campaign_type' => array(
                        'description' => '캠페인 유형',
                        'example' => '1 (검색광고)',
                    ),
                    'n_ad_group_type' => array(
                        'description' => '광고그룹 유형',
                        'example' => '',
                    ),
                    'n_match' => array(
                        'description' => '키워드 매칭 방식',
                        'example' => '1 (정확/확장)',
                    ),
                ),
            ),
            
            // === 네이버 쇼핑검색광고 ===
            'shopping_search' => array(
                'name' => '네이버 쇼핑검색광고',
                'auto_params' => array(
                    'n_media' => array(
                        'description' => '매체 고유 ID',
                    ),
                    'n_query' => array(
                        'description' => '이용자 검색어',
                    ),
                    'n_rank' => array(
                        'description' => '광고 노출 순위',
                    ),
                    'n_ad_group' => array(
                        'description' => '광고그룹 고유 ID',
                    ),
                    'n_ad' => array(
                        'description' => '광고 소재 고유 ID',
                    ),
                    'n_mall_pid' => array(
                        'description' => '쇼핑몰 상품 ID',
                    ),
                    'n_mall_id' => array(
                        'description' => '쇼핑몰 ID',
                    ),
                    'n_keyword_id' => array(
                        'description' => '키워드 ID',
                    ),
                    'n_keyword' => array(
                        'description' => '등록 키워드',
                    ),
                    'n_campaign_type' => array(
                        'description' => '캠페인 유형',
                    ),
                    'n_ad_group_type' => array(
                        'description' => '광고그룹 유형',
                    ),
                    'n_match' => array(
                        'description' => '키워드 매칭 방식',
                    ),
                ),
            ),
            
            // === 네이버 브랜드검색광고 ===
            'brand_search' => array(
                'name' => '네이버 브랜드검색광고',
                'auto_params' => array(
                    'n_media' => array(
                        'description' => '매체 고유 ID',
                    ),
                    'n_query' => array(
                        'description' => '브랜드 검색어',
                    ),
                    'n_campaign_type' => array(
                        'description' => '캠페인 유형 (브랜드검색)',
                    ),
                ),
            ),
            
            // === 네이버 성과형 디스플레이 광고 (GFA) ===
            'gfa' => array(
                'name' => '네이버 성과형 DA (GFA)',
                'auto_params' => array(
                    'na_source' => array(
                        'description' => '트래픽 소스',
                        'example' => 'gfa',
                    ),
                    'na_medium' => array(
                        'description' => '매체 유형',
                        'example' => 'display',
                    ),
                    'na_campaign' => array(
                        'description' => '캠페인명',
                    ),
                    'na_adset' => array(
                        'description' => '광고세트명',
                    ),
                    'na_ad' => array(
                        'description' => '광고명',
                    ),
                ),
                'manual_macros' => array(
                    '{campaign_id}' => '캠페인 ID',
                    '{campaign_name}' => '캠페인명',
                    '{adset_id}' => '광고세트 ID',
                    '{adset_name}' => '광고세트명',
                    '{ad_id}' => '광고 ID',
                    '{ad_name}' => '광고명',
                ),
            ),
            
            // === 네이버 보장형 디스플레이 광고 ===
            'display_guaranteed' => array(
                'name' => '네이버 보장형 DA',
                'note' => '보장형 광고는 UTM 수동 설정 권장',
            ),
        );
    }

    /**
     * 카카오 광고 매개변수
     * 
     * 카카오 모먼트, 카카오 검색광고(다음), 카카오 디스플레이 네트워크 포함
     */
    public function get_kakao_parameters() {
        return array(
            // === 카카오 모먼트 ===
            'moment' => array(
                'name' => '카카오 모먼트',
                'placements' => array( '카카오톡', '카카오스토리', '다음', '카카오 디스플레이 네트워크' ),
                'auto_params' => array(
                    'kakao_campaign' => array(
                        'description' => '캠페인 ID/명',
                    ),
                    'kakao_adgrp' => array(
                        'description' => '광고그룹 ID/명',
                    ),
                    'kakao_creative' => array(
                        'description' => '소재 ID/명',
                    ),
                    'kakao_keyword' => array(
                        'description' => '키워드 (검색광고)',
                    ),
                ),
                'click_id_param' => '{click_id}',
                'manual_macros' => array(
                    '{click_id}' => '클릭 ID (필수)',
                    '{campaign_id}' => '캠페인 ID',
                    '{campaign_name}' => '캠페인명',
                    '{adgroup_id}' => '광고그룹 ID',
                    '{adgroup_name}' => '광고그룹명',
                    '{creative_id}' => '소재 ID',
                    '{creative_name}' => '소재명',
                    '{google_aid}' => 'Google 광고 ID (Android)',
                    '{apple_ifa}' => 'Apple IDFA (iOS)',
                    '{p_id}' => '카탈로그 상품 ID',
                    '{p_필드명}' => '카탈로그 상품 필드값',
                ),
            ),
            
            // === 카카오 키워드 광고 (다음 검색) ===
            'keyword' => array(
                'name' => '카카오 키워드광고 (다음)',
                'auto_params' => array(
                    'dkwid' => array(
                        'description' => '다음 키워드 ID',
                    ),
                    'dkw' => array(
                        'description' => '검색 키워드',
                    ),
                ),
            ),
        );
    }

    /**
     * Google 광고 매개변수 (ValueTrack)
     */
    public function get_google_parameters() {
        return array(
            // === Google Ads ===
            'ads' => array(
                'name' => 'Google Ads',
                'auto_tag' => 'gclid',
                'valuetrack_params' => array(
                    // 캠페인/광고그룹/광고 식별
                    '{campaignid}' => '캠페인 ID',
                    '{adgroupid}' => '광고그룹 ID',
                    '{creative}' => '광고 소재 ID',
                    '{feeditemid}' => '피드 항목 ID',
                    
                    // 키워드/타겟팅
                    '{keyword}' => '매칭된 키워드',
                    '{targetid}' => '타겟 ID (관심사, 리마케팅 등)',
                    '{target}' => '게재위치 타겟 (디스플레이)',
                    '{placement}' => '게재위치 도메인',
                    
                    // 매칭 및 네트워크
                    '{matchtype}' => '매치 유형 (e=정확, p=구문, b=확장)',
                    '{network}' => '네트워크 (g=검색, s=검색파트너, d=디스플레이)',
                    '{adposition}' => '광고 위치',
                    
                    // 디바이스
                    '{device}' => '기기 (m=모바일, t=태블릿, c=PC)',
                    '{devicemodel}' => '기기 모델',
                    
                    // 위치
                    '{loc_physical_ms}' => '사용자 물리적 위치 ID',
                    '{loc_interest_ms}' => '사용자 관심 위치 ID',
                    
                    // 기타
                    '{lpurl}' => '최종 도착 URL',
                    '{random}' => '랜덤 숫자 (캐시 방지)',
                    '{aceid}' => '실험/광고 조합 ID',
                    '{ifmobile:[value]}' => '모바일일 때 값',
                    '{ifnotmobile:[value]}' => '모바일 아닐 때 값',
                    '{ifsearch:[value]}' => '검색 네트워크일 때 값',
                    '{ifcontent:[value]}' => '디스플레이일 때 값',
                ),
            ),
            
            // === Google Analytics 4 ===
            'ga4' => array(
                'name' => 'Google Analytics 4',
                'auto_params' => array(
                    '_gl' => 'Google 링커 파라미터',
                    '_ga' => 'GA 클라이언트 ID',
                ),
            ),
        );
    }

    /**
     * Meta (Facebook/Instagram) 광고 매개변수
     */
    public function get_meta_parameters() {
        return array(
            'auto_tag' => 'fbclid',
            'placements' => array(
                'fb' => 'Facebook',
                'ig' => 'Instagram',
                'msg' => 'Messenger',
                'an' => 'Audience Network',
            ),
            'dynamic_params' => array(
                // 사이트/네트워크
                '{{site_source_name}}' => '광고 게재 플랫폼 (fb, ig, msg, an)',
                
                // 캠페인
                '{{campaign.id}}' => '캠페인 ID',
                '{{campaign.name}}' => '캠페인명 (게시 시점 고정)',
                
                // 광고세트
                '{{adset.id}}' => '광고세트 ID',
                '{{adset.name}}' => '광고세트명 (게시 시점 고정)',
                
                // 광고
                '{{ad.id}}' => '광고 ID',
                '{{ad.name}}' => '광고명 (게시 시점 고정)',
                
                // 게재위치
                '{{placement}}' => '상세 게재위치',
            ),
            'note' => 'name 매크로는 광고 게시 시점의 이름으로 고정됩니다. 이후 이름 변경 시 불일치 발생.',
        );
    }

    /**
     * TikTok 광고 매개변수
     */
    public function get_tiktok_parameters() {
        return array(
            'auto_tag' => 'ttclid',
            'ttclid_validity' => '30일',
            'dynamic_macros' => array(
                '__CAMPAIGN_ID__' => '캠페인 ID',
                '__CAMPAIGN_NAME__' => '캠페인명 (실시간 반영)',
                '__AID__' => '광고그룹 ID',
                '__AID_NAME__' => '광고그룹명 (실시간 반영)',
                '__CID__' => '광고 ID',
                '__CID_NAME__' => '광고명 (실시간 반영)',
                '__PLACEMENT__' => '게재위치 (TikTok, Pangle 등)',
            ),
            'recommended_template' => 'utm_source=tiktok&utm_medium=cpc&utm_campaign=__CAMPAIGN_NAME__&utm_term=__AID_NAME__&utm_content=__CID_NAME__',
        );
    }

    /**
     * Microsoft Ads (Bing) 매개변수
     */
    public function get_microsoft_parameters() {
        return array(
            'auto_tag' => 'msclkid',
            'uet_tag' => 'Microsoft UET Tag',
            'tracking_params' => array(
                '{campaignid}' => '캠페인 ID',
                '{CampaignId}' => '캠페인 ID (대소문자 구분)',
                '{Campaign}' => '캠페인명',
                '{adgroupid}' => '광고그룹 ID',
                '{AdGroupId}' => '광고그룹 ID',
                '{AdGroup}' => '광고그룹명',
                '{adid}' => '광고 ID',
                '{AdId}' => '광고 ID',
                '{keyword}' => '키워드',
                '{Keyword}' => '키워드 (대소문자 구분)',
                '{keyword:default}' => '키워드 (기본값 포함)',
                '{MatchType}' => '매치 유형',
                '{Network}' => '네트워크',
                '{Device}' => '기기',
                '{TargetId}' => '타겟 ID',
                '{QueryString}' => '검색 쿼리',
                '{msclkid}' => 'Microsoft Click ID',
            ),
            'recommended_template' => '{lpurl}?utm_source=bing&utm_medium=cpc&utm_campaign={Campaign}&utm_term={keyword:default}&msclkid={msclkid}',
        );
    }

    /**
     * 토스 애드 매개변수
     */
    public function get_toss_parameters() {
        return array(
            'name' => '토스애즈 (Toss Ads)',
            'note' => '토스애즈는 웹사이트 트래킹을 자체 지원하지 않음. UTM 파라미터 수동 설정 권장.',
            'recommended_utm' => array(
                'utm_source' => 'toss',
                'utm_medium' => 'cpc',
                'utm_campaign' => '[캠페인명]',
            ),
            'mat_support' => array( 'Appsflyer', 'Airbridge', 'Branch', 'Adjust' ),
        );
    }

    /**
     * 당근마켓 (당근비즈니스) 매개변수
     */
    public function get_daangn_parameters() {
        return array(
            'name' => '당근비즈니스',
            'tracking_method' => '전환 추적 코드 (픽셀)',
            'note' => '당근비즈니스는 자체 전환 추적 코드를 제공하며, UTM 파라미터를 별도로 설정해야 함.',
            'recommended_utm' => array(
                'utm_source' => 'daangn',
                'utm_medium' => 'cpc',
                'utm_campaign' => '[캠페인명]',
            ),
            'mat_support' => array( 'Appsflyer', 'Airbridge', 'Adjust', 'Singular' ),
        );
    }

    /**
     * 데이블 (Dable) 네이티브 광고 매개변수
     */
    public function get_dable_parameters() {
        return array(
            'name' => '데이블 네이티브 AD',
            'type' => '네이티브 광고 (콘텐츠 추천)',
            'auto_params' => array(
                'dablead' => array(
                    'description' => '데이블 광고 식별자',
                    'example' => 'preview-2xwNyBNX7sED',
                ),
            ),
            'recommended_utm' => array(
                'utm_source' => 'dable',
                'utm_medium' => 'native',
                'utm_campaign' => '[캠페인명]',
            ),
            'targeting' => array( '관심사 타겟팅', '지역 타겟팅 (도/광역시 단위)' ),
        );
    }

    /**
     * 모비온 (Mobon) 리타겟팅 광고 매개변수
     */
    public function get_mobon_parameters() {
        return array(
            'name' => '모비온 (Mobon)',
            'type' => '리타겟팅 광고',
            'script_config' => array(
                'format' => "var wp_conf = 'ti=[광고주코드]&v=1&device=web';",
                'note' => '타겟팅게이츠 혹은 광고 에이전시에서 광고주 코드 확인',
            ),
            'recommended_utm' => array(
                'utm_source' => 'mobon',
                'utm_medium' => 'display',
                'utm_campaign' => '[캠페인명]',
            ),
        );
    }

    /**
     * 타겟팅게이츠 (Targeting Gates) 매개변수
     */
    public function get_targeting_gates_parameters() {
        return array(
            'name' => '타겟팅게이츠 (Wider Planet)',
            'type' => 'DMP 기반 DSP',
            'products' => array(
                'retargeting' => '리타겟팅',
                'user_targeting' => '유저타겟팅 (관심사/인구통계)',
                'dmp_targeting' => 'DMP 타겟팅 (행동 패턴 분석)',
                'keyword_targeting' => '검색어 기반 타겟팅 (14일 이내)',
            ),
            'script_config' => array(
                'format' => "var wp_conf = 'ti=[광고주코드]&v=1&device=web';",
            ),
            'recommended_utm' => array(
                'utm_source' => 'targetinggates',
                'utm_medium' => 'display',
                'utm_campaign' => '[캠페인명]',
            ),
        );
    }

    /**
     * 크리테오 (Criteo) 매개변수
     */
    public function get_criteo_parameters() {
        return array(
            'name' => '크리테오 (Criteo)',
            'type' => '리타겟팅/퍼포먼스 광고',
            'recommended_utm' => array(
                'utm_source' => 'criteo',
                'utm_medium' => 'cpc',
                'utm_campaign' => 'retargeting',
            ),
            'tracking_types' => array(
                'global_tracker' => '전체 캠페인 공통 트래커',
                'creative_tracker' => '소재별 개별 트래커',
            ),
        );
    }

    /**
     * AI 검색 엔진/챗봇 리퍼러 도메인
     */
    public function get_ai_referrer_domains() {
        return array(
            // OpenAI / ChatGPT
            'chat.openai.com' => array(
                'source' => 'chatgpt',
                'name' => 'ChatGPT',
                'type' => 'ai_chatbot',
                'note' => '대부분 direct로 기록됨 (리퍼러 미전송)',
            ),
            'chatgpt.com' => array(
                'source' => 'chatgpt',
                'name' => 'ChatGPT',
                'type' => 'ai_chatbot',
            ),
            
            // Perplexity AI
            'perplexity.ai' => array(
                'source' => 'perplexity',
                'name' => 'Perplexity AI',
                'type' => 'ai_search',
                'note' => '리퍼러 정상 전송 (추적 용이)',
            ),
            'www.perplexity.ai' => array(
                'source' => 'perplexity',
                'name' => 'Perplexity AI',
                'type' => 'ai_search',
            ),
            
            // Google Gemini / Bard
            'gemini.google.com' => array(
                'source' => 'gemini',
                'name' => 'Google Gemini',
                'type' => 'ai_chatbot',
            ),
            'bard.google.com' => array(
                'source' => 'gemini',
                'name' => 'Google Bard (구)',
                'type' => 'ai_chatbot',
            ),
            
            // Anthropic Claude
            'claude.ai' => array(
                'source' => 'claude',
                'name' => 'Claude AI',
                'type' => 'ai_chatbot',
            ),
            
            // Microsoft Copilot
            'copilot.microsoft.com' => array(
                'source' => 'copilot',
                'name' => 'Microsoft Copilot',
                'type' => 'ai_chatbot',
            ),
            'www.bing.com/chat' => array(
                'source' => 'copilot',
                'name' => 'Bing Chat (Copilot)',
                'type' => 'ai_chatbot',
            ),
            
            // You.com
            'you.com' => array(
                'source' => 'you',
                'name' => 'You.com AI',
                'type' => 'ai_search',
            ),
            
            // Phind
            'phind.com' => array(
                'source' => 'phind',
                'name' => 'Phind',
                'type' => 'ai_search',
            ),
            'www.phind.com' => array(
                'source' => 'phind',
                'name' => 'Phind',
                'type' => 'ai_search',
            ),
            
            // Kagi
            'kagi.com' => array(
                'source' => 'kagi',
                'name' => 'Kagi Search',
                'type' => 'ai_search',
            ),
            
            // SearchGPT / OpenAI Search (신규)
            'search.openai.com' => array(
                'source' => 'searchgpt',
                'name' => 'SearchGPT',
                'type' => 'ai_search',
            ),
        );
    }

    /**
     * 검색 엔진 리퍼러 도메인 (전통적)
     */
    public function get_search_engine_domains() {
        return array(
            // Google
            'google.' => array(
                'source' => 'google',
                'name' => 'Google',
                'type' => 'search',
                'query_param' => 'q',
            ),
            
            // Naver
            'search.naver.com' => array(
                'source' => 'naver',
                'name' => '네이버',
                'type' => 'search',
                'query_param' => 'query',
            ),
            'm.search.naver.com' => array(
                'source' => 'naver',
                'name' => '네이버 (모바일)',
                'type' => 'search',
                'query_param' => 'query',
            ),
            
            // Daum
            'search.daum.net' => array(
                'source' => 'daum',
                'name' => '다음',
                'type' => 'search',
                'query_param' => 'q',
            ),
            'm.search.daum.net' => array(
                'source' => 'daum',
                'name' => '다음 (모바일)',
                'type' => 'search',
                'query_param' => 'q',
            ),
            
            // Bing
            'bing.com' => array(
                'source' => 'bing',
                'name' => 'Bing',
                'type' => 'search',
                'query_param' => 'q',
            ),
            
            // Yahoo
            'search.yahoo.' => array(
                'source' => 'yahoo',
                'name' => 'Yahoo',
                'type' => 'search',
                'query_param' => 'p',
            ),
            
            // Baidu
            'baidu.com' => array(
                'source' => 'baidu',
                'name' => 'Baidu',
                'type' => 'search',
                'query_param' => 'wd',
            ),
            
            // DuckDuckGo
            'duckduckgo.com' => array(
                'source' => 'duckduckgo',
                'name' => 'DuckDuckGo',
                'type' => 'search',
                'query_param' => 'q',
            ),
            
            // ZUM
            'search.zum.com' => array(
                'source' => 'zum',
                'name' => 'ZUM',
                'type' => 'search',
                'query_param' => 'query',
            ),
        );
    }

    /**
     * 소셜 미디어 리퍼러 도메인
     */
    public function get_social_media_domains() {
        return array(
            // Facebook
            'facebook.com' => array( 'source' => 'facebook', 'name' => 'Facebook', 'type' => 'social' ),
            'l.facebook.com' => array( 'source' => 'facebook', 'name' => 'Facebook', 'type' => 'social' ),
            'lm.facebook.com' => array( 'source' => 'facebook', 'name' => 'Facebook (모바일)', 'type' => 'social' ),
            'm.facebook.com' => array( 'source' => 'facebook', 'name' => 'Facebook (모바일)', 'type' => 'social' ),
            'fb.com' => array( 'source' => 'facebook', 'name' => 'Facebook', 'type' => 'social' ),
            
            // Instagram
            'instagram.com' => array( 'source' => 'instagram', 'name' => 'Instagram', 'type' => 'social' ),
            'l.instagram.com' => array( 'source' => 'instagram', 'name' => 'Instagram', 'type' => 'social' ),
            
            // Twitter / X
            't.co' => array( 'source' => 'twitter', 'name' => 'Twitter (X)', 'type' => 'social' ),
            'twitter.com' => array( 'source' => 'twitter', 'name' => 'Twitter', 'type' => 'social' ),
            'x.com' => array( 'source' => 'twitter', 'name' => 'X (Twitter)', 'type' => 'social' ),
            
            // LinkedIn
            'linkedin.com' => array( 'source' => 'linkedin', 'name' => 'LinkedIn', 'type' => 'social' ),
            'lnkd.in' => array( 'source' => 'linkedin', 'name' => 'LinkedIn', 'type' => 'social' ),
            
            // TikTok
            'tiktok.com' => array( 'source' => 'tiktok', 'name' => 'TikTok', 'type' => 'social' ),
            'vm.tiktok.com' => array( 'source' => 'tiktok', 'name' => 'TikTok', 'type' => 'social' ),
            
            // YouTube
            'youtube.com' => array( 'source' => 'youtube', 'name' => 'YouTube', 'type' => 'video' ),
            'youtu.be' => array( 'source' => 'youtube', 'name' => 'YouTube', 'type' => 'video' ),
            'm.youtube.com' => array( 'source' => 'youtube', 'name' => 'YouTube (모바일)', 'type' => 'video' ),
            
            // Pinterest
            'pinterest.com' => array( 'source' => 'pinterest', 'name' => 'Pinterest', 'type' => 'social' ),
            'pinterest.co.kr' => array( 'source' => 'pinterest', 'name' => 'Pinterest (한국)', 'type' => 'social' ),
            
            // Reddit
            'reddit.com' => array( 'source' => 'reddit', 'name' => 'Reddit', 'type' => 'social' ),
            
            // Threads
            'threads.net' => array( 'source' => 'threads', 'name' => 'Threads', 'type' => 'social' ),
            
            // Snapchat
            'snapchat.com' => array( 'source' => 'snapchat', 'name' => 'Snapchat', 'type' => 'social' ),
            
            // 네이버 블로그
            'blog.naver.com' => array( 'source' => 'naver_blog', 'name' => '네이버 블로그', 'type' => 'social' ),
            'm.blog.naver.com' => array( 'source' => 'naver_blog', 'name' => '네이버 블로그 (모바일)', 'type' => 'social' ),
            
            // 네이버 카페
            'cafe.naver.com' => array( 'source' => 'naver_cafe', 'name' => '네이버 카페', 'type' => 'social' ),
            
            // 티스토리
            'tistory.com' => array( 'source' => 'tistory', 'name' => '티스토리', 'type' => 'social' ),
            
            // 브런치
            'brunch.co.kr' => array( 'source' => 'brunch', 'name' => '브런치', 'type' => 'social' ),
        );
    }

    /**
     * 메신저 리퍼러 도메인
     */
    public function get_messenger_domains() {
        return array(
            // KakaoTalk
            'talk.kakao.com' => array( 'source' => 'kakaotalk', 'name' => '카카오톡', 'type' => 'messenger' ),
            'pf.kakao.com' => array( 'source' => 'kakaotalk', 'name' => '카카오톡 채널', 'type' => 'messenger' ),
            'open.kakao.com' => array( 'source' => 'kakaotalk', 'name' => '카카오톡 오픈채팅', 'type' => 'messenger' ),
            
            // LINE
            'line.me' => array( 'source' => 'line', 'name' => 'LINE', 'type' => 'messenger' ),
            'lin.ee' => array( 'source' => 'line', 'name' => 'LINE', 'type' => 'messenger' ),
            
            // Facebook Messenger
            'l.messenger.com' => array( 'source' => 'messenger', 'name' => 'Messenger', 'type' => 'messenger' ),
            'm.me' => array( 'source' => 'messenger', 'name' => 'Messenger', 'type' => 'messenger' ),
            
            // WhatsApp
            'wa.me' => array( 'source' => 'whatsapp', 'name' => 'WhatsApp', 'type' => 'messenger' ),
            'web.whatsapp.com' => array( 'source' => 'whatsapp', 'name' => 'WhatsApp Web', 'type' => 'messenger' ),
            'api.whatsapp.com' => array( 'source' => 'whatsapp', 'name' => 'WhatsApp', 'type' => 'messenger' ),
            
            // Telegram
            't.me' => array( 'source' => 'telegram', 'name' => 'Telegram', 'type' => 'messenger' ),
            'telegram.me' => array( 'source' => 'telegram', 'name' => 'Telegram', 'type' => 'messenger' ),
            
            // Discord
            'discord.com' => array( 'source' => 'discord', 'name' => 'Discord', 'type' => 'messenger' ),
            'discord.gg' => array( 'source' => 'discord', 'name' => 'Discord', 'type' => 'messenger' ),
            
            // Slack
            'slack.com' => array( 'source' => 'slack', 'name' => 'Slack', 'type' => 'messenger' ),
        );
    }

    /**
     * 이메일 클라이언트 리퍼러 도메인
     */
    public function get_email_domains() {
        return array(
            // Gmail
            'mail.google.com' => array( 'source' => 'gmail', 'name' => 'Gmail', 'type' => 'email' ),
            
            // Naver Mail
            'mail.naver.com' => array( 'source' => 'naver_mail', 'name' => '네이버 메일', 'type' => 'email' ),
            
            // Daum Mail
            'mail.daum.net' => array( 'source' => 'daum_mail', 'name' => '다음 메일', 'type' => 'email' ),
            
            // Outlook
            'outlook.live.com' => array( 'source' => 'outlook', 'name' => 'Outlook', 'type' => 'email' ),
            'outlook.office.com' => array( 'source' => 'outlook', 'name' => 'Outlook 365', 'type' => 'email' ),
            'outlook.office365.com' => array( 'source' => 'outlook', 'name' => 'Outlook 365', 'type' => 'email' ),
            
            // Yahoo Mail
            'mail.yahoo.com' => array( 'source' => 'yahoo_mail', 'name' => 'Yahoo Mail', 'type' => 'email' ),
        );
    }

    /**
     * 쇼핑 플랫폼 리퍼러 도메인
     */
    public function get_shopping_domains() {
        return array(
            // 쿠팡
            'coupang.com' => array( 'source' => 'coupang', 'name' => '쿠팡', 'type' => 'shopping' ),
            'link.coupang.com' => array( 'source' => 'coupang', 'name' => '쿠팡', 'type' => 'shopping' ),
            
            // G마켓
            'gmarket.co.kr' => array( 'source' => 'gmarket', 'name' => 'G마켓', 'type' => 'shopping' ),
            
            // 11번가
            '11st.co.kr' => array( 'source' => '11st', 'name' => '11번가', 'type' => 'shopping' ),
            
            // 옥션
            'auction.co.kr' => array( 'source' => 'auction', 'name' => '옥션', 'type' => 'shopping' ),
            
            // SSG
            'ssg.com' => array( 'source' => 'ssg', 'name' => 'SSG닷컴', 'type' => 'shopping' ),
            
            // 티몬
            'tmon.co.kr' => array( 'source' => 'tmon', 'name' => '티몬', 'type' => 'shopping' ),
            
            // 위메프
            'wemakeprice.com' => array( 'source' => 'wemakeprice', 'name' => '위메프', 'type' => 'shopping' ),
            
            // 네이버 쇼핑
            'shopping.naver.com' => array( 'source' => 'naver_shopping', 'name' => '네이버 쇼핑', 'type' => 'shopping' ),
            
            // 카카오 쇼핑
            'shopping.kakao.com' => array( 'source' => 'kakao_shopping', 'name' => '카카오 쇼핑', 'type' => 'shopping' ),
            
            // 무신사
            'musinsa.com' => array( 'source' => 'musinsa', 'name' => '무신사', 'type' => 'shopping' ),
            
            // 오늘의집
            'ohou.se' => array( 'source' => 'ohouse', 'name' => '오늘의집', 'type' => 'shopping' ),
            
            // 아마존
            'amazon.' => array( 'source' => 'amazon', 'name' => 'Amazon', 'type' => 'shopping' ),
        );
    }

    /**
     * 모든 트래픽 소스 정보 통합 반환
     */
    public function get_all_definitions() {
        return array(
            'click_ids' => $this->get_click_id_parameters(),
            'naver' => $this->get_naver_parameters(),
            'kakao' => $this->get_kakao_parameters(),
            'google' => $this->get_google_parameters(),
            'meta' => $this->get_meta_parameters(),
            'tiktok' => $this->get_tiktok_parameters(),
            'microsoft' => $this->get_microsoft_parameters(),
            'toss' => $this->get_toss_parameters(),
            'daangn' => $this->get_daangn_parameters(),
            'dable' => $this->get_dable_parameters(),
            'mobon' => $this->get_mobon_parameters(),
            'targeting_gates' => $this->get_targeting_gates_parameters(),
            'criteo' => $this->get_criteo_parameters(),
            'referrers' => array(
                'ai' => $this->get_ai_referrer_domains(),
                'search' => $this->get_search_engine_domains(),
                'social' => $this->get_social_media_domains(),
                'messenger' => $this->get_messenger_domains(),
                'email' => $this->get_email_domains(),
                'shopping' => $this->get_shopping_domains(),
            ),
        );
    }

    /**
     * URL 파라미터에서 광고 소스 감지
     * 
     * @param array $params URL 파라미터 배열
     * @return array 감지된 광고 소스 정보
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
            
            // 캠페인 유형 감지
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
     * 
     * @param string $referrer_url 리퍼러 URL
     * @return array 감지된 트래픽 소스 정보
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

        // www 제거
        $host = preg_replace( '/^www\./', '', $host );

        // 모든 도메인 목록 검색
        $all_domains = array_merge(
            $this->get_ai_referrer_domains(),
            $this->get_search_engine_domains(),
            $this->get_social_media_domains(),
            $this->get_messenger_domains(),
            $this->get_email_domains(),
            $this->get_shopping_domains()
        );

        // 정확한 매칭
        if ( isset( $all_domains[ $host ] ) ) {
            return $all_domains[ $host ];
        }

        // 부분 매칭 (도메인에 .이 포함된 패턴)
        foreach ( $all_domains as $domain => $info ) {
            if ( strpos( $domain, '.' ) === 0 ) {
                // .google. 같은 패턴
                continue;
            }
            if ( strpos( $host, $domain ) !== false ) {
                return $info;
            }
        }

        // 특수 케이스: google 도메인 (국가별)
        if ( strpos( $host, 'google.' ) !== false ) {
            return array(
                'source' => 'google',
                'name' => 'Google',
                'type' => 'search',
            );
        }

        // 알 수 없는 리퍼러
        return array(
            'source' => $host,
            'name' => $host,
            'type' => 'referral',
        );
    }
}

/**
 * 전역 함수: 트래픽 소스 정의 인스턴스 반환
 */
function acf_nf_traffic_sources() {
    return ACF_Nudge_Flow_Traffic_Source_Definitions::instance();
}
