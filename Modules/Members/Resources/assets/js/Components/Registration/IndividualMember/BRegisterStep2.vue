<template>
    <div class="b-register-step-2">

        <div class="at-section">
            <b-wave fill="white"></b-wave>
            <div class="container">
                <div class="at-section__inner">
                    <h3 class="at-heading is-grass">Select Membership Category</h3>

                    <div class="form-row">
                        <b-select
                            name="category"
                            label="Category"
                            v-model="fields.category"
                            :options="options.category"
                            required
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="at-section">
            <b-wave fill="white"></b-wave>
            <div class="container">
                <div class="at-section__inner">
                    <h3 class="at-heading is-grass">Complete your details</h3>

                    <div class="form-row">
                        <b-select
                            name="title"
                            label="Title"
                            v-model="fields.title"
                            :options="$root.select_options.title"
                            required
                        />
                        <b-text-input
                            name="first_name"
                            label="First Name"
                            v-model="fields.first_name"
                            required
                        />
                        <b-text-input
                            name="last_name"
                            label="Last Name"
                            v-model="fields.last_name"
                            required
                        />
                    </div>

                    <div class="form-row">
                        <b-date-of-birth-input
                            name="meta_date_of_birth"
                            label="Date of Birth"
                            v-model="fields.meta.date_of_birth"
                            required
                        />
                        <b-select
                            name="meta_gender"
                            label="Gender"
                            v-model="fields.meta.gender"
                            :options="$root.select_options.gender"
                        />
                        <b-select
                            name="meta_ethnicity"
                            label="Ethnicity"
                            v-model="fields.meta.ethnicity"
                            :options="$root.select_options.ethnicity"
                            required
                        />
                    </div>

                    <div class="form-row">
                        <b-text-input
                            name="home_telephone"
                            label="Home Telephone"
                            v-model="fields.home_telephone"
                        />
                        <b-text-input
                            name="mobile_telephone"
                            label="Mobile Telephone"
                            v-model="fields.mobile_telephone"
                        />
                    </div>

                    <h4 class="at-heading is-grass">Disability</h4>

                    <div class="form-row">
                        <b-radio-input
                            name="meta_disability_1"
                            label="Do you have any physical or mental health conditions or illnesses that have lasted or are expected to last 12 months or more?"
                            :options="$root.select_options.disability_1"
                            v-model="fields.meta.disability_1"
                            required
                        />
                    </div>

                    <div v-if="fields.meta.disability_1.value === 'yes'" class="form-row">
                        <b-radio-input
                            name="meta_disability_2"
                            label="Do these physical or mental health conditions or illnesses has a substantial effect on your ability to do normal daily activities?"
                            :options="$root.select_options.disability_2"
                            v-model="fields.meta.disability_2"
                            required
                        />
                    </div>

                    <div v-if="fields.meta.disability_1.value === 'yes'" class="form-row">
                        <b-checkbox-input
                            name="meta_disability_3"
                            label="Does this disability or illness affect you in any of the following areas?"
                            :options="$root.select_options.disability_3"
                            v-model="fields.meta.disability_3"
                            multiple
                        />
                    </div>

                    <h4 class="at-heading is-grass">Address</h4>
                    <p class="at-text-red">Please start to enter the first line of your address (not the postcode) and then select the correct address from the list that appears</p>

                    <b-address-lookup v-if="$window.google" v-on:address_fetched="set_address" />
                    <br>

                    <div class="form-row">
                        <b-text-input
                            name="address_line_1"
                            label="Line 1"
                            v-model="fields.address_line_1"
                            required
                        />
                    </div>
                    <div class="form-row">
                        <b-text-input
                            name="address_line_2"
                            label="Line 2"
                            v-model="fields.address_line_2"
                        />
                        <b-text-input
                            name="address_town"
                            label="Town"
                            v-model="fields.address_town"
                            required
                        />
                    </div>
                    <div class="form-row">
                        <b-text-input
                            name="address_county"
                            label="County"
                            v-model="fields.address_county"
                            required
                        />
                        <b-text-input
                            name="address_postcode"
                            label="Postcode"
                            v-model="fields.address_postcode"
                            required
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="at-section">
            <b-wave fill="white"></b-wave>
            <div class="container">
                <div class="section__inner">
                    <button class="at-btn is-green is-solid" @click="next" :disabled="is_submitting">Save & Continue</button>
                </div>
            </div>
        </div>

        <b-modal v-if="$root.member" ref="under_13_message">
            <p>If you would like to join as a Junior member and you are under 13 years of age we will need parental consent.  Please ask your parent or guardian to contact the Membership team on 0343 507 7006 (Option 1)</p>
            <h3>Membership Number: <span v-text="$root.member.reference"></span></h3>
        </b-modal>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                is_submitting: false,
                options: {
                    category: [
                        {
                            id: 'adult',
                            name: 'Adult – 22-64 years'
                        },
                        {
                            id: 'young-adult',
                            name: 'Young Adult - 18-21 years'
                        },
                        {
                            id: 'junior',
                            name: 'Junior – up to 17 years'
                        },
                        {
                            id: 'senior-citizen',
                            name: 'Senior Citizen – 65 years plus'
                        },
                        {
                            id: 'life',
                            name: 'Life'
                        },
                        {
                            id: 'life-membership-premier',
                            name: 'Life Premier'
                        }
                    ]
                },
                fields: {
                    category: {
                        value: null, error: null
                    },
                    title: {
                        value: null, error: null
                    },
                    first_name: {
                        value: null, error: null
                    },
                    last_name: {
                        value: null, error: null
                    },
                    home_telephone: {
                        value: null, error: null
                    },
                    mobile_telephone: {
                        value: null, error: null
                    },
                    address_line_1: {
                        value: null, error: null
                    },
                    address_line_2: {
                        value: null, error: null
                    },
                    address_town: {
                        value: null, error: null
                    },
                    address_county: {
                        value: null, error: null
                    },
                    address_postcode: {
                        value: null, error: null
                    },
                    meta : {
                        date_of_birth: {
                            value: null, error: null
                        },
                        gender: {
                            value: null, error: null
                        },
                        ethnicity: {
                            value: null, error: null
                        },
                        disability_1: {
                            value: null, error: null
                        },
                        disability_2: {
                            value: null, error: null
                        },
                        disability_3: {
                            value: [], error: null
                        },
                    },
                }
            }
        },

        watch: {
            'fields.meta.date_of_birth.value': function(value) {
                if (!value) {
                    return;
                }

                let dob = value.year + '-' + value.month + '-' + value.day;
                let cutoff = new Date();

                cutoff.setMonth(cutoff.getMonth() - (12 * 13));

                if (dob > cutoff.toISOString()) {
                    this.$refs.under_13_message.open();
                    this.is_submitting = true;
                } else {
                    this.is_submitting = false;
                }
            }
        },

        methods: {
            set_address(address) {
                this.fields.address_line_1.value = address.line_1;
                this.fields.address_line_2.value = address.line_2;
                this.fields.address_town.value = address.town;
                this.fields.address_county.value = address.county;
                this.fields.address_postcode.value = address.postcode;
            },

            next() {
                this.is_submitting = true;

                this.$laravel_api.post('auth/register/step-2', this.$fields.serialise_fields(this.fields))
                    .then(({data}) => {
                        if (data.success) {
                            this.$root.member = data.data.member;
                            this.$root.to_step(3);
                        } else {
                            this.is_submitting = false;

                            if (data.error.code === 422) {
                                this.$fields.fill_errors(this.fields, data.data.errors);
                                this.$toast.error('You have validation errors, please make sure you filled out all required fields.');
                            }
                        }
                    })
                    .catch((response) => {
                        this.$toast.error();
                        this.is_submitting = false;
                    })
            }
        }
    }
</script>
