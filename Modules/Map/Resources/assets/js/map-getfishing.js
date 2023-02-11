
import {Vue} from '../../../../../resources/js/app';

import * as VueGoogleMaps from 'vue2-google-maps';
import {gmapApi} from 'vue2-google-maps';
import VueRecaptcha from 'vue-recaptcha';

Vue.use(VueGoogleMaps, {
    load: {
        key: process.env.MIX_GOOGLE_MAPS_API_KEY,
        libraries: 'places,geometry'
    }
});

window.GOOGLE_RECAPTCHA_SITE_KEY = '6LceW-8UAAAAALhnkyLI5PrzTkUQkYC06gG3yBT3';

Vue.component('b-text-input', require('./../../../../../resources/js/Components/BTextInput.vue').default);
Vue.component('b-location-input', require('./../../../../../resources/js/Components/BLocationInput.vue').default);
Vue.component('b-select', require('./../../../../../resources/js/Components/BSelect.vue').default);
Vue.component('b-select2', require('./../../../../../resources/js/Components/BSelect2.vue').default);
Vue.component('b-select2-input', require('./../../../../../resources/js/Components/BSelect2Input.vue').default);
Vue.component('b-checkbox-input', require('./../../../../../resources/js/Components/BCheckboxInput.vue').default);

Vue.component('b-map-wrapper', require('./Components/GetFishing/BMapWrapper.vue').default);
Vue.component('fishing-location-info', require('./Components/FishingLocationInfo.vue').default);
Vue.component('b-map', require('./Components/BMap.vue').default);
Vue.component('river-level-graph', require('./Components/RiverLevelGraph.vue').default);
Vue.component('b-map-modal', require('./Components/BMapModal.vue').default);
Vue.component('b-map-search', require('./Components/BMapSearch.vue').default);
Vue.component('coach-info', require('./Components/CoachInfo.vue').default);
Vue.component('event-info', require('./Components/EventInfo.vue').default);
Vue.component('b-map-marker', require('./Components/BMapMarker.vue').default);
Vue.component('b-map-quick-start', require('./Components/GetFishing/BMapQuickStart.vue').default);
Vue.component('b-map-filter-pane', require('./Components/GetFishing/BMapFilterPane.vue').default);

Vue.component('vue-recaptcha', VueRecaptcha);

const app = new Vue({
    el: '#map-app',

    created() {
        this.$on('click:station', this.open_station_info);
        this.$on('update:center', this.update_center);
        this.$on('update:zoom', this.update_zoom);


        this.$laravel_api.get('member_select_options')
            .then(({data}) => {
                if (data.success) {
                    this.select_options = data.data.options;
                }
            });

        this.$wp_api.get('wp-json/barques/map/species')
            .then(({data}) => {
                if (data.success) {
                    this.species = data.data.species;
                }
            });
    },

    data() {
        return {
            hide_update_button: true,
            show_quick_start: true,
            is_loading: false,
            filter_pane: false,
            minimum_zoom: 10,
            is_looking_for_location: false,
            markers_axios_cancel_token: null,
            filters: {
                location_types: {
                    events: true,
                    fishing_locations: true,
                    coaches: true
                }
            },
            events: [],
            fishing_locations: [],
            coaches: [],
            hide_markers: false,
            center: {
                lat: 52.408486673821166, lng: -1.3796415548897745
            },
            zoom: 8,
            satellite_view: false,
            options: {
                mapTypeControl: false,
                streetViewControl: false,
                zoomControl: false,
                fullscreenControl: false,
                scrollwheel: false
            },
            select_options: null,
            location_types: [
                {
                    id: 'events',
                    name: 'Events'
                },
                {
                    id: 'fishing_locations',
                    name: 'Venues'
                },
                {
                    id: 'coaches',
                    name: 'Coaches'
                },
            ],
            species: []
        }
    },

    computed: {
        google: gmapApi,

        map() {
            return {
                center: this.center,
                zoom: this.zoom,
                options: this.options
            }
        },

        show_events() {
            if (!this.filters.location_types.events) {
                return false;
            }

            return !this.hide_markers;
        },

        show_fishing_locations() {
            if (!this.filters.location_types.fishing_locations) {
                return false;
            }

            return !this.hide_markers;
        },

        show_coaches() {
            if (!this.filters.location_types.coaches) {
                return false;
            }

            return !this.hide_markers;
        },

        filtered_events() {
            let events = this.events;

            if (!events) {
                return [];
            }

            return events;
        },

        filtered_fishing_locations() {
            let fishing_locations = this.fishing_locations;

            if (!fishing_locations) {
                return [];
            }

            if (this.filters.water_type) {
                fishing_locations = fishing_locations.filter((fishing_location) => {
                    return fishing_location.water_type == this.filters.water_type;
                });
            }

            return fishing_locations;
        },

        filtered_coaches() {
            let coaches = this.coaches;

            if (!coaches) {
                return [];
            }

            if (this.filters.discipline) {
                coaches = coaches.filter((coach) => {
                    return coach.discipline_ids.includes(this.get_active_filter_discipline_id());
                });
            }

            return coaches;
        },

        total_visible_markers() {
            return this.filtered_fishing_locations.length + this.filtered_coaches.length;
        }
    },

    watch: {
        center() {
            if (this.zoom < this.minimum_zoom) {
                this.clear_stations();
                return;
            }

            this.debounced_load_markers();
        },

        zoom() {
            if (this.zoom < this.minimum_zoom) {
                this.clear_stations();
                return;
            }

            this.debounced_load_markers();
        }
    },

    methods: {
        get_active_filter_discipline_id() {
            let disciplines = {
                'coarse': "1",
                'match': "2",
                'sea': "3",
                'carp': "4",
                'game': "6",
                'recreation': "29",
                'specimen': "30"
            };

            return disciplines[this.filters.discipline];
        },

        update_zoom(value) {
            this.zoom = value;
        },

        update_center(value) {
            if (!value) {
                return;
            }

            this.center = value;
        },

        go_to_location(location) {
            return new Promise((resolve, reject) => {
                window.debug('Map', 'Setting Center: ' + location.lat + ' (lat) ' + location.lng + ' (lng)');

                this.update_center(location);

                resolve();
            });
        },

        debounced_load_markers() {
            this.load_markers_timeout = setTimeout(() => {
                this.hide_markers = false;
                this.load_markers();
            }, 500);
        },

        load_markers() {
            this.$refs.map.get_map().then((map) => {
                let bounds = map.getBounds();

                if (!bounds) {
                    return;
                }

                let a = bounds.getSouthWest();
                let b = bounds.getNorthEast();

                this.get_markers(a, b);
            });
        },

        get_markers(a, b) {
            this.is_loading = true;

            if (this.cancel_token) {
                this.cancel_token.cancel();
                this.cancel_token = null;
            }

            const CancelToken = axios.CancelToken;
            this.cancel_token = CancelToken.source();

            console.log('Loading Markers');

            let location_types = [];

            window.for_each(this.filters.location_types, (key, value) => {
                if (value) {
                    location_types.push(key);
                }
            });

            let params = {
                bounds: {
                    a: {
                        lat: a.lat(),
                        lng: a.lng()
                    },
                    b: {
                        lat: b.lat(),
                        lng: b.lng()
                    }
                },
                location_types: location_types
            };

            this.$wp_api.post('/wp-json/barques/map/getfishing/markers', params, {
                cancelToken: this.cancel_token.token
            }).then(({data}) => {
                if (data.success) {
                    this.events = data.data.events;
                    this.fishing_locations = data.data.fishing_locations;
                    this.coaches = data.data.coaches;
                }

                this.is_loading = false;
            }).catch(() => {});
        },

        clear_stations() {
            this.hide_markers = true;
            clearTimeout(this.load_markers_timeout);

            if (this.cancel_token) {
                this.cancel_token.cancel('halt');
            }
        },

        open_event_info(event) {
            this.$refs.event_info.open(event);
        },

        open_fishing_location_info(fishing_location) {
            this.$refs.fishing_location_info.open(fishing_location);
        },

        open_coach_info(coach) {
            this.$refs.coach_info.open(coach);
        },

        use_my_location() {
            return new Promise((resolve, reject) => {
                this.is_looking_for_location = true;

                navigator.geolocation.getCurrentPosition((position) => {
                    this.is_looking_for_location = false;
                    this.go_to_location({
                        lat: position.coords.latitude, lng: position.coords.longitude
                    }, -20);
                    resolve();
                }, (test) => {
                    this.$toast.error('Location services could not be accessed, please check your browser settings.');
                    this.is_looking_for_location = false;
                    reject();
                });
            });

        },

        search_for_a_location() {
            this.$refs.map_search.open();
        },

        reset_filters() {
            this.filters = {
                discipline: null,
                water_type: null,
                location_types: {
                    fishing_locations: false,
                    coaches: false,
                }
            }
        },

        open_submit_entry_modal() {
            this.$refs.submit_entry_modal.open();

            window.setTimeout(() => {
                this.filter_pane = false;
            }, 100);
        },

        switch_to_satellite_view() {
            this.satellite_view = true;

            this.$refs.map.get_map().then((map) => {
                map.setMapTypeId('satellite');
            });
        },

        switch_to_default_view() {
            this.satellite_view = false;

            this.$refs.map.get_map().then((map) => {
                map.setMapTypeId('roadmap');
            });
        },

        open_edit_fishing_location_modal(fishing_location) {
            this.$refs.update_fishing_location_modal.open(fishing_location);
        },

        open_edit_coach_modal(coach) {
            this.$refs.update_coach_modal.open(coach);
        },

        ensure_zoomed_in() {
            if (this.zoom < 12) {
                this.zoom = 12;
            }
        }
    }

});
