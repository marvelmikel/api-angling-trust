<template>
    <div>
        <div class="b-stripe-card">
            <template v-if="!loading">
                <b-text-input
                    name="card_holder_name"
                    label="Card Holder Name"
                    v-model="card_holder_name"
                    :disabled="loading"
                />
                <div id="card-element" class="stripe-input" :class="{ 'is-disabled': loading }"></div>
            </template>
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

        <button class="at-btn is-green is-solid" :disabled="(is_submitting || loading) || !is_verified" @click="take_payment">Pay Now</button>

    </div>
</template>

<script>
    export default {
        props: ['amount', 'description', 'recurring'],

        data() {
            return {
                loading: true,
                is_submitting: false,
                is_verified: false,
                card_holder_name: {
                    value: null, error: null
                }
            }
        },

        mounted() {
            let amount = parseFloat(this.amount) * 100;

            let endpoint = `any/payment/intent?amount=${amount}`;

            if (this.description) {
                endpoint += `&description=${this.description}`;
            }

            this.$laravel_api.get(endpoint)
                .then(({data}) => {
                    this.secret = data.data.client_secret;
                    this.loading = false;

                    this.$nextTick(() => {
                        this.mount_stripe();
                    });
                })
                .catch((response) => {
                    console.log(response);
                });
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
            },

            async take_payment() {
                this.is_submitting = true;

                // this.$refs.recaptcha.execute()

                this.stripe.confirmCardPayment(this.secret, {
                    payment_method: {
                        card: this.cardElement,
                        billing_details: {
                            name: this.card_holder_name.value,
                        }
                    }
                })
                .then(({paymentIntent}) => {
                    if (paymentIntent.status === 'succeeded') {
                        this.$emit('payment_taken');
                    } else {
                        this.$toast.error();
                    }

                    this.is_submitting = false;
                })
                .catch((response) => {
                    this.is_submitting = false;
                    this.$toast.error();
                });
            },

            async checkRecaptcha() {
                console.log(token);
                const token = await this.$refs.recaptcha.execute()

            }
        }
    }
</script>
