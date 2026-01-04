<?php
if (!defined('ABSPATH')) exit;

$license_manager = OneClick_SEO_License_Manager::get_instance();
$licenses_data = $license_manager->get_licenses([
    'page' => isset($_GET['paged']) ? intval($_GET['paged']) : 1,
    'search' => isset($_GET['s']) ? sanitize_text_field($_GET['s']) : ''
]);
?>
<div class="wrap seo-master-wrap">
    <h1>
        <?php esc_html_e('License Management', 'oneclick-seo-master'); ?>
        <button type="button" class="page-title-action" id="generate-license-btn">
            <?php esc_html_e('Generate New License', 'oneclick-seo-master'); ?>
        </button>
    </h1>

    <!-- Search -->
    <form method="get" class="search-form">
        <input type="hidden" name="page" value="seo-master-licenses">
        <p class="search-box">
            <input type="search" name="s" value="<?php echo esc_attr($_GET['s'] ?? ''); ?>" placeholder="<?php esc_attr_e('Search licenses...', 'oneclick-seo-master'); ?>">
            <button type="submit" class="button"><?php esc_html_e('Search', 'oneclick-seo-master'); ?></button>
        </p>
    </form>

    <!-- Licenses Table -->
    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th><?php esc_html_e('License Key', 'oneclick-seo-master'); ?></th>
                <th><?php esc_html_e('Type', 'oneclick-seo-master'); ?></th>
                <th><?php esc_html_e('Customer', 'oneclick-seo-master'); ?></th>
                <th><?php esc_html_e('Sites', 'oneclick-seo-master'); ?></th>
                <th><?php esc_html_e('Status', 'oneclick-seo-master'); ?></th>
                <th><?php esc_html_e('Expires', 'oneclick-seo-master'); ?></th>
                <th><?php esc_html_e('Actions', 'oneclick-seo-master'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($licenses_data['licenses'])): ?>
            <tr><td colspan="7"><?php esc_html_e('No licenses found', 'oneclick-seo-master'); ?></td></tr>
            <?php else: ?>
            <?php foreach ($licenses_data['licenses'] as $license): ?>
            <tr data-id="<?php echo esc_attr($license->id); ?>">
                <td><code><?php echo esc_html($license->license_key); ?></code></td>
                <td><span class="license-type license-<?php echo esc_attr($license->license_type); ?>"><?php echo esc_html(ucfirst($license->license_type)); ?></span></td>
                <td>
                    <strong><?php echo esc_html($license->customer_name); ?></strong><br>
                    <small><?php echo esc_html($license->customer_email); ?></small>
                </td>
                <td><?php echo esc_html($license->active_sites); ?>/<?php echo $license->max_sites < 0 ? '∞' : esc_html($license->max_sites); ?></td>
                <td><span class="status-<?php echo esc_attr($license->status); ?>"><?php echo esc_html(ucfirst($license->status)); ?></span></td>
                <td><?php echo $license->expires_at ? esc_html(date('Y-m-d', strtotime($license->expires_at))) : '∞'; ?></td>
                <td>
                    <button type="button" class="button button-small view-activations" data-id="<?php echo esc_attr($license->id); ?>"><?php esc_html_e('View Sites', 'oneclick-seo-master'); ?></button>
                    <?php if ($license->status === 'active'): ?>
                    <button type="button" class="button button-small revoke-license" data-id="<?php echo esc_attr($license->id); ?>"><?php esc_html_e('Revoke', 'oneclick-seo-master'); ?></button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Generate License Modal -->
<div id="generate-license-modal" class="seo-master-modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2><?php esc_html_e('Generate New License', 'oneclick-seo-master'); ?></h2>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="generate-license-form">
                <p>
                    <label><?php esc_html_e('Customer Email', 'oneclick-seo-master'); ?> *</label>
                    <input type="email" name="email" required class="regular-text">
                </p>
                <p>
                    <label><?php esc_html_e('Customer Name', 'oneclick-seo-master'); ?></label>
                    <input type="text" name="name" class="regular-text">
                </p>
                <p>
                    <label><?php esc_html_e('License Type', 'oneclick-seo-master'); ?></label>
                    <select name="type">
                        <option value="starter">Starter (1 site)</option>
                        <option value="professional">Professional (5 sites)</option>
                        <option value="agency">Agency (25 sites)</option>
                        <option value="enterprise">Enterprise (Unlimited)</option>
                    </select>
                </p>
                <p>
                    <label><?php esc_html_e('Expires At', 'oneclick-seo-master'); ?></label>
                    <input type="date" name="expires_at">
                    <small><?php esc_html_e('Leave empty for lifetime license', 'oneclick-seo-master'); ?></small>
                </p>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="button modal-cancel"><?php esc_html_e('Cancel', 'oneclick-seo-master'); ?></button>
            <button type="button" class="button button-primary" id="submit-license"><?php esc_html_e('Generate License', 'oneclick-seo-master'); ?></button>
        </div>
    </div>
</div>

<!-- Activations Modal -->
<div id="activations-modal" class="seo-master-modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2><?php esc_html_e('License Activations', 'oneclick-seo-master'); ?></h2>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <table class="wp-list-table widefat">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Site', 'oneclick-seo-master'); ?></th>
                        <th><?php esc_html_e('Type', 'oneclick-seo-master'); ?></th>
                        <th><?php esc_html_e('Status', 'oneclick-seo-master'); ?></th>
                        <th><?php esc_html_e('Activated', 'oneclick-seo-master'); ?></th>
                    </tr>
                </thead>
                <tbody id="activations-list"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Open generate modal
    $('#generate-license-btn').on('click', function() {
        $('#generate-license-modal').show();
    });

    // Close modals
    $('.modal-close, .modal-cancel, .modal-overlay').on('click', function() {
        $('.seo-master-modal').hide();
    });

    // Submit license
    $('#submit-license').on('click', function() {
        var $btn = $(this);
        var $form = $('#generate-license-form');
        
        $btn.prop('disabled', true).text('<?php esc_html_e('Generating...', 'oneclick-seo-master'); ?>');

        $.post(seoMaster.ajaxUrl, {
            action: 'generate_license',
            nonce: seoMaster.nonce,
            email: $form.find('[name="email"]').val(),
            name: $form.find('[name="name"]').val(),
            type: $form.find('[name="type"]').val(),
            expires_at: $form.find('[name="expires_at"]').val()
        }).done(function(response) {
            if (response.success) {
                alert('<?php esc_html_e('License generated:', 'oneclick-seo-master'); ?> ' + response.data.license_key);
                location.reload();
            } else {
                alert(response.data.message || '<?php esc_html_e('Error', 'oneclick-seo-master'); ?>');
            }
        }).always(function() {
            $btn.prop('disabled', false).text('<?php esc_html_e('Generate License', 'oneclick-seo-master'); ?>');
        });
    });

    // View activations
    $('.view-activations').on('click', function() {
        var licenseId = $(this).data('id');
        
        $.post(seoMaster.ajaxUrl, {
            action: 'get_license_activations',
            nonce: seoMaster.nonce,
            license_id: licenseId
        }).done(function(response) {
            if (response.success) {
                var html = '';
                response.data.forEach(function(a) {
                    html += '<tr>' +
                        '<td>' + a.site_url + '</td>' +
                        '<td>' + a.site_type + '</td>' +
                        '<td><span class="status-' + a.status + '">' + a.status + '</span></td>' +
                        '<td>' + a.activated_at + '</td>' +
                    '</tr>';
                });
                $('#activations-list').html(html || '<tr><td colspan="4"><?php esc_html_e('No activations', 'oneclick-seo-master'); ?></td></tr>');
                $('#activations-modal').show();
            }
        });
    });

    // Revoke license
    $('.revoke-license').on('click', function() {
        if (!confirm('<?php esc_html_e('Are you sure you want to revoke this license?', 'oneclick-seo-master'); ?>')) return;
        
        var $btn = $(this);
        var licenseId = $btn.data('id');

        $.post(seoMaster.ajaxUrl, {
            action: 'revoke_license',
            nonce: seoMaster.nonce,
            license_id: licenseId
        }).done(function(response) {
            if (response.success) {
                location.reload();
            }
        });
    });
});
</script>
