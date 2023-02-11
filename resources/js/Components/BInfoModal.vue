<template>
    <span class="b-info-modal" :style="({
        display: inline ? 'inline-flex' : 'flex',
    })">
        <span class="icon-wrapper" @click="open">
            <i class="fa fa-info"></i>
        </span>
        <small class="ml-2" v-if="label"> {{ label }}</small>

        <b-modal ref="modal">
            <span class="close-btn" @click="close">
                <i class="fa fa-times"></i>
            </span>
            <slot></slot>
        </b-modal>
    </span>
</template>

<script>
    export default {
        props: {
            inline: {
                type: Boolean,
                required: false,
                default: false,
            },
            label: {
                type: String,
                required: false,
                default: '',
            }
        },

        methods: {
            open() {
                this.$refs.modal.open();
            },

            close() {
                this.$refs.modal.close();
            }
        }
    }
</script>

