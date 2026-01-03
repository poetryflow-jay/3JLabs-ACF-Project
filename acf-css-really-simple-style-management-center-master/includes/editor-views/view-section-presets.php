<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Design Presets View
 * - 제니(CPO)의 마케팅 감성이 반영된 프리셋 선택 화면
 */
$presets = JJ_Style_Presets::instance()->get_presets();
?>
<div class="jj-presets-container">
    <div class="jj-section-header">
        <h2><?php _e( '🎨 디자인 프리셋 라이브러리', 'acf-css-really-simple-style-management-center' ); ?></h2>
        <p class="description"><?php _e( '전문 디자이너 제니가 설계한 스타일을 클릭 한 번으로 적용해보세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
    </div>

    <div class="jj-presets-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; margin-top: 20px;">
        <?php foreach ( $presets as $id => $data ) : ?>
            <div class="jj-preset-card" data-preset-id="<?php echo esc_attr( $id ); ?>" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <div class="jj-preset-thumb" style="height: 120px; background: <?php echo esc_attr( $data['colors']['primary'] ); ?>; display: flex; align-items: center; justify-content: center; position: relative;">
                    <span class="dashicons <?php echo esc_attr( $data['thumbnail'] ); ?>" style="font-size: 48px; width: 48px; height: 48px; color: #fff;"></span>
                    <div class="jj-preset-badge" style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 4px; color: #fff; font-size: 10px; font-weight: 700; text-transform: uppercase;">Premium</div>
                </div>
                <div class="jj-preset-info" style="padding: 20px;">
                    <h3 style="margin: 0 0 8px 0; font-size: 18px; color: #1e293b;"><?php echo esc_html( $data['name'] ); ?></h3>
                    <p style="margin: 0 0 15px 0; font-size: 13px; color: #64748b; line-height: 1.5;"><?php echo esc_html( $data['description'] ); ?></p>
                    <div class="jj-preset-colors" style="display: flex; gap: 5px; margin-bottom: 20px;">
                        <?php foreach ( $data['colors'] as $key => $color ) : if($key == 'background' || $key == 'foreground') continue; ?>
                            <span style="width: 20px; height: 20px; border-radius: 50%; background: <?php echo esc_attr( $color ); ?>; border: 1px solid rgba(0,0,0,0.05);"></span>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="button button-primary jj-import-preset-btn" style="width: 100%; height: 40px; border-radius: 8px; font-weight: 600;">
                        <?php _e( '프리셋 적용하기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.jj-preset-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    border-color: #3b82f6 !important;
}
.jj-preset-card.active {
    border: 2px solid #3b82f6 !important;
}
</style>
