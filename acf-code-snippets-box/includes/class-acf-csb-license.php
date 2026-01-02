<?php
/**
 * ACF Code Snippets Box - License & Feature Access
 *
 * 라이선스 확인 및 요금제별 기능 접근 제어
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * License 클래스
 */
class ACF_CSB_License {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 요금제 계층 (낮은 순 → 높은 순)
     */
    const TIER_FREE      = 0;
    const TIER_BASIC     = 1;
    const TIER_PREMIUM   = 2;
    const TIER_UNLIMITED = 3;
    const TIER_PARTNER   = 4;
    const TIER_MASTER    = 5;

    /**
     * 요금제명 → 레벨 매핑
     */
    private static $tier_levels = array(
        'free'      => self::TIER_FREE,
        'basic'     => self::TIER_BASIC,
        'premium'   => self::TIER_PREMIUM,
        'unlimited' => self::TIER_UNLIMITED,
        'partner'   => self::TIER_PARTNER,
        'master'    => self::TIER_MASTER,
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
     * 현재 라이선스 타입 가져오기
     *
     * @return string
     */
    public static function get_license_type() {
        // ACF CSS 메인 플러그인의 라이선스 확인
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            return strtolower( JJ_STYLE_GUIDE_LICENSE_TYPE );
        }

        // ACF CSS Neural Link의 라이선스 확인
        $license = get_option( 'acf_css_neural_link_license', array() );
        if ( ! empty( $license['type'] ) ) {
            return strtolower( $license['type'] );
        }

        return 'free';
    }

    /**
     * 현재 사용자 타입 가져오기
     *
     * @return string
     */
    public static function get_user_type() {
        if ( defined( 'JJ_STYLE_GUIDE_USER_TYPE' ) ) {
            return strtolower( JJ_STYLE_GUIDE_USER_TYPE );
        }
        return 'standard';
    }

    /**
     * 현재 요금제 레벨 가져오기
     *
     * @return int
     */
    public static function get_tier_level() {
        $license_type = self::get_license_type();
        $user_type = self::get_user_type();

        // 사용자 타입이 partner/master면 해당 레벨 반환
        if ( $user_type === 'partner' ) {
            return self::TIER_PARTNER;
        }
        if ( $user_type === 'master' ) {
            return self::TIER_MASTER;
        }

        // 라이선스 타입에 따른 레벨
        return isset( self::$tier_levels[ $license_type ] ) 
            ? self::$tier_levels[ $license_type ] 
            : self::TIER_FREE;
    }

    /**
     * Pro 사용자 여부 (Basic 이상)
     *
     * @return bool
     */
    public static function is_pro() {
        return self::get_tier_level() >= self::TIER_BASIC;
    }

    /**
     * Premium 사용자 여부
     *
     * @return bool
     */
    public static function is_premium() {
        return self::get_tier_level() >= self::TIER_PREMIUM;
    }

    /**
     * Unlimited 사용자 여부
     *
     * @return bool
     */
    public static function is_unlimited() {
        return self::get_tier_level() >= self::TIER_UNLIMITED;
    }

    /**
     * Partner 사용자 여부
     *
     * @return bool
     */
    public static function is_partner() {
        return self::get_tier_level() >= self::TIER_PARTNER;
    }

    /**
     * Master 사용자 여부
     *
     * @return bool
     */
    public static function is_master() {
        return self::get_tier_level() >= self::TIER_MASTER;
    }

    /**
     * 특정 요금제 이상인지 확인
     *
     * @param string $required_tier 필요한 최소 요금제
     * @return bool
     */
    public static function has_access( $required_tier ) {
        $required_level = isset( self::$tier_levels[ strtolower( $required_tier ) ] ) 
            ? self::$tier_levels[ strtolower( $required_tier ) ] 
            : self::TIER_FREE;

        return self::get_tier_level() >= $required_level;
    }

    /**
     * 기능별 접근 권한 확인
     *
     * @param string $feature 기능명
     * @return bool
     */
    public static function can_access_feature( $feature ) {
        $feature_requirements = self::get_feature_requirements();

        if ( ! isset( $feature_requirements[ $feature ] ) ) {
            return true; // 정의되지 않은 기능은 기본 허용
        }

        return self::has_access( $feature_requirements[ $feature ] );
    }

    /**
     * 기능별 최소 요금제 요구사항
     *
     * @return array
     */
    public static function get_feature_requirements() {
        return array(
            // 기본 기능 (Free)
            'snippets_basic'           => 'free',
            'snippets_limit_10'        => 'free',
            'presets_basic'            => 'free',
            'condition_basic'          => 'free',
            'syntax_highlighting'      => 'free',

            // Pro Basic
            'snippets_unlimited'       => 'basic',
            'condition_taxonomy'       => 'basic',
            'condition_url'            => 'basic',
            'condition_user_role'      => 'basic',
            'presets_woocommerce'      => 'basic',
            'import_export'            => 'basic',
            'autocomplete_basic'       => 'basic',

            // Pro Premium
            'condition_time'           => 'premium',
            'condition_day'            => 'premium',
            'condition_custom_php'     => 'premium',
            'condition_groups'         => 'premium',
            'presets_all'              => 'premium',
            'version_history'          => 'premium',
            'live_preview'             => 'premium',
            'autocomplete_advanced'    => 'premium',
            'ai_suggestions'           => 'premium',

            // Pro Unlimited
            'multisite'                => 'unlimited',
            'white_label'              => 'unlimited',
            'cloud_sync'               => 'unlimited',
            'condition_woocommerce'    => 'unlimited',
            'priority_support'         => 'unlimited',

            // Partner
            'client_dashboard'         => 'partner',
            'unlimited_sites'          => 'partner',
            'reseller'                 => 'partner',

            // Master
            'source_access'            => 'master',
            'beta_features'            => 'master',
            'developer_tools'          => 'master',
        );
    }

    /**
     * 스니펫 제한 수 가져오기
     *
     * @return int (-1 = 무제한)
     */
    public static function get_snippet_limit() {
        $tier = self::get_tier_level();

        if ( $tier >= self::TIER_BASIC ) {
            return -1; // 무제한
        }

        return 10; // Free는 10개 제한
    }

    /**
     * 라이선스 정보 배열 반환
     *
     * @return array
     */
    public static function get_license_info() {
        return array(
            'license_type' => self::get_license_type(),
            'user_type'    => self::get_user_type(),
            'tier_level'   => self::get_tier_level(),
            'is_pro'       => self::is_pro(),
            'is_premium'   => self::is_premium(),
            'is_unlimited' => self::is_unlimited(),
            'is_partner'   => self::is_partner(),
            'is_master'    => self::is_master(),
        );
    }

    /**
     * 업그레이드 안내 메시지
     *
     * @param string $feature 기능명
     * @return string
     */
    public static function get_upgrade_message( $feature ) {
        $requirements = self::get_feature_requirements();
        $required_tier = isset( $requirements[ $feature ] ) ? $requirements[ $feature ] : 'basic';

        $tier_names = array(
            'basic'     => __( 'Pro Basic', 'acf-code-snippets-box' ),
            'premium'   => __( 'Pro Premium', 'acf-code-snippets-box' ),
            'unlimited' => __( 'Pro Unlimited', 'acf-code-snippets-box' ),
            'partner'   => __( 'Partner', 'acf-code-snippets-box' ),
            'master'    => __( 'Master', 'acf-code-snippets-box' ),
        );

        $tier_name = isset( $tier_names[ $required_tier ] ) ? $tier_names[ $required_tier ] : $required_tier;

        return sprintf(
            /* translators: %s: required tier name */
            __( '이 기능은 %s 이상의 요금제에서 사용할 수 있습니다.', 'acf-code-snippets-box' ),
            $tier_name
        );
    }
}
