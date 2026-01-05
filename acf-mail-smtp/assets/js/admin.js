/**
 * ACF Mail SMTP Admin JavaScript
 * Drag & Drop Form Builder
 *
 * @package ACF_Mail_SMTP
 * @version 2.0.0
 * @author 3J Labs
 */

(function($) {
    'use strict';

    /**
     * Field Types Configuration
     */
    const FIELD_TYPES = {
        text: {
            label: '텍스트',
            icon: '📝',
            category: 'basic',
            hasOptions: false,
            defaultSettings: {
                placeholder: '',
                maxLength: '',
                minLength: '',
                pattern: ''
            }
        },
        email: {
            label: '이메일',
            icon: '📧',
            category: 'basic',
            hasOptions: false,
            defaultSettings: {
                placeholder: '이메일 주소를 입력하세요'
            }
        },
        textarea: {
            label: '텍스트 영역',
            icon: '📄',
            category: 'basic',
            hasOptions: false,
            defaultSettings: {
                placeholder: '',
                rows: 4,
                maxLength: ''
            }
        },
        number: {
            label: '숫자',
            icon: '🔢',
            category: 'basic',
            hasOptions: false,
            defaultSettings: {
                placeholder: '',
                min: '',
                max: '',
                step: '1'
            }
        },
        tel: {
            label: '전화번호',
            icon: '📱',
            category: 'basic',
            hasOptions: false,
            defaultSettings: {
                placeholder: '010-0000-0000',
                pattern: ''
            }
        },
        url: {
            label: 'URL',
            icon: '🔗',
            category: 'basic',
            hasOptions: false,
            defaultSettings: {
                placeholder: 'https://'
            }
        },
        select: {
            label: '드롭다운 선택',
            icon: '📋',
            category: 'choice',
            hasOptions: true,
            defaultSettings: {
                placeholder: '선택하세요',
                multiple: false
            }
        },
        checkbox: {
            label: '체크박스',
            icon: '☑️',
            category: 'choice',
            hasOptions: true,
            defaultSettings: {
                layout: 'vertical'
            }
        },
        radio: {
            label: '라디오 버튼',
            icon: '🔘',
            category: 'choice',
            hasOptions: true,
            defaultSettings: {
                layout: 'vertical'
            }
        },
        date: {
            label: '날짜',
            icon: '📅',
            category: 'advanced',
            hasOptions: false,
            defaultSettings: {
                minDate: '',
                maxDate: '',
                format: 'Y-m-d'
            }
        },
        time: {
            label: '시간',
            icon: '⏰',
            category: 'advanced',
            hasOptions: false,
            defaultSettings: {
                format: 'H:i'
            }
        },
        datetime: {
            label: '날짜 & 시간',
            icon: '📆',
            category: 'advanced',
            hasOptions: false,
            defaultSettings: {
                format: 'Y-m-d H:i'
            }
        },
        file: {
            label: '파일 업로드',
            icon: '📎',
            category: 'advanced',
            hasOptions: false,
            defaultSettings: {
                allowedTypes: 'jpg,jpeg,png,gif,pdf,doc,docx',
                maxSize: 5,
                multiple: false
            }
        },
        hidden: {
            label: '히든 필드',
            icon: '👁️‍🗨️',
            category: 'advanced',
            hasOptions: false,
            defaultSettings: {
                defaultValue: ''
            }
        },
        html: {
            label: 'HTML 블록',
            icon: '🧩',
            category: 'layout',
            hasOptions: false,
            defaultSettings: {
                content: '<p>커스텀 HTML 내용</p>'
            }
        },
        divider: {
            label: '구분선',
            icon: '➖',
            category: 'layout',
            hasOptions: false,
            defaultSettings: {}
        },
        heading: {
            label: '제목',
            icon: '🔤',
            category: 'layout',
            hasOptions: false,
            defaultSettings: {
                content: '섹션 제목',
                level: 'h3'
            }
        }
    };

    const FIELD_CATEGORIES = {
        basic: { label: '기본 필드', icon: '📝' },
        choice: { label: '선택 필드', icon: '☑️' },
        advanced: { label: '고급 필드', icon: '⚙️' },
        layout: { label: '레이아웃', icon: '🎨' }
    };

    /**
     * Drag & Drop Form Builder Class
     */
    class DragDropFormBuilder {
        constructor(container) {
            this.$container = $(container);
            this.fields = [];
            this.selectedFieldId = null;
            this.draggedFieldType = null;
            this.draggedFieldId = null;
            this.fieldCounter = 0;

            this.init();
        }

        init() {
            this.render();
            this.bindEvents();
            this.loadExistingFields();
        }

        /**
         * Render the form builder layout
         */
        render() {
            const html = `
                <div class="acf-form-builder">
                    <!-- Left Panel: Field Palette -->
                    <div class="acf-builder-panel acf-builder-palette">
                        <div class="acf-builder-panel-header">
                            <h3>📦 필드 팔레트</h3>
                            <input type="text" class="acf-field-search" placeholder="필드 검색...">
                        </div>
                        <div class="acf-builder-panel-body">
                            ${this.renderFieldPalette()}
                        </div>
                    </div>

                    <!-- Center Panel: Canvas -->
                    <div class="acf-builder-panel acf-builder-canvas">
                        <div class="acf-builder-panel-header">
                            <h3>🎨 폼 캔버스</h3>
                            <div class="acf-canvas-actions">
                                <button type="button" class="acf-btn acf-btn-sm acf-btn-preview">
                                    👁️ 미리보기
                                </button>
                                <button type="button" class="acf-btn acf-btn-sm acf-btn-clear">
                                    🗑️ 모두 삭제
                                </button>
                            </div>
                        </div>
                        <div class="acf-builder-panel-body">
                            <div class="acf-canvas-drop-zone" data-drop-zone="canvas">
                                <div class="acf-canvas-empty">
                                    <div class="acf-canvas-empty-icon">📝</div>
                                    <p>왼쪽 팔레트에서 필드를 드래그하여<br>여기에 놓으세요</p>
                                </div>
                                <div class="acf-canvas-fields"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel: Field Settings -->
                    <div class="acf-builder-panel acf-builder-settings">
                        <div class="acf-builder-panel-header">
                            <h3>⚙️ 필드 설정</h3>
                        </div>
                        <div class="acf-builder-panel-body">
                            <div class="acf-settings-empty">
                                <p>필드를 선택하면<br>설정이 여기에 표시됩니다</p>
                            </div>
                            <div class="acf-settings-content" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <!-- Preview Modal -->
                <div class="acf-preview-modal" style="display: none;">
                    <div class="acf-preview-modal-overlay"></div>
                    <div class="acf-preview-modal-content">
                        <div class="acf-preview-modal-header">
                            <h3>폼 미리보기</h3>
                            <button type="button" class="acf-preview-modal-close">&times;</button>
                        </div>
                        <div class="acf-preview-modal-body"></div>
                    </div>
                </div>
            `;

            this.$container.html(html);
        }

        /**
         * Render field palette
         */
        renderFieldPalette() {
            let html = '';

            Object.entries(FIELD_CATEGORIES).forEach(([categoryKey, category]) => {
                const categoryFields = Object.entries(FIELD_TYPES)
                    .filter(([, field]) => field.category === categoryKey);

                if (categoryFields.length === 0) return;

                html += `
                    <div class="acf-palette-category" data-category="${categoryKey}">
                        <div class="acf-palette-category-header">
                            <span>${category.icon} ${category.label}</span>
                            <span class="acf-palette-toggle">▼</span>
                        </div>
                        <div class="acf-palette-category-fields">
                `;

                categoryFields.forEach(([fieldType, field]) => {
                    html += `
                        <div class="acf-palette-field"
                             data-field-type="${fieldType}"
                             draggable="true">
                            <span class="acf-palette-field-icon">${field.icon}</span>
                            <span class="acf-palette-field-label">${field.label}</span>
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            });

            return html;
        }

        /**
         * Bind events
         */
        bindEvents() {
            const self = this;

            // Category toggle
            this.$container.on('click', '.acf-palette-category-header', function() {
                $(this).closest('.acf-palette-category').toggleClass('collapsed');
            });

            // Field search
            this.$container.on('input', '.acf-field-search', function() {
                const query = $(this).val().toLowerCase();
                self.$container.find('.acf-palette-field').each(function() {
                    const label = $(this).find('.acf-palette-field-label').text().toLowerCase();
                    const type = $(this).data('field-type').toLowerCase();
                    $(this).toggle(label.includes(query) || type.includes(query));
                });
            });

            // Drag start from palette
            this.$container.on('dragstart', '.acf-palette-field', function(e) {
                self.draggedFieldType = $(this).data('field-type');
                self.draggedFieldId = null;
                $(this).addClass('dragging');
                e.originalEvent.dataTransfer.effectAllowed = 'copy';
                e.originalEvent.dataTransfer.setData('text/plain', self.draggedFieldType);
                self.$container.find('.acf-canvas-drop-zone').addClass('drag-active');
            });

            // Drag start from canvas field (reorder)
            this.$container.on('dragstart', '.acf-canvas-field', function(e) {
                e.stopPropagation();
                self.draggedFieldType = null;
                self.draggedFieldId = $(this).data('field-id');
                $(this).addClass('dragging');
                e.originalEvent.dataTransfer.effectAllowed = 'move';
                e.originalEvent.dataTransfer.setData('text/plain', self.draggedFieldId);
            });

            // Drag end
            $(document).on('dragend', '.acf-palette-field, .acf-canvas-field', function() {
                self.draggedFieldType = null;
                self.draggedFieldId = null;
                $('.dragging').removeClass('dragging');
                $('.drag-active').removeClass('drag-active');
                $('.drag-over').removeClass('drag-over');
            });

            // Drag over canvas
            this.$container.on('dragover', '.acf-canvas-drop-zone', function(e) {
                e.preventDefault();
                e.originalEvent.dataTransfer.dropEffect = self.draggedFieldType ? 'copy' : 'move';
                $(this).addClass('drag-over');
            });

            // Drag leave canvas
            this.$container.on('dragleave', '.acf-canvas-drop-zone', function(e) {
                if (!$(e.relatedTarget).closest('.acf-canvas-drop-zone').length) {
                    $(this).removeClass('drag-over');
                }
            });

            // Drop on canvas
            this.$container.on('drop', '.acf-canvas-drop-zone', function(e) {
                e.preventDefault();
                $(this).removeClass('drag-over drag-active');

                if (self.draggedFieldType) {
                    // New field from palette
                    self.addField(self.draggedFieldType);
                } else if (self.draggedFieldId) {
                    // Reorder existing field
                    const $target = $(e.target).closest('.acf-canvas-field');
                    if ($target.length) {
                        self.reorderField(self.draggedFieldId, $target.data('field-id'));
                    }
                }

                self.draggedFieldType = null;
                self.draggedFieldId = null;
            });

            // Select field
            this.$container.on('click', '.acf-canvas-field', function(e) {
                if ($(e.target).closest('.acf-field-actions').length) return;
                const fieldId = $(this).data('field-id');
                self.selectField(fieldId);
            });

            // Delete field
            this.$container.on('click', '.acf-field-delete', function(e) {
                e.stopPropagation();
                const fieldId = $(this).closest('.acf-canvas-field').data('field-id');
                if (confirm('이 필드를 삭제하시겠습니까?')) {
                    self.removeField(fieldId);
                }
            });

            // Duplicate field
            this.$container.on('click', '.acf-field-duplicate', function(e) {
                e.stopPropagation();
                const fieldId = $(this).closest('.acf-canvas-field').data('field-id');
                self.duplicateField(fieldId);
            });

            // Clear all fields
            this.$container.on('click', '.acf-btn-clear', function() {
                if (self.fields.length === 0) return;
                if (confirm('모든 필드를 삭제하시겠습니까?')) {
                    self.clearFields();
                }
            });

            // Preview
            this.$container.on('click', '.acf-btn-preview', function() {
                self.showPreview();
            });

            // Close preview
            this.$container.on('click', '.acf-preview-modal-close, .acf-preview-modal-overlay', function() {
                self.hidePreview();
            });

            // Settings panel input changes
            this.$container.on('input change', '.acf-settings-content input, .acf-settings-content select, .acf-settings-content textarea', function() {
                const fieldId = self.selectedFieldId;
                if (!fieldId) return;

                const field = self.getField(fieldId);
                if (!field) return;

                const name = $(this).attr('name');
                let value = $(this).val();

                if ($(this).attr('type') === 'checkbox') {
                    value = $(this).is(':checked');
                }

                // Handle nested settings
                if (name.includes('.')) {
                    const [parent, child] = name.split('.');
                    if (!field[parent]) field[parent] = {};
                    field[parent][child] = value;
                } else {
                    field[name] = value;
                }

                self.updateFieldInCanvas(fieldId);
            });

            // Options management (add/remove/edit)
            this.$container.on('click', '.acf-add-option', function() {
                self.addOption();
            });

            this.$container.on('click', '.acf-remove-option', function() {
                $(this).closest('.acf-option-row').remove();
                self.saveOptionsFromPanel();
            });

            this.$container.on('input', '.acf-option-row input', function() {
                self.saveOptionsFromPanel();
            });

            // Option row drag reorder
            this.$container.on('dragstart', '.acf-option-row', function(e) {
                $(this).addClass('dragging-option');
            });

            this.$container.on('dragend', '.acf-option-row', function() {
                $(this).removeClass('dragging-option');
                self.saveOptionsFromPanel();
            });

            this.$container.on('dragover', '.acf-option-row', function(e) {
                e.preventDefault();
                const $dragging = $('.dragging-option');
                const $this = $(this);
                if ($dragging[0] !== $this[0]) {
                    const rect = this.getBoundingClientRect();
                    const midY = rect.top + rect.height / 2;
                    if (e.originalEvent.clientY < midY) {
                        $this.before($dragging);
                    } else {
                        $this.after($dragging);
                    }
                }
            });

            // Conditional logic events
            this.$container.on('change', '.acf-conditional-enabled', function() {
                const enabled = $(this).is(':checked');
                $(this).closest('.acf-conditional-logic-section').find('.acf-conditional-settings').toggle(enabled);
                self.saveConditionalLogic();
            });

            this.$container.on('change', '.acf-conditional-action, .acf-conditional-logic', function() {
                self.saveConditionalLogic();
            });

            this.$container.on('change input', '.acf-rule-field, .acf-rule-operator, .acf-rule-value', function() {
                // Re-render value field if field changed (to show options dropdown)
                if ($(this).hasClass('acf-rule-field') || $(this).hasClass('acf-rule-operator')) {
                    const $rule = $(this).closest('.acf-conditional-rule');
                    const operator = $rule.find('.acf-rule-operator').val();

                    if (['empty', 'not_empty'].includes(operator)) {
                        $rule.find('.acf-rule-value').replaceWith('<input type="hidden" class="acf-rule-value" value="">');
                    }
                }
                self.saveConditionalLogic();
            });

            this.$container.on('click', '.acf-add-condition-rule', function() {
                self.addConditionalRule();
            });

            this.$container.on('click', '.acf-remove-condition-rule', function() {
                const $rule = $(this).closest('.acf-conditional-rule');
                const $rules = $rule.closest('.acf-conditional-rules');
                $rule.remove();

                // Ensure at least one rule exists
                if ($rules.find('.acf-conditional-rule').length === 0) {
                    const otherFields = self.fields.filter(f => f.id !== self.selectedFieldId && !['divider', 'html', 'heading', 'hidden'].includes(f.type));
                    $rules.html(self.renderConditionalRule({ fieldId: '', operator: '==', value: '' }, 0, otherFields));
                }

                self.saveConditionalLogic();
            });
        }

        /**
         * Load existing fields from form data
         */
        loadExistingFields() {
            if (typeof existingFormFields !== 'undefined' && Array.isArray(existingFormFields)) {
                existingFormFields.forEach(field => {
                    this.fieldCounter++;
                    field.id = field.id || 'field_' + this.fieldCounter;
                    this.fields.push(field);
                });
                this.renderCanvasFields();
            }
        }

        /**
         * Add a new field
         */
        addField(type, position = null) {
            this.fieldCounter++;
            const fieldConfig = FIELD_TYPES[type];

            const field = {
                id: 'field_' + this.fieldCounter + '_' + Date.now(),
                type: type,
                label: fieldConfig.label,
                name: type + '_' + this.fieldCounter,
                required: false,
                settings: { ...fieldConfig.defaultSettings },
                options: fieldConfig.hasOptions ? [
                    { label: '옵션 1', value: 'option_1' },
                    { label: '옵션 2', value: 'option_2' }
                ] : []
            };

            if (position !== null && position >= 0) {
                this.fields.splice(position, 0, field);
            } else {
                this.fields.push(field);
            }

            this.renderCanvasFields();
            this.selectField(field.id);
            this.showToast('필드가 추가되었습니다.', 'success');
        }

        /**
         * Remove a field
         */
        removeField(fieldId) {
            this.fields = this.fields.filter(f => f.id !== fieldId);
            if (this.selectedFieldId === fieldId) {
                this.selectedFieldId = null;
                this.hideSettings();
            }
            this.renderCanvasFields();
            this.showToast('필드가 삭제되었습니다.', 'info');
        }

        /**
         * Duplicate a field
         */
        duplicateField(fieldId) {
            const field = this.getField(fieldId);
            if (!field) return;

            this.fieldCounter++;
            const newField = JSON.parse(JSON.stringify(field));
            newField.id = 'field_' + this.fieldCounter + '_' + Date.now();
            newField.name = field.type + '_' + this.fieldCounter;
            newField.label = field.label + ' (복사)';

            const index = this.fields.findIndex(f => f.id === fieldId);
            this.fields.splice(index + 1, 0, newField);

            this.renderCanvasFields();
            this.selectField(newField.id);
            this.showToast('필드가 복제되었습니다.', 'success');
        }

        /**
         * Reorder field
         */
        reorderField(sourceId, targetId) {
            const sourceIndex = this.fields.findIndex(f => f.id === sourceId);
            const targetIndex = this.fields.findIndex(f => f.id === targetId);

            if (sourceIndex === -1 || targetIndex === -1) return;

            const [field] = this.fields.splice(sourceIndex, 1);
            this.fields.splice(targetIndex, 0, field);

            this.renderCanvasFields();
        }

        /**
         * Clear all fields
         */
        clearFields() {
            this.fields = [];
            this.selectedFieldId = null;
            this.hideSettings();
            this.renderCanvasFields();
            this.showToast('모든 필드가 삭제되었습니다.', 'info');
        }

        /**
         * Get field by ID
         */
        getField(fieldId) {
            return this.fields.find(f => f.id === fieldId);
        }

        /**
         * Select a field
         */
        selectField(fieldId) {
            this.selectedFieldId = fieldId;
            this.$container.find('.acf-canvas-field').removeClass('selected');
            this.$container.find(`.acf-canvas-field[data-field-id="${fieldId}"]`).addClass('selected');
            this.showSettings(fieldId);
        }

        /**
         * Render canvas fields
         */
        renderCanvasFields() {
            const $canvas = this.$container.find('.acf-canvas-fields');
            const $empty = this.$container.find('.acf-canvas-empty');

            if (this.fields.length === 0) {
                $canvas.html('');
                $empty.show();
                return;
            }

            $empty.hide();
            let html = '';

            this.fields.forEach((field, index) => {
                const fieldConfig = FIELD_TYPES[field.type] || { icon: '❓', label: field.type };
                const isSelected = field.id === this.selectedFieldId;

                html += `
                    <div class="acf-canvas-field ${isSelected ? 'selected' : ''}"
                         data-field-id="${field.id}"
                         data-field-index="${index}"
                         draggable="true">
                        <div class="acf-field-header">
                            <div class="acf-field-drag-handle">⋮⋮</div>
                            <span class="acf-field-icon">${fieldConfig.icon}</span>
                            <span class="acf-field-label">${this.escapeHtml(field.label || fieldConfig.label)}</span>
                            ${field.required ? '<span class="acf-field-required">*필수</span>' : ''}
                            <span class="acf-field-type">(${fieldConfig.label})</span>
                        </div>
                        <div class="acf-field-preview">
                            ${this.renderFieldPreview(field)}
                        </div>
                        <div class="acf-field-actions">
                            <button type="button" class="acf-field-duplicate" title="복제">📋</button>
                            <button type="button" class="acf-field-delete" title="삭제">🗑️</button>
                        </div>
                    </div>
                `;
            });

            $canvas.html(html);
        }

        /**
         * Render field preview in canvas
         */
        renderFieldPreview(field) {
            const placeholder = field.settings?.placeholder || '';

            switch (field.type) {
                case 'text':
                case 'email':
                case 'tel':
                case 'url':
                case 'number':
                    return `<input type="${field.type}" placeholder="${this.escapeHtml(placeholder)}" disabled>`;

                case 'textarea':
                    return `<textarea rows="2" placeholder="${this.escapeHtml(placeholder)}" disabled></textarea>`;

                case 'select':
                    let selectOptions = (field.options || []).slice(0, 3).map(opt =>
                        `<option>${this.escapeHtml(opt.label)}</option>`
                    ).join('');
                    return `<select disabled>${selectOptions}</select>`;

                case 'checkbox':
                    let checkboxes = (field.options || []).slice(0, 2).map(opt =>
                        `<label class="acf-preview-checkbox"><input type="checkbox" disabled> ${this.escapeHtml(opt.label)}</label>`
                    ).join('');
                    return `<div class="acf-preview-options">${checkboxes}</div>`;

                case 'radio':
                    let radios = (field.options || []).slice(0, 2).map(opt =>
                        `<label class="acf-preview-radio"><input type="radio" disabled> ${this.escapeHtml(opt.label)}</label>`
                    ).join('');
                    return `<div class="acf-preview-options">${radios}</div>`;

                case 'date':
                    return `<input type="date" disabled>`;

                case 'time':
                    return `<input type="time" disabled>`;

                case 'datetime':
                    return `<input type="datetime-local" disabled>`;

                case 'file':
                    return `<div class="acf-preview-file">📎 파일 선택...</div>`;

                case 'hidden':
                    return `<div class="acf-preview-hidden">[히든 필드]</div>`;

                case 'html':
                    return `<div class="acf-preview-html">${field.settings?.content || ''}</div>`;

                case 'divider':
                    return `<hr class="acf-preview-divider">`;

                case 'heading':
                    const level = field.settings?.level || 'h3';
                    return `<${level} class="acf-preview-heading">${this.escapeHtml(field.settings?.content || '')}</${level}>`;

                default:
                    return `<div class="acf-preview-unknown">[${field.type}]</div>`;
            }
        }

        /**
         * Update a single field in canvas
         */
        updateFieldInCanvas(fieldId) {
            const field = this.getField(fieldId);
            if (!field) return;

            const $fieldEl = this.$container.find(`.acf-canvas-field[data-field-id="${fieldId}"]`);
            if ($fieldEl.length) {
                const fieldConfig = FIELD_TYPES[field.type] || { icon: '❓', label: field.type };
                $fieldEl.find('.acf-field-label').text(field.label || fieldConfig.label);
                $fieldEl.find('.acf-field-required').remove();
                if (field.required) {
                    $fieldEl.find('.acf-field-label').after('<span class="acf-field-required">*필수</span>');
                }
                $fieldEl.find('.acf-field-preview').html(this.renderFieldPreview(field));
            }
        }

        /**
         * Show settings panel for field
         */
        showSettings(fieldId) {
            const field = this.getField(fieldId);
            if (!field) return;

            const fieldConfig = FIELD_TYPES[field.type] || {};
            const $empty = this.$container.find('.acf-settings-empty');
            const $content = this.$container.find('.acf-settings-content');

            $empty.hide();
            $content.show().html(this.renderSettingsPanel(field, fieldConfig));
        }

        /**
         * Hide settings panel
         */
        hideSettings() {
            this.$container.find('.acf-settings-empty').show();
            this.$container.find('.acf-settings-content').hide().html('');
        }

        /**
         * Render settings panel
         */
        renderSettingsPanel(field, fieldConfig) {
            let html = `
                <div class="acf-settings-section">
                    <h4>기본 설정</h4>
                    <div class="acf-settings-field">
                        <label>필드 레이블</label>
                        <input type="text" name="label" value="${this.escapeHtml(field.label || '')}" placeholder="필드 이름">
                    </div>
                    <div class="acf-settings-field">
                        <label>필드 이름 (Name)</label>
                        <input type="text" name="name" value="${this.escapeHtml(field.name || '')}" placeholder="field_name">
                        <small>폼 데이터에서 사용될 고유 이름</small>
                    </div>
                    <div class="acf-settings-field acf-settings-checkbox">
                        <label>
                            <input type="checkbox" name="required" ${field.required ? 'checked' : ''}>
                            필수 필드
                        </label>
                    </div>
                </div>
            `;

            // Type-specific settings
            html += this.renderTypeSpecificSettings(field, fieldConfig);

            // Options for select, checkbox, radio
            if (fieldConfig.hasOptions) {
                html += this.renderOptionsSettings(field);
            }

            // Conditional Logic settings
            html += this.renderConditionalLogicSettings(field);

            // Advanced settings
            html += `
                <div class="acf-settings-section acf-settings-advanced">
                    <h4>고급 설정</h4>
                    <div class="acf-settings-field">
                        <label>CSS 클래스</label>
                        <input type="text" name="settings.cssClass" value="${this.escapeHtml(field.settings?.cssClass || '')}" placeholder="custom-class">
                    </div>
                    <div class="acf-settings-field">
                        <label>설명 텍스트</label>
                        <textarea name="settings.description" rows="2" placeholder="필드 아래에 표시될 설명">${this.escapeHtml(field.settings?.description || '')}</textarea>
                    </div>
                </div>
            `;

            return html;
        }

        /**
         * Render conditional logic settings
         */
        renderConditionalLogicSettings(field) {
            const conditions = field.conditionalLogic || { enabled: false, action: 'show', logic: 'all', rules: [] };
            const otherFields = this.fields.filter(f => f.id !== field.id && !['divider', 'html', 'heading', 'hidden'].includes(f.type));

            if (otherFields.length === 0) {
                return '';
            }

            let html = `
                <div class="acf-settings-section acf-conditional-logic-section">
                    <h4>🔗 조건부 로직</h4>
                    <div class="acf-settings-field acf-settings-checkbox">
                        <label>
                            <input type="checkbox" class="acf-conditional-enabled" ${conditions.enabled ? 'checked' : ''}>
                            조건부 로직 활성화
                        </label>
                    </div>
                    <div class="acf-conditional-settings" style="display: ${conditions.enabled ? 'block' : 'none'};">
                        <div class="acf-conditional-action-row" style="display: flex; gap: 10px; align-items: center; margin-bottom: 12px;">
                            <select class="acf-conditional-action">
                                <option value="show" ${conditions.action === 'show' ? 'selected' : ''}>표시</option>
                                <option value="hide" ${conditions.action === 'hide' ? 'selected' : ''}>숨김</option>
                            </select>
                            <span>이 필드를</span>
                            <select class="acf-conditional-logic">
                                <option value="all" ${conditions.logic === 'all' ? 'selected' : ''}>모든</option>
                                <option value="any" ${conditions.logic === 'any' ? 'selected' : ''}>하나 이상의</option>
                            </select>
                            <span>조건 충족 시</span>
                        </div>
                        <div class="acf-conditional-rules">
            `;

            // Render existing rules or empty rule
            const rules = conditions.rules && conditions.rules.length > 0 ? conditions.rules : [{ fieldId: '', operator: '==', value: '' }];
            rules.forEach((rule, index) => {
                html += this.renderConditionalRule(rule, index, otherFields);
            });

            html += `
                        </div>
                        <button type="button" class="acf-btn acf-btn-sm acf-add-condition-rule">+ 조건 추가</button>
                    </div>
                </div>
            `;

            return html;
        }

        /**
         * Render single conditional rule
         */
        renderConditionalRule(rule, index, otherFields) {
            const selectedField = this.fields.find(f => f.id === rule.fieldId);
            const fieldHasOptions = selectedField && ['select', 'checkbox', 'radio'].includes(selectedField.type);

            let html = `
                <div class="acf-conditional-rule" data-rule-index="${index}">
                    <select class="acf-rule-field">
                        <option value="">필드 선택...</option>
            `;

            otherFields.forEach(f => {
                html += `<option value="${f.id}" ${rule.fieldId === f.id ? 'selected' : ''}>${this.escapeHtml(f.label || f.name)}</option>`;
            });

            html += `
                    </select>
                    <select class="acf-rule-operator">
                        <option value="==" ${rule.operator === '==' ? 'selected' : ''}>같음 (=)</option>
                        <option value="!=" ${rule.operator === '!=' ? 'selected' : ''}>같지 않음 (≠)</option>
                        <option value=">" ${rule.operator === '>' ? 'selected' : ''}>보다 큼 (>)</option>
                        <option value="<" ${rule.operator === '<' ? 'selected' : ''}>보다 작음 (<)</option>
                        <option value=">=" ${rule.operator === '>=' ? 'selected' : ''}>이상 (≥)</option>
                        <option value="<=" ${rule.operator === '<=' ? 'selected' : ''}>이하 (≤)</option>
                        <option value="contains" ${rule.operator === 'contains' ? 'selected' : ''}>포함</option>
                        <option value="empty" ${rule.operator === 'empty' ? 'selected' : ''}>비어있음</option>
                        <option value="not_empty" ${rule.operator === 'not_empty' ? 'selected' : ''}>비어있지 않음</option>
                    </select>
            `;

            // Value input - show select for choice fields, text for others
            if (fieldHasOptions && selectedField.options && selectedField.options.length > 0 && !['empty', 'not_empty'].includes(rule.operator)) {
                html += `<select class="acf-rule-value">`;
                html += `<option value="">값 선택...</option>`;
                selectedField.options.forEach(opt => {
                    html += `<option value="${this.escapeHtml(opt.value)}" ${rule.value === opt.value ? 'selected' : ''}>${this.escapeHtml(opt.label)}</option>`;
                });
                html += `</select>`;
            } else if (!['empty', 'not_empty'].includes(rule.operator)) {
                html += `<input type="text" class="acf-rule-value" value="${this.escapeHtml(rule.value || '')}" placeholder="값">`;
            } else {
                html += `<input type="hidden" class="acf-rule-value" value="">`;
            }

            html += `
                    <button type="button" class="acf-remove-condition-rule" title="삭제">✕</button>
                </div>
            `;

            return html;
        }

        /**
         * Render type-specific settings
         */
        renderTypeSpecificSettings(field, fieldConfig) {
            let html = '<div class="acf-settings-section"><h4>필드 설정</h4>';

            switch (field.type) {
                case 'text':
                case 'email':
                case 'tel':
                case 'url':
                    html += `
                        <div class="acf-settings-field">
                            <label>Placeholder</label>
                            <input type="text" name="settings.placeholder" value="${this.escapeHtml(field.settings?.placeholder || '')}">
                        </div>
                        <div class="acf-settings-field">
                            <label>기본값</label>
                            <input type="text" name="settings.defaultValue" value="${this.escapeHtml(field.settings?.defaultValue || '')}">
                        </div>
                    `;
                    if (field.type === 'text') {
                        html += `
                            <div class="acf-settings-row">
                                <div class="acf-settings-field">
                                    <label>최소 길이</label>
                                    <input type="number" name="settings.minLength" value="${field.settings?.minLength || ''}" min="0">
                                </div>
                                <div class="acf-settings-field">
                                    <label>최대 길이</label>
                                    <input type="number" name="settings.maxLength" value="${field.settings?.maxLength || ''}" min="0">
                                </div>
                            </div>
                        `;
                    }
                    break;

                case 'textarea':
                    html += `
                        <div class="acf-settings-field">
                            <label>Placeholder</label>
                            <input type="text" name="settings.placeholder" value="${this.escapeHtml(field.settings?.placeholder || '')}">
                        </div>
                        <div class="acf-settings-row">
                            <div class="acf-settings-field">
                                <label>행 수</label>
                                <input type="number" name="settings.rows" value="${field.settings?.rows || 4}" min="1" max="20">
                            </div>
                            <div class="acf-settings-field">
                                <label>최대 길이</label>
                                <input type="number" name="settings.maxLength" value="${field.settings?.maxLength || ''}" min="0">
                            </div>
                        </div>
                    `;
                    break;

                case 'number':
                    html += `
                        <div class="acf-settings-field">
                            <label>Placeholder</label>
                            <input type="text" name="settings.placeholder" value="${this.escapeHtml(field.settings?.placeholder || '')}">
                        </div>
                        <div class="acf-settings-row">
                            <div class="acf-settings-field">
                                <label>최소값</label>
                                <input type="number" name="settings.min" value="${field.settings?.min || ''}">
                            </div>
                            <div class="acf-settings-field">
                                <label>최대값</label>
                                <input type="number" name="settings.max" value="${field.settings?.max || ''}">
                            </div>
                            <div class="acf-settings-field">
                                <label>단위</label>
                                <input type="number" name="settings.step" value="${field.settings?.step || '1'}" min="0.001">
                            </div>
                        </div>
                    `;
                    break;

                case 'select':
                    html += `
                        <div class="acf-settings-field">
                            <label>Placeholder</label>
                            <input type="text" name="settings.placeholder" value="${this.escapeHtml(field.settings?.placeholder || '선택하세요')}">
                        </div>
                        <div class="acf-settings-field acf-settings-checkbox">
                            <label>
                                <input type="checkbox" name="settings.multiple" ${field.settings?.multiple ? 'checked' : ''}>
                                다중 선택 허용
                            </label>
                        </div>
                    `;
                    break;

                case 'checkbox':
                case 'radio':
                    html += `
                        <div class="acf-settings-field">
                            <label>레이아웃</label>
                            <select name="settings.layout">
                                <option value="vertical" ${field.settings?.layout === 'vertical' ? 'selected' : ''}>세로 (Vertical)</option>
                                <option value="horizontal" ${field.settings?.layout === 'horizontal' ? 'selected' : ''}>가로 (Horizontal)</option>
                            </select>
                        </div>
                    `;
                    break;

                case 'date':
                    html += `
                        <div class="acf-settings-row">
                            <div class="acf-settings-field">
                                <label>최소 날짜</label>
                                <input type="date" name="settings.minDate" value="${field.settings?.minDate || ''}">
                            </div>
                            <div class="acf-settings-field">
                                <label>최대 날짜</label>
                                <input type="date" name="settings.maxDate" value="${field.settings?.maxDate || ''}">
                            </div>
                        </div>
                    `;
                    break;

                case 'file':
                    html += `
                        <div class="acf-settings-field">
                            <label>허용 파일 형식</label>
                            <input type="text" name="settings.allowedTypes" value="${this.escapeHtml(field.settings?.allowedTypes || 'jpg,jpeg,png,gif,pdf')}" placeholder="jpg,png,pdf">
                            <small>쉼표로 구분하여 확장자 입력</small>
                        </div>
                        <div class="acf-settings-field">
                            <label>최대 파일 크기 (MB)</label>
                            <input type="number" name="settings.maxSize" value="${field.settings?.maxSize || 5}" min="1" max="100">
                        </div>
                        <div class="acf-settings-field acf-settings-checkbox">
                            <label>
                                <input type="checkbox" name="settings.multiple" ${field.settings?.multiple ? 'checked' : ''}>
                                다중 파일 업로드 허용
                            </label>
                        </div>
                    `;
                    break;

                case 'hidden':
                    html += `
                        <div class="acf-settings-field">
                            <label>기본값</label>
                            <input type="text" name="settings.defaultValue" value="${this.escapeHtml(field.settings?.defaultValue || '')}">
                        </div>
                    `;
                    break;

                case 'html':
                    html += `
                        <div class="acf-settings-field">
                            <label>HTML 내용</label>
                            <textarea name="settings.content" rows="5" placeholder="<p>HTML 내용을 입력하세요</p>">${this.escapeHtml(field.settings?.content || '')}</textarea>
                        </div>
                    `;
                    break;

                case 'heading':
                    html += `
                        <div class="acf-settings-field">
                            <label>제목 텍스트</label>
                            <input type="text" name="settings.content" value="${this.escapeHtml(field.settings?.content || '')}">
                        </div>
                        <div class="acf-settings-field">
                            <label>제목 레벨</label>
                            <select name="settings.level">
                                <option value="h2" ${field.settings?.level === 'h2' ? 'selected' : ''}>H2</option>
                                <option value="h3" ${field.settings?.level === 'h3' ? 'selected' : ''}>H3</option>
                                <option value="h4" ${field.settings?.level === 'h4' ? 'selected' : ''}>H4</option>
                                <option value="h5" ${field.settings?.level === 'h5' ? 'selected' : ''}>H5</option>
                            </select>
                        </div>
                    `;
                    break;

                case 'divider':
                    html += `<p class="acf-settings-note">구분선에는 추가 설정이 없습니다.</p>`;
                    break;
            }

            html += '</div>';
            return html;
        }

        /**
         * Render options settings (for select, checkbox, radio)
         */
        renderOptionsSettings(field) {
            let html = `
                <div class="acf-settings-section">
                    <h4>옵션</h4>
                    <div class="acf-options-list">
            `;

            (field.options || []).forEach((option, index) => {
                html += `
                    <div class="acf-option-row" draggable="true" data-index="${index}">
                        <span class="acf-option-drag">⋮⋮</span>
                        <input type="text" class="acf-option-label" value="${this.escapeHtml(option.label)}" placeholder="레이블">
                        <input type="text" class="acf-option-value" value="${this.escapeHtml(option.value)}" placeholder="값">
                        <button type="button" class="acf-remove-option" title="삭제">✕</button>
                    </div>
                `;
            });

            html += `
                    </div>
                    <button type="button" class="acf-btn acf-btn-sm acf-add-option">+ 옵션 추가</button>
                </div>
            `;

            return html;
        }

        /**
         * Add new option
         */
        addOption() {
            const field = this.getField(this.selectedFieldId);
            if (!field) return;

            const optionIndex = (field.options || []).length + 1;
            field.options = field.options || [];
            field.options.push({
                label: '옵션 ' + optionIndex,
                value: 'option_' + optionIndex
            });

            this.showSettings(this.selectedFieldId);
            this.updateFieldInCanvas(this.selectedFieldId);
        }

        /**
         * Save options from settings panel
         */
        saveOptionsFromPanel() {
            const field = this.getField(this.selectedFieldId);
            if (!field) return;

            const options = [];
            this.$container.find('.acf-option-row').each(function() {
                const label = $(this).find('.acf-option-label').val();
                const value = $(this).find('.acf-option-value').val();
                if (label || value) {
                    options.push({ label, value: value || label.toLowerCase().replace(/\s+/g, '_') });
                }
            });

            field.options = options;
            this.updateFieldInCanvas(this.selectedFieldId);
        }

        /**
         * Save conditional logic from settings panel
         */
        saveConditionalLogic() {
            const field = this.getField(this.selectedFieldId);
            if (!field) return;

            const $section = this.$container.find('.acf-conditional-logic-section');
            if ($section.length === 0) return;

            const enabled = $section.find('.acf-conditional-enabled').is(':checked');
            const action = $section.find('.acf-conditional-action').val();
            const logic = $section.find('.acf-conditional-logic').val();

            const rules = [];
            $section.find('.acf-conditional-rule').each(function() {
                const fieldId = $(this).find('.acf-rule-field').val();
                const operator = $(this).find('.acf-rule-operator').val();
                const value = $(this).find('.acf-rule-value').val();
                rules.push({ fieldId, operator, value });
            });

            field.conditionalLogic = {
                enabled,
                action,
                logic,
                rules
            };

            this.updateFieldInCanvas(this.selectedFieldId);
        }

        /**
         * Add a new conditional rule
         */
        addConditionalRule() {
            const $rules = this.$container.find('.acf-conditional-rules');
            const otherFields = this.fields.filter(f => f.id !== this.selectedFieldId && !['divider', 'html', 'heading', 'hidden'].includes(f.type));
            const newIndex = $rules.find('.acf-conditional-rule').length;

            $rules.append(this.renderConditionalRule({ fieldId: '', operator: '==', value: '' }, newIndex, otherFields));
            this.saveConditionalLogic();
        }

        /**
         * Show preview modal
         */
        showPreview() {
            const $modal = this.$container.find('.acf-preview-modal');
            const $body = $modal.find('.acf-preview-modal-body');

            if (this.fields.length === 0) {
                $body.html('<p style="text-align: center; color: #666;">필드가 없습니다.</p>');
            } else {
                $body.html(this.renderFormPreview());
            }

            $modal.fadeIn(200);
            $('body').addClass('acf-modal-open');
        }

        /**
         * Hide preview modal
         */
        hidePreview() {
            this.$container.find('.acf-preview-modal').fadeOut(200);
            $('body').removeClass('acf-modal-open');
        }

        /**
         * Render form preview
         */
        renderFormPreview() {
            let html = '<form class="acf-preview-form">';

            this.fields.forEach(field => {
                if (field.type === 'hidden') return;

                html += '<div class="acf-preview-field-wrapper">';

                if (field.type !== 'divider' && field.type !== 'html' && field.type !== 'heading') {
                    html += `
                        <label class="acf-preview-label">
                            ${this.escapeHtml(field.label)}
                            ${field.required ? '<span class="required">*</span>' : ''}
                        </label>
                    `;
                }

                html += this.renderPreviewInput(field);

                if (field.settings?.description) {
                    html += `<p class="acf-preview-description">${this.escapeHtml(field.settings.description)}</p>`;
                }

                html += '</div>';
            });

            html += '<button type="button" class="acf-preview-submit">제출</button></form>';
            return html;
        }

        /**
         * Render preview input
         */
        renderPreviewInput(field) {
            const settings = field.settings || {};

            switch (field.type) {
                case 'text':
                case 'email':
                case 'tel':
                case 'url':
                    return `<input type="${field.type}" placeholder="${this.escapeHtml(settings.placeholder || '')}" ${field.required ? 'required' : ''}>`;

                case 'number':
                    return `<input type="number" placeholder="${this.escapeHtml(settings.placeholder || '')}" min="${settings.min || ''}" max="${settings.max || ''}" step="${settings.step || '1'}" ${field.required ? 'required' : ''}>`;

                case 'textarea':
                    return `<textarea rows="${settings.rows || 4}" placeholder="${this.escapeHtml(settings.placeholder || '')}" ${field.required ? 'required' : ''}></textarea>`;

                case 'select':
                    let selectHtml = `<select ${settings.multiple ? 'multiple' : ''} ${field.required ? 'required' : ''}>`;
                    if (settings.placeholder) {
                        selectHtml += `<option value="">${this.escapeHtml(settings.placeholder)}</option>`;
                    }
                    (field.options || []).forEach(opt => {
                        selectHtml += `<option value="${this.escapeHtml(opt.value)}">${this.escapeHtml(opt.label)}</option>`;
                    });
                    selectHtml += '</select>';
                    return selectHtml;

                case 'checkbox':
                    let checkHtml = `<div class="acf-preview-options ${settings.layout || 'vertical'}">`;
                    (field.options || []).forEach(opt => {
                        checkHtml += `
                            <label class="acf-preview-option">
                                <input type="checkbox" value="${this.escapeHtml(opt.value)}">
                                ${this.escapeHtml(opt.label)}
                            </label>
                        `;
                    });
                    checkHtml += '</div>';
                    return checkHtml;

                case 'radio':
                    let radioHtml = `<div class="acf-preview-options ${settings.layout || 'vertical'}">`;
                    (field.options || []).forEach((opt, i) => {
                        radioHtml += `
                            <label class="acf-preview-option">
                                <input type="radio" name="preview_${field.id}" value="${this.escapeHtml(opt.value)}">
                                ${this.escapeHtml(opt.label)}
                            </label>
                        `;
                    });
                    radioHtml += '</div>';
                    return radioHtml;

                case 'date':
                    return `<input type="date" min="${settings.minDate || ''}" max="${settings.maxDate || ''}" ${field.required ? 'required' : ''}>`;

                case 'time':
                    return `<input type="time" ${field.required ? 'required' : ''}>`;

                case 'datetime':
                    return `<input type="datetime-local" ${field.required ? 'required' : ''}>`;

                case 'file':
                    return `<input type="file" accept="${(settings.allowedTypes || '').split(',').map(t => '.' + t.trim()).join(',')}" ${settings.multiple ? 'multiple' : ''} ${field.required ? 'required' : ''}>`;

                case 'html':
                    return settings.content || '';

                case 'divider':
                    return '<hr>';

                case 'heading':
                    const level = settings.level || 'h3';
                    return `<${level}>${this.escapeHtml(settings.content || '')}</${level}>`;

                default:
                    return '';
            }
        }

        /**
         * Get all fields data
         */
        getFields() {
            return this.fields;
        }

        /**
         * Set fields data
         */
        setFields(fields) {
            this.fields = fields || [];
            this.renderCanvasFields();
        }

        /**
         * Show toast notification
         */
        showToast(message, type = 'success') {
            if (typeof window.acfMailSmtpToast === 'function') {
                window.acfMailSmtpToast(message, type);
            }
        }

        /**
         * Escape HTML
         */
        escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    }

    // Export to global scope
    window.DragDropFormBuilder = DragDropFormBuilder;

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        // Common AJAX error handler
        $(document).ajaxError(function(event, xhr, settings, error) {
            if (settings.url && settings.url.includes('acf_mail_smtp')) {
                console.error('ACF Mail SMTP AJAX Error:', error);
            }
        });

        // Toast notifications
        function showToast(message, type) {
            type = type || 'success';

            // Remove existing toasts
            $('.acf-mail-smtp-toast').remove();

            var toast = $('<div class="acf-mail-smtp-toast toast-' + type + '">' + message + '</div>');
            $('body').append(toast);

            // Animate in
            setTimeout(function() {
                toast.addClass('show');
            }, 10);

            // Auto hide
            setTimeout(function() {
                toast.removeClass('show');
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }, 3000);
        }

        // Expose to global scope
        window.acfMailSmtpToast = showToast;

        // Initialize form builder if on forms page
        if ($('#form-builder-container').length) {
            window.formBuilder = new DragDropFormBuilder('#form-builder-container');
        }
    });

})(jQuery);
