<template>
    <div class="container">
        <div class="at-section__inner">
            <div flex spread>
                <p class="at-heading--h1">{{ $root.event.name }}</p>
                <p class="at-heading--h1">
                    <span>{{ formatted_time_remaining }}</span> <i class="fal fa-stopwatch"></i>
                </p>
            </div>
            <p class="at-heading--h2">{{ $root.ticket.name }} {{ $root.ticket.formatted_price }}</p>
        </div>
    </div>
</template>

<script>
    export default {
        created() {
            let expires = this.$moment(this.$root.frozen_ticket.expires_at);
            let now = this.$moment();

            this.time_remaining = Math.round(expires.diff(now) / 1000);

            this.ticker = setInterval(() => {
                this.time_remaining --;

                if (this.time_remaining <= 0) {
                    this.time_remaining = 0;
                    clearInterval(this.ticker);
                }
            }, 1000);
        },

        computed: {
            formatted_time_remaining() {
                let minutes = Math.floor(this.time_remaining / 60);
                let seconds = this.time_remaining - (minutes * 60);

                return `${minutes}:${window.pad(seconds)}`;
            }
        },

        data() {
            return {
                time_remaining: null,
            }
        }
    }
</script>
