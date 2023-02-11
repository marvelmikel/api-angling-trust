<template>
    <div v-if="$root.member" class="b-register-step-4">
        <div class="at-section">
            <div class="container">
                <div class="at-section__inner">
                    <h3 class="at-heading is-grass">You're almost done!</h3>
                    <p>Please check your membership type below, then when you're ready complete payment.</p>
                </div>
            </div>
        </div>
        <div class="at-section is-grass">
            <b-wave fill="grass"></b-wave>
            <div class="container">
                <div class="at-section__inner">
                    <h4 class="at-heading is-white">My Membership Type</h4>
                    <b-my-membership-type v-model="$root.member" :donation="donation" />
                </div>
            </div>
        </div>
        <div class="at-section">
            <b-wave fill="white"></b-wave>
            <div class="container">
                <div class="section__inner">
                    <div class="payment-choices-wrapper" v-if="payment_method === null">
                        <h3 class="at-heading is-grass">Payment</h3>
                        <p>Please select one of the following payment methods:</p>
                        <div class="payment-choices">
                            <div class="payment-choice" @click="payment_choice = 'credit-card'" :class="{ 'is-active': payment_choice === 'credit-card' }">
                                <!-- image? -->
                                <div class="payment-choice__content">
                                    <h4>Credit/Debit Card</h4>
                                    <p>Select this option if you would like to pay by Credit or Debit card. Once you confirm your payment method you will be directed to the payment screen to complete your membership</p>
                                </div>
                            </div>
                            <div v-if="!$root.is_life_member() && !$root.is_junior_member()" class="payment-choice" @click="payment_choice = 'direct-debit'" :class="{ 'is-active': payment_choice === 'direct-debit' }">
                                <!-- image? -->
                                <div class="payment-choice__content">
                                    <h4>Direct Debit</h4>
                                    <p>Select this option if you would like to pay by Direct Debit. Once you confirm your payment method you will be directed to the payment screen to complete your membership</p>
                                    <span class="b-info-modal is-sm-only">
                                        <div class="icon-wrapper">
                                            <a href="/direct-debit-terms" target="_blank">
                                                <i class="fa fa-info"></i>
                                            </a>
                                        </div>
                                    </span>
                                    <b-info-modal class="is-md-and-up">
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
                        <div v-if="!$root.is_life_member()" class="at-section">
                            <input id="payment_is_recurring" type="checkbox" :disabled="payment_choice==='direct-debit'" v-model="payment_is_recurring"/>
                            <label for="payment_is_recurring">Automatically Renew My Membership</label>
                            <p v-if="hasRecurringPrice()">By paying for your membership with recurring card or direct debit payment, you are charged a lower annual rate. If you untick this box and choose to pay by single card payment you will be charged a higher annual rate - £33.00 for adult membership and £28.00 for senior and young adult membership</p>
                        </div>
                        <button class="at-btn is-blue is-solid" :disabled="is_submitting" @click="confirm_payment_method">Confirm Payment Method</button>
                    </div>
                    <div class="payment-method" v-if="payment_method === 'credit-card'">
                        <h3 class="at-heading is-grass">Credit Card Payment</h3>
                        <p>Please enter your card details below:</p>
                        <b-stripe-membership-payment
                            v-if="!$root.is_junior_member()"
                            :recurring="$root.member.payment_is_recurring"
                            :price="payment_is_recurring ? $root.member.category.price_recurring : $root.member.category.price"
                            :donation="donation"
                            v-on:payment_complete="payment_complete"
                        />
                        <b-stripe-membership-payment
                            v-if="$root.is_junior_member()"
                            :price="0"
                            :recurring="false"
                            :donation="donation"
                            v-on:payment_complete="payment_complete"
                        />
                    </div>
                    <div class="payment-method" v-if="payment_method === 'direct-debit'">
                        <h3 class="at-heading is-grass">Direct Debit Payment</h3>
                        <p>Please enter your bank details below:</p>
                        <b-smart-debit-membership-payment
                            :price="payment_is_recurring ? $root.member.category.price_recurring : $root.member.category.price"
                            :donation="donation"
                            v-on:payment_complete="payment_complete"
                        />
                    </div>
                </div>

            </div>
        </div>
        <b-modal ref="no_recurring_payment">
            <p>Warning – by unticking recurring payment you will be charged a higher annual rate. Please tick the recurring payment box if you wish to pay the lower subscription rate. If you are happy to proceed at the higher rate, please click Confirm Payment Method below</p>
        </b-modal>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                is_submitting: false,
                payment_choice: 'credit-card',
                payment_method: null,
                payment_is_recurring: true,
            }
        },

        created() {
            if (!this.$root.is_life_member()) {
                this.update_payment_is_recurring(true);
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

        computed: {
            donation() {
                if (this.$root.member.donations.length > 0) {
                    return this.$root.member.donations[0];
                }

                return null;
            },
        },

        methods: {
            async waitForMember(callback) {
                if (!this.$root.member) {
                    setTimeout(() => this.waitForMember(callback), 250);
                    return;
                }

                callback(this.$root.member)
            },

            update_payment_is_recurring(paymentIsRecurring) {
                this.waitForMember(() => {
                    this.$root.member.payment_is_recurring = paymentIsRecurring
                    if (paymentIsRecurring === false && this.hasRecurringPrice()) {
                        this.$refs.no_recurring_payment.open();
                    }
                });
            },

            hasRecurringPrice() {
                return this.$root.member && this.$root.member.category.price_recurring !== this.$root.member.category.price;
            },

            payment_complete(args) {
                this.is_submitting = true;

                let payment_is_recurring;

                if (typeof args.payment_is_recurring !== 'undefined') {
                    payment_is_recurring = args.payment_is_recurring;
                } else {
                    payment_is_recurring = args;
                }

                this.$laravel_api.post('auth/register/step-4', {
                    payment_is_recurring: payment_is_recurring
                })
                    .then(({data}) => {
                        if (data.success) {
                            window.delete_cookie('MEMBER_REGISTRATION_STEP');
                            window.delete_cookie('MEMBER_REGISTRATION_TYPE');
                            location.replace(location.origin + '/thank-you-for-joining');
                        } else {
                            this.$toast.error();
                            this.is_submitting = false;
                        }
                    })
                    .catch((response) => {
                        this.$toast.error();
                        this.is_submitting = false;
                    });
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
            }
        }
    }
</script>
