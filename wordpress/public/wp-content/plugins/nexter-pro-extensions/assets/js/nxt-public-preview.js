var el = wp.element.createElement;
var __ = wp.i18n.__;
var registerPlugin = wp.plugins.registerPlugin;
var PluginPostStatusInfo = wp.editPost.PluginPostStatusInfo;
var buttonControl = wp.components.Button;

function nxtPublicPreviewButton({}) {
    return el(
        PluginPostStatusInfo,
        {
            className: 'nxt-public-preview-draft'
        },
        el(
            buttonControl,
            {
                variant: 'secondary',
                name: 'nxt_public_preview_link',
                isLink: true,
                title: nxt_pp_params.post_title,
                href : nxt_pp_params.public_preview_link
            }, nxt_pp_params.post_text
        )
    );
}

registerPlugin( 'nxt-public-preview-draft-plugin', {
    render: nxtPublicPreviewButton
} );