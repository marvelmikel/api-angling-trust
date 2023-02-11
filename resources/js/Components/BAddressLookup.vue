<template>
    <div v-if="!completed" class="b-address-lookup-field">
        <input type="text" ref="input" placeholder="Start typing your address to search"/>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.$nextTick(() => {
                this.autocomplete = new this.$window.google.maps.places.Autocomplete(this.$refs.input);
                this.autocomplete.addListener('place_changed', this.on_place_changed);
                this.autocomplete.setComponentRestrictions({
                    'country': ['uk']
                });
            });
        },

        data() {
            return {
                completed: false
            }
        },

        methods: {
            on_place_changed() {
                let place = this.autocomplete.getPlace();
                let parts = [];

                place.address_components.forEach((component) => {
                    let key = component.types[0];
                    parts[key] = component.long_name;
                });

                let address = {
                    line_1: '',
                    line_2: '',
                    town: '',
                    county: '',
                    postcode: ''
                };

                if (parts.street_number) {
                    address.line_1 += parts.street_number + ' ';
                }

                if (parts.route) {
                    address.line_1 += parts.route;
                }

                if (parts.postal_town) {
                    address.town += parts.postal_town;
                }

                if (parts.administrative_area_level_2) {
                    address.county = parts.administrative_area_level_2;
                }

                if (parts.postal_code) {
                    address.postcode += parts.postal_code;
                }

                this.$emit('address_fetched', address);
                this.completed = true;
            }
        }
    }
</script>
