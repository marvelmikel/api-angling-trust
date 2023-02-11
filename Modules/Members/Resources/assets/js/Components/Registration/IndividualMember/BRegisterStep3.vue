<template>
    <div class="b-register-step-3">
        <div class="at-section">
            <div class="container">
                <div class="section__inner">
                    <h3 class="at-heading is-grass">What type of angling do you take part in – tick all that apply <span class="required">*</span></h3>
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
                    <div class="form-row">
                        <b-select
                            name="meta_registration_source"
                            label="How did you hear about us?"
                            v-model="fields.meta.registration_source"
                            :options="$root.select_options.registration_source"
                            required
                        />

                        <b-text-input
                            v-if="fields.meta.registration_source.value === 'other'"
                            name="meta_registration_source_other"
                            label="Please Specify"
                            v-model="fields.meta.registration_source_other"
                            required
                        />
                    </div>

                    <div class="form-row">
                        <b-select
                            name="meta_reason_for_joining"
                            label="Why would you like to join?"
                            v-model="fields.meta.reason_for_joining"
                            :options="$root.select_options.reason_for_joining"
                            required
                        />

                        <b-text-input
                            v-if="fields.meta.reason_for_joining.value === 'other'"
                            name="meta_reason_for_joining_other"
                            label="Please Specify"
                            v-model="fields.meta.reason_for_joining_other"
                            required
                        />
                    </div>

                    <div class="form-row">
                        <b-text-input
                            name="meta_promo_code"
                            label="Promo Code (if applicable)"
                            v-model="fields.meta.promo_code"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="at-section" v-if="!$root.is_junior_member()">
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
                <div class="section__inner">
                    <button class="at-btn is-green is-solid" @click="next" :disabled="is_submitting">Save & Continue</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
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
                        division_id: {
                            value: null, error: null
                        },
                        regions: {
                            value: [], error: null
                        },
                        registration_source: {
                            value: null, error: null
                        },
                        registration_source_other: {
                            value: null, error: null
                        },
                        reason_for_joining: {
                            value: null, error: null
                        },
                        reason_for_joining_other: {
                            value: null, error: null
                        },
                        promo_code: {
                            value: null, error: null
                        },
                        raffle_opt_out: {
                            value: true, error: null
                        },
                    }
                },
            }
        },

        methods: {
            next() {
                this.is_submitting = true;

                this.$laravel_api.post('auth/register/step-3', this.$fields.serialise_fields(this.fields))
                .then(({data}) => {
                    if (data.success) {
                        this.$root.member = data.data.member;

                        if (data.data.skip_payment) {
                            window.delete_cookie('MEMBER_REGISTRATION_STEP');
                            window.delete_cookie('MEMBER_REGISTRATION_TYPE');
                            location.replace(location.origin + '/thank-you-for-joining');
                        } else {
                            this.$root.to_step(4);
                        }
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
