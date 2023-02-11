
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
Vue.component('b-text-area', require('./../../../../../resources/js/Components/BTextArea.vue').default);
Vue.component('b-multi-select', require('./../../../../../resources/js/Components/BMultiSelect.vue').default);
Vue.component('b-location-input', require('./../../../../../resources/js/Components/BLocationInput.vue').default);

Vue.component('vue-recaptcha', VueRecaptcha);

const app = new Vue({
    el: '#events-submit-app',

    created() {

    },

    data() {
        return {
            has_submitted: false,
            is_submitting: false,
            gr_response: null,
            fields: {
                name: {
                    value: null, error: null
                },
                start_date: {
                    value: null, error: null
                },
                end_date: {
                    value: null, error: null
                },
                address: {
                    value: null, error: null
                },
                location: {
                    value: null, error: null
                },
                description: {
                    value: null, error: null
                }
            }
        }
    },

    computed: {
        google: gmapApi,

        can_submit() {
            return !this.is_submitting && this.gr_response !== null;
        }
    },

    methods: {
        verify(response) {
            this.gr_response = response;
        },

        expired() {
            this.gr_response = null;
        },

        submit() {
            this.is_submitting = true;

            if (!this.validate()) {
                this.is_submitting = false;
                this.$toast.error('You have validation errors.');
                return;
            }

            let data = this.$fields.serialise_fields(this.fields);
            data['gr_response'] = this.gr_response;

            this.$wp_api.post('/wp-json/barques/events/submit', data)
                .then(({data}) => {
                    this.has_submitted = true;
                    this.is_submitting = false;
                    this.$toast.success('Thank you for submitting your event.');
                })
                .catch((response) => {
                    console.log(response);
                    this.$toast.error();
                    this.is_submitting = false;
                });
        },

        validate() {
            let valid = true;

            let required = ['name', 'start_date', 'end_date'];
            let dates = ['start_date', 'end_date'];

            required.forEach((key) => {
                if (!this.fields[key].value) {
                    this.fields[key].error = `The ${key} is required.`;
                    valid = false;
                }
            });

            dates.forEach((key) => {
                if (this.fields[key].value && !this.$window.is_valid_date(this.fields[key].value)) {
                    this.fields[key].error = `The ${key} must match the format dd/mm/yyyy.`;
                    valid = false;
                }
            });

            return valid;
        }
    }

});
