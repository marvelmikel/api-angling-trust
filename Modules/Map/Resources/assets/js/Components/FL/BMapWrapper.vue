<template>
    <div class="b-map-wrapper">
        <slot name="map"></slot>
        <div v-if="$root.zoom < $root.minimum_zoom && !$root.is_looking_for_location" class="map-modal-wrapper has-clickthrough">
            <div class="map-card">
                <div class="map-card__inner">
                    <h3 class="">Please zoom in to use the map.</h3>
                    <button class="at-btn is-blue is-solid has-icon no-clickthrough" @click="$root.zoom = $root.minimum_zoom">
                        <i class="fal fa-search-plus"></i>
                        <span>Zoom In</span>
                    </button>
                    <b-wave fill="white" anchor="bottom" direction="down"></b-wave>
                </div>
            </div>
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
        <div v-if="!$root.is_looking_for_location && $root.zoom >= $root.minimum_zoom && $root.show_no_results_modal" class="map-modal-wrapper has-clickthrough">
            <div class="map-card">
                <div class="map-card__inner">
                    <p class="text">Looks like there is nothing in this area.</p>
                    <b-wave fill="white" anchor="bottom" direction="down"></b-wave>
                </div>
            </div>
        </div>
        <div class="map-controls is-bottom-right is-vertical">
            <button class="map-button is-icon is-white" @click="$root.zoom = $root.zoom + 1">
                <i class="fal fa-plus"></i>
            </button>
            <button class="map-button is-icon is-white" @click="$root.zoom = $root.zoom - 1">
                <i class="fal fa-minus"></i>
            </button>
        </div>
    </div>
</template>

<script>
    export default {

    }
</script>
