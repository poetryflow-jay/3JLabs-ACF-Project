<?php
/**
 * ACF Nudge Flow - Database Manager
 *
 * 데이터베이스 설치, RFM 분석, 외부 데이터 소스 통합을 관리합니다.
 *
 * @package ACF_Nudge_Flow
 * @version 1.0.0
 * @since 22.5.3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ACF_NF_Database_Manager
 */
class ACF_NF_Database_Manager {

    /**
     * 싱글톤 인스턴스
     *
     * @var ACF_NF_Database_Manager|null
     */
    private static $instance = null;

    /**
     * 데이터베이스 버전
     *
     * @var string
     */
    private $db_version = '1.0.0';

    /**
     * 테이블 접두사
     *
     * @var string
     */
    private $prefix;

    /**
     * 싱글톤 인스턴스 반환
     *
     * @return ACF_NF_Database_Manager
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        global $wpdb;
        $this->prefix = $wpdb->prefix . 'nf_';

        // 활성화 시 테이블 설치
        register_activation_hook( ACF_NUDGE_FLOW_PLUGIN_FILE, array( $this, 'install_tables' ) );

        // 주문 완료 시 RFM 업데이트
        add_action( 'woocommerce_order_status_completed', array( $this, 'on_order_complete' ), 10, 1 );
        add_action( 'woocommerce_order_status_processing', array( $this, 'on_order_complete' ), 10, 1 );

        // 일일 RFM 재계산 스케줄
        add_action( 'acf_nf_daily_rfm_calculation', array( $this, 'calculate_all_rfm' ) );
        if ( ! wp_next_scheduled( 'acf_nf_daily_rfm_calculation' ) ) {
            wp_schedule_event( time(), 'daily', 'acf_nf_daily_rfm_calculation' );
        }
    }

    /**
     * 테이블 설치
     *
     * @return bool
     */
    public function install_tables() {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();

        // SQL 파일 경로
        $sql_files = array(
            ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/content/template/NF_Database_Complete.sql',
            ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/content/template/NF_Database_Integrations.sql',
        );

        foreach ( $sql_files as $sql_file ) {
            if ( file_exists( $sql_file ) ) {
                $sql_content = file_get_contents( $sql_file );

                // 플레이스홀더 교체
                $sql_content = str_replace( '{$wpdb->prefix}', $wpdb->prefix, $sql_content );

                // 주석 제거 및 SQL 문 분리
                $sql_statements = $this->parse_sql_statements( $sql_content );

                foreach ( $sql_statements as $statement ) {
                    if ( ! empty( trim( $statement ) ) ) {
                        // CREATE TABLE 문인 경우 dbDelta 사용
                        if ( stripos( $statement, 'CREATE TABLE' ) !== false ) {
                            dbDelta( $statement );
                        } else {
                            // INSERT, DROP 등은 직접 실행
                            $wpdb->query( $statement );
                        }
                    }
                }
            }
        }

        // 버전 저장
        update_option( 'acf_nf_db_version', $this->db_version );

        return true;
    }

    /**
     * SQL 문 파싱
     *
     * @param string $sql_content SQL 내용.
     * @return array SQL 문 배열.
     */
    private function parse_sql_statements( $sql_content ) {
        // 주석 제거
        $sql_content = preg_replace( '/--.*$/m', '', $sql_content );
        $sql_content = preg_replace( '/\/\*.*?\*\//s', '', $sql_content );

        // DELIMITER 처리
        $sql_content = preg_replace( '/DELIMITER\s+\/\/.*?DELIMITER\s+;/s', '', $sql_content );

        // 세미콜론으로 분리
        return array_filter( explode( ';', $sql_content ) );
    }

    /**
     * RFM 세그먼트 목록 조회
     *
     * @param bool $active_only 활성 세그먼트만 조회.
     * @return array
     */
    public function get_rfm_segments( $active_only = true ) {
        global $wpdb;

        $table = $this->prefix . 'rfm_segments';

        $where = $active_only ? 'WHERE is_active = 1' : '';

        $results = $wpdb->get_results(
            "SELECT * FROM {$table} {$where} ORDER BY priority_rank ASC",
            ARRAY_A
        );

        return $results ?: array();
    }

    /**
     * 세그먼트 스토어 조회
     *
     * @param string $category 카테고리 필터.
     * @param string $trigger_type 트리거 타입 필터.
     * @return array
     */
    public function get_segment_store( $category = '', $trigger_type = '' ) {
        global $wpdb;

        $table = $this->prefix . 'segment_store';

        $where = array( 'is_active = 1' );

        if ( ! empty( $category ) ) {
            $where[] = $wpdb->prepare( 'category = %s', $category );
        }

        if ( ! empty( $trigger_type ) ) {
            $where[] = $wpdb->prepare( 'trigger_type = %s', $trigger_type );
        }

        $where_clause = implode( ' AND ', $where );

        $results = $wpdb->get_results(
            "SELECT * FROM {$table} WHERE {$where_clause} ORDER BY sort_order ASC",
            ARRAY_A
        );

        return $results ?: array();
    }

    /**
     * 고객 필터 조회
     *
     * @param string $category 카테고리 필터.
     * @return array
     */
    public function get_customer_filters( $category = '' ) {
        global $wpdb;

        $table = $this->prefix . 'customer_filters';

        $where = array( 'is_active = 1' );

        if ( ! empty( $category ) ) {
            $where[] = $wpdb->prepare( 'category = %s', $category );
        }

        $where_clause = implode( ' AND ', $where );

        $results = $wpdb->get_results(
            "SELECT * FROM {$table} WHERE {$where_clause} ORDER BY sort_order ASC",
            ARRAY_A
        );

        return $results ?: array();
    }

    /**
     * 디자인 템플릿 조회
     *
     * @param string $design_type 디자인 타입 필터.
     * @return array
     */
    public function get_design_templates( $design_type = '' ) {
        global $wpdb;

        $table = $this->prefix . 'design_store';

        $where = array( 'is_active = 1' );

        if ( ! empty( $design_type ) ) {
            $where[] = $wpdb->prepare( 'design_type = %s', $design_type );
        }

        $where_clause = implode( ' AND ', $where );

        $results = $wpdb->get_results(
            "SELECT * FROM {$table} WHERE {$where_clause} ORDER BY sort_order ASC",
            ARRAY_A
        );

        return $results ?: array();
    }

    /**
     * 메시지 템플릿 조회
     *
     * @param string $category 카테고리 필터.
     * @param bool   $premium_only 프리미엄 전용.
     * @return array
     */
    public function get_message_templates( $category = '', $premium_only = false ) {
        global $wpdb;

        $table = $this->prefix . 'message_templates';

        $where = array( 'is_active = 1' );

        if ( ! empty( $category ) ) {
            $where[] = $wpdb->prepare( 'category = %s', $category );
        }

        if ( $premium_only ) {
            $where[] = 'is_premium = 1';
        }

        $where_clause = implode( ' AND ', $where );

        $results = $wpdb->get_results(
            "SELECT * FROM {$table} WHERE {$where_clause} ORDER BY id ASC",
            ARRAY_A
        );

        return $results ?: array();
    }

    /**
     * 개별 고객 RFM 점수 계산
     *
     * @param int $customer_id WooCommerce 고객 ID.
     * @return array|false 계산 결과 또는 false.
     */
    public function calculate_customer_rfm( $customer_id ) {
        global $wpdb;

        if ( ! $customer_id ) {
            return false;
        }

        // RFM 설정 가져오기
        $settings = $this->get_rfm_settings();

        // 주문 데이터 집계
        $order_data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT
                    MAX(DATE(posts.post_date)) as last_purchase_date,
                    COUNT(*) as total_orders,
                    COALESCE(SUM(CAST(meta_total.meta_value AS DECIMAL(12,2))), 0) as total_amount,
                    MIN(DATE(posts.post_date)) as first_purchase_date
                FROM {$wpdb->posts} posts
                JOIN {$wpdb->postmeta} meta_customer
                    ON posts.ID = meta_customer.post_id
                    AND meta_customer.meta_key = '_customer_user'
                    AND meta_customer.meta_value = %d
                LEFT JOIN {$wpdb->postmeta} meta_total
                    ON posts.ID = meta_total.post_id
                    AND meta_total.meta_key = '_order_total'
                WHERE posts.post_type = 'shop_order'
                AND posts.post_status IN ('wc-completed', 'wc-processing')",
                $customer_id
            ),
            ARRAY_A
        );

        if ( ! $order_data || empty( $order_data['last_purchase_date'] ) ) {
            return false;
        }

        // 경과일 계산
        $days_since = (int) ( ( time() - strtotime( $order_data['last_purchase_date'] ) ) / DAY_IN_SECONDS );

        // R 점수 계산
        $r_score = 1;
        if ( $days_since <= $settings['r_score_5'] ) {
            $r_score = 5;
        } elseif ( $days_since <= $settings['r_score_4'] ) {
            $r_score = 4;
        } elseif ( $days_since <= $settings['r_score_3'] ) {
            $r_score = 3;
        } elseif ( $days_since <= $settings['r_score_2'] ) {
            $r_score = 2;
        }

        // F 점수 계산
        $total_orders = (int) $order_data['total_orders'];
        $f_score      = 1;
        if ( $total_orders >= $settings['f_score_5'] ) {
            $f_score = 5;
        } elseif ( $total_orders >= $settings['f_score_4'] ) {
            $f_score = 4;
        } elseif ( $total_orders >= $settings['f_score_3'] ) {
            $f_score = 3;
        } elseif ( $total_orders >= $settings['f_score_2'] ) {
            $f_score = 2;
        }

        // M 점수 계산
        $total_amount = (float) $order_data['total_amount'];
        $m_score      = 1;
        if ( $total_amount >= $settings['m_score_5'] ) {
            $m_score = 5;
        } elseif ( $total_amount >= $settings['m_score_4'] ) {
            $m_score = 4;
        } elseif ( $total_amount >= $settings['m_score_3'] ) {
            $m_score = 3;
        } elseif ( $total_amount >= $settings['m_score_2'] ) {
            $m_score = 2;
        }

        // 세그먼트 매핑
        $segment_id = $this->map_rfm_to_segment( $r_score, $f_score, $m_score );

        // 결과 저장
        $rfm_table = $this->prefix . 'customer_rfm';
        $rfm_data  = array(
            'customer_id'        => $customer_id,
            'last_purchase_date' => $order_data['last_purchase_date'],
            'days_since_purchase' => $days_since,
            'total_orders'       => $total_orders,
            'total_amount'       => $total_amount,
            'avg_order_value'    => $total_orders > 0 ? $total_amount / $total_orders : 0,
            'r_score'            => $r_score,
            'f_score'            => $f_score,
            'm_score'            => $m_score,
            'rfm_score'          => sprintf( '%d-%d-%d', $r_score, $f_score, $m_score ),
            'rfm_total'          => $r_score + $f_score + $m_score,
            'segment_id'         => $segment_id,
            'first_purchase_date' => $order_data['first_purchase_date'],
            'calculated_at'      => current_time( 'mysql' ),
        );

        // UPSERT
        $wpdb->replace( $rfm_table, $rfm_data );

        return $rfm_data;
    }

    /**
     * RFM 점수를 세그먼트에 매핑
     *
     * @param int $r_score R 점수.
     * @param int $f_score F 점수.
     * @param int $m_score M 점수.
     * @return int|null 세그먼트 ID.
     */
    private function map_rfm_to_segment( $r_score, $f_score, $m_score ) {
        global $wpdb;

        $table = $this->prefix . 'rfm_mapping_rules';

        // M 레벨 결정
        $m_level = 'any';
        if ( $m_score >= 4 ) {
            $m_level = 'high';
        } elseif ( $m_score >= 2 ) {
            $m_level = 'medium';
        } else {
            $m_level = 'low';
        }

        $segment_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT segment_id FROM {$table}
                WHERE is_active = 1
                AND %d BETWEEN r_min AND r_max
                AND %d BETWEEN f_min AND f_max
                AND (m_condition = 'any' OR m_condition = %s)
                ORDER BY priority ASC
                LIMIT 1",
                $r_score,
                $f_score,
                $m_level
            )
        );

        return $segment_id ? (int) $segment_id : null;
    }

    /**
     * RFM 설정 조회
     *
     * @return array
     */
    public function get_rfm_settings() {
        global $wpdb;

        $table = $this->prefix . 'rfm_settings';

        $results = $wpdb->get_results(
            "SELECT setting_key, setting_value FROM {$table}",
            ARRAY_A
        );

        $settings = array();
        foreach ( $results as $row ) {
            $settings[ $row['setting_key'] ] = (int) $row['setting_value'];
        }

        // 기본값 설정
        return wp_parse_args(
            $settings,
            array(
                'r_score_5' => 7,
                'r_score_4' => 30,
                'r_score_3' => 60,
                'r_score_2' => 90,
                'r_score_1' => 999,
                'f_score_5' => 10,
                'f_score_4' => 6,
                'f_score_3' => 3,
                'f_score_2' => 2,
                'f_score_1' => 1,
                'm_score_5' => 500000,
                'm_score_4' => 300000,
                'm_score_3' => 150000,
                'm_score_2' => 50000,
                'm_score_1' => 0,
            )
        );
    }

    /**
     * 전체 고객 RFM 계산
     *
     * @return array 결과 통계.
     */
    public function calculate_all_rfm() {
        global $wpdb;

        // 모든 고객 ID 가져오기
        $customer_ids = $wpdb->get_col(
            "SELECT DISTINCT meta_value
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_customer_user'
            AND meta_value > 0"
        );

        $success_count = 0;
        $error_count   = 0;

        foreach ( $customer_ids as $customer_id ) {
            $result = $this->calculate_customer_rfm( (int) $customer_id );
            if ( $result ) {
                $success_count++;
            } else {
                $error_count++;
            }
        }

        return array(
            'total'   => count( $customer_ids ),
            'success' => $success_count,
            'error'   => $error_count,
            'date'    => current_time( 'mysql' ),
        );
    }

    /**
     * 주문 완료 시 콜백
     *
     * @param int $order_id 주문 ID.
     */
    public function on_order_complete( $order_id ) {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

        $customer_id = $order->get_customer_id();

        if ( $customer_id ) {
            $this->calculate_customer_rfm( $customer_id );
        }
    }

    /**
     * 고객 RFM 정보 조회
     *
     * @param int $customer_id 고객 ID.
     * @return array|null
     */
    public function get_customer_rfm( $customer_id ) {
        global $wpdb;

        $table = $this->prefix . 'customer_rfm';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT c.*, s.segment_name, s.segment_name_en, s.marketing_action, s.color_code, s.icon
                FROM {$table} c
                LEFT JOIN {$this->prefix}rfm_segments s ON c.segment_id = s.id
                WHERE c.customer_id = %d",
                $customer_id
            ),
            ARRAY_A
        );
    }

    /**
     * 세그먼트별 통계 조회
     *
     * @return array
     */
    public function get_segment_stats() {
        global $wpdb;

        $rfm_table     = $this->prefix . 'customer_rfm';
        $segment_table = $this->prefix . 'rfm_segments';

        return $wpdb->get_results(
            "SELECT
                s.id,
                s.segment_key,
                s.segment_name,
                s.segment_name_en,
                s.color_code,
                s.priority_rank,
                COUNT(c.id) as customer_count,
                COALESCE(SUM(c.total_amount), 0) as total_revenue,
                COALESCE(AVG(c.total_amount), 0) as avg_customer_value,
                COALESCE(AVG(c.total_orders), 0) as avg_orders
            FROM {$segment_table} s
            LEFT JOIN {$rfm_table} c ON s.id = c.segment_id
            GROUP BY s.id
            ORDER BY s.priority_rank ASC",
            ARRAY_A
        );
    }

    /**
     * 활성 데이터 소스 조회
     *
     * @return array
     */
    public function get_active_data_sources() {
        global $wpdb;

        $table = $this->prefix . 'data_sources';

        return $wpdb->get_results(
            "SELECT * FROM {$table} WHERE is_enabled = 1 ORDER BY source_type, source_name",
            ARRAY_A
        );
    }

    /**
     * 데이터 소스 감지 및 활성화
     *
     * @return array 감지된 플러그인 목록.
     */
    public function detect_data_sources() {
        global $wpdb;

        $table = $this->prefix . 'data_sources';

        // 모든 데이터 소스 가져오기
        $sources = $wpdb->get_results(
            "SELECT id, source_key, plugin_slug FROM {$table} WHERE plugin_slug IS NOT NULL",
            ARRAY_A
        );

        $detected = array();

        foreach ( $sources as $source ) {
            $is_active = is_plugin_active( $source['plugin_slug'] );

            $wpdb->update(
                $table,
                array(
                    'is_detected' => $is_active ? 1 : 0,
                    'updated_at'  => current_time( 'mysql' ),
                ),
                array( 'id' => $source['id'] )
            );

            if ( $is_active ) {
                $detected[] = $source['source_key'];
            }
        }

        return $detected;
    }

    /**
     * 통합 고객 프로필 가져오기 또는 생성
     *
     * @param int    $user_id WordPress 사용자 ID.
     * @param string $email   이메일 (비회원용).
     * @param string $visitor_id 방문자 ID.
     * @return int 프로필 ID.
     */
    public function get_or_create_unified_profile( $user_id = null, $email = null, $visitor_id = null ) {
        global $wpdb;

        $table = $this->prefix . 'unified_customer_profile';

        // 기존 프로필 검색
        $profile_id = null;

        if ( $user_id ) {
            $profile_id = $wpdb->get_var(
                $wpdb->prepare( "SELECT id FROM {$table} WHERE wp_user_id = %d", $user_id )
            );
        } elseif ( $email ) {
            $profile_id = $wpdb->get_var(
                $wpdb->prepare( "SELECT id FROM {$table} WHERE email = %s", $email )
            );
        } elseif ( $visitor_id ) {
            $profile_id = $wpdb->get_var(
                $wpdb->prepare( "SELECT id FROM {$table} WHERE visitor_id = %s", $visitor_id )
            );
        }

        if ( $profile_id ) {
            return (int) $profile_id;
        }

        // 새 프로필 생성
        $wpdb->insert(
            $table,
            array(
                'wp_user_id' => $user_id,
                'email'      => $email,
                'visitor_id' => $visitor_id,
                'created_at' => current_time( 'mysql' ),
            )
        );

        return $wpdb->insert_id;
    }

    /**
     * 방문자에게 세그먼트 조건 매칭
     *
     * @param array $visitor_data 방문자 데이터.
     * @return array 매칭된 세그먼트 목록.
     */
    public function match_visitor_segments( $visitor_data ) {
        $matched_segments = array();

        // 세그먼트 스토어에서 활성 세그먼트 가져오기
        $segments = $this->get_segment_store();

        foreach ( $segments as $segment ) {
            if ( $this->evaluate_segment_condition( $segment, $visitor_data ) ) {
                $matched_segments[] = $segment;
            }
        }

        return $matched_segments;
    }

    /**
     * 세그먼트 조건 평가
     *
     * @param array $segment 세그먼트 데이터.
     * @param array $visitor_data 방문자 데이터.
     * @return bool
     */
    private function evaluate_segment_condition( $segment, $visitor_data ) {
        $parameters = json_decode( $segment['parameters'], true );

        // 파라미터가 없는 단순 조건
        if ( empty( $parameters ) ) {
            switch ( $segment['id'] ) {
                case 1: // 첫 방문자
                    return isset( $visitor_data['visit_count'] ) && $visitor_data['visit_count'] <= 1;
                case 2: // 재방문자
                    return isset( $visitor_data['visit_count'] ) && $visitor_data['visit_count'] > 1;
                case 16: // PC 방문자
                    return isset( $visitor_data['device'] ) && 'desktop' === $visitor_data['device'];
                case 17: // 모바일 방문자
                    return isset( $visitor_data['device'] ) && 'mobile' === $visitor_data['device'];
                case 36: // 로그인 회원
                    return is_user_logged_in();
                case 47: // 1회 구매자
                    return isset( $visitor_data['purchase_count'] ) && 1 === $visitor_data['purchase_count'];
                case 48: // 재구매자
                    return isset( $visitor_data['purchase_count'] ) && $visitor_data['purchase_count'] >= 2;
                case 49: // 비구매자
                    return ! isset( $visitor_data['purchase_count'] ) || 0 === $visitor_data['purchase_count'];
            }
        }

        // 파라미터 기반 조건 평가
        foreach ( $parameters as $param_key ) {
            if ( ! isset( $visitor_data[ $param_key ] ) ) {
                return false;
            }

            // 여기에 각 파라미터 타입별 조건 평가 로직 추가
            // 실제 구현에서는 segment['parameter_types']의 정의에 따라 비교 수행
        }

        return true;
    }
}

// 전역 함수로 인스턴스 접근
function acf_nf_database() {
    return ACF_NF_Database_Manager::instance();
}
