
import {Vue} from '../../../../../resources/js/app';

Vue.component('b-text-input', require('./../../../../../resources/js/Components/BTextInput.vue').default);
Vue.component('b-stripe-admin-payment', require('./../../../../../resources/js/Components/BStripeAdminPayment.vue').default);
Vue.component('b-other-membership-payment', require('./../../../../../resources/js/Components/BOtherMembershipPayment.vue').default);

Vue.component('b-admin-payment', require('./Components/Payment/BAdminPayment.vue').default);
Vue.component('b-my-membership-type', require('./Components/BMyMembershipType.vue').default);

const app = new Vue({
    el: '#members-admin-payment-app',

    created() {
        this.$laravel_api.get('members/me')
            .then(({data}) => {
                this.member = data.data.member;
                this.is_loading = false;
            });
    },

    data() {
        return {
            is_loading: true,
            is_submitting: false,
            member: null,
            price: null
        }
    },

    methods: {
        is_life_member() {
            return false;
        },

        get_payment_intent_secret() {
            return new Promise((resolve, reject) => {
                this.$laravel_api.get('payment_methods/stripe/intent')
                    .then(({data}) => {
                        resolve(data.data.client_secret);
                    })
                    .catch((response) => {
                        reject(response);
                    });
            });
        },
    }
});
