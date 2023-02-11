<template>
    <div v-if="!is_submitting" class="b-payment-methods">
        <template v-if="provider === 'SMART_DEBIT'">
            Direct Debit

            <div class="b-payment-method-info">
                <i class="fal fa-scroll"></i> {{ details.account_name }} | {{ details.account_number }} | {{ details.sort_code }}
            </div>
        </template>
        <template v-if="provider === 'STRIPE'">
            Credit/Debit Card

            <div class="b-payment-method-info">
                <i class="fal fa-credit-card"></i> {{ details.billing_details.name }} | **** **** **** {{ details.card.last4 }} | Exp {{ $window.pad(details.card.exp_month) }}/{{ details.card.exp_year }}
            </div>
        </template>

        <div class="at-section">
            <input id="payment_is_recurring" type="checkbox" v-model="member.payment_is_recurring" disabled/> <label for="payment_is_recurring">Automatically Renew My Membership</label>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['value'],

        created() {
            this.$laravel_api.get('payment_methods/primary')
                .then(({data}) => {
                    if (data.success) {
                        this.provider = data.data.provider;
                        this.details = data.data.details;
                        this.is_submitting = false;
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

        data() {
            return {
                is_submitting: true,
                details: null,
                provider: null
            }
        },

        computed: {
            member: {
                get() {
                    return this.value;
                },

                set(value) {
                    this.$emit('input', value);
                }
            }
        },

        methods: {
            change_payment_method() {
                setTimeout(() => {
                    this.$root.to_step(4);
                }, 300);
            }
        }
    }
</script>
