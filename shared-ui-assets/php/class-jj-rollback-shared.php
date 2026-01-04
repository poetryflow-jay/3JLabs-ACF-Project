<?php
/**
 * JJ Rollback Shared - 공유 롤백 관리 클래스
 *
 * 모든 3J Labs 플러그인에서 사용할 수 있는 롤백 기능을 제공합니다.
 * WordPress Core의 Plugin_Upgrader 클래스를 활용하여 안전하게 롤백을 수행합니다.
 *
 * @package    3J_Labs_Shared
 * @subpackage Rollback
 * @since      1.0.0
 * @author     3J Labs (제이x제니x제이슨 연구소)
 * @link       https://3j-labs.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_Rollback_Shared' ) ) {

    /**
     * 공유 롤백 관리 클래스
     *
     * @since 1.0.0
     */
    class JJ_Rollback_Shared {

        use JJ_Singleton_Trait;

        /**
         * 트레이트 로드 확인
         */
        private function __construct() {
            // JJ_Singleton_Trait 로드 확인
            if ( ! trait_exists( 'JJ_Singleton_Trait' ) ) {
                $trait_path = dirname( __FILE__ ) . '/trait-jj-singleton.php';
                if ( file_exists( $trait_path ) ) {
                    require_once $trait_path;
                }
            }

        /**
         * 롤백 히스토리 옵션 키
         *
         * @var string
         */
        const HISTORY_OPTION_KEY = 'jj_rollback_history';

        /**
         * 최대 히스토리 개수
         *
         * @var int
         */
        const MAX_HISTORY = 50;

        /**
         * 생성자
         *
         * @since 1.0.0
         */
        protected function __construct() {
            // WordPress Core 업데이트 클래스 로드
            if ( ! class_exists( 'Plugin_Upgrader' ) ) {
                require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
                require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }
        }

        /**
         * 플러그인 롤백 실행
         *
         * @since 1.0.0
         * @param string $plugin_basename 플러그인 베이스네임 (예: 'plugin-folder/plugin-file.php')
         * @param string $target_version 롤백할 버전 (선택사항, 없으면 이전 버전으로)
         * @return array|WP_Error 성공 시 롤백 정보, 실패 시 WP_Error
         */
        public function rollback_plugin( $plugin_basename, $target_version = '' ) {
            // 권한 확인
            if ( ! current_user_can( 'update_plugins' ) ) {
                return new WP_Error( 'no_permission', __( '플러그인 업데이트 권한이 없습니다.', 'jj-shared' ) );
            }

            // 플러그인 정보 가져오기
            $plugin_data = $this->get_plugin_data( $plugin_basename );
            if ( is_wp_error( $plugin_data ) ) {
                return $plugin_data;
            }

            $current_version = $plugin_data['Version'];
            $plugin_slug = dirname( $plugin_basename );

            // 롤백할 버전 결정
            if ( empty( $target_version ) ) {
                $target_version = $this->get_previous_version( $plugin_slug, $current_version );
                if ( empty( $target_version ) ) {
                    return new WP_Error( 'no_previous_version', __( '롤백할 이전 버전을 찾을 수 없습니다.', 'jj-shared' ) );
                }
            }

            // 현재 버전과 동일한 경우
            if ( $target_version === $current_version ) {
                return new WP_Error( 'same_version', __( '이미 해당 버전입니다.', 'jj-shared' ) );
            }

            // 롤백 패키지 URL 가져오기
            $package_url = $this->get_rollback_package_url( $plugin_slug, $target_version );
            if ( is_wp_error( $package_url ) ) {
                return $package_url;
            }

            // 롤백 실행
            $result = $this->perform_rollback( $plugin_basename, $package_url, $current_version, $target_version );

            // 히스토리 저장
            if ( ! is_wp_error( $result ) ) {
                $this->save_rollback_history( $plugin_basename, $current_version, $target_version, $result );
            }

            return $result;
        }

        /**
         * 플러그인 데이터 가져오기
         *
         * @since 1.0.0
         * @param string $plugin_basename 플러그인 베이스네임
         * @return array|WP_Error 플러그인 데이터 또는 에러
         */
        private function get_plugin_data( $plugin_basename ) {
            if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_basename ) ) {
                return new WP_Error( 'plugin_not_found', __( '플러그인을 찾을 수 없습니다.', 'jj-shared' ) );
            }

            $plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_basename );
            if ( empty( $plugin_data['Version'] ) ) {
                return new WP_Error( 'invalid_plugin', __( '유효하지 않은 플러그인입니다.', 'jj-shared' ) );
            }

            return $plugin_data;
        }

        /**
         * 이전 버전 가져오기
         *
         * @since 1.0.0
         * @param string $plugin_slug 플러그인 슬러그
         * @param string $current_version 현재 버전
         * @return string 이전 버전 또는 빈 문자열
         */
        private function get_previous_version( $plugin_slug, $current_version ) {
            // 1. 롤백 히스토리에서 찾기
            $history = $this->get_rollback_history( $plugin_slug );
            if ( ! empty( $history ) ) {
                // 최근 롤백 이전 버전 찾기
                foreach ( $history as $entry ) {
                    if ( $entry['from_version'] === $current_version ) {
                        return $entry['to_version'];
                    }
                }
            }

            // 2. package_signatures.json에서 찾기
            $versions = $this->get_available_versions( $plugin_slug );
            if ( ! empty( $versions ) ) {
                // 현재 버전보다 낮은 버전 중 최신 버전
                $previous = '';
                foreach ( $versions as $version ) {
                    if ( version_compare( $version, $current_version, '<' ) ) {
                        if ( empty( $previous ) || version_compare( $version, $previous, '>' ) ) {
                            $previous = $version;
                        }
                    }
                }
                return $previous;
            }

            // 3. WordPress.org에서 찾기 (3J Labs 플러그인이 아닌 경우)
            return $this->get_wordpress_org_previous_version( $plugin_slug, $current_version );
        }

        /**
         * 사용 가능한 버전 목록 가져오기
         *
         * @since 1.0.0
         * @param string $plugin_slug 플러그인 슬러그
         * @return array 버전 목록
         */
        private function get_available_versions( $plugin_slug ) {
            $versions = array();

            // package_signatures.json 파일 찾기 (여러 경로 확인)
            $possible_paths = array(
                dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/dist/package_signatures.json',  // 프로젝트 루트/dist
                WP_CONTENT_DIR . '/../dist/package_signatures.json',  // WordPress 루트/dist
                WP_CONTENT_DIR . '/dist/package_signatures.json',  // wp-content/dist
            );

            $signatures_file = '';
            foreach ( $possible_paths as $path ) {
                if ( file_exists( $path ) ) {
                    $signatures_file = $path;
                    break;
                }
            }

            if ( ! empty( $signatures_file ) && file_exists( $signatures_file ) ) {
                $signatures_content = @file_get_contents( $signatures_file );
                if ( $signatures_content !== false ) {
                    $signatures = json_decode( $signatures_content, true );
                    if ( is_array( $signatures ) ) {
                        foreach ( $signatures as $filename => $data ) {
                            if ( ! is_array( $data ) || ! isset( $data['plugin_id'] ) ) {
                                continue;
                            }

                            // 플러그인 슬러그 매칭
                            $plugin_id = $data['plugin_id'];
                            if ( $this->match_plugin_slug( $plugin_slug, $plugin_id ) && isset( $data['version'] ) ) {
                                $versions[] = $data['version'];
                            }
                        }
                    }
                }
            }

            // 중복 제거 및 정렬
            $versions = array_unique( $versions );
            usort( $versions, 'version_compare' );

            return $versions;
        }

        /**
         * 플러그인 슬러그 매칭
         *
         * @since 1.0.0
         * @param string $plugin_slug 플러그인 슬러그
         * @param string $plugin_id 플러그인 ID
         * @return bool 매칭 여부
         */
        private function match_plugin_slug( $plugin_slug, $plugin_id ) {
            // 직접 매칭
            if ( $plugin_slug === $plugin_id ) {
                return true;
            }

            // 플러그인 ID에서 슬러그 추출
            $id_parts = explode( '-', $plugin_id );
            $slug_parts = explode( '-', $plugin_slug );

            // 일부 매칭 (예: 'acf-css-manager'와 'acf-css-really-simple-style-management-center-master')
            $common_parts = array_intersect( $id_parts, $slug_parts );
            if ( count( $common_parts ) >= 2 ) {
                return true;
            }

            return false;
        }

        /**
         * WordPress.org에서 이전 버전 가져오기
         *
         * @since 1.0.0
         * @param string $plugin_slug 플러그인 슬러그
         * @param string $current_version 현재 버전
         * @return string 이전 버전 또는 빈 문자열
         */
        private function get_wordpress_org_previous_version( $plugin_slug, $current_version ) {
            $api = plugins_api( 'plugin_information', array(
                'slug' => $plugin_slug,
                'fields' => array( 'versions' => true ),
            ) );

            if ( is_wp_error( $api ) || ! isset( $api->versions ) ) {
                return '';
            }

            $versions = array_keys( $api->versions );
            usort( $versions, 'version_compare' );

            // 현재 버전보다 낮은 버전 중 최신 버전
            $previous = '';
            foreach ( $versions as $version ) {
                if ( version_compare( $version, $current_version, '<' ) ) {
                    if ( empty( $previous ) || version_compare( $version, $previous, '>' ) ) {
                        $previous = $version;
                    }
                }
            }

            return $previous;
        }

        /**
         * 롤백 패키지 URL 가져오기
         *
         * @since 1.0.0
         * @param string $plugin_slug 플러그인 슬러그
         * @param string $version 버전
         * @return string|WP_Error 패키지 URL 또는 에러
         */
        private function get_rollback_package_url( $plugin_slug, $version ) {
            // 1. 로컬 dist 폴더에서 찾기
            $local_url = $this->get_local_package_url( $plugin_slug, $version );
            if ( ! empty( $local_url ) ) {
                return $local_url;
            }

            // 2. 3J Labs 업데이트 서버에서 가져오기
            $server_url = $this->get_server_package_url( $plugin_slug, $version );
            if ( ! is_wp_error( $server_url ) ) {
                return $server_url;
            }

            // 3. WordPress.org에서 가져오기
            return $this->get_wordpress_org_package_url( $plugin_slug, $version );
        }

        /**
         * 로컬 패키지 URL 가져오기
         *
         * @since 1.0.0
         * @param string $plugin_slug 플러그인 슬러그
         * @param string $version 버전
         * @return string 패키지 URL 또는 빈 문자열
         */
        private function get_local_package_url( $plugin_slug, $version ) {
            // 여러 가능한 경로 확인
            $possible_paths = array(
                dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/dist',  // 프로젝트 루트/dist
                WP_CONTENT_DIR . '/../dist',  // WordPress 루트/dist
                WP_CONTENT_DIR . '/dist',  // wp-content/dist
            );

            // 패키지 파일명 패턴
            $patterns = array(
                $plugin_slug . '-master-v' . $version . '.zip',
                $plugin_slug . '-v' . $version . '.zip',
                '*' . $plugin_slug . '*v' . $version . '.zip',
            );

            foreach ( $possible_paths as $dist_path ) {
                if ( ! is_dir( $dist_path ) ) {
                    continue;
                }

                foreach ( $patterns as $pattern ) {
                    $files = glob( $dist_path . '/' . $pattern );
                    if ( ! empty( $files ) && file_exists( $files[0] ) ) {
                        $filename = basename( $files[0] );
                        
                        // URL 생성
                        if ( strpos( $dist_path, WP_CONTENT_DIR ) === 0 ) {
                            $dist_url = content_url( str_replace( WP_CONTENT_DIR, '', $dist_path ) );
                        } else {
                            // 프로젝트 루트의 경우 직접 파일 경로 반환
                            return $files[0];
                        }
                        
                        return trailingslashit( $dist_url ) . $filename;
                    }
                }
            }

            return '';
        }

        /**
         * 서버 패키지 URL 가져오기
         *
         * @since 1.0.0
         * @param string $plugin_slug 플러그인 슬러그
         * @param string $version 버전
         * @return string|WP_Error 패키지 URL 또는 에러
         */
        private function get_server_package_url( $plugin_slug, $version ) {
            // Neural Link 업데이트 서버 사용
            if ( class_exists( 'ACF_CSS_Neural_Link_Update_Server' ) ) {
                $server = ACF_CSS_Neural_Link_Update_Server::instance();
                return $server->get_download_url( $plugin_slug, $version );
            }

            return new WP_Error( 'no_server', __( '업데이트 서버를 사용할 수 없습니다.', 'jj-shared' ) );
        }

        /**
         * WordPress.org 패키지 URL 가져오기
         *
         * @since 1.0.0
         * @param string $plugin_slug 플러그인 슬러그
         * @param string $version 버전
         * @return string|WP_Error 패키지 URL 또는 에러
         */
        private function get_wordpress_org_package_url( $plugin_slug, $version ) {
            $api = plugins_api( 'plugin_information', array(
                'slug' => $plugin_slug,
                'fields' => array( 'versions' => true ),
            ) );

            if ( is_wp_error( $api ) ) {
                return $api;
            }

            if ( ! isset( $api->versions[ $version ] ) ) {
                return new WP_Error( 'version_not_found', __( '해당 버전을 찾을 수 없습니다.', 'jj-shared' ) );
            }

            return $api->versions[ $version ];
        }

        /**
         * 롤백 실행
         *
         * @since 1.0.0
         * @param string $plugin_basename 플러그인 베이스네임
         * @param string $package_url 패키지 URL
         * @param string $from_version 현재 버전
         * @param string $to_version 롤백할 버전
         * @return array|WP_Error 롤백 결과
         */
        private function perform_rollback( $plugin_basename, $package_url, $from_version, $to_version ) {
            // 로컬 파일인 경우 직접 사용
            $temp_file = '';
            if ( file_exists( $package_url ) ) {
                $temp_file = $package_url;
            } else {
                // 원격 URL인 경우 다운로드
                $temp_file = download_url( $package_url );
                if ( is_wp_error( $temp_file ) ) {
                    return $temp_file;
                }
            }

            // 플러그인 상태 저장
            $was_active = is_plugin_active( $plugin_basename );
            $was_network_active = is_plugin_active_for_network( $plugin_basename );
            $plugin_slug = dirname( $plugin_basename );

            // 플러그인 디렉토리 백업
            $plugin_dir = WP_PLUGIN_DIR . '/' . $plugin_slug;
            $backup_dir = '';
            if ( is_dir( $plugin_dir ) ) {
                $backup_dir = WP_PLUGIN_DIR . '/' . $plugin_slug . '-backup-' . time();
                if ( ! $this->copy_directory( $plugin_dir, $backup_dir ) ) {
                    if ( ! file_exists( $package_url ) ) {
                        @unlink( $temp_file );
                    }
                    return new WP_Error( 'backup_failed', __( '플러그인 백업 생성에 실패했습니다.', 'jj-shared' ) );
                }
            }

            // 플러그인 비활성화
            if ( $was_active ) {
                deactivate_plugins( $plugin_basename, true );
            }

            // 업그레이더 인스턴스 생성
            $skin = new WP_Ajax_Upgrader_Skin();
            $upgrader = new Plugin_Upgrader( $skin );

            // 기존 플러그인 삭제
            if ( is_dir( $plugin_dir ) ) {
                $this->delete_directory( $plugin_dir );
            }

            // 롤백 실행
            $result = $upgrader->install( $temp_file, array(
                'clear_destination' => true,
                'clear_working' => true,
            ) );

            // 임시 파일 삭제 (다운로드한 경우만)
            if ( ! file_exists( $package_url ) && file_exists( $temp_file ) ) {
                @unlink( $temp_file );
            }

            if ( is_wp_error( $result ) || $result === false ) {
                // 실패 시 백업 복원
                if ( is_dir( $backup_dir ) ) {
                    $this->copy_directory( $backup_dir, $plugin_dir );
                    $this->delete_directory( $backup_dir );
                }
                // 플러그인 재활성화
                if ( $was_active ) {
                    if ( $was_network_active ) {
                        activate_plugin( $plugin_basename, '', true );
                    } else {
                        activate_plugin( $plugin_basename );
                    }
                }
                return is_wp_error( $result ) ? $result : new WP_Error( 'install_failed', __( '플러그인 설치에 실패했습니다.', 'jj-shared' ) );
            }

            // 백업 디렉토리 삭제
            if ( is_dir( $backup_dir ) ) {
                $this->delete_directory( $backup_dir );
            }

            // 플러그인 재활성화
            if ( $was_network_active ) {
                activate_plugin( $plugin_basename, '', true );
            } elseif ( $was_active ) {
                activate_plugin( $plugin_basename );
            }

            // 플러그인 정보 새로고침
            delete_plugins_cache();

            return array(
                'success' => true,
                'from_version' => $from_version,
                'to_version' => $to_version,
                'plugin' => $plugin_basename,
                'timestamp' => current_time( 'mysql' ),
            );
        }

        /**
         * 디렉토리 복사 (재귀)
         *
         * @since 1.0.0
         * @param string $source 소스 디렉토리
         * @param string $dest 대상 디렉토리
         * @return bool 성공 여부
         */
        private function copy_directory( $source, $dest ) {
            if ( ! is_dir( $source ) ) {
                return false;
            }

            if ( ! is_dir( $dest ) ) {
                wp_mkdir_p( $dest );
            }

            $dir = opendir( $source );
            if ( ! $dir ) {
                return false;
            }

            while ( ( $file = readdir( $dir ) ) !== false ) {
                if ( $file === '.' || $file === '..' ) {
                    continue;
                }

                $src_path = $source . '/' . $file;
                $dest_path = $dest . '/' . $file;

                if ( is_dir( $src_path ) ) {
                    $this->copy_directory( $src_path, $dest_path );
                } else {
                    copy( $src_path, $dest_path );
                }
            }

            closedir( $dir );
            return true;
        }

        /**
         * 디렉토리 삭제 (재귀)
         *
         * @since 1.0.0
         * @param string $dir 디렉토리 경로
         * @return bool 성공 여부
         */
        private function delete_directory( $dir ) {
            if ( ! is_dir( $dir ) ) {
                return false;
            }

            $files = array_diff( scandir( $dir ), array( '.', '..' ) );

            foreach ( $files as $file ) {
                $path = $dir . '/' . $file;
                if ( is_dir( $path ) ) {
                    $this->delete_directory( $path );
                } else {
                    @unlink( $path );
                }
            }

            return @rmdir( $dir );
        }

        /**
         * 롤백 히스토리 저장
         *
         * @since 1.0.0
         * @param string $plugin_basename 플러그인 베이스네임
         * @param string $from_version 현재 버전
         * @param string $to_version 롤백한 버전
         * @param array $result 롤백 결과
         */
        private function save_rollback_history( $plugin_basename, $from_version, $to_version, $result ) {
            $history = get_option( self::HISTORY_OPTION_KEY, array() );

            $entry = array(
                'plugin' => $plugin_basename,
                'from_version' => $from_version,
                'to_version' => $to_version,
                'timestamp' => current_time( 'mysql' ),
                'unix_timestamp' => time(),
                'user_id' => get_current_user_id(),
                'user_name' => wp_get_current_user()->display_name,
                'success' => isset( $result['success'] ) ? $result['success'] : false,
                'ip_address' => $this->get_client_ip(),
            );

            // 플러그인별 히스토리
            if ( ! isset( $history[ $plugin_basename ] ) ) {
                $history[ $plugin_basename ] = array();
            }

            array_unshift( $history[ $plugin_basename ], $entry );

            // 최대 개수 제한
            if ( count( $history[ $plugin_basename ] ) > self::MAX_HISTORY ) {
                $history[ $plugin_basename ] = array_slice( $history[ $plugin_basename ], 0, self::MAX_HISTORY );
            }

            update_option( self::HISTORY_OPTION_KEY, $history );
        }

        /**
         * 클라이언트 IP 주소 가져오기
         *
         * @since 1.0.0
         * @return string IP 주소
         */
        private function get_client_ip() {
            $ip_keys = array(
                'HTTP_CF_CONNECTING_IP', // Cloudflare
                'HTTP_X_REAL_IP',         // Nginx
                'HTTP_X_FORWARDED_FOR',   // Proxy
                'REMOTE_ADDR',             // 기본
            );

            foreach ( $ip_keys as $key ) {
                if ( ! empty( $_SERVER[ $key ] ) ) {
                    $ip = $_SERVER[ $key ];
                    if ( strpos( $ip, ',' ) !== false ) {
                        $ip = explode( ',', $ip );
                        $ip = trim( $ip[0] );
                    }
                    if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                        return $ip;
                    }
                }
            }

            return '0.0.0.0';
        }

        /**
         * 롤백 히스토리 가져오기
         *
         * @since 1.0.0
         * @param string $plugin_basename 플러그인 베이스네임 (선택사항)
         * @param int $limit 최대 개수 (선택사항)
         * @return array 롤백 히스토리
         */
        public function get_rollback_history( $plugin_basename = '', $limit = 0 ) {
            $history = get_option( self::HISTORY_OPTION_KEY, array() );

            if ( empty( $plugin_basename ) ) {
                return $history;
            }

            $plugin_history = isset( $history[ $plugin_basename ] ) ? $history[ $plugin_basename ] : array();

            if ( $limit > 0 && count( $plugin_history ) > $limit ) {
                $plugin_history = array_slice( $plugin_history, 0, $limit );
            }

            return $plugin_history;
        }

        /**
         * 롤백 히스토리 삭제
         *
         * @since 1.0.0
         * @param string $plugin_basename 플러그인 베이스네임 (선택사항, 없으면 전체 삭제)
         * @return bool 성공 여부
         */
        public function clear_rollback_history( $plugin_basename = '' ) {
            if ( empty( $plugin_basename ) ) {
                return delete_option( self::HISTORY_OPTION_KEY );
            }

            $history = get_option( self::HISTORY_OPTION_KEY, array() );
            if ( isset( $history[ $plugin_basename ] ) ) {
                unset( $history[ $plugin_basename ] );
                return update_option( self::HISTORY_OPTION_KEY, $history );
            }

            return true;
        }

        /**
         * 사용 가능한 롤백 버전 목록 가져오기
         *
         * @since 1.0.0
         * @param string $plugin_basename 플러그인 베이스네임
         * @return array 버전 목록 (버전 문자열 배열)
         */
        public function get_available_rollback_versions( $plugin_basename ) {
            $plugin_data = $this->get_plugin_data( $plugin_basename );
            if ( is_wp_error( $plugin_data ) ) {
                return array();
            }

            $current_version = $plugin_data['Version'];
            $plugin_slug = dirname( $plugin_basename );
            if ( empty( $plugin_slug ) || $plugin_slug === '.' ) {
                $plugin_slug = str_replace( '.php', '', basename( $plugin_basename ) );
            }

            $versions = $this->get_available_versions( $plugin_slug );
            $available = array();

            foreach ( $versions as $version ) {
                if ( version_compare( $version, $current_version, '<' ) ) {
                    // 패키지 URL 확인 (에러가 아니면 사용 가능)
                    $package_url = $this->get_rollback_package_url( $plugin_slug, $version );
                    if ( ! is_wp_error( $package_url ) ) {
                        $available[] = $version;
                    }
                }
            }

            // 버전 정렬 (최신 버전부터)
            usort( $available, function( $a, $b ) {
                return version_compare( $b, $a );
            } );

            return $available;
        }

        /**
         * 자동 롤백 트리거 확인
         *
         * @since 1.0.0
         * @param string $plugin_basename 플러그인 베이스네임
         * @param string $new_version 새 버전
         * @return bool 자동 롤백 필요 여부
         */
        public function should_auto_rollback( $plugin_basename, $new_version ) {
            // 자동 롤백이 비활성화된 경우
            $auto_rollback_enabled = get_option( 'jj_auto_rollback_enabled', false );
            if ( ! $auto_rollback_enabled ) {
                return false;
            }

            // 치명적 오류 발생 시
            if ( $this->has_fatal_error( $plugin_basename ) ) {
                return true;
            }

            // 특정 버전에서 알려진 문제가 있는 경우
            if ( $this->has_known_issues( $plugin_basename, $new_version ) ) {
                return true;
            }

            // 사용자 설정에 따라
            $auto_rollback_on_error = get_option( 'jj_auto_rollback_on_error', false );
            if ( $auto_rollback_on_error && $this->has_critical_error( $plugin_basename ) ) {
                return true;
            }

            // 최근 롤백 후 짧은 시간 내 다시 오류 발생 시
            if ( $this->has_recent_rollback_and_error( $plugin_basename ) ) {
                return true;
            }

            return false;
        }

        /**
         * 최근 롤백 후 오류 발생 확인
         *
         * @since 1.0.0
         * @param string $plugin_basename 플러그인 베이스네임
         * @return bool
         */
        private function has_recent_rollback_and_error( $plugin_basename ) {
            $history = $this->get_rollback_history( $plugin_basename, 1 );
            if ( empty( $history ) ) {
                return false;
            }

            $last_rollback = $history[0];
            $rollback_time = isset( $last_rollback['unix_timestamp'] ) ? $last_rollback['unix_timestamp'] : strtotime( $last_rollback['timestamp'] );
            $time_since_rollback = time() - $rollback_time;

            // 롤백 후 1시간 이내에 오류 발생 시
            if ( $time_since_rollback < 3600 && $this->has_critical_error( $plugin_basename ) ) {
                return true;
            }

            return false;
        }

        /**
         * 치명적 오류 확인
         *
         * @since 1.0.0
         * @param string $plugin_basename 플러그인 베이스네임
         * @return bool
         */
        private function has_fatal_error( $plugin_basename ) {
            // WordPress 오류 로그 확인
            $error_log = WP_CONTENT_DIR . '/debug.log';
            if ( file_exists( $error_log ) ) {
                $content = file_get_contents( $error_log );
                if ( strpos( $content, $plugin_basename ) !== false && strpos( $content, 'Fatal error' ) !== false ) {
                    return true;
                }
            }

            return false;
        }

        /**
         * 알려진 문제 확인
         *
         * @since 1.0.0
         * @param string $plugin_basename 플러그인 베이스네임
         * @param string $version 버전
         * @return bool
         */
        private function has_known_issues( $plugin_basename, $version ) {
            // 알려진 문제 버전 목록 (옵션으로 관리)
            $known_issues = get_option( 'jj_known_issue_versions', array() );
            return isset( $known_issues[ $plugin_basename ] ) && in_array( $version, $known_issues[ $plugin_basename ], true );
        }

        /**
         * 심각한 오류 확인
         *
         * @since 1.0.0
         * @param string $plugin_basename 플러그인 베이스네임
         * @return bool
         */
        private function has_critical_error( $plugin_basename ) {
            // 최근 5분 내 오류 발생 여부 확인
            $recent_errors = get_transient( 'jj_plugin_errors_' . md5( $plugin_basename ) );
            return ! empty( $recent_errors );
        }
    }
}
