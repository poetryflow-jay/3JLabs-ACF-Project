<?php
/**
 * WP Bulk SEO - Settings View
 *
 * @package OneClick_SEO_Pro
 * @subpackage Admin/Views
 */

if (!defined('ABSPATH')) {
    exit;
}

$tabs = [
    'general' => __('General', 'oneclick-seo-pro'),
    'api' => __('API Keys', 'oneclick-seo-pro'),
    'sitemap' => __('Sitemap', 'oneclick-seo-pro'),
    'schema' => __('Schema', 'oneclick-seo-pro'),
    'aeo' => __('AEO', 'oneclick-seo-pro'),
];
?>
<div class="wrap oneclick-seo-pro-settings">
    <h1><?php esc_html_e('WP Bulk SEO Settings', 'oneclick-seo-pro'); ?></h1>

    <nav class="nav-tab-wrapper">
        <?php foreach ($tabs as $tab_id => $tab_name): ?>
        <a href="<?php echo esc_url(add_query_arg('tab', $tab_id)); ?>"
           class="nav-tab <?php echo $active_tab === $tab_id ? 'nav-tab-active' : ''; ?>">
            <?php echo esc_html($tab_name); ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <div class="settings-content">
        <?php
        switch ($active_tab):
            case 'api':
                ?>
                <form method="post" action="options.php">
                    <?php settings_fields('oneclick_seo_pro_api'); ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="pagespeed_api_key"><?php esc_html_e('PageSpeed API Key', 'oneclick-seo-pro'); ?></label>
                            </th>
                            <td>
                                <input type="password" id="pagespeed_api_key" name="oneclick_seo_pro_pagespeed_api_key"
                                       value="<?php echo esc_attr(get_option('oneclick_seo_pro_pagespeed_api_key', '')); ?>"
                                       class="regular-text">
                                <p class="description">
                                    <?php printf(
                                        __('Get your API key from %s', 'oneclick-seo-pro'),
                                        '<a href="https://developers.google.com/speed/docs/insights/v5/get-started" target="_blank">Google PageSpeed API</a>'
                                    ); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="openai_api_key"><?php esc_html_e('OpenAI API Key', 'oneclick-seo-pro'); ?></label>
                            </th>
                            <td>
                                <input type="password" id="openai_api_key" name="oneclick_seo_pro_openai_api_key"
                                       value="<?php echo esc_attr(get_option('oneclick_seo_pro_openai_api_key', '')); ?>"
                                       class="regular-text">
                                <p class="description">
                                    <?php printf(
                                        __('Get your API key from %s', 'oneclick-seo-pro'),
                                        '<a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a>'
                                    ); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="anthropic_api_key"><?php esc_html_e('Anthropic API Key', 'oneclick-seo-pro'); ?></label>
                            </th>
                            <td>
                                <input type="password" id="anthropic_api_key" name="oneclick_seo_pro_anthropic_api_key"
                                       value="<?php echo esc_attr(get_option('oneclick_seo_pro_anthropic_api_key', '')); ?>"
                                       class="regular-text">
                                <p class="description">
                                    <?php printf(
                                        __('Get your API key from %s', 'oneclick-seo-pro'),
                                        '<a href="https://console.anthropic.com/" target="_blank">Anthropic Console</a>'
                                    ); ?>
                                </p>
                            </td>
                        </tr>
                    </table>

                    <?php submit_button(); ?>
                </form>
                <?php
                break;

            case 'sitemap':
                $sitemap_settings = get_option('oneclick_seo_pro_sitemap', []);
                ?>
                <form method="post" action="options.php">
                    <?php settings_fields('oneclick_seo_pro_sitemap'); ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Enable Sitemap', 'oneclick-seo-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="oneclick_seo_pro_sitemap[enabled]" value="1"
                                           <?php checked(!empty($sitemap_settings['enabled'])); ?>>
                                    <?php esc_html_e('Generate XML sitemap', 'oneclick-seo-pro'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Post Types', 'oneclick-seo-pro'); ?></th>
                            <td>
                                <?php
                                $post_types = get_post_types(['public' => true], 'objects');
                                $selected_types = $sitemap_settings['post_types'] ?? ['post', 'page'];
                                foreach ($post_types as $type):
                                    if ($type->name === 'attachment') continue;
                                ?>
                                <label style="display: block; margin-bottom: 5px;">
                                    <input type="checkbox" name="oneclick_seo_pro_sitemap[post_types][]"
                                           value="<?php echo esc_attr($type->name); ?>"
                                           <?php checked(in_array($type->name, $selected_types)); ?>>
                                    <?php echo esc_html($type->labels->name); ?>
                                </label>
                                <?php endforeach; ?>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Include Images', 'oneclick-seo-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="oneclick_seo_pro_sitemap[include_images]" value="1"
                                           <?php checked(!empty($sitemap_settings['include_images'])); ?>>
                                    <?php esc_html_e('Include images in sitemap', 'oneclick-seo-pro'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Ping Search Engines', 'oneclick-seo-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="oneclick_seo_pro_sitemap[ping_search_engines]" value="1"
                                           <?php checked(!empty($sitemap_settings['ping_search_engines'])); ?>>
                                    <?php esc_html_e('Notify Google and Bing when content is published', 'oneclick-seo-pro'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <h3><?php esc_html_e('Sitemap URLs', 'oneclick-seo-pro'); ?></h3>
                    <p>
                        <strong><?php esc_html_e('Main Sitemap:', 'oneclick-seo-pro'); ?></strong>
                        <a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" target="_blank">
                            <?php echo esc_html(home_url('/sitemap.xml')); ?>
                        </a>
                    </p>

                    <?php submit_button(); ?>
                </form>
                <?php
                break;

            case 'schema':
                $org_settings = get_option('oneclick_seo_pro_organization', []);
                ?>
                <form method="post" action="options.php">
                    <?php settings_fields('oneclick_seo_pro_schema'); ?>

                    <h3><?php esc_html_e('Organization Information', 'oneclick-seo-pro'); ?></h3>

                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="org_name"><?php esc_html_e('Organization Name', 'oneclick-seo-pro'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="org_name" name="oneclick_seo_pro_organization[name]"
                                       value="<?php echo esc_attr($org_settings['name'] ?? get_bloginfo('name')); ?>"
                                       class="regular-text">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="org_url"><?php esc_html_e('Website URL', 'oneclick-seo-pro'); ?></label>
                            </th>
                            <td>
                                <input type="url" id="org_url" name="oneclick_seo_pro_organization[url]"
                                       value="<?php echo esc_attr($org_settings['url'] ?? home_url()); ?>"
                                       class="regular-text">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="org_logo"><?php esc_html_e('Logo URL', 'oneclick-seo-pro'); ?></label>
                            </th>
                            <td>
                                <input type="url" id="org_logo" name="oneclick_seo_pro_organization[logo]"
                                       value="<?php echo esc_attr($org_settings['logo'] ?? ''); ?>"
                                       class="regular-text">
                                <button type="button" class="button" id="select-logo"><?php esc_html_e('Select Image', 'oneclick-seo-pro'); ?></button>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e('Social Profiles', 'oneclick-seo-pro'); ?></label>
                            </th>
                            <td>
                                <?php
                                $social_profiles = $org_settings['same_as'] ?? [];
                                $socials = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'];
                                foreach ($socials as $i => $social):
                                    $value = $social_profiles[$i] ?? '';
                                ?>
                                <input type="url" name="oneclick_seo_pro_organization[same_as][]"
                                       value="<?php echo esc_attr($value); ?>"
                                       placeholder="<?php echo esc_attr(ucfirst($social) . ' URL'); ?>"
                                       class="regular-text" style="margin-bottom: 5px; display: block;">
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    </table>

                    <?php submit_button(); ?>
                </form>
                <?php
                break;

            case 'aeo':
                $aeo_settings = get_option('oneclick_seo_pro_aeo', []);
                ?>
                <form method="post" action="options.php">
                    <?php settings_fields('oneclick_seo_pro_aeo'); ?>

                    <h3><?php esc_html_e('AI Engine Optimization (AEO)', 'oneclick-seo-pro'); ?></h3>
                    <p class="description">
                        <?php esc_html_e('Optimize your content for AI search engines like ChatGPT, Perplexity, and Google SGE.', 'oneclick-seo-pro'); ?>
                    </p>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Enable AEO', 'oneclick-seo-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="oneclick_seo_pro_aeo[enabled]" value="1"
                                           <?php checked(!empty($aeo_settings['enabled'])); ?>>
                                    <?php esc_html_e('Enable AEO analysis and optimization', 'oneclick-seo-pro'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Auto-generate FAQ', 'oneclick-seo-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="oneclick_seo_pro_aeo[auto_generate_faq]" value="1"
                                           <?php checked(!empty($aeo_settings['auto_generate_faq'])); ?>>
                                    <?php esc_html_e('Automatically generate FAQ from content', 'oneclick-seo-pro'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Snippet Optimization', 'oneclick-seo-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="oneclick_seo_pro_aeo[auto_optimize_snippets]" value="1"
                                           <?php checked(!empty($aeo_settings['auto_optimize_snippets'])); ?>>
                                    <?php esc_html_e('Optimize content for featured snippets', 'oneclick-seo-pro'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="ai_provider"><?php esc_html_e('AI Provider', 'oneclick-seo-pro'); ?></label>
                            </th>
                            <td>
                                <select id="ai_provider" name="oneclick_seo_pro_aeo[ai_provider]">
                                    <option value="openai" <?php selected($aeo_settings['ai_provider'] ?? '', 'openai'); ?>>
                                        OpenAI (GPT-4)
                                    </option>
                                    <option value="anthropic" <?php selected($aeo_settings['ai_provider'] ?? '', 'anthropic'); ?>>
                                        Anthropic (Claude)
                                    </option>
                                </select>
                            </td>
                        </tr>
                    </table>

                    <?php submit_button(); ?>
                </form>
                <?php
                break;

            default: // general
                $general_settings = get_option('oneclick_seo_pro_general', []);
                ?>
                <form method="post" action="options.php">
                    <?php settings_fields('oneclick_seo_pro_general'); ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Post Types to Analyze', 'oneclick-seo-pro'); ?></th>
                            <td>
                                <?php
                                $post_types = get_post_types(['public' => true], 'objects');
                                $selected_types = $general_settings['post_types'] ?? ['post', 'page'];
                                foreach ($post_types as $type):
                                    if ($type->name === 'attachment') continue;
                                ?>
                                <label style="display: block; margin-bottom: 5px;">
                                    <input type="checkbox" name="oneclick_seo_pro_general[post_types][]"
                                           value="<?php echo esc_attr($type->name); ?>"
                                           <?php checked(in_array($type->name, $selected_types)); ?>>
                                    <?php echo esc_html($type->labels->name); ?>
                                </label>
                                <?php endforeach; ?>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Auto-analyze', 'oneclick-seo-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="oneclick_seo_pro_general[auto_analyze]" value="1"
                                           <?php checked(!empty($general_settings['auto_analyze'])); ?>>
                                    <?php esc_html_e('Automatically analyze content on save', 'oneclick-seo-pro'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Show Score in Lists', 'oneclick-seo-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="oneclick_seo_pro_general[score_in_list]" value="1"
                                           <?php checked($general_settings['score_in_list'] ?? true); ?>>
                                    <?php esc_html_e('Show SEO score column in post lists', 'oneclick-seo-pro'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <?php submit_button(); ?>
                </form>
                <?php
        endswitch;
        ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Media uploader for logo
    $('#select-logo').on('click', function(e) {
        e.preventDefault();

        var frame = wp.media({
            title: '<?php esc_html_e('Select Logo', 'oneclick-seo-pro'); ?>',
            button: { text: '<?php esc_html_e('Use as Logo', 'oneclick-seo-pro'); ?>' },
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#org_logo').val(attachment.url);
        });

        frame.open();
    });
});
</script>
