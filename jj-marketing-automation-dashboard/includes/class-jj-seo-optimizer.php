<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * SEO Optimizer
 * 
 * SEO 최적화 및 감사 기능을 제공합니다.
 * 메타 태그 최적화, 구조화된 데이터, 사이트맵, 로봇 텍스트 관리.
 * 
 * @package JJ_Marketing_Dashboard
 * @version 1.0.0
 */

class JJ_SEO_Optimizer {

    private static $instance = null;
    private $option_key = 'jj_marketing_seo_data';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'wp_head', array( $this, 'add_meta_tags' ) );
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'wp_ajax_jj_seo_audit', array( $this, 'ajax_audit' ) );
        add_action( 'wp_ajax_jj_seo_fix_issues', array( $this, 'ajax_fix_issues' ) );
    }

    public function add_meta_tags() {
        if ( is_admin() ) {
            return;
        }

        $settings = $this->get_settings();

        if ( $settings['enable_auto_meta'] ) {
            // Open Graph 태그
            $this->add_open_graph_tags();

            // Twitter Card 태그
            $this->add_twitter_card_tags();

            // Canonical URL
            $this->add_canonical_tag();
        }

        // 구조화된 데이터 (JSON-LD)
        if ( $settings['enable_structured_data'] ) {
            $this->add_structured_data();
        }
    }

    private function add_open_graph_tags() {
        global $post;

        if ( ! is_singular() || ! $post ) {
            return;
        }

        $title = get_the_title();
        $description = $this->get_meta_description();
        $image = $this->get_meta_image();
        $url = get_permalink();

        echo sprintf(
            '<meta property="og:title" content="%s" />' . "\n",
            esc_attr( $title )
        );

        echo sprintf(
            '<meta property="og:description" content="%s" />' . "\n",
            esc_attr( $description )
        );

        echo sprintf(
            '<meta property="og:image" content="%s" />' . "\n",
            esc_url( $image )
        );

        echo sprintf(
            '<meta property="og:url" content="%s" />' . "\n",
            esc_url( $url )
        );

        echo sprintf(
            '<meta property="og:type" content="website" />' . "\n"
        );

        echo sprintf(
            '<meta property="og:locale" content="%s" />' . "\n",
            esc_attr( get_locale() )
        );
    }

    private function add_twitter_card_tags() {
        global $post;

        if ( ! is_singular() || ! $post ) {
            return;
        }

        $title = get_the_title();
        $description = $this->get_meta_description();
        $image = $this->get_meta_image();

        echo sprintf(
            '<meta name="twitter:card" content="summary_large_image" />' . "\n"
        );

        echo sprintf(
            '<meta name="twitter:title" content="%s" />' . "\n",
            esc_attr( $title )
        );

        echo sprintf(
            '<meta name="twitter:description" content="%s" />' . "\n",
            esc_attr( $description )
        );

        echo sprintf(
            '<meta name="twitter:image" content="%s" />' . "\n",
            esc_url( $image )
        );
    }

    private function add_canonical_tag() {
        if ( is_singular() ) {
            echo sprintf(
                '<link rel="canonical" href="%s" />' . "\n",
                esc_url( get_permalink() )
            );
        }
    }

    private function add_structured_data() {
        global $post;

        if ( ! is_singular() || ! $post ) {
            return;
        }

        $data = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo( 'name' ),
            'url' => home_url( '/' ),
            'description' => get_bloginfo( 'description' ),
        );

        echo '<script type="application/ld+json">' . wp_json_encode( $data ) . '</script>' . "\n";
    }

    public function ajax_audit() {
        check_ajax_referer( 'jj_marketing_dashboard_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        $url = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : home_url( '/' );

        $results = $this->run_seo_audit( $url );

        wp_send_json_success( $results );
    }

    public function ajax_fix_issues() {
        check_ajax_referer( 'jj_marketing_dashboard_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        $url = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : home_url( '/' );

        $results = $this->auto_fix_seo_issues( $url );

        wp_send_json_success( $results );
    }

    private function run_seo_audit( $url ) {
        $results = array(
            'url' => $url,
            'timestamp' => current_time( 'mysql' ),
            'issues' => array(),
            'score' => 0,
            'suggestions' => array(),
        );

        // 태틀 감사
        $title_status = $this->audit_title_tag( $url );
        if ( ! $title_status['exists'] ) {
            $results['issues'][] = array(
                'type' => 'missing_title',
                'severity' => 'high',
                'message' => '제목 태그가 없습니다.',
                'suggestion' => '<title> 태그를 추가하세요.',
            );
        }

        // 메타 설명 감사
        $meta_description = $this->get_meta_description();
        if ( empty( $meta_description ) || strlen( $meta_description ) < 50 ) {
            $results['issues'][] = array(
                'type' => 'short_description',
                'severity' => 'medium',
                'message' => '메타 설명이 너무 짧거나 없습니다.',
                'suggestion' => '메타 설명을 120-160자로 작성하세요.',
            );
        }

        // 이미지 태그 감사
        $image_tag = $this->get_meta_image();
        if ( ! $image_tag ) {
            $results['issues'][] = array(
                'type' => 'missing_image',
                'severity' => 'medium',
                'message' => 'OG 이미지가 없습니다.',
                'suggestion' => 'og:image 태그를 추가하세요.',
            );
        }

        // 점수 계산
        $score = 100;
        foreach ( $results['issues'] as $issue ) {
            if ( $issue['severity'] === 'high' ) {
                $score -= 20;
            } elseif ( $issue['severity'] === 'medium' ) {
                $score -= 10;
            } else {
                $score -= 5;
            }
        }
        $results['score'] = max( $score, 0 );

        // 제안
        if ( $results['score'] < 100 ) {
            $results['suggestions'][] = '전반적인 SEO 점수를 높이세요.';
        }

        return $results;
    }

    private function auto_fix_seo_issues( $url ) {
        $fixed = array(
            'url' => $url,
            'fixed_issues' => array(),
        );

        // 여기서 실제 수정 로직을 구현
        // 예: 메타 태그 자동 추가 등

        return $fixed;
    }

    private function audit_title_tag( $url ) {
        $response = wp_remote_get( $url );
        
        if ( is_wp_error( $response ) ) {
            return array( 'exists' => false );
        }

        $body = wp_remote_retrieve_body( $response );

        if ( preg_match( '/<title[^>]*>([^<]*)<\/title>/i', $body, $matches ) ) {
            return array(
                'exists' => true,
                'title' => trim( $matches[1] ),
            );
        }

        return array( 'exists' => false );
    }

    private function get_meta_description() {
        $description = get_post_meta( get_the_ID(), '_seo_description', true );

        if ( empty( $description ) ) {
            $description = wp_trim_words( get_the_excerpt(), 30 );
        }

        return $description;
    }

    private function get_meta_image() {
        $image = get_post_meta( get_the_ID(), '_seo_image', true );

        if ( empty( $image ) ) {
            if ( has_post_thumbnail() ) {
                $image = get_the_post_thumbnail_url( 'full' );
            }
        }

        return $image;
    }

    private function get_settings() {
        $default_settings = array(
            'enable_auto_meta' => true,
            'enable_structured_data' => true,
            'enable_sitemap' => true,
        'enable_robots_txt' => true,
        'default_title_template' => '%site_title% - %post_title%',
        'default_description_template' => '%post_title% | %site_description%',
        'social_image_size' => '1200x630',
        'enable_auto_fix' => false,
        'audit_frequency' => 'weekly',
            'notification_email' => '',
        );

        return wp_parse_args( get_option( 'jj_marketing_seo_settings', array() ), $default_settings );
    }
}
