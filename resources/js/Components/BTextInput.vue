<template>
    <div class="form-input" :class="{ 'has-error': has_error }">
        <label v-if="!!label" :for="name"><span class="mr-2">{{ label }} <span v-if="required === ''" class="required">*</span></span>
            <b-info-modal v-if="info" inline>{{info}}</b-info-modal>
        </label>

        <b-info-modal v-if="!label && info" inline :label="infoLabel">{{info}}</b-info-modal>

        <input :id="name"
               type="text"
               v-model="field.value"
               :placeholder="placeholder"
               :required="!!required"
               :disabled="!!disabled"
               v-on:blur="$emit('blur', $event.target.value)"
        >
        <div class="form-errors" v-text="field.error"></div>
    </div>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                required: true,
            },
            label: {
                type: String,
                required: false,
                default: '',
            },
            placeholder: {
                type: String,
                required: false,
                default: ''
            },
            value: {
                type: Object,
                required: true,
            },
            required: {
                type: Boolean,
                required: false,
                default: false,
            },
            disabled: {
                type: Boolean,
                required: false,
                default: false,
            },
            info: {
                type: String,
                required: false,
                default: '',
            },
            infoLabel: {
                type: String,
                required: false,
                default: '',
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
            },
        },

        watch: {
            'field.value': function() {
                if (this.field.error) {
                    this.field.error = null;
                };
            }
        }
    }
</script>
