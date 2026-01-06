<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$colors_layout = $this->get_admin_menu_colors();
?>
<div class="jj-admin-center-tab-content" data-tab="colors">
    <div class="jj-ai-palette-generator" style="margin-bottom: 20px; padding: 15px; background: #f0f0f1; border-radius: 5px; border-left: 4px solid #72aee6;">
        <h4 style="margin-top: 0;"><?php esc_html_e( '🤖 AI 스타일 인텔리전스', 'acf-css-really-simple-style-management-center' ); ?></h4>
        <p style="margin-bottom: 10px;"><?php esc_html_e( '메인 색상 하나만 고르면, 전문적인 팔레트를 자동으로 생성해 드립니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
        
        <div style="display: flex; align-items: center; gap: 10px;">
            <input type="text" id="jj-ai-base-color" class="jj-color-picker" value="#2271b1" />
            <select id="jj-ai-harmony">
                <option value="monochromatic"><?php esc_html_e( '단색 (Monochromatic)', 'acf-css-really-simple-style-management-center' ); ?></option>
                <option value="analogous"><?php esc_html_e( '유사색 (Analogous)', 'acf-css-really-simple-style-management-center' ); ?></option>
                <option value="complementary"><?php esc_html_e( '보색 (Complementary)', 'acf-css-really-simple-style-management-center' ); ?></option>
                <option value="triadic"><?php esc_html_e( '3색 조화 (Triadic)', 'acf-css-really-simple-style-management-center' ); ?></option>
            </select>
            <button type="button" class="button button-secondary" id="jj-btn-generate-palette">
                <?php esc_html_e( '팔레트 생성', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
        </div>
        <div id="jj-ai-palette-result" style="margin-top: 15px; display: none;">
            <strong><?php esc_html_e( '추천 팔레트:', 'acf-css-really-simple-style-management-center' ); ?></strong>
            <div class="jj-ai-color-chips" style="display: flex; gap: 5px; margin-top: 5px;">
                <!-- 결과가 여기에 표시됨 -->
            </div>
            <button type="button" class="button button-primary" id="jj-btn-apply-ai-palette" style="margin-top: 10px;">
                <?php esc_html_e( '이 팔레트 적용하기', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
        </div>
    </div>

    <!-- [v22.6.0] 다크모드 프리셋 선택기 -->
    <div class="jj-darkmode-presets" style="margin-bottom: 20px; padding: 20px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 8px; color: #fff;">
        <h4 style="margin-top: 0; color: #fff; display: flex; align-items: center; gap: 8px;">
            <span class="dashicons dashicons-admin-appearance" style="font-size: 20px;"></span>
            <?php esc_html_e( '다크모드 프리셋', 'acf-css-really-simple-style-management-center' ); ?>
        </h4>
        <p style="margin-bottom: 15px; opacity: 0.85; font-size: 13px;">
            <?php esc_html_e( '원클릭으로 전문적인 관리자 다크모드 테마를 적용하세요.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>

        <div class="jj-preset-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px;">
            <!-- Midnight Blue -->
            <div class="jj-preset-card" data-preset="midnight_blue"
                 data-colors='{"sidebar_bg":"#0f172a","sidebar_text":"#94a3b8","sidebar_text_hover":"#f1f5f9","sidebar_bg_hover":"#1e293b","sidebar_bg_active":"#334155","sidebar_text_active":"#ffffff","topbar_bg":"#020617","topbar_text":"#94a3b8","topbar_text_hover":"#ffffff"}'
                 style="background: #0f172a; border: 2px solid #334155; border-radius: 6px; padding: 12px; cursor: pointer; transition: all 0.2s;">
                <div style="display: flex; gap: 4px; margin-bottom: 8px;">
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #0f172a; border: 1px solid #475569;"></span>
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #1e293b;"></span>
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #334155;"></span>
                </div>
                <strong style="font-size: 13px;"><?php esc_html_e( 'Midnight Blue', 'acf-css-really-simple-style-management-center' ); ?></strong>
                <p style="font-size: 11px; opacity: 0.7; margin: 4px 0 0 0;"><?php esc_html_e( '깊고 차분한 네이비', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>

            <!-- Carbon Black -->
            <div class="jj-preset-card" data-preset="carbon_black"
                 data-colors='{"sidebar_bg":"#171717","sidebar_text":"#a3a3a3","sidebar_text_hover":"#fafafa","sidebar_bg_hover":"#262626","sidebar_bg_active":"#404040","sidebar_text_active":"#ffffff","topbar_bg":"#0a0a0a","topbar_text":"#a3a3a3","topbar_text_hover":"#ffffff"}'
                 style="background: #171717; border: 2px solid #404040; border-radius: 6px; padding: 12px; cursor: pointer; transition: all 0.2s;">
                <div style="display: flex; gap: 4px; margin-bottom: 8px;">
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #0a0a0a; border: 1px solid #525252;"></span>
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #171717;"></span>
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #262626;"></span>
                </div>
                <strong style="font-size: 13px;"><?php esc_html_e( 'Carbon Black', 'acf-css-really-simple-style-management-center' ); ?></strong>
                <p style="font-size: 11px; opacity: 0.7; margin: 4px 0 0 0;"><?php esc_html_e( '순수한 블랙 모노', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>

            <!-- Ocean Deep -->
            <div class="jj-preset-card" data-preset="ocean_deep"
                 data-colors='{"sidebar_bg":"#0c4a6e","sidebar_text":"#7dd3fc","sidebar_text_hover":"#f0f9ff","sidebar_bg_hover":"#075985","sidebar_bg_active":"#0369a1","sidebar_text_active":"#ffffff","topbar_bg":"#082f49","topbar_text":"#7dd3fc","topbar_text_hover":"#ffffff"}'
                 style="background: #0c4a6e; border: 2px solid #0369a1; border-radius: 6px; padding: 12px; cursor: pointer; transition: all 0.2s;">
                <div style="display: flex; gap: 4px; margin-bottom: 8px;">
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #082f49; border: 1px solid #0284c7;"></span>
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #0c4a6e;"></span>
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #0369a1;"></span>
                </div>
                <strong style="font-size: 13px;"><?php esc_html_e( 'Ocean Deep', 'acf-css-really-simple-style-management-center' ); ?></strong>
                <p style="font-size: 11px; opacity: 0.7; margin: 4px 0 0 0;"><?php esc_html_e( '깊은 바다의 청량함', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>

            <!-- Forest Night -->
            <div class="jj-preset-card" data-preset="forest_night"
                 data-colors='{"sidebar_bg":"#14532d","sidebar_text":"#86efac","sidebar_text_hover":"#f0fdf4","sidebar_bg_hover":"#166534","sidebar_bg_active":"#15803d","sidebar_text_active":"#ffffff","topbar_bg":"#052e16","topbar_text":"#86efac","topbar_text_hover":"#ffffff"}'
                 style="background: #14532d; border: 2px solid #15803d; border-radius: 6px; padding: 12px; cursor: pointer; transition: all 0.2s;">
                <div style="display: flex; gap: 4px; margin-bottom: 8px;">
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #052e16; border: 1px solid #22c55e;"></span>
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #14532d;"></span>
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #166534;"></span>
                </div>
                <strong style="font-size: 13px;"><?php esc_html_e( 'Forest Night', 'acf-css-really-simple-style-management-center' ); ?></strong>
                <p style="font-size: 11px; opacity: 0.7; margin: 4px 0 0 0;"><?php esc_html_e( '숲의 깊은 녹음', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>

            <!-- Purple Haze -->
            <div class="jj-preset-card" data-preset="purple_haze"
                 data-colors='{"sidebar_bg":"#3b0764","sidebar_text":"#c4b5fd","sidebar_text_hover":"#faf5ff","sidebar_bg_hover":"#581c87","sidebar_bg_active":"#6b21a8","sidebar_text_active":"#ffffff","topbar_bg":"#2e1065","topbar_text":"#c4b5fd","topbar_text_hover":"#ffffff"}'
                 style="background: #3b0764; border: 2px solid #6b21a8; border-radius: 6px; padding: 12px; cursor: pointer; transition: all 0.2s;">
                <div style="display: flex; gap: 4px; margin-bottom: 8px;">
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #2e1065; border: 1px solid #a855f7;"></span>
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #3b0764;"></span>
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #581c87;"></span>
                </div>
                <strong style="font-size: 13px;"><?php esc_html_e( 'Purple Haze', 'acf-css-really-simple-style-management-center' ); ?></strong>
                <p style="font-size: 11px; opacity: 0.7; margin: 4px 0 0 0;"><?php esc_html_e( '신비로운 보라빛', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>

            <!-- Cyber Neon -->
            <div class="jj-preset-card" data-preset="cyber_neon"
                 data-colors='{"sidebar_bg":"#020617","sidebar_text":"#22d3ee","sidebar_text_hover":"#f0fdff","sidebar_bg_hover":"#0f172a","sidebar_bg_active":"#1e293b","sidebar_text_active":"#a5f3fc","topbar_bg":"#000000","topbar_text":"#22d3ee","topbar_text_hover":"#a5f3fc"}'
                 style="background: #020617; border: 2px solid #22d3ee; border-radius: 6px; padding: 12px; cursor: pointer; transition: all 0.2s;">
                <div style="display: flex; gap: 4px; margin-bottom: 8px;">
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #000000; border: 1px solid #22d3ee;"></span>
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #06b6d4;"></span>
                    <span style="width: 16px; height: 16px; border-radius: 50%; background: #a5f3fc;"></span>
                </div>
                <strong style="font-size: 13px;"><?php esc_html_e( 'Cyber Neon', 'acf-css-really-simple-style-management-center' ); ?></strong>
                <p style="font-size: 11px; opacity: 0.7; margin: 4px 0 0 0;"><?php esc_html_e( '미래적 사이버펑크', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>
        </div>
    </div>

    <h3><?php esc_html_e( '관리자 메뉴 / 상단바 색상', 'acf-css-really-simple-style-management-center' ); ?></h3>
    <p class="description">
        <?php esc_html_e( '좌측 관리자 메뉴와 상단 관리자 바에 사용할 배경/텍스트 색상을 지정할 수 있습니다. #RRGGBB 형식으로 입력해 주세요.', 'acf-css-really-simple-style-management-center' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
    <tr>
        <th scope="row"><?php esc_html_e( '사이드바 배경', 'acf-css-really-simple-style-management-center' ); ?></th>
        <td>
            <div class="jj-admin-center-color-input">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <input type="text"
                           name="jj_admin_menu_colors[sidebar_bg]"
                           value="<?php echo esc_attr( $colors_layout['sidebar_bg'] ?? $this->get_default_admin_colors()['sidebar_bg'] ); ?>" 
                           class="regular-text jj-admin-center-color-picker"
                           placeholder="#1f2933"
                           style="flex: 1; max-width: 200px;" />
                    <div class="jj-admin-center-color-preview" style="width: 40px; height: 40px; border: 2px solid #c3c4c7; border-radius: 4px; cursor: pointer; flex-shrink: 0;"></div>
                </div>
                <!-- 팔레트 컬러칩 선택 영역 -->
                <div class="jj-admin-center-palette-chips" data-target="input[name='jj_admin_menu_colors[sidebar_bg]']" style="margin-top: 10px; padding: 10px; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 4px;">
                    <p style="margin: 0 0 8px 0; font-size: 12px; color: #666;">
                        <strong><?php esc_html_e( '팔레트에서 선택:', 'acf-css-really-simple-style-management-center' ); ?></strong>
                    </p>
                    <div class="jj-palette-chips-container" style="display: flex; flex-wrap: wrap; gap: 6px;">
                        <span class="spinner is-active" style="float: none; margin: 0;"></span>
                    </div>
                </div>
                <div class="jj-admin-center-color-tools" style="margin-top: 8px; display: flex; gap: 8px;">
                    <button type="button" class="button button-small jj-admin-center-eyedropper" data-target="input[name='jj_admin_menu_colors[sidebar_bg]']">
                        <span class="dashicons dashicons-admin-appearance" style="margin-top: 3px;"></span> <?php esc_html_e( '스포이드', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                </div>
            </div>
        </td>
    </tr>
    <?php
    // 색상 필드 정의 (반복 작업을 위한 배열)
    $color_fields = array(
        'sidebar_text' => array(
            'label' => __( '사이드바 텍스트 (기본)', 'acf-css-really-simple-style-management-center' ),
            'placeholder' => '#d9e2ec',
        ),
        'sidebar_text_hover' => array(
            'label' => __( '사이드바 텍스트 (호버)', 'acf-css-really-simple-style-management-center' ),
            'placeholder' => '#ffffff',
        ),
        'sidebar_bg_hover' => array(
            'label' => __( '사이드바 배경 (호버)', 'acf-css-really-simple-style-management-center' ),
            'placeholder' => '#111827',
        ),
        'sidebar_bg_active' => array(
            'label' => __( '사이드바 배경 (활성 메뉴)', 'acf-css-really-simple-style-management-center' ),
            'placeholder' => '#111827',
        ),
        'sidebar_text_active' => array(
            'label' => __( '사이드바 텍스트 (활성 메뉴)', 'acf-css-really-simple-style-management-center' ),
            'placeholder' => '#ffffff',
        ),
        'topbar_bg' => array(
            'label' => __( '상단바 배경', 'acf-css-really-simple-style-management-center' ),
            'placeholder' => '#111827',
        ),
        'topbar_text' => array(
            'label' => __( '상단바 텍스트 (기본)', 'acf-css-really-simple-style-management-center' ),
            'placeholder' => '#d9e2ec',
        ),
        'topbar_text_hover' => array(
            'label' => __( '상단바 텍스트 (호버)', 'acf-css-really-simple-style-management-center' ),
            'placeholder' => '#ffffff',
        ),
    );

    foreach ( $color_fields as $field_key => $field_data ) :
        $field_value = $colors_layout[ $field_key ] ?? $this->get_default_admin_colors()[ $field_key ];
        $field_name = 'jj_admin_menu_colors[' . $field_key . ']';
    ?>
    <tr>
        <th scope="row"><?php echo esc_html( $field_data['label'] ); ?></th>
        <td>
            <div class="jj-admin-center-color-input">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <input type="text"
                           name="<?php echo esc_attr( $field_name ); ?>"
                           value="<?php echo esc_attr( $field_value ); ?>"
                           class="regular-text jj-admin-center-color-picker"
                           placeholder="<?php echo esc_attr( $field_data['placeholder'] ); ?>"
                           style="flex: 1; max-width: 200px;" />
                    <div class="jj-admin-center-color-preview" style="width: 40px; height: 40px; border: 2px solid #c3c4c7; border-radius: 4px; cursor: pointer; flex-shrink: 0;"></div>
                </div>
                <!-- 팔레트 컬러칩 선택 영역 -->
                <div class="jj-admin-center-palette-chips" data-target="input[name='<?php echo esc_attr( $field_name ); ?>']" style="margin-top: 10px; padding: 10px; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 4px;">
                    <p style="margin: 0 0 8px 0; font-size: 12px; color: #666;">
                        <strong><?php esc_html_e( '팔레트에서 선택:', 'acf-css-really-simple-style-management-center' ); ?></strong>
                    </p>
                    <div class="jj-palette-chips-container" style="display: flex; flex-wrap: wrap; gap: 6px;">
                        <span class="spinner is-active" style="float: none; margin: 0;"></span>
                    </div>
                </div>
                <div class="jj-admin-center-color-tools" style="margin-top: 8px; display: flex; gap: 8px;">
                    <button type="button" class="button button-small jj-admin-center-eyedropper" data-target="input[name='<?php echo esc_attr( $field_name ); ?>']">
                        <span class="dashicons dashicons-admin-appearance" style="margin-top: 3px;"></span> <?php esc_html_e( '스포이드', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                </div>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
        </tbody>
    </table>

    <!-- 팔레트 선택 모달 -->
    <div id="jj-admin-center-palette-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 100000; overflow-y: auto;">
        <div style="background: #fff; margin: 50px auto; max-width: 600px; padding: 25px; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">
            <h3 style="margin-top: 0;"><?php esc_html_e( '팔레트에서 색상 불러오기', 'acf-css-really-simple-style-management-center' ); ?></h3>
            <p class="description"><?php esc_html_e( '불러올 팔레트를 선택하세요. 선택한 색상이 현재 입력 필드에 적용됩니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
            
            <div id="jj-admin-center-palette-list" style="margin: 20px 0;">
                <!-- 팔레트 목록이 JavaScript로 동적으로 추가됨 -->
            </div>
            
            <div style="margin-top: 20px; text-align: right;">
                <button type="button" class="button button-secondary jj-admin-center-palette-modal-close">
                    <?php esc_html_e( '취소', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
            </div>
        </div>
    </div>

    <p class="submit">
        <button type="button" id="jj-admin-center-reset-colors" class="button button-secondary">
            <?php esc_html_e( '색상 기본값으로 되돌리기', 'acf-css-really-simple-style-management-center' ); ?>
        </button>
    </p>
</div>

