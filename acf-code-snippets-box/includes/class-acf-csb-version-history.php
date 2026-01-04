<?php
/**
 * ACF Code Snippets Box - Version History
 *
 * 코드 스니펫 버전 관리 및 롤백 기능
 *
 * @package ACF_Code_Snippets_Box
 * @since 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Version History 클래스
 */
class ACF_CSB_Version_History {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 최대 버전 저장 개수
     */
    const MAX_VERSIONS = 20;

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
        add_action( 'save_post_acf_code_snippet', array( $this, 'save_version' ), 20, 2 );
        add_action( 'wp_ajax_acf_csb_get_versions', array( $this, 'ajax_get_versions' ) );
        add_action( 'wp_ajax_acf_csb_restore_version', array( $this, 'ajax_restore_version' ) );
        add_action( 'wp_ajax_acf_csb_compare_versions', array( $this, 'ajax_compare_versions' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_version_meta_box' ) );
    }

    /**
     * 버전 저장
     */
    public function save_version( $post_id, $post ) {
        // 자동 저장 방지
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // 권한 확인
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $code = get_post_meta( $post_id, '_acf_csb_code', true );
        if ( empty( $code ) ) {
            return;
        }

        // 기존 버전 가져오기
        $versions = get_post_meta( $post_id, '_acf_csb_versions', true );
        if ( ! is_array( $versions ) ) {
            $versions = array();
        }

        // 마지막 버전과 비교 (변경이 없으면 저장 안함)
        if ( ! empty( $versions ) ) {
            $last_version = end( $versions );
            if ( isset( $last_version['code'] ) && $last_version['code'] === $code ) {
                return;
            }
        }

        // 새 버전 추가
        $current_user = wp_get_current_user();
        $versions[] = array(
            'version'    => count( $versions ) + 1,
            'code'       => $code,
            'code_type'  => get_post_meta( $post_id, '_acf_csb_code_type', true ),
            'user_id'    => $current_user->ID,
            'user_name'  => $current_user->display_name,
            'timestamp'  => current_time( 'mysql' ),
            'code_hash'  => md5( $code ),
            'code_lines' => substr_count( $code, "\n" ) + 1,
            'code_size'  => strlen( $code ),
        );

        // 최대 버전 개수 제한
        if ( count( $versions ) > self::MAX_VERSIONS ) {
            $versions = array_slice( $versions, -self::MAX_VERSIONS );
        }

        update_post_meta( $post_id, '_acf_csb_versions', $versions );
    }

    /**
     * 버전 히스토리 메타 박스 추가
     */
    public function add_version_meta_box() {
        add_meta_box(
            'acf_csb_version_history',
            __( '버전 히스토리', 'acf-code-snippets-box' ) . ' <span class="acf-csb-pro-badge">v4.1</span>',
            array( $this, 'render_version_meta_box' ),
            'acf_code_snippet',
            'side',
            'low'
        );
    }

    /**
     * 버전 히스토리 메타 박스 렌더링
     */
    public function render_version_meta_box( $post ) {
        $versions = get_post_meta( $post->ID, '_acf_csb_versions', true );
        if ( ! is_array( $versions ) ) {
            $versions = array();
        }

        $version_count = count( $versions );
        ?>
        <div class="acf-csb-version-history">
            <div class="version-summary" style="margin-bottom: 12px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                <strong style="font-size: 24px; color: #1d2327;"><?php echo esc_html( $version_count ); ?></strong>
                <span style="color: #646970; margin-left: 5px;"><?php esc_html_e( '저장된 버전', 'acf-code-snippets-box' ); ?></span>
            </div>

            <?php if ( $version_count > 0 ) : ?>
                <div class="version-list" style="max-height: 200px; overflow-y: auto;">
                    <?php
                    $reversed = array_reverse( $versions );
                    $show_count = min( 5, count( $reversed ) );
                    for ( $i = 0; $i < $show_count; $i++ ) :
                        $v = $reversed[ $i ];
                        $is_current = ( $i === 0 );
                    ?>
                        <div class="version-item" style="padding: 8px 10px; border-bottom: 1px solid #eee; <?php echo $is_current ? 'background: #e7f5ff;' : ''; ?>">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 600;">
                                    v<?php echo esc_html( $v['version'] ); ?>
                                    <?php if ( $is_current ) : ?>
                                        <span style="background: #28a745; color: #fff; padding: 1px 6px; border-radius: 3px; font-size: 10px; margin-left: 5px;">현재</span>
                                    <?php endif; ?>
                                </span>
                                <?php if ( ! $is_current ) : ?>
                                    <button type="button" class="button button-small acf-csb-restore-version"
                                            data-version="<?php echo esc_attr( $v['version'] ); ?>"
                                            style="font-size: 11px; padding: 2px 8px;">
                                        복원
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 11px; color: #666; margin-top: 3px;">
                                <?php echo esc_html( $v['user_name'] ); ?> •
                                <?php echo esc_html( human_time_diff( strtotime( $v['timestamp'] ), current_time( 'timestamp' ) ) ); ?> 전
                                • <?php echo esc_html( $v['code_lines'] ); ?>줄
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <?php if ( $version_count > 5 ) : ?>
                    <button type="button" class="button button-secondary acf-csb-show-all-versions"
                            data-post-id="<?php echo esc_attr( $post->ID ); ?>"
                            style="width: 100%; margin-top: 10px;">
                        <?php printf( esc_html__( '모든 버전 보기 (%d)', 'acf-code-snippets-box' ), $version_count ); ?>
                    </button>
                <?php endif; ?>

                <button type="button" class="button button-link acf-csb-compare-versions"
                        data-post-id="<?php echo esc_attr( $post->ID ); ?>"
                        style="width: 100%; margin-top: 8px; text-align: center;">
                    📊 <?php esc_html_e( '버전 비교', 'acf-code-snippets-box' ); ?>
                </button>
            <?php else : ?>
                <p style="color: #666; font-size: 13px; margin: 0;">
                    <?php esc_html_e( '아직 저장된 버전이 없습니다. 스니펫을 저장하면 버전이 기록됩니다.', 'acf-code-snippets-box' ); ?>
                </p>
            <?php endif; ?>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // 버전 복원
            $('.acf-csb-restore-version').on('click', function(e) {
                e.preventDefault();
                var version = $(this).data('version');
                if (confirm('버전 ' + version + '으로 복원하시겠습니까? 현재 코드가 대체됩니다.')) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'acf_csb_restore_version',
                            post_id: <?php echo esc_js( $post->ID ); ?>,
                            version: version,
                            nonce: '<?php echo wp_create_nonce( 'acf_csb_version_nonce' ); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('복원 실패: ' + (response.data || '알 수 없는 오류'));
                            }
                        }
                    });
                }
            });
        });
        </script>
        <?php
    }

    /**
     * AJAX: 버전 목록 가져오기
     */
    public function ajax_get_versions() {
        check_ajax_referer( 'acf_csb_version_nonce', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $versions = get_post_meta( $post_id, '_acf_csb_versions', true );
        wp_send_json_success( is_array( $versions ) ? $versions : array() );
    }

    /**
     * AJAX: 버전 복원
     */
    public function ajax_restore_version() {
        check_ajax_referer( 'acf_csb_version_nonce', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        $version = isset( $_POST['version'] ) ? intval( $_POST['version'] ) : 0;

        if ( ! $post_id || ! $version || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $versions = get_post_meta( $post_id, '_acf_csb_versions', true );
        if ( ! is_array( $versions ) ) {
            wp_send_json_error( '버전을 찾을 수 없습니다.' );
        }

        // 버전 찾기
        $target_version = null;
        foreach ( $versions as $v ) {
            if ( $v['version'] === $version ) {
                $target_version = $v;
                break;
            }
        }

        if ( ! $target_version ) {
            wp_send_json_error( '버전을 찾을 수 없습니다.' );
        }

        // 코드 복원
        update_post_meta( $post_id, '_acf_csb_code', $target_version['code'] );
        if ( isset( $target_version['code_type'] ) ) {
            update_post_meta( $post_id, '_acf_csb_code_type', $target_version['code_type'] );
        }

        wp_send_json_success( array(
            'message' => '버전 ' . $version . '으로 복원되었습니다.',
            'code'    => $target_version['code'],
        ) );
    }

    /**
     * AJAX: 버전 비교
     */
    public function ajax_compare_versions() {
        check_ajax_referer( 'acf_csb_version_nonce', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        $v1 = isset( $_POST['version1'] ) ? intval( $_POST['version1'] ) : 0;
        $v2 = isset( $_POST['version2'] ) ? intval( $_POST['version2'] ) : 0;

        if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $versions = get_post_meta( $post_id, '_acf_csb_versions', true );
        if ( ! is_array( $versions ) ) {
            wp_send_json_error( '버전을 찾을 수 없습니다.' );
        }

        $code1 = null;
        $code2 = null;
        foreach ( $versions as $v ) {
            if ( $v['version'] === $v1 ) $code1 = $v['code'];
            if ( $v['version'] === $v2 ) $code2 = $v['code'];
        }

        if ( $code1 === null || $code2 === null ) {
            wp_send_json_error( '버전을 찾을 수 없습니다.' );
        }

        // 간단한 diff 생성
        $diff = $this->generate_diff( $code1, $code2 );

        wp_send_json_success( array(
            'diff' => $diff,
            'code1' => $code1,
            'code2' => $code2,
        ) );
    }

    /**
     * 간단한 Diff 생성
     */
    private function generate_diff( $old, $new ) {
        $old_lines = explode( "\n", $old );
        $new_lines = explode( "\n", $new );

        $diff = array();
        $max_lines = max( count( $old_lines ), count( $new_lines ) );

        for ( $i = 0; $i < $max_lines; $i++ ) {
            $old_line = isset( $old_lines[ $i ] ) ? $old_lines[ $i ] : null;
            $new_line = isset( $new_lines[ $i ] ) ? $new_lines[ $i ] : null;

            if ( $old_line === $new_line ) {
                $diff[] = array( 'type' => 'same', 'line' => $i + 1, 'content' => $old_line );
            } elseif ( $old_line === null ) {
                $diff[] = array( 'type' => 'added', 'line' => $i + 1, 'content' => $new_line );
            } elseif ( $new_line === null ) {
                $diff[] = array( 'type' => 'removed', 'line' => $i + 1, 'content' => $old_line );
            } else {
                $diff[] = array( 'type' => 'removed', 'line' => $i + 1, 'content' => $old_line );
                $diff[] = array( 'type' => 'added', 'line' => $i + 1, 'content' => $new_line );
            }
        }

        return $diff;
    }

    /**
     * 버전 개수 가져오기
     */
    public static function get_version_count( $post_id ) {
        $versions = get_post_meta( $post_id, '_acf_csb_versions', true );
        return is_array( $versions ) ? count( $versions ) : 0;
    }
}
