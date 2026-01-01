<?php
/**
 * Advanced Members intergrate ACF top navigation
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

global $title, $post_new_file, $post_type_object, $post;
$acf_title_placeholder = apply_filters( 'enter_title_here', __( 'Add title' ), $post );
$acf_title             = $post->post_title;
$acf_post_type         = is_object( $post_type_object ) ? $post_type_object->name : '';
$acf_publish_btn_name  = 'save';
$acf_duplicated_from   = '';

if ( 'publish' !== $post->post_status ) {
	$acf_publish_btn_name = 'publish';
}

?>
<div class="acf-headerbar acf-headerbar-field-editor">
	<div class="acf-headerbar-inner">

		<div class="acf-headerbar-content">
			<h1 class="acf-page-title">
			<?php echo esc_html( $title ); ?>
			</h1>
		</div>

		<div class="acf-headerbar-actions" id="submitpost">
			<button form="post" class="acf-btn acf-publish" name="<?php echo esc_attr( $acf_publish_btn_name ); ?>" type="submit">
				<?php esc_html_e( 'Save Changes', 'advanced-members' ); ?>
			</button>
		</div>

	</div>
</div>
