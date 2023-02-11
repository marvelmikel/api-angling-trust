<template>
    <div class="map-quick-start map-card no-clickthrough">
        <div class="map-card__inner">
            <div v-if="step === 1">
                <h3>Select a Category</h3>
                <div class="location-type-buttons">
                    <img
                        class="map-button"
                        v-tooltip="{ content: 'River Levels', placement: 'bottom' }"
                        :src="$window.laravel_url + '/img/station-icon.svg'"
                        @click="select_category('stations')"
                    />
                    <button
                        v-tooltip="{ content: 'Venues', placement: 'bottom' }"
                        class="map-button is-icon is-aqua"
                        @click="select_category('fishing_locations')"
                    >
                        <i class="fal fa-fish"></i>
                    </button>
                    <img
                        v-tooltip="{ content: 'Coaches', placement: 'bottom' }"
                        class="map-button"
                        :src="$window.laravel_url + '/img/coach-icon.svg'"
                        @click="select_category('coaches')"
                    />
                    <img
                        v-tooltip="{ content: 'Tackle Shops', placement: 'bottom' }"
                        class="map-button"
                        :src="$window.laravel_url + '/img/shop-icon.svg'"
                        @click="select_category('shops')"
                    />
                    <img
                        v-tooltip="{ content: 'Clubs', placement: 'bottom' }"
                        class="map-button"
                        :src="$window.laravel_url + '/img/club-icon.svg'"
                        @click="select_category('clubs')"
                    />
                                        <img
                        v-tooltip="{
                            content: 'Charter Boats',
                            placement: 'bottom'
                        }"
                        class="map-button"
                        :src="
                            $window.laravel_url + '/img/charter-boats-icon.svg'
                        "
                        @click="select_category('charter_boats')"
                    />
                </div>
            </div>
            <div v-if="step === 2">
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
    </div>
</template>

<script>
    export default {
        data() {
            return {
                step: 1
            }
        },

        methods: {
            select_category(category) {
                this.$root.filters.location_types[category] = true;
                this.step = 2;
            },

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
