<template>
    <b-map-modal v-if="show" :header="true" :title="club.name" v-on:close="close" class="is-grey">
        <b-tabs id="fishing-location-tabs">
            <template v-slot:nav>
                <b-tabs-nav-item tab="details" is-active>
                    Details
                </b-tabs-nav-item>
            </template>
            <template v-slot:tabs>
                <b-tabs-tab tab="details" is-active>
                    <p v-if="club.address">Address: {{ club.address }}</p>
                    <p v-if="club.phone_number">Telephone: <a :href="`tel:${club.phone_number}`">{{ club.phone_number }}</a></p>
                    <p v-if="club.email">Email: <a :href="`mailto:${club.email}`">{{ club.email }}</a></p>
                    <p v-if="club.website">Website: <a :href="club.website">{{ club.website }}</a></p>
                </b-tabs-tab>
            </template>
        </b-tabs>
        <div class="actions">
            <button class="at-btn is-white is-frame" @click="update_details">Update Details</button>
        </div>
    </b-map-modal>
</template>

<script>
    export default {
        data() {
            return {
                show: false,
                club: null
            }
        },

        computed: {

        },

        methods: {
            open(club) {
                this.club = club;
                this.show = true;
            },

            close() {
                this.show = false;
                this.club = null;
            },

            update_details() {
                this.show = false;
                this.$root.open_edit_club_modal(this.club);
                this.club = null;
            }
        }
    }
</script>
