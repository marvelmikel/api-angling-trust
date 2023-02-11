<template>
    <b-map-modal v-if="show" :header="true" title="Update Details" v-on:close="close" class="is-aqua">
        <div class="map-submit">
            <submit-fishing-location-form ref="update_fishing_location_form" v-on:saved="close" v-on:cancel="close" edit_mode></submit-fishing-location-form>
        </div>
    </b-map-modal>
</template>

<script>
    export default {
        data() {
            return {
                show: false,
                fishing_location: null,
            }
        },

        methods: {
            open(fishing_location) {
                this.fishing_location = fishing_location;
                this.show = true;

                setTimeout(() => {
                    this.$refs.update_fishing_location_form.fill_fields(fishing_location);
                }, 200);
            },

            close() {
                this.show = false;
                this.fishing_location = null;
                this.$root.$refs.update_thank_you_modal.open();
            }
        }
    }
</script>
