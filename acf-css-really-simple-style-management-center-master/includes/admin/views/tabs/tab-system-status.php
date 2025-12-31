<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="jj-admin-center-tab-content" data-tab="system-status">
    <div class="jj-system-status-wrap">
        <h3><?php esc_html_e( '시스템 상태', 'jj-style-guide' ); ?></h3>
        <p class="description">
            <?php esc_html_e( '플러그인의 메모리 사용량, 캐시 상태, 성능 정보를 확인할 수 있습니다.', 'jj-style-guide' ); ?>
        </p>

        <!-- [Phase 6] 자가 진단 기능 -->
        <div class="jj-self-test-section" style="margin-bottom: 25px; padding: 15px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; border-left: 4px solid #2271b1;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="margin: 0 0 5px 0;"><?php esc_html_e( '🩺 자가 진단 (Self-Health Check)', 'jj-style-guide' ); ?></h4>
                    <p style="margin: 0; font-size: 13px; color: #666;">
                        <?php esc_html_e( '플러그인의 핵심 기능이 정상 작동하는지 검사합니다.', 'jj-style-guide' ); ?>
                    </p>
                </div>
                <button type="button" class="button button-primary" id="jj-run-self-test">
                    <?php esc_html_e( '자가 진단 실행', 'jj-style-guide' ); ?>
                </button>
            </div>
            <div id="jj-self-test-results" style="margin-top: 15px; display: none;">
                <hr style="margin: 15px 0;">
                <div class="jj-test-progress">
                    <span class="spinner is-active" style="float: none; margin: 0 5px 0 0;"></span>
                    <span class="jj-test-status-text"><?php esc_html_e( '진단 중...', 'jj-style-guide' ); ?></span>
                </div>
                <ul class="jj-test-results-list" style="list-style: none; margin: 10px 0 0 0; padding: 0;"></ul>
            </div>
        </div>

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
                        <h4 style="margin: 20px 0 10px 0;"><?php esc_html_e( '메모리 사용량', 'jj-style-guide' ); ?></h4>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '메모리 제한', 'jj-style-guide' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( isset( $memory_stats['memory_limit_formatted'] ) ? $memory_stats['memory_limit_formatted'] : 'N/A' ); ?></strong>
                        <?php if ( isset( $memory_stats['is_low_memory'] ) && $memory_stats['is_low_memory'] ) : ?>
                            <span style="color: #d63638; margin-left: 10px;"><?php esc_html_e( '(낮은 메모리 환경)', 'jj-style-guide' ); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '현재 사용량', 'jj-style-guide' ); ?></th>
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
                    <th scope="row"><?php esc_html_e( '피크 사용량', 'jj-style-guide' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( isset( $memory_stats['peak_usage_formatted'] ) ? $memory_stats['peak_usage_formatted'] : 'N/A' ); ?></strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '사용 가능', 'jj-style-guide' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( isset( $memory_stats['available_formatted'] ) ? $memory_stats['available_formatted'] : 'N/A' ); ?></strong>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- 옵션 캐시 통계 -->
                <?php if ( ! empty( $options_cache_stats ) ) : ?>
                <tr>
                    <th scope="row" colspan="2">
                        <h4 style="margin: 20px 0 10px 0;"><?php esc_html_e( '옵션 캐시', 'jj-style-guide' ); ?></h4>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '캐시된 옵션 수', 'jj-style-guide' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( isset( $options_cache_stats['cached_options'] ) ? $options_cache_stats['cached_options'] : 0 ); ?></strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '메모리 사용량', 'jj-style-guide' ); ?></th>
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
                        <h4 style="margin: 20px 0 10px 0;"><?php esc_html_e( 'CSS 캐시', 'jj-style-guide' ); ?></h4>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '캐시 항목 수', 'jj-style-guide' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( isset( $css_cache_stats['count'] ) ? $css_cache_stats['count'] : 0 ); ?></strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '총 크기', 'jj-style-guide' ); ?></th>
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
                    <th scope="row"><?php esc_html_e( '메모리 캐시 항목 수', 'jj-style-guide' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( isset( $css_cache_stats['memory_cache_count'] ) ? $css_cache_stats['memory_cache_count'] : 0 ); ?></strong>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- 안전 모드 상태 -->
                <?php if ( ! empty( $safe_mode_info ) ) : ?>
                <tr>
                    <th scope="row" colspan="2">
                        <h4 style="margin: 20px 0 10px 0;"><?php esc_html_e( '안전 모드', 'jj-style-guide' ); ?></h4>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '상태', 'jj-style-guide' ); ?></th>
                    <td>
                        <?php if ( isset( $safe_mode_info['enabled'] ) && $safe_mode_info['enabled'] ) : ?>
                            <span style="color: #d63638; font-weight: 600;"><?php esc_html_e( '활성화됨', 'jj-style-guide' ); ?></span>
                            <?php if ( ! empty( $safe_mode_info['reason'] ) ) : ?>
                                <p class="description" style="margin-top: 5px;">
                                    <?php echo esc_html( $safe_mode_info['reason'] ); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ( ! empty( $safe_mode_info['timestamp'] ) ) : ?>
                                <p class="description" style="margin-top: 5px;">
                                    <?php echo esc_html( sprintf( __( '활성화 시간: %s', 'jj-style-guide' ), $safe_mode_info['timestamp'] ) ); ?>
                                </p>
                            <?php endif; ?>
                        <?php else : ?>
                            <span style="color: #2271b1; font-weight: 600;"><?php esc_html_e( '비활성화됨', 'jj-style-guide' ); ?></span>
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
                        <h4 style="margin: 20px 0 10px 0;"><?php esc_html_e( '활성화 상태', 'jj-style-guide' ); ?></h4>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( '활성화 완료', 'jj-style-guide' ); ?></th>
                    <td>
                        <?php if ( $activation_completed ) : ?>
                            <span style="color: #2271b1; font-weight: 600;"><?php esc_html_e( '예', 'jj-style-guide' ); ?></span>
                        <?php else : ?>
                            <span style="color: #d63638; font-weight: 600;"><?php esc_html_e( '아니오', 'jj-style-guide' ); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ( ! empty( $activation_failed_step ) ) : ?>
                <tr>
                    <th scope="row"><?php esc_html_e( '실패한 단계', 'jj-style-guide' ); ?></th>
                    <td>
                        <span style="color: #d63638;"><?php echo esc_html( $activation_failed_step ); ?></span>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if ( ! empty( $activation_error ) ) : ?>
                <tr>
                    <th scope="row"><?php esc_html_e( '오류 메시지', 'jj-style-guide' ); ?></th>
                    <td>
                        <p style="color: #d63638; margin: 0;"><?php echo esc_html( $activation_error ); ?></p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

