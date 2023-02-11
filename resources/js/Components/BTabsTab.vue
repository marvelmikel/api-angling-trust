<template>
    <div v-if="open" class="b-tabs-tab">
        <slot></slot>
    </div>
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
        }
    }
</script>
