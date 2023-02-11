<template>
    <div v-if="is_open" class="b-modal-wrapper" @click.self="close">
        <div class="b-modal">
            <div class="b-modal__inner">
                <slot></slot>
                <b-wave fill="white" anchor="bottom" direction="down" />
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['permanent'],

        data() {
           return {
               is_open: false
           }
        },

        methods: {
            open() {
                this.is_open = true;
            },

            close() {
                if (this.$flag.is_true(this.permanent)) {
                    return;
                }

                this.is_open = false;
            },

            force_close() {
                this.is_open = false;
            }
        }
    }
</script>
