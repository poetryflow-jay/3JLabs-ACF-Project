<?php
/**
 * J&J 라이센스 발행 관리자 (마스터 플러그인 전용)
 * 
 * 라이센스 키 생성, 발행, 활성화 상태 확인, 비활성화 등을 관리합니다.
 * 마스터 버전에서만 사용 가능한 기능입니다.
 * 
 * @version 3.8.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

final class JJ_License_Issuer {

    private static $instance = null;
    private $licenses_option_key = 'jj_style_guide_issued_licenses';
    private $license_activations_key = 'jj_style_guide_license_activations';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 마스터 버전 체크
        if ( ! $this->is_master_version() ) {
            return;
        }

        add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_generate_license', array( $this, 'ajax_generate_license' ) );
        add_action( 'wp_ajax_jj_deactivate_license', array( $this, 'ajax_deactivate_license' ) );
        add_action( 'wp_ajax_jj_check_license_status', array( $this, 'ajax_check_license_status' ) );
        add_action( 'wp_ajax_jj_delete_license', array( $this, 'ajax_delete_license' ) );
    }

    /**
     * 마스터 버전 여부 확인
     * 
     * @return bool
     */
    private function is_master_version() {
        // Master 에디션에서만 활성화 (Partner는 제외)
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                return JJ_Edition_Controller::instance()->is_at_least( 'master' );
            } catch ( Exception $e ) {
                return false;
            } catch ( Error $e ) {
                return false;
            }
        }
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            return ( 'MASTER' === JJ_STYLE_GUIDE_LICENSE_TYPE );
        }
        return false;
    }

    /**
     * 관리자 메뉴 페이지 추가
     */
    public function add_admin_menu_page() {
        if ( ! $this->is_master_version() ) {
            return;
        }

        add_submenu_page(
            'options-general.php',
            __( '라이센스 발행 관리', 'jj-style-guide' ),
            __( '라이센스 발행 관리', 'jj-style-guide' ),
            'manage_options',
            'jj-license-issuer',
            array( $this, 'render_license_issuer_page' )
        );
    }

    /**
     * 에셋 enqueue
     */
    public function enqueue_assets( $hook ) {
        if ( 'settings_page_jj-license-issuer' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'jj-license-issuer',
            JJ_STYLE_GUIDE_URL . 'assets/css/jj-license-issuer.css',
            array(),
            JJ_STYLE_GUIDE_VERSION
        );

        wp_enqueue_script(
            'jj-license-issuer',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-license-issuer.js',
            array( 'jquery' ),
            JJ_STYLE_GUIDE_VERSION,
            true
        );

        wp_localize_script(
            'jj-license-issuer',
            'jjLicenseIssuer',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_license_issuer_action' ),
                'i18n'     => array(
                    'confirm_delete' => __( '정말로 이 라이센스를 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.', 'jj-style-guide' ),
                    'confirm_deactivate' => __( '정말로 이 라이센스를 비활성화하시겠습니까?', 'jj-style-guide' ),
                    'generating' => __( '라이센스 생성 중...', 'jj-style-guide' ),
                    'success' => __( '성공', 'jj-style-guide' ),
                    'error' => __( '오류', 'jj-style-guide' ),
                ),
            )
        );
    }

    /**
     * 라이센스 발행 관리 페이지 렌더링
     */
    public function render_license_issuer_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! $this->is_master_version() ) {
            ?>
            <div class="wrap">
                <h1><?php _e( '라이센스 발행 관리', 'jj-style-guide' ); ?></h1>
                <div class="notice notice-error">
                    <p><?php _e( '이 기능은 마스터 버전에서만 사용할 수 있습니다.', 'jj-style-guide' ); ?></p>
                </div>
            </div>
            <?php
            return;
        }

        $licenses = $this->get_all_licenses();
        ?>
        <div class="wrap jj-license-issuer-wrap">
            <h1><?php _e( 'J&J 라이센스 발행 관리', 'jj-style-guide' ); ?></h1>
            
            <div class="jj-license-issuer-header" style="margin: 20px 0; padding: 20px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px;">
                <h2 style="margin-top: 0;"><?php _e( '새 라이센스 발행', 'jj-style-guide' ); ?></h2>
                <form id="jj-generate-license-form" method="post">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="jj-license-type"><?php _e( '라이센스 타입', 'jj-style-guide' ); ?></label>
                                </th>
                                <td>
                                    <select id="jj-license-type" name="license_type" required style="width: 300px; padding: 8px;">
                                        <option value=""><?php _e( '선택하세요', 'jj-style-guide' ); ?></option>
                                        <option value="FREE"><?php _e( 'Free', 'jj-style-guide' ); ?></option>
                                        <option value="BASIC"><?php _e( 'Basic', 'jj-style-guide' ); ?></option>
                                        <option value="PREM"><?php _e( 'Premium', 'jj-style-guide' ); ?></option>
                                        <option value="UNLIM"><?php _e( 'Unlimited', 'jj-style-guide' ); ?></option>
                                    </select>
                                    <p class="description">
                                        <?php _e( '발행할 라이센스의 타입을 선택하세요.', 'jj-style-guide' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="jj-license-customer"><?php _e( '고객명/메모', 'jj-style-guide' ); ?></label>
                                </th>
                                <td>
                                    <input type="text" 
                                           id="jj-license-customer" 
                                           name="customer" 
                                           class="regular-text" 
                                           placeholder="<?php esc_attr_e( '예: 홍길동, example@email.com', 'jj-style-guide' ); ?>">
                                    <p class="description">
                                        <?php _e( '라이센스 구매자 정보나 메모를 입력하세요 (선택사항).', 'jj-style-guide' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="jj-license-expires"><?php _e( '만료일', 'jj-style-guide' ); ?></label>
                                </th>
                                <td>
                                    <input type="date" 
                                           id="jj-license-expires" 
                                           name="expires" 
                                           class="regular-text"
                                           min="<?php echo date( 'Y-m-d' ); ?>">
                                    <p class="description">
                                        <?php _e( '라이센스 만료일을 설정하세요. 비워두면 1년 후로 설정됩니다. (선택사항)', 'jj-style-guide' ); ?>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="submit">
                        <?php wp_nonce_field( 'jj_license_issuer_action', 'jj_license_issuer_nonce' ); ?>
                        <button type="submit" class="button button-primary button-large" id="jj-generate-license-btn">
                            <span class="dashicons dashicons-admin-network" style="margin-top: 4px;"></span>
                            <?php _e( '라이센스 생성', 'jj-style-guide' ); ?>
                        </button>
                        <span class="spinner" style="float: none; margin-left: 10px;"></span>
                    </p>
                </form>
            </div>

            <div class="jj-license-issuer-list" style="margin: 20px 0;">
                <h2><?php _e( '발행된 라이센스 목록', 'jj-style-guide' ); ?></h2>
                <div class="jj-license-stats" style="margin-bottom: 20px; padding: 15px; background: #f0f6fc; border: 1px solid #c3c4c7; border-radius: 4px;">
                    <strong><?php _e( '통계:', 'jj-style-guide' ); ?></strong>
                    <span style="margin-left: 15px;">
                        <?php
                        $stats = $this->get_license_stats();
                        printf(
                            __( '전체: %d개 | 활성화됨: %d개 | 비활성화됨: %d개 | 사용 중: %d개', 'jj-style-guide' ),
                            $stats['total'],
                            $stats['active'],
                            $stats['inactive'],
                            $stats['in_use']
                        );
                        ?>
                    </span>
                </div>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 200px;"><?php _e( '라이센스 키', 'jj-style-guide' ); ?></th>
                            <th style="width: 100px;"><?php _e( '타입', 'jj-style-guide' ); ?></th>
                            <th style="width: 150px;"><?php _e( '고객명/메모', 'jj-style-guide' ); ?></th>
                            <th style="width: 120px;"><?php _e( '상태', 'jj-style-guide' ); ?></th>
                            <th style="width: 200px;"><?php _e( '활성화된 사이트', 'jj-style-guide' ); ?></th>
                            <th style="width: 120px;"><?php _e( '발행일', 'jj-style-guide' ); ?></th>
                            <th style="width: 120px;"><?php _e( '만료일', 'jj-style-guide' ); ?></th>
                            <th style="width: 150px;"><?php _e( '작업', 'jj-style-guide' ); ?></th>
                        </tr>
                    </thead>
                    <tbody id="jj-license-list-body">
                        <?php if ( empty( $licenses ) ) : ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 30px;">
                                <?php _e( '발행된 라이센스가 없습니다.', 'jj-style-guide' ); ?>
                            </td>
                        </tr>
                        <?php else : ?>
                        <?php foreach ( $licenses as $license_key => $license_data ) : 
                            $activations = $this->get_license_activations( $license_key );
                            $is_active = isset( $license_data['status'] ) && $license_data['status'] === 'active';
                            $is_expired = isset( $license_data['expires'] ) && strtotime( $license_data['expires'] ) < time();
                        ?>
                        <tr data-license-key="<?php echo esc_attr( $license_key ); ?>" class="<?php echo $is_expired ? 'expired' : ''; ?>">
                            <td>
                                <code style="font-size: 11px; background: #f0f0f1; padding: 4px 8px; border-radius: 3px; word-break: break-all;">
                                    <?php echo esc_html( $license_key ); ?>
                                </code>
                                <button type="button" class="button button-small jj-copy-license-key" 
                                        data-license-key="<?php echo esc_attr( $license_key ); ?>"
                                        style="margin-top: 5px; font-size: 11px; padding: 2px 8px; height: 24px;">
                                    <span class="dashicons dashicons-admin-page" style="font-size: 12px; margin-top: 2px;"></span>
                                    <?php _e( '복사', 'jj-style-guide' ); ?>
                                </button>
                            </td>
                            <td>
                                <span class="jj-license-type-badge jj-license-type-<?php echo strtolower( esc_attr( $license_data['type'] ?? 'FREE' ) ); ?>">
                                    <?php echo esc_html( $license_data['type'] ?? 'FREE' ); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo esc_html( $license_data['customer'] ?? '-' ); ?>
                            </td>
                            <td>
                                <?php if ( $is_expired ) : ?>
                                    <span class="jj-status-badge expired"><?php _e( '만료됨', 'jj-style-guide' ); ?></span>
                                <?php elseif ( $is_active ) : ?>
                                    <span class="jj-status-badge active"><?php _e( '활성화됨', 'jj-style-guide' ); ?></span>
                                <?php else : ?>
                                    <span class="jj-status-badge inactive"><?php _e( '비활성화됨', 'jj-style-guide' ); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ( ! empty( $activations ) ) : ?>
                                    <div class="jj-activations-list">
                                        <?php foreach ( $activations as $activation ) : ?>
                                        <div class="jj-activation-item" style="margin-bottom: 5px; padding: 5px; background: #f9f9f9; border-radius: 3px; font-size: 12px;">
                                            <strong><?php echo esc_html( $activation['site_url'] ?? 'Unknown' ); ?></strong>
                                            <br>
                                            <small style="color: #666;">
                                                <?php printf( __( '활성화: %s', 'jj-style-guide' ), date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $activation['activated_at'] ?? time() ) ); ?>
                                            </small>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <span style="color: #999;"><?php _e( '없음', 'jj-style-guide' ); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo isset( $license_data['issued_at'] ) ? date_i18n( get_option( 'date_format' ), $license_data['issued_at'] ) : '-'; ?>
                            </td>
                            <td>
                                <?php 
                                if ( isset( $license_data['expires'] ) ) {
                                    $expires_timestamp = strtotime( $license_data['expires'] );
                                    echo date_i18n( get_option( 'date_format' ), $expires_timestamp );
                                    if ( $is_expired ) {
                                        echo ' <span style="color: #d63638;">(' . __( '만료', 'jj-style-guide' ) . ')</span>';
                                    }
                                } else {
                                    echo __( '무제한', 'jj-style-guide' );
                                }
                                ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                    <?php if ( $is_active ) : ?>
                                    <button type="button" 
                                            class="button button-small jj-deactivate-license" 
                                            data-license-key="<?php echo esc_attr( $license_key ); ?>"
                                            style="font-size: 11px; padding: 2px 8px; height: 26px;">
                                        <?php _e( '비활성화', 'jj-style-guide' ); ?>
                                    </button>
                                    <?php else : ?>
                                    <button type="button" 
                                            class="button button-small jj-activate-license" 
                                            data-license-key="<?php echo esc_attr( $license_key ); ?>"
                                            style="font-size: 11px; padding: 2px 8px; height: 26px;">
                                        <?php _e( '활성화', 'jj-style-guide' ); ?>
                                    </button>
                                    <?php endif; ?>
                                    <button type="button" 
                                            class="button button-small jj-check-license-status" 
                                            data-license-key="<?php echo esc_attr( $license_key ); ?>"
                                            style="font-size: 11px; padding: 2px 8px; height: 26px;">
                                        <?php _e( '상태 확인', 'jj-style-guide' ); ?>
                                    </button>
                                    <button type="button" 
                                            class="button button-small button-link-delete jj-delete-license" 
                                            data-license-key="<?php echo esc_attr( $license_key ); ?>"
                                            style="font-size: 11px; padding: 2px 8px; height: 26px; color: #b32d2e;">
                                        <?php _e( '삭제', 'jj-style-guide' ); ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * 라이센스 키 생성
     * 
     * @param string $type
     * @param string $customer
     * @param string $expires
     * @return array
     */
    public function generate_license_key( $type = 'FREE', $customer = '', $expires = '' ) {
        // 라이센스 타입 검증
        $allowed_types = array( 'FREE', 'BASIC', 'PREM', 'UNLIM' );
        if ( ! in_array( $type, $allowed_types ) ) {
            return array(
                'success' => false,
                'message' => __( '유효하지 않은 라이센스 타입입니다.', 'jj-style-guide' ),
            );
        }

        // 버전 정보
        $version = JJ_STYLE_GUIDE_VERSION;
        
        // 랜덤 문자열 생성 (8자리)
        $random = strtoupper( wp_generate_password( 8, false, false ) );
        
        // 체크섬 생성
        $checksum_source = $version . $type . $random . 'jj-secret-key';
        $checksum = strtoupper( substr( md5( $checksum_source ), 0, 8 ) );
        
        // 라이센스 키 생성: JJ-[VERSION]-[TYPE]-[RANDOM]-[CHECKSUM]
        $license_key = sprintf( 'JJ-%s-%s-%s-%s', $version, $type, $random, $checksum );
        
        // 만료일 설정 (없으면 1년 후)
        if ( empty( $expires ) ) {
            $expires = date( 'Y-m-d', strtotime( '+1 year' ) );
        }
        $expires_timestamp = strtotime( $expires );
        
        // 라이센스 데이터 저장
        $licenses = get_option( $this->licenses_option_key, array() );
        $licenses[ $license_key ] = array(
            'type' => $type,
            'customer' => sanitize_text_field( $customer ),
            'issued_at' => time(),
            'expires' => $expires,
            'expires_timestamp' => $expires_timestamp,
            'status' => 'active',
        );
        update_option( $this->licenses_option_key, $licenses );
        
        return array(
            'success' => true,
            'license_key' => $license_key,
            'message' => __( '라이센스 키가 생성되었습니다.', 'jj-style-guide' ),
            'data' => $licenses[ $license_key ],
        );
    }

    /**
     * 모든 라이센스 가져오기
     * 
     * @return array
     */
    public function get_all_licenses() {
        return get_option( $this->licenses_option_key, array() );
    }

    /**
     * 특정 라이센스 가져오기
     * 
     * @param string $license_key
     * @return array|null
     */
    public function get_license( $license_key ) {
        $licenses = $this->get_all_licenses();
        return isset( $licenses[ $license_key ] ) ? $licenses[ $license_key ] : null;
    }

    /**
     * 라이센스 활성화 상태 변경
     * 
     * @param string $license_key
     * @param string $status (active|inactive)
     * @return array
     */
    public function set_license_status( $license_key, $status = 'active' ) {
        $licenses = $this->get_all_licenses();
        
        if ( ! isset( $licenses[ $license_key ] ) ) {
            return array(
                'success' => false,
                'message' => __( '라이센스를 찾을 수 없습니다.', 'jj-style-guide' ),
            );
        }
        
        $licenses[ $license_key ]['status'] = $status;
        $licenses[ $license_key ]['status_changed_at'] = time();
        update_option( $this->licenses_option_key, $licenses );
        
        return array(
            'success' => true,
            'message' => $status === 'active' 
                ? __( '라이센스가 활성화되었습니다.', 'jj-style-guide' )
                : __( '라이센스가 비활성화되었습니다.', 'jj-style-guide' ),
        );
    }

    /**
     * 라이센스 삭제
     * 
     * @param string $license_key
     * @return array
     */
    public function delete_license( $license_key ) {
        $licenses = $this->get_all_licenses();
        
        if ( ! isset( $licenses[ $license_key ] ) ) {
            return array(
                'success' => false,
                'message' => __( '라이센스를 찾을 수 없습니다.', 'jj-style-guide' ),
            );
        }
        
        unset( $licenses[ $license_key ] );
        update_option( $this->licenses_option_key, $licenses );
        
        // 활성화 정보도 삭제
        $activations = get_option( $this->license_activations_key, array() );
        if ( isset( $activations[ $license_key ] ) ) {
            unset( $activations[ $license_key ] );
            update_option( $this->license_activations_key, $activations );
        }
        
        return array(
            'success' => true,
            'message' => __( '라이센스가 삭제되었습니다.', 'jj-style-guide' ),
        );
    }

    /**
     * 라이센스 활성화 정보 가져오기
     * 
     * @param string $license_key
     * @return array
     */
    public function get_license_activations( $license_key ) {
        $activations = get_option( $this->license_activations_key, array() );
        return isset( $activations[ $license_key ] ) ? $activations[ $license_key ] : array();
    }

    /**
     * 라이센스 활성화 기록
     * 
     * @param string $license_key
     * @param string $site_id
     * @param string $site_url
     * @return void
     */
    public function record_activation( $license_key, $site_id, $site_url ) {
        $activations = get_option( $this->license_activations_key, array() );
        
        if ( ! isset( $activations[ $license_key ] ) ) {
            $activations[ $license_key ] = array();
        }
        
        // 동일한 사이트 ID가 있으면 업데이트, 없으면 추가
        $found = false;
        foreach ( $activations[ $license_key ] as &$activation ) {
            if ( $activation['site_id'] === $site_id ) {
                $activation['site_url'] = $site_url;
                $activation['last_verified'] = time();
                $found = true;
                break;
            }
        }
        
        if ( ! $found ) {
            $activations[ $license_key ][] = array(
                'site_id' => $site_id,
                'site_url' => $site_url,
                'activated_at' => time(),
                'last_verified' => time(),
            );
        }
        
        update_option( $this->license_activations_key, $activations );
    }

    /**
     * 라이센스 통계 가져오기
     * 
     * @return array
     */
    public function get_license_stats() {
        $licenses = $this->get_all_licenses();
        $activations = get_option( $this->license_activations_key, array() );
        
        $stats = array(
            'total' => count( $licenses ),
            'active' => 0,
            'inactive' => 0,
            'in_use' => 0,
        );
        
        foreach ( $licenses as $license_key => $license_data ) {
            if ( isset( $license_data['status'] ) && $license_data['status'] === 'active' ) {
                $stats['active']++;
            } else {
                $stats['inactive']++;
            }
            
            if ( isset( $activations[ $license_key ] ) && ! empty( $activations[ $license_key ] ) ) {
                $stats['in_use']++;
            }
        }
        
        return $stats;
    }

    /**
     * AJAX: 라이센스 생성
     */
    public function ajax_generate_license() {
        check_ajax_referer( 'jj_license_issuer_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        if ( ! $this->is_master_version() ) {
            wp_send_json_error( array( 'message' => __( '마스터 버전에서만 사용할 수 있습니다.', 'jj-style-guide' ) ) );
        }
        
        $type = isset( $_POST['license_type'] ) ? sanitize_text_field( $_POST['license_type'] ) : '';
        $customer = isset( $_POST['customer'] ) ? sanitize_text_field( $_POST['customer'] ) : '';
        $expires = isset( $_POST['expires'] ) ? sanitize_text_field( $_POST['expires'] ) : '';
        
        if ( empty( $type ) ) {
            wp_send_json_error( array( 'message' => __( '라이센스 타입을 선택하세요.', 'jj-style-guide' ) ) );
        }
        
        $result = $this->generate_license_key( $type, $customer, $expires );
        
        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }

    /**
     * AJAX: 라이센스 비활성화/활성화
     */
    public function ajax_deactivate_license() {
        check_ajax_referer( 'jj_license_issuer_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
        $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'inactive';
        
        if ( empty( $license_key ) ) {
            wp_send_json_error( array( 'message' => __( '라이센스 키가 필요합니다.', 'jj-style-guide' ) ) );
        }
        
        $result = $this->set_license_status( $license_key, $status );
        
        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }

    /**
     * AJAX: 라이센스 상태 확인
     */
    public function ajax_check_license_status() {
        check_ajax_referer( 'jj_license_issuer_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
        
        if ( empty( $license_key ) ) {
            wp_send_json_error( array( 'message' => __( '라이센스 키가 필요합니다.', 'jj-style-guide' ) ) );
        }
        
        $license = $this->get_license( $license_key );
        $activations = $this->get_license_activations( $license_key );
        
        if ( ! $license ) {
            wp_send_json_error( array( 'message' => __( '라이센스를 찾을 수 없습니다.', 'jj-style-guide' ) ) );
        }
        
        wp_send_json_success( array(
            'license' => $license,
            'activations' => $activations,
            'message' => __( '라이센스 상태를 확인했습니다.', 'jj-style-guide' ),
        ) );
    }

    /**
     * AJAX: 라이센스 삭제
     */
    public function ajax_delete_license() {
        check_ajax_referer( 'jj_license_issuer_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
        
        if ( empty( $license_key ) ) {
            wp_send_json_error( array( 'message' => __( '라이센스 키가 필요합니다.', 'jj-style-guide' ) ) );
        }
        
        $result = $this->delete_license( $license_key );
        
        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }
}

