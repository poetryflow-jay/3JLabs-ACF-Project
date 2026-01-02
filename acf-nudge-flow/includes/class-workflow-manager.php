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
     */
    private function format_workflow( $post ) {
        $nodes = get_post_meta( $post->ID, '_acf_nudge_workflow_nodes', true );
        $edges = get_post_meta( $post->ID, '_acf_nudge_workflow_edges', true );
        $settings = get_post_meta( $post->ID, '_acf_nudge_workflow_settings', true );

        return array(
            'id'       => $post->ID,
            'title'    => $post->post_title,
            'nodes'    => is_array( $nodes ) ? $nodes : array(),
            'edges'    => is_array( $edges ) ? $edges : array(),
            'settings' => is_array( $settings ) ? $settings : array(),
        );
    }

    /**
     * 워크플로우 저장
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
