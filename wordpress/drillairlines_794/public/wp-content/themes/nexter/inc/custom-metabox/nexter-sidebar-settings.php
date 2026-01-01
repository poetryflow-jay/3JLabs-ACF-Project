<?php
/**
 * Meta Box Single sidebar Field
 * @return array
 */
add_action('add_meta_boxes', 'nexter_nxt_sidebar_settings_meta_box');
add_action('save_post', 'nexter_nxt_save_sidebar_settings');

/**
 * Add custom meta box for Sidebar Settings.
 */
function nexter_nxt_sidebar_settings_meta_box() {
    $set_priority = defined( 'ACF' ) ? 'normal' : 'side';
    add_meta_box(
        'nxt_sidebar_settings',
        esc_html__('Sidebar Settings', 'nexter'),
        'nexter_nxt_sidebar_settings_html',
        array('page', 'post'),
        $set_priority
    );
}

/**
 * HTML content for the meta box.
 *
 * @param WP_Post $post Current post object.
 */
function nexter_nxt_sidebar_settings_html($post) {
    $prefix = 'nxt-';
    
    // Retrieve existing meta values if they exist.
    $sidebar_option = get_post_meta($post->ID, $prefix . 'post-page-sidebar', true);
    $display_sidebar = get_post_meta($post->ID, $prefix . 'post-page-display-sidebar', true);
    $custom_sidebar = get_post_meta($post->ID, $prefix . 'post-page-custom-sidebar', true);

    wp_nonce_field('nexter_sidebar_settings_nonce', 'nexter_sidebar_settings_nonce_field');

    // Display Sidebar Field
    echo '<label for="nxt_post_page_sidebar">' . esc_html__('Display Sidebar', 'nexter') . '</label>';
   
	$options = array(
        'default' => array(
            'image' => NXT_THEME_URI . 'assets/images/customizer/sidebar/structure/default.png',
            'title' => esc_html__('Customizer Default', 'nexter'),
        ),
        'no-sidebar' => array(
            'image' => NXT_THEME_URI . 'assets/images/customizer/sidebar/structure/no-sidebar.png',
            'title' => esc_html__('No Sidebar', 'nexter'),
        ),
        'left-sidebar' => array(
            'image' => NXT_THEME_URI . 'assets/images/customizer/sidebar/structure/left-sidebar.png',
            'title' => esc_html__('Left Sidebar', 'nexter'),
        ),
        'right-sidebar' => array(
            'image' => NXT_THEME_URI . 'assets/images/customizer/sidebar/structure/right-sidebar.png',
            'title' => esc_html__('Right Sidebar', 'nexter'),
        ),
    );

	echo '<div class="nxt-sidebar-selection">';
		foreach ($options as $value => $data) {
			$is_selected = ($sidebar_option === $value) ? 'selected' : '';
			echo '<label class="nxt-sidebar-option ' . esc_attr($is_selected) . '">';
				echo '<input class="nxt-sidebar-radio" type="radio" name="' . esc_attr($prefix . 'post-page-sidebar') . '" value="' . esc_attr($value) . '" ' . checked($sidebar_option, $value, false) . '>';
				echo '<img src="' . esc_url($data['image']) . '" title="' . esc_attr($data['title']) . '" alt="' . esc_attr($data['title']) . '">';
			echo '</label>';
		}
    echo '</div>';

    global $pagenow;
    if ('widgets.php' !== $pagenow && 'customize.php' !== $pagenow) {
        // Display Sidebar Selection
        echo '<div class="nxt-page-post-sidebar">';
        echo '<label for="nxt_post_page_display_sidebar">' . esc_html__('Select Sidebar', 'nexter') . '</label>';
        echo '<select name="' . esc_attr($prefix . 'post-page-display-sidebar') . '" id="nxt_post_page_display_sidebar">';
        $sidebars = nexter_get_sidebar_list(); // Function to get list of sidebars
        foreach ($sidebars as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($display_sidebar, $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        echo '</div>';

        // Custom Sidebar Field (shown conditionally)
        echo '<div class="nxt-custom-sidebar">';
        echo '<label for="nxt_post_page_custom_sidebar">' . esc_html__('Custom Sidebar', 'nexter') . '</label>';
        echo '<select name="' . esc_attr($prefix . 'post-page-custom-sidebar') . '" id="nxt_post_page_custom_sidebar">';
        $custom_sidebars = nexter_builders_posts_list(); // Function to get list of custom sidebars
        foreach ($custom_sidebars as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($custom_sidebar, $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        echo '</div>';
    }
}

/**
 * Save the meta box data when the post is saved.
 *
 * @param int $post_id The post ID.
 */
function nexter_nxt_save_sidebar_settings($post_id) {
    // Verify the nonce before proceeding.
    if (!isset($_POST['nexter_sidebar_settings_nonce_field']) || !wp_verify_nonce($_POST['nexter_sidebar_settings_nonce_field'], 'nexter_sidebar_settings_nonce')) {
        return $post_id;
    }

    // Don't save during autosave or revision.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check if user has permission to save.
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    $prefix = 'nxt-';
    
    // Save 'Display Sidebar' field.
    if (isset($_POST[$prefix . 'post-page-sidebar'])) {
        update_post_meta($post_id, $prefix . 'post-page-sidebar', sanitize_text_field($_POST[$prefix . 'post-page-sidebar']));
    }

    // Save 'Display Sidebar Selection' field.
    if (isset($_POST[$prefix . 'post-page-display-sidebar'])) {
        update_post_meta($post_id, $prefix . 'post-page-display-sidebar', sanitize_text_field($_POST[$prefix . 'post-page-display-sidebar']));
    }

    // Save 'Custom Sidebar' field.
    if (isset($_POST[$prefix . 'post-page-custom-sidebar'])) {
        update_post_meta($post_id, $prefix . 'post-page-custom-sidebar', sanitize_text_field($_POST[$prefix . 'post-page-custom-sidebar']));
    }
}
