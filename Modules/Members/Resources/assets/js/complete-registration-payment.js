
import {Vue} from '../../../../../resources/js/app';

Vue.component('b-text-input', require('./../../../../../resources/js/Components/BTextInput.vue').default);
Vue.component('b-stripe-membership-payment', require('./../../../../../resources/js/Components/BStripeMembershipPayment.vue').default);
Vue.component('b-smart-debit-membership-payment', require('./../../../../../resources/js/Components/BSmartDebitMembershipPayment.vue').default);

Vue.component('b-complete-registration-payment', require('./Components/Payment/BCompleteRegistrationPayment.vue').default);
Vue.component('b-my-membership-type', require('./Components/BMyMembershipType.vue').default);

const app = new Vue({
    el: '#members-complete-registration-payment-app',

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
            member: null
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
