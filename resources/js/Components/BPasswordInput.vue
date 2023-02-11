<template>
    <div class="form-input" :class="{ 'has-error': has_error, 'has-strength-meter': strong === '' }">
        <label :for="name">{{ label }}<span v-if="required === ''" class="required">*</span></label>
        <input :id="name" type="password" v-model="field.value">
        <b-password-strength-meter v-if="strong === ''" class="password-strength-meter" v-model="field.value" :strength-meter-only="true"/>
        <div class="form-errors" v-text="field.error"></div>
    </div>
</template>

<script>
    export default {
        props: ['name', 'label', 'value', 'required', 'strong'],

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
