document.addEventListener('DOMContentLoaded', function () {
    // Move the Replace Media section
    const replaceDiv = document.getElementById('nxt-media-replace-main');
    const sidebar = document.getElementById('postbox-container-1');
    if (replaceDiv && sidebar) sidebar.appendChild(replaceDiv);
});

function nexterReplaceMedia(originalAttachmentId, oldImageMimeType) {
    if (!oldImageMimeType) {
        const fileTypeEl = document.querySelector('.details .file-type');
        const altFileTypeEl = document.querySelector('.details .filename:nth-child(2)');
        let oldImageMimeTypeFromDom = fileTypeEl?.innerHTML || altFileTypeEl?.innerHTML || '';
        oldImageMimeType = oldImageMimeTypeFromDom.replace(/<strong>(.*?)<\/strong>/, '').replace(/\s/g, '');
    }

    const mediaFrame = wp.media({
        title: nxtMediaReplace.select,
        button: {
            text: nxtMediaReplace.replace
        },
        multiple: false
    });

    const mediaFrameEl = mediaFrame.open().el;

    // Wait for media modal to render before querying
    const observer = new MutationObserver(() => {
        const uploadTab = mediaFrameEl.querySelector('#menu-item-upload');
        if (uploadTab) {
            uploadTab.click();
            observer.disconnect();
        }
    });
    observer.observe(mediaFrameEl, { childList: true, subtree: true });

    mediaFrameEl.addEventListener('click', function (e) {
        const attachmentItem = e.target.closest('li.attachment');
        if (!attachmentItem) return;

        const selected = mediaFrame.state().get('selection').first().toJSON();
        const selectedMime = selected.mime;
        const toolbar = mediaFrameEl.querySelector('.media-frame-toolbar .media-toolbar-primary');
        const warning = toolbar?.querySelector('.mime-type-warning');
        const selectBtn = toolbar?.querySelector('.media-button-select');

        if (selectedMime !== oldImageMimeType) {
            if (!warning) {
                const warnDiv = document.createElement('div');
                warnDiv.className = 'mime-type-warning';
                warnDiv.textContent = 'The selected image is of a different type than the image to replace. Please choose an image with the same type.';
                toolbar.prepend(warnDiv);
            }
            if (selectBtn) selectBtn.disabled = true;
        } else {
            if (warning) warning.remove();
            if (selectBtn) selectBtn.disabled = false;
        }
    });

    document.querySelector('.supports-drag-drop:not(.upload.php)')?.addEventListener('drop', () => {
        const uploader = document.querySelector('.uploader-window');
        if (uploader) uploader.style.display = 'none';
    });

    mediaFrame.on('select', () => {
        const attachment = mediaFrame.state().get('selection').first().toJSON();
        const newImageMimeType = attachment.mime;

        if (oldImageMimeType === newImageMimeType) {
            const input = document.getElementById('new-attachment-id-' + originalAttachmentId);
            if (input) {
                input.value = attachment.id;
                const form = input.closest('form');
                const isModal = input.closest('.media-modal');
                if (!isModal && form) {
                    form.submit();
                    mediaFrame.close();
                }
            }
        }
    });
}