import {Vue} from '../../../../../resources/js/app';

Vue.component('b-text-input', require('./../../../../../resources/js/Components/BTextInput.vue').default);
Vue.component('b-checkbox-input', require('./../../../../../resources/js/Components/BCheckboxInput.vue').default);
Vue.component('b-multi-select', require('./../../../../../resources/js/Components/BMultiSelect.vue').default);
Vue.component('b-date-of-birth-input', require('./../../../../../resources/js/Components/BDateOfBirthInput.vue').default);

Vue.component('b-ticket-details', require('./Components/BTicketDetails.vue').default);
Vue.component('b-individual-form', require('./Components/Forms/IndividualForm.vue').default);
Vue.component('b-junior-individual-form', require('./Components/Forms/JuniorIndividualForm.vue').default);
Vue.component('b-pair-form', require('./Components/Forms/PairForm.vue').default);
Vue.component('b-team-form', require('./Components/Forms/TeamForm.vue').default);
Vue.component('b-ticket-stripe-purchase', require('./Components/BTicketStripePurchase.vue').default);

const app = new Vue({
    el: '#ticket-purchase-app',

    created() {
        let params = new URLSearchParams(document.location.search.substring(1));
        let reference = params.get('ticket');

        let frozen_ticket_token = this.$window.get_cookie('FROZEN_TICKET_TOKEN');

        if (window.has_cookie('PASSPORT_ACCESS_TOKEN')) {
            this.$laravel_api.get('members/me')
                .then(({data}) => {
                    this.member = data.data.member;
                    this.is_loading++;
                });
        } else {
            this.is_loading++;
        }

        let ticket_basket_token = this.$window.get_cookie('TICKET_BASKET_TOKEN');

        this.$laravel_api.get(`any/tickets/${reference}`, {
            params: {
                frozen_ticket_token: frozen_ticket_token,
                ticket_basket_token: ticket_basket_token
            }
        })
            .then(({data}) => {
                if (data.success) {
                    console.log(data);
                    this.ticket = data.data.ticket;
                    this.event = data.data.event;
                    this.frozen_ticket = data.data.frozen_ticket;

                    let expires = "expires=" + data.data.cookie_expiry;
                    document.cookie = 'FROZEN_TICKET_TOKEN' + "=" + data.data.frozen_ticket.token + ";" + expires + ";path=/";
                    document.cookie = 'FROZEN_TICKET_EVENT' + '=' + data.data.event.wp_id + ";" + expires + ";path=/";
                    document.cookie = 'TICKET_BASKET_TOKEN' + "=" + data.data.basket + ";" + expires + ";path=/";
                    document.cookie = 'TICKET_BASKET_EXPIRY' + "=" + data.data.cookie_expiry + ";" + expires + ";path=/";
                } else {
                    this.has_server_error = true;
                    this.server_error = data.error.message;
                }

                this.is_loading++;
            });
    },

    data() {
        return {
            is_loading: 0,
            member: null,
            has_server_error: false,
            ticket: null,
            event: null,
            purchased_ticket: null,
            frozen_ticket: null,
            form_data: null,
            price: null
        }
    },

    methods: {
        next(data) {
            let count = 0;
            let cart_tickets = [];
            let cart_ticket_categories = [];

            if (this.$window.has_cookie('TICKET_BASKET_COUNT')) {
                count = parseInt(this.$window.get_cookie('TICKET_BASKET_COUNT'));
            }
            if (this.$window.has_cookie('TICKET_BASKET_EVENT')) {
                cart_tickets = JSON.parse(this.$window.get_cookie('TICKET_BASKET_EVENT'));
            }
            if (this.$window.has_cookie('TICKET_BASKET_CATEGORY')) {
                cart_ticket_categories = JSON.parse(this.$window.get_cookie('TICKET_BASKET_CATEGORY'));
            }
            console.log(this.event);
            cart_tickets.push(this.event.wp_id);
            cart_ticket_categories.push({
                id: this.event.category,
                limit: this.event.category_limit
            });
            this.$window.set_cookie('TICKET_BASKET_COUNT', count + 1);
            this.$window.set_cookie('TICKET_BASKET_CATEGORY', JSON.stringify(cart_ticket_categories));
            this.$window.set_cookie('TICKET_BASKET_EVENT', JSON.stringify(cart_tickets));

            this.$window.delete_cookie('FROZEN_TICKET_TOKEN');
            this.$window.delete_cookie('FROZEN_TICKET_EVENT');
            window.location.replace(window.location.origin + '/events/checkout');
        }
    }

});
