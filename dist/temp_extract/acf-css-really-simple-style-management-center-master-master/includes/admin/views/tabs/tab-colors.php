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

