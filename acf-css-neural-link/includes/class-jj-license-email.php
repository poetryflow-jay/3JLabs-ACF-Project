<?php
/**
 * 이메일 템플릿 클래스
 * 
 * @package JJ_LicenseManagerincludes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Email {
    
    /**
     * 라이센스 생성 이메일 발송
     * 
     * @param WP_User $user 사용자 객체
     * @param string $license_key 라이센스 키
     * @param string $license_type 라이센스 타입
     * @param string|null $expires_at 만료일
     */
    public static function send_license_created_email( $user, $license_key, $license_type, $expires_at = null ) {
        $subject = self::get_email_subject( 'created' );
        $message = self::get_email_template( 'created', array(
            'user_name' => $user->display_name,
            'license_key' => $license_key,
            'license_type' => $license_type,
            'expires_at' => $expires_at,
            'site_name' => get_bloginfo( 'name' ),
            'site_url' => home_url(),
        ) );
        
        wp_mail( $user->user_email, $subject, $message );
    }
    
    /**
     * 라이센스 갱신 이메일 발송
     * 
     * @param WP_User $user 사용자 객체
     * @param string $license_key 라이센스 키
     * @param string|null $expires_at 새 만료일
     */
    public static function send_license_renewed_email( $user, $license_key, $expires_at = null ) {
        $subject = self::get_email_subject( 'renewed' );
        $message = self::get_email_template( 'renewed', array(
            'user_name' => $user->display_name,
            'license_key' => $license_key,
            'expires_at' => $expires_at,
            'site_name' => get_bloginfo( 'name' ),
            'site_url' => home_url(),
        ) );
        
        wp_mail( $user->user_email, $subject, $message );
    }
    
    /**
     * 라이센스 만료 임박 이메일 발송
     * 
     * @param WP_User $user 사용자 객체
     * @param string $license_key 라이센스 키
     * @param string $expires_at 만료일
     * @param int $days_remaining 남은 일수
     */
    public static function send_license_expiring_email( $user, $license_key, $expires_at, $days_remaining ) {
        $subject = self::get_email_subject( 'expiring' );
        $message = self::get_email_template( 'expiring', array(
            'user_name' => $user->display_name,
            'license_key' => $license_key,
            'expires_at' => $expires_at,
            'days_remaining' => $days_remaining,
            'site_name' => get_bloginfo( 'name' ),
            'site_url' => home_url(),
            'renewal_url' => home_url( '/wp-admin/admin.php?page=jj-license-manager' ),
        ) );
        
        wp_mail( $user->user_email, $subject, $message );
    }
    
    /**
     * 이메일 제목 가져오기
     * 
     * @param string $type 이메일 타입
     * @return string 제목
     */
    private static function get_email_subject( $type ) {
        $subjects = array(
            'created' => sprintf( __( '[%s] 라이센스 키가 발급되었습니다', 'jj-license-manager' ), get_bloginfo( 'name' ) ),
            'renewed' => sprintf( __( '[%s] 라이센스가 갱신되었습니다', 'jj-license-manager' ), get_bloginfo( 'name' ) ),
            'expiring' => sprintf( __( '[%s] 라이센스 만료 예정 알림', 'jj-license-manager' ), get_bloginfo( 'name' ) ),
        );
        
        // 커스텀 제목 확인
        $custom_subject = get_option( 'jj_license_email_subject_' . $type, '' );
        if ( ! empty( $custom_subject ) ) {
            return $custom_subject;
        }
        
        return isset( $subjects[ $type ] ) ? $subjects[ $type ] : '';
    }
    
    /**
     * 이메일 템플릿 가져오기
     * 
     * @param string $type 이메일 타입
     * @param array $args 변수
     * @return string 메시지
     */
    private static function get_email_template( $type, $args = array() ) {
        // 커스텀 템플릿 확인
        $custom_template = get_option( 'jj_license_email_template_' . $type, '' );
        if ( ! empty( $custom_template ) ) {
            return self::replace_template_variables( $custom_template, $args );
        }
        
        // 기본 템플릿
        $templates = array(
            'created' => sprintf(
                __( '안녕하세요 %s님,\n\n라이센스 키가 성공적으로 발급되었습니다.\n\n라이센스 키: %s\n라이센스 타입: %s\n만료일: %s\n\n이 라이센스 키를 사용하여 플러그인을 활성화할 수 있습니다.\n\n감사합니다.\n%s', 'jj-license-manager' ),
                '{user_name}',
                '{license_key}',
                '{license_type}',
                '{expires_at}',
                '{site_name}'
            ),
            'renewed' => sprintf(
                __( '안녕하세요 %s님,\n\n라이센스가 성공적으로 갱신되었습니다.\n\n라이센스 키: %s\n새 만료일: %s\n\n감사합니다.\n%s', 'jj-license-manager' ),
                '{user_name}',
                '{license_key}',
                '{expires_at}',
                '{site_name}'
            ),
            'expiring' => sprintf(
                __( '안녕하세요 %s님,\n\n라이센스가 곧 만료됩니다.\n\n라이센스 키: %s\n만료일: %s\n남은 기간: %d일\n\n갱신하여 계속 사용하세요: %s\n\n감사합니다.\n%s', 'jj-license-manager' ),
                '{user_name}',
                '{license_key}',
                '{expires_at}',
                '{days_remaining}',
                '{renewal_url}',
                '{site_name}'
            ),
        );
        
        $template = isset( $templates[ $type ] ) ? $templates[ $type ] : '';
        
        return self::replace_template_variables( $template, $args );
    }
    
    /**
     * 템플릿 변수 치환
     * 
     * @param string $template 템플릿
     * @param array $args 변수
     * @return string 치환된 템플릿
     */
    private static function replace_template_variables( $template, $args ) {
        $replacements = array(
            '{user_name}' => isset( $args['user_name'] ) ? $args['user_name'] : '',
            '{license_key}' => isset( $args['license_key'] ) ? $args['license_key'] : '',
            '{license_type}' => isset( $args['license_type'] ) ? $args['license_type'] : '',
            '{expires_at}' => isset( $args['expires_at'] ) && $args['expires_at'] ? 
                date_i18n( get_option( 'date_format' ), strtotime( $args['expires_at'] ) ) : 
                __( '평생', 'jj-license-manager' ),
            '{days_remaining}' => isset( $args['days_remaining'] ) ? $args['days_remaining'] : '',
            '{site_name}' => isset( $args['site_name'] ) ? $args['site_name'] : get_bloginfo( 'name' ),
            '{site_url}' => isset( $args['site_url'] ) ? $args['site_url'] : home_url(),
            '{renewal_url}' => isset( $args['renewal_url'] ) ? $args['renewal_url'] : home_url(),
        );
        
        return str_replace( array_keys( $replacements ), array_values( $replacements ), $template );
    }
}

