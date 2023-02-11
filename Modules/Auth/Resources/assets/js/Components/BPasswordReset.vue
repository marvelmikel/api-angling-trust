<template>
    <div class="b-password-reset">
        <template v-if="!is_complete">
            <div class="fields">
                <div class="form-row">
                    <b-password-input
                        name="password"
                        label="Password"
                        v-model="fields.password"
                        strong
                        required
                    ></b-password-input>
                </div>
            </div>

            <button class="at-btn is-solid is-grass" :disabled="is_submitting" @click="submit">
                <span>Submit</span>
            </button>
        </template>
        <template v-if="is_complete">
            <p>Your password has been updated, please login with your new password.</p>
            <a href="/members/login" class="at-btn is-blue is-solid">Login</a>
        </template>
    </div>
</template>

<script>
    export default {
        props: ['reset_token'],

        data() {
            return {
                is_submitting: false,
                is_complete: false,
                fields: {
                    password: {
                        value: null, error: null
                    }
                }
            }
        },

        methods: {
            submit() {
                this.is_submitting = true;

                this.$laravel_api.post('auth/password-reset/complete', {
                    token: this.reset_token,
                    password: this.fields.password.value
                })
                .then(({data}) => {
                    if (data.success) {
                        this.is_complete = true;
                    } else {
                        this.is_submitting = false;

                        if (data.error.code === 422) {
                            this.$fields.fill_errors(this.fields, data.data.errors);
                        }
                    }
                });
            }
        }
    }
</script>
