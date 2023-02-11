<template>
   <div class="b-draw-prize-purchase">
       <div class="form-row">
           <b-text-input
               name="card_holder_name"
               label="Card Holder Name"
               v-model="fields.card_holder_name"
               required
           />
       </div>
       <div class="form-row">
           <div class="form-input">
               <div class="b-stripe-card" style="margin: 0;">
                   <div id="card-element" class="stripe-input" style="margin: 0"></div>
               </div>
           </div>
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

       <button class="at-btn is-red is-solid" @click="back" :disabled="is_submitting">Back</button>
       <button class="at-btn is-green is-solid" :disabled="!is_verified || is_submitting" @click="pay_now">Pay Now</button>
    </div>
</template>

<script>
    export default {
        props: ['payment_amount'],

        mounted() {
            this.mount_stripe();
        },

        data() {
            return {
                is_loading: true,
                is_submitting: false,
                is_verified: false,
                secret: null,
                fields: {
                    card_holder_name: {
                        value: null, error: null
                    }
                }
            }
        },

        methods: {
            back() {
                this.$emit('back');
            },

            mount_stripe() {
                const styles = {
                    base: {
                        color: '#04385c',
                        fontSize: '16px',
                        fontFamily: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',
                        fontSmoothing: 'antialiased',
                        '::placeholder': {
                            color: '#57797b',
                            fontWeight: '600'
                        },
                    },
                    invalid: {
                        color: '#e5424d',
                        ':focus': {
                            color: '#303238',
                        },
                    },
                };

                this.stripe = Stripe(process.env.MIX_DRAW_STRIPE_KEY);
                this.elements = this.stripe.elements();
                this.cardElement = this.elements.create('card', {
                    style: styles,
                });

                this.cardElement.mount('#card-element');
                this.$emit('mounted');
            },

            validate() {
                let valid = true;

                if (this.$parent.fields.name.value === null) {
                    this.$parent.fields.name.error = 'This field is required';
                    valid = false;
                }

                if (this.$parent.fields.email.value === null) {
                    this.$parent.fields.email.error = 'This field is required';
                    valid = false;
                }

                if (this.fields.card_holder_name.value === null) {
                    this.fields.card_holder_name.error = 'This field is required';
                    valid = false;
                }

                return valid;
            },

            pay_now() {
                this.is_submitting = true;

                if (!this.validate()) {
                    this.is_submitting = false;
                    this.$toast.error();
                    return;
                }

                let amount = this.payment_amount;

                let description = `[Fishing Draw] NC: 4201 | Name: ${this.$parent.fields.name.value} | Email: ${this.$parent.fields.email.value}`;

                console.log(description);

                this.$laravel_api.get(`any/payment/intent?amount=${amount}&description=${description}&stripe=draw`)
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
                                    reject();
                                }
                            })
                            .catch((response) => {
                                this.$toast.error();
                                this.is_submitting = false;
                                reject();
                            });
                    })
                    .catch((response) => {
                        this.$toast.error();
                        this.is_submitting = false;
                        reject();
                    });
            }
        }
    }
</script>
