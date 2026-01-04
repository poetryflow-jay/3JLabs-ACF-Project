<?php
/**
 * Settings View
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['wp_bulk_seo_aeo_settings_submit']) && check_admin_referer('wp_bulk_seo_aeo_settings')) {
    // PageSpeed Insights API Key
    if (isset($_POST['pagespeed_api_key'])) {
        update_option('wp_bulk_seo_aeo_pagespeed_api_key', sanitize_text_field($_POST['pagespeed_api_key']));
    }

    // Google Search Console
    if (isset($_POST['gsc_token'])) {
        update_option('wp_bulk_seo_aeo_gsc_token', sanitize_text_field($_POST['gsc_token']));
    }
    if (isset($_POST['gsc_site_url'])) {
        update_option('wp_bulk_seo_aeo_gsc_site_url', esc_url_raw($_POST['gsc_site_url']));
    }

    // Google Analytics
    if (isset($_POST['ga_token'])) {
        update_option('wp_bulk_seo_aeo_ga_token', sanitize_text_field($_POST['ga_token']));
    }
    if (isset($_POST['ga_view_id'])) {
        update_option('wp_bulk_seo_aeo_ga_view_id', sanitize_text_field($_POST['ga_view_id']));
    }

    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('설정이 저장되었습니다.', 'wp-bulk-seo-aeo') . '</p></div>';
}

$pagespeed_key = get_option('wp_bulk_seo_aeo_pagespeed_api_key', '');
$gsc_token = get_option('wp_bulk_seo_aeo_gsc_token', '');
$gsc_site_url = get_option('wp_bulk_seo_aeo_gsc_site_url', '');
$ga_token = get_option('wp_bulk_seo_aeo_ga_token', '');
$ga_view_id = get_option('wp_bulk_seo_aeo_ga_view_id', '');
?>

<div class="wrap">
    <h1><?php esc_html_e('WP Bulk SEO & AEO Settings', 'wp-bulk-seo-aeo'); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('wp_bulk_seo_aeo_settings'); ?>

        <h2><?php esc_html_e('API 설정', 'wp-bulk-seo-aeo'); ?></h2>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="pagespeed_api_key"><?php esc_html_e('PageSpeed Insights API Key', 'wp-bulk-seo-aeo'); ?></label>
                </th>
                <td>
                    <input type="text" id="pagespeed_api_key" name="pagespeed_api_key" value="<?php echo esc_attr($pagespeed_key); ?>" class="regular-text" />
                    <p class="description">
                        <?php esc_html_e('Google Cloud Console에서 PageSpeed Insights API 키를 발급받으세요.', 'wp-bulk-seo-aeo'); ?>
                        <a href="https://console.cloud.google.com/apis/credentials" target="_blank"><?php esc_html_e('API 키 발급', 'wp-bulk-seo-aeo'); ?></a>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="gsc_token"><?php esc_html_e('Google Search Console OAuth Token', 'wp-bulk-seo-aeo'); ?></label>
                </th>
                <td>
                    <input type="text" id="gsc_token" name="gsc_token" value="<?php echo esc_attr($gsc_token); ?>" class="regular-text" />
                    <p class="description">
                        <?php esc_html_e('Google Search Console API 접근 토큰을 입력하세요.', 'wp-bulk-seo-aeo'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="gsc_site_url"><?php esc_html_e('Google Search Console Site URL', 'wp-bulk-seo-aeo'); ?></label>
                </th>
                <td>
                    <input type="url" id="gsc_site_url" name="gsc_site_url" value="<?php echo esc_attr($gsc_site_url); ?>" class="regular-text" placeholder="https://example.com/" />
                    <p class="description">
                        <?php esc_html_e('Search Console에 등록된 사이트 URL을 입력하세요.', 'wp-bulk-seo-aeo'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="ga_token"><?php esc_html_e('Google Analytics OAuth Token', 'wp-bulk-seo-aeo'); ?></label>
                </th>
                <td>
                    <input type="text" id="ga_token" name="ga_token" value="<?php echo esc_attr($ga_token); ?>" class="regular-text" />
                    <p class="description">
                        <?php esc_html_e('Google Analytics Reporting API 접근 토큰을 입력하세요.', 'wp-bulk-seo-aeo'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="ga_view_id"><?php esc_html_e('Google Analytics View ID', 'wp-bulk-seo-aeo'); ?></label>
                </th>
                <td>
                    <input type="text" id="ga_view_id" name="ga_view_id" value="<?php echo esc_attr($ga_view_id); ?>" class="regular-text" />
                    <p class="description">
                        <?php esc_html_e('Google Analytics View ID를 입력하세요. (ga:123456789 형식)', 'wp-bulk-seo-aeo'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button(__('설정 저장', 'wp-bulk-seo-aeo'), 'primary', 'wp_bulk_seo_aeo_settings_submit'); ?>
    </form>
</div>
