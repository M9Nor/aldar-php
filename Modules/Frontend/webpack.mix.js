const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

let WebpackRTLPlugin = require('webpack-rtl-plugin');

mix.setPublicPath('../../public').mergeManifest();

mix.sass( __dirname + '/Resources/assets/scss/styles.scss', 'css/frontend.min.css')
mix.js( __dirname + '/Resources/assets/js/script.js', 'js/frontend.min.js')
mix.js( __dirname + '/Resources/assets/js/jquery.sticky.js', 'js/jquery.sticky.min.js')
mix.js( __dirname + '/Resources/assets/js/inner.js', 'js/inner.min.js')
.options({ processCssUrls: false })
.webpackConfig({
    plugins: [
        new WebpackRTLPlugin()
    ],
    devtool: "inline-source-map"

});

if (mix.inProduction()) {
    mix.version();
}
