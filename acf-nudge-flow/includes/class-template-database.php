<?php
/**
 * 템플릿 데이터베이스 관리 클래스
 * 
 * 고객 세그먼트 및 넛지 템플릿을 데이터베이스에서 관리
 * 통합 사용자 프로필 기반 스마트 템플릿 추천
 * 
 * @package ACF_Nudge_Flow
 * @since 22.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Nudge_Flow_Template_Database {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 테이블 이름
     */
    private $table_segments;
    private $table_rfm_segments;
    private $table_templates;
    private $table_mapping;
    private $table_rfm_scoring;
    private $table_data_sources;
    private $table_unified_profiles;
    private $table_template_analytics;

    /**
     * 데이터 소스 통합 인스턴스
     */
    private $data_source;

    /**
     * 템플릿 캐시
     */
    private $template_cache = array();

    /**
     * 캐시 TTL (초)
     */
    private $cache_ttl = 600; // 10분

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
        global $wpdb;
        
        $prefix = $wpdb->prefix;
        $this->table_segments = $prefix . 'acf_nudge_customer_segments';
        $this->table_rfm_segments = $prefix . 'acf_nudge_rfm_segments';
        $this->table_templates = $prefix . 'acf_nudge_templates';
        $this->table_mapping = $prefix . 'acf_nudge_segment_template_mapping';
        $this->table_rfm_scoring = $prefix . 'acf_nudge_rfm_scoring';
        $this->table_data_sources = $prefix . 'acf_nudge_data_sources';
        $this->table_unified_profiles = $prefix . 'acf_nudge_unified_profiles';
        $this->table_template_analytics = $prefix . 'acf_nudge_template_analytics';

        // 데이터 소스 통합 클래스 로드
        if ( class_exists( 'ACF_Nudge_Flow_Data_Source_Integration' ) ) {
            $this->data_source = ACF_Nudge_Flow_Data_Source_Integration::instance();
        }

        // 훅 초기화
        $this->init_hooks();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 템플릿 표시 시 분석 데이터 기록
        add_action( 'acf_nudge_flow_template_displayed', array( $this, 'record_template_impression' ), 10, 2 );
        add_action( 'acf_nudge_flow_template_clicked', array( $this, 'record_template_click' ), 10, 2 );
        add_action( 'acf_nudge_flow_template_converted', array( $this, 'record_template_conversion' ), 10, 2 );

        // AJAX 핸들러
        add_action( 'wp_ajax_acf_nf_get_smart_templates', array( $this, 'ajax_get_smart_templates' ) );
        add_action( 'wp_ajax_nopriv_acf_nf_get_smart_templates', array( $this, 'ajax_get_smart_templates' ) );
    }

    /**
     * 데이터베이스 테이블 생성
     * 
     * [v22.5.2] SQL 파일을 읽어서 테이블 생성
     */
    public function create_tables() {
        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // 기본 테이블 SQL 파일
        $sql_files = array(
            ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/content/template/nudge_flow_database.sql',
            ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/content/template/NF_Database_Complete.sql',
            ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/content/template/NF_Database_Integrations.sql',
        );

        foreach ( $sql_files as $sql_file ) {
            if ( ! file_exists( $sql_file ) ) {
                continue;
            }

            $sql = file_get_contents( $sql_file );
            $this->execute_sql_file( $sql );
        }

        // 템플릿 분석 테이블 추가 생성
        $this->create_template_analytics_table();
        
        return true;
    }

    /**
     * SQL 파일 실행
     *
     * @param string $sql SQL 내용
     */
    private function execute_sql_file( $sql ) {
        global $wpdb;
        
        // WordPress 테이블 prefix 적용
        $sql = str_replace( 'wp_', $wpdb->prefix, $sql );
        
        // DROP TABLE 문 제거
        $sql = preg_replace( '/-- DROP TABLE IF EXISTS[^;]+;/i', '', $sql );
        $sql = preg_replace( '/DROP TABLE IF EXISTS[^;]+;/i', '', $sql );
        
        // SQL 문을 개별적으로 실행
        $queries = $this->parse_sql_queries( $sql );
        
        foreach ( $queries as $query ) {
            if ( empty( trim( $query ) ) || stripos( $query, '--' ) === 0 ) {
                continue;
            }
            
            if ( stripos( $query, 'CREATE TABLE' ) !== false ) {
                dbDelta( $query );
            } elseif ( stripos( $query, 'INSERT' ) !== false ) {
                $wpdb->query( $query );
            } elseif ( stripos( $query, 'CREATE OR REPLACE VIEW' ) !== false || stripos( $query, 'CREATE VIEW' ) !== false ) {
                $wpdb->query( $query );
            } elseif ( stripos( $query, 'CREATE PROCEDURE' ) !== false || stripos( $query, 'CREATE FUNCTION' ) !== false ) {
                $wpdb->query( $query );
            }
        }
    }

    /**
     * SQL 쿼리 파싱
     *
     * @param string $sql SQL 문자열
     * @return array 쿼리 배열
     */
    private function parse_sql_queries( $sql ) {
        $queries = array();
        $current_query = '';
        $in_string = false;
        $string_char = '';
        
        for ( $i = 0; $i < strlen( $sql ); $i++ ) {
            $char = $sql[$i];
            
            if ( ( $char === '"' || $char === "'" ) && ( $i === 0 || $sql[$i-1] !== '\\' ) ) {
                if ( ! $in_string ) {
                    $in_string = true;
                    $string_char = $char;
                } elseif ( $char === $string_char ) {
                    $in_string = false;
                    $string_char = '';
                }
            }
            
            $current_query .= $char;
            
            if ( $char === ';' && ! $in_string ) {
                $queries[] = trim( $current_query );
                $current_query = '';
            }
        }
        
        if ( ! empty( trim( $current_query ) ) ) {
            $queries[] = trim( $current_query );
        }

        return $queries;
    }

    /**
     * 템플릿 분석 테이블 생성
     */
    private function create_template_analytics_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_template_analytics} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            template_id varchar(50) NOT NULL,
            user_id bigint(20) unsigned DEFAULT 0,
            visitor_id varchar(100) DEFAULT NULL,
            event_type enum('impression', 'click', 'conversion', 'dismiss') NOT NULL,
            segment_code varchar(50) DEFAULT NULL,
            rfm_code varchar(20) DEFAULT NULL,
            page_url varchar(500) DEFAULT NULL,
            referrer varchar(500) DEFAULT NULL,
            device_type varchar(20) DEFAULT NULL,
            browser varchar(50) DEFAULT NULL,
            user_agent varchar(500) DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            country_code varchar(2) DEFAULT NULL,
            session_id varchar(100) DEFAULT NULL,
            conversion_value decimal(15,2) DEFAULT 0.00,
            metadata longtext DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_template_id (template_id),
            KEY idx_user_id (user_id),
            KEY idx_event_type (event_type),
            KEY idx_created_at (created_at),
            KEY idx_segment_code (segment_code)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * 고객 세그먼트 조회
     * 
     * @param array $args 조회 조건
     * @return array 세그먼트 배열
     */
    public function get_customer_segments( $args = array() ) {
        global $wpdb;

        $defaults = array(
            'category' => '',
            'trigger_type' => '',
            'is_active' => 1,
            'orderby' => 'priority',
            'order' => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );

        $where = array( '1=1' );
        $where_values = array();

        if ( ! empty( $args['category'] ) ) {
            $where[] = 'category = %s';
            $where_values[] = $args['category'];
        }

        if ( ! empty( $args['trigger_type'] ) ) {
            $where[] = 'trigger_type = %s';
            $where_values[] = $args['trigger_type'];
        }

        if ( isset( $args['is_active'] ) ) {
            $where[] = 'is_active = %d';
            $where_values[] = $args['is_active'];
        }

        $where_clause = implode( ' AND ', $where );
        $orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );

        $query = "SELECT * FROM {$this->table_segments} WHERE $where_clause ORDER BY $orderby";

        if ( ! empty( $where_values ) ) {
            $query = $wpdb->prepare( $query, $where_values );
        }

        return $wpdb->get_results( $query, ARRAY_A );
    }

    /**
     * RFM 세그먼트 조회
     * 
     * @param array $args 조회 조건
     * @return array RFM 세그먼트 배열
     */
    public function get_rfm_segments( $args = array() ) {
        global $wpdb;

        $defaults = array(
            'strategy' => '',
            'is_active' => 1,
            'orderby' => 'priority',
            'order' => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );

        $where = array( '1=1' );
        $where_values = array();

        if ( ! empty( $args['strategy'] ) ) {
            $where[] = 'strategy = %s';
            $where_values[] = $args['strategy'];
        }

        if ( isset( $args['is_active'] ) ) {
            $where[] = 'is_active = %d';
            $where_values[] = $args['is_active'];
        }

        $where_clause = implode( ' AND ', $where );
        $orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );

        $query = "SELECT * FROM {$this->table_rfm_segments} WHERE $where_clause ORDER BY $orderby";

        if ( ! empty( $where_values ) ) {
            $query = $wpdb->prepare( $query, $where_values );
        }

        return $wpdb->get_results( $query, ARRAY_A );
    }

    /**
     * 넛지 템플릿 조회
     * 
     * @param array $args 조회 조건
     * @return array 템플릿 배열
     */
    public function get_nudge_templates( $args = array() ) {
        global $wpdb;

        $defaults = array(
            'type' => '',
            'category' => '',
            'trigger_type' => '',
            'is_active' => 1,
            'orderby' => 'usage_count',
            'order' => 'DESC',
            'limit' => 0,
        );

        $args = wp_parse_args( $args, $defaults );

        $where = array( '1=1' );
        $where_values = array();

        if ( ! empty( $args['type'] ) ) {
            $where[] = 'type = %s';
            $where_values[] = $args['type'];
        }

        if ( ! empty( $args['category'] ) ) {
            $where[] = 'category = %s';
            $where_values[] = $args['category'];
        }

        if ( ! empty( $args['trigger_type'] ) ) {
            $where[] = 'trigger_type = %s';
            $where_values[] = $args['trigger_type'];
        }

        if ( isset( $args['is_active'] ) ) {
            $where[] = 'is_active = %d';
            $where_values[] = $args['is_active'];
        }

        $where_clause = implode( ' AND ', $where );
        $orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );
        $limit_clause = $args['limit'] > 0 ? sprintf( ' LIMIT %d', intval( $args['limit'] ) ) : '';

        $query = "SELECT * FROM {$this->table_templates} WHERE $where_clause ORDER BY $orderby" . $limit_clause;

        if ( ! empty( $where_values ) ) {
            $query = $wpdb->prepare( $query, $where_values );
        }

        return $wpdb->get_results( $query, ARRAY_A );
    }

    /**
     * 세그먼트에 맞는 템플릿 조회
     * 
     * [v22.5.2] 통합 사용자 프로필 기반 템플릿 추천
     * 
     * @param string $segment_id 세그먼트 ID
     * @param string $rfm_segment_code RFM 세그먼트 코드
     * @param array $user_profile 통합 사용자 프로필 데이터 (선택)
     * @return array 템플릿 배열
     */
    public function get_templates_by_segment( $segment_id = '', $rfm_segment_code = '', $user_profile = array() ) {
        global $wpdb;

        $where = array( 't.is_active = 1' );
        $join = array();
        $where_values = array();

        if ( ! empty( $segment_id ) ) {
            $join[] = "INNER JOIN {$this->table_mapping} m ON t.id = m.template_id";
            $join[] = "INNER JOIN {$this->table_segments} s ON m.segment_id = s.id";
            $where[] = 's.segment_id = %s';
            $where_values[] = $segment_id;
        }

        if ( ! empty( $rfm_segment_code ) ) {
            if ( empty( $join ) ) {
                $join[] = "INNER JOIN {$this->table_mapping} m ON t.id = m.template_id";
            }
            $join[] = "INNER JOIN {$this->table_rfm_segments} r ON m.rfm_segment_id = r.id";
            $where[] = 'r.segment_code = %s';
            $where_values[] = $rfm_segment_code;
        }

        $join_clause = ! empty( $join ) ? implode( ' ', $join ) : '';
        $where_clause = implode( ' AND ', $where );

        $query = "SELECT DISTINCT t.*, m.match_priority, m.is_recommended
                  FROM {$this->table_templates} t
                  $join_clause
                  WHERE $where_clause
                  ORDER BY m.is_recommended DESC, m.match_priority DESC, t.usage_count DESC";

        if ( ! empty( $where_values ) ) {
            $query = $wpdb->prepare( $query, $where_values );
        }

        $templates = $wpdb->get_results( $query, ARRAY_A );

        // 통합 프로필 데이터가 있으면 추가 필터링 및 점수 조정
        if ( ! empty( $user_profile ) && ! empty( $templates ) ) {
            $templates = $this->score_templates_by_profile( $templates, $user_profile );
        }

        return $templates;
    }

    /**
     * 스마트 템플릿 추천
     * 
     * 통합 사용자 프로필, 행동 점수, 세그먼트를 종합하여
     * 가장 적합한 템플릿을 추천합니다.
     * 
     * @param int $user_id 사용자 ID (0이면 비회원)
     * @param array $context 컨텍스트 정보 (페이지, 트리거 등)
     * @return array 추천 템플릿 배열
     */
    public function get_smart_templates( $user_id = 0, $context = array() ) {
        // 캐시 키 생성
        $cache_key = 'smart_templates_' . $user_id . '_' . md5( serialize( $context ) );
        
        // 캐시 확인
        if ( isset( $this->template_cache[ $cache_key ] ) ) {
            $cached = $this->template_cache[ $cache_key ];
            if ( $cached['expires'] > time() ) {
                return $cached['data'];
            }
        }

        // 통합 프로필 조회
        $profile = array();
        if ( $user_id && $this->data_source ) {
            $profile = $this->data_source->get_unified_profile( $user_id );
        }

        // 기본 템플릿 조회 조건
        $template_args = array(
            'is_active' => 1,
            'limit' => 20,
        );

        // 컨텍스트 기반 필터링
        if ( ! empty( $context['trigger_type'] ) ) {
            $template_args['trigger_type'] = $context['trigger_type'];
        }
        if ( ! empty( $context['category'] ) ) {
            $template_args['category'] = $context['category'];
        }

        // 템플릿 조회
        $templates = $this->get_nudge_templates( $template_args );

        // 빈 결과면 기본 템플릿 반환
        if ( empty( $templates ) ) {
            $templates = $this->get_nudge_templates( array( 'is_active' => 1, 'limit' => 10 ) );
        }

        // 스마트 점수 계산
        $scored_templates = $this->calculate_smart_scores( $templates, $profile, $context );

        // 점수별 정렬
        usort( $scored_templates, function( $a, $b ) {
            return $b['smart_score'] - $a['smart_score'];
        });

        // 상위 5개 반환
        $result = array_slice( $scored_templates, 0, 5 );

        // 캐시 저장
        $this->template_cache[ $cache_key ] = array(
            'data' => $result,
            'expires' => time() + $this->cache_ttl,
        );

        return $result;
    }

    /**
     * 스마트 점수 계산
     * 
     * @param array $templates 템플릿 배열
     * @param array $profile 사용자 프로필
     * @param array $context 컨텍스트
     * @return array 점수가 추가된 템플릿 배열
     */
    private function calculate_smart_scores( $templates, $profile, $context ) {
        foreach ( $templates as &$template ) {
            $score = 50; // 기본 점수

            // 1. 성공률 기반 점수 (0-20점)
            if ( ! empty( $template['success_rate'] ) ) {
                $score += floatval( $template['success_rate'] ) * 0.2;
            }

            // 2. 사용 빈도 기반 점수 (0-10점)
            if ( ! empty( $template['usage_count'] ) ) {
                $usage = intval( $template['usage_count'] );
                $score += min( 10, $usage / 100 );
            }

            // 3. 프로필 기반 점수 조정
            if ( ! empty( $profile ) ) {
                $score += $this->calculate_profile_match_score( $template, $profile );
            }

            // 4. 컨텍스트 기반 점수 조정
            $score += $this->calculate_context_match_score( $template, $context );

            // 5. 시간대 기반 점수 조정
            $score += $this->calculate_time_based_score( $template );

            // 최종 점수 (0-100 범위로 정규화)
            $template['smart_score'] = min( 100, max( 0, round( $score ) ) );
        }

        return $templates;
    }

    /**
     * 프로필 매칭 점수 계산
     * 
     * @param array $template 템플릿
     * @param array $profile 사용자 프로필
     * @return int 추가 점수
     */
    private function calculate_profile_match_score( $template, $profile ) {
        $score = 0;

        // 행동 점수 기반 매칭
        if ( ! empty( $profile['scores'] ) ) {
            $scores = $profile['scores'];
            $category = strtolower( $template['category'] ?? '' );

            // 이탈 위험 고객 -> 리텐션 템플릿
            if ( $scores['churn_risk'] >= 60 ) {
                if ( strpos( $category, 'retention' ) !== false || 
                     strpos( $category, 'win back' ) !== false ||
                     strpos( $category, 'winback' ) !== false ) {
                    $score += 20;
                }
            }

            // 높은 구매 의도 -> 전환 템플릿
            if ( $scores['purchase_intent'] >= 60 ) {
                if ( strpos( $category, 'conversion' ) !== false || 
                     strpos( $category, 'checkout' ) !== false ||
                     strpos( $category, 'cart' ) !== false ) {
                    $score += 15;
                }
            }

            // VIP 고객 -> 프리미엄 템플릿
            if ( $scores['value'] >= 80 ) {
                if ( strpos( $category, 'vip' ) !== false || 
                     strpos( $category, 'premium' ) !== false ||
                     strpos( $category, 'exclusive' ) !== false ) {
                    $score += 15;
                }
            }

            // 높은 참여도 -> 크로스셀/업셀 템플릿
            if ( $scores['engagement'] >= 70 ) {
                if ( strpos( $category, 'cross' ) !== false || 
                     strpos( $category, 'upsell' ) !== false ) {
                    $score += 10;
                }
            }
        }

        // 추론된 세그먼트 기반 매칭
        if ( ! empty( $profile['inferred_segments'] ) ) {
            $segments = $profile['inferred_segments'];

            // 장바구니 포기자
            if ( in_array( 'CART_ABANDONER', $segments ) ) {
                if ( strpos( strtolower( $template['category'] ?? '' ), 'cart' ) !== false ) {
                    $score += 25;
                }
            }

            // 신규 사용자
            if ( in_array( 'NEW_USER', $segments ) ) {
                if ( strpos( strtolower( $template['category'] ?? '' ), 'welcome' ) !== false ||
                     strpos( strtolower( $template['category'] ?? '' ), 'onboarding' ) !== false ) {
                    $score += 20;
                }
            }

            // 활성 학습자
            if ( in_array( 'ACTIVE_LEARNER', $segments ) || in_array( 'POWER_LEARNER', $segments ) ) {
                if ( strpos( strtolower( $template['category'] ?? '' ), 'course' ) !== false ||
                     strpos( strtolower( $template['category'] ?? '' ), 'learning' ) !== false ) {
                    $score += 15;
                }
            }
        }

        // 데이터 소스별 특수 매칭
        if ( ! empty( $profile['sources'] ) ) {
            $sources = $profile['sources'];

            // WooCommerce 데이터
            if ( ! empty( $sources['woocommerce'] ) ) {
                $woo = $sources['woocommerce'];
                
                // 장바구니에 상품이 있으면 체크아웃 템플릿 우선
                if ( ! empty( $woo['cart']['item_count'] ) && $woo['cart']['item_count'] > 0 ) {
                    if ( strpos( strtolower( $template['trigger_type'] ?? '' ), 'cart' ) !== false ) {
                        $score += 20;
                    }
                }

                // 위시리스트 있으면 프로모션 템플릿 우선
                if ( ! empty( $woo['wishlist_count'] ) && $woo['wishlist_count'] > 0 ) {
                    if ( strpos( strtolower( $template['category'] ?? '' ), 'promo' ) !== false ||
                         strpos( strtolower( $template['category'] ?? '' ), 'sale' ) !== false ) {
                        $score += 15;
                    }
                }
            }

            // LearnDash 데이터
            if ( ! empty( $sources['learndash'] ) ) {
                $ld = $sources['learndash'];
                
                // 진행 중인 코스가 있으면 학습 독려 템플릿
                if ( $ld['in_progress_courses'] > 0 && $ld['completion_rate'] < 50 ) {
                    if ( strpos( strtolower( $template['category'] ?? '' ), 'learn' ) !== false ||
                         strpos( strtolower( $template['category'] ?? '' ), 'progress' ) !== false ) {
                        $score += 15;
                    }
                }
            }

            // GamiPress 데이터
            if ( ! empty( $sources['gamipress'] ) ) {
                $gp = $sources['gamipress'];
                
                // 많은 포인트 보유자에게 보상 템플릿
                if ( $gp['total_points'] > 5000 ) {
                    if ( strpos( strtolower( $template['category'] ?? '' ), 'reward' ) !== false ||
                         strpos( strtolower( $template['category'] ?? '' ), 'redeem' ) !== false ) {
                        $score += 15;
                    }
                }
            }
        }

        return $score;
    }

    /**
     * 컨텍스트 매칭 점수 계산
     * 
     * @param array $template 템플릿
     * @param array $context 컨텍스트
     * @return int 추가 점수
     */
    private function calculate_context_match_score( $template, $context ) {
        $score = 0;

        // 페이지 타입 매칭
        if ( ! empty( $context['page_type'] ) ) {
            $page_type = strtolower( $context['page_type'] );
            $template_trigger = strtolower( $template['trigger_type'] ?? '' );

            $page_trigger_map = array(
                'product' => array( 'product_view', 'product', 'pdp' ),
                'cart' => array( 'cart', 'checkout', 'add_to_cart' ),
                'checkout' => array( 'checkout', 'purchase', 'order' ),
                'category' => array( 'category', 'browse', 'collection' ),
                'search' => array( 'search', 'no_result' ),
                'account' => array( 'account', 'login', 'profile' ),
                'course' => array( 'course', 'lesson', 'learning' ),
            );

            if ( isset( $page_trigger_map[ $page_type ] ) ) {
                foreach ( $page_trigger_map[ $page_type ] as $trigger ) {
                    if ( strpos( $template_trigger, $trigger ) !== false ) {
                        $score += 15;
                        break;
                    }
                }
            }
        }

        // 디바이스 타입 매칭
        if ( ! empty( $context['device'] ) && ! empty( $template['device_target'] ) ) {
            if ( $context['device'] === $template['device_target'] || $template['device_target'] === 'all' ) {
                $score += 5;
            }
        }

        // 트래픽 소스 매칭
        if ( ! empty( $context['traffic_source'] ) ) {
            $source = strtolower( $context['traffic_source'] );
            
            // 광고 트래픽 -> 전환 템플릿
            if ( in_array( $source, array( 'cpc', 'ppc', 'ad', 'paid' ) ) ) {
                if ( strpos( strtolower( $template['category'] ?? '' ), 'conversion' ) !== false ) {
                    $score += 10;
                }
            }
            
            // 소셜 트래픽 -> 소셜 프루프 템플릿
            if ( in_array( $source, array( 'social', 'facebook', 'instagram', 'twitter' ) ) ) {
                if ( strpos( strtolower( $template['category'] ?? '' ), 'social' ) !== false ) {
                    $score += 10;
                }
            }
        }

        // 첫 방문 여부
        if ( ! empty( $context['is_first_visit'] ) && $context['is_first_visit'] ) {
            if ( strpos( strtolower( $template['category'] ?? '' ), 'welcome' ) !== false ||
                 strpos( strtolower( $template['category'] ?? '' ), 'first' ) !== false ) {
                $score += 15;
            }
        }

        return $score;
    }

    /**
     * 시간 기반 점수 계산
     * 
     * @param array $template 템플릿
     * @return int 추가 점수
     */
    private function calculate_time_based_score( $template ) {
        $score = 0;
        $current_hour = intval( date( 'G' ) );
        $current_day = intval( date( 'N' ) ); // 1 (Monday) to 7 (Sunday)

        // 템플릿에 시간대 설정이 있으면 적용
        if ( ! empty( $template['time_settings'] ) ) {
            $settings = maybe_unserialize( $template['time_settings'] );
            
            if ( ! empty( $settings['peak_hours'] ) && in_array( $current_hour, $settings['peak_hours'] ) ) {
                $score += 10;
            }
            
            if ( ! empty( $settings['peak_days'] ) && in_array( $current_day, $settings['peak_days'] ) ) {
                $score += 5;
            }
        }

        // 기본 시간대 보정
        // 점심 시간 (12-14시) - 구매 활동 증가 시간
        if ( $current_hour >= 12 && $current_hour <= 14 ) {
            if ( strpos( strtolower( $template['category'] ?? '' ), 'sale' ) !== false ||
                 strpos( strtolower( $template['category'] ?? '' ), 'promo' ) !== false ) {
                $score += 5;
            }
        }

        // 저녁 시간 (19-22시) - 브라우징 피크 타임
        if ( $current_hour >= 19 && $current_hour <= 22 ) {
            if ( strpos( strtolower( $template['category'] ?? '' ), 'recommend' ) !== false ||
                 strpos( strtolower( $template['category'] ?? '' ), 'browse' ) !== false ) {
                $score += 5;
            }
        }

        // 주말
        if ( $current_day >= 6 ) {
            if ( strpos( strtolower( $template['category'] ?? '' ), 'weekend' ) !== false ||
                 strpos( strtolower( $template['category'] ?? '' ), 'special' ) !== false ) {
                $score += 5;
            }
        }

        return $score;
    }

    /**
     * 사용자 프로필 기반 템플릿 점수 조정 (레거시 메서드)
     * 
     * @param array $templates 템플릿 배열
     * @param array $user_profile 통합 사용자 프로필
     * @return array 점수 조정된 템플릿 배열
     */
    private function score_templates_by_profile( $templates, $user_profile ) {
        foreach ( $templates as &$template ) {
            $score_boost = 0;

            // 행동 점수 사용
            if ( ! empty( $user_profile['scores'] ) ) {
                $scores = $user_profile['scores'];
                
                // 이탈 위험 고객
                if ( $scores['churn_risk'] >= 60 ) {
                    if ( strpos( $template['category'], 'Retention' ) !== false || 
                         strpos( $template['category'], 'Win Back' ) !== false ) {
                        $score_boost += 20;
                    }
                }

                // 높은 구매 의도
                if ( $scores['purchase_intent'] >= 60 ) {
                    if ( strpos( $template['category'], 'Conversion' ) !== false ) {
                        $score_boost += 15;
                    }
                }

                // VIP 고객
                if ( $scores['value'] >= 80 ) {
                    $score_boost += 10;
                }
            }

            // 회원 등급 기반 점수 조정 (레거시 호환)
            if ( ! empty( $user_profile['membership']['membership_level'] ) ) {
                $level = strtolower( $user_profile['membership']['membership_level'] );
                if ( strpos( $level, 'vip' ) !== false || strpos( $level, 'premium' ) !== false ) {
                    $score_boost += 10;
                }
            }

            // 커뮤니티 활동 기반 점수 조정
            if ( ! empty( $user_profile['sources']['buddypress']['activity_count'] ) ) {
                $activity_count = intval( $user_profile['sources']['buddypress']['activity_count'] );
                if ( $activity_count > 50 ) {
                    $score_boost += 5;
                }
            }

            // 게이미피케이션 포인트 기반 점수 조정
            if ( ! empty( $user_profile['sources']['gamipress']['total_points'] ) ) {
                $points = intval( $user_profile['sources']['gamipress']['total_points'] );
                if ( $points > 10000 ) {
                    $score_boost += 5;
                }
            }

            // 점수 적용
            $template['match_priority'] = intval( $template['match_priority'] ?? 0 ) + $score_boost;
        }

        // 점수 순으로 재정렬
        usort( $templates, function( $a, $b ) {
            return intval( $b['match_priority'] ) - intval( $a['match_priority'] );
        } );

        return $templates;
    }

    /**
     * 템플릿 노출 기록
     * 
     * @param string $template_id 템플릿 ID
     * @param array $data 추가 데이터
     */
    public function record_template_impression( $template_id, $data = array() ) {
        $this->record_template_event( $template_id, 'impression', $data );
        $this->increment_template_usage( $template_id );
    }

    /**
     * 템플릿 클릭 기록
     * 
     * @param string $template_id 템플릿 ID
     * @param array $data 추가 데이터
     */
    public function record_template_click( $template_id, $data = array() ) {
        $this->record_template_event( $template_id, 'click', $data );
    }

    /**
     * 템플릿 전환 기록
     * 
     * @param string $template_id 템플릿 ID
     * @param array $data 추가 데이터 (conversion_value 포함 가능)
     */
    public function record_template_conversion( $template_id, $data = array() ) {
        $this->record_template_event( $template_id, 'conversion', $data );
        
        // 성공률 업데이트
        $this->update_template_success_rate_auto( $template_id );
    }

    /**
     * 템플릿 이벤트 기록
     * 
     * @param string $template_id 템플릿 ID
     * @param string $event_type 이벤트 타입
     * @param array $data 추가 데이터
     */
    private function record_template_event( $template_id, $event_type, $data = array() ) {
        global $wpdb;

        $user_id = get_current_user_id();
        $visitor_id = isset( $_COOKIE['acf_nf_visitor_id'] ) ? sanitize_text_field( $_COOKIE['acf_nf_visitor_id'] ) : '';
        $session_id = isset( $_COOKIE['acf_nf_session_id'] ) ? sanitize_text_field( $_COOKIE['acf_nf_session_id'] ) : '';

        $insert_data = array(
            'template_id' => $template_id,
            'user_id' => $user_id,
            'visitor_id' => $visitor_id,
            'event_type' => $event_type,
            'segment_code' => $data['segment_code'] ?? null,
            'rfm_code' => $data['rfm_code'] ?? null,
            'page_url' => isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( $_SERVER['REQUEST_URI'] ) : null,
            'referrer' => isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( $_SERVER['HTTP_REFERER'] ) : null,
            'device_type' => $this->detect_device_type(),
            'browser' => $this->detect_browser(),
            'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : null,
            'ip_address' => $this->get_client_ip(),
            'session_id' => $session_id,
            'conversion_value' => $data['conversion_value'] ?? 0,
            'metadata' => ! empty( $data['metadata'] ) ? wp_json_encode( $data['metadata'] ) : null,
            'created_at' => current_time( 'mysql' ),
        );

        $wpdb->insert( $this->table_template_analytics, $insert_data );
    }

    /**
     * 디바이스 타입 감지
     * 
     * @return string
     */
    private function detect_device_type() {
        $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        if ( preg_match( '/Mobile|Android|iPhone|iPad|iPod|webOS|BlackBerry|IEMobile|Opera Mini/i', $user_agent ) ) {
            if ( preg_match( '/iPad|Tablet/i', $user_agent ) ) {
                return 'tablet';
            }
            return 'mobile';
        }
        
        return 'desktop';
    }

    /**
     * 브라우저 감지
     * 
     * @return string
     */
    private function detect_browser() {
        $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        if ( strpos( $user_agent, 'Chrome' ) !== false ) return 'Chrome';
        if ( strpos( $user_agent, 'Firefox' ) !== false ) return 'Firefox';
        if ( strpos( $user_agent, 'Safari' ) !== false ) return 'Safari';
        if ( strpos( $user_agent, 'Edge' ) !== false ) return 'Edge';
        if ( strpos( $user_agent, 'MSIE' ) !== false || strpos( $user_agent, 'Trident' ) !== false ) return 'IE';
        
        return 'Other';
    }

    /**
     * 클라이언트 IP 조회
     * 
     * @return string
     */
    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        );

        foreach ( $ip_keys as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ip = sanitize_text_field( $_SERVER[ $key ] );
                // 여러 IP가 있으면 첫 번째 사용
                if ( strpos( $ip, ',' ) !== false ) {
                    $ip = trim( explode( ',', $ip )[0] );
                }
                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }

        return '';
    }

    /**
     * 템플릿 사용 횟수 증가
     * 
     * @param string $template_id 템플릿 ID
     */
    public function increment_template_usage( $template_id ) {
        global $wpdb;

        $wpdb->query( $wpdb->prepare(
            "UPDATE {$this->table_templates}
             SET usage_count = usage_count + 1
             WHERE template_id = %s",
            $template_id
        ) );
    }

    /**
     * 템플릿 성공률 업데이트
     * 
     * @param string $template_id 템플릿 ID
     * @param float $success_rate 성공률 (%)
     */
    public function update_template_success_rate( $template_id, $success_rate ) {
        global $wpdb;

        $wpdb->query( $wpdb->prepare(
            "UPDATE {$this->table_templates}
             SET success_rate = %f
             WHERE template_id = %s",
            $success_rate,
            $template_id
        ) );
    }

    /**
     * 템플릿 성공률 자동 계산 및 업데이트
     * 
     * @param string $template_id 템플릿 ID
     */
    private function update_template_success_rate_auto( $template_id ) {
        global $wpdb;

        // 최근 30일 데이터 기준
        $stats = $wpdb->get_row( $wpdb->prepare(
            "SELECT
                COUNT(CASE WHEN event_type = 'impression' THEN 1 END) as impressions,
                COUNT(CASE WHEN event_type = 'click' THEN 1 END) as clicks,
                COUNT(CASE WHEN event_type = 'conversion' THEN 1 END) as conversions
             FROM {$this->table_template_analytics}
             WHERE template_id = %s
             AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            $template_id
        ) );

        if ( $stats && $stats->impressions > 0 ) {
            // 전환율 = (전환 / 노출) * 100
            $success_rate = ( $stats->conversions / $stats->impressions ) * 100;
            $this->update_template_success_rate( $template_id, $success_rate );
        }
    }

    /**
     * 템플릿 분석 데이터 조회
     * 
     * @param string $template_id 템플릿 ID
     * @param int $days 조회 기간 (일)
     * @return array 분석 데이터
     */
    public function get_template_analytics( $template_id, $days = 30 ) {
        global $wpdb;

        $stats = $wpdb->get_row( $wpdb->prepare(
            "SELECT
                COUNT(CASE WHEN event_type = 'impression' THEN 1 END) as impressions,
                COUNT(CASE WHEN event_type = 'click' THEN 1 END) as clicks,
                COUNT(CASE WHEN event_type = 'conversion' THEN 1 END) as conversions,
                COUNT(CASE WHEN event_type = 'dismiss' THEN 1 END) as dismissals,
                SUM(CASE WHEN event_type = 'conversion' THEN conversion_value ELSE 0 END) as total_revenue,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT visitor_id) as unique_visitors
             FROM {$this->table_template_analytics}
             WHERE template_id = %s
             AND created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)",
            $template_id,
            $days
        ), ARRAY_A );

        // 비율 계산
        if ( $stats ) {
            $stats['ctr'] = $stats['impressions'] > 0 
                ? round( ( $stats['clicks'] / $stats['impressions'] ) * 100, 2 ) 
                : 0;
            $stats['conversion_rate'] = $stats['impressions'] > 0 
                ? round( ( $stats['conversions'] / $stats['impressions'] ) * 100, 2 ) 
                : 0;
            $stats['dismiss_rate'] = $stats['impressions'] > 0 
                ? round( ( $stats['dismissals'] / $stats['impressions'] ) * 100, 2 ) 
                : 0;
        }

        return $stats;
    }

    /**
     * AJAX: 스마트 템플릿 조회
     */
    public function ajax_get_smart_templates() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $user_id = get_current_user_id();
        $context = array(
            'page_type' => isset( $_POST['page_type'] ) ? sanitize_text_field( $_POST['page_type'] ) : '',
            'trigger_type' => isset( $_POST['trigger_type'] ) ? sanitize_text_field( $_POST['trigger_type'] ) : '',
            'category' => isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '',
            'device' => isset( $_POST['device'] ) ? sanitize_text_field( $_POST['device'] ) : '',
            'traffic_source' => isset( $_POST['traffic_source'] ) ? sanitize_text_field( $_POST['traffic_source'] ) : '',
            'is_first_visit' => isset( $_POST['is_first_visit'] ) && $_POST['is_first_visit'] === 'true',
        );

        $templates = $this->get_smart_templates( $user_id, $context );

        wp_send_json_success( array(
            'templates' => $templates,
            'context' => $context,
        ) );
    }

    /**
     * 테이블 이름 조회
     * 
     * @param string $table 테이블 키
     * @return string 테이블 이름
     */
    public function get_table_name( $table ) {
        $tables = array(
            'segments' => $this->table_segments,
            'rfm_segments' => $this->table_rfm_segments,
            'templates' => $this->table_templates,
            'mapping' => $this->table_mapping,
            'rfm_scoring' => $this->table_rfm_scoring,
            'data_sources' => $this->table_data_sources,
            'unified_profiles' => $this->table_unified_profiles,
            'template_analytics' => $this->table_template_analytics,
        );

        return isset( $tables[ $table ] ) ? $tables[ $table ] : '';
    }
}

/**
 * 전역 함수: 템플릿 데이터베이스 인스턴스 반환
 *
 * @return ACF_Nudge_Flow_Template_Database
 */
function acf_nudge_flow_template_db() {
    return ACF_Nudge_Flow_Template_Database::instance();
}
