<template>
    <div class="map-filter-pane" :class="{'is-open': $root.filter_pane}">
        <div class="map-filter-pane__header">
            <h3 class="map-filter-pane__title">Filter</h3>
            <button class="map-button is-white is-solid map-filter-pane__close-btn" @click="$root.filter_pane = false">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="map-filter-pane__body">
            <div class="map-filter-pane__filters">
                <div class="map-filter-pane__filter location-filter">
                    <h4>Location</h4>
                    <div class="filter-pane-location-search">
                        <b-map-search
                            ref="map_search"
                            v-on:search_complete="select_location"
                        ></b-map-search>
                        <button class="map-button is-blue is-solid has-icon" @click="select_my_location" v-tooltip="{ content: 'Use My Location', placement: 'bottom' }">
                            <i class="fas fa-location-arrow"></i>
                        </button>
                    </div>
                </div>
                <div class="map-filter-pane__filter category-filter">
                    <h4>Category</h4>
                    <div class="location-type-buttons">
                        <img
                            class="map-button"
                            :src="$window.laravel_url + '/img/station-icon.svg'"
                            v-tooltip="{ content: 'River Levels', placement: 'bottom' }"
                            :class="{ 'is-inactive' : !$root.show_stations }"
                            @click="select_category('stations')"
                        />
                        <button
                            class="map-button is-icon is-aqua"
                            v-tooltip="{ content: 'Venues', placement: 'bottom' }"
                            :class="{ 'is-inactive' : !$root.show_fishing_locations }"
                            @click="select_category('fishing_locations')"
                        >
                            <i class="fal fa-fish"></i>
                        </button>
                        <img
                            class="map-button"
                            :src="$window.laravel_url + '/img/coach-icon.svg'"
                            v-tooltip="{ content: 'Coaches', placement: 'bottom' }"
                            :class="{ 'is-inactive' : !$root.show_coaches }"
                            @click="select_category('coaches')"
                        />
                        <img
                            class="map-button"
                            :src="$window.laravel_url + '/img/shop-icon.svg'"
                            v-tooltip="{ content: 'Tackle Shops', placement: 'bottom' }"
                            :class="{ 'is-inactive' : !$root.show_shops }"
                            @click="select_category('shops')"
                        />
                        <img
                            class="map-button"
                            :src="$window.laravel_url + '/img/club-icon.svg'"
                            v-tooltip="{ content: 'Clubs', placement: 'bottom' }"
                            :class="{ 'is-inactive' : !$root.show_clubs }"
                            @click="select_category('clubs')"
                        />
                                                <img
                            class="map-button"
                            :src="
                                $window.laravel_url +
                                    '/img/charter-boats-icon.svg'
                            "
                            v-tooltip="{
                                content: 'Charter Boats',
                                placement: 'bottom'
                            }"
                            :class="{ 'is-inactive': !$root.show_boats }"
                            @click="select_category('charter_boats')"
                        />
                    </div>
                </div>
                <div class="map-filter-pane__filter map-type-filter">
                    <h4>Map Type</h4>
                    <div class="map-types">
                        <div
                            class="map-types__item"
                            :class="{ 'is-inactive': $root.satellite_view }"
                            v-tooltip="{ content: 'Default View', placement: 'bottom' }"
                            @click="$root.switch_to_default_view"
                        >
                            <img :src="$window.laravel_url + '/img/default-map-preview.png'"/>
                        </div>
                        <div
                            class="map-types__item"
                            :class="{ 'is-inactive': !$root.satellite_view }"
                            v-tooltip="{ content: 'Satellite View', placement: 'bottom' }"
                            @click="$root.switch_to_satellite_view"
                        >
                            <img :src="$window.laravel_url + '/img/satellite-map-preview.png'"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        methods: {
            select_location({lat, lng}) {
                this.$root.go_to_location({
                    lat: lat, lng: lng
                })
                .then(() => {
                    this.$root.ensure_zoomed_in();

                    if (this.$window.matchMedia("(max-width: 991px)").matches) {
                        this.$root.filter_pane = false;
                    }
                });
            },

            select_my_location() {
                this.$root.use_my_location()
                    .then(() => {
                        this.$root.ensure_zoomed_in();

                        if (this.$window.matchMedia("(max-width: 991px)").matches) {
                            this.$root.filter_pane = false;
                        }
                    });
            },

            select_category(location_type) {
                this.$root.filters.location_types[location_type] = !this.$root.filters.location_types[location_type];
                this.$root.debounced_load_markers();
            }
        }
    }
</script>
