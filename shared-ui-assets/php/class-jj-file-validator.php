<?php
/**
 * JJ File Validator - 파일 업로드 검증 유틸리티
 *
 * 모든 3J Labs 플러그인에서 사용하는 파일 업로드 검증 헬퍼 클래스입니다.
 * ZIP 파일 검증, MIME 타입 확인, 보안 검사 등을 통합 제공합니다.
 *
 * @package    3J_Labs_Shared
 * @subpackage Utilities
 * @since      1.0.0
 * @author     3J Labs (제이x제니x제이슨 연구소)
 * @link       https://3j-labs.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_File_Validator' ) ) {

    /**
     * 파일 검증 헬퍼 클래스
     *
     * @since 1.0.0
     */
    class JJ_File_Validator {

        /**
         * 싱글톤 인스턴스
         *
         * @var JJ_File_Validator|null
         */
        private static $instance = null;

        /**
         * 허용된 MIME 타입
         *
         * @var array
         */
        private $allowed_mime_types = array(
            'zip' => array(
                'application/zip',
                'application/x-zip',
                'application/x-zip-compressed',
                'application/octet-stream',
            ),
        );

        /**
         * 최대 파일 크기 (바이트)
         *
         * @var int
         */
        private $max_file_size = 104857600; // 100MB

        /**
         * 싱글톤 인스턴스 반환
         *
         * @since 1.0.0
         * @return JJ_File_Validator
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * 최대 파일 크기 설정
         *
         * @since 1.0.0
         * @param int $bytes 바이트 단위 최대 크기
         * @return $this
         */
        public function set_max_file_size( $bytes ) {
            $this->max_file_size = absint( $bytes );
            return $this;
        }

        /**
         * ZIP 파일 검증
         *
         * 업로드된 파일이 유효한 ZIP 파일인지 확인합니다.
         *
         * @since 1.0.0
         *
         * @param array $file $_FILES 배열의 단일 파일 항목
         * @return array|WP_Error 검증 결과 또는 에러
         */
        public function validate_zip( $file ) {
            // 1. 기본 파일 존재 확인
            if ( empty( $file ) || ! isset( $file['tmp_name'] ) || empty( $file['tmp_name'] ) ) {
                return new WP_Error(
                    'no_file',
                    __( '업로드된 파일이 없습니다.', '3j-labs' )
                );
            }

            // 2. 업로드 에러 확인
            if ( isset( $file['error'] ) && UPLOAD_ERR_OK !== $file['error'] ) {
                return new WP_Error(
                    'upload_error',
                    $this->get_upload_error_message( $file['error'] )
                );
            }

            // 3. 파일 확장자 확인
            $extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
            if ( 'zip' !== $extension ) {
                return new WP_Error(
                    'invalid_extension',
                    __( 'ZIP 파일만 업로드할 수 있습니다.', '3j-labs' )
                );
            }

            // 4. 파일 크기 확인
            if ( $file['size'] > $this->max_file_size ) {
                return new WP_Error(
                    'file_too_large',
                    sprintf(
                        /* translators: %s: 최대 파일 크기 */
                        __( '파일 크기가 최대 허용 크기(%s)를 초과합니다.', '3j-labs' ),
                        size_format( $this->max_file_size )
                    )
                );
            }

            // 5. MIME 타입 확인
            $mime_type = $this->get_mime_type( $file['tmp_name'] );
            if ( ! in_array( $mime_type, $this->allowed_mime_types['zip'], true ) ) {
                return new WP_Error(
                    'invalid_mime',
                    __( '유효하지 않은 파일 형식입니다.', '3j-labs' )
                );
            }

            // 6. 실제 ZIP 파일 구조 확인
            if ( ! $this->is_valid_zip_structure( $file['tmp_name'] ) ) {
                return new WP_Error(
                    'invalid_zip',
                    __( '손상된 ZIP 파일입니다.', '3j-labs' )
                );
            }

            return array(
                'valid'     => true,
                'name'      => sanitize_file_name( $file['name'] ),
                'size'      => $file['size'],
                'mime_type' => $mime_type,
                'tmp_name'  => $file['tmp_name'],
            );
        }

        /**
         * WordPress 플러그인/테마 타입 감지
         *
         * ZIP 파일 내부를 분석하여 플러그인인지 테마인지 판별합니다.
         *
         * @since 1.0.0
         *
         * @param string $zip_path ZIP 파일 경로
         * @return string 'plugin', 'theme', 또는 'unknown'
         */
        public function detect_package_type( $zip_path ) {
            if ( ! class_exists( 'ZipArchive' ) ) {
                return 'unknown';
            }

            $zip = new ZipArchive();
            if ( true !== $zip->open( $zip_path ) ) {
                return 'unknown';
            }

            $type = 'unknown';

            for ( $i = 0; $i < $zip->numFiles; $i++ ) {
                $filename = $zip->getNameIndex( $i );

                // 테마 감지: style.css with Theme Name
                if ( preg_match( '/^[^\/]+\/style\.css$/', $filename ) ) {
                    $content = $zip->getFromIndex( $i );
                    if ( false !== strpos( $content, 'Theme Name:' ) ) {
                        $type = 'theme';
                        break;
                    }
                }

                // 플러그인 감지: PHP 파일 with Plugin Name
                if ( preg_match( '/^[^\/]+\/[^\/]+\.php$/', $filename ) ) {
                    $content = $zip->getFromIndex( $i );
                    if ( false !== strpos( $content, 'Plugin Name:' ) ) {
                        $type = 'plugin';
                        break;
                    }
                }
            }

            $zip->close();
            return $type;
        }

        /**
         * 파일 MIME 타입 가져오기
         *
         * @since 1.0.0
         *
         * @param string $file_path 파일 경로
         * @return string MIME 타입
         */
        private function get_mime_type( $file_path ) {
            // finfo 사용 (권장)
            if ( function_exists( 'finfo_open' ) ) {
                $finfo = finfo_open( FILEINFO_MIME_TYPE );
                $mime  = finfo_file( $finfo, $file_path );
                finfo_close( $finfo );
                return $mime;
            }

            // mime_content_type 폴백
            if ( function_exists( 'mime_content_type' ) ) {
                return mime_content_type( $file_path );
            }

            // WordPress 함수 폴백
            $filetype = wp_check_filetype( $file_path );
            return $filetype['type'] ?? 'application/octet-stream';
        }

        /**
         * ZIP 파일 구조 유효성 확인
         *
         * @since 1.0.0
         *
         * @param string $file_path ZIP 파일 경로
         * @return bool 유효한 ZIP 파일 여부
         */
        private function is_valid_zip_structure( $file_path ) {
            if ( ! class_exists( 'ZipArchive' ) ) {
                // ZipArchive가 없으면 매직 바이트로 확인
                $handle = fopen( $file_path, 'rb' );
                if ( false === $handle ) {
                    return false;
                }
                $magic = fread( $handle, 4 );
                fclose( $handle );

                // ZIP 매직 바이트: PK\x03\x04
                return 'PK' === substr( $magic, 0, 2 );
            }

            $zip = new ZipArchive();
            $result = $zip->open( $file_path, ZipArchive::CHECKCONS );

            if ( true === $result ) {
                $zip->close();
                return true;
            }

            return false;
        }

        /**
         * PHP 업로드 에러 메시지 반환
         *
         * @since 1.0.0
         *
         * @param int $error_code PHP 업로드 에러 코드
         * @return string 에러 메시지
         */
        private function get_upload_error_message( $error_code ) {
            $messages = array(
                UPLOAD_ERR_INI_SIZE   => __( '파일이 서버의 최대 업로드 크기를 초과합니다.', '3j-labs' ),
                UPLOAD_ERR_FORM_SIZE  => __( '파일이 폼의 최대 크기를 초과합니다.', '3j-labs' ),
                UPLOAD_ERR_PARTIAL    => __( '파일이 일부만 업로드되었습니다.', '3j-labs' ),
                UPLOAD_ERR_NO_FILE    => __( '업로드된 파일이 없습니다.', '3j-labs' ),
                UPLOAD_ERR_NO_TMP_DIR => __( '임시 폴더가 없습니다.', '3j-labs' ),
                UPLOAD_ERR_CANT_WRITE => __( '디스크에 쓸 수 없습니다.', '3j-labs' ),
                UPLOAD_ERR_EXTENSION  => __( 'PHP 확장에 의해 업로드가 중단되었습니다.', '3j-labs' ),
            );

            return $messages[ $error_code ] ?? __( '알 수 없는 업로드 오류가 발생했습니다.', '3j-labs' );
        }
    }
}
