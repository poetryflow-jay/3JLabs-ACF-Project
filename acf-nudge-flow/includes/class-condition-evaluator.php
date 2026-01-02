<?php
/**
 * 조건 평가기 클래스
 *
 * @package ACF_Nudge_Flow
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 조건 평가기
 */
class ACF_Nudge_Condition_Evaluator {

    /**
     * 워크플로우 평가
     */
    public function evaluate_workflow( $workflow ) {
        if ( empty( $workflow['nodes'] ) ) {
            return array();
        }

        $trigger_manager = new ACF_Nudge_Trigger_Manager();
        $actions_to_execute = array();

        // 노드 순회
        foreach ( $workflow['nodes'] as $node ) {
            if ( $node['type'] === 'trigger' ) {
                // 트리거 평가
                $trigger_id = $node['data']['trigger_id'] ?? '';
                $settings = $node['data']['settings'] ?? array();

                if ( $trigger_manager->evaluate( $trigger_id, $settings ) ) {
                    // 연결된 액션 찾기
                    $actions = $this->find_connected_actions( $node['id'], $workflow );
                    $actions_to_execute = array_merge( $actions_to_execute, $actions );
                }
            }
        }

        return $actions_to_execute;
    }

    /**
     * 연결된 액션 찾기
     */
    private function find_connected_actions( $node_id, $workflow ) {
        $actions = array();
        $edges = $workflow['edges'] ?? array();
        $nodes = $workflow['nodes'] ?? array();

        // 해당 노드에서 나가는 엣지 찾기
        foreach ( $edges as $edge ) {
            if ( $edge['source'] === $node_id ) {
                $target_id = $edge['target'];

                // 타겟 노드 찾기
                foreach ( $nodes as $node ) {
                    if ( $node['id'] === $target_id ) {
                        if ( $node['type'] === 'action' ) {
                            $actions[] = array(
                                'action_id' => $node['data']['action_id'] ?? '',
                                'settings'  => $node['data']['settings'] ?? array(),
                            );
                        } elseif ( $node['type'] === 'condition' ) {
                            // 조건 노드면 조건 평가 후 연결된 액션 찾기
                            if ( $this->evaluate_condition( $node ) ) {
                                $nested_actions = $this->find_connected_actions( $target_id, $workflow );
                                $actions = array_merge( $actions, $nested_actions );
                            }
                        }
                    }
                }
            }
        }

        return $actions;
    }

    /**
     * 조건 노드 평가
     */
    private function evaluate_condition( $node ) {
        $condition_type = $node['data']['condition_type'] ?? 'all';
        $conditions = $node['data']['conditions'] ?? array();

        if ( empty( $conditions ) ) {
            return true;
        }

        $trigger_manager = new ACF_Nudge_Trigger_Manager();

        if ( $condition_type === 'all' ) {
            // AND 로직
            foreach ( $conditions as $condition ) {
                if ( ! $trigger_manager->evaluate( $condition['trigger_id'], $condition['settings'] ?? array() ) ) {
                    return false;
                }
            }
            return true;
        } else {
            // OR 로직
            foreach ( $conditions as $condition ) {
                if ( $trigger_manager->evaluate( $condition['trigger_id'], $condition['settings'] ?? array() ) ) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * 단일 조건 평가
     */
    public function evaluate_single( $trigger_id, $settings = array(), $operator = '==', $value = null ) {
        $trigger_manager = new ACF_Nudge_Trigger_Manager();
        $result = $trigger_manager->evaluate( $trigger_id, $settings );

        if ( $value !== null ) {
            switch ( $operator ) {
                case '==':
                    return $result == $value;
                case '!=':
                    return $result != $value;
                case '>':
                    return $result > $value;
                case '<':
                    return $result < $value;
                case '>=':
                    return $result >= $value;
                case '<=':
                    return $result <= $value;
                case 'contains':
                    return is_string( $result ) && strpos( $result, $value ) !== false;
                case 'not_contains':
                    return is_string( $result ) && strpos( $result, $value ) === false;
            }
        }

        return (bool) $result;
    }
}
