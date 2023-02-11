
<template>
    <div class="b-donate">
        <div class="b-donate__innner">
            <template v-if="status === 1">
                <div>
                    <div v-if="recurring == true">
                        <h4 style="color: #2b2d2e;">Make a monthly donation</h4>
                        <p>By opting to make a monthly donation, you agree to Fish Legal
                             taking a monthly payment for your specified amount on this date every month.
                              You can cancel this donation at any time by emailing
                             <a style="background: green; color: white; " href="mailto:admin@fishlegal.net">admin@fishlegal.net</a>
                             or by contacting the Membership team on <a   style="background: green; color: white;"  href="tel:01568620447">01568 620447.</a></p>
                    </div>
                    <div v-if="recurring == false">
                        <h4 style="color: #2b2d2e;">Make a one-off donation</h4>
                    </div>
                    <b-donation-input  v-model="donation" :available_options="donation_options"/>

                    <button class="at-btn is-green is-solid" @click="to_status_2" :disabled="$root.is_loading">
                        Donate
                    </button>
                </div>
            </template>
            <template v-if="status === 2">
                <h4 style="color: #2b2d2e;">Contact details</h4>
                <br>
                <div class="form-row">
                    <b-text-input
                        name="name"
                        label="Name"
                        v-model="member_details.name"
                        required
                    />

                    <b-text-input
                        name="email"
                        label="Email"
                        v-model="member_details.email"
                        required
                    />

                    <div style="width: 100%;margin: 20px 0 10px;">
                        <p style="color: #2b2d2e;"><strong>Are you an Angling Trust and Fish Legal member?</strong></p>
                    </div>

                    <div style="width: 100%;margin: 10px 0 30px;">
                        <button class="at-btn is-green is-solid" id="yes_button" @click="is_member(true)">
                            Yes
                        </button>
                        <button class="at-btn is-green is-solid" id="no_button" @click="is_member(false)">
                            No
                        </button>
                    </div>
                    
                    <!-- for the monthly donation -->
                    <div v-if="recurring == true" style="width: 100%;margin: 20px 0 10px;">
                        <p style="color: #2b2d2e;"><strong>Would you like to receive updates and the latest news via email from Fish Legal?</strong></p>
                    </div>

                    <div v-if="recurring == true"  style="width: 100%;margin: 10px 0 30px;">
                        <button class="at-btn is-green is-solid" id="yes_sub" @click="is_subscribed(true)">
                            Yes
                        </button>
                        <button class="at-btn is-green is-solid" id="no_sub" @click="is_subscribed(false)">
                            No
                        </button>
                    </div>



                    <!-- for one-off donation -->
                    <!-- <div v-if="recurring == false" style="width: 100%;margin: 20px 0 10px;">
                        <p style="color: #2b2d2e;"><strong>Would you like to receive updates and the latest news via email from Fish Legal?</strong></p>
                    </div>

                    <div v-if="recurring == false"  style="width: 100%;margin: 10px 0 30px;">
                        <button class="at-btn is-green is-solid" id="yes_sub" @click="is_subscribed(true)">
                            Yes
                        </button>
                        <button class="at-btn is-green is-solid" id="no_sub" @click="is_subscribed(false)">
                            No
                        </button>
                    </div> -->

                    <div style="width: 100%;margin: 20px 0 10px;">
                        <p style="color: #2b2d2e;"><strong>Is this donation in memory of a loved one?</strong></p>
                    </div>

                    <b-text-input
                        name="donation_detail"
                        label="Add Details"
                        v-model="member_details.donation_detail"
                        required
                    />

                </div>
                <button class="at-btn is-green is-solid" @click="to_status_3">
                    Next
                </button>
            </template>
            <template v-if="status === 3">
                <h4 style="color: #2b2d2e;">Payment details</h4>
                <b-stripe-payment
                    ref="stripe_card"
                    :amount="parseFloat(donation.amount)"
                    :description="description"
                    :recurring="recurring"
                    v-on:payment_taken="donate"
                />
            </template>
            <template  v-if="status === 4">
                <div class="b-donate__message" v-text="message"></div>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['choices', 'message', 'disabled'],

        data() {
            return {
                status: 1,
                donation: {},
                member_details: {
                    name: {
                        value: null, error: null
                    },
                    email: {
                        value: null, error: null
                    },
                    is_member: {
                        value: false, error: null
                    },
                    is_subscribed: {
                        value: false, error: null
                    },
                    donation_detail: {
                        value: null, error: null
                    }
                },
                is_for_campaign: false,
                recurring: false,
            }
        },

        computed: {
            description() {
                var origin = window.origin === 'https://fishlegal.net' ? 'FL' : 'AT';
                if(window.origin.indexOf('fish-legal') !== -1) {
                    origin = 'FL';
                }

                const member = {
                    name: this.$root.member ? this.$root.member.full_name : this.member_details.name.value,
                    email: this.$root.member ? this.$root.member.email : this.member_details.email.value,
                };

                if(this.recurring) {
                    return `[${origin} Recurring Donation] Name: ${member.name} | Email: ${member.email} | Destination: ${this.donation.destination || 'any'}`;
                }
                else {
                    return `[${origin} Donation] Name: ${member.name} | Email: ${member.email} | Destination: ${this.donation.destination || 'any'}`;
                }
            },

            donation_options() {
                return this.choices.split(',').map(choice => {
                    const name = choice.replace(/\.00$/, '');

                    return {
                        name: `£${name}`,
                        value: choice
                    };
                })
            }
        },

        mounted() {
            let ctx = this;
            document.querySelectorAll('.one-off')[0].addEventListener('click', function(){
                ctx.recurring = false;
                document.getElementById('store-donate-app').scrollIntoView({
                    behavior: 'smooth'
                });
            });

            document.querySelectorAll('.monthly')[0].addEventListener('click', function(){
                ctx.recurring = true;
                document.getElementById('store-donate-app').scrollIntoView({
                    behavior: 'smooth'
                });
            });
        },
        methods: {
            donate() {
                this.status = 4;
                this.$laravel_api.post('any/donation', {
                    amount: parseFloat(this.donation.amount),
                    destination: this.donation.destination,
                    note: this.donation.note,
                    name: this.member_details.name.value,
                    email: this.member_details.email.value,
                    is_subscribed:this.member_details.is_subscribed.value,
                    is_member: this.member_details.is_member.value,
                    donation_detail: this.member_details.donation_detail.value
                });
            },

            to_status_2() {
                this.status = this.$root.member ? 3 : 2;
            },


            to_status_3() {
                let valid = true;

                if (!this.member_details.name.value) {
                    this.member_details.name.error = 'This field is required.';
                    valid = false;
                }

                if (!this.member_details.email.value) {
                    this.member_details.email.error = 'This field is required.';
                    valid = false;
                }

                if (!valid) {
                    return;
                }

                this.status = 3;
            },

            is_member(value) {
                this.member_details.is_member.value = value;

                if(value == true) {
                    document.getElementById('yes_button').classList.add('darken');
                    document.getElementById('no_button').classList.remove('darken');
                }
                if(value == false) {
                    document.getElementById('no_button').classList.add('darken');
                    document.getElementById('yes_button').classList.remove('darken');
                }
            },
           is_subscribed(value){
                this.member_details.is_subscribed.value = value;
                if(value == true) {
                    document.getElementById('yes_sub').classList.add('darken');
                    document.getElementById('no_sub').classList.remove('darken');
                }
                if(value == false) {
                    document.getElementById('no_sub').classList.add('darken');
                    document.getElementById('yes_sub').classList.remove('darken');
                }

            }
        }
    }
</script>

<style type="text/css">
    .b-donate__checkbox {
        margin-bottom: 2rem;
    }

    .b-donate__checkbox input {
        margin-right: 5px;
    }

    .b-donate__checkbox label {
        font-size: 12px;
    }

    .at-btn.one-off .wp-block-button__link{
        padding: 0;
        text-decoration: none !important;
        font-size: unset;
    }

    .at-btn.monthly .wp-block-button__link{
        padding: 0;
        text-decoration: none !important;
        font-size: unset;
    }
    .at-btn.darken {
        filter: brightness(75%);
    }

    #store-donate-app {
        scroll-margin-top: 40px;
    }

</style>