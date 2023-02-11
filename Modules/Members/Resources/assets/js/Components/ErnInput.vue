<template>
    <div class="form-input--ern">
        <b-text-input
            v-if="no_ern.value === false"
            :name="name"
            :label="label"
            :value="value"
            infoLabel="What is an ERN?"
            info="An ERN is an employer reference number, also known as an employer PAYE reference number. If your
                club, fishery, federation, or consultative/river association is an employer paying any employee above
                the PAYE threshold it should have an ERN. We need to collect the ERN for those members who have one
                and provide this to our insurers in connection with the employer’s liability insurance element of
                your membership package. If you fail to disclose your ERN, your employer’s liability insurance may be
                invalidated in the event of a claim. If you do not have your ERN number to hand now, you may continue
                with your membership, but please do enter the ERN number via your login dashboard or send it on to us
                as soon as you can so we can notify the insurers."
        />

        <b-checkbox-input
            name="no_ern"
            label="I do not have an ERN"
            v-model="no_ern"
        />
    </div>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                required: true,
            },
            label: {
                type: String|null,
                required: false,
                default: null,
            },
            placeholder: {
                type: String,
                required: false,
                default: ''
            },
            value: {
                type: Object,
                required: true,
            },
            required: {
                type: Boolean,
                required: false,
                default: false,
            }
        },

        data() {
            return {
                no_ern: {
                    value: false,
                    error: null,
                }
            }
        },

        watch: {
            no_ern: {
                handler(no_ern) {
                    this.$emit('input', {
                        value: no_ern.value ? 'N/A' : '',
                        error: null
                    })
                },
                deep: true,
            }
        }
    }
</script>
