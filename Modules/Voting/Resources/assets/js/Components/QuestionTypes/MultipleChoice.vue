<template>
    <div class="voting-question voting-question__multiplechoice">
        <div class="option" v-for="option in question.options">
            <label :for="`mc_${id}__${option.id}`">
                <input :id="`mc_${id}__${option.id}`"
                       type="checkbox"
                       :value="option.content"
                       v-model="value"
                       :disabled="isDisabled(option)"
                >
                <span>{{ option.content }}</span>
            </label>
        </div>

        <div class="form-errors" v-if="errors.length">
            <p v-for="error in errors">{{ error }}</p>
        </div>

    </div>
</template>

<script>

import BaseField from "./BaseField"

export default {
    extends: BaseField,
    data() {
        return {
            value: [],
        }
    },
    watch: {
        value(newVal) {
            this.$emit('input', newVal)
        }
    },
    methods: {
        isDisabled(option) {
            if (this.value.indexOf(option.content) > -1) {
                return false
            }
            return this.question.max > 0 && this.question.max === this.value.length
        }
    }
}

</script>
