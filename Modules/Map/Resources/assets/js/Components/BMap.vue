<template>
    <gmap-map
        ref="map"
        :center="map.center"
        :zoom="map.zoom"
        :options="map.options"
        @center_changed="center_changed"
        @zoom_changed="update_zoom"
        @dragend="update_center"
    >
        <slot></slot>
    </gmap-map>
</template>

<script>
    export default {
        props: ['map'],

        data() {
            return {
                real_center: null
            }
        },

        methods: {
            center_changed(value) {
                this.real_center = {
                    lat: value.lat(),
                    lng: value.lng()
                };
            },

            update_zoom(value, test) {
                this.$root.$emit('update:zoom', value);
                this.update_center();
            },

            update_center() {
                this.$root.$emit('update:center', this.real_center);
            },

            get_map() {
                return this.$refs.map.$mapPromise;
            }
        }
    }
</script>
