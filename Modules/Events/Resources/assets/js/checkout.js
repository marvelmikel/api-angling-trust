import {Vue} from '../../../../../resources/js/app';

Vue.component('b-text-input', require('./../../../../../resources/js/Components/BTextInput.vue').default);
Vue.component('b-ticket-stripe-purchase', require('./Components/BTicketStripePurchase.vue').default);
Vue.component('b-basket-details', require('./Components/BBasketDetails.vue').default);
Vue.component('b-basket-item', require('./Components/BBasketItem.vue').default);

const app = new Vue({
    el: '#ticket-checkout-app',

    created() {
        let ticket_basket_token = this.$window.get_cookie('TICKET_BASKET_TOKEN');

        console.log(ticket_basket_token);

        if (ticket_basket_token === '') {
            this.is_loading++;
        } else {
            this.$laravel_api.get(`any/events/checkout?ticket_basket_token=${ticket_basket_token}`)
                .then(({data}) => {
                    if (data.success) {
                        this.basket = data.data.basket;
                        this.$window.set_cookie('TICKET_BASKET_COUNT', this.basket.tickets.length);
                    } else {
                        this.server_error = data.error.message;
                    }

                    this.is_loading++;
                });
        }
    },

    data() {
        return {
            is_loading: 0,
            step: 1,
            server_error: null,
            basket: null,
            is_submitting: false
        }
    },

    computed: {
        is_empty() {
            if (this.$window.get_cookie('TICKET_BASKET_TOKEN') === '') {
                return true;
            }

            if (this.basket === null) {
                return true;
            }

            return this.basket.tickets.length === 0;
        },

        total() {
            if (this.basket === null) {
                return 0;
            }

            return this.basket.price;
        },

        formatted_total() {
            if (this.total === 0) {
                return '£0';
            }

            return '£' + (this.total / 100);
        }
    },

    methods: {
        start_payment() {
            this.step = 2;
        },

        complete(paymentIntent) {
            console.log(paymentIntent);

            this.$laravel_api.post(`any/events/checkout`, {
                ticket_basket_token: this.basket.reference,
                payment_id: paymentIntent.id,
            })
                .then(({data}) => {
                    if (data.success) {
                        this.$window.delete_cookie('TICKET_BASKET_TOKEN');
                        this.$window.delete_cookie('TICKET_BASKET_EVENT');
                        this.$window.delete_cookie('TICKET_BASKET_CATEGORY');
                        window.location.replace(window.location.origin + '/thank-you-for-purchasing');
                    } else {
                        this.server_error = data.error.message;
                    }
                });

            // todo: add catch
        }
    }

});
