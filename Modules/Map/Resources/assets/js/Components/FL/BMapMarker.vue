<template>
    <div class="b-map-marker">
        <gmap-marker
            :position="{lat: parseFloat(marker.lat), lng: parseFloat(marker.lng)}"
            :clickable="true"
            :icon="{
                url: '/wp-content/themes/barques/public/img/' + icon,
                scaledSize: new $window.google.maps.Size(50, 50)
            }"
            @click="on_click"
        />
    </div>
</template>

<script>
    export default {
        props: ['marker', 'icon'],

        methods: {
            on_click() {
                this.$emit('click', this.marker);
            }
        }
    }
</script>
