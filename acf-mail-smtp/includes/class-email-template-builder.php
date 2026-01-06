<?php
/**
 * Email Template Visual Builder
 *
 * 비주얼 이메일 템플릿 빌더 클래스
 * 드래그&드롭으로 이메일 템플릿을 구성할 수 있음
 *
 * @package ACF_Mail_SMTP
 * @version 2.3.0
 * @since Phase 49-2
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Mail_SMTP_Email_Template_Builder {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 템플릿 저장 옵션 키
     */
    private $option_key = 'acf_mail_smtp_email_templates';

    /**
     * 사전 정의 블록 타입
     */
    private $block_types = array();

    /**
     * 사전 정의 레이아웃 프리셋
     */
    private $presets = array();

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->define_block_types();
        $this->define_presets();
        $this->init_hooks();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // AJAX handlers
        add_action( 'wp_ajax_acf_mail_smtp_get_templates', array( $this, 'ajax_get_templates' ) );
        add_action( 'wp_ajax_acf_mail_smtp_save_template', array( $this, 'ajax_save_template' ) );
        add_action( 'wp_ajax_acf_mail_smtp_delete_template', array( $this, 'ajax_delete_template' ) );
        add_action( 'wp_ajax_acf_mail_smtp_preview_template', array( $this, 'ajax_preview_template' ) );
        add_action( 'wp_ajax_acf_mail_smtp_get_block_types', array( $this, 'ajax_get_block_types' ) );
        add_action( 'wp_ajax_acf_mail_smtp_get_presets', array( $this, 'ajax_get_presets' ) );
        add_action( 'wp_ajax_acf_mail_smtp_duplicate_template', array( $this, 'ajax_duplicate_template' ) );
    }

    /**
     * 블록 타입 정의
     */
    private function define_block_types() {
        $this->block_types = array(
            'header' => array(
                'name'        => __( '헤더', 'acf-mail-smtp' ),
                'icon'        => 'dashicons-editor-aligncenter',
                'description' => __( '이메일 상단의 로고와 제목 영역', 'acf-mail-smtp' ),
                'category'    => 'structure',
                'defaults'    => array(
                    'logo_url'         => '',
                    'logo_width'       => 150,
                    'title'            => '{form_title}',
                    'title_color'      => '#ffffff',
                    'title_size'       => 24,
                    'background_color' => '#667eea',
                    'background_gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    'padding'          => 30,
                    'alignment'        => 'center',
                ),
            ),
            'text' => array(
                'name'        => __( '텍스트', 'acf-mail-smtp' ),
                'icon'        => 'dashicons-editor-paragraph',
                'description' => __( '일반 텍스트 콘텐츠', 'acf-mail-smtp' ),
                'category'    => 'content',
                'defaults'    => array(
                    'content'    => '',
                    'text_color' => '#333333',
                    'font_size'  => 16,
                    'line_height' => 1.6,
                    'padding'    => 20,
                    'alignment'  => 'left',
                ),
            ),
            'image' => array(
                'name'        => __( '이미지', 'acf-mail-smtp' ),
                'icon'        => 'dashicons-format-image',
                'description' => __( '이미지 블록', 'acf-mail-smtp' ),
                'category'    => 'content',
                'defaults'    => array(
                    'src'       => '',
                    'alt'       => '',
                    'width'     => '100%',
                    'max_width' => 600,
                    'alignment' => 'center',
                    'link'      => '',
                    'padding'   => 10,
                ),
            ),
            'button' => array(
                'name'        => __( '버튼', 'acf-mail-smtp' ),
                'icon'        => 'dashicons-button',
                'description' => __( 'CTA 버튼', 'acf-mail-smtp' ),
                'category'    => 'content',
                'defaults'    => array(
                    'text'             => __( '자세히 보기', 'acf-mail-smtp' ),
                    'url'              => '{site_url}',
                    'background_color' => '#667eea',
                    'text_color'       => '#ffffff',
                    'border_radius'    => 6,
                    'padding_x'        => 30,
                    'padding_y'        => 15,
                    'alignment'        => 'center',
                    'full_width'       => false,
                ),
            ),
            'divider' => array(
                'name'        => __( '구분선', 'acf-mail-smtp' ),
                'icon'        => 'dashicons-minus',
                'description' => __( '섹션 구분선', 'acf-mail-smtp' ),
                'category'    => 'structure',
                'defaults'    => array(
                    'color'   => '#e9ecef',
                    'height'  => 1,
                    'width'   => '100%',
                    'style'   => 'solid',
                    'margin'  => 20,
                ),
            ),
            'spacer' => array(
                'name'        => __( '여백', 'acf-mail-smtp' ),
                'icon'        => 'dashicons-editor-expand',
                'description' => __( '빈 공간 추가', 'acf-mail-smtp' ),
                'category'    => 'structure',
                'defaults'    => array(
                    'height' => 30,
                ),
            ),
            'form_data' => array(
                'name'        => __( '폼 데이터', 'acf-mail-smtp' ),
                'icon'        => 'dashicons-editor-table',
                'description' => __( '제출된 폼 데이터 표시', 'acf-mail-smtp' ),
                'category'    => 'dynamic',
                'defaults'    => array(
                    'style'            => 'table',
                    'label_color'      => '#333333',
                    'value_color'      => '#666666',
                    'border_color'     => '#e9ecef',
                    'background_color' => '#f8f9fa',
                    'padding'          => 20,
                ),
            ),
            'social' => array(
                'name'        => __( '소셜 링크', 'acf-mail-smtp' ),
                'icon'        => 'dashicons-share',
                'description' => __( '소셜 미디어 아이콘', 'acf-mail-smtp' ),
                'category'    => 'content',
                'defaults'    => array(
                    'facebook'   => '',
                    'twitter'    => '',
                    'instagram'  => '',
                    'linkedin'   => '',
                    'youtube'    => '',
                    'icon_size'  => 32,
                    'icon_style' => 'circle',
                    'alignment'  => 'center',
                    'spacing'    => 10,
                ),
            ),
            'footer' => array(
                'name'        => __( '푸터', 'acf-mail-smtp' ),
                'icon'        => 'dashicons-editor-aligncenter',
                'description' => __( '이메일 하단 푸터 영역', 'acf-mail-smtp' ),
                'category'    => 'structure',
                'defaults'    => array(
                    'text'             => __( '이 이메일은 {site_name}에서 자동으로 발송되었습니다.', 'acf-mail-smtp' ),
                    'unsubscribe_text' => __( '수신 거부', 'acf-mail-smtp' ),
                    'unsubscribe_url'  => '',
                    'text_color'       => '#6c757d',
                    'font_size'        => 12,
                    'background_color' => '#f8f9fa',
                    'padding'          => 20,
                    'alignment'        => 'center',
                ),
            ),
            'columns' => array(
                'name'        => __( '컬럼 레이아웃', 'acf-mail-smtp' ),
                'icon'        => 'dashicons-columns',
                'description' => __( '2-4개의 컬럼 레이아웃', 'acf-mail-smtp' ),
                'category'    => 'structure',
                'defaults'    => array(
                    'columns'  => 2,
                    'gap'      => 20,
                    'padding'  => 10,
                    'content'  => array(
                        array( 'type' => 'text', 'content' => '' ),
                        array( 'type' => 'text', 'content' => '' ),
                    ),
                ),
            ),
            'html' => array(
                'name'        => __( 'HTML 코드', 'acf-mail-smtp' ),
                'icon'        => 'dashicons-editor-code',
                'description' => __( '커스텀 HTML 코드', 'acf-mail-smtp' ),
                'category'    => 'advanced',
                'defaults'    => array(
                    'html' => '',
                ),
            ),
        );
    }

    /**
     * 프리셋 템플릿 정의
     */
    private function define_presets() {
        $this->presets = array(
            'minimal' => array(
                'name'        => __( '미니멀', 'acf-mail-smtp' ),
                'description' => __( '깔끔하고 심플한 디자인', 'acf-mail-smtp' ),
                'thumbnail'   => 'minimal.png',
                'blocks'      => array(
                    array( 'type' => 'header', 'settings' => array( 'background_color' => '#1a1a1a', 'background_gradient' => '' ) ),
                    array( 'type' => 'text', 'settings' => array( 'content' => '새로운 폼 제출이 있습니다.' ) ),
                    array( 'type' => 'form_data', 'settings' => array() ),
                    array( 'type' => 'footer', 'settings' => array() ),
                ),
            ),
            'modern' => array(
                'name'        => __( '모던', 'acf-mail-smtp' ),
                'description' => __( '그라데이션이 적용된 현대적 디자인', 'acf-mail-smtp' ),
                'thumbnail'   => 'modern.png',
                'blocks'      => array(
                    array( 'type' => 'header', 'settings' => array() ),
                    array( 'type' => 'spacer', 'settings' => array( 'height' => 20 ) ),
                    array( 'type' => 'text', 'settings' => array( 'content' => '안녕하세요!\n\n새로운 폼 제출이 있습니다. 아래에서 자세한 내용을 확인해주세요.' ) ),
                    array( 'type' => 'divider', 'settings' => array() ),
                    array( 'type' => 'form_data', 'settings' => array() ),
                    array( 'type' => 'button', 'settings' => array( 'text' => '관리자 페이지에서 확인하기' ) ),
                    array( 'type' => 'footer', 'settings' => array() ),
                ),
            ),
            'corporate' => array(
                'name'        => __( '비즈니스', 'acf-mail-smtp' ),
                'description' => __( '전문적인 비즈니스 스타일', 'acf-mail-smtp' ),
                'thumbnail'   => 'corporate.png',
                'blocks'      => array(
                    array( 'type' => 'header', 'settings' => array( 'background_color' => '#2c3e50', 'background_gradient' => '' ) ),
                    array( 'type' => 'text', 'settings' => array( 'content' => '안녕하세요,\n\n문의 폼을 통해 새로운 메시지가 접수되었습니다.' ) ),
                    array( 'type' => 'form_data', 'settings' => array( 'style' => 'table' ) ),
                    array( 'type' => 'spacer', 'settings' => array( 'height' => 20 ) ),
                    array( 'type' => 'button', 'settings' => array( 'text' => '답변하기', 'background_color' => '#2c3e50' ) ),
                    array( 'type' => 'divider', 'settings' => array() ),
                    array( 'type' => 'footer', 'settings' => array() ),
                ),
            ),
            'newsletter' => array(
                'name'        => __( '뉴스레터', 'acf-mail-smtp' ),
                'description' => __( '뉴스레터 스타일 템플릿', 'acf-mail-smtp' ),
                'thumbnail'   => 'newsletter.png',
                'blocks'      => array(
                    array( 'type' => 'header', 'settings' => array( 'background_gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)' ) ),
                    array( 'type' => 'image', 'settings' => array( 'src' => '' ) ),
                    array( 'type' => 'text', 'settings' => array( 'content' => '최신 소식을 전해드립니다!' ) ),
                    array( 'type' => 'columns', 'settings' => array( 'columns' => 2 ) ),
                    array( 'type' => 'button', 'settings' => array( 'text' => '더 알아보기', 'background_color' => '#f5576c' ) ),
                    array( 'type' => 'social', 'settings' => array() ),
                    array( 'type' => 'footer', 'settings' => array() ),
                ),
            ),
            'notification' => array(
                'name'        => __( '알림', 'acf-mail-smtp' ),
                'description' => __( '간단한 알림 이메일', 'acf-mail-smtp' ),
                'thumbnail'   => 'notification.png',
                'blocks'      => array(
                    array( 'type' => 'text', 'settings' => array( 'content' => '새로운 알림이 있습니다.', 'font_size' => 20, 'alignment' => 'center' ) ),
                    array( 'type' => 'divider', 'settings' => array() ),
                    array( 'type' => 'form_data', 'settings' => array( 'style' => 'simple' ) ),
                    array( 'type' => 'footer', 'settings' => array( 'background_color' => 'transparent' ) ),
                ),
            ),
        );
    }

    /**
     * 블록 타입 목록 반환
     */
    public function get_block_types() {
        return apply_filters( 'acf_mail_smtp_template_block_types', $this->block_types );
    }

    /**
     * 프리셋 목록 반환
     */
    public function get_presets() {
        return apply_filters( 'acf_mail_smtp_template_presets', $this->presets );
    }

    /**
     * 템플릿 목록 가져오기
     */
    public function get_templates() {
        return get_option( $this->option_key, array() );
    }

    /**
     * 템플릿 저장
     */
    public function save_template( $template_data ) {
        $templates = $this->get_templates();

        $template_id = isset( $template_data['id'] ) ? sanitize_key( $template_data['id'] ) : '';

        // 새 템플릿이면 ID 생성
        if ( empty( $template_id ) ) {
            $template_id = 'tpl_' . wp_generate_uuid4();
        }

        $sanitized = array(
            'id'          => $template_id,
            'name'        => isset( $template_data['name'] ) ? sanitize_text_field( $template_data['name'] ) : __( '새 템플릿', 'acf-mail-smtp' ),
            'description' => isset( $template_data['description'] ) ? sanitize_textarea_field( $template_data['description'] ) : '',
            'blocks'      => isset( $template_data['blocks'] ) ? $this->sanitize_blocks( $template_data['blocks'] ) : array(),
            'settings'    => isset( $template_data['settings'] ) ? $this->sanitize_template_settings( $template_data['settings'] ) : array(),
            'created_at'  => isset( $templates[ $template_id ]['created_at'] ) ? $templates[ $template_id ]['created_at'] : current_time( 'mysql' ),
            'updated_at'  => current_time( 'mysql' ),
        );

        $templates[ $template_id ] = $sanitized;

        update_option( $this->option_key, $templates );

        return $sanitized;
    }

    /**
     * 블록 데이터 살균
     */
    private function sanitize_blocks( $blocks ) {
        if ( ! is_array( $blocks ) ) {
            return array();
        }

        $sanitized = array();

        foreach ( $blocks as $block ) {
            if ( ! isset( $block['type'] ) ) {
                continue;
            }

            $block_type = sanitize_key( $block['type'] );

            if ( ! isset( $this->block_types[ $block_type ] ) ) {
                continue;
            }

            $sanitized_block = array(
                'type'     => $block_type,
                'id'       => isset( $block['id'] ) ? sanitize_key( $block['id'] ) : 'block_' . uniqid(),
                'settings' => array(),
            );

            // 블록 타입에 따라 설정 살균
            $defaults = $this->block_types[ $block_type ]['defaults'];
            $settings = isset( $block['settings'] ) ? $block['settings'] : array();

            foreach ( $defaults as $key => $default_value ) {
                if ( isset( $settings[ $key ] ) ) {
                    $sanitized_block['settings'][ $key ] = $this->sanitize_block_setting( $key, $settings[ $key ], $default_value );
                } else {
                    $sanitized_block['settings'][ $key ] = $default_value;
                }
            }

            $sanitized[] = $sanitized_block;
        }

        return $sanitized;
    }

    /**
     * 개별 블록 설정 살균
     */
    private function sanitize_block_setting( $key, $value, $default ) {
        // 색상 값
        if ( strpos( $key, 'color' ) !== false || strpos( $key, 'gradient' ) !== false ) {
            if ( preg_match( '/^#[a-fA-F0-9]{3,8}$/', $value ) ) {
                return sanitize_text_field( $value );
            }
            if ( preg_match( '/^(linear-gradient|radial-gradient)\(.+\)$/', $value ) ) {
                return sanitize_text_field( $value );
            }
            return $default;
        }

        // URL 값
        if ( strpos( $key, 'url' ) !== false || $key === 'src' || $key === 'link' ) {
            return esc_url_raw( $value );
        }

        // 숫자 값
        if ( is_numeric( $default ) ) {
            return is_numeric( $value ) ? (float) $value : $default;
        }

        // 불린 값
        if ( is_bool( $default ) ) {
            return (bool) $value;
        }

        // 배열 값
        if ( is_array( $default ) ) {
            return is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : $default;
        }

        // 기본: 텍스트
        return sanitize_textarea_field( $value );
    }

    /**
     * 템플릿 전역 설정 살균
     */
    private function sanitize_template_settings( $settings ) {
        if ( ! is_array( $settings ) ) {
            return array();
        }

        return array(
            'max_width'        => isset( $settings['max_width'] ) ? absint( $settings['max_width'] ) : 600,
            'background_color' => isset( $settings['background_color'] ) ? sanitize_hex_color( $settings['background_color'] ) : '#f4f4f4',
            'content_bg_color' => isset( $settings['content_bg_color'] ) ? sanitize_hex_color( $settings['content_bg_color'] ) : '#ffffff',
            'font_family'      => isset( $settings['font_family'] ) ? sanitize_text_field( $settings['font_family'] ) : 'Arial, sans-serif',
            'border_radius'    => isset( $settings['border_radius'] ) ? absint( $settings['border_radius'] ) : 8,
        );
    }

    /**
     * 템플릿 삭제
     */
    public function delete_template( $template_id ) {
        $templates = $this->get_templates();

        if ( isset( $templates[ $template_id ] ) ) {
            unset( $templates[ $template_id ] );
            update_option( $this->option_key, $templates );
            return true;
        }

        return false;
    }

    /**
     * 템플릿 복제
     */
    public function duplicate_template( $template_id ) {
        $templates = $this->get_templates();

        if ( ! isset( $templates[ $template_id ] ) ) {
            return false;
        }

        $original = $templates[ $template_id ];
        $new_id = 'tpl_' . wp_generate_uuid4();

        $duplicate = array(
            'id'          => $new_id,
            'name'        => $original['name'] . ' (복사본)',
            'description' => $original['description'],
            'blocks'      => $original['blocks'],
            'settings'    => $original['settings'],
            'created_at'  => current_time( 'mysql' ),
            'updated_at'  => current_time( 'mysql' ),
        );

        $templates[ $new_id ] = $duplicate;
        update_option( $this->option_key, $templates );

        return $duplicate;
    }

    /**
     * 템플릿을 HTML로 렌더링
     */
    public function render_template( $template_id, $data = array() ) {
        $templates = $this->get_templates();

        if ( ! isset( $templates[ $template_id ] ) ) {
            // 기본 템플릿 반환
            return ACF_Mail_SMTP_Email_Template::get_default_template();
        }

        $template = $templates[ $template_id ];
        $settings = isset( $template['settings'] ) ? $template['settings'] : array();
        $blocks = isset( $template['blocks'] ) ? $template['blocks'] : array();

        // 글로벌 설정
        $max_width = isset( $settings['max_width'] ) ? $settings['max_width'] : 600;
        $bg_color = isset( $settings['background_color'] ) ? $settings['background_color'] : '#f4f4f4';
        $content_bg = isset( $settings['content_bg_color'] ) ? $settings['content_bg_color'] : '#ffffff';
        $font_family = isset( $settings['font_family'] ) ? $settings['font_family'] : 'Arial, sans-serif';
        $border_radius = isset( $settings['border_radius'] ) ? $settings['border_radius'] : 8;

        // HTML 생성
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; font-family: ' . esc_attr( $font_family ) . '; background-color: ' . esc_attr( $bg_color ) . ';">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: ' . esc_attr( $bg_color ) . '; padding: 20px;">
        <tr>
            <td align="center">
                <table width="' . esc_attr( $max_width ) . '" cellpadding="0" cellspacing="0" style="background-color: ' . esc_attr( $content_bg ) . '; border-radius: ' . esc_attr( $border_radius ) . 'px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';

        // 블록 렌더링
        foreach ( $blocks as $block ) {
            $html .= $this->render_block( $block, $data );
        }

        $html .= '
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        // 변수 치환
        return ACF_Mail_SMTP_Email_Template::replace_variables( $html, $data );
    }

    /**
     * 개별 블록 렌더링
     */
    private function render_block( $block, $data = array() ) {
        $type = isset( $block['type'] ) ? $block['type'] : '';
        $settings = isset( $block['settings'] ) ? $block['settings'] : array();

        switch ( $type ) {
            case 'header':
                return $this->render_header_block( $settings, $data );

            case 'text':
                return $this->render_text_block( $settings, $data );

            case 'image':
                return $this->render_image_block( $settings );

            case 'button':
                return $this->render_button_block( $settings, $data );

            case 'divider':
                return $this->render_divider_block( $settings );

            case 'spacer':
                return $this->render_spacer_block( $settings );

            case 'form_data':
                return $this->render_form_data_block( $settings, $data );

            case 'social':
                return $this->render_social_block( $settings );

            case 'footer':
                return $this->render_footer_block( $settings, $data );

            case 'html':
                return $this->render_html_block( $settings );

            default:
                return '';
        }
    }

    /**
     * 헤더 블록 렌더링
     */
    private function render_header_block( $settings, $data ) {
        $bg = ! empty( $settings['background_gradient'] ) ? $settings['background_gradient'] : $settings['background_color'];
        $title = ACF_Mail_SMTP_Email_Template::replace_variables( $settings['title'], $data );

        $html = '<tr>
            <td style="background: ' . esc_attr( $bg ) . '; padding: ' . esc_attr( $settings['padding'] ) . 'px; text-align: ' . esc_attr( $settings['alignment'] ) . ';">';

        if ( ! empty( $settings['logo_url'] ) ) {
            $html .= '<img src="' . esc_url( $settings['logo_url'] ) . '" alt="Logo" style="width: ' . esc_attr( $settings['logo_width'] ) . 'px; margin-bottom: 15px;" /><br>';
        }

        $html .= '<h1 style="color: ' . esc_attr( $settings['title_color'] ) . '; margin: 0; font-size: ' . esc_attr( $settings['title_size'] ) . 'px;">' . esc_html( $title ) . '</h1>
            </td>
        </tr>';

        return $html;
    }

    /**
     * 텍스트 블록 렌더링
     */
    private function render_text_block( $settings, $data ) {
        $content = ACF_Mail_SMTP_Email_Template::replace_variables( $settings['content'], $data );
        $content = nl2br( esc_html( $content ) );

        return '<tr>
            <td style="padding: ' . esc_attr( $settings['padding'] ) . 'px; text-align: ' . esc_attr( $settings['alignment'] ) . ';">
                <p style="color: ' . esc_attr( $settings['text_color'] ) . '; font-size: ' . esc_attr( $settings['font_size'] ) . 'px; line-height: ' . esc_attr( $settings['line_height'] ) . '; margin: 0;">' . $content . '</p>
            </td>
        </tr>';
    }

    /**
     * 이미지 블록 렌더링
     */
    private function render_image_block( $settings ) {
        if ( empty( $settings['src'] ) ) {
            return '';
        }

        $img = '<img src="' . esc_url( $settings['src'] ) . '" alt="' . esc_attr( $settings['alt'] ) . '" style="width: ' . esc_attr( $settings['width'] ) . '; max-width: ' . esc_attr( $settings['max_width'] ) . 'px; display: block;" />';

        if ( ! empty( $settings['link'] ) ) {
            $img = '<a href="' . esc_url( $settings['link'] ) . '">' . $img . '</a>';
        }

        return '<tr>
            <td style="padding: ' . esc_attr( $settings['padding'] ) . 'px; text-align: ' . esc_attr( $settings['alignment'] ) . ';">' . $img . '</td>
        </tr>';
    }

    /**
     * 버튼 블록 렌더링
     */
    private function render_button_block( $settings, $data ) {
        $url = ACF_Mail_SMTP_Email_Template::replace_variables( $settings['url'], $data );
        $width = $settings['full_width'] ? 'width: 100%;' : '';

        return '<tr>
            <td style="padding: 20px; text-align: ' . esc_attr( $settings['alignment'] ) . ';">
                <a href="' . esc_url( $url ) . '" style="display: inline-block; ' . $width . ' background-color: ' . esc_attr( $settings['background_color'] ) . '; color: ' . esc_attr( $settings['text_color'] ) . '; text-decoration: none; padding: ' . esc_attr( $settings['padding_y'] ) . 'px ' . esc_attr( $settings['padding_x'] ) . 'px; border-radius: ' . esc_attr( $settings['border_radius'] ) . 'px; font-weight: bold; text-align: center;">' . esc_html( $settings['text'] ) . '</a>
            </td>
        </tr>';
    }

    /**
     * 구분선 블록 렌더링
     */
    private function render_divider_block( $settings ) {
        return '<tr>
            <td style="padding: ' . esc_attr( $settings['margin'] ) . 'px 0;">
                <hr style="border: none; border-top: ' . esc_attr( $settings['height'] ) . 'px ' . esc_attr( $settings['style'] ) . ' ' . esc_attr( $settings['color'] ) . '; width: ' . esc_attr( $settings['width'] ) . '; margin: 0;" />
            </td>
        </tr>';
    }

    /**
     * 여백 블록 렌더링
     */
    private function render_spacer_block( $settings ) {
        return '<tr>
            <td style="height: ' . esc_attr( $settings['height'] ) . 'px;"></td>
        </tr>';
    }

    /**
     * 폼 데이터 블록 렌더링
     */
    private function render_form_data_block( $settings, $data ) {
        $form_data_html = isset( $data['form_data'] ) ? $data['form_data'] : '{form_data}';

        return '<tr>
            <td style="padding: ' . esc_attr( $settings['padding'] ) . 'px;">' . $form_data_html . '</td>
        </tr>';
    }

    /**
     * 소셜 블록 렌더링
     */
    private function render_social_block( $settings ) {
        $icons = array();
        $socials = array( 'facebook', 'twitter', 'instagram', 'linkedin', 'youtube' );

        foreach ( $socials as $social ) {
            if ( ! empty( $settings[ $social ] ) ) {
                $icons[] = '<a href="' . esc_url( $settings[ $social ] ) . '" style="display: inline-block; margin: 0 ' . esc_attr( $settings['spacing'] / 2 ) . 'px;">
                    <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/' . $social . '.svg" alt="' . ucfirst( $social ) . '" style="width: ' . esc_attr( $settings['icon_size'] ) . 'px; height: ' . esc_attr( $settings['icon_size'] ) . 'px;" />
                </a>';
            }
        }

        if ( empty( $icons ) ) {
            return '';
        }

        return '<tr>
            <td style="padding: 20px; text-align: ' . esc_attr( $settings['alignment'] ) . ';">' . implode( '', $icons ) . '</td>
        </tr>';
    }

    /**
     * 푸터 블록 렌더링
     */
    private function render_footer_block( $settings, $data ) {
        $text = ACF_Mail_SMTP_Email_Template::replace_variables( $settings['text'], $data );

        $html = '<tr>
            <td style="background-color: ' . esc_attr( $settings['background_color'] ) . '; padding: ' . esc_attr( $settings['padding'] ) . 'px; text-align: ' . esc_attr( $settings['alignment'] ) . '; border-top: 1px solid #e9ecef;">
                <p style="color: ' . esc_attr( $settings['text_color'] ) . '; font-size: ' . esc_attr( $settings['font_size'] ) . 'px; margin: 0;">' . esc_html( $text ) . '</p>';

        if ( ! empty( $settings['unsubscribe_url'] ) ) {
            $html .= '<p style="margin: 10px 0 0 0;"><a href="' . esc_url( $settings['unsubscribe_url'] ) . '" style="color: ' . esc_attr( $settings['text_color'] ) . '; font-size: ' . esc_attr( $settings['font_size'] ) . 'px;">' . esc_html( $settings['unsubscribe_text'] ) . '</a></p>';
        }

        $html .= '</td>
        </tr>';

        return $html;
    }

    /**
     * HTML 블록 렌더링
     */
    private function render_html_block( $settings ) {
        return '<tr>
            <td>' . wp_kses_post( $settings['html'] ) . '</td>
        </tr>';
    }

    // ===== AJAX Handlers =====

    /**
     * AJAX: 템플릿 목록
     */
    public function ajax_get_templates() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        wp_send_json_success( $this->get_templates() );
    }

    /**
     * AJAX: 템플릿 저장
     */
    public function ajax_save_template() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $template_data = isset( $_POST['template'] ) ? json_decode( wp_unslash( $_POST['template'] ), true ) : array();

        if ( empty( $template_data ) ) {
            wp_send_json_error( array( 'message' => __( '템플릿 데이터가 비어있습니다.', 'acf-mail-smtp' ) ) );
        }

        $saved = $this->save_template( $template_data );

        wp_send_json_success( array(
            'message'  => __( '템플릿이 저장되었습니다.', 'acf-mail-smtp' ),
            'template' => $saved,
        ) );
    }

    /**
     * AJAX: 템플릿 삭제
     */
    public function ajax_delete_template() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $template_id = isset( $_POST['template_id'] ) ? sanitize_key( $_POST['template_id'] ) : '';

        if ( empty( $template_id ) ) {
            wp_send_json_error( array( 'message' => __( '템플릿 ID가 필요합니다.', 'acf-mail-smtp' ) ) );
        }

        if ( $this->delete_template( $template_id ) ) {
            wp_send_json_success( array( 'message' => __( '템플릿이 삭제되었습니다.', 'acf-mail-smtp' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( '템플릿을 찾을 수 없습니다.', 'acf-mail-smtp' ) ) );
        }
    }

    /**
     * AJAX: 템플릿 미리보기
     */
    public function ajax_preview_template() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $template_id = isset( $_POST['template_id'] ) ? sanitize_key( $_POST['template_id'] ) : '';

        // 샘플 데이터
        $sample_data = array(
            'form_title' => __( '문의 폼', 'acf-mail-smtp' ),
            'form_data'  => ACF_Mail_SMTP_Email_Template::format_form_data( array(
                'name'    => '홍길동',
                'email'   => 'hong@example.com',
                'phone'   => '010-1234-5678',
                'message' => '안녕하세요. 문의 드립니다.',
            ) ),
            'site_name'  => get_option( 'blogname' ),
            'site_url'   => home_url(),
            'date'       => current_time( 'Y-m-d H:i:s' ),
        );

        $html = $this->render_template( $template_id, $sample_data );

        wp_send_json_success( array( 'html' => $html ) );
    }

    /**
     * AJAX: 블록 타입 목록
     */
    public function ajax_get_block_types() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        wp_send_json_success( $this->get_block_types() );
    }

    /**
     * AJAX: 프리셋 목록
     */
    public function ajax_get_presets() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        wp_send_json_success( $this->get_presets() );
    }

    /**
     * AJAX: 템플릿 복제
     */
    public function ajax_duplicate_template() {
        check_ajax_referer( 'acf_mail_smtp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-mail-smtp' ) ) );
        }

        $template_id = isset( $_POST['template_id'] ) ? sanitize_key( $_POST['template_id'] ) : '';

        if ( empty( $template_id ) ) {
            wp_send_json_error( array( 'message' => __( '템플릿 ID가 필요합니다.', 'acf-mail-smtp' ) ) );
        }

        $duplicate = $this->duplicate_template( $template_id );

        if ( $duplicate ) {
            wp_send_json_success( array(
                'message'  => __( '템플릿이 복제되었습니다.', 'acf-mail-smtp' ),
                'template' => $duplicate,
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( '템플릿을 찾을 수 없습니다.', 'acf-mail-smtp' ) ) );
        }
    }
}
