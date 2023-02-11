const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public').mergeManifest();

require('dotenv').config({
    path: __dirname + '/./../../' + process.env.ENV_FILE
});

mix
    .js(__dirname + '/Resources/assets/js/donate.js', 'js/store-donate.js')
;
