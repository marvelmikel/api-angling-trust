<template>
    <div class="individual-form">

        <div class="at-section">
            <div class="container">
                <div class="at-section__inner">
                    <h4 class="at-heading is-grass">Your Details</h4>

                    <div class="form-row">
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
                        <b-text-input
                            name="email"
                            label="Email"
                            v-model="fields.email"
                            required
                        />
                        <b-text-input
                            name="contact_number"
                            label="Contact Number"
                            v-model="fields.contact_number"
                            required
                        />
                    </div>
                    <div class="form-row">
                        <b-date-of-birth-input
                            name="date_of_birth"
                            label="Date of Birth"
                            v-model="fields.date_of_birth"
                            required
                        />
                        <b-text-input
                            name="disability"
                            label="Disability"
                            v-model="fields.disability"
                        />
                    </div>
                    <div class="form-row">
                        <b-text-input
                            name="fishing_licence_number"
                            label="Fishing Licence Number"
                            v-model="fields.fishing_licence_number"
                            required
                        />
                    </div>

                    <br>

                    <h4 class="at-heading is-grass">Parent/Guardian Details</h4>

                    <div class="form-row">
                        <b-text-input
                            name="parent_name"
                            label="Name"
                            v-model="fields.parent.name"
                            required
                        />
                    </div>
                    <div class="form-row">
                        <b-text-input
                            name="email"
                            label="Email"
                            v-model="fields.parent.email"
                            required
                        />

                        <b-text-input
                            name="telephone"
                            label="Telephone"
                            v-model="fields.parent.telephone"
                            required
                        />
                    </div>
                    <div class="form-row display-block">
                        <b-checkbox-input
                            name="consent_photos"
                            label="Photos Consent Tick Box"
                            v-model="fields.consent.photos"
                        />

                        <b-checkbox-input
                            name="consent_medical_treatment"
                            label="Medical Treatment Consent Tick Box"
                            v-model="fields.consent.medical_treatment"
                        />
                    </div>

                    <br>

                    <h4 class="at-heading is-grass">Address</h4>

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
                        />
                        <b-text-input
                            name="address_postcode"
                            label="Postcode"
                            v-model="fields.address_postcode"
                            required
                        />
                    </div>

                     <span v-if="$root.event.bursary_code">
                        <h4 class="at-heading is-grass">Bursary Code</h4>
                        <div class="form-row">
                            <b-text-input
                                name="bursary_code"
                                label="Bursary Code"
                                v-model="fields.bursary_code"
                                v-on:blur="validateBursaryCode"
                            />
                        </div>
                        <div class="valid-bursary" v-if="valid_bursary_code">
                            Bursary Code recognised. Your 50% discount has been applied.
                        </div>
                    </span>

                    <template v-if="$root.event.post_type === 'competition' && anti_doping">
                        <div class="form-row anti-doping-checkbox">
                            <b-checkbox-input
                                name="anti_doping_policy"
                                :label="anti_doping.agreement"
                                v-model="fields.anti_doping_policy"
                                required
                            />
                        </div>
                        <div class="anti-doping-link">
                            <a :href="anti_doping.policy.url" :target="anti_doping.policy.target">{{ anti_doping.policy.title }}</a>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="at-section" v-if="$root.event.has_pools_payments">
            <div class="container">
                <div class="at-section__inner">
                    <h4 class="at-heading is-grass">Pools</h4>

                    <div class="form-row pool-payment-checkbox" v-for="(payment,index) in $root.event.pools_payments">
                        <b-checkbox-input
                            :name="`pools_payment_${index}`"
                            :label="`${payment.label} (+${pool_price(payment.amount)})`"
                            v-model="fields.pools_payment[index]"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="at-section">
            <div class="container">
                <div class="at-section__inner">
                    <button class="at-btn is-green is-solid" @click="next">Next</button>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props: ['anti_doping_data'],

        created() {
            if (this.anti_doping_data) {
                this.anti_doping = JSON.parse(this.anti_doping_data);
            }

            if (this.$root.member) {
                this.fields.first_name.value = this.$root.member.first_name;
                this.fields.last_name.value = this.$root.member.last_name;
                this.fields.email.value = this.$root.member.user.email;
                this.fields.date_of_birth.value = this.$root.member.meta.date_of_birth;
                this.fields.fishing_licence_number.value = this.$root.member.meta.fishing_licence_number;
                this.fields.address_line_1.value = this.$root.member.address_line_1;
                this.fields.address_line_2.value = this.$root.member.address_line_2;
                this.fields.address_town.value = this.$root.member.address_town;
                this.fields.address_county.value = this.$root.member.address_county;
                this.fields.address_postcode.value = this.$root.member.address_postcode;
            }
        },

        data() {
            return {
                valid_bursary_code: false,
                fields: {
                    first_name: {
                        value: null, error: null
                    },
                    last_name: {
                        value: null, error: null
                    },
                    email: {
                        value: null, error: null
                    },
                    contact_number: {
                        value: null, error: null
                    },
                    date_of_birth: {
                        value: null, error: null
                    },
                    disability: {
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
                    fishing_licence_number: {
                        value: null, error: null
                    },
                    parent: {
                        name: {
                            value: null, error: null
                        },
                        email: {
                            value: null, error: null
                        },
                        telephone: {
                            value: null, error: null
                        }
                    },
                    consent: {
                        photos: {
                            value: null, error: null
                        },
                        medical_treatment: {
                            value: null, error: null
                        }
                    },
                    anti_doping_policy: {
                        value: null, error: null
                    },
                    pools_payment: [
                        {
                            value: null, error: null
                        },
                        {
                            value: null, error: null
                        },
                        {
                            value: null, error: null
                        },
                        {
                            value: null, error: null
                        },
                        {
                            value: null, error: null
                        }
                    ]
                }
            }
        },

        methods: {
            pool_price(amount) {
                let price = amount / 100;
                return '£' + price.toFixed(2);
            },

            next() {
                let data = this.$fields.serialise_fields(this.fields);
                data['ticket_basket_token'] = this.$window.get_cookie('TICKET_BASKET_TOKEN');

                this.$laravel_api.post(`any/tickets/${this.$root.ticket.ref}/purchase`, data)
                    .then(({data}) => {
                        if (data.success) {
                            this.$emit('next', data);
                        } else {
                            this.is_submitting = false;

                            if (data.error.code === 422) {
                                this.$toast.error("There are errors in your submission.");
                                this.$fields.fill_errors(this.fields, data.data.errors);
                            }
                        }
                    })
                    .catch((response) => {
                        this.is_submitting = false;
                        this.$toast.error();
                    });
            },
            validateBursaryCode() {
                if(this.fields.bursary_code.value === null) {
                    return;
                }
                if(this.fields.bursary_code.value.trim() !== this.$root.event.bursary_code.trim()) {
                    this.fields.bursary_code.error = "Bursary code not recognised";
                    this.valid_bursary_code = false;
                    this.$root.ticket.formatted_price = "£" + ((this.$root.ticket.price / 100)).toFixed(2);
                } else {
                    this.fields.bursary_code.error = null;
                    this.valid_bursary_code = true;
                    this.$root.ticket.formatted_price = "£" + (((this.$root.ticket.price / 100) / 2)).toFixed(2);
                }
            }
        }
    }
</script>
