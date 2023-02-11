<template>
    <div class="b-smart-debit">
        <div class="form-row">
            <b-text-input
                name="description"
                label="Description"
                v-model="fields.description"
            />
        </div>

        <button class="at-btn is-green is-solid" :disabled="is_submitting" @click="submit">Complete Payment</button>
    </div>
</template>

<script>
    export default {
        props: ['purpose', 'price'],

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

                let data = this.$fields.serialise_fields(this.fields);

                if (this.purpose) {
                    data['purpose'] = this.purpose;
                }

                if (this.price) {
                    data['price'] = this.price;
                }

                this.$laravel_api.post('payment_methods/offline', data)
                .then(({data}) => {
                    if (data.success) {
                        this.$emit('payment_complete');
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
