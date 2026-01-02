<?php
/**
 * 안전 모드 클래스
 * 
 * [v5.3.7] 신규 생성
 * - 플러그인 오류 발생 시 안전 모드로 전환
 * - 하얀 화면 방지
 * - 최소한의 기능만 로드하여 사이트 복구 가능
 * 
 * @package JJ_Style_Guide
 * @since v5.3.7
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // 직접 접근 차단
}

/**
 * 안전 모드 관리 클래스
 * 
 * [v5.3.7] 플러그인 오류 발생 시 안전 모드로 전환하여 사이트 복구 가능
 * 
 * @package JJ_Style_Guide
 * @since v5.3.7
 * 
 * @method static bool is_enabled() 안전 모드 활성화 여부 확인
 * @method bool enable(string $reason = '') 안전 모드 활성화
 * @method bool disable() 안전 모드 비활성화
 * @method array get_info() 안전 모드 정보 가져오기
 * @method bool load_minimal_features() 안전 모드에서 로드할 최소 기능
 * @method void show_safe_mode_notice() 안전 모드 알림 표시
 * @method void auto_enable_on_error(string $error_message, string $error_type = 'fatal') 오류 발생 시 자동 안전 모드 전환
 */
class JJ_Safe_Mode {
    
    /**
     * 싱글톤 인스턴스
     * 
     * @var JJ_Safe_Mode|null
     */
    private static $instance = null;
    
    /**
     * 안전 모드 활성화 여부
     * 
     * @var bool
     */
    private static $enabled = false;
    
    /**
     * 안전 모드 옵션 키
     * 
     * @var string
     */
    private $option_key = 'jj_style_guide_safe_mode';
    
    /**
     * 싱글톤 인스턴스 반환
     * 
     * @return JJ_Safe_Mode
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 생성자
     * 
     * [v5.3.7] 안전 모드 상태 확인 및 초기화
     */
    private function __construct() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'get_option' ) ) {
            return;
        }
        
        // 안전 모드 활성화 여부 확인
        self::$enabled = (bool) get_option( $this->option_key, false );
    }
    
    /**
     * 안전 모드 활성화 여부 확인
     * 
     * @return bool 안전 모드 활성화 여부
     */
    public static function is_enabled() {
        if ( null === self::$instance ) {
            self::instance();
        }
        return self::$enabled;
    }
    
    /**
     * 안전 모드 활성화
     * 
     * [v5.3.7] 플러그인 오류 발생 시 안전 모드로 전환
     * 
     * @param string $reason 안전 모드 활성화 이유
     * @return bool 성공 여부
     */
    public function enable( $reason = '' ) {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'update_option' ) ) {
            return false;
        }
        
        self::$enabled = true;
        $result = update_option( $this->option_key, true );
        
        // 활성화 이유 기록
        if ( ! empty( $reason ) && function_exists( 'update_option' ) ) {
            update_option( 'jj_style_guide_safe_mode_reason', $reason );
            update_option( 'jj_style_guide_safe_mode_timestamp', function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ) );
        }
        
        // 로그 기록
        if ( function_exists( 'error_log' ) ) {
            error_log( 'JJ Simple Style Guide: 안전 모드가 활성화되었습니다. 이유: ' . $reason );
        }
        
        return $result;
    }
    
    /**
     * 안전 모드 비활성화
     * 
     * [v5.3.7] 안전 모드 해제
     * 
     * @return bool 성공 여부
     */
    public function disable() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'update_option' ) || ! function_exists( 'delete_option' ) ) {
            return false;
        }
        
        self::$enabled = false;
        $result = update_option( $this->option_key, false );
        
        // 관련 옵션 삭제
        delete_option( 'jj_style_guide_safe_mode_reason' );
        delete_option( 'jj_style_guide_safe_mode_timestamp' );
        
        // 로그 기록
        if ( function_exists( 'error_log' ) ) {
            error_log( 'JJ Simple Style Guide: 안전 모드가 비활성화되었습니다.' );
        }
        
        return $result;
    }
    
    /**
     * 안전 모드 정보 가져오기
     * 
     * [v5.3.7] 안전 모드 상태 및 정보 반환
     * 
     * @return array 안전 모드 정보
     */
    public function get_info() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'get_option' ) ) {
            return array(
                'enabled' => false,
                'reason' => '',
                'timestamp' => '',
            );
        }
        
        return array(
            'enabled' => self::$enabled,
            'reason' => get_option( 'jj_style_guide_safe_mode_reason', '' ),
            'timestamp' => get_option( 'jj_style_guide_safe_mode_timestamp', '' ),
        );
    }
    
    /**
     * 안전 모드에서 로드할 최소 기능
     * 
     * [v5.3.7] 안전 모드 활성화 시 최소한의 기능만 로드
     * 
     * @return bool 성공 여부
     */
    public function load_minimal_features() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'add_action' ) ) {
            return false;
        }
        
        // 관리자 페이지에 안전 모드 알림 추가
        if ( is_admin() ) {
            add_action( 'admin_notices', array( $this, 'show_safe_mode_notice' ) );
        }
        
        return true;
    }
    
    /**
     * 안전 모드 알림 표시
     * 
     * [v5.3.7] 관리자 페이지에 안전 모드 알림 표시
     */
    public function show_safe_mode_notice() {
        // WordPress 함수 존재 확인
        if ( ! function_exists( 'esc_html' ) || ! function_exists( 'admin_url' ) ) {
            return;
        }
        
        $info = $this->get_info();
        
        if ( ! $info['enabled'] ) {
            return;
        }
        
        ?>
        <div class="notice notice-error">
            <p>
                <strong><?php esc_html_e( 'JJ Simple Style Guide: 안전 모드가 활성화되어 있습니다.', 'jj-simple-style-guide' ); ?></strong>
                <?php if ( ! empty( $info['reason'] ) ) : ?>
                    <br><?php echo esc_html( $info['reason'] ); ?>
                <?php endif; ?>
                <?php if ( ! empty( $info['timestamp'] ) ) : ?>
                    <br><small><?php echo esc_html( sprintf( __( '활성화 시간: %s', 'jj-simple-style-guide' ), $info['timestamp'] ) ); ?></small>
                <?php endif; ?>
                <br>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=jj-style-guide&action=disable_safe_mode' ) ); ?>" class="button">
                    <?php esc_html_e( '안전 모드 비활성화', 'jj-simple-style-guide' ); ?>
                </a>
            </p>
        </div>
        <?php
    }
    
    /**
     * 오류 발생 시 자동 안전 모드 전환
     * 
     * [v5.3.7] 치명적 오류 발생 시 자동으로 안전 모드 전환
     * 
     * @param string $error_message 오류 메시지
     * @param string $error_type 오류 타입 (fatal, warning, notice)
     */
    public function auto_enable_on_error( $error_message, $error_type = 'fatal' ) {
        // 치명적 오류인 경우에만 자동 전환
        if ( 'fatal' !== $error_type ) {
            return;
        }
        
        // 이미 안전 모드가 활성화되어 있으면 중복 전환 방지
        if ( self::$enabled ) {
            return;
        }
        
        // 안전 모드 활성화
        $this->enable( sprintf( '자동 전환: %s', $error_message ) );
        
        // 로그 기록
        if ( function_exists( 'error_log' ) ) {
            error_log( sprintf(
                'JJ Simple Style Guide: 치명적 오류로 인해 안전 모드가 자동으로 활성화되었습니다. 오류: %s',
                $error_message
            ) );
        }
    }
}

