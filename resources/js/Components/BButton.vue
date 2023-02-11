<template>
    <span class="b-button">
        <template v-if="href">
            <a :class="classes" :href="href">
                <slot></slot>
            </a>
        </template>
        <template v-if="!href">
            <button :class="classes">
                <slot></slot>
            </button>
        </template>
    </span>
</template>

<script>
    export default {
        props: ['colour', 'solid', 'outline', 'href'],

        computed: {
            type() {
                if (this.$flag.is_true(this.solid)) {
                    return 'solid';
                }

                if (this.$flag.is_true(this.outline)) {
                    return 'outline';
                }

                return 'frame';
            },

            classes() {
                let classes = ['at-btn'];

                if (this.colour) {
                    classes.push(`is-${this.colour}`);
                } else {
                    classes.push('is-blue');
                }

                classes.push(`is-${this.type}`);

                return classes.join(' ');
            }
        }
    }
</script>
