(function ($) {
  var reCaptcha = {
    timer: null,
    delay: 200,
    xhr: null,
    fields: null,
    cache: null,
    currentSiteKey: null,
    currentSecretKey: null,
    currentValidated: null,
    spinner: $('<span>').addClass('acf-spinner'),
    strings: amemReCaptchaAdmin.strings,
    formDisabled: false,
    init: function () {
      if ( typeof acf == 'undefined' || !$('#amem-recaptcha-site-key').length )
        return;

      this.fields = {
        version: acf.getField( $('#amem-recaptcha-version') ),
        type: acf.getField( $('#amem-recaptcha-type') ),
        size: acf.getField( $('#amem-recatpcha-size') ),
        siteKey: acf.getField( $('#amem-recaptcha-site-key') ),
        secretKey: acf.getField( $('#amem-recaptcha-secret-key') ),
        // threshold: acf.getField( $('#amem-recaptcha-score') ),
        // disableBadge: acf.getField( $('#amem-recaptcha-hide-badge') ),
        keysStatus: acf.getField( $('#amem-recaptcha-key-verified') ),
        save: $('.acf-headerbar-field-editor button.acf-publish')
      }

      if ( typeof this.fields.version != 'object' )
        return;

      if ( this.fields.version.val() == 'v3_ent' ) {
        this.verifyEnt();
      } else {
        this.verifyGen();
      }

      this.fields.siteKey.$el.find('.acf-input-wrap').css({'display':'flex', 'align-items': 'center'});
      this.fields.secretKey.$el.find('.acf-input-wrap').css({'display':'flex', 'align-items': 'center'});

      this.currentSiteKey = this.fields.siteKey.val().trim();
      this.currentSecretKey = this.fields.secretKey.val().trim();
      this.currentValidated = this.fields.keysStatus.val().trim();

      this.addEventListeners();
    },

    // verifyV2: function () {
    //   var verify = {
    //     fields: null,
    //     container: null,
    //     recaptcha: null,
    //     save: null,
    //     init: function () {
    //       this.cacheElements();
    //       this.addEventListeners();
    //     },
    //     cacheElements: function () {
    //       this.container = $('div[id="gform_setting_reset_v2"]');
    //       this.fields = {
    //         siteKey: $(reCaptcha.fields.siteKey),
    //         secretKey: $(reCaptcha.fields.secretKey),
    //         reset: $('input[name="_gform_setting_reset_v2"]'),
    //         type: $(reCaptcha.fields.v2Type)
    //       };
    //     },
    //     addEventListeners: function () {
    //       this.fields.siteKey.on('change', window.loadRecaptcha);
    //       this.fields.secretKey.on('change', window.loadRecaptcha);
    //       this.fields.type.on('change', function () {
    //         return window.loadRecaptcha();
    //       });
    //     },
    //     loadRecaptcha: function () {
    //       var loader = {
    //         init: function () {
    //           verify.recaptcha = $('#recaptcha');
    //           verify.save = $('#gform-settings-save');
    //           loader.flushExistingState();

    //           // Reset key status.
    //           // Note: recaptcha is misspelled here for legacy reasons.
    //           $('#recpatcha .gform-settings-field__feedback').remove();

    //           // If no public or private key is provided, exit.
    //           if (!loader.canBeDisplayed()) {
    //             loader.hideRecaptcha();
    //             return;
    //           }
    //           verify.save.prop('disabled', true);
    //           loader.showSelectedRecaptcha();
    //         },
    //         render: function (typeValue) {
    //           // Render reCAPTCHA.
    //           grecaptcha.render('recaptcha', {
    //             sitekey: verify.fields.siteKey.val().trim(),
    //             size: typeValue === 'invisible' ? typeValue : '',
    //             badge: 'inline',
    //             'error-callback': function errorCallback() {},
    //             callback: function callback() {
    //               return verify.save.prop('disabled', false);
    //             }
    //           });
    //         },
    //         flushExistingState: function () {
    //           window.___grecaptcha_cfg.clients = {};
    //           window.___grecaptcha_cfg.count = 0;
    //           verify.recaptcha.html('');
    //           verify.fields.reset.val('1');
    //         },
    //         canBeDisplayed: function () {
    //           return verify.fields.siteKey.val() && verify.fields.secretKey.val();
    //         },
    //         hideRecaptcha: function () {
    //           verify.save.prop('disabled', false);
    //           verify.container.hide();
    //         },
    //         showSelectedRecaptcha: function () {
    //           var typeValue = $('input[name="_gform_setting_type_v2"]:checked').val();
    //           loader.render(typeValue);
    //           switch (typeValue) {
    //             case 'checkbox':
    //               $('#gforms_checkbox_recaptcha_message, label[for="reset"]').show();
    //               break;
    //             case 'invisible':
    //               $('#gforms_checkbox_recaptcha_message, label[for="reset"]').hide();
    //               break;
    //             default:
    //               throw new Error('Unexpected type selected.');
    //           }
    //           verify.container.show();
    //           if (typeValue === 'invisible') {
    //             grecaptcha.execute();
    //           }
    //         }
    //       };

    //       loader.init();
    //     }  
    //   };

    //   verify.init();
    // },

    beforeValidation: function() {
      this.spinner.clone().appendTo( reCaptcha.fields.siteKey.$el.find('.acf-input-wrap') ).css( {'display': 'inline-block', 'margin-left': '10px'} );
      this.spinner.clone().appendTo( reCaptcha.fields.secretKey.$el.find('.acf-input-wrap') ).css( {'display': 'inline-block', 'margin-left': '10px'} );
    },

    afterValidation: function() {
      this.fields.siteKey.$el.find('.acf-spinner').remove();
      this.fields.secretKey.$el.find('.acf-spinner').remove();
      this.xhr = null;
    },

    addEventListeners: function(e) {
      $('#post').on('keyup keypress', function(e) {
        if ( !reCaptcha.formDisabled ) {
          return true;
        }
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
          e.preventDefault();
          return false;
        }
      });

      this.fields.version.on('change', function() {
        reCaptcha.fields.siteKey.val('');
        reCaptcha.fields.secretKey.val('');
        reCaptcha.fields.keysStatus.val('0');
      });

      this.fields.type.on('change', function() {
        reCaptcha.fields.siteKey.val('');
        reCaptcha.fields.secretKey.val('');
        reCaptcha.fields.keysStatus.val('0');
      });
    },

    disableSubmit: function() {
      this.fields.save.prop('disabled', true);
      this.formDisabled = true;
    },

    enableSubmit: function() {
      this.fields.save.prop('disabled', false);
      this.formDisabled = false;
    },

    verifyGen: function() {
      var verify = {
        fields: null,
        cache: null,
        token: null,
        string: null,
        isFirstLoad: true,
        scriptId: 'amem-recaptcha-api-script',
        init: function () {
          this.cleanup();
          // this.loadApi( reCaptcha.fields.siteKey.val().trim() );
          // this.validateKeys();
          this.addEventListeners();
        },

        cleanup: function() {
          $('#' + this.scriptId).remove();
          $('.grecaptcha-badge').remove();
          if (window.grecaptcha) {
            try {
              delete window.grecaptcha;
            } catch (e) {
              window.grecaptcha = undefined;
            }
          }
          reCaptcha.currentSiteKey = null;
          this.isLoading = false;
          this.loadPromise = null;
        },

        loadApi: function(siteKey) {
          if ( !this.isFirstLoad && reCaptcha.currentSiteKey === siteKey && !this.isLoading) {
            return Promise.resolve();
          }

          this.isFirstLoad = false;

          if (this.isLoading) {
            return this.loadPromise.then(() => this.loadApi(siteKey));
          }

          this.isLoading = true;
          this.cleanup();
          reCaptcha.currentSiteKey = siteKey;
          renderKey = reCaptcha.fields.version.val() == 'v2' ? 'explicit' : siteKey;

          this.loadPromise = new Promise((resolve, reject) => {
            $.ajax({
              url: `https://www.google.com/recaptcha/api.js?render=${renderKey}`,
              dataType: 'script',
              cache: true,
              scriptAttrs: {
                id: this.scriptId,
                async: true,
                defer: true
              }
            })
            .done(() => {
              this.isLoading = false;
              if (typeof grecaptcha === 'undefined' || typeof grecaptcha.ready !== 'function') {
                this.cleanup();
                reject( reCaptcha.strings.no_recaptcha );
                return;
              }
              grecaptcha.ready(() => {
                resolve();
              });
            })
            .fail((jqXHR, textStatus) => {
              this.isLoading = false;
              this.cleanup();
              reject( `${amemReCaptchaAdmin.api_load_failed} ${textStatus}.` );
            });
          });

          return this.loadPromise;
        },

        addEventListeners: function () {
          reCaptcha.fields.siteKey.$el.find('input').on('keyup change', function (e) {
            if ( reCaptcha.currentSiteKey == $(this).val().trim() ) {
              if ( reCaptcha.currentValidated > '0' ) {
                verify.reset( reCaptcha.fields.keysStatus );
                verify.reset( reCaptcha.fields.siteKey );
              } else {
                verify.setInvalid( reCaptcha.fields.keysStatus, reCaptcha.strings.needs_validation );
              }
              return;
            }
            verify.validateKeys(e);
          });
          reCaptcha.fields.secretKey.$el.find('input').on('keyup change', function (e) {
            if ( reCaptcha.currentSecretKey == $(this).val().trim() ) {
              if ( reCaptcha.currentValidated > '0' ) {
                verify.reset( reCaptcha.fields.keysStatus );
                verify.reset( reCaptcha.fields.secretKey );
              } else {
                verify.setInvalid( reCaptcha.fields.keysStatus, reCaptcha.strings.needs_validation );
              }
              return;
            }

            reCaptcha.currentSecretKey = $(this).val().trim();
            verify.validateKeys(e);
          });
        },

        validateKeys: function(e) {
          reCaptcha.disableSubmit();

          if (reCaptcha.timer) {
            clearTimeout(reCaptcha.timer);
            if (reCaptcha.xhr && typeof reCaptcha.xhr.abort == 'function') {
              reCaptcha.xhr.abort();
              reCaptcha.xhr = null;
            }
          }

          verify.resetAll();
          reCaptcha.afterValidation();

          var $el = $(e.currentTarget);

          if ( !$el.val().trim().length ) {
            field = $el.attr('id').indexOf('site_key') > 0 ? reCaptcha.fields.siteKey : reCaptcha.fields.secretKey;
            reCaptcha.fields.keysStatus.val('0');
            verify.setInvalid(field, reCaptcha.strings.empty_key);
            verify.setInvalid(reCaptcha.fields.keysStatus, reCaptcha.strings.needs_validation);

            reCaptcha.afterValidation();
            reCaptcha.enableSubmit();

            return;
          }

          // 새로운 타이머 설정
          reCaptcha.timer = setTimeout(() => {
            verify.doValidateKeys($el);
          }, reCaptcha.delay);

        },

        getRecaptchaToken: function () {
          return new Promise(function (resolve, reject) {
            try {
              var siteKeyValue = reCaptcha.fields.siteKey.val().trim();
              var version = reCaptcha.fields.version.val();
              var type = version == 'v2' ? reCaptcha.fields.type.val() : null;

              if (0 === siteKeyValue.length) {
                reCaptcha.fields.keysStatus.val('0');
                return;
              }

              verify.loadApi(siteKeyValue)
              .then(() => {
                grecaptcha.ready(function () {
                  try {
                    if ( version == 'v2' ) {
                      if ( typeof verify.widgetId != 'undefined' ) {
                        grecaptcha.reset(verify.widgetId);
                        $('#' + verify.widgetContainerId).remove();
                      }
                      // ready for container
                      // if (!verify.widgetContainerId) {
                        verify.widgetContainerId = 'amem-recaptcha-v2-widget';
                        if ( !$('#' + verify.widgetContainerId).length ) {
                          container = $('<div>').attr('id', verify.widgetContainerId)/*.css({display:'none'})*/[type != 'invisible' ? 'show' : 'hide']();
                          reCaptcha.fields.keysStatus.$el.append( container );
                        }
                      // }
                      // render widget
                      verify.widgetId = grecaptcha.render(verify.widgetContainerId, {
                        sitekey: siteKeyValue,
                        size: type == 'invisible' ? 'invisible' : reCaptcha.fields.size.val(),
                        callback: function(token) {
                          resolve(token);
                        },
                        'error-callback': function() {
                          reject(reCaptcha.strings.v2_sitekey_error);
                        }
                      });
                      if ( type == 'invisible' )
                        grecaptcha.execute(verify.widgetId);

                    } else if ( version == 'v2') {
                      reject('v2checkbox');
                    } else {
                      const getToken = grecaptcha.execute(siteKeyValue, {
                        action: 'homepage'
                      }).then(function (token) {
                        resolve(token);
                      });                      
                    }

                  } catch (error) {
                    console.log(error);
                    reject(error);
                  }
                });
              })

            } catch (error) {
              reject(error);
            }
          });
        },

        doValidateKeys: function ($el) {
          var siteKey = reCaptcha.fields.siteKey;
          var secretKey = reCaptcha.fields.secretKey;
          var keysStatus = reCaptcha.fields.keysStatus;

          reCaptcha.beforeValidation();

          if ( !reCaptcha.fields.siteKey.val().trim().length ) {
            // verify.unsetValid(siteKey);
            // verify.unsetValid(secretKey);
            keysStatus.val('0');
            verify.setInvalid(siteKey, reCaptcha.strings.empty_key);

            reCaptcha.afterValidation();
            reCaptcha.enableSubmit();

            return;
          }
          verify.getRecaptchaToken().then(function (token) {
            if ( siteKey.val().trim() != reCaptcha.currentSiteKey )
              verify.setValid( siteKey );
            verify.token = token;
          }).catch(function (error) {
            if (error === null) {
              verify.setInvalid(siteKey, reCaptcha.strings.invalid_request);
              keysStatus.val('0');
              verify.setInvalid(keysStatus, reCaptcha.strings.needs_validation);
            } else if (error === 'v2checkbox') {
              keysStatus.val('1');
              verify.setInvalid(keysStatus, reCaptcha.strings.checkbox_novalidation);
            } else if (error && error.message) {
              verify.setInvalid(siteKey, error.message);
              keysStatus.val('0');
              verify.setInvalid(keysStatus, reCaptcha.strings.needs_validation);
            } else {
              verify.setInvalid(siteKey, String(error));
              keysStatus.val('0');
              verify.setInvalid(keysStatus, reCaptcha.strings.needs_validation);
            }
          }).finally(function () {
            if ( !verify.token ) {
              reCaptcha.afterValidation();
              reCaptcha.enableSubmit();
              return;
            }
            reCaptcha.xhr = $.ajax({
              method: 'POST',
              dataType: 'JSON',
              url: amem_options.ajaxurl,
              data: {
                action: 'amem/recaptcha/key_verify',
                nonce: amemReCaptchaAdmin.nonce,
                token: verify.token,
                site_key: reCaptcha.fields.siteKey.val(),
                version: reCaptcha.fields.version.val(),
                secret_key: reCaptcha.fields.secretKey.val()
              },
              success: function(response) {
                verify.resetAll();
                if ( response.success ) {
                  verify.setValid( secretKey );
                  keysStatus.val('1');
                  verify.setValid( keysStatus, reCaptcha.strings.keys_verified );

                } else {
                  verify.setInvalid( secretKey, response.data );
                  keysStatus.val('0');
                  verify.setInvalid( keysStatus, reCaptcha.strings.needs_validation );
                }
              },
              error: function() {
                console.log('reCAPTCHA server request failed');

                reCaptcha.afterValidation();
                reCaptcha.enableSubmit();
              },
              complete: function() {
                verify.token = null;
                reCaptcha.afterValidation();
                reCaptcha.enableSubmit();
              }
            });

          });
        },
        reset: function (field) {
          field.$el.find('.acf-notice').remove();
          field.removeError();
        },
        resetAll: function() {
          this.reset( reCaptcha.fields.siteKey );
          this.reset( reCaptcha.fields.secretKey );
          // this.reset( reCaptcha.fields.keysStatus );
        },
        setValid: function (field, message) {
          this.reset(field);
          if ( !message )
            message = reCaptcha.strings.is_valid_key;
          var $msg = $('<span class="acf-notice -success acf-success-message">' + message + '</span>');

          field.$el.find('.acf-input').prepend($msg);
        },
        setInvalid: function (field, message) {
          this.reset(field);

          field.showError( message );
        }
      };

      verify.init();
    },

    verifyEnt: function() {
      // @todo: ready for v3 enterprise
    }

  }

  $(document).ready( function() {
    reCaptcha.init();
  });

})(jQuery);
