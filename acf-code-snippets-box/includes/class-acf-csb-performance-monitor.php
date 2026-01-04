<?php
/**
 * ACF Code Snippets Box - Performance Monitor
 *
 * 코드 실행 성능 모니터링 및 통계
 *
 * @package ACF_Code_Snippets_Box
 * @since 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Performance Monitor 클래스
 */
class ACF_CSB_Performance_Monitor {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 실행 시간 기록
     */
    private $execution_times = array();

    /**
     * 시작 시간
     */
    private $start_times = array();

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
        add_action( 'wp_ajax_acf_csb_get_performance_stats', array( $this, 'ajax_get_stats' ) );
        add_action( 'wp_ajax_acf_csb_reset_performance_stats', array( $this, 'ajax_reset_stats' ) );
        add_action( 'shutdown', array( $this, 'save_execution_stats' ) );

        // 대시보드 위젯
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
    }

    /**
     * 실행 시작 기록
     */
    public function start_timing( $snippet_id ) {
        $this->start_times[ $snippet_id ] = microtime( true );
    }

    /**
     * 실행 종료 기록
     */
    public function end_timing( $snippet_id ) {
        if ( isset( $this->start_times[ $snippet_id ] ) ) {
            $execution_time = ( microtime( true ) - $this->start_times[ $snippet_id ] ) * 1000; // ms
            $this->execution_times[ $snippet_id ] = $execution_time;
            unset( $this->start_times[ $snippet_id ] );
            return $execution_time;
        }
        return 0;
    }

    /**
     * 실행 통계 저장
     */
    public function save_execution_stats() {
        if ( empty( $this->execution_times ) ) {
            return;
        }

        $stats = get_option( 'acf_csb_performance_stats', array() );
        $today = date( 'Y-m-d' );

        if ( ! isset( $stats[ $today ] ) ) {
            $stats[ $today ] = array();
        }

        foreach ( $this->execution_times as $snippet_id => $time ) {
            if ( ! isset( $stats[ $today ][ $snippet_id ] ) ) {
                $stats[ $today ][ $snippet_id ] = array(
                    'executions' => 0,
                    'total_time' => 0,
                    'min_time'   => $time,
                    'max_time'   => $time,
                    'avg_time'   => $time,
                );
            }

            $current = &$stats[ $today ][ $snippet_id ];
            $current['executions']++;
            $current['total_time'] += $time;
            $current['min_time'] = min( $current['min_time'], $time );
            $current['max_time'] = max( $current['max_time'], $time );
            $current['avg_time'] = $current['total_time'] / $current['executions'];
        }

        // 30일 이상 된 데이터 정리
        $cutoff = date( 'Y-m-d', strtotime( '-30 days' ) );
        foreach ( array_keys( $stats ) as $date ) {
            if ( $date < $cutoff ) {
                unset( $stats[ $date ] );
            }
        }

        update_option( 'acf_csb_performance_stats', $stats );
    }

    /**
     * 통계 가져오기
     */
    public function get_stats( $days = 7 ) {
        $stats = get_option( 'acf_csb_performance_stats', array() );
        $result = array();

        $start_date = date( 'Y-m-d', strtotime( "-{$days} days" ) );

        foreach ( $stats as $date => $day_stats ) {
            if ( $date >= $start_date ) {
                $result[ $date ] = $day_stats;
            }
        }

        return $result;
    }

    /**
     * 스니펫별 통계 가져오기
     */
    public function get_snippet_stats( $snippet_id, $days = 7 ) {
        $stats = $this->get_stats( $days );
        $result = array(
            'total_executions' => 0,
            'total_time'       => 0,
            'avg_time'         => 0,
            'min_time'         => PHP_FLOAT_MAX,
            'max_time'         => 0,
            'daily_data'       => array(),
        );

        foreach ( $stats as $date => $day_stats ) {
            if ( isset( $day_stats[ $snippet_id ] ) ) {
                $s = $day_stats[ $snippet_id ];
                $result['total_executions'] += $s['executions'];
                $result['total_time'] += $s['total_time'];
                $result['min_time'] = min( $result['min_time'], $s['min_time'] );
                $result['max_time'] = max( $result['max_time'], $s['max_time'] );
                $result['daily_data'][ $date ] = $s;
            }
        }

        if ( $result['total_executions'] > 0 ) {
            $result['avg_time'] = $result['total_time'] / $result['total_executions'];
        }

        if ( $result['min_time'] === PHP_FLOAT_MAX ) {
            $result['min_time'] = 0;
        }

        return $result;
    }

    /**
     * 전체 요약 통계
     */
    public function get_summary_stats( $days = 7 ) {
        $stats = $this->get_stats( $days );
        $summary = array(
            'total_executions'  => 0,
            'total_snippets'    => 0,
            'total_time'        => 0,
            'avg_time'          => 0,
            'slowest_snippets'  => array(),
            'most_used'         => array(),
            'daily_executions'  => array(),
        );

        $snippet_totals = array();

        foreach ( $stats as $date => $day_stats ) {
            $day_total = 0;
            foreach ( $day_stats as $snippet_id => $s ) {
                $summary['total_executions'] += $s['executions'];
                $summary['total_time'] += $s['total_time'];
                $day_total += $s['executions'];

                if ( ! isset( $snippet_totals[ $snippet_id ] ) ) {
                    $snippet_totals[ $snippet_id ] = array(
                        'executions' => 0,
                        'total_time' => 0,
                        'avg_time'   => 0,
                    );
                }
                $snippet_totals[ $snippet_id ]['executions'] += $s['executions'];
                $snippet_totals[ $snippet_id ]['total_time'] += $s['total_time'];
            }
            $summary['daily_executions'][ $date ] = $day_total;
        }

        $summary['total_snippets'] = count( $snippet_totals );

        if ( $summary['total_executions'] > 0 ) {
            $summary['avg_time'] = $summary['total_time'] / $summary['total_executions'];
        }

        // 평균 실행 시간 계산
        foreach ( $snippet_totals as $id => &$data ) {
            if ( $data['executions'] > 0 ) {
                $data['avg_time'] = $data['total_time'] / $data['executions'];
            }
        }

        // 가장 느린 스니펫 (상위 5개)
        uasort( $snippet_totals, function( $a, $b ) {
            return $b['avg_time'] <=> $a['avg_time'];
        });
        $summary['slowest_snippets'] = array_slice( $snippet_totals, 0, 5, true );

        // 가장 많이 사용된 스니펫 (상위 5개)
        uasort( $snippet_totals, function( $a, $b ) {
            return $b['executions'] <=> $a['executions'];
        });
        $summary['most_used'] = array_slice( $snippet_totals, 0, 5, true );

        return $summary;
    }

    /**
     * AJAX: 통계 가져오기
     */
    public function ajax_get_stats() {
        check_ajax_referer( 'acf_csb_performance_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $days = isset( $_POST['days'] ) ? intval( $_POST['days'] ) : 7;
        $snippet_id = isset( $_POST['snippet_id'] ) ? intval( $_POST['snippet_id'] ) : 0;

        if ( $snippet_id > 0 ) {
            $stats = $this->get_snippet_stats( $snippet_id, $days );
        } else {
            $stats = $this->get_summary_stats( $days );
        }

        // 스니펫 제목 추가
        if ( isset( $stats['slowest_snippets'] ) ) {
            foreach ( $stats['slowest_snippets'] as $id => &$data ) {
                $data['title'] = get_the_title( $id );
            }
        }
        if ( isset( $stats['most_used'] ) ) {
            foreach ( $stats['most_used'] as $id => &$data ) {
                $data['title'] = get_the_title( $id );
            }
        }

        wp_send_json_success( $stats );
    }

    /**
     * AJAX: 통계 초기화
     */
    public function ajax_reset_stats() {
        check_ajax_referer( 'acf_csb_performance_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        delete_option( 'acf_csb_performance_stats' );
        wp_send_json_success( '통계가 초기화되었습니다.' );
    }

    /**
     * 대시보드 위젯 추가
     */
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'acf_csb_performance_widget',
            '📦 Code Snippets 성능 모니터',
            array( $this, 'render_dashboard_widget' )
        );
    }

    /**
     * 대시보드 위젯 렌더링
     */
    public function render_dashboard_widget() {
        $summary = $this->get_summary_stats( 7 );
        ?>
        <div class="acf-csb-perf-widget">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px;">
                <div style="text-align: center; padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; color: #fff;">
                    <div style="font-size: 28px; font-weight: 700;"><?php echo number_format( $summary['total_executions'] ); ?></div>
                    <div style="font-size: 12px; opacity: 0.9;">총 실행 횟수</div>
                </div>
                <div style="text-align: center; padding: 15px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 8px; color: #fff;">
                    <div style="font-size: 28px; font-weight: 700;"><?php echo number_format( $summary['avg_time'], 2 ); ?></div>
                    <div style="font-size: 12px; opacity: 0.9;">평균 실행 시간 (ms)</div>
                </div>
                <div style="text-align: center; padding: 15px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 8px; color: #fff;">
                    <div style="font-size: 28px; font-weight: 700;"><?php echo $summary['total_snippets']; ?></div>
                    <div style="font-size: 12px; opacity: 0.9;">활성 스니펫</div>
                </div>
            </div>

            <?php if ( ! empty( $summary['slowest_snippets'] ) ) : ?>
                <h4 style="margin: 15px 0 10px; color: #1d2327;">⚠️ 주의가 필요한 스니펫</h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <?php foreach ( $summary['slowest_snippets'] as $id => $data ) :
                        if ( $data['avg_time'] < 5 ) continue; // 5ms 이하는 무시
                    ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 8px 0;">
                                <a href="<?php echo esc_url( admin_url( "post.php?post={$id}&action=edit" ) ); ?>">
                                    <?php echo esc_html( get_the_title( $id ) ); ?>
                                </a>
                            </td>
                            <td style="padding: 8px 0; text-align: right; color: <?php echo $data['avg_time'] > 50 ? '#dc3545' : '#ffc107'; ?>; font-weight: 600;">
                                <?php echo number_format( $data['avg_time'], 2 ); ?>ms
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>

            <p style="margin-top: 15px; text-align: center;">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-code-snippets&tab=performance' ) ); ?>" class="button button-primary">
                    상세 통계 보기
                </a>
            </p>
        </div>
        <?php
    }

    /**
     * 성능 경고 확인
     */
    public function check_performance_warnings( $snippet_id ) {
        $stats = $this->get_snippet_stats( $snippet_id, 7 );
        $warnings = array();

        // 평균 실행 시간 경고 (100ms 이상)
        if ( $stats['avg_time'] > 100 ) {
            $warnings[] = array(
                'type'    => 'critical',
                'message' => sprintf(
                    __( '이 스니펫의 평균 실행 시간이 %.2fms로 매우 느립니다. 최적화가 필요합니다.', 'acf-code-snippets-box' ),
                    $stats['avg_time']
                ),
            );
        } elseif ( $stats['avg_time'] > 50 ) {
            $warnings[] = array(
                'type'    => 'warning',
                'message' => sprintf(
                    __( '이 스니펫의 평균 실행 시간이 %.2fms입니다. 개선을 고려해보세요.', 'acf-code-snippets-box' ),
                    $stats['avg_time']
                ),
            );
        }

        // 최대 실행 시간 급증 경고
        if ( $stats['max_time'] > $stats['avg_time'] * 5 && $stats['max_time'] > 100 ) {
            $warnings[] = array(
                'type'    => 'warning',
                'message' => sprintf(
                    __( '일부 실행에서 성능 저하가 감지되었습니다 (최대 %.2fms).', 'acf-code-snippets-box' ),
                    $stats['max_time']
                ),
            );
        }

        return $warnings;
    }
}
