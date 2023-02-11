
import {Vue} from '../../../../../resources/js/app';

Vue.component('b-step-nav-item', require('./../../../../../resources/js/Components/BStepNavItem.vue').default);
Vue.component('b-step-content', require('./../../../../../resources/js/Components/BStepContent.vue').default);
Vue.component('b-text-input', require('./../../../../../resources/js/Components/BTextInput.vue').default);
Vue.component('b-select', require('./../../../../../resources/js/Components/BSelect.vue').default);
Vue.component('b-multi-select', require('./../../../../../resources/js/Components/BMultiSelect.vue').default);
Vue.component('b-donation-input', require('./../../../../../resources/js/Components/BDonationInput.vue').default);
Vue.component('b-date-of-birth-input', require('./../../../../../resources/js/Components/BDateOfBirthInput.vue').default);
Vue.component('b-password-input', require('./../../../../../resources/js/Components/BPasswordInput.vue').default);
Vue.component('b-address-lookup', require('./../../../../../resources/js/Components/BAddressLookup.vue').default);
Vue.component('b-radio-input', require('./../../../../../resources/js/Components/BRadioInput.vue').default);
Vue.component('b-checkbox-input', require('./../../../../../resources/js/Components/BCheckboxInput.vue').default);
Vue.component('b-member-numbers-input', require('./../../../../../resources/js/Components/BMemberNumbersInput.vue').default);
Vue.component('b-stripe-membership-payment', require('./../../../../../resources/js/Components/BStripeMembershipPayment.vue').default);
Vue.component('b-smart-debit-membership-payment', require('./../../../../../resources/js/Components/BSmartDebitMembershipPayment.vue').default);

Vue.component('b-my-personal-details', require('./Components/BMyPersonalDetails.vue').default);
Vue.component('b-my-preferences', require('./Components/BMyPreferences.vue').default);
Vue.component('b-my-membership-type', require('./Components/BMyMembershipType.vue').default);
Vue.component('b-my-payment-methods', require('./Components/BMyPaymentMethods.vue').default);
Vue.component('b-card-payment-method', require('./Components/BCardPaymentMethod.vue').default);

Vue.component('b-register-individual-member-step-2', require('./Components/Registration/IndividualMember/BRegisterStep2.vue').default);
Vue.component('b-register-individual-member-step-3', require('./Components/Registration/IndividualMember/BRegisterStep3.vue').default);
Vue.component('b-register-individual-member-step-4', require('./Components/Registration/Any/BRegisterStep4.vue').default);

Vue.component('b-register-club-or-syndicate-step-2', require('./Components/Registration/ClubOrSyndicate/BRegisterStep2.vue').default);
Vue.component('b-register-club-or-syndicate-step-3', require('./Components/Registration/ClubOrSyndicate/BRegisterStep3.vue').default);
Vue.component('b-register-club-or-syndicate-step-4', require('./Components/Registration/Any/BRegisterStep4.vue').default);

Vue.component('b-register-fishery-step-2', require('./Components/Registration/Fishery/BRegisterStep2.vue').default);
Vue.component('b-register-fishery-step-3', require('./Components/Registration/Fishery/BRegisterStep3.vue').default);
Vue.component('b-register-fishery-step-4', require('./Components/Registration/Any/BRegisterStep4.vue').default);

Vue.component('b-register-trade-member-step-2', require('./Components/Registration/TradeMember/BRegisterStep2.vue').default);
Vue.component('b-register-trade-member-step-3', require('./Components/Registration/TradeMember/BRegisterStep3.vue').default);
Vue.component('b-register-trade-member-step-4', require('./Components/Registration/Any/BRegisterStep4.vue').default);

Vue.component('b-register-trade-supporter-step-2', require('./Components/Registration/TradeMember/BRegisterStep2.vue').default);
Vue.component('b-register-trade-supporter-step-3', require('./Components/Registration/TradeMember/BRegisterStep3.vue').default);
Vue.component('b-register-trade-supporter-step-4', require('./Components/Registration/Any/BRegisterStep4.vue').default);


const app = new Vue({
    el: '#members-register-app',

    created() {
        window.googleMapLoaded = () => {
            this.is_loading++;
        };

        this.$laravel_api.get('personal/membership_types')
            .then(({data}) => {
                this.membership_types = data.data.membership_types;
                this.is_loading++;
            });

        this.$laravel_api.get('member_select_options')
            .then(({data}) => {
                if (data.success) {
                    this.select_options = data.data.options;

                    this.select_options['yes_no'] = [
                        {
                            id: 'yes',
                            name: 'Yes'
                        },
                        {
                            id: 'no',
                            name: 'No'
                        }
                    ]
                }

                this.is_loading++;
            });

        if (window.has_cookie('PASSPORT_ACCESS_TOKEN')) {
            this.$laravel_api.get('members/me')
                .then(({data}) => {
                    this.member = data.data.member;
                });
        }

        let params = new URLSearchParams(document.location.search.substring(1));

        if (params.has('step')) {
            this.step = parseInt(params.get('step'));
        } else {
            if (window.has_cookie('MEMBER_REGISTRATION_STEP')) {
                this.step = parseInt(window.get_cookie('MEMBER_REGISTRATION_STEP'));
            } else {
                this.$toast.error();
            }
        }
    },

    data() {
        return {
            is_loading: 0,
            is_loading_threshold: 3,
            member: null,
            step: null,
            membership_types: [],
            select_options: null,
            disciplines: [],
            divisions: [],
            regions: [],
            honorifics: [
                'Mr',
                'Mrs',
                'Miss',
                'Ms',
                'Mx',
                'Dr',
                'Sir',
                'Lord'
            ]
        }
    },

    computed: {
        selected_membership_type() {
            if (this.membership_types === [] || this.member === null) {
                return null;
            }

            return this.membership_types.find(x => x.id == this.member.membership_type_id);
        }
    },

    methods: {
        abandon_registration() {
            window.delete_cookie('MEMBER_REGISTRATION_STEP');
            window.delete_cookie('PASSPORT_ACCESS_TOKEN');

            window.location.replace(window.location.origin + '/members/register/');
        },

        to_step(step) {
            window.set_cookie('MEMBER_REGISTRATION_STEP', step, 1000 * 60 * 60 * 24 * 360);
            window.scrollTo(0, 0);
            this.step = step;
            this.$toast.success('Your progress has been saved.');
        },

        get_payment_intent_secret() {
            return new Promise((resolve, reject) => {
                this.$laravel_api.get('payment_methods/stripe/intent')
                    .then(({data}) => {
                        resolve(data.data.client_secret);
                    })
                    .catch((response) => {
                        reject(response);
                    });
            });
        },


        has_no_donation() {
            return this.$root.member && this.$root.member.donations.length === 0;
        },

        is_junior_member() {
            return this._is_individual_with_category([
                'junior'
            ]);
        },

        is_life_member() {
            return this._is_individual_with_category([
                    'life',
                    'life-membership-premier'
                ]);
        },

        is_life_premier_member() {
            return this._is_individual_with_category([
                    'life-membership-premier'
                ]);
        },

        _is_individual_with_category(categories) {
            return this.$root.member &&
                this.$root.member.membershipType.slug === 'individual-member' &&
                categories.includes(this.$root.member.category.slug);
        },

        can_have_joining_gift() {
            if (this.$root.member.membershipType.slug !== 'individual-member') {
                return false;
            }

            if (this.$root.member.category.slug !== 'life' && this.$root.member.category.slug !== 'adult' && this.$root.member.category.slug !== 'senior-citizen') {
                return false;
            }

            return true;
        },
    }
});
