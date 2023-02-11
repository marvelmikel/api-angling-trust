
import {Vue} from '../../../../../resources/js/app';
import Password from 'vue-password-strength-meter';

Vue.component('b-text-input', require('./../../../../../resources/js/Components/BTextInput.vue').default);
Vue.component('b-checkbox-input', require('./../../../../../resources/js/Components/BCheckboxInput.vue').default);
Vue.component('b-select', require('./../../../../../resources/js/Components/BSelect.vue').default);
Vue.component('b-password-input', require('./../../../../../resources/js/Components/BPasswordInput.vue').default);

Vue.component('b-password-strength-meter', Password);

const app = new Vue({
    el: '#members-register-app',

    created() {
        let params = new URLSearchParams(document.location.search.substring(1));

        if (params.has('membership_type')) {
            this.fields.membership_type.value = params.get('membership_type');
        }
    },

    data() {
        return {
            is_loading: true,
            is_submitting: false,
            membership_types: [
                { id: 'individual-member', name: 'Individual Member' },
                { id: 'club-or-syndicate', name: 'Club or Syndicate' },
                { id: 'fishery', name: 'Fishery' },
                { id: 'trade-member', name: 'Retail Associate' },
                { id: 'trade-supporter', name: 'Trade Supporter' },
            ],
            fields: {
                user: {
                    email: {
                        value: null, error: null
                    },
                    password: {
                        value: null, error: null
                    },
                    password_confirmation: {
                        value: null, error: null
                    }
                },
                membership_type: {
                    value: null, error: null
                },
                promo_code: {
                    value: null, error: null
                },
                opt_in_1: {
                    value: null, error: null
                },
                opt_in_2: {
                    value: null, error: null
                }
            }
        }
    },

    methods: {
        abandon_registration() {
            window.delete_cookie('MEMBER_REGISTRATION_STEP');
            window.delete_cookie('MEMBER_REGISTRATION_TYPE');
            window.delete_cookie('PASSPORT_ACCESS_TOKEN');
            window.delete_cookie('MEMBERSHIP_TYPE');

            window.location.replace(window.location.origin + '/members/register/');
        },

        submit() {
            this.is_submitting = true;

            this.$laravel_api.post('auth/register/step-1', this.$fields.serialise_fields(this.fields))
                .then(({data}) => {
                    if (data.success) {
                        window.set_cookie('MEMBER_REGISTRATION_TYPE', data.data.member.membership_type_slug, 1000 * 60 * 60 * 24 * 360);
                        window.set_cookie('MEMBER_REGISTRATION_STEP', 2, 1000 * 60 * 60 * 24 * 360);
                        window.set_cookie('PASSPORT_ACCESS_TOKEN', data.data.access_token, data.data.expires);
                        window.set_cookie('MEMBERSHIP_TYPE', data.data.membership_type, data.data.expires);

                        this.$laravel_api.defaults.headers.common['Authorization'] = `Bearer ${data.data.access_token}`;

                        window.location.replace(window.location.origin + '/members/register/continue');
                    } else {
                        this.is_submitting = false;
                        this.$toast.error("There are errors in your submission.");

                        if (data.error.code === 422) {
                            this.$fields.fill_errors(this.fields, data.data.errors);
                        }
                    }
                })
                .catch((response) => {
                    this.$toast.error();
                    this.is_submitting = false;
                });
        }
    }
});
