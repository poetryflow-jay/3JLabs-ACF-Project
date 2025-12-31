<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once dirname( __FILE__ ) . '/interface-ai-provider.php';

/**
 * OpenAI Provider Implementation
 */
class JJ_AI_Provider_OpenAI implements JJ_AI_Provider_Interface {

    private $api_endpoint = 'https://api.openai.com/v1/chat/completions';
    private $model = 'gpt-3.5-turbo'; // 기본값 (설정으로 변경 가능)

    public function get_id() {
        return 'openai';
    }

    public function get_name() {
        return 'OpenAI (GPT-3.5/4)';
    }

    public function validate_key( $api_key ) {
        if ( empty( $api_key ) || strpos( $api_key, 'sk-' ) !== 0 ) {
            return new WP_Error( 'invalid_key', 'API Key가 올바르지 않습니다. (sk-로 시작해야 함)' );
        }
        return true;
    }

    public function generate_style( $prompt, $context, $api_key ) {
        // [Final Polish] 데모 모드 (키 검사 전에 체크하여 키 없이도 작동 가능하게 함)
        if ( strpos( $prompt, 'demo:' ) === 0 ) {
            return $this->get_demo_response( $prompt );
        }

        if ( empty( $api_key ) ) {
            return new WP_Error( 'no_key', 'API Key가 설정되지 않았습니다.' );
        }

        $system_prompt = $this->get_system_prompt( $context );
        
        $body = array(
            'model'       => $this->model,
            'messages'    => array(
                array( 'role' => 'system', 'content' => $system_prompt ),
                array( 'role' => 'user', 'content' => $prompt ),
            ),
            'temperature' => 0.7,
            'max_tokens'  => 1500,
            'response_format' => array( 'type' => 'json_object' ), // JSON 강제
        );

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type'  => 'application/json',
            ),
            'body'    => wp_json_encode( $body ),
            'timeout' => 30,
        );

        $response = wp_remote_post( $this->api_endpoint, $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( $code !== 200 ) {
            $msg = isset( $data['error']['message'] ) ? $data['error']['message'] : 'API 요청 실패';
            return new WP_Error( 'api_error', 'OpenAI Error: ' . $msg, array( 'status' => $code ) );
        }

        if ( empty( $data['choices'][0]['message']['content'] ) ) {
            return new WP_Error( 'empty_response', 'AI 응답이 비어있습니다.' );
        }

        $content = $data['choices'][0]['message']['content'];
        $parsed = json_decode( $content, true );

        if ( ! is_array( $parsed ) || ! isset( $parsed['settings_patch'] ) ) {
            return new WP_Error( 'invalid_json', 'AI 응답 형식이 올바르지 않습니다.' );
        }

        return array(
            'explanation'    => isset( $parsed['explanation'] ) ? $parsed['explanation'] : '',
            'settings_patch' => $parsed['settings_patch'],
        );
    }

    /**
     * [Final Polish] 마케팅 스크린샷용 데모 응답 생성
     */
    private function get_demo_response( $prompt ) {
        $patch = array();
        $explanation = '';

        if ( strpos( $prompt, 'dark' ) !== false ) {
            $patch = array(
                'palettes' => array(
                    'brand' => array(
                        'primary_color' => '#ffcc00', // Gold
                        'primary_color_hover' => '#e6b800',
                        'secondary_color' => '#333333',
                        'secondary_color_hover' => '#111111',
                    )
                ),
                'buttons' => array(
                    'primary' => array(
                        'background_color' => '#ffcc00',
                        'background_color_hover' => '#e6b800',
                        'text_color' => '#000000',
                    )
                ),
                'forms' => array(
                    'field' => array(
                        'border_color_focus' => '#ffcc00',
                    )
                )
            );
            $explanation = '[Demo Mode] "다크 모드" 요청을 감지했습니다. 고급스러운 블랙 배경에 골드 포인트를 매치하여 프리미엄 느낌을 연출했습니다.';
        } else {
            $patch = array(
                'palettes' => array(
                    'brand' => array(
                        'primary_color' => '#3498db', // Blue
                        'primary_color_hover' => '#2980b9',
                    )
                ),
                'buttons' => array(
                    'primary' => array(
                        'background_color' => '#3498db',
                        'background_color_hover' => '#2980b9',
                    )
                ),
                'forms' => array(
                    'field' => array(
                        'border_color_focus' => '#3498db',
                    )
                )
            );
            $explanation = '[Demo Mode] 데모 요청입니다. 신뢰감을 주는 블루 톤으로 변경했습니다.';
        }

        // 지연 효과 (Spinner 확인용)
        sleep(1);

        return array(
            'explanation' => $explanation,
            'settings_patch' => $patch,
        );
    }

    private function get_system_prompt( $context ) {
        // 현재 설정 컨텍스트 요약
        $context_json = wp_json_encode( $context );
        
        return <<<EOT
너는 WordPress 'ACF CSS Manager' 플러그인의 스타일 전문가 AI야.
사용자의 프롬프트(분위기, 브랜드, 업종 등)를 분석해서 JSON 포맷으로 스타일 설정(settings_patch)을 제안해야 해.

[제약 사항]
1. 반드시 유효한 JSON 객체를 반환할 것.
2. 최상위 키는 "explanation"(변경 이유 요약, 한국어)과 "settings_patch"(설정 객체)여야 함.
3. settings_patch는 아래 구조를 따름 (일부만 포함 가능):
   - palettes: { brand: { primary_color, primary_color_hover, secondary_color, ... } }
   - typography: { h1: { font_family, font_size, ... }, body: { ... } }
   - buttons: { primary: { background_color, background_color_hover, border_color, border_color_hover, text_color, text_color_hover }, text: { text_color, text_color_hover } }
   - forms: { field: { border_color_focus } }
4. 색상은 반드시 Hex Code(#RRGGBB) 형태여야 함.
5. 현재 설정 컨텍스트를 참고하여, 변경이 필요한 부분만 settings_patch에 포함할 것.

[현재 설정 컨텍스트]
{$context_json}
EOT;
    }
}
