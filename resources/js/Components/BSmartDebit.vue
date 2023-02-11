<template>
    <div class="b-smart-debit">
        <div class="form-row">
            <b-text-input
                name="first_name"
                label="First Name"
                v-model="fields.first_name"
            />

            <b-text-input
                name="last_name"
                label="Last Name"
                v-model="fields.last_name"
            />
        </div>

        <div class="form-row">
            <b-text-input
                name="account_name"
                label="Account Name"
                v-model="fields.account_name"
            />
        </div>

        <div class="form-row">
            <b-text-input
                name="account_number"
                label="Account Number"
                v-model="fields.account_number"
            />

            <b-text-input
                name="sort_code"
                label="Sort Code (xx-xx-xx)"
                v-model="fields.sort_code"
            />
        </div>

        <div class="at-section">
            <input id="auto_renew" type="checkbox" checked disabled/> <label for="auto_renew">Automatically Renew My Membership</label>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
        </div>

        <button class="at-btn is-green is-solid" :disabled="is_submitting" @click="submit">Save & Continue</button>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.fields.first_name.value = this.$root.member.user.first_name;
            this.fields.last_name.value = this.$root.member.user.last_name;
        },

        data() {
            return {
                is_submitting: false,
                fields: {
                    first_name: {
                        value: null, error: null
                    },
                    last_name: {
                        value: null, error: null
                    },
                    account_name: {
                        value: null, error: null
                    },
                    account_number: {
                        value: null, error: null
                    },
                    sort_code: {
                        value: null, error: null
                    }
                }
            }
        },

        methods: {
            submit() {
                this.is_submitting = true;

                this.$laravel_api.post('payment_methods/smart_debit/validate', this.$fields.serialise_fields(this.fields))
                .then(({data}) => {
                    this.is_submitting = false;

                    if (data.success) {
                        this.$root.member = data.data.member;
                        this.$emit('saved');
                    } else {
                        if (data.error.code === 422) {
                            this.$toast.error('You have validation errors');
                            this.$fields.fill_errors(this.fields, data.data.errors);
                        } else if (data.error.code === 1) {
                            this.$toast.error('Direct debit request was denied, please check your details are correct');
                        } else {
                            this.$toast.error();
                        }
                    }
                }).catch(() => {
                    this.$toast.error();
                    this.is_submitting = false;
                });
            }
        }
    }
</script>
