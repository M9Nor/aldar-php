const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public').mergeManifest();

mix.js(__dirname + '/Resources/assets/js/master.js', 'modules/cms/js/master.js')
    .sass( __dirname + '/Resources/assets/sass/master.scss', 'modules/cms/css/master.css');

mix.js(__dirname + '/Resources/assets/js/auth.js', 'modules/cms/js/auth.js')
    .sass( __dirname + '/Resources/assets/sass/auth.scss', 'modules/cms/css/auth.css');

if (mix.inProduction()) {
    mix.version();
}