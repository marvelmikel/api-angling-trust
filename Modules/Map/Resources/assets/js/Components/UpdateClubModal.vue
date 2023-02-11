<template>
    <b-map-modal v-if="show" :header="true" title="Update Details" v-on:close="close" class="is-grey">
        <div class="map-submit">
            <submit-club-form ref="update_club_form" v-on:saved="close" v-on:cancel="close" edit_mode></submit-club-form>
        </div>
    </b-map-modal>
</template>

<script>
    export default {
        data() {
            return {
                show: false,
                club: null,
            }
        },

        methods: {
            open(club) {
                this.club = club;
                this.show = true;

                setTimeout(() => {
                    this.$refs.update_club_form.fill_fields(club);
                }, 200);
            },

            close() {
                this.show = false;
                this.club = null;
                this.$root.$refs.update_thank_you_modal.open();
            }
        }
    }
</script>
