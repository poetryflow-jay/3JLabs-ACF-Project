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
     * [v23.0.0] IF-DO 데이터를 nodes 배열로 변환하여 프론트엔드 호환성 보장
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

        // [v23.0.0] IF-DO 데이터가 있고 nodes가 비어있으면 nodes 배열로 변환
        if ( ! empty( $trigger ) && ! empty( $action ) && empty( $nodes ) ) {
            $nodes = array();

            // 트리거 노드 생성
            $nodes[] = array(
                'id'   => 'trigger_' . $post->ID,
                'type' => 'trigger',
                'data' => array(
                    'trigger_id' => $trigger,
                    'settings'   => is_array( $trigger_settings ) ? $trigger_settings : array(),
                ),
            );

            // 액션 노드 생성
            $nodes[] = array(
                'id'   => 'action_' . $post->ID,
                'type' => 'action',
                'data' => array(
                    'action_id' => $action,
                    'settings'  => is_array( $action_settings ) ? $action_settings : array(),
                ),
            );

            // 엣지 생성
            $edges = array(
                array(
                    'id'     => 'edge_' . $post->ID,
                    'source' => 'trigger_' . $post->ID,
                    'target' => 'action_' . $post->ID,
                ),
            );
        }

        // 즉시 실행 여부 결정 (서버측에서 트리거 조건 평가)
        $immediate = $this->should_execute_immediately( $trigger, $trigger_settings );

        return array(
            'id'               => $post->ID,
            'title'            => $post->post_title,
            'trigger'          => $trigger,
            'trigger_settings' => is_array( $trigger_settings ) ? $trigger_settings : array(),
            'action'           => $action,
            'action_settings'  => is_array( $action_settings ) ? $action_settings : array(),
            'enabled'          => $enabled,
            'immediate'        => $immediate,
            // 프론트엔드용 nodes/edges (IF-DO에서 변환된 것 포함)
            'nodes'            => is_array( $nodes ) ? $nodes : array(),
            'edges'            => is_array( $edges ) ? $edges : array(),
            'settings'         => is_array( $settings ) ? $settings : array(),
        );
    }

    /**
     * 트리거가 서버측에서 즉시 실행되어야 하는지 판단
     * [v23.0.0] 방문자 기반 트리거는 서버에서 평가 후 즉시 실행
     */
    private function should_execute_immediately( $trigger_id, $trigger_settings ) {
        // 서버에서 평가하고 즉시 실행할 트리거 목록
        $server_evaluated_triggers = array(
            'first_visit',
            'returning_visitor',
            'visit_count',
            'referrer_type',
            'utm_source',
            'utm_campaign',
            'utm_medium',
            'logged_in',
            'logged_out',
            'user_role',
            'device_type',
            'page_view',
            'specific_page',
        );

        // 클라이언트에서만 감지 가능한 트리거 (즉시 실행 안 함)
        $client_only_triggers = array(
            'exit_intent',
            'scroll_depth',
            'time_on_site',
            'time_on_page',
            'idle_user',
            'tab_return',
            'click_element',
            'delay',
            'timed_delay',
        );

        if ( in_array( $trigger_id, $client_only_triggers, true ) ) {
            return false;
        }

        if ( in_array( $trigger_id, $server_evaluated_triggers, true ) ) {
            // 서버에서 조건 평가
            return $this->evaluate_server_trigger( $trigger_id, $trigger_settings );
        }

        return false;
    }

    /**
     * 서버측 트리거 조건 평가
     * [v23.0.0]
     */
    private function evaluate_server_trigger( $trigger_id, $settings ) {
        $tracker = new ACF_Nudge_Visitor_Tracker();
        $visitor_data = $tracker->get_data();

        switch ( $trigger_id ) {
            case 'first_visit':
                return $visitor_data['is_first_visit'] === true;

            case 'returning_visitor':
                return $visitor_data['is_first_visit'] === false;

            case 'visit_count':
                $target_count = isset( $settings['count'] ) ? intval( $settings['count'] ) : 1;
                $operator = isset( $settings['operator'] ) ? $settings['operator'] : 'gte';
                $current_count = intval( $visitor_data['visit_count'] );

                switch ( $operator ) {
                    case 'eq':
                        return $current_count === $target_count;
                    case 'gt':
                        return $current_count > $target_count;
                    case 'gte':
                        return $current_count >= $target_count;
                    case 'lt':
                        return $current_count < $target_count;
                    case 'lte':
                        return $current_count <= $target_count;
                    default:
                        return $current_count >= $target_count;
                }

            case 'referrer_type':
                $target_type = isset( $settings['type'] ) ? $settings['type'] : '';
                return $visitor_data['referrer_type'] === $target_type;

            case 'utm_source':
                $target_source = isset( $settings['source'] ) ? $settings['source'] : '';
                return ! empty( $target_source ) && isset( $visitor_data['utm']['source'] ) && $visitor_data['utm']['source'] === $target_source;

            case 'utm_campaign':
                $target_campaign = isset( $settings['campaign'] ) ? $settings['campaign'] : '';
                return ! empty( $target_campaign ) && isset( $visitor_data['utm']['campaign'] ) && $visitor_data['utm']['campaign'] === $target_campaign;

            case 'utm_medium':
                $target_medium = isset( $settings['medium'] ) ? $settings['medium'] : '';
                return ! empty( $target_medium ) && isset( $visitor_data['utm']['medium'] ) && $visitor_data['utm']['medium'] === $target_medium;

            case 'logged_in':
                return is_user_logged_in();

            case 'logged_out':
                return ! is_user_logged_in();

            case 'user_role':
                if ( ! is_user_logged_in() ) {
                    return false;
                }
                $target_role = isset( $settings['role'] ) ? $settings['role'] : '';
                $user = wp_get_current_user();
                return in_array( $target_role, $user->roles, true );

            case 'device_type':
                $target_device = isset( $settings['device'] ) ? $settings['device'] : '';
                return $visitor_data['device'] === $target_device;

            case 'page_view':
            case 'specific_page':
                // 항상 true (페이지 로드 시점에 실행)
                return true;

            default:
                return false;
        }
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
