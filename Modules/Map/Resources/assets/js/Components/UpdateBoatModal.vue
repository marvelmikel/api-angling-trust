<template>
    <b-map-modal
        v-if="show"
        :header="true"
        title="Update Details"
        v-on:close="close"
        class="is-midnight"
    >
        <div class="map-submit">
            <submit-boat-form
                ref="update_boat_form"
                v-on:saved="close"
                v-on:cancel="close"
                edit_mode
            ></submit-boat-form>
        </div>
    </b-map-modal>
</template>

<script>
export default {
    data() {
        return {
            show: false,
            boat: null
        };
    },
    methods: {
        open(boat) {
            this.boat = boat;
            this.show = true;
            setTimeout(() => {
                this.$refs.update_boat_form.fill_fields(boat);
            }, 200);
        },
        close() {
            this.show = false;
            this.boat = null;
            this.$root.$refs.update_thank_you_modal.open();
        }
    }
};
</script>
