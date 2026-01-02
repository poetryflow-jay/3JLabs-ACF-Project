<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="jj-admin-center-tab-content" data-tab="system-status">
    <div class="jj-system-status-wrap">
        <h3><?php esc_html_e( '시스템 상태', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <p class="description">
            <?php esc_html_e( '플러그인의 메모리 사용량, 캐시 상태, 성능 정보를 확인할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>

        <?php
        // [Phase 8.0] 매니페스트 검증 및 자가진단
        $manifest_diagnosis = array();
        $can_repair = false;
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'validate_manifest' ) ) {
            $manifest_result = JJ_Safe_Loader::validate_manifest();
            if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'generate_diagnosis' ) ) {
                $manifest_diagnosis = JJ_Safe_Loader::generate_diagnosis();
                $can_repair = ! empty( $manifest_diagnosis['can_repair'] );
            }
        }
        $has_missing_required = ! empty( $manifest_diagnosis['diagnosis']['manifest']['missing_required'] );
        
        // 활성화 오류 확인
        $activation_error = get_option( 'jj_style_guide_activation_error', null );
        ?>

        <!-- [Phase 6] 자가 진단 기능 -->
        <div class="jj-self-test-section" style="margin-bottom: 25px; padding: 15px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; border-left: 4px solid #2271b1;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="margin: 0 0 5px 0;"><?php esc_html_e( '🩺 자가 진단 (Self-Health Check)', 'acf-css-really-simple-style-management-center' ); ?></h4>
                    <p style="margin: 0; font-size: 13px; color: #666;">
                        <?php esc_html_e( '플러그인의 핵심 기능이 정상 작동하는지 검사합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="button" id="jj-copy-self-test" disabled>
                        <?php esc_html_e( '결과 복사', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                    <button type="button" class="button" id="jj-download-self-test" disabled>
                        <?php esc_html_e( 'JSON 다운로드', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                    <button type="button" class="button button-primary" id="jj-run-self-test">
                        <?php esc_html_e( '자가 진단 실행', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                </div>
            </div>
            
            <?php if ( $has_missing_required || $activation_error ) : ?>
                <!-- [Phase 8.0] 매니페스트 검증 결과 및 해결 방법 -->
                <div style="margin-top: 15px; padding: 14px; background: <?php echo $has_missing_required ? '#f8d7da' : '#fff3cd'; ?>; border: 2px solid <?php echo $has_missing_required ? '#d63638' : '#ffc107'; ?>; border-radius: 4px;">
                    <h4 style="margin: 0 0 10px 0; color: <?php echo $has_missing_required ? '#721c24' : '#856404'; ?>;">
                        <span class="dashicons dashicons-<?php echo $has_missing_required ? 'warning' : 'info'; ?>" style="vertical-align: middle;"></span>
                        <?php esc_html_e( '파일 누락 감지', 'acf-css-really-simple-style-management-center' ); ?>
                    </h4>
                    <?php if ( $has_missing_required ) : ?>
                        <p style="margin: 0 0 10px 0; color: #721c24;">
                            <?php
                            printf(
                                /* translators: %d: number of missing files */
                                esc_html__( '필수 파일 %d개가 누락되었습니다. 플러그인이 정상 작동하지 않을 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                                count( $manifest_diagnosis['diagnosis']['manifest']['missing_required'] )
                            );
                            ?>
                        </p>
                        <ul style="margin: 10px 0 0 20px; padding: 0; color: #721c24;">
                            <?php foreach ( array_slice( $manifest_diagnosis['diagnosis']['manifest']['missing_required'], 0, 10 ) as $missing ) : ?>
                                <li style="margin-bottom: 4px;">
                                    <code><?php echo esc_html( $missing['path'] ); ?></code>
                                </li>
                            <?php endforeach; ?>
                            <?php if ( count( $manifest_diagnosis['diagnosis']['manifest']['missing_required'] ) > 10 ) : ?>
                                <li style="color: #856404;">
                                    <?php
                                    printf(
                                        /* translators: %d: additional count */
                                        esc_html__( '... 외 %d개 파일 누락', 'acf-css-really-simple-style-management-center' ),
                                        count( $manifest_diagnosis['diagnosis']['manifest']['missing_required'] ) - 10
                                    );
                                    ?>
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                    
                    <?php if ( $activation_error ) : ?>
                        <div style="margin-top: 12px; padding: 10px; background: #fff; border: 1px solid #d63638; border-radius: 4px;">
                            <strong style="color: #721c24;"><?php esc_html_e( '활성화 오류:', 'acf-css-really-simple-style-management-center' ); ?></strong>
                            <div style="margin-top: 6px; color: #721c24; font-size: 12px;">
                                <?php
                                $reason = isset( $activation_error['reason'] ) ? $activation_error['reason'] : 'unknown';
                                $message = isset( $activation_error['message'] ) ? $activation_error['message'] : '';
                                
                                if ( 'minimal_boot_failed' === $reason ) {
                                    esc_html_e( '최소 부팅 경로 실패: 필수 파일이 누락되어 플러그인을 시작할 수 없습니다.', 'acf-css-really-simple-style-management-center' );
                                } elseif ( 'activation_exception' === $reason || 'activation_fatal_error' === $reason ) {
                                    printf(
                                        /* translators: %s: error message */
                                        esc_html__( '활성화 중 오류: %s', 'acf-css-really-simple-style-management-center' ),
                                        esc_html( $message )
                                    );
                                } else {
                                    esc_html_e( '알 수 없는 활성화 오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' );
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( ! empty( $manifest_diagnosis['solutions'] ) ) : ?>
                        <div style="margin-top: 12px;">
                            <strong style="color: #721c24; display: block; margin-bottom: 8px;"><?php esc_html_e( '💡 해결 방법:', 'acf-css-really-simple-style-management-center' ); ?></strong>
                            <?php foreach ( $manifest_diagnosis['solutions'] as $solution ) : ?>
                                <div style="margin-bottom: 8px; padding: 10px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px;">
                                    <strong style="color: #0f172a;"><?php echo esc_html( $solution['title'] ); ?></strong>
                                    <p style="margin: 6px 0 0 0; font-size: 12px; color: #475569;">
                                        <?php echo esc_html( $solution['description'] ); ?>
                                    </p>
                                    <?php if ( ! empty( $solution['action_url'] ) ) : ?>
                                        <a href="<?php echo esc_url( $solution['action_url'] ); ?>" class="button button-small" style="margin-top: 8px;">
                                            <?php echo esc_html( $solution['title'] ); ?>
                                        </a>
                                    <?php elseif ( 'repair_missing_files' === $solution['action'] && $can_repair ) : ?>
                                        <button type="button" class="button button-small button-primary" id="jj-repair-missing-files" style="margin-top: 8px;">
                                            <?php esc_html_e( '복구 시도', 'acf-css-really-simple-style-management-center' ); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div style="margin-top: 12px; padding: 10px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px;">
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <button type="button" class="button button-small" id="jj-copy-diagnosis-log">
                                <?php esc_html_e( '로그 복사', 'acf-css-really-simple-style-management-center' ); ?>
                            </button>
                            <button type="button" class="button button-small" id="jj-download-diagnosis-log">
                                <?php esc_html_e( '로그 다운로드', 'acf-css-really-simple-style-management-center' ); ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php elseif ( ! empty( $manifest_diagnosis ) && ! empty( $manifest_diagnosis['diagnosis']['manifest']['missing_optional'] ) ) : ?>
                <!-- 선택적 파일 누락 알림 (경고) -->
                <div style="margin-top: 15px; padding: 12px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
                    <p style="margin: 0; color: #856404; font-size: 13px;">
                        <span class="dashicons dashicons-info" style="vertical-align: middle;"></span>
                        <?php
                        printf(
                            /* translators: %d: number of optional files */
                            esc_html__( '선택적 파일 %d개가 누락되었습니다. 일부 기능이 제한될 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                            count( $manifest_diagnosis['diagnosis']['manifest']['missing_optional'] )
                        );
                        ?>
                    </p>
                </div>
            <?php endif; ?>
            
            <div id="jj-self-test-results" style="margin-top: 15px; display: none;">
                <hr style="margin: 15px 0;">
                <div class="jj-test-progress">
                    <span class="spinner is-active" style="float: none; margin: 0 5px 0 0;"></span>
                    <span class="jj-test-status-text"><?php esc_html_e( '진단 중...', 'acf-css-really-simple-style-management-center' ); ?></span>
                </div>
                <ul class="jj-test-results-list" style="list-style: none; margin: 10px 0 0 0; padding: 0;"></ul>
            </div>
        </div>

        <?php
        // [Phase 8.2] 보안 로그 표시
        $security_logs = array();
        if ( class_exists( 'JJ_Security_Hardener' ) ) {
            $security_logs = JJ_Security_Hardener::get_security_logs( 20 );
        }
        ?>
        
        <?php if ( ! empty( $security_logs ) ) : ?>
            <!-- [Phase 8.2] 보안 이벤트 로그 -->
            <div class="jj-security-logs-section" style="margin: 20px 0; padding: 15px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; border-left: 4px solid #d63638;">
                <h4 style="margin: 0 0 10px 0;">
                    <span class="dashicons dashicons-shield-alt" style="vertical-align: middle;"></span>
                    <?php esc_html_e( '보안 이벤트 로그', 'acf-css-really-simple-style-management-center' ); ?>
                </h4>
                <p style="margin: 0 0 12px 0; font-size: 13px; color: #666;">
                    <?php esc_html_e( '최근 보안 관련 이벤트를 확인할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                <table class="widefat striped" style="margin-top: 10px;">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( '시간', 'acf-css-really-simple-style-management-center' ); ?></th>
                            <th><?php esc_html_e( '이벤트 타입', 'acf-css-really-simple-style-management-center' ); ?></th>
                            <th><?php esc_html_e( '상세', 'acf-css-really-simple-style-management-center' ); ?></th>
                            <th><?php esc_html_e( 'IP', 'acf-css-really-simple-style-management-center' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( array_reverse( array_slice( $security_logs, -10 ) ) as $log ) : ?>
                            <tr>
                                <td style="white-space: nowrap;">
                                    <?php echo esc_html( isset( $log['timestamp'] ) ? $log['timestamp'] : '' ); ?>
                                </td>
                                <td>
                                    <code style="font-size: 11px;">
                                        <?php echo esc_html( isset( $log['type'] ) ? $log['type'] : '' ); ?>
                                    </code>
                                </td>
                                <td>
                                    <?php
                                    if ( isset( $log['data'] ) && is_array( $log['data'] ) ) {
                                        $details = array();
                                        if ( isset( $log['data']['action'] ) ) {
                                            $details[] = 'Action: ' . esc_html( $log['data']['action'] );
                                        }
                                        if ( isset( $log['data']['capability'] ) ) {
                                            $details[] = 'Required: ' . esc_html( $log['data']['capability'] );
                                        }
                                        echo implode( ', ', $details );
                                    }
                                    ?>
                                </td>
                                <td>
                                    <code style="font-size: 11px;">
                                        <?php echo esc_html( isset( $log['ip'] ) ? $log['ip'] : '' ); ?>
                                    </code>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php
        // 메모리 사용량 정보
        $memory_stats = array();
        if ( class_exists( 'JJ_Memory_Manager' ) && method_exists( 'JJ_Memory_Manager', 'instance' ) ) {
            $memory_manager = JJ_Memory_Manager::instance();
            if ( method_exists( $memory_manager, 'get_stats' ) ) {
                $memory_stats = $memory_manager->get_stats();
            }
        }

        // 옵션 캐시 통계
        $options_cache_stats = array();
        if ( class_exists( 'JJ_Options_Cache' ) && method_exists( 'JJ_Options_Cache', 'instance' ) ) {
            $options_cache = JJ_Options_Cache::instance();
            if ( method_exists( $options_cache, 'get_stats' ) ) {
                $options_cache_stats = $options_cache->get_stats();
            }
        }

        // CSS 캐시 통계
        $css_cache_stats = array();
        if ( class_exists( 'JJ_CSS_Cache' ) && method_exists( 'JJ_CSS_Cache', 'instance' ) ) {
            $css_cache = JJ_CSS_Cache::instance();
            if ( method_exists( $css_cache, 'get_stats' ) ) {
                $css_cache_stats = $css_cache->get_stats();
            }
        }

        // 안전 모드 정보
        $safe_mode_info = array();
        if ( class_exists( 'JJ_Safe_Mode' ) && method_exists( 'JJ_Safe_Mode', 'instance' ) ) {
            $safe_mode = JJ_Safe_Mode::instance();
            if ( method_exists( $safe_mode, 'get_info' ) ) {
                $safe_mode_info = $safe_mode->get_info();
            }
        }
        ?>

        <table class="form-table" role="presentation">
            <tbody>
                <!-- 메모리 사용량 -->
                <?php if ( ! empty( $memory_stats ) ) : ?>
                <tr>
                    <th scope="row" colspan="2">
                        <h4 style="margin: 20px 0 10px 0;"><?php esc_html_e( '메모리 사용량', 'acf-css-really-simple-style-management-center' ); ?></h4>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '메모리 제한', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( isset( $memory_stats['memory_limit_formatted'] ) ? $memory_stats['memory_limit_formatted'] : 'N/A' ); ?></strong>
                        <?php if ( isset( $memory_stats['is_low_memory'] ) && $memory_stats['is_low_memory'] ) : ?>
                            <span style="color: #d63638; margin-left: 10px;"><?php esc_html_e( '(낮은 메모리 환경)', 'acf-css-really-simple-style-management-center' ); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '현재 사용량', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( isset( $memory_stats['current_usage_formatted'] ) ? $memory_stats['current_usage_formatted'] : 'N/A' ); ?></strong>
                        <?php if ( isset( $memory_stats['usage_percentage'] ) ) : ?>
                            <span style="margin-left: 10px; color: <?php echo $memory_stats['usage_percentage'] > 80 ? '#d63638' : ( $memory_stats['usage_percentage'] > 60 ? '#dba617' : '#2271b1' ); ?>;">
                                (<?php echo esc_html( $memory_stats['usage_percentage'] ); ?>%)
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '피크 사용량', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( isset( $memory_stats['peak_usage_formatted'] ) ? $memory_stats['peak_usage_formatted'] : 'N/A' ); ?></strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '사용 가능', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( isset( $memory_stats['available_formatted'] ) ? $memory_stats['available_formatted'] : 'N/A' ); ?></strong>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- 옵션 캐시 통계 -->
                <?php if ( ! empty( $options_cache_stats ) ) : ?>
                <tr>
                    <th scope="row" colspan="2">
                        <h4 style="margin: 20px 0 10px 0;"><?php esc_html_e( '옵션 캐시', 'acf-css-really-simple-style-management-center' ); ?></h4>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '캐시된 옵션 수', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( isset( $options_cache_stats['cached_options'] ) ? $options_cache_stats['cached_options'] : 0 ); ?></strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '메모리 사용량', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <td>
                        <?php
                        if ( isset( $options_cache_stats['memory_usage'] ) ) {
                            $memory_manager = class_exists( 'JJ_Memory_Manager' ) ? JJ_Memory_Manager::instance() : null;
                            if ( $memory_manager && method_exists( $memory_manager, 'format_bytes' ) ) {
                                echo '<strong>' . esc_html( $memory_manager->format_bytes( $options_cache_stats['memory_usage'] ) ) . '</strong>';
                            } else {
                                echo '<strong>' . esc_html( number_format( $options_cache_stats['memory_usage'] / 1024, 2 ) . ' KB' ) . '</strong>';
                            }
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- CSS 캐시 통계 -->
                <?php if ( ! empty( $css_cache_stats ) ) : ?>
                <tr>
                    <th scope="row" colspan="2">
                        <h4 style="margin: 20px 0 10px 0;"><?php esc_html_e( 'CSS 캐시', 'acf-css-really-simple-style-management-center' ); ?></h4>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '캐시 항목 수', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( isset( $css_cache_stats['count'] ) ? $css_cache_stats['count'] : 0 ); ?></strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '총 크기', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <td>
                        <?php
                        if ( isset( $css_cache_stats['total_size'] ) ) {
                            $memory_manager = class_exists( 'JJ_Memory_Manager' ) ? JJ_Memory_Manager::instance() : null;
                            if ( $memory_manager && method_exists( $memory_manager, 'format_bytes' ) ) {
                                echo '<strong>' . esc_html( $memory_manager->format_bytes( $css_cache_stats['total_size'] ) ) . '</strong>';
                            } else {
                                echo '<strong>' . esc_html( number_format( $css_cache_stats['total_size'] / 1024, 2 ) . ' KB' ) . '</strong>';
                            }
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '메모리 캐시 항목 수', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( isset( $css_cache_stats['memory_cache_count'] ) ? $css_cache_stats['memory_cache_count'] : 0 ); ?></strong>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- 안전 모드 상태 -->
                <?php if ( ! empty( $safe_mode_info ) ) : ?>
                <tr>
                    <th scope="row" colspan="2">
                        <h4 style="margin: 20px 0 10px 0;"><?php esc_html_e( '안전 모드', 'acf-css-really-simple-style-management-center' ); ?></h4>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '상태', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <td>
                        <?php if ( isset( $safe_mode_info['enabled'] ) && $safe_mode_info['enabled'] ) : ?>
                            <span style="color: #d63638; font-weight: 600;"><?php esc_html_e( '활성화됨', 'acf-css-really-simple-style-management-center' ); ?></span>
                            <?php if ( ! empty( $safe_mode_info['reason'] ) ) : ?>
                                <p class="description" style="margin-top: 5px;">
                                    <?php echo esc_html( $safe_mode_info['reason'] ); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ( ! empty( $safe_mode_info['timestamp'] ) ) : ?>
                                <p class="description" style="margin-top: 5px;">
                                    <?php echo esc_html( sprintf( __( '활성화 시간: %s', 'acf-css-really-simple-style-management-center' ), $safe_mode_info['timestamp'] ) ); ?>
                                </p>
                            <?php endif; ?>
                        <?php else : ?>
                            <span style="color: #2271b1; font-weight: 600;"><?php esc_html_e( '비활성화됨', 'acf-css-really-simple-style-management-center' ); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- 활성화 상태 -->
                <?php
                $activation_completed = function_exists( 'get_option' ) ? get_option( 'jj_style_guide_activation_completed', false ) : false;
                $activation_failed_step = function_exists( 'get_option' ) ? get_option( 'jj_style_guide_activation_failed_step', '' ) : '';
                $activation_error = function_exists( 'get_option' ) ? get_option( 'jj_style_guide_activation_error', '' ) : '';
                ?>
                <tr>
                    <th scope="row" colspan="2">
                        <h4 style="margin: 20px 0 10px 0;"><?php esc_html_e( '활성화 상태', 'acf-css-really-simple-style-management-center' ); ?></h4>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '활성화 완료', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <td>
                        <?php if ( $activation_completed ) : ?>
                            <span style="color: #2271b1; font-weight: 600;"><?php esc_html_e( '예', 'acf-css-really-simple-style-management-center' ); ?></span>
                        <?php else : ?>
                            <span style="color: #d63638; font-weight: 600;"><?php esc_html_e( '아니오', 'acf-css-really-simple-style-management-center' ); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ( ! empty( $activation_failed_step ) ) : ?>
                <tr>
                    <th scope="row"><?php esc_html_e( '실패한 단계', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <td>
                        <span style="color: #d63638;"><?php echo esc_html( $activation_failed_step ); ?></span>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if ( ! empty( $activation_error ) ) : ?>
                <tr>
                    <th scope="row"><?php esc_html_e( '오류 메시지', 'acf-css-really-simple-style-management-center' ); ?></th>
                    <td>
                        <p style="color: #d63638; margin: 0;"><?php echo esc_html( $activation_error ); ?></p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

