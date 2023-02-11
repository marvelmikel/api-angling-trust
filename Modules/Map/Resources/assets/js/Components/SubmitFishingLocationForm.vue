<template>
    <div class="submit-fishing-location-form">
        <div class="form-row">
            <b-location-input
                v-if="edit_mode !== ''"
                name="location"
                label="Location"
                icon="fishing-location-marker.svg"
                v-model="fields.location"
                :center="$root.map.center"
                :zoom="$root.map.zoom"
                required
            />
        </div>
        <div class="form-row">
            <b-text-input
                name="name"
                label="Venue Name"
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
                label="Telephone"
                v-model="fields.phone_number"
            />
        </div>
        <div class="form-row">
            <b-text-input
                name="nearest_town"
                label="Nearest Town/City"
                v-model="fields.nearest_town"
            />

            <b-text-input
                name="nearest_county"
                label="County"
                v-model="fields.nearest_county"
            />
        </div>
        <div class="form-row">
            <b-text-input
                name="location_description"
                label="Directions"
                v-model="fields.location_description"
            />
        </div>
        <div class="form-row">
            <b-select2-input
                name="description"
                label="Permits Available"
                v-model="fields.description"
                :options="select_options.permits"
                :options_are_not_entities="true"
                multiple
            />

            <b-text-input
                name="permit_other"
                label="Permit Details"
                v-model="fields.permit_other"
            />
        </div>
        <div class="form-row">
            <b-checkbox-input
                name="disabled_facility"
                label="Disabled Facility"
                v-model="fields.disabled_facility"
            />
        </div>
        <div class="form-row">
            <b-select
                name="water_type"
                label="Water Type"
                v-model="fields.water_type"
                :options="$root.select_options.water_type"
            />
        </div>
        <div class="form-row">
            <b-text-input
                name="river_kilometers"
                label="River Kilometres"
                v-model="fields.river_kilometers"
            />

            <b-text-input
                name="stillwater_hectares"
                label="Stillwater Hectares"
                v-model="fields.stillwater_hectares"
            />
        </div>
        <div class="form-row">
            <b-select2-input
                name="discipline_id"
                label="Fishery Type"
                v-model="fields.discipline_id"
                :options="filtered_disciplines"
                multiple
            />

            <b-select2-input
                name="species"
                label="Species"
                v-model="fields.species"
                :options="$root.species"
                multiple
            />
        </div>
        <div class="form-row">
            <b-checkbox-input
                name="fishery_stocked"
                label="Fishery Stocked"
                v-model="fields.fishery_stocked"
            />

            <b-checkbox-input
                name="fly_only_restriction"
                label="Fly Only Restriction"
                v-model="fields.fly_only_restriction"
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
                fishing_location_id: null,
                select_options: {
                    permits: [
                        'Day',
                        'Season',
                        'Membership Only',
                        'None Required'
                    ]
                },
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
                    nearest_town: {
                        value: null, error: null
                    },
                    nearest_county: {
                        value: null, error: null
                    },
                    location_description: {
                        value: null, error: null
                    },
                    description: {
                        value: null, error: null
                    },
                    permit_other: {
                        value: null, error: null
                    },
                    water_type: {
                        value: null, error: null
                    },
                    river_kilometers: {
                        value: null, error: null
                    },
                    stillwater_hectares: {
                        value: null, error: null
                    },
                    discipline_id: {
                        value: null, error: null
                    },
                    species: {
                        value: null, error: null
                    },
                    disabled_facility: {
                        value: null, error: null
                    },
                    fishery_stocked: {
                        value: null, error: null
                    },
                    fly_only_restriction: {
                        value: null, error: null
                    }
                }
            }
        },

        computed: {
            can_submit() {
                return !this.is_submitting && this.gr_response !== null;
            },

            filtered_disciplines() {
                let disciplines = [];

                for_each(this.$root.select_options.disciplines, function(discipline) {
                    if (discipline.id !== 'recreation' && discipline.id !== 'specimen') {
                        disciplines.push(discipline);
                    }
                });

                return disciplines;
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

                let endpoint = '/wp-json/barques/map/submit/fishing-location';

                if (this.edit_mode === '') {
                    endpoint = `/wp-json/barques/map/submit/fishing-location/${this.fishing_location_id}`;
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

            fill_fields(fishing_location) {
                this.fishing_location_id = fishing_location.ID;
                this.$fields.fill_fields(this.fields, fishing_location);
            }
        }
    }
</script>
