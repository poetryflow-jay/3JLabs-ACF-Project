<?php
/**
 * 워크플로우 매니저 클래스
 *
 * @package ACF_Nudge_Flow
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 워크플로우 매니저
 */
class ACF_Nudge_Workflow_Manager {

    /**
     * 활성 워크플로우 반환
     */
    public function get_active() {
        $workflows = get_posts( array(
            'post_type'      => 'acf_nudge_workflow',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => '_acf_nudge_workflow_enabled',
                    'value'   => '1',
                    'compare' => '=',
                ),
            ),
        ) );

        $result = array();
        foreach ( $workflows as $workflow ) {
            $result[] = $this->format_workflow( $workflow );
        }

        return $result;
    }

    /**
     * 워크플로우 포맷팅
     * [v22.5.0] IF-DO 방식 지원
     */
    private function format_workflow( $post ) {
        // IF-DO 방식 데이터 (우선)
        $trigger = get_post_meta( $post->ID, '_acf_nudge_workflow_trigger', true );
        $trigger_settings = get_post_meta( $post->ID, '_acf_nudge_workflow_trigger_settings', true );
        $action = get_post_meta( $post->ID, '_acf_nudge_workflow_action', true );
        $action_settings = get_post_meta( $post->ID, '_acf_nudge_workflow_action_settings', true );
        $enabled = get_post_meta( $post->ID, '_acf_nudge_workflow_enabled', true ) === '1';
        
        // 하위 호환성: 기존 nodes/edges 방식
        $nodes = get_post_meta( $post->ID, '_acf_nudge_workflow_nodes', true );
        $edges = get_post_meta( $post->ID, '_acf_nudge_workflow_edges', true );
        $settings = get_post_meta( $post->ID, '_acf_nudge_workflow_settings', true );

        return array(
            'id'               => $post->ID,
            'title'            => $post->post_title,
            'trigger'          => $trigger,
            'trigger_settings' => is_array( $trigger_settings ) ? $trigger_settings : array(),
            'action'           => $action,
            'action_settings'  => is_array( $action_settings ) ? $action_settings : array(),
            'enabled'          => $enabled,
            // 하위 호환성
            'nodes'            => is_array( $nodes ) ? $nodes : array(),
            'edges'            => is_array( $edges ) ? $edges : array(),
            'settings'         => is_array( $settings ) ? $settings : array(),
        );
    }

    /**
     * 워크플로우 저장
     * [v22.5.0] IF-DO 방식으로 변경 (trigger, action 직접 저장)
     */
    public function save( $id, $data ) {
        if ( $id ) {
            wp_update_post( array(
                'ID'         => $id,
                'post_title' => sanitize_text_field( $data['title'] ?? '' ),
            ) );
        } else {
            $id = wp_insert_post( array(
                'post_type'   => 'acf_nudge_workflow',
                'post_status' => 'publish',
                'post_title'  => sanitize_text_field( $data['title'] ?? __( '새 워크플로우', 'acf-nudge-flow' ) ),
            ) );
        }

        // IF-DO 방식: 트리거와 액션 직접 저장
        if ( isset( $data['trigger'] ) ) {
            update_post_meta( $id, '_acf_nudge_workflow_trigger', sanitize_text_field( $data['trigger'] ) );
        }

        if ( isset( $data['trigger_settings'] ) && is_array( $data['trigger_settings'] ) ) {
            update_post_meta( $id, '_acf_nudge_workflow_trigger_settings', array_map( 'sanitize_text_field', $data['trigger_settings'] ) );
        }

        if ( isset( $data['action'] ) ) {
            update_post_meta( $id, '_acf_nudge_workflow_action', sanitize_text_field( $data['action'] ) );
        }

        if ( isset( $data['action_settings'] ) && is_array( $data['action_settings'] ) ) {
            // 액션 설정은 다양한 타입이므로 wp_kses_post로 처리
            $sanitized_action_settings = array();
            foreach ( $data['action_settings'] as $key => $value ) {
                if ( is_array( $value ) ) {
                    $sanitized_action_settings[ $key ] = array_map( 'sanitize_text_field', $value );
                } else {
                    $sanitized_action_settings[ $key ] = wp_kses_post( $value );
                }
            }
            update_post_meta( $id, '_acf_nudge_workflow_action_settings', $sanitized_action_settings );
        }

        // 하위 호환성: 기존 nodes/edges 방식도 지원
        if ( isset( $data['nodes'] ) ) {
            update_post_meta( $id, '_acf_nudge_workflow_nodes', $data['nodes'] );
        }

        if ( isset( $data['edges'] ) ) {
            update_post_meta( $id, '_acf_nudge_workflow_edges', $data['edges'] );
        }

        if ( isset( $data['settings'] ) ) {
            update_post_meta( $id, '_acf_nudge_workflow_settings', $data['settings'] );
        }

        if ( isset( $data['enabled'] ) ) {
            update_post_meta( $id, '_acf_nudge_workflow_enabled', $data['enabled'] ? '1' : '0' );
        } elseif ( isset( $data['enabled'] ) && $data['enabled'] === false ) {
            update_post_meta( $id, '_acf_nudge_workflow_enabled', '0' );
        }

        return $id;
    }

    /**
     * 워크플로우 평가 및 실행
     */
    public function evaluate( $workflow_id ) {
        $workflow = $this->get_by_id( $workflow_id );
        if ( ! $workflow ) {
            return false;
        }

        $evaluator = new ACF_Nudge_Condition_Evaluator();
        return $evaluator->evaluate_workflow( $workflow );
    }

    /**
     * ID로 워크플로우 조회
     */
    public function get_by_id( $id ) {
        $post = get_post( $id );
        if ( ! $post || $post->post_type !== 'acf_nudge_workflow' ) {
            return null;
        }

        return $this->format_workflow( $post );
    }

    /**
     * 워크플로우 삭제
     */
    public function delete( $id ) {
        return wp_delete_post( $id, true );
    }

    /**
     * 워크플로우 복제
     */
    public function duplicate( $id ) {
        $original = $this->get_by_id( $id );
        if ( ! $original ) {
            return false;
        }

        $new_data = array(
            'title'    => $original['title'] . ' (복사본)',
            'nodes'    => $original['nodes'],
            'edges'    => $original['edges'],
            'settings' => $original['settings'],
            'enabled'  => false,
        );

        return $this->save( null, $new_data );
    }

    /**
     * 워크플로우 내보내기
     */
    public function export( $id ) {
        $workflow = $this->get_by_id( $id );
        if ( ! $workflow ) {
            return null;
        }

        return json_encode( $workflow, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    /**
     * 워크플로우 가져오기
     */
    public function import( $json ) {
        $data = json_decode( $json, true );
        if ( ! $data || ! isset( $data['nodes'] ) ) {
            return false;
        }

        unset( $data['id'] ); // 새 ID로 생성
        return $this->save( null, $data );
    }
}
