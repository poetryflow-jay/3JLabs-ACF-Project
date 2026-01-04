<?php
/**
 * WP Bulk SEO - Settings View
 *
 * @package WP_Bulk_SEO
 * @subpackage Admin/Views
 */

if (!defined('ABSPATH')) {
    exit;
}

$tabs = [
    'general' => __('General', 'wp-bulk-seo'),
    'api' => __('API Keys', 'wp-bulk-seo'),
    'search_console' => __('Search Console', 'wp-bulk-seo'), // [v2.0.0]
    'monitor' => __('Monitoring', 'wp-bulk-seo'), // [v2.0.0]
    'sitemap' => __('Sitemap', 'wp-bulk-seo'),
    'schema' => __('Schema', 'wp-bulk-seo'),
    'aeo' => __('AEO', 'wp-bulk-seo'),
];
?>
<div class="wrap wp-bulk-seo-settings">
    <h1><?php esc_html_e('WP Bulk SEO Settings', 'wp-bulk-seo'); ?></h1>

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
                    <?php settings_fields('wp_bulk_seo_api'); ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="pagespeed_api_key"><?php esc_html_e('PageSpeed API Key', 'wp-bulk-seo'); ?></label>
                            </th>
                            <td>
                                <input type="password" id="pagespeed_api_key" name="wp_bulk_seo_pagespeed_api_key"
                                       value="<?php echo esc_attr(get_option('wp_bulk_seo_pagespeed_api_key', '')); ?>"
                                       class="regular-text">
                                <p class="description">
                                    <?php printf(
                                        __('Get your API key from %s', 'wp-bulk-seo'),
                                        '<a href="https://developers.google.com/speed/docs/insights/v5/get-started" target="_blank">Google PageSpeed API</a>'
                                    ); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="openai_api_key"><?php esc_html_e('OpenAI API Key', 'wp-bulk-seo'); ?></label>
                            </th>
                            <td>
                                <input type="password" id="openai_api_key" name="wp_bulk_seo_openai_api_key"
                                       value="<?php echo esc_attr(get_option('wp_bulk_seo_openai_api_key', '')); ?>"
                                       class="regular-text">
                                <p class="description">
                                    <?php printf(
                                        __('Get your API key from %s', 'wp-bulk-seo'),
                                        '<a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a>'
                                    ); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="anthropic_api_key"><?php esc_html_e('Anthropic API Key', 'wp-bulk-seo'); ?></label>
                            </th>
                            <td>
                                <input type="password" id="anthropic_api_key" name="wp_bulk_seo_anthropic_api_key"
                                       value="<?php echo esc_attr(get_option('wp_bulk_seo_anthropic_api_key', '')); ?>"
                                       class="regular-text">
                                <p class="description">
                                    <?php printf(
                                        __('Get your API key from %s', 'wp-bulk-seo'),
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

            case 'search_console': // [v2.0.0]
                $gsc_client_id = get_option('wp_bulk_seo_gsc_client_id', '');
                $gsc_client_secret = get_option('wp_bulk_seo_gsc_client_secret', '');
                $gsc_connected = false;

                if (class_exists('WP_Bulk_SEO_Google_Search_Console')) {
                    $gsc = WP_Bulk_SEO_Google_Search_Console::instance();
                    $gsc_connected = $gsc->is_connected();
                }
                ?>
                <div class="search-console-settings">
                    <h2><?php esc_html_e('Google Search Console 연동', 'wp-bulk-seo'); ?></h2>
                    <p class="description">
                        <?php esc_html_e('Google Search Console API를 통해 검색 성과 데이터를 가져옵니다.', 'wp-bulk-seo'); ?>
                    </p>

                    <?php if (!$gsc_connected): ?>
                    <div class="notice notice-info">
                        <p>
                            <strong><?php esc_html_e('연동 방법:', 'wp-bulk-seo'); ?></strong><br>
                            1. <?php esc_html_e('Google Cloud Console에서 OAuth 2.0 클라이언트 ID를 생성하세요.', 'wp-bulk-seo'); ?><br>
                            2. <?php esc_html_e('아래에 클라이언트 ID와 Secret을 입력하세요.', 'wp-bulk-seo'); ?><br>
                            3. <?php esc_html_e('인증 버튼을 클릭하여 Google 계정에 연결하세요.', 'wp-bulk-seo'); ?>
                        </p>
                        <p>
                            <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="button">
                                <?php esc_html_e('Google Cloud Console 열기', 'wp-bulk-seo'); ?>
                            </a>
                        </p>
                    </div>

                    <form method="post" action="options.php">
                        <?php settings_fields('wp_bulk_seo_gsc'); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="gsc_client_id"><?php esc_html_e('Client ID', 'wp-bulk-seo'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="gsc_client_id" name="wp_bulk_seo_gsc_client_id"
                                           value="<?php echo esc_attr($gsc_client_id); ?>"
                                           class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="gsc_client_secret"><?php esc_html_e('Client Secret', 'wp-bulk-seo'); ?></label>
                                </th>
                                <td>
                                    <input type="password" id="gsc_client_secret" name="wp_bulk_seo_gsc_client_secret"
                                           value="<?php echo esc_attr($gsc_client_secret); ?>"
                                           class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Redirect URI', 'wp-bulk-seo'); ?></th>
                                <td>
                                    <code><?php echo esc_html(admin_url('admin.php?page=wp-bulk-seo-settings&tab=search_console')); ?></code>
                                    <p class="description">
                                        <?php esc_html_e('이 URL을 Google Cloud Console의 승인된 리디렉션 URI에 추가하세요.', 'wp-bulk-seo'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button(); ?>
                    </form>

                    <?php if (!empty($gsc_client_id) && !empty($gsc_client_secret)): ?>
                    <div style="margin-top: 20px;">
                        <?php
                        $auth_url = '';
                        if (class_exists('WP_Bulk_SEO_Google_Search_Console')) {
                            $gsc = WP_Bulk_SEO_Google_Search_Console::instance();
                            $auth_url = $gsc->get_auth_url();
                        }
                        ?>
                        <?php if (!empty($auth_url)): ?>
                        <a href="<?php echo esc_url($auth_url); ?>" class="button button-primary button-large">
                            <?php esc_html_e('🔗 Google 계정에 연결', 'wp-bulk-seo'); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php else: ?>
                    <div class="notice notice-success">
                        <p>
                            <strong><?php esc_html_e('✓ Google Search Console에 연결되었습니다.', 'wp-bulk-seo'); ?></strong>
                        </p>
                    </div>

                    <div style="margin-top: 20px;">
                        <h3><?php esc_html_e('연결된 사이트', 'wp-bulk-seo'); ?></h3>
                        <p><?php echo esc_html(get_site_url()); ?></p>
                    </div>

                    <form method="post" action="">
                        <?php wp_nonce_field('wp_bulk_seo_disconnect_gsc', 'disconnect_nonce'); ?>
                        <input type="hidden" name="action" value="disconnect_gsc">
                        <button type="submit" class="button button-secondary" onclick="return confirm('<?php esc_attr_e('연결을 해제하시겠습니까?', 'wp-bulk-seo'); ?>');">
                            <?php esc_html_e('연결 해제', 'wp-bulk-seo'); ?>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
                <?php
                break;

            case 'monitor': // [v2.0.0]
                $monitor_settings = get_option('wp_bulk_seo_monitor_settings', []);
                $notifications = $monitor_settings['notifications'] ?? [];
                $notification_methods = $monitor_settings['notification_methods'] ?? ['email'];
                ?>
                <form method="post" action="options.php">
                    <?php
                    // Register settings if not already registered
                    if (!get_option('wp_bulk_seo_monitor_settings')) {
                        register_setting('wp_bulk_seo_monitor', 'wp_bulk_seo_monitor_settings');
                    }
                    settings_fields('wp_bulk_seo_monitor');
                    ?>

                    <h2><?php esc_html_e('실시간 모니터링 설정', 'wp-bulk-seo'); ?></h2>
                    <p class="description">
                        <?php esc_html_e('SEO 점수 변화와 이슈를 실시간으로 모니터링하고 알림을 받습니다.', 'wp-bulk-seo'); ?>
                    </p>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('알림 활성화', 'wp-bulk-seo'); ?></th>
                            <td>
                                <fieldset>
                                    <label style="display: block; margin-bottom: 10px;">
                                        <input type="checkbox" name="wp_bulk_seo_monitor_settings[notifications][score_drop]" value="1"
                                               <?php checked(!empty($notifications['score_drop'])); ?>>
                                        <?php esc_html_e('점수 급락 알림 (10점 이상 하락 시)', 'wp-bulk-seo'); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 10px;">
                                        <input type="checkbox" name="wp_bulk_seo_monitor_settings[notifications][critical_issue]" value="1"
                                               <?php checked(!empty($notifications['critical_issue'])); ?>>
                                        <?php esc_html_e('Critical 이슈 알림 (즉시)', 'wp-bulk-seo'); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 10px;">
                                        <input type="checkbox" name="wp_bulk_seo_monitor_settings[notifications][multiple_issues]" value="1"
                                               <?php checked(!empty($notifications['multiple_issues'])); ?>>
                                        <?php esc_html_e('다중 이슈 알림 (24시간 내 5개 이상)', 'wp-bulk-seo'); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('알림 방법', 'wp-bulk-seo'); ?></th>
                            <td>
                                <fieldset>
                                    <label style="display: block; margin-bottom: 10px;">
                                        <input type="checkbox" name="wp_bulk_seo_monitor_settings[notification_methods][]" value="email"
                                               <?php checked(in_array('email', $notification_methods)); ?>>
                                        <?php esc_html_e('이메일', 'wp-bulk-seo'); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 10px;">
                                        <input type="checkbox" name="wp_bulk_seo_monitor_settings[notification_methods][]" value="dashboard"
                                               <?php checked(in_array('dashboard', $notification_methods)); ?>>
                                        <?php esc_html_e('대시보드 알림', 'wp-bulk-seo'); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 10px;">
                                        <input type="checkbox" name="wp_bulk_seo_monitor_settings[notification_methods][]" value="webhook"
                                               <?php checked(in_array('webhook', $notification_methods)); ?>>
                                        <?php esc_html_e('Webhook', 'wp-bulk-seo'); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="webhook_url"><?php esc_html_e('Webhook URL', 'wp-bulk-seo'); ?></label>
                            </th>
                            <td>
                                <input type="url" id="webhook_url" name="wp_bulk_seo_webhook_url"
                                       value="<?php echo esc_attr(get_option('wp_bulk_seo_webhook_url', '')); ?>"
                                       class="regular-text"
                                       placeholder="https://example.com/webhook">
                                <p class="description">
                                    <?php esc_html_e('알림을 받을 Webhook URL을 입력하세요.', 'wp-bulk-seo'); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('일일 리포트', 'wp-bulk-seo'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wp_bulk_seo_send_daily_report" value="1"
                                           <?php checked(get_option('wp_bulk_seo_send_daily_report', false)); ?>>
                                    <?php esc_html_e('매일 SEO 리포트를 이메일로 받기', 'wp-bulk-seo'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('임계값 설정', 'wp-bulk-seo'); ?></th>
                            <td>
                                <label style="display: block; margin-bottom: 10px;">
                                    <?php esc_html_e('점수 하락 임계값:', 'wp-bulk-seo'); ?>
                                    <input type="number" name="wp_bulk_seo_monitor_settings[thresholds][score_drop]" 
                                           value="<?php echo esc_attr($monitor_settings['thresholds']['score_drop'] ?? 10); ?>"
                                           min="1" max="50" style="width: 80px;">
                                    <?php esc_html_e('점', 'wp-bulk-seo'); ?>
                                </label>
                                <label style="display: block; margin-bottom: 10px;">
                                    <?php esc_html_e('새 이슈 임계값:', 'wp-bulk-seo'); ?>
                                    <input type="number" name="wp_bulk_seo_monitor_settings[thresholds][new_issue]" 
                                           value="<?php echo esc_attr($monitor_settings['thresholds']['new_issue'] ?? 5); ?>"
                                           min="1" max="20" style="width: 80px;">
                                    <?php esc_html_e('개', 'wp-bulk-seo'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <?php submit_button(); ?>
                </form>
                <?php
                break;

            case 'sitemap':
                $sitemap_settings = get_option('wp_bulk_seo_sitemap', []);
                ?>
                <form method="post" action="options.php">
                    <?php settings_fields('wp_bulk_seo_sitemap'); ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Enable Sitemap', 'wp-bulk-seo'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wp_bulk_seo_sitemap[enabled]" value="1"
                                           <?php checked(!empty($sitemap_settings['enabled'])); ?>>
                                    <?php esc_html_e('Generate XML sitemap', 'wp-bulk-seo'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Post Types', 'wp-bulk-seo'); ?></th>
                            <td>
                                <?php
                                $post_types = get_post_types(['public' => true], 'objects');
                                $selected_types = $sitemap_settings['post_types'] ?? ['post', 'page'];
                                foreach ($post_types as $type):
                                    if ($type->name === 'attachment') continue;
                                ?>
                                <label style="display: block; margin-bottom: 5px;">
                                    <input type="checkbox" name="wp_bulk_seo_sitemap[post_types][]"
                                           value="<?php echo esc_attr($type->name); ?>"
                                           <?php checked(in_array($type->name, $selected_types)); ?>>
                                    <?php echo esc_html($type->labels->name); ?>
                                </label>
                                <?php endforeach; ?>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Include Images', 'wp-bulk-seo'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wp_bulk_seo_sitemap[include_images]" value="1"
                                           <?php checked(!empty($sitemap_settings['include_images'])); ?>>
                                    <?php esc_html_e('Include images in sitemap', 'wp-bulk-seo'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Ping Search Engines', 'wp-bulk-seo'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wp_bulk_seo_sitemap[ping_search_engines]" value="1"
                                           <?php checked(!empty($sitemap_settings['ping_search_engines'])); ?>>
                                    <?php esc_html_e('Notify Google and Bing when content is published', 'wp-bulk-seo'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <h3><?php esc_html_e('Sitemap URLs', 'wp-bulk-seo'); ?></h3>
                    <p>
                        <strong><?php esc_html_e('Main Sitemap:', 'wp-bulk-seo'); ?></strong>
                        <a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" target="_blank">
                            <?php echo esc_html(home_url('/sitemap.xml')); ?>
                        </a>
                    </p>

                    <?php submit_button(); ?>
                </form>
                <?php
                break;

            case 'schema':
                $org_settings = get_option('wp_bulk_seo_organization', []);
                ?>
                <form method="post" action="options.php">
                    <?php settings_fields('wp_bulk_seo_schema'); ?>

                    <h3><?php esc_html_e('Organization Information', 'wp-bulk-seo'); ?></h3>

                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="org_name"><?php esc_html_e('Organization Name', 'wp-bulk-seo'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="org_name" name="wp_bulk_seo_organization[name]"
                                       value="<?php echo esc_attr($org_settings['name'] ?? get_bloginfo('name')); ?>"
                                       class="regular-text">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="org_url"><?php esc_html_e('Website URL', 'wp-bulk-seo'); ?></label>
                            </th>
                            <td>
                                <input type="url" id="org_url" name="wp_bulk_seo_organization[url]"
                                       value="<?php echo esc_attr($org_settings['url'] ?? home_url()); ?>"
                                       class="regular-text">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="org_logo"><?php esc_html_e('Logo URL', 'wp-bulk-seo'); ?></label>
                            </th>
                            <td>
                                <input type="url" id="org_logo" name="wp_bulk_seo_organization[logo]"
                                       value="<?php echo esc_attr($org_settings['logo'] ?? ''); ?>"
                                       class="regular-text">
                                <button type="button" class="button" id="select-logo"><?php esc_html_e('Select Image', 'wp-bulk-seo'); ?></button>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e('Social Profiles', 'wp-bulk-seo'); ?></label>
                            </th>
                            <td>
                                <?php
                                $social_profiles = $org_settings['same_as'] ?? [];
                                $socials = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'];
                                foreach ($socials as $i => $social):
                                    $value = $social_profiles[$i] ?? '';
                                ?>
                                <input type="url" name="wp_bulk_seo_organization[same_as][]"
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
                $aeo_settings = get_option('wp_bulk_seo_aeo', []);
                ?>
                <form method="post" action="options.php">
                    <?php settings_fields('wp_bulk_seo_aeo'); ?>

                    <h3><?php esc_html_e('AI Engine Optimization (AEO)', 'wp-bulk-seo'); ?></h3>
                    <p class="description">
                        <?php esc_html_e('Optimize your content for AI search engines like ChatGPT, Perplexity, and Google SGE.', 'wp-bulk-seo'); ?>
                    </p>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Enable AEO', 'wp-bulk-seo'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wp_bulk_seo_aeo[enabled]" value="1"
                                           <?php checked(!empty($aeo_settings['enabled'])); ?>>
                                    <?php esc_html_e('Enable AEO analysis and optimization', 'wp-bulk-seo'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Auto-generate FAQ', 'wp-bulk-seo'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wp_bulk_seo_aeo[auto_generate_faq]" value="1"
                                           <?php checked(!empty($aeo_settings['auto_generate_faq'])); ?>>
                                    <?php esc_html_e('Automatically generate FAQ from content', 'wp-bulk-seo'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Snippet Optimization', 'wp-bulk-seo'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wp_bulk_seo_aeo[auto_optimize_snippets]" value="1"
                                           <?php checked(!empty($aeo_settings['auto_optimize_snippets'])); ?>>
                                    <?php esc_html_e('Optimize content for featured snippets', 'wp-bulk-seo'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="ai_provider"><?php esc_html_e('AI Provider', 'wp-bulk-seo'); ?></label>
                            </th>
                            <td>
                                <select id="ai_provider" name="wp_bulk_seo_aeo[ai_provider]">
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
                $general_settings = get_option('wp_bulk_seo_general', []);
                ?>
                <form method="post" action="options.php">
                    <?php settings_fields('wp_bulk_seo_general'); ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Post Types to Analyze', 'wp-bulk-seo'); ?></th>
                            <td>
                                <?php
                                $post_types = get_post_types(['public' => true], 'objects');
                                $selected_types = $general_settings['post_types'] ?? ['post', 'page'];
                                foreach ($post_types as $type):
                                    if ($type->name === 'attachment') continue;
                                ?>
                                <label style="display: block; margin-bottom: 5px;">
                                    <input type="checkbox" name="wp_bulk_seo_general[post_types][]"
                                           value="<?php echo esc_attr($type->name); ?>"
                                           <?php checked(in_array($type->name, $selected_types)); ?>>
                                    <?php echo esc_html($type->labels->name); ?>
                                </label>
                                <?php endforeach; ?>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Auto-analyze', 'wp-bulk-seo'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wp_bulk_seo_general[auto_analyze]" value="1"
                                           <?php checked(!empty($general_settings['auto_analyze'])); ?>>
                                    <?php esc_html_e('Automatically analyze content on save', 'wp-bulk-seo'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Show Score in Lists', 'wp-bulk-seo'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wp_bulk_seo_general[score_in_list]" value="1"
                                           <?php checked($general_settings['score_in_list'] ?? true); ?>>
                                    <?php esc_html_e('Show SEO score column in post lists', 'wp-bulk-seo'); ?>
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
            title: '<?php esc_html_e('Select Logo', 'wp-bulk-seo'); ?>',
            button: { text: '<?php esc_html_e('Use as Logo', 'wp-bulk-seo'); ?>' },
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
