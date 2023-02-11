<template>
    <div class="b-my-contact-details">
        <template v-if="!edit_mode">
            <div class="at-section">
                <b-wave fill="white"></b-wave>
                <div class="container">
                    <div class="at-section__inner">
                        <h3 class="at-heading is-grass">CAAG Details</h3>

                        <div class="info-items">
                            <b-info-item label="Membership Number" :value="$root.member.reference" />
                            <b-info-item label="Joined" v-if="$root.member.registered_at" :value="$date_format($root.member.registered_at, 'DD/MM/YYYY')" />
                            <b-info-item label="Renewed" v-if="$root.member.renewed_at" :value="$date_format($root.member.renewed_at, 'DD/MM/YYYY')" />
                            <b-info-item label="Expires" v-if="$root.member.expires_at" :value="$date_format($root.member.expires_at, 'DD/MM/YYYY')" />
                            <br>
                            <b-info-item label="Name" :value="$root.member.meta.club_name" />
                        </div>

                        <div class="info-items">
                            <b-info-item label="Facebook URL" :value="$root.member.meta.facebook_url" />
                            <b-info-item label="Instagram URL" :value="$root.member.meta.instagram_url" />
                            <b-info-item label="Twitter URL" :value="$root.member.meta.twitter_url" />
                            <b-info-item label="Website" :value="$root.member.meta.website" />
                        </div>

                        <hr>

                        <h3 class="at-heading is-grass">Your Details</h3>

                        <div class="info-items">
                            <b-info-item label="Title" :value="$root.member.title" :options="$root.select_options.title" />
                            <b-info-item label="First Name" :value="$root.member.first_name" />
                            <b-info-item label="Last Name" :value="$root.member.last_name" />
                            <b-info-item label="Email" :value="$root.member.user.email" />
                            <b-info-item label="Home Telephone" :value="$root.member.home_telephone" />
                            <b-info-item label="Mobile Telephone" :value="$root.member.mobile_telephone" />
                        </div>

                        <h4 class="at-heading is-grass">Address</h4>

                        <div class="info-items">
                            <b-info-item label="Line 1" :value="$root.member.address_line_1" />
                            <b-info-item label="Line 2" :value="$root.member.address_line_2" />
                            <b-info-item label="Town" :value="$root.member.address_town" />
                            <b-info-item label="County" :value="$root.member.address_county" />
                            <b-info-item label="Postcode" :value="$root.member.address_postcode" />
                        </div>

                        <hr>

                        <h3 class="at-heading is-grass">Primary Contact</h3>

                        <div class="info-items">
                            <b-info-item label="Title" :value="$root.member.meta.primary_contact.title" :options="$root.select_options.title" />
                            <b-info-item label="First Name" :value="$root.member.meta.primary_contact.first_name" />
                            <b-info-item label="Last Name" :value="$root.member.meta.primary_contact.last_name" />
                            <b-info-item label="Email" :value="$root.member.meta.primary_contact.email" />
                            <b-info-item label="Home Telephone" :value="$root.member.meta.primary_contact.home_telephone" />
                            <b-info-item label="Mobile Telephone" :value="$root.member.meta.primary_contact.mobile_telephone" />
                        </div>

                        <h4 class="at-heading is-grass">Address</h4>

                        <div class="info-items">
                            <b-info-item label="Line 1" :value="$root.member.meta.primary_contact.address_line_1" />
                            <b-info-item label="Line 2" :value="$root.member.meta.primary_contact.address_line_2" />
                            <b-info-item label="Town" :value="$root.member.meta.primary_contact.address_town" />
                            <b-info-item label="County" :value="$root.member.meta.primary_contact.address_county" />
                            <b-info-item label="Postcode" :value="$root.member.meta.primary_contact.address_postcode" />
                        </div>

                        <hr>

                        <h3 class="at-heading is-grass">Additional Contact</h3>

                        <div class="info-items">
                            <b-info-item label="Title" :value="$root.member.meta.additional_contact.title" :options="$root.select_options.title" />
                            <b-info-item label="First Name" :value="$root.member.meta.additional_contact.first_name" />
                            <b-info-item label="Last Name" :value="$root.member.meta.additional_contact.last_name" />
                            <b-info-item label="Email" :value="$root.member.meta.additional_contact.email" />
                            <b-info-item label="Home Telephone" :value="$root.member.meta.additional_contact.home_telephone" />
                            <b-info-item label="Mobile Telephone" :value="$root.member.meta.additional_contact.mobile_telephone" />
                        </div>

                        <h4 class="at-heading is-grass">Address</h4>

                        <div class="info-items">
                            <b-info-item label="Line 1" :value="$root.member.meta.additional_contact.address_line_1" />
                            <b-info-item label="Line 2" :value="$root.member.meta.additional_contact.address_line_2" />
                            <b-info-item label="Town" :value="$root.member.meta.additional_contact.address_town" />
                            <b-info-item label="County" :value="$root.member.meta.additional_contact.address_county" />
                            <b-info-item label="Postcode" :value="$root.member.meta.additional_contact.address_postcode" />
                        </div>

                        <hr>

                        <b-info-item
                            v-if="$root.member.meta.raffle_opt_out"
                            label="I would like to receive marketing material relating to prize draws and raffles, including receiving raffle tickets"
                            value="Yes"
                        />

                        <b-info-item
                            v-if="!$root.member.meta.raffle_opt_out"
                            label="I would like to receive marketing material relating to prize draws and raffles, including receiving raffle tickets"
                            value="No"
                        />

                        <h4 class="at-heading is-grass">Get the latest news via email from:</h4>

                        <b-info-item label="Angling Trust" value="Yes" v-if="$root.member.opt_in_1"/>
                        <b-info-item label="Angling Trust" value="No" v-if="!$root.member.opt_in_1"/>
                        <b-info-item label="Our partners and sponsors" value="Yes" v-if="$root.member.opt_in_2"/>
                        <b-info-item label="Our partners and sponsors" value="No" v-if="!$root.member.opt_in_2"/>

                        <div class="actions">
                            <button class="at-btn is-blue is-frame" @click="edit">
                                <i class="fal fa-edit"></i> Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-if="edit_mode">
            <div class="at-section">
                <b-wave fill="white"></b-wave>
                <div class="container">
                    <div class="at-section__inner">
                        <h3 class="at-heading is-grass">Club Details</h3>

                        <div class="form-row">
                            <b-text-input
                                name="meta_facebook_url"
                                label="Facebook URL"
                                v-model="fields.meta.facebook_url"
                            />

                            <b-text-input
                                name="meta_instagram_url"
                                label="Instagram URL"
                                v-model="fields.meta.instagram_url"
                            />
                        </div>

                        <div class="form-row">
                            <b-text-input
                                name="meta_twitter_url"
                                label="Twitter URL"
                                v-model="fields.meta.twitter_url"
                            />

                            <b-text-input
                                name="meta_website"
                                label="Website"
                                v-model="fields.meta.website"
                            />
                        </div>

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
                                name="user[email]"
                                label="Email"
                                v-model="fields.user.email"
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

                        <div class="form-row">
                            <b-text-input
                                name="address_line_1"
                                label="Line 1"
                                v-model="fields.address_line_1"
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

                        <h3 class="at-heading is-grass">Primary Contact</h3>

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

                        <div class="form-row">
                            <b-text-input
                                name="meta_primary_contact_address_line_1"
                                label="Line 1"
                                v-model="fields.meta.primary_contact.address_line_1"
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
                            />
                        </div>
                        <div class="form-row">
                            <b-text-input
                                name="meta_primary_contact_address_county"
                                label="County"
                                v-model="fields.meta.primary_contact.address_county"
                            />
                            <b-text-input
                                name="meta_primary_contact_address_postcode"
                                label="Postcode"
                                v-model="fields.meta.primary_contact.address_postcode"
                                required
                            />
                        </div>

                        <h3 class="at-heading is-grass">Additional Contact</h3>

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

                        <div class="form-row">
                            <b-text-input
                                name="meta_additional_contact_address_line_1"
                                label="Line 1"
                                v-model="fields.meta.additional_contact.address_line_1"
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
                            />
                        </div>
                        <div class="form-row">
                            <b-text-input
                                name="meta_additional_contact_address_county"
                                label="County"
                                v-model="fields.meta.additional_contact.address_county"
                            />
                            <b-text-input
                                name="meta_additional_contact_address_postcode"
                                label="Postcode"
                                v-model="fields.meta.additional_contact.address_postcode"
                                required
                            />
                        </div>

                        <div class="form-row opt-ins">
                            <b-checkbox-input
                                name="meta_raffle_opt_out"
                                label="Please untick the box if you would prefer to opt out of receiving marketing material relating to our prize draws and raffles, including receiving raffle tickets."
                                v-model="fields.meta.raffle_opt_out"
                            />
                        </div>

                        <p style="margin-bottom: 0.5rem">Get the latest news via email from:</p>

                        <div class="opt-ins" style="margin-bottom: 2rem">
                            <b-checkbox-input
                                name="opt_in_1"
                                label="Angling Trust"
                                v-model="fields.opt_in_1"
                            ></b-checkbox-input>

                            <b-checkbox-input
                                name="opt_in_2"
                                label="Our partners and sponsors"
                                v-model="fields.opt_in_2"
                            ></b-checkbox-input>
                        </div>

                        <button class="at-btn is-red is-solid" @click="edit_mode = false" :disabled="is_submitting">Cancel</button>
                        <button class="at-btn is-green is-solid" @click="save" :disabled="is_submitting">Save</button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                is_submitting: false,
                edit_mode: false,
                fields: {
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
                    opt_in_1: {
                        value: null, error: null
                    },
                    opt_in_2: {
                        value: null, error: null
                    },
                    user: {
                        email: {
                            value: null, error: null
                        }
                    },
                    meta : {
                        club_name: {
                            value: null, error: null
                        },
                        facebook_url: {
                            value: null, error: null
                        },
                        instagram_url: {
                            value: null, error: null
                        },
                        twitter_url: {
                            value: null, error: null
                        },
                        website: {
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
                            },
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
                            },
                        },
                        raffle_opt_out: {
                            value: null, error: null
                        }
                    },
                }
            }
        },

        methods: {
            edit() {
                this.$fields.fill_fields(this.fields, this.$root.member);
                this.edit_mode = true;
            },

            save() {
                this.is_submitting = true;

                this.$laravel_api.post('members/me', this.$fields.serialise_fields(this.fields))
                    .then(({data}) => {
                        if (data.success) {
                            this.$root.member = data.data.member;
                            this.edit_mode = false;
                            this.$toast.success('Your contact details have been updated.');
                            this.is_submitting = false;
                        } else {
                            if (data.error.code === 422) {
                                this.$fields.fill_errors(this.fields, data.data.errors);
                                this.$toast.error("There are errors in your submission.");
                            }

                            this.is_submitting = false;
                        }
                    })
                    .catch(() => {
                        this.is_submitting = false;
                        this.$toast.error();
                    });
            }
        }
    }
</script>
