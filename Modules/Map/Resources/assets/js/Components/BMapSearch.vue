<template>
    <div class="b-map-search">
        <input v-model="input" type="text" ref="input" placeholder=""/>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                is_open: false,
                autocomplete: null,
                input: null
            }
        },

        mounted() {
            setTimeout(() => {
                this.autocomplete = new this.$root.google.maps.places.Autocomplete(this.$refs.input, {
                    types: ['(regions)']
                });

                this.autocomplete.addListener('place_changed', this.on_place_changed);

                this.autocomplete.setComponentRestrictions({
                    'country': ['uk']
                });
            }, 1000);
        },

        methods: {
            on_place_changed() {
                let place = this.autocomplete.getPlace();

                this.$emit('search_complete', {
                    lat: place.geometry.location.lat(), lng: place.geometry.location.lng()
                });

                this.input = '';
            }
        }
    }
</script>
