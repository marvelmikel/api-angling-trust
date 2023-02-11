<template>
    <div class="form-input" :class="{ 'has-error': has_error }">
        <p v-if="label">{{ label }} <span v-if="required === ''" class="required">*</span></p>
        <template v-for="option in options">
            <div class="form-input--radio">
                <input :id="unique_key(option.id)" type="radio" :name="name" :value="option.id" v-model="field.value">
                <label :for="unique_key(option.id)">{{ option.name }}</label>
            </div>
        </template>
        <div class="form-errors" v-text="field.error"></div>
    </div>
</template>

<script>
    export default {
        props: ['name', 'label', 'value', 'options', 'required'],
        computed: {
            field: {
                get() {
                    return this.value;
                },

                set(value) {
                    this.$emit('input', value);
                },
                error: null
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
                if(this.name) {
                    return `${this.name}-${key}`;
                }
                return key;
            }
        }

    }
</script>
