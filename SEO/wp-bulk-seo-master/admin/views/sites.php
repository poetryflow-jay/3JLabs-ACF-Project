<?php
if (!defined('ABSPATH')) exit;

$site_manager = WP_Bulk_SEO_Remote_Site_Manager::get_instance();
$sites = $site_manager->get_sites();
?>
<div class="wrap seo-master-wrap">
    <h1>
        <?php esc_html_e('Remote Sites', 'wp-bulk-seo-master'); ?>
        <button type="button" class="page-title-action" id="add-site-btn"><?php esc_html_e('Add Site', 'wp-bulk-seo-master'); ?></button>
    </h1>

    <p class="description"><?php esc_html_e('Manage WordPress and non-WordPress sites. For non-WP sites, add the snippet code to their header.', 'wp-bulk-seo-master'); ?></p>

    <div class="sites-grid">
        <?php if (empty($sites)): ?>
        <div class="empty-state">
            <span class="dashicons dashicons-admin-multisite"></span>
            <p><?php esc_html_e('No sites added yet', 'wp-bulk-seo-master'); ?></p>
            <button type="button" class="button button-primary" id="add-first-site"><?php esc_html_e('Add Your First Site', 'wp-bulk-seo-master'); ?></button>
        </div>
        <?php else: ?>
        <?php foreach ($sites as $site): ?>
        <div class="site-card" data-id="<?php echo esc_attr($site->id); ?>">
            <div class="site-header">
                <span class="site-type-badge <?php echo esc_attr($site->site_type); ?>"><?php echo esc_html(ucfirst($site->site_type)); ?></span>
                <span class="site-status status-<?php echo esc_attr($site->status); ?>"><?php echo esc_html(ucfirst($site->status)); ?></span>
            </div>
            <div class="site-body">
                <h3><?php echo esc_html($site->site_name ?: parse_url($site->site_url, PHP_URL_HOST)); ?></h3>
                <a href="<?php echo esc_url($site->site_url); ?>" target="_blank" class="site-url"><?php echo esc_html($site->site_url); ?></a>
                
                <div class="site-score">
                    <?php if ($site->seo_score): ?>
                    <div class="score-circle <?php echo $site->seo_score >= 80 ? 'good' : ($site->seo_score >= 60 ? 'ok' : 'poor'); ?>">
                        <?php echo esc_html($site->seo_score); ?>
                    </div>
                    <?php else: ?>
                    <div class="score-circle pending">?</div>
                    <?php endif; ?>
                    <span class="score-label"><?php esc_html_e('SEO Score', 'wp-bulk-seo-master'); ?></span>
                </div>

                <?php if ($site->last_crawl): ?>
                <p class="last-crawl"><?php esc_html_e('Last crawl:', 'wp-bulk-seo-master'); ?> <?php echo esc_html(human_time_diff(strtotime($site->last_crawl))); ?> ago</p>
                <?php endif; ?>
            </div>
            <div class="site-actions">
                <button type="button" class="button crawl-site" data-id="<?php echo esc_attr($site->id); ?>">
                    <span class="dashicons dashicons-update"></span> <?php esc_html_e('Crawl', 'wp-bulk-seo-master'); ?>
                </button>
                <button type="button" class="button view-snippet" data-key="<?php echo esc_attr($site->snippet_key); ?>">
                    <span class="dashicons dashicons-editor-code"></span> <?php esc_html_e('Snippet', 'wp-bulk-seo-master'); ?>
                </button>
                <button type="button" class="button manage-seo" data-id="<?php echo esc_attr($site->id); ?>">
                    <span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e('Manage', 'wp-bulk-seo-master'); ?>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Add Site Modal -->
<div id="add-site-modal" class="seo-master-modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2><?php esc_html_e('Add Remote Site', 'wp-bulk-seo-master'); ?></h2>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="add-site-form">
                <p>
                    <label><?php esc_html_e('Site URL', 'wp-bulk-seo-master'); ?> *</label>
                    <input type="url" name="url" required class="regular-text" placeholder="https://example.com">
                </p>
                <p>
                    <label><?php esc_html_e('Site Name', 'wp-bulk-seo-master'); ?></label>
                    <input type="text" name="name" class="regular-text">
                </p>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="button modal-cancel"><?php esc_html_e('Cancel', 'wp-bulk-seo-master'); ?></button>
            <button type="button" class="button button-primary" id="submit-site"><?php esc_html_e('Add Site', 'wp-bulk-seo-master'); ?></button>
        </div>
    </div>
</div>

<!-- Snippet Modal -->
<div id="snippet-modal" class="seo-master-modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content modal-wide">
        <div class="modal-header">
            <h2><?php esc_html_e('Installation Snippet', 'wp-bulk-seo-master'); ?></h2>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p><?php esc_html_e('Add this code to your site\'s <head> section:', 'wp-bulk-seo-master'); ?></p>
            <textarea id="snippet-code" readonly rows="8" class="code"></textarea>
            <button type="button" class="button" id="copy-snippet"><?php esc_html_e('Copy to Clipboard', 'wp-bulk-seo-master'); ?></button>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Open add modal
    $('#add-site-btn, #add-first-site').on('click', function() {
        $('#add-site-modal').show();
    });

    // Close modals
    $('.modal-close, .modal-cancel, .modal-overlay').on('click', function() {
        $('.seo-master-modal').hide();
    });

    // Submit site
    $('#submit-site').on('click', function() {
        var $btn = $(this);
        var $form = $('#add-site-form');
        
        $btn.prop('disabled', true);

        $.post(seoMaster.ajaxUrl, {
            action: 'add_remote_site',
            nonce: seoMaster.nonce,
            url: $form.find('[name="url"]').val(),
            name: $form.find('[name="name"]').val()
        }).done(function(response) {
            if (response.success) {
                $('#snippet-code').val(response.data.snippet_code);
                $('#add-site-modal').hide();
                $('#snippet-modal').show();
            } else {
                alert(response.data.message);
            }
        }).always(function() {
            $btn.prop('disabled', false);
        });
    });

    // View snippet
    $('.view-snippet').on('click', function() {
        var key = $(this).data('key');
        var snippet = '<!-- 3J SEO Optimizer -->\n<script>\n(function(){var s=document.createElement("script");s.src="<?php echo esc_js(home_url('/wp-content/plugins/wp-bulk-seo-master/assets/js/seo-snippet.min.js')); ?>";s.setAttribute("data-key","' + key + '");s.async=true;document.head.appendChild(s);})();\n<\/script>';
        $('#snippet-code').val(snippet);
        $('#snippet-modal').show();
    });

    // Copy snippet
    $('#copy-snippet').on('click', function() {
        $('#snippet-code').select();
        document.execCommand('copy');
        $(this).text('<?php esc_html_e('Copied!', 'wp-bulk-seo-master'); ?>');
        setTimeout(function() {
            $('#copy-snippet').text('<?php esc_html_e('Copy to Clipboard', 'wp-bulk-seo-master'); ?>');
        }, 2000);
    });

    // Crawl site
    $('.crawl-site').on('click', function() {
        var $btn = $(this);
        var siteId = $btn.data('id');
        
        $btn.prop('disabled', true).find('.dashicons').addClass('spin');

        $.post(seoMaster.ajaxUrl, {
            action: 'crawl_remote_site',
            nonce: seoMaster.nonce,
            site_id: siteId
        }).done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data.message);
            }
        }).always(function() {
            $btn.prop('disabled', false).find('.dashicons').removeClass('spin');
        });
    });
});
</script>
