<template>
    <div class="b-location-input" :class="{ 'has-error': has_error }">
        <label :for="name">{{ label }} <span v-if="required === ''" class="required">*</span></label>
        <gmap-map
            ref="map"
            :center="map.center"
            :zoom="map.zoom"
            :options="map.options"
            @click="set_marker"
        >
            <template v-if="icon">
                <gmap-marker
                    v-if="field.value"
                    :position="{ lat: field.value.lat, lng: field.value.lng }"
                    :icon="{
                        url: $window.laravel_url + '/img/' + icon,
                        scaledSize: new $window.google.maps.Size(50, 50)
                    }"
                />
            </template>
            <template v-if="!icon">
                <gmap-marker
                    v-if="field.value"
                    :position="{ lat: field.value.lat, lng: field.value.lng }"
                />
            </template>
        </gmap-map>
        <div class="form-errors" v-text="field.error"></div>
    </div>
</template>

<script>
    export default {
        props: ['name', 'label', 'value', 'required', 'center', 'zoom', 'icon'],

        created() {
            this.map = {
                center: this.center,
                zoom: this.zoom,
                options: {
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: false,
                    scrollwheel: false
                }
            }
        },

        data() {
            return {
                map: null
            }
        },

        computed: {
            field: {
                get() {
                    return this.value;
                },

                set(value) {
                    this.$emit('input', value);
                }
            },

            has_error() {
                return this.field.error !== null;
            }
        },

        watch: {
            'field.value': function() {
                if (this.field.error) {
                    this.field.error = null;
                }
            }
        },

        methods: {
            set_marker(data) {
                this.field.value = {
                    lat: data.latLng.lat(),
                    lng: data.latLng.lng()
                }
            }
        }
    }
</script>
