<?php
/**
 * [v25.0.0] 공유 라이센스 관리자
 * 
 * 모든 3J Labs 플러그인에서 공유 사용하는 라이센스 관리 시스템
 * 
 * @package 3J_Labs_Shared
 * @version 25.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Manager_Shared {

    private static $instances = array();
    private $plugin_slug = '';
    private $option_prefix = '';

    public static function instance( $plugin_slug ) {
        if ( ! isset( self::$instances[ $plugin_slug ] ) ) {
            self::$instances[ $plugin_slug ] = new self( $plugin_slug );
        }
        return self::$instances[ $plugin_slug ];
    }

    private function __construct( $plugin_slug ) {
        $this->plugin_slug = $plugin_slug;
        $this->option_prefix = 'jj_' . str_replace( '-', '_', $plugin_slug ) . '_';
        
        $this->init_hooks();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 라이센스 설정 페이지 추가
        add_action( 'admin_menu', array( $this, 'add_license_page' ), 20 );
        
        // 라이센스 저장 처리
        add_action( 'admin_post_' . $this->plugin_slug . '_save_license', array( $this, 'save_license' ) );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_' . $this->plugin_slug . '_verify_license', array( $this, 'ajax_verify_license' ) );
    }

    /**
     * 라이센스 페이지 추가
     * [v25.0.1] 메뉴 이름을 '3J 라이센스 관리 🔑'로 변경
     */
    public function add_license_page() {
        add_submenu_page(
            'options-general.php',
            __( '3J 라이센스 관리', '3j-labs-shared' ),
            __( '3J 라이센스 관리', '3j-labs-shared' ) . ' 🔑',
            'manage_options',
            $this->plugin_slug . '-license',
            array( $this, 'render_license_page' )
        );
    }

    /**
     * 라이센스 페이지 렌더링
     */
    public function render_license_page() {
        $license_key = $this->get_license_key();
        $license_status = $this->get_license_status();
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            
            <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
                <?php wp_nonce_field( $this->plugin_slug . '_license_nonce', $this->plugin_slug . '_license_nonce' ); ?>
                <input type="hidden" name="action" value="<?php echo esc_attr( $this->plugin_slug . '_save_license' ); ?>">
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="license_key"><?php _e( '라이센스 키', '3j-labs-shared' ); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="license_key" 
                                   name="license_key" 
                                   value="<?php echo esc_attr( $license_key ); ?>" 
                                   class="regular-text"
                                   placeholder="XXXXX-XXXXX-XXXXX-XXXXX">
                            <p class="description">
                                <?php _e( '라이센스 키를 입력하세요. 형식: XXXXX-XXXXX-XXXXX-XXXXX', '3j-labs-shared' ); ?>
                            </p>
                        </td>
                    </tr>
                    <?php if ( ! empty( $license_key ) ) : ?>
                    <tr>
                        <th scope="row"><?php _e( '라이센스 상태', '3j-labs-shared' ); ?></th>
                        <td>
                            <?php if ( $license_status['valid'] ) : ?>
                                <span style="color: green;">✓ <?php _e( '유효', '3j-labs-shared' ); ?></span>
                                <p class="description">
                                    <?php printf( __( '타입: %s, 만료일: %s', '3j-labs-shared' ), 
                                        esc_html( $license_status['type'] ?? 'N/A' ),
                                        esc_html( $license_status['expires'] ?? 'N/A' )
                                    ); ?>
                                </p>
                            <?php else : ?>
                                <span style="color: red;">✗ <?php _e( '무효', '3j-labs-shared' ); ?></span>
                                <p class="description">
                                    <?php echo esc_html( $license_status['message'] ?? __( '라이센스 키가 유효하지 않습니다.', '3j-labs-shared' ) ); ?>
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
                
                <?php submit_button( __( '라이센스 저장', '3j-labs-shared' ) ); ?>
            </form>
            
            <div style="margin-top: 20px;">
                <button type="button" class="button" id="verify-license-btn">
                    <?php _e( '라이센스 검증', '3j-labs-shared' ); ?>
                </button>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#verify-license-btn').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true).text('<?php _e( '검증 중...', '3j-labs-shared' ); ?>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: '<?php echo esc_js( $this->plugin_slug . '_verify_license' ); ?>',
                        nonce: '<?php echo wp_create_nonce( $this->plugin_slug . '_license_nonce' ); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('<?php _e( '라이센스가 유효합니다.', '3j-labs-shared' ); ?>');
                            location.reload();
                        } else {
                            alert('<?php _e( '라이센스 검증 실패: ', '3j-labs-shared' ); ?>' + (response.data.message || '<?php _e( '알 수 없는 오류', '3j-labs-shared' ); ?>'));
                        }
                    },
                    error: function() {
                        alert('<?php _e( '라이센스 검증 중 오류가 발생했습니다.', '3j-labs-shared' ); ?>');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('<?php _e( '라이센스 검증', '3j-labs-shared' ); ?>');
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * 라이센스 저장
     */
    public function save_license() {
        check_admin_referer( $this->plugin_slug . '_license_nonce', $this->plugin_slug . '_license_nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', '3j-labs-shared' ) );
        }

        $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
        
        // 라이센스 키 저장
        update_option( $this->option_prefix . 'license_key', $license_key );
        
        // 라이센스 검증
        if ( ! empty( $license_key ) ) {
            $this->verify_license( $license_key );
        } else {
            // 라이센스 키가 비어있으면 상태 초기화
            delete_option( $this->option_prefix . 'license_status' );
        }
        
        wp_redirect( add_query_arg( array(
            'page' => $this->plugin_slug . '-license',
            'updated' => '1'
        ), admin_url( 'options-general.php' ) ) );
        exit;
    }

    /**
     * 라이센스 키 가져오기
     */
    public function get_license_key() {
        return get_option( $this->option_prefix . 'license_key', '' );
    }

    /**
     * 라이센스 상태 가져오기
     */
    public function get_license_status() {
        $status = get_option( $this->option_prefix . 'license_status', array(
            'valid' => false,
            'type' => 'FREE',
            'expires' => '',
            'message' => ''
        ) );
        
        return $status;
    }

    /**
     * 라이센스 검증
     */
    public function verify_license( $license_key = null ) {
        if ( $license_key === null ) {
            $license_key = $this->get_license_key();
        }

        if ( empty( $license_key ) ) {
            return array( 'valid' => false, 'message' => __( '라이센스 키가 없습니다.', '3j-labs-shared' ) );
        }

        // 라이센스 보안 모듈이 있으면 사용
        if ( class_exists( 'JJ_License_Security_Shared' ) ) {
            $security = JJ_License_Security_Shared::instance( $this->plugin_slug );
            $is_valid = apply_filters( 'jj_license_is_valid', false, $license_key );
            
            if ( $is_valid ) {
                $server_data = $security->get_license_data_from_server( $license_key );
                $status = array(
                    'valid' => true,
                    'type' => $server_data['type'] ?? 'FREE',
                    'edition' => $server_data['edition'] ?? 'free',
                    'expires' => $server_data['expires_at'] ?? '',
                    'message' => __( '라이센스가 유효합니다.', '3j-labs-shared' )
                );
            } else {
                $status = array(
                    'valid' => false,
                    'type' => 'FREE',
                    'message' => __( '라이센스 키가 유효하지 않습니다.', '3j-labs-shared' )
                );
            }
        } else {
            // 기본 검증 (형식만 확인)
            $pattern = '/^[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}$/';
            if ( preg_match( $pattern, $license_key ) ) {
                $status = array(
                    'valid' => true,
                    'type' => 'BASIC',
                    'message' => __( '라이센스 키 형식이 올바릅니다. (서버 검증 필요)', '3j-labs-shared' )
                );
            } else {
                $status = array(
                    'valid' => false,
                    'type' => 'FREE',
                    'message' => __( '라이센스 키 형식이 올바르지 않습니다.', '3j-labs-shared' )
                );
            }
        }

        // 상태 저장
        update_option( $this->option_prefix . 'license_status', $status );
        
        return $status;
    }

    /**
     * AJAX 라이센스 검증
     */
    public function ajax_verify_license() {
        check_ajax_referer( $this->plugin_slug . '_license_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', '3j-labs-shared' ) ) );
        }

        $status = $this->verify_license();
        
        if ( $status['valid'] ) {
            wp_send_json_success( $status );
        } else {
            wp_send_json_error( $status );
        }
    }

    /**
     * 라이센스가 유효한지 확인
     */
    public function is_valid() {
        $status = $this->get_license_status();
        return ! empty( $status['valid'] );
    }

    /**
     * 라이센스 타입 가져오기
     */
    public function get_license_type() {
        $status = $this->get_license_status();
        return $status['type'] ?? 'FREE';
    }
}
