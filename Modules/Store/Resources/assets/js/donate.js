
import {Vue} from '../../../../../resources/js/app';

Vue.component('b-text-input', require('./../../../../../resources/js/Components/BTextInput.vue').default);
Vue.component('b-donation-input', require('./../../../../../resources/js/Components/BDonationInput.vue').default);
Vue.component('b-radio-input', require('./../../../../../resources/js/Components/BRadioInput.vue').default);
Vue.component('b-stripe-payment', require('./../../../../../resources/js/Components/BStripePayment.vue').default);

Vue.component('b-donate', require('./Components/Donate.vue').default);

const app = new Vue({
    el: '#store-donate-app',

    created() {
        this.$laravel_api.get('members/me')
            .then(({data}) => {
                if (data.success) {
                    this.member = data.data.member;
                }

                this.is_loading = false;
            });
    },

    data() {
        return {
            member: null,
            is_loading: true
        }
    },

    methods: {

    }
});
