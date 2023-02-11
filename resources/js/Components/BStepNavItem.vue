<template>
    <button class="at-btn is-blue" :class="classes" :disabled="disabled">
        <slot></slot>
    </button>
</template>

<script>
    export default {
        props: ['step'],

        computed: {
            classes() {
                let classes = [];

                if (this.$root.step >= this.step) {
                    classes.push('is-solid');
                } else {
                    classes.push('is-frame');
                }

                return classes.join(' ');
            },

            disabled() {
                return this.$root.step !== this.step;
            }
        }
    }
</script>
