<template>
    <div class="at-filters__fields__item">
        <span class="at-select">
            <select v-model="selected">
                <option value="any" disabled>{{ label }}</option>
                <option v-for="option in options" :value="option.id">{{ option.name }}</option>
            </select>
        </span>
    </div>
</template>

<script>
    export default {
        props: ['label', 'options', 'value'],

        computed: {
            selected: {
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
