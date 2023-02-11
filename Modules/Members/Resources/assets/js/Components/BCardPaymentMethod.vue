<template>
    <div class="b-card-payment-method" :class="{ 'is-disabled': disabled }">
        <p><i class="fal fa-credit-card"></i> {{ payment_method.billing_details.name }} | **** **** **** {{ payment_method.card.last4 }} | Exp {{ payment_method.card.exp_month }}/{{ payment_method.card.exp_year }}</p>
        <button class="at-btn is-solid is-red" @click="delete_payment_method" :disabled="!can_delete">
            <i class="fal fa-trash"></i>
        </button>
    </div>
</template>

<script>
    export default {
        props: ['payment_method', 'can_delete'],

        data() {
            return {
                disabled: false
            }
        },

        methods: {
            delete_payment_method() {
                this.$confirm().then(() => {
                    this.disabled = true;

                    this.$laravel_api.delete(`payment_methods/stripe/${this.payment_method.id}`)
                        .then((response) => {
                            this.$emit('deleted', this.payment_method);
                        });
                });
            }
        }
    }
</script>
