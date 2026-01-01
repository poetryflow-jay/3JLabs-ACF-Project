<div class="wrap">
    <h1 class="nxt-to-heading"><?php echo esc_html__('Taxonomy Term Order', 'nexter-pro-extensions'); ?></h1>
    <?php
        $parent_file = ($post_type === 'attachment') ? 'upload.php' : 'admin.php';
    ?>

    <form action="<?php echo esc_url($parent_file); ?>" method="get" id="nxt_taxonomy_order_form">
        <input type="hidden" name="page" value="nxt-terms-order" />
        <?php /* if (!in_array($post_type, ['post', 'attachment'])): ?>
            <input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>" />
        <?php endif; */ ?>

        <?php if (!empty(self::$taxonomy_opt['taxonomy'])):
            $taxonomies = self::$taxonomy_opt['taxonomy'];
            $class_name = (count($taxonomies) > 1) ? 'multiple-taxonomies' : 'single-taxonomy';
        ?>
            <div class="nxt-to-select-wrap <?php echo esc_attr($class_name); ?>">
                <label for="taxonomy-select"><?php echo esc_html__('Select Taxonomy', 'nexter-pro-extensions'); ?></label>
                <select name="taxonomy" id="taxonomy-select" onchange="nxt_taxonomy_order_change(this)">
                    <?php foreach ($taxonomies as $tax_slug): 
                        $tax_info = get_taxonomy($tax_slug);
                        if( !empty( $tax_info ) ){
                        $terms = get_terms(['hide_empty' => false, 'taxonomy' => $tax_slug]);
                    ?>
                        <option value="<?php echo esc_attr($tax_slug); ?>" <?php selected($tax_slug, $taxonomy); ?>>
                            <?php echo esc_html($tax_info->label); ?> (<?php echo esc_html($tax_info->name); ?>) - <?php echo esc_html(count($terms)); ?> <?php echo esc_html__('terms', 'nexter-pro-extensions'); ?>
                        </option>
                    <?php }
                    endforeach; ?>
                </select>
            </div>

            <div id="nxt-order-terms">
                <div id="post-body">
                    <ul class="sortable" id="wp_term_sortable">
                        <?php $instance->tto_terms_list($taxonomy); ?>
                    </ul>
                    <div class="clear"></div>
                </div>

                <div class="actions">
                    <p class="submit">
                        <a href="javascript:;" id="save-terms-order" class="save-order button-primary"><?php echo esc_html__('Update', 'nexter-pro-extensions'); ?></a>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </form>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        const $sortableLists = $("ul.sortable");

        $sortableLists.sortable({
            tolerance: 'intersect',
            cursor: 'pointer',
            items: '> li',
            toleranceElement: '> div',
            axis: 'y',
            placeholder: 'placeholder',
            nested: 'ul',
            opacity: 0.6,
            update: function(event, ui) {
                const $notice = $('#updating-order-notice');
                const $spinner = $('#spinner-img');
                const $check = $('.updating-order-notice .dashicons-saved');
                $notice.contents().filter(function() {
                    return this.nodeType === 3; // text node
                }).last().replaceWith('Updating Order');
                ui.item.find('.item:first').append($notice);
                $spinner.show();
                $check.hide();
                $notice.fadeIn();

                $('#save-terms-order').click();
            }
        });

        $(".save-order").on("click", function() {
            let sortedData = [];

            $sortableLists.each(function() {
                const serialized = $(this).sortable("serialize");
                const parentTag = $(this).parent().prop('tagName').toLowerCase();

                if (parentTag === 'li') {
                    const tagId = $(this).parent().attr('id');
                    sortedData[tagId] = serialized;
                } else {
                    sortedData[0] = serialized;
                }
            });

            const $notice = $('#updating-order-notice');
            const $spinner = $('#spinner-img');
            const $check = $('.updating-order-notice .dashicons-saved');

            const payload = JSON.stringify(nxt_array_to_obj_conv(sortedData));

            $.post(ajaxurl, {
                action: 'nxt-ext-taxonomy-save-order',
                order: payload,
                nonce: '<?php echo esc_html(wp_create_nonce("nxt-ext-taxonomy-save-order")); ?>'
            }, function(response) {
                $spinner.hide();
                $check.show();
                $notice.contents().filter(function() {
                    return this.nodeType === 3; // text node
                }).last().replaceWith('Updated Order');
                $notice.delay(1000).fadeOut();
            }).fail(function(err) {
                console.error(err);
            });
        });
    });
    </script>

    <div id="updating-order-notice" class="updating-order-notice" style="display:none;">
        <img src="<?php echo esc_url(NXT_PRO_EXT_URI . 'assets/images/processing.svg'); ?>" id="spinner-img" class="spinner-img" />
        <span class="dashicons dashicons-saved" style="display:none;"></span><?php echo esc_html__('Updating order...', 'nexter-pro-extensions'); ?>
    </div>
</div>