<template>
    <div>
        <div class="b-stripe-card">
            <b-text-input
                name="card_holder_name"
                label="Card Holder Name"
                v-model="fields.card_holder_name"
            />
            <div id="card-element" class="stripe-input"></div>
        </div>

        <div class="recaptcha__wrap">
            <vue-recaptcha
                :sitekey="this.$root.$recaptcha_key"
                @verify="is_verified = true"
                @error="is_verified = false"
                @expired="is_verified = false"
                :loadRecaptchaScript="true"
            />
        </div>

        <button class="at-btn is-green is-solid" :disabled="!is_verified || is_submitting" @click="pay_now">
            <span v-if="is_submitting"><i class="fa fa-spinner fa-spin mr-1"></i> Working...</span>
            <span v-else>Pay Now</span>
        </button>
    </div>
</template>

<script>
    export default {
        props: ['price', 'donation', 'recurring'],

        mounted() {
            this.mount_stripe();
        },

        data() {
            return {
                is_loading: true,
                is_submitting: false,
                secret: null,
                payment_method_id: null,
                is_verified: false,
                fields: {
                    card_holder_name: {
                        value: null, error: null
                    }
                }
            }
        },

        methods: {
            mount_stripe() {
                const styles = {
                    base: {
                        color: '#04385c',
                        fontSize: '16px',
                        fontFamily: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',
                        fontSmoothing: 'antialiased',
                        '::placeholder': {
                            color: '#CFD7DF',
                        },
                    },
                    invalid: {
                        color: '#e5424d',
                        ':focus': {
                            color: '#303238',
                        },
                    },
                };

                this.stripe = Stripe(process.env.MIX_STRIPE_KEY);
                this.elements = this.stripe.elements();
                this.cardElement = this.elements.create('card', {
                    style: styles,
                });

                this.cardElement.mount('#card-element');
                this.$emit('mounted');
            },

            async pay_now() {
                this.is_submitting = true;

                if (this.recurring) {
                    await this.pay_for_donation()

                    return this.create_subscription();
                }

                return this.pay_for_donation_and_membership();
            },

            async pay_for_donation() {
                if (!this.donation) {
                    console.log('Donation Skipped');
                    return;
                }

                const amount = this.donation.amount;
                const description = `Donation: destination: ${this.donation.destination}`;

                console.log(`Charging donation of £${amount}`);

                try {
                    const { data } = await this.$laravel_api.get(
                        `any/payment/intent?amount=${amount}&description=${description}`
                    );


                    this.secret = data.data.client_secret;

                    const { paymentIntent } = await this.stripe.confirmCardPayment(this.secret, {
                        payment_method: {
                            card: this.cardElement,
                            billing_details: {
                                name: this.fields.card_holder_name.value,
                            }
                        }
                    })

                    if (paymentIntent.status === 'succeeded') {
                        await this.$laravel_api.post('payment_methods/stripe/record', {
                            donation: this.donation
                        })
                        console.log('Payment Taken');

                        await this.record_donation();

                    } else {
                        // noinspection ExceptionCaughtLocallyJS
                        throw new Error();
                    }
                } catch(error) {
                    this.$toast.error();
                    this.is_submitting = false;
                }
            },

            async pay_for_donation_and_membership() {
                let amount = this.price;
                if (this.donation) {
                    amount += this.donation.amount || 0
                }

                if (!amount) {
                    this.$emit('payment_complete', true);
                }

                let description = `Membership: ${this.$root.member.membershipType.name} (${this.$root.member.category.name})`;

                if (this.donation) {
                    description = `${description} | £${this.donation.amount} Donation for: ${this.donation.destination}`
                }

                try {
                    const { data } = await this.$laravel_api.get(
                        `any/payment/intent?amount=${amount}&description=${description}`
                    )
                    this.secret = data.data.client_secret;

                    const { paymentIntent } = await this.stripe.confirmCardPayment(this.secret, {
                        payment_method: {
                            card: this.cardElement,
                            billing_details: {
                                name: this.fields.card_holder_name.value,
                            }
                        }
                    })

                    if (paymentIntent.status === 'succeeded') {
                        await this.$laravel_api.post('payment_methods/stripe/record', {
                            membership: this.price,
                            donation: this.donation
                        });

                        if (this.donation) {
                            await this.record_donation();
                        }
                        this.$emit('payment_complete', false);
                        return;

                    }

                    // noinspection ExceptionCaughtLocallyJS
                    throw new Error();
                } catch(error) {
                    console.error(error);

                    this.$toast.error();
                    this.is_submitting = false;
                }
            },

            async create_subscription() {
                if (!this.price) {
                    this.$emit('payment_complete', true);
                }

                const { error: createError, paymentMethod } = await this.stripe.createPaymentMethod({
                    type: 'card',
                    card: this.cardElement
                });

               if (createError) {
                   this.$toast.error();
                   this.is_submitting = false;

                   return;
               }

               this.payment_method_id = paymentMethod.id;

               const { data } = await this.$laravel_api.post('payment_methods/stripe/subscribe', {
                   payment_method_id: this.payment_method_id
               });

                if (!data.success) {
                   this.$toast.error();
                   this.is_submitting = false;

                   return;
               }

               const { complete, secret } = data.data;

               if (complete) {
                   this.$emit('payment_complete', true);
                   return;
               }

               try {
                   const { error, paymentIntent } = await this.stripe.confirmCardPayment(secret, {
                       payment_method: this.payment_method_id
                   })

                   if (error) {
                       this.$toast.error(error.message);
                       this.is_submitting = false;
                       return;
                   }

                   await this.$laravel_api.post('payment_methods/stripe/complete', {
                       reference: paymentIntent.id
                   });

                   this.$emit('payment_complete', true);
               } catch(error)  {
                   this.$toast.error();
                   this.is_submitting = false;
               }
            },

            async record_donation() {
                return this.$laravel_api.post('any/donation', {
                    amount: this.donation.amount,
                    destination: this.donation.destination,
                    note: this.donation.note,
                });
            },
        }
    }
</script>
