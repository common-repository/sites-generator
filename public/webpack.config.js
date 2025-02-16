const path = require('path');
// const HtmlWebpackPlugin = require('html-webpack-plugin');
require("@babel/register");

const config = {
  entry: ['@babel/polyfill','./js/sites-generator-admin.js'],
  output: {
    path: __dirname + '/dist',
    filename: 'bundle.js'
  },
  module: {
    rules : [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: ['babel-loader']
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader']
      }
    ]
  },
  // plugins: [
  //   new HtmlWebpackPlugin({
  //       hash: true
  //   })
  // ],
  resolve: {
    modules: [
      path.resolve('./js'),
      path.resolve('./node_modules')
    ]
  },
  devServer: {
    contentBase: __dirname + '/dist',
    compress: true,
    port: 1234,
    open: true,
    stats: {
        assets: false,
        children: false,
        chunks: false,
        chunkModules: false,
        colors: true,
        entrypoints: false,
        hash: false,
        modules: false,
        timings: false,
        version: false,
    }
  },
  watch: false,
  devtool: 'source-map',
};

module.exports = config;