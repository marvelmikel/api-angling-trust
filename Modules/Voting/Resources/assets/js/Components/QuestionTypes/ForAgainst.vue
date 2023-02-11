<template>
    <div class="voting-question voting-question__foragainst">

        <div v-for="option in question.options" class="option">
            <span>{{ option.content }}</span>

            <div v-for="position in ['for', 'against', 'abstain']">
                <label :for="`fa_${id}${option.id}_${position}`">{{ position }}</label> <br>
                <input
                    type="radio"
                    :name="`fa_${id}${option.id}`"
                    :id="`fa_${id}${option.id}_${position}`"
                    :value="position"
                    @change="onChange(option, position)"
                >
            </div>
        </div>

        <div class="form-errors" v-if="errors.length">
            <p v-for="error in errors">{{ error }}</p>
        </div>
    </div>
</template>

<script>

import BaseField from "./BaseField";

export default {
    extends: BaseField,
    props: {
        question: {
            type: Object,
            required: true,
        }
    },
    created() {
        const values = {}
        this.question.options.forEach(option => {
            values[option.id] = {
                content: option.content,
                position: 'none',
            }
        })
        this.values = values;
    },
    data() {
        return {
            values: {}
        }
    },
    methods: {
      onChange({id, content}, position) {
          const {...values} = this.values;
          values[id] = {
              content,
              position,
          }
          this.values = values;
      }
    },
    watch: {
        values() {
          this.$emit('input', this.values);
        }
    }
}

</script>

