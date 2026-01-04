<?php
/**
 * 광고 매체 픽셀 및 전환 추적 관리자
 * 
 * 주요 광고 매체의 전환 스크립트를 통합 관리
 * - Meta (Facebook/Instagram)
 * - Google Ads / GA4
 * - Naver (네이버 광고)
 * - Kakao (카카오 모먼트)
 * - Toss (토스 애드)
 * - TikTok
 * - Twitter (X)
 * - Microsoft Ads (Bing)
 * - LinkedIn
 * - Pinterest
 * - Criteo
 * - Taboola
 * - Yahoo Japan
 * 
 * @package ACF_Nudge_Flow
 * @since 22.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Nudge_Flow_Ad_Pixel_Manager {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 광고 매체 설정
     */
    private $ad_settings = array();

    /**
     * 지원하는 광고 매체 정의
     */
    private $supported_platforms = array(
        // 글로벌
        'meta' => array(
            'name' => 'Meta (Facebook/Instagram)',
            'id_field' => 'pixel_id',
            'id_label' => 'Pixel ID',
            'id_placeholder' => '예: 1234567890123456',
            'id_pattern' => '/^\d{15,16}$/',
            'events' => array( 'PageView', 'ViewContent', 'AddToCart', 'InitiateCheckout', 'Purchase', 'Lead', 'CompleteRegistration', 'Search', 'AddToWishlist', 'Contact' ),
            'has_access_token' => true,
            'docs_url' => 'https://developers.facebook.com/docs/meta-pixel/',
        ),
        'google_ads' => array(
            'name' => 'Google Ads',
            'id_field' => 'conversion_id',
            'id_label' => 'Conversion ID',
            'id_placeholder' => '예: AW-1234567890',
            'id_pattern' => '/^AW-\d{10,12}$/',
            'has_conversion_label' => true,
            'events' => array( 'conversion', 'page_view', 'purchase', 'sign_up', 'generate_lead', 'add_to_cart', 'begin_checkout' ),
            'docs_url' => 'https://support.google.com/google-ads/answer/7548399',
        ),
        'ga4' => array(
            'name' => 'Google Analytics 4',
            'id_field' => 'measurement_id',
            'id_label' => 'Measurement ID',
            'id_placeholder' => '예: G-XXXXXXXXXX',
            'id_pattern' => '/^G-[A-Z0-9]{10}$/',
            'events' => array( 'page_view', 'view_item', 'add_to_cart', 'begin_checkout', 'purchase', 'sign_up', 'login', 'search', 'generate_lead' ),
            'docs_url' => 'https://developers.google.com/analytics/devguides/collection/ga4',
        ),
        'tiktok' => array(
            'name' => 'TikTok',
            'id_field' => 'pixel_id',
            'id_label' => 'Pixel ID',
            'id_placeholder' => '예: C1234567890ABCDEF',
            'id_pattern' => '/^C[A-Z0-9]{16,20}$/',
            'events' => array( 'ViewContent', 'ClickButton', 'Search', 'AddToWishlist', 'AddToCart', 'InitiateCheckout', 'AddPaymentInfo', 'PlaceAnOrder', 'CompletePayment', 'Subscribe', 'SubmitForm', 'Contact', 'Download', 'CompleteRegistration' ),
            'docs_url' => 'https://ads.tiktok.com/help/article/standard-events-parameters',
        ),
        'twitter' => array(
            'name' => 'Twitter (X)',
            'id_field' => 'pixel_id',
            'id_label' => 'Pixel ID',
            'id_placeholder' => '예: o1234',
            'id_pattern' => '/^o\d{4,10}$/',
            'events' => array( 'PageView', 'ViewContent', 'AddToCart', 'AddToWishlist', 'InitiateCheckout', 'Purchase', 'Search', 'CompleteRegistration', 'Download', 'Lead', 'SignUp' ),
            'docs_url' => 'https://business.twitter.com/en/help/campaign-measurement-and-analytics/conversion-tracking-for-websites.html',
        ),
        'linkedin' => array(
            'name' => 'LinkedIn',
            'id_field' => 'partner_id',
            'id_label' => 'Partner ID',
            'id_placeholder' => '예: 1234567',
            'id_pattern' => '/^\d{6,10}$/',
            'events' => array( 'conversion' ),
            'has_conversion_id' => true,
            'docs_url' => 'https://www.linkedin.com/help/lms/answer/a418880',
        ),
        'pinterest' => array(
            'name' => 'Pinterest',
            'id_field' => 'tag_id',
            'id_label' => 'Tag ID',
            'id_placeholder' => '예: 1234567890123',
            'id_pattern' => '/^\d{13}$/',
            'events' => array( 'pagevisit', 'viewcategory', 'search', 'addtocart', 'checkout', 'watchvideo', 'signup', 'lead', 'custom' ),
            'docs_url' => 'https://help.pinterest.com/en/business/article/install-the-pinterest-tag',
        ),
        'microsoft' => array(
            'name' => 'Microsoft Ads (Bing)',
            'id_field' => 'uet_tag_id',
            'id_label' => 'UET Tag ID',
            'id_placeholder' => '예: 12345678',
            'id_pattern' => '/^\d{8,12}$/',
            'events' => array( 'page_view', 'purchase', 'signup', 'subscribe', 'add_to_cart', 'begin_checkout', 'contact', 'download', 'submit_lead_form' ),
            'docs_url' => 'https://about.ads.microsoft.com/en-us/resources/training/universal-event-tracking',
        ),
        'criteo' => array(
            'name' => 'Criteo',
            'id_field' => 'partner_id',
            'id_label' => 'Partner ID',
            'id_placeholder' => '예: 12345',
            'id_pattern' => '/^\d{4,6}$/',
            'events' => array( 'viewHome', 'viewList', 'viewItem', 'viewBasket', 'trackTransaction' ),
            'docs_url' => 'https://help.criteo.com/kb/guide/en/onetag-implementation-guide-uAj2XCJqaI/',
        ),

        // 한국
        'naver' => array(
            'name' => '네이버 광고 (NAVER)',
            'id_field' => 'account_id',
            'id_label' => '네이버 광고 계정 ID',
            'id_placeholder' => '예: s_1234567890ab',
            'id_pattern' => '/^s_[a-z0-9]{12,14}$/',
            'has_conversion_type' => true,
            'events' => array( 'purchase', 'sign_up', 'add_to_cart', 'lead', 'page_view', 'custom' ),
            'docs_url' => 'https://searchad.naver.com/',
        ),
        'naver_gfa' => array(
            'name' => '네이버 GFA (성과형 디스플레이)',
            'id_field' => 'na_account_id',
            'id_label' => 'GFA 계정 ID',
            'id_placeholder' => '예: 가 1234567890123',
            'id_pattern' => '/^.{10,20}$/',
            'events' => array( 'purchase', 'sign_up', 'add_to_cart', 'lead' ),
            'docs_url' => 'https://displayad.naver.com/',
        ),
        'kakao' => array(
            'name' => '카카오 모먼트 (Kakao Moment)',
            'id_field' => 'track_id',
            'id_label' => '카카오 픽셀 ID',
            'id_placeholder' => '예: 1234567890123456789',
            'id_pattern' => '/^\d{18,20}$/',
            'events' => array( 'pageView', 'completeRegistration', 'search', 'viewContent', 'viewCart', 'addToCart', 'addToWishList', 'initiateCheckout', 'purchase', 'participation', 'signUp' ),
            'docs_url' => 'https://kakaoad.github.io/kakao-pixel/',
        ),
        'kakao_bizboard' => array(
            'name' => '카카오 비즈보드',
            'id_field' => 'bizboard_id',
            'id_label' => '비즈보드 광고 ID',
            'id_placeholder' => '예: abc123def456',
            'id_pattern' => '/^[a-z0-9]{10,16}$/',
            'events' => array( 'purchase', 'sign_up', 'add_to_cart' ),
            'docs_url' => 'https://business.kakao.com/',
        ),
        'toss' => array(
            'name' => '토스 애드 (Toss Ads)',
            'id_field' => 'pixel_id',
            'id_label' => '토스 픽셀 ID',
            'id_placeholder' => '예: toss_12345678',
            'id_pattern' => '/^toss_[a-z0-9]{8,12}$/i',
            'events' => array( 'page_view', 'purchase', 'add_to_cart', 'sign_up', 'lead' ),
            'docs_url' => 'https://toss.im/ads',
        ),
        'coupang' => array(
            'name' => '쿠팡 파트너스',
            'id_field' => 'affiliate_id',
            'id_label' => '파트너스 ID',
            'id_placeholder' => '예: AF1234567',
            'id_pattern' => '/^AF\d{7,10}$/',
            'events' => array( 'click', 'purchase' ),
            'docs_url' => 'https://partners.coupang.com/',
        ),

        // 일본
        'yahoo_japan' => array(
            'name' => 'Yahoo! JAPAN',
            'id_field' => 'yahoo_id',
            'id_label' => 'Yahoo ID',
            'id_placeholder' => '예: 1000123456',
            'id_pattern' => '/^\d{10}$/',
            'events' => array( 'page_view', 'purchase', 'sign_up', 'add_to_cart' ),
            'docs_url' => 'https://ads-help.yahoo.co.jp/',
        ),
        'line' => array(
            'name' => 'LINE Ads',
            'id_field' => 'tag_id',
            'id_label' => 'LINE Tag ID',
            'id_placeholder' => '예: 12345678-abcd-1234-efgh-567890abcdef',
            'id_pattern' => '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i',
            'events' => array( 'PageView', 'Conversion' ),
            'docs_url' => 'https://www.linebiz.com/',
        ),

        // 기타
        'taboola' => array(
            'name' => 'Taboola',
            'id_field' => 'account_id',
            'id_label' => 'Account ID',
            'id_placeholder' => '예: 1234567',
            'id_pattern' => '/^\d{6,10}$/',
            'events' => array( 'page_view', 'purchase', 'lead', 'search' ),
            'docs_url' => 'https://help.taboola.com/',
        ),
        'outbrain' => array(
            'name' => 'Outbrain',
            'id_field' => 'ob_pixel_id',
            'id_label' => 'Pixel ID',
            'id_placeholder' => '예: ob-12345678',
            'id_pattern' => '/^ob-\d{8,10}$/',
            'events' => array( 'PAGE_VIEW', 'PURCHASE', 'ADD_TO_CART' ),
            'docs_url' => 'https://www.outbrain.com/',
        ),
    );

    /**
     * 전환 이벤트 매핑 (표준화)
     */
    private $event_mapping = array(
        'page_view' => array(
            'meta' => 'PageView',
            'google_ads' => 'page_view',
            'ga4' => 'page_view',
            'tiktok' => 'PageView',
            'twitter' => 'PageView',
            'naver' => 'page_view',
            'kakao' => 'pageView',
            'toss' => 'page_view',
            'linkedin' => null,
            'pinterest' => 'pagevisit',
            'microsoft' => 'page_view',
        ),
        'view_content' => array(
            'meta' => 'ViewContent',
            'google_ads' => 'view_item',
            'ga4' => 'view_item',
            'tiktok' => 'ViewContent',
            'twitter' => 'ViewContent',
            'naver' => 'view_content',
            'kakao' => 'viewContent',
            'pinterest' => 'viewcategory',
        ),
        'add_to_cart' => array(
            'meta' => 'AddToCart',
            'google_ads' => 'add_to_cart',
            'ga4' => 'add_to_cart',
            'tiktok' => 'AddToCart',
            'twitter' => 'AddToCart',
            'naver' => 'add_to_cart',
            'kakao' => 'addToCart',
            'toss' => 'add_to_cart',
            'pinterest' => 'addtocart',
            'microsoft' => 'add_to_cart',
            'criteo' => 'viewBasket',
        ),
        'begin_checkout' => array(
            'meta' => 'InitiateCheckout',
            'google_ads' => 'begin_checkout',
            'ga4' => 'begin_checkout',
            'tiktok' => 'InitiateCheckout',
            'twitter' => 'InitiateCheckout',
            'naver' => 'checkout',
            'kakao' => 'initiateCheckout',
            'pinterest' => 'checkout',
            'microsoft' => 'begin_checkout',
        ),
        'purchase' => array(
            'meta' => 'Purchase',
            'google_ads' => 'purchase',
            'ga4' => 'purchase',
            'tiktok' => 'CompletePayment',
            'twitter' => 'Purchase',
            'naver' => 'purchase',
            'kakao' => 'purchase',
            'toss' => 'purchase',
            'pinterest' => 'checkout',
            'microsoft' => 'purchase',
            'criteo' => 'trackTransaction',
            'linkedin' => 'conversion',
        ),
        'sign_up' => array(
            'meta' => 'CompleteRegistration',
            'google_ads' => 'sign_up',
            'ga4' => 'sign_up',
            'tiktok' => 'CompleteRegistration',
            'twitter' => 'SignUp',
            'naver' => 'sign_up',
            'kakao' => 'signUp',
            'toss' => 'sign_up',
            'microsoft' => 'signup',
            'pinterest' => 'signup',
        ),
        'lead' => array(
            'meta' => 'Lead',
            'google_ads' => 'generate_lead',
            'ga4' => 'generate_lead',
            'tiktok' => 'SubmitForm',
            'twitter' => 'Lead',
            'naver' => 'lead',
            'kakao' => 'participation',
            'toss' => 'lead',
            'microsoft' => 'submit_lead_form',
            'pinterest' => 'lead',
        ),
        'search' => array(
            'meta' => 'Search',
            'google_ads' => 'search',
            'ga4' => 'search',
            'tiktok' => 'Search',
            'twitter' => 'Search',
            'kakao' => 'search',
            'pinterest' => 'search',
        ),
        'add_to_wishlist' => array(
            'meta' => 'AddToWishlist',
            'ga4' => 'add_to_wishlist',
            'tiktok' => 'AddToWishlist',
            'twitter' => 'AddToWishlist',
            'kakao' => 'addToWishList',
        ),
        'contact' => array(
            'meta' => 'Contact',
            'tiktok' => 'Contact',
            'microsoft' => 'contact',
        ),
    );

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->load_settings();
        $this->init_hooks();
    }

    /**
     * 설정 로드
     */
    private function load_settings() {
        $this->ad_settings = get_option( 'acf_nf_ad_pixels', array() );
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 프론트엔드 픽셀 스크립트 출력
        add_action( 'wp_head', array( $this, 'output_pixel_scripts' ), 1 );
        add_action( 'wp_footer', array( $this, 'output_pixel_noscript' ), 1 );

        // WooCommerce 전환 추적
        if ( class_exists( 'WooCommerce' ) ) {
            add_action( 'woocommerce_thankyou', array( $this, 'track_woo_purchase' ), 10, 1 );
            add_action( 'woocommerce_add_to_cart', array( $this, 'track_woo_add_to_cart' ), 10, 6 );
            add_action( 'woocommerce_after_single_product', array( $this, 'track_woo_view_content' ) );
            add_action( 'woocommerce_before_checkout_form', array( $this, 'track_woo_begin_checkout' ) );
        }

        // 회원가입 전환 추적
        add_action( 'user_register', array( $this, 'track_sign_up' ), 10, 1 );

        // AJAX 핸들러
        add_action( 'wp_ajax_acf_nf_track_conversion', array( $this, 'ajax_track_conversion' ) );
        add_action( 'wp_ajax_nopriv_acf_nf_track_conversion', array( $this, 'ajax_track_conversion' ) );

        // 관리자 메뉴
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        // REST API
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
    }

    /**
     * [v22.8.0] 픽셀 스크립트 출력 (트래픽 소스 연동)
     */
    public function output_pixel_scripts() {
        if ( is_admin() || $this->is_excluded_page() ) {
            return;
        }

        $output = '';

        foreach ( $this->supported_platforms as $platform_id => $platform ) {
            if ( $this->is_platform_enabled( $platform_id ) ) {
                $output .= $this->get_pixel_script( $platform_id );
            }
        }

        // [v22.8.0] 트래픽 소스 정보를 dataLayer에 푸시
        $traffic_source_script = $this->get_traffic_source_datalayer_script();

        if ( ! empty( $output ) || ! empty( $traffic_source_script ) ) {
            echo "\n<!-- ACF Nudge Flow - Ad Pixel Scripts (v22.8.0) -->\n";
            echo $traffic_source_script;
            echo $output;
            echo "<!-- End ACF Nudge Flow Ad Pixels -->\n\n";
        }
    }

    /**
     * [v22.8.0] 트래픽 소스 정보를 dataLayer에 전달하는 스크립트
     */
    private function get_traffic_source_datalayer_script() {
        return "
<script>
// ACF Nudge Flow - Traffic Source DataLayer
window.dataLayer = window.dataLayer || [];
(function() {
    var utmData = window.acfNFGetUTM ? window.acfNFGetUTM() : null;
    var adSource = window.acfNFGetAdSource ? window.acfNFGetAdSource() : null;
    var referrer = window.acfNFGetReferrer ? window.acfNFGetReferrer() : null;

    if (utmData || adSource || referrer) {
        window.dataLayer.push({
            'event': 'acf_nf_traffic_source',
            'acf_nf_utm_source': utmData ? utmData.utm_source : null,
            'acf_nf_utm_medium': utmData ? utmData.utm_medium : null,
            'acf_nf_utm_campaign': utmData ? utmData.utm_campaign : null,
            'acf_nf_ad_source': adSource ? adSource.source : null,
            'acf_nf_ad_platform': adSource ? adSource.platform : null,
            'acf_nf_ad_type': adSource ? adSource.type : null,
            'acf_nf_referrer_type': referrer ? referrer.type : null,
            'acf_nf_referrer_source': referrer ? referrer.source : null
        });
    }
})();
</script>
";
    }

    /**
     * noscript 폴백 출력
     */
    public function output_pixel_noscript() {
        if ( is_admin() || $this->is_excluded_page() ) {
            return;
        }

        $output = '';

        // Meta Pixel noscript
        if ( $this->is_platform_enabled( 'meta' ) ) {
            $pixel_id = $this->get_platform_id( 'meta' );
            $output .= '<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=' . esc_attr( $pixel_id ) . '&ev=PageView&noscript=1"/></noscript>';
        }

        if ( ! empty( $output ) ) {
            echo $output;
        }
    }

    /**
     * 플랫폼별 픽셀 스크립트 생성
     */
    private function get_pixel_script( $platform_id ) {
        $id = $this->get_platform_id( $platform_id );
        if ( empty( $id ) ) {
            return '';
        }

        $script = '';

        switch ( $platform_id ) {
            case 'meta':
                $script = $this->get_meta_pixel_script( $id );
                break;
            case 'google_ads':
                $script = $this->get_google_ads_script( $id );
                break;
            case 'ga4':
                $script = $this->get_ga4_script( $id );
                break;
            case 'tiktok':
                $script = $this->get_tiktok_script( $id );
                break;
            case 'twitter':
                $script = $this->get_twitter_script( $id );
                break;
            case 'naver':
                $script = $this->get_naver_script( $id );
                break;
            case 'naver_gfa':
                $script = $this->get_naver_gfa_script( $id );
                break;
            case 'kakao':
                $script = $this->get_kakao_script( $id );
                break;
            case 'toss':
                $script = $this->get_toss_script( $id );
                break;
            case 'linkedin':
                $script = $this->get_linkedin_script( $id );
                break;
            case 'pinterest':
                $script = $this->get_pinterest_script( $id );
                break;
            case 'microsoft':
                $script = $this->get_microsoft_script( $id );
                break;
            case 'criteo':
                $script = $this->get_criteo_script( $id );
                break;
            case 'line':
                $script = $this->get_line_script( $id );
                break;
            case 'taboola':
                $script = $this->get_taboola_script( $id );
                break;
            case 'yahoo_japan':
                $script = $this->get_yahoo_japan_script( $id );
                break;
        }

        return $script;
    }

    /**
     * Meta (Facebook) Pixel
     */
    private function get_meta_pixel_script( $pixel_id ) {
        return "
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '" . esc_js( $pixel_id ) . "');
fbq('track', 'PageView');
</script>
";
    }

    /**
     * Google Ads
     */
    private function get_google_ads_script( $conversion_id ) {
        return "
<script async src=\"https://www.googletagmanager.com/gtag/js?id=" . esc_attr( $conversion_id ) . "\"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '" . esc_js( $conversion_id ) . "');
</script>
";
    }

    /**
     * Google Analytics 4
     */
    private function get_ga4_script( $measurement_id ) {
        return "
<script async src=\"https://www.googletagmanager.com/gtag/js?id=" . esc_attr( $measurement_id ) . "\"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '" . esc_js( $measurement_id ) . "', {
    'send_page_view': true,
    'cookie_flags': 'SameSite=None;Secure'
});
</script>
";
    }

    /**
     * TikTok Pixel
     */
    private function get_tiktok_script( $pixel_id ) {
        return "
<script>
!function (w, d, t) {
  w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=[\"page\",\"track\",\"identify\",\"instances\",\"debug\",\"on\",\"off\",\"once\",\"ready\",\"alias\",\"group\",\"enableCookie\",\"disableCookie\"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i=\"https://analytics.tiktok.com/i18n/pixel/events.js\";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement(\"script\");o.type=\"text/javascript\",o.async=!0,o.src=i+\"?sdkid=\"+e+\"&lib=\"+t;var a=document.getElementsByTagName(\"script\")[0];a.parentNode.insertBefore(o,a)};
  ttq.load('" . esc_js( $pixel_id ) . "');
  ttq.page();
}(window, document, 'ttq');
</script>
";
    }

    /**
     * Twitter (X) Pixel
     */
    private function get_twitter_script( $pixel_id ) {
        return "
<script>
!function(e,t,n,s,u,a){e.twq||(s=e.twq=function(){s.exe?s.exe.apply(s,arguments):s.queue.push(arguments);
},s.version='1.1',s.queue=[],u=t.createElement(n),u.async=!0,u.src='https://static.ads-twitter.com/uwt.js',
a=t.getElementsByTagName(n)[0],a.parentNode.insertBefore(u,a))}(window,document,'script');
twq('config','" . esc_js( $pixel_id ) . "');
</script>
";
    }

    /**
     * 네이버 광고 (검색광고)
     */
    private function get_naver_script( $account_id ) {
        return "
<script type=\"text/javascript\" src=\"//wcs.naver.net/wcslog.js\"></script>
<script type=\"text/javascript\">
if(!wcs_add) var wcs_add = {};
wcs_add[\"wa\"] = \"" . esc_js( $account_id ) . "\";
if(window.wcs) {
    wcs_do();
}
</script>
";
    }

    /**
     * 네이버 GFA (성과형 디스플레이)
     */
    private function get_naver_gfa_script( $account_id ) {
        return "
<script type=\"text/javascript\" src=\"//wcs.naver.net/wcslog.js\"></script>
<script type=\"text/javascript\">
var _nasa={};
if(window.wcs) _nasa[\"cnv\"] = wcs.cnv(\"1\",\"1\");
</script>
";
    }

    /**
     * 카카오 모먼트
     */
    private function get_kakao_script( $track_id ) {
        return "
<script type=\"text/javascript\" charset=\"UTF-8\" src=\"//t1.daumcdn.net/kas/static/kp.js\"></script>
<script type=\"text/javascript\">
kakaoPixel('" . esc_js( $track_id ) . "').pageView();
</script>
";
    }

    /**
     * 토스 애드
     */
    private function get_toss_script( $pixel_id ) {
        return "
<script>
(function(t,o,s,s_id){
    t.TossPixel=t.TossPixel||function(){(t.TossPixel.q=t.TossPixel.q||[]).push(arguments)};
    var e=o.createElement('script');e.async=1;e.src=s;
    var n=o.getElementsByTagName('script')[0];n.parentNode.insertBefore(e,n);
    t.TossPixel('init',s_id);
    t.TossPixel('track','PageView');
})(window,document,'https://static.toss.im/ads/pixel.min.js','" . esc_js( $pixel_id ) . "');
</script>
";
    }

    /**
     * LinkedIn Insight Tag
     */
    private function get_linkedin_script( $partner_id ) {
        return "
<script type=\"text/javascript\">
_linkedin_partner_id = \"" . esc_js( $partner_id ) . "\";
window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
window._linkedin_data_partner_ids.push(_linkedin_partner_id);
</script>
<script type=\"text/javascript\">
(function(l) {
if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
window.lintrk.q=[]}
var s = document.getElementsByTagName(\"script\")[0];
var b = document.createElement(\"script\");
b.type = \"text/javascript\";b.async = true;
b.src = \"https://snap.licdn.com/li.lms-analytics/insight.min.js\";
s.parentNode.insertBefore(b, s);})(window.lintrk);
</script>
";
    }

    /**
     * Pinterest Tag
     */
    private function get_pinterest_script( $tag_id ) {
        return "
<script>
!function(e){if(!window.pintrk){window.pintrk = function () {
window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var
n=window.pintrk;n.queue=[],n.version=\"3.0\";var
t=document.createElement(\"script\");t.async=!0,t.src=e;var
r=document.getElementsByTagName(\"script\")[0];
r.parentNode.insertBefore(t,r)}}(\"https://s.pinimg.com/ct/core.js\");
pintrk('load', '" . esc_js( $tag_id ) . "');
pintrk('page');
</script>
";
    }

    /**
     * Microsoft Ads (Bing) UET
     */
    private function get_microsoft_script( $uet_tag_id ) {
        return "
<script>
(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:\"" . esc_js( $uet_tag_id ) . "\", enableAutoSpaTracking: true};o.q=w[u],w[u]=new UET(o),w[u].push(\"pageLoad\")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!==\"loaded\"&&s!==\"complete\"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,\"script\",\"//bat.bing.com/bat.js\",\"uetq\");
</script>
";
    }

    /**
     * Criteo OneTag
     */
    private function get_criteo_script( $partner_id ) {
        return "
<script type=\"text/javascript\" src=\"//dynamic.criteo.com/js/ld/ld.js?a=" . esc_attr( $partner_id ) . "\" async=\"true\"></script>
<script type=\"text/javascript\">
window.criteo_q = window.criteo_q || [];
var deviceType = /iPad/.test(navigator.userAgent) ? \"t\" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? \"m\" : \"d\";
window.criteo_q.push(
    { event: \"setAccount\", account: " . intval( $partner_id ) . " },
    { event: \"setSiteType\", type: deviceType },
    { event: \"viewHome\" }
);
</script>
";
    }

    /**
     * LINE Tag
     */
    private function get_line_script( $tag_id ) {
        return "
<script>
(function(g,d,o){
  g._ltq=g._ltq||[];g._lt=g._lt||function(){g._ltq.push(arguments)};
  var h=location.protocol==='https:'?'https://d.line-scdn.net':'http://d.line-cdn.net';
  var s=d.createElement('script');s.async=1;
  s.src=o||h+'/n/line_tag/public/release/v1/lt.js';
  var t=d.getElementsByTagName('script')[0];t.parentNode.insertBefore(s,t);
})(window, document);
_lt('init', {
    customerType: 'lap',
    tagId: '" . esc_js( $tag_id ) . "'
});
_lt('send', 'pv', ['" . esc_js( $tag_id ) . "']);
</script>
";
    }

    /**
     * Taboola Pixel
     */
    private function get_taboola_script( $account_id ) {
        return "
<script type=\"text/javascript\">
window._tfa = window._tfa || [];
window._tfa.push({notify: 'event', name: 'page_view', id: " . intval( $account_id ) . "});
!function (t, f, a, x) {
    if (!document.getElementById(x)) {
        t.async = 1;t.src = a;t.id=x;f.parentNode.insertBefore(t, f);
    }
}(document.createElement('script'),
document.getElementsByTagName('script')[0],
'//cdn.taboola.com/libtrc/unip/" . intval( $account_id ) . "/tfa.js',
'tb_tfa_script');
</script>
";
    }

    /**
     * Yahoo Japan
     */
    private function get_yahoo_japan_script( $yahoo_id ) {
        return "
<script type=\"text/javascript\">
(function(d,s,id){
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://s.yimg.jp/images/listing/tool/cv/ytag.js';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'yahoo-ydn-js'));
</script>
<script type=\"text/javascript\">
window.yjDataLayer = window.yjDataLayer || [];
function ytag() { yjDataLayer.push(arguments); }
ytag({\"type\":\"ycl_cookie\"});
ytag({\"type\":\"yjad_retargeting\", \"config\":{\"yahoo_retargeting_id\": \"" . esc_js( $yahoo_id ) . "\", \"yahoo_retargeting_label\": \"\", \"yahoo_retargeting_page_type\": \"other\"}});
</script>
";
    }

    // ==========================================
    // 전환 추적 메서드
    // ==========================================

    /**
     * WooCommerce 구매 전환 추적
     */
    public function track_woo_purchase( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return;
        }

        // 이미 추적된 주문인지 확인
        if ( get_post_meta( $order_id, '_acf_nf_conversion_tracked', true ) ) {
            return;
        }

        $order_total = $order->get_total();
        $currency = $order->get_currency();
        $items = array();

        foreach ( $order->get_items() as $item ) {
            $product = $item->get_product();
            $items[] = array(
                'id' => $product ? $product->get_id() : 0,
                'sku' => $product ? $product->get_sku() : '',
                'name' => $item->get_name(),
                'price' => $order->get_item_total( $item ),
                'quantity' => $item->get_quantity(),
                'category' => $product ? $this->get_product_category( $product ) : '',
            );
        }

        $conversion_data = array(
            'event' => 'purchase',
            'value' => $order_total,
            'currency' => $currency,
            'order_id' => $order_id,
            'items' => $items,
            'customer_email' => $order->get_billing_email(),
            'customer_phone' => $order->get_billing_phone(),
        );

        // 전환 스크립트 출력
        $this->output_conversion_script( $conversion_data );

        // 추적 완료 표시
        update_post_meta( $order_id, '_acf_nf_conversion_tracked', true );
    }

    /**
     * WooCommerce 장바구니 추가 추적
     */
    public function track_woo_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
        $product = wc_get_product( $variation_id ? $variation_id : $product_id );
        if ( ! $product ) {
            return;
        }

        $conversion_data = array(
            'event' => 'add_to_cart',
            'value' => $product->get_price() * $quantity,
            'currency' => get_woocommerce_currency(),
            'items' => array(
                array(
                    'id' => $product->get_id(),
                    'sku' => $product->get_sku(),
                    'name' => $product->get_name(),
                    'price' => $product->get_price(),
                    'quantity' => $quantity,
                    'category' => $this->get_product_category( $product ),
                ),
            ),
        );

        // 세션에 저장 (다음 페이지 로드 시 전송)
        WC()->session->set( 'acf_nf_pending_conversion', $conversion_data );
    }

    /**
     * WooCommerce 상품 조회 추적
     */
    public function track_woo_view_content() {
        global $product;
        if ( ! $product ) {
            return;
        }

        $conversion_data = array(
            'event' => 'view_content',
            'value' => $product->get_price(),
            'currency' => get_woocommerce_currency(),
            'items' => array(
                array(
                    'id' => $product->get_id(),
                    'sku' => $product->get_sku(),
                    'name' => $product->get_name(),
                    'price' => $product->get_price(),
                    'category' => $this->get_product_category( $product ),
                ),
            ),
        );

        $this->output_inline_conversion_script( $conversion_data );
    }

    /**
     * WooCommerce 결제 시작 추적
     */
    public function track_woo_begin_checkout() {
        $cart = WC()->cart;
        if ( ! $cart || $cart->is_empty() ) {
            return;
        }

        $items = array();
        foreach ( $cart->get_cart() as $cart_item ) {
            $product = $cart_item['data'];
            $items[] = array(
                'id' => $product->get_id(),
                'sku' => $product->get_sku(),
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'quantity' => $cart_item['quantity'],
                'category' => $this->get_product_category( $product ),
            );
        }

        $conversion_data = array(
            'event' => 'begin_checkout',
            'value' => $cart->get_cart_contents_total(),
            'currency' => get_woocommerce_currency(),
            'items' => $items,
        );

        $this->output_inline_conversion_script( $conversion_data );
    }

    /**
     * 회원가입 추적
     */
    public function track_sign_up( $user_id ) {
        $user = get_userdata( $user_id );
        if ( ! $user ) {
            return;
        }

        $conversion_data = array(
            'event' => 'sign_up',
            'user_id' => $user_id,
            'email' => $user->user_email,
        );

        // 세션에 저장
        set_transient( 'acf_nf_signup_conversion_' . $user_id, $conversion_data, 300 );
    }

    /**
     * 전환 스크립트 출력
     */
    private function output_conversion_script( $data ) {
        $event = $data['event'];
        $scripts = array();

        foreach ( $this->supported_platforms as $platform_id => $platform ) {
            if ( ! $this->is_platform_enabled( $platform_id ) ) {
                continue;
            }

            $mapped_event = $this->get_mapped_event( $event, $platform_id );
            if ( ! $mapped_event ) {
                continue;
            }

            $scripts[] = $this->get_conversion_call( $platform_id, $mapped_event, $data );
        }

        if ( ! empty( $scripts ) ) {
            echo "\n<script>\n" . implode( "\n", $scripts ) . "\n</script>\n";
        }
    }

    /**
     * 인라인 전환 스크립트 출력
     */
    private function output_inline_conversion_script( $data ) {
        add_action( 'wp_footer', function() use ( $data ) {
            $this->output_conversion_script( $data );
        }, 99 );
    }

    /**
     * 플랫폼별 전환 호출 생성
     */
    private function get_conversion_call( $platform_id, $event, $data ) {
        $value = isset( $data['value'] ) ? floatval( $data['value'] ) : 0;
        $currency = isset( $data['currency'] ) ? $data['currency'] : 'KRW';
        $items = isset( $data['items'] ) ? $data['items'] : array();
        $order_id = isset( $data['order_id'] ) ? $data['order_id'] : '';

        switch ( $platform_id ) {
            case 'meta':
                $content_ids = array_map( function( $item ) {
                    return $item['id'];
                }, $items );
                return "fbq('track', '" . esc_js( $event ) . "', {value: " . $value . ", currency: '" . esc_js( $currency ) . "', content_ids: " . wp_json_encode( $content_ids ) . ", content_type: 'product'});";

            case 'google_ads':
                $conversion_label = $this->get_platform_setting( 'google_ads', 'conversion_label' );
                $conversion_id = $this->get_platform_id( 'google_ads' );
                if ( $event === 'purchase' && $conversion_label ) {
                    return "gtag('event', 'conversion', {'send_to': '" . esc_js( $conversion_id ) . "/" . esc_js( $conversion_label ) . "', 'value': " . $value . ", 'currency': '" . esc_js( $currency ) . "', 'transaction_id': '" . esc_js( $order_id ) . "'});";
                }
                return "gtag('event', '" . esc_js( $event ) . "', {'value': " . $value . ", 'currency': '" . esc_js( $currency ) . "'});";

            case 'ga4':
                $ga4_items = array_map( function( $item ) {
                    return array(
                        'item_id' => $item['id'],
                        'item_name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                    );
                }, $items );
                return "gtag('event', '" . esc_js( $event ) . "', {value: " . $value . ", currency: '" . esc_js( $currency ) . "', transaction_id: '" . esc_js( $order_id ) . "', items: " . wp_json_encode( $ga4_items ) . "});";

            case 'tiktok':
                $content_ids = array_map( function( $item ) {
                    return (string) $item['id'];
                }, $items );
                return "ttq.track('" . esc_js( $event ) . "', {value: " . $value . ", currency: '" . esc_js( $currency ) . "', contents: [{content_id: " . wp_json_encode( $content_ids ) . ", content_type: 'product', quantity: 1}]});";

            case 'twitter':
                return "twq('event', 'tw-" . esc_js( $this->get_platform_id( 'twitter' ) ) . "-" . esc_js( $event ) . "', {value: " . $value . ", currency: '" . esc_js( $currency ) . "', num_items: " . count( $items ) . "});";

            case 'naver':
                $type = $event === 'purchase' ? '1' : '2';
                return "if(window.wcs) wcs.trans({\"wcs_add\":{\"wa\":\"" . esc_js( $this->get_platform_id( 'naver' ) ) . "\"},\"cnv\":wcs.cnv(\"" . $type . "\",\"" . $value . "\")});";

            case 'kakao':
                return "kakaoPixel('" . esc_js( $this->get_platform_id( 'kakao' ) ) . "')." . esc_js( $event ) . "();";

            case 'toss':
                return "TossPixel('track', '" . esc_js( $event ) . "', {value: " . $value . ", currency: '" . esc_js( $currency ) . "'});";

            case 'linkedin':
                $conversion_id = $this->get_platform_setting( 'linkedin', 'conversion_id' );
                if ( $conversion_id ) {
                    return "lintrk('track', { conversion_id: " . intval( $conversion_id ) . " });";
                }
                return '';

            case 'pinterest':
                return "pintrk('track', '" . esc_js( $event ) . "', {value: " . $value . ", currency: '" . esc_js( $currency ) . "'});";

            case 'microsoft':
                return "window.uetq = window.uetq || []; window.uetq.push('event', '" . esc_js( $event ) . "', {'revenue_value': " . $value . ", 'currency': '" . esc_js( $currency ) . "'});";

            case 'criteo':
                if ( $event === 'trackTransaction' ) {
                    $criteo_items = array_map( function( $item ) {
                        return array( 'id' => $item['id'], 'price' => $item['price'], 'quantity' => $item['quantity'] );
                    }, $items );
                    return "window.criteo_q.push({event: 'trackTransaction', id: '" . esc_js( $order_id ) . "', item: " . wp_json_encode( $criteo_items ) . "});";
                }
                return '';

            default:
                return '';
        }
    }

    /**
     * 이벤트 매핑 조회
     */
    private function get_mapped_event( $standard_event, $platform_id ) {
        if ( isset( $this->event_mapping[ $standard_event ][ $platform_id ] ) ) {
            return $this->event_mapping[ $standard_event ][ $platform_id ];
        }
        return null;
    }

    // ==========================================
    // 유틸리티 메서드
    // ==========================================

    /**
     * 플랫폼 활성화 여부 확인
     */
    public function is_platform_enabled( $platform_id ) {
        return ! empty( $this->ad_settings[ $platform_id ]['enabled'] ) 
               && ! empty( $this->ad_settings[ $platform_id ]['id'] );
    }

    /**
     * 플랫폼 ID 조회
     */
    public function get_platform_id( $platform_id ) {
        return isset( $this->ad_settings[ $platform_id ]['id'] ) 
               ? $this->ad_settings[ $platform_id ]['id'] 
               : '';
    }

    /**
     * 플랫폼 설정 조회
     */
    public function get_platform_setting( $platform_id, $key ) {
        return isset( $this->ad_settings[ $platform_id ][ $key ] ) 
               ? $this->ad_settings[ $platform_id ][ $key ] 
               : '';
    }

    /**
     * 제외 페이지 확인
     */
    private function is_excluded_page() {
        // 관리자 페이지 제외
        if ( is_admin() ) {
            return true;
        }

        // 관리자 역할 제외 (설정에 따라)
        if ( current_user_can( 'manage_options' ) ) {
            $settings = get_option( 'acf_nudge_flow_settings', array() );
            if ( ! empty( $settings['exclude_admins_from_tracking'] ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * 상품 카테고리 조회
     */
    private function get_product_category( $product ) {
        $terms = get_the_terms( $product->get_id(), 'product_cat' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            return $terms[0]->name;
        }
        return '';
    }

    /**
     * 지원 플랫폼 목록 조회
     */
    public function get_supported_platforms() {
        return $this->supported_platforms;
    }

    /**
     * 활성화된 플랫폼 목록 조회
     */
    public function get_enabled_platforms() {
        $enabled = array();
        foreach ( $this->supported_platforms as $id => $platform ) {
            if ( $this->is_platform_enabled( $id ) ) {
                $enabled[ $id ] = $platform;
            }
        }
        return $enabled;
    }

    // ==========================================
    // AJAX 핸들러
    // ==========================================

    /**
     * AJAX 전환 추적
     */
    public function ajax_track_conversion() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $event = isset( $_POST['event'] ) ? sanitize_text_field( $_POST['event'] ) : '';
        $data = isset( $_POST['data'] ) ? $_POST['data'] : array();

        if ( empty( $event ) ) {
            wp_send_json_error( 'Event required' );
        }

        // 데이터 정제
        $conversion_data = array(
            'event' => $event,
            'value' => isset( $data['value'] ) ? floatval( $data['value'] ) : 0,
            'currency' => isset( $data['currency'] ) ? sanitize_text_field( $data['currency'] ) : 'KRW',
            'items' => isset( $data['items'] ) ? $data['items'] : array(),
            'order_id' => isset( $data['order_id'] ) ? sanitize_text_field( $data['order_id'] ) : '',
        );

        // 전환 기록
        $this->record_conversion( $conversion_data );

        wp_send_json_success();
    }

    /**
     * 전환 기록 (DB 저장)
     */
    private function record_conversion( $data ) {
        global $wpdb;

        $table = $wpdb->prefix . 'acf_nf_conversions';

        $wpdb->insert( $table, array(
            'event_type' => $data['event'],
            'value' => $data['value'],
            'currency' => $data['currency'],
            'order_id' => $data['order_id'],
            'items' => wp_json_encode( $data['items'] ),
            'user_id' => get_current_user_id(),
            'visitor_id' => isset( $_COOKIE['acf_nf_visitor_id'] ) ? sanitize_text_field( $_COOKIE['acf_nf_visitor_id'] ) : '',
            'utm_source' => isset( $_COOKIE['acf_nf_utm_source'] ) ? sanitize_text_field( $_COOKIE['acf_nf_utm_source'] ) : '',
            'utm_medium' => isset( $_COOKIE['acf_nf_utm_medium'] ) ? sanitize_text_field( $_COOKIE['acf_nf_utm_medium'] ) : '',
            'utm_campaign' => isset( $_COOKIE['acf_nf_utm_campaign'] ) ? sanitize_text_field( $_COOKIE['acf_nf_utm_campaign'] ) : '',
            'page_url' => isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( $_SERVER['HTTP_REFERER'] ) : '',
            'created_at' => current_time( 'mysql' ),
        ) );
    }

    // ==========================================
    // 관리자 메뉴
    // ==========================================

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_submenu_page(
            'acf-nudge-flow',
            __( '광고 픽셀 설정', 'acf-nudge-flow' ),
            __( '광고 픽셀', 'acf-nudge-flow' ),
            'manage_options',
            'acf-nudge-flow-pixels',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * 설정 등록
     */
    public function register_settings() {
        register_setting( 'acf_nf_ad_pixels', 'acf_nf_ad_pixels', array( $this, 'sanitize_settings' ) );
    }

    /**
     * 설정 정제
     */
    public function sanitize_settings( $input ) {
        $sanitized = array();

        foreach ( $this->supported_platforms as $platform_id => $platform ) {
            if ( isset( $input[ $platform_id ] ) ) {
                $sanitized[ $platform_id ] = array(
                    'enabled' => ! empty( $input[ $platform_id ]['enabled'] ),
                    'id' => sanitize_text_field( $input[ $platform_id ]['id'] ?? '' ),
                );

                // 추가 필드
                if ( ! empty( $platform['has_conversion_label'] ) ) {
                    $sanitized[ $platform_id ]['conversion_label'] = sanitize_text_field( $input[ $platform_id ]['conversion_label'] ?? '' );
                }
                if ( ! empty( $platform['has_access_token'] ) ) {
                    $sanitized[ $platform_id ]['access_token'] = sanitize_text_field( $input[ $platform_id ]['access_token'] ?? '' );
                }
                if ( ! empty( $platform['has_conversion_id'] ) ) {
                    $sanitized[ $platform_id ]['conversion_id'] = sanitize_text_field( $input[ $platform_id ]['conversion_id'] ?? '' );
                }
            }
        }

        return $sanitized;
    }

    /**
     * 관리자 페이지 렌더링
     */
    public function render_admin_page() {
        include ACF_NUDGE_FLOW_PLUGIN_DIR . 'admin/views/ad-pixels-settings.php';
    }

    // ==========================================
    // REST API
    // ==========================================

    /**
     * REST API 라우트 등록
     */
    public function register_rest_routes() {
        $namespace = 'acf-nudge-flow/v1';

        register_rest_route( $namespace, '/pixels', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_pixels' ),
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            },
        ));

        register_rest_route( $namespace, '/pixels', array(
            'methods' => 'POST',
            'callback' => array( $this, 'rest_update_pixels' ),
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            },
        ));

        register_rest_route( $namespace, '/track', array(
            'methods' => 'POST',
            'callback' => array( $this, 'rest_track_conversion' ),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * REST: 픽셀 설정 조회
     */
    public function rest_get_pixels( $request ) {
        return new WP_REST_Response( array(
            'platforms' => $this->supported_platforms,
            'settings' => $this->ad_settings,
            'enabled' => $this->get_enabled_platforms(),
        ), 200 );
    }

    /**
     * REST: 픽셀 설정 업데이트
     */
    public function rest_update_pixels( $request ) {
        $params = $request->get_json_params();
        $sanitized = $this->sanitize_settings( $params );
        update_option( 'acf_nf_ad_pixels', $sanitized );
        $this->ad_settings = $sanitized;

        return new WP_REST_Response( array(
            'success' => true,
            'settings' => $sanitized,
        ), 200 );
    }

    /**
     * REST: 전환 추적
     */
    public function rest_track_conversion( $request ) {
        $params = $request->get_json_params();
        
        if ( empty( $params['event'] ) ) {
            return new WP_REST_Response( array( 'error' => 'Event required' ), 400 );
        }

        $this->record_conversion( array(
            'event' => sanitize_text_field( $params['event'] ),
            'value' => floatval( $params['value'] ?? 0 ),
            'currency' => sanitize_text_field( $params['currency'] ?? 'KRW' ),
            'items' => $params['items'] ?? array(),
            'order_id' => sanitize_text_field( $params['order_id'] ?? '' ),
        ));

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    /**
     * [v22.7.0] 모든 테이블 생성
     */
    public function create_tables() {
        $this->create_conversion_table();
        $this->create_utm_tracking_table();
        $this->create_funnel_table();
    }

    public function create_conversion_table() {
        global $wpdb;

        $table = $wpdb->prefix . 'acf_nf_conversions';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            value decimal(15,2) DEFAULT 0,
            currency varchar(10) DEFAULT 'KRW',
            order_id varchar(50) DEFAULT NULL,
            items longtext DEFAULT NULL,
            user_id bigint(20) unsigned DEFAULT 0,
            visitor_id varchar(100) DEFAULT NULL,
            utm_source varchar(100) DEFAULT NULL,
            utm_medium varchar(100) DEFAULT NULL,
            utm_campaign varchar(100) DEFAULT NULL,
            utm_term varchar(100) DEFAULT NULL,
            utm_content varchar(100) DEFAULT NULL,
            referrer varchar(500) DEFAULT NULL,
            page_url varchar(500) DEFAULT NULL,
            device_type varchar(20) DEFAULT NULL,
            browser varchar(50) DEFAULT NULL,
            country_code varchar(2) DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            platforms_tracked text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_event_type (event_type),
            KEY idx_user_id (user_id),
            KEY idx_visitor_id (visitor_id),
            KEY idx_utm_source (utm_source),
            KEY idx_created_at (created_at)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * [v22.8.0] UTM 추적 테이블 생성 (확장된 파라미터 지원)
     */
    public function create_utm_tracking_table() {
        global $wpdb;

        $table = $wpdb->prefix . 'acf_nf_utm_tracking';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            visitor_id varchar(100) NOT NULL,
            user_id bigint(20) unsigned DEFAULT 0,
            session_id varchar(100) DEFAULT NULL,

            -- UTM 파라미터
            utm_source varchar(100) DEFAULT NULL,
            utm_medium varchar(100) DEFAULT NULL,
            utm_campaign varchar(255) DEFAULT NULL,
            utm_term varchar(255) DEFAULT NULL,
            utm_content varchar(255) DEFAULT NULL,

            -- 글로벌 광고 클릭 ID (자동 태깅)
            gclid varchar(100) DEFAULT NULL,
            gad_source varchar(50) DEFAULT NULL,
            gbraid varchar(100) DEFAULT NULL,
            wbraid varchar(100) DEFAULT NULL,
            fbclid varchar(100) DEFAULT NULL,
            ttclid varchar(100) DEFAULT NULL,
            twclid varchar(100) DEFAULT NULL,
            msclkid varchar(100) DEFAULT NULL,
            li_fat_id varchar(100) DEFAULT NULL,
            epik varchar(100) DEFAULT NULL,
            sccid varchar(100) DEFAULT NULL,

            -- 네이버 광고 파라미터
            n_media varchar(50) DEFAULT NULL,
            n_query varchar(255) DEFAULT NULL,
            n_rank varchar(10) DEFAULT NULL,
            n_ad_group varchar(100) DEFAULT NULL,
            n_ad varchar(100) DEFAULT NULL,
            n_keyword_id varchar(100) DEFAULT NULL,
            n_keyword varchar(255) DEFAULT NULL,
            n_campaign_type varchar(50) DEFAULT NULL,
            n_ad_group_type varchar(50) DEFAULT NULL,
            n_match varchar(10) DEFAULT NULL,
            n_mall_pid varchar(100) DEFAULT NULL,
            n_mall_id varchar(100) DEFAULT NULL,
            na_source varchar(50) DEFAULT NULL,
            na_medium varchar(50) DEFAULT NULL,
            na_campaign varchar(255) DEFAULT NULL,
            na_adset varchar(255) DEFAULT NULL,
            na_ad varchar(255) DEFAULT NULL,

            -- 카카오 광고 파라미터
            kakao_campaign varchar(100) DEFAULT NULL,
            kakao_adgrp varchar(100) DEFAULT NULL,
            kakao_creative varchar(100) DEFAULT NULL,
            kakao_keyword varchar(255) DEFAULT NULL,
            dkwid varchar(100) DEFAULT NULL,
            dkw varchar(255) DEFAULT NULL,

            -- 기타 광고 파라미터
            dablead varchar(100) DEFAULT NULL,
            ti varchar(100) DEFAULT NULL,

            -- 광고 소스 감지 결과
            detected_ad_source varchar(50) DEFAULT NULL,
            detected_ad_platform varchar(100) DEFAULT NULL,
            detected_ad_type varchar(50) DEFAULT NULL,
            ad_source_details longtext DEFAULT NULL,

            -- 리퍼러 정보
            referrer_url varchar(500) DEFAULT NULL,
            referrer_domain varchar(255) DEFAULT NULL,
            referrer_type varchar(50) DEFAULT NULL,
            referrer_source varchar(100) DEFAULT NULL,
            referrer_name varchar(100) DEFAULT NULL,

            -- 랜딩 페이지
            landing_page varchar(500) DEFAULT NULL,
            landing_path varchar(255) DEFAULT NULL,

            -- 디바이스/환경 정보
            device_type varchar(20) DEFAULT NULL,
            browser varchar(50) DEFAULT NULL,
            os varchar(50) DEFAULT NULL,
            screen_resolution varchar(20) DEFAULT NULL,
            viewport varchar(20) DEFAULT NULL,
            user_language varchar(10) DEFAULT NULL,
            country_code varchar(2) DEFAULT NULL,
            city varchar(100) DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,

            -- 타임스탬프
            first_touch_at datetime DEFAULT CURRENT_TIMESTAMP,
            last_touch_at datetime DEFAULT CURRENT_TIMESTAMP,
            touch_count int(11) DEFAULT 1,

            -- 전환 추적
            converted tinyint(1) DEFAULT 0,
            conversion_value decimal(15,2) DEFAULT 0,
            conversion_at datetime DEFAULT NULL,

            PRIMARY KEY (id),
            KEY idx_visitor_id (visitor_id),
            KEY idx_user_id (user_id),
            KEY idx_session_id (session_id),
            KEY idx_utm_source (utm_source),
            KEY idx_utm_campaign (utm_campaign),
            KEY idx_gclid (gclid),
            KEY idx_fbclid (fbclid),
            KEY idx_ttclid (ttclid),
            KEY idx_msclkid (msclkid),
            KEY idx_n_media (n_media),
            KEY idx_kakao_campaign (kakao_campaign),
            KEY idx_detected_ad_source (detected_ad_source),
            KEY idx_referrer_type (referrer_type),
            KEY idx_first_touch_at (first_touch_at),
            KEY idx_converted (converted)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * [v22.7.0] 퍼널 분석 테이블 생성
     */
    public function create_funnel_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // 퍼널 정의 테이블
        $funnel_table = $wpdb->prefix . 'acf_nf_funnels';
        $sql_funnel = "CREATE TABLE IF NOT EXISTS $funnel_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text DEFAULT NULL,
            steps longtext NOT NULL,
            conversion_goal varchar(100) DEFAULT NULL,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_is_active (is_active)
        ) $charset_collate;";

        // 퍼널 진행 추적 테이블
        $progress_table = $wpdb->prefix . 'acf_nf_funnel_progress';
        $sql_progress = "CREATE TABLE IF NOT EXISTS $progress_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            funnel_id bigint(20) unsigned NOT NULL,
            visitor_id varchar(100) NOT NULL,
            user_id bigint(20) unsigned DEFAULT 0,
            session_id varchar(100) DEFAULT NULL,
            current_step int(11) DEFAULT 0,
            steps_completed text DEFAULT NULL,
            step_timestamps text DEFAULT NULL,
            utm_source varchar(100) DEFAULT NULL,
            utm_campaign varchar(255) DEFAULT NULL,
            is_completed tinyint(1) DEFAULT 0,
            is_abandoned tinyint(1) DEFAULT 0,
            abandoned_at_step int(11) DEFAULT NULL,
            time_in_funnel int(11) DEFAULT 0,
            started_at datetime DEFAULT CURRENT_TIMESTAMP,
            completed_at datetime DEFAULT NULL,
            last_activity_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_funnel_id (funnel_id),
            KEY idx_visitor_id (visitor_id),
            KEY idx_user_id (user_id),
            KEY idx_current_step (current_step),
            KEY idx_is_completed (is_completed),
            KEY idx_is_abandoned (is_abandoned),
            KEY idx_started_at (started_at)
        ) $charset_collate;";

        // 퍼널 단계별 통계 테이블
        $stats_table = $wpdb->prefix . 'acf_nf_funnel_stats';
        $sql_stats = "CREATE TABLE IF NOT EXISTS $stats_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            funnel_id bigint(20) unsigned NOT NULL,
            step_index int(11) NOT NULL,
            date date NOT NULL,
            entrances int(11) DEFAULT 0,
            exits int(11) DEFAULT 0,
            completions int(11) DEFAULT 0,
            avg_time_seconds int(11) DEFAULT 0,
            bounce_rate decimal(5,2) DEFAULT 0,
            conversion_rate decimal(5,2) DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY idx_funnel_step_date (funnel_id, step_index, date),
            KEY idx_date (date)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_funnel );
        dbDelta( $sql_progress );
        dbDelta( $sql_stats );

        // 기본 이커머스 퍼널 생성
        $this->maybe_create_default_funnels();
    }

    /**
     * [v22.7.0] 기본 퍼널 생성
     */
    private function maybe_create_default_funnels() {
        global $wpdb;

        $table = $wpdb->prefix . 'acf_nf_funnels';
        $exists = $wpdb->get_var( "SELECT COUNT(*) FROM $table" );

        if ( $exists > 0 ) {
            return;
        }

        // 기본 이커머스 퍼널
        $ecommerce_funnel = array(
            'name' => __( '이커머스 구매 퍼널', 'acf-nudge-flow' ),
            'description' => __( '방문자가 상품을 구매하기까지의 여정을 추적합니다.', 'acf-nudge-flow' ),
            'steps' => wp_json_encode( array(
                array(
                    'name' => __( '홈페이지', 'acf-nudge-flow' ),
                    'type' => 'page_view',
                    'condition' => 'is_front_page',
                ),
                array(
                    'name' => __( '상품 목록', 'acf-nudge-flow' ),
                    'type' => 'page_view',
                    'condition' => 'is_shop',
                ),
                array(
                    'name' => __( '상품 상세', 'acf-nudge-flow' ),
                    'type' => 'page_view',
                    'condition' => 'is_product',
                ),
                array(
                    'name' => __( '장바구니 추가', 'acf-nudge-flow' ),
                    'type' => 'event',
                    'condition' => 'add_to_cart',
                ),
                array(
                    'name' => __( '장바구니', 'acf-nudge-flow' ),
                    'type' => 'page_view',
                    'condition' => 'is_cart',
                ),
                array(
                    'name' => __( '결제 페이지', 'acf-nudge-flow' ),
                    'type' => 'page_view',
                    'condition' => 'is_checkout',
                ),
                array(
                    'name' => __( '구매 완료', 'acf-nudge-flow' ),
                    'type' => 'event',
                    'condition' => 'purchase',
                ),
            ) ),
            'conversion_goal' => 'purchase',
            'is_active' => 1,
        );

        $wpdb->insert( $table, $ecommerce_funnel );

        // 회원가입 퍼널
        $signup_funnel = array(
            'name' => __( '회원가입 퍼널', 'acf-nudge-flow' ),
            'description' => __( '방문자가 회원가입까지의 여정을 추적합니다.', 'acf-nudge-flow' ),
            'steps' => wp_json_encode( array(
                array(
                    'name' => __( '랜딩 페이지', 'acf-nudge-flow' ),
                    'type' => 'page_view',
                    'condition' => 'landing_page',
                ),
                array(
                    'name' => __( '가입 페이지', 'acf-nudge-flow' ),
                    'type' => 'page_view',
                    'condition' => 'is_register_page',
                ),
                array(
                    'name' => __( '가입 완료', 'acf-nudge-flow' ),
                    'type' => 'event',
                    'condition' => 'sign_up',
                ),
            ) ),
            'conversion_goal' => 'sign_up',
            'is_active' => 1,
        );

        $wpdb->insert( $table, $signup_funnel );
    }
}

/**
 * 전역 함수: 광고 픽셀 관리자 인스턴스 반환
 */
function acf_nudge_flow_pixels() {
    return ACF_Nudge_Flow_Ad_Pixel_Manager::instance();
}
