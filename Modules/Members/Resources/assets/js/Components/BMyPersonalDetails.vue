<template>
    <div v-if="member" class="b-my-personal-details">
        <div class="info-section">
            <template v-if="!edit_mode">
                <dl>
                    <dt>First Name</dt>
                    <dd v-text="member.user.first_name"></dd>
                    <dt>Last Name</dt>
                    <dd v-text="member.user.last_name"></dd>
                    <dt>Date Of Birth</dt>
                    <dd v-text="moment(member.date_of_birth)"></dd>
                    <dt v-if="member.gender">Gender</dt>
                    <dd v-text="member.gender"></dd>
                    <dt v-if="member.disability">Disability</dt>
                    <dd v-text="member.disability"></dd>
                    <dt>Email Address</dt>
                    <dd v-text="member.user.email"></dd>
                    <dt v-if="member.home_telephone">Home Telephone</dt>
                    <dd v-text="member.home_telephone"></dd>
                    <dt v-if="member.mobile_telephone">Mobile Telephone</dt>
                    <dd v-text="member.mobile_telephone"></dd>
                    <dt>Address</dt>
                    <dd v-text="member.address.full_address"></dd>
                </dl>
                <button class="at-btn has-icon" :class="{ 'is-blue is-frame': background === 'white', 'is-white is-frame': background === 'grass'}" @click="edit">
                    <i class="fal fa-pencil-alt"></i>
                    <span>Edit My Personal Details</span>
                </button>
            </template>
        </div>
        <div class="edit-section">
            <template v-if="edit_mode">
                <div class="form-row">
                    <b-text-input
                        name="first_name"
                        label="First Name"
                        v-model="fields.user.first_name"
                        required
                    />
                    <b-text-input
                        name="last_name"
                        label="Last Name"
                        v-model="fields.user.last_name"
                        required
                    />
                </div>
                <div class="form-row">
                    <b-date-of-birth-input
                        name="date_of_birth"
                        label="Date of Birth"
                        v-model="fields.date_of_birth"
                        required
                    />
                    <b-text-input
                        name="gender"
                        label="Gender"
                        v-model="fields.gender"
                    />
                </div>
                <div class="form-row">
                    <b-text-input
                        name="disability"
                        label="Disability"
                        v-model="fields.disability"
                    />
                    <b-text-input
                        name="email"
                        label="Email"
                        v-model="fields.user.email"
                        required
                    />
                </div>
                <div class="form-row">
                    <b-text-input
                        name="home_telephone"
                        label="Home Telephone"
                        v-model="fields.home_telephone"
                    />
                    <b-text-input
                        name="mobile_telephone"
                        label="Mobile Telephone"
                        v-model="fields.mobile_telephone"
                    />
                </div>
                <h5>Address</h5>
                <div class="form-row">
                    <b-text-input
                        name="address_line_1"
                        label="Line 1"
                        v-model="fields.address.line_1"
                        required
                    />
                </div>
                <div class="form-row">
                    <b-text-input
                        name="address_line_2"
                        label="Line 2"
                        v-model="fields.address.line_2"
                    />
                    <b-text-input
                        name="address_town"
                        label="Town"
                        v-model="fields.address.town"
                        required
                    />
                </div>
                <div class="form-row">
                    <b-text-input
                        name="address_county"
                        label="County"
                        v-model="fields.address.county"
                    />
                    <b-text-input
                        name="address_postcode"
                        label="Postcode"
                        v-model="fields.address.postcode"
                        required
                    />
                </div>
                <button class="at-btn is-green is-solid" @click="save" :disabled="is_submitting">
                    Save
                </button>
                <button class="at-btn is-red is-solid" @click="cancel" :disabled="is_submitting">
                    Cancel
                </button>
            </template>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';

    export default {
        props: ['value', 'background'],

        created() {
            this.fields = this.$fields.initialise_fields([
                'user.first_name', 'user.last_name', 'user.email', 'user.email',
                'date_of_birth',
                'gender',
                'disability',
                'home_telephone',
                'mobile_telephone',
                'address.line_1', 'address.line_2', 'address.town', 'address.county', 'address.postcode'
            ]);
        },

        data() {
            return {
                is_submitting: false,
                edit_mode: false,
                fields: {}
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
            moment(value) {
                return moment(value).format('DD/MM/YYYY');
            },

            edit() {
                this.$fields.fill_fields(this.fields, this.member);
                this.edit_mode = true;
            },

            cancel() {
                this.edit_mode = false;
                this.$fields.clear_errors(this.fields);
            },

            save() {
                this.is_submitting = true;

                this.$laravel_api.post('members/me', this.$fields.serialise_fields(this.fields))
                .then(({data}) => {
                    if (data.success) {
                        this.member = data.data.member;
                        this.edit_mode = false;
                        this.$toast.success('Your personal details have been updated.');
                        this.is_submitting = false;
                    } else {
                        if (data.error.code === 422) {
                            this.$fields.fill_errors(this.fields, data.data.errors);
                            this.$toast.error("There are errors in your submission.");
                        }

                        this.is_submitting = false;
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
