<?php 
/*
 * Media Replacement Extension
 * @since 4.2.1
 */
defined('ABSPATH') or die();

 class Nexter_Ext_Pro_Media_Replace {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this,'media_replace_scripts' ] );
		add_filter( 'media_row_actions', [ $this, 'modify_media_edit_link' ], 10, 2 );
		add_filter( 'attachment_fields_to_edit', [$this, 'render_media_replace_button'], 10, 2 );
        add_action( 'edit_attachment', [$this, 'edit_replace_media'] );
        add_filter( 'post_updated_messages', [$this, 'custom_attachment_message'] );

        // Cache bust filters
        add_filter('wp_calculate_image_srcset', [$this, 'cache_bust_srcset'], 10, 5);
        add_filter('wp_get_attachment_image_src', [$this, 'cache_bust_image_src'], 10, 2);
        add_filter('wp_prepare_attachment_for_js', [$this, 'cache_bust_js_image'], 10, 2);
        add_filter('wp_get_attachment_url', [$this, 'cache_bust_attachment_url'], 20, 2);
    }

    public function media_replace_scripts( $hook ) {
        $screen = get_current_screen();
    
        if ( ! empty( $screen ) && ( $screen->base === 'upload' || $screen->id === 'attachment' ) ) {
            wp_enqueue_style( 'nxt-media-replace', NXT_PRO_EXT_URI . 'assets/css/nxt-media-replace.css', [], NXT_PRO_EXT_VER );
            wp_enqueue_script( 'nxt-media-replace', NXT_PRO_EXT_URI . 'assets/js/nxt-media-replace.js', [], NXT_PRO_EXT_VER, false );
    
            $vars = [
                'select' => __( 'Select File to Replace', 'nexter-pro-extensions' ),
                'replace' => __( 'Perform Replacement', 'nexter-pro-extensions' ),
            ];
            wp_localize_script( 'nxt-media-replace', 'nxtMediaReplace', $vars );
        }
    }

	/**
     * Replace 'Edit' link in Media Library with a custom label.
     *
     * @param array   $actions Existing row actions.
     * @param WP_Post $post    Current media item.
     * @return array Modified actions.
     */
	public function modify_media_edit_link($actions, $post) {
        if (isset($actions['edit'])) {
            $actions['edit'] = sprintf(
                '<a href="%s" aria-label="%s">%s</a>',
                esc_url(get_edit_post_link($post)),
                esc_attr__('Edit or Replace', 'nexter-pro-extensions'),
                esc_html__('Edit or Replace', 'nexter-pro-extensions')
            );
        }

        return $actions;
    }

    public function render_media_replace_button( $fields, $post ) {
        global $pagenow, $typenow;
    
        // Bail out early if not on the correct post type or page
        if (
            $typenow !== 'attachment' &&
            ! in_array( $pagenow, [ 'post.php', 'post-new.php' ], true )
        ) {
            
            $attachment_id = $post->ID;
            $edit_url = get_edit_post_link($attachment_id);
            $link = "href=\"$edit_url\"";
            $fields["nxt-pop-media-replace"] = array(
                "label" => esc_html__("Replace Media", "nexter-pro-extensions"),
                "input" => "html",
                "html" => "<a class='button-secondary' $link>" . esc_html__("Upload a new file", "nexter-pro-extensions") . "</a>", "helps" => esc_html__("To replace the current file, click the link and upload a replacement file.", "nexter-pro-extensions")
            );
            return $fields;
        }
    
        if ( ! is_object( $post ) ) {
            return $fields;
        }
    
        $attachment_id = $post->ID;
        $mime_type = property_exists( $post, 'post_mime_type' ) ? $post->post_mime_type : '';
    
        wp_enqueue_media();
    
        ob_start();
        ?>
        <div id="nxt-media-replace-main" class="postbox attachment-id-<?php echo esc_attr( $attachment_id ); ?>" data-original-image-id="<?php echo esc_attr( $attachment_id ); ?>">
            <div class="postbox-header">
                <h2 class="hndle ui-sortable-handle"><?php esc_html_e( 'Media Replace', 'nexter-pro-extensions' ); ?></h2>
            </div>
            <div class="inside">
                <button
                    type="button"
                    id="nxt-media-replace-btn"
                    class="button-secondary button-large nxt-media-replace-button"
                    data-old-image-mime-type="<?php echo esc_attr( $mime_type ); ?>"
                    onclick="nexterReplaceMedia('<?php echo esc_js( $attachment_id ); ?>', '<?php echo esc_js( $mime_type ); ?>');"
                >
                    <?php esc_html_e( 'Select File to Replace', 'nexter-pro-extensions' ); ?>
                </button>
                <input type="hidden" id="new-attachment-id-<?php echo esc_attr( $attachment_id ); ?>" name="new-attachment-id-<?php echo esc_attr( $attachment_id ); ?>" />
                <div class="nxt-media-replace-desc">
                    <p>
                        <?php esc_html_e( 'This file will be replaced with a new one of the same type. The ID, publish date, and URL will remain unchanged.', 'nexter-pro-extensions' ); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
        $fields['nxt-media-replace'] = [
            'label' => '',
            'input' => 'html',
            'html'  => ob_get_clean(),
        ];

        return $fields;
    }
	

    public function edit_replace_media( $old_id ) {
        $post_key = 'new-attachment-id-' . $old_id;
    
        if ( empty( $_POST[ $post_key ] ) ) {
            return;
        }
    
        $new_id = intval( sanitize_text_field( wp_unslash($_POST[ $post_key ]) ) );
        $old_meta = get_post( $old_id, ARRAY_A );
        $new_meta = get_post( $new_id, ARRAY_A );
    
        $old_mime = $old_meta['post_mime_type'] ?? '';
        $new_mime = $new_meta['post_mime_type'] ?? '';
    
        if ( ! $new_id || ! is_numeric( $new_id ) || $old_mime !== $new_mime ) {
            return;
        }
    
        $new_data = wp_get_attachment_metadata( $new_id );
        $upload = wp_upload_dir();
    
        $new_path = array_key_exists( 'original_image', $new_data )
            ? wp_get_original_image_path( $new_id )
            : wp_normalize_path( trailingslashit( $upload['basedir'] ) . get_post_meta( $new_id, '_wp_attached_file', true ) );
    
        if ( ! $new_path || ! is_file( $new_path ) ) {
            return false;
        }
    
        $this->delete_media_files( $old_id );
    
        $old_path = array_key_exists( 'original_image', $new_data )
            ? wp_get_original_image_path( $old_id )
            : wp_normalize_path( trailingslashit( $upload['basedir'] ) . get_post_meta( $old_id, '_wp_attached_file', true ) );
    
        $old_dir = dirname( $old_path );

        if ( ! file_exists( $old_dir ) ) {
            mkdir( $old_dir, 0755, true );
        }
    
        copy( $new_path, $old_path );
    
        $new_meta_data = wp_generate_attachment_metadata( $old_id, $old_path );
        wp_update_attachment_metadata( $old_id, $new_meta_data );
        wp_delete_attachment( $new_id, true );
    
        $opt_key = 'nxt_ext_media_replace_list';
        $opt = get_option( $opt_key, [] );
        $replaced = $opt['replaced_media'] ?? [];
    
        if ( count( $replaced ) >= 5 ) {
            array_shift( $replaced );
        }
    
        $replaced[] = $old_id;
        $opt['replaced_media'] = array_unique( $replaced );
        update_option( $opt_key, $opt, true );
    
        sleep(2);
    }

    public function delete_media_files( $id ) {
        $meta = wp_get_attachment_metadata( $id );
        $file = get_attached_file( $id );
        $base = basename( $file );
    
        if ( ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
            foreach ( $meta['sizes'] as $size ) {
                $size_path = str_replace( $base, $size['file'], $file );
                wp_delete_file( $size_path );
            }
        }
    
        wp_delete_file( $file );
    
        $original = wp_get_original_image_path( $id );
        if ( $original ) {
            wp_delete_file( $original );
        }
    }

    /**
     * Customize updated message for media attachments.
     */
    public function custom_attachment_message( $msgs ) {
        foreach ( $msgs as $type => $arr ) {
            if ( $type === 'attachment' ) {
                $arr[4] = sprintf(
                        // Translators: %s is a link to an article explaining how to hard refresh a browser.
                        __('Media file replaced successfully. <a href="%s" target="_blank" rel="noopener noreferrer">Hard refresh</a> your browser to see the latest version.', 'nexter-pro-extensions'),
                        'https://fabricdigital.co.nz/blog/how-to-hard-refresh-your-browser-and-clear-cache'
                    );
            }
            $msgs[ $type ] = $arr;
        }
        return $msgs;
    }

    /**
     * Add cache-busting param to image srcset
     */
    public function cache_bust_srcset($srcs, $size_arr, $src, $meta, $id) {
        $opts = get_option('nxt_ext_media_replace_list', []);
        $recent = $opts['replaced_media'] ?? [];
        $mime = get_post_mime_type($id);

        if (in_array($id, $recent) && strpos($mime, 'image') !== false) {
            foreach ($srcs as $size => $s) {
                $s['url'] .= $this->add_timestamp($s['url']);
                $srcs[$size] = $s;
            }
        }

        return $srcs;
    }

    /**
     * Add cache-busting param to image src
     */
    public function cache_bust_image_src($img, $id) {
        $opts = get_option('nxt_ext_media_replace_list', []);
        $recent = $opts['replaced_media'] ?? [];
        $mime = get_post_mime_type($id);

        if (!empty($img[0]) && in_array($id, $recent) && strpos($mime, 'image') !== false) {
            $img[0] .= $this->add_timestamp($img[0]);
        }

        return $img;
    }

    /**
     * Add cache-busting param to JS attachment URLs
     */
    public function cache_bust_js_image($res, $att) {
        $opts = get_option('nxt_ext_media_replace_list', []);
        $recent = $opts['replaced_media'] ?? [];
        $mime = get_post_mime_type($att->ID);

        if (in_array($att->ID, $recent) && strpos($mime, 'image') !== false) {
            if (strpos($res['url'], '?') !== false) {
                $res['url'] .= $this->add_timestamp($res['url']);
            }

            if (isset($res['sizes'])) {
                foreach ($res['sizes'] as $name => $s) {
                    $res['sizes'][$name]['url'] .= $this->add_timestamp($s['url']);
                }
            }
        }

        return $res;
    }

    /**
     * Add cache-busting param to attachment URL
     */
    public function cache_bust_attachment_url($url, $id) {
        $opts = get_option('nxt_ext_media_replace_list', []);
        $recent = $opts['replaced_media'] ?? [];
        $mime = get_post_mime_type($id);

        if (in_array($id, $recent) && strpos($mime, 'image') !== false) {
            $url .= $this->add_timestamp($url);
        }

        return $url;
    }

    /**
     * Append ?t=timestamp if not already present
     */
    public function add_timestamp($url) {
        $parts = wp_parse_url($url);
        $param = '';

        if (!empty($parts['query'])) {
            parse_str($parts['query'], $q);
            if (!isset($q['t'])) {
                $param = (strpos($url, '?') === false ? '?' : '&') . 't=' . time();
            }
        } else {
            $param = (strpos($url, '?') === false ? '?' : '&') . 't=' . time();
        }

        return $param;
    }
}

 new Nexter_Ext_Pro_Media_Replace();