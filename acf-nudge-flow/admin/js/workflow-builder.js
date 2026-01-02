/**
 * ACF Nudge Flow - Workflow Builder (React)
 * 
 * @package ACF_Nudge_Flow
 */

(function() {
    'use strict';

    const { createElement, render, useState, useEffect } = wp.element;
    const { Button, TextControl, SelectControl, Panel, PanelBody } = wp.components;
    const { __ } = wp.i18n;

    /**
     * 워크플로우 빌더 컴포넌트
     */
    const WorkflowBuilder = ({ workflowId, template }) => {
        const [workflow, setWorkflow] = useState({
            title: '',
            nodes: [],
            edges: [],
            enabled: false
        });
        const [triggers, setTriggers] = useState({});
        const [actions, setActions] = useState({});
        const [loading, setLoading] = useState(true);

        useEffect(() => {
            loadData();
        }, []);

        const loadData = async () => {
            try {
                // 트리거/액션 목록 로드
                const triggersRes = await fetch(`${ajaxurl}?action=acf_nudge_get_triggers&nonce=${acfNudgeFlow.nonce}`);
                const triggersData = await triggersRes.json();
                if (triggersData.success) {
                    setTriggers(triggersData.data);
                }

                const actionsRes = await fetch(`${ajaxurl}?action=acf_nudge_get_actions&nonce=${acfNudgeFlow.nonce}`);
                const actionsData = await actionsRes.json();
                if (actionsData.success) {
                    setActions(actionsData.data);
                }

                // 기존 워크플로우 로드
                if (workflowId) {
                    const wfRes = await fetch(`${ajaxurl}?action=acf_nudge_get_workflow&workflow_id=${workflowId}&nonce=${acfNudgeFlow.nonce}`);
                    const wfData = await wfRes.json();
                    if (wfData.success) {
                        setWorkflow(wfData.data);
                    }
                }

                setLoading(false);
            } catch (error) {
                console.error('Failed to load data:', error);
                setLoading(false);
            }
        };

        const saveWorkflow = async () => {
            try {
                const formData = new FormData();
                formData.append('action', 'acf_nudge_save_workflow');
                formData.append('nonce', acfNudgeFlow.nonce);
                formData.append('workflow_id', workflowId || 0);
                formData.append('data', JSON.stringify(workflow));

                const response = await fetch(ajaxurl, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    alert(__('워크플로우가 저장되었습니다.', 'acf-nudge-flow'));
                    if (!workflowId && data.data.id) {
                        window.location.href = `admin.php?page=acf-nudge-flow-builder&id=${data.data.id}`;
                    }
                }
            } catch (error) {
                console.error('Failed to save workflow:', error);
                alert(__('저장 중 오류가 발생했습니다.', 'acf-nudge-flow'));
            }
        };

        if (loading) {
            return createElement('div', { className: 'acf-nudge-builder-loading' },
                createElement('p', null, __('워크플로우 빌더 로딩 중...', 'acf-nudge-flow'))
            );
        }

        return createElement('div', { className: 'acf-nudge-builder' },
            // 헤더
            createElement('div', { className: 'acf-nudge-builder-header', style: { display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '20px', background: '#fff', borderBottom: '1px solid #e2e8f0' } },
                createElement(TextControl, {
                    label: __('워크플로우 이름', 'acf-nudge-flow'),
                    value: workflow.title,
                    onChange: (value) => setWorkflow({ ...workflow, title: value }),
                    placeholder: __('예: 첫 방문자 환영 팝업', 'acf-nudge-flow')
                }),
                createElement(Button, { isPrimary: true, onClick: saveWorkflow }, __('저장', 'acf-nudge-flow'))
            ),

            // 빌더 영역
            createElement('div', { className: 'acf-nudge-builder-main', style: { display: 'flex', minHeight: '500px' } },
                // 좌측 패널 - 노드 팔레트
                createElement('div', { className: 'acf-nudge-builder-palette', style: { width: '250px', background: '#fff', borderRight: '1px solid #e2e8f0', padding: '20px' } },
                    createElement('h3', null, __('트리거', 'acf-nudge-flow')),
                    createElement('p', { style: { color: '#718096', fontSize: '0.9em' } }, __('드래그하여 캔버스에 추가', 'acf-nudge-flow')),
                    Object.entries(triggers).slice(0, 5).map(([id, trigger]) =>
                        createElement('div', { key: id, className: 'palette-item', style: { padding: '10px', background: '#f7fafc', borderRadius: '8px', marginBottom: '8px', cursor: 'grab' } },
                            createElement('span', null, trigger.icon + ' ' + trigger.label)
                        )
                    ),

                    createElement('h3', { style: { marginTop: '20px' } }, __('액션', 'acf-nudge-flow')),
                    Object.entries(actions).slice(0, 5).map(([id, action]) =>
                        createElement('div', { key: id, className: 'palette-item', style: { padding: '10px', background: '#f7fafc', borderRadius: '8px', marginBottom: '8px', cursor: 'grab' } },
                            createElement('span', null, action.icon + ' ' + action.label)
                        )
                    )
                ),

                // 중앙 - 캔버스
                createElement('div', { className: 'acf-nudge-builder-canvas', style: { flex: 1, background: '#f0f2f5', padding: '20px' } },
                    createElement('div', { style: { textAlign: 'center', color: '#718096', paddingTop: '100px' } },
                        createElement('p', { style: { fontSize: '3em', marginBottom: '20px' } }, '🚀'),
                        createElement('h3', null, __('워크플로우를 만들어보세요', 'acf-nudge-flow')),
                        createElement('p', null, __('왼쪽 패널에서 트리거와 액션을 드래그하여 자동화를 구성하세요.', 'acf-nudge-flow'))
                    )
                ),

                // 우측 패널 - 설정
                createElement('div', { className: 'acf-nudge-builder-settings', style: { width: '300px', background: '#fff', borderLeft: '1px solid #e2e8f0', padding: '20px' } },
                    createElement('h3', null, __('설정', 'acf-nudge-flow')),
                    createElement('p', { style: { color: '#718096', fontSize: '0.9em' } }, __('노드를 선택하면 상세 설정이 표시됩니다.', 'acf-nudge-flow'))
                )
            )
        );
    };

    // 마운트
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('acf-nudge-workflow-builder');
        if (container) {
            const workflowId = container.dataset.workflowId || null;
            const template = container.dataset.template || null;
            render(createElement(WorkflowBuilder, { workflowId, template }), container);
        }
    });

})();
