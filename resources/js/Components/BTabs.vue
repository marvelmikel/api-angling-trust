<template>
    <div class="b-tabs">
        <div class="b-tabs-nav">
            <slot name="nav"></slot>
        </div>
        <slot name="tabs"></slot>
    </div>
</template>

<script>
    export default {
        props: ['id'],

        provide() {
            return {
                id: this.id
            }
        }
    }
</script>
