<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$sections_layout = $this->get_sections_layout();
?>
<div class="jj-admin-center-tab-content" data-tab="section-layout">
<?php
// [v5.5.0] Feature Gating: Section Layout
$has_access = true;
if ( class_exists( 'JJ_Edition_Controller' ) ) {
    $has_access = JJ_Edition_Controller::instance()->has_capability( 'admin_center_full' );
}

if ( ! $has_access ) : ?>
    <div class="jj-feature-locked">
        <div class="jj-lock-icon"><span class="dashicons dashicons-lock"></span></div>
        <h3><?php esc_html_e( '이 기능은 Premium 버전 이상에서 사용할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <p><?php esc_html_e( '섹션 레이아웃을 자유롭게 변경하고 탭을 제어하려면 업그레이드하세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
        <a href="https://j-j-labs.com" target="_blank" class="button button-primary"><?php esc_html_e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?></a>
    </div>
<?php else : ?>

    <h3><?php esc_html_e( '스타일 센터 섹션 레이아웃', 'acf-css-really-simple-style-management-center' ); ?></h3>
<p class="description jj-section-layout-description" data-tooltip="section-layout-description">
    <?php esc_html_e( '각 섹션의 표시 여부와 순서를 제어할 수 있습니다. 번호는 표시가 허용된 섹션의 순서에 맞추어 자동으로 계산됩니다. [v5.0.0] 각 섹션 내의 탭도 활성화/비활성화할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
    <span class="dashicons dashicons-editor-help" style="margin-left: 5px; cursor: help;" aria-label="<?php esc_attr_e( '도움말', 'acf-css-really-simple-style-management-center' ); ?>"></span>
</p>

<!-- [v5.0.0] 경고 메시지 -->
<div class="notice notice-warning inline jj-section-disable-warning" style="display: none; margin: 15px 0;">
    <p>
        <strong><?php esc_html_e( '주의:', 'acf-css-really-simple-style-management-center' ); ?></strong>
        <?php esc_html_e( '섹션 또는 탭을 비활성화하면 스타일 센터에서 해당 섹션/탭이 표시되지 않습니다. 비활성화된 섹션/탭의 설정 데이터는 보존되지만 UI에서 접근할 수 없게 됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
    </p>
</div>

<!-- [v5.0.0] 일괄 작업 및 검색 -->
<div class="jj-bulk-actions" style="margin-bottom: 15px;">
    <button type="button" class="button button-small jj-bulk-enable-all">
        <span class="dashicons dashicons-yes-alt" style="font-size: 16px; margin-top: 3px;"></span>
        <?php esc_html_e( '모두 활성화', 'acf-css-really-simple-style-management-center' ); ?>
    </button>
    <button type="button" class="button button-small jj-bulk-disable-all">
        <span class="dashicons dashicons-dismiss" style="font-size: 16px; margin-top: 3px;"></span>
        <?php esc_html_e( '모두 비활성화', 'acf-css-really-simple-style-management-center' ); ?>
    </button>
    <button type="button" class="button button-small jj-reset-to-defaults" style="margin-left: auto;">
        <span class="dashicons dashicons-undo" style="font-size: 16px; margin-top: 3px;"></span>
        <?php esc_html_e( '기본값으로 되돌리기', 'acf-css-really-simple-style-management-center' ); ?>
    </button>
</div>

<!-- [v5.0.0] 검색/필터 -->
<div class="jj-section-filter">
    <input type="search" 
           id="jj-section-search" 
           placeholder="<?php esc_attr_e( '섹션 또는 탭 검색...', 'acf-css-really-simple-style-management-center' ); ?>"
           style="width: 100%; max-width: 400px; padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 3px; font-size: 13px;" />
</div>

<table class="form-table" role="presentation" id="jj-sections-layout-table">
    <thead>
    <tr>
        <th style="width: 30px;">
            <input type="checkbox" 
                   id="jj-select-all-sections" 
                   class="jj-select-all-checkbox" 
                   title="<?php esc_attr_e( '전체 선택/해제', 'acf-css-really-simple-style-management-center' ); ?>" />
        </th>
        <th><?php esc_html_e( '섹션', 'acf-css-really-simple-style-management-center' ); ?></th>
        <th><?php esc_html_e( '표시', 'acf-css-really-simple-style-management-center' ); ?></th>
        <th><?php esc_html_e( '순서', 'acf-css-really-simple-style-management-center' ); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ( $this->get_default_sections_layout() as $slug => $meta ) :
        $current = $sections_layout[ $slug ] ?? $meta;
        $enabled = ! empty( $current['enabled'] );
        $order   = isset( $current['order'] ) ? (int) $current['order'] : (int) $meta['order'];
        $tabs    = isset( $current['tabs'] ) ? $current['tabs'] : ( isset( $meta['tabs'] ) ? $meta['tabs'] : array() );
        ?>
        <tr class="jj-section-row <?php echo $enabled ? '' : 'jj-section-row-disabled'; ?>" 
            data-section-slug="<?php echo esc_attr( $slug ); ?>"
            data-section-label="<?php echo esc_attr( strtolower( $meta['label'] ) ); ?>">
            <td style="width: 30px; text-align: center;">
                <input type="checkbox" 
                       class="jj-section-checkbox" 
                       data-section="<?php echo esc_attr( $slug ); ?>"
                       <?php checked( $enabled ); ?> />
            </td>
            <th scope="row">
                <?php echo esc_html( $meta['label'] ); ?>
                <?php if ( ! $enabled ) : ?>
                    <span class="jj-disabled-icon" title="<?php esc_attr_e( '비활성화됨', 'acf-css-really-simple-style-management-center' ); ?>"></span>
                <?php else : ?>
                    <span class="jj-enabled-icon" title="<?php esc_attr_e( '활성화됨', 'acf-css-really-simple-style-management-center' ); ?>"></span>
                <?php endif; ?>
                <div style="font-size:11px;color:#666;margin-top:4px;">
                    <code><?php echo esc_html( $slug ); ?></code>
                </div>
            </th>
            <td>
                <label data-tooltip="section-enabled">
                    <input type="checkbox"
                           name="jj_section_layout[<?php echo esc_attr( $slug ); ?>][enabled]"
                           value="1"
                           class="jj-section-enabled-checkbox"
                           data-section="<?php echo esc_attr( $slug ); ?>"
                        <?php checked( $enabled ); ?> />
                    <?php esc_html_e( '표시', 'acf-css-really-simple-style-management-center' ); ?>
                </label>
            </td>
            <td>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <input type="number"
                           name="jj_section_layout[<?php echo esc_attr( $slug ); ?>][order]"
                           value="<?php echo esc_attr( $order ); ?>"
                           class="jj-section-order-input"
                           data-section="<?php echo esc_attr( $slug ); ?>"
                           style="width:80px;" />
                    <span class="jj-section-order-preview" 
                          data-section="<?php echo esc_attr( $slug ); ?>"
                          style="font-size: 12px; color: #646970; font-weight: 600;">
                        <?php 
                        // 활성화된 섹션 중에서의 순서 계산
                        $enabled_sections = array_filter(
                            $sections_layout,
                            function($meta) {
                                return !empty($meta['enabled']);
                            }
                        );
                        uasort($enabled_sections, function($a, $b) {
                            return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
                        });
                        $display_order = 1;
                        foreach ($enabled_sections as $s => $m) {
                            if ($s === $slug) {
                                echo '→ ' . $display_order;
                                break;
                            }
                            $display_order++;
                        }
                        ?>
                    </span>
                </div>
            </td>
        </tr>
        <?php if ( ! empty( $meta['tabs'] ) && is_array( $meta['tabs'] ) ) : ?>
        <tr class="jj-section-tabs-row" data-section="<?php echo esc_attr( $slug ); ?>">
            <td colspan="4" style="padding: 0 20px 15px 40px; border-top: none;">
                <div class="jj-section-tabs-container">
                <div style="margin-top: 10px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h4 style="margin: 0; font-size: 13px;">
                            <?php esc_html_e( '섹션 내 탭 활성화/비활성화', 'acf-css-really-simple-style-management-center' ); ?>
                        </h4>
                        <label style="font-size: 12px; font-weight: normal; cursor: pointer;">
                            <input type="checkbox" 
                                   class="jj-select-all-tabs-checkbox" 
                                   data-section="<?php echo esc_attr( $slug ); ?>"
                                   <?php 
                                   $all_tabs_enabled = true;
                                   foreach ( $meta['tabs'] as $tab_slug => $tab_meta ) {
                                       $tab_current = isset( $tabs[ $tab_slug ] ) ? $tabs[ $tab_slug ] : $tab_meta;
                                       $tab_enabled_check = ! empty( $tab_current['enabled'] );
                                       if ( ! $tab_enabled_check ) {
                                           $all_tabs_enabled = false;
                                           break;
                                       }
                                   }
                                   checked( $all_tabs_enabled );
                                   ?> />
                            <?php esc_html_e( '전체 선택', 'acf-css-really-simple-style-management-center' ); ?>
                        </label>
                    </div>
                    <table class="form-table" style="margin: 0;">
                        <tbody>
                        <?php foreach ( $meta['tabs'] as $tab_slug => $tab_meta ) :
                            $tab_current = isset( $tabs[ $tab_slug ] ) ? $tabs[ $tab_slug ] : $tab_meta;
                            $tab_enabled = ! empty( $tab_current['enabled'] );
                            ?>
                            <tr class="jj-tab-row <?php echo $tab_enabled ? '' : 'jj-tab-row-disabled'; ?>"
                                data-tab-slug="<?php echo esc_attr( $tab_slug ); ?>"
                                data-tab-label="<?php echo esc_attr( strtolower( $tab_meta['label'] ) ); ?>">
                                <th scope="row" style="padding: 8px 0; font-weight: normal; width: 200px;">
                                    <?php echo esc_html( $tab_meta['label'] ); ?>
                                    <?php if ( ! $tab_enabled ) : ?>
                                        <span class="jj-disabled-icon" title="<?php esc_attr_e( '비활성화됨', 'acf-css-really-simple-style-management-center' ); ?>"></span>
                                    <?php else : ?>
                                        <span class="jj-enabled-icon" title="<?php esc_attr_e( '활성화됨', 'acf-css-really-simple-style-management-center' ); ?>"></span>
                                    <?php endif; ?>
                                    <div style="font-size:10px;color:#999;margin-top:2px;">
                                        <code><?php echo esc_html( $tab_slug ); ?></code>
                                    </div>
                                </th>
                                <td style="padding: 8px 0;">
                                    <label data-tooltip="tab-enabled">
                                        <input type="checkbox"
                                               name="jj_section_layout[<?php echo esc_attr( $slug ); ?>][tabs][<?php echo esc_attr( $tab_slug ); ?>][enabled]"
                                               value="1"
                                               class="jj-tab-enabled-checkbox"
                                               data-section="<?php echo esc_attr( $slug ); ?>"
                                               data-tab="<?php echo esc_attr( $tab_slug ); ?>"
                                            <?php checked( $tab_enabled ); ?> />
                                        <?php esc_html_e( '활성화', 'acf-css-really-simple-style-management-center' ); ?>
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                </div>
            </td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; // End Feature Gating ?>
</div>

