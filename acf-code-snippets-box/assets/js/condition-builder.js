/**
 * ACF Code Snippets Box - Condition Builder UI
 *
 * 조건 빌더 관리자 인터페이스
 *
 * @package ACF_Code_Snippets_Box
 */

(function($) {
    'use strict';

    /**
     * 조건 빌더 클래스
     */
    class ACFCSBConditionBuilder {
        constructor(container) {
            this.$container = $(container);
            this.$groupsContainer = this.$container.find('.acf-csb-condition-groups');
            this.$hiddenInput = this.$container.find('input[name="acf_csb_conditions"]');
            
            this.conditions = [];
            this.conditionTypes = window.acfCsbConditionTypes || {};
            this.licenseType = window.acfCsbLicenseType || 'free';
            
            this.init();
        }

        init() {
            this.loadExistingConditions();
            this.bindEvents();
            this.render();
        }

        /**
         * 기존 조건 로드
         */
        loadExistingConditions() {
            const savedConditions = this.$hiddenInput.val();
            if (savedConditions) {
                try {
                    this.conditions = JSON.parse(savedConditions);
                } catch (e) {
                    this.conditions = [];
                }
            }

            // 기본 그룹이 없으면 추가
            if (this.conditions.length === 0) {
                this.conditions = [{
                    logic: 'AND',
                    rules: []
                }];
            }
        }

        /**
         * 이벤트 바인딩
         */
        bindEvents() {
            const self = this;

            // 조건 추가
            this.$container.on('click', '.acf-csb-add-condition', function(e) {
                e.preventDefault();
                const groupIndex = $(this).closest('.acf-csb-condition-group').data('group-index');
                self.addCondition(groupIndex);
            });

            // 그룹 추가
            this.$container.on('click', '.acf-csb-add-group', function(e) {
                e.preventDefault();
                self.addGroup();
            });

            // 조건 삭제
            this.$container.on('click', '.acf-csb-condition-remove', function(e) {
                e.preventDefault();
                const $row = $(this).closest('.acf-csb-condition-row');
                const groupIndex = $row.closest('.acf-csb-condition-group').data('group-index');
                const conditionIndex = $row.data('condition-index');
                self.removeCondition(groupIndex, conditionIndex);
            });

            // 그룹 삭제
            this.$container.on('click', '.acf-csb-group-remove', function(e) {
                e.preventDefault();
                const groupIndex = $(this).closest('.acf-csb-condition-group').data('group-index');
                self.removeGroup(groupIndex);
            });

            // 조건 타입 변경
            this.$container.on('change', '.acf-csb-condition-type', function() {
                const $row = $(this).closest('.acf-csb-condition-row');
                const groupIndex = $row.closest('.acf-csb-condition-group').data('group-index');
                const conditionIndex = $row.data('condition-index');
                const newType = $(this).val();
                
                self.updateConditionType(groupIndex, conditionIndex, newType);
            });

            // 연산자 변경
            this.$container.on('change', '.acf-csb-condition-operator', function() {
                const $row = $(this).closest('.acf-csb-condition-row');
                const groupIndex = $row.closest('.acf-csb-condition-group').data('group-index');
                const conditionIndex = $row.data('condition-index');
                
                self.conditions[groupIndex].rules[conditionIndex].operator = $(this).val();
                self.saveConditions();
            });

            // 값 변경
            this.$container.on('change keyup', '.acf-csb-condition-value', function() {
                const $row = $(this).closest('.acf-csb-condition-row');
                const groupIndex = $row.closest('.acf-csb-condition-group').data('group-index');
                const conditionIndex = $row.data('condition-index');
                
                self.conditions[groupIndex].rules[conditionIndex].value = $(this).val();
                self.saveConditions();
            });

            // 그룹 로직 변경
            this.$container.on('change', '.acf-csb-group-logic', function() {
                const groupIndex = $(this).closest('.acf-csb-condition-group').data('group-index');
                self.conditions[groupIndex].logic = $(this).val();
                self.saveConditions();
                self.render();
            });

            // 조건 테스트
            this.$container.on('click', '.acf-csb-test-conditions', function(e) {
                e.preventDefault();
                self.testConditions();
            });
        }

        /**
         * 그룹 추가
         */
        addGroup() {
            this.conditions.push({
                logic: 'AND',
                rules: []
            });
            this.saveConditions();
            this.render();
        }

        /**
         * 그룹 삭제
         */
        removeGroup(groupIndex) {
            if (this.conditions.length <= 1) {
                alert(acfCsbAdmin.i18n.minOneGroup || '최소 하나의 조건 그룹이 필요합니다.');
                return;
            }
            this.conditions.splice(groupIndex, 1);
            this.saveConditions();
            this.render();
        }

        /**
         * 조건 추가
         */
        addCondition(groupIndex) {
            const newCondition = {
                type: 'post_type',
                operator: 'is',
                value: ''
            };
            
            this.conditions[groupIndex].rules.push(newCondition);
            this.saveConditions();
            this.render();
        }

        /**
         * 조건 삭제
         */
        removeCondition(groupIndex, conditionIndex) {
            this.conditions[groupIndex].rules.splice(conditionIndex, 1);
            this.saveConditions();
            this.render();
        }

        /**
         * 조건 타입 업데이트
         */
        updateConditionType(groupIndex, conditionIndex, newType) {
            const typeConfig = this.conditionTypes[newType] || {};
            
            this.conditions[groupIndex].rules[conditionIndex] = {
                type: newType,
                operator: typeConfig.defaultOperator || 'is',
                value: ''
            };
            
            this.saveConditions();
            this.render();
        }

        /**
         * 조건 저장
         */
        saveConditions() {
            this.$hiddenInput.val(JSON.stringify(this.conditions));
        }

        /**
         * 렌더링
         */
        render() {
            const self = this;
            let html = '';

            this.conditions.forEach((group, groupIndex) => {
                html += self.renderGroup(group, groupIndex);
                
                // 그룹 사이 OR 표시
                if (groupIndex < self.conditions.length - 1) {
                    html += `
                        <div class="acf-csb-condition-logic">
                            <span class="acf-csb-condition-logic-label or">OR</span>
                        </div>
                    `;
                }
            });

            html += `
                <div class="acf-csb-condition-actions">
                    <button type="button" class="acf-csb-add-group">
                        <span class="dashicons dashicons-plus-alt2"></span>
                        ${acfCsbAdmin.i18n.addGroup || '그룹 추가 (OR)'}
                    </button>
                    <button type="button" class="acf-csb-test-conditions button">
                        <span class="dashicons dashicons-yes-alt"></span>
                        ${acfCsbAdmin.i18n.testConditions || '조건 테스트'}
                    </button>
                </div>
            `;

            this.$groupsContainer.html(html);
            this.updatePreview();
        }

        /**
         * 그룹 렌더링
         */
        renderGroup(group, groupIndex) {
            const self = this;
            let rulesHtml = '';

            group.rules.forEach((rule, ruleIndex) => {
                rulesHtml += self.renderConditionRow(rule, groupIndex, ruleIndex);
                
                // 조건 사이 AND 표시
                if (ruleIndex < group.rules.length - 1) {
                    rulesHtml += `
                        <div class="acf-csb-condition-logic">
                            <span class="acf-csb-condition-logic-label and">AND</span>
                        </div>
                    `;
                }
            });

            return `
                <div class="acf-csb-condition-group" data-group-index="${groupIndex}">
                    <div class="acf-csb-condition-group-header">
                        <span class="acf-csb-condition-group-title">
                            ${acfCsbAdmin.i18n.conditionGroup || '조건 그룹'} ${groupIndex + 1}
                        </span>
                        <div class="acf-csb-condition-group-actions">
                            <button type="button" class="acf-csb-group-remove" title="${acfCsbAdmin.i18n.removeGroup || '그룹 삭제'}">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="acf-csb-conditions">
                        ${rulesHtml}
                    </div>
                    
                    <button type="button" class="acf-csb-add-condition">
                        <span class="dashicons dashicons-plus"></span>
                        ${acfCsbAdmin.i18n.addCondition || '조건 추가'}
                    </button>
                </div>
            `;
        }

        /**
         * 조건 행 렌더링
         */
        renderConditionRow(rule, groupIndex, ruleIndex) {
            const typeOptions = this.renderTypeOptions(rule.type);
            const operatorOptions = this.renderOperatorOptions(rule.type, rule.operator);
            const valueInput = this.renderValueInput(rule.type, rule.value);

            return `
                <div class="acf-csb-condition-row" data-condition-index="${ruleIndex}">
                    <select class="acf-csb-condition-type">
                        ${typeOptions}
                    </select>
                    <select class="acf-csb-condition-operator">
                        ${operatorOptions}
                    </select>
                    ${valueInput}
                    <button type="button" class="acf-csb-condition-remove" title="${acfCsbAdmin.i18n.removeCondition || '조건 삭제'}">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                </div>
            `;
        }

        /**
         * 타입 옵션 렌더링
         */
        renderTypeOptions(selectedType) {
            const self = this;
            let html = '';
            let currentCategory = '';

            // 카테고리별로 그룹화
            const categories = {
                basic: { label: '기본', types: [] },
                page: { label: '페이지/포스트', types: [] },
                user: { label: '사용자', types: [] },
                device: { label: '기기/환경', types: [] },
                time: { label: '시간', types: [] },
                woocommerce: { label: 'WooCommerce', types: [] },
                acf: { label: 'ACF', types: [] },
                facetwp: { label: 'FacetWP', types: [] },
                advanced: { label: '고급', types: [] }
            };

            Object.entries(this.conditionTypes).forEach(([key, config]) => {
                const cat = config.category || 'basic';
                if (categories[cat]) {
                    categories[cat].types.push({ key, config });
                }
            });

            Object.entries(categories).forEach(([catKey, catData]) => {
                if (catData.types.length === 0) return;

                html += `<optgroup label="${catData.label}">`;
                
                catData.types.forEach(({ key, config }) => {
                    const isLocked = config.pro_only && !self.hasAccess(config.min_tier);
                    const lockIcon = isLocked ? ' 🔒' : '';
                    const disabled = isLocked ? ' disabled' : '';
                    const selected = key === selectedType ? ' selected' : '';
                    
                    html += `<option value="${key}"${selected}${disabled}>${config.name}${lockIcon}</option>`;
                });
                
                html += '</optgroup>';
            });

            return html;
        }

        /**
         * 연산자 옵션 렌더링
         */
        renderOperatorOptions(type, selectedOperator) {
            const typeConfig = this.conditionTypes[type] || {};
            const operators = typeConfig.operators || ['is', 'is_not'];
            
            const operatorLabels = {
                'is': '같음',
                'is_not': '같지 않음',
                '>': '보다 큼',
                '>=': '이상',
                '<': '보다 작음',
                '<=': '이하',
                'contains': '포함',
                'not_contains': '미포함',
                'starts_with': '시작',
                'ends_with': '끝남',
                'matches': '정규식 매치'
            };

            let html = '';
            operators.forEach(op => {
                const selected = op === selectedOperator ? ' selected' : '';
                html += `<option value="${op}"${selected}>${operatorLabels[op] || op}</option>`;
            });

            return html;
        }

        /**
         * 값 입력 렌더링
         */
        renderValueInput(type, value) {
            const typeConfig = this.conditionTypes[type] || {};
            const inputType = typeConfig.valueType || 'text';

            switch (inputType) {
                case 'select':
                    return this.renderSelectInput(type, typeConfig.options || [], value);
                
                case 'multiselect':
                    return this.renderMultiSelectInput(type, typeConfig.options || [], value);
                
                case 'number':
                    return `<input type="number" class="acf-csb-condition-value" value="${this.escapeHtml(value)}" placeholder="${typeConfig.placeholder || ''}">`;
                
                case 'time':
                    return `<input type="time" class="acf-csb-condition-value" value="${this.escapeHtml(value)}">`;
                
                case 'none':
                    return '<span class="acf-csb-condition-value-none"></span>';
                
                default:
                    return `<input type="text" class="acf-csb-condition-value" value="${this.escapeHtml(value)}" placeholder="${typeConfig.placeholder || '값 입력'}">`;
            }
        }

        /**
         * 셀렉트 입력 렌더링
         */
        renderSelectInput(type, options, selectedValue) {
            let html = '<select class="acf-csb-condition-value">';
            html += '<option value="">선택...</option>';
            
            if (typeof options === 'function') {
                // 동적 옵션 (AJAX 로드)
                options = this.loadDynamicOptions(type) || [];
            }

            options.forEach(opt => {
                const optValue = typeof opt === 'object' ? opt.value : opt;
                const optLabel = typeof opt === 'object' ? opt.label : opt;
                const selected = optValue === selectedValue ? ' selected' : '';
                html += `<option value="${this.escapeHtml(optValue)}"${selected}>${this.escapeHtml(optLabel)}</option>`;
            });

            html += '</select>';
            return html;
        }

        /**
         * 멀티셀렉트 입력 렌더링
         */
        renderMultiSelectInput(type, options, selectedValues) {
            const values = Array.isArray(selectedValues) ? selectedValues : [];
            let html = '<select class="acf-csb-condition-value" multiple>';
            
            options.forEach(opt => {
                const optValue = typeof opt === 'object' ? opt.value : opt;
                const optLabel = typeof opt === 'object' ? opt.label : opt;
                const selected = values.includes(optValue) ? ' selected' : '';
                html += `<option value="${this.escapeHtml(optValue)}"${selected}>${this.escapeHtml(optLabel)}</option>`;
            });

            html += '</select>';
            return html;
        }

        /**
         * 동적 옵션 로드
         */
        loadDynamicOptions(type) {
            // 캐시된 옵션 반환
            if (this.optionsCache && this.optionsCache[type]) {
                return this.optionsCache[type];
            }

            // AJAX로 옵션 로드 (비동기이므로 초기에는 빈 배열)
            this.fetchDynamicOptions(type);
            return [];
        }

        /**
         * 동적 옵션 AJAX 로드
         */
        fetchDynamicOptions(type) {
            const self = this;

            $.ajax({
                url: acfCsbAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'acf_csb_get_condition_options',
                    nonce: acfCsbAdmin.nonce,
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        self.optionsCache = self.optionsCache || {};
                        self.optionsCache[type] = response.data;
                        self.render(); // 다시 렌더링
                    }
                }
            });
        }

        /**
         * 라이선스 접근 권한 확인
         */
        hasAccess(minTier) {
            const tiers = ['free', 'basic', 'premium', 'unlimited', 'partner', 'master'];
            const currentTierIndex = tiers.indexOf(this.licenseType.toLowerCase());
            const requiredTierIndex = tiers.indexOf(minTier.toLowerCase());
            
            return currentTierIndex >= requiredTierIndex;
        }

        /**
         * 미리보기 업데이트
         */
        updatePreview() {
            const $preview = this.$container.find('.acf-csb-condition-preview');
            if ($preview.length === 0) return;

            let previewText = '';
            
            this.conditions.forEach((group, gIndex) => {
                if (group.rules.length === 0) return;

                if (gIndex > 0) {
                    previewText += ' <strong>OR</strong> ';
                }

                previewText += '(';
                group.rules.forEach((rule, rIndex) => {
                    if (rIndex > 0) {
                        previewText += ' <strong>AND</strong> ';
                    }
                    
                    const typeConfig = this.conditionTypes[rule.type] || {};
                    previewText += `<code>${typeConfig.name || rule.type}</code> ${rule.operator} "${rule.value}"`;
                });
                previewText += ')';
            });

            if (previewText) {
                $preview.find('.acf-csb-condition-preview-content').html(previewText);
                $preview.show();
            } else {
                $preview.hide();
            }
        }

        /**
         * 조건 테스트
         */
        testConditions() {
            const self = this;
            const $testBtn = this.$container.find('.acf-csb-test-conditions');
            const $result = this.$container.find('.acf-csb-condition-test-result');

            $testBtn.prop('disabled', true).text('테스트 중...');

            $.ajax({
                url: acfCsbAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'acf_csb_test_conditions',
                    nonce: acfCsbAdmin.nonce,
                    conditions: JSON.stringify(self.conditions)
                },
                success: function(response) {
                    if (response.success) {
                        const passed = response.data.passed;
                        $result.removeClass('pass fail')
                               .addClass(passed ? 'pass' : 'fail')
                               .html(passed ? '✓ 조건 충족' : '✗ 조건 불충족')
                               .show();
                    }
                },
                complete: function() {
                    $testBtn.prop('disabled', false).html(
                        '<span class="dashicons dashicons-yes-alt"></span> ' +
                        (acfCsbAdmin.i18n.testConditions || '조건 테스트')
                    );
                }
            });
        }

        /**
         * HTML 이스케이프
         */
        escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }
    }

    /**
     * jQuery 플러그인으로 등록
     */
    $.fn.acfCsbConditionBuilder = function() {
        return this.each(function() {
            if (!$(this).data('acf-csb-condition-builder')) {
                $(this).data('acf-csb-condition-builder', new ACFCSBConditionBuilder(this));
            }
        });
    };

    /**
     * DOM Ready
     */
    $(document).ready(function() {
        // 조건 빌더 초기화
        $('#acf-csb-condition-builder').acfCsbConditionBuilder();
    });

})(jQuery);
