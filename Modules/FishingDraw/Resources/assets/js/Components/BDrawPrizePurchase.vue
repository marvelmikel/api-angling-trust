<template>
    <div class="b-draw-prize-purchase">
        <template v-if="step === 1">
            <slot name="pre_step_1"></slot>
            <div class="button-group">
                <div class="buttons">
                    <button class="at-btn is-blue" :class="{ 'is-solid': quantity === 1, 'is-frame': quantity !== 1 }" @click="set_quantity(1)">1 Ticket</button>
                    <button class="at-btn is-blue" :class="{ 'is-solid': quantity === 5, 'is-frame': quantity !== 5 }" @click="set_quantity(5)">5 Tickets</button>
                    <button class="at-btn is-blue" :class="{ 'is-solid': other_quantity === true, 'is-frame': other_quantity === false }" @click="set_other_quantity">Other</button>
                </div>

                <b-text-input
                    v-if="other_quantity"
                    name="other_quantity"
                    label="Quantity"
                    v-model="fields.quantity"
                />
            </div>
            <h2 class="total">Total £{{ total_price }}</h2>
            <button class="at-btn is-green is-solid" @click="to_step_2" :disabled="quantity_error">Buy Now</button>
        </template>
        <template v-if="step === 2">
            <div class="form-row">
                <b-text-input
                    name="name"
                    label="Name"
                    v-model="fields.name"
                    required
                />

                <b-text-input
                    name="email"
                    label="Email"
                    v-model="fields.email"
                    required
                />
            </div>
            <b-draw-stripe-purchase
                :payment_amount="payment_amount"
                v-on:back="step = 1"
                v-on:complete="complete"
            />
        </template>
        <template v-if="step === 3">
            <slot name="step_3"></slot>
        </template>
    </div>
</template>

<script>
    export default {
        props: ['draw_id', 'prize_id', 'ticket_price'],

        mounted() {
            if (this.$root.member) {
                this.fields.name.value = this.$root.member.first_name + ' ' + this.$root.member.last_name;
                this.fields.email.value = this.$root.member.user.email;
            }
        },

        data() {
            return {
                is_submitting: false,
                step: 1,
                other_quantity: false,
                fields: {
                    quantity: {
                        value: 1, error: null
                    },
                    name: {
                        value: null, error: null
                    },
                    email: {
                        value: null, error: null
                    }
                }
            }
        },

        computed: {
            quantity: {
                get() {
                    return parseInt(this.fields.quantity.value);
                },

                set(value) {
                    this.fields.quantity.value = value;
                }
            },

            quantity_error() {
                if (this.quantity > 10) {
                    return true;
                }

                if (this.quantity < 1 || this.fields.quantity.value === '') {
                    return true;
                }

                return false
            },

            payment_amount() {
                if (this.quantity < 1 || this.fields.quantity.value === '') {
                    return 0;
                }

                return this.quantity * this.ticket_price;
            },

            total_price() {
                let total = this.payment_amount / 100;
                return total.toFixed(2);
            },

            member_id() {
                if (!this.$root.member) {
                    return null;
                }

                return this.$root.member.id;
            }
        },

        watch: {
            'fields.quantity.value': function(value) {
                if (parseInt(value) > 10) {
                    this.$nextTick(() => {
                        this.fields.quantity.error = 'Maximum of 10';
                    });
                }

                if (parseInt(value) < 1 || value === '') {
                    this.$nextTick(() => {
                        this.fields.quantity.error = 'Minimum of 1';
                    });
                }
            }
        },

        methods: {
            to_step_2() {
                this.step = 2;

                if (window.innerWidth < 768) {
                    this.$scroll_to('#at-draw-form-top', 500, {offset: -50});
                }
            },

            set_quantity(value) {
                this.quantity = value;
                this.other_quantity = false;
            },

            set_other_quantity() {
                this.other_quantity = true;
            },

            complete() {
                this.is_submitting = true;

                this.$laravel_api.post(`fishing-draw/${this.draw_id}/${this.prize_id}`, {
                    name: this.fields.name.value,
                    email: this.fields.email.value,
                    quantity: this.quantity,
                    payment_amount: this.payment_amount,
                    member_id: this.member_id
                }).then(({data}) => {
                    if (data.success) {
                        this.step = 3;
                    } else {
                        if (data.error.code === 422) {
                            this.$fields.fill_errors(this.fields, data.data.errors);
                            this.$toast.error("There are errors in your submission.");
                        }

                    }

                    this.is_submitting = false;
                }).catch(() => {
                    this.is_submitting = false;
                    this.$toast.error();
                });
            }
        }
    }
</script>
