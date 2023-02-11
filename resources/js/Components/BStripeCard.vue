<template>
    <div>
        <div v-if="!loading" class="b-stripe-card">
            <b-text-input
                name="card_holder_name"
                label="Card Holder Name"
                v-model="card_holder_name"
                :disabled="loading"
            />
            <div id="card-element" class="stripe-input" :class="{ 'is-disabled': loading }"></div>
        </div>

        <div v-if="!$root.is_life_member()" class="at-section">
            <input id="payment_is_recurring" type="checkbox" v-model="fields.payment_is_recurring.value"/> <label for="payment_is_recurring">Automatically Renew My Membership</label>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
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

        <button class="at-btn is-green is-solid" :disabled="!is_verified || (disabled || is_submitting)" @click="save_card">Save & Continue</button>
    </div>
</template>

<script>
    export default {
        props: ['disabled'],

        data() {
            return {
                loading: true,
                is_submitting: false,
                is_verified: false,
                card_holder_name: {
                    value: null, error: null
                },
                fields: {
                    payment_is_recurring: {
                        value: true, error: null
                    }
                }
            }
        },

        mounted() {
            this.$emit('mounting');

            this.$root.get_payment_intent_secret()
                .then((secret) => {
                    this.secret = secret;
                    this.loading = false;

                    this.$nextTick(() => {
                        this.mount_stripe();
                    });
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
                this.$emit('mounted');
            },

            save_card() {
                this.is_submitting = true;

                this.stripe.confirmCardSetup(this.secret, {
                    payment_method: {
                        card: this.cardElement,
                        billing_details: {
                            name: this.card_holder_name.value,
                        },
                    },
                })
                .then(({setupIntent}) => {
                    this.$laravel_api.post('payment_methods/stripe', {
                        'payment_method': setupIntent.payment_method,
                        'payment_is_recurring': this.fields.payment_is_recurring.value
                    }).then(({data}) => {
                        this.$root.member = data.data.member;
                        this.$emit('saved');
                        this.is_submitting = false;
                    }).catch(() => {
                        this.is_submitting = false;
                    });
                })
                .catch(() => {
                    this.is_submitting = false;
                });
            },

            take_payment() {
                return new Promise((resolve, reject) => {
                    this.stripe.confirmCardPayment(this.secret, {
                        payment_method: {
                            card: this.cardElement,
                            billing_details: {
                                name: this.card_holder_name.value,
                            }
                        }
                    })
                    .then((result) => {
                        if (result.error) {
                            reject(result.error.message);
                        } else {
                            resolve(result);
                        }
                    });
                });
            }
        }
    }
</script>
