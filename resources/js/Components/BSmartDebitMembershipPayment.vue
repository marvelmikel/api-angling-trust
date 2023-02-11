<template>
    <div class="b-smart-debit">
        <div class="form-row">
            <b-text-input
                name="first_name"
                label="First Name"
                v-model="fields.first_name"
            />

            <b-text-input
                name="last_name"
                label="Last Name"
                v-model="fields.last_name"
            />
        </div>

        <div class="form-row">
            <b-text-input
                name="account_name"
                label="Account Name"
                v-model="fields.account_name"
            />
        </div>

        <div class="form-row">
            <b-text-input
                name="account_number"
                label="Account Number"
                v-model="fields.account_number"
            />

            <b-text-input
                name="sort_code"
                label="Sort Code (xx-xx-xx)"
                v-model="fields.sort_code"
            />
        </div>

        <button class="at-btn is-green is-solid" :disabled="is_submitting" @click="validate">Save & Continue</button>
    </div>
</template>

<script>
    export default {
        props: ['price', 'donation', 'is_renewal'],

        data() {
            return {
                is_submitting: false,
                fields: {
                    first_name: {
                        value: null, error: null
                    },
                    last_name: {
                        value: null, error: null
                    },
                    account_name: {
                        value: null, error: null
                    },
                    account_number: {
                        value: null, error: null
                    },
                    sort_code: {
                        value: null, error: null
                    }
                }
            }
        },

        methods: {
            async validate() {
                this.is_submitting = true;

                try {
                    const { data } = await this.$laravel_api.post('payment_methods/smart_debit/validate', this.$fields.serialise_fields(this.fields))

                    if (data.success) {
                        console.log('Details Validated');

                        this.setup_donation_payment()
                            .then(() => {
                                this.setup_membership_payment();
                            });
                    } else {
                        if (data.error.code === 422) {
                            this.$toast.error('You have validation errors');
                            this.$fields.fill_errors(this.fields, data.data.errors);
                        } else if (data.error.code === 1) {
                            this.$toast.error('Direct debit request was denied, please check your details are correct');
                        } else {
                            this.$toast.error();
                        }

                        this.is_submitting = false;
                    }
                } catch(error) {
                    this.$toast.error();
                    this.is_submitting = false;
                }
            },

            async setup_donation_payment() {
                console.log('Setting up Donation');
                if (!this.donation) {
                    console.log('Skipping Donation');
                    return;
                }
                try {
                    const { data } = await this.$laravel_api.post('payment_methods/smart_debit/donation', {
                        amount: this.donation.amount,
                        destination: this.donation.destination,
                        note: this.donation.note,
                        fields: this.$fields.serialise_fields(this.fields)
                    });

                    if (data.success) {
                        console.log('Donation Set Up');
                        return
                    }

                    // noinspection ExceptionCaughtLocallyJS
                    throw new Error();
                } catch(error) {
                    this.$toast.error();
                    this.is_submitting = false;
                }
            },

            async setup_membership_payment() {
                console.log('Setting up Membership');
                const endpoint = `payment_methods/smart_debit/${this.is_renewal ? 'renewMembership' : 'membership'}`;

                try {
                    const { data } = await this.$laravel_api.post(endpoint, this.$fields.serialise_fields(this.fields))
                    if (data.success) {
                        console.log('Membership Set Up');

                        this.$emit('payment_complete', {
                            payment_is_recurring: true,
                            payment_date: data.data.payment_date
                        });

                        return;
                    }

                    // noinspection ExceptionCaughtLocallyJS
                    throw new Error();
                } catch(error) {
                    this.$toast.error();
                    this.is_submitting = false;
                }
            }
        }
    }
</script>
