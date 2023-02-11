const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public').mergeManifest();

require('dotenv').config({
    path: __dirname + '/./../../' + process.env.ENV_FILE
});

mix
    .js(__dirname + '/Resources/assets/js/dashboard.js', 'js/members-dashboard.js')
    .js(__dirname + '/Resources/assets/js/fl-dashboard.js', 'js/members-fl-dashboard.js')
    .js(__dirname + '/Resources/assets/js/register.js', 'js/members-register.js')
    .js(__dirname + '/Resources/assets/js/register-continue.js', 'js/members-register-continue.js')
    .js(__dirname + '/Resources/assets/js/renew.js', 'js/members-renew.js')
    .js(__dirname + '/Resources/assets/js/admin-payment.js', 'js/members-admin-payment.js')
    .js(__dirname + '/Resources/assets/js/complete-registration-payment.js', 'js/members-complete-registration-payment.js')
;
