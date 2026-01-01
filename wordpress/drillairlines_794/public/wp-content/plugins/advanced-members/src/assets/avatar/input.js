/*!
 * Powered by https://github.com/joppuyo/acf-image-aspect-ratio-crop
 */

import Cropper from 'cropperjs';
import axios from 'axios';
import qs from 'qs';
import { sprintf } from 'sprintf-js';
// import { Dropzone } from "dropzone";

(function($) {
  var field = null;

  acf.fields.amem_avatar = acf.field.extend({
    type: 'amem_avatar',
    $el: null,
    $input: null,
    $img: null,
    $field: null,
    ajaxurl: amemAvatar.ajaxurl,
    isFirstCrop: null,
    fileData: null,
    acfKey: null,

    actions: {
      ready: 'initialize',
      append: 'initialize',
    },

    events: {
      'click a[data-name="remove"]': 'remove',
      'change .js-amem-avatar-upload': 'upload_basic',
    },

    upload_basic: function(event) {
      let uploadElement = event.currentTarget;
      let files = uploadElement.files;
      let formData = new FormData();

      var self = this;

      this.isFirstCrop = true;

      if ( !files.length ) {
        return;
      }

      Array.from(Array(files.length).keys()).map(index => {
        formData.append('image', files[index], files[index].name);
        formData.append('key', this.acfKey);
        formData.append('preview_size', this.$el.data('preview_size'));
        formData.append('uid', this.$el.data('uid'));
      });

      uploadElement.value = '';

      let settings = {
        onUploadProgress: progressEvent => {
          let percentCompleted = Math.round(
            (progressEvent.loaded * 100) / progressEvent.total,
          );

          this.$el.find('.js-amem-avatar-upload-progress')
          .html( 
            sprintf(
              amemAvatar.l10n.upload_progress,
              percentCompleted,
            ),
          );
        },
        headers: {
          'X-AMem-Avatar-Nonce': amemAvatar.nonce,
          'X-WP-Nonce': amemAvatar.rest_nonce,
        },
      };

      $(this.$el).find('.js-amem-avatar-upload').hide();
      $(this.$el).find('.js-amem-avatar-upload-progress').show();

      axios.post(`${amemAvatar.api_root}/upload`, formData, settings)
      .then(response => {
        let attachment = new window.Backbone.Model(response.data);
        this.render(attachment);

        $(this.$el).find('.js-amem-avatar-upload-progress').hide();
        $(this.$el).find('.js-amem-avatar-upload').show();

        $('.js-amem-avatar-crop').data('file-data', JSON.stringify(response.data) );
        this.fileData = response.data;

        let popup = new window.Backbone.Model(response.data);

        this.render(popup);
        this.openModal({ attachment: popup, field: this.$field });
      })
      .catch(error => {
        $(this.$el).find('.js-amem-avatar-upload-progress').hide();

        $(this.$el).find('.js-amem-avatar-upload').show();

        let errorMessage = amemAvatar.l10n.upload_failed;

        if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          errorMessage = error.response.data.message;
        }
        console.log(error);
        window.alert(errorMessage);
      });
    },

    focus: function() {
      // vars
      this.$el = this.$field.find('.amem-image-avatar-uploader');
      this.$input = this.$el.find('input[type="hidden"]');
      this.$img = this.$el.find('img');

      // options
      this.o = acf.get_data(this.$el);
    },

    initialize: function() {
      this.isFirstCrop = null;
      var self = this;

      // add attribute to form
      if (this.o.uploader == 'basic') {
        this.$el.closest('form').attr('enctype', 'multipart/form-data');
      }

      this.escapeHandlerBound = this.escapeHandler.bind(this);

      $(document).on('click', '.js-amem-avatar-cancel', () => this.cancelCrop(), );

      $(document).off('click', '.js-amem-avatar-reset')
      .on('click', '.js-amem-avatar-reset', () => {
        this.cropper.reset();
      });

      $(document) .off('click', '.js-amem-avatar-crop')
      .on('click', '.js-amem-avatar-crop', function() {
        var cropData = self.cropper.getData(true);

        $('.js-amem-avatar-modal').css(
          'max-width',
          self.cropper.containerData.width,
        );

        let acfKey = $(field).data('key');

        var data = {
          id: $(this).data('id'),
          ratioH: amemAvatar.ratioH,
          ratioW: amemAvatar.ratioW,
          cropType: amemAvatar.cropType,
          data: $(this).data('file-data'),
          previewSize: $(this).data('preview-size'),
          x: cropData.x,
          y: cropData.y,
          width: cropData.width,
          height: cropData.height,
          temp_user_id: amemAvatar.temp_user_id,
          key: acfKey,
        };

        $('.js-amem-avatar-crop').prop('disabled', true);
        $('.js-amem-avatar-reset').prop('disabled', true);

        // prettier-ignore
        var loading = '<div class="amem-avatar-modal-loading">' +
          '<div class="amem-avatar-modal-loading-icon">' +
          '<!-- Icon from https://github.com/google/material-design-icons -->' +
          '<!-- Licensed under Apache License 2.0 -->' +
          '<!-- Copyright (c) Google Inc. -->' +
          '<svg width="14" height="14" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg"><path d="M7 2.64V1L4.75 3.18 7 5.36V3.73A3.33 3.33 0 0 1 10.38 7c0 .55-.15 1.07-.4 1.53l.82.8c.44-.68.7-1.47.7-2.33A4.43 4.43 0 0 0 7 2.64zm0 7.63A3.33 3.33 0 0 1 3.62 7c0-.55.15-1.07.4-1.53l-.82-.8c-.44.68-.7 1.47-.7 2.33A4.43 4.43 0 0 0 7 11.36V13l2.25-2.18L7 8.64v1.63z" fill="#FFF" fill-rule="nonzero"/></svg>' +
          '</div>' +
          '<div class="amem-avatar-modal-loading-text">' +
          amemAvatar.l10n.cropping_in_progress +
          '</div>' +
        '</div>';

        // prettier-ignore
        var error = '<div class="amem-avatar-modal-error">' +
          '<div class="amem-avatar-modal-error-icon">' +
          '<!-- Icon from https://github.com/google/material-design-icons -->' +
          '<!-- Licensed under Apache License 2.0 -->' +
          '<!-- Copyright (c) Google Inc. -->' +
          '<svg width="22" height="22" viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg"><path d="M1 20.14h20l-10-17-10 17zm10.9-2.69h-1.8v-1.79h1.8v1.8zm0-3.58h-1.8V10.3h1.8v3.58z" fill="#F44336" fill-rule="nonzero"/></svg>' +
          '</div>' +
          '<div class="amem-avatar-modal-error-text">' +
          amemAvatar.l10n.cropping_failed +
          '</div>' +
        '</div>';

        $('.js-amem-avatar-modal-footer-status').empty();
        $('.js-amem-avatar-modal-footer-status').html(
          loading,
        );
        self.cropper.disable();

        let options = {};

        let url = null;

        if (amemAvatar.rest_api_compat === '1') {
          url = self.ajaxurl;
          data = qs.stringify({
            action: 'amem_avatar_crop',
            data: JSON.stringify(data),
            _wpnonce: amemAvatar.nonce
          });
        } else {
          url = `${amemAvatar.api_root}/crop`;
          options = {
            headers: {
              'X-AMem-Avatar-Nonce': amemAvatar.nonce,
              'X-WP-Nonce': amemAvatar.rest_nonce,
            },
          };
        }

        axios.post(url, data, options)
        .then(response => {
          self.cropComplete(response.data);
          $('.js-amem-avatar-crop').prop('disabled', false);
          $('.js-amem-avatar-reset').prop('disabled', false);
          $('.js-amem-avatar-modal-footer-status').empty();
        })
        .catch(response => {
          console.error(response);
          self.cropper.enable();
          $('.js-amem-avatar-crop').prop('disabled', false);
          $('.js-amem-avatar-reset').prop('disabled', false);
          $('.js-amem-avatar-modal-footer-status').empty();
          $('.js-amem-avatar-modal-footer-status').html( error );
        });
      });
    },

    prepare: function(attachment) {
      // defaults
      attachment = attachment || {};

      // bail ealry if already valid
      if (attachment._valid) return attachment;

      // vars
      var data = {
        url: '',
        preview_url: '',
        alt: '',
        title: '',
        caption: '',
        description: '',
        width: 0,
        height: 0,
      };

      // wp image
      if (attachment.id) {
        // update data
        data = attachment.attributes;
      }

      // valid
      data._valid = true;

      // return
      return data;
    },

    render: function(data) {
      data = this.prepare(data);

      // update image
      this.$img.attr({ src: data.preview_url, alt: data.alt, title: data.title });

      // vars
      var val = '';

      // WP attachment
      if (data.id) {
        val = data.id;
      }

      // update val
      acf.val(this.$input, val);

      // update class
      if (val) {
        this.$el.addClass('has-value');
      } else {
        this.$el.removeClass('has-value');
      }
    },

    remove: function() {
      // Remove all data attributes from the previous image
      this.$field
        .find('.amem-image-avatar-uploader')
        .data('original-image-id', null)
        .attr('data-original-image-id', null);

      // vars
      var attachment = {};

      // add file to field
      this.render(attachment);
    },

    escapeHandler: function(event) {
      if (event.key === 'Escape') {
        this.cancelCrop();
      }
    },

    openModal: function(data) {

      var url = data.attachment.attributes.url;
      var id = data.attachment.attributes.id;
      field = data.field;

      this.fileData = data.attachment.attributes;
      var fileDataAtt = encodeURIComponent( JSON.stringify(this.fileData) );

      document.addEventListener('keydown', this.escapeHandlerBound);

      var cropType = amemAvatar.cropType,
      minWidth = this.$el.data('min_width'),
      minHeight = minWidth,
      maxWidth = this.$el.data('max_width'),
      maxHeight = maxWidth;

      var options = {
        aspectRatio: amemAvatar.ratioW / amemAvatar.ratioH,
        viewMode: 1,
        autoCropArea: 1,
        zoomable: false,
        checkCrossOrigin: false,
        checkOrientation: false,
        responsive: true,
      };

      if ( amemAvatar.cropType === 'aspect_ratio' && minHeight !== 0 && minWidth !== 0 ) {
        // options.crop = function(event) {
        //   let width = event.detail.width;
        //   let height = event.detail.height;
        //   if (width < minWidth || height < minHeight) {
        //     this.cropper.setData({
        //       width: minWidth,
        //       height: minHeight,
        //     });
        //   }
        // };

        options.crop = function (event) {
          var width = Math.round(event.detail.width);
          var height = Math.round(event.detail.height);

          if (
            width < minWidth
            || height < minHeight
            // || width > maxWidth
            // || height > maxHeight
          ) {
            this.cropper.setData({
              width: Math.max(minWidth, Math.min(maxWidth, width)),
              height: Math.max(minHeight, Math.min(maxHeight, height)),
            });
          }

          // data.textContent = JSON.stringify(this.cropper.getData(true));
        };
      }

      // prettier-ignore
      $('body').append(`
<div class="amem-avatar-backdrop">
  <div class="amem-avatar-modal-wrapper">
    <div
      class="amem-avatar-modal js-amem-avatar-modal"
    >
      <div class="amem-avatar-modal-heading">
        <div class="amem-avatar-modal-heading-text">
          ${amemAvatar.l10n.modal_title}
        </div>
        <button
          class="amem-avatar-modal-heading-close js-amem-avatar-cancel"
          aria-label="Close"
        >
          ${require('!raw-loader!./close.svg').default}
        </button>
      </div>
      <div class="amem-avatar-modal-image-container">
        <img
          class="amem-avatar-modal-image js-amem-avatar-modal-image"
          src="${url}"
        />
      </div>

      <div class="amem-avatar-modal-footer">
        <div
          class="amem-avatar-modal-footer-status js-amem-avatar-modal-footer-status"
        ></div>
        <div class="amem-avatar-modal-footer-buttons">
          <button
            class="amem-avatar-button amem-avatar-button-link amem-avatar-reset js-amem-avatar-reset"
          >
            ${require('!raw-loader!./reset.svg').default}
            ${amemAvatar.l10n.reset}
          </button>
          <button class="amem-avatar-button amem-avatar-button-default js-amem-avatar-cancel">
            ${amemAvatar.l10n.cancel}
          </button>
          <button
            class="amem-avatar-button amem-avatar-button-primary js-amem-avatar-crop"
            data-id="${id}"
            data-ratio-h="${amemAvatar.ratioH}"
            data-ratio-w="${amemAvatar.ratioW}"
            data-crop-type="${amemAvatar.cropType}"
            data-file-data="${fileDataAtt}"
          >
            ${amemAvatar.l10n.crop}
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
`);

      this.cropper = new Cropper(
        $('.js-amem-avatar-modal-image')[0],
        options,
      );

      // Test helper
      window._amem_avatar_cropper = this.cropper;
    },

    cropComplete: function(data) {
      // Cropping successful, change image to cropped version
      this.cropper.destroy();

      this.$field.find('input.amem-avatar').val(data.id);

      let attachment = new window.Backbone.Model(data);

      this.render(attachment);
      this.isFirstCrop = false;
      this.closeModal();

      // acf.getField( this.$field ).showNotice({
      //   text: amemAvatar.l10n.updated,
      //   type: 'success',       // warning, error, success
      //   dismiss: true,  // allow notice to be dismissed
      // });
    },

    closeModal: function() {
      if (this.isFirstCrop) {
        acf.val(this.$input, '');
        this.render({});
      }
      $('.amem-avatar-backdrop').remove();
      document.removeEventListener('keydown', this.escapeHandlerBound);
      this.cropper.destroy();
    },

    cancelCrop: function() {
      let url = `${amemAvatar.api_root}/cancelCrop`;
      let options = {
        headers: {
          'X-AMem-Avatar-Nonce': amemAvatar.nonce,
          'X-WP-Nonce': amemAvatar.rest_nonce,
        },
      };
      let data = {
        file: this.fileData.file ? this.fileData.file : ''
      };
      var self = this;

      axios.post(url, data, options)
      .then(response => {
        self.closeModal();
      })
      .catch(response => {
        console.error(response);
        self.closeModal();
      });
    }
  });


})(jQuery);
