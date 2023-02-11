<template>
    <b-map-modal v-if="show" :header="false" v-on:close="close" class="is-grass fl-casestudy__modal">
        <h2 class="fl-casestudy__modal__title">{{ case_study.name }}</h2>
        <div class="fl-casestudy__modal__status" v-if="case_study.status_names">Status: {{ case_study.status_names.join(', ') }}</div>
        <div class="fl-casestudy__modal__category" v-if="case_study.category_names"><span v-if="case_study.category_names.length == 1">Category</span><span v-else>Categories</span>: {{ case_study.category_names.join(', ') }}</div>
        <div class="fl-casestudy__modal__date" v-if="case_study.timeline_date"><i class="fal fa-calendar"></i> {{ case_study.timeline_date }}</div>
        <div class="fl-casestudy__modal__member" v-if="case_study.member"><i class="fal fa-user-circle"></i> {{ case_study.member }}</div>
        <div class="fl-casestudy__modal__img" v-if="case_study.featured_image" :style="{'background-image':`url(${case_study.featured_image})`}"><img :src="case_study.featured_image" :alt="case_study.name"></div>
        <div class="fl-casestudy__modal__headline" v-if="case_study.headline" v-html="case_study.headline"></div>
        <a v-if="case_study.permalink" v-bind:class="{ 'is-hidden': case_study.hide_button }" class="at-btn is-grey is-solid" :href="case_study.permalink">View case study</a>
    </b-map-modal>
</template>

<script>
    export default {
        data() {
            return {
                show: false,
                case_study: null
            }
        },

        methods: {
            open(case_study) {
                this.case_study = case_study;
                this.show = true;
            },

            close() {
                this.show = false;
                this.case_study = null;
            }
        }
    }
</script>
