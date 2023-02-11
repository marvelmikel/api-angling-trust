<template>
    <div class="checkout__item">
        <span class="checkout__item-name" v-text="ticket.event.name"></span>
        <div>
            <span class="checkout__item-price" v-text="formatted_price"></span>
            <span class="checkout__item-remove" @click="remove">
                <i class="fas fa-times"></i>
            </span>
        </div>
    </div>
</template>

<script>
export default {
    name: "BBasketItem",
    props: ['ticket'],

    computed: {
        formatted_price() {
            return '£' + (this.ticket.price / 100);
        }
    },

    methods: {
        remove() {
            this.$window.delete_cookie('TICKET_BASKET_CATEGORY');
            this.$laravel_api.post(`any/events/checkout/ticket/${this.ticket.reference}/remove`, {
                ticket_basket_token: this.$root.basket.reference,
            })
            .then(({data}) => {
                this.$root.basket = data.data.basket;
                var cart_tickets = [];

                if(this.$root.basket.tickets.length > 0){
                    this.$root.basket.tickets.forEach((value, index) => {
                        cart_tickets.push(value.event.wp_id);
                    });

                }
                this.$window.set_cookie('TICKET_BASKET_EVENT', JSON.stringify(cart_tickets));
                this.$window.set_cookie('TICKET_BASKET_COUNT', this.$root.basket.tickets.length);
            });
        }
    }
}
</script>
