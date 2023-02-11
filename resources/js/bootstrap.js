window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });

/**
 * Helper Functions
 */

window.get_cookie = function(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');

    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];

        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }

        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }

    return "";
};

window.set_cookie = function(cname, cvalue, expiry) {
    let date = new Date();
    date.setTime(date.getTime() + expiry);
    let expires = "expires=" + date.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
};

window.delete_cookie = function(cname) {
    window.set_cookie(cname, null, -1);
};

window.has_cookie = function(cname) {
    return window.get_cookie(cname) !== '';
};

window.for_each = function(items, callback) {
  Object.entries(items).forEach((item) => {
      let key = item[0];
      let value = item[1];

      if (callback.length === 1) {
          return callback(value);
      }

      if (callback.length === 2) {
          return callback(key, value);
      }

      return null;
  })
};

window.get_parameter = function(name) {
    let params = window.location.search.substr(1).split('&');
    let value = null;

    params.forEach((param) => {
        let parts = param.split('=');

        if (parts[0] === name) {
            value = parts[1];
        }
    });

    return value;
};

window.have_debugged = [];

window.debug = function(label, value) {
    let colors = [
        '#48a3d2',
        '#de7fe5',
        '#e37838',
        '#00ada9',
        '#b65d58',
        '#8e6fd2'
    ];

    if (!window.have_debugged.includes(label)) {
        window.have_debugged.push(label);
    }

    let index = window.have_debugged.indexOf(label) % 6;
    console.log(`%c${label}%c ${value}`, `background: ${colors[index]}; border-radius: 2px; padding: 1px 8px;`, '');
};

window.pad = function(number, size = 2) {
    let result = number + "";

    while (result.length < size) {
        result = "0" + result;
    }

    return result;
};

window.is_valid_date = function(date) {
    // Check Format
    if (!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(date)) {
        return false;
    }

    let parts = date.split("/");
    let day = parseInt(parts[0], 10);
    let month = parseInt(parts[1], 10);
    let year = parseInt(parts[2], 10);

    // Check Month/Year
    if (year < 1000 || year > 3000 || month == 0 || month > 12) {
        return false;
    }

    let month_length = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Leap Year Check
    if (year % 400 == 0 || (year % 100 != 0 && year % 4 == 0)) {
        month_length[1] = 29;
    }

    // Check Day
    return day > 0 && day <= month_length[month - 1];
};
