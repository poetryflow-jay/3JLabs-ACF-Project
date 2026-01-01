/**
 * Update Live Customizer Setting
 *
 * @package Nexter
 * @since 1.0.0
 */
(function ($) {
  'use strict';

  var api = wp.customize;
  var nexter_preview = {
    init: function () {
      var $this = this,
        themeOption = 'nxt-theme-options';

      // Body Typography
      $this.responsiveSlider(
        themeOption + '[body-line-height]',
        'body, button, input, select,optgroup, textarea',
        'line-height'
      );
      $this.commonCss(
        themeOption + '[body-transform]',
        'body, button, input, select,optgroup, textarea',
        'text-transform'
      );
      $this.commonCss(themeOption + '[body-color]', 'body', 'color');
      $this.commonCss(
        themeOption + '[paragraph-mb]',
        'p, .entry-content p',
        'margin-bottom',
        'em'
      );
      $this.backgroundCss(themeOption + '[body-bgcolor]', 'body');
      $this.backgroundCss(
        themeOption + '[content-bgcolor]',
        '#content.site-content'
      );

      // Containers & Fluid Spacing
      [
        {
          opt: '[site-header-container-width]',
          sel1: '#nxt-header .nxt-container-block-editor > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-template-load):not(.nxt-alignfull):not(.nxt-content-page-template),#nxt-header .nxt-container-block-editor > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce), .nxt-breadcrumb-wrap .nxt-container-block-editor > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull),#nxt-header .nxt-container-block-editor .alignwide:not(.tpgb-container-row),#nxt-header .nxt-container-block-editor .alignwide.tpgb-nxtcont-type,#nxt-header .nxt-container',
          sel2: '#nxt-header .nxt-container-block-editor .tpgb-nxtcont-type'
        },
        {
          opt: '[site-footer-container-width]',
          sel1: '#nxt-footer .nxt-container-block-editor > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-template-load),#nxt-footer .nxt-container-block-editor > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce),#nxt-footer .nxt-container-block-editor .alignwide:not(.tpgb-container-row),#nxt-footer .nxt-container-block-editor .alignwide.tpgb-nxtcont-type,#nxt-footer .nxt-container',
          sel2: '#nxt-footer .nxt-container-block-editor .tpgb-nxtcont-type'
        },
        {
          opt: '[layout-container]',
          sel1: '.site-content .nxt-container-block-editor > .nxt-row article > .entry-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce),.nxt-container-block-editor .site-main > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull), .site-content > .nxt-container-block-editor > *:not(.content-area):not(.nxt-row):not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull),.site-content .nxt-container-block-editor .alignwide:not(.tpgb-container-row),.site-content .nxt-container-block-editor .alignwide.tpgb-nxtcont-type,.site-content .nxt-container',
          sel2: '.site-content .nxt-container-block-editor .tpgb-nxtcont-type'
        },
        {
          opt: '[layout-page-container]',
          sel1: '.site-content .nxt-page-cont.nxt-container-block-editor >.nxt-row article >.entry-content >*:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce),.site-content .nxt-page-cont.nxt-container-block-editor .alignwide:not(.tpgb-container-row),.site-content .nxt-page-cont.nxt-container-block-editor .alignwide.tpgb-nxtcont-type, .nxt-page-cont.nxt-container',
          sel2: '.site-content .nxt-page-cont.nxt-container-block-editor .tpgb-nxtcont-type'
        },
        {
          opt: '[layout-posts-container]',
          sel1: '.site-content > .nxt-post-cont.nxt-container-block-editor .site-main >*:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.nxt-blog-single-post), .site-content > .nxt-post-cont.nxt-container-block-editor >*:not(.content-area):not(.nxt-row):not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull),.site-content .nxt-post-cont.nxt-container-block-editor .alignwide:not(.tpgb-container-row),.site-content .nxt-post-cont.nxt-container-block-editor .alignwide.tpgb-nxtcont-type, .nxt-post-cont.nxt-container',
          sel2: '.site-content .nxt-post-cont.nxt-container-block-editor .tpgb-nxtcont-type'
        },
        {
          opt: '[layout-archive-container]',
          sel1: '.site-content >.nxt-container-block-editor.nxt-archive-cont >*:not(.content-area):not(.nxt-row):not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull), .nxt-container-block-editor.nxt-archive-cont .site-main >*:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull),.site-content .nxt-archive-cont.nxt-container-block-editor .alignwide:not(.tpgb-container-row),.site-content .nxt-archive-cont.nxt-container-block-editor .alignwide.tpgb-nxtcont-type, .site-content .nxt-archive-cont.nxt-container',
          sel2: '.site-content .nxt-archive-cont.nxt-container-block-editor .tpgb-nxtcont-type'
        }
      ].forEach(function (c) {
        $this.containerCss(themeOption + c.opt, c.sel1, 'max-width', '1');
        $this.containerCss(themeOption + c.opt, c.sel2, '--tpgb-container-sm', '2');
      });

      // WooCommerce Container
      $this.containerCss(themeOption + '[woo-container-width]', '.nxt-woocommerce .nxt-container', 'max-width');

      // Frame padding
      $this.responsiveDimension(themeOption + '[body-frame-padding]', 'body', 'padding', ['top', 'right', 'bottom', 'left']);
      ['top', 'bottom', 'left', 'right'].forEach(function (pos) {
        var prop = pos === 'top' || pos === 'bottom' ? 'height' : 'width';
        $this.responsiveDimension(themeOption + '[body-frame-padding]', '.nxt-body-frame.frame-' + pos, prop, [pos], true);
      });

      // Selection
      $this.commonCss(themeOption + '[selected-text-bg-color]', '::selection', 'background');
      $this.commonCss(themeOption + '[selected-text-color]', '::selection', 'color');

      // Headings (H1-H6)
      var headingStyle = {
        h1: 'h1, h1 a',
        h2: 'h2, h2 a',
        h3: 'h3, h3 a, .archive-post-title a',
        h4: 'h4, h4 a',
        h5: 'h5, h5 a',
        h6: 'h6, h6 a'
      };
      Object.keys(headingStyle).forEach(function (key) {
        $this.responsiveFontSize(themeOption + '[font-size-' + key + ']', headingStyle[key]);
        $this.responsiveSlider(themeOption + '[line-height-' + key + ']', headingStyle[key], 'line-height');
        $this.commonCss(themeOption + '[transform-' + key + ']', headingStyle[key], 'text-transform');
        $this.commonCss(themeOption + '[heading-color-' + key + ']', headingStyle[key], 'color');
      });

      // Single Post Title
      $this.responsiveFontSize(themeOption + '[font-size-s-blog-title]', '.single-post-title h1');
      $this.commonCss(themeOption + '[s-blog-title-line-height]', '.single-post-title h1', 'line-height');
      $this.commonCss(themeOption + '[s-blog-title-transform]', '.single-post-title h1', 'text-transform');
      $this.commonCss(themeOption + '[s-blog-title-color]', '.single-post-title h1', 'color');
      $this.commonCss(themeOption + '[s-blog-title-letter-spacing]', '.single-post-title h1', 'letter-spacing', 'px');

      // Post Meta
      $this.responsiveFontSize(themeOption + '[font-size-s-post-meta]', '.nxt-meta-info');
      $this.commonCss(themeOption + '[s-post-meta-line-height]', '.nxt-meta-info', 'line-height');
      $this.commonCss(themeOption + '[s-post-meta-transform]', '.nxt-meta-info', 'text-transform');
      $this.commonCss(themeOption + '[s-post-meta-color]', '.nxt-meta-info,.nxt-meta-info a', 'color');
      $this.commonCss(themeOption + '[s-post-meta-letter-spacing]', '.nxt-meta-info', 'letter-spacing', 'px');
    },

    // ------------ Utilities ------------
    removeStyleClass: function (option) {
      if (option) {
        option = option.replace('[', '-').replace(']', '');
        $('style.' + option).remove();
      }
    },

    addStyleClass: function (option, style, repeat = '') {
      if (!option || !style) return;
      option = option.replace('[', '-').replace(']', '');
      var cls = 'style.' + option + repeat;
      var el = $(cls);
      el.length ? el.html(style) : $('head').append('<style class="' + option + repeat + '">' + style + '</style>');
    },

    responsiveMedia: function (selector, css, maxWidth) {
      return css ? '@media (max-width:' + maxWidth + 'px){' + selector + '{' + css + '}}' : '';
    },

    responsiveFontSize: function (option, selector) {
      var $this = this;
      api(option, function (value) {
        var apply = function (val) {
          var style = '';
          if (val.desktop)
            style += selector + '{font-size:' + val.desktop + val['desktop-unit'] + '}';
          if (val.tablet)
            style += $this.responsiveMedia(selector, 'font-size:' + val.tablet + val['tablet-unit'], '1024');
          if (val.mobile)
            style += $this.responsiveMedia(selector, 'font-size:' + val.mobile + val['mobile-unit'], '767');
          $this.addStyleClass(option, style);
        };
        apply(value.get());
        value.bind(apply);
      });
    },

    responsiveSlider: function (option, selector, property, unit = '') {
      var $this = this;
      api(option, function (value) {
        var apply = function (val) {
          var style = '';
          if (val.desktop) style += selector + '{' + property + ':' + val.desktop + unit + '}';
          if (val.tablet)
            style += $this.responsiveMedia(selector, property + ':' + val.tablet + unit, '1024');
          if (val.mobile)
            style += $this.responsiveMedia(selector, property + ':' + val.mobile + unit, '767');
          $this.addStyleClass(option, style);
        };
        apply(value.get());
        value.bind(apply);
      });
    },

    responsiveDimension: function (option, selector, property, align, fixed_val = false, minus = '') {
      var $this = this;
      api(option, function (value) {
        var apply = function (val) {
          var deviceStyle = { md: '', sm: '', xs: '' };
          ['md', 'sm', 'xs'].forEach(function (d) {
            align.forEach(function (a) {
              if (val[d] && val[d][a]) {
                var prop = property + (fixed_val ? '' : '-' + a);
                deviceStyle[d] += prop + ':' + (minus === 'minus' ? '-' : '') + val[d][a] + val[d + '-unit'] + ';';
              }
            });
          });
          var style =
            (deviceStyle.md ? selector + '{' + deviceStyle.md + '}' : '') +
            $this.responsiveMedia(selector, deviceStyle.sm, '1024') +
            $this.responsiveMedia(selector, deviceStyle.xs, '767');
          $this.addStyleClass(option + '-' + property + '-' + align.join('-'), style);
        };
        apply(value.get());
        value.bind(apply);
      });
    },

    backgroundCss: function (option, selector) {
      var $this = this;
      api(option, function (value) {
        var apply = function (val) {
          var style = '',
            t = val['bg-type'] || '';
          if (t === 'color' && val['bg-color'])
            style = 'background-color:' + val['bg-color'] + ';background-image:none;';
          if (t === 'image' && val['bg-image']) {
            style = 'background-image:url(' + val['bg-image'] + ');';
            ['bg-size', 'bg-position', 'bg-repeat', 'bg-attachment'].forEach(function (p) {
              if (val[p]) style += p.replace('bg-', 'background-') + ':' + val[p] + ';';
            });
          }
          $this.addStyleClass(option, style ? selector + '{' + style + '}' : '');
        };
        apply(value.get());
        value.bind(apply);
      });
    },

    containerCss: function (option, selector, property, repeat = '') {
      var $this = this;
      api(option, function (value) {
        var apply = function (val) {
          var style = '';
          if (val.desktop)
            style += '@media(min-width:992px){' + selector + '{' + property + ':' + val.desktop + 'px}}';
          if (val.tablet)
            style += '@media(max-width:991px) and (min-width:577px){' + selector + '{' + property + ':' + val.tablet + 'px}}';
          if (val.mobile)
            style += '@media(max-width:576px){' + selector + '{' + property + ':' + val.mobile + 'px}}';
          $this.addStyleClass(option, style, repeat);
        };
        apply(value.get());
        value.bind(apply);
      });
    },

    commonCss: function (option, selector, property, unit) {
      var $this = this;
      api(option, function (value) {
        var apply = function (val) {
          if (!val) return $this.removeStyleClass(option);
          if (unit) val = unit === 'url' ? 'url(' + val + ')' : val + unit;
          $this.addStyleClass(option, selector + '{' + property + ':' + val + '}');
        };
        apply(value.get());
        value.bind(apply);
      });
    }
  };

  $(function () {
    nexter_preview.init();
  });
})(jQuery);
