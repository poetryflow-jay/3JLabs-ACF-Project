/**
 * Webpack Configuration for ACF CSS Manager
 * Phase 8.1: 빌드 시스템 구축
 */

const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

module.exports = (env, argv) => {
  const isProduction = argv.mode === 'production';
  const baseDir = path.resolve(__dirname, 'acf-css-really-simple-style-management-center-master');

  return {
    entry: {
      // Admin Center (Critical Path)
      'admin-center': path.join(baseDir, 'assets/js/jj-common-utils.js'),
      'admin-center': [
        path.join(baseDir, 'assets/js/jj-common-utils.js'),
        path.join(baseDir, 'assets/js/jj-admin-center.js')
      ],
      
      // Style Guide Editor
      'style-guide-editor': path.join(baseDir, 'assets/js/jj-style-guide-editor.js'),
      
      // Presets
      'style-guide-presets': path.join(baseDir, 'assets/js/jj-style-guide-presets.js'),
      
      // Labs Center
      'labs-center': path.join(baseDir, 'assets/js/jj-labs-center.js'),
      
      // Other scripts
      'keyboard-shortcuts': path.join(baseDir, 'assets/js/jj-keyboard-shortcuts.js'),
      'tooltips': path.join(baseDir, 'assets/js/jj-tooltips.js'),
      'live-preview': path.join(baseDir, 'assets/js/jj-live-preview.js'),
    },
    
    output: {
      path: path.join(baseDir, 'assets/js/bundled'),
      filename: isProduction ? '[name].min.js' : '[name].js',
      clean: false, // 기존 파일 유지
    },
    
    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env'],
            },
          },
        },
      ],
    },
    
    plugins: [
      new MiniCssExtractPlugin({
        filename: '../css/bundled/[name].min.css',
      }),
    ],
    
    optimization: {
      minimize: isProduction,
      minimizer: [
        new TerserPlugin({
          terserOptions: {
            compress: {
              drop_console: isProduction, // 프로덕션에서 console 제거
            },
          },
          extractComments: false,
        }),
        new CssMinimizerPlugin(),
      ],
      
      // 코드 스플리팅
      splitChunks: {
        chunks: 'all',
        cacheGroups: {
          vendor: {
            test: /[\\/]node_modules[\\/]/,
            name: 'vendors',
            priority: 10,
          },
          common: {
            name: 'common',
            minChunks: 2,
            priority: 5,
            reuseExistingChunk: true,
          },
        },
      },
    },
    
    resolve: {
      alias: {
        '@': path.resolve(baseDir, 'assets/js'),
      },
    },
    
    externals: {
      jquery: 'jQuery',
    },
    
    devtool: isProduction ? false : 'source-map',
    
    stats: {
      colors: true,
      modules: false,
      children: false,
      chunks: false,
      chunkModules: false,
    },
  };
};
