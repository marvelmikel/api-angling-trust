<template>
    <div class="map-modal-wrapper" @click.self="close">
        <div class="map-modal">
            <div v-if="header" class="map-modal-header is-bluestone">
                <h2 class="title">{{ title }}</h2>
                <button class="at-btn is-white is-frame has-icon" @click="close">
                    <i class="fal fa-times"></i> <span>Close</span>
                </button>
            </div>
            <button v-if="!header" class="at-btn is-grey is-outline has-icon" @click="close">
                <i class="fal fa-times"></i> <span>Close</span>
            </button>
            <div class="map-modal-content">
                <div v-if="is_loading" class="loader"></div>
                <slot v-if="!is_loading"></slot>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        props: ['title', 'show', 'header', 'is_loading'],

        methods: {
            close() {
                this.$emit('close');
            }
        }
    }
</script>
