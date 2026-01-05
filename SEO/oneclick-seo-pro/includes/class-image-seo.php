<?php
/**
 * OneClick SEO Pro - Image SEO Module
 *
 * Automatic image optimization for SEO:
 * - Auto alt text generation
 * - Title attribute optimization
 * - Filename to alt text conversion
 * - Lazy loading management
 * - WebP suggestion
 * - Image sitemap integration
 *
 * @package OneClick_SEO_Pro
 * @subpackage ImageSEO
 * @version 2.1.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Image SEO Class
 */
class OneClick_SEO_Pro_Image_SEO {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Settings
     */
    private $settings = [];

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->settings = get_option('oneclick_seo_pro_image_seo', $this->get_default_settings());
        $this->init_hooks();
    }

    /**
     * Get default settings
     */
    private function get_default_settings() {
        return [
            'enabled' => true,
            'auto_alt' => true,
            'auto_title' => true,
            'filename_to_alt' => true,
            'strip_numbers' => true,
            'add_focus_keyword' => false,
            'lazy_loading' => true,
            'webp_notice' => true,
            'bulk_optimize' => true,
        ];
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        if (empty($this->settings['enabled'])) {
            return;
        }

        // Auto-fill alt/title on upload
        add_filter('wp_generate_attachment_metadata', [$this, 'auto_fill_image_meta'], 10, 2);

        // Filter content images
        add_filter('the_content', [$this, 'optimize_content_images'], 99);

        // Add lazy loading
        if (!empty($this->settings['lazy_loading'])) {
            add_filter('wp_get_attachment_image_attributes', [$this, 'add_lazy_loading'], 10, 3);
        }

        // Admin hooks
        if (is_admin()) {
            add_filter('attachment_fields_to_edit', [$this, 'add_seo_fields_to_attachment'], 10, 2);
            add_filter('attachment_fields_to_save', [$this, 'save_seo_fields'], 10, 2);
            add_action('admin_notices', [$this, 'show_webp_notice']);

            // Bulk actions
            add_filter('bulk_actions-upload', [$this, 'add_bulk_actions']);
            add_filter('handle_bulk_actions-upload', [$this, 'handle_bulk_actions'], 10, 3);
            add_action('admin_notices', [$this, 'bulk_action_admin_notice']);

            // AJAX handlers
            add_action('wp_ajax_oneclick_seo_image_analyze', [$this, 'ajax_analyze_images']);
            add_action('wp_ajax_oneclick_seo_image_bulk_optimize', [$this, 'ajax_bulk_optimize']);
            add_action('wp_ajax_oneclick_seo_generate_alt', [$this, 'ajax_generate_alt']);
        }

        // REST API
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    /**
     * Auto-fill image meta on upload
     *
     * @param array $metadata Attachment metadata
     * @param int $attachment_id Attachment ID
     * @return array Modified metadata
     */
    public function auto_fill_image_meta($metadata, $attachment_id) {
        $attachment = get_post($attachment_id);

        if (!$attachment || !wp_attachment_is_image($attachment_id)) {
            return $metadata;
        }

        // Get filename without extension
        $filename = pathinfo(get_attached_file($attachment_id), PATHINFO_FILENAME);

        // Clean filename for alt text
        $cleaned_name = $this->filename_to_text($filename);

        $update_data = [];

        // Auto alt text
        if (!empty($this->settings['auto_alt'])) {
            $current_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
            if (empty($current_alt)) {
                update_post_meta($attachment_id, '_wp_attachment_image_alt', $cleaned_name);
            }
        }

        // Auto title
        if (!empty($this->settings['auto_title'])) {
            if (empty($attachment->post_title) || $attachment->post_title === $filename) {
                $update_data['post_title'] = ucwords($cleaned_name);
            }
        }

        // Update post if needed
        if (!empty($update_data)) {
            $update_data['ID'] = $attachment_id;
            wp_update_post($update_data);
        }

        // Store original filename for reference
        update_post_meta($attachment_id, '_oneclick_seo_original_filename', $filename);

        return $metadata;
    }

    /**
     * Convert filename to readable text
     *
     * @param string $filename Filename without extension
     * @return string Cleaned text
     */
    public function filename_to_text($filename) {
        // Replace common separators with spaces
        $text = str_replace(['-', '_', '.'], ' ', $filename);

        // Remove numbers if setting enabled
        if (!empty($this->settings['strip_numbers'])) {
            $text = preg_replace('/\b\d+\b/', '', $text);
        }

        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // Capitalize first letter of each word
        $text = ucwords(strtolower($text));

        return $text;
    }

    /**
     * Optimize images in content
     *
     * @param string $content Post content
     * @return string Modified content
     */
    public function optimize_content_images($content) {
        if (empty($content)) {
            return $content;
        }

        // Parse images in content
        $content = preg_replace_callback(
            '/<img([^>]+)>/i',
            [$this, 'optimize_single_image'],
            $content
        );

        return $content;
    }

    /**
     * Optimize a single image tag
     *
     * @param array $matches Regex matches
     * @return string Modified image tag
     */
    public function optimize_single_image($matches) {
        $img_tag = $matches[0];
        $attributes = $matches[1];

        // Check if alt is empty or missing
        $has_alt = preg_match('/alt\s*=\s*["\']([^"\']*)["\']/', $attributes, $alt_match);
        $alt_value = $has_alt ? $alt_match[1] : '';

        // Get src for filename fallback
        preg_match('/src\s*=\s*["\']([^"\']+)["\']/', $attributes, $src_match);
        $src = !empty($src_match[1]) ? $src_match[1] : '';

        // Auto-fill empty alt
        if (empty($alt_value) && !empty($src) && !empty($this->settings['filename_to_alt'])) {
            $filename = pathinfo(parse_url($src, PHP_URL_PATH), PATHINFO_FILENAME);
            $generated_alt = $this->filename_to_text($filename);

            // Add focus keyword if enabled and available
            if (!empty($this->settings['add_focus_keyword'])) {
                $post_id = get_the_ID();
                if ($post_id) {
                    $focus_keyword = get_post_meta($post_id, '_oneclick_seo_pro_focus_keyword', true);
                    if ($focus_keyword && stripos($generated_alt, $focus_keyword) === false) {
                        $generated_alt .= ' - ' . $focus_keyword;
                    }
                }
            }

            if ($has_alt) {
                // Replace empty alt
                $img_tag = preg_replace('/alt\s*=\s*["\'][^"\']*["\']/', 'alt="' . esc_attr($generated_alt) . '"', $img_tag);
            } else {
                // Add alt attribute
                $img_tag = str_replace('<img', '<img alt="' . esc_attr($generated_alt) . '"', $img_tag);
            }
        }

        // Add title if missing and setting enabled
        if (!empty($this->settings['auto_title'])) {
            $has_title = preg_match('/title\s*=\s*["\']/', $attributes);
            if (!$has_title && !empty($alt_value)) {
                $img_tag = str_replace('<img', '<img title="' . esc_attr($alt_value) . '"', $img_tag);
            }
        }

        return $img_tag;
    }

    /**
     * Add lazy loading attribute
     *
     * @param array $attr Attributes array
     * @param WP_Post $attachment Attachment post
     * @param string|array $size Image size
     * @return array Modified attributes
     */
    public function add_lazy_loading($attr, $attachment, $size) {
        // Skip if already has loading attribute
        if (isset($attr['loading'])) {
            return $attr;
        }

        // Skip for small sizes (likely thumbnails/icons)
        if (is_string($size) && in_array($size, ['thumbnail', 'post-thumbnail'])) {
            return $attr;
        }

        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';

        return $attr;
    }

    /**
     * Add SEO fields to attachment edit page
     *
     * @param array $form_fields Form fields
     * @param WP_Post $post Attachment post
     * @return array Modified form fields
     */
    public function add_seo_fields_to_attachment($form_fields, $post) {
        if (!wp_attachment_is_image($post->ID)) {
            return $form_fields;
        }

        // SEO Score
        $seo_score = $this->calculate_image_seo_score($post->ID);
        $score_color = $seo_score >= 80 ? '#10B981' : ($seo_score >= 50 ? '#F59E0B' : '#EF4444');

        $form_fields['oneclick_seo_score'] = [
            'label' => __('SEO Score', 'oneclick-seo-pro'),
            'input' => 'html',
            'html' => sprintf(
                '<div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 50px; height: 50px; border-radius: 50%%; background: %s; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px;">%d</div>
                    <span style="color: %s; font-weight: 500;">%s</span>
                </div>',
                esc_attr($score_color),
                intval($seo_score),
                esc_attr($score_color),
                $this->get_score_label($seo_score)
            ),
        ];

        // Original filename
        $original_filename = get_post_meta($post->ID, '_oneclick_seo_original_filename', true);
        if ($original_filename) {
            $form_fields['oneclick_original_filename'] = [
                'label' => __('Original Filename', 'oneclick-seo-pro'),
                'input' => 'html',
                'html' => '<code>' . esc_html($original_filename) . '</code>',
            ];
        }

        // Suggestions
        $suggestions = $this->get_image_suggestions($post->ID);
        if (!empty($suggestions)) {
            $suggestions_html = '<ul style="margin: 0; padding-left: 20px;">';
            foreach ($suggestions as $suggestion) {
                $suggestions_html .= '<li style="color: #666;">' . esc_html($suggestion) . '</li>';
            }
            $suggestions_html .= '</ul>';

            $form_fields['oneclick_seo_suggestions'] = [
                'label' => __('SEO Suggestions', 'oneclick-seo-pro'),
                'input' => 'html',
                'html' => $suggestions_html,
            ];
        }

        return $form_fields;
    }

    /**
     * Save SEO fields
     *
     * @param array $post Post data
     * @param array $attachment Attachment data
     * @return array Modified post data
     */
    public function save_seo_fields($post, $attachment) {
        // Future: Save custom SEO fields
        return $post;
    }

    /**
     * Calculate image SEO score
     *
     * @param int $attachment_id Attachment ID
     * @return int Score 0-100
     */
    public function calculate_image_seo_score($attachment_id) {
        $score = 0;
        $max_score = 100;

        $attachment = get_post($attachment_id);
        if (!$attachment) {
            return 0;
        }

        // Alt text (40 points)
        $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        if (!empty($alt)) {
            $score += 30;
            // Bonus for descriptive alt (more than 3 words)
            if (str_word_count($alt) >= 3) {
                $score += 10;
            }
        }

        // Title (20 points)
        if (!empty($attachment->post_title)) {
            $filename = pathinfo(get_attached_file($attachment_id), PATHINFO_FILENAME);
            // Title is different from filename
            if (strtolower($attachment->post_title) !== strtolower($filename)) {
                $score += 20;
            } else {
                $score += 10;
            }
        }

        // Caption (15 points)
        if (!empty($attachment->post_excerpt)) {
            $score += 15;
        }

        // Description (15 points)
        if (!empty($attachment->post_content)) {
            $score += 15;
        }

        // File size check (10 points)
        $file_path = get_attached_file($attachment_id);
        if ($file_path && file_exists($file_path)) {
            $file_size = filesize($file_path);
            // Under 200KB is good
            if ($file_size < 200 * 1024) {
                $score += 10;
            } elseif ($file_size < 500 * 1024) {
                $score += 5;
            }
        }

        return min($score, $max_score);
    }

    /**
     * Get score label
     *
     * @param int $score Score
     * @return string Label
     */
    private function get_score_label($score) {
        if ($score >= 90) return __('Excellent', 'oneclick-seo-pro');
        if ($score >= 80) return __('Good', 'oneclick-seo-pro');
        if ($score >= 60) return __('Fair', 'oneclick-seo-pro');
        if ($score >= 40) return __('Needs Work', 'oneclick-seo-pro');
        return __('Poor', 'oneclick-seo-pro');
    }

    /**
     * Get image SEO suggestions
     *
     * @param int $attachment_id Attachment ID
     * @return array Suggestions
     */
    public function get_image_suggestions($attachment_id) {
        $suggestions = [];
        $attachment = get_post($attachment_id);

        if (!$attachment) {
            return $suggestions;
        }

        // Alt text
        $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        if (empty($alt)) {
            $suggestions[] = __('Add alt text to improve accessibility and SEO', 'oneclick-seo-pro');
        } elseif (str_word_count($alt) < 3) {
            $suggestions[] = __('Make alt text more descriptive (3+ words recommended)', 'oneclick-seo-pro');
        }

        // Title
        $filename = pathinfo(get_attached_file($attachment_id), PATHINFO_FILENAME);
        if (empty($attachment->post_title) || strtolower($attachment->post_title) === strtolower($filename)) {
            $suggestions[] = __('Add a descriptive title different from filename', 'oneclick-seo-pro');
        }

        // Caption
        if (empty($attachment->post_excerpt)) {
            $suggestions[] = __('Add a caption for better context', 'oneclick-seo-pro');
        }

        // File size
        $file_path = get_attached_file($attachment_id);
        if ($file_path && file_exists($file_path)) {
            $file_size = filesize($file_path);
            if ($file_size > 500 * 1024) {
                $suggestions[] = sprintf(
                    __('Image is large (%s). Consider compressing for faster loading.', 'oneclick-seo-pro'),
                    size_format($file_size)
                );
            }

            // WebP check
            $mime_type = get_post_mime_type($attachment_id);
            if ($mime_type !== 'image/webp' && !empty($this->settings['webp_notice'])) {
                $suggestions[] = __('Consider converting to WebP format for better performance', 'oneclick-seo-pro');
            }
        }

        return $suggestions;
    }

    /**
     * Show WebP notice
     */
    public function show_webp_notice() {
        global $pagenow;

        if ($pagenow !== 'upload.php' || empty($this->settings['webp_notice'])) {
            return;
        }

        // Check if already dismissed
        if (get_user_meta(get_current_user_id(), 'oneclick_seo_webp_notice_dismissed', true)) {
            return;
        }

        // Count non-WebP images
        $non_webp_count = $this->count_non_webp_images();
        if ($non_webp_count < 10) {
            return;
        }

        ?>
        <div class="notice notice-info is-dismissible" id="oneclick-seo-webp-notice">
            <p>
                <strong><?php esc_html_e('Image SEO Tip:', 'oneclick-seo-pro'); ?></strong>
                <?php printf(
                    esc_html__('You have %d images that could be converted to WebP format for better performance and SEO.', 'oneclick-seo-pro'),
                    $non_webp_count
                ); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro-settings&tab=image-seo')); ?>">
                    <?php esc_html_e('Learn more', 'oneclick-seo-pro'); ?>
                </a>
            </p>
        </div>
        <script>
        jQuery(document).on('click', '#oneclick-seo-webp-notice .notice-dismiss', function() {
            jQuery.post(ajaxurl, {
                action: 'oneclick_seo_dismiss_webp_notice',
                nonce: '<?php echo esc_js(wp_create_nonce('oneclick_seo_dismiss_notice')); ?>'
            });
        });
        </script>
        <?php
    }

    /**
     * Count non-WebP images
     *
     * @return int Count
     */
    private function count_non_webp_images() {
        global $wpdb;

        return (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_type = 'attachment'
             AND post_mime_type LIKE 'image/%'
             AND post_mime_type != 'image/webp'"
        );
    }

    /**
     * Add bulk actions
     *
     * @param array $actions Bulk actions
     * @return array Modified actions
     */
    public function add_bulk_actions($actions) {
        $actions['oneclick_seo_optimize_alt'] = __('Optimize Alt Text (SEO)', 'oneclick-seo-pro');
        $actions['oneclick_seo_analyze_images'] = __('Analyze SEO Score', 'oneclick-seo-pro');
        return $actions;
    }

    /**
     * Handle bulk actions
     *
     * @param string $redirect_url Redirect URL
     * @param string $action Action name
     * @param array $post_ids Selected post IDs
     * @return string Modified redirect URL
     */
    public function handle_bulk_actions($redirect_url, $action, $post_ids) {
        if ($action === 'oneclick_seo_optimize_alt') {
            $optimized = 0;
            foreach ($post_ids as $post_id) {
                if (wp_attachment_is_image($post_id)) {
                    $alt = get_post_meta($post_id, '_wp_attachment_image_alt', true);
                    if (empty($alt)) {
                        $filename = pathinfo(get_attached_file($post_id), PATHINFO_FILENAME);
                        $generated_alt = $this->filename_to_text($filename);
                        update_post_meta($post_id, '_wp_attachment_image_alt', $generated_alt);
                        $optimized++;
                    }
                }
            }
            $redirect_url = add_query_arg('oneclick_seo_optimized', $optimized, $redirect_url);
        }

        if ($action === 'oneclick_seo_analyze_images') {
            $analyzed = 0;
            $total_score = 0;
            foreach ($post_ids as $post_id) {
                if (wp_attachment_is_image($post_id)) {
                    $score = $this->calculate_image_seo_score($post_id);
                    update_post_meta($post_id, '_oneclick_seo_image_score', $score);
                    $total_score += $score;
                    $analyzed++;
                }
            }
            $avg_score = $analyzed > 0 ? round($total_score / $analyzed) : 0;
            $redirect_url = add_query_arg([
                'oneclick_seo_analyzed' => $analyzed,
                'oneclick_seo_avg_score' => $avg_score,
            ], $redirect_url);
        }

        return $redirect_url;
    }

    /**
     * Bulk action admin notice
     */
    public function bulk_action_admin_notice() {
        if (!empty($_REQUEST['oneclick_seo_optimized'])) {
            $count = intval($_REQUEST['oneclick_seo_optimized']);
            printf(
                '<div class="notice notice-success is-dismissible"><p>' .
                _n('Optimized alt text for %d image.', 'Optimized alt text for %d images.', $count, 'oneclick-seo-pro') .
                '</p></div>',
                $count
            );
        }

        if (!empty($_REQUEST['oneclick_seo_analyzed'])) {
            $count = intval($_REQUEST['oneclick_seo_analyzed']);
            $avg = intval($_REQUEST['oneclick_seo_avg_score'] ?? 0);
            printf(
                '<div class="notice notice-success is-dismissible"><p>' .
                __('Analyzed %1$d images. Average SEO score: %2$d/100', 'oneclick-seo-pro') .
                '</p></div>',
                $count,
                $avg
            );
        }
    }

    /**
     * AJAX: Analyze images
     */
    public function ajax_analyze_images() {
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => __('Permission denied', 'oneclick-seo-pro')]);
        }

        global $wpdb;

        // Get images without scores
        $images = $wpdb->get_results(
            "SELECT p.ID FROM {$wpdb->posts} p
             LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_oneclick_seo_image_score'
             WHERE p.post_type = 'attachment'
             AND p.post_mime_type LIKE 'image/%'
             AND pm.meta_id IS NULL
             LIMIT 50"
        );

        $results = [];
        foreach ($images as $image) {
            $score = $this->calculate_image_seo_score($image->ID);
            update_post_meta($image->ID, '_oneclick_seo_image_score', $score);
            $results[] = [
                'id' => $image->ID,
                'score' => $score,
            ];
        }

        // Get overall stats
        $total_images = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%'"
        );

        $avg_score = (float) $wpdb->get_var(
            "SELECT AVG(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta}
             WHERE meta_key = '_oneclick_seo_image_score'"
        );

        $missing_alt = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts} p
             LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_attachment_image_alt'
             WHERE p.post_type = 'attachment'
             AND p.post_mime_type LIKE 'image/%'
             AND (pm.meta_value IS NULL OR pm.meta_value = '')"
        );

        wp_send_json_success([
            'analyzed' => count($results),
            'results' => $results,
            'stats' => [
                'total_images' => $total_images,
                'average_score' => round($avg_score),
                'missing_alt' => $missing_alt,
            ],
        ]);
    }

    /**
     * AJAX: Bulk optimize
     */
    public function ajax_bulk_optimize() {
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => __('Permission denied', 'oneclick-seo-pro')]);
        }

        $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'alt';
        $limit = isset($_POST['limit']) ? min(100, intval($_POST['limit'])) : 50;

        global $wpdb;

        $optimized = 0;

        if ($type === 'alt') {
            // Get images without alt text
            $images = $wpdb->get_results($wpdb->prepare(
                "SELECT p.ID FROM {$wpdb->posts} p
                 LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_attachment_image_alt'
                 WHERE p.post_type = 'attachment'
                 AND p.post_mime_type LIKE 'image/%%'
                 AND (pm.meta_value IS NULL OR pm.meta_value = '')
                 LIMIT %d",
                $limit
            ));

            foreach ($images as $image) {
                $filename = pathinfo(get_attached_file($image->ID), PATHINFO_FILENAME);
                $generated_alt = $this->filename_to_text($filename);
                update_post_meta($image->ID, '_wp_attachment_image_alt', $generated_alt);
                $optimized++;
            }
        }

        if ($type === 'title') {
            // Get images with filename as title
            $images = $wpdb->get_results($wpdb->prepare(
                "SELECT ID, post_title FROM {$wpdb->posts}
                 WHERE post_type = 'attachment'
                 AND post_mime_type LIKE 'image/%%'
                 LIMIT %d",
                $limit
            ));

            foreach ($images as $image) {
                $file_path = get_attached_file($image->ID);
                if (!$file_path) continue;

                $filename = pathinfo($file_path, PATHINFO_FILENAME);

                // Check if title matches filename
                if (strtolower($image->post_title) === strtolower($filename) ||
                    strtolower($image->post_title) === strtolower(str_replace(['-', '_'], ' ', $filename))) {

                    $new_title = $this->filename_to_text($filename);
                    wp_update_post([
                        'ID' => $image->ID,
                        'post_title' => $new_title,
                    ]);
                    $optimized++;
                }
            }
        }

        wp_send_json_success([
            'optimized' => $optimized,
            'type' => $type,
            'message' => sprintf(
                __('Optimized %d images', 'oneclick-seo-pro'),
                $optimized
            ),
        ]);
    }

    /**
     * AJAX: Generate alt text with AI
     */
    public function ajax_generate_alt() {
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => __('Permission denied', 'oneclick-seo-pro')]);
        }

        $attachment_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;

        if (!$attachment_id || !wp_attachment_is_image($attachment_id)) {
            wp_send_json_error(['message' => __('Invalid image', 'oneclick-seo-pro')]);
        }

        // Check if AI provider is configured
        if (!class_exists('OneClick_SEO_Pro_AI_Provider')) {
            require_once ONECLICK_SEO_PRO_DIR . 'includes/api/class-ai-provider.php';
        }

        $ai = new OneClick_SEO_Pro_AI_Provider();

        if (!$ai->is_configured()) {
            // Fallback to filename-based generation
            $filename = pathinfo(get_attached_file($attachment_id), PATHINFO_FILENAME);
            $generated_alt = $this->filename_to_text($filename);

            wp_send_json_success([
                'alt' => $generated_alt,
                'method' => 'filename',
                'message' => __('Generated from filename (AI not configured)', 'oneclick-seo-pro'),
            ]);
        }

        // Get image URL for AI analysis
        $image_url = wp_get_attachment_url($attachment_id);

        // Use AI to generate alt text
        $result = $ai->generate_image_alt($image_url);

        if (is_wp_error($result)) {
            // Fallback to filename
            $filename = pathinfo(get_attached_file($attachment_id), PATHINFO_FILENAME);
            $generated_alt = $this->filename_to_text($filename);

            wp_send_json_success([
                'alt' => $generated_alt,
                'method' => 'filename',
                'message' => __('Generated from filename (AI error)', 'oneclick-seo-pro'),
            ]);
        }

        wp_send_json_success([
            'alt' => $result,
            'method' => 'ai',
            'message' => __('Generated with AI', 'oneclick-seo-pro'),
        ]);
    }

    /**
     * Register REST routes
     */
    public function register_rest_routes() {
        register_rest_route('oneclick-seo-pro/v1', '/image-seo/stats', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_get_stats'],
            'permission_callback' => function() {
                return current_user_can('upload_files');
            },
        ]);

        register_rest_route('oneclick-seo-pro/v1', '/image-seo/analyze/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_analyze_image'],
            'permission_callback' => function() {
                return current_user_can('upload_files');
            },
        ]);
    }

    /**
     * REST: Get image SEO stats
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response Response
     */
    public function rest_get_stats($request) {
        global $wpdb;

        $total_images = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%'"
        );

        $with_alt = (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
             WHERE p.post_type = 'attachment'
             AND p.post_mime_type LIKE 'image/%'
             AND pm.meta_key = '_wp_attachment_image_alt'
             AND pm.meta_value != ''"
        );

        $avg_score = (float) $wpdb->get_var(
            "SELECT AVG(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta}
             WHERE meta_key = '_oneclick_seo_image_score'"
        );

        return rest_ensure_response([
            'total_images' => $total_images,
            'with_alt' => $with_alt,
            'without_alt' => $total_images - $with_alt,
            'average_score' => round($avg_score),
            'alt_coverage' => $total_images > 0 ? round(($with_alt / $total_images) * 100) : 0,
        ]);
    }

    /**
     * REST: Analyze single image
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response Response
     */
    public function rest_analyze_image($request) {
        $attachment_id = $request->get_param('id');

        if (!wp_attachment_is_image($attachment_id)) {
            return new WP_Error('invalid_image', __('Not a valid image', 'oneclick-seo-pro'), ['status' => 400]);
        }

        $score = $this->calculate_image_seo_score($attachment_id);
        $suggestions = $this->get_image_suggestions($attachment_id);

        update_post_meta($attachment_id, '_oneclick_seo_image_score', $score);

        return rest_ensure_response([
            'id' => $attachment_id,
            'score' => $score,
            'label' => $this->get_score_label($score),
            'suggestions' => $suggestions,
        ]);
    }
}
