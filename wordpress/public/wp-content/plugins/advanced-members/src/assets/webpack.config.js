const path = require( 'path' );
const FixStyleOnlyEntriesPlugin = require( 'webpack-fix-style-only-entries' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const CopyWebpackPlugin = require( 'copy-webpack-plugin' );

/**
 * WordPress Dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );

//Remove default CopyWebpackPlugin settings
defaultConfig.plugins = defaultConfig.plugins.filter((plugin) => {
  return !(plugin instanceof CopyWebpackPlugin);
});

module.exports = {
  ...defaultConfig,
  ...{
    entry: {
      'css/form': path.resolve( process.cwd(), 'src/assets/css', 'form.css' ),
      'css/admin': path.resolve( process.cwd(), 'src/assets/css', 'admin.css' ),
      'css/themes/default': path.resolve( process.cwd(), 'src/assets/css/themes', 'default.css' ),
      'css/themes/acf': path.resolve( process.cwd(), 'src/assets/css/themes', 'acf.css' ),
      'js/amem-admin': path.resolve( process.cwd(), 'src/assets/js', 'amem-admin.js' ),
      'js/amem-menus': path.resolve( process.cwd(), 'src/assets/js', 'amem-menus.js' ),
      'js/amem-input': path.resolve( process.cwd(), 'src/assets/js', 'amem-input.js' ),
      'js/forms': path.resolve( process.cwd(), 'src/assets/js', 'forms.js' ),
      'js/password-strength': path.resolve( process.cwd(), 'src/assets/js', 'password-strength.js' ),
      'js/multi-form-validation-hotfix': path.resolve( process.cwd(), 'src/assets/js', 'multi-form-validation-hotfix.js' ),
      'js/recaptcha-input': path.resolve( process.cwd(), 'src/assets/js', 'recaptcha-input.js' ),
      'js/recaptcha-admin': path.resolve( process.cwd(), 'src/assets/js', 'recaptcha-admin.js' ),
      'avatar/avatar': path.resolve( process.cwd(), 'src/assets/avatar', 'avatar.js' ),
    },
    output: {
      filename: '[name].js',
      path: path.resolve(process.cwd(), 'build/assets')
    },
  },
  plugins: [
    ...defaultConfig.plugins,
    new FixStyleOnlyEntriesPlugin(),
    new MiniCssExtractPlugin(),
    new CopyWebpackPlugin({
      patterns: [
        { from: path.resolve( process.cwd(), 'src/assets/images' ), to: "images" },
      ],
    })
  ],
}