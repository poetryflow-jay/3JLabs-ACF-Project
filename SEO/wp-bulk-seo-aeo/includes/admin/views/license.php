<?php
/**
 * License & Updates Management View
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

$license_key = get_option('wp_bulk_seo_aeo_license_key', '');
$license_status = get_option('wp_bulk_seo_aeo_license_status', 'inactive');
$license_data = get_option('wp_bulk_seo_aeo_license_data', []);
$license_expires = get_option('wp_bulk_seo_aeo_license_expires', '');
?>

<div class="wrap wp-bulk-seo-aeo-license">
    <h1><?php esc_html_e('License & Updates Management', 'wp-bulk-seo-aeo'); ?></h1>

    <div class="license-section">
        <h2><?php esc_html_e('라이센스 관리', 'wp-bulk-seo-aeo'); ?></h2>

        <div class="license-status">
            <p>
                <strong><?php esc_html_e('상태:', 'wp-bulk-seo-aeo'); ?></strong>
                <span class="status-badge status-<?php echo esc_attr($license_status); ?>">
                    <?php
                    $status_labels = [
                        'active' => __('활성화됨', 'wp-bulk-seo-aeo'),
                        'inactive' => __('비활성화됨', 'wp-bulk-seo-aeo'),
                        'expired' => __('만료됨', 'wp-bulk-seo-aeo'),
                    ];
                    echo esc_html($status_labels[$license_status] ?? $license_status);
                    ?>
                </span>
            </p>

            <?php if ($license_expires): ?>
                <p>
                    <strong><?php esc_html_e('만료일:', 'wp-bulk-seo-aeo'); ?></strong>
                    <?php echo esc_html($license_expires); ?>
                </p>
            <?php endif; ?>
        </div>

        <form id="wp-bulk-seo-aeo-license-form">
            <?php wp_nonce_field('wp_rest', 'nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="license_key"><?php esc_html_e('라이센스 키', 'wp-bulk-seo-aeo'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="license_key" name="license_key" value="<?php echo esc_attr($license_key); ?>" class="regular-text" />
                        <p class="description">
                            <?php esc_html_e('라이센스 키를 입력하세요.', 'wp-bulk-seo-aeo'); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <?php if ($license_status === 'active'): ?>
                    <button type="button" class="button" id="wp-bulk-seo-aeo-deactivate-license">
                        <?php esc_html_e('라이센스 비활성화', 'wp-bulk-seo-aeo'); ?>
                    </button>
                    <button type="button" class="button" id="wp-bulk-seo-aeo-check-license">
                        <?php esc_html_e('라이센스 확인', 'wp-bulk-seo-aeo'); ?>
                    </button>
                <?php else: ?>
                    <button type="button" class="button button-primary" id="wp-bulk-seo-aeo-activate-license">
                        <?php esc_html_e('라이센스 활성화', 'wp-bulk-seo-aeo'); ?>
                    </button>
                <?php endif; ?>
            </p>
        </form>
    </div>

    <div class="remote-sites-section">
        <h2><?php esc_html_e('원격 사이트 관리', 'wp-bulk-seo-aeo'); ?></h2>
        <p><?php esc_html_e('비 WordPress 사이트에 대한 원격 SEO 서비스를 관리합니다.', 'wp-bulk-seo-aeo'); ?></p>

        <div id="wp-bulk-seo-aeo-remote-sites-list">
            <?php
            global $wpdb;
            $table = $wpdb->prefix . 'bulk_seo_aeo_remote_sites';
            $sites = $wpdb->get_results("SELECT * FROM $table ORDER BY registered_at DESC", ARRAY_A);

            if (empty($sites)):
            ?>
                <p><?php esc_html_e('등록된 원격 사이트가 없습니다.', 'wp-bulk-seo-aeo'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('사이트명', 'wp-bulk-seo-aeo'); ?></th>
                            <th><?php esc_html_e('URL', 'wp-bulk-seo-aeo'); ?></th>
                            <th><?php esc_html_e('플랫폼', 'wp-bulk-seo-aeo'); ?></th>
                            <th><?php esc_html_e('상태', 'wp-bulk-seo-aeo'); ?></th>
                            <th><?php esc_html_e('등록일', 'wp-bulk-seo-aeo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sites as $site): ?>
                            <tr>
                                <td><?php echo esc_html($site['site_name']); ?></td>
                                <td><?php echo esc_html($site['site_url']); ?></td>
                                <td><?php echo esc_html($site['platform']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo esc_attr($site['status']); ?>">
                                        <?php echo esc_html($site['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html($site['registered_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="installation-code-section">
        <h2><?php esc_html_e('원클릭 설치 코드', 'wp-bulk-seo-aeo'); ?></h2>
        <p><?php esc_html_e('비 WordPress 사이트에 이 코드 한 줄만 추가하면 자동 SEO 최적화가 시작됩니다.', 'wp-bulk-seo-aeo'); ?></p>

        <?php
        $gtm_optimizer = new WP_Bulk_SEO_AEO_GTM_Style_Optimizer();
        $installation_code = $gtm_optimizer->get_installation_code();
        ?>

        <textarea readonly class="large-text code" rows="3" onclick="this.select();"><?php echo esc_textarea($installation_code); ?></textarea>
        <p class="description">
            <?php esc_html_e('이 코드를 웹사이트의 &lt;head&gt; 섹션에 추가하세요.', 'wp-bulk-seo-aeo'); ?>
        </p>
    </div>
</div>

<style>
.wp-bulk-seo-aeo-license {
    max-width: 1200px;
}
.license-section,
.remote-sites-section,
.installation-code-section {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    border-radius: 4px;
}
.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: bold;
}
.status-badge.status-active {
    background: #00a32a;
    color: #fff;
}
.status-badge.status-inactive {
    background: #dba617;
    color: #fff;
}
.status-badge.status-expired {
    background: #d63638;
    color: #fff;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('#wp-bulk-seo-aeo-activate-license').on('click', function() {
        var licenseKey = $('#license_key').val();
        if (!licenseKey) {
            alert('라이센스 키를 입력하세요.');
            return;
        }

        $.ajax({
            url: wpBulkSEOAEO.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wp_bulk_seo_aeo_activate_license',
                license_key: licenseKey,
                nonce: wpBulkSEOAEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('라이센스가 활성화되었습니다.');
                    location.reload();
                } else {
                    alert('오류: ' + (response.data?.message || '알 수 없는 오류'));
                }
            }
        });
    });

    $('#wp-bulk-seo-aeo-deactivate-license').on('click', function() {
        if (!confirm('라이센스를 비활성화하시겠습니까?')) {
            return;
        }

        $.ajax({
            url: wpBulkSEOAEO.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wp_bulk_seo_aeo_deactivate_license',
                nonce: wpBulkSEOAEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('라이센스가 비활성화되었습니다.');
                    location.reload();
                } else {
                    alert('오류: ' + (response.data?.message || '알 수 없는 오류'));
                }
            }
        });
    });

    $('#wp-bulk-seo-aeo-check-license').on('click', function() {
        $.ajax({
            url: wpBulkSEOAEO.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wp_bulk_seo_aeo_check_license',
                nonce: wpBulkSEOAEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('라이센스 상태: ' + response.data.status);
                    location.reload();
                } else {
                    alert('오류: ' + (response.data?.message || '알 수 없는 오류'));
                }
            }
        });
    });
});
</script>
