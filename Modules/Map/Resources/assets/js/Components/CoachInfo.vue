<template>
    <b-map-modal v-if="show" :header="true" :title="coach.name" v-on:close="close" class="is-grass">
        <b-tabs id="fishing-location-tabs">
            <template v-slot:nav>
                <b-tabs-nav-item tab="details" is-active>
                    Details
                </b-tabs-nav-item>
            </template>
            <template v-slot:tabs>
                <b-tabs-tab tab="details" is-active>
                    <p v-if="coach.address">Address: {{ coach.address }}</p>
                    <p v-if="coach.phone_number">Telephone: <a :href="`tel:${coach.phone_number}`">{{ coach.phone_number }}</a></p>
                    <p v-if="coach.email">Email: <a :href="`mailto:${coach.email}`">{{ coach.email }}</a></p>
                    <p v-if="coach.website">Website: <a :href="coach.website">{{ coach.website }}</a></p>
                    <p v-if="coach.disciplines">Preferences: {{ coach.disciplines.join(', ') }}</p>
                    <p v-if="coach.coach_level">Coach Level: {{ coach.coach_level }}</p>
                </b-tabs-tab>
            </template>
        </b-tabs>
        <div v-if="!$root.hide_update_button" class="actions">
            <button class="at-btn is-white is-frame" @click="update_details">Update Details</button>
        </div>
    </b-map-modal>
</template>

<script>
    export default {
        data() {
            return {
                show: false,
                coach: null
            }
        },

        computed: {

        },

        methods: {
            open(coach) {
                this.coach = coach;
                this.show = true;
            },

            close() {
                this.show = false;
                this.coach = null;
            },

            update_details() {
                this.show = false;
                this.$root.open_edit_coach_modal(this.coach);
                this.coach = null;
            }
        }
    }
</script>
