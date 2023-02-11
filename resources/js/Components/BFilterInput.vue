<template>
    <div class="at-filters__fields__item is-two-line">
        <label>{{ label }}</label>
        <input type="text" v-model="input_value">
    </div>
</template>

<script>
    export default {
        props: ['label', 'value'],

        computed: {
            input_value: {
                get() {
                    return this.value;
                },

                set(value) {
                    this.$emit('input', value);
                }
            }
        }
    }
</script>
