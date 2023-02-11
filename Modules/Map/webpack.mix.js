const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

require('dotenv').config({
    path: __dirname + '/./../../' + process.env.ENV_FILE
});

mix.setPublicPath('../../public').mergeManifest();

mix.js(__dirname + '/Resources/assets/js/map.js', 'js/map.js')
    .js(__dirname + '/Resources/assets/js/map-fl.js', 'js/map-fl.js')
    .js(__dirname + '/Resources/assets/js/map-getfishing.js', 'js/map-getfishing.js')
;

