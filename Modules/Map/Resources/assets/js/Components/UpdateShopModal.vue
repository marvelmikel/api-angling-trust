<template>
    <b-map-modal v-if="show" :header="true" title="Update Details" v-on:close="close" class="is-pebble">
        <div class="map-submit">
            <submit-shop-form ref="update_shop_form" v-on:saved="close" v-on:cancel="close" edit_mode></submit-shop-form>
        </div>
    </b-map-modal>
</template>

<script>
    export default {
        data() {
            return {
                show: false,
                shop: null,
            }
        },

        methods: {
            open(shop) {
                this.shop = shop;
                this.show = true;

                setTimeout(() => {
                    this.$refs.update_shop_form.fill_fields(shop);
                }, 200);
            },

            close() {
                this.show = false;
                this.shop = null;
                this.$root.$refs.update_thank_you_modal.open();
            }
        }
    }
</script>
