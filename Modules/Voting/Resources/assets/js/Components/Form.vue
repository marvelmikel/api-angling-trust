<template xmlns="http://www.w3.org/1999/html">
    <div class="at-voting">
        <Registration
            v-if="!has_registered"
            :intro="form.intro_text"
            :confirmation="form.intro_confirmation_text"
            :rejection="form.intro_rejection_text"
            :disabled="saving"
            @save="handleRegistration"
        />

        <template v-else-if="has_submitted">
            <div v-if="registration_intention" v-html="form.confirmation_text"></div>
            <div v-else v-html="form.rejection_text"></div>
        </template>

        <template v-else-if="form">
            <Questions
                :questions="form.questions"
                :errors="errors"
                :disabled="saving"
                :isAttending="registration_intention"
                @submit="handleSubmission"
            />
        </template>

        <div v-if="errors.general" class="form-error">{{ errors.general }}</div>

    </div>
</template>

<script>

import {mapGetters, mapActions} from 'vuex'
import Registration from "./Registration";
import Questions from "./Questions";

export default {
    components: {
        Registration,
        Questions,
    },
    data() {
        return {
            response: {},
            registration: 'none',
            saving: false,
            errors: {},
        }
    },
    computed: {
        ...mapGetters({
            'form': 'voting/form',
            'has_registered': 'voting/has_registered',
            'has_submitted': 'voting/has_submitted',
            'registration_intention': 'voting/registration_intention',
        }),
    },
    methods: {
        ...mapActions([
            'voting/saveRegistration',
            'voting/saveResponses',
        ]),
        async handleRegistration(registration) {
            this.saving = true;

            try {
                await this['voting/saveRegistration']({registration})
            } catch(error) {
                console.log(error);
            }

            this.saving = false;
        },

        async handleSubmission(responses) {
            this.saving = true;

            try {
                await this['voting/saveResponses']({responses})
            } catch(error) {
                this.errors = error.response?.data?.errors || {'general': 'Unknown error'}
            }

            this.saving = false;
        },
    }
}

</script>
