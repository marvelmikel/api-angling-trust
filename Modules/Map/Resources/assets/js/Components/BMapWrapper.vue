<template>
    <div class="b-map-wrapper">
        <slot name="map"></slot>
        <div v-if="$root.show_quick_start && !$root.is_looking_for_location" class="map-modal-wrapper">
            <b-map-quick-start></b-map-quick-start>
        </div>
        <div v-if="$root.is_looking_for_location" class="map-modal-wrapper">
            <div class="map-card">
                <div class="map-card__inner">
                    <i class="fal fa-map-marker"></i>
                    <p class="text">One moment while we find your location. <br> If you see a browser notification please click allow.</p>
                    <b-wave fill="white" anchor="bottom" direction="down"></b-wave>
                </div>
            </div>
        </div>
        <div v-if="$root.zoom < $root.minimum_zoom && !$root.show_quick_start && !$root.is_looking_for_location" class="map-modal-wrapper has-clickthrough">
            <div class="map-card">
                <div class="map-card__inner">
                    <p class="text">Please zoom in to use the map.</p>
                </div>
            </div>
        </div>
        <div v-if="$root.is_loading && $root.zoom >= $root.minimum_zoom && !$root.show_quick_start" class="map-modal-wrapper has-clickthrough">
            <b-loader :white="$root.satellite_view"></b-loader>
        </div>
        <div class="map-controls is-bottom-right is-vertical">
            <button
                class="map-button is-icon is-white"
                @click="$root.zoom = $root.zoom + 1"
                :disabled="$root.show_quick_start"
            >
                <i class="fal fa-plus"></i>
            </button>
            <button
                class="map-button is-icon is-white"
                @click="$root.zoom = $root.zoom - 1"
                :disabled="$root.show_quick_start"
            >
                <i class="fal fa-minus"></i>
            </button>
        </div>
        <div class="map-controls is-top-right">
            <button class="at-btn is-blue is-solid" @click="$root.open_submit_entry_modal">
                Submit an Entry
            </button>
        </div>
        <div class="map-controls is-top-left">
            <button class="at-btn is-blue is-solid has-icon" @click="$root.filter_pane = true" :disabled="$root.show_quick_start">
                <i class="fa fa-filter"></i> <span>Filter</span>
            </button>
        </div>
        <b-map-filter-pane></b-map-filter-pane>
    </div>
</template>

<script>
    export default {

    }
</script>
