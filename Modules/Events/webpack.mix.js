const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public').mergeManifest();

require('dotenv').config({
    path: __dirname + '/./../../' + process.env.ENV_FILE
});

mix
    .js(__dirname + '/Resources/assets/js/events.js', 'js/events.js')
    .js(__dirname + '/Resources/assets/js/submit.js', 'js/events-submit.js')
    .js(__dirname + '/Resources/assets/js/tickets.js', 'js/events-tickets.js')
    .js(__dirname + '/Resources/assets/js/checkout.js', 'js/events-checkout.js')
;
