
import {Vue} from '../../../../../resources/js/app';
import Password from "vue-password-strength-meter";

Vue.component('b-password-reset', require('./Components/BPasswordReset.vue').default);
Vue.component('b-password-input', require('./../../../../../resources/js/Components/BPasswordInput.vue').default);

Vue.component('b-password-strength-meter', Password);

const app = new Vue({
    el: '#password-reset-app',

    data() {
        return {
            is_submitting: false
        }
    }
});
