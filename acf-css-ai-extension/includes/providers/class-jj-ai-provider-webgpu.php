<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once dirname( __FILE__ ) . '/interface-ai-provider.php';

/**
 * WebGPU Provider (Browser-side via WebLLM)
 *
 * - 실제 추론은 브라우저(JS)에서 수행됩니다.
 * - 서버(AJAX) 경로로 호출되면 안내 메시지를 반환합니다.
 */
class JJ_AI_Provider_WebGPU implements JJ_AI_Provider_Interface {

    public function get_id() {
        return 'webgpu';
    }

    public function get_name() {
        return 'WebGPU (Browser Local / WebLLM)';
    }

    public function validate_key( $api_key ) {
        // WebGPU는 API Key가 필요 없음
        return true;
    }

    public function generate_style( $prompt, $context, $api_key ) {
        return new WP_Error(
            'webgpu_client_side',
            'WebGPU(브라우저 로컬) 모드는 서버에서 생성하지 않습니다. 이 페이지에서 “Gemma 모델 로드” 후 “AI 제안 생성”을 눌러주세요.'
        );
    }
}

