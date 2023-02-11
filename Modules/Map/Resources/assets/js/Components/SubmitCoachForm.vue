<template>
    <div class="submit-coach-form">
        <div class="form-row">
            <b-location-input
                v-if="edit_mode !== ''"
                name="location"
                label="Location"
                icon="coach-marker.svg"
                v-model="fields.location"
                :center="$root.map.center"
                :zoom="$root.map.zoom"
                required
            />
        </div>
        <div class="form-row">
            <b-text-input
                name="name"
                label="Name"
                v-model="fields.name"
                required
            />

            <b-text-input
                name="email"
                label="Email"
                v-model="fields.email"
            />
        </div>
        <div class="form-row">
            <b-text-input
                name="phone_number"
                label="Phone Number"
                v-model="fields.phone_number"
            />

            <b-text-input
                name="website"
                label="Website"
                v-model="fields.website"
            />
        </div>
        <div class="form-row">
            <b-text-input
                name="postcode"
                label="Postcode"
                v-model="fields.postcode"
            />
        </div>
        <div class="form-buttons-wrapper">
            <div class="form-buttons is-left">
                <vue-recaptcha
                    :sitekey="$window.GOOGLE_RECAPTCHA_SITE_KEY"
                    v-on:verify="verify"
                    v-on:expired="expired"
                ></vue-recaptcha>
            </div>
            <div class="form-buttons is-right">
                <button class="at-btn is-green is-solid" @click="submit" :disabled="!can_submit">Submit</button>
                <button class="at-btn is-red is-solid" @click="$emit('cancel')" :disabled="is_submitting">Cancel</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['edit_mode'],

        data() {
            return {
                is_submitting: false,
                gr_response: null,
                coach_id: null,
                fields: {
                    location: {
                        value: null, error: null
                    },
                    name: {
                        value: null, error: null
                    },
                    email: {
                        value: null, error: null
                    },
                    phone_number: {
                        value: null, error: null
                    },
                    website: {
                        value: null, error: null
                    },
                    postcode: {
                        value: null, error: null
                    }
                }
            }
        },

        computed: {
            can_submit() {
                return !this.is_submitting && this.gr_response !== null;
            }
        },

        methods: {
            verify(response) {
                this.gr_response = response;
            },

            expired() {
                this.gr_response = null;
            },

            submit() {
                this.is_submitting = true;

                if (!this.validate()) {
                    this.is_submitting = false;
                    return;
                }

                let data = this.$fields.serialise_fields(this.fields);
                data['gr_response'] = this.gr_response;

                let endpoint = '/wp-json/barques/map/submit/coach';

                if (this.edit_mode === '') {
                    endpoint = `/wp-json/barques/map/submit/coach/${this.coach_id}`;
                }

                this.$wp_api.post(endpoint, data)
                    .then(({data}) => {
                        this.$emit('saved');
                        this.is_submitting = false;
                    })
                    .catch((response) => {
                        console.log(response);
                        this.$toast.error();
                        this.is_submitting = false;
                    });
            },

            validate() {
                let valid = true;

                if (this.edit_mode !== '' && !this.fields.location.value) {
                    this.fields.location.error = 'The location is required.';
                    valid = false;
                }

                if (!this.fields.name.value) {
                    this.fields.name.error = 'The name is required.';
                    valid = false;
                }

                return valid;
            },

            fill_fields(coach) {
                this.coach_id = coach.ID;
                this.$fields.fill_fields(this.fields, coach);
            }
        }
    }
</script>
