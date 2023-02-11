<template>
    <b-map-modal v-if="show" :header="true" :title="shop.name" v-on:close="close" class="is-pebble">
        <b-tabs id="fishing-location-tabs">
            <template v-slot:nav>
                <b-tabs-nav-item tab="details" is-active>
                    Details
                </b-tabs-nav-item>
            </template>
            <template v-slot:tabs>
                <b-tabs-tab tab="details" is-active>
                    <p v-if="shop.address">Address: {{ shop.address }}</p>
                    <p v-if="shop.phone_number">Telephone: <a :href="`tel:${shop.phone_number}`">{{ shop.phone_number }}</a></p>
                    <p v-if="shop.email">Email: <a :href="`mailto:${shop.email}`">{{ shop.email }}</a></p>
                    <p v-if="shop.website">Website: <a :href="shop.website">{{ shop.website }}</a></p>
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
                shop: null
            }
        },

        computed: {

        },

        methods: {
            open(shop) {
                this.shop = shop;
                this.show = true;
            },

            close() {
                this.show = false;
                this.shop = null;
            },

            update_details() {
                this.show = false;
                this.$root.open_edit_shop_modal(this.shop);
                this.shop = null;
            }
        }
    }
</script>
