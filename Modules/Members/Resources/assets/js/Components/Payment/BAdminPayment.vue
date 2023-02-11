<template>
    <div class="b-renew">
        <div class="at-section">
            <div class="container">
                <div class="section__inner">
                    <div class="payment-choices-wrapper" v-if="payment_method === null">
                        <h3 class="at-heading is-grass">Payment Method</h3>

                        <div class="form-row">
                            <b-text-input
                                name="purpose"
                                label="Purpose"
                                v-model="purpose"
                                required
                            />

                            <b-text-input
                                name="amount"
                                label="Amount"
                                v-model="amount"
                                required
                            />
                        </div>

                        <div class="payment-choices">
                            <div class="payment-choice" @click="payment_choice = 'credit-card'" :class="{ 'is-active': payment_choice === 'credit-card' }">
                                <div class="payment-choice__content">
                                    <h4>Credit/Debit Card</h4>
                                    <p>Stripe</p>
                                </div>
                            </div>
                            <div class="payment-choice" @click="payment_choice = 'other'" :class="{ 'is-active': payment_choice === 'other' }">
                                <div class="payment-choice__content">
                                    <h4>Other</h4>
                                    <p>Cheque, Bank Transfer etc.</p>
                                </div>
                            </div>
                        </div>
                        <button class="at-btn is-blue is-solid" :disabled="is_submitting" @click="confirm_payment_method">Take Payment</button>
                    </div>
                    <div class="payment-method" v-if="payment_method === 'credit-card'">
                        <h3 class="at-heading is-grass">Payment Method</h3>
                        <p>Please enter your card details below:</p>
                        <b-stripe-admin-payment
                            :price="price"
                            :purpose="purpose.value"
                            v-on:payment_complete="payment_complete"
                        />
                    </div>
                    <div class="payment-method" v-if="payment_method === 'other'">
                        <h3 class="at-heading is-grass">Payment Method</h3>
                        <b-other-membership-payment
                            :price="price"
                            :purpose="purpose.value"
                            v-on:payment_complete="payment_complete"
                        />
                    </div>
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
                payment_choice: 'credit-card',
                payment_method: null,
                purpose: {
                    value: null, error: null
                },
                amount: {
                    value: null, error: null
                }
            }
        },

        computed: {
          price() {
              if (!this.amount.value) {
                  return null;
              }

              return (this.amount.value * 100).toFixed(0);
          }
        },

        methods: {
            confirm_payment_method() {
                let valid = true;

                if (!this.purpose.value) {
                    this.purpose.error = 'The purpose field is required.';
                    valid = false;
                }

                let regex = /^\d+(?:\.\d{0,2})$/;

                if (!regex.test(this.amount.value)) {
                    this.amount.error = 'The amount is invalid, please enter with 2 decimal places.';
                    valid = false;
                }

                if (!this.amount.value) {
                    this.amount.error = 'The amount field is required.';
                    valid = false;
                }

                if (!valid) {
                    this.$toast.error();
                    return;
                }

                this.$root.price = this.amount.value;

                this.is_submitting = true;

                this.$laravel_api.post('members/me/chosen-payment-method', {
                    'method': this.payment_choice
                }).then(({data}) => {
                    this.payment_method = this.payment_choice;
                    this.is_submitting = false;
                }).catch(() => {
                    this.$toast.error();
                    this.is_submitting = false;
                });
            },

            payment_complete() {
                this.return_to_admin_area();
            },

            return_to_admin_area() {
                window.location.replace(window.location.origin + '/wp/wp-admin/admin.php?page=at-members&id=' + this.$root.member.id);
            }
        }
    }
</script>
