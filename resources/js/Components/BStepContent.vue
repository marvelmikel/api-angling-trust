<template>
    <div v-if="show" class="b-step-content">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        props: ['step'],

        computed: {
            show() {
                return this.$root.step === this.step;
            }
        }
    }
</script>
