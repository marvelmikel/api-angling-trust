<template>
    <div class="at-events__filters">
        <div class="at-btn is-blue has-icon at-filters__toggle" @click="toggle" :class="{ 'is-solid js-active': is_open || is_closing, 'is-frame': !is_open && !is_closing }">
            <i class="fal fa-filter"></i> <span>Filter</span>
        </div>
        <div class="at-filters__closed-text" ref="closed_text" :class="{ 'js-active': !is_open && !is_closing }">
            <p v-html="closed_text"></p>
        </div>
        <div class="at-filters" ref="filters" :class="{ 'js-active': is_open }">
            <div class="at-filters__inner">
                <div class="at-filters__actions">
                    <div class="at-filters__actions--right"></div>
                    <div class="at-filters__actions--left">
                        <div class="at-btn is-outline is-blue has-icon" @click="reset">
                            <i class="fal fa-trash-undo-alt"></i> <span>Reset</span>
                        </div>
                        <div class="at-btn is-outline is-blue has-icon at-filters__close" @click="close">
                            <i class="fal fa-times"></i> <span>Close</span>
                        </div>
                    </div>
                </div>
                <div class="at-filters__fields">
                    <slot></slot>
                </div>
                <div class="at-filters__submission">
                    <div class="at-btn is-solid is-grass" @click="submit">
                        <span>Submit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['closed_text'],

        mounted() {
            this.timeline = window.gsap.timeline({
                paused: true,
            });

            this.timeline.fromTo(this.$refs.filters, {
                duration: 0.75,
                ease: Power2.easeOut,
                height: 0,
            }, {
                autoAlpha: 1,
                height: 'auto',
            });

            this.timeline.fromTo(this.$refs.closed_text, {
                duration: 0.25,
                ease: Power2.easeOut,
                opacity: 1
            }, {
                opacity: 0
            }, "-=0.75");
        },

        data() {
            return {
                is_open: false,
                is_closing: false,
                timeline: null
            }
        },

        methods: {
            reset() {
                this.$emit('reset');
            },

            submit() {
                this.close();
                this.$emit('submit');
            },

            toggle() {
                this.is_open ? this.close() : this.open();
            },

            open() {
                this.is_open = true;
                this.timeline.play();
            },

            close() {
                this.is_open = false;
                this.is_closing = true;
                this.timeline.reverse();

                setTimeout(() => {
                    this.is_closing = false;
                }, 750);
            }
        }
    }
</script>
