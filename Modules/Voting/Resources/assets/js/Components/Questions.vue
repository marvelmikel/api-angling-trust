<template>
    <div>
        <component
            v-for="question in questions"
            v-if="showIfAttending(question.show_if_attending)"
            v-model="responses[question.id]"
            :errors="getErrors(question.id)"
            :question="question"
            :is="`${question.type_studly}Field`"
            :key="question.id"
        />
        <div>
            <button
                class="at-btn is-blue is-frame"
                v-on:click.prevent="$emit('submit', responses)"
                :disabled="disabled"
            >Submit</button>
        </div>
    </div>
</template>

<script>
import MultipleChoiceField from "./QuestionTypes/MultipleChoice";
import ForAgainstField from "./QuestionTypes/ForAgainst";
import TextField from "./QuestionTypes/Text";

const Questions = {
    components: {
        MultipleChoiceField,
        ForAgainstField,
        TextField,
    },
    props: {
        questions: {
            type: Array,
            required: true,
        },
        errors: {
            type: Object,
            required: false,
            default: {}
        },
        disabled: {
            type: Boolean,
            required: false,
            default: false,
        },
        isAttending: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
            responses: {}
        }
    },
    methods: {
        getErrors(questionId) {
            const error = this.errors[`responses.${questionId}`];
            return error || [];
        },
        showIfAttending(showIfAttending) {
            if (showIfAttending === null || showIfAttending === undefined) {
                return true;
            }

            if (showIfAttending) {
                return this.isAttending;
            }

            return !this.isAttending;
        }

    }
}

export default Questions;
</script>
