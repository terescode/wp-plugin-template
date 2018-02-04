/* eslint-env node */

var ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
  entry: './admin/js/src/app.jsx',
  output: {
    path: './admin/js/',
    filename: 'bundle.js'
  },
  resolve: {
    extensions: ['', '.webpack.js', '.web.js', '.js', '.jsx']
  },
  module: {
    loaders: [
      {
        test: /\.js([x])?$/,
        exclude: /(node_modules)/,
        loader: 'babel-loader',
        query: {
          presets: ['react', 'es2015'],
          plugins: ['transform-object-rest-spread']
        }
      },
      {
        test: /\.css/,
        loader: ExtractTextPlugin.extract('css?modules&importLoaders=1&localIdentName=[name]__[local]___[hash:base64:5]&minimize')
      }
    ]
  },
  plugins: [
    new ExtractTextPlugin('../css/bundle.css')
  ]
};