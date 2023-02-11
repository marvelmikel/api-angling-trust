<template>
    <div>
        <h3 class="at-heading is-grass">Payment</h3>
        <p class="checkout__subheading">Please add a card below which will be used to pay for your ticket.</p>

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

        <div class="checkout__button">
            <button class="at-btn is-green is-solid" :disabled="!is_verified || is_submitting" @click="pay_now">Pay Now</button>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.mount_stripe();
        },

        data() {
            return {
                is_loading: true,
                is_submitting: false,
                is_verified: false,
                secret: null,
                payment_method_id: null,
                fields: {
                    card_holder_name: {
                        value: null, error: null
                    }
                }
            }
        },

        computed: {
            price() {
                return this.$root.total;
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

            pay_now() {
                this.is_submitting = true;

                let amount = this.price;

                const ticketNames = this.$root.basket.tickets.map(({event: {name, department_code, nominal_code}}) => {
                    return `${name}|DC:${department_code}|NC:${nominal_code}`;
                }).join('; ');

                let description = `[Ticket Purchase] ${ticketNames} ${this.fields.card_holder_name.value}`;

                this.$laravel_api.get(`any/payment/intent?amount=${amount}&description=${description}`)
                    .then(({data}) => {
                        this.secret = data.data.client_secret;

                        this.stripe.confirmCardPayment(this.secret, {
                            payment_method: {
                                card: this.cardElement,
                                billing_details: {
                                    name: this.fields.card_holder_name.value,
                                }
                            }
                        })
                            .then(({paymentIntent}) => {
                                if (paymentIntent.status === 'succeeded') {
                                    this.$emit('complete', paymentIntent);
                                } else {
                                    this.$toast.error();
                                    this.is_submitting = false;

                                }
                            })
                            .catch((response) => {
                                this.$toast.error();
                                this.is_submitting = false;
                                });
                    })
                    .catch((response) => {
                        this.$toast.error();
                        this.is_submitting = false;
                    });
            }
        }
    }
</script>
