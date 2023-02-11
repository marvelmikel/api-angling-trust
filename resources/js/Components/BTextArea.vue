<template>
    <div class="form-input" :class="{ 'has-error': has_error }">
        <label :for="name">{{ label }} <span v-if="required === ''" class="required">*</span></label>
        <textarea :id="name" v-model="field.value" :placeholder="placeholder" :rows="number_of_rows"></textarea>
        <div class="form-errors" v-text="field.error"></div>
    </div>
</template>

<script>
    export default {
        props: ['name', 'label', 'value', 'placeholder', 'rows', 'required'],

        created() {
            if (this.rows) {
                this.number_of_rows = this.rows;
            }
        },

        data() {
            return {
                number_of_rows: 10
            }
        },

        computed: {
            field: {
                get() {
                    return this.value;
                },

                set(value) {
                    this.$emit('input', value);
                }
            },

            has_error() {
                return this.field.error !== null;
            }
        },

        watch: {
            'field.value': function() {
                if (this.field.error) {
                    this.field.error = null;
                }
            }
        }
    }
</script>
