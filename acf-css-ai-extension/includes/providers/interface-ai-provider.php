<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * AI Provider Interface
 */
interface JJ_AI_Provider_Interface {
    /**
     * Provider ID (e.g. 'openai', 'anthropic')
     * @return string
     */
    public function get_id();

    /**
     * 표시 이름
     * @return string
     */
    public function get_name();

    /**
     * API Key 검증
     * @param string $api_key
     * @return bool|WP_Error
     */
    public function validate_key( $api_key );

    /**
     * 스타일 제안 생성
     * @param string $prompt 사용자 입력
     * @param array $context 현재 설정 컨텍스트
     * @param string $api_key
     * @return array|WP_Error 결과(explanation, settings_patch)
     */
    public function generate_style( $prompt, $context, $api_key );
}

