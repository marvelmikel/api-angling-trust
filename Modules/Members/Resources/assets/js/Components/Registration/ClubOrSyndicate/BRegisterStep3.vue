<template>
    <div class="b-register-step-3">

        <div class="at-section">
            <div class="container">
                <div class="section__inner">
                    <h3 class="at-heading is-grass">Types of Angling <span class="required">*</span></h3>
                    <div class="form-row">
                        <b-multi-select
                            name="meta_disciplines"
                            v-model="fields.meta.disciplines"
                            :options="$root.select_options.disciplines"
                        ></b-multi-select>
                    </div>
                </div>
            </div>
        </div>

        <div class="at-section">
            <div class="container">
                <div class="section__inner">
                    <h3 class="at-heading is-grass">Employers Reference Number (ERN) <span class="required">*</span>
                    </h3>
                    <div class="form-row">
                        <ErnInput
                            name="ern"
                            v-model="fields.meta.ern"
                            required
                        />
                    </div>

                </div>
            </div>
        </div>

        <div class="at-section">
            <div class="container">
                <div class="section__inner">
                    <h3 class="at-heading is-grass">How Fishing Rights Held <span class="required">*</span></h3>
                    <div class="form-row">
                        <b-multi-select
                            name="meta_fishing_rights"
                            v-model="fields.meta.fishing_rights"
                            :options="$root.select_options.fishing_rights"
                        ></b-multi-select>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="$root.member && $root.member.fl_member" class="at-section">
            <div class="container">
                <div class="section__inner">
                    <h3 class="at-heading is-grass">Catchments Relevant to Club's Fisheries</h3>
                    <div class="form-row">
                        <b-multi-select
                            name="meta_relevant_catchments"
                            v-model="fields.meta.relevant_catchments"
                            :options="$root.select_options.relevant_catchments"
                        ></b-multi-select>
                    </div>
                </div>
            </div>
        </div>

        <div class="at-section">
            <div class="container">
                <div class="section__inner">
                    <h3 class="at-heading is-grass">Member Numbers <span class="required">*</span></h3>
                    <div class="form-row">
                        <b-member-numbers-input
                            v-model="fields.meta.member_numbers"
                        ></b-member-numbers-input>
                    </div>
                </div>
            </div>
        </div>

        <div class="at-section is-grass">
            <b-wave fill="grass"></b-wave>
            <div class="container">
                <div class="section__inner">
                    <h3 class="at-heading is-white">Would you like to add a donation?</h3>
                    <div class="form-row">
                        <b-donation-input v-model="fields.donation.value"></b-donation-input>
                    </div>
                </div>
            </div>
        </div>

        <div class="at-section">
            <b-wave fill="white"></b-wave>
            <div class="container">
                <div class="section__inner">
                    <div class="form-row opt-ins">
                        <b-checkbox-input
                            name="meta_raffle_opt_out"
                            label="Please untick the box if you would prefer to opt out of receiving marketing material relating to our prize draws and raffles, including receiving raffle tickets."
                            v-model="fields.meta.raffle_opt_out"
                        ></b-checkbox-input>
                    </div>
                </div>
            </div>
        </div>

        <div class="at-section">
            <b-wave fill="white"></b-wave>
            <div class="container">
                <div class="at-section__inner">
                    <button class="at-btn is-green is-solid" @click="next" :disabled="is_submitting">Save & Continue</button>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import ErnInput from "../../ErnInput";
    export default {
        components: {ErnInput},
        data() {
            return {
                is_submitting: false,
                fields: {
                    donation: {
                        value: {}, error: null
                    },
                    meta: {
                        disciplines: {
                            value: [], error: null
                        },
                        ern: {
                            value: null, error: null
                        },
                        fishing_rights: {
                            value: [], error: null
                        },
                        relevant_catchments: {
                            value: [], error: null
                        },
                        member_numbers: {
                            junior: {
                                male: {
                                    value: 0, error: null
                                },
                                female: {
                                    value: 0, error: null
                                }
                            },
                            senior: {
                                male: {
                                    value: 0, error: null
                                },
                                female: {
                                    value: 0, error: null
                                }
                            },
                            veteran: {
                                male: {
                                    value: 0, error: null
                                },
                                female: {
                                    value: 0, error: null
                                }
                            },
                            disabled: {
                                male: {
                                    value: 0, error: null
                                },
                                female: {
                                    value: 0, error: null
                                }
                            }
                        },
                        raffle_opt_out: {
                            value: true, error: null
                        }
                    }
                }
            }
        },

        computed: {

        },

        methods: {
            next() {
                this.is_submitting = true;

                this.$laravel_api.post('auth/register/step-3', this.$fields.serialise_fields(this.fields))
                    .then(({data}) => {
                        if (data.success) {
                            this.$root.member = data.data.member;
                            this.$root.to_step(4);
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
