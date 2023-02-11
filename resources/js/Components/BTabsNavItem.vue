<template>
    <button class="at-btn is-blue has-icon" :class="{ 'is-frame' : !open, 'is-solid': open }" @click="open_tab">
        <slot></slot>
    </button>
</template>

<script>
    export default {
        props: ['tab', 'is-active'],
        inject: ['id'],

        data() {
            return {
                open: false
            }
        },

        created() {
            if (this.isActive === '') {
                this.open = true;
            }

            this.$root.$on(`tabs:${this.id}:change`, (tab) => {
                if (this.tab === tab) {
                    this.open = true;
                } else {
                    this.open = false;
                }
            });
        },

        methods: {
            open_tab() {
                this.$root.$emit(`tabs:${this.id}:change`, this.tab);
            }
        }
    }
</script>
