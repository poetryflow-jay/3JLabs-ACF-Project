<?php
/**
 * J&J 버전별 기능 제한 체크 클래스
 * 
 * 각 버전별로 사용 가능한 기능을 체크하고 제한을 적용합니다.
 * 
 * @version 3.8.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

final class JJ_Version_Features {

    private static $instance = null;
    private $current_edition = 'free';
    private $license_manager = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->license_manager = JJ_License_Manager::instance();
        
        // [Safety Lock] JJ_Edition_Controller가 있으면 거기서 에디션을 가져옴
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            $this->current_edition = JJ_Edition_Controller::instance()->get_current_edition();
            return;
        }

        // 보안 강화: 라이센스 매니저에서 검증된 라이센스 타입 사용
        $verified_license_type = $this->license_manager->get_current_license_type();
        
        // 현재 버전/에디션 확인 (검증된 라이센스 타입 우선 사용)
        $edition_map = array(
            'FREE'    => 'free',
            'BASIC'   => 'basic',
            'PREM'    => 'premium',
            'UNLIM'   => 'unlimited',
            'PARTNER' => 'partner',
            'MASTER'  => 'master',
        );

        if ( defined( 'JJ_STYLE_GUIDE_EDITION' ) ) {
            $expected_edition = isset( $edition_map[ $verified_license_type ] ) ? $edition_map[ $verified_license_type ] : 'free';
            
            // 정의된 에디션과 검증된 라이센스 타입이 일치하는지 확인
            if ( JJ_STYLE_GUIDE_EDITION === $expected_edition || 'master' === JJ_STYLE_GUIDE_EDITION ) {
                $this->current_edition = JJ_STYLE_GUIDE_EDITION;
            } else {
                $this->current_edition = $expected_edition;
            }
        } else {
            $this->current_edition = isset( $edition_map[ $verified_license_type ] ) ? $edition_map[ $verified_license_type ] : 'free';
        }
    }

    /**
     * 현재 버전/에디션 가져오기
     * 
     * @return string
     */
    public function get_current_edition() {
        return $this->current_edition;
    }

    /**
     * 기능 사용 가능 여부 확인
     * 
     * @param string $feature
     * @return bool
     */
    public function can_use_feature( $feature ) {
        // 라이센스 강제 실행 클래스 확인
        if ( class_exists( 'JJ_License_Enforcement' ) ) {
            $enforcement = JJ_License_Enforcement::instance();
            $can_use = $this->license_manager->can_use_feature( $feature );
            return apply_filters( 'jj_can_use_feature', $can_use, $feature );
        }
        
        return $this->license_manager->can_use_feature( $feature );
    }

    /**
     * 팔레트 사용 가능 여부
     * 
     * @param string $palette_type (brand, system, alternative, another, temp)
     * @return bool
     */
    public function can_use_palette( $palette_type ) {
        // [Safety Lock] MASTER 버전은 모든 기능 사용 가능
        if ( $this->current_edition === 'master' ) {
            return true;
        }

        // 코드 무결성 모니터 확인 (최우선)
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                return false; // 잠금 상태면 모든 기능 비활성화
            }
        }
        
        switch ( $palette_type ) {
            case 'brand':
                return true; // 모든 버전에서 사용 가능
            case 'system':
                return $this->is_at_least( 'basic' );
            case 'alternative':
            case 'another':
                return $this->is_at_least( 'premium' );
            case 'temp':
                return $this->is_at_least( 'basic' );
            default:
                return false;
        }
    }

    /**
     * 타이포그래피 태그 사용 가능 여부
     * 
     * @param string $tag (h1, h2, h3, h4, h5, h6, p)
     * @return bool
     */
    public function can_use_typography_tag( $tag ) {
        // [Safety Lock] MASTER 버전은 모든 기능 사용 가능
        if ( $this->current_edition === 'master' ) {
            return true;
        }

        // 코드 무결성 모니터 확인 (최우선)
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                return false; // 잠금 상태면 모든 기능 비활성화
            }
        }
        
        switch ( $this->current_edition ) {
            case 'free':
                return in_array( $tag, array( 'h1', 'h2', 'p' ) );
            case 'basic':
                return in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'p' ) );
            case 'premium':
            case 'unlimited':
            case 'partner':
                return true; // 모든 태그 사용 가능
            default:
                return false;
        }
    }

    /**
     * 버튼 타입 사용 가능 여부
     * 
     * @param string $button_type (primary, secondary, text)
     * @return bool
     */
    public function can_use_button_type( $button_type ) {
        // [Safety Lock] MASTER 버전은 모든 기능 사용 가능
        if ( $this->current_edition === 'master' ) {
            return true;
        }

        // 코드 무결성 모니터 확인 (최우선)
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                return false; // 잠금 상태면 모든 기능 비활성화
            }
        }
        
        switch ( $this->current_edition ) {
            case 'free':
                return $button_type === 'primary';
            case 'basic':
                return in_array( $button_type, array( 'primary', 'secondary' ) );
            case 'premium':
            case 'unlimited':
            case 'partner':
                return true; // 모든 버튼 타입 사용 가능
            default:
                return false;
        }
    }

    /**
     * 섹션별 내보내기/불러오기 사용 가능 여부
     * 
     * @return bool
     */
    public function can_use_section_export_import() {
        return $this->is_at_least( 'premium' );
    }

    /**
     * 전체 내보내기/불러오기 사용 가능 여부
     * 
     * @return bool
     */
    public function can_use_full_export_import() {
        return $this->is_at_least( 'basic' );
    }

    /**
     * 관리자 센터 접근 가능 여부
     * 
     * @param string $tab (general, admin-menu, section-layout, texts, colors, backup)
     * @return bool
     */
    public function can_access_admin_center( $tab = 'general' ) {
        // [Safety Lock] MASTER 버전은 모든 기능 사용 가능
        if ( $this->current_edition === 'master' ) {
            return true;
        }

        // 코드 무결성 모니터 확인 (최우선)
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                return false; // 잠금 상태면 모든 기능 비활성화
            }
        }
        
        if ( $this->current_edition === 'free' ) {
            return false; // Free 버전: 접근 불가
        }
        
        if ( $this->current_edition === 'basic' ) {
            // Basic 버전: General, Section Layout만 접근 가능
            return in_array( $tab, array( 'general', 'section-layout' ) );
        }
        
        // Premium 이상: 모든 탭 접근 가능
        return true;
    }

    /**
     * 실험실 센터 접근 가능 여부
     * 
     * @param string $section (scanner, overrides, supported-list)
     * @return bool
     */
    public function can_access_labs_center( $section = 'supported-list' ) {
        // [Safety Lock] MASTER 버전은 모든 기능 사용 가능
        if ( $this->current_edition === 'master' ) {
            return true;
        }

        // 코드 무결성 모니터 확인 (최우선)
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                return false; // 잠금 상태면 모든 기능 비활성화
            }
        }
        
        if ( $this->current_edition === 'free' ) {
            return false; // Free 버전: 접근 불가
        }
        
        if ( $this->current_edition === 'basic' ) {
            // Basic 버전: 공식 지원 목록만 접근 가능
            return $section === 'supported-list';
        }
        
        if ( $this->current_edition === 'premium' ) {
            // Premium 버전: 공식 지원 목록만 접근 가능 (스캐너/재정의 제한)
            return $section === 'supported-list';
        }
        
        // Unlimited 이상: 모든 섹션 접근 가능
        return true;
    }

    /**
     * 현재 에디션이 특정 등급 이상인지 확인
     */
    public function is_at_least( $target_edition ) {
        $hierarchy = array(
            'free'      => 0,
            'basic'     => 1,
            'premium'   => 2,
            'unlimited' => 2, // 프리미엄과 동일한 권한 수준
            'partner'   => 3,
            'master'    => 4
        );

        $current_level = isset( $hierarchy[ $this->current_edition ] ) ? $hierarchy[ $this->current_edition ] : 0;
        $target_level  = isset( $hierarchy[ $target_edition ] ) ? $hierarchy[ $target_edition ] : 0;

        return $current_level >= $target_level;
    }

    /**
     * 테마 어댑터 지원 수
     * 
     * @return int
     */
    public function get_max_themes() {
        if ( $this->current_edition === 'master' || $this->current_edition === 'partner' ) {
            return 999;
        }
        switch ( $this->current_edition ) {
            case 'free':
                return 2;
            case 'basic':
                return 5;
            case 'premium':
            case 'unlimited':
                return 10;
            default:
                return 0;
        }
    }

    /**
     * 플러그인 어댑터 지원 수
     * 
     * @return int
     */
    public function get_max_plugins() {
        if ( $this->current_edition === 'master' || $this->current_edition === 'partner' ) {
            return 999;
        }
        switch ( $this->current_edition ) {
            case 'free':
                return 1;
            case 'basic':
                return 5;
            case 'premium':
            case 'unlimited':
                return 15;
            default:
                return 0;
        }
    }

    /**
     * 스포이드 기능 사용 가능 여부
     * 
     * @return bool
     */
    public function can_use_eyedropper() {
        return $this->is_at_least( 'basic' );
    }

    /**
     * 팔레트에서 색상 불러오기 사용 가능 여부
     * 
     * @return bool
     */
    public function can_use_palette_load() {
        return $this->is_at_least( 'premium' );
    }

    /**
     * Customizer 색상 자동 불러오기 사용 가능 여부
     * 
     * @return bool
     */
    public function can_use_customizer_sync() {
        return $this->is_at_least( 'premium' );
    }

    /**
     * 섹션별 새로고침 버튼 사용 가능 여부
     * 
     * @return bool
     */
    public function can_use_section_refresh() {
        return $this->is_at_least( 'premium' );
    }

    /**
     * 업그레이드 프롬프트 메시지 생성
     * 
     * @param string $feature
     * @return string
     */
    public function get_upgrade_prompt( $feature ) {
        return $this->license_manager->get_upgrade_prompt( $feature );
    }
}

