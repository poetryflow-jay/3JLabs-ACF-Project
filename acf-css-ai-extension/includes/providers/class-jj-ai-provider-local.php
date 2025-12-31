<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once dirname( __FILE__ ) . '/interface-ai-provider.php';

/**
 * Local AI Provider Implementation (Gemma 3)
 */
class JJ_AI_Provider_Local implements JJ_AI_Provider_Interface {

    private $api_endpoint = 'http://127.0.0.1:5000/generate';
    private $server_status_url = 'http://127.0.0.1:5000/status';

    public function get_id() {
        return 'local';
    }

    public function get_name() {
        return 'Local (Gemma 3 4B)';
    }

    public function validate_key( $api_key ) {
        // 로컬 모델은 API Key가 필요 없지만, 서버가 실행 중인지 체크하는 용도로 활용 가능
        $response = wp_remote_get( $this->server_status_url, array( 'timeout' => 5 ) );
        
        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'server_down', '로컬 AI 서버가 꺼져 있습니다.<br>플러그인 폴더 내 <code>local-server/run_server_with_launcher_env.bat</code> 파일을 실행해주세요.' );
        }

        $code = wp_remote_retrieve_response_code( $response );
        if ( $code !== 200 ) {
            return new WP_Error( 'server_error', '로컬 AI 서버 상태가 비정상입니다.' );
        }

        return true;
    }

    public function generate_style( $prompt, $context, $api_key ) {
        // 서버 상태 확인
        $status_check = $this->validate_key( '' );
        if ( is_wp_error( $status_check ) ) {
            return $status_check;
        }

        $system_prompt = $this->get_system_prompt( $context );
        
        // Gemma 3 Chat Template 구성
        // <start_of_turn>user\n{system_prompt}\n\n{user_prompt}<end_of_turn>\n<start_of_turn>model\n
        $full_prompt = "<start_of_turn>user\n" . $system_prompt . "\n\n" . "사용자 요청: " . $prompt . "<end_of_turn>\n<start_of_turn>model\n";

        $body = array(
            'prompt' => $full_prompt
        );

        $args = array(
            'headers' => array(
                'Content-Type'  => 'application/json',
            ),
            'body'    => wp_json_encode( $body ),
            'timeout' => 60, // 로컬 추론은 시간이 걸릴 수 있음
        );

        $response = wp_remote_post( $this->api_endpoint, $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( $code !== 200 ) {
            $msg = isset( $data['error'] ) ? $data['error'] : 'API 요청 실패';
            return new WP_Error( 'api_error', 'Local Server Error: ' . $msg, array( 'status' => $code ) );
        }

        if ( empty( $data['response'] ) ) {
            return new WP_Error( 'empty_response', 'AI 응답이 비어있습니다.' );
        }

        // 응답에서 JSON 파싱
        $raw_content = $data['response'];
        
        // JSON 블록 추출 (```json ... ``` 또는 단순 {...})
        $json_str = $this->extract_json( $raw_content );
        
        if ( ! $json_str ) {
             return new WP_Error( 'invalid_json', 'AI 응답에서 유효한 JSON을 찾을 수 없습니다: ' . substr($raw_content, 0, 100) . '...' );
        }

        $parsed = json_decode( $json_str, true );

        if ( ! is_array( $parsed ) || ! isset( $parsed['settings_patch'] ) ) {
            return new WP_Error( 'invalid_schema', 'AI 응답 형식이 스키마와 일치하지 않습니다.' );
        }

        return array(
            'explanation'    => isset( $parsed['explanation'] ) ? $parsed['explanation'] : '',
            'settings_patch' => $parsed['settings_patch'],
        );
    }

    private function get_system_prompt( $context ) {
        // 현재 설정 컨텍스트 요약
        $context_json = wp_json_encode( $context );
        
        return <<<EOT
너는 WordPress 'ACF CSS Manager' 플러그인의 스타일 전문가 AI야.
사용자의 프롬프트(분위기, 브랜드, 업종 등)를 분석해서 JSON 포맷으로 스타일 설정(settings_patch)을 제안해야 해.

[제약 사항]
1. 반드시 유효한 JSON 객체만 출력할 것. 마크다운이나 부가 설명 없이 JSON만 출력해.
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

    private function extract_json( $text ) {
        // Markdown Code Block 제거
        if ( preg_match( '/```json\s*(\{.*?\})\s*```/s', $text, $matches ) ) {
            return $matches[1];
        }
        // 그냥 중괄호 찾기
        if ( preg_match( '/(\{.*\})/s', $text, $matches ) ) {
            return $matches[1];
        }
        return $text; // 그대로 시도
    }
}

