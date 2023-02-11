<template>
    <div
        class="at-ticket-list__ticket at-card"
        :class="{ 'is-canceled': has_been_canceled }"
    >
        <div class="at-ticket-list__ticket__logos">
            <div
                class="at-ticket-list__ticket__logo"
                :style="`background-image: url('${event.category_logo_url}')`"
            ></div>
        </div>
        <div class="at-ticket-list__ticket__details">
            <div>
                <h3 class="at-heading--h3">{{ event.name }}</h3>
                <p>
                    <i class="fal fa-calendar"></i>
                    <span v-text="event_date_time"></span>
                </p>
                <p>
                    <i class="fal fa-map-marker"></i>
                    <span>{{ event.details.address }}</span>
                </p>
            </div>
            <div>
                <h5 class="at-heading--h5" v-if="!has_been_canceled">
                    <span v-if="!is_pair_event">1</span
                    ><span v-if="is_pair_event">2</span> x {{ ticket.name }}
                </h5>
                <h5 class="at-heading--h5" v-if="has_been_canceled">
                    Canceled
                </h5>
            </div>
        </div>
        <div class="at-ticket-list__ticket__actions">
            <a
                :href="`/competitions/${event.slug}`"
                class="at-btn is-blue is-solid"
                >Information</a
            >
            <template v-if="!is_pair_event && !has_been_canceled">
                <a
                    :href="`/tickets/${purchased_ticket.reference}.pdf`"
                    target="_blank"
                    class="at-btn is-aqua is-solid"
                    >Download Ticket</a
                >
            </template>
            <template v-if="is_pair_event && !has_been_canceled">
                <a
                    :href="`/tickets/${purchased_ticket.reference}_a.pdf`"
                    target="_blank"
                    class="at-btn is-aqua is-solid"
                    >Download Ticket A</a
                >
                <a
                    :href="`/tickets/${purchased_ticket.reference}_b.pdf`"
                    target="_blank"
                    class="at-btn is-aqua is-solid"
                    >Download Ticket B</a
                >
            </template>
            <button
                v-if="can_cancel"
                class="at-btn is-red is-solid"
                @click="confirm_cancel"
                :disabled="is_loading"
            >
                Cancel Ticket<span v-if="is_pair_event">s</span>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: ["purchased_ticket"],

    data() {
        return {
            is_loading: false,
            force_canceled: false
        };
    },

    computed: {
        event() {
            return this.purchased_ticket.event;
        },

        event_date_time() {
            return (
                this.$date_format(this.event.details.start_date, "DD/MM/YYYY") +
                " " +
                this.event.details.start_time +
                " - " +
                this.$date_format(this.event.details.end_date, "DD/MM/YYYY") +
                " " +
                this.event.details.end_time
            );
        },

        is_pair_event() {
            return this.event.type === "pair";
        },

        ticket() {
            return this.purchased_ticket.ticket;
        },

        has_been_canceled() {
            return (
                this.force_canceled ||
                this.purchased_ticket.canceled_at !== null
            );
        },

        can_cancel() {
            let has_started =
                this.$moment(this.event.start_date).diff(this.$moment()) < 0;

            return !has_started && !this.has_been_canceled;
        }
    },

    methods: {
        confirm_cancel() {
            this.$confirm(
                "Are you sure?",
                "The Angling Trust require 3 days notice prior to the qualifier in order to process a refund. Any notification outside of this time scale will not be entitled to receive a refund. There is no secondary market for the sale or transfer of any tickets. Please be aware that once cancelled, tickets cannot be reinstated. The Competitions team will be in touch via email to confirm your refund, if eligible. If you wish to continue, click Yes.",
                "Yes",
                "No"
            ).then(() => {
                this.cancel();
            });
        },

        cancel() {
            this.is_loading = true;

            this.$laravel_api
                .post(`any/ticket-purchase/${this.purchased_ticket.id}/cancel`)
                .then(({ data }) => {
                    if (data.success) {
                        let index = this.$root.member.purchasedTickets.findIndex(
                            x => x.id === this.purchased_ticket.id
                        );
                        this.$root.member.purchasedTickets[index] =
                            data.data.purchasedTicket;

                        this.force_canceled = true;
                        this.$toast.success("Your ticket has been canceled");
                    } else {
                        this.$toast.error(data.error.message);
                    }

                    this.is_loading = false;
                })
                .catch(() => {
                    this.$toast.error();
                    this.is_loading = false;
                });
        }
    }
};
</script>
