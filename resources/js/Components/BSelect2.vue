<template>
    <select class="b-select-2" :multiple="multiple">
        <slot></slot>
    </select>
</template>

<script>
    export default {
        props: ['value', 'multiple', 'placeholder'],

        mounted() {
            $(this.$el)
                .select2({
                    placeholder: this.placeholder
                })
                .val(this.value)
                .trigger('change')
                .on('change', () => {
                    this.$emit('input', $(this.$el).val());
                });
        },

        watch: {
            value: function (value) {
                if (JSON.stringify(value) === JSON.stringify($(this.$el).val())) {
                    return;
                }

                $(this.$el)
                    .val(value)
                    .trigger('change');
            }
        },

        destroyed: function () {
            $(this.$el).off().select2('destroy')
        },
    }
</script>
