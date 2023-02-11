
require('./bootstrap');

import Vue from 'vue';
import Vuex from 'vuex';
import Swal from 'sweetalert2';
import VueScrollTo from 'vue-scrollto';
import fields from './includes/fields';
import toast from './includes/toast';
import moment from "moment";
import 'moment-timezone';
import VueTheMask from 'vue-the-mask';
import 'select2/dist/css/select2.min.css';
import VTooltip from 'v-tooltip';
import VueRecaptcha from 'vue-recaptcha';

let laravel_axios = axios.create();
laravel_axios.defaults.baseURL = window.laravel_url;

let token = get_cookie('PASSPORT_ACCESS_TOKEN');

if (token) {
    laravel_axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

let wp_axios = axios.create();
wp_axios.defaults.baseURL = window.wp_url;

Object.defineProperty(Vue.prototype, '$axios', {
    get: function get() {
        return axios;
    }
});

Vuex.Store.prototype.$laravel_api = laravel_axios

Object.defineProperty(Vue.prototype, '$recaptcha_key', {
    get: function get() {
        return '6LfnSXohAAAAAMqhT17aPY40_-fI0b523e1FP9s0';
    }
});

Object.defineProperty(Vue.prototype, '$laravel_api', {
    get: function get() {
        return laravel_axios;
    }
});

Object.defineProperty(Vue.prototype, '$wp_api', {
    get: function get() {
        return wp_axios;
    }
});

Object.defineProperty(Vue.prototype, '$window', {
    get: function get() {
        return window;
    }
});

Object.defineProperty(Vue.prototype, '$fields', {
    get: function get() {
        return fields;
    }
});

Object.defineProperty(Vue.prototype, '$toast', {
    get: function get() {
        return toast;
    }
});

Object.defineProperty(Vue.prototype, '$moment', {
    get: function get() {
        return moment;
    }
});

Object.defineProperty(Vue.prototype, '$flag', {
    get: function get() {
        return {
            is_true(flag) {
                return flag === '' || flag === true || flag === 'true';
            },

            is_false(flag) {
                console.log(flag);
                return flag == null || flag === false || flag === 'false';
            }
        };
    }
});

Object.defineProperty(Vue.prototype, '$confirm', {
   get: function get() {
       return function(title = 'Are you sure?', html = '', confirmText = 'Confirm', cancelText = 'Cancel') {
           return new Promise(function(resolve, reject) {
               return Swal.fire({
                   title: title,
                   html: html,
                   showCancelButton: true,
                   showConfirmButton: true,
                   confirmButtonText: confirmText,
                   confirmButtonColor: '#04385c',
                   cancelButtonText: cancelText
               }).then(({value}) => {
                   if (value) {
                       resolve();
                   }
               });
           });
       }
   }
});

Object.defineProperty(Vue.prototype, '$scroll_to', {
    get: function get() {
        return function(element, duration = 500, options = {}) {
            return new Promise((resolve, reject) => {
                options.onDone = resolve;
                options.onCancel = reject;

                VueScrollTo.scrollTo(element, duration, options);
            });
        }
    }
});

Object.defineProperty(Vue.prototype, '$date_format', {
    get: function get() {
        return function(date, format) {
            date = moment(date, 'YYYY-MM-DD HH:mm');

            return date.format(format);
        }
    }
});

window.VueScrollTo = VueScrollTo;

Vue.use(VueTheMask);
Vue.use(VTooltip);
Vue.use(Vuex);

Vue.component('vue-recaptcha', VueRecaptcha);
Vue.component('b-wave', require('./Components/BWave.vue').default);
Vue.component('b-loader', require('./Components/BLoader.vue').default);
Vue.component('b-tabs', require('./Components/BTabs.vue').default);
Vue.component('b-tabs-nav-item', require('./Components/BTabsNavItem.vue').default);
Vue.component('b-tabs-tab', require('./Components/BTabsTab.vue').default);
Vue.component('b-filters', require('./Components/BFilters.vue').default);
Vue.component('b-filter-select', require('./Components/BFilterSelect.vue').default);
Vue.component('b-filter-input', require('./Components/BFilterInput.vue').default);
Vue.component('b-modal', require('./Components/BModal.vue').default);
Vue.component('b-button', require('./Components/BButton.vue').default);
Vue.component('b-info-modal', require('./Components/BInfoModal.vue').default);

const store = new Vuex.Store({})

export { Vue, store };
