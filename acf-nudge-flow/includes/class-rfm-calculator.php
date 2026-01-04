<?php
/**
 * RFM 세그먼트 계산기
 * 
 * Recency (최근성): 마지막 구매일로부터 경과일
 * Frequency (빈도): 구매 횟수
 * Monetary (금액): 총 구매액
 * 
 * @package ACF_Nudge_Flow
 * @since 22.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Nudge_Flow_RFM_Calculator {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * RFM 점수 계산 기준 (기본값)
     */
    private $scoring_criteria = array(
        'recency' => array(
            5 => array( 'max' => 29, 'min' => 0 ),      // 1개월 이내
            4 => array( 'max' => 89, 'min' => 30 ),    // 1-3개월
            3 => array( 'max' => 179, 'min' => 90 ),   // 3-6개월
            2 => array( 'max' => 364, 'min' => 180 ),   // 6개월-1년
            1 => array( 'max' => null, 'min' => 365 ),  // 1년 이상
        ),
        'frequency' => array(
            5 => array( 'min' => 13, 'max' => null ),  // 13회 이상
            4 => array( 'min' => 7, 'max' => 12 ),      // 7-12회
            3 => array( 'min' => 4, 'max' => 6 ),       // 4-6회
            2 => array( 'min' => 2, 'max' => 3 ),       // 2-3회
            1 => array( 'min' => 0, 'max' => 1 ),       // 0-1회
        ),
        'monetary' => array(
            5 => array( 'min' => 1000000, 'max' => null ),  // 100만원 이상
            4 => array( 'min' => 500000, 'max' => 999999 ),  // 50-100만원
            3 => array( 'min' => 200000, 'max' => 499999 ),  // 20-50만원
            2 => array( 'min' => 50000, 'max' => 199999 ),   // 5-20만원
            1 => array( 'min' => 0, 'max' => 49999 ),        // 5만원 미만
        ),
    );

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
     * 데이터 소스 통합 인스턴스
     */
    private $data_source;

    /**
     * 생성자
     */
    private function __construct() {
        $this->load_scoring_criteria();
        
        // 데이터 소스 통합 클래스 로드
        if ( class_exists( 'ACF_Nudge_Flow_Data_Source_Integration' ) ) {
            $this->data_source = ACF_Nudge_Flow_Data_Source_Integration::instance();
        }
    }

    /**
     * 데이터베이스에서 RFM 점수 계산 기준 로드
     */
    private function load_scoring_criteria() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'acf_nudge_rfm_scoring';
        
        // 테이블이 존재하는지 확인
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
            return; // 테이블이 없으면 기본값 사용
        }

        $results = $wpdb->get_results( 
            "SELECT score, metric_type, recency_days_max, recency_days_min, 
                    frequency_count_min, frequency_count_max,
                    monetary_amount_min, monetary_amount_max
             FROM $table_name
             ORDER BY score DESC"
        );

        if ( ! empty( $results ) ) {
            $this->scoring_criteria = array(
                'recency' => array(),
                'frequency' => array(),
                'monetary' => array(),
            );

            foreach ( $results as $row ) {
                // Recency 점수
                if ( $row->metric_type === 'recency' ) {
                    $this->scoring_criteria['recency'][ $row->score ] = array(
                        'max' => $row->recency_days_max,
                        'min' => $row->recency_days_min,
                    );
                }

                // Frequency 점수
                if ( $row->metric_type === 'frequency' ) {
                    $this->scoring_criteria['frequency'][ $row->score ] = array(
                        'min' => $row->frequency_count_min,
                        'max' => $row->frequency_count_max,
                    );
                }

                // Monetary 점수
                if ( $row->metric_type === 'monetary' ) {
                    $this->scoring_criteria['monetary'][ $row->score ] = array(
                        'min' => floatval( $row->monetary_amount_min ),
                        'max' => $row->monetary_amount_max ? floatval( $row->monetary_amount_max ) : null,
                    );
                }
            }
        }
    }

    /**
     * 사용자의 RFM 점수 계산
     * 
     * @param int $user_id 사용자 ID
     * @return array RFM 점수 배열 ['r' => 1-5, 'f' => 1-5, 'm' => 1-5]
     */
    public function calculate_rfm_scores( $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( ! $user_id ) {
            return array( 'r' => 1, 'f' => 1, 'm' => 1 );
        }

        // WooCommerce가 활성화되어 있는지 확인
        if ( ! class_exists( 'WooCommerce' ) ) {
            return array( 'r' => 1, 'f' => 1, 'm' => 1 );
        }

        // Recency 점수 계산
        $r_score = $this->calculate_recency_score( $user_id );

        // Frequency 점수 계산
        $f_score = $this->calculate_frequency_score( $user_id );

        // Monetary 점수 계산
        $m_score = $this->calculate_monetary_score( $user_id );

        return array(
            'r' => $r_score,
            'f' => $f_score,
            'm' => $m_score,
        );
    }

    /**
     * Recency 점수 계산 (마지막 구매일로부터 경과일)
     * 
     * [v22.5.2] 통합 데이터 소스 지원
     * 
     * @param int $user_id 사용자 ID
     * @param array $purchase_data 통합 구매 데이터 (선택)
     * @return int 점수 (1-5)
     */
    private function calculate_recency_score( $user_id, $purchase_data = array() ) {
        $days_since_last_order = null;

        // 통합 데이터에서 마지막 주문일 조회
        if ( ! empty( $purchase_data['days_since_last_order'] ) ) {
            $days_since_last_order = intval( $purchase_data['days_since_last_order'] );
        } elseif ( ! empty( $purchase_data['last_order_date'] ) ) {
            $last_order_timestamp = strtotime( $purchase_data['last_order_date'] );
            $days_since_last_order = floor( ( time() - $last_order_timestamp ) / DAY_IN_SECONDS );
        } else {
            // 직접 데이터베이스 조회 (WooCommerce)
            global $wpdb;

            $last_order_date = $wpdb->get_var( $wpdb->prepare(
                "SELECT MAX(p.post_date)
                 FROM {$wpdb->posts} p
                 INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                 WHERE p.post_type = 'shop_order'
                 AND p.post_status IN ('wc-completed', 'wc-processing')
                 AND pm.meta_key = '_customer_user'
                 AND pm.meta_value = %d",
                $user_id
            ) );

            if ( $last_order_date ) {
                $last_order_timestamp = strtotime( $last_order_date );
                $days_since_last_order = floor( ( time() - $last_order_timestamp ) / DAY_IN_SECONDS );
            }
        }

        if ( is_null( $days_since_last_order ) ) {
            return 1; // 구매 이력이 없으면 최저 점수
        }

        // 점수 계산
        foreach ( $this->scoring_criteria['recency'] as $score => $criteria ) {
            $min_match = is_null( $criteria['min'] ) || $days_since_last_order >= $criteria['min'];
            $max_match = is_null( $criteria['max'] ) || $days_since_last_order <= $criteria['max'];

            if ( $min_match && $max_match ) {
                return $score;
            }
        }

        return 1; // 기본값
    }

    /**
     * Frequency 점수 계산 (구매 횟수)
     * 
     * [v22.5.2] 통합 데이터 소스 지원 (WooCommerce, RunDash 등)
     * 
     * @param int $user_id 사용자 ID
     * @param array $purchase_data 통합 구매 데이터 (선택)
     * @return int 점수 (1-5)
     */
    private function calculate_frequency_score( $user_id, $purchase_data = array() ) {
        $purchase_count = 0;

        // 통합 데이터에서 구매 횟수 조회
        if ( ! empty( $purchase_data['total_orders'] ) ) {
            $purchase_count = intval( $purchase_data['total_orders'] );
        } else {
            // 직접 데이터베이스 조회 (WooCommerce)
            global $wpdb;

            $purchase_count = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT p.ID)
                 FROM {$wpdb->posts} p
                 INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                 WHERE p.post_type = 'shop_order'
                 AND p.post_status IN ('wc-completed', 'wc-processing')
                 AND pm.meta_key = '_customer_user'
                 AND pm.meta_value = %d",
                $user_id
            ) );
        }

        // RunDash 데이터가 있으면 런 횟수도 고려 (활동성 지표)
        if ( ! empty( $purchase_data['rundash']['total_runs'] ) ) {
            // 런 횟수를 구매 빈도에 반영 (가중치 적용)
            $run_count = intval( $purchase_data['rundash']['total_runs'] );
            // 런 10회 = 구매 1회로 환산 (가중치 조정 가능)
            $purchase_count += floor( $run_count / 10 );
        }

        // 점수 계산
        foreach ( $this->scoring_criteria['frequency'] as $score => $criteria ) {
            $min_match = is_null( $criteria['min'] ) || $purchase_count >= $criteria['min'];
            $max_match = is_null( $criteria['max'] ) || $purchase_count <= $criteria['max'];

            if ( $min_match && $max_match ) {
                return $score;
            }
        }

        return 1; // 기본값
    }

    /**
     * Monetary 점수 계산 (총 구매액)
     * 
     * [v22.5.2] 통합 데이터 소스 지원 (WooCommerce, GamiPress 포인트 등)
     * 
     * @param int $user_id 사용자 ID
     * @param array $purchase_data 통합 구매 데이터 (선택)
     * @return int 점수 (1-5)
     */
    private function calculate_monetary_score( $user_id, $purchase_data = array() ) {
        $total_spent = 0.00;

        // 통합 데이터에서 총 구매액 조회
        if ( ! empty( $purchase_data['total_spent'] ) ) {
            $total_spent = floatval( $purchase_data['total_spent'] );
        } else {
            // 직접 데이터베이스 조회 (WooCommerce)
            global $wpdb;

            $total_spent = $wpdb->get_var( $wpdb->prepare(
                "SELECT SUM(CAST(pm2.meta_value AS DECIMAL(15,2)))
                 FROM {$wpdb->posts} p
                 INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id
                 INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id
                 WHERE p.post_type = 'shop_order'
                 AND p.post_status IN ('wc-completed', 'wc-processing')
                 AND pm1.meta_key = '_customer_user'
                 AND pm1.meta_value = %d
                 AND pm2.meta_key = '_order_total'",
                $user_id
            ) );

            $total_spent = floatval( $total_spent );
        }

        // GamiPress 포인트가 있으면 가치로 환산 (선택적)
        if ( ! empty( $purchase_data['gamipress']['total_points'] ) ) {
            // 포인트를 현금 가치로 환산 (예: 1000포인트 = 10,000원)
            $points_value = floatval( $purchase_data['gamipress']['total_points'] ) * 10;
            $total_spent += $points_value;
        }

        // 점수 계산
        foreach ( $this->scoring_criteria['monetary'] as $score => $criteria ) {
            $min_match = is_null( $criteria['min'] ) || $total_spent >= $criteria['min'];
            $max_match = is_null( $criteria['max'] ) || $total_spent <= $criteria['max'];

            if ( $min_match && $max_match ) {
                return $score;
            }
        }

        return 1; // 기본값
    }

    /**
     * RFM 점수로 세그먼트 매칭
     * 
     * @param array $rfm_scores RFM 점수 배열
     * @return string|null 세그먼트 코드
     */
    public function match_rfm_segment( $rfm_scores ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'acf_nudge_rfm_segments';

        // 테이블이 존재하는지 확인
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
            return null;
        }

        $r = $rfm_scores['r'];
        $f = $rfm_scores['f'];
        $m = $rfm_scores['m'];

        // RFM 점수로 세그먼트 매칭
        $segment = $wpdb->get_row( $wpdb->prepare(
            "SELECT segment_code, segment_name, strategy
             FROM $table_name
             WHERE is_active = 1
             AND r_score_min <= %d AND r_score_max >= %d
             AND f_score_min <= %d AND f_score_max >= %d
             AND m_score_min <= %d AND m_score_max >= %d
             ORDER BY priority DESC
             LIMIT 1",
            $r, $r, $f, $f, $m, $m
        ) );

        return $segment ? $segment->segment_code : null;
    }

    /**
     * 사용자의 RFM 세그먼트 조회
     * 
     * @param int $user_id 사용자 ID
     * @return array 세그먼트 정보
     */
    public function get_user_rfm_segment( $user_id = 0 ) {
        $rfm_scores = $this->calculate_rfm_scores( $user_id );
        $segment_code = $this->match_rfm_segment( $rfm_scores );

        return array(
            'scores' => $rfm_scores,
            'segment_code' => $segment_code,
            'rfm_code' => sprintf( 'R%d-F%d-M%d', $rfm_scores['r'], $rfm_scores['f'], $rfm_scores['m'] ),
        );
    }

    /**
     * RFM 점수 계산 기준 업데이트
     * 
     * @param array $criteria 새로운 기준
     */
    public function update_scoring_criteria( $criteria ) {
        $this->scoring_criteria = wp_parse_args( $criteria, $this->scoring_criteria );
    }

    /**
     * RFM 점수 계산 기준 조회
     * 
     * @return array 기준 배열
     */
    public function get_scoring_criteria() {
        return $this->scoring_criteria;
    }
}
