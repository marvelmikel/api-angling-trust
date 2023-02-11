<template>
    <div v-if="is_open" class="b-map-search">
        <h4>Start typing to search for a location.</h4>
        <input type="text" ref="input"/>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                is_open: false,
                autocomplete: null
            }
        },

        methods: {
            open() {
                this.is_open = true;

                this.$nextTick(() => {
                    this.autocomplete = new this.$root.google.maps.places.Autocomplete(this.$refs.input, {
                        types: ['(regions)']
                    });

                    this.autocomplete.addListener('place_changed', this.on_place_changed);
                });
            },

            close() {
                this.is_open = false;
            },

            on_place_changed() {
                let place = this.autocomplete.getPlace();
                this.close();

                this.$root.go_to_location({
                    lat: place.geometry.location.lat(), lng: place.geometry.location.lng()
                });

                this.$scroll_to('#map-filters', 1000, {offset: -68});
            }
        }
    }
</script>
