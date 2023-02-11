<template>
    <b-map-modal v-if="show" :header="true" :title="station.name" v-on:close="close" :is_loading="is_loading" class="is-bluestone">
        <b-tabs id="station-info-tabs">
            <template v-slot:nav>
                <b-tabs-nav-item tab="statistics" is-active>
                    Statistics
                </b-tabs-nav-item>
                <b-tabs-nav-item tab="readings">
                    Readings
                </b-tabs-nav-item>
            </template>
            <template v-slot:tabs>
                <b-tabs-tab tab="statistics" is-active>
                    <div v-if="stats" class="stats">
                        <div v-if="latest_reading" class="stat">
                            <div class="info">Current Level: {{ latest_reading.value }}m</div>
                            <div class="extra">Recorded: {{ $date_format(latest_reading.recorded_at, 'DD/MM/YYYY HH:mm') }}</div>
                        </div>
                        <div v-if="stats.max_on_record" class="stat">
                            <div class="info">Highest On Record: {{ stats.max_on_record.value }}m</div>
                            <div class="extra">Recorded: {{ $date_format(stats.max_on_record.recorded_at, 'DD/MM/YYYY HH:mm') }}</div>
                        </div>
                        <div v-if="stats.min_on_record" class="stat">
                            <div class="info">Lowest On Record: {{ stats.min_on_record.value }}m</div>
                            <div class="extra">Recorded: {{ $date_format(stats.min_on_record.recorded_at, 'DD/MM/YYYY HH:mm') }}</div>
                        </div>
                        <div v-if="stats.typical_range" class="stat">
                            <div class="info">Typical Range: Between {{ stats.typical_range.low }}m and {{ stats.typical_range.high }}m</div>
                        </div>
                    </div>
                </b-tabs-tab>
                <b-tabs-tab tab="readings">
                    <div v-if="readings" class="readings">
                        <river-level-graph :readings="readings" :stats="stats"/>
                    </div>
                </b-tabs-tab>
            </template>
        </b-tabs>
    </b-map-modal>
</template>

<script>
    export default {
        data() {
            return {
                show: false,
                is_loading: true,
                station: null,
                stats: null,
                readings: [],
                latest_reading: null
            }
        },

        watch: {
            station(value) {
                if (!value) {
                    return;
                }

                this.fetch_stats();
            }
        },

        methods: {
            fetch_stats() {
                this.is_loading = true;
                this.cancel_token = axios.CancelToken.source();

                this.$laravel_api.get('/map/stations/' + this.station.id, {
                    cancelToken: this.cancel_token.token
                }).then(({data}) => {
                    if (data.success) {
                        this.stats = data.data.station.stats;
                        this.readings = data.data.station.readings;
                        this.latest_reading = data.data.station.latest_reading;
                        this.is_loading = false;
                    }
                }).catch(() => {
                    this.is_loading = false;
                });
            },

            open(station) {
                this.station = station;
                this.show = true;
            },

            close() {
                this.show = false;
                this.station = null;
                this.cancel_token.cancel();
            }
        }
    }
</script>
