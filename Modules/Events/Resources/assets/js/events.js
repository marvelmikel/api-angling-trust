
import {Vue} from '../../../../../resources/js/app';

Vue.component('b-event', require('./Components/BEvent.vue').default);
Vue.component('b-event-list', require('./Components/BEventList.vue').default);

Vue.component('b-select2', require('./../../../../../resources/js/Components/BSelect2.vue').default);

const app = new Vue({
    el: '#events-app',

    created() {

        this.$laravel_api.get('member_select_options')
            .then(({data}) => {
                if (data.success) {
                    this.select_options = data.data.options;
                }

                this.is_loading++;
            });

        this.$wp_api.get('/wp-json/barques/v1/competitions/future')
            .then(({data}) => {
                if (data.success) {
                    this.events = data.data.competitions;
                    this.is_loading++;
                }
            });

        this.$wp_api.get('/wp-json/barques/v1/competition_categories')
            .then(({data}) => {
                if (data.success) {
                    this.competition_categories = data.data.categories;
                    this.is_loading++;
                }
            });
    },

    data() {
        return {
            is_loading: 0,
            active_filters: {
                keyword: '',
                category: null,
                discipline: null,
                region: null
            },
            filters: {
                keyword: '',
                category: null,
                discipline: null,
                region: null
            },
            select_options: null,
            disciplines: [
                {
                    id: 'coarse',
                    name: 'Coarse'
                },
                {
                    id: 'game',
                    name: 'Game'
                },
                {
                    id: 'sea',
                    name: 'Sea'
                }
            ],
            competition_categories: [],
            events: []
        }
    },

    computed: {
        filtered_events() {
            let events = this.events;

            if (this.active_filters.keyword !== '') {
                events = events.filter((event) => {
                    return event.name.toLowerCase().includes(this.active_filters.keyword.toLowerCase());
                });
            }

            if (this.active_filters.category !== null) {
                events = events.filter((event) => {
                    return event.categories.includes(parseInt(this.active_filters.category));
                });
            }

            if (this.active_filters.discipline !== null) {
                events = events.filter((event) => {
                    return event.discipline == this.active_filters.discipline;
                });
            }

            return events;
        },

        filters_closed_text() {
            if (this.active_filters.discipline === null && this.active_filters.category === null && this.active_filters.keyword === '') {
                return;
            }

            let active_filters = [];

            if (this.active_filters.keyword !== '') {
                active_filters.push(this.active_filters.keyword);
            }

            if (this.active_filters.category !== null) {
                let category = this.competition_categories.find(x => x.id == this.active_filters.category);
                active_filters.push(category.name);
            }

            if (this.active_filters.discipline !== null) {
                let discipline = this.disciplines.find(x => x.id == this.active_filters.discipline);
                active_filters.push(discipline.name);
            }

            return "Currently Filtered By: " + active_filters.join(', ');
        },
    },

    methods: {
        reset_filters() {
            this.filters = {
                keyword: '',
                category: null,
                discipline: null
            };
        },

        submit_filters() {
            this.active_filters = Object.assign({}, this.filters);
            this.$scroll_to('#event-filters', 1000, {offset: -12});
        }
    }

});
