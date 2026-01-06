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

    // ===== [Phase 49-5] 고급 버전 관리 기능 =====

    /**
     * 버전에 태그 추가
     *
     * @param int    $post_id    스니펫 ID
     * @param int    $version    버전 번호
     * @param string $tag_name   태그 이름
     * @param string $tag_color  태그 색상
     * @return bool
     */
    public function add_version_tag( $post_id, $version, $tag_name, $tag_color = '#6366f1' ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return false;
        }

        $versions = get_post_meta( $post_id, '_acf_csb_versions', true );
        if ( ! is_array( $versions ) ) {
            return false;
        }

        foreach ( $versions as &$v ) {
            if ( $v['version'] === $version ) {
                if ( ! isset( $v['tags'] ) ) {
                    $v['tags'] = array();
                }
                $v['tags'][] = array(
                    'name'       => sanitize_text_field( $tag_name ),
                    'color'      => sanitize_hex_color( $tag_color ),
                    'created_at' => current_time( 'mysql' ),
                    'user_id'    => get_current_user_id(),
                );
                update_post_meta( $post_id, '_acf_csb_versions', $versions );
                return true;
            }
        }

        return false;
    }

    /**
     * 버전 태그 제거
     *
     * @param int    $post_id   스니펫 ID
     * @param int    $version   버전 번호
     * @param string $tag_name  태그 이름
     * @return bool
     */
    public function remove_version_tag( $post_id, $version, $tag_name ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return false;
        }

        $versions = get_post_meta( $post_id, '_acf_csb_versions', true );
        if ( ! is_array( $versions ) ) {
            return false;
        }

        foreach ( $versions as &$v ) {
            if ( $v['version'] === $version && isset( $v['tags'] ) ) {
                $v['tags'] = array_filter( $v['tags'], function( $tag ) use ( $tag_name ) {
                    return $tag['name'] !== $tag_name;
                } );
                update_post_meta( $post_id, '_acf_csb_versions', $versions );
                return true;
            }
        }

        return false;
    }

    /**
     * 태그가 있는 버전 찾기
     *
     * @param int    $post_id  스니펫 ID
     * @param string $tag_name 태그 이름
     * @return array|null
     */
    public function find_tagged_version( $post_id, $tag_name ) {
        $versions = get_post_meta( $post_id, '_acf_csb_versions', true );
        if ( ! is_array( $versions ) ) {
            return null;
        }

        foreach ( $versions as $v ) {
            if ( isset( $v['tags'] ) ) {
                foreach ( $v['tags'] as $tag ) {
                    if ( $tag['name'] === $tag_name ) {
                        return $v;
                    }
                }
            }
        }

        return null;
    }

    /**
     * 브랜치 생성
     *
     * @param int    $post_id      원본 스니펫 ID
     * @param int    $version      브랜치 시작 버전
     * @param string $branch_name  브랜치 이름
     * @return int|WP_Error 새 스니펫 ID 또는 에러
     */
    public function create_branch( $post_id, $version, $branch_name ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return new WP_Error( 'permission_denied', '권한이 없습니다.' );
        }

        $versions = get_post_meta( $post_id, '_acf_csb_versions', true );
        if ( ! is_array( $versions ) ) {
            return new WP_Error( 'no_versions', '버전을 찾을 수 없습니다.' );
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
            return new WP_Error( 'version_not_found', '버전을 찾을 수 없습니다.' );
        }

        // 원본 포스트 정보 가져오기
        $original_post = get_post( $post_id );

        // 새 스니펫 생성
        $new_post_id = wp_insert_post( array(
            'post_type'   => 'acf_code_snippet',
            'post_title'  => $original_post->post_title . ' [Branch: ' . $branch_name . ']',
            'post_status' => 'draft',
            'post_author' => get_current_user_id(),
        ) );

        if ( is_wp_error( $new_post_id ) ) {
            return $new_post_id;
        }

        // 메타 복사
        update_post_meta( $new_post_id, '_acf_csb_code', $target_version['code'] );
        if ( isset( $target_version['code_type'] ) ) {
            update_post_meta( $new_post_id, '_acf_csb_code_type', $target_version['code_type'] );
        }

        // 브랜치 메타데이터
        update_post_meta( $new_post_id, '_acf_csb_branch_info', array(
            'parent_id'      => $post_id,
            'parent_version' => $version,
            'branch_name'    => $branch_name,
            'created_at'     => current_time( 'mysql' ),
            'created_by'     => get_current_user_id(),
        ) );

        // 원본에 브랜치 정보 추가
        $branches = get_post_meta( $post_id, '_acf_csb_branches', true );
        if ( ! is_array( $branches ) ) {
            $branches = array();
        }
        $branches[] = array(
            'branch_id'   => $new_post_id,
            'branch_name' => $branch_name,
            'version'     => $version,
            'created_at'  => current_time( 'mysql' ),
        );
        update_post_meta( $post_id, '_acf_csb_branches', $branches );

        return $new_post_id;
    }

    /**
     * 브랜치 머지
     *
     * @param int    $branch_id    브랜치 스니펫 ID
     * @param int    $target_id    대상 스니펫 ID
     * @param string $merge_mode   머지 모드 (replace, append)
     * @return bool|WP_Error
     */
    public function merge_branch( $branch_id, $target_id, $merge_mode = 'replace' ) {
        if ( ! current_user_can( 'edit_post', $branch_id ) || ! current_user_can( 'edit_post', $target_id ) ) {
            return new WP_Error( 'permission_denied', '권한이 없습니다.' );
        }

        $branch_code = get_post_meta( $branch_id, '_acf_csb_code', true );
        if ( empty( $branch_code ) ) {
            return new WP_Error( 'no_code', '브랜치 코드가 비어있습니다.' );
        }

        if ( $merge_mode === 'replace' ) {
            // 현재 코드를 백업 태그로 저장
            $this->add_version_tag( $target_id, $this->get_current_version( $target_id ), 'pre-merge-backup', '#f59e0b' );

            // 코드 교체
            update_post_meta( $target_id, '_acf_csb_code', $branch_code );
        } elseif ( $merge_mode === 'append' ) {
            $target_code = get_post_meta( $target_id, '_acf_csb_code', true );
            $merged_code = $target_code . "\n\n/* === Merged from Branch === */\n" . $branch_code;
            update_post_meta( $target_id, '_acf_csb_code', $merged_code );
        }

        // 머지 기록
        $merge_history = get_post_meta( $target_id, '_acf_csb_merge_history', true );
        if ( ! is_array( $merge_history ) ) {
            $merge_history = array();
        }
        $merge_history[] = array(
            'branch_id'  => $branch_id,
            'merged_at'  => current_time( 'mysql' ),
            'merged_by'  => get_current_user_id(),
            'merge_mode' => $merge_mode,
        );
        update_post_meta( $target_id, '_acf_csb_merge_history', $merge_history );

        return true;
    }

    /**
     * 현재 버전 번호 가져오기
     *
     * @param int $post_id 스니펫 ID
     * @return int
     */
    public function get_current_version( $post_id ) {
        $versions = get_post_meta( $post_id, '_acf_csb_versions', true );
        if ( ! is_array( $versions ) || empty( $versions ) ) {
            return 0;
        }
        $last = end( $versions );
        return isset( $last['version'] ) ? $last['version'] : 0;
    }

    /**
     * 자동 백업 스케줄 설정
     *
     * @param int    $post_id  스니펫 ID
     * @param string $interval 백업 간격 (hourly, twicedaily, daily)
     */
    public function schedule_auto_backup( $post_id, $interval = 'daily' ) {
        $hook = 'acf_csb_auto_backup_' . $post_id;

        // 기존 스케줄 제거
        wp_clear_scheduled_hook( $hook );

        if ( $interval !== 'disabled' ) {
            wp_schedule_event( time(), $interval, $hook );
        }

        update_post_meta( $post_id, '_acf_csb_auto_backup_interval', $interval );
    }

    /**
     * 스냅샷 생성 (수동 백업)
     *
     * @param int    $post_id     스니펫 ID
     * @param string $description 스냅샷 설명
     * @return bool
     */
    public function create_snapshot( $post_id, $description = '' ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return false;
        }

        $code = get_post_meta( $post_id, '_acf_csb_code', true );
        if ( empty( $code ) ) {
            return false;
        }

        $snapshots = get_post_meta( $post_id, '_acf_csb_snapshots', true );
        if ( ! is_array( $snapshots ) ) {
            $snapshots = array();
        }

        $current_user = wp_get_current_user();
        $snapshots[] = array(
            'id'          => uniqid( 'snap_' ),
            'code'        => $code,
            'code_type'   => get_post_meta( $post_id, '_acf_csb_code_type', true ),
            'description' => sanitize_text_field( $description ),
            'user_id'     => $current_user->ID,
            'user_name'   => $current_user->display_name,
            'timestamp'   => current_time( 'mysql' ),
            'code_hash'   => md5( $code ),
            'code_lines'  => substr_count( $code, "\n" ) + 1,
        );

        // 최대 50개 스냅샷 유지
        if ( count( $snapshots ) > 50 ) {
            $snapshots = array_slice( $snapshots, -50 );
        }

        update_post_meta( $post_id, '_acf_csb_snapshots', $snapshots );
        return true;
    }

    /**
     * 스냅샷 복원
     *
     * @param int    $post_id     스니펫 ID
     * @param string $snapshot_id 스냅샷 ID
     * @return bool
     */
    public function restore_snapshot( $post_id, $snapshot_id ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return false;
        }

        $snapshots = get_post_meta( $post_id, '_acf_csb_snapshots', true );
        if ( ! is_array( $snapshots ) ) {
            return false;
        }

        foreach ( $snapshots as $snapshot ) {
            if ( $snapshot['id'] === $snapshot_id ) {
                update_post_meta( $post_id, '_acf_csb_code', $snapshot['code'] );
                if ( isset( $snapshot['code_type'] ) ) {
                    update_post_meta( $post_id, '_acf_csb_code_type', $snapshot['code_type'] );
                }
                return true;
            }
        }

        return false;
    }

    /**
     * 버전 통계 가져오기
     *
     * @param int $post_id 스니펫 ID
     * @return array
     */
    public function get_version_stats( $post_id ) {
        $versions = get_post_meta( $post_id, '_acf_csb_versions', true );
        if ( ! is_array( $versions ) || empty( $versions ) ) {
            return array(
                'total_versions'   => 0,
                'total_edits'      => 0,
                'unique_editors'   => 0,
                'avg_code_lines'   => 0,
                'code_growth'      => 0,
                'first_version'    => null,
                'latest_version'   => null,
                'most_active_day'  => null,
            );
        }

        $total = count( $versions );
        $editors = array();
        $total_lines = 0;
        $edits_by_day = array();

        foreach ( $versions as $v ) {
            if ( isset( $v['user_id'] ) ) {
                $editors[ $v['user_id'] ] = true;
            }
            if ( isset( $v['code_lines'] ) ) {
                $total_lines += $v['code_lines'];
            }
            if ( isset( $v['timestamp'] ) ) {
                $day = date( 'Y-m-d', strtotime( $v['timestamp'] ) );
                if ( ! isset( $edits_by_day[ $day ] ) ) {
                    $edits_by_day[ $day ] = 0;
                }
                $edits_by_day[ $day ]++;
            }
        }

        $first = reset( $versions );
        $last = end( $versions );
        $first_lines = isset( $first['code_lines'] ) ? $first['code_lines'] : 0;
        $last_lines = isset( $last['code_lines'] ) ? $last['code_lines'] : 0;
        $code_growth = $first_lines > 0 ? round( ( ( $last_lines - $first_lines ) / $first_lines ) * 100, 1 ) : 0;

        arsort( $edits_by_day );
        $most_active_day = ! empty( $edits_by_day ) ? key( $edits_by_day ) : null;

        return array(
            'total_versions'   => $total,
            'total_edits'      => $total,
            'unique_editors'   => count( $editors ),
            'avg_code_lines'   => round( $total_lines / $total ),
            'code_growth'      => $code_growth,
            'first_version'    => $first,
            'latest_version'   => $last,
            'most_active_day'  => $most_active_day,
        );
    }

    /**
     * 버전 내보내기 (JSON)
     *
     * @param int   $post_id  스니펫 ID
     * @param array $options  내보내기 옵션
     * @return string JSON 문자열
     */
    public function export_versions( $post_id, $options = array() ) {
        $versions = get_post_meta( $post_id, '_acf_csb_versions', true );
        if ( ! is_array( $versions ) ) {
            $versions = array();
        }

        $post = get_post( $post_id );
        $export_data = array(
            'snippet_id'    => $post_id,
            'snippet_title' => $post->post_title,
            'exported_at'   => current_time( 'mysql' ),
            'versions'      => $versions,
        );

        if ( ! empty( $options['include_snapshots'] ) ) {
            $export_data['snapshots'] = get_post_meta( $post_id, '_acf_csb_snapshots', true ) ?: array();
        }

        if ( ! empty( $options['include_branches'] ) ) {
            $export_data['branches'] = get_post_meta( $post_id, '_acf_csb_branches', true ) ?: array();
        }

        return wp_json_encode( $export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    /**
     * 버전 가져오기 (JSON)
     *
     * @param int    $post_id   대상 스니펫 ID
     * @param string $json_data JSON 문자열
     * @return bool|WP_Error
     */
    public function import_versions( $post_id, $json_data ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return new WP_Error( 'permission_denied', '권한이 없습니다.' );
        }

        $data = json_decode( $json_data, true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'invalid_json', 'JSON 파싱 오류: ' . json_last_error_msg() );
        }

        if ( ! isset( $data['versions'] ) || ! is_array( $data['versions'] ) ) {
            return new WP_Error( 'no_versions', '버전 데이터가 없습니다.' );
        }

        // 기존 버전 백업
        $existing = get_post_meta( $post_id, '_acf_csb_versions', true );
        if ( is_array( $existing ) && ! empty( $existing ) ) {
            update_post_meta( $post_id, '_acf_csb_versions_backup', $existing );
        }

        // 버전 가져오기
        update_post_meta( $post_id, '_acf_csb_versions', $data['versions'] );

        // 스냅샷 가져오기
        if ( isset( $data['snapshots'] ) && is_array( $data['snapshots'] ) ) {
            update_post_meta( $post_id, '_acf_csb_snapshots', $data['snapshots'] );
        }

        return true;
    }
}
