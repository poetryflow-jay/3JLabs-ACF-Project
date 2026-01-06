<?php
/**
 * 3J Labs Plugin Registry - 플러그인 레지스트리 시스템
 *
 * 모든 3J Labs 플러그인의 상태를 중앙에서 관리하고 추적합니다.
 * 플러그인 간 상호 인식 및 의존성 관리를 제공합니다.
 *
 * 사용법:
 * ```php
 * // 플러그인 등록
 * JJ_3J_Plugin_Registry::instance()->register( 'my-plugin', array(
 *     'name'        => 'My Plugin',
 *     'version'     => '1.0.0',
 *     'file'        => __FILE__,
 *     'category'    => 'utility',
 *     'provides'    => array( 'email', 'notifications' ),
 *     'requires'    => array( 'acf-css-master' ),
 * ));
 *
 * // 플러그인 상태 확인
 * if ( JJ_3J_Plugin_Registry::instance()->is_active( 'acf-css-neural-link' ) ) {
 *     // Neural Link 기능 사용
 * }
 *
 * // 기능 제공자 찾기
 * $email_plugins = JJ_3J_Plugin_Registry::instance()->get_providers( 'email' );
 * ```
 *
 * @package    3J_Labs_Shared
 * @subpackage Plugin_Registry
 * @since      1.0.0
 * @version    1.0.0
 * @author     3J Labs (제이x제니x제이슨 연구소)
 * @link       https://3j-labs.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_3J_Plugin_Registry' ) ) {

    /**
     * 플러그인 레지스트리 클래스
     *
     * @since 1.0.0
     */
    class JJ_3J_Plugin_Registry {

        /**
         * 싱글톤 인스턴스
         *
         * @var JJ_3J_Plugin_Registry|null
         */
        private static $instance = null;

        /**
         * 등록된 플러그인 목록
         *
         * @var array
         */
        private $plugins = array();

        /**
         * 기능별 제공자 맵
         *
         * @var array
         */
        private $capability_map = array();

        /**
         * 의존성 그래프
         *
         * @var array
         */
        private $dependency_graph = array();

        /**
         * 초기화 완료 플래그
         *
         * @var bool
         */
        private $initialized = false;

        /**
         * 싱글톤 인스턴스 반환
         *
         * @since 1.0.0
         * @return JJ_3J_Plugin_Registry
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
            add_action( 'plugins_loaded', array( $this, 'finalize_registration' ), 99 );
            add_action( 'admin_init', array( $this, 'check_dependencies' ) );
        }

        /**
         * 플러그인 등록
         *
         * @since 1.0.0
         * @param string $slug 플러그인 슬러그 (고유 식별자)
         * @param array  $data 플러그인 데이터
         * @return bool 등록 성공 여부
         */
        public function register( $slug, $data ) {
            if ( empty( $slug ) || ! is_array( $data ) ) {
                return false;
            }

            // 기본값 설정
            $defaults = array(
                'slug'         => $slug,
                'name'         => $slug,
                'version'      => '1.0.0',
                'file'         => '',
                'category'     => 'other',
                'description'  => '',
                'provides'     => array(),  // 제공하는 기능
                'requires'     => array(),  // 필수 의존성
                'suggests'     => array(),  // 선택적 의존성
                'conflicts'    => array(),  // 충돌 플러그인
                'priority'     => 10,       // 로드 우선순위
                'status'       => 'active',
                'registered_at' => time(),
            );

            $plugin = wp_parse_args( $data, $defaults );

            // 플러그인 등록
            $this->plugins[ $slug ] = $plugin;

            // 기능 맵 업데이트
            if ( ! empty( $plugin['provides'] ) ) {
                foreach ( (array) $plugin['provides'] as $capability ) {
                    if ( ! isset( $this->capability_map[ $capability ] ) ) {
                        $this->capability_map[ $capability ] = array();
                    }
                    $this->capability_map[ $capability ][] = $slug;
                }
            }

            // 의존성 그래프 업데이트
            if ( ! empty( $plugin['requires'] ) ) {
                $this->dependency_graph[ $slug ] = (array) $plugin['requires'];
            }

            /**
             * 플러그인 등록 시 액션
             *
             * @since 1.0.0
             * @param string $slug   플러그인 슬러그
             * @param array  $plugin 플러그인 데이터
             */
            do_action( '3j_labs_plugin_registered', $slug, $plugin );

            return true;
        }

        /**
         * 플러그인 등록 해제
         *
         * @since 1.0.0
         * @param string $slug 플러그인 슬러그
         * @return bool
         */
        public function unregister( $slug ) {
            if ( ! isset( $this->plugins[ $slug ] ) ) {
                return false;
            }

            $plugin = $this->plugins[ $slug ];

            // 기능 맵에서 제거
            if ( ! empty( $plugin['provides'] ) ) {
                foreach ( (array) $plugin['provides'] as $capability ) {
                    if ( isset( $this->capability_map[ $capability ] ) ) {
                        $key = array_search( $slug, $this->capability_map[ $capability ], true );
                        if ( $key !== false ) {
                            unset( $this->capability_map[ $capability ][ $key ] );
                        }
                    }
                }
            }

            // 의존성 그래프에서 제거
            unset( $this->dependency_graph[ $slug ] );

            // 플러그인 목록에서 제거
            unset( $this->plugins[ $slug ] );

            /**
             * 플러그인 등록 해제 시 액션
             *
             * @since 1.0.0
             * @param string $slug 플러그인 슬러그
             */
            do_action( '3j_labs_plugin_unregistered', $slug );

            return true;
        }

        /**
         * 등록 완료 처리
         *
         * @since 1.0.0
         */
        public function finalize_registration() {
            if ( $this->initialized ) {
                return;
            }

            // 우선순위별 정렬
            uasort( $this->plugins, function( $a, $b ) {
                return $a['priority'] - $b['priority'];
            });

            $this->initialized = true;

            /**
             * 레지스트리 초기화 완료 액션
             *
             * @since 1.0.0
             * @param array $plugins 등록된 플러그인 목록
             */
            do_action( '3j_labs_registry_initialized', $this->plugins );
        }

        /**
         * 의존성 검사
         *
         * @since 1.0.0
         */
        public function check_dependencies() {
            $issues = array();

            foreach ( $this->dependency_graph as $plugin => $dependencies ) {
                foreach ( $dependencies as $required ) {
                    if ( ! $this->is_registered( $required ) ) {
                        $issues[] = array(
                            'plugin'   => $plugin,
                            'requires' => $required,
                            'type'     => 'missing',
                            'message'  => sprintf(
                                '%s 플러그인이 %s를 필요로 하지만 설치되어 있지 않습니다.',
                                $this->get_name( $plugin ),
                                $required
                            ),
                        );
                    } elseif ( ! $this->is_active( $required ) ) {
                        $issues[] = array(
                            'plugin'   => $plugin,
                            'requires' => $required,
                            'type'     => 'inactive',
                            'message'  => sprintf(
                                '%s 플러그인이 %s를 필요로 하지만 비활성화되어 있습니다.',
                                $this->get_name( $plugin ),
                                $this->get_name( $required )
                            ),
                        );
                    }
                }
            }

            // 충돌 검사
            foreach ( $this->plugins as $slug => $plugin ) {
                if ( empty( $plugin['conflicts'] ) ) {
                    continue;
                }

                foreach ( (array) $plugin['conflicts'] as $conflict ) {
                    if ( $this->is_active( $conflict ) ) {
                        $issues[] = array(
                            'plugin'    => $slug,
                            'conflicts' => $conflict,
                            'type'      => 'conflict',
                            'message'   => sprintf(
                                '%s와 %s가 충돌합니다.',
                                $this->get_name( $slug ),
                                $this->get_name( $conflict )
                            ),
                        );
                    }
                }
            }

            if ( ! empty( $issues ) ) {
                /**
                 * 의존성 문제 발견 시 액션
                 *
                 * @since 1.0.0
                 * @param array $issues 발견된 문제 목록
                 */
                do_action( '3j_labs_dependency_issues', $issues );

                // 관리자 알림 추가
                add_action( 'admin_notices', function() use ( $issues ) {
                    foreach ( $issues as $issue ) {
                        $class = $issue['type'] === 'conflict' ? 'notice-error' : 'notice-warning';
                        printf(
                            '<div class="notice %s is-dismissible"><p><strong>3J Labs:</strong> %s</p></div>',
                            esc_attr( $class ),
                            esc_html( $issue['message'] )
                        );
                    }
                });
            }

            return $issues;
        }

        /**
         * 플러그인이 등록되어 있는지 확인
         *
         * @since 1.0.0
         * @param string $slug 플러그인 슬러그
         * @return bool
         */
        public function is_registered( $slug ) {
            return isset( $this->plugins[ $slug ] );
        }

        /**
         * 플러그인이 활성화되어 있는지 확인
         *
         * @since 1.0.0
         * @param string $slug 플러그인 슬러그
         * @return bool
         */
        public function is_active( $slug ) {
            return isset( $this->plugins[ $slug ] ) && $this->plugins[ $slug ]['status'] === 'active';
        }

        /**
         * 플러그인 정보 조회
         *
         * @since 1.0.0
         * @param string $slug 플러그인 슬러그
         * @return array|null
         */
        public function get( $slug ) {
            return isset( $this->plugins[ $slug ] ) ? $this->plugins[ $slug ] : null;
        }

        /**
         * 플러그인 이름 조회
         *
         * @since 1.0.0
         * @param string $slug 플러그인 슬러그
         * @return string
         */
        public function get_name( $slug ) {
            if ( isset( $this->plugins[ $slug ] ) ) {
                return $this->plugins[ $slug ]['name'];
            }
            return $slug;
        }

        /**
         * 플러그인 버전 조회
         *
         * @since 1.0.0
         * @param string $slug 플러그인 슬러그
         * @return string|null
         */
        public function get_version( $slug ) {
            if ( isset( $this->plugins[ $slug ] ) ) {
                return $this->plugins[ $slug ]['version'];
            }
            return null;
        }

        /**
         * 모든 플러그인 목록 조회
         *
         * @since 1.0.0
         * @param string|null $category 카테고리 필터 (선택)
         * @return array
         */
        public function get_all( $category = null ) {
            if ( is_null( $category ) ) {
                return $this->plugins;
            }

            return array_filter( $this->plugins, function( $plugin ) use ( $category ) {
                return $plugin['category'] === $category;
            });
        }

        /**
         * 활성화된 플러그인만 조회
         *
         * @since 1.0.0
         * @return array
         */
        public function get_active() {
            return array_filter( $this->plugins, function( $plugin ) {
                return $plugin['status'] === 'active';
            });
        }

        /**
         * 특정 기능을 제공하는 플러그인 조회
         *
         * @since 1.0.0
         * @param string $capability 기능 이름
         * @return array 제공자 플러그인 슬러그 목록
         */
        public function get_providers( $capability ) {
            if ( ! isset( $this->capability_map[ $capability ] ) ) {
                return array();
            }

            // 활성화된 것만 반환
            return array_filter( $this->capability_map[ $capability ], function( $slug ) {
                return $this->is_active( $slug );
            });
        }

        /**
         * 특정 기능이 사용 가능한지 확인
         *
         * @since 1.0.0
         * @param string $capability 기능 이름
         * @return bool
         */
        public function has_capability( $capability ) {
            return count( $this->get_providers( $capability ) ) > 0;
        }

        /**
         * 플러그인 상태 업데이트
         *
         * @since 1.0.0
         * @param string $slug   플러그인 슬러그
         * @param string $status 새 상태 ('active', 'inactive', 'error')
         * @return bool
         */
        public function set_status( $slug, $status ) {
            if ( ! isset( $this->plugins[ $slug ] ) ) {
                return false;
            }

            $old_status = $this->plugins[ $slug ]['status'];
            $this->plugins[ $slug ]['status'] = $status;

            /**
             * 플러그인 상태 변경 시 액션
             *
             * @since 1.0.0
             * @param string $slug       플러그인 슬러그
             * @param string $new_status 새 상태
             * @param string $old_status 이전 상태
             */
            do_action( '3j_labs_plugin_status_changed', $slug, $status, $old_status );

            return true;
        }

        /**
         * 플러그인 메타 데이터 업데이트
         *
         * @since 1.0.0
         * @param string $slug 플러그인 슬러그
         * @param string $key  메타 키
         * @param mixed  $value 메타 값
         * @return bool
         */
        public function update_meta( $slug, $key, $value ) {
            if ( ! isset( $this->plugins[ $slug ] ) ) {
                return false;
            }

            if ( ! isset( $this->plugins[ $slug ]['meta'] ) ) {
                $this->plugins[ $slug ]['meta'] = array();
            }

            $this->plugins[ $slug ]['meta'][ $key ] = $value;

            return true;
        }

        /**
         * 플러그인 메타 데이터 조회
         *
         * @since 1.0.0
         * @param string $slug    플러그인 슬러그
         * @param string $key     메타 키
         * @param mixed  $default 기본값
         * @return mixed
         */
        public function get_meta( $slug, $key, $default = null ) {
            if ( ! isset( $this->plugins[ $slug ] ) ) {
                return $default;
            }

            if ( ! isset( $this->plugins[ $slug ]['meta'][ $key ] ) ) {
                return $default;
            }

            return $this->plugins[ $slug ]['meta'][ $key ];
        }

        /**
         * 의존성 그래프 조회
         *
         * @since 1.0.0
         * @return array
         */
        public function get_dependency_graph() {
            return $this->dependency_graph;
        }

        /**
         * 기능 맵 조회
         *
         * @since 1.0.0
         * @return array
         */
        public function get_capability_map() {
            return $this->capability_map;
        }

        /**
         * 레지스트리 통계 조회
         *
         * @since 1.0.0
         * @return array
         */
        public function get_stats() {
            $active = 0;
            $inactive = 0;
            $error = 0;
            $categories = array();

            foreach ( $this->plugins as $plugin ) {
                switch ( $plugin['status'] ) {
                    case 'active':
                        $active++;
                        break;
                    case 'inactive':
                        $inactive++;
                        break;
                    case 'error':
                        $error++;
                        break;
                }

                $cat = $plugin['category'];
                if ( ! isset( $categories[ $cat ] ) ) {
                    $categories[ $cat ] = 0;
                }
                $categories[ $cat ]++;
            }

            return array(
                'total'        => count( $this->plugins ),
                'active'       => $active,
                'inactive'     => $inactive,
                'error'        => $error,
                'categories'   => $categories,
                'capabilities' => count( $this->capability_map ),
            );
        }

        /**
         * 디버그용 덤프
         *
         * @since 1.0.0
         * @return array
         */
        public function dump() {
            return array(
                'plugins'        => $this->plugins,
                'capability_map' => $this->capability_map,
                'dependencies'   => $this->dependency_graph,
                'initialized'    => $this->initialized,
            );
        }
    }
}

// 편의 함수
if ( ! function_exists( 'jj_registry' ) ) {
    /**
     * 플러그인 레지스트리 인스턴스 반환
     *
     * @since 1.0.0
     * @return JJ_3J_Plugin_Registry
     */
    function jj_registry() {
        return JJ_3J_Plugin_Registry::instance();
    }
}

if ( ! function_exists( 'jj_plugin_active' ) ) {
    /**
     * 플러그인 활성화 상태 확인 (편의 함수)
     *
     * @since 1.0.0
     * @param string $slug 플러그인 슬러그
     * @return bool
     */
    function jj_plugin_active( $slug ) {
        return JJ_3J_Plugin_Registry::instance()->is_active( $slug );
    }
}

if ( ! function_exists( 'jj_has_capability' ) ) {
    /**
     * 기능 사용 가능 여부 확인 (편의 함수)
     *
     * @since 1.0.0
     * @param string $capability 기능 이름
     * @return bool
     */
    function jj_has_capability( $capability ) {
        return JJ_3J_Plugin_Registry::instance()->has_capability( $capability );
    }
}
