<?php
/**
 * ACF Code Snippets Box - 프리셋 라이브러리 페이지
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$css_presets = ACF_CSB_Presets::get_css_presets();
$js_presets = ACF_CSB_Presets::get_js_presets();
$php_presets = ACF_CSB_Presets::get_php_presets();
?>
<div class="wrap acf-csb-presets-page">
    <h1>
        <span class="dashicons dashicons-welcome-widgets-menus" style="font-size: 30px; width: 30px; height: 30px; margin-right: 10px;"></span>
        <?php esc_html_e( '프리셋 라이브러리', 'acf-code-snippets-box' ); ?>
    </h1>
    <p class="description">
        <?php esc_html_e( '자주 사용되는 유용한 코드 스니펫을 원클릭으로 추가하세요.', 'acf-code-snippets-box' ); ?>
    </p>

    <!-- 탭 네비게이션 -->
    <nav class="nav-tab-wrapper" style="margin-top: 20px;">
        <a href="#css-presets" class="nav-tab nav-tab-active" data-tab="css"><?php esc_html_e( 'CSS 프리셋', 'acf-code-snippets-box' ); ?></a>
        <a href="#js-presets" class="nav-tab" data-tab="js"><?php esc_html_e( 'JavaScript 프리셋', 'acf-code-snippets-box' ); ?></a>
        <a href="#php-presets" class="nav-tab" data-tab="php"><?php esc_html_e( 'PHP 프리셋', 'acf-code-snippets-box' ); ?></a>
    </nav>

    <!-- CSS 프리셋 -->
    <div id="css-presets" class="acf-csb-preset-tab" style="margin-top: 20px;">
        <div class="acf-csb-preset-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php foreach ( $css_presets as $id => $preset ) : ?>
            <div class="acf-csb-preset-card" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); color: #fff; padding: 15px 20px;">
                    <h3 style="margin: 0; font-size: 16px;"><?php echo esc_html( $preset['name'] ); ?></h3>
                </div>
                <div style="padding: 20px;">
                    <p style="color: #646970; margin: 0 0 15px;"><?php echo esc_html( $preset['description'] ); ?></p>
                    <pre style="background: #f6f7f7; padding: 15px; border-radius: 6px; font-size: 12px; overflow-x: auto; max-height: 150px;"><code><?php echo esc_html( $preset['code'] ); ?></code></pre>
                    <div style="margin-top: 15px; display: flex; gap: 10px;">
                        <button type="button" class="button button-primary acf-csb-use-preset" data-type="css" data-id="<?php echo esc_attr( $id ); ?>">
                            <?php esc_html_e( '스니펫으로 추가', 'acf-code-snippets-box' ); ?>
                        </button>
                        <button type="button" class="button acf-csb-copy-code" data-code="<?php echo esc_attr( $preset['code'] ); ?>">
                            <?php esc_html_e( '코드 복사', 'acf-code-snippets-box' ); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- JavaScript 프리셋 -->
    <div id="js-presets" class="acf-csb-preset-tab" style="margin-top: 20px; display: none;">
        <div class="acf-csb-preset-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php foreach ( $js_presets as $id => $preset ) : ?>
            <div class="acf-csb-preset-card" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="background: linear-gradient(135deg, #f0ad4e 0%, #ffc107 100%); color: #fff; padding: 15px 20px;">
                    <h3 style="margin: 0; font-size: 16px;"><?php echo esc_html( $preset['name'] ); ?></h3>
                </div>
                <div style="padding: 20px;">
                    <p style="color: #646970; margin: 0 0 15px;"><?php echo esc_html( $preset['description'] ); ?></p>
                    <pre style="background: #f6f7f7; padding: 15px; border-radius: 6px; font-size: 12px; overflow-x: auto; max-height: 150px;"><code><?php echo esc_html( $preset['code'] ); ?></code></pre>
                    <div style="margin-top: 15px; display: flex; gap: 10px;">
                        <button type="button" class="button button-primary acf-csb-use-preset" data-type="js" data-id="<?php echo esc_attr( $id ); ?>">
                            <?php esc_html_e( '스니펫으로 추가', 'acf-code-snippets-box' ); ?>
                        </button>
                        <button type="button" class="button acf-csb-copy-code" data-code="<?php echo esc_attr( $preset['code'] ); ?>">
                            <?php esc_html_e( '코드 복사', 'acf-code-snippets-box' ); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- PHP 프리셋 -->
    <div id="php-presets" class="acf-csb-preset-tab" style="margin-top: 20px; display: none;">
        <div class="notice notice-warning" style="margin-bottom: 20px;">
            <p>
                <strong><?php esc_html_e( '⚠️ 주의:', 'acf-code-snippets-box' ); ?></strong>
                <?php esc_html_e( 'PHP 코드를 실행하려면 설정에서 "PHP 코드 실행"을 활성화해야 합니다.', 'acf-code-snippets-box' ); ?>
            </p>
        </div>
        <div class="acf-csb-preset-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php foreach ( $php_presets as $id => $preset ) : ?>
            <div class="acf-csb-preset-card" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="background: linear-gradient(135deg, #8892bf 0%, #a29bfe 100%); color: #fff; padding: 15px 20px;">
                    <h3 style="margin: 0; font-size: 16px;"><?php echo esc_html( $preset['name'] ); ?></h3>
                    <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 10px;">
                        <?php echo esc_html( $preset['category'] ); ?>
                    </span>
                </div>
                <div style="padding: 20px;">
                    <p style="color: #646970; margin: 0 0 15px;"><?php echo esc_html( $preset['description'] ); ?></p>
                    <pre style="background: #f6f7f7; padding: 15px; border-radius: 6px; font-size: 12px; overflow-x: auto; max-height: 150px;"><code><?php echo esc_html( $preset['code'] ); ?></code></pre>
                    <div style="margin-top: 15px; display: flex; gap: 10px;">
                        <button type="button" class="button button-primary acf-csb-use-preset" data-type="php" data-id="<?php echo esc_attr( $id ); ?>">
                            <?php esc_html_e( '스니펫으로 추가', 'acf-code-snippets-box' ); ?>
                        </button>
                        <button type="button" class="button acf-csb-copy-code" data-code="<?php echo esc_attr( $preset['code'] ); ?>">
                            <?php esc_html_e( '코드 복사', 'acf-code-snippets-box' ); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // 탭 전환
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        $('.acf-csb-preset-tab').hide();
        $('#' + $(this).data('tab') + '-presets').show();
    });

    // 코드 복사
    $('.acf-csb-copy-code').on('click', function() {
        const code = $(this).data('code');
        navigator.clipboard.writeText(code).then(() => {
            const originalText = $(this).text();
            $(this).text('<?php echo esc_js( __( '복사됨!', 'acf-code-snippets-box' ) ); ?>');
            setTimeout(() => $(this).text(originalText), 2000);
        });
    });

    // 프리셋 사용
    $('.acf-csb-use-preset').on('click', function() {
        const type = $(this).data('type');
        const id = $(this).data('id');
        
        // 새 스니펫 추가 페이지로 이동 (프리셋 ID 전달)
        window.location.href = '<?php echo esc_url( admin_url( 'post-new.php?post_type=acf_code_snippet' ) ); ?>&preset_type=' + type + '&preset_id=' + id;
    });
});
</script>
