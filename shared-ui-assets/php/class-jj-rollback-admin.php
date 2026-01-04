<?php
/**
 * JJ Rollback Admin - 롤백 관리자 페이지
 *
 * 롤백 히스토리 조회 및 관리 기능을 제공합니다.
 *
 * @package    3J_Labs_Shared
 * @subpackage Rollback
 * @since      1.0.0
 * @author     3J Labs (제이x제니x제이슨 연구소)
 * @link       https://3j-labs.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_Rollback_Admin' ) ) {

    /**
     * 롤백 관리자 페이지 클래스
     *
     * @since 1.0.0
     */
    class JJ_Rollback_Admin {

        use JJ_Singleton_Trait;

        /**
         * 생성자
         *
         * @since 1.0.0
         */
        protected function __construct() {
            // 관리자 메뉴 추가
            add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
            
            // AJAX 핸들러
            add_action( 'wp_ajax_jj_get_rollback_history', array( $this, 'ajax_get_history' ) );
            add_action( 'wp_ajax_jj_clear_rollback_history', array( $this, 'ajax_clear_history' ) );
        }

        /**
         * 관리자 메뉴 추가
         *
         * @since 1.0.0
         */
        public function add_admin_menu() {
            add_management_page(
                __( '롤백 히스토리', 'jj-shared' ),
                __( '롤백 히스토리', 'jj-shared' ),
                'manage_options',
                'jj-rollback-history',
                array( $this, 'render_history_page' )
            );
        }

        /**
         * 히스토리 페이지 렌더링
         *
         * @since 1.0.0
         */
        public function render_history_page() {
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( __( '권한이 없습니다.', 'jj-shared' ) );
            }

            $rollback = JJ_Rollback_Shared::instance();
            $all_history = $rollback->get_rollback_history();
            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                
                <div class="jj-rollback-history-container">
                    <?php if ( empty( $all_history ) ) : ?>
                        <div class="notice notice-info">
                            <p><?php _e( '롤백 히스토리가 없습니다.', 'jj-shared' ); ?></p>
                        </div>
                    <?php else : ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th><?php _e( '플러그인', 'jj-shared' ); ?></th>
                                    <th><?php _e( '이전 버전', 'jj-shared' ); ?></th>
                                    <th><?php _e( '롤백 버전', 'jj-shared' ); ?></th>
                                    <th><?php _e( '사용자', 'jj-shared' ); ?></th>
                                    <th><?php _e( '시간', 'jj-shared' ); ?></th>
                                    <th><?php _e( '상태', 'jj-shared' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $all_history as $plugin => $history ) : ?>
                                    <?php foreach ( $history as $entry ) : ?>
                                        <tr>
                                            <td><strong><?php echo esc_html( $plugin ); ?></strong></td>
                                            <td><?php echo esc_html( $entry['from_version'] ); ?></td>
                                            <td><?php echo esc_html( $entry['to_version'] ); ?></td>
                                            <td><?php echo esc_html( isset( $entry['user_name'] ) ? $entry['user_name'] : 'N/A' ); ?></td>
                                            <td><?php echo esc_html( $entry['timestamp'] ); ?></td>
                                            <td>
                                                <?php if ( isset( $entry['success'] ) && $entry['success'] ) : ?>
                                                    <span class="dashicons dashicons-yes-alt" style="color: #00a32a;"></span>
                                                    <?php _e( '성공', 'jj-shared' ); ?>
                                                <?php else : ?>
                                                    <span class="dashicons dashicons-dismiss" style="color: #d63638;"></span>
                                                    <?php _e( '실패', 'jj-shared' ); ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <p>
                            <button type="button" class="button" id="jj-clear-all-history">
                                <?php _e( '전체 히스토리 삭제', 'jj-shared' ); ?>
                            </button>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <script>
            jQuery(document).ready(function($) {
                $('#jj-clear-all-history').on('click', function() {
                    if (!confirm('<?php echo esc_js( __( '정말로 전체 롤백 히스토리를 삭제하시겠습니까?', 'jj-shared' ) ); ?>')) {
                        return;
                    }
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'jj_clear_rollback_history',
                            nonce: '<?php echo wp_create_nonce( 'jj_clear_rollback_history' ); ?>',
                            plugin: ''
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert(response.data.message || '<?php echo esc_js( __( '삭제에 실패했습니다.', 'jj-shared' ) ); ?>');
                            }
                        }
                    });
                });
            });
            </script>
            <?php
        }

        /**
         * AJAX: 롤백 히스토리 가져오기
         *
         * @since 1.0.0
         */
        public function ajax_get_history() {
            check_ajax_referer( 'jj_get_rollback_history', 'nonce' );

            if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-shared' ) ) );
            }

            $plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( $_POST['plugin'] ) : '';
            $limit = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 0;

            $rollback = JJ_Rollback_Shared::instance();
            $history = $rollback->get_rollback_history( $plugin, $limit );

            wp_send_json_success( array( 'history' => $history ) );
        }

        /**
         * AJAX: 롤백 히스토리 삭제
         *
         * @since 1.0.0
         */
        public function ajax_clear_history() {
            check_ajax_referer( 'jj_clear_rollback_history', 'nonce' );

            if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-shared' ) ) );
            }

            $plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( $_POST['plugin'] ) : '';

            $rollback = JJ_Rollback_Shared::instance();
            $result = $rollback->clear_rollback_history( $plugin );

            if ( $result ) {
                wp_send_json_success( array( 
                    'message' => empty( $plugin ) 
                        ? __( '전체 롤백 히스토리가 삭제되었습니다.', 'jj-shared' )
                        : sprintf( __( '%s의 롤백 히스토리가 삭제되었습니다.', 'jj-shared' ), $plugin )
                ) );
            } else {
                wp_send_json_error( array( 'message' => __( '히스토리 삭제에 실패했습니다.', 'jj-shared' ) ) );
            }
        }
    }
}
