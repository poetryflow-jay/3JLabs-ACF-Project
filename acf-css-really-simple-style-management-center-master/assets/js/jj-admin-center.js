/**
 * J&J Admin Center JavaScript - v4.0.2
 * 
 * 탭 기반 인터페이스와 2패널 레이아웃 제어
 * - 탭 전환 기능
 * - 2패널 레이아웃: 메뉴 아이템 선택 및 상세 정보 표시
 * - 색상 미리보기 업데이트
 * - 사이드바 액션 버튼 처리
 * - AJAX 기반 설정 저장/불러오기
 * 
 * @since 3.7.0
 * @version 4.0.2
 * 
 * [v4.0.2 변경사항]
 * - [CRITICAL FIX] 저장 기능 완전 수정
 *   - AJAX 핸들러 추가로 저장 기능 작동 보장
 *   - FormData 대신 jQuery serialize() 사용
 *   - 메뉴 순서 데이터 명시적 수집 및 업데이트
 * 
 * [v4.0.1 변경사항]
 * - 드래그앤드롭 초기화 로직 대폭 개선
 *   - 드래그 핸들 자동 생성 기능 추가
 *   - 초기화 검증 및 자동 재시도 메커니즘 강화
 *   - 이벤트 핸들링 개선 (다른 스크립트와의 충돌 방지)
 *   - 상세한 디버깅 로그 추가
 *   - 탭 전환 시 초기화 로직 개선
 *   - 재시도 횟수 증가 (20 → 30)
 *   - 터치 이벤트 지원 추가
 * 
 * [v6.3.0 추가]
 * - 왼쪽 고정 사이드바 네비게이션 제어
 * - 색상 히스토리 관리 (localStorage)
 * - 변경사항 미리보기 모달 시스템
 * - 모바일 최적화 (스와이프, 햄버거 메뉴)
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const $wrap = $('.jj-admin-center-wrap');

        // [v5.6.0] 미디어 업로드 버튼 핸들러
        $wrap.on('click', '.jj-upload-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const target = $btn.data('target');
            const $targetInput = $(target);
            
            let frame = wp.media({
                title: '이미지 선택',
                multiple: false,
                library: { type: 'image' },
                button: { text: '선택' }
            });
            
            frame.on('select', function() {
                const attachment = frame.state().get('selection').first().toJSON();
                $targetInput.val(attachment.url);
            });
            
            frame.open();
        });

        // [v5.7.0] AI Smart Palette Generator
        $wrap.on('click', '#jj-btn-generate-palette', function(e) {
            e.preventDefault();
            const baseColor = $('#jj-ai-base-color').val();
            const harmony = $('#jj-ai-harmony').val();
            const $resultDiv = $('#jj-ai-palette-result');
            const $chipsDiv = $resultDiv.find('.jj-ai-color-chips');
            
            if (!baseColor) {
                alert('색상을 선택하세요.');
                return;
            }
            
            $chipsDiv.html('<span class="spinner is-active" style="float:none;"></span>');
            $resultDiv.show();
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_generate_smart_palette',
                    nonce: jjAdminCenter.nonce,
                    base_color: baseColor,
                    harmony: harmony
                },
                success: function(response) {
                    if (response.success) {
                        const palette = response.data.palette.palette; // 배열
                        let html = '';
                        palette.forEach(function(color) {
                            html += '<div class="jj-ai-chip" style="width:40px; height:40px; background:' + color + '; border-radius:4px; cursor:pointer;" title="' + color + '" data-color="' + color + '"></div>';
                        });
                        $chipsDiv.html(html);
                        
                        // 적용하기 버튼 데이터 바인딩
                        $('#jj-btn-apply-ai-palette').data('palette', response.data.palette);
                    } else {
                        $chipsDiv.html('<span style="color:red;">' + response.data.message + '</span>');
                    }
                },
                error: function() {
                    $chipsDiv.html('<span style="color:red;">서버 오류가 발생했습니다.</span>');
                }
            });
        });
        
        // 팔레트 적용하기 버튼
        $wrap.on('click', '#jj-btn-apply-ai-palette', function(e) {
            e.preventDefault();
            const paletteData = $(this).data('palette');
            if (!paletteData) return;
            
            // Primary
            $('input[name*="[primary_color]"]').first().val(paletteData.primary).trigger('change');
            // Secondary
            $('input[name*="[secondary_color]"]').first().val(paletteData.secondary).trigger('change');
            
            alert('팔레트가 적용되었습니다. 저장 버튼을 눌러 확정하세요.');
        });

        // [v5.8.0] Cloud Export
        $wrap.on('click', '#jj-btn-cloud-export', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $result = $('#jj-cloud-export-result');
            
            $btn.prop('disabled', true).text('저장 중...');
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_cloud_export',
                    nonce: jjAdminCenter.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $result.find('.jj-code-box').text(response.data.share_code);
                        $result.slideDown();
                        alert('클라우드에 저장되었습니다. 공유 코드를 확인하세요.');
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('서버 통신 오류가 발생했습니다.');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('지금 저장하기');
                }
            });
        });

        // [v5.8.0] Cloud Import
        $wrap.on('click', '#jj-btn-cloud-import', function(e) {
            e.preventDefault();
            const code = $('#jj-cloud-share-code').val().trim();
            if (!code) {
                alert('공유 코드를 입력하세요.');
                return;
            }
            
            if (!confirm('현재 설정이 덮어씌워집니다. 계속하시겠습니까?')) {
                return;
            }
            
            const $btn = $(this);
            $btn.prop('disabled', true).text('불러오는 중...');
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_cloud_import',
                    nonce: jjAdminCenter.nonce,
                    share_code: code
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('서버 통신 오류가 발생했습니다.');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('불러오기');
                }
            });
        });

        // [v3.8.0 신규] 라이센스 관리 기능
        $('#jj-license-key-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $('#jj-save-license-btn');
            const $spinner = $('.jj-license-spinner');
            const $message = $('#jj-license-message');
            const licenseKey = $('#jj-license-key-input').val().trim();

            if (!licenseKey) {
                showLicenseMessage('error', '라이센스 키를 입력하세요.');
                return;
            }

            $btn.prop('disabled', true);
            $spinner.show();
            $message.hide();
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_save_license_key',
                    nonce: jjAdminCenter.nonce,
                    license_key: licenseKey
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    
                    if (response.success) {
                        showLicenseMessage('success', response.data.message);
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        showLicenseMessage('error', response.data.message || '라이센스 키 저장에 실패했습니다.');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    showLicenseMessage('error', '오류가 발생했습니다.');
                }
            });
        });
        
        // 라이센스 검증 버튼 클릭
        $('#jj-verify-license-btn').on('click', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $spinner = $('.jj-license-spinner');
            const $message = $('#jj-license-message');
            
            $btn.prop('disabled', true);
            $spinner.show();
            $message.hide();
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_verify_license_key',
                    nonce: jjAdminCenter.nonce
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    
                    if (response.success) {
                        const status = response.data.status;
                        let message = response.data.message;
                        
                        if (status.valid) {
                            showLicenseMessage('success', message);
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            showLicenseMessage('error', message);
                        }
                    } else {
                        showLicenseMessage('error', response.data.message || '라이센스 검증에 실패했습니다.');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    showLicenseMessage('error', '오류가 발생했습니다.');
                }
            });
        });
        
        // 라이센스 키 제거 버튼 클릭
        $('#jj-remove-license-btn').on('click', function(e) {
            e.preventDefault();
            if (!confirm('정말로 라이센스 키를 제거하시겠습니까? Free 버전으로 실행됩니다.')) {
                return;
            }
            
            const $btn = $(this);
            const $spinner = $('.jj-license-spinner');
            const $message = $('#jj-license-message');
            
            $btn.prop('disabled', true);
            $spinner.show();
            $message.hide();
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_save_license_key',
                    nonce: jjAdminCenter.nonce,
                    license_key: '' 
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    
                    showLicenseMessage('success', '라이센스 키가 제거되었습니다. Free 버전으로 실행됩니다.');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function() {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    showLicenseMessage('error', '오류가 발생했습니다.');
                }
            });
        });
        
        // 라이센스 키 복사 버튼
        $('.jj-copy-license-key-display').on('click', function(e) {
            e.preventDefault();
            const licenseKey = $(this).data('license-key');
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(licenseKey).select();
            document.execCommand('copy');
            $temp.remove();
            
            const $btn = $(this);
            const originalText = $btn.html();
            $btn.html('<span class="dashicons dashicons-yes" style="font-size: 12px; margin-top: 2px; color: #00a32a;"></span> 복사됨');
            
            setTimeout(function() {
                $btn.html(originalText);
            }, 2000);
        });
        
        function showLicenseMessage(type, message) {
            const $message = $('#jj-license-message');
            $message.removeClass('success error')
                    .addClass(type)
                    .html(message)
                    .fadeIn(300);
            
            setTimeout(function() {
                $message.fadeOut(300);
            }, 5000);
        }

        if (!$wrap.length) {
            return;
        }

        // [v6.3.0] 탭 전환 공통 함수
        function switchTab(tabId) {
            $wrap.find('.jj-admin-center-tabs li').removeClass('active');
            $wrap.find('.jj-admin-center-tabs li[data-tab="' + tabId + '"]').addClass('active');
            
            $wrap.find('.jj-admin-center-sidebar-nav a').removeClass('active');
            $wrap.find('.jj-admin-center-sidebar-nav a[data-tab="' + tabId + '"]').addClass('active');

            $wrap.find('.jj-admin-center-tab-content').removeClass('active');
            $wrap.find('.jj-admin-center-tab-content[data-tab="' + tabId + '"]').addClass('active');
            
            if (history.pushState) {
                history.pushState(null, null, '#' + tabId);
            } else {
                window.location.hash = tabId;
            }
        }

        $wrap.on('click', '.jj-admin-center-tabs a', function(e) {
            e.preventDefault();
            const $tab = $(this).closest('li');
            const tabId = $tab.data('tab');
            switchTab(tabId);
            
            if (tabId === 'admin-menu') {
                let tabInitRetryCount = 0;
                const tabInitMaxRetries = 10;
                const initSortableOnTabSwitch = function() {
                    const $menuList = $wrap.find('.jj-admin-center-menu-list');
                    const hasMenuItems = $menuList.length && $menuList.find('.jj-admin-center-menu-item').length > 0;
                    
                    if ($.fn.sortable && hasMenuItems) {
                        requestAnimationFrame(function() {
                            setTimeout(function() {
                                initializeSortable();
                            }, 100);
                        });
                    } else if (tabInitRetryCount < tabInitMaxRetries) {
                        tabInitRetryCount++;
                        setTimeout(initSortableOnTabSwitch, 200);
                    }
                };
                setTimeout(initSortableOnTabSwitch, 200);
            }
            
            if (tabId === 'colors' || tabId === 'visual') {
                setTimeout(function() {
                    initColorPickers();
                    loadPaletteChips(); 
                    initColorHistory(); 
                }, 200);
            }
        });

        let isDragging = false;
        let dragStartTime = 0;
        let dragStartPosition = null;
        
        $wrap.on('mousedown', '.jj-admin-center-menu-item', function(e) {
            if ($(e.target).closest('.jj-admin-center-menu-item-handle').length) {
                isDragging = false;
                dragStartTime = Date.now();
                dragStartPosition = { x: e.pageX, y: e.pageY };
                return true; 
            }
            isDragging = false;
            dragStartTime = 0;
            dragStartPosition = null;
        });
        
        $wrap.on('click', '.jj-admin-center-menu-item', function(e) {
            if ($(e.target).closest('.jj-toggle-submenu, .jj-admin-center-menu-item-handle').length) {
                return;
            }
            
            if (isDragging || (dragStartTime > 0 && Date.now() - dragStartTime < 300 && dragStartPosition)) {
                const currentPosition = { x: e.pageX, y: e.pageY };
                const distance = Math.sqrt(
                    Math.pow(currentPosition.x - dragStartPosition.x, 2) + 
                    Math.pow(currentPosition.y - dragStartPosition.y, 2)
                );
                if (distance > 5) return;
            }
            
            const $item = $(this);
            const itemId = $item.data('item-id');

            $wrap.find('.jj-admin-center-menu-item').removeClass('active');
            $item.addClass('active');

            loadMenuItemDetails(itemId);
        });

        function initColorPickers() {
            $wrap.find('.jj-admin-center-color-picker').each(function() {
                const $input = $(this);
                if ($input.data('wpColorPicker')) {
                    try {
                        $input.wpColorPicker('destroy');
                    } catch (e) {}
                }
                
                $input.wpColorPicker({
                    change: function(event, ui) {
                        const color = ui.color.toString();
                        $input.val(color).trigger('change');
                        updateColorPreview($input);
                    },
                    clear: function(event) {
                        $input.val('').trigger('change');
                        updateColorPreview($input);
                    }
                });
            });
        }

        function updateColorPreview($input) {
            const $preview = $input.closest('.jj-admin-center-color-input').find('.jj-admin-center-color-preview');
            const color = $input.val();

            if (color && /^#[0-9A-Fa-f]{6}$/.test(color)) {
                $preview.css('background-color', color);
                const $paletteChips = $input.closest('.jj-admin-center-color-input').find('.jj-palette-chip-inline');
                $paletteChips.css({
                    'border-color': '#ddd',
                    'border-width': '2px'
                });
                $paletteChips.filter('[data-color="' + color + '"]').css({
                    'border-color': '#2271b1',
                    'border-width': '3px'
                });
            } else {
                $preview.css('background-color', 'transparent');
            }
        }

        $wrap.on('input change', '.jj-admin-center-color-input input[type="text"]', function() {
            const $input = $(this);
            updateColorPreview($input);
            if ($input.data('wpColorPicker')) {
                try {
                    $input.wpColorPicker('color', $input.val());
                } catch (e) {}
            }
        });

        $wrap.find('.jj-admin-center-color-input input[type="text"]').each(function() {
            updateColorPreview($(this));
        });
        
        const COLOR_HISTORY_KEY = 'jj_color_history';
        const MAX_HISTORY_SIZE = 20;

        function getColorHistory() {
            try {
                const history = localStorage.getItem(COLOR_HISTORY_KEY);
                return history ? JSON.parse(history) : [];
            } catch (e) { return []; }
        }

        function saveColorToHistory(color) {
            if (!color || !/^#[0-9A-Fa-f]{6}$/.test(color)) return;
            let history = getColorHistory();
            history = history.filter(c => c !== color);
            history.unshift(color);
            if (history.length > MAX_HISTORY_SIZE) history = history.slice(0, MAX_HISTORY_SIZE);
            try {
                localStorage.setItem(COLOR_HISTORY_KEY, JSON.stringify(history));
            } catch (e) {}
        }

        function renderColorHistory($input) {
            const history = getColorHistory();
            const $historyContainer = $input.closest('.jj-admin-center-color-input').find('.jj-color-history');
            const $chipsContainer = $historyContainer.find('.jj-color-history-chips');
            
            if (history.length === 0) {
                $historyContainer.hide();
                return;
            }
            
            $historyContainer.show();
            $chipsContainer.empty();
            
            history.forEach(function(color) {
                const $chip = $('<div>')
                    .addClass('jj-color-history-chip')
                    .css('background-color', color)
                    .attr('data-color', color)
                    .attr('title', color)
                    .on('click', function() {
                        $input.val(color).trigger('change');
                        updateColorPreview($input);
                        if ($input.data('wpColorPicker')) {
                            try { $input.wpColorPicker('color', color); } catch (e) {}
                        }
                    });
                $chipsContainer.append($chip);
            });
        }

        function initColorHistory() {
            $wrap.find('.jj-admin-center-color-picker').each(function() {
                renderColorHistory($(this));
            });
        }

        $wrap.on('change', '.jj-admin-center-color-picker', function() {
            const color = $(this).val();
            if (color && /^#[0-9A-Fa-f]{6}$/.test(color)) {
                saveColorToHistory(color);
                renderColorHistory($(this));
            }
        });

        $wrap.on('click', '.jj-admin-center-color-preview', function(e) {
            e.preventDefault();
            const $input = $(this).closest('.jj-admin-center-color-input').find('.jj-admin-center-color-picker');
            if ($input.length && $input.data('wpColorPicker')) {
                $input.closest('.wp-picker-container').find('.wp-color-result').trigger('click');
            }
        });

        if ($wrap.find('.jj-admin-center-tab-content[data-tab="colors"]').hasClass('active')) {
            setTimeout(function() {
                initColorPickers();
                loadPaletteChips();
                initColorHistory();
            }, 200);
        }
        
        function loadPaletteChips() {
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_get_palette_data',
                    nonce: jjAdminCenter.nonce
                },
                success: function(response) {
                    if (response.success && response.data.palettes) {
                        const palettes = response.data.palettes;
                        $wrap.find('.jj-admin-center-palette-chips').each(function() {
                            const $container = $(this);
                            const $targetInput = $container.closest('.jj-admin-center-color-input').find('.jj-admin-center-color-picker').first();
                            const $chipsContainer = $container.find('.jj-palette-chips-container');
                            $chipsContainer.empty();
                            
                            Object.keys(palettes).forEach(function(paletteKey) {
                                const palette = palettes[paletteKey];
                                const colors = palette.colors || {};
                                Object.keys(colors).forEach(function(colorKey) {
                                    const color = colors[colorKey];
                                    if (color && /^#[0-9A-Fa-f]{6}$/.test(color)) {
                                        const $chip = $('<div class="jj-palette-chip-inline" style="background: ' + color + '" data-color="' + color + '"></div>');
                                        $chip.on('click', function() {
                                            const selectedColor = $(this).data('color');
                                            $targetInput.val(selectedColor).trigger('change');
                                            if ($targetInput.data('wpColorPicker')) {
                                                try { $targetInput.wpColorPicker('color', selectedColor); } catch (e) {}
                                            }
                                            updateColorPreview($targetInput);
                                        });
                                        $chipsContainer.append($chip);
                                    }
                                });
                            });
                        });
                    }
                }
            });
        }

        function collectChanges() {
            const changes = [];
            const $form = $('#jj-admin-center-form');
            
            $form.find('.jj-admin-center-color-picker').each(function() {
                const $input = $(this);
                const newValue = $input.val();
                const oldValue = $input.data('original-value') || '';
                if (newValue !== oldValue && newValue) {
                    changes.push({ label: $input.closest('tr').find('th').text().trim(), oldValue: oldValue || '(비어있음)', newValue: newValue });
                }
            });
            
            $form.find('input[type="text"]:not(.jj-admin-center-color-picker), textarea').each(function() {
                const $input = $(this);
                const name = $input.attr('name');
                if (!name || name.includes('_nonce')) return;
                const newValue = $input.val();
                const oldValue = $input.data('original-value') || '';
                if (newValue !== oldValue) {
                    changes.push({ label: $input.closest('tr').find('th').text().trim() || name, oldValue: oldValue || '(비어있음)', newValue: newValue || '(비어있음)' });
                }
            });
            
            return changes;
        }

        function showChangesPreview(changes) {
            const $modal = $('#jj-changes-preview-modal');
            const $list = $('#jj-changes-list');
            if (changes.length === 0) {
                $list.html('<p>변경된 내용이 없습니다.</p>');
            } else {
                let html = '';
                changes.forEach(function(change) {
                    html += '<div class="jj-change-item"><div class="jj-change-item-title">' + change.label + '</div>';
                    html += '<div class="jj-change-item-detail">이전: <span class="jj-change-old-value">' + change.oldValue + '</span> -> 변경: <span class="jj-change-new-value">' + change.newValue + '</span></div></div>';
                });
                $list.html(html);
            }
            $modal.fadeIn(200);
        }

        function executeSave() {
            const $form = $('#jj-admin-center-form');
            const $submitBtn = $form.find('button[type="submit"]');
            $submitBtn.prop('disabled', true);
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: $form.serialize() + '&action=jj_admin_center_save&security=' + jjAdminCenter.nonce,
                success: function(response) {
                    if (response.success) {
                        $('#jj-changes-preview-modal').fadeOut(200);
                        location.reload();
                    } else { alert(response.data.message); }
                },
                complete: function() { $submitBtn.prop('disabled', false); }
            });
        }

        $wrap.on('click', '.jj-modal-confirm-save', executeSave);
        $wrap.on('click', '.jj-modal-close, .jj-modal-cancel', function() { $('#jj-changes-preview-modal').fadeOut(200); });

        $('#jj-admin-center-form').on('submit', function(e) {
            e.preventDefault();
            showChangesPreview(collectChanges());
        });

        // ============================================================
        // Updates Tab (WP Plugins screen UX-aligned)
        // ============================================================
        function updateAutoUpdateBadge(isEnabled) {
            const $badge = $('.jj-admin-center-tab-content[data-tab="updates"]').find('.jj-status-badge').first();
            if (!$badge.length) return;
            $badge.toggleClass('active', !!isEnabled);
            $badge.toggleClass('inactive', !isEnabled);
            $badge.text(isEnabled ? 'AUTO UPDATE: ON' : 'AUTO UPDATE: OFF');
        }

        function syncAutoUpdateToggleUi() {
            const enabled = $('#jj_auto_update_enabled').is(':checked');
            const $btn = $('#jj-toggle-auto-update');
            if ($btn.length) {
                $btn.attr('data-enabled', enabled ? '1' : '0');
                $btn.text(enabled ? '비활성화' : '활성화');
            }
            updateAutoUpdateBadge(enabled);
        }

        // 코어 + 애드온(Updates Overview 테이블) 자동 업데이트 토글
        function setSuiteRowAutoUpdate($row, enabled) {
            const $badge = $row.find('.jj-suite-auto-badge').first();
            const $btn = $row.find('.jj-suite-toggle-auto-update').first();
            if ($badge.length) {
                $badge.toggleClass('active', !!enabled);
                $badge.toggleClass('inactive', !enabled);
                $badge.text(enabled ? 'AUTO UPDATE: ON' : 'AUTO UPDATE: OFF');
            }
            if ($btn.length) {
                $btn.attr('data-enabled', enabled ? '1' : '0');
                $btn.text(enabled ? '비활성화' : '활성화');
            }
        }

        $wrap.on('click', '.jj-suite-toggle-auto-update', function(e) {
            e.preventDefault();
            if (typeof jjAdminCenter === 'undefined' || !jjAdminCenter.ajax_url || !jjAdminCenter.nonce) return;

            const $btn = $(this);
            const $row = $btn.closest('.jj-suite-row');
            const plugin = $btn.attr('data-plugin') || '';
            const enabledNow = ($btn.attr('data-enabled') === '1');
            const target = !enabledNow;

            if (!plugin) return;

            $btn.prop('disabled', true);
            $.post(jjAdminCenter.ajax_url, {
                action: 'jj_toggle_auto_update_plugin',
                security: jjAdminCenter.nonce,
                plugin: plugin,
                enabled: target ? '1' : '0'
            }).done(function(resp) {
                if (resp && resp.success) {
                    setSuiteRowAutoUpdate($row, !!(resp.data && resp.data.enabled));
                    // 코어 토글이면 기존 UI도 함께 동기화
                    if (plugin.indexOf('acf-css-really-simple-style-guide.php') !== -1) {
                        $('#jj_auto_update_enabled').prop('checked', !!(resp.data && resp.data.enabled));
                        syncAutoUpdateToggleUi();
                    }
                } else {
                    const msg = resp && resp.data && resp.data.message ? resp.data.message : '자동 업데이트 토글에 실패했습니다.';
                    alert(msg);
                }
            }).fail(function() {
                alert('서버 통신 오류가 발생했습니다.');
            }).always(function() {
                $btn.prop('disabled', false);
            });
        });

        // Updates Overview: 검색/필터(미설치 숨김, 업데이트만)
        function filterUpdatesSuiteRows() {
            const q = ($('#jj-updates-suite-search').val() || '').toLowerCase().trim();
            const hideUninstalled = $('#jj-updates-suite-hide-uninstalled').is(':checked');
            const onlyUpdates = $('#jj-updates-suite-only-updates').is(':checked');

            $('.jj-suite-row').each(function() {
                const $row = $(this);
                const name = ($row.attr('data-name') || '').toLowerCase();
                const installed = ($row.attr('data-installed') === '1');
                const hasUpdate = ($row.attr('data-has-update') === '1');

                let visible = true;
                if (q && name.indexOf(q) === -1) visible = false;
                if (hideUninstalled && !installed) visible = false;
                if (onlyUpdates && !hasUpdate) visible = false;

                $row.toggle(visible);
            });
        }

        $wrap.on('input', '#jj-updates-suite-search', filterUpdatesSuiteRows);
        $wrap.on('change', '#jj-updates-suite-hide-uninstalled, #jj-updates-suite-only-updates', filterUpdatesSuiteRows);
        filterUpdatesSuiteRows();

        // 토글 버튼: 체크박스 상태만 즉시 변경 (저장은 별도)
        $wrap.on('click', '#jj-toggle-auto-update', function(e) {
            e.preventDefault();
            const $checkbox = $('#jj_auto_update_enabled');
            if (!$checkbox.length || $checkbox.is(':disabled')) return;
            $checkbox.prop('checked', !$checkbox.is(':checked')).trigger('change');
            syncAutoUpdateToggleUi();
        });

        $wrap.on('change', '#jj_auto_update_enabled', function() {
            syncAutoUpdateToggleUi();
        });

        // 업데이트 설정 저장
        $wrap.on('click', '#jj-save-update-settings', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $status = $('#jj-update-status');

            const payload = {
                action: 'jj_save_update_settings',
                security: jjAdminCenter.nonce,
                update_settings: {
                    auto_update_enabled: $('#jj_auto_update_enabled').is(':checked') ? '1' : '0',
                    update_channel: $('#jj_update_channel').val() || 'stable',
                    beta_updates_enabled: $('#jj_beta_updates_enabled').is(':checked') ? '1' : '0',
                    send_app_logs: $('#jj_send_app_logs').is(':checked') ? '1' : '0',
                    send_error_logs: $('#jj_send_error_logs').is(':checked') ? '1' : '0',
                }
            };

            $btn.prop('disabled', true);
            $status.html('<span class="spinner is-active" style="float:none; margin:0 6px 0 0;"></span> 저장 중...');

            $.post(jjAdminCenter.ajax_url, payload)
                .done(function(resp) {
                    if (resp && resp.success) {
                        $status.html('<div class="notice notice-success inline"><p><strong>저장 완료:</strong> 업데이트 설정이 저장되었습니다.</p></div>');
                    } else {
                        const msg = resp && resp.data && resp.data.message ? resp.data.message : '저장에 실패했습니다.';
                        $status.html('<div class="notice notice-error inline"><p><strong>오류:</strong> ' + msg + '</p></div>');
                    }
                })
                .fail(function() {
                    $status.html('<div class="notice notice-error inline"><p><strong>오류:</strong> 네트워크 오류가 발생했습니다.</p></div>');
                })
                .always(function() {
                    $btn.prop('disabled', false);
                    // UI 동기화
                    syncAutoUpdateToggleUi();
                });
        });

        // 지금 업데이트 확인
        $wrap.on('click', '#jj-check-updates-now', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $status = $('#jj-update-status');

            $btn.prop('disabled', true);
            $status.html('<span class="spinner is-active" style="float:none; margin:0 6px 0 0;"></span> 업데이트 확인 중...');

            $.post(jjAdminCenter.ajax_url, { action: 'jj_check_updates_now', security: jjAdminCenter.nonce })
                .done(function(resp) {
                    if (resp && resp.success) {
                        const d = resp.data || {};
                        if (d.has_update) {
                            $status.html('<div class="notice notice-warning inline"><p><strong>업데이트 가능:</strong> ' + (d.message || '') + '</p></div>');
                        } else {
                            $status.html('<div class="notice notice-success inline"><p><strong>최신 상태:</strong> ' + (d.message || '') + '</p></div>');
                        }
                    } else {
                        const msg = resp && resp.data && resp.data.message ? resp.data.message : '업데이트 확인에 실패했습니다.';
                        $status.html('<div class="notice notice-error inline"><p><strong>오류:</strong> ' + msg + '</p></div>');
                    }
                })
                .fail(function() {
                    $status.html('<div class="notice notice-error inline"><p><strong>오류:</strong> 네트워크 오류가 발생했습니다.</p></div>');
                })
                .always(function() {
                    $btn.prop('disabled', false);
                });
        });

        // 최초 1회 동기화
        syncAutoUpdateToggleUi();

        function loadMenuItemDetails(itemId) {
            $wrap.find('.jj-admin-center-item-details').removeClass('active');
            $wrap.find('.jj-admin-center-item-details[data-item-id="' + itemId + '"]').addClass('active');
        }

        function initializeSortable() {
            const $menuList = $wrap.find('.jj-admin-center-menu-list');
            if (!$menuList.length || !$.fn.sortable) return;
            $menuList.sortable({
                handle: '.jj-admin-center-menu-item-handle',
                placeholder: 'jj-sortable-placeholder',
                stop: function() { updateMenuOrder(); }
            });
        }

        function updateMenuOrder() {
            $wrap.find('.jj-admin-center-menu-list > .jj-admin-center-menu-item').each(function(index) {
                const order = index + 1;
                $(this).find('.jj-admin-center-menu-item-order').text(order);
                const itemId = $(this).data('item-id');
                $wrap.find('input[name="jj_admin_menu_layout[' + itemId + '][order]"]').val(order);
            });
        }

        // [Phase 6+] 시스템 상태: 자가 진단 실행 (AJAX)
        $wrap.on('click', '#jj-run-self-test', function(e) {
            e.preventDefault();
            if (typeof jjAdminCenter === 'undefined' || !jjAdminCenter.ajax_url || !jjAdminCenter.nonce) {
                alert('AJAX 설정이 초기화되지 않았습니다.');
                return;
            }

            const $btn = $(this);
            const $box = $('#jj-self-test-results');
            const $spinner = $box.find('.spinner');
            const $statusText = $box.find('.jj-test-status-text');
            const $list = $box.find('.jj-test-results-list');

            $box.show();
            $list.empty();
            $spinner.addClass('is-active').show();
            $statusText.text('진단 중...');
            $btn.prop('disabled', true);

            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_run_self_test',
                    security: jjAdminCenter.nonce
                }
            }).done(function(resp) {
                $spinner.removeClass('is-active').hide();

                if (!resp || !resp.success) {
                    const msg = (resp && resp.data && resp.data.message) ? resp.data.message : '자가 진단에 실패했습니다.';
                    $statusText.text('오류');
                    $list.append('<li style="padding:8px 10px; border:1px solid #d63638; border-radius:6px; background:#fcf0f1; color:#8a2424;">' + msg + '</li>');
                    return;
                }

                const results = (resp.data && Array.isArray(resp.data.results)) ? resp.data.results : [];
                if (!results.length) {
                    $statusText.text('완료');
                    $list.append('<li style="padding:8px 10px; border:1px solid #c3c4c7; border-radius:6px; background:#f6f7f7;">결과가 없습니다.</li>');
                    return;
                }

                let counts = { pass: 0, warn: 0, fail: 0, skip: 0 };
                results.forEach(function(r) {
                    const t = (r && r.test) ? String(r.test) : '(unknown)';
                    const s = (r && r.status) ? String(r.status) : 'warn';
                    const m = (r && r.message) ? String(r.message) : '';

                    if (counts[s] !== undefined) counts[s]++; else counts.warn++;

                    let color = '#2271b1';
                    let bg = '#f0f6fc';
                    let border = '#b6d1ea';
                    let label = 'INFO';

                    if (s === 'pass') { color = '#1d6b2f'; bg = '#edfaef'; border = '#b7e1c2'; label = 'PASS'; }
                    if (s === 'warn') { color = '#8a5a00'; bg = '#fff7e5'; border = '#f3d19e'; label = 'WARN'; }
                    if (s === 'fail') { color = '#8a2424'; bg = '#fcf0f1'; border = '#f0b6b6'; label = 'FAIL'; }
                    if (s === 'skip') { color = '#475569'; bg = '#f1f5f9'; border = '#cbd5e1'; label = 'SKIP'; }

                    const badge = '<span style="display:inline-block; min-width:46px; text-align:center; padding:2px 6px; margin-right:8px; border-radius:999px; font-weight:700; font-size:11px; border:1px solid ' + border + '; background:' + bg + '; color:' + color + ';">' + label + '</span>';
                    const item = '<li style="display:flex; gap:10px; align-items:flex-start; padding:8px 10px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; margin-bottom:6px;">'
                        + '<div style="flex:0 0 auto;">' + badge + '</div>'
                        + '<div style="flex:1 1 auto;">'
                        + '<div style="font-weight:700; color:#0f172a;">' + t + '</div>'
                        + (m ? ('<div style="font-size:12px; color:#64748b; margin-top:2px;">' + m + '</div>') : '')
                        + '</div>'
                        + '</li>';

                    $list.append(item);
                });

                $statusText.text('완료 (PASS ' + counts.pass + ' / WARN ' + counts.warn + ' / FAIL ' + counts.fail + ' / SKIP ' + counts.skip + ')');
            }).fail(function() {
                $spinner.removeClass('is-active').hide();
                $statusText.text('오류');
                $list.append('<li style="padding:8px 10px; border:1px solid #d63638; border-radius:6px; background:#fcf0f1; color:#8a2424;">서버 통신 오류가 발생했습니다.</li>');
            }).always(function() {
                $btn.prop('disabled', false);
            });
        });

        initializeSortable();
        $wrap.on('click', '.jj-admin-center-sidebar-nav a', function(e) {
            e.preventDefault();
            switchTab($(this).data('tab'));
        });

        $wrap.on('click', '.jj-sidebar-toggle', function() {
            $('.jj-admin-center-sidebar').toggleClass('jj-sidebar-open');
        });
    });
})(jQuery);
