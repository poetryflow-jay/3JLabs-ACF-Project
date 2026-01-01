<?php
/**
 * Advanced Members intergrate ACF top title
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

global $title, $post_new_file, $post_type_object, $post;
$amem_post_type = is_object( $post_type_object ) ? $post_type_object->name : '';
$acf_title_placeholder = apply_filters( 'enter_title_here', __( 'Add title' ), $post );
$acf_title = $post->post_title;

$allowed_post_types = apply_filters( 'amem/admin/show_single_post_title', ['amem-form'] );
if ( !in_array( $amem_post_type, $allowed_post_types, true ) )
	return;
?>

<div id="amem-title-wrap" class="acf-title-wrap" style="display:none;">
	<label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo esc_html( $acf_title_placeholder ); ?></label>
	<input form="post" type="text" name="post_title" size="30" value="<?php echo esc_attr( $acf_title ); ?>" id="title" class="acf-headerbar-title-field" spellcheck="true" autocomplete="off" placeholder="<?php esc_attr_e( 'Title', 'acf' ); ?>" />
</div>
