<template>
    <b-map-modal v-if="show" :header="true" :title="modal_title" v-on:close="close" :class="modal_class">
        <div class="map-submit">
            <template v-if="step === 1">
                <p v-text="step_1_text"></p>
                <div class="map-submit__choices">
                    <button class="at-btn is-aqua is-solid" @click="open_form('fishing_location')">
                        <i class="fal fa-fish"></i> <span>Venue</span>
                    </button>
                    <button class="at-btn is-grass is-solid" @click="open_form('coach')">
                        <img class="button-icon" :src="$window.laravel_url + '/img/coach-icon.svg'"/> <span>Coach</span>
                    </button>
                    <button class="at-btn is-pebble is-solid" @click="open_form('shop')">
                        <img class="button-icon" :src="$window.laravel_url + '/img/shop-icon.svg'"/> <span>Tackle Shop</span>
                    </button>
                    <button class="at-btn is-grey is-solid" @click="open_form('club')">
                        <img class="button-icon" :src="$window.laravel_url + '/img/club-icon.svg'"/> <span>Club</span>
                    </button>
                                        <button
                        class="at-btn is-midnight is-solid"
                        @click="open_form('charter_boat')"
                    >
                        <img
                            class="button-icon"
                            :src="
                                $window.laravel_url +
                                    '/img/charter-boats-icon.svg'
                            "
                        />
                        <span>Charter Boat</span>
                    </button>
                </div>
            </template>
            <template v-if="step === 2">
                <submit-coach-form v-if="post_type === 'coach'" v-on:saved="saved" v-on:cancel="cancel" />
                <submit-shop-form v-if="post_type === 'shop'" v-on:saved="saved" v-on:cancel="cancel" />
                <submit-club-form v-if="post_type === 'club'" v-on:saved="saved" v-on:cancel="cancel" />
                <submit-fishing-location-form v-if="post_type === 'fishing_location'" v-on:saved="saved" v-on:cancel="cancel" />
            </template>
            <template v-if="step === 3">
                <p v-text="step_3_text"></p>
            </template>
        </div>
    </b-map-modal>
</template>

<script>
    export default {
        props: ['step_1_text', 'step_3_text'],

        data() {
            return {
                show: false,
                post_type: null,
                step: 1
            }
        },

        computed: {
            modal_title() {
                if (this.step === 2) {
                    if (this.post_type === 'coach') {
                        return 'Submit a Coach';
                    }

                    if (this.post_type === 'shop') {
                        return 'Submit a Tackle Shop';
                    }

                    if (this.post_type === 'fishing_location') {
                        return 'Submit a Venue';
                    }

                    if (this.post_type === 'club') {
                        return 'Submit a Club';
                    }

                if (this.post_type === "charter_boat") {
                    return "Submit a Charter Boat";
                }
                }

                return 'Submit an Entry';
            },

            modal_class() {
                let classes = [
                    'submit-entry-modal',
                    `is-step-${this.step}`
                ];

                if (this.step === 2) {
                    if (this.post_type === 'coach') {
                        classes.push('is-grass');
                    }

                    if (this.post_type === 'shop') {
                        classes.push('is-pebble');
                    }

                    if (this.post_type === 'fishing_location') {
                        classes.push('is-aqua');
                    }

                    if (this.post_type === 'club') {
                        classes.push('is-grey');
                    }
                } else {
                    classes.push('is-blue');
                }

                return classes.join(' ');
            }
        },

        methods: {
            open_form(post_type) {
                this.post_type = post_type;
                this.step = 2;
            },

            saved() {
                this.step = 3;
            },

            cancel() {
                this.post_type = null;
                this.step = 1;
            },

            open() {
                this.show = true;
            },

            close() {
                this.show = false;
                this.post_type = null;
                this.step = 1;
            }
        }
    }
</script>

<style>
    .map-submit__choices .button-icon {
        height: 10px;
        transform: scale(3.5) translateX(-2px);
    }

    .map-submit__choices .fa-fish {
        transform: translateX(-2px);
    }
</style>
