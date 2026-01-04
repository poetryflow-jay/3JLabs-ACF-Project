<?php
if (!defined('ABSPATH')) exit;

$settings = get_option('wp_bulk_seo_master_settings', []);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('seo_master_settings')) {
    $settings = [
        'rank_check_frequency' => intval($_POST['rank_check_frequency']),
        'default_country' => sanitize_text_field($_POST['default_country']),
        'api_rate_limit' => intval($_POST['api_rate_limit']),
        'enable_email_reports' => isset($_POST['enable_email_reports']),
        'report_email' => sanitize_email($_POST['report_email']),
        'snippet_cache_ttl' => intval($_POST['snippet_cache_ttl'])
    ];
    update_option('wp_bulk_seo_master_settings', $settings);
    echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved.', 'wp-bulk-seo-master') . '</p></div>';
}
?>
<div class="wrap seo-master-wrap">
    <h1><?php esc_html_e('Settings', 'wp-bulk-seo-master'); ?></h1>

    <form method="post">
        <?php wp_nonce_field('seo_master_settings'); ?>

        <table class="form-table">
            <tr>
                <th><label for="rank_check_frequency"><?php esc_html_e('Rank Check Frequency', 'wp-bulk-seo-master'); ?></label></th>
                <td>
                    <select name="rank_check_frequency" id="rank_check_frequency">
                        <option value="2" <?php selected($settings['rank_check_frequency'] ?? 2, 2); ?>><?php esc_html_e('2x per day', 'wp-bulk-seo-master'); ?></option>
                        <option value="4" <?php selected($settings['rank_check_frequency'] ?? 2, 4); ?>><?php esc_html_e('4x per day', 'wp-bulk-seo-master'); ?></option>
                        <option value="6" <?php selected($settings['rank_check_frequency'] ?? 2, 6); ?>><?php esc_html_e('6x per day', 'wp-bulk-seo-master'); ?></option>
                        <option value="12" <?php selected($settings['rank_check_frequency'] ?? 2, 12); ?>><?php esc_html_e('12x per day', 'wp-bulk-seo-master'); ?></option>
                    </select>
                    <p class="description"><?php esc_html_e('How often to check keyword rankings', 'wp-bulk-seo-master'); ?></p>
                </td>
            </tr>

            <tr>
                <th><label for="default_country"><?php esc_html_e('Default Search Country', 'wp-bulk-seo-master'); ?></label></th>
                <td>
                    <select name="default_country" id="default_country">
                        <option value="us" <?php selected($settings['default_country'] ?? 'us', 'us'); ?>>United States</option>
                        <option value="kr" <?php selected($settings['default_country'] ?? 'us', 'kr'); ?>>South Korea</option>
                        <option value="jp" <?php selected($settings['default_country'] ?? 'us', 'jp'); ?>>Japan</option>
                        <option value="gb" <?php selected($settings['default_country'] ?? 'us', 'gb'); ?>>United Kingdom</option>
                        <option value="de" <?php selected($settings['default_country'] ?? 'us', 'de'); ?>>Germany</option>
                        <option value="fr" <?php selected($settings['default_country'] ?? 'us', 'fr'); ?>>France</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label for="api_rate_limit"><?php esc_html_e('API Rate Limit', 'wp-bulk-seo-master'); ?></label></th>
                <td>
                    <input type="number" name="api_rate_limit" id="api_rate_limit" value="<?php echo esc_attr($settings['api_rate_limit'] ?? 60); ?>" min="10" max="600">
                    <p class="description"><?php esc_html_e('Maximum API requests per minute', 'wp-bulk-seo-master'); ?></p>
                </td>
            </tr>

            <tr>
                <th><label for="snippet_cache_ttl"><?php esc_html_e('Snippet Cache TTL', 'wp-bulk-seo-master'); ?></label></th>
                <td>
                    <input type="number" name="snippet_cache_ttl" id="snippet_cache_ttl" value="<?php echo esc_attr($settings['snippet_cache_ttl'] ?? 3600); ?>" min="300" max="86400">
                    <p class="description"><?php esc_html_e('How long snippet config is cached (seconds)', 'wp-bulk-seo-master'); ?></p>
                </td>
            </tr>

            <tr>
                <th><?php esc_html_e('Email Reports', 'wp-bulk-seo-master'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="enable_email_reports" <?php checked($settings['enable_email_reports'] ?? false); ?>>
                        <?php esc_html_e('Send weekly rank reports', 'wp-bulk-seo-master'); ?>
                    </label>
                </td>
            </tr>

            <tr>
                <th><label for="report_email"><?php esc_html_e('Report Email', 'wp-bulk-seo-master'); ?></label></th>
                <td>
                    <input type="email" name="report_email" id="report_email" value="<?php echo esc_attr($settings['report_email'] ?? get_option('admin_email')); ?>" class="regular-text">
                </td>
            </tr>
        </table>

        <h2><?php esc_html_e('API Endpoints', 'wp-bulk-seo-master'); ?></h2>
        <table class="form-table">
            <tr>
                <th><?php esc_html_e('License Validation', 'wp-bulk-seo-master'); ?></th>
                <td><code><?php echo esc_html(rest_url('seo-master/v1/license/validate')); ?></code></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Snippet Config', 'wp-bulk-seo-master'); ?></th>
                <td><code><?php echo esc_html(rest_url('seo-master/v1/snippet/config')); ?></code></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Rank Sync', 'wp-bulk-seo-master'); ?></th>
                <td><code><?php echo esc_html(rest_url('seo-master/v1/rankings/sync')); ?></code></td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
