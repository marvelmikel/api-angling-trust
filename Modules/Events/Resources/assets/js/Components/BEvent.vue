<template>
    <div class="at-card at-card--event" :class="`is-${mode}`">
        <div class="at-card__inner at-card--event__inner">
            <div class="at-card__content at-card--event__content" :style="`background-image: url('${event.thumbnail}')`">
                <dl class="at-card--event__details">
                    <div class="at-card--event__details__item">
                        <i aria-hidden="true" class="fal fa-calendar"></i>
                        <span>{{ $date_format(event.start_date, 'DD/MM/YYYY') }}</span>
                        <span v-if="event.start_date !== event.end_date"> - {{ $date_format(event.end_date, 'DD/MM/YYYY') }}</span>
                    </div>
                    <div class="at-card--event__details__item" v-if="event.address">
                        <i class="fal fa-map-marker"></i> <span>{{ event.address }}</span>
                    </div>
                    <div class="at-card--event__details__item">
                        <i aria-hidden="true" class="fal fa-user-circle"></i> <span>{{ open_to_text }}</span>
                    </div>
                    <div class="at-card--event__details__item" v-if="event.tickets_remaining !== null">
                        <i aria-hidden="true" class="fal fa-ticket"></i>
                        <span>{{ event.tickets_remaining }}</span>
                    </div>
                </dl>
                <h2 class="at-heading--h1">{{ event.name }}</h2>
                <div class="desc at-text--card">{{ event.summary }}</div>
                <a :href="event.link" class="at-btn is-white is-frame "><span class="at-btn__text">Read more</span></a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['event', 'mode'],

        computed: {
            open_to_text() {
                if (this.event.member_only) {
                    return 'Members only';
                } else {
                    return 'Open to all';
                }
            }
        }
    }
</script>
