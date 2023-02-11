<template>
    <b-map-modal v-if="show" :header="true" :title="fishing_location.name" v-on:close="close" :is_loading="loaded < 3" class="is-aqua">
        <b-tabs id="fishing-location-tabs">
            <template v-slot:nav>
                <b-tabs-nav-item tab="location" is-active>
                    Location
                </b-tabs-nav-item>
                <b-tabs-nav-item tab="water">
                    Water Information
                </b-tabs-nav-item>
                <b-tabs-nav-item v-if="events.length > 0" tab="events-and-competitions">
                    Events & Competitions
                </b-tabs-nav-item>
                <b-tabs-nav-item v-if="coaches.length > 0" tab="coaches">
                    Coaches
                </b-tabs-nav-item>
            </template>
            <template v-slot:tabs>
                <b-tabs-tab tab="location" is-active>
                    <p v-if="fishing_location.email">Email: <a :href="`mailto:${fishing_location.email}`">{{ fishing_location.email }}</a></p>
                    <p v-if="fishing_location.telephone">Telephone: <a :href="`tel:${fishing_location.telephone}`">{{ fishing_location.telephone }}</a></p>
                    <p v-if="fishing_location.nearest_town">Nearest Town/City: {{ fishing_location.nearest_town }}</p>
                    <p v-if="fishing_location.nearest_county">County: {{ fishing_location.nearest_county }}</p>
                    <p v-if="fishing_location.location_description">Directions: {{ fishing_location.location_description }}</p>
                    <p v-if="fishing_location.description">Permits Available: {{ fishing_location.description }}</p>
                    <p v-if="fishing_location.permit_other">
                        Permit Details: <span v-html="fishing_location.permit_other"></span>
                    </p>
                    <p v-if="fishing_location.disabled_facility">Disabled Facility</p>
                </b-tabs-tab>
                <b-tabs-tab tab="water">
                    <p v-if="water_type">Water Type: {{ water_type }}</p>
                    <p v-if="fishing_location.river_kilometers">River Kilometres: {{ fishing_location.river_kilometers }}</p>
                    <p v-if="fishing_location.stillwater_hectares">Stillwater Hectares: {{ fishing_location.stillwater_hectares }}</p>
                    <p v-if="fishing_location.disciplines">Fishery Type: {{ fishing_location.disciplines.join(', ') }}</p>
                    <p v-if="fishing_location.species">Fishery Species: {{ fishing_location.species.join(', ') }}</p>
                    <p v-if="fishing_location.fishery_stocked">Fishery Stocked</p>
                    <p v-if="fishing_location.fly_only_restriction">Fly Only Restriction</p>
                </b-tabs-tab>
                <b-tabs-tab tab="events-and-competitions">
                    <div v-for="event in events">
                        <p>{{ event.name }}</p>
                    </div>
                </b-tabs-tab>
                <b-tabs-tab tab="coaches">
                    <div v-for="coach in coaches">
                        <p>{{ coach.name }} ({{ coach.phone_number }})</p>
                        <p v-if="coach.website"><a :href="coach.website" target="blank">{{ coach.website }}</a></p>
                    </div>
                </b-tabs-tab>
            </template>
        </b-tabs>
        <div v-if="!$root.hide_update_button" class="actions">
            <button class="at-btn is-white is-frame" @click="update_details">Update Details</button>
        </div>
    </b-map-modal>
</template>

<script>
    export default {
        data() {
            return {
                show: false,
                loaded: 0,
                fishing_location: null,
                coaches: [],
                events: [],
                station: null
            }
        },

        computed: {
            water_type() {
                let water_types = {
                    'river': 'River',
                    'stillwater': 'Stillwater',
                    'canal': 'Canal',
                    'sea': 'Sea'
                };

                if (!this.fishing_location) {
                    return null;
                }

                return water_types[this.fishing_location.water_type];
            }
        },

        watch: {
            fishing_location(value) {
                if (!value) {
                    return;
                }

                this.fetch();
            }
        },

        methods: {
            fetch() {
                this.cancel_token = axios.CancelToken.source();
                this.loaded = 0;

                if (this.fishing_location.coaches && this.fishing_location.coaches.length > 0) {
                    this.fetch_coaches();
                } else {
                    this.loaded++;
                }

                if (this.fishing_location.events && this.fishing_location.events.length > 0) {
                    this.fetch_events();
                } else {
                    this.loaded++;
                }

                if (this.fishing_location.station_id) {
                    this.load_station();
                } else {
                    this.loaded++;
                }
            },

            fetch_coaches() {
                this.$wp_api.get('/wp-json/barques/v1/coaches/in', {
                    params: {
                        'id': this.fishing_location.coaches
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        this.coaches = data.data.coaches;
                        this.loaded++;
                    }
                });
            },

            fetch_events() {
                this.$wp_api.get('/wp-json/barques/v1/competitions/in', {
                    params: {
                        'id': this.fishing_location.events
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        this.events = data.data.competitions;
                        this.loaded++;
                    }
                });
            },

            load_station() {
                this.$laravel_api.get('/map/stations/' + this.fishing_location.station_id)
                .then(({data}) => {
                    if (data.success) {
                        this.station = data.data.station;
                        this.loaded++;
                    }
                });
            },

            open(fishing_location) {
                this.fishing_location = fishing_location;
                this.show = true;
            },

            close() {
                this.show = false;
                this.fishing_location = null;
                this.coaches = [];
                this.cancel_token.cancel();
            },

            update_details() {
                this.show = false;
                this.$root.open_edit_fishing_location_modal(this.fishing_location);
                this.fishing_location = null;
            }
        }
    }
</script>
