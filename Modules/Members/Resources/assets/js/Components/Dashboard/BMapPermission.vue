<template>
    <div class="b-map-permission">
        <b-modal ref="modal">
            <div class="b-map-permission__text" v-html="text"></div>
            <div class="b-map-permission__actions">
                <button class="at-btn is-solid is-blue" @click="allow" :disabled="is_submitting">Allow</button>
                <button class="at-btn is-solid is-grey" @click="deny" :disabled="is_submitting">Deny</button>
            </div>
        </b-modal>
    </div>
</template>

<script>
    export default {
        props: ['text'],

        mounted() {
            if (typeof this.$root.member.meta.map_permission === 'undefined') {
                this.$refs.modal.open();
            }
        },

        data() {
            return {
                is_submitting: false
            }
        },

        methods: {
            allow() {
                this.is_submitting = true;

                this.$laravel_api.post('members/me/update-map-permission', {
                    allow: true
                }).then(() => {
                    this.$refs.modal.close();
                    this.is_submitting = false;
                });
            },

            deny() {
                this.is_submitting = true;

                this.$laravel_api.post('members/me/update-map-permission', {
                    allow: false
                }).then(() => {
                    this.$refs.modal.close();
                    this.is_submitting = false;
                });
            }
        }
    }
</script>

<style type="text/css">
    .b-map-permission .b-modal {
        max-width: 750px;
    }

    .b-map-permission__text {
        margin-bottom: 1.5rem;
    }
</style>
