<?php
/**
 * JJ Multisite Controller
 *
 * Phase 5 B: 멀티사이트(네트워크) 제어
 * - 네트워크 기본 스타일을 저장하고, 각 사이트에 공통 적용할 수 있습니다.
 *
 * @since 6.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_Multisite_Controller' ) ) {
    final class JJ_Multisite_Controller {

        private static $instance = null;

        /** @var string */
        private $site_option_key = 'jj_style_guide_network_control';

        public static function instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
            if ( function_exists( 'is_multisite' ) && is_multisite() && function_exists( 'add_action' ) ) {
                add_action( 'network_admin_menu', array( $this, 'register_network_menu' ) );
            }
        }

        private function defaults() {
            return array(
                'enabled'            => false,
                'allow_site_override'=> true,
                'network_options'    => array(),
            );
        }

        public function get_network_config() {
            $defaults = $this->defaults();
            if ( ! function_exists( 'get_site_option' ) ) {
                return $defaults;
            }
            $cfg = get_site_option( $this->site_option_key, array() );
            if ( ! is_array( $cfg ) ) {
                $cfg = array();
            }
            $cfg = array_merge( $defaults, $cfg );
            $cfg['enabled'] = ! empty( $cfg['enabled'] );
            $cfg['allow_site_override'] = ! empty( $cfg['allow_site_override'] );
            if ( ! isset( $cfg['network_options'] ) || ! is_array( $cfg['network_options'] ) ) {
                $cfg['network_options'] = array();
            }
            return $cfg;
        }

        public function is_enabled() {
            $cfg = $this->get_network_config();
            return ! empty( $cfg['enabled'] );
        }

        public function allow_site_override() {
            $cfg = $this->get_network_config();
            return ! empty( $cfg['allow_site_override'] );
        }

        /**
         * 사이트 옵션 필터링: 네트워크 기본값 적용
         *
         * @param array $site_options
         * @return array
         */
        public function filter_site_options( $site_options ) {
            if ( ! function_exists( 'is_multisite' ) || ! is_multisite() ) {
                return $site_options;
            }

            $cfg = $this->get_network_config();
            if ( empty( $cfg['enabled'] ) ) {
                return $site_options;
            }

            $network = isset( $cfg['network_options'] ) && is_array( $cfg['network_options'] ) ? $cfg['network_options'] : array();

            if ( empty( $cfg['allow_site_override'] ) ) {
                return $network;
            }

            // 네트워크 기본값 위에 사이트 옵션을 덮어쓰는 방식(재귀)
            if ( function_exists( 'array_replace_recursive' ) ) {
                return array_replace_recursive( $network, (array) $site_options );
            }

            // PHP 7.4 폴백: 얕은 병합
            return array_merge( $network, (array) $site_options );
        }

        /**
         * 네트워크 관리자 메뉴 등록
         */
        public function register_network_menu() {
            if ( ! function_exists( 'add_submenu_page' ) ) {
                return;
            }
            add_submenu_page(
                'settings.php',
                __( 'ACF CSS Network', 'jj-style-guide' ),
                __( 'ACF CSS Network', 'jj-style-guide' ),
                'manage_network_options',
                'acf-css-network',
                array( $this, 'render_network_page' )
            );
        }

        /**
         * 네트워크 설정 페이지 렌더링
         */
        public function render_network_page() {
            if ( ! current_user_can( 'manage_network_options' ) ) {
                return;
            }

            $cfg = $this->get_network_config();
            $notice = '';
            $notice_type = 'updated';

            // 저장 처리
            if ( isset( $_POST['jj_network_save'] ) && function_exists( 'check_admin_referer' ) ) {
                check_admin_referer( 'jj_network_control_save', 'jj_network_control_nonce' );

                $enabled = isset( $_POST['jj_network_enabled'] ) && '1' === $_POST['jj_network_enabled'];
                $allow_override = isset( $_POST['jj_network_allow_override'] ) && '1' === $_POST['jj_network_allow_override'];

                $json = isset( $_POST['jj_network_options_json'] ) ? wp_unslash( $_POST['jj_network_options_json'] ) : '';
                $decoded = array();
                if ( is_string( $json ) && '' !== trim( $json ) ) {
                    $tmp = json_decode( $json, true );
                    if ( is_array( $tmp ) ) {
                        $decoded = $tmp;
                    } else {
                        $notice_type = 'error';
                        $notice = __( '네트워크 기본값 JSON이 올바르지 않습니다.', 'jj-style-guide' );
                    }
                }

                if ( 'error' !== $notice_type ) {
                    $new_cfg = array(
                        'enabled'             => $enabled,
                        'allow_site_override' => $allow_override,
                        'network_options'     => $decoded,
                    );
                    if ( function_exists( 'update_site_option' ) ) {
                        update_site_option( $this->site_option_key, $new_cfg );
                        $cfg = $this->get_network_config();
                        $notice = __( '네트워크 설정이 저장되었습니다.', 'jj-style-guide' );
                    }
                }
            }

            // 전체 사이트에 적용(오버라이트)
            if ( isset( $_POST['jj_network_apply_all'] ) && function_exists( 'check_admin_referer' ) ) {
                check_admin_referer( 'jj_network_control_save', 'jj_network_control_nonce' );

                if ( function_exists( 'get_sites' ) && function_exists( 'switch_to_blog' ) && function_exists( 'restore_current_blog' ) && function_exists( 'update_option' ) ) {
                    $ids = get_sites( array( 'fields' => 'ids', 'number' => 200 ) ); // 안전 상한
                    $applied = 0;
                    foreach ( (array) $ids as $blog_id ) {
                        switch_to_blog( (int) $blog_id );
                        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
                        update_option( $key, $cfg['network_options'] );
                        restore_current_blog();
                        $applied++;
                    }
                    $notice = sprintf( __( '네트워크 기본값을 %d개 사이트에 적용했습니다. (상한: 200)', 'jj-style-guide' ), $applied );
                } else {
                    $notice_type = 'error';
                    $notice = __( '멀티사이트 적용에 필요한 WordPress 함수가 로드되지 않았습니다.', 'jj-style-guide' );
                }
            }

            $json_value = '';
            if ( function_exists( 'wp_json_encode' ) ) {
                $json_value = wp_json_encode( $cfg['network_options'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
            } else {
                $json_value = json_encode( $cfg['network_options'] );
            }
            ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'ACF CSS Network Control', 'jj-style-guide' ); ?></h1>
                <p class="description"><?php esc_html_e( '멀티사이트 환경에서 네트워크 기본 스타일을 설정하고, 전체 사이트에 일괄 적용할 수 있습니다.', 'jj-style-guide' ); ?></p>

                <?php if ( $notice ) : ?>
                    <div class="<?php echo ( 'error' === $notice_type ) ? 'notice notice-error' : 'notice notice-success'; ?>"><p><?php echo esc_html( $notice ); ?></p></div>
                <?php endif; ?>

                <form method="post">
                    <?php wp_nonce_field( 'jj_network_control_save', 'jj_network_control_nonce' ); ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e( '네트워크 제어 활성화', 'jj-style-guide' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="jj_network_enabled" value="1" <?php checked( ! empty( $cfg['enabled'] ), true ); ?> />
                                    <?php esc_html_e( '네트워크 기본값을 사이트에 적용', 'jj-style-guide' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( '사이트별 오버라이드', 'jj-style-guide' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="jj_network_allow_override" value="1" <?php checked( ! empty( $cfg['allow_site_override'] ), true ); ?> />
                                    <?php esc_html_e( '각 사이트 설정이 네트워크 기본값을 덮어쓸 수 있음', 'jj-style-guide' ); ?>
                                </label>
                                <p class="description"><?php esc_html_e( '끄면 각 사이트의 저장은 제한되며(권장: 네트워크 전용 운영 시), 네트워크 기본값만 적용됩니다.', 'jj-style-guide' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( '네트워크 기본값(JSON)', 'jj-style-guide' ); ?></th>
                            <td>
                                <textarea name="jj_network_options_json" rows="14" class="large-text code"><?php echo esc_textarea( $json_value ); ?></textarea>
                                <p class="description"><?php esc_html_e( '스타일 옵션을 JSON 형태로 저장합니다. (고급 사용자용)', 'jj-style-guide' ); ?></p>
                            </td>
                        </tr>
                    </table>

                    <p class="submit">
                        <button type="submit" name="jj_network_save" class="button button-primary">
                            <?php esc_html_e( '저장', 'jj-style-guide' ); ?>
                        </button>
                        <button type="submit" name="jj_network_apply_all" class="button button-secondary" onclick="return confirm('네트워크 기본값을 최대 200개 사이트에 덮어씁니다. 진행하시겠습니까?');">
                            <?php esc_html_e( '전체 사이트에 적용(덮어쓰기)', 'jj-style-guide' ); ?>
                        </button>
                    </p>
                </form>
            </div>
            <?php
        }
    }
}


