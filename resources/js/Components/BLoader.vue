<template>
    <div class="loader-wrapper" :style="{'min-height': height ? height : '400px'}">
        <div v-if="is_loading" class="loader" :class="{'is-white': white}"></div>
        <slot v-if="!is_loading"></slot>
    </div>
</template>

<script>
    export default {
        props: ['height', 'threshold', 'white'],

        computed: {
            is_loading() {
                if (this.threshold != null) {
                    return this.$root.is_loading < this.threshold;
                }

                return this.$root.is_loading;
            }
        }
    }
</script>
