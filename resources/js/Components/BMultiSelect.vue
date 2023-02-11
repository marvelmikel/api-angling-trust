<template>
    <div class="b-multi-select wrap" :id="name">
        <template v-for="option in options">
            <button :key="option.id" v-if="field.value.includes(option.id)" class="at-btn is-solid is-blue has-icon">
                <span>{{ option.name }}</span>
                <i class="fal fa-times" @click="remove_option(option.id)"></i>
            </button>
        </template>
        <template v-if="can_add_options">
            <template v-if="select_is_open">
                <span class="at-select">
                    <select v-model="new_selected">
                        <option v-for="option in options" v-if="!field.value.includes(option.id)" :key="option.id" :value="option.id">{{ option.name }}</option>
                    </select>
                </span>
                <button class="confirm at-btn is-solid is-green has-icon" @click="add_option">
                    <i class="fal fa-check"></i>
                </button>
            </template>
            <template v-if="!select_is_open">
                <button class="add-more at-btn is-solid is-green has-icon" @click="open_select">
                    <i class="fal fa-plus"></i>
                </button>
            </template>
        </template>
        <div class="form-errors" v-text="field.error"></div>
    </div>
</template>

<script>
    export default {
        props: ['value', 'options', 'name'],

        data() {
            return {
                select_is_open: false,
                new_selected: null
            }
        },

        computed: {
            field: {
                get() {
                    return this.value;
                },

                set(value) {
                    this.$emit('input', value);
                }
            },

            can_add_options() {
                return this.field.value.length < this.options.length;
            }
        },

        watch: {
            'field.value': function() {
                if (this.field.error) {
                    this.field.error = null;
                }
            }
        },

        methods: {
            open_select() {
                this.select_is_open = true;
            },

            add_option() {
                if (!this.new_selected) {
                    return;
                }

                let option = this.options.find((x) => x.id === this.new_selected);
                this.field.value.push(option.id);

                this.select_is_open = false;
                this.new_selected = null;
            },

            remove_option(id) {
                let index = this.field.value.findIndex((x) => x === id);
                this.field.value.splice(index, 1);
            }
        }
    }
</script>
