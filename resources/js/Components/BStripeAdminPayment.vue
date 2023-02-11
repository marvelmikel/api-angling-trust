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

        <button class="at-btn is-green is-solid" :disabled="is_submitting" @click="pay_now">Complete Payment</button>
    </div>
</template>

<script>
    export default {
        props: ['price', 'purpose'],

        created() {
            if (this.initial_payment_is_recurring) {
                this.payment_is_recurring = this.initial_payment_is_recurring;
            }

            if (this.$root.is_life_member()) {
                this.payment_is_recurring = false;
            }
        },

        watch: {
            payment_is_recurring(value) {
                this.$emit('payment_is_recurring', value);
            }
        },

        mounted() {
            this.mount_stripe();
        },

        data() {
            return {
                is_loading: true,
                is_submitting: false,
                secret: null,
                payment_method_id: null,
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

            pay_now() {
                this.is_submitting = true;

                let description = `[Admin Payment] First Name: ${this.$root.member.first_name} | Surname: ${this.$root.member.last_name} | Membership Number: ${this.$root.member.reference} | Postcode: ${this.$root.member.address_postcode} | Membership Type: ${this.$root.member.membershipType.name}`;

                this.$laravel_api.get(`any/payment/intent?amount=${this.price}&description=${description}`)
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

                                    this.$laravel_api.post('payment_methods/stripe/record-other', {
                                        price: this.price,
                                        purpose: this.purpose
                                    }).then(() => {
                                        console.log('Payment Taken');
                                        this.$emit('payment_complete');
                                    }).catch(() => {
                                        this.$toast.error();
                                        this.is_submitting = false;
                                    });

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
