<template>
    <div>
        <p v-html="intro"></p>
        <p v-for="{text, _value} in values">
            <label>
                <input type="radio" name="registration" @change="value = _value">
                <span v-html="text" />
            </label>
        </p>

        <p>Click submit to access your ballot paper.</p>

        <p>
            <button
                class="at-btn is-blue is-frame"
                :disabled="disabled || value === null"
                v-on:click.prevent="$emit('save', value)"
            >Submit</button>
        </p>
    </div>
</template>

<script>

const Registration = {
    props: {
        intro: {
            type: String,
            required: true,
        },
        confirmation: {
            type: String,
            required: true,
        },
        rejection: {
            type: String,
            required: true,
        },
        disabled: {
            type: Boolean,
            required: false,
            default: false,
        },
    },
    data() {
        return {
            value: null
        }
    },

    computed: {
        values() {
            return [
                {
                    text: this.confirmation,
                    _value: true
                },
                {
                    text: this.rejection,
                    _value: false
                }
            ]
        }
    }
}

export default Registration;

</script>
