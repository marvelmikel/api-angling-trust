
// create vue app
// step 1 info and + for quantity of tickets
// step 2 enter personal info and card number
// step 3 thank you message

import {Vue} from '../../../../../resources/js/app';

Vue.component('b-text-input', require('./../../../../../resources/js/Components/BTextInput.vue').default);

Vue.component('b-draw-prize-purchase', require('./Components/BDrawPrizePurchase').default);
Vue.component('b-draw-stripe-purchase', require('./Components/BDrawStripePurchase').default);

const app = new Vue({
    el: '#fishing-draw-app',

    created() {
        if (window.has_cookie('PASSPORT_ACCESS_TOKEN')) {
            this.$laravel_api.get('members/me')
                .then(({data}) => {
                    if (data.success) {
                        this.member = data.data.member;
                    }

                    this.is_loading = false;
                });
        } else {
            this.is_loading = false;
        }

    },

    data() {
        return {
            member: null,
            is_loading: true
        }
    },

    methods: {

    }
});
