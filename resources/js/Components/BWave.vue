<template>
    <svg :class="classes" class="svg-wave is-fullwidth">
        <use xlink:href="#wave"></use>
    </svg>
</template>

<script>
    export default {
        props: ['fill', 'opacity', 'direction', 'anchor'],

        created() {
            if (this.fill) {
                this.classes += 'is-' + this.fill;

                if (this.opacity) {
                    this.classes += `--${this.opacity}`;
                }
            }
            if(this.direction) {
                this.classes += ' is-' + this.direction;
            }
            if(this.anchor) {
                this.classes += ' is-' + this.anchor;
            }
        },

        data() {
            return {
                classes: ''
            }
        }
    };
</script>
