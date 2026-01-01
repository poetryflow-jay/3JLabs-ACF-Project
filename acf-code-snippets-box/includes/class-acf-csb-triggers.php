<?php
/**
 * ACF Code Snippets Box - Triggers
 * 
 * 트리거 조건 관리
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Triggers 클래스
 */
class ACF_CSB_Triggers {

    /**
     * 사용 가능한 트리거 목록
     */
    public static function get_available_triggers() {
        return array(
            'location' => array(
                'label'   => __( '실행 위치', 'acf-code-snippets-box' ),
                'options' => array(
                    'everywhere'     => __( '모든 페이지', 'acf-code-snippets-box' ),
                    'frontend'       => __( '프론트엔드만', 'acf-code-snippets-box' ),
                    'admin'          => __( '관리자 페이지만', 'acf-code-snippets-box' ),
                    'specific_pages' => __( '특정 페이지', 'acf-code-snippets-box' ),
                    'specific_posts' => __( '특정 포스트', 'acf-code-snippets-box' ),
                    'post_types'     => __( '특정 포스트 타입', 'acf-code-snippets-box' ),
                    'taxonomies'     => __( '특정 택소노미', 'acf-code-snippets-box' ),
                    'home'           => __( '홈페이지만', 'acf-code-snippets-box' ),
                    'archive'        => __( '아카이브 페이지만', 'acf-code-snippets-box' ),
                    'search'         => __( '검색 결과 페이지만', 'acf-code-snippets-box' ),
                    '404'            => __( '404 페이지만', 'acf-code-snippets-box' ),
                ),
            ),
            'user_roles' => array(
                'label'   => __( '사용자 역할', 'acf-code-snippets-box' ),
                'options' => 'dynamic', // wp_roles()에서 동적으로 가져옴
            ),
            'device' => array(
                'label'   => __( '디바이스', 'acf-code-snippets-box' ),
                'options' => array(
                    'all'     => __( '모든 디바이스', 'acf-code-snippets-box' ),
                    'desktop' => __( '데스크탑만', 'acf-code-snippets-box' ),
                    'mobile'  => __( '모바일만', 'acf-code-snippets-box' ),
                    'tablet'  => __( '태블릿만', 'acf-code-snippets-box' ),
                ),
            ),
            'logged_in' => array(
                'label'   => __( '로그인 상태', 'acf-code-snippets-box' ),
                'options' => array(
                    'all'        => __( '모든 사용자', 'acf-code-snippets-box' ),
                    'logged_in'  => __( '로그인한 사용자만', 'acf-code-snippets-box' ),
                    'logged_out' => __( '비로그인 사용자만', 'acf-code-snippets-box' ),
                ),
            ),
            'time_based' => array(
                'label'   => __( '시간 기반', 'acf-code-snippets-box' ),
                'options' => array(
                    'always'    => __( '항상', 'acf-code-snippets-box' ),
                    'date_range' => __( '특정 기간', 'acf-code-snippets-box' ),
                    'time_range' => __( '특정 시간대', 'acf-code-snippets-box' ),
                    'weekdays'   => __( '특정 요일', 'acf-code-snippets-box' ),
                ),
            ),
        );
    }

    /**
     * 포스트 타입 목록 가져오기
     */
    public static function get_post_types() {
        $post_types = get_post_types( array( 'public' => true ), 'objects' );
        $result = array();
        
        foreach ( $post_types as $post_type ) {
            $result[ $post_type->name ] = $post_type->label;
        }
        
        return $result;
    }

    /**
     * 택소노미 목록 가져오기
     */
    public static function get_taxonomies() {
        $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
        $result = array();
        
        foreach ( $taxonomies as $taxonomy ) {
            $result[ $taxonomy->name ] = $taxonomy->label;
        }
        
        return $result;
    }
}
