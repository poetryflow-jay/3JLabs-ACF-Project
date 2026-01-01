/**
 * Minyfy all JS/CSS
 */

const minify = require('@node-minify/core');
// const babelMinify = require('@node-minify/babel-minify');
// const yui = require('@node-minify/yui');
const cleanCSS = require('@node-minify/clean-css');
const gcc = require("@node-minify/google-closure-compiler");

/** @js */
minify({
  compressor: gcc,
  type: "js",
  input: "build/assets/js/forms.js",
  output: "build/assets/js/forms.min.js",
  callback: function(err, min) {}
});
minify({
  compressor: gcc,
  type: "js",
  input: "build/assets/js/amem-admin.js",
  output: "build/assets/js/amem-admin.min.js",
  callback: function(err, min) {}
});
minify({
  compressor: gcc,
  type: "js",
  input: "build/assets/js/amem-input.js",
  output: "build/assets/js/amem-input.min.js",
  callback: function(err, min) {}
});
minify({
  compressor: gcc,
  type: "js",
  input: "build/assets/js/multi-form-validation-hotfix.js",
  output: "build/assets/js/multi-form-validation-hotfix.min.js",
  callback: function(err, min) {}
});
minify({
  compressor: gcc,
  type: "js",
  input: "build/assets/js/password-strength.js",
  output: "build/assets/js/password-strength.min.js",
  callback: function(err, min) {}
});


/** @css */
minify({
  compressor: cleanCSS,
  input: "build/assets/css/form.css",
  output: "build/assets/css/form.min.css",
  callback: (err, min) => {
    // console.log("cleancss concat");
    // console.log(err);
    // //console.log(min);
  }/*,
  options: {
    sourceMap: {
      filename: "public/css-dist/cleancss-concat.map",
      url: "public/css-dist/cleancss-concat.map",
    },
  },*/
});
minify({
  compressor: cleanCSS,
  input: "build/assets/css/admin.css",
  output: "build/assets/css/admin.min.css",
  callback: (err, min) => {}
});
minify({
  compressor: cleanCSS,
  input: "build/assets/css/themes/default.css",
  output: "build/assets/css/themes/default.min.css",
  callback: (err, min) => {}
});