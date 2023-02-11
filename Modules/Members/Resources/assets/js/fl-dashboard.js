
import { mapActions, mapGetters } from 'vuex'
import {store, Vue} from '../../../../../resources/js/app';
import voting from '../../../../Voting/Resources/assets/js/store'

store.registerModule('voting', voting)
Vue.component('voting-form', require('./../../../../Voting/Resources/assets/js/Components/Form.vue').default)

Vue.component('b-text-input', require('./../../../../../resources/js/Components/BTextInput.vue').default);
Vue.component('b-multi-select', require('./../../../../../resources/js/Components/BMultiSelect.vue').default);
Vue.component('b-date-of-birth-input', require('./../../../../../resources/js/Components/BDateOfBirthInput.vue').default);
Vue.component('b-select', require('./../../../../../resources/js/Components/BSelect.vue').default);
Vue.component('b-radio-input', require('./../../../../../resources/js/Components/BRadioInput.vue').default);
Vue.component('b-checkbox-input', require('./../../../../../resources/js/Components/BCheckboxInput.vue').default);
Vue.component('b-info-item', require('./../../../../../resources/js/Components/BInfoItem.vue').default);
Vue.component('b-member-numbers-input', require('./../../../../../resources/js/Components/BMemberNumbersInput.vue').default);
Vue.component('b-member-numbers-info-item', require('./../../../../../resources/js/Components/BMemberNumbersInfoItem.vue').default);

Vue.component('b-event', require('./../../../../Events/Resources/assets/js/Components/BEvent.vue').default);

Vue.component('b-my-contact-details', require('./Components/BMyContactDetails.vue').default);
Vue.component('b-my-preferences', require('./Components/BMyPreferences.vue').default);

Vue.component('b-individual-member-my-contact-details', require('./Components/Dashboard/IndividualMember/BMyContactDetails.vue').default);
Vue.component('b-individual-member-my-preferences', require('./Components/Dashboard/IndividualMember/BMyPreferences.vue').default);

Vue.component('b-club-or-syndicate-my-contact-details', require('./Components/Dashboard/ClubOrSyndicate/BMyContactDetails.vue').default);
Vue.component('b-club-or-syndicate-my-preferences', require('./Components/Dashboard/ClubOrSyndicate/BMyPreferences.vue').default);

Vue.component('b-fishery-my-contact-details', require('./Components/Dashboard/Fishery/BMyContactDetails.vue').default);
Vue.component('b-fishery-my-preferences', require('./Components/Dashboard/Fishery/BMyPreferences.vue').default);

Vue.component('b-trade-member-my-contact-details', require('./Components/Dashboard/TradeMember/BMyContactDetails.vue').default);
Vue.component('b-trade-member-my-preferences', require('./Components/Dashboard/TradeMember/BMyPreferences.vue').default);

Vue.component('b-federation-my-contact-details', require('./Components/Dashboard/Federation/BMyContactDetails.vue').default);
Vue.component('b-caag-my-contact-details', require('./Components/Dashboard/CAAG/BMyContactDetails.vue').default);
Vue.component('b-affiliate-my-contact-details', require('./Components/Dashboard/Affiliate/BMyContactDetails.vue').default);
Vue.component('b-consultatives-my-contact-details', require('./Components/Dashboard/Consultatives/BMyContactDetails.vue').default);
Vue.component('b-charter-boat-my-contact-details', require('./Components/Dashboard/CharterBoat/BMyContactDetails.vue').default);
Vue.component('b-salmon-fishery-board-my-contact-details', require('./Components/Dashboard/SalmonFisheryBoard/BMyContactDetails.vue').default);
Vue.component('b-coach-my-contact-details', require('./Components/Dashboard/Coach/BMyContactDetails.vue').default);

const app = new Vue({
    el: '#members-dashboard-app',
    store,

    created() {
        this['voting/getForm']().finally(r => this.is_loading++)

        this.$laravel_api.get('members/me')
            .then(({data}) => {
                this.member = data.data.member;
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
    },
    computed: {
        ...mapGetters({
            can_vote: 'voting/can_vote',
        })
    },
    data() {
        return {
            is_loading: 0,
            member: null,
            select_options: null
        }
    },

    methods: {
        ...mapActions([
            'voting/getForm',
        ]),
        is_junior_member() {
            return this.$root.member.membershipType.slug === 'individual-member' && this.$root.member.category.slug === 'junior';
        }
    }

});
