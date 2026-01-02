<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.4] Batch Processor
 * 
 * 배치 처리 및 자동화 시스템
 * - 일괄 스타일 적용
 * - 스케줄링 기능
 * 
 * @since 9.4.0
 */
class JJ_Batch_Processor {

    private static $instance = null;
    private $option_key = 'jj_batch_jobs';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_create_batch_job', array( $this, 'ajax_create_batch_job' ) );
        add_action( 'wp_ajax_jj_get_batch_jobs', array( $this, 'ajax_get_batch_jobs' ) );
        add_action( 'wp_ajax_jj_cancel_batch_job', array( $this, 'ajax_cancel_batch_job' ) );
        
        // 스케줄된 작업 실행
        add_action( 'jj_process_scheduled_batch', array( $this, 'process_scheduled_batch' ), 10, 1 );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'jj-admin-center' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'jj-batch-processor',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-batch-processor.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '9.4.0',
            true
        );

        wp_localize_script(
            'jj-batch-processor',
            'jjBatchProcessor',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_batch_processor_action' ),
                'strings'  => array(
                    'processing' => __( '처리 중...', 'acf-css-really-simple-style-management-center' ),
                    'completed' => __( '완료되었습니다.', 'acf-css-really-simple-style-management-center' ),
                ),
            )
        );
    }

    /**
     * 배치 작업 생성
     */
    public function create_batch_job( $job_data ) {
        $job = array(
            'id'          => $this->generate_job_id(),
            'type'        => $job_data['type'] ?? 'apply_styles',
            'targets'     => $job_data['targets'] ?? array(),
            'styles'      => $job_data['styles'] ?? array(),
            'scheduled_at' => $job_data['scheduled_at'] ?? null,
            'status'      => $job_data['scheduled_at'] ? 'scheduled' : 'pending',
            'created_at'  => current_time( 'mysql' ),
            'created_by'  => get_current_user_id(),
            'progress'    => 0,
        );

        $jobs = $this->get_batch_jobs();
        $jobs[] = $job;
        update_option( $this->option_key, $jobs );

        // 스케줄된 작업이면 Cron 등록
        if ( $job['scheduled_at'] ) {
            $this->schedule_job( $job );
        } else {
            // 즉시 실행
            $this->process_job( $job['id'] );
        }

        return $job;
    }

    /**
     * 배치 작업 처리
     */
    public function process_job( $job_id ) {
        $jobs = $this->get_batch_jobs();
        $job = null;
        $job_index = null;

        foreach ( $jobs as $index => $j ) {
            if ( $j['id'] === $job_id ) {
                $job = $j;
                $job_index = $index;
                break;
            }
        }

        if ( ! $job ) {
            return false;
        }

        // 작업 상태 업데이트
        $jobs[ $job_index ]['status'] = 'processing';
        $jobs[ $job_index ]['started_at'] = current_time( 'mysql' );
        update_option( $this->option_key, $jobs );

        // 작업 타입에 따른 처리
        $result = false;
        switch ( $job['type'] ) {
            case 'apply_styles':
                $result = $this->apply_styles_batch( $job );
                break;
            case 'update_colors':
                $result = $this->update_colors_batch( $job );
                break;
            case 'update_fonts':
                $result = $this->update_fonts_batch( $job );
                break;
        }

        // 작업 완료 처리
        $jobs[ $job_index ]['status'] = $result ? 'completed' : 'failed';
        $jobs[ $job_index ]['completed_at'] = current_time( 'mysql' );
        $jobs[ $job_index ]['progress'] = 100;
        update_option( $this->option_key, $jobs );

        return $result;
    }

    /**
     * 스타일 일괄 적용
     */
    private function apply_styles_batch( $job ) {
        $options = get_option( 'jj_style_guide_options', array() );
        
        if ( ! empty( $job['styles'] ) ) {
            $options = array_merge_recursive( $options, $job['styles'] );
            update_option( 'jj_style_guide_options', $options );
        }

        return true;
    }

    /**
     * 색상 일괄 업데이트
     */
    private function update_colors_batch( $job ) {
        $options = get_option( 'jj_style_guide_options', array() );
        
        if ( isset( $job['styles']['palettes'] ) ) {
            if ( ! isset( $options['palettes'] ) ) {
                $options['palettes'] = array();
            }
            $options['palettes'] = array_merge( $options['palettes'], $job['styles']['palettes'] );
            update_option( 'jj_style_guide_options', $options );
        }

        return true;
    }

    /**
     * 폰트 일괄 업데이트
     */
    private function update_fonts_batch( $job ) {
        $options = get_option( 'jj_style_guide_options', array() );
        
        if ( isset( $job['styles']['typography'] ) ) {
            if ( ! isset( $options['typography'] ) ) {
                $options['typography'] = array();
            }
            $options['typography'] = array_merge( $options['typography'], $job['styles']['typography'] );
            update_option( 'jj_style_guide_options', $options );
        }

        return true;
    }

    /**
     * 배치 작업 목록 가져오기
     */
    public function get_batch_jobs( $status = null ) {
        $jobs = get_option( $this->option_key, array() );
        
        if ( $status ) {
            $jobs = array_filter( $jobs, function( $job ) use ( $status ) {
                return $job['status'] === $status;
            } );
        }

        // 최신순 정렬
        usort( $jobs, function( $a, $b ) {
            return strtotime( $b['created_at'] ) - strtotime( $a['created_at'] );
        } );

        return $jobs;
    }

    /**
     * 작업 스케줄링
     */
    private function schedule_job( $job ) {
        $timestamp = strtotime( $job['scheduled_at'] );
        
        if ( $timestamp && $timestamp > time() ) {
            wp_schedule_single_event( $timestamp, 'jj_process_scheduled_batch', array( $job['id'] ) );
        }
    }

    /**
     * 스케줄된 작업 처리
     */
    public function process_scheduled_batch( $job_id ) {
        $this->process_job( $job_id );
    }

    /**
     * 작업 ID 생성
     */
    private function generate_job_id() {
        return 'batch_' . time() . '_' . wp_generate_password( 8, false );
    }

    /**
     * AJAX: 배치 작업 생성
     */
    public function ajax_create_batch_job() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_batch_processor_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $job_data = array(
            'type' => isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'apply_styles',
            'targets' => isset( $_POST['targets'] ) ? json_decode( stripslashes( $_POST['targets'] ), true ) : array(),
            'styles' => isset( $_POST['styles'] ) ? json_decode( stripslashes( $_POST['styles'] ), true ) : array(),
            'scheduled_at' => isset( $_POST['scheduled_at'] ) ? sanitize_text_field( $_POST['scheduled_at'] ) : null,
        );

        $job = $this->create_batch_job( $job_data );

        wp_send_json_success( array(
            'job' => $job,
            'message' => __( '배치 작업이 생성되었습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }

    /**
     * AJAX: 배치 작업 목록
     */
    public function ajax_get_batch_jobs() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_batch_processor_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : null;
        $jobs = $this->get_batch_jobs( $status );

        wp_send_json_success( array(
            'jobs' => $jobs,
        ) );
    }

    /**
     * AJAX: 배치 작업 취소
     */
    public function ajax_cancel_batch_job() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_batch_processor_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $job_id = isset( $_POST['job_id'] ) ? sanitize_text_field( $_POST['job_id'] ) : '';
        
        if ( ! $job_id ) {
            wp_send_json_error( array( 'message' => __( '작업 ID가 필요합니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $jobs = $this->get_batch_jobs();
        foreach ( $jobs as $index => $job ) {
            if ( $job['id'] === $job_id ) {
                $jobs[ $index ]['status'] = 'cancelled';
                $jobs[ $index ]['cancelled_at'] = current_time( 'mysql' );
                
                // 스케줄된 작업이면 Cron 취소
                if ( $job['scheduled_at'] ) {
                    wp_unschedule_event( strtotime( $job['scheduled_at'] ), 'jj_process_scheduled_batch', array( $job_id ) );
                }
                
                break;
            }
        }

        update_option( $this->option_key, $jobs );

        wp_send_json_success( array(
            'message' => __( '작업이 취소되었습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }
}
