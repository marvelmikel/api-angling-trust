<template>
    <b-map-modal v-if="show" :header="true" :title="event.name" v-on:close="close" class="is-bluestone">
        <div class="event-info">
            <div class="event-info__left">
                <img :src="event.featured_image" :alt="event.name" />
            </div>
            <div class="event-info__right">
                <h3>
                    <template v-if="event.start_date === event.end_date">
                        <span v-text="$date_format(event.start_date, 'DD/MM/YY')"></span> <br>
                        <span v-text="event.start_time"></span> - <span v-text="event.end_time"></span>
                    </template>
                    <template v-if="event.start_date !== event.end_date">
                        Starts: <span v-text="$date_format(event.start_date, 'DD/MM/YY')"></span> at <span v-text="event.start_time"></span> <br>
                        Ends: <span v-text="$date_format(event.end_date, 'DD/MM/YY')"></span> at <span v-text="event.end_time"></span>
                    </template>
                </h3>
                <p v-if="event.summary" v-html="event.summary"></p>
                <p v-if="event.ticket_additional_info" v-html="event.ticket_additional_info"></p>
                <a :href="event.permalink" class="at-btn is-white is-frame">Read More</a>
            </div>
        </div>
    </b-map-modal>
</template>

<script>
    export default {
        data() {
            return {
                show: false,
                event: null
            }
        },

        computed: {

        },

        methods: {
            open(event) {
                this.event = event;
                this.show = true;
            },

            close() {
                this.show = false;
                this.event = null;
            }
        }
    }
</script>
