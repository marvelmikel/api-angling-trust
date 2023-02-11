<template>
    <div class="at-filters__fields__item">
        <span class="at-select">
            <b-select2 v-model="selected" :multiple="multiple" :placeholder="placeholder">
                <option v-for="option in options" :value="option.id" v-html="option.name"></option>
            </b-select2>
        </span>
    </div>
</template>

<script>
    export default {
        props: ['options', 'value', 'multiple', 'placeholder'],

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
