(function($) {

  if (typeof acf === 'undefined') {
    return;
  }

  /**
   * Field: reCaptcha
   */
  var reCAPTCHA = acf.Field.extend({

    type: 'amem_recaptcha',

    wait: 'load',

    widgetID: 0,

    containerV2: null,

    events: {
      'invalidField': 'onInvalidField'
    },

    $control: function() {
      return this.$('.acf-input-wrap');
    },

    $input: function() {
      return this.$('input[type="hidden"]');
    },

    initialize: function() {
      reCAPTCHA_API.load(this, this.render);
    },

    render: function() {

      if (this.get('version') === 'v2') {
        this.renderV2();

      } else if (this.get('version') === 'v3') {
        this.renderV3();
      }
    },

    getContainerV2: function() {
      if ( !$('#amem-recaptcha-v2-widget').length ) {
        this.containerV2 = $('<div>').attr('id', 'amem-recaptcha-v2-widget');
        this.$control().find('> div').append( this.containerV2 );
      }

      return this.containerV2;
    },

    renderV2: function() {
      // request
      this.widgetID = grecaptcha.render(this.getContainerV2()[0], {
        'sitekey': this.get('siteKey'),
        'theme': this.get('theme'),
        'size': this.get('size'),

        'callback': this.proxy(function(response) {
          acf.val(this.$input(), response, true);
          this.removeError();
        }),

        'error-callback': this.proxy(function() {
          // add custom error
          // this avoid multiple requests with onInvalidField() if keys are wrong
          this.$el.addClass('acf-error');

          this.showNotice({
            text: amemReCAPTCHA.l10n.error,
            type: 'error',
            dismiss: false
          });
        }),

        'expired-callback': this.proxy(function() {
          if ( this.get('size') == 'invisible' ) {
            this.reset();
            // grecaptcha.execute(this.widgetID);
            this.showError( amemReCAPTCHA.l10n.expired );
          }
        })
      });

      if ( this.get('size') == 'invisible' ) {
        grecaptcha.execute(this.widgetID);
      }

    },

    renderV3: function() {

      // vars
      var $input = this.$input();
      var sitekey = this.get('siteKey');
      var hideBadge = this.get('hideBadge');

      // request
      var request = function() {

        grecaptcha.execute(sitekey, {
          action: 'homepage'
        }).then(function(response) {
          acf.val($input, response, true);
        });

        // refresh every 90sec
        // this avoid an issue where token becomes invalid after 2min
        setTimeout(request, 90 * 1000);

      }

      grecaptcha.ready( function() {
        if ( hideBadge ) {
          $('.grecaptcha-badge').css({'visibility':'hidden'});
        }
        request();
      });

    },

    reset: function() {

      // reset v2
      if (this.get('version') === 'v2') {
        grecaptcha.reset(this.widgetID);
        acf.val(this.$input(), '', true);

        if ( this.get('size') == 'invisible' ) {
          this.containerV2.remove();
          this.renderV2();
        }

        // reset v3
      } else if (this.get('version') === 'v3') {
        this.renderV3();

      }

    },

    onInvalidField: function(e, $el) {
      this.reset();
    },

    validateReCAPTCHA: function(e) {
      return this.i(e.form).validate(e);
    },

    i: function (e) {
      var i = e.data("acf");
      return i || (i = new t(e)), i;
    }

  });

  acf.registerFieldType(reCAPTCHA);


  /**
   * reCAPTCHA_API
   *
   * @type {acf.Model}
   */
  var reCAPTCHA_API = new acf.Model({

    busy: false,

    load: function(field, callback) {

      // defaults
      callback = field.proxy(callback);

      // vars
      var url_v2 = 'https://www.google.com/recaptcha/api.js?render=explicit';
      var url_v3 = 'https://www.google.com/recaptcha/api.js?render=' + field.get('siteKey');
      var url = field.get('version') === 'v2' ? url_v2 : url_v3;

      // check if recaptcha exists
      if (typeof grecaptcha !== 'undefined' || acf.isset(window, 'grecaptcha')) {
        return callback();
      }

      acf.addAction('amem/recpatcha/loaded', callback);

      // already busy
      if (this.busy) {
        return;
      }

      // set busy
      this.busy = true;

      // load api
      $.ajax({
        url: url,
        dataType: 'script',
        cache: true,
        context: this,
        success: function() {
          grecaptcha.ready(this.proxy(function() {
            acf.doAction('amem/recpatcha/loaded');
            this.busy = false;
          }));
        }
      });

    }

  });

})(jQuery);