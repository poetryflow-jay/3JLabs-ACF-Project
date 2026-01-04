<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$settings = get_option( 'jj_marketing_dashboard_settings', array() );
?>
<div class="wrap jj-marketing-settings">
    <h1><?php esc_html_e( '⚙️ 설정', 'jj-marketing-dashboard' ); ?></h1>
    <p class="description">
        <?php esc_html_e( '마케팅 대시보드 설정을 관리합니다.', 'jj-marketing-dashboard' ); ?>
    </p>

    <form method="post" action="" id="jj-marketing-settings-form">
        <?php wp_nonce_field( 'jj_marketing_dashboard_nonce', 'jj_marketing_dashboard_nonce' ); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="google_analytics_enabled"><?php esc_html_e( 'Google Analytics 활성화', 'jj-marketing-dashboard' ); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="settings[google_analytics_enabled]" id="google_analytics_enabled" value="1" <?php checked( ! empty( $settings['google_analytics_enabled'] ) ); ?>>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="google_analytics_id"><?php esc_html_e( 'Google Analytics ID', 'jj-marketing-dashboard' ); ?></label>
                </th>
                <td>
                    <input type="text" name="settings[google_analytics_id]" id="google_analytics_id" value="<?php echo esc_attr( $settings['google_analytics_id'] ?? '' ); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="google_search_console_enabled"><?php esc_html_e( 'Google Search Console 활성화', 'jj-marketing-dashboard' ); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="settings[google_search_console_enabled]" id="google_search_console_enabled" value="1" <?php checked( ! empty( $settings['google_search_console_enabled'] ) ); ?>>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="button" class="button button-primary" onclick="jj_marketing_save_settings()">
                <?php esc_html_e( '설정 저장', 'jj-marketing-dashboard' ); ?>
            </button>
        </p>
    </form>
</div>

<script>
function jj_marketing_save_settings() {
    const formData = jQuery('#jj-marketing-settings-form').serialize();
    
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'jj_marketing_save_settings',
            nonce: '<?php echo wp_create_nonce( 'jj_marketing_dashboard_nonce' ); ?>',
            settings: jQuery('#jj-marketing-settings-form').serializeArray()
        },
        success: function(response) {
            if (response.success) {
                alert('<?php esc_html_e( '설정이 저장되었습니다.', 'jj-marketing-dashboard' ); ?>');
            }
        }
    });
}
</script>
