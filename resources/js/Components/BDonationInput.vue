<template>
    <div>

        <div class="at-donation wrap" flex center>
            <div class="wrap">
                <template v-for="option in options">
                    <button class="at-btn is-white"
                            @click="(custom = false); handleDonationChange(option.value)"
                            :class="{
                                'is-solid': !custom && (amount === option.value),
                                'is-frame': custom || (amount !== option.value)
                            }"
                    >{{ option.name }}</button>
                </template>
                <button class="at-btn is-white"
                        @click="custom = true"
                        :class="{
                            'is-solid': custom,
                            'is-frame': !custom
                        }">Custom
                </button>
            </div>

            <div class="form-input" v-if="custom">
                <span>&pound;</span>
                <input type="number" :value="amount" @change="({ target: { value } }) => handleDonationChange(value)">
            </div>
        </div>
        <div v-if="parseFloat(amount) && allowNote">
            <div class="form-input">
                <p>If you would like to leave a message about why you are
                     making this donation, please enter it below. By entering
                      a message, you are giving permission for it to be included
                      in future promotional activity on our website.</p>
                <div class="mb-4">
                    <textarea
                        :value="note"
                        @input="({ target: { value } }) => handleNoteInput(value)"
                    />
                </div>
            </div>
        </div>
        <div v-if="parseFloat(amount) && allowDestinations && destinations">
            <div class="form-input">
<!--                <p>Do you have a preference as to what your donation is used for?-->
<!--                    <span class="required">*</span>-->
<!--                </p>-->
                <div class="mb-4">

                    <div class="donation-boxes" style="display:flex; margin: 20px 0;">
<!--                    <div class="form-input&#45;&#45;radio" v-for="option in destinationOptions">-->
<!--                        <input :id="`destination&#45;&#45;${option.slug}`"-->
<!--                               type="radio"-->
<!--                               name="destination"-->
<!--                               :value="option.slug"-->
<!--                               :checked="destination === option.slug"-->
<!--                               @change="handleDestinationChange"-->
<!--                        >-->
<!--                        <label :for="`destination&#45;&#45;${option.slug}`">{{ option.text }}</label>-->
<!--                    </div>-->
                        <div class="donation-box" style="margin: 0 20px;width: 30%; height:350px"  :style="`background-image: url('${option.image}')`" v-for="option in destinationOptions">
                            <div class="donation-box-body" style="position: absolute;bottom: 0;width: 100%;left: 0;">

                                <div class="donation-box-value" style="height: 75px;color:#ffffff; font-size: 35px;background-color: grey;text-align: center;border-radius: 100% 100% 0 0;margin-bottom: -10px;"> {{ option.value }}
                                </div>
                                <div class="donation-box-text" style="position:relative;min-height: 60px;background-color: #ffffff;color: #000000;padding: 15px;">
                                    <svg class="svg-wave is-white is-fullwidth"><use xlink:href="#wave"></use></svg>
                                    {{ option.text }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                type: Object,
                required: true
            },
            available_options: {
                type: Array|null,
                required: false,
                default: null
            },
            allowDestinations: {
                type: Boolean,
                required: false,
                default: true
            },
            allowNote: {
                type: Boolean,
                required: false,
                default: true
            }

        },

        data() {
            return {
                custom: false,
                destination: this.value.destination || 'any',
                amount: this.value.amount || '',
                note: this.value.note || '',

                options: this.available_options || [
                    {name: 'None', value: '0.00'},
                    {name: '£5', value: '5.00'},
                    {name: '£10', value: '10.00'},
                    {name: '£50', value: '50.00'}
                ],
                destinations: [],
            }
        },

        async created() {
            this.destinations = await this.fetchDestinations();
            if (!this.amount) {
                this.amount = this.options[0].value;
            }
            this.emitDonationArray();


        },

        computed: {
            destinationOptions() {
                return [
                    ...this.destinations.filter(({active}) => active)
                ]
            },
        },

        methods: {
            select(value) {
                this.amount = value;
                this.custom = false;
            },
            async fetchDestinations() {
                const { data } = await this.$wp_api.get('/wp-json/barques/donation/destinations')

                return data;
            },
            handleDestinationChange({ target: { value } }) {
                this.destination = value;
                this.emitDonationArray();
            },
            handleDonationChange(value) {
                this.amount = parseFloat(value).toFixed(2);
                this.emitDonationArray();
            },
            handleNoteInput(value) {
                this.note = value;
                this.emitDonationArray();
            },
            emitDonationArray() {
                this.$emit('input', this.getDonationArray());
            },
            getDonationArray() {
                return {
                    amount: this.amount,
                    destination: this.destination,
                    note: this.note,
                }
            }
        },
    }
</script>
