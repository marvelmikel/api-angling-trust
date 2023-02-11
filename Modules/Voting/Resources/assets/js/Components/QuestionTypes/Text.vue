<template>
    <div class="voting-question voting-question__text" v-html="question.content"></div>
</template>

<script>

import BaseField from "./BaseField";

export default {
    extends: BaseField,
    data() {
        return {
            value: [],
        }
    },
}

</script>
