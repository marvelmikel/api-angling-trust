<template>
    <div class="b-register-step-2">

        <div class="at-section">
            <div class="container">
                <div class="at-section__inner">
                    <h3 class="at-heading is-grass">Club or Syndicate Details</h3>

                    <div class="form-row">
                        <b-text-input
                            name="meta_club_name"
                            label="Name"
                            v-model="fields.meta.club_name"
                            required
                        />

                        <b-select
                            name="category"
                            label="Size of club/syndicate (number of members)"
                            v-model="fields.category"
                            :options="options.category"
                            required
                        />
                    </div>
                </div>
            </div>
        </div>

        <div v-if="fields.category.value" class="at-section">
            <div class="container">
                <div class="at-section__inner">
                    <div class="heading-with-info">
                        <h3 class="at-heading is-grass">Fish Legal</h3>
                        <b-info-modal message="Test">
                            <p>Fish Legal is a unique membership association using the law to fight pollution and protect fish stocks on behalf of its members throughout the UK.  It is united in a collaborative relationship with the Angling Trust.  Fish Legal membership entitles you to <strong>expert legal advice</strong> on angling and fishery related matters.  Fish Legal can also <strong>take legal action on your behalf</strong> against environmental damage or threats to your Club, meeting the cost and <strong>passing on 100% of any compensation won</strong>.  It is excellent value for money as annual membership is much less than the cost of a few hours of these legal services elsewhere.</p>
                        </b-info-modal>
                    </div>

                    <b-checkbox-input
                        name="fl_member"
                        label="I would also like to join Fish Legal"
                        v-model="fields.fl_member"
                    ></b-checkbox-input>

                    <p v-if="fl_cost">+£{{ fl_cost }} for {{ fields.category.value }} Members</p>
                </div>
            </div>
        </div>

        <div class="at-section">
            <div class="container">
                <div class="at-section__inner">
                    <h3 class="at-heading is-grass">Your Details</h3>

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
            <div class="container">
                <div class="at-section__inner">
                    <h3 class="at-heading is-grass">Primary Contact Info</h3>

                    <div class="form-row">
                        <div class="form-input">
                            <input id="copy_from_your_details" type="checkbox" v-model="copy_from_your_details">
                            <label for="copy_from_your_details">Same as Your Details</label>
                        </div>
                    </div>

                    <div class="form-row">
                        <b-select
                            name="meta_primary_contact_title"
                            label="Title"
                            v-model="fields.meta.primary_contact.title"
                            :options="$root.select_options.title"
                            required
                        />
                        <b-text-input
                            name="meta_primary_contact_first_name"
                            label="First Name"
                            v-model="fields.meta.primary_contact.first_name"
                            required
                        />
                        <b-text-input
                            name="meta_primary_contact_last_name"
                            label="Last Name"
                            v-model="fields.meta.primary_contact.last_name"
                            required
                        />
                    </div>

                    <div class="form-row">
                        <b-text-input
                            name="meta_primary_contact_email"
                            label="Email"
                            v-model="fields.meta.primary_contact.email"
                            required
                        />
                    </div>

                    <div class="form-row">
                        <b-text-input
                            name="meta_primary_contact_home_telephone"
                            label="Home Telephone"
                            v-model="fields.meta.primary_contact.home_telephone"
                        />
                        <b-text-input
                            name="meta_primary_contact_mobile_telephone"
                            label="Mobile Telephone"
                            v-model="fields.meta.primary_contact.mobile_telephone"
                        />
                    </div>

                    <h4 class="at-heading is-grass">Address</h4>
                    <p class="at-text-red">Please start to enter the first line of your address (not the postcode) and then select the correct address from the list that appears</p>

                    <b-address-lookup v-if="$window.google" v-on:address_fetched="set_primary_contact_address" />
                    <br>

                    <div class="form-row">
                        <b-text-input
                            name="meta_primary_contact_address_line_1"
                            label="Line 1"
                            v-model="fields.meta.primary_contact.address_line_1"
                            required
                        />
                    </div>
                    <div class="form-row">
                        <b-text-input
                            name="meta_primary_contact_address_line_2"
                            label="Line 2"
                            v-model="fields.meta.primary_contact.address_line_2"
                        />
                        <b-text-input
                            name="meta_primary_contact_address_town"
                            label="Town"
                            v-model="fields.meta.primary_contact.address_town"
                            required
                        />
                    </div>
                    <div class="form-row">
                        <b-text-input
                            name="meta_primary_contact_address_county"
                            label="County"
                            v-model="fields.meta.primary_contact.address_county"
                            required
                        />
                        <b-text-input
                            name="meta_primary_contact_address_postcode"
                            label="Postcode"
                            v-model="fields.meta.primary_contact.address_postcode"
                            required
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="at-section">
            <div class="container">
                <div class="at-section__inner">
                    <div class="form-input">
                        <input id="has_additional_contact" type="checkbox" v-model="has_additional_contact">
                        <label for="has_additional_contact">Add Additional Contact</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="at-section" v-if="has_additional_contact">
            <div class="container">
                <div class="at-section__inner">
                    <h3 class="at-heading is-grass">Additional Contact Info</h3>

                    <div class="form-row">
                        <b-select
                            name="meta_additional_contact_title"
                            label="Title"
                            v-model="fields.meta.additional_contact.title"
                            :options="$root.select_options.title"
                            required
                        />
                        <b-text-input
                            name="meta_additional_contact_first_name"
                            label="First Name"
                            v-model="fields.meta.additional_contact.first_name"
                            required
                        />
                        <b-text-input
                            name="meta_additional_contact_last_name"
                            label="Last Name"
                            v-model="fields.meta.additional_contact.last_name"
                            required
                        />
                    </div>

                    <div class="form-row">
                        <b-text-input
                            name="meta_additional_contact_email"
                            label="Email"
                            v-model="fields.meta.additional_contact.email"
                            required
                        />
                    </div>

                    <div class="form-row">
                        <b-text-input
                            name="meta_additional_contact_home_telephone"
                            label="Home Telephone"
                            v-model="fields.meta.additional_contact.home_telephone"
                        />
                        <b-text-input
                            name="meta_additional_contact_mobile_telephone"
                            label="Mobile Telephone"
                            v-model="fields.meta.additional_contact.mobile_telephone"
                        />
                    </div>

                    <h4 class="at-heading is-grass">Address</h4>
                    <p class="at-text-red">Please start to enter the first line of your address (not the postcode) and then select the correct address from the list that appears</p>

                    <b-address-lookup v-if="$window.google" v-on:address_fetched="set_additional_contact_address" />
                    <br>

                    <div class="form-row">
                        <b-text-input
                            name="meta_additional_contact_address_line_1"
                            label="Line 1"
                            v-model="fields.meta.additional_contact.address_line_1"
                            required
                        />
                    </div>
                    <div class="form-row">
                        <b-text-input
                            name="meta_additional_contact_address_line_2"
                            label="Line 2"
                            v-model="fields.meta.additional_contact.address_line_2"
                        />
                        <b-text-input
                            name="meta_additional_contact_address_town"
                            label="Town"
                            v-model="fields.meta.additional_contact.address_town"
                            required
                        />
                    </div>
                    <div class="form-row">
                        <b-text-input
                            name="meta_additional_contact_address_county"
                            label="County"
                            v-model="fields.meta.additional_contact.address_county"
                            required
                        />
                        <b-text-input
                            name="meta_additional_contact_address_postcode"
                            label="Postcode"
                            v-model="fields.meta.additional_contact.address_postcode"
                            required
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="at-section">
            <div class="container">
                <div class="at-section__inner">
                    <button class="at-btn is-green is-solid" @click="next" :disabled="is_submitting">Save & Continue</button>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {

        created() {
            this.$laravel_api.get('price_matrix/club-or-syndicate')
                .then(({data}) => {
                    this.price_matrix = data.data.price_matrix;
                });
        },

        data() {
            return {
                is_submitting: false,
                has_additional_contact: false,
                price_matrix: null,
                copy_from_your_details: false,
                options: {
                    category: [
                        {
                            id: '1-20',
                            name: '1-20'
                        },
                        {
                            id: '1-50',
                            name: '1-50'
                        },
                        {
                            id: '51-200',
                            name: '51-200'
                        },
                        {
                            id: '201-500',
                            name: '201-500'
                        },
                        {
                            id: '501-1000',
                            name: '501-1000'
                        },
                        {
                            id: '1001-2000',
                            name: '1001-2000'
                        },
                        {
                            id: '2001-3000',
                            name: '2001-3000'
                        },
                        {
                            id: '3001-4000',
                            name: '3001-4000'
                        },
                        {
                            id: '4001-5000',
                            name: '4001-5000'
                        },
                        {
                            id: '5001+',
                            name: '5001+'
                        },
                    ]
                },
                fields: {
                    fl_member: {
                        value: null, error: null
                    },
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
                    meta: {
                        club_name: {
                            value: null, error: null
                        },
                        primary_contact: {
                            title: {
                                value: null, error: null
                            },
                            first_name: {
                                value: null, error: null
                            },
                            last_name: {
                                value: null, error: null
                            },
                            email: {
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
                            }
                        },
                        additional_contact: {
                            title: {
                                value: null, error: null
                            },
                            first_name: {
                                value: null, error: null
                            },
                            last_name: {
                                value: null, error: null
                            },
                            email: {
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
                            }
                        }
                    }
                }
            }
        },

        computed: {
            fl_cost() {
                let category = this.fields.category.value;

                if (!category in this.price_matrix.at_only) {
                    return null;
                }

                if (!category in this.price_matrix.at_and_fl) {
                    return null;
                }

                let price = this.price_matrix.at_and_fl[category] - this.price_matrix.at_only[category];

                return price / 100;
            }
        },

        watch: {
            copy_from_your_details(value) {
                if (value === true) {
                    this.fields.meta.primary_contact.title.value = this.fields.title.value;
                    this.fields.meta.primary_contact.first_name.value = this.fields.first_name.value;
                    this.fields.meta.primary_contact.last_name.value = this.fields.last_name.value;
                    this.fields.meta.primary_contact.email.value = this.$root.member.user.email;
                    this.fields.meta.primary_contact.home_telephone.value = this.fields.home_telephone.value;
                    this.fields.meta.primary_contact.mobile_telephone.value = this.fields.mobile_telephone.value;
                    this.fields.meta.primary_contact.address_line_1.value = this.fields.address_line_1.value;
                    this.fields.meta.primary_contact.address_line_2.value = this.fields.address_line_2.value;
                    this.fields.meta.primary_contact.address_town.value = this.fields.address_town.value;
                    this.fields.meta.primary_contact.address_county.value = this.fields.address_county.value;
                    this.fields.meta.primary_contact.address_postcode.value = this.fields.address_postcode.value;
                } else {
                    this.fields.meta.primary_contact.title.value = null;
                    this.fields.meta.primary_contact.first_name.value = null;
                    this.fields.meta.primary_contact.last_name.value = null;
                    this.fields.meta.primary_contact.email.value = null;
                    this.fields.meta.primary_contact.home_telephone.value = null;
                    this.fields.meta.primary_contact.mobile_telephone.value = null;
                    this.fields.meta.primary_contact.address_line_1.value = null;
                    this.fields.meta.primary_contact.address_line_2.value = null;
                    this.fields.meta.primary_contact.address_town.value = null;
                    this.fields.meta.primary_contact.address_county.value = null;
                    this.fields.meta.primary_contact.address_postcode.value = null;
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

            set_primary_contact_address(address) {
                this.fields.meta.primary_contact.address_line_1.value = address.line_1;
                this.fields.meta.primary_contact.address_line_2.value = address.line_2;
                this.fields.meta.primary_contact.address_town.value = address.town;
                this.fields.meta.primary_contact.address_county.value = address.county;
                this.fields.meta.primary_contact.address_postcode.value = address.postcode;
            },

            set_additional_contact_address(address) {
                this.fields.meta.additional_contact.address_line_1.value = address.line_1;
                this.fields.meta.additional_contact.address_line_2.value = address.line_2;
                this.fields.meta.additional_contact.address_town.value = address.town;
                this.fields.meta.additional_contact.address_county.value = address.county;
                this.fields.meta.additional_contact.address_postcode.value = address.postcode;
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
