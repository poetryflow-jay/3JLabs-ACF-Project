/**
 * [Phase 8.4] 적용 전/후 요약(diff) + Undo 시스템 확장
 * 
 * 전체 코어 설정(팔레트/버튼/폼/링크)에 대한 Undo 기능 제공
 */

(function($) {
    'use strict';

    const UndoSystem = {
        snapshots: [],
        maxSnapshots: 10,
        currentSnapshotIndex: -1,

        /**
         * 현재 모든 설정 스냅샷 생성
         */
        captureSnapshot: function(label) {
            const snapshot = {
                timestamp: Date.now(),
                label: label || '변경',
                data: this.getAllSettings()
            };

            // 현재 인덱스 이후의 스냅샷 제거 (재실행 시)
            if (this.currentSnapshotIndex < this.snapshots.length - 1) {
                this.snapshots = this.snapshots.slice(0, this.currentSnapshotIndex + 1);
            }

            // 새 스냅샷 추가
            this.snapshots.push(snapshot);

            // 최대 개수 제한
            if (this.snapshots.length > this.maxSnapshots) {
                this.snapshots.shift();
            } else {
                this.currentSnapshotIndex++;
            }

            // localStorage에 저장 (페이지 새로고침 시 복원 가능)
            this.saveToStorage();

            return snapshot;
        },

        /**
         * 모든 설정 가져오기
         */
        getAllSettings: function() {
            const $form = $('#jj-admin-center-form, #jj-style-guide-form');
            if (!$form.length) return {};

            const settings = {};

            // 팔레트 설정
            $form.find('[name*="palettes"], [data-setting-key*="palettes"]').each(function() {
                const $field = $(this);
                const name = $field.attr('name') || $field.data('setting-key') || '';
                if (name) {
                    if ($field.is(':checkbox, :radio')) {
                        settings[name] = $field.is(':checked') ? $field.val() : '';
                    } else {
                        settings[name] = $field.val();
                    }
                }
            });

            // 버튼 설정
            $form.find('[name*="btn"], [data-setting-key*="btn"], [name*="button"], [data-setting-key*="button"]').each(function() {
                const $field = $(this);
                const name = $field.attr('name') || $field.data('setting-key') || '';
                if (name) {
                    settings[name] = $field.val();
                }
            });

            // 폼 설정
            $form.find('[name*="form"], [data-setting-key*="form"], [name*="field"], [data-setting-key*="field"]').each(function() {
                const $field = $(this);
                const name = $field.attr('name') || $field.data('setting-key') || '';
                if (name) {
                    settings[name] = $field.val();
                }
            });

            // 링크 설정
            $form.find('[name*="link"], [data-setting-key*="link"]').each(function() {
                const $field = $(this);
                const name = $field.attr('name') || $field.data('setting-key') || '';
                if (name) {
                    settings[name] = $field.val();
                }
            });

            return settings;
        },

        /**
         * 스냅샷 복원
         */
        restoreSnapshot: function(snapshot) {
            if (!snapshot || !snapshot.data) return false;

            const $form = $('#jj-admin-center-form, #jj-style-guide-form');
            if (!$form.length) return false;

            let restoredCount = 0;

            Object.keys(snapshot.data).forEach(function(key) {
                const value = snapshot.data[key];
                
                // name 속성으로 찾기
                let $field = $form.find('[name="' + key + '"]');
                
                // data-setting-key로 찾기
                if (!$field.length) {
                    $field = $form.find('[data-setting-key="' + key + '"]');
                }

                if ($field.length) {
                    if ($field.is(':checkbox, :radio')) {
                        $field.prop('checked', $field.val() === value);
                    } else {
                        $field.val(value);
                        
                        // wpColorPicker 동기화
                        if ($field.closest('.wp-picker-container').length) {
                            try {
                                $field.wpColorPicker('color', value);
                            } catch (e) {}
                        }
                        
                        // change 이벤트 트리거
                        $field.trigger('change');
                    }
                    restoredCount++;
                }
            });

            // 미리보기 업데이트
            if (typeof updateInlinePreview === 'function') {
                updateInlinePreview();
            }

            return restoredCount > 0;
        },

        /**
         * Undo 실행
         */
        undo: function() {
            if (this.currentSnapshotIndex < 0) {
                return false;
            }

            const snapshot = this.snapshots[this.currentSnapshotIndex];
            if (this.restoreSnapshot(snapshot)) {
                this.currentSnapshotIndex--;
                this.saveToStorage();

                // diff 요약 표시
                this.showDiffSummary(snapshot, '복원됨');

                return true;
            }

            return false;
        },

        /**
         * Redo 실행
         */
        redo: function() {
            if (this.currentSnapshotIndex >= this.snapshots.length - 1) {
                return false;
            }

            this.currentSnapshotIndex++;
            const snapshot = this.snapshots[this.currentSnapshotIndex];
            
            if (this.restoreSnapshot(snapshot)) {
                this.saveToStorage();

                // diff 요약 표시
                this.showDiffSummary(snapshot, '재적용됨');

                return true;
            }

            return false;
        },

        /**
         * 변경사항 diff 요약 표시
         */
        showDiffSummary: function(snapshot, action) {
            if (!snapshot || !snapshot.data) return;

            const currentSettings = this.getAllSettings();
            const changes = [];

            // 변경된 항목 찾기
            Object.keys(snapshot.data).forEach(function(key) {
                const oldValue = snapshot.data[key];
                const newValue = currentSettings[key];

                if (oldValue !== newValue) {
                    changes.push({
                        key: key,
                        old: oldValue,
                        new: newValue,
                        label: UndoSystem.getFieldLabel(key)
                    });
                }
            });

            // Toast로 표시
            if (typeof JJUtils !== 'undefined' && JJUtils.showInfo) {
                const summary = changes.length > 0 
                    ? action + ': ' + changes.length + '개 항목 변경됨'
                    : action;
                
                JJUtils.showInfo(summary, {
                    duration: 3000,
                    action: changes.length > 0 ? {
                        label: '자세히',
                        callback: function() {
                            UndoSystem.showDetailedDiff(changes);
                        }
                    } : null
                });
            }
        },

        /**
         * 상세 diff 모달 표시
         */
        showDetailedDiff: function(changes) {
            if (!changes || changes.length === 0) return;

            let html = '<div class="jj-diff-modal" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:999999;display:flex;align-items:center;justify-content:center;padding:20px;">';
            html += '<div style="background:#fff;border-radius:8px;max-width:600px;max-height:80vh;overflow:auto;box-shadow:0 4px 20px rgba(0,0,0,0.3);">';
            html += '<div style="padding:20px;border-bottom:1px solid #ddd;display:flex;justify-content:space-between;align-items:center;">';
            html += '<h3 style="margin:0;">변경사항 요약</h3>';
            html += '<button type="button" class="jj-close-diff" style="background:none;border:none;font-size:24px;cursor:pointer;">×</button>';
            html += '</div>';
            html += '<div style="padding:20px;">';
            
            changes.forEach(function(change) {
                html += '<div style="margin-bottom:15px;padding:12px;background:#f9f9f9;border-radius:4px;">';
                html += '<div style="font-weight:600;margin-bottom:8px;">' + change.label + '</div>';
                html += '<div style="display:flex;gap:15px;font-size:13px;">';
                html += '<div style="flex:1;"><strong>이전:</strong> <span style="color:#d63638;">' + (change.old || '(없음)') + '</span></div>';
                html += '<div style="flex:1;"><strong>변경:</strong> <span style="color:#2271b1;">' + (change.new || '(없음)') + '</span></div>';
                html += '</div>';
                html += '</div>';
            });

            html += '</div></div></div>';

            const $modal = $(html);
            $('body').append($modal);

            $modal.find('.jj-close-diff, .jj-diff-modal').on('click', function(e) {
                if (e.target === this) {
                    $modal.fadeOut(300, function() {
                        $(this).remove();
                    });
                }
            });
        },

        /**
         * 필드 레이블 가져오기
         */
        getFieldLabel: function(key) {
            // 키를 사람이 읽기 쉬운 레이블로 변환
            const labels = {
                'palettes[brand][primary_color]': '브랜드 Primary 색상',
                'palettes[brand][secondary_color]': '브랜드 Secondary 색상',
                'palettes[system][text_color]': '시스템 텍스트 색상',
                'palettes[system][link_color]': '시스템 링크 색상',
            };

            if (labels[key]) {
                return labels[key];
            }

            // 키에서 추론
            return key
                .replace(/\[/g, ' - ')
                .replace(/\]/g, '')
                .replace(/palettes|btn|form|link/gi, function(match) {
                    const map = {
                        'palettes': '팔레트',
                        'btn': '버튼',
                        'form': '폼',
                        'link': '링크'
                    };
                    return map[match.toLowerCase()] || match;
                });
        },

        /**
         * localStorage에 저장
         */
        saveToStorage: function() {
            try {
                localStorage.setItem('jj_undo_snapshots', JSON.stringify({
                    snapshots: this.snapshots,
                    currentIndex: this.currentSnapshotIndex
                }));
            } catch (e) {
                // ignore
            }
        },

        /**
         * localStorage에서 복원
         */
        loadFromStorage: function() {
            try {
                const data = localStorage.getItem('jj_undo_snapshots');
                if (data) {
                    const parsed = JSON.parse(data);
                    this.snapshots = parsed.snapshots || [];
                    this.currentSnapshotIndex = parsed.currentIndex || -1;
                }
            } catch (e) {
                // ignore
            }
        },

        /**
         * 초기화
         */
        init: function() {
            // localStorage에서 복원
            this.loadFromStorage();

            // 프리셋/팔레트 적용 시 자동 스냅샷
            $(document).on('jj:palette:applied jj:preset:applied', function(e, label) {
                UndoSystem.captureSnapshot(label || '팔레트 적용');
                UndoSystem.updateUndoButton();
            });

            // Undo/Redo 버튼 생성
            this.createUndoButtons();
        },

        /**
         * Undo/Redo 버튼 생성
         */
        createUndoButtons: function() {
            const $wrap = $('.jj-admin-center-wrap');
            if (!$wrap.length) return;

            // 기존 버튼 제거
            $('.jj-undo-redo-buttons').remove();

            const $buttons = $('<div class="jj-undo-redo-buttons" style="position:fixed;bottom:20px;left:20px;z-index:999998;display:flex;gap:8px;">' +
                '<button type="button" class="button jj-undo-btn" title="이전으로 (Ctrl+Z)">' +
                '<span class="dashicons dashicons-undo"></span> Undo</button>' +
                '<button type="button" class="button jj-redo-btn" title="다시 실행 (Ctrl+Y)">' +
                '<span class="dashicons dashicons-redo"></span> Redo</button>' +
                '</div>');

            $('body').append($buttons);

            // 클릭 이벤트
            $buttons.find('.jj-undo-btn').on('click', function() {
                UndoSystem.undo();
                UndoSystem.updateUndoButton();
            });

            $buttons.find('.jj-redo-btn').on('click', function() {
                UndoSystem.redo();
                UndoSystem.updateUndoButton();
            });

            // 키보드 단축키
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
                    e.preventDefault();
                    UndoSystem.undo();
                    UndoSystem.updateUndoButton();
                } else if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) {
                    e.preventDefault();
                    UndoSystem.redo();
                    UndoSystem.updateUndoButton();
                }
            });

            this.updateUndoButton();
        },

        /**
         * Undo/Redo 버튼 상태 업데이트
         */
        updateUndoButton: function() {
            const $undoBtn = $('.jj-undo-btn');
            const $redoBtn = $('.jj-redo-btn');

            $undoBtn.prop('disabled', this.currentSnapshotIndex < 0);
            $redoBtn.prop('disabled', this.currentSnapshotIndex >= this.snapshots.length - 1);
        }
    };

    // 초기화
    $(document).ready(function() {
        UndoSystem.init();
    });

    // 전역 접근
    window.JJUndoSystem = UndoSystem;

})(jQuery);
