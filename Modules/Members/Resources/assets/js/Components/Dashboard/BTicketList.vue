<template>
    <div class="at-ticket-list">
        <b-ticket-list-ticket v-for="purchased_ticket in purchased_tickets" :key="purchased_ticket.id" :purchased_ticket="purchased_ticket" />
        <p v-if="!has_purchased_tickets && future">You don't have any upcoming competitions.</p>
        <p v-if="!has_purchased_tickets && !future">You don't have any past competitions.</p>
    </div>
</template>

<script>
    export default {
        props: ['tickets', 'future'],

        computed: {
            purchased_tickets() {
                return this.tickets.filter((ticket) => {
                    if (this.future === true) {
                        return this.$moment(ticket.event.end_date).diff(this.$moment()) > 0;
                    } else {
                        return this.$moment(ticket.event.end_date).diff(this.$moment()) < 0;
                    }
                });
            },

            has_purchased_tickets() {
                return this.purchased_tickets.length > 0;
            }
        }
    }
</script>
