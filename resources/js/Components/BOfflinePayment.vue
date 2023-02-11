<template>
    <div class="b-smart-debit">
        <div class="form-row">
            <b-text-input
                name="description"
                label="Description"
                v-model="fields.description"
            />
        </div>

        <button class="at-btn is-green is-solid" :disabled="is_submitting" @click="submit">Save & Continue</button>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                is_submitting: false,
                fields: {
                    description: {
                        value: null, error: null
                    },
                }
            }
        },

        methods: {
            submit() {
                this.is_submitting = true;

                this.$laravel_api.post('members/me/make-an-offline-payment', this.$fields.serialise_fields(this.fields))
                    .then(({data}) => {
                        this.is_submitting = false;

                        if (data.success) {
                            this.$emit('saved');
                        } else {
                            if (data.error.code === 422) {
                                this.$toast.error('You have validation errors');
                                this.$fields.fill_errors(this.fields, data.data.errors);
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
