<template>
    <div class="b-renew">
        <div class="at-section">
            <div class="container">
                <div class="section__inner">
                    <div class="payment-choices-wrapper" v-if="payment_method === null">
                        <h3 class="at-heading is-grass">Payment Method</h3>
                        <p>Please select one of the following payment methods:</p>
                        <div class="payment-choices">
                            <div class="payment-choice" @click="payment_choice = 'credit-card'" :class="{ 'is-active': payment_choice === 'credit-card' }">
                                <!-- image? -->
                                <div class="payment-choice__content">
                                    <h4>Credit/Debit Card</h4>
                                    <p>Select this option if you would like to pay by Credit or Debit card. Once you confirm your payment method you will be directed to the payment screen to complete your membership</p>
                                </div>
                            </div>
                            <div class="payment-choice" @click="payment_choice = 'direct-debit'" :class="{ 'is-active': payment_choice === 'direct-debit' }">
                                <!-- image? -->
                                <div class="payment-choice__content">
                                    <h4>Direct Debit</h4>
                                    <p>Select this option if you would like to pay by Direct Debit. Once you confirm your payment method you will be directed to the payment screen to complete your membership</p>
                                    <b-info-modal>
                                        <h5>The Direct Debit Guarantee</h5>
                                        <p>This Guarantee is offered by all banks and building societies that accept instructions to pay Direct Debits</p>
                                        <p>If there are any changes to the amount, date or frequency of your Direct Debit Angling Trust will notify you 10 working days in advance of your account being debited or as otherwise agreed. If you request Angling Trust to collect a payment, confirmation of the amount and date will be given to you at the time of the request.</p>
                                        <p>If an error is made in the payment of your Direct Debit, by Angling Trust or your bank or building society, you are entitled to a full and immediate refund of the amount paid from your bank or building society</p>
                                        <p>If you receive a refund you are not entitled to, you must pay it back when Angling Trust asks you to</p>
                                        <p>You can cancel a Direct Debit at any time by simply contacting your bank or building society. Written confirmation may be required. Please also notify us.</p>

                                        <h5>The Angling Trust and your DD payment</h5>
                                        <p>Under our current arrangements with Smart Debit we collect DDs on either the 1st or 15th of the month, depending on the next available date from when the DD is established. The collection date is set during the Smart Debit set-up.</p>
                                    </b-info-modal>
                                </div>
                            </div>
                        </div>

                        <div v-if="recurring_optional()" class="at-section">
                            <input id="payment_is_recurring" type="checkbox" :disabled="payment_choice==='direct-debit'" v-model="payment_is_recurring"/>
                            <label for="payment_is_recurring">Automatically Renew Membership</label>
                            <p v-if="$root.member.category.price_recurring !== $root.member.category.price">By paying for membership with recurring card or direct debit payment, members are charged a lower annual rate.</p>
                        </div>

                        <button class="at-btn is-blue is-solid" :disabled="is_submitting" @click="confirm_payment_method">Confirm Payment Method</button>
                    </div>
                    <div class="payment-method" v-if="payment_method === 'credit-card'">
                        <h3 class="at-heading is-grass">Payment Method</h3>
                        <p>Please enter your card details below:</p>
                        <b-stripe-membership-payment
                            :price="$root.member.payment_is_recurring ? $root.member.category.price_recurring : $root.member.category.price"
                            :recurring="$root.member.payment_is_recurring"
                            v-on:payment_complete="payment_complete"
                        />
                    </div>
                    <div class="payment-method" v-if="payment_method === 'direct-debit'">
                        <h3 class="at-heading is-grass">Payment Method</h3>
                        <b-smart-debit-membership-payment
                            :price="$root.member.payment_is_recurring ? $root.member.category.price_recurring : $root.member.category.price"
                            v-on:payment_complete="smart_debit_payment_complete"
                            :is_renewal="true"
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
                payment_is_recurring: this.initial_payment_is_recurring()
            }
        },

        watch: {
            payment_is_recurring(paymentIsRecurring) {
                this.update_payment_is_recurring(paymentIsRecurring)
            },

            payment_choice(paymentChoice) {
                if(paymentChoice === 'direct-debit') {
                    this.payment_is_recurring = true;
                    this.update_payment_is_recurring(true)
                }
            }
        },

        methods: {
            initial_payment_is_recurring() {
                if (this.recurring_optional()) {
                    return this.$root.member.payment_is_recurring ?? false
                }

                return true;
            },

            recurring_optional() {
                return !this.$root.is_life_member();
            },

            update_payment_is_recurring(paymentIsRecurring) {
                this.$root.member.payment_is_recurring = paymentIsRecurring;
            },
            confirm_payment_method() {
                this.is_submitting = true;

                this.$laravel_api.post('members/me/chosen-payment-method', {
                    'method': this.payment_choice
                }).then(({data}) => {
                    this.payment_method = this.payment_choice;
                    this.is_submitting = false;
                }).catch(() => {
                    this.$toast.error();
                    this.is_submitting = true;
                });
            },

            async payment_complete(payment_is_recurring) {
                this.is_submitting = true;

                try {
                    const {data} = await this.$laravel_api.post('members/me/renew', {
                        payment_is_recurring: payment_is_recurring
                    })

                    if (data.success) {
                        window.location.replace(window.location.origin + '/thank-you-for-renewing');
                        return;
                    }

                    // noinspection ExceptionCaughtLocallyJS
                    throw new Error();
                } catch (error) {
                    this.$toast.error();
                    this.is_submitting = false;
                }
            },

            smart_debit_payment_complete(args) {
                this.is_submitting = true;
                console.log('Renewing Member');
                console.log(args);

                this.$laravel_api.post('members/me/renew', {
                    payment_is_recurring: args.payment_is_recurring,
                    payment_date: args.payment_date
                }).then(({data}) => {
                    if (data.success) {
                        console.log('Member Renewed');
                        window.location.replace(window.location.origin + '/thank-you-for-renewing');
                    } else {
                        this.$toast.error();
                        this.is_submitting = false;
                    }
                }).catch(() => {
                    this.$toast.error();
                    this.is_submitting = false;
                });
            }
        }
    }
</script>
