<template>
    <div class="form-input" :class="{ 'has-error': has_error }">
        <label :for="name">{{ label }} <span v-if="required === ''" class="required">*</span></label>
        <template v-if="options_are_not_entities">
            <span class="at-select">
                <b-select2 :id="name" v-model="field.value" :multiple="multiple">
                    <option v-for="option in options" :value="option">{{ option }}</option>
                </b-select2>
            </span>
        </template>
        <template v-if="!options_are_not_entities">
            <span class="at-select">
                <b-select2 :id="name" v-model="field.value" :multiple="multiple">
                    <option v-for="option in options" :value="option.id">{{ option.name }}</option>
                </b-select2>
            </span>
        </template>
        <div class="form-errors" v-text="field.error"></div>
    </div>
</template>

<script>
    export default {
        props: ['name', 'label', 'value', 'options', 'options_are_not_entities', 'required', 'multiple'],

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
