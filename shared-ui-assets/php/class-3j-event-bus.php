<?php
/**
 * 3J Labs Event Bus - 크로스 플러그인 이벤트 시스템
 *
 * 플러그인 간 이벤트 발행/구독(Pub/Sub) 패턴을 구현합니다.
 * WordPress hooks를 기반으로 하되, 추가적인 메타데이터와 필터링 기능을 제공합니다.
 *
 * 사용법:
 * ```php
 * // 이벤트 구독
 * JJ_3J_Event_Bus::instance()->subscribe( 'style_updated', function( $data, $event ) {
 *     // 스타일이 업데이트되면 캐시 무효화
 *     my_clear_cache();
 * }, array(
 *     'source_plugin' => 'acf-css-master',  // 특정 플러그인 이벤트만
 *     'priority'      => 10,
 * ));
 *
 * // 이벤트 발행
 * JJ_3J_Event_Bus::instance()->publish( 'style_updated', array(
 *     'style_id' => 123,
 *     'changes'  => array( 'color' => '#ff0000' ),
 * ));
 * ```
 *
 * @package    3J_Labs_Shared
 * @subpackage Event_Bus
 * @since      1.0.0
 * @version    1.0.0
 * @author     3J Labs (제이x제니x제이슨 연구소)
 * @link       https://3j-labs.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_3J_Event_Bus' ) ) {

    /**
     * 이벤트 버스 클래스
     *
     * @since 1.0.0
     */
    class JJ_3J_Event_Bus {

        /**
         * 싱글톤 인스턴스
         *
         * @var JJ_3J_Event_Bus|null
         */
        private static $instance = null;

        /**
         * 이벤트 훅 프리픽스
         *
         * @var string
         */
        const EVENT_PREFIX = '3j_labs_event_';

        /**
         * 등록된 구독자 목록
         *
         * @var array
         */
        private $subscribers = array();

        /**
         * 이벤트 히스토리 (디버그용)
         *
         * @var array
         */
        private $event_history = array();

        /**
         * 히스토리 최대 크기
         *
         * @var int
         */
        private $history_limit = 100;

        /**
         * 현재 발행 중인 이벤트 (재귀 방지용)
         *
         * @var array
         */
        private $publishing = array();

        /**
         * 디버그 모드
         *
         * @var bool
         */
        private $debug_mode = false;

        /**
         * 싱글톤 인스턴스 반환
         *
         * @since 1.0.0
         * @return JJ_3J_Event_Bus
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * 생성자
         *
         * @since 1.0.0
         */
        private function __construct() {
            $this->debug_mode = defined( 'WP_DEBUG' ) && WP_DEBUG;

            // REST API 엔드포인트 등록
            add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
        }

        /**
         * 이벤트 구독
         *
         * @since 1.0.0
         * @param string   $event    이벤트 이름
         * @param callable $callback 콜백 함수
         * @param array    $options  옵션
         *                           - source_plugin: 특정 플러그인 이벤트만 수신
         *                           - priority: 우선순위 (기본 10)
         *                           - once: 한 번만 실행 후 자동 해제
         *                           - filter: 데이터 필터 조건 (callable)
         * @return string 구독 ID
         */
        public function subscribe( $event, $callback, $options = array() ) {
            $defaults = array(
                'source_plugin' => null,
                'priority'      => 10,
                'once'          => false,
                'filter'        => null,
            );

            $options = wp_parse_args( $options, $defaults );

            // 고유 구독 ID 생성
            $subscriber_id = $this->generate_subscriber_id( $event, $callback );

            // 구독자 정보 저장
            $this->subscribers[ $event ][ $subscriber_id ] = array(
                'callback' => $callback,
                'options'  => $options,
            );

            // WordPress hook에 래퍼 등록
            $wrapper = $this->create_callback_wrapper( $event, $subscriber_id );
            add_action( self::EVENT_PREFIX . $event, $wrapper, $options['priority'], 2 );

            return $subscriber_id;
        }

        /**
         * 이벤트 구독 해제
         *
         * @since 1.0.0
         * @param string $event         이벤트 이름
         * @param string $subscriber_id 구독 ID (subscribe 반환값)
         * @return bool
         */
        public function unsubscribe( $event, $subscriber_id ) {
            if ( ! isset( $this->subscribers[ $event ][ $subscriber_id ] ) ) {
                return false;
            }

            $subscriber = $this->subscribers[ $event ][ $subscriber_id ];

            // WordPress hook에서 제거
            remove_action(
                self::EVENT_PREFIX . $event,
                $this->create_callback_wrapper( $event, $subscriber_id ),
                $subscriber['options']['priority']
            );

            // 내부 목록에서 제거
            unset( $this->subscribers[ $event ][ $subscriber_id ] );

            return true;
        }

        /**
         * 이벤트 발행
         *
         * @since 1.0.0
         * @param string $event  이벤트 이름
         * @param array  $data   이벤트 데이터
         * @param array  $meta   추가 메타데이터
         *                       - source_plugin: 발행 플러그인 슬러그
         *                       - async: 비동기 실행 여부 (기본 false)
         *                       - persist: 영속 저장 여부 (기본 false)
         * @return array 실행 결과
         */
        public function publish( $event, $data = array(), $meta = array() ) {
            // 재귀 방지
            if ( isset( $this->publishing[ $event ] ) ) {
                return array(
                    'success'    => false,
                    'error'      => 'recursive_publish',
                    'message'    => '이벤트가 재귀적으로 발행되었습니다.',
                );
            }

            $this->publishing[ $event ] = true;

            $defaults = array(
                'source_plugin' => $this->detect_source_plugin(),
                'async'         => false,
                'persist'       => false,
            );

            $meta = wp_parse_args( $meta, $defaults );

            // 이벤트 객체 생성
            $event_obj = array(
                'name'          => $event,
                'data'          => $data,
                'meta'          => $meta,
                'timestamp'     => microtime( true ),
                'id'            => wp_generate_uuid4(),
            );

            // 비동기 실행
            if ( $meta['async'] ) {
                $this->schedule_async_event( $event_obj );
                unset( $this->publishing[ $event ] );

                return array(
                    'success'   => true,
                    'async'     => true,
                    'event_id'  => $event_obj['id'],
                );
            }

            // 영속 저장
            if ( $meta['persist'] ) {
                $this->persist_event( $event_obj );
            }

            // 히스토리 기록
            $this->record_history( $event_obj );

            // WordPress action 실행
            $results = array();
            $executed = 0;

            /**
             * 이벤트 발행 전 필터
             *
             * @since 1.0.0
             * @param array  $event_obj 이벤트 객체
             * @param string $event     이벤트 이름
             */
            $event_obj = apply_filters( '3j_labs_before_event', $event_obj, $event );

            if ( $event_obj !== false ) {
                do_action( self::EVENT_PREFIX . $event, $event_obj['data'], $event_obj );
                $executed = did_action( self::EVENT_PREFIX . $event );
            }

            /**
             * 이벤트 발행 후 액션
             *
             * @since 1.0.0
             * @param array  $event_obj 이벤트 객체
             * @param int    $executed  실행된 리스너 수
             */
            do_action( '3j_labs_after_event', $event_obj, $executed );

            unset( $this->publishing[ $event ] );

            return array(
                'success'    => true,
                'event_id'   => $event_obj['id'],
                'listeners'  => $executed,
                'timestamp'  => $event_obj['timestamp'],
            );
        }

        /**
         * 한 번만 실행되는 이벤트 구독 (편의 메서드)
         *
         * @since 1.0.0
         * @param string   $event    이벤트 이름
         * @param callable $callback 콜백 함수
         * @param array    $options  옵션
         * @return string 구독 ID
         */
        public function once( $event, $callback, $options = array() ) {
            $options['once'] = true;
            return $this->subscribe( $event, $callback, $options );
        }

        /**
         * 특정 플러그인의 이벤트만 구독 (편의 메서드)
         *
         * @since 1.0.0
         * @param string   $source_plugin 소스 플러그인 슬러그
         * @param string   $event         이벤트 이름
         * @param callable $callback      콜백 함수
         * @param array    $options       추가 옵션
         * @return string 구독 ID
         */
        public function from( $source_plugin, $event, $callback, $options = array() ) {
            $options['source_plugin'] = $source_plugin;
            return $this->subscribe( $event, $callback, $options );
        }

        /**
         * 콜백 래퍼 생성
         *
         * @since 1.0.0
         * @param string $event         이벤트 이름
         * @param string $subscriber_id 구독 ID
         * @return callable
         */
        private function create_callback_wrapper( $event, $subscriber_id ) {
            $self = $this;

            return function( $data, $event_obj ) use ( $self, $event, $subscriber_id ) {
                if ( ! isset( $self->subscribers[ $event ][ $subscriber_id ] ) ) {
                    return;
                }

                $subscriber = $self->subscribers[ $event ][ $subscriber_id ];
                $options    = $subscriber['options'];
                $callback   = $subscriber['callback'];

                // 소스 플러그인 필터
                if ( ! empty( $options['source_plugin'] ) ) {
                    if ( $event_obj['meta']['source_plugin'] !== $options['source_plugin'] ) {
                        return;
                    }
                }

                // 커스텀 필터 조건
                if ( ! empty( $options['filter'] ) && is_callable( $options['filter'] ) ) {
                    if ( ! call_user_func( $options['filter'], $data, $event_obj ) ) {
                        return;
                    }
                }

                // 콜백 실행
                try {
                    call_user_func( $callback, $data, $event_obj );
                } catch ( Exception $e ) {
                    if ( $self->debug_mode ) {
                        error_log( sprintf(
                            '[3J Event Bus] Error in subscriber %s for event %s: %s',
                            $subscriber_id,
                            $event,
                            $e->getMessage()
                        ));
                    }
                }

                // once 옵션 처리
                if ( $options['once'] ) {
                    $self->unsubscribe( $event, $subscriber_id );
                }
            };
        }

        /**
         * 구독 ID 생성
         *
         * @since 1.0.0
         * @param string   $event    이벤트 이름
         * @param callable $callback 콜백
         * @return string
         */
        private function generate_subscriber_id( $event, $callback ) {
            if ( is_array( $callback ) ) {
                if ( is_object( $callback[0] ) ) {
                    $id = spl_object_hash( $callback[0] ) . '::' . $callback[1];
                } else {
                    $id = $callback[0] . '::' . $callback[1];
                }
            } elseif ( is_object( $callback ) ) {
                $id = spl_object_hash( $callback );
            } else {
                $id = (string) $callback;
            }

            return md5( $event . '_' . $id . '_' . uniqid() );
        }

        /**
         * 소스 플러그인 자동 감지
         *
         * @since 1.0.0
         * @return string|null
         */
        private function detect_source_plugin() {
            $backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 10 );

            foreach ( $backtrace as $trace ) {
                if ( ! isset( $trace['file'] ) ) {
                    continue;
                }

                // 플러그인 디렉토리에서 실행되었는지 확인
                if ( strpos( $trace['file'], WP_PLUGIN_DIR ) !== false ) {
                    $relative = str_replace( WP_PLUGIN_DIR . '/', '', $trace['file'] );
                    $parts    = explode( '/', $relative );

                    if ( ! empty( $parts[0] ) && $parts[0] !== 'shared-ui-assets' ) {
                        return $parts[0];
                    }
                }
            }

            return null;
        }

        /**
         * 비동기 이벤트 스케줄
         *
         * @since 1.0.0
         * @param array $event_obj 이벤트 객체
         */
        private function schedule_async_event( $event_obj ) {
            if ( ! wp_next_scheduled( '3j_labs_async_event', array( $event_obj ) ) ) {
                wp_schedule_single_event( time(), '3j_labs_async_event', array( $event_obj ) );
            }
        }

        /**
         * 이벤트 영속 저장
         *
         * @since 1.0.0
         * @param array $event_obj 이벤트 객체
         */
        private function persist_event( $event_obj ) {
            $persisted = get_option( '3j_labs_persisted_events', array() );
            $persisted[] = $event_obj;

            // 최대 1000개 유지
            if ( count( $persisted ) > 1000 ) {
                $persisted = array_slice( $persisted, -1000 );
            }

            update_option( '3j_labs_persisted_events', $persisted );
        }

        /**
         * 히스토리 기록
         *
         * @since 1.0.0
         * @param array $event_obj 이벤트 객체
         */
        private function record_history( $event_obj ) {
            $this->event_history[] = array(
                'id'        => $event_obj['id'],
                'name'      => $event_obj['name'],
                'source'    => $event_obj['meta']['source_plugin'],
                'timestamp' => $event_obj['timestamp'],
            );

            // 제한 초과 시 오래된 항목 제거
            if ( count( $this->event_history ) > $this->history_limit ) {
                array_shift( $this->event_history );
            }
        }

        /**
         * REST API 라우트 등록
         *
         * @since 1.0.0
         */
        public function register_rest_routes() {
            // GET /events/subscribers - 구독자 목록
            register_rest_route(
                '3j-labs/v1',
                '/events/subscribers',
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'rest_get_subscribers' ),
                    'permission_callback' => function() {
                        return current_user_can( 'manage_options' );
                    },
                )
            );

            // GET /events/history - 이벤트 히스토리
            register_rest_route(
                '3j-labs/v1',
                '/events/history',
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'rest_get_history' ),
                    'permission_callback' => function() {
                        return current_user_can( 'manage_options' );
                    },
                )
            );

            // POST /events/publish - 이벤트 발행
            register_rest_route(
                '3j-labs/v1',
                '/events/publish',
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'rest_publish_event' ),
                    'permission_callback' => function() {
                        return current_user_can( 'manage_options' );
                    },
                    'args'                => array(
                        'event' => array(
                            'required'          => true,
                            'type'              => 'string',
                            'sanitize_callback' => 'sanitize_key',
                        ),
                        'data' => array(
                            'type'    => 'object',
                            'default' => array(),
                        ),
                    ),
                )
            );
        }

        /**
         * REST: 구독자 목록 조회
         *
         * @since 1.0.0
         * @return WP_REST_Response
         */
        public function rest_get_subscribers() {
            $result = array();

            foreach ( $this->subscribers as $event => $subs ) {
                $result[ $event ] = array(
                    'count'       => count( $subs ),
                    'subscribers' => array_keys( $subs ),
                );
            }

            return new WP_REST_Response( array(
                'success' => true,
                'data'    => $result,
            ), 200 );
        }

        /**
         * REST: 이벤트 히스토리 조회
         *
         * @since 1.0.0
         * @param WP_REST_Request $request 요청 객체
         * @return WP_REST_Response
         */
        public function rest_get_history( $request ) {
            $limit = $request->get_param( 'limit' );
            $limit = $limit ? intval( $limit ) : 50;

            $history = array_slice( array_reverse( $this->event_history ), 0, $limit );

            return new WP_REST_Response( array(
                'success' => true,
                'data'    => $history,
                'meta'    => array(
                    'total' => count( $this->event_history ),
                    'limit' => $limit,
                ),
            ), 200 );
        }

        /**
         * REST: 이벤트 발행
         *
         * @since 1.0.0
         * @param WP_REST_Request $request 요청 객체
         * @return WP_REST_Response
         */
        public function rest_publish_event( $request ) {
            $event = $request->get_param( 'event' );
            $data  = $request->get_param( 'data' );

            $result = $this->publish( $event, $data, array(
                'source_plugin' => 'rest-api',
            ));

            $status = $result['success'] ? 200 : 400;

            return new WP_REST_Response( $result, $status );
        }

        /**
         * 특정 이벤트의 구독자 수 조회
         *
         * @since 1.0.0
         * @param string $event 이벤트 이름
         * @return int
         */
        public function get_subscriber_count( $event ) {
            if ( ! isset( $this->subscribers[ $event ] ) ) {
                return 0;
            }
            return count( $this->subscribers[ $event ] );
        }

        /**
         * 이벤트 히스토리 조회
         *
         * @since 1.0.0
         * @param int $limit 조회 개수
         * @return array
         */
        public function get_history( $limit = 50 ) {
            return array_slice( array_reverse( $this->event_history ), 0, $limit );
        }

        /**
         * 등록된 모든 이벤트 목록 조회
         *
         * @since 1.0.0
         * @return array
         */
        public function get_registered_events() {
            return array_keys( $this->subscribers );
        }

        /**
         * 이벤트 버스 통계
         *
         * @since 1.0.0
         * @return array
         */
        public function get_stats() {
            $total_subscribers = 0;
            foreach ( $this->subscribers as $subs ) {
                $total_subscribers += count( $subs );
            }

            return array(
                'events'           => count( $this->subscribers ),
                'total_subscribers' => $total_subscribers,
                'history_size'     => count( $this->event_history ),
                'history_limit'    => $this->history_limit,
            );
        }

        /**
         * 히스토리 초기화
         *
         * @since 1.0.0
         */
        public function clear_history() {
            $this->event_history = array();
        }

        /**
         * 디버그 모드 설정
         *
         * @since 1.0.0
         * @param bool $enabled 활성화 여부
         */
        public function set_debug_mode( $enabled ) {
            $this->debug_mode = (bool) $enabled;
        }
    }
}

// 비동기 이벤트 핸들러 등록
add_action( '3j_labs_async_event', function( $event_obj ) {
    do_action( JJ_3J_Event_Bus::EVENT_PREFIX . $event_obj['name'], $event_obj['data'], $event_obj );
});

// 편의 함수
if ( ! function_exists( 'jj_event_bus' ) ) {
    /**
     * 이벤트 버스 인스턴스 반환
     *
     * @since 1.0.0
     * @return JJ_3J_Event_Bus
     */
    function jj_event_bus() {
        return JJ_3J_Event_Bus::instance();
    }
}

if ( ! function_exists( 'jj_publish' ) ) {
    /**
     * 이벤트 발행 (편의 함수)
     *
     * @since 1.0.0
     * @param string $event 이벤트 이름
     * @param array  $data  이벤트 데이터
     * @param array  $meta  메타데이터
     * @return array
     */
    function jj_publish( $event, $data = array(), $meta = array() ) {
        return JJ_3J_Event_Bus::instance()->publish( $event, $data, $meta );
    }
}

if ( ! function_exists( 'jj_subscribe' ) ) {
    /**
     * 이벤트 구독 (편의 함수)
     *
     * @since 1.0.0
     * @param string   $event    이벤트 이름
     * @param callable $callback 콜백 함수
     * @param array    $options  옵션
     * @return string 구독 ID
     */
    function jj_subscribe( $event, $callback, $options = array() ) {
        return JJ_3J_Event_Bus::instance()->subscribe( $event, $callback, $options );
    }
}
