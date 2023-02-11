<template>
    <div class="map-quick-start map-card no-clickthrough">
        <div class="map-card__inner">
            <h3>Enter a Location</h3>
            <b-map-search
                ref="map_search"
                v-on:search_complete="select_location"
            ></b-map-search>
            <p>or</p>
            <button class="at-btn is-blue is-solid has-icon" @click="select_my_location">
                <i class="fas fa-location-arrow"></i> <span>Use My Location</span>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {

            }
        },

        methods: {
            select_location({lat, lng}) {
                this.$root.show_quick_start = false;
                this.step = 1;

                this.$root.go_to_location({
                    lat: lat, lng: lng
                }).then(() => {
                    this.$root.ensure_zoomed_in();

                    if (this.$window.matchMedia("(min-width: 992px)").matches) {
                        this.$root.filter_pane = true;
                    }
                });
            },

            select_my_location() {
                this.$root.show_quick_start = false;
                this.step = 1;

                this.$root.use_my_location()
                    .then(() => {
                        this.$root.ensure_zoomed_in();

                        if (this.$window.matchMedia("(min-width: 992px)").matches) {
                            this.$root.filter_pane = true;
                        }
                    });
            }
        }
    }
</script>
