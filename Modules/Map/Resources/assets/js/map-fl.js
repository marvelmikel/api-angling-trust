
import {Vue} from '../../../../../resources/js/app';

import * as VueGoogleMaps from 'vue2-google-maps';
import {gmapApi} from 'vue2-google-maps';

Vue.use(VueGoogleMaps, {
    load: {
        key: process.env.MIX_GOOGLE_MAPS_FL_API_KEY,
        libraries: 'places,geometry'
    }
});

Vue.component('b-fl-filter-select', require('./../../../../../resources/js/Components/BFLFilterSelect.vue').default);

Vue.component('b-map-wrapper', require('./Components/FL/BMapWrapper.vue').default);
Vue.component('b-map', require('./Components/BMap.vue').default);
Vue.component('b-map-modal', require('./Components/BMapModal.vue').default);
Vue.component('b-map-search', require('./Components/FL/BMapSearch.vue').default);
Vue.component('b-map-marker', require('./Components/FL/BMapMarker.vue').default);
Vue.component('case-study-info', require('./Components/FL/CaseStudyInfo.vue').default);

const app = new Vue({
    el: '#map-app',

    created() {
        this.$on('update:center', this.update_center);
        this.$on('update:zoom', this.update_zoom);

        console.log('map init');

        let loading = [];

        loading.push(
            this.$wp_api.get('/wp-json/barques/case-studies/categories')
                .then(({data}) => {
                    if (data.success) {
                        this.categories = data.data.categories;
                    }
                }).catch((response) => {
                    console.log(response);
                })
        );

        loading.push(
            this.$wp_api.get('/wp-json/barques/case-studies/statuses')
                .then(({data}) => {
                    if (data.success) {
                        this.statuses = data.data.statuses;
                    }
                }).catch((response) => {
                    console.log(response);
                })
        );

        Promise.all(loading)
            .then(() => {

                let status = this.statuses.find(x => x.slug == this.$window.get_parameter('status'));

                if (status) {
                    this.filters.status = status.id;
                    this.active_filters.status = status.id;
                }

                let category = this.categories.find(x => x.slug == this.$window.get_parameter('category'));

                if (category) {
                    this.filters.category = category.id;
                    this.active_filters.category = category.id;
                }

            })
    },

    mounted() {
        if (this.zoom >= this.minimum_zoom) {
            this.get_markers(
                {
                    lat: () => { return 50.33077228550391 },
                    lng: () => { return -11.778201125202274 }
                },
                {
                    lat: () => { return 56.824363635411224 },
                    lng: () => { return 2.8446016091727255 }
                }
            );
        }
    },

    data() {
        return {
            minimum_zoom: 6,
            is_looking_for_location: false,
            show_no_results_modal: false,
            markers_axios_cancel_token: null,
            active_filters: {
                category: 'any',
                status: 'any'
            },
            filters: {
                category: 'any',
                status: 'any'
            },
            case_studies: [],
            categories: [],
            statuses: [],
            hide_markers: false,
            center: {
                lat: 53.70259473191529, lng: -4.4667997580147745
            },
            zoom: 7,
            options: {
                mapTypeControl: false,
                streetViewControl: false,
                zoomControl: false,
                fullscreenControl: false,
                scrollwheel: false
            }
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


        filters_closed_text() {
            if (this.active_filters.category === 'any' && this.active_filters.status === 'any') {
                return;
            }

            let active_filters = [];

            if (this.active_filters.status !== 'any') {
                let status = this.statuses.find(x => x.id == this.active_filters.status);
                active_filters.push(status.name);
            }

            if (this.active_filters.category !== 'any') {
                let category = this.categories.find(x => x.id == this.active_filters.category);
                active_filters.push(category.name);
            }

            return "Currently Filtered By: " + active_filters.join(', ');
        },

        filtered_case_studies() {
            let case_studies = this.case_studies;

            if (this.active_filters.category !== 'any') {
                case_studies = case_studies.filter((case_study) => {
                    return case_study.categories.indexOf(this.active_filters.category) !== -1;
                });
            }

            if (this.active_filters.status !== 'any') {
                case_studies = case_studies.filter((case_study) => {
                    return case_study.statuses.indexOf(this.active_filters.status) !== -1;
                });
            }

            return case_studies;
        }
    },

    watch: {
        center() {
            if (this.zoom < this.minimum_zoom) {
                this.clear_markers();
                return;
            }

            this.debounced_load_markers();
        },

        zoom() {
            if (this.zoom < this.minimum_zoom) {
                this.clear_markers();
                return;
            }

            this.debounced_load_markers();
        }
    },

    methods: {
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
            window.debug('Map', 'Setting Center: ' + location.lat + ' (lat) ' + location.lng + ' (lng)');

            this.update_center(location);

            if (this.zoom < 14) {
                this.zoom = 14;
            }
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
            if (this.cancel_token) {
                this.cancel_token.cancel();
                this.cancel_token = null;
            }

            const CancelToken = axios.CancelToken;
            this.cancel_token = CancelToken.source();

            this.$wp_api.get(`/wp-json/barques/map/markers?bounds[a][lat]=${a.lat()}&bounds[a][lng]=${a.lng()}&bounds[b][lat]=${b.lat()}&bounds[b][lng]=${b.lng()}`, {
                cancelToken: this.cancel_token.token
            }).then(({data}) => {
                if (data.success) {
                    this.case_studies = data.data.case_studies;

                    if (this.case_studies.length === 0) {
                        this.show_no_results_modal = true;
                    } else {
                        this.show_no_results_modal = false;
                    }
                }

                console.log(data);
            }).catch(() => {});
        },

        clear_markers() {
            this.hide_markers = true;
            clearTimeout(this.load_markers_timeout);

            if (this.cancel_token) {
                this.cancel_token.cancel('halt');
            }
        },

        use_my_location() {
            let modal = window.setTimeout(() => {
                this.is_looking_for_location = true;
            }, 500);

            navigator.geolocation.getCurrentPosition((position) => {
                window.clearTimeout(modal);
                this.is_looking_for_location = false;
                this.go_to_location({
                    lat: position.coords.latitude, lng: position.coords.longitude
                }, -20);
            }, (test) => {
                window.clearTimeout(modal);
                this.$toast.error('Location services could not be accessed, please check your browser settings.');
                this.is_looking_for_location = false;
            });

            this.$scroll_to('#map-filters', 1000, {offset: -12});
        },

        search_for_a_location() {
            this.$refs.map_search.open();
        },

        reset_filters() {
            this.filters = {
                category: 'any',
                status: 'any'
            }
            if(window.history.pushState) {
                window.history.pushState('', '/', window.location.pathname)
            } else {
                window.location.hash = '';
            }
        },

        submit_filters() {
            this.active_filters = Object.assign({}, this.filters);
            this.$scroll_to('#map-filters', 1000, {offset: -12})
                .then(() => {
                    if (this.zoom < this.minimum_zoom) {
                        this.zoom = this.minimum_zoom;
                    }
                })
                .catch(() => {});
        },

        open_case_study_info(case_study) {
            this.$refs.case_study_info.open(case_study);
        },

        map_marker_icon(case_study) {
            let category_id = case_study.categories[0];
            let category = this.categories.find(x => x.id == category_id);

            if (!category || !category.color) {
                return 'case-study-marker-abb3b8.svg';
            }

            let color = category.color.substring(1);

            return `case-study-marker-${color}.svg`;
        }
    }

});
