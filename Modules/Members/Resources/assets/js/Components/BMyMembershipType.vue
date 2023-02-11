<template>
    <div v-if="member" class="b-my-membership-type">
        <template v-if="!edit_mode">
            <div flex spread>
                <p class="at-heading--h1 membership-type__name">
                    <span>{{ member.membershipType.name }} ({{ member.category.name }})</span>
                    <br>
                    <span v-if="member.fl_member" class="small-text">+ Fish Legal Membership</span>
                </p>
                <p class="at-heading--h1 membership-type__price">
                    <span v-if="member.payment_is_recurring">{{ member.category.formatted_price_recurring }}/yr</span>
                    <span v-if="!member.payment_is_recurring">{{ member.category.formatted_price }}</span>
                    <br>
                    <span v-if="donation" class="small-text">+ {{ donation.formatted_amount }} donation</span>
                </p>
            </div>
            <div flex center spread>
                <h4 class="at-heading is-blue">Need help? Call us on 0343 507 7006 or use our Live at</h4>
                <a href="javascript:void(Tawk_API.toggle())" class="at-btn is-blue is-frame">
                    <i class="fal fa-comment-dots"></i>
                    <span>Live chat</span>
                </a>
            </div>
        </template>
        <template v-if="edit_mode">
            <b-select
                name="membership_type_id"
                label="Select Membership Type"
                v-model="fields.membership_type_id"
                :options="$root.membership_types"
                required
            />

            <div class="wrap">
                <button class="at-btn is-green is-solid" @click="save" :disabled="is_submitting">
                    Save
                </button>
                <button class="at-btn is-red is-solid" @click="edit_mode = false" :disabled="is_submitting">
                    Cancel
                </button>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        props: ['value', 'donation'],

        data() {
            return {
                is_submitting: false,
                edit_mode: false,
                fields: {
                    membership_type_id: {
                        value: null, error: null
                    }
                }
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
            edit() {
                this.fields.membership_type_id.value = this.member.membership_type_id;
                this.edit_mode = true;
            },

            save() {
                this.is_submitting = true;

                this.$laravel_api.post('members/me/membership-type', this.$fields.serialise_fields(this.fields))
                .then(({data}) => {
                    if (data.success) {
                        this.member = data.data.member;
                        this.edit_mode = false;
                        this.is_submitting = false;
                        this.$toast.success('Your membership type has been updated.');
                    } else {
                        this.$toast.error();
                        this.is_submitting = true;
                    }
                })
                .catch(() => {
                    this.is_submitting = false;
                    this.$toast.error();
                });
            }
        }
    }
</script>
