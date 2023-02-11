<template>
    <b-map-modal v-if="show" :header="true" title="Update Details" v-on:close="close" class="is-grass">
        <div class="map-submit">
            <submit-coach-form ref="update_coach_form" v-on:saved="close" v-on:cancel="close" edit_mode></submit-coach-form>
        </div>
    </b-map-modal>
</template>

<script>
    export default {
        data() {
            return {
                show: false,
                coach: null,
            }
        },

        methods: {
            open(coach) {
                this.coach = coach;
                this.show = true;

                setTimeout(() => {
                    this.$refs.update_coach_form.fill_fields(coach);
                }, 200);
            },

            close() {
                this.show = false;
                this.coach = null;
                this.$root.$refs.update_thank_you_modal.open();
            }
        }
    }
</script>
