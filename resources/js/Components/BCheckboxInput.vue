<template>
    <div class="form-input" :class="{ 'has-error': has_error }">
        <template v-if="multiple == ''">
            <p v-if="label">{{ label }} <span v-if="required === ''" class="required">*</span></p>
            <template v-for="option in options">
                <div class="form-input--checkbox">
                    <input :id="unique_key(option.id)" type="checkbox" :name="unique_key(option.id)" :value="option.id" v-model="field.value" :disabled="disabled">
                    <label :for="unique_key(option.id)">{{ option.name }}</label>
                </div>
            </template>
        </template>
        <template v-if="multiple != ''">
            <div class="form-input--checkbox">
                <input :id="name" type="checkbox" :name="name" value="true" v-model="field.value" :disabled="disabled">
                <label :for="name">{{ label }}</label>
            </div>
        </template>
        <div class="form-errors" v-text="field.error"></div>
    </div>
</template>

<script>
    export default {
        props: ['name', 'label', 'value', 'options', 'multiple', 'disabled', 'required'],

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
        },

        methods: {
            unique_key(key) {
                return this.name + '-' + key;
            }
        }

    }
</script>
